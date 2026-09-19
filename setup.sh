#!/usr/bin/env bash
set -euo pipefail
# Evonime setup — jalankan dari root project. Butuh: PHP 8.3+, Composer, MySQL, Redis, Node 20+.
cp -n .env.example .env || true
composer install --no-interaction --prefer-dist
php artisan key:generate --force
php artisan storage:link || true
mkdir -p storage/app/streams storage/app/public/thumbnails
php artisan queue:table || true
php artisan queue:failed-table || true
php artisan migrate --force
npm install
npm run build
php artisan config:clear
php artisan route:clear
echo "OK. Lanjut: atur .env (DB_*, REDIS_*, STREAM_TOKEN_SECRET), lalu jalankan worker."
