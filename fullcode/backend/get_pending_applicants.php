<?php
// Get pending applicants from database
header('Content-Type: application/json');
require 'db_connect.php';

try {
    // Fetch all pending applicants with their personal information
    $query = "
        SELECT 
            a.applicant_id,
            a.user_id,
            a.status,
            pi.firstname,
            pi.lastname,
            pi.middlename,
            pi.gender,
            pi.date_of_birth,
            r.email,
            a.created_at
        FROM applicants a
        LEFT JOIN personal_information pi ON a.applicant_id = pi.applicant_id
        LEFT JOIN registration r ON a.user_id = r.user_id
        WHERE a.status = 'pending'
        ORDER BY a.created_at DESC
    ";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $applicants = [];
    while ($row = $result->fetch_assoc()) {
        $applicants[] = $row;
    }
    
    echo json_encode($applicants);
    
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
