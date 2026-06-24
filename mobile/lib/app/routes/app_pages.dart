import 'package:get/get.dart';
import 'app_routes.dart';

// ─── Views ──────────────────────────────────────────────────────────────
import '../../modules/splash/splash_view.dart';
import '../../modules/auth/login_view.dart';
import '../../modules/auth/auth_controller.dart';
import '../../modules/home/home_view.dart';
import '../../modules/home/home_controller.dart';
import '../../modules/chat/chat_view.dart';
import '../../modules/chat/chat_controller.dart';
import '../../modules/profil/profil_view.dart';

/// Daftar semua halaman aplikasi Netlabs via GetX navigation
/// 
/// Catatan: View seperti PertemuanDetailView, KuisView, HasilKuisView 
/// memakai constructor parameter wajib, jadi dinavigasikan langsung 
/// dengan Navigator.push() / Get.to() bukan Get.toNamed().
class AppPages {
  static final routes = [
    // ─── Splash ────────────────────────────────────────────────────────
    GetPage(
      name: Routes.splash,
      page: () => const SplashView(),
    ),

    // ─── Auth ───────────────────────────────────────────────────────────
    GetPage(
      name: Routes.login,
      page: () => const LoginView(),
      binding: BindingsBuilder(() {
        Get.lazyPut(() => AuthController());
      }),
    ),

    // ─── Home (dashboard bottom nav) ────────────────────────────────────
    GetPage(
      name: Routes.home,
      page: () => const HomeView(),
      binding: BindingsBuilder(() {
        Get.put(HomeController());
      }),
    ),

    // ─── AI Chat (dipanggil dari bottom nav) ────────────────────────────
    GetPage(
      name: Routes.chat,
      page: () => const ChatView(),
      binding: BindingsBuilder(() {
        Get.lazyPut(() => ChatController());
      }),
    ),

    // ─── Profil (dipanggil dari bottom nav) ─────────────────────────────
    GetPage(
      name: Routes.profil,
      page: () => const ProfilView(),
    ),
  ];
}