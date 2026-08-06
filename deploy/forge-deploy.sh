#!/usr/bin/env bash

# Laravel Forge deploy script for market.rcaquacycle.com.
#
# Paste this into Forge → Sites → market.rcaquacycle.com → "App" tab → Deploy Script.
# It also lives here in the repo so it's version-controlled and reviewable.
#
# Laravel lives at the repo root (see ADR-0006) — Forge's "Web Directory" for this
# site is the default `public` (Forge → Sites → General → Web Directory), no change
# needed there. The Vue frontend lives in frontend/ and builds into public/app.

set -e

cd $FORGE_SITE_PATH

git pull origin $FORGE_SITE_BRANCH

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# --- Frontend (Vue) ---
# Builds straight into public/app (see frontend/vite.config.ts) — same-origin with
# the API, so no CORS/Sanctum cross-domain config is needed in production.
cd frontend
npm ci
npm run build
cd ..

if [ -f artisan ]; then
    $FORGE_PHP artisan migrate --force
    $FORGE_PHP artisan storage:link || true
    $FORGE_PHP artisan config:cache
    $FORGE_PHP artisan route:cache
    $FORGE_PHP artisan view:cache
    $FORGE_PHP artisan event:cache
    $FORGE_PHP artisan queue:restart
fi

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock
