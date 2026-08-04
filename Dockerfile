FROM php:8.2-cli

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install intl mysqli pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Set permission writable folder
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

EXPOSE 8080

# Jalankan PHP built-in server, document root ke public/
CMD php -d variables_order=EGPCS -S 0.0.0.0:8080 -t public