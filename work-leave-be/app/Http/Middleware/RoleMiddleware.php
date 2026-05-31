<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
                'errors'  => [],
            ], 401);
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
                'errors'  => [],
            ], 401);
        }

        if (!in_array($user->type_name, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Insufficient permissions.',
                'errors'  => [],
            ], 403);
        }

        return $next($request);
    }
}
