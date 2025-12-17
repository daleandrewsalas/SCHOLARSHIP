<?php
session_start();
require_once __DIR__ . '/../backend/db_connect.php';

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
        // Check if email already exists using MySQLi (check both users and registration tables)
        $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        if (!$checkStmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $checkStmt->bind_param("s", $email);
        if (!$checkStmt->execute()) {
            die("Execute failed: " . $conn->error);
        }
        
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $errors[] = "Email already registered!";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert into users table using MySQLi
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("ssss", $firstname, $lastname, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $_SESSION["registration_success"] = true;
                $_SESSION["registered_email"] = $email;
                header("Location: login.php");
                exit;
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduGrants - Register</title>
  <link rel="stylesheet" href="../css/register.css" />
  <link rel="stylesheet" href="../css/fonts.css" />
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
