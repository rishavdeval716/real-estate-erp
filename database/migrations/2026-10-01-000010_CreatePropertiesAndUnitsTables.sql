-- ==============================================================================
-- Migration: 2026-10-01-000010_CreatePropertiesAndUnitsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: properties, property_units
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `properties` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `property_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `property_type_id` int unsigned NOT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `location_id` int unsigned NOT NULL,
  `owner_id` int unsigned DEFAULT NULL,
  `owner_name_or_reference` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ownership_details` text COLLATE utf8mb4_unicode_ci,
  `area` decimal(12,2) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `status` enum('Available','Reserved','Under Negotiation','Booked','Sold','Rented','Under Maintenance','Unavailable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Available',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `property_code` (`property_code`),
  KEY `property_type_id` (`property_type_id`),
  KEY `project_id` (`project_id`),
  KEY `location_id` (`location_id`),
  KEY `status` (`status`),
  CONSTRAINT `properties_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `properties_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `properties_property_type_id_foreign` FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `property_units`
--

DROP TABLE IF EXISTS `property_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_units` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `property_id` int unsigned DEFAULT NULL,
  `project_id` int unsigned NOT NULL,
  `tower_id` int unsigned DEFAULT NULL,
  `unit_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `floor` int NOT NULL DEFAULT '0',
  `flat_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `carpet_area` decimal(10,2) NOT NULL,
  `built_up_area` decimal(10,2) NOT NULL,
  `balcony` int unsigned NOT NULL DEFAULT '0',
  `parking` int unsigned NOT NULL DEFAULT '0',
  `facing` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `availability_status` enum('Available','Reserved','Under Negotiation','Booked','Sold','Rented','Under Maintenance','Unavailable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Available',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  KEY `project_id` (`project_id`),
  KEY `tower_id` (`tower_id`),
  KEY `unit_number` (`unit_number`),
  KEY `availability_status` (`availability_status`),
  CONSTRAINT `property_units_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `property_units_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `property_units_tower_id_foreign` FOREIGN KEY (`tower_id`) REFERENCES `project_towers` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
