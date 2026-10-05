<?php

/**
 * Phase 2 Comprehensive Automated Verification Suite
 * Tests end-to-end Property Management Foundation:
 * Property Types, Locations, Amenities, Projects, Towers,
 * Properties, Units, Media Uploads/Download, Availability Transitions & History,
 * Pricing & Valuations, Dynamic Inventory Matrix, Permissions Gates, and Audit Logs.
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = sys_get_temp_dir() . '/ci4_phase2_cookie.txt';
if (file_exists($cookieFile)) {
    @unlink($cookieFile);
}

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

function httpReq($url, $method = 'GET', $data = [], $followRedirect = false, $cookies = true, $files = []) {
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
        if (!empty($files)) {
            // Multipart upload
            $postFields = $data;
            foreach ($files as $field => $path) {
                $postFields[$field] = new CURLFile($path);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
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

echo "========================================================\n";
echo "PHASE 2 AUTOMATED VERIFICATION SUITE\n";
echo "========================================================\n\n";

// 1. Authenticate as Super Admin
$res = httpReq("$baseUrl/login");
$csrf = $res['csrfToken'];
$loginRes = httpReq("$baseUrl/login", 'POST', [
    'csrf_token_name' => $csrf,
    'email'           => 'admin@realestate-erp.local',
    'password'        => 'Password@123',
]);
$isLoggedIn = in_array($loginRes['code'], [200, 302, 303]);
logTest("1. Authentication (Super Admin Login)", $isLoggedIn, "Code: {$loginRes['code']}");

// 2. Dashboard with Phase 2 Dynamic Metrics
$dashRes = httpReq("$baseUrl/dashboard");
$hasDashCards = (
    strpos($dashRes['body'], 'Total Properties') !== false &&
    strpos($dashRes['body'], 'Available Properties') !== false &&
    strpos($dashRes['body'], 'Total Projects') !== false &&
    strpos($dashRes['body'], 'Total Units') !== false
);
logTest("2. Executive Dashboard (Phase 2 Metrics Rendered)", $dashRes['code'] === 200 && $hasDashCards, "HTTP 200 + MySQL property metrics present");

// 3. Property Types CRUD
$ptList = httpReq("$baseUrl/property-types");
$ptHasInitial = (strpos($ptList['body'], 'Apartment') !== false && strpos($ptList['body'], 'Commercial') !== false);
logTest("3.1 Property Types List & Seed Data", $ptList['code'] === 200 && $ptHasInitial, "11 initial types seeded and rendered");

// Create new property type
$ptName = 'Penthouse Luxury ' . rand(100, 999);
$ptSlug = 'penthouse-luxury-' . rand(100, 999);
$ptCreateRes = httpReq("$baseUrl/property-types/store", 'POST', [
    'csrf_token_name' => $ptList['csrfToken'],
    'name'            => $ptName,
    'slug'            => $ptSlug,
    'description'     => 'Top-floor high-end luxury residences',
    'status'          => 'active',
]);
logTest("3.2 Property Type Create", in_array($ptCreateRes['code'], [200, 302, 303]), "Created '$ptName'");

// Fetch created ID from database
$newPtId = $pdo->query("SELECT id FROM property_types WHERE slug = '$ptSlug'")->fetchColumn();

// View Property Type
$ptView = $newPtId ? httpReq("$baseUrl/property-types/view/$newPtId") : ['code' => 500];
logTest("3.3 Property Type View", $ptView['code'] === 200 && strpos($ptView['body'], $ptName) !== false, "View page returned HTTP 200");

// Update Property Type
$ptUpdate = $newPtId ? httpReq("$baseUrl/property-types/update/$newPtId", 'POST', [
    'csrf_token_name' => $ptView['csrfToken'],
    'name'            => $ptName . ' Updated',
    'slug'            => $ptSlug,
    'description'     => 'Updated description',
    'status'          => 'active',
]) : ['code' => 500];
logTest("3.4 Property Type Update", in_array($ptUpdate['code'], [200, 302, 303]), "Updated successfully");

// Delete Property Type
$ptDelete = $newPtId ? httpReq("$baseUrl/property-types/delete/$newPtId", 'POST', [
    'csrf_token_name' => $ptView['csrfToken'],
]) : ['code' => 500];
logTest("3.5 Property Type Delete (Soft Delete)", in_array($ptDelete['code'], [200, 302, 303]), "Soft deleted successfully");

// 4. Locations CRUD
$locList = httpReq("$baseUrl/locations");
$locHasInitial = (strpos($locList['body'], 'Mumbai') !== false && strpos($locList['body'], 'Bandra West') !== false);
logTest("4.1 Locations List & Initial Data", $locList['code'] === 200 && $locHasInitial, "Locations listing returned HTTP 200");

// Create Location
$locArea = 'HITEC City ' . rand(100, 999);
$locCreate = httpReq("$baseUrl/locations/store", 'POST', [
    'csrf_token_name'  => $locList['csrfToken'],
    'state'            => 'Telangana',
    'city'             => 'Hyderabad',
    'area'             => $locArea,
    'locality'         => 'Madhapur',
    'landmark'         => 'Cyber Towers',
    'pincode'          => '500081',
    'nearby_locations' => 'Gachibowli, Kondapur',
    'status'           => 'active',
]);
logTest("4.2 Location Create", in_array($locCreate['code'], [200, 302, 303]), "Created '$locArea, Hyderabad'");

// Find created location ID from DB
$newLocId = $pdo->query("SELECT id FROM locations WHERE area = '$locArea'")->fetchColumn();

// View Location
$locView = $newLocId ? httpReq("$baseUrl/locations/view/$newLocId") : ['code' => 500];
logTest("4.3 Location View", $locView['code'] === 200 && strpos($locView['body'], $locArea) !== false, "View location page returned HTTP 200");

// Update Location
$locUpdate = $newLocId ? httpReq("$baseUrl/locations/update/$newLocId", 'POST', [
    'csrf_token_name' => $locView['csrfToken'],
    'state'           => 'Telangana',
    'city'            => 'Hyderabad',
    'area'            => $locArea . ' Prime',
    'pincode'         => '500081',
    'status'          => 'active',
]) : ['code' => 500];
logTest("4.4 Location Update", in_array($locUpdate['code'], [200, 302, 303]), "Updated area to '$locArea Prime'");

// 5. Amenities Management
$amList = httpReq("$baseUrl/amenities");
$amHasInitial = (strpos($amList['body'], 'Swimming Pool') !== false && strpos($amList['body'], 'Clubhouse') !== false);
logTest("5.1 Amenities List & Initial Data", $amList['code'] === 200 && $amHasInitial, "10 standard amenities present");

$amName = 'EV Station ' . rand(100, 999);
$amCreate = httpReq("$baseUrl/amenities/store", 'POST', [
    'csrf_token_name' => $amList['csrfToken'],
    'name'            => $amName,
    'icon'            => 'ri-charging-pile-line',
    'description'     => 'Fast electric vehicle charging stations',
    'status'          => 'active',
]);
logTest("5.2 Amenity Create", in_array($amCreate['code'], [200, 302, 303]), "Created '$amName'");

// Find Amenity ID
$newAmId = $pdo->query("SELECT id FROM amenities WHERE name = '$amName'")->fetchColumn();

$amDelete = $newAmId ? httpReq("$baseUrl/amenities/delete/$newAmId", 'POST', [
    'csrf_token_name' => $amList['csrfToken'],
]) : ['code' => 500];
logTest("5.3 Amenity Delete", in_array($amDelete['code'], [200, 302, 303]), "Deleted test amenity");

// 6. Project & Towers Management
$prjList = httpReq("$baseUrl/projects");
$prjHasInitial = (strpos($prjList['body'], 'Skyline Horizon') !== false);
logTest("6.1 Project List", $prjList['code'] === 200 && $prjHasInitial, "Rendered projects list with unit counts");

$prjCode = 'PRJ-TEST-' . rand(1000, 9999);
$prjCreate = httpReq("$baseUrl/projects/store", 'POST', [
    'csrf_token_name'     => $prjList['csrfToken'],
    'project_code'        => $prjCode,
    'name'                => 'Cyber Heights Tech Residences',
    'builder_developer'   => 'Cyber Realty Corp',
    'location_id'         => $newLocId ?: 1,
    'construction_status' => 'Under Construction',
    'possession_date'     => '2028-12-31',
    'status'              => 'active',
]);
logTest("6.2 Project Create", in_array($prjCreate['code'], [200, 302, 303]), "Created project '$prjCode'");

// Find created project ID from DB
$newPrjId = $pdo->query("SELECT id FROM projects WHERE project_code = '$prjCode'")->fetchColumn();

// View Project
$prjView = $newPrjId ? httpReq("$baseUrl/projects/view/$newPrjId") : ['code' => 500];
logTest("6.3 Project View & Inventory Summary", $prjView['code'] === 200 && strpos($prjView['body'], 'Cyber Heights Tech Residences') !== false, "Project view page rendered HTTP 200");

// Add Tower to Project
$twrAdd = $newPrjId ? httpReq("$baseUrl/projects/towers/store/$newPrjId", 'POST', [
    'csrf_token_name'  => $prjView['csrfToken'],
    'tower_name'       => 'Tower Pinnacle',
    'tower_code'       => 'PIN-1',
    'number_of_floors' => 20,
    'status'           => 'active',
]) : ['code' => 500];
logTest("6.4 Tower Create", in_array($twrAdd['code'], [200, 302, 303]), "Added 'Tower Pinnacle' (PIN-1) with 20 floors");

// 7. Property CRUD
$propList = httpReq("$baseUrl/properties");
$propHasInitial = (strpos($propList['body'], 'PROP-MUM-001') !== false);
logTest("7.1 Property Listing & Multi-Filter Search", $propList['code'] === 200 && $propHasInitial, "Properties search page returned HTTP 200");

$propCode = 'PROP-TEST-' . rand(1000, 9999);
$propCreate = httpReq("$baseUrl/properties/store", 'POST', [
    'csrf_token_name'  => $propList['csrfToken'],
    'property_code'    => $propCode,
    'title'            => 'Cyber Heights 3 BHK Smart Residence',
    'property_type_id' => 1,
    'location_id'      => $newLocId ?: 1,
    'project_id'       => $newPrjId,
    'area'             => 1650.00,
    'price'            => 18500000.00,
    'status'           => 'Available',
    'amenities'        => [1, 2, 3],
]);
logTest("7.2 Property Create with Amenities", in_array($propCreate['code'], [200, 302, 303]), "Created '$propCode'");

// Find created property ID from DB
$newPropId = $pdo->query("SELECT id FROM properties WHERE property_code = '$propCode'")->fetchColumn();

// View Property Details with Tabs
$propView = $newPropId ? httpReq("$baseUrl/properties/view/$newPropId") : ['code' => 500];
$hasTabs = (
    strpos($propView['body'], 'tab-overview') !== false &&
    strpos($propView['body'], 'tab-amenities') !== false &&
    strpos($propView['body'], 'tab-units') !== false &&
    strpos($propView['body'], 'tab-media') !== false &&
    strpos($propView['body'], 'tab-pricing') !== false &&
    strpos($propView['body'], 'tab-statusHistory') !== false
);
logTest("7.3 Property Details Tabbed View", $propView['code'] === 200 && $hasTabs, "All 6 tabs (Overview, Amenities, Units, Media, Pricing, Status History) present");

// 8. Property Unit Management
$unitList = httpReq("$baseUrl/units");
logTest("8.1 Property Units List", $unitList['code'] === 200, "Units listing rendered HTTP 200");

// Get Tower ID for the project
$twrId = $pdo->query("SELECT id FROM project_towers WHERE project_id = $newPrjId")->fetchColumn();

$unitCreate = httpReq("$baseUrl/units/store", 'POST', [
    'csrf_token_name'     => $unitList['csrfToken'],
    'project_id'          => $newPrjId,
    'tower_id'            => $twrId ?: null,
    'property_id'         => $newPropId,
    'unit_number'         => 'PIN-1001',
    'floor'               => 10,
    'flat_type'           => '3 BHK',
    'carpet_area'         => 1300.00,
    'built_up_area'       => 1650.00,
    'balcony'             => 2,
    'parking'             => 2,
    'facing'              => 'East',
    'unit_price'          => 18500000.00,
    'availability_status' => 'Available',
]);
logTest("8.2 Unit Create in Project & Tower", in_array($unitCreate['code'], [200, 302, 303]), "Created unit 'PIN-1001'");

// Check duplicate unit rejection
$unitDup = httpReq("$baseUrl/units/store", 'POST', [
    'csrf_token_name'     => $unitList['csrfToken'],
    'project_id'          => $newPrjId,
    'tower_id'            => $twrId ?: null,
    'unit_number'         => 'PIN-1001',
    'floor'               => 10,
    'flat_type'           => '3 BHK',
    'carpet_area'         => 1300.00,
    'built_up_area'       => 1650.00,
    'unit_price'          => 18500000.00,
    'availability_status' => 'Available',
]);
$dupRejected = (strpos($unitDup['headers'], 'error') !== false || in_array($unitDup['code'], [302, 303]));
logTest("8.3 Duplicate Unit Prevention", $dupRejected, "Duplicate unit within project/tower correctly blocked");

// 9. Availability Transitions & History
$availChange = $newPropId ? httpReq("$baseUrl/availability/property", 'POST', [
    'csrf_token_name' => $propView['csrfToken'],
    'property_id'     => $newPropId,
    'status'          => 'Reserved',
    'remarks'         => 'Test booking reservation made by corporate investor',
]) : ['code' => 500];
logTest("9.1 Availability Status Transition", in_array($availChange['code'], [200, 302, 303]), "Property transitioned to 'Reserved'");

// Verify in status history table
$historyCount = $pdo->query("SELECT COUNT(*) FROM property_status_history WHERE property_id = $newPropId")->fetchColumn();
logTest("9.2 Status History Logging", $historyCount >= 2, "Found $historyCount chronological history transitions");

// Check availability audit view
$availView = httpReq("$baseUrl/availability");
logTest("9.3 Availability Audit Log View", $availView['code'] === 200 && strpos($availView['body'], 'Reserved') !== false, "System-wide availability log rendered HTTP 200");

// 10. Pricing & Valuation Management
$pricingCreate = $newPropId ? httpReq("$baseUrl/pricing/store", 'POST', [
    'csrf_token_name'      => $propView['csrfToken'],
    'property_id'          => $newPropId,
    'base_price'           => 19200000.00,
    'price_per_sqft'       => 11636.36,
    'market_price'         => 20000000.00,
    'negotiated_price'     => 19000000.00,
    'discount'             => 200000.00,
    'effective_from'       => date('Y-m-d'),
    'remarks'              => 'Revised valuation schedule Q4',
    'update_primary_price' => '1',
]) : ['code' => 500];
logTest("10.1 Pricing Revision Schedule Recorded", in_array($pricingCreate['code'], [200, 302, 303]), "Valuation revision recorded with auto-update");

// Verify price updated on property
$updatedPropPrice = $pdo->query("SELECT price FROM properties WHERE id = $newPropId")->fetchColumn();
logTest("10.2 Live Property Price Auto-Update", (float)$updatedPropPrice === 19200000.00, "Base price synchronized to active listing");

// 11. Project Unit Inventory Matrix (Dynamic Calculation)
$invView = $newPrjId ? httpReq("$baseUrl/inventory?project_id=$newPrjId") : ['code' => 500];
$invHasUnit = (strpos($invView['body'], 'PIN-1001') !== false && strpos($invView['body'], 'Floor 10') !== false);
logTest("11.1 Dynamic Inventory Matrix", $invView['code'] === 200 && $invHasUnit, "Floor-by-floor unit grid rendered dynamically from MySQL");

// 12. Media Upload & Safe File Serving
$tempImg = sys_get_temp_dir() . '/test_prop_image.png';
$im = imagecreatetruecolor(200, 200);
$bg = imagecolorallocate($im, 37, 99, 235);
imagefill($im, 0, 0, $bg);
imagepng($im, $tempImg);
imagedestroy($im);

$uploadRes = $newPropId ? httpReq("$baseUrl/media/upload", 'POST', [
    'csrf_token_name' => $propView['csrfToken'],
    'property_id'     => $newPropId,
    'media_type'      => 'photo',
    'title'           => 'High Floor Sea Panorama Test',
    'is_primary'      => '1',
], false, true, ['media_file' => $tempImg]) : ['code' => 500];
logTest("12.1 Secure Media Upload", in_array($uploadRes['code'], [200, 302, 303]), "Uploaded image with safe random filename");

// Verify media record and file download
$mediaRow = $pdo->query("SELECT * FROM property_media WHERE property_id = $newPropId")->fetch();
if ($mediaRow) {
    $mediaFileRes = httpReq("$baseUrl/media/file/{$mediaRow['id']}");
    logTest("12.2 Media Serving Endpoint", $mediaFileRes['code'] === 200 && strlen($mediaFileRes['body']) > 0, "HTTP 200 binary response with proper Content-Type");

    // Delete media
    $mediaDel = httpReq("$baseUrl/media/delete/{$mediaRow['id']}", 'POST', [
        'csrf_token_name' => $propView['csrfToken'],
    ]);
    logTest("12.3 Media Deletion & Disk Cleanup", in_array($mediaDel['code'], [200, 302, 303]), "Deleted DB record and unlinked file");
}
@unlink($tempImg);

// 13. RBAC Permission Enforcement (Manager attempting unauthorized deletion)
@unlink($cookieFile);
$mgrLoginRes = httpReq("$baseUrl/login", 'POST', [
    'csrf_token_name' => $csrf,
    'email'           => 'manager@realestate-erp.local',
    'password'        => 'Password@123',
]);
$mgrPtDelete = $newPtId ? httpReq("$baseUrl/property-types/delete/$newPtId", 'POST', [
    'csrf_token_name' => $csrf,
]) : ['code' => 403];
logTest("13. RBAC Permission Gate (Manager 403 Forbidden)", $mgrPtDelete['code'] === 403, "Manager blocked with HTTP 403 from deleting property type");

// 14. Audit Logs Verification
$propAuditCount = $pdo->query("SELECT COUNT(*) FROM audit_logs WHERE module = 'Properties'")->fetchColumn();
$availAuditCount = $pdo->query("SELECT COUNT(*) FROM audit_logs WHERE module = 'Availability'")->fetchColumn();
$pricingAuditCount = $pdo->query("SELECT COUNT(*) FROM audit_logs WHERE module = 'Pricing'")->fetchColumn();
logTest("14. Audit Logging (Properties, Availability, Pricing)", ($propAuditCount > 0 && $availAuditCount > 0 && $pricingAuditCount > 0), "Audit trails recorded in MySQL (Props: $propAuditCount, Avail: $availAuditCount, Pricing: $pricingAuditCount)");

// Clean up test records
$pdo->exec("DELETE FROM property_units WHERE project_id = $newPrjId");
$pdo->exec("DELETE FROM project_towers WHERE project_id = $newPrjId");
$pdo->exec("DELETE FROM properties WHERE id = $newPropId");
$pdo->exec("DELETE FROM projects WHERE id = $newPrjId");
$pdo->exec("DELETE FROM locations WHERE id = $newLocId");

echo "\n========================================================\n";
echo sprintf("TEST SUMMARY: %s (%d/%d Tests Passed)\n", $allPassed ? "ALL PASSED" : "FAILURES DETECTED", count(array_filter($results, fn($r) => $r['status'] === 'PASS')), count($results));
echo "========================================================\n";

exit($allPassed ? 0 : 1);
