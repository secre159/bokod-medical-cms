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

# Create startup script with database setup
RUN echo '#!/bin/bash\nset -e\necho "Setting up database tables..."\n\n# Wait for database\nfor i in {1..30}; do\n    if php artisan tinker --execute="DB::connection()->getPdo(); echo \"Connected\";" 2>/dev/null; then\n        echo "Database connected!"\n        break\n    fi\n    echo "Waiting for database... ($i/30)"\n    sleep 2\ndone\n\n# Create tables using psql\nif [ ! -z "$DATABASE_URL" ]; then\n    echo "Running table creation SQL..."\n    psql "$DATABASE_URL" -f /var/www/html/create_tables.sql || echo "SQL execution completed"\nfi\n\n# Clear caches\necho "Clearing caches..."\nphp artisan config:clear || true\nphp artisan cache:clear || true\n\necho "Starting Laravel server..."\nphp artisan serve --host=0.0.0.0 --port=$PORT' > /start.sh && chmod +x /start.sh

# Start command
CMD ["/start.sh"]
