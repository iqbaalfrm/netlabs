<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialController extends Controller
{
    /**
     * Form tambah materi ke modul tertentu.
     * GET /modul/{moduleId}/materi/create
     */
    public function create(int $moduleId): View
    {
        $module = Module::findOrFail($moduleId);
        return view('materi.create', compact('module'));
    }

    /**
     * Simpan materi baru.
     * POST /modul/{moduleId}/materi
     */
    public function store(Request $request, int $moduleId): RedirectResponse
    {
        $data = $request->validate([
            'judul'   => 'required|string|max:255',
            'konten'  => 'required|string',
            'urutan'  => 'required|integer|min:1',
        ]);

        $data['module_id'] = $moduleId;
        $data['aktif'] = true;

        Material::create($data);

        return redirect("/modul/{$moduleId}")->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Form edit materi.
     * GET /materi/{id}/edit
     */
    public function edit(int $id): View
    {
        $materi = Material::findOrFail($id);
        return view('materi.edit', compact('materi'));
    }

    /**
     * Update materi.
     * PUT /materi/{id}
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'judul'   => 'required|string|max:255',
            'konten'  => 'required|string',
            'urutan'  => 'required|integer|min:1',
            'aktif'   => 'boolean',
        ]);

        $materi = Material::findOrFail($id);
        $materi->update($data);

        return redirect("/modul/{$materi->module_id}")->with('success', 'Materi berhasil diupdate.');
    }

    /**
     * Hapus materi.
     * DELETE /materi/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $materi = Material::findOrFail($id);
        $moduleId = $materi->module_id;
        $materi->delete();

        return redirect("/modul/{$moduleId}")->with('success', 'Materi berhasil dihapus.');
    }
}