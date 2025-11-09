#!/bin/bash
# docker-entrypoint.sh

# Generate APP_KEY if not set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run database migrations and seeders
php artisan migrate --force --seed

# Start Laravel built-in server on 0.0.0.0:10000
php artisan serve --host=0.0.0.0 --port=10000
