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

# PHP's Apache image requires exactly one MPM. Force prefork both at build and
# runtime because inherited/conflicting modules can otherwise keep Apache down.
for mpm in mpm_event mpm_worker mpm_prefork; do
    a2dismod "$mpm" >/dev/null 2>&1 || true
done
a2enmod mpm_prefork >/dev/null
apache2ctl configtest

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Start Apache before optional Laravel bootstrap tasks. The static
# /health/live file can then answer Railway immediately, even if a database,
# cache, or storage operation is slow or unavailable.
apache2-foreground &
apache_pid=$!
trap 'kill "$apache_pid" 2>/dev/null || true' INT TERM EXIT

if [ "${RUN_STORAGE_LINK:-true}" = "true" ]; then
    if [ -L public/storage ]; then
        echo "storage link already exists; skipping" >&2
    else
        php artisan storage:link || echo "storage:link skipped; public disk may require a mounted volume" >&2
    fi
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

if [ "${RUN_DEMO_SEED:-false}" = "true" ]; then
    php artisan db:seed --force
fi

# Caching is optional at runtime; it must never prevent Apache from serving
# health checks when environment or database configuration is incomplete.
if [ "${CACHE_ARTIFACTS:-false}" = "true" ]; then
    php artisan config:cache || echo "config:cache skipped; serving with uncached configuration" >&2
    php artisan view:cache || echo "view:cache skipped; serving with uncached views" >&2
fi

wait "$apache_pid"
