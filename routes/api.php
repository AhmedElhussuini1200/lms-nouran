<?php

use App\Http\Controllers\Api\MobileController;
use Illuminate\Support\Facades\Route;

Route::post('login', [MobileController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [MobileController::class, 'me']);
    Route::get('my-teachers', [MobileController::class, 'myTeachers']);
    Route::post('select-teacher', [MobileController::class, 'selectTeacher']);
    Route::get('courses', [MobileController::class, 'courses']);
    Route::get('videos', [MobileController::class, 'videos']);
    Route::get('exams', [MobileController::class, 'exams']);
    Route::post('exams/{exam}/submit', [MobileController::class, 'submitExam']);
    Route::get('payments', [MobileController::class, 'payments']);
    Route::get('leaderboard', [MobileController::class, 'leaderboard']);
    Route::get('offline-manifest', [MobileController::class, 'offlineManifest']);
    Route::post('logout', [MobileController::class, 'logout']);
});
