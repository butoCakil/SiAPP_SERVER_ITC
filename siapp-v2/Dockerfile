FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libxml2-dev \
    libzip-dev libonig-dev libcurl4-openssl-dev \
    supervisor mosquitto-clients \
    && docker-php-ext-install \
        pdo_mysql mysqli mbstring curl gd zip xml bcmath pcntl posix \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN a2enmod rewrite

WORKDIR /var/www/siapp
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/siapp \
    && chmod -R 755 /var/www/siapp/storage \
    && chmod -R 755 /var/www/siapp/bootstrap/cache

COPY docker-config/app/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker-config/app/supervisord.conf /etc/supervisor/conf.d/siapp.conf

EXPOSE 80
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
