<?php
require 'db_connect.php';

$fullname = $_POST['fullname'];
$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];

// Check if username exists
$check = $conn->prepare("SELECT id FROM admins WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    die("Username already exists!");
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert admin
$sql = "INSERT INTO admins (fullname, username, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $fullname, $username, $hashedPassword, $role);

if ($stmt->execute()) {
    echo "Account created successfully!";
    header("Location: ../front_end/system_account.php");
    exit;
} else {
    echo "Error: " . $conn->error;
}
?>
