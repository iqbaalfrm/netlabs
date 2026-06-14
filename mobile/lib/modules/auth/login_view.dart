import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import '../../app/theme/app_colors.dart';
import 'auth_controller.dart';

class LoginView extends GetView<AuthController> {
  const LoginView({super.key});

  @override
  Widget build(BuildContext context) {
    final tt = Theme.of(context).textTheme;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: Scaffold(
        backgroundColor: AppColors.background,
        body: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 60),

                // Logo
                Container(
                  width: 56,
                  height: 56,
                  decoration: BoxDecoration(
                    color: AppColors.primary,
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: const Icon(Icons.hub_rounded, size: 32, color: Colors.white),
                ),

                const SizedBox(height: 32),

                Text('Masuk ke\nNetlabs', style: tt.displayLarge),
                const SizedBox(height: 8),
                Text(
                  'Masukkan NIS dan kata sandi kamu',
                  style: tt.bodyMedium,
                ),

                const SizedBox(height: 40),

                // NIS
                Text('NIS', style: tt.labelLarge),
                const SizedBox(height: 8),
                _buildTextField(
                  context: context,
                  controller: controller.nisController,
                  hint: 'Contoh: 2122100045',
                  icon: Icons.badge_outlined,
                ),

                const SizedBox(height: 20),

                // Password
                Text('Kata Sandi', style: tt.labelLarge),
                const SizedBox(height: 8),
                Obx(() => _buildTextField(
                  context: context,
                  controller: controller.passwordController,
                  hint: '••••••••',
                  icon: Icons.lock_outline_rounded,
                  obscure: controller.passwordTersembunyi.value,
                  suffix: GestureDetector(
                    onTap: controller.togglePassword,
                    child: Padding(
                      padding: const EdgeInsets.all(14),
                      child: Icon(
                        controller.passwordTersembunyi.value
                            ? Icons.visibility_off_outlined
                            : Icons.visibility_outlined,
                        size: 20,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ),
                )),

                const SizedBox(height: 40),

                // Button
                Obx(() => SizedBox(
                  width: double.infinity,
                  height: 56,
                  child: ElevatedButton(
                    onPressed: controller.sedangLoading.value ? null : controller.login,
                    child: controller.sedangLoading.value
                        ? const SizedBox(
                            width: 22,
                            height: 22,
                            child: CircularProgressIndicator(
                              color: Colors.white,
                              strokeWidth: 2.5,
                            ),
                          )
                        : Text('Masuk', style: tt.labelLarge?.copyWith(color: Colors.white)),
                  ),
                )),

                const SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildTextField({
    required BuildContext context,
    required TextEditingController controller,
    required String hint,
    required IconData icon,
    bool obscure = false,
    Widget? suffix,
  }) {
    return TextField(
      controller: controller,
      obscureText: obscure,
      style: Theme.of(context).textTheme.bodyLarge,
      decoration: InputDecoration(
        hintText: hint,
        prefixIcon: Icon(icon, size: 20, color: AppColors.textSecondary),
        suffixIcon: suffix,
      ),
    );
  }
}