import 'package:get/get.dart';

// Controller halaman utama
class HomeController extends GetxController {
  // Tab bottom navigation yang aktif
  var tabAktif = 0.obs;

  // Fungsi ganti tab
  void gantiTab(int index) {
    tabAktif.value = index;
  }

  // Navigasi ke tab AI Chat
  void keChat() {
    tabAktif.value = 2;
  }

  // Navigasi ke tab Materi
  void keMateri() {
    tabAktif.value = 1;
  }
}
