<?php
// Ensure we always return valid JSON even if PHP emits warnings/notices
header('Content-Type: application/json');
// Turn off display errors to avoid HTML error output breaking JSON
@ini_set('display_errors', '0');
@error_reporting(0);

// Capture any accidental output and discard it to keep JSON clean
ob_start();
include 'db_connect.php';

$approvedCount = 0;
$barangayData = [];
$districtData = [];

try {
    // Count approved scholars from system_accounts (only active accounts)
    $approved = $conn->query("SELECT COUNT(*) as total FROM system_accounts WHERE status = 'active'");
    if ($approved) {
        $row = $approved->fetch_assoc();
        $approvedCount = isset($row['total']) ? (int)$row['total'] : 0;
    }

    // Barangay chart - count approved scholars by barangay by joining system_accounts -> users -> applicants -> residency_information
    $result = $conn->query("\
        SELECT 
            COALESCE(ri.permanent_address, 'Unknown') as barangay, 
            COUNT(*) as count 
        FROM system_accounts sa
        LEFT JOIN users u ON u.email = sa.email
        LEFT JOIN applicants a ON a.applicant_id = u.applicant_id
        LEFT JOIN residency_information ri ON a.applicant_id = ri.applicant_id
        WHERE sa.status = 'active'
        GROUP BY barangay
        ORDER BY count DESC
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $barangayData[] = $row;
        }
    }

    // If no barangay data from residency, try to use applicant count by status
    if (empty($barangayData)) {
        $result = $conn->query("
            SELECT 
                a.status as barangay, 
                COUNT(*) as count 
            FROM applicants a
            GROUP BY a.status
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $barangayData[] = $row;
            }
        }
    }
} catch (Exception $e) {
    // swallow and return what we have
}

// discard any buffered output (warnings, accidental HTML)
@ob_end_clean();

echo json_encode([
    'approved' => $approvedCount,
    'barangay_data' => $barangayData,
    'district_data' => $barangayData  // use same data for second chart
]);
?>
