<?php

use App\Http\Controllers\Api\AssigmentApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveApiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\SallaryApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::put('password', [ProfileController::class, 'updatePassword']);
        Route::put('profile/picture', [ProfileController::class, 'updateProfilePicture']);
    });
    
    // Attendance routes
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index']);
        Route::get('/today', [AttendanceController::class, 'getTodayAttendances']);
        Route::post('/clock-in', [AttendanceController::class, 'clockIn']);
        Route::post('/clock-out', [AttendanceController::class, 'clockOut']);
        Route::get('/status', [AttendanceController::class, 'getStatus']);
        Route::post('/{id}/notes', [AttendanceController::class, 'addAttendanceNotes']);
        Route::get('/{id}/notes', [AttendanceController::class, 'getAttendanceNotes']);
    });

    // Assignment employee route
    Route::prefix('assigment')->group(function () {
        Route::get('/', [AssigmentApiController::class, 'index']);
        Route::get('/alltask', [AssigmentApiController::class, 'AllTask']);
        Route::post('/report/{id}', [AssigmentApiController::class, 'ReportTask']);
    });
    
    // Get salary
    Route::prefix('sallary')->group(function () {
        Route::get('/', [SallaryApiController::class, 'index']);
    });
    
    // Get leave
    Route::prefix('leave')->group(function () {
        Route::get('/', [LeaveApiController::class, 'index']);
        Route::post('/req-leave', [LeaveApiController::class, 'ReqLeave']);
    });
});