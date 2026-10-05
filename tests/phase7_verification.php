<?php

/**
 * Phase 7 Comprehensive Automated Verification Suite
 * Tests Property Owners & Landlord Portfolio, Agents & External Brokers,
 * Property Expense Accounting, Marketing Campaigns & Dynamic CAC/CPL Metrics,
 * Compliance Documents & File Streaming, Legal Verifications & Checklist Verdicts,
 * Centralized Notifications & Reminders, Customer Multi-Channel Communications,
 * Executive Reports Hub (Sales, Rental, Expense), System Settings & Data Export,
 * Landlord Self-Service Portal, RBAC Permission Enforcement, and Audit Trail.
 */

$baseUrl = 'http://localhost:8080';
$adminCookieFile = sys_get_temp_dir() . '/ci4_p7_admin_cookie.txt';
$salesCookieFile = sys_get_temp_dir() . '/ci4_p7_sales_cookie.txt';
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
    $headerStr = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

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

echo "========================================================================\n";
echo " PHASE 7 ERP COMPLETENESS & PRODUCTION POLISH AUTOMATED TEST SUITE\n";
echo "========================================================================\n\n";

// ========================================================================
// 1. DATABASE SCHEMA & INTEGRITY CHECKS
// ========================================================================
echo "--- 1. Database Schema & Architecture Verification ---\n";

$requiredTables = [
    'property_owners',
    'agents',
    'expense_categories',
    'property_expenses',
    'marketing_campaigns',
    'property_documents',
    'property_verifications',
    'notifications',
    'customer_communications',
    'system_settings',
];

foreach ($requiredTables as $tbl) {
    $exists = $pdo->query("SHOW TABLES LIKE '{$tbl}'")->rowCount() > 0;
    logTest("Table Existence: {$tbl}", $exists, "MySQL 8.0 schema check");
}

$stmt = $pdo->query("SHOW COLUMNS FROM properties LIKE 'owner_id'");
logTest("Properties Table: owner_id foreign key column", $stmt->rowCount() > 0, "Backward-compatible schema expansion");

// Check settings seeded
$settingsCount = (int)$pdo->query("SELECT COUNT(*) FROM system_settings")->fetchColumn();
logTest("System Settings Table Seeded", $settingsCount >= 15, "Found {$settingsCount} settings");

// ========================================================================
// 2. AUTHENTICATION & CSRF
// ========================================================================
echo "\n--- 2. Authentication & Session Establishment ---\n";

$loginPage = httpReq("{$baseUrl}/login", 'GET', [], $adminCookieFile);
$csrf = $loginPage['csrfToken'];
logTest("Admin Login Page Accessible", $loginPage['code'] === 200);

$loginResp = httpReq("{$baseUrl}/login", 'POST', [
    'csrf_token_name' => $csrf,
    'email'            => 'admin@realestate-erp.local',
    'password'         => 'Password@123',
], $adminCookieFile, false);

logTest("Super Admin Authentication", isOkOrRedirect($loginResp['code']), "HTTP {$loginResp['code']}");

// Sales Executive login
$salesPage = httpReq("{$baseUrl}/login", 'GET', [], $salesCookieFile);
$salesCsrf = $salesPage['csrfToken'];
$salesLoginResp = httpReq("{$baseUrl}/login", 'POST', [
    'csrf_token_name' => $salesCsrf,
    'email'            => 'sales@realestate-erp.local',
    'password'         => 'Password@123',
], $salesCookieFile, false);
logTest("Sales Executive Authentication", isOkOrRedirect($salesLoginResp['code']), "HTTP {$salesLoginResp['code']}");

// ========================================================================
// 3. PROPERTY OWNERS & LANDLORD PORTFOLIO
// ========================================================================
echo "\n--- 3. Property Owners & Landlord Portfolio Suite ---\n";

$ownersIndex = httpReq("{$baseUrl}/owners", 'GET', [], $adminCookieFile);
logTest("Property Owners Directory (GET /owners)", $ownersIndex['code'] === 200 && str_contains($ownersIndex['body'], 'Property Owners Management'));

// Create a new property owner
$createOwnerResp = httpReq("{$baseUrl}/owners/store", 'POST', [
    'first_name'          => 'Vikram',
    'last_name'           => 'Singhania ' . rand(100, 999),
    'email'               => 'landlord' . time() . '@example.com',
    'phone'               => '98765' . rand(10000, 99999),
    'pan_number'          => 'ABCDE' . rand(1000, 9999) . 'Z',
    'bank_name'           => 'HDFC Bank',
    'bank_account_number' => '50100' . rand(1000000, 9999999),
    'bank_ifsc'           => 'HDFC0001234',
    'status'              => 'active',
], $adminCookieFile, false);

logTest("Create Property Owner (POST /owners/store)", isOkOrRedirect($createOwnerResp['code']), "Created owner");

$newOwner = $pdo->query("SELECT * FROM property_owners ORDER BY id DESC LIMIT 1")->fetch();
$ownerId = $newOwner['id'] ?? 1;

$ownerView = httpReq("{$baseUrl}/owners/view/{$ownerId}", 'GET', [], $adminCookieFile);
logTest("View Property Owner Portfolio (GET /owners/view/{$ownerId})", $ownerView['code'] === 200 && str_contains($ownerView['body'], 'Properties Owned'));

$ownerStatement = httpReq("{$baseUrl}/owners/statement/{$ownerId}", 'GET', [], $adminCookieFile);
logTest("Printable Owner Financial Statement (GET /owners/statement/{$ownerId})", $ownerStatement['code'] === 200 && str_contains($ownerStatement['body'], 'Owner Portfolio Statement'));

// ========================================================================
// 4. AGENTS & EXTERNAL BROKERS
// ========================================================================
echo "\n--- 4. Agent & External Broker Management Suite ---\n";

$agentsIndex = httpReq("{$baseUrl}/agents", 'GET', [], $adminCookieFile);
logTest("Agents Directory (GET /agents)", $agentsIndex['code'] === 200 && str_contains($agentsIndex['body'], 'Agent & Broker Directory'));

$createAgentResp = httpReq("{$baseUrl}/agents/store", 'POST', [
    'first_name'        => 'Kunal',
    'last_name'         => 'Mehta ' . rand(100, 999),
    'agent_type'        => 'External Broker',
    'agency_name'       => 'Apex Realty Associates',
    'phone'             => '91234' . rand(10000, 99999),
    'email'             => 'broker' . time() . '@example.com',
    'rera_number'       => 'RERA/AGT/' . rand(1000, 9999),
    'commission_rate'   => '2.50',
    'status'            => 'Active',
], $adminCookieFile, false);

logTest("Create Real Estate Agent (POST /agents/store)", isOkOrRedirect($createAgentResp['code']), "Created broker");

$newAgent = $pdo->query("SELECT * FROM agents ORDER BY id DESC LIMIT 1")->fetch();
$agentId = $newAgent['id'] ?? 1;

$agentView = httpReq("{$baseUrl}/agents/view/{$agentId}", 'GET', [], $adminCookieFile);
logTest("View Agent Performance & Commission Ledger (GET /agents/view/{$agentId})", $agentView['code'] === 200 && str_contains($agentView['body'], 'Commission Earned'));

// ========================================================================
// 5. PROPERTY EXPENSE ACCOUNTING
// ========================================================================
echo "\n--- 5. Property Expense & Operating Outlays Suite ---\n";

$expensesIndex = httpReq("{$baseUrl}/expenses", 'GET', [], $adminCookieFile);
logTest("Property Expenses Ledger (GET /expenses)", $expensesIndex['code'] === 200 && str_contains($expensesIndex['body'], 'Property Expense Management'));

$categoriesResp = httpReq("{$baseUrl}/expenses/categories", 'GET', [], $adminCookieFile);
logTest("Expense Categories Directory (GET /expenses/categories)", $categoriesResp['code'] === 200 && str_contains($categoriesResp['body'], 'Expense Categories'));

// Add category
$newCatName = 'Facility Upgrade ' . rand(10, 99);
$createCatResp = httpReq("{$baseUrl}/expenses/categories/store", 'POST', [
    'name'        => $newCatName,
    'description' => 'Automated test expense category',
    'is_active'   => 1,
], $adminCookieFile, false);
logTest("Create Expense Category (POST /expenses/categories/store)", isOkOrRedirect($createCatResp['code']), "Created category {$newCatName}");

// Fetch a property and category for recording expense
$firstProp = $pdo->query("SELECT id FROM properties LIMIT 1")->fetch();
$firstCat = $pdo->query("SELECT id FROM expense_categories LIMIT 1")->fetch();

$propId = $firstProp['id'] ?? 1;
$catId = $firstCat['id'] ?? 1;

$createExpenseResp = httpReq("{$baseUrl}/expenses/store", 'POST', [
    'property_id'    => $propId,
    'category_id'    => $catId,
    'payee_vendor'   => 'Apex Facility Care Ltd',
    'title'          => 'Elevator Annual AMC Servicing',
    'amount'         => '15000.00',
    'tax_amount'     => '2700.00',
    'expense_date'   => date('Y-m-d'),
    'payment_method' => 'Bank Transfer',
    'status'         => 'Paid',
    'notes'          => 'Quarterly preventive elevator servicing invoice',
], $adminCookieFile, false);

logTest("Record Property Operating Expense (POST /expenses/store)", isOkOrRedirect($createExpenseResp['code']), "Created expense voucher");

$newExpense = $pdo->query("SELECT * FROM property_expenses ORDER BY id DESC LIMIT 1")->fetch();
$expenseId = $newExpense['id'] ?? 1;

$expenseView = httpReq("{$baseUrl}/expenses/view/{$expenseId}", 'GET', [], $adminCookieFile);
logTest("View Expense Voucher (GET /expenses/view/{$expenseId})", $expenseView['code'] === 200 && str_contains($expenseView['body'], 'Voucher'));

$expenseReport = httpReq("{$baseUrl}/expenses/report", 'GET', [], $adminCookieFile);
logTest("Annual Property Expense Statement (GET /expenses/report)", $expenseReport['code'] === 200 && str_contains($expenseReport['body'], 'Annual Property Expense Report'));

// ========================================================================
// 6. MARKETING & ADVERTISING CAMPAIGNS
// ========================================================================
echo "\n--- 6. Marketing Campaigns & Dynamic KPI Metrics Suite ---\n";

$campaignsIndex = httpReq("{$baseUrl}/campaigns", 'GET', [], $adminCookieFile);
logTest("Marketing Campaigns Dashboard (GET /campaigns)", $campaignsIndex['code'] === 200 && str_contains($campaignsIndex['body'], 'Marketing & Advertisement Management'));
logTest("Dynamic CPL & Conversion Metrics Verified", str_contains($campaignsIndex['body'], 'Cost Per Lead') && str_contains($campaignsIndex['body'], 'Conversion Rate'));

$createCampaignResp = httpReq("{$baseUrl}/campaigns/store", 'POST', [
    'name'           => 'Festive Luxury Expo ' . rand(100, 999),
    'campaign_type'  => 'Social Media Ads',
    'property_id'    => $propId,
    'start_date'     => date('Y-m-01'),
    'end_date'       => date('Y-m-28'),
    'budget'         => '75000.00',
    'actual_spend'   => '68500.00',
    'leads_count'    => 85,
    'qualified_leads'=> 34,
    'converted_leads'=> 5,
    'status'         => 'Active',
    'description'    => 'Targeted Meta and Google Discovery ad campaigns',
], $adminCookieFile, false);

logTest("Create Marketing Campaign (POST /campaigns/store)", isOkOrRedirect($createCampaignResp['code']), "Created campaign with leads");

$newCampaign = $pdo->query("SELECT * FROM marketing_campaigns ORDER BY id DESC LIMIT 1")->fetch();
$campaignId = $newCampaign['id'] ?? 1;

$campaignView = httpReq("{$baseUrl}/campaigns/view/{$campaignId}", 'GET', [], $adminCookieFile);
logTest("View Campaign Analytics (GET /campaigns/view/{$campaignId})", $campaignView['code'] === 200 && str_contains($campaignView['body'], 'Cost Per Lead (CPL)'));

// ========================================================================
// 7. PROPERTY DOCUMENTS & COMPLIANCE REPOSITORY
// ========================================================================
echo "\n--- 7. Compliance Documents & Verification Repository Suite ---\n";

$documentsIndex = httpReq("{$baseUrl}/documents", 'GET', [], $adminCookieFile);
logTest("Compliance Documents Repository (GET /documents)", $documentsIndex['code'] === 200 && str_contains($documentsIndex['body'], 'Legal & Property Document Management'));

// Store a compliance document
$createDocResp = httpReq("{$baseUrl}/documents/store", 'POST', [
    'title'               => 'Municipal Fire Safety NOC 2026',
    'document_category'   => 'Fire NOC',
    'property_id'         => $propId,
    'document_number'     => 'FIRE-NOC-' . rand(1000, 9999),
    'issue_date'          => date('Y-01-01'),
    'expiry_date'         => date('Y-12-31', strtotime('+1 year')),
    'issuing_authority'   => 'Municipal Fire Prevention Bureau',
    'verification_status' => 'Pending',
], $adminCookieFile, false);

logTest("Register Property Document (POST /documents/store)", isOkOrRedirect($createDocResp['code']), "Registered statutory document");

$newDoc = $pdo->query("SELECT * FROM property_documents ORDER BY id DESC LIMIT 1")->fetch();
$docId = $newDoc['id'] ?? 1;

$docView = httpReq("{$baseUrl}/documents/view/{$docId}", 'GET', [], $adminCookieFile);
logTest("View Document Detail & Audit Metadata (GET /documents/view/{$docId})", $docView['code'] === 200 && str_contains($docView['body'], 'Compliance File Profile'));

// Verify document
$verifyDocResp = httpReq("{$baseUrl}/documents/verify/{$docId}", 'POST', [
    'verification_status' => 'Verified',
    'rejection_reason'    => '',
], $adminCookieFile, false);

logTest("Perform Document Statutory Verification (POST /documents/verify/{$docId})", isOkOrRedirect($verifyDocResp['code']), "Approved document status");

$updatedDoc = $pdo->query("SELECT verification_status FROM property_documents WHERE id = {$docId}")->fetch();
logTest("Database Status Updated to Verified", $updatedDoc['verification_status'] === 'Verified');

// ========================================================================
// 8. PROPERTY LEGAL & STATUTORY VERIFICATIONS
// ========================================================================
echo "\n--- 8. Property Legal & Statutory Verifications Suite ---\n";

$verificationsIndex = httpReq("{$baseUrl}/verifications", 'GET', [], $adminCookieFile);
logTest("Property Verifications Directory (GET /verifications)", $verificationsIndex['code'] === 200 && str_contains($verificationsIndex['body'], 'Property Verification & Compliance Audits'));

$createVerifResp = httpReq("{$baseUrl}/verifications/store", 'POST', [
    'property_id'       => $propId,
    'verification_type' => 'RERA Compliance Check',
    'status'            => 'Pending',
    'scheduled_date'    => date('Y-m-d'),
    'checklist_notes'   => 'Checking RERA registration certificate and escrow reconciliation',
], $adminCookieFile, false);

logTest("Initiate Property Verification Audit (POST /verifications/store)", isOkOrRedirect($createVerifResp['code']), "Initiated verification check");

$newVerif = $pdo->query("SELECT * FROM property_verifications ORDER BY id DESC LIMIT 1")->fetch();
$verifId = $newVerif['id'] ?? 1;

$verifView = httpReq("{$baseUrl}/verifications/view/{$verifId}", 'GET', [], $adminCookieFile);
logTest("View Legal Verification Audit Screen (GET /verifications/view/{$verifId})", $verifView['code'] === 200 && str_contains($verifView['body'], 'Audit Findings & Evidence'));

// Issue verdict
$conductResp = httpReq("{$baseUrl}/verifications/conduct/{$verifId}", 'POST', [
    'status'   => 'Verified',
    'findings' => 'All approved floor plans and commencement certificates match municipal sanctions',
], $adminCookieFile, false);

logTest("Execute Verification Verdict (POST /verifications/conduct/{$verifId})", isOkOrRedirect($conductResp['code']), "Verdict issued: Verified");

$updatedVerif = $pdo->query("SELECT status FROM property_verifications WHERE id = {$verifId}")->fetch();
logTest("Verification Audit Status Set to Verified", $updatedVerif['status'] === 'Verified');

// ========================================================================
// 9. CENTRALIZED NOTIFICATIONS & REMINDER CENTER
// ========================================================================
echo "\n--- 9. Notifications & Reminder Center Suite ---\n";

$notifsIndex = httpReq("{$baseUrl}/notifications", 'GET', [], $adminCookieFile);
logTest("Notification Center (GET /notifications)", $notifsIndex['code'] === 200 && str_contains($notifsIndex['body'], 'Notification & Reminder Center'));

$unreadFilter = httpReq("{$baseUrl}/notifications?filter=unread", 'GET', [], $adminCookieFile);
logTest("Filter Unread Notifications (GET /notifications?filter=unread)", $unreadFilter['code'] === 200);

$urgentFilter = httpReq("{$baseUrl}/notifications?filter=urgent", 'GET', [], $adminCookieFile);
logTest("Filter Urgent Notifications (GET /notifications?filter=urgent)", $urgentFilter['code'] === 200);

// Broadcast internal reminder
$broadcastResp = httpReq("{$baseUrl}/notifications/create", 'POST', [
    'title'    => 'Statutory Fire Audit Scheduled',
    'message'  => 'Municipal inspector visit scheduled for tomorrow at 11 AM.',
    'type'     => 'verification',
    'priority' => 'high',
], $adminCookieFile, false);

logTest("Broadcast Internal Reminder (POST /notifications/create)", isOkOrRedirect($broadcastResp['code']), "Broadcasted reminder");

$latestNotif = $pdo->query("SELECT * FROM notifications ORDER BY id DESC LIMIT 1")->fetch();
$notifId = $latestNotif['id'] ?? 1;

$markReadResp = httpReq("{$baseUrl}/notifications/mark-read/{$notifId}", 'GET', [], $adminCookieFile, false);
logTest("Mark Notification as Read (GET /notifications/mark-read/{$notifId})", isOkOrRedirect($markReadResp['code']));

$markAllResp = httpReq("{$baseUrl}/notifications/mark-all-read", 'GET', [], $adminCookieFile, false);
logTest("Mark All Notifications Read (GET /notifications/mark-all-read)", isOkOrRedirect($markAllResp['code']));

// ========================================================================
// 10. CUSTOMER COMMUNICATION HISTORY & LOGS
// ========================================================================
echo "\n--- 10. Customer Multi-Channel Communication Suite ---\n";

$commsIndex = httpReq("{$baseUrl}/communications", 'GET', [], $adminCookieFile);
logTest("Customer Communications History (GET /communications)", $commsIndex['code'] === 200 && str_contains($commsIndex['body'], 'Customer Communication Logs'));

// Filter by channel
$filterCommResp = httpReq("{$baseUrl}/communications?channel=Phone+Call", 'GET', [], $adminCookieFile);
logTest("Filter by Phone Call Channel", $filterCommResp['code'] === 200);

// Store communication log
$firstCustomer = $pdo->query("SELECT id FROM customers LIMIT 1")->fetch();
$custId = $firstCustomer['id'] ?? 1;

$storeCommResp = httpReq("{$baseUrl}/communications/store", 'POST', [
    'customer_id'        => $custId,
    'channel'            => 'WhatsApp',
    'purpose'            => 'Booking Confirmation',
    'subject'            => 'Shared unit allocation agreement draft',
    'content'            => 'Sent client the agreement PDF and payment receipt for token advance',
    'status'             => 'Completed',
    'communication_date' => date('Y-m-d H:i:s'),
    'response_notes'     => 'Client acknowledged receipt and agreed to sign on Monday',
], $adminCookieFile, false);

logTest("Log Multi-Channel Interaction (POST /communications/store)", isOkOrRedirect($storeCommResp['code']), "Saved WhatsApp engagement");

$newComm = $pdo->query("SELECT * FROM customer_communications ORDER BY id DESC LIMIT 1")->fetch();
logTest("Communication Log Created in Database", !empty($newComm['comm_code']), "Generated code {$newComm['comm_code']}");

// ========================================================================
// 11. EXECUTIVE REPORTS & ANALYTICS HUB
// ========================================================================
echo "\n--- 11. Executive Reports & Analytics Hub Suite ---\n";

$reportsHub = httpReq("{$baseUrl}/reports", 'GET', [], $adminCookieFile);
logTest("Master Reports Hub (GET /reports)", $reportsHub['code'] === 200 && str_contains($reportsHub['body'], 'Executive Reports & Analytics Hub'));
logTest("Hub Displays Live Database Metrics", str_contains($reportsHub['body'], 'Gross Bookings Value') && str_contains($reportsHub['body'], 'Actual Collections'));

$salesReport = httpReq("{$baseUrl}/reports/sales?year=" . date('Y'), 'GET', [], $adminCookieFile);
logTest("Property Sales Report (GET /reports/sales)", $salesReport['code'] === 200 && str_contains($salesReport['body'], 'Property Sales & Revenue Turnover Report'));

$rentalReport = httpReq("{$baseUrl}/reports/rental", 'GET', [], $adminCookieFile);
logTest("Rental & Tenancy Report (GET /reports/rental)", $rentalReport['code'] === 200 && str_contains($rentalReport['body'], 'Rental Performance & Tenancy Audit'));

// ========================================================================
// 12. SYSTEM SETTINGS & DATA EXPORT
// ========================================================================
echo "\n--- 12. System Settings & Backup Suite ---\n";

$settingsIndex = httpReq("{$baseUrl}/settings?tab=general", 'GET', [], $adminCookieFile);
logTest("System Settings View (GET /settings)", $settingsIndex['code'] === 200 && str_contains($settingsIndex['body'], 'Settings & System Configuration'));

$settingsUpdateResp = httpReq("{$baseUrl}/settings/update", 'POST', [
    'tab'      => 'general',
    'settings' => [
        'app_name'         => 'Imperial Estates & Infrastructure ERP',
        'default_currency' => 'INR',
    ],
], $adminCookieFile, false);

logTest("Update System Settings (POST /settings/update)", isOkOrRedirect($settingsUpdateResp['code']), "Updated enterprise company name");

$updatedSetting = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'app_name'")->fetchColumn();
logTest("Setting Value Verified in Database", $updatedSetting === 'Imperial Estates & Infrastructure ERP');

// Data export JSON
$exportResp = httpReq("{$baseUrl}/settings/export?type=properties", 'GET', [], $adminCookieFile);
logTest("Export Data in Structured JSON (GET /settings/export?type=properties)", $exportResp['code'] === 200 && str_contains($exportResp['headers'], 'application/json'));

$backupResp = httpReq("{$baseUrl}/settings/backup", 'POST', [], $adminCookieFile, false);
logTest("Database Backup Snapshot Trigger (POST /settings/backup)", isOkOrRedirect($backupResp['code']), "Triggered backup snapshot");

// ========================================================================
// 13. OWNER PORTFOLIO INTEGRATION
// ========================================================================
echo "\n--- 13. Landlord Self-Service Portal Integration ---\n";

$ownerPortalResp = httpReq("{$baseUrl}/portal/owner", 'GET', [], $adminCookieFile);
logTest("Owner Portal Portfolio Page (GET /portal/owner)", $ownerPortalResp['code'] === 200 && str_contains($ownerPortalResp['body'], 'Landlord Portfolio'));

// ========================================================================
// 14. RBAC & AUDIT LOG ENFORCEMENT
// ========================================================================
echo "\n--- 14. RBAC & Audit Trail Integrity Checks ---\n";

// Sales Executive shouldn't have access to settings
$salesSettingsResp = httpReq("{$baseUrl}/settings", 'GET', [], $salesCookieFile);
logTest("Sales Role Blocked from System Settings", $salesSettingsResp['code'] === 403 || str_contains($salesSettingsResp['body'], 'Unauthorized') || str_contains($salesSettingsResp['body'], 'Access Denied'), "Access restricted by permission");

// Check Audit Log entry created for Phase 7 actions
$auditCount = (int)$pdo->query("SELECT COUNT(*) FROM audit_logs WHERE action IN ('OWNER_CREATED', 'AGENT_CREATED', 'EXPENSE_CREATED', 'CAMPAIGN_CREATED', 'DOCUMENT_UPLOADED', 'COMMUNICATION_LOGGED', 'SETTINGS_UPDATED', 'BACKUP_CREATED')")->fetchColumn();
logTest("Audit Trail Generated for Phase 7 Actions", $auditCount > 0, "Found {$auditCount} Phase 7 audit log records");

// ========================================================================
// FINAL SUMMARY
// ========================================================================
echo "\n========================================================================\n";
$totalTests = count($results);
$passedTests = count(array_filter($results, fn($r) => $r['status'] === 'PASS'));
$failedTests = $totalTests - $passedTests;

echo sprintf("TOTAL TESTS : %d\n", $totalTests);
echo sprintf("PASSED      : %d (%.1f%%)\n", $passedTests, ($passedTests / $totalTests) * 100);
echo sprintf("FAILED      : %d\n", $failedTests);
echo "========================================================================\n";

if ($allPassed) {
    echo ">>> ALL PHASE 7 AUTOMATED VERIFICATION TESTS PASSED SUCCESSFULLY! <<<\n\n";
    exit(0);
} else {
    echo ">>> SOME TESTS FAILED. PLEASE REVIEW LOG ABOVE. <<<\n\n";
    exit(1);
}
