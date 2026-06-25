<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * Kirim pesan ke RAG Service lalu simpan ke database.
     * POST /api/chat
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pertanyaan'     => 'required|string',
            'pertemuan_id'   => 'nullable|integer|exists:pertemuan,id',
            'riwayat_chat'   => 'nullable|array',
        ]);

        $siswaId = auth('api')->id();

        // Simpan pesan siswa
        ChatHistory::create([
            'siswa_id'      => $siswaId,
            'pertemuan_id'  => $validated['pertemuan_id'] ?? null,
            'dari_siswa'    => true,
            'pesan'         => $validated['pertanyaan'],
        ]);

        // Forward ke RAG Service
        $ragUrl = env('RAG_SERVICE_URL', 'http://127.0.0.1:5001') . '/rag/chat';

        try {
            $response = Http::timeout(30)->post($ragUrl, [
                'pertanyaan'    => $validated['pertanyaan'],
                'riwayat_chat'  => $validated['riwayat_chat'] ?? [],
            ]);

            if ($response->successful()) {
                $body = $response->json();
                $jawaban = $body['jawaban'] ?? $body['answer'] ?? $body['response'] ?? 'Maaf, tidak ada jawaban.';
            } else {
                $jawaban = 'Layanan AI sedang sibuk, coba lagi nanti.';
            }
        } catch (\Exception $e) {
            $jawaban = 'Gagal terhubung ke layanan AI.';
        }

        // Simpan balasan AI
        $chatBalasan = ChatHistory::create([
            'siswa_id'      => $siswaId,
            'pertemuan_id'  => $validated['pertemuan_id'] ?? null,
            'dari_siswa'    => false,
            'pesan'         => $jawaban,
        ]);

        return response()->json([
            'success'  => true,
            'data'     => $chatBalasan,
            'jawaban'  => $jawaban,
            'message'  => 'Jawaban diterima.',
        ]);
    }

    /**
     * Riwayat chat per siswa.
     * GET /api/chat/riwayat/{siswa_id}
     */
    public function riwayat($siswaId): JsonResponse
    {
        $data = ChatHistory::where('siswa_id', $siswaId)
                           ->orderBy('created_at')
                           ->limit(100)
                           ->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Riwayat chat.',
        ]);
    }
}