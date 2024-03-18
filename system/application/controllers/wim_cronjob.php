<?php
include "base.php";

class Wim_cronjob extends Base {
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("gpsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		
	}
	
	function completedWIM($tID=0, $type="")
	{
		ini_set('memory_limit', '3G');
		date_default_timezone_set("Asia/Jakarta");
		/* if($date == ""){
			$nowdate = date("Y-m-d 00:00:00");
		}else{
			
			$nowdate = date("Y-m-d H:i:s", strtotime($date." ".$time));
		} */
		
		$start_time = date("Y-m-d H:i:s");
		$limitdate = date("Y-m-d H:i:s", strtotime("2022-08-04 00:00:00"));
		
		printf("=============================== \r\n");
		$lat_wim = "-3.627237";
		$long_wim = "115.652437";
		printf("====PROCESS DATA DATE==== : %s \r\n",$limitdate);
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("integrationwim_id,integrationwim_transactionID,integrationwim_truckID,integrationwim_noRangka,
								integrationwim_penimbanganStartLocal,integrationwim_penimbanganFinishLocal,integrationwim_distanceWB");
		$this->dbreport->order_by("integrationwim_penimbanganStartLocal","desc");
		$this->dbreport->where("integrationwim_penimbanganStartLocal >=", $limitdate);
		
		if($tID !=0){
			$this->dbreport->where("integrationwim_transactionID",$tID);
			
		}else{
			if($type == "past"){
				$this->dbreport->where("integrationwim_last_rom", null); //rom kosong
				//$this->dbreport->limit(30); 
			}else{
				$this->dbreport->where("integrationwim_fms_status",0); //belum ke update fms
				
			}
			
		}
		
		//$this->dbreport->where("integrationwim_distanceWB_status",0); //belum ke cek
		$this->dbreport->where("integrationwim_operator_status",0); //belum ke update operator
		$this->dbreport->where("integrationwim_status","ACTUAL");
		
		$this->dbreport->from("historikal_integrationwim_unit");
        $q = $this->dbreport->get();
        $rows = $q->result();
		$total = count($rows);
		
		$rombib_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK");
		$wim_register = array("KM 13.5");
		
		printf("TOTAL DATA : %s \r\n",$total);
		if($total>0)
		{
			//content Looping 
			for($x=0;$x<count($rows);$x++)
			{
				
				$no_urut = $x+1;
				printf("============================================================== \r\n");
				printf("PROCESS : TransID %s %s %s of %s \r\n",$rows[$x]->integrationwim_transactionID, $rows[$x]->integrationwim_truckID, $no_urut, $total);
				$id_data = $rows[$x]->integrationwim_id;
				$no_unit = $rows[$x]->integrationwim_truckID;
				$id_trx = $rows[$x]->integrationwim_transactionID;
				$no_rangka = $rows[$x]->integrationwim_noRangka;
				$localtime_start = $rows[$x]->integrationwim_penimbanganStartLocal;
				$localtime_end = $rows[$x]->integrationwim_penimbanganFinishLocal;
				$vehicle = $this->getVehicle_fromFMS($no_unit,$no_rangka);
				//print_r($vehicle);
				
				if($vehicle == true)
				{
					//get lastpoition from DB live
					$db_live = $vehicle->vehicle_dbname_live;
					$ex_device = explode("@",$vehicle->vehicle_device);
					$gps_imei = $ex_device[0];
					$cam_imei = $vehicle->vehicle_mv03;
					$vehicle_no = $vehicle->vehicle_no;
					printf("GET DATA GPS FROM : %s WIM START LOCAL TIME: %s END LOCAL TIME: %s \r\n",$no_unit,$localtime_start,$localtime_end);
					$datapos = $this->get_datapositionDBPORT($vehicle,$localtime_start,$localtime_end,$id_data);
					
					//print_r($datapos);exit();
					$datapos_stop = array();
					$datapos_stop_rom = array();
					$datapos_stop_rom_time = array();
					$datapos_wim = array();
					$no = 0;
					
					
					if ($datapos != false)
					{
					
						for($i=0;$i<count($datapos);$i++)
						{
							$speed = $datapos[$i]->gps_speed;
							$lat_coord =  $datapos[$i]->gps_latitude_real;
							$lng_coord =  $datapos[$i]->gps_longitude_real;
							$gps_time = $datapos[$i]->gps_time;
							
								$position = $this->getPosition_other($lng_coord, $lat_coord);
									if(isset($position)){
										$ex_position = explode(",",$position->display_name);
										if(count($ex_position)>0){
											$position_name = $ex_position[0];
										}else{
											$position_name = $ex_position[0];
										}
									}else{
										$position_name = $position->display_name;
									}
								
							//get last ROM condition
							if($speed == 0)
							{
								if (in_array($position_name, $rombib_register))
									{
										$no_urut = $no+1;
										$info_name = $position_name;
										$info_time = $gps_time;
										
										printf("===Lokasi ROM & Speed Nol: %s %s \r\n", $position_name, $gps_time);
										array_push($datapos_stop_rom, $info_name);
										array_push($datapos_stop_rom_time, $info_time);
									}
									
									$info = $position_name;
									array_push($datapos_stop,$info);
							}
							
							
							if (in_array($position_name, $wim_register))
							{
								$info_wim = $position_name."|".$gps_time."|".$lat_coord."|".$lng_coord;
								array_push($datapos_wim,$info_wim);
							}
							
						}
						
						//rekap data in ROM yg speed nol
						if(count($datapos_stop_rom)>0)
						{
							$city_counts = array();                            // create an array to hold the counts
							foreach ($datapos_stop_rom as $city_object) {                 // loop over the array of city objects
							// checking isset will prevent undefined index notices    
							if (isset($city_counts[$city_object])) {            
									$city_counts[$city_object]++;        // increment the count for the city
								} else {
									$city_counts[$city_object] = 1;      // initialize the count for the city
								}
							}
							
							
							//jika hanya ada 1 nama rom maka langsung ambil
							if(count($city_counts) == 1)
							{
								$last_rom_name = $datapos_stop_rom[0];
								printf("DATA LAST ROM : %s \r\n",$last_rom_name);
								$data_material = $this->getMaterial_info($last_rom_name);
								
								if($data_material != false){
									
									
									$data["integrationwim_material_id "] = $data_material->material_id;
									$data["integrationwim_hauling_id"] = $data_material->material_hauling;
									$data["integrationwim_itws_coal "] = $data_material->material_coal;
								}
								
								$data["integrationwim_last_rom"] = $last_rom_name;
								$this->dbreport = $this->load->database("tensor_report",TRUE);
								$this->dbreport->where("integrationwim_id", $id_data);
								$this->dbreport->limit(1);
								$this->dbreport->update("historikal_integrationwim_unit",$data);
								
								
								printf("UPDATE LAST ROM OK \r\n ");
								printf("============================= \r\n");
							}
							//kondisi jika didapat > 1 nama rom
							else
							{
								//analisa
								//send telegram
								$title_name = "WIM TRANSACTION ID ".$id_trx;
									$message = urlencode(
											"".$title_name." \n".
											"TRANSACTION ID: ".$id_trx." \n".
											"TERDETEKSI LEBIH DARI 1 ROM. TOTAL : ".count($city_counts)." ROM"." \n"
										);
								sleep(2);		
								$sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
								
							}
						
						
						}
						else
						{
							printf("TIDAK ADA DATA SPEED NOL DI GEOFENCE ROM \r\n");
							
						}
						
						//rekap duration in ROM
						if(count($datapos_stop_rom_time)>0)
						{
							usort($datapos_stop_rom_time, function($a, $b) {
							$dateTimestamp1 = strtotime($a);
							$dateTimestamp2 = strtotime($b);

							return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
							});
							
							$min_rom_datetime = $datapos_stop_rom_time[0];
							$max_rom_datetime = $datapos_stop_rom_time[count($datapos_stop_rom_time) - 1];
							printf("DATA IN ROM : min %s max %s \r\n",$min_rom_datetime,$max_rom_datetime);
							
					
							$min_rom_datetime_wita = date("Y-m-d H:i:s", strtotime($min_rom_datetime . "+7hours"));
							$max_rom_datetime_wita = date("Y-m-d H:i:s", strtotime($max_rom_datetime . "+7hours"));
							
							
							$data["integrationwim_last_rom_stime"] = $min_rom_datetime_wita;
							$data["integrationwim_last_rom_etime"] = $max_rom_datetime_wita;
							$this->dbreport = $this->load->database("tensor_report",TRUE);
							$this->dbreport->where("integrationwim_id", $id_data);
							$this->dbreport->limit(1);
							$this->dbreport->update("historikal_integrationwim_unit",$data);
							
							printf("UPDATE DURASI LAST ROM OK \r\n ");
							printf("============================= \r\n");
							
						}
						else
						{
							printf("TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE ROM \r\n");
							
						}
											
						//get near end time near WIM	
						if(count($datapos_wim)>0)
						{
							//rekap duration in ROM
							usort($datapos_wim, function($a1, $b1) 
							{
								$dateTimestamp1_w = strtotime($a1);
								$dateTimestamp2_w = strtotime($b1);

								return $dateTimestamp1_w < $dateTimestamp2_w ? -1: 1;
							});
								
							$min_wim_gpsdata = $datapos_wim[0];
							printf("FIRST DATA IN WIM : min %s \r\n",$min_wim_gpsdata);
							
							if(isset($min_wim_gpsdata))
							{
								
								$ex_min_wim_gpsdata = explode("|",$min_wim_gpsdata);
								
								$last_lat = $ex_min_wim_gpsdata[2];
								$last_long = $ex_min_wim_gpsdata[3];
								
								//compare distance WIM vs Location GPS
								$distance_wb = $this->getDistance_radius($lat_wim,$long_wim,$last_lat,$last_long);
								$distance_wb_meter = $distance_wb*1000;
								//update REMARK by trans ID unique
								unset($data);
								
								$data["integrationwim_distanceWB"] = $distance_wb_meter;
								$data["integrationwim_distanceWB_status"] = 1; //sudah di cek dan ada data distance
								$this->dbreport = $this->load->database("tensor_report",TRUE);
								$this->dbreport->where("integrationwim_id", $id_data);
								$this->dbreport->limit(1);
								$this->dbreport->update("historikal_integrationwim_unit",$data);
													
								printf("UPDATE REMARK DISTANCE WB OK \r\n ");
								printf("============================= \r\n");
							}
							
						}
						else
						{
							printf("NO DATA GPS at WIM %s \r\n",$vehicle_no);
						}
							
							
				
					}
					else
					{
						printf("NO DATA GPS %s \r\n",$vehicle_no);
					}
					
					
				}
				else
				{
					
						/* $data["integrationwim_distanceWB_status"] = 2;
						$this->dbreport = $this->load->database("tensor_report",TRUE);
						$this->dbreport->where("integrationwim_id", $id_data);
						$this->dbreport->limit(1);
						$this->dbreport->update("historikal_integrationwim_unit",$data);  */
					
					printf("=====NO Data No Unit====== \r\n");
					printf("============================= \r\n");
					
				}
				
				
				//GET DATA DRIVER BY CAM FACE RECOG
				
				if($vehicle == true)
				{
					//get one time token
					$session_id = $this->getOneTimetokenAPI("temanindobara","000000","4408");
					printf("===ONETIME SESSION : %s \r\n", $session_id);
					$get_laststatus = $this->devicestatusapi($session_id,$cam_imei,$vehicle_no);
					
					if($get_laststatus != false){
						
						$ex_simper = explode("|",$get_laststatus);
						$simper_id = $ex_simper[0];
						$simper_detected = $ex_simper[1];
						
							
							unset($data);
							$data["integrationwim_driver_id"] = $simper_id;
							$data["integrationwim_driver_name"] = "";
							$data["integrationwim_driver_change"] = $simper_detected;
							$this->dbreport = $this->load->database("tensor_report",TRUE);
							$this->dbreport->where("integrationwim_id", $id_data);
							$this->dbreport->limit(1);
							$this->dbreport->update("historikal_integrationwim_unit",$data);
							printf("UPDATE DRIVER FROM API OK %s \r\n",$vehicle_no);
						
					}
					
					
					// GET EXCA BY RADIUS HOLD dulu
					/* if($last_rom_name == "ROM A2"){
						printf("===GET DATA RADIUS EXCA : %s \r\n", $vehicle_no);
						$datarad = $this->get_dataradiusDBLIVE($gps_imei,$db_live,$min_rom_datetime,$max_rom_datetime);
						
						//jika ada maka
						if(count($datarad)>0){
							$datarad_exca = array();
							for($i=0;$i<count($datarad_exca);$i++)
							{
								$host_imei = $data_rad_exca[$i]->radius_host;
								$host_radius = $data_rad_exca[$i]->radius_meter;
								$guest_time = $data_rad_exca[$i]->radius_guest_time;
								$v_exca = $this->getVehicle_byIMEI_fromFMS($host_imei);
								$host_exca_name = $v_exca->vehicle_no;
								
								$info_exca = $host_exca_name."|".$host_radius."|".$guest_time;
								array_push($datarad_exca, $info_exca);
								
								
								
							}
							
							print_r($datarad_exca);exit();
							
						}
					}else{
						printf("===BUKAN DARI ROM A2 : %s %s \r\n", $vehicle_no, $last_rom_name);
					} */
					
				}
				
				//update FMS status + datettime
				unset($data);
				$now_time   = date("Y-m-d H:i:s");
				$now_time_wita = date("Y-m-d H:i:s", strtotime($now_time . "+1hours"));
							
				$data["integrationwim_fms_status"] = 1;
				$data["integrationwim_fms_status_datetime"] = $now_time_wita;
				$this->dbreport = $this->load->database("tensor_report",TRUE);
				$this->dbreport->where("integrationwim_id", $id_data);
				$this->dbreport->limit(1);
				$this->dbreport->update("historikal_integrationwim_unit",$data);
				printf("UPDATE FMS STATUS OK %s \r\n",$now_time_wita);
				
			}
			
		}
		else
		{
			printf("======No DATA WIM====== \r\n");
			printf("============================= \r\n");
		}
		
		//send telegram
		if($total > 0){
			$finish_time = date("Y-m-d H:i:s");
			$title_name = "WIM CRONJOB";
			$message = urlencode(
					"".$title_name." \n".
					"PERIODE DATA START: ".$limitdate." \n".
					"TOTAL DATA CHECK: ".$total." \n".
					"CRONJOB START: ".$start_time." \n".
					"CRONJOB END: ".$finish_time." \n"
					);
			sleep(2);		
			//$sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
			
		}
		
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
		
		printf("========FINISH====== \r\n");
		
		
	}
	
	function completedPORT($tID=0, $type="")
	{
		ini_set('memory_limit', '3G');
		date_default_timezone_set("Asia/Jakarta");
		/* if($date == ""){
			
		}else{
			
			$nowdate = date("Y-m-d H:i:s", strtotime($date." ".$time));
		} */
		
		$limit_now = date("Y-m-d H:i:s");
		$limit_start = date("Y-m-d 00:00:00");
		$limit_end = date("Y-m-d H:i:s", strtotime($limit_now . "-2hours"));
		printf("PROSES PERIODE LIMIT TIME %s to %s \r\n",$limit_start, $limit_end); 
	
		
		printf("=============================== \r\n");
		$lat_wim = "-3.627237";
		$long_wim = "115.652437";
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("integrationwim_id,integrationwim_transactionID,integrationwim_truckID,integrationwim_noRangka,
								integrationwim_penimbanganStartLocal,integrationwim_penimbanganFinishLocal,integrationwim_distanceWB");
		$this->dbreport->order_by("integrationwim_penimbanganStartLocal","asc");
		$this->dbreport->where("integrationwim_penimbanganStartLocal >=", $limit_start);
		//$this->dbreport->where("integrationwim_penimbanganStartLocal <", $limit_end);
		if($tID !=0){
			$this->dbreport->where("integrationwim_transactionID",$tID);
			
		}else{
			$this->dbreport->where("integrationwim_dumping_fms_status",0); //belum ke update fms
		}
		$this->dbreport->where("integrationwim_fms_status",1); //sudah di cek last ROM
		$this->dbreport->where("integrationwim_status","ACTUAL");
		$this->dbreport->from("historikal_integrationwim_unit");
		//$this->dbreport->limit(30);
        $q = $this->dbreport->get();
        $rows = $q->result();
		$total = count($rows);
		
		$portbib_register = $this->config->item('port_register_autocheck');
		$cpbib_register =  $this->config->item('cp_register_autocheck');
		printf("TOTAL DATA : %s \r\n",$total);
		if($total>0)
		{
			//content Looping 
			for($x=0;$x<count($rows);$x++)
			{
				
				$no_urut = $x+1;
				printf("============================================================== \r\n");
				printf("PROCESS : TransID %s %s %s of %s \r\n",$rows[$x]->integrationwim_transactionID, $rows[$x]->integrationwim_truckID, $no_urut, $total);
				$id_data = $rows[$x]->integrationwim_id;
				$no_unit = $rows[$x]->integrationwim_truckID;
				$id_trx = $rows[$x]->integrationwim_transactionID;
				$no_rangka = $rows[$x]->integrationwim_noRangka;
				$localtime_start = $rows[$x]->integrationwim_penimbanganStartLocal;
				$localtime_end = $rows[$x]->integrationwim_penimbanganFinishLocal;
				$vehicle = $this->getVehicle_fromFMS($no_unit,$no_rangka);
				//print_r($vehicle);
				
				if($vehicle == true)
				{
					//get lastpoition from DB live
					$db_live = $vehicle->vehicle_dbname_live;
					$ex_device = explode("@",$vehicle->vehicle_device);
					$gps_imei = $ex_device[0];
					$cam_imei = $vehicle->vehicle_mv03;
					$vehicle_no = $vehicle->vehicle_no;
					printf("GET DATA GPS FROM : %s WIM START LOCAL TIME: %s END LOCAL TIME: %s \r\n",$no_unit,$localtime_start,$localtime_end);
					$datapos = $this->get_datapositionDUMPING($vehicle,$localtime_start,$localtime_end,$id_data);
					
					//print_r($datapos);exit();
					$datapos_stop = array();
					$datapos_stop_port = array();
					$datapos_stop_cp = array();
					$datapos_stop_port_time = array();
					$datapos_stop_cp_time = array();
					$datapos_wim = array();
					$no = 0;
					
					
					if ($datapos != false)
					{
					
						for($i=0;$i<count($datapos);$i++)
						{
							$speed = $datapos[$i]->gps_speed;
							$lat_coord =  $datapos[$i]->gps_latitude_real;
							$lng_coord =  $datapos[$i]->gps_longitude_real;
							$gps_time = $datapos[$i]->gps_time;
							
								$position = $this->getPosition_other($lng_coord, $lat_coord);
									if(isset($position)){
										$ex_position = explode(",",$position->display_name);
										if(count($ex_position)>0){
											$position_name = $ex_position[0];
										}else{
											$position_name = $ex_position[0];
										}
									}else{
										$position_name = $position->display_name;
									}
								
								//get last PORT condition semua sudah speed nol
								if (in_array($position_name, $portbib_register))
									{
										$no_urut = $no+1;
										$info_name = $position_name;
										$info_time = $gps_time;
										
										printf("===Lokasi PORT & Speed Nol: %s %s \r\n", $position_name, $gps_time);
										array_push($datapos_stop_port, $info_name);
										array_push($datapos_stop_port_time, $info_time);
									}
									
								if (in_array($position_name, $cpbib_register))
									{
										$no_urut = $no+1;
										$info_name = $position_name;
										$info_time = $gps_time;
										
										printf("===Lokasi PORT & Speed Nol: %s %s \r\n", $position_name, $gps_time);
										array_push($datapos_stop_cp, $info_name);
										array_push($datapos_stop_cp_time, $info_time);
									}
									
									$info = $position_name;
									
					
							
						}
						
						/* print_r($datapos_stop_port);
						print_r($datapos_stop_cp);exit(); */
						
						//rekap data in CP
						
						if(count($datapos_stop_cp)>0)
						{
								$city_counts = array();                            // create an array to hold the counts
								foreach ($datapos_stop_cp as $city_object) {                 // loop over the array of city objects
								// checking isset will prevent undefined index notices    
								if (isset($city_counts[$city_object])) {            
										$city_counts[$city_object]++;        // increment the count for the city
									} else {
										$city_counts[$city_object] = 1;      // initialize the count for the city
									}
								}
								
								
								//jika hanya ada 1 nama CP maka langsung ambil
								if(count($city_counts) == 1)
								{
									$last_cp_name = $datapos_stop_cp[0];
									printf("DATA LAST CP : %s \r\n",$last_cp_name);
									
									
									$data["integrationwim_dumping_fms_port"] = "PORT BIB";
									$data["integrationwim_dumping_fms_cp"] = $last_cp_name;
								
									
									$this->dbreport = $this->load->database("tensor_report",TRUE);
									$this->dbreport->where("integrationwim_id", $id_data);
									$this->dbreport->limit(1);
									$this->dbreport->update("historikal_integrationwim_unit",$data);
									
									
									printf("UPDATE LAST PORT OK \r\n ");
									printf("============================= \r\n");
								}
								//kondisi jika didapat > 1 nama PORT
								else
								{
									printf("TERDETEKSI 2 DATA CP \r\n ");
									//analisa
									//send telegram
									$title_name = "WIM TRANSACTION ID ".$id_trx;
										$message = urlencode(
												"".$title_name." \n".
												"TRANSACTION ID: ".$id_trx." \n".
												"TERDETEKSI LEBIH DARI 1 CP. TOTAL : ".count($city_counts)." CP"." \n"
											);
									sleep(2);		
									$sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
									
								}
								
								
								//rekap duration in CP
								if(count($datapos_stop_cp_time)>0)
								{
									usort($datapos_stop_cp_time, function($a, $b) {
									$dateTimestamp1 = strtotime($a);
									$dateTimestamp2 = strtotime($b);

									return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
									});
									
									$cp_datetime = $datapos_stop_cp_time[0];
									$cp_datetime_wita = date("Y-m-d H:i:s", strtotime($cp_datetime . "+7hours"));
									
									printf("DATA IN PORT WITA: %s \r\n",$cp_datetime_wita);
									$data["integrationwim_dumping_fms_time"] = $cp_datetime_wita;
								
									
									$this->dbreport = $this->load->database("tensor_report",TRUE);
									$this->dbreport->where("integrationwim_id", $id_data);
									$this->dbreport->limit(1);
									$this->dbreport->update("historikal_integrationwim_unit",$data);
									
									printf("UPDATE DURASI LAST PORT OK \r\n ");
									printf("============================= \r\n");
									
								}
								else
								{
									printf("TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE CP \r\n");
									
								}
								
								unset($data);
								$now_time   = date("Y-m-d H:i:s");
								$now_time_wita = date("Y-m-d H:i:s", strtotime($now_time . "+1hours"));
							
								$data["integrationwim_dumping_fms_status"] = 1;
								$data["integrationwim_dumping_fms_status_datetime"] = $now_time_wita;
								$this->dbreport = $this->load->database("tensor_report",TRUE);
								$this->dbreport->where("integrationwim_id", $id_data);
								$this->dbreport->limit(1);
								$this->dbreport->update("historikal_integrationwim_unit",$data);
								printf("UPDATE FMS STATUS OK %s \r\n",$now_time_wita);
							
						}
						else
						{
							//jika tidak ada di CP maka show ambil dari data port
							//rekap data in PORT yg speed nol
							if(count($datapos_stop_port)>0)
							{
								$city_counts = array();                            // create an array to hold the counts
								foreach ($datapos_stop_port as $city_object) {                 // loop over the array of city objects
								// checking isset will prevent undefined index notices    
								if (isset($city_counts[$city_object])) {            
										$city_counts[$city_object]++;        // increment the count for the city
									} else {
										$city_counts[$city_object] = 1;      // initialize the count for the city
									}
								}
								
								
								//jika hanya ada 1 nama PORT maka langsung ambil
								if(count($city_counts) == 1)
								{
									$last_port_name = $datapos_stop_port[0];
									printf("DATA LAST PORT : %s \r\n",$last_port_name);
									
								
									$data["integrationwim_dumping_fms_port"] = $last_port_name;
									$data["integrationwim_dumping_fms_cp"] = "";
									
									
									$this->dbreport = $this->load->database("tensor_report",TRUE);
									$this->dbreport->where("integrationwim_id", $id_data);
									$this->dbreport->limit(1);
									$this->dbreport->update("historikal_integrationwim_unit",$data);
									
									
									printf("UPDATE LAST PORT OK \r\n ");
									printf("============================= \r\n");
								}
								//kondisi jika didapat > 1 nama PORT
								else
								{
									printf("TERDETEKSI 2 DATA PORT \r\n ");
									//analisa
									//send telegram
									$title_name = "WIM TRANSACTION ID ".$id_trx;
										$message = urlencode(
												"".$title_name." \n".
												"TRANSACTION ID: ".$id_trx." \n".
												"TERDETEKSI LEBIH DARI 1 PORT. TOTAL : ".count($city_counts)." PORT"." \n"
											);
									sleep(2);		
									$sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
									
								}
								
								unset($data);
								$now_time   = date("Y-m-d H:i:s");
								$now_time_wita = date("Y-m-d H:i:s", strtotime($now_time . "+1hours"));
							
								$data["integrationwim_dumping_fms_status"] = 1;
								$data["integrationwim_dumping_fms_status_datetime"] = $now_time_wita;
								$this->dbreport = $this->load->database("tensor_report",TRUE);
								$this->dbreport->where("integrationwim_id", $id_data);
								$this->dbreport->limit(1);
								$this->dbreport->update("historikal_integrationwim_unit",$data);
								printf("UPDATE FMS STATUS OK %s \r\n",$now_time_wita);
							
							}
							else
							{
								printf("TIDAK ADA DATA SPEED NOL DI GEOFENCE PORT \r\n");
								
							}
							
							
								//rekap duration in PORT
								if(count($datapos_stop_port_time)>0)
								{
									usort($datapos_stop_port_time, function($a, $b) {
									$dateTimestamp1 = strtotime($a);
									$dateTimestamp2 = strtotime($b);

									return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
									});
									
									$port_datetime = $datapos_stop_port_time[0];
									$port_datetime_wita = date("Y-m-d H:i:s", strtotime($port_datetime . "+7hours"));
									
									printf("DATA IN PORT WITA: %s \r\n",$port_datetime_wita);
									$data["integrationwim_dumping_fms_time"] = $port_datetime_wita;
								
									
									$this->dbreport = $this->load->database("tensor_report",TRUE);
									$this->dbreport->where("integrationwim_id", $id_data);
									$this->dbreport->limit(1);
									$this->dbreport->update("historikal_integrationwim_unit",$data);
									
									printf("UPDATE DURASI LAST PORT OK \r\n ");
									printf("============================= \r\n");
									
								}
								else
								{
									printf("TIDAK ADA DATA DURASI SPEED NOL DI GEOFENCE CP \r\n");
									
								}
						
						}
						
						
					}
					else
					{
						printf("NO DATA GPS %s \r\n",$vehicle_no);
					}
					
					
				}
				else
				{
					
		
					
					printf("=====NO Data No Unit====== \r\n");
					printf("============================= \r\n");
					
				}
			
				
			}
			
		}
		else
		{
			printf("======No DATA WIM====== \r\n");
			printf("============================= \r\n");
		}
		
		//send telegram
		if($total > 0){
			$finish_time = date("Y-m-d H:i:s");
			$title_name = "WIM CRONJOB DUMPING";
			$message = urlencode(
					"".$title_name." \n".
					"PERIODE DATA START: ".$limit_start." \n".
					"TOTAL DATA CHECK: ".$total." \n".
					"CRONJOB START: ".$limit_start." \n".
					"CRONJOB END: ".$limit_end." \n"
					);
			sleep(2);		
			$sendtelegram = $this->telegram_direct("-657527213",$message); //FMS TESTING
			
		}
		
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
		
		printf("========FINISH====== \r\n");
		
		
	}
	
	function getVehicle_fromFMS($no_unit,$rangka)
	{
	
		$nounit = str_replace("-", "", $no_unit);
		$this->db = $this->load->database("default",true);
		$this->db->select("vehicle_id,vehicle_device,vehicle_type,vehicle_name,vehicle_no,vehicle_mv03,vehicle_company,vehicle_dbname_live,vehicle_info");	
		$this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_no", $nounit);
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
	
	function getMaterial_info($rom_name)
	{
	
		$this->db = $this->load->database("default",true);
		$this->db->select("material_id,material_hauling,material_coal,material_geofence");	
		$this->db->order_by("material_reg_date", "asc");
		$this->db->where("material_geofence", $rom_name);
		$this->db->limit(1);
		$q = $this->db->get("master_material");
		$rows = $q->row();
		$total_rows = count($rows);
		
		if($total_rows > 0){
			$data_material = $rows;
			return $data_material;
		}else{
			return false;
		}
		
	}
	
	function getVehicle_byIMEI_fromFMS($imei)
	{

		$this->db = $this->load->database("default",true);
		$this->db->select("vehicle_id,vehicle_device,vehicle_type,vehicle_name,vehicle_no,vehicle_mv03,vehicle_company,vehicle_dbname_live,vehicle_info");	
		$this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $imei."@"."VT200");
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
	
	function get_datapositionDBPORT($rowvehicle,$localtime_start,$localtime_end,$id_data)
	{
	
			//gps tim
			$sdate_gmt = date("Y-m-d H:i:s", strtotime($localtime_start . "-7hours"));
			$edate_gmt = date("Y-m-d H:i:s", strtotime($localtime_end . "-7hours"));
			$sdate = date("Y-m-d H:i:s", strtotime($sdate_gmt . "-45minutes"));
			//$edate = date("Y-m-d H:i:s", strtotime($edate_gmt));
			$edate = date("Y-m-d H:i:s", strtotime($sdate_gmt . "+10minutes"));
			printf("PERIODE GPS TIME %s to %s \r\n",$sdate, $edate);
			//PORT Only
            if (isset($rowvehicle->vehicle_info))
            {
                $json = json_decode($rowvehicle->vehicle_info);
                if (isset($json->vehicle_ip) && isset($json->vehicle_port))
                {
                    
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");				
						$this->dbhist = $this->load->database($database, TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);
										
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);
									
					}
					
					$vehicle_device = explode("@", $rowvehicle->vehicle_device);  
                    $vehicle_no = $rowvehicle->vehicle_no;
					$vehicle_dev = $rowvehicle->vehicle_device;
					$vehicle_name = $rowvehicle->vehicle_name;
					$vehicle_type = $rowvehicle->vehicle_type;
						
					if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE")
                    {
                        $tablehist = $vehicle_device[0]."@t5_gps";
                        $tablehistinfo = $vehicle_device[0]."@t5_info";    
                    }
                    else
                    {
						$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
						$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                    }
					
						$this->dbhist->select("gps_name,gps_time,gps_latitude_real,gps_longitude_real,gps_speed");
						$this->dbhist->order_by("gps_time","desc");
						$this->dbhist->group_by("gps_time");						
                        $this->dbhist->where("gps_name", $vehicle_device[0]);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
						//$this->dbhist->limit(1);    
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows = $q->result();
						printf("TOTAL DATA LEV #1: %s \r\n",count($rows));
						
						if(count($rows) > 0)
						{
							
							
							$this->dbhist->close();
							$this->dbhist->cache_delete_all();
							return $rows;
						}
						else 
						{
							
							$this->dbhist2->select("gps_name,gps_time,gps_latitude_real,gps_longitude_real,gps_speed");
							$this->dbhist2->order_by("gps_time","desc");
							$this->dbhist2->group_by("gps_time");						
							$this->dbhist2->where("gps_name", $vehicle_device[0]);
							$this->dbhist2->where("gps_time >=", $sdate);
							$this->dbhist2->where("gps_time <=", $edate);
							//$this->dbhist2->limit(1);    
							$this->dbhist2->from($tablehist);
							$q2 = $this->dbhist2->get();
							$rows = $q2->result();
							printf("TOTAL DATA LEV #2 : %s \r\n",count($rows));
							
							if(count($rows) > 0){
								
								$this->dbhist2->close();
								$this->dbhist2->cache_delete_all();
								return $rows;
							}
							
							
						}
                  
						
						
						/*$data["integrationwim_distanceWB_status"] = 3; 
						 $this->dbreport = $this->load->database("tensor_report",TRUE);
						$this->dbreport->where("integrationwim_id", $id_data);
						$this->dbreport->limit(1);
						$this->dbreport->update("historikal_integrationwim_unit",$data); */
						
						return false;
						
				}else{
					
					printf("BUKAN GPS PORT : %s \r\n",$trows);
					return false;
				}
			}
		
		
	}
	
	function get_datapositionDUMPING($rowvehicle,$localtime_start,$localtime_end,$id_data)
	{
	
			//periode start dari endtime data wim + limit time 
			$sdate = date("Y-m-d H:i:s", strtotime($localtime_end . "-7hours"));
			$edate = date("Y-m-d H:i:s", strtotime($sdate . "+60minutes"));
			
			printf("PERIODE GPS TIME %s to %s \r\n",$sdate, $edate); 
			//PORT Only
            if (isset($rowvehicle->vehicle_info))
            {
                $json = json_decode($rowvehicle->vehicle_info);
                if (isset($json->vehicle_ip) && isset($json->vehicle_port))
                {
                    
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");				
						$this->dbhist = $this->load->database($database, TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);
										
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);
									
					}
					
					$vehicle_device = explode("@", $rowvehicle->vehicle_device);  
                    $vehicle_no = $rowvehicle->vehicle_no;
					$vehicle_dev = $rowvehicle->vehicle_device;
					$vehicle_name = $rowvehicle->vehicle_name;
					$vehicle_type = $rowvehicle->vehicle_type;
						
					if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE")
                    {
                        $tablehist = $vehicle_device[0]."@t5_gps";
                        $tablehistinfo = $vehicle_device[0]."@t5_info";    
                    }
                    else
                    {
						$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
						$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                    }
					
						$this->dbhist->select("gps_name,gps_time,gps_latitude_real,gps_longitude_real,gps_speed");
						$this->dbhist->order_by("gps_time","desc");
						$this->dbhist->group_by("gps_time");						
                        $this->dbhist->where("gps_name", $vehicle_device[0]);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);  
						$this->dbhist->where("gps_speed", 0);   
						//$this->dbhist->limit(1);    
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows = $q->result();
						printf("TOTAL DATA LEV #1: %s \r\n",count($rows));
						
						if(count($rows) > 0)
						{
							
							
							$this->dbhist->close();
							$this->dbhist->cache_delete_all();
							return $rows;
						}
						else 
						{
							
							$this->dbhist2->select("gps_name,gps_time,gps_latitude_real,gps_longitude_real,gps_speed");
							$this->dbhist2->order_by("gps_time","desc");
							$this->dbhist2->group_by("gps_time");						
							$this->dbhist2->where("gps_name", $vehicle_device[0]);
							$this->dbhist2->where("gps_time >=", $sdate);
							$this->dbhist2->where("gps_time <=", $edate);
							$this->dbhist2->where("gps_speed", 0);   
							//$this->dbhist2->limit(1);    
							$this->dbhist2->from($tablehist);
							$q2 = $this->dbhist2->get();
							$rows = $q2->result();
							printf("TOTAL DATA LEV #2 : %s \r\n",count($rows));
							
							if(count($rows) > 0){
								
								$this->dbhist2->close();
								$this->dbhist2->cache_delete_all();
								return $rows;
							}
							
							
						}
                  
						return false;
						
				}else{
					
					printf("BUKAN GPS PORT : %s \r\n",$trows);
					return false;
				}
			}
		
		
	}
	
	function get_dataradiusDBLIVE($imei,$dblive,$stime,$etime)
	{

		$this->dblive = $this->load->database($dblive,true);
		$this->dblive->order_by("radius_guest_time","desc");
		$this->dblive->group_by("radius_guest_time");
		$this->dblive->where("radius_guest", $imei);
		$this->dblive->where("radius_guest_time >=",$stime);
		$this->dblive->where("radius_guest_time <=", $etime);
		$q = $this->dblive->get("radius");
        $rows = $q->result();
		$total = count($rows);
		print_r($rows);exit();
		return $rows;
	
		
	}

	function getDistance_radius($latitude1="", $longitude1="", $latitude2="", $longitude2="") 
	{  
	  $earth_radius = 6371;

	  $dLat = deg2rad($latitude2 - $latitude1);  
	  $dLon = deg2rad($longitude2 - $longitude1);  

	  $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
	  $c = 2 * asin(sqrt($a));  
	  $d = $earth_radius * $c;  
	  $data =  round($d,2);
	  printf("===RESULT DISTANCE: %s meter \r\n",$data*1000);
      return $data;
	}
	
	function getPosition_other($longitude, $latitude)
	{
		//$api = $this->config->item('GOOGLE_MAP_API_KEY');
		$api = "AIzaSyCGr6BW7vPItrWq95DxMvL292Kf6jHNA5c"; //lacaktranslog prem
		//$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);
		$georeverse = $this->gpsmodel->getLocation_byGeoCode($latitude, $longitude, $api);

		return $georeverse;
	}
	
	function getOneTimetokenAPI($username,$password,$userid)
	{

		$feature = array();

		$dataJson = file_get_contents("http://gpsdvr.pilartech.co.id/StandardApiAction_login.action?account=".$username."&password=".$password."");

		$data = json_decode($dataJson,true);
		$result = $data["result"];
		$response = "";
		if($result == 0){

			$session_id = $data["JSESSIONID"];
			printf("===LOGIN SUCCESS: %s \r\n", $session_id);
		}else{
			$err_message = $data["message"];
			printf("===LOGIN FAILED: %s \r\n", $err_message);
		}

		$response = $session_id;
		return $response;

	}
	
	function devicestatusapi($sess_id,$imei,$vno)
	{

		$feature = array();
		$host = "MV03";
		$dataJson = file_get_contents("http://gpsdvr.pilartech.co.id/StandardApiAction_getDeviceStatus.action?jsession=".$sess_id."&devIdno=".$imei."&toMap=1&driver=0&language=zh"); //IP baru Local

		$data = json_decode($dataJson,true);
		$result = $data['result'];
		$status = $data['status'][0];
		
		if($result == 0)
		{

			/*

			[0] => Array
                (
                    [id] => 020200360002 		= imei
                    [lc] => 0					= milleage (meter)
                    [dt] => 2					= hard type (1: sd card, 2: hardisk, 3 ssd card)
                    [vid] => 020200360002		= plate number
                    [pt] => 6  					= protocol type
                    [jn] =>						= driver work number
                    [dn] =>						= driver name
                    [sp] => 0					= speed (km/h) (dibagi 10)
                    [abbr] =>
                    [lid] => 0					= bus use, line number
                    [ct] =>						= Valid when lg=2,Compartment temperature (0x06)(Refer to the 808-2019 agreement)
                    [ft] => 0					= factory type
                    [dst] =>					= Front vehicle/pedestrian distance (100ms).
                    [fl] =>						= Road marker identification type
                    [ps] => 22.533209,113.945436= Geographical Position
                    [adas2] =>
                    [rft] =>
                    [dsm1] =>
                    [cet] =>
                    [pss] =>
                    [tsp] => 0					= Bus use, Site status 0- station 1- next stop
                    [rt] =>
                    [bsd1] =>
                    [es] =>
                    [yn] =>
                    [wc] =>
                    [rfd] =>
                    [ls] =>
                    [net] => 3					= Network Type 1 means 3G, 2 means WIFI
                    [adas1] =>
                    [fvs] =>
                    [dsm2] =>
                    [lg] =>
                    [lt] => 0					=Login type:0-linux, 1-windows, 2-web, 3-Android, 4-ios
                    [ios] =>
                    [aq] =>
                    [sn] =>
                    [bsd2] =>
                    [dvt] =>
                    [pk] => 4856				= Parking Time (sec)
                    [mlng] => 113.945436		= map lng
                    [ac] =>						= Audio Type
                    [s3] => 0					= Status 3
                    [t2] => 0					= Temp Sensor 2
                    [lng] => 113945436			= lng
                    [mlat] => 22.533209			= map lng
                    [yl] => 0					= Fuel Unit: L, you must first use divided by 100.
                    [gt] => 2020-09-09 14:59:09.0	= gps time (WIB)
                    [po] =>
                    [sv] =>
                    [sfg] => 0					= Bus use, Site sign 0- site 1- station yard
                    [ol] => 1					= online status (1:online, else offline
                    [snm] => 0					= site index
                    [sst] => 0					= Site status 1 site 0 station
                    [s1] => -2147469949			= status1 :
                    [hx] => 0					= Direction North direction is 0 degrees, clockwise increases, the maximum value of 360 degrees.
                    [t1] => 0					= Temp Sensor 1
                    [or] => 0					= OBD collects engine speed
                    [hv] =>
                    [os] => 0					= OBD capture engine speed
                    [ov] => 0					= OBD collecting battery voltage
                    [ojt] => 0					= OBD collecting battery voltage
                    [t4] => 0					= Temp Sensor 4
                    [lat] => 22533209			= lat
                    [s4] => 0					= status4 : 0= Positioning Type
                    [s2] => 262144				= status2 :
                    [t3] => 0					= Temp Sensor 3
                    [fdt] => 3					= Factory Subtype
                    [drid] => 0					= driver id
                    [dct] => 0					= Line direction 0 Up 1 Down
                    [gw] => G1					= Gateway Server Number
                    [ust] =>
                    [glat] =>
                    [p10] => 0					= ?
                    [glng] =>
                    [ef] => 0					= Additional information flag 0-Bus OBD 1-Video Department 2-UAE School Bus
                    [p7] => 0					= ef=2:Humidity 3 sensor
                    [p8] => 0					= ?
                    [ost] => 0					= OBD acquisition status
                    [p5] => 0			 		= ef=1:Fatigue degree, ef=2: humidity 1 sensor
                    [p6] => 0					= ef=2:Humidity 2 sensor
                    [p9] => 0					= ?
                    [ojm] => 0					= OBD capture throttle position
                    [imei] =>
                    [imsi] =>
                    [p4] => 0					= ef=1:Abnormal driving flag, ef=2: Hard disk 4 type 1sd, 2hd, 3ssd
                    [tp] =>
                    [p1] => 0					= ef=1:video loss flag, ef=2: hard disk 3 status 0 is invalid, 1 exists, 2 does not exist
                    [p3] => 0					= ef=1:Disk error flag, ef=2: Hard disk 4 status 0 is invalid, 1 exists, 2 does not exist
                    [p2] => 0					= ef=1:Video occlusion flag, ef=2: Hard disk 3 type 1sd, 2hd, 3ssd


			*/
					$driIMEI = $status['id'];
					
					$driJn = trim($status['driJn']);
					$driSw = $status['driSw'];
					$driSwStr = $status['driSwStr'];

					
					///* x */print(date("Y-m-d H:i:s")." ".$imei." driJn : ".$driJn."\n");
					///* x */print(date("Y-m-d H:i:s")." ".$imei." driSw : ".$driSw."\n");
					///* x */print(date("Y-m-d H:i:s")." ".$imei." driSwStr : ".$driSwStr."\n");


							$change_imei = trim($driIMEI);
							$change_driver_id = $driJn;

							if($driSwStr == ""){
								$change_driver_time = $driSwStr;
							}else{
								$change_driver_time = date("Y-m-d H:i:s", strtotime($driSwStr." "."-1 hours"));
							}

			printf("===TERDETEKSI ID SIMPER: %s at %s \r\n", $driJn, $driSwStr);
			return $driJn."|".$driSwStr;
			
		}
		else
		{
			printf("===FAILED TO GET RESULT: %s \r\n", $vno);
			
			return false;
			
		}


	}
	
	function telegram_direct($groupid,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        //$url = "http://lacak-mobil.com/telegram/telegram_directpost";
		$url = "http://admintib.buddiyanto.my.id/telegram/telegram_directpost";
        
        $data = array("id" => $groupid, "message" => $message);
        $data_string = json_encode($data);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);                                                           
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);                        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));  
        $result = curl_exec($ch);
        
        if ($result === FALSE) {
            die("Curl failed: " . curL_error($ch));
        }
        echo $result;
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
    }
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
