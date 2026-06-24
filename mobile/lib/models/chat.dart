/// Model satu pesan chat AI
class ChatMessage {
  final String id;
  final String pertemuanId;
  final String pertanyaan;
  final String jawaban;
  final String? sumber;       // Label sumber dari RAG (misal: "Modul 1 - Pengenalan Jaringan")
  final DateTime waktu;
  final String peran;         // 'siswa' atau 'ai'

  ChatMessage({
    required this.id,
    required this.pertemuanId,
    required this.pertanyaan,
    required this.jawaban,
    this.sumber,
    required this.waktu,
    required this.peran,
  });

  factory ChatMessage.fromJson(Map<String, dynamic> json) {
    return ChatMessage(
      id: json['id'] ?? '',
      pertemuanId: json['pertemuan_id'] ?? '',
      pertanyaan: json['pertanyaan'] ?? '',
      jawaban: json['jawaban'] ?? '',
      sumber: json['sumber'],
      waktu: json['waktu'] != null
          ? DateTime.parse(json['waktu'])
          : DateTime.now(),
      peran: json['peran'] ?? 'siswa',
    );
  }

  Map<String, dynamic> toJson() => {
        'pertanyaan': pertanyaan,
        'jawaban': jawaban,
        'peran': peran,
      };
}