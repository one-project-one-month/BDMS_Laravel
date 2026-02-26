<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1/')->group(function () {
    Route::middleware(['auth:sanctum', 'Role.check:Admin'])->group(function () {
        // Write Your Route Here
    });
});
