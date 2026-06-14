import 'package:get/get.dart';
import '../../app/constants/dummy_data.dart';
import '../../app/services/api_service.dart';

class PertemuanController extends GetxController {
  var semesterAktif = 0.obs;
  var sedangMemuat = false.obs;

  var daftarSemester1 = <Map<String, dynamic>>[].obs;
  var daftarSemester2 = <Map<String, dynamic>>[].obs;

  @override
  void onInit() {
    super.onInit();
    _muatPertemuan();
  }

  Future<void> _muatPertemuan() async {
    sedangMemuat.value = true;
    try {
      final data = await ApiService.getPertemuan();
      if (data.isNotEmpty) {
        final list = data.cast<Map<String, dynamic>>();
        daftarSemester1.value = list.where((p) {
          final nomor = (p['nomor_urut'] as num?)?.toInt() ?? 0;
          return nomor >= 1 && nomor <= 8;
        }).toList();
        daftarSemester2.value = list.where((p) {
          final nomor = (p['nomor_urut'] as num?)?.toInt() ?? 0;
          return nomor > 8;
        }).toList();
      } else {
        _loadDummy();
      }
    } catch (_) {
      _loadDummy();
    } finally {
      sedangMemuat.value = false;
    }
  }

  void _loadDummy() {
    daftarSemester1.value = DummyData.semester1;
    daftarSemester2.value = DummyData.semester2;
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
