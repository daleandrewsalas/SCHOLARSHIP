_<?php
// Path: front_end/login_admin.php
session_start();

// Ensure the user is not already logged in
// Assume admin_dashboard.html is the target page.
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    header("Location: admin_dashboard.html");
    exit;
}

// Check for error from login_process.php
$error = $_GET['error'] ?? '';

// Sanitize username input for display persistence
$username = htmlspecialchars($_POST['username'] ?? '');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>EduGrants - Admin Login</title>
<link rel="stylesheet" href="../css/logout.css" /> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="logout-body">
<div class="logout-left">
 <h1>WELCOME!</h1>
</div>

<div class="logout-right">
 <div class="logout-box">
 <h2>Admin Login</h2>

     <?php if(!empty($error)) echo "<p style='color:red; font-weight:bold; margin-bottom: 15px;'>$error</p>"; ?>

     <form method="POST" action="../backend/login_process.php">
    <input name="username" type="text" placeholder="Username" required value="<?php echo $username; ?>" />
  
  <div style="position:relative;">
  <input name="password" type="password" placeholder="Password" id="passwordField" required style="width:100%; padding:10px 40px 10px 10px; margin-bottom:12px; border:1px solid #ccc; border-radius:4px; font-size:14px;" />
  <i onclick="togglePassword()" id="toggleIcon" class="fa-regular fa-eye" style="position:absolute; right:12px; top:12px; cursor:pointer; font-size:18px; color:#7B7B7B;"></i>
  </div>

    <button type="submit" class="logout-btn">Log In</button>
 </form>
 </div>
 </div>

<script>
 function togglePassword() {
  const passwordField = document.getElementById('passwordField');
  const toggleIcon = document.getElementById('toggleIcon');
 
  if (passwordField.type === 'password') {
  passwordField.type = 'text';
  toggleIcon.classList.remove('fa-eye');
  toggleIcon.classList.add('fa-eye-slash');
 } else {
  passwordField.type = 'password';
  toggleIcon.classList.remove('fa-eye-slash');
  toggleIcon.classList.add('fa-eye');
 }
 }
</script>

</body>
</html>