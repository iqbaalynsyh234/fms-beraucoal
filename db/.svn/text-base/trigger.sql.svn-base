DROP TRIGGER IF EXISTS t1_oninserted;
DELIMITER |
CREATE TRIGGER t1_oninserted AFTER INSERT ON webtracking_gps
  FOR EACH ROW BEGIN
	DECLARE maxspeed DOUBLE;
	DECLARE speed DOUBLE;
	DECLARE pstatus INT;	
	DECLARE maxparking INT;
	
	DECLARE geofence INT;
	DECLARE lng DOUBLE;
	DECLARE lat DOUBLE;
  
	SET maxspeed = getMaxSpeed(NEW.gps_name, NEW.gps_host);		
	IF maxspeed > 0 THEN	
		SET pstatus = t1_getAlertMaxSpeedStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;	
		
		IF speed > maxspeed THEN
			IF pstatus = 0 OR pstatus = 3 THEN
				INSERT webtracking_smsmaxspeed_t1 SET smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsmaxspeed_speed = speed, smsmaxspeed_max = maxspeed, smsmaxspeed_status = 1, smsmaxspeed_alert = '0000-00-00 00:00:00', smsmaxspeed_normal = '0000-00-00 00:00:00', smsmaxspeed_created = NOW();
			END IF;
		ELSE
			IF pstatus = 1 OR pstatus = 2 THEN
				UPDATE webtracking_smsmaxspeed_t1 SET smsmaxspeed_status = 3, smsmaxspeed_normal = NOW() WHERE smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host);
			END IF;
		END IF;
	END IF;
	
	SET maxparking = getMaxParking(NEW.gps_name, NEW.gps_host);
	IF maxparking > 0 THEN
		SET pstatus = t1_getAlertParkingStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;
		
		IF speed = 0 THEN
			if pstatus = 0 OR pstatus = 2 then
				INSERT webtracking_smsparking_t1 SET smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsparking_begin = NEW.gps_time, smsparking_end = NEW.gps_time,  smsparking_status = 1, smsparking_alert = 0, smsparking_alerted = '0000-00-00 00:00:00', smsparking_setting = maxparking;
			ELSE
				UPDATE webtracking_smsparking_t1 SET smsparking_end = NEW.gps_time WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
			END IF;
		ELSE
			UPDATE webtracking_smsparking_t1 SET smsparking_status = 2 WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
		END IF;
		
	END IF;
	
	SET geofence = IsInside(NEW.gps_name, NEW.gps_host, NEW.gps_latitude, NEW.gps_ns, NEW.gps_longitude, NEW.gps_ew);
	IF geofence >= 0 THEN
		SET pstatus = t1_getGeofenceStatus(NEW.gps_name, NEW.gps_host);	
		IF pstatus = 1 THEN
			if geofence > 1 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t1 SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 2, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00', smsgeofence_geofence = geofence;
			END IF;
		ELSE
			if geofence = 0 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t1 SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 1, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00';
			END IF;			
		END IF;
	END IF;
		
  END;
|
DELIMITER ;


DROP TRIGGER IF EXISTS t1_1_oninserted;
DELIMITER |
CREATE TRIGGER t1_1_oninserted AFTER INSERT ON webtracking_gps_t1_1
  FOR EACH ROW BEGIN
	DECLARE maxspeed DOUBLE;
	DECLARE speed DOUBLE;
	DECLARE pstatus INT;
	
	DECLARE maxparking INT;
	
	DECLARE geofence INT;
	DECLARE lng DOUBLE;
	DECLARE lat DOUBLE;		
  
	SET maxspeed = getMaxSpeed(NEW.gps_name, NEW.gps_host);		
	IF maxspeed > 0 THEN	
		SET pstatus = t1_1_getAlertMaxSpeedStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;	
		
		IF speed > maxspeed THEN
			IF pstatus = 0 OR pstatus = 3 THEN
				INSERT webtracking_smsmaxspeed_t1_1 SET smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsmaxspeed_speed = speed, smsmaxspeed_max = maxspeed, smsmaxspeed_status = 1, smsmaxspeed_alert = '0000-00-00 00:00:00', smsmaxspeed_normal = '0000-00-00 00:00:00', smsmaxspeed_created = NOW();
			END IF;
		ELSE
			IF pstatus = 1 OR pstatus = 2 THEN
				UPDATE webtracking_smsmaxspeed_t1_1 SET smsmaxspeed_status = 3, smsmaxspeed_normal = NOW() WHERE smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host);
			END IF;
		END IF;
	END IF;
	
	SET maxparking = getMaxParking(NEW.gps_name, NEW.gps_host);
	IF maxparking > 0 THEN
		SET pstatus = t1_1_getAlertParkingStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;
		
		IF speed = 0 THEN
			IF pstatus = 0 OR pstatus = 2 THEN
				INSERT webtracking_smsparking_t1_1 SET smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsparking_begin = NEW.gps_time, smsparking_end = NEW.gps_time,  smsparking_status = 1, smsparking_alert = 0, smsparking_alerted = '0000-00-00 00:00:00', smsparking_setting = maxparking;
			ELSE
				UPDATE webtracking_smsparking_t1_1 SET smsparking_end = NEW.gps_time WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
			END IF;
		ELSE
			UPDATE webtracking_smsparking_t1_1 SET smsparking_status = 2 WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
		END IF;		
	END IF;
	
	SET geofence = IsInside(NEW.gps_name, NEW.gps_host, NEW.gps_latitude, NEW.gps_ns, NEW.gps_longitude, NEW.gps_ew);
	IF geofence >= 0 THEN
		SET pstatus = t1_1_getGeofenceStatus(NEW.gps_name, NEW.gps_host);	
		IF pstatus = 1 THEN
			if geofence > 1 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t1_1 SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 2, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00', smsgeofence_geofence = geofence;
			END IF;
		ELSE
			if geofence = 0 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t1_1 SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 1, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00';
			END IF;			
		END IF;
	END IF;	
		
  END;
|
DELIMITER ;

DELIMITER ;

DROP TRIGGER IF EXISTS t1_pln_oninserted;
DELIMITER |
CREATE TRIGGER t1_pln_oninserted AFTER INSERT ON webtracking_gps_pln
  FOR EACH ROW BEGIN
	DECLARE maxspeed DOUBLE;
	DECLARE speed DOUBLE;
	DECLARE pstatus INT;
	
	DECLARE maxparking INT;
  
	DECLARE geofence INT;
	DECLARE lng DOUBLE;
	DECLARE lat DOUBLE;		
	  
	SET maxspeed = getMaxSpeed(NEW.gps_name, NEW.gps_host);		
	IF maxspeed > 0 THEN	
		SET pstatus = t1_pln_getAlertMaxSpeedStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;	
		
		IF speed > maxspeed THEN
			IF pstatus = 0 OR pstatus = 3 THEN
				INSERT webtracking_smsmaxspeed_t1_pln SET smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsmaxspeed_speed = speed, smsmaxspeed_max = maxspeed, smsmaxspeed_status = 1, smsmaxspeed_alert = '0000-00-00 00:00:00', smsmaxspeed_normal = '0000-00-00 00:00:00', smsmaxspeed_created = NOW();
			END IF;
		ELSE
			IF pstatus = 1 OR pstatus = 2 THEN
				UPDATE webtracking_smsmaxspeed_t1_pln SET smsmaxspeed_status = 3, smsmaxspeed_normal = NOW() WHERE smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host);
			END IF;
		END IF;
	END IF;
	
	SET maxparking = getMaxParking(NEW.gps_name, NEW.gps_host);
	IF maxparking > 0 THEN
		SET pstatus = t1_pln_getAlertParkingStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;
		
		IF speed = 0 THEN
			if pstatus = 0 OR pstatus = 2 then
				INSERT webtracking_smsparking_t1_pln SET smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsparking_begin = NEW.gps_time, smsparking_end = NEW.gps_time,  smsparking_status = 1, smsparking_alert = 0, smsparking_alerted = '0000-00-00 00:00:00', smsparking_setting = maxparking;
			ELSE
				UPDATE webtracking_smsparking_t1_pln SET smsparking_end = NEW.gps_time WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
			END IF;
		ELSE
			UPDATE webtracking_smsparking_t1_pln SET smsparking_status = 2 WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
		END IF;		
	END IF;
	
	SET geofence = IsInside(NEW.gps_name, NEW.gps_host, NEW.gps_latitude, NEW.gps_ns, NEW.gps_longitude, NEW.gps_ew);
	IF geofence >= 0 THEN
		SET pstatus = t1_pln_getGeofenceStatus(NEW.gps_name, NEW.gps_host);	
		IF pstatus = 1 THEN
			if geofence > 1 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t1_pln SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 2, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00', smsgeofence_geofence = geofence;
			END IF;
		ELSE
			if geofence = 0 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t1_pln SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 1, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00';
			END IF;			
		END IF;
	END IF;		
		
  END;
|
DELIMITER ;

DROP TRIGGER IF EXISTS t3_oninserted;
DELIMITER |
CREATE TRIGGER t3_oninserted AFTER INSERT ON webtracking_gps_sms
  FOR EACH ROW BEGIN
	DECLARE maxspeed DOUBLE;
	DECLARE speed DOUBLE;
	DECLARE pstatus INT;
	
	DECLARE maxparking INT;
	
	DECLARE geofence INT;
	DECLARE lng DOUBLE;
	DECLARE lat DOUBLE;		
  
	SET maxspeed = getMaxSpeed(NEW.gps_name, NEW.gps_host);		
	IF maxspeed > 0 THEN	
		SET pstatus = t3_getAlertMaxSpeedStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;	
		
		IF speed > maxspeed THEN
			IF pstatus = 0 OR pstatus = 3 THEN
				INSERT webtracking_smsmaxspeed_t3 SET smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsmaxspeed_speed = speed, smsmaxspeed_max = maxspeed, smsmaxspeed_status = 1, smsmaxspeed_alert = '0000-00-00 00:00:00', smsmaxspeed_normal = '0000-00-00 00:00:00', smsmaxspeed_created = NOW();
			END IF;
		ELSE
			IF pstatus = 1 OR pstatus = 2 THEN
				UPDATE webtracking_smsmaxspeed_t3 SET smsmaxspeed_status = 3, smsmaxspeed_normal = NOW() WHERE smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host);
			END IF;
		END IF;
	END IF;
	
	SET maxparking = getMaxParking(NEW.gps_name, NEW.gps_host);
	IF maxparking > 0 THEN
		SET pstatus = t3_getAlertParkingStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;
		
		IF speed = 0 THEN
			if pstatus = 0 OR pstatus = 2 then
				INSERT webtracking_smsparking_t3 SET smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsparking_begin = NEW.gps_time, smsparking_end = NEW.gps_time,  smsparking_status = 1, smsparking_alert = 0, smsparking_alerted = '0000-00-00 00:00:00', smsparking_setting = maxparking;
			ELSE
				UPDATE webtracking_smsparking_t3 SET smsparking_end = NEW.gps_time WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
			END IF;
		ELSE
			UPDATE webtracking_smsparking_t3 SET smsparking_status = 2 WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
		END IF;		
	END IF;
	
	SET geofence = IsInside(NEW.gps_name, NEW.gps_host, NEW.gps_latitude, NEW.gps_ns, NEW.gps_longitude, NEW.gps_ew);
	IF geofence >= 0 THEN
		SET pstatus = t3_getGeofenceStatus(NEW.gps_name, NEW.gps_host);	
		IF pstatus = 1 THEN
			if geofence > 1 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t3 SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 2, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00', smsgeofence_geofence = geofence;
			END IF;
		ELSE
			if geofence = 0 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t3 SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 1, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00';
			END IF;			
		END IF;
	END IF;			
		
  END;
|
DELIMITER ;

DROP TRIGGER IF EXISTS t4_oninserted;
DELIMITER |
CREATE TRIGGER t4_oninserted AFTER INSERT ON webtracking_gps_gtp
  FOR EACH ROW BEGIN
	DECLARE maxspeed DOUBLE;
	DECLARE speed DOUBLE;
	DECLARE pstatus INT;
	
	DECLARE maxparking INT;
	
	DECLARE geofence INT;
	DECLARE lng DOUBLE;
	DECLARE lat DOUBLE;			
  
	SET maxspeed = getMaxSpeed(NEW.gps_name, NEW.gps_host);		
	IF maxspeed > 0 THEN	
		SET pstatus = t4_getAlertMaxSpeedStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;	
		
		IF speed > maxspeed THEN
			IF pstatus = 0 OR pstatus = 3 THEN
				INSERT webtracking_smsmaxspeed_t4 SET smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsmaxspeed_speed = speed, smsmaxspeed_max = maxspeed, smsmaxspeed_status = 1, smsmaxspeed_alert = '0000-00-00 00:00:00', smsmaxspeed_normal = '0000-00-00 00:00:00', smsmaxspeed_created = NOW();
			END IF;
		ELSE
			IF pstatus = 1 OR pstatus = 2 THEN
				UPDATE webtracking_smsmaxspeed_t4 SET smsmaxspeed_status = 3, smsmaxspeed_normal = NOW() WHERE smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host);
			END IF;
		END IF;
	END IF;
	
	SET maxparking = getMaxParking(NEW.gps_name, NEW.gps_host);
	IF maxparking > 0 THEN
		SET pstatus = t4_getAlertParkingStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;
		
		IF speed = 0 THEN
			if pstatus = 0 OR pstatus = 2 then
				INSERT webtracking_smsparking_t4 SET smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsparking_begin = NEW.gps_time, smsparking_end = NEW.gps_time,  smsparking_status = 1, smsparking_alert = 0, smsparking_alerted = '0000-00-00 00:00:00', smsparking_setting = maxparking;
			ELSE
				UPDATE webtracking_smsparking_t4 SET smsparking_end = NEW.gps_time WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
			END IF;
		ELSE
			UPDATE webtracking_smsparking_t4 SET smsparking_status = 2 WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
		END IF;		
	END IF;
	
	SET geofence = IsInside(NEW.gps_name, NEW.gps_host, NEW.gps_latitude, NEW.gps_ns, NEW.gps_longitude, NEW.gps_ew);
	IF geofence >= 0 THEN
		SET pstatus = t4_getGeofenceStatus(NEW.gps_name, NEW.gps_host);	
		IF pstatus = 1 THEN
			if geofence > 1 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t4 SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 2, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00', smsgeofence_geofence = geofence;
			END IF;
		ELSE
			if geofence = 0 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t4 SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 1, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00';
			END IF;			
		END IF;
	END IF;		
		
  END;
|
DELIMITER ;

DROP TRIGGER IF EXISTS t4_farrasindo_oninserted;
DELIMITER |
CREATE TRIGGER t4_farrasindo_oninserted AFTER INSERT ON webtracking_gps_farrasindo
  FOR EACH ROW BEGIN
	DECLARE maxspeed DOUBLE;
	DECLARE speed DOUBLE;
	DECLARE pstatus INT;
	
	DECLARE maxparking INT;
	
	DECLARE geofence INT;
	DECLARE lng DOUBLE;
	DECLARE lat DOUBLE;	
  
	SET maxspeed = getMaxSpeed(NEW.gps_name, NEW.gps_host);		
	IF maxspeed > 0 THEN	
		SET pstatus = t4_farrasindo_getAlertMaxSpeedStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;	
		
		IF speed > maxspeed THEN
			IF pstatus = 0 OR pstatus = 3 THEN
				INSERT webtracking_smsmaxspeed_t4_farrasindo SET smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsmaxspeed_speed = speed, smsmaxspeed_max = maxspeed, smsmaxspeed_status = 1, smsmaxspeed_alert = '0000-00-00 00:00:00', smsmaxspeed_normal = '0000-00-00 00:00:00', smsmaxspeed_created = NOW();
			END IF;
		ELSE
			IF pstatus = 1 OR pstatus = 2 THEN
				UPDATE webtracking_smsmaxspeed_t4_farrasindo SET smsmaxspeed_status = 3, smsmaxspeed_normal = NOW() WHERE smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host);
			END IF;
		END IF;
	END IF;
	
	SET maxparking = getMaxParking(NEW.gps_name, NEW.gps_host);
	IF maxparking > 0 THEN
		SET pstatus = t4_farrasindo_getAlertParkingStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;
		
		IF speed = 0 THEN
			if pstatus = 0 OR pstatus = 2 then
				INSERT webtracking_smsparking_t4_farrasindo SET smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsparking_begin = NEW.gps_time, smsparking_end = NEW.gps_time,  smsparking_status = 1, smsparking_alert = 0, smsparking_alerted = '0000-00-00 00:00:00', smsparking_setting = maxparking;
			ELSE
				UPDATE webtracking_smsparking_t4_farrasindo SET smsparking_end = NEW.gps_time WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
			END IF;
		ELSE
			UPDATE webtracking_smsparking_t4_farrasindo SET smsparking_status = 2 WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
		END IF;		
	END IF;
	
	SET geofence = IsInside(NEW.gps_name, NEW.gps_host, NEW.gps_latitude, NEW.gps_ns, NEW.gps_longitude, NEW.gps_ew);
	IF geofence >= 0 THEN
		SET pstatus = t4_farrasindo_getGeofenceStatus(NEW.gps_name, NEW.gps_host);	
		IF pstatus = 1 THEN
			if geofence > 1 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t4_farrasindo SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 2, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00', smsgeofence_geofence = geofence;
			END IF;
		ELSE
			if geofence = 0 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_t4_farrasindo SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 1, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00';
			END IF;			
		END IF;
	END IF;	
		
  END;
|
DELIMITER ;

DROP TRIGGER IF EXISTS indogps_oninserted;
DELIMITER |
CREATE TRIGGER indogps_oninserted AFTER INSERT ON webtracking_gps_indogps
  FOR EACH ROW BEGIN
	DECLARE maxspeed DOUBLE;
	DECLARE speed DOUBLE;
	DECLARE pstatus INT;
	
	DECLARE maxparking INT;
  
	DECLARE geofence INT;
	DECLARE lng DOUBLE;
	DECLARE lat DOUBLE;	
	  
	SET maxspeed = getMaxSpeed(NEW.gps_name, NEW.gps_host);		
	IF maxspeed > 0 THEN	
		SET pstatus = indogps_getAlertMaxSpeedStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;	
		
		IF speed > maxspeed THEN
			IF pstatus = 0 OR pstatus = 3 THEN
				INSERT webtracking_smsmaxspeed_indogps SET smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsmaxspeed_speed = speed, smsmaxspeed_max = maxspeed, smsmaxspeed_status = 1, smsmaxspeed_alert = '0000-00-00 00:00:00', smsmaxspeed_normal = '0000-00-00 00:00:00', smsmaxspeed_created = NOW();
			END IF;
		ELSE
			IF pstatus = 1 OR pstatus = 2 THEN
				UPDATE webtracking_smsmaxspeed_indogps SET smsmaxspeed_status = 3, smsmaxspeed_normal = NOW() WHERE smsmaxspeed_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host);
			END IF;
		END IF;
	END IF;
	
	SET maxparking = getMaxParking(NEW.gps_name, NEW.gps_host);
	IF maxparking > 0 THEN
		SET pstatus = indogps_getAlertParkingStatus(NEW.gps_name, NEW.gps_host);
		SET speed = NEW.gps_speed*1.852;
		
		IF speed = 0 THEN
			if pstatus = 0 OR pstatus = 2 then
				INSERT webtracking_smsparking_indogps SET smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsparking_begin = NEW.gps_time, smsparking_end = NEW.gps_time,  smsparking_status = 1, smsparking_alert = 0, smsparking_alerted = '0000-00-00 00:00:00', smsparking_setting = maxparking;
			ELSE
				UPDATE webtracking_smsparking_indogps SET smsparking_end = NEW.gps_time WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
			END IF;
		ELSE
			UPDATE webtracking_smsparking_indogps SET smsparking_status = 2 WHERE smsparking_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host) AND smsparking_status = 1;
		END IF;		
	END IF;
	
	SET geofence = IsInside(NEW.gps_name, NEW.gps_host, NEW.gps_latitude, NEW.gps_ns, NEW.gps_longitude, NEW.gps_ew);
	IF geofence >= 0 THEN
		SET pstatus = indogps_getGeofenceStatus(NEW.gps_name, NEW.gps_host);	
		IF pstatus = 1 THEN
			if geofence > 1 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_indogps SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 2, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00', smsgeofence_geofence = geofence;
			END IF;
		ELSE
			if geofence = 0 then
				SET lat = getLatitude(NEW.gps_latitude, NEW.gps_ns);
				SET lng = getLatitude(NEW.gps_longitude, NEW.gps_ew);
			
				INSERT webtracking_smsgeofence_indogps SET smsgeofence_time = NEW.gps_time, smsgeofence_status = 1, smsgeofence_alert = 1, smsgeofence_device = CONCAT(NEW.gps_name, '@', NEW.gps_host), smsgeofence_latitude = lat, smsgeofence_longitude = lng, smsgeofence_alerttime = '0000-00-00 00:00:00';
			END IF;			
		END IF;
	END IF;		
		
  END;
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `t1_onbeforeinserted` BEFORE INSERT ON `webtracking_gps`
 FOR EACH ROW BEGIN	
		DECLARE lsignal INT;
		
		SET lsignal = t1_getLastSignal(NEW.gps_name, NEW.gps_host);
		
		IF lsignal = 1 THEN
			IF NEW.gps_status = 'V' THEN
				INSERT webtracking_smssignal_t1 SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 1, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		ELSE
			IF NEW.gps_status = 'A' THEN
				INSERT webtracking_smssignal_t1 SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 2, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		END IF;
	END|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `t4_farrasindo_onbeforeinserted` BEFORE INSERT ON `webtracking_gps_farrasindo`
 FOR EACH ROW BEGIN	
		DECLARE lsignal INT;
		
		SET lsignal = t4_farrasindo_getLastSignal(NEW.gps_name, NEW.gps_host);
		
		IF lsignal = 1 THEN
			IF NEW.gps_status = 'V' THEN
				INSERT webtracking_smssignal_t4_farrasindo SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 1, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		ELSE
			IF NEW.gps_status = 'A' THEN
				INSERT webtracking_smssignal_t4_farrasindo SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 2, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		END IF;
	END|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `t4_onbeforeinserted` BEFORE INSERT ON `webtracking_gps_gtp`
 FOR EACH ROW BEGIN	
		DECLARE lsignal INT;
		
		SET lsignal = t4_getLastSignal(NEW.gps_name, NEW.gps_host);
		
		IF lsignal = 1 THEN
			IF NEW.gps_status = 'V' THEN
				INSERT webtracking_smssignal_t4 SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 1, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		ELSE
			IF NEW.gps_status = 'A' THEN
				INSERT webtracking_smssignal_t4 SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 2, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		END IF;
	END|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `t4_new_onbeforeinserted` BEFORE INSERT ON `webtracking_gps_gtp_new`
 FOR EACH ROW BEGIN	
		DECLARE lsignal INT;
		
		SET lsignal = t4_new_getLastSignal(NEW.gps_name, NEW.gps_host);
		
		IF lsignal = 1 THEN
			IF NEW.gps_status = 'V' THEN
				INSERT webtracking_smssignal_t4_new SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 1, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		ELSE
			IF NEW.gps_status = 'A' THEN
				INSERT webtracking_smssignal_t4_new SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 2, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		END IF;
	END|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `indogps_onbeforeinserted` BEFORE INSERT ON `webtracking_gps_indogps`
 FOR EACH ROW BEGIN	
		DECLARE lsignal INT;
		
		SET lsignal = indogps_getLastSignal(NEW.gps_name, NEW.gps_host);
		
		IF lsignal = 1 THEN
			IF NEW.gps_status = 'V' THEN
				INSERT webtracking_smssignal_indogps SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 1, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		ELSE
			IF NEW.gps_status = 'A' THEN
				INSERT webtracking_smssignal_indogps SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 2, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		END IF;
	END|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `t1_pln_onbeforeinserted` BEFORE INSERT ON `webtracking_gps_pln`
 FOR EACH ROW BEGIN	
		DECLARE lsignal INT;
		
		SET lsignal = t1_pln_getLastSignal(NEW.gps_name, NEW.gps_host);
		
		IF lsignal = 1 THEN
			IF NEW.gps_status = 'V' THEN
				INSERT webtracking_smssignal_t1_pln SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 1, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		ELSE
			IF NEW.gps_status = 'A' THEN
				INSERT webtracking_smssignal_t1_pln SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 2, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		END IF;
	END|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `t3_onbeforeinserted` BEFORE INSERT ON `webtracking_gps_sms`
 FOR EACH ROW BEGIN	
		DECLARE lsignal INT;
		
		SET lsignal = t3_getLastSignal(NEW.gps_name, NEW.gps_host);
		
		IF lsignal = 1 THEN
			IF NEW.gps_status = 'V' THEN
				INSERT webtracking_smssignal_t3 SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 1, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		ELSE
			IF NEW.gps_status = 'A' THEN
				INSERT webtracking_smssignal_t3 SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 2, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		END IF;
	END|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `t1_1_onbeforeinserted` BEFORE INSERT ON `webtracking_gps_t1_1`
 FOR EACH ROW BEGIN	
		DECLARE lsignal INT;
		
		SET lsignal = t1_1_getLastSignal(NEW.gps_name, NEW.gps_host);
		
		IF lsignal = 1 THEN
			IF NEW.gps_status = 'V' THEN
				INSERT webtracking_smssignal_t1_1 SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 1, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		ELSE
			IF NEW.gps_status = 'A' THEN
				INSERT webtracking_smssignal_t1_1 SET smssignal_vehicle = CONCAT(NEW.gps_name, '@', NEW.gps_host), smssignal_created = NOW(), smssignal_type = 2, smssignal_status = 1, smssignal_alerted = NOW();
			END IF;
		END IF;
	END|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `t4_info_onbeforeinserted` BEFORE INSERT ON `webtracking_gps_info_gtp`
 FOR EACH ROW BEGIN	
		IF NEW.gps_info_alarm_alert = '01' THEN
			INSERT webtracking_sms_sos_t4 SET sms_sos_vehicle = NEW.gps_info_device, sms_sos_created = NOW(), sms_sos_status = 1, sms_sos_alerted = NOW(), sms_sos_type = NEW.gps_info_alarm_data;
		END IF;
	END|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `t4_info_farrasindo_onbeforeinserted` BEFORE INSERT ON `webtracking_gps_info_farrasindo`
 FOR EACH ROW BEGIN	
		IF NEW.gps_info_alarm_alert = '01' THEN
			INSERT webtracking_sms_sos_t4_farrasindo SET sms_sos_vehicle = NEW.gps_info_device, sms_sos_created = NOW(), sms_sos_status = 1, sms_sos_alerted = NOW(), sms_sos_type = NEW.gps_info_alarm_data;
		END IF;
	END|
DELIMITER ;

DELIMITER |
CREATE TRIGGER `t4_new_info_onbeforeinserted` BEFORE INSERT ON `webtracking_gps_info_gtp_new`
 FOR EACH ROW BEGIN	
		IF NEW.gps_info_alarm_alert = '01' THEN
			INSERT webtracking_sms_sos_t4_new SET sms_sos_vehicle = NEW.gps_info_device, sms_sos_created = NOW(), sms_sos_status = 1, sms_sos_alerted = NOW(), sms_sos_type = NEW.gps_info_alarm_data;
		END IF;

	END|
DELIMITER ;
