#!/usr/bin/env bash
set -e
cd /var/www/html

# copy CA cert to a readable place
cp /etc/secrets/aiven-ca.pem /var/www/html/storage/aiven-ca.pem

php artisan config:clear
php artisan cache:clear
php artisan view:clear

chmod 644 /var/www/html/storage/aiven-ca.pem
chmod 644 /var/www/html/storage/aiven-ca.pem

umask 0002

mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Make sure BOTH nginx user and php-fpm user can write

chmod -R 775 storage bootstrap/cache || true
chown -R www-data:www-data storage bootstrap/cache || true


composer install --no-dev --optimize-autoloader

: "${PORT:=10000}"

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-available/default

php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

php artisan config:clear
php artisan cache:clear

php-fpm -D
nginx -t
nginx -g "daemon off;"
exec "$@"
