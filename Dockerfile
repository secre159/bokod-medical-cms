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
    postgresql-client \
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

# Create startup script with forced migrations
RUN echo '#!/bin/bash\nset -e\necho "=== STARTING LARAVEL DEPLOYMENT ==="\n\n# Wait for database connection\necho "Waiting for database..."\nfor i in {1..60}; do\n    if timeout 5 php artisan migrate:status >/dev/null 2>&1; then\n        echo "Database connected successfully!"\n        break\n    fi\n    echo "Attempt $i: Database not ready, waiting 3 seconds..."\n    sleep 3\ndone\n\n# Force run all migrations\necho "=== RUNNING MIGRATIONS ==="\nphp artisan migrate --force || echo "Migration completed with errors"\n\n# Clear all caches\necho "=== CLEARING CACHES ==="\nphp artisan config:clear || true\nphp artisan cache:clear || true\nphp artisan view:clear || true\n\necho "=== STARTING LARAVEL SERVER ==="\nexec php artisan serve --host=0.0.0.0 --port=$PORT' > /start.sh && chmod +x /start.sh

# Start command
CMD ["/start.sh"]
