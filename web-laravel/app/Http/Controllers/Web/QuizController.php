<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Module;
use App\Models\Question;
use App\Services\GeminiQuizService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Form tambah kuis ke modul tertentu.
     * GET /modul/{moduleId}/kuis/create
     */
    public function create(int $moduleId): View
    {
        $module = Module::findOrFail($moduleId);
        return view('kuis.create', compact('module'));
    }

    /**
     * Simpan kuis baru.
     * POST /modul/{moduleId}/kuis
     */
    public function store(Request $request, int $moduleId): RedirectResponse
    {
        $data = $request->validate([
            'judul'        => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
        ]);

        $data['module_id'] = $moduleId;
        $data['aktif'] = true;

        Quiz::create($data);

        return redirect("/modul/{$moduleId}")->with('success', 'Kuis berhasil ditambahkan.');
    }

    /**
     * Form edit kuis.
     * GET /kuis/{id}/edit
     */
    public function edit(int $id): View
    {
        $kuis = Quiz::findOrFail($id);
        return view('kuis.edit', compact('kuis'));
    }

    /**
     * Update kuis.
     * PUT /kuis/{id}
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'judul'        => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'aktif'        => 'boolean',
        ]);

        $kuis = Quiz::findOrFail($id);
        $kuis->update($data);

        return redirect("/modul/{$kuis->module_id}")->with('success', 'Kuis berhasil diupdate.');
    }

    /**
     * Hapus kuis.
     * DELETE /kuis/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $kuis = Quiz::findOrFail($id);
        $moduleId = $kuis->module_id;
        $kuis->delete();

        return redirect("/modul/{$moduleId}")->with('success', 'Kuis berhasil dihapus.');
    }

    // ─── Soal ─────────────────────────────────────────

    /**
     * Form tambah soal ke kuis tertentu.
     * GET /kuis/{kuisId}/soal/create
     */
    public function createSoal(int $kuisId): View
    {
        $kuis = Quiz::findOrFail($kuisId);
        $soal = Question::where('quiz_id', $kuisId)->orderBy('id')->get();
        return view('kuis.soal', compact('kuis', 'soal'));
    }

    /**
     * Simpan soal baru.
     * POST /kuis/{kuisId}/soal
     */
    public function storeSoal(Request $request, int $kuisId): RedirectResponse
    {
        $data = $request->validate([
            'pertanyaan'    => 'required|string|max:1000',
            'opsi_a'        => 'required|string|max:255',
            'opsi_b'        => 'required|string|max:255',
            'opsi_c'        => 'required|string|max:255',
            'opsi_d'        => 'required|string|max:255',
            'jawaban_benar' => 'required|in:a,b,c,d',
        ]);

        $data['quiz_id'] = $kuisId;

        Question::create($data);

        return redirect("/kuis/{$kuisId}/soal/create")->with('success', 'Soal berhasil ditambahkan.');
    }

    /**
     * Hapus soal.
     * DELETE /soal/{id}
     */
    public function destroySoal(int $id): RedirectResponse
    {
        $soal = Question::findOrFail($id);
        $kuisId = $soal->quiz_id;
        $soal->delete();

        return redirect("/kuis/{$kuisId}/soal/create")->with('success', 'Soal berhasil dihapus.');
    }

    // ─── AI Quiz Generator ──────────────────────────────

    /**
     * Form pengaturan generate soal AI.
     * GET /modul/{module}/quiz/generate
     */
    public function generateForm(Module $module): View
    {
        return view('kuis.generate', compact('module'));
    }

    /**
     * Proses generate soal AI dari Gemini.
     * POST /modul/{module}/quiz/generate
     */
    public function generate(Request $request, Module $module, GeminiQuizService $service): RedirectResponse
    {
        $data = $request->validate([
            'judul'        => 'required|string|max:255',
            'jumlah_soal'  => 'required|integer|min:5|max:50',
            'durasi_menit' => 'required|integer|min:1',
        ]);

        try {
            // Panggil Gemini API untuk generate soal
            $soalAI = $service->generateQuestions($module, (int) $data['jumlah_soal']);

            // Simpan kuis baru
            $kuis = Quiz::create([
                'module_id'    => $module->id,
                'judul'        => $data['judul'],
                'durasi_menit' => $data['durasi_menit'],
                'jumlah_soal'  => count($soalAI),
                'is_generated' => true,
                'generated_at' => now(),
                'aktif'        => false, // draft dulu
            ]);

            // Simpan semua soal hasil generate
            foreach ($soalAI as $item) {
                Question::create([
                    'quiz_id'       => $kuis->id,
                    'pertanyaan'    => $item['pertanyaan'],
                    'opsi_a'        => $item['opsi_a'],
                    'opsi_b'        => $item['opsi_b'],
                    'opsi_c'        => $item['opsi_c'],
                    'opsi_d'        => $item['opsi_d'],
                    'jawaban_benar' => $item['jawaban_benar'],
                ]);
            }

            return redirect("/kuis/{$kuis->id}/review")
                ->with('success', count($soalAI) . ' soal berhasil di-generate! Silakan review dan edit.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal generate soal: ' . $e->getMessage());
        }
    }

    /**
     * Halaman review & edit soal hasil generate AI.
     * GET /kuis/{quiz}/review
     */
    public function review(Quiz $quiz): View
    {
        $kuis = $quiz->load('questions', 'module');
        return view('kuis.review', compact('kuis'));
    }

    /**
     * Simpan hasil review/edit soal, lalu publish kuis.
     * POST /kuis/{quiz}/review
     */
    public function saveReview(Request $request, Quiz $quiz): RedirectResponse
    {
        // Update soal-soal dari form review
        if ($request->has('soal')) {
            foreach ($request->soal as $id => $data) {
                $soal = Question::find($id);
                if ($soal && $soal->quiz_id === $quiz->id) {
                    $soal->update([
                        'pertanyaan'    => $data['pertanyaan'],
                        'opsi_a'        => $data['opsi_a'],
                        'opsi_b'        => $data['opsi_b'],
                        'opsi_c'        => $data['opsi_c'],
                        'opsi_d'        => $data['opsi_d'],
                        'jawaban_benar' => $data['jawaban_benar'],
                    ]);
                }
            }
        }

        // Aktifkan kuis
        $quiz->update(['aktif' => true]);

        return redirect("/modul/{$quiz->module_id}")
            ->with('success', 'Kuis berhasil disimpan & dipublish.');
    }
}
