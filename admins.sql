/*
SQLyog Ultimate - MySQL GUI v8.2 
MySQL - 5.5.5-10.4.28-MariaDB 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `admins` (
	`id` double ,
	`username` varchar (300),
	`password` varchar (765),
	`fullname` varchar (765),
	`email` varchar (300),
	`role` varchar (150)
); 
insert into `admins` (`id`, `username`, `password`, `fullname`, `email`, `role`) values('1','superadmin','12345','Test',NULL,'Super Administrator');
insert into `admins` (`id`, `username`, `password`, `fullname`, `email`, `role`) values('2','test','$2y$10$YaSTo2IJ5z6AUIyJwJ.Ln.yBkO/ylFFqPRSeEQDTapKyy0y4eCX/O','sample','test4@example.com','Administrator');
