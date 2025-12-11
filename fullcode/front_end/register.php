<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validation
    $errors = [];

    if (empty($firstname)) {
        $errors[] = "First name is required!";
    }

    if (empty($lastname)) {
        $errors[] = "Last name is required!";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required!";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters!";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }

    // If no errors, register the user
    if (empty($errors)) {
        // In a real application, you would:
        // 1. Hash the password: $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // 2. Save to database with firstname, lastname, email, and hashed password
        
        // For now, we'll simulate successful registration
        $_SESSION["registration_success"] = true;
        $_SESSION["registered_email"] = $email;
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduGrants - Register</title>
  <link rel="stylesheet" href="register.css" />
</head>

<body class="register-body">
  <div class="register-left">
    <h1>JOIN US!</h1>
  </div>

  <div class="register-right">
    <div class="register-box">
      <h2>Register</h2>

      <?php if(!empty($errors)): ?>
        <div class="error-messages">
          <?php foreach($errors as $error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <label>First Name</label>
        <input name="firstname" type="text" value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>" required />

        <label>Last Name</label>
        <input name="lastname" type="text" value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>" required />

        <label>Email Address</label>
        <input name="email" type="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />

        <label>Password</label>
        <input name="password" type="password" required />

        <label>Confirm Password</label>
        <input name="confirm_password" type="password" required />

        <button class="register-btn" type="submit">Sign Up</button>
      </form>
    </div>

    <div class="register-bottom">
      <p>Have an account Already?</p>
      <button class="register-login-btn" onclick="window.location.href='logout.php'">
        Login
      </button>
    </div>
  </div>

</body>
</html>