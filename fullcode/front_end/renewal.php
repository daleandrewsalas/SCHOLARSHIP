<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduGrants - Renewal</title>
    <link rel="stylesheet" href="renewal.css">
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
                <a href="dashboard.php" class="nav-item">
                    <img src="images/dashboard.png" alt="Dashboard" width="20" height="20">
                    Dashboard
                </a>

                <a href="apply.php" class="nav-item">
                    <img src="images/apply.png" alt="Apply Scholarship" width="20" height="20">
                    Apply Scholarship
                </a>

                <a href="renewal.php" class="nav-item active">
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
                <h2>RENEWAL</h2>
                <div class="page-header">
                    
                    <p class="subtitle">Welcome to the Scholarship Renewal Page. Please submit your requirements for the next semester.</p>
                </div>

                <!-- BEAUTIFIED SCHOLARSHIP RENEWAL FORM -->
                <div class="renewal-wrapper">
                    <h3 class="form-title">Scholarship Renewal Form</h3>

                    <form class="renewal-modern">

                        <div class="grid-2">
                            <div class="input-box">
                                <label>School Year / Semester</label>
                                <input type="text" placeholder="e.g. 2025 - 2026 / 1st Semester">
                            </div>

                            <div class="input-box">
                                <label>School</label>
                                <input type="text" placeholder="Enter your school name">
                            </div>
                        </div>

                        <div class="grid-1">
                            <div class="input-box">
                                <label>GPA</label>
                                <input type="text" placeholder="Enter GPA">
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

                        <button type="submit" class="btn-submit">Submit</button>

                    </form>
                </div>

            </div>
        </main>
    </div>
</body>
</html>