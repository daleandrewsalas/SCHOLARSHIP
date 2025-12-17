<?php
// One-time script: hash plaintext admin passwords in `admins` table.
// Run from CLI: `php hash_admin_passwords.php` or visit it briefly in a browser.

require_once __DIR__ . '/db_connect.php';

echo "Scanning admins table...\n";

$res = $conn->query("SELECT id, username, password FROM admins");
if (!$res) {
    die("Query failed: " . $conn->error);
}

$count = 0;
while ($r = $res->fetch_assoc()) {
    $id = $r['id'];
    $user = $r['username'];
    $pw = $r['password'];

    // detect common hash prefixes (bcrypt/argon)
    if (preg_match('/^\$2[yb]\$|^\$argon2/i', $pw)) {
        // already hashed
        continue;
    }

    // Hash plaintext password and update
    $newHash = password_hash($pw, PASSWORD_DEFAULT);
    $safeHash = $conn->real_escape_string($newHash);
    $safeId = $conn->real_escape_string($id);
    $upd = $conn->query("UPDATE admins SET password = '$safeHash' WHERE id = '$safeId'");
    if ($upd) {
        echo "Updated admin id={$id} user={$user}\n";
        $count++;
    } else {
        echo "Failed to update id={$id}: " . $conn->error . "\n";
    }
}

echo "Done. Passwords hashed: $count\n";

?>
