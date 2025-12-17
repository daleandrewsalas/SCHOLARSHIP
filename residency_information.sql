/*
SQLyog Ultimate - MySQL GUI v8.2 
MySQL - 5.5.5-10.4.28-MariaDB 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `residency_information` (
	`id` double ,
	`applicant_id` double ,
	`permanent_address` varchar (765),
	`residency_duration` varchar (300),
	`voter_father` varchar (30),
	`voter_mother` varchar (30),
	`voter_applicant` varchar (30),
	`voter_guardian` varchar (30),
	`guardian_name` varchar (300),
	`guardian_relationship` varchar (150),
	`guardian_address` varchar (765),
	`guardian_contact` varchar (60),
	`created_at` timestamp ,
	`updated_at` timestamp 
); 
insert into `residency_information` (`id`, `applicant_id`, `permanent_address`, `residency_duration`, `voter_father`, `voter_mother`, `voter_applicant`, `voter_guardian`, `guardian_name`, `guardian_relationship`, `guardian_address`, `guardian_contact`, `created_at`, `updated_at`) values('3','6','adsas','dasdasd','yes','yes','yes','yes','adssa','sad','ad','1234567891011','2025-12-13 03:16:56','2025-12-13 03:16:56');
