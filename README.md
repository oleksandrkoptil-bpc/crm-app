# CRM

CRM service built on Laravel 12 with Docker, Nginx, MySQL and Redis.

## Stack

- Laravel 12
- PHP 8.4 (FPM)
- Nginx
- MySQL 8.4
- Redis 7.4
- Swagger (`l5-swagger`)
- Spatie Media Library
- Spatie Permission

## Requirements

- Docker
- Docker Compose

## Project services

Docker starts these containers:

- `crm-app` - PHP application container
- `crm-nginx` - web server
- `crm-mysql` - MySQL
- `crm-redis` - Redis

Default forwarded ports:

- App: `8080`
- MySQL: `3306`
- Redis: `6379`

You can change them in `.env`:

```dotenv
APP_PORT=8080
DB_FORWARD_PORT=3306
REDIS_FORWARD_PORT=6379
```

## First run

1. Copy environment file:

```bash
cp .env.example .env
```

2. Set your API token in `.env`:

```dotenv
API_TOKEN=change-me
```

3. Build and start containers:

```bash
docker compose up -d --build
```

4. Install PHP dependencies:

```bash
docker compose exec app composer install
```

5. Generate application key:

```bash
docker compose exec app php artisan key:generate
```

6. Run migrations and seed demo data:

```bash
docker compose exec app php artisan migrate --seed
```

7. Generate Swagger documentation:

```bash
docker compose exec app php artisan l5-swagger:generate
```

After that the app is available at:

```text
http://localhost:8080
```

## Demo access

Manager account:

```text
Email: manager@crm.test
Password: password
```

Admin account:

```text
Email: admin@crm.test
Password: password
```

## Main routes

Web:

- `/login` - login page
- `/manager/tickets` - manager panel
- `/widget` - embeddable customer widget

Swagger:

- `/api/documentation` - Swagger UI
- `/docs/api-docs.json` - generated OpenAPI JSON

API:

- `POST /api/tickets`
- `GET /api/tickets/statistics`

## API authentication

API requests use Bearer token from `.env`:

```http
Authorization: Bearer your-token
Accept: application/json
```

Example:

```bash
curl -X POST http://localhost:8080/api/tickets \
  -H "Authorization: Bearer change-me" \
  -H "Accept: application/json" \
  -F "customer[name]=John Smith" \
  -F "customer[phone]=+380501112233" \
  -F "customer[email]=john@example.test" \
  -F "subject=Payment issue" \
  -F "message=Customer cannot complete payment."
```

## Daily commands

Start containers:

```bash
docker compose up -d
```

Stop containers:

```bash
docker compose down
```

Stop containers and remove volumes:

```bash
docker compose down -v
```

Rebuild containers:

```bash
docker compose up -d --build
```

Install a Laravel package:

```bash
docker compose exec app composer require vendor/package
```

Install Composer dependencies:

```bash
docker compose exec app composer install
```

Run migrations:

```bash
docker compose exec app php artisan migrate
```

Run fresh migrations with seeders:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Run seeders only:

```bash
docker compose exec app php artisan db:seed
```

Run tests:

```bash
docker compose exec app php artisan test
```

Run a specific test file:

```bash
docker compose exec app php artisan test --filter=ApiTicketsTest
```

Clear Laravel caches:

```bash
docker compose exec app php artisan optimize:clear
```

Generate Swagger docs:

```bash
docker compose exec app php artisan l5-swagger:generate
```

Check registered routes:

```bash
docker compose exec app php artisan route:list
```

Open Laravel Tinker:

```bash
docker compose exec app php artisan tinker
```

Check container logs:

```bash
docker compose logs -f
```

Check app logs only:

```bash
docker compose logs -f app
```

## Useful reset commands

Reset database and demo data:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Rebuild project from scratch:

```bash
docker compose down -v
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan l5-swagger:generate
```

## Notes

- Root `/` redirects guests to `/login`.
- Redis is used as the default cache store.
- Ticket statistics endpoint uses Redis cache.
- Ticket creation is limited to one submission per day for the same phone number or email.
- Swagger JSON is generated into `storage/api-docs` and is not committed to the repository.
