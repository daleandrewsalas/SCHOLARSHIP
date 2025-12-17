<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduGrants - Dashboard</title>
    <link rel="stylesheet" href="../css/Admin_Dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/fonts.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-wrapper {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .chart-wrapper h4 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 16px;
            color: #333;
        }
        .chart-container {
            position: relative;
            width: 100%;
            height: 300px;
        }
        .chart-container canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 80px;
        }
    </style>
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
    $role = $_SESSION['role'] ?? 'Applicant';
    $fullname = trim($firstname . ' ' . $lastname);
    ?>
    
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
                    <img src="../icon/renewal.png" alt="Renewal" width="20" height="20">
                    Renewal
                </a>
                <a href="logout.php" class="nav-item">
                    <img src="../icon/logout.png" alt="Log out" width="20" height="20">
                    Log out
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar" id="userAvatar"><?php echo strtoupper(substr($firstname,0,1)); ?></div>
                    <div class="user-info">
                        <div class="user-name" id="userFullname"><?php echo htmlspecialchars($fullname); ?></div>
                        <div class="user-role"><?php echo htmlspecialchars($role); ?></div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">

            
            <div class="content-area">
                <h3>DASHBOARD</h3>
                <p>Welcome, <?php echo htmlspecialchars($fullname); ?>!</p>
                <p>Email: <?php echo htmlspecialchars($email); ?></p>

                <!-- Charts Grid -->
                <div class="charts-grid">
                    <div class="chart-wrapper">
                        <h4>Applicants by Course</h4>
                        <div class="chart-container">
                            <canvas id="courseChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-wrapper">
                        <h4>Course Distribution</h4>
                        <div class="chart-container">
                            <canvas id="courseDonutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let courseChartObj = null;
        let courseDonutChartObj = null;

        // Load dashboard statistics
        function loadDashboardStats() {
            // Fetch courses and render course distribution charts
            fetch('../backend/get_accounts.php')
                .then(r => r.json())
                .then(accountsResp => {
                    const accounts = accountsResp.accounts || [];
                    const counts = {};
                    accounts.forEach(a => {
                        let course = (a.course || '').trim();
                        if (!course) course = 'Unknown';
                        counts[course] = (counts[course] || 0) + 1;
                    });
                    const courseLabels = Object.keys(counts);
                    const courseValues = courseLabels.map(l => counts[l]);
                    if (courseLabels.length === 0) {
                        renderChart('courseChart', ['No data'], [0], 'bar');
                        renderChart('courseDonutChart', ['No data'], [0], 'doughnut');
                    } else {
                        renderChart('courseChart', courseLabels, courseValues, 'bar');
                        renderChart('courseDonutChart', courseLabels, courseValues, 'doughnut');
                    }
                })
                .catch(err => {
                    console.error('Failed to load course data:', err);
                });
        }

        function renderChart(canvasId, labels, values, type = 'bar') {
            const ctx = document.getElementById(canvasId).getContext('2d');
            const backgroundColors = [
                '#1e3c72',
                '#0093E9',
                '#3b82f6',
                '#60a5fa',
                '#667eea',
                '#0284c7',
                '#0ea5e9',
                '#06b6d4',
                '#2563eb',
                '#1d4ed8'
            ];

            const cfg = {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Count',
                        data: values,
                        backgroundColor: backgroundColors.slice(0, labels.length),
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: type === 'doughnut',
                            position: 'bottom'
                        }
                    },
                    scales: type !== 'doughnut' ? {
                        y: {
                            beginAtZero: true
                        }
                    } : {}
                }
            };

            if (canvasId === 'courseChart' && courseChartObj) {
                courseChartObj.destroy();
            }
            if (canvasId === 'courseDonutChart' && courseDonutChartObj) {
                courseDonutChartObj.destroy();
            }

            const chart = new Chart(ctx, cfg);
            if (canvasId === 'courseChart') courseChartObj = chart;
            if (canvasId === 'courseDonutChart') courseDonutChartObj = chart;
        }

        // Load data on page load
        loadDashboardStats();
    </script>
</body>
</html>
