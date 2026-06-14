# Router AI Chat — endpoint RAG dengan sanitasi input
import bleach
from fastapi import APIRouter, Depends, Request
from datetime import datetime
from slowapi import Limiter
from slowapi.util import get_remote_address

from app.database import db
from app.middleware.auth import get_current_user, siswa_only
from app.schemas import ChatRequest
from app.services import rag_service
from app.helpers.pagination import paginate_params, paginate_response

router = APIRouter()
limiter = Limiter(key_func=get_remote_address)

MAX_PERTANYAAN_LENGTH = 1000


def _sanitize_input(text: str) -> str:
    """Strip HTML tags dan limit panjang."""
    cleaned = bleach.clean(text, tags=[], strip=True)
    return cleaned[:MAX_PERTANYAAN_LENGTH].strip()


@router.post("/tanya")
@limiter.limit("20/minute")
async def tanya_ai(request: Request, body: ChatRequest, user: dict = Depends(siswa_only)):
    """
    Endpoint utama RAG — siswa bertanya, AI menjawab dari modul.
    """
    waktu = datetime.utcnow().isoformat()

    pertanyaan = _sanitize_input(body.pertanyaan)
    if not pertanyaan:
        return {"jawaban": "Pertanyaan tidak boleh kosong.", "label_sumber": None, "waktu": waktu}

    riwayat = [{"dari_siswa": p.dari_siswa, "teks": _sanitize_input(p.teks)} for p in body.riwayat_chat[-10:]]

    konteks = rag_service.cari_konteks(pertanyaan, body.pertemuan_id)
    hasil = await rag_service.buat_jawaban(pertanyaan, konteks, riwayat)

    db.table("chat_history").insert([
        {
            "siswa_id": user["id"],
            "pertemuan_id": body.pertemuan_id,
            "dari_siswa": True,
            "teks": pertanyaan,
            "waktu": waktu,
        },
        {
            "siswa_id": user["id"],
            "pertemuan_id": body.pertemuan_id,
            "dari_siswa": False,
            "teks": hasil["jawaban"],
            "label_sumber": hasil.get("label_sumber"),
            "waktu": waktu,
        },
    ]).execute()

    db.table("users").update({
        "total_chat": (user.get("total_chat") or 0) + 1,
    }).eq("id", user["id"]).execute()

    return {
        "jawaban": hasil["jawaban"],
        "label_sumber": hasil.get("label_sumber"),
        "waktu": waktu,
    }


@router.get("/riwayat/{siswa_id}")
async def ambil_riwayat(
    siswa_id: str,
    page: int = 1,
    limit: int = 20,
    user: dict = Depends(get_current_user),
):
    """Ambil semua riwayat chat siswa dengan pagination."""
    if user.get("role") == "siswa" and user["id"] != siswa_id:
        from fastapi import HTTPException
        raise HTTPException(status_code=403, detail="Tidak bisa mengakses data siswa lain")

    page, limit = paginate_params(page, limit)
    offset = (page - 1) * limit

    count_result = db.table("chat_history").select("*", count="exact").eq("siswa_id", siswa_id).execute()
    total = count_result.count or 0

    result = db.table("chat_history").select("*").eq(
        "siswa_id", siswa_id).order("waktu").range(offset, offset + limit - 1).execute()

    return paginate_response(result.data or [], page, limit, total)


@router.get("/riwayat/{siswa_id}/{pertemuan_id}")
async def ambil_riwayat_pertemuan(
    siswa_id: str,
    pertemuan_id: str,
    page: int = 1,
    limit: int = 20,
    user: dict = Depends(get_current_user),
):
    """Ambil riwayat chat dalam satu pertemuan dengan pagination."""
    if user.get("role") == "siswa" and user["id"] != siswa_id:
        from fastapi import HTTPException
        raise HTTPException(status_code=403, detail="Tidak bisa mengakses data siswa lain")

    page, limit = paginate_params(page, limit)
    offset = (page - 1) * limit

    count_result = db.table("chat_history").select("*", count="exact").eq(
        "siswa_id", siswa_id).eq("pertemuan_id", pertemuan_id).execute()
    total = count_result.count or 0

    result = db.table("chat_history").select("*").eq(
        "siswa_id", siswa_id).eq("pertemuan_id", pertemuan_id).order("waktu").range(offset, offset + limit - 1).execute()

    return paginate_response(result.data or [], page, limit, total)
