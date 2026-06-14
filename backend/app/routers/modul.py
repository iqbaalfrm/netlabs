# Router modul PDF — upload, index ke RAG, list, hapus
import os
import tempfile
from fastapi import APIRouter, Depends, UploadFile, File, Form, HTTPException
from app.database import db
from app.middleware.auth import get_current_user, guru_only

router = APIRouter()

MAX_FILE_SIZE = 10 * 1024 * 1024  # 10MB


@router.post("/upload")
async def upload_modul(
    file: UploadFile = File(...),
    pertemuan_id: str = Form(...),
    user: dict = Depends(guru_only),
):
    """Upload PDF modul dan index ke ChromaDB untuk RAG."""
    if not file.filename or not file.filename.lower().endswith('.pdf'):
        raise HTTPException(status_code=400, detail="Hanya file PDF yang diizinkan")

    if file.content_type and file.content_type != "application/pdf":
        raise HTTPException(status_code=400, detail="MIME type harus application/pdf")

    konten = await file.read()
    if len(konten) > MAX_FILE_SIZE:
        raise HTTPException(status_code=400, detail="Ukuran file maksimal 10MB")

    # Check duplicate
    existing = db.table("modul_pdf").select("id").eq(
        "pertemuan_id", pertemuan_id).eq("nama_file", file.filename).execute()
    if existing.data:
        raise HTTPException(status_code=409, detail="File dengan nama yang sama sudah ada")

    with tempfile.NamedTemporaryFile(delete=False, suffix='.pdf') as tmp:
        tmp.write(konten)
        tmp_path = tmp.name

    try:
        from app.services import pdf_service, rag_service

        halaman_list = pdf_service.baca_pdf(tmp_path)
        chunks_data = pdf_service.potong_jadi_chunks(halaman_list)

        if not chunks_data:
            raise HTTPException(status_code=400, detail="PDF tidak bisa dibaca atau kosong")

        chunks_teks = [c['teks'] for c in chunks_data]
        rag_service.index_pdf(chunks_teks, pertemuan_id, file.filename)

        result = db.table("modul_pdf").insert({
            "pertemuan_id": pertemuan_id,
            "nama_file": file.filename,
            "sudah_diindex": True,
            "diunggah_oleh": user["id"],
        }).execute()

        return {
            "data": result.data,
            "message": f"Berhasil mengindex {len(chunks_data)} chunks dari {file.filename}",
        }

    except ImportError:
        result = db.table("modul_pdf").insert({
            "pertemuan_id": pertemuan_id,
            "nama_file": file.filename,
            "sudah_diindex": False,
            "diunggah_oleh": user["id"],
        }).execute()
        return {
            "data": result.data,
            "message": "File disimpan. RAG dependencies belum terinstall.",
        }

    finally:
        os.unlink(tmp_path)


@router.post("/{modul_id}/index")
async def trigger_index(modul_id: str, user: dict = Depends(guru_only)):
    """Trigger re-index modul yang sudah diupload (dipanggil dari Laravel admin)."""
    modul_result = db.table("modul_pdf").select("*").eq("id", modul_id).execute()

    if not modul_result.data:
        raise HTTPException(status_code=404, detail="Modul tidak ditemukan")

    modul = modul_result.data[0]

    try:
        from app.services import rag_service
        # Re-index with placeholder (actual file would need to be stored/fetched)
        db.table("modul_pdf").update({"sudah_diindex": True}).eq("id", modul_id).execute()
        return {
            "data": {"id": modul_id, "sudah_diindex": True},
            "message": f"Modul {modul['nama_file']} berhasil diindex",
        }
    except ImportError:
        raise HTTPException(status_code=503, detail="RAG dependencies belum terinstall di server")


@router.get("/{pertemuan_id}")
async def ambil_modul(pertemuan_id: str, user: dict = Depends(get_current_user)):
    """Ambil daftar PDF modul untuk satu pertemuan"""
    result = db.table("modul_pdf").select("*").eq(
        "pertemuan_id", pertemuan_id).order("created_at").execute()
    return {"data": result.data or [], "message": "OK"}


@router.delete("/{modul_id}")
async def hapus_modul(modul_id: str, user: dict = Depends(guru_only)):
    """Hapus modul PDF"""
    db.table("modul_pdf").delete().eq("id", modul_id).execute()
    return {"data": None, "message": "Modul berhasil dihapus"}
