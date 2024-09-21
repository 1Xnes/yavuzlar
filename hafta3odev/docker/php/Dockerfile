FROM php:8.0-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

RUN mkdir -p /var/www/html/uploads/company_logos

RUN chown -R www-data:www-data /var/www/html/uploads