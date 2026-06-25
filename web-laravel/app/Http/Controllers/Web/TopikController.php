<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Topik;
use App\Models\Pertemuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopikController extends Controller
{
    /**
     * Form tambah topik untuk pertemuan tertentu.
     * GET /pertemuan/{id}/topik/create
     */
    public function create($pertemuanId): View
    {
        $pertemuan = Pertemuan::findOrFail($pertemuanId);

        return view('topik.create', compact('pertemuan'));
    }

    /**
     * Simpan topik baru.
     * POST /pertemuan/{id}/topik
     */
    public function store(Request $request, $pertemuanId): RedirectResponse
    {
        $validated = $request->validate([
            'judul'    => 'required|string|max:255',
            'konten'   => 'nullable|string',
            'urutan'   => 'nullable|integer|min:1',
            'estimasi_menit' => 'nullable|integer|min:1',
        ]);

        Topik::create([
            'pertemuan_id'  => $pertemuanId,
            'judul'         => $validated['judul'],
            'konten'        => $validated['konten'] ?? null,
            'urutan'        => $validated['urutan'] ?? 0,
            'estimasi_menit'=> $validated['estimasi_menit'] ?? null,
        ]);

        return redirect('/pertemuan/' . $pertemuanId)->with('success', 'Topik berhasil ditambahkan.');
    }

    /**
     * Form edit topik.
     * GET /topik/{id}/edit
     */
    public function edit($id): View
    {
        $topik = Topik::findOrFail($id);

        return view('topik.edit', compact('topik'));
    }

    /**
     * Update topik.
     * PUT /topik/{id}
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $topik = Topik::findOrFail($id);

        $validated = $request->validate([
            'judul'    => 'required|string|max:255',
            'konten'   => 'nullable|string',
            'urutan'   => 'nullable|integer|min:1',
            'estimasi_menit' => 'nullable|integer|min:1',
        ]);

        $topik->update($validated);

        return redirect('/pertemuan/' . $topik->pertemuan_id)->with('success', 'Topik berhasil diperbarui.');
    }

    /**
     * Hapus topik.
     * DELETE /topik/{id}
     */
    public function destroy($id): RedirectResponse
    {
        $topik = Topik::findOrFail($id);
        $pertemuanId = $topik->pertemuan_id;
        $topik->delete();

        return redirect('/pertemuan/' . $pertemuanId)->with('success', 'Topik berhasil dihapus.');
    }
}