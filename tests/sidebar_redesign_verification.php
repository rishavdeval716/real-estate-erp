<?php
/**
 * REAL ESTATE ERP - SIDEBAR UX REDESIGN AUTOMATED VERIFICATION SUITE
 * 
 * Verifies:
 * 1. Super Admin Authentication & Session
 * 2. Presence of all 10 Navigation Categories
 * 3. Exact 100% preservation of all 66 modules and routes
 * 4. In-Sidebar Live Search elements (#sidebarSearchInput, #sidebarSearchClear, #sidebarNoResults)
 * 5. Accessibility semantics (button tags, aria-expanded, aria-controls, role="region")
 * 6. Smart Active State across multiple distinct route categories
 * 7. RBAC integrity: Sales Executive sees only permitted modules, restricted modules/categories omitted
 * 8. CSS & JS integrity: Accordion styling, transitions, collapsed overrides, live search logic
 */

$baseUrl = 'http://localhost:8080';
$adminCookieFile = __DIR__ . '/test_admin_sidebar_cookies.txt';
$salesCookieFile = __DIR__ . '/test_sales_sidebar_cookies.txt';

if (file_exists($adminCookieFile)) unlink($adminCookieFile);
if (file_exists($salesCookieFile)) unlink($salesCookieFile);

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function logTest($name, $passed, $details = '') {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    if ($passed) {
        $passedTests++;
        echo "[PASS] $name" . ($details ? " - $details" : "") . "\n";
    } else {
        $failedTests++;
        echo "[FAIL] $name" . ($details ? " - $details" : "") . "\n";
    }
}

function httpReq($url, $method = 'GET', $data = [], $cookieFile = null, $followRedirect = true) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirect);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, true);

    if ($cookieFile) {
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

echo "========================================================================\n";
echo " SIDEBAR UX REDESIGN & ACCORDION NAVIGATION VERIFICATION SUITE\n";
echo "========================================================================\n\n";

// --- 1. SUPER ADMIN AUTHENTICATION ---
echo "--- 1. Super Admin Authentication & Dashboard Fetch ---\n";
$loginPage = httpReq("$baseUrl/login", 'GET', [], $adminCookieFile);
$csrf = $loginPage['csrfToken'];
$authRes = httpReq("$baseUrl/login", 'POST', [
    'csrf_token_name' => $csrf,
    'email'           => 'admin@realestate-erp.local',
    'password'        => 'Password@123',
], $adminCookieFile);
$isLoggedIn = in_array($authRes['code'], [200, 302, 303]);
logTest("1.1 Super Admin Authentication", $isLoggedIn, "Code: {$authRes['code']}");

$dashRes = httpReq("$baseUrl/dashboard", 'GET', [], $adminCookieFile);
$dashHtml = $dashRes['body'];
logTest("1.2 Dashboard Route Accessible", $dashRes['code'] === 200 && strpos($dashHtml, 'app-sidebar') !== false, "Sidebar component rendered");

// --- 2. PRESENCE OF ALL 10 CATEGORIES ---
echo "\n--- 2. Presence of All 10 Navigation Categories ---\n";
$expectedCategories = [
    'overview' => 'Overview',
    'property-management' => 'Property Management',
    'crm-sales' => 'CRM & Sales',
    'sales-transactions' => 'Sales & Transactions',
    'rentals-leasing' => 'Rentals & Leasing',
    'facility-operations' => 'Facility & Operations',
    'construction-handover' => 'Construction & Handover',
    'portals' => 'Self-Service Portals',
    'compliance-reports' => 'Compliance & Reports',
    'administration' => 'Administration'
];

foreach ($expectedCategories as $catId => $catTitle) {
    $hasCat = strpos($dashHtml, 'data-category="' . $catId . '"') !== false;
    $hasTitle = (strpos($dashHtml, $catTitle) !== false || strpos($dashHtml, htmlspecialchars($catTitle, ENT_QUOTES, 'UTF-8')) !== false);
    logTest("2. Category: $catTitle", $hasCat && $hasTitle, "data-category=\"$catId\" present with title");
}

// --- 3. ALL 66 MODULES STRICTLY PRESERVED ---
echo "\n--- 3. Strict 100% Preservation of All 66 Modules & Routes ---\n";
$expectedModules = [
    // A. Overview (2)
    ['Dashboard', '/dashboard'],
    ['Notifications & Alerts', '/notifications'],

    // B. Property Management (12)
    ['Properties', '/properties'],
    ['Property Types', '/property-types'],
    ['Property Units', '/units'],
    ['Projects', '/projects'],
    ['Locations', '/locations'],
    ['Amenities', '/amenities'],
    ['Availability', '/availability'],
    ['Pricing', '/pricing'],
    ['Unit Inventory', '/inventory'],
    ['Property Owners', '/owners'],
    ['Property Documents', '/documents'],
    ['Property Verifications', '/verifications'],

    // C. CRM & Sales (10)
    ['Leads', '/leads'],
    ['Sales Pipeline', '/pipeline'],
    ['Enquiries', '/enquiries'],
    ['Follow-ups', '/followups'],
    ['Site Visits', '/site-visits'],
    ['Unit Holds', '/unit-holds'],
    ['Lead Sources', '/lead-sources'],
    ['Agents & Brokers', '/agents'],
    ['Marketing Campaigns', '/campaigns'],
    ['Customer Comms', '/communications'],

    // D. Sales & Transactions (9)
    ['Sales Dashboard', '/sales-dashboard'],
    ['Customers', '/customers'],
    ['Bookings', '/bookings'],
    ['Agreements', '/agreements'],
    ['Payments', '/payments'],
    ['Invoices', '/invoices'],
    ['Receipts', '/receipts'],
    ['Commissions', '/commissions'],
    ['Property Expenses', '/expenses'],

    // E. Rentals & Leasing (5)
    ['Tenants & KYC', '/tenants'],
    ['Lease Agreements', '/leases'],
    ['Security Deposits', '/deposits'],
    ['Rent Demands', '/rent-demands'],
    ['Rent Collections', '/rent-collections'],

    // F. Facility & Operations (6)
    ['Work Orders', '/maintenance'],
    ['Resident Complaints', '/complaints'],
    ['Preventive AMC', '/preventive-maintenance'],
    ['Facility Assets', '/facility-assets'],
    ['Technicians', '/technicians'],
    ['CAM Billing', '/cam-charges'],

    // G. Construction & Handover (8)
    ['Site Dashboard', '/construction/dashboard'],
    ['Milestones & Progress', '/construction/milestones'],
    ['Daily Site Logs', '/construction/daily-logs'],
    ['Contractors & Vendors', '/contractors'],
    ['Work Contracts', '/construction/work-orders'],
    ['Material Indents', '/procurement'],
    ['Quality Inspections', '/inspections'],
    ['Possession Handover', '/handover'],

    // H. Self-Service Portals (3)
    ['Owner Portal', '/portal/owner'],
    ['Tenant Portal', '/portal/tenant'],
    ['Partner Portal', '/portal/partner'],

    // I. Compliance & Reports (4)
    ['Reports Hub', '/reports'],
    ['GST Reports', '/reports/gst'],
    ['TDS & Form 16A', '/reports/tds'],
    ['Receivables Ageing', '/reports/receivables'],

    // J. Administration (7)
    ['Users', '/users'],
    ['Roles', '/roles'],
    ['Permissions', '/permissions'],
    ['Company', '/company'],
    ['Branches', '/branches'],
    ['Audit Logs', '/audit-logs'],
    ['System Settings', '/settings']
];

logTest("3.0 Expected Module Count Checklist", count($expectedModules) === 66, "Exactly 66 modules in definition");

$allModulesFound = true;
$missingModules = [];
foreach ($expectedModules as $idx => [$name, $route]) {
    $hasRoute = strpos($dashHtml, 'href="' . $route . '"') !== false;
    $hasName = (strpos($dashHtml, $name) !== false || strpos($dashHtml, htmlspecialchars($name, ENT_QUOTES, 'UTF-8')) !== false);
    if (!$hasRoute || !$hasName) {
        $allModulesFound = false;
        $missingModules[] = "$name ($route)";
    }
}
logTest("3.1 All 66 Modules Rendered in Sidebar", $allModulesFound, $allModulesFound ? "Found all 66 items" : "Missing: " . implode(', ', $missingModules));

// Check uniqueness - no duplicated routes in sidebar
$hrefOccurrences = [];
if (preg_match('/<aside class="app-sidebar">([\s\S]*?)<\/aside>/', $dashHtml, $sideMatch)) {
    foreach ($expectedModules as [$name, $route]) {
        $sideCount = substr_count($sideMatch[1], 'href="' . $route . '"');
        if ($sideCount !== 1) {
            $hrefOccurrences[] = "$route ($sideCount times in sidebar)";
        }
    }
}
logTest("3.2 No Duplicate Navigation Routes in Sidebar", count($hrefOccurrences) === 0, count($hrefOccurrences) === 0 ? "Each route appears once in sidebar" : "Duplicates: " . implode(', ', $hrefOccurrences));

// --- 4. ACCESSIBILITY & ACCORDION SEMANTICS ---
echo "\n--- 4. Accessibility Semantics & Accordion Structure ---\n";
$hasSearchInput = strpos($dashHtml, 'id="sidebarSearchInput"') !== false;
logTest("4.1 Search Input Element Present", $hasSearchInput, "id=\"sidebarSearchInput\" rendered");

$hasSearchClear = strpos($dashHtml, 'id="sidebarSearchClear"') !== false;
logTest("4.2 Search Clear Button Present", $hasSearchClear, "id=\"sidebarSearchClear\" rendered");

$hasNoResults = strpos($dashHtml, 'id="sidebarNoResults"') !== false;
logTest("4.3 No Results Feedback Element Present", $hasNoResults, "id=\"sidebarNoResults\" rendered");

$hasAriaExpanded = strpos($dashHtml, 'aria-expanded=') !== false;
logTest("4.4 Accordion Buttons Have aria-expanded", $hasAriaExpanded, "Proper ARIA expansion state");

$hasAriaControls = strpos($dashHtml, 'aria-controls=') !== false;
logTest("4.5 Accordion Buttons Have aria-controls", $hasAriaControls, "Links buttons to submenus");

$hasSubmenuRoles = strpos($dashHtml, 'role="region"') !== false;
logTest("4.6 Submenus Have role=\"region\"", $hasSubmenuRoles, "Accessible regions for accordion panels");

$hasChevrons = strpos($dashHtml, 'category-chevron') !== false;
logTest("4.7 Category Chevrons Present", $hasChevrons, "Visual indicator chevrons rendered");

// --- 5. SMART ACTIVE STATE ---
echo "\n--- 5. Smart Active State across Modules ---\n";

// Test 5.1: On /dashboard, Overview category should be open
$dashOverviewOpen = preg_match('/data-category="overview"[^>]*class="[^"]*open/', $dashHtml);
logTest("5.1 /dashboard -> Overview Category Open", $dashOverviewOpen === 1, "data-category=\"overview\" has .open");

// Test 5.2: On /properties, Property Management should be open, others closed
$propRes = httpReq("$baseUrl/properties", 'GET', [], $adminCookieFile);
$propHtml = $propRes['body'];
$propCatOpen = preg_match('/data-category="property-management"[^>]*class="[^"]*open/', $propHtml);
$crmCatNotOpen = preg_match('/data-category="crm-sales"[^>]*class="[^"]*open/', $propHtml);
logTest("5.2 /properties -> Property Management Open", $propCatOpen === 1 && $crmCatNotOpen === 0, "Property Management is open, CRM & Sales is closed");

// Test 5.3: On /reports/gst, Compliance & Reports should be open
$gstRes = httpReq("$baseUrl/reports/gst", 'GET', [], $adminCookieFile);
$gstHtml = $gstRes['body'];
$gstCatOpen = preg_match('/data-category="compliance-reports"[^>]*class="[^"]*open/', $gstHtml);
$propCatNotOpen = preg_match('/data-category="property-management"[^>]*class="[^"]*open/', $gstHtml);
logTest("5.3 /reports/gst -> Compliance & Reports Open", $gstCatOpen === 1 && $propCatNotOpen === 0, "Compliance & Reports is open, Property Management is closed");

// Test 5.4: On /users, Administration should be open
$usersRes = httpReq("$baseUrl/users", 'GET', [], $adminCookieFile);
$usersHtml = $usersRes['body'];
$adminCatOpen = preg_match('/data-category="administration"[^>]*class="[^"]*open/', $usersHtml);
logTest("5.4 /users -> Administration Category Open", $adminCatOpen === 1, "Administration is open");

// --- 6. RBAC PERMISSION INTEGRITY ---
echo "\n--- 6. RBAC Integrity (Non-Admin User View) ---\n";
// Log in as sales user with dedicated cookie jar
$agentLoginPage = httpReq("$baseUrl/login", 'GET', [], $salesCookieFile);
$agentCsrf = $agentLoginPage['csrfToken'];
$agentLogin = httpReq("$baseUrl/login", 'POST', [
    'csrf_token_name' => $agentCsrf,
    'email'           => 'sales@realestate-erp.local',
    'password'        => 'Password@123',
], $salesCookieFile, false);
$agentPage = httpReq("$baseUrl/leads", 'GET', [], $salesCookieFile);
$agentHtml = $agentPage['body'];

// Sales Executive should NOT see administration settings, users, roles, etc.
$agentHasSettings = strpos($agentHtml, 'href="/settings"') !== false;
$agentHasUsers = strpos($agentHtml, 'href="/users"') !== false;
logTest("6.1 Restricted Modules Hidden for Non-Admin User", !$agentHasSettings && !$agentHasUsers, "Settings and Users excluded by RBAC");

// Sales Executive SHOULD see CRM leads and pipeline
$agentHasLeads = strpos($agentHtml, 'href="/leads"') !== false;
$agentHasPipeline = strpos($agentHtml, 'href="/pipeline"') !== false;
logTest("6.2 Permitted Modules Visible for Non-Admin User", $agentHasLeads && $agentHasPipeline, "Leads and Pipeline visible (HTTP {$agentPage['code']})");

// --- 7. CSS & JS INTEGRITY ---
echo "\n--- 7. CSS & JS Code Integrity ---\n";
$cssContent = file_get_contents(__DIR__ . '/../public/assets/css/erp-style.css');
$jsContent = file_get_contents(__DIR__ . '/../public/assets/js/erp-app.js');

logTest("7.1 CSS: .sidebar-search-box Defined", strpos($cssContent, '.sidebar-search-box') !== false, "Search container styling");
logTest("7.2 CSS: .category-header Defined", strpos($cssContent, '.category-header') !== false, "Accordion header button styling");
logTest("7.3 CSS: .category-chevron Rotation Defined", strpos($cssContent, 'transform: rotate(180deg)') !== false, "Chevron 180deg rotation");
logTest("7.4 CSS: .category-submenu Accordion Defined", strpos($cssContent, '.category-submenu') !== false && strpos($cssContent, 'max-height: 1500px') !== false, "Collapsible transition animation");
logTest("7.5 CSS: Collapsed Sidebar Rules Preserved", strpos($cssContent, '.app-sidebar.collapsed') !== false && strpos($cssContent, '.category-submenu') !== false, "Collapsed 78px rules defined");
logTest("7.6 JS: Accordion Toggle Click Handlers", strpos($jsContent, 'categoryToggles') !== false, "Event listeners on category-header");
logTest("7.7 JS: Live Search Input Handler", strpos($jsContent, 'sidebarSearchInput') !== false && strpos($jsContent, 'performSearch') !== false, "Live filtering on input event");

echo "\n========================================================================\n";
echo "TOTAL TESTS : $totalTests\n";
echo "PASSED      : $passedTests (" . round(($passedTests / $totalTests) * 100, 1) . "%)\n";
echo "FAILED      : $failedTests\n";
echo "========================================================================\n";

if (file_exists($adminCookieFile)) unlink($adminCookieFile);
if (file_exists($salesCookieFile)) unlink($salesCookieFile);

if ($failedTests === 0) {
    echo ">>> ALL SIDEBAR REDESIGN & ACCORDION NAVIGATION TESTS PASSED! <<<\n";
    exit(0);
} else {
    echo ">>> SOME TESTS FAILED! <<<\n";
    exit(1);
}
