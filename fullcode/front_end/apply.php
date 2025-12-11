
<?php
// Include DB Connection
include("database.php");

$success_message = "";
$current_section = isset($_GET['section']) ? $_GET['section'] : "personal-info";

// Retrieve existing data
$personal_data = null;
$residency_data = null;
$family_data = null;
$appointment_data = null;

try {
    // Get latest personal information
    $stmt = $pdo->query("SELECT * FROM personal_information ORDER BY id DESC LIMIT 1");
    $personal_data = $stmt->fetch();
    
    // Get latest residency information
    $stmt = $pdo->query("SELECT * FROM residency_information ORDER BY id DESC LIMIT 1");
    $residency_data = $stmt->fetch();
    
    // Get latest family background
    $stmt = $pdo->query("SELECT * FROM family_background ORDER BY id DESC LIMIT 1");
    $family_data = $stmt->fetch();
    
    // Get latest appointment
    $stmt = $pdo->query("SELECT * FROM appointments ORDER BY id DESC LIMIT 1");
    $appointment_data = $stmt->fetch();
} catch(PDOException $e) {
    // Tables might not exist yet
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Personal Information Section
    if (!isset($_POST['step'])) {
        $lastname = $_POST["lastname"];
        $firstname = $_POST["firstname"];
        $middlename = $_POST["middlename"];
        $gender = $_POST["gender"];
        $civil_status = $_POST["civil_status"];
        $date_of_birth = $_POST["date_of_birth"];
        $course = $_POST["course"];
        $gpa = $_POST["gpa"];
        $school_name = $_POST["school_name"];
        $skills = $_POST["skills"];
        $talent = $_POST["talent"];

        // Check if record exists
        if ($personal_data) {
            // Update existing record
            $sql = "UPDATE personal_information SET 
                    lastname=?, firstname=?, middlename=?, gender=?, civil_status=?, 
                    date_of_birth=?, course=?, gpa=?, school_name=?, skills=?, talent=?
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $lastname, $firstname, $middlename, $gender, $civil_status,
                $date_of_birth, $course, $gpa, $school_name, $skills, $talent, $personal_data['id']
            ]);
        } else {
            // Insert new record
            $sql = "INSERT INTO personal_information 
                    (lastname, firstname, middlename, gender, civil_status, date_of_birth, course, gpa, school_name, skills, talent)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $lastname, $firstname, $middlename, $gender, $civil_status,
                $date_of_birth, $course, $gpa, $school_name, $skills, $talent
            ]);
        }

        if ($result) {
            // $success_message = "Personal Information Saved Successfully!";
            header("Location: apply.php?section=residency");
            exit();
        } else {
            // $success_message = "Error saving data.";
        }
    }
    
    // Residency Section
    if (isset($_POST['step']) && $_POST['step'] === 'residency') {
        $permanent_address = $_POST["permanent_address"];
        $residency_duration = $_POST["residency_duration"];
        $voter_father = $_POST["father_voter"];
        $voter_mother = $_POST["mother_voter"];
        $voter_applicant = $_POST["applicant_voter"];
        $voter_guardian = isset($_POST["guardian_voter"]) ? $_POST["guardian_voter"] : '';
        $guardian_name = $_POST["guardian_name"];
        $guardian_relationship = $_POST["guardian_relationship"];
        $guardian_address = $_POST["guardian_address"];
        $guardian_contact = $_POST["guardian_contact"];

        // Check if record exists
        if ($residency_data) {
            // Update existing record
            $sql = "UPDATE residency_information SET 
                    permanent_address=?, residency_duration=?, voter_father=?, voter_mother=?, 
                    voter_applicant=?, voter_guardian=?, guardian_name=?, guardian_relationship=?, 
                    guardian_address=?, guardian_contact=?
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $permanent_address, $residency_duration, $voter_father, $voter_mother, $voter_applicant, 
                $voter_guardian, $guardian_name, $guardian_relationship, $guardian_address, $guardian_contact,
                $residency_data['id']
            ]);
        } else {
            // Insert new record
            $sql = "INSERT INTO residency_information 
                    (permanent_address, residency_duration, voter_father, voter_mother, voter_applicant, voter_guardian, 
                     guardian_name, guardian_relationship, guardian_address, guardian_contact)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $permanent_address, $residency_duration, $voter_father, $voter_mother, $voter_applicant, 
                $voter_guardian, $guardian_name, $guardian_relationship, $guardian_address, $guardian_contact
            ]);
        }

        if ($result) {
            // $success_message = "Residency Information Saved Successfully!";
            header("Location: apply.php?section=family");
            exit();
        } else {
            // $success_message = "Error saving residency data.";
        }
    }
    
    // Family Background Section
    if (isset($_POST['step']) && $_POST['step'] === 'family') {
        $father_name = $_POST["father_name"];
        $father_suffix = $_POST["father_suffix"];
        $father_address = $_POST["father_address"];
        $father_age = $_POST["father_age"];
        $father_contact = $_POST["father_contact"];
        $father_citizenship = $_POST["father_citizenship"];
        $father_occupation = $_POST["father_occupation"];
        $father_religion = $_POST["father_religion"];
        $father_dob = $_POST["father_dob"];
        $father_income = $_POST["father_income"];
        
        $mother_name = $_POST["mother_name"];
        $mother_address = $_POST["mother_address"];
        $mother_age = $_POST["mother_age"];
        $mother_contact = $_POST["mother_contact"];
        $mother_citizenship = $_POST["mother_citizenship"];
        $mother_occupation = $_POST["mother_occupation"];
        $mother_religion = $_POST["mother_religion"];
        $mother_dob = $_POST["mother_dob"];
        $mother_income = $_POST["mother_income"];

        // Check if record exists
        if ($family_data) {
            // Update existing record
            $sql = "UPDATE family_background SET 
                    father_name=?, father_suffix=?, father_address=?, father_age=?, father_contact=?,
                    father_citizenship=?, father_occupation=?, father_religion=?, father_dob=?, father_income=?,
                    mother_name=?, mother_address=?, mother_age=?, mother_contact=?, mother_citizenship=?,
                    mother_occupation=?, mother_religion=?, mother_dob=?, mother_income=?
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $father_name, $father_suffix, $father_address, $father_age, $father_contact,
                $father_citizenship, $father_occupation, $father_religion, $father_dob, $father_income,
                $mother_name, $mother_address, $mother_age, $mother_contact, $mother_citizenship,
                $mother_occupation, $mother_religion, $mother_dob, $mother_income,
                $family_data['id']
            ]);
        } else {
            // Insert new record
            $sql = "INSERT INTO family_background 
                    (father_name, father_suffix, father_address, father_age, father_contact, father_citizenship,
                     father_occupation, father_religion, father_dob, father_income,
                     mother_name, mother_address, mother_age, mother_contact, mother_citizenship,
                     mother_occupation, mother_religion, mother_dob, mother_income)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $father_name, $father_suffix, $father_address, $father_age, $father_contact,
                $father_citizenship, $father_occupation, $father_religion, $father_dob, $father_income,
                $mother_name, $mother_address, $mother_age, $mother_contact, $mother_citizenship,
                $mother_occupation, $mother_religion, $mother_dob, $mother_income
            ]);
        }

        if ($result) {
            // $success_message = "Family Background Saved Successfully!";
            header("Location: apply.php?section=fileupload");
            exit();
        } else {
            // $success_message = "Error saving family background data.";
        }
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
        $total_slots = $_POST["total_slots"];
        $remaining_slots = $_POST["remaining_slots"];

        // Check if record exists
        if ($appointment_data) {
            // Update existing record
            $sql = "UPDATE appointments SET 
                    appointment_date=?, total_slots=?, remaining_slots=?
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $appointment_date, $total_slots, $remaining_slots,
                $appointment_data['id']
            ]);
        } else {
            // Insert new record
            $sql = "INSERT INTO appointments 
                    (appointment_date, total_slots, remaining_slots)
                    VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $appointment_date, $total_slots, $remaining_slots
            ]);
        }

        if ($result) {
            // $success_message = "Appointment Scheduled Successfully!";
            header("Location: apply.php?section=finish");
            exit();
        } else {
            // $success_message = "Error saving appointment data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduGrants - Apply Scholarship</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="apply.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="images/graducation.png" alt="EduGrants Logo" width="30" height="30">
                <h1>EduGrants</h1>
            </div>
            
            <div class="user-profile">
                <div class="avatar">
                    <img src="images/user.png" alt="User Avatar">
                </div>
                <h3>John Cruz Galang</h3>
                <span class="verified-badge">Unverified Account</span>
            </div>
            
            <nav class="navigation">
                <a href="dashboard.php" class="nav-item">
                    <img src="images/dashboard.png" alt="Dashboard" width="20" height="20">
                    Dashboard
                </a>
                <a href="apply.php" class="nav-item active">
                    <img src="images/apply.png" alt="Apply Scholarship" width="20" height="20">
                    Apply Scholarship
                </a>
                <a href="renewal.php" class="nav-item">
                    <img src="images/renewal.png" alt="Renewal" width="20" height="20">
                    Renewal
                </a>
                <a href="logout.php" class="nav-item">
                    <img src="images/logout.png" alt="Log out" width="20" height="20">
                    Log out
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <div class="user-info">
                    <img src="images/notification.png" alt="Notification" width="20" height="20">
                    <span>John Galang</span>
                </div>
            </header>

            <div class="content-area">
                <h3>DASHBOARD / APPLY SCHOLARSHIP</h3>
                
                <?php /* SUCCESS MESSAGE REMOVED
                if (isset($success_message)): ?>
                    <div style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; 
                */ ?>
                
                <!-- Steps -->
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
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <div class="step-text">Personal Information</div>
                    </div>
                    
                    <div class="step <?php echo ($current_index >= 1) ? 'active' : ''; ?>" onclick="showSection('residency')">
                        
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                    </div>
                    <div class="step-text">Residency</div>
                </div>
                
                <div class="step <?php echo ($current_index >= 2) ? 'active' : ''; ?>" onclick="showSection('family')">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    
                    <div class="step-text">Family Background</div>
                </div>
                
                <div class="step <?php echo ($current_index >= 3) ? 'active' : ''; ?>" onclick="showSection('fileupload')">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                        </svg>
                    </div>
                    
                    <div class="step-text">File Upload</div>
                </div>
                
                <div class="step <?php echo ($current_index >= 4) ? 'active' : ''; ?>" onclick="showSection('appointment')">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>
                        </svg>
                    </div>
                    <div class="step-text">Appointment</div>
                </div>

                <div class="step <?php echo ($current_index >= 5) ? 'active' : ''; ?>" onclick="showSection('finish')">

                <div class="step-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                    </svg>

                </div>
                
                <div class="step-text">Finish</div>
            </div>
        </div> 
                <!-- Personal Information Form -->
                <div id="personal-info" class="form-section <?php echo ($current_section === 'personal-info') ? 'active' : ''; ?>">
                    <h2 class="section-title">PERSONAL INFORMATION:</h2>
                    
                    <form class="scholarship-form" method="POST" action="apply.php">
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

                        <div class="form-row">
                            <div class="form-group">
                                <label>Skills (optional)</label>
                                <input type="text" name="skills" 
                                       value="<?php echo $personal_data ? htmlspecialchars($personal_data['skills']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Talent (optional)</label>
                                <input type="text" name="talent" 
                                       value="<?php echo $personal_data ? htmlspecialchars($personal_data['talent']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-next">Submit</button>
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
                                <select name="appointment_date" required>
                                    <option value="">Available Dates</option>
                                    <option value="2024-12-15" <?php echo ($appointment_data && $appointment_data['appointment_date'] === '2024-12-15') ? 'selected' : ''; ?>>December 15, 2024</option>
                                    <option value="2024-12-20" <?php echo ($appointment_data && $appointment_data['appointment_date'] === '2024-12-20') ? 'selected' : ''; ?>>December 20, 2024</option>
                                    <option value="2024-12-25" <?php echo ($appointment_data && $appointment_data['appointment_date'] === '2024-12-25') ? 'selected' : ''; ?>>December 25, 2024</option>
                                </select>
                            </div>
                            
                            <div class="appointment-col">
                                <label>Total Slots:</label>
                                <input type="text" name="total_slots" readonly 
                                       value="<?php echo $appointment_data ? htmlspecialchars($appointment_data['total_slots']) : '0'; ?>">
                            </div>
                            
                            <div class="appointment-col">
                                <label>Remaining Slots:</label>
                                <input type="text" name="remaining_slots" readonly 
                                       value="<?php echo $appointment_data ? htmlspecialchars($appointment_data['remaining_slots']) : '0'; ?>">
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

                            <!-- Submit button → triggers success -->
                            <button type="button" class="btn-submit" onclick="showSuccess()">Submit Application</button>
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

        // File upload display filename
        document.addEventListener('DOMContentLoaded', function() {
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

</body>
</html>