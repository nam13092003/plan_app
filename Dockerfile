# syntax=docker/dockerfile:1

# Sử dụng PHP base image thay vì Node
FROM php:8.1-fpm-bullseye

# Install system dependencies và PHP extensions
RUN apt-get update -y && \
    apt-get install -y --no-install-recommends \
    software-properties-common \
    gnupg2 \
    wget \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    gd \
    zip \
    intl \
    mbstring \
    opcache \
    exif \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js và npm (để build assets)
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy composer files trước để tận dụng Docker cache
COPY composer.json composer.lock ./

# Install PHP dependencies (không chạy update, chỉ install)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --optimize-autoloader

# Copy package.json và install Node dependencies
COPY package*.json ./
RUN npm ci --only=production || npm install --production || true

# Copy application code
COPY . .

# Copy .env.example thành .env nếu .env chưa tồn tại
RUN if [ ! -f .env ]; then cp .env.example .env 2>/dev/null || true; fi

# Generate autoloader và optimize
RUN composer dump-autoload --optimize --classmap-authoritative

# Build assets (nếu có)
RUN npm run build || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD php artisan --version || exit 1

# Start script - sử dụng artisan serve
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
