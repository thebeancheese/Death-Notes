-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: deathnotes
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `death_records`
--

DROP TABLE IF EXISTS `death_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `death_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `deceased_name` varchar(150) NOT NULL,
  `date_of_death` date NOT NULL,
  `place_of_death` varchar(150) NOT NULL,
  `cause_of_death` varchar(200) NOT NULL,
  `informant_name` varchar(150) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `applicant_user_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` varchar(30) DEFAULT 'Pending',
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`record_id`),
  KEY `applicant_user_id` (`applicant_user_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `death_records_ibfk_1` FOREIGN KEY (`applicant_user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `death_records_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `death_records`
--

LOCK TABLES `death_records` WRITE;
/*!40000 ALTER TABLE `death_records` DISABLE KEYS */;
INSERT INTO `death_records` VALUES (1,'Jose Rizal','2026-02-15','Bagumbayan','Gun Shooting','Josephine Bracken','Sexual Partner',9,8,'Pending','2026-02-15 11:55:55'),(2,'Andres Bonifactio','2026-02-04','Cavite','Riot','Asher Malabanan','Grandson',9,6,'Verified','2026-02-15 11:56:43'),(3,'Charlie Kirk','2026-01-01','U.S.A','Assassination','Donald Trump','Cousin',10,7,'Rejected','2026-02-15 12:33:56'),(4,'Juice World','2026-02-03','U.S.A','Overdose','Aubrey Graham','Opps',10,8,'Rejected','2026-02-15 12:36:07'),(5,'Rodrigo Dutertle','2025-12-25','Netherlands','HIV','Kitty Dutertle','Daughter',10,1,'Approved','2026-02-15 12:37:37'),(6,'Allysa Apao','2025-01-31','Sindalan','SA','Jian Christian Pascual','Ex-Husband',12,11,'Approved','2026-02-15 12:49:57');
/*!40000 ALTER TABLE `death_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin'),(2,'Barangay Staff'),(3,'Municipal Staff'),(4,'User');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 4,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'System Admin','admin@deathnotes.com','$2y$10$njjOriUlp8yE3P42PEeZF.TNDL6ZIQOB.rtee8U8akFbXF90EL3Dy',1,'active','2026-02-15 10:27:41'),(6,'Anhony Fernan Dela Cruz','adc@deathnotes.com','$2y$10$0fCpg7Rsf5YnneVEWePqsuziS2qriAsZ682MY0mzV3sxCo7xQYyOy',2,'active','2026-02-15 11:40:34'),(7,'Eisen Josh Bangit','eb@deathnotes.com','$2y$10$dX8s5qz6KHS7BDVbv7jka.ZDFDf16vndpcauOylQVZfF3JYUt6ive',2,'active','2026-02-15 11:43:35'),(8,'Vince Raiezen Cruzada','vc@deathnotes.com','$2y$10$YF8EekFedU8WHI/KFZnLbuh.S7SBgIctZRZWaD7IT.0h7Ttbjp/Ki',2,'active','2026-02-15 11:43:52'),(9,'Asher Malabanan','am@deathnotes.com','$2y$10$5okw./J5tK.RGcCcky3q3ONnHO5Qy1XlVI6jH6ozDj3hWH8AwZTfm',3,'active','2026-02-15 11:45:48'),(10,'Paolo Yshmael Trinidad','pt@deathnotes.com','$2y$10$aT8RThGrT06lUTaHKYj89uF1KECTCBp0N0o8S6tug5KmCV4xY2gYu',3,'active','2026-02-15 12:32:17'),(11,'Ivan Bacani','ib@deathnotes.com','$2y$10$BPY0CT07roG8s2lh/rfLWeOEHeXNdltZ7F2IxtVCw4v0QpBEcjbBW',2,'active','2026-02-15 12:45:50'),(12,'Jian Christian Pascual','jp@deathnotes.com','$2y$10$3jR1rGG4H6VTnJr9wbZuxOJ7aLwTIY.28YQ2MRbXnu6lh884rV.DC',3,'active','2026-02-15 12:47:26'),(13,'Asher Malabanan','acm@deathnotes.com','$2y$10$LOIQ7nOFc6D1ro.Axl6pmO46c/gQX9gjihza4fMiqAiWadu3/fKA.',3,'active','2026-02-15 13:23:23');
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

-- Dump completed on 2026-02-15 21:52:12
