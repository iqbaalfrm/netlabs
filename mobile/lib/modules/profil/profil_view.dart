import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import '../../app/routes/app_routes.dart';

// Halaman profil siswa
class ProfilView extends StatelessWidget {
  const ProfilView({super.key});

  // Ambil data user dari GetStorage
  Map<String, dynamic> get user {
    return GetStorage().read<Map<String, dynamic>>('user') ?? {
      'nama': 'Iqbal', 'nis': '2122100045', 'kelas': 'XI TKJ 2',
    };
  }

  @override
  Widget build(BuildContext context) {
    final inisial = (user['nama'] as String? ?? 'S')[0].toUpperCase();

    return Scaffold(
      backgroundColor: AppColors.bgLight,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(
            children: [
              const SizedBox(height: 10),
              _buildHeader(inisial),
              const SizedBox(height: 20),
              _buildStatistik(),
              const SizedBox(height: 24),
              _buildSectionLabel('Informasi Akun'),
              const SizedBox(height: 10),
              _infoItem(LucideIcons.hash, 'NIS', user['nis'] ?? '-'),
              _infoItem(LucideIcons.school, 'Kelas', user['kelas'] ?? '-'),
              _infoItem(LucideIcons.building, 'Sekolah', 'SMK Bhakti Praja Dukuhwaru'),
              const SizedBox(height: 24),
              _buildSectionLabel('Pengaturan'),
              const SizedBox(height: 10),
              _menuItem(LucideIcons.clipboardList, 'Riwayat Kuis', const Color(0xFF0F9B8E), () {}),
              _menuItem(LucideIcons.bell, 'Notifikasi', const Color(0xFFFF9500), () {}),
              _menuItem(LucideIcons.lock, 'Ubah Kata Sandi', const Color(0xFF7B5EA7), () {}),
              _menuItem(LucideIcons.info, 'Tentang Netlabs', AppColors.primary, () {
                _showTentang(context);
              }),
              const SizedBox(height: 24),
              _buildTombolKeluar(context),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader(String inisial) {
    return Container(
      width: double.infinity, padding: const EdgeInsets.symmetric(vertical: 24),
      decoration: BoxDecoration(
        color: AppColors.primary, borderRadius: BorderRadius.circular(14)),
      child: Column(children: [
        Container(
          width: 64, height: 64,
          decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.2), shape: BoxShape.circle),
          child: Center(child: Text(inisial, style: GoogleFonts.plusJakartaSans(
            fontSize: 24, fontWeight: FontWeight.bold, color: Colors.white))),
        ),
        const SizedBox(height: 12),
        Text(user['nama'] ?? 'Siswa', style: GoogleFonts.plusJakartaSans(
          fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
        const SizedBox(height: 4),
        Text('${user['kelas'] ?? ''} • SMK Bhakti Praja', style: GoogleFonts.plusJakartaSans(
          fontSize: 13, color: Colors.white70)),
      ]),
    );
  }

  Widget _buildStatistik() {
    return Row(children: [
      _statCard('3/5', 'Pertemuan'),
      const SizedBox(width: 8),
      _statCard('82', 'Nilai'),
      const SizedBox(width: 8),
      _statCard('7 hari', 'Streak'),
    ]);
  }

  Widget _statCard(String angka, String label) {
    return Expanded(child: Container(
      padding: const EdgeInsets.symmetric(vertical: 14),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(10),
        border: Border.all(color: AppColors.border)),
      child: Column(children: [
        Text(angka, style: GoogleFonts.plusJakartaSans(
          fontSize: 16, fontWeight: FontWeight.bold, color: AppColors.textPrimary)),
        const SizedBox(height: 2),
        Text(label, style: GoogleFonts.plusJakartaSans(
          fontSize: 11, color: AppColors.textSecondary)),
      ]),
    ));
  }

  Widget _buildSectionLabel(String text) {
    return Align(alignment: Alignment.centerLeft,
      child: Text(text, style: GoogleFonts.plusJakartaSans(
        fontSize: 15, fontWeight: FontWeight.bold, color: AppColors.textPrimary)));
  }

  Widget _infoItem(IconData icon, String label, String value) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(10),
        border: Border.all(color: AppColors.border)),
      child: Row(children: [
        Icon(icon, size: 18, color: AppColors.primary),
        const SizedBox(width: 12),
        Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 13, color: AppColors.textSecondary)),
        const Spacer(),
        Text(value, style: GoogleFonts.plusJakartaSans(
          fontSize: 13, fontWeight: FontWeight.w500, color: AppColors.textPrimary)),
      ]),
    );
  }

  Widget _menuItem(IconData icon, String label, Color iconColor, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 8),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(10),
          border: Border.all(color: AppColors.border)),
        child: Row(children: [
          Container(width: 32, height: 32,
            decoration: BoxDecoration(color: iconColor.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(8)),
            child: Icon(icon, size: 16, color: iconColor)),
          const SizedBox(width: 12),
          Expanded(child: Text(label, style: GoogleFonts.plusJakartaSans(
            fontSize: 13, color: AppColors.textPrimary))),
          Icon(LucideIcons.chevronRight, size: 16, color: AppColors.textSecondary),
        ]),
      ),
    );
  }

  Widget _buildTombolKeluar(BuildContext context) {
    return SizedBox(width: double.infinity,
      child: OutlinedButton(
        onPressed: () => _konfirmasiKeluar(context),
        style: OutlinedButton.styleFrom(
          padding: const EdgeInsets.symmetric(vertical: 14),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          side: const BorderSide(color: Colors.red)),
        child: Text('Keluar', style: GoogleFonts.plusJakartaSans(
          fontSize: 14, fontWeight: FontWeight.w600, color: Colors.red)),
      ),
    );
  }

  void _konfirmasiKeluar(BuildContext context) {
    showModalBottomSheet(context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.fromLTRB(24, 24, 24, 32),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(width: 40, height: 4,
            decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(2))),
          const SizedBox(height: 20),
          Container(width: 48, height: 48,
            decoration: BoxDecoration(color: Colors.red.withValues(alpha: 0.1), shape: BoxShape.circle),
            child: const Icon(LucideIcons.logOut, size: 22, color: Colors.red)),
          const SizedBox(height: 16),
          Text('Keluar dari akun?', style: GoogleFonts.plusJakartaSans(
            fontSize: 16, fontWeight: FontWeight.bold, color: AppColors.textPrimary)),
          const SizedBox(height: 6),
          Text('Kamu perlu login lagi untuk mengakses aplikasi.',
            style: GoogleFonts.plusJakartaSans(fontSize: 13, color: AppColors.textSecondary),
            textAlign: TextAlign.center),
          const SizedBox(height: 20),
          Row(children: [
            Expanded(child: OutlinedButton(
              onPressed: () => Navigator.pop(ctx),
              style: OutlinedButton.styleFrom(
                padding: const EdgeInsets.symmetric(vertical: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                side: BorderSide(color: AppColors.border)),
              child: Text('Batal', style: GoogleFonts.plusJakartaSans(
                fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.textPrimary)))),
            const SizedBox(width: 12),
            Expanded(child: ElevatedButton(
              onPressed: () {
                Navigator.pop(ctx);
                // Hapus token dan data user
                GetStorage().erase();
                Get.offAllNamed(Routes.login);
              },
              style: ElevatedButton.styleFrom(backgroundColor: Colors.red,
                padding: const EdgeInsets.symmetric(vertical: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)), elevation: 0),
              child: Text('Keluar', style: GoogleFonts.plusJakartaSans(
                fontSize: 13, fontWeight: FontWeight.w600, color: Colors.white)))),
          ]),
        ]),
      ),
    );
  }

  void _showTentang(BuildContext context) {
    showDialog(context: context, builder: (ctx) => AlertDialog(
      title: Text('Netlabs v1.0.0', style: GoogleFonts.plusJakartaSans(
        fontSize: 16, fontWeight: FontWeight.w600)),
      content: Text('Platform ITS + LMS untuk Praktikum Jaringan Komputer Dasar.\n\nDibuat untuk skripsi 2026.\nSMK Bhakti Praja Dukuhwaru.',
        style: GoogleFonts.plusJakartaSans(fontSize: 13, color: AppColors.textSecondary, height: 1.5)),
      actions: [TextButton(onPressed: () => Navigator.pop(ctx),
        child: Text('Tutup', style: GoogleFonts.plusJakartaSans(color: AppColors.primary)))],
    ));
  }
}
