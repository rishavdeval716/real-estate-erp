-- ==============================================================================
-- Migration: 2026-10-01-000019_CreateBookingsAndHistoryTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: bookings, booking_status_history
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `booking_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `lead_id` int unsigned DEFAULT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `property_unit_id` int unsigned NOT NULL,
  `sales_executive_id` int unsigned DEFAULT NULL,
  `booking_date` date NOT NULL,
  `booking_status` enum('Draft','Pending Confirmation','Confirmed','Cancelled','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `base_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `final_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `token_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `booking_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_number` (`booking_number`),
  KEY `bookings_created_by_foreign` (`created_by`),
  KEY `customer_id` (`customer_id`),
  KEY `lead_id` (`lead_id`),
  KEY `project_id` (`project_id`),
  KEY `property_id` (`property_id`),
  KEY `property_unit_id` (`property_unit_id`),
  KEY `sales_executive_id` (`sales_executive_id`),
  KEY `booking_status` (`booking_status`),
  KEY `booking_date` (`booking_date`),
  CONSTRAINT `bookings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `bookings_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bookings_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bookings_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bookings_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `bookings_sales_executive_id_foreign` FOREIGN KEY (`sales_executive_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `booking_status_history`
--

DROP TABLE IF EXISTS `booking_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_status_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int unsigned NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` int unsigned DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_status_history_changed_by_foreign` (`changed_by`),
  KEY `booking_id` (`booking_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `booking_status_history_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `booking_status_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
