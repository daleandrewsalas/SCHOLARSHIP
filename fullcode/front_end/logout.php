<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Hardcoded sample credentials (pwede palitan ng database)
    if ($email === "example1@gmail.com" && $password === "123student") {
        $_SESSION["logged_in"] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduGrants - Login</title>
  <link rel="stylesheet" href="logout.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="logout-body">
  <div class="logout-left">
    <h1>WELCOME!</h1>
  </div>

  <div class="logout-right">
    <div class="logout-box">
      <h2>Login</h2>

      <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

      <form method="POST">
        <input name="email" type="text" placeholder="Username or Email Address" required />
        
        <div style="position:relative;">
          <input name="password" type="password" placeholder="Password" id="passwordField" required style="width:100%; padding:10px 40px 10px 10px; margin-bottom:12px; border:1px solid #ccc; border-radius:4px; font-size:14px;" />
          <i onclick="togglePassword()" id="toggleIcon" class="fa-regular fa-eye" style="position:absolute; right:12px; top:12px; cursor:pointer; font-size:18px; color:#7B7B7B;"></i>
        </div>

        <a href="forgotpass.php" style="display:block; text-align:center; margin:20px 0 30px 0; font-size:13px; color:#000; text-decoration:none;">Forgot Password?</a>

        <button class="logout-btn">Log In</button>
      </form>
    </div>

    <div class="logout-bottom">
      <p>Don't have an account yet?</p>
      <button class="logout-register-btn" onclick="window.location.href='register.php'">
        Register
      </button>
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