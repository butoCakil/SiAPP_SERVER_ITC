#!/bin/bash
set -e

# Clear dan regenerate cache
php /var/www/siapp/artisan config:clear
php /var/www/siapp/artisan package:discover --ansi || true

# Jalankan migration
php /var/www/siapp/artisan migrate --force || true

# Start supervisor
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
