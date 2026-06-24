/// Data dummy untuk tampilan sementara sebelum API tersedia
class DummyData {
  // --- Data siswa ---
  static const String nama = 'Muhammad Iqbal';
  static const String kelas = 'X TKJ 1';
  static const int totalPertemuan = 4;
  static const int pertemuanSelesai = 1;
  static const int nilaiRataRata = 85;
  static const int totalChat = 12;

  // --- Daftar pertemuan semester 1 & 2 (untuk PertemuanController) ---
  static final List<Map<String, dynamic>> semester1 = [
    {
      'id': 'p1',
      'nomor_urut': 1,
      'judul': 'K3LH & Perakitan Komputer',
      'status': 'selesai',
      'progress': 1.0,
      'topik_selesai': 3,
      'total_topik': 3,
    },
    {
      'id': 'p2',
      'nomor_urut': 2,
      'judul': 'Dasar-Dasar Jaringan Komputer',
      'status': 'aktif',
      'progress': 0.33,
      'topik_selesai': 1,
      'total_topik': 3,
    },
    {
      'id': 'p3',
      'nomor_urut': 3,
      'judul': 'IP Address & Subnetting',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p4',
      'nomor_urut': 4,
      'judul': 'Media Transmisi & Pengkabelan',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p5',
      'nomor_urut': 5,
      'judul': 'Konfigurasi IP di Windows',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p6',
      'nomor_urut': 6,
      'judul': 'Sharing Data & Resource',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p7',
      'nomor_urut': 7,
      'judul': 'Pengenalan Cisco Packet Tracer',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p8',
      'nomor_urut': 8,
      'judul': 'Simulasi Jaringan Sederhana',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
  ];

  static final List<Map<String, dynamic>> semester2 = [
    {
      'id': 'p9',
      'nomor_urut': 9,
      'judul': 'VLAN: Konsep & Implementasi',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p10',
      'nomor_urut': 10,
      'judul': 'Static & Dynamic Routing',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p11',
      'nomor_urut': 11,
      'judul': 'DHCP Server Configuration',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p12',
      'nomor_urut': 12,
      'judul': 'DNS Server Configuration',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p13',
      'nomor_urut': 13,
      'judul': 'FTP & File Server',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p14',
      'nomor_urut': 14,
      'judul': 'Web Server & HTTP',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p15',
      'nomor_urut': 15,
      'judul': 'Firewall & Keamanan Dasar',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
    {
      'id': 'p16',
      'nomor_urut': 16,
      'judul': 'Ujian Praktik Akhir',
      'status': 'terkunci',
      'progress': 0,
      'topik_selesai': 0,
      'total_topik': 3,
    },
  ];

  // --- Chat suggestions (untuk ChatView) ---
  static const List<String> chatSuggestions = [
    'Apa itu topologi star?',
    'Jelaskan perbedaan TCP dan UDP',
    'Bagaimana cara menghitung subnet mask?',
    'Apa fungsi switch?',
  ];

  // --- Balasan AI dummy (fallback jika API error) ---
  static String getBalasanAI(String pertanyaan) {
    final p = pertanyaan.toLowerCase();
    if (p.contains('topologi') && p.contains('star')) {
      return 'Topologi star adalah topologi jaringan di mana semua node terhubung ke perangkat pusat (hub/switch). '
          'Kelebihan: mudah deteksi kerusakan. Kekurangan: jika hub/switch rusak, semua jaringan mati. '
          'Topologi ini paling sering digunakan di lab sekolah karena instalasinya sederhana.';
    }
    if (p.contains('tcp') || p.contains('udp')) {
      return 'TCP (Transmission Control Protocol) dan UDP (User Datagram Protocol) adalah dua protokol layer transport:\n\n'
          '**TCP:**\n- Connection-oriented (handshake 3-way)\n- Menjamin data sampai utuh\n- Lebih lambat\n- Contoh: HTTP, FTP, Email\n\n'
          '**UDP:**\n- Connectionless\n- Tidak menjamin data sampai\n- Lebih cepat\n- Contoh: DNS, Streaming, VoIP';
    }
    if (p.contains('subnet')) {
      return 'Untuk menghitung subnet mask, gunakan rumus:\n\n'
          '1. Tentukan prefix (misal /26)\n'
          '2. Subnet mask = 255.255.255.192\n'
          '3. Jumlah subnet = 2^n (n = bit yang dipinjam)\n'
          '4. Jumlah host per subnet = 2^(32-prefix) - 2\n\n'
          'Contoh untuk /26: 4 subnet, masing-masing 62 host.';
    }
    if (p.contains('switch')) {
      return 'Switch adalah perangkat jaringan yang bekerja di Layer 2 (Data Link) model OSI. Fungsinya:\n\n'
          '1. Meneruskan frame berdasarkan MAC address\n'
          '2. Menghubungkan perangkat dalam satu segmen jaringan\n'
          '3. Menghindari collision domain (berbeda dengan hub)\n'
          '4. Support full-duplex communication\n\n'
          'Di lab, kita pakai switch Cisco Catalyst 2960 yang bisa dikonfigurasi VLAN.';
    }
    return 'Pertanyaan yang bagus! Untuk materi "$pertanyaan", saya sarankan kamu membaca modul terkait di materi pertemuan ya. '
        'Kalau ada istilah atau konsep spesifik yang ingin dijelaskan, tanyakan lagi dengan lebih detail.';
  }
}