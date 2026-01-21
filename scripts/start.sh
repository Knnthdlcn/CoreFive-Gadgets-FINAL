#!/usr/bin/env bash
set -e

cd /var/www/html

# Start MySQL (testing)
service mysql start

# If DB_PASSWORD is empty, set a default (avoid errors)
if [ -z "${DB_PASSWORD}" ]; then
  export DB_PASSWORD="root12345"
fi

# Create DB and set root password
mysql -u root <<MYSQL
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${DB_PASSWORD}';
FLUSH PRIVILEGES;
CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\`;
MYSQL

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear & cache config so it reads Render env vars
php artisan config:clear
php artisan cache:clear || true
php artisan config:cache

# Run migrations automatically (no Shell needed)
php artisan migrate --force

# Permissions
chmod -R ug+rw storage bootstrap/cache || true

# Start PHP + Nginx
php-fpm -D
nginx -g "daemon off;"
