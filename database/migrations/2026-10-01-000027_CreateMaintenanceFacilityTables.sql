-- ==============================================================================
-- Migration: 2026-10-01-000027_CreateMaintenanceFacilityTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: facility_assets, technicians, sla_rules, maintenance_requests, complaints, preventive_maintenance, cam_charges
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `facility_assets`
--

DROP TABLE IF EXISTS `facility_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facility_assets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `asset_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('elevators','generators','fire_safety','water_treatment','electrical','hvac','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `property_id` int unsigned NOT NULL,
  `location_details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `installation_date` date DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `status` enum('operational','under_maintenance','decommissioned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operational',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_code` (`asset_code`),
  KEY `facility_assets_property_id_foreign` (`property_id`),
  KEY `status` (`status`),
  CONSTRAINT `facility_assets_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `technicians`
--

DROP TABLE IF EXISTS `technicians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `technicians` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `technician_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skill` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `availability` enum('available','busy','on_leave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `technician_code` (`technician_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sla_rules`
--

DROP TABLE IF EXISTS `sla_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sla_rules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `response_time_hours` int NOT NULL DEFAULT '4',
  `resolution_time_hours` int NOT NULL DEFAULT '24',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_priority` (`category`,`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maintenance_requests`
--

DROP TABLE IF EXISTS `maintenance_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenance_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` int unsigned DEFAULT NULL,
  `property_id` int unsigned NOT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `asset_id` int unsigned DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subcategory` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `assigned_technician_id` int unsigned DEFAULT NULL,
  `sla_due_date` datetime DEFAULT NULL,
  `status` enum('open','assigned','in_progress','on_hold','resolved','closed','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `resolution` text COLLATE utf8mb4_unicode_ci,
  `closed_date` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `maintenance_requests_tenant_id_foreign` (`tenant_id`),
  KEY `maintenance_requests_property_id_foreign` (`property_id`),
  KEY `maintenance_requests_property_unit_id_foreign` (`property_unit_id`),
  KEY `maintenance_requests_asset_id_foreign` (`asset_id`),
  KEY `maintenance_requests_assigned_technician_id_foreign` (`assigned_technician_id`),
  KEY `maintenance_requests_created_by_foreign` (`created_by`),
  KEY `status` (`status`),
  KEY `priority` (`priority`),
  CONSTRAINT `maintenance_requests_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `facility_assets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `maintenance_requests_assigned_technician_id_foreign` FOREIGN KEY (`assigned_technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `maintenance_requests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `maintenance_requests_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `maintenance_requests_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `maintenance_requests_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `complaints` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `complaint_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `complaint_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_id` int unsigned NOT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `tenant_id` int unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `assigned_user_id` int unsigned DEFAULT NULL,
  `status` enum('submitted','in_review','in_progress','resolved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `resolution` text COLLATE utf8mb4_unicode_ci,
  `feedback_rating` int DEFAULT NULL,
  `feedback_comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `complaint_code` (`complaint_code`),
  KEY `complaints_property_id_foreign` (`property_id`),
  KEY `complaints_property_unit_id_foreign` (`property_unit_id`),
  KEY `complaints_tenant_id_foreign` (`tenant_id`),
  KEY `complaints_assigned_user_id_foreign` (`assigned_user_id`),
  KEY `status` (`status`),
  CONSTRAINT `complaints_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `complaints_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `complaints_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `complaints_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `preventive_maintenance`
--

DROP TABLE IF EXISTS `preventive_maintenance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `preventive_maintenance` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `schedule_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asset_id` int unsigned NOT NULL,
  `maintenance_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency` enum('daily','weekly','monthly','quarterly','semi_annual','annual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `last_service_date` date DEFAULT NULL,
  `next_service_date` date NOT NULL,
  `assigned_technician_id` int unsigned DEFAULT NULL,
  `status` enum('scheduled','completed','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schedule_code` (`schedule_code`),
  KEY `preventive_maintenance_asset_id_foreign` (`asset_id`),
  KEY `preventive_maintenance_assigned_technician_id_foreign` (`assigned_technician_id`),
  KEY `status` (`status`),
  CONSTRAINT `preventive_maintenance_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `facility_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `preventive_maintenance_assigned_technician_id_foreign` FOREIGN KEY (`assigned_technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cam_charges`
--

DROP TABLE IF EXISTS `cam_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cam_charges` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cam_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_id` int unsigned NOT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `tenant_id` int unsigned DEFAULT NULL,
  `area_sqft` decimal(10,2) NOT NULL,
  `billing_model` enum('per_sqft','flat_rate') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'per_sqft',
  `rate` decimal(10,2) NOT NULL,
  `period` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `status` enum('unbilled','billed','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unbilled',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cam_code` (`cam_code`),
  KEY `cam_charges_property_id_foreign` (`property_id`),
  KEY `cam_charges_property_unit_id_foreign` (`property_unit_id`),
  KEY `cam_charges_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `cam_charges_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `cam_charges_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `cam_charges_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
