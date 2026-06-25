<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topik;
use App\Models\Pertemuan;
use App\Models\ProgressTopik;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopikController extends Controller
{
    /**
     * Daftar topik per pertemuan.
     * GET /api/pertemuan/{id}/topik
     */
    public function index($pertemuanId): JsonResponse
    {
        $pertemuan = Pertemuan::find($pertemuanId);

        if (! $pertemuan) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Pertemuan tidak ditemukan.',
            ], 404);
        }

        $data = Topik::where('pertemuan_id', $pertemuanId)
                     ->orderBy('nomor_urut')
                     ->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Daftar topik.',
        ]);
    }

    /**
     * Buat topik baru.
     * POST /api/pertemuan/{id}/topik
     */
    public function store(Request $request, $pertemuanId): JsonResponse
    {
        $pertemuan = Pertemuan::find($pertemuanId);

        if (! $pertemuan) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Pertemuan tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'judul'      => 'required|string|max:255',
            'nomor_urut' => 'required|integer|min:1',
            'isi'        => 'nullable|string',
        ]);
        $validated['pertemuan_id'] = $pertemuanId;

        $topik = Topik::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $topik,
            'message' => 'Topik berhasil dibuat.',
        ], 201);
    }

    /**
     * Detail satu topik.
     * GET /api/topik/{id}
     */
    public function show($id): JsonResponse
    {
        $topik = Topik::find($id);

        if (! $topik) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Topik tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $topik,
            'message' => 'Detail topik.',
        ]);
    }

    /**
     * Update topik.
     * PUT /api/topik/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $topik = Topik::find($id);

        if (! $topik) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Topik tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'judul'      => 'sometimes|string|max:255',
            'nomor_urut' => 'sometimes|integer|min:1',
            'isi'       => 'nullable|string',
        ]);

        $topik->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $topik,
            'message' => 'Topik berhasil diupdate.',
        ]);
    }

    /**
     * Hapus topik.
     * DELETE /api/topik/{id}
     */
    public function destroy($id): JsonResponse
    {
        $topik = Topik::find($id);

        if (! $topik) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Topik tidak ditemukan.',
            ], 404);
        }

        $topik->delete();

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Topik berhasil dihapus.',
        ]);
    }

    /**
     * Tandai topik selesai oleh siswa.
     * POST /api/topik/{id}/selesai
     */
    public function tandaiSelesai($id): JsonResponse
    {
        $topik = Topik::find($id);

        if (! $topik) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Topik tidak ditemukan.',
            ], 404);
        }

        $siswaId = auth('api')->id();

        ProgressTopik::firstOrCreate(
            [
                'topik_id' => $id,
                'siswa_id' => $siswaId,
            ],
            [
                'is_selesai'  => true,
                'selesai_pada' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Topik ditandai selesai.',
        ]);
    }
}