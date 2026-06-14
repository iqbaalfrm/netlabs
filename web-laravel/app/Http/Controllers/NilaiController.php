<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class NilaiController extends Controller
{
    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index(Request $request)
    {
        $kelas = $this->supabase->getKelas();
        $pertemuan = $this->supabase->getPertemuan();

        $filterKelas = $request->query('kelas');
        $filterPertemuan = $request->query('pertemuan_id');

        $siswa = $filterKelas
            ? $this->supabase->getSiswaByKelas($filterKelas)
            : $this->supabase->getSiswa();

        $rekap = $this->supabase->getRekap();

        // Build matrix: siswa x pertemuan
        $nilaiMap = [];
        foreach ($rekap as $r) {
            $key = $r['siswa_id'] . '_' . $r['pertemuan_id'];
            if (!isset($nilaiMap[$key]) || $r['nilai'] > $nilaiMap[$key]) {
                $nilaiMap[$key] = $r['nilai'];
            }
        }

        // Filter pertemuan if specified
        $displayPertemuan = $filterPertemuan
            ? array_filter($pertemuan, fn($p) => $p['id'] === $filterPertemuan)
            : $pertemuan;

        return view('nilai.index', compact(
            'kelas', 'pertemuan', 'siswa', 'nilaiMap',
            'displayPertemuan', 'filterKelas', 'filterPertemuan'
        ));
    }

    public function export(Request $request)
    {
        $filterKelas = $request->query('kelas');
        $pertemuan = $this->supabase->getPertemuan();

        $siswa = $filterKelas
            ? $this->supabase->getSiswaByKelas($filterKelas)
            : $this->supabase->getSiswa();

        $rekap = $this->supabase->getRekap();

        $nilaiMap = [];
        foreach ($rekap as $r) {
            $key = $r['siswa_id'] . '_' . $r['pertemuan_id'];
            if (!isset($nilaiMap[$key]) || $r['nilai'] > $nilaiMap[$key]) {
                $nilaiMap[$key] = $r['nilai'];
            }
        }

        // Generate CSV
        $filename = 'nilai_' . ($filterKelas ?: 'semua') . '_' . date('Ymd') . '.csv';

        $headers = ['Nama', 'NIS', 'Kelas'];
        foreach ($pertemuan as $p) {
            $headers[] = 'P' . $p['nomor_urut'];
        }
        $headers[] = 'Rata-rata';

        $rows = [];
        foreach ($siswa as $s) {
            $row = [$s['nama'], $s['nis'], $s['kelas'] ?? '-'];
            $nilaiSiswa = [];
            foreach ($pertemuan as $p) {
                $key = $s['id'] . '_' . $p['id'];
                $nilai = $nilaiMap[$key] ?? '-';
                $row[] = $nilai;
                if (is_numeric($nilai)) $nilaiSiswa[] = $nilai;
            }
            $row[] = count($nilaiSiswa) > 0 ? round(array_sum($nilaiSiswa) / count($nilaiSiswa), 1) : '-';
            $rows[] = $row;
        }

        $callback = function() use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
