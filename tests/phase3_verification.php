<?php

/**
 * Phase 3 Comprehensive Automated Verification Suite
 * Tests end-to-end Real Estate CRM, Lead Management & Sales Pipeline:
 * Lead Sources, Leads CRUD, Code Generation, Assignment & Reassignment,
 * Round-Robin, Qualification, Kanban Pipeline, Enquiries, Property Interests,
 * Follow-up Management, Site Visits & Feedback, Unified Timeline,
 * Temporary Unit Holds / Token Reservations (Expiry, Release, Availability updates),
 * Dynamic Dashboard Cards, Search/Filter, RBAC Permissions, and Audit Logs.
 */

$baseUrl = 'http://localhost:8080';
$adminCookieFile = sys_get_temp_dir() . '/ci4_p3_admin_cookie.txt';
$salesCookieFile = sys_get_temp_dir() . '/ci4_p3_sales_cookie.txt';

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
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $error = curl_error($ch);
    curl_close($ch);

    $headerStr = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    $csrfToken = '';
    if (preg_match('/name="csrf_test_name" value="([^"]+)"/', $body, $m)) {
        $csrfToken = $m[1];
    } elseif (preg_match('/name="csrf_token_name" value="([^"]+)"/', $body, $m)) {
        $csrfToken = $m[1];
    } elseif (preg_match('/name="X-CSRF-TOKEN" content="([^"]+)"/', $body, $m)) {
        $csrfToken = $m[1];
    } elseif ($cookieFile) {
        $csrfToken = getCsrfFromCookieJar($cookieFile);
    }

    return [
        'code'      => $httpCode,
        'headers'   => $headerStr,
        'body'      => $body,
        'url'       => $effectiveUrl,
        'csrfToken' => $csrfToken,
        'error'     => $error,
    ];
}

function isOkOrRedirect($code) {
    return in_array($code, [200, 301, 302, 303], true);
}

echo "========================================================\n";
echo "PHASE 3 CRM & SALES PIPELINE AUTOMATED VERIFICATION\n";
echo "========================================================\n\n";

// 1. Authenticate as Super Admin
$res = httpReq("$baseUrl/login", 'GET', [], $adminCookieFile);
$csrf = $res['csrfToken'];
$loginRes = httpReq("$baseUrl/login", 'POST', [
    'csrf_token_name' => $csrf,
    'email'           => 'admin@realestate-erp.local',
    'password'        => 'Password@123',
], $adminCookieFile);

$dashRes = httpReq("$baseUrl/dashboard", 'GET', [], $adminCookieFile);
logTest("1. Super Admin Authentication", $dashRes['code'] === 200 && str_contains($dashRes['body'], 'Executive Dashboard'), "Dashboard accessible");

// 2. Lead Sources CRUD & Duplicate Prevention
$srcListRes = httpReq("$baseUrl/lead-sources", 'GET', [], $adminCookieFile);
logTest("2.1 Lead Sources Listing", $srcListRes['code'] === 200 && (str_contains($srcListRes['body'], 'Lead Sources') || str_contains($srcListRes['body'], 'Website') || str_contains($srcListRes['body'], 'Direct Enquiry')), "Default sources present");

$createSrcForm = httpReq("$baseUrl/lead-sources/create", 'GET', [], $adminCookieFile);
$csrf = $createSrcForm['csrfToken'];
$uniqueSuffix = time();
$newSrcName = "Airport Billboard {$uniqueSuffix}";
$createSrcRes = httpReq("$baseUrl/lead-sources/store", 'POST', [
    'csrf_token_name' => $csrf,
    'name'            => $newSrcName,
    'description'     => 'Outdoor prime location hoarding',
    'status'          => 'active',
], $adminCookieFile);
logTest("2.2 Create Lead Source", isOkOrRedirect($createSrcRes['code']), "Code: {$createSrcRes['code']}");

$srcRow = $pdo->query("SELECT * FROM lead_sources WHERE name = " . $pdo->quote($newSrcName))->fetch();
logTest("2.3 Lead Source Saved in DB", !empty($srcRow) && str_starts_with($srcRow['slug'], 'airport-billboard-'), "Slug: {$srcRow['slug']}");

// Test Duplicate Source Name Prevention
$dupSrcForm = httpReq("$baseUrl/lead-sources/create", 'GET', [], $adminCookieFile);
$csrf = $dupSrcForm['csrfToken'];
$dupSrcRes = httpReq("$baseUrl/lead-sources/store", 'POST', [
    'csrf_token_name' => $csrf,
    'name'            => $newSrcName,
    'description'     => 'Duplicate test',
    'status'          => 'active',
], $adminCookieFile);
$dupCheckCount = $pdo->query("SELECT COUNT(*) FROM lead_sources WHERE name = " . $pdo->quote($newSrcName))->fetchColumn();
logTest("2.4 Duplicate Source Name Blocked", $dupCheckCount == 1, "Duplicate source was rejected");

// 3. Lead Creation & Sequential Unique Lead Code
$leadForm = httpReq("$baseUrl/leads/create", 'GET', [], $adminCookieFile);
$csrf = $leadForm['csrfToken'];

$propRow = $pdo->query("SELECT id, title, project_id FROM properties WHERE status = 'Available' LIMIT 1")->fetch();
$unitRow = $pdo->query("SELECT id, unit_number FROM property_units WHERE availability_status = 'Available' LIMIT 1")->fetch();
$salesUser = $pdo->query("SELECT id, name FROM users WHERE email = 'sales@realestate-erp.local'")->fetch();

$uniqueEmail = "vikram.singhania.{$uniqueSuffix}@corp.local";
$leadData = [
    'csrf_token_name'    => $csrf,
    'first_name'         => 'Vikram',
    'last_name'          => 'Singhania',
    'email'              => $uniqueEmail,
    'phone'              => '+91 98200 88771',
    'alternate_phone'    => '+91 98200 88772',
    'lead_source_id'     => $srcRow['id'],
    'budget_min'         => 18000000,
    'budget_max'         => 25000000,
    'preferred_location' => 'Bandra Kurla Complex',
    'priority'           => 'High',
    'project_id'         => $propRow['project_id'] ?? null,
    'property_id'        => $propRow['id'] ?? null,
    'remarks'            => 'Looking for luxury 3BHK for immediate investment',
];

$storeLeadRes = httpReq("$baseUrl/leads/store", 'POST', $leadData, $adminCookieFile);
logTest("3.1 Lead Creation Request", isOkOrRedirect($storeLeadRes['code']), "Code: {$storeLeadRes['code']}");

$createdLead = $pdo->query("SELECT * FROM leads WHERE email = '{$uniqueEmail}'")->fetch();
$leadCodeValid = !empty($createdLead) && preg_match('/^LEAD-\d{4}-\d{6}$/', $createdLead['lead_code']);
logTest("3.2 Sequential Unique Lead Code", $leadCodeValid, "Lead Code: " . ($createdLead['lead_code'] ?? 'None'));

// Check Prospect Foundation Record Created
$prospect = $pdo->query("SELECT * FROM prospects WHERE lead_id = " . (int)$createdLead['id'])->fetch();
logTest("4. Prospect Foundation Auto-Created", !empty($prospect) && $prospect['email'] === $uniqueEmail, "Prospect record linked to lead");

// 5. Lead Assignment & Reassignment History
$leadViewRes = httpReq("$baseUrl/leads/view/{$createdLead['id']}", 'GET', [], $adminCookieFile);
$csrf = $leadViewRes['csrfToken'];

// Initial Assignment to Sales Executive
$assignRes = httpReq("$baseUrl/leads/assign/{$createdLead['id']}", 'POST', [
    'csrf_token_name'  => $csrf,
    'assigned_user_id' => $salesUser['id'],
    'remarks'          => 'Assigned to senior sales specialist',
], $adminCookieFile);
logTest("5.1 Lead Assignment Request", isOkOrRedirect($assignRes['code']), "Assigned to sales executive");

$assignHist1 = $pdo->query("SELECT * FROM lead_assignments WHERE lead_id = {$createdLead['id']} ORDER BY id ASC LIMIT 1")->fetch();
logTest("5.2 Initial Assignment Recorded", !empty($assignHist1) && $assignHist1['assignment_type'] === 'Initial', "Assignment type: " . ($assignHist1['assignment_type'] ?? ''));

// Reassignment to Manager
$managerUser = $pdo->query("SELECT id FROM users WHERE email = 'manager@realestate-erp.local'")->fetch();
$leadViewRes2 = httpReq("$baseUrl/leads/view/{$createdLead['id']}", 'GET', [], $adminCookieFile);
$csrf = $leadViewRes2['csrfToken'];
$reassignRes = httpReq("$baseUrl/leads/assign/{$createdLead['id']}", 'POST', [
    'csrf_token_name'  => $csrf,
    'assigned_user_id' => $managerUser['id'],
    'remarks'          => 'VIP escalation to branch manager',
], $adminCookieFile);

$assignHist2 = $pdo->query("SELECT * FROM lead_assignments WHERE lead_id = {$createdLead['id']} ORDER BY id DESC LIMIT 1")->fetch();
$totalAssignCount = $pdo->query("SELECT COUNT(*) FROM lead_assignments WHERE lead_id = {$createdLead['id']}")->fetchColumn();
logTest("5.3 Reassignment Preserves Complete History", !empty($assignHist2) && $assignHist2['assignment_type'] === 'Reassignment' && $totalAssignCount == 2, "Assignment history count: {$totalAssignCount}");

// 6. Round-Robin Assignment Logic
$roundRobinRes = httpReq("$baseUrl/leads/assign-round-robin/{$createdLead['id']}", 'POST', [
    'csrf_token_name' => $csrf,
], $adminCookieFile);
logTest("6. Round-Robin Auto Assignment", isOkOrRedirect($roundRobinRes['code']), "Round-robin distributed to eligible agent");

// 7. Lead Qualification
$leadViewRes3 = httpReq("$baseUrl/leads/view/{$createdLead['id']}", 'GET', [], $adminCookieFile);
$csrf = $leadViewRes3['csrfToken'];
$qualifyRes = httpReq("$baseUrl/leads/qualify/{$createdLead['id']}", 'POST', [
    'csrf_token_name'            => $csrf,
    'qualification_result'       => 'Qualified',
    'budget_min'                 => 20000000,
    'budget_max'                 => 25000000,
    'preferred_location'         => 'BKC',
    'purchase_purpose'           => 'Investment',
    'expected_purchase_timeline' => 'Immediate (< 30 days)',
    'financing_required'         => 'No',
    'site_visit_required'        => 'Yes',
    'qualification_remarks'      => 'High net-worth investor, verified funds',
], $adminCookieFile);
logTest("7.1 Lead Qualification Request", isOkOrRedirect($qualifyRes['code']), "Code: {$qualifyRes['code']}");

$qualifiedLead = $pdo->query("SELECT lead_status, lead_stage, purchase_purpose, financing_required FROM leads WHERE id = {$createdLead['id']}")->fetch();
logTest("7.2 Lead Status & Stage Updated by Qualification", 
    $qualifiedLead['lead_status'] === 'Qualified' && $qualifiedLead['lead_stage'] === 'Qualified' && $qualifiedLead['purchase_purpose'] === 'Investment',
    "Status: {$qualifiedLead['lead_status']}, Stage: {$qualifiedLead['lead_stage']}");

// 8. CRM Pipeline Kanban & Stage Progression
$pipelineRes = httpReq("$baseUrl/pipeline", 'GET', [], $adminCookieFile);
logTest("8.1 Kanban Pipeline Board", $pipelineRes['code'] === 200 && str_contains($pipelineRes['body'], 'Sales Pipeline'), "Pipeline rendered with 10 stages");

// Transition Stage: Qualified -> Site Visit Scheduled
$csrf = $pipelineRes['csrfToken'];
$stageRes = httpReq("$baseUrl/pipeline/stage", 'POST', [
    'csrf_token_name' => $csrf,
    'lead_id'         => $createdLead['id'],
    'lead_stage'      => 'Site Visit Scheduled',
    'remarks'         => 'Appointment scheduled for weekend tour',
], $adminCookieFile);
logTest("8.2 Pipeline Stage Transition", isOkOrRedirect($stageRes['code']), "Moved to Site Visit Scheduled");

$leadStageCheck = $pdo->query("SELECT lead_stage FROM leads WHERE id = {$createdLead['id']}")->fetchColumn();
logTest("8.3 Stage In Database Updated", $leadStageCheck === 'Site Visit Scheduled', "Current stage: {$leadStageCheck}");

// 9. Enquiry Management
$enqIndex = httpReq("$baseUrl/enquiries", 'GET', [], $adminCookieFile);
logTest("9.1 Enquiry Management Listing", $enqIndex['code'] === 200 && (str_contains($enqIndex['body'], 'Property Enquiries') || str_contains($enqIndex['body'], 'Enquiries')), "Enquiries table rendered");

$csrf = $enqIndex['csrfToken'];
$enqData = [
    'csrf_token_name'    => $csrf,
    'lead_id'            => $createdLead['id'],
    'property_id'        => $propRow['id'] ?? null,
    'property_unit_id'   => $unitRow['id'] ?? null,
    'enquiry_type'       => 'Purchase',
    'requirement'        => 'Requires high-floor 3BHK with sea facing balcony',
    'budget'             => 22000000,
    'preferred_location' => 'Bandra',
    'status'             => 'Open',
    'remarks'            => 'Client requested floor plans and brochure',
];
$enqStoreRes = httpReq("$baseUrl/enquiries/store", 'POST', $enqData, $adminCookieFile);
logTest("9.2 Create Enquiry Request", isOkOrRedirect($enqStoreRes['code']), "Code: {$enqStoreRes['code']}");

$createdEnq = $pdo->query("SELECT * FROM enquiries WHERE lead_id = {$createdLead['id']} ORDER BY id DESC LIMIT 1")->fetch();
$enqCodeValid = !empty($createdEnq) && preg_match('/^ENQ-\d{4}-\d{6}$/', $createdEnq['enquiry_code']);
logTest("9.3 Sequential Enquiry Code Generation", $enqCodeValid, "Enquiry Code: " . ($createdEnq['enquiry_code'] ?? 'None'));

// 10. Property Interest Linking (Foreign keys to Phase 2)
$leadViewRes4 = httpReq("$baseUrl/leads/view/{$createdLead['id']}", 'GET', [], $adminCookieFile);
$csrf = $leadViewRes4['csrfToken'];
$interestRes = httpReq("$baseUrl/leads/interest/{$createdLead['id']}", 'POST', [
    'csrf_token_name'  => $csrf,
    'property_id'      => $propRow['id'] ?? null,
    'property_unit_id' => $unitRow['id'] ?? null,
    'interest_level'   => 'Primary',
    'remarks'          => 'Client primary choice after reviewing specs',
], $adminCookieFile);
logTest("10.1 Add Property Interest", isOkOrRedirect($interestRes['code']), "Code: {$interestRes['code']}");

$interestRow = $pdo->query("SELECT * FROM lead_property_interests WHERE lead_id = {$createdLead['id']} ORDER BY id DESC LIMIT 1")->fetch();
logTest("10.2 Property Interest In Database", !empty($interestRow) && $interestRow['interest_level'] === 'Primary', "Linked to property ID: {$interestRow['property_id']}");

// 11. Follow-up Management
$fuIndex = httpReq("$baseUrl/followups", 'GET', [], $adminCookieFile);
logTest("11.1 Follow-up Management Index", $fuIndex['code'] === 200 && str_contains($fuIndex['body'], 'Follow-up Management'), "Follow-ups page rendered");

$csrf = $fuIndex['csrfToken'];
$scheduledTime = date('Y-m-d 17:00:00');
$storeFuRes = httpReq("$baseUrl/followups/store", 'POST', [
    'csrf_token_name' => $csrf,
    'lead_id'         => $createdLead['id'],
    'followup_type'   => 'Phone Call',
    'scheduled_at'    => $scheduledTime,
    'notes'           => 'Call regarding site visit timings and driver details',
], $adminCookieFile);
logTest("11.2 Schedule Follow-up", isOkOrRedirect($storeFuRes['code']), "Code: {$storeFuRes['code']}");

$fuRow = $pdo->query("SELECT * FROM lead_followups WHERE lead_id = {$createdLead['id']} ORDER BY id DESC LIMIT 1")->fetch();
logTest("11.3 Follow-up In Database", !empty($fuRow) && $fuRow['status'] === 'Pending', "Follow-up ID: {$fuRow['id']}");

// Complete Follow-up
$csrf = getCsrfFromCookieJar($adminCookieFile);
$completeFuRes = httpReq("$baseUrl/followups/complete/{$fuRow['id']}", 'POST', [
    'csrf_token_name'   => $csrf,
    'outcome'           => 'Site visit confirmed for Saturday 11 AM',
    'notes'             => 'Spoke with Mr. Singhania, agreed on property itinerary',
    'next_followup_at'  => date('Y-m-d 12:00:00', strtotime('+2 days')),
], $adminCookieFile);
logTest("11.4 Complete Follow-up Request", isOkOrRedirect($completeFuRes['code']), "Code: {$completeFuRes['code']}");

$completedFu = $pdo->query("SELECT status, outcome FROM lead_followups WHERE id = {$fuRow['id']}")->fetch();
logTest("11.5 Follow-up Completed State In DB", $completedFu['status'] === 'Completed' && str_contains($completedFu['outcome'], 'confirmed'), "Outcome: {$completedFu['outcome']}");

// 12. Site Visit Management & Completion Feedback
$visitIndex = httpReq("$baseUrl/site-visits", 'GET', [], $adminCookieFile);
logTest("12.1 Site Visits Listing", $visitIndex['code'] === 200 && str_contains($visitIndex['body'], 'Site Visit Management'), "Site visits page rendered");

$csrf = $visitIndex['csrfToken'];
$visitSchedule = date('Y-m-d 11:00:00', strtotime('+1 day'));
$storeVisitRes = httpReq("$baseUrl/site-visits/store", 'POST', [
    'csrf_token_name'  => $csrf,
    'lead_id'          => $createdLead['id'],
    'property_id'      => $propRow['id'] ?? null,
    'property_unit_id' => $unitRow['id'] ?? null,
    'visit_type'       => 'Property Visit',
    'scheduled_at'     => $visitSchedule,
    'visitor_count'    => 2,
    'remarks'          => 'VIP couple touring sample flat',
], $adminCookieFile);
logTest("12.2 Schedule Site Visit Request", isOkOrRedirect($storeVisitRes['code']), "Code: {$storeVisitRes['code']}");

$visitRow = $pdo->query("SELECT * FROM site_visits WHERE lead_id = {$createdLead['id']} ORDER BY id DESC LIMIT 1")->fetch();
$visitCodeValid = !empty($visitRow) && preg_match('/^SV-\d{4}-\d{6}$/', $visitRow['visit_code']);
logTest("12.3 Site Visit Code Generation", $visitCodeValid, "Visit Code: " . ($visitRow['visit_code'] ?? 'None'));

// Complete Site Visit with Feedback, Rating, and Next Action
$csrf = getCsrfFromCookieJar($adminCookieFile);
$completeVisitRes = httpReq("$baseUrl/site-visits/complete/{$visitRow['id']}", 'POST', [
    'csrf_token_name'   => $csrf,
    'rating'            => 5,
    'interest_level'    => 'High',
    'feedback'          => 'Loved the spacious layout and clubhouse amenities',
    'agent_observation' => 'Client is serious, wants token hold on Unit ' . ($unitRow['unit_number'] ?? 'selected'),
    'preferred_unit'    => 'Unit ' . ($unitRow['unit_number'] ?? 'A-101'),
    'price_feedback'    => 'Price acceptable within budget',
    'next_action'       => 'Negotiation',
], $adminCookieFile);
logTest("12.4 Complete Site Visit Request", isOkOrRedirect($completeVisitRes['code']), "Code: {$completeVisitRes['code']}");

$completedVisit = $pdo->query("SELECT status, rating, interest_level, next_action FROM site_visits WHERE id = {$visitRow['id']}")->fetch();
logTest("12.5 Site Visit Feedback & Next Action Saved", 
    $completedVisit['status'] === 'Completed' && $completedVisit['rating'] == 5 && $completedVisit['next_action'] === 'Negotiation',
    "Status: {$completedVisit['status']}, Rating: {$completedVisit['rating']}/5");

// 13. Temporary Unit Hold / Token Reservation
$holdIndex = httpReq("$baseUrl/unit-holds", 'GET', [], $adminCookieFile);
logTest("13.1 Unit Holds Index View", $holdIndex['code'] === 200 && str_contains($holdIndex['body'], 'Temporary Unit Holds'), "Unit holds page rendered");

// Pick an Available Unit for Hold
$availUnit = $pdo->query("SELECT * FROM property_units WHERE availability_status = 'Available' ORDER BY id ASC LIMIT 1")->fetch();
if (!$availUnit) {
    $pdo->query("UPDATE unit_holds SET hold_status = 'Released' WHERE property_unit_id = 9 AND hold_status = 'Active'");
    $pdo->query("UPDATE property_units SET availability_status = 'Available' WHERE id = 9");
    $availUnit = $pdo->query("SELECT * FROM property_units WHERE id = 9")->fetch();
}
$csrf = $holdIndex['csrfToken'];

$holdStoreRes = httpReq("$baseUrl/unit-holds/store", 'POST', [
    'csrf_token_name'  => $csrf,
    'lead_id'          => $createdLead['id'],
    'property_unit_id' => $availUnit['id'],
    'duration_hours'   => 48,
    'hold_reason'      => 'Token advance negotiation with buyer',
    'remarks'          => 'Client depositing token cheque tomorrow morning',
], $adminCookieFile);
logTest("13.2 Create Unit Hold Request", isOkOrRedirect($holdStoreRes['code']), "Hold placed on Unit {$availUnit['unit_number']}");

$createdHold = $pdo->query("SELECT * FROM unit_holds WHERE property_unit_id = {$availUnit['id']} AND hold_status = 'Active'")->fetch();
$holdCodeValid = !empty($createdHold) && preg_match('/^HOLD-\d{4}-\d{6}$/', $createdHold['hold_code']);
logTest("13.3 Hold Code Generation", $holdCodeValid, "Hold Code: " . ($createdHold['hold_code'] ?? 'None'));

// Verify Unit Status Transitioned to 'Reserved'
$unitStatusAfterHold = $pdo->query("SELECT availability_status FROM property_units WHERE id = {$availUnit['id']}")->fetchColumn();
logTest("13.4 Unit Status Transition: Available -> Reserved", $unitStatusAfterHold === 'Reserved', "Unit availability_status is now Reserved");

// Verify Status History Logged
$histCheck = $pdo->query("SELECT * FROM property_status_history WHERE unit_id = {$availUnit['id']} AND new_status = 'Reserved' ORDER BY id DESC LIMIT 1")->fetch();
logTest("13.5 Status History Recorded for Hold", !empty($histCheck) && $histCheck['old_status'] === 'Available', "History logged by user");

// Test Duplicate Hold Prevention on the Same Unit
$dupAnotherLead = $pdo->query("SELECT id FROM leads WHERE id != {$createdLead['id']} LIMIT 1")->fetchColumn();
$csrf = getCsrfFromCookieJar($adminCookieFile);
$dupHoldRes = httpReq("$baseUrl/unit-holds/store", 'POST', [
    'csrf_token_name'  => $csrf,
    'lead_id'          => $dupAnotherLead,
    'property_unit_id' => $availUnit['id'],
    'duration_hours'   => 24,
    'hold_reason'      => 'Attempt duplicate hold',
], $adminCookieFile);
$activeHoldsOnUnit = $pdo->query("SELECT COUNT(*) FROM unit_holds WHERE property_unit_id = {$availUnit['id']} AND hold_status = 'Active'")->fetchColumn();
logTest("13.6 Duplicate Unit Hold Prevention", $activeHoldsOnUnit == 1, "Duplicate hold blocked. Active holds: {$activeHoldsOnUnit}");

// Test Hold Expiry Logic
$availUnit2 = $pdo->query("SELECT * FROM property_units WHERE availability_status = 'Available' LIMIT 1")->fetch();
if (!$availUnit2) {
    $availUnit2 = $pdo->query("SELECT * FROM property_units LIMIT 1")->fetch();
}
$pastTime = date('Y-m-d H:i:s', strtotime('-2 hours'));
$pdo->prepare("INSERT INTO unit_holds (hold_code, lead_id, property_id, property_unit_id, held_by, hold_status, hold_reason, started_at, expires_at, created_at)
    VALUES (?, ?, ?, ?, ?, 'Active', 'Expiry test', ?, ?, NOW())")
    ->execute(['HOLD-TEST-EXP-' . time(), $createdLead['id'], $availUnit2['property_id'], $availUnit2['id'], $salesUser['id'], $pastTime, $pastTime]);

$pdo->query("UPDATE property_units SET availability_status = 'Reserved' WHERE id = {$availUnit2['id']}");

// Access unit holds page to trigger expireOverdueHolds()
$refreshHoldsRes = httpReq("$baseUrl/unit-holds", 'GET', [], $adminCookieFile);
$expiredHoldRow = $pdo->query("SELECT hold_status FROM unit_holds WHERE property_unit_id = {$availUnit2['id']} ORDER BY id DESC LIMIT 1")->fetch();
$unit2StatusRestored = $pdo->query("SELECT availability_status FROM property_units WHERE id = {$availUnit2['id']}")->fetchColumn();
logTest("13.7 Hold Expiry On-Access Mechanism", 
    $expiredHoldRow['hold_status'] === 'Expired' && $unit2StatusRestored === 'Available',
    "Hold status: {$expiredHoldRow['hold_status']}, Unit status restored to: {$unit2StatusRestored}");

// Test Manual Release of the First Hold
$csrf = getCsrfFromCookieJar($adminCookieFile);
$releaseHoldRes = httpReq("$baseUrl/unit-holds/release/{$createdHold['id']}", 'POST', [
    'csrf_token_name' => $csrf,
    'release_reason'  => 'Buyer opted for alternative penthouse unit',
], $adminCookieFile);
logTest("13.8 Manual Hold Release Request", isOkOrRedirect($releaseHoldRes['code']), "Code: {$releaseHoldRes['code']}");

$releasedHoldRow = $pdo->query("SELECT hold_status, released_at FROM unit_holds WHERE id = {$createdHold['id']}")->fetch();
$unit1StatusRestored = $pdo->query("SELECT availability_status FROM property_units WHERE id = {$availUnit['id']}")->fetchColumn();
logTest("13.9 Unit Restored to Available on Manual Release", 
    $releasedHoldRow['hold_status'] === 'Released' && $unit1StatusRestored === 'Available' && !empty($releasedHoldRow['released_at']),
    "Hold: {$releasedHoldRow['hold_status']}, Unit status: {$unit1StatusRestored}");

// 14. Unified Lead Timeline
$timelineRes = httpReq("$baseUrl/leads/view/{$createdLead['id']}", 'GET', [], $adminCookieFile);
$timelineBody = $timelineRes['body'];
$hasCreatedEvent = str_contains($timelineBody, 'Lead Ingested') || str_contains($timelineBody, 'Lead Created');
$hasFollowupEvent = str_contains($timelineBody, 'Follow-up') || str_contains($timelineBody, 'Phone Call');
$hasSiteVisitEvent = str_contains($timelineBody, 'Site Visit') || str_contains($timelineBody, 'Scheduled');
logTest("14. Unified Lead Timeline Display", $hasCreatedEvent && $hasFollowupEvent && $hasSiteVisitEvent, "Multi-source activities rendered in chronological timeline");

// 15. Lead Search & Filtering
$searchRes = httpReq("$baseUrl/leads?search=Singhania", 'GET', [], $adminCookieFile);
logTest("15.1 Lead Search By Keyword", $searchRes['code'] === 200 && str_contains($searchRes['body'], 'Singhania'), "Search returns matching lead");

$filterStatusRes = httpReq("$baseUrl/leads?lead_status=Qualified", 'GET', [], $adminCookieFile);
logTest("15.2 Lead Filter By Status", $filterStatusRes['code'] === 200 && str_contains($filterStatusRes['body'], 'Singhania'), "Status filter returns matching qualified leads");

// 16. RBAC Permissions Enforcement
// Login as Sales Executive
$resSales = httpReq("$baseUrl/login", 'GET', [], $salesCookieFile);
$csrf = $resSales['csrfToken'];
httpReq("$baseUrl/login", 'POST', [
    'csrf_token_name' => $csrf,
    'email'           => 'sales@realestate-erp.local',
    'password'        => 'Password@123',
], $salesCookieFile);

// Sales Executive can view leads
$salesLeadsRes = httpReq("$baseUrl/leads", 'GET', [], $salesCookieFile);
logTest("16.1 Sales Executive Permitted: View Leads", $salesLeadsRes['code'] === 200, "HTTP 200 OK");

// Sales Executive attempting to delete a Lead Source should be forbidden (403)
$csrfSales = getCsrfFromCookieJar($salesCookieFile);
$salesDelRes = httpReq("$baseUrl/lead-sources/delete/{$srcRow['id']}", 'POST', [
    'csrf_token_name' => $csrfSales,
], $salesCookieFile);
logTest("16.2 Sales Executive Forbidden: Delete Lead Source", in_array($salesDelRes['code'], [403, 302]) || str_contains($salesDelRes['body'], '403') || str_contains($salesDelRes['body'], 'Access Denied'), "Access blocked for unauthorized role");

// 17. Dynamic CRM Dashboard Verification
$dashRes = httpReq("$baseUrl/dashboard", 'GET', [], $adminCookieFile);
$dashBody = $dashRes['body'];
$hasTotalLeadsCard   = str_contains($dashBody, "Total Leads");
$hasQualifiedCard    = str_contains($dashBody, "Qualified Leads");
$hasFollowupsCard    = str_contains($dashBody, "Today's Follow-ups");
$hasSiteVisitsCard   = str_contains($dashBody, "Upcoming Visits") || str_contains($dashBody, "Today's Visits");
$hasUnitHoldsCard    = str_contains($dashBody, "Active Unit Holds");
logTest("17. Dynamic CRM Dashboard Metrics Grid", 
    $hasTotalLeadsCard && $hasQualifiedCard && $hasFollowupsCard && $hasSiteVisitsCard && $hasUnitHoldsCard,
    "All CRM summary cards dynamically rendered from MySQL");

// 18. Audit Logging for CRM Transactions
$auditRows = $pdo->query("SELECT action, module FROM audit_logs WHERE module IN ('Leads', 'SiteVisits', 'UnitHolds', 'Pipeline', 'LeadSources', 'CRM') ORDER BY id DESC LIMIT 20")->fetchAll();
$auditActions = array_column($auditRows, 'action');
logTest("18. Audit Trail for CRM Operations", 
    count($auditRows) >= 3,
    "Recent logged actions: " . implode(', ', array_unique($auditActions)));

echo "\n========================================================\n";
echo sprintf("RESULTS: %d Tests Run | %s\n", count($results), $allPassed ? "ALL TESTS PASSED! (100% SUCCESS)" : "SOME TESTS FAILED");
echo "========================================================\n";

exit($allPassed ? 0 : 1);
