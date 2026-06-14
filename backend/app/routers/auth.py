# Router autentikasi — login, profil, ganti password, refresh token, logout
import uuid
from fastapi import APIRouter, Depends, HTTPException, Request
from datetime import datetime, timedelta
from jose import jwt
import bcrypt
from slowapi import Limiter
from slowapi.util import get_remote_address

from app.config import JWT_SECRET, JWT_ALGORITHM, JWT_EXPIRE_HOURS
from app.database import db
from app.middleware.auth import get_current_user
from app.schemas import LoginRequest

router = APIRouter()
limiter = Limiter(key_func=get_remote_address)

REFRESH_TOKEN_HOURS = 24 * 7  # 7 days


def _verify_password(plain: str, hashed: str) -> bool:
    return bcrypt.checkpw(plain.encode(), hashed.encode())


def _hash_password(plain: str) -> str:
    return bcrypt.hashpw(plain.encode(), bcrypt.gensalt(rounds=12)).decode()


def _create_token(user_id: str, role: str, expire_hours: int = None) -> tuple[str, str]:
    """Create JWT token with jti claim. Returns (token, jti)."""
    jti = str(uuid.uuid4())
    expire = datetime.utcnow() + timedelta(hours=expire_hours or JWT_EXPIRE_HOURS)
    token = jwt.encode(
        {"user_id": user_id, "role": role, "exp": expire, "jti": jti},
        JWT_SECRET, algorithm=JWT_ALGORITHM,
    )
    return token, jti


@router.post("/login")
@limiter.limit("5/minute")
async def login(request: Request, body: LoginRequest):
    """Login siswa atau guru — return JWT token"""
    nis = body.nis.strip()
    password = body.password

    result = db.table("users").select("*").eq("nis", nis).execute()

    if not result.data:
        raise HTTPException(status_code=401, detail="NIS atau password salah")

    user = result.data[0]

    # Check locked
    locked_until = user.get("locked_until")
    if locked_until:
        try:
            lock_str = locked_until.replace("Z", "+00:00") if isinstance(locked_until, str) else str(locked_until)
            lock_time = datetime.fromisoformat(lock_str)
            if datetime.utcnow() < lock_time.replace(tzinfo=None):
                raise HTTPException(status_code=423, detail="Akun terkunci sementara. Coba lagi dalam 15 menit.")
        except (ValueError, TypeError):
            pass

    if not _verify_password(password, user["password_hash"]):
        attempts = (user.get("failed_login_attempts") or 0) + 1
        update_data = {"failed_login_attempts": attempts}
        if attempts >= 5:
            update_data["locked_until"] = (datetime.utcnow() + timedelta(minutes=15)).isoformat()
        db.table("users").update(update_data).eq("id", user["id"]).execute()
        raise HTTPException(status_code=401, detail="NIS atau password salah")

    # Reset failed attempts
    db.table("users").update({"failed_login_attempts": 0, "locked_until": None}).eq("id", user["id"]).execute()

    token, _ = _create_token(user["id"], user["role"])

    return {
        "data": {
            "token": token,
            "user": {
                "id": user["id"],
                "nis": user["nis"],
                "nama": user["nama"],
                "role": user["role"],
                "kelas": user.get("kelas"),
                "sekolah": user.get("sekolah"),
                "is_first_login": user.get("is_first_login", False),
            },
        },
        "message": "Login berhasil",
    }


@router.post("/refresh")
async def refresh_token(user: dict = Depends(get_current_user)):
    """Refresh JWT token"""
    token, _ = _create_token(user["id"], user["role"])
    return {"data": {"token": token}, "message": "Token berhasil diperbarui"}


@router.post("/logout")
async def logout(request: Request, user: dict = Depends(get_current_user)):
    """Logout — blacklist current token"""
    auth_header = request.headers.get("Authorization", "")
    token = auth_header.replace("Bearer ", "")

    try:
        payload = jwt.decode(token, JWT_SECRET, algorithms=[JWT_ALGORITHM])
        jti = payload.get("jti")
        exp = payload.get("exp")

        if jti and exp:
            db.table("token_blacklist").insert({
                "token_jti": jti,
                "user_id": user["id"],
                "expired_at": datetime.utcfromtimestamp(exp).isoformat(),
            }).execute()
    except Exception:
        pass

    return {"data": None, "message": "Logout berhasil"}


@router.get("/me")
async def profil_saya(user: dict = Depends(get_current_user)):
    """Ambil data user yang sedang login"""
    return {
        "data": {
            "id": user["id"],
            "nis": user["nis"],
            "nama": user["nama"],
            "role": user["role"],
            "kelas": user.get("kelas"),
            "sekolah": user.get("sekolah"),
            "streak_hari": user.get("streak_hari", 0),
            "total_chat": user.get("total_chat", 0),
            "is_first_login": user.get("is_first_login", False),
        },
        "message": "OK",
    }


@router.put("/profil")
async def update_profil(
    user: dict = Depends(get_current_user),
    nama: str = None,
    sekolah: str = None,
):
    """Update profil user"""
    update_data = {}
    if nama and len(nama.strip()) > 0:
        if len(nama) > 100:
            raise HTTPException(status_code=422, detail="Nama maksimal 100 karakter")
        update_data["nama"] = nama.strip()
    if sekolah is not None:
        if len(sekolah) > 100:
            raise HTTPException(status_code=422, detail="Sekolah maksimal 100 karakter")
        update_data["sekolah"] = sekolah.strip()

    if not update_data:
        raise HTTPException(status_code=400, detail="Tidak ada data yang diupdate")

    db.table("users").update(update_data).eq("id", user["id"]).execute()
    return {"data": None, "message": "Profil berhasil diperbarui"}


@router.post("/ganti-password")
async def ganti_password(
    user: dict = Depends(get_current_user),
    password_lama: str = "",
    password_baru: str = "",
):
    """Ganti password user"""
    if not password_baru or len(password_baru) < 6:
        raise HTTPException(status_code=422, detail="Password baru minimal 6 karakter")
    if len(password_baru) > 128:
        raise HTTPException(status_code=422, detail="Password terlalu panjang")

    if not user.get("is_first_login"):
        if not password_lama:
            raise HTTPException(status_code=422, detail="Password lama wajib diisi")
        if not _verify_password(password_lama, user["password_hash"]):
            raise HTTPException(status_code=401, detail="Password lama salah")

    db.table("users").update({
        "password_hash": _hash_password(password_baru),
        "is_first_login": False,
    }).eq("id", user["id"]).execute()

    return {"data": None, "message": "Password berhasil diganti"}
