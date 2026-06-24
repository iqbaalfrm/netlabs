import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import '../../app/constants/dummy_data.dart';
import '../../app/services/api_service.dart';

class ChatController extends GetxController {
  final inputController = TextEditingController();
  final scrollController = ScrollController();
  var inputTeks = ''.obs;

  @override
  void onInit() {
    super.onInit();
    inputController.addListener(() {
      inputTeks.value = inputController.text;
    });
  }

  var daftarPesan = <Map<String, dynamic>>[
    {
      'dariSiswa': false,
      'tipe': 'teks',
      'teks': 'Halo! Saya AI Tutor Netlabs. Tanya apa saja tentang materi praktikum jaringan komputer.',
    },
  ].obs;

  var aiMenulis = false.obs;
  var isRecording = false.obs;

  String pertemuanId = 'p1';

  String get siswaId {
    final user = GetStorage().read<Map>('user');
    return user?['id']?.toString() ?? 'demo';
  }

  void kirimPesan([String? teksLangsung]) async {
    final teks = teksLangsung ?? inputController.text.trim();
    if (teks.isEmpty) return;

    daftarPesan.add({
      'dariSiswa': true, 
      'tipe': 'teks',
      'teks': teks
    });
    inputController.clear();
    aiMenulis.value = true;
    _scrollKeBawah();

    final riwayat = daftarPesan
        .where((p) => p['teks'] != null)
        .map((p) => {'dari_siswa': p['dariSiswa'], 'teks': p['teks']})
        .toList();

    final hasil = await ApiService.tanyaAI(
      pertanyaan: teks,
      pertemuanId: pertemuanId,
      riwayatChat: riwayat.cast<Map<String, dynamic>>(),
    );

    aiMenulis.value = false;

    if (hasil['success'] == true) {
      daftarPesan.add({
        'dariSiswa': false,
        'tipe': 'teks',
        'teks': hasil['jawaban'] ?? 'Maaf, tidak ada jawaban.',
        'labelSumber': hasil['label_sumber'],
      });
    } else {
      // Fallback ke dummy jika API gagal
      final jawaban = hasil['jawaban'];
      if (jawaban != null && jawaban.isNotEmpty) {
        daftarPesan.add({
          'dariSiswa': false,
          'tipe': 'teks',
          'teks': jawaban
        });
      } else {
        daftarPesan.add({
          'dariSiswa': false,
          'tipe': 'teks',
          'teks': DummyData.getBalasanAI(teks),
        });
      }
    }

    _scrollKeBawah();
  }

  void simulasiUploadFile(String nama, String tipe, String ukuran) async {
    // 1. Tambah file ke chat
    daftarPesan.add({
      'dariSiswa': true,
      'tipe': 'file',
      'teks': nama,
      'namaFile': nama,
      'tipeFile': tipe,
      'ukuranFile': ukuran,
    });
    _scrollKeBawah();

    // 2. AI merespon
    aiMenulis.value = true;
    await Future.delayed(const Duration(seconds: 2));
    aiMenulis.value = false;

    daftarPesan.add({
      'dariSiswa': false,
      'tipe': 'teks',
      'teks': 'Saya telah menerima berkas "$nama" ($ukuran). Berkas ini berisi data praktikum. Ada bagian tertentu yang ingin Anda saya analisis atau tanyakan terkait berkas tersebut?',
    });
    _scrollKeBawah();
  }

  void simulasiRecordAudio() async {
    if (isRecording.value) {
      // Selesai merekam
      isRecording.value = false;
      
      // Tambahkan audio bubble
      daftarPesan.add({
        'dariSiswa': true,
        'tipe': 'audio',
        'durasi': '0:08',
        'isPlaying': false,
      });
      _scrollKeBawah();

      // AI merespon dengan transkripsi & jawaban
      aiMenulis.value = true;
      await Future.delayed(const Duration(seconds: 2));
      aiMenulis.value = false;

      daftarPesan.add({
        'dariSiswa': false,
        'tipe': 'teks',
        'teks': '🎙️ *Hasil Transkripsi Suara: "Bagaimana cara melakukan konfigurasi DHCP Server?"*\n\nBerikut langkah ringkas konfigurasi DHCP Server di Cisco Router:\n1. Aktifkan IP Pool: `ip dhcp pool Latihan`\n2. Atur Network: `network 192.168.10.0 255.255.255.0`\n3. Atur Gateway: `default-router 192.168.10.1`\n4. Atur DNS: `dns-server 8.8.8.8`\n\nApakah langkah tersebut sudah jelas?',
      });
      _scrollKeBawah();
    } else {
      // Mulai merekam
      isRecording.value = true;
      // Simulasi merekam selama 3 detik lalu otomatis simpan jika tidak dihentikan manual
      Future.delayed(const Duration(seconds: 4), () {
        if (isRecording.value) {
          simulasiRecordAudio();
        }
      });
    }
  }

  void togglePlayAudio(int index) {
    if (index >= 0 && index < daftarPesan.length) {
      final status = daftarPesan[index]['isPlaying'] ?? false;
      // Reset play status untuk pesan audio lain
      for (var i = 0; i < daftarPesan.length; i++) {
        if (daftarPesan[i]['tipe'] == 'audio') {
          daftarPesan[i]['isPlaying'] = false;
        }
      }
      daftarPesan[index]['isPlaying'] = !status;
      daftarPesan.refresh();

      // Simulasi selesai main setelah 8 detik
      if (!status) {
        Future.delayed(const Duration(seconds: 8), () {
          if (index < daftarPesan.length && daftarPesan[index]['tipe'] == 'audio') {
            daftarPesan[index]['isPlaying'] = false;
            daftarPesan.refresh();
          }
        });
      }
    }
  }

  void _scrollKeBawah() {
    Future.delayed(const Duration(milliseconds: 100), () {
      if (scrollController.hasClients) {
        scrollController.animateTo(
          scrollController.position.maxScrollExtent,
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeOut,
        );
      }
    });
  }

  @override
  void onClose() {
    inputController.dispose();
    scrollController.dispose();
    super.onClose();
  }
}
