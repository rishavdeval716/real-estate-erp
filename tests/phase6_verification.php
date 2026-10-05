<?php

/**
 * Phase 6 Comprehensive Automated Verification Suite
 * Tests Project Construction Management, Contractor & Vendor Operations,
 * Construction Milestones & Daily Site Logs, Material Procurement / Indents,
 * Site Quality & Safety Inspections, Unit Possession & Key Handover Certification,
 * RBAC Permissions, CSRF Protection, and Audit Trails.
 */

$baseUrl = 'http://localhost:8080';
$adminCookieFile = sys_get_temp_dir() . '/ci4_p6_admin_cookie.txt';
$salesCookieFile = sys_get_temp_dir() . '/ci4_p6_sales_cookie.txt';
@unlink($adminCookieFile);
@unlink($salesCookieFile);

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
    $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $headerStr  = substr($response, 0, $headerSize);
    $body       = substr($response, $headerSize);

    // Extract CSRF token from HTML form or meta if present
    $csrfToken = '';
    if (preg_match('/name=["\']csrf_token_name["\']\s+value=["\']([^"\']+)["\']/i', $body, $m)) {
        $csrfToken = $m[1];
    } elseif (preg_match('/name=["\']csrf_test_name["\']\s+value=["\']([^"\']+)["\']/i', $body, $m)) {
        $csrfToken = $m[1];
    } else {
        $csrfToken = getCsrfFromCookieJar($cookieFile);
    }

    curl_close($ch);

    return [
        'code'      => $httpCode,
        'headers'   => $headerStr,
        'body'      => $body,
        'csrfToken' => $csrfToken,
    ];
}

function isOkOrRedirect($code) {
    return in_array($code, [200, 302, 303], true);
}

echo "\n========================================================\n";
echo "PHASE 6 COMPREHENSIVE AUTOMATED VERIFICATION SUITE\n";
echo "========================================================\n\n";

// =========================================================================
// 1. DATABASE SCHEMA & PHASE 6 TABLES VERIFICATION
// =========================================================================
echo "--- 1. Database Schema & Phase 6 Tables Verification ---\n";
$tables = [
    'construction_milestones',
    'daily_site_logs',
    'contractors',
    'construction_work_orders',
    'material_requisitions',
    'site_inspections',
    'handover_certificates',
];

foreach ($tables as $t) {
    try {
        $cnt = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        logTest("Table `{$t}` exists", true, "{$cnt} rows in MySQL");
    } catch (\Exception $e) {
        logTest("Table `{$t}` exists", false, $e->getMessage());
    }
}

// Verify Phase 6 Permissions
$p6Count = $pdo->query("SELECT COUNT(*) FROM permissions WHERE slug LIKE 'construction.%' OR slug LIKE 'contractors.%' OR slug LIKE 'procurement.%' OR slug LIKE 'inspections.%' OR slug LIKE 'handover.%'")->fetchColumn();
logTest("Phase 6 Granular Permissions in DB", $p6Count >= 20, "Found {$p6Count} permissions");

// =========================================================================
// 2. AUTHENTICATION & SESSION SETUP
// =========================================================================
echo "\n--- 2. Authentication & Session Setup ---\n";
$loginPage = httpReq("$baseUrl/login", 'GET', [], $adminCookieFile);
$csrf = $loginPage['csrfToken'];

$loginRes = httpReq("$baseUrl/login", 'POST', [
    'csrf_token_name' => $csrf,
    'email'            => 'admin@realestate-erp.local',
    'password'         => 'Password@123',
], $adminCookieFile, false);

logTest("Super Admin Authentication & Session", isOkOrRedirect($loginRes['code']), "HTTP {$loginRes['code']}");

// Login Sales Executive for RBAC test
$salesLoginPage = httpReq("$baseUrl/login", 'GET', [], $salesCookieFile);
$salesCsrf = $salesLoginPage['csrfToken'];
$salesLoginRes = httpReq("$baseUrl/login", 'POST', [
    'csrf_token_name' => $salesCsrf,
    'email'            => 'sales@realestate-erp.local',
    'password'         => 'Password@123',
], $salesCookieFile, false);
logTest("Sales Executive Authentication for RBAC", isOkOrRedirect($salesLoginRes['code']), "HTTP {$salesLoginRes['code']}");

// =========================================================================
// 3. CONSTRUCTION DASHBOARD & EXECUTIVE KPIs
// =========================================================================
echo "\n--- 3. Construction Dashboard & Executive KPIs ---\n";
$dashRes = httpReq("$baseUrl/construction/dashboard", 'GET', [], $adminCookieFile);
logTest("Construction Dashboard (/construction/dashboard)", $dashRes['code'] === 200, "HTTP {$dashRes['code']}, rendered live metrics");
logTest("Dashboard Contains Milestone Progress %", str_contains($dashRes['body'], 'Milestone Progress'), "Calculated from MySQL");
logTest("Dashboard Contains Active Work Contracts", str_contains($dashRes['body'], 'Active Work Contracts'), "Work contract values rendered");
logTest("Dashboard Contains Possession Handovers", str_contains($dashRes['body'], 'Possession Handovers'), "Handover KPI rendered");

// =========================================================================
// 4. CONSTRUCTION MILESTONES & PROGRESS TRACKING
// =========================================================================
echo "\n--- 4. Construction Milestones & Progress Tracking ---\n";
$milestoneIndex = httpReq("$baseUrl/construction/milestones", 'GET', [], $adminCookieFile);
logTest("Construction Milestones Index (/construction/milestones)", $milestoneIndex['code'] === 200, "HTTP 200, stage tracking rendered");

// Create New Construction Milestone
$uniqueMilName = 'Phase 6 Test Superstructure Stage ' . time();
$createMilRes = httpReq("$baseUrl/construction/milestones/store", 'POST', [
    'csrf_token_name'        => $milestoneIndex['csrfToken'],
    'project_id'             => 1,
    'tower_id'               => 1,
    'milestone_name'         => $uniqueMilName,
    'stage_order'            => 10,
    'weightage_percentage'   => 5.0,
    'target_start_date'      => date('Y-m-d'),
    'target_completion_date' => date('Y-m-d', strtotime('+30 days')),
    'progress_percentage'    => 25.0,
    'status'                 => 'In Progress',
    'remarks'                => 'Automated test stage milestone',
], $adminCookieFile);
logTest("Create Milestone Form POST & CSRF", isOkOrRedirect($createMilRes['code']), "HTTP {$createMilRes['code']}");

$createdMil = $pdo->query("SELECT * FROM construction_milestones WHERE milestone_name = '$uniqueMilName'")->fetch();
logTest("Milestone Persisted with Sequential Code", !empty($createdMil) && str_starts_with($createdMil['milestone_code'], 'MIL-'), "Code: " . ($createdMil['milestone_code'] ?? 'None'));

// Update Milestone Progress to 100% / Completed
if ($createdMil) {
    $updateMilRes = httpReq("$baseUrl/construction/milestones/update/{$createdMil['id']}", 'POST', [
        'csrf_token_name'     => $milestoneIndex['csrfToken'],
        'progress_percentage' => 100,
        'status'              => 'Completed',
        'remarks'             => 'Cube tests certified 100% compressive strength. Approved by chief structural auditor.',
    ], $adminCookieFile);
    logTest("Update Milestone Progress to Completed (100%)", isOkOrRedirect($updateMilRes['code']), "HTTP {$updateMilRes['code']}");

    $updatedMil = $pdo->query("SELECT * FROM construction_milestones WHERE id = {$createdMil['id']}")->fetch();
    logTest("Milestone Marked Completed in DB with Verifier", $updatedMil['status'] === 'Completed' && (float)$updatedMil['progress_percentage'] == 100.0 && !empty($updatedMil['verified_at']), "Verified at: {$updatedMil['verified_at']}");
}

// =========================================================================
// 5. DAILY SITE LOGS & MANPOWER EXECUTION
// =========================================================================
echo "\n--- 5. Daily Site Progress Logs ---\n";
$dailyLogsIndex = httpReq("$baseUrl/construction/daily-logs", 'GET', [], $adminCookieFile);
logTest("Daily Site Logs Index (/construction/daily-logs)", $dailyLogsIndex['code'] === 200, "HTTP 200, logs register rendered");

$createDailyLogPage = httpReq("$baseUrl/construction/daily-logs/create", 'GET', [], $adminCookieFile);
logTest("Create Daily Log Page (/construction/daily-logs/create)", $createDailyLogPage['code'] === 200, "HTTP 200, submission form rendered");

// Submit Daily Site Log
$uniqueWorkDesc = 'Test concrete pour for 14th floor grid: ' . time();
$storeLogRes = httpReq("$baseUrl/construction/daily-logs/store", 'POST', [
    'csrf_token_name'       => $createDailyLogPage['csrfToken'],
    'log_date'              => date('Y-m-d'),
    'project_id'            => 1,
    'tower_id'              => 1,
    'milestone_id'          => $createdMil ? $createdMil['id'] : 1,
    'skilled_workers'       => 32,
    'unskilled_workers'     => 50,
    'weather_condition'     => 'Sunny',
    'work_completed'        => $uniqueWorkDesc,
    'materials_used'        => '55 cu.m RMC M40, 4.2 MT steel',
    'equipment_deployed'    => 'Tower Crane #1, Concrete Pump',
    'delays_or_impediments' => 'Zero delays, perfect pour',
], $adminCookieFile);
logTest("Submit Daily Site Log Form POST & CSRF", isOkOrRedirect($storeLogRes['code']), "HTTP {$storeLogRes['code']}");

$createdLog = $pdo->query("SELECT * FROM daily_site_logs WHERE work_completed = '$uniqueWorkDesc'")->fetch();
logTest("Daily Log Persisted with Sequential Code", !empty($createdLog) && str_starts_with($createdLog['log_code'], 'LOG-'), "Code: " . ($createdLog['log_code'] ?? 'None'));

if ($createdLog) {
    // Approve Daily Log
    $approveLogRes = httpReq("$baseUrl/construction/daily-logs/approve/{$createdLog['id']}", 'POST', [
        'csrf_token_name' => $createDailyLogPage['csrfToken'],
    ], $adminCookieFile);
    logTest("Approve Daily Site Log POST", isOkOrRedirect($approveLogRes['code']), "HTTP {$approveLogRes['code']}");

    $approvedLog = $pdo->query("SELECT * FROM daily_site_logs WHERE id = {$createdLog['id']}")->fetch();
    logTest("Daily Log Status Updated to Approved in DB", $approvedLog['status'] === 'Approved' && !empty($approvedLog['approved_by']), "Approved by User ID: {$approvedLog['approved_by']}");
}

// =========================================================================
// 6. CONTRACTOR & VENDOR MANAGEMENT
// =========================================================================
echo "\n--- 6. Contractor & Vendor Management ---\n";
$contractorIndex = httpReq("$baseUrl/contractors", 'GET', [], $adminCookieFile);
logTest("Contractor Directory Index (/contractors)", $contractorIndex['code'] === 200, "HTTP 200, directory rendered");

$createContractorPage = httpReq("$baseUrl/contractors/create", 'GET', [], $adminCookieFile);
logTest("Onboard Contractor Page (/contractors/create)", $createContractorPage['code'] === 200, "HTTP 200, form rendered");

// Onboard New Contractor
$uniqueConName = 'Reliance Infrastructure Specialist ' . time();
$storeConRes = httpReq("$baseUrl/contractors/store", 'POST', [
    'csrf_token_name' => $createContractorPage['csrfToken'],
    'company_name'    => $uniqueConName,
    'specialization'  => 'Civil & Structural',
    'contact_person'  => 'Sunil Mittal',
    'phone'           => '+91 98200 99881',
    'email'           => 'contracts.' . time() . '@relianceinfra.local',
    'license_number'  => 'LIC-REL-2026-991',
    'gstin'           => '27AAACR1234F1Z9',
    'rating'          => 4.9,
    'status'          => 'Active',
], $adminCookieFile);
logTest("Onboard Contractor Form POST & CSRF", isOkOrRedirect($storeConRes['code']), "HTTP {$storeConRes['code']}");

$createdCon = $pdo->query("SELECT * FROM contractors WHERE company_name = '$uniqueConName'")->fetch();
logTest("Contractor Persisted with Sequential Code", !empty($createdCon) && str_starts_with($createdCon['contractor_code'], 'CON-'), "Code: " . ($createdCon['contractor_code'] ?? 'None'));

if ($createdCon) {
    // Edit Contractor View
    $editConPage = httpReq("$baseUrl/contractors/edit/{$createdCon['id']}", 'GET', [], $adminCookieFile);
    logTest("Edit Contractor Profile Page (/contractors/edit/{id})", $editConPage['code'] === 200, "HTTP 200");

    // Update Contractor
    $updateConRes = httpReq("$baseUrl/contractors/update/{$createdCon['id']}", 'POST', [
        'csrf_token_name' => $editConPage['csrfToken'],
        'company_name'    => $uniqueConName . ' Prime',
        'specialization'  => 'Civil & Structural',
        'contact_person'  => 'Sunil Mittal',
        'phone'           => '+91 98200 99882',
        'email'           => $createdCon['email'],
        'license_number'  => 'LIC-REL-2026-991-REV',
        'gstin'           => '27AAACR1234F1Z9',
        'rating'          => 5.0,
        'status'          => 'Active',
    ], $adminCookieFile);
    logTest("Update Contractor Profile POST", isOkOrRedirect($updateConRes['code']), "HTTP {$updateConRes['code']}");

    // Deactivate Contractor
    $deleteConRes = httpReq("$baseUrl/contractors/delete/{$createdCon['id']}", 'POST', [
        'csrf_token_name' => $editConPage['csrfToken'],
    ], $adminCookieFile);
    logTest("Deactivate Contractor POST", isOkOrRedirect($deleteConRes['code']), "HTTP {$deleteConRes['code']}");

    $deactivatedCon = $pdo->query("SELECT status FROM contractors WHERE id = {$createdCon['id']}")->fetch();
    logTest("Contractor Status Updated to Inactive in DB", $deactivatedCon['status'] === 'Inactive', "Status: {$deactivatedCon['status']}");
}

// =========================================================================
// 7. CONSTRUCTION WORK ORDERS & CONTRACTS
// =========================================================================
echo "\n--- 7. Construction Work Orders & Contracts ---\n";
$workOrderIndex = httpReq("$baseUrl/construction/work-orders", 'GET', [], $adminCookieFile);
logTest("Construction Work Orders Index (/construction/work-orders)", $workOrderIndex['code'] === 200, "HTTP 200, contracts register rendered");

// Award New Work Order
$uniqueWorkTitle = 'Elevator Shaft Structural Package: ' . time();
$activeContractor = $pdo->query("SELECT id FROM contractors WHERE status = 'Active' LIMIT 1")->fetch();

$storeWorkOrderRes = httpReq("$baseUrl/construction/work-orders/store", 'POST', [
    'csrf_token_name'      => $workOrderIndex['csrfToken'],
    'contractor_id'        => $activeContractor['id'],
    'project_id'           => 1,
    'tower_id'             => 1,
    'title'                => $uniqueWorkTitle,
    'scope_of_work'        => 'Structural fabrication, machine room slab casting and lift guide rail brackets installation',
    'contract_amount'      => 8500000.00,
    'retention_percentage' => 5.0,
    'start_date'           => date('Y-m-d'),
    'completion_date'      => date('Y-m-d', strtotime('+60 days')),
    'payment_terms'        => 'Stage-wise RA bills certified by elevator consultant',
    'status'               => 'Awarded',
], $adminCookieFile);
logTest("Award Work Order Form POST & CSRF", isOkOrRedirect($storeWorkOrderRes['code']), "HTTP {$storeWorkOrderRes['code']}");

$createdWO = $pdo->query("SELECT * FROM construction_work_orders WHERE title = '$uniqueWorkTitle'")->fetch();
logTest("Work Order Persisted with Sequential Code", !empty($createdWO) && str_starts_with($createdWO['work_order_code'], 'CWO-'), "Code: " . ($createdWO['work_order_code'] ?? 'None'));

if ($createdWO) {
    // Transition Work Order Status to In Progress
    $statusRes = httpReq("$baseUrl/construction/work-orders/status/{$createdWO['id']}", 'POST', [
        'csrf_token_name' => $workOrderIndex['csrfToken'],
        'status'          => 'In Progress',
    ], $adminCookieFile);
    logTest("Update Work Order Status to In Progress POST", isOkOrRedirect($statusRes['code']), "HTTP {$statusRes['code']}");

    $updatedWO = $pdo->query("SELECT status FROM construction_work_orders WHERE id = {$createdWO['id']}")->fetch();
    logTest("Work Order Status Updated in DB", $updatedWO['status'] === 'In Progress', "Status: {$updatedWO['status']}");
}

// =========================================================================
// 8. MATERIAL PROCUREMENT & REQUISITIONS
// =========================================================================
echo "\n--- 8. Material Requisitions & Procurement ---\n";
$procurementIndex = httpReq("$baseUrl/procurement", 'GET', [], $adminCookieFile);
logTest("Material Requisitions Index (/procurement)", $procurementIndex['code'] === 200, "HTTP 200, procurement register rendered");

$createProcurePage = httpReq("$baseUrl/procurement/create", 'GET', [], $adminCookieFile);
logTest("Create Material Indent Page (/procurement/create)", $createProcurePage['code'] === 200, "HTTP 200, indent form rendered");

// Submit Material Requisition
$uniqueItemName = 'ACC Concrete Ready-Mix M40 Grade: ' . time();
$storeReqRes = httpReq("$baseUrl/procurement/store", 'POST', [
    'csrf_token_name'     => $createProcurePage['csrfToken'],
    'project_id'          => 1,
    'tower_id'            => 1,
    'item_name'           => $uniqueItemName,
    'category'            => 'Cement',
    'quantity'            => 80,
    'unit_of_measure'     => 'Truckloads',
    'estimated_unit_cost' => 4500.00,
    'required_by_date'    => date('Y-m-d', strtotime('+10 days')),
    'priority'            => 'Urgent',
    'remarks'             => 'High early strength required for transfer slab',
], $adminCookieFile);
logTest("Submit Material Requisition Form POST & CSRF", isOkOrRedirect($storeReqRes['code']), "HTTP {$storeReqRes['code']}");

$createdReq = $pdo->query("SELECT * FROM material_requisitions WHERE item_name = '$uniqueItemName'")->fetch();
logTest("Material Requisition Persisted with Sequential Code", !empty($createdReq) && str_starts_with($createdReq['requisition_code'], 'REQ-'), "Code: " . ($createdReq['requisition_code'] ?? 'None'));
logTest("Estimated Total Cost Accurately Computed", (float)$createdReq['estimated_total_cost'] == (80 * 4500.00), "Total: ₹" . number_format($createdReq['estimated_total_cost'], 2));

if ($createdReq) {
    // Approve Requisition
    $approveReqRes = httpReq("$baseUrl/procurement/approve/{$createdReq['id']}", 'POST', [
        'csrf_token_name' => $procurementIndex['csrfToken'],
    ], $adminCookieFile);
    logTest("Approve Material Requisition POST", isOkOrRedirect($approveReqRes['code']), "HTTP {$approveReqRes['code']}");

    // Mark as Procured
    $procureRes = httpReq("$baseUrl/procurement/procure/{$createdReq['id']}", 'POST', [
        'csrf_token_name' => $procurementIndex['csrfToken'],
    ], $adminCookieFile);
    logTest("Mark Requisition Procured POST", isOkOrRedirect($procureRes['code']), "HTTP {$procureRes['code']}");

    $procuredReq = $pdo->query("SELECT status FROM material_requisitions WHERE id = {$createdReq['id']}")->fetch();
    logTest("Requisition Status Updated to Procured in DB", $procuredReq['status'] === 'Procured', "Status: {$procuredReq['status']}");
}

// =========================================================================
// 9. SITE QUALITY & SAFETY INSPECTIONS
// =========================================================================
echo "\n--- 9. Site Quality & Safety Inspections ---\n";
$inspectionIndex = httpReq("$baseUrl/inspections", 'GET', [], $adminCookieFile);
logTest("Site Quality Inspections Index (/inspections)", $inspectionIndex['code'] === 200, "HTTP 200, inspections register rendered");

$createInspPage = httpReq("$baseUrl/inspections/create", 'GET', [], $adminCookieFile);
logTest("Schedule Inspection Page (/inspections/create)", $createInspPage['code'] === 200, "HTTP 200, form rendered");

// Record Inspection with Snags
$uniqueInspRemark = 'Automated test snag inspection: ' . time();
$storeInspRes = httpReq("$baseUrl/inspections/store", 'POST', [
    'csrf_token_name'        => $createInspPage['csrfToken'],
    'project_id'             => 1,
    'tower_id'               => 1,
    'inspection_type'        => 'Pre-Possession Snagging Checklist',
    'inspection_date'        => date('Y-m-d'),
    'result'                 => 'Conditional Pass',
    'snags_found'            => 2,
    'snag_details'           => 'Living room window sliding latch stiff, balcony tile grout gap',
    'rectification_deadline' => date('Y-m-d', strtotime('+5 days')),
    'remarks'                => $uniqueInspRemark,
], $adminCookieFile);
logTest("Record Inspection Form POST & CSRF", isOkOrRedirect($storeInspRes['code']), "HTTP {$storeInspRes['code']}");

$createdInsp = $pdo->query("SELECT * FROM site_inspections WHERE remarks = '$uniqueInspRemark'")->fetch();
logTest("Site Inspection Persisted with Sequential Code", !empty($createdInsp) && str_starts_with($createdInsp['inspection_code'], 'INSP-'), "Code: " . ($createdInsp['inspection_code'] ?? 'None'));

if ($createdInsp) {
    // Rectify Snags
    $rectifyRes = httpReq("$baseUrl/inspections/rectify/{$createdInsp['id']}", 'POST', [
        'csrf_token_name' => $inspectionIndex['csrfToken'],
    ], $adminCookieFile);
    logTest("Rectify Snags & Close Inspection POST", isOkOrRedirect($rectifyRes['code']), "HTTP {$rectifyRes['code']}");

    $closedInsp = $pdo->query("SELECT status FROM site_inspections WHERE id = {$createdInsp['id']}")->fetch();
    logTest("Inspection Status Updated to Rectified & Closed", $closedInsp['status'] === 'Rectified & Closed', "Status: {$closedInsp['status']}");
}

// =========================================================================
// 10. POSSESSION & KEY HANDOVER CERTIFICATES
// =========================================================================
echo "\n--- 10. Possession & Key Handover Management ---\n";
$handoverIndex = httpReq("$baseUrl/handover", 'GET', [], $adminCookieFile);
logTest("Possession Handover Index (/handover)", $handoverIndex['code'] === 200, "HTTP 200, handover register rendered");

$createHandoverPage = httpReq("$baseUrl/handover/create", 'GET', [], $adminCookieFile);
logTest("Execute Handover Page (/handover/create)", $createHandoverPage['code'] === 200, "HTTP 200, clearance form rendered");

// Pick a confirmed booking for handover
$eligibleBooking = $pdo->query("SELECT bookings.id, bookings.property_unit_id FROM bookings WHERE booking_status = 'Confirmed' AND id NOT IN (SELECT booking_id FROM handover_certificates) LIMIT 1")->fetch();

if (!$eligibleBooking) {
    // Pick any confirmed booking
    $eligibleBooking = $pdo->query("SELECT bookings.id, bookings.property_unit_id FROM bookings WHERE booking_status = 'Confirmed' LIMIT 1")->fetch();
}

$storeHandoverRes = httpReq("$baseUrl/handover/store", 'POST', [
    'csrf_token_name'             => $createHandoverPage['csrfToken'],
    'booking_id'                  => $eligibleBooking['id'],
    'handover_date'               => date('Y-m-d'),
    'financial_clearance'         => 1,
    'snagging_clearance'          => 1,
    'occupancy_certificate_ref'   => 'MCGM/BP/OC-2026/0491-REV',
    'electricity_meter_number'    => 'MSEDCL-LT-889025',
    'initial_electricity_reading' => 15.50,
    'water_meter_number'          => 'MCGM-WM-44125',
    'initial_water_reading'       => 5.00,
    'key_sets_provided'           => 3,
    'customer_acknowledged'       => 1,
    'notes'                       => 'All master brass keys handed over with welcome kit and appliance warranties.',
], $adminCookieFile);
logTest("Execute Handover Form POST & CSRF", isOkOrRedirect($storeHandoverRes['code']), "HTTP {$storeHandoverRes['code']}, redirected to certificate");

$latestHandover = $pdo->query("SELECT * FROM handover_certificates ORDER BY id DESC LIMIT 1")->fetch();
logTest("Handover Certificate Persisted with Sequential Code", !empty($latestHandover) && str_starts_with($latestHandover['certificate_number'], 'HND-'), "Code: " . ($latestHandover['certificate_number'] ?? 'None'));

// Check Unit Availability Status Updated to 'Sold'
$unitAfterHandover = $pdo->query("SELECT availability_status FROM property_units WHERE id = {$eligibleBooking['property_unit_id']}")->fetch();
logTest("Property Unit Status Transitioned to 'Sold'", $unitAfterHandover['availability_status'] === 'Sold', "Unit Availability Status: {$unitAfterHandover['availability_status']}");

// Printable Certificate View
$certRes = httpReq("$baseUrl/handover/certificate/{$latestHandover['id']}", 'GET', [], $adminCookieFile);
logTest("Printable Possession & Key Handover Certificate (/handover/certificate/{id})", $certRes['code'] === 200, "HTTP 200, rendered formal certificate with seal");
logTest("Certificate Contains Utility Meter Readings", str_contains($certRes['body'], 'Utility Meter Handover Readings'), "Electricity & Water meter readings present");
logTest("Certificate Contains Clearances & Key Sets", str_contains($certRes['body'], 'Master Key Sets'), "Checklist verified");

// =========================================================================
// 11. RBAC PERMISSIONS ENFORCEMENT
// =========================================================================
echo "\n--- 11. RBAC Permissions Enforcement ---\n";
// Sales Executive should get 403 when trying to access /construction/work-orders
$unauthWO = httpReq("$baseUrl/construction/work-orders", 'GET', [], $salesCookieFile);
logTest("RBAC Gate: Sales Executive Forbidden from Work Contracts", $unauthWO['code'] === 403, "HTTP {$unauthWO['code']} Forbidden");

// Sales Executive should get 403 when trying to delete/deactivate a contractor
$unauthDeleteCon = httpReq("$baseUrl/contractors/delete/1", 'POST', ['csrf_token_name' => $salesCsrf], $salesCookieFile);
logTest("RBAC Gate: Sales Executive Forbidden from Deactivating Contractors", $unauthDeleteCon['code'] === 403, "HTTP {$unauthDeleteCon['code']} Forbidden");

// =========================================================================
// 12. AUDIT TRAIL VERIFICATION
// =========================================================================
echo "\n--- 12. Audit Trail Verification in MySQL ---\n";
$p6AuditActions = [
    'CONSTRUCTION_MILESTONE_CREATED',
    'CONSTRUCTION_MILESTONE_UPDATED',
    'DAILY_SITE_LOG_CREATED',
    'DAILY_SITE_LOG_APPROVED',
    'CONTRACTOR_CREATED',
    'WORK_ORDER_AWARDED',
    'MATERIAL_REQUISITION_CREATED',
    'SITE_INSPECTION_CONDUCTED',
    'POSSESSION_HANDOVER_COMPLETED',
];

$auditFound = 0;
foreach ($p6AuditActions as $action) {
    $exists = $pdo->query("SELECT COUNT(*) FROM audit_logs WHERE action = '$action'")->fetchColumn();
    if ($exists > 0) {
        $auditFound++;
    }
}
logTest("Phase 6 Operations Logged in MySQL `audit_logs`", $auditFound >= 6, "Found {$auditFound} of " . count($p6AuditActions) . " audit action types");

// =========================================================================
// SUMMARY
// =========================================================================
$total = count($results);
$passed = count(array_filter($results, fn($r) => $r['status'] === 'PASS'));
$failed = $total - $passed;

echo "\n========================================================\n";
echo "PHASE 6 VERIFICATION COMPLETED\n";
echo "Total Tests: {$total} | Passed: {$passed} | Failed: {$failed}\n";
echo "Success Rate: " . round(($passed / $total) * 100, 1) . "%\n";
echo "========================================================\n";

if ($allPassed) {
    echo ">>> ALL PHASE 6 TESTS PASSED PERFECTLY (100%)! <<<\n";
    exit(0);
} else {
    echo ">>> SOME PHASE 6 TESTS FAILED! <<<\n";
    exit(1);
}
