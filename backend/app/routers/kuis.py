from datetime import datetime
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException
from pydantic import BaseModel, Field

from app.database import db
from app.middleware.auth import guru_only, siswa_only

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
    user: dict = Depends(guru_only),
):
    """Ambil bank soal untuk guru."""
    query = db.table("soal_kuis").select("*")
    if pertemuan_id:
        query = query.eq("pertemuan_id", pertemuan_id)
    result = query.execute()
    return {"data": result.data or []}


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
    return {"message": "Soal kuis berhasil dihapus"}


@router.post("/submit")
async def submit_kuis(request: SubmitKuisRequest, user: dict = Depends(siswa_only)):
    """Submit jawaban kuis dan simpan nilai siswa."""
    if not request.jawaban:
        raise HTTPException(status_code=422, detail="Jawaban kuis tidak boleh kosong")

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
        "data": insert_result.data,
        "jumlah_benar": jumlah_benar,
        "total_soal": total_soal,
        "nilai": nilai,
        "message": "Kuis berhasil disubmit",
    }


@router.get("/{pertemuan_id}")
async def ambil_kuis(pertemuan_id: str, user: dict = Depends(siswa_only)):
    """Ambil maksimal 5 soal kuis untuk siswa."""
    result = (
        db.table("soal_kuis")
        .select("id,pertemuan_id,pertanyaan,pilihan_a,pilihan_b,pilihan_c,pilihan_d")
        .eq("pertemuan_id", pertemuan_id)
        .limit(5)
        .execute()
    )
    return {"data": result.data or []}
