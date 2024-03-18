<?php
		$config['template'] = 'bib/';
		$config['admin_template'] = 'admin/';
		//$config['mapinhome'] = true;
		$config['favicon'] = "assets/transporter/images/truck_blue.ico";
		$config['license'] = "www.lacak-mobil.com";
		//$config['login'] = "http://transporter.lacak-mobil.com";
		//$config['transporter_agent_id'] = 1;
		//$config['transporter_agent_name'] = "Jaya";
		//$config['transporter_user_type'] = 2;
		$config['dir_photo'] = "assets/transporter/images/photo/";
		$config['default_photo_driver'] = "default_photo_driver.png";
		$config['transporter_user_type_name'] = "Regular";
        $config['transporter_agent'] = 1;
		//$config['cust_company'] = "3";
		$config["interval_notification"] = 10000; //10 Detik
		$config["interval_geofence_alert"] = 20000; //20 Detik
		$config["interval_get_geofence_alert"] = 30000; //30 Detik

		//$config['GOOGLE_MAP_API_KEY'] = "AIzaSyDTzBAgqzSvZDDjA-5z3u7C7C8Z1VCsLaw"; //mitratrans
		$config['GOOGLE_MAP_API_KEY'] = "AIzaSyA9iQg8G8Z7FPKFD0u3BwugOBCfg_7W-SY"; //BIB PREMIUM



		$config["fan_app"] = 1; //Use Fan Application
		$config["comment_app"] = 1; //Use Comment Application

		//company yg pakai sistem DOSJ
		// 571 = kencana
		// 240 = demo transporter
		$config["app_dosj_all"] = 1; // TRUE
		$config['company_view_dosj'] = array('571');
