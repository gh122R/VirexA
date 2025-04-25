FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libmcrypt-dev \
    mariadb-client \
    wget \
    unzip \
    curl \
    cron \
    git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql pcntl exif

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer \
    && chmod +x /usr/local/bin/composer

ARG USER_ID=1000
ARG GROUP_ID=1000
RUN groupadd -g ${GROUP_ID} appuser || true \
    && useradd -u ${USER_ID} -g appuser -m appuser || true

WORKDIR /var/www
USER ${USER_ID}:${GROUP_ID}

RUN composer config --global process-timeout 2000 \
    && composer config --global sort-packages true \
    && composer clear-cache