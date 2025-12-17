<?php
header('Content-Type: application/json');
include 'db_connect.php';

try {
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'schedules'");
    $tableExists = $result && $result->num_rows > 0;
    
    // Get all schedules
    $schedules = [];
    if ($tableExists) {
        $res = $conn->query("SELECT * FROM schedules ORDER BY schedule_date DESC");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $schedules[] = $row;
            }
        }
    }
    
    // Get table info
    $tableInfo = [];
    if ($tableExists) {
        $res = $conn->query("SHOW COLUMNS FROM schedules");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $tableInfo[] = $row;
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'table_exists' => $tableExists,
        'total_schedules' => count($schedules),
        'schedules' => $schedules,
        'table_info' => $tableInfo,
        'db_name' => $dbname,
        'connection_ok' => true
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
