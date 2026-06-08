import 'package:dio/dio.dart';
import 'package:get_storage/get_storage.dart';
import '../constants/dummy_data.dart';

// Service API — Modifikasi untuk offline demo menggunakan dummy data terpusat
class ApiService {
  static const String baseUrl = 'https://netlabs-backend-production.up.railway.app';

  // Dio tetap dideklarasikan agar tidak merusak import/inisialisasi jika ada
  static final Dio _dio = Dio(BaseOptions(
    baseUrl: baseUrl,
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 10),
  ));

  // AUTH
  static Future<Map<String, dynamic>> login(String nis, String password) async {
    await Future.delayed(const Duration(milliseconds: 600));
    return {
      'token': 'demo-token-123456',
      'user': {
        'id': '1',
        'nama': 'Muhammad Iqbal',
        'nis': nis,
        'kelas': 'XI TKJ 2',
        'role': 'siswa',
        'sekolah': 'SMK Bhakti Praja Dukuhwaru',
      }
    };
  }

  static Future<Map<String, dynamic>> getMe() async {
    await Future.delayed(const Duration(milliseconds: 300));
    return {
      'id': '1',
      'nama': 'Muhammad Iqbal',
      'nis': '2122100045',
      'kelas': 'XI TKJ 2',
      'role': 'siswa',
      'sekolah': 'SMK Bhakti Praja Dukuhwaru',
    };
  }

  // PERTEMUAN
  static Future<List> getPertemuan() async {
    await Future.delayed(const Duration(milliseconds: 500));
    return [
      ...DummyData.semester1,
      ...DummyData.semester2,
    ];
  }

  static Future<Map<String, dynamic>> getDetailPertemuan(String id) async {
    await Future.delayed(const Duration(milliseconds: 300));
    final all = [...DummyData.semester1, ...DummyData.semester2];
    return all.firstWhere((p) => p['id'] == id, orElse: () => {});
  }

  // TOPIK
  static Future<List> getTopik(String pertemuanId) async {
    await Future.delayed(const Duration(milliseconds: 300));
    final numStr = pertemuanId.replaceAll(RegExp(r'[^0-9]'), '');
    final number = int.tryParse(numStr) ?? 1;
    final topics = {
      1: ['Pengertian Jaringan & Syariah', 'Jenis Jaringan (LAN, MAN, WAN)', 'Topologi Jaringan', 'Perangkat Keras Jaringan'],
      2: ['Pengertian IP Address', 'Kelas IP Address (A, B, C)', 'IP Public vs IP Private', 'Subnetting & Mitigasi Risiko'],
      3: ['Setting IP Manual di Windows', 'Verifikasi Koneksi dengan CMD', 'Troubleshooting Jaringan'],
      4: ['Pengertian VLAN', 'Konfigurasi VLAN di Switch', 'Inter-VLAN Routing', 'Verifikasi VLAN'],
      5: ['Konsep Routing', 'Konfigurasi Static Route', 'Verifikasi Routing Table'],
    };
    final list = topics[number] ?? ['Topik 1', 'Topik 2', 'Topik 3'];
    return list.asMap().entries.map((e) => {
      'id': '${pertemuanId}_t${e.key + 1}',
      'nomor': e.key + 1,
      'judul': e.value,
      'selesai': false,
    }).toList();
  }

  static Future<void> tandaiTopikDibaca(String topikId) async {
    await Future.delayed(const Duration(milliseconds: 100));
  }

  // KUIS
  static Future<List> getSoalKuis(String pertemuanId) async {
    await Future.delayed(const Duration(milliseconds: 400));
    final numStr = pertemuanId.replaceAll(RegExp(r'[^0-9]'), '');
    final number = int.tryParse(numStr) ?? 1;
    final soalMap = {
      1: [
        {'id': 's1_1', 'pertanyaan': 'Apa tujuan utama penggunaan jaringan komputer dalam Lembaga Keuangan Syariah?', 'pilihan_a': 'Memfasilitasi transfer data secara cepat dan aman (amanah)', 'pilihan_b': 'Menghindari pembayaran pajak', 'pilihan_c': 'Membagikan software bajakan', 'pilihan_d': 'Meningkatkan spekulasi (gharar)', 'jawaban': 0},
        {'id': 's1_2', 'pertanyaan': 'Jaringan yang menghubungkan kantor pusat Bank Syariah Nasional dengan kantor cabang disebut?', 'pilihan_a': 'LAN', 'pilihan_b': 'MAN', 'pilihan_c': 'WAN', 'pilihan_d': 'PAN', 'jawaban': 2},
        {'id': 's1_3', 'pertanyaan': 'Topologi jaringan yang paling aman dan stabil untuk menghindari risiko downtime transaksi online adalah?', 'pilihan_a': 'Bus', 'pilihan_b': 'Ring', 'pilihan_c': 'Star', 'pilihan_d': 'Mesh', 'jawaban': 2},
        {'id': 's1_4', 'pertanyaan': 'Perangkat yang digunakan untuk merutekan paket transaksi keuangan dengan aman adalah?', 'pilihan_a': 'Hub', 'pilihan_b': 'Switch', 'pilihan_c': 'Router', 'pilihan_d': 'Repeater', 'jawaban': 2},
        {'id': 's1_5', 'pertanyaan': 'Penggunaan koneksi internet publik untuk transaksi perbankan syariah tanpa enkripsi melanggar prinsip?', 'pilihan_a': 'Saling rida', 'pilihan_b': 'Keadilan', 'pilihan_c': 'Amanah (menjaga rahasia data nasabah)', 'pilihan_d': 'Kemudahan', 'jawaban': 2},
      ],
      2: [
        {'id': 's2_1', 'pertanyaan': 'Panjang alamat IPv4 yang digunakan untuk mengidentifikasi host di jaringan adalah?', 'pilihan_a': '16 bit', 'pilihan_b': '32 bit', 'pilihan_c': '64 bit', 'pilihan_d': '128 bit', 'jawaban': 1},
        {'id': 's2_2', 'pertanyaan': 'IP Address 192.168.10.1 yang sering digunakan di jaringan lokal tergolong kelas?', 'pilihan_a': 'Kelas A', 'pilihan_b': 'Kelas B', 'pilihan_c': 'Kelas C', 'pilihan_d': 'Kelas D', 'jawaban': 2},
        {'id': 's2_3', 'pertanyaan': 'Untuk mitigasi risiko serangan hacker, IP address private manakah yang aman digunakan di dalam jaringan lokal BMT?', 'pilihan_a': '8.8.8.8', 'pilihan_b': '192.168.1.1', 'pilihan_c': '203.0.113.1', 'pilihan_d': '1.1.1.1', 'jawaban': 1},
        {'id': 's2_4', 'pertanyaan': 'Subnet mask default untuk IP kelas C demi efisiensi alokasi host adalah?', 'pilihan_a': '255.0.0.0', 'pilihan_b': '255.255.0.0', 'pilihan_c': '255.255.255.0', 'pilihan_d': '255.255.255.255', 'jawaban': 2},
        {'id': 's2_5', 'pertanyaan': 'Dalam pengelolaan blockchain aset kripto syariah, tujuan dari subnetting adalah?', 'pilihan_a': 'Meningkatkan volatilitas harga', 'pilihan_b': 'Efisiensi dan pembatasan akses broadcast untuk mitigasi risiko keamanan', 'pilihan_c': 'Mempercepat penambangan koin', 'pilihan_d': 'Menghapus data transaksi', 'jawaban': 1},
      ],
      3: [
        {'id': 's3_1', 'pertanyaan': 'Perintah CMD untuk memverifikasi konfigurasi IP client sebelum melakukan transfer dana syariah?', 'pilihan_a': 'ping', 'pilihan_b': 'ipconfig', 'pilihan_c': 'tracert', 'pilihan_d': 'netstat', 'jawaban': 1},
        {'id': 's3_2', 'pertanyaan': 'Perintah pengujian latency untuk mitigasi risiko kegagalan serah terima (taqabud) pada transaksi online?', 'pilihan_a': 'ipconfig', 'pilihan_b': 'ping', 'pilihan_c': 'nslookup', 'pilihan_d': 'arp', 'jawaban': 1},
        {'id': 's3_3', 'pertanyaan': 'Alamat loopback IP 127.0.0.1 digunakan untuk?', 'pilihan_a': 'Menghubungkan ke server luar', 'pilihan_b': 'Broadcast ke seluruh kelas', 'pilihan_c': 'Menguji kartu jaringan lokal (self-test)', 'pilihan_d': 'Gateway eksternal', 'jawaban': 2},
        {'id': 's3_4', 'pertanyaan': 'Jika hasil ping ke server trading kripto menunjukkan RTO (Request Time Out), tindakan pertama adalah?', 'pilihan_a': 'Melakukan transaksi berulang', 'pilihan_b': 'Memeriksa koneksi fisik dan konfigurasi gateway lokal', 'pilihan_c': 'Membeli router baru', 'pilihan_d': 'Mengganti IP Publik', 'jawaban': 1},
        {'id': 's3_5', 'pertanyaan': 'Dalam transaksi syariah online, peran Default Gateway adalah sebagai?', 'pilihan_a': 'Penyimpan data koin', 'pilihan_b': 'Pintu gerbang penghubung ke jaringan luar (internet)', 'pilihan_c': 'Penyaring konten pornografi', 'pilihan_d': 'Server database utama', 'jawaban': 1},
      ],
    };
    return soalMap[number] ?? soalMap[1]!;
  }

  static Future<Map<String, dynamic>> submitKuis(
      String pertemuanId, List<Map<String, String>> jawaban) async {
    await Future.delayed(const Duration(milliseconds: 500));
    final correctAnswers = {
      's1_1': 'a', 's1_2': 'c', 's1_3': 'c', 's1_4': 'c', 's1_5': 'c',
      's2_1': 'b', 's2_2': 'c', 's2_3': 'b', 's2_4': 'c', 's2_5': 'b',
      's3_1': 'b', 's3_2': 'b', 's3_3': 'c', 's3_4': 'b', 's3_5': 'b',
    };
    int correctCount = 0;
    for (var jwb in jawaban) {
      final id = jwb['soal_id'];
      final ans = jwb['jawaban'];
      if (correctAnswers[id] == ans) {
        correctCount++;
      }
    }
    return {
      'jumlah_benar': correctCount,
      'total_soal': jawaban.length,
      'nilai': (correctCount / jawaban.length * 100).round(),
    };
  }

  // NILAI
  static Future<Map<String, dynamic>> getNilaiSaya() async {
    await Future.delayed(const Duration(milliseconds: 300));
    return {
      'nilai_rata_rata': 82,
      'pertemuan_selesai': 3,
      'total_pertemuan': 5,
    };
  }

  // CHAT / RAG
  static Future<Map<String, dynamic>> tanyaAI({
    required String pertanyaan,
    required String pertemuanId,
    List<Map<String, dynamic>> riwayatChat = const [],
  }) async {
    await Future.delayed(const Duration(milliseconds: 1200));
    final jawabanText = DummyData.getBalasanAI(pertanyaan);
    String labelSumber = 'Modul Komputer Jaringan Dasar SMK';
    if (pertanyaan.toLowerCase().contains('syariah') || pertanyaan.toLowerCase().contains('fiqh')) {
      labelSumber = 'Buku Fiqh Muamalah Kontemporer';
    } else if (pertanyaan.toLowerCase().contains('kripto') || pertanyaan.toLowerCase().contains('risiko')) {
      labelSumber = 'Panduan Manajemen Risiko Aset Kripto Bappebti';
    }
    return {
      'jawaban': jawabanText,
      'label_sumber': labelSumber,
    };
  }

  static Future<List> getRiwayatChat(String siswaId) async {
    await Future.delayed(const Duration(milliseconds: 400));
    return [];
  }
}
