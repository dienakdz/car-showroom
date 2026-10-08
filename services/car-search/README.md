# Car Search Service

Small FastAPI service for the current showroom inventory. It reads public car
data from MySQL for each search request, parses structured Vietnamese search
intent, applies exact/fuzzy matching, and ranks the remaining candidates with
BM25.

This first phase intentionally has no local language model, embeddings, vector
database, persistent search index, queue synchronization, or Laravel client.

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
