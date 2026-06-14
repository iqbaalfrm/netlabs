import 'package:flutter/material.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';

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
    final tt = Theme.of(context).textTheme;
    final nilai = totalSoal > 0 ? (jumlahBenar / totalSoal * 100).round() : 0;
    final lulus = nilai >= 75;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        title: Text('Hasil Kuis Pertemuan $nomorPertemuan'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(children: [
          _buildNilaiCard(tt, nilai, lulus),
          const SizedBox(height: 16),
          _buildStatRow(tt),
          const SizedBox(height: 20),
          _buildRingkasanJawaban(tt),
          const SizedBox(height: 20),
          _buildSaranAI(tt, lulus),
          const SizedBox(height: 28),
          _buildButtons(context, tt, lulus),
          const SizedBox(height: 30),
        ]),
      ),
    );
  }

  Widget _buildNilaiCard(TextTheme tt, int nilai, bool lulus) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 36, horizontal: 24),
      decoration: BoxDecoration(
        color: lulus ? AppColors.primary : AppColors.error,
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(children: [
        Container(
          width: 64, height: 64,
          decoration: BoxDecoration(
            color: Colors.white.withValues(alpha: 0.15),
            shape: BoxShape.circle,
          ),
          child: Icon(
            lulus ? LucideIcons.trophy : LucideIcons.refreshCw,
            size: 28, color: Colors.white),
        ),
        const SizedBox(height: 16),
        Text(
          lulus ? 'Selamat, Kamu Lulus!' : 'Belum Lulus',
          style: tt.titleMedium?.copyWith(color: Colors.white),
        ),
        const SizedBox(height: 6),
        Text(
          lulus
              ? 'Nilai kamu memenuhi standar kelulusan'
              : 'Pelajari ulang materi dan coba lagi ya',
          style: tt.bodyMedium?.copyWith(
            color: Colors.white.withValues(alpha: 0.8)),
        ),
        const SizedBox(height: 24),
        Text('$nilai',
          style: tt.displayLarge?.copyWith(
            fontSize: 56, fontWeight: FontWeight.w900, color: Colors.white)),
        Text('/ 100',
          style: tt.bodyMedium?.copyWith(
            color: Colors.white.withValues(alpha: 0.7))),
      ]),
    );
  }

  Widget _buildStatRow(TextTheme tt) {
    return Row(children: [
      _statCard('Benar', '$jumlahBenar', AppColors.success, tt),
      const SizedBox(width: 10),
      _statCard('Salah', '${totalSoal - jumlahBenar}', AppColors.error, tt),
      const SizedBox(width: 10),
      _statCard('Total', '$totalSoal', AppColors.primary, tt),
    ]);
  }

  Widget _statCard(String label, String angka, Color color, TextTheme tt) {
    return Expanded(child: Container(
      padding: const EdgeInsets.symmetric(vertical: 16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(children: [
        Text(angka,
          style: tt.headlineMedium?.copyWith(color: color)),
        const SizedBox(height: 4),
        Text(label, style: tt.labelSmall),
      ]),
    ));
  }

  Widget _buildRingkasanJawaban(TextTheme tt) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 12),
          child: Row(children: [
            const Icon(LucideIcons.clipboardList,
              size: 16, color: AppColors.textSecondary),
            const SizedBox(width: 8),
            Text('Ringkasan Jawaban', style: tt.bodyLarge),
          ]),
        ),
        const Divider(height: 1, color: AppColors.border),
        ...List.generate(totalSoal, (i) {
          final benar = i < jumlahBenar;
          return Column(children: [
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              child: Row(children: [
                Container(
                  width: 32, height: 32,
                  decoration: BoxDecoration(
                    color: benar ? AppColors.accent : const Color(0xFFFEE2E2),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Icon(
                    benar ? LucideIcons.check : LucideIcons.x,
                    size: 14,
                    color: benar ? AppColors.primary : AppColors.error),
                ),
                const SizedBox(width: 12),
                Text('Soal ${i + 1}', style: tt.bodyLarge?.copyWith(fontSize: 13)),
                const Spacer(),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: benar ? AppColors.accent : const Color(0xFFFEE2E2),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Text(benar ? 'Benar' : 'Salah',
                    style: TextStyle(
                      fontSize: 11, fontWeight: FontWeight.w700,
                      color: benar ? AppColors.primary : AppColors.error)),
                ),
              ]),
            ),
            if (i < totalSoal - 1)
              const Divider(height: 1, indent: 60, color: AppColors.border),
          ]);
        }),
      ]),
    );
  }

  Widget _buildSaranAI(TextTheme tt, bool lulus) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppColors.primaryDark,
        borderRadius: BorderRadius.circular(16),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 36, height: 36,
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Icon(LucideIcons.bot, size: 18, color: Colors.white),
          ),
          const SizedBox(width: 12),
          Expanded(child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Saran AI Tutor',
                style: tt.labelLarge?.copyWith(color: Colors.white)),
              const SizedBox(height: 6),
              Text(
                lulus
                    ? 'Bagus! Kamu sudah memahami materi ini. Lanjutkan ke pertemuan berikutnya untuk memperdalam ilmu jaringan komputer.'
                    : 'Jangan menyerah! Coba baca ulang topik yang belum dipahami. Gunakan AI Tutor untuk bertanya tentang bagian yang masih membingungkan.',
                style: tt.bodyMedium?.copyWith(
                  color: Colors.white.withValues(alpha: 0.85), height: 1.5),
              ),
            ],
          )),
        ],
      ),
    );
  }

  Widget _buildButtons(BuildContext context, TextTheme tt, bool lulus) {
    return Column(children: [
      SizedBox(
        width: double.infinity,
        height: 54,
        child: ElevatedButton(
          onPressed: () => Navigator.pop(context),
          child: Text('Kembali ke Materi',
            style: tt.labelLarge?.copyWith(color: Colors.white)),
        ),
      ),
      if (!lulus) ...[
        const SizedBox(height: 12),
        SizedBox(
          width: double.infinity,
          height: 54,
          child: OutlinedButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Ulangi Kuis', style: tt.labelLarge),
          ),
        ),
      ],
    ]);
  }
}