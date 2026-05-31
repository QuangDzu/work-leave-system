<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.', 'errors' => []], 401);
            }

            $user->loadMissing('roles.permissions');
        } catch (\Exception) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.', 'errors' => []], 401);
        }

        return $next($request);
    }
}
