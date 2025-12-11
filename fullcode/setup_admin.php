<?php
// Admin setup script - creates a default admin account
require_once __DIR__ . '/config/database.php';

try {
    // Check if admins table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins");
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        echo "✅ Admin account(s) already exist in the database.<br>";
        echo "Existing admins:<br>";
        $stmt = $pdo->query("SELECT id, username, fullname, role FROM admins");
        while ($row = $stmt->fetch()) {
            echo "- ID: {$row['id']}, Username: {$row['username']}, Fullname: {$row['fullname']}, Role: {$row['role']}<br>";
        }
    } else {
        // Create default admin account
        $username = "admin";
        $fullname = "Administrator";
        $password = "admin123"; // ⚠️ CHANGE THIS PASSWORD IN PRODUCTION!
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = "Administrator";
        
        $stmt = $pdo->prepare("INSERT INTO admins (username, fullname, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $fullname, $hashedPassword, $role]);
        
        echo "✅ Default admin account created successfully!<br>";
        echo "Username: <strong>admin</strong><br>";
        echo "Password: <strong>admin123</strong><br>";
        echo "⚠️ <strong>IMPORTANT:</strong> Please change this password in production!<br>";
        echo "<br><a href='../front_end/login_admin.php'>Go to Admin Login</a>";
    }
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
