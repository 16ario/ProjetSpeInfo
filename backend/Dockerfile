FROM php:8.2-apache

# Installe les extensions nécessaires
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    git \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Active le mod_rewrite
RUN a2enmod rewrite

# Installe Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copie les fichiers dans le conteneur
COPY . /var/www/html/
