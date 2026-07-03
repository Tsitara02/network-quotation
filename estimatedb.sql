/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.8-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: estimatedb
-- ------------------------------------------------------
-- Server version	11.8.8-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `adresse` varchar(150) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `type_client` varchar(50) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES
(1,'Le Nouvel Eden','032 47 843 25','Tanambao Morafeno, Toliara','contact@nouveleden.mg','Hôtel','2026-07-02 14:10:15'),
(2,'zaza be drala','034 55 657 34','Toliara centre','clientzaza@gmail.com','Particulier','2026-07-02 15:31:56');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `devis`
--

DROP TABLE IF EXISTS `devis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `devis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_devis` varchar(50) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `type_installation` varchar(100) DEFAULT NULL,
  `total_ht` decimal(12,2) DEFAULT NULL,
  `tva` decimal(12,2) DEFAULT NULL,
  `total_ttc` decimal(12,2) DEFAULT NULL,
  `statut` varchar(30) DEFAULT 'Brouillon',
  `date_devis` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `devis_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devis`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `devis` WRITE;
/*!40000 ALTER TABLE `devis` DISABLE KEYS */;
INSERT INTO `devis` VALUES
(1,'DEV-2026-0001',2,'Liaison CPE point à point',500800.00,100160.00,600960.00,'Accepté','2026-07-02 15:34:10');
/*!40000 ALTER TABLE `devis` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `lignes_devis`
--

DROP TABLE IF EXISTS `lignes_devis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lignes_devis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `devis_id` int(11) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `type_ligne` varchar(30) DEFAULT NULL,
  `quantite` decimal(10,2) DEFAULT NULL,
  `prix_unitaire` decimal(12,2) DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `devis_id` (`devis_id`),
  CONSTRAINT `lignes_devis_ibfk_1` FOREIGN KEY (`devis_id`) REFERENCES `devis` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lignes_devis`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `lignes_devis` WRITE;
/*!40000 ALTER TABLE `lignes_devis` DISABLE KEYS */;
INSERT INTO `lignes_devis` VALUES
(1,1,'Câble RJ45 Cat6','Matériel',10.00,3000.00,30000.00),
(2,1,'TP-Link CPE 510','Matériel',1.01,280000.00,282800.00),
(3,1,'Switch 8 ports','Matériel',1.00,180000.00,180000.00),
(4,1,'Connecteur RJ45','Matériel',8.00,1000.00,8000.00);
/*!40000 ALTER TABLE `lignes_devis` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `materiels`
--

DROP TABLE IF EXISTS `materiels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `materiels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `categorie` varchar(50) DEFAULT NULL,
  `prix_unitaire` decimal(12,2) NOT NULL,
  `unite` varchar(30) DEFAULT NULL,
  `actif` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materiels`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `materiels` WRITE;
/*!40000 ALTER TABLE `materiels` DISABLE KEYS */;
INSERT INTO `materiels` VALUES
(1,'MikroTik hAP ac2','Routeur',350000.00,'pièce',1),
(2,'TP-Link CPE 510','CPE',280000.00,'pièce',1),
(3,'Support mural Starlink','Support',80000.00,'pièce',1),
(4,'Câble RJ45 Cat6','Câble',3000.00,'mètre',1),
(5,'Connecteur RJ45','Accessoire',1000.00,'pièce',1),
(6,'Switch 8 ports','Switch',180000.00,'pièce',1),
(7,'Caméra IP','Caméra',300000.00,'pièce',1);
/*!40000 ALTER TABLE `materiels` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `prestations`
--

DROP TABLE IF EXISTS `prestations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `prestations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prix_base` decimal(12,2) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prestations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `prestations` WRITE;
/*!40000 ALTER TABLE `prestations` DISABLE KEYS */;
INSERT INTO `prestations` VALUES
(1,'Installation Starlink',200000.00,'Pose,fixation,activation,configuration de base'),
(2,'Installation Starlink',150000.00,'Installation et orientation du kit Starlink'),
(3,'Configuration MikroTik',80000.00,'Configuration routeur, DHCP, NAT, firewall'),
(4,'Installation caméra IP',50000.00,'Pose et configuration caméra IP'),
(5,'Audit réseau',70000.00,'Diagnostic réseau et recommandations');
/*!40000 ALTER TABLE `prestations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'admin',
  `date_creation` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES
(1,'Administrateur','admin@akioservice.com','$2y$12$k.GQiYDBGs9NsVNPCcuwy.Ho0MB7D.9WPNvrSWS9qcmZFYN0ZNhm2','admin','2026-07-03 10:04:52');
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-07-03 13:20:37
