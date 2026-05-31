<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private LeaveApplication $leave,
        private string $rejectorName
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $typeMap = [
            'annual' => 'Nghỉ phép năm',
            'sick'   => 'Nghỉ ốm',
            'unpaid' => 'Nghỉ không lương',
        ];

        $typeName = $typeMap[$this->leave->type] ?? $this->leave->type;

        return (new MailMessage)
            ->subject('[WorkLeave] Đơn nghỉ phép của bạn đã bị từ chối')
            ->greeting("Xin chào {$notifiable->name},")
            ->line("Đơn **{$typeName}** từ ngày **{$this->leave->start_date->format('d/m/Y')}** đến **{$this->leave->end_date->format('d/m/Y')}** ({$this->leave->total_days} ngày) của bạn đã bị **từ chối**.")
            ->line('**Lý do từ chối:**')
            ->line($this->leave->rejection_reason)
            ->line("Người từ chối: **{$this->rejectorName}**")
            ->line('Nếu có thắc mắc, vui lòng liên hệ trực tiếp với quản lý của bạn.')
            ->salutation('Trân trọng, Hệ thống WorkLeave');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'leave_id'         => $this->leave->id,
            'rejection_reason' => $this->leave->rejection_reason,
            'rejected_by'      => $this->rejectorName,
        ];
    }
}
