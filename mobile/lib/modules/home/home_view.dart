import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import 'home_controller.dart';
import 'widgets/beranda_content.dart';
import '../pertemuan/pertemuan_view.dart';
import '../profil/profil_view.dart';
import '../chat/chat_view.dart';

class HomeView extends GetView<HomeController> {
  const HomeView({super.key});

  @override
  Widget build(BuildContext context) {
    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: Scaffold(
        backgroundColor: AppColors.background,
        body: Obx(() => IndexedStack(
          index: controller.tabAktif.value,
          children: const [
            BerandaContent(),
            PertemuanView(),
            ChatView(),
            ProfilView(),
          ],
        )),
        bottomNavigationBar: _buildBottomNav(),
      ),
    );
  }

  Widget _buildBottomNav() {
    return Obx(() => Container(
      decoration: const BoxDecoration(
        color: AppColors.surface,
        border: Border(
          top: BorderSide(color: AppColors.border, width: 1),
        ),
      ),
      child: SafeArea(
        top: false,
        child: SizedBox(
          height: 64,
          child: Row(children: [
            _navItem(0, LucideIcons.house, 'Beranda'),
            _navItem(1, LucideIcons.bookOpen, 'Materi'),
            _navItemAI(),
            _navItem(3, LucideIcons.user, 'Profil'),
          ]),
        ),
      ),
    ));
  }

  Widget _navItem(int index, IconData icon, String label) {
    final aktif = controller.tabAktif.value == index;
    return Expanded(
      child: GestureDetector(
        onTap: () => controller.gantiTab(index),
        behavior: HitTestBehavior.opaque,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon,
              size: 22,
              color: aktif ? AppColors.primary : AppColors.textSecondary),
            const SizedBox(height: 4),
            Text(label,
              style: TextStyle(
                fontSize: 11,
                fontWeight: aktif ? FontWeight.w700 : FontWeight.w400,
                color: aktif ? AppColors.primary : AppColors.textSecondary,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _navItemAI() {
    final aktif = controller.tabAktif.value == 2;
    return Expanded(
      child: GestureDetector(
        onTap: () => controller.gantiTab(2),
        behavior: HitTestBehavior.opaque,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
              decoration: BoxDecoration(
                color: aktif ? AppColors.primary : AppColors.accent,
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(LucideIcons.bot,
                    size: 15,
                    color: aktif ? Colors.white : AppColors.primary),
                  const SizedBox(width: 4),
                  Text('AI',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w700,
                      color: aktif ? Colors.white : AppColors.primary,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}