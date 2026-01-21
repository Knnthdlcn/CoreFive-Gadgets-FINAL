#!/usr/bin/env bash
set -e
cd /var/www/html

# Create Laravel required cache directories
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true


composer install --no-dev --optimize-autoloader

: "${PORT:=10000}"

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-available/default

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

php-fpm -D
nginx -t
nginx -g "daemon off;"
