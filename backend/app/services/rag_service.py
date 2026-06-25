# RAG Service — inti fitur AI Tutor
# RAG = Retrieval-Augmented Generation
# Cari konteks dari modul PDF → kirim ke Gemini → jawab pertanyaan
#
# CATATAN: chromadb dan sentence-transformers adalah dependency berat.
# Diinisialisasi secara lazy (saat pertama kali dipakai), bukan saat startup.
#
# AI Engine: Google Gemini API (key yang sama dengan Quiz Generator di Laravel)

import httpx
from app.config import CHROMA_PATH, GEMINI_API_KEY, GEMINI_MODEL

GEMINI_URL = "https://generativelanguage.googleapis.com/v1beta/models"

# Flag apakah RAG sudah siap
_rag_ready = False
_chroma = None
_collection = None
_embedder = None


def _init_rag():
    """Inisialisasi RAG saat pertama kali dipakai."""
    global _rag_ready, _chroma, _collection, _embedder
    if _rag_ready:
        return True

    try:
        import chromadb
        from sentence_transformers import SentenceTransformer

        _chroma = chromadb.PersistentClient(path=CHROMA_PATH)
        _collection = _chroma.get_or_create_collection("modul_jaringan")
        _embedder = SentenceTransformer("all-MiniLM-L6-v2")
        _rag_ready = True
        return True
    except ImportError:
        # RAG dependencies belum diinstall
        return False
    except Exception:
        return False


def index_pdf(chunks: list, pertemuan_id: str, nama_file: str):
    """Simpan chunks teks dari PDF ke ChromaDB."""
    if not _init_rag():
        raise RuntimeError("RAG belum siap. Install chromadb dan sentence-transformers.")

    # Hapus chunks lama untuk file yang sama (re-index friendly)
    try:
        _collection.delete(where={"pertemuan_id": pertemuan_id, "file": nama_file})
    except Exception:
        pass  # Belum ada data lama, lanjut

    for i, chunk in enumerate(chunks):
        chunk_id = f"{pertemuan_id}_{nama_file}_{i}"
        embedding = _embedder.encode(chunk).tolist()
        _collection.add(
            ids=[chunk_id],
            embeddings=[embedding],
            documents=[chunk],
            metadatas=[{"pertemuan_id": pertemuan_id, "file": nama_file}],
        )


def cari_konteks(pertanyaan: str, pertemuan_id: str, top_k: int = 3) -> list:
    """Cari chunk paling relevan dari ChromaDB."""
    if not _init_rag() or _collection.count() == 0:
        return []

    embedding = _embedder.encode(pertanyaan).tolist()
    hasil = _collection.query(
        query_embeddings=[embedding],
        n_results=top_k,
        where={"pertemuan_id": pertemuan_id},
    )
    return hasil["documents"][0] if hasil and hasil["documents"] else []


def hapus_index(pertemuan_id: str, nama_file: str = None):
    """Hapus chunks dari ChromaDB untuk pertemuan dan/atau file tertentu."""
    if not _init_rag():
        return False

    try:
        if nama_file:
            _collection.delete(where={"pertemuan_id": pertemuan_id, "file": nama_file})
        else:
            _collection.delete(where={"pertemuan_id": pertemuan_id})
        return True
    except Exception:
        return False


def get_status() -> dict:
    """Cek status RAG: dependency, jumlah chunks, API key."""
    status = {
        "rag_ready": False,
        "chroma_connected": False,
        "total_chunks": 0,
        "gemini_api_key_set": bool(
            GEMINI_API_KEY and GEMINI_API_KEY != "your_gemini_api_key"
        ),
        "gemini_model": GEMINI_MODEL,
        "embedder_loaded": False,
    }

    try:
        if _init_rag():
            status["rag_ready"] = True
            status["chroma_connected"] = True
            status["embedder_loaded"] = True
            status["total_chunks"] = _collection.count()
    except Exception:
        pass

    return status


async def buat_jawaban(pertanyaan: str, konteks_chunks: list, riwayat_chat: list) -> dict:
    """Buat jawaban AI berdasarkan konteks modul menggunakan Gemini API."""
    if not GEMINI_API_KEY or GEMINI_API_KEY == "your_gemini_api_key":
        return {
            "jawaban": (
                "Maaf, AI Tutor belum tersedia. "
                "Konfigurasi GEMINI_API_KEY di file .env backend."
            ),
            "label_sumber": None,
        }

    system = (
        "Kamu adalah tutor AI praktikum Jaringan Komputer Dasar "
        "untuk siswa SMK. Jawab pertanyaan HANYA berdasarkan "
        "konteks modul berikut. Jika tidak ada di modul, katakan "
        "kamu tidak menemukan informasinya. Gunakan bahasa yang "
        "mudah dipahami siswa SMK.\n\n"
    )
    if konteks_chunks:
        system += "=== KONTEKS MODUL ===\n"
        for i, chunk in enumerate(konteks_chunks):
            system += f"\n[Bagian {i+1}]\n{chunk}\n"
    else:
        system += "(Belum ada modul untuk pertemuan ini. Jawab berdasarkan pengetahuan umum.)\n"

    pesan = ""
    if riwayat_chat:
        pesan += "Riwayat:\n"
        for p in riwayat_chat[-5:]:
            role = "Siswa" if p.get("dari_siswa") else "AI"
            pesan += f"{role}: {p.get('teks', '')}\n"
        pesan += "\n"
    pesan += f"Pertanyaan: {pertanyaan}"

    try:
        url = f"{GEMINI_URL}/{GEMINI_MODEL}:generateContent?key={GEMINI_API_KEY}"
        payload = {
            "system_instruction": {
                "parts": [{"text": system}]
            },
            "contents": [
                {
                    "parts": [{"text": pesan}]
                }
            ],
            "generationConfig": {
                "maxOutputTokens": 1024,
                "temperature": 0.7,
            },
        }

        async with httpx.AsyncClient(timeout=60.0) as client:
            response = await client.post(url, json=payload)
            response.raise_for_status()

        body = response.json()
        jawaban = body["candidates"][0]["content"]["parts"][0]["text"]

    except httpx.TimeoutException:
        jawaban = "Maaf, AI membutuhkan waktu terlalu lama. Coba lagi sebentar."
    except httpx.HTTPStatusError as e:
        error_msg = str(e.response.status_code)
        jawaban = f"Maaf, AI sedang tidak tersedia. (HTTP {error_msg})"
    except Exception as e:
        jawaban = f"Maaf, AI sedang tidak tersedia. ({str(e)[:80]})"

    return {
        "jawaban": jawaban,
        "label_sumber": "Modul Praktikum" if konteks_chunks else None,
    }