from typing import Any, Literal

from pydantic import BaseModel, ConfigDict, Field


class SearchFilters(BaseModel):
    model_config = ConfigDict(extra="forbid")

    make: str | None = None
    model: str | None = None
    trim: str | None = None
    condition: str | None = None
    body_type: str | None = None
    fuel_type: str | None = None
    transmission: str | None = None
    drivetrain: str | None = None
    exterior_color: str | None = None
    seats: int | None = Field(default=None, ge=1, le=20)
    year: int | None = Field(default=None, ge=1900, le=2200)
    min_year: int | None = Field(default=None, ge=1900, le=2200)
    max_year: int | None = Field(default=None, ge=1900, le=2200)
    min_price: int | None = Field(default=None, ge=0)
    max_price: int | None = Field(default=None, ge=0)
    min_mileage: int | None = Field(default=None, ge=0)
    max_mileage: int | None = Field(default=None, ge=0)


class SearchRequest(BaseModel):
    model_config = ConfigDict(extra="forbid")

    query: str = Field(min_length=1, max_length=500)
    filters: SearchFilters = Field(default_factory=SearchFilters)
    limit: int = Field(default=20, ge=1, le=100)


class SearchIntent(SearchFilters):
    pass


class SearchHit(BaseModel):
    car_unit_id: int
    stock_code: str
    title: str
    score: float
    reasons: list[str]
    relaxed_constraints: list[str] = Field(default_factory=list)


class SearchResponse(BaseModel):
    query: str
    normalized_query: str
    intent: SearchIntent
    match_mode: Literal["strict", "relaxed", "none"]
    hits: list[SearchHit]
    total: int
    took_ms: float


class HealthResponse(BaseModel):
    status: str
    service: str
    database: str


class CarDocument(BaseModel):
    model_config = ConfigDict(arbitrary_types_allowed=True)

    car_unit_id: int
    trim_id: int
    stock_code: str
    condition: str
    year: int
    mileage: int | None
    price: int | None
    published_at: Any
    make: str
    make_slug: str
    model: str
    model_slug: str
    trim: str
    trim_slug: str
    description: str
    body_type: str | None
    body_type_slug: str | None
    fuel_type: str | None
    fuel_type_slug: str | None
    transmission: str | None
    transmission_slug: str | None
    drivetrain: str | None
    drivetrain_slug: str | None
    exterior_color: str | None
    exterior_color_slug: str | None
    interior_color: str | None
    interior_color_slug: str | None
    features: list[str] = Field(default_factory=list)
    attributes: dict[str, str | float | bool] = Field(default_factory=dict)

    @property
    def title(self) -> str:
        return f"{self.make} {self.model} {self.trim} {self.year}"
