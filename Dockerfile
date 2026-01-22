FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx git unzip libzip-dev gettext-base \
  && docker-php-ext-install pdo pdo_mysql zip \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Copy nginx template
COPY conf/nginx/site.conf.template /etc/nginx/templates/default.conf.template
COPY start.sh /start.sh
# Start script
COPY scripts/start.sh /start.sh
RUN chmod +x /start.sh

RUN sed -i 's|listen = 9000|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/zz-docker.conf
# Ensure Laravel can write cache/logs
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
# Ensure Laravel writable folders exist + permissions are correct
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

RUN chmod +x /start.sh
CMD ["/start.sh", "supervisord", "-c", "/etc/supervisord.conf"]

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
COPY composer.json ./
COPY scripts/start.sh /start.sh
RUN chmod +x /start.sh




# Build -> generates public/build/manifest.json
RUN npm run build


# =========================
# 2) PHP + Nginx runtime
# =========================
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx git unzip libzip-dev gettext-base \
    && docker-php-ext-install pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

# ✅ Copy built Vite assets into final image
COPY --from=assets /app/public/build /var/www/html/public/build

# Copy nginx template + start script
COPY conf/nginx/site.conf.template /etc/nginx/templates/default.conf.template
COPY scripts/start.sh /start.sh
RUN chmod +x /start.sh

RUN sed -i 's|listen = 9000|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/zz-docker.conf

# Ensure Laravel can write cache/logs
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx git unzip libzip-dev gettext-base \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 1) Copy composer files first (better caching)
COPY composer.json composer.lock ./

# 2) Install PHP deps -> creates /var/www/html/vendor
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# 3) Now copy the rest of the app
COPY . .

# Copy nginx template + start script
COPY conf/nginx/site.conf.template /etc/nginx/templates/default.conf.template
COPY start.sh /start.sh
RUN chmod +x /start.sh

RUN sed -i 's|listen = 9000|listen = 127.0.0.1:9000|g' /usr/local/etc/php-fpm.d/zz-docker.conf

RUN chown -R www-data:www-data storage bootstrap/cache \
 && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
 && chmod -R 775 storage bootstrap/cache


CMD ["/start.sh", "supervisord", "-c", "/etc/supervisord.conf"]


