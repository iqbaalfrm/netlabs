# Entry point FastAPI Netlabs
# Jalankan: uvicorn main:app --reload
# Docs: http://localhost:8000/docs

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.routers import auth, pertemuan, topik, chat, kuis, nilai, guru, modul

app = FastAPI(
    title="Netlabs API",
    description="API untuk ITS + LMS Praktikum Jaringan Komputer SMK",
    version="1.0.0",
    redirect_slashes=False,
)

# CORS — izinkan akses dari Flutter & React
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=False,
    allow_methods=["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    allow_headers=["*"],
)

# Daftarkan semua router
app.include_router(auth.router,      prefix="/api/auth",      tags=["Auth"])
app.include_router(pertemuan.router, prefix="/api/pertemuan", tags=["Pertemuan"])
app.include_router(topik.router,     prefix="/api/topik",     tags=["Topik"])
app.include_router(chat.router,      prefix="/api/chat",      tags=["AI Chat"])
app.include_router(kuis.router,      prefix="/api/kuis",      tags=["Kuis"])
app.include_router(nilai.router,     prefix="/api/nilai",     tags=["Nilai"])
app.include_router(guru.router,      prefix="/api/guru",      tags=["Guru Dashboard"])
app.include_router(modul.router,     prefix="/api/modul",     tags=["Modul PDF"])


@app.get("/")
async def root():
    return {"message": "Netlabs API berjalan! Buka /docs untuk dokumentasi."}
