from dataclasses import dataclass
import re

from rapidfuzz import fuzz

from app.domain.car import CarDocument
from app.schemas.search import SearchFilters, SearchIntent
from app.search.text import fold_text
from app.search.vocabulary import (
    BODY_TYPE_TERMS,
    COLOR_TERMS,
    CONDITION_TERMS,
    DRIVETRAIN_TERMS,
    FUEL_TYPE_TERMS,
    TRANSMISSION_TERMS,
)


@dataclass(frozen=True, slots=True)
class ResolvedEntity:
    slug: str
    name: str
    confidence: float


class IntentParser:
    def parse(
        self,
        query: str,
        documents: list[CarDocument],
        explicit: SearchFilters,
    ) -> SearchIntent:
        parsed: dict[str, object | None] = {
            "make": self._resolve_entity(query, documents, "make"),
            "model": self._resolve_entity(query, documents, "model"),
            "trim": self._resolve_entity(query, documents, "trim"),
            "condition": self._resolve_term(query, CONDITION_TERMS),
            "body_type": self._resolve_term(query, BODY_TYPE_TERMS),
            "fuel_type": self._resolve_term(query, FUEL_TYPE_TERMS),
            "transmission": self._resolve_term(query, TRANSMISSION_TERMS),
            "drivetrain": self._resolve_term(query, DRIVETRAIN_TERMS),
            "exterior_color": self._resolve_term(query, COLOR_TERMS),
        }
        parsed.update(self._parse_numbers(query))

        for field, value in explicit.model_dump().items():
            if value is not None and value != "":
                parsed[field] = fold_text(value) if isinstance(value, str) else value

        return SearchIntent(**parsed)

    def _resolve_entity(
        self,
        query: str,
        documents: list[CarDocument],
        field: str,
    ) -> str | None:
        slug_field = f"{field}_slug"
        choices = {
            str(getattr(document, slug_field)): str(getattr(document, field))
            for document in documents
        }

        for slug, name in sorted(choices.items(), key=lambda item: len(item[1]), reverse=True):
            values = {fold_text(name), fold_text(slug)}
            for value in values:
                if self._contains_entity(query, value):
                    return slug

        best: ResolvedEntity | None = None
        for slug, name in choices.items():
            candidate = fold_text(name)
            if len(candidate.replace(" ", "")) < 4:
                continue
            confidence = float(fuzz.partial_ratio(query, candidate))
            if confidence < 88:
                continue
            if best is None or confidence > best.confidence:
                best = ResolvedEntity(slug=slug, name=name, confidence=confidence)

        return best.slug if best is not None else None

    @staticmethod
    def _contains_entity(query: str, entity: str) -> bool:
        if re.search(rf"(?<![a-z0-9]){re.escape(entity)}(?![a-z0-9])", query):
            return True
        compact_entity = re.sub(r"[^a-z0-9]", "", entity)
        compact_query = re.sub(r"[^a-z0-9]", "", query)
        return len(compact_entity) >= 3 and compact_entity in compact_query

    @staticmethod
    def _resolve_term(query: str, terms: dict[str, tuple[str, ...]]) -> str | None:
        for value, aliases in terms.items():
            for alias in sorted(aliases, key=len, reverse=True):
                if re.search(rf"(?<![a-z0-9]){re.escape(alias)}(?![a-z0-9])", query):
                    return value
        return None

    def _parse_numbers(self, query: str) -> dict[str, int | None]:
        result: dict[str, int | None] = {}
        price_expression = r"(\d+(?:[.,]\d+)?)\s*(ty|trieu|tr)"

        range_match = re.search(
            rf"(?:tu\s+)?{price_expression}\s*(?:den|toi|-)\s*{price_expression}",
            query,
        )
        if range_match:
            result["min_price"] = self._price_value(range_match.group(1), range_match.group(2))
            result["max_price"] = self._price_value(range_match.group(3), range_match.group(4))
        else:
            under_match = re.search(rf"(?:duoi|khong qua|toi da)\s*{price_expression}", query)
            over_match = re.search(rf"(?:tren|tu)\s*{price_expression}(?:\s*tro len)?", query)
            target_match = re.search(rf"(?:tam|khoang|gia)\s*{price_expression}", query)
            if under_match:
                result["max_price"] = self._price_value(under_match.group(1), under_match.group(2))
            elif over_match:
                result["min_price"] = self._price_value(over_match.group(1), over_match.group(2))
            elif target_match:
                target = self._price_value(target_match.group(1), target_match.group(2))
                result["min_price"] = int(target * 0.85)
                result["max_price"] = int(target * 1.15)

        seats_match = re.search(r"(\d{1,2})\s*(?:cho|seat)", query)
        if seats_match:
            result["seats"] = int(seats_match.group(1))

        min_year_match = re.search(r"(?:tu|doi)\s*(20\d{2})\s*(?:tro len|ve sau)", query)
        max_year_match = re.search(r"(?:truoc|toi da)\s*(20\d{2})", query)
        exact_year_match = re.search(r"(?:doi|nam)\s*(20\d{2})", query)
        if min_year_match:
            result["min_year"] = int(min_year_match.group(1))
        elif max_year_match:
            result["max_year"] = int(max_year_match.group(1))
        elif exact_year_match:
            result["year"] = int(exact_year_match.group(1))

        mileage_match = re.search(r"(?:duoi|khong qua|toi da)\s*(\d[\d.,]*)\s*(?:km|kilomet)", query)
        if mileage_match:
            result["max_mileage"] = int(re.sub(r"[^0-9]", "", mileage_match.group(1)))

        return result

    @staticmethod
    def _price_value(number: str, unit: str) -> int:
        value = float(number.replace(",", "."))
        multiplier = 1_000_000_000 if unit == "ty" else 1_000_000
        return int(value * multiplier)
