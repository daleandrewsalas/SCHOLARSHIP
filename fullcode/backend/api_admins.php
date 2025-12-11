<?php
// backend/api_admins.php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

// Helper: read JSON body
$input = json_decode(file_get_contents('php://input'), true);

$method = $_SERVER['REQUEST_METHOD'];

// Helper function: check if column exists
function columnExists($pdo, $table, $column) {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM $table LIKE ?");
        $stmt->execute([$column]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Check if password column exists in admins table
$hasPasswordColumn = columnExists($pdo, 'admins', 'password');
// Check name column variations
$hasFullnameColumn = columnExists($pdo, 'admins', 'fullname');
$hasFirstNameColumn = columnExists($pdo, 'admins', 'first_name');
$hasLastNameColumn = columnExists($pdo, 'admins', 'last_name');
// Check if email column exists
$hasEmailColumn = columnExists($pdo, 'admins', 'email');

try {
    if ($method === 'GET') {
        // List all or single if id provided
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $row = $stmt->fetch();
            ob_end_clean();
            echo json_encode(['admin' => $row]);
        } else {
            // optional: search by q param
            if (!empty($_GET['q'])) {
                $q = '%' . $_GET['q'] . '%';
                // build dynamic search columns based on existing schema
                $searchCols = [];
                if (columnExists($pdo, 'admins', 'employee_id')) $searchCols[] = 'employee_id';
                if (columnExists($pdo, 'admins', 'username')) $searchCols[] = 'username';
                if ($hasFullnameColumn) $searchCols[] = 'fullname';
                if ($hasFirstNameColumn) $searchCols[] = 'first_name';
                if ($hasLastNameColumn) $searchCols[] = 'last_name';
                if (columnExists($pdo, 'admins', 'role')) $searchCols[] = 'role';
                if (empty($searchCols)) {
                    $stmt = $pdo->query("SELECT * FROM admins ORDER BY id DESC");
                } else {
                    // prepare WHERE clause like: col1 LIKE ? OR col2 LIKE ?
                    $where = implode(' LIKE ? OR ', $searchCols) . ' LIKE ?';
                    $sql = "SELECT * FROM admins WHERE $where ORDER BY id DESC";
                    $stmt = $pdo->prepare($sql);
                    $params = array_fill(0, count($searchCols), $q);
                    $stmt->execute($params);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM admins ORDER BY id DESC");
            }
            $rows = $stmt->fetchAll();
            ob_end_clean();
            echo json_encode(['admins' => $rows]);
        }
    } elseif ($method === 'POST') {
        // Create new admin
        $username = $input['username'] ?? '';
        $fullname = $input['fullname'] ?? '';
        $role = $input['role'] ?? '';
        $email = $input['email'] ?? null;
        $password = $input['password'] ?? null;

        if (!$username) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['error' => 'Username is required']);
            exit;
        }
        
        // Check uniqueness of username and email (if columns exist)
        if (columnExists($pdo, 'admins', 'username')) {
            $chk = $pdo->prepare("SELECT id FROM admins WHERE username = ? LIMIT 1");
            $chk->execute([$username]);
            if ($chk->fetch()) {
                ob_end_clean();
                http_response_code(409);
                echo json_encode(['error' => 'Username already exists']);
                exit;
            }
        }
        if ($hasEmailColumn && $email) {
            $chk = $pdo->prepare("SELECT id FROM admins WHERE email = ? LIMIT 1");
            $chk->execute([$email]);
            if ($chk->fetch()) {
                ob_end_clean();
                http_response_code(409);
                echo json_encode(['error' => 'Email already exists']);
                exit;
            }
        }
        // Hash password if provided
        $hashedPassword = $password ? password_hash($password, PASSWORD_DEFAULT) : null;

        // Insert depending on schema
        if ($hasFullnameColumn) {
            // use fullname column
            // build insert depending on whether email/password columns exist
            if ($hasPasswordColumn) {
                if ($hasEmailColumn) {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, fullname, role, email, password) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$username, $fullname, $role, $email, $hashedPassword]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, fullname, role, password) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $fullname, $role, $hashedPassword]);
                }
            } else {
                if ($hasEmailColumn) {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, fullname, role, email) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $fullname, $role, $email]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, fullname, role) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $fullname, $role]);
                }
            }
        } elseif ($hasFirstNameColumn || $hasLastNameColumn) {
            // split fullname into first/last for compatibility
            $names = array_filter(explode(' ', trim($fullname)));
            $first_name = $names[0] ?? '';
            $last_name = isset($names[1]) ? implode(' ', array_slice($names, 1)) : '';
            if ($hasPasswordColumn) {
                if ($hasEmailColumn) {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, first_name, last_name, role, email, password) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$username, $first_name, $last_name, $role, $email, $hashedPassword]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, first_name, last_name, role, password) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$username, $first_name, $last_name, $role, $hashedPassword]);
                }
            } else {
                if ($hasEmailColumn) {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, first_name, last_name, role, email) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$username, $first_name, $last_name, $role, $email]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, first_name, last_name, role) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $first_name, $last_name, $role]);
                }
            }
        } else {
            // fallback: insert minimal fields
            if ($hasPasswordColumn) {
                if ($hasEmailColumn) {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, role, email, password) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $role, $email, $hashedPassword]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, role, password) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $role, $hashedPassword]);
                }
            } else {
                if ($hasEmailColumn) {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, role, email) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $role, $email]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO admins (username, role) VALUES (?, ?)");
                    $stmt->execute([$username, $role]);
                }
            }
        }
        ob_end_clean();
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } elseif ($method === 'PUT') {
        // Update admin
        $id = $input['id'] ?? null;
        if (!$id) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }
        
        $username = $input['username'] ?? null;
        $fullname = $input['fullname'] ?? null;
        $email = $input['email'] ?? null;
        $password = $input['password'] ?? null;
        
        $updates = [];
        $values = [];
        
        if ($username) {
            $updates[] = 'username = ?';
            $values[] = $username;
        }
        if ($fullname) {
            // update either fullname or first_name/last_name depending on schema
            if ($hasFullnameColumn) {
                $updates[] = 'fullname = ?';
                $values[] = $fullname;
            } else {
                $names = array_filter(explode(' ', trim($fullname)));
                $first_name = $names[0] ?? '';
                $last_name = isset($names[1]) ? implode(' ', array_slice($names, 1)) : '';
                if ($hasFirstNameColumn) {
                    $updates[] = 'first_name = ?';
                    $values[] = $first_name;
                }
                if ($hasLastNameColumn) {
                    $updates[] = 'last_name = ?';
                    $values[] = $last_name;
                }
            }
        }
        if ($email !== null) {
            if (columnExists($pdo, 'admins', 'email')) {
                // ensure new email isn't used by another admin
                $chk = $pdo->prepare("SELECT id FROM admins WHERE email = ? LIMIT 1");
                $chk->execute([$email]);
                $row = $chk->fetch();
                if ($row && intval($row['id']) !== intval($id)) {
                    ob_end_clean();
                    http_response_code(409);
                    echo json_encode(['error' => 'Email already exists for another admin']);
                    exit;
                }
                $updates[] = 'email = ?';
                $values[] = $email;
            }
        }
        if ($password && $hasPasswordColumn) {
            $updates[] = 'password = ?';
            $values[] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        if (empty($updates)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'msg' => 'No data to update']);
            exit;
        }
        
        $values[] = $id;
        $sql = "UPDATE admins SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        ob_end_clean();
        echo json_encode(['success' => true]);
    } elseif ($method === 'DELETE') {
        // Delete by id
        $id = $_GET['id'] ?? ($input['id'] ?? null);
        if (!$id) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        ob_end_clean();
        echo json_encode(['success' => true]);
    } else {
        ob_end_clean();
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>