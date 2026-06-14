-- ============================================================
-- MIGRATION: Tambah kolom dan tabel yang kurang
-- Jalankan di Supabase SQL Editor setelah database_schema.sql
-- ============================================================

-- 1. Tambah kolom security di tabel users
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_first_login BOOLEAN DEFAULT TRUE;
ALTER TABLE users ADD COLUMN IF NOT EXISTS failed_login_attempts INT DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS locked_until TIMESTAMP DEFAULT NULL;

-- 2. Tabel kelas
CREATE TABLE IF NOT EXISTS kelas (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  nama_kelas VARCHAR(50) UNIQUE NOT NULL,
  created_at TIMESTAMP DEFAULT NOW()
);

-- 3. Tambah topik_id di modul_pdf (agar modul bisa terikat ke topik)
ALTER TABLE modul_pdf ADD COLUMN IF NOT EXISTS topik_id UUID REFERENCES topik(id) ON DELETE SET NULL;
ALTER TABLE modul_pdf ADD COLUMN IF NOT EXISTS judul VARCHAR(255);

-- 4. Tambah topik_id di soal_kuis (agar soal bisa terikat ke topik)
ALTER TABLE soal_kuis ADD COLUMN IF NOT EXISTS topik_id UUID REFERENCES topik(id) ON DELETE SET NULL;

-- 5. Tabel token_blacklist (untuk logout/invalidasi token)
CREATE TABLE IF NOT EXISTS token_blacklist (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  token_jti VARCHAR(255) NOT NULL,
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  expired_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP DEFAULT NOW()
);

-- 6. Index untuk performa query
CREATE INDEX IF NOT EXISTS idx_users_nis ON users(nis);
CREATE INDEX IF NOT EXISTS idx_users_role_kelas ON users(role, kelas);
CREATE INDEX IF NOT EXISTS idx_topik_pertemuan ON topik(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_soal_pertemuan ON soal_kuis(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_soal_topik ON soal_kuis(topik_id);
CREATE INDEX IF NOT EXISTS idx_modul_pertemuan ON modul_pdf(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_modul_topik ON modul_pdf(topik_id);
CREATE INDEX IF NOT EXISTS idx_hasil_siswa ON hasil_kuis(siswa_id);
CREATE INDEX IF NOT EXISTS idx_hasil_pertemuan ON hasil_kuis(pertemuan_id);
CREATE INDEX IF NOT EXISTS idx_progress_siswa ON progress_topik(siswa_id);
CREATE INDEX IF NOT EXISTS idx_chat_siswa ON chat_history(siswa_id);
CREATE INDEX IF NOT EXISTS idx_token_blacklist_jti ON token_blacklist(token_jti);

-- 7. Seed kelas awal
INSERT INTO kelas (nama_kelas) VALUES
  ('XI TKJ 1'),
  ('XI TKJ 2'),
  ('XII TKJ 1'),
  ('XII TKJ 2')
ON CONFLICT (nama_kelas) DO NOTHING;

-- 8. Update is_first_login = false untuk akun demo yang sudah ada
UPDATE users SET is_first_login = FALSE WHERE nis IN ('GURU001', '2122100045');
