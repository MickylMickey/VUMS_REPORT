-- ==========================================================
-- 1. DATABASE INITIALIZATION
-- ==========================================================

CREATE DATABASE  IF NOT EXISTS `vums_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `vums_db`;
-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: vums_db
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

SET FOREIGN_KEY_CHECKS = 0;

-- ==========================================================
-- 2. INDEPENDENT TABLES (Lookup Tables)
-- ==========================================================



DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
    `user_role_id` int NOT NULL AUTO_INCREMENT,
    `role_name` varchar(50) NOT NULL,
    `role_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `role_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `user_role` (`user_role_id`, `role_name`)
VALUES
(1, 'Admin'),
(2, 'User');



DROP TABLE IF EXISTS `user_status`;
CREATE TABLE `user_status` (
    `user_status_id` int NOT NULL AUTO_INCREMENT,
    `status_name` varchar(50) NOT NULL,
    `status_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `status_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO `user_status` (`user_status_id`, `status_name`) 
VALUES 
(1, 'Active'), 
(2, 'Inactive');


DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
    `status_id` int NOT NULL AUTO_INCREMENT,
    `status_desc` varchar(50) NOT NULL,
    `status_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `status_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO `status` (`status_id`, `status_desc`) 
VALUES 
(1, 'Pending'), 
(2, 'In-Progress'), 
(3, 'Completed'), 
(4, 'Cancelled');


DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
    `cat_id` int NOT NULL AUTO_INCREMENT,
    `category` VARCHAR (50) NOT NULL,
    `cat_desc` TEXT NOT NULL,
    `cat_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `cat_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO `category` (`cat_id`, `category`, `cat_desc`) VALUES 
(1, 'BUG', 'Signifies a bug in the system'),
(2, 'RBAC', 'Signifies a permission error'),
(3, 'UXUI', 'Signifies a problem in the UI and the UX'),
(4, 'VALID', 'Signifies a Validation or Business rule error'),
(5, 'GAP', 'Signifies a Process and Workflow gap'),
(6, 'DATA', 'Signifies a Data or Master data issue'),
(7, 'PERF', 'Signifies a Performance Issue'),
(8, 'RPT', 'Signifies a Report, Printing and Exporting error'),
(9, 'INTG', 'Signifies an Integration error'),
(10,'SEC', 'Signifies a Security and Access issue');



DROP TABLE IF EXISTS `module`;
CREATE TABLE `module` (
    `mod_id` int NOT NULL AUTO_INCREMENT,
    `module` VARCHAR(50) NOT NULL,
    `mod_desc` TEXT NOT NULL,
    `mod_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `mod_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`mod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO `module` (`mod_id`, `module`, `mod_desc`) VALUES 
(1, 'DASH', 'Dashboard Module'),
(2, 'HR', 'HR Module'),
(3, 'PAY', 'Payroll Module'),
(4, 'PROC', 'Procurement Module'),
(5, 'PR', 'Purchase Requisition Module'),
(6, 'PO', 'Purchase Order Module'),
(7, 'AP', 'Accounts Payable Module'),
(8, 'AR', 'Accounts Receivable Module'),
(9, 'FIN', 'Finance Module'),
(10, 'INV', 'Inventory Module'),
(11, 'PIM', 'Product Information Management Module'),
(12, 'PPL', 'Product Price List Module'),
(13, 'SALES', 'Sales Module'),
(14, 'SO', 'Sales Order Module'),
(15, 'JO', 'Job Order Module'),
(16, 'QUO', 'Quotation Module'),
(17, 'PROJ', 'Project Management Module'),
(18, 'SOW', 'Scope of Work Module'),
(19, 'BOM', 'Bills of Materials Module'),
(20, 'BQM', 'Bills of Quantities Module'),
(21, 'PMF', 'Pull out of Materials Module'),
(22, 'SMF', 'Service Materials Form Module'),
(23, 'WAE', 'Work Accomplishment Entry Module'),
(24, 'DPR', 'Daily Progress Report Module'),
(25, 'PORTAL', 'Client Portal Module'),
(26, 'DOC', 'Document Control Module'),
(27, 'ASSET', 'Assets Module'),
(28, 'TOOLS', 'Tools and Equipment Module'),
(29, 'MOC', 'Main Operating Cash Module'),
(30, 'DOCASH', 'Department Operating Cash Module'),
(31, 'GOV', 'Government Payables Module'),
(32, 'BILL', 'Billing Module'),
(33, 'LOGIN', 'Authentication and Login Module');


DROP TABLE IF EXISTS `severity`;
CREATE TABLE `severity` (
    `sev_id` int NOT NULL AUTO_INCREMENT,
    `severity` VARCHAR(50) NOT NULL,
    `sev_desc` TEXT NOT NULL,
    `sev_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `sev_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sev_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO `severity` (`sev_id`, `severity`, `sev_desc`) VALUES
(1, 'C', 'Critical'),
(2, 'H', 'High'),
(3, 'M', 'Medium'),
(4, 'L', 'Low');


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `user_id` CHAR(36) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `user_role_id` INT NOT NULL,
    `user_status_id` INT NOT NULL DEFAULT 1,
    `user_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `user_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `username` (`username`),
    CONSTRAINT `user_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `user_role`(`user_role_id`) ON DELETE CASCADE,
    CONSTRAINT `user_ibfk_2` FOREIGN KEY (`user_status_id`) REFERENCES `user_status`(`user_status_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users`(`user_id`, `username`, `password`, `user_role_id`, `user_status_id`)
VALUES
('ecf740ae-2721-11f1-80cd-fc068c03d3f8', 'admin', '$2y$12$9ws8..jQHFKZDdP/v6m6POZE1HGvOiNdQJpjYWNdQAaL7PMk/23om','1','1'),
('ecf74437-2721-11f1-80cd-fc068c03d3f8', 'niel', '$2y$12$bLYvFjsMo.YIAL7SrGcTMeZUeIOAX4TTQ1aNx5c6OF1JQXjyDCKCu','2','1');



DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE `user_profile` (
    `user_id` CHAR(36) NOT NULL,
    `user_first_name` VARCHAR(50) NOT NULL,
    `user_middle_name` VARCHAR(50) DEFAULT NULL,
    `user_last_name` VARCHAR(50) NOT NULL,
    `user_dob` DATE NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `user_prof` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `email` (`email`),
    CONSTRAINT `user_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `user_profile` (`user_id`, `user_first_name`, `user_middle_name`, `user_last_name`, `user_dob`,`email`)
VALUES
('ecf740ae-2721-11f1-80cd-fc068c03d3f8', 'Mickyl','Gaytana','Sumagang','2003-06-09','test@gmail.com'),
('ecf74437-2721-11f1-80cd-fc068c03d3f8', 'Niel','Magsumbol','Apaitan','2003-10-21','test1@gmail.com');


DROP TABLE IF EXISTS `report`;
CREATE TABLE `report` (
    `report_id` INT NOT NULL AUTO_INCREMENT,
    `user_id` CHAR(36) NOT NULL,
    `cat_id` INT NOT NULL,
    `mod_id` INT NOT NULL,
    `sev_id` INT NOT NULL,
    `status_id` INT NOT NULL,
    `ref_num` INT NOT NULL,
    `report_img` VARCHAR(255) DEFAULT NULL,
    `report_desc` TEXT NOT NULL,
    `report_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `report_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`report_id`),
    CONSTRAINT `report_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
    CONSTRAINT `report_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `category`(`cat_id`) ON DELETE CASCADE,
    CONSTRAINT `report_ibfk_3` FOREIGN KEY (`mod_id`) REFERENCES `module`(`mod_id`) ON DELETE CASCADE,
    CONSTRAINT `report_ibfk_4` FOREIGN KEY (`sev_id`) REFERENCES `severity`(`sev_id`) ON DELETE CASCADE,
    CONSTRAINT `report_ibfk_5` FOREIGN KEY (`status_id`) REFERENCES `status`(`status_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `user_suggestions`;
CREATE TABLE `user_suggestions` (
    `suggestion_id` INT NOT NULL,
    `user_id` CHAR(36) NOT NULL,
    `suggestion_desc` TEXT NOT NULL,
    `status_id` INT NOT NULL,
    `suggestion_img` VARCHAR(255) DEFAULT NULL,
    `suggestion_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `suggestion_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`suggestion_id`),
    CONSTRAINT `user_suggestion_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
    CONSTRAINT `user_suggestion_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `status`(`status_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


SET FOREIGN_KEY_CHECKS = 1;