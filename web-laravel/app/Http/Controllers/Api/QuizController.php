<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Ambil daftar kuis untuk satu modul.
     * GET /api/modul/{moduleId}/kuis
     */
    public function index(int $moduleId): JsonResponse
    {
        $quizzes = Quiz::where('module_id', $moduleId)
                      ->where('aktif', true)
                      ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar kuis.',
            'data'    => $quizzes,
        ]);
    }

    /**
     * Ambil 5 soal random untuk satu kuis.
     * GET /api/kuis/{id}/soal
     */
    public function soal(int $id): JsonResponse
    {
        $quiz = Quiz::find($id);

        if (! $quiz) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        // Ambil 5 soal secara random
        $questions = Question::where('quiz_id', $id)
                            ->inRandomOrder()
                            ->take(5)
                            ->get()
                            ->makeHidden(['jawaban_benar']); // Sembunyikan jawaban

        return response()->json([
            'success' => true,
            'message' => 'Soal kuis.',
            'data'    => [
                'quiz'       => $quiz,
                'soal'       => $questions,
                'jumlah'     => $questions->count(),
            ],
        ]);
    }

    /**
     * Submit hasil kuis.
     * POST /api/kuis/hasil
     */
    public function hasil(Request $request): JsonResponse
    {
        $data = $request->validate([
            'quiz_id'  => 'required|exists:quizzes,id',
            'jawaban'  => 'required|array',      // { soal_id: "a", soal_id: "b" }
        ]);

        // Hitung skor
        $benar = 0;
        $total = count($data['jawaban']);

        foreach ($data['jawaban'] as $soalId => $jawabanSiswa) {
            $soal = Question::find($soalId);
            if ($soal && $soal->jawaban_benar === $jawabanSiswa) {
                $benar++;
            }
        }

        $skor = $total > 0 ? round(($benar / $total) * 100) : 0;

        // Simpan hasil
        $result = QuizResult::create([
            'user_id'    => auth('api')->id(),
            'quiz_id'    => $data['quiz_id'],
            'skor'       => $skor,
            'total_soal' => $total,
            'jawaban'    => $data['jawaban'],
            'selesai_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hasil kuis tersimpan.',
            'data'    => [
                'skor'     => $skor,
                'benar'    => $benar,
                'total'    => $total,
                'selesai_at' => $result->selesai_at,
            ],
        ]);
    }
}