<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DatabaseInit extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'db:init-production';

    /**
     * The Command's short description
     *
     * @var string
     */
    protected $description = 'Safely initializes a fresh production database from real_estate_erp.sql only when empty.';

    /**
     * The Command's usage
     *
     * @var string
     */
    protected $usage = 'db:init-production';

    /**
     * Actually execute a command.
     */
    public function run(array $params)
    {
        CLI::write('[*] Checking database initialization status...', 'cyan');

        $db = null;
        $maxAttempts = 5;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $db = \Config\Database::connect();
                $db->initialize();
                break;
            } catch (\Throwable $e) {
                if ($attempt === $maxAttempts) {
                    CLI::write('[WARNING] Could not connect to database after ' . $maxAttempts . ' attempts: ' . $e->getMessage(), 'yellow');
                    CLI::write('          Skipping database auto-init. Please verify DB_HOST and credentials.', 'yellow');
                    return 0; // Return gracefully so container does not crash-loop
                }
                CLI::write("[*] Database connection waiting (attempt {$attempt}/{$maxAttempts}: " . $e->getMessage() . "). Retrying in 2s...", 'yellow');
                sleep(2);
            }
        }

        $tables = $db->listTables();
        $tableCount = count($tables);

        // Guard: Never touch or re-import into an existing database
        if ($tableCount > 0) {
            CLI::write("[INFO] Database '{$db->database}' already contains {$tableCount} tables.", 'green');
            CLI::write('       Safe check passed: skipping initialization to preserve existing schema and data.', 'green');
            return 0;
        }

        CLI::write("[NOTICE] Database '{$db->database}' is empty (0 tables).", 'yellow');
        CLI::write('         Beginning safe import from database/backups/real_estate_erp.sql...', 'cyan');

        $backupPath = ROOTPATH . 'database/backups/real_estate_erp.sql';
        if (!file_exists($backupPath)) {
            CLI::error("[-] SQL backup file not found at: {$backupPath}");
            return 1;
        }

        $sql = file_get_contents($backupPath);
        if (empty($sql)) {
            CLI::error('[-] SQL backup file is empty!');
            return 1;
        }

        $conn = $db->connID;
        $importFailed = false;
        $errorMessage = '';
        $statementIndex = 1;

        if ($conn instanceof \mysqli) {
            $conn->query('SET FOREIGN_KEY_CHECKS = 0;');

            try {
                if (!$conn->multi_query($sql)) {
                    $importFailed = true;
                    $errorMessage = "Statement #1 failed: " . $conn->error . " (Error code: " . $conn->errno . ")";
                } else {
                    do {
                        if ($result = $conn->store_result()) {
                            $result->free();
                        } elseif ($conn->errno) {
                            $importFailed = true;
                            $errorMessage = "Statement #{$statementIndex} failed: " . $conn->error . " (Error code: " . $conn->errno . ")";
                            break;
                        }

                        if (!$conn->more_results()) {
                            break;
                        }

                        $statementIndex++;

                        if (!$conn->next_result()) {
                            if ($conn->errno) {
                                $importFailed = true;
                                $errorMessage = "Statement #{$statementIndex} failed: " . $conn->error . " (Error code: " . $conn->errno . ")";
                            }
                            break;
                        }
                    } while (true);
                }
            } catch (\Throwable $e) {
                $importFailed = true;
                $errorMessage = "Exception on statement #{$statementIndex}: " . $e->getMessage();
            } finally {
                // Ensure foreign key checks are ALWAYS restored, even on error or crash
                $conn->query('SET FOREIGN_KEY_CHECKS = 1;');
            }
        } else {
            // Fallback for PDO driver
            try {
                $db->query('SET FOREIGN_KEY_CHECKS = 0;');
                $db->query($sql);
                $db->query('SET FOREIGN_KEY_CHECKS = 1;');
            } catch (\Throwable $e) {
                $importFailed = true;
                $errorMessage = "PDO Execution error: " . $e->getMessage();
            }
        }

        // Failure handling: never report success on a failed import
        if ($importFailed) {
            CLI::error("[-] SQL Import FAILED: {$errorMessage}");
            CLI::error("[-] Target database '{$db->database}' may be incomplete. Initialization aborted.");
            return 1;
        }

        $finalTables = $db->listTables();
        $finalCount = count($finalTables);

        // Verification check: ensure expected ERP tables were actually created
        if ($finalCount < 74) {
            CLI::error("[-] Verification FAILED: expected at least 74 tables, but only found {$finalCount}.");
            return 1;
        }

        CLI::write("[SUCCESS] Successfully initialized database '{$db->database}' with {$finalCount} tables!", 'green');
        return 0;
    }
}
