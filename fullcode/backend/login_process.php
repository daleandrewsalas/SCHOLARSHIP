<?php
ob_start();
session_start();
// Tiyakin na ang path ay tama papunta sa database connection file.
require 'db_connect.php'; 

// Validate inputs
if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: ../front_end/login_admin.php?error=Missing fields");
    exit();
}

$username = trim($_POST['username']);
$password = $_POST['password'];

try {
    // Tiyakin na ang $conn ay ang mysqli connection object, tulad ng ginamit sa orihinal.
    // Kung gumagamit ka ng PDO (tulad ng sa login_admin.php), palitan ang logic.

    // Prepare SQL: Hanapin ang user at kunin ang naka-hash na password
    $sql = "SELECT id, fullname, role, password FROM admins WHERE username = ?";
    
    // Assuming $conn is a mysqli connection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // 1. Verify password using password_verify (STANDARD SECURE PRACTICE)
        if (password_verify($password, $admin["password"])) {

            // 2. Login SUCCESS: Store session data
            $_SESSION['logged_in'] = true; // Ito ang ginagamit sa admin_dashboard.php check
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['fullname'] = $admin['fullname'];
            $_SESSION['role'] = $admin['role'];

            // 3. Redirect sa Admin Dashboard
            header("Location: ../front_end/admin_dashboard.html");
            exit();

        } else {
            // Failed login: Wrong password
            header("Location: ../front_end/login_admin.php?error=Invalid username or password");
            exit();
        }
    } else {
         // Failed login: User not found
        header("Location: ../front_end/login_admin.php?error=Invalid username or password");
        exit();
    }

} catch (Exception $e) {
    // Handle database/execution errors
    header("Location: ../front_end/login_admin.php?error=Database error. Please try again later.");
    // error_log("Login Error: " . $e->getMessage()); // Recommended for real debugging
    exit();
}


?>