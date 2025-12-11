<?php
header("Content-Type: application/json");
session_start();
require "db_connect.php";

// ✅ Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input["username"], $input["password"])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing username or password"
    ]);
    exit;
}

$username = $input["username"];
$password = $input["password"];

// ✅ Prepare SQL
$sql = "SELECT id, fullname, role, password FROM admins WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// ✅ Check user
if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    // ✅ If password is NOT hashed (plain text)
    if ($password === $admin["password"]) {

        $_SESSION["user_id"] = $admin["id"];
        $_SESSION["fullname"] = $admin["fullname"];
        $_SESSION["role"] = $admin["role"];

        echo json_encode([
            "success" => true,
            "user" => [
                "id" => $admin["id"],
                "fullname" => $admin["fullname"],
                "role" => $admin["role"]
            ]
        ]);
        exit;
    }
}

// ❌ Wrong login
echo json_encode([
    "success" => false,
    "message" => "Invalid username or password"
]);
exit;
?>
