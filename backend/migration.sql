-- ============================================================
-- MIGRATION: Schema Netlabs (sinkron dengan backend FastAPI)
-- Jalankan di Supabase SQL Editor
-- ============================================================

-- 1. Tabel users (siswa + guru jadi satu, dibedakan role)
CREATE TABLE IF NOT EXISTS users (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  nis TEXT UNIQUE NOT NULL,
  nama TEXT NOT NULL,
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL DEFAULT 'siswa',       -- 'siswa' atau 'guru'
  kelas TEXT,                               -- hanya untuk siswa
  sekolah TEXT,                             -- opsional
  streak_hari INT DEFAULT 0,
  total_chat INT DEFAULT 0,
  is_first_login BOOLEAN DEFAULT TRUE,
  failed_login_attempts INT DEFAULT 0,
  locked_until TIMESTAMPTZ,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 2. Token blacklist (untuk logout)
CREATE TABLE IF NOT EXISTS token_blacklist (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  token_jti TEXT UNIQUE NOT NULL,
  user_id UUID REFERENCES users(id),
  expired_at TIMESTAMPTZ NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 3. Pertemuan praktikum
CREATE TABLE IF NOT EXISTS pertemuan (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  judul TEXT NOT NULL,
  deskripsi TEXT,
  nomor_urut INT NOT NULL,
  warna_hex TEXT DEFAULT '#2D7DD2',
  dibuat_oleh UUID REFERENCES users(id),
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 4. Topik materi per pertemuan
CREATE TABLE IF NOT EXISTS topik (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE CASCADE,
  judul TEXT NOT NULL,
  isi_materi TEXT NOT NULL,
  nomor_urut INT DEFAULT 1,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 5. Progress baca siswa per topik
CREATE TABLE IF NOT EXISTS progress_topik (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id),
  topik_id UUID REFERENCES topik(id) ON DELETE CASCADE,
  sudah_dibaca BOOLEAN DEFAULT FALSE,
  waktu_dibaca TIMESTAMPTZ,
  UNIQUE(siswa_id, topik_id)
);

-- 6. Bank soal kuis pilihan ganda
CREATE TABLE IF NOT EXISTS soal_kuis (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE CASCADE,
  pertanyaan TEXT NOT NULL,
  pilihan_a TEXT NOT NULL,
  pilihan_b TEXT NOT NULL,
  pilihan_c TEXT NOT NULL,
  pilihan_d TEXT NOT NULL,
  jawaban_benar TEXT NOT NULL,              -- 'A', 'B', 'C', atau 'D'
  penjelasan TEXT DEFAULT '',
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 7. Hasil kuis siswa
CREATE TABLE IF NOT EXISTS hasil_kuis (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id),
  pertemuan_id UUID REFERENCES pertemuan(id),
  jumlah_benar INT NOT NULL,
  total_soal INT NOT NULL,
  nilai FLOAT NOT NULL,
  waktu_kuis TIMESTAMPTZ DEFAULT NOW()
);

-- 8. Chat history RAG (dari_siswa=true → pertanyaan, false → jawaban AI)
CREATE TABLE IF NOT EXISTS chat_history (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id),
  pertemuan_id UUID REFERENCES pertemuan(id),
  dari_siswa BOOLEAN NOT NULL,              -- true = pertanyaan, false = jawaban AI
  teks TEXT NOT NULL,
  label_sumber TEXT,                        -- sumber modul (jika dari AI)
  waktu TIMESTAMPTZ DEFAULT NOW()
);

-- 9. Modul PDF yang diupload guru
CREATE TABLE IF NOT EXISTS modul_pdf (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id),
  nama_file TEXT NOT NULL,
  sudah_diindex BOOLEAN DEFAULT FALSE,
  diunggah_oleh UUID REFERENCES users(id),
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- INDEX
-- ============================================================
CREATE INDEX IF NOT EXISTS idx_users_nis ON users(nis);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_token_blacklist_jti ON token_blacklist(token_jti);
CREATE INDEX IF NOT EXISTS idx_topik_pertemuan ON topik(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_soal_pertemuan ON soal_kuis(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_hasil_siswa ON hasil_kuis(siswa_id);
CREATE INDEX IF NOT EXISTS idx_hasil_pertemuan ON hasil_kuis(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_chat_siswa ON chat_history(siswa_id);
CREATE INDEX IF NOT EXISTS idx_chat_pertemuan ON chat_history(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_chat_dari_siswa ON chat_history(dari_siswa);
CREATE INDEX IF NOT EXISTS idx_progress_siswa ON progress_topik(siswa_id);
CREATE INDEX IF NOT EXISTS idx_modul_pertemuan ON modul_pdf(pertemuan_id);

-- ============================================================
-- SEED DATA DEMO
-- ============================================================

-- Guru demo (password: guru123)
INSERT INTO users (nis, nama, password_hash, role) VALUES
  ('GURU001', 'Pak Ahmad', '$2b$12$LJ3m4ys3GgkRqFpVBkFvQuHUM0VqCqGCfFvNBHVjgHQzRQFRwI1Wu', 'guru')
ON CONFLICT (nis) DO NOTHING;

-- Siswa demo (password: siswa123)
INSERT INTO users (nis, nama, kelas, password_hash, role, is_first_login) VALUES
  ('2122100045', 'Ahmad Fauzi', 'XI TKJ 1', '$2b$12$LJ3m4ys3GgkRqFpVBkFvQuHUM0VqCqGCfFvNBHVjgHQzRQFRwI1Wu', 'siswa', FALSE),
  ('2122100046', 'Siti Nurhaliza', 'XI TKJ 1', '$2b$12$LJ3m4ys3GgkRqFpVBkFvQuHUM0VqCqGCfFvNBHVjgHQzRQFRwI1Wu', 'siswa', FALSE),
  ('2122100047', 'Budi Santoso', 'XI TKJ 2', '$2b$12$LJ3m4ys3GgkRqFpVBkFvQuHUM0VqCqGCfFvNBHVjgHQzRQFRwI1Wu', 'siswa', FALSE)
ON CONFLICT (nis) DO NOTHING;