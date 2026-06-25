<?php

namespace App\Http\Controllers;

use App\Models\Pertemuan;
use App\Models\SoalKuis;
use Illuminate\Http\Request;

class KuisController extends Controller
{
    /** Form tambah soal */
    public function create($pertemuanId)
    {
        $pertemuan = Pertemuan::findOrFail($pertemuanId);
        return view('kuis.create', compact('pertemuan'));
    }

    /** Simpan soal baru */
    public function store(Request $request, $pertemuanId)
    {
        Pertemuan::findOrFail($pertemuanId);
        $request->validate([
            'pertanyaan' => 'required|string',
            'pilihan_a' => 'required|string|max:500',
            'pilihan_b' => 'required|string|max:500',
            'pilihan_c' => 'required|string|max:500',
            'pilihan_d' => 'required|string|max:500',
            'pilihan_e' => 'required|string|max:500',
            'kunci' => 'required|string|in:A,B,C,D,E',
        ]);
        SoalKuis::create([
            'pertemuan_id' => $pertemuanId,
            'pertanyaan' => $request->pertanyaan,
            'pilihan_a' => $request->pilihan_a,
            'pilihan_b' => $request->pilihan_b,
            'pilihan_c' => $request->pilihan_c,
            'pilihan_d' => $request->pilihan_d,
            'pilihan_e' => $request->pilihan_e,
            'kunci' => $request->kunci,
        ]);
        return redirect()->route('pertemuan.show', ['id'=>$pertemuanId, 'tab'=>'soal'])
            ->with('success', 'Soal berhasil ditambahkan');
    }

    /** Form edit soal */
    public function edit($id)
    {
        $soal = SoalKuis::findOrFail($id);
        return view('kuis.edit', compact('soal'));
    }

    /** Update soal */
    public function update(Request $request, $id)
    {
        $soal = SoalKuis::findOrFail($id);
        $request->validate([
            'pertanyaan' => 'required|string',
            'pilihan_a' => 'required|string|max:500',
            'pilihan_b' => 'required|string|max:500',
            'pilihan_c' => 'required|string|max:500',
            'pilihan_d' => 'required|string|max:500',
            'pilihan_e' => 'required|string|max:500',
            'kunci' => 'required|string|in:A,B,C,D,E',
        ]);
        $soal->update($request->only(['pertanyaan','pilihan_a','pilihan_b','pilihan_c','pilihan_d','pilihan_e','kunci']));
        return redirect()->route('pertemuan.show', ['id'=>$soal->pertemuan_id, 'tab'=>'soal'])
            ->with('success', 'Soal berhasil diperbarui');
    }

    /** Hapus soal */
    public function destroy($id)
    {
        $soal = SoalKuis::findOrFail($id);
        $pid = $soal->pertemuan_id;
        $soal->delete();
        return redirect()->route('pertemuan.show', ['id'=>$pid, 'tab'=>'soal'])
            ->with('success', 'Soal berhasil dihapus');
    }
}