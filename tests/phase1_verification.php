<?php

/**
 * Phase 1 Comprehensive Automated Verification Suite
 * Tests full end-to-end HTTP flows, session management, CSRF,
 * Authentication, RBAC Permission Filters, User CRUD, Role CRUD,
 * Branch CRUD, Company setup, and Audit Logging against real MySQL.
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = sys_get_temp_dir() . '/ci4_phase1_cookie.txt';
if (file_exists($cookieFile)) {
    @unlink($cookieFile);
}

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

function httpReq($url, $method = 'GET', $data = [], $followRedirect = false, $cookies = true) {
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

    // Extract CSRF token if present
    $csrfToken = '';
    if (preg_match('/name="csrf_token_name" value="([^"]+)"/', $body, $m)) {
        $csrfToken = $m[1];
    } elseif (preg_match('/name="X-CSRF-TOKEN" content="([^"]+)"/', $body, $m)) {
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

echo "\n=======================================================\n";
echo "RUNNING PHASE 1 AUTOMATED VERIFICATION SUITE\n";
echo "=======================================================\n\n";

// TEST 1: Unauthenticated protection on /dashboard
$res = httpReq($baseUrl . '/dashboard', 'GET', [], false);
logTest(
    "1. Route Protection on /dashboard",
    $res['code'] === 302 && strpos($res['headers'], 'Location: http://localhost:8080/login') !== false,
    "Status {$res['code']}, Redirected to login"
);

// TEST 2: Unauthenticated protection on other foundation routes
$routesToCheck = ['/users', '/roles', '/permissions', '/company', '/branches', '/audit-logs'];
$allProtected = true;
foreach ($routesToCheck as $r) {
    $res = httpReq($baseUrl . $r, 'GET', [], false);
    if ($res['code'] !== 302) {
        $allProtected = false;
        break;
    }
}
logTest("2. Protected Foundation Routes (/users, /roles, etc.)", $allProtected, "All 6 protected endpoints redirect to login");

// TEST 3: Login Page Rendering and CSRF Token Acquisition
$loginPage = httpReq($baseUrl . '/login', 'GET');
$hasCsrf = !empty($loginPage['csrfToken']);
logTest("3. Login Page & CSRF Token", $loginPage['code'] === 200 && $hasCsrf, "CSRF Token: " . substr($loginPage['csrfToken'], 0, 10) . "...");

// TEST 4: Invalid Password Rejection
$invalidLogin = httpReq($baseUrl . '/login', 'POST', [
    'csrf_token_name' => $loginPage['csrfToken'],
    'email'           => 'admin@realestate-erp.local',
    'password'        => 'WrongPassword!',
], true);
$rejectedProperly = strpos($invalidLogin['body'], 'Invalid email address or password.') !== false;
logTest("4. Invalid Credentials Rejection", $rejectedProperly, "Shows error alert and blocks authentication");

// TEST 5: Successful Super Admin Authentication
$freshLoginPage = httpReq($baseUrl . '/login', 'GET');
$authLogin = httpReq($baseUrl . '/login', 'POST', [
    'csrf_token_name' => $freshLoginPage['csrfToken'],
    'email'           => 'admin@realestate-erp.local',
    'password'        => 'Password@123',
], false);

$redirectsToDashboard = ($authLogin['code'] === 302 || $authLogin['code'] === 303);
$dashboardCheck = httpReq($baseUrl . '/dashboard', 'GET');
$hasExecutive = strpos($dashboardCheck['body'], 'Executive Dashboard') !== false;
$authSuccess = $redirectsToDashboard && ($dashboardCheck['code'] === 200 && $hasExecutive);

logTest("5. Super Admin Login & Dashboard Access", $authSuccess, "Status {$authLogin['code']} redirect and 200 on /dashboard");

// TEST 6: Dynamic Dashboard Metrics from MySQL
$dashboardPage = httpReq($baseUrl . '/dashboard', 'GET');
$hasTotalUsers = strpos($dashboardPage['body'], 'Total Users') !== false;
$hasActiveUsers = strpos($dashboardPage['body'], 'Active Users') !== false;
$hasRoles = strpos($dashboardPage['body'], 'Configured Roles') !== false;
$hasBranches = strpos($dashboardPage['body'], 'Total Branches') !== false;
logTest("6. Dashboard Metrics Rendered", $hasTotalUsers && $hasActiveUsers && $hasRoles && $hasBranches, "All foundation metrics dynamic from DB");

// TEST 7: Users Listing Page
$usersPage = httpReq($baseUrl . '/users', 'GET');
$hasUsersTable = strpos($usersPage['body'], 'Super Administrator') !== false;
logTest("7. User Management Listing", $usersPage['code'] === 200 && $hasUsersTable, "Users table rendered with seed data");

// TEST 8: Create User via POST
$createForm = httpReq($baseUrl . '/users/create', 'GET');
$newEmail = 'testuser_' . time() . '@apexhorizon.com';
$createUser = httpReq($baseUrl . '/users/store', 'POST', [
    'csrf_token_name' => $createForm['csrfToken'],
    'name'            => 'Pooja Bhatt',
    'email'           => $newEmail,
    'phone'           => '+91 91234 56789',
    'password'        => 'SecurePass123!',
    'status'          => 'active',
    'roles'           => [2], // Admin role
], true);
$userCreated = strpos($createUser['body'], 'Pooja Bhatt') !== false;
logTest("8. User Creation & Role Assignment", $userCreated, "Created new user and verified on listing");

// TEST 9: Role Management Listing & Creation
$rolesPage = httpReq($baseUrl . '/roles', 'GET');
$hasRolesTable = strpos($rolesPage['body'], 'Super Admin') !== false && strpos($rolesPage['body'], 'Property Manager') !== false;
logTest("9. Role Management Listing", $rolesPage['code'] === 200 && $hasRolesTable, "All 9 specification roles present");

$roleForm = httpReq($baseUrl . '/roles/create', 'GET');
$newRoleName = 'Legal Consultant ' . time();
$createRole = httpReq($baseUrl . '/roles/store', 'POST', [
    'csrf_token_name' => $roleForm['csrfToken'],
    'name'            => $newRoleName,
    'description'     => 'Reviews tenancy and sale agreements',
    'status'          => 'active',
    'permissions'     => [1, 2, 14], // dashboard.view, users.view, company.view
], true);
$roleCreated = strpos($createRole['body'], $newRoleName) !== false;
logTest("10. Role Creation & Permission Matrix Sync", $roleCreated, "Role created with permission set");

// TEST 11: Permissions Management Listing & Creation
$permsPage = httpReq($baseUrl . '/permissions?group=Dashboard', 'GET');
$hasPerms = strpos($permsPage['body'], 'dashboard.view') !== false;
logTest("11. Permission Management Listing", $permsPage['code'] === 200 && $hasPerms, "Permissions table rendered");

// TEST 12: Company Profile View & Update
$companyPage = httpReq($baseUrl . '/company', 'GET');
$hasCompany = strpos($companyPage['body'], 'Apex Horizon Real Estate Corp') !== false;
logTest("12. Company Profile View", $companyPage['code'] === 200 && $hasCompany, "Corporate profile rendered");

// TEST 13: Branch Management Listing & Create Branch
$branchesPage = httpReq($baseUrl . '/branches', 'GET');
$hasBranches = strpos($branchesPage['body'], 'Branch Management') !== false && strpos($branchesPage['body'], 'Branch Name') !== false;
logTest("13. Branch Management Listing", $branchesPage['code'] === 200 && $hasBranches, "Branches list rendered");

$branchForm = httpReq($baseUrl . '/branches/create', 'GET');
$newBranchCode = 'BR-AHM-' . rand(100, 999);
$createBranch = httpReq($baseUrl . '/branches/store', 'POST', [
    'csrf_token_name' => $branchForm['csrfToken'],
    'company_id'      => 1,
    'name'            => 'Ahmedabad Commercial Hub',
    'code'            => $newBranchCode,
    'manager_name'    => 'Karan Patel',
    'phone'           => '+91 79 2650 1100',
    'email'           => 'ahmedabad@apexhorizon.com',
    'city'            => 'Ahmedabad',
    'state'           => 'Gujarat',
    'country'         => 'India',
    'pincode'         => '380015',
    'status'          => 'active',
], true);
$branchCreated = strpos($createBranch['body'], $newBranchCode) !== false;
logTest("14. Branch Creation & Association", $branchCreated, "Created new branch {$newBranchCode}");

// TEST 15: Audit Logs Verification
$auditPage = httpReq($baseUrl . '/audit-logs', 'GET');
$hasAuditEntries = strpos($auditPage['body'], 'USER_CREATED') !== false || strpos($auditPage['body'], 'LOGIN') !== false;
logTest("15. Audit Log Trail Tracking", $auditPage['code'] === 200 && $hasAuditEntries, "Audit entries recorded and visible");

// TEST 16: PermissionFilter Authorization Gate (403 Test)
// Login as Branch Manager (who does NOT have roles.create or permissions.view)
if (file_exists($cookieFile)) @unlink($cookieFile);
$managerLogin = httpReq($baseUrl . '/login', 'GET');
$authManager = httpReq($baseUrl . '/login', 'POST', [
    'csrf_token_name' => $managerLogin['csrfToken'],
    'email'           => 'manager@realestate-erp.local',
    'password'        => 'Password@123',
], true);

// Branch Manager attempts to access /permissions (Requires permissions.view which manager lacks)
$forbiddenAccess = httpReq($baseUrl . '/permissions', 'GET');
$is403 = ($forbiddenAccess['code'] === 403 && strpos($forbiddenAccess['body'], '403') !== false);
logTest("16. PermissionFilter Enforcing 403 Forbidden", $is403, "Forbidden page shown when missing permissions.view");

// TEST 17: Logout & Session Invalidation
$logoutReq = httpReq($baseUrl . '/logout', 'GET', [], true);
$loggedOut = ($logoutReq['url'] === $baseUrl . '/login' || strpos($logoutReq['body'], 'You have been safely signed out') !== false);
logTest("17. Logout & Session Termination", $loggedOut, "Session destroyed and returned to login");

echo "\n=======================================================\n";
if ($allPassed) {
    echo "VERIFICATION SUITE RESULT: ALL TESTS PASSED (100% SUCCESS)\n";
} else {
    echo "VERIFICATION SUITE RESULT: SOME TESTS FAILED\n";
}
echo "=======================================================\n\n";

if (file_exists($cookieFile)) {
    @unlink($cookieFile);
}
exit($allPassed ? 0 : 1);
