<?php

use Illuminate\Http\Request;
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
Route::post('password/verify-otp', [AuthController::class, 'verifyPasswordResetOTP']);
Route::post('password/update', [AuthController::class, 'updatePassword']);

Route::middleware('web', 'auth:sanctum')->group(function () {

    Route::post('/users/password/create', [AuthController::class, 'verifyPassword']);

    // Authenticated routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);

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

    Route::get('/genders', [GenderController::class, 'index']);
    Route::get('/genders/{gender}', [GenderController::class, 'show']);
    Route::post('/genders', [GenderController::class, 'store']);
    Route::put('/genders/{gender}', [GenderController::class, 'update']);
    Route::delete('/genders/{gender}', [GenderController::class, 'destroy']);

    Route::get('/goals', [GoalController::class, 'index']);
    Route::get('/goals/{goal}', [GoalController::class, 'show']);
    Route::post('/goals', [GoalController::class, 'store']);
    Route::put('/goals/{goal}', [GoalController::class, 'update']);
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy']);

    Route::get('/units-of-measurement', [UnitOfMeasurementController::class, 'index']);
    Route::get('/units-of-measurement/{unitOfMeasurement}', [UnitOfMeasurementController::class, 'show']);
    Route::post('/units-of-measurement', [UnitOfMeasurementController::class, 'store']);
    Route::put('/units-of-measurement/{unitOfMeasurement}', [UnitOfMeasurementController::class, 'update']);
    Route::delete('/units-of-measurement/{unitOfMeasurement}', [UnitOfMeasurementController::class, 'destroy']);

    Route::get('/user-types', [UserTypeController::class, 'index']);
    Route::get('/user-types/{userType}', [UserTypeController::class, 'show']);
    Route::post('/user-types', [UserTypeController::class, 'store']);
    Route::put('/user-types/{userType}', [UserTypeController::class, 'update']);
    Route::delete('/user-types/{userType}', [UserTypeController::class, 'destroy']);
});