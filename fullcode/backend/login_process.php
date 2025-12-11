<?php
ob_start();
session_start();
require 'db_connect.php';

// Validate inputs
if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: ../front_end/login.html?error=Missing fields");
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

// Prepare SQL
$sql = "SELECT id, fullname, role, password FROM admins WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $admin["password"])) {

        // Store session data
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['fullname'] = $admin['fullname'];
        $_SESSION['role'] = $admin['role'];

        header("Location: ../front_end/index.html");
        exit();

    }
}

// Failed login
header("Location: ../front_end/login.html?error=Invalid credentials");
exit();
