# Build PHP dependencies with Composer
FROM composer:2.8 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --classmap-authoritative --ignore-platform-reqs --no-scripts --no-interaction

# Build frontend assets with Node
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json ./
RUN npm install
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# Final runtime image
FROM php:8.4-fpm-alpine
RUN apk add --no-cache libzip-dev zip unzip oniguruma-dev libxml2-dev sqlite-dev \
    && docker-php-ext-install pdo pdo_sqlite mbstring xml
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY . .
RUN mkdir -p bootstrap/cache \
    && rm -rf bootstrap/cache/* || true \
    && php artisan package:discover --ansi --no-interaction \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
EXPOSE 9000
CMD ["php-fpm"]
