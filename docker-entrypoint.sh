#!/bin/bash
# docker-entrypoint.sh

# Generate APP_KEY if not set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Start Laravel built-in server on 0.0.0.0:10000
php artisan serve --host=0.0.0.0 --port=10000

