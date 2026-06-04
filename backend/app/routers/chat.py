# Router AI Chat — endpoint RAG (fitur utama skripsi)
from fastapi import APIRouter, Depends
from datetime import datetime
from app.database import db
from app.middleware.auth import get_current_user, siswa_only
from app.schemas import ChatRequest
from app.services import rag_service
from app.middleware.rate_limit import cek_rate_limit

router = APIRouter()


@router.post("/tanya")
async def tanya_ai(request: ChatRequest, user: dict = Depends(siswa_only)):
    """
    Endpoint utama RAG — siswa bertanya, AI menjawab dari modul.

    Proses:
    1. Cari konteks dari ChromaDB
    2. Kirim ke Claude API dengan konteks
    3. Simpan percakapan ke database
    """
    waktu = datetime.utcnow().isoformat()
    riwayat = [p.model_dump() for p in request.riwayat_chat]

    # Cek rate limit sebelum panggil AI
    cek_rate_limit(user["id"])

    # Langkah 1 & 2: Cari konteks + buat jawaban
    konteks = rag_service.cari_konteks(request.pertanyaan, request.pertemuan_id)
    hasil = await rag_service.buat_jawaban(request.pertanyaan, konteks, riwayat)

    # Langkah 3: Simpan ke database
    db.table("chat_history").insert([
        {
            "siswa_id": user["id"],
            "pertemuan_id": request.pertemuan_id,
            "dari_siswa": True,
            "teks": request.pertanyaan,
            "waktu": waktu,
        },
        {
            "siswa_id": user["id"],
            "pertemuan_id": request.pertemuan_id,
            "dari_siswa": False,
            "teks": hasil["jawaban"],
            "label_sumber": hasil.get("label_sumber"),
            "waktu": waktu,
        },
    ]).execute()

    # Update total_chat siswa
    db.table("users").update({
        "total_chat": user.get("total_chat", 0) + 1,
    }).eq("id", user["id"]).execute()

    return {
        "jawaban": hasil["jawaban"],
        "label_sumber": hasil.get("label_sumber"),
        "waktu": waktu,
    }


@router.get("/riwayat/{siswa_id}")
async def ambil_riwayat(siswa_id: str, user: dict = Depends(get_current_user)):
    """Ambil semua riwayat chat siswa"""
    result = db.table("chat_history").select("*").eq(
        "siswa_id", siswa_id).order("waktu").execute()
    return {"data": result.data or []}


@router.get("/riwayat/{siswa_id}/{pertemuan_id}")
async def ambil_riwayat_pertemuan(
    siswa_id: str, pertemuan_id: str, user: dict = Depends(get_current_user)
):
    """Ambil riwayat chat dalam satu pertemuan"""
    result = db.table("chat_history").select("*").eq(
        "siswa_id", siswa_id).eq("pertemuan_id", pertemuan_id).order("waktu").execute()
    return {"data": result.data or []}
