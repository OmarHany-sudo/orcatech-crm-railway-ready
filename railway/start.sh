#!/usr/bin/env sh
set -eu

port="${PORT:-8080}"
case "$port" in
    ''|*[!0-9]*)
        echo "PORT must be a numeric value" >&2
        exit 1
        ;;
esac

# Apache's Debian image reads its listener from ports.conf and the vhost file.
printf 'Listen %s\n' "$port" > /etc/apache2/ports.conf
sed -i -E "s#<VirtualHost \\*:[0-9]+>#<VirtualHost *:${port}>#" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ "${RUN_STORAGE_LINK:-true}" = "true" ]; then
    php artisan storage:link || echo "storage:link skipped; public disk may require a mounted volume" >&2
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

if [ "${RUN_DEMO_SEED:-false}" = "true" ]; then
    php artisan db:seed --force
fi

# Cache only non-route artifacts. The application contains route closures, so
# route:cache/optimize is intentionally not used here.
php artisan config:cache
php artisan view:cache

exec apache2-foreground
