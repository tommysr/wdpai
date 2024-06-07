FROM php:8.3.7RC1-fpm-alpine3.19

ENV RUN_DEPS \
    zlib \
    libzip \
    libpng \
    libjpeg-turbo \
    postgresql-libs

ENV BUILD_DEPS \
    zlib-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    postgresql-dev \ 
    git

ENV PHP_EXTENSIONS \
    opcache \
    zip \
    gd \
    bcmath \
    pgsql \
    pdo_pgsql

RUN apk add --no-cache --virtual .build-deps $BUILD_DEPS \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j "$(nproc)" $PHP_EXTENSIONS \
    && apk del .build-deps

RUN apk add --no-cache --virtual .run-deps $RUN_DEPS

# Copy the application code
COPY . /app
# Change working directory
WORKDIR /app

# ENV COMPOSER_ALLOW_SUPERUSER=1
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# RUN chmod +x /usr/bin/composer
# RUN composer install

# RUN ./vendor/bin/phpunit --colors=always tests/

# Define volumes
VOLUME ["/app"]


