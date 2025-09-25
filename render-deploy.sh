#!/bin/bash
# Render deployment script for Laravel with cloud storage
# This script will be executed during deployment on Render

set -e

echo "Starting Render deployment..."

# Install dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Clear and cache configuration
echo "Clearing and caching configuration..."
php artisan config:clear
php artisan config:cache

# Clear and cache routes
echo "Caching routes..."
php artisan route:clear
php artisan route:cache

# Clear and cache views
echo "Caching views..."
php artisan view:clear
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Create storage directories if they don't exist (for local fallback)
echo "Creating storage directories..."
mkdir -p storage/app/public/avatars
mkdir -p storage/app/public/attachments
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set proper permissions for storage (if needed)
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Clear all cache
echo "Clearing application cache..."
php artisan cache:clear

# Create symbolic link (may fail on some systems, but that's ok for cloud storage)
echo "Attempting to create storage link..."
php artisan storage:link || echo "Storage link creation failed (this is OK if using cloud storage)"

# Seed database if needed (only for first deployment)
# php artisan db:seed --force

echo "Deployment completed successfully!"
echo ""
echo "IMPORTANT: Set these Cloudinary environment variables in Render:"
echo "- FILESYSTEM_DISK=cloudinary"
echo "- CLOUDINARY_CLOUD_NAME=your-cloud-name"
echo "- CLOUDINARY_API_KEY=your-api-key"
echo "- CLOUDINARY_API_SECRET=your-api-secret"
echo ""
