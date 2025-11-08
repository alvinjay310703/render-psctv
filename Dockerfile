# Stage 1 - Build Frontend (Vite)
FROM node:18 AS frontend
WORKDIR /app

# Copy package files and install Node dependencies
COPY package*.json ./
RUN npm install

# Copy all source files and build frontend with correct APP_URL
COPY . .
ENV APP_URL=https://render-psctv.onrender.com
RUN npm run build

# Stage 2 - Backend (Laravel + PHP + Composer)
FROM php:8.2-fpm AS backend

# Install system dependencies for Laravel & Postgres
RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy Laravel app files
COPY . .

# Copy built frontend from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Fix permissions for Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public/build
RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache /var/www/public/build

# Make entrypoint script executable
RUN chmod +x /var/www/docker-entrypoint.sh

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Clear caches
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

# Use custom entrypoint to generate key, run migrations, and start built-in server
CMD ["./docker-entrypoint.sh"]
