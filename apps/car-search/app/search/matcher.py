from app.domain.car import CarDocument
from app.schemas.search import SearchIntent


class CarMatcher:
    @staticmethod
    def has_structured_intent(intent: SearchIntent) -> bool:
        return any(value is not None for value in intent.model_dump().values())

    @staticmethod
    def strict_matches(document: CarDocument, intent: SearchIntent) -> bool:
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

    @staticmethod
    def identity_candidates(
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
