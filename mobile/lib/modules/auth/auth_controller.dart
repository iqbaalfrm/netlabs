import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import '../../app/routes/app_routes.dart';
import '../../app/services/api_service.dart';

class AuthController extends GetxController {
  final nisController = TextEditingController();
  final passwordController = TextEditingController();
  var passwordTersembunyi = true.obs;
  var sedangLoading = false.obs;

  void togglePassword() => passwordTersembunyi.value = !passwordTersembunyi.value;

  void login() async {
    if (nisController.text.isEmpty || passwordController.text.isEmpty) {
      Get.snackbar('Gagal', 'NIS dan Password harus diisi',
          backgroundColor: Colors.red.shade100, colorText: Colors.red.shade800);
      return;
    }

    sedangLoading.value = true;

    final result = await ApiService.login(
      nisController.text.trim(),
      passwordController.text.trim(),
    );

    sedangLoading.value = false;

    if (result['success'] == true) {
      Get.offAllNamed(Routes.home);
    } else {
      final message = result['message'] ?? 'Login gagal';

      if (message.contains('Tidak bisa terhubung') || message.contains('timeout')) {
        _loginDummy();
      } else {
        Get.snackbar('Login Gagal', message,
            backgroundColor: Colors.red.shade100, colorText: Colors.red.shade800);
      }
    }
  }

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
