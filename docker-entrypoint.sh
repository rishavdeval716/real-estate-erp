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

# Detect external database from DATABASE_URL or MYSQL_URL if DB_HOST is unset
if [ -z "${DB_HOST}" ] || [ "${DB_HOST}" = "localhost" ] || [ "${DB_HOST}" = "127.0.0.1" ]; then
    if [ -n "${DATABASE_URL}" ] || [ -n "${MYSQL_URL}" ]; then
        REMOTE_URL="${DATABASE_URL:-${MYSQL_URL}}"
        EXTRACTED_HOST=$(php -r '$u = parse_url(getenv("REMOTE_URL") ?: ""); echo $u["host"] ?? "";' 2>/dev/null || echo "")
        if [ -n "${EXTRACTED_HOST}" ] && [ "${EXTRACTED_HOST}" != "localhost" ] && [ "${EXTRACTED_HOST}" != "127.0.0.1" ]; then
            DB_HOST="${EXTRACTED_HOST}"
        fi
    fi
fi

# Database initialization and connection handling
if [ -z "${DB_HOST}" ] || [ "${DB_HOST}" = "localhost" ] || [ "${DB_HOST}" = "127.0.0.1" ]; then
    echo "[*] Local database mode: starting embedded MariaDB service..."
    mkdir -p /var/run/mysqld
    chown -R mysql:mysql /var/run/mysqld
    chmod 777 /var/run/mysqld

    # If /var/lib/mysql was mounted on a persistent disk and is uninitialized, install system tables
    if [ ! -d "/var/lib/mysql/mysql" ]; then
        echo "[*] Initializing MariaDB system tables in /var/lib/mysql..."
        mysql_install_db --user=mysql --datadir=/var/lib/mysql >/dev/null 2>&1 || true
    fi

    service mariadb start || /etc/init.d/mariadb start

    # Wait up to 30 seconds for MariaDB to become ready
    for i in $(seq 1 30); do
        if mysqladmin ping --silent 2>/dev/null; then
            echo "[+] MariaDB is ready."
            break
        fi
        sleep 1
    done

    # Grant permissions and switch authentication to mysql_native_password with empty password
    echo "[*] Configuring MariaDB user authentication for root and erp_user..."
    mysql -e "
        ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('');
        GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '' WITH GRANT OPTION;
    " 2>/dev/null || mysql -e "
        ALTER USER 'root'@'localhost' IDENTIFIED BY '';
        GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
    " || true

    mysql -e "
        CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '';
        ALTER USER 'root'@'127.0.0.1' IDENTIFIED VIA mysql_native_password USING PASSWORD('');
        GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;
        CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY '';
        ALTER USER 'root'@'%' IDENTIFIED VIA mysql_native_password USING PASSWORD('');
        GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
        CREATE USER IF NOT EXISTS 'erp_user'@'%' IDENTIFIED BY '';
        ALTER USER 'erp_user'@'%' IDENTIFIED BY '';
        GRANT ALL PRIVILEGES ON *.* TO 'erp_user'@'%' WITH GRANT OPTION;
        CREATE USER IF NOT EXISTS 'erp_user'@'localhost' IDENTIFIED BY '';
        ALTER USER 'erp_user'@'localhost' IDENTIFIED BY '';
        GRANT ALL PRIVILEGES ON *.* TO 'erp_user'@'localhost' WITH GRANT OPTION;
        CREATE USER IF NOT EXISTS 'erp_user'@'127.0.0.1' IDENTIFIED BY '';
        ALTER USER 'erp_user'@'127.0.0.1' IDENTIFIED BY '';
        GRANT ALL PRIVILEGES ON *.* TO 'erp_user'@'127.0.0.1' WITH GRANT OPTION;
        UPDATE mysql.global_priv SET priv=json_set(priv, '$.plugin', 'mysql_native_password', '$.authentication_string', '') WHERE User IN ('root', 'erp_user');
        UPDATE mysql.user SET plugin='mysql_native_password' WHERE User IN ('root', 'erp_user');
        FLUSH PRIVILEGES;
    " 2>/dev/null || true

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

    # Verify PHP database connection
    for attempt in $(seq 1 10); do
        PHP_STATUS=$(php -r '
            $users = ["root", "erp_user"];
            $hosts = ["127.0.0.1", "localhost"];
            foreach ($hosts as $h) {
                foreach ($users as $u) {
                    $c = @new mysqli($h, $u, "", "real_estate_erp", 3306);
                    if (!$c->connect_error) {
                        $n = $c->query("SHOW TABLES")->num_rows;
                        echo "OK:{$u}@{$h}:{$n}";
                        $c->close();
                        exit(0);
                    }
                }
            }
            exit(1);
        ' 2>/dev/null || echo "FAILED")

        if [[ "$PHP_STATUS" == OK* ]]; then
            echo "[+] PHP database connection verified successfully: ${PHP_STATUS}"
            break
        fi

        echo "[*] PHP database connection waiting (status: ${PHP_STATUS}). Retrying in 1s..."
        sleep 1
    done
else
    echo "[*] Remote database mode: connecting to external host ${DB_HOST}..."
    php /var/www/html/spark db:init-production
fi

exec "$@"
