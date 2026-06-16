<?php

use App\Http\Controllers\WaterController;
use App\Http\Controllers\AdminDashboardController;

use Illuminate\Support\Facades\Route;

// ── 1. Landing Page Utama (Publik) ───────────────────────────
// Menggantikan halaman utama dengan Landing Page edukasi & download APK
Route::get('/', [AdminDashboardController::class, 'index']);

// ── 2. Proses Form Air ────────────────────────────────────────
Route::post('/add-water', [WaterController::class, 'store'])->name('water.store');

// ── 3. Admin — Dashboard ──────────────────────────────────────
Route::get('/admin/dashboard',       [AdminDashboardController::class, 'index']);
Route::get('/admin/live-data',       [AdminDashboardController::class, 'getLiveData']);

// ── 4. Admin — Riwayat per User ───────────────────────────────
Route::get('/admin/user-history/{id}', [AdminDashboardController::class, 'getUserHistory']);

// ── 5. Admin — Manajemen Users ────────────────────────────────
Route::get('/admin/users',            [AdminDashboardController::class, 'users']);
Route::delete('/admin/users/{id}',    [AdminDashboardController::class, 'deleteUser']);

// ── 6. Admin — Log Air ────────────────────────────────────────
Route::get('/admin/logs',             [AdminDashboardController::class, 'logs']);

// ── 7. Admin — Laporan & Export CSV ──────────────────────────
Route::get('/admin/reports',          [AdminDashboardController::class, 'reports']);
Route::get('/admin/reports/export-csv', [AdminDashboardController::class, 'exportCsv']);