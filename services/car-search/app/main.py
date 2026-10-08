from functools import lru_cache
import secrets

from fastapi import Depends, FastAPI, Header, HTTPException, status
from pymysql import MySQLError

from app.config import get_settings
from app.models import HealthResponse, SearchRequest, SearchResponse
from app.repository import CarRepository
from app.search import CarSearchEngine


app = FastAPI(
    title="Car Search Service",
    version="1.0.0",
    description="Structured Vietnamese inventory search with fuzzy matching and BM25 ranking.",
)


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


@app.get("/health", response_model=HealthResponse)
def health(repository: CarRepository = Depends(get_repository)) -> HealthResponse:
    try:
        repository.ping()
    except MySQLError as error:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Database is unavailable",
        ) from error

    return HealthResponse(status="ok", service="car-search", database="reachable")


@app.post(
    "/search",
    response_model=SearchResponse,
    dependencies=[Depends(require_internal_token)],
)
def search(
    request: SearchRequest,
    engine: CarSearchEngine = Depends(get_search_engine),
) -> SearchResponse:
    try:
        return engine.search(request)
    except MySQLError as error:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Database is unavailable",
        ) from error
