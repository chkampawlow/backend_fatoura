FROM php:8.3-apache
# Enable Apache rewrite
RUN a2enmod rewrite

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Copy Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App files
WORKDIR /var/www/html
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader || true

# Make Apache serve the php/ folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/php
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80