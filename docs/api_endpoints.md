# API Endpoints — Netlabs Backend

Base URL: `http://localhost:8000`
Dokumentasi Swagger: `http://localhost:8000/docs`

## Auth (`/api/auth`)

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| POST | `/api/auth/login` | - | Login siswa/guru |
| GET | `/api/auth/me` | Token | Profil user login |

## Pertemuan (`/api/pertemuan`)

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| GET | `/api/pertemuan` | Token | Semua pertemuan + progress |
| GET | `/api/pertemuan/{id}` | Token | Detail + daftar topik |
| POST | `/api/pertemuan` | Guru | Buat pertemuan baru |
| PUT | `/api/pertemuan/{id}` | Guru | Update pertemuan |
| DELETE | `/api/pertemuan/{id}` | Guru | Hapus pertemuan |

## Topik (`/api/topik`)

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| GET | `/api/topik/{pertemuan_id}` | Token | Topik per pertemuan |
| POST | `/api/topik` | Guru | Buat topik baru |
| PUT | `/api/topik/{id}` | Guru | Update topik |
| DELETE | `/api/topik/{id}` | Guru | Hapus topik |
| POST | `/api/topik/{id}/baca` | Siswa | Tandai sudah dibaca |

## AI Chat / RAG (`/api/chat`)

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| POST | `/api/chat/tanya` | Siswa | Tanya AI (RAG) |
| GET | `/api/chat/riwayat/{siswa_id}` | Token | Riwayat chat |
| GET | `/api/chat/riwayat/{siswa_id}/{pertemuan_id}` | Token | Chat per pertemuan |

### Request Body `/api/chat/tanya`
```json
{
  "pertanyaan": "Apa itu VLAN?",
  "pertemuan_id": "uuid-pertemuan-4",
  "riwayat_chat": [
    { "dari_siswa": true, "teks": "pertanyaan sebelumnya" },
    { "dari_siswa": false, "teks": "jawaban AI sebelumnya" }
  ]
}
```

## Kuis (`/api/kuis`)

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| GET | `/api/kuis/{pertemuan_id}` | Siswa | 5 soal random |
| POST | `/api/kuis/submit` | Siswa | Submit jawaban |
| GET | `/api/kuis/soal?pertemuan_id=` | Guru | Semua soal |
| POST | `/api/kuis/soal` | Guru | Buat soal baru |
| DELETE | `/api/kuis/soal/{id}` | Guru | Hapus soal |

## Nilai (`/api/nilai`)

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| GET | `/api/nilai/saya` | Siswa | Nilai saya |
| GET | `/api/nilai/siswa/{id}` | Guru | Nilai siswa tertentu |
| GET | `/api/nilai/rekap` | Guru | Rekap semua siswa |

## Guru Dashboard (`/api/guru`)

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| GET | `/api/guru/dashboard` | Guru | Statistik dashboard |
| GET | `/api/guru/siswa` | Guru | Daftar semua siswa |
| GET | `/api/guru/siswa/{id}` | Guru | Detail siswa |
| GET | `/api/guru/pertanyaan` | Guru | Pertanyaan populer |
| GET | `/api/guru/laporan/export` | Guru | Export data nilai |

## Format Response Standar

```json
{
  "success": true,
  "data": { ... },
  "message": "Pesan opsional"
}
```

## Error Response

```json
{
  "detail": "Pesan error"
}
```

Status codes:
- `200` — Berhasil
- `401` — Tidak terautentikasi
- `403` — Tidak punya akses
- `404` — Data tidak ditemukan
- `422` — Validasi gagal
