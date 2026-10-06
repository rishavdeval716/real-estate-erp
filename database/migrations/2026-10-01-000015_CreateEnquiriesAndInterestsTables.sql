-- ==============================================================================
-- Migration: 2026-10-01-000015_CreateEnquiriesAndInterestsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: enquiries, lead_property_interests
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `enquiries`
--

DROP TABLE IF EXISTS `enquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enquiries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `enquiry_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_id` int unsigned NOT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `enquiry_type` enum('Purchase','Investment','Rent','Commercial','Plot/Land','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Purchase',
  `requirement` text COLLATE utf8mb4_unicode_ci,
  `budget` decimal(15,2) DEFAULT NULL,
  `preferred_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_property_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Open','In Progress','Qualified','Closed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Open',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enquiry_code` (`enquiry_code`),
  KEY `enquiries_lead_id_foreign` (`lead_id`),
  KEY `enquiries_project_id_foreign` (`project_id`),
  KEY `enquiries_property_id_foreign` (`property_id`),
  KEY `enquiries_property_unit_id_foreign` (`property_unit_id`),
  KEY `enquiries_created_by_foreign` (`created_by`),
  KEY `status` (`status`),
  CONSTRAINT `enquiries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `enquiries_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `enquiries_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `enquiries_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `enquiries_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_property_interests`
--

DROP TABLE IF EXISTS `lead_property_interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_property_interests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int unsigned NOT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `interest_level` enum('Primary','Interested','Alternative','Not Interested') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Interested',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_property_interests_lead_id_foreign` (`lead_id`),
  KEY `lead_property_interests_project_id_foreign` (`project_id`),
  KEY `lead_property_interests_property_id_foreign` (`property_id`),
  KEY `lead_property_interests_property_unit_id_foreign` (`property_unit_id`),
  CONSTRAINT `lead_property_interests_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lead_property_interests_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `lead_property_interests_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `lead_property_interests_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
