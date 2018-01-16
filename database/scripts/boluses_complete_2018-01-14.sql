# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.1.21-MariaDB)
# Database: resuscitation
# Generation Time: 2018-01-14 23:17:55 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table boluses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `boluses`;

CREATE TABLE `boluses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commercial_concentration` double(8,2) NOT NULL,
  `dosage` double(8,2) NOT NULL,
  `minimum_dose` double(8,2) NOT NULL,
  `maximum_dose` double(8,2) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `boluses` WRITE;
/*!40000 ALTER TABLE `boluses` DISABLE KEYS */;

INSERT INTO `boluses` (`id`, `name`, `unit`, `commercial_concentration`, `dosage`, `minimum_dose`, `maximum_dose`, `type`, `created_at`, `updated_at`)
VALUES
	(1,'Adénosine (1ère dose)','mg',3.00,0.10,0.00,6.00,1,NULL,NULL),
	(2,'Amiodarone','mg',50.00,5.00,0.00,300.00,1,NULL,NULL),
	(3,'Atropine (bradycardie)  min: 0.1 mg','mg',0.60,0.02,0.10,0.50,1,NULL,NULL),
	(4,'Bicarbonate de Na 8.4%','mmol',1.00,1.00,0.00,0.00,1,NULL,NULL),
	(5,'Calcium, chlorure 10%','mg',100.00,20.00,0.00,1000.00,1,NULL,NULL),
	(6,'Calcium, gluconate','mg',100.00,60.00,0.00,3000.00,1,NULL,NULL),
	(7,'Dextrose 10%','g',0.10,0.50,0.00,25.00,1,NULL,NULL),
	(8,'Épinéphrine 1:10 000 (IV/IO)','mg',0.10,0.01,0.00,1.00,1,NULL,NULL),
	(9,'Épinéphrine 1:1000 (E.T.)','mg',1.00,0.10,0.00,2.50,1,NULL,NULL),
	(10,'Flumazénil','mg',0.10,0.01,0.00,0.20,1,NULL,NULL),
	(11,'Lidocaine 2%','mg',20.00,1.00,0.00,0.00,1,NULL,NULL),
	(12,'Magnésium, sulfate 20%','mg',200.00,50.00,0.00,2000.00,1,NULL,NULL),
	(13,'Naloxone (renversement complet)','mg',0.40,0.10,0.00,2.00,1,NULL,NULL),
	(14,'Fentanyl','mcg',50.00,2.00,0.00,100.00,2,NULL,NULL),
	(15,'Midazolam','mg',5.00,0.10,0.00,10.00,2,NULL,NULL),
	(16,'Kétamine','mg',10.00,1.50,0.00,0.00,2,NULL,NULL),
	(17,'Rocuronium','mg',10.00,1.00,0.00,0.00,2,NULL,NULL),
	(18,'Succinylcholine','mg',20.00,1.50,0.00,100.00,2,NULL,NULL),
	(19,'Propofol','mg',10.00,3.00,0.00,0.00,2,NULL,NULL),
	(20,'Étomidate','mg',2.00,0.30,0.00,20.00,2,NULL,NULL),
	(21,'Mannitol 20%','g',0.20,1.00,0.00,0.00,3,NULL,NULL),
	(22,'NaCl 3%','ml',0.00,3.00,0.00,0.00,3,NULL,NULL),
	(23,'Phenobarbital','mg',120.00,20.00,0.00,0.00,3,NULL,NULL),
	(24,'Phénytoïne','mg',50.00,20.00,0.00,0.00,3,NULL,NULL),
	(25,'Épinéphrine pour anaphylaxie 1:1000 (IM)','mg',1.00,0.01,0.00,0.50,3,NULL,NULL),
	(26,'Lorazépam','mg',4.00,0.10,0.00,4.00,3,NULL,NULL),
	(27,'Première dose','J',0.00,2.00,0.00,200.00,4,NULL,NULL),
	(28,'Deuxième dose','J',0.00,4.00,0.00,200.00,4,NULL,NULL);

/*!40000 ALTER TABLE `boluses` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
