<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // JWT auth (replaces built-in auth:api redirect)
            'auth.api'        => \App\Http\Middleware\ApiAuthenticate::class,

            // Permission-based guard  →  usage: middleware('can.permission:leave.approve')
            'can.permission'  => \App\Http\Middleware\CheckPermission::class,

            // Legacy role guard (kept for backwards compat)
            'role'            => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Return JSON for unauthenticated API requests (no redirect to /login)
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                    'errors'  => [],
                ], 401);
            }
        });
    })
    ->create();
