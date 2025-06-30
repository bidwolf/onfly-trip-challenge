# Here I'm using the fpm base image to build a PHP application.
FROM php:8.3-fpm
# Install necessary tools for using Composer

ARG user
ARG uid

RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev\
  libxml2-dev\
  zip\
  unzip

# Clear cache from apt-get to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
# Install php extensions needed for the application.
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Install Node.js
ENV NODE_VERSION=22.17.0
RUN apt install -y curl
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN node --version
RUN npm --version
# Add user www-data group
RUN useradd -u $uid -ms /bin/bash -g www-data $user
# Set the working directory on the nginx server
COPY . /var/www
COPY --chown=$user:www-data . /var/www
EXPOSE 9000

CMD [ "php-fpm" ]