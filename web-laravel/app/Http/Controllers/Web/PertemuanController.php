<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pertemuan;
use App\Models\ModulPdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PertemuanController extends Controller
{
    /**
     * Daftar semua pertemuan. GET /pertemuan
     */
    public function index(): View
    {
        $pertemuan = Pertemuan::withCount(['topik', 'soalKuis'])
                              ->orderBy('urutan')
                              ->get();

        return view('pertemuan.index', compact('pertemuan'));
    }

    /**
     * Form tambah pertemuan. GET /pertemuan/create
     */
    public function create(): View
    {
        return view('pertemuan.create');
    }

    /**
     * Simpan pertemuan baru. POST /pertemuan
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'urutan'       => 'required|integer|min:1',
            'tanggal'      => 'nullable|date',
            'kode_ruangan' => 'nullable|string|max:50',
        ]);

        Pertemuan::create($validated);

        return redirect('/pertemuan')->with('success', 'Pertemuan berhasil ditambahkan.');
    }

    /**
     * Detail pertemuan + 3 tab: Topik | Soal | Upload PDF. GET /pertemuan/{id}
     */
    public function show($id): View
    {
        $pertemuan = Pertemuan::with(['topik', 'soalKuis', 'modulPdf'])->findOrFail($id);

        return view('pertemuan.show', compact('pertemuan'));
    }

    /**
     * Form edit pertemuan. GET /pertemuan/{id}/edit
     */
    public function edit($id): View
    {
        $pertemuan = Pertemuan::findOrFail($id);

        return view('pertemuan.edit', compact('pertemuan'));
    }

    /**
     * Update pertemuan. PUT /pertemuan/{id}
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $pertemuan = Pertemuan::findOrFail($id);

        $validated = $request->validate([
            'judul'        => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'urutan'       => 'required|integer|min:1',
            'tanggal'      => 'nullable|date',
            'kode_ruangan' => 'nullable|string|max:50',
        ]);

        $pertemuan->update($validated);

        return redirect('/pertemuan')->with('success', 'Pertemuan berhasil diperbarui.');
    }

    /**
     * Hapus pertemuan. DELETE /pertemuan/{id}
     */
    public function destroy($id): RedirectResponse
    {
        Pertemuan::destroy($id);

        return redirect('/pertemuan')->with('success', 'Pertemuan berhasil dihapus.');
    }

    /**
     * Upload PDF modul untuk pertemuan. POST /pertemuan/{id}/modul
     */
    public function uploadModul(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'file'  => 'required|file|mimes:pdf|max:10240',
        ]);

        $path = $request->file('file')->store('modul', 'public');

        ModulPdf::create([
            'pertemuan_id' => $id,
            'judul'        => $request->judul,
            'file_path'    => $path,
        ]);

        return back()->with('success', 'Modul PDF berhasil diupload.');
    }
}