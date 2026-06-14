# Rate limiter sederhana untuk endpoint chat (backup in-memory)
from fastapi import HTTPException
from datetime import datetime, timedelta
from collections import defaultdict

_request_log: dict[str, list[datetime]] = defaultdict(list)

MAX_REQUEST = 20
WINDOW_MENIT = 1


def cek_rate_limit(user_id: str):
    """Cek rate limit per user. 429 jika melebihi batas."""
    sekarang = datetime.utcnow()
    batas = sekarang - timedelta(minutes=WINDOW_MENIT)

    _request_log[user_id] = [
        t for t in _request_log[user_id] if t > batas
    ]

    if len(_request_log[user_id]) >= MAX_REQUEST:
        sisa_detik = 60 - (sekarang - _request_log[user_id][0]).seconds
        raise HTTPException(
            status_code=429,
            detail=f"Terlalu banyak permintaan. Coba lagi dalam {max(sisa_detik, 1)} detik."
        )

    _request_log[user_id].append(sekarang)
