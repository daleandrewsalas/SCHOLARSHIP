<?php
// Return admin name as JSON. Suppress accidental HTML/errors so frontend can parse.
header('Content-Type: application/json');
@ini_set('display_errors', '0');
@error_reporting(0);
ob_start();
session_start();
include 'db_connect.php';

$name = 'User';
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

// Determine which table to query based on role
if ($user_id !== null) {
    try {
        if ($role === 'admin') {
            $stmt = $conn->prepare("SELECT fullname FROM admins WHERE id = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_assoc();
                if (!empty($data['fullname'])) $name = $data['fullname'];
            }
        } else {
            // Assume regular user/applicant
            $stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE user_id = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_assoc();
                if ($data) {
                    $fname = trim($data['first_name'] ?? '');
                    $lname = trim($data['last_name'] ?? '');
                    $full = trim($fname . ' ' . $lname);
                    if ($full !== '') $name = $full;
                }
            }
        }
    } catch (Exception $e) {
        // ignore errors and fall back to default
    }
}

@ob_end_clean();
echo json_encode(['name' => $name]);
?>