-- ==============================================================================
-- Migration: 2026-10-01-000007_CreateLocationsTable
-- Converted from PHP migration to MySQL DDL
-- Target tables: locations
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locality` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `landmark` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nearby_locations` text COLLATE utf8mb4_unicode_ci,
  `map_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `city_area` (`city`,`area`),
  KEY `pincode` (`pincode`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
