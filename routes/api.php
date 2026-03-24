<?php

use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\AppointmentsController;
use App\Http\Controllers\Api\Admin\CertificateController;
use App\Http\Controllers\Api\Admin\DonationController;
use App\Http\Controllers\Api\Admin\DonorController;
use App\Http\Controllers\Api\Admin\HospitalController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\BloodInventoryController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Admin\BloodRequestController;
use App\Http\Controllers\Api\Admin\MedicalRecordController;
use App\Http\Controllers\Api\User\HomeController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\ProfileDonorController;
use App\Http\Controllers\Api\User\CertificateController as UserCertificateController;
use App\Http\Controllers\Api\User\AppointmentController as UserAppointmentController;
use App\Http\Controllers\Api\User\BloodRequestController as UserBloodRequestController;
use App\Http\Controllers\Api\User\DonationController as UserDonationController;
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

    // Common routes
    Route::apiResource('announcements', AnnouncementController::class)->only('index', 'show');

    // Admin and Staff
    Route::middleware(['auth:sanctum', 'Role.check:1,2'])->group(function () {
        // User Routes
        Route::patch('users/{id}/deactivate', [UserController::class, 'deactivate']);
        Route::patch('users/{id}/activate', [UserController::class, 'activate']);
        Route::apiResource('users', UserController::class);

        // Donor Routes
        Route::patch('donors/{id}/deactivate', [UserController::class, 'deactivate']);
        Route::patch('donors/{id}/activate', [UserController::class, 'activate']);
        Route::apiResource('donors', DonorController::class);

        // Blood Requests Routes
        Route::apiResource('blood-requests', BloodRequestController::class);
        Route::patch('blood-requests/{id}', [BloodRequestController::class, 'updateStatus']);

        // Announcement Routes
        Route::apiResource('announcements', AnnouncementController::class)->only('store', 'update');
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destory']);
        Route::patch('announcements/{id}/deactivate', [AnnouncementController::class, 'deactivate']);
        Route::patch('announcements/{id}/activate', [AnnouncementController::class, 'activate']);

        // Role Routes
        Route::get('/roles', [RoleController::class, 'index']);

        // Hospital Routes
        Route::get('/hospitals', [HospitalController::class, 'index']);

        // Donation Routes
        Route::apiResource('donations', DonationController::class);

        // Appointments Routes
        Route::prefix('/appointments')->group(function () {
            Route::post('/donation', [AppointmentsController::class, 'storeDonationAppointment']);
            Route::post('/blood-request', [AppointmentsController::class, 'storeBloodRequestAppointment']);
            Route::get('/', [AppointmentsController::class, 'index']);
            Route::get('/{id}', [AppointmentsController::class, 'show']);
            Route::put('/{id}', [AppointmentsController::class, 'update']);
            Route::delete('/{id}', [AppointmentsController::class, 'destory']);
            Route::patch('/{id}/toggle-status', [AppointmentsController::class, 'toggleStatus']);
        });

        // Blood Inventory Routes
        Route::apiResource('blood-inventories', BloodInventoryController::class)->except('destory');
        Route::put('blood-inventories/{id}/used', [BloodInventoryController::class, 'markUsed']);

        // MedicalRecord Routes
        Route::apiResource('medical-recores', MedicalRecordController::class);

        // Dashboard Route
        Route::get('/dashboard', DashboardController::class);

        // Certificate Route
        Route::apiResource('certificates', CertificateController::class);
    });

    // Admin Only
    Route::middleware(['auth:sanctum', 'Role.check:1'])->group(function () {

        Route::get('users/trashed', [UserController::class, 'trashedIndex']);
        Route::post('users/{id}/restore', [UserController::class, 'restore']);
        Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete']);

        Route::post('donors/{id}/restore', [DonorController::class, 'restore']);
        Route::delete('donors/{id}/force-delete', [DonorController::class, 'forceDelete']);

        Route::patch('blood-requests/{id}/fulfill', [BloodRequestController::class, 'fulfill']);

        Route::post('announcements/{id}/restore', [AnnouncementController::class, 'restore']);
        Route::delete('announcements/{id}/force-delete', [AnnouncementController::class, 'forceDelete']);

        Route::post('blood-inventory/{id}/restore', [BloodInventoryController::class, 'restore']);
        Route::delete('blood-inventory/{id}/force-delete', [BloodInventoryController::class, 'forceDelete']);
        Route::post('donations/{id}/restore', [DonationController::class, 'restore']);
        Route::delete('donations/{id}/force-delete', [DonationController::class, 'forceDelete']);

    });

    // User Only
    Route::middleware(['auth:sanctum', 'Role.check:3'])->group(function () {

        // Home Routes
        Route::get('/home', [HomeController::class, 'index']);

        // User Profile Routes
        Route::get('/user/{userId}', [ProfileController::class, 'show']);
        Route::put('/user/{userId}', [ProfileController::class, 'update']);

        // User Donor Routes
        Route::get('user/{userId}/doners', [ProfileDonorController::class, 'index']);
        Route::post('user/{userId}/doners', [ProfileDonorController::class, 'store']);

        // User Certificate Routes
        Route::get('/{userId}/certificates', [UserCertificateController::class, 'index']);
        Route::get('/{userId}/certificates/{id}', [UserCertificateController::class, 'show']);

        // User Appointment Routes
        Route::get("/{userId}/appointments", [UserAppointmentController::class, "index"]);
        Route::get("/{userId}/appointments/{id}", [UserAppointmentController::class, "show"]);
        Route::patch("/{userId}/appointments/{id}", [UserAppointmentController::class, "update"]);

        // User Blood Request Routes
        Route::prefix('{userId}/blood-requests')->group(function () {
            Route::get('/', [UserBloodRequestController::class, 'index']);
            Route::post('/', [UserBloodRequestController::class, 'store']);
        });
        Route::patch('blood-requests/{id}/cancel', [UserBloodRequestController::class, 'cancel']);

        // Donation Routes
        Route::get('/{userId}/donations', [UserDonationController::class, 'index']);
        Route::get('/{userId}/donations/{id}', [UserDonationController::class, 'show']);
        Route::post('/{userId}/donations', [UserDonationController::class, 'store']);
        Route::patch('/{userId}/donations/{id}/cancel', [UserDonationController::class, 'cancel']);
    });
});
