-- ==============================================================================
-- Migration: 2026-10-01-000029_CreateConstructionAndHandoverTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: construction_milestones, daily_site_logs, contractors, construction_work_orders, material_requisitions, site_inspections, handover_certificates
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `construction_milestones`
--

DROP TABLE IF EXISTS `construction_milestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `construction_milestones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `milestone_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int unsigned NOT NULL,
  `tower_id` int unsigned DEFAULT NULL,
  `milestone_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stage_order` int NOT NULL DEFAULT '1',
  `weightage_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `target_start_date` date DEFAULT NULL,
  `target_completion_date` date DEFAULT NULL,
  `actual_completion_date` date DEFAULT NULL,
  `progress_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('Not Started','In Progress','Under Review','Completed','Delayed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Not Started',
  `verified_by` int unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `milestone_code` (`milestone_code`),
  KEY `project_id` (`project_id`),
  KEY `tower_id` (`tower_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_site_logs`
--

DROP TABLE IF EXISTS `daily_site_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_site_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `log_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_date` date NOT NULL,
  `project_id` int unsigned NOT NULL,
  `tower_id` int unsigned DEFAULT NULL,
  `milestone_id` int unsigned DEFAULT NULL,
  `skilled_workers` int NOT NULL DEFAULT '0',
  `unskilled_workers` int NOT NULL DEFAULT '0',
  `weather_condition` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sunny',
  `work_completed` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `materials_used` text COLLATE utf8mb4_unicode_ci,
  `equipment_deployed` text COLLATE utf8mb4_unicode_ci,
  `delays_or_impediments` text COLLATE utf8mb4_unicode_ci,
  `logged_by` int unsigned NOT NULL,
  `approved_by` int unsigned DEFAULT NULL,
  `status` enum('Draft','Submitted','Approved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Submitted',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `log_code` (`log_code`),
  KEY `project_id` (`project_id`),
  KEY `log_date` (`log_date`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contractors`
--

DROP TABLE IF EXISTS `contractors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contractors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `contractor_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialization` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gstin` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` decimal(3,1) NOT NULL DEFAULT '5.0',
  `status` enum('Active','Inactive','Blacklisted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contractor_code` (`contractor_code`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `construction_work_orders`
--

DROP TABLE IF EXISTS `construction_work_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `construction_work_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `work_order_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contractor_id` int unsigned NOT NULL,
  `project_id` int unsigned NOT NULL,
  `tower_id` int unsigned DEFAULT NULL,
  `milestone_id` int unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope_of_work` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `contract_amount` decimal(12,2) NOT NULL,
  `retention_percentage` decimal(5,2) NOT NULL DEFAULT '5.00',
  `start_date` date NOT NULL,
  `completion_date` date NOT NULL,
  `payment_terms` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Draft','Awarded','In Progress','Under Inspection','Completed','Terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Awarded',
  `created_by` int unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `work_order_code` (`work_order_code`),
  KEY `contractor_id` (`contractor_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `material_requisitions`
--

DROP TABLE IF EXISTS `material_requisitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_requisitions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `requisition_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int unsigned NOT NULL,
  `tower_id` int unsigned DEFAULT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_of_measure` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimated_unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estimated_total_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `required_by_date` date NOT NULL,
  `priority` enum('Low','Medium','High','Urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Medium',
  `requested_by` int unsigned NOT NULL,
  `status` enum('Requested','Approved','Procured','Rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Requested',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `requisition_code` (`requisition_code`),
  KEY `project_id` (`project_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_inspections`
--

DROP TABLE IF EXISTS `site_inspections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_inspections` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `inspection_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int unsigned NOT NULL,
  `tower_id` int unsigned DEFAULT NULL,
  `unit_id` int unsigned DEFAULT NULL,
  `inspection_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inspection_date` date NOT NULL,
  `inspector_id` int unsigned NOT NULL,
  `result` enum('Passed','Failed','Conditional Pass') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Passed',
  `snags_found` int NOT NULL DEFAULT '0',
  `snag_details` text COLLATE utf8mb4_unicode_ci,
  `rectification_deadline` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Scheduled','Completed','Action Required','Rectified & Closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Completed',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inspection_code` (`inspection_code`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `handover_certificates`
--

DROP TABLE IF EXISTS `handover_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `handover_certificates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `certificate_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `property_unit_id` int unsigned NOT NULL,
  `handover_date` date NOT NULL,
  `financial_clearance` tinyint(1) NOT NULL DEFAULT '1',
  `snagging_clearance` tinyint(1) NOT NULL DEFAULT '1',
  `occupancy_certificate_ref` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `electricity_meter_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `initial_electricity_reading` decimal(10,2) NOT NULL DEFAULT '0.00',
  `water_meter_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `initial_water_reading` decimal(10,2) NOT NULL DEFAULT '0.00',
  `key_sets_provided` int NOT NULL DEFAULT '2',
  `customer_acknowledged` tinyint(1) NOT NULL DEFAULT '1',
  `authorized_by` int unsigned NOT NULL,
  `status` enum('Draft','Ready for Handover','Handed Over','Archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Handed Over',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificate_number` (`certificate_number`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  KEY `property_unit_id` (`property_unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
