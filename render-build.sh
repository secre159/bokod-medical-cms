#!/usr/bin/env bash
# Render deployment script for Laravel

echo "Starting Laravel deployment on Render..."

# Set build environment
export NODE_VERSION=18
export PHP_VERSION=8.2

# Install dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies (if you have frontend assets)
if [ -f "package.json" ]; then
    echo "Installing Node.js dependencies..."
    npm ci
    echo "Building frontend assets..."
    npm run build
fi

# Clear and optimize Laravel
echo "Optimizing Laravel application..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Generate optimized files for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Create symbolic link for storage
echo "Creating storage symlink..."
php artisan storage:link

# Set proper permissions
echo "Setting file permissions..."
chmod -R 755 storage bootstrap/cache

echo "Laravel deployment completed successfully!"