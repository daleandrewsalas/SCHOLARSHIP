<?php
require 'db_connect.php';

// Fetch APPROVED accounts from system_accounts table
$sql = "SELECT 
          account_id as id,
          '' as picture,
          lastname,
          firstname,
          middlename,
          email,
          '' as barangay
        FROM system_accounts
        WHERE status = 'active'
        ORDER BY lastname ASC";

$result = $conn->query($sql);

$rows = [];

$uploadDir = __DIR__ . '/../uploads/';

while ($row = $result->fetch_assoc()) {
    // Normalize picture value
    $pic = isset($row['picture']) ? trim($row['picture']) : '';
    $fullPath = $pic !== '' ? $uploadDir . $pic : '';

    $row['picture_exists'] = false;
    $row['picture_encoded'] = '';

    if ($pic !== '' && file_exists($fullPath)) {
        $row['picture_exists'] = true;
        // Provide an encoded filename for safer URL building on the client
        $row['picture_encoded'] = rawurlencode($pic);
    } else {
        // If the file is missing, clear the picture value so frontend can fallback
        $row['picture'] = '';
    }

    $rows[] = $row;
}

echo json_encode([
    "accounts" => $rows
]);
?>
