import 'package:dio/dio.dart';
import 'package:get_storage/get_storage.dart';

// Service API — satu tempat untuk semua request ke backend
// Nanti ganti BASE_URL dengan URL production
class ApiService {
  // URL backend production (Railway)
  static const String baseUrl = 'https://netlabs-backend-production.up.railway.app';
  // static const String baseUrl = 'http://10.0.2.2:8000'; // Android emulator lokal

  static final Dio _dio = Dio(BaseOptions(
    baseUrl: baseUrl,
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 10),
  ))
    ..interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) {
        // Tambahkan token ke setiap request
        final token = GetStorage().read<String>('token');
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
      onError: (error, handler) {
        // Handle 401 — redirect ke login
        if (error.response?.statusCode == 401) {
          GetStorage().erase();
          // Get.offAllNamed(Routes.LOGIN); // uncomment kalau mau auto logout
        }
        return handler.next(error);
      },
    ));

  // AUTH
  static Future<Map<String, dynamic>> login(String nis, String password) async {
    final res = await _dio.post('/api/auth/login', data: {'nis': nis, 'password': password});
    return res.data;
  }

  static Future<Map<String, dynamic>> getMe() async {
    final res = await _dio.get('/api/auth/me');
    return res.data;
  }

  // PERTEMUAN
  static Future<List> getPertemuan() async {
    final res = await _dio.get('/api/pertemuan');
    return res.data['data'] ?? [];
  }

  static Future<Map<String, dynamic>> getDetailPertemuan(String id) async {
    final res = await _dio.get('/api/pertemuan/$id');
    return res.data['data'];
  }

  // TOPIK
  static Future<List> getTopik(String pertemuanId) async {
    final res = await _dio.get('/api/topik/$pertemuanId');
    return res.data['data'] ?? [];
  }

  static Future<void> tandaiTopikDibaca(String topikId) async {
    await _dio.post('/api/topik/$topikId/baca');
  }

  // KUIS
  static Future<List> getSoalKuis(String pertemuanId) async {
    final res = await _dio.get('/api/kuis/$pertemuanId');
    return res.data['data'] ?? [];
  }

  static Future<Map<String, dynamic>> submitKuis(
      String pertemuanId, List<Map<String, String>> jawaban) async {
    final res = await _dio.post('/api/kuis/submit', data: {
      'pertemuan_id': pertemuanId,
      'jawaban': jawaban,
    });
    return res.data;
  }

  // NILAI
  static Future<Map<String, dynamic>> getNilaiSaya() async {
    final res = await _dio.get('/api/nilai/saya');
    return res.data;
  }

  // CHAT / RAG
  static Future<Map<String, dynamic>> tanyaAI({
    required String pertanyaan,
    required String pertemuanId,
    List<Map<String, dynamic>> riwayatChat = const [],
  }) async {
    final res = await _dio.post('/api/chat/tanya', data: {
      'pertanyaan': pertanyaan,
      'pertemuan_id': pertemuanId,
      'riwayat_chat': riwayatChat,
    });
    return res.data;
  }

  static Future<List> getRiwayatChat(String siswaId) async {
    final res = await _dio.get('/api/chat/riwayat/$siswaId');
    return res.data['data'] ?? [];
  }
}
