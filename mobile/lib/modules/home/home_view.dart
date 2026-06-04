import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import 'home_controller.dart';
import 'widgets/beranda_content.dart';
import '../pertemuan/pertemuan_view.dart';
import '../profil/profil_view.dart';
import '../chat/chat_view.dart';

// Halaman utama dengan Bottom Navigation 4 tab
class HomeView extends GetView<HomeController> {
  const HomeView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Obx(() => IndexedStack(
        index: controller.tabAktif.value,
        children: [
          const BerandaContent(),
          const PertemuanView(),
          const ChatView(),
          const ProfilView(),
        ],
      )),

      // Bottom nav — 4 tab
      bottomNavigationBar: Obx(() => Container(
        decoration: BoxDecoration(
          color: Colors.white,
          border: Border(top: BorderSide(color: Colors.grey.shade200, width: 0.5)),
        ),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 8),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _tab(0, LucideIcons.house, 'Beranda'),
                _tab(1, LucideIcons.bookOpen, 'Materi'),
                _tabAI(),
                _tab(3, LucideIcons.user, 'Profil'),
              ],
            ),
          ),
        ),
      )),
    );
  }

  // Tab biasa
  Widget _tab(int index, IconData icon, String label) {
    final aktif = controller.tabAktif.value == index;
    return GestureDetector(
      onTap: () => controller.gantiTab(index),
      behavior: HitTestBehavior.opaque,
      child: SizedBox(
        width: 70,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 22,
              color: aktif ? AppColors.primary : AppColors.textSecondary),
            const SizedBox(height: 4),
            Text(label,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 11,
                fontWeight: aktif ? FontWeight.w600 : FontWeight.w400,
                color: aktif ? AppColors.primary : AppColors.textSecondary)),
          ],
        ),
      ),
    );
  }

  // Tab AI Chat
  Widget _tabAI() {
    final aktif = controller.tabAktif.value == 2;
    return GestureDetector(
      onTap: () => controller.gantiTab(2),
      behavior: HitTestBehavior.opaque,
      child: SizedBox(
        width: 70,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 38, height: 38,
              decoration: BoxDecoration(
                color: AppColors.primary,
                borderRadius: BorderRadius.circular(10)),
              child: const Icon(LucideIcons.bot, size: 18, color: Colors.white),
            ),
            const SizedBox(height: 4),
            Text('AI Chat',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 11, fontWeight: FontWeight.w600,
                color: aktif ? AppColors.primary : AppColors.textSecondary)),
          ],
        ),
      ),
    );
  }
}
