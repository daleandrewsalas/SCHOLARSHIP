<?php
session_start();
include("../config/database.php");

// Add new schedule
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $schedule_date = $_POST['schedule_date'];
    $total_slots = $_POST['total_slots'] ?? 10;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO schedules (schedule_date, total_slots, remaining_slots) VALUES (?, ?, ?)");
        $stmt->execute([$schedule_date, $total_slots, $total_slots]);
        $success_message = "Schedule added successfully!";
    } catch(PDOException $e) {
        $error_message = "Error adding schedule: " . $e->getMessage();
    }
}

// Delete schedule
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $schedule_id = $_POST['schedule_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM schedules WHERE schedule_id = ?");
        $stmt->execute([$schedule_id]);
        $success_message = "Schedule deleted successfully!";
    } catch(PDOException $e) {
        $error_message = "Error deleting schedule: " . $e->getMessage();
    }
}

// Fetch all schedules
try {
    $stmt = $pdo->query("SELECT * FROM schedules ORDER BY schedule_date DESC");
    $schedules = $stmt->fetchAll();
} catch(PDOException $e) {
    $schedules = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointment Schedules</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/fonts.css">
    <style>
        .schedule-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-top: 30px;
        }
        
        .add-schedule-form {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 20px;
            align-items: flex-end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .btn-add {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-add:hover {
            background: #229954;
        }
        
        .schedules-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .schedules-table th {
            background: #e74c3c;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        .schedules-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        .schedules-table tr:hover {
            background: #f5f5f5;
        }
        
        .status-active {
            color: #27ae60;
            font-weight: 600;
        }
        
        .status-inactive {
            color: #c0392b;
            font-weight: 600;
        }
        
        .btn-delete {
            background: #c0392b;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn-delete:hover {
            background: #a93226;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="images/graducation.png" alt="EduGrants Logo" width="30" height="30">
                <h1>EduGrants</h1>
            </div>
            <nav class="navigation">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="manage_schedules.php" class="nav-item">Manage Schedules</a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="content-area">
                <h3>MANAGE APPOINTMENT SCHEDULES</h3>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <!-- Add Schedule Form -->
                <div class="schedule-container">
                    <h4>Add New Schedule</h4>
                    <form method="POST" class="add-schedule-form">
                        <input type="hidden" name="action" value="add">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="schedule_date" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Total Slots</label>
                                <input type="number" name="total_slots" value="10" min="1" max="100">
                            </div>
                            <button type="submit" class="btn-add">Add Schedule</button>
                        </div>
                    </form>
                </div>
                
                <!-- Schedules List -->
                <div class="schedule-container">
                    <h4>Available Schedules</h4>
                    <table class="schedules-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Slots</th>
                                <th>Remaining Slots</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($schedules)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 20px;">No schedules available</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($schedules as $schedule): ?>
                                    <tr>
                                        <td><?php echo date('F j, Y', strtotime($schedule['schedule_date'])); ?></td>
                                        <td><?php echo $schedule['total_slots']; ?></td>
                                        <td><?php echo $schedule['remaining_slots']; ?></td>
                                        <td>
                                            <span class="<?php echo $schedule['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                                <?php echo $schedule['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="schedule_id" value="<?php echo $schedule['schedule_id']; ?>">
                                                <button type="submit" class="btn-delete" onclick="return confirm('Are you sure?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
