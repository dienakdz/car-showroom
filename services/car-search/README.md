# Car Search Service

Small FastAPI service for the current showroom inventory. It reads public car
data from MySQL for each search request, parses structured Vietnamese search
intent, applies exact/fuzzy matching, and ranks the remaining candidates with
BM25.

When no car satisfies every structured condition, the service keeps the most
specific detected identity (trim, model, or make) and returns the closest
available cars. The response uses `match_mode: "relaxed"` and lists unmet
conditions in each hit's `relaxed_constraints` field.

The service intentionally has no local language model, embeddings, vector
database, persistent search index, or queue synchronization.

## Project structure

```text
app/
├── main.py                 # FastAPI application assembly
├── api/
│   ├── dependencies.py     # Dependency wiring and internal-token guard
│   └── routes/             # Health and search HTTP endpoints
├── core/                   # Environment-backed configuration
├── domain/                 # Internal car document model
├── repositories/           # Read-only MySQL data access
├── schemas/                # Pydantic API contracts
└── search/
    ├── engine.py           # Search use-case orchestration
    ├── intent_parser.py    # Entity, phrase, and numeric intent parsing
    ├── matcher.py          # Strict filters and relaxed identity candidates
    ├── text.py             # Vietnamese normalization and tokenization
    ├── vocabulary.py       # Aliases, stop words, and controlled terms
    └── ranking/
        ├── common.py       # Shared BM25 signals and document text
        ├── strict.py       # Exact-result ranking
        └── relaxed.py      # Closest-result scoring and explanations
```

Dependencies flow inward from API routes to the search engine, repository,
schemas, and domain model. HTTP handling, database access, parsing, matching,
and ranking stay in separate modules so future recommendation strategies do not
need to expand one shared file.

## Endpoints

- `GET /health`: checks that the service and MySQL connection are available.
- `POST /search`: returns parsed intent and ranked `car_unit_id` values.
- `GET /docs`: FastAPI interactive API documentation.

The development compose file binds the service to `http://localhost:8001`, so
the interactive documentation is available at `http://localhost:8001/docs`.

`POST /search` requires the `X-Internal-Token` header when
`CAR_SEARCH_API_TOKEN` is configured.

Example request:

```json
{
  "query": "Toyota 7 chỗ cũ dưới 1 tỷ",
  "limit": 20
}
```

The service performs `SELECT` statements only. For production, configure
`SEARCH_DB_USERNAME` with a database account that has read-only access to the
required showroom tables.
