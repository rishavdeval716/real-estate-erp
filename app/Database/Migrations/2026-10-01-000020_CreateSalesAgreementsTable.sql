-- ==============================================================================
-- Migration: 2026-10-01-000020_CreateSalesAgreementsTable
-- Converted from PHP migration to MySQL DDL
-- Target tables: sales_agreements
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `sales_agreements`
--

DROP TABLE IF EXISTS `sales_agreements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_agreements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `agreement_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `property_unit_id` int unsigned NOT NULL,
  `agreement_date` date NOT NULL,
  `agreement_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sale Agreement',
  `agreement_status` enum('Draft','Pending Signature','Signed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `total_value` decimal(15,2) NOT NULL DEFAULT '0.00',
  `terms_conditions` text COLLATE utf8mb4_unicode_ci,
  `special_conditions` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agreement_number` (`agreement_number`),
  KEY `sales_agreements_created_by_foreign` (`created_by`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  KEY `project_id` (`project_id`),
  KEY `property_id` (`property_id`),
  KEY `property_unit_id` (`property_unit_id`),
  KEY `agreement_status` (`agreement_status`),
  KEY `agreement_date` (`agreement_date`),
  CONSTRAINT `sales_agreements_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `sales_agreements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sales_agreements_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `sales_agreements_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sales_agreements_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sales_agreements_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
