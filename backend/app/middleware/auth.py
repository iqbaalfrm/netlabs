# Middleware autentikasi — JWT verification + token blacklist + internal service key
from fastapi import Depends, HTTPException, Request
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from jose import JWTError, jwt
from app.config import JWT_SECRET, JWT_ALGORITHM, RAG_SERVICE_KEY
from app.database import db

security = HTTPBearer()


def _verify_service_key(request: Request) -> bool:
    """Cek apakah request dari Laravel (internal) via X-Service-Key header."""
    key = request.headers.get("X-Service-Key", "")
    return bool(RAG_SERVICE_KEY and key == RAG_SERVICE_KEY)


def _is_token_blacklisted(token: str) -> bool:
    """Cek apakah token ada di blacklist (sudah logout)."""
    try:
        payload = jwt.decode(token, JWT_SECRET, algorithms=[JWT_ALGORITHM], options={"verify_exp": False})
        jti = payload.get("jti")
        if not jti:
            return False
        result = db.table("token_blacklist").select("id").eq("token_jti", jti).limit(1).execute()
        return bool(result.data)
    except (JWTError, Exception):
        return False


async def get_current_user(
    request: Request,
    credentials: HTTPAuthorizationCredentials = Depends(security),
) -> dict:
    # ── Path 1: Internal service key dari Laravel ──
    if _verify_service_key(request):
        user_id = request.headers.get("X-User-Id", "")
        user_role = request.headers.get("X-User-Role", "")
        if not user_id:
            raise HTTPException(status_code=401, detail="X-User-Id header wajib untuk service key")

        # Ambil data user dari DB (agar tetap konsisten)
        result = db.table("users").select("*").eq("id", user_id).single().execute()
        if result.data:
            return result.data

        # Fallback: buat user dict minimal dari header
        return {"id": user_id, "role": user_role or "siswa"}

    # ── Path 2: JWT token normal (dari mobile app langsung) ──
    token = credentials.credentials

    if _is_token_blacklisted(token):
        raise HTTPException(status_code=401, detail="Token sudah tidak valid")

    try:
        payload = jwt.decode(token, JWT_SECRET, algorithms=[JWT_ALGORITHM])
        user_id = payload.get("user_id")

        if not user_id:
            raise HTTPException(status_code=401, detail="Token tidak valid")

        result = db.table("users").select("*").eq("id", user_id).single().execute()

        if not result.data:
            raise HTTPException(status_code=401, detail="User tidak ditemukan")

        return result.data

    except JWTError:
        raise HTTPException(status_code=401, detail="Token kadaluarsa atau tidak valid")


async def guru_only(user: dict = Depends(get_current_user)) -> dict:
    if user.get("role") != "guru":
        raise HTTPException(status_code=403, detail="Hanya guru yang bisa akses")
    return user


async def siswa_only(user: dict = Depends(get_current_user)) -> dict:
    if user.get("role") != "siswa":
        raise HTTPException(status_code=403, detail="Hanya siswa yang bisa akses")
    return user
