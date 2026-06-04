# Panduan Deploy Netlabs

## 1. Setup Supabase

### Buat Project
1. Buka https://app.supabase.com → New Project
2. Isi nama: `netlabs`, pilih region: Southeast Asia (Singapore)
3. Catat: Project URL, anon key, service_role key

### Jalankan Schema
1. Masuk ke SQL Editor di Supabase Dashboard
2. Copy isi file `docs/database_schema.sql`
3. Klik Run — semua tabel + seed data akan dibuat

### Akun Demo
| Role | NIS | Password |
|------|-----|----------|
| Guru | GURU001 | guru123 |
| Siswa | 2122100045 | siswa123 |

---

## 2. Setup Backend

### Lokal (Development)
```bash
cd backend
python -m venv venv
venv\Scripts\activate       # Windows
# source venv/bin/activate  # Linux/Mac
pip install -r requirements.txt
cp .env.example .env
```

Isi file `.env`:
```
SUPABASE_URL=https://xxx.supabase.co
SUPABASE_KEY=your_anon_key
SUPABASE_SERVICE_KEY=your_service_key
ANTHROPIC_API_KEY=your_claude_api_key
JWT_SECRET=netlabs-jwt-secret-ganti-ini
CHROMA_PATH=./chroma_db
```

```bash
uvicorn main:app --reload
# Buka: http://localhost:8000/docs
```

### Install RAG Dependencies (Opsional)
```bash
pip install chromadb sentence-transformers pymupdf
```
Setelah ini, fitur upload PDF dan AI berbasis modul aktif.

### Deploy ke Railway (Gratis Tier)
1. Push backend ke GitHub
2. Buka https://railway.app → New Project → Deploy from GitHub
3. Tambahkan Environment Variables (sama dengan `.env`)
4. Railway otomatis detect `uvicorn main:app`

### Deploy ke Render
1. Push ke GitHub
2. Buka https://render.com → New Web Service
3. Build Command: `pip install -r requirements.txt`
4. Start Command: `uvicorn main:app --host 0.0.0.0 --port $PORT`

---

## 3. Setup Web Admin

### Lokal
```bash
cd web-admin
npm install
npm run dev
# Buka: http://localhost:5173
```

### Environment Variables (buat `.env`)
```
VITE_API_URL=http://localhost:8000
```
Untuk production, ganti dengan URL backend yang sudah deploy.

### Deploy ke Vercel
```bash
npm install -g vercel
cd web-admin
vercel
```
Atau push ke GitHub → import di https://vercel.com

### Deploy ke Netlify
```bash
npm run build
# Upload folder dist/ ke Netlify
```

---

## 4. Build APK Mobile

### Konfigurasi
Di file `mobile/lib/app/services/api_service.dart`:
```dart
// Ganti dengan URL backend production
static const String baseUrl = 'https://your-backend.railway.app';
```

### Build APK
```bash
cd mobile
flutter build apk --release
# APK ada di: build/app/outputs/flutter-apk/app-release.apk
```

### Build untuk Debug (Testing)
```bash
flutter build apk --debug
```

---

## 5. CORS Production

Update `backend/main.py` untuk production:
```python
app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "https://your-web-admin.vercel.app",
        "https://your-web-admin.netlify.app",
    ],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)
```

---

## 6. Upload Modul PDF

Setelah backend berjalan:
1. Login ke web admin sebagai guru
2. Buka halaman Pertemuan → klik pertemuan
3. Upload PDF modul
4. Backend otomatis index ke ChromaDB
5. AI Tutor siap menjawab berdasarkan modul

---

## 7. Checklist Sebelum Demo

- [ ] Supabase project aktif dan data seed sudah masuk
- [ ] Backend bisa diakses dari URL public
- [ ] Web admin bisa login dengan GURU001/guru123
- [ ] Mobile bisa login dengan 2122100045/siswa123
- [ ] Upload minimal 1 PDF modul untuk pertemuan 1
- [ ] Test chat AI → harus menjawab berdasarkan modul
- [ ] Test kuis → nilai tersimpan di database
