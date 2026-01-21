#!/usr/bin/env bash
set -e
cd /var/www/html

composer install --no-dev --optimize-autoloader

php artisan config:clear
php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true

chmod -R ug+rw storage bootstrap/cache || true

php-fpm -D
nginx -g "daemon off;"
