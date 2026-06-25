<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * Daftar semua modul.
     * GET /modul
     */
    public function index(): View
    {
        $modules = Module::orderBy('urutan')->get();
        return view('modul.index', compact('modules'));
    }

    /**
     * Form tambah modul.
     * GET /modul/create
     */
    public function create(): View
    {
        return view('modul.create');
    }

    /**
     * Simpan modul baru.
     * POST /modul
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'urutan'      => 'required|integer|min:1',
            'aktif'       => 'boolean',
        ]);

        Module::create($data);

        return redirect('/modul')->with('success', 'Modul berhasil ditambahkan.');
    }

    /**
     * Detail modul + materi + kuis.
     * GET /modul/{id}
     */
    public function show(int $id): View
    {
        $module = Module::with(['materials', 'quizzes'])->findOrFail($id);
        return view('modul.show', compact('module'));
    }

    /**
     * Form edit modul.
     * GET /modul/{id}/edit
     */
    public function edit(int $id): View
    {
        $module = Module::findOrFail($id);
        return view('modul.edit', compact('module'));
    }

    /**
     * Update modul.
     * PUT /modul/{id}
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'urutan'      => 'required|integer|min:1',
            'aktif'       => 'boolean',
        ]);

        Module::findOrFail($id)->update($data);

        return redirect('/modul')->with('success', 'Modul berhasil diupdate.');
    }

    /**
     * Hapus modul.
     * DELETE /modul/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        Module::findOrFail($id)->delete();
        return redirect('/modul')->with('success', 'Modul berhasil dihapus.');
    }
}