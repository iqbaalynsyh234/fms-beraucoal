<?php
include "base.php";

class Overspeedalert extends Base {
	function __construct()
	{
		parent::__construct();

		$this->load->model("gpsmodel");
		$this->load->model("configmodel");
		$this->load->model("m_securityevidence");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		date_default_timezone_set("Asia/Jakarta");
	}

	//by gps alert
	function overspeed_telegram($userid="",$dblive=""){
		ini_set('memory_limit', '3G');
		date_default_timezone_set("Asia/Jakarta");
		printf("==GET NEW OVERSPEED >> START \r\n");
		$nowtime = date("Y-m-d H:i:s");
		$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
		$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-5minutes")); //default = 4 min

		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id, user_dblive");
		$this->db->order_by("user_id","asc");
		$this->db->where("user_id", $userid);
		$q = $this->db->get("user");
		$row = $q->row();
		$total_row = count($row);

		$startdate = $last_fiveminutes;
		$enddate = $nowtime_wita;


		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

		//print_r($sdate." ".$edate);exit();
		$wa_token = $this->getWAToken($userid);
		printf("===GET TOKEN WA : %s \n", $wa_token->sess_value);
		printf("===Periode Alarm %s to %s \r\n", $sdate, $edate);

		$this->dbalert = $this->load->database($dblive, TRUE);
		//$this->dbalert->group_by("gps_time");
		$this->dbalert->distinct("gps_time");
		$this->dbalert->order_by("gps_time","asc");
		$this->dbalert->where("gps_time >=", $sdate);
		$this->dbalert->where("gps_speed >=", 11.3);  // >= 21 kph
		$this->dbalert->where("gps_speed <=", 40);  // <= 65 kph (35) -> update Ke <= 74 kph (40)
		$this->dbalert->where("gps_alert", "Speeding Alarm");
		$this->dbalert->where("gps_notif", 0); //belum ke send
		$this->dbalert->where("gps_status", "A"); // status valid
		//$this->dbalert->limit(5); //limit
		$q = $this->dbalert->get("gps_alert");
		$rows = $q->result();
		$total_alert = count($rows);
		//print_r($total_alert);exit();
		if($total_alert >0){
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				$title_name = "OVERSPEED ALARM";
				$vehicle_device = $rows[$i]->gps_name."@".$rows[$i]->gps_host;
				$data_vehicle = $this->getvehicle($vehicle_device);
				$vehicle_id = $data_vehicle->vehicle_id;
				$vehicle_no = $data_vehicle->vehicle_no;
				$vehicle_name = $data_vehicle->vehicle_name;
				$vehicle_company = $data_vehicle->vehicle_company;
				//$vehicle_dblive = $data_vehicle->vehicle_dbname_live;
				$vehicle_dblive = "webtracking_gps_temanindobara_live";

				$telegram_group_dt = $this->get_telegramgroup_overspeed($data_vehicle->vehicle_company);

				if(count($telegram_group_dt)>0){
					$telegram_group = $telegram_group_dt->company_telegram_speed;
				}else{
					$telegram_group = "-495868829"; //testing anything
				}


				//matikan sementara driver , tunggu cam pasang
				/* $driver = $this->getdriver($vehicle_id);
				if($driver == false){
					$driver_name = "-";
				}else{
					$driver_ex = explode("-",$driver);
					$driver_name = $driver_ex[1];
				} */

				$driver_name = "-";


				printf("===Process Alarm ID %s %s %s  SPDdata: %s (%d/%d)  \r\n", $rows[$i]->gps_id, $data_vehicle->vehicle_no, $data_vehicle->vehicle_device, $rows[$i]->gps_speed, $j, $total_alert);
				$skip_sent = 0;
				$position = $this->getPosition_other($rows[$i]->gps_longitude_real,$rows[$i]->gps_latitude_real);
					if(isset($position)){
						$ex_position = explode(",",$position->display_name);
						if(count($ex_position)>0){
							$position_name = $ex_position[0];
						}else{
							$position_name = $ex_position[0];
						}
					}else{
						$position_name = $position->display_name;
							$skip_sent = 1;
					}

						$street_onduty = $this->config->item('street_onduty_autocheck');


						if (in_array($position_name, $street_onduty)){
							$skip_sent = 0;
						}else{
							$skip_sent = 1;
						}




				$gps_time = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))); //sudah wita
				$coordinate = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
				$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
				$gpsspeed_kph = round($rows[$i]->gps_speed*1.852,0);
				$direction = $rows[$i]->gps_course;

				$cardinal_direction = $this->wind_cardinal($direction);
				$cardinal_dir_kosongan = array("W","WNW","NW","NNW","N","NNE","NE","ENE");
				$cardinal_dir_muatan = array("E","ESE","SE","SSE","S","SSW","SW","WSW");

				if (in_array($cardinal_direction, $cardinal_dir_kosongan))
				{
					$jalur_bycardinal = "kosongan";
				}
				else if(in_array($cardinal_direction, $cardinal_dir_muatan))
				{
					$jalur_bycardinal = "muatan";
				}
				else
				{
					$jalur_bycardinal = "";
				}
				printf("===Cardinal Direction : %s %s -> %s \r\n", $cardinal_direction, $direction, $jalur_bycardinal);

				//$jalur = $this->get_jalurname_new($direction);
				$jalur = $jalur_bycardinal;
				if($jalur == ""){
					//$jalur = $rows[$i]->gps_last_road_type;
					$skip_sent = 1;
				}

				$rowgeofence = $this->getGeofence_location_live($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $vehicle_dblive);

								if($rowgeofence == false){
									$geofence_id = 0;
									$geofence_name = "";
									$geofence_speed = 0;
									$geofence_speed_muatan = "";
									$geofence_type = "";
									$geofence_speed_limit = 0;


								}else{
									$geofence_id = $rowgeofence->geofence_id;
									$geofence_name = $rowgeofence->geofence_name;
									$geofence_speed = $rowgeofence->geofence_speed;
									$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
									$geofence_type = $rowgeofence->geofence_type;

									if($jalur == "muatan"){
										$geofence_speed_limit = $geofence_speed_muatan;
									}else if($jalur == "kosongan"){
										$geofence_speed_limit = $geofence_speed;
									}else{
										$geofence_speed_limit = 0;

									}
								}

						printf("===Position : %s Geofence : %s Jalur: %s \r\n", $position_name, $geofence_name, $jalur);

						$speed_status = 0;
						if($gpsspeed_kph <= $geofence_speed_limit){
							$skip_sent = 1;
						}

						if($geofence_speed_limit == 0){
							$skip_sent = 1;
						}

						$gpsspeed_kph = $gpsspeed_kph-3;
						$geofence_speed_limit = $geofence_speed_limit-3;

						$delta_speed = $gpsspeed_kph - $geofence_speed_limit;

						//$delta_speed = 7; //tes
						printf("=== GPS Speed %s kph x Delta %s kph \r\n", $gpsspeed_kph, $delta_speed);

						//toleransi sampai dengan 6kph
						if($delta_speed >= 6 && $delta_speed <= 30)
						{
							$dt_speed_level_imktt = $this->get_delta_speed($delta_speed);
							$dt_level_ex = explode("|",$dt_speed_level_imktt);

							$speed_level = $dt_level_ex[1];
							$speed_level_alias = $dt_level_ex[0];
							$speed_status = 1;
						}
						else if($delta_speed >= 31 && $delta_speed <= 999999)
						{
							$speed_level = 5;
							$speed_level_alias = "Level 5";
							$speed_status = 1;
						}
						else
						{
							$speed_level = 0;
							$speed_level_alias = "Level 0";
							printf("===Overspeed Delta < 6 : %s kph \r\n", $delta_speed);
							$speed_status = 0;
						}

						if($speed_level == 0){
							$skip_sent = 1;
						}

						printf("===Speed : %s Limit : %s Level : %s Lev , value: %s \r\n", $gpsspeed_kph, $geofence_speed_limit, $speed_level_alias, $speed_level);
						$message = urlencode(
							"".$title_name." ".$speed_level_alias." \n".
							"Time: ".$gps_time." \n".
							"Vehicle No: ".$vehicle_no." ".$vehicle_name." \n".
							"Driver: ".$driver_name." \n".
							"Position: ".$position_name." \n".
							"Coordinate: ".$url." \n".
							"Speed (kph): ".$gpsspeed_kph." \n".
							"Rambu (kph): ".$geofence_speed_limit." \n".
							"Geofence: ".$geofence_name." \n".
							"Jalur: ".$jalur." \n"

							);

							//printf("===Message : %s \r\n", $message);
							sleep(2);
							if($skip_sent == 0){

								//sent notif WA
								$notif_wa_ovspeed = $this->sendnotif_wa_ovspeed($wa_token,$title_name,$speed_level_alias,$gps_time,$vehicle_no,$vehicle_name,$driver_name,$position_name,
																				$url,$gpsspeed_kph,$geofence_speed_limit,$geofence_name,$jalur,$telegram_group_dt);

								$sendtelegram = $this->telegram_direct($telegram_group,$message);
								//$sendtelegram = $this->telegram_direct("-438080733",$message); //telegram FMS TESTING
								printf("===SENT TELEGRAM OK\r\n");
							}else{

								printf("X==SKIP SENT TELEGRAM\r\n");
							}

						//KHUSUS BIB VILATION
						if($speed_level == 4 || $speed_level == 5)
						{

							$message = urlencode(
							"".$title_name." ".$speed_level_alias." \n".
							"Time: ".$gps_time." \n".
							"Vehicle No: ".$vehicle_no." ".$vehicle_name." \n".
							"Driver: ".$driver_name." \n".
							"Position: ".$position_name." \n".
							"Coordinate: ".$url." \n".
							"Speed (kph): ".$gpsspeed_kph." \n".
							"Rambu (kph): ".$geofence_speed_limit." \n".
							"Geofence: ".$geofence_name." \n".
							"Jalur: ".$jalur." \n"

							);

							//printf("===Message : %s \r\n", $message);
							sleep(2);
							if($skip_sent == 0){
								//sent notif WA
								$notif_wa_ovspeed = $this->sendnotif_wa_ovspeed_bib($wa_token,$title_name,$speed_level_alias,$gps_time,$vehicle_no,$vehicle_name,$driver_name,$position_name,
																					$url,$gpsspeed_kph,$geofence_speed_limit,$geofence_name,$jalur,$telegram_group_dt);

								//$sendtelegram = $this->telegram_direct("-438080733",$message); //telegram FMS TESTING
								$sendtelegram = $this->telegram_direct("-542787721",$message); //telegram BIB VIOLATION
								printf("===SENT TELEGRAM BIB OK\r\n");


							}else{

								printf("X==SKIP SENT BIB TELEGRAM\r\n");
							}

						}



				//update notif status == 1

				unset($datanotif);
				$datanotif["gps_notif"] = 1;
				$this->dbalert->where("gps_name", $rows[$i]->gps_name);
				$this->dbalert->where("gps_time", $rows[$i]->gps_time);
				$this->dbalert->update("gps_alert",$datanotif);
				printf("===UPDATE NOTIF OKE \r\n ");



				if($skip_sent == 0){
					$this->insert_overspeed_hour($userid,$data_vehicle,$gpsspeed_kph,$rows[$i],$rowgeofence,$jalur,$speed_status,$speed_level,$speed_level_alias,$delta_speed,$position_name);
					printf("===INSERT OVSPD OK\r\n");
				}



				$j++;
			}

		}else{
			printf("NO DATA OVERSPEED \r\n");
		}

			$this->db->close();
			$this->db->cache_delete_all();
			$this->dbalert->close();
			$this->dbalert->cache_delete_all();

			$finishtime = date("Y-m-d H:i:s");

			//send telegram
			$title_name = "OVERSPEED NEW ALERT CHECK: ".$dblive;
				$message = urlencode(
						"".$title_name." \n".
						"Start Time: ".$nowtime." \n".
						"End Time: ".$finishtime." \n"
					);
			sleep(2);
			$sendtelegram = $this->telegram_direct("-671321211",$message); //autocheck hour
			printf("===SENT TELEGRAM OK\r\n");

			printf("===FINISH %s to %s \r\n", $nowtime, $finishtime);


	}
	
	
	//2022-12-25
	function overspeed_telegram_bk_no_wa($userid="",$dblive=""){
		ini_set('memory_limit', '3G');
		date_default_timezone_set("Asia/Jakarta");
		printf("==GET NEW OVERSPEED >> START \r\n");
		$nowtime = date("Y-m-d H:i:s");
		$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
		$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-3minutes"));

		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id, user_dblive");
		$this->db->order_by("user_id","asc");
		$this->db->where("user_id", $userid);
		$q = $this->db->get("user");
		$row = $q->row();
		$total_row = count($row);

		$startdate = $last_fiveminutes;
		$enddate = $nowtime_wita;


		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

		//print_r($sdate." ".$edate);exit();
		printf("===Periode Alarm %s to %s \r\n", $sdate, $edate);

		$this->dbalert = $this->load->database($dblive, TRUE);
		$this->dbalert->group_by("gps_time");
		$this->dbalert->order_by("gps_time","asc");
		$this->dbalert->where("gps_time >=", $sdate);
        //$this->dbalert->where("gps_time <=", $edate);
		$this->dbalert->where("gps_speed >=", 11.3);  // >= 21 kph
		//$this->dbalert->where("gps_speed_status", 1);
		$this->dbalert->where("gps_alert", "Speeding Alarm");
		$this->dbalert->where("gps_notif", 0); //belum ke send
		//$this->dbalert->limit(5); //limit
		$q = $this->dbalert->get("gps_alert");
		$rows = $q->result();
		$total_alert = count($rows);
		//print_r($total_alert);exit();
		if($total_alert >0){
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				$title_name = "OVERSPEED ALARM";
				$vehicle_device = $rows[$i]->gps_name."@".$rows[$i]->gps_host;
				$data_vehicle = $this->getvehicle($vehicle_device);
				$vehicle_id = $data_vehicle->vehicle_id;
				$vehicle_no = $data_vehicle->vehicle_no;
				$vehicle_name = $data_vehicle->vehicle_name;
				$vehicle_company = $data_vehicle->vehicle_company;
				//$vehicle_dblive = $data_vehicle->vehicle_dbname_live;
				$vehicle_dblive = "webtracking_gps_temanindobara_live";

			/* 	$telegram_group = $this->get_telegramgroup_overspeed($data_vehicle->vehicle_company);
				//$telegram_group = "-495868829"; //testing anything */

				$telegram_group_dt = $this->get_telegramgroup_overspeed($data_vehicle->vehicle_company);

				if(count($telegram_group_dt)>0){
					$telegram_group = $telegram_group_dt->company_telegram_speed;
				}else{
					$telegram_group = "-495868829"; //testing anything
				}




				//matikan sementara driver , tunggu cam pasang
				/* $driver = $this->getdriver($vehicle_id);
				if($driver == false){
					$driver_name = "-";
				}else{
					$driver_ex = explode("-",$driver);
					$driver_name = $driver_ex[1];
				} */

				$driver_name = "-";


				printf("===Process Alarm ID %s %s %s (%d/%d) \r\n", $rows[$i]->gps_id, $data_vehicle->vehicle_no, $data_vehicle->vehicle_device, $j, $total_alert);
				$skip_sent = 0;
				$position = $this->getPosition_other($rows[$i]->gps_longitude_real,$rows[$i]->gps_latitude_real);
					if(isset($position)){
						$ex_position = explode(",",$position->display_name);
						if(count($ex_position)>0){
							$position_name = $ex_position[0];
						}else{
							$position_name = $ex_position[0];
						}
					}else{
						$position_name = $position->display_name;
							$skip_sent = 1;
					}

						$street_onduty = $this->config->item('street_onduty_autocheck');


						if (in_array($position_name, $street_onduty)){
							$skip_sent = 0;
						}else{
							$skip_sent = 1;
						}




				$gps_time = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))); //sudah wita
				$coordinate = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
				$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
				$gpsspeed_kph = round($rows[$i]->gps_speed*1.852,0);
				$direction = $rows[$i]->gps_course;

				$cardinal_direction = $this->wind_cardinal($direction);
				$cardinal_dir_kosongan = array("W","WNW","NW","NNW","N","NNE","NE","ENE");
				$cardinal_dir_muatan = array("E","ESE","SE","SSE","S","SSW","SW","WSW");

				if (in_array($cardinal_direction, $cardinal_dir_kosongan))
				{
					$jalur_bycardinal = "kosongan";
				}
				else if(in_array($cardinal_direction, $cardinal_dir_muatan))
				{
					$jalur_bycardinal = "muatan";
				}
				else
				{
					$jalur_bycardinal = "";
				}
				printf("===Cardinal Direction : %s %s -> %s \r\n", $cardinal_direction, $direction, $jalur_bycardinal);

				//$jalur = $this->get_jalurname_new($direction);
				$jalur = $jalur_bycardinal;
				if($jalur == ""){
					//$jalur = $rows[$i]->gps_last_road_type;
					$skip_sent = 1;
				}

				$rowgeofence = $this->getGeofence_location_live($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $vehicle_dblive);

								if($rowgeofence == false){
									$geofence_id = 0;
									$geofence_name = "";
									$geofence_speed = 0;
									$geofence_speed_muatan = "";
									$geofence_type = "";
									$geofence_speed_limit = 0;


								}else{
									$geofence_id = $rowgeofence->geofence_id;
									$geofence_name = $rowgeofence->geofence_name;
									$geofence_speed = $rowgeofence->geofence_speed;
									$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
									$geofence_type = $rowgeofence->geofence_type;

									if($jalur == "muatan"){
										$geofence_speed_limit = $geofence_speed_muatan;
									}else if($jalur == "kosongan"){
										$geofence_speed_limit = $geofence_speed;
									}else{
										$geofence_speed_limit = 0;

									}
								}

						printf("===Position : %s Geofence : %s Jalur: %s \r\n", $position_name, $geofence_name, $jalur);

						$speed_status = 0;
						if($gpsspeed_kph <= $geofence_speed_limit){
							$skip_sent = 1;
						}

						if($geofence_speed_limit == 0){
							$skip_sent = 1;
						}

						$gpsspeed_kph = $gpsspeed_kph-3;
						$geofence_speed_limit = $geofence_speed_limit-3;

						$delta_speed = $gpsspeed_kph - $geofence_speed_limit;

						//$delta_speed = 7; //tes
						printf("=== Delta %s kph \r\n", $delta_speed);

						//toleransi sampai dengan 6kph
						if($delta_speed >= 6 && $delta_speed <= 30)
						{
							$dt_speed_level_imktt = $this->get_delta_speed($delta_speed);
							$dt_level_ex = explode("|",$dt_speed_level_imktt);

							$speed_level = $dt_level_ex[1];
							$speed_level_alias = $dt_level_ex[0];
							$speed_status = 1;
						}
						else if($delta_speed >= 31 && $delta_speed <= 999999)
						{
							$speed_level = 5;
							$speed_level_alias = "Level 5";
							$speed_status = 1;
						}
						else
						{
							$speed_level = 0;
							$speed_level_alias = "Level 0";
							printf("===Overspeed Delta < 6 : %s kph \r\n", $delta_speed);
							$speed_status = 0;
						}

						if($speed_level == 0){
							$skip_sent = 1;
						}

						printf("===Speed : %s Limit : %s Level : %s Lev , value: %s \r\n", $gpsspeed_kph, $geofence_speed_limit, $speed_level_alias, $speed_level);
						$message = urlencode(
							"".$title_name." ".$speed_level_alias." \n".
							"Time: ".$gps_time." \n".
							"Vehicle No: ".$vehicle_no." ".$vehicle_name." \n".
							"Driver: ".$driver_name." \n".
							"Position: ".$position_name." \n".
							"Coordinate: ".$url." \n".
							"Speed (kph): ".$gpsspeed_kph." \n".
							"Rambu (kph): ".$geofence_speed_limit." \n".
							"Goefence: ".$geofence_name." \n".
							"Jalur: ".$jalur." \n"

							);

							//printf("===Message : %s \r\n", $message);
							sleep(2);
							if($skip_sent == 0){
								$sendtelegram = $this->telegram_direct($telegram_group,$message);
								//$sendtelegram = $this->telegram_direct("-438080733",$message); //telegram FMS TESTING
								printf("===SENT TELEGRAM OK\r\n");
							}else{

								printf("X==SKIP SENT TELEGRAM\r\n");
							}

						//KHUSUS BIB VILATION
						if($speed_level == 3 || $speed_level == 4 || $speed_level == 5)
						{

							$message = urlencode(
							"".$title_name." ".$speed_level_alias." \n".
							"Time: ".$gps_time." \n".
							"Vehicle No: ".$vehicle_no." ".$vehicle_name." \n".
							"Driver: ".$driver_name." \n".
							"Position: ".$position_name." \n".
							"Coordinate: ".$url." \n".
							"Speed (kph): ".$gpsspeed_kph." \n".
							"Rambu (kph): ".$geofence_speed_limit." \n".
							"Goefence: ".$geofence_name." \n".
							"Jalur: ".$jalur." \n"

							);

							//printf("===Message : %s \r\n", $message);
							sleep(2);
							if($skip_sent == 0){
								//$sendtelegram = $this->telegram_direct("-438080733",$message); //telegram FMS TESTING
								$sendtelegram = $this->telegram_direct("-542787721",$message); //telegram BIB VIOLATION
								printf("===SENT TELEGRAM BIB OK\r\n");


							}else{

								printf("X==SKIP SENT BIB TELEGRAM\r\n");
							}

						}



				//update notif status == 1
				unset($datanotif);
				$datanotif["gps_notif"] = 1;
				$this->dbalert->where("gps_name", $rows[$i]->gps_name);
				$this->dbalert->where("gps_time", $rows[$i]->gps_time);
				$this->dbalert->update("gps_alert",$datanotif);
				printf("===UPDATE NOTIF OKE \r\n ");

				if($skip_sent == 0){
					$this->insert_overspeed_hour($userid,$data_vehicle,$gpsspeed_kph,$rows[$i],$rowgeofence,$jalur,$speed_status,$speed_level,$speed_level_alias,$delta_speed,$position_name);
					printf("===INSERT OVSPD OK\r\n");
				}



				$j++;
			}

		}else{
			printf("NO DATA OVERSPEED \r\n");
		}

			$this->db->close();
			$this->db->cache_delete_all();
			$this->dbalert->close();
			$this->dbalert->cache_delete_all();

			$finishtime = date("Y-m-d H:i:s");

			//send telegram
			$title_name = "OVERSPEED NEW ALERT CHECK: ".$dblive;
				$message = urlencode(
						"".$title_name." \n".
						"Start Time: ".$nowtime." \n".
						"End Time: ".$finishtime." \n"
					);
			sleep(2);
			$sendtelegram = $this->telegram_direct("-671321211",$message); //autocheck hour
			printf("===SENT TELEGRAM OK\r\n");



			printf("===FINISH %s to %s \r\n", $nowtime, $finishtime);


	}

	function get_delta_speed($value)
	{
		//get telegram group by company

		$this->dbts = $this->load->database("webtracking_ts",TRUE);
		$this->dbts->order_by("level_value","asc");
		$this->dbts->where("level_flag",0);
		$qspdlevel = $this->dbts->get("ts_speed_level");
		$rspdlevel = $qspdlevel->result();

		$dt_alias = "";
		$dt_level = "";

		$result = $dt_alias."|".$dt_level;

		if(count($rspdlevel)>0)
		{

			for ($u=0;$u<count($rspdlevel);$u++){

				$num_list = json_decode($rspdlevel[$u]->level_content);
				$num_list2 = json_decode(json_encode($num_list), true);
				//print_r($num_list2);
				if (in_array($value, $num_list2)){

					$dt_alias = $rspdlevel[$u]->level_name;
					$dt_level = $rspdlevel[$u]->level_value;
					$result = $dt_alias."|".$dt_level;

					break;
				}
			}

			return $result;

		}
		else
		{
			$dt_alias = "";
			$dt_level = "";

			return $result;
		}

		$this->dbts->close();
		$this->dbts->cache_delete_all();


	}

	function insert_overspeed_hour($userid,$data_vehicle,$gpsspeed_kph,$rows,$rowgeofence,$jalur,$speed_status,$speed_level,$speed_level_alias,$delta_speed,$position_name){

		$reporttype = 0;
		$arah = "";
		$gps_time_wib = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows->gps_time)));

		if($rows->gps_status == "A"){
			$gps_status = "OK";
		}else{
			$gps_status = "NOT OK";
		}



		$month = date("F", strtotime($gps_time_wib));
		$year = date("Y", strtotime($gps_time_wib));
		$report = "overspeed_hour_";

		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}

		$geofence_id = $rowgeofence->geofence_id;
		$geofence_name = $rowgeofence->geofence_name;
		$geofence_speed = $rowgeofence->geofence_speed;
		$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
		$geofence_type = $rowgeofence->geofence_type;

		if($jalur == "muatan"){
			$geofence_speed_limit = $geofence_speed_muatan;
		}else if($jalur == "kosongan"){
			$geofence_speed_limit = $geofence_speed;
		}else{
			$geofence_speed_limit = 0;

		}

		$overspeed_report_vehicle_user_id = $data_vehicle->vehicle_user_id;
		$overspeed_report_vehicle_id = $data_vehicle->vehicle_id;
		$overspeed_report_vehicle_device = $data_vehicle->vehicle_device;
		$overspeed_report_imei = $data_vehicle->vehicle_mv03;
		$overspeed_report_vehicle_no = $data_vehicle->vehicle_no;
		$overspeed_report_vehicle_name = $data_vehicle->vehicle_name;
		$overspeed_report_vehicle_type = $data_vehicle->vehicle_type;
		$overspeed_report_vehicle_company = $data_vehicle->vehicle_company;
		$overspeed_report_type = $reporttype;
		$overspeed_report_name = "overspeed";
		$overspeed_report_speed = $gpsspeed_kph;
		$overspeed_report_speed_status = $speed_status;
		$overspeed_report_gpsstatus = $gps_status;
		$overspeed_report_gps_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows->gps_time))); //sudah wita
		$overspeed_report_geofence_id = $geofence_id;
		$overspeed_report_geofence_name = $geofence_name;
		$overspeed_report_geofence_limit = $geofence_speed_limit;
		$overspeed_report_geofence_type = $geofence_type;
		$overspeed_report_jalur = $jalur;
		$overspeed_report_level = $speed_level;
		$overspeed_report_level_alias = $speed_level_alias;
		$overspeed_report_delta = $delta_speed;

		$overspeed_report_direction = $rows->gps_course;
		$overspeed_report_direction_status = $arah;
		$overspeed_report_duration = "";
		$overspeed_report_duration_sec = 0;
		$overspeed_report_location = $position_name;
		$overspeed_report_coordinate = $rows->gps_latitude_real.",".$rows->gps_longitude_real;


		unset($datainsert);
		$datainsert["overspeed_report_vehicle_user_id"] = $overspeed_report_vehicle_user_id;
		$datainsert["overspeed_report_vehicle_id"] = $overspeed_report_vehicle_id;
		$datainsert["overspeed_report_vehicle_device"] = $overspeed_report_vehicle_device;
		$datainsert["overspeed_report_imei"] = $overspeed_report_imei;
		$datainsert["overspeed_report_vehicle_no"] = $overspeed_report_vehicle_no;
		$datainsert["overspeed_report_vehicle_name"] = $overspeed_report_vehicle_name;
		$datainsert["overspeed_report_vehicle_type"] = $overspeed_report_vehicle_type;
		$datainsert["overspeed_report_vehicle_company"] = $overspeed_report_vehicle_company;
		$datainsert["overspeed_report_type"] = $overspeed_report_type;
		$datainsert["overspeed_report_name"] = $overspeed_report_name;
		$datainsert["overspeed_report_speed"] = $overspeed_report_speed;
		$datainsert["overspeed_report_speed_status"] = $overspeed_report_speed_status;
		$datainsert["overspeed_report_gpsstatus"] = $overspeed_report_gpsstatus;
		$datainsert["overspeed_report_gps_time"] = $overspeed_report_gps_time;
		$datainsert["overspeed_report_geofence_id"] = $overspeed_report_geofence_id;
		$datainsert["overspeed_report_geofence_name"] = $overspeed_report_geofence_name;
		$datainsert["overspeed_report_geofence_limit"] = $overspeed_report_geofence_limit;
		$datainsert["overspeed_report_geofence_type"] = $overspeed_report_geofence_type;
		$datainsert["overspeed_report_jalur"] = $overspeed_report_jalur;

		$datainsert["overspeed_report_level"] = $overspeed_report_level;
		$datainsert["overspeed_report_level_alias"] = $overspeed_report_level_alias;
		$datainsert["overspeed_report_delta"] = $overspeed_report_delta;

		$datainsert["overspeed_report_direction"] = $overspeed_report_direction;
		$datainsert["overspeed_report_direction_status"] = $overspeed_report_direction_status;
		$datainsert["overspeed_report_duration"] = $overspeed_report_duration;
		$datainsert["overspeed_report_duration_sec"] = $overspeed_report_duration_sec;
		$datainsert["overspeed_report_location"] = $overspeed_report_location;
		$datainsert["overspeed_report_coordinate"] = $overspeed_report_coordinate;


		$this->dbtrans = $this->load->database("tensor_report",true);
		$this->dbtrans->insert($dbtable,$datainsert);
		//exit();

	}

	function get_telegramgroup_overspeed($company_id)
	{
		//get telegram group by company
		$this->db = $this->load->database("default",TRUE);
		$this->db->select("company_id,company_telegram_speed,company_hp");
		$this->db->where("company_id",$company_id);
		$qcompany = $this->db->get("company");
		$rcompany = $qcompany->row();
		/* if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_speed;
		}else{
			$telegram_group = 0;
		} */

		$this->db->close();
		$this->db->cache_delete_all();

		return $rcompany;
	}

	function getGeofence_location_live($longitude, $latitude, $vehicle_dblive)
	{

		$this->db = $this->load->database($vehicle_dblive, true);
		$lng = $longitude;
		$lat = $latitude;
		$geo_name = "''";
		$sql = sprintf("
					SELECT 	geofence_name,geofence_id,geofence_speed,geofence_speed_muatan,geofence_type
					FROM 	webtracking_geofence
					WHERE 	TRUE
							AND (geofence_name <> %s)
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
							AND (geofence_status = 1)
					ORDER BY geofence_id DESC LIMIT 1 OFFSET 0", $geo_name, $lng, $lat);
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
			$row = $q->row();
            /*$total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
				$data = $row[$i]->geofence_name;
				$data = $row;
				return $data;
            }*/
			$data = $row;
			return $data;

		}
		else
        {
			$data = false;
            return $data;
        }

	}

	function overspeed_breakdown_hour($userid="",$orderby="",$typereport="",$startdate="",$enddate="")
	{
		$report = "overspeed_hour_";
		$report_location = "location_";
		$startdate = date("Y-m-d");
		$enddate = date("Y-m-d");
		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != ""){
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }

		if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($enddate != ""){
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
			$month = date("F", strtotime($enddate));
			$year = date("Y", strtotime($enddate));
        }

		//print_r($startdate." ".$enddate);exit();
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_location = $report_location."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_location = $report_location."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_location = $report_location."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_location = $report_location."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_location = $report_location."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_location = $report_location."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_location = $report_location."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_location = $report_location."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_location = $report_location."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_location = $report_location."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_location = $report_location."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_location = $report_location."desember_".$year;
			break;
		}

		printf("===STARTING REPORT %s %s \r\n", $startdate, $enddate);
		$this->db = $this->load->database("default",true);
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_device,vehicle_no");
		$this->db->where("vehicle_user_id", $userid);
		//$this->db->where("vehicle_id", 72152395);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		//print_r($rows);exit();

		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			//exit();
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$vehicleid = $rows[$i]->vehicle_id;
				$deviceid = $rows[$i]->vehicle_device;
				$vehicle_no = $rows[$i]->vehicle_no;
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $vehicle_no, $nourut, $total_rows);

				$update_event_data = $this->getOverspeedEvent_location($vehicleid,$startdate,$enddate,$typereport,$dbtable);
			}
		}else{
			printf("===========TIDAK ADA DATA VEHICLE======== \r\n");
		}

		printf("===========SELESAI======== \r\n");

	}

	function overspeed_breakdown($userid="",$orderby="",$typereport="",$startdate="",$enddate="")
	{
		$report = "overspeed_";
		$report_location = "location_";
		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != ""){
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }

		if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($enddate != ""){
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
			$month = date("F", strtotime($enddate));
			$year = date("Y", strtotime($enddate));
        }

		//print_r($startdate." ".$enddate);exit();
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_location = $report_location."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_location = $report_location."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_location = $report_location."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_location = $report_location."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_location = $report_location."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_location = $report_location."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_location = $report_location."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_location = $report_location."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_location = $report_location."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_location = $report_location."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_location = $report_location."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_location = $report_location."desember_".$year;
			break;
		}

		printf("===STARTING REPORT %s %s \r\n", $startdate, $enddate);
		$this->db = $this->load->database("default",true);
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_device,vehicle_no");
		$this->db->where("vehicle_user_id", $userid);
		//$this->db->where("vehicle_id", 72152395);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		//print_r($rows);exit();

		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			//exit();
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$vehicleid = $rows[$i]->vehicle_id;
				$deviceid = $rows[$i]->vehicle_device;
				$vehicle_no = $rows[$i]->vehicle_no;
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $vehicle_no, $nourut, $total_rows);

				$update_event_data = $this->getOverspeedEvent_location($vehicleid,$startdate,$enddate,$typereport,$dbtable);
			}
		}else{
			printf("===========TIDAK ADA DATA VEHICLE======== \r\n");
		}

		printf("===========SELESAI======== \r\n");

	}

	function getOverspeedEvent_location($vehicleid,$sdate,$edate,$reporttype,$dbtable){

		$this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->order_by("overspeed_report_gps_time","asc");
		//$this->dbreport->select("overspeed_report_id");
		$this->dbreport->where("overspeed_report_vehicle_id", $vehicleid);
		$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
		$this->dbreport->where("overspeed_report_gps_time <=", $edate);
		$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
		$this->dbreport->where("overspeed_report_geofence_type", "road"); //khusus dijalan
		$this->dbreport->where("overspeed_report_type", $reporttype); //data fix (default) = 0
		$q = $this->dbreport->get($dbtable);
        $rows = $q->result();
		$total = count($rows);
		if($total>0){

			$total_event_same_loc = 0;
			$delta = 0;
			$totaldelta = 0;
			for($x=0;$x<$total;$x++)
			{
				$norut = $x+1;
				printf("===DATA OVERSPEED ke %s of %s \r\n", $norut, $total);
				$id_report = $rows[$x]->overspeed_report_id;

				if($norut != $total){
					$jalurnext = $rows[$x+1]->overspeed_report_jalur;
					$jalurcurrent = $rows[$x]->overspeed_report_jalur;

					//grouping KM stgah
					$ex_locationnext = explode(".",$rows[$x+1]->overspeed_report_location);
					$ex_locationcurrent = explode(".",$rows[$x]->overspeed_report_location);

					/* $locationnext = $rows[$x+1]->overspeed_report_location;
					$locationcurrent = $rows[$x]->overspeed_report_location; */

					$locationnext = $ex_locationnext[0];
					$locationcurrent = $ex_locationcurrent[0];

					$timenext = strtotime($rows[$x+1]->overspeed_report_gps_time);
					$timecurrent = strtotime($rows[$x]->overspeed_report_gps_time);

					/* $currentposition = $jalurcurrent.",".$locationcurrent;
					$nextposition = $jalurnext.",".$locationnext; */

					$currentposition = $locationcurrent;
					$nextposition = $locationnext;

					$delta = $timenext - $timecurrent; //sec
					//$limit_sec = 60*60; // sec to menit

					//if(($currentposition == $nextposition) && ($delta < $limit_sec))
					if($currentposition == $nextposition)
					{
						$event_same_loc = "A";
						$total_event_same_loc = $total_event_same_loc + 1;
						$totaldelta = $totaldelta + $delta;
						printf("===LOKASI YG SAMA : %s || %s EVENT : %s \r\n",$currentposition, $nextposition, $event_same_loc);
						$status = 0;
						$status_end = 0;
					}
					else
					{
						//jika BEDA LOKASI, INSERT LOKASI BEFORE
						$event_same_loc = "B";
						printf("===BEDA LOKASI : %s || %s EVENT : %s \r\n",$currentposition, $nextposition, $event_same_loc);
						$status = 1;
						$status_end = 0;

					}

				}
				else
				{
					$event_same_loc = "B";
					printf("===END EVENT : %s \r\n",$event_same_loc);
					$status = 1;
					$status_end = 1;
				}

				//update
				if($status == 1)
				{

					unset($data);

					$data["overspeed_report_event_time"] = $totaldelta;
					if($status_end == 1){
						$data["overspeed_report_event_location"] = $nextposition;
					}else{
						$data["overspeed_report_event_location"] = $currentposition;
					}

					$data["overspeed_report_event_total"] = $total_event_same_loc+1;
					$data["overspeed_report_event_status"] = 1;
					$this->dbreport->where("overspeed_report_id", $id_report);
					//$this->dbreport->limit(1);
					$this->dbreport->update($dbtable,$data);
					printf("===UPDATE OK: %s \r\n",$currentposition);
					printf("============== \r\n");

					//clear
					$delta = 0;
					$totaldelta = 0;
					$total_event_same_loc = 0;
				}
				else
				{

					unset($data);

					$data["overspeed_report_event_time"] = "";
					$data["overspeed_report_event_location"] = "";
					$data["overspeed_report_event_total"] = "";
					$data["overspeed_report_event_status"] = 0;
					$this->dbreport->where("overspeed_report_id", $id_report);
					//$this->dbreport->limit(1);
					$this->dbreport->update($dbtable,$data);
					printf("===CLEAR OK: %s \r\n",$currentposition);
					printf("============== \r\n");

				}
			}
		}
		else
		{
			printf("===TIDAK ADA DATA OVERSPEED \r\n");
		}
	}

	function overspeed_board_all($userid="", $orderby="", $typereport="", $startdate="",$enddate=""){

		$this->overspeed_breakdown($userid,$orderby,$typereport,$startdate,$enddate);
		$this->overspeed_board_company($userid,$orderby,$typereport,$startdate,$enddate);
		$this->overspeed_board_hour($userid,$orderby,$typereport,$startdate,$enddate);
		$this->overspeed_board_level($userid,$orderby,$typereport,$startdate,$enddate);
		$this->overspeed_board_street($userid,$orderby,$typereport,$startdate,$enddate);
		$this->overspeed_board_vehicle($userid,$orderby,$typereport,$startdate,$enddate);
		$this->overspeed_board_sign($userid,$orderby,$typereport,$startdate,$enddate);

	}

	//overspeed by vehicle
	function overspeed_board_vehicle($userid="", $orderby="", $typereport="", $startdate = "", $enddate = ""){

		ini_set('memory_limit', '2G');
        printf("OVERSPEED BY VEHICLE >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";

        $report_type = "overspeed";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "overspeed_";
		$report_new = "overspeed_board_";
		$model = "vehicle";
		//$model_array = array("vehicle","jalur","street","geofence");

        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }

        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }

        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }

		if ($orderby == "") {
            $orderby = "asc";
        }

		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_new = $report_new."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_new = $report_new."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_new = $report_new."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_new = $report_new."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_new = $report_new."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_new = $report_new."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_new = $report_new."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_new = $report_new."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_new = $report_new."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_new = $report_new."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_new = $report_new."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_new = $report_new."desember_".$year;
			break;
		}

        $sdate = date("Y-m-d H:i:s", strtotime($startdate)); //wita
        $edate = date("Y-m-d H:i:s", strtotime($enddate));  //wita
        $z =0;
		printf("START DATE - END DATE : %s %s \r\n", $sdate, $edate);

		$this->db->order_by("vehicle_id", $orderby);
		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();

        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");


			for($x=0;$x<count($rowvehicle);$x++){
				printf("==PROCESS %s %s of %s \r\n",$rowvehicle[$x]->vehicle_no, $x+1, $total_process);
				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true);
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_vehicle_id", $rowvehicle[$x]->vehicle_id);
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_geofence_type", "road"); //khusus dijalan
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$this->dbreport->where("overspeed_report_event_status", 1); // only group_by 1
				$this->dbreport->where("overspeed_report_event_total !=", 0); // only group_by 1
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query

					unset($datainsert);
					$datainsert["overspeed_board_vehicle_user_id"] = $rowvehicle[$x]->vehicle_user_id;
					$datainsert["overspeed_board_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
					$datainsert["overspeed_board_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
					$datainsert["overspeed_board_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
					$datainsert["overspeed_board_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
					$datainsert["overspeed_board_vehicle_type"] = $rowvehicle[$x]->vehicle_type;
					$datainsert["overspeed_board_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
					$datainsert["overspeed_board_imei"] = $rowvehicle[$x]->vehicle_mv03;
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");


				//get last data
				$this->dbreport->where("overspeed_board_vehicle_id", $rowvehicle[$x]->vehicle_id);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){

					$this->dbreport->where("overspeed_board_vehicle_id", $rowvehicle[$x]->vehicle_id);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{

					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}



			}



		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
	}

	//overspeed by Geofence
	function overspeed_board_geofence($userid="", $orderby="", $typereport="", $startdate = "", $enddate = ""){

		ini_set('memory_limit', '2G');
        printf("OVERSPEED BY GEOFENCE >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";

        $report_type = "overspeed";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "overspeed_";
		$report_new = "overspeed_board_";
		$model = "geofence";
		//$model_array = array("vehicle","jalur","street","geofence");

        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }

        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }

        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }

		if ($orderby == "") {
            $orderby = "asc";
        }

		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_new = $report_new."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_new = $report_new."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_new = $report_new."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_new = $report_new."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_new = $report_new."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_new = $report_new."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_new = $report_new."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_new = $report_new."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_new = $report_new."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_new = $report_new."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_new = $report_new."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_new = $report_new."desember_".$year;
			break;
		}

        $sdate = date("Y-m-d H:i:s", strtotime($startdate)); //wita
        $edate = date("Y-m-d H:i:s", strtotime($enddate));  //wita
        $z =0;
		printf("START DATE - END DATE : %s %s \r\n", $sdate, $edate);

		$this->db->select("user_id,user_dblive");
		$this->db->where("user_id", $userid);
		$q = $this->db->get("user");
        $rowuser = $q->row();

		if(count($rowuser)>0){
			$dblive = $rowuser->user_dblive;
		}else{
			printf("NO DATA USER DB LIVE : %s \r\n",$userid);
			return;
		}

		$this->dblive = $this->load->database($dblive,true);
		$this->dblive->select("geofence_id,geofence_name,geofence_speed,geofence_speed_muatan");
		$this->dblive->order_by("geofence_id", "asc");
		$this->dblive->where("geofence_group", $typereport);
		$this->dblive->where("geofence_user", $userid);
		$this->dblive->where("geofence_status", 1);
		$this->dblive->where("geofence_type", "road");
		$q = $this->dblive->get("geofence");
        $rowgeofence = $q->result();

        $total_process = count($rowgeofence);
        printf("TOTAL PROSES GEOFENCE : %s \r\n",$total_process);
        printf("============================================ \r\n");


			//kosongan
			for($x=0;$x<count($rowgeofence);$x++){
				printf("==PROCESS KOSONGAN %s %s of %s \r\n",$rowgeofence[$x]->geofence_name, $x+1, $total_process);
				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true);
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_geofence_name", $rowgeofence[$x]->geofence_name);
				$this->dbreport->where("overspeed_report_jalur", "kosongan");
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query

					unset($datainsert);
					$datainsert["overspeed_board_vehicle_user_id"] = $userid;
					$datainsert["overspeed_board_geofence"] = $rowgeofence[$x]->geofence_name;
					$datainsert["overspeed_board_jalur"] = "kosongan";
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");


				//get last data
				$this->dbreport->where("overspeed_board_geofence", $rowgeofence[$x]->geofence_name);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$this->dbreport->where("overspeed_board_jalur","kosongan");
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){

					$this->dbreport->where("overspeed_board_geofence", $rowgeofence[$x]->geofence_name);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->where("overspeed_board_jalur","kosongan");
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{

					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}



			}

			//Muatan
			for($x=0;$x<count($rowgeofence);$x++){
				printf("==PROCESS MUATAN %s %s of %s \r\n",$rowgeofence[$x]->geofence_name, $x+1, $total_process);
				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true);
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_geofence_name", $rowgeofence[$x]->geofence_name);
				$this->dbreport->where("overspeed_report_jalur", "muatan");
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query

					unset($datainsert);
					$datainsert["overspeed_board_vehicle_user_id"] = $userid;
					$datainsert["overspeed_board_geofence"] = $rowgeofence[$x]->geofence_name;
					$datainsert["overspeed_board_jalur"] = "muatan";
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");


				//get last data
				$this->dbreport->where("overspeed_board_geofence", $rowgeofence[$x]->geofence_name);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$this->dbreport->where("overspeed_board_jalur","muatan");
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){

					$this->dbreport->where("overspeed_board_geofence", $rowgeofence[$x]->geofence_name);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->where("overspeed_board_jalur","muatan");
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{

					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}



			}



		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
		$this->dblive->close();
		$this->dblive->cache_delete_all();
	}

	//overspeed by Street
	function overspeed_board_street($userid="", $orderby="", $typereport="", $startdate = "", $enddate = ""){

		ini_set('memory_limit', '2G');
        printf("OVERSPEED BY STREET >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";

        $report_type = "overspeed";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "overspeed_";
		$report_new = "overspeed_board_";
		$model = "street";
		//$model_array = array("vehicle","jalur","street","geofence");

        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }

        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }

        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }

		if ($orderby == "") {
            $orderby = "asc";
        }

		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_new = $report_new."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_new = $report_new."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_new = $report_new."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_new = $report_new."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_new = $report_new."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_new = $report_new."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_new = $report_new."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_new = $report_new."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_new = $report_new."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_new = $report_new."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_new = $report_new."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_new = $report_new."desember_".$year;
			break;
		}

        $sdate = date("Y-m-d H:i:s", strtotime($startdate)); //wita
        $edate = date("Y-m-d H:i:s", strtotime($enddate));  //wita
        $z =0;
		printf("START DATE - END DATE : %s %s \r\n", $sdate, $edate);
		$this->db = $this->load->database("default",true);
		$this->db->select("user_id,user_dblive");
		$this->db->select("user_id,user_dblive");
		$this->db->where("user_id", $userid);
		$q = $this->db->get("user");
        $rowuser = $q->row();

		if(count($rowuser)>0){
			$dblive = $rowuser->user_dblive;
		}else{
			printf("NO DATA USER DB LIVE : %s \r\n",$userid);
			return;
		}

		$this->db = $this->load->database("default",true);
		$this->db->distinct("street_name");
		//$this->db->group_by("street_name");
		$this->db->select("street_id,street_name");
		$this->db->order_by("street_name", "asc");
		$this->db->where("street_creator", $userid);
		$this->db->where("street_type", 1); //khusus hauling road
		//$this->db->like("street_name", "KM");
		$q = $this->db->get("street");
        $rowstreet = $q->result();

        $total_process = count($rowstreet);
        printf("TOTAL PROSES STREET : %s \r\n",$total_process);
        printf("============================================ \r\n");

			for($x=0;$x<count($rowstreet);$x++){
				$street_name = str_replace(",", "", $rowstreet[$x]->street_name);
				$ex_street_name = explode(" ",$street_name);
				$street_name_alias = round($ex_street_name[1],0,PHP_ROUND_HALF_UP);
				printf("==PROCESS KOSONGAN %s alias: %s (%s of %s) \r\n",$street_name, $street_name_alias, $x+1, $total_process);

				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true);
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_location", $street_name);
				$this->dbreport->where("overspeed_report_jalur", "kosongan");
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$this->dbreport->where("overspeed_report_event_status", 1); // only group_by 1
				$this->dbreport->where("overspeed_report_event_total !=", 0); // only group_by 1
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query

					unset($datainsert);
					$datainsert["overspeed_board_vehicle_user_id"] = $userid;
					$datainsert["overspeed_board_street"] = $street_name;
					$datainsert["overspeed_board_street_alias"] = $street_name_alias;
					$datainsert["overspeed_board_jalur"] = "kosongan";
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");


				//get last data
				$this->dbreport->where("overspeed_board_street", $street_name);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$this->dbreport->where("overspeed_board_jalur","kosongan");
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){

					$this->dbreport->where("overspeed_board_street", $street_name);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->where("overspeed_board_jalur","kosongan");
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{

					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}



			}

			//Muatan
			for($x=0;$x<count($rowstreet);$x++){
				$street_name = str_replace(",", "", $rowstreet[$x]->street_name);
				$ex_street_name = explode(" ",$street_name);
				$street_name_alias = round($ex_street_name[1],0,PHP_ROUND_HALF_UP);
				printf("==PROCESS MUATAN %s alias: %s (%s of %s) \r\n",$street_name, $street_name_alias, $x+1, $total_process);

				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true);
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_location", $street_name);
				$this->dbreport->where("overspeed_report_jalur", "muatan");
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$this->dbreport->where("overspeed_report_event_status", 1); // only group_by 1
				$this->dbreport->where("overspeed_report_event_total !=", 0); // only group_by 1
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query

					unset($datainsert);
					$datainsert["overspeed_board_vehicle_user_id"] = $userid;
					$datainsert["overspeed_board_street"] = $street_name;
					$datainsert["overspeed_board_street_alias"] = $street_name_alias;
					$datainsert["overspeed_board_jalur"] = "muatan";
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");


				//get last data
				$this->dbreport->where("overspeed_board_street", $street_name);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$this->dbreport->where("overspeed_board_jalur","muatan");
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){

					$this->dbreport->where("overspeed_board_street", $street_name);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->where("overspeed_board_jalur","muatan");
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{

					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}



			}



		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();

	}

	//overspeed by company
	function overspeed_board_company($userid="", $orderby="", $typereport="", $startdate = "", $enddate = ""){

		ini_set('memory_limit', '2G');
        printf("OVERSPEED BY COMPANY >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";

        $report_type = "overspeed";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "overspeed_";
		$report_new = "overspeed_board_";
		$model = "company";
		//$model_array = array("vehicle","jalur","street","geofence");

        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }

        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }

        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }

		if ($orderby == "") {
            $orderby = "asc";
        }

		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_new = $report_new."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_new = $report_new."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_new = $report_new."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_new = $report_new."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_new = $report_new."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_new = $report_new."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_new = $report_new."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_new = $report_new."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_new = $report_new."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_new = $report_new."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_new = $report_new."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_new = $report_new."desember_".$year;
			break;
		}

        $sdate = date("Y-m-d H:i:s", strtotime($startdate)); //wita
        $edate = date("Y-m-d H:i:s", strtotime($enddate));  //wita
        $z =0;
		printf("START DATE - END DATE : %s %s \r\n", $sdate, $edate);

		$this->db = $this->load->database("default",TRUE);
		$this->db->order_by("company_name", $orderby);
		$this->db->select("company_id,company_name");
		$this->db->where("company_created_by", $userid);
		$this->db->where("company_flag", 0);
		$q = $this->db->get("company");
        $rowcompany = $q->result();

        $total_process = count($rowcompany);
        printf("TOTAL PROSES COMPANY : %s \r\n",$total_process);
        printf("============================================ \r\n");


			for($x=0;$x<count($rowcompany);$x++){
				printf("==PROCESS %s %s of %s \r\n",$rowcompany[$x]->company_name, $x+1, $total_process);
				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true);
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_vehicle_company", $rowcompany[$x]->company_id);
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_geofence_type", "road"); //khusus dijalan
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$this->dbreport->where("overspeed_report_event_status", 1); // only group_by 1
				$this->dbreport->where("overspeed_report_event_total !=", 0); // only group_by 1
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query
				//exit();
					unset($datainsert);
					/* $datainsert["overspeed_board_vehicle_user_id"] = $rowvehicle[$x]->vehicle_user_id;
					$datainsert["overspeed_board_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
					$datainsert["overspeed_board_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
					$datainsert["overspeed_board_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
					$datainsert["overspeed_board_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
					$datainsert["overspeed_board_vehicle_type"] = $rowvehicle[$x]->vehicle_type; */
					$datainsert["overspeed_board_vehicle_company"] = $rowcompany[$x]->company_id;
					$datainsert["overspeed_board_company_name"] = $rowcompany[$x]->company_name;
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");


				//get last data
				$this->dbreport->where("overspeed_board_vehicle_company", $rowcompany[$x]->company_id);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){

					$this->dbreport->where("overspeed_board_vehicle_company", $rowcompany[$x]->company_id);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{

					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}



			}



		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
	}

	//overspeed by hour
	function overspeed_board_hour($userid="", $orderby="", $typereport="", $startdate = "", $enddate = ""){

		ini_set('memory_limit', '2G');
        printf("OVERSPEED BY COMPANY >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";

        $report_type = "overspeed";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "overspeed_";
		$report_new = "overspeed_board_";
		$model = "hour";
		//$model_array = array("vehicle","jalur","street","geofence");

        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }

        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }

        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }

		if ($orderby == "") {
            $orderby = "asc";
        }

		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_new = $report_new."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_new = $report_new."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_new = $report_new."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_new = $report_new."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_new = $report_new."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_new = $report_new."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_new = $report_new."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_new = $report_new."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_new = $report_new."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_new = $report_new."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_new = $report_new."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_new = $report_new."desember_".$year;
			break;
		}

        $sdate = date("Y-m-d H:i:s", strtotime($startdate)); //wita
        $edate = date("Y-m-d H:i:s", strtotime($enddate));  //wita
        $z =0;
		printf("START DATE - END DATE : %s %s \r\n", $sdate, $edate);

		//get data hour (24 jam)
		$this->db = $this->load->database("webtracking_ts",true);
		$this->db->order_by("hour_value", $orderby);
		$this->db->select("hour_value,hour_name");
		//$this->db->where("company_created_by", $userid);
		$this->db->where("hour_flag", 0);
		$q = $this->db->get("ts_hour");
        $rowmaster = $q->result();

        $total_process = count($rowmaster);
        printf("TOTAL PROSES HOUR : %s \r\n",$total_process);
        printf("============================================ \r\n");


			for($x=0;$x<count($rowmaster);$x++){
				printf("==PROCESS %s %s of %s \r\n",$rowmaster[$x]->hour_name, $x+1, $total_process);
				$report_date = date("Y-m-d", strtotime($startdate));
				$report_hour_start = date("H:i:s", strtotime($rowmaster[$x]->hour_value.":"."00:00"));
				$report_hour_end = date("H:i:s", strtotime($rowmaster[$x]->hour_value.":"."59:59"));
				$sdate_ex = date("Y-m-d H:i:s", strtotime($report_date." ".$report_hour_start));
				$edate_ex = date("Y-m-d H:i:s", strtotime($report_date." ".$report_hour_end));
				//print_r($sdate_ex." ".$edate_ex);exit();
				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true);
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate_ex);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate_ex);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_geofence_type", "road"); //khusus dijalan
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$this->dbreport->where("overspeed_report_event_status", 1); // only group_by 1
				$this->dbreport->where("overspeed_report_event_total !=", 0); // only group_by 1
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query

					unset($datainsert);
					/* $datainsert["overspeed_board_vehicle_user_id"] = $rowvehicle[$x]->vehicle_user_id;
					$datainsert["overspeed_board_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
					$datainsert["overspeed_board_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
					$datainsert["overspeed_board_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
					$datainsert["overspeed_board_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
					$datainsert["overspeed_board_vehicle_type"] = $rowvehicle[$x]->vehicle_type; */
					$datainsert["overspeed_board_hour_name"] = $rowmaster[$x]->hour_name;
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");


				//get last data
				$this->dbreport->where("overspeed_board_hour_name", $rowmaster[$x]->hour_name);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){

					$this->dbreport->where("overspeed_board_hour_name", $rowmaster[$x]->hour_name);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{

					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}



			}



		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
	}

	//overspeed by level
	function overspeed_board_level($userid="", $orderby="", $typereport="", $startdate = "", $enddate = ""){

		ini_set('memory_limit', '2G');
        printf("OVERSPEED BY COMPANY >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";

        $report_type = "overspeed";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "overspeed_";
		$report_new = "overspeed_board_";
		$model = "level";
		//$model_array = array("vehicle","jalur","street","geofence");

        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }

        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }

        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }

		if ($orderby == "") {
            $orderby = "asc";
        }

		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_new = $report_new."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_new = $report_new."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_new = $report_new."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_new = $report_new."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_new = $report_new."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_new = $report_new."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_new = $report_new."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_new = $report_new."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_new = $report_new."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_new = $report_new."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_new = $report_new."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_new = $report_new."desember_".$year;
			break;
		}

        $sdate = date("Y-m-d H:i:s", strtotime($startdate)); //wita
        $edate = date("Y-m-d H:i:s", strtotime($enddate));  //wita
        $z =0;
		printf("START DATE - END DATE : %s %s \r\n", $sdate, $edate);

		//get data hour (24 jam)
		$this->db = $this->load->database("webtracking_ts",true);
		$this->db->order_by("level_value", $orderby);
		$this->db->select("level_value,level_name");
		//$this->db->where("company_created_by", $userid);
		$this->db->where("level_flag", 0);
		$q = $this->db->get("ts_speed_level");
        $rowmaster = $q->result();

        $total_process = count($rowmaster);
        printf("TOTAL PROSES LEVEL : %s \r\n",$total_process);
        printf("============================================ \r\n");


			for($x=0;$x<count($rowmaster);$x++){
				printf("==PROCESS %s %s of %s \r\n",$rowmaster[$x]->level_name, $x+1, $total_process);
				/* $report_date = date("Y-m-d", strtotime($startdate));
				$report_hour_start = date("H:i:s", strtotime($rowmaster[$x]->level_value.":"."00:00"));
				$report_hour_end = date("H:i:s", strtotime($rowmaster[$x]->level_value.":"."59:59"));
				$sdate_ex = date("Y-m-d H:i:s", strtotime($report_date." ".$report_hour_start));
				$edate_ex = date("Y-m-d H:i:s", strtotime($report_date." ".$report_hour_end)); */
				//print_r($sdate_ex." ".$edate_ex);exit();
				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true);
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_geofence_type", "road"); //khusus dijalan
				$this->dbreport->where("overspeed_report_level",$rowmaster[$x]->level_value);
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$this->dbreport->where("overspeed_report_event_status", 1); // only group_by 1
				$this->dbreport->where("overspeed_report_event_total !=", 0); // only group_by 1
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query

					unset($datainsert);
					/* $datainsert["overspeed_board_vehicle_user_id"] = $rowvehicle[$x]->vehicle_user_id;
					$datainsert["overspeed_board_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
					$datainsert["overspeed_board_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
					$datainsert["overspeed_board_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
					$datainsert["overspeed_board_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
					$datainsert["overspeed_board_vehicle_type"] = $rowvehicle[$x]->vehicle_type; */
					$datainsert["overspeed_board_level_name"] = $rowmaster[$x]->level_name;
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");


				//get last data
				$this->dbreport->where("overspeed_board_level_name", $rowmaster[$x]->level_name);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){

					$this->dbreport->where("overspeed_board_level_name", $rowmaster[$x]->level_name);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{

					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}



			}



		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
	}

	//overspeed by Sign
	function overspeed_board_sign($userid="", $orderby="", $typereport="", $startdate = "", $enddate = ""){

		ini_set('memory_limit', '2G');
        printf("OVERSPEED BY SIGN >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";

        $report_type = "overspeed";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "overspeed_";
		$report_new = "overspeed_board_";
		$model = "sign";
		//$model_array = array("vehicle","jalur","street","geofence");

        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }

        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }

        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }

        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }

		if ($orderby == "") {
            $orderby = "asc";
        }

		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_new = $report_new."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_new = $report_new."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_new = $report_new."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_new = $report_new."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_new = $report_new."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_new = $report_new."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_new = $report_new."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_new = $report_new."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_new = $report_new."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_new = $report_new."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_new = $report_new."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_new = $report_new."desember_".$year;
			break;
		}

        $sdate = date("Y-m-d H:i:s", strtotime($startdate)); //wita
        $edate = date("Y-m-d H:i:s", strtotime($enddate));  //wita
        $z =0;
		printf("START DATE - END DATE : %s %s \r\n", $sdate, $edate);
		/* $this->db = $this->load->database("default",true);
		$this->db->select("user_id,user_dblive");
		$this->db->select("user_id,user_dblive");
		$this->db->where("user_id", $userid);
		$q = $this->db->get("user");
        $rowuser = $q->row();

		if(count($rowuser)>0){
			$dblive = $rowuser->user_dblive;
		}else{
			printf("NO DATA USER DB LIVE : %s \r\n",$userid);
			return;
		} */

		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->select("*");
		$this->dbts->order_by("sign_value", "asc");
		$this->dbts->where("sign_user", $userid);
		$this->dbts->where("sign_type", 1);
		$q = $this->dbts->get("ts_sign");
        $rowsign = $q->result();

        $total_process = count($rowsign);
        printf("TOTAL PROSES SIGN : %s \r\n",$total_process);
        printf("============================================ \r\n");

			for($x=0;$x<count($rowsign);$x++){

				printf("==PROCESS KOSONGAN %s (%s of %s) \r\n",$rowsign[$x]->sign_name,  $x+1, $total_process);

				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true);
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_geofence_limit", $rowsign[$x]->sign_value);
				$this->dbreport->where("overspeed_report_jalur", "kosongan");
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$this->dbreport->where("overspeed_report_event_status", 1); // only group_by 1
				$this->dbreport->where("overspeed_report_event_total !=", 0); // only group_by 1
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query

					unset($datainsert);
					$datainsert["overspeed_board_vehicle_user_id"] = $userid;
					$datainsert["overspeed_board_sign"] = $rowsign[$x]->sign_value;
					$datainsert["overspeed_board_sign_alias"] = $rowsign[$x]->sign_name;
					$datainsert["overspeed_board_jalur"] = "kosongan";
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");


				//get last data
				$this->dbreport->where("overspeed_board_sign", $rowsign[$x]->sign_value);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$this->dbreport->where("overspeed_board_jalur","kosongan");
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){

					$this->dbreport->where("overspeed_board_sign", $rowsign[$x]->sign_value);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->where("overspeed_board_jalur","kosongan");
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{

					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}



			}

			//Muatan
			for($x=0;$x<count($rowsign);$x++){
				printf("==PROCESS MUATAN %s (%s of %s) \r\n",$rowsign[$x]->sign_name,  $x+1, $total_process);

				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true);
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_geofence_limit", $rowsign[$x]->sign_value);
				$this->dbreport->where("overspeed_report_jalur", "muatan");
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$this->dbreport->where("overspeed_report_event_status", 1); // only group_by 1
				$this->dbreport->where("overspeed_report_event_total !=", 0); // only group_by 1
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query

					unset($datainsert);
					$datainsert["overspeed_board_vehicle_user_id"] = $userid;
					$datainsert["overspeed_board_sign"] = $rowsign[$x]->sign_value;
					$datainsert["overspeed_board_sign_alias"] = $rowsign[$x]->sign_name;
					$datainsert["overspeed_board_jalur"] = "muatan";
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");


				//get last data
				$this->dbreport->where("overspeed_board_sign", $rowsign[$x]->sign_value);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$this->dbreport->where("overspeed_board_jalur","muatan");
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){

					$this->dbreport->where("overspeed_board_sign", $rowsign[$x]->sign_value);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->where("overspeed_board_jalur","muatan");
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{

					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}



			}



		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();

	}

	function getPosition_other($longitude, $latitude)
	{
		//$api = $this->config->item('GOOGLE_MAP_API_KEY');
		$api = "AIzaSyCGr6BW7vPItrWq95DxMvL292Kf6jHNA5c"; //lacaktranslog prem
		//$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);
		$georeverse = $this->gpsmodel->getLocation_byGeoCode($latitude, $longitude, $api);

		return $georeverse;
	}

	function getvehicle($vehicle_device){

		$this->db = $this->load->database("default",true);
		$this->db->select("vehicle_id,vehicle_device,vehicle_type,vehicle_name,vehicle_no,vehicle_mv03,vehicle_user_id,
							vehicle_company,vehicle_dbname_live,vehicle_info");
		$this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $vehicle_device);
		$q = $this->db->get("vehicle");
		$rows = $q->row();
		$total_rows = count($rows);

		if($total_rows > 0){
			$data_vehicle = $rows;
			return $data_vehicle;
		}else{
			return false;
		}

	}

	function get_jalurname_new($direction){
		$arah = "";

		if($direction > 0 && $direction <= 180){ // arah ke atas (kosongan)
			$arah = "atas";
			$jalur = "kosongan";
		}else if($direction >= 181 && $direction <= 360){ // arah ke bawah (muatan)
			$arah = "bawah";
			$jalur = "muatan";
		}else{
			$arah = $direction;
			$jalur = "";
		}

		//printf("===Arah : %s \r\n", $arah);

		return $jalur;
	}

	function wind_cardinal($degree) {

            switch( $degree ) {

                case ( $degree >= 348.75 && $degree <= 360 ):
                    $cardinal = "N";
                break;

                case ( $degree >= 0 && $degree <= 11.249 ):
                    $cardinal = "N";
                break;

                case ( $degree >= 11.25 && $degree <= 33.749 ):
                    $cardinal = "NNE";
                break;

                case ( $degree >= 33.75 && $degree <= 56.249 ):
                    $cardinal = "NE";
                break;

                case ( $degree >= 56.25 && $degree <= 78.749 ):
                    $cardinal = "ENE";
                break;

                case ( $degree >= 78.75 && $degree <= 101.249 ):
                    $cardinal = "E";
                break;

                case ( $degree >= 101.25 && $degree <= 123.749 ):
                    $cardinal = "ESE";
                break;

                case ( $degree >= 123.75 && $degree <= 146.249 ):
                    $cardinal = "SE";
                break;

                case ( $degree >= 146.25 && $degree <= 168.749 ):
                    $cardinal = "N";
                break;

                case ( $degree >= 168.75 && $degree <= 191.249 ):
                    $cardinal = "S";
                break;

                case ( $degree >= 191.25 && $degree <= 213.749 ):
                    $cardinal = "SSW";
                break;

                case ( $degree >= 213.75 && $degree <= 236.249 ):
                    $cardinal = "SW";
                break;

                case ( $degree >= 236.25 && $degree <= 258.749 ):
                    $cardinal = "WSW";
                break;

                case ( $degree >= 258.75 && $degree <= 281.249 ):
                    $cardinal = "W";
                break;

                case ( $degree >= 281.25 && $degree <= 303.749 ):
                    $cardinal = "WNW";
                break;

                case ( $degree >= 303.75 && $degree <= 326.249 ):
                    $cardinal = "NW";
                break;

                case ( $degree >= 326.25 && $degree <= 348.749 ):
                    $cardinal = "NNW";
                break;

                default:
                    $cardinal = null;

            }

           return $cardinal;

       }

	function telegram_direct($groupid,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        //$url = "http://lacak-mobil.com/telegram/telegram_directpost";
		//$url = "http://admintib.buddiyanto.my.id/telegram/telegram_directpost";
		//$url = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
		//$url = "http://admin.abditrack.com/telegram/telegram_directpost";
		$url = $this->config->item('url_send_telegram');


        $data = array("id" => $groupid, "message" => $message);
        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);	//new
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die("Curl failed: " . curL_error($ch));
        }
        echo $result;
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);

    }

	function sendnotif_wa_ovspeed($wa_token,$wa_title_name,$wa_speed_level_alias,$wa_gps_time,$wa_vehicle_no,$wa_vehicle_name,$wa_driver_name,$wa_position_name,$wa_url,$wa_gpsspeed_kph,$wa_geofence_speed_limit,$wa_geofence_name,$wa_jalur,$wa_company_telegram){

		$mytoken = $wa_token->sess_value;

		$authorization = "token: Bearer ".$mytoken;
		$url = $this->config->item('WA_URL_POST_MESSAGE');

		$DataToUpload = array();
		unset($DataToUpload);

		$namespace = $this->config->item('WA_NAMESPACE');
		$nametemplate = $this->config->item('WA_TEMPLATE_VIOLATION_OVS'); //10 param
		$recipient_dt = $wa_company_telegram;
		$total_recipient = count($recipient_dt);

		if($total_recipient>0){
			$recipient_dt_ex = explode(";",$recipient_dt->company_hp);

			$total_recipient_ex = count($recipient_dt_ex);

		}

		for($w=0;$w<$total_recipient_ex;$w++){


			$DataToUpload->to = $recipient_dt_ex[$w];
			$DataToUpload->type = "template";
			$DataToUpload->template['namespace'] = $namespace;
			$DataToUpload->template['language']['policy'] = "deterministic";
			$DataToUpload->template['language']['code'] = "en";
			$DataToUpload->template['name'] = $nametemplate;
			$DataToUpload->template['components'][0]->type = "body";
			$DataToUpload->template['components'][0]->parameters[0]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[0]['text'] = $wa_title_name." ".$wa_speed_level_alias;
			$DataToUpload->template['components'][0]->parameters[1]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[1]['text'] = $wa_gps_time;
			$DataToUpload->template['components'][0]->parameters[2]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[2]['text'] = $wa_vehicle_no." ".$wa_vehicle_name;
			$DataToUpload->template['components'][0]->parameters[3]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[3]['text'] = $wa_driver_name;
			$DataToUpload->template['components'][0]->parameters[4]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[4]['text'] = $wa_position_name;
			$DataToUpload->template['components'][0]->parameters[5]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[5]['text'] = $wa_url;
			$DataToUpload->template['components'][0]->parameters[6]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[6]['text'] = $wa_gpsspeed_kph;
			$DataToUpload->template['components'][0]->parameters[7]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[7]['text'] = $wa_geofence_speed_limit;
			$DataToUpload->template['components'][0]->parameters[8]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[8]['text'] = $wa_geofence_name;
			$DataToUpload->template['components'][0]->parameters[9]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[9]['text'] = $wa_jalur;

			$content = json_encode($DataToUpload);

			//print_r($content);

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$headers = array(
						"Content-Type: application/json",
						"Authorization: Bearer " .$mytoken,
						);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			//for debug only!
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($curl);
			echo $result;


		}


		printf("-------- \r\n");
	}
	
	function sendnotif_wa_ovspeed_bib($wa_token,$wa_title_name,$wa_speed_level_alias,$wa_gps_time,$wa_vehicle_no,$wa_vehicle_name,$wa_driver_name,$wa_position_name,$wa_url,$wa_gpsspeed_kph,$wa_geofence_speed_limit,$wa_geofence_name,$wa_jalur,$wa_company_telegram){

		$mytoken = $wa_token->sess_value;

		$authorization = "token: Bearer ".$mytoken;
		$url = $this->config->item('WA_URL_POST_MESSAGE');

		$DataToUpload = array();
		unset($DataToUpload);

		$namespace = $this->config->item('WA_NAMESPACE');
		$nametemplate = $this->config->item('WA_TEMPLATE_VIOLATION_OVS'); //10 param
		$recipient_dt = $this->config->item('WA_NOMOR_USER_BIB');
		$total_recipient = count($recipient_dt);

		for($w=0;$w<$total_recipient;$w++){


			$DataToUpload->to = $recipient_dt[$w];
			$DataToUpload->type = "template";
			$DataToUpload->template['namespace'] = $namespace;
			$DataToUpload->template['language']['policy'] = "deterministic";
			$DataToUpload->template['language']['code'] = "en";
			$DataToUpload->template['name'] = $nametemplate;
			$DataToUpload->template['components'][0]->type = "body";
			$DataToUpload->template['components'][0]->parameters[0]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[0]['text'] = $wa_title_name." ".$wa_speed_level_alias;
			$DataToUpload->template['components'][0]->parameters[1]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[1]['text'] = $wa_gps_time;
			$DataToUpload->template['components'][0]->parameters[2]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[2]['text'] = $wa_vehicle_no." ".$wa_vehicle_name;
			$DataToUpload->template['components'][0]->parameters[3]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[3]['text'] = $wa_driver_name;
			$DataToUpload->template['components'][0]->parameters[4]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[4]['text'] = $wa_position_name;
			$DataToUpload->template['components'][0]->parameters[5]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[5]['text'] = $wa_url;
			$DataToUpload->template['components'][0]->parameters[6]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[6]['text'] = $wa_gpsspeed_kph;
			$DataToUpload->template['components'][0]->parameters[7]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[7]['text'] = $wa_geofence_speed_limit;
			$DataToUpload->template['components'][0]->parameters[8]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[8]['text'] = $wa_geofence_name;
			$DataToUpload->template['components'][0]->parameters[9]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[9]['text'] = $wa_jalur;

			$content = json_encode($DataToUpload);

			//print_r($content);

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$headers = array(
						"Content-Type: application/json",
						"Authorization: Bearer " .$mytoken,
						);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			//for debug only!
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($curl);
			echo $result;


		}


		printf("-------- \r\n");
	}
	
	function getWAToken($usid)
	{
		$status = false;
		$this->dbts = $this->load->database("webtracking_ts", TRUE);
		$this->dbts->order_by("sess_expired","desc");
		$this->dbts->select("sess_value");
		$this->dbts->where("sess_user",$usid);
		$this->dbts->where("sess_status",1);
		$this->dbts->limit(1);
		$q = $this->dbts->get("ts_wa_token");
		$data = $q->row();

		$this->dbts->close();
		$this->dbts->cache_delete_all();

		return $data;

	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
