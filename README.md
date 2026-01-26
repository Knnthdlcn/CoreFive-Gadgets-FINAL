# CoreFive Gadgets (Laravel)

## Requirements

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL (XAMPP is OK)

## Local Setup (Windows / XAMPP)

1. Clone the repo and enter the folder
2. Install backend dependencies:

```bash
composer install
```

3. Create `.env`:

```bash
copy .env.example .env
php artisan key:generate
```

4. Create the MySQL database `eshop` (phpMyAdmin or MySQL CLI)
5. Run migrations + seeders:

```bash
php artisan migrate
php artisan db:seed
```

6. Install/build frontend assets:

```bash
npm install
npm run dev
```

7. Start the app:

```bash
php artisan serve
```

## Admin Separation (Option 1)

This project supports a separated admin entrypoint using an admin-only host.

- Set `ADMIN_HOST` in `.env` (example: `admin.yourshop.com`)
- Admin routes will only be accessible when the request host matches `ADMIN_HOST`.

For local subdomain testing with XAMPP VirtualHosts, see:
- LOCAL_ADMIN_HOST_TESTING.md

## Deployment Notes (high level)

- Set web server DocumentRoot to the `public/` directory.
- Use `APP_ENV=production`, `APP_DEBUG=false`.
- Run:
  - `composer install --no-dev --optimize-autoloader`
  - `npm ci && npm run build`
  - `php artisan migrate --force`
  - `php artisan config:cache`
  - `php artisan route:cache`

## Security Basics (recommended)

- Keep admin on `admin.*` and set `ADMIN_HOST`.
- Add an additional gate for admin (IP allowlist / Basic Auth / Cloudflare Access).
- Use HTTPS for both storefront and admin.

## Running Tests (safe)

This project is configured so tests can use a dedicated MySQL database via a local `.env.testing` (kept out of git).

1. Create a test database (recommended: `eshop_test`):

```bash
php scripts/create_test_database.php eshop_test
```

2. Create `.env.testing`:

```bash
copy .env.testing.example .env.testing
```

3. Edit `.env.testing` and set `DB_PASSWORD` (and any other DB values if your MySQL differs).

4. Run tests:

```bash[CoreFive-Gadgets-User-Manual.pdf](https://github.com/user-attachments/files/24859978/CoreFive-Gadgets-User-Manual.pdf)

php artisan test
```



