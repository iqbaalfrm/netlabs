from fastapi import APIRouter, Depends

from app.database import db
from app.middleware.auth import guru_only

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
        }
    }


@router.get("/siswa")
async def daftar_siswa(user: dict = Depends(guru_only)):
    """Daftar semua siswa."""
    result = (
        db.table("users")
        .select("id,nis,nama,kelas,sekolah,streak_hari,total_chat,created_at")
        .eq("role", "siswa")
        .order("nama")
        .execute()
    )
    return {"data": result.data or []}


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
        }
    }


@router.get("/pertanyaan")
async def pertanyaan_terbaru(user: dict = Depends(guru_only)):
    """Pertanyaan siswa terbaru untuk monitoring guru."""
    result = (
        db.table("chat_history")
        .select("*, users(nama, nis, kelas)")
        .eq("dari_siswa", True)
        .order("waktu", desc=True)
        .limit(20)
        .execute()
    )
    return {"data": result.data or []}
