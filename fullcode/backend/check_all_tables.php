<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Check all users with status approved
$result = $conn->query("SELECT id, firstname, lastname, email, status FROM users WHERE status='approved' LIMIT 10");
$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Check system_accounts
$sysResult = $conn->query("SELECT account_id, username, firstname, lastname, email FROM system_accounts LIMIT 10");
$sysAccounts = [];
if ($sysResult) {
    while ($row = $sysResult->fetch_assoc()) {
        $sysAccounts[] = $row;
    }
}

// Check pending applicants
$pending = $conn->query("SELECT a.applicant_id, pi.firstname, pi.lastname, a.status FROM applicants a LEFT JOIN personal_information pi ON a.applicant_id = pi.applicant_id WHERE a.status='pending' LIMIT 5");
$pendingList = [];
if ($pending) {
    while ($row = $pending->fetch_assoc()) {
        $pendingList[] = $row;
    }
}

echo json_encode([
    'approved_users' => $users,
    'system_accounts' => $sysAccounts,
    'pending_applicants' => $pendingList
]);
?>