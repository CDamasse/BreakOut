#!/bin/bash
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Clear and rebuild caches (useful if config changed)
php artisan config:clear
php artisan config:cache
php artisan route:cache

# Start server
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
