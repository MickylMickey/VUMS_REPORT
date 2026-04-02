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

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `cat_id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `cat_desc` text NOT NULL,
  `cat_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `cat_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'BUG','Signifies a bug in the system','2026-03-27 05:47:55','2026-03-27 05:47:55'),(2,'RBAC','Signifies a permission error','2026-03-27 05:47:55','2026-03-27 05:47:55'),(3,'UXUI','Signifies a problem in the UI and the UX','2026-03-27 05:47:55','2026-03-27 05:47:55'),(4,'VALID','Signifies a Validation or Business rule error','2026-03-27 05:47:55','2026-03-27 05:47:55'),(5,'GAP','Signifies a Process and Workflow gap','2026-03-27 05:47:55','2026-03-27 05:47:55'),(6,'DATA','Signifies a Data or Master data issue','2026-03-27 05:47:55','2026-03-27 05:47:55'),(7,'PERF','Signifies a Performance Issue','2026-03-27 05:47:55','2026-03-27 05:47:55'),(8,'RPT','Signifies a Report, Printing and Exporting error','2026-03-27 05:47:55','2026-03-27 05:47:55'),(9,'INTG','Signifies an Integration error','2026-03-27 05:47:55','2026-03-27 05:47:55'),(10,'SEC','Signifies a Security and Access issue','2026-03-27 05:47:55','2026-03-27 05:47:55');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `module` (
  `mod_id` int NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `mod_desc` text NOT NULL,
  `mod_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mod_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module`
--

LOCK TABLES `module` WRITE;
/*!40000 ALTER TABLE `module` DISABLE KEYS */;
INSERT INTO `module` VALUES (1,'DASH','Dashboard Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(2,'HR','HR Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(3,'PAY','Payroll Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(4,'PROC','Procurement Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(5,'PR','Purchase Requisition Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(6,'PO','Purchase Order Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(7,'AP','Accounts Payable Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(8,'AR','Accounts Receivable Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(9,'FIN','Finance Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(10,'INV','Inventory Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(11,'PIM','Product Information Management Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(12,'PPL','Product Price List Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(13,'SALES','Sales Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(14,'SO','Sales Order Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(15,'JO','Job Order Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(16,'QUO','Quotation Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(17,'PROJ','Project Management Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(18,'SOW','Scope of Work Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(19,'BOM','Bills of Materials Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(20,'BQM','Bills of Quantities Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(21,'PMF','Pull out of Materials Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(22,'SMF','Service Materials Form Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(23,'WAE','Work Accomplishment Entry Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(24,'DPR','Daily Progress Report Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(25,'PORTAL','Client Portal Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(26,'DOC','Document Control Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(27,'ASSET','Assets Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(28,'TOOLS','Tools and Equipment Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(29,'MOC','Main Operating Cash Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(30,'DOCASH','Department Operating Cash Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(31,'GOV','Government Payables Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(32,'BILL','Billing Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(33,'LOGIN','Authentication and Login Module','2026-03-27 05:47:55','2026-03-27 05:47:55');
/*!40000 ALTER TABLE `module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `user_id` char(36) NOT NULL,
  `cat_id` int NOT NULL,
  `mod_id` int NOT NULL,
  `sev_id` int NOT NULL,
  `status_id` int NOT NULL DEFAULT '1',
  `ref_num` varchar(36) NOT NULL,
  `report_img` varchar(255) DEFAULT NULL,
  `report_desc` text NOT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `report_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `report_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`),
  KEY `report_ibfk_1` (`user_id`),
  KEY `report_ibfk_2` (`cat_id`),
  KEY `report_ibfk_3` (`mod_id`),
  KEY `report_ibfk_4` (`sev_id`),
  KEY `report_ibfk_5` (`status_id`),
  KEY `report_ibfk_6` (`updated_by`),
  CONSTRAINT `report_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `report_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `category` (`cat_id`) ON DELETE CASCADE,
  CONSTRAINT `report_ibfk_3` FOREIGN KEY (`mod_id`) REFERENCES `module` (`mod_id`) ON DELETE CASCADE,
  CONSTRAINT `report_ibfk_4` FOREIGN KEY (`sev_id`) REFERENCES `severity` (`sev_id`) ON DELETE CASCADE,
  CONSTRAINT `report_ibfk_5` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE CASCADE,
  CONSTRAINT `report_ibfk_6` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report`
--

LOCK TABLES `report` WRITE;
/*!40000 ALTER TABLE `report` DISABLE KEYS */;
INSERT INTO `report` VALUES (9,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,1,2,2,'rbac-dash-h-001',NULL,'wala','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-01 08:28:10','2026-04-01 08:28:12');
/*!40000 ALTER TABLE `report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_archive`
--

DROP TABLE IF EXISTS `report_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_archive` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `user_id` char(36) NOT NULL,
  `cat_id` int NOT NULL,
  `mod_id` int NOT NULL,
  `sev_id` int NOT NULL,
  `status_id` int NOT NULL DEFAULT '1',
  `ref_num` varchar(36) NOT NULL,
  `report_img` varchar(255) DEFAULT NULL,
  `report_desc` text NOT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `report_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `report_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`),
  KEY `report_ibfk_1` (`user_id`),
  KEY `report_ibfk_2` (`cat_id`),
  KEY `report_ibfk_3` (`mod_id`),
  KEY `report_ibfk_4` (`sev_id`),
  KEY `report_ibfk_5` (`status_id`),
  KEY `report_ibfk_6` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_archive`
--

LOCK TABLES `report_archive` WRITE;
/*!40000 ALTER TABLE `report_archive` DISABLE KEYS */;
INSERT INTO `report_archive` VALUES (6,'ecf74437-2721-11f1-80cd-fc068c03d3f8',2,1,1,3,'rbac-dash-c-001',NULL,'wala lang',NULL,'2026-04-01 01:58:18','2026-04-01 08:13:23'),(7,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,3,2,3,'rbac-pay-h-001',NULL,'wala',NULL,'2026-04-01 02:07:33','2026-04-01 08:17:14'),(8,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,3,1,3,'rbac-pay-c-002','1775014211_d47d3471b8.png','nothuing happen','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-01 03:30:11','2026-04-01 08:28:00');
/*!40000 ALTER TABLE `report_archive` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `severity`
--

DROP TABLE IF EXISTS `severity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `severity` (
  `sev_id` int NOT NULL AUTO_INCREMENT,
  `severity` varchar(50) NOT NULL,
  `sev_desc` text NOT NULL,
  `sev_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sev_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sev_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `severity`
--

LOCK TABLES `severity` WRITE;
/*!40000 ALTER TABLE `severity` DISABLE KEYS */;
INSERT INTO `severity` VALUES (1,'C','Critical','2026-03-27 05:47:55','2026-03-27 05:47:55'),(2,'H','High','2026-03-27 05:47:55','2026-03-27 05:47:55'),(3,'M','Medium','2026-03-27 05:47:55','2026-03-27 05:47:55'),(4,'L','Low','2026-03-27 05:47:55','2026-03-27 05:47:55');
/*!40000 ALTER TABLE `severity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status` (
  `status_id` int NOT NULL AUTO_INCREMENT,
  `status_desc` varchar(50) NOT NULL,
  `status_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES (1,'Pending','2026-03-27 05:47:55','2026-03-27 05:47:55'),(2,'In-Progress','2026-03-27 05:47:55','2026-03-27 05:47:55'),(3,'Completed','2026-03-27 05:47:55','2026-03-27 05:47:55'),(4,'Cancelled','2026-03-27 05:47:55','2026-03-27 05:47:55');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suggestion_archive`
--

DROP TABLE IF EXISTS `suggestion_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suggestion_archive` (
  `suggestion_id` int NOT NULL AUTO_INCREMENT,
  `user_id` char(36) NOT NULL,
  `suggestion_desc` text NOT NULL,
  `status_id` int NOT NULL DEFAULT '1',
  `suggestion_img` varchar(255) DEFAULT NULL,
  `suggestion_updated_by` char(36) DEFAULT NULL,
  `suggestion_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `suggestion_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`suggestion_id`),
  KEY `user_suggestion_ibfk_1` (`user_id`),
  KEY `user_suggestion_ibfk_2` (`status_id`),
  KEY `user_suggestion_ibfk_3` (`suggestion_updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suggestion_archive`
--

LOCK TABLES `suggestion_archive` WRITE;
/*!40000 ALTER TABLE `suggestion_archive` DISABLE KEYS */;
INSERT INTO `suggestion_archive` VALUES (1,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','Maligo',3,NULL,NULL,'2026-03-27 05:48:05','2026-04-01 08:08:19'),(4,'ecf74437-2721-11f1-80cd-fc068c03d3f8','Mag lagay na ng Design CSS',3,'sug_1775028693_5b212358.jpg',NULL,'2026-04-01 07:31:34','2026-04-01 08:17:45'),(5,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','test',4,NULL,NULL,'2026-04-01 07:33:06','2026-04-01 08:18:05');
/*!40000 ALTER TABLE `suggestion_archive` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_profile` (
  `user_id` char(36) NOT NULL,
  `user_first_name` varchar(50) NOT NULL,
  `user_middle_name` varchar(50) DEFAULT NULL,
  `user_last_name` varchar(50) NOT NULL,
  `user_dob` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_prof` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  CONSTRAINT `user_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profile`
--

LOCK TABLES `user_profile` WRITE;
/*!40000 ALTER TABLE `user_profile` DISABLE KEYS */;
INSERT INTO `user_profile` VALUES ('184251a9-9554-4ef8-bf0b-d03b06d33dd9','Vincent','Binas','Pasion','2001-03-31','vincent@gmail.com','69cc8fbf9f65d.jpg'),('1d50ae09-9604-4419-b11f-495e72312b18','aaron','pusod','flores','2026-03-17','aaron@gmail.com','69c63ff0edaa3.png'),('61b23cef-9381-4d9c-bd0e-7db0b71b3580','Jonalyn','Jormigos','Parreno','2001-07-25','email@gmail.com',NULL),('cbf12398-7d7e-4e03-9f1d-21d73999c2ad','Nathaniel','Middle','Lincuran','2026-03-02','naths@gmail.com','69c63c80bb3ee.png'),('ecf740ae-2721-11f1-80cd-fc068c03d3f8','Mickyl','Gaytana','Sumagang','2003-06-09','test@gmail.com',NULL),('ecf74437-2721-11f1-80cd-fc068c03d3f8','Niel','Magsumbol','Apaitan','2003-10-21','test1@gmail.com',NULL);
/*!40000 ALTER TABLE `user_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_role` (
  `user_role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `role_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (1,'Admin','2026-03-27 05:47:54','2026-03-27 05:47:54'),(2,'User','2026-03-27 05:47:54','2026-03-27 05:47:54');
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_status`
--

DROP TABLE IF EXISTS `user_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_status` (
  `user_status_id` int NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL,
  `status_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_status`
--

LOCK TABLES `user_status` WRITE;
/*!40000 ALTER TABLE `user_status` DISABLE KEYS */;
INSERT INTO `user_status` VALUES (1,'Active','2026-03-27 05:47:55','2026-03-27 05:47:55'),(2,'Inactive','2026-03-27 05:47:55','2026-03-27 05:47:55');
/*!40000 ALTER TABLE `user_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_suggestions`
--

DROP TABLE IF EXISTS `user_suggestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_suggestions` (
  `suggestion_id` int NOT NULL AUTO_INCREMENT,
  `user_id` char(36) NOT NULL,
  `suggestion_desc` text NOT NULL,
  `status_id` int NOT NULL DEFAULT '1',
  `suggestion_img` varchar(255) DEFAULT NULL,
  `suggestion_updated_by` char(36) DEFAULT NULL,
  `suggestion_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `suggestion_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`suggestion_id`),
  KEY `user_suggestion_ibfk_1` (`user_id`),
  KEY `user_suggestion_ibfk_2` (`status_id`),
  KEY `user_suggestion_ibfk_3` (`suggestion_updated_by`),
  CONSTRAINT `user_suggestion_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `user_suggestion_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE CASCADE,
  CONSTRAINT `user_suggestion_ibfk_3` FOREIGN KEY (`suggestion_updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_suggestions`
--

LOCK TABLES `user_suggestions` WRITE;
/*!40000 ALTER TABLE `user_suggestions` DISABLE KEYS */;
INSERT INTO `user_suggestions` VALUES (6,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','mag jogging 3x a day',2,NULL,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-01 08:22:50','2026-04-01 08:28:36');
/*!40000 ALTER TABLE `user_suggestions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` char(36) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_role_id` int NOT NULL,
  `user_status_id` int NOT NULL DEFAULT '1',
  `user_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `user_ibfk_1` (`user_role_id`),
  KEY `user_ibfk_2` (`user_status_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `user_role` (`user_role_id`) ON DELETE CASCADE,
  CONSTRAINT `user_ibfk_2` FOREIGN KEY (`user_status_id`) REFERENCES `user_status` (`user_status_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('184251a9-9554-4ef8-bf0b-d03b06d33dd9','vincent','$2y$12$1gypdgmTq4mhMbMtdQyUA.neCdiDTXaqBseHyAFbQWuJFPNEstNoa',1,1,'2026-04-01 03:23:12','2026-04-01 03:23:12'),('1d50ae09-9604-4419-b11f-495e72312b18','aaron','$2y$12$s06Ma2o8tIhY8bPudVKADePa5FWFprp94zTj47fwmid.aHeLi1.y.',2,1,'2026-03-27 08:29:36','2026-03-27 08:29:36'),('61b23cef-9381-4d9c-bd0e-7db0b71b3580','jonalyn','$2y$12$c3FFRvfrGPVjD87v9XzbO.IOqj5EF9W8LrZWzG75omSo1RQsC/2ru',1,1,'2026-03-27 08:07:59','2026-03-27 08:07:59'),('cbf12398-7d7e-4e03-9f1d-21d73999c2ad','Naths','$2y$12$Rh3Hxq6VeJLsx5fCKa.jgOyu1/huP50v2mcEQtmHbcN0.fklgw0IS',2,1,'2026-03-27 08:14:56','2026-03-27 08:40:08'),('ecf740ae-2721-11f1-80cd-fc068c03d3f8','admin','$2y$12$9ws8..jQHFKZDdP/v6m6POZE1HGvOiNdQJpjYWNdQAaL7PMk/23om',1,1,'2026-03-27 05:47:55','2026-03-27 05:47:55'),('ecf74437-2721-11f1-80cd-fc068c03d3f8','niel','$2y$12$bLYvFjsMo.YIAL7SrGcTMeZUeIOAX4TTQ1aNx5c6OF1JQXjyDCKCu',2,1,'2026-03-27 05:47:55','2026-03-27 05:47:55');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-01 17:25:01
