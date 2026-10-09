from app.domain.car import CarDocument
from app.schemas.search import SearchHit, SearchIntent
from app.search.ranking.common import build_lexical_signals


class RelaxedRanker:
    def rank(
        self,
        query: str,
        documents: list[CarDocument],
        intent: SearchIntent,
    ) -> list[SearchHit]:
        if not documents:
            return []

        signals = build_lexical_signals(query, documents)
        ranked_rows: list[tuple[float, CarDocument, list[str], list[str]]] = []

        for index, document in enumerate(documents):
            score, reasons, relaxed_constraints = self._structured_score(document, intent)
            score += signals.normalized_scores[index] * 2.0

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

    def _structured_score(
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
