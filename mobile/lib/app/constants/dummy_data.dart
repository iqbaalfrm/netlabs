// Data dummy terpusat — murni Jaringan Komputer Dasar (tanpa unsur Fiqh/Kripto)
class DummyData {
  // Data siswa
  static const String nama = 'Muhammad Iqbal';
  static const String nis = '2122100045';
  static const String kelas = 'XI TKJ 2';
  static const String sekolah = 'SMK';

  // Statistik
  static const int pertemuanSelesai = 3;
  static const int totalPertemuan = 5;
  static const int nilaiRataRata = 82;
  static const int totalChat = 24;

  // Balasan AI dummy berdasarkan kata kunci (IT Networking)
  static String getBalasanAI(String pertanyaan) {
    final lower = pertanyaan.toLowerCase();
    
    if (lower.contains('ip') || lower.contains('address')) {
      return 'IP Address adalah alamat logis yang diberikan ke setiap perangkat dalam jaringan komputer.\n\n'
          'Format IPv4 terdiri dari 32 bit yang dibagi menjadi 4 oktet (contoh: 192.168.1.1). Tiap oktet bernilai 0-255.\n\n'
          'Kelas IP Address:\n'
          '• Kelas A: 1.0.0.0 - 126.255.255.255 (Subnet mask default: 255.0.0.0)\n'
          '• Kelas B: 128.0.0.0 - 191.255.255.255 (Subnet mask default: 255.255.0.0)\n'
          '• Kelas C: 192.0.0.0 - 223.255.255.255 (Subnet mask default: 255.255.255.0)\n\n'
          'Perangkat server biasanya dikonfigurasi dengan IP Statis agar tidak berubah-ubah, sedangkan perangkat klien menggunakan DHCP.';
    } else if (lower.contains('vlan')) {
      return 'VLAN (Virtual Local Area Network) adalah teknologi yang memungkinkan satu switch fisik dibagi menjadi beberapa jaringan logis terpisah.\n\n'
          'Fungsi VLAN:\n'
          '• Memisahkan domain broadcast untuk meningkatkan efisiensi bandwidth.\n'
          '• Meningkatkan keamanan jaringan dengan mengontrol akses antar divisi.\n'
          '• Mempermudah manajemen jaringan secara logis tanpa memindahkan kabel fisik.\n\n'
          'Contoh perintah membuat VLAN di switch Cisco:\n'
          'Switch(config)# vlan 10\n'
          'Switch(config-vlan)# name GURU';
    } else if (lower.contains('routing') || lower.contains('route')) {
      return 'Routing adalah proses menentukan jalur terbaik (best path) untuk mengirimkan paket data dari satu jaringan ke jaringan lain.\n\n'
          'Jenis-jenis Routing:\n'
          '• Static Routing: Dikonfigurasi secara manual oleh network administrator. Stabil dan hemat resource router, namun sulit dikelola pada jaringan skala besar.\n'
          '• Dynamic Routing: Otomatis mencari rute terbaik menggunakan protokol routing seperti OSPF, RIP, atau BGP. Menyesuaikan diri secara otomatis jika ada link yang mati.';
    } else if (lower.contains('subnet')) {
      return 'Subnetting adalah teknik membagi jaringan IP yang besar menjadi beberapa sub-jaringan (subnet) yang lebih kecil.\n\n'
          'Tujuan Subnetting:\n'
          '• Mengurangi lalu lintas broadcast domain.\n'
          '• Mengoptimalkan efisiensi alokasi IP Address agar tidak ada IP yang terbuang sia-sia.\n'
          '• Meningkatkan keamanan jaringan.\n\n'
          'Contoh: Network 192.168.1.0/26 memiliki subnet mask 255.255.255.192 dan menyediakan 62 host yang dapat digunakan.';
    } else if (lower.contains('ping') || lower.contains('cmd') || lower.contains('trouble')) {
      return 'Pengecekan koneksi jaringan menggunakan Command Prompt (CMD) sangat penting untuk troubleshooting.\n\n'
          'Perintah dasar:\n'
          '• ipconfig — Melihat alamat IP, subnet mask, dan default gateway komputer saat ini.\n'
          '• ping [ip_tujuan] — Menguji konektivitas dan mengukur waktu respons (latency) ke host tujuan.\n'
          '• tracert [ip_tujuan] — Melacak rute perjalanan paket data dari komputer lokal hingga ke tujuan untuk mendeteksi rute yang bermasalah.';
    } else if (lower.contains('topologi')) {
      return 'Topologi jaringan adalah desain tata letak fisik atau logis dari koneksi antar perangkat dalam jaringan.\n\n'
          'Jenis Topologi:\n'
          '• Star: Semua perangkat terhubung ke switch/hub pusat. Sangat populer karena mudah di-troubleshoot dan jika satu kabel putus, perangkat lain tidak terganggu.\n'
          '• Mesh: Setiap perangkat terhubung langsung ke semua perangkat lain. Sangat handal karena memiliki banyak jalur cadangan (redundansi), namun mahal dalam pemasangannya.';
    } else {
      return 'Halo! Saya AI Tutor Netlabs. Silakan ajukan pertanyaan seputar materi praktikum Jaringan Komputer Dasar.\n\n'
          'Rekomendasi topik tanya jawab:\n'
          '• Pembagian Kelas IP Address & Subnetting\n'
          '• Konfigurasi VLAN di Switch Cisco\n'
          '• Perbedaan Static Routing & Dynamic Routing (OSPF)\n'
          '• Perintah CMD untuk troubleshooting (ping, ipconfig, tracert)\n'
          '• Jenis-jenis Topologi Jaringan (Star, Mesh, Ring, dll.)';
    }
  }

  // Suggestion chips untuk chat
  static const List<String> chatSuggestions = [
    'Apa itu IP Address?',
    'Bagaimana cara konfigurasi VLAN?',
    'Apa perbedaan static & dynamic routing?',
    'Bagaimana cara menghitung subnetting?',
    'Kenapa ping saya RTO (Request Time Out)?',
  ];

  // ===== DUMMY DATA PERTEMUAN (offline demo mode) =====
  static final List<Map<String, dynamic>> semester1 = [
    {
      'id': 'p1',
      'nomor': 1,
      'nomor_urut': 1,
      'judul': 'Pengenalan Jaringan Komputer',
      'warna_hex': '#2D7DD2',
      'progress': 1.0,
      'status': 'selesai',
      'kuis': true,
      'topik': 4
    },
    {
      'id': 'p2',
      'nomor': 2,
      'nomor_urut': 2,
      'judul': 'Pengalamatan IP (IP Addressing)',
      'warna_hex': '#0F9B8E',
      'progress': 0.75,
      'status': 'selesai',
      'kuis': true,
      'topik': 4
    },
    {
      'id': 'p3',
      'nomor': 3,
      'nomor_urut': 3,
      'judul': 'Konfigurasi IP di Windows',
      'warna_hex': '#7B5EA7',
      'progress': 0.5,
      'status': 'aktif',
      'kuis': false,
      'topik': 3
    },
    {
      'id': 'p4',
      'nomor': 4,
      'nomor_urut': 4,
      'judul': 'Implementasi VLAN',
      'warna_hex': '#F4A261',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 4
    },
    {
      'id': 'p5',
      'nomor': 5,
      'nomor_urut': 5,
      'judul': 'Static Routing',
      'warna_hex': '#E05263',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 3
    },
    {
      'id': 'p6',
      'nomor': 6,
      'nomor_urut': 6,
      'judul': 'Dynamic Routing (OSPF)',
      'warna_hex': '#2D7DD2',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 3
    },
    {
      'id': 'p7',
      'nomor': 7,
      'nomor_urut': 7,
      'judul': 'Network Address Translation (NAT)',
      'warna_hex': '#0F9B8E',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 3
    },
    {
      'id': 'p8',
      'nomor': 8,
      'nomor_urut': 8,
      'judul': 'Ujian Praktik Jaringan Semester 1',
      'warna_hex': '#1A2B5F',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 0
    },
  ];

  static final List<Map<String, dynamic>> semester2 = [
    {
      'id': 'p9',
      'nomor': 9,
      'nomor_urut': 9,
      'judul': 'Wireless LAN (WiFi)',
      'warna_hex': '#7B5EA7',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 4
    },
    {
      'id': 'p10',
      'nomor': 10,
      'nomor_urut': 10,
      'judul': 'Keamanan Jaringan Dasar',
      'warna_hex': '#F4A261',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 3
    },
    {
      'id': 'p11',
      'nomor': 11,
      'nomor_urut': 11,
      'judul': 'Firewall & Access Control List',
      'warna_hex': '#E05263',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 4
    },
    {
      'id': 'p12',
      'nomor': 12,
      'nomor_urut': 12,
      'judul': 'Monitoring Jaringan',
      'warna_hex': '#2D7DD2',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 3
    },
    {
      'id': 'p13',
      'nomor': 13,
      'nomor_urut': 13,
      'judul': 'Troubleshooting Jaringan',
      'warna_hex': '#0F9B8E',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 4
    },
    {
      'id': 'p14',
      'nomor': 14,
      'nomor_urut': 14,
      'judul': 'Manajemen Bandwidth',
      'warna_hex': '#7B5EA7',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 3
    },
    {
      'id': 'p15',
      'nomor': 15,
      'nomor_urut': 15,
      'judul': 'Proyek Akhir Rancang Bangun Jaringan',
      'warna_hex': '#F4A261',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 2
    },
    {
      'id': 'p16',
      'nomor': 16,
      'nomor_urut': 16,
      'judul': 'Ujian Praktik Jaringan Semester 2',
      'warna_hex': '#1A2B5F',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 0
    },
  ];
}
