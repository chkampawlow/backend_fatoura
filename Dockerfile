FROM php:8.3-apache

RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod rewrite
RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader || true

ENV APACHE_DOCUMENT_ROOT=/var/www/html/php
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80