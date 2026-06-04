-- ============================================================
-- DATABASE SCHEMA NETLABS
-- Jalankan di Supabase SQL Editor
-- Dashboard: https://app.supabase.com/project/<ref>/sql
-- ============================================================

-- Tabel users (siswa & guru)
CREATE TABLE IF NOT EXISTS users (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  nis VARCHAR(20) UNIQUE NOT NULL,
  nama VARCHAR(100) NOT NULL,
  role VARCHAR(10) DEFAULT 'siswa' CHECK (role IN ('siswa', 'guru')),
  kelas VARCHAR(20),
  sekolah VARCHAR(100),
  password_hash VARCHAR(255) NOT NULL,
  streak_hari INT DEFAULT 0,
  total_chat INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Tabel pertemuan
CREATE TABLE IF NOT EXISTS pertemuan (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  nomor_urut INT NOT NULL,
  judul VARCHAR(200) NOT NULL,
  deskripsi TEXT,
  warna_hex VARCHAR(10) DEFAULT '#2D7DD2',
  dibuat_oleh UUID REFERENCES users(id) ON DELETE SET NULL,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Tabel topik (isi materi per pertemuan)
CREATE TABLE IF NOT EXISTS topik (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE CASCADE,
  judul VARCHAR(200) NOT NULL,
  isi_materi TEXT NOT NULL,
  nomor_urut INT NOT NULL,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Tabel progress_topik (tracking siswa baca materi)
CREATE TABLE IF NOT EXISTS progress_topik (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id) ON DELETE CASCADE,
  topik_id UUID REFERENCES topik(id) ON DELETE CASCADE,
  sudah_dibaca BOOLEAN DEFAULT FALSE,
  waktu_dibaca TIMESTAMP,
  UNIQUE(siswa_id, topik_id)
);

-- Tabel modul_pdf (file PDF knowledge base untuk RAG)
CREATE TABLE IF NOT EXISTS modul_pdf (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE CASCADE,
  nama_file VARCHAR(200) NOT NULL,
  url_file TEXT,
  sudah_diindex BOOLEAN DEFAULT FALSE,
  diunggah_oleh UUID REFERENCES users(id) ON DELETE SET NULL,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Tabel chat_history (riwayat percakapan AI)
CREATE TABLE IF NOT EXISTS chat_history (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id) ON DELETE CASCADE,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE SET NULL,
  dari_siswa BOOLEAN NOT NULL,
  teks TEXT NOT NULL,
  label_sumber VARCHAR(200),
  waktu TIMESTAMP DEFAULT NOW()
);

-- Tabel soal_kuis (bank soal per pertemuan)
CREATE TABLE IF NOT EXISTS soal_kuis (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE CASCADE,
  pertanyaan TEXT NOT NULL,
  pilihan_a TEXT NOT NULL,
  pilihan_b TEXT NOT NULL,
  pilihan_c TEXT NOT NULL,
  pilihan_d TEXT NOT NULL,
  jawaban_benar CHAR(1) NOT NULL CHECK (jawaban_benar IN ('a','b','c','d')),
  penjelasan TEXT DEFAULT '',
  created_at TIMESTAMP DEFAULT NOW()
);

-- Tabel hasil_kuis (nilai kuis siswa)
CREATE TABLE IF NOT EXISTS hasil_kuis (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id) ON DELETE CASCADE,
  pertemuan_id UUID REFERENCES pertemuan(id) ON DELETE SET NULL,
  jumlah_benar INT NOT NULL,
  total_soal INT NOT NULL,
  nilai FLOAT NOT NULL,
  waktu_kuis TIMESTAMP DEFAULT NOW()
);

-- Tabel badge (pencapaian siswa)
CREATE TABLE IF NOT EXISTS badge (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  siswa_id UUID REFERENCES users(id) ON DELETE CASCADE,
  nama_badge VARCHAR(100),
  ikon VARCHAR(10),
  diraih_pada TIMESTAMP DEFAULT NOW()
);

-- ============================================================
-- SEED DATA — Akun demo untuk testing
-- guru123 dan siswa123 sudah di-hash dengan bcrypt
-- ============================================================

INSERT INTO users (nis, nama, role, sekolah, password_hash) VALUES
  ('GURU001', 'Pak Ahmad', 'guru', 'SMK Bhakti Praja Dukuhwaru',
   '$2b$12$LUxIszyML3aFqv.QLPQEnun.lppuE6VjettMw.Cwax1axMZdxhfB2')
ON CONFLICT (nis) DO NOTHING;

INSERT INTO users (nis, nama, role, kelas, sekolah, password_hash) VALUES
  ('2122100045', 'Muhammad Iqbal', 'siswa', 'XI TKJ 2',
   'SMK Bhakti Praja Dukuhwaru',
   '$2b$12$TfD80QX0bS.fuVJL6Ns.A.ggvMPQxpPkTzK6ZjZXYprZpt1HDBhhm')
ON CONFLICT (nis) DO NOTHING;

-- 5 pertemuan
INSERT INTO pertemuan (nomor_urut, judul, deskripsi, warna_hex) VALUES
  (1, 'Pengenalan Jaringan Komputer',
   'Memahami konsep dasar jaringan, jenis jaringan, dan topologi.',
   '#2D7DD2'),
  (2, 'Pengalamatan IP (IP Addressing)',
   'Mempelajari IP Address, kelas IP, dan dasar subnetting.',
   '#0F9B8E'),
  (3, 'Konfigurasi IP di Windows',
   'Praktik setting IP dan verifikasi dengan CMD.',
   '#7B5EA7'),
  (4, 'Implementasi VLAN',
   'Konsep VLAN dan konfigurasi di switch Cisco.',
   '#F4A261'),
  (5, 'Static Routing',
   'Konsep routing dan konfigurasi static route di router Cisco.',
   '#E05263')
ON CONFLICT DO NOTHING;

-- ============================================================
-- CATATAN PENGGUNAAN
-- ============================================================
-- 1. Jalankan SQL di atas di Supabase SQL Editor
-- 2. Update password_hash dengan hash yang valid (generate via Python)
-- 3. Aktifkan Row Level Security (RLS) di production
