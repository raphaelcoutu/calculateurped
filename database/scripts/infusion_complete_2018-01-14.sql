# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.1.21-MariaDB)
# Database: resuscitation
# Generation Time: 2018-01-15 01:43:48 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table infusion_concentrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `infusion_concentrations`;

CREATE TABLE `infusion_concentrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `infusion_drug_id` tinyint(4) NOT NULL,
  `concentration` double(8,2) NOT NULL,
  `concentration_unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipe` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_volume` double(8,2) NOT NULL,
  `weight_category` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `infusion_concentrations` WRITE;
/*!40000 ALTER TABLE `infusion_concentrations` DISABLE KEYS */;

INSERT INTO `infusion_concentrations` (`id`, `infusion_drug_id`, `concentration`, `concentration_unit`, `recipe`, `total_volume`, `weight_category`, `created_at`, `updated_at`)
VALUES
	(1,1,4.00,'mcg','200 mcg = 2 ml + 48 ml de NS ou D5%',50.00,1,NULL,NULL),
	(2,1,4.00,'mcg','200 mcg = 2 ml + 48 ml de NS ou D5%',50.00,2,NULL,NULL),
	(3,1,4.00,'mcg','200 mcg = 2 ml + 48 ml de NS ou D5%',50.00,3,NULL,NULL),
	(4,1,4.00,'mcg','200 mcg = 2 ml + 48 ml de NS ou D5%',50.00,4,NULL,NULL),
	(5,1,4.00,'mcg','200 mcg = 2 ml + 48 ml de NS ou D5%',50.00,5,NULL,NULL),
	(6,2,50.00,'mcg','Solution pure',0.00,1,NULL,NULL),
	(7,2,50.00,'mcg','Solution pure',0.00,2,NULL,NULL),
	(8,2,50.00,'mcg','Solution pure',0.00,3,NULL,NULL),
	(9,2,50.00,'mcg','Solution pure',0.00,4,NULL,NULL),
	(10,2,50.00,'mcg','Solution pure',0.00,5,NULL,NULL),
	(11,3,1.00,'mg','25 mg = 2,5 ml + 22,5 ml de NS ou D5%',25.00,1,NULL,NULL),
	(12,3,1.00,'mg','25 mg = 2,5 ml + 22,5 ml de NS ou D5%',25.00,2,NULL,NULL),
	(13,3,1.00,'mg','25 mg = 2,5 ml + 22,5 ml de NS ou D5%',25.00,3,NULL,NULL),
	(14,3,1.00,'mg','25 mg = 2,5 ml + 22,5 ml de NS ou D5%',25.00,4,NULL,NULL),
	(15,3,5.00,'mg','250 mg = 25 ml + 25 ml de NS ou D5%',50.00,5,NULL,NULL),
	(16,4,1.00,'mg','50 mg = 10 ml + 40 ml de NS ou D5%',50.00,1,NULL,NULL),
	(17,4,1.00,'mg','50 mg = 10 ml + 40 ml de NS ou D5%',50.00,2,NULL,NULL),
	(18,4,1.00,'mg','50 mg = 10 ml + 40 ml de NS ou D5%',50.00,3,NULL,NULL),
	(19,4,1.00,'mg','50 mg = 10 ml + 40 ml de NS ou D5%',50.00,4,NULL,NULL),
	(20,4,5.00,'mg','Solution pure',0.00,5,NULL,NULL),
	(21,5,10.00,'mg','Solution pure',0.00,1,NULL,NULL),
	(22,5,10.00,'mg','Solution pure',0.00,2,NULL,NULL),
	(23,5,10.00,'mg','Solution pure',0.00,3,NULL,NULL),
	(24,5,10.00,'mg','Solution pure',0.00,4,NULL,NULL),
	(25,5,10.00,'mg','Solution pure',0.00,5,NULL,NULL),
	(26,6,10.00,'mcg','500 mcg = 1 ml + 49 ml de NS ou D5%',50.00,1,NULL,NULL),
	(27,7,1.80,'mg','450 mg = 9 ml + 91 ml de D5% (sac sans PVC)',100.00,1,NULL,NULL),
	(28,7,1.80,'mg','450 mg = 9 ml + 91 ml de D5% (sac sans PVC)',100.00,2,NULL,NULL),
	(29,7,1.80,'mg','450 mg = 9 ml + 91 ml de D5% (sac sans PVC)',100.00,3,NULL,NULL),
	(30,7,1.80,'mg','450 mg = 9 ml + 91 ml de D5% (sac sans PVC)',100.00,4,NULL,NULL),
	(31,7,1.80,'mg','450 mg = 9 ml + 91 ml de D5% (sac sans PVC)',100.00,5,NULL,NULL),
	(32,8,800.00,'mcg','80 mg = 6,4 ml dans 100 ml de NS ou D5%',100.00,1,NULL,NULL),
	(33,8,800.00,'mcg','80 mg = 6,4 ml dans 100 ml de NS ou D5%',100.00,2,NULL,NULL),
	(34,8,800.00,'mcg','80 mg = 6,4 ml dans 100 ml de NS ou D5%',100.00,3,NULL,NULL),
	(35,8,1600.00,'mcg','160 mg = 12,8 ml dans 87 ml de NS ou D5%',100.00,4,NULL,NULL),
	(36,8,3200.00,'mcg','320 mg = 25,8 ml dans 74 ml de NS ou D5%',100.00,5,NULL,NULL),
	(37,9,1600.00,'mcg','N/A',0.00,1,NULL,NULL),
	(38,9,1600.00,'mcg','N/A',0.00,2,NULL,NULL),
	(39,9,1600.00,'mcg','N/A',0.00,3,NULL,NULL),
	(40,9,1600.00,'mcg','N/A',0.00,4,NULL,NULL),
	(41,9,1600.00,'mcg','N/A',0.00,5,NULL,NULL),
	(42,10,16.00,'mcg','1,6 mg = 1,6 ml dans 100 ml de NS ou D5%',100.00,1,NULL,NULL),
	(43,10,32.00,'mcg','3,2 mg = 3,2 ml dans 100 ml de NS ou D5%',100.00,2,NULL,NULL),
	(44,10,64.00,'mcg','6,4 mg = 6,4 ml dans 100 ml de NS ou D5%',100.00,3,NULL,NULL),
	(45,10,64.00,'mcg','6,4 mg = 6,4 ml dans 100 ml de NS ou D5%',100.00,4,NULL,NULL),
	(46,10,64.00,'mcg','6,4 mg = 6,4 ml dans 100 ml de NS ou D5%',100.00,5,NULL,NULL),
	(47,11,0.20,'mg','5 mg = 5 ml + 20 ml de NS ou D5%',25.00,1,NULL,NULL),
	(48,11,0.20,'mg','5 mg = 5 ml + 20 ml de NS ou D5%',25.00,2,NULL,NULL),
	(49,11,0.20,'mg','5 mg = 5 ml + 20 ml de NS ou D5%',25.00,3,NULL,NULL),
	(50,11,0.20,'mg','5 mg = 5 ml + 20 ml de NS ou D5%',25.00,4,NULL,NULL),
	(51,11,0.20,'mg','10 mg = 10 ml + 40 ml de NS ou D5%',50.00,5,NULL,NULL),
	(52,12,0.20,'mg','20 mg = 0,8 ml dans 100 ml de NS ou D5%',100.00,1,NULL,NULL),
	(53,12,0.40,'mg','40 mg = 1,6 ml dans 100 ml de NS ou D5%',100.00,2,NULL,NULL),
	(54,12,0.40,'mg','40 mg = 1,6 ml dans 100 ml de NS ou D5%',100.00,3,NULL,NULL),
	(55,12,0.40,'mg','40 mg = 1,6 ml dans 100 ml de NS ou D5%',100.00,4,NULL,NULL),
	(56,12,0.40,'mg','40 mg = 1,6 ml dans 100 ml de NS ou D5%',100.00,5,NULL,NULL),
	(57,13,16.00,'mcg','1,6 mg = 1,6 ml dans 100 ml de NS ou D5%',100.00,1,NULL,NULL),
	(58,13,32.00,'mcg','3,2 mg = 3,2 ml dans 100 ml de NS ou D5%',100.00,2,NULL,NULL),
	(59,13,64.00,'mcg','6,4 mg = 6,4 ml dans 100 ml de NS ou D5%',100.00,3,NULL,NULL),
	(60,13,64.00,'mcg','6,4 mg = 6,4 ml dans 100 ml de NS ou D5%',100.00,4,NULL,NULL),
	(61,13,64.00,'mcg','6,4 mg = 6,4 ml dans 100 ml de NS ou D5%',100.00,5,NULL,NULL),
	(62,14,100.00,'mU','10 unités = 0,5 ml dans 100 ml de NS ou D5%',100.00,1,NULL,NULL),
	(63,14,100.00,'mU','10 unités = 0,5 ml dans 100 ml de NS ou D5%',100.00,2,NULL,NULL),
	(64,14,100.00,'mU','10 unités = 0,5 ml dans 100 ml de NS ou D5%',100.00,3,NULL,NULL),
	(65,14,100.00,'mU','10 unités = 0,5 ml dans 100 ml de NS ou D5%',100.00,4,NULL,NULL),
	(66,14,100.00,'mU','10 unités = 0,5 ml dans 100 ml de NS ou D5%',100.00,5,NULL,NULL),
	(67,15,0.10,'unité','5 unités = 0,05 ml dans 50 ml de NS',50.00,1,NULL,NULL),
	(68,15,0.10,'unité','25 unités = 0,25 ml dans 250 ml de NS',250.00,2,NULL,NULL),
	(69,15,0.10,'unité','25 unités = 0,25 ml dans 250 ml de NS',250.00,3,NULL,NULL),
	(70,15,0.10,'unité','25 unités = 0,25 ml dans 250 ml de NS',250.00,4,NULL,NULL),
	(71,15,0.10,'unité','25 unités = 0,25 ml dans 250 ml de NS',250.00,5,NULL,NULL),
	(72,16,0.50,'mg','25 mg = 25 ml + 25 ml de NS ou D5%',50.00,1,NULL,NULL),
	(73,16,0.50,'mg','25 mg = 25 ml + 25 ml de NS ou D5%',50.00,2,NULL,NULL),
	(74,16,0.50,'mg','25 mg = 25 ml + 25 ml de NS ou D5%',50.00,3,NULL,NULL),
	(75,16,0.50,'mg','25 mg = 25 ml + 25 ml de NS ou D5%',50.00,4,NULL,NULL),
	(76,16,0.50,'mg','25 mg = 25 ml + 25 ml de NS ou D5%',50.00,5,NULL,NULL);

/*!40000 ALTER TABLE `infusion_concentrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table infusion_drugs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `infusion_drugs`;

CREATE TABLE `infusion_drugs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `concentration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debit_min` double(8,2) NOT NULL,
  `debit_max` double(8,2) NOT NULL,
  `debit_dose_unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debit_time_unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `infusion_drugs` WRITE;
/*!40000 ALTER TABLE `infusion_drugs` DISABLE KEYS */;

INSERT INTO `infusion_drugs` (`id`, `name`, `concentration`, `debit_min`, `debit_max`, `debit_dose_unit`, `debit_time_unit`, `created_at`, `updated_at`)
VALUES
	(1,'Dexmédétomidine','100 mcg/ml',0.30,1.50,'mcg','h',NULL,NULL),
	(2,'Fentanyl','50 mcg/ml',1.00,2.00,'mcg','h',NULL,NULL),
	(3,'Morphine','10 mg/ml',0.05,0.10,'mg','h',NULL,NULL),
	(4,'Midazolam','5 mg/ml',0.08,0.40,'mg','h',NULL,NULL),
	(5,'Propofol','10 mg/ml',30.00,80.00,'mcg','min',NULL,NULL),
	(6,'Alprostadil','500 mcg/ml',0.05,0.10,'mcg','min',NULL,NULL),
	(7,'Amiodarone','50 mg/ml',5.00,15.00,'mcg','min',NULL,NULL),
	(8,'Dobutamine','12.5 mg/ml',10.00,20.00,'mcg','min',NULL,NULL),
	(9,'Dopamine','1600 mcg/ml',10.00,20.00,'mcg','min',NULL,NULL),
	(10,'Épinéphrine','1 mg/ml',0.10,1.00,'mcg','min',NULL,NULL),
	(11,'Milrinone','1 mg/ml',0.25,0.75,'mcg','min',NULL,NULL),
	(12,'Nitroprusside','25 mg/ml',0.50,8.00,'mcg','min',NULL,NULL),
	(13,'Norépinéphrine','1 mg/ml',0.10,1.00,'mcg','min',NULL,NULL),
	(14,'Vasopressine','20 unités/ml',0.20,2.00,'mU','min',NULL,NULL),
	(15,'Insuline','100 unités/ml',0.10,0.10,'unité','h',NULL,NULL),
	(16,'Salbutamol','1 mg/ml',1.00,5.00,'mcg','min',NULL,NULL);

/*!40000 ALTER TABLE `infusion_drugs` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
