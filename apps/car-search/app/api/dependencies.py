from functools import lru_cache
import secrets

from fastapi import Header, HTTPException, status

from app.core.config import get_settings
from app.repositories.car_repository import CarRepository
from app.search.engine import CarSearchEngine


@lru_cache
def get_repository() -> CarRepository:
    return CarRepository(get_settings())


@lru_cache
def get_search_engine() -> CarSearchEngine:
    return CarSearchEngine(get_repository())


def require_internal_token(x_internal_token: str | None = Header(default=None)) -> None:
    expected = get_settings().api_token
    if expected and (x_internal_token is None or not secrets.compare_digest(x_internal_token, expected)):
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Invalid internal token")
