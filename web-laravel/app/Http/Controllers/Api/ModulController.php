<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModulPdf;
use App\Models\Pertemuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModulController extends Controller
{
    /**
     * Upload file PDF modul.
     * POST /api/modul/upload
     */
    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pertemuan_id' => 'required|exists:pertemuan,id',
            'judul'        => 'required|string|max:255',
            'file'         => 'required|file|mimes:pdf|max:10240',
        ]);

        // Simpan file ke storage/app/public/modul/
        $path = $request->file('file')->store('modul', 'public');

        $modul = ModulPdf::create([
            'pertemuan_id' => $validated['pertemuan_id'],
            'judul'        => $validated['judul'],
            'file_path'    => $path,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $modul,
            'message' => 'Modul berhasil diupload.',
        ], 201);
    }

    /**
     * Ambil modul PDF per pertemuan.
     * GET /api/modul/{pertemuan_id}
     */
    public function getByPertemuan($pertemuanId): JsonResponse
    {
        $pertemuan = Pertemuan::find($pertemuanId);

        if (! $pertemuan) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Pertemuan tidak ditemukan.',
            ], 404);
        }

        $data = ModulPdf::where('pertemuan_id', $pertemuanId)->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Daftar modul.',
        ]);
    }
}