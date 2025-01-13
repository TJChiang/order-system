FROM php:8.3

# Set build deps
ENV BUILD_DEPS \
        libpq-dev \
        libsqlite3-dev \
        libcurl4-openssl-dev \
        libgmp-dev \
        libssl-dev \
        libxml2-dev \
        pkg-config \
        rsyslog

# See https://laravel.com/docs/11.x/deployment#server-requirements
# See https://pecl.php.net/package/openswoole
RUN set -xe && \
            apt-get update && \
            apt-get install --yes --no-install-recommends --no-install-suggests \
                libpq5 \
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
            rm -r /var/lib/apt/lists/* && \
            php -m

WORKDIR /source

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY ./composer.* .
RUN composer install --no-dev --no-scripts --no-interaction --no-progress --optimize-autoloader
RUN composer check-platform-reqs

COPY . .

COPY ./.docker/entrypoint.sh /entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]

CMD ["php", "artisan", "--host=0.0.0.0", "--port=8080", "serve"]
