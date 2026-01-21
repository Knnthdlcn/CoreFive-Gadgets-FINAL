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

CMD ["/start.sh"]
