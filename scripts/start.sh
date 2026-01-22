#!/usr/bin/env bash
set -e

cd /var/www/html

# --- Make folders first ---
umask 0002
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

# Permissions
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# --- Aiven CA cert (DON'T crash if missing) ---
if [ -f /etc/secrets/aiven-ca.pem ]; then
  cp /etc/secrets/aiven-ca.pem /var/www/html/storage/aiven-ca.pem
  chmod 644 /var/www/html/storage/aiven-ca.pem
  chown www-data:www-data /var/www/html/storage/aiven-ca.pem || true
fi

# --- Laravel caches (safe) ---
php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache  || true
php artisan view:cache   || true

# --- Run migrations (safe-ish) ---
php artisan migrate --force || true

# --- Nginx config from template ---
: "${PORT:=10000}"
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default


# --- Start services ---
php-fpm -D
nginx -t
nginx -g "daemon off;"

