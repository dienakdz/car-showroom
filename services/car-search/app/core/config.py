from dataclasses import dataclass
from functools import lru_cache
import os


@dataclass(frozen=True, slots=True)
class Settings:
    api_token: str
    db_host: str
    db_port: int
    db_database: str
    db_username: str
    db_password: str


@lru_cache
def get_settings() -> Settings:
    return Settings(
        api_token=os.getenv("CAR_SEARCH_API_TOKEN", ""),
        db_host=os.getenv("SEARCH_DB_HOST", "mysql"),
        db_port=int(os.getenv("SEARCH_DB_PORT", "3306")),
        db_database=os.getenv("SEARCH_DB_DATABASE", "car_showroom"),
        db_username=os.getenv("SEARCH_DB_USERNAME", "car"),
        db_password=os.getenv("SEARCH_DB_PASSWORD", "car"),
    )
