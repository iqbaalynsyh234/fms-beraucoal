CREATE TABLE IF NOT EXISTS `%s` (
  `ritase_id` int(11) NOT NULL AUTO_INCREMENT,
  `ritase_device` varchar(255) DEFAULT '',
  `ritase_value` int(10) DEFAULT '0',
  `ritase_status` tinyint(1) DEFAULT '1',
  `ritase_gpstime` datetime DEFAULT NULL,
  `ritase_coordinate` varchar(255) DEFAULT '',
  `ritase_note` varchar(255) DEFAULT '',
  `ritase_last_site` varchar(255) DEFAULT NULL,
  `ritase_last_site_datetime` datetime DEFAULT NULL,
  `ritase_last_dest` varchar(255) DEFAULT NULL,
  `ritase_last_dest_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`ritase_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

