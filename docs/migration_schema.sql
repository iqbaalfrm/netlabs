-- ============================================================
-- MIGRATION: Schema utama Netlabs (Siswa, Guru, Konten, AI)
-- Jalankan di Supabase SQL Editor
-- ============================================================

-- 1. Tabel siswa
CREATE TABLE IF NOT EXISTS siswa (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  nis TEXT UNIQUE NOT NULL,
  nama TEXT NOT NULL,
  kelas TEXT NOT NULL,
  password TEXT NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 2. Tabel guru
CREATE TABLE IF NOT EXISTS guru (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  id_guru TEXT UNIQUE NOT NULL,
  nama TEXT NOT NULL,
  password TEXT NOT NULL,
  role TEXT DEFAULT 'guru',
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 3. Tabel pertemuan
CREATE TABLE IF NOT EXISTS pertemuan (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  nomor INT NOT NULL,
  judul TEXT NOT NULL,
  deskripsi TEXT,
  tujuan TEXT,
  semester INT DEFAULT 1,
  warna TEXT DEFAULT '#2D6A4F',
  terkunci BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 4. Tabel topik materi
CREATE TABLE IF NOT EXISTS topik (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE CASCADE,
  judul TEXT NOT NULL,
  konten TEXT NOT NULL,
  urutan INT DEFAULT 1
);

-- 5. Progress siswa per topik
CREATE TABLE IF NOT EXISTS progress_topik (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES siswa(id),
  topik_id UUID REFERENCES topik(id),
  selesai BOOLEAN DEFAULT FALSE,
  updated_at TIMESTAMPTZ DEFAULT NOW(),
  UNIQUE(siswa_id, topik_id)
);

-- 6. Soal kuis pilihan ganda
CREATE TABLE IF NOT EXISTS soal_kuis (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE CASCADE,
  pertanyaan TEXT NOT NULL,
  pilihan_a TEXT NOT NULL,
  pilihan_b TEXT NOT NULL,
  pilihan_c TEXT NOT NULL,
  pilihan_d TEXT NOT NULL,
  jawaban_benar TEXT NOT NULL,
  penjelasan TEXT
);

-- 7. Hasil kuis siswa
CREATE TABLE IF NOT EXISTS hasil_kuis (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES siswa(id),
  pertemuan_id UUID REFERENCES pertemuan(id),
  nilai FLOAT NOT NULL,
  jumlah_benar INT NOT NULL,
  total_soal INT NOT NULL,
  waktu_selesai TIMESTAMPTZ DEFAULT NOW()
);

-- 8. Chat history RAG
CREATE TABLE IF NOT EXISTS chat_history (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES siswa(id),
  pertemuan_id UUID REFERENCES pertemuan(id),
  pertanyaan TEXT NOT NULL,
  jawaban TEXT NOT NULL,
  sumber TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 9. Modul PDF yang diupload guru
CREATE TABLE IF NOT EXISTS modul_pdf (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id),
  nama_file TEXT NOT NULL,
  status TEXT DEFAULT 'pending',
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Index untuk performa
CREATE INDEX IF NOT EXISTS idx_siswa_nis ON siswa(nis);
CREATE INDEX IF NOT EXISTS idx_guru_id ON guru(id_guru);
CREATE INDEX IF NOT EXISTS idx_topik_pertemuan ON topik(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_soal_pertemuan ON soal_kuis(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_hasil_siswa ON hasil_kuis(siswa_id);
CREATE INDEX IF NOT EXISTS idx_hasil_pertemuan ON hasil_kuis(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_progress_siswa ON progress_topik(siswa_id);
CREATE INDEX IF NOT EXISTS idx_chat_siswa ON chat_history(siswa_id);

-- Seed: guru demo
INSERT INTO guru (id_guru, nama, password) VALUES
  ('GURU001', 'Pak Ahmad', '$2b$12$LJ3m4ys3GgkRqFpVBkFvQuHUM0VqCqGCfFvNBHVjgHQzRQFRwI1Wu')
ON CONFLICT (id_guru) DO NOTHING;
-- password: guru123 (bcrypt hash)

-- Seed: siswa demo
INSERT INTO siswa (nis, nama, kelas, password) VALUES
  ('2122100045', 'Ahmad Fauzi', 'XI TKJ 1', '$2b$12$LJ3m4ys3GgkRqFpVBkFvQuHUM0VqCqGCfFvNBHVjgHQzRQFRwI1Wu'),
  ('2122100046', 'Siti Nurhaliza', 'XI TKJ 1', '$2b$12$LJ3m4ys3GgkRqFpVBkFvQuHUM0VqCqGCfFvNBHVjgHQzRQFRwI1Wu'),
  ('2122100047', 'Budi Santoso', 'XI TKJ 2', '$2b$12$LJ3m4ys3GgkRqFpVBkFvQuHUM0VqCqGCfFvNBHVjgHQzRQFRwI1Wu')
ON CONFLICT (nis) DO NOTHING;
-- password: 123456 (bcrypt hash)