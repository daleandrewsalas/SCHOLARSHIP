<?php
header('Content-Type: text/html; charset=utf-8');
require '../backend/db_connect.php';

echo "<h1>Database Debug Report</h1>";
echo "<p>Generated: " . date('Y-m-d H:i:s') . "</p>";

// Test 1: Users Table
echo "<h2>1. Users Table</h2>";
$r = $conn->query("SELECT user_id, first_name, last_name, email, applicant_id FROM users ORDER BY user_id DESC LIMIT 10");
if ($r && $r->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>user_id</th><th>first_name</th><th>last_name</th><th>email</th><th>applicant_id</th></tr>";
    while ($row = $r->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['first_name'] . "</td>";
        echo "<td>" . $row['last_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . ($row['applicant_id'] ? $row['applicant_id'] : 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>NO USERS FOUND</p>";
}

// Test 2: Applicants Table
echo "<h2>2. Applicants Table</h2>";
$r = $conn->query("SELECT applicant_id, user_id, status FROM applicants ORDER BY applicant_id DESC LIMIT 10");
if ($r && $r->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>applicant_id</th><th>user_id</th><th>status</th></tr>";
    while ($row = $r->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['applicant_id'] . "</td>";
        echo "<td>" . ($row['user_id'] ? $row['user_id'] : 'NULL') . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>NO APPLICANTS FOUND</p>";
}

// Test 3: Personal Information
echo "<h2>3. Personal Information</h2>";
$r = $conn->query("SELECT id, applicant_id, firstname, lastname FROM personal_information ORDER BY id DESC LIMIT 10");
if ($r && $r->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>id</th><th>applicant_id</th><th>firstname</th><th>lastname</th></tr>";
    while ($row = $r->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['applicant_id'] . "</td>";
        echo "<td>" . $row['firstname'] . "</td>";
        echo "<td>" . $row['lastname'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>NO PERSONAL INFO FOUND</p>";
}

// Test 4: Full Link Check
echo "<h2>4. Full Data Link Check</h2>";
$r = $conn->query("
    SELECT 
        u.user_id,
        u.first_name,
        u.applicant_id,
        a.status,
        pi.firstname as app_firstname
    FROM users u
    LEFT JOIN applicants a ON u.applicant_id = a.applicant_id
    LEFT JOIN personal_information pi ON a.applicant_id = pi.applicant_id
    ORDER BY u.user_id DESC LIMIT 5
");
if ($r && $r->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>user_id</th><th>user_first_name</th><th>applicant_id</th><th>app_status</th><th>pi_firstname</th></tr>";
    while ($row = $r->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['first_name'] . "</td>";
        echo "<td>" . ($row['applicant_id'] ? $row['applicant_id'] : 'NULL') . "</td>";
        echo "<td>" . ($row['status'] ? $row['status'] : 'NULL') . "</td>";
        echo "<td>" . ($row['app_firstname'] ? $row['app_firstname'] : 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<p>Test links:<br>";
echo "Register: <a href='register.php'>register.php</a><br>";
echo "Login: <a href='login.php'>login.php</a><br>";
echo "Apply: <a href='apply.php'>apply.php</a><br>";
echo "</p>";
?>
