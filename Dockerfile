# =========================
# 1) Frontend build (Vite)
# =========================
FROM node:20-alpine AS assets
WORKDIR /app

# Install node deps
COPY package*.json ./
RUN npm ci

# Copy only what Vite needs
COPY vite.config.* ./
COPY resources ./resources
COPY public ./public

# Build -> outputs public/build/manifest.json
RUN npm run build


# =========================
# 2) PHP + Nginx runtime
# =========================
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx unzip libzip-dev gettext-base supervisor \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 1) Install PHP deps first (better caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# 2) Copy the rest of the Laravel app
COPY . .

# 3) Copy built Vite assets into final image
COPY --from=assets /app/public/build /var/www/html/public/build

# Nginx template
COPY conf/nginx/site.conf.template /etc/nginx/templates/default.conf.template

# Start script (THIS is the file Render was failing to find)
COPY scripts/start.sh /start.sh
RUN chmod +x /start.sh

# PHP-FPM listen fix
RUN sed -i 's|listen = 9000|listen = 127.0.0.1:9000|g' /usr/local/etc/php-fpm.d/zz-docker.conf

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
 && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
 && chmod -R 775 storage bootstrap/cache

CMD ["/start.sh", "supervisord", "-c", "/etc/supervisord.conf"]
