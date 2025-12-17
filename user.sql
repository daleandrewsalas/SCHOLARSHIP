/*
SQLyog Ultimate - MySQL GUI v8.2 
MySQL - 5.5.5-10.4.28-MariaDB 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `users` (
	`user_id` double ,
	`first_name` varchar (150),
	`last_name` varchar (150),
	`email` varchar (300),
	`password_hash` varchar (765),
	`applicant_id` double ,
	`created_at` timestamp 
); 
insert into `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `applicant_id`, `created_at`) values('4','Dale','Salas','sample@example.com','dale123',NULL,'2025-12-11 21:01:21');
insert into `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `applicant_id`, `created_at`) values('5','Dale','Salas','dale@gmail.com','$2y$10$r9hXg64uHOW1LmAHKOSzJuTVdquLFoyB5W81l07Lzwyw8ntaqqmN2',NULL,'2025-12-13 03:15:41');
