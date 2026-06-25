<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pertemuan;
use App\Models\SoalKuis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KuisController extends Controller
{
    /**
     * Form tambah soal kuis untuk pertemuan tertentu.
     * GET /pertemuan/{id}/kuis/create
     */
    public function create($pertemuanId): View
    {
        $pertemuan = Pertemuan::findOrFail($pertemuanId);

        return view('kuis.create', compact('pertemuan'));
    }

    /**
     * Simpan soal kuis baru.
     * POST /pertemuan/{id}/kuis
     */
    public function store(Request $request, $pertemuanId): RedirectResponse
    {
        $validated = $request->validate([
            'pertanyaan'   => 'required|string',
            'pilihan_a'    => 'required|string|max:500',
            'pilihan_b'    => 'required|string|max:500',
            'pilihan_c'    => 'required|string|max:500',
            'pilihan_d'    => 'required|string|max:500',
            'kunci_jawaban'=> 'required|in:A,B,C,D',
        ]);

        SoalKuis::create([
            'pertemuan_id'  => $pertemuanId,
            'pertanyaan'    => $validated['pertanyaan'],
            'pilihan_a'     => $validated['pilihan_a'],
            'pilihan_b'     => $validated['pilihan_b'],
            'pilihan_c'     => $validated['pilihan_c'],
            'pilihan_d'     => $validated['pilihan_d'],
            'kunci_jawaban' => $validated['kunci_jawaban'],
        ]);

        return redirect('/pertemuan/' . $pertemuanId)->with('success', 'Soal kuis berhasil ditambahkan.');
    }

    /**
     * Form edit soal kuis.
     * GET /kuis/{id}/edit
     */
    public function edit($id): View
    {
        $soal = SoalKuis::findOrFail($id);

        return view('kuis.edit', compact('soal'));
    }

    /**
     * Update soal kuis.
     * PUT /kuis/{id}
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $soal = SoalKuis::findOrFail($id);

        $validated = $request->validate([
            'pertanyaan'   => 'required|string',
            'pilihan_a'    => 'required|string|max:500',
            'pilihan_b'    => 'required|string|max:500',
            'pilihan_c'    => 'required|string|max:500',
            'pilihan_d'    => 'required|string|max:500',
            'kunci_jawaban'=> 'required|in:A,B,C,D',
        ]);

        $soal->update($validated);

        return redirect('/pertemuan/' . $soal->pertemuan_id)->with('success', 'Soal kuis berhasil diperbarui.');
    }

    /**
     * Hapus soal kuis.
     * DELETE /kuis/{id}
     */
    public function destroy($id): RedirectResponse
    {
        $soal = SoalKuis::findOrFail($id);
        $pertemuanId = $soal->pertemuan_id;
        $soal->delete();

        return redirect('/pertemuan/' . $pertemuanId)->with('success', 'Soal kuis berhasil dihapus.');
    }
}