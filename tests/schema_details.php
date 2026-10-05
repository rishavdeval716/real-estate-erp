<?php
require_once __DIR__ . '/test_db_helper.php';
$pdo = getTestPdo();

$tablesToCheck = ['properties', 'leads', 'commissions', 'portal_requests', 'users'];
foreach ($tablesToCheck as $tbl) {
    echo "=== Table: $tbl ===\n";
    $cols = $pdo->query("DESCRIBE $tbl")->fetchAll();
    foreach ($cols as $c) {
        echo "  {$c['Field']} ({$c['Type']})\n";
    }
}
