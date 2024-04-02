<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GenderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserTypeController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UnitOfMeasurementController;


Route::post('/register-with-social', [AuthController::class, 'registerWithoutPass']);
Route::post('/login-with-social', [AuthController::class, 'loginWithoutPass']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/users/verify', [AuthController::class, 'verifyOtpAndRegister']);
Route::post('/users/send-otp', [AuthController::class, 'sendOtp']);

// Password Reset Routes
Route::post('password/reset', [AuthController::class, 'resetPassword']);
// Route::post('password/verify-otp', [AuthController::class, 'verifyPasswordResetOTP']);
Route::post('password/update', [AuthController::class, 'updatePassword']);

//Resend OTP
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

// Core value data
Route::get('/genders', [GenderController::class, 'index']);
Route::get('/goals', [GoalController::class, 'index']);
Route::get('/units-of-measurement', [UnitOfMeasurementController::class, 'index']);
Route::get('/user-types', [UserTypeController::class, 'index']);

Route::middleware('web', 'auth:sanctum')->group(function () {

    Route::post('/users/password/create', [AuthController::class, 'verifyPassword']);

    // Authenticated routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::post('/upload-profile-picture', [ProfileController::class, 'uploadProfilePicture']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{role}', [RoleController::class, 'show']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{role}', [RoleController::class, 'update']);
    Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
    Route::post('/permissions', [PermissionController::class, 'store']);
    Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);
});