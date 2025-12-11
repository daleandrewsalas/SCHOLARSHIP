<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } else {
        if ($email === "example1@gmail.com") {
            
            $_SESSION["reset_email_sent"] = true;
            $success = "Password reset link has been sent to your email!";
        } else {
            $error = "This email is not registered in our system!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduGrants - Forgot Password</title>
  <link rel="stylesheet" href="forgotpass.css" />
</head>

<body class="forgot-body">
  <div class="forgot-left">
    <!-- Background image will be here -->
  </div>

  <div class="forgot-right">
    <div class="forgot-box">
      <h2>Forgot Password</h2>

      <?php if(isset($error)) echo "<p style='color:red; font-size:14px;'>$error</p>"; ?>
      <?php if(isset($success)) echo "<p style='color:green; font-size:14px;'>$success</p>"; ?>

      <form method="POST">
        <label>Enter Email Address</label>
        <input name="email" type="email" placeholder="example@gmail.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />

        <a href="logout.php" class="back-to-login">Back to Log In</a>

        <button class="forgot-send-btn" type="submit">Send</button>
      </form>
    </div>
  </div>

</body>
</html>