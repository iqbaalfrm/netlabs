<?php

// ============================================================
// API Routes — Netlabs LMS (JSON API untuk Mobile Flutter)
// Semua return format: {success, data, message}
// ============================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PertemuanController as ApiPertemuan;
use App\Http\Controllers\Api\TopikController as ApiTopik;
use App\Http\Controllers\Api\KuisController as ApiKuis;
use App\Http\Controllers\Api\ChatController as ApiChat;
use App\Http\Controllers\Api\SiswaController as ApiSiswa;
use App\Http\Controllers\Api\ModulController as ApiModul;
use App\Http\Controllers\Api\ProgressController as ApiProgress;
use App\Http\Controllers\Api\NilaiController as ApiNilai;

// ─── Auth ───────────────────────────────────────────
Route::post('/auth/login',          [AuthController::class, 'login']);
Route::post('/auth/login-siswa',    [AuthController::class, 'loginSiswa']);
Route::post('/auth/login-guru',     [AuthController::class, 'loginGuru']);
Route::post('/auth/logout',         [AuthController::class, 'logout'])
    ->middleware('auth:api');
Route::get('/auth/me',              [AuthController::class, 'me'])
    ->middleware('auth:api');
Route::post('/auth/update-password', [AuthController::class, 'updatePassword'])
    ->middleware('auth:api');

// ─── Protected (JWT) ────────────────────────────────
Route::middleware('auth:api')->group(function () {

    // Pertemuan CRUD
    Route::get('/pertemuan',          [ApiPertemuan::class, 'index']);
    Route::post('/pertemuan',         [ApiPertemuan::class, 'store']);
    Route::get('/pertemuan/{id}',     [ApiPertemuan::class, 'show']);
    Route::put('/pertemuan/{id}',     [ApiPertemuan::class, 'update']);
    Route::delete('/pertemuan/{id}',  [ApiPertemuan::class, 'destroy']);

    // Topik (nested di pertemuan)
    Route::get('/pertemuan/{id}/topik',  [ApiTopik::class, 'index']);
    Route::post('/pertemuan/{id}/topik', [ApiTopik::class, 'store']);
    Route::get('/topik/{id}',            [ApiTopik::class, 'show']);
    Route::put('/topik/{id}',            [ApiTopik::class, 'update']);
    Route::delete('/topik/{id}',         [ApiTopik::class, 'destroy']);
    Route::post('/topik/{id}/selesai',   [ApiTopik::class, 'tandaiSelesai']);

    // Kuis
    Route::get('/kuis/{pertemuan_id}',  [ApiKuis::class, 'getSoal']);   // 5 soal random
    Route::post('/kuis/soal',           [ApiKuis::class, 'storeSoal']);
    Route::put('/kuis/soal/{id}',       [ApiKuis::class, 'updateSoal']);
    Route::delete('/kuis/soal/{id}',    [ApiKuis::class, 'destroySoal']);
    Route::post('/kuis/hasil',          [ApiKuis::class, 'submitHasil']);
    Route::post('/kuis/jawaban',        [ApiKuis::class, 'submitHasil']); // alias
    Route::get('/kuis/riwayat/{user_id}', [ApiKuis::class, 'riwayat']);

    // Chat AI (forward ke RAG)
    Route::post('/chat',                       [ApiChat::class, 'send']);
    Route::get('/chat/riwayat/{user_id}',      [ApiChat::class, 'riwayat']);
    Route::delete('/chat/{user_id}',           [ApiChat::class, 'destroy']);

    // Progress Topik
    Route::get('/progress/{pertemuan_id}',           [ApiProgress::class, 'index']);
    Route::post('/progress/{topik_id}/selesai',      [ApiProgress::class, 'tandaiSelesai']);

    // Nilai
    Route::get('/nilai',                [ApiNilai::class, 'index']);
    Route::get('/nilai/{pertemuan_id}', [ApiNilai::class, 'show']);

    // Modul PDF
    Route::post('/modul/upload',                [ApiModul::class, 'upload']);
    Route::get('/modul/{pertemuan_id}',         [ApiModul::class, 'getByPertemuan']);
    Route::delete('/modul/{id}',                [ApiModul::class, 'destroy']);

    // Siswa
    Route::get('/siswa',        [ApiSiswa::class, 'index']);
    Route::get('/siswa/{id}',   [ApiSiswa::class, 'show']);
});