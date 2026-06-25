<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use App\Models\HasilKuis;
use App\Models\User;

class SiswaController extends Controller
{
    /**
     * Daftar siswa + fitur search
     */
    public function index()
    {
        $search = request('search');
        $siswa = User::where('role', 'siswa')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('kelas', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        return view('siswa.index', compact('siswa', 'search'));
    }

    /**
     * Profil siswa + riwayat kuis + riwayat chat
     */
    public function show($id)
    {
        $siswa = User::where('role', 'siswa')->findOrFail($id);

        // Riwayat kuis — format array agar view sederhana
        $riwayatKuis = HasilKuis::where('user_id', $id)
            ->with('pertemuan:id,judul')
            ->latest('dikerjakan_pada')
            ->limit(20)
            ->get()
            ->map(function ($h) {
                return [
                    'pertemuan_judul' => $h->pertemuan->judul ?? '-',
                    'benar'           => $h->benar,
                    'salah'           => $h->salah,
                    'skor'            => $h->skor,
                    'waktu'           => $h->dikerjakan_pada,
                ];
            });

        // Riwayat chat — format array agar view sederhana
        $riwayatChat = ChatHistory::where('user_id', $id)
            ->with('pertemuan:id,judul')
            ->latest('waktu')
            ->limit(20)
            ->get()
            ->map(function ($c) {
                return [
                    'pertemuan_judul' => $c->pertemuan->judul ?? '-',
                    'pesan'           => \Illuminate\Support\Str::limit($c->pesan, 60),
                    'jawaban'         => \Illuminate\Support\Str::limit($c->jawaban, 80),
                    'waktu'           => $c->waktu,
                ];
            });

        return view('siswa.show', compact('siswa', 'riwayatKuis', 'riwayatChat'));
    }
}