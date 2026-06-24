# CLAUDE.md — Netlabs

> ITS + LMS Praktikum Jaringan Komputer Dasar SMK
> FastAPI (BE) · Laravel Blade (Web) · Flutter GetX (Mobile) · Supabase · Claude + ChromaDB (RAG)
> **Last update:** 2026-06-24

---

## 1. Arsitektur

```
Flutter Mobile ──▶ FastAPI :8000 ──▶ Supabase PostgreSQL
Laravel Blade  ──▶ FastAPI :8000 ──▶ ChromaDB + Claude API
```

**Aturan kritis:**
- Mobile hanya akses API via `api_service.dart` — **jangan pernah** koneksi langsung ke Supabase
- Laravel hanya akses data via `NetlabsApiClient.php` ke FastAPI — **jangan** query Supabase langsung
- Perubahan schema di Supabase **wajib** dicek dampaknya ke backend (`app/`) **dan** Laravel (`NetlabsApiClient.php`)

---

## 2. Struktur Project (real)

```
netlabs/
├── backend/                    # FastAPI — satu-satunya yg koneksi Supabase
│   ├── main.py                 # CORS, rate limiter 100/min, security headers
│   ├── migration.sql           # ⚠️ Schema acuan UTAMA (bukan docs/)
│   ├── requirements.txt        # fastapi, supabase, chromadb, sentence-transformers, pymupdf, anthropic
│   ├── app/
│   │   ├── config.py           # Semua env vars
│   │   ├── database.py         # Supabase client
│   │   ├── routers/            # auth, pertemuan, topik, chat, kuis, nilai, guru, modul
│   │   ├── services/           # rag_service, pdf_service
│   │   └── middleware/         # auth.py (JWT), rate_limit.py
│   └── chroma_db/              # Vector store lokal
│
├── web-laravel/                # ⚠️ Laravel Blade, BUKAN React (README/blueprint outdated)
│   ├── routes/web.php          # Semua route prefix /guru/, middleware auth.guru
│   ├── app/Services/NetlabsApiClient.php  # HTTP client ke FastAPI (FLASK_API_URL env)
│   ├── app/Http/Controllers/   # Auth, Dashboard, Pertemuan, Siswa, Kelas, Modul, Kuis, Nilai, Pengaturan
│   └── public/assets/css/      # Star Admin 2 template
│
├── mobile/                     # Flutter ^3.8.1
│   ├── lib/app/services/api_service.dart  # Semua panggilan API
│   ├── lib/app/constants/dummy_data.dart  # Fallback saat offline
│   ├── lib/app/routes/         # app_routes.dart + app_pages.dart (GetX named routes)
│   ├── lib/models/             # pertemuan, topik, chat, kuis, siswa
│   └── lib/modules/            # splash, auth, home, pertemuan, chat, kuis, profil
│
├── docs/                       # ⚠️ PRD_Netlabs.md, BLUEPRINT.md masih sebut React — abaikan
├── deploy/                     # Script deployment ke VPS 161.35.55.122
└── supabase/                   # Kosong
```

---

## 3. Database — 9 Tabel (acuan: `backend/migration.sql`)

| Tabel | Kolom kunci | Note |
|---|---|---|
| `users` | id, nis, nama, password_hash, role (siswa/guru), kelas, is_first_login, failed_login_attempts, locked_until | |
| `token_blacklist` | token_jti, user_id, expired_at | Invalidasi JWT logout |
| `pertemuan` | id, nomor_urut, judul, deskripsi, warna_hex | 5 pertemuan |
| `topik` | id, pertemuan_id, judul, isi_materi, nomor_urut | |
| `progress_topik` | siswa_id, topik_id, sudah_dibaca | UNIQUE constraint |
| `soal_kuis` | id, pertemuan_id, pertanyaan, pilihan_a-d, jawaban_benar, penjelasan | |
| `hasil_kuis` | siswa_id, pertemuan_id, jumlah_benar, total_soal, nilai | |
| `chat_history` | siswa_id, pertemuan_id, dari_siswa, teks, label_sumber | |
| `modul_pdf` | pertemuan_id, nama_file, sudah_diindex | **Tidak ada** url_file — file di filesystem |

**Seed:** GURU001/guru123, 2122100045/siswa123, 2122100046/siswa123, 2122100047/siswa123

---

## 4. API Endpoints

Prefix `/api/` — semua lewat Nginx reverse proxy ke FastAPI :8000

| Router | Endpoint utama |
|---|---|
| `/api/auth` | POST login, GET me, POST logout, PUT profil, POST ganti-password |
| `/api/pertemuan` | CRUD, GET / (dgn progress) |
| `/api/topik` | CRUD, GET /{pertemuan_id}, GET /detail/{id}, POST /{id}/baca |
| `/api/chat` | POST /tanya (RAG), GET /riwayat/{siswa_id}, GET /riwayat/{siswa_id}/{pertemuan_id} |
| `/api/kuis` | GET /{pertemuan_id} (5 soal random), POST /submit, CRUD /soal |
| `/api/nilai` | GET /saya, GET /siswa/{id}, GET /rekap |
| `/api/guru` | GET /dashboard, GET /siswa, GET /siswa/{id}, GET /pertanyaan, GET /laporan/export |
| `/api/modul` | POST /upload, GET /{pertemuan_id}, DELETE /{id} |

Response format: `{ success, data, message }`

---

## 5. AI RAG Pipeline

**Indexing:** PDF → PyMuPDF → chunk ~400 kata → Sentence Transformers (`all-MiniLM-L6-v2`) → ChromaDB

**Query:** Pertanyaan → embedding → ChromaDB search (top 3, filter pertemuan_id) → system prompt + 3 chunk + 5 riwayat terakhir → Claude (`claude-3-haiku`) → jawaban + label sumber → simpan ke chat_history

> ⚠️ Di VPS (1 core/2GB): ChromaDB + sentence-transformers **tidak diinstall** — terlalu berat. AI Tutor jalan via Anthropic API langsung tanpa RAG lokal.

---

## 6. Design System

**Warna:** primary #2D7DD2 · navy #1A2B5F · purple #7B5EA7 · teal #0F9B8E · orange #F4A261 · error #E05263 · bg #F5F7FA · text #1A1A2E / #6B7A99

**Font:** Plus Jakarta Sans (Google Fonts)

**Mobile stacks:** GetX (GetxController + .obs + Obx), Dio (JWT interceptor), GetStorage + FlutterSecureStorage

**Laravel stacks:** Blade + Star Admin 2 + session auth (middleware `auth.guru`) + `NetlabsApiClient` HTTP client

---

## 7. Env Vars

**Backend (`backend/.env`):**
```
SUPABASE_URL=          SUPABASE_KEY=          SUPABASE_SERVICE_KEY=
ANTHROPIC_API_KEY=     JWT_SECRET=(min 32 char)          CHROMA_PATH=./chroma_db
APP_ENV=development|production          CORS_ORIGINS=(comma-separated)
```

**Laravel (`web-laravel/.env`):**
```
FLASK_API_URL=http://localhost:8000    (nama misleading — ini URL FastAPI)
SUPABASE_URL=          SUPABASE_ANON_KEY=          SUPABASE_SERVICE_KEY=
BACKEND_URL=http://127.0.0.1:8000
```

**Mobile:** Base URL di `api_service.dart` → `http://10.0.2.2:8000` (emulator) / `http://161.35.55.122` (production)

---

## 8. Commands

```bash
# Backend
cd backend && venv/Scripts/activate && uvicorn main:app --reload    # → :8000/docs

# Laravel
cd web-laravel && php artisan serve                                  # → :8001
# Lint: ./vendor/bin/pint   Test: php artisan test

# Mobile
cd mobile && flutter pub get && flutter run
```

---

## 9. Deploy ke VPS

**Server:** 161.35.55.122 / 1 core / 2GB RAM + 2GB swap / Ubuntu

**Alur deploy:**
1. Upload: `scp -r netlabs deploy@161.35.55.122:/home/deploy/` atau `rsync` (exclude node_modules, vendor, venv, chroma_db, .env)
2. Backend: venv → `pip install -r requirements.txt gunicorn` → isi `.env` → `systemctl start netlabs-backend`
3. Laravel: `composer install --no-dev` → `npm install && npm run build` → isi `.env` → `php artisan key:generate` → set permission storage → `systemctl start netlabs-queue`
4. Nginx reverse proxy: `/api/*` → :8000, `/` → PHP-FPM

**Services:** `netlabs-backend` (1 Gunicorn worker) · `netlabs-queue` · `nginx` · `php8.2-fpm`

**Logs:** `journalctl -u netlabs-backend -f` · `tail -f /var/log/nginx/error.log` · `/var/log/netlabs-backend-error.log`

---

## 10. Konvensi & Checklist Commit

- **Bahasa:** Dokumentasi/UI/komentar = Indonesia. Identifier = campuran (domain Indonesia + camelCase/snake_case)
- **Commit checklist:** ikuti arsitektur §1 · cek dampak schema · mobile hanya api_service · validasi + auth · lint (`pint` / `flutter analyze`) · AI domain-restricted · dummy data mobile tetap OK

---

## 11. Catatan Kritis

1. **Schema acuan:** `backend/migration.sql` — abaikan `docs/database_schema.sql`
2. **Laravel, bukan React** — README.md dan BLUEPRINT.md masih salah
3. **FLASK_API_URL** = URL FastAPI backend (nama variabel misleading)
4. **modul_pdf** tidak lagi simpan `url_file` — file di filesystem
5. **routes.dart** di root mobile = deprecated → pakai `app/routes/`
6. **ChromaDB tidak jalan di VPS** (1 core/2GB) — AI Tutor fallback ke Anthropic API saja
7. **Rate limiter:** 100 req/menit global. Timeout chat: 40 detik