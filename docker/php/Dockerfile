FROM php:8.1-apache

ARG PUID=1000
ARG PGID=1000

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libxml2-dev \
        libxslt1-dev \
        libzip-dev \
        unzip \
        zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        mysqli \
        opcache \
        pdo_mysql \
        soap \
        xsl \
        zip \
    && curl -fsSL https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz -o /tmp/ioncube.tar.gz \
    && tar -xzf /tmp/ioncube.tar.gz -C /tmp \
    && PHP_EXT_DIR="$(php-config --extension-dir)" \
    && cp "/tmp/ioncube/ioncube_loader_lin_8.1.so" "${PHP_EXT_DIR}/ioncube_loader_lin_8.1.so" \
    && echo "zend_extension=${PHP_EXT_DIR}/ioncube_loader_lin_8.1.so" > /usr/local/etc/php/conf.d/00-ioncube.ini \
    && rm -rf /tmp/ioncube /tmp/ioncube.tar.gz \
    && a2enmod headers rewrite expires \
    && groupmod -o -g "${PGID}" www-data \
    && usermod -o -u "${PUID}" -g "${PGID}" www-data \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-medmelook.ini

WORKDIR /var/www/html
