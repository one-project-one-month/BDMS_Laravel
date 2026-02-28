<?php

use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/')->group(function () {
    Route::middleware(['auth:sanctum', 'Role.check:admin,staff'])->group(function () {
        Route::patch('users/{id}/deactivate', [UserController::class, 'deactivate']);
        Route::patch('users/{id}/activate', [UserController::class, 'activate']);
        Route::apiResource('users', UserController::class);
    });
});
