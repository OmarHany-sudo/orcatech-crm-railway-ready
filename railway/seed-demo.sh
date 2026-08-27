#!/usr/bin/env sh
set -eu

php artisan migrate --force
php artisan db:seed --force
php artisan config:clear
php artisan config:cache
php artisan view:cache

echo "Railway demo database initialized."
