-- ==============================================================================
-- Migration: 2026-10-01-000017_CreateSiteVisitsAndUnitHoldsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: site_visits, unit_holds
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `site_visits`
--

DROP TABLE IF EXISTS `site_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_visits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `visit_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_id` int unsigned NOT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `assigned_user_id` int unsigned DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `visit_type` enum('Property Visit','Project Visit','Virtual Visit') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Property Visit',
  `status` enum('Scheduled','Confirmed','Completed','Cancelled','No Show','Rescheduled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Scheduled',
  `visitor_count` int unsigned NOT NULL DEFAULT '1',
  `rating` int unsigned DEFAULT NULL,
  `interest_level` enum('High','Medium','Low','Not Interested') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `agent_observation` text COLLATE utf8mb4_unicode_ci,
  `preferred_unit` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_feedback` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_action` enum('Follow-up','Negotiation','Alternative Property','Token Discussion','Lost') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visit_code` (`visit_code`),
  KEY `site_visits_lead_id_foreign` (`lead_id`),
  KEY `site_visits_project_id_foreign` (`project_id`),
  KEY `site_visits_property_id_foreign` (`property_id`),
  KEY `site_visits_property_unit_id_foreign` (`property_unit_id`),
  KEY `site_visits_assigned_user_id_foreign` (`assigned_user_id`),
  KEY `scheduled_at` (`scheduled_at`),
  KEY `status` (`status`),
  CONSTRAINT `site_visits_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `site_visits_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `site_visits_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `site_visits_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `site_visits_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unit_holds`
--

DROP TABLE IF EXISTS `unit_holds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_holds` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hold_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_id` int unsigned NOT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `property_unit_id` int unsigned NOT NULL,
  `held_by` int unsigned NOT NULL,
  `hold_status` enum('Active','Expired','Released','Converted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `hold_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `released_at` datetime DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hold_code` (`hold_code`),
  KEY `unit_holds_lead_id_foreign` (`lead_id`),
  KEY `unit_holds_property_id_foreign` (`property_id`),
  KEY `unit_holds_property_unit_id_foreign` (`property_unit_id`),
  KEY `unit_holds_held_by_foreign` (`held_by`),
  KEY `hold_status` (`hold_status`),
  KEY `expires_at` (`expires_at`),
  CONSTRAINT `unit_holds_held_by_foreign` FOREIGN KEY (`held_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `unit_holds_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `unit_holds_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `unit_holds_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
