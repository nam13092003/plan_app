#!/bin/bash
set -e

# Wait for database
echo "Waiting for database connection..."
until php artisan migrate:status > /dev/null 2>&1; do
    echo "Database is unavailable - sleeping"
    sleep 2
done

echo "Database is up - executing commands"

# Run migrations
php artisan migrate --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start server
echo "Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8000

