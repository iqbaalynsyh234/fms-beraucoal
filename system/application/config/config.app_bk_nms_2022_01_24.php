<?php
	$config['APPKEYWORDS'] = "pelacak mobil, gps, gps tracking, tracker, rute mobil, tracking service, tracking, route, tracking, suatu sistem alat keamanan mobil yang dikembangkan antara gabungan sistem GPS (Global Positioning System) dengan teknologi GSM (mobile phone/handphone) yang memungkinkan anda dapat selalu mengontrol, melacak dan memantau mobil kesayangan anda dari mana saja dan kapan saja";
	$config['APPDESCRIPTION'] = "pelacak mobil, gps, gps tracking, tracker, rute mobil, tracking service, tracking, route, tracking, suatu sistem alat keamanan mobil yang dikembangkan antara gabungan sistem GPS (Global Positioning System) dengan teknologi GSM (mobile phone/handphone) yang memungkinkan anda dapat selalu mengontrol, melacak dan memantau mobil kesayangan anda dari mana saja dan kapan saja";

	$config['admin_name'] = "Support lacak-mobil.com";

	$config['session_lang'] = 'indonesia';
	$config['session_name'] = "lacakmobil";

	$config['GPSANDALASID'] = 3;
	$config['GPSANDALAS_MAIL'] = array("norman_ab@gpsandalas.com");

	$config['INVOICE_AGENT'] = array(1);

	$config['zoom_realtime'] = 17;
	$config['zoom_history'] = 11;
	$config['zoom_poi'] = 17;
    $config['zoom_poi_history'] = 50;

	$config['timer_realtime'] = 60000;
	$config['timer_list'] = 100;
	$config['timer_updated'] = 0.1; // 1 menit

	$config['css_tracker_delay'][] = array(1440, 	"#ff0000", "#ffffff", -1);
	$config['css_tracker_delay'][] = array(60, 	"#ffff00", "#000000", 1440); // menit, background, fore
	$config['css_tracker_delay'][] = array(0, 	"#ffffff", "#000000");

	//admin
	$config['css_tracker_delay_admin'][] = array(1440, 	"#ff0000", "#ffffff", -1);
	$config['css_tracker_delay_admin'][] = array(10, 	"#ffff00", "#000000", 1440);
	$config['css_tracker_delay_admin'][] = array(0, 	"#ffffff", "#000000");

	//for ssi
	$config['css_tracker_delay_ssi'][] = array(180, 	"#ff0000", "#ffffff", -1); //red condition
	$config['css_tracker_delay_ssi'][] = array(60, 	"#ffff00", "#000000", 1440); //yellow condition
	$config['css_tracker_delay_ssi'][] = array(0, 	"#ffffff", "#000000");

	//for pins_indonesia
	$config['css_tracker_delay_pins'][] = array(157680000, 	"#ff0000", "#ffffff", -1); //red condition 24 jam 			//data before : 1440 		//rev >= 5 tahun = red
	$config['css_tracker_delay_pins'][] = array(31536000, 	"#ffff00", "#000000", 157680000); //yellow condition 12 jam //data before : 720 , 1440	//rev 1 tahun - 5 tahun = yellow
	$config['css_tracker_delay_pins'][] = array(0, 	"#ffffff", "#000000");

	$config['limit_records'] = 10;
	$config['history_limit_records'] = 10;

	$config['lagent_name'] = "Agent Name";
	$config['csv_separator'] = ';';

	$config['upload']['upload_path'] = BASEPATH."../assets/images/POI/";;
	$config['upload']['allowed_types'] = 'gif|jpg|png';
	$config['upload']['max_size']	= '100';
	$config['upload']['max_width']  = '256';
	$config['upload']['max_height']  = '256';

	$config['importpoi']['upload_path'] = BASEPATH."../assets/upload/importpoi/";;
	$config['importpoi']['allowed_types'] = 'application/octet-stream';
	//$config['importpoi'][''] = 'kml';

	//$config['googlecity'] = "JAKARTA TIMUR,JAKARTA PUSAT,JAKARTA SELATAN,JAKARTA BARAT,JAKARTA UTARA,BANDUNG,SURABAYA"; // pisahkan dengan koma, jangan ada spasi
	$config['googlecity'] = "KUTAI";

	$config['alarmtimer'] = 60; // dalam detik;
	$config['alarmtype'] = array(
							  "01"=>"SOS emergency"
							, "10"=>"low battery alarm"
							, "11"=>"overspeed alarm"
							, "12"=>"geofence alarm"
							, "15"=>"No GPS signal alarm"
							, "20"=>"Cut off power supply"
							, "35"=>"Geofence Outside alarm"
							, "53"=>"Geofence Inside alarm"
						);

	//$config['georeverse_host'] = "119.235.20.251";
	$config['georeverse_host'] = "www.lacak-mobil.com";

	$config['google_georeverse_api']['v2'] = "http://maps.google.com/maps/api/geocode/json?latlng=%s,%s&sensor=true";
	$config['google_georeverse_api']['v3'] = "https://maps.googleapis.com/maps/api/geocode/json?latlng=%s,%s&sensor=true";

	$config['google_georeverse_active'] = "v3";

	$config['sites'] = array("lacak-mobil.com", "tracker.gpsandalas.com", "app.oto-track.com", "app.nusa-track.com");
	$config['serviceamount'] = "";

	$config['LIMITS'] = array(10, 30, 50, 100, 300, 500);
	$config['ANIMATE_LONG'] = 4;

	$config['skipnavigation']['member']['clock'] = true;
	$config['skipnavigation']['map']['lastinfo'] = true;
	$config['skipnavigation']['user']['cekreqinfo'] = true;
	$config['skipnavigation']['alarm']['getcount'] = true;
	$config['skipnavigation']['member']['clock'] = true;
	$config['skipnavigation']['announcement'][''] = true;
	$config['skipnavigation']['announcement']['search'] = true;

	$config['payment_type'] = array(1=>"labodemen", "lyearly");

	$config['service']['gps'][] = "webtracking_gprmc_13520_1";
	$config['service']['gps'][] = "webtracking_gprmc_13502";
	$config['service']['gps_farrasindo'][] = "webtracking_farrasindo_13522";
	$config['service']['gps_gtp'][] = "webtracking_gtp_13521_1";
	$config['service']['gps_indogps'][] = "webtracking_prpv_13420";
	$config['service']['gps_sms'][] = "webtracking_sms_13540";
	$config['service']['gps_t1_1'][] = "webtracking_gprmc_13503_T1_1";
	$config['service']['gps_gtp_new'][] = "webtracking_gtp_new_13525";
	$config['service']['gps_t1_2'][] = "webtracking_gprmc_13505_T1_2_UDP";
	$config['service']['gps_t5'][] = "webtracking_13506_T5";
	$config['service']['gps_t5_pulse'][] = "webtracking_T5_Pulse_13507";
	$config['service']['gps_pln'][] = "webtracking_gprmc_13504_T1_pln";

	$config['maxspeed']['gps'] = "smsmaxspeed_t1";
	$config['maxspeed']['gps_farrasindo'] = "smsmaxspeed_t4_farrasindo";
	$config['maxspeed']['gps_gtp'] = "smsmaxspeed_t4";
	$config['maxspeed']['gps_indogps'] = "smsmaxspeed_indogps";
	$config['maxspeed']['gps_sms'] = "smsmaxspeed_t3";
	$config['maxspeed']['gps_t1_1'] = "smsmaxspeed_t1_1";
	$config['maxspeed']['gps_gtp_new'] = "smsmaxspeed_t4_new";
	$config['maxspeed']['gps_t1_2'] = "smsmaxspeed_t1_2";
	$config['maxspeed']['gps_t5'] = "smsmaxspeed_t5";
	$config['maxspeed']['gps_t5_pulse'] = "smsmaxspeed_t5_pulse";
	$config['maxspeed']['gps_pln'] = "smsmaxspeed_t1_pln";
	$config['maxspeed']['gps_fuel'] = "smsmaxspeed_t5_fuel";

	$config['parking']['gps'] = "smsparking_t1";
	$config['parking']['gps_farrasindo'] = "smsparking_t4_farrasindo";
	$config['parking']['gps_gtp'] = "smsparking_t4";
	$config['parking']['gps_indogps'] = "smsparking_indogps";
	$config['parking']['gps_sms'] = "smsparking_t3";
	$config['parking']['gps_t1_1'] = "smsparking_t1_1";
	$config['parking']['gps_gtp_new'] = "smsparking_t4_new";
	$config['parking']['gps_t1_2'] = "smsparking_t1_2";
	$config['parking']['gps_t5'] = "smsparking_t5";
	$config['parking']['gps_t5_pulse'] = "smsparking_t5_pulse";
	$config['parking']['gps_pln'] = "smsparking_t1_pln";
	$config['parking']['gps_fuel'] = "smsparking_t5_fule";

	$config['MYSQL_DUMP_PATH'] = "/home/lacakmobil/dump/";
	$config['MYSQL_DUMP_CLI'] = "mysqldump -h 192.168.1.101 -uadilahsoft -pbismillaah webtracking --no-create-info %s > %s%s";
	$config['erpservice'] = "http://www.lacak-mobil.com";

	//$config['SERVER_TRACKERS'] = array("119.235.20.251"=>"COLO 1 (Windows XP)", "202.129.190.194"=>"COLO 2 (CENTOS)");
	$config['SERVER_TRACKERS'] = array("119.235.20.251"=>"COLO 1 (Windows XP)", "202.129.190.194"=>"COLO 2 (CENTOS)", "119.235.20.252"=>"COLO 3 (SERVICE SERVER)", "103.253.107.156"=>"COLO 4 (GOBLIN)");
	$config['socket_server'] = array("30000"=>"Lacak Mobil T5 (30000)","30002"=>"Lacak Mobil T5 (30002)", "30009"=>"Lacak Mobil T5 (30009)", "30010"=>"Lacak Mobil T5 (30010)",
								"30013"=>"Lacak Mobil T5 (30013)","30015"=>"Lacak Mobil T5 (30015)","30016"=>"Lacak Mobil T5 (30016)",
								"30017"=>"Lacak Mobil T1 (30017)","30018"=>"Lacak Mobil T4 (30018)",
								"30001"=>"Farrasindo","30003"=>"Dokar","30004"=>"KOPINDOSAT","30012"=>"POWERBLOCK",
								"30005"=>"Lacak Mobil T5Pulse (30005)","30006"=>"OTO TRACK T5Pulse (30006)","30011"=>"OTO TRACK T5Pulse (30011)",
								"30007"=>"GPSAndalas T5Pulse (30007)", "30014"=>"GPS ANDALAS T5 (30014)",
								"30008"=>"Intan Utama");

			$config['port_server'] = array(
								"80001"=>"lacak mobil TK315DOOR (80001)",
								"80002"=>"lacak mobil TK315N (80002)",
								"80003"=>"lacak mobil TK315DOOR-Balrich-RadjaKiani(80003)",
								);

	$config['direction'] = array("Utara", "Timur Laut", "Timur Laut", "Timur Laut", "Timur", "Tenggara", "Tenggara", "Tenggara", "Selatan", "Barat Daya", "Barat Daya", "Barat Daya", "Barat", "Barat Laut", "Barat Laut", "Barat Laut");

	$config['geofencetype'] = array("ho"=>"Head Office","pl"=>"Pool","cust"=>"Customer","bo"=>"Branch Office","rest"=>"Rest Area","pom"=>"POM Bensin","st"=>"Site");

	$config['dbhistory_default'] = "gpshistory";
	$config['is_dbhistory'] = 1;

	//Vehicle PCL/DHL
	/*$config['vehicle_pcl_dhl'] = array("061451461159@T8","061453203991@T8","061451461108@T8","061451461138@T8","061453203985@T8","061451596376@T8","061451461356@T8",
									   "061451461304@T8","061451461306@T8","088888000146@T8","088888000147@T8","061451461106@T8","014513564760@T8","061451461158@T8",
									   "061452086531@T8","061451461156@T8","061452899502@T8","061452086429@T8","061452899587@T8","061453203782@T8","061451461305@T8",
									   "4700460246@A13","4700460408@A13","4700460434@A13","4700460498@A13","4700476185@A13","4700262150@A13","4700460496@A13","002100004507@T5",
									   "061451461278@T8","061451461139@T8","061451461160@T8","061451461302@T8");
	*/
	$config['vehicle_pcl_dhl'] = array("4700460246@A13","4700460408@A13","4700460434@A13","4700460498@A13","4700476185@A13",
									   "4700262150@A13","4700460496@A13","002100004507@T5","352312090031926@TK309",
									   "006100000487@T5","061453203995@T8","4700476188@A13","4700476216@A13","352312090032510@TK309",
									   "061453838110@T8","4700460250@A13","002100004506@T5","002100005761@T5",
									   "352312090341945@TK315","352312090692354@TK309","4700460439@A13"
									   );
	$config['vehicle_simarno_transtrack'] = array("002100004561@T5","088888000150@T8","088888000151@T8","002100004563@T5","088888000149@T8","352312090615652@TK309");


	$config['url_api_dhl'] = "http://www.muliatrack.com/wspubdhlgps/service.asmx/UpdatePosisi";
	$config['url_api_transtrack'] = "http://telematics.transtrack.id:6055?";
	$config['user_pins'] = array("3409");
	$config['user_hide_simno'] = array("3396","3371","3536","3543","3357","3369","3701","3364","3523","3222","3363","3366","3493",
										"3359","3547","3362","3365","3553","3387","3360","3670","3630","3672","3223","3224","3899",
										"3900","3901","3902","3996");

	$config['user_view_customreport'] = array("2590","631","1445","3878","3903","3904","3212","3950","3957","1933","3974","4023",
												"1745","4047","4064","1715","4080","4084","3897","3898","4110","4120","1821","4174");
	$config['user_view_pto'] = array("389", "2516");
	$config['user_view_snap'] = array("4122", "4120", "4164", "4166" , "4178");

	$config['limit_snap_report'] = 30;

	$config['wa_monitoring1'] = "+628558208484";
	$config['wa_monitoring2'] = "+628119338009";
	$config['wa_monitoring3'] = "+628111108236";
	$config['wa_hallo'] = "Hallo. Ada yang ingin saya tanyakan.";
	$config['vehicle_demo_transporter'] = 4110;

	$config['TELEGRAM_SERVICE_ALERT'] = "-633424308"; //BIB SERVICE ALERT


	$config['street_register'] = array(
															"PORT BIB","PORT BIR","PORT TIA","PORT BBC",
															"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
															"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","ROM EST 2","PIT CK","PIT PASOPATI","ROM PASOPATI","ROM GH",
															"ROM B1 ROAD","ROM B2 ROAD","ROM B3 ROAD","EST ROAD","ROM 06 ROAD",

															"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI","POOL KUSAN BAWAH",
															"POOL KUSAN","Jalur PCN",
															"WS USAHA BARU","WS GROUP JULIUS","WS SEBAMBAN MOTOR","WS GROUP SUWARNO","WS GROUP TRI","WS SAWITAN 6","WS MELFIDA",
															"WS BEP","WS BEP 2","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MKS 2","WS MKS 3","WS MMS","WS RBT",
															"WS ANEKA","WS EST 32","WS MEKAR JAYA","WS MEKAR JAYA 2","WS MEKAR JAYA 3","PADAIDI","WS SEBAMBAN","WS Umum Simpang Telkom","WS JOAIRBRUSH","RM UMUM 1",
															"POOL Bagong Office",


															"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
															"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
															"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
															"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31.5","KM 32","KM 32.5","KM 33","KM 33.5","KM 34","KM 34.5","KM 35","KM 35.5","KM 36",

															"KM 15.5 B1","KM 16 B1","KM 16.5 B1","KM 17 B1","KM 17.5 B1",
															"KM 20.5 B2","KM 21 B2","KM 21.5 B2","KM 22 B2","KM 22.5 B2",
															"KM 21.5 B3","KM 22 B3","KM 22.5 B3","KM 23 B3","KM 23.5 B3","KM 24 B3","KM 24.5 B3","KM 25 B3","KM 25.5 B3","KM 26 B3","KM 26.5 B3","KM 27 B3",
															"KM 24 A1","KM 24.5 A1","KM 25 A1","KM 25.5 A1","KM 26 A1","KM 26.5 A1",

															"TIA KM 0","TIA KM 0.5","TIA KM 1","TIA KM 1.5","TIA KM 2","TIA KM 2.5","TIA KM 3","TIA KM 3.5","TIA KM 4","TIA KM 4.5","TIA KM 5",
															"TIA KM 5.5","TIA KM 6","TIA KM 6.5","TIA KM 7","TIA KM 7.5","TIA KM 8","TIA KM 8.5","TIA KM 9","TIA KM 9.5","TIA KM 10","TIA KM 10.5",
															"TIA KM 11","TIA KM 11.5","TIA KM 12","TIA KM 12.5","TIA KM 13","TIA KM 13.5","TIA KM 14","TIA KM 14.5","TIA KM 15",
															"TIA KM 15.5","TIA KM 16","TIA KM 16.5","TIA KM 17","TIA KM 17.5","TIA KM 18","TIA KM 18.5","TIA KM 19","TIA KM 19.5","TIA KM 20",
															"TIA KM 20.5","TIA KM 21","TIA KM 21.5","TIA KM 22","TIA KM 22.5","TIA KM 23","TIA KM 23.5","TIA KM 24","TIA KM 24.5","TIA KM 25",
															"TIA KM 25.5","TIA KM 26","TIA KM 26.5","TIA KM 27","TIA KM 27.5","TIA KM 28","TIA KM 28.5","TIA KM 29","TIA KM 29.5","TIA KM 30",
															"Jalur TIA Utara","Jalur TIA Selatan",

															"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7","BIB CP 8",
															"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
															"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
															"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
															"PORT BIB - Antrian","Port BIB - Antrian"
										);


	$config['redzone_register'] = array("STU area","PCN area",
										"Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom", "Non BIB Angsana","Non BIB TBR/SDJ"
										);

	$config['street_register_all'] = array(
															"PORT BIB","PORT BIR","PORT TIA","PORT BBC",
															"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
															"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","ROM EST 2","PIT CK","PIT PASOPATI","ROM PASOPATI","ROM GH",
															"ROM B1 ROAD","ROM B2 ROAD","ROM B3 ROAD","EST ROAD","ROM 06 ROAD",

															"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI","POOL KUSAN BAWAH",
															"POOL KUSAN","Jalur PCN",
															"WS USAHA BARU","WS GROUP JULIUS","WS SEBAMBAN MOTOR","WS GROUP SUWARNO","WS GROUP TRI","WS SAWITAN 6","WS MELFIDA",
															"WS BEP","WS BEP 2","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MKS 2","WS MKS 3","WS MMS","WS RBT",
															"WS ANEKA","WS EST 32","WS MEKAR JAYA","WS MEKAR JAYA 2","WS MEKAR JAYA 3","PADAIDI","WS SEBAMBAN","WS Umum Simpang Telkom","WS JOAIRBRUSH","RM UMUM 1",
															"POOL Bagong Office",

															"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
															"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
															"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
															"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31.5","KM 32","KM 32.5","KM 33","KM 33.5","KM 34","KM 34.5","KM 35","KM 35.5","KM 36",

															"KM 15.5 B1","KM 16 B1","KM 16.5 B1","KM 17 B1","KM 17.5 B1",
															"KM 20.5 B2","KM 21 B2","KM 21.5 B2","KM 22 B2","KM 22.5 B2",
															"KM 21.5 B3","KM 22 B3","KM 22.5 B3","KM 23 B3","KM 23.5 B3","KM 24 B3","KM 24.5 B3","KM 25 B3","KM 25.5 B3","KM 26 B3","KM 26.5 B3","KM 27 B3",
															"KM 24 A1","KM 24.5 A1","KM 25 A1","KM 25.5 A1","KM 26 A1","KM 26.5 A1",

															"TIA KM 0","TIA KM 0.5","TIA KM 1","TIA KM 1.5","TIA KM 2","TIA KM 2.5","TIA KM 3","TIA KM 3.5","TIA KM 4","TIA KM 4.5","TIA KM 5",
															"TIA KM 5.5","TIA KM 6","TIA KM 6.5","TIA KM 7","TIA KM 7.5","TIA KM 8","TIA KM 8.5","TIA KM 9","TIA KM 9.5","TIA KM 10","TIA KM 10.5",
															"TIA KM 11","TIA KM 11.5","TIA KM 12","TIA KM 12.5","TIA KM 13","TIA KM 13.5","TIA KM 14","TIA KM 14.5","TIA KM 15",
															"TIA KM 15.5","TIA KM 16","TIA KM 16.5","TIA KM 17","TIA KM 17.5","TIA KM 18","TIA KM 18.5","TIA KM 19","TIA KM 19.5","TIA KM 20",
															"TIA KM 20.5","TIA KM 21","TIA KM 21.5","TIA KM 22","TIA KM 22.5","TIA KM 23","TIA KM 23.5","TIA KM 24","TIA KM 24.5","TIA KM 25",
															"TIA KM 25.5","TIA KM 26","TIA KM 26.5","TIA KM 27","TIA KM 27.5","TIA KM 28","TIA KM 28.5","TIA KM 29","TIA KM 29.5","TIA KM 30",
															"Jalur TIA Utara","Jalur TIA Selatan",

															"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7","BIB CP 8",
															"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
															"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
															"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
															"PORT BIB - Antrian","Port BIB - Antrian"

										);


	$config['street_register_autocheck'] = array(
															"PORT BIB","PORT BIR","PORT TIA","PORT BBC",
															"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
															"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","ROM EST 2","PIT CK","PIT PASOPATI","ROM PASOPATI","ROM GH",
															"ROM B1 ROAD","ROM B2 ROAD","ROM B3 ROAD","EST ROAD","ROM 06 ROAD",

															"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI","POOL KUSAN BAWAH",
															"POOL KUSAN","Jalur PCN",
															"WS USAHA BARU","WS GROUP JULIUS","WS SEBAMBAN MOTOR","WS GROUP SUWARNO","WS GROUP TRI","WS SAWITAN 6","WS MELFIDA",
															"WS BEP","WS BEP 2","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MKS 2","WS MKS 3","WS MMS","WS RBT",
															"WS ANEKA","WS EST 32","WS MEKAR JAYA","WS MEKAR JAYA 2","WS MEKAR JAYA 3","PADAIDI","WS SEBAMBAN","WS Umum Simpang Telkom","WS JOAIRBRUSH","RM UMUM 1",
															"POOL Bagong Office",

															"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
															"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
															"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
															"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31.5","KM 32","KM 32.5","KM 33","KM 33.5","KM 34","KM 34.5","KM 35","KM 35.5","KM 36",

															"KM 15.5 B1","KM 16 B1","KM 16.5 B1","KM 17 B1","KM 17.5 B1",
															"KM 20.5 B2","KM 21 B2","KM 21.5 B2","KM 22 B2","KM 22.5 B2",
															"KM 21.5 B3","KM 22 B3","KM 22.5 B3","KM 23 B3","KM 23.5 B3","KM 24 B3","KM 24.5 B3","KM 25 B3","KM 25.5 B3","KM 26 B3","KM 26.5 B3","KM 27 B3",
															"KM 24 A1","KM 24.5 A1","KM 25 A1","KM 25.5 A1","KM 26 A1","KM 26.5 A1",

															"TIA KM 0","TIA KM 0.5","TIA KM 1","TIA KM 1.5","TIA KM 2","TIA KM 2.5","TIA KM 3","TIA KM 3.5","TIA KM 4","TIA KM 4.5","TIA KM 5",
															"TIA KM 5.5","TIA KM 6","TIA KM 6.5","TIA KM 7","TIA KM 7.5","TIA KM 8","TIA KM 8.5","TIA KM 9","TIA KM 9.5","TIA KM 10","TIA KM 10.5",
															"TIA KM 11","TIA KM 11.5","TIA KM 12","TIA KM 12.5","TIA KM 13","TIA KM 13.5","TIA KM 14","TIA KM 14.5","TIA KM 15",
															"TIA KM 15.5","TIA KM 16","TIA KM 16.5","TIA KM 17","TIA KM 17.5","TIA KM 18","TIA KM 18.5","TIA KM 19","TIA KM 19.5","TIA KM 20",
															"TIA KM 20.5","TIA KM 21","TIA KM 21.5","TIA KM 22","TIA KM 22.5","TIA KM 23","TIA KM 23.5","TIA KM 24","TIA KM 24.5","TIA KM 25",
															"TIA KM 25.5","TIA KM 26","TIA KM 26.5","TIA KM 27","TIA KM 27.5","TIA KM 28","TIA KM 28.5","TIA KM 29","TIA KM 29.5","TIA KM 30",
															"Jalur TIA Utara","Jalur TIA Selatan",

															"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7","BIB CP 8",
															"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
															"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
															"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
															"PORT BIB - Antrian","Port BIB - Antrian"
												);

	$config['port_register_autocheck'] = array(				"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7","BIB CP 8",
															"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
															"PORT BIB","PORT BIR","PORT TIA"
												);


	$config['rom_register_autocheck'] = array(			"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","ROM EST 2","PIT CK","PIT PASOPATI","ROM PASOPATI","ROM GH",
														"Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom","Non BIB Anzawara","Non BIB TBR/SDJ"

											 );

	$config['rombib_register_autocheck']  = array(	"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","ROM EST 2","PIT CK","PIT PASOPATI","ROM PASOPATI","ROM GH"

												 );

	$config['wim_register_autocheck'] = array("KM 13.5");

	$config['grayarea_register_autocheck'] = array("KM 6","KM 6.5");

	$config['nonbib_register_autocheck'] = array("SUNGAI DANAU","KINTAB","sungai danau","kintab","Non BIB Anzawara","Non BIB TBR/SDJ"

												);


	$config['bayah_muatan_register_autocheck'] = array(	"Port BIB - Antrian","PORT BIB - Antrian","Port BIR - Antrian WB"

														);

	$config['bayah_kosongan_register_autocheck'] = array(	"Port BIB - Kosongan 1","Port BIB - Kosongan 2","Port BIR - Kosongan 1",
															"Port BIR - Kosongan 2", "Simpang Bayah - Kosongan"
														);

   // hanya opr hauling ROM, HAULING, PORT BIB
	$config['street_onduty_autocheck'] = array(
															"PORT BIB","PORT BIR","PORT TIA",
															//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
															"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","ROM EST 2","PIT CK","PIT PASOPATI","ROM PASOPATI","ROM GH",
															"ROM B1 ROAD","ROM B2 ROAD","ROM B3 ROAD","EST ROAD","ROM 06 ROAD",

															//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI","POOL KUSAN BAWAH",
															//"POOL KUSAN","Jalur PCN",
															//"WS USAHA BARU","WS GROUP JULIUS","WS SEBAMBAN MOTOR","WS GROUP SUWARNO","WS GROUP TRI","WS SAWITAN 6","WS MELFIDA",
															//"WS BEP","WS BEP 2","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MKS 2","WS MKS 3","WS MMS","WS RBT",
															//"WS ANEKA","WS EST 32","WS MEKAR JAYA","WS MEKAR JAYA 2","WS MEKAR JAYA 3","PADAIDI","WS SEBAMBAN","WS Umum Simpang Telkom","WS JOAIRBRUSH","RM UMUM 1",
															//"POOL Bagong Office",

															"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
															"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
															"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
															"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31.5","KM 32","KM 32.5","KM 33","KM 33.5","KM 34","KM 34.5","KM 35","KM 35.5","KM 36",

															"KM 15.5 B1","KM 16 B1","KM 16.5 B1","KM 17 B1","KM 17.5 B1",
															"KM 20.5 B2","KM 21 B2","KM 21.5 B2","KM 22 B2","KM 22.5 B2",
															"KM 21.5 B3","KM 22 B3","KM 22.5 B3","KM 23 B3","KM 23.5 B3","KM 24 B3","KM 24.5 B3","KM 25 B3","KM 25.5 B3","KM 26 B3","KM 26.5 B3","KM 27 B3",
															"KM 24 A1","KM 24.5 A1","KM 25 A1","KM 25.5 A1","KM 26 A1","KM 26.5 A1",

															"TIA KM 0","TIA KM 0.5","TIA KM 1","TIA KM 1.5","TIA KM 2","TIA KM 2.5","TIA KM 3","TIA KM 3.5","TIA KM 4","TIA KM 4.5","TIA KM 5",
															"TIA KM 5.5","TIA KM 6","TIA KM 6.5","TIA KM 7","TIA KM 7.5","TIA KM 8","TIA KM 8.5","TIA KM 9","TIA KM 9.5","TIA KM 10","TIA KM 10.5",
															"TIA KM 11","TIA KM 11.5","TIA KM 12","TIA KM 12.5","TIA KM 13","TIA KM 13.5","TIA KM 14","TIA KM 14.5","TIA KM 15",
															"TIA KM 15.5","TIA KM 16","TIA KM 16.5","TIA KM 17","TIA KM 17.5","TIA KM 18","TIA KM 18.5","TIA KM 19","TIA KM 19.5","TIA KM 20",
															"TIA KM 20.5","TIA KM 21","TIA KM 21.5","TIA KM 22","TIA KM 22.5","TIA KM 23","TIA KM 23.5","TIA KM 24","TIA KM 24.5","TIA KM 25",
															"TIA KM 25.5","TIA KM 26","TIA KM 26.5","TIA KM 27","TIA KM 27.5","TIA KM 28","TIA KM 28.5","TIA KM 29","TIA KM 29.5","TIA KM 30",
															"Jalur TIA Utara","Jalur TIA Selatan",

															"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7","BIB CP 8",
															"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
															"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
															"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
															"PORT BIB - Antrian","Port BIB - Antrian"

															);

	$config['pool_register_autocheck'] = array(

															"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI","POOL KUSAN BAWAH",
															"POOL KUSAN","Jalur PCN",
															"WS USAHA BARU","WS GROUP JULIUS","WS SEBAMBAN MOTOR","WS GROUP SUWARNO","WS GROUP TRI","WS SAWITAN 6","WS MELFIDA",
															"WS BEP","WS BEP 2","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MKS 2","WS MKS 3","WS MMS","WS RBT",
															"WS ANEKA","WS EST 32","WS MEKAR JAYA","WS MEKAR JAYA 2","WS MEKAR JAYA 3","PADAIDI","WS SEBAMBAN","WS Umum Simpang Telkom","WS JOAIRBRUSH","RM UMUM 1",
															"POOL Bagong Office"



															);

	// only street & ROM BIB
	$config['street_rombib_autocheck'] = array(				"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","ROM EST 2","PIT CK","PIT PASOPATI","ROM PASOPATI","ROM GH",
															"ROM B1 ROAD","ROM B2 ROAD","ROM B3 ROAD","EST ROAD","ROM 06 ROAD",


															"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
															"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
															"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
															"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31.5","KM 32","KM 32.5","KM 33","KM 33.5","KM 34","KM 34.5","KM 35","KM 35.5","KM 36",

															"KM 15.5 B1","KM 16 B1","KM 16.5 B1","KM 17 B1","KM 17.5 B1",
															"KM 20.5 B2","KM 21 B2","KM 21.5 B2","KM 22 B2","KM 22.5 B2",
															"KM 21.5 B3","KM 22 B3","KM 22.5 B3","KM 23 B3","KM 23.5 B3","KM 24 B3","KM 24.5 B3","KM 25 B3","KM 25.5 B3","KM 26 B3","KM 26.5 B3","KM 27 B3",
															"KM 24 A1","KM 24.5 A1","KM 25 A1","KM 25.5 A1","KM 26 A1","KM 26.5 A1",

															"TIA KM 0","TIA KM 0.5","TIA KM 1","TIA KM 1.5","TIA KM 2","TIA KM 2.5","TIA KM 3","TIA KM 3.5","TIA KM 4","TIA KM 4.5","TIA KM 5",
															"TIA KM 5.5","TIA KM 6","TIA KM 6.5","TIA KM 7","TIA KM 7.5","TIA KM 8","TIA KM 8.5","TIA KM 9","TIA KM 9.5","TIA KM 10","TIA KM 10.5",
															"TIA KM 11","TIA KM 11.5","TIA KM 12","TIA KM 12.5","TIA KM 13","TIA KM 13.5","TIA KM 14","TIA KM 14.5","TIA KM 15",
															"TIA KM 15.5","TIA KM 16","TIA KM 16.5","TIA KM 17","TIA KM 17.5","TIA KM 18","TIA KM 18.5","TIA KM 19","TIA KM 19.5","TIA KM 20",
															"TIA KM 20.5","TIA KM 21","TIA KM 21.5","TIA KM 22","TIA KM 22.5","TIA KM 23","TIA KM 23.5","TIA KM 24","TIA KM 24.5","TIA KM 25",
															"TIA KM 25.5","TIA KM 26","TIA KM 26.5","TIA KM 27","TIA KM 27.5","TIA KM 28","TIA KM 28.5","TIA KM 29","TIA KM 29.5","TIA KM 30",
															"Jalur TIA Utara","Jalur TIA Selatan",

															);

	$config['redzone_area_autocheck'] = array("STU area","PCN area");

	$config['warningzone_area_autocheck'] = array("WR 01","WR 02","WR 03","WR 04","WR 05","WR 06","WR 07");

	$config['cp_register_autocheck'] = array("BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7","BIB CP 8");


	$config['playbackremote_code'] = "-02022022";
	$config['GOOGLE_SIGNIN_CLIENT_ID'] = "413638888219-16r78o0dr78dsp2a31g3vapbm4tqf7s4.apps.googleusercontent.com";
	$config['GOOGLE_SIGNIN_CLIENT_SECRET'] = "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";
	$config['url_send_telegram'] = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
	//$config['url_send_telegram'] = "http://admin.abditrack.com/telegram/telegram_directpost";


	//NOTIFIKASI WA
	$config['WA_USERNAME_BIB']     = "temanindobara";
	$config['WA_PASSWORD_BIB']     = "SmmIndobaratmn68%";
	$config['WA_URL_GET_TOKEN']    = "https://interactive.jatismobile.com/wa/users/login";

	$config['WA_NAMESPACE'] = "56bfcbc9_e293_4702_8497_b768270ba792";
	$config['WA_URL_POST_MESSAGE'] = "https://interactive.jatismobile.com/v1/messages";
	$config['WA_TEMPLATE_VIOLATION'] = "dgto_temanindobara_violation";
	$config['WA_TEMPLATE_VIOLATION_OVS'] = "dgto_temanindobara_violation_ovs";
	$config['WA_TEMPLATE_1HRPORT'] = "dgto_temanindobara_1hrport";
	$config['WA_TEMPLATE_NONBIBACT'] = "dgto_temanindobara_nonbibact";
	$config['WA_TEMPLATE_NONBIBACT_DUMPING'] = "dgto_temanindobara_nonbibact_dumping";
	$config['WA_TEMPLATE_WARNINGZONE'] = "dgto_temanindobara_warningzone";
	$config['WA_TEMPLATE_REDZONE'] = "dgto_temanindobara_redzone";

	$config['WA_NOMOR_WARNING_ZONE'] = array("6282287747740");
	$config['WA_NOMOR_REDZONE_ZONE'] = array("6281617467868","6282287747740","6281393123577","6282254414549","6287708700195","6285779187617");
	$config['WA_NOMOR_NONBIB_ACTIVITY'] = array("6281617467868","6282287747740");
	$config['WA_NOMOR_USER_BIB'] = array("6281617467868","6282287747740","6281393123577", "6287708700195", "6282254414549");
