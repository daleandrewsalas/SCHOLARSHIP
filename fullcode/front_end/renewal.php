<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is not logged in, redirect to login page.
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduGrants - Renewal</title>
    <link rel="stylesheet" href="../css/Admin_Dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/fonts.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../icon/graducation.png" alt="EduGrants Logo" width="30" height="30">
                <h1>EduGrants</h1>
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

                <a href="renewal.php" class="nav-item">
                    <img src="../icon/renewal.png   " alt="Renewal" width="20" height="20">
                    Renewal
                </a>

                <a href="login.php" class="nav-item">
                    <img src="../icon/logout.png" alt="Log out" width="20" height="20">
                    Log out
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar" id="userAvatar">?</div>
                    <div class="user-info">
                        <div class="user-name" id="userFullname"></div>
                        <div class="user-role" id="userRole">Applicant</div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
    
            
            <div class="content-area">
                <h2>RENEWAL</h2>
                <div class="page-header">
                    
                    <p class="subtitle">Welcome to the Scholarship Renewal Page. Please submit your requirements for the next semester.</p>
                </div>

                <!-- BEAUTIFIED SCHOLARSHIP RENEWAL FORM -->
                <div class="renewal-wrapper">
                    <h3 class="form-title">Scholarship Renewal Form</h3>

                    <?php if (!empty(
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        $_SESSION['renewal_status']
                    )): 
                        $rs = $_SESSION['renewal_status'];
                        $cls = $rs['success'] ? 'background: #e6ffed; border:1px solid #2ecc71; color:#155724; padding:10px; margin-bottom:12px;' : 'background:#fff0f0; border:1px solid #e74c3c; color:#7a1f1f; padding:10px; margin-bottom:12px;';
                    ?>
                        <div class="renewal-flash" style="<?php echo $cls; ?>">
                            <?php echo htmlspecialchars($rs['message']); ?>
                        </div>
                    <?php unset($_SESSION['renewal_status']); endif; ?>

                    <form class="renewal-modern" method="POST" action="../backend/submit_renewal.php">

                        <div class="grid-2">
                            <div class="input-box">
                                <label>School Year / Semester</label>
                                <input type="text" name="school_year" placeholder="e.g. 2025 - 2026 / 1st Semester" required>
                            </div>

                            <div class="input-box">
                                <label>School</label>
                                <input type="text" name="school" placeholder="Enter your school name" required>
                            </div>
                        </div>

                        <div class="grid-1">
                            <div class="input-box">
                                <label>GPA</label>
                                <input type="text" name="gpa" placeholder="Enter GPA" required>
                            </div>
                        </div>

                        <h4 class="section-title">Requirements</h4>

                        <div class="requirements-grid">

                            <div class="requirement-card">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" id="req1">
                                    <label for="req1">Photo copy of report card (Form 138)</label>
                                </div>
                                <input type="file">
                            </div>

                            <div class="requirement-card">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" id="req2">
                                    <label for="req2">Latest</label>
                                </div>
                                <input type="file">
                            </div>

                            <div class="requirement-card">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" id="req3">
                                    <label for="req3">Barangay clearance</label>
                                </div>
                                <input type="file">
                            </div>

                            <div class="requirement-card">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" id="req4">
                                    <label for="req4">
                                        Upload any types of valid ID
                                        <small>Voter's ID, National ID, etc.</small>
                                    </label>
                                </div>
                                <input type="file">
                            </div>

                        </div>

                        <button type="submit" class="btn-submit">Submit Renewal</button>

                    </form>
                </div>

            </div>
        </main>
    </div>

    <script>
        function loadUserName() {
            fetch('../backend/get_user.php')
            .then(res => res.json())
            .then(data => {
                if (data && data.name) {
                    const fullName = data.name || 'User';
                    const avatar = fullName.charAt(0).toUpperCase();
                    
                    const avatarEl = document.getElementById('userAvatar');
                    const nameEl = document.getElementById('userFullname');
                    
                    if (avatarEl) avatarEl.textContent = avatar;
                    if (nameEl) nameEl.textContent = fullName;
                }
            })
            .catch(err => console.error('Failed to load user data:', err));
        }

        // Load on page load
        document.addEventListener('DOMContentLoaded', loadUserName);
    </script>
</body>
</html>
