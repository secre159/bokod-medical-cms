#!/bin/bash
set -e

echo "Starting Laravel application..."

# Wait for database with timeout
echo "Waiting for database connection..."
for i in {1..30}; do
    if php -r "try { \$pdo = new PDO(\$_ENV['DATABASE_URL']); echo 'Connected'; } catch(Exception \$e) { exit(1); }" 2>/dev/null; then
        echo "Database connection established!"
        break
    fi
    echo "Attempt $i: Database not ready, waiting..."
    sleep 2
done

# Clear caches first
echo "Clearing caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Generate app key if needed
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:GENERATE_THIS_AFTER_DEPLOYMENT" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Create storage link
echo "Creating storage symlink..."
php artisan storage:link || true

# Set permissions
chmod -R 755 storage bootstrap/cache || true

echo "Laravel application ready!"

# Start Laravel server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
