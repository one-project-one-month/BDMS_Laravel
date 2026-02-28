<?php

use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/')->group(function () {
    Route::middleware(['auth:sanctum', 'Role.check:admin,staff'])->group(function () {
        Route::resource('/users', UserController::class);
    });
});
