<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth.api')->group(function () {
        Route::post('/logout',  [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me',       [AuthController::class, 'me']);
    });
});

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth.api')->group(function () {

    // Users — require user.view permission
    Route::prefix('users')->middleware('can.permission:user.view')->group(function () {
        Route::get('/',        [UserController::class, 'index']);
        Route::get('/{id}',    [UserController::class, 'show']);

        // Create/Edit/Delete require additional permissions (checked inside controller)
        Route::post('/',       [UserController::class, 'store']);
        Route::put('/{id}',    [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // Leave Applications
    Route::prefix('leaves')->group(function () {
        Route::get('/',    [LeaveController::class, 'index']);
        Route::get('/{id}', [LeaveController::class, 'show']);

        Route::middleware('can.permission:leave.create')->group(function () {
            Route::post('/', [LeaveController::class, 'store']);
        });

        Route::put('/{id}',        [LeaveController::class, 'update']);
        Route::delete('/{id}',     [LeaveController::class, 'destroy']);

        // Approve / Reject — requires leave.approve
        Route::middleware('can.permission:leave.approve')->group(function () {
            Route::put('/{id}/approve', [LeaveController::class, 'approve']);
            Route::put('/{id}/reject',  [LeaveController::class, 'reject']);
        });

        // Cancel — any authenticated user (ownership checked inside controller)
        Route::put('/{id}/cancel', [LeaveController::class, 'cancel']);
    });
});
