import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import '../../app/routes/app_routes.dart';

class ProfilView extends StatelessWidget {
  const ProfilView({super.key});

  Map<String, dynamic> get user {
    return GetStorage().read<Map<String, dynamic>>('user') ?? {
      'nama': 'Muhammad Iqbal',
      'nis': '2122100045',
      'kelas': 'XI TKJ 2',
      'sekolah': 'SMK',
    };
  }

  @override
  Widget build(BuildContext context) {
    final tt = Theme.of(context).textTheme;
    final nama = user['nama'] as String? ?? 'Siswa';
    final inisial = nama.substring(0, 1).toUpperCase();

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(children: [
            _buildHeader(tt, inisial, nama),
            const SizedBox(height: 24),
            _buildStatRow(tt),
            const SizedBox(height: 24),
            _buildInfoSection(tt),
            const SizedBox(height: 16),
            _buildMenuSection(context, tt),
            const SizedBox(height: 24),
            _buildLogoutButton(context, tt),
            const SizedBox(height: 100),
          ]),
        ),
      ),
    );
  }

  Widget _buildHeader(TextTheme tt, String inisial, String nama) {
    return Row(
      children: [
        Container(
          width: 60, height: 60,
          decoration: BoxDecoration(
            color: AppColors.primary,
            borderRadius: BorderRadius.circular(16),
          ),
          child: Center(
            child: Text(inisial,
              style: const TextStyle(
                fontSize: 24, fontWeight: FontWeight.w800, color: Colors.white)),
          ),
        ),
        const SizedBox(width: 16),
        Expanded(child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(nama, style: tt.headlineMedium),
            const SizedBox(height: 4),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: AppColors.accent,
                borderRadius: BorderRadius.circular(16),
              ),
              child: Text(user['kelas'] ?? '',
                style: tt.labelSmall?.copyWith(
                  color: AppColors.primary, fontWeight: FontWeight.w700)),
            ),
          ],
        )),
      ],
    );
  }

  Widget _buildStatRow(TextTheme tt) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(children: [
        _statItem('3/5', 'Pertemuan', AppColors.primary, tt),
        _divider(),
        _statItem('82', 'Nilai', AppColors.textPrimary, tt),
        _divider(),
        _statItem('24', 'Chat AI', AppColors.textPrimary, tt),
      ]),
    );
  }

  Widget _statItem(String angka, String label, Color color, TextTheme tt) {
    return Expanded(child: Column(children: [
      Text(angka, style: tt.headlineMedium?.copyWith(color: color)),
      const SizedBox(height: 2),
      Text(label, style: tt.labelSmall),
    ]));
  }

  Widget _divider() {
    return Container(width: 1, height: 36, color: AppColors.border);
  }

  Widget _buildInfoSection(TextTheme tt) {
    return _card('Informasi Akun', [
      _row(LucideIcons.hash, 'NIS', user['nis'] ?? '-', AppColors.primary, tt),
      _row(LucideIcons.graduationCap, 'Kelas', user['kelas'] ?? '-', AppColors.primary, tt),
      _row(LucideIcons.building2, 'Sekolah', user['sekolah'] ?? 'SMK', AppColors.primary, tt),
    ], tt);
  }

  Widget _buildMenuSection(BuildContext context, TextTheme tt) {
    return _card('Menu', [
      _menuTile(LucideIcons.clipboardList, 'Riwayat Kuis', tt, () {}),
      _menuTile(LucideIcons.bell, 'Notifikasi', tt, () {}),
      _menuTile(LucideIcons.info, 'Tentang Netlabs', tt, () => _showTentang(context)),
    ], tt);
  }

  Widget _card(String title, List<Widget> items, TextTheme tt) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.only(left: 4, bottom: 10),
          child: Text(title, style: tt.labelLarge?.copyWith(color: AppColors.textSecondary)),
        ),
        Container(
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(children: items.asMap().entries.map((e) {
            final isLast = e.key == items.length - 1;
            return Column(children: [
              e.value,
              if (!isLast)
                const Divider(height: 1, indent: 56, color: AppColors.border),
            ]);
          }).toList()),
        ),
      ],
    );
  }

  Widget _row(IconData icon, String label, String value, Color color, TextTheme tt) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      child: Row(children: [
        Container(
          width: 34, height: 34,
          decoration: BoxDecoration(
            color: color.withValues(alpha: 0.1),
            borderRadius: BorderRadius.circular(16),
          ),
          child: Icon(icon, size: 16, color: color),
        ),
        const SizedBox(width: 12),
        Text(label, style: tt.bodyMedium),
        const Spacer(),
        Text(value, style: tt.bodyLarge),
      ]),
    );
  }

  Widget _menuTile(IconData icon, String label, TextTheme tt, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      behavior: HitTestBehavior.opaque,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        child: Row(children: [
          Container(
            width: 34, height: 34,
            decoration: BoxDecoration(
              color: AppColors.primary.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Icon(icon, size: 16, color: AppColors.primary),
          ),
          const SizedBox(width: 12),
          Expanded(child: Text(label, style: tt.bodyLarge?.copyWith(fontSize: 14))),
          const Icon(LucideIcons.chevronRight, size: 16, color: AppColors.textSecondary),
        ]),
      ),
    );
  }

  Widget _buildLogoutButton(BuildContext context, TextTheme tt) {
    return SizedBox(
      width: double.infinity,
      height: 50,
      child: OutlinedButton(
        onPressed: () => _konfirmasiKeluar(context),
        style: OutlinedButton.styleFrom(
          foregroundColor: AppColors.error,
          side: const BorderSide(color: AppColors.error),
        ),
        child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [
          const Icon(LucideIcons.logOut, size: 16),
          const SizedBox(width: 8),
          Text('Keluar', style: tt.labelLarge?.copyWith(color: AppColors.error)),
        ]),
      ),
    );
  }

  void _konfirmasiKeluar(BuildContext context) {
    final tt = Theme.of(context).textTheme;
    showModalBottomSheet(
      context: context,
      backgroundColor: AppColors.surface,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.fromLTRB(24, 16, 24, 36),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(width: 36, height: 4,
            decoration: BoxDecoration(
              color: AppColors.border,
              borderRadius: BorderRadius.circular(2))),
          const SizedBox(height: 24),
          Container(width: 52, height: 52,
            decoration: const BoxDecoration(
              color: Color(0xFFFEF2F2), shape: BoxShape.circle),
            child: const Icon(LucideIcons.logOut,
              size: 22, color: AppColors.error)),
          const SizedBox(height: 14),
          Text('Keluar dari akun?', style: tt.titleMedium),
          const SizedBox(height: 6),
          Text('Kamu perlu login lagi untuk mengakses aplikasi.',
            style: tt.bodyMedium, textAlign: TextAlign.center),
          const SizedBox(height: 24),
          Row(children: [
            Expanded(child: SizedBox(
              height: 48,
              child: OutlinedButton(
                onPressed: () => Navigator.pop(ctx),
                child: Text('Batal', style: tt.labelLarge),
              ),
            )),
            const SizedBox(width: 12),
            Expanded(child: SizedBox(
              height: 48,
              child: ElevatedButton(
                onPressed: () {
                  Navigator.pop(ctx);
                  GetStorage().erase();
                  Get.offAllNamed(Routes.login);
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.error),
                child: Text('Keluar',
                  style: tt.labelLarge?.copyWith(color: Colors.white)),
              ),
            )),
          ]),
        ]),
      ),
    );
  }

  void _showTentang(BuildContext context) {
    final tt = Theme.of(context).textTheme;
    showDialog(
      context: context,
      builder: (ctx) => Dialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16)),
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            Container(
              width: 52, height: 52,
              decoration: BoxDecoration(
                color: AppColors.primary,
                borderRadius: BorderRadius.circular(16)),
              child: const Icon(Icons.hub_rounded,
                color: Colors.white, size: 26)),
            const SizedBox(height: 14),
            Text('Netlabs v1.0.0', style: tt.titleMedium),
            const SizedBox(height: 8),
            Text(
              'Platform ITS + LMS\nPraktikum Jaringan Komputer Dasar\n\nDibuat untuk skripsi 2026.',
              style: tt.bodyMedium?.copyWith(height: 1.6),
              textAlign: TextAlign.center),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 44,
              child: ElevatedButton(
                onPressed: () => Navigator.pop(ctx),
                child: Text('Tutup',
                  style: tt.labelLarge?.copyWith(color: Colors.white)),
              ),
            ),
          ]),
        ),
      ),
    );
  }
}