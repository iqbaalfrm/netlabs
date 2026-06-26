<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HasilKuis;
use App\Models\Pertemuan;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class NilaiController extends Controller
{
    /**
     * Ambil semua nilai siswa (untuk guru).
     * GET /api/nilai/semua
     */
    public function semua(): JsonResponse
    {
        $nilai = HasilKuis::with(['user:id,nis,nama', 'pertemuan:id,judul'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $nilai,
            'message' => 'Daftar nilai semua siswa.',
        ]);
    }

    /**
     * Ambil nilai satu siswa berdasarkan ID (untuk guru).
     * GET /api/nilai/siswa/{siswa_id}
     */
    public function siswa($siswaId): JsonResponse
    {
        $siswa = User::find($siswaId);

        if (! $siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan.',
            ], 404);
        }

        $nilai = HasilKuis::with('pertemuan:id,judul')
            ->where('user_id', $siswaId)
            ->orderBy('created_at', 'desc')
            ->get();

        $rataRata = $nilai->count() > 0 ? round($nilai->avg('skor')) : 0;

        return response()->json([
            'success'   => true,
            'data'      => $nilai,
            'siswa'     => $siswa,
            'rata_rata' => $rataRata,
            'message'   => 'Daftar nilai siswa.',
        ]);
    }

    /**
     * Ambil semua nilai siswa yang sedang login.
     * GET /api/nilai
     */
    public function index(): JsonResponse
    {
        $userId = auth('api')->id();

        $nilai = HasilKuis::with('pertemuan:id,judul')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $rataRata = $nilai->count() > 0 ? round($nilai->avg('skor')) : 0;

        return response()->json([
            'success' => true,
            'data'    => $nilai,
            'rata_rata' => $rataRata,
            'message' => 'Daftar nilai siswa.',
        ]);
    }

    /**
     * Ambil detail nilai per pertemuan.
     * GET /api/nilai/{pertemuan_id}
     */
    public function show($pertemuanId): JsonResponse
    {
        $userId = auth('api')->id();

        $pertemuan = Pertemuan::find($pertemuanId);

        if (! $pertemuan) {
            return response()->json([
                'success' => false,
                'message' => 'Pertemuan tidak ditemukan.',
            ], 404);
        }

        $nilai = HasilKuis::where('user_id', $userId)
            ->where('pertemuan_id', $pertemuanId)
            ->first();

        return response()->json([
            'success' => true,
            'data'    => $nilai,
            'message' => 'Detail nilai.',
        ]);
    }
}