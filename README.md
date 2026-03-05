# SERP Rank Checker

Невеликий застосунок на Laravel для перевірки позиції домену в органічній видачі Google за ключовим словом, локацією та мовою.

## Вимоги

- PHP 8.2+
- Composer
- Облікові дані SERP API (DataForSEO)

## Запуск локально

```bash
composer install

cp .env.example .env
php artisan key:generate
```

У файлі `.env` заповніть змінні:

- `SERP_API_LOGIN`
- `SERP_API_PASSWORD`
- (опційно) `SERP_API_BASE_URL`

Запустіть сервер:

```bash
php artisan serve
```

Відкрийте `http://127.0.0.1:8000`.

## Тести

```bash
php artisan test
```
