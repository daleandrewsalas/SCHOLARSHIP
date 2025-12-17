<?php
// Include DB Connection
session_start();
require("../backend/db_connect.php");

$success_message = "";
$current_section = isset($_GET['section']) ? $_GET['section'] : "personal-info";

// Get or create applicant_id from session
if (!isset($_SESSION['applicant_id'])) {
    // Create a new applicant record
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $stmt = $conn->prepare("INSERT INTO applicants (user_id, status) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("is", $user_id, $status);
        $status = 'pending';
        if ($stmt->execute()) {
            $_SESSION['applicant_id'] = $conn->insert_id;
        }
    }
}

$applicant_id = $_SESSION['applicant_id'] ?? null;

// Retrieve existing data
$personal_data = null;
$residency_data = null;
$family_data = null;
$appointment_data = null;

// Fetch data using MySQLi
$personal_data = null;
$residency_data = null;
$family_data = null;
$appointment_data = null;

if ($applicant_id) {
    // Get personal information for this applicant
    $stmt = $conn->prepare("SELECT * FROM personal_information WHERE applicant_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $applicant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $personal_data = $result->fetch_assoc();
    }
    
    // Get residency information for this applicant
    $stmt = $conn->prepare("SELECT * FROM residency_information WHERE applicant_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $applicant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $residency_data = $result->fetch_assoc();
    }
    
    // Get family background for this applicant
    $stmt = $conn->prepare("SELECT * FROM family_background WHERE applicant_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $applicant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $family_data = $result->fetch_assoc();
    }
    
    // Get appointment for this applicant
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE applicant_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $applicant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointment_data = $result->fetch_assoc();
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Personal Information Section
    if (!isset($_POST['step']) || $_POST['step'] === 'personal-info') {
        $lastname = $_POST["lastname"] ?? '';
        $firstname = $_POST["firstname"] ?? '';
        $middlename = $_POST["middlename"] ?? '';
        $gender = $_POST["gender"] ?? '';
        $civil_status = $_POST["civil_status"] ?? '';
        $date_of_birth = $_POST["date_of_birth"] ?? '';
        $course = $_POST["course"] ?? '';
        $gpa = $_POST["gpa"] ?? '';
        $school_name = $_POST["school_name"] ?? '';
        $skills = $_POST["skills"] ?? '';
        $talent = $_POST["talent"] ?? '';

        // Check if record exists
        if ($personal_data) {
            // Update existing record
            $stmt = $conn->prepare("UPDATE personal_information SET 
                    lastname=?, firstname=?, middlename=?, gender=?, civil_status=?, 
                    date_of_birth=?, course=?, gpa=?, school_name=?, skills=?, talent=?
                    WHERE applicant_id=?");
            if ($stmt) {
                $stmt->bind_param("ssssssssdssi", $lastname, $firstname, $middlename, $gender, $civil_status,
                    $date_of_birth, $course, $gpa, $school_name, $skills, $talent, $applicant_id);
                $stmt->execute();
            }
        } else {
            // Insert new record
            $stmt = $conn->prepare("INSERT INTO personal_information 
                    (applicant_id, lastname, firstname, middlename, gender, civil_status, date_of_birth, course, gpa, school_name, skills, talent)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("isssssssdsss", $applicant_id, $lastname, $firstname, $middlename, $gender, $civil_status,
                    $date_of_birth, $course, $gpa, $school_name, $skills, $talent);
                $stmt->execute();
            }
        }

        header("Location: apply.php?section=residency");
        exit();
    }
    
    // Residency Section
    if (isset($_POST['step']) && $_POST['step'] === 'residency') {
        $permanent_address = $_POST["permanent_address"] ?? '';
        $residency_duration = $_POST["residency_duration"] ?? '';
        $voter_father = $_POST["father_voter"] ?? '';
        $voter_mother = $_POST["mother_voter"] ?? '';
        $voter_applicant = $_POST["applicant_voter"] ?? '';
        $voter_guardian = $_POST["guardian_voter"] ?? '';
        $guardian_name = $_POST["guardian_name"] ?? '';
        $guardian_relationship = $_POST["guardian_relationship"] ?? '';
        $guardian_address = $_POST["guardian_address"] ?? '';
        $guardian_contact = $_POST["guardian_contact"] ?? '';

        if ($residency_data) {
            $stmt = $conn->prepare("UPDATE residency_information SET 
                    permanent_address=?, residency_duration=?, voter_father=?, voter_mother=?, 
                    voter_applicant=?, voter_guardian=?, guardian_name=?, guardian_relationship=?, 
                    guardian_address=?, guardian_contact=?
                    WHERE applicant_id=?");
            if ($stmt) {
                $stmt->bind_param("ssssssssssi", $permanent_address, $residency_duration, $voter_father, $voter_mother, $voter_applicant, 
                    $voter_guardian, $guardian_name, $guardian_relationship, $guardian_address, $guardian_contact, $applicant_id);
                $stmt->execute();
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO residency_information 
                    (applicant_id, permanent_address, residency_duration, voter_father, voter_mother, voter_applicant, voter_guardian, 
                     guardian_name, guardian_relationship, guardian_address, guardian_contact)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("issssssssss", $applicant_id, $permanent_address, $residency_duration, $voter_father, $voter_mother, $voter_applicant, 
                    $voter_guardian, $guardian_name, $guardian_relationship, $guardian_address, $guardian_contact);
                $stmt->execute();
            }
        }

        header("Location: apply.php?section=family");
        exit();
    }
    
    // Family Background Section
    if (isset($_POST['step']) && $_POST['step'] === 'family') {
        $father_name = $_POST["father_name"] ?? '';
        $father_suffix = $_POST["father_suffix"] ?? '';
        $father_address = $_POST["father_address"] ?? '';
        $father_age = $_POST["father_age"] ?? '';
        $father_contact = $_POST["father_contact"] ?? '';
        $father_citizenship = $_POST["father_citizenship"] ?? '';
        $father_occupation = $_POST["father_occupation"] ?? '';
        $father_religion = $_POST["father_religion"] ?? '';
        $father_dob = $_POST["father_dob"] ?? '';
        $father_income = $_POST["father_income"] ?? '';
        
        $mother_name = $_POST["mother_name"] ?? '';
        $mother_address = $_POST["mother_address"] ?? '';
        $mother_age = $_POST["mother_age"] ?? '';
        $mother_contact = $_POST["mother_contact"] ?? '';
        $mother_citizenship = $_POST["mother_citizenship"] ?? '';
        $mother_occupation = $_POST["mother_occupation"] ?? '';
        $mother_religion = $_POST["mother_religion"] ?? '';
        $mother_dob = $_POST["mother_dob"] ?? '';
        $mother_income = $_POST["mother_income"] ?? '';

        if ($family_data) {
            $stmt = $conn->prepare("UPDATE family_background SET 
                    father_name=?, father_suffix=?, father_address=?, father_age=?, father_contact=?,
                    father_citizenship=?, father_occupation=?, father_religion=?, father_dob=?, father_income=?,
                    mother_name=?, mother_address=?, mother_age=?, mother_contact=?, mother_citizenship=?,
                    mother_occupation=?, mother_religion=?, mother_dob=?, mother_income=?
                    WHERE applicant_id=?");
            if ($stmt) {
                $stmt->bind_param("sssssssssssssssssssi",
                    $father_name, $father_suffix, $father_address, $father_age, $father_contact,
                    $father_citizenship, $father_occupation, $father_religion, $father_dob, $father_income,
                    $mother_name, $mother_address, $mother_age, $mother_contact, $mother_citizenship,
                    $mother_occupation, $mother_religion, $mother_dob, $mother_income,
                    $applicant_id);
                $stmt->execute();
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO family_background 
                    (applicant_id, father_name, father_suffix, father_address, father_age, father_contact, father_citizenship,
                     father_occupation, father_religion, father_dob, father_income,
                     mother_name, mother_address, mother_age, mother_contact, mother_citizenship,
                     mother_occupation, mother_religion, mother_dob, mother_income)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("isssssssssssssssssss",
                    $applicant_id, $father_name, $father_suffix, $father_address, $father_age, $father_contact,
                    $father_citizenship, $father_occupation, $father_religion, $father_dob, $father_income,
                    $mother_name, $mother_address, $mother_age, $mother_contact, $mother_citizenship,
                    $mother_occupation, $mother_religion, $mother_dob, $mother_income);
                $stmt->execute();
            }
        }

        header("Location: apply.php?section=fileupload");
        exit();
    }
    
    // File Upload Section
    if (isset($_POST['step']) && $_POST['step'] === 'fileupload') {
        // Note: File handling would be done here
        // For now, just redirect to appointment
        header("Location: apply.php?section=appointment");
        exit();
    }
    
    // Appointment Section
    if (isset($_POST['step']) && $_POST['step'] === 'appointment') {
        $appointment_date = $_POST["appointment_date"];
        $appointment_time = $_POST["appointment_time"] ?? null;

        // Check if record exists
        if ($appointment_data) {
            $stmt = $conn->prepare("UPDATE appointments SET 
                    appointment_date=?, appointment_time=?
                    WHERE applicant_id=?");
            if ($stmt) {
                $stmt->bind_param("ssi", $appointment_date, $appointment_time, $applicant_id);
                $stmt->execute();
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO appointments 
                    (applicant_id, appointment_date, appointment_time)
                    VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iss", $applicant_id, $appointment_date, $appointment_time);
                $stmt->execute();
                
                // Decrement remaining slots in schedules table
                $updateSlots = $conn->prepare("UPDATE schedules SET remaining_slots = remaining_slots - 1 WHERE schedule_date = ? AND remaining_slots > 0");
                if ($updateSlots) {
                    $updateSlots->bind_param("s", $appointment_date);
                    $updateSlots->execute();
                }
            }
        }

        header("Location: apply.php?section=finish");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduGrants - Apply Scholarship</title>
    <link rel="stylesheet" href="../css/Admin_Dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/apply.css">
</head>
<body>

<?php
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header("Location: login.php");
        exit;
    }
    
    $firstname = $_SESSION['firstname'] ?? 'User';
    $lastname = $_SESSION['lastname'] ?? '';
    $email = $_SESSION['email'] ?? '';
    $role = $_SESSION['role'] ?? 'Applicant';
    $fullname = trim($firstname . ' ' . $lastname);
    ?>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../icon/graducation.png" alt="EduGrants Logo" width="30" height="30">
                <h1>EduGrants</h1>
            </div>
            
            
            <nav class="navigation">
                <a href="dashboard.php" class="nav-item">
                    <img src="../icon/dashboard.png" alt="Dashboard" width="20" height="20">
                    Dashboard
                </a>
                <a href="apply.php" class="nav-item">
                    <img src="../icon/apply.png" alt="Apply Scholarship" width="20" height="20">
                    Apply Scholarship
                </a>
                <a href="renewal.php" class="nav-item">
                    <img src="../icon/renewal.png" alt="Renewal" width="20" height="20">
                    Renewal
                </a>
                <a href="login.php" class="nav-item">
                    <img src="../icon/logout.png" alt="Log out" width="20" height="20">
                    Log out
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar" id="userAvatar"><?php echo strtoupper(substr($firstname,0,1)); ?></div>
                    <div class="user-info">
                        <div class="user-name" id="userFullname"><?php echo htmlspecialchars($fullname); ?></div>
                        <div class="user-role"><?php echo htmlspecialchars($role); ?></div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
          
            <div class="content-area apply-page">
                <h3>DASHBOARD / APPLY SCHOLARSHIP</h3>
                
                <?php /* SUCCESS MESSAGE REMOVED
                if (isset($success_message)): ?>
                    <div style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; 
                */ ?>
                
                <!-- Steps -->
                <?php
                // Define step order
                $steps_order = ['personal-info', 'residency', 'family', 'fileupload', 'appointment', 'finish'];
                $current_index = array_search($current_section, $steps_order);
                ?>
                <div class="steps">
                    <div class="step <?php echo ($current_index >= 0) ? 'active' : ''; ?>" onclick="showSection('personal-info')">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <div class="step-text">Personal Information</div>
                    </div>
                    
                    <div class="step <?php echo ($current_index >= 1) ? 'active' : ''; ?>" onclick="showSection('residency')">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24">
                                <path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                        </div>
                        <div class="step-text">Residency</div>
                    </div>
                    
                    <div class="step <?php echo ($current_index >= 2) ? 'active' : ''; ?>" onclick="showSection('family')">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24">
                                <path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                        </div>
                        <div class="step-text">Family Background</div>
                    </div>
                    
                    <div class="step <?php echo ($current_index >= 3) ? 'active' : ''; ?>" onclick="showSection('fileupload')">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24">
                                <path fill="currentColor" d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                            </svg>
                        </div>
                        <div class="step-text">File Upload</div>
                    </div>
                    
                    <div class="step <?php echo ($current_index >= 4) ? 'active' : ''; ?>" onclick="showSection('appointment')">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24">
                                <path fill="currentColor" d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>
                            </svg>
                        </div>
                        <div class="step-text">Appointment</div>
                    </div>

                    <div class="step <?php echo ($current_index >= 5) ? 'active' : ''; ?>" onclick="showSection('finish')">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24">
                                <path fill="currentColor" d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                            </svg>
                        </div>
                        <div class="step-text">Finish</div>
                    </div>
                </div> 
                <!-- Personal Information Form -->
                <div id="personal-info" class="form-section <?php echo ($current_section === 'personal-info') ? 'active' : ''; ?>" style="<?php echo ($current_section === 'personal-info') ? 'display: block !important;' : 'display: none;'; ?>">
                    <h2 class="section-title">PERSONAL INFORMATION:</h2>
                    
                    <form class="scholarship-form" method="POST" action="apply.php">
                        <input type="hidden" name="step" value="personal-info">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Lastname</label>
                                <input type="text" name="lastname" placeholder="Enter your Lastname" 
                                       value="<?php echo $personal_data ? htmlspecialchars($personal_data['lastname']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Firstname</label>
                                <input type="text" name="firstname" placeholder="Enter your Firstname" 
                                       value="<?php echo $personal_data ? htmlspecialchars($personal_data['firstname']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Middlename</label>
                                <input type="text" name="middlename" placeholder="Enter your Middlename"
                                       value="<?php echo $personal_data ? htmlspecialchars($personal_data['middlename']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Gender</label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="gender" value="male" 
                                               <?php echo ($personal_data && $personal_data['gender'] === 'male') ? 'checked' : ''; ?> required> Male
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="gender" value="female" 
                                               <?php echo ($personal_data && $personal_data['gender'] === 'female') ? 'checked' : ''; ?> required> Female
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Civil Status</label>
                                <select name="civil_status" required>
                                    <option value="">Please Select</option>
                                    <option value="single" <?php echo ($personal_data && $personal_data['civil_status'] === 'single') ? 'selected' : ''; ?>>Single</option>
                                    <option value="married" <?php echo ($personal_data && $personal_data['civil_status'] === 'married') ? 'selected' : ''; ?>>Married</option>
                                    <option value="widowed" <?php echo ($personal_data && $personal_data['civil_status'] === 'widowed') ? 'selected' : ''; ?>>Widowed</option>
                                    <option value="separated" <?php echo ($personal_data && $personal_data['civil_status'] === 'separated') ? 'selected' : ''; ?>>Separated</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" 
                                       value="<?php echo $personal_data ? $personal_data['date_of_birth'] : ''; ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Course</label>
                                <input type="text" name="course" 
                                       value="<?php echo $personal_data ? htmlspecialchars($personal_data['course']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>GPA</label>
                                <input type="text" name="gpa" 
                                       value="<?php echo $personal_data ? htmlspecialchars($personal_data['gpa']) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label>School Name</label>
                            <input type="text" name="school_name" 
                                   value="<?php echo $personal_data ? htmlspecialchars($personal_data['school_name']) : ''; ?>" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-next">Next</button>
                        </div>
                    </form>
                </div>

                <!-- Residency Section -->
                <div id="residency" class="form-section <?php echo ($current_section === 'residency') ? 'active' : ''; ?>">
                    <h2 class="section-title">RESIDENCY:</h2>
                    
                    <form class="scholarship-form" method="POST" action="apply.php">
                        <input type="hidden" name="step" value="residency">
                        
                        <div class="form-group full-width">
                            <label>Permanent Address:</label>
                            <input type="text" name="permanent_address" placeholder="Enter your permanent address" 
                                   value="<?php echo $residency_data ? htmlspecialchars($residency_data['permanent_address']) : ''; ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label>No. of Months/Years of Residency:</label>
                            <input type="text" name="residency_duration" placeholder="Enter duration" 
                                   value="<?php echo $residency_data ? htmlspecialchars($residency_data['residency_duration']) : ''; ?>" required>
                        </div>

                        <h3 class="subsection-title">Are you and your parents a Pampanga Registered Voters?</h3>

                        <div class="voter-group">
                            <label class="voter-label">Father:</label>
                            <div class="radio-group-inline">
                                <label class="radio-label">
                                    <input type="radio" name="father_voter" value="yes" 
                                           <?php echo ($residency_data && $residency_data['voter_father'] === 'yes') ? 'checked' : ''; ?> required> Yes
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="father_voter" value="no" 
                                           <?php echo ($residency_data && $residency_data['voter_father'] === 'no') ? 'checked' : ''; ?> required> No
                                </label>
                            </div>
                        </div>

                        <div class="voter-group">
                            <label class="voter-label">Mother:</label>
                            <div class="radio-group-inline">
                                <label class="radio-label">
                                    <input type="radio" name="mother_voter" value="yes" 
                                           <?php echo ($residency_data && $residency_data['voter_mother'] === 'yes') ? 'checked' : ''; ?> required> Yes
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="mother_voter" value="no" 
                                           <?php echo ($residency_data && $residency_data['voter_mother'] === 'no') ? 'checked' : ''; ?> required> No
                                </label>
                            </div>
                        </div>

                        <div class="voter-group">
                            <label class="voter-label">Applicant:</label>
                            <div class="radio-group-inline">
                                <label class="radio-label">
                                    <input type="radio" name="applicant_voter" value="yes" 
                                           <?php echo ($residency_data && $residency_data['voter_applicant'] === 'yes') ? 'checked' : ''; ?> required> Yes
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="applicant_voter" value="no" 
                                           <?php echo ($residency_data && $residency_data['voter_applicant'] === 'no') ? 'checked' : ''; ?> required> No
                                </label>
                            </div>
                        </div>

                        <div class="voter-group">
                            <label class="voter-label">Guardian:</label>
                            <div class="radio-group-inline">
                                <label class="radio-label">
                                    <input type="radio" name="guardian_voter" value="yes" 
                                           <?php echo ($residency_data && $residency_data['voter_guardian'] === 'yes') ? 'checked' : ''; ?>> Yes
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="guardian_voter" value="no" 
                                           <?php echo ($residency_data && $residency_data['voter_guardian'] === 'no') ? 'checked' : ''; ?>> No
                                </label>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label>Please Indicate the Name of guardian:</label>
                            <input type="text" name="guardian_name" placeholder="Guardian's Name" 
                                   value="<?php echo $residency_data ? htmlspecialchars($residency_data['guardian_name']) : ''; ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>RELATIONSHIP to the guardian:</label>
                            <input type="text" name="guardian_relationship" placeholder="Relationship" 
                                   value="<?php echo $residency_data ? htmlspecialchars($residency_data['guardian_relationship']) : ''; ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>ADDRESS of your guardian:</label>
                            <input type="text" name="guardian_address" placeholder="Guardian's Address" 
                                   value="<?php echo $residency_data ? htmlspecialchars($residency_data['guardian_address']) : ''; ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>CONTACT NUMBER of your guardian:</label>
                            <input type="text" name="guardian_contact" placeholder="Contact Number" 
                                   value="<?php echo $residency_data ? htmlspecialchars($residency_data['guardian_contact']) : ''; ?>">
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-prev" onclick="window.location.href='apply.php?section=personal-info'">Previous</button>
                            <button type="submit" class="btn-next">Next</button>
                        </div>
                    </form>
                </div>

                <!-- Family Background Section -->
                <div id="family" class="form-section <?php echo ($current_section === 'family') ? 'active' : ''; ?>">
                    <h2 class="section-title">FAMILY BACKGROUND:</h2>
                    
                    <form class="scholarship-form" method="POST" action="apply.php">
                        <input type="hidden" name="step" value="family">
                        
                        <h3 class="subsection-title">FATHER'S INFORMATION</h3>
                        
                        <div class="family-row">
                            <div class="family-col">
                                <label>Father's Name:</label>
                                <input type="text" name="father_name" placeholder="Enter father's name" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['father_name']) : ''; ?>">
                            </div>
                            <div class="family-col-small">
                                <label>Suffix:</label>
                                <input type="text" name="father_suffix" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['father_suffix']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="family-row">
                            <div class="family-col">
                                <label>Home Address:</label>
                                <input type="text" name="father_address" placeholder="Enter home address" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['father_address']) : ''; ?>">
                            </div>
                            <div class="family-col-small">
                                <label>Age:</label>
                                <input type="text" name="father_age" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['father_age']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="family-row">
                            <div class="family-col">
                                <label>Contact No.:</label>
                                <input type="text" name="father_contact" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['father_contact']) : ''; ?>">
                            </div>
                            <div class="family-col">
                                <label>Citizenship:</label>
                                <input type="text" name="father_citizenship" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['father_citizenship']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="family-row">
                            <div class="family-col">
                                <label>Present Occupation:</label>
                                <input type="text" name="father_occupation" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['father_occupation']) : ''; ?>">
                            </div>
                            <div class="family-col">
                                <label>Religion:</label>
                                <input type="text" name="father_religion" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['father_religion']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="family-row">
                            <div class="family-col">
                                <label>Date of Birth:</label>
                                <input type="date" name="father_dob" 
                                       value="<?php echo $family_data ? $family_data['father_dob'] : ''; ?>">
                            </div>
                            <div class="family-col">
                                <label>Monthly Income:</label>
                                <input type="text" name="father_income" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['father_income']) : ''; ?>">
                            </div>
                        </div>
                        
                        <hr style="margin:30px 0; border:1px solid #e0e0e0;">
                        
                        <h3 class="subsection-title">MOTHER'S INFORMATION</h3>
                        
                        <div class="family-row">
                            <div class="family-col-full">
                                <label>Mother's Name:</label>
                                <input type="text" name="mother_name" placeholder="Enter mother's name" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['mother_name']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="family-row">
                            <div class="family-col">
                                <label>Home Address:</label>
                                <input type="text" name="mother_address" placeholder="Enter home address" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['mother_address']) : ''; ?>">
                            </div>
                            <div class="family-col-small">
                                <label>Age:</label>
                                <input type="text" name="mother_age" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['mother_age']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="family-row">
                            <div class="family-col">
                                <label>Contact No.:</label>
                                <input type="text" name="mother_contact" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['mother_contact']) : ''; ?>">
                            </div>
                            <div class="family-col">
                                <label>Citizenship:</label>
                                <input type="text" name="mother_citizenship" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['mother_citizenship']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="family-row">
                            <div class="family-col">
                                <label>Present Occupation:</label>
                                <input type="text" name="mother_occupation" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['mother_occupation']) : ''; ?>">
                            </div>
                            <div class="family-col">
                                <label>Religion:</label>
                                <input type="text" name="mother_religion" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['mother_religion']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="family-row">
                            <div class="family-col">
                                <label>Date of Birth:</label>
                                <input type="date" name="mother_dob" 
                                       value="<?php echo $family_data ? $family_data['mother_dob'] : ''; ?>">
                            </div>
                            <div class="family-col">
                                <label>Monthly Income:</label>
                                <input type="text" name="mother_income" placeholder="" 
                                       value="<?php echo $family_data ? htmlspecialchars($family_data['mother_income']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-prev" onclick="window.location.href='apply.php?section=residency'">Previous</button>
                            <button type="submit" class="btn-next">Next</button>
                        </div>
                    </form>
                </div>

                <!-- File Upload Section -->
                <div id="fileupload" class="form-section <?php echo ($current_section === 'fileupload') ? 'active' : ''; ?>">
                    <h2 class="section-title">Requirements Upload:</h2>
                    
                    <form class="scholarship-form" method="POST" action="apply.php" enctype="multipart/form-data">
                        <input type="hidden" name="step" value="fileupload">
                        
                        <!-- Report Card Upload -->
                        <div class="upload-item">
                            <div class="upload-header">
                                <input type="checkbox" id="report_card_check" name="report_card_check">
                                <label for="report_card_check" class="upload-label">Photo copy of report card (Form 138)</label>
                            </div>
                            <div class="file-upload-wrapper">
                                <label for="report_card" class="file-upload-btn">Choose File</label>
                                <input type="file" id="report_card" name="report_card" accept=".pdf,.jpg,.jpeg,.png">
                                <span class="file-name" id="report_card_name">No file chosen</span>
                            </div>
                        </div>
                        
                        <!-- Barangay Clearance Upload -->
                        <div class="upload-item">
                            <div class="upload-header">
                                <input type="checkbox" id="barangay_clearance_check" name="barangay_clearance_check">
                                <label for="barangay_clearance_check" class="upload-label">Barangay clearance</label>
                            </div>
                            <div class="file-upload-wrapper">
                                <label for="barangay_clearance" class="file-upload-btn">Choose File</label>
                                <input type="file" id="barangay_clearance" name="barangay_clearance" accept=".pdf,.jpg,.jpeg,.png">
                                <span class="file-name" id="barangay_clearance_name">No file chosen</span>
                            </div>
                        </div>
                        
                        <!-- Valid ID Upload -->
                        <div class="upload-item">
                            <div class="upload-header">
                                <input type="checkbox" id="valid_id_check" name="valid_id_check">
                                <label for="valid_id_check" class="upload-label">Upload any types of a valid ID</label>
                            </div>
                            <p class="upload-description">Photocopy of voter's ID, National ID</p>
                            <div class="file-upload-wrapper">
                                <label for="valid_id" class="file-upload-btn">Choose File</label>
                                <input type="file" id="valid_id" name="valid_id" accept=".pdf,.jpg,.jpeg,.png">
                                <span class="file-name" id="valid_id_name">No file chosen</span>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-prev" onclick="window.location.href='apply.php?section=family'">Previous</button>
                            <button type="submit" class="btn-next">Next</button>
                        </div>
                    </form>
                </div>

                <!-- Appointment Section -->
                <div id="appointment" class="form-section <?php echo ($current_section === 'appointment') ? 'active' : ''; ?>">
                    <h2 class="section-title">Set Appointment:</h2>
                    
                    <div class="appointment-subtitle">Schedule of Submission of Requirements</div>
                    
                    <form class="scholarship-form" method="POST" action="apply.php">
                        <input type="hidden" name="step" value="appointment">
                        
                        <div class="appointment-row">
                            <div class="appointment-col">
                                <label>Please select a date</label>
                                <select name="appointment_date" id="appointmentDate" required onchange="updateSlotInfo()">
                                    <option value="">Available Dates</option>
                                    <?php
                                    // Fetch available schedules from database
                                    $schedules = [];
                                    $stmt = $conn->prepare("SELECT schedule_id, schedule_date, total_slots, remaining_slots FROM schedules WHERE is_active = 1 ORDER BY schedule_date ASC");
                                    if ($stmt) {
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($row = $result->fetch_assoc()) {
                                            $schedules[] = $row;
                                        }
                                    }
                                    
                                    if (count($schedules) === 0) {
                                        echo "<option value=''>No schedules available</option>";
                                    } else {
                                        foreach ($schedules as $schedule) {
                                            $date = new DateTime($schedule['schedule_date']);
                                            $formatted_date = $date->format('F j, Y');
                                            $selected = ($appointment_data && $appointment_data['appointment_date'] === $schedule['schedule_date']) ? 'selected' : '';
                                            echo "<option value='{$schedule['schedule_date']}' data-total='{$schedule['total_slots']}' data-remaining='{$schedule['remaining_slots']}' {$selected}>{$formatted_date}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="appointment-col">
                                <label>Total Slots:</label>
                                <input type="text" id="totalSlots" readonly 
                                       value="<?php echo ($appointment_data && isset($schedules)) ? ($schedules[0]['total_slots'] ?? '0') : '0'; ?>">
                            </div>
                            
                            <div class="appointment-col">
                                <label>Remaining Slots:</label>
                                <input type="text" id="remainingSlots" readonly 
                                       value="<?php echo ($appointment_data && isset($schedules)) ? ($schedules[0]['remaining_slots'] ?? '0') : '0'; ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-prev" onclick="window.location.href='apply.php?section=fileupload'">Previous</button>
                            <button type="submit" class="btn-finish">Finish</button>
                        </div>
                    </form>
                </div>

                <!-- Finish Section -->
                <div id="finish" class="form-section <?php echo ($current_section === 'finish') ? 'active' : ''; ?>">

                    <!-- SUCCESS MESSAGE (hidden by default) -->
                    <div id="successMessage" style="display:none; text-align:center; padding:60px 20px;">

                        <!-- Green Check Icon -->
                        <div style="width:100%; display:flex; justify-content:center; margin-bottom:25px;">
                            <div style="
                                width:110px; 
                                height:110px; 
                                background:#4CAF50; 
                                border-radius:50%; 
                                display:flex; 
                                align-items:center; 
                                justify-content:center;
                            ">
                                <span style="font-size:60px; color:white;">✔</span>
                            </div>
                        </div>

                        <h2 style="font-size:40px; margin-bottom:10px; color:#333; font-weight:bold;">SUCCESS!</h2>

                        <p style="font-size:18px; color:#666;">
                            Wait further announcement for your applications to be approved. Thank you!
                        </p>
                    </div>

                    <!-- NORMAL FINISH CONTENT -->
                    <div id="finishContent">
                        <h2 class="section-title">FINISH:</h2>
                        <p>Review and submit your application...</p>

                        <div class="form-actions">
                            <button type="button" class="btn-prev" onclick="showSection('appointment')">Previous</button>

                            <!-- Submit button → posts to backend then triggers success -->
                            <button type="button" class="btn-submit" onclick="submitApplication()">Submit Application</button>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all sections
            const sections = document.querySelectorAll('.form-section');
            sections.forEach(section => {
                section.classList.remove('active');
            });

            // Remove active class from all steps
            const steps = document.querySelectorAll('.step');
            steps.forEach(step => {
                step.classList.remove('active');
            });

            // Show selected section
            document.getElementById(sectionId).classList.add('active');

            // Step mapping
            const stepMap = {
                'personal-info': 0,
                'residency': 1,
                'family': 2,
                'fileupload': 3,
                'appointment': 4,
                'finish': 5
            };

            const stepIndex = stepMap[sectionId];
            if (stepIndex !== undefined) {
                steps[stepIndex].classList.add('active');
            }

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ========== SHOW SUCCESS SCREEN ==========
        function showSuccess() {
            document.getElementById('finishContent').style.display = 'none';  
            document.getElementById('successMessage').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ========== UPDATE SLOT INFO ON DATE CHANGE ==========
        function updateSlotInfo() {
            const selectElement = document.getElementById('appointmentDate');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            
            const totalSlots = selectedOption.getAttribute('data-total') || '0';
            const remainingSlots = selectedOption.getAttribute('data-remaining') || '0';
            
            document.getElementById('totalSlots').value = totalSlots;
            document.getElementById('remainingSlots').value = remainingSlots;
        }

        // ========== SUBMIT APPLICATION (mark pending) ==========
        function submitApplication() {
            fetch('../backend/submit_application.php', { method: 'POST', credentials: 'same-origin' })
                .then(res => res.json())
                .then(data => {
                    if (data && data.status === 'success') {
                        // show success screen after backend confirms
                        showSuccess();
                    } else {
                        alert('Failed to submit application: ' + (data.error || data.message || 'Unknown'));
                    }
                })
                .catch(err => {
                    alert('Request failed');
                });
        }

        // File upload display filename
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize slot info on page load
            updateSlotInfo();
            
            const fileInputs = document.querySelectorAll('input[type="file"]');
            
            fileInputs.forEach(input => {
                input.addEventListener('change', function(e) {
                    const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
                    const fileNameSpan = document.getElementById(e.target.id + '_name');
                    if (fileNameSpan) {
                        fileNameSpan.textContent = fileName;
                    }
                });
            });
        });
    </script>  
</main>
</body>
</html>
<script>
// Update slot info when a schedule is selected
function updateSlotInfo(){
    var sel = document.getElementById('appointmentDate');
    if(!sel) return;
    var opt = sel.options[sel.selectedIndex];
    if(!opt) return;
    var total = opt.getAttribute('data-total') || '0';
    var remaining = opt.getAttribute('data-remaining') || '0';
    var totalEl = document.getElementById('totalSlots');
    var remEl = document.getElementById('remainingSlots');
    if(totalEl) totalEl.value = total;
    if(remEl) remEl.value = remaining;
}

document.addEventListener('DOMContentLoaded', function(){
    // initialize fields from currently selected option
    updateSlotInfo();
    // ensure onchange is wired (in case HTML changed)
    var sel = document.getElementById('appointmentDate');
    if(sel) sel.addEventListener('change', updateSlotInfo);
});
</script>