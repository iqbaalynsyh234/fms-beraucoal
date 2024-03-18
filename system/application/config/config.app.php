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
	$config['georeverse_host'] = "fmspoc.abditrack.com";

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

	//all street KM
	$config['street_register'] = array(
												"PMO KM 1","PMO KM 1.5","PMO KM 2","PMO KM 2.5","PMO KM 3","PMO KM 3.5","PMO KM 4","PMO KM 4.5","PMO KM 5","PMO KM 5.5","PMO KM 6","PMO KM 6.5",
												"PMO KM 7","PMO KM 7.5","PMO KM 8","PMO KM 8.5","PMO KM 9","PMO KM 9.5","PMO KM 10","PMO KM 10.5","PMO KM 11","PMO KM 11.5","PMO KM 12","PMO KM 12.5",
												"PMO KM 13","PMO KM 14",

												"BMO 1 KM 1","BMO 1 KM 1.5","BMO 1 KM 2","BMO 1 KM 2.5","BMO 1 KM 3","BMO 1 KM 3.5","BMO 1 KM 4","BMO 1 KM 4.5","BMO 1 KM 5","BMO 1 KM 5.5","BMO 1 KM 6","BMO 1 KM 6.5",
												"BMO 1 KM 7","BMO 1 KM 7.5","BMO 1 KM 8","BMO 1 KM 8.5","BMO 1 KM 9","BMO 1 KM 9.5","BMO 1 KM 10","BMO 1 KM 10.5","BMO 1 KM 11","BMO 1 KM 11.5","BMO 1 KM 12.5","BMO 1 KM 13.5",
												"BMO 1 KM 14","BMO 1 KM 14.5","BMO 1 KM 15","BMO 1 KM 15.5","BMO 1 KM 16","BMO 1 KM 16.5","BMO 1 KM 17","BMO 1 KM 17.5","BMO 1 KM 18","BMO 1 KM 18.5","BMO 1 KM 19","BMO 1 KM 19.5",
												"BMO 1 KM 20","BMO 1 KM 20.5","BMO 1 KM 21","BMO 1 KM 22.5","BMO 1 KM 23","BMO 1 KM 23.5","BMO 1 KM 24","BMO 1 KM 24.5","BMO 1 KM 25","BMO 1 KM 25.5","BMO 1 KM 26","BMO 1 KM 27.5",
												"BMO 1 KM 28","BMO 1 KM 28.5","BMO 1 KM 29","BMO 1 KM 29.5","BMO 1 KM 30",

												"SMO 1 KM 1","SMO 1 KM 1.5","SMO 1 KM 2","SMO 1 KM 2.5","SMO 1 KM 3","SMO 1 KM 3.5","SMO 1 KM 4","SMO 1 KM 4.5","SMO 1 KM 5","SMO 1 KM 5.5","SMO 1 KM 6","SMO 1 KM 6.5",
												"SMO 2 KM 1","SMO 2 KM 1.5","SMO 2 KM 2","SMO 2 KM 2.5","SMO 2 KM 3","SMO 2 KM 3.5","SMO 2 KM 4","SMO 2 KM 4.5","SMO 2 KM 5","SMO 2 KM 5.5","SMO 2 KM 6","SMO 2 KM 6.5",

												"LMO KM 1","LMO KM 1.5","LMO KM 2","LMO KM 2.5","LMO KM 3","LMO KM 3.5","LMO KM 4","LMO KM 4.5","LMO KM 5","LMO KM 5.5","LMO KM 6","LMO KM 6.5",
												"LMO KM 7","LMO KM 7.5","LMO KM 8",

												"LMO KM 8.5","LMO KM 9","LMO KM 9.5","LMO KM 10","LMO KM 10.5","LMO KM 11","LMO KM 11.5","LMO KM 12","LMO KM 12.5","LMO KM 13","LMO KM 13.5","LMO KM 14",
												"LMO KM 8.5B","LMO KM 9B","LMO KM 9.5B","LMO KM 10B","LMO KM 10.5B","LMO KM 11B","LMO KM 11.5B","LMO KM 12B","LMO KM 12.5B","LMO KM 13B","LMO KM 13.5B","LMO KM 14B"


										);


	$config['redzone_register'] = array("");

	// all street
	$config['street_register_all'] = array(
												"PMO KM 1","PMO KM 1.5","PMO KM 2","PMO KM 2.5","PMO KM 3","PMO KM 3.5","PMO KM 4","PMO KM 4.5","PMO KM 5","PMO KM 5.5","PMO KM 6","PMO KM 6.5",
												"PMO KM 7","PMO KM 7.5","PMO KM 8","PMO KM 8.5","PMO KM 9","PMO KM 9.5","PMO KM 10","PMO KM 10.5","PMO KM 11","PMO KM 11.5","PMO KM 12","PMO KM 12.5",
												"PMO KM 13","PMO KM 14",

												"BMO 1 KM 1","BMO 1 KM 1.5","BMO 1 KM 2","BMO 1 KM 2.5","BMO 1 KM 3","BMO 1 KM 3.5","BMO 1 KM 4","BMO 1 KM 4.5","BMO 1 KM 5","BMO 1 KM 5.5","BMO 1 KM 6","BMO 1 KM 6.5",
												"BMO 1 KM 7","BMO 1 KM 7.5","BMO 1 KM 8","BMO 1 KM 8.5","BMO 1 KM 9","BMO 1 KM 9.5","BMO 1 KM 10","BMO 1 KM 10.5","BMO 1 KM 11","BMO 1 KM 11.5","BMO 1 KM 12.5","BMO 1 KM 13.5",
												"BMO 1 KM 14","BMO 1 KM 14.5","BMO 1 KM 15","BMO 1 KM 15.5","BMO 1 KM 16","BMO 1 KM 16.5","BMO 1 KM 17","BMO 1 KM 17.5","BMO 1 KM 18","BMO 1 KM 18.5","BMO 1 KM 19","BMO 1 KM 19.5",
												"BMO 1 KM 20","BMO 1 KM 20.5","BMO 1 KM 21","BMO 1 KM 22.5","BMO 1 KM 23","BMO 1 KM 23.5","BMO 1 KM 24","BMO 1 KM 24.5","BMO 1 KM 25","BMO 1 KM 25.5","BMO 1 KM 26","BMO 1 KM 27.5",
												"BMO 1 KM 28","BMO 1 KM 28.5","BMO 1 KM 29","BMO 1 KM 29.5","BMO 1 KM 30",

												"SMO 1 KM 1","SMO 1 KM 1.5","SMO 1 KM 2","SMO 1 KM 2.5","SMO 1 KM 3","SMO 1 KM 3.5","SMO 1 KM 4","SMO 1 KM 4.5","SMO 1 KM 5","SMO 1 KM 5.5","SMO 1 KM 6","SMO 1 KM 6.5",
												"SMO 2 KM 1","SMO 2 KM 1.5","SMO 2 KM 2","SMO 2 KM 2.5","SMO 2 KM 3","SMO 2 KM 3.5","SMO 2 KM 4","SMO 2 KM 4.5","SMO 2 KM 5","SMO 2 KM 5.5","SMO 2 KM 6","SMO 2 KM 6.5",

												"LMO KM 1","LMO KM 1.5","LMO KM 2","LMO KM 2.5","LMO KM 3","LMO KM 3.5","LMO KM 4","LMO KM 4.5","LMO KM 5","LMO KM 5.5","LMO KM 6","LMO KM 6.5",
												"LMO KM 7","LMO KM 7.5","LMO KM 8",

												"LMO KM 8.5","LMO KM 9","LMO KM 9.5","LMO KM 10","LMO KM 10.5","LMO KM 11","LMO KM 11.5","LMO KM 12","LMO KM 12.5","LMO KM 13","LMO KM 13.5","LMO KM 14",
												"LMO KM 8.5B","LMO KM 9B","LMO KM 9.5B","LMO KM 10B","LMO KM 10.5B","LMO KM 11B","LMO KM 11.5B","LMO KM 12B","LMO KM 12.5B","LMO KM 13B","LMO KM 13.5B","LMO KM 14B",

												"Beaching Point B1","Beaching Point B2","Beaching Point B3","Beaching Point P2","Beaching Point P3","Beaching Point P1",
												"ROM K1-2","ROM P3","ROM 1","ROM B1B",

												"Jembatan Timbang KM1","CR12","CR11","CR3 dan CR4",
												"R AGATHIS KM 6.5","KELAY RIVER",

												"WS FAD PMO","CHANGE SHIFT FAD PMO",

												"BMO 1 CPP KM 28","BMO 1 CPP KM 30","BMO 1 CPP KM 1",
												"SITE BMO 1 PMO PRAPATAN","SMO CPP PORT"

										);

	//street khusus autocheck
	$config['street_register_autocheck'] = array(
												"PMO KM 1","PMO KM 1.5","PMO KM 2","PMO KM 2.5","PMO KM 3","PMO KM 3.5","PMO KM 4","PMO KM 4.5","PMO KM 5","PMO KM 5.5","PMO KM 6","PMO KM 6.5",
												"PMO KM 7","PMO KM 7.5","PMO KM 8","PMO KM 8.5","PMO KM 9","PMO KM 9.5","PMO KM 10","PMO KM 10.5","PMO KM 11","PMO KM 11.5","PMO KM 12","PMO KM 12.5",
												"PMO KM 13","PMO KM 14",

												"BMO 1 KM 1","BMO 1 KM 1.5","BMO 1 KM 2","BMO 1 KM 2.5","BMO 1 KM 3","BMO 1 KM 3.5","BMO 1 KM 4","BMO 1 KM 4.5","BMO 1 KM 5","BMO 1 KM 5.5","BMO 1 KM 6","BMO 1 KM 6.5",
												"BMO 1 KM 7","BMO 1 KM 7.5","BMO 1 KM 8","BMO 1 KM 8.5","BMO 1 KM 9","BMO 1 KM 9.5","BMO 1 KM 10","BMO 1 KM 10.5","BMO 1 KM 11","BMO 1 KM 11.5","BMO 1 KM 12.5","BMO 1 KM 13.5",
												"BMO 1 KM 14","BMO 1 KM 14.5","BMO 1 KM 15","BMO 1 KM 15.5","BMO 1 KM 16","BMO 1 KM 16.5","BMO 1 KM 17","BMO 1 KM 17.5","BMO 1 KM 18","BMO 1 KM 18.5","BMO 1 KM 19","BMO 1 KM 19.5",
												"BMO 1 KM 20","BMO 1 KM 20.5","BMO 1 KM 21","BMO 1 KM 22.5","BMO 1 KM 23","BMO 1 KM 23.5","BMO 1 KM 24","BMO 1 KM 24.5","BMO 1 KM 25","BMO 1 KM 25.5","BMO 1 KM 26","BMO 1 KM 27.5",
												"BMO 1 KM 28","BMO 1 KM 28.5","BMO 1 KM 29","BMO 1 KM 29.5","BMO 1 KM 30",

												"SMO 1 KM 1","SMO 1 KM 1.5","SMO 1 KM 2","SMO 1 KM 2.5","SMO 1 KM 3","SMO 1 KM 3.5","SMO 1 KM 4","SMO 1 KM 4.5","SMO 1 KM 5","SMO 1 KM 5.5","SMO 1 KM 6","SMO 1 KM 6.5",
												"SMO 2 KM 1","SMO 2 KM 1.5","SMO 2 KM 2","SMO 2 KM 2.5","SMO 2 KM 3","SMO 2 KM 3.5","SMO 2 KM 4","SMO 2 KM 4.5","SMO 2 KM 5","SMO 2 KM 5.5","SMO 2 KM 6","SMO 2 KM 6.5",

												"LMO KM 1","LMO KM 1.5","LMO KM 2","LMO KM 2.5","LMO KM 3","LMO KM 3.5","LMO KM 4","LMO KM 4.5","LMO KM 5","LMO KM 5.5","LMO KM 6","LMO KM 6.5",
												"LMO KM 7","LMO KM 7.5","LMO KM 8",

												"LMO KM 8.5","LMO KM 9","LMO KM 9.5","LMO KM 10","LMO KM 10.5","LMO KM 11","LMO KM 11.5","LMO KM 12","LMO KM 12.5","LMO KM 13","LMO KM 13.5","LMO KM 14",
												"LMO KM 8.5B","LMO KM 9B","LMO KM 9.5B","LMO KM 10B","LMO KM 10.5B","LMO KM 11B","LMO KM 11.5B","LMO KM 12B","LMO KM 12.5B","LMO KM 13B","LMO KM 13.5B","LMO KM 14B",

												"Beaching Point B1","Beaching Point B2","Beaching Point B3","Beaching Point P2","Beaching Point P3","Beaching Point P1",

												"Jembatan Timbang KM1","CR12","CR11","CR3 dan CR4",
												"R AGATHIS KM 6.5","KELAY RIVER"

												);

	//port register
	$config['port_register_autocheck'] = array(
												"Jembatan Timbang KM1","CR12","CR11","CR3 dan CR4",
												"BMO 1 CPP KM 28","BMO 1 CPP KM 30","BMO 1 CPP KM 1",
												"SMO CPP PORT"
											  );


	$config['rom_register_autocheck'] = array(
												"ROM K1-2","ROM P3","ROM 1"
											 );
	$config['rombib_register_autocheck']  = array(
													"ROM K1-2","ROM P3","ROM 1"
												 );

	$config['wim_register_autocheck'] = array("");
	$config['km_atas_register_autocheck'] = array("");
	$config['grayarea_register_autocheck'] = array("");
	$config['nonbib_register_autocheck'] = array("");
	$config['bayah_muatan_register_autocheck'] = array("");
	$config['bayah_kosongan_register_autocheck'] = array("");

	//hanya opr hauling ROM, HAULING, PORT
	$config['street_onduty_autocheck'] = array(
												"PMO KM 1","PMO KM 1.5","PMO KM 2","PMO KM 2.5","PMO KM 3","PMO KM 3.5","PMO KM 4","PMO KM 4.5","PMO KM 5","PMO KM 5.5","PMO KM 6","PMO KM 6.5",
												"PMO KM 7","PMO KM 7.5","PMO KM 8","PMO KM 8.5","PMO KM 9","PMO KM 9.5","PMO KM 10","PMO KM 10.5","PMO KM 11","PMO KM 11.5","PMO KM 12","PMO KM 12.5",
												"PMO KM 13","PMO KM 14",

												"BMO 1 KM 1","BMO 1 KM 1.5","BMO 1 KM 2","BMO 1 KM 2.5","BMO 1 KM 3","BMO 1 KM 3.5","BMO 1 KM 4","BMO 1 KM 4.5","BMO 1 KM 5","BMO 1 KM 5.5","BMO 1 KM 6","BMO 1 KM 6.5",
												"BMO 1 KM 7","BMO 1 KM 7.5","BMO 1 KM 8","BMO 1 KM 8.5","BMO 1 KM 9","BMO 1 KM 9.5","BMO 1 KM 10","BMO 1 KM 10.5","BMO 1 KM 11","BMO 1 KM 11.5","BMO 1 KM 12.5","BMO 1 KM 13.5",
												"BMO 1 KM 14","BMO 1 KM 14.5","BMO 1 KM 15","BMO 1 KM 15.5","BMO 1 KM 16","BMO 1 KM 16.5","BMO 1 KM 17","BMO 1 KM 17.5","BMO 1 KM 18","BMO 1 KM 18.5","BMO 1 KM 19","BMO 1 KM 19.5",
												"BMO 1 KM 20","BMO 1 KM 20.5","BMO 1 KM 21","BMO 1 KM 22.5","BMO 1 KM 23","BMO 1 KM 23.5","BMO 1 KM 24","BMO 1 KM 24.5","BMO 1 KM 25","BMO 1 KM 25.5","BMO 1 KM 26","BMO 1 KM 27.5",
												"BMO 1 KM 28","BMO 1 KM 28.5","BMO 1 KM 29","BMO 1 KM 29.5","BMO 1 KM 30",

												"SMO 1 KM 1","SMO 1 KM 1.5","SMO 1 KM 2","SMO 1 KM 2.5","SMO 1 KM 3","SMO 1 KM 3.5","SMO 1 KM 4","SMO 1 KM 4.5","SMO 1 KM 5","SMO 1 KM 5.5","SMO 1 KM 6","SMO 1 KM 6.5",
												"SMO 2 KM 1","SMO 2 KM 1.5","SMO 2 KM 2","SMO 2 KM 2.5","SMO 2 KM 3","SMO 2 KM 3.5","SMO 2 KM 4","SMO 2 KM 4.5","SMO 2 KM 5","SMO 2 KM 5.5","SMO 2 KM 6","SMO 2 KM 6.5",

												"LMO KM 1","LMO KM 1.5","LMO KM 2","LMO KM 2.5","LMO KM 3","LMO KM 3.5","LMO KM 4","LMO KM 4.5","LMO KM 5","LMO KM 5.5","LMO KM 6","LMO KM 6.5",
												"LMO KM 7","LMO KM 7.5","LMO KM 8",

												"LMO KM 8.5","LMO KM 9","LMO KM 9.5","LMO KM 10","LMO KM 10.5","LMO KM 11","LMO KM 11.5","LMO KM 12","LMO KM 12.5","LMO KM 13","LMO KM 13.5","LMO KM 14",
												"LMO KM 8.5B","LMO KM 9B","LMO KM 9.5B","LMO KM 10B","LMO KM 10.5B","LMO KM 11B","LMO KM 11.5B","LMO KM 12B","LMO KM 12.5B","LMO KM 13B","LMO KM 13.5B","LMO KM 14B",

												"Beaching Point B1","Beaching Point B2","Beaching Point B3","Beaching Point P2","Beaching Point P3","Beaching Point P1",
												"ROM K1-2","ROM P3","ROM 1","ROM B1B",

												"Jembatan Timbang KM1","CR12","CR11","CR3 dan CR4",
												"R AGATHIS KM 6.5","KELAY RIVER",

												"BMO 1 CPP KM 28","BMO 1 CPP KM 30","BMO 1 CPP KM 1",
												"SMO CPP PORT"

												);
	//pool / WS
	$config['pool_register_autocheck'] = array("WS FAD PMO","CHANGE SHIFT FAD PMO","WS KBB");

	// street & ROM
	$config['street_rombib_autocheck'] = array(
											"PMO KM 1","PMO KM 1.5","PMO KM 2","PMO KM 2.5","PMO KM 3","PMO KM 3.5","PMO KM 4","PMO KM 4.5","PMO KM 5","PMO KM 5.5","PMO KM 6","PMO KM 6.5",
											"PMO KM 7","PMO KM 7.5","PMO KM 8","PMO KM 8.5","PMO KM 9","PMO KM 9.5","PMO KM 10","PMO KM 10.5","PMO KM 11","PMO KM 11.5","PMO KM 12","PMO KM 12.5",
											"PMO KM 13","PMO KM 14",

											"BMO 1 KM 1","BMO 1 KM 1.5","BMO 1 KM 2","BMO 1 KM 2.5","BMO 1 KM 3","BMO 1 KM 3.5","BMO 1 KM 4","BMO 1 KM 4.5","BMO 1 KM 5","BMO 1 KM 5.5","BMO 1 KM 6","BMO 1 KM 6.5",
											"BMO 1 KM 7","BMO 1 KM 7.5","BMO 1 KM 8","BMO 1 KM 8.5","BMO 1 KM 9","BMO 1 KM 9.5","BMO 1 KM 10","BMO 1 KM 10.5","BMO 1 KM 11","BMO 1 KM 11.5","BMO 1 KM 12.5","BMO 1 KM 13.5",
											"BMO 1 KM 14","BMO 1 KM 14.5","BMO 1 KM 15","BMO 1 KM 15.5","BMO 1 KM 16","BMO 1 KM 16.5","BMO 1 KM 17","BMO 1 KM 17.5","BMO 1 KM 18","BMO 1 KM 18.5","BMO 1 KM 19","BMO 1 KM 19.5",
											"BMO 1 KM 20","BMO 1 KM 20.5","BMO 1 KM 21","BMO 1 KM 22.5","BMO 1 KM 23","BMO 1 KM 23.5","BMO 1 KM 24","BMO 1 KM 24.5","BMO 1 KM 25","BMO 1 KM 25.5","BMO 1 KM 26","BMO 1 KM 27.5",
											"BMO 1 KM 28","BMO 1 KM 28.5","BMO 1 KM 29","BMO 1 KM 29.5","BMO 1 KM 30",

											"SMO 1 KM 1","SMO 1 KM 1.5","SMO 1 KM 2","SMO 1 KM 2.5","SMO 1 KM 3","SMO 1 KM 3.5","SMO 1 KM 4","SMO 1 KM 4.5","SMO 1 KM 5","SMO 1 KM 5.5","SMO 1 KM 6","SMO 1 KM 6.5",
											"SMO 2 KM 1","SMO 2 KM 1.5","SMO 2 KM 2","SMO 2 KM 2.5","SMO 2 KM 3","SMO 2 KM 3.5","SMO 2 KM 4","SMO 2 KM 4.5","SMO 2 KM 5","SMO 2 KM 5.5","SMO 2 KM 6","SMO 2 KM 6.5",

											"LMO KM 1","LMO KM 1.5","LMO KM 2","LMO KM 2.5","LMO KM 3","LMO KM 3.5","LMO KM 4","LMO KM 4.5","LMO KM 5","LMO KM 5.5","LMO KM 6","LMO KM 6.5",
											"LMO KM 7","LMO KM 7.5","LMO KM 8",

											"LMO KM 8.5","LMO KM 9","LMO KM 9.5","LMO KM 10","LMO KM 10.5","LMO KM 11","LMO KM 11.5","LMO KM 12","LMO KM 12.5","LMO KM 13","LMO KM 13.5","LMO KM 14",
											"LMO KM 8.5B","LMO KM 9B","LMO KM 9.5B","LMO KM 10B","LMO KM 10.5B","LMO KM 11B","LMO KM 11.5B","LMO KM 12B","LMO KM 12.5B","LMO KM 13B","LMO KM 13.5B","LMO KM 14B",

											"Beaching Point B1","Beaching Point B2","Beaching Point B3","Beaching Point P2","Beaching Point P3","Beaching Point P1",
											"ROM K1-2","ROM P3","ROM 1","ROM B1B",

											"Jembatan Timbang KM1","CR12","CR11","CR3 dan CR4",
											"R AGATHIS KM 6.5","KELAY RIVER"

											);
	// street only
	$config['street_only_autocheck'] = array(
											"PMO KM 1","PMO KM 1.5","PMO KM 2","PMO KM 2.5","PMO KM 3","PMO KM 3.5","PMO KM 4","PMO KM 4.5","PMO KM 5","PMO KM 5.5","PMO KM 6","PMO KM 6.5",
											"PMO KM 7","PMO KM 7.5","PMO KM 8","PMO KM 8.5","PMO KM 9","PMO KM 9.5","PMO KM 10","PMO KM 10.5","PMO KM 11","PMO KM 11.5","PMO KM 12","PMO KM 12.5",
											"PMO KM 13","PMO KM 14",

											"BMO 1 KM 1","BMO 1 KM 1.5","BMO 1 KM 2","BMO 1 KM 2.5","BMO 1 KM 3","BMO 1 KM 3.5","BMO 1 KM 4","BMO 1 KM 4.5","BMO 1 KM 5","BMO 1 KM 5.5","BMO 1 KM 6","BMO 1 KM 6.5",
											"BMO 1 KM 7","BMO 1 KM 7.5","BMO 1 KM 8","BMO 1 KM 8.5","BMO 1 KM 9","BMO 1 KM 9.5","BMO 1 KM 10","BMO 1 KM 10.5","BMO 1 KM 11","BMO 1 KM 11.5","BMO 1 KM 12.5","BMO 1 KM 13.5",
											"BMO 1 KM 14","BMO 1 KM 14.5","BMO 1 KM 15","BMO 1 KM 15.5","BMO 1 KM 16","BMO 1 KM 16.5","BMO 1 KM 17","BMO 1 KM 17.5","BMO 1 KM 18","BMO 1 KM 18.5","BMO 1 KM 19","BMO 1 KM 19.5",
											"BMO 1 KM 20","BMO 1 KM 20.5","BMO 1 KM 21","BMO 1 KM 22.5","BMO 1 KM 23","BMO 1 KM 23.5","BMO 1 KM 24","BMO 1 KM 24.5","BMO 1 KM 25","BMO 1 KM 25.5","BMO 1 KM 26","BMO 1 KM 27.5",
											"BMO 1 KM 28","BMO 1 KM 28.5","BMO 1 KM 29","BMO 1 KM 29.5","BMO 1 KM 30",

											"SMO 1 KM 1","SMO 1 KM 1.5","SMO 1 KM 2","SMO 1 KM 2.5","SMO 1 KM 3","SMO 1 KM 3.5","SMO 1 KM 4","SMO 1 KM 4.5","SMO 1 KM 5","SMO 1 KM 5.5","SMO 1 KM 6","SMO 1 KM 6.5",
											"SMO 2 KM 1","SMO 2 KM 1.5","SMO 2 KM 2","SMO 2 KM 2.5","SMO 2 KM 3","SMO 2 KM 3.5","SMO 2 KM 4","SMO 2 KM 4.5","SMO 2 KM 5","SMO 2 KM 5.5","SMO 2 KM 6","SMO 2 KM 6.5",

											"LMO KM 1","LMO KM 1.5","LMO KM 2","LMO KM 2.5","LMO KM 3","LMO KM 3.5","LMO KM 4","LMO KM 4.5","LMO KM 5","LMO KM 5.5","LMO KM 6","LMO KM 6.5",
											"LMO KM 7","LMO KM 7.5","LMO KM 8",

											"LMO KM 8.5","LMO KM 9","LMO KM 9.5","LMO KM 10","LMO KM 10.5","LMO KM 11","LMO KM 11.5","LMO KM 12","LMO KM 12.5","LMO KM 13","LMO KM 13.5","LMO KM 14",
											"LMO KM 8.5B","LMO KM 9B","LMO KM 9.5B","LMO KM 10B","LMO KM 10.5B","LMO KM 11B","LMO KM 11.5B","LMO KM 12B","LMO KM 12.5B","LMO KM 13B","LMO KM 13.5B","LMO KM 14B",

											"Beaching Point B1","Beaching Point B2","Beaching Point B3","Beaching Point P2","Beaching Point P3","Beaching Point P1",

											"Jembatan Timbang KM1","CR12","CR11","CR3 dan CR4",
											"R AGATHIS KM 6.5","KELAY RIVER"

											);

	$config['redzone_area_autocheck'] = array("");
	$config['redzone_sawitan_autocheck'] = array("");
	$config['warningzone_area_autocheck'] = array("");
	$config['km_bawah_register_autocheck'] = array("");
	$config['km_atas_register_autocheck'] = array("");

	$config['cp_register_autocheck'] = array(
											  "Jembatan Timbang KM1","CR12","CR11","CR3 dan CR4",
											  "BMO 1 CPP KM 28","BMO 1 CPP KM 30","BMO 1 CPP KM 1",
											  "SMO CPP PORT"
											);

	$config['beaching_register_autocheck'] = array(
													"Beaching Point B1","Beaching Point B2","Beaching Point B3","Beaching Point P2","Beaching Point P3","Beaching Point P1",
													"KELAY RIVER"
											);

	$config['DT_company'] = array("1959","1948","1947","1946","1945","1926","1839","1837","1835","1834");

	$config['submit_hazard_register_1'] = array("604", "618"); //lvl 1 car distance, fatigue, //array("622", "604", "618", "620", "626"); // lvl 1 FATIGUE smoking, car distance, fatigue, call, driver abnormal
	$config['submit_hazard_register_2'] = array("605", "619"); //lvl 2 car distance, fatigue, //array("623", "605", "619", "621", "627"); // lvl 2 FATIGUE smoking, car distance, fatigue, call, driver abnormal

	$config['playbackremote_code'] = "-02022022";
	$config['GOOGLE_SIGNIN_CLIENT_ID'] = "413638888219-16r78o0dr78dsp2a31g3vapbm4tqf7s4.apps.googleusercontent.com";
	$config['GOOGLE_SIGNIN_CLIENT_SECRET'] = "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";
	//$config['url_send_telegram'] = "http://admin.abditrack.com/telegram/telegram_directpost";
	//$config['url_send_telegram'] = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
	$config['url_send_telegram'] = "http://admintib.abditrack.com/telegram/telegram_directpost";
	//$config['url_send_telegram'] = "http://fmspoc.abditrack.com/telegram/telegram_directpost";

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
	$config['WA_TEMPLATE_FRAUD_NOLINK'] = "dgto_temanindobara_fraud_nolink";
	$config['WA_TEMPLATE_FRAUD_LINK'] = "dgto_temanindobara_fraud_link";

	$config['WA_NOMOR_WARNING_ZONE'] = array("6282287747740");
	$config['WA_NOMOR_REDZONE_ZONE'] = array("6281617467868","6282287747740","6281393123577","6282254414549","6287708700195","6285779187617");
	$config['WA_NOMOR_NONBIB_ACTIVITY'] = array("6282287747740");
	$config['WA_NOMOR_USER_BIB'] = array("6282287747740","6287708700195", "6282254414549", "6281312596913");
	$config['WA_NOMOR_FRAUD'] = array("6281617467868");
	$config['ADAS_TYPE_ALERT'] = array("Forward Collision Warning", "Following Distance Monitoring", "Pedestrian Detection");
	$config['BASE64_DEFAULT'] = "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";
