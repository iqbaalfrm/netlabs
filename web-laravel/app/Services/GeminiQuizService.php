<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk generate soal kuis menggunakan Gemini AI.
 * Mengirim konten materi ke Gemini API, mem-parse response JSON.
 */
class GeminiQuizService
{
    /**
     * Generate soal pilihan ganda berdasarkan konten materi modul.
     *
     * @param Module $module Modul yang materinya jadi sumber soal
     * @param int $jumlah Jumlah soal yang diinginkan (min 5, max 50)
     * @return array Array soal ['pertanyaan', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'jawaban_benar']
     * @throws \Exception Jika API gagal atau response tidak valid
     */
    public function generateQuestions(Module $module, int $jumlah = 10): array
    {
        // ─── 1. Ambil semua konten materi dari modul ───
        $kontenMateri = $this->kumpulkanKontenMateri($module);

        if (empty(trim($kontenMateri))) {
            throw new \Exception('Modul ini belum memiliki materi. Silakan tambahkan materi terlebih dahulu.');
        }

        // ─── 2. Susun prompt untuk Gemini ───
        $prompt = $this->buatPrompt($kontenMateri, $jumlah);

        // ─── 3. Kirim request ke Gemini API ───
        $responseJson = $this->kirimKeGemini($prompt);

        // ─── 4. Parse response JSON ───
        $soal = $this->parseResponse($responseJson);

        if (empty($soal)) {
            throw new \Exception('Gemini tidak menghasilkan soal. Coba lagi dengan prompt berbeda.');
        }

        return $soal;
    }

    /**
     * Kumpulkan semua konten materi dari modul menjadi satu string.
     */
    private function kumpulkanKontenMateri(Module $module): string
    {
        $materials = $module->materials()->orderBy('urutan')->get();
        $bagian = [];

        foreach ($materials as $materi) {
            $bagian[] = "JUDUL: {$materi->judul}\nKONTEN: {$materi->konten}";
        }

        return implode("\n\n---\n\n", $bagian);
    }

    /**
     * Buat prompt terstruktur untuk Gemini.
     */
    private function buatPrompt(string $kontenMateri, int $jumlah): string
    {
        return <<<PROMPT
Kamu adalah pembuat soal ujian SMK jurusan Teknik Komputer Jaringan.
Berdasarkan materi berikut, buatlah {$jumlah} soal pilihan ganda
dalam Bahasa Indonesia dengan ketentuan:
- Setiap soal punya 4 pilihan jawaban (a, b, c, d)
- Tingkat kesulitan bervariasi (mudah, sedang, sulit)
- Jawaban benar bervariasi, tidak selalu a
- Soal relevan dengan materi jaringan komputer SMK

Materi:
{$kontenMateri}

Kembalikan HANYA dalam format JSON array tanpa markdown code block:
[
  {
    "pertanyaan": "...",
    "opsi_a": "...",
    "opsi_b": "...",
    "opsi_c": "...",
    "opsi_d": "...",
    "jawaban_benar": "a"
  }
]
PROMPT;
    }

    /**
     * Kirim HTTP POST ke Gemini API dan kembalikan teks response.
     */
    private function kirimKeGemini(string $prompt): string
    {
        $apiKey = config('ai.gemini_api_key');
        $model  = config('ai.gemini_model');
        $url    = config('ai.gemini_url') . "{$model}:generateContent?key={$apiKey}";

        $response = Http::timeout(60)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if (!$response->successful()) {
            Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('Gagal menghubungi Gemini API. Status: ' . $response->status());
        }

        $body = $response->json();

        // Ambil teks dari response Gemini
        $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            Log::error('Gemini response kosong', ['body' => $body]);
            throw new \Exception('Gemini tidak mengembalikan teks.');
        }

        return $text;
    }

    /**
     * Parse teks response Gemini menjadi array soal.
     * Membersihkan markdown code block jika ada.
     */
    private function parseResponse(string $text): array
    {
        // Bersihkan markdown code block ```json ... ``` jika ada
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);

        $soal = json_decode($text, true);

        if (!is_array($soal)) {
            Log::error('Gemini response bukan JSON valid', ['text' => $text]);
            throw new \Exception('Response Gemini bukan format JSON yang valid.');
        }

        // Validasi struktur tiap soal
        $valid = [];
        foreach ($soal as $item) {
            if (isset($item['pertanyaan'], $item['opsi_a'], $item['opsi_b'], $item['opsi_c'], $item['opsi_d'], $item['jawaban_benar'])) {
                $valid[] = $item;
            }
        }

        return $valid;
    }
}