import 'package:get/get.dart';
import 'app_routes.dart';
import '../../modules/splash/splash_view.dart';
import '../../modules/auth/login_view.dart';
import '../../modules/auth/auth_controller.dart';
import '../../modules/home/home_view.dart';
import '../../modules/home/home_controller.dart';

// Daftar halaman yang bisa diakses
class AppPages {
  static final routes = [
    // Splash screen
    GetPage(
      name: Routes.splash,
      page: () => const SplashView(),
    ),
    // Halaman Login
    GetPage(
      name: Routes.login,
      page: () => const LoginView(),
      binding: BindingsBuilder(() {
        Get.lazyPut(() => AuthController());
      }),
    ),
    // Halaman Home (Dashboard)
    GetPage(
      name: Routes.home,
      page: () => const HomeView(),
      binding: BindingsBuilder(() {
        Get.put(HomeController());
      }),
    ),
  ];
}
