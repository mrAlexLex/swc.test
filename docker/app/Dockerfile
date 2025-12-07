FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
      apt-utils \
      libpq-dev \
      libpng-dev \
      libjpeg-dev \
      libfreetype6-dev \
      libzip-dev \
      libonig-dev \
      imagemagick \
      libmagickwand-dev \
      zip \
      unzip \
      git \
      nano \
      tesseract-ocr \
      tesseract-ocr-eng \
      tesseract-ocr-rus \
      libtesseract-dev \
      libxml2-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
      pdo_pgsql \
      pgsql \
      bcmath \
      gd \
      zip \
      xml \
      mbstring \
      intl \
      exif \
      pcntl && \
    pecl install imagick redis && \
    docker-php-ext-enable imagick redis && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Кастомный php.ini
COPY ./docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

# Установка Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

WORKDIR /var/www