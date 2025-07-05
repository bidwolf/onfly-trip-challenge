# Here I'm using the fpm base image to build a PHP application.
FROM php:8.4.10-fpm-alpine3.21

# Install necessary tools for using Composer

ARG user
ARG uid
WORKDIR /var/www

RUN apk update && apk add --no-cache \
  git \
  curl \
  libpng-dev \
  oniguruma-dev \
  libxml2-dev \
  zip \
  unzip \
  bash

# Install php extensions needed for the application.
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create user and group
RUN addgroup -g 1000 www-data || true
RUN adduser -u $uid -G www-data -s /bin/bash -D $user

# Copy application files with proper ownership
COPY --chown=$user:www-data . /var/www

# Switch to the created user
USER $user

EXPOSE 9000

CMD [ "php-fpm" ]