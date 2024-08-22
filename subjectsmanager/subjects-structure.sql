-- MySQL dump 10.13  Distrib 8.0.33, for Linux (x86_64)
--
-- Host: localhost    Database: psccore
-- ------------------------------------------------------
-- Server version	8.0.33-0ubuntu0.20.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topics` (
  `id` INT AUTO_INCREMENT,
  `topic_name` varchar(512) NOT NULL,
  `is_umbrella` TINYINT NOT NULL default 0,
  `see_id` INT DEFAULT NULL,
  `consensusDefinition` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `topic_name` (`topic_name`),
  CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`see_id`) REFERENCES `topics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;




--
-- Table structure for table `project_topic_data`
--

DROP TABLE IF EXISTS `project_topic_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_topic_data` (
	`id` INT AUTO_INCREMENT,
  `topic_id` INT NOT NULL,
  `project_sitename` varchar(10) NOT NULL,
  `internalNote` varchar(512),
  `publicNote` varchar(512),
  `last_update` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `hide` TINYINT,
  PRIMARY KEY (`id`,`project_sitename`),
  CONSTRAINT `unique_topic_project` UNIQUE (`topic_id`, `project_sitename`),
  CONSTRAINT `project_topic_data_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `topic_relationships`
--

DROP TABLE IF EXISTS `topic_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topic_relationships` ( 
	`id`  INT AUTO_INCREMENT,
  `topic_id` INT NOT NULL,
  `related_topic_id` INT NOT NULL,
  `relationship` varchar(512) NOT NULL,

  PRIMARY KEY (`id`),
  CONSTRAINT `topic_relationships_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`),
  CONSTRAINT `topic_relationships_ibfk_2` FOREIGN KEY (`related_topic_id`) REFERENCES `topics` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
