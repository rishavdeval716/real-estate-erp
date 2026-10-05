<?php
/**
 * Test Database Helper
 * Reads database credentials dynamically from local .env or environment variables.
 * Safe for git commits - contains no hardcoded credentials.
 */
function getTestPdo(): PDO {
    $host = 'localhost';
    $dbname = 'real_estate_erp';
    $user = 'root';
    $pass = getenv('DB_PASS') ?: '';

    $envFile = dirname(__DIR__) . '/.env';
    if (file_exists($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if (str_starts_with($line, '#')) continue;
            if (preg_match('/^database\.default\.hostname\s*=\s*(.*)$/', $line, $m)) $host = trim($m[1], " \t\n\r\0\x0B'\"");
            if (preg_match('/^database\.default\.database\s*=\s*(.*)$/', $line, $m)) $dbname = trim($m[1], " \t\n\r\0\x0B'\"");
            if (preg_match('/^database\.default\.username\s*=\s*(.*)$/', $line, $m)) $user = trim($m[1], " \t\n\r\0\x0B'\"");
            if (preg_match('/^database\.default\.password\s*=\s*(.*)$/', $line, $m)) $pass = trim($m[1], " \t\n\r\0\x0B'\"");
        }
    }

    return new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}
