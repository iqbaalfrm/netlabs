from typing import Optional
from fastapi import APIRouter, Depends
from app.database import db
from app.middleware.auth import guru_only
from app.helpers.pagination import paginate_params, paginate_response

router = APIRouter()


@router.get("/dashboard")
async def dashboard_guru(user: dict = Depends(guru_only)):
    """Statistik ringkas untuk dashboard guru."""
    siswa = db.table("users").select("id", count="exact").eq("role", "siswa").execute()
    chat = db.table("chat_history").select("id", count="exact").execute()
    nilai = db.table("hasil_kuis").select("nilai").execute()
    pertemuan = db.table("pertemuan").select("id", count="exact").execute()

    nilai_data = nilai.data or []
    rata_rata = 0
    if nilai_data:
        rata_rata = round(sum(item["nilai"] for item in nilai_data) / len(nilai_data), 1)

    return {
        "data": {
            "total_siswa": siswa.count or 0,
            "total_chat": chat.count or 0,
            "rata_rata_nilai": rata_rata,
            "total_pertemuan": pertemuan.count or 0,
        },
        "message": "OK",
    }


@router.get("/siswa")
async def daftar_siswa(
    page: int = 1,
    limit: int = 15,
    search: Optional[str] = None,
    kelas: Optional[str] = None,
    user: dict = Depends(guru_only),
):
    """Daftar semua siswa dengan pagination."""
    page, limit = paginate_params(page, limit)
    offset = (page - 1) * limit

    count_query = db.table("users").select("*", count="exact").eq("role", "siswa")
    if search:
        count_query = count_query.ilike("nama", f"%{search}%")
    if kelas:
        count_query = count_query.eq("kelas", kelas)
    count_result = count_query.execute()
    total = count_result.count or 0

    data_query = db.table("users").select(
        "id,nis,nama,kelas,sekolah,streak_hari,total_chat,created_at"
    ).eq("role", "siswa")
    if search:
        data_query = data_query.ilike("nama", f"%{search}%")
    if kelas:
        data_query = data_query.eq("kelas", kelas)
    result = data_query.order("nama").range(offset, offset + limit - 1).execute()

    return paginate_response(result.data or [], page, limit, total)


@router.get("/siswa/{siswa_id}")
async def detail_siswa(siswa_id: str, user: dict = Depends(guru_only)):
    """Detail siswa beserta nilai dan riwayat chat terakhir."""
    siswa = (
        db.table("users")
        .select("id,nis,nama,kelas,sekolah,streak_hari,total_chat,created_at")
        .eq("id", siswa_id)
        .single()
        .execute()
    )
    nilai = (
        db.table("hasil_kuis")
        .select("*")
        .eq("siswa_id", siswa_id)
        .order("waktu_kuis", desc=True)
        .execute()
    )
    chat = (
        db.table("chat_history")
        .select("*")
        .eq("siswa_id", siswa_id)
        .order("waktu", desc=True)
        .limit(10)
        .execute()
    )

    return {
        "data": {
            "siswa": siswa.data,
            "nilai": nilai.data or [],
            "chat_terakhir": chat.data or [],
        },
        "message": "OK",
    }


@router.get("/pertanyaan")
async def pertanyaan_terbaru(
    page: int = 1,
    limit: int = 20,
    user: dict = Depends(guru_only),
):
    """Pertanyaan siswa terbaru untuk monitoring guru."""
    page, limit = paginate_params(page, limit)
    offset = (page - 1) * limit

    count_result = db.table("chat_history").select("*", count="exact").eq("dari_siswa", True).execute()
    total = count_result.count or 0

    result = (
        db.table("chat_history")
        .select("*, users(nama, nis, kelas)")
        .eq("dari_siswa", True)
        .order("waktu", desc=True)
        .range(offset, offset + limit - 1)
        .execute()
    )

    return paginate_response(result.data or [], page, limit, total)
