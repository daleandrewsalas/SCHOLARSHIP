<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Check users table
$result = $conn->query("SELECT * FROM users WHERE status='approved' ORDER BY id DESC LIMIT 5");
$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode(['users_table' => $users]);
?>