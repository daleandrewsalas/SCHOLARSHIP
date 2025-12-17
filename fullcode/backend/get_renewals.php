<?php
header('Content-Type: application/json');
@ini_set('display_errors', '0');
@error_reporting(0);
ob_start();

require 'db_connect.php';

$renewals = [];
$total = 0;
$approved = 0;
$pending = 0;

try {
    // Check if renewal_requests table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'renewal_requests'");
    if ($tableCheck->num_rows > 0) {
        // Get all renewal requests
        $result = $conn->query("
            SELECT id, user_id, email, firstname, lastname, school_year, school, gpa, status, created_at
            FROM renewal_requests
            ORDER BY created_at DESC
        ");

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $renewals[] = $row;
            }
        }

        // Get counts
        $totalResult = $conn->query("SELECT COUNT(*) as count FROM renewal_requests");
        if ($totalResult) {
            $row = $totalResult->fetch_assoc();
            $total = $row['count'];
        }

        $approvedResult = $conn->query("SELECT COUNT(*) as count FROM renewal_requests WHERE status = 'approved'");
        if ($approvedResult) {
            $row = $approvedResult->fetch_assoc();
            $approved = $row['count'];
        }

        $pendingResult = $conn->query("SELECT COUNT(*) as count FROM renewal_requests WHERE status = 'pending'");
        if ($pendingResult) {
            $row = $pendingResult->fetch_assoc();
            $pending = $row['count'];
        }
    }
} catch (Exception $e) {
    // return empty data
}

@ob_end_clean();
echo json_encode([
    'renewals' => $renewals,
    'total' => $total,
    'approved' => $approved,
    'pending' => $pending
]);
?>
