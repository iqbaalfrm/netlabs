import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import '../../app/services/api_service.dart';

/// Controller halaman profil — data dari API backend
class ProfilController extends GetxController {
  var sedangMemuat = true.obs;

  // Statistik
  var pertemuanSelesai = 0.obs;
  var totalPertemuan = 0.obs;
  var rataNilai = 0.obs;
  var totalChat = 0.obs;

  // Data user (cache dari GetStorage)
  Map<String, dynamic> get user =>
      GetStorage().read<Map<String, dynamic>>('user') ?? {};

  @override
  void onInit() {
    super.onInit();
    muatStatistik();
  }

  Future<void> muatStatistik() async {
    sedangMemuat.value = true;
    try {
      // Ambil daftar pertemuan untuk hitung progress
      final pertemuan = await ApiService.getPertemuan();
      if (pertemuan.isNotEmpty) {
        totalPertemuan.value = pertemuan.length;
        final selesai = pertemuan.where((p) =>
            (p['progress'] as num?)?.toDouble() == 1.0 ||
            p['status'] == 'selesai').length;
        pertemuanSelesai.value = selesai;
      }

      // Ambil nilai
      final nilai = await ApiService.getNilaiSaya();
      if (nilai['success'] == true && nilai['data'] != null) {
        final list = nilai['data'] as List? ?? [];
        if (list.isNotEmpty) {
          final total = list.fold<int>(0,
              (sum, n) => sum + ((n['nilai'] as num?)?.toInt() ?? 0));
          rataNilai.value = (total / list.length).round();
        }
      }
    } catch (_) {
      // Abaikan — tampilkan default
    } finally {
      sedangMemuat.value = false;
    }
  }

  /// Logout: panggil API lalu hapus session
  Future<void> logout() async {
    await ApiService.logout();
  }
}