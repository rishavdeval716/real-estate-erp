-- ==============================================================================
-- Migration: 2026-10-01-000023_CreateCommissionsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: commission_rules, commissions
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `commission_rules`
--

DROP TABLE IF EXISTS `commission_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commission_rules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission_type` enum('Percentage','Fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Percentage',
  `commission_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `applicable_to` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'All',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `commissions`
--

DROP TABLE IF EXISTS `commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int unsigned NOT NULL,
  `agent_user_id` int unsigned NOT NULL,
  `commission_rule_id` int unsigned DEFAULT NULL,
  `booking_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `commission_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('Pending','Approved','Paid','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `payable_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commissions_commission_rule_id_foreign` (`commission_rule_id`),
  KEY `booking_id` (`booking_id`),
  KEY `agent_user_id` (`agent_user_id`),
  KEY `status` (`status`),
  CONSTRAINT `commissions_agent_user_id_foreign` FOREIGN KEY (`agent_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `commissions_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `commissions_commission_rule_id_foreign` FOREIGN KEY (`commission_rule_id`) REFERENCES `commission_rules` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
