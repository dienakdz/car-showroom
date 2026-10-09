from app.domain.car import CarDocument
from app.schemas.search import SearchHit, SearchIntent
from app.search.matcher import CarMatcher
from app.search.ranking.common import build_lexical_signals
from app.search.text import tokenize


class StrictRanker:
    def rank(
        self,
        query: str,
        documents: list[CarDocument],
        intent: SearchIntent,
    ) -> list[SearchHit]:
        if not documents:
            return []

        query_tokens = tokenize(query)
        has_intent = CarMatcher.has_structured_intent(intent)
        if not query_tokens and not has_intent:
            return []

        signals = build_lexical_signals(query, documents)
        ranked_rows: list[tuple[float, CarDocument, list[str]]] = []

        for index, document in enumerate(documents):
            lexical = signals.normalized_scores[index]
            overlap = signals.overlaps[index]
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
