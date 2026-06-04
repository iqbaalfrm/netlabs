import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import 'pertemuan_controller.dart';
import 'pertemuan_detail_view.dart';

// Halaman daftar pertemuan — GetView + PertemuanController
class PertemuanView extends GetView<PertemuanController> {
  const PertemuanView({super.key});

  @override
  Widget build(BuildContext context) {
    if (!Get.isRegistered<PertemuanController>()) {
      Get.put(PertemuanController());
    }

    return Scaffold(
      backgroundColor: AppColors.bgLight,
      body: SafeArea(
        child: Obx(() {
          // Loading state
          if (controller.sedangMemuat.value) {
            return const Center(child: CircularProgressIndicator(color: AppColors.primary));
          }

          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 16),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Text('Materi Praktikum',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 20, fontWeight: FontWeight.bold, color: AppColors.textPrimary)),
              ),
              const SizedBox(height: 16),
              _buildBannerAktif(),
              const SizedBox(height: 16),
              _buildTabSemester(),
              const SizedBox(height: 12),
              Expanded(
                child: ListView.builder(
                  padding: const EdgeInsets.fromLTRB(20, 0, 20, 80),
                  itemCount: controller.daftarPertemuan.length,
                  itemBuilder: (ctx, i) =>
                      _pertemuanItem(ctx, controller.daftarPertemuan[i]),
                ),
              ),
            ],
          );
        }),
      ),
    );
  }

  Widget _buildBannerAktif() {
    final p = controller.pertemuanAktif;
    if (p.isEmpty) return const SizedBox.shrink();
    final progress = (p['progress'] as num?)?.toDouble() ?? 0.0;

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: AppColors.primary, borderRadius: BorderRadius.circular(14)),
        child: Row(children: [
          Expanded(
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text('Lanjutkan', style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.white70)),
              const SizedBox(height: 4),
              Text('Pertemuan ${p['nomor_urut'] ?? ''}',
                style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
              Text(p['judul'] ?? '', style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.white70)),
              const SizedBox(height: 10),
              Row(children: [
                Expanded(child: ClipRRect(
                  borderRadius: BorderRadius.circular(3),
                  child: LinearProgressIndicator(value: progress,
                    backgroundColor: Colors.white24, color: Colors.white, minHeight: 4))),
                const SizedBox(width: 10),
                Text('${(progress * 100).toInt()}%',
                  style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.white)),
              ]),
            ]),
          ),
          const SizedBox(width: 12),
          Container(width: 40, height: 40,
            decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(10)),
            child: const Icon(LucideIcons.play, size: 18, color: Colors.white)),
        ]),
      ),
    );
  }

  Widget _buildTabSemester() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Container(
        padding: const EdgeInsets.all(4),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(10),
          border: Border.all(color: AppColors.border)),
        child: Row(children: [
          _tabItem('Semester 1', 0),
          _tabItem('Semester 2', 1),
        ]),
      ),
    );
  }

  Widget _tabItem(String label, int idx) {
    return Obx(() {
      final aktif = controller.semesterAktif.value == idx;
      return Expanded(
        child: GestureDetector(
          onTap: () => controller.gantiSemester(idx),
          child: Container(
            padding: const EdgeInsets.symmetric(vertical: 10),
            decoration: BoxDecoration(
              color: aktif ? AppColors.primary : Colors.transparent,
              borderRadius: BorderRadius.circular(8)),
            child: Center(child: Text(label,
              style: GoogleFonts.plusJakartaSans(fontSize: 13, fontWeight: FontWeight.w600,
                color: aktif ? Colors.white : AppColors.textSecondary))),
          ),
        ),
      );
    });
  }

  Widget _pertemuanItem(BuildContext context, Map<String, dynamic> data) {
    final terkunci = data['status'] == 'terkunci';
    final selesai = data['status'] == 'selesai';
    final aktif = data['status'] == 'aktif';
    final progress = (data['progress'] as num?)?.toDouble() ?? 0.0;

    return GestureDetector(
      onTap: terkunci ? null : () {
        Navigator.push(context, MaterialPageRoute(
          builder: (_) => PertemuanDetailView(pertemuan: data)));
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        decoration: BoxDecoration(
          color: terkunci ? Colors.white.withValues(alpha: 0.6) : Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: aktif ? AppColors.primary : AppColors.border, width: aktif ? 1.5 : 1)),
        child: Column(children: [
          // Banner atas
          Container(
            height: 48, width: double.infinity,
            decoration: BoxDecoration(
              color: terkunci ? AppColors.border.withValues(alpha: 0.5) : AppColors.primary.withValues(alpha: 0.06),
              borderRadius: const BorderRadius.only(topLeft: Radius.circular(13), topRight: Radius.circular(13))),
            child: Row(children: [
              const SizedBox(width: 14),
              Icon(terkunci ? LucideIcons.lock : selesai ? LucideIcons.circleCheck : LucideIcons.bookOpen,
                size: 16, color: terkunci ? AppColors.textSecondary : AppColors.primary),
              const SizedBox(width: 8),
              Text('Pertemuan ${data['nomor_urut'] ?? ''}',
                style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.w600,
                  color: terkunci ? AppColors.textSecondary : AppColors.primary)),
              const Spacer(),
              if (selesai) Padding(padding: const EdgeInsets.only(right: 14),
                child: Text('Selesai ✓', style: GoogleFonts.plusJakartaSans(
                  fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.primary))),
              if (aktif) Padding(padding: const EdgeInsets.only(right: 14),
                child: Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(color: AppColors.primary, borderRadius: BorderRadius.circular(6)),
                  child: Text('Aktif', style: GoogleFonts.plusJakartaSans(
                    fontSize: 10, fontWeight: FontWeight.w600, color: Colors.white)))),
            ]),
          ),
          // Konten
          Padding(padding: const EdgeInsets.all(14), child: Row(children: [
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(data['judul'] ?? '', style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.w600,
                color: terkunci ? AppColors.textSecondary : AppColors.textPrimary)),
              const SizedBox(height: 6),
              Row(children: [
                if ((data['topik'] ?? 0) > 0) ...[
                  Icon(LucideIcons.fileText, size: 12, color: AppColors.textSecondary), const SizedBox(width: 4),
                  Text('${data['topik']} topik', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: AppColors.textSecondary)),
                  const SizedBox(width: 12),
                ],
                if (!terkunci && (data['topik'] ?? 0) > 0) ...[
                  Icon(LucideIcons.clock, size: 12, color: AppColors.textSecondary), const SizedBox(width: 4),
                  Text('~${data['topik'] * 10} menit', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: AppColors.textSecondary)),
                ],
              ]),
              if (!terkunci && progress > 0) ...[
                const SizedBox(height: 10),
                ClipRRect(borderRadius: BorderRadius.circular(3),
                  child: LinearProgressIndicator(value: progress,
                    backgroundColor: AppColors.border, color: AppColors.primary, minHeight: 3)),
              ],
            ])),
            if (!terkunci) Icon(LucideIcons.chevronRight, size: 16, color: AppColors.textSecondary),
          ])),
        ]),
      ),
    );
  }
}
