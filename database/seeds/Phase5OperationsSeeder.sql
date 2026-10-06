-- ==============================================================================
-- Seeder: Phase5OperationsSeeder
-- Converted from PHP seeder to MySQL DML
-- Target tables: tenants, tenant_documents, lease_agreements, rental_histories, security_deposits, rent_demands, rent_collections, technicians, facility_assets, maintenance_requests, preventive_maintenance, sla_rules, complaints, cam_charges
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES (1,'TEN-2026-000001','individual','Vikramaditya Birla',NULL,NULL,'9820011223','vikramaditya.birla@example.com','Flat 401, Tower A, Grand Horizon','Mumbai','Maharashtra','400050','PAN Card','ABCDE1234F','verified',1,5,1,'2026-01-01','2026-12-31','active',NULL,'2026-10-01 17:56:00','2026-10-01 17:56:00',NULL),(2,'TEN-2026-000002','company','Nexus Tech Solutions Pvt. Ltd.','Nexus Tech Solutions Pvt. Ltd.','Rohan Deshmukh (Head of Admin)','9820099887','admin@nexustech.example.com','Level 5, Platinum Business Tower','Pune','Maharashtra','411006','Certificate of Incorporation','U72200PN2020PTC123456','verified',2,8,1,'2026-04-01','2029-03-31','active',NULL,'2026-10-01 17:56:00','2026-10-01 17:56:00',NULL),(3,'TEN-2026-000003','individual','Dr. Ananya Sen',NULL,NULL,'9811122334','ananya.sen@example.com','Apartment 102, Garden Heights','Bengaluru','Karnataka','560001','Passport','Z1234567','pending',NULL,NULL,1,NULL,NULL,'active',NULL,'2026-10-01 17:56:00','2026-10-01 17:56:00',NULL),(4,'TEN-2026-000004','individual','Pooja Agarwal 1791008034',NULL,NULL,'9820014348','pooja.1791008034@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA9253Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 11:43:54','2026-10-03 11:43:54',NULL),(5,'TEN-2026-000005','individual','Pooja Agarwal 1791008224',NULL,NULL,'9820077089','pooja.1791008224@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA3807Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 11:47:05','2026-10-03 11:47:05',NULL),(6,'TEN-2026-000006','individual','Pooja Agarwal 1791008700',NULL,NULL,'9820048899','pooja.1791008700@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA6333Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 11:55:01','2026-10-03 11:55:01',NULL),(7,'TEN-2026-000007','individual','Pooja Agarwal 1791009864',NULL,NULL,'9820011499','pooja.1791009864@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA9532Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 12:14:25','2026-10-03 12:14:25',NULL),(8,'TEN-2026-000008','individual','Pooja Agarwal 1791011365',NULL,NULL,'9820041698','pooja.1791011365@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA3972Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 12:39:25','2026-10-03 12:39:25',NULL),(9,'TEN-2026-000009','individual','Pooja Agarwal 1791013103',NULL,NULL,'9820015773','pooja.1791013103@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA1700Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 13:08:23','2026-10-03 13:08:23',NULL),(10,'TEN-2026-000010','individual','Pooja Agarwal 1791014104',NULL,NULL,'9820079349','pooja.1791014104@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA1325Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 13:25:04','2026-10-03 13:25:04',NULL),(11,'TEN-2026-000011','individual','Pooja Agarwal 1791017099',NULL,NULL,'9820085064','pooja.1791017099@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA9356Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 14:14:59','2026-10-03 14:14:59',NULL),(12,'TEN-2026-000012','individual','Pooja Agarwal 1791017722',NULL,NULL,'9820083746','pooja.1791017722@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA1325Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 14:25:22','2026-10-03 14:25:22',NULL),(13,'TEN-2026-000013','individual','Pooja Agarwal 1791018059',NULL,NULL,'9820095065','pooja.1791018059@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA9348Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 14:30:59','2026-10-03 14:30:59',NULL),(14,'TEN-2026-000014','individual','Pooja Agarwal 1791021287',NULL,NULL,'9820032408','pooja.1791021287@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA4607Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-03 15:24:47','2026-10-03 15:24:47',NULL),(15,'TEN-2026-000015','individual','Pooja Agarwal 1791178428',NULL,NULL,'9820059976','pooja.1791178428@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA8226Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-05 11:03:48','2026-10-05 11:03:48',NULL),(16,'TEN-2026-000016','individual','Pooja Agarwal 1791184108',NULL,NULL,'9820061361','pooja.1791184108@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA1343Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-05 12:38:28','2026-10-05 12:38:28',NULL),(17,'TEN-2026-000017','individual','Pooja Agarwal 1791184714',NULL,NULL,'9820090094','pooja.1791184714@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA8792Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-05 12:48:35','2026-10-05 12:48:35',NULL),(18,'TEN-2026-000018','individual','Pooja Agarwal 1791185784',NULL,NULL,'9820089277','pooja.1791185784@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA2530Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-05 13:06:24','2026-10-05 13:06:24',NULL),(19,'TEN-2026-000019','individual','Pooja Agarwal 1791191225',NULL,NULL,'9820026709','pooja.1791191225@example.com',NULL,NULL,NULL,NULL,'PAN Card','ABCPA8099Z','pending',NULL,NULL,NULL,NULL,NULL,'active',NULL,'2026-10-05 14:37:05','2026-10-05 14:37:05',NULL);
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `tenant_documents`
--

LOCK TABLES `tenant_documents` WRITE;
/*!40000 ALTER TABLE `tenant_documents` DISABLE KEYS */;
INSERT INTO `tenant_documents` VALUES (1,1,'PAN Card','ABCDE1234F','pan_vikramaditya.pdf','kyc/tenants/demo_pan.pdf','verified',1,'2026-10-01 17:56:00',NULL,NULL,'2026-10-01 17:56:00','2026-10-01 17:56:00');
/*!40000 ALTER TABLE `tenant_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `lease_agreements`
--

LOCK TABLES `lease_agreements` WRITE;
/*!40000 ALTER TABLE `lease_agreements` DISABLE KEYS */;
INSERT INTO `lease_agreements` VALUES (1,'LSE-2026-000001',1,1,5,'residential','2026-01-01','2026-12-31',6,30,45000.00,90000.00,5000.00,5.00,'annual',5,500.00,'Standard residential tenancy agreement with 6 months lock-in and 1 month notice.',NULL,'active',1,'2026-10-01 17:56:00','2026-10-01 17:56:00',NULL),(2,'LSE-2026-000002',2,2,8,'commercial','2026-04-01','2029-03-31',12,60,180000.00,540000.00,20000.00,7.50,'annual',1,2000.00,'Commercial lease with 36 months duration, 18% GST applicable.',NULL,'active',1,'2026-10-01 17:56:00','2026-10-01 17:56:00',NULL);
/*!40000 ALTER TABLE `lease_agreements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `rental_histories`
--

LOCK TABLES `rental_histories` WRITE;
/*!40000 ALTER TABLE `rental_histories` DISABLE KEYS */;
INSERT INTO `rental_histories` VALUES (1,1,1,5,1,NULL,45000.00,'2026-01-01','2026-12-31','Active','Initial tenancy commenced.','2026-10-01 17:56:00');
/*!40000 ALTER TABLE `rental_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `security_deposits`
--

LOCK TABLES `security_deposits` WRITE;
/*!40000 ALTER TABLE `security_deposits` DISABLE KEYS */;
INSERT INTO `security_deposits` VALUES (1,'DEP-2026-000001',1,1,90000.00,'2026-01-01',90000.00,0.00,NULL,'held',NULL,'Security deposit received in full via Bank Transfer','2026-10-01 17:56:00','2026-10-01 17:56:00'),(2,'DEP-2026-000002',2,2,540000.00,'2026-04-01',540000.00,0.00,NULL,'held',NULL,'Commercial deposit (3 months rent equivalent)','2026-10-01 17:56:00','2026-10-01 17:56:00');
/*!40000 ALTER TABLE `security_deposits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `rent_demands`
--

LOCK TABLES `rent_demands` WRITE;
/*!40000 ALTER TABLE `rent_demands` DISABLE KEYS */;
INSERT INTO `rent_demands` VALUES (1,'RNT-2026-000001',1,1,1,5,'2026-09',45000.00,5000.00,0.00,0.00,0.00,50000.00,50000.00,0.00,'2026-09-05','paid',1,'2026-09-01 08:00:00','2026-09-04 11:30:00'),(2,'RNT-2026-000002',1,1,1,5,'2026-10',45000.00,5000.00,0.00,0.00,0.00,50000.00,0.00,50000.00,'2026-10-05','unpaid',1,'2026-10-01 17:56:00','2026-10-01 17:56:00'),(3,'RNT-2026-000003',2,2,2,8,'2026-10',180000.00,20000.00,0.00,2000.00,36000.00,238000.00,0.00,238000.00,'2026-10-01','overdue',1,'2026-10-01 17:56:00','2026-10-05 14:37:07');
/*!40000 ALTER TABLE `rent_demands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `rent_collections`
--

LOCK TABLES `rent_collections` WRITE;
/*!40000 ALTER TABLE `rent_collections` DISABLE KEYS */;
INSERT INTO `rent_collections` VALUES (1,'RCL-2026-000001',1,1,1,50000.00,'2026-09-04','NEFT','NEFT-AXIS-99881122','Cleared September rent & maintenance in full.',1,'2026-09-04 11:30:00');
/*!40000 ALTER TABLE `rent_collections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `technicians`
--

LOCK TABLES `technicians` WRITE;
/*!40000 ALTER TABLE `technicians` DISABLE KEYS */;
INSERT INTO `technicians` VALUES (1,'TECH-2026-000001','Rajesh Kumar','9876501111','rajesh.kumar@realestate-erp.local','Licensed Master Electrician','Electrical & Power Systems','available','active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(2,'TECH-2026-000002','Amit Verma','9876502222','amit.verma@realestate-erp.local','HVAC & Central Chiller Specialist','Climate Control & Mechanical','busy','active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(3,'TECH-2026-000003','Suresh Patil','9876503333','suresh.patil@realestate-erp.local','Water Treatment & Piping Engineer','Sanitation & Plumbing','available','active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(4,'TECH-2026-000004','Dinesh Sharma','9876504444','dinesh.sharma@realestate-erp.local','Elevator Automation & VFD Drives','Vertical Transportation','available','active','2026-10-01 17:56:00','2026-10-01 17:56:00');
/*!40000 ALTER TABLE `technicians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `facility_assets`
--

LOCK TABLES `facility_assets` WRITE;
/*!40000 ALTER TABLE `facility_assets` DISABLE KEYS */;
INSERT INTO `facility_assets` VALUES (1,'AST-2026-000001','Passenger Elevator Unit A (13 Passengers)','elevators',1,'Tower 1, Core Lift Lobby','2024-03-15','2027-03-14','operational','2026-10-01 17:56:00','2026-10-01 17:56:00'),(2,'AST-2026-000002','Emergency Diesel Generator (250 kVA Cummins)','generators',1,'Basement 1, Power Substation Room','2023-11-10','2026-11-09','operational','2026-10-01 17:56:00','2026-10-01 17:56:00'),(3,'AST-2026-000003','Automated Fire Sprinkler & Alarm System','fire_safety',1,'Floors 1-20 & Basements','2024-01-20','2028-01-19','operational','2026-10-01 17:56:00','2026-10-01 17:56:00'),(4,'AST-2026-000004','Central Rooftop VRF Air Conditioner Plant','hvac',2,'Rooftop Utility Deck','2023-08-01','2026-07-31','under_maintenance','2026-10-01 17:56:00','2026-10-01 17:56:00');
/*!40000 ALTER TABLE `facility_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `maintenance_requests`
--

LOCK TABLES `maintenance_requests` WRITE;
/*!40000 ALTER TABLE `maintenance_requests` DISABLE KEYS */;
INSERT INTO `maintenance_requests` VALUES (1,'MR-2026-000001',1,1,5,1,'electrical','Breaker Tripping','high','Master circuit breaker in Unit 401 is tripping intermittently during peak load.',NULL,'2026-10-01 13:56:00',1,'2026-10-01 21:56:00','in_progress',NULL,NULL,1,'2026-10-01 17:56:00','2026-10-01 17:56:00');
/*!40000 ALTER TABLE `maintenance_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `preventive_maintenance`
--

LOCK TABLES `preventive_maintenance` WRITE;
/*!40000 ALTER TABLE `preventive_maintenance` DISABLE KEYS */;
INSERT INTO `preventive_maintenance` VALUES (1,'PM-2026-000001',1,'Elevator Monthly Rope & Safety Inspection','monthly','2026-09-06','2026-10-06',4,'scheduled','Check governor speed clamp, door sensors, and counterweight balance.','2026-10-01 17:56:00','2026-10-01 17:56:00'),(2,'PM-2026-000002',2,'Diesel Generator Oil & Filter Replacement','quarterly','2026-07-13','2026-10-11',1,'scheduled','Replace 15W40 lube oil, fuel filter cartridge, and battery check.','2026-10-01 17:56:00','2026-10-01 17:56:00');
/*!40000 ALTER TABLE `preventive_maintenance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `sla_rules`
--

LOCK TABLES `sla_rules` WRITE;
/*!40000 ALTER TABLE `sla_rules` DISABLE KEYS */;
INSERT INTO `sla_rules` VALUES (1,'electrical','urgent',1,4,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(2,'electrical','high',2,8,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(3,'electrical','medium',4,24,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(4,'electrical','low',8,48,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(5,'plumbing','urgent',1,4,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(6,'plumbing','high',2,8,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(7,'plumbing','medium',4,24,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(8,'hvac','high',2,12,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(9,'hvac','medium',4,24,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(10,'elevators','urgent',1,3,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(11,'fire_safety','urgent',1,2,'active','2026-10-01 17:56:00','2026-10-01 17:56:00'),(12,'other','medium',6,36,'active','2026-10-01 17:56:00','2026-10-01 17:56:00');
/*!40000 ALTER TABLE `sla_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
INSERT INTO `complaints` VALUES (1,'CMP-2026-000001','Noise Disturbance',1,5,1,'Ongoing late night construction noise from adjacent development site exceeding permitted hours.','medium',1,'in_review',NULL,NULL,NULL,'2026-09-30 17:56:00','2026-10-01 17:56:00');
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `cam_charges`
--

LOCK TABLES `cam_charges` WRITE;
/*!40000 ALTER TABLE `cam_charges` DISABLE KEYS */;
INSERT INTO `cam_charges` VALUES (1,'CAM-2026-000001',1,5,1,1450.00,'per_sqft',4.50,'2026-10',6525.00,1174.50,7699.50,'billed','2026-10-01 17:56:00','2026-10-01 17:56:00');
/*!40000 ALTER TABLE `cam_charges` ENABLE KEYS */;
UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS = 1;
