# Router autentikasi — login dan profil
from fastapi import APIRouter, Depends, HTTPException
from datetime import datetime, timedelta
from jose import jwt
import bcrypt
from app.config import JWT_SECRET, JWT_ALGORITHM, JWT_EXPIRE_HOURS
from app.database import db
from app.middleware.auth import get_current_user
from app.schemas import LoginRequest

router = APIRouter()


def _verify_password(plain: str, hashed: str) -> bool:
    """Verifikasi password dengan bcrypt"""
    return bcrypt.checkpw(plain.encode(), hashed.encode())


@router.post("/login")
async def login(request: LoginRequest):
    """Login siswa atau guru — return JWT token"""
    result = db.table("users").select("*").eq("nis", request.nis).execute()

    if not result.data:
        raise HTTPException(status_code=401, detail="NIS tidak ditemukan")

    user = result.data[0]

    if not _verify_password(request.password, user["password_hash"]):
        raise HTTPException(status_code=401, detail="Password salah")

    expire = datetime.utcnow() + timedelta(hours=JWT_EXPIRE_HOURS)
    token = jwt.encode(
        {"user_id": user["id"], "role": user["role"], "exp": expire},
        JWT_SECRET, algorithm=JWT_ALGORITHM,
    )

    return {
        "token": token,
        "user": {
            "id": user["id"],
            "nis": user["nis"],
            "nama": user["nama"],
            "role": user["role"],
            "kelas": user.get("kelas"),
            "sekolah": user.get("sekolah"),
        },
    }


@router.get("/me")
async def profil_saya(user: dict = Depends(get_current_user)):
    """Ambil data user yang sedang login"""
    return {"data": user}
