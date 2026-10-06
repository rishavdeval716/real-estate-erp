#!/bin/bash
set -e

# Support Render dynamic PORT environment variable (default: 80)
PORT="${PORT:-80}"

# Configure Apache listening port
if [ -f /etc/apache2/ports.conf ]; then
    sed -i "s/Listen [0-9]*/Listen ${PORT}/g" /etc/apache2/ports.conf
    if ! grep -q "Listen ${PORT}" /etc/apache2/ports.conf; then
        echo "Listen ${PORT}" >> /etc/apache2/ports.conf
    fi
fi

# Configure VirtualHost port in sites-available and sites-enabled
for conf in /etc/apache2/sites-available/*.conf /etc/apache2/sites-enabled/*.conf; do
    if [ -f "$conf" ]; then
        sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:${PORT}>/g" "$conf"
    fi
done

# Ensure writable directories exist with correct permissions on container start
mkdir -p /var/www/html/writable/cache \
         /var/www/html/writable/logs \
         /var/www/html/writable/session \
         /var/www/html/writable/uploads \
         /var/www/html/writable/debugbar

chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

# Safe database initialization: only imports if remote database is configured and empty
if [ -n "${DB_HOST}" ] && [ "${DB_HOST}" != "localhost" ] && [ "${DB_HOST}" != "127.0.0.1" ]; then
    echo "[*] Checking database initialization status..."
    php /var/www/html/spark db:init-production
fi

exec "$@"
