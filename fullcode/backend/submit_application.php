<?php
header('Content-Type: application/json');
require 'db_connect.php';
session_start();

// Prefer existing applicant_id from session (apply.php sets this)
$applicant_id = $_SESSION['applicant_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

try {
    if ($applicant_id) {
        // Update existing applicant to pending
        $stmt = $conn->prepare("UPDATE applicants SET status = 'pending' WHERE applicant_id = ?");
        if (!$stmt) throw new Exception('Prepare failed: '.$conn->error);
        $stmt->bind_param('i', $applicant_id);
        if (!$stmt->execute()) throw new Exception('Execute failed: '.$conn->error);
        echo json_encode(['status'=>'success','msg'=>'Application marked as pending','applicant_id'=>$applicant_id]);
        exit;
    }

    // If no applicant_id in session, try to find by user_id
    if ($user_id) {
        $stmt = $conn->prepare("SELECT applicant_id FROM applicants WHERE user_id = ? LIMIT 1");
        if (!$stmt) throw new Exception('Prepare failed: '.$conn->error);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && !empty($res['applicant_id'])) {
            $aid = (int)$res['applicant_id'];
            $stmt2 = $conn->prepare("UPDATE applicants SET status = 'pending' WHERE applicant_id = ?");
            if (!$stmt2) throw new Exception('Prepare failed: '.$conn->error);
            $stmt2->bind_param('i', $aid);
            if (!$stmt2->execute()) throw new Exception('Execute failed: '.$conn->error);
            $_SESSION['applicant_id'] = $aid;
            echo json_encode(['status'=>'success','msg'=>'Application marked as pending','applicant_id'=>$aid]);
            exit;
        }
    }

    // As a fallback, create a simple applicant record with NULL user_id
    $stmt = $conn->prepare("INSERT INTO applicants (user_id, status, created_at) VALUES (NULL, 'pending', NOW())");
    if (!$stmt) throw new Exception('Prepare failed: '.$conn->error);
    if (!$stmt->execute()) throw new Exception('Execute failed: '.$conn->error);
    
    $new_id = $conn->insert_id;
    $_SESSION['applicant_id'] = $new_id;
    echo json_encode(['status'=>'success','msg'=>'Application submitted and pending admin approval','applicant_id'=>$new_id]);

} catch(Exception $e) {
    echo json_encode(['status'=>'error','error'=>$e->getMessage()]);
}
?>
