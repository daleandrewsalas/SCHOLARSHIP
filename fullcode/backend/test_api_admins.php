<?php
// test_api_admins.php
function do_post($url, $data){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) return ['error' => $err];
    return json_decode($res, true) ?: ['raw' => $res];
}

function do_get($url){
    $res = file_get_contents($url);
    return json_decode($res, true) ?: ['raw' => $res];
}

$base = dirname(__FILE__) . '/api_admins.php';
// If running from browser, use relative URL
if (php_sapi_name() === 'cli') {
    $api = 'http://localhost/SCHOLARSHIP/fullcode/backend/api_admins.php';
} else {
    // try to detect
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = dirname($_SERVER['SCRIPT_NAME']);
    $api = "$protocol://$host$script/api_admins.php";
}

// POST test
$postData = [
    'username' => 'testuser_' . rand(1000,9999),
    'fullname' => 'Test User',
    'role' => 'Administrator',
    'email' => 'test@example.com',
    'password' => 'secret123'
];

echo "Posting to: $api\n";
$res = do_post($api, $postData);
print_r($res);

// GET test
echo "\nGET list:\n";
$list = do_get($api);
print_r($list);

?>