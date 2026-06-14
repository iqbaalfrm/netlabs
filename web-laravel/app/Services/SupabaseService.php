<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Service untuk komunikasi dengan Supabase REST API
 * Semua akses database lewat sini (tidak pakai PDO langsung)
 */
class SupabaseService
{
    private string $url;
    private string $anonKey;
    private string $serviceKey;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->anonKey = config('services.supabase.anon_key');
        $this->serviceKey = config('services.supabase.service_key');
    }

    /**
     * Login guru via Supabase REST API (pakai tabel users langsung)
     * Kita tidak pakai Supabase Auth, tapi cek langsung ke tabel users
     */
    public function loginGuru(string $nis, string $password): array
    {
        // Ambil data guru dari tabel users
        $response = Http::withHeaders([
            'apikey' => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/users', [
            'nis' => 'eq.' . $nis,
            'role' => 'eq.guru',
            'select' => '*',
            'limit' => 1,
        ]);

        if (!$response->successful()) {
            return ['success' => false, 'message' => 'Koneksi database gagal'];
        }

        $users = $response->json();

        if (empty($users)) {
            return ['success' => false, 'message' => 'NIS atau password salah'];
        }

        $user = $users[0];

        // Verifikasi password (bcrypt)
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'NIS atau password salah'];
        }

        return [
            'success' => true,
            'user' => [
                'id'     => $user['id'],
                'nis'    => $user['nis'],
                'nama'   => $user['nama'],
                'role'   => $user['role'],
                'kelas'  => $user['kelas'],
                'sekolah' => $user['sekolah'],
            ],
        ];
    }

    /**
     * Ambil semua data pertemuan + jumlah topik
     */
    public function getPertemuan(): array
    {
        $response = Http::withHeaders([
            'apikey' => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/pertemuan', [
            'select' => '*',
            'order'  => 'nomor_urut.asc',
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }

    /**
     * Ambil detail pertemuan berdasarkan ID
     */
    public function getPertemuanById(string $id): ?array
    {
        $response = Http::withHeaders([
            'apikey' => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/pertemuan', [
            'id'     => 'eq.' . $id,
            'select' => '*',
            'limit'  => 1,
        ]);

        if (!$response->successful()) return null;
        $data = $response->json();
        return $data[0] ?? null;
    }

    /**
     * Buat pertemuan baru
     */
    public function createPertemuan(array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->post($this->url . '/rest/v1/pertemuan', $data);

        if (!$response->successful()) {
            return ['success' => false, 'message' => 'Gagal membuat pertemuan'];
        }

        return ['success' => true, 'data' => $response->json()];
    }

    /**
     * Update pertemuan
     */
    public function updatePertemuan(string $id, array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->patch($this->url . '/rest/v1/pertemuan?id=eq.' . $id, $data);

        if (!$response->successful()) {
            return ['success' => false, 'message' => 'Gagal update pertemuan'];
        }

        return ['success' => true];
    }

    /**
     * Hapus pertemuan
     */
    public function deletePertemuan(string $id): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->delete($this->url . '/rest/v1/pertemuan?id=eq.' . $id);

        if (!$response->successful()) {
            return ['success' => false, 'message' => 'Gagal hapus pertemuan'];
        }

        return ['success' => true];
    }

    /**
     * Ambil topik berdasarkan pertemuan ID
     */
    public function getTopikByPertemuan(string $pertemuanId): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/topik', [
            'pertemuan_id' => 'eq.' . $pertemuanId,
            'select'       => '*',
            'order'        => 'nomor_urut.asc',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Buat topik baru
     */
    public function createTopik(array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->post($this->url . '/rest/v1/topik', $data);

        if (!$response->successful()) {
            return ['success' => false, 'message' => 'Gagal membuat topik'];
        }

        return ['success' => true, 'data' => $response->json()];
    }

    /**
     * Update topik
     */
    public function updateTopik(string $id, array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->patch($this->url . '/rest/v1/topik?id=eq.' . $id, $data);

        return $response->successful()
            ? ['success' => true]
            : ['success' => false, 'message' => 'Gagal update topik'];
    }

    /**
     * Hapus topik
     */
    public function deleteTopik(string $id): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->delete($this->url . '/rest/v1/topik?id=eq.' . $id);

        return $response->successful()
            ? ['success' => true]
            : ['success' => false, 'message' => 'Gagal hapus topik'];
    }

    /**
     * Ambil semua siswa
     */
    public function getSiswa(): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/users', [
            'role'   => 'eq.siswa',
            'select' => 'id,nis,nama,kelas,sekolah,streak_hari,total_chat,created_at',
            'order'  => 'nama.asc',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Ambil detail siswa berdasarkan ID
     */
    public function getSiswaById(string $id): ?array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/users', [
            'id'     => 'eq.' . $id,
            'role'   => 'eq.siswa',
            'select' => 'id,nis,nama,kelas,sekolah,streak_hari,total_chat,created_at',
            'limit'  => 1,
        ]);

        if (!$response->successful()) return null;
        $data = $response->json();
        return $data[0] ?? null;
    }

    /**
     * Ambil hasil kuis semua siswa (rekap)
     */
    public function getRekap(): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/hasil_kuis', [
            'select' => '*',
            'order'  => 'waktu_kuis.desc',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Ambil hasil kuis per siswa
     */
    public function getHasilKuisBySiswa(string $siswaId): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/hasil_kuis', [
            'siswa_id' => 'eq.' . $siswaId,
            'select'   => '*',
            'order'    => 'waktu_kuis.desc',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Ambil soal kuis berdasarkan pertemuan
     */
    public function getSoalKuis(?string $pertemuanId = null): array
    {
        $params = [
            'select' => '*',
            'order'  => 'created_at.asc',
        ];

        if ($pertemuanId) {
            $params['pertemuan_id'] = 'eq.' . $pertemuanId;
        }

        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/soal_kuis', $params);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Buat soal kuis baru
     */
    public function createSoal(array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->post($this->url . '/rest/v1/soal_kuis', $data);

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'message' => 'Gagal membuat soal'];
    }

    /**
     * Hapus soal kuis
     */
    public function deleteSoal(string $id): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->delete($this->url . '/rest/v1/soal_kuis?id=eq.' . $id);

        return $response->successful()
            ? ['success' => true]
            : ['success' => false, 'message' => 'Gagal hapus soal'];
    }

    /**
     * Statistik untuk dashboard guru
     */
    public function getDashboardStats(): array
    {
        // Ambil semua data sekaligus lebih simpel
        $siswa = $this->getSiswa();
        $pertemuan = $this->getPertemuan();
        $rekap = $this->getRekap();

        $rataRata = !empty($rekap)
            ? round(array_sum(array_column($rekap, 'nilai')) / count($rekap), 1)
            : 0;

        return [
            'total_siswa'     => count($siswa),
            'total_pertemuan' => count($pertemuan),
            'total_kuis'      => count($rekap),
            'rata_rata_nilai' => $rataRata,
        ];
    }

    /**
     * Ambil modul PDF berdasarkan pertemuan
     */
    public function getModulPdf(string $pertemuanId): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/modul_pdf', [
            'pertemuan_id' => 'eq.' . $pertemuanId,
            'select'       => '*',
            'order'        => 'created_at.desc',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Hapus modul PDF
     */
    public function deleteModul(string $id): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->delete($this->url . '/rest/v1/modul_pdf?id=eq.' . $id);

        return $response->successful()
            ? ['success' => true]
            : ['success' => false, 'message' => 'Gagal hapus modul'];
    }

    /**
     * Update modul PDF
     */
    public function updateModul(string $id, array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->patch($this->url . '/rest/v1/modul_pdf?id=eq.' . $id, $data);

        return $response->successful()
            ? ['success' => true]
            : ['success' => false, 'message' => 'Gagal update modul'];
    }

    /**
     * Ambil semua modul PDF
     */
    public function getAllModul(): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/modul_pdf', [
            'select' => '*',
            'order'  => 'created_at.desc',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Buat modul PDF baru
     */
    public function createModul(array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->post($this->url . '/rest/v1/modul_pdf', $data);

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'message' => 'Gagal membuat modul'];
    }

    // ===== KELAS =====

    public function getKelas(): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/kelas', [
            'select' => '*',
            'order'  => 'nama_kelas.asc',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    public function getKelasById(string $id): ?array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/kelas', [
            'id'     => 'eq.' . $id,
            'select' => '*',
            'limit'  => 1,
        ]);

        if (!$response->successful()) return null;
        $data = $response->json();
        return $data[0] ?? null;
    }

    public function createKelas(array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->post($this->url . '/rest/v1/kelas', $data);

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'message' => 'Gagal membuat kelas'];
    }

    public function updateKelas(string $id, array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->patch($this->url . '/rest/v1/kelas?id=eq.' . $id, $data);

        return $response->successful()
            ? ['success' => true]
            : ['success' => false, 'message' => 'Gagal update kelas'];
    }

    public function deleteKelas(string $id): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->delete($this->url . '/rest/v1/kelas?id=eq.' . $id);

        return $response->successful()
            ? ['success' => true]
            : ['success' => false, 'message' => 'Gagal hapus kelas'];
    }

    /**
     * Ambil siswa berdasarkan kelas
     */
    public function getSiswaByKelas(string $kelas): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/users', [
            'role'   => 'eq.siswa',
            'kelas'  => 'eq.' . $kelas,
            'select' => 'id,nis,nama,kelas,sekolah,streak_hari,total_chat,created_at',
            'order'  => 'nama.asc',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Buat siswa baru
     */
    public function createSiswa(array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->post($this->url . '/rest/v1/users', $data);

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'message' => 'Gagal membuat siswa'];
    }

    /**
     * Update user (siswa atau guru)
     */
    public function updateUser(string $id, array $data): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Prefer'       => 'return=representation',
        ])->patch($this->url . '/rest/v1/users?id=eq.' . $id, $data);

        return $response->successful()
            ? ['success' => true]
            : ['success' => false, 'message' => 'Gagal update data'];
    }

    /**
     * Hapus siswa
     */
    public function deleteSiswa(string $id): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->delete($this->url . '/rest/v1/users?id=eq.' . $id);

        return $response->successful()
            ? ['success' => true]
            : ['success' => false, 'message' => 'Gagal hapus siswa'];
    }

    /**
     * Ambil topik berdasarkan ID
     */
    public function getTopikById(string $id): ?array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/topik', [
            'id'     => 'eq.' . $id,
            'select' => '*',
            'limit'  => 1,
        ]);

        if (!$response->successful()) return null;
        $data = $response->json();
        return $data[0] ?? null;
    }

    /**
     * Ambil modul PDF berdasarkan topik
     */
    public function getModulByTopik(string $topikId): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/modul_pdf', [
            'topik_id' => 'eq.' . $topikId,
            'select'   => '*',
            'order'    => 'created_at.desc',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Ambil soal kuis berdasarkan topik
     */
    public function getSoalByTopik(string $topikId): array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->get($this->url . '/rest/v1/soal_kuis', [
            'topik_id' => 'eq.' . $topikId,
            'select'   => '*',
            'order'    => 'created_at.asc',
        ]);

        return $response->successful() ? $response->json() : [];
    }
}
