FROM node:24-bookworm-slim AS assets

WORKDIR /app
COPY . .
RUN npm install && npm run build

FROM php:8.3-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --no-dev --optimize

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
