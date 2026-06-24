<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class SiswaController extends Controller
{
    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index(Request $request)
    {
        $kelas = $this->supabase->getKelas();
        $filterKelas = $request->query('kelas');

        $siswa = $filterKelas
            ? $this->supabase->getSiswaByKelas($filterKelas)
            : $this->supabase->getSiswa();

        return view('siswa.index', compact('siswa', 'kelas', 'filterKelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nis' => 'required|string|max:20',
            'kelas' => 'required|string|max:20',
            'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::min(6)->letters()->numbers()],
        ]);

        $result = $this->supabase->createSiswa([
            'nama' => $request->nama,
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'role' => 'siswa',
            'password_hash' => password_hash($request->password, PASSWORD_BCRYPT),
            'sekolah' => $request->sekolah ?? '',
        ]);

        return redirect()->route('siswa.index')
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Siswa berhasil ditambahkan' : $result['message']);
    }

    public function show(string $id)
    {
        $siswa = $this->supabase->getSiswaById($id);

        if (!$siswa) {
            return redirect()->route('siswa.index')->with('error', 'Siswa tidak ditemukan');
        }

        $hasilKuis = $this->supabase->getHasilKuisBySiswa($id);
        $pertemuan = $this->supabase->getPertemuan();

        $pertemuanMap = [];
        foreach ($pertemuan as $p) {
            $pertemuanMap[$p['id']] = $p;
        }

        $rataRata = !empty($hasilKuis)
            ? round(array_sum(array_column($hasilKuis, 'nilai')) / count($hasilKuis), 1)
            : 0;

        return view('siswa.show', compact('siswa', 'hasilKuis', 'pertemuanMap', 'rataRata'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string|max:20',
        ]);

        $data = [
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'sekolah' => $request->sekolah ?? '',
        ];

        if ($request->filled('password_baru')) {
            $data['password_hash'] = password_hash($request->password_baru, PASSWORD_BCRYPT);
        }

        $result = $this->supabase->updateUser($id, $data);

        return redirect()->route('siswa.show', $id)
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Data siswa berhasil diperbarui' : $result['message']);
    }

    public function destroy(string $id)
    {
        $result = $this->supabase->deleteSiswa($id);

        return redirect()->route('siswa.index')
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Siswa berhasil dihapus' : $result['message']);
    }

    public function resetPassword(string $id)
    {
        $siswa = $this->supabase->getSiswaById($id);

        if (!$siswa) {
            return redirect()->route('siswa.index')->with('error', 'Siswa tidak ditemukan');
        }

        // Reset password ke default (yaitu NIS siswa tersebut)
        $defaultPassword = $siswa['nis'];
        $result = $this->supabase->updateUser($id, [
            'password_hash' => password_hash($defaultPassword, PASSWORD_BCRYPT),
        ]);

        return redirect()->route('siswa.show', $id)
            ->with($result['success'] ? 'success' : 'error',
                   $result['success'] ? 'Password siswa berhasil di-reset menjadi default (NIS)' : $result['message']);
    }
}
