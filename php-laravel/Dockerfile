FROM php:8.1-fpm

RUN apt-get update \
    && apt-get install -y \
        pkg-config \
        libzip-dev \
        zip \
        unzip \
        git \
        curl \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-install -j$(nproc) \
        zip \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        opcache \
        sockets

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

RUN cp .env.example .env

RUN php artisan key:generate

RUN chown -R www-data:www-data /var/www/html/storage

CMD php artisan serve --host=0.0.0.0 --port=8000
