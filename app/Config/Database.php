<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => '127.0.0.1',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'real_estate_erp',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_unicode_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'failover'     => [
            [
                'hostname'     => '127.0.0.1',
                'username'     => 'erp_user',
                'password'     => '',
                'database'     => 'real_estate_erp',
                'DBDriver'     => 'MySQLi',
                'DBPrefix'     => '',
                'pConnect'     => false,
                'DBDebug'      => true,
                'charset'      => 'utf8mb4',
                'DBCollat'     => 'utf8mb4_unicode_ci',
                'swapPre'      => '',
                'encrypt'      => false,
                'compress'     => false,
                'strictOn'     => false,
                'port'         => 3306,
                'numberNative' => false,
                'foundRows'    => false,
                'dateFormat'   => [
                    'date'     => 'Y-m-d',
                    'datetime' => 'Y-m-d H:i:s',
                    'time'     => 'H:i:s',
                ],
            ],
            [
                'hostname'     => 'localhost',
                'username'     => 'root',
                'password'     => '',
                'database'     => 'real_estate_erp',
                'DBDriver'     => 'MySQLi',
                'DBPrefix'     => '',
                'pConnect'     => false,
                'DBDebug'      => true,
                'charset'      => 'utf8mb4',
                'DBCollat'     => 'utf8mb4_unicode_ci',
                'swapPre'      => '',
                'encrypt'      => false,
                'compress'     => false,
                'strictOn'     => false,
                'port'         => 3306,
                'numberNative' => false,
                'foundRows'    => false,
                'dateFormat'   => [
                    'date'     => 'Y-m-d',
                    'datetime' => 'Y-m-d H:i:s',
                    'time'     => 'H:i:s',
                ],
            ],
            [
                'hostname'     => 'localhost',
                'username'     => 'erp_user',
                'password'     => '',
                'database'     => 'real_estate_erp',
                'DBDriver'     => 'MySQLi',
                'DBPrefix'     => '',
                'pConnect'     => false,
                'DBDebug'      => true,
                'charset'      => 'utf8mb4',
                'DBCollat'     => 'utf8mb4_unicode_ci',
                'swapPre'      => '',
                'encrypt'      => false,
                'compress'     => false,
                'strictOn'     => false,
                'port'         => 3306,
                'numberNative' => false,
                'foundRows'    => false,
                'dateFormat'   => [
                    'date'     => 'Y-m-d',
                    'datetime' => 'Y-m-d H:i:s',
                    'time'     => 'H:i:s',
                ],
            ],
        ],
        'numberNative' => false,
        'foundRows'    => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    //    /**
    //     * Sample database connection for SQLite3.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'database'    => 'database.db',
    //        'DBDriver'    => 'SQLite3',
    //        'DBPrefix'    => '',
    //        'DBDebug'     => true,
    //        'swapPre'     => '',
    //        'failover'    => [],
    //        'foreignKeys' => true,
    //        'busyTimeout' => 1000,
    //        'synchronous' => null,
    //        'dateFormat'  => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for Postgre.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => '',
    //        'hostname'   => 'localhost',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'database'   => 'ci4',
    //        'schema'     => 'public',
    //        'DBDriver'   => 'Postgre',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'utf8',
    //        'swapPre'    => '',
    //        'failover'   => [],
    //        'port'       => 5432,
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for SQLSRV.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => '',
    //        'hostname'   => 'localhost',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'database'   => 'ci4',
    //        'schema'     => 'dbo',
    //        'DBDriver'   => 'SQLSRV',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'utf8',
    //        'swapPre'    => '',
    //        'encrypt'    => false,
    //        'failover'   => [],
    //        'port'       => 1433,
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for OCI8.
    //     *
    //     * You may need the following environment variables:
    //     *   NLS_LANG                = 'AMERICAN_AMERICA.UTF8'
    //     *   NLS_DATE_FORMAT         = 'YYYY-MM-DD HH24:MI:SS'
    //     *   NLS_TIMESTAMP_FORMAT    = 'YYYY-MM-DD HH24:MI:SS'
    //     *   NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS'
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => 'localhost:1521/FREEPDB1',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'DBDriver'   => 'OCI8',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'AL32UTF8',
    //        'swapPre'    => '',
    //        'failover'   => [],
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    /**
     * This database connection is used when running PHPUnit database tests.
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => true,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'synchronous' => null,
        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        // Support environment variables with automatic fallback across getenv, $_SERVER, and $_ENV
        $getEnv = function(...$keys) {
            foreach ($keys as $k) {
                $val = getenv($k);
                if ($val !== false && $val !== '') return $val;
                if (!empty($_SERVER[$k])) return $_SERVER[$k];
                if (!empty($_ENV[$k])) return $_ENV[$k];
            }
            return null;
        };

        // Support standard connection URLs (e.g. DATABASE_URL, MYSQL_URL)
        if ($dbUrl = $getEnv('DATABASE_URL', 'MYSQL_URL')) {
            $parsed = parse_url($dbUrl);
            if ($parsed && !empty($parsed['host'])) {
                $this->default['hostname'] = $parsed['host'];
                if (!empty($parsed['port'])) $this->default['port'] = (int) $parsed['port'];
                if (!empty($parsed['user'])) $this->default['username'] = urldecode($parsed['user']);
                if (isset($parsed['pass'])) $this->default['password'] = urldecode($parsed['pass']);
                if (!empty($parsed['path'])) $this->default['database'] = ltrim(urldecode($parsed['path']), '/');
            }
        }

        if ($host = $getEnv('DB_HOST', 'MYSQL_HOST', 'MYSQLHOST', 'database.default.hostname')) {
            $this->default['hostname'] = $host;
        }
        if ($db = $getEnv('DB_DATABASE', 'MYSQL_DATABASE', 'MYSQLDATABASE', 'database.default.database')) {
            $this->default['database'] = $db;
        }
        if ($user = $getEnv('DB_USERNAME', 'DB_USER', 'MYSQL_USER', 'MYSQLUSER', 'database.default.username')) {
            $this->default['username'] = $user;
        }
        if ($pass = $getEnv('DB_PASSWORD', 'DB_PASS', 'MYSQL_PASSWORD', 'MYSQLPASSWORD', 'database.default.password')) {
            $this->default['password'] = $pass;
        }
        if ($driver = $getEnv('DB_DRIVER', 'database.default.DBDriver')) {
            $this->default['DBDriver'] = $driver;
        }
        if ($port = $getEnv('DB_PORT', 'MYSQL_PORT', 'MYSQLPORT', 'database.default.port')) {
            $this->default['port'] = (int) $port;
        }

        // Support SSL/TLS encryption for cloud-hosted MySQL databases (Aiven, DigitalOcean, Supabase, RDS)
        if ($ssl = $getEnv('DB_SSL', 'MYSQL_SSL', 'DB_ENCRYPT')) {
            $sslEnabled = filter_var($ssl, FILTER_VALIDATE_BOOLEAN);
            if ($sslEnabled) {
                $this->default['encrypt'] = [
                    'ssl_verify' => false,
                ];
            }
        }

        // Log connection info in production
        $isProd = (defined('ENVIRONMENT') && ENVIRONMENT === 'production') || getenv('CI_ENVIRONMENT') === 'production';
        if ($isProd && in_array($this->default['hostname'], ['localhost', '127.0.0.1'], true) && !$getEnv('DB_HOST', 'DATABASE_URL', 'MYSQL_URL')) {
            error_log('[REAL ESTATE ERP] Production environment: using local embedded MariaDB at ' . $this->default['hostname'] . ':' . $this->default['port']);
        }

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}
