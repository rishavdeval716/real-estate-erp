-- ==============================================================================
-- Migration: 2026-10-01-000014_CreateLeadsAndProspectsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: leads, prospects
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lead_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternate_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_source_id` int unsigned DEFAULT NULL,
  `assigned_user_id` int unsigned DEFAULT NULL,
  `branch_id` int unsigned DEFAULT NULL,
  `lead_status` enum('New','Contacted','Qualified','Unqualified','Converted','Lost') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'New',
  `lead_stage` enum('New','Contacted','Qualified','Site Visit Scheduled','Site Visit Completed','Negotiation','Token Pending','Ready for Booking','Won','Lost') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'New',
  `priority` enum('Low','Medium','High','Urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Medium',
  `budget_min` decimal(15,2) DEFAULT NULL,
  `budget_max` decimal(15,2) DEFAULT NULL,
  `preferred_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_type_id` int unsigned DEFAULT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `purchase_purpose` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_timeline` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `financing_required` enum('Yes','No','Undecided') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Undecided',
  `site_visit_required` enum('Yes','No','Scheduled','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_code` (`lead_code`),
  KEY `leads_lead_source_id_foreign` (`lead_source_id`),
  KEY `leads_assigned_user_id_foreign` (`assigned_user_id`),
  KEY `leads_branch_id_foreign` (`branch_id`),
  KEY `leads_property_type_id_foreign` (`property_type_id`),
  KEY `leads_project_id_foreign` (`project_id`),
  KEY `leads_property_id_foreign` (`property_id`),
  KEY `leads_property_unit_id_foreign` (`property_unit_id`),
  KEY `phone` (`phone`),
  KEY `email` (`email`),
  KEY `lead_status` (`lead_status`),
  KEY `lead_stage` (`lead_stage`),
  KEY `priority` (`priority`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `leads_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `leads_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `leads_lead_source_id_foreign` FOREIGN KEY (`lead_source_id`) REFERENCES `lead_sources` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `leads_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `leads_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `leads_property_type_id_foreign` FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `leads_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prospects`
--

DROP TABLE IF EXISTS `prospects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prospects` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternate_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_contact_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Phone',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prospects_lead_id_foreign` (`lead_id`),
  CONSTRAINT `prospects_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
