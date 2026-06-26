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

    // Kuis — PENTING: route statis HARUS sebelum route ber-parameter
    Route::get('/kuis/riwayat',                  [ApiKuis::class, 'riwayatSaya']);   // mobile: GET /api/kuis/riwayat
    Route::get('/kuis/riwayat/{user_id}',        [ApiKuis::class, 'riwayat']);       // existing
    Route::get('/kuis/{pertemuan_id}/soal',      [ApiKuis::class, 'getSoal']);       // mobile: GET /api/kuis/{id}/soal
    Route::get('/kuis/{pertemuan_id}',           [ApiKuis::class, 'getSoal']);       // existing
    Route::post('/kuis/soal',                    [ApiKuis::class, 'storeSoal']);
    Route::put('/kuis/soal/{id}',                [ApiKuis::class, 'updateSoal']);
    Route::delete('/kuis/soal/{id}',             [ApiKuis::class, 'destroySoal']);
    Route::post('/kuis/hasil',                   [ApiKuis::class, 'submitHasil']);
    Route::post('/kuis/jawaban',                 [ApiKuis::class, 'submitHasil']);   // alias
    Route::post('/kuis/{pertemuan_id}/jawaban',  [ApiKuis::class, 'submitJawaban']); // mobile: POST /api/kuis/{id}/jawaban

    // Chat AI (forward ke RAG) — route statis HARUS sebelum route ber-parameter
    Route::post('/chat',                 [ApiChat::class, 'send']);          // mobile: POST /api/chat
    Route::get('/chat',                  [ApiChat::class, 'riwayatSaya']);   // mobile: GET /api/chat
    Route::get('/chat/riwayat/{user_id}',[ApiChat::class, 'riwayat']);       // existing
    Route::delete('/chat',               [ApiChat::class, 'destroySaya']);   // mobile: DELETE /api/chat
    Route::delete('/chat/{user_id}',     [ApiChat::class, 'destroy']);       // existing

    // Progress Topik
    Route::get('/progress',                      [ApiProgress::class, 'indexAll']);    // mobile: GET /api/progress
    Route::get('/progress/{pertemuan_id}',       [ApiProgress::class, 'index']);       // existing
    Route::post('/progress/{topik_id}/selesai',  [ApiProgress::class, 'tandaiSelesai']); // existing
    Route::delete('/progress/{topik_id}',        [ApiProgress::class, 'reset']);       // mobile: DELETE /api/progress/{topikId}

    // Nilai — route statis HARUS sebelum route ber-parameter
    Route::get('/nilai/semua',          [ApiNilai::class, 'semua']);         // mobile: GET /api/nilai/semua
    Route::get('/nilai/siswa/{siswa_id}',[ApiNilai::class, 'siswa']);        // mobile: GET /api/nilai/siswa/{id}
    Route::get('/nilai',                [ApiNilai::class, 'index']);         // existing
    Route::get('/nilai/{pertemuan_id}', [ApiNilai::class, 'show']);          // existing

    // Modul PDF
    Route::post('/modul/upload',                [ApiModul::class, 'upload']);
    Route::get('/modul/{pertemuan_id}',         [ApiModul::class, 'getByPertemuan']);
    Route::delete('/modul/{id}',                [ApiModul::class, 'destroy']);

    // Siswa
    Route::get('/siswa',        [ApiSiswa::class, 'index']);
    Route::get('/siswa/{id}',   [ApiSiswa::class, 'show']);
});