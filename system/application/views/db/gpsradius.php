CREATE TABLE IF NOT EXISTS `%s` (
  `radius_id` int(11) NOT NULL AUTO_INCREMENT,
  `radius_host` varchar(32) DEFAULT '',
  `radius_host_time` datetime DEFAULT NULL,
  `radius_host_coord` varchar(255) DEFAULT NULL,
  `radius_host_speed` float DEFAULT NULL,
  `radius_host_location` varchar(100) DEFAULT NULL,
  `radius_guest` varchar(32) DEFAULT '0',
  `radius_guest_time` datetime DEFAULT NULL,
  `radius_guest_coord` varchar(255) DEFAULT NULL,
  `radius_guest_speed` float DEFAULT NULL,
  `radius_meter` double DEFAULT NULL,
  `radius_event` varchar(5) DEFAULT NULL,
  `radius_event_delta` int(11) DEFAULT NULL,
  `radius_event_total` int(11) DEFAULT '0',
  `radius_status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`radius_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;