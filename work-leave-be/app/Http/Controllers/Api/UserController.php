<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->withCount('leaveApplications');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved.',
            'data'    => UserResource::collection($users),
            'meta'    => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::with(['leaveApplications' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(10);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'User retrieved.',
            'data'    => new UserResource($user),
            'meta'    => [],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'type'     => ['required', 'integer', Rule::in([User::TYPE_ADMIN, User::TYPE_MANAGER, User::TYPE_EMPLOYEE])],
        ]);

        $user = User::create([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'type'           => $validated['type'],
            'remaining_days' => 12,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created.',
            'data'    => new UserResource($user),
            'meta'    => [],
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'           => ['sometimes', 'string', 'max:255'],
            'email'          => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password'       => ['sometimes', 'string', 'min:8'],
            'role_name'      => ['sometimes', 'string', Rule::in(['admin', 'manager', 'employee'])],
            'remaining_days' => ['sometimes', 'integer', 'min:0'],
        ]);

        if (isset($validated['role_name'])) {
            $role = \App\Models\Role::where('name', $validated['role_name'])->first();
            if ($role) {
                $user->roles()->sync([$role->id => ['assigned_at' => now()]]);
            }
            unset($validated['role_name']);
        }

        if (isset($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->update($validated);

        $user->load('roles.permissions');

        return response()->json([
            'success' => true,
            'message' => 'User updated.',
            'data'    => new UserResource($user->fresh()->load('roles.permissions')),
            'meta'    => [],
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth('api')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete yourself.',
                'errors'  => [],
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted.',
            'data'    => [],
            'meta'    => [],
        ]);
    }
}
