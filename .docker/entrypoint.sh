#!/usr/bin/env sh

set -xe

php artisan optimize --quiet

exec $@
