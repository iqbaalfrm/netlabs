# Router nilai — rekap hasil kuis
from fastapi import APIRouter, Depends
from app.database import db
from app.middleware.auth import get_current_user, siswa_only, guru_only

router = APIRouter()


@router.get("/saya")
async def nilai_saya(user: dict = Depends(siswa_only)):
    """Ambil semua hasil kuis siswa yang login"""
    result = db.table("hasil_kuis").select("*").eq(
        "siswa_id", user["id"]).order("waktu_kuis", desc=True).execute()

    # Hitung rata-rata
    data = result.data or []
    rata_rata = 0
    if data:
        rata_rata = round(sum(d["nilai"] for d in data) / len(data), 1)

    return {"rata_rata": rata_rata, "data": data}


@router.get("/siswa/{siswa_id}")
async def nilai_siswa(siswa_id: str, user: dict = Depends(guru_only)):
    """Ambil rekap nilai siswa tertentu (guru only)"""
    result = db.table("hasil_kuis").select("*").eq(
        "siswa_id", siswa_id).order("waktu_kuis", desc=True).execute()
    return {"data": result.data}


@router.get("/rekap")
async def rekap_nilai(user: dict = Depends(guru_only)):
    """Rekap nilai semua siswa (guru only)"""
    result = db.table("hasil_kuis").select(
        "*, users(nama, nis, kelas)").order("waktu_kuis", desc=True).execute()
    return {"data": result.data}
