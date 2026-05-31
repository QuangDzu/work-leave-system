<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (!$token = auth('api')->attempt($request->only('email', 'password'))) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials.', 'errors' => []], 401);
        }

        $user = auth('api')->user();
        $user->load('roles.permissions');

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'user'         => new UserResource($user),
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60,
            ],
            'meta' => [],
        ]);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();
        return response()->json(['success' => true, 'message' => 'Logged out.', 'data' => [], 'meta' => []]);
    }

    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();
        return response()->json([
            'success' => true,
            'message' => 'Token refreshed.',
            'data'    => ['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth('api')->factory()->getTTL() * 60],
            'meta'    => [],
        ]);
    }

    public function me(): JsonResponse
    {
        $user = auth('api')->user();
        $user->load('roles.permissions');
        return response()->json(['success' => true, 'message' => 'User retrieved.', 'data' => new UserResource($user), 'meta' => []]);
    }
}
