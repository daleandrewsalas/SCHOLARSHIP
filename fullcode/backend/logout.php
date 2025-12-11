<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect back to the login page
// Assuming login_admin.php is in the front_end folder
header("Location: ../front_end/login_admin.php");
exit;
?>