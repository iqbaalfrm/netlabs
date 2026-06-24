// Nama-nama route halaman sebagai konstanta
// ignore_for_file: constant_identifier_names
class Routes {
  // ─── Core ─────────────────────────────────────────────────────────────
  static const splash = '/splash';
  static const login = '/login';
  static const home = '/home';

  // ─── Pertemuan ────────────────────────────────────────────────────────
  static const pertemuan = '/pertemuan';                // daftar semua
  static const detailPertemuan = '/pertemuan/detail';  // detail + topik

  // ─── AI Chat ─────────────────────────────────────────────────────────
  static const chat = '/chat';
  static const chatPertemuan = '/chat/pertemuan';      // chat per pertemuan

  // ─── Kuis ────────────────────────────────────────────────────────────
  static const kuis = '/kuis';                          // kerjakan kuis
  static const hasilKuis = '/kuis/hasil';               // hasil + rekomendasi AI

  // ─── Profil ──────────────────────────────────────────────────────────
  static const profil = '/profil';
  static const editProfil = '/profil/edit';             // edit nama/sekolah
  static const gantiPassword = '/profil/ganti-password';

  // ─── Alias untuk kompatibilitas kode lama ────────────────────────────
  static const SPLASH = splash;
  static const LOGIN = login;
  static const HOME = home;
}