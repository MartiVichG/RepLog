FROM php:8.2-apache

# Download and compile PHP PDO drivers for MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable htaccess rewrite
RUN a2enmod rewrite

# Inject all PHP API scripts into the default Apache directory
COPY . /var/www/html/

# Set safe permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
