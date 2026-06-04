# Pydantic schemas — validasi request dan response
from pydantic import BaseModel, field_validator
from typing import Optional, List


# ── AUTH ──
class LoginRequest(BaseModel):
    nis: str
    password: str

    @field_validator('nis', 'password')
    @classmethod
    def tidak_boleh_kosong(cls, v: str) -> str:
        if not v.strip():
            raise ValueError('Tidak boleh kosong')
        return v


# ── PERTEMUAN ──
class PertemuanCreate(BaseModel):
    judul: str
    deskripsi: str
    nomor_urut: int
    warna_hex: str = "#2D7DD2"


class PertemuanUpdate(BaseModel):
    judul: Optional[str] = None
    deskripsi: Optional[str] = None
    warna_hex: Optional[str] = None


# ── TOPIK ──
class TopikCreate(BaseModel):
    pertemuan_id: str
    judul: str
    isi_materi: str
    nomor_urut: int


class TopikUpdate(BaseModel):
    judul: Optional[str] = None
    isi_materi: Optional[str] = None


# ── CHAT / RAG ──
class PesanRiwayat(BaseModel):
    dari_siswa: bool
    teks: str


class ChatRequest(BaseModel):
    pertanyaan: str
    pertemuan_id: str
    riwayat_chat: List[PesanRiwayat] = []

    @field_validator('pertanyaan')
    @classmethod
    def pertanyaan_tidak_kosong(cls, v: str) -> str:
        if not v.strip():
            raise ValueError('Pertanyaan tidak boleh kosong')
        return v


# ── KUIS ──
class SoalCreate(BaseModel):
    pertemuan_id: str
    pertanyaan: str
    pilihan_a: str
    pilihan_b: str
    pilihan_c: str
    pilihan_d: str
    jawaban_benar: str

    @field_validator('jawaban_benar')
    @classmethod
    def harus_a_b_c_d(cls, v: str) -> str:
        if v not in ['a', 'b', 'c', 'd']:
            raise ValueError('Jawaban benar harus a, b, c, atau d')
        return v

    penjelasan: str = ""


class JawabanItem(BaseModel):
    soal_id: str
    jawaban: str


class SubmitKuis(BaseModel):
    pertemuan_id: str
    jawaban: List[JawabanItem]
