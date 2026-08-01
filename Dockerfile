# syntax=docker/dockerfile:1

FROM php:8.3-apache-bookworm AS php_dependencies

ENV APP_ENV=production \
    APP_DEBUG=false \
    APACHE_DOCUMENT_ROOT=/var/www/html/public \
    COMPOSER_ALLOW_SUPERUSER=1 \
    LOG_CHANNEL=stderr \
    PORT=10000

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        dom \
        gd \
        intl \
        pdo_mysql \
        pdo_pgsql \
        zip \
    && a2enmod expires headers rewrite \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php.ini /usr/local/etc/php/conf.d/99-app.ini

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
COPY --from=php_dependencies /var/www/html/vendor ./vendor
RUN npm run build

FROM php_dependencies AS app

COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer dump-autoload --no-dev --classmap-authoritative \
    && mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x docker/entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["apache2-foreground"]
