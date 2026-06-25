<?php

// ============================================================
// Web Routes — Netlabs Admin Panel (Blade + Tailwind CDN)
// Middleware auth.guru: cek session('guru'), redirect /login
// ============================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\PertemuanController;
use App\Http\Controllers\Web\TopikController;
use App\Http\Controllers\Web\KuisController;
use App\Http\Controllers\Web\SiswaController;

// ─── Root Redirect ──────────────────────────────────
Route::get('/', function () {
    return session('guru') ? redirect('/dashboard') : redirect('/login');
});

// ─── Auth ───────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Protected (session guru) ───────────────────────
Route::middleware('auth.guru')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pertemuan CRUD
    Route::get('/pertemuan', [PertemuanController::class, 'index'])->name('pertemuan.index');
    Route::get('/pertemuan/create', [PertemuanController::class, 'create'])->name('pertemuan.create');
    Route::post('/pertemuan', [PertemuanController::class, 'store'])->name('pertemuan.store');
    Route::get('/pertemuan/{id}', [PertemuanController::class, 'show'])->name('pertemuan.show');
    Route::get('/pertemuan/{id}/edit', [PertemuanController::class, 'edit'])->name('pertemuan.edit');
    Route::put('/pertemuan/{id}', [PertemuanController::class, 'update'])->name('pertemuan.update');
    Route::delete('/pertemuan/{id}', [PertemuanController::class, 'destroy'])->name('pertemuan.destroy');

    // Topik (nested di pertemuan)
    Route::get('/pertemuan/{id}/topik/create', [TopikController::class, 'create'])->name('topik.create');
    Route::post('/pertemuan/{id}/topik', [TopikController::class, 'store'])->name('topik.store');
    Route::get('/topik/{id}/edit', [TopikController::class, 'edit'])->name('topik.edit');
    Route::put('/topik/{id}', [TopikController::class, 'update'])->name('topik.update');
    Route::delete('/topik/{id}', [TopikController::class, 'destroy'])->name('topik.destroy');

    // Kuis / Soal (nested di pertemuan)
    Route::get('/pertemuan/{id}/kuis/create', [KuisController::class, 'create'])->name('kuis.create');
    Route::post('/pertemuan/{id}/kuis', [KuisController::class, 'store'])->name('kuis.store');
    Route::get('/kuis/{id}/edit', [KuisController::class, 'edit'])->name('kuis.edit');
    Route::put('/kuis/{id}', [KuisController::class, 'update'])->name('kuis.update');
    Route::delete('/kuis/{id}', [KuisController::class, 'destroy'])->name('kuis.destroy');

    // Upload Modul PDF per pertemuan
    Route::post('/pertemuan/{id}/modul', [PertemuanController::class, 'uploadModul'])->name('modul.upload');

    // Siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{id}', [SiswaController::class, 'show'])->name('siswa.show');
});