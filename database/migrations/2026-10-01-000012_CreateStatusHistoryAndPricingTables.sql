-- ==============================================================================
-- Migration: 2026-10-01-000012_CreateStatusHistoryAndPricingTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: property_status_history, property_pricing
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `property_status_history`
--

DROP TABLE IF EXISTS `property_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_status_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `property_id` int unsigned DEFAULT NULL,
  `unit_id` int unsigned DEFAULT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` int unsigned DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_status_history_changed_by_foreign` (`changed_by`),
  KEY `property_id` (`property_id`),
  KEY `unit_id` (`unit_id`),
  KEY `new_status` (`new_status`),
  CONSTRAINT `property_status_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `property_status_history_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `property_status_history_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `property_units` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `property_pricing`
--

DROP TABLE IF EXISTS `property_pricing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_pricing` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `property_id` int unsigned NOT NULL,
  `unit_id` int unsigned DEFAULT NULL,
  `base_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `price_per_sqft` decimal(15,2) DEFAULT NULL,
  `market_price` decimal(15,2) DEFAULT NULL,
  `negotiated_price` decimal(15,2) DEFAULT NULL,
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `property_pricing_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `property_pricing_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `property_units` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
