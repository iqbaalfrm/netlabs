# Product Requirements Document
# Netlabs — Intelligent Tutoring System + LMS Praktikum Jaringan Komputer

| | |
|---|---|
| **Document Owner** | Muhammad Iqbal |
| **Product Manager** | Muhammad Iqbal |
| **Designer** | Muhammad Iqbal |
| **Tech Lead** | Muhammad Iqbal |
| **Developer** | Muhammad Iqbal |
| **QA** | Muhammad Iqbal |

---

## 1. Latar Belakang dan Tujuan

### 1.1 Latar Belakang

Praktikum Komputer Jaringan Dasar di SMK masih menghadapi beberapa kendala utama, di antaranya keterbatasan pendampingan guru saat kegiatan praktikum berlangsung, minimnya media belajar mandiri yang kontekstual, serta rendahnya kemandirian siswa dalam menyelesaikan permasalahan praktikum tanpa bantuan langsung dari guru.

Berdasarkan observasi di salah satu SMK, siswa kelas XI TKJ sering mengalami kebingungan saat menghadapi langkah-langkah konfigurasi jaringan dan tidak memiliki akses cepat ke sumber referensi yang relevan dengan materi yang sedang dipelajari. Ketergantungan siswa terhadap guru menyebabkan proses praktikum menjadi kurang efisien.

Perkembangan teknologi kecerdasan buatan, khususnya Retrieval-Augmented Generation (RAG), membuka peluang untuk mengembangkan sistem tutor cerdas yang mampu memberikan jawaban kontekstual berdasarkan modul dan materi resmi sekolah. Teknologi ini memungkinkan sistem untuk tidak hanya menjawab pertanyaan secara umum, tetapi secara spesifik merujuk pada konten modul praktikum yang telah diunggah oleh guru.

Oleh karena itu, dikembangkan **Netlabs** — sebuah platform Intelligent Tutoring System (ITS) berbasis RAG yang diintegrasikan dengan Learning Management System (LMS) dalam bentuk aplikasi Android untuk siswa dan dashboard web untuk guru.

### 1.2 Tujuan

- Membantu siswa memperoleh bimbingan praktikum secara mandiri melalui AI Tutor berbasis RAG.
- Menyediakan sistem tanya jawab berbasis AI yang menjawab sesuai dengan modul dan jobsheet praktikum Komputer Jaringan Dasar.
- Mengintegrasikan knowledge base praktikum menggunakan teknologi RAG dengan ChromaDB sebagai vector store.
- Meningkatkan kemandirian belajar siswa selama kegiatan praktikum berlangsung.
- Menyediakan sistem LMS bagi guru untuk mengelola materi, topik, dan soal kuis per pertemuan.
- Mengembangkan aplikasi mobile Android yang mudah digunakan di lingkungan laboratorium komputer SMK.

---

## 2. Success Metrics

| Tipe | Metrik |
|------|--------|
| **[Main]** | Peningkatan kemandirian belajar siswa selama kegiatan praktikum Komputer Jaringan Dasar yang diukur melalui penurunan frekuensi bertanya langsung kepada guru. |
| **[Main]** | Tingkat relevansi jawaban sistem AI Tutor terhadap pertanyaan siswa berdasarkan knowledge base modul praktikum (target: >75% jawaban relevan). |
| **[Secondary]** | Jumlah pertanyaan siswa yang berhasil dijawab oleh sistem ITS berbasis RAG per sesi praktikum. |
| **[Secondary]** | Tingkat kepuasan pengguna (siswa dan guru) terhadap kemudahan penggunaan aplikasi, diukur menggunakan kuesioner SUS (System Usability Scale). |
| **[Secondary]** | Penurunan ketergantungan siswa terhadap bantuan langsung guru, diukur dari data observasi sebelum dan sesudah penggunaan aplikasi. |
| **[Secondary]** | Waktu respons sistem dalam menampilkan jawaban AI kepada pengguna (target: <3 detik). |
| **[Secondary]** | Tingkat keberhasilan integrasi aplikasi mobile dengan backend RAG tanpa error saat pengujian fungsional. |
| **[Secondary]** | Persentase siswa yang menyelesaikan seluruh topik dalam satu pertemuan sebelum mengerjakan kuis. |

---

## 3. Requirements (Kebutuhan) Aplikasi

### 3.1 Daftar Requirement

#### Aplikasi Mobile Siswa (Android)

| Kode Req | Requirement |
|----------|-------------|
| | **Halaman Splash & Login** |
| REQ-001 | Sistem menampilkan splash screen dengan identitas aplikasi Netlabs saat pertama kali dibuka. |
| REQ-002 | Siswa dapat login menggunakan NIS (Nomor Induk Siswa) dan kata sandi. |
| REQ-003 | Sistem menyimpan sesi login siswa menggunakan token JWT sehingga siswa tidak perlu login ulang saat membuka aplikasi kembali. |
| | **Halaman Beranda** |
| REQ-004 | Siswa dapat melihat ringkasan aktivitas belajar pada halaman beranda, meliputi progress keseluruhan, jumlah pertemuan yang telah diselesaikan, nilai rata-rata, dan streak belajar harian. |
| REQ-005 | Siswa dapat melihat informasi identitas singkat (nama dan kelas) serta akses cepat ke fitur AI Tutor dari beranda. |
| REQ-006 | Siswa dapat melihat daftar pertemuan yang sedang aktif (berlangsung) beserta progress persentasenya di beranda. |
| REQ-007 | Siswa dapat melihat pertanyaan terakhir yang diajukan ke AI Tutor beserta waktu pengirimannya. |
| REQ-008 | Siswa dapat melihat kuis yang tersedia untuk dikerjakan di bagian bawah beranda. |
| | **Halaman Materi (Daftar Pertemuan)** |
| REQ-009 | Siswa dapat melihat daftar semua pertemuan praktikum yang tersedia, dikelompokkan berdasarkan semester (Semester 1 dan Semester 2). |
| REQ-010 | Setiap kartu pertemuan menampilkan informasi nomor urut, judul pertemuan, jumlah topik, estimasi waktu belajar, dan progress penyelesaian. |
| REQ-011 | Pertemuan yang belum terbuka (terkunci) ditampilkan dengan ikon gembok dan tidak dapat diakses siswa sebelum memenuhi syarat. |
| REQ-012 | Sistem menampilkan banner pertemuan yang sedang aktif di bagian atas halaman materi sebagai akses cepat untuk melanjutkan belajar. |
| | **Halaman Detail Pertemuan** |
| REQ-013 | Siswa dapat melihat detail pertemuan yang mencakup judul, deskripsi, tujuan pembelajaran, dan daftar topik yang harus dipelajari. |
| REQ-014 | Siswa dapat membuka detail materi setiap topik yang berisi isi materi lengkap dalam bentuk teks terstruktur. |
| REQ-015 | Siswa dapat menandai topik sebagai "sudah dibaca" setelah selesai membaca materi, dan sistem akan menyimpan progress secara otomatis. |
| REQ-016 | Tombol "Mulai Kuis" pada halaman detail pertemuan hanya aktif jika seluruh topik telah ditandai selesai oleh siswa. |
| REQ-017 | Siswa dapat mengakses fitur AI Tutor kontekstual dari halaman detail pertemuan untuk bertanya tentang materi yang sedang dipelajari. |
| | **AI Chat (AI Tutor Berbasis RAG)** |
| REQ-018 | Siswa dapat mengirim pertanyaan terkait materi, praktikum, maupun kendala jaringan komputer melalui antarmuka chat. |
| REQ-019 | Sistem dapat memberikan jawaban yang relevan dan kontekstual menggunakan teknologi RAG berdasarkan knowledge base berupa modul dan jobsheet praktikum resmi yang diunggah guru. |
| REQ-020 | Sistem menampilkan label sumber referensi jawaban (nama modul/pertemuan) di bawah setiap respons AI Tutor. |
| REQ-021 | Sistem menyediakan suggestion chips berupa pertanyaan umum yang relevan untuk membantu siswa memulai percakapan. |
| REQ-022 | Sistem menampilkan indikator "sedang mengetik" saat AI sedang memproses jawaban. |
| REQ-023 | Sistem menyimpan riwayat percakapan siswa dengan AI Tutor ke database untuk keperluan monitoring guru. |
| | **Halaman Kuis** |
| REQ-024 | Siswa dapat mengerjakan kuis evaluasi berupa soal pilihan ganda (4 pilihan) untuk setiap pertemuan yang telah diselesaikan. |
| REQ-025 | Sistem menampilkan soal satu per satu dengan progress bar yang menunjukkan nomor soal saat ini dari total soal. |
| REQ-026 | Tombol "Lanjut" hanya aktif setelah siswa memilih satu jawaban untuk soal yang sedang ditampilkan. |
| REQ-027 | Sistem menampilkan halaman hasil kuis setelah seluruh soal selesai dijawab, mencakup nilai, jumlah benar, jumlah salah, dan rekomendasi belajar dari AI. |
| REQ-028 | Sistem menyimpan hasil kuis siswa (nilai, jumlah benar, total soal, waktu pengerjaan) ke database. |
| | **Halaman Profil** |
| REQ-029 | Siswa dapat melihat informasi profil lengkap mencakup nama, NIS, kelas, nama sekolah, dan statistik belajar. |
| REQ-030 | Siswa dapat keluar dari akun (logout) melalui halaman profil dengan konfirmasi bottom sheet. |

#### Web Admin Guru

| Kode Req | Requirement |
|----------|-------------|
| | **Halaman Login** |
| REQ-031 | Guru dapat login menggunakan NIS/ID guru dan kata sandi. Sistem memverifikasi bahwa akun yang login memiliki role "guru". |
| | **Dashboard** |
| REQ-032 | Guru dapat melihat ringkasan statistik pada dashboard: total siswa, total chat hari ini, rata-rata nilai, dan jumlah pertemuan aktif. |
| REQ-033 | Guru dapat melihat daftar pertanyaan terbaru yang diajukan siswa ke AI Tutor untuk keperluan monitoring. |
| | **Halaman Pertemuan** |
| REQ-034 | Guru dapat melihat daftar semua pertemuan yang telah dibuat. |
| REQ-035 | Guru dapat menambahkan pertemuan baru dengan mengisi nomor urut, judul, deskripsi, dan memilih warna tema. |
| REQ-036 | Guru dapat membuka halaman detail pertemuan untuk mengelola topik materi dan soal kuis. |
| | **Halaman Detail Pertemuan** |
| REQ-037 | Guru dapat menambahkan topik materi baru dalam suatu pertemuan dengan mengisi judul dan isi materi. |
| REQ-038 | Guru dapat menghapus topik materi dari suatu pertemuan. |
| REQ-039 | Guru dapat menambahkan soal kuis pilihan ganda baru beserta 4 pilihan jawaban, kunci jawaban, dan penjelasan. |
| REQ-040 | Guru dapat menghapus soal kuis yang tidak diperlukan. |
| | **Halaman Data Siswa** |
| REQ-041 | Guru dapat melihat daftar semua siswa beserta statistik: NIS, nama, kelas, total chat, dan streak belajar. |
| REQ-042 | Guru dapat melakukan pencarian siswa berdasarkan nama atau NIS. |

---

### 3.2 Fitur di Luar Ruang Lingkup

Fitur-fitur berikut tidak termasuk dalam cakupan pengembangan Netlabs versi ini:

- Registrasi akun mandiri oleh siswa (akun dibuat oleh admin/guru)
- Gamifikasi tingkat lanjut (leaderboard, kompetisi antar siswa)
- Notifikasi push (push notification)
- Sinkronisasi dengan sistem akademik sekolah (SIMAS, e-rapor)
- Fitur video pembelajaran atau live streaming
- Pembayaran atau monetisasi platform
- Versi iOS (hanya Android yang dikembangkan)
- Multi-bahasa (hanya Bahasa Indonesia)
- Upload foto profil siswa

---

### 3.3 Functional Requirements (Kebutuhan Fungsional)

| Requirement | Spesifikasi |
|-------------|-------------|
| **Halaman Beranda — Ringkasan Aktivitas Belajar** | |
| Siswa dapat melihat statistik belajar pada bagian progress cards di beranda. | • Terdapat 3 kartu statistik: Pertemuan (x/total), Nilai rata-rata, Streak hari belajar berturut-turut. • Setiap kartu menampilkan ikon yang relevan, angka utama bold, dan label kecil. • Data diambil dari API backend dan ditampilkan secara real-time. |
| Siswa dapat melihat daftar pertemuan yang sedang aktif di beranda. | • Daftar ditampilkan dalam format horizontal scroll card. • Setiap card menampilkan: nomor pertemuan, judul (maks. 2 baris), jumlah topik, dan progress bar. • Tap pada card mengarahkan ke halaman Detail Pertemuan yang sesuai. • Tombol "Lihat semua" di kanan mengarahkan ke tab Materi. |
| **AI Chat — Sistem RAG** | |
| Sistem memproses pertanyaan siswa menggunakan pipeline RAG. | **Pipeline RAG:** 1. Pertanyaan siswa diubah menjadi vektor embedding menggunakan model `all-MiniLM-L6-v2`. 2. Sistem mencari 3 chunk paling relevan di ChromaDB, difilter berdasarkan `pertemuan_id`. 3. Chunk relevan + riwayat 5 pesan terakhir dikirim ke Claude API (model: claude-3-haiku). 4. Jawaban dikembalikan ke siswa beserta label sumber referensi. 5. Percakapan disimpan ke tabel `chat_history` di Supabase. |
| Sistem menampilkan indikator sumber jawaban. | • Label sumber ditampilkan sebagai chip kecil di bawah bubble respons AI. • Format: "Nama Pertemuan — Modul Praktikum". • Jika tidak ada chunk relevan, AI menjawab berdasarkan pengetahuan umum tanpa label sumber. |
| **Halaman Kuis — Mekanisme Evaluasi** | |
| Sistem memvalidasi jawaban kuis dan menyimpan hasil. | • Soal diambil dari API `/api/kuis/{pertemuan_id}` (maks. 5 soal random). • Setiap soal ditampilkan satu per satu dengan 4 pilihan jawaban (A, B, C, D). • Jawaban dipilih dengan mengetuk card pilihan; card yang dipilih ter-highlight biru. • Setelah semua soal dijawab, nilai dihitung: (benar ÷ total) × 100. • Hasil disimpan ke tabel `hasil_kuis` di Supabase. |
| **Upload Modul PDF — Indexing RAG** | |
| Guru dapat mengunggah modul PDF yang akan diindex secara otomatis untuk AI Tutor. | • Guru mengunggah file PDF melalui endpoint `POST /api/modul/upload`. • Backend melakukan parsing PDF menggunakan PyMuPDF. • Teks di-chunk per ~400 kata dengan metadata `{pertemuan_id, nomor_halaman}`. • Setiap chunk diubah menjadi embedding dan disimpan ke ChromaDB. • Status indexing diperbarui di tabel `modul_pdf`. |
