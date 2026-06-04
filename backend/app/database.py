# Koneksi ke Supabase
from fastapi import HTTPException
from app.config import SUPABASE_URL, SUPABASE_SERVICE_KEY


class UnavailableDatabase:
    def table(self, *_args, **_kwargs):
        raise HTTPException(
            status_code=503,
            detail=(
                "Supabase belum dikonfigurasi. Isi SUPABASE_URL dan "
                "SUPABASE_SERVICE_KEY di file .env backend."
            ),
        )


def create_database_client():
    if not SUPABASE_URL or not SUPABASE_SERVICE_KEY:
        return UnavailableDatabase()

    from supabase import create_client

    # Pakai service key supaya bisa bypass RLS di backend saja.
    return create_client(SUPABASE_URL, SUPABASE_SERVICE_KEY)


db = create_database_client()
