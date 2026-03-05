# Google Organic SERP Rank Checker (Laravel 12)

Small Laravel application to check a website position in Google organic results for a given keyword, location, and language.

## Tech

- PHP 8.2+
- Laravel 12
- Blade

## Requirements

- PHP + Composer
- SERP API credentials (see `.env.example`)

## Local Run

```bash
composer install

cp .env.example .env
# configure required env vars

php artisan key:generate
php artisan serve
```

Open `http://127.0.0.1:8000`.

## Tests

```bash
php artisan test
```
