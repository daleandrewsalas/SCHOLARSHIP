<?php
header('Content-Type: application/json');
@ini_set('display_errors', '0');
@error_reporting(0);
ob_start();

include 'db_connect.php';

try {
    // Auto-create `schedules` table if missing
    $tableCheck = $conn->query("SHOW TABLES LIKE 'schedules'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        $createTable = "CREATE TABLE schedules (
            schedule_id INT AUTO_INCREMENT PRIMARY KEY,
            schedule_date DATE NOT NULL UNIQUE,
            total_slots INT NOT NULL,
            remaining_slots INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        if (!$conn->query($createTable)) {
            throw new Exception('Failed to create schedules table: ' . $conn->error);
        }
    }

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
