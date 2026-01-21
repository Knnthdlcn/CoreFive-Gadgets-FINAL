#!/usr/bin/env bash
set -e

cd /var/www/html

composer install --no-dev --optimize-autoloader

# Render provides PORT
: "${PORT:=10000}"

# Generate nginx config with correct PORT
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-available/default

# Laravel cache (don’t break deploy if views missing)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

chmod -R ug+rw storage bootstrap/cache || true

php-fpm -D

# Test nginx config (very helpful)
nginx -t

# Start nginx (this is what opens the HTTP port)
nginx -g "daemon off;"
