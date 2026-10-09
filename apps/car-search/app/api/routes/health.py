from fastapi import APIRouter, Depends, HTTPException, status
from pymysql import MySQLError

from app.api.dependencies import get_repository
from app.repositories.car_repository import CarRepository
from app.schemas.health import HealthResponse


router = APIRouter(tags=["health"])


@router.get("/health", response_model=HealthResponse)
def health(repository: CarRepository = Depends(get_repository)) -> HealthResponse:
    try:
        repository.ping()
    except MySQLError as error:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Database is unavailable",
        ) from error

    return HealthResponse(status="ok", service="car-search", database="reachable")
