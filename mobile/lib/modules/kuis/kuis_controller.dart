import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../app/services/api_service.dart';
import 'hasil_kuis_view.dart';

class KuisController extends GetxController {
  late int nomorPertemuan;
  late String pertemuanId;

  var soalSekarang = 0.obs;
  var jawabanDipilih = (-1).obs;
  final List<int> jawabanSiswa = [];

  final RxList<Map<String, dynamic>> daftarSoal = <Map<String, dynamic>>[].obs;

  var sedangMemuat = true.obs;

  void init(int nomor, String idPertemuan) {
    nomorPertemuan = nomor;
    pertemuanId = idPertemuan;
    _muatSoal();
  }

  Future<void> _muatSoal() async {
    sedangMemuat.value = true;

    // Check if already submitted
    final hasilCek = await ApiService.cekHasilKuis(pertemuanId);
    if (hasilCek['sudah_dikerjakan'] == true) {
      sedangMemuat.value = false;
      final data = hasilCek['data'];
      if (data != null) {
        Get.snackbar('Info', 'Kuis ini sudah pernah dikerjakan',
            backgroundColor: Colors.blue.shade100, colorText: Colors.blue.shade800);
      }
    }

    final data = await ApiService.getSoalKuis(pertemuanId);
    if (data.isNotEmpty) {
      daftarSoal.assignAll(data.cast<Map<String, dynamic>>());
    } else {
      daftarSoal.assignAll(_getSoalDummy(nomorPertemuan));
    }
    sedangMemuat.value = false;
  }

  void pilihJawaban(int index) => jawabanDipilih.value = index;

  void lanjut(BuildContext context) async {
    final huruf = ['a', 'b', 'c', 'd'];
    jawabanSiswa.add(jawabanDipilih.value);

    if (soalSekarang.value < daftarSoal.length - 1) {
      soalSekarang.value++;
      jawabanDipilih.value = -1;
    } else {
      int benarLokal = 0;
      final List<Map<String, String>> jawabanKirim = [];
      for (int i = 0; i < daftarSoal.length; i++) {
        final soal = daftarSoal[i];
        final indexPilih = jawabanSiswa[i];
        final jawabanHuruf = indexPilih >= 0 ? huruf[indexPilih] : 'a';
        jawabanKirim.add({'soal_id': soal['id']?.toString() ?? '', 'jawaban': jawabanHuruf});

        final jwbBenar = soal['jawaban'] as int? ?? 0;
        if (indexPilih == jwbBenar) benarLokal++;
      }

      final hasil = await ApiService.submitKuis(pertemuanId, jawabanKirim);

      if (!context.mounted) return;

      if (hasil['success'] == true) {
        Navigator.pushReplacement(context, MaterialPageRoute(
          builder: (_) => HasilKuisView(
            jumlahBenar: (hasil['jumlah_benar'] as num).toInt(),
            totalSoal: (hasil['total_soal'] as num).toInt(),
            nomorPertemuan: nomorPertemuan,
          ),
        ));
      } else {
        // Fallback or already submitted
        final msg = hasil['message'] ?? '';
        if (msg.contains('sudah pernah')) {
          Get.snackbar('Info', msg,
              backgroundColor: Colors.orange.shade100, colorText: Colors.orange.shade800);
        }
        Navigator.pushReplacement(context, MaterialPageRoute(
          builder: (_) => HasilKuisView(
            jumlahBenar: benarLokal,
            totalSoal: daftarSoal.length,
            nomorPertemuan: nomorPertemuan,
          ),
        ));
      }
    }
  }

  Map<String, dynamic> get soalAktif {
    if (daftarSoal.isEmpty || soalSekarang.value >= daftarSoal.length) {
      return {};
    }
    return daftarSoal[soalSekarang.value];
  }

  List<Map<String, dynamic>> _getSoalDummy(int nomor) {
    final soalMap = {
      1: [
        {'id': 's1_1', 'pertanyaan': 'Apa yang dimaksud dengan jaringan komputer?', 'pilihan_a': 'Sekumpulan komputer yang saling terhubung', 'pilihan_b': 'Satu komputer dengan banyak monitor', 'pilihan_c': 'Perangkat lunak untuk mengelola file', 'pilihan_d': 'Sistem operasi berbasis jaringan', 'jawaban': 0},
        {'id': 's1_2', 'pertanyaan': 'Jaringan yang mencakup area satu kota disebut?', 'pilihan_a': 'LAN', 'pilihan_b': 'MAN', 'pilihan_c': 'WAN', 'pilihan_d': 'PAN', 'jawaban': 1},
        {'id': 's1_3', 'pertanyaan': 'Topologi yang semua perangkat terhubung ke satu pusat?', 'pilihan_a': 'Bus', 'pilihan_b': 'Ring', 'pilihan_c': 'Star', 'pilihan_d': 'Mesh', 'jawaban': 2},
        {'id': 's1_4', 'pertanyaan': 'Perangkat yang menghubungkan dua jaringan berbeda?', 'pilihan_a': 'Hub', 'pilihan_b': 'Switch', 'pilihan_c': 'Router', 'pilihan_d': 'Repeater', 'jawaban': 2},
        {'id': 's1_5', 'pertanyaan': 'Contoh jaringan WAN yang paling umum?', 'pilihan_a': 'WiFi rumah', 'pilihan_b': 'LAN sekolah', 'pilihan_c': 'Internet', 'pilihan_d': 'Bluetooth', 'jawaban': 2},
      ],
      2: [
        {'id': 's2_1', 'pertanyaan': 'Berapa bit panjang alamat IPv4?', 'pilihan_a': '16 bit', 'pilihan_b': '32 bit', 'pilihan_c': '64 bit', 'pilihan_d': '128 bit', 'jawaban': 1},
        {'id': 's2_2', 'pertanyaan': 'IP 192.168.1.1 termasuk kelas?', 'pilihan_a': 'Kelas A', 'pilihan_b': 'Kelas B', 'pilihan_c': 'Kelas C', 'pilihan_d': 'Kelas D', 'jawaban': 2},
        {'id': 's2_3', 'pertanyaan': 'Manakah yang termasuk IP Private?', 'pilihan_a': '8.8.8.8', 'pilihan_b': '192.168.1.1', 'pilihan_c': '203.0.113.1', 'pilihan_d': '1.1.1.1', 'jawaban': 1},
        {'id': 's2_4', 'pertanyaan': 'Subnet mask default untuk kelas C?', 'pilihan_a': '255.0.0.0', 'pilihan_b': '255.255.0.0', 'pilihan_c': '255.255.255.0', 'pilihan_d': '255.255.255.255', 'jawaban': 2},
        {'id': 's2_5', 'pertanyaan': 'Tujuan utama subnetting?', 'pilihan_a': 'Mempercepat internet', 'pilihan_b': 'Efisiensi penggunaan IP', 'pilihan_c': 'Menambah bandwidth', 'pilihan_d': 'Mengenkripsi data', 'jawaban': 1},
      ],
      3: [
        {'id': 's3_1', 'pertanyaan': 'Perintah CMD untuk melihat konfigurasi IP?', 'pilihan_a': 'ping', 'pilihan_b': 'ipconfig', 'pilihan_c': 'tracert', 'pilihan_d': 'netstat', 'jawaban': 1},
        {'id': 's3_2', 'pertanyaan': 'Perintah untuk menguji koneksi ke perangkat lain?', 'pilihan_a': 'ipconfig', 'pilihan_b': 'ping', 'pilihan_c': 'nslookup', 'pilihan_d': 'arp', 'jawaban': 1},
        {'id': 's3_3', 'pertanyaan': 'IP 127.0.0.1 disebut juga?', 'pilihan_a': 'Gateway', 'pilihan_b': 'Broadcast', 'pilihan_c': 'Loopback', 'pilihan_d': 'Subnet', 'jawaban': 2},
        {'id': 's3_4', 'pertanyaan': 'Jika ping ke gateway gagal, masalah ada di?', 'pilihan_a': 'DNS server', 'pilihan_b': 'Koneksi lokal', 'pilihan_c': 'Website tujuan', 'pilihan_d': 'Browser', 'jawaban': 1},
        {'id': 's3_5', 'pertanyaan': 'Default Gateway berfungsi sebagai?', 'pilihan_a': 'DNS resolver', 'pilihan_b': 'Pintu keluar ke jaringan lain', 'pilihan_c': 'Firewall', 'pilihan_d': 'DHCP server', 'jawaban': 1},
      ],
    };
    return soalMap[nomor] ?? soalMap[1]!;
  }
}
