#!/bin/bash
echo "🚀 Starting Render build with messaging system fix..."

# Standard build steps
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations first
echo "📊 Running database migrations..."
php artisan migrate --force

# Run the messaging system fix
echo "🔧 Running messaging system database fix..."
php fix-production-messaging-database.php

# Clear caches after fixes
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear

echo "✅ Build completed with messaging system fix!"