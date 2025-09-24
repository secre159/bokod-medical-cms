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
