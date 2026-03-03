<?php

use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\DonorController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\ProfileDonorController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
    });
});

Route::prefix('v1/')->group(function () {
    Route::middleware(['auth:sanctum', 'Role.check:admin,staff'])->group(function () {
        Route::patch('users/{id}/deactivate', [UserController::class, 'deactivate']);
        Route::patch('users/{id}/activate', [UserController::class, 'activate']);
        Route::apiResource('users', UserController::class);

        Route::patch('donors/{id}/deactivate', [UserController::class, 'deactivate']);
        Route::patch('donors/{id}/activate', [UserController::class, 'activate']);
        Route::apiResource('donors', DonorController::class);

        Route::apiResource('announcements', AnnouncementController::class);
        Route::patch('announcements/{id}/deactivate', [AnnouncementController::class, 'deactivate']);
        Route::patch('announcements/{id}/activate', [AnnouncementController::class, 'activate']);

        Route::get('users/{userId}/donors', [ProfileDonorController::class, 'index']);
        Route::post('users/{userId}/donors', [ProfileDonorController::class, 'store']);
        Route::apiResource('users', ProfileDonorController::class);

        Route::get('users/{userId}', [ProfileController::class, 'show']);
        Route::put('users/{userId}', [ProfileController::class, 'update']);
        Route::apiResource('users', ProfileController::class);
    });

    Route::middleware(['auth:sanctum', 'Role.check:admin'])->group(function () {
        Route::get('users/trashed', [UserController::class, 'trashedIndex']);
        Route::post('users/{id}/restore', [UserController::class, 'restore']);
        Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete']);

        Route::post('donors/{id}/restore', [DonorController::class, 'restore']);
        Route::delete('donors/{id}/force-delete', [DonorController::class, 'forceDelete']);

        Route::post('announcements/{id}/restore', [AnnouncementController::class, 'restore']);
        Route::delete('announcements/{id}/force-delete', [AnnouncementController::class, 'forceDelete']);
    });
});
