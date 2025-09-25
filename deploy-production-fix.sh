#!/bin/bash

# Production deployment script to fix database issues
# This script should be run on the production server

echo "🔧 Starting production database fix..."

# Make sure we're in the right directory
cd /var/www/html || {
    echo "❌ Could not change to /var/www/html directory"
    exit 1
}

# Clear caches first
echo "🧹 Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run pending migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# If migrations fail, try the emergency fix command
if [ $? -ne 0 ]; then
    echo "⚠️  Migrations failed, trying emergency database fix..."
    
    # Try to run the emergency fix migration specifically
    php artisan migrate:refresh --path=/database/migrations/2025_09_25_030800_emergency_fix_users_table.php --force
    
    if [ $? -ne 0 ]; then
        echo "❌ Emergency migration failed. Trying manual fix command..."
        php artisan db:fix-users-table
    fi
fi

# Optimize application
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "🔐 Setting file permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

echo "✅ Production fix completed!"

# Test if the application is working
echo "🧪 Testing application..."
php artisan tinker --execute="echo 'Laravel is working: ' . (DB::connection()->getPDO() ? 'Database OK' : 'Database Error');"

if [ $? -eq 0 ]; then
    echo "✅ Application test passed!"
else
    echo "❌ Application test failed - please check logs"
    exit 1
fi

echo "🎉 Production deployment fix completed successfully!"