# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.9)
# Database: contrails
# Generation Time: 2012-09-22 21:48:44 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table mm_usradmin_usr_grp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mm_usradmin_usr_grp`;

CREATE TABLE `mm_usradmin_usr_grp` (
  `local_id` int(10) unsigned NOT NULL DEFAULT '0',
  `foreign_id` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `local_id` (`local_id`,`foreign_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `mm_usradmin_usr_grp` WRITE;
/*!40000 ALTER TABLE `mm_usradmin_usr_grp` DISABLE KEYS */;

INSERT INTO `mm_usradmin_usr_grp` (`local_id`, `foreign_id`)
VALUES
	(200,68);

/*!40000 ALTER TABLE `mm_usradmin_usr_grp` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mod_page
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mod_page`;

CREATE TABLE `mod_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` text,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `keywords` text,
  `lft` int(10) unsigned NOT NULL DEFAULT '0',
  `rgt` int(10) unsigned NOT NULL DEFAULT '0',
  `root_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `set_ignore` tinyint(1) NOT NULL DEFAULT '0',
  `template_name` varchar(255) NOT NULL,
  `rights` int(10) unsigned NOT NULL DEFAULT '0',
  `structure` text NOT NULL,
  `lost_mods` text NOT NULL,
  `sys_trashcan` smallint(1) unsigned NOT NULL DEFAULT '0',
  `sys_date_created` int(10) unsigned NOT NULL DEFAULT '0',
  `sys_date_changed` int(10) unsigned NOT NULL DEFAULT '0',
  `cookie_name` varchar(255) DEFAULT NULL,
  `cookie_lifetime` int(11) DEFAULT NULL,
  `cookie_value` varchar(255) DEFAULT NULL,
  `redirect_to` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `mod_page` WRITE;
/*!40000 ALTER TABLE `mod_page` DISABLE KEYS */;

INSERT INTO `mod_page` (`id`, `name`, `url`, `title`, `description`, `keywords`, `lft`, `rgt`, `root_id`, `parent_id`, `set_ignore`, `template_name`, `rights`, `structure`, `lost_mods`, `sys_trashcan`, `sys_date_created`, `sys_date_changed`, `cookie_name`, `cookie_lifetime`, `cookie_value`, `redirect_to`)
VALUES
	(1,'oOS','oos.html','','','',1,26,1,0,0,'contrails.php',0,'a:1:{i:1;a:2:{i:2;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"66aead64539794311287b63524e54227\";}i:0;a:3:{s:9:\"view_name\";s:3:\"pap\";s:8:\"mod_name\";s:4:\"page\";s:3:\"vid\";s:32:\"a3085a6b778ed493964539d7cb2c838a\";}}}','a:11:{i:0;a:4:{s:9:\"view_name\";s:32:\"89ce3cb75e21acf41d7b67d3b9bdaa6c\";s:4:\"meta\";a:3:{s:4:\"time\";i:1092913822;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:5:\"level\";s:3:\"vid\";s:32:\"6e0866963dc7c964655193fb464105d3\";}i:1;a:4:{s:9:\"view_name\";s:32:\"b8674f6e173a5f489ade57057ac23de9\";s:4:\"meta\";a:3:{s:4:\"time\";i:1096038311;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:5:\"level\";s:3:\"vid\";s:32:\"643614841cdc37a60ee2e97b44d139b3\";}i:2;a:4:{s:9:\"view_name\";s:32:\"6df1cdf6c4eb59bef6fc35ea50e18047\";s:4:\"meta\";a:3:{s:4:\"time\";i:1096620902;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:3:\"mms\";s:3:\"vid\";s:32:\"835fb2d1812b1e292d5369ae92dd2913\";}i:3;a:4:{s:9:\"view_name\";s:32:\"d318a62bab1248c9a46f1d6767ceaeb4\";s:4:\"meta\";a:3:{s:4:\"time\";i:1096621286;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:8:\"usradmin\";s:3:\"vid\";s:32:\"3b6234e7773e2785ed2b5d285bed3403\";}i:4;a:4:{s:9:\"view_name\";s:32:\"b25f8fdc3465492a053f7cd3edfea270\";s:4:\"meta\";a:3:{s:4:\"time\";i:1098967440;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:8:\"usradmin\";s:3:\"vid\";s:32:\"0f0d85b807a2a49bce724595fccfad25\";}i:5;a:4:{s:9:\"view_name\";s:3:\"pap\";s:4:\"meta\";a:3:{s:4:\"time\";i:1100773745;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:4:\"page\";s:3:\"vid\";s:32:\"49043c7a1be12f1e2fc41751c6c6e4ad\";}i:6;a:4:{s:9:\"view_name\";s:4:\"grid\";s:4:\"meta\";a:3:{s:4:\"time\";i:1121701721;s:5:\"uname\";s:6:\"german\";s:3:\"uid\";s:3:\"199\";}s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"72d4e8288cea6c9f971c813b34b32966\";}i:7;a:4:{s:9:\"view_name\";s:7:\"article\";s:4:\"meta\";a:3:{s:4:\"time\";i:1126171489;s:5:\"uname\";s:7:\"germany\";s:3:\"uid\";s:3:\"199\";}s:8:\"mod_name\";s:7:\"article\";s:3:\"vid\";s:32:\"1fe047cf04de5268745df64e9cff5605\";}i:8;a:4:{s:9:\"view_name\";s:0:\"\";s:4:\"meta\";a:3:{s:4:\"time\";i:1126523207;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:5:\"login\";s:3:\"vid\";s:32:\"bab81032f1199b36f755a27d4760923e\";}i:9;a:4:{s:9:\"view_name\";s:4:\"grid\";s:4:\"meta\";a:3:{s:4:\"time\";i:1138813838;s:5:\"uname\";s:7:\"english\";s:3:\"uid\";s:3:\"199\";}s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"0be7c48893dfff8857996e61036f3610\";}i:10;a:4:{s:9:\"view_name\";s:0:\"\";s:4:\"meta\";a:3:{s:4:\"time\";i:1142002098;s:5:\"uname\";N;s:3:\"uid\";N;}s:8:\"mod_name\";s:12:\"pn_imgteaser\";s:3:\"vid\";s:32:\"fed65bbc1157763651381e81abfe0843\";}}',0,0,0,NULL,NULL,NULL,NULL),
	(348,'Welcome','welcome.html','','','',3,14,1,370,0,'contrails.php',0,'a:2:{i:1;a:0:{}i:0;a:1:{i:0;a:3:{s:9:\"view_name\";N;s:8:\"mod_name\";s:8:\"usradmin\";s:3:\"vid\";s:32:\"5373ce84a31fa49179ae403fa8da4edc\";}}}','a:7:{i:0;a:4:{s:9:\"view_name\";s:7:\"article\";s:4:\"meta\";a:3:{s:4:\"time\";i:1126171542;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:7:\"article\";s:3:\"vid\";s:32:\"ba67c985d90efe1ed69ada2c20dd7f50\";}i:1;a:4:{s:9:\"view_name\";s:0:\"\";s:4:\"meta\";a:3:{s:4:\"time\";i:1126523803;s:5:\"uname\";s:7:\"english\";s:3:\"uid\";s:3:\"199\";}s:8:\"mod_name\";s:5:\"login\";s:3:\"vid\";s:32:\"11cb1e7be47205aff8936cf1e258f267\";}i:2;a:4:{s:9:\"view_name\";s:0:\"\";s:4:\"meta\";a:3:{s:4:\"time\";i:1142002146;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:4:\"page\";s:3:\"vid\";s:32:\"d7655f10b07abdc07beedcac3d520412\";}i:3;a:4:{s:9:\"view_name\";s:4:\"grid\";s:4:\"meta\";a:3:{s:4:\"time\";i:1264193476;s:5:\"uname\";s:4:\"Gast\";s:3:\"uid\";s:3:\"200\";}s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"524538288a0b8c76b25be03e79e12cc9\";}i:4;a:4:{s:9:\"view_name\";N;s:4:\"meta\";a:3:{s:4:\"time\";i:1265183592;s:5:\"uname\";s:5:\"Guest\";s:3:\"uid\";s:3:\"200\";}s:8:\"mod_name\";s:7:\"menubar\";s:3:\"vid\";s:32:\"177aabffd31435fd336d5154bcc54d9c\";}i:5;a:4:{s:9:\"view_name\";s:3:\"pap\";s:4:\"meta\";a:3:{s:4:\"time\";i:1346527841;s:5:\"uname\";s:5:\"guest\";s:3:\"uid\";s:3:\"200\";}s:8:\"mod_name\";s:4:\"page\";s:3:\"vid\";s:32:\"d65eb69c195ee1fe23e6ff68550bda34\";}i:6;a:4:{s:9:\"view_name\";N;s:4:\"meta\";a:3:{s:4:\"time\";i:1346530017;s:5:\"uname\";s:5:\"guest\";s:3:\"uid\";s:3:\"200\";}s:8:\"mod_name\";s:8:\"usradmin\";s:3:\"vid\";s:32:\"5373ce84a31fa49179ae403fa8da4edc\";}}',0,0,0,NULL,NULL,NULL,NULL),
	(349,'Administration','','','','',17,22,1,370,0,'contrails.php',0,'a:2:{i:1;a:0:{}i:0;a:3:{i:0;a:3:{s:9:\"view_name\";s:3:\"pap\";s:8:\"mod_name\";s:4:\"page\";s:3:\"vid\";s:32:\"5348152a7c88737b4d922e3f378c082a\";}i:1;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"c175f4c81e32046ada0f31d6d1759844\";}i:2;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"c175f4c81e32046ada0f31d6d1759844\";}}}','a:3:{i:0;a:4:{s:9:\"view_name\";s:0:\"\";s:4:\"meta\";a:3:{s:4:\"time\";i:1126523750;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:5:\"login\";s:3:\"vid\";s:32:\"455846f507463bf4c745b9857a176db7\";}i:1;a:4:{s:9:\"view_name\";s:0:\"\";s:4:\"meta\";a:3:{s:4:\"time\";i:1142002148;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:12:\"pn_imgteaser\";s:3:\"vid\";s:32:\"f1bcb8f7432382cf39790152971b1cf7\";}i:2;a:4:{s:9:\"view_name\";N;s:4:\"meta\";a:3:{s:4:\"time\";i:1265183590;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:7:\"menubar\";s:3:\"vid\";s:32:\"9e8228748dbd4d61e0b49dc25b7fe0a0\";}}',0,0,0,NULL,NULL,NULL,NULL),
	(367,'Benutzer/Gruppen','','','','',18,19,1,349,0,'contrails.php',0,'a:2:{i:1;a:0:{}i:0;a:3:{i:0;a:3:{s:9:\"view_name\";s:3:\"pap\";s:8:\"mod_name\";s:4:\"page\";s:3:\"vid\";s:32:\"73592f4fed5d66f837851fe00ac7c391\";}i:1;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"44cb6a24ed66423899d37dd1c080c56a\";}i:2;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"44cb6a24ed66423899d37dd1c080c56a\";}}}','a:1:{i:0;a:4:{s:9:\"view_name\";N;s:4:\"meta\";a:3:{s:4:\"time\";i:1265184099;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:7:\"menubar\";s:3:\"vid\";s:32:\"71151e7330ec9d7f496f5c47c5ae20d9\";}}',0,0,0,NULL,NULL,NULL,NULL),
	(368,'Register','','','','',15,16,1,370,0,'contrails.php',0,'a:2:{i:1;a:0:{}i:0;a:3:{i:0;a:3:{s:9:\"view_name\";s:3:\"pap\";s:8:\"mod_name\";s:4:\"page\";s:3:\"vid\";s:32:\"4a28e4358a7fcb17fb12b61334c57b85\";}i:1;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"bc529107a21939daffb3e5e7c479bc72\";}i:2;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"bc529107a21939daffb3e5e7c479bc72\";}}}','a:3:{i:0;a:4:{s:9:\"view_name\";N;s:4:\"meta\";a:3:{s:4:\"time\";i:1264194042;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:7:\"menubar\";s:3:\"vid\";s:32:\"ed12784f592f31f21e81f84cc3c8d4c5\";}i:1;a:4:{s:9:\"view_name\";s:4:\"grid\";s:4:\"meta\";a:3:{s:4:\"time\";i:1264328198;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"bc529107a21939daffb3e5e7c479bc72\";}i:2;a:4:{s:9:\"view_name\";N;s:4:\"meta\";a:3:{s:4:\"time\";i:1265183788;s:5:\"uname\";s:5:\"Guest\";s:3:\"uid\";s:3:\"200\";}s:8:\"mod_name\";s:7:\"menubar\";s:3:\"vid\";s:32:\"cdf87c3d06629f291d309be6b78e76e6\";}}',0,0,0,NULL,NULL,NULL,NULL),
	(370,'Start','','','','',2,25,1,1,0,'contrails.php',0,'a:1:{i:1;a:2:{i:0;a:3:{s:9:\"view_name\";s:3:\"pap\";s:8:\"mod_name\";s:4:\"page\";s:3:\"vid\";s:32:\"ba8166bbc44ae0ba823b088dba381837\";}i:2;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"574ac8132213cbc5099aefece95f47d9\";}}}','',0,0,0,NULL,NULL,NULL,NULL),
	(378,'Imprint','','','','',23,24,1,370,0,'contrails.php',0,'a:1:{i:0;a:2:{i:0;a:3:{s:9:\"view_name\";s:3:\"pap\";s:8:\"mod_name\";s:4:\"page\";s:3:\"vid\";s:32:\"418f69881323b10594d73b5996bf193e\";}i:1;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"f29684b88d84d04de510519abe960fc4\";}}}','',0,0,0,NULL,NULL,NULL,NULL),
	(377,'Modules','','','','',20,21,1,349,0,'contrails.php',0,'a:1:{i:0;a:3:{i:0;a:3:{s:9:\"view_name\";s:3:\"pap\";s:8:\"mod_name\";s:4:\"page\";s:3:\"vid\";s:32:\"d342ea57645efb375af08dd0c2a75165\";}i:1;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"0757f78c38e5f4ee7c6ce9f60e5e0f1a\";}i:2;a:3:{s:9:\"view_name\";s:4:\"grid\";s:8:\"mod_name\";s:4:\"grid\";s:3:\"vid\";s:32:\"0757f78c38e5f4ee7c6ce9f60e5e0f1a\";}}}','a:1:{i:0;a:4:{s:9:\"view_name\";N;s:4:\"meta\";a:3:{s:4:\"time\";i:1265383870;s:5:\"uname\";s:4:\"root\";s:3:\"uid\";s:3:\"122\";}s:8:\"mod_name\";s:7:\"menubar\";s:3:\"vid\";s:32:\"e4f4db3a4e77fe958aa8303a60d1a016\";}}',0,0,0,NULL,NULL,NULL,NULL),
	(379,'',NULL,NULL,NULL,NULL,4,5,1,0,0,'',0,'N;','',0,0,0,NULL,NULL,NULL,NULL),
	(380,'',NULL,NULL,NULL,NULL,6,7,1,0,0,'',0,'','',0,0,0,NULL,NULL,NULL,NULL),
	(381,'',NULL,NULL,NULL,NULL,8,9,1,0,0,'',0,'','',0,0,0,NULL,NULL,NULL,NULL),
	(382,'',NULL,NULL,NULL,NULL,10,11,1,0,0,'',0,'N;','',0,0,0,NULL,NULL,NULL,NULL),
	(383,'adsf2','adsf2','adsf2','','',12,13,1,348,0,'contrails.php',0,'','',0,0,0,'',0,'',0);

/*!40000 ALTER TABLE `mod_page` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mod_page_acl
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mod_page_acl`;

CREATE TABLE `mod_page_acl` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` int(1) unsigned NOT NULL DEFAULT '0',
  `aid` int(10) unsigned NOT NULL DEFAULT '0',
  `ar` int(32) unsigned NOT NULL DEFAULT '0',
  `inherit_pid` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `pid` (`id`,`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `mod_page_acl` WRITE;
/*!40000 ALTER TABLE `mod_page_acl` DISABLE KEYS */;

INSERT INTO `mod_page_acl` (`id`, `type`, `aid`, `ar`, `inherit_pid`)
VALUES
	(368,1,68,1,1),
	(348,1,68,1,1),
	(370,1,68,1,1),
	(1,1,68,1,1),
	(348,1,66,1,1),
	(378,1,66,1,1),
	(368,2,200,1,1),
	(370,1,66,1,1),
	(1,1,66,1,1),
	(348,2,200,1,1),
	(370,2,200,1,1),
	(368,1,66,1,1),
	(378,2,200,1,1),
	(1,2,200,1,1),
	(378,1,68,1,1),
	(379,1,66,1,348),
	(379,1,68,1,348),
	(379,2,200,1,348),
	(380,1,66,1,348),
	(380,1,68,1,348),
	(380,2,200,1,348),
	(382,1,66,1,348),
	(382,1,68,1,348),
	(382,2,200,1,348),
	(383,1,66,1,348),
	(383,1,68,1,348),
	(383,2,200,1,348);

/*!40000 ALTER TABLE `mod_page_acl` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mod_page_mod_grp_ar
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mod_page_mod_grp_ar`;

CREATE TABLE `mod_page_mod_grp_ar` (
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `gid` int(10) unsigned NOT NULL DEFAULT '0',
  `mid` int(10) unsigned NOT NULL DEFAULT '0',
  `ar` int(32) unsigned NOT NULL DEFAULT '0',
  `inherit_pid` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `pid` (`pid`,`gid`,`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table mod_page_mod_usr_ar
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mod_page_mod_usr_ar`;

CREATE TABLE `mod_page_mod_usr_ar` (
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `mid` int(10) unsigned NOT NULL DEFAULT '0',
  `ar` int(32) unsigned NOT NULL DEFAULT '0',
  `inherit_pid` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `pid` (`pid`,`uid`,`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table mod_page_tpl
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mod_page_tpl`;

CREATE TABLE `mod_page_tpl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tpl_name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `sys_trashcan` smallint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `mod_page_tpl` WRITE;
/*!40000 ALTER TABLE `mod_page_tpl` DISABLE KEYS */;

INSERT INTO `mod_page_tpl` (`id`, `tpl_name`, `label`, `sys_trashcan`)
VALUES
	(6,'contrails.php','CONTRAILS',0);

/*!40000 ALTER TABLE `mod_page_tpl` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mod_usradmin_grp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mod_usradmin_grp`;

CREATE TABLE `mod_usradmin_grp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `lang` int(2) NOT NULL DEFAULT '0',
  `sys_trashcan` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `mod_usradmin_grp` WRITE;
/*!40000 ALTER TABLE `mod_usradmin_grp` DISABLE KEYS */;

INSERT INTO `mod_usradmin_grp` (`id`, `pid`, `name`, `lang`, `sys_trashcan`)
VALUES
	(66,367,'Registered',1,0),
	(68,367,'Guest',1,0);

/*!40000 ALTER TABLE `mod_usradmin_grp` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mod_usradmin_usr
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mod_usradmin_usr`;

CREATE TABLE `mod_usradmin_usr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `show_id` varchar(16) NOT NULL,
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `usr` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `tel` varchar(40) NOT NULL,
  `fax` varchar(40) NOT NULL,
  `street` varchar(255) NOT NULL,
  `num` varchar(10) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` char(2) NOT NULL DEFAULT 'de',
  `lang` tinyint(2) NOT NULL DEFAULT '0',
  `lang_default` tinyint(1) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `register_key` varchar(64) NOT NULL,
  `accept` tinyint(1) NOT NULL DEFAULT '0',
  `sys_trashcan` smallint(1) unsigned NOT NULL DEFAULT '0',
  `sys_date_created` int(10) unsigned NOT NULL DEFAULT '0',
  `sys_date_changed` int(10) unsigned NOT NULL DEFAULT '0',
  `sys_date_lastlogin` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `mod_usradmin_usr` WRITE;
/*!40000 ALTER TABLE `mod_usradmin_usr` DISABLE KEYS */;

INSERT INTO `mod_usradmin_usr` (`id`, `show_id`, `pid`, `usr`, `pwd`, `name`, `email`, `tel`, `fax`, `street`, `num`, `zip`, `city`, `country`, `lang`, `lang_default`, `type`, `register_key`, `accept`, `sys_trashcan`, `sys_date_created`, `sys_date_changed`, `sys_date_lastlogin`)
VALUES
	(122,'122',348,'root','a029d0df84eb5549c641e04a9ef389e5','','','','','','','','','AF',1,0,0,'',0,0,0,1147172573,1265383859),
	(200,'200',357,'guest','a6d414ac4f293187dd042025834925f7','','','','','','','','','DE',1,1,0,'',0,0,1126541294,1265383381,0);

/*!40000 ALTER TABLE `mod_usradmin_usr` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_burc
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_burc`;

CREATE TABLE `sys_burc` (
  `burc` varchar(20) NOT NULL,
  `pid` int(11) NOT NULL,
  `data` text NOT NULL,
  `sys_date_created` int(11) NOT NULL,
  `permanent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`burc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `sys_burc` WRITE;
/*!40000 ALTER TABLE `sys_burc` DISABLE KEYS */;

INSERT INTO `sys_burc` (`burc`, `pid`, `data`, `sys_date_created`, `permanent`)
VALUES
	('p1409228737_348',348,'a:4:{s:3:\"pid\";i:348;s:3:\"mod\";s:4:\"page\";s:5:\"event\";s:5:\"pa_ar\";s:4:\"data\";a:1:{s:3:\"pid\";i:348;}}',1348350503,0),
	('p2460342256_348',348,'a:4:{s:3:\"pid\";i:348;s:3:\"mod\";s:4:\"page\";s:5:\"event\";s:7:\"pa_type\";s:8:\"edit_pid\";i:348;}',1348350503,0),
	('p4269505774_348',348,'a:5:{s:3:\"pid\";i:348;s:3:\"mod\";s:8:\"acladmin\";s:5:\"event\";s:8:\"acl_list\";s:7:\"edit_id\";i:348;s:4:\"data\";a:1:{s:3:\"tbl\";s:8:\"mod_page\";}}',1348350503,0),
	('p2314348126_348',348,'a:5:{s:3:\"pid\";i:348;s:3:\"mod\";s:4:\"page\";s:5:\"event\";s:8:\"pa_enter\";s:8:\"edit_pid\";i:348;s:4:\"data\";a:1:{s:10:\"parent_pid\";i:348;}}',1348350503,0),
	('p108071938_348',348,'a:3:{s:3:\"pid\";i:348;s:5:\"event\";s:6:\"logout\";s:3:\"mod\";s:8:\"usradmin\";}',1348350503,0),
	('p1603634944_348',348,'a:3:{s:3:\"pid\";i:348;s:5:\"event\";s:4:\"test\";s:3:\"mod\";s:4:\"test\";}',1348350503,0);

/*!40000 ALTER TABLE `sys_burc` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_log`;

CREATE TABLE `sys_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `project` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `pid` int(11) NOT NULL,
  `mod` varchar(255) NOT NULL,
  `event` varchar(255) NOT NULL,
  `files` text NOT NULL,
  `post` text NOT NULL,
  `get` text NOT NULL,
  `ip` varchar(30) NOT NULL,
  `session` varchar(40) NOT NULL,
  `browser` text NOT NULL,
  `uid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `referer` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `sys_log` WRITE;
/*!40000 ALTER TABLE `sys_log` DISABLE KEYS */;

INSERT INTO `sys_log` (`id`, `time`, `project`, `url`, `pid`, `mod`, `event`, `files`, `post`, `get`, `ip`, `session`, `browser`, `uid`, `name`, `referer`)
VALUES
	(1,1348350503,'contrails','contrails.local',348,'','','YTowOnt9','YTowOnt9','YTozOntzOjQ6ImZpbGUiO3M6MDoiIjtzOjQ6ImJ1cmMiO2E6MTp7czo1OiJlcnJvciI7YjoxO31zOjM6InBpZCI7aTozNDg7fQ==','127.0.0.1','8c85b4970f6f96f5f8c573ed14e0fdee','Array',122,'root',''),
	(2,1348350503,'contrails','contrails.local',348,'','','YTowOnt9','YTowOnt9','YTozOntzOjQ6ImZpbGUiO3M6MTE6ImZhdmljb24uaWNvIjtzOjQ6ImJ1cmMiO2E6MTp7czo1OiJlcnJvciI7YjoxO31zOjM6InBpZCI7aTozNDg7fQ==','127.0.0.1','8c85b4970f6f96f5f8c573ed14e0fdee','Array',122,'root',''),
	(3,1348350505,'contrails','contrails.local',348,'usradmin','logout','YTowOnt9','YTowOnt9','YTo1OntzOjQ6ImZpbGUiO3M6Mjc6IndlbGNvbWUtcDEwODA3MTkzOF8zNDguaHRtbCI7czozOiJwaWQiO2k6MzQ4O3M6NToiZXZlbnQiO3M6NjoibG9nb3V0IjtzOjM6Im1vZCI7czo4OiJ1c3JhZG1pbiI7czo0OiJidXJjIjthOjE6e3M6NToiZXJyb3IiO2I6MDt9fQ==','127.0.0.1','8c85b4970f6f96f5f8c573ed14e0fdee','Array',122,'root','http://contrails.local/'),
	(4,1348350505,'contrails','contrails.local',348,'','','YTowOnt9','YTowOnt9','YTozOntzOjQ6ImZpbGUiO3M6MDoiIjtzOjQ6ImJ1cmMiO2E6MTp7czo1OiJlcnJvciI7YjoxO31zOjM6InBpZCI7aTozNDg7fQ==','127.0.0.1','8c85b4970f6f96f5f8c573ed14e0fdee','Array',200,'guest','http://contrails.local/'),
	(5,1348350505,'contrails','contrails.local',348,'','','YTowOnt9','YTowOnt9','YTozOntzOjQ6ImZpbGUiO3M6MTE6ImZhdmljb24uaWNvIjtzOjQ6ImJ1cmMiO2E6MTp7czo1OiJlcnJvciI7YjoxO31zOjM6InBpZCI7aTozNDg7fQ==','127.0.0.1','8c85b4970f6f96f5f8c573ed14e0fdee','Array',200,'guest',''),
	(6,1348350512,'contrails','contrails.local',348,'','','YTowOnt9','YTowOnt9','YTozOntzOjQ6ImZpbGUiO3M6MDoiIjtzOjQ6ImJ1cmMiO2E6MTp7czo1OiJlcnJvciI7YjoxO31zOjM6InBpZCI7aTozNDg7fQ==','127.0.0.1','8c85b4970f6f96f5f8c573ed14e0fdee','Array',200,'guest',''),
	(7,1348350512,'contrails','contrails.local',348,'','','YTowOnt9','YTowOnt9','YTozOntzOjQ6ImZpbGUiO3M6MTE6ImZhdmljb24uaWNvIjtzOjQ6ImJ1cmMiO2E6MTp7czo1OiJlcnJvciI7YjoxO31zOjM6InBpZCI7aTozNDg7fQ==','127.0.0.1','8c85b4970f6f96f5f8c573ed14e0fdee','Array',200,'guest','');

/*!40000 ALTER TABLE `sys_log` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_mc_listeners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_mc_listeners`;

CREATE TABLE `sys_mc_listeners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mod_shout` varchar(200) NOT NULL,
  `event_shout` varchar(200) NOT NULL,
  `mod_listen` varchar(200) NOT NULL,
  `event_listen` varchar(200) NOT NULL,
  `att_listen` text,
  `start` int(11) NOT NULL DEFAULT '0',
  `stop` int(11) NOT NULL DEFAULT '2147483647',
  `pre` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `sys_mc_listeners` WRITE;
/*!40000 ALTER TABLE `sys_mc_listeners` DISABLE KEYS */;

INSERT INTO `sys_mc_listeners` (`id`, `mod_shout`, `event_shout`, `mod_listen`, `event_listen`, `att_listen`, `start`, `stop`, `pre`)
VALUES
	(1,'page','pa_save','navigation_1','inherit','a:1:{s:3:\"vid\";s:15:\"main_navigation\";}',0,2147483647,1);

/*!40000 ALTER TABLE `sys_mc_listeners` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_module
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_module`;

CREATE TABLE `sys_module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modul_name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `sys_trashcan` smallint(1) unsigned NOT NULL DEFAULT '0',
  `virtual` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `sys_module` WRITE;
/*!40000 ALTER TABLE `sys_module` DISABLE KEYS */;

INSERT INTO `sys_module` (`id`, `modul_name`, `label`, `sys_trashcan`, `virtual`)
VALUES
	(9,'page','PAGE',0,1),
	(46,'acladmin','ACLADMIN',0,1),
	(48,'objbrowser','OBJBROWSER',0,1);

/*!40000 ALTER TABLE `sys_module` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_vid
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_vid`;

CREATE TABLE `sys_vid` (
  `vid` varchar(32) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `mod_name` varchar(100) NOT NULL,
  PRIMARY KEY (`vid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
