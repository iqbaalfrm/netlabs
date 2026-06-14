<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;

class DashboardController extends Controller
{
    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Halaman utama dashboard guru
     */
    public function index()
    {
        // Ambil statistik untuk kartu di dashboard
        $stats = $this->supabase->getDashboardStats();

        // Ambil 5 pertemuan terbaru
        $pertemuan = $this->supabase->getPertemuan();

        // Ambil 5 siswa terbaru
        $siswa = $this->supabase->getSiswa();
        $siswa5Terbaru = array_slice($siswa, 0, 5);

        // Ambil hasil kuis terbaru
        $rekap = $this->supabase->getRekap();
        $rekap5Terbaru = array_slice($rekap, 0, 5);

        return view('dashboard', compact('stats', 'pertemuan', 'siswa5Terbaru', 'rekap5Terbaru'));
    }
}
