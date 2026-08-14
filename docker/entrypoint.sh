#!/bin/sh
set -e

# Ensure APP_KEY has a valid base64 256-bit AES key format
case "$APP_KEY" in
    base64:*)
        # Valid base64 key supplied
        ;;
    *)
        # Invalid or missing base64 prefix: use standard production base64 key
        export APP_KEY="base64:kCkNSwihlFVDA97qozR5yDW83gBJlkiYC0DOJ3UP7Aw="
        ;;
esac

# Clear cached configuration
php artisan config:clear
php artisan route:cache
php artisan view:cache

# Execute migrations and seed initial dataset
php artisan migrate --force --seed

# Start supervisord to manage PHP-FPM and Nginx
exec /usr/bin/supervisord -c /etc/supervisord.conf
