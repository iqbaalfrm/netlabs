# Rate limiter sederhana untuk endpoint chat
# Mencegah spam request ke Claude API
from fastapi import HTTPException
from datetime import datetime, timedelta
from collections import defaultdict

# Simpan waktu request per user (in-memory, reset saat restart)
_request_log: dict[str, list[datetime]] = defaultdict(list)

# Maksimal 20 request per menit per user
MAX_REQUEST = 20
WINDOW_MENIT = 1


def cek_rate_limit(user_id: str):
    """
    Cek apakah user sudah melebihi batas request.
    Lempar HTTPException 429 jika sudah limit.
    """
    sekarang = datetime.utcnow()
    batas = sekarang - timedelta(minutes=WINDOW_MENIT)

    # Hapus request lama di luar window
    _request_log[user_id] = [
        t for t in _request_log[user_id] if t > batas
    ]

    # Cek batas
    if len(_request_log[user_id]) >= MAX_REQUEST:
        raise HTTPException(
            status_code=429,
            detail=f"Terlalu banyak request. Maksimal {MAX_REQUEST} per menit."
        )

    # Catat request ini
    _request_log[user_id].append(sekarang)
