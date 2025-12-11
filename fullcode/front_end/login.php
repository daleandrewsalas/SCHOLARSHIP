<?php
// Tiyaking ito ang unang linya, walang space bago ito.
session_start();

// Ang iyong path para sa database connection file. Tiyakin na tama ito.
require_once __DIR__ . '/../config/database.php';

// I-check kung may nag-submit ng form (POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kumuha ng data at iwasan ang simple XSS (Cross-Site Scripting)
    $email = htmlspecialchars($_POST["email"]);
    $password = $_POST["password"];

    // --- 1. Query Database para sa User ---
    // Gumamit ng prepared statement para sa seguridad
    $stmt = $pdo->prepare("SELECT user_id, firstname, password_hash FROM registration WHERE email = ?");
    
    // Ang execute() ay ligtas na ipinapasa ang user input
    if ($stmt->execute([$email])) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // --- 2. Verify Password ---
        // I-check kung nakita ang user at kung tugma ang password sa naka-hash na password
        if ($user && password_verify($password, $user['password_hash'])) {
            
            // --- 3. Login SUCCESS ---
            // I-set ang session variables
            $_SESSION["logged_in"] = true;
            $_SESSION["user_id"] = $user['user_id'];
            $_SESSION["firstname"] = $user['firstname'];
            
            // I-redirect sa dashboard. Tiyakin na WALANG output bago ito.
            header("Location: dashboard.php");
            exit; // Mahalaga na huminto ang script pagkatapos ng redirection
        } else {
            // Login FAILED (Invalid credentials)
            $error = "Invalid email or password!";
        }
    } else {
        // Error sa pag-execute ng query
        $error = "An error occurred while fetching user data.";
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

            <?php if(isset($error)) echo "<p style='color:red; margin-bottom: 15px;'>$error</p>"; ?>

            <form method="POST">
                <input name="email" type="email" placeholder="Email Address" required />
                
                <div style="position:relative;">
                    <input name="password" type="password" placeholder="Password" id="passwordField" required 
                           style="width:100%; padding:10px 40px 10px 10px; margin-bottom:12px; border:1px solid #ccc; border-radius:4px; font-size:14px;" />
                           
                    <i onclick="togglePassword()" id="toggleIcon" class="fa-regular fa-eye" 
                       style="position:absolute; right:12px; top:12px; cursor:pointer; font-size:18px; color:#7B7B7B;"></i>
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
                toggleIcon.classList.add('fa-eye-slash'); // Icon para sa 'hide'
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye'); // Icon para sa 'show'
            }
        }
    </script>
</body>
</html>