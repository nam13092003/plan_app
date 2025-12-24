#!/bin/bash
set -e

# Wait for database to be ready
echo "Waiting for database..."
until php -r "
try {
    \$pdo = new PDO(
        'mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306}',
        '${DB_USERNAME:-laravel}',
        '${DB_PASSWORD:-password}'
    );
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Database is ready\n';
    exit(0);
} catch (PDOException \$e) {
    exit(1);
}
" 2>/dev/null; do
    echo "Database is unavailable - sleeping"
    sleep 2
done

# Run migrations (chỉ khi có biến RUN_MIGRATIONS=true)
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force || true
fi

# Clear and cache config (chỉ khi không phải development)
if [ "${APP_ENV:-production}" != "local" ]; then
    echo "Optimizing application..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Start PHP-FPM
exec php-fpm

