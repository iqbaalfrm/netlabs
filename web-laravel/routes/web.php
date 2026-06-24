<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PertemuanController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\ModulController;
use App\Http\Controllers\KuisController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PengaturanController;

// ==============================
// AUTH ROUTES
// ==============================
Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==============================
// PROTECTED ROUTES (guru)
// ==============================
Route::middleware('auth.guru')->prefix('guru')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{id}', [KelasController::class, 'show'])->name('kelas.show');
    Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // Manajemen Siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/{id}', [SiswaController::class, 'show'])->name('siswa.show');
    Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::post('/siswa/{id}/reset-password', [SiswaController::class, 'resetPassword'])->name('siswa.reset-password');


    // Pertemuan & Topik
    Route::get('/pertemuan', [PertemuanController::class, 'index'])->name('pertemuan.index');
    Route::post('/pertemuan', [PertemuanController::class, 'store'])->name('pertemuan.store');
    Route::get('/pertemuan/{id}', [PertemuanController::class, 'show'])->name('pertemuan.show');
    Route::put('/pertemuan/{id}', [PertemuanController::class, 'update'])->name('pertemuan.update');
    Route::delete('/pertemuan/{id}', [PertemuanController::class, 'destroy'])->name('pertemuan.destroy');

    // Topik (nested)
    Route::post('/pertemuan/{id}/topik', [PertemuanController::class, 'storeTopik'])->name('topik.store');
    Route::get('/pertemuan/{pertemuanId}/topik/{topikId}', [PertemuanController::class, 'showTopik'])->name('topik.show');
    Route::put('/pertemuan/{pertemuanId}/topik/{topikId}', [PertemuanController::class, 'updateTopik'])->name('topik.update');
    Route::delete('/pertemuan/{pertemuanId}/topik/{topikId}', [PertemuanController::class, 'destroyTopik'])->name('topik.destroy');

    // Modul PDF (di dalam topik)
    Route::get('/modul', [ModulController::class, 'index'])->name('modul.index');
    Route::post('/modul', [ModulController::class, 'store'])->name('modul.store');
    Route::delete('/modul/{id}', [ModulController::class, 'destroy'])->name('modul.destroy');
    Route::post('/modul/{id}/trigger-rag', [ModulController::class, 'triggerRag'])->name('modul.triggerRag');
    Route::post('/pertemuan/{id}/modul', [PertemuanController::class, 'uploadModul'])->name('modul.upload');
    Route::delete('/pertemuan/{pertemuanId}/modul/{modulId}', [PertemuanController::class, 'destroyModul'])->name('modul.destroyLegacy');

    // Soal Kuis (di dalam topik)
    Route::post('/topik/{topikId}/soal', [KuisController::class, 'storeSoalByTopik'])->name('soal.storeByTopik');
    Route::delete('/soal/{soalId}', [KuisController::class, 'destroySoalById'])->name('soal.destroyById');

    // Kuis & Soal (legacy standalone pages)
    Route::get('/kuis', [KuisController::class, 'index'])->name('kuis.index');
    Route::get('/kuis/{pertemuanId}', [KuisController::class, 'show'])->name('kuis.show');
    Route::post('/kuis/{pertemuanId}/soal', [KuisController::class, 'storeSoal'])->name('soal.store');
    Route::delete('/kuis/{pertemuanId}/soal/{soalId}', [KuisController::class, 'destroySoal'])->name('soal.destroy');

    // Nilai & Progress
    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::get('/nilai/export', [NilaiController::class, 'export'])->name('nilai.export');

    // Pengaturan
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan/profil', [PengaturanController::class, 'updateProfil'])->name('pengaturan.profil');
});
