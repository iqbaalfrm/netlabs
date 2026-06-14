# Router topik — CRUD topik + tandai sudah dibaca + pagination
from typing import Optional
from fastapi import APIRouter, Depends, HTTPException
from datetime import datetime
from app.database import db
from app.middleware.auth import get_current_user, guru_only, siswa_only
from app.schemas import TopikCreate, TopikUpdate
from app.helpers.pagination import paginate_params, paginate_response

router = APIRouter()


@router.get("/{pertemuan_id}")
async def ambil_topik(
    pertemuan_id: str,
    page: int = 1,
    limit: int = 10,
    search: Optional[str] = None,
    user: dict = Depends(get_current_user),
):
    """Ambil daftar topik dengan pagination + status baca siswa"""
    page, limit = paginate_params(page, limit)
    offset = (page - 1) * limit

    count_query = db.table("topik").select("*", count="exact").eq("pertemuan_id", pertemuan_id)
    if search:
        count_query = count_query.ilike("judul", f"%{search}%")
    count_result = count_query.execute()
    total = count_result.count or 0

    data_query = db.table("topik").select("*").eq("pertemuan_id", pertemuan_id)
    if search:
        data_query = data_query.ilike("judul", f"%{search}%")
    data_query = data_query.order("nomor_urut").range(offset, offset + limit - 1)
    result = data_query.execute()

    topik_list = result.data or []

    if user.get("role") == "siswa":
        progress = db.table("progress_topik").select("topik_id").eq(
            "siswa_id", user["id"]).eq("sudah_dibaca", True).execute()
        sudah_dibaca_ids = {p["topik_id"] for p in (progress.data or [])}
        for t in topik_list:
            t["sudah_dibaca"] = t["id"] in sudah_dibaca_ids

    return paginate_response(topik_list, page, limit, total)


@router.get("/detail/{topik_id}")
async def ambil_detail_topik(topik_id: str, user: dict = Depends(get_current_user)):
    """Ambil detail topik lengkap"""
    result = db.table("topik").select("*").eq("id", topik_id).single().execute()
    if not result.data:
        raise HTTPException(status_code=404, detail="Topik tidak ditemukan")
    return {"data": result.data, "message": "OK"}


@router.post("/")
async def buat_topik(request: TopikCreate, user: dict = Depends(guru_only)):
    """Buat topik baru (guru only)"""
    result = db.table("topik").insert({
        "pertemuan_id": request.pertemuan_id,
        "judul": request.judul,
        "isi_materi": request.isi_materi,
        "nomor_urut": request.nomor_urut,
    }).execute()
    return {"data": result.data, "message": "Topik berhasil dibuat"}


@router.put("/{topik_id}")
async def update_topik(topik_id: str, request: TopikUpdate, user: dict = Depends(guru_only)):
    """Update topik (guru only)"""
    update = {k: v for k, v in request.model_dump().items() if v is not None}
    if not update:
        raise HTTPException(status_code=400, detail="Tidak ada data yang diupdate")

    result = db.table("topik").update(update).eq("id", topik_id).execute()
    return {"data": result.data, "message": "Topik berhasil diupdate"}


@router.delete("/{topik_id}")
async def hapus_topik(topik_id: str, user: dict = Depends(guru_only)):
    """Hapus topik (guru only)"""
    db.table("topik").delete().eq("id", topik_id).execute()
    return {"data": None, "message": "Topik berhasil dihapus"}


@router.post("/{topik_id}/baca")
async def tandai_dibaca(topik_id: str, user: dict = Depends(siswa_only)):
    """Tandai topik sudah dibaca oleh siswa"""
    waktu = datetime.utcnow().isoformat()

    existing = db.table("progress_topik").select("id").eq(
        "siswa_id", user["id"]).eq("topik_id", topik_id).execute()

    if existing.data:
        db.table("progress_topik").update({
            "sudah_dibaca": True, "waktu_dibaca": waktu,
        }).eq("siswa_id", user["id"]).eq("topik_id", topik_id).execute()
    else:
        db.table("progress_topik").insert({
            "siswa_id": user["id"], "topik_id": topik_id,
            "sudah_dibaca": True, "waktu_dibaca": waktu,
        }).execute()

    return {"data": None, "message": "Topik ditandai sudah dibaca"}
