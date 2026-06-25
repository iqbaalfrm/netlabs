<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class SiswaController extends Controller
{
    /**
     * Daftar semua siswa.
     * GET /api/siswa
     */
    public function index(): JsonResponse
    {
        // Hanya user dengan role 'siswa'
        $data = User::where('role', 'siswa')
                    ->orderBy('nama')
                    ->get(['id', 'nama', 'email', 'kelas']);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Daftar siswa.',
        ]);
    }

    /**
     * Detail satu siswa.
     * GET /api/siswa/{id}
     */
    public function show($id): JsonResponse
    {
        $siswa = User::where('role', 'siswa')->find($id);

        if (! $siswa) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Siswa tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $siswa->only('id', 'nama', 'email', 'kelas'),
            'message' => 'Detail siswa.',
        ]);
    }
}