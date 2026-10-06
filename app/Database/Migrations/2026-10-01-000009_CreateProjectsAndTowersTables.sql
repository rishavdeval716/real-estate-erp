-- ==============================================================================
-- Migration: 2026-10-01-000009_CreateProjectsAndTowersTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: projects, project_towers
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `builder_developer` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` int unsigned NOT NULL,
  `construction_status` enum('Pre-Launch','Under Construction','Ready to Move','Completed','On Hold') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Under Construction',
  `possession_date` date DEFAULT NULL,
  `total_units` int unsigned NOT NULL DEFAULT '0',
  `available_units` int unsigned NOT NULL DEFAULT '0',
  `status` enum('active','inactive','completed','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_code` (`project_code`),
  KEY `location_id` (`location_id`),
  KEY `construction_status` (`construction_status`),
  CONSTRAINT `projects_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_towers`
--

DROP TABLE IF EXISTS `project_towers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_towers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int unsigned NOT NULL,
  `tower_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tower_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_of_floors` int unsigned NOT NULL DEFAULT '1',
  `total_units` int unsigned NOT NULL DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_tower_code` (`project_id`,`tower_code`),
  CONSTRAINT `project_towers_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
