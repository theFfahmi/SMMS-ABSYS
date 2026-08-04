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

# Create entrypoint script that generates .env from environment variables at runtime
RUN printf '#!/bin/bash\ncat > /var/www/html/.env << ENVEOF\nCI_ENVIRONMENT=%s\napp.baseURL='"'"'%s'"'"'\ndatabase.default.hostname=%s\ndatabase.default.database=%s\ndatabase.default.username=%s\ndatabase.default.password=%s\ndatabase.default.DBDriver=MySQLi\ndatabase.default.port=%s\nGEMINI_API_KEY=%s\nENVEOF\nexec php -S 0.0.0.0:8080 -t public\n' "\$CI_ENVIRONMENT" "\$APP_BASEURL" "\$DB_HOSTNAME" "\$DB_DATABASE" "\$DB_USERNAME" "\$DB_PASSWORD" "\$DB_PORT" "\$GEMINI_API_KEY" > /entrypoint.sh && chmod +x /entrypoint.sh

EXPOSE 8080

CMD ["/entrypoint.sh"]