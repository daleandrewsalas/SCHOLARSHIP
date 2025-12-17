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
$addressData = [];
$courseData = [];
$pendingCount = 0;

try {
    // Count approved scholars from users table
    $approved = $conn->query("SELECT COUNT(*) as total FROM users");
    if ($approved) {
        $row = $approved->fetch_assoc();
        $approvedCount = isset($row['total']) ? (int)$row['total'] : 0;
    }

    // Count pending applications (if applicants table exists)
    $pending = $conn->query("SELECT COUNT(*) as total FROM applicants WHERE status = 'pending'");
    if ($pending) {
        $row = $pending->fetch_assoc();
        $pendingCount = isset($row['total']) ? (int)$row['total'] : 0;
    }

    // Fetch permanent_address data from residency_information (for bar chart)
    $result = $conn->query("
        SELECT 
            COALESCE(permanent_address, 'Unknown') as address, 
            COUNT(*) as count 
        FROM residency_information
        GROUP BY address
        ORDER BY count DESC
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $addressData[] = [
                'address' => $row['address'],
                'count' => (int)$row['count']
            ];
        }
    }

    // Fetch course data from personal_information (for doughnut chart)
    $courseResult = $conn->query("
        SELECT 
            COALESCE(course, 'Unknown') as course, 
            COUNT(*) as count 
        FROM personal_information
        GROUP BY course
        ORDER BY count DESC
    ");
    if ($courseResult) {
        while ($row = $courseResult->fetch_assoc()) {
            $courseData[] = [
                'course' => $row['course'],
                'count' => (int)$row['count']
            ];
        }
    }
} catch (Exception $e) {
    // swallow and return what we have
}

// discard any buffered output (warnings, accidental HTML)
@ob_end_clean();

echo json_encode([
    'approved' => $approvedCount,
    'pending' => $pendingCount,
    'address_data' => $addressData,
    'course_data' => $courseData
]);
?>
