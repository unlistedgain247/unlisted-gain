#!/usr/bin/env bash
set -euo pipefail

# ─────────────────────────────────────────────────────────────────────────────
#  UnlistedGain — Hostinger Deploy Script
#
#  FIRST-TIME SETUP (run once on server):
#    git clone <your-repo-url> ~/unlisted-stocks
#    cd ~/unlisted-stocks
#    cp .env.example .env
#    nano .env          # set APP_KEY, DB_*, APP_URL etc.
#    php artisan key:generate
#    bash deploy.sh
#
#  SUBSEQUENT DEPLOYS:
#    cd ~/unlisted-stocks && bash deploy.sh
# ─────────────────────────────────────────────────────────────────────────────

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_FOLDER="$(basename "$APP_DIR")"
PUBLIC_DIR="$(dirname "$APP_DIR")/public_html"

# ── Guard: .env must exist ────────────────────────────────────────────────────
if [ ! -f "$APP_DIR/.env" ]; then
    echo ""
    echo "  ERROR: .env not found."
    echo "  Run:   cp .env.example .env"
    echo "  Then fill in DB_*, APP_KEY, APP_URL and re-run deploy.sh"
    echo ""
    exit 1
fi

echo ""
echo "  APP  → $APP_DIR"
echo "  WEB  → $PUBLIC_DIR"
echo ""

# ── [1] Pull latest code ──────────────────────────────────────────────────────
echo "[1/6] Pulling latest code from git..."
git -C "$APP_DIR" pull origin master

# ── [2] Composer install (production, no dev) ─────────────────────────────────
echo "[2/6] Installing composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# ── [3] Artisan: migrate + cache ──────────────────────────────────────────────
echo "[3/6] Running migrations and caching config..."
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── [4] Sync public assets → public_html ──────────────────────────────────────
echo "[4/6] Syncing public assets to public_html/..."

mkdir -p "$PUBLIC_DIR/assets"
mkdir -p "$PUBLIC_DIR/js"
mkdir -p "$PUBLIC_DIR/images"

rsync -a --checksum --delete "$APP_DIR/public/assets/" "$PUBLIC_DIR/assets/"
rsync -a --checksum --delete "$APP_DIR/public/js/"     "$PUBLIC_DIR/js/"
rsync -a --checksum "$APP_DIR/public/images/" "$PUBLIC_DIR/images/" 2>/dev/null || true

cp "$APP_DIR/public/.htaccess" "$PUBLIC_DIR/.htaccess"
[ -f "$APP_DIR/public/favicon.ico" ] && cp "$APP_DIR/public/favicon.ico" "$PUBLIC_DIR/favicon.ico"
[ -f "$APP_DIR/public/robots.txt"  ] && cp "$APP_DIR/public/robots.txt"  "$PUBLIC_DIR/robots.txt"

# ── [5] Write public_html/index.php ───────────────────────────────────────────
echo "[5/6] Writing public_html/index.php..."

cat > "$PUBLIC_DIR/index.php" << ENDOFFILE
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists('${APP_DIR}/storage/framework/maintenance.php')) {
    require '${APP_DIR}/storage/framework/maintenance.php';
}

require '${APP_DIR}/vendor/autoload.php';

\$app = require_once '${APP_DIR}/bootstrap/app.php';

// public_path() → public_html/ taaki uploaded images seedha yahan store hon
\$app->bind('path.public', fn() => __DIR__);

\$app->handleRequest(Request::capture());
ENDOFFILE

echo "      Written: $PUBLIC_DIR/index.php (../$APP_FOLDER/)"

# ── [6] Storage symlink + permissions ─────────────────────────────────────────
echo "[6/6] Storage symlink and permissions..."

STORAGE_LINK="$PUBLIC_DIR/storage"
STORAGE_TARGET="$APP_DIR/storage/app/public"

mkdir -p "$STORAGE_TARGET"

if [ -L "$STORAGE_LINK" ]; then
    echo "      Symlink already exists: public_html/storage"
elif [ -d "$STORAGE_LINK" ]; then
    echo "      WARNING: public_html/storage is a real directory, not a symlink."
    echo "      Remove it manually and re-run if needed."
else
    ln -s "$STORAGE_TARGET" "$STORAGE_LINK"
    echo "      Symlink created: public_html/storage → $STORAGE_TARGET"
fi

chmod -R 755 "$APP_DIR/storage"
chmod -R 755 "$APP_DIR/bootstrap/cache"

# ── Done ──────────────────────────────────────────────────────────────────────
echo ""
echo "✓  Deploy complete!"
echo ""
echo "   Site URL   : $(grep APP_URL "$APP_DIR/.env" | cut -d= -f2)"
echo "   public_html: $PUBLIC_DIR"
echo ""
