/*
SQLyog Ultimate - MySQL GUI v8.2 
MySQL - 5.5.5-10.4.28-MariaDB 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `personal_information` (
	`id` double ,
	`applicant_id` double ,
	`lastname` varchar (300),
	`firstname` varchar (300),
	`middlename` varchar (300),
	`gender` varchar (60),
	`civil_status` varchar (150),
	`date_of_birth` date ,
	`course` varchar (300),
	`gpa` Decimal (5),
	`school_name` varchar (450),
	`skills` blob ,
	`talent` blob ,
	`created_at` timestamp ,
	`updated_at` timestamp 
); 
insert into `personal_information` (`id`, `applicant_id`, `lastname`, `firstname`, `middlename`, `gender`, `civil_status`, `date_of_birth`, `course`, `gpa`, `school_name`, `skills`, `talent`, `created_at`, `updated_at`) values('1','2','Dela Cruz','Juan','Santos','male',NULL,'2000-05-15',NULL,NULL,NULL,NULL,NULL,'2025-12-10 09:00:09','2025-12-10 09:00:09');
insert into `personal_information` (`id`, `applicant_id`, `lastname`, `firstname`, `middlename`, `gender`, `civil_status`, `date_of_birth`, `course`, `gpa`, `school_name`, `skills`, `talent`, `created_at`, `updated_at`) values('2','1','dadsad','Dale ','','female','single','2005-12-20','asdas','9.99','0','','','2025-12-10 09:53:32','2025-12-10 09:58:33');
