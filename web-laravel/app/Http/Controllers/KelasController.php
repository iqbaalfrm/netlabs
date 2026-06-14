<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class KelasController extends Controller
{
    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        $kelas = $this->supabase->getKelas();
        return view('kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
        ]);

        $result = $this->supabase->createKelas([
            'nama_kelas' => $request->nama_kelas,
        ]);

        return redirect()->route('kelas.index')
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Kelas berhasil ditambahkan' : $result['message']);
    }

    public function show(string $id)
    {
        $kelas = $this->supabase->getKelasById($id);
        if (!$kelas) {
            return redirect()->route('kelas.index')->with('error', 'Kelas tidak ditemukan');
        }

        $siswa = $this->supabase->getSiswaByKelas($kelas['nama_kelas']);

        return view('kelas.show', compact('kelas', 'siswa'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
        ]);

        $result = $this->supabase->updateKelas($id, [
            'nama_kelas' => $request->nama_kelas,
        ]);

        return redirect()->route('kelas.show', $id)
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Kelas berhasil diperbarui' : $result['message']);
    }

    public function destroy(string $id)
    {
        $result = $this->supabase->deleteKelas($id);

        return redirect()->route('kelas.index')
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Kelas berhasil dihapus' : $result['message']);
    }
}
