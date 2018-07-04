/*
SQLyog Ultimate v12.4.0 (64 bit)
MySQL - 10.1.16-MariaDB : Database - koperasi_c45
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `data_latih` */

DROP TABLE IF EXISTS `data_latih`;

CREATE TABLE `data_latih` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(200) DEFAULT NULL,
  `status_pernikahan` varchar(100) DEFAULT NULL,
  `status_rumah` varchar(100) DEFAULT NULL,
  `penghasilan` double DEFAULT NULL,
  `umur` int(11) DEFAULT NULL,
  `kelas_asli` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

/*Data for the table `data_latih` */

insert  into `data_latih`(`id`,`nama`,`status_pernikahan`,`status_rumah`,`penghasilan`,`umur`,`kelas_asli`) values 
(1,'Mus','menikah','rumah sendiri',1800000,54,'lancar'),
(2,'Umu','menikah','rumah sendiri',1500000,39,'lancar'),
(3,'Reni','menikah','rumah sendiri',3000000,45,'lancar'),
(4,'Nur','janda','rumah sendiri',1000000,40,'lancar'),
(5,'Ponik','menikah','kontrak',2500000,41,'macet'),
(6,'Sunar','menikah','rumah sendiri',1800000,36,'macet'),
(7,'Muslik','menikah','rumah sendiri',2000000,55,'macet'),
(8,'Suhar','janda','kontrak',1500000,49,'macet'),
(9,'Astuti','janda','rumah sendiri',1700000,54,'macet'),
(10,'Win','menikah','rumah sendiri',2000000,36,'macet'),
(11,'Setri','menikah','rumah sendiri',2700000,44,'lancar'),
(12,'Sri','menikah','rumah sendiri',3000000,47,'lancar'),
(13,'Suharti','menikah','kontrak',2800000,52,'lancar'),
(14,'Kaseh','menikah','rumah sendiri',1800000,46,'lancar'),
(15,'Eti','menikah','rumah sendiri',1500000,40,'lancar'),
(16,'Las','menikah','rumah sendiri',1000000,60,'macet'),
(17,'Sipah','menikah','rumah sendiri',1000000,54,'macet'),
(18,'Umuh','menikah','rumah sendiri',2000000,49,'lancar'),
(19,'Lilik','menikah','rumah sendiri',1500000,37,'lancar'),
(20,'Sundari','menikah','rumah sendiri',2700000,36,'macet'),
(21,'Yuniarti','menikah','rumah sendiri',2700000,35,'lancar'),
(22,'Munta','janda','rumah sendiri',1000000,51,'macet'),
(23,'Yayuk','menikah','rumah sendiri',2500000,54,'lancar'),
(24,'Pia','menikah','rumah sendiri',2000000,55,'lancar'),
(25,'Patona','menikah','rumah sendiri',3000000,25,'lancar'),
(26,'Muniro','menikah','rumah sendiri',2000000,50,'macet'),
(27,'Isnu','menikah','rumah sendiri',1000000,54,'macet'),
(28,'Alima','menikah','rumah sendiri',1000000,60,'macet'),
(29,'Lua','menikah','rumah sendiri',1000000,60,'macet'),
(30,'Amina','menikah','rumah sendiri',1500000,38,'macet');

/*Table structure for table `data_uji` */

DROP TABLE IF EXISTS `data_uji`;

CREATE TABLE `data_uji` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(200) DEFAULT NULL,
  `status_pernikahan` varchar(100) DEFAULT NULL,
  `status_rumah` varchar(100) DEFAULT NULL,
  `penghasilan` double DEFAULT NULL,
  `umur` int(11) DEFAULT NULL,
  `kelas_asli` varchar(100) DEFAULT NULL,
  `kelas_hasil` varchar(100) DEFAULT NULL,
  `id_rule` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `data_uji` */

insert  into `data_uji`(`id`,`nama`,`status_pernikahan`,`status_rumah`,`penghasilan`,`umur`,`kelas_asli`,`kelas_hasil`,`id_rule`) values 
(1,'Muslimah ','menikah ','rumah sendiri ',1800000,54,'lancar',NULL,0),
(2,'Muslikah ','menikah ','rumah sendiri ',2000000,55,'macet ',NULL,0),
(3,'Astutik ','janda ','rumah sendiri ',1700000,54,'macet ',NULL,0),
(4,'Muawanah ','janda ','rumah sendiri ',3000000,50,'lancar ',NULL,0),
(5,'Lami ','menikah ','rumah sendiri ',2700000,50,'lancar ',NULL,0);

/*Table structure for table `gain` */

DROP TABLE IF EXISTS `gain`;

CREATE TABLE `gain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `atribut` varchar(100) DEFAULT NULL,
  `gain` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `gain` */

insert  into `gain`(`id`,`node_id`,`atribut`,`gain`) values 
(1,1,'status_rumah',0),
(2,1,'Penghasilan=1000000',0),
(3,1,'Penghasilan=2000000',0),
(4,1,'Penghasilan=3000000',0),
(5,1,'Umur=35',0),
(6,1,'Umur=40',0),
(7,1,'Umur=45',1);

/*Table structure for table `hasil_prediksi` */

DROP TABLE IF EXISTS `hasil_prediksi`;

CREATE TABLE `hasil_prediksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(200) DEFAULT NULL,
  `status_pernikahan` varchar(100) DEFAULT NULL,
  `status_rumah` varchar(100) DEFAULT NULL,
  `penghasilan` double DEFAULT NULL,
  `umur` int(11) DEFAULT NULL,
  `kelas_hasil` varchar(100) DEFAULT NULL,
  `id_rule` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `hasil_prediksi` */

/*Table structure for table `rasio_gain` */

DROP TABLE IF EXISTS `rasio_gain`;

CREATE TABLE `rasio_gain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opsi` varchar(10) DEFAULT NULL,
  `cabang1` varchar(50) DEFAULT NULL,
  `cabang2` varchar(50) DEFAULT NULL,
  `rasio_gain` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `rasio_gain` */

/*Table structure for table `t_keputusan` */

DROP TABLE IF EXISTS `t_keputusan`;

CREATE TABLE `t_keputusan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` text,
  `akar` text,
  `keputusan` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/*Data for the table `t_keputusan` */

insert  into `t_keputusan`(`id`,`parent`,`akar`,`keputusan`) values 
(1,'(penghasilan<=1000000)','(umur<=40)','lancar'),
(2,'(penghasilan<=1000000)','(umur>40)','macet'),
(3,'(penghasilan>1000000) AND (status_pernikahan=\'menikah\')','(umur<=35)','lancar'),
(4,'(penghasilan>1000000) AND (status_pernikahan=\'menikah\') AND (umur>35) AND (umur<=40)','(penghasilan<=2000000)','lancar'),
(5,'(penghasilan>1000000) AND (status_pernikahan=\'menikah\') AND (umur>35) AND (umur<=40)','(penghasilan>2000000)','macet'),
(6,'(penghasilan>1000000) AND (status_pernikahan=\'menikah\') AND (umur>35) AND (umur>40) AND (status_rumah=\'rumah sendiri\')','(penghasilan<=2000000)','lancar'),
(7,'(penghasilan>1000000) AND (status_pernikahan=\'menikah\') AND (umur>35) AND (umur>40) AND (status_rumah=\'rumah sendiri\')','(penghasilan>2000000)','lancar'),
(8,'(penghasilan>1000000) AND (status_pernikahan=\'menikah\') AND (umur>35) AND (umur>40) AND (status_rumah=\'kontrak\')','(umur<=45)','macet'),
(9,'(penghasilan>1000000) AND (status_pernikahan=\'menikah\') AND (umur>35) AND (umur>40) AND (status_rumah=\'kontrak\')','(umur>45)','lancar'),
(10,'(penghasilan>1000000)','(status_pernikahan=\'janda\')','macet');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` text,
  `level` char(1) DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

/*Data for the table `users` */

insert  into `users`(`id_user`,`nama`,`username`,`password`,`level`) values 
(1,'Admin','admin','0192023a7bbd73250516f069df18b500','1'),
(27,'Kepala','kepala','836b1f7f9b7f9bf98f1f645302defc59','2');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
