# Router modul PDF — upload, index ke RAG, dan hapus
import os
import tempfile
from fastapi import APIRouter, Depends, UploadFile, File, Form, HTTPException
from app.database import db
from app.middleware.auth import get_current_user, guru_only
from app.services import pdf_service, rag_service

router = APIRouter()


@router.post("/upload")
async def upload_modul(
    file: UploadFile = File(...),
    pertemuan_id: str = Form(...),
    user: dict = Depends(guru_only),
):
    """
    Upload PDF modul dan index ke ChromaDB untuk RAG.

    Proses:
    1. Validasi file PDF
    2. Simpan sementara ke disk
    3. Parse PDF jadi chunks
    4. Index ke ChromaDB
    5. Simpan metadata ke database
    """
    # Validasi format file
    if not file.filename or not file.filename.endswith('.pdf'):
        raise HTTPException(status_code=400, detail="Hanya file PDF yang diizinkan")

    # Simpan file sementara
    with tempfile.NamedTemporaryFile(delete=False, suffix='.pdf') as tmp:
        konten = await file.read()
        tmp.write(konten)
        tmp_path = tmp.name

    try:
        # Parse PDF jadi chunks
        halaman_list = pdf_service.baca_pdf(tmp_path)
        chunks_data = pdf_service.potong_jadi_chunks(halaman_list)

        if not chunks_data:
            raise HTTPException(status_code=400, detail="PDF tidak bisa dibaca atau kosong")

        # Index ke ChromaDB
        chunks_teks = [c['teks'] for c in chunks_data]
        rag_service.index_pdf(chunks_teks, pertemuan_id, file.filename)

        # Simpan metadata ke database
        result = db.table("modul_pdf").insert({
            "pertemuan_id": pertemuan_id,
            "nama_file": file.filename,
            "sudah_diindex": True,
            "diunggah_oleh": user["id"],
        }).execute()

        return {
            "message": f"Berhasil mengindex {len(chunks_data)} chunks dari {file.filename}",
            "data": result.data,
        }

    finally:
        # Hapus file sementara
        os.unlink(tmp_path)


@router.get("/{pertemuan_id}")
async def ambil_modul(pertemuan_id: str, user: dict = Depends(get_current_user)):
    """Ambil daftar PDF modul untuk satu pertemuan"""
    result = db.table("modul_pdf").select("*").eq(
        "pertemuan_id", pertemuan_id).order("created_at").execute()
    return {"data": result.data or []}


@router.delete("/{modul_id}")
async def hapus_modul(modul_id: str, user: dict = Depends(guru_only)):
    """Hapus modul PDF dari database (tidak hapus dari ChromaDB otomatis)"""
    db.table("modul_pdf").delete().eq("id", modul_id).execute()
    return {"message": "Modul berhasil dihapus"}
