<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pertemuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PertemuanController extends Controller
{
    /**
     * Tampilkan semua pertemuan (urut nomor_urut ASC).
     * GET /api/pertemuan
     */
    public function index(): JsonResponse
    {
        $data = Pertemuan::orderBy('nomor_urut')->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Daftar pertemuan.',
        ]);
    }

    /**
     * Simpan pertemuan baru.
     * POST /api/pertemuan
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'judul'      => 'required|string|max:255',
            'nomor_urut' => 'required|integer|min:1',
            'deskripsi'  => 'nullable|string',
            'status'     => 'nullable|in:aktif,selesai,terkunci',
        ]);

        $pertemuan = Pertemuan::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $pertemuan,
            'message' => 'Pertemuan berhasil dibuat.',
        ], 201);
    }

    /**
     * Detail satu pertemuan + topik + soal_kuis.
     * GET /api/pertemuan/{id}
     */
    public function show($id): JsonResponse
    {
        $pertemuan = Pertemuan::with(['topik', 'soalKuis'])->find($id);

        if (! $pertemuan) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Pertemuan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $pertemuan,
            'message' => 'Detail pertemuan.',
        ]);
    }

    /**
     * Update pertemuan.
     * PUT /api/pertemuan/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $pertemuan = Pertemuan::find($id);

        if (! $pertemuan) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Pertemuan tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'judul'      => 'sometimes|string|max:255',
            'nomor_urut' => 'sometimes|integer|min:1',
            'deskripsi'  => 'nullable|string',
            'status'     => 'nullable|in:aktif,selesai,terkunci',
        ]);

        $pertemuan->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $pertemuan,
            'message' => 'Pertemuan berhasil diupdate.',
        ]);
    }

    /**
     * Hapus pertemuan.
     * DELETE /api/pertemuan/{id}
     */
    public function destroy($id): JsonResponse
    {
        $pertemuan = Pertemuan::find($id);

        if (! $pertemuan) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Pertemuan tidak ditemukan.',
            ], 404);
        }

        $pertemuan->delete();

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Pertemuan berhasil dihapus.',
        ]);
    }
}