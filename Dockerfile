# ---- Stage 1: build frontend assets (Tailwind + Vite) ----
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

# ---- Stage 2: PHP application ----
FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
        git unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 10000

# config:cache runs at container start (not build time) because env vars
# from Render are only injected at runtime, not during the Docker build.
CMD php artisan config:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
