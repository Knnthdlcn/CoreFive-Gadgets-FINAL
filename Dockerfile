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

CMD ["/start.sh"]
