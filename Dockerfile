# syntax=docker/dockerfile:1.7

ARG PHP_VERSION=8.5

# Install PHP dependencies without development packages.
FROM php:${PHP_VERSION}-cli-bookworm AS vendor
WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libgd-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libzip-dev \
        libxml2-dev \
        libgmp-dev \
        libpq-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath curl exif gd intl mbstring pdo_mysql pdo_pgsql pcntl sockets zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY lang ./lang
COPY modules ./modules
COPY routes ./routes
COPY themes ./themes
COPY artisan ./artisan

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-ansi \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# Build Vite assets after vendor is available because the Filament theme imports
# its base stylesheet from vendor/filament/filament/resources/css/theme.css.
FROM node:22-alpine AS frontend
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY --from=vendor /app/vendor ./vendor
COPY app ./app
COPY modules ./modules
COPY themes ./themes
COPY resources ./resources
COPY public ./public
COPY vite.config.js tailwind.config.js postcss.config.cjs ./
RUN npm run build

# Production runtime: Apache serves only Laravel's public directory.
FROM php:${PHP_VERSION}-apache-bookworm AS runtime
WORKDIR /var/www/html

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=warning \
    COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libgd-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libzip-dev \
        libxml2-dev \
        libgmp-dev \
        libpq-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath curl exif gd intl mbstring pdo_mysql pdo_pgsql pcntl sockets zip \
    && for mpm in mpm_event mpm_worker mpm_prefork; do a2dismod "$mpm" >/dev/null 2>&1 || true; done \
    && a2enmod mpm_prefork rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=frontend /app/public/build ./public/build

COPY railway/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY railway/php-production.ini ${PHP_INI_DIR}/conf.d/99-orcatech-production.ini
COPY railway/start.sh /usr/local/bin/railway-start

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /usr/local/bin/railway-start \
    && APP_ENV=production APP_DEBUG=false APP_KEY= php artisan package:discover --ansi \
    && rm -f .env

# Railway injects PORT; railway/start.sh configures Apache at runtime.

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl --fail --silent "http://127.0.0.1:${PORT:-8080}/health/live" || exit 1

CMD ["/usr/local/bin/railway-start"]
