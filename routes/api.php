<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\WaterApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Taruh di PALING ATAS, sebelum semua route lain
Route::get('/test', function () {
    return response()->json(['status' => 'OK', 'message' => 'Laravel routing works!']);
});

// ── Public routes ─────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ── Protected routes ──────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout',          [AuthController::class, 'logout']);
    Route::get('/profile',          [AuthController::class, 'profile']);
    Route::put('/profile/update',   [AuthController::class, 'updateProfile']);
    Route::put('/target/update',    [AuthController::class, 'updateTarget']);
    
    // Water
    Route::get('/water-data',       [WaterApiController::class, 'getTodayData']);
    Route::get('/stats/weekly',     [WaterApiController::class, 'getWeeklyStats']);
    Route::post('/water-add',       [WaterApiController::class, 'storeData']);
    });