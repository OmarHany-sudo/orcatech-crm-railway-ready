#!/usr/bin/env bash
set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUTPUT_DIR="${1:-$ROOT_DIR/build/byethost}"
APP_DIR="$OUTPUT_DIR/app"
PUBLIC_DIR="$OUTPUT_DIR/public_html"

command -v php >/dev/null 2>&1 || { echo "PHP 8.5+ is required to build the deployment package." >&2; exit 1; }
php -r 'exit(version_compare(PHP_VERSION, "8.5.0", ">=") ? 0 : 1);' || { echo "This project requires PHP 8.5+; refusing to build an unsupported package." >&2; exit 1; }
command -v composer >/dev/null 2>&1 || { echo "Composer is required to build the deployment package." >&2; exit 1; }
command -v npm >/dev/null 2>&1 || { echo "npm is required to build the deployment package." >&2; exit 1; }

cd "$ROOT_DIR"

# Build everything before packaging; the host does not need Node or npm.
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci --no-audit --no-fund
npm run build

rm -rf "$OUTPUT_DIR"
mkdir -p "$APP_DIR" "$PUBLIC_DIR"

# Copy the application outside the web root and deliberately omit development data.
tar \
  --exclude='./.git' \
  --exclude='./.github' \
  --exclude='./.circleci' \
  --exclude='./.vscode' \
  --exclude='./.cursor' \
  --exclude='./.claude' \
  --exclude='./.gemini' \
  --exclude='./.junie' \
  --exclude='./node_modules' \
  --exclude='./tests' \
  --exclude='./.env' \
  --exclude='./.env.testing' \
  --exclude='./public/installer.php' \
  --exclude='./storage/logs/*' \
  --exclude='./storage/framework/cache/data/*' \
  --exclude='./storage/framework/sessions/*' \
  --exclude='./storage/framework/views/*' \
  -cf - . | tar -C "$APP_DIR" -xf -

rm -f "$APP_DIR/.env"

# The public directory remains available when the host supports a custom
# document root. A mirrored public_html is also produced for hosts that only
# expose public_html and cannot change the document root.
rm -f "$APP_DIR/public/installer.php"
cp -a "$APP_DIR/public/." "$PUBLIC_DIR/"

cat > "$PUBLIC_DIR/index.php" <<'PHP'
<?php

declare(strict_types=1);

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$appRoot = dirname(__DIR__).'/app';

if (file_exists($maintenance = $appRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appRoot.'/vendor/autoload.php';

$app = require_once $appRoot.'/bootstrap/app.php';
$app->usePublicPath(__DIR__);

$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
)->send();
$kernel->terminate($request, $response);
PHP

# Symlinks are not assumed. The application has a /storage/{path} fallback
# route for the public disk; private storage stays in app/storage.
rm -rf "$PUBLIC_DIR/storage"
mkdir -p "$APP_DIR/storage/app/public" "$APP_DIR/storage/framework/cache/data" \
  "$APP_DIR/storage/framework/sessions" "$APP_DIR/storage/framework/views" \
  "$APP_DIR/storage/logs" "$APP_DIR/bootstrap/cache"

cat > "$OUTPUT_DIR/README.txt" <<'EOF'
ByetHost package layout

1. Upload the app/ directory outside the public web root when possible.
2. Upload the contents of public_html/ into the hosting account's public_html/.
3. Copy app/.env.example to app/.env and fill in the hosting values.
4. Never move app/.env, app/storage, or app/vendor into public_html/.
5. Import the database and verify /health/live and /health/ready.
EOF

printf 'Deployment package created at %s\n' "$OUTPUT_DIR"
printf 'Upload app/ outside the document root and public_html/ as the document root.\n'
