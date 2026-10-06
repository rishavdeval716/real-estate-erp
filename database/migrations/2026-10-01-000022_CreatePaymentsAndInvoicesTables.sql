-- ==============================================================================
-- Migration: 2026-10-01-000022_CreatePaymentsAndInvoicesTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: payments, invoices, receipts
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

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

SET FOREIGN_KEY_CHECKS = 1;
