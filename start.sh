#!/bin/bash
set -e

echo "=== STARTING LARAVEL DEPLOYMENT ==="
echo "Working directory: $(pwd)"
echo "Database URL: ${DATABASE_URL:0:20}..."
echo "Port: $PORT"

# Wait for database connection
echo "Waiting for database connection..."
for i in {1..30}; do
    if php artisan migrate:status >/dev/null 2>&1; then
        echo "Database connected successfully!"
        break
    fi
    echo "Attempt $i: Database not ready, waiting 3 seconds..."
    sleep 3
done

# Force run all migrations
echo "=== RUNNING MIGRATIONS ==="
php artisan migrate --force --verbose
echo "=== MIGRATION STATUS ==="
php artisan migrate:status

# Clear all caches
echo "=== CLEARING CACHES ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "=== STARTING LARAVEL SERVER ==="
exec php artisan serve --host=0.0.0.0 --port=$PORT