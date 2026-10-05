<?php
require_once __DIR__ . '/tests/test_db_helper.php';
$pdo = getTestPdo();
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo "TOTAL TABLES IN real_estate_erp: " . count($tables) . "\n";
foreach ($tables as $t) {
    echo "- " . $t . "\n";
}

echo "\nMIGRATIONS TABLE STATUS:\n";
$migrations = $pdo->query('SELECT version, class, `group`, time, batch FROM migrations ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
foreach ($migrations as $m) {
    echo sprintf("[%d] %s (%s)\n", $m['batch'], $m['version'], $m['class']);
}
