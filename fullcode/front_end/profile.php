<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduGrants - Settings</title>
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/fonts.css">
</head>
<body>
    <?php
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header("Location: login.php");
        exit;
    }
    
    $firstname = $_SESSION['firstname'] ?? 'User';
    $lastname = $_SESSION['lastname'] ?? '';
    $email = $_SESSION['email'] ?? '';
    $fullname = trim($firstname . ' ' . $lastname);
    ?>
    
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../icon/graducation.png" alt="EduGrants Logo" width="30" height="30">
                <h1>EduGrants</h1>
            </div>
            
            <div class="user-profile">
                <div class="avatar">
                    <img src="../icon/user.png" alt="User Avatar">
                </div>
                <h3><?php echo htmlspecialchars($fullname); ?></h3>
            </div>
            
            <nav class="navigation">
                <a href="dashboard.php" class="nav-item">
                    <img src="../icon/dashboard.png" alt="Dashboard" width="20" height="20">
                    Dashboard
                </a>
                <a href="apply.php" class="nav-item">
                    <img src="../icon/apply.png" alt="Apply Scholarship" width="20" height="20">
                    Apply Scholarship
                </a>
                <a href="logout.php" class="nav-item">
                    <img src="../icon/logout.png" alt="Log out" width="20" height="20">
                    Log out
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <div class="user-info">
                    <img src="../icon/notification.png" alt="Notification" width="20" height="20">
                    <span><?php echo htmlspecialchars($firstname); ?></span>
                </div>
            </header>
            
            <div class="content-area">
                <h2>Settings</h2>
                
                <div class="settings-container">
                    <!-- Settings Sidebar -->
                    <div class="settings-sidebar">
                        <button class="settings-tab active" data-tab="profile">Profile</button>
                        <button class="settings-tab" data-tab="security">Security</button>
                        <button class="settings-tab" data-tab="notification">Notification</button>
                        <button class="settings-tab" data-tab="account">Account Setting</button>
                    </div>
                    
                    <!-- Settings Content -->
                    <div class="settings-content">
                        <div class="tab-content active" id="profile">
                            <h3>Profile Settings</h3>
                            <hr>
                            
                            <!-- Profile Picture Upload -->
                            <div class="profile-upload-section">
                                <h4>Profile picture Upload</h4>
                                <div class="profile-upload">
                                    <div class="profile-avatar">
                                        <img src="../icon/user.png" alt="Profile">
                                    </div>
                                    <div class="profile-info">
                                        <h5><?php echo htmlspecialchars($fullname); ?></h5>
                                        <p>Student</p>
                                    </div>
                                    <div class="profile-actions">
                                        <button class="btn-upload">Upload New Photo</button>
                                        <button class="btn-delete">Delete</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Personal Information -->
                            <div class="form-section">
                                <h4>Personal Information</h4>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" placeholder="First Name" value="<?php echo htmlspecialchars($firstname); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" placeholder="Last Name" value="<?php echo htmlspecialchars($lastname); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>