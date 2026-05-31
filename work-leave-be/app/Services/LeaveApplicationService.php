<?php

namespace App\Services;

use App\Models\LeaveApplication;
use App\Models\User;
use App\Notifications\LeaveRejectedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LeaveApplicationService
{
    private array $holidays = ['01-01', '04-30', '05-01', '09-02'];

    // ── Create ──
    public function create(array $data, User $user): LeaveApplication
    {
        $start = Carbon::parse($data['start_date']);
        $end   = Carbon::parse($data['end_date']);

        if ($start->gt($end)) {
            throw ValidationException::withMessages(['start_date' => ['start_date phải nhỏ hơn hoặc bằng end_date.']]);
        }

        $totalDays = $this->calculateTotalDays($data['start_date'], $data['end_date']);

        if ($totalDays <= 0) {
            throw ValidationException::withMessages(['start_date' => ['Khoảng thời gian không có ngày làm việc.']]);
        }

        if ($this->checkOverlap($user->id, $data['start_date'], $data['end_date'])) {
            throw ValidationException::withMessages(['start_date' => ['Thời gian trùng với đơn đã duyệt hoặc đang chờ duyệt.']]);
        }

        if ($data['type'] === LeaveApplication::TYPE_ANNUAL && !$this->checkQuota($user, $totalDays, $data['type'])) {
            throw ValidationException::withMessages(['total_days' => ["Không đủ ngày phép. Còn lại: {$user->remaining_days} ngày."]]);
        }

        $leave = LeaveApplication::create([
            'user_id'    => $user->id,
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'],
            'total_days' => $totalDays,
            'reason'     => $data['reason'],
            'type'       => $data['type'],
            'status'     => LeaveApplication::STATUS_NEW,
        ]);

        Log::info('Leave created', ['leave_id' => $leave->id, 'user_id' => $user->id]);

        return $leave->load('user');
    }

    // ── Update ──
    public function update(LeaveApplication $leave, array $data, User $user): LeaveApplication
    {
        if (!$leave->isEditable()) {
            throw ValidationException::withMessages(['status' => ['Chỉ đơn new/pending mới được cập nhật.']]);
        }

        $start     = Carbon::parse($data['start_date'] ?? $leave->start_date);
        $end       = Carbon::parse($data['end_date']   ?? $leave->end_date);
        $totalDays = $this->calculateTotalDays($start->toDateString(), $end->toDateString());

        if ($this->checkOverlap($leave->user_id, $start->toDateString(), $end->toDateString(), $leave->id)) {
            throw ValidationException::withMessages(['start_date' => ['Thời gian trùng với đơn khác.']]);
        }

        $leave->update([
            'start_date' => $start->toDateString(),
            'end_date'   => $end->toDateString(),
            'total_days' => $totalDays,
            'reason'     => $data['reason'] ?? $leave->reason,
            'type'       => $data['type']   ?? $leave->type,
        ]);

        return $leave->load('user');
    }

    // ── Delete ──
    public function delete(LeaveApplication $leave, User $user): bool
    {
        if (!$leave->isEditable()) {
            throw ValidationException::withMessages(['status' => ['Chỉ đơn new/pending mới được xóa.']]);
        }
        Log::info('Leave deleted', ['leave_id' => $leave->id, 'user_id' => $user->id]);
        return $leave->delete();
    }

    // ── Approve ─-
    public function approve(LeaveApplication $leave, User $approver): LeaveApplication
    {
        if (!$leave->isPending()) {
            throw ValidationException::withMessages(['status' => ['Chỉ đơn pending mới được duyệt.']]);
        }

        DB::transaction(function () use ($leave, $approver) {
            $leave->update(['status' => LeaveApplication::STATUS_APPROVED]);

            if ($leave->type === LeaveApplication::TYPE_ANNUAL) {
                $leave->user->decrement('remaining_days', $leave->total_days);
            }

            Log::info('Leave approved', ['leave_id' => $leave->id, 'approver_id' => $approver->id, 'at' => now()]);
        });

        return $leave->load('user');
    }

    // ── Reject (với lý do + notification) ─
    public function reject(LeaveApplication $leave, User $rejector, string $rejectionReason): LeaveApplication
    {
        if (!$leave->isPending()) {
            throw ValidationException::withMessages(['status' => ['Chỉ đơn pending mới được từ chối.']]);
        }

        DB::transaction(function () use ($leave, $rejector, $rejectionReason) {
            $leave->update([
                'status'           => LeaveApplication::STATUS_REJECTED,
                'rejection_reason' => $rejectionReason,
            ]);

            Log::info('Leave rejected', [
                'leave_id'         => $leave->id,
                'rejector_id'      => $rejector->id,
                'rejection_reason' => $rejectionReason,
                'at'               => now(),
            ]);
        });

        // Gửi notification cho employee (ngoài transaction để không ảnh hưởng DB nếu mail fail)
        try {
            $leave->load('user');
            $leave->user->notify(new LeaveRejectedNotification($leave, $rejector->name));
        } catch (\Exception $e) {
            Log::warning('Failed to send rejection notification', ['error' => $e->getMessage()]);
        }

        return $leave->load('user');
    }

    // ── Cancel ──
    public function cancel(LeaveApplication $leave, User $user): LeaveApplication
    {
        if (!in_array($leave->status, [LeaveApplication::STATUS_NEW, LeaveApplication::STATUS_PENDING])) {
            throw ValidationException::withMessages(['status' => ['Chỉ đơn new/pending mới được hủy.']]);
        }

        DB::transaction(function () use ($leave) {
            $leave->update(['status' => LeaveApplication::STATUS_CANCELLED]);

            if ($leave->type === LeaveApplication::TYPE_ANNUAL && $leave->wasApproved()) {
                $leave->user->increment('remaining_days', $leave->total_days);
            }
        });

        return $leave->load('user');
    }

    // ── Helpers ──
    public function calculateTotalDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->startOfDay();
        $days  = 0;
        $cur   = $start->copy();

        while ($cur->lte($end)) {
            if (!$cur->isWeekend() && !in_array($cur->format('m-d'), $this->holidays)) {
                $days++;
            }
            $cur->addDay();
        }

        return $days;
    }

    public function checkOverlap(string $userId, string $startDate, string $endDate, ?string $excludeId = null): bool
    {
        $query = LeaveApplication::where('user_id', $userId)
            ->whereIn('status', [LeaveApplication::STATUS_APPROVED, LeaveApplication::STATUS_PENDING])
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate);

        if ($excludeId) $query->where('id', '!=', $excludeId);

        return $query->exists();
    }

    public function checkQuota(User $user, int $totalDays, string $type): bool
    {
        return $type !== LeaveApplication::TYPE_ANNUAL || $user->remaining_days >= $totalDays;
    }
}
