import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import 'pertemuan_controller.dart';
import 'pertemuan_detail_view.dart';

class PertemuanView extends GetView<PertemuanController> {
  const PertemuanView({super.key});

  @override
  Widget build(BuildContext context) {
    if (!Get.isRegistered<PertemuanController>()) {
      Get.put(PertemuanController());
    }

    final tt = Theme.of(context).textTheme;

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Obx(() {
          if (controller.sedangMemuat.value) {
            return const Center(
              child: CircularProgressIndicator(color: AppColors.primary));
          }

          return Column(children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 20, 24, 0),
              child: Row(children: [
                Expanded(child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Materi Praktikum', style: tt.headlineMedium),
                    const SizedBox(height: 4),
                    Text('Jaringan Komputer Dasar', style: tt.bodyMedium),
                  ],
                )),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  decoration: BoxDecoration(
                    color: AppColors.accent,
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Row(mainAxisSize: MainAxisSize.min, children: [
                    const Icon(LucideIcons.trophy, size: 13, color: AppColors.primary),
                    const SizedBox(width: 5),
                    Text('3/5 selesai',
                      style: tt.labelSmall?.copyWith(
                        color: AppColors.primary, fontWeight: FontWeight.w700)),
                  ]),
                ),
              ]),
            ),

            const SizedBox(height: 20),
            _buildBannerAktif(tt),
            const SizedBox(height: 20),
            _buildTabSemester(tt),
            const SizedBox(height: 16),

            Expanded(
              child: ListView.builder(
                padding: const EdgeInsets.fromLTRB(24, 0, 24, 80),
                itemCount: controller.daftarPertemuan.length,
                itemBuilder: (ctx, i) =>
                    _pertemuanItem(ctx, controller.daftarPertemuan[i], i, tt),
              ),
            ),
          ]);
        }),
      ),
    );
  }

  Widget _buildBannerAktif(TextTheme tt) {
    final p = controller.pertemuanAktif;
    if (p.isEmpty) return const SizedBox.shrink();
    final progress = (p['progress'] as num?)?.toDouble() ?? 0.0;

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: AppColors.primaryDark,
          borderRadius: BorderRadius.circular(16),
        ),
        child: Row(children: [
          Expanded(child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Text('Lanjutkan',
                  style: tt.labelSmall?.copyWith(
                    color: Colors.white, fontWeight: FontWeight.w700)),
              ),
              const SizedBox(height: 10),
              Text('Pertemuan ${p['nomor_urut'] ?? ''}',
                style: tt.titleMedium?.copyWith(color: Colors.white)),
              const SizedBox(height: 2),
              Text(p['judul'] ?? '',
                style: tt.bodyMedium?.copyWith(
                  color: Colors.white.withValues(alpha: 0.7))),
              const SizedBox(height: 14),
              Row(children: [
                Expanded(child: ClipRRect(
                  borderRadius: BorderRadius.circular(4),
                  child: LinearProgressIndicator(
                    value: progress,
                    backgroundColor: Colors.white.withValues(alpha: 0.2),
                    valueColor: const AlwaysStoppedAnimation(Colors.white),
                    minHeight: 5,
                  ),
                )),
                const SizedBox(width: 10),
                Text('${(progress * 100).toInt()}%',
                  style: tt.labelSmall?.copyWith(
                    color: Colors.white, fontWeight: FontWeight.w700)),
              ]),
            ],
          )),
          const SizedBox(width: 16),
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Icon(LucideIcons.play, size: 20, color: Colors.white),
          ),
        ]),
      ),
    );
  }

  Widget _buildTabSemester(TextTheme tt) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Container(
        padding: const EdgeInsets.all(4),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border),
        ),
        child: Row(children: [
          _tabItem('Semester 1', 0, tt),
          _tabItem('Semester 2', 1, tt),
        ]),
      ),
    );
  }

  Widget _tabItem(String label, int idx, TextTheme tt) {
    return Obx(() {
      final aktif = controller.semesterAktif.value == idx;
      return Expanded(child: GestureDetector(
        onTap: () => controller.gantiSemester(idx),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: aktif ? AppColors.primary : Colors.transparent,
            borderRadius: BorderRadius.circular(12),
          ),
          child: Center(child: Text(label,
            style: tt.labelLarge?.copyWith(
              color: aktif ? Colors.white : AppColors.textSecondary))),
        ),
      ));
    });
  }

  Widget _pertemuanItem(BuildContext context,
      Map<String, dynamic> data, int index, TextTheme tt) {
    final terkunci = data['status'] == 'terkunci';
    final selesai = data['status'] == 'selesai';
    final aktif = data['status'] == 'aktif';
    final progress = (data['progress'] as num?)?.toDouble() ?? 0.0;
    final nomor = (data['nomor_urut'] as int?) ?? index + 1;
    final color = terkunci
        ? AppColors.border
        : AppColors.pertemuanColors[(nomor - 1) % AppColors.pertemuanColors.length];

    return GestureDetector(
      onTap: terkunci ? null : () {
        Navigator.push(context, MaterialPageRoute(
          builder: (_) => PertemuanDetailView(pertemuan: data)));
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: terkunci
              ? AppColors.surface.withValues(alpha: 0.6)
              : AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: aktif ? color : AppColors.border,
            width: aktif ? 2 : 1),
        ),
        child: Row(children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: terkunci
                  ? AppColors.background
                  : color.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Center(child: terkunci
              ? const Icon(LucideIcons.lock, size: 16, color: AppColors.textSecondary)
              : Text('$nomor',
                  style: TextStyle(
                    fontSize: 15, fontWeight: FontWeight.w800, color: color))),
          ),
          const SizedBox(width: 14),

          Expanded(child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(data['judul'] ?? '',
                style: tt.bodyLarge?.copyWith(
                  color: terkunci ? AppColors.textSecondary : AppColors.textPrimary),
                maxLines: 1, overflow: TextOverflow.ellipsis),
              const SizedBox(height: 4),
              Row(children: [
                if ((data['topik'] ?? 0) > 0) ...[
                  Text('${data['topik']} topik', style: tt.labelSmall),
                  const SizedBox(width: 8),
                  Container(
                    width: 3, height: 3,
                    decoration: const BoxDecoration(
                      color: AppColors.textSecondary,
                      shape: BoxShape.circle)),
                  const SizedBox(width: 8),
                ],
                if (selesai)
                  Text('Selesai',
                    style: tt.labelSmall?.copyWith(
                      color: AppColors.success, fontWeight: FontWeight.w700))
                else if (aktif)
                  Row(children: [
                    Container(
                      width: 6, height: 6,
                      decoration: BoxDecoration(
                        color: color, shape: BoxShape.circle)),
                    const SizedBox(width: 4),
                    Text('Sedang dipelajari',
                      style: tt.labelSmall?.copyWith(
                        color: color, fontWeight: FontWeight.w700)),
                  ])
                else if (terkunci)
                  Text('Terkunci', style: tt.labelSmall)
                else
                  Text('Belum dimulai', style: tt.labelSmall),
              ]),
              if (!terkunci && progress > 0) ...[
                const SizedBox(height: 10),
                ClipRRect(
                  borderRadius: BorderRadius.circular(4),
                  child: LinearProgressIndicator(
                    value: progress,
                    backgroundColor: color.withValues(alpha: 0.1),
                    valueColor: AlwaysStoppedAnimation(color),
                    minHeight: 4,
                  ),
                ),
              ],
            ],
          )),

          if (!terkunci)
            const Icon(LucideIcons.chevronRight,
              size: 16, color: AppColors.textSecondary),
        ]),
      ),
    );
  }
}