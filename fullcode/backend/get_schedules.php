<?php
header('Content-Type: application/json');
@ini_set('display_errors', '0');
@error_reporting(0);
ob_start();

include 'db_connect.php';

try {
    // Query existing schedules table
    $result = $conn->query("SELECT schedule_date as date, total_slots as total, remaining_slots as remaining FROM schedules ORDER BY schedule_date DESC");
    
    if (!$result) {
        throw new Exception('Query error: ' . $conn->error);
    }

    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }

    ob_end_clean();
    echo json_encode(['schedules' => $schedules]);
    exit;

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['error' => $e->getMessage(), 'schedules' => []]);
    exit;
}
?>
