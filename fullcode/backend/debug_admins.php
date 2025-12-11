<?php
require 'db_connect.php';
header('Content-Type: application/json');
try {
    $res = $conn->query("SELECT id, username, fullname, email, role, created_at FROM admins ORDER BY id DESC");
    $rows = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) $rows[] = $r;
    }
    echo json_encode(['count' => count($rows), 'admins' => $rows]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>