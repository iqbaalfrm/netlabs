# Middleware autentikasi — JWT verification + token blacklist
from fastapi import Depends, HTTPException
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from jose import JWTError, jwt
from app.config import JWT_SECRET, JWT_ALGORITHM
from app.database import db

security = HTTPBearer()


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
    credentials: HTTPAuthorizationCredentials = Depends(security),
) -> dict:
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
