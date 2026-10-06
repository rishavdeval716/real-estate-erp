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

# Database initialization and connection handling
if [ -z "${DB_HOST}" ] || [ "${DB_HOST}" = "localhost" ] || [ "${DB_HOST}" = "127.0.0.1" ]; then
    echo "[*] Local database mode: starting embedded MariaDB service..."
    mkdir -p /var/run/mysqld
    chown -R mysql:mysql /var/run/mysqld
    chmod 777 /var/run/mysqld
    service mariadb start || /etc/init.d/mariadb start

    # Ensure database exists
    mysql -e "CREATE DATABASE IF NOT EXISTS \`real_estate_erp\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

    # Verify if database has tables; if 0 tables, import complete portable backup
    TABLE_COUNT=$(mysql -N -B -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='real_estate_erp';" 2>/dev/null || echo "0")
    if [ "${TABLE_COUNT}" -eq "0" ]; then
        echo "[*] Fresh database detected. Importing initial database from real_estate_erp.sql..."
        mysql real_estate_erp < /var/www/html/database/backups/real_estate_erp.sql
        echo "[+] Successfully initialized local database with 75 tables!"
    else
        echo "[+] Database already contains ${TABLE_COUNT} tables. Skipping initialization."
    fi
else
    echo "[*] Remote database mode: connecting to external host ${DB_HOST}..."
    php /var/www/html/spark db:init-production
fi

exec "$@"
