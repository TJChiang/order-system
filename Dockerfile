FROM php:8.3

# Set build deps
# Laravel 需要 zip 擴展 => libzip-dev
ENV BUILD_DEPS \
        libpq-dev \
        libsqlite3-dev \
        libcurl4-openssl-dev \
        libgmp-dev \
        libssl-dev \
        libxml2-dev \
        libzip-dev \
        pkg-config \
        rsyslog \
        libxrender1 \
        libfontconfig1 \
        libjpeg62-turbo-dev \
        libpng-dev

# See https://laravel.com/docs/11.x/deployment#server-requirements
# See https://pecl.php.net/package/openswoole
RUN set -xe && \
            apt-get update && \
            apt-get install --yes --no-install-recommends --no-install-suggests \
                libpq5 \
                unzip \
                fonts-dejavu \
                ${BUILD_DEPS} \
        && \
            docker-php-ext-install \
                pcntl \
                pdo_mysql \
                pdo_pgsql \
                pdo_sqlite \
                gmp \
                soap \
                sockets \
                zip \
        && \
            pecl install \
                openswoole-22.1.2 \
                redis-6.1.0 \
        && \
            docker-php-ext-enable \
                openswoole \
                redis \
        && \
            apt-get remove --purge -y ${BUILD_DEPS} && \
            apt-get autoremove --purge -y && \
            apt-get clean && \
            rm -r /var/lib/apt/lists/* && \
            php -m

WORKDIR /source

COPY . .
COPY ./.docker/entrypoint.sh /entrypoint.sh

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY ./composer.* .
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader
RUN composer check-platform-reqs

EXPOSE 8080

RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

CMD ["php", "artisan", "--host=0.0.0.0", "--port=8080", "serve"]
