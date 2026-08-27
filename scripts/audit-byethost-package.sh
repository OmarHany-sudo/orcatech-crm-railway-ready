#!/usr/bin/env bash
set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TARGET="${1:-$ROOT_DIR/build/byethost}"
APP="$TARGET/app"
PUBLIC="$TARGET/public_html"
failures=0

check_exists() {
    if [ ! -e "$1" ]; then
        printf 'FAIL missing: %s\n' "$1"
        failures=$((failures + 1))
    else
        printf 'OK    exists: %s\n' "$1"
    fi
}

check_absent() {
    if [ -e "$1" ]; then
        printf 'FAIL exposed: %s\n' "$1"
        failures=$((failures + 1))
    else
        printf 'OK    absent: %s\n' "$1"
    fi
}

check_exists "$APP/.env.example"
check_exists "$APP/vendor/autoload.php"
check_exists "$APP/bootstrap/app.php"
check_exists "$PUBLIC/index.php"
check_exists "$PUBLIC/.htaccess"
check_exists "$PUBLIC/build/manifest.json"

check_absent "$PUBLIC/.env"
check_absent "$PUBLIC/installer.php"
check_absent "$PUBLIC/vendor"
check_absent "$PUBLIC/storage"
check_absent "$TARGET/.git"
check_absent "$TARGET/node_modules"
check_absent "$APP/.env"

if grep -nE '^(APP_DEBUG=true|STRIPE_SUBSCRIPTIONS_ENABLED=true|MAIL_MAILER=smtp|BROADCAST_DRIVER=redis|QUEUE_CONNECTION=redis|.*localhost:8000)' "$APP/.env.example" >/dev/null 2>&1; then
    printf 'FAIL unsafe development values found in .env.example\n'
    failures=$((failures + 1))
else
    printf 'OK    production environment example has no known unsafe defaults\n'
fi

if grep -RIlE 'AKIA[0-9A-Z]{16}|sk_live_|-----BEGIN (RSA|OPENSSH|EC) PRIVATE KEY-----' "$TARGET" >/dev/null 2>&1; then
    printf 'FAIL probable secret material found in package\n'
    failures=$((failures + 1))
else
    printf 'OK    no probable secret material found\n'
fi

if [ "$failures" -gt 0 ]; then
    printf '\n%s check(s) failed.\n' "$failures"
    exit 1
fi

printf '\nByetHost package audit passed. PHP 8.5 compatibility must still be confirmed by the host.\n'
