/// Model data satu pertemuan praktikum
class Pertemuan {
  final String id;
  final int nomor;           // Pertemuan 1, 2, ...
  final String judul;
  final int semester;        // 1 atau 2
  final int totalTopik;
  final int topikSelesai;    // Untuk progress
  final bool terkunci;       // true jika pertemuan belum dibuka
  final String estimasi;     // e.g. "2 × 45 menit"
  final String deskripsi;
  final String tujuan;

  Pertemuan({
    required this.id,
    required this.nomor,
    required this.judul,
    required this.semester,
    required this.totalTopik,
    this.topikSelesai = 0,
    this.terkunci = false,
    required this.estimasi,
    required this.deskripsi,
    required this.tujuan,
  });

  double get progress =>
      totalTopik == 0 ? 0 : topikSelesai / totalTopik;
}