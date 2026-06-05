# Router modul PDF — upload dan index ke RAG
import os
import tempfile
from fastapi import APIRouter, Depends, UploadFile, File, Form, HTTPException
from app.database import db
from app.middleware.auth import get_current_user, guru_only

router = APIRouter()


@router.post("/upload")
async def upload_modul(
    file: UploadFile = File(...),
    pertemuan_id: str = Form(...),
    user: dict = Depends(guru_only),
):
    """
    Upload PDF modul dan index ke ChromaDB untuk RAG.
    Membutuhkan: pip install pymupdf chromadb sentence-transformers
    """
    if not file.filename or not file.filename.endswith('.pdf'):
        raise HTTPException(status_code=400, detail="Hanya file PDF yang diizinkan")

    # Simpan file sementara
    with tempfile.NamedTemporaryFile(delete=False, suffix='.pdf') as tmp:
        konten = await file.read()
        tmp.write(konten)
        tmp_path = tmp.name

    try:
        # Import RAG dependencies secara lazy
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
            "message": f"Berhasil mengindex {len(chunks_data)} chunks dari {file.filename}",
            "data": result.data,
        }

    except ImportError:
        # RAG dependencies belum diinstall — simpan file saja tanpa indexing
        result = db.table("modul_pdf").insert({
            "pertemuan_id": pertemuan_id,
            "nama_file": file.filename,
            "sudah_diindex": False,
            "diunggah_oleh": user["id"],
        }).execute()
        return {
            "message": "File disimpan. Install chromadb dan sentence-transformers untuk mengaktifkan indexing.",
            "data": result.data,
        }

    finally:
        os.unlink(tmp_path)


@router.get("/{pertemuan_id}")
async def ambil_modul(pertemuan_id: str, user: dict = Depends(get_current_user)):
    """Ambil daftar PDF modul untuk satu pertemuan"""
    result = db.table("modul_pdf").select("*").eq(
        "pertemuan_id", pertemuan_id).order("created_at").execute()
    return {"data": result.data or []}


@router.delete("/{modul_id}")
async def hapus_modul(modul_id: str, user: dict = Depends(guru_only)):
    """Hapus modul PDF"""
    db.table("modul_pdf").delete().eq("id", modul_id).execute()
    return {"message": "Modul berhasil dihapus"}
