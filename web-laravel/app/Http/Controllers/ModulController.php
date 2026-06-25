<?php

namespace App\Http\Controllers;

use App\Models\ModulPdf;
use App\Models\Pertemuan;
use Illuminate\Http\Request;

class ModulController extends Controller
{
    /**
     * Upload modul PDF dari form web
     */
    public function store(Request $request, $pertemuanId)
    {
        Pertemuan::findOrFail($pertemuanId);

        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $path = $request->file('file')->store('modul', 'public');

        ModulPdf::create([
            'pertemuan_id' => $pertemuanId,
            'nama_file'    => $request->file('file')->getClientOriginalName(),
            'path'         => $path,
            'diupload_oleh'=> session('guru_id'),
        ]);

        return redirect()->route('pertemuan.show', ['id'=>$pertemuanId, 'tab'=>'modul'])
            ->with('success', 'Modul PDF berhasil diupload');
    }
}