# Car Showroom

Car Showroom is a Docker-based showroom application composed of a Laravel web
application and a Python inventory search service. Laravel owns the public and
admin experiences, business workflows, and rendered vehicle data. The FastAPI
service parses Vietnamese search text and ranks matching or closest available
cars without using an LLM, embedding model, or vector database.

## Repository structure

```text
car-showroom/
├── apps/
│   ├── laravel/          # Laravel, Blade, Livewire, and frontend assets
│   └── car-search/       # FastAPI text parsing and inventory ranking
├── infra/
│   └── docker/           # PHP-FPM and Nginx configuration
├── docs/                 # Architecture, standards, commands, and specifications
├── .github/              # CI workflows
├── .husky/               # Git hooks
├── AGENTS.md              # Repository implementation instructions
└── docker-compose.yml     # Local application stack
```

## Local development

Start the full stack from the repository root:

```bash
docker compose up -d --build
```

Local endpoints:

- Laravel showroom: <http://localhost:8889>
- Car search health check: <http://localhost:8001/health>
- MySQL host port: `3307`

The Laravel container reaches the Python service through the internal Docker
URL `http://car-search:8000`; that hostname is not intended for the host browser.

Stop the stack without deleting named volumes:

```bash
docker compose down
```

## Quality checks

Run Laravel checks from `apps/laravel/` on a configured host, or through the
application container:

```bash
docker compose exec app composer lint
docker compose exec app composer stan
```

Run a Python syntax check through its container:

```bash
docker compose exec car-search python -m compileall -q app
```

## Documentation

- [Application architecture](docs/application-architecture.md)
- [Coding standards](docs/coding-standards.md)
- [Development commands](docs/commands.md)
- [Car search service](apps/car-search/README.md)
- [Project diagrams](docs/diagrams/)
- [Design specifications](docs/specifications/)
