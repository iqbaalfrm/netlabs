<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class KuisController extends Controller
{
    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        $pertemuan = $this->supabase->getPertemuan();

        $kuisData = [];
        foreach ($pertemuan as $p) {
            $soal = $this->supabase->getSoalKuis($p['id']);
            $kuisData[] = [
                'pertemuan' => $p,
                'jumlah_soal' => count($soal),
            ];
        }

        return view('kuis.index', compact('kuisData'));
    }

    public function show(string $pertemuanId)
    {
        $pertemuan = $this->supabase->getPertemuanById($pertemuanId);
        if (!$pertemuan) {
            return redirect()->route('kuis.index')->with('error', 'Pertemuan tidak ditemukan');
        }

        $soal = $this->supabase->getSoalKuis($pertemuanId);

        return view('kuis.show', compact('pertemuan', 'soal'));
    }

    public function storeSoal(Request $request, string $pertemuanId)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D',
        ]);

        $result = $this->supabase->createSoal([
            'pertemuan_id' => $pertemuanId,
            'pertanyaan' => $request->pertanyaan,
            'pilihan_a' => $request->pilihan_a,
            'pilihan_b' => $request->pilihan_b,
            'pilihan_c' => $request->pilihan_c,
            'pilihan_d' => $request->pilihan_d,
            'jawaban_benar' => $request->jawaban_benar,
            'penjelasan' => $request->penjelasan,
        ]);

        return redirect()->route('kuis.show', $pertemuanId)
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Soal berhasil ditambahkan' : $result['message']);
    }

    public function destroySoal(string $pertemuanId, string $soalId)
    {
        $result = $this->supabase->deleteSoal($soalId);

        return redirect()->route('kuis.show', $pertemuanId)
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Soal berhasil dihapus' : $result['message']);
    }

    public function storeSoalByTopik(Request $request, string $topikId)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D',
            'pertemuan_id' => 'required|string',
        ]);

        $result = $this->supabase->createSoal([
            'pertemuan_id' => $request->pertemuan_id,
            'topik_id' => $topikId,
            'pertanyaan' => $request->pertanyaan,
            'pilihan_a' => $request->pilihan_a,
            'pilihan_b' => $request->pilihan_b,
            'pilihan_c' => $request->pilihan_c,
            'pilihan_d' => $request->pilihan_d,
            'jawaban_benar' => $request->jawaban_benar,
            'penjelasan' => $request->penjelasan,
        ]);

        return redirect()->route('topik.show', [$request->pertemuan_id, $topikId])
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Soal berhasil ditambahkan' : $result['message']);
    }

    public function destroySoalById(string $soalId)
    {
        $result = $this->supabase->deleteSoal($soalId);

        return back()->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Soal berhasil dihapus' : $result['message']);
    }
}
