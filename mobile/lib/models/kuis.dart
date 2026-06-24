/// Model soal pilihan ganda
class SoalKuis {
  final String id;
  final String pertemuanId;
  final String pertanyaan;
  final String pilihanA;
  final String pilihanB;
  final String pilihanC;
  final String pilihanD;
  final int? jawabanBenar;  // index 0-3, disembunyikan dari siswa
  final int? dipilih;        // index jawaban yg dipilih siswa, null jika belum

  SoalKuis({
    required this.id,
    required this.pertemuanId,
    required this.pertanyaan,
    required this.pilihanA,
    required this.pilihanB,
    required this.pilihanC,
    required this.pilihanD,
    this.jawabanBenar,
    this.dipilih,
  });

  factory SoalKuis.fromJson(Map<String, dynamic> json) {
    return SoalKuis(
      id: json['id'] ?? '',
      pertemuanId: json['pertemuan_id'] ?? '',
      pertanyaan: json['pertanyaan'] ?? '',
      pilihanA: json['pilihan_a'] ?? '',
      pilihanB: json['pilihan_b'] ?? '',
      pilihanC: json['pilihan_c'] ?? '',
      pilihanD: json['pilihan_d'] ?? '',
      jawabanBenar: json['jawaban_benar'],
      dipilih: json['dipilih'],
    );
  }

  List<String> get pilihan => [pilihanA, pilihanB, pilihanC, pilihanD];

  /// Konversi ke format jawaban untuk API
  Map<String, dynamic> toJawabanJson() => {
        'soal_id': id,
        'jawaban_dipilih': dipilih,
      };
}

/// Model hasil kuis
class HasilKuis {
  final String id;
  final String pertemuanId;
  final int benar;
  final int total;
  final DateTime selesaiPada;
  final String? rekomendasiAi; // Rekomendasi dari Claude API

  HasilKuis({
    required this.id,
    required this.pertemuanId,
    required this.benar,
    required this.total,
    required this.selesaiPada,
    this.rekomendasiAi,
  });

  factory HasilKuis.fromJson(Map<String, dynamic> json) {
    return HasilKuis(
      id: json['id'] ?? '',
      pertemuanId: json['pertemuan_id'] ?? '',
      benar: json['benar'] ?? 0,
      total: json['total'] ?? 0,
      selesaiPada: json['selesai_pada'] != null
          ? DateTime.parse(json['selesai_pada'])
          : DateTime.now(),
      rekomendasiAi: json['rekomendasi_ai'],
    );
  }

  double get persentase => total == 0 ? 0 : (benar / total * 100);
}

/// Model modul (PDF)
class Modul {
  final String id;
  final String pertemuanId;
  final String judul;
  final String? urlPdf;       // URL untuk download/view PDF
  final DateTime? uploadedAt;

  Modul({
    required this.id,
    required this.pertemuanId,
    required this.judul,
    this.urlPdf,
    this.uploadedAt,
  });

  factory Modul.fromJson(Map<String, dynamic> json) {
    return Modul(
      id: json['id'] ?? '',
      pertemuanId: json['pertemuan_id'] ?? '',
      judul: json['judul'] ?? '',
      urlPdf: json['url_pdf'],
      uploadedAt: json['uploaded_at'] != null
          ? DateTime.parse(json['uploaded_at'])
          : null,
    );
  }
}