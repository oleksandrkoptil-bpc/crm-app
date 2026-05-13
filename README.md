# CRM

Clean Laravel 12 application prepared for local development with Docker.

## Stack

- Laravel 12
- PHP 8.4 FPM
- Nginx
- MySQL 8.4
- Redis 7.4

## First Run

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

Application: http://localhost:8080

## Daily Commands

```bash
docker compose up -d
docker compose exec app php artisan migrate
docker compose exec app composer require vendor/package
docker compose exec app php artisan test
docker compose down
```

## Services

- App container: `crm-app`
- Web container: `crm-nginx`
- Database container: `crm-mysql`
- Redis container: `crm-redis`
- MySQL host from Laravel: `mysql`
- Redis host from Laravel: `redis`

Local forwarded ports can be changed in `.env`:

```dotenv
APP_PORT=8080
DB_FORWARD_PORT=3306
REDIS_FORWARD_PORT=6379
```
