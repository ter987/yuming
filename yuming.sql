/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.6.21-log : Database - reci
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `dics` */

DROP TABLE IF EXISTS `dics`;

CREATE TABLE `dics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `pinyin` varchar(128) DEFAULT NULL,
  `com_price` int(5) DEFAULT NULL,
  `cn_price` int(5) DEFAULT NULL,
  `net_price` int(5) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=dec8;

/*Data for the table `dics` */

/*Table structure for table `hanzi` */

DROP TABLE IF EXISTS `hanzi`;

CREATE TABLE `hanzi` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hanzi` varchar(128) NOT NULL,
  `dics_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `hanzi` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
