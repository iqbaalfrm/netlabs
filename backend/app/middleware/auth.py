# Middleware autentikasi — cek JWT token di setiap request
from fastapi import Depends, HTTPException
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from jose import JWTError, jwt
from app.config import JWT_SECRET, JWT_ALGORITHM
from app.database import db

security = HTTPBearer()


# Fungsi untuk verifikasi token dan ambil data user
async def get_current_user(
    credentials: HTTPAuthorizationCredentials = Depends(security),
) -> dict:
    token = credentials.credentials

    try:
        # Decode token
        payload = jwt.decode(token, JWT_SECRET, algorithms=[JWT_ALGORITHM])
        user_id = payload.get("user_id")

        if not user_id:
            raise HTTPException(status_code=401, detail="Token tidak valid")

        # Ambil data user dari database
        result = db.table("users").select("*").eq("id", user_id).single().execute()

        if not result.data:
            raise HTTPException(status_code=401, detail="User tidak ditemukan")

        return result.data

    except JWTError:
        raise HTTPException(status_code=401, detail="Token kadaluarsa")


# Middleware khusus guru
async def guru_only(user: dict = Depends(get_current_user)) -> dict:
    if user.get("role") != "guru":
        raise HTTPException(status_code=403, detail="Hanya guru yang bisa akses")
    return user


# Middleware khusus siswa
async def siswa_only(user: dict = Depends(get_current_user)) -> dict:
    if user.get("role") != "siswa":
        raise HTTPException(status_code=403, detail="Hanya siswa yang bisa akses")
    return user
