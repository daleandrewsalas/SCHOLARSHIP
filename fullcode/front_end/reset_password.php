<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduGrants - Reset Password</title>
  <link rel="stylesheet" href="../css/forgotpass.css" />
  <link rel="stylesheet" href="../css/fonts.css" />
</head>

<body class="forgot-body">
  <div class="forgot-left">
    <!-- Background image will be here -->
  </div>

  <div class="forgot-right">
    <div class="forgot-box">
      <h2>Reset Password</h2>

      <?php 
      if(isset($_SESSION["error"])) {
          echo "<p style='color:red; font-size:14px;'>" . htmlspecialchars($_SESSION["error"]) . "</p>";
          unset($_SESSION["error"]);
      }
      if(isset($_SESSION["success"])) {
          echo "<p style='color:green; font-size:14px;'>" . htmlspecialchars($_SESSION["success"]) . "</p>";
          unset($_SESSION["success"]);
      }
      ?>

      <form method="POST" action="../backend/reset_password_process.php">
        <label>Email Address</label>
        <input name="email" type="email" placeholder="example@gmail.com" required />

        <label>New Password</label>
        <input name="new_password" type="password" placeholder="Enter new password" required />

        <label>Confirm Password</label>
        <input name="confirm_password" type="password" placeholder="Confirm new password" required />

        <a href="login.php" class="back-to-login">Back to Log In</a>

        <button class="forgot-send-btn" type="submit">Reset Password</button>
      </form>
    </div>
  </div>

</body>
</html>
