<?php
header('Content-Type: application/json');
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if (!$action || !$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

if (!in_array($action, ['approve','decline'])) {
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    exit;
}

try {
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE renewal_requests SET status = 'approved' WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) echo json_encode(['success' => true]); else echo json_encode(['success' => false, 'message' => $conn->error]);
        $stmt->close();
        exit;
    }

    if ($action === 'decline') {
        $stmt = $conn->prepare("UPDATE renewal_requests SET status = 'declined' WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) echo json_encode(['success' => true]); else echo json_encode(['success' => false, 'message' => $conn->error]);
        $stmt->close();
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unhandled case']);
