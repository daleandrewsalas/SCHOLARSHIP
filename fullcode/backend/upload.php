<?php
// backend/upload.php
header('Content-Type: application/json');
$targetDir = __DIR__ . '/../uploads/';
if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['file'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No file provided']);
        exit;
    }
    $file = $_FILES['file'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg','jpeg','png','gif'];
    if (!in_array(strtolower($ext), $allowed)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type']);
        exit;
    }
    $fname = uniqid('img_') . '.' . $ext;
    $dest = $targetDir . $fname;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        // Return a web-accessible path (adjust if your uploads folder is public)
        $webPath = '/uploads/' . $fname;
        echo json_encode(['success' => true, 'path' => $webPath]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Upload failed']);
    }
}