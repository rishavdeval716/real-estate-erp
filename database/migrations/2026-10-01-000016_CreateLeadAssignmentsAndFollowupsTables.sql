-- ==============================================================================
-- Migration: 2026-10-01-000016_CreateLeadAssignmentsAndFollowupsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: lead_assignments, lead_followups
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `lead_assignments`
--

DROP TABLE IF EXISTS `lead_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_assignments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int unsigned NOT NULL,
  `assigned_to` int unsigned NOT NULL,
  `assigned_by` int unsigned DEFAULT NULL,
  `assignment_type` enum('Initial','Reassignment') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Initial',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_assignments_lead_id_foreign` (`lead_id`),
  KEY `lead_assignments_assigned_to_foreign` (`assigned_to`),
  KEY `lead_assignments_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `lead_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `lead_assignments_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `lead_assignments_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_followups`
--

DROP TABLE IF EXISTS `lead_followups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_followups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int unsigned NOT NULL,
  `assigned_to` int unsigned NOT NULL,
  `followup_type` enum('Phone Call','WhatsApp','Email','Meeting','Site Visit','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Phone Call',
  `scheduled_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `status` enum('Pending','Completed','Cancelled','Missed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `outcome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `next_followup_at` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_followups_lead_id_foreign` (`lead_id`),
  KEY `lead_followups_assigned_to_foreign` (`assigned_to`),
  KEY `lead_followups_created_by_foreign` (`created_by`),
  KEY `status` (`status`),
  KEY `scheduled_at` (`scheduled_at`),
  KEY `next_followup_at` (`next_followup_at`),
  CONSTRAINT `lead_followups_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `lead_followups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `lead_followups_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
