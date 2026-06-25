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

        $userId = auth('api')->id();

        // Simpan pesan siswa ke database
        ChatHistory::create([
            'user_id'       => $userId,
            'pertemuan_id'  => $validated['pertemuan_id'] ?? null,
            'pesan'         => $validated['pertanyaan'],
            'jawaban'       => '',
        ]);

        // Forward ke RAG Service (dengan internal service key)
        // Backend FastAPI: POST /api/chat/tanya
        $ragUrl = env('RAG_SERVICE_URL', 'http://127.0.0.1:8000') . '/api/chat/tanya';
        $ragKey = env('RAG_SERVICE_KEY', '');
        $user   = auth('api')->user();

        try {
            $response = Http::timeout(30)->withHeaders([
                'X-Service-Key' => $ragKey,
                'X-User-Id'     => (string) $userId,
                'X-User-Role'   => $user->role ?? 'siswa',
            ])->post($ragUrl, [
                'pertanyaan'     => $validated['pertanyaan'],
                'pertemuan_id'   => $validated['pertemuan_id'] ?? null,
                'riwayat_chat'   => $validated['riwayat_chat'] ?? [],
            ]);

            if ($response->successful()) {
                $body = $response->json();
                $jawaban = $body['jawaban'] ?? 'Maaf, tidak ada jawaban.';
            } else {
                $jawaban = 'Layanan AI sedang sibuk, coba lagi nanti.';
            }
        } catch (\Exception $e) {
            $jawaban = 'Gagal terhubung ke layanan AI.';
        }

        // Simpan balasan AI ke database
        $chatBalasan = ChatHistory::create([
            'user_id'       => $userId,
            'pertemuan_id'  => $validated['pertemuan_id'] ?? null,
            'pesan'         => $validated['pertanyaan'],
            'jawaban'       => $jawaban,
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
     * GET /api/chat/riwayat/{user_id}
     */
    public function riwayat($siswaId): JsonResponse
    {
        $data = ChatHistory::where('user_id', $siswaId)
                           ->orderBy('waktu')
                           ->limit(100)
                           ->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Riwayat chat.',
        ]);
    }
}