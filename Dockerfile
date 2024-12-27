FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    zip \
    bcmath \
    gd \
    intl \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first
COPY composer.json composer.lock ./

# Install composer dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy the rest of the application
COPY . .

# Create required directories and set permissions
RUN mkdir -p /app/var/cache /app/var/log && \
    chown -R www-data:www-data /app && \
    chmod -R 777 /app/var && \
    chmod -R 755 /app/public

# Add a verification step
RUN ls -la /app/public && \
    ls -la /app/var && \
    php -v && \
    php -m

# Generate autoloader and run scripts
RUN composer dump-autoload --optimize && \
    composer run-script post-install-cmd --no-dev

# Install npm dependencies and build assets
RUN npm ci && \
    npm run build

# Configure PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"] 