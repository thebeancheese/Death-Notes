
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
DROP TABLE IF EXISTS `death_certificate_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `death_certificate_requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `record_id` int(11) NOT NULL,
  `requester_user_id` int(11) NOT NULL,
  `recipient_email` varchar(190) NOT NULL,
  `purpose` varchar(150) NOT NULL,
  `copies_requested` int(11) NOT NULL DEFAULT 1,
  `contact_number` varchar(40) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Submitted',
  `email_status` varchar(30) NOT NULL DEFAULT 'Pending',
  `email_error` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `emailed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `idx_certificate_requests_record` (`record_id`),
  KEY `idx_certificate_requests_requester` (`requester_user_id`),
  KEY `idx_certificate_requests_submitted` (`submitted_at`),
  CONSTRAINT `fk_certificate_requests_record` FOREIGN KEY (`record_id`) REFERENCES `death_records` (`record_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_certificate_requests_requester` FOREIGN KEY (`requester_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `death_certificate_requests` WRITE;
/*!40000 ALTER TABLE `death_certificate_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `death_certificate_requests` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `death_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `death_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `tracking_code` varchar(20) DEFAULT NULL,
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
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`record_id`),
  UNIQUE KEY `idx_tracking_code` (`tracking_code`),
  KEY `applicant_user_id` (`applicant_user_id`),
  KEY `created_by` (`created_by`),
  KEY `idx_death_records_deleted_at` (`deleted_at`),
  KEY `idx_death_records_deleted_by` (`deleted_by`),
  CONSTRAINT `death_records_ibfk_1` FOREIGN KEY (`applicant_user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `death_records_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `fk_death_records_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `death_records` WRITE;
/*!40000 ALTER TABLE `death_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `death_records` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `email_verification_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_verification_tokens` (
  `verification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`verification_id`),
  UNIQUE KEY `uniq_email_verification_token` (`token_hash`),
  KEY `idx_email_verification_user` (`user_id`),
  CONSTRAINT `fk_email_verification_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `email_verification_tokens` WRITE;
/*!40000 ALTER TABLE `email_verification_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_verification_tokens` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `related_record_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notification_id`),
  KEY `idx_notifications_user` (`user_id`),
  KEY `idx_notifications_user_read` (`user_id`,`is_read`),
  KEY `idx_notifications_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `reset_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`reset_id`),
  UNIQUE KEY `uniq_password_reset_token` (`token_hash`),
  KEY `idx_password_reset_user` (`user_id`),
  CONSTRAINT `fk_password_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `record_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `record_activity_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `related_record_id` int(11) DEFAULT NULL,
  `actor_user_id` int(11) NOT NULL,
  `affected_user_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `old_status` varchar(30) DEFAULT NULL,
  `new_status` varchar(30) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `idx_logs_record` (`related_record_id`),
  KEY `idx_logs_actor` (`actor_user_id`),
  KEY `idx_logs_affected` (`affected_user_id`),
  KEY `idx_logs_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `record_activity_logs` WRITE;
/*!40000 ALTER TABLE `record_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `record_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `record_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `record_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `record_id` int(11) NOT NULL,
  `document_type` varchar(80) NOT NULL,
  `original_file_name` varchar(255) NOT NULL,
  `stored_file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `mime_type` varchar(120) DEFAULT NULL,
  `file_size` int(11) NOT NULL DEFAULT 0,
  `uploaded_by` int(11) NOT NULL,
  `review_status` varchar(30) NOT NULL DEFAULT 'Pending Review',
  `review_notes` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`document_id`),
  KEY `idx_record_documents_record` (`record_id`),
  KEY `idx_record_documents_uploaded_by` (`uploaded_by`),
  KEY `idx_record_documents_reviewed_by` (`reviewed_by`),
  KEY `idx_record_documents_status` (`review_status`),
  CONSTRAINT `fk_record_documents_record` FOREIGN KEY (`record_id`) REFERENCES `death_records` (`record_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_record_documents_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_record_documents_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `record_documents` WRITE;
/*!40000 ALTER TABLE `record_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `record_documents` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin'),(2,'Barangay Staff'),(3,'User');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `social_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_accounts` (
  `social_account_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider` varchar(30) NOT NULL,
  `provider_user_id` varchar(191) NOT NULL,
  `provider_email` varchar(191) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`social_account_id`),
  UNIQUE KEY `uniq_provider_user` (`provider`,`provider_user_id`),
  KEY `idx_social_user` (`user_id`),
  CONSTRAINT `fk_social_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `social_accounts` WRITE;
/*!40000 ALTER TABLE `social_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `social_accounts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 3,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `email_verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'System Admin','adminmemento@gmail.com','$2y$10$0X0jn8KcOppD9irxsLJ1ze..jvO/Ruyo6xLDzDdEWitCmoda20SOi',1,'active','2026-03-20 23:28:03','2026-03-20 15:28:03');
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

