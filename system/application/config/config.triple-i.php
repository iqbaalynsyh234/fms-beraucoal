<?php
		$config['template'] = 'triple-i/';		
		$config['license'] = "www.lacak-mobil.com";
		$config['dir_photo'] = "assets/transporter/images/photo/";
		$config['default_photo_driver'] = "default_photo_driver.png";
		$config['transporter_user_type_name'] = "Regular";
        $config['transporter_agent'] = 1;
        $config['transporter_allow_request'] = array('18');
        $config['new_order'] = 5; //Lihat ditable transporter_request_status;
        $config["cancel_by_customer"] = 4; //Status request
        $config["booked"] = 1;
        $config["order_complete"] = 6;
        $config["interval_notification"] = 10000; //10 Detik
		$config["interval_geofence_alert"] = 20000; //20 Detik
		$config["interval_get_geofence_alert"] = 30000; //30 Detik 		
        //$config['GOOGLE_MAP_API_KEY'] = "ABQIAAAAi-rilXjtWa4N8goKibpm1hTQZCaXYr9Eb6bdggGxQOy5QP1nhxQXFrb_gY2qobVL5-YxSDVTMD2neg";
