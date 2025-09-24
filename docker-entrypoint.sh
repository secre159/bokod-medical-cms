#!/bin/bash
set -e

echo "Starting Laravel application deployment..."

# Wait for database to be ready
echo "Waiting for database connection..."
until php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; do
    echo "Database not ready, waiting..."
    sleep 2
done

echo "Database connection established!"

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Generate app key if not exists
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:GENERATE_THIS_AFTER_DEPLOYMENT" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Cache configuration for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
echo "Creating storage symlink..."
php artisan storage:link

# Set permissions
chmod -R 755 storage bootstrap/cache

echo "Laravel application ready!"

# Start Laravel development server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}