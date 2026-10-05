<?php
require_once __DIR__ . '/test_db_helper.php';
$pdo = getTestPdo();
$pdo->exec("UPDATE unit_holds SET hold_status = 'Released', released_at = NOW(), remarks = 'Pre-test reset' WHERE property_unit_id = 9 AND hold_status = 'Active'");
$pdo->exec("UPDATE property_units SET availability_status = 'Available' WHERE id = 9");
$pdo->exec("UPDATE bookings SET booking_status = 'Cancelled', remarks = 'Pre-test reset' WHERE property_unit_id = 5 AND booking_status IN ('Draft', 'Pending Confirmation', 'Confirmed')");
$pdo->exec("UPDATE property_units SET availability_status = 'Available' WHERE id = 5");
echo "Reset test state for unit 9 and unit 5 successfully.\n";
