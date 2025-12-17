<?php
session_start();
// Ensure the path to the database connection file is correct
require_once __DIR__ . '/../backend/db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];
    $user_data = null;
    $role = null;

    // ===============================================
    // 1. ATTEMPT LOGIN AS ADMIN
    // ===============================================
    // FIX 1: Pinalitan ang 'full_name' ng 'fullname'
    $stmt_admin = $conn->prepare("SELECT id, username, email, password, fullname, role FROM admins WHERE email = ? OR username = ?");
    
    if ($stmt_admin) {
      $stmt_admin->bind_param("ss", $email, $email);
      $stmt_admin->execute();
      $result_admin = $stmt_admin->get_result();
      $admin = $result_admin->fetch_assoc();
      $stmt_admin->close();

      // Check if admin credentials match. Support both hashed and legacy plaintext passwords.
      if ($admin) {
        $stored = $admin['password'];
        $match = false;

        // If stored password appears to be a hash, use password_verify
        if (strpos($stored, '$2y$') === 0 || strpos($stored, '$2b$') === 0 || stripos($stored, 'argon2') !== false) {
          if (password_verify($password, $stored)) {
            $match = true;
          }
        } else {
          // Legacy fallback: plaintext comparison
          if ($stored === $password) {
            $match = true;
            // Re-hash the plaintext password and update DB for improved security
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $safeHash = $conn->real_escape_string($newHash);
            $safeId = $conn->real_escape_string($admin['id']);
            $conn->query("UPDATE admins SET password = '$safeHash' WHERE id = '$safeId'");
            // Update local value so session stores consistent data if needed
            $admin['password'] = $newHash;
          }
        }

        if ($match) {
          $user_data = $admin;
          $role = 'admin';
        }
      }
    }

    // ===============================================
    // 2. ATTEMPT LOGIN AS REGULAR USER (APPLICANT)
    // ===============================================
    if (!$user_data) {
        // Check credentials against users table
        $stmt_user = $conn->prepare("SELECT user_id, first_name, last_name, email, password_hash FROM users WHERE email = ?");
        if ($stmt_user) {
            $stmt_user->bind_param("s", $email);
            $stmt_user->execute();
            $result_user = $stmt_user->get_result();
            $user = $result_user->fetch_assoc();
            $stmt_user->close();

            if ($user) {
              $storedUserPw = $user['password_hash'];
              $userMatch = false;

              // If stored looks like a hash, verify it
              if (strpos($storedUserPw, '$2y$') === 0 || strpos($storedUserPw, '$2b$') === 0 || stripos($storedUserPw, 'argon2') !== false) {
                if (password_verify($password, $storedUserPw)) $userMatch = true;
              } else {
                // Legacy plaintext fallback
                if ($storedUserPw === $password) {
                  $userMatch = true;
                  // Re-hash and update database for security
                  $newHash = password_hash($password, PASSWORD_DEFAULT);
                  $safeHash = $conn->real_escape_string($newHash);
                  $safeId = $conn->real_escape_string($user['user_id']);
                  $conn->query("UPDATE users SET password_hash = '$safeHash' WHERE user_id = '$safeId'");
                  $user['password_hash'] = $newHash;
                }
              }

              if ($userMatch) {
                $user_data = $user;
                $role = 'applicant';
              }
            }
        }
    }


    // ===============================================
    // 3. HANDLE LOGIN RESULT AND REDIRECT
    // ===============================================
    if ($user_data) {
        // Set session variables
        $_SESSION["logged_in"] = true;
        $_SESSION["user_id"] = $user_data[$role === 'admin' ? 'id' : 'user_id'];
        $_SESSION["email"] = $user_data['email'];
        $_SESSION["role"] = $role; // Critical: store the role

        if ($role === 'admin') {
            // Admin Redirection
            // FIX 2: Pinalitan ang 'full_name' ng 'fullname' dito rin
            $_SESSION["fullname"] = $user_data['fullname']; 
            header("Location: admin_dashboard.html"); // I-assume na ito ang path ng admin dashboard mo
            exit;
        } else {
            // Regular User (Applicant) Redirection
            $_SESSION["firstname"] = $user_data['first_name'];
            $_SESSION["lastname"] = $user_data['last_name'];
            header("Location: dashboard.php"); // I-assume na ito ang path ng applicant dashboard mo
            exit;
        }

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
  <link rel="stylesheet" href="../css/logout.css" />
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