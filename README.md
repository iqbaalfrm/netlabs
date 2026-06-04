# Netlabs — ITS + LMS Praktikum Jaringan Komputer

Platform **Intelligent Tutoring System** berbasis RAG + **LMS** untuk praktikum Jaringan Komputer Dasar siswa SMK.

## Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 📚 LMS | Materi per pertemuan dengan tracking progress |
| 🤖 AI Tutor (RAG) | Tanya jawab AI berdasarkan modul PDF yang diunggah guru |
| 📝 Kuis | Evaluasi pilihan ganda per pertemuan + rekap nilai |
| 👨‍🏫 Dashboard Guru | Pantau aktivitas siswa, kelola materi, upload modul |

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Mobile (Siswa) | Flutter + GetX |
| Backend API | FastAPI (Python) |
| Web Admin (Guru) | React + Vite + TailwindCSS |
| Database | Supabase (PostgreSQL) |
| AI Engine | Claude API (Anthropic) |
| Vector Store | ChromaDB (lokal) |
| Auth | JWT (python-jose + bcrypt) |

## Struktur Project

```
netlabs/
├── mobile/          # Flutter app siswa
├── backend/         # FastAPI Python
├── web-admin/       # React + Vite admin guru
├── docs/
│   ├── database_schema.sql   # Schema + seed akun demo
│   ├── seed_data.sql          # Topik dan soal kuis
│   ├── DEPLOY_GUIDE.md        # Panduan deploy
│   └── BLUEPRINT.md           # Spesifikasi lengkap
└── PRODUCTION_READY_PLAN.md   # Status pengembangan
```

## Quick Start

### 1. Backend

```bash
cd backend
python -m venv venv && venv\Scripts\activate
pip install -r requirements.txt
cp .env.example .env    # isi kredensial Supabase & Anthropic
uvicorn main:app --reload
```
Docs: http://localhost:8000/docs

### 2. Web Admin

```bash
cd web-admin
npm install
npm run dev
```
URL: http://localhost:5173 — Login: GURU001 / guru123

### 3. Mobile

```bash
cd mobile
flutter pub get
flutter run
```
Login: 2122100045 / siswa123

## Setup Database (Supabase)

1. Buat project di https://app.supabase.com
2. SQL Editor → jalankan `docs/database_schema.sql`
3. SQL Editor → jalankan `docs/seed_data.sql`
4. Isi `.env` dengan URL dan key dari project

## Akun Demo

| Role | NIS/ID | Password |
|------|--------|----------|
| Guru | GURU001 | guru123 |
| Siswa | 2122100045 | siswa123 |

## Alur RAG (AI Tutor)

```
Guru upload PDF → backend parse → potong chunks →
buat embedding → simpan ChromaDB

Siswa tanya → embedding pertanyaan → cari chunk relevan →
susun prompt + konteks → Claude API → jawaban
```

## Catatan

- Semua screen punya **dummy fallback** saat backend offline
- RAG dependency (chromadb, sentence-transformers) opsional — backend tetap jalan tanpa ini
- Lihat `PRODUCTION_READY_PLAN.md` untuk status development
