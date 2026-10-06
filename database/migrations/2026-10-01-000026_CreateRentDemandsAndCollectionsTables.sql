-- ==============================================================================
-- Migration: 2026-10-01-000026_CreateRentDemandsAndCollectionsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: rent_demands, rent_collections
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

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

SET FOREIGN_KEY_CHECKS = 1;
