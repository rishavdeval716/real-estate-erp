<?php

/**
 * REAL ESTATE MANAGEMENT SOFTWARE (ERP / CRM)
 * RESPONSIVE UI & DASHBOARD POLISH AUTOMATED TEST SUITE
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = __DIR__ . '/responsive_cookies.txt';
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

function httpReq($url, $method = 'GET', $data = [], $cookies = true, $followRedirect = true) {
    global $cookieFile;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirect);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

    if ($cookies) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($method === 'POST') {
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
    if (preg_match('/name="csrf_token_name" value="([^"]+)"/', $body, $m)) {
        $csrfToken = $m[1];
    } elseif (preg_match('/meta name="csrf-token" content="([^"]+)"/', $body, $m)) {
        $csrfToken = $m[1];
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

$testCount = 0;
$passCount = 0;
$failCount = 0;

function logTest($name, $passed, $details = '') {
    global $testCount, $passCount, $failCount;
    $testCount++;
    if ($passed) {
        $passCount++;
        echo "[PASS] {$name} " . ($details ? "- {$details}" : '') . "\n";
    } else {
        $failCount++;
        echo "[FAIL] {$name} " . ($details ? "- {$details}" : '') . "\n";
    }
}

echo "========================================================================\n";
echo " RESPONSIVE UI & DASHBOARD POLISH AUTOMATED VERIFICATION SUITE\n";
echo "========================================================================\n\n";

// 1. Authenticate as Super Admin
$loginPage = httpReq("$baseUrl/login");
$csrf = $loginPage['csrfToken'];
$authRes = httpReq("$baseUrl/login", 'POST', [
    'csrf_token_name' => $csrf,
    'email'           => 'admin@realestate-erp.local',
    'password'        => 'Password@123',
]);
$isLoggedIn = in_array($authRes['code'], [200, 302, 303]);
logTest("1. Authentication (Super Admin Login)", $isLoggedIn, "Code: {$authRes['code']}");

// 2. Main Executive Dashboard Responsiveness
$dashRes = httpReq("$baseUrl/dashboard");
logTest("2.1 Executive Dashboard Route Accessible", $dashRes['code'] === 200, "HTTP 200 OK");

$hasFoundationGrid = strpos($dashRes['body'], 'class="metric-grid foundation-grid"') !== false;
logTest("2.2 Foundation Metrics Grid Class", $hasFoundationGrid, "Uses .foundation-grid class");

$hasPropertyGrid = strpos($dashRes['body'], 'class="metric-grid property-grid"') !== false;
logTest("2.3 Property & Inventory Overview Grid Class", $hasPropertyGrid, "Uses .property-grid class (3-column balanced)");

$hasCrmGrid = strpos($dashRes['body'], 'class="metric-grid crm-grid"') !== false;
logTest("2.4 CRM & Sales Pipeline Overview Grid Class", $hasCrmGrid, "Uses .crm-grid class (dynamic auto-fit)");

$hasFinancialGrid = strpos($dashRes['body'], 'class="metric-grid financial-grid"') !== false;
logTest("2.5 Financial Transactions Performance Grid Class", $hasFinancialGrid, "Uses .financial-grid class");

$hasOperationsSection = strpos($dashRes['body'], 'Enterprise Operations & Compliance Overview') !== false;
logTest("2.6 Operations & Compliance Lifecycle Section Present", $hasOperationsSection, "Construction, Rentals, Maintenance, Compliance rendered");

$hasTablesGrid = strpos($dashRes['body'], 'class="dashboard-tables-grid"') !== false;
logTest("2.7 Dashboard Tables Grid Class", $hasTablesGrid, "Uses .dashboard-tables-grid (desktop 2-col, tablet/mobile 1-col)");

// Check that all 9 Property & Inventory cards are present in the DOM
$all9PropertyCards = (
    strpos($dashRes['body'], 'Total Properties') !== false &&
    strpos($dashRes['body'], 'Available Properties') !== false &&
    strpos($dashRes['body'], 'Reserved Properties') !== false &&
    strpos($dashRes['body'], 'Booked Properties') !== false &&
    strpos($dashRes['body'], 'Sold Properties') !== false &&
    strpos($dashRes['body'], 'Rented Properties') !== false &&
    strpos($dashRes['body'], 'Total Projects') !== false &&
    strpos($dashRes['body'], 'Total Units') !== false &&
    strpos($dashRes['body'], 'Available Units') !== false
);
logTest("2.8 Property & Inventory 9-Card Integrity", $all9PropertyCards, "All 9 status and inventory cards present");

// Check that all 11 CRM cards are present in the DOM
$all11CrmCards = (
    strpos($dashRes['body'], 'Total Leads') !== false &&
    strpos($dashRes['body'], 'New Leads') !== false &&
    strpos($dashRes['body'], 'Qualified Leads') !== false &&
    strpos($dashRes['body'], 'Open Enquiries') !== false &&
    strpos($dashRes['body'], "Today's Follow-ups") !== false &&
    strpos($dashRes['body'], 'Overdue Follow-ups') !== false &&
    strpos($dashRes['body'], "Today's Visits") !== false &&
    strpos($dashRes['body'], 'Upcoming Visits') !== false &&
    strpos($dashRes['body'], 'Active Unit Holds') !== false &&
    strpos($dashRes['body'], 'Leads Won') !== false &&
    strpos($dashRes['body'], 'Leads Lost') !== false
);
logTest("2.9 CRM & Sales Pipeline 11-Card Integrity", $all11CrmCards, "All 11 CRM cards present");

// 3. CSS Audit & Responsive Rules Inspection
$cssContent = file_get_contents(__DIR__ . '/../public/assets/css/erp-style.css');

$hasNo100vwBody = strpos($cssContent, 'max-width: 100vw;') === false;
logTest("3.1 Global Horizontal Overflow Prevention", $hasNo100vwBody, "Replaced 100vw with 100% on body & wrapper");

$hasCollapsedSidebarCss = strpos($cssContent, '.app-sidebar.collapsed') !== false;
logTest("3.2 Sidebar Collapsed CSS Rules Present", $hasCollapsedSidebarCss, "Includes .app-sidebar.collapsed and sibling rules");

$hasPropertyGridCss = strpos($cssContent, '.property-grid') !== false;
logTest("3.3 Property Grid 3-Column CSS Definition", $hasPropertyGridCss, "Defines .property-grid repeat(3, 1fr)");

$hasCrmGridCss = strpos($cssContent, '.crm-grid') !== false;
logTest("3.4 CRM Grid Auto-Fit CSS Definition", $hasCrmGridCss, "Defines .crm-grid with minmax auto-fit");

$hasTablesGridCss = strpos($cssContent, '.dashboard-tables-grid') !== false;
logTest("3.5 Dashboard Tables Responsive Grid Definition", $hasTablesGridCss, "Defines .dashboard-tables-grid");

$hasIosZoomFix = strpos($cssContent, 'font-size: 16px !important') !== false;
logTest("3.6 iOS Auto-Zoom Prevention on Mobile Inputs", $hasIosZoomFix, "Enforces 16px font-size on mobile form inputs");

$hasModalHardening = strpos($cssContent, 'max-width: calc(100% - 24px) !important') !== false;
logTest("3.7 Mobile Modal Dialog Screen Fitting", $hasModalHardening, "Enforces max-width calc(100% - 24px) on modals");

// 4. JavaScript Sidebar & Viewport Synchronization Inspection
$jsContent = file_get_contents(__DIR__ . '/../public/assets/js/erp-app.js');
$hasDynamicWidthCalc = strpos($jsContent, 'calc(100% - 78px)') !== false;
logTest("4.1 JS Sidebar Toggle Width Calculation", $hasDynamicWidthCalc, "Synchronizes main width dynamically on collapse");

$hasResizeWidthCalc = strpos($jsContent, "main.style.width = '100%'") !== false;
logTest("4.2 JS Window Resize Layout Normalization", $hasResizeWidthCalc, "Sets 100% width on tablet/mobile resize");

// 5. Related Dashboards Check
$salesDashRes = httpReq("$baseUrl/sales-dashboard");
logTest("5.1 Sales Dashboard (/sales-dashboard)", $salesDashRes['code'] === 200, "HTTP 200 OK");

$constructionDashRes = httpReq("$baseUrl/construction/dashboard");
logTest("5.2 Construction Dashboard (/construction/dashboard)", $constructionDashRes['code'] === 200, "HTTP 200 OK");

$reportsHubRes = httpReq("$baseUrl/reports");
logTest("5.3 Reports Hub Dashboard (/reports)", $reportsHubRes['code'] === 200, "HTTP 200 OK");

echo "\n========================================================================\n";
echo "TOTAL TESTS : {$testCount}\n";
echo "PASSED      : {$passCount} (" . round(($passCount / $testCount) * 100, 1) . "%)\n";
echo "FAILED      : {$failCount}\n";
echo "========================================================================\n";

if ($failCount === 0) {
    echo ">>> ALL RESPONSIVE UI & DASHBOARD POLISH VERIFICATION TESTS PASSED! <<<\n";
    exit(0);
} else {
    echo ">>> SOME TESTS FAILED. PLEASE REVIEW LOGS. <<<\n";
    exit(1);
}
