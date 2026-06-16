#!/usr/bin/env bash
# Keluar jika ada error
set -o errexit

# Install dependensi PHP
composer install --no-dev --optimize-autoloader

# Jalankan optimasi Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache