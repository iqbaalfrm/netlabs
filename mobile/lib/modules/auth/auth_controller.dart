import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:dio/dio.dart';
import '../../app/routes/app_routes.dart';
import '../../app/services/api_service.dart';

// Controller untuk halaman login
class AuthController extends GetxController {
  final nisController = TextEditingController();
  final passwordController = TextEditingController();
  var passwordTersembunyi = true.obs;
  var sedangLoading = false.obs;

  void togglePassword() => passwordTersembunyi.value = !passwordTersembunyi.value;

  // Fungsi login — pakai API backend, fallback ke dummy jika gagal
  void login() async {
    if (nisController.text.isEmpty || passwordController.text.isEmpty) {
      Get.snackbar('Gagal', 'NIS dan Password harus diisi',
          backgroundColor: Colors.red.shade100, colorText: Colors.red.shade800);
      return;
    }

    sedangLoading.value = true;

    try {
      // Coba API backend
      final result = await ApiService.login(
        nisController.text.trim(),
        passwordController.text.trim(),
      );

      // Simpan token dan data user
      final storage = GetStorage();
      storage.write('token', result['token']);
      storage.write('user', result['user']);

      sedangLoading.value = false;
      Get.offAllNamed(Routes.home);
    } on DioException catch (e) {
      sedangLoading.value = false;

      // Jika server tidak bisa dijangkau, pakai login dummy
      final isOffline = e.type == DioExceptionType.connectionTimeout ||
          e.type == DioExceptionType.receiveTimeout ||
          e.type == DioExceptionType.unknown;

      if (isOffline) {
        _loginDummy();
      } else {
        final pesan = e.response?.data?['detail'] ?? 'Login gagal';
        Get.snackbar('Login Gagal', pesan,
            backgroundColor: Colors.red.shade100, colorText: Colors.red.shade800);
      }
    }
  }

  // Login dummy saat backend tidak tersedia (untuk demo)
  void _loginDummy() {
    final storage = GetStorage();
    storage.write('token', 'demo-token');
    storage.write('user', {
      'id': '1',
      'nama': 'Iqbal',
      'nis': '2122100045',
      'kelas': 'XI TKJ 2',
      'role': 'siswa',
    });
    Get.offAllNamed(Routes.home);
  }

  @override
  void onClose() {
    nisController.dispose();
    passwordController.dispose();
    super.onClose();
  }
}
