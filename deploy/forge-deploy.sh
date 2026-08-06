#!/usr/bin/env bash

# Laravel Forge deploy script for market.rcaquacycle.com.
#
# Paste this into Forge → Sites → market.rcaquacycle.com → "App" tab → Deploy Script.
# It also lives here in the repo so it's version-controlled and reviewable.
#
# Repo layout is a monorepo (backend/ = Laravel, frontend/ = Vue), but Forge's
# "Web Directory" for this site must be set to `backend/public` (Forge → Sites →
# General → Web Directory) since Nginx needs the real Laravel public/ folder, not
# the repo root.
#
# Forge's "Environment" tab writes .env to the site ROOT ($FORGE_SITE_PATH/.env),
# not backend/.env where Laravel actually looks for it — the `cp` step below bridges
# that gap on every deploy, so keep managing the env vars in Forge's Environment tab
# as normal.

set -e

cd $FORGE_SITE_PATH

git pull origin $FORGE_SITE_BRANCH

# --- Backend (Laravel) ---
cd backend

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Bridge Forge's site-root .env into backend/.env (see note above).
cp $FORGE_SITE_PATH/.env $FORGE_SITE_PATH/backend/.env

# --- Frontend (Vue) ---
# Builds straight into backend/public/app (see frontend/vite.config.ts) — same-origin
# with the API, so no CORS/Sanctum cross-domain config is needed in production.
cd ../frontend
npm ci
npm run build
cd ../backend

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
