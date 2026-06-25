<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HasilKuis;
use App\Models\Pertemuan;
use Illuminate\Http\JsonResponse;

class NilaiController extends Controller
{
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