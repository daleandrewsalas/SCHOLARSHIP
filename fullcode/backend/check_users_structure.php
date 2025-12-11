<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Get users table structure
$cols = $conn->query("SHOW COLUMNS FROM users");
$columns = [];
if ($cols) {
    while ($c = $cols->fetch_assoc()) {
        $columns[] = $c;
    }
}

// Get sample users
$result = $conn->query("SELECT * FROM users LIMIT 3");
$sample = [];
if ($result) {
    while ($r = $result->fetch_assoc()) {
        $sample[] = $r;
    }
}

echo json_encode([
    'columns' => $columns,
    'sample_data' => $sample
]);
?>