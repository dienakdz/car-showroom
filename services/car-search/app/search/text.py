import re
import unicodedata

from app.search.vocabulary import ALIASES, STOP_WORDS


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
