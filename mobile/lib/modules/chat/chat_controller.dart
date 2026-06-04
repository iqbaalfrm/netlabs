import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:dio/dio.dart';
import '../../app/constants/dummy_data.dart';
import '../../app/services/api_service.dart';

// Controller untuk halaman AI Chat
class ChatController extends GetxController {
  final inputController = TextEditingController();
  final scrollController = ScrollController();

  // Daftar pesan chat
  var daftarPesan = <Map<String, dynamic>>[
    {
      'dariSiswa': false,
      'teks': 'Halo! Saya AI Tutor Netlabs. Tanya apa saja tentang materi praktikum jaringan komputer.',
    },
  ].obs;

  // Status AI sedang mengetik
  var aiMenulis = false.obs;

  // ID pertemuan konteks saat ini (null = umum)
  String pertemuanId = 'p1';

  // Ambil user dari storage untuk keperluan API
  String get siswaId {
    final user = GetStorage().read<Map>('user');
    return user?['id']?.toString() ?? 'demo';
  }

  // Kirim pesan
  void kirimPesan([String? teksLangsung]) async {
    final teks = teksLangsung ?? inputController.text.trim();
    if (teks.isEmpty) return;

    // Tambah pesan siswa ke daftar
    daftarPesan.add({'dariSiswa': true, 'teks': teks});
    inputController.clear();
    aiMenulis.value = true;
    _scrollKeBawah();

    // Ambil 5 pesan terakhir untuk konteks riwayat
    final riwayat = daftarPesan
        .take(daftarPesan.length)
        .map((p) => {'dari_siswa': p['dariSiswa'], 'teks': p['teks']})
        .toList();

    try {
      // Coba pakai API backend (RAG)
      final hasil = await ApiService.tanyaAI(
        pertanyaan: teks,
        pertemuanId: pertemuanId,
        riwayatChat: riwayat.cast<Map<String, dynamic>>(),
      );
      aiMenulis.value = false;
      daftarPesan.add({
        'dariSiswa': false,
        'teks': hasil['jawaban'] ?? 'Maaf, tidak ada jawaban.',
        'labelSumber': hasil['label_sumber'],
      });
    } on DioException {
      // Fallback: pakai dummy response lokal
      await Future.delayed(const Duration(milliseconds: 1500));
      aiMenulis.value = false;
      daftarPesan.add({
        'dariSiswa': false,
        'teks': DummyData.getBalasanAI(teks),
      });
    }

    _scrollKeBawah();
  }

  // Scroll ke bawah otomatis
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
