from fastapi import APIRouter, Depends, HTTPException, status
from pymysql import MySQLError

from app.api.dependencies import get_search_engine, require_internal_token
from app.schemas.search import SearchRequest, SearchResponse
from app.search.engine import CarSearchEngine


router = APIRouter(tags=["search"])


@router.post(
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
