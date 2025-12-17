<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db_connect.php';

// Detect AJAX requests so we can return JSON for XHR, but redirect for normal form submits.
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $email = $_SESSION['email'] ?? null;
    $firstname = $_SESSION['firstname'] ?? '';
    $lastname = $_SESSION['lastname'] ?? '';
    
    // require only a valid user id; try to resolve missing email from DB
    if (!$user_id) {
        // Log session/cookie state to help debug missing session issues
        $logDir = __DIR__ . '/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
        $logFile = $logDir . '/renewal_debug.log';
        $entry = "[" . date('Y-m-d H:i:s') . "] User not logged in on submit_renewal\n";
        $entry .= "Session ID: " . session_id() . "\n";
        $entry .= "\$_SESSION: " . print_r($_SESSION, true) . "\n";
        $entry .= "\
    COOKIE: " . print_r($_COOKIE, true) . "\n";
        $entry .= "\
    SERVER: " . print_r(array_intersect_key($_SERVER, array_flip(['HTTP_HOST','REQUEST_URI','REMOTE_ADDR','HTTP_USER_AGENT'])), true) . "\n";
        @file_put_contents($logFile, $entry, FILE_APPEND);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            exit;
        }

        $_SESSION['renewal_status'] = ['success' => false, 'message' => 'User not logged in'];
        header('Location: ../front_end/renewal.php');
        exit;
    }

    // If email missing in session, try to read from DB based on role
    if (empty($email)) {
        $role = $_SESSION['role'] ?? null;
        try {
            if ($role === 'admin') {
                $q = $conn->prepare("SELECT email, fullname FROM admins WHERE id = ? LIMIT 1");
                if ($q) {
                    $q->bind_param('i', $user_id);
                    $q->execute();
                    $res = $q->get_result();
                    $row = $res->fetch_assoc();
                    if ($row) {
                        $email = $row['email'] ?? $email;
                        if (empty($firstname) && !empty($row['fullname'])) $firstname = $row['fullname'];
                    }
                    $q->close();
                }
            } else {
                $q = $conn->prepare("SELECT email, first_name, last_name FROM users WHERE user_id = ? LIMIT 1");
                if ($q) {
                    $q->bind_param('i', $user_id);
                    $q->execute();
                    $res = $q->get_result();
                    $row = $res->fetch_assoc();
                    if ($row) {
                        $email = $row['email'] ?? $email;
                        if (empty($firstname)) $firstname = $row['first_name'] ?? $firstname;
                        if (empty($lastname)) $lastname = $row['last_name'] ?? $lastname;
                    }
                    $q->close();
                }
            }
        } catch (Exception $e) {
            // ignore DB lookup errors; continue with whatever we have
        }
    }

    $school_year = trim($_POST['school_year'] ?? '');
    $school = trim($_POST['school'] ?? '');
    $gpa = floatval(trim($_POST['gpa'] ?? '0'));
    
    if (empty($school_year) || empty($school) || empty($gpa)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $_SESSION['renewal_status'] = ['success' => false, 'message' => 'Missing required fields'];
        header('Location: ../front_end/renewal.php');
        exit;
    }

    try {
        // Check if renewal_requests table exists; create if missing
        $tableCheck = $conn->query("SHOW TABLES LIKE 'renewal_requests'");
        if ($tableCheck->num_rows === 0) {
            $createTable = "CREATE TABLE renewal_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                email VARCHAR(300),
                firstname VARCHAR(300),
                lastname VARCHAR(300),
                school_year VARCHAR(100),
                school VARCHAR(300),
                gpa DECIMAL(5,2),
                status VARCHAR(50) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $conn->query($createTable);
        } else {
            // Table exists: ensure required columns are present (fix older schemas)
            $dbName = '';
            $dbRes = $conn->query("SELECT DATABASE() as db");
            if ($dbRes) {
                $r = $dbRes->fetch_assoc();
                $dbName = $r['db'] ?? '';
            }

            $existing = [];
            if ($dbName) {
                $colRes = $conn->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . $conn->real_escape_string($dbName) . "' AND TABLE_NAME = 'renewal_requests'");
                if ($colRes) {
                    while ($c = $colRes->fetch_assoc()) {
                        $existing[] = $c['COLUMN_NAME'];
                    }
                }
            }

            $required = [
                'email' => "VARCHAR(300)",
                'firstname' => "VARCHAR(300)",
                'lastname' => "VARCHAR(300)",
                'school_year' => "VARCHAR(100)",
                'school' => "VARCHAR(300)",
                'gpa' => "DECIMAL(5,2)",
                'status' => "VARCHAR(50) DEFAULT 'pending'",
                'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
            ];

            foreach ($required as $col => $def) {
                if (!in_array($col, $existing)) {
                    $sql = "ALTER TABLE renewal_requests ADD COLUMN `" . $col . "` " . $def;
                    // Best effort; ignore failures
                    @$conn->query($sql);
                }
            }
        }

        // Insert renewal request
        $stmt = $conn->prepare("
            INSERT INTO renewal_requests 
            (user_id, email, firstname, lastname, school_year, school, gpa, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");

        if ($stmt) {
            $stmt->bind_param('isssssd', $user_id, $email, $firstname, $lastname, $school_year, $school, $gpa);
            if ($stmt->execute()) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Renewal request submitted successfully']);
                    $stmt->close();
                    exit;
                }

                $_SESSION['renewal_status'] = ['success' => true, 'message' => 'Renewal request submitted successfully'];
                $stmt->close();
                header('Location: ../front_end/renewal.php');
                exit;
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Failed to submit renewal request']);
                    $stmt->close();
                    exit;
                }

                $_SESSION['renewal_status'] = ['success' => false, 'message' => 'Failed to submit renewal request'];
                $stmt->close();
                header('Location: ../front_end/renewal.php');
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } catch (Exception $e) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }

        $_SESSION['renewal_status'] = ['success' => false, 'message' => $e->getMessage()];
        header('Location: ../front_end/renewal.php');
        exit;
    }
    } else {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $_SESSION['renewal_status'] = ['success' => false, 'message' => 'Invalid request method'];
        header('Location: ../front_end/renewal.php');
        exit;
    }
?>
