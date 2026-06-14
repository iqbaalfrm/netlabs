# Pagination helper — reusable untuk semua endpoint list
from typing import Any


def paginate_params(page: int = 1, limit: int = 10) -> tuple[int, int]:
    """Normalize pagination params."""
    page = max(1, page)
    limit = max(1, min(100, limit))
    return page, limit


def paginate_response(data: list, page: int, limit: int, total: int, message: str = "OK") -> dict[str, Any]:
    """Build standardized paginated response."""
    total_pages = (total + limit - 1) // limit if total > 0 else 0
    return {
        "data": data,
        "meta": {
            "page": page,
            "limit": limit,
            "total": total,
            "total_pages": total_pages,
            "has_next": page < total_pages,
            "has_prev": page > 1,
        },
        "message": message,
    }
