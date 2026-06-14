import 'package:dio/dio.dart';
import 'package:get_storage/get_storage.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import '../../app/routes/app_routes.dart';

class ApiService {
  static const String _baseUrlDefault = 'https://netlabs-backend-production.up.railway.app';

  static String get baseUrl => _baseUrlDefault;

  static final Dio _dio = Dio(BaseOptions(
    baseUrl: _baseUrlDefault,
    connectTimeout: const Duration(seconds: 15),
    receiveTimeout: const Duration(seconds: 15),
    headers: {'Content-Type': 'application/json'},
  ));

  static const _secureStorage = FlutterSecureStorage(
    aOptions: AndroidOptions(encryptedSharedPreferences: true),
  );

  static final GetStorage _storage = GetStorage();

  static const _keyAccessToken = 'access_token';
  static const _keyUserId = 'user_id';

  static void init() {
    _dio.interceptors.clear();
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _secureStorage.read(key: _keyAccessToken);
        if (token != null && token.isNotEmpty) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) async {
        if (error.response?.statusCode == 401) {
          final refreshed = await _refreshToken();
          if (refreshed) {
            final opts = error.requestOptions;
            final newToken = await _secureStorage.read(key: _keyAccessToken);
            opts.headers['Authorization'] = 'Bearer $newToken';
            final response = await _dio.fetch(opts);
            return handler.resolve(response);
          }
          await clearSession();
          Get.offAllNamed(Routes.login);
        }
        handler.next(error);
      },
    ));
  }

  static Future<bool> _refreshToken() async {
    try {
      final token = await _secureStorage.read(key: _keyAccessToken);
      if (token == null) return false;

      final response = await Dio(BaseOptions(
        baseUrl: _baseUrlDefault,
        connectTimeout: const Duration(seconds: 10),
        receiveTimeout: const Duration(seconds: 10),
      )).post('/api/auth/refresh', options: Options(
        headers: {'Authorization': 'Bearer $token'},
      ));

      if (response.statusCode == 200) {
        final newToken = response.data['data']['token'];
        await _secureStorage.write(key: _keyAccessToken, value: newToken);
        return true;
      }
      return false;
    } catch (_) {
      return false;
    }
  }

  static Future<void> clearSession() async {
    await _secureStorage.deleteAll();
    _storage.erase();
  }

  static String _extractError(dynamic error) {
    if (error is DioException) {
      if (error.response?.statusCode == 429) {
        final detail = error.response?.data;
        if (detail is Map) return detail['detail'] ?? 'Terlalu banyak permintaan. Coba lagi nanti.';
        return 'Terlalu banyak permintaan. Coba lagi nanti.';
      }
      if (error.response?.data is Map) {
        return error.response?.data['detail'] ?? error.response?.data['message'] ?? 'Terjadi kesalahan';
      }
      if (error.type == DioExceptionType.connectionTimeout ||
          error.type == DioExceptionType.receiveTimeout) {
        return 'Koneksi timeout. Periksa internet kamu.';
      }
      if (error.type == DioExceptionType.connectionError) {
        return 'Tidak bisa terhubung ke server.';
      }
      return 'Terjadi kesalahan jaringan';
    }
    return 'Terjadi kesalahan';
  }

  // ============ AUTH ============

  static Future<Map<String, dynamic>> login(String nis, String password) async {
    if (nis.trim().isEmpty || password.isEmpty) {
      return {'success': false, 'message': 'NIS dan password wajib diisi'};
    }

    try {
      final response = await _dio.post('/api/auth/login', data: {
        'nis': nis.trim(),
        'password': password,
      });
      final data = response.data['data'];

      await _secureStorage.write(key: _keyAccessToken, value: data['token']);
      await _secureStorage.write(key: _keyUserId, value: data['user']['id']);
      _storage.write('user', data['user']);
      _storage.write('token', data['token']);

      return {'success': true, ...data};
    } catch (e) {
      return {'success': false, 'message': _extractError(e)};
    }
  }

  static Future<void> logout() async {
    try {
      await _dio.post('/api/auth/logout');
    } catch (_) {}
    await clearSession();
  }

  static Future<Map<String, dynamic>> getMe() async {
    try {
      final response = await _dio.get('/api/auth/me');
      return response.data['data'];
    } catch (e) {
      return {};
    }
  }

  static Future<Map<String, dynamic>> updateProfil({String? nama, String? sekolah}) async {
    try {
      final response = await _dio.put('/api/auth/profil', queryParameters: {
        if (nama != null) 'nama': nama.trim(),
        if (sekolah != null) 'sekolah': sekolah.trim(),
      });
      return {'success': true, 'message': response.data['message']};
    } catch (e) {
      return {'success': false, 'message': _extractError(e)};
    }
  }

  static Future<Map<String, dynamic>> gantiPassword({
    required String passwordLama,
    required String passwordBaru,
  }) async {
    if (passwordBaru.length < 6) {
      return {'success': false, 'message': 'Password baru minimal 6 karakter'};
    }

    try {
      final response = await _dio.post('/api/auth/ganti-password', queryParameters: {
        'password_lama': passwordLama,
        'password_baru': passwordBaru,
      });
      return {'success': true, 'message': response.data['message']};
    } catch (e) {
      return {'success': false, 'message': _extractError(e)};
    }
  }

  // ============ PERTEMUAN ============

  static Future<List> getPertemuan() async {
    try {
      final response = await _dio.get('/api/pertemuan/');
      return response.data['data'] ?? [];
    } catch (_) {
      return [];
    }
  }

  static Future<Map<String, dynamic>> getDetailPertemuan(String id) async {
    try {
      final response = await _dio.get('/api/pertemuan/$id');
      return response.data['data'] ?? {};
    } catch (_) {
      return {};
    }
  }

  // ============ TOPIK ============

  static Future<List> getTopik(String pertemuanId) async {
    try {
      final response = await _dio.get('/api/topik/$pertemuanId');
      return response.data['data'] ?? [];
    } catch (_) {
      return [];
    }
  }

  static Future<Map<String, dynamic>> getDetailTopik(String topikId) async {
    try {
      final response = await _dio.get('/api/topik/detail/$topikId');
      return response.data['data'] ?? {};
    } catch (_) {
      return {};
    }
  }

  static Future<Map<String, dynamic>> tandaiTopikDibaca(String topikId) async {
    try {
      final response = await _dio.post('/api/topik/$topikId/baca');
      return {'success': true, 'message': response.data['message']};
    } catch (e) {
      return {'success': false, 'message': _extractError(e)};
    }
  }

  // ============ MODUL PDF ============

  static Future<List> getModul(String pertemuanId) async {
    try {
      final response = await _dio.get('/api/modul/$pertemuanId');
      return response.data['data'] ?? [];
    } catch (_) {
      return [];
    }
  }

  // ============ KUIS ============

  static Future<List> getSoalKuis(String pertemuanId) async {
    try {
      final response = await _dio.get('/api/kuis/$pertemuanId');
      return response.data['data'] ?? [];
    } catch (_) {
      return [];
    }
  }

  static Future<Map<String, dynamic>> cekHasilKuis(String pertemuanId) async {
    try {
      final response = await _dio.get('/api/kuis/$pertemuanId/hasil');
      return response.data;
    } catch (_) {
      return {'sudah_dikerjakan': false, 'data': null};
    }
  }

  static Future<Map<String, dynamic>> submitKuis(
      String pertemuanId, List<Map<String, String>> jawaban) async {
    try {
      final response = await _dio.post('/api/kuis/submit', data: {
        'pertemuan_id': pertemuanId,
        'jawaban': jawaban.map((j) => {
          return {'soal_id': j['soal_id'], 'jawaban': j['jawaban']};
        }).toList(),
      });
      return {'success': true, ...response.data['data']};
    } catch (e) {
      if (e is DioException && e.response?.statusCode == 409) {
        return {'success': false, 'message': 'Kuis ini sudah pernah dikerjakan'};
      }
      return {'success': false, 'message': _extractError(e)};
    }
  }

  // ============ NILAI ============

  static Future<Map<String, dynamic>> getNilaiSaya() async {
    try {
      final response = await _dio.get('/api/nilai/saya');
      return response.data['data'] ?? {};
    } catch (_) {
      return {'rata_rata': 0, 'total_kuis': 0, 'hasil': []};
    }
  }

  // ============ CHAT / RAG ============

  static Future<Map<String, dynamic>> tanyaAI({
    required String pertanyaan,
    required String pertemuanId,
    List<Map<String, dynamic>> riwayatChat = const [],
  }) async {
    final sanitized = pertanyaan.trim();
    if (sanitized.isEmpty) {
      return {'success': false, 'jawaban': 'Pertanyaan tidak boleh kosong.'};
    }
    if (sanitized.length > 1000) {
      return {'success': false, 'jawaban': 'Pertanyaan terlalu panjang (maks 1000 karakter).'};
    }

    try {
      final response = await _dio.post('/api/chat/tanya', data: {
        'pertanyaan': sanitized,
        'pertemuan_id': pertemuanId,
        'riwayat_chat': riwayatChat.take(10).map((c) => {
          return {'dari_siswa': c['dariSiswa'] ?? c['dari_siswa'], 'teks': c['teks']};
        }).toList(),
      });
      return {
        'success': true,
        'jawaban': response.data['jawaban'],
        'label_sumber': response.data['label_sumber'],
      };
    } catch (e) {
      if (e is DioException && e.response?.statusCode == 429) {
        return {'success': false, 'jawaban': 'Terlalu banyak pertanyaan. Tunggu sebentar ya.'};
      }
      return {'success': false, 'jawaban': 'Maaf, AI Tutor sedang tidak tersedia. Coba lagi nanti.'};
    }
  }

  static Future<List> getRiwayatChat(String pertemuanId) async {
    try {
      final userId = await _secureStorage.read(key: _keyUserId) ?? '';
      final response = await _dio.get('/api/chat/riwayat/$userId/$pertemuanId');
      return response.data['data'] ?? [];
    } catch (_) {
      return [];
    }
  }
}
