-- ==============================================================================
-- Migration: 2026-10-01-000028_CreateFinanceComplianceAndTdsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: tds_entries, tds_certificates, portal_requests
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `tds_entries`
--

DROP TABLE IF EXISTS `tds_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tds_entries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `entry_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `party_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pan_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '194H',
  `transaction_reference` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission_id` int unsigned DEFAULT NULL,
  `gross_amount` decimal(12,2) NOT NULL,
  `tds_rate` decimal(5,2) NOT NULL DEFAULT '5.00',
  `tds_amount` decimal(12,2) NOT NULL,
  `net_payable` decimal(12,2) NOT NULL,
  `deduction_date` date NOT NULL,
  `financial_year` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quarter` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('deducted','deposited','certified') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'deducted',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_code` (`entry_code`),
  KEY `tds_entries_commission_id_foreign` (`commission_id`),
  KEY `pan_number` (`pan_number`),
  KEY `status` (`status`),
  CONSTRAINT `tds_entries_commission_id_foreign` FOREIGN KEY (`commission_id`) REFERENCES `commissions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tds_certificates`
--

DROP TABLE IF EXISTS `tds_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tds_certificates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `certificate_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `party_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pan_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_amount` decimal(12,2) NOT NULL,
  `tds_amount` decimal(12,2) NOT NULL,
  `financial_year` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quarter` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `certificate_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` date NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificate_number` (`certificate_number`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `portal_requests`
--

DROP TABLE IF EXISTS `portal_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `portal_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `request_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `portal_type` enum('customer','tenant','partner') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `request_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('submitted','in_progress','approved','rejected','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_code` (`request_code`),
  KEY `portal_requests_user_id_foreign` (`user_id`),
  KEY `portal_type_user_id` (`portal_type`,`user_id`),
  CONSTRAINT `portal_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
