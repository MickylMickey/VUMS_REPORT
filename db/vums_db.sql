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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'BUG','Signifies a bug in the system','2026-03-27 05:47:55','2026-03-27 05:47:55'),(2,'RBAC','Signifies a permission error','2026-03-27 05:47:55','2026-03-27 05:47:55'),(3,'UXUI','Signifies a problem in the UI and the UX','2026-03-27 05:47:55','2026-03-27 05:47:55'),(4,'VALID','Signifies a Validation or Business rule error','2026-03-27 05:47:55','2026-03-27 05:47:55'),(5,'GAP','Signifies a Process and Workflow gap','2026-03-27 05:47:55','2026-03-27 05:47:55'),(6,'DATA','Signifies a Data or Master data issue','2026-03-27 05:47:55','2026-03-27 05:47:55'),(7,'PERF','Signifies a Performance Issue','2026-03-27 05:47:55','2026-03-27 05:47:55'),(8,'RPT','Signifies a Report, Printing and Exporting error','2026-03-27 05:47:55','2026-03-27 05:47:55'),(9,'INTG','Signifies an Integration error','2026-03-27 05:47:55','2026-03-27 05:47:55'),(10,'SEC','Signifies a Security and Access issue','2026-03-27 05:47:55','2026-04-06 08:24:12'),(11,'LOGOUT','Authentication and Logout Module','2026-04-06 07:30:47','2026-04-06 08:24:37'),(12,'Test','Test 101','2026-04-07 06:48:09','2026-04-07 06:48:09'),(13,'TESTING 101111','sdafsdfasdf','2026-04-10 08:24:37','2026-04-10 08:24:37');
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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module`
--

LOCK TABLES `module` WRITE;
/*!40000 ALTER TABLE `module` DISABLE KEYS */;
INSERT INTO `module` VALUES (1,'DASH','Dashboard Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(2,'HR','HR Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(3,'PAY','Payroll Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(4,'PROC','Procurement Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(5,'PR','Purchase Requisition Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(6,'PO','Purchase Order Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(7,'AP','Accounts Payable Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(8,'AR','Accounts Receivable Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(9,'FIN','Finance Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(10,'INV','Inventory Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(11,'PIM','Product Information Management Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(12,'PPL','Product Price List Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(13,'SALES','Sales Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(14,'SO','Sales Order Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(15,'JO','Job Order Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(16,'QUO','Quotation Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(17,'PROJ','Project Management Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(18,'SOW','Scope of Work Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(19,'BOM','Bills of Materials Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(20,'BQM','Bills of Quantities Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(21,'PMF','Pull out of Materials Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(22,'SMF','Service Materials Form Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(23,'WAE','Work Accomplishment Entry Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(24,'DPR','Daily Progress Report Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(25,'PORTAL','Client Portal Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(26,'DOC','Document Control Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(27,'ASSET','Assets Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(28,'TOOLS','Tools and Equipment Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(29,'MOC','Main Operating Cash Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(30,'DOCASH','Department Operating Cash Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(31,'GOV','Government Payables Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(32,'BILL','Billing Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(33,'LOGIN','Authentication and Login Module','2026-03-27 05:47:55','2026-03-27 05:47:55'),(34,'Logout','Authentication and Logout Module','2026-04-06 07:33:49','2026-04-06 08:30:40'),(35,'test','test 102','2026-04-07 06:48:18','2026-04-07 06:48:18');
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
  `cat_id` int DEFAULT NULL,
  `mod_id` int DEFAULT NULL,
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
  CONSTRAINT `report_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `category` (`cat_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `report_ibfk_3` FOREIGN KEY (`mod_id`) REFERENCES `module` (`mod_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `report_ibfk_4` FOREIGN KEY (`sev_id`) REFERENCES `severity` (`sev_id`) ON DELETE CASCADE,
  CONSTRAINT `report_ibfk_5` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE CASCADE,
  CONSTRAINT `report_ibfk_6` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report`
--

LOCK TABLES `report` WRITE;
/*!40000 ALTER TABLE `report` DISABLE KEYS */;
INSERT INTO `report` VALUES (47,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',NULL,NULL,1,1,'xxx-xxx-c-001',NULL,'TEST 101',NULL,'2026-04-10 01:13:37','2026-04-10 01:13:37'),(48,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,1,1,1,'bug-dash-c-004',NULL,'ewan',NULL,'2026-04-10 01:40:33','2026-04-10 01:40:33'),(49,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,1,4,1,'bug-dash-l-005',NULL,'Wala',NULL,'2026-04-10 02:24:18','2026-04-10 02:24:18'),(55,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,3,4,1,'rbac-pay-l-003','1775808331_6689094ecc.png','fdsafdsa','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 08:05:31','2026-04-10 08:12:11'),(56,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,5,1,1,'bug-pr-c-001',NULL,'Passion',NULL,'2026-04-10 08:22:00','2026-04-10 08:22:00'),(57,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad',1,2,1,1,'bug-hr-c-003','1775810055_949838ea2a.png','wessdfsdf',NULL,'2026-04-10 08:34:15','2026-04-10 08:34:15'),(58,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad',2,1,4,1,'rbac-dash-l-001',NULL,'dfssdfasdf',NULL,'2026-04-10 08:34:28','2026-04-10 08:34:28'),(59,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,2,1,1,'rbac-hr-c-001','1776072367_03e05fe75c.jpg','CRIT 100',NULL,'2026-04-13 09:26:07','2026-04-13 09:26:07'),(60,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',NULL,NULL,1,1,'xxx-xxx-c-002',NULL,'Test',NULL,'2026-04-14 00:54:36','2026-04-14 00:54:36'),(61,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,1,1,1,'bug-dash-c-005','1776131477_1733e03763.png','Test',NULL,'2026-04-14 01:51:17','2026-04-14 01:51:17'),(62,'ecf74437-2721-11f1-80cd-fc068c03d3f8',1,NULL,1,1,'bug-xxx-c-002','1776136453_bdf54571cc.png','Test',NULL,'2026-04-14 03:14:13','2026-04-14 03:14:13'),(63,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,1,1,1,'rbac-dash-c-002',NULL,'Test 101',NULL,'2026-04-16 00:59:05','2026-04-16 00:59:05'),(64,'ecf74437-2721-11f1-80cd-fc068c03d3f8',1,3,3,2,'bug-pay-m-001',NULL,'Testttttt','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-16 02:45:13','2026-04-16 02:46:46'),(66,'ecf74437-2721-11f1-80cd-fc068c03d3f8',7,4,1,1,'perf-proc-c-001',NULL,'fdsafdsaf',NULL,'2026-04-16 03:30:57','2026-04-16 03:30:57'),(67,'ecf74437-2721-11f1-80cd-fc068c03d3f8',4,2,1,1,'valid-hr-c-001',NULL,'fdsafdsafdsafasd',NULL,'2026-04-16 03:31:06','2026-04-16 03:31:06'),(68,'ecf74437-2721-11f1-80cd-fc068c03d3f8',3,2,1,1,'uxui-hr-c-001',NULL,'asfasdfasd',NULL,'2026-04-16 03:42:05','2026-04-16 03:42:05');
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
  `cat_id` int DEFAULT NULL,
  `mod_id` int DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_archive`
--

LOCK TABLES `report_archive` WRITE;
/*!40000 ALTER TABLE `report_archive` DISABLE KEYS */;
INSERT INTO `report_archive` VALUES (6,'ecf74437-2721-11f1-80cd-fc068c03d3f8',2,1,1,3,'rbac-dash-c-001',NULL,'wala lang',NULL,'2026-04-01 01:58:18','2026-04-01 08:13:23'),(7,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,3,2,3,'rbac-pay-h-001',NULL,'wala',NULL,'2026-04-01 02:07:33','2026-04-01 08:17:14'),(8,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,3,1,3,'rbac-pay-c-002','1775014211_d47d3471b8.png','nothuing happen','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-01 03:30:11','2026-04-01 08:28:00'),(9,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,6,2,3,'rbac-po-h-001',NULL,'ayaw ko nga','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-01 08:28:10','2026-04-07 06:24:05'),(16,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',NULL,NULL,1,3,'xxx-xxx-c-001','1775445411_d080304cee.png','ewan','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-06 03:16:51','2026-04-06 03:37:46'),(17,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,NULL,1,3,'bug-xxx-c-001',NULL,'wala','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-06 03:21:40','2026-04-06 03:37:07'),(18,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',NULL,5,1,3,'xxx-pr-c-001',NULL,'wala','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-06 03:37:59','2026-04-06 03:38:07'),(19,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,NULL,1,3,'bug-xxx-c-001',NULL,'wala rin','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-06 03:38:20','2026-04-06 03:38:45'),(20,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',NULL,NULL,1,3,'xxx-xxx-c-001',NULL,'wala','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-06 03:38:37','2026-04-06 03:38:46'),(21,'ecf74437-2721-11f1-80cd-fc068c03d3f8',10,11,1,3,'sec-pim-c-001',NULL,'ano lalabas','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-06 05:27:45','2026-04-07 06:24:07'),(22,'ecf74437-2721-11f1-80cd-fc068c03d3f8',1,NULL,2,3,'bug-xxx-h-001',NULL,'HAHokokh','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-06 05:27:56','2026-04-07 06:24:09'),(23,'ecf74437-2721-11f1-80cd-fc068c03d3f8',NULL,6,3,3,'xxx-po-m-001',NULL,'ewan ko rin ehhhh','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-06 05:28:13','2026-04-07 06:24:11'),(24,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,6,1,3,'bug-po-c-002','1775462519_17d0eead2e.png','SER GAB','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-06 08:01:59','2026-04-06 08:02:32'),(25,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,10,1,3,'bug-inv-c-001',NULL,'wala','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 05:34:01','2026-04-07 06:24:13'),(26,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,10,1,3,'bug-inv-c-002',NULL,'wala','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 05:50:56','2026-04-07 07:00:35'),(27,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,4,1,3,'rbac-proc-c-001',NULL,'','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 05:55:40','2026-04-10 01:11:19'),(28,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',11,15,1,3,'logout-jo-c-001',NULL,'di maka log out','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 05:56:32','2026-04-08 06:05:30'),(29,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,6,4,3,'rbac-po-l-002',NULL,'asd','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 05:57:35','2026-04-10 01:13:03'),(30,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',6,6,3,4,'data-po-m-001',NULL,'wala','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 05:58:02','2026-04-10 03:28:32'),(31,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',3,15,1,4,'uxui-jo-c-001',NULL,'asdas','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 05:59:11','2026-04-10 03:29:00'),(32,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,1,2,3,'bug-dash-h-001',NULL,'wala','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:09:58','2026-04-10 03:32:11'),(33,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',NULL,1,4,3,'xxx-dash-l-001',NULL,'test','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:21:17','2026-04-10 08:26:17'),(34,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',3,2,2,4,'uxui-hr-h-001',NULL,'sadasd','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:23:41','2026-04-13 09:25:07'),(35,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',4,3,1,3,'valid-pay-c-001',NULL,'ksdjfgasdjkf','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:24:23','2026-04-14 08:38:43'),(36,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',NULL,1,1,3,'xxx-dash-c-001',NULL,'asdasd','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:25:57','2026-04-14 08:39:05'),(37,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,2,1,3,'bug-hr-c-001',NULL,'fhj','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:29:16','2026-04-15 05:09:36'),(38,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',NULL,3,3,4,'xxx-pay-m-001',NULL,'sadas','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:32:48','2026-04-15 05:26:57'),(39,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',NULL,1,2,3,'xxx-dash-h-002',NULL,'asdas','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:34:10','2026-04-15 05:26:59'),(40,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,NULL,1,3,'bug-xxx-c-001',NULL,'dsad','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:36:45','2026-04-15 05:20:20'),(41,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,9,2,3,'bug-fin-h-001',NULL,'asdasd','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:37:48','2026-04-15 06:00:33'),(42,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,3,1,3,'bug-pay-c-001',NULL,'dsadas','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:55:45','2026-04-15 05:21:31'),(43,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',2,4,2,3,'rbac-proc-h-001','1775544996_3e2ecdc4c1.png','sdfgdfg','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 06:56:36','2026-04-16 01:20:18'),(44,'ecf74437-2721-11f1-80cd-fc068c03d3f8',1,1,2,3,'bug-dash-h-002',NULL,'asdsad','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-07 07:05:09','2026-04-16 02:44:37'),(45,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad',1,1,2,3,'bug-dash-h-003',NULL,'fgdsg','cbf12398-7d7e-4e03-9f1d-21d73999c2ad','2026-04-07 07:25:16','2026-04-07 07:26:52'),(46,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,1,4,4,'bug-dash-l-003',NULL,'TEST','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 01:01:51','2026-04-16 03:05:08'),(50,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,1,1,3,'bug-dash-c-006',NULL,'','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 02:44:04','2026-04-10 05:41:05'),(53,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,2,4,3,'bug-hr-l-002',NULL,'test','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 03:28:14','2026-04-10 03:28:26'),(54,'ecf740ae-2721-11f1-80cd-fc068c03d3f8',1,2,3,3,'bug-hr-m-002',NULL,'sdjkfhjasdk','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 03:46:18','2026-04-15 09:33:51'),(65,'ecf74437-2721-11f1-80cd-fc068c03d3f8',1,7,1,4,'bug-ap-c-001',NULL,'fsdfasdfadf','ecf74437-2721-11f1-80cd-fc068c03d3f8','2026-04-16 03:30:47','2026-04-16 03:47:56');
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
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suggestion_archive`
--

LOCK TABLES `suggestion_archive` WRITE;
/*!40000 ALTER TABLE `suggestion_archive` DISABLE KEYS */;
INSERT INTO `suggestion_archive` VALUES (1,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','Maligo',3,NULL,NULL,'2026-03-27 05:48:05','2026-04-01 08:08:19'),(4,'ecf74437-2721-11f1-80cd-fc068c03d3f8','Mag lagay na ng Design CSS',3,'sug_1775028693_5b212358.jpg',NULL,'2026-04-01 07:31:34','2026-04-01 08:17:45'),(5,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','test',4,NULL,NULL,'2026-04-01 07:33:06','2026-04-01 08:18:05'),(6,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','mag jogging 3x a day',3,NULL,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-01 08:22:50','2026-04-06 09:22:51'),(7,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','sugestion 101',3,NULL,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad','2026-04-07 06:40:50','2026-04-07 07:02:23'),(8,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad','basta suggest',3,NULL,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad','2026-04-07 07:04:11','2026-04-07 07:07:52'),(9,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad','isa pa',3,NULL,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad','2026-04-07 07:04:14','2026-04-07 07:08:18'),(10,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','lol hehehe baho ng posa kong si Gnar meow meow ',3,'sug_1775627785_dd573480.jpg','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-08 05:56:25','2026-04-08 06:23:23'),(11,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','asdasdasdasd',3,'sug_1775627921_87f54148.jpg','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-08 05:58:41','2026-04-10 00:49:39'),(12,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','TEST',3,'sug_1775782005_b6cf6f06.jpg','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 00:46:45','2026-04-10 03:29:19'),(13,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','TEST 2',3,NULL,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 00:48:20','2026-04-10 05:53:07'),(14,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','AYAW',3,'sug_1775782175_53eaa42d.jpg','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 00:49:35','2026-04-10 06:00:15'),(15,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','I suggest na maging hatdog tayong lahat',3,NULL,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 05:55:30','2026-04-10 05:55:49'),(17,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','Please Add proper things',3,'sug_1775801579_a3c78a97.png','cbf12398-7d7e-4e03-9f1d-21d73999c2ad','2026-04-10 06:12:59','2026-04-10 06:37:42'),(18,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad','Buhay ay di karera',3,NULL,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad','2026-04-10 06:17:06','2026-04-10 06:35:44'),(19,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad','TEST',3,'sug_1775803155_2a944eca.png','cbf12398-7d7e-4e03-9f1d-21d73999c2ad','2026-04-10 06:39:15','2026-04-10 06:39:21'),(20,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','TEST ',3,NULL,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 07:00:11','2026-04-10 08:26:23'),(24,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','>',3,NULL,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-13 08:59:32','2026-04-13 08:59:40'),(25,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','ANG POGI NI SER REIGNNE SOBRAAAAAAA',4,'sug_1776072412_984648c5.jpg','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-13 09:26:52','2026-04-13 09:27:14');
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
INSERT INTO `user_profile` VALUES ('06aace05-e7be-4b27-a89a-ca0bf9b0fac3','Christina May','Lebuna','Aguilar','2003-04-12','gmail@gmail.com','06aace05-e7be-4b27-a89a-ca0bf9b0fac3.jpg'),('184251a9-9554-4ef8-bf0b-d03b06d33dd9','Vincent','Binas','Pasion','2001-03-31','vincent@gmail.com','69cc8fbf9f65d.jpg'),('3adb4433-0e84-4b4b-bdb9-c8b742ccd7d3','Jhizelle','remolin','De Guzman','2003-01-22','jhizelle@gmail.com','3adb4433-0e84-4b4b-bdb9-c8b742ccd7d3.png'),('3cf46000-78f7-4221-9a8c-b7663ec2d3bb','james','gitna','aguilar','2000-02-12','james@gmail.com','default.png'),('61b23cef-9381-4d9c-bd0e-7db0b71b3580','Jonalyn','Jormigos','Parreno','2001-07-25','email@gmail.com',NULL),('cbf12398-7d7e-4e03-9f1d-21d73999c2ad','Nathaniel','Middle','Lincuran','2026-03-02','naths@gmail.com','69c63c80bb3ee.png'),('d49b2d24-303f-4a4f-aa0b-d681928ce9e8','yves','middle','syent','1999-12-23','syent@gmail.com','default.png'),('ecf740ae-2721-11f1-80cd-fc068c03d3f8','mickyl','Gaytana','Sumagang','2003-06-09','test@gmail.com','default.png'),('ecf74437-2721-11f1-80cd-fc068c03d3f8','Niel','Magsumbol','Apaitan','2003-10-21','test1@gmail.com',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_suggestions`
--

LOCK TABLES `user_suggestions` WRITE;
/*!40000 ALTER TABLE `user_suggestions` DISABLE KEYS */;
INSERT INTO `user_suggestions` VALUES (16,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','Suggestion ko umuwi na tayong lahat at mamuhay ng mapayapa at marangal <3',1,'sug_1775801108_78440c50.png','ecf740ae-2721-11f1-80cd-fc068c03d3f8','2026-04-10 06:05:08','2026-04-10 06:05:25'),(21,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','Merge Completed',1,NULL,NULL,'2026-04-10 08:22:13','2026-04-10 08:22:13'),(22,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad','ergsdfgdfgfsgsdfgdfASHDFJKLahfjkhsdjkfhjsdkalhfdjkslajkxlznJCKlxnzjklcvbejkslahdjkflehdjskhfdhfhfdhjkladfhjklasdnjcvklsdanfkj,lsdafnklsd;afdsaf',1,NULL,NULL,'2026-04-10 08:34:51','2026-04-10 08:34:51'),(23,'cbf12398-7d7e-4e03-9f1d-21d73999c2ad','dsfffffffffffffffffsafsdfjasdlfhajksdhfkdlwhfklsdhfjsdklahfkljsdhfjkldhsafjkdshafjkdlshafjkdlhasjfkdlsahjfkldshajkfldhsajkfldhajkflhdjksalfhdjklsafhdjsahfhasdfhfhfhhhfhffsdjkalfhdsjaklfhdsjkafhjkdsahfjkdlhsajklfdhsjkalfhdjsklafhjdkslahfjkdlsahjfkldsahfjkdshafkdhsakflshdaklfhsadlfsadfasdf',1,NULL,NULL,'2026-04-10 08:35:12','2026-04-10 08:35:12'),(26,'ecf740ae-2721-11f1-80cd-fc068c03d3f8','Test',1,'sug_1776132897_52e52640.png',NULL,'2026-04-14 02:14:57','2026-04-14 02:14:57');
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
INSERT INTO `users` VALUES ('06aace05-e7be-4b27-a89a-ca0bf9b0fac3','tinay','$2y$12$luAUSB1Ju0iP4XIJsnG93.qSsXfIwbSXBRR1YKhYfGPQ70Ka6nzma',2,1,'2026-04-13 02:07:28','2026-04-13 02:08:13'),('184251a9-9554-4ef8-bf0b-d03b06d33dd9','vincent','$2y$12$1gypdgmTq4mhMbMtdQyUA.neCdiDTXaqBseHyAFbQWuJFPNEstNoa',1,1,'2026-04-01 03:23:12','2026-04-01 03:23:12'),('3adb4433-0e84-4b4b-bdb9-c8b742ccd7d3','Jhizelle','$2y$12$mk9ulgzC5lUk845nt.tFrOTgbSS/ZK2f1KZiPCO.Kyki1k.D9k3n.',1,1,'2026-04-07 06:45:20','2026-04-16 01:11:19'),('3cf46000-78f7-4221-9a8c-b7663ec2d3bb','james','$2y$12$dto1.tK.ZjvOh.EY/qY7Y.AYL29qfDSuo4nPj1SX/yf/fMCl4a02O',1,1,'2026-04-13 07:47:46','2026-04-16 01:26:19'),('61b23cef-9381-4d9c-bd0e-7db0b71b3580','jonalyn','$2y$12$c3FFRvfrGPVjD87v9XzbO.IOqj5EF9W8LrZWzG75omSo1RQsC/2ru',1,1,'2026-03-27 08:07:59','2026-04-13 02:53:45'),('cbf12398-7d7e-4e03-9f1d-21d73999c2ad','naths','$2y$12$cAqPjEqb4Lx4CoUYmyKVAuLNpx2139E5KXq52L1lXylryZuRsWWsy',1,1,'2026-03-27 08:14:56','2026-04-13 07:52:42'),('d49b2d24-303f-4a4f-aa0b-d681928ce9e8','yves','$2y$12$Fkx.oCGEPJ107sDSxnauVufp5vLGxM1pGl5cWpwPuCk4UAAK2FhTi',1,1,'2026-04-13 07:49:03','2026-04-15 09:39:11'),('ecf740ae-2721-11f1-80cd-fc068c03d3f8','admin','$2y$12$9ws8..jQHFKZDdP/v6m6POZE1HGvOiNdQJpjYWNdQAaL7PMk/23om',1,1,'2026-03-27 05:47:55','2026-04-13 02:45:50'),('ecf74437-2721-11f1-80cd-fc068c03d3f8','niel','$2y$12$bLYvFjsMo.YIAL7SrGcTMeZUeIOAX4TTQ1aNx5c6OF1JQXjyDCKCu',2,1,'2026-03-27 05:47:55','2026-04-13 07:53:29');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `v_dashboard_reports`
--

DROP TABLE IF EXISTS `v_dashboard_reports`;
/*!50001 DROP VIEW IF EXISTS `v_dashboard_reports`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_dashboard_reports` AS SELECT 
 1 AS `report_id`,
 1 AS `ref_num`,
 1 AS `user_id`,
 1 AS `cat_id`,
 1 AS `sev_id`,
 1 AS `status_id`,
 1 AS `created_at`,
 1 AS `record_type`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_dashboard_reports`
--

/*!50001 DROP VIEW IF EXISTS `v_dashboard_reports`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_dashboard_reports` AS select `report`.`report_id` AS `report_id`,`report`.`ref_num` AS `ref_num`,`report`.`user_id` AS `user_id`,`report`.`cat_id` AS `cat_id`,`report`.`sev_id` AS `sev_id`,`report`.`status_id` AS `status_id`,`report`.`report_created_at` AS `created_at`,'Active' AS `record_type` from `report` union all select `report_archive`.`report_id` AS `report_id`,`report_archive`.`ref_num` AS `ref_num`,`report_archive`.`user_id` AS `user_id`,`report_archive`.`cat_id` AS `cat_id`,`report_archive`.`sev_id` AS `sev_id`,`report_archive`.`status_id` AS `status_id`,`report_archive`.`report_created_at` AS `created_at`,'Archived' AS `record_type` from `report_archive` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-16 14:03:00
