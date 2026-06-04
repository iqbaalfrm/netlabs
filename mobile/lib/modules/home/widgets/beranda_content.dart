import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../../app/theme/app_colors.dart';
import '../../../app/constants/dummy_data.dart';
import '../home_controller.dart';

// Konten Beranda — clean, satu warna utama (biru)
class BerandaContent extends StatelessWidget {
  const BerandaContent({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bgLight,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.only(bottom: 100),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 20),
              _buildHeader(),
              const SizedBox(height: 20),
              _buildBannerAI(),
              const SizedBox(height: 20),
              _buildSectionProgress(),
              const SizedBox(height: 24),
              _buildSectionPertemuan(),
              const SizedBox(height: 24),
              _buildAktivitasTerakhir(),
              const SizedBox(height: 24),
              _buildKuisTersedia(),
            ],
          ),
        ),
      ),
    );
  }

  // Header
  Widget _buildHeader() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Row(
        children: [
          Container(
            width: 38, height: 38,
            decoration: BoxDecoration(
              color: AppColors.primary.withValues(alpha: 0.1),
              shape: BoxShape.circle,
            ),
            child: Center(
              child: Text('I',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 15, fontWeight: FontWeight.bold,
                  color: AppColors.primary)),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Hi, ${DummyData.nama}',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 13, color: AppColors.textSecondary)),
                Text('Mau belajar apa?',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 18, fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary)),
              ],
            ),
          ),
          Container(
            width: 36, height: 36,
            decoration: BoxDecoration(
              color: Colors.white,
              shape: BoxShape.circle,
              border: Border.all(color: AppColors.border),
            ),
            child: const Icon(LucideIcons.bell,
                size: 17, color: AppColors.textPrimary),
          ),
        ],
      ),
    );
  }

  // Banner AI
  Widget _buildBannerAI() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: GestureDetector(
        onTap: () => Get.find<HomeController>().keChat(),
        child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: AppColors.border),
        ),
        child: Row(
          children: [
            Container(
              width: 36, height: 36,
              decoration: BoxDecoration(
                color: AppColors.primary.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(10)),
              child: const Icon(LucideIcons.bot,
                  color: AppColors.primary, size: 18),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('AI Tutor',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 15, fontWeight: FontWeight.w600,
                      color: AppColors.textPrimary)),
                  Text('Tanya materi yang belum paham',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12, color: AppColors.textSecondary)),
                ],
              ),
            ),
            const Icon(LucideIcons.arrowRight,
                size: 18, color: AppColors.primary),
          ],
        ),
      ),
      ),
    );
  }

  // Progress — 3 card putih simple
  Widget _buildSectionProgress() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Row(
        children: [
          _progressItem(LucideIcons.bookOpen, '${DummyData.pertemuanSelesai}/${DummyData.totalPertemuan}', 'pertemuan'),
          const SizedBox(width: 10),
          _progressItem(LucideIcons.star, '${DummyData.nilaiRataRata}', 'nilai'),
          const SizedBox(width: 10),
          _progressItem(LucideIcons.flame, '${DummyData.streakHari} hari', 'streak'),
        ],
      ),
    );
  }

  Widget _progressItem(IconData icon, String angka, String label) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: AppColors.border),
        ),
        child: Row(
          children: [
            Icon(icon, size: 15, color: AppColors.primary),
            const SizedBox(width: 8),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(angka,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 14, fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary)),
                Text(label,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 10, color: AppColors.textSecondary)),
              ],
            ),
          ],
        ),
      ),
    );
  }

  // Pertemuan — horizontal scroll
  Widget _buildSectionPertemuan() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Pertemuan',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 18, fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary)),
              GestureDetector(
                onTap: () => Get.find<HomeController>().keMateri(),
                child: Text('Lihat semua',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 13, color: AppColors.primary)),
              ),
            ],
          ),
        ),
        const SizedBox(height: 14),
        SizedBox(
          height: 150,
          child: ListView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 20),
            children: [
              _pertemuanCard(1, 'Pengenalan\nJaringan', '4 topik', 0.75),
              _pertemuanCard(2, 'Pengalamatan\nIP', '4 topik', 0.5),
              _pertemuanCard(3, 'Konfigurasi\nIP Windows', '3 topik', 0.33),
              _pertemuanCard(4, 'Implementasi\nVLAN', '4 topik', 0.0),
              _pertemuanCard(5, 'Static\nRouting', '3 topik', 0.0),
            ],
          ),
        ),
      ],
    );
  }

  Widget _pertemuanCard(int nomor, String judul, String topik, double progress) {
    return Container(
      width: 145,
      margin: const EdgeInsets.only(right: 12),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Nomor
          Text('$nomor',
            style: GoogleFonts.plusJakartaSans(
              fontSize: 13, fontWeight: FontWeight.bold,
              color: AppColors.primary)),
          const SizedBox(height: 10),
          // Judul
          Text(judul,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 13, fontWeight: FontWeight.w600,
              color: AppColors.textPrimary, height: 1.3)),
          const Spacer(),
          // Info
          Text(topik,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 11, color: AppColors.textSecondary)),
          const SizedBox(height: 8),
          // Progress bar — semua biru
          ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: LinearProgressIndicator(
              value: progress,
              backgroundColor: AppColors.border,
              color: AppColors.primary,
              minHeight: 4,
            ),
          ),
        ],
      ),
    );
  }

  // Aktivitas terakhir
  Widget _buildAktivitasTerakhir() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Aktivitas Terakhir',
            style: GoogleFonts.plusJakartaSans(
              fontSize: 18, fontWeight: FontWeight.bold,
              color: AppColors.textPrimary)),
          const SizedBox(height: 14),
          _aktivitasItem(LucideIcons.bot, 'Tanya AI: Cara konfigurasi VLAN?', '10 menit lalu'),
          _aktivitasItem(LucideIcons.circleCheck, 'Topik selesai: Kelas IP Address', '1 jam lalu'),
          _aktivitasItem(LucideIcons.fileQuestion, 'Kuis Pertemuan 1 — Nilai: 85', 'Kemarin'),
        ],
      ),
    );
  }

  Widget _aktivitasItem(IconData icon, String judul, String waktu) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          Icon(icon, size: 20, color: AppColors.primary),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(judul,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 13, fontWeight: FontWeight.w500,
                    color: AppColors.textPrimary),
                  maxLines: 1, overflow: TextOverflow.ellipsis),
                const SizedBox(height: 2),
                Text(waktu,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 11, color: AppColors.textSecondary)),
              ],
            ),
          ),
          Icon(LucideIcons.chevronRight, size: 16, color: AppColors.border),
        ],
      ),
    );
  }

  // Kuis tersedia
  Widget _buildKuisTersedia() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Kuis Tersedia',
            style: GoogleFonts.plusJakartaSans(
              fontSize: 18, fontWeight: FontWeight.bold,
              color: AppColors.textPrimary)),
          const SizedBox(height: 14),
          _kuisCard('Pertemuan 3', 'Konfigurasi IP di Windows', '5 soal'),
          _kuisCard('Pertemuan 2', 'Pengalamatan IP', '5 soal'),
        ],
      ),
    );
  }

  Widget _kuisCard(String pertemuan, String judul, String soal) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(pertemuan,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 11, color: AppColors.primary,
                    fontWeight: FontWeight.w600)),
                const SizedBox(height: 4),
                Text(judul,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 14, fontWeight: FontWeight.w600,
                    color: AppColors.textPrimary)),
                const SizedBox(height: 4),
                Text(soal,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12, color: AppColors.textSecondary)),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
            decoration: BoxDecoration(
              color: AppColors.primary,
              borderRadius: BorderRadius.circular(8)),
            child: Text('Mulai',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 12, fontWeight: FontWeight.w600, color: Colors.white)),
          ),
        ],
      ),
    );
  }
}
