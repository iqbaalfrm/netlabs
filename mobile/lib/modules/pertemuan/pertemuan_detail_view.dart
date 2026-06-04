import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import '../kuis/kuis_view.dart';

// Halaman detail pertemuan — daftar topik + kuis
// Muncul saat siswa tap salah satu pertemuan
class PertemuanDetailView extends StatefulWidget {
  final Map<String, dynamic> pertemuan;
  const PertemuanDetailView({super.key, required this.pertemuan});

  @override
  State<PertemuanDetailView> createState() => _PertemuanDetailViewState();
}

class _PertemuanDetailViewState extends State<PertemuanDetailView> {
  // Data topik dummy (nanti dari API)
  late List<Map<String, dynamic>> daftarTopik;

  @override
  void initState() {
    super.initState();
    // Generate topik berdasarkan jumlah
    daftarTopik = _generateTopik(widget.pertemuan['nomor'], widget.pertemuan['topik']);
  }

  // Generate data topik dummy berdasarkan nomor pertemuan
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
      // Hitung berapa topik yang sudah selesai berdasarkan progress
      final selesaiCount = (progress * judulList.length).round();
      return {
        'nomor': i + 1,
        'judul': judulList[i],
        'selesai': i < selesaiCount,
      };
    });
  }

  // Cek apakah semua topik sudah selesai
  bool get semuaTopikSelesai => daftarTopik.every((t) => t['selesai'] == true);

  // Tandai topik selesai
  void _tandaiSelesai(int index) {
    setState(() {
      daftarTopik[index]['selesai'] = true;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bgLight,
      // AppBar
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(LucideIcons.arrowLeft, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text('Pertemuan ${widget.pertemuan['nomor']}',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 16, fontWeight: FontWeight.w600,
            color: AppColors.textPrimary)),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: AppColors.border),
        ),
      ),
      body: Column(
        children: [
          // Konten scrollable
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Header info
                  _buildHeaderInfo(),
                  const SizedBox(height: 24),
                  // Daftar topik
                  _buildDaftarTopik(),
                  const SizedBox(height: 24),
                  // Section kuis
                  _buildSectionKuis(),
                  const SizedBox(height: 16),
                  // Tombol AI
                  _buildTombolAI(),
                  const SizedBox(height: 20),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  // Header dengan judul dan deskripsi
  Widget _buildHeaderInfo() {
    // Deskripsi per pertemuan
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
        Text(widget.pertemuan['judul'],
          style: GoogleFonts.plusJakartaSans(
            fontSize: 18, fontWeight: FontWeight.bold,
            color: AppColors.textPrimary)),
        const SizedBox(height: 8),
        Text(deskripsi,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 13, color: AppColors.textSecondary, height: 1.5)),
        const SizedBox(height: 12),
        // Info ringkas
        Row(
          children: [
            Icon(LucideIcons.fileText, size: 14, color: AppColors.textSecondary),
            const SizedBox(width: 6),
            Text('${daftarTopik.length} topik',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 12, color: AppColors.textSecondary)),
            const SizedBox(width: 16),
            Icon(LucideIcons.clock, size: 14, color: AppColors.textSecondary),
            const SizedBox(width: 6),
            Text('~${daftarTopik.length * 10} menit',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 12, color: AppColors.textSecondary)),
            const SizedBox(width: 16),
            Icon(LucideIcons.circleCheck, size: 14, color: AppColors.primary),
            const SizedBox(width: 6),
            Text('${daftarTopik.where((t) => t['selesai'] == true).length}/${daftarTopik.length} selesai',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 12, color: AppColors.primary, fontWeight: FontWeight.w500)),
          ],
        ),
        const SizedBox(height: 16),
        // Capaian pembelajaran
        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(10),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Capaian Pembelajaran',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 13, fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary)),
              const SizedBox(height: 8),
              ...capaian.map((item) => Padding(
                padding: const EdgeInsets.only(bottom: 6),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('•  ', style: GoogleFonts.plusJakartaSans(
                      fontSize: 12, color: AppColors.primary)),
                    Expanded(
                      child: Text(item,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 12, color: AppColors.textSecondary, height: 1.4)),
                    ),
                  ],
                ),
              )),
            ],
          ),
        ),
      ],
    );
  }

  // Daftar topik
  Widget _buildDaftarTopik() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text('Topik Pembelajaran',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 15, fontWeight: FontWeight.bold,
            color: AppColors.textPrimary)),
        const SizedBox(height: 12),
        ...daftarTopik.asMap().entries.map((entry) {
          final index = entry.key;
          final topik = entry.value;
          final selesai = topik['selesai'] as bool;

          return GestureDetector(
            onTap: () => _bukaMaterTopik(index, topik),
            child: Container(
              margin: const EdgeInsets.only(bottom: 8),
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: AppColors.border),
              ),
              child: Row(
                children: [
                  // Status icon
                  Container(
                    width: 28, height: 28,
                    decoration: BoxDecoration(
                      color: selesai
                          ? AppColors.primary.withValues(alpha: 0.1)
                          : AppColors.bgLight,
                      borderRadius: BorderRadius.circular(8),
                      border: selesai ? null : Border.all(color: AppColors.border),
                    ),
                    child: Center(
                      child: selesai
                          ? Icon(LucideIcons.check, size: 14, color: AppColors.primary)
                          : Text('${topik['nomor']}',
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 12, color: AppColors.textSecondary)),
                    ),
                  ),
                  const SizedBox(width: 12),
                  // Judul topik
                  Expanded(
                    child: Text(topik['judul'],
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 13, fontWeight: FontWeight.w500,
                        color: AppColors.textPrimary)),
                  ),
                  // Arrow
                  Icon(LucideIcons.chevronRight, size: 16, color: AppColors.textSecondary),
                ],
              ),
            ),
          );
        }),
      ],
    );
  }

  // Section kuis
  Widget _buildSectionKuis() {
    return Container(
      padding: const EdgeInsets.all(16),
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
              Icon(LucideIcons.clipboardList, size: 18,
                color: semuaTopikSelesai ? AppColors.primary : AppColors.textSecondary),
              const SizedBox(width: 10),
              Text('Kuis Pertemuan ${widget.pertemuan['nomor']}',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 14, fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary)),
            ],
          ),
          const SizedBox(height: 10),
          // Detail kuis
          Row(
            children: [
              _kuisInfo(LucideIcons.circleHelp, '5 soal'),
              const SizedBox(width: 16),
              _kuisInfo(LucideIcons.clock, '10 menit'),
              const SizedBox(width: 16),
              _kuisInfo(LucideIcons.target, 'Min. 75'),
            ],
          ),
          const SizedBox(height: 12),
          // Tombol mulai
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: semuaTopikSelesai ? () {
                Navigator.push(context, MaterialPageRoute(
                  builder: (context) => KuisView(nomorPertemuan: widget.pertemuan['nomor']),
                ));
              } : null,
              style: ElevatedButton.styleFrom(
                backgroundColor: semuaTopikSelesai ? AppColors.primary : AppColors.border,
                padding: const EdgeInsets.symmetric(vertical: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                elevation: 0,
              ),
              child: Text(
                semuaTopikSelesai ? 'Mulai Kuis' : 'Selesaikan semua topik dulu',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 13, fontWeight: FontWeight.w600,
                  color: semuaTopikSelesai ? Colors.white : AppColors.textSecondary)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _kuisInfo(IconData icon, String text) {
    return Row(
      children: [
        Icon(icon, size: 13, color: AppColors.textSecondary),
        const SizedBox(width: 4),
        Text(text, style: GoogleFonts.plusJakartaSans(
          fontSize: 12, color: AppColors.textSecondary)),
      ],
    );
  }

  // Tombol tanya AI
  Widget _buildTombolAI() {
    return GestureDetector(
      onTap: () {
        // Nanti navigasi ke chat AI dengan konteks pertemuan ini
      },
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: AppColors.border),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(LucideIcons.bot, size: 18, color: AppColors.primary),
            const SizedBox(width: 8),
            Text('Tanya AI tentang materi ini',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 13, fontWeight: FontWeight.w500,
                color: AppColors.primary)),
          ],
        ),
      ),
    );
  }

  // Buka halaman baca materi topik
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

// ===== HALAMAN BACA MATERI TOPIK =====
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
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(LucideIcons.arrowLeft, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(judul,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 15, fontWeight: FontWeight.w600,
            color: AppColors.textPrimary),
          maxLines: 1, overflow: TextOverflow.ellipsis),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: AppColors.border),
        ),
      ),
      body: Column(
        children: [
          // Konten materi
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(judul,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 18, fontWeight: FontWeight.bold,
                      color: AppColors.textPrimary)),
                  const SizedBox(height: 6),
                  Text('Pertemuan $nomorPertemuan',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12, color: AppColors.textSecondary)),
                  const SizedBox(height: 20),
                  // Isi materi (dummy text)
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
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 14, color: AppColors.textPrimary, height: 1.7),
                  ),
                ],
              ),
            ),
          ),

          // Tombol bawah
          Container(
            padding: const EdgeInsets.fromLTRB(20, 12, 20, 20),
            decoration: BoxDecoration(
              color: Colors.white,
              border: Border(top: BorderSide(color: AppColors.border)),
            ),
            child: SafeArea(
              child: SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: sudahSelesai ? null : onSelesai,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: sudahSelesai ? AppColors.border : AppColors.primary,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10)),
                    elevation: 0,
                  ),
                  child: Text(
                    sudahSelesai ? 'Sudah selesai ✓' : 'Tandai selesai',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 14, fontWeight: FontWeight.w600,
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
