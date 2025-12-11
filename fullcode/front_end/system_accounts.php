<?php
session_start();
require_once __DIR__ . '/../backend/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

$firstname = $_SESSION['firstname'] ?? 'User';
$lastname = $_SESSION['lastname'] ?? '';
$fullname = trim($firstname . ' ' . $lastname);

// Fetch all approved accounts
$accounts = [];
$query = "SELECT account_id, username, firstname, lastname, email, gender, birthdate, account_type, status, created_at FROM system_accounts ORDER BY created_at DESC";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $accounts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduGrants - System Accounts</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/fonts.css">
    <style>
        .accounts-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .accounts-table thead {
            background: #f5f5f5;
            border-bottom: 2px solid #ddd;
        }
        .accounts-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        .accounts-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .accounts-table tbody tr:hover {
            background: #f9f9f9;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .search-box {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }
    </style>
</head>
<body>
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
                <span class="verified-badge">Verified Account</span>
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
                <a href="system_accounts.php" class="nav-item active">
                    <img src="../icon/apply.png" alt="System Accounts" width="20" height="20">
                    System Accounts
                </a>
                <a href="pending_accounts.html" class="nav-item">
                    <img src="../icon/apply.png" alt="Pending Accounts" width="20" height="20">
                    Pending Accounts
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
                <h3>SYSTEM ACCOUNTS (Approved Applicants)</h3>
                <p>Total Approved: <strong><?php echo count($accounts); ?></strong></p>
                
                <input type="text" class="search-box" id="searchBox" placeholder="Search by name or email...">
                
                <?php if (!empty($accounts)): ?>
                    <table class="accounts-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Gender</th>
                                <th>Birthdate</th>
                                <th>Account Type</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $account): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($account['account_id']); ?></td>
                                    <td><?php echo htmlspecialchars($account['username']); ?></td>
                                    <td><?php echo htmlspecialchars($account['firstname'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($account['lastname'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($account['email'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($account['gender'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($account['birthdate'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($account['account_type'] ?? 'student'); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $account['status'] == 'active' ? 'active' : 'inactive'; ?>">
                                            <?php echo htmlspecialchars($account['status'] ?? 'unknown'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($account['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="margin-top: 20px; color: #666;">No approved applicants yet.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script>
        // Simple search functionality
        document.getElementById('searchBox').addEventListener('keyup', function(e) {
            const filter = e.target.value.toLowerCase();
            const table = document.querySelector('.accounts-table tbody');
            if (!table) return;
            
            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
