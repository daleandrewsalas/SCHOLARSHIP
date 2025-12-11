<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduGrants - Settings</title>
    <link rel="stylesheet" href="profile.css">
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
                <a href="profile.php" class="nav-item">
                    <img src="images/dashboard.png" alt="Dashboard" width="20" height="20">
                    Dashboard
                </a>
                <a href="apply.php" class="nav-item">
                    <img src="images/apply.png" alt="Apply Scholarship" width="20" height="20">
                    Apply Scholarship
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
                                        <img src="images/user.png" alt="Profile">
                                    </div>
                                    <div class="profile-info">
                                        <h5>John Cruz Galang</h5>
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
                                        <input type="text" placeholder="First Name">
                                    </div>
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" placeholder="Last Name">
                                    </div>
                                    <div class="form-group">
                                        <label>Middle Name</label>
                                        <input type="text" placeholder="Middle Name">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" placeholder="Phone Number">
                                    </div>
                                    <div class="form-group">
                                        <label>Student ID</label>
                                        <input type="text" placeholder="Student ID">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Address -->
                            <div class="form-section">
                                <h4>Address</h4>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Barangay</label>
                                        <input type="text" placeholder="Barangay">
                                    </div>
                                    <div class="form-group">
                                        <label>Street/Purok</label>
                                        <input type="text" placeholder="Street/Purok">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>House Number</label>
                                        <input type="text" placeholder="House Number">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" placeholder="Email">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-content" id="security">
                            <h3>Security Settings</h3>
                            <hr>
                            <p>Security settings content will go here...</p>
                        </div>
                        
                        <div class="tab-content" id="notification">
                            <h3>Notification Settings</h3>
                            <hr>
                            <p>Notification settings content will go here...</p>
                        </div>
                        
                        <div class="tab-content" id="account">
                            <h3>Account Settings</h3>
                            <hr>
                            <p>Account settings content will go here...</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Tab switching functionality
        const tabs = document.querySelectorAll('.settings-tab');
        const contents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                tab.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html>