from datetime import datetime
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException
from pydantic import BaseModel, Field

from app.database import db
from app.middleware.auth import guru_only, siswa_only, get_current_user
from app.helpers.pagination import paginate_params, paginate_response

router = APIRouter()


class SoalCreate(BaseModel):
    pertemuan_id: str
    pertanyaan: str
    pilihan_a: str
    pilihan_b: str
    pilihan_c: str
    pilihan_d: str
    jawaban_benar: str = Field(min_length=1, max_length=1)
    penjelasan: Optional[str] = None


class JawabanKuis(BaseModel):
    soal_id: str
    jawaban: str = Field(min_length=1, max_length=1)


class SubmitKuisRequest(BaseModel):
    pertemuan_id: str
    jawaban: List[JawabanKuis]


@router.get("/soal")
async def daftar_soal(
    pertemuan_id: Optional[str] = None,
    page: int = 1,
    limit: int = 10,
    user: dict = Depends(guru_only),
):
    """Ambil bank soal untuk guru dengan pagination."""
    page, limit = paginate_params(page, limit)
    offset = (page - 1) * limit

    count_query = db.table("soal_kuis").select("*", count="exact")
    if pertemuan_id:
        count_query = count_query.eq("pertemuan_id", pertemuan_id)
    count_result = count_query.execute()
    total = count_result.count or 0

    data_query = db.table("soal_kuis").select("*")
    if pertemuan_id:
        data_query = data_query.eq("pertemuan_id", pertemuan_id)
    result = data_query.order("created_at").range(offset, offset + limit - 1).execute()

    return paginate_response(result.data or [], page, limit, total)


@router.post("/soal")
async def buat_soal(request: SoalCreate, user: dict = Depends(guru_only)):
    """Buat soal kuis baru."""
    payload = request.dict()
    payload["jawaban_benar"] = payload["jawaban_benar"].upper()
    result = db.table("soal_kuis").insert(payload).execute()
    return {"data": result.data, "message": "Soal kuis berhasil dibuat"}


@router.delete("/soal/{soal_id}")
async def hapus_soal(soal_id: str, user: dict = Depends(guru_only)):
    """Hapus soal kuis."""
    db.table("soal_kuis").delete().eq("id", soal_id).execute()
    return {"data": None, "message": "Soal kuis berhasil dihapus"}


@router.get("/{pertemuan_id}")
async def ambil_kuis(pertemuan_id: str, user: dict = Depends(siswa_only)):
    """Ambil soal kuis untuk siswa (maks 5 soal)."""
    result = (
        db.table("soal_kuis")
        .select("id,pertemuan_id,pertanyaan,pilihan_a,pilihan_b,pilihan_c,pilihan_d")
        .eq("pertemuan_id", pertemuan_id)
        .limit(5)
        .execute()
    )
    return {"data": result.data or [], "message": "OK"}


@router.get("/{pertemuan_id}/hasil")
async def cek_hasil_kuis(pertemuan_id: str, user: dict = Depends(siswa_only)):
    """Cek apakah siswa sudah mengerjakan kuis ini."""
    result = (
        db.table("hasil_kuis")
        .select("*")
        .eq("siswa_id", user["id"])
        .eq("pertemuan_id", pertemuan_id)
        .order("waktu_kuis", desc=True)
        .limit(1)
        .execute()
    )

    if result.data:
        return {
            "data": result.data[0],
            "sudah_dikerjakan": True,
            "message": "Kuis sudah pernah dikerjakan",
        }

    return {
        "data": None,
        "sudah_dikerjakan": False,
        "message": "Kuis belum dikerjakan",
    }


@router.post("/submit")
async def submit_kuis(request: SubmitKuisRequest, user: dict = Depends(siswa_only)):
    """Submit jawaban kuis dan simpan nilai siswa."""
    if not request.jawaban:
        raise HTTPException(status_code=422, detail="Jawaban kuis tidak boleh kosong")

    # Check if already submitted
    existing = (
        db.table("hasil_kuis")
        .select("id")
        .eq("siswa_id", user["id"])
        .eq("pertemuan_id", request.pertemuan_id)
        .execute()
    )
    if existing.data:
        raise HTTPException(status_code=409, detail="Kuis ini sudah pernah dikerjakan")

    soal_ids = [item.soal_id for item in request.jawaban]
    soal_result = (
        db.table("soal_kuis")
        .select("id,jawaban_benar")
        .eq("pertemuan_id", request.pertemuan_id)
        .in_("id", soal_ids)
        .execute()
    )

    kunci = {row["id"]: row["jawaban_benar"].upper() for row in soal_result.data or []}
    if len(kunci) != len(soal_ids):
        raise HTTPException(status_code=404, detail="Sebagian soal kuis tidak ditemukan")

    jumlah_benar = sum(
        1 for item in request.jawaban if kunci[item.soal_id] == item.jawaban.upper()
    )
    total_soal = len(request.jawaban)
    nilai = round((jumlah_benar / total_soal) * 100, 1)

    insert_result = db.table("hasil_kuis").insert({
        "siswa_id": user["id"],
        "pertemuan_id": request.pertemuan_id,
        "jumlah_benar": jumlah_benar,
        "total_soal": total_soal,
        "nilai": nilai,
        "waktu_kuis": datetime.utcnow().isoformat(),
    }).execute()

    return {
        "data": {
            "jumlah_benar": jumlah_benar,
            "total_soal": total_soal,
            "nilai": nilai,
        },
        "message": "Kuis berhasil disubmit",
    }
