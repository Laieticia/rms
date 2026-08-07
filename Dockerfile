# Étape 1: Builder stage
FROM php:8.2-fpm-bullseye AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    git \
    zip \
    unzip \
    libzip-dev \
    libonig-dev \
    libssl-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd \
    intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-gd

# Étape 2: Runtime stage
FROM php:8.2-fpm-bullseye

# nginx, supervisor (process manager) et gettext-base (envsubst, pour injecter
# le $PORT de Render dans le vhost nginx au démarrage)
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    wget \
    nginx \
    supervisor \
    gettext-base \
    net-tools \
    libzip4 \
    libonig5 \
    libpng16-16 \
    libjpeg62-turbo \
    libfreetype6 \
    libwebp6 \
    libicu67 \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP depuis le builder
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/

# Configuration PHP / PHP-FPM
COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# Configuration Nginx (le vhost est un template rendu par entrypoint.sh au
# démarrage, car Render impose un $PORT dynamique)
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf.template /etc/nginx/templates/default.conf.template
RUN mkdir -p /etc/nginx/sites-enabled

# Supervisord (gère php-fpm + nginx + queue worker optionnel)
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script de démarrage
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /app

COPY --from=builder /app /app

RUN mkdir -p storage/logs storage/app/uploads bootstrap/cache && \
    mkdir -p /var/log/php /var/run/php && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /app /var/log/php /var/run/php

RUN mkdir -p /var/log/nginx /var/run/nginx /var/lib/nginx/tmp && \
    chown -R www-data:www-data /var/log/nginx /var/run/nginx /var/lib/nginx

# Documentation uniquement: Render fournit $PORT dynamiquement (10000 par défaut),
# le conteneur n'écoute jamais réellement sur 80 sur Render.
EXPOSE 80

HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=5 \
    CMD sh -c "curl -f http://127.0.0.1:${PORT:-80}/health || exit 1"

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]