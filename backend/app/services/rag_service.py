# RAG Service — inti fitur AI Tutor
# RAG = Retrieval-Augmented Generation
# Cari konteks dari modul PDF → kirim ke Claude → jawab pertanyaan
#
# CATATAN: chromadb dan sentence-transformers adalah dependency berat.
# Diinisialisasi secara lazy (saat pertama kali dipakai), bukan saat startup.

from app.config import CHROMA_PATH, ANTHROPIC_API_KEY

# Flag apakah RAG sudah siap
_rag_ready = False
_chroma = None
_collection = None
_embedder = None
_claude = None


def _init_rag():
    """Inisialisasi RAG saat pertama kali dipakai."""
    global _rag_ready, _chroma, _collection, _embedder, _claude
    if _rag_ready:
        return True

    try:
        import chromadb
        from sentence_transformers import SentenceTransformer
        import anthropic

        _chroma = chromadb.PersistentClient(path=CHROMA_PATH)
        _collection = _chroma.get_or_create_collection("modul_jaringan")
        _embedder = SentenceTransformer("all-MiniLM-L6-v2")
        _claude = anthropic.Anthropic(api_key=ANTHROPIC_API_KEY)
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


async def buat_jawaban(pertanyaan: str, konteks_chunks: list, riwayat_chat: list) -> dict:
    """Buat jawaban AI berdasarkan konteks modul."""
    if not _init_rag():
        # Fallback: jawaban tanpa AI saat dependency belum ada
        return {
            "jawaban": (
                "Maaf, AI Tutor belum tersedia. "
                "Install dependency RAG dan konfigurasi ANTHROPIC_API_KEY."
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
        response = _claude.messages.create(
            model="claude-3-haiku-20240307",
            max_tokens=1024,
            system=system,
            messages=[{"role": "user", "content": pesan}],
        )
        jawaban = response.content[0].text
    except Exception as e:
        jawaban = f"Maaf, AI sedang tidak tersedia. ({str(e)[:50]})"

    return {
        "jawaban": jawaban,
        "label_sumber": "Modul Praktikum" if konteks_chunks else None,
    }
