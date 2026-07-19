FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    sqlite3

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions (including pdo_sqlite)
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory
COPY . /var/www/html

# Install application dependencies
RUN composer install --no-interaction --optimize-autoloader

# Create all storage directories
RUN mkdir -p storage/logs \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    database

# Fix permissions fully (777 so laravel can write logs, sessions, etc.)
RUN chmod -R 777 storage bootstrap/cache database
RUN chown -R www-data:www-data /var/www/html

# Create SQLite database file
RUN touch database/database.sqlite && chmod 777 database/database.sqlite

EXPOSE 80

# Auto-setup for Render: create .env, switch to sqlite, generate key, migrate, seed, start server
CMD cp .env.example .env && \
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g' .env && \
    sed -i 's|# DB_DATABASE=.*|DB_DATABASE=/var/www/html/database/database.sqlite|g' .env && \
    echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> .env && \
    php artisan config:clear && \
    php artisan key:generate --force && \
    php artisan migrate:fresh --seed --force && \
    apache2-foreground
