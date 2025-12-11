<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $result = [
        'users_count' => 0,
        'applicants_count' => 0,
        'personal_info_count' => 0,
        'residency_info_count' => 0,
        'family_bg_count' => 0,
        'users_with_applicant_id' => [],
        'applicants_by_status' => [],
        'links_check' => []
    ];
    
    // Count users
    $r = $conn->query("SELECT COUNT(*) as cnt FROM users");
    if ($r) {
        $row = $r->fetch_assoc();
        $result['users_count'] = $row['cnt'];
    }
    
    // Count applicants
    $r = $conn->query("SELECT COUNT(*) as cnt FROM applicants");
    if ($r) {
        $row = $r->fetch_assoc();
        $result['applicants_count'] = $row['cnt'];
    }
    
    // Count personal info
    $r = $conn->query("SELECT COUNT(*) as cnt FROM personal_information");
    if ($r) {
        $row = $r->fetch_assoc();
        $result['personal_info_count'] = $row['cnt'];
    }
    
    // Count residency
    $r = $conn->query("SELECT COUNT(*) as cnt FROM residency_information");
    if ($r) {
        $row = $r->fetch_assoc();
        $result['residency_info_count'] = $row['cnt'];
    }
    
    // Count family background
    $r = $conn->query("SELECT COUNT(*) as cnt FROM family_background");
    if ($r) {
        $row = $r->fetch_assoc();
        $result['family_bg_count'] = $row['cnt'];
    }
    
    // Show users with applicant_id
    $r = $conn->query("SELECT user_id, first_name, last_name, applicant_id FROM users LIMIT 10");
    if ($r) {
        while ($row = $r->fetch_assoc()) {
            $result['users_with_applicant_id'][] = $row;
        }
    }
    
    // Show applicants by status
    $r = $conn->query("SELECT status, COUNT(*) as cnt FROM applicants GROUP BY status");
    if ($r) {
        while ($row = $r->fetch_assoc()) {
            $result['applicants_by_status'][] = $row;
        }
    }
    
    // Check if there are applicants with NULL user_id
    $r = $conn->query("SELECT COUNT(*) as cnt FROM applicants WHERE user_id IS NULL");
    if ($r) {
        $row = $r->fetch_assoc();
        $result['applicants_with_null_user_id'] = $row['cnt'];
    }
    
    // Check personal_information linked to applicants
    $r = $conn->query("SELECT pi.applicant_id, pi.firstname, pi.lastname, a.status FROM personal_information pi LEFT JOIN applicants a ON pi.applicant_id = a.applicant_id LIMIT 10");
    if ($r) {
        $links = [];
        while ($row = $r->fetch_assoc()) {
            $links[] = $row;
        }
        $result['links_check'] = $links;
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
