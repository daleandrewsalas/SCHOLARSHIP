<?php
require 'db_connect.php';
header('Content-Type: application/json');
try {
    $res = $conn->query("SHOW TABLES LIKE 'schedules'");
    if (!$res) {
        echo json_encode(['ok' => false, 'error' => $conn->error]);
        exit;
    }
    $exists = ($res->num_rows > 0);
    $columns = [];
    if ($exists) {
        $cols = $conn->query("SHOW COLUMNS FROM schedules");
        while ($c = $cols->fetch_assoc()) $columns[] = $c;
    }
    echo json_encode(['ok' => true, 'exists' => $exists, 'columns' => $columns]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
?>