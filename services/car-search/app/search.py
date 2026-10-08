from __future__ import annotations

from dataclasses import dataclass
import re
import time
import unicodedata

from rank_bm25 import BM25Okapi
from rapidfuzz import fuzz

from app.models import (
    CarDocument,
    SearchFilters,
    SearchHit,
    SearchIntent,
    SearchRequest,
    SearchResponse,
)
from app.repository import CarRepository


STOP_WORDS = {
    "ban",
    "chiec",
    "cho",
    "co",
    "con",
    "dep",
    "di",
    "giup",
    "la",
    "minh",
    "muon",
    "nao",
    "rat",
    "tim",
    "toi",
    "va",
    "xe",
    "xem",
}

ALIASES = {
    "mec": "mercedes-benz",
    "mẹc": "mercedes-benz",
    "mer": "mercedes-benz",
    "mac da": "mazda",
    "bim": "bmw",
    "lech xu": "lexus",
    "huyndai": "hyundai",
    "huyen dai": "hyundai",
    "pot che": "porsche",
    "cx nam": "cx-5",
}

CONDITION_TERMS = {
    "new": ("xe moi", "moi 100", "new", "moi"),
    "used": ("xe cu", "da qua su dung", "sieu luot", "used", "cu"),
    "cpo": ("cpo", "certified pre-owned", "xe chung nhan"),
}

BODY_TYPE_TERMS = {
    "suv": ("suv", "gam cao"),
    "sedan": ("sedan",),
    "hatchback": ("hatchback",),
    "pickup": ("pickup", "ban tai"),
}

FUEL_TYPE_TERMS = {
    "gasoline": ("gasoline", "may xang", "xe xang"),
    "diesel": ("diesel", "may dau", "xe dau"),
    "hybrid": ("hybrid", "xang lai dien"),
    "electric": ("electric", "xe dien", "thuan dien", "ev"),
}

TRANSMISSION_TERMS = {
    "automatic": ("automatic", "so tu dong"),
    "manual": ("manual", "so san"),
    "cvt": ("cvt", "vo cap"),
}

DRIVETRAIN_TERMS = {
    "fwd": ("fwd", "dan dong cau truoc", "cau truoc"),
    "rwd": ("rwd", "dan dong cau sau", "cau sau"),
    "awd": ("awd", "dan dong 4 banh toan thoi gian"),
    "4wd": ("4wd", "4x4", "hai cau", "2 cau"),
}

COLOR_TERMS = {
    "obsidian-black": ("mau den", "xe den", "black"),
    "pearl-white": ("mau trang", "xe trang", "white"),
    "candy-red": ("mau do", "xe do", "red"),
    "ocean-blue": ("mau xanh", "xe xanh", "xanh duong", "blue"),
}


def fold_text(value: str) -> str:
    normalized = unicodedata.normalize("NFKD", value.lower().strip())
    without_marks = "".join(char for char in normalized if not unicodedata.combining(char))
    without_marks = without_marks.replace("đ", "d")
    return re.sub(r"\s+", " ", without_marks).strip()


def normalize_query(value: str) -> str:
    query = fold_text(value)
    for alias, replacement in sorted(ALIASES.items(), key=lambda item: len(item[0]), reverse=True):
        query = re.sub(rf"(?<![a-z0-9]){re.escape(alias)}(?![a-z0-9])", replacement, query)
    return re.sub(r"\s+", " ", query).strip()


def tokenize(value: str) -> list[str]:
    folded = fold_text(value)
    tokens = re.findall(r"[a-z0-9]+(?:-[a-z0-9]+)*", folded)
    identifiers = [
        f"{letters}{digits}"
        for letters, digits in re.findall(r"(?<![a-z0-9])([a-z]{1,5})[\s-]+(\d{1,4})(?!\d)", folded)
    ]
    return [
        token
        for token in [*tokens, *identifiers]
        if token not in STOP_WORDS and (len(token) > 1 or token.isdigit())
    ]


@dataclass(frozen=True, slots=True)
class ResolvedEntity:
    slug: str
    name: str
    confidence: float
    exact: bool


class CarSearchEngine:
    def __init__(self, repository: CarRepository) -> None:
        self.repository = repository

    def search(self, request: SearchRequest) -> SearchResponse:
        started_at = time.perf_counter()
        documents = self.repository.load_public_cars()
        normalized = normalize_query(request.query)
        intent = self._parse_intent(normalized, documents, request.filters)
        candidates = [document for document in documents if self._matches(document, intent)]
        ranked = self._rank(normalized, candidates, intent)
        match_mode = "strict" if ranked else "none"

        if not ranked and self._has_structured_intent(intent):
            relaxed_documents = self._identity_candidates(documents, intent)
            ranked = self._rank_relaxed(normalized, relaxed_documents, intent)
            match_mode = "relaxed" if ranked else "none"

        hits = ranked[: request.limit]
        elapsed = round((time.perf_counter() - started_at) * 1000, 2)
        return SearchResponse(
            query=request.query,
            normalized_query=normalized,
            intent=intent,
            match_mode=match_mode,
            hits=hits,
            total=len(ranked),
            took_ms=elapsed,
        )

    def _parse_intent(
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

    @staticmethod
    def _has_structured_intent(intent: SearchIntent) -> bool:
        return any(value is not None for value in intent.model_dump().values())

    @staticmethod
    def _identity_candidates(
        documents: list[CarDocument],
        intent: SearchIntent,
    ) -> list[CarDocument]:
        identity_fields = (
            ("trim", "trim_slug"),
            ("model", "model_slug"),
            ("make", "make_slug"),
        )

        for intent_field, document_field in identity_fields:
            expected = getattr(intent, intent_field)
            if expected is None:
                continue

            matching = [
                document
                for document in documents
                if getattr(document, document_field) == expected
            ]
            if matching:
                return matching

        return documents

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
                best = ResolvedEntity(slug=slug, name=name, confidence=confidence, exact=False)

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

    @staticmethod
    def _matches(document: CarDocument, intent: SearchIntent) -> bool:
        string_filters = {
            "make": "make_slug",
            "model": "model_slug",
            "trim": "trim_slug",
            "condition": "condition",
            "body_type": "body_type_slug",
            "fuel_type": "fuel_type_slug",
            "transmission": "transmission_slug",
            "drivetrain": "drivetrain_slug",
            "exterior_color": "exterior_color_slug",
        }
        for intent_field, document_field in string_filters.items():
            expected = getattr(intent, intent_field)
            if expected is not None and getattr(document, document_field) != expected:
                return False

        if intent.seats is not None and int(document.attributes.get("seats", 0)) != intent.seats:
            return False
        if intent.year is not None and document.year != intent.year:
            return False
        if intent.min_year is not None and document.year < intent.min_year:
            return False
        if intent.max_year is not None and document.year > intent.max_year:
            return False
        if intent.min_price is not None and (document.price is None or document.price < intent.min_price):
            return False
        if intent.max_price is not None and (document.price is None or document.price > intent.max_price):
            return False
        if intent.min_mileage is not None and (
            document.mileage is None or document.mileage < intent.min_mileage
        ):
            return False
        if intent.max_mileage is not None and (
            document.mileage is None or document.mileage > intent.max_mileage
        ):
            return False
        return True

    def _rank(
        self,
        query: str,
        documents: list[CarDocument],
        intent: SearchIntent,
    ) -> list[SearchHit]:
        if not documents:
            return []

        query_tokens = tokenize(query)
        has_intent = self._has_structured_intent(intent)
        if not query_tokens and not has_intent:
            return []

        corpus = [tokenize(self._document_text(document)) for document in documents]
        lexical_scores = BM25Okapi(corpus).get_scores(query_tokens) if query_tokens else [0.0] * len(documents)
        positive_scores = [max(0.0, float(score)) for score in lexical_scores]
        max_lexical = max(positive_scores, default=0.0)
        query_token_set = set(query_tokens)

        ranked_rows: list[tuple[float, CarDocument, list[str]]] = []
        for index, document in enumerate(documents):
            lexical = positive_scores[index] / max_lexical if max_lexical > 0 else 0.0
            overlap = (
                len(query_token_set.intersection(corpus[index])) / len(query_token_set)
                if query_token_set
                else 0.0
            )
            score, reasons = self._structured_score(document, intent)
            score += lexical * 4.0 + overlap
            if lexical >= 0.15 or overlap >= 0.2:
                reasons.append("Mô tả và trang bị phù hợp với từ khóa")

            if score <= 0 and not has_intent:
                continue
            ranked_rows.append((score, document, reasons))

        ranked_rows.sort(
            key=lambda row: (
                row[0],
                row[1].year,
                row[1].published_at,
                row[1].car_unit_id,
            ),
            reverse=True,
        )
        max_score = max((row[0] for row in ranked_rows), default=1.0)

        return [
            SearchHit(
                car_unit_id=document.car_unit_id,
                stock_code=document.stock_code,
                title=document.title,
                score=round(score / max_score, 4) if max_score > 0 else 0.0,
                reasons=reasons[:4],
            )
            for score, document, reasons in ranked_rows
        ]

    def _rank_relaxed(
        self,
        query: str,
        documents: list[CarDocument],
        intent: SearchIntent,
    ) -> list[SearchHit]:
        if not documents:
            return []

        query_tokens = tokenize(query)
        corpus = [tokenize(self._document_text(document)) for document in documents]
        lexical_scores = BM25Okapi(corpus).get_scores(query_tokens) if query_tokens else [0.0] * len(documents)
        positive_scores = [max(0.0, float(score)) for score in lexical_scores]
        max_lexical = max(positive_scores, default=0.0)

        ranked_rows: list[tuple[float, CarDocument, list[str], list[str]]] = []
        for index, document in enumerate(documents):
            score, reasons, relaxed_constraints = self._relaxed_structured_score(document, intent)
            lexical = positive_scores[index] / max_lexical if max_lexical > 0 else 0.0
            score += lexical * 2.0

            if score <= 0:
                continue

            ranked_rows.append((score, document, reasons, relaxed_constraints))

        ranked_rows.sort(
            key=lambda row: (
                row[0],
                -len(row[3]),
                row[1].year,
                row[1].published_at,
                row[1].car_unit_id,
            ),
            reverse=True,
        )
        max_score = max((row[0] for row in ranked_rows), default=1.0)

        return [
            SearchHit(
                car_unit_id=document.car_unit_id,
                stock_code=document.stock_code,
                title=document.title,
                score=round(score / max_score, 4) if max_score > 0 else 0.0,
                reasons=reasons[:4],
                relaxed_constraints=relaxed_constraints[:4],
            )
            for score, document, reasons, relaxed_constraints in ranked_rows
        ]

    def _relaxed_structured_score(
        self,
        document: CarDocument,
        intent: SearchIntent,
    ) -> tuple[float, list[str], list[str]]:
        score = 0.0
        reasons: list[str] = []
        relaxed: list[str] = []

        preferences = (
            (intent.make, document.make_slug, 8.0, f"Đúng hãng {document.make}", "Hãng xe"),
            (intent.model, document.model_slug, 10.0, f"Đúng dòng xe {document.model}", "Dòng xe"),
            (intent.trim, document.trim_slug, 12.0, f"Đúng phiên bản {document.trim}", "Phiên bản"),
            (intent.condition, document.condition, 3.0, "Đúng tình trạng xe", "Tình trạng"),
            (intent.body_type, document.body_type_slug, 2.5, "Đúng kiểu dáng", "Kiểu dáng"),
            (intent.fuel_type, document.fuel_type_slug, 2.5, "Đúng loại nhiên liệu", "Nhiên liệu"),
            (intent.transmission, document.transmission_slug, 1.5, "Đúng loại hộp số", "Hộp số"),
            (intent.drivetrain, document.drivetrain_slug, 1.5, "Đúng hệ dẫn động", "Hệ dẫn động"),
            (intent.exterior_color, document.exterior_color_slug, 1.0, "Đúng màu ngoại thất", "Màu ngoại thất"),
        )

        for expected, actual, weight, reason, label in preferences:
            if expected is None:
                continue
            if actual == expected:
                score += weight
                reasons.append(reason)
                continue

            relaxed.append(f"{label} {actual or 'chưa cập nhật'} thay vì {expected}")

        score += self._score_seats(document, intent, reasons, relaxed)
        score += self._score_year(document, intent, reasons, relaxed)
        score += self._score_price(document, intent, reasons, relaxed)
        score += self._score_mileage(document, intent, reasons, relaxed)

        return score, reasons, relaxed

    @staticmethod
    def _score_seats(
        document: CarDocument,
        intent: SearchIntent,
        reasons: list[str],
        relaxed: list[str],
    ) -> float:
        if intent.seats is None:
            return 0.0

        value = document.attributes.get("seats")
        actual = int(float(value)) if value is not None else None
        if actual == intent.seats:
            reasons.append("Đúng số chỗ")
            return 4.0

        if actual is None:
            relaxed.append("Số chỗ chưa được cập nhật")
            return 0.0

        relaxed.append(f"Xe có {actual} chỗ thay vì {intent.seats} chỗ")
        difference = abs(actual - intent.seats)
        return max(0.0, 4.0 * (1.0 - min(difference, 4) / 4))

    @staticmethod
    def _score_year(
        document: CarDocument,
        intent: SearchIntent,
        reasons: list[str],
        relaxed: list[str],
    ) -> float:
        score = 0.0

        if intent.year is not None:
            difference = abs(document.year - intent.year)
            if difference == 0:
                reasons.append("Đúng năm sản xuất")
                score += 3.0
            else:
                relaxed.append(f"Năm {document.year} thay vì {intent.year}")
                score += max(0.0, 3.0 * (1.0 - min(difference, 10) / 10))

        if intent.min_year is not None:
            if document.year >= intent.min_year:
                reasons.append("Đạt năm sản xuất tối thiểu")
                score += 2.0
            else:
                difference = intent.min_year - document.year
                relaxed.append(f"Năm {document.year}, thấp hơn yêu cầu {intent.min_year}")
                score += max(0.0, 2.0 * (1.0 - min(difference, 10) / 10))

        if intent.max_year is not None:
            if document.year <= intent.max_year:
                reasons.append("Đạt năm sản xuất tối đa")
                score += 2.0
            else:
                difference = document.year - intent.max_year
                relaxed.append(f"Năm {document.year}, cao hơn yêu cầu {intent.max_year}")
                score += max(0.0, 2.0 * (1.0 - min(difference, 10) / 10))

        return score

    def _score_price(
        self,
        document: CarDocument,
        intent: SearchIntent,
        reasons: list[str],
        relaxed: list[str],
    ) -> float:
        if intent.min_price is None and intent.max_price is None:
            return 0.0

        if document.price is None:
            relaxed.append("Giá xe chưa được cập nhật")
            return 0.0

        score = 0.0
        if intent.min_price is not None:
            if document.price >= intent.min_price:
                reasons.append("Đạt mức giá tối thiểu")
                score += 3.0
            else:
                relaxed.append(
                    f"Giá {self._format_price(document.price)}, thấp hơn {self._format_price(intent.min_price)}"
                )
                score += self._numeric_proximity(document.price, intent.min_price, 3.0)

        if intent.max_price is not None:
            if document.price <= intent.max_price:
                reasons.append("Trong ngân sách tối đa")
                score += 3.0
            else:
                relaxed.append(
                    f"Giá {self._format_price(document.price)}, vượt ngân sách {self._format_price(intent.max_price)}"
                )
                score += self._numeric_proximity(document.price, intent.max_price, 3.0)

        return score

    def _score_mileage(
        self,
        document: CarDocument,
        intent: SearchIntent,
        reasons: list[str],
        relaxed: list[str],
    ) -> float:
        if intent.min_mileage is None and intent.max_mileage is None:
            return 0.0

        if document.mileage is None:
            relaxed.append("Số kilomet chưa được cập nhật")
            return 0.0

        score = 0.0
        if intent.min_mileage is not None:
            if document.mileage >= intent.min_mileage:
                reasons.append("Đạt số kilomet tối thiểu")
                score += 2.0
            else:
                relaxed.append(
                    f"Odo {document.mileage:,} km, thấp hơn {intent.min_mileage:,} km"
                )
                score += self._numeric_proximity(document.mileage, intent.min_mileage, 2.0)

        if intent.max_mileage is not None:
            if document.mileage <= intent.max_mileage:
                reasons.append("Trong giới hạn kilomet")
                score += 2.0
            else:
                relaxed.append(
                    f"Odo {document.mileage:,} km, vượt giới hạn {intent.max_mileage:,} km"
                )
                score += self._numeric_proximity(document.mileage, intent.max_mileage, 2.0)

        return score

    @staticmethod
    def _numeric_proximity(actual: int, target: int, weight: float) -> float:
        if target <= 0:
            return 0.0

        difference_ratio = abs(actual - target) / target
        return max(0.0, weight * (1.0 - min(difference_ratio, 1.0)))

    @staticmethod
    def _format_price(value: int) -> str:
        if value >= 1_000_000_000:
            amount = f"{value / 1_000_000_000:.2f}".rstrip("0").rstrip(".")
            return f"{amount} tỷ"

        amount = f"{value / 1_000_000:.0f}"
        return f"{amount} triệu"

    @staticmethod
    def _structured_score(document: CarDocument, intent: SearchIntent) -> tuple[float, list[str]]:
        score = 0.0
        reasons: list[str] = []

        if intent.trim is not None:
            score += 6.0
            reasons.append(f"Đúng phiên bản {document.trim}")
        elif intent.model is not None:
            score += 4.0
            reasons.append(f"Đúng dòng xe {document.model}")
        elif intent.make is not None:
            score += 2.5
            reasons.append(f"Đúng hãng {document.make}")

        structured_reasons = (
            (intent.condition, 0.8, "Đúng tình trạng xe"),
            (intent.body_type, 0.8, "Đúng kiểu dáng"),
            (intent.fuel_type, 0.8, "Đúng loại nhiên liệu"),
            (intent.transmission, 0.5, "Đúng loại hộp số"),
            (intent.drivetrain, 0.5, "Đúng hệ dẫn động"),
            (intent.exterior_color, 0.5, "Đúng màu ngoại thất"),
            (intent.seats, 0.8, "Đúng số chỗ"),
            (intent.year or intent.min_year or intent.max_year, 0.5, "Đúng năm sản xuất"),
            (intent.min_price or intent.max_price, 0.8, "Đúng khoảng giá"),
            (intent.min_mileage or intent.max_mileage, 0.5, "Đúng số kilomet"),
        )
        for value, weight, reason in structured_reasons:
            if value is not None:
                score += weight
                reasons.append(reason)

        return score, reasons

    @staticmethod
    def _document_text(document: CarDocument) -> str:
        attributes = " ".join(f"{key} {value}" for key, value in document.attributes.items())
        fields = [
            document.title,
            document.title,
            document.make,
            document.model,
            document.trim,
            document.description,
            document.body_type or "",
            document.fuel_type or "",
            document.transmission or "",
            document.drivetrain or "",
            document.exterior_color or "",
            " ".join(document.features),
            attributes,
        ]
        return " ".join(fields)
