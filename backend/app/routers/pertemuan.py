# Router pertemuan — CRUD pertemuan praktikum dengan pagination
from typing import Optional
from fastapi import APIRouter, Depends, HTTPException
from app.database import db
from app.middleware.auth import get_current_user, guru_only
from app.schemas import PertemuanCreate, PertemuanUpdate
from app.helpers.pagination import paginate_params, paginate_response

router = APIRouter()


@router.get("/")
async def ambil_semua(
    page: int = 1,
    limit: int = 10,
    search: Optional[str] = None,
    user: dict = Depends(get_current_user),
):
    """Ambil semua pertemuan dengan pagination."""
    page, limit = paginate_params(page, limit)

    query = db.table("pertemuan").select("*", count="exact")
    if search:
        query = query.ilike("judul", f"%{search}%")
    query = query.order("nomor_urut")

    # Get total
    count_result = query.execute()
    total = count_result.count or 0

    # Get paginated data
    offset = (page - 1) * limit
    data_query = db.table("pertemuan").select("*")
    if search:
        data_query = data_query.ilike("judul", f"%{search}%")
    data_query = data_query.order("nomor_urut").range(offset, offset + limit - 1)
    result = data_query.execute()

    return paginate_response(result.data or [], page, limit, total)


@router.get("/{pertemuan_id}")
async def ambil_detail(pertemuan_id: str, user: dict = Depends(get_current_user)):
    """Ambil detail pertemuan + daftar topik"""
    pertemuan = db.table("pertemuan").select("*").eq("id", pertemuan_id).single().execute()

    if not pertemuan.data:
        raise HTTPException(status_code=404, detail="Pertemuan tidak ditemukan")

    topik = db.table("topik").select("*").eq(
        "pertemuan_id", pertemuan_id).order("nomor_urut").execute()

    data = pertemuan.data
    data["daftar_topik"] = topik.data or []
    return {"data": data, "message": "OK"}


@router.post("/")
async def buat_pertemuan(request: PertemuanCreate, user: dict = Depends(guru_only)):
    """Buat pertemuan baru (guru only)"""
    result = db.table("pertemuan").insert({
        "judul": request.judul,
        "deskripsi": request.deskripsi,
        "nomor_urut": request.nomor_urut,
        "warna_hex": request.warna_hex,
        "dibuat_oleh": user["id"],
    }).execute()
    return {"data": result.data, "message": "Pertemuan berhasil dibuat"}


@router.put("/{pertemuan_id}")
async def update_pertemuan(pertemuan_id: str, request: PertemuanUpdate, user: dict = Depends(guru_only)):
    """Update pertemuan (guru only)"""
    update = {k: v for k, v in request.model_dump().items() if v is not None}
    if not update:
        raise HTTPException(status_code=400, detail="Tidak ada data yang diupdate")

    result = db.table("pertemuan").update(update).eq("id", pertemuan_id).execute()
    return {"data": result.data, "message": "Pertemuan berhasil diupdate"}


@router.delete("/{pertemuan_id}")
async def hapus_pertemuan(pertemuan_id: str, user: dict = Depends(guru_only)):
    """Hapus pertemuan (guru only)"""
    db.table("pertemuan").delete().eq("id", pertemuan_id).execute()
    return {"data": None, "message": "Pertemuan berhasil dihapus"}
