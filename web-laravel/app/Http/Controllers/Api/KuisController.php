<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SoalKuis;
use App\Models\HasilKuis;
use App\Models\Pertemuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KuisController extends Controller
{
    /**
     * Ambil 5 soal random untuk satu pertemuan.
     * GET /api/kuis/{pertemuan_id}
     */
    public function getSoal($pertemuanId): JsonResponse
    {
        $pertemuan = Pertemuan::find($pertemuanId);

        if (! $pertemuan) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Pertemuan tidak ditemukan.',
            ], 404);
        }

        $soal = SoalKuis::where('pertemuan_id', $pertemuanId)
                        ->inRandomOrder()
                        ->limit(5)
                        ->get();

        return response()->json([
            'success' => true,
            'data'    => $soal,
            'message' => 'Soal kuis.',
        ]);
    }

    /**
     * Tambah soal kuis baru.
     * POST /api/kuis/soal
     */
    public function storeSoal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pertemuan_id' => 'required|exists:pertemuan,id',
            'pertanyaan'   => 'required|string',
            'pilihan_a'    => 'required|string',
            'pilihan_b'    => 'required|string',
            'pilihan_c'    => 'required|string',
            'pilihan_d'    => 'required|string',
            'kunci'        => 'required|in:a,b,c,d,e',
        ]);

        $soal = SoalKuis::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $soal,
            'message' => 'Soal berhasil ditambahkan.',
        ], 201);
    }

    /**
     * Update soal kuis.
     * PUT /api/kuis/soal/{id}
     */
    public function updateSoal(Request $request, $id): JsonResponse
    {
        $soal = SoalKuis::find($id);

        if (! $soal) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Soal tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'pertanyaan' => 'sometimes|string',
            'pilihan_a'  => 'sometimes|string',
            'pilihan_b'  => 'sometimes|string',
            'pilihan_c'  => 'sometimes|string',
            'pilihan_d'  => 'sometimes|string',
            'kunci'      => 'sometimes|in:a,b,c,d,e',
        ]);

        $soal->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $soal,
            'message' => 'Soal berhasil diupdate.',
        ]);
    }

    /**
     * Hapus soal kuis.
     * DELETE /api/kuis/soal/{id}
     */
    public function destroySoal($id): JsonResponse
    {
        $soal = SoalKuis::find($id);

        if (! $soal) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Soal tidak ditemukan.',
            ], 404);
        }

        $soal->delete();

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Soal berhasil dihapus.',
        ]);
    }

    /**
     * Riwayat hasil kuis siswa.
     * GET /api/kuis/riwayat/{user_id}
     */
    public function riwayat($userId): JsonResponse
    {
        $riwayat = HasilKuis::with('pertemuan:id,judul')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $riwayat,
            'message' => 'Riwayat kuis.',
        ]);
    }

    /**
     * Riwayat hasil kuis siswa yang sedang login.
     * GET /api/kuis/riwayat  (tanpa parameter — ambil dari JWT)
     */
    public function riwayatSaya(): JsonResponse
    {
        $userId = auth('api')->id();

        $riwayat = HasilKuis::with('pertemuan:id,judul')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $riwayat,
            'message' => 'Riwayat kuis.',
        ]);
    }

    /**
     * Submit hasil kuis — pertemuan_id dari URL.
     * POST /api/kuis/{pertemuan_id}/jawaban
     * Body: { jawaban: [ { soal_id, jawaban }, ... ] }
     */
    public function submitJawaban($pertemuanId, Request $request): JsonResponse
    {
        $pertemuan = Pertemuan::find($pertemuanId);
        if (! $pertemuan) {
            return response()->json([
                'success' => false,
                'message' => 'Pertemuan tidak ditemukan.',
            ], 404);
        }

        // Inject pertemuan_id dari URL ke body lalu delegasikan ke submitHasil
        $data = $request->all();
        $data['pertemuan_id'] = $pertemuanId;
        $request->merge(['pertemuan_id' => $pertemuanId]);

        return $this->submitHasil($request);
    }

    /**
     * Submit hasil kuis.
     * POST /api/kuis/hasil
     * Alias: POST /api/kuis/jawaban
     */
    public function submitHasil(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pertemuan_id' => 'required|exists:pertemuan,id',
            'jawaban'      => 'required|array',
            'jawaban.*.soal_id' => 'required|exists:soal_kuis,id',
            'jawaban.*.jawaban' => 'required|string|in:a,b,c,d,e',
        ]);

        $siswaId = auth('api')->id();

        // Cek apakah siswa sudah pernah submit
        $sudah = HasilKuis::where('user_id', $siswaId)
                           ->where('pertemuan_id', $validated['pertemuan_id'])
                           ->exists();

        if ($sudah) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Kuis sudah pernah dikerjakan.',
            ], 422);
        }

        $jumlahBenar = 0;
        $totalSoal = count($validated['jawaban']);

        foreach ($validated['jawaban'] as $jw) {
            $soal = SoalKuis::find($jw['soal_id']);
            if ($soal && $jw['jawaban'] === $soal->kunci) {
                $jumlahBenar++;
            }
        }

        $jumlahSalah = $totalSoal - $jumlahBenar;
        $skor = $totalSoal > 0 ? round(($jumlahBenar / $totalSoal) * 100) : 0;

        $hasil = HasilKuis::create([
            'user_id'       => $siswaId,
            'pertemuan_id'  => $validated['pertemuan_id'],
            'benar'         => $jumlahBenar,
            'salah'         => $jumlahSalah,
            'skor'          => $skor,
        ]);

        return response()->json([
            'success'       => true,
            'data'          => $hasil,
            'jumlah_benar'  => $jumlahBenar,
            'total_soal'    => $totalSoal,
            'message'       => 'Kuis berhasil dikumpulkan.',
        ], 201);
    }
}