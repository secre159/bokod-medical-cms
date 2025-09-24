FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    # Basic tools
    git \
    zip \
    unzip \
    curl \
    wget \
    # Database drivers
    libpq-dev \
    # Image processing (for intervention/image)
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libgd-dev \
    libwebp-dev \
    # XML/DOM processing (for dompdf)
    libxml2-dev \
    # Zip support
    libzip-dev \
    # Other common extensions
    libonig-dev \
    libicu-dev \
    # Cleanup
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        zip \
        gd \
        xml \
        dom \
        mbstring \
        intl \
        bcmath \
        exif \
        pcntl

# Set composer environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Remove any .env files to force environment variable usage
RUN rm -f .env .env.example

# Generate app key if needed (only if APP_KEY not set)
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
set -e\n\
echo "Starting Laravel application..."\n\
\n\
# Wait for database connection\n\
echo "Waiting for database connection..."\n\
for i in {1..30}; do\n\
    if php artisan tinker --execute="DB::connection()->getPdo(); echo \"Connected\";" 2>/dev/null; then\n\
        echo "Database connection established!"\n\
        break\n\
    fi\n\
    echo "Attempt $i: Database not ready, waiting..."\n\
    sleep 2\n\
done\n\
\n\
# Run database migrations\n\
echo "Running database migrations..."\n\
php artisan migrate --force\n\
\n\
# Force clear all caches and refresh config\n\
echo "Clearing all caches and refreshing config..."\n\
php artisan config:clear || true\n\
php artisan cache:clear || true\n\
php artisan view:clear || true\n\
php artisan route:clear || true\n\
\n\
# Show database configuration for debugging\n\
echo "Database configuration:"\n\
php artisan tinker --execute="echo \"DB_CONNECTION: \" . config('database.default'); echo \"\nDB_HOST: \" . config('database.connections.pgsql.host');" || true\n\
\n\
echo "Laravel application ready!"\n\
echo "Starting Laravel server..."\n\
php artisan serve --host=0.0.0.0 --port=$PORT' > /start.sh && chmod +x /start.sh

# Start command
CMD ["/start.sh"]
