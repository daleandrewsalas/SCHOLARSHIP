<?php
session_start();

// Destroy the session
session_unset();
session_destroy();

// Redirect to login
header("Location: login.php");
exit;
?>