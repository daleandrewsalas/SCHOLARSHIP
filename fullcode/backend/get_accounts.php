<?php
require 'db_connect.php';

// Fetch approved users from personal_information table
$sql = "SELECT 
          id,
          firstname,
          lastname,
          middlename,
          gender,
          course,
          '' as picture
        FROM personal_information
        ORDER BY lastname ASC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "error" => "Query failed: " . $conn->error,
        "accounts" => []
    ]);
    exit;
}

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
    "accounts" => $rows,
    "total" => count($rows)
]);
?>
