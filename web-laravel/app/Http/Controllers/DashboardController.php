<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use App\Models\Pertemuan;
use App\Models\SoalKuis;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Dashboard guru — 4 stat card + tabel 10 chat terbaru
     */
    public function index()
    {
        // 4 stat card
        $stats = [
            'total_siswa'    => User::where('role', 'siswa')->count(),
            'total_pertemuan' => Pertemuan::count(),
            'total_soal'      => SoalKuis::count(),
            'total_chat'      => ChatHistory::count(),
        ];

        // 10 chat terbaru
        $chatTerbaru = ChatHistory::with(['user:id,nama,nis', 'pertemuan:id,judul'])
            ->latest('waktu')
            ->limit(10)
            ->get()
            ->map(function ($c) {
                return [
                    'id'              => $c->id,
                    'siswa_nama'      => $c->user->nama ?? '-',
                    'siswa_nis'       => $c->user->nis ?? '-',
                    'pertemuan_judul' => $c->pertemuan->judul ?? '-',
                    'pesan'           => \Illuminate\Support\Str::limit($c->pesan, 60),
                    'jawaban'         => \Illuminate\Support\Str::limit($c->jawaban, 80),
                    'waktu'           => $c->waktu,
                ];
            });

        return view('dashboard', compact('stats', 'chatTerbaru'));
    }
}