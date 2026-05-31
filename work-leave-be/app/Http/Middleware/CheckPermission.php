<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): mixed
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.', 'errors' => []], 401);
        }

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.', 'errors' => []], 401);
        }

        // Eager load roles.permissions một lần
        $user->loadMissing('roles.permissions');

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Forbidden. Insufficient permissions.',
            'errors'  => [],
        ], 403);
    }
}
