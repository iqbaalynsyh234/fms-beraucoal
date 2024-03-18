CREATE TABLE IF NOT EXISTS `%s` (
  `respon_id` int(11) NOT NULL AUTO_INCREMENT,
  `respon_device` varchar(255) DEFAULT NULL,
  `respon_text` varchar(255) DEFAULT NULL,
  `respon_value` varchar(255) DEFAULT '',
  `respon_status` tinyint(1) DEFAULT '0',
  `respon_date` datetime DEFAULT NULL,
  PRIMARY KEY (`respon_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

