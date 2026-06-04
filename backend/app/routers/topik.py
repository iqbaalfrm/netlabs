# Router topik — CRUD topik + tandai sudah dibaca
from fastapi import APIRouter, Depends, HTTPException
from datetime import datetime
from app.database import db
from app.middleware.auth import get_current_user, guru_only, siswa_only
from app.schemas import TopikCreate, TopikUpdate

router = APIRouter()


@router.get("/{pertemuan_id}")
async def ambil_topik(pertemuan_id: str, user: dict = Depends(get_current_user)):
    """Ambil daftar topik + status baca siswa"""
    result = db.table("topik").select("*").eq(
        "pertemuan_id", pertemuan_id).order("nomor_urut").execute()

    topik_list = result.data or []

    # Jika siswa, tambahkan status sudah_dibaca
    if user.get("role") == "siswa":
        progress = db.table("progress_topik").select("topik_id").eq(
            "siswa_id", user["id"]).eq("sudah_dibaca", True).execute()
        sudah_dibaca_ids = {p["topik_id"] for p in (progress.data or [])}
        for t in topik_list:
            t["sudah_dibaca"] = t["id"] in sudah_dibaca_ids

    return {"data": topik_list}


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
    return {"message": "Topik berhasil dihapus"}


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

    return {"message": "Topik ditandai sudah dibaca ✅"}
