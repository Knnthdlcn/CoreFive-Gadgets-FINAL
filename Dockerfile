# =========================
# 1) Frontend build (Vite)
# =========================
FROM node:20-alpine AS assets
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY vite.config.* ./
COPY resources ./resources
COPY public ./public

RUN npm run build


# =========================
# 2) PHP dependencies (vendor)
# =========================
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./

# IMPORTANT: do NOT run Laravel scripts here (artisan isn't copied yet)
RUN composer install \
  --no-dev \
  --prefer-dist \
  --no-interaction \
  --optimize-autoloader \
  --no-scripts


# =========================
# 3) Final runtime: PHP + Nginx
# =========================
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx supervisor gettext-base unzip git libzip-dev \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy app source (includes artisan)
COPY . .

# Copy vendor from composer stage
COPY --from=vendor /app/vendor ./vendor

# Copy built Vite assets
COPY --from=assets /app/public/build ./public/build

# Nginx template + start script
COPY conf/nginx/site.conf.template /etc/nginx/templates/default.conf.template
COPY scripts/start.sh /start.sh
RUN chmod +x /start.sh

# PHP-FPM listen fix
RUN sed -i 's|listen = 9000|listen = 127.0.0.1:9000|g' /usr/local/etc/php-fpm.d/zz-docker.conf

# Permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache public/images \
 && chown -R www-data:www-data storage bootstrap/cache public/images \
 && chmod -R 775 storage bootstrap/cache public/images

CMD ["/start.sh"]
