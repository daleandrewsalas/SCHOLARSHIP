<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduGrants - Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="images/graducation.png" alt="EduGrants Logo" width="30" height="30">
                <h1>EduGrants</h1>
            </div>
            
            <div class="user-profile">
                <div class="avatar">
                    <img src="images/user.png" alt="User Avatar">
                </div>
                <h3>John Cruz Galang</h3>
                <span class="verified-badge">Unverified Account</span>
            </div>
            
            <nav class="navigation">
                <a href="dashboard.php" class="nav-item active">
                    <img src="images/dashboard.png" alt="Dashboard" width="20" height="20">
                    Dashboard
                </a>
                <a href="apply.php" class="nav-item">
                    <img src="images/apply.png" alt="Apply Scholarship" width="20" height="20">
                    Apply Scholarship
                </a>
                <a href="renewal.php" class="nav-item">
                    <img src="images/renewal.png" alt="Renewal" width="20" height="20">
                    Renewal
                </a>
                <a href="logout.php" class="nav-item">
                    <img src="images/logout.png" alt="Log out" width="20" height="20">
                    Log out
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <div class="user-info">
                    <img src="images/notification.png" alt="Notification" width="20" height="20">
                    <span>John Galang</span>
                </div>
            </header>
            
            <div class="content-area">
                <h3>DASHBOARD</h3>
                <!-- Dashboard content goes here -->
            </div>
        </main>
    </div>
</body>
</html>