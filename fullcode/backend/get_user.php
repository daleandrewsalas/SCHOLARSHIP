<?php
// Return admin name as JSON. Suppress accidental HTML/errors so frontend can parse.
header('Content-Type: application/json');
@ini_set('display_errors', '0');
@error_reporting(0);
ob_start();
session_start();
include 'db_connect.php';

$admin_id = $_SESSION['user_id'] ?? null; // Use the correct session key
$name = 'Admin';

if ($admin_id !== null) {
    try {
        // Fetch user data based on the stored session ID
        $stmt = $conn->prepare("SELECT fullname FROM admins WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            
            // Use fullname if available
            if (!empty($data['fullname'])) {
                $name = $data['fullname'];
            }
        }
    } catch (Exception $e) {
        // ignore and return default 'Admin'
    }
}

@ob_end_clean();
// Return the name found or the default 'Admin'
echo json_encode(['name' => $name]);
?>