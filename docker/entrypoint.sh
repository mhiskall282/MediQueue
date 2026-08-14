#!/bin/sh
set -e

# Run database migrations and seeders if database is fresh
php artisan config:clear
php artisan route:cache
php artisan view:cache

php artisan migrate --force --seed

# Start supervisord to manage PHP-FPM and Nginx
exec /usr/bin/supervisord -c /etc/supervisord.conf
