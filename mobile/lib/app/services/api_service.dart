import 'package:dio/dio.dart';
import 'package:get_storage/get_storage.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// ApiService — semua komunikasi ke backend Netlabs Laravel via HTTP
///
/// [PENTING] Sebelum deploy:
/// 1. Ganti `_baseUrl` dengan URL production VPS
/// 2. Untuk development Android emulator gunakan 10.0.2.2 (alias localhost)
/// 3. Untuk device fisik gunakan IP lokal server (contoh: 192.168.1.x)
class ApiService {
  // ─── Konfigurasi ────────────────────────────────────────────────────────

  /// Ganti base URL sesuai environment:
  /// - Emulator Android : http://10.0.2.2:8000
  /// - Device fisik     : http://192.168.x.x:8000
  /// - Production       : https://vps-anda.com  (atau IP VPS)
  static const String _baseUrl = 'http://10.0.2.2:8000';

  static final Dio _dio = Dio(BaseOptions(
    baseUrl: _baseUrl,
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 30),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
  ));

  static final GetStorage _storage = GetStorage();
  static const _secureStorage = FlutterSecureStorage();
  static const _keyAccessToken = 'access_token';
  static const _keyUserId = 'user_id';

  // ─── Inisialisasi ──────────────────────────────────────────────────────

  /// Panggil sekali saat `main()` — set interceptor Bearer token
  static void init() {
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _secureStorage.read(key: _keyAccessToken);
        if (token != null && token.isNotEmpty) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) {
        if (error.response?.statusCode == 401) {
          clearSession();
        }
        handler.next(error);
      },
    ));
  }

  /// Hapus semua session tersimpan
  static Future<void> clearSession() async {
    await _secureStorage.deleteAll();
    _storage.erase();
  }

  // ─── Helper response ───────────────────────────────────────────────────

  static Map<String, dynamic> _ok(dynamic data, {String message = 'OK'}) {
    if (data is Map<String, dynamic>) return data;
    return {'data': data, 'message': message};
  }

  static Map<String, dynamic> _err(Object e, {String fallback = 'Gagal terhubung ke server'}) {
    if (e is DioException) {
      final msg = e.response?.data?.toString() ?? fallback;
      // Laravel validation errors
      if (e.response?.data is Map && (e.response?.data)['message'] != null) {
        return {'success': false, 'message': (e.response?.data)['message'].toString()};
      }
      return {'success': false, 'message': msg};
    }
    return {'success': false, 'message': fallback};
  }

  // ════════════════════════════════════════════════════════════════════════
  //  AUTH
  // ════════════════════════════════════════════════════════════════════════

  /// Login guru — POST /api/auth/login
  /// Response Laravel: { user: {...}, token: "..." }
  static Future<Map<String, dynamic>> login(String nis, String password) async {
    try {
      final res = await _dio.post('/api/auth/login', data: {
        'nis': nis.trim(),
        'password': password,
      });

      final token = res.data['token'] as String;
      final user = res.data['user'] as Map<String, dynamic>;

      // Simpan token & user
      await _secureStorage.write(key: _keyAccessToken, value: token);
      await _secureStorage.write(key: _keyUserId, value: user['id'].toString());
      _storage.write('token', token);
      _storage.write('user', user);

      return {'success': true, 'token': token, 'user': user};
    } catch (e) {
      return _err(e);
    }
  }

  /// Login siswa — POST /api/auth/login-siswa
  /// Response Laravel: { user: {...}, token: "..." }
  static Future<Map<String, dynamic>> loginSiswa(String nis, String password) async {
    try {
      final res = await _dio.post('/api/auth/login-siswa', data: {
        'nis': nis.trim(),
        'password': password,
      });

      final token = res.data['token'] as String;
      final user = res.data['user'] as Map<String, dynamic>;

      await _secureStorage.write(key: _keyAccessToken, value: token);
      await _secureStorage.write(key: _keyUserId, value: user['id'].toString());
      _storage.write('token', token);
      _storage.write('user', user);

      return {'success': true, 'token': token, 'user': user};
    } catch (e) {
      return _err(e);
    }
  }

  /// Logout — POST /api/auth/logout (Bearer token dihapus di server)
  static Future<void> logout() async {
    try {
      await _dio.post('/api/auth/logout');
    } catch (_) {
      // Abaikan error — tetap clear session lokal
    }
    await clearSession();
  }

  /// Ambil profil user login — GET /api/auth/me
  static Future<Map<String, dynamic>> getMe() async {
    try {
      final res = await _dio.get('/api/auth/me');
      final user = res.data is Map ? res.data as Map<String, dynamic> : (res.data['data'] ?? res.data);
      _storage.write('user', user);
      return {'success': true, 'data': user};
    } catch (e) {
      return _err(e);
    }
  }

  /// Update password — POST /api/auth/update-password
  static Future<Map<String, dynamic>> gantiPassword({
    required String passwordLama,
    required String passwordBaru,
  }) async {
    try {
      await _dio.post('/api/auth/update-password', data: {
        'password_lama': passwordLama,
        'password_baru': passwordBaru,
        'password_baru_confirmation': passwordBaru,
      });
      return {'success': true, 'message': 'Password berhasil diganti'};
    } catch (e) {
      return _err(e);
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  PERTEMUAN
  // ════════════════════════════════════════════════════════════════════════

  /// Ambil semua pertemuan — GET /api/pertemuan
  static Future<List> getPertemuan() async {
    try {
      final res = await _dio.get('/api/pertemuan');
      // Laravel apiResource returns array directly
      if (res.data is List) return res.data as List;
      return (res.data['data'] as List?) ?? [];
    } catch (e) {
      return [];
    }
  }

  /// Detail pertemuan — GET /api/pertemuan/{pertemuan_id}
  static Future<Map<String, dynamic>> getDetailPertemuan(String pertemuanId) async {
    try {
      final res = await _dio.get('/api/pertemuan/$pertemuanId');
      if (res.data is Map) return _ok(res.data);
      return _ok({'data': res.data});
    } catch (e) {
      return _err(e);
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  TOPIK
  // ════════════════════════════════════════════════════════════════════════

  /// Detail topik — GET /api/topik/{topik_id}
  static Future<Map<String, dynamic>> getDetailTopik(String topikId) async {
    try {
      final res = await _dio.get('/api/topik/$topikId');
      if (res.data is Map) return _ok(res.data);
      return _ok({'data': res.data});
    } catch (e) {
      return _err(e);
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  PROGRESS
  // ════════════════════════════════════════════════════════════════════════

  /// Ambil progress user — GET /api/progress
  static Future<List> getProgress() async {
    try {
      final res = await _dio.get('/api/progress');
      if (res.data is List) return res.data as List;
      return (res.data['data'] as List?) ?? [];
    } catch (e) {
      return [];
    }
  }

  /// Tandai topik selesai — POST /api/progress/{topikId}/selesai
  static Future<Map<String, dynamic>> tandaiTopikSelesai(String topikId) async {
    try {
      final res = await _dio.post('/api/progress/$topikId/selesai');
      return _ok(res.data, message: 'Materi berhasil ditandai selesai');
    } catch (e) {
      return _err(e);
    }
  }

  /// Reset progress topik — DELETE /api/progress/{topikId}
  static Future<Map<String, dynamic>> resetProgress(String topikId) async {
    try {
      await _dio.delete('/api/progress/$topikId');
      return {'success': true, 'message': 'Progress direset'};
    } catch (e) {
      return _err(e);
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  MODUL PDF (via topik/pertemuan)
  // ════════════════════════════════════════════════════════════════════════

  /// Modul sudah termasuk dalam response detail pertemuan & topik.
  /// Tidak ada endpoint dedicated, gunakan getDetailPertemuan() atau getDetailTopik()

  // ════════════════════════════════════════════════════════════════════════
  //  KUIS
  // ════════════════════════════════════════════════════════════════════════

  /// Ambil soal kuis — GET /api/kuis/{pertemuanId}/soal
  static Future<List<Map<String, dynamic>>> getSoalKuis(String pertemuanId) async {
    try {
      final res = await _dio.get('/api/kuis/$pertemuanId/soal');
      if (res.data is List) {
        return (res.data as List).map((e) => Map<String, dynamic>.from(e)).toList();
      }
      final list = (res.data['data'] as List?) ?? [];
      return list.map((e) => Map<String, dynamic>.from(e)).toList();
    } catch (e) {
      return [];
    }
  }

  /// Submit jawaban kuis — POST /api/kuis/{pertemuanId}/jawaban
  /// Body: { jawaban: [ { soal_id: "...", jawaban: "A" }, ... ] }
  static Future<Map<String, dynamic>> submitKuis(
      String pertemuanId, List<Map<String, String>> jawaban) async {
    try {
      final body = {
        'jawaban': jawaban,
      };
      final res = await _dio.post('/api/kuis/$pertemuanId/jawaban', data: body);
      return _ok(res.data);
    } catch (e) {
      return _err(e);
    }
  }

  /// Riwayat kuis — GET /api/kuis/riwayat
  static Future<List> getRiwayatKuis() async {
    try {
      final res = await _dio.get('/api/kuis/riwayat');
      if (res.data is List) return res.data as List;
      return (res.data['data'] as List?) ?? [];
    } catch (e) {
      return [];
    }
  }

  /// Cek hasil kuis untuk pertemuan tertentu (dari riwayat)
  static Future<Map<String, dynamic>> cekHasilKuis(String pertemuanId) async {
    try {
      final riwayat = await getRiwayatKuis();
      final hasil = riwayat.firstWhere(
        (r) => r['pertemuan_id'].toString() == pertemuanId,
        orElse: () => null,
      );
      if (hasil != null) {
        return _ok(hasil);
      }
      return {'success': false, 'message': 'Belum mengerjakan kuis'};
    } catch (e) {
      return _err(e);
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  NILAI
  // ════════════════════════════════════════════════════════════════════════

  /// Nilai siswa yang login — GET /api/nilai/siswa/{siswaId}
  static Future<Map<String, dynamic>> getNilaiSaya() async {
    try {
      final siswaId = await _secureStorage.read(key: _keyUserId);
      if (siswaId == null) {
        return {'success': false, 'message': 'User tidak ditemukan'};
      }
      final res = await _dio.get('/api/nilai/siswa/$siswaId');
      return _ok(res.data);
    } catch (e) {
      return _err(e);
    }
  }

  /// Semua nilai (guru) — GET /api/nilai/semua
  static Future<List> getNilaiSemua() async {
    try {
      final res = await _dio.get('/api/nilai/semua');
      if (res.data is List) return res.data as List;
      return (res.data['data'] as List?) ?? [];
    } catch (e) {
      return [];
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  AI CHAT
  // ════════════════════════════════════════════════════════════════════════

  /// Kirim chat ke AI — POST /api/chat
  /// Body: { pertanyaan: "...", pertemuan_id: "...", riwayat_chat: [...] }
  static Future<Map<String, dynamic>> tanyaAI({
    required String pertanyaan,
    required String pertemuanId,
    List<Map<String, dynamic>> riwayatChat = const [],
  }) async {
    try {
      final body = {
        'pertanyaan': pertanyaan,
        'pertemuan_id': pertemuanId,
        'riwayat_chat': riwayatChat,
      };
      final res = await _dio.post('/api/chat', data: body,
          options: Options(receiveTimeout: const Duration(seconds: 60)));
      return _ok(res.data);
    } catch (e) {
      return _err(e, fallback: 'AI Tutor sedang tidak tersedia. Silakan coba lagi.');
    }
  }

  /// Riwayat chat user — GET /api/chat
  static Future<List> getRiwayatChat() async {
    try {
      final res = await _dio.get('/api/chat');
      if (res.data is List) return res.data as List;
      return (res.data['data'] as List?) ?? [];
    } catch (e) {
      return [];
    }
  }

  /// Hapus semua riwayat chat — DELETE /api/chat
  static Future<Map<String, dynamic>> hapusRiwayatChat() async {
    try {
      await _dio.delete('/api/chat');
      return {'success': true, 'message': 'Riwayat chat dihapus'};
    } catch (e) {
      return _err(e);
    }
  }
}