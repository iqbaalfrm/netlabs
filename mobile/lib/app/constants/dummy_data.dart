// Data dummy terpusat — diselaraskan dengan Fiqh Muamalah & Mitigasi Risiko Aset Kripto
class DummyData {
  // Data siswa
  static const String nama = 'Muhammad Iqbal';
  static const String nis = '2122100045';
  static const String kelas = 'XI TKJ 2';
  static const String sekolah = 'SMK Bhakti Praja Dukuhwaru';

  // Statistik
  static const int pertemuanSelesai = 3;
  static const int totalPertemuan = 5;
  static const int nilaiRataRata = 82;
  static const int streakHari = 7;

  // Balasan AI dummy berdasarkan kata kunci (Fiqh Muamalah & IT Networking)
  static String getBalasanAI(String pertanyaan) {
    final lower = pertanyaan.toLowerCase();
    
    if (lower.contains('syariah') || lower.contains('fiqh') || lower.contains('muamalah') || lower.contains('hukum')) {
      return 'Dalam perspektif Fiqh Muamalah, pemanfaatan infrastruktur jaringan komputer harus memenuhi prinsip kejujuran (amanah), kejelasan (transparansi/anti-gharar), dan keadilan.\n\n'
          'Penerapannya dalam praktikum jaringan:\n'
          '• Menggunakan perangkat lunak legal (menghindari ghasb/pencurian hak kekayaan intelektual).\n'
          '• Segmentasi jaringan keuangan syariah menggunakan VLAN agar terhindar dari kebocoran data nasabah.\n'
          '• Keandalan jaringan (redudansi) untuk meminimalisasi risiko kegagalan akad (transaksi) secara online.';
    } else if (lower.contains('kripto') || lower.contains('crypto') || lower.contains('blockchain') || lower.contains('investasi')) {
      return 'Aset Kripto dalam kajian Fiqh Muamalah kontemporer dipandang sebagai komoditas digital (sil\'ah) jika memenuhi kriteria memiliki nilai (tamawwul) dan kemanfaatan.\n\n'
          'Mitigasi Risiko dalam jaringan Aset Kripto:\n'
          '• Keamanan Kunci Privat (Private Key) melalui enkripsi end-to-end.\n'
          '• Penggunaan subnetting terisolasi khusus untuk node validator blockchain.\n'
          '• Implementasi Access Control List (ACL) pada firewall untuk menyaring traffic mencurigakan ke crypto wallet server.';
    } else if (lower.contains('ip') || lower.contains('address')) {
      return 'IP Address adalah identitas logis perangkat di jaringan komputer.\n\n'
          'Dalam implementasi Jaringan Keuangan Syariah, alokasi IP harus dikelola secara terencana:\n'
          '• Kelas IP Address (A, B, C) dipilih sesuai skala jaringan.\n'
          '• Contoh IP Kelas C: 192.168.1.1 (umumnya untuk host retail/kantor cabang).\n'
          '• Alokasi IP Statis diberikan pada server kritis seperti Sharia Core Banking, sementara DHCP digunakan untuk klien dinamis demi kemudahan manajemen.';
    } else if (lower.contains('vlan')) {
      return 'VLAN (Virtual Local Area Network) membagi satu switch fisik menjadi beberapa jaringan logis secara terpisah.\n\n'
          'Penerapan VLAN untuk Keuangan Syariah:\n'
          '• VLAN 10 (GURU/ADMIN) untuk mengelola data rahasia sekolah.\n'
          '• VLAN 20 (TRANSAKSI_SYARIAH) untuk jalur transaksi keuangan halal.\n'
          '• VLAN 30 (GUEST/SISWA) dibatasi aksesnya menggunakan router (Inter-VLAN Routing) guna mencegah penyusupan dan menjaga kepatuhan syariah (syariah compliance).';
    } else if (lower.contains('routing') || lower.contains('route')) {
      return 'Routing menentukan jalur terbaik (best path) pengiriman paket data dari satu jaringan ke jaringan lain.\n\n'
          'Dalam sistem transfer dana syariah, routing harus andal:\n'
          '• Static Routing dikonfigurasi manual oleh network administrator untuk stabilitas jalur utama.\n'
          '• Dynamic Routing (OSPF/BGP) digunakan untuk redundansi jika jalur utama terputus, guna meminimalisasi risiko penundaan transaksi (yang dilarang dalam transaksi syariah serah-terima tunai/taqabud).';
    } else if (lower.contains('subnet')) {
      return 'Subnetting membagi jaringan IP besar menjadi sub-jaringan (subnet) yang lebih kecil.\n\n'
          'Manfaat Subnetting dalam Mitigasi Risiko:\n'
          '• Mengisolasi domain broadcast demi meningkatkan keamanan data sensitif.\n'
          '• Membatasi akses antar divisi (misal: memisahkan server Aset Kripto dari jaringan siswa).\n'
          '• Contoh: Network 192.168.1.0/26 (Subnet Mask 255.255.255.192, menyediakan 62 host per subnet).';
    } else if (lower.contains('ping') || lower.contains('cmd') || lower.contains('trouble')) {
      return 'Pengecekan koneksi jaringan mutlak diperlukan untuk memastikan keberlangsungan transaksi keuangan online.\n\n'
          'Perintah CMD utama:\n'
          '• ipconfig /all — Cek alamat IP, DNS, dan default gateway.\n'
          '• ping [ip_tujuan] — Uji latensi jaringan. Latensi tinggi atau RTO (Request Time Out) berisiko menggagalkan transaksi online.\n'
          '• tracert [ip_tujuan] — Lacak rute perjalanan paket data untuk menemukan titik kegagalan jalur.';
    } else if (lower.contains('topologi')) {
      return 'Topologi jaringan menggambarkan tata letak fisik atau logis dari koneksi antar perangkat.\n\n'
          '• Topologi Star: Menggunakan switch pusat. Sangat direkomendasikan untuk kantor Lembaga Keuangan Syariah (BMT, Bank Syariah) karena kegagalan satu klien tidak mengganggu operasional sistem secara keseluruhan.\n'
          '• Topologi Mesh: Menyediakan jalur cadangan penuh (redundansi), krusial untuk mitigasi risiko downtime pada server perdagangan aset kripto.';
    } else {
      return 'Halo! Saya AI Tutor Netlabs. Silakan ajukan pertanyaan terkait materi praktikum Jaringan Komputer Dasar dan kaitannya dengan Fiqh Muamalah atau Mitigasi Risiko Aset Kripto.\n\n'
          'Rekomendasi topik tanya jawab:\n'
          '• Alokasi IP Address untuk Jaringan Keuangan Syariah\n'
          '• Segmentasi VLAN untuk Mengamankan Wallet Aset Kripto\n'
          '• Mitigasi Risiko downtime dengan Static/Dynamic Routing\n'
          '• Perintah dasar CMD (ping, ipconfig, tracert)\n'
          '• Hukum Fiqh Muamalah terkait penggunaan bandwidth dan software.';
    }
  }

  // Suggestion chips untuk chat
  static const List<String> chatSuggestions = [
    'Bagaimana hukum Fiqh Muamalah dalam jaringan?',
    'Cara mitigasi risiko wallet Kripto dengan VLAN?',
    'Apa guna routing untuk transaksi syariah?',
    'Cara subnetting IP Address?',
    'Bagaimana mendeteksi RTO di CMD?',
  ];

  // ===== DUMMY DATA PERTEMUAN (offline demo mode) =====
  static final List<Map<String, dynamic>> semester1 = [
    {
      'id': 'p1',
      'nomor': 1,
      'nomor_urut': 1,
      'judul': 'Pengenalan Jaringan & Kajian Syariah Teknologi',
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
      'judul': 'Pengalamatan IP & Mitigasi Risiko Aset Kripto',
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
      'judul': 'Konfigurasi IP & Keamanan Transaksi Muamalah',
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
      'judul': 'VLAN untuk Segmentasi Jaringan Syariah',
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
      'judul': 'Static Routing & Redundansi Data Kripto',
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
      'judul': 'Dynamic Routing (OSPF) & Keandalan Akad',
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
      'judul': 'NAT untuk Akses Aman Wallet Syariah',
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
      'judul': 'Ujian Praktik Jaringan & Kepatuhan Syariah 1',
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
      'judul': 'Wireless LAN (WiFi) & Keamanan Mobile Banking',
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
      'judul': 'Keamanan Jaringan & Proteksi Dompet Digital',
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
      'judul': 'Firewall & ACL untuk Server Fiqh Muamalah',
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
      'judul': 'Monitoring Jaringan & Deteksi Fraud Kripto',
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
      'judul': 'Troubleshooting & Keandalan FinTech Syariah',
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
      'judul': 'Manajemen Bandwidth untuk Aplikasi Syariah',
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
      'judul': 'Proyek Akhir Rancang Jaringan Lembaga Syariah',
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
      'judul': 'Ujian Praktik Jaringan & Kepatuhan Syariah 2',
      'warna_hex': '#1A2B5F',
      'progress': 0.0,
      'status': 'terkunci',
      'kuis': false,
      'topik': 0
    },
  ];
}
