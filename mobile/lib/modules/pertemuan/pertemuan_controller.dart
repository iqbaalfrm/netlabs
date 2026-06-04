import 'package:get/get.dart';
import 'package:dio/dio.dart';
import '../../app/constants/dummy_data.dart';
import '../../app/services/api_service.dart';

// Controller untuk halaman daftar pertemuan
// Data dari API, fallback ke dummy jika offline
class PertemuanController extends GetxController {
  var semesterAktif = 0.obs;
  var sedangMemuat = false.obs;

  // Data pertemuan dari API (atau dummy)
  var daftarSemester1 = <Map<String, dynamic>>[].obs;
  var daftarSemester2 = <Map<String, dynamic>>[].obs;

  @override
  void onInit() {
    super.onInit();
    _muatPertemuan();
  }

  // Ambil data pertemuan dari backend
  Future<void> _muatPertemuan() async {
    sedangMemuat.value = true;
    try {
      final data = await ApiService.getPertemuan();
      final list = data.cast<Map<String, dynamic>>();
      // Pisahkan per semester (1-8 = semester 1, 9-16 = semester 2)
      daftarSemester1.value = list.where((p) {
        final nomor = (p['nomor_urut'] as num?)?.toInt() ?? 0;
        return nomor >= 1 && nomor <= 8;
      }).toList();
      daftarSemester2.value = list.where((p) {
        final nomor = (p['nomor_urut'] as num?)?.toInt() ?? 0;
        return nomor > 8;
      }).toList();
    } on DioException {
      // Fallback ke dummy data saat offline
      daftarSemester1.value = DummyData.semester1;
      daftarSemester2.value = DummyData.semester2;
    } finally {
      sedangMemuat.value = false;
    }
  }

  void gantiSemester(int index) => semesterAktif.value = index;

  List<Map<String, dynamic>> get daftarPertemuan =>
      semesterAktif.value == 0 ? daftarSemester1 : daftarSemester2;

  Map<String, dynamic> get pertemuanAktif {
    final berlangsung = daftarSemester1.firstWhereOrNull(
        (p) => p['status'] == 'aktif');
    return berlangsung ?? (daftarSemester1.isNotEmpty ? daftarSemester1.first : {});
  }
}
