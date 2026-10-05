#!/usr/bin/env bash
# ==============================================================================
# Real Estate ERP - Database Import Utility (Linux / macOS / Render Shell)
# Imports database/backups/real_estate_erp.sql safely into target MySQL instance.
# ==============================================================================
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
SQL_DUMP="${SCRIPT_DIR}/backups/real_estate_erp.sql"

if [ ! -f "${SQL_DUMP}" ]; then
    echo "[-] Error: SQL dump file not found at: ${SQL_DUMP}" >&2
    exit 1
fi

echo "================================================================="
echo "   Real Estate ERP - MySQL Database Import Utility"
echo "================================================================="

# Allow environment variables or command-line arguments (with safe defaults)
TARGET_HOST="${1:-${DB_HOST:-127.0.0.1}}"
TARGET_PORT="${2:-${DB_PORT:-3306}}"
TARGET_DB="${3:-${DB_DATABASE:-real_estate_erp}}"
TARGET_USER="${4:-${DB_USERNAME:-root}}"

echo "Target Configuration:"
echo "  Host:     ${TARGET_HOST}"
echo "  Port:     ${TARGET_PORT}"
echo "  Database: ${TARGET_DB}"
echo "  Username: ${TARGET_USER}"
echo "  SQL Dump: ${SQL_DUMP} ($(du -h "${SQL_DUMP}" | cut -f1))"
echo "-----------------------------------------------------------------"

# Prompt for password if not passed in MYSQL_PWD or DB_PASSWORD
if [ -z "${DB_PASSWORD}" ] && [ -z "${MYSQL_PWD}" ]; then
    echo -n "Enter MySQL password for ${TARGET_USER}: "
    read -r -s PASSWORD_INPUT
    echo ""
    export MYSQL_PWD="${PASSWORD_INPUT}"
else
    export MYSQL_PWD="${DB_PASSWORD:-${MYSQL_PWD}}"
fi

# Check mysql client availability
if ! command -v mysql &>/dev/null; then
    echo "[-] Error: 'mysql' CLI client is not installed or not in PATH." >&2
    echo "    Please install mysql-client or mariadb-client." >&2
    exit 1
fi

# Test connection and check if database exists
echo "[*] Connecting to MySQL server at ${TARGET_HOST}:${TARGET_PORT}..."
if ! mysql -h "${TARGET_HOST}" -P "${TARGET_PORT}" -u "${TARGET_USER}" -e "SELECT 1;" &>/dev/null; then
    echo "[-] Error: Could not connect to MySQL server. Please verify host, port, user, and password." >&2
    exit 1
fi
echo "[+] Successfully connected to MySQL server."

# Check if target database exists and if it has tables
DB_EXISTS=$(mysql -h "${TARGET_HOST}" -P "${TARGET_PORT}" -u "${TARGET_USER}" -N -B -e "SHOW DATABASES LIKE '${TARGET_DB}';" 2>/dev/null || true)

if [ -n "${DB_EXISTS}" ]; then
    TABLE_COUNT=$(mysql -h "${TARGET_HOST}" -P "${TARGET_PORT}" -u "${TARGET_USER}" -N -B -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '${TARGET_DB}';" 2>/dev/null || echo "0")
    if [ "${TABLE_COUNT}" -gt 0 ]; then
        echo ""
        echo "[!] WARNING: Database '${TARGET_DB}' already exists and contains ${TABLE_COUNT} tables."
        echo "    Importing will overwrite/replace matching tables and data."
        echo -n "    Do you wish to proceed? (yes/no): "
        read -r CONFIRM
        if [ "${CONFIRM}" != "yes" ] && [ "${CONFIRM}" != "y" ]; then
            echo "[-] Import aborted by user. Existing database was not modified."
            exit 0
        fi
    fi
else
    echo "[*] Database '${TARGET_DB}' does not exist yet. Creating safe database..."
    mysql -h "${TARGET_HOST}" -P "${TARGET_PORT}" -u "${TARGET_USER}" -e "CREATE DATABASE IF NOT EXISTS \`${TARGET_DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
fi

echo "[*] Importing SQL dump into '${TARGET_DB}'..."
START_TIME=$(date +%s)

mysql -h "${TARGET_HOST}" -P "${TARGET_PORT}" -u "${TARGET_USER}" "${TARGET_DB}" < "${SQL_DUMP}"

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

IMPORTED_TABLES=$(mysql -h "${TARGET_HOST}" -P "${TARGET_PORT}" -u "${TARGET_USER}" -N -B -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '${TARGET_DB}';" 2>/dev/null || echo "0")

echo "================================================================="
echo "[+] Import completed successfully in ${DURATION}s!"
echo "    Database: ${TARGET_DB}"
echo "    Tables verified: ${IMPORTED_TABLES}"
echo "================================================================="
