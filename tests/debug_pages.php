<?php
$cookieFile = sys_get_temp_dir() . '/ci4_phase2_cookie.txt';
function getPage($url) {
    global $cookieFile;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $res];
}

$loginRes = getPage('http://localhost:8080/login');
preg_match('/name="csrf_token_name" value="([^"]+)"/', $loginRes['body'], $m);
$csrf = $m[1] ?? '';

$ch = curl_init('http://localhost:8080/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'csrf_token_name' => $csrf,
    'email' => 'admin@realestate-erp.local',
    'password' => 'Password@123'
]));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_exec($ch);
curl_close($ch);

echo "Testing /properties/view/1:\n";
$pv = getPage('http://localhost:8080/properties/view/1');
echo "Code: " . $pv['code'] . "\n";
if ($pv['code'] !== 200) {
    echo substr($pv['body'], 0, 500) . "\n";
} else {
    echo "Success! Length: " . strlen($pv['body']) . "\n";
}

echo "\nTesting /availability:\n";
$av = getPage('http://localhost:8080/availability');
echo "Code: " . $av['code'] . "\n";
if ($av['code'] !== 200) {
    echo substr($av['body'], 0, 800) . "\n";
} else {
    echo "Success! Length: " . strlen($av['body']) . "\n";
}
