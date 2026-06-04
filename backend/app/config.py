# Konfigurasi — baca variabel dari .env
import os
from dotenv import load_dotenv

load_dotenv()

SUPABASE_URL = os.getenv("SUPABASE_URL")
SUPABASE_KEY = os.getenv("SUPABASE_KEY")
SUPABASE_SERVICE_KEY = os.getenv("SUPABASE_SERVICE_KEY")
ANTHROPIC_API_KEY = os.getenv("ANTHROPIC_API_KEY")
JWT_SECRET = os.getenv("JWT_SECRET", "rahasia-default")
JWT_ALGORITHM = "HS256"
JWT_EXPIRE_HOURS = 24
CHROMA_PATH = os.getenv("CHROMA_PATH", "./chroma_db")
