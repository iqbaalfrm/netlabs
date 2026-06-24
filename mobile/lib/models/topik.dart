/// Model satu topik dalam pertemuan
class Topik {
  final String id;
  final String pertemuanId;
  final String judul;
  final String konten;       // Isi materi panjang (dummy lorem ipsum)
  final bool selesai;        // Sudah ditandai selesai oleh siswa

  Topik({
    required this.id,
    required this.pertemuanId,
    required this.judul,
    required this.konten,
    this.selesai = false,
  });
}