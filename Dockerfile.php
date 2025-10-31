FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Copy PHP code
COPY php-backend/ /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]