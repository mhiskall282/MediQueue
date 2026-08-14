# Multi-stage Dockerfile for MediQueue (Laravel 12 + PHP 8.2 + Nginx + Node.js)
FROM node:22-alpine AS frontend-builder

WORKDIR /app
COPY package*.json vite.config.js ./
RUN npm install

COPY resources/ ./resources/
COPY public/ ./public/
RUN npm run build

# Production Stage: PHP 8.2 + Nginx
FROM php:8.2-fpm-alpine

# Install system dependencies & Nginx
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite-dev \
    postgresql-dev \
    oniguruma-dev \
    libzip-dev

# Install PHP extensions (SQLite, PostgreSQL, MySQL supported)
RUN docker-php-ext-install pdo pdo_sqlite pdo_pgsql pdo_mysql mbstring exif pcntl bcmath gd zip

# Get Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application source
COPY . .

# Copy compiled frontend assets from builder stage
COPY --from=frontend-builder /app/public/build ./public/build

# Install PHP dependencies without dev dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set up storage and database permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create SQLite database file if not present
RUN touch /var/www/html/database/database.sqlite \
    && chown www-data:www-data /var/www/html/database/database.sqlite \
    && chmod 664 /var/www/html/database/database.sqlite

# Copy Nginx and Supervisor configurations
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
