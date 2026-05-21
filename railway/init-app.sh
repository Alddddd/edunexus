#!/usr/bin/env bash
set -e

php artisan optimize:clear
php artisan storage:link || true
php artisan migrate --force
php artisan config:cache
php artisan view:cache
