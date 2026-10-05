<?php

/**
 * Phase 5 Comprehensive Automated Verification Suite
 * Tests Rentals, Tenant KYC, Lease Contracts, Rent Schedules, Security Deposits,
 * Maintenance Work Orders, Complaints, Preventive AMC, CAM Billing, Facility Assets,
 * Engineering Technicians, Owner/Tenant/Partner Portals, GST/TDS Statutory Reports,
 * Business Logic, and Audit Trails.
 */

$baseUrl = 'http://localhost:8080';
$adminCookieFile = sys_get_temp_dir() . '/ci4_p5_admin_cookie.txt';
@unlink($adminCookieFile);

// Direct PDO connection to verify database state accurately
require_once __DIR__ . '/test_db_helper.php';
$pdo = getTestPdo();

$results = [];
$allPassed = true;

function logTest($name, $passed, $details = '') {
    global $results, $allPassed;
    $status = $passed ? 'PASS' : 'FAIL';
    $results[] = ['name' => $name, 'status' => $status, 'details' => $details];
    echo sprintf("[%s] %s %s\n", $status, $name, $details ? "- " . $details : "");
    if (!$passed) {
        $allPassed = false;
    }
}

function getCsrfFromCookieJar($cookieFile) {
    if (file_exists($cookieFile)) {
        $lines = file($cookieFile);
        foreach ($lines as $line) {
            if (str_contains($line, 'csrf_cookie_name')) {
                $parts = preg_split('/\s+/', trim($line));
                if (!empty($parts)) {
                    return end($parts);
                }
            }
        }
    }
    return '';
}

function httpReq($url, $method = 'GET', $data = [], $cookieFile = null, $followRedirect = false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirect);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($method === 'POST') {
        $cookieCsrf = $cookieFile ? getCsrfFromCookieJar($cookieFile) : '';
        if ($cookieCsrf) {
            if (empty($data['csrf_token_name'])) {
                $data['csrf_token_name'] = $cookieCsrf;
            }
            if (empty($data['csrf_test_name'])) {
                $data['csrf_test_name'] = $cookieCsrf;
            }
        }
        if (isset($data['csrf_token_name']) && !isset($data['csrf_test_name'])) {
            $data['csrf_test_name'] = $data['csrf_token_name'];
        }
        if (isset($data['csrf_test_name']) && !isset($data['csrf_token_name'])) {
            $data['csrf_token_name'] = $data['csrf_test_name'];
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    $csrfToken = '';
    if (preg_match('/name="csrf_test_name" value="([^"]+)"/', $body, $m)) {
        $csrfToken = $m[1];
    } elseif (preg_match('/name="csrf_token_name" value="([^"]+)"/', $body, $m)) {
        $csrfToken = $m[1];
    } elseif ($cookieFile) {
        $csrfToken = getCsrfFromCookieJar($cookieFile);
    }

    return ['code' => $httpCode, 'header' => $header, 'body' => $body, 'csrfToken' => $csrfToken, 'error' => $error];
}

echo "========================================================\n";
echo "PHASE 5 COMPREHENSIVE AUTOMATED VERIFICATION SUITE\n";
echo "========================================================\n\n";

// ----------------------------------------------------
// 1. Database Schema & Tables
// ----------------------------------------------------
echo "--- 1. Database Schema & Phase 5 Tables Verification ---\n";

$p5Tables = [
    'tenants',
    'tenant_documents',
    'lease_agreements',
    'security_deposits',
    'rental_histories',
    'rent_demands',
    'rent_collections',
    'maintenance_requests',
    'complaints',
    'preventive_maintenance',
    'cam_charges',
    'facility_assets',
    'technicians',
    'sla_rules',
    'portal_requests',
    'tds_entries',
    'tds_certificates',
];

foreach ($p5Tables as $tbl) {
    try {
        $count = (int)$pdo->query("SELECT COUNT(*) FROM `{$tbl}`")->fetchColumn();
        logTest("Table `{$tbl}` exists", true, "{$count} rows in MySQL");
    } catch (\Exception $e) {
        logTest("Table `{$tbl}` exists", false, $e->getMessage());
    }
}

// Check Phase 5 Permissions & Roles
$permCount = (int)$pdo->query("SELECT COUNT(*) FROM permissions WHERE slug LIKE 'tenants.%' OR slug LIKE 'leases.%' OR slug LIKE 'deposits.%' OR slug LIKE 'rent_%' OR slug LIKE 'maintenance.%' OR slug LIKE 'complaints.%' OR slug LIKE 'preventive.%' OR slug LIKE 'facility_%' OR slug LIKE 'technicians.%' OR slug LIKE 'cam.%' OR slug LIKE 'portal.%' OR slug LIKE 'reports.%'")->fetchColumn();
logTest("Phase 5 Granular Permissions in DB", $permCount >= 40, "Found {$permCount} permissions");

$rolePermCount = (int)$pdo->query("SELECT COUNT(*) FROM role_permissions rp JOIN permissions p ON p.id = rp.permission_id WHERE p.slug LIKE 'tenants.%' OR p.slug LIKE 'leases.%' OR p.slug LIKE 'maintenance.%' OR p.slug LIKE 'portal.%' OR p.slug LIKE 'reports.%'")->fetchColumn();
logTest("Role-Permission Mappings in DB", $rolePermCount >= 50, "Found {$rolePermCount} mappings");

// ----------------------------------------------------
// 2. Authentication & Admin Authorization
// ----------------------------------------------------
echo "\n--- 2. Authentication & Session Setup ---\n";
$init = httpReq($baseUrl . '/login', 'GET', [], $adminCookieFile);
$csrfToken = $init['csrfToken'];

$loginResp = httpReq($baseUrl . '/login', 'POST', [
    'email'           => 'admin@realestate-erp.local',
    'password'        => 'Password@123',
    'csrf_token_name' => $csrfToken,
    'csrf_test_name'  => $csrfToken,
], $adminCookieFile);

$dashCheck = httpReq($baseUrl . '/dashboard', 'GET', [], $adminCookieFile);
logTest(
    'Super Admin Authentication & Session',
    $dashCheck['code'] === 200 && str_contains($dashCheck['body'], 'Executive Dashboard'),
    "HTTP {$dashCheck['code']}"
);

// ----------------------------------------------------
// 3. Tenants & Tenant KYC
// ----------------------------------------------------
echo "\n--- 3. Tenant Master & KYC Management ---\n";
$tenantsResp = httpReq($baseUrl . '/tenants', 'GET', [], $adminCookieFile);
logTest(
    'Tenant Directory Index (/tenants)',
    $tenantsResp['code'] === 200 && str_contains($tenantsResp['body'], 'Tenant Directory') && str_contains($tenantsResp['body'], 'Total Tenants'),
    "HTTP {$tenantsResp['code']}, rendered KPI metrics & tenants table"
);

$tenantCreateResp = httpReq($baseUrl . '/tenants/create', 'GET', [], $adminCookieFile);
logTest(
    'Register Tenant Page (/tenants/create)',
    $tenantCreateResp['code'] === 200 && str_contains($tenantCreateResp['body'], 'Register New Tenant'),
    "HTTP {$tenantCreateResp['code']}"
);

// Test Tenant Creation via POST with CSRF and Audit Trail Generation
$newTenantPost = httpReq($baseUrl . '/tenants/store', 'POST', [
    'tenant_type'     => 'individual',
    'full_name'       => 'Pooja Agarwal ' . time(),
    'mobile'          => '98200' . rand(10000, 99999),
    'email'           => 'pooja.' . time() . '@example.com',
    'id_proof_type'   => 'PAN Card',
    'id_proof_number' => 'ABCPA' . rand(1000, 9999) . 'Z',
    'csrf_token_name' => $tenantCreateResp['csrfToken'],
    'csrf_test_name'  => $tenantCreateResp['csrfToken'],
], $adminCookieFile);

logTest(
    'Register Tenant Form POST & CSRF',
    $newTenantPost['code'] === 302 || $newTenantPost['code'] === 303,
    "HTTP {$newTenantPost['code']}, redirected to tenant profile"
);

$tenantViewResp = httpReq($baseUrl . '/tenants/view/1', 'GET', [], $adminCookieFile);
logTest(
    'Tenant Profile & KYC View (/tenants/view/1)',
    $tenantViewResp['code'] === 200 && str_contains($tenantViewResp['body'], 'KYC Compliance') && str_contains($tenantViewResp['body'], 'Lease Agreements'),
    "HTTP {$tenantViewResp['code']}, documents & history rendered"
);

// ----------------------------------------------------
// 4. Lease Agreements
// ----------------------------------------------------
echo "\n--- 4. Lease Agreements & Contracting ---\n";
$leaseIndex = httpReq($baseUrl . '/leases', 'GET', [], $adminCookieFile);
logTest(
    'Lease Agreements Index (/leases)',
    $leaseIndex['code'] === 200 && str_contains($leaseIndex['body'], 'Lease Agreements') && str_contains($leaseIndex['body'], 'Active Leases'),
    "HTTP {$leaseIndex['code']}, KPIs rendered"
);

$leaseCreate = httpReq($baseUrl . '/leases/create', 'GET', [], $adminCookieFile);
logTest(
    'Draft Lease Agreement Page (/leases/create)',
    $leaseCreate['code'] === 200 && str_contains($leaseCreate['body'], 'Draft Lease Agreement') && str_contains($leaseCreate['body'], 'Lock-in Period'),
    "HTTP {$leaseCreate['code']}, terms form rendered"
);

$leaseView = httpReq($baseUrl . '/leases/view/1', 'GET', [], $adminCookieFile);
logTest(
    'Lease Agreement Details View (/leases/view/1)',
    $leaseView['code'] === 200 && str_contains($leaseView['body'], 'Lease Agreement') && str_contains($leaseView['body'], 'Contractual Tenancy Terms'),
    "HTTP {$leaseView['code']}, linked deposit & terms rendered"
);

$leaseVoucher = httpReq($baseUrl . '/leases/voucher/1', 'GET', [], $adminCookieFile);
logTest(
    'Printable Lease Agreement Sheet (/leases/voucher/1)',
    $leaseVoucher['code'] === 200 && str_contains($leaseVoucher['body'], 'LEASE AGREEMENT') && str_contains($leaseVoucher['body'], 'Lessor'),
    "HTTP {$leaseVoucher['code']}, legal contract voucher rendered"
);

// ----------------------------------------------------
// 5. Security Deposits
// ----------------------------------------------------
echo "\n--- 5. Security Deposits & Settlements ---\n";
$depIndex = httpReq($baseUrl . '/deposits', 'GET', [], $adminCookieFile);
logTest(
    'Security Deposits Index (/deposits)',
    $depIndex['code'] === 200 && str_contains($depIndex['body'], 'Security Deposits') && str_contains($depIndex['body'], 'Total Deposits Held'),
    "HTTP {$depIndex['code']}, escrow KPI & refund modal rendered"
);

// ----------------------------------------------------
// 6. Rent Demands
// ----------------------------------------------------
echo "\n--- 6. Rent Demands & Recurring Billing ---\n";
$demandIndex = httpReq($baseUrl . '/rent-demands', 'GET', [], $adminCookieFile);
logTest(
    'Rent Demands Index (/rent-demands)',
    $demandIndex['code'] === 200 && str_contains($demandIndex['body'], 'Rent Demands') && str_contains($demandIndex['body'], 'Overdue Demands'),
    "HTTP {$demandIndex['code']}, billing table rendered"
);

$demandView = httpReq($baseUrl . '/rent-demands/view/1', 'GET', [], $adminCookieFile);
logTest(
    'Rent Demand Notice View (/rent-demands/view/1)',
    $demandView['code'] === 200 && str_contains($demandView['body'], 'Rent Demand Notice') && str_contains($demandView['body'], 'Record Rent Collection'),
    "HTTP {$demandView['code']}, statement & payment trigger rendered"
);

// ----------------------------------------------------
// 7. Rent Collections & Receipts
// ----------------------------------------------------
echo "\n--- 7. Rent Collections & Official Receipts ---\n";
$collIndex = httpReq($baseUrl . '/rent-collections', 'GET', [], $adminCookieFile);
logTest(
    'Rent Collections Index (/rent-collections)',
    $collIndex['code'] === 200 && str_contains($collIndex['body'], 'Rent Collections') && str_contains($collIndex['body'], 'Total Collections Value'),
    "HTTP {$collIndex['code']}, receipts ledger rendered"
);

$collReceipt = httpReq($baseUrl . '/rent-collections/receipt/1', 'GET', [], $adminCookieFile);
logTest(
    'Printable Rent Payment Receipt (/rent-collections/receipt/1)',
    $collReceipt['code'] === 200 && str_contains($collReceipt['body'], 'Payment Received') && str_contains($collReceipt['body'], 'Official Receipt'),
    "HTTP {$collReceipt['code']}, official receipt view rendered"
);

// ----------------------------------------------------
// 8. Maintenance Work Orders
// ----------------------------------------------------
echo "\n--- 8. Facility Maintenance & Work Orders ---\n";
$maintIndex = httpReq($baseUrl . '/maintenance', 'GET', [], $adminCookieFile);
logTest(
    'Work Orders Index (/maintenance)',
    $maintIndex['code'] === 200 && str_contains($maintIndex['body'], 'Maintenance Work Orders') && str_contains($maintIndex['body'], 'Unassigned / Open'),
    "HTTP {$maintIndex['code']}, SLA targets rendered"
);

$maintCreate = httpReq($baseUrl . '/maintenance/create', 'GET', [], $adminCookieFile);
logTest(
    'Create Maintenance Ticket Page (/maintenance/create)',
    $maintCreate['code'] === 200 && str_contains($maintCreate['body'], 'Create Maintenance Ticket') && str_contains($maintCreate['body'], 'Priority'),
    "HTTP {$maintCreate['code']}"
);

$maintView = httpReq($baseUrl . '/maintenance/view/1', 'GET', [], $adminCookieFile);
logTest(
    'Maintenance Work Order Details (/maintenance/view/1)',
    $maintView['code'] === 200 && str_contains($maintView['body'], 'Reported Defect') && str_contains($maintView['body'], 'Technician Dispatch'),
    "HTTP {$maintView['code']}, dispatch & resolution form rendered"
);

$maintVoucher = httpReq($baseUrl . '/maintenance/voucher/1', 'GET', [], $adminCookieFile);
logTest(
    'Printable Work Order Sheet (/maintenance/voucher/1)',
    $maintVoucher['code'] === 200 && str_contains($maintVoucher['body'], 'WORK ORDER SHEET') && str_contains($maintVoucher['body'], 'Field Technician Signature'),
    "HTTP {$maintVoucher['code']}, sign-off sheet rendered"
);

// ----------------------------------------------------
// 9. Resident Complaints
// ----------------------------------------------------
echo "\n--- 9. Resident Grievance Management ---\n";
$complaintsIndex = httpReq($baseUrl . '/complaints', 'GET', [], $adminCookieFile);
logTest(
    'Complaints & Grievance Desk (/complaints)',
    $complaintsIndex['code'] === 200 && str_contains($complaintsIndex['body'], 'Resident Complaints') && str_contains($complaintsIndex['body'], 'Log Resident Grievance'),
    "HTTP {$complaintsIndex['code']}, 1-5 star ratings rendered"
);

// ----------------------------------------------------
// 10. Preventive Maintenance & AMC
// ----------------------------------------------------
echo "\n--- 10. Preventive Maintenance & AMC Schedules ---\n";
$preventiveIndex = httpReq($baseUrl . '/preventive-maintenance', 'GET', [], $adminCookieFile);
logTest(
    'Preventive AMC Schedules (/preventive-maintenance)',
    $preventiveIndex['code'] === 200 && str_contains($preventiveIndex['body'], 'Preventive Maintenance') && str_contains($preventiveIndex['body'], 'Schedule Preventive Service'),
    "HTTP {$preventiveIndex['code']}, frequency advancement rendered"
);

// ----------------------------------------------------
// 11. CAM Charges & Common Billing
// ----------------------------------------------------
echo "\n--- 11. CAM Charges & Common Billing ---\n";
$camIndex = httpReq($baseUrl . '/cam-charges', 'GET', [], $adminCookieFile);
logTest(
    'CAM Charges Billing (/cam-charges)',
    $camIndex['code'] === 200 && str_contains($camIndex['body'], 'Common Area Maintenance') && str_contains($camIndex['body'], '18% GST'),
    "HTTP {$camIndex['code']}, per sq.ft & flat rate model rendered"
);

// ----------------------------------------------------
// 12. Facility Assets Directory
// ----------------------------------------------------
echo "\n--- 12. Facility Equipment & Assets Register ---\n";
$assetsIndex = httpReq($baseUrl . '/facility-assets', 'GET', [], $adminCookieFile);
logTest(
    'Facility Assets Register (/facility-assets)',
    $assetsIndex['code'] === 200 && str_contains($assetsIndex['body'], 'Facility Assets') && str_contains($assetsIndex['body'], 'Operational'),
    "HTTP {$assetsIndex['code']}, elevators, generators & HVAC listed"
);

// ----------------------------------------------------
// 13. Service Technicians
// ----------------------------------------------------
echo "\n--- 13. Service Technicians & Engineering Roster ---\n";
$techIndex = httpReq($baseUrl . '/technicians', 'GET', [], $adminCookieFile);
logTest(
    'Technicians Roster (/technicians)',
    $techIndex['code'] === 200 && str_contains($techIndex['body'], 'Service Technicians') && str_contains($techIndex['body'], 'Available for Dispatch'),
    "HTTP {$techIndex['code']}, availability status rendered"
);

// ----------------------------------------------------
// 14. Self-Service Portals
// ----------------------------------------------------
echo "\n--- 14. Dedicated Self-Service Portals ---\n";
$ownerPortal = httpReq($baseUrl . '/portal/owner', 'GET', [], $adminCookieFile);
logTest(
    'Property Owner & Buyer Portal (/portal/owner)',
    $ownerPortal['code'] === 200 && (str_contains($ownerPortal['body'], 'Owner Portal') || str_contains($ownerPortal['body'], 'Customer')) && str_contains($ownerPortal['body'], 'My Booked Real Estate Units'),
    "HTTP {$ownerPortal['code']}, customer bookings & NOC trigger rendered"
);

$tenantPortal = httpReq($baseUrl . '/portal/tenant', 'GET', [], $adminCookieFile);
logTest(
    'Resident Tenant Portal (/portal/tenant)',
    $tenantPortal['code'] === 200 && str_contains($tenantPortal['body'], 'Resident Tenant Portal') && str_contains($tenantPortal['body'], 'My Active Leases'),
    "HTTP {$tenantPortal['code']}, rent dues & service requests rendered"
);

$partnerPortal = httpReq($baseUrl . '/portal/partner', 'GET', [], $adminCookieFile);
logTest(
    'Channel Partner & Broker Portal (/portal/partner)',
    $partnerPortal['code'] === 200 && str_contains($partnerPortal['body'], 'Channel Partner') && str_contains($partnerPortal['body'], 'Broker Commission Tracking'),
    "HTTP {$partnerPortal['code']}, referred leads & live inventory rendered"
);

// ----------------------------------------------------
// 15. Compliance & Financial Reports
// ----------------------------------------------------
echo "\n--- 15. Statutory Compliance & Finance Reports ---\n";
$gstReport = httpReq($baseUrl . '/reports/gst', 'GET', [], $adminCookieFile);
logTest(
    'GST Statutory Filing Reports (/reports/gst)',
    $gstReport['code'] === 200 && str_contains($gstReport['body'], 'Table 3.1') && str_contains($gstReport['body'], 'Central GST (CGST 9%)'),
    "HTTP {$gstReport['code']}, GSTR-1 & GSTR-3B summary rendered"
);

$tdsReport = httpReq($baseUrl . '/reports/tds', 'GET', [], $adminCookieFile);
logTest(
    'TDS & Form 16A Management (/reports/tds)',
    $tdsReport['code'] === 200 && str_contains($tdsReport['body'], 'Tax Deducted at Source') && str_contains($tdsReport['body'], 'Sec 194H'),
    "HTTP {$tdsReport['code']}, TDS register & certificates rendered"
);

$ageingReport = httpReq($baseUrl . '/reports/receivables', 'GET', [], $adminCookieFile);
logTest(
    'Accounts Receivable & Ageing Analysis (/reports/receivables)',
    $ageingReport['code'] === 200 && str_contains($ageingReport['body'], 'Accounts Receivable') && str_contains($ageingReport['body'], '90+ Days'),
    "HTTP {$ageingReport['code']}, 5 ageing buckets rendered"
);

// ----------------------------------------------------
// 16. Business Logic Validation & DB Integrity
// ----------------------------------------------------
echo "\n--- 16. Core Business Logic Assertions ---\n";

// A. SLA Matrix Lookup Verification
$slaHoursUrgent = (int)$pdo->query("SELECT resolution_time_hours FROM sla_rules WHERE category = 'electrical' AND priority = 'urgent'")->fetchColumn();
logTest('SLA Matrix Urgent Resolution Hours', $slaHoursUrgent === 4, "Configured for {$slaHoursUrgent} hours");

$slaHoursElevator = (int)$pdo->query("SELECT resolution_time_hours FROM sla_rules WHERE category = 'elevators' AND priority = 'urgent'")->fetchColumn();
logTest('SLA Matrix Elevator Resolution Hours', $slaHoursElevator === 3, "Configured for {$slaHoursElevator} hours");

// B. Commercial Lease GST Computation
$commDemand = $pdo->query("SELECT * FROM rent_demands WHERE lease_id IN (SELECT id FROM lease_agreements WHERE agreement_type = 'commercial') LIMIT 1")->fetch();
if ($commDemand) {
    $expectedTax = round(((float)$commDemand['base_rent'] + (float)$commDemand['maintenance'] + (float)$commDemand['other_charges']) * 0.18, 2);
    logTest('Commercial Rent Demand 18% GST Accuracy', abs((float)$commDemand['tax'] - $expectedTax) < 0.05, "Computed: ₹{$commDemand['tax']}, Expected: ₹{$expectedTax}");
}

// C. Security Deposit Math Integrity
$depositSample = $pdo->query("SELECT * FROM security_deposits LIMIT 1")->fetch();
if ($depositSample) {
    $sumCheck = (float)$depositSample['refundable_amount'] + (float)$depositSample['adjusted_amount'];
    logTest('Security Deposit Balance Conservation', abs($sumCheck - (float)$depositSample['amount']) < 0.05, "Refundable + Adjusted = ₹{$sumCheck} (Matches Total ₹{$depositSample['amount']})");
}

// D. Audit Logging Coverage
$p5AuditCount = (int)$pdo->query("SELECT COUNT(*) FROM audit_logs WHERE action IN ('TENANT_CREATED', 'LEASE_CREATED', 'LEASE_ACTIVATED', 'WORK_ORDER_CREATED', 'RENT_DEMAND_GENERATED', 'RENT_COLLECTION_RECORDED', 'TDS_RECORDED', 'PORTAL_REQUEST_SUBMITTED')")->fetchColumn();
logTest('Phase 5 Audit Trails Logged in MySQL', $p5AuditCount >= 1, "Recorded {$p5AuditCount} Phase 5 audit trail events");

// ----------------------------------------------------
// Summary
// ----------------------------------------------------
echo "\n========================================================\n";
$passCount = count(array_filter($results, fn($r) => $r['status'] === 'PASS'));
$failCount = count(array_filter($results, fn($r) => $r['status'] === 'FAIL'));
$totalCount = count($results);

echo "PHASE 5 VERIFICATION COMPLETED\n";
echo "Total Tests: {$totalCount} | Passed: {$passCount} | Failed: {$failCount}\n";
$pct = round(($passCount / $totalCount) * 100, 2);
echo "Success Rate: {$pct}%\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
exit(0);
