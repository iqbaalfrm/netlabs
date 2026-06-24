import 'package:get/get.dart';

import '../../app/services/api_service.dart';
import '../../models/pertemuan.dart';
import '../../models/chat.dart';

/// Controller untuk halaman beranda
class HomeController extends GetxController {
  final pertemuanAktif = <Pertemuan>[].obs;
  final chatTerakhir = <ChatMessage>[].obs;
  final isLoading = true.obs;
  final tabAktif = 0.obs;

  void gantiTab(int index) => tabAktif.value = index;
  void keMateri() => tabAktif.value = 1;
  void keChat() => tabAktif.value = 2;

  @override
  void onInit() {
    super.onInit();
    loadData();
  }

  Future<void> loadData() async {
    isLoading.value = true;
    try {
      // Ambil daftar pertemuan
      final data = await ApiService.getPertemuan();
      pertemuanAktif.value = data
          .map((e) => Pertemuan(
                id: e['id']?.toString() ?? '',
                nomor: e['nomor'] ?? 0,
                semester: e['semester'] ?? 1,
                judul: e['judul'] ?? '',
                estimasi: e['estimasi'] ?? '2 × 45 menit',
                deskripsi: e['deskripsi'] ?? '',
                tujuan: e['tujuan'] ?? '',
                totalTopik: e['total_topik'] ?? 0,
                topikSelesai: e['topik_selesai'] ?? 0,
                terkunci: e['terkunci'] == true,
              ))
          .toList();

      // Ambil chat terakhir (pakai dummy jika API belum siap)
      chatTerakhir.clear();
    } catch (_) {
      // Data dummy fallback
      pertemuanAktif.value = _dummyPertemuan();
    } finally {
      isLoading.value = false;
    }
  }

  // --- Dummy data fallback ---
  List<Pertemuan> _dummyPertemuan() => [
        Pertemuan(
          id: '1',
          nomor: 1,
          semester: 1,
          judul: 'K3LH & Perakitan Komputer',
          estimasi: '2 × 45 menit',
          deskripsi: 'Pengenalan K3LH dan cara merakit komputer dengan benar.',
          tujuan: 'Siswa memahami prinsip K3LH dan mampu merakit PC.',
          totalTopik: 3,
          topikSelesai: 2,
        ),
        Pertemuan(
          id: '2',
          nomor: 2,
          semester: 1,
          judul: 'Dasar-Dasar Jaringan Komputer',
          estimasi: '2 × 45 menit',
          deskripsi: 'Pengenalan jaringan komputer, topologi, dan komponen jaringan.',
          tujuan: 'Siswa memahami dasar jaringan dan topologi.',
          totalTopik: 3,
          topikSelesai: 0,
        ),
        Pertemuan(
          id: '3',
          nomor: 3,
          semester: 1,
          judul: 'IP Address & Subnetting',
          estimasi: '2 × 45 menit',
          deskripsi: 'Materi tentang pengalamatan IP, subnet mask, dan perhitungan subnet.',
          tujuan: 'Siswa mampu menghitung subnetting.',
          totalTopik: 3,
          topikSelesai: 0,
          terkunci: true,
        ),
        Pertemuan(
          id: '4',
          nomor: 4,
          semester: 1,
          judul: 'Media Transmisi & Pengkabelan',
          estimasi: '2 × 45 menit',
          deskripsi: 'Jenis kabel jaringan, konektor, dan teknik crimping.',
          tujuan: 'Siswa mampu membuat kabel jaringan.',
          totalTopik: 3,
          topikSelesai: 0,
          terkunci: true,
        ),
      ];
}