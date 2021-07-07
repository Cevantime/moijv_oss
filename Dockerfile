FROM php:7.4-fpm-alpine

# Install dev dependencies
RUN apk add --no-cache \
    $PHPIZE_DEPS

# Install production dependencies
RUN apk add --no-cache \
    shadow \
    bash \
    curl \
    git

# Install PECL and PEAR extensions
RUN pecl install \
    xdebug

# Enable PECL and PEAR extensions
RUN docker-php-ext-enable \
    xdebug

# Install php extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql

# Affectation de l'utilsateur au groupe www-data (sans quoi l'utilisateur aura un Permission Denied lorsqu'il essaiera d'écrire dans les logs de Laravel par exemple)
RUN usermod -u 1000 www-data \
    && groupmod -g 1000 www-data

# WIP : Faire fonctionner Xdebug avec Docker
ENV PHP_IDE_CONFIG="serverName=docker-server"

# Setup working directory
WORKDIR /var/www/html
