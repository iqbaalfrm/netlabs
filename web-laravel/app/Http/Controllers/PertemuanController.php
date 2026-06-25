<?php

namespace App\Http\Controllers;

use App\Models\Pertemuan;
use App\Models\SoalKuis;
use App\Models\Topik;
use Illuminate\Http\Request;

class PertemuanController extends Controller
{
    /**
     * Daftar semua pertemuan (tabel)
     */
    public function index()
    {
        $pertemuan = Pertemuan::withCount(['topik', 'soal'])->orderBy('nomor_urut')->get();
        return view('pertemuan.index', compact('pertemuan'));
    }

    /**
     * Form tambah pertemuan
     */
    public function create()
    {
        return view('pertemuan.create');
    }

    /**
     * Simpan pertemuan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'      => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
            'nomor_urut' => 'required|integer|min:1',
            'warna_hex'  => 'nullable|string|max:7',
        ]);

        Pertemuan::create([
            'judul'      => $request->judul,
            'deskripsi'  => $request->deskripsi,
            'nomor_urut' => $request->nomor_urut,
            'warna_hex'  => $request->warna_hex ?? '#2D6A4F',
            'dibuat_oleh'=> session('guru_id'),
        ]);

        return redirect()->route('pertemuan.index')->with('success', 'Pertemuan berhasil ditambahkan');
    }

    /**
     * Detail pertemuan + 3 tab: Topik | Soal | Upload PDF
     */
    public function show($id)
    {
        $p = Pertemuan::with(['topik' => function ($q) {
            $q->orderBy('nomor_urut');
        }, 'soal'])->findOrFail($id);

        // Tab aktif (default: topik)
        $tab = request('tab', 'topik');

        return view('pertemuan.show', compact('p', 'tab'));
    }

    /**
     * Form edit pertemuan
     */
    public function edit($id)
    {
        $pertemuan = Pertemuan::findOrFail($id);
        return view('pertemuan.edit', compact('pertemuan'));
    }

    /**
     * Update pertemuan
     */
    public function update(Request $request, $id)
    {
        $pertemuan = Pertemuan::findOrFail($id);

        $request->validate([
            'judul'      => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
            'nomor_urut' => 'required|integer|min:1',
            'warna_hex'  => 'nullable|string|max:7',
        ]);

        $pertemuan->update($request->only(['judul', 'deskripsi', 'nomor_urut', 'warna_hex']));

        return redirect()->route('pertemuan.show', $id)->with('success', 'Pertemuan berhasil diperbarui');
    }

    /**
     * Hapus pertemuan beserta topik & soal terkait
     */
    public function destroy($id)
    {
        $pertemuan = Pertemuan::findOrFail($id);

        // Hapus topik, soal, modul terkait (cascade manual biar jelas)
        Topik::where('pertemuan_id', $id)->delete();
        SoalKuis::where('pertemuan_id', $id)->delete();
        \App\Models\ModulPdf::where('pertemuan_id', $id)->delete();

        $pertemuan->delete();

        return redirect()->route('pertemuan.index')->with('success', 'Pertemuan berhasil dihapus');
    }
}