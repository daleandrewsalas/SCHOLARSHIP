<?php
// Approve pending applicant and add to system accounts
header('Content-Type: application/json');
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicant_id = $_POST['applicant_id'] ?? null;
    
    if (!$applicant_id) {
        echo json_encode(['success' => false, 'message' => 'Applicant ID required']);
        exit;
    }
    
    try {
        // Get applicant details
        $query = "
                SELECT 
                    a.applicant_id,
                    a.user_id,
                    pi.firstname,
                    pi.lastname,
                    pi.middlename,
                    pi.gender,
                    pi.date_of_birth,
                    u.email
                FROM applicants a
                LEFT JOIN personal_information pi ON a.applicant_id = pi.applicant_id
                LEFT JOIN users u ON a.user_id = u.user_id
                WHERE a.applicant_id = ?
                LIMIT 1
        ";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) throw new Exception('Prepare failed: '.$conn->error);
        
        $stmt->bind_param('i', $applicant_id);
        if (!$stmt->execute()) throw new Exception('Execute failed: '.$conn->error);
        
        $result = $stmt->get_result();
        $applicant = $result->fetch_assoc();
        
        if (!$applicant) {
            throw new Exception('Applicant not found');
        }
        
        // Update applicant status to approved
        $updateStmt = $conn->prepare("UPDATE applicants SET status = 'approved' WHERE applicant_id = ?");
        if (!$updateStmt) throw new Exception('Prepare failed: '.$conn->error);
        
        $updateStmt->bind_param('i', $applicant_id);
        if (!$updateStmt->execute()) throw new Exception('Execute failed: '.$conn->error);
        
        // Add to system_accounts table
        // Create username from email (before @) and make it unique by appending applicant_id
        $username = explode('@', $applicant['email'] ?? 'user')[0] . '_' . $applicant_id;
        $firstname = $applicant['firstname'] ?? '';
        $lastname = $applicant['lastname'] ?? '';
        $middlename = $applicant['middlename'] ?? '';
        $gender = $applicant['gender'] ?? '';
        $birthdate = $applicant['date_of_birth'] ?? null;
        $email = $applicant['email'] ?? '';
        $picture = 'default_profile.jpg';
        $account_type = 'student';
        $status = 'active';
        
        $addAccountStmt = $conn->prepare("
            INSERT INTO system_accounts 
            (username, firstname, lastname, middlename, gender, birthdate, email, picture, account_type, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if (!$addAccountStmt) throw new Exception('Prepare failed: '.$conn->error);
        
        $addAccountStmt->bind_param(
            'ssssssssss',
            $username,
            $firstname,
            $lastname,
            $middlename,
            $gender,
            $birthdate,
            $email,
            $picture,
            $account_type,
            $status
        );
        
        if (!$addAccountStmt->execute()) throw new Exception('Execute failed: '.$conn->error);

        // Generate a temporary password and hash it
        $temporaryPassword = bin2hex(random_bytes(4)); // 8 hex chars (~4 bytes)
        $passwordHash = password_hash($temporaryPassword, PASSWORD_DEFAULT);

        // Insert into registration table first so we get a user id we can reference
        $newUserId = null;
        $regCheck = $conn->query("SHOW TABLES LIKE 'registration'");
        if ($regCheck && $regCheck->num_rows > 0) {
            $insReg = $conn->prepare("INSERT INTO registration (firstname, lastname, email, password_hash) VALUES (?, ?, ?, ?)");
            if ($insReg) {
                $insReg->bind_param('ssss', $firstname, $lastname, $email, $passwordHash);
                if ($insReg->execute()) {
                    $newUserId = $conn->insert_id;
                }
                $insReg->close();
            }
        }

        // Insert into users table if exists (will display in admin accounts view)
        $usersCheck = $conn->query("SHOW TABLES LIKE 'users'");
        $usersInserted = false;
        if ($usersCheck && $usersCheck->num_rows > 0) {
            // Build simple insert with the exact columns that exist in users table
            $sql = "INSERT INTO users (first_name, last_name, email, password_hash, applicant_id) VALUES (?, ?, ?, ?, ?)";
            $stmtUsers = $conn->prepare($sql);
            if ($stmtUsers) {
                $stmtUsers->bind_param('ssssi', $firstname, $lastname, $email, $passwordHash, $applicant_id);
                if ($stmtUsers->execute()) {
                    $usersInserted = true;
                } else {
                    throw new Exception('Failed to insert into users: ' . $stmtUsers->error);
                }
                $stmtUsers->close();
            }
        }

        // Insert into approved_credentials table (with user id if available)
        $tableCheck = $conn->query("SHOW TABLES LIKE 'approved_credentials'");
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $insertApproved = $conn->prepare("INSERT INTO approved_credentials (applicant_id, user_id, temporary_password, approval_date) VALUES (?, ?, ?, NOW())");
            if ($insertApproved) {
                $uid = $newUserId ?? null;
                if ($uid === null) {
                    $nullUser = null;
                    $insertApproved->bind_param('iis', $applicant_id, $nullUser, $temporaryPassword);
                } else {
                    $insertApproved->bind_param('iis', $applicant_id, $uid, $temporaryPassword);
                }
                $insertApproved->execute();
                $insertApproved->close();
            }
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Applicant approved and added to system accounts',
            'applicant_id' => $applicant_id,
            'temporary_password' => $temporaryPassword,
            'user_id' => $newUserId,
            'users_inserted' => $usersInserted
        ]);    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

?>
