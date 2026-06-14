import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import '../../app/constants/dummy_data.dart';
import '../../app/services/api_service.dart';

class ChatController extends GetxController {
  final inputController = TextEditingController();
  final scrollController = ScrollController();

  var daftarPesan = <Map<String, dynamic>>[
    {
      'dariSiswa': false,
      'teks': 'Halo! Saya AI Tutor Netlabs. Tanya apa saja tentang materi praktikum jaringan komputer.',
    },
  ].obs;

  var aiMenulis = false.obs;

  String pertemuanId = 'p1';

  String get siswaId {
    final user = GetStorage().read<Map>('user');
    return user?['id']?.toString() ?? 'demo';
  }

  void kirimPesan([String? teksLangsung]) async {
    final teks = teksLangsung ?? inputController.text.trim();
    if (teks.isEmpty) return;

    daftarPesan.add({'dariSiswa': true, 'teks': teks});
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
        'teks': hasil['jawaban'] ?? 'Maaf, tidak ada jawaban.',
        'labelSumber': hasil['label_sumber'],
      });
    } else {
      // Fallback ke dummy jika API gagal
      final jawaban = hasil['jawaban'];
      if (jawaban != null && jawaban.isNotEmpty) {
        daftarPesan.add({'dariSiswa': false, 'teks': jawaban});
      } else {
        daftarPesan.add({
          'dariSiswa': false,
          'teks': DummyData.getBalasanAI(teks),
        });
      }
    }

    _scrollKeBawah();
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
