<?php
require_once __DIR__ . '/test_db_helper.php';
$pdo = getTestPdo();

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables (" . count($tables) . "):\n";
foreach ($tables as $t) {
    echo "- $t\n";
}
