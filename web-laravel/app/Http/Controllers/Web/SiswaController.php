<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HasilKuis;
use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaController extends Controller
{
    /**
     * Daftar siswa + search.
     * GET /siswa
     */
    public function index(Request $request): View
    {
        $search = $request->get('cari');

        $siswa = User::where('role', 'siswa')
                     ->when($search, function ($q) use ($search) {
                         $q->where('nama', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                     })
                     ->orderBy('nama')
                     ->paginate(15)
                     ->withQueryString();

        return view('siswa.index', compact('siswa', 'search'));
    }

    /**
     * Profil siswa + riwayat kuis + riwayat chat.
     * GET /siswa/{id}
     */
    public function show($id): View
    {
        $siswa = User::where('role', 'siswa')->findOrFail($id);
        $riwayatKuis = HasilKuis::with('pertemuan:id,judul')
                               ->where('user_id', $id)
                               ->orderBy('dikerjakan_pada', 'desc')
                               ->limit(20)
                               ->get();
        $riwayatChat = ChatHistory::with('pertemuan:id,judul')
                                  ->where('user_id', $id)
                                  ->orderBy('waktu', 'desc')
                                 ->limit(20)
                                 ->get();

        return view('siswa.show', compact('siswa', 'riwayatKuis', 'riwayatChat'));
    }
}