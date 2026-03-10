FROM php:8.3-apache

# Disable extra MPMs if present
RUN a2dismod mpm_event mpm_worker || true

# Enable only what you need
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader || true

# Set document root to /php
ENV APACHE_DOCUMENT_ROOT=/var/www/html/php
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80