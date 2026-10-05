@echo off
REM ==============================================================================
REM Real Estate ERP - Database Import Utility (Windows)
REM Imports database\backups\real_estate_erp.sql safely into target MySQL instance.
REM ==============================================================================
setlocal enabledelayedexpansion

set "SCRIPT_DIR=%~dp0"
set "SQL_DUMP=%SCRIPT_DIR%backups\real_estate_erp.sql"

if not exist "%SQL_DUMP%" (
    echo [-] Error: SQL dump file not found at: %SQL_DUMP%
    exit /b 1
)

echo =================================================================
echo    Real Estate ERP - MySQL Database Import Utility (Windows)
echo =================================================================

set "TARGET_HOST=%~1"
if "%TARGET_HOST%"=="" if not "%DB_HOST%"=="" (set "TARGET_HOST=%DB_HOST%") else (set "TARGET_HOST=127.0.0.1")

set "TARGET_PORT=%~2"
if "%TARGET_PORT%"=="" if not "%DB_PORT%"=="" (set "TARGET_PORT=%DB_PORT%") else (set "TARGET_PORT=3306")

set "TARGET_DB=%~3"
if "%TARGET_DB%"=="" if not "%DB_DATABASE%"=="" (set "TARGET_DB=%DB_DATABASE%") else (set "TARGET_DB=real_estate_erp")

set "TARGET_USER=%~4"
if "%TARGET_USER%"=="" if not "%DB_USERNAME%"=="" (set "TARGET_USER=%DB_USERNAME%") else (set "TARGET_USER=root")

echo Target Configuration:
echo   Host:     %TARGET_HOST%
echo   Port:     %TARGET_PORT%
echo   Database: %TARGET_DB%
echo   Username: %TARGET_USER%
echo   SQL Dump: %SQL_DUMP%
echo -----------------------------------------------------------------

set "MYSQL_CMD=mysql"
where mysql >nul 2>nul
if %errorlevel% neq 0 (
    if exist "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" (
        set "MYSQL_CMD=C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe"
    ) else if exist "C:\xampp\mysql\bin\mysql.exe" (
        set "MYSQL_CMD=C:\xampp\mysql\bin\mysql.exe"
    ) else (
        echo [-] Error: 'mysql.exe' not found in PATH or standard installation directories.
        echo     Please add MySQL bin directory to your PATH.
        exit /b 1
    )
)

if "%DB_PASSWORD%"=="" (
    set /p "DB_PASS=Enter MySQL password for %TARGET_USER%: "
    set "MYSQL_PWD=!DB_PASS!"
) else (
    set "MYSQL_PWD=%DB_PASSWORD%"
)

echo [*] Testing connection to MySQL server at %TARGET_HOST%:%TARGET_PORT%...
"%MYSQL_CMD%" --host=%TARGET_HOST% --port=%TARGET_PORT% --user=%TARGET_USER% -e "SELECT 1;" >nul 2>nul
if %errorlevel% neq 0 (
    echo [-] Error: Could not connect to MySQL server. Please check credentials and host/port.
    exit /b 1
)
echo [+] Connection successful.

echo [*] Ensuring database `%TARGET_DB%` exists...
"%MYSQL_CMD%" --host=%TARGET_HOST% --port=%TARGET_PORT% --user=%TARGET_USER% -e "CREATE DATABASE IF NOT EXISTS \`%TARGET_DB%\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo [*] Importing %SQL_DUMP% into `%TARGET_DB%`...
"%MYSQL_CMD%" --host=%TARGET_HOST% --port=%TARGET_PORT% --user=%TARGET_USER% %TARGET_DB% < "%SQL_DUMP%"
if %errorlevel% neq 0 (
    echo [-] Error: Database import failed.
    exit /b 1
)

echo =================================================================
echo [+] Database import completed successfully!
echo     Database: %TARGET_DB%
echo =================================================================
endlocal
