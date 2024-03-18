DELIMITER |

CREATE FUNCTION t1_1_getAlertMaxSpeedStatus(prname VARCHAR(32), prhost VARCHAR(32))
RETURNS INT DETERMINISTIC
BEGIN
	DECLARE pstatus INT;
	SELECT smsmaxspeed_status INTO pstatus FROM webtracking_smsmaxspeed_t1_1 WHERE smsmaxspeed_vehicle = CONCAT(prname, '@', prhost) ORDER BY smsmaxspeed_created DESC LIMIT 1 OFFSET 0;
	
	IF pstatus is null then
		RETURN 0;
	END IF;
	
	RETURN pstatus;
END|

DELIMITER ;

DELIMITER |

CREATE FUNCTION t1_1_getAlertParkingStatus(prname VARCHAR(32), prhost VARCHAR(32))
RETURNS INT DETERMINISTIC
BEGIN
	DECLARE pstatus INT;
	SELECT smsparking_status INTO pstatus FROM webtracking_smsparking_t1_1 WHERE smsparking_vehicle = CONCAT(prname, '@', prhost) ORDER BY smsparking_begin DESC LIMIT 1 OFFSET 0;
	
	IF pstatus is null then
		RETURN 0;
	END IF;
	
	RETURN pstatus;
END|

DELIMITER ;

DELIMITER |

CREATE FUNCTION t1_1_getGeofenceStatus(prname VARCHAR(32), prhost VARCHAR(32))
RETURNS INT DETERMINISTIC
BEGIN
	DECLARE pstatus INT;
	SELECT smsgeofence_status INTO pstatus FROM webtracking_smsgeofence_t1_1 WHERE smsgeofence_device = CONCAT(prname, '@', prhost) ORDER BY smsgeofence_time DESC LIMIT 1 OFFSET 0;
	
	IF pstatus is null then
		RETURN -1;
	END IF;
	
	RETURN pstatus;
END|

DELIMITER ;



DELIMITER |

CREATE FUNCTION t1_2_getAlertMaxSpeedStatus(prname VARCHAR(32), prhost VARCHAR(32))
RETURNS INT DETERMINISTIC
BEGIN
	DECLARE pstatus INT;
	SELECT smsmaxspeed_status INTO pstatus FROM webtracking_smsmaxspeed_t1_2 WHERE smsmaxspeed_vehicle = CONCAT(prname, '@', prhost) ORDER BY smsmaxspeed_created DESC LIMIT 1 OFFSET 0;
	
	IF pstatus is null then
		RETURN 0;
	END IF;
	
	RETURN pstatus;
END|

DELIMITER ;

DELIMITER |

CREATE FUNCTION t1_2_getAlertParkingStatus(prname VARCHAR(32), prhost VARCHAR(32))
RETURNS INT DETERMINISTIC
BEGIN
	DECLARE pstatus INT;
	SELECT smsparking_status INTO pstatus FROM webtracking_smsparking_t1_2 WHERE smsparking_vehicle = CONCAT(prname, '@', prhost) ORDER BY smsparking_begin DESC LIMIT 1 OFFSET 0;
	
	IF pstatus is null then
		RETURN 0;
	END IF;
	
	RETURN pstatus;
END|

DELIMITER ;

DELIMITER |

CREATE FUNCTION t1_2_getGeofenceStatus(prname VARCHAR(32), prhost VARCHAR(32))
RETURNS INT DETERMINISTIC
BEGIN
	DECLARE pstatus INT;
	SELECT smsgeofence_status INTO pstatus FROM webtracking_smsgeofence_t1_2 WHERE smsgeofence_device = CONCAT(prname, '@', prhost) ORDER BY smsgeofence_time DESC LIMIT 1 OFFSET 0;
	
	IF pstatus is null then
		RETURN -1;
	END IF;
	
	RETURN pstatus;
END|

DELIMITER ;

DELIMITER |

CREATE FUNCTION `t1_2_getLastSignal`(prname VARCHAR(32), prhost VARCHAR(32)) RETURNS int(11)
BEGIN
	DECLARE pstatus VARCHAR(2);
	SELECT gps_status INTO pstatus FROM webtracking_gps_t1_2 WHERE gps_name = prname AND gps_host = prhost ORDER BY gps_id DESC LIMIT 1 OFFSET 0;
	
	IF pstatus is null then
		RETURN 1;
	END IF;
	
	IF pstatus = 'A' THEN
		RETURN 1;
	END IF;
	
	RETURN 0;
END|

DELIMITER ;

DELIMITER |

CREATE FUNCTION `t1_2_getLastUpdate`(prname VARCHAR(32), prhost VARCHAR(32), prtime TIMESTAMP) RETURNS int(11)
BEGIN
	DECLARE lama INT;
	SELECT TIMESTAMPDIFF(MINUTE, gps_time, prtime) INTO lama FROM webtracking_gps_t1_2 WHERE gps_name = prname AND gps_host = prhost ORDER BY gps_id DESC LIMIT 1 OFFSET 0;
	
	
	IF lama is null then
		RETURN 0;
	END IF;
	
	RETURN lama;
END|

DELIMITER ;

