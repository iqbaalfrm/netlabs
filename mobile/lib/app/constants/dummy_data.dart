// Data dummy terpusat — nanti diganti API call
class DummyData {
  // Data siswa
  static const String nama = 'Iqbal';
  static const String nis = '2122100045';
  static const String kelas = 'XI TKJ 2';
  static const String sekolah = 'SMK Bhakti Praja Dukuhwaru';

  // Statistik
  static const int pertemuanSelesai = 3;
  static const int totalPertemuan = 5;
  static const int nilaiRataRata = 82;
  static const int streakHari = 7;

  // Balasan AI dummy berdasarkan kata kunci
  static String getBalasanAI(String pertanyaan) {
    final lower = pertanyaan.toLowerCase();
    if (lower.contains('ip') || lower.contains('address')) {
      return 'IP Address adalah alamat logis yang diberikan ke setiap perangkat dalam jaringan. '
          'Format IPv4 terdiri dari 4 oktet (contoh: 192.168.1.1). '
          'Setiap oktet bernilai 0-255.\n\n'
          'IP Address dibagi menjadi:\n'
          '• Kelas A: 1.0.0.0 - 126.255.255.255\n'
          '• Kelas B: 128.0.0.0 - 191.255.255.255\n'
          '• Kelas C: 192.0.0.0 - 223.255.255.255';
    } else if (lower.contains('vlan')) {
      return 'VLAN (Virtual Local Area Network) adalah teknologi untuk membagi satu jaringan fisik '
          'menjadi beberapa jaringan logis.\n\n'
          'Fungsi VLAN:\n'
          '• Memisahkan broadcast domain\n'
          '• Meningkatkan keamanan jaringan\n'
          '• Memudahkan manajemen jaringan\n\n'
          'Perintah membuat VLAN di Cisco:\n'
          'Switch(config)# vlan 10\n'
          'Switch(config-vlan)# name GURU';
    } else if (lower.contains('routing') || lower.contains('route')) {
      return 'Routing adalah proses menentukan jalur terbaik untuk mengirim paket data.\n\n'
          'Jenis routing:\n'
          '• Static: dikonfigurasi manual oleh admin\n'
          '• Dynamic: otomatis menggunakan protokol (OSPF, RIP)\n\n'
          'Perintah static route:\n'
          'Router(config)# ip route 192.168.2.0 255.255.255.0 10.0.0.2';
    } else if (lower.contains('subnet')) {
      return 'Subnetting adalah teknik membagi jaringan besar menjadi sub-jaringan lebih kecil.\n\n'
          'Tujuan:\n'
          '• Efisiensi penggunaan IP\n'
          '• Meningkatkan keamanan\n'
          '• Mengurangi traffic broadcast\n\n'
          'Contoh: 192.168.1.0/26\n'
          'Subnet mask: 255.255.255.192\n'
          'Jumlah host: 62';
    } else if (lower.contains('ping') || lower.contains('cmd')) {
      return 'Perintah CMD untuk verifikasi jaringan:\n\n'
          '• ipconfig — melihat konfigurasi IP\n'
          '• ping 192.168.1.1 — test koneksi\n'
          '• tracert google.com — lacak jalur paket\n'
          '• ipconfig /all — detail lengkap\n\n'
          'Jika ping gagal, cek:\n'
          '1. Kabel fisik\n'
          '2. Konfigurasi IP\n'
          '3. Default gateway';
    } else if (lower.contains('topologi')) {
      return 'Topologi jaringan adalah pola penyusunan koneksi antar perangkat.\n\n'
          'Jenis topologi:\n'
          '• Star: semua terhubung ke switch pusat (paling umum)\n'
          '• Bus: satu kabel utama\n'
          '• Ring: membentuk lingkaran\n'
          '• Mesh: setiap perangkat terhubung ke semua\n\n'
          'Topologi Star paling banyak digunakan karena mudah troubleshooting.';
    } else {
      return 'Berdasarkan materi praktikum jaringan komputer, topik ini berkaitan dengan '
          'konsep dasar networking.\n\n'
          'Coba tanyakan lebih spesifik tentang:\n'
          '• IP Address & Subnetting\n'
          '• VLAN & konfigurasinya\n'
          '• Routing (static/dynamic)\n'
          '• Perintah CMD (ping, ipconfig)\n'
          '• Topologi jaringan\n\n'
          'Saya akan menjawab berdasarkan modul praktikum.';
    }
  }

  // Suggestion chips untuk chat
  static const List<String> chatSuggestions = [
    'Apa itu IP Address?',
    'Cara konfigurasi VLAN?',
    'Perbedaan static & dynamic routing?',
    'Cara subnetting?',
    'Perintah ping gagal, kenapa?',
  ];

  // ===== DUMMY DATA PERTEMUAN (fallback saat offline) =====
  static final List<Map<String, dynamic>> semester1 = [
    {'id': 'p1', 'nomor_urut': 1, 'judul': 'Pengenalan Jaringan Komputer', 'warna_hex': '#2D7DD2', 'progress': 1.0, 'status': 'selesai', 'kuis': true, 'topik': 4},
    {'id': 'p2', 'nomor_urut': 2, 'judul': 'Pengalamatan IP (IP Addressing)', 'warna_hex': '#0F9B8E', 'progress': 0.75, 'status': 'selesai', 'kuis': true, 'topik': 4},
    {'id': 'p3', 'nomor_urut': 3, 'judul': 'Konfigurasi IP di Windows', 'warna_hex': '#7B5EA7', 'progress': 0.5, 'status': 'aktif', 'kuis': false, 'topik': 3},
    {'id': 'p4', 'nomor_urut': 4, 'judul': 'Implementasi VLAN', 'warna_hex': '#F4A261', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 4},
    {'id': 'p5', 'nomor_urut': 5, 'judul': 'Static Routing', 'warna_hex': '#E05263', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 3},
    {'id': 'p6', 'nomor_urut': 6, 'judul': 'Dynamic Routing (OSPF)', 'warna_hex': '#2D7DD2', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 3},
    {'id': 'p7', 'nomor_urut': 7, 'judul': 'Network Address Translation', 'warna_hex': '#0F9B8E', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 3},
    {'id': 'p8', 'nomor_urut': 8, 'judul': 'Ujian Praktik Semester 1', 'warna_hex': '#1A2B5F', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 0},
  ];

  static final List<Map<String, dynamic>> semester2 = [
    {'id': 'p9',  'nomor_urut': 9,  'judul': 'Wireless LAN (WiFi)', 'warna_hex': '#7B5EA7', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 4},
    {'id': 'p10', 'nomor_urut': 10, 'judul': 'Keamanan Jaringan Dasar', 'warna_hex': '#F4A261', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 3},
    {'id': 'p11', 'nomor_urut': 11, 'judul': 'Firewall & Access Control List', 'warna_hex': '#E05263', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 4},
    {'id': 'p12', 'nomor_urut': 12, 'judul': 'Monitoring Jaringan', 'warna_hex': '#2D7DD2', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 3},
    {'id': 'p13', 'nomor_urut': 13, 'judul': 'Troubleshooting Jaringan', 'warna_hex': '#0F9B8E', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 4},
    {'id': 'p14', 'nomor_urut': 14, 'judul': 'Manajemen Bandwidth', 'warna_hex': '#7B5EA7', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 3},
    {'id': 'p15', 'nomor_urut': 15, 'judul': 'Proyek Akhir Jaringan', 'warna_hex': '#F4A261', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 2},
    {'id': 'p16', 'nomor_urut': 16, 'judul': 'Ujian Praktik Semester 2', 'warna_hex': '#1A2B5F', 'progress': 0.0, 'status': 'terkunci', 'kuis': false, 'topik': 0},
  ];
}
