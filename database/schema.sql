-- ==============================================================================
-- Real Estate ERP — Complete Database Schema (DDL)
-- Contains 74 application tables + 1 migrations table
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `agents`
--

DROP TABLE IF EXISTS `agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `agent_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `agent_type` enum('Internal Agent','External Broker','Channel Partner','Agency') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'External Broker',
  `agency_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pan_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '2.00',
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agent_code` (`agent_code`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amenities`
--

DROP TABLE IF EXISTS `amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amenities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` int unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `module_action` (`module`,`action`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1489 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `booking_status_history`
--

DROP TABLE IF EXISTS `booking_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_status_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int unsigned NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` int unsigned DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_status_history_changed_by_foreign` (`changed_by`),
  KEY `booking_id` (`booking_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `booking_status_history_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `booking_status_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `booking_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `lead_id` int unsigned DEFAULT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `property_unit_id` int unsigned NOT NULL,
  `sales_executive_id` int unsigned DEFAULT NULL,
  `booking_date` date NOT NULL,
  `booking_status` enum('Draft','Pending Confirmation','Confirmed','Cancelled','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `base_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `final_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `token_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `booking_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_number` (`booking_number`),
  KEY `bookings_created_by_foreign` (`created_by`),
  KEY `customer_id` (`customer_id`),
  KEY `lead_id` (`lead_id`),
  KEY `project_id` (`project_id`),
  KEY `property_id` (`property_id`),
  KEY `property_unit_id` (`property_unit_id`),
  KEY `sales_executive_id` (`sales_executive_id`),
  KEY `booking_status` (`booking_status`),
  KEY `booking_date` (`booking_date`),
  CONSTRAINT `bookings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `bookings_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bookings_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bookings_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bookings_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `bookings_sales_executive_id_foreign` FOREIGN KEY (`sales_executive_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manager_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'India',
  `pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `branches_company_id_foreign` (`company_id`),
  CONSTRAINT `branches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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

--
-- Table structure for table `commission_rules`
--

DROP TABLE IF EXISTS `commission_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commission_rules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission_type` enum('Percentage','Fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Percentage',
  `commission_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `applicable_to` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'All',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `commissions`
--

DROP TABLE IF EXISTS `commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int unsigned NOT NULL,
  `agent_user_id` int unsigned NOT NULL,
  `commission_rule_id` int unsigned DEFAULT NULL,
  `booking_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `commission_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('Pending','Approved','Paid','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `payable_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commissions_commission_rule_id_foreign` (`commission_rule_id`),
  KEY `booking_id` (`booking_id`),
  KEY `agent_user_id` (`agent_user_id`),
  KEY `status` (`status`),
  CONSTRAINT `commissions_agent_user_id_foreign` FOREIGN KEY (`agent_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `commissions_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `commissions_commission_rule_id_foreign` FOREIGN KEY (`commission_rule_id`) REFERENCES `commission_rules` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alternate_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'India',
  `pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
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
-- Table structure for table `customer_communications`
--

DROP TABLE IF EXISTS `customer_communications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_communications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `comm_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `lead_id` int unsigned DEFAULT NULL,
  `channel` enum('Phone Call','WhatsApp','Email','SMS','In-Person Meeting','Video Call','Letter / Notice') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Phone Call',
  `purpose` enum('Follow-up','Property Enquiry','Site Visit Reminder','Booking Confirmation','Payment Reminder','Payment Receipt','Rent Reminder','Agreement Execution','Document Request','Grievance Resolution','General Update') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Follow-up',
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Planned','Sent','Delivered','Completed','Failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Completed',
  `communication_date` datetime NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `response_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comm_code` (`comm_code`),
  KEY `customer_id` (`customer_id`),
  KEY `lead_id` (`lead_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_documents`
--

DROP TABLE IF EXISTS `customer_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_documents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `document_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_status` enum('Pending','Verified','Rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `verified_by` int unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_documents_verified_by_foreign` (`verified_by`),
  KEY `customer_id` (`customer_id`),
  KEY `document_type` (`document_type`),
  KEY `verification_status` (`verification_status`),
  CONSTRAINT `customer_documents_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `customer_documents_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_id` int unsigned DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternate_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_proof_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_proof_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kyc_status` enum('Pending','Verified','Rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_code` (`customer_code`),
  KEY `phone` (`phone`),
  KEY `email` (`email`),
  KEY `kyc_status` (`kyc_status`),
  KEY `status` (`status`),
  KEY `lead_id` (`lead_id`),
  CONSTRAINT `customers_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('Draft','Issued','Partially Paid','Paid','Cancelled','Overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  KEY `status` (`status`),
  KEY `due_date` (`due_date`),
  CONSTRAINT `invoices_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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

--
-- Table structure for table `lead_sources`
--

DROP TABLE IF EXISTS `lead_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_sources` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `lease_agreements`
--

DROP TABLE IF EXISTS `lease_agreements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lease_agreements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `agreement_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` int unsigned NOT NULL,
  `property_id` int unsigned NOT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `agreement_type` enum('residential','commercial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'residential',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `lock_in_period_months` int NOT NULL DEFAULT '0',
  `notice_period_days` int NOT NULL DEFAULT '30',
  `monthly_rent` decimal(12,2) NOT NULL DEFAULT '0.00',
  `security_deposit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `maintenance_charges` decimal(12,2) NOT NULL DEFAULT '0.00',
  `rent_escalation_pct` decimal(5,2) NOT NULL DEFAULT '5.00',
  `escalation_frequency` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'annual',
  `payment_due_day` int NOT NULL DEFAULT '5',
  `late_fee_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `terms_conditions` text COLLATE utf8mb4_unicode_ci,
  `document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','active','expiring_soon','expired','terminated','renewed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agreement_number` (`agreement_number`),
  KEY `lease_agreements_tenant_id_foreign` (`tenant_id`),
  KEY `lease_agreements_property_id_foreign` (`property_id`),
  KEY `lease_agreements_property_unit_id_foreign` (`property_unit_id`),
  KEY `lease_agreements_created_by_foreign` (`created_by`),
  KEY `status` (`status`),
  KEY `start_date_end_date` (`start_date`,`end_date`),
  CONSTRAINT `lease_agreements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `lease_agreements_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `lease_agreements_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `lease_agreements_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `marketing_campaigns`
--

DROP TABLE IF EXISTS `marketing_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marketing_campaigns` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `campaign_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_type` enum('Social Media','Google Ads','Property Portal','Print Media','Hoarding / Outdoor','Email Marketing','SMS Campaign','Event / Expo','Referral Program','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Social Media',
  `project_id` int unsigned DEFAULT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(15,2) NOT NULL DEFAULT '0.00',
  `actual_spend` decimal(15,2) NOT NULL DEFAULT '0.00',
  `leads_generated` int unsigned NOT NULL DEFAULT '0',
  `qualified_leads` int unsigned NOT NULL DEFAULT '0',
  `converted_leads` int unsigned NOT NULL DEFAULT '0',
  `status` enum('Planning','Active','Paused','Completed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `description` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_code` (`campaign_code`),
  KEY `project_id` (`project_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `role_id` int unsigned DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('lead','site_visit','followup','payment_due','overdue_payment','rent_due','agreement_expiry','booking','document_expiry','maintenance','system') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `related_module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` int unsigned DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_schedule_items`
--

DROP TABLE IF EXISTS `payment_schedule_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_schedule_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_schedule_id` int unsigned NOT NULL,
  `milestone_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` date DEFAULT NULL,
  `percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('Pending','Partially Paid','Paid','Overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_schedule_id` (`payment_schedule_id`),
  KEY `status` (`status`),
  KEY `due_date` (`due_date`),
  CONSTRAINT `payment_schedule_items_payment_schedule_id_foreign` FOREIGN KEY (`payment_schedule_id`) REFERENCES `payment_schedules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_schedules`
--

DROP TABLE IF EXISTS `payment_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int unsigned NOT NULL,
  `schedule_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Standard Construction Linked Plan',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `payment_schedules_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `payment_schedule_item_id` int unsigned DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('Cash','Bank Transfer','NEFT','RTGS','IMPS','UPI','Cheque','Online Gateway','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bank Transfer',
  `transaction_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cheque_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Pending','Received','Failed','Cancelled','Refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Received',
  `received_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_number` (`payment_number`),
  KEY `payments_received_by_foreign` (`received_by`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  KEY `payment_schedule_item_id` (`payment_schedule_item_id`),
  KEY `status` (`status`),
  KEY `payment_date` (`payment_date`),
  CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `payments_payment_schedule_item_id_foreign` FOREIGN KEY (`payment_schedule_item_id`) REFERENCES `payment_schedule_items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `portal_requests`
--

DROP TABLE IF EXISTS `portal_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `portal_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `request_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `portal_type` enum('customer','tenant','partner') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `request_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('submitted','in_progress','approved','rejected','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_code` (`request_code`),
  KEY `portal_requests_user_id_foreign` (`user_id`),
  KEY `portal_type_user_id` (`portal_type`,`user_id`),
  CONSTRAINT `portal_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
-- Table structure for table `property_documents`
--

DROP TABLE IF EXISTS `property_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_documents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `document_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_category` enum('Ownership / Title Deed','Registry Document','Property Tax Receipt','NOC Clearance','Building Approval Plan','Occupancy Certificate','RERA Certificate','Encumbrance Certificate','Legal Opinion','KYC Document','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ownership / Title Deed',
  `property_id` int unsigned DEFAULT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `owner_id` int unsigned DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int unsigned NOT NULL DEFAULT '0',
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'application/pdf',
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `verification_status` enum('Pending','Under Review','Verified','Rejected','Expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `verified_by` int unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `uploaded_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_code` (`document_code`),
  KEY `property_id` (`property_id`),
  KEY `owner_id` (`owner_id`),
  KEY `verification_status` (`verification_status`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `property_expenses`
--

DROP TABLE IF EXISTS `property_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_expenses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `expense_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int unsigned NOT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_date` date NOT NULL,
  `payee_vendor` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL,
  `payment_method` enum('Cash','Bank Transfer','Cheque','UPI','Credit Card','NEFT','RTGS','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bank Transfer',
  `status` enum('Draft','Pending Approval','Approved','Paid','Rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Approved',
  `receipt_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `approved_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_code` (`expense_code`),
  KEY `category_id` (`category_id`),
  KEY `property_id` (`property_id`),
  KEY `project_id` (`project_id`),
  KEY `expense_date` (`expense_date`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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

--
-- Table structure for table `property_owners`
--

DROP TABLE IF EXISTS `property_owners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_owners` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `owner_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternate_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pan_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhaar_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kyc_status` enum('pending','verified','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_ifsc` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `owner_code` (`owner_code`),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `property_pricing`
--

DROP TABLE IF EXISTS `property_pricing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_pricing` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `property_id` int unsigned NOT NULL,
  `unit_id` int unsigned DEFAULT NULL,
  `base_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `price_per_sqft` decimal(15,2) DEFAULT NULL,
  `market_price` decimal(15,2) DEFAULT NULL,
  `negotiated_price` decimal(15,2) DEFAULT NULL,
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `property_pricing_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `property_pricing_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `property_units` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `property_status_history`
--

DROP TABLE IF EXISTS `property_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_status_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `property_id` int unsigned DEFAULT NULL,
  `unit_id` int unsigned DEFAULT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` int unsigned DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_status_history_changed_by_foreign` (`changed_by`),
  KEY `property_id` (`property_id`),
  KEY `unit_id` (`unit_id`),
  KEY `new_status` (`new_status`),
  CONSTRAINT `property_status_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `property_status_history_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `property_status_history_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `property_units` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `property_types`
--

DROP TABLE IF EXISTS `property_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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

--
-- Table structure for table `property_verifications`
--

DROP TABLE IF EXISTS `property_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_verifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `verification_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_id` int unsigned NOT NULL,
  `verification_type` enum('Legal Title Clearance','Physical Property Audit','RERA Compliance Check','Municipal Approval Check','Structural & Fire Safety','Tax Compliance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Legal Title Clearance',
  `status` enum('Pending','Under Review','Verified','Rejected','Expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `assigned_to` int unsigned DEFAULT NULL,
  `checklist_data` json DEFAULT NULL,
  `findings` text COLLATE utf8mb4_unicode_ci,
  `verified_by` int unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `expiry_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `verification_code` (`verification_code`),
  KEY `property_id` (`property_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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

--
-- Table structure for table `receipts`
--

DROP TABLE IF EXISTS `receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receipts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_id` int unsigned NOT NULL,
  `booking_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `receipt_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_number` (`receipt_number`),
  KEY `payment_id` (`payment_id`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  KEY `receipt_date` (`receipt_date`),
  CONSTRAINT `receipts_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `receipts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `receipts_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rent_collections`
--

DROP TABLE IF EXISTS `rent_collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rent_collections` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `collection_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rent_demand_id` int unsigned NOT NULL,
  `tenant_id` int unsigned NOT NULL,
  `lease_id` int unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('Cash','Bank Transfer','NEFT','RTGS','IMPS','UPI','Cheque','Online Gateway','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bank Transfer',
  `transaction_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `received_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collection_number` (`collection_number`),
  KEY `rent_collections_rent_demand_id_foreign` (`rent_demand_id`),
  KEY `rent_collections_tenant_id_foreign` (`tenant_id`),
  KEY `rent_collections_lease_id_foreign` (`lease_id`),
  KEY `rent_collections_received_by_foreign` (`received_by`),
  CONSTRAINT `rent_collections_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `lease_agreements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rent_collections_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `rent_collections_rent_demand_id_foreign` FOREIGN KEY (`rent_demand_id`) REFERENCES `rent_demands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rent_collections_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rent_demands`
--

DROP TABLE IF EXISTS `rent_demands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rent_demands` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `demand_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` int unsigned NOT NULL,
  `lease_id` int unsigned NOT NULL,
  `property_id` int unsigned NOT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `billing_period` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_rent` decimal(12,2) NOT NULL,
  `maintenance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `other_charges` decimal(12,2) NOT NULL DEFAULT '0.00',
  `late_fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance_amount` decimal(12,2) NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('unpaid','partially_paid','paid','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `generated_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `demand_number` (`demand_number`),
  UNIQUE KEY `lease_id_billing_period` (`lease_id`,`billing_period`),
  KEY `rent_demands_tenant_id_foreign` (`tenant_id`),
  KEY `rent_demands_property_id_foreign` (`property_id`),
  KEY `rent_demands_property_unit_id_foreign` (`property_unit_id`),
  KEY `rent_demands_generated_by_foreign` (`generated_by`),
  KEY `status` (`status`),
  KEY `due_date` (`due_date`),
  CONSTRAINT `rent_demands_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `rent_demands_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `lease_agreements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rent_demands_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `rent_demands_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `rent_demands_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rental_histories`
--

DROP TABLE IF EXISTS `rental_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rental_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int unsigned NOT NULL,
  `property_id` int unsigned NOT NULL,
  `property_unit_id` int unsigned DEFAULT NULL,
  `lease_id` int unsigned DEFAULT NULL,
  `previous_rent` decimal(12,2) DEFAULT NULL,
  `current_rent` decimal(12,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rental_histories_property_id_foreign` (`property_id`),
  KEY `rental_histories_property_unit_id_foreign` (`property_unit_id`),
  KEY `rental_histories_lease_id_foreign` (`lease_id`),
  KEY `tenant_id` (`tenant_id`),
  CONSTRAINT `rental_histories_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `lease_agreements` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `rental_histories_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `rental_histories_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `rental_histories_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int unsigned NOT NULL,
  `permission_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_id_permission_id` (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=690 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sales_agreements`
--

DROP TABLE IF EXISTS `sales_agreements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_agreements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `agreement_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `project_id` int unsigned DEFAULT NULL,
  `property_id` int unsigned DEFAULT NULL,
  `property_unit_id` int unsigned NOT NULL,
  `agreement_date` date NOT NULL,
  `agreement_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sale Agreement',
  `agreement_status` enum('Draft','Pending Signature','Signed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `total_value` decimal(15,2) NOT NULL DEFAULT '0.00',
  `terms_conditions` text COLLATE utf8mb4_unicode_ci,
  `special_conditions` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agreement_number` (`agreement_number`),
  KEY `sales_agreements_created_by_foreign` (`created_by`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  KEY `project_id` (`project_id`),
  KEY `property_id` (`property_id`),
  KEY `property_unit_id` (`property_unit_id`),
  KEY `agreement_status` (`agreement_status`),
  KEY `agreement_date` (`agreement_date`),
  CONSTRAINT `sales_agreements_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `sales_agreements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sales_agreements_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `sales_agreements_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sales_agreements_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sales_agreements_property_unit_id_foreign` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `security_deposits`
--

DROP TABLE IF EXISTS `security_deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `security_deposits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `deposit_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` int unsigned NOT NULL,
  `lease_id` int unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `deposit_date` date NOT NULL,
  `refundable_amount` decimal(12,2) NOT NULL,
  `adjusted_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `refund_date` date DEFAULT NULL,
  `refund_status` enum('held','partially_refunded','refunded','forfeited') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'held',
  `adjustment_reason` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deposit_number` (`deposit_number`),
  KEY `security_deposits_tenant_id_foreign` (`tenant_id`),
  KEY `security_deposits_lease_id_foreign` (`lease_id`),
  KEY `refund_status` (`refund_status`),
  CONSTRAINT `security_deposits_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `lease_agreements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_deposits_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `setting_group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `setting_group` (`setting_group`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tds_certificates`
--

DROP TABLE IF EXISTS `tds_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tds_certificates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `certificate_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `party_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pan_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_amount` decimal(12,2) NOT NULL,
  `tds_amount` decimal(12,2) NOT NULL,
  `financial_year` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quarter` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `certificate_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` date NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificate_number` (`certificate_number`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tds_entries`
--

DROP TABLE IF EXISTS `tds_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tds_entries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `entry_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `party_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pan_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '194H',
  `transaction_reference` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission_id` int unsigned DEFAULT NULL,
  `gross_amount` decimal(12,2) NOT NULL,
  `tds_rate` decimal(5,2) NOT NULL DEFAULT '5.00',
  `tds_amount` decimal(12,2) NOT NULL,
  `net_payable` decimal(12,2) NOT NULL,
  `deduction_date` date NOT NULL,
  `financial_year` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quarter` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('deducted','deposited','certified') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'deducted',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_code` (`entry_code`),
  KEY `tds_entries_commission_id_foreign` (`commission_id`),
  KEY `pan_number` (`pan_number`),
  KEY `status` (`status`),
  CONSTRAINT `tds_entries_commission_id_foreign` FOREIGN KEY (`commission_id`) REFERENCES `commissions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
-- Table structure for table `tenant_documents`
--

DROP TABLE IF EXISTS `tenant_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_documents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int unsigned NOT NULL,
  `document_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_status` enum('pending','verified','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `verified_by` int unsigned DEFAULT NULL,
  `verification_date` datetime DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_documents_verified_by_foreign` (`verified_by`),
  KEY `tenant_id` (`tenant_id`),
  KEY `verification_status` (`verification_status`),
  CONSTRAINT `tenant_documents_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tenant_documents_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tenant_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_type` enum('individual','company') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'individual',
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_proof_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_proof_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kyc_status` enum('pending','verified','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `occupied_property_id` int unsigned DEFAULT NULL,
  `occupied_unit_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `lease_start_date` date DEFAULT NULL,
  `lease_end_date` date DEFAULT NULL,
  `status` enum('active','inactive','blacklisted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_code` (`tenant_code`),
  KEY `tenants_occupied_property_id_foreign` (`occupied_property_id`),
  KEY `tenants_occupied_unit_id_foreign` (`occupied_unit_id`),
  KEY `tenants_user_id_foreign` (`user_id`),
  KEY `mobile` (`mobile`),
  KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `kyc_status` (`kyc_status`),
  CONSTRAINT `tenants_occupied_property_id_foreign` FOREIGN KEY (`occupied_property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `tenants_occupied_unit_id_foreign` FOREIGN KEY (`occupied_unit_id`) REFERENCES `property_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `tenants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_role_id` (`user_id`,`role_id`),
  KEY `user_roles_role_id_foreign` (`role_id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int unsigned DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `users_branch_id_foreign` (`branch_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET FOREIGN_KEY_CHECKS = 1;
