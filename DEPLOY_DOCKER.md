# Docker deployment (production)

This file describes how to build and run the project using Docker + Docker Compose.

1) Prepare environment

- Copy or create your production environment file:

```
cp .env.example .env
```

- Edit `.env` and set database variables to match the `db` service (or your external DB):

- Required changes (example):

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
APP_ENV=production
APP_DEBUG=false
```

2) Build and run

Build the image and run containers:

```bash
docker compose up --build -d
```

3) Post-deploy tasks

Run these inside the `app` container:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan storage:link || true
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

4) Notes

- The `docker-compose.yml` provided is a production-style compose that builds the app image from the root `Dockerfile` and runs a MySQL 8 container.
- For CI/CD or pushing to a registry, build and push `corefive-gadgets-app:latest` to your registry and update deployment tooling to pull the image.
- If you prefer persistent local development with file mounts, I can add a `docker-compose.dev.yml` that mounts the source directory.
