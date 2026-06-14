import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import 'kuis_controller.dart';

class KuisView extends StatefulWidget {
  final int nomorPertemuan;
  const KuisView({super.key, required this.nomorPertemuan});

  @override
  State<KuisView> createState() => _KuisViewState();
}

class _KuisViewState extends State<KuisView> {
  late KuisController controller;

  @override
  void initState() {
    super.initState();
    if (Get.isRegistered<KuisController>()) {
      Get.delete<KuisController>();
    }
    controller = Get.put(KuisController());
    controller.init(widget.nomorPertemuan, 'p${widget.nomorPertemuan}');
  }

  @override
  Widget build(BuildContext context) {
    final tt = Theme.of(context).textTheme;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(LucideIcons.x, size: 20),
          onPressed: () => _konfirmasiKeluar(context),
        ),
        title: Text('Kuis Pertemuan ${widget.nomorPertemuan}'),
      ),
      body: Obx(() {
        if (controller.sedangMemuat.value) {
          return const Center(
            child: CircularProgressIndicator(color: AppColors.primary));
        }

        if (controller.daftarSoal.isEmpty) {
          return Center(child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline_rounded,
                size: 48, color: AppColors.textSecondary),
              const SizedBox(height: 16),
              Text('Soal tidak tersedia', style: tt.bodyLarge),
            ],
          ));
        }

        final soal = controller.soalAktif;
        if (soal.isEmpty) {
          return const Center(
            child: CircularProgressIndicator(color: AppColors.primary));
        }

        final pilihan = [
          soal['pilihan_a']?.toString() ?? '',
          soal['pilihan_b']?.toString() ?? '',
          soal['pilihan_c']?.toString() ?? '',
          soal['pilihan_d']?.toString() ?? '',
        ];

        return Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildProgress(tt),
              const SizedBox(height: 28),
              _buildSoalCard(tt, soal),
              const SizedBox(height: 20),
              Expanded(
                child: ListView.builder(
                  itemCount: pilihan.length,
                  itemBuilder: (_, i) => _buildPilihan(i, pilihan[i], tt),
                ),
              ),
              _buildTombolLanjut(context, tt),
              const SizedBox(height: 8),
            ],
          ),
        );
      }),
    );
  }

  Widget _buildProgress(TextTheme tt) {
    final total = controller.daftarSoal.length;
    final now = controller.soalSekarang.value + 1;
    final ratio = total > 0 ? now / total : 0.0;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text('Progres', style: tt.labelSmall),
            Text('$now / $total',
              style: tt.labelSmall?.copyWith(
                color: AppColors.primary, fontWeight: FontWeight.w700)),
          ],
        ),
        const SizedBox(height: 8),
        ClipRRect(
          borderRadius: BorderRadius.circular(8),
          child: LinearProgressIndicator(
            value: ratio,
            backgroundColor: AppColors.border,
            valueColor: const AlwaysStoppedAnimation(AppColors.primary),
            minHeight: 6,
          ),
        ),
      ],
    );
  }

  Widget _buildSoalCard(TextTheme tt, Map<String, dynamic> soal) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
            decoration: BoxDecoration(
              color: AppColors.accent,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Text(
              'Soal ${controller.soalSekarang.value + 1}',
              style: tt.labelSmall?.copyWith(
                color: AppColors.primary, fontWeight: FontWeight.w700)),
          ),
          const SizedBox(height: 14),
          Text(
            soal['pertanyaan']?.toString() ?? '',
            style: tt.bodyLarge?.copyWith(height: 1.5),
          ),
        ],
      ),
    );
  }

  Widget _buildPilihan(int index, String teks, TextTheme tt) {
    if (teks.isEmpty) return const SizedBox.shrink();

    final huruf = ['A', 'B', 'C', 'D'];

    return Obx(() {
      final dipilih = controller.jawabanDipilih.value == index;

      return GestureDetector(
        onTap: () => controller.pilihJawaban(index),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 150),
          width: double.infinity,
          margin: const EdgeInsets.only(bottom: 10),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          decoration: BoxDecoration(
            color: dipilih ? AppColors.accent : AppColors.surface,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: dipilih ? AppColors.primary : AppColors.border,
              width: dipilih ? 2 : 1),
          ),
          child: Row(children: [
            Container(
              width: 36, height: 36,
              decoration: BoxDecoration(
                color: dipilih ? AppColors.primary : AppColors.background,
                borderRadius: BorderRadius.circular(16),
              ),
              child: Center(child: Text(huruf[index],
                style: TextStyle(
                  fontSize: 14, fontWeight: FontWeight.w700,
                  color: dipilih ? Colors.white : AppColors.textSecondary))),
            ),
            const SizedBox(width: 14),
            Expanded(child: Text(teks,
              style: tt.bodyLarge?.copyWith(
                fontSize: 14,
                fontWeight: dipilih ? FontWeight.w600 : FontWeight.w400))),
            if (dipilih)
              const Icon(Icons.check_circle_rounded,
                size: 20, color: AppColors.primary),
          ]),
        ),
      );
    });
  }

  Widget _buildTombolLanjut(BuildContext context, TextTheme tt) {
    return Obx(() {
      final aktif = controller.jawabanDipilih.value != -1;
      final isAkhir = controller.soalSekarang.value >=
          controller.daftarSoal.length - 1;

      return SizedBox(
        width: double.infinity,
        height: 54,
        child: ElevatedButton(
          onPressed: aktif ? () => controller.lanjut(context) : null,
          style: ElevatedButton.styleFrom(
            backgroundColor: aktif ? AppColors.primary : AppColors.border,
            disabledBackgroundColor: AppColors.border,
          ),
          child: Text(
            isAkhir ? 'Selesai & Lihat Nilai' : 'Lanjut',
            style: TextStyle(
              fontSize: 14, fontWeight: FontWeight.w700,
              color: aktif ? Colors.white : AppColors.textSecondary),
          ),
        ),
      );
    });
  }

  void _konfirmasiKeluar(BuildContext context) {
    showModalBottomSheet(
      context: context,
      backgroundColor: AppColors.surface,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) {
        final tt = Theme.of(ctx).textTheme;
        return Padding(
          padding: const EdgeInsets.fromLTRB(24, 16, 24, 36),
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            Container(width: 36, height: 4,
              decoration: BoxDecoration(
                color: AppColors.border,
                borderRadius: BorderRadius.circular(2))),
            const SizedBox(height: 24),
            Container(width: 52, height: 52,
              decoration: const BoxDecoration(
                color: Color(0xFFFFF7ED),
                shape: BoxShape.circle),
              child: const Icon(Icons.warning_amber_rounded,
                size: 24, color: AppColors.warning)),
            const SizedBox(height: 16),
            Text('Keluar dari kuis?', style: tt.titleMedium),
            const SizedBox(height: 8),
            Text('Progres jawaban tidak akan disimpan.',
              style: tt.bodyMedium),
            const SizedBox(height: 24),
            Row(children: [
              Expanded(child: SizedBox(
                height: 48,
                child: OutlinedButton(
                  onPressed: () => Navigator.pop(ctx),
                  child: Text('Lanjutkan', style: tt.labelLarge),
                ),
              )),
              const SizedBox(width: 12),
              Expanded(child: SizedBox(
                height: 48,
                child: ElevatedButton(
                  onPressed: () {
                    Navigator.pop(ctx);
                    Navigator.pop(context);
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.error,
                  ),
                  child: Text('Keluar',
                    style: tt.labelLarge?.copyWith(color: Colors.white)),
                ),
              )),
            ]),
          ]),
        );
      },
    );
  }
}