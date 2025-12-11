<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Check users table structure
$cols = $conn->query("SHOW COLUMNS FROM users");
$columns = [];
if ($cols) {
    while ($c = $cols->fetch_assoc()) {
        $columns[] = $c['Field'];
    }
}

// Count approved users
$approved = $conn->query("SELECT COUNT(*) as count FROM users WHERE status='approved'");
$approvedCount = 0;
if ($approved) {
    $row = $approved->fetch_assoc();
    $approvedCount = $row['count'] ?? 0;
}

// Get pending applicants for approval
$pending = $conn->query("
    SELECT 
        a.applicant_id,
        pi.firstname,
        pi.lastname,
        pi.email,
        a.status
    FROM applicants a
    LEFT JOIN personal_information pi ON a.applicant_id = pi.applicant_id
    WHERE a.status = 'pending'
    LIMIT 5
");
$pendingList = [];
if ($pending) {
    while ($row = $pending->fetch_assoc()) {
        $pendingList[] = $row;
    }
}

echo json_encode([
    'users_columns' => $columns,
    'approved_count' => $approvedCount,
    'pending_applicants' => $pendingList
]);
?>