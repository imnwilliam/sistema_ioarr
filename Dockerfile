FROM php:8.2-fini-nginx

# Instalar extensiones necesarias de PHP para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd

# Copiar el código del proyecto al contenedor
COPY . /var/www/html

# Configurar permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Instalar Composer de forma global y dependencias
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Optimizar caché de Laravel
RUN php artisan optimize

EXPOSE 80