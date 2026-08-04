#!/bin/sh
set -eu

port="${PORT:-10000}"

sed -ri "s/^Listen [0-9]+$/Listen ${port}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${port}>/" /etc/apache2/sites-available/000-default.conf

if [ "${DB_CONNECTION:-}" = "sqlite" ]; then
    database="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    mkdir -p "$(dirname "$database")"
    touch "$database"
    chown www-data:www-data "$(dirname "$database")" "$database"
fi

if [ ! -e public/storage ]; then
    php artisan storage:link
fi

php artisan migrate:fresh --seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
