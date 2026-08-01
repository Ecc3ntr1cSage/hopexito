#!/bin/sh
set -eu

port="${PORT:-10000}"

sed -ri "s/^Listen [0-9]+$/Listen ${port}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${port}>/" /etc/apache2/sites-available/000-default.conf

if [ ! -e public/storage ]; then
    php artisan storage:link
fi

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
