# Entry point FastAPI Netlabs
# Jalankan: uvicorn main:app --reload
import os
from contextlib import asynccontextmanager

from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from starlette.middleware.base import BaseHTTPMiddleware
from slowapi import Limiter, _rate_limit_exceeded_handler
from slowapi.util import get_remote_address
from slowapi.errors import RateLimitExceeded

from app.routers import auth, pertemuan, topik, chat, kuis, nilai, guru, modul

# Rate limiter global
limiter = Limiter(key_func=get_remote_address, default_limits=["100/minute"])


@asynccontextmanager
async def lifespan(app: FastAPI):
    yield


app = FastAPI(
    title="Netlabs API",
    description="API untuk ITS + LMS Praktikum Jaringan Komputer SMK",
    version="1.0.0",
    redirect_slashes=False,
    lifespan=lifespan,
    docs_url="/docs" if os.getenv("APP_ENV", "development") != "production" else None,
    redoc_url=None,
)

app.state.limiter = limiter
app.add_exception_handler(RateLimitExceeded, _rate_limit_exceeded_handler)

# CORS — whitelist origins
ALLOWED_ORIGINS = [
    "http://localhost:3000",
    "http://localhost:5173",
    "http://localhost:8080",
    "http://127.0.0.1:8080",
    "https://netlabs-admin.vercel.app",
    "http://161.35.55.122",
]

# Add from env if set
extra_origins = os.getenv("CORS_ORIGINS", "")
if extra_origins:
    ALLOWED_ORIGINS.extend([o.strip() for o in extra_origins.split(",") if o.strip()])

app.add_middleware(
    CORSMiddleware,
    allow_origins=ALLOWED_ORIGINS,
    allow_credentials=True,
    allow_methods=["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    allow_headers=["Authorization", "Content-Type"],
)


# Security headers middleware
class SecurityHeadersMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):
        response = await call_next(request)
        response.headers["X-Content-Type-Options"] = "nosniff"
        response.headers["X-Frame-Options"] = "DENY"
        response.headers["X-XSS-Protection"] = "1; mode=block"
        response.headers["Strict-Transport-Security"] = "max-age=31536000; includeSubDomains"
        response.headers["Referrer-Policy"] = "strict-origin-when-cross-origin"
        response.headers["Permissions-Policy"] = "camera=(), microphone=(), geolocation=()"
        return response


app.add_middleware(SecurityHeadersMiddleware)


# Global exception handler — never expose stack traces
@app.exception_handler(Exception)
async def global_exception_handler(request: Request, exc: Exception):
    if os.getenv("APP_ENV") == "production":
        return JSONResponse(
            status_code=500,
            content={"detail": "Terjadi kesalahan internal. Silakan coba lagi."},
        )
    return JSONResponse(
        status_code=500,
        content={"detail": str(exc)},
    )


# Daftarkan semua router
app.include_router(auth.router, prefix="/api/auth", tags=["Auth"])
app.include_router(pertemuan.router, prefix="/api/pertemuan", tags=["Pertemuan"])
app.include_router(topik.router, prefix="/api/topik", tags=["Topik"])
app.include_router(chat.router, prefix="/api/chat", tags=["AI Chat"])
app.include_router(kuis.router, prefix="/api/kuis", tags=["Kuis"])
app.include_router(nilai.router, prefix="/api/nilai", tags=["Nilai"])
app.include_router(guru.router, prefix="/api/guru", tags=["Guru Dashboard"])
app.include_router(modul.router, prefix="/api/modul", tags=["Modul PDF"])


@app.get("/")
async def root():
    return {"message": "Netlabs API berjalan!", "version": "1.0.0"}
