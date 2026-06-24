<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Session;

/**
 * NetlabsApiClient — HTTP Client ke Flask Backend API
 * 
 * Semua data (pertemuan, topik, kuis, chat, dsb.) diambil dari Flask API,
 * bukan dari database langsung. Laravel hanya sebagai presentation layer.
 * 
 * [PENTING] Sebelum deploy:
 * - Ganti FLASK_API_URL di .env dengan URL production
 * - Untuk development lokal: FLASK_API_URL=http://localhost:8000
 */
class NetlabsApiClient
{
    private PendingRequest $client;
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('FLASK_API_URL', 'http://localhost:8000'), '/');
        $this->client = Http::baseUrl($this->baseUrl)
            ->timeout(10)
            ->acceptJson()
            ->asJson();

        // Attach JWT token jika sudah login
        $token = Session::get('access_token');
        if ($token) {
            $this->client->withToken($token);
        }
    }

    // ─── Auth ───────────────────────────────────────────────────────────

    /** POST /api/auth/login */
    public function login(string $nis, string $password): array
    {
        $res = $this->client->post('/api/auth/login', [
            'nis' => $nis,
            'password' => $password,
        ]);

        return $this->parse($res);
    }

    /** GET /api/auth/me */
    public function me(): array
    {
        $res = $this->client->get('/api/auth/me');
        return $this->parse($res);
    }

    /** POST /api/auth/logout */
    public function logout(): array
    {
        $res = $this->client->post('/api/auth/logout');
        return $this->parse($res);
    }

    // ─── Dashboard Guru ─────────────────────────────────────────────────

    /** GET /api/guru/dashboard */
    public function dashboard(): array
    {
        $res = $this->client->get('/api/guru/dashboard');
        return $this->parse($res);
    }

    /** GET /api/guru/siswa */
    public function daftarSiswa(int $page = 1, int $limit = 20): array
    {
        $res = $this->client->get('/api/guru/siswa', [
            'page' => $page,
            'limit' => $limit,
        ]);
        return $this->parse($res);
    }

    /** GET /api/guru/siswa/{id} */
    public function detailSiswa(string $id): array
    {
        $res = $this->client->get("/api/guru/siswa/{$id}");
        return $this->parse($res);
    }

    // ─── Pertemuan ─────────────────────────────────────────────────────

    /** GET /api/pertemuan/ */
    public function daftarPertemuan(): array
    {
        $res = $this->client->get('/api/pertemuan/');
        return $this->parse($res);
    }

    /** POST /api/pertemuan */
    public function buatPertemuan(array $data): array
    {
        $res = $this->client->post('/api/pertemuan', $data);
        return $this->parse($res);
    }

    /** PUT /api/pertemuan/{id} */
    public function updatePertemuan(string $id, array $data): array
    {
        $res = $this->client->put("/api/pertemuan/{$id}", $data);
        return $this->parse($res);
    }

    /** DELETE /api/pertemuan/{id} */
    public function hapusPertemuan(string $id): array
    {
        $res = $this->client->delete("/api/pertemuan/{$id}");
        return $this->parse($res);
    }

    // ─── Topik ──────────────────────────────────────────────────────────

    /** GET /api/topik/{pertemuan_id} */
    public function daftarTopik(string $pertemuanId): array
    {
        $res = $this->client->get("/api/topik/{$pertemuanId}");
        return $this->parse($res);
    }

    /** POST /api/topik */
    public function buatTopik(array $data): array
    {
        $res = $this->client->post('/api/topik', $data);
        return $this->parse($res);
    }

    /** PUT /api/topik/{id} */
    public function updateTopik(string $id, array $data): array
    {
        $res = $this->client->put("/api/topik/{$id}", $data);
        return $this->parse($res);
    }

    /** DELETE /api/topik/{id} */
    public function hapusTopik(string $id): array
    {
        $res = $this->client->delete("/api/topik/{$id}");
        return $this->parse($res);
    }

    // ─── Kuis ───────────────────────────────────────────────────────────

    /** GET /api/kuis/soal?pertemuan_id= */
    public function daftarSoal(string $pertemuanId): array
    {
        $res = $this->client->get('/api/kuis/soal', [
            'pertemuan_id' => $pertemuanId,
        ]);
        return $this->parse($res);
    }

    /** POST /api/kuis/soal */
    public function buatSoal(array $data): array
    {
        $res = $this->client->post('/api/kuis/soal', $data);
        return $this->parse($res);
    }

    /** DELETE /api/kuis/soal/{id} */
    public function hapusSoal(string $id): array
    {
        $res = $this->client->delete("/api/kuis/soal/{$id}");
        return $this->parse($res);
    }

    // ─── Modul PDF ─────────────────────────────────────────────────────

    /** GET /api/modul/{pertemuan_id} */
    public function daftarModul(string $pertemuanId): array
    {
        $res = $this->client->get("/api/modul/{$pertemuanId}");
        return $this->parse($res);
    }

    /** POST /api/modul/upload (multipart form) */
    public function uploadModul(string $pertemuanId, $file): array
    {
        $res = Http::baseUrl($this->baseUrl)
            ->timeout(60)
            ->withToken(Session::get('access_token'))
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post('/api/modul/upload', [
                'pertemuan_id' => $pertemuanId,
            ]);

        return $this->parse($res);
    }

    // ─── Nilai ──────────────────────────────────────────────────────────

    /** GET /api/nilai/rekap */
    public function rekapNilai(): array
    {
        $res = $this->client->get('/api/nilai/rekap');
        return $this->parse($res);
    }

    /** GET /api/guru/laporan/export */
    public function exportNilai(): mixed
    {
        $res = Http::baseUrl($this->baseUrl)
            ->timeout(30)
            ->withToken(Session::get('access_token'))
            ->accept('text/csv')
            ->get('/api/guru/laporan/export');

        if ($res->successful()) {
            return $res->body();
        }
        return null;
    }

    // ─── Chat History ───────────────────────────────────────────────────

    /** GET /api/chat/riwayat/{siswa_id} */
    public function riwayatChatSiswa(string $siswaId): array
    {
        $res = $this->client->get("/api/chat/riwayat/{$siswaId}");
        return $this->parse($res);
    }

    // ─── Helper ─────────────────────────────────────────────────────────

    private function parse($response): array
    {
        if ($response->successful()) {
            $body = $response->json();
            return [
                'success' => true,
                'data'    => $body['data'] ?? null,
                'message' => $body['message'] ?? 'OK',
                'total'   => $body['total'] ?? null,
                'page'    => $body['page'] ?? null,
            ];
        }

        $status = $response->status();
        $detail = $response->json('detail') ?? 'Terjadi kesalahan server';

        return [
            'success' => false,
            'message' => "[{$status}] {$detail}",
            'data'    => null,
        ];
    }
}