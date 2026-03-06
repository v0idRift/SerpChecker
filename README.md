# SERP Rank Checker

A small Laravel application for checking a domain's position in Google organic search results by keyword, location, and language.

## Requirements

- PHP 8.2+
- Composer
- SERP API credentials (DataForSEO)

## Run Locally

```bash
git clone https://github.com/v0idRift/SerpChecker.git
cd SerpChecker

composer install

cp .env.example .env
php artisan key:generate
```

Windows note:
- The same flow works on Windows in both `PowerShell` and `Git Bash`.
- In `PowerShell`, use:

```powershell
Copy-Item .env.example .env
```

- In `Git Bash`, `cp .env.example .env` works as-is.

In your `.env` file, set:

- `SERP_API_LOGIN`
- `SERP_API_PASSWORD`
- (optional) `SERP_API_BASE_URL`

Start the development server:

```bash
php artisan serve
```

Open `http://127.0.0.1:8000`.

## Tests

```bash
php artisan test
```
