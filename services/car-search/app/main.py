from fastapi import FastAPI

from app.api.routes.health import router as health_router
from app.api.routes.search import router as search_router


app = FastAPI(
    title="Car Search Service",
    version="1.0.0",
    description="Structured Vietnamese inventory search with fuzzy matching and BM25 ranking.",
)

app.include_router(health_router)
app.include_router(search_router)
