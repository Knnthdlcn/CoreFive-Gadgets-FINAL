FROM php:8.4-fpm


RUN apt-get update && apt-get install -y \
    nginx git unzip libzip-dev \
  && docker-php-ext-install pdo pdo_mysql zip \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

COPY conf/nginx/site.conf /etc/nginx/sites-available/default
COPY scripts/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000
CMD ["/start.sh"]
