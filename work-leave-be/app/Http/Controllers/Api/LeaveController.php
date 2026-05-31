<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveStoreRequest;
use App\Http\Requests\LeaveUpdateRequest;
use App\Http\Requests\RejectLeaveRequest;
use App\Http\Resources\LeaveResource;
use App\Models\LeaveApplication;
use App\Services\LeaveApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function __construct(private LeaveApplicationService $service) {}

    public function index(Request $request): JsonResponse
    {
        $user  = auth('api')->user();
        $query = LeaveApplication::with('user')->orderBy('created_at', 'desc');

        if ($user->isEmployee()) $query->forUser($user->id);

        if ($request->filled('status'))    $query->byStatus($request->status);
        if ($request->filled('type'))      $query->byType($request->type);
        if ($request->filled('from_date')) $query->fromDate($request->from_date);
        if ($request->filled('to_date'))   $query->toDate($request->to_date);
        if ($request->filled('user_id') && $user->isManagerOrAdmin()) $query->forUser($request->user_id);

        $leaves = $query->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Leave applications retrieved.',
            'data'    => LeaveResource::collection($leaves),
            'meta'    => [
                'current_page' => $leaves->currentPage(),
                'last_page'    => $leaves->lastPage(),
                'per_page'     => $leaves->perPage(),
                'total'        => $leaves->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $user  = auth('api')->user();
        $leave = LeaveApplication::with('user')->findOrFail($id);

        if ($user->isEmployee() && $leave->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.', 'errors' => []], 403);
        }

        return response()->json(['success' => true, 'message' => 'Leave retrieved.', 'data' => new LeaveResource($leave), 'meta' => []]);
    }

    public function store(LeaveStoreRequest $request): JsonResponse
    {
        $leave = $this->service->create($request->validated(), auth('api')->user());
        return response()->json(['success' => true, 'message' => 'Leave created.', 'data' => new LeaveResource($leave), 'meta' => []], 201);
    }

    public function update(LeaveUpdateRequest $request, string $id): JsonResponse
    {
        $user  = auth('api')->user();
        $leave = LeaveApplication::with('user')->findOrFail($id);

        if ($user->isEmployee() && $leave->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.', 'errors' => []], 403);
        }

        $leave = $this->service->update($leave, $request->validated(), $user);
        return response()->json(['success' => true, 'message' => 'Leave updated.', 'data' => new LeaveResource($leave), 'meta' => []]);
    }

    public function destroy(string $id): JsonResponse
    {
        $user  = auth('api')->user();
        $leave = LeaveApplication::findOrFail($id);

        if ($user->isEmployee() && $leave->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.', 'errors' => []], 403);
        }

        $this->service->delete($leave, $user);
        return response()->json(['success' => true, 'message' => 'Leave deleted.', 'data' => [], 'meta' => []]);
    }

    public function approve(string $id): JsonResponse
    {
        $user  = auth('api')->user();

        if (!$user->hasPermission('leave.approve')) {
            return response()->json(['success' => false, 'message' => 'Forbidden.', 'errors' => []], 403);
        }

        $leave = LeaveApplication::with('user')->findOrFail($id);
        $leave = $this->service->approve($leave, $user);

        return response()->json(['success' => true, 'message' => 'Leave approved.', 'data' => new LeaveResource($leave), 'meta' => []]);
    }

    public function reject(RejectLeaveRequest $request, string $id): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user->hasPermission('leave.approve')) {
            return response()->json(['success' => false, 'message' => 'Forbidden.', 'errors' => []], 403);
        }

        $leave = LeaveApplication::with('user')->findOrFail($id);
        $leave = $this->service->reject($leave, $user, $request->rejection_reason);

        return response()->json(['success' => true, 'message' => 'Leave rejected.', 'data' => new LeaveResource($leave), 'meta' => []]);
    }

    public function cancel(string $id): JsonResponse
    {
        $user  = auth('api')->user();
        $leave = LeaveApplication::with('user')->findOrFail($id);

        if ($leave->user_id !== $user->id && !$user->isManagerOrAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden.', 'errors' => []], 403);
        }

        $leave = $this->service->cancel($leave, $user);
        return response()->json(['success' => true, 'message' => 'Leave cancelled.', 'data' => new LeaveResource($leave), 'meta' => []]);
    }
}
