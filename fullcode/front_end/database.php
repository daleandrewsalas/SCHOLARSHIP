<?php
// Database Configuration
$host = '127.0.0.1';
$dbname = 'db_edugrants';
$username = 'root';
$password = '123456';

// Create connection using PDO
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    // echo "Connected successfully"; // Uncomment to test connection
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>