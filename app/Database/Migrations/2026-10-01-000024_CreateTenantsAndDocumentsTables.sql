-- ==============================================================================
-- Migration: 2026-10-01-000024_CreateTenantsAndDocumentsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: tenants, tenant_documents
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

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

SET FOREIGN_KEY_CHECKS = 1;
