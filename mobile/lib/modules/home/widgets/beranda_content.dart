import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../../app/theme/app_colors.dart';
import '../../../app/constants/dummy_data.dart';
import '../home_controller.dart';

class BerandaContent extends StatelessWidget {
  const BerandaContent({super.key});

  @override
  Widget build(BuildContext context) {
    final tt = Theme.of(context).textTheme;

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: CustomScrollView(
          slivers: [
            SliverToBoxAdapter(child: _buildHeader(tt)),
            const SliverToBoxAdapter(child: SizedBox(height: 24)),
            SliverToBoxAdapter(child: _buildStatRow(tt)),
            const SliverToBoxAdapter(child: SizedBox(height: 24)),
            SliverToBoxAdapter(child: _buildBannerAI(tt)),
            const SliverToBoxAdapter(child: SizedBox(height: 32)),
            SliverToBoxAdapter(child: _buildSectionTitle('Pertemuan', tt, onTap: () {
              Get.find<HomeController>().keMateri();
            })),
            const SliverToBoxAdapter(child: SizedBox(height: 12)),
            SliverToBoxAdapter(child: _buildPertemuanScroll(tt)),
            const SliverToBoxAdapter(child: SizedBox(height: 32)),
            SliverToBoxAdapter(child: _buildSectionTitle('Aktivitas Terakhir', tt)),
            const SliverToBoxAdapter(child: SizedBox(height: 12)),
            SliverToBoxAdapter(child: _buildAktivitas(tt)),
            const SliverToBoxAdapter(child: SizedBox(height: 32)),
            SliverToBoxAdapter(child: _buildSectionTitle('Kuis Tersedia', tt)),
            const SliverToBoxAdapter(child: SizedBox(height: 12)),
            SliverToBoxAdapter(child: _buildKuis(tt)),
            const SliverToBoxAdapter(child: SizedBox(height: 100)),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader(TextTheme tt) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(24, 20, 24, 0),
      child: Row(children: [
        Container(
          width: 44,
          height: 44,
          decoration: BoxDecoration(
            color: AppColors.primary,
            borderRadius: BorderRadius.circular(16),
          ),
          child: Center(
            child: Text(
              DummyData.nama.substring(0, 1),
              style: const TextStyle(
                fontSize: 18, fontWeight: FontWeight.w700, color: Colors.white),
            ),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Halo, ${DummyData.nama.split(' ').first}',
              style: tt.titleMedium),
            const SizedBox(height: 2),
            Text('Kelas ${DummyData.kelas}',
              style: tt.labelSmall),
          ],
        )),
        Container(
          width: 40,
          height: 40,
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.border),
          ),
          child: const Icon(LucideIcons.bell,
            size: 18, color: AppColors.textSecondary),
        ),
      ]),
    );
  }

  Widget _buildStatRow(TextTheme tt) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Row(children: [
        _statCard(
          '${DummyData.pertemuanSelesai}/${DummyData.totalPertemuan}',
          'Pertemuan',
          LucideIcons.bookOpen,
          tt,
        ),
        const SizedBox(width: 12),
        _statCard(
          '${DummyData.nilaiRataRata}',
          'Nilai',
          LucideIcons.star,
          tt,
        ),
        const SizedBox(width: 12),
        _statCard(
          '${DummyData.totalChat}',
          'Chat AI',
          LucideIcons.messageCircle,
          tt,
        ),
      ]),
    );
  }

  Widget _statCard(String angka, String label, IconData icon, TextTheme tt) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border),
        ),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Icon(icon, size: 16, color: AppColors.textSecondary),
          const SizedBox(height: 10),
          Text(angka, style: tt.headlineMedium),
          const SizedBox(height: 2),
          Text(label, style: tt.labelSmall),
        ]),
      ),
    );
  }

  Widget _buildBannerAI(TextTheme tt) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: GestureDetector(
        onTap: () => Get.find<HomeController>().keChat(),
        child: Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: AppColors.primaryDark,
            borderRadius: BorderRadius.circular(16),
          ),
          child: Row(children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: Colors.white.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(16),
              ),
              child: const Icon(LucideIcons.bot,
                color: Colors.white, size: 22),
            ),
            const SizedBox(width: 14),
            Expanded(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('AI Tutor',
                  style: tt.titleMedium?.copyWith(color: Colors.white)),
                const SizedBox(height: 4),
                Text('Tanya apapun soal materi',
                  style: tt.bodyMedium?.copyWith(
                    color: Colors.white.withValues(alpha: 0.6))),
              ],
            )),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
              ),
              child: Text('Tanya',
                style: tt.labelLarge?.copyWith(color: AppColors.primaryDark)),
            ),
          ]),
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String text, TextTheme tt, {VoidCallback? onTap}) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Row(children: [
        Text(text, style: tt.titleMedium),
        const Spacer(),
        if (onTap != null)
          GestureDetector(
            onTap: onTap,
            child: Text('Semua',
              style: tt.labelLarge?.copyWith(color: AppColors.primary)),
          ),
      ]),
    );
  }

  Widget _buildPertemuanScroll(TextTheme tt) {
    final data = [
      (1, 'Pengenalan\nJaringan', 4, 0.75),
      (2, 'Pengalamatan\nIP', 4, 0.5),
      (3, 'Konfigurasi\nWindows', 3, 0.33),
      (4, 'Implementasi\nVLAN', 4, 0.0),
      (5, 'Static\nRouting', 3, 0.0),
    ];

    return SizedBox(
      height: 160,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 24),
        itemCount: data.length,
        itemBuilder: (_, i) {
          final (nomor, judul, topik, progress) = data[i];
          final color = AppColors.pertemuanColors[
              i % AppColors.pertemuanColors.length];
          return _pertemuanCard(nomor, judul, topik, progress, color, tt);
        },
      ),
    );
  }

  Widget _pertemuanCard(int nomor, String judul, int topik,
      double progress, Color accentColor, TextTheme tt) {
    final locked = progress == 0.0 && nomor > 3;

    return Container(
      width: 140,
      margin: const EdgeInsets.only(right: 12),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Stack(children: [
        Positioned(top: 0, left: 0, right: 0,
          child: Container(
            height: 4,
            decoration: BoxDecoration(
              color: locked ? AppColors.border : accentColor,
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(15),
                topRight: Radius.circular(15),
              ),
            ),
          ),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(14, 18, 14, 14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('P-$nomor',
                style: tt.labelSmall?.copyWith(
                  color: locked ? AppColors.textSecondary : AppColors.primary,
                  fontWeight: FontWeight.w700,
                )),
              const SizedBox(height: 6),
              Text(judul,
                style: TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                  height: 1.3,
                  color: locked ? AppColors.textSecondary : AppColors.textPrimary,
                )),
              const Spacer(),
              Row(children: [
                Icon(
                  locked ? LucideIcons.lock : LucideIcons.layoutList,
                  size: 11, color: AppColors.textSecondary),
                const SizedBox(width: 4),
                Text(locked ? 'Terkunci' : '$topik topik',
                  style: tt.labelSmall),
              ]),
              if (!locked && progress > 0) ...[
                const SizedBox(height: 8),
                ClipRRect(
                  borderRadius: BorderRadius.circular(4),
                  child: LinearProgressIndicator(
                    value: progress,
                    backgroundColor: AppColors.border,
                    valueColor: AlwaysStoppedAnimation(accentColor),
                    minHeight: 4,
                  ),
                ),
              ],
            ],
          ),
        ),
      ]),
    );
  }

  Widget _buildAktivitas(TextTheme tt) {
    final items = [
      (LucideIcons.bot, 'Tanya AI: Cara konfigurasi VLAN?', '10 mnt lalu'),
      (LucideIcons.circleCheck, 'Selesai: Kelas IP Address', '1 jam lalu'),
      (LucideIcons.fileQuestion, 'Kuis Pertemuan 1 — Nilai: 85', 'Kemarin'),
    ];

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(children: items.map((item) {
        final (icon, judul, waktu) = item;
        return Container(
          margin: const EdgeInsets.only(bottom: 8),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.border),
          ),
          child: Row(children: [
            Icon(icon, size: 18, color: AppColors.textSecondary),
            const SizedBox(width: 12),
            Expanded(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(judul,
                  style: tt.bodyLarge?.copyWith(fontSize: 13),
                  maxLines: 1, overflow: TextOverflow.ellipsis),
                const SizedBox(height: 2),
                Text(waktu, style: tt.labelSmall),
              ],
            )),
            const Icon(LucideIcons.chevronRight,
              size: 14, color: AppColors.border),
          ]),
        );
      }).toList()),
    );
  }

  Widget _buildKuis(TextTheme tt) {
    final items = [
      ('Pertemuan 3', 'Konfigurasi IP di Windows', 5),
      ('Pertemuan 2', 'Pengalamatan IP', 5),
    ];

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(children: items.map((item) {
        final (pertemuan, judul, soal) = item;
        return Container(
          margin: const EdgeInsets.only(bottom: 10),
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.border),
          ),
          child: Row(children: [
            Expanded(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(pertemuan,
                  style: tt.labelSmall?.copyWith(
                    color: AppColors.primary, fontWeight: FontWeight.w700)),
                const SizedBox(height: 4),
                Text(judul, style: tt.bodyLarge?.copyWith(fontSize: 14)),
                const SizedBox(height: 4),
                Text('$soal soal · ~5 menit', style: tt.labelSmall),
              ],
            )),
            const SizedBox(width: 12),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color: AppColors.primary,
                borderRadius: BorderRadius.circular(16),
              ),
              child: Text('Mulai',
                style: tt.labelLarge?.copyWith(color: Colors.white)),
            ),
          ]),
        );
      }).toList()),
    );
  }
}