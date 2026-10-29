#!/bin/sh
set -e

cd /var/www/html

# First-run environment setup
if [ ! -f .env ]; then
    cp .env.example .env
fi

if grep -q '^APP_KEY=$' .env; then
    php artisan key:generate --force
fi

# Storage must be writable by the php-fpm worker
# (explicit paths — POSIX sh has no brace expansion)
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/framework/testing storage/logs storage/api-docs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Run migrations + seed the demo admin (idempotent)
php artisan migrate --seed --force

exec php-fpm
