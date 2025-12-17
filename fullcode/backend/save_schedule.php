<?php
header('Content-Type: application/json');
@ini_set('display_errors', '0');
@error_reporting(0);
ob_start();

include 'db_connect.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $schedule_date = isset($_POST['schedule_date']) ? trim($_POST['schedule_date']) : '';
    $total_slots = isset($_POST['total_slots']) ? (int)$_POST['total_slots'] : 0;

    if (empty($schedule_date) || $total_slots <= 0) {
        throw new Exception('Invalid schedule date or total slots');
    }

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

    // Use `schedules` table created by setup_database.php
    $remaining_slots = $total_slots;

    // Check if a schedule for this date already exists
    $check = $conn->prepare("SELECT schedule_id FROM schedules WHERE schedule_date = ? LIMIT 1");
    if (!$check) throw new Exception('Prepare error (check): ' . $conn->error);
    $check->bind_param('s', $schedule_date);
    if (!$check->execute()) throw new Exception('Execute error (check): ' . $check->error);
    $res = $check->get_result();

    if ($res && $res->num_rows > 0) {
        // Update existing schedule
        $update = $conn->prepare("UPDATE schedules SET total_slots = ?, remaining_slots = ? WHERE schedule_date = ?");
        if (!$update) throw new Exception('Prepare error (update): ' . $conn->error);
        $update->bind_param('iis', $total_slots, $remaining_slots, $schedule_date);
        if (!$update->execute()) throw new Exception('Execute error (update): ' . $update->error);
        $update->close();
    } else {
        // Insert new schedule
        $insert = $conn->prepare("INSERT INTO schedules (schedule_date, total_slots, remaining_slots) VALUES (?, ?, ?)");
        if (!$insert) throw new Exception('Prepare error (insert): ' . $conn->error);
        $insert->bind_param('sii', $schedule_date, $total_slots, $remaining_slots);
        if (!$insert->execute()) throw new Exception('Execute error (insert): ' . $insert->error);
        $insert->close();
    }
    $check->close();
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Schedule saved successfully']);
    exit;

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
?>
