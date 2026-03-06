<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

// 1. Public Route: Login
Route::post('/login', [AuthController::class, 'login']);

// 2. Protected Routes: Only for logged-in users
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Get current user info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('projects', ProjectController::class)->except(['destroy']);

    Route::apiResource('tasks', TaskController::class)->except(['index', 'show']);
});

Route::get('/dashboard', [DashboardController::class, 'index']);

