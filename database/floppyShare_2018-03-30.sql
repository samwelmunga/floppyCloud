# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Värd: 127.0.0.1 (MySQL 5.6.33)
# Databas: floppyShare
# Genereringstid: 2018-03-30 04:08:52 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Database: `floppyShare`
--
CREATE DATABASE IF NOT EXISTS `floppyShare` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `floppyShare`;


# Tabelldump floppyFiles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `floppyFiles`;

CREATE TABLE `floppyFiles` (
  `owner_id` int(11) NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file_size` int(11) NOT NULL,
  `category` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `mime_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `data` longblob NOT NULL,
  `shared` tinyint(1) NOT NULL DEFAULT '0',
  `upload_date` datetime NOT NULL,
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `floppyfiles_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `floppyUsers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabelldump floppyUsers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `floppyUsers`;

CREATE TABLE `floppyUsers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
