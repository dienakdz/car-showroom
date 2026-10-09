from typing import Any

from pydantic import BaseModel, ConfigDict, Field


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
