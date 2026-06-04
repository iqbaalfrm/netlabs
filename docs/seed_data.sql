-- ============================================================
-- SEED DATA LENGKAP — Topik dan Soal Kuis
-- Jalankan SETELAH database_schema.sql
-- ============================================================

-- ============================================================
-- TOPIK PER PERTEMUAN
-- ============================================================

-- Pertemuan 1: Pengenalan Jaringan Komputer
INSERT INTO topik (pertemuan_id, judul, isi_materi, nomor_urut)
SELECT p.id, t.judul, t.isi_materi, t.nomor_urut
FROM pertemuan p,
(VALUES
  ('Pengertian Jaringan Komputer',
   'Jaringan komputer adalah sekumpulan komputer dan perangkat yang saling terhubung melalui media transmisi untuk saling bertukar data dan informasi.

Tujuan jaringan komputer:
1. Berbagi sumber daya (printer, file, internet)
2. Komunikasi antar pengguna
3. Akses informasi secara terpusat
4. Efisiensi biaya perangkat keras', 1),
  ('Jenis Jaringan (LAN, MAN, WAN)',
   'Berdasarkan jangkauan:

LAN (Local Area Network)
- Area kecil: ruangan, gedung, kampus
- Jangkauan: 10m - 1km
- Contoh: jaringan lab komputer

MAN (Metropolitan Area Network)
- Area satu kota
- Jangkauan: 1km - 50km

WAN (Wide Area Network)
- Area luas, antar kota/negara
- Contoh: Internet', 2),
  ('Topologi Jaringan',
   'Topologi Star: semua perangkat terhubung ke satu switch pusat. Paling umum karena mudah troubleshooting.

Topologi Bus: perangkat terhubung ke satu kabel utama.

Topologi Ring: perangkat terhubung membentuk lingkaran.

Topologi Mesh: setiap perangkat terhubung ke semua perangkat lain. Redunsi tinggi tapi mahal.', 3),
  ('Perangkat Keras Jaringan',
   'NIC (Network Interface Card): kartu jaringan di komputer.

Switch: menghubungkan perangkat dalam satu jaringan lokal.

Router: menghubungkan dua jaringan berbeda dan menentukan jalur paket.

Access Point: titik akses WiFi untuk koneksi nirkabel.

Kabel UTP: media transmisi kabel paling umum.', 4)
) AS t(judul, isi_materi, nomor_urut)
WHERE p.nomor_urut = 1
ON CONFLICT DO NOTHING;

-- Pertemuan 2: Pengalamatan IP
INSERT INTO topik (pertemuan_id, judul, isi_materi, nomor_urut)
SELECT p.id, t.judul, t.isi_materi, t.nomor_urut
FROM pertemuan p,
(VALUES
  ('Pengertian IP Address',
   'IP Address adalah alamat logis 32-bit yang diberikan ke setiap perangkat jaringan.
Format: 4 oktet desimal dipisah titik (contoh: 192.168.1.1)
Setiap oktet bernilai 0-255.', 1),
  ('Kelas IP Address',
   'Kelas A: 1.0.0.0 - 126.255.255.255 (jaringan besar)
Kelas B: 128.0.0.0 - 191.255.255.255 (jaringan menengah)
Kelas C: 192.0.0.0 - 223.255.255.255 (jaringan kecil)

Subnet mask default:
Kelas A: 255.0.0.0
Kelas B: 255.255.0.0
Kelas C: 255.255.255.0', 2),
  ('IP Public vs IP Private',
   'IP Public: bisa diakses dari internet, diberikan ISP.

IP Private (tidak bisa diakses internet):
- 10.0.0.0 - 10.255.255.255 (Kelas A)
- 172.16.0.0 - 172.31.255.255 (Kelas B)
- 192.168.0.0 - 192.168.255.255 (Kelas C)', 3),
  ('Subnetting Dasar',
   'Subnetting: membagi jaringan besar jadi sub-jaringan lebih kecil.

Contoh: 192.168.1.0/26
- Subnet mask: 255.255.255.192
- Jumlah subnet: 4
- Host per subnet: 62

Rumus host: 2^n - 2
(n = jumlah bit host)', 4)
) AS t(judul, isi_materi, nomor_urut)
WHERE p.nomor_urut = 2
ON CONFLICT DO NOTHING;

-- Pertemuan 3: Konfigurasi IP di Windows
INSERT INTO topik (pertemuan_id, judul, isi_materi, nomor_urut)
SELECT p.id, t.judul, t.isi_materi, t.nomor_urut
FROM pertemuan p,
(VALUES
  ('Setting IP Manual di Windows',
   'Langkah setting IP manual:
1. Control Panel → Network and Sharing Center
2. Klik adapter aktif → Properties
3. Pilih Internet Protocol Version 4 (TCP/IPv4) → Properties
4. Pilih "Use the following IP address"
5. Isi IP Address, Subnet Mask, Default Gateway
6. Klik OK

Contoh:
IP: 192.168.1.10
Subnet: 255.255.255.0
Gateway: 192.168.1.1', 1),
  ('Verifikasi dengan CMD',
   'Perintah CMD penting:

ipconfig - melihat konfigurasi IP aktif
ipconfig /all - detail lengkap termasuk MAC
ping 192.168.1.1 - test koneksi ke gateway
ping google.com - test koneksi internet
tracert google.com - lacak jalur paket
arp -a - lihat tabel ARP', 2),
  ('Troubleshooting Koneksi',
   'Langkah troubleshooting:
1. ping 127.0.0.1 → cek TCP/IP stack lokal
2. ping <IP sendiri> → cek NIC
3. ping <gateway> → cek koneksi lokal
4. ping 8.8.8.8 → cek koneksi internet
5. ping google.com → cek DNS

Jika gagal di langkah tertentu, masalah ada di antara langkah tersebut dan sebelumnya.', 3)
) AS t(judul, isi_materi, nomor_urut)
WHERE p.nomor_urut = 3
ON CONFLICT DO NOTHING;

-- ============================================================
-- SOAL KUIS PER PERTEMUAN
-- ============================================================

-- Soal Pertemuan 1
INSERT INTO soal_kuis (pertemuan_id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, penjelasan)
SELECT p.id, s.pertanyaan, s.pa, s.pb, s.pc, s.pd, s.jwb, s.penjelasan
FROM pertemuan p,
(VALUES
  ('Apa yang dimaksud dengan jaringan komputer?',
   'Sekumpulan komputer yang saling terhubung',
   'Satu komputer dengan banyak monitor',
   'Perangkat lunak untuk mengelola file',
   'Sistem operasi berbasis jaringan',
   'a', 'Jaringan komputer adalah sekumpulan komputer yang saling terhubung untuk berbagi data dan sumber daya.'),
  ('Jaringan yang mencakup area satu kota disebut?',
   'LAN', 'MAN', 'WAN', 'PAN',
   'b', 'MAN (Metropolitan Area Network) mencakup area satu kota, jangkauan 1-50km.'),
  ('Topologi yang paling umum digunakan saat ini?',
   'Bus', 'Ring', 'Star', 'Mesh',
   'c', 'Topologi Star paling umum karena mudah instalasi dan troubleshooting.'),
  ('Perangkat yang menghubungkan dua jaringan berbeda?',
   'Hub', 'Switch', 'Router', 'Repeater',
   'c', 'Router menghubungkan dua atau lebih jaringan yang berbeda dan menentukan jalur paket.'),
  ('Contoh jaringan WAN yang paling umum?',
   'WiFi rumah', 'LAN sekolah', 'Internet', 'Bluetooth',
   'c', 'Internet adalah WAN terbesar yang menghubungkan jutaan jaringan di seluruh dunia.')
) AS s(pertanyaan, pa, pb, pc, pd, jwb, penjelasan)
WHERE p.nomor_urut = 1
ON CONFLICT DO NOTHING;

-- Soal Pertemuan 2
INSERT INTO soal_kuis (pertemuan_id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, penjelasan)
SELECT p.id, s.pertanyaan, s.pa, s.pb, s.pc, s.pd, s.jwb, s.penjelasan
FROM pertemuan p,
(VALUES
  ('Berapa bit panjang alamat IPv4?',
   '16 bit', '32 bit', '64 bit', '128 bit',
   'b', 'IPv4 menggunakan 32 bit, dibagi menjadi 4 oktet masing-masing 8 bit.'),
  ('IP Address 192.168.1.1 termasuk kelas?',
   'Kelas A', 'Kelas B', 'Kelas C', 'Kelas D',
   'c', '192.x.x.x masuk range Kelas C (192.0.0.0 - 223.255.255.255).'),
  ('Manakah yang termasuk IP Private?',
   '8.8.8.8', '192.168.1.1', '203.0.113.1', '1.1.1.1',
   'b', '192.168.x.x adalah range IP Private Kelas C yang tidak bisa diakses langsung dari internet.'),
  ('Subnet mask default untuk Kelas C?',
   '255.0.0.0', '255.255.0.0', '255.255.255.0', '255.255.255.255',
   'c', 'Kelas C memiliki subnet mask default /24 atau 255.255.255.0.'),
  ('Tujuan utama subnetting adalah?',
   'Mempercepat internet', 'Efisiensi penggunaan IP Address', 'Menambah bandwidth', 'Mengenkripsi data',
   'b', 'Subnetting bertujuan membagi jaringan untuk efisiensi IP, keamanan, dan mengurangi broadcast.')
) AS s(pertanyaan, pa, pb, pc, pd, jwb, penjelasan)
WHERE p.nomor_urut = 2
ON CONFLICT DO NOTHING;

-- Soal Pertemuan 3
INSERT INTO soal_kuis (pertemuan_id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, penjelasan)
SELECT p.id, s.pertanyaan, s.pa, s.pb, s.pc, s.pd, s.jwb, s.penjelasan
FROM pertemuan p,
(VALUES
  ('Perintah CMD untuk melihat konfigurasi IP?',
   'ping', 'ipconfig', 'tracert', 'netstat',
   'b', 'ipconfig menampilkan konfigurasi IP aktif. ipconfig /all untuk detail lengkap.'),
  ('Perintah untuk menguji koneksi ke perangkat lain?',
   'ipconfig', 'ping', 'nslookup', 'arp',
   'b', 'ping mengirim ICMP request untuk mengecek konektivitas ke host tujuan.'),
  ('IP 127.0.0.1 disebut juga?',
   'Gateway', 'Broadcast', 'Loopback', 'Subnet',
   'c', '127.0.0.1 adalah alamat loopback untuk menguji TCP/IP stack lokal.'),
  ('Jika ping ke gateway gagal, kemungkinan masalah ada di?',
   'DNS server', 'Koneksi fisik atau konfigurasi IP lokal', 'Website tujuan', 'Browser',
   'b', 'Gagal ping gateway berarti masalah di jaringan lokal: kabel, NIC, atau konfigurasi IP.'),
  ('Default Gateway berfungsi sebagai?',
   'DNS resolver', 'Pintu keluar ke jaringan lain', 'Firewall', 'DHCP server',
   'b', 'Default Gateway adalah router yang menjadi pintu keluar ke jaringan lain atau internet.')
) AS s(pertanyaan, pa, pb, pc, pd, jwb, penjelasan)
WHERE p.nomor_urut = 3
ON CONFLICT DO NOTHING;
