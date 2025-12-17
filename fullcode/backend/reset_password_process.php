<?php
ob_start();
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST["email"]);
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    
    // Validate inputs
    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $_SESSION["error"] = "All fields are required!";
        header("Location: ../front_end/reset_password.php");
        exit();
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"] = "Invalid email format!";
        header("Location: ../front_end/reset_password.php");
        exit();
    }
    
    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION["error"] = "Passwords do not match!";
        header("Location: ../front_end/reset_password.php");
        exit();
    }
    
    // Check password length
    if (strlen($new_password) < 6) {
        $_SESSION["error"] = "Password must be at least 6 characters long!";
        header("Location: ../front_end/reset_password.php");
        exit();
    }
    
    try {
        // Check if user exists in users table
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $_SESSION["error"] = "This email is not registered in our system!";
            header("Location: ../front_end/reset_password.php");
            $stmt->close();
            exit();
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Hash the new password
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password in database
        $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        if (!$update_stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $update_stmt->bind_param("ss", $password_hash, $email);
        
        if ($update_stmt->execute()) {
            $_SESSION["success"] = "Your password has been reset successfully! You can now log in with your new password.";
            $update_stmt->close();
            header("Location: ../front_end/login.php");
            exit();
        } else {
            throw new Exception("Failed to update password: " . $update_stmt->error);
        }
        
    } catch (Exception $e) {
        $_SESSION["error"] = "An error occurred: " . $e->getMessage();
        header("Location: ../front_end/reset_password.php");
        exit();
    }
}

// If not POST request, redirect to reset password page
header("Location: ../front_end/reset_password.php");
exit();
?>
