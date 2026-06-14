<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class ModulController extends Controller
{
    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        $pertemuan = $this->supabase->getPertemuan();
        $modul = $this->supabase->getAllModul();

        return view('modul.index', compact('pertemuan', 'modul'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'pertemuan_id' => 'required|string',
            'file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $file = $request->file('file');
        $namaFile = $file->getClientOriginalName();

        $data = [
            'pertemuan_id' => $request->pertemuan_id,
            'nama_file' => $namaFile,
            'judul' => $request->judul,
            'sudah_diindex' => false,
        ];

        if ($request->filled('topik_id')) {
            $data['topik_id'] = $request->topik_id;
        }

        $result = $this->supabase->createModul($data);

        if ($request->filled('topik_id')) {
            return redirect()->route('topik.show', [$request->pertemuan_id, $request->topik_id])
                ->with($result['success'] ? 'success' : 'error',
                       $result['success'] ? 'Modul berhasil diupload' : $result['message']);
        }

        return redirect()->route('modul.index')
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Modul berhasil diupload' : $result['message']);
    }

    public function destroy(string $id)
    {
        $result = $this->supabase->deleteModul($id);

        return redirect()->route('modul.index')
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Modul berhasil dihapus' : $result['message']);
    }

    public function triggerRag(string $id)
    {
        $backendUrl = config('services.backend.url', 'http://localhost:8000');

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(60)
                ->post($backendUrl . '/api/modul/' . $id . '/index');

            if ($response->successful()) {
                $this->supabase->updateModul($id, ['sudah_diindex' => true]);
                return redirect()->route('modul.index')
                    ->with('success', 'Modul berhasil diproses RAG');
            }

            return redirect()->route('modul.index')
                ->with('error', 'Gagal memproses RAG: ' . $response->body());
        } catch (\Exception $e) {
            return redirect()->route('modul.index')
                ->with('error', 'Backend tidak tersedia: ' . $e->getMessage());
        }
    }
}
