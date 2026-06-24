/// Model data siswa (dari JWT / login response)
class Siswa {
  final String nis;
  final String nama;
  final String kelas;         // X TKJ 1, X TKJ 2
  final String? token;

  Siswa({
    required this.nis,
    required this.nama,
    required this.kelas,
    this.token,
  });

  factory Siswa.fromJson(Map<String, dynamic> json) => Siswa(
        nis: json['nis'] ?? '',
        nama: json['nama'] ?? '',
        kelas: json['kelas'] ?? '',
        token: json['token'],
      );
}