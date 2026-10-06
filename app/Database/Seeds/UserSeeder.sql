-- ==============================================================================
-- Seeder: UserSeeder
-- Converted from PHP seeder to MySQL DML
-- Target tables: users, user_roles
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Super Administrator','admin@realestate-erp.local','+91 99000 11222','$2y$10$f2RgZZpfrgEBiEYICrUamO2ohqzBRiNydNvRMaI6DUQ7XrRlIZ1VG','active','2026-10-05 16:53:00','2026-10-01 14:37:22','2026-10-05 16:53:00',NULL),(2,1,'Operations Admin','admin.ops@realestate-erp.local','+91 99000 33444','$2y$10$f2RgZZpfrgEBiEYICrUamO2ohqzBRiNydNvRMaI6DUQ7XrRlIZ1VG','active','2026-10-05 13:04:15','2026-10-01 14:37:22','2026-10-05 13:04:15',NULL),(3,1,'Branch Manager','manager@realestate-erp.local','+91 99000 55666','$2y$10$f2RgZZpfrgEBiEYICrUamO2ohqzBRiNydNvRMaI6DUQ7XrRlIZ1VG','active','2026-10-05 14:34:54','2026-10-01 14:37:22','2026-10-05 14:34:54',NULL),(4,NULL,'Pooja Bhatt','testuser_1790846373@apexhorizon.com','+91 91234 56789','$2y$10$JkFh8lpcxDi8YjA2CESI0urO8Z5/9MZl92p3wV08LMlukPYXh.i6W','active',NULL,'2026-10-01 14:49:33','2026-10-01 14:49:33',NULL),(5,NULL,'Pooja Bhatt','testuser_1790846392@apexhorizon.com','+91 91234 56789','$2y$10$uwYRVH8mghCbIm.EkPMaEesVtCgd/38IBvIygRc4z1yhCPN/4oVBm','active',NULL,'2026-10-01 14:49:52','2026-10-05 13:09:43','2026-10-05 13:09:43'),(6,NULL,'Pooja Bhatt','testuser_1790846409@apexhorizon.com','+91 91234 56789','$2y$10$ec3pD5q8AU7zAWLw4/0.SeM2gSIV4IF2MSPw8ttGibgUUEd.3cS8m','active',NULL,'2026-10-01 14:50:10','2026-10-01 14:50:10',NULL),(7,NULL,'Pooja Bhatt','testuser_1790846440@apexhorizon.com','+91 91234 56789','$2y$10$DtHTdImuOG4HvJcoyn/S0OHtzy5svX9n9YLe.GTuVzyabWo9M/Pdq','active',NULL,'2026-10-01 14:50:41','2026-10-01 14:50:41',NULL),(8,NULL,'Pooja Bhatt','testuser_1790846456@apexhorizon.com','+91 91234 56789','$2y$10$ODU.v03xEaRN/sy8WDkNEORNuoRRuMaMPx4wkjlsUiu1pIA0Bg/9C','active',NULL,'2026-10-01 14:50:56','2026-10-01 14:50:56',NULL),(9,NULL,'Pooja Bhatt','testuser_1790846524@apexhorizon.com','+91 91234 56789','$2y$10$rFnamGpbJIr9KcU7sqZP6OAzUSgwFCgs/f6GxLBzkzPZqTXWkyxR6','active',NULL,'2026-10-01 14:52:04','2026-10-01 14:52:04',NULL),(10,NULL,'Pooja Bhatt','testuser_1790846550@apexhorizon.com','+91 91234 56789','$2y$10$IS12kbl.CH5v75zmfwDuGejnwipu2wtNJxzgq5JH8RjyZf6fucoY6','active',NULL,'2026-10-01 14:52:30','2026-10-01 14:52:30',NULL),(11,NULL,'Pooja Bhatt','testuser_1790846575@apexhorizon.com','+91 91234 56789','$2y$10$wCnUbzAsQfdrXd5zLkkAMOypeaMi/z/616Ikspkoc2EREF1.k7jdK','active',NULL,'2026-10-01 14:52:55','2026-10-01 14:52:55',NULL),(12,NULL,'Pooja Bhatt','testuser_1790846610@apexhorizon.com','+91 91234 56789','$2y$10$uKPj7RqUIxuJRDnuV8SQSOCJINTE62tTKAD2qSGGbrDfMEXLN6PaC','active',NULL,'2026-10-01 14:53:30','2026-10-01 14:53:30',NULL),(13,NULL,'Pooja Bhatt','testuser_1790846673@apexhorizon.com','+91 91234 56789','$2y$10$6Y0C5BEGQbVFA5ahfin21uDdP3HNRah4qF/MMPvZGWTJnXxpiJiGO','active',NULL,'2026-10-01 14:54:33','2026-10-01 14:54:33',NULL),(14,NULL,'Pooja Bhatt','testuser_1790847141@apexhorizon.com','+91 91234 56789','$2y$10$zlJM3zfZYFK6ZF/O12zV/ehadKtlH7kPn.tX/YzPTL9Ew8S.Ahf8O','active',NULL,'2026-10-01 15:02:21','2026-10-01 15:02:21',NULL),(15,NULL,'Pooja Bhatt','testuser_1790849749@apexhorizon.com','+91 91234 56789','$2y$10$gpsS5.iej9kETsiFkvgAGenrK7CXZlmCkNIDW1q6Uda7EI3wee11a','active',NULL,'2026-10-01 15:45:49','2026-10-01 15:45:49',NULL),(16,NULL,'Pooja Bhatt','testuser_1790849800@apexhorizon.com','+91 91234 56789','$2y$10$Cs9b5YnqAG76Axra5sU3fuynzBrdzBh2mUSmNo0Ipoy5vDsSm9kO2','active',NULL,'2026-10-01 15:46:40','2026-10-01 15:46:40',NULL),(17,1,'Rajesh Sharma (Senior Sales)','sales@realestate-erp.local','+91 98200 99881','$2y$10$npN9WeK6XoGBFkZHxC3PFOqAAadCMDSsLC.V2FYgoPy6aol/4QjiC','active','2026-10-05 16:36:28','2026-10-01 16:02:38','2026-10-05 16:36:28',NULL),(18,NULL,'Pooja Bhatt','testuser_1790852100@apexhorizon.com','+91 91234 56789','$2y$10$xVlHRlMevfELRg96EfwVzuCA2mD5zDMC7d9YRqjo8DHFElbe/2iaK','active',NULL,'2026-10-01 16:25:00','2026-10-01 16:25:00',NULL),(19,NULL,'Pooja Bhatt','testuser_1790852574@apexhorizon.com','+91 91234 56789','$2y$10$9phNgpqFCCbZX304RduEA.jR0IFTNH0MGUGUHEjhJkJ22zMGl/orq','active',NULL,'2026-10-01 16:32:54','2026-10-01 16:32:54',NULL),(20,NULL,'Pooja Bhatt','testuser_1790852946@apexhorizon.com','+91 91234 56789','$2y$10$Vfvhm01UlQ.0nFWokNg2eOnNYgmP8K39csY9.MYqsqFT3s65/k.2q','active',NULL,'2026-10-01 16:39:06','2026-10-01 16:39:06',NULL),(21,NULL,'Pooja Bhatt','testuser_1790855073@apexhorizon.com','+91 91234 56789','$2y$10$LTcyqzZVvF8aRkFSl0zG4.UyfckoAn.CpcRNiKZLR3/eTrl.sM7hK','active',NULL,'2026-10-01 17:14:33','2026-10-01 17:14:33',NULL),(22,NULL,'Pooja Bhatt','testuser_1790855422@apexhorizon.com','+91 91234 56789','$2y$10$dAUJHVMXSYjv5o6W.rJjv.huKRQHRyws9n6J7iruKUPYX8wYUB5Om','active',NULL,'2026-10-01 17:20:22','2026-10-01 17:20:22',NULL),(23,NULL,'Pooja Bhatt','testuser_1791008267@apexhorizon.com','+91 91234 56789','$2y$10$Ork61ysq.NtkoZpyyDx5WOsfxHicnU3cU4BdZCJ2.rdJzTITZ3LeW','active',NULL,'2026-10-03 11:47:47','2026-10-03 11:47:47',NULL),(24,NULL,'Pooja Bhatt','testuser_1791010814@apexhorizon.com','+91 91234 56789','$2y$10$zGYc6HBs9tr3keM9ufIvsulDuhH0kYxv8nXCTrt0D4Y6EMCCAApqi','active',NULL,'2026-10-03 12:30:14','2026-10-03 12:30:14',NULL),(25,NULL,'Pooja Bhatt','testuser_1791012970@apexhorizon.com','+91 91234 56789','$2y$10$WKaPrbLXjzej1m811kFDteSEv/JIGwx3V1.VehXhGrWlZmR93nSpO','active',NULL,'2026-10-03 13:06:10','2026-10-03 13:06:10',NULL),(26,NULL,'Pooja Bhatt','testuser_1791014006@apexhorizon.com','+91 91234 56789','$2y$10$uUjYpIMn6I3cwwGcutqfUewhZxNpIkGdhVtBZImhTREL3cCMgiqiu','active',NULL,'2026-10-03 13:23:26','2026-10-03 13:23:26',NULL),(27,NULL,'Pooja Bhatt','testuser_1791016960@apexhorizon.com','+91 91234 56789','$2y$10$hxiiR4KBCK63re4icTWQHeqK4ZQ1XxUk0BXenAuVDXeiOd0QDtlCO','active',NULL,'2026-10-03 14:12:40','2026-10-03 14:12:40',NULL),(28,NULL,'Pooja Bhatt','testuser_1791017688@apexhorizon.com','+91 91234 56789','$2y$10$u.VxzxC0v6p1DQTNuoGdqOVwV1MIdBZRoq93QixcAXG6V3v0dMx1G','active',NULL,'2026-10-03 14:24:48','2026-10-03 14:24:48',NULL),(29,NULL,'Pooja Bhatt','testuser_1791018021@apexhorizon.com','+91 91234 56789','$2y$10$CFvXXBN3m6p2qTYMpp4.kut7G1SH.Z9w1eqJBc8rpNrgbw7QD7d3O','active',NULL,'2026-10-03 14:30:21','2026-10-03 14:30:21',NULL),(30,NULL,'Pooja Bhatt','testuser_1791021084@apexhorizon.com','+91 91234 56789','$2y$10$M93BYc3yKRKvs8FArfcQd.5gOVM6FEgU7zVX2CqVpNvTVU1BRUB8C','active',NULL,'2026-10-03 15:21:24','2026-10-03 15:21:24',NULL),(31,NULL,'Pooja Bhatt','testuser_1791178387@apexhorizon.com','+91 91234 56789','$2y$10$4uZKRt/2JDK/tuZNvipCauxnM724XSQa8kl/i71qgk9IZBe0Vxlze','active',NULL,'2026-10-05 11:03:07','2026-10-05 11:03:07',NULL),(32,NULL,'Pooja Bhatt','testuser_1791183580@apexhorizon.com','+91 91234 56789','$2y$10$Jv494bF.ug1kT3zSjz0fXuGq9I/XoLVifPirQhy5QduAlDiTZU4jq','active',NULL,'2026-10-05 12:29:40','2026-10-05 12:29:40',NULL),(33,NULL,'Pooja Bhatt','testuser_1791184678@apexhorizon.com','+91 91234 56789','$2y$10$0iA8PZsZTfmXLyRv0xsvUeylb9QLeJELCcddqI9R/3DGcEln/C50q','active',NULL,'2026-10-05 12:47:58','2026-10-05 12:47:58',NULL),(34,NULL,'Pooja Bhatt','testuser_1791185752@apexhorizon.com','+91 91234 56789','$2y$10$AygNCL9yigA3tAeig7EmCuzkkdzgDa.3Ye7AEf9DUoRiKtu/OuU/a','active',NULL,'2026-10-05 13:05:52','2026-10-05 13:05:52',NULL),(35,NULL,'Pooja Bhatt','testuser_1791191090@apexhorizon.com','+91 91234 56789','$2y$10$D.0sVDzaGLAheNjJ/H34X.BNashatGYO86rMZ1GFMx8qnQnOHMMcq','active',NULL,'2026-10-05 14:34:50','2026-10-05 14:34:50',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'real_estate_erp'
--

--
-- Dumping routines for database 'real_estate_erp'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-05 17:59:23

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,1,1),(2,2,2),(3,3,3),(4,4,2),(5,5,2),(6,6,2),(7,7,2),(8,8,2),(9,9,2),(10,10,2),(11,11,2),(12,12,2),(13,13,2),(14,14,2),(15,15,2),(16,16,2),(17,17,4),(18,18,2),(19,19,2),(20,20,2),(21,21,2),(22,22,2),(23,23,2),(24,24,2),(25,25,2),(26,26,2),(27,27,2),(28,28,2),(29,29,2),(30,30,2),(31,31,2),(32,32,2),(33,33,2),(34,34,2),(35,35,2);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS = 1;
