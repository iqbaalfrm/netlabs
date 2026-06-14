import 'package:flutter/material.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import '../kuis/kuis_view.dart';

class PertemuanDetailView extends StatefulWidget {
  final Map<String, dynamic> pertemuan;
  const PertemuanDetailView({super.key, required this.pertemuan});

  @override
  State<PertemuanDetailView> createState() => _PertemuanDetailViewState();
}

class _PertemuanDetailViewState extends State<PertemuanDetailView> {
  late List<Map<String, dynamic>> daftarTopik;

  @override
  void initState() {
    super.initState();
    daftarTopik = _generateTopik(widget.pertemuan['nomor'], widget.pertemuan['topik']);
  }

  List<Map<String, dynamic>> _generateTopik(int nomorPertemuan, int jumlah) {
    final topikData = {
      1: ['Pengertian Jaringan Komputer', 'Jenis Jaringan (LAN, MAN, WAN)', 'Topologi Jaringan', 'Perangkat Keras Jaringan'],
      2: ['Pengertian IP Address', 'Kelas IP Address (A, B, C)', 'IP Public vs IP Private', 'Subnetting Dasar'],
      3: ['Setting IP Manual di Windows', 'Verifikasi dengan CMD', 'Troubleshooting Koneksi Dasar'],
      4: ['Pengertian dan Fungsi VLAN', 'Konfigurasi VLAN di Switch', 'Inter-VLAN Routing', 'Verifikasi VLAN'],
      5: ['Konsep Dasar Routing', 'Konfigurasi Static Route', 'Verifikasi Routing Table'],
    };

    final judulList = topikData[nomorPertemuan] ?? List.generate(jumlah, (i) => 'Topik ${i + 1}');
    final progress = widget.pertemuan['progress'] as double;

    return List.generate(judulList.length, (i) {
      final selesaiCount = (progress * judulList.length).round();
      return {
        'nomor': i + 1,
        'judul': judulList[i],
        'selesai': i < selesaiCount,
      };
    });
  }

  bool get semuaTopikSelesai => daftarTopik.every((t) => t['selesai'] == true);

  void _tandaiSelesai(int index) {
    setState(() {
      daftarTopik[index]['selesai'] = true;
    });
  }

  @override
  Widget build(BuildContext context) {
    final tt = Theme.of(context).textTheme;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(LucideIcons.arrowLeft, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text('Pertemuan ${widget.pertemuan['nomor']}'),
      ),
      body: Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildHeaderInfo(tt),
                  const SizedBox(height: 28),
                  _buildDaftarTopik(tt),
                  const SizedBox(height: 28),
                  _buildSectionKuis(tt),
                  const SizedBox(height: 16),
                  _buildTombolAI(tt),
                  const SizedBox(height: 20),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildHeaderInfo(TextTheme tt) {
    final deskripsiMap = {
      1: 'Memahami konsep dasar jaringan komputer, mengenal jenis-jenis jaringan berdasarkan jangkauan, serta mempelajari berbagai topologi yang digunakan.',
      2: 'Mempelajari sistem pengalamatan IP versi 4, pembagian kelas IP, perbedaan IP public dan private, serta dasar-dasar subnetting.',
      3: 'Praktik langsung mengkonfigurasi IP address secara manual di Windows dan memverifikasi koneksi menggunakan perintah CMD.',
      4: 'Memahami konsep Virtual LAN dan cara implementasinya pada switch Cisco untuk memisahkan broadcast domain.',
      5: 'Mempelajari konsep routing statis dan cara mengkonfigurasi static route pada router Cisco.',
    };

    final capaianMap = {
      1: ['Menjelaskan pengertian jaringan komputer', 'Membedakan jenis jaringan LAN, MAN, WAN', 'Menggambarkan topologi jaringan', 'Mengidentifikasi perangkat jaringan'],
      2: ['Menjelaskan format IP Address v4', 'Menentukan kelas IP Address', 'Membedakan IP public dan private', 'Melakukan perhitungan subnetting dasar'],
      3: ['Mengkonfigurasi IP secara manual', 'Menggunakan perintah ipconfig', 'Melakukan ping test', 'Melakukan troubleshooting koneksi'],
      4: ['Menjelaskan fungsi VLAN', 'Membuat VLAN di switch Cisco', 'Mengkonfigurasi trunk port', 'Memverifikasi konfigurasi VLAN'],
      5: ['Menjelaskan konsep routing', 'Mengkonfigurasi static route', 'Membaca routing table', 'Memverifikasi konektivitas antar jaringan'],
    };

    final nomor = widget.pertemuan['nomor'] as int;
    final deskripsi = deskripsiMap[nomor] ?? 'Deskripsi pertemuan.';
    final capaian = capaianMap[nomor] ?? ['Capaian pembelajaran'];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(widget.pertemuan['judul'], style: tt.headlineMedium),
        const SizedBox(height: 8),
        Text(deskripsi, style: tt.bodyMedium?.copyWith(height: 1.5)),
        const SizedBox(height: 16),
        Row(children: [
          _infoChip(LucideIcons.fileText, '${daftarTopik.length} topik'),
          const SizedBox(width: 12),
          _infoChip(LucideIcons.clock, '~${daftarTopik.length * 10} mnt'),
          const SizedBox(width: 12),
          _infoChip(LucideIcons.circleCheck,
            '${daftarTopik.where((t) => t['selesai'] == true).length}/${daftarTopik.length}',
            highlight: true),
        ]),
        const SizedBox(height: 20),
        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Capaian Pembelajaran', style: tt.labelLarge),
              const SizedBox(height: 10),
              ...capaian.map((item) => Padding(
                padding: const EdgeInsets.only(bottom: 8),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      margin: const EdgeInsets.only(top: 6),
                      width: 6, height: 6,
                      decoration: const BoxDecoration(
                        color: AppColors.primary, shape: BoxShape.circle),
                    ),
                    const SizedBox(width: 10),
                    Expanded(child: Text(item,
                      style: tt.bodyMedium?.copyWith(height: 1.4))),
                  ],
                ),
              )),
            ],
          ),
        ),
      ],
    );
  }

  Widget _infoChip(IconData icon, String text, {bool highlight = false}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: highlight ? AppColors.accent : AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(mainAxisSize: MainAxisSize.min, children: [
        Icon(icon, size: 13,
          color: highlight ? AppColors.primary : AppColors.textSecondary),
        const SizedBox(width: 5),
        Text(text, style: TextStyle(
          fontSize: 12, fontWeight: FontWeight.w500,
          color: highlight ? AppColors.primary : AppColors.textSecondary)),
      ]),
    );
  }

  Widget _buildDaftarTopik(TextTheme tt) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text('Topik Pembelajaran', style: tt.titleMedium),
        const SizedBox(height: 12),
        ...daftarTopik.asMap().entries.map((entry) {
          final index = entry.key;
          final topik = entry.value;
          final selesai = topik['selesai'] as bool;

          return GestureDetector(
            onTap: () => _bukaMaterTopik(index, topik),
            child: Container(
              margin: const EdgeInsets.only(bottom: 8),
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.border),
              ),
              child: Row(children: [
                Container(
                  width: 32, height: 32,
                  decoration: BoxDecoration(
                    color: selesai ? AppColors.accent : AppColors.background,
                    borderRadius: BorderRadius.circular(16),
                    border: selesai ? null : Border.all(color: AppColors.border),
                  ),
                  child: Center(
                    child: selesai
                        ? const Icon(LucideIcons.check, size: 15, color: AppColors.primary)
                        : Text('${topik['nomor']}',
                            style: tt.labelSmall?.copyWith(fontWeight: FontWeight.w700)),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(child: Text(topik['judul'], style: tt.bodyLarge?.copyWith(fontSize: 14))),
                const Icon(LucideIcons.chevronRight, size: 16, color: AppColors.textSecondary),
              ]),
            ),
          );
        }),
      ],
    );
  }

  Widget _buildSectionKuis(TextTheme tt) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(children: [
            Icon(LucideIcons.clipboardList, size: 18,
              color: semuaTopikSelesai ? AppColors.primary : AppColors.textSecondary),
            const SizedBox(width: 10),
            Text('Kuis Pertemuan ${widget.pertemuan['nomor']}', style: tt.bodyLarge),
          ]),
          const SizedBox(height: 12),
          Row(children: [
            _kuisInfo(LucideIcons.circleHelp, '5 soal'),
            const SizedBox(width: 16),
            _kuisInfo(LucideIcons.clock, '10 menit'),
            const SizedBox(width: 16),
            _kuisInfo(LucideIcons.target, 'Min. 75'),
          ]),
          const SizedBox(height: 16),
          SizedBox(
            width: double.infinity,
            height: 50,
            child: ElevatedButton(
              onPressed: semuaTopikSelesai ? () {
                Navigator.push(context, MaterialPageRoute(
                  builder: (context) => KuisView(nomorPertemuan: widget.pertemuan['nomor']),
                ));
              } : null,
              style: ElevatedButton.styleFrom(
                backgroundColor: semuaTopikSelesai ? AppColors.primary : AppColors.border,
                disabledBackgroundColor: AppColors.border,
              ),
              child: Text(
                semuaTopikSelesai ? 'Mulai Kuis' : 'Selesaikan semua topik dulu',
                style: TextStyle(
                  fontSize: 14, fontWeight: FontWeight.w700,
                  color: semuaTopikSelesai ? Colors.white : AppColors.textSecondary),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _kuisInfo(IconData icon, String text) {
    return Row(children: [
      Icon(icon, size: 13, color: AppColors.textSecondary),
      const SizedBox(width: 4),
      Text(text, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
    ]);
  }

  Widget _buildTombolAI(TextTheme tt) {
    return GestureDetector(
      onTap: () {},
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: AppColors.accent,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(LucideIcons.bot, size: 18, color: AppColors.primary),
            const SizedBox(width: 8),
            Text('Tanya AI tentang materi ini',
              style: tt.labelLarge?.copyWith(color: AppColors.primary)),
          ],
        ),
      ),
    );
  }

  void _bukaMaterTopik(int index, Map<String, dynamic> topik) {
    Navigator.push(context, MaterialPageRoute(
      builder: (context) => _TopikReadView(
        judul: topik['judul'],
        nomorPertemuan: widget.pertemuan['nomor'],
        sudahSelesai: topik['selesai'],
        onSelesai: () {
          _tandaiSelesai(index);
          Navigator.pop(context);
        },
      ),
    ));
  }
}

class _TopikReadView extends StatelessWidget {
  final String judul;
  final int nomorPertemuan;
  final bool sudahSelesai;
  final VoidCallback onSelesai;

  const _TopikReadView({
    required this.judul,
    required this.nomorPertemuan,
    required this.sudahSelesai,
    required this.onSelesai,
  });

  @override
  Widget build(BuildContext context) {
    final tt = Theme.of(context).textTheme;

    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(LucideIcons.arrowLeft, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(judul, maxLines: 1, overflow: TextOverflow.ellipsis),
      ),
      body: Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(judul, style: tt.headlineMedium),
                  const SizedBox(height: 6),
                  Text('Pertemuan $nomorPertemuan', style: tt.labelSmall),
                  const SizedBox(height: 24),
                  Text(
                    'Materi ini membahas tentang $judul secara lengkap. '
                    'Dalam praktikum jaringan komputer, pemahaman tentang topik ini '
                    'sangat penting sebagai dasar untuk materi selanjutnya.\n\n'
                    'Beberapa poin penting yang perlu dipahami:\n\n'
                    '1. Konsep dasar dan pengertian\n'
                    '2. Cara kerja dan implementasi\n'
                    '3. Contoh penerapan di dunia nyata\n'
                    '4. Langkah-langkah konfigurasi\n\n'
                    'Pastikan kamu memahami semua poin di atas sebelum '
                    'melanjutkan ke topik berikutnya. Jika ada yang belum '
                    'dipahami, gunakan fitur AI Tutor untuk bertanya.',
                    style: tt.bodyLarge?.copyWith(height: 1.7),
                  ),
                ],
              ),
            ),
          ),
          Container(
            padding: const EdgeInsets.fromLTRB(24, 12, 24, 24),
            decoration: const BoxDecoration(
              color: AppColors.surface,
              border: Border(top: BorderSide(color: AppColors.border)),
            ),
            child: SafeArea(
              child: SizedBox(
                width: double.infinity,
                height: 52,
                child: ElevatedButton(
                  onPressed: sudahSelesai ? null : onSelesai,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: sudahSelesai ? AppColors.border : AppColors.primary,
                    disabledBackgroundColor: AppColors.border,
                  ),
                  child: Text(
                    sudahSelesai ? 'Sudah selesai' : 'Tandai selesai',
                    style: TextStyle(
                      fontSize: 14, fontWeight: FontWeight.w700,
                      color: sudahSelesai ? AppColors.textSecondary : Colors.white),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}