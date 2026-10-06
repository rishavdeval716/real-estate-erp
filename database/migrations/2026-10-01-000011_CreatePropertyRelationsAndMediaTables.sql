-- ==============================================================================
-- Migration: 2026-10-01-000011_CreatePropertyRelationsAndMediaTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: property_amenities, property_media
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `property_amenities`
--

DROP TABLE IF EXISTS `property_amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_amenities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `property_id` int unsigned NOT NULL,
  `amenity_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `property_id_amenity_id` (`property_id`,`amenity_id`),
  KEY `property_amenities_amenity_id_foreign` (`amenity_id`),
  CONSTRAINT `property_amenities_amenity_id_foreign` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `property_amenities_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `property_media`
--

DROP TABLE IF EXISTS `property_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_media` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `property_id` int unsigned NOT NULL,
  `media_type` enum('photo','gallery','video','floor_plan','brochure','virtual_tour','document') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'photo',
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  KEY `media_type` (`media_type`),
  KEY `is_primary` (`is_primary`),
  CONSTRAINT `property_media_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
