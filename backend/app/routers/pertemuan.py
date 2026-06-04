# Router pertemuan — CRUD pertemuan praktikum
from fastapi import APIRouter, Depends, HTTPException
from app.database import db
from app.middleware.auth import get_current_user, guru_only
from app.schemas import PertemuanCreate, PertemuanUpdate

router = APIRouter()


@router.get("/")
async def ambil_semua(user: dict = Depends(get_current_user)):
    """Ambil semua pertemuan, urut berdasarkan nomor"""
    result = db.table("pertemuan").select("*").order("nomor_urut").execute()
    return {"data": result.data or []}


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
    return {"data": data}


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
    return {"message": "Pertemuan berhasil dihapus"}
