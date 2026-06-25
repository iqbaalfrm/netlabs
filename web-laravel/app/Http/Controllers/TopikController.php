<?php

namespace App\Http\Controllers;

use App\Models\Pertemuan;
use App\Models\Topik;
use Illuminate\Http\Request;

class TopikController extends Controller
{
    /** Form tambah topik */
    public function create($pertemuanId)
    {
        $pertemuan = Pertemuan::findOrFail($pertemuanId);
        return view('topik.create', compact('pertemuan'));
    }

    /** Simpan topik baru */
    public function store(Request $request, $pertemuanId)
    {
        Pertemuan::findOrFail($pertemuanId);
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
            'nomor_urut' => 'required|integer|min:1',
        ]);
        Topik::create([
            'pertemuan_id' => $pertemuanId,
            'judul' => $request->judul,
            'isi' => $request->isi,
            'nomor_urut' => $request->nomor_urut,
        ]);
        return redirect()->route('pertemuan.show', $pertemuanId)->with('success', 'Topik ditambahkan');
    }

    /** Form edit topik */
    public function edit($id)
    {
        $topik = Topik::findOrFail($id);
        return view('topik.edit', compact('topik'));
    }

    /** Update topik */
    public function update(Request $request, $id)
    {
        $topik = Topik::findOrFail($id);
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
            'nomor_urut' => 'required|integer|min:1',
        ]);
        $topik->update($request->only(['judul', 'isi', 'nomor_urut']));
        return redirect()->route('pertemuan.show', $topik->pertemuan_id)->with('success', 'Topik diperbarui');
    }

    /** Hapus topik */
    public function destroy($id)
    {
        $topik = Topik::findOrFail($id);
        $pid = $topik->pertemuan_id;
        $topik->delete();
        return redirect()->route('pertemuan.show', $pid)->with('success', 'Topik dihapus');
    }
}