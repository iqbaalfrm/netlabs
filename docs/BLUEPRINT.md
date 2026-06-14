# BLUEPRINT APLIKASI NETLABS
# Dokumen Acuan Lengkap untuk Pengembangan

---

## 1. TENTANG APLIKASI

**Netlabs** adalah platform Intelligent Tutoring System (ITS) + LMS untuk praktikum
Jaringan Komputer Dasar siswa SMK. Menggunakan teknologi RAG (Retrieval-Augmented
Generation) agar AI Tutor menjawab pertanyaan siswa berdasarkan modul PDF yang
diunggah guru.

**Target pengguna:**
- Siswa SMK (mobile app Flutter)
- Guru (web admin React)

**Fitur utama:**
1. LMS — modul per pertemuan, setiap pertemuan ada topik-topik
2. ITS AI Chat — tanya jawab AI kontekstual per pertemuan (RAG)
3. Kuis — evaluasi per pertemuan (5 soal pilihan ganda)
4. Progres & Nilai — rekap kemandirian belajar siswa
5. Dashboard Guru — pantau aktivitas, kelola konten, export laporan

---

## 2. TECH STACK

| Layer | Teknologi | Keterangan |
|-------|-----------|------------|
| Mobile (Siswa) | Flutter + GetX | State management simpel |
| Backend API | FastAPI (Python) | Async, auto docs Swagger |
| Web Admin (Guru) | React + Vite + TailwindCSS | SPA dashboard |
| Database | Supabase (PostgreSQL) | Hosted, free tier |
| AI Engine | Claude API (Anthropic) | Model: claude-3-haiku |
| Vector Store | ChromaDB | Embedding PDF lokal |
| File Storage | Supabase Storage | Upload PDF modul |
| Auth | JWT (python-jose) | Token-based |

---

## 3. STRUKTUR MONOREPO

```
netlabs/
├── README.md
├── .env.example
├── .gitignore
│
├── mobile/                    # Flutter App (Siswa)
│   ├── pubspec.yaml
│   └── lib/
│       ├── main.dart
│       ├── app/
│       │   ├── routes/        # app_routes.dart, app_pages.dart
│       │   ├── theme/         # app_colors.dart, app_typography.dart, app_theme.dart
│       │   ├── constants/     # dummy_data.dart
│       │   └── widgets/       # custom_button, custom_text_field, pertemuan_card, stat_chip
│       ├── data/models/       # pertemuan_model, topik_model, chat_model, soal_model, badge_model, user_model
│       └── modules/
│           ├── splash/        # binding, controller, view
│           ├── auth/          # binding, controller, login_view
│           ├── home/          # binding, controller, view (bottom nav 5 tab)
│           ├── pertemuan/     # binding, controller, list_view, detail_view
│           ├── topik/         # binding, controller, detail_view
│           ├── chat/          # binding, controller, view, widgets/
│           ├── kuis/          # binding, controller, view, hasil_view
│           ├── nilai/         # binding, controller, view
│           ├── progres/       # binding, controller, view
│           └── profil/        # binding, controller, view
│
├── backend/                   # FastAPI (Python)
│   ├── main.py
│   ├── requirements.txt
│   ├── .env
│   ├── app/
│   │   ├── __init__.py
│   │   ├── config.py
│   │   ├── database.py
│   │   ├── schemas/           # user.py, pertemuan.py, chat.py, kuis.py
│   │   ├── routers/           # auth, pertemuan, topik, chat, kuis, nilai, guru
│   │   ├── services/          # rag_service, pdf_service, ai_service, auth_service
│   │   └── middleware/        # auth_middleware.py
│   └── chroma_db/             # Vector store lokal (auto-generated)
│
├── web-admin/                 # React + Vite (Guru)
│   ├── package.json
│   ├── vite.config.js
│   ├── tailwind.config.js
│   ├── index.html
│   └── src/
│       ├── main.jsx
│       ├── App.jsx
│       ├── index.css
│       ├── api/               # axiosInstance, authApi, pertemuanApi, siswaApi, laporanApi
│       ├── pages/             # Login, Dashboard, Pertemuan, DetailPertemuan, DataSiswa, DetailSiswa, Pertanyaan, Laporan
│       ├── components/        # Sidebar, Navbar, StatCard, ModalForm, UploadPDF
│       └── context/           # AuthContext.jsx
│
└── docs/
    ├── BLUEPRINT.md           # File ini
    ├── database_schema.sql    # SQL untuk Supabase
    └── api_endpoints.md       # Dokumentasi API
```

---

## 4. DATABASE SCHEMA (SUPABASE)

### Tabel: users
```sql
CREATE TABLE users (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  nis VARCHAR(20) UNIQUE,
  nama VARCHAR(100) NOT NULL,
  role VARCHAR(10) DEFAULT 'siswa',  -- 'siswa' / 'guru'
  kelas VARCHAR(20),
  sekolah VARCHAR(100),
  password_hash VARCHAR(255),
  streak_hari INT DEFAULT 0,
  total_chat INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT NOW()
);
```

### Tabel: pertemuan
```sql
CREATE TABLE pertemuan (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  nomor_urut INT NOT NULL,
  judul VARCHAR(200) NOT NULL,
  deskripsi TEXT,
  warna_hex VARCHAR(10),
  dibuat_oleh UUID REFERENCES users(id),
  created_at TIMESTAMP DEFAULT NOW()
);
```

### Tabel: topik
```sql
CREATE TABLE topik (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE CASCADE,
  judul VARCHAR(200) NOT NULL,
  isi_materi TEXT NOT NULL,
  nomor_urut INT NOT NULL,
  created_at TIMESTAMP DEFAULT NOW()
);
```

### Tabel: progress_topik
```sql
CREATE TABLE progress_topik (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id) ON DELETE CASCADE,
  topik_id UUID REFERENCES topik(id) ON DELETE CASCADE,
  sudah_dibaca BOOLEAN DEFAULT FALSE,
  waktu_dibaca TIMESTAMP,
  UNIQUE(siswa_id, topik_id)
);
```

### Tabel: modul_pdf
```sql
CREATE TABLE modul_pdf (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE CASCADE,
  nama_file VARCHAR(200),
  url_file TEXT,
  sudah_diindex BOOLEAN DEFAULT FALSE,
  diunggah_oleh UUID REFERENCES users(id),
  created_at TIMESTAMP DEFAULT NOW()
);
```

### Tabel: chat_history
```sql
CREATE TABLE chat_history (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id) ON DELETE CASCADE,
  pertemuan_id UUID REFERENCES pertemuan(id),
  dari_siswa BOOLEAN NOT NULL,
  teks TEXT NOT NULL,
  label_sumber VARCHAR(200),
  waktu TIMESTAMP DEFAULT NOW()
);
```

### Tabel: soal_kuis
```sql
CREATE TABLE soal_kuis (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE CASCADE,
  pertanyaan TEXT NOT NULL,
  pilihan_a TEXT NOT NULL,
  pilihan_b TEXT NOT NULL,
  pilihan_c TEXT NOT NULL,
  pilihan_d TEXT NOT NULL,
  jawaban_benar CHAR(1) NOT NULL,
  penjelasan TEXT,
  created_at TIMESTAMP DEFAULT NOW()
);
```

### Tabel: hasil_kuis
```sql
CREATE TABLE hasil_kuis (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id) ON DELETE CASCADE,
  pertemuan_id UUID REFERENCES pertemuan(id),
  jumlah_benar INT NOT NULL,
  total_soal INT NOT NULL,
  nilai FLOAT NOT NULL,
  waktu_kuis TIMESTAMP DEFAULT NOW()
);
```

### Tabel: badge
```sql
CREATE TABLE badge (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id) ON DELETE CASCADE,
  nama_badge VARCHAR(100),
  ikon VARCHAR(10),
  diraih_pada TIMESTAMP DEFAULT NOW()
);
```

---

## 5. API ENDPOINTS

### AUTH (/api/auth)
| Method | Endpoint | Auth | Body/Response |
|--------|----------|------|---------------|
| POST | /api/auth/login | - | Body: {nis, password} → {token, user} |
| GET | /api/auth/me | Token | → data user login |

### PERTEMUAN (/api/pertemuan)
| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|------------|
| GET | / | Token | Semua pertemuan + progress |
| GET | /{id} | Token | Detail + daftar topik |
| POST | / | Guru | Buat pertemuan baru |
| PUT | /{id} | Guru | Update pertemuan |
| DELETE | /{id} | Guru | Hapus pertemuan |

### TOPIK (/api/topik)
| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|------------|
| GET | /{pertemuan_id} | Token | List topik |
| POST | / | Guru | Buat topik |
| PUT | /{id} | Guru | Update topik |
| DELETE | /{id} | Guru | Hapus topik |
| POST | /{id}/baca | Siswa | Tandai sudah dibaca |

### MODUL PDF (/api/modul)
| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|------------|
| POST | /upload | Guru | Upload + index PDF (RAG) |
| GET | /{pertemuan_id} | Token | List PDF per pertemuan |
| DELETE | /{id} | Guru | Hapus PDF |

### AI CHAT / RAG (/api/chat)
| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|------------|
| POST | /tanya | Siswa | Tanya AI (RAG pipeline) |
| GET | /riwayat/{siswa_id} | Token | Semua riwayat chat |
| GET | /riwayat/{siswa_id}/{pertemuan_id} | Token | Chat per pertemuan |

### KUIS (/api/kuis)
| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|------------|
| GET | /{pertemuan_id} | Siswa | 5 soal random |
| POST | /submit | Siswa | Submit jawaban → nilai |
| GET | /soal?pertemuan_id= | Guru | Semua soal |
| POST | /soal | Guru | Buat soal |
| DELETE | /soal/{id} | Guru | Hapus soal |

### NILAI (/api/nilai)
| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|------------|
| GET | /saya | Siswa | Nilai saya |
| GET | /siswa/{id} | Guru | Nilai siswa tertentu |
| GET | /rekap | Guru | Rekap semua siswa |

### GURU DASHBOARD (/api/guru)
| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|------------|
| GET | /dashboard | Guru | Statistik |
| GET | /siswa | Guru | Daftar siswa |
| GET | /siswa/{id} | Guru | Detail siswa |
| GET | /pertanyaan | Guru | Pertanyaan populer |
| GET | /laporan/export | Guru | Export CSV |

---

## 6. ALUR RAG (FITUR INTI)

```
INDEXING (saat guru upload PDF):
┌─────────┐    ┌──────────┐    ┌────────┐    ┌──────────┐
│ PDF     │───▶│ PyMuPDF  │───▶│ Chunks │───▶│ Embedding│───▶ ChromaDB
│ Upload  │    │ (parse)  │    │ (500w) │    │ (vector) │
└─────────┘    └──────────┘    └────────┘    └──────────┘

QUERY (saat siswa bertanya):
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌─────────┐
│Pertanyaan│───▶│ Embedding│───▶│ ChromaDB │───▶│ Top 3   │
│ Siswa    │    │ (vector) │    │ (search) │    │ Chunks  │
└──────────┘    └──────────┘    └──────────┘    └────┬────┘
                                                     │
┌──────────┐    ┌──────────┐                         │
│ Jawaban  │◀───│ Claude   │◀── System Prompt + Context + History
│ ke Siswa │    │ API      │
└──────────┘    └──────────┘
```

**System Prompt:**
```
Kamu adalah tutor AI praktikum Jaringan Komputer Dasar
untuk siswa SMK. Jawab pertanyaan HANYA berdasarkan konteks modul berikut.
Jika tidak ada di modul, katakan tidak tahu.
Gunakan bahasa yang mudah dipahami siswa SMK.
```

---

## 7. MOBILE APP (FLUTTER) — SCREEN LIST

| # | Screen | Route | Deskripsi |
|---|--------|-------|-----------|
| 1 | Splash | /splash | Logo + shimmer, 2.5s → login |
| 2 | Login | /login | NIS + password, validasi |
| 3 | Beranda | /home (tab 0) | Hero, stats, pertemuan aktif |
| 4 | Daftar Pertemuan | /home (tab 1) | Filter + list 5 pertemuan |
| 5 | AI Chat | /home (tab 2) | Chat RAG kontekstual |
| 6 | Nilai | /home (tab 3) | Rekap nilai + chart |
| 7 | Profil | /home (tab 4) | Info akun + logout |
| 8 | Detail Pertemuan | /pertemuan/detail | Topik list + aksi |
| 9 | Detail Topik | /topik/detail | Isi materi + tandai baca |
| 10 | Kuis | /kuis | 5 soal + timer 30s |
| 11 | Hasil Kuis | /kuis/hasil | Nilai + rekomendasi AI |
| 12 | Progres | /progres | Progress ring + badge |

**Bottom Navigation:** Beranda / Pertemuan / AI Chat (FAB) / Nilai / Profil

---

## 8. WEB ADMIN (REACT) — HALAMAN

| # | Halaman | Route | Deskripsi |
|---|---------|-------|-----------|
| 1 | Login | /login | Form login guru |
| 2 | Dashboard | / | Statistik + aktivitas |
| 3 | Pertemuan | /pertemuan | CRUD pertemuan |
| 4 | Detail Pertemuan | /pertemuan/:id | Tab: Topik / PDF / Kuis |
| 5 | Data Siswa | /siswa | Tabel + search |
| 6 | Detail Siswa | /siswa/:id | Profil + riwayat |
| 7 | Pertanyaan | /pertanyaan | Pertanyaan populer |
| 8 | Laporan | /laporan | Export CSV |

**Layout:** Sidebar navy kiri + Navbar atas + Konten putih

---

## 9. DESIGN SYSTEM

### Warna
| Nama | Hex | Kegunaan |
|------|-----|----------|
| primary | #2D7DD2 | Tombol, link, aksen utama |
| navyDeep | #1A2B5F | Sidebar web, gradient splash |
| purple | #7B5EA7 | Gradient tombol, aksen |
| teal | #0F9B8E | Sukses, badge lulus |
| orange | #F4A261 | Warning, streak |
| gold | #F7C948 | Badge, bintang |
| error | #E05263 | Error, gagal |
| bgWhite | #FFFFFF | Background utama |
| bgLight | #F5F7FA | Background section |
| textPrimary | #1A1A2E | Teks utama |
| textSecondary | #6B7A99 | Teks sekunder |
| pastelBlue | #EEF4FF | Background kartu |
| pastelPurple | #F3EEFF | Background kartu |
| pastelTeal | #EDFAF6 | Background kartu |
| pastelOrange | #FFF4ED | Background kartu |

### Typography
- Font: Plus Jakarta Sans (Google Fonts)
- Judul halaman: 22px Bold
- Judul section: 18px W700
- Body: 14px W400
- Body kecil: 12px W400

### Spacing & Radius
- Radius kartu: 16-20px
- Padding horizontal: 20px
- Jarak antar section: 24-28px

---

## 10. GAYA KODING

### Wajib:
- Komentar Bahasa Indonesia di bagian penting
- Nama variabel deskriptif (daftar_siswa, bukan lst)
- Fungsi pendek, 1 tujuan jelas
- Error handling simpel
- Tidak over-engineering

### Python (FastAPI):
- async/await semua endpoint
- Pydantic schemas validasi
- Response konsisten: `{ success, data, message }`

### Flutter (GetX):
- GetxController + .obs + Obx()
- Get.toNamed() untuk navigasi
- Binding per module

### React:
- Functional components + hooks
- useState, useEffect simpel
- Axios + React Query
- Context untuk auth

---

## 11. KONTEN PRAKTIKUM (5 PERTEMUAN)

| # | Judul | Warna | Topik |
|---|-------|-------|-------|
| 1 | Pengenalan Jaringan Komputer | #2D7DD2 | Pengertian, Jenis (LAN/MAN/WAN), Topologi, Perangkat Keras |
| 2 | Pengalamatan IP (IP Addressing) | #0F9B8E | Pengertian IP, Kelas IP, IP Public/Private, Subnetting |
| 3 | Konfigurasi IP di Windows | #7B5EA7 | Setting IP Manual, Verifikasi CMD, Troubleshooting |
| 4 | Implementasi VLAN | #F4A261 | Pengertian VLAN, Config Switch, Inter-VLAN, Verifikasi |
| 5 | Static Routing | #E05263 | Konsep Routing, Config Static Route, Verifikasi Table |

---

## 12. CARA SETUP & JALANKAN

### Backend (FastAPI)
```bash
cd backend
python -m venv venv
venv\Scripts\activate          # Windows
pip install -r requirements.txt
cp .env.example .env           # Isi kredensial
uvicorn main:app --reload
# → http://localhost:8000/docs
```

### Web Admin (React)
```bash
cd web-admin
npm install
cp .env.example .env           # VITE_API_URL=http://localhost:8000
npm run dev
# → http://localhost:5173
```

### Mobile (Flutter)
```bash
cd mobile
flutter pub get
flutter run
```

---

## 13. DEPENDENCIES

### Backend (requirements.txt)
```
fastapi==0.109.0
uvicorn==0.27.0
python-dotenv==1.0.0
supabase==2.3.0
anthropic==0.18.0
chromadb==0.4.22
sentence-transformers==2.3.1
pymupdf==1.23.8
python-multipart==0.0.9
pydantic==2.5.0
python-jose==3.3.0
passlib==1.7.4
```

### Mobile (pubspec.yaml)
```yaml
dependencies:
  get: ^4.6.6
  google_fonts: ^6.1.0
  flutter_animate: ^4.5.0
  dio: ^5.4.0
  fl_chart: ^0.67.0
  shimmer: ^3.0.0
  percent_indicator: ^4.2.3
  get_storage: ^2.1.1
```

### Web Admin (package.json)
```json
{
  "react": "^18.2.0",
  "react-router-dom": "^6.22.0",
  "axios": "^1.6.0",
  "@tanstack/react-query": "^5.0.0",
  "recharts": "^2.10.0",
  "tailwindcss": "^3.4.0",
  "lucide-react": "^0.330.0",
  "react-dropzone": "^14.2.3",
  "react-hot-toast": "^2.4.1"
}
```

---

## 14. SEED DATA (untuk testing)

### Guru
- NIS: GURU001, Nama: Pak Ahmad, Password: guru123

### Siswa
- NIS: 2122100045, Nama: Muhammad Rafli, Kelas: XI TKJ 2, Password: siswa123

### Data dummy:
- 5 pertemuan dengan masing-masing 3-4 topik
- 5 soal kuis per pertemuan (total 25 soal)
- 5 badge (3 terbuka, 2 terkunci)
- Riwayat chat contoh
- Aktivitas mingguan (grid 8x7)
