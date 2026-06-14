<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class PertemuanController extends Controller
{
    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Daftar semua pertemuan
     */
    public function index()
    {
        $pertemuan = $this->supabase->getPertemuan();
        return view('pertemuan.index', compact('pertemuan'));
    }

    /**
     * Detail pertemuan — tab: Topik, PDF, Soal Kuis
     */
    public function show(string $id)
    {
        $pertemuan = $this->supabase->getPertemuanById($id);

        if (!$pertemuan) {
            return redirect()->route('pertemuan.index')
                ->with('error', 'Pertemuan tidak ditemukan');
        }

        $topik    = $this->supabase->getTopikByPertemuan($id);
        $modul    = $this->supabase->getModulPdf($id);
        $soal     = $this->supabase->getSoalKuis($id);

        return view('pertemuan.show', compact('pertemuan', 'topik', 'modul', 'soal'));
    }

    /**
     * Simpan pertemuan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_urut' => 'required|integer|min:1',
            'judul'      => 'required|string|max:200',
            'deskripsi'  => 'nullable|string',
            'warna_hex'  => 'nullable|string|max:10',
        ]);

        $guru = session('guru');

        $result = $this->supabase->createPertemuan([
            'nomor_urut'  => (int) $request->nomor_urut,
            'judul'       => $request->judul,
            'deskripsi'   => $request->deskripsi ?? '',
            'warna_hex'   => $request->warna_hex ?? '#2D7DD2',
            'dibuat_oleh' => $guru['id'],
        ]);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('pertemuan.index')
            ->with('success', 'Pertemuan berhasil dibuat');
    }

    /**
     * Update pertemuan
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nomor_urut' => 'required|integer|min:1',
            'judul'      => 'required|string|max:200',
            'deskripsi'  => 'nullable|string',
            'warna_hex'  => 'nullable|string|max:10',
        ]);

        $result = $this->supabase->updatePertemuan($id, [
            'nomor_urut' => (int) $request->nomor_urut,
            'judul'      => $request->judul,
            'deskripsi'  => $request->deskripsi ?? '',
            'warna_hex'  => $request->warna_hex ?? '#2D7DD2',
        ]);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('pertemuan.show', $id)
            ->with('success', 'Pertemuan berhasil diperbarui');
    }

    /**
     * Hapus pertemuan
     */
    public function destroy(string $id)
    {
        $result = $this->supabase->deletePertemuan($id);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('pertemuan.index')
            ->with('success', 'Pertemuan berhasil dihapus');
    }

    // ===== TOPIK =====

    /**
     * Detail topik — modul PDF + soal kuis
     */
    public function showTopik(string $pertemuanId, string $topikId)
    {
        $pertemuan = $this->supabase->getPertemuanById($pertemuanId);
        if (!$pertemuan) {
            return redirect()->route('pertemuan.index')->with('error', 'Pertemuan tidak ditemukan');
        }

        $topik = $this->supabase->getTopikById($topikId);
        if (!$topik) {
            return redirect()->route('pertemuan.show', $pertemuanId)->with('error', 'Topik tidak ditemukan');
        }

        $modul = $this->supabase->getModulByTopik($topikId);
        $soal = $this->supabase->getSoalByTopik($topikId);

        return view('pertemuan.topik-show', compact('pertemuan', 'topik', 'modul', 'soal'));
    }

    /**
     * Simpan topik baru
     */
    public function storeTopik(Request $request, string $pertemuanId)
    {
        $request->validate([
            'judul'       => 'required|string|max:200',
            'isi_materi'  => 'required|string',
            'nomor_urut'  => 'required|integer|min:1',
        ]);

        $result = $this->supabase->createTopik([
            'pertemuan_id' => $pertemuanId,
            'judul'        => $request->judul,
            'isi_materi'   => $request->isi_materi,
            'nomor_urut'   => (int) $request->nomor_urut,
        ]);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('pertemuan.show', $pertemuanId)
            ->with('success', 'Topik berhasil ditambahkan')->withFragment('tab-topik');
    }

    /**
     * Update topik
     */
    public function updateTopik(Request $request, string $pertemuanId, string $topikId)
    {
        $request->validate([
            'judul'      => 'required|string|max:200',
            'isi_materi' => 'required|string',
            'nomor_urut' => 'required|integer|min:1',
        ]);

        $result = $this->supabase->updateTopik($topikId, [
            'judul'      => $request->judul,
            'isi_materi' => $request->isi_materi,
            'nomor_urut' => (int) $request->nomor_urut,
        ]);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('pertemuan.show', $pertemuanId)
            ->with('success', 'Topik berhasil diperbarui')->withFragment('tab-topik');
    }

    /**
     * Hapus topik
     */
    public function destroyTopik(string $pertemuanId, string $topikId)
    {
        $result = $this->supabase->deleteTopik($topikId);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('pertemuan.show', $pertemuanId)
            ->with('success', 'Topik berhasil dihapus')->withFragment('tab-topik');
    }

    // ===== SOAL KUIS =====

    /**
     * Simpan soal baru
     */
    public function storeSoal(Request $request, string $pertemuanId)
    {
        $request->validate([
            'pertanyaan'   => 'required|string',
            'pilihan_a'    => 'required|string',
            'pilihan_b'    => 'required|string',
            'pilihan_c'    => 'required|string',
            'pilihan_d'    => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D',
            'penjelasan'   => 'nullable|string',
        ]);

        $result = $this->supabase->createSoal([
            'pertemuan_id'  => $pertemuanId,
            'pertanyaan'    => $request->pertanyaan,
            'pilihan_a'     => $request->pilihan_a,
            'pilihan_b'     => $request->pilihan_b,
            'pilihan_c'     => $request->pilihan_c,
            'pilihan_d'     => $request->pilihan_d,
            'jawaban_benar' => $request->jawaban_benar,
            'penjelasan'    => $request->penjelasan ?? '',
        ]);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('pertemuan.show', $pertemuanId)
            ->with('success', 'Soal berhasil ditambahkan')->withFragment('tab-kuis');
    }

    /**
     * Upload PDF — kirim multipart ke Railway backend
     */
    public function uploadModul(Request $request, string $pertemuanId)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480', // max 20MB
        ], [
            'file.required' => 'Pilih file PDF terlebih dahulu',
            'file.mimes'    => 'Hanya file PDF yang diizinkan',
            'file.max'      => 'Ukuran file maksimal 20MB',
        ]);

        $token = session('guru_token');

        if (!$token) {
            return back()->with('error', 'Sesi habis, silakan login ulang');
        }

        try {
            // Kirim file ke Railway backend sebagai multipart
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->timeout(60)
                ->attach(
                    'file',
                    file_get_contents($request->file('file')->getRealPath()),
                    $request->file('file')->getClientOriginalName()
                )
                ->post(config('services.railway.url') . '/api/modul/upload', [
                    'pertemuan_id' => $pertemuanId,
                ]);

            if ($response->successful()) {
                return redirect()->route('pertemuan.show', $pertemuanId)
                    ->with('success', 'PDF berhasil diupload dan diindex untuk AI Tutor')
                    ->withFragment('tab-pdf');
            }

            $msg = $response->json('detail') ?? 'Upload gagal';
            return back()->with('error', $msg);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke server: ' . $e->getMessage());
        }
    }

    /**
     * Hapus modul PDF via Railway backend
     */
    public function destroyModul(string $pertemuanId, string $modulId)
    {
        $token = session('guru_token');

        try {
            \Illuminate\Support\Facades\Http::withToken($token)
                ->delete(config('services.railway.url') . '/api/modul/' . $modulId);
        } catch (\Exception $e) {
            // Lanjutkan walau gagal hapus di backend
        }

        return redirect()->route('pertemuan.show', $pertemuanId)
            ->with('success', 'Modul berhasil dihapus')
            ->withFragment('tab-pdf');
    }

    /**
     * Hapus soal
     */
    public function destroySoal(string $pertemuanId, string $soalId)
    {
        $result = $this->supabase->deleteSoal($soalId);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('pertemuan.show', $pertemuanId)
            ->with('success', 'Soal berhasil dihapus')->withFragment('tab-kuis');
    }
}
