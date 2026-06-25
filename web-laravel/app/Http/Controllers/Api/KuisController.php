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
            'jawaban'      => 'required|integer|min:0|max:3',
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
            'jawaban'    => 'sometimes|integer|min:0|max:3',
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
     * Submit hasil kuis.
     * POST /api/kuis/hasil
     */
    public function submitHasil(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pertemuan_id' => 'required|exists:pertemuan,id',
            'jawaban'      => 'required|array',
            'jawaban.*.soal_id' => 'required|exists:soal_kuis,id',
            'jawaban.*.jawaban' => 'required|string|in:a,b,c,d',
        ]);

        $siswaId = auth('api')->id();

        // Cek apakah siswa sudah pernah submit
        $sudah = HasilKuis::where('siswa_id', $siswaId)
                          ->where('pertemuan_id', $validated['pertemuan_id'])
                          ->exists();

        if ($sudah) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Kuis sudah pernah dikerjakan.',
            ], 422);
        }

        $hurufKeIndex = ['a' => 0, 'b' => 1, 'c' => 2, 'd' => 3];
        $jumlahBenar = 0;
        $totalSoal = count($validated['jawaban']);

        foreach ($validated['jawaban'] as $jw) {
            $soal = SoalKuis::find($jw['soal_id']);
            if ($soal && $hurufKeIndex[$jw['jawaban']] === (int) $soal->jawaban) {
                $jumlahBenar++;
            }
        }

        $nilai = $totalSoal > 0 ? round(($jumlahBenar / $totalSoal) * 100) : 0;

        $hasil = HasilKuis::create([
            'siswa_id'      => $siswaId,
            'pertemuan_id'  => $validated['pertemuan_id'],
            'jumlah_benar'  => $jumlahBenar,
            'total_soal'    => $totalSoal,
            'nilai'         => $nilai,
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