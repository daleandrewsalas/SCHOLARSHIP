<?php
session_start();
require_once __DIR__ . '/../backend/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } else {
        try {
            // Check if email exists in users table
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                
                if ($result->num_rows > 0) {
                    $_SESSION["reset_email_sent"] = true;
                    $_SESSION["reset_email"] = $email;
                    $success = "Email verified! You will be redirected to reset your password.";
                    // Redirect after 2 seconds
                    header("refresh:2;url=reset_password.php");
                } else {
                    $error = "This email is not registered in our system!";
                }
            } else {
                $error = "Database error. Please try again later.";
            }
        } catch (Exception $e) {
            $error = "An error occurred. Please try again.";
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
  <link rel="stylesheet" href="../css/forgotpass.css" />
  <link rel="stylesheet" href="../css/fonts.css" />
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
