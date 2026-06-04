import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import 'kuis_controller.dart';

// Halaman kuis — menggunakan GetView + KuisController
class KuisView extends StatelessWidget {
  final int nomorPertemuan;
  const KuisView({super.key, required this.nomorPertemuan});

  @override
  Widget build(BuildContext context) {
    // Daftarkan controller dan inisialisasi
    final controller = Get.put(KuisController());
    controller.init(nomorPertemuan, 'p$nomorPertemuan');

    return Scaffold(
      backgroundColor: AppColors.bgLight,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(LucideIcons.x, size: 20),
          onPressed: () => _konfirmasiKeluar(context),
        ),
        title: Text('Kuis Pertemuan $nomorPertemuan',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 15, fontWeight: FontWeight.w600,
            color: AppColors.textPrimary)),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: AppColors.border),
        ),
      ),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Obx(() {
          final soal = controller.soalAktif;
          final pilihan = soal['pilihan'] as List<String>;

          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Progress
              _buildProgress(controller),
              const SizedBox(height: 24),
              // Pertanyaan
              Text(soal['pertanyaan'],
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 16, fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary, height: 1.5)),
              const SizedBox(height: 20),
              // Pilihan
              ...List.generate(pilihan.length, (i) =>
                  _buildPilihan(controller, i, pilihan[i])),
              const Spacer(),
              // Tombol lanjut
              _buildTombolLanjut(context, controller),
            ],
          );
        }),
      ),
    );
  }

  Widget _buildProgress(KuisController c) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Text('${c.soalSekarang.value + 1} / ${c.daftarSoal.length}',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 12, color: AppColors.textSecondary)),
        const SizedBox(height: 8),
        ClipRRect(
          borderRadius: BorderRadius.circular(4),
          child: LinearProgressIndicator(
            value: (c.soalSekarang.value + 1) / c.daftarSoal.length,
            backgroundColor: AppColors.border,
            color: AppColors.primary,
            minHeight: 5,
          ),
        ),
      ],
    );
  }

  Widget _buildPilihan(KuisController c, int index, String teks) {
    final dipilih = c.jawabanDipilih.value == index;
    final huruf = ['A', 'B', 'C', 'D'];

    return GestureDetector(
      onTap: () => c.pilihJawaban(index),
      child: Container(
        width: double.infinity,
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: dipilih ? AppColors.primary.withValues(alpha: 0.05) : Colors.white,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(
            color: dipilih ? AppColors.primary : AppColors.border,
            width: dipilih ? 1.5 : 1),
        ),
        child: Row(
          children: [
            Container(
              width: 28, height: 28,
              decoration: BoxDecoration(
                color: dipilih ? AppColors.primary : AppColors.bgLight,
                borderRadius: BorderRadius.circular(8)),
              child: Center(
                child: Text(huruf[index],
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12, fontWeight: FontWeight.w600,
                    color: dipilih ? Colors.white : AppColors.textSecondary)),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(teks,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 13, color: AppColors.textPrimary)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTombolLanjut(BuildContext context, KuisController c) {
    final aktif = c.jawabanDipilih.value != -1;
    return SizedBox(
      width: double.infinity,
      child: ElevatedButton(
        onPressed: aktif ? () => c.lanjut(context) : null,
        style: ElevatedButton.styleFrom(
          backgroundColor: aktif ? AppColors.primary : AppColors.border,
          padding: const EdgeInsets.symmetric(vertical: 14),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          elevation: 0),
        child: Text(
          c.soalSekarang.value < c.daftarSoal.length - 1 ? 'Lanjut' : 'Selesai',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 14, fontWeight: FontWeight.w600,
            color: aktif ? Colors.white : AppColors.textSecondary)),
      ),
    );
  }

  void _konfirmasiKeluar(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Keluar dari kuis?',
          style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.w600)),
        content: Text('Progres tidak akan disimpan.',
          style: GoogleFonts.plusJakartaSans(fontSize: 13, color: AppColors.textSecondary)),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx),
            child: Text('Batal', style: GoogleFonts.plusJakartaSans(color: AppColors.textSecondary))),
          TextButton(onPressed: () { Navigator.pop(ctx); Navigator.pop(context); },
            child: Text('Keluar', style: GoogleFonts.plusJakartaSans(color: Colors.red))),
        ],
      ),
    );
  }
}
