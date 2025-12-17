/*
SQLyog Ultimate - MySQL GUI v8.2 
MySQL - 5.5.5-10.4.28-MariaDB : Database - edugrants
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`edugrants` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `edugrants`;

/*Table structure for table `admins` */

DROP TABLE IF EXISTS `admins`;

CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `admins` */

insert  into `admins`(`id`,`username`,`password`,`fullname`,`email`,`role`) values (1,'superadmin','$2y$10$NcMrARcbtzaofMl89Bj8G.UNP06Z8Es3i1nPvQeubATgnXA7189eC','Test',NULL,'Super Administrator'),(2,'test','$2y$10$YaSTo2IJ5z6AUIyJwJ.Ln.yBkO/ylFFqPRSeEQDTapKyy0y4eCX/O','sample','test4@example.com','Administrator');

/*Table structure for table `applicants` */

DROP TABLE IF EXISTS `applicants`;

CREATE TABLE `applicants` (
  `applicant_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`applicant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `applicants` */

insert  into `applicants`(`applicant_id`,`user_id`,`status`,`created_at`,`updated_at`) values (6,5,'approved','2025-12-13 03:16:02','2025-12-13 03:19:02'),(7,5,'approved','2025-12-14 15:20:40','2025-12-14 15:34:08');

/*Table structure for table `appointments` */

DROP TABLE IF EXISTS `appointments`;

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `applicant_id` (`applicant_id`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `appointments` */

insert  into `appointments`(`id`,`applicant_id`,`appointment_date`,`appointment_time`,`notes`,`created_at`,`updated_at`) values (4,6,'2025-12-08',NULL,NULL,'2025-12-13 03:18:03','2025-12-13 03:18:03');

/*Table structure for table `approved_credentials` */

DROP TABLE IF EXISTS `approved_credentials`;

CREATE TABLE `approved_credentials` (
  `applicant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `temporary_password` varchar(255) NOT NULL,
  `approval_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`applicant_id`),
  UNIQUE KEY `user_id_unique` (`user_id`),
  CONSTRAINT `approved_credentials_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `approved_credentials_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `registration` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `approved_credentials` */

/*Table structure for table `family_background` */

DROP TABLE IF EXISTS `family_background`;

CREATE TABLE `family_background` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `father_suffix` varchar(20) DEFAULT NULL,
  `father_address` varchar(255) DEFAULT NULL,
  `father_age` int(11) DEFAULT NULL,
  `father_contact` varchar(20) DEFAULT NULL,
  `father_citizenship` varchar(50) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `father_religion` varchar(50) DEFAULT NULL,
  `father_dob` date DEFAULT NULL,
  `father_income` varchar(50) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `mother_address` varchar(255) DEFAULT NULL,
  `mother_age` int(11) DEFAULT NULL,
  `mother_contact` varchar(20) DEFAULT NULL,
  `mother_citizenship` varchar(50) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `mother_religion` varchar(50) DEFAULT NULL,
  `mother_dob` date DEFAULT NULL,
  `mother_income` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `applicant_id` (`applicant_id`),
  CONSTRAINT `family_background_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `family_background` */

insert  into `family_background`(`id`,`applicant_id`,`father_name`,`father_suffix`,`father_address`,`father_age`,`father_contact`,`father_citizenship`,`father_occupation`,`father_religion`,`father_dob`,`father_income`,`mother_name`,`mother_address`,`mother_age`,`mother_contact`,`mother_citizenship`,`mother_occupation`,`mother_religion`,`mother_dob`,`mother_income`,`created_at`,`updated_at`) values (3,6,'asda','','dasda',0,'12321312','asdasd','asdasd','sadasd','2007-02-27','1231321','asdsa','dsad',0,'12312312','ssdasda','asdsad','sadas','2005-02-09','123213','2025-12-13 03:17:52','2025-12-13 03:17:52');

/*Table structure for table `personal_information` */

DROP TABLE IF EXISTS `personal_information`;

CREATE TABLE `personal_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `school_name` varchar(150) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `talent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `applicant_id` (`applicant_id`),
  CONSTRAINT `personal_information_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `personal_information` */

insert  into `personal_information`(`id`,`applicant_id`,`lastname`,`firstname`,`middlename`,`gender`,`civil_status`,`date_of_birth`,`course`,`gpa`,`school_name`,`skills`,`talent`,`created_at`,`updated_at`) values (4,6,'dadsad','Dale ','asd','male','single','2001-01-08','IT','9.99','asdasd','','','2025-12-13 03:16:29','2025-12-14 16:19:04');

/*Table structure for table `registration` */

DROP TABLE IF EXISTS `registration`;

CREATE TABLE `registration` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `registration` */

insert  into `registration`(`user_id`,`firstname`,`lastname`,`email`,`password_hash`,`created_at`) values (5,'Dale ','dadsad','test@example.com','$2y$10$aLNUt9BY/eD0DzFr.AC1BuAErJoAl7ICQblLu6e.4CwyRBk9TWvk6','2025-12-13 03:19:02'),(6,'Dale ','dadsad','test9@example.com','$2y$10$KNy7ijnwZjjUgg5wbIt/Ce5G2abJ/IQPiXobqrsrnCXjLHawnDhy.','2025-12-13 03:19:51'),(8,'Dale ','dadsad','test@.com','$2y$10$jSmfwhM5IbyKDc66UxhtK.KEVYymjs/I/60P2Ko3KEIeBn9M9l0Nu','2025-12-13 03:21:01'),(9,'Dale ','dadsad','dale@gmail.com','$2y$10$8cdIJvxTlvqOxYFr7sJr4O3CumB19ow9I1/FHyRFUoUPd0PUSRnwW','2025-12-13 03:21:26');

/*Table structure for table `renewal_applications` */

DROP TABLE IF EXISTS `renewal_applications`;

CREATE TABLE `renewal_applications` (
  `renewal_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `applicant_id` int(11) NOT NULL COMMENT 'Foreign Key linking to Applicants',
  `school_year` varchar(50) NOT NULL COMMENT 'e.g., 2025 - 2026 / 1st Semester',
  `gpa` decimal(4,2) NOT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`renewal_id`),
  KEY `fk_renewal_applicant` (`applicant_id`),
  CONSTRAINT `fk_renewal_applicant_revised` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores academic and status data for each scholarship renewal attempt.';

/*Data for the table `renewal_applications` */

/*Table structure for table `renewal_requests` */

DROP TABLE IF EXISTS `renewal_requests`;

CREATE TABLE `renewal_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `school_year` varchar(100) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `gpa` decimal(5,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(300) DEFAULT NULL,
  `firstname` varchar(300) DEFAULT NULL,
  `lastname` varchar(300) DEFAULT NULL,
  `school` varchar(300) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `renewal_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `renewal_requests` */

insert  into `renewal_requests`(`id`,`user_id`,`school_year`,`school_name`,`gpa`,`status`,`created_at`,`email`,`firstname`,`lastname`,`school`,`updated_at`) values (1,5,'2025-2026 / 2nd Semester',NULL,'9.99','approved','2025-12-17 09:35:27','dale@gmail.com','Dale','Salas','City College','2025-12-17 09:40:17');

/*Table structure for table `renewal_requirements` */

DROP TABLE IF EXISTS `renewal_requirements`;

CREATE TABLE `renewal_requirements` (
  `requirement_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `renewal_id` int(11) NOT NULL COMMENT 'Foreign Key linking to the Renewal Application',
  `document_type` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`requirement_id`),
  UNIQUE KEY `uk_renewal_doc` (`renewal_id`,`document_type`),
  KEY `fk_req_renewal` (`renewal_id`),
  CONSTRAINT `fk_req_renewal` FOREIGN KEY (`renewal_id`) REFERENCES `renewal_applications` (`renewal_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores details and file paths for uploaded renewal requirements.';

/*Data for the table `renewal_requirements` */

/*Table structure for table `residency_information` */

DROP TABLE IF EXISTS `residency_information`;

CREATE TABLE `residency_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) DEFAULT NULL,
  `permanent_address` varchar(255) DEFAULT NULL,
  `residency_duration` varchar(100) DEFAULT NULL,
  `voter_father` varchar(10) DEFAULT NULL,
  `voter_mother` varchar(10) DEFAULT NULL,
  `voter_applicant` varchar(10) DEFAULT NULL,
  `voter_guardian` varchar(10) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_relationship` varchar(50) DEFAULT NULL,
  `guardian_address` varchar(255) DEFAULT NULL,
  `guardian_contact` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `applicant_id` (`applicant_id`),
  CONSTRAINT `residency_information_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `residency_information` */

insert  into `residency_information`(`id`,`applicant_id`,`permanent_address`,`residency_duration`,`voter_father`,`voter_mother`,`voter_applicant`,`voter_guardian`,`guardian_name`,`guardian_relationship`,`guardian_address`,`guardian_contact`,`created_at`,`updated_at`) values (3,6,'San Jose','dasdasd','yes','yes','yes','yes','adssa','sad','ad','1234567891011','2025-12-13 03:16:56','2025-12-14 16:18:47');

/*Table structure for table `schedules` */

DROP TABLE IF EXISTS `schedules`;

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_date` date NOT NULL,
  `total_slots` int(11) DEFAULT 10,
  `remaining_slots` int(11) DEFAULT 10,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`schedule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `schedules` */

insert  into `schedules`(`schedule_id`,`schedule_date`,`total_slots`,`remaining_slots`,`is_active`,`created_at`,`updated_at`) values (1,'2025-12-20',20,20,1,'2025-12-10 10:00:31','2025-12-10 10:00:31'),(2,'2025-12-08',10,8,1,'2025-12-10 10:04:59','2025-12-13 03:18:03'),(3,'2025-12-31',10,10,1,'2025-12-17 11:43:33','2025-12-17 11:43:33');

/*Table structure for table `system_accounts` */

DROP TABLE IF EXISTS `system_accounts`;

CREATE TABLE `system_accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `picture` varchar(255) DEFAULT 'default_profile.jpg',
  `account_type` varchar(50) DEFAULT 'student',
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `system_accounts` */

insert  into `system_accounts`(`account_id`,`username`,`firstname`,`lastname`,`middlename`,`gender`,`birthdate`,`email`,`picture`,`account_type`,`status`,`created_at`,`updated_at`) values (7,'test6','Dale ','dadsad','asd','male','2001-01-08','test@example.com','default_profile.jpg','student','active','2025-12-13 03:19:02','2025-12-13 03:20:05'),(14,'dale_7','','','','',NULL,'dale@gmail.com','default_profile.jpg','student','active','2025-12-14 15:34:08','2025-12-14 15:34:08');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL COMMENT 'Stores the securely hashed password',
  `applicant_id` int(11) DEFAULT NULL COMMENT 'Links to the applicant record',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `applicant_id` (`applicant_id`),
  CONSTRAINT `fk_user_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores user registration and authentication data.';

/*Data for the table `users` */

insert  into `users`(`user_id`,`first_name`,`last_name`,`email`,`password_hash`,`applicant_id`,`created_at`) values (4,'Dale','Salas','sample@example.com','$2y$10$5t3qgdz1G45kYBvv/dviIONn3Jni5LSToOcBVPXhG/iqO7E4ZI4Bu',NULL,'2025-12-11 21:01:21'),(5,'Dale','Salas','dale@gmail.com','$2y$10$Li1DTAGNvj/8w7S2bQuQ3.nB.yBgSCNJkdmLcE5Yv3cJbO8rQ0ZTu',NULL,'2025-12-13 03:15:41');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
