#!/usr/bin/env bash
# Render start script for Laravel

echo "Starting Laravel application..."

# Start PHP-FPM and Nginx
exec docker-php-entrypoint php-fpm