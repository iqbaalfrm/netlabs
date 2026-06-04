import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../app/theme/app_colors.dart';
import '../../app/routes/app_routes.dart';

// Halaman splash — cek token, arahkan ke halaman yang tepat
class SplashView extends StatefulWidget {
  const SplashView({super.key});

  @override
  State<SplashView> createState() => _SplashViewState();
}

class _SplashViewState extends State<SplashView> {
  @override
  void initState() {
    super.initState();
    _cekLoginDanNavigasi();
  }

  Future<void> _cekLoginDanNavigasi() async {
    await Future.delayed(const Duration(seconds: 2));

    final token = GetStorage().read<String>('token');
    if (token != null && token.isNotEmpty) {
      Get.offAllNamed(Routes.home);
    } else {
      Get.offAllNamed(Routes.login);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.primary,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 64, height: 64,
              decoration: BoxDecoration(
                color: Colors.white.withValues(alpha: 0.2),
                borderRadius: BorderRadius.circular(16)),
              child: const Icon(Icons.hub_rounded, size: 36, color: Colors.white),
            ),
            const SizedBox(height: 16),
            Text('Netlabs', style: GoogleFonts.plusJakartaSans(
              fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white)),
            const SizedBox(height: 6),
            Text('Praktikum Jaringan Komputer', style: GoogleFonts.plusJakartaSans(
              fontSize: 13, color: Colors.white70)),
          ],
        ),
      ),
    );
  }
}
