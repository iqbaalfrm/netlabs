import 'package:dio/dio.dart';
import 'package:get_storage/get_storage.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// ApiService — semua komunikasi ke backend Netlabs via HTTP
/// 
/// [PENTING] Sebelum deploy:
/// 1. Ganti `_baseUrl` dengan URL production Railway / VPS
/// 2. Untuk development Android emulator gunakan 10.0.2.2 (alias localhost)
/// 3. Untuk device fisik gunakan IP lokal server (contoh: 192.168.1.x)
class ApiService {
  // ─── Konfigurasi ────────────────────────────────────────────────────────
  
  /// Ganti base URL sesuai environment:
  /// - Emulator Android : http://10.0.2.2:8000
  /// - Device fisik     : http://192.168.x.x:8000
  /// - Production       : https://netlabs-api.railway.app  (atau VPS)
  static const String _baseUrl = 'http://10.0.2.2:8000';

  static final Dio _dio = Dio(BaseOptions(
    baseUrl: _baseUrl,
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 30),
    headers: {'Content-Type': 'application/json'},
  ));

  static final GetStorage _storage = GetStorage();
  static const _secureStorage = FlutterSecureStorage();
  static const _keyAccessToken = 'access_token';
  static const _keyUserId = 'user_id';

  // ─── Inisialisasi ──────────────────────────────────────────────────────

  /// Panggil sekali saat `main()` — set interceptor JWT
  static void init() {
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _secureStorage.read(key: _keyAccessToken);
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) {
        if (error.response?.statusCode == 401) {
          // Token expired — bisa redirect ke login
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

  /// Bungkus response Dio menjadi Map yang aman
  static Map<String, dynamic> _ok(dynamic data, {String message = 'OK'}) {
    if (data is Map<String, dynamic>) return data;
    return {'data': data, 'message': message};
  }

  static Map<String, dynamic> _err(Object e, {String fallback = 'Gagal terhubung ke server'}) {
    if (e is DioException) {
      final msg = e.response?.data?['detail'] ?? fallback;
      return {'success': false, 'message': msg.toString()};
    }
    return {'success': false, 'message': fallback};
  }

  // ════════════════════════════════════════════════════════════════════════
  //  AUTH
  // ════════════════════════════════════════════════════════════════════════

  /// Login siswa/guru — POST /api/auth/login
  static Future<Map<String, dynamic>> login(String nis, String password) async {
    try {
      final res = await _dio.post('/api/auth/login', data: {
        'nis': nis.trim(),
        'password': password,
      });

      final token = res.data['data']['token'];
      final user = res.data['data']['user'];

      // Simpan token & user
      await _secureStorage.write(key: _keyAccessToken, value: token);
      await _secureStorage.write(key: _keyUserId, value: user['id']);
      _storage.write('token', token);
      _storage.write('user', user);

      return {'success': true, 'token': token, 'user': user};
    } catch (e) {
      return _err(e);
    }
  }

  /// Logout — POST /api/auth/logout
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
      _storage.write('user', res.data['data']);
      return _ok(res.data);
    } catch (e) {
      return _err(e);
    }
  }

  /// Update profil — PUT /api/auth/profil
  static Future<Map<String, dynamic>> updateProfil({String? nama, String? sekolah}) async {
    try {
      final body = <String, dynamic>{};
      if (nama != null) body['nama'] = nama;
      if (sekolah != null) body['sekolah'] = sekolah;
      await _dio.put('/api/auth/profil', data: body);
      return {'success': true, 'message': 'Profil berhasil diperbarui'};
    } catch (e) {
      return _err(e);
    }
  }

  /// Ganti password — POST /api/auth/ganti-password
  static Future<Map<String, dynamic>> gantiPassword({
    required String passwordLama,
    required String passwordBaru,
  }) async {
    try {
      final body = <String, dynamic>{'password_baru': passwordBaru};
      if (passwordLama.isNotEmpty) body['password_lama'] = passwordLama;
      await _dio.post('/api/auth/ganti-password', data: body);
      return {'success': true, 'message': 'Sandi berhasil diganti'};
    } catch (e) {
      return _err(e);
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  PERTEMUAN
  // ════════════════════════════════════════════════════════════════════════

  /// Ambil semua pertemuan — GET /api/pertemuan/
  static Future<List> getPertemuan({int page = 1, int limit = 20}) async {
    try {
      final res = await _dio.get('/api/pertemuan/', queryParameters: {
        'page': page, 'limit': limit,
      });
      return (res.data['data'] as List?) ?? [];
    } catch (e) {
      return [];
    }
  }

  /// Detail pertemuan + daftar topik — GET /api/pertemuan/{pertemuan_id}
  static Future<Map<String, dynamic>> getDetailPertemuan(String pertemuanId) async {
    try {
      final res = await _dio.get('/api/pertemuan/$pertemuanId');
      return _ok(res.data);
    } catch (e) {
      return _err(e);
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  TOPIK
  // ════════════════════════════════════════════════════════════════════════

  /// Ambil daftar topik — GET /api/topik/{pertemuan_id}
  static Future<List> getTopik(String pertemuanId, {int page = 1, int limit = 20}) async {
    try {
      final res = await _dio.get('/api/topik/$pertemuanId', queryParameters: {
        'page': page, 'limit': limit,
      });
      return (res.data['data'] as List?) ?? [];
    } catch (e) {
      return [];
    }
  }

  /// Detail topik — GET /api/topik/detail/{topik_id}
  static Future<Map<String, dynamic>> getDetailTopik(String topikId) async {
    try {
      final res = await _dio.get('/api/topik/detail/$topikId');
      return _ok(res.data);
    } catch (e) {
      return _err(e);
    }
  }

  /// Tandai topik sudah dibaca — POST /api/topik/{topik_id}/baca
  static Future<Map<String, dynamic>> tandaiTopikDibaca(String topikId) async {
    try {
      await _dio.post('/api/topik/$topikId/baca');
      return {'success': true, 'message': 'Materi berhasil ditandai selesai'};
    } catch (e) {
      return _err(e);
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  MODUL PDF
  // ════════════════════════════════════════════════════════════════════════

  /// Ambil daftar modul — GET /api/modul/{pertemuan_id}
  static Future<List> getModul(String pertemuanId) async {
    try {
      final res = await _dio.get('/api/modul/$pertemuanId');
      return (res.data['data'] as List?) ?? [];
    } catch (e) {
      return [];
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  KUIS
  // ════════════════════════════════════════════════════════════════════════

  /// Ambil soal kuis — GET /api/kuis/{pertemuan_id}
  static Future<List> getSoalKuis(String pertemuanId) async {
    try {
      final res = await _dio.get('/api/kuis/$pertemuanId');
      return (res.data['data'] as List?) ?? [];
    } catch (e) {
      return [];
    }
  }

  /// Cek apakah kuis sudah dikerjakan — GET /api/kuis/{pertemuan_id}/hasil
  static Future<Map<String, dynamic>> cekHasilKuis(String pertemuanId) async {
    try {
      final res = await _dio.get('/api/kuis/$pertemuanId/hasil');
      return _ok(res.data);
    } catch (e) {
      return _err(e);
    }
  }

  /// Submit jawaban kuis — POST /api/kuis/submit
  static Future<Map<String, dynamic>> submitKuis(
      String pertemuanId, List<Map<String, String>> jawaban) async {
    try {
      final body = {
        'pertemuan_id': pertemuanId,
        'jawaban': jawaban,
      };
      final res = await _dio.post('/api/kuis/submit', data: body);
      return _ok(res.data);
    } catch (e) {
      return _err(e);
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  NILAI
  // ════════════════════════════════════════════════════════════════════════

  /// Nilai siswa yang login — GET /api/nilai/saya
  static Future<Map<String, dynamic>> getNilaiSaya({int page = 1, int limit = 10}) async {
    try {
      final res = await _dio.get('/api/nilai/saya', queryParameters: {
        'page': page, 'limit': limit,
      });
      return _ok(res.data);
    } catch (e) {
      return _err(e);
    }
  }

  // ════════════════════════════════════════════════════════════════════════
  //  AI CHAT / RAG
  // ════════════════════════════════════════════════════════════════════════

  /// Tanya AI Tutor RAG — POST /api/chat/tanya
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
      // Timeout lebih lama karena AI butuh waktu
      final res = await _dio.post('/api/chat/tanya', data: body,
          options: Options(receiveTimeout: const Duration(seconds: 40)));
      return _ok(res.data);
    } catch (e) {
      return _err(e, fallback: 'AI Tutor sedang tidak tersedia. Silakan coba lagi.');
    }
  }

  /// Riwayat chat per pertemuan — GET /api/chat/riwayat/{siswa_id}/{pertemuan_id}
  static Future<List> getRiwayatChat(String siswaId, String pertemuanId,
      {int page = 1, int limit = 30}) async {
    try {
      final res = await _dio.get('/api/chat/riwayat/$siswaId/$pertemuanId',
          queryParameters: {'page': page, 'limit': limit});
      return (res.data['data'] as List?) ?? [];
    } catch (e) {
      return [];
    }
  }
}