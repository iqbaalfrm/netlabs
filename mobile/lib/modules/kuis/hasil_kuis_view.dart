import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';

// Halaman hasil kuis
class HasilKuisView extends StatelessWidget {
  final int jumlahBenar;
  final int totalSoal;
  final int nomorPertemuan;

  const HasilKuisView({
    super.key,
    required this.jumlahBenar,
    required this.totalSoal,
    required this.nomorPertemuan,
  });

  @override
  Widget build(BuildContext context) {
    final nilai = (jumlahBenar / totalSoal * 100).round();
    final lulus = nilai >= 75;

    return Scaffold(
      backgroundColor: AppColors.bgLight,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        automaticallyImplyLeading: false,
        title: Text('Hasil Kuis',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 16, fontWeight: FontWeight.w600,
            color: AppColors.textPrimary)),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: AppColors.border),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Card nilai utama
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: lulus ? AppColors.primary : Colors.white,
                borderRadius: BorderRadius.circular(14),
                border: lulus ? null : Border.all(color: AppColors.border),
              ),
              child: Column(
                children: [
                  Icon(lulus ? LucideIcons.trophy : LucideIcons.circleAlert,
                    size: 28, color: lulus ? Colors.white : Colors.red),
                  const SizedBox(height: 10),
                  Text(lulus ? 'Lulus!' : 'Belum Lulus',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 18, fontWeight: FontWeight.bold,
                      color: lulus ? Colors.white : AppColors.textPrimary)),
                  const SizedBox(height: 4),
                  Text('Pertemuan $nomorPertemuan • ${lulus ? "Nilai mencukupi" : "Minimum 75"}',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12,
                      color: lulus ? Colors.white70 : AppColors.textSecondary)),
                ],
              ),
            ),

            const SizedBox(height: 16),

            // Row: Nilai, Benar, Salah
            Row(
              children: [
                _statCard('Nilai', '$nilai', lulus ? AppColors.primary : Colors.red),
                const SizedBox(width: 8),
                _statCard('Benar', '$jumlahBenar', AppColors.primary),
                const SizedBox(width: 8),
                _statCard('Salah', '${totalSoal - jumlahBenar}', Colors.red),
              ],
            ),

            const SizedBox(height: 24),

            // Ringkasan per soal
            Text('Ringkasan Jawaban',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 15, fontWeight: FontWeight.bold,
                color: AppColors.textPrimary)),
            const SizedBox(height: 12),
            ...List.generate(totalSoal, (i) {
              final benar = i < jumlahBenar; // simplified
              return Container(
                margin: const EdgeInsets.only(bottom: 8),
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: AppColors.border),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 24, height: 24,
                      decoration: BoxDecoration(
                        color: benar
                            ? AppColors.primary.withValues(alpha: 0.1)
                            : Colors.red.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(6)),
                      child: Icon(
                        benar ? LucideIcons.check : LucideIcons.x,
                        size: 13,
                        color: benar ? AppColors.primary : Colors.red),
                    ),
                    const SizedBox(width: 10),
                    Text('Soal ${i + 1}',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 13, color: AppColors.textPrimary)),
                    const Spacer(),
                    Text(benar ? 'Benar' : 'Salah',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12, fontWeight: FontWeight.w500,
                        color: benar ? AppColors.primary : Colors.red)),
                  ],
                ),
              );
            }),

            const SizedBox(height: 20),

            // Saran AI
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.border),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(LucideIcons.bot, size: 16, color: AppColors.primary),
                      const SizedBox(width: 8),
                      Text('Saran AI Tutor',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 13, fontWeight: FontWeight.w600,
                          color: AppColors.textPrimary)),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    lulus
                        ? 'Bagus! Kamu sudah memahami materi pertemuan ini. Lanjutkan ke pertemuan berikutnya untuk memperdalam ilmu jaringan komputer.'
                        : 'Jangan menyerah! Coba baca ulang topik yang belum dipahami, terutama bagian konsep dasar. Gunakan fitur AI Tutor untuk bertanya jika ada yang membingungkan.',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12, color: AppColors.textSecondary, height: 1.5)),
                ],
              ),
            ),

            const SizedBox(height: 20),

            // Tombol
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  elevation: 0,
                ),
                child: Text('Kembali',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 14, fontWeight: FontWeight.w600, color: Colors.white)),
              ),
            ),

            if (!lulus) ...[
              const SizedBox(height: 10),
              SizedBox(
                width: double.infinity,
                child: OutlinedButton(
                  onPressed: () {
                    Navigator.pop(context); // kembali ke detail
                  },
                  style: OutlinedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    side: BorderSide(color: AppColors.border),
                  ),
                  child: Text('Baca Ulang Materi',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 14, fontWeight: FontWeight.w600,
                      color: AppColors.textPrimary)),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _statCard(String label, String angka, Color warna) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: AppColors.border),
        ),
        child: Column(
          children: [
            Text(angka,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 20, fontWeight: FontWeight.bold, color: warna)),
            const SizedBox(height: 2),
            Text(label,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 11, color: AppColors.textSecondary)),
          ],
        ),
      ),
    );
  }
}
