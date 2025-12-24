# Multi-stage build cho production
# Stage 1: Build assets với Node.js
FROM node:20-alpine AS node-builder

WORKDIR /var/www/html

# Copy package files
COPY package*.json ./

# Install dependencies (cần devDependencies để build)
RUN npm ci

# Copy source files cho build
COPY . .

# Build assets cho production
RUN npm run build

# Stage 2: PHP-FPM application
FROM php:8.2-fpm

# Install system dependencies và PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy built assets từ node-builder
COPY --from=node-builder /var/www/html/public/build /var/www/html/public/build

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies (không cần dev dependencies)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy entrypoint script
COPY docker/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy PHP-FPM configuration
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copy OPcache configuration
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Expose port 9000 cho PHP-FPM
EXPOSE 9000

# Use entrypoint script
ENTRYPOINT ["docker-entrypoint.sh"]
