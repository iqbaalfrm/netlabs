import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';

import '../../app/services/api_service.dart';
import '../../models/siswa.dart';

/// Controller untuk halaman login
class AuthController extends GetxController {
  final nisController = TextEditingController();
  final passwordController = TextEditingController();
  final formKey = GlobalKey<FormState>();

  final _storage = GetStorage();
  final sedangLoading = false.obs;
  final passwordTersembunyi = true.obs;

  void togglePassword() => passwordTersembunyi.toggle();

  String? validateNis(String? value) {
    if (value == null || value.trim().isEmpty) return 'NIS wajib diisi';
    return null;
  }

  String? validatePassword(String? value) {
    if (value == null || value.trim().isEmpty) return 'Kata sandi wajib diisi';
    if (value.length < 4) return 'Kata sandi minimal 4 karakter';
    return null;
  }

  Future<void> login() async {
    if (!formKey.currentState!.validate()) return;

    sedangLoading.value = true;
    try {
      final result = await ApiService.login(
        nisController.text.trim(),
        passwordController.text,
      );

      if (result['success'] == true) {
        final user = Siswa.fromJson(result['user']);
        _storage.write('isLoggedIn', true);
        _storage.write('user', result['user']);
        Get.offAllNamed('/home');
      } else {
        Get.snackbar(
          'Login Gagal',
          result['message'] ?? 'NIS atau password salah',
          backgroundColor: Colors.red.shade50,
          colorText: Colors.red.shade800,
        );
      }
    } finally {
      sedangLoading.value = false;
    }
  }

  @override
  void onClose() {
    nisController.dispose();
    passwordController.dispose();
    super.onClose();
  }
}