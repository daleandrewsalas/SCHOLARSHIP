<?php
// pending_accounts.php
// Returns pending applicants (GET) and handles approve/reject (POST)
header('Content-Type: application/json');
require 'db_connect.php';

// Helper: send json
function js($arr){ echo json_encode($arr); exit; }

// GET: list pending applicants
if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $q = isset($_GET['q']) ? $_GET['q'] : '';
    if ($q !== ''){
        $sql = "SELECT * FROM applicants WHERE status='pending' AND (firstname LIKE ? OR lastname LIKE ? OR email LIKE ?) ORDER BY id DESC";
        $like = "%" . $q . "%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $like, $like, $like);
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        $res = $conn->query("SELECT * FROM applicants WHERE status='pending' ORDER BY id DESC");
    }

    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    js(['status'=>'success','count'=>count($rows),'accounts'=>$rows]);
}

// POST: actions approve / reject
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if (!$action || !$id) js(['status'=>'error','error'=>'Invalid parameters']);

    if ($action === 'reject'){
        // applicants table uses applicant_id as primary key
        $del = $conn->prepare("DELETE FROM applicants WHERE applicant_id = ?");
        $del->bind_param('i',$id);
        if ($del->execute()) js(['status'=>'success']);
        else js(['status'=>'error','error'=>$conn->error]);
    }

    if ($action === 'approve'){
        // fetch applicant
        // applicants table uses applicant_id as primary key
        $stmt = $conn->prepare("SELECT * FROM applicants WHERE applicant_id = ? AND status='pending'");
        $stmt->bind_param('i',$id);
        $stmt->execute();
        $res = $stmt->get_result();
        $app = $res->fetch_assoc();
        if (!$app) js(['status'=>'error','error'=>'Applicant not found']);

        // check if email already exists in registration
        $check = $conn->prepare("SELECT user_id FROM registration WHERE email = ?");
        $check->bind_param('s',$app['email']);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) js(['status'=>'error','error'=>'Email already registered']);

        // generate temp password
        $temp = bin2hex(random_bytes(4)); // 8 chars
        $hash = password_hash($temp, PASSWORD_DEFAULT);

        // insert into registration
        $ins = $conn->prepare("INSERT INTO registration (firstname, lastname, middlename, email, password_hash, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $ins->bind_param('sssss', $app['firstname'], $app['lastname'], $app['middlename'], $app['email'], $hash);
        if (!$ins->execute()) js(['status'=>'error','error'=>'Insert failed: '.$conn->error]);

        // remove from applicants
        $del = $conn->prepare("DELETE FROM applicants WHERE id = ?");
        $del->bind_param('i',$id);
        $del->execute();

        js(['status'=>'success','temp_password'=>$temp]);
    }

    js(['status'=>'error','error'=>'Unknown action']);
}

// Method not allowed
http_response_code(405);
js(['status'=>'error','error'=>'Method not allowed']);

?>