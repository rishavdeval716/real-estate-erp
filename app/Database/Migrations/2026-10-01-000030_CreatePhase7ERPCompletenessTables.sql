-- ==============================================================================
-- Migration: 2026-10-01-000030_CreatePhase7ERPCompletenessTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: property_owners, agents, expense_categories, property_expenses, marketing_campaigns, property_documents, property_verifications, notifications, customer_communications, system_settings
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

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

SET FOREIGN_KEY_CHECKS = 1;
