<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModulPdf;
use App\Models\Pertemuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ModulController extends Controller
{
    /**
     * Upload file PDF modul + forward ke Python RAG Service untuk indexing.
     * POST /api/modul/upload
     */
    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pertemuan_id' => 'required|exists:pertemuan,id',
            'file'         => 'required|file|mimes:pdf|max:10240',
        ]);

        // Simpan file ke storage/app/public/modul/
        $path = $request->file('file')->store('modul', 'public');
        $fileName = $request->file('file')->getClientOriginalName();
        $fileSize = $request->file('file')->getSize();

        $modul = ModulPdf::create([
            'pertemuan_id'  => $validated['pertemuan_id'],
            'nama_file'     => $fileName,
            'path'          => $path,
            'ukuran_bytes'  => $fileSize,
            'diupload_oleh' => auth('api')->id(),
            'sudah_diindex' => false,
        ]);

        // ── Forward PDF ke Python RAG Service untuk indexing ke ChromaDB ──
        $ragIndexed = $this->forwardToRagService(
            $request->file('file'),
            $validated['pertemuan_id'],
            $fileName
        );

        $modul->sudah_diindex = $ragIndexed;
        $modul->save();

        return response()->json([
            'success'      => true,
            'data'         => $modul,
            'rag_indexed'  => $ragIndexed,
            'message'      => $ragIndexed
                ? 'Modul berhasil diupload dan diindex ke RAG.'
                : 'Modul diupload, tapi RAG indexing gagal. Coba re-index nanti.',
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

    /**
     * Hapus modul PDF + notifikasi Python untuk hapus chunks dari ChromaDB.
     * DELETE /api/modul/{id}
     */
    public function destroy($id): JsonResponse
    {
        $modul = ModulPdf::find($id);

        if (! $modul) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan.',
            ], 404);
        }

        // Hapus file dari storage
        if ($modul->path && Storage::disk('public')->exists($modul->path)) {
            Storage::disk('public')->delete($modul->path);
        }

        // Notifikasi Python untuk hapus chunks dari ChromaDB
        $this->notifyRagDelete($modul->pertemuan_id, $modul->nama_file);

        $modul->delete();

        return response()->json([
            'success' => true,
            'message' => 'Modul berhasil dihapus.',
        ]);
    }

    /**
     * Forward PDF file ke Python RAG Service untuk indexing.
     */
    private function forwardToRagService($file, $pertemuanId, $fileName): bool
    {
        $ragUrl = rtrim(env('RAG_SERVICE_URL', 'http://127.0.0.1:8000'), '/') . '/api/modul/upload';
        $ragKey = env('RAG_SERVICE_KEY', '');
        $user   = auth('api')->user();

        try {
            $response = Http::timeout(60)->withHeaders([
                'X-Service-Key' => $ragKey,
                'X-User-Id'     => (string) ($user->id ?? ''),
                'X-User-Role'   => $user->role ?? 'guru',
            ])->attach('file', file_get_contents($file->getRealPath()), $fileName)
              ->post($ragUrl, [
                  'pertemuan_id' => (string) $pertemuanId,
              ]);

            if ($response->successful()) {
                Log::info("RAG indexing berhasil untuk modul pertemuan {$pertemuanId}");
                return true;
            }

            Log::error("RAG indexing gagal: HTTP {$response->status()} - {$response->body()}");
            return false;
        } catch (\Exception $e) {
            Log::error("RAG service error: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Notifikasi Python RAG Service untuk hapus chunks dari ChromaDB.
     */
    private function notifyRagDelete($pertemuanId, $fileName): void
    {
        $ragUrl = rtrim(env('RAG_SERVICE_URL', 'http://127.0.0.1:8000'), '/') . '/api/modul/delete-by-name';
        $ragKey = env('RAG_SERVICE_KEY', '');

        try {
            Http::timeout(10)->withHeaders([
                'X-Service-Key' => $ragKey,
            ])->post($ragUrl, [
                'pertemuan_id' => (string) $pertemuanId,
                'nama_file'    => $fileName,
            ]);
        } catch (\Exception $e) {
            Log::warning("Gagal notifikasi RAG delete: {$e->getMessage()}");
        }
    }
}