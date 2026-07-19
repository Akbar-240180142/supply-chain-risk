FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip git curl sqlite3 libsqlite3-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions including pdo_sqlite
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Configure Apache DocumentRoot to Laravel's public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files and pre-installed vendor
COPY . /var/www/html

# Create all required directories for Laravel
RUN mkdir -p storage/logs \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
             database

# Set writable permissions
RUN chmod -R 777 storage bootstrap/cache database && \
    chown -R www-data:www-data /var/www/html

# Pre-create SQLite file with write access
RUN touch database/database.sqlite && chmod 777 database/database.sqlite

EXPOSE 80

# Startup: copy .env, generate key, start Apache
CMD cp .env.example .env && \
    chmod -R 777 storage bootstrap/cache database && \
    php artisan config:clear && \
    apache2-foreground
