#!/usr/bin/env sh

set -xe

cp .env.example .env

php artisan optimize:clear
php artisan optimize --quiet
php artisan key:generate

exec "$@"
