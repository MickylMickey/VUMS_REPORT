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



DROP TABLE IF EXISTS `user_status`;
CREATE TABLE `user_status` (
    `user_status_id` int NOT NULL AUTO_INCREMENT,
    `status_name` varchar(50) NOT NULL,
    `status_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `status_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
    `status_id` int NOT NULL AUTO_INCREMENT,
    `status_desc` varchar(50) NOT NULL,
    `status_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `status_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
    `cat_id` int NOT NULL AUTO_INCREMENT,
    `cat_desc` TEXT NOT NULL,
    `cat_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `cat_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



DROP TABLE IF EXISTS `module`;
CREATE TABLE `module` (
    `mod_id` int NOT NULL AUTO_INCREMENT,
    `mod_desc` TEXT NOT NULL,
    `mod_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `mod_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`mod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



DROP TABLE IF EXISTS `severity`;
CREATE TABLE `severity` (
    `sev_id` int NOT NULL AUTO_INCREMENT,
    `sev_desc` TEXT NOT NULL,
    `sev_created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `sev_updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sev_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
    `user_id` CHAR(36) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `user_role_id` INT NOT NULL,
    `user_status_id` INT NOT NULL,
    `user_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `user_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `username` (`username`),
    CONSTRAINT `user_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `user_role`(`user_role_id`) ON DELETE CASCADE,
    CONSTRAINT `user_ibfk_2` FOREIGN KEY (`user_status_id`) REFERENCES `user_status`(`user_status_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


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
    CONSTRAINT `user_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


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