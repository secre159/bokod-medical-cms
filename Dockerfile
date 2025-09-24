FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy rest of application files
COPY . .

# Generate app key if needed
RUN if [ ! -f .env ]; then cp .env.example .env; fi
RUN php artisan key:generate --no-interaction || true

# Run migrations (will happen at runtime when DB is available)
# Create storage link
RUN php artisan storage:link || true

# Set permissions
RUN chmod -R 755 storage bootstrap/cache

# Expose port
EXPOSE $PORT

# Create startup script
RUN echo '#!/bin/bash\n\
echo "Running database migrations..."\n\
php artisan migrate --force\n\
echo "Starting Laravel server..."\n\
php artisan serve --host=0.0.0.0 --port=$PORT' > /start.sh && chmod +x /start.sh

# Start command
CMD ["/start.sh"]
