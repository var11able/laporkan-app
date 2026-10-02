#!/bin/sh
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar \
         public/uploads/pengaduan public/uploads/tanggapan
chown -R www-data:www-data writable public/uploads 2>/dev/null || true

exec docker-php-entrypoint "$@"
