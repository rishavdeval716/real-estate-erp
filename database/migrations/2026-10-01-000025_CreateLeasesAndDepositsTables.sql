-- ==============================================================================
-- Migration: 2026-10-01-000025_CreateLeasesAndDepositsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: lease_agreements, security_deposits, rental_histories
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

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

SET FOREIGN_KEY_CHECKS = 1;
