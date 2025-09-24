FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Make build script executable and run it
RUN chmod +x render-build.sh && ./render-build.sh

# Expose port
EXPOSE $PORT

# Start command
CMD php artisan serve --host=0.0.0.0 --port=$PORT