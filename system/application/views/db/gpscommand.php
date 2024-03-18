CREATE TABLE IF NOT EXISTS `%s` (
  `command_id` int(11) NOT NULL AUTO_INCREMENT,
  `command_device` varchar(255) DEFAULT NULL,
  `command_text` varchar(50) DEFAULT NULL,
  `command_hexa` text,
  `command_value` varchar(50) DEFAULT '',
  `command_status` int(11) DEFAULT '0',
  `command_date` datetime DEFAULT NULL,
  `command_respon` datetime DEFAULT NULL,
  `command_port` int(11) DEFAULT '0',
  `command_ip` varchar(50) NOT NULL DEFAULT '',
  `command_host` varchar(50) DEFAULT '',
  `command_note` varchar(255) DEFAULT '',
  `command_success` tinyint(1) DEFAULT '1',
  `command_success_datetime` datetime DEFAULT NULL,
  `command_reset_datetime` datetime DEFAULT NULL,
  `command_geofence` varchar(255) DEFAULT NULL,
  `command_geofence_site` varchar(255) DEFAULT NULL,
  `command_road_type` varchar(50) DEFAULT '',
  `command_gpstime` datetime DEFAULT NULL,
  `command_coordinate` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`command_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

