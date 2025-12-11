<?php
// Database setup script to create all necessary tables
require_once __DIR__ . '/config/database.php';

try {
    // Drop existing tables with foreign keys first (in reverse order of dependencies)
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    
    // Drop tables if they exist
    $pdo->exec("DROP TABLE IF EXISTS residency_information");
    $pdo->exec("DROP TABLE IF EXISTS family_background");
    $pdo->exec("DROP TABLE IF EXISTS appointments");
    $pdo->exec("DROP TABLE IF EXISTS schedules");
    $pdo->exec("DROP TABLE IF EXISTS personal_information");
    $pdo->exec("DROP TABLE IF EXISTS applicants");
    $pdo->exec("DROP TABLE IF EXISTS registration");
    $pdo->exec("DROP TABLE IF EXISTS system_accounts");
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    // Create schedules table first (for appointment dates)
    $pdo->exec("CREATE TABLE IF NOT EXISTS schedules (
        schedule_id INT PRIMARY KEY AUTO_INCREMENT,
        schedule_date DATE NOT NULL,
        total_slots INT DEFAULT 10,
        remaining_slots INT DEFAULT 10,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Create applicants table (parent table)
    $pdo->exec("CREATE TABLE IF NOT EXISTS applicants (
        applicant_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Create personal_information table
    $pdo->exec("CREATE TABLE IF NOT EXISTS personal_information (
        id INT PRIMARY KEY AUTO_INCREMENT,
        applicant_id INT,
        lastname VARCHAR(100) NOT NULL,
        firstname VARCHAR(100) NOT NULL,
        middlename VARCHAR(100),
        gender VARCHAR(20),
        civil_status VARCHAR(50),
        date_of_birth DATE,
        course VARCHAR(100),
        gpa DECIMAL(3,2),
        school_name VARCHAR(150),
        skills TEXT,
        talent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE
    )");
    
    // Create residency_information table
    $pdo->exec("CREATE TABLE IF NOT EXISTS residency_information (
        id INT PRIMARY KEY AUTO_INCREMENT,
        applicant_id INT,
        permanent_address VARCHAR(255),
        residency_duration VARCHAR(100),
        voter_father VARCHAR(10),
        voter_mother VARCHAR(10),
        voter_applicant VARCHAR(10),
        voter_guardian VARCHAR(10),
        guardian_name VARCHAR(100),
        guardian_relationship VARCHAR(50),
        guardian_address VARCHAR(255),
        guardian_contact VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE
    )");
    
    // Create family_background table
    $pdo->exec("CREATE TABLE IF NOT EXISTS family_background (
        id INT PRIMARY KEY AUTO_INCREMENT,
        applicant_id INT,
        father_name VARCHAR(100),
        father_suffix VARCHAR(20),
        father_address VARCHAR(255),
        father_age INT,
        father_contact VARCHAR(20),
        father_citizenship VARCHAR(50),
        father_occupation VARCHAR(100),
        father_religion VARCHAR(50),
        father_dob DATE,
        father_income VARCHAR(50),
        mother_name VARCHAR(100),
        mother_address VARCHAR(255),
        mother_age INT,
        mother_contact VARCHAR(20),
        mother_citizenship VARCHAR(50),
        mother_occupation VARCHAR(100),
        mother_religion VARCHAR(50),
        mother_dob DATE,
        mother_income VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE
    )");
    
    // Create appointments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        applicant_id INT,
        appointment_date DATE,
        appointment_time TIME,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE
    )");
    
    // Create registration table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS registration (
        user_id INT PRIMARY KEY AUTO_INCREMENT,
        firstname VARCHAR(100) NOT NULL,
        lastname VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create system_accounts table for approved applicants
    $pdo->exec("CREATE TABLE IF NOT EXISTS system_accounts (
        account_id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(100) UNIQUE NOT NULL,
        firstname VARCHAR(100) NOT NULL,
        lastname VARCHAR(100) NOT NULL,
        middlename VARCHAR(100),
        gender VARCHAR(20),
        birthdate DATE,
        email VARCHAR(100) UNIQUE NOT NULL,
        picture VARCHAR(255) DEFAULT 'default_profile.jpg',
        account_type VARCHAR(50) DEFAULT 'student',
        status VARCHAR(50) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    echo "✅ Database setup completed successfully!<br>";
    // Create users table for registered students
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INT PRIMARY KEY AUTO_INCREMENT,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        applicant_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Create admins table for admin users
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(100) UNIQUE NOT NULL,
        fullname VARCHAR(100),
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        email VARCHAR(100),
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'Administrator',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Create approved_credentials table for tracking approvals
    $pdo->exec("CREATE TABLE IF NOT EXISTS approved_credentials (
        id INT PRIMARY KEY AUTO_INCREMENT,
        applicant_id INT,
        user_id INT,
        temporary_password VARCHAR(255),
        approval_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "✅ Database setup completed successfully!<br>";
    echo "All tables have been created with proper relationships.<br>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

