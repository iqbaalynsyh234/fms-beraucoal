-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 22, 2011 at 07:08 PM
-- Server version: 5.1.33
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `webtracking`
--

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_agent`
--

CREATE TABLE IF NOT EXISTS `webtracking_agent` (
  `agent_id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_name` varchar(100) DEFAULT NULL,
  `agent_status` int(11) DEFAULT NULL,
  `agent_site` varchar(100) DEFAULT NULL,
  `agent_canedit_vactive` int(11) DEFAULT '0',
  `agent_mail` varchar(100) DEFAULT NULL,
  `agent_mail_name` varchar(100) DEFAULT NULL,
  `agent_pascabayar` int(11) NOT NULL,
  `agent_site_backup` varchar(100) NOT NULL,
  PRIMARY KEY (`agent_id`),
  UNIQUE KEY `NewIndex1` (`agent_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_alarm`
--

CREATE TABLE IF NOT EXISTS `webtracking_alarm` (
  `alarm_id` int(11) NOT NULL AUTO_INCREMENT,
  `alarm_user_id` int(11) DEFAULT NULL,
  `alarm_gps_info_id` int(11) DEFAULT NULL,
  `alarm_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`alarm_id`),
  KEY `NewIndex1` (`alarm_user_id`,`alarm_gps_info_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=468 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_announcement`
--

CREATE TABLE IF NOT EXISTS `webtracking_announcement` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_message` text,
  `announcement_owner` varchar(100) DEFAULT NULL,
  `announcement_creator` int(11) DEFAULT NULL,
  `announcement_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `announcement_status` int(11) DEFAULT '1',
  PRIMARY KEY (`announcement_id`),
  KEY `NewIndex1` (`announcement_owner`,`announcement_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_bank`
--

CREATE TABLE IF NOT EXISTS `webtracking_bank` (
  `bank_id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_acc` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_branch` varchar(255) DEFAULT NULL,
  `bank_order` int(11) DEFAULT NULL,
  `bank_agent` int(11) DEFAULT NULL COMMENT 'seperti pln, harus bayar ke bank_code adilahsoft',
  PRIMARY KEY (`bank_id`),
  KEY `bank_agent_index` (`bank_agent`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_cctv`
--

CREATE TABLE IF NOT EXISTS `webtracking_cctv` (
  `cctv_id` int(11) NOT NULL AUTO_INCREMENT,
  `cctv_lat` varchar(10) DEFAULT NULL,
  `cctv_lon` varchar(10) DEFAULT NULL,
  `cctv_src` varchar(100) DEFAULT NULL,
  `cctv_tag` varchar(100) DEFAULT NULL,
  `cctv_name` varchar(100) DEFAULT NULL,
  `cctv_status` int(11) DEFAULT '1',
  `cctv_desc` text,
  `cctv_alias` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cctv_id`),
  UNIQUE KEY `NewIndex1` (`cctv_lat`,`cctv_lon`),
  KEY `NewIndex2` (`cctv_tag`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_company`
--

CREATE TABLE IF NOT EXISTS `webtracking_company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(100) NOT NULL,
  `company_agent` int(11) NOT NULL,
  `company_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `company_site` varchar(100) NOT NULL,
  PRIMARY KEY (`company_id`),
  UNIQUE KEY `company_uniq` (`company_name`,`company_agent`),
  KEY `company_name_index` (`company_name`),
  FULLTEXT KEY `company_site_index` (`company_site`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_config`
--

CREATE TABLE IF NOT EXISTS `webtracking_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(100) DEFAULT NULL,
  `config_value` text,
  `config_lastmodified` timestamp NULL DEFAULT NULL,
  `config_lastmodifier` int(11) DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_contactus`
--

CREATE TABLE IF NOT EXISTS `webtracking_contactus` (
  `contactus_id` int(11) NOT NULL AUTO_INCREMENT,
  `contactus_subject` varchar(255) DEFAULT NULL,
  `contactus_message` text,
  `contactus_creator` int(11) DEFAULT NULL,
  `contactus_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `contactus_status` int(11) DEFAULT '1',
  PRIMARY KEY (`contactus_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_desa`
--

CREATE TABLE IF NOT EXISTS `webtracking_desa` (
  `ID` int(11) NOT NULL,
  `DESA` varchar(255) DEFAULT NULL,
  `KECAMATAN` varchar(255) DEFAULT NULL,
  `KAB_KOTA` varchar(255) DEFAULT NULL,
  `PROPINSI` varchar(255) DEFAULT NULL,
  `KODE` varchar(255) DEFAULT NULL,
  `ogc_geom` geometry DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_geofence`
--

CREATE TABLE IF NOT EXISTS `webtracking_geofence` (
  `geofence_id` int(11) NOT NULL AUTO_INCREMENT,
  `geofence_vehicle` varchar(100) DEFAULT NULL,
  `geofence_coordinate` varchar(255) DEFAULT NULL,
  `geofence_json` text,
  `geofence_user` int(11) DEFAULT NULL,
  `geofence_status` int(11) DEFAULT '1' COMMENT '1=aktif, 2=historical',
  `geofence_created` timestamp NULL DEFAULT NULL,
  `geofence_deleted` timestamp NULL DEFAULT NULL,
  `geofence_polygon` geometry DEFAULT NULL,
  `geofence_name` varchar(255) NOT NULL,
  PRIMARY KEY (`geofence_id`),
  KEY `NewIndex1` (`geofence_vehicle`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=136 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `gps_vihicle` (`gps_name`),
  KEY `NewIndex2` (`gps_host`),
  KEY `NewIndex3` (`gps_name`,`gps_host`),
  KEY `NewIndex1` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58352412 ;


CREATE TABLE IF NOT EXISTS `webtracking_gps_agungputra` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `gps_vihicle` (`gps_name`),
  KEY `NewIndex2` (`gps_host`),
  KEY `NewIndex3` (`gps_name`,`gps_host`),
  KEY `NewIndex1` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14990283 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_config`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_config` (
  `gps_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_config_interval_move` int(11) DEFAULT NULL,
  `gps_config_interval_park` int(11) DEFAULT NULL,
  `gps_config_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gps_config_device` varchar(100) DEFAULT NULL,
  `gps_config_speed_limit` int(11) DEFAULT NULL,
  `gps_config_geo_fence` int(11) DEFAULT NULL,
  `gps_config_ext_func` int(11) DEFAULT NULL,
  `gps_config_power_saving` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_error`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_error` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `NewIndex1` (`gps_name`,`gps_host`),
  KEY `NewIndex3` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_utc_coord`,`gps_utc_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=666218 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_farrasindo`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_farrasindo` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `gps_vihicle` (`gps_name`),
  KEY `NewIndex2` (`gps_host`),
  KEY `NewIndex3` (`gps_name`,`gps_host`),
  KEY `NewIndex1` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4821942 ;


-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_gtp`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_gtp` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL,
  `gps_status` char(1) DEFAULT NULL,
  `gps_latitude` varchar(16) DEFAULT NULL,
  `gps_ns` char(1) DEFAULT NULL,
  `gps_longitude` varchar(16) DEFAULT NULL,
  `gps_ew` char(1) DEFAULT NULL,
  `gps_speed` float DEFAULT NULL,
  `gps_course` float DEFAULT NULL,
  `gps_utc_date` int(11) DEFAULT NULL,
  `gps_mvd` float DEFAULT NULL,
  `gps_mv` char(1) DEFAULT NULL,
  `gps_cs` varchar(100) DEFAULT NULL,
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11220706 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_gtp_andalas`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_gtp_andalas` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL,
  `gps_status` char(1) DEFAULT NULL,
  `gps_latitude` varchar(16) DEFAULT NULL,
  `gps_ns` char(1) DEFAULT NULL,
  `gps_longitude` varchar(16) DEFAULT NULL,
  `gps_ew` char(1) DEFAULT NULL,
  `gps_speed` float DEFAULT NULL,
  `gps_course` float DEFAULT NULL,
  `gps_utc_date` int(11) DEFAULT NULL,
  `gps_mvd` float DEFAULT NULL,
  `gps_mv` char(1) DEFAULT NULL,
  `gps_cs` varchar(100) DEFAULT NULL,
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2522 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_gtp_error`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_gtp_error` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL,
  `gps_status` char(1) DEFAULT NULL,
  `gps_latitude` varchar(16) DEFAULT NULL,
  `gps_ns` char(1) DEFAULT NULL,
  `gps_longitude` varchar(16) DEFAULT NULL,
  `gps_ew` char(1) DEFAULT NULL,
  `gps_speed` float DEFAULT NULL,
  `gps_course` float DEFAULT NULL,
  `gps_utc_date` int(11) DEFAULT NULL,
  `gps_mvd` float DEFAULT NULL,
  `gps_mv` char(1) DEFAULT NULL,
  `gps_cs` varchar(100) DEFAULT NULL,
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_gtp_new`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_gtp_new` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL,
  `gps_status` char(1) DEFAULT NULL,
  `gps_latitude` varchar(16) DEFAULT NULL,
  `gps_ns` char(1) DEFAULT NULL,
  `gps_longitude` varchar(16) DEFAULT NULL,
  `gps_ew` char(1) DEFAULT NULL,
  `gps_speed` float DEFAULT NULL,
  `gps_course` float DEFAULT NULL,
  `gps_utc_date` int(11) DEFAULT NULL,
  `gps_mvd` float DEFAULT NULL,
  `gps_mv` char(1) DEFAULT NULL,
  `gps_cs` varchar(100) DEFAULT NULL,
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7243441 ;


-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_hist`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_hist` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `gps_vihicle` (`gps_name`),
  KEY `NewIndex2` (`gps_host`),
  KEY `NewIndex3` (`gps_name`,`gps_host`),
  KEY `NewIndex1` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=151877544 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_indogps`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_indogps` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL,
  `gps_status` char(1) DEFAULT NULL,
  `gps_latitude` varchar(16) DEFAULT NULL,
  `gps_ns` char(1) DEFAULT NULL,
  `gps_longitude` varchar(16) DEFAULT NULL,
  `gps_ew` char(1) DEFAULT NULL,
  `gps_speed` float DEFAULT NULL,
  `gps_course` float DEFAULT NULL,
  `gps_utc_date` int(11) DEFAULT NULL,
  `gps_mvd` float DEFAULT NULL,
  `gps_mv` char(1) DEFAULT NULL,
  `gps_cs` varchar(100) DEFAULT NULL,
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2212112 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info_agungputra`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_agungputra` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info_farrasindo`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_farrasindo` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4815005 ;


-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info_gtp`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_gtp` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11202914 ;


-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info_gtp_andalas`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_gtp_andalas` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2522 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info_gtp_new`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_gtp_new` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7232033 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info_hist`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_hist` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19234160 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info_indogps`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_indogps` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info_pln`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_pln` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info_sms`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_sms` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=66 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_info_t1_1`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_t1_1` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=954 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_pln`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_pln` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `gps_vihicle` (`gps_name`),
  KEY `NewIndex2` (`gps_host`),
  KEY `NewIndex3` (`gps_name`,`gps_host`),
  KEY `NewIndex1` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=869476 ;


-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_pln_error`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_pln_error` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `gps_vihicle` (`gps_name`),
  KEY `NewIndex2` (`gps_host`),
  KEY `NewIndex3` (`gps_name`,`gps_host`),
  KEY `NewIndex1` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=714958 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_sms`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_sms` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL,
  `gps_status` char(1) DEFAULT NULL,
  `gps_latitude` varchar(16) DEFAULT NULL,
  `gps_ns` char(1) DEFAULT NULL,
  `gps_longitude` varchar(16) DEFAULT NULL,
  `gps_ew` char(1) DEFAULT NULL,
  `gps_speed` float DEFAULT NULL,
  `gps_course` float DEFAULT NULL,
  `gps_utc_date` int(11) DEFAULT NULL,
  `gps_mvd` float DEFAULT NULL,
  `gps_mv` char(1) DEFAULT NULL,
  `gps_cs` varchar(100) DEFAULT NULL,
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6705135 ;


-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_t1_1`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_t1_1` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `gps_vihicle` (`gps_name`),
  KEY `NewIndex2` (`gps_host`),
  KEY `NewIndex3` (`gps_name`,`gps_host`),
  KEY `NewIndex1` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17318813 ;


-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_t1_1_error`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_t1_1_error` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `gps_vihicle` (`gps_name`),
  KEY `NewIndex2` (`gps_host`),
  KEY `NewIndex3` (`gps_name`,`gps_host`),
  KEY `NewIndex1` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5273273 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_gps_temp`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_temp` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `gps_vihicle` (`gps_name`),
  KEY `NewIndex2` (`gps_host`),
  KEY `NewIndex3` (`gps_name`,`gps_host`),
  KEY `NewIndex1` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=89494986 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_group`
--

CREATE TABLE IF NOT EXISTS `webtracking_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) NOT NULL,
  `group_parent` int(11) NOT NULL,
  `group_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_creator` int(11) NOT NULL,
  `group_status` int(1) NOT NULL,
  `group_company` int(11) NOT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name_uniq` (`group_name`),
  KEY `group_company_index` (`group_company`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_jalan`
--

CREATE TABLE IF NOT EXISTS `webtracking_jalan` (
  `ID` int(11) NOT NULL,
  `TYPE` int(11) DEFAULT NULL,
  `LABEL` varchar(255) DEFAULT NULL,
  `ogc_geom` geometry DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_jalanext`
--

CREATE TABLE IF NOT EXISTS `webtracking_jalanext` (
  `ID` int(11) NOT NULL,
  `LABEL` varchar(255) DEFAULT NULL,
  `DESKRIPSI` varchar(255) DEFAULT NULL,
  `ogc_geom` geometry DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_kabkota`
--

CREATE TABLE IF NOT EXISTS `webtracking_kabkota` (
  `ID` int(11) NOT NULL,
  `KAB_KOTA` varchar(255) DEFAULT NULL,
  `PROPINSI` varchar(255) DEFAULT NULL,
  `ogc_geom` geometry DEFAULT NULL,
  `kabkota_status` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `kabkota_provinsi_index` (`PROPINSI`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_kecamatan`
--

CREATE TABLE IF NOT EXISTS `webtracking_kecamatan` (
  `ID` int(11) NOT NULL,
  `LABEL` varchar(255) DEFAULT NULL,
  `KABUPATEN` varchar(255) DEFAULT NULL,
  `ogc_geom` geometry DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_location`
--

CREATE TABLE IF NOT EXISTS `webtracking_location` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_lat` varchar(10) DEFAULT NULL,
  `location_lng` varchar(10) DEFAULT NULL,
  `location_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  UNIQUE KEY `NewIndex1` (`location_lat`,`location_lng`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=120022 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_log`
--

CREATE TABLE IF NOT EXISTS `webtracking_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `log_creator` int(11) DEFAULT NULL,
  `log_type` varchar(100) DEFAULT NULL,
  `log_ip` varchar(100) DEFAULT NULL,
  `log_data` text,
  `log_version` varchar(32) DEFAULT NULL,
  `log_target` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `NewIndex1` (`log_type`),
  KEY `NewIndex2` (`log_ip`),
  KEY `NewIndex3` (`log_creator`),
  KEY `NewIndex4` (`log_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=143106 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_logs`
--

CREATE TABLE IF NOT EXISTS `webtracking_logs` (
  `logs_id` int(11) NOT NULL AUTO_INCREMENT,
  `logs_type` varchar(100) DEFAULT NULL,
  `logs_created` timestamp NULL DEFAULT NULL,
  `logs_content` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`logs_id`),
  KEY `logs_type_index` (`logs_type`,`logs_created`),
  KEY `logs_type_index2` (`logs_type`,`logs_content`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4832 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_notice`
--

CREATE TABLE IF NOT EXISTS `webtracking_notice` (
  `notice_id` int(11) NOT NULL AUTO_INCREMENT,
  `notice_vehicle` varchar(32) DEFAULT NULL,
  `notice_status` int(11) DEFAULT NULL COMMENT '1=sent;2=failed',
  `notice_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notice_type` varchar(32) DEFAULT NULL,
  `notice_desc` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`notice_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55296 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_payment`
--

CREATE TABLE IF NOT EXISTS `webtracking_payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_vehicle` int(11) DEFAULT NULL,
  `payment_method` varchar(32) DEFAULT NULL,
  `payment_accdest` int(11) DEFAULT NULL,
  `payment_amount` double DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `payment_transfer_code` varchar(32) DEFAULT NULL,
  `payment_name` varchar(100) DEFAULT NULL,
  `payment_creator` int(11) DEFAULT NULL,
  `payment_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `payment_status` int(11) DEFAULT NULL,
  `payment_mail` int(11) DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_poi`
--

CREATE TABLE IF NOT EXISTS `webtracking_poi` (
  `poi_id` int(11) NOT NULL AUTO_INCREMENT,
  `poi_name` varchar(255) DEFAULT NULL,
  `poi_latitude` varchar(20) DEFAULT NULL,
  `poi_longitude` varchar(20) DEFAULT NULL,
  `poi_status` int(11) DEFAULT '1',
  `poi_category` int(11) DEFAULT NULL,
  `poi_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`poi_id`),
  UNIQUE KEY `NewIndex2` (`poi_latitude`,`poi_longitude`),
  KEY `poicatindex` (`poi_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4950 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_poi_category`
--

CREATE TABLE IF NOT EXISTS `webtracking_poi_category` (
  `poi_cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `poi_cat_name` varchar(100) DEFAULT NULL,
  `poi_cat_icon` varchar(100) DEFAULT NULL,
  `poi_cat_status` int(11) DEFAULT '1',
  `poi_cat_creator` int(11) DEFAULT '0',
  `poi_cat_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`poi_cat_id`),
  UNIQUE KEY `NewIndex1` (`poi_cat_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_session`
--

CREATE TABLE IF NOT EXISTS `webtracking_session` (
  `session_id` varchar(255) NOT NULL,
  `session_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `NewIndex1` (`session_id`,`session_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsannouncement`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsannouncement` (
  `smsannouncement_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsannouncement_user` int(11) DEFAULT NULL,
  `smsannouncement_send` timestamp NULL DEFAULT NULL,
  `smsannouncement_content` text,
  PRIMARY KEY (`smsannouncement_id`),
  KEY `NewIndex1` (`smsannouncement_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4505 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsbalance`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsbalance` (
  `smsbalance_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsbalance_agent` int(11) DEFAULT NULL,
  `smsbalance_user` int(11) DEFAULT NULL,
  `smsbalance_debet` double DEFAULT NULL,
  `smsbalance_kredit` double DEFAULT NULL,
  `smsbalance_saldo` double DEFAULT NULL,
  `smsbalance_desc` text,
  `smsbalance_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `smsbalance_creator` int(11) DEFAULT NULL,
  PRIMARY KEY (`smsbalance_id`),
  KEY `NewIndex1` (`smsbalance_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsgeofence_indogps`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsgeofence_indogps` (
  `smsgeofence_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsgeofence_time` timestamp NULL DEFAULT NULL,
  `smsgeofence_status` int(11) DEFAULT '1' COMMENT '1=keluar; 2=masuk',
  `smsgeofence_alert` int(11) DEFAULT '1' COMMENT '1=belum disms',
  `smsgeofence_device` varchar(32) DEFAULT NULL COMMENT 'fk ke table geofence',
  `smsgeofence_latitude` varchar(32) DEFAULT NULL,
  `smsgeofence_longitude` varchar(32) DEFAULT NULL,
  `smsgeofence_alerttime` timestamp NULL DEFAULT NULL COMMENT 'kapan dialert',
  `smsgeofence_geofence` int(11) DEFAULT NULL,
  PRIMARY KEY (`smsgeofence_id`),
  KEY `smsgeofence_geofence_index` (`smsgeofence_geofence`),
  KEY `smsgeofence_time_index` (`smsgeofence_time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsgeofence_t1`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsgeofence_t1` (
  `smsgeofence_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsgeofence_time` timestamp NULL DEFAULT NULL,
  `smsgeofence_status` int(11) DEFAULT '1' COMMENT '1=keluar; 2=masuk',
  `smsgeofence_alert` int(11) DEFAULT '1' COMMENT '1=belum disms',
  `smsgeofence_device` varchar(32) DEFAULT NULL COMMENT 'fk ke table geofence',
  `smsgeofence_latitude` varchar(32) DEFAULT NULL,
  `smsgeofence_longitude` varchar(32) DEFAULT NULL,
  `smsgeofence_alerttime` timestamp NULL DEFAULT NULL COMMENT 'kapan dialert',
  `smsgeofence_geofence` int(11) DEFAULT NULL,
  PRIMARY KEY (`smsgeofence_id`),
  KEY `smsgeofence_geofence_index` (`smsgeofence_geofence`),
  KEY `smsgeofence_time_index` (`smsgeofence_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsgeofence_t1_1`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsgeofence_t1_1` (
  `smsgeofence_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsgeofence_time` timestamp NULL DEFAULT NULL,
  `smsgeofence_status` int(11) DEFAULT '1' COMMENT '1=keluar; 2=masuk',
  `smsgeofence_alert` int(11) DEFAULT '1' COMMENT '1=belum disms',
  `smsgeofence_device` varchar(32) DEFAULT NULL COMMENT 'fk ke table geofence',
  `smsgeofence_latitude` varchar(32) DEFAULT NULL,
  `smsgeofence_longitude` varchar(32) DEFAULT NULL,
  `smsgeofence_alerttime` timestamp NULL DEFAULT NULL COMMENT 'kapan dialert',
  `smsgeofence_geofence` int(11) DEFAULT NULL,
  PRIMARY KEY (`smsgeofence_id`),
  KEY `smsgeofence_geofence_index` (`smsgeofence_geofence`),
  KEY `smsgeofence_time_index` (`smsgeofence_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsgeofence_t1_pln`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsgeofence_t1_pln` (
  `smsgeofence_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsgeofence_time` timestamp NULL DEFAULT NULL,
  `smsgeofence_status` int(11) DEFAULT '1' COMMENT '1=keluar; 2=masuk',
  `smsgeofence_alert` int(11) DEFAULT '1' COMMENT '1=belum disms',
  `smsgeofence_device` varchar(32) DEFAULT NULL COMMENT 'fk ke table geofence',
  `smsgeofence_latitude` varchar(32) DEFAULT NULL,
  `smsgeofence_longitude` varchar(32) DEFAULT NULL,
  `smsgeofence_alerttime` timestamp NULL DEFAULT NULL COMMENT 'kapan dialert',
  `smsgeofence_geofence` int(11) DEFAULT NULL,
  PRIMARY KEY (`smsgeofence_id`),
  KEY `smsgeofence_geofence_index` (`smsgeofence_geofence`),
  KEY `smsgeofence_time_index` (`smsgeofence_time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsgeofence_t3`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsgeofence_t3` (
  `smsgeofence_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsgeofence_time` timestamp NULL DEFAULT NULL,
  `smsgeofence_status` int(11) DEFAULT '1' COMMENT '1=keluar; 2=masuk',
  `smsgeofence_alert` int(11) DEFAULT '1' COMMENT '1=belum disms',
  `smsgeofence_device` varchar(32) DEFAULT NULL COMMENT 'fk ke table geofence',
  `smsgeofence_latitude` varchar(32) DEFAULT NULL,
  `smsgeofence_longitude` varchar(32) DEFAULT NULL,
  `smsgeofence_alerttime` timestamp NULL DEFAULT NULL COMMENT 'kapan dialert',
  `smsgeofence_geofence` int(11) DEFAULT NULL,
  PRIMARY KEY (`smsgeofence_id`),
  KEY `smsgeofence_geofence_index` (`smsgeofence_geofence`),
  KEY `smsgeofence_time_index` (`smsgeofence_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsgeofence_t4`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsgeofence_t4` (
  `smsgeofence_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsgeofence_time` timestamp NULL DEFAULT NULL,
  `smsgeofence_status` int(11) DEFAULT '1' COMMENT '1=keluar; 2=masuk',
  `smsgeofence_alert` int(11) DEFAULT '1' COMMENT '1=belum disms',
  `smsgeofence_device` varchar(32) DEFAULT NULL COMMENT 'fk ke table geofence',
  `smsgeofence_latitude` varchar(32) DEFAULT NULL,
  `smsgeofence_longitude` varchar(32) DEFAULT NULL,
  `smsgeofence_alerttime` timestamp NULL DEFAULT NULL COMMENT 'kapan dialert',
  `smsgeofence_geofence` int(11) DEFAULT NULL,
  PRIMARY KEY (`smsgeofence_id`),
  KEY `smsgeofence_geofence_index` (`smsgeofence_geofence`),
  KEY `smsgeofence_time_index` (`smsgeofence_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsgeofence_t4_farrasindo`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsgeofence_t4_farrasindo` (
  `smsgeofence_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsgeofence_time` timestamp NULL DEFAULT NULL,
  `smsgeofence_status` int(11) DEFAULT '1' COMMENT '1=keluar; 2=masuk',
  `smsgeofence_alert` int(11) DEFAULT '1' COMMENT '1=belum disms',
  `smsgeofence_device` varchar(32) DEFAULT NULL COMMENT 'fk ke table geofence',
  `smsgeofence_latitude` varchar(32) DEFAULT NULL,
  `smsgeofence_longitude` varchar(32) DEFAULT NULL,
  `smsgeofence_alerttime` timestamp NULL DEFAULT NULL COMMENT 'kapan dialert',
  `smsgeofence_geofence` int(11) DEFAULT NULL,
  PRIMARY KEY (`smsgeofence_id`),
  KEY `smsgeofence_geofence_index` (`smsgeofence_geofence`),
  KEY `smsgeofence_time_index` (`smsgeofence_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsgeofence_t4_new`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsgeofence_t4_new` (
  `smsgeofence_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsgeofence_time` timestamp NULL DEFAULT NULL,
  `smsgeofence_status` int(11) DEFAULT '1' COMMENT '1=keluar; 2=masuk',
  `smsgeofence_alert` int(11) DEFAULT '1' COMMENT '1=belum disms',
  `smsgeofence_device` varchar(32) DEFAULT NULL COMMENT 'fk ke table geofence',
  `smsgeofence_latitude` varchar(32) DEFAULT NULL,
  `smsgeofence_longitude` varchar(32) DEFAULT NULL,
  `smsgeofence_alerttime` timestamp NULL DEFAULT NULL COMMENT 'kapan dialert',
  `smsgeofence_geofence` int(11) DEFAULT NULL,
  PRIMARY KEY (`smsgeofence_id`),
  KEY `smsgeofence_geofence_index` (`smsgeofence_geofence`),
  KEY `smsgeofence_time_index` (`smsgeofence_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsmaxspeed_hist`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsmaxspeed_hist` (
  `smsmaxspeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsmaxspeed_vehicle` varchar(32) DEFAULT NULL,
  `smsmaxspeed_speed` double DEFAULT NULL COMMENT 'alert pada kecepatan berapa',
  `smsmaxspeed_max` double DEFAULT NULL COMMENT 'max kecepatan',
  `smsmaxspeed_status` int(11) DEFAULT '1' COMMENT '1=new;2=alerted;3=normal',
  `smsmaxspeed_alert` timestamp NULL DEFAULT NULL COMMENT 'waktu kecepatan melebihi max speed',
  `smsmaxspeed_normal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'waktu kecepatan kembali normal',
  `smsmaxspeed_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsmaxspeed_id`),
  KEY `NewIndex1` (`smsmaxspeed_vehicle`),
  KEY `NewIndex2` (`smsmaxspeed_alert`),
  KEY `smsmaxspeed_created_index` (`smsmaxspeed_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1389 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsmaxspeed_indogps`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsmaxspeed_indogps` (
  `smsmaxspeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsmaxspeed_vehicle` varchar(32) DEFAULT NULL,
  `smsmaxspeed_speed` double DEFAULT NULL COMMENT 'alert pada kecepatan berapa',
  `smsmaxspeed_max` double DEFAULT NULL COMMENT 'max kecepatan',
  `smsmaxspeed_status` int(11) DEFAULT '1' COMMENT '1=new;2=alerted;3=normal',
  `smsmaxspeed_alert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'waktu kecepatan melebihi max speed',
  `smsmaxspeed_normal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'waktu kecepatan kembali normal',
  `smsmaxspeed_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsmaxspeed_id`),
  KEY `NewIndex1` (`smsmaxspeed_vehicle`),
  KEY `NewIndex2` (`smsmaxspeed_alert`),
  KEY `smsmaxspeed_created_index` (`smsmaxspeed_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=390 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsmaxspeed_t1`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsmaxspeed_t1` (
  `smsmaxspeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsmaxspeed_vehicle` varchar(32) DEFAULT NULL,
  `smsmaxspeed_speed` double DEFAULT NULL COMMENT 'alert pada kecepatan berapa',
  `smsmaxspeed_max` double DEFAULT NULL COMMENT 'max kecepatan',
  `smsmaxspeed_status` int(11) DEFAULT '1' COMMENT '1=new;2=alerted;3=normal',
  `smsmaxspeed_alert` timestamp NULL DEFAULT NULL COMMENT 'waktu kecepatan melebihi max speed',
  `smsmaxspeed_normal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'waktu kecepatan kembali normal',
  `smsmaxspeed_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsmaxspeed_id`),
  KEY `NewIndex1` (`smsmaxspeed_vehicle`),
  KEY `NewIndex2` (`smsmaxspeed_alert`),
  KEY `smsmaxspeed_created_index` (`smsmaxspeed_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=405 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsmaxspeed_t1_1`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsmaxspeed_t1_1` (
  `smsmaxspeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsmaxspeed_vehicle` varchar(32) DEFAULT NULL,
  `smsmaxspeed_speed` double DEFAULT NULL COMMENT 'alert pada kecepatan berapa',
  `smsmaxspeed_max` double DEFAULT NULL COMMENT 'max kecepatan',
  `smsmaxspeed_status` int(11) DEFAULT '1' COMMENT '1=new;2=alerted;3=normal',
  `smsmaxspeed_alert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'waktu kecepatan melebihi max speed',
  `smsmaxspeed_normal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'waktu kecepatan kembali normal',
  `smsmaxspeed_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsmaxspeed_id`),
  KEY `NewIndex1` (`smsmaxspeed_vehicle`),
  KEY `NewIndex2` (`smsmaxspeed_alert`),
  KEY `smsmaxspeed_created_index` (`smsmaxspeed_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=285 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsmaxspeed_t1_pln`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsmaxspeed_t1_pln` (
  `smsmaxspeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsmaxspeed_vehicle` varchar(32) DEFAULT NULL,
  `smsmaxspeed_speed` double DEFAULT NULL COMMENT 'alert pada kecepatan berapa',
  `smsmaxspeed_max` double DEFAULT NULL COMMENT 'max kecepatan',
  `smsmaxspeed_status` int(11) DEFAULT '1' COMMENT '1=new;2=alerted;3=normal',
  `smsmaxspeed_alert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'waktu kecepatan melebihi max speed',
  `smsmaxspeed_normal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'waktu kecepatan kembali normal',
  `smsmaxspeed_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsmaxspeed_id`),
  KEY `NewIndex1` (`smsmaxspeed_vehicle`),
  KEY `NewIndex2` (`smsmaxspeed_alert`),
  KEY `smsmaxspeed_created_index` (`smsmaxspeed_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsmaxspeed_t3`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsmaxspeed_t3` (
  `smsmaxspeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsmaxspeed_vehicle` varchar(32) DEFAULT NULL,
  `smsmaxspeed_speed` double DEFAULT NULL COMMENT 'alert pada kecepatan berapa',
  `smsmaxspeed_max` double DEFAULT NULL COMMENT 'max kecepatan',
  `smsmaxspeed_status` int(11) DEFAULT '1' COMMENT '1=new;2=alerted;3=normal',
  `smsmaxspeed_alert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'waktu kecepatan melebihi max speed',
  `smsmaxspeed_normal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'waktu kecepatan kembali normal',
  `smsmaxspeed_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsmaxspeed_id`),
  KEY `NewIndex1` (`smsmaxspeed_vehicle`),
  KEY `NewIndex2` (`smsmaxspeed_alert`),
  KEY `smsmaxspeed_created_index` (`smsmaxspeed_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=159 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsmaxspeed_t4`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsmaxspeed_t4` (
  `smsmaxspeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsmaxspeed_vehicle` varchar(32) DEFAULT NULL,
  `smsmaxspeed_speed` double DEFAULT NULL COMMENT 'alert pada kecepatan berapa',
  `smsmaxspeed_max` double DEFAULT NULL COMMENT 'max kecepatan',
  `smsmaxspeed_status` int(11) DEFAULT '1' COMMENT '1=new;2=alerted;3=normal',
  `smsmaxspeed_alert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'waktu kecepatan melebihi max speed',
  `smsmaxspeed_normal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'waktu kecepatan kembali normal',
  `smsmaxspeed_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsmaxspeed_id`),
  KEY `NewIndex1` (`smsmaxspeed_vehicle`),
  KEY `NewIndex2` (`smsmaxspeed_alert`),
  KEY `smsmaxspeed_created_index` (`smsmaxspeed_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=163 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsmaxspeed_t4_farrasindo`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsmaxspeed_t4_farrasindo` (
  `smsmaxspeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsmaxspeed_vehicle` varchar(32) DEFAULT NULL,
  `smsmaxspeed_speed` double DEFAULT NULL COMMENT 'alert pada kecepatan berapa',
  `smsmaxspeed_max` double DEFAULT NULL COMMENT 'max kecepatan',
  `smsmaxspeed_status` int(11) DEFAULT '1' COMMENT '1=new;2=alerted;3=normal',
  `smsmaxspeed_alert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'waktu kecepatan melebihi max speed',
  `smsmaxspeed_normal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'waktu kecepatan kembali normal',
  `smsmaxspeed_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsmaxspeed_id`),
  KEY `NewIndex1` (`smsmaxspeed_vehicle`),
  KEY `NewIndex2` (`smsmaxspeed_alert`),
  KEY `smsmaxspeed_created_index` (`smsmaxspeed_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsmaxspeed_t4_new`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsmaxspeed_t4_new` (
  `smsmaxspeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsmaxspeed_vehicle` varchar(32) DEFAULT NULL,
  `smsmaxspeed_speed` double DEFAULT NULL COMMENT 'alert pada kecepatan berapa',
  `smsmaxspeed_max` double DEFAULT NULL COMMENT 'max kecepatan',
  `smsmaxspeed_status` int(11) DEFAULT '1' COMMENT '1=new;2=alerted;3=normal',
  `smsmaxspeed_alert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'waktu kecepatan melebihi max speed',
  `smsmaxspeed_normal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'waktu kecepatan kembali normal',
  `smsmaxspeed_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsmaxspeed_id`),
  KEY `NewIndex1` (`smsmaxspeed_vehicle`),
  KEY `NewIndex2` (`smsmaxspeed_alert`),
  KEY `smsmaxspeed_created_index` (`smsmaxspeed_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=84 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsparking_hist`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsparking_hist` (
  `smsparking_int` int(11) NOT NULL AUTO_INCREMENT,
  `smsparking_vehicle` varchar(32) DEFAULT NULL,
  `smsparking_begin` timestamp NULL DEFAULT NULL,
  `smsparking_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_status` int(11) DEFAULT '1' COMMENT '1=parking,2=not parking',
  `smsparking_alert` int(11) DEFAULT '0' COMMENT '0=belum dialert;1=sudah dialert',
  `smsparking_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_setting` int(11) DEFAULT NULL COMMENT 'dalam jam',
  PRIMARY KEY (`smsparking_int`),
  KEY `NewIndex1` (`smsparking_vehicle`),
  KEY `NewIndex2` (`smsparking_alert`),
  KEY `NewIndex3` (`smsparking_status`),
  KEY `smsparking_end_index` (`smsparking_end`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11225 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsparking_indogps`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsparking_indogps` (
  `smsparking_int` int(11) NOT NULL AUTO_INCREMENT,
  `smsparking_vehicle` varchar(32) DEFAULT NULL,
  `smsparking_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_status` int(11) DEFAULT '1' COMMENT '1=parking,2=not parking',
  `smsparking_alert` int(11) DEFAULT '0' COMMENT '0=belum dialert;1=sudah dialert',
  `smsparking_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_setting` int(11) DEFAULT NULL COMMENT 'dalam jam',
  PRIMARY KEY (`smsparking_int`),
  KEY `NewIndex1` (`smsparking_vehicle`),
  KEY `NewIndex2` (`smsparking_alert`),
  KEY `NewIndex3` (`smsparking_status`),
  KEY `smsparking_end_index` (`smsparking_end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsparking_t1`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsparking_t1` (
  `smsparking_int` int(11) NOT NULL AUTO_INCREMENT,
  `smsparking_vehicle` varchar(32) DEFAULT NULL,
  `smsparking_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_status` int(11) DEFAULT '1' COMMENT '1=parking,2=not parking',
  `smsparking_alert` int(11) DEFAULT '0' COMMENT '0=belum dialert;1=sudah dialert',
  `smsparking_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_setting` int(11) DEFAULT NULL COMMENT 'dalam jam',
  PRIMARY KEY (`smsparking_int`),
  KEY `NewIndex1` (`smsparking_vehicle`),
  KEY `NewIndex2` (`smsparking_alert`),
  KEY `NewIndex3` (`smsparking_status`),
  KEY `smsparking_end_index` (`smsparking_end`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5951 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsparking_t1_1`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsparking_t1_1` (
  `smsparking_int` int(11) NOT NULL AUTO_INCREMENT,
  `smsparking_vehicle` varchar(32) DEFAULT NULL,
  `smsparking_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_status` int(11) DEFAULT '1' COMMENT '1=parking,2=not parking',
  `smsparking_alert` int(11) DEFAULT '0' COMMENT '0=belum dialert;1=sudah dialert',
  `smsparking_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_setting` int(11) DEFAULT NULL COMMENT 'dalam jam',
  PRIMARY KEY (`smsparking_int`),
  KEY `NewIndex1` (`smsparking_vehicle`),
  KEY `NewIndex2` (`smsparking_alert`),
  KEY `NewIndex3` (`smsparking_status`),
  KEY `smsparking_end_index` (`smsparking_end`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2575 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsparking_t1_pln`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsparking_t1_pln` (
  `smsparking_int` int(11) NOT NULL AUTO_INCREMENT,
  `smsparking_vehicle` varchar(32) DEFAULT NULL,
  `smsparking_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_status` int(11) DEFAULT '1' COMMENT '1=parking,2=not parking',
  `smsparking_alert` int(11) DEFAULT '0' COMMENT '0=belum dialert;1=sudah dialert',
  `smsparking_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_setting` int(11) DEFAULT NULL COMMENT 'dalam jam',
  PRIMARY KEY (`smsparking_int`),
  KEY `NewIndex1` (`smsparking_vehicle`),
  KEY `NewIndex2` (`smsparking_alert`),
  KEY `NewIndex3` (`smsparking_status`),
  KEY `smsparking_end_index` (`smsparking_end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsparking_t3`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsparking_t3` (
  `smsparking_int` int(11) NOT NULL AUTO_INCREMENT,
  `smsparking_vehicle` varchar(32) DEFAULT NULL,
  `smsparking_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_status` int(11) DEFAULT '1' COMMENT '1=parking,2=not parking',
  `smsparking_alert` int(11) DEFAULT '0' COMMENT '0=belum dialert;1=sudah dialert',
  `smsparking_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_setting` int(11) DEFAULT NULL COMMENT 'dalam jam',
  PRIMARY KEY (`smsparking_int`),
  KEY `NewIndex1` (`smsparking_vehicle`),
  KEY `NewIndex2` (`smsparking_alert`),
  KEY `NewIndex3` (`smsparking_status`),
  KEY `smsparking_end_index` (`smsparking_end`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2874 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsparking_t4`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsparking_t4` (
  `smsparking_int` int(11) NOT NULL AUTO_INCREMENT,
  `smsparking_vehicle` varchar(32) DEFAULT NULL,
  `smsparking_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_status` int(11) DEFAULT '1' COMMENT '1=parking,2=not parking',
  `smsparking_alert` int(11) DEFAULT '0' COMMENT '0=belum dialert;1=sudah dialert',
  `smsparking_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_setting` int(11) DEFAULT NULL COMMENT 'dalam jam',
  PRIMARY KEY (`smsparking_int`),
  KEY `NewIndex1` (`smsparking_vehicle`),
  KEY `NewIndex2` (`smsparking_alert`),
  KEY `NewIndex3` (`smsparking_status`),
  KEY `smsparking_end_index` (`smsparking_end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsparking_t4_farrasindo`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsparking_t4_farrasindo` (
  `smsparking_int` int(11) NOT NULL AUTO_INCREMENT,
  `smsparking_vehicle` varchar(32) DEFAULT NULL,
  `smsparking_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_status` int(11) DEFAULT '1' COMMENT '1=parking,2=not parking',
  `smsparking_alert` int(11) DEFAULT '0' COMMENT '0=belum dialert;1=sudah dialert',
  `smsparking_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_setting` int(11) DEFAULT NULL COMMENT 'dalam jam',
  PRIMARY KEY (`smsparking_int`),
  KEY `NewIndex1` (`smsparking_vehicle`),
  KEY `NewIndex2` (`smsparking_alert`),
  KEY `NewIndex3` (`smsparking_status`),
  KEY `smsparking_end_index` (`smsparking_end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsparking_t4_new`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsparking_t4_new` (
  `smsparking_int` int(11) NOT NULL AUTO_INCREMENT,
  `smsparking_vehicle` varchar(32) DEFAULT NULL,
  `smsparking_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_status` int(11) DEFAULT '1' COMMENT '1=parking,2=not parking',
  `smsparking_alert` int(11) DEFAULT '0' COMMENT '0=belum dialert;1=sudah dialert',
  `smsparking_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_setting` int(11) DEFAULT NULL COMMENT 'dalam jam',
  PRIMARY KEY (`smsparking_int`),
  KEY `NewIndex1` (`smsparking_vehicle`),
  KEY `NewIndex2` (`smsparking_alert`),
  KEY `NewIndex3` (`smsparking_status`),
  KEY `smsparking_end_index` (`smsparking_end`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=995 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smspayment`
--

CREATE TABLE IF NOT EXISTS `webtracking_smspayment` (
  `smspayment_id` int(11) NOT NULL AUTO_INCREMENT,
  `smspayment_user` int(11) DEFAULT NULL,
  `smspayment_method` varchar(32) DEFAULT NULL,
  `smspayment_bank` int(11) DEFAULT NULL,
  `smspayment_amount` double DEFAULT NULL,
  `smspayment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `smspayment_validation` varchar(8) DEFAULT NULL,
  `smspayment_name` varchar(100) DEFAULT NULL,
  `smspayment_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smspayment_creator` int(11) DEFAULT NULL,
  `smspayment_status` int(11) DEFAULT '1' COMMENT '1=baru; 2=approved;3=cancelled',
  `smspayment_cancelled_user` int(11) DEFAULT NULL,
  `smspayment_cancelled_time` timestamp NULL DEFAULT NULL,
  `smspayment_approved_user` int(11) DEFAULT NULL,
  `smspayment_approved_time` timestamp NULL DEFAULT NULL,
  `smspayment_agent` int(11) DEFAULT NULL,
  PRIMARY KEY (`smspayment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsreceive`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsreceive` (
  `smsreceive_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsreceive_from` varchar(32) DEFAULT NULL,
  `smsreceive_sent` timestamp NULL DEFAULT NULL,
  `smsreceive_received` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsreceive_message` text,
  `smsreceive_reply` int(11) DEFAULT '0',
  PRIMARY KEY (`smsreceive_id`),
  FULLTEXT KEY `smsrecv_message` (`smsreceive_message`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38279 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smssignal_indogps`
--

CREATE TABLE IF NOT EXISTS `webtracking_smssignal_indogps` (
  `smssignal_id` int(11) NOT NULL AUTO_INCREMENT,
  `smssignal_vehicle` varchar(32) NOT NULL,
  `smssignal_created` timestamp NULL DEFAULT NULL,
  `smssignal_type` int(11) NOT NULL COMMENT '1=OK=>NOT OK, 2=NOT OK=>OK',
  `smssignal_status` int(11) NOT NULL COMMENT '1=belum diprocess, 2=processed',
  `smssignal_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`smssignal_id`),
  KEY `smssignal_vehicle_index` (`smssignal_vehicle`,`smssignal_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smssignal_t1`
--

CREATE TABLE IF NOT EXISTS `webtracking_smssignal_t1` (
  `smssignal_id` int(11) NOT NULL AUTO_INCREMENT,
  `smssignal_vehicle` varchar(32) NOT NULL,
  `smssignal_created` timestamp NULL DEFAULT NULL,
  `smssignal_type` int(11) NOT NULL COMMENT '1=OK=>NOT OK, 2=NOT OK=>OK',
  `smssignal_status` int(11) NOT NULL COMMENT '1=belum diprocess, 2=processed',
  `smssignal_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`smssignal_id`),
  KEY `smssignal_vehicle_index` (`smssignal_vehicle`,`smssignal_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5717 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smssignal_t1_1`
--

CREATE TABLE IF NOT EXISTS `webtracking_smssignal_t1_1` (
  `smssignal_id` int(11) NOT NULL AUTO_INCREMENT,
  `smssignal_vehicle` varchar(32) NOT NULL,
  `smssignal_created` timestamp NULL DEFAULT NULL,
  `smssignal_type` int(11) NOT NULL COMMENT '1=OK=>NOT OK, 2=NOT OK=>OK',
  `smssignal_status` int(11) NOT NULL COMMENT '1=belum diprocess, 2=processed',
  `smssignal_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`smssignal_id`),
  KEY `smssignal_vehicle_index` (`smssignal_vehicle`,`smssignal_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15764 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smssignal_t1_pln`
--

CREATE TABLE IF NOT EXISTS `webtracking_smssignal_t1_pln` (
  `smssignal_id` int(11) NOT NULL AUTO_INCREMENT,
  `smssignal_vehicle` varchar(32) NOT NULL,
  `smssignal_created` timestamp NULL DEFAULT NULL,
  `smssignal_type` int(11) NOT NULL COMMENT '1=OK=>NOT OK, 2=NOT OK=>OK',
  `smssignal_status` int(11) NOT NULL COMMENT '1=belum diprocess, 2=processed',
  `smssignal_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`smssignal_id`),
  KEY `smssignal_vehicle_index` (`smssignal_vehicle`,`smssignal_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=209 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smssignal_t3`
--

CREATE TABLE IF NOT EXISTS `webtracking_smssignal_t3` (
  `smssignal_id` int(11) NOT NULL AUTO_INCREMENT,
  `smssignal_vehicle` varchar(32) NOT NULL,
  `smssignal_created` timestamp NULL DEFAULT NULL,
  `smssignal_type` int(11) NOT NULL COMMENT '1=OK=>NOT OK, 2=NOT OK=>OK',
  `smssignal_status` int(11) NOT NULL COMMENT '1=belum diprocess, 2=processed',
  `smssignal_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`smssignal_id`),
  KEY `smssignal_vehicle_index` (`smssignal_vehicle`,`smssignal_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smssignal_t4`
--

CREATE TABLE IF NOT EXISTS `webtracking_smssignal_t4` (
  `smssignal_id` int(11) NOT NULL AUTO_INCREMENT,
  `smssignal_vehicle` varchar(32) NOT NULL,
  `smssignal_created` timestamp NULL DEFAULT NULL,
  `smssignal_type` int(11) NOT NULL COMMENT '1=OK=>NOT OK, 2=NOT OK=>OK',
  `smssignal_status` int(11) NOT NULL COMMENT '1=belum diprocess, 2=processed',
  `smssignal_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`smssignal_id`),
  KEY `smssignal_vehicle_index` (`smssignal_vehicle`,`smssignal_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25859 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smssignal_t4_farrasindo`
--

CREATE TABLE IF NOT EXISTS `webtracking_smssignal_t4_farrasindo` (
  `smssignal_id` int(11) NOT NULL AUTO_INCREMENT,
  `smssignal_vehicle` varchar(32) NOT NULL,
  `smssignal_created` timestamp NULL DEFAULT NULL,
  `smssignal_type` int(11) NOT NULL COMMENT '1=OK=>NOT OK, 2=NOT OK=>OK',
  `smssignal_status` int(11) NOT NULL COMMENT '1=belum diprocess, 2=processed',
  `smssignal_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`smssignal_id`),
  KEY `smssignal_vehicle_index` (`smssignal_vehicle`,`smssignal_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3382 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smssignal_t4_new`
--

CREATE TABLE IF NOT EXISTS `webtracking_smssignal_t4_new` (
  `smssignal_id` int(11) NOT NULL AUTO_INCREMENT,
  `smssignal_vehicle` varchar(32) NOT NULL,
  `smssignal_created` timestamp NULL DEFAULT NULL,
  `smssignal_type` int(11) NOT NULL COMMENT '1=OK=>NOT OK, 2=NOT OK=>OK',
  `smssignal_status` int(11) NOT NULL COMMENT '1=belum diprocess, 2=processed',
  `smssignal_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`smssignal_id`),
  KEY `smssignal_vehicle_index` (`smssignal_vehicle`,`smssignal_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17803 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsupdated_indogps`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsupdated_indogps` (
  `smsupdated_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsupdated_vehicle` varchar(32) NOT NULL,
  `smsupdated_created` timestamp NULL DEFAULT NULL,
  `smsupdated_status` int(11) NOT NULL,
  `smsupdated_alerted` timestamp NULL DEFAULT NULL,
  `smsupdated_lastupdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsupdated_id`),
  KEY `smsupdated_vehicle_index` (`smsupdated_vehicle`,`smsupdated_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsupdated_t1`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsupdated_t1` (
  `smsupdated_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsupdated_vehicle` varchar(32) NOT NULL,
  `smsupdated_created` timestamp NULL DEFAULT NULL,
  `smsupdated_status` int(11) NOT NULL,
  `smsupdated_alerted` timestamp NULL DEFAULT NULL,
  `smsupdated_lastupdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsupdated_id`),
  KEY `smsupdated_vehicle_index` (`smsupdated_vehicle`,`smsupdated_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsupdated_t1_1`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsupdated_t1_1` (
  `smsupdated_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsupdated_vehicle` varchar(32) NOT NULL,
  `smsupdated_created` timestamp NULL DEFAULT NULL,
  `smsupdated_status` int(11) NOT NULL,
  `smsupdated_alerted` timestamp NULL DEFAULT NULL,
  `smsupdated_lastupdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsupdated_id`),
  KEY `smsupdated_vehicle_index` (`smsupdated_vehicle`,`smsupdated_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsupdated_t1_pln`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsupdated_t1_pln` (
  `smsupdated_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsupdated_vehicle` varchar(32) NOT NULL,
  `smsupdated_created` timestamp NULL DEFAULT NULL,
  `smsupdated_status` int(11) NOT NULL,
  `smsupdated_alerted` timestamp NULL DEFAULT NULL,
  `smsupdated_lastupdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsupdated_id`),
  KEY `smsupdated_vehicle_index` (`smsupdated_vehicle`,`smsupdated_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsupdated_t3`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsupdated_t3` (
  `smsupdated_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsupdated_vehicle` varchar(32) NOT NULL,
  `smsupdated_created` timestamp NULL DEFAULT NULL,
  `smsupdated_status` int(11) NOT NULL,
  `smsupdated_alerted` timestamp NULL DEFAULT NULL,
  `smsupdated_lastupdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsupdated_id`),
  KEY `smsupdated_vehicle_index` (`smsupdated_vehicle`,`smsupdated_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsupdated_t4`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsupdated_t4` (
  `smsupdated_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsupdated_vehicle` varchar(32) NOT NULL,
  `smsupdated_created` timestamp NULL DEFAULT NULL,
  `smsupdated_status` int(11) NOT NULL,
  `smsupdated_alerted` timestamp NULL DEFAULT NULL,
  `smsupdated_lastupdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsupdated_id`),
  KEY `smsupdated_vehicle_index` (`smsupdated_vehicle`,`smsupdated_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsupdated_t4_farrasindo`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsupdated_t4_farrasindo` (
  `smsupdated_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsupdated_vehicle` varchar(32) NOT NULL,
  `smsupdated_created` timestamp NULL DEFAULT NULL,
  `smsupdated_status` int(11) NOT NULL,
  `smsupdated_alerted` timestamp NULL DEFAULT NULL,
  `smsupdated_lastupdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsupdated_id`),
  KEY `smsupdated_vehicle_index` (`smsupdated_vehicle`,`smsupdated_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_smsupdated_t4_new`
--

CREATE TABLE IF NOT EXISTS `webtracking_smsupdated_t4_new` (
  `smsupdated_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsupdated_vehicle` varchar(32) NOT NULL,
  `smsupdated_created` timestamp NULL DEFAULT NULL,
  `smsupdated_status` int(11) NOT NULL,
  `smsupdated_alerted` timestamp NULL DEFAULT NULL,
  `smsupdated_lastupdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsupdated_id`),
  KEY `smsupdated_vehicle_index` (`smsupdated_vehicle`,`smsupdated_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_sms_sos_t4`
--

CREATE TABLE IF NOT EXISTS `webtracking_sms_sos_t4` (
  `sms_sos_id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_sos_vehicle` varchar(32) NOT NULL,
  `sms_sos_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sms_sos_status` int(11) NOT NULL,
  `sms_sos_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sms_sos_type` varchar(10) NOT NULL,
  PRIMARY KEY (`sms_sos_id`),
  KEY `sms_sos_vehicle_index` (`sms_sos_vehicle`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9455 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_sms_sos_t4_farrasindo`
--

CREATE TABLE IF NOT EXISTS `webtracking_sms_sos_t4_farrasindo` (
  `sms_sos_id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_sos_vehicle` varchar(32) NOT NULL,
  `sms_sos_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sms_sos_status` int(11) NOT NULL,
  `sms_sos_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sms_sos_type` varchar(10) NOT NULL,
  PRIMARY KEY (`sms_sos_id`),
  KEY `sms_sos_vehicle_index` (`sms_sos_vehicle`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1106 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_sms_sos_t4_new`
--

CREATE TABLE IF NOT EXISTS `webtracking_sms_sos_t4_new` (
  `sms_sos_id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_sos_vehicle` varchar(32) NOT NULL,
  `sms_sos_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sms_sos_status` int(11) NOT NULL,
  `sms_sos_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sms_sos_type` varchar(10) NOT NULL,
  PRIMARY KEY (`sms_sos_id`),
  KEY `sms_sos_vehicle_index` (`sms_sos_vehicle`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_street`
--

CREATE TABLE IF NOT EXISTS `webtracking_street` (
  `street_id` int(11) NOT NULL AUTO_INCREMENT,
  `street_name` varchar(100) DEFAULT NULL,
  `street_line` geometry DEFAULT NULL,
  `street_creator` int(11) DEFAULT NULL,
  `street_created` timestamp NULL DEFAULT NULL,
  `street_serialize` text,
  PRIMARY KEY (`street_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_user`
--

CREATE TABLE IF NOT EXISTS `webtracking_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_login` varchar(100) DEFAULT NULL,
  `user_pass` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_license_id` varchar(100) DEFAULT NULL,
  `user_license_type` varchar(2) DEFAULT NULL,
  `user_sex` char(1) DEFAULT NULL COMMENT 'M=Male,F=Female',
  `user_birth_date` int(11) DEFAULT NULL,
  `user_province` varchar(100) DEFAULT NULL,
  `user_city` varchar(100) DEFAULT NULL,
  `user_address` text,
  `user_mobile` varbinary(32) DEFAULT NULL,
  `user_phone` varchar(32) DEFAULT NULL,
  `user_type` int(11) DEFAULT NULL COMMENT '1=administrator, 2=regular',
  `user_status` int(11) DEFAULT NULL COMMENT '1=active, 2=disabled',
  `user_lastlogin_date` int(11) DEFAULT NULL,
  `user_lastlogin_time` int(11) DEFAULT NULL,
  `user_photo` varchar(255) DEFAULT NULL,
  `user_zipcode` varchar(32) DEFAULT NULL,
  `user_create_date` int(11) DEFAULT NULL,
  `user_agent` int(11) DEFAULT '0',
  `user_mail` varchar(100) DEFAULT NULL,
  `user_agent_admin` int(11) DEFAULT '1',
  `user_alarm` timestamp NULL DEFAULT NULL,
  `user_engine` int(11) NOT NULL,
  `user_group` int(11) NOT NULL,
  `user_company` int(11) NOT NULL,
  `user_manage_password` int(11) NOT NULL DEFAULT '1',
  `user_sms_notifikasi` int(11) NOT NULL DEFAULT '1',
  `user_change_profile` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_login` (`user_login`,`user_pass`),
  KEY `NewIndex1` (`user_type`),
  KEY `user_company_index` (`user_company`),
  KEY `user_name_index` (`user_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=609 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_vehicle`
--

CREATE TABLE IF NOT EXISTS `webtracking_vehicle` (
  `vehicle_id` int(11) NOT NULL AUTO_INCREMENT,
  `vehicle_user_id` int(11) DEFAULT NULL,
  `vehicle_device` varchar(100) DEFAULT NULL,
  `vehicle_no` varchar(16) DEFAULT NULL,
  `vehicle_name` varchar(255) DEFAULT NULL,
  `vehicle_active_date2` int(11) DEFAULT '0',
  `vehicle_card_no` varchar(100) DEFAULT NULL,
  `vehicle_operator` varchar(100) DEFAULT NULL,
  `vehicle_active_date` int(11) DEFAULT NULL,
  `vehicle_active_date1` int(11) DEFAULT '0',
  `vehicle_status` int(11) NOT NULL DEFAULT '1' COMMENT '1=layanan sudah dibayar, 2=expired',
  `vehicle_image` varchar(100) DEFAULT NULL,
  `vehicle_created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vehicle_type` varchar(32) DEFAULT NULL,
  `vehicle_autorefill` int(11) DEFAULT '0',
  `vehicle_maxspeed` double DEFAULT '0',
  `vehicle_maxparking` int(11) DEFAULT '0',
  `vehicle_group` int(11) NOT NULL,
  `vehicle_company` int(11) NOT NULL,
  PRIMARY KEY (`vehicle_id`),
  UNIQUE KEY `NewIndex3` (`vehicle_user_id`,`vehicle_device`),
  KEY `NewIndex1` (`vehicle_device`),
  KEY `NewIndex2` (`vehicle_user_id`),
  KEY `NewIndex4` (`vehicle_status`),
  KEY `vehicle_index_sort1` (`vehicle_name`,`vehicle_no`),
  KEY `vehicle_active_date_index` (`vehicle_active_date2`,`vehicle_user_id`),
  KEY `vehicle_active_date_1_index` (`vehicle_user_id`,`vehicle_type`,`vehicle_active_date2`),
  KEY `vehicle_type_index` (`vehicle_type`),
  KEY `vehicle_status_index` (`vehicle_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7210356 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_vehiclegroup`
--

CREATE TABLE IF NOT EXISTS `webtracking_vehiclegroup` (
  `vehiclegroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `vehiclegroup_name` varchar(100) DEFAULT NULL,
  `vehiclegroup_created` int(11) DEFAULT NULL,
  `vehiclegroup_creator` int(11) DEFAULT NULL,
  PRIMARY KEY (`vehiclegroup_id`),
  UNIQUE KEY `NewIndex1` (`vehiclegroup_name`,`vehiclegroup_creator`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `webtracking_vehiclegroup_rel`
--

CREATE TABLE IF NOT EXISTS `webtracking_vehiclegroup_rel` (
  `vehiclegroup_rel_id` int(11) NOT NULL AUTO_INCREMENT,
  `vehiclegroup_rel_vehicle` int(11) DEFAULT NULL,
  `vehiclegroup_rel_group` int(11) DEFAULT NULL,
  PRIMARY KEY (`vehiclegroup_rel_id`),
  UNIQUE KEY `NewIndex1` (`vehiclegroup_rel_vehicle`,`vehiclegroup_rel_group`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `webtracking_gps`
--

CREATE TABLE IF NOT EXISTS `webtracking_gps_t1_2` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `gps_vihicle` (`gps_name`),
  KEY `NewIndex2` (`gps_host`),
  KEY `NewIndex3` (`gps_name`,`gps_host`),
  KEY `NewIndex1` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `webtracking_gps_t1_2_error` (
  `gps_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_name` varchar(32) DEFAULT NULL,
  `gps_host` varchar(32) DEFAULT NULL,
  `gps_type` varchar(10) DEFAULT NULL,
  `gps_utc_coord` int(11) DEFAULT NULL COMMENT 'in UTC (coordinated universal time zone). UTC used be known as GMT',
  `gps_status` char(1) DEFAULT NULL COMMENT 'Status A = Valid, V = Invalid',
  `gps_latitude` varchar(16) DEFAULT NULL COMMENT 'latitude of the GPS position fix',
  `gps_ns` char(1) DEFAULT NULL COMMENT 'N/S Indicator S N = North, S = South',
  `gps_longitude` varchar(16) DEFAULT NULL COMMENT 'longitude of the GPS position fix',
  `gps_ew` char(1) DEFAULT NULL COMMENT 'E/W Indicator E E = East, W = West',
  `gps_speed` float DEFAULT NULL COMMENT 'Speed over ground in Knots',
  `gps_course` float DEFAULT NULL COMMENT 'Course over ground 0.00 Degrees',
  `gps_utc_date` int(11) DEFAULT NULL COMMENT 'UTC Date 211200 DDMMYY',
  `gps_mvd` float DEFAULT NULL COMMENT 'Magnetic variation Degrees',
  `gps_mv` char(1) DEFAULT NULL COMMENT 'mv',
  `gps_cs` varchar(100) DEFAULT NULL COMMENT 'Checksum *25',
  `gps_msg_ori` varchar(1024) DEFAULT NULL,
  `gps_time` timestamp NULL DEFAULT NULL,
  `gps_latitude_real` double DEFAULT NULL,
  `gps_longitude_real` double DEFAULT NULL,
  `gps_odometer` double DEFAULT NULL,
  `gps_workhour` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_id`),
  KEY `NewIndex1` (`gps_name`,`gps_host`),
  KEY `NewIndex3` (`gps_utc_coord`),
  KEY `NewIndex4` (`gps_utc_date`),
  KEY `NewIndex5` (`gps_utc_coord`,`gps_utc_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_smsgeofence_t1_2` (
  `smsgeofence_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsgeofence_time` timestamp NULL DEFAULT NULL,
  `smsgeofence_status` int(11) DEFAULT '1' COMMENT '1=keluar; 2=masuk',
  `smsgeofence_alert` int(11) DEFAULT '1' COMMENT '1=belum disms',
  `smsgeofence_device` varchar(32) DEFAULT NULL COMMENT 'fk ke table geofence',
  `smsgeofence_latitude` varchar(32) DEFAULT NULL,
  `smsgeofence_longitude` varchar(32) DEFAULT NULL,
  `smsgeofence_alerttime` timestamp NULL DEFAULT NULL COMMENT 'kapan dialert',
  `smsgeofence_geofence` int(11) DEFAULT NULL,
  PRIMARY KEY (`smsgeofence_id`),
  KEY `smsgeofence_geofence_index` (`smsgeofence_geofence`),
  KEY `smsgeofence_time_index` (`smsgeofence_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_smsmaxspeed_t1_2` (
  `smsmaxspeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsmaxspeed_vehicle` varchar(32) DEFAULT NULL,
  `smsmaxspeed_speed` double DEFAULT NULL COMMENT 'alert pada kecepatan berapa',
  `smsmaxspeed_max` double DEFAULT NULL COMMENT 'max kecepatan',
  `smsmaxspeed_status` int(11) DEFAULT '1' COMMENT '1=new;2=alerted;3=normal',
  `smsmaxspeed_alert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'waktu kecepatan melebihi max speed',
  `smsmaxspeed_normal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'waktu kecepatan kembali normal',
  `smsmaxspeed_created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsmaxspeed_id`),
  KEY `NewIndex1` (`smsmaxspeed_vehicle`),
  KEY `NewIndex2` (`smsmaxspeed_alert`),
  KEY `smsmaxspeed_created_index` (`smsmaxspeed_created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `webtracking_smsparking_t1_2` (
  `smsparking_int` int(11) NOT NULL AUTO_INCREMENT,
  `smsparking_vehicle` varchar(32) DEFAULT NULL,
  `smsparking_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_status` int(11) DEFAULT '1' COMMENT '1=parking,2=not parking',
  `smsparking_alert` int(11) DEFAULT '0' COMMENT '0=belum dialert;1=sudah dialert',
  `smsparking_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `smsparking_setting` int(11) DEFAULT NULL COMMENT 'dalam jam',
  PRIMARY KEY (`smsparking_int`),
  KEY `NewIndex1` (`smsparking_vehicle`),
  KEY `NewIndex2` (`smsparking_alert`),
  KEY `NewIndex3` (`smsparking_status`),
  KEY `smsparking_end_index` (`smsparking_end`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_smssignal_t1_2` (
  `smssignal_id` int(11) NOT NULL AUTO_INCREMENT,
  `smssignal_vehicle` varchar(32) NOT NULL,
  `smssignal_created` timestamp NULL DEFAULT NULL,
  `smssignal_type` int(11) NOT NULL COMMENT '1=OK=>NOT OK, 2=NOT OK=>OK',
  `smssignal_status` int(11) NOT NULL COMMENT '1=belum diprocess, 2=processed',
  `smssignal_alerted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`smssignal_id`),
  KEY `smssignal_vehicle_index` (`smssignal_vehicle`,`smssignal_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15764 ;

CREATE TABLE IF NOT EXISTS `webtracking_smsupdated_t1_2` (
  `smsupdated_id` int(11) NOT NULL AUTO_INCREMENT,
  `smsupdated_vehicle` varchar(32) NOT NULL,
  `smsupdated_created` timestamp NULL DEFAULT NULL,
  `smsupdated_status` int(11) NOT NULL,
  `smsupdated_alerted` timestamp NULL DEFAULT NULL,
  `smsupdated_lastupdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`smsupdated_id`),
  KEY `smsupdated_vehicle_index` (`smsupdated_vehicle`,`smsupdated_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_t1_2` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_hist_t1` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_hist_t1_1` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_hist_t1_2` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_hist_t1_pln` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_hist_t3` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_hist_t4` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_hist_t4_farrasindo` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_hist_t4_new` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `webtracking_gps_info_hist_indogps` (
  `gps_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `gps_info_device` varchar(100) DEFAULT NULL,
  `gps_info_hdop` varchar(4) DEFAULT NULL,
  `gps_info_io_port` varchar(10) DEFAULT NULL,
  `gps_info_distance` float DEFAULT NULL,
  `gps_info_alarm_data` varchar(10) DEFAULT NULL,
  `gps_info_ad_input` varchar(10) DEFAULT NULL,
  `gps_info_utc_coord` int(11) DEFAULT NULL,
  `gps_info_utc_date` int(11) DEFAULT NULL,
  `gps_info_alarm_alert` varchar(4) DEFAULT NULL,
  `gps_info_time` timestamp NULL DEFAULT NULL,
  `gps_info_status` int(11) DEFAULT '0' COMMENT '0=new;1=alarm proccessed',
  `gps_info_gps` int(11) DEFAULT NULL,
  PRIMARY KEY (`gps_info_id`),
  KEY `NewIndex1` (`gps_info_device`),
  KEY `gps_info_time` (`gps_info_time`),
  KEY `NewIndex3` (`gps_info_alarm_alert`,`gps_info_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
