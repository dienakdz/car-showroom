from dataclasses import dataclass

from rank_bm25 import BM25Okapi

from app.domain.car import CarDocument
from app.search.text import tokenize


@dataclass(frozen=True, slots=True)
class LexicalSignals:
    normalized_scores: list[float]
    overlaps: list[float]


def build_lexical_signals(query: str, documents: list[CarDocument]) -> LexicalSignals:
    query_tokens = tokenize(query)
    corpus = [tokenize(document_text(document)) for document in documents]
    lexical_scores = BM25Okapi(corpus).get_scores(query_tokens) if query_tokens else [0.0] * len(documents)
    positive_scores = [max(0.0, float(score)) for score in lexical_scores]
    max_lexical = max(positive_scores, default=0.0)
    query_token_set = set(query_tokens)

    normalized_scores = [
        score / max_lexical if max_lexical > 0 else 0.0
        for score in positive_scores
    ]
    overlaps = [
        len(query_token_set.intersection(tokens)) / len(query_token_set)
        if query_token_set
        else 0.0
        for tokens in corpus
    ]

    return LexicalSignals(
        normalized_scores=normalized_scores,
        overlaps=overlaps,
    )


def document_text(document: CarDocument) -> str:
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
