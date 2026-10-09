import time
from typing import Literal

from app.repositories.car_repository import CarRepository
from app.schemas.search import SearchRequest, SearchResponse
from app.search.intent_parser import IntentParser
from app.search.matcher import CarMatcher
from app.search.ranking.relaxed import RelaxedRanker
from app.search.ranking.strict import StrictRanker
from app.search.text import normalize_query


class CarSearchEngine:
    def __init__(self, repository: CarRepository) -> None:
        self.repository = repository
        self.intent_parser = IntentParser()
        self.matcher = CarMatcher()
        self.strict_ranker = StrictRanker()
        self.relaxed_ranker = RelaxedRanker()

    def search(self, request: SearchRequest) -> SearchResponse:
        started_at = time.perf_counter()
        documents = self.repository.load_public_cars()
        normalized = normalize_query(request.query)
        intent = self.intent_parser.parse(normalized, documents, request.filters)

        candidates = [
            document
            for document in documents
            if self.matcher.strict_matches(document, intent)
        ]
        ranked = self.strict_ranker.rank(normalized, candidates, intent)
        match_mode: Literal["strict", "relaxed", "none"] = "strict" if ranked else "none"

        if not ranked and self.matcher.has_structured_intent(intent):
            relaxed_documents = self.matcher.identity_candidates(documents, intent)
            ranked = self.relaxed_ranker.rank(normalized, relaxed_documents, intent)
            match_mode = "relaxed" if ranked else "none"

        elapsed = round((time.perf_counter() - started_at) * 1000, 2)
        return SearchResponse(
            query=request.query,
            normalized_query=normalized,
            intent=intent,
            match_mode=match_mode,
            hits=ranked[: request.limit],
            total=len(ranked),
            took_ms=elapsed,
        )
