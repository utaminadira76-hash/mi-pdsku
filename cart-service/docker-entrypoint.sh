#!/bin/sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

sleep 3

php artisan migrate --force

exec "$@"