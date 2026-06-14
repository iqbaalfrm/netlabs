# Router nilai — rekap hasil kuis dengan pagination
from typing import Optional
from fastapi import APIRouter, Depends
from app.database import db
from app.middleware.auth import get_current_user, siswa_only, guru_only
from app.helpers.pagination import paginate_params, paginate_response

router = APIRouter()


@router.get("/saya")
async def nilai_saya(
    page: int = 1,
    limit: int = 10,
    user: dict = Depends(siswa_only),
):
    """Ambil semua hasil kuis siswa yang login"""
    page, limit = paginate_params(page, limit)
    offset = (page - 1) * limit

    count_result = db.table("hasil_kuis").select("*", count="exact").eq(
        "siswa_id", user["id"]).execute()
    total = count_result.count or 0

    result = db.table("hasil_kuis").select("*").eq(
        "siswa_id", user["id"]).order("waktu_kuis", desc=True).range(offset, offset + limit - 1).execute()

    data = result.data or []

    # Hitung rata-rata dari semua (bukan hanya page ini)
    all_result = db.table("hasil_kuis").select("nilai").eq("siswa_id", user["id"]).execute()
    all_data = all_result.data or []
    rata_rata = round(sum(d["nilai"] for d in all_data) / len(all_data), 1) if all_data else 0

    response = paginate_response(data, page, limit, total)
    response["rata_rata"] = rata_rata
    response["total_kuis"] = total
    return response


@router.get("/siswa/{siswa_id}")
async def nilai_siswa(
    siswa_id: str,
    page: int = 1,
    limit: int = 10,
    user: dict = Depends(guru_only),
):
    """Ambil rekap nilai siswa tertentu (guru only)"""
    page, limit = paginate_params(page, limit)
    offset = (page - 1) * limit

    count_result = db.table("hasil_kuis").select("*", count="exact").eq(
        "siswa_id", siswa_id).execute()
    total = count_result.count or 0

    result = db.table("hasil_kuis").select("*").eq(
        "siswa_id", siswa_id).order("waktu_kuis", desc=True).range(offset, offset + limit - 1).execute()

    return paginate_response(result.data or [], page, limit, total)


@router.get("/rekap")
async def rekap_nilai(
    page: int = 1,
    limit: int = 15,
    kelas: Optional[str] = None,
    pertemuan_id: Optional[str] = None,
    user: dict = Depends(guru_only),
):
    """Rekap nilai semua siswa (guru only)"""
    page, limit = paginate_params(page, limit)
    offset = (page - 1) * limit

    query = db.table("hasil_kuis").select("*, users(nama, nis, kelas)", count="exact")
    if pertemuan_id:
        query = query.eq("pertemuan_id", pertemuan_id)
    count_result = query.order("waktu_kuis", desc=True).execute()
    total = count_result.count or 0

    data_query = db.table("hasil_kuis").select("*, users(nama, nis, kelas)")
    if pertemuan_id:
        data_query = data_query.eq("pertemuan_id", pertemuan_id)
    result = data_query.order("waktu_kuis", desc=True).range(offset, offset + limit - 1).execute()

    return paginate_response(result.data or [], page, limit, total)
