# Konfigurasi — baca variabel dari .env
import os
import secrets
from dotenv import load_dotenv

load_dotenv()

SUPABASE_URL = os.getenv("SUPABASE_URL")
SUPABASE_KEY = os.getenv("SUPABASE_KEY")
SUPABASE_SERVICE_KEY = os.getenv("SUPABASE_SERVICE_KEY")
ANTHROPIC_API_KEY = os.getenv("ANTHROPIC_API_KEY")

# JWT — secret minimal 32 karakter
JWT_SECRET = os.getenv("JWT_SECRET", "")
if len(JWT_SECRET) < 32:
    if os.getenv("APP_ENV") == "production":
        raise RuntimeError(
            "JWT_SECRET wajib diisi di .env untuk production "
            "(minimal 32 karakter). Generate dengan: "
            "python -c 'import secrets; print(secrets.token_urlsafe(48))'"
        )
    JWT_SECRET = secrets.token_urlsafe(48)

JWT_ALGORITHM = "HS256"
JWT_EXPIRE_HOURS = 1

CHROMA_PATH = os.getenv("CHROMA_PATH", "./chroma_db")
APP_ENV = os.getenv("APP_ENV", "development")
