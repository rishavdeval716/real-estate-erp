-- ==============================================================================
-- Migration: 2026-10-01-000021_CreatePaymentSchedulesAndItemsTables
-- Converted from PHP migration to MySQL DDL
-- Target tables: payment_schedules, payment_schedule_items
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

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

SET FOREIGN_KEY_CHECKS = 1;
