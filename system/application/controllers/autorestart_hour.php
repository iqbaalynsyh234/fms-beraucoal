<?php
include "base.php";

class Autorestart_hour extends Base {
	var $otherdb;
	function Autorestart_hour()
	{
		parent::Base();	
		$this->load->model("gpsmodel_autosetting");
		$this->load->model("vehiclemodel");
		$this->load->model("smsmodel");
		$this->load->model("gpsmodel");
	}
	
	//gps offline di insert
	function autocheck_hour_data($userid="", $order="asc")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
		$days_report = date("d", strtotime($nowtime_wita));
		$month_report = date("F", strtotime($nowtime_wita));
		$year_report = date("Y", strtotime($nowtime_wita));
		$before_status = 0;
		$year_before = date('Y',strtotime('-1 year',strtotime($year_report)));
		
		$report = "location_hour_";
		$report_ritase = "location_hour_";
		
		switch ($month_report)
		{
			case "January":
            $dbtable = $report."januari_".$year_report;
			$dbtable_ritase = $report_ritase."januari_".$year_report;
			$dbtable_before = $report."desember_".$year_before;
			break;
			case "February":
            $dbtable = $report."februari_".$year_report;
			$dbtable_ritase = $report_ritase."februari_".$year_report;
			$dbtable_before = $report."januari_".$year_report;
			break;
			case "March":
            $dbtable = $report."maret_".$year_report;
			$dbtable_ritase = $report_ritase."maret_".$year_report;
			$dbtable_before = $report."februari_".$year_report;
			break;
			case "April":
            $dbtable = $report."april_".$year_report;
			$dbtable_ritase = $report_ritase."april_".$year_report;
			$dbtable_before = $report."maret_".$year_report;
			break;
			case "May":
            $dbtable = $report."mei_".$year_report;
			$dbtable_ritase = $report_ritase."mei_".$year_report;
			$dbtable_before = $report."april_".$year_report;
			break;
			case "June":
            $dbtable = $report."juni_".$year_report;
			$dbtable_ritase = $report_ritase."juni_".$year_report;
			$dbtable_before = $report."mei_".$year_report;
			break;
			case "July":
            $dbtable = $report."juli_".$year_report;
			$dbtable_ritase = $report_ritase."juli_".$year_report;
			$dbtable_before = $report."juni_".$year_report;
			break;
			case "August":
            $dbtable = $report."agustus_".$year_report;
			$dbtable_ritase = $report_ritase."agustus_".$year_report;
			$dbtable_before = $report."juli_".$year_report;
			break;
			case "September":
            $dbtable = $report."september_".$year_report;
			$dbtable_ritase = $report_ritase."september_".$year_report;
			$dbtable_before = $report."agustus_".$year_report;
			break;
			case "October":
            $dbtable = $report."oktober_".$year_report;
			$dbtable_ritase = $report_ritase."oktober_".$year_report;
			$dbtable_before = $report."september_".$year_report;
			break;
			case "November":
            $dbtable = $report."november_".$year_report;
			$dbtable_ritase = $report_ritase."november_".$year_report;
			$dbtable_before = $report."oktober_".$year_report;
			break;
			case "December":
            $dbtable = $report."desember_".$year_report;
			$dbtable_ritase = $report_ritase."desember_".$year_report;
			$dbtable_before = $report."november_".$year_report;
			break;
		}

		printf("===STARTING AUTOCHECK HOURLY %s WIB %s WITA\r\n", $nowtime, $nowtime_wita); 
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("company_name","asc");
		$this->db->select("company_name,company_id");
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", $userid);
		$q = $this->db->get("company");
		$rows = $q->result();
		$totalcompany = count($rows);
		printf("===TOTAL COMPANY %s \r\n", $totalcompany);
																
		$street_register = $this->config->item('street_onduty_autocheck'); //only all street hauling
		$port_register = $this->config->item('port_register_autocheck'); //port legal
		$rom_register = $this->config->item('rombib_register_autocheck'); // all rom legal 
		$pool_register = $this->config->item('pool_register_autocheck'); // all pool/ws
		
		for ($i=0;$i<$totalcompany;$i++)
		{
			$j = $i+1;
			$total_unit = 0;
			$total_duty = 0;
			$total_duty_persen = 0;
			$total_idle = 0;
			
			printf("===PROCESS COMPANY %s of %s \r\n", $j, $totalcompany);
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id","asc");
			$this->db->select("vehicle_id,vehicle_no,vehicle_device,vehicle_user_id,vehicle_name,vehicle_company,vehicle_mv03,vehicle_type,vehicle_autocheck");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_company", $rows[$i]->company_id);
			$this->db->where("vehicle_user_id", $userid);
			//$this->db->where("vehicle_no", "BKA 1180"); //test
			
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->result();
			$totalvehicle = count($rowvehicle);
			
			for ($x=0;$x<$totalvehicle;$x++)
			{
				$j2 = $x+1;
				printf("===PROCESS VEHICLE %s of %s, %s, %s, %s of %s\r\n", $j2, $totalvehicle, $rowvehicle[$x]->vehicle_no, $rows[$i]->company_name, $j, $totalcompany);
				$json = json_decode($rowvehicle[$x]->vehicle_autocheck);
				//print_r($json); exit();
				
				//grouping insert ke location_hour
				//master data
				$location_report_vehicle_user_id = $rowvehicle[$x]->vehicle_user_id;
				$location_report_vehicle_id = $rowvehicle[$x]->vehicle_id;
				$location_report_vehicle_device = $rowvehicle[$x]->vehicle_device;
				$location_report_imei = $rowvehicle[$x]->vehicle_mv03;
				$location_report_vehicle_no = $rowvehicle[$x]->vehicle_no;
				$location_report_vehicle_name = $rowvehicle[$x]->vehicle_name;
				$location_report_vehicle_type = $rowvehicle[$x]->vehicle_type;
				$location_report_vehicle_company = $rowvehicle[$x]->vehicle_company;
				$location_report_company_name = $this->getCompanyName($location_report_vehicle_company);
				
				//GPS OFFLINE TETAP DIINSERT
				if($json->auto_status == "M")
				{
					
					$location_report_speed = "";
					$location_report_jalur = "";
					$location_report_direction = "";
					$location_report_location = "";
					$location_report_coordinate = "";
					$location_report_odometer = 0;
					$location_report_latitude = "";
					$location_report_longitude = "";
					$location_report_fuel_data = 0;
					$location_report_gsm = "";
					$location_report_sat = "";
					$location_report_auto_status = $json->auto_status; 
					$location_report_engine = "";
					$location_report_gps_time = date("Y-m-d H:i:s", strtotime($nowtime_wita)); //sudah wita
					$location_report_gps_date = date("Y-m-d",strtotime($nowtime_wita));
					$location_report_gps_hour = date("H:00:00",strtotime($nowtime_wita));
					$location_report_gpsstatus = "";
					
					$location_report_geofence_id = "";
					$location_report_geofence_name = "";
					$location_report_geofence_type = "";
					
					$position_name = ""; 
					$hauling = "";
					$group = "OFFLINE";
					$streetname = "";
					$pool_id = 0;
					
					printf("!==GPS OFFLINE %s %s \r\n", $location_report_vehicle_no, $json->auto_last_update);
					
				}
				//GPS DELAY (cek delay jika > 1 jam dari now time WITA maka dianggap offline ( dan di insert )
				else if($json->auto_status == "K")
				{
					$gps_realtime = $json->auto_last_update;
					//delta time gps VS gps now WITA
					$gps_realtime_sec = strtotime($gps_realtime);
					$nowtime_sec = strtotime($nowtime_wita);
					$delta = $nowtime_sec - $gps_realtime_sec;
					$duration = get_time_difference($gps_realtime, $nowtime_wita);
									
					$show = "";
					if($duration[0]!=0)
					{
						$show .= $duration[0] ." Day ";
					}
					if($duration[1]!=0)
					{
						$show .= $duration[1] ." Hour ";
					}
					if($duration[2]!=0)
					{
						 $show .= $duration[2] ." Min ";
					}
					if($show == "")
					{
						$show .= "0 Min";
					}
					
					printf("==GPS DELAY %s %s \r\n", $location_report_vehicle_no, $json->auto_last_update);
					printf("===Delta %s %s \r\n", $delta, $show);
					
					// GPS DELAY > 1 JAM TERMASUK DATA OFFLINE
					if ($delta >= 3600 && $delta <= 86400) //default 1jam(3600) -> 24jam(86400)
					{
						$location_report_speed = "";
						$location_report_jalur = "";
						$location_report_direction = "";
						$location_report_location = "";
						$location_report_coordinate = "";
						$location_report_odometer = 0;
						$location_report_latitude = "";
						$location_report_longitude = "";
						$location_report_fuel_data = 0;
						$location_report_gsm = "";
						$location_report_sat = "";
						$location_report_auto_status = $json->auto_status; 
						$location_report_engine = "";
						$location_report_gps_time = date("Y-m-d H:i:s", strtotime($nowtime_wita)); //sudah wita
						$location_report_gps_date = date("Y-m-d",strtotime($nowtime_wita));
						$location_report_gps_hour = date("H:00:00",strtotime($nowtime_wita));
						$location_report_gpsstatus = "";
						
						$location_report_geofence_id = "";
						$location_report_geofence_name = "";
						$location_report_geofence_type = "";
						
						$position_name = ""; 
						$hauling = "";
						$group = "OFFLINE";
						$streetname = "";
						$pool_id = 0;
					}
					// GPS DELAY < 1 JAM
					else
					{
						$location_report_speed = $json->auto_last_speed;
						$location_report_jalur = $json->auto_last_road;
						$location_report_direction = $json->auto_last_course;
						$location_report_location = $json->auto_last_position;
						$location_report_coordinate = $json->auto_last_lat.",".$json->auto_last_long;
						$location_report_odometer = 0;
						$location_report_latitude = $json->auto_last_lat;
						$location_report_longitude = $json->auto_last_long;
						$location_report_fuel_data = 0;
						$location_report_gsm = "";
						$location_report_sat = "";
						$location_report_auto_status = $json->auto_status; 
						$location_report_engine = $json->auto_last_engine;
						$location_report_gps_time = date("Y-m-d H:i:s", strtotime($json->auto_last_update)); //sudah wita
						$location_report_gps_date = date("Y-m-d",strtotime($json->auto_last_update));
						$location_report_gps_hour = date("H:00:00",strtotime($json->auto_last_update));
						$location_report_gpsstatus = $json->auto_last_gpsstatus;
						
						$location_report_geofence_id = "";
						$location_report_geofence_name = "";
						$location_report_geofence_type = "";
									
						$pool_id = 0;
						$ex_lastposition = explode(",",$location_report_location);
										
						$position_name = $ex_lastposition[0]; 
							if (in_array($position_name, $street_register)){
								$hauling = "in";
								$group = "STREET";
							}else if(in_array($position_name, $port_register)){
								$hauling = "in";
								$group = "PORT";
							}else if(in_array($position_name, $rom_register)){
								$hauling = "in";
								$group = "ROM";
							}else if(in_array($position_name, $pool_register)){
								$hauling = "in";
								$group = "POOL";
								$streetname = $position_name.",";
								$pool_id = $this->get_street_id($streetname,4408);
								printf("===POOL NAME %s \r\n", $position_name);
								printf("===POOL ID %s \r\n", $pool_id);
							}else{
								$group = "OUT";
								$hauling = "out";
							}
						
					}
				
				}
				//GPS UPDATE (BUKAN STATUS K DAN M )
				else
				{
					$location_report_speed = $json->auto_last_speed;
					$location_report_jalur = $json->auto_last_road;
					$location_report_direction = $json->auto_last_course;
					$location_report_location = $json->auto_last_position;
					$location_report_coordinate = $json->auto_last_lat.",".$json->auto_last_long;
					$location_report_odometer = 0;
					$location_report_latitude = $json->auto_last_lat;
					$location_report_longitude = $json->auto_last_long;
					$location_report_fuel_data = 0;
					$location_report_gsm = "";
					$location_report_sat = "";
					$location_report_auto_status = $json->auto_status; 
					$location_report_engine = $json->auto_last_engine;
					$location_report_gps_time = date("Y-m-d H:i:s", strtotime($json->auto_last_update)); //sudah wita
					$location_report_gps_date = date("Y-m-d",strtotime($json->auto_last_update));
					$location_report_gps_hour = date("H:00:00",strtotime($json->auto_last_update));
					$location_report_gpsstatus = $json->auto_last_gpsstatus;
					
					$location_report_geofence_id = "";
					$location_report_geofence_name = "";
					$location_report_geofence_type = "";
								
					$pool_id = 0;
					$ex_lastposition = explode(",",$location_report_location);
									
					$position_name = $ex_lastposition[0]; 
						if (in_array($position_name, $street_register)){
							$hauling = "in";
							$group = "STREET";
						}else if(in_array($position_name, $port_register)){
							$hauling = "in";
							$group = "PORT";
						}else if(in_array($position_name, $rom_register)){
							$hauling = "in";
							$group = "ROM";
						}else if(in_array($position_name, $pool_register)){
							$hauling = "in";
							$group = "POOL";
							$streetname = $position_name.",";
							$pool_id = $this->get_street_id($streetname,4408);
							printf("===POOL NAME %s \r\n", $position_name);
							printf("===POOL ID %s \r\n", $pool_id);
						}else{
							$group = "OUT";
							$hauling = "out";
						}
				}
				
								unset($datainsert);
								$datainsert["location_report_vehicle_user_id"] = $location_report_vehicle_user_id;
								$datainsert["location_report_vehicle_id"] = $location_report_vehicle_id;
								$datainsert["location_report_vehicle_device"] = $location_report_vehicle_device;
								$datainsert["location_report_imei"] = $location_report_imei;
								$datainsert["location_report_vehicle_no"] = $location_report_vehicle_no;
								$datainsert["location_report_vehicle_name"] = $location_report_vehicle_name;
								$datainsert["location_report_vehicle_type"] = $location_report_vehicle_type;
								$datainsert["location_report_vehicle_company"] = $location_report_vehicle_company;
								$datainsert["location_report_company_name"] = $location_report_company_name;
								
								$datainsert["location_report_speed"] = $location_report_speed;
								$datainsert["location_report_gpsstatus"] = $location_report_gpsstatus;
								$datainsert["location_report_gps_time"] = $location_report_gps_time;
								$datainsert["location_report_gps_date"] = $location_report_gps_date;
								$datainsert["location_report_gps_hour"] = $location_report_gps_hour;
								$datainsert["location_report_geofence_id"] = $location_report_geofence_id;
								$datainsert["location_report_geofence_name"] = $location_report_geofence_name;
								$datainsert["location_report_geofence_type"] = $location_report_geofence_type;
								$datainsert["location_report_jalur"] = $location_report_jalur;
								$datainsert["location_report_direction"] = $location_report_direction;
								$datainsert["location_report_location"] = $position_name;
								$datainsert["location_report_coordinate"] = $location_report_coordinate;
								$datainsert["location_report_latitude"] = $location_report_latitude;
								$datainsert["location_report_longitude"] = $location_report_longitude;
								$datainsert["location_report_odometer"] = $location_report_odometer;
								$datainsert["location_report_fuel_data"] = $location_report_fuel_data;
								$datainsert["location_report_gsm"] = $location_report_gsm;
								$datainsert["location_report_sat"] = $location_report_sat;
								$datainsert["location_report_auto_status"] = $location_report_auto_status;
								$datainsert["location_report_engine"] = $location_report_engine;
								
								$datainsert["location_report_hauling"] = $hauling;
								$datainsert["location_report_group"] = $group;
								$datainsert["location_report_street_id"] = $pool_id;
								
								//get last data
								$this->dbts = $this->load->database("tensor_report",true); 
								$this->dbts->select("location_report_id");
								$this->dbts->where("location_report_vehicle_id", $location_report_vehicle_id);
								$this->dbts->where("location_report_gps_date",$location_report_gps_date);
								$this->dbts->where("location_report_gps_hour",$location_report_gps_hour);
								//$this->dbts->limit(1);
								$q_last = $this->dbts->get($dbtable);
								$row_last = $q_last->row();
								$total_last = count($row_last);
								
								if($total_last>0){
									$this->dbts = $this->load->database("tensor_report",true); 
									$this->dbts->where("location_report_vehicle_id", $location_report_vehicle_id);
									$this->dbts->where("location_report_gps_date",$location_report_gps_date);
									$this->dbts->where("location_report_gps_hour",$location_report_gps_hour);
									//$this->dbts->limit(1);
									$this->dbts->update($dbtable,$datainsert);
									printf("!==UPDATE OK \r\n ");
								}else{
									
									$this->dbts->insert($dbtable,$datainsert);
									printf("===INSERT OK \r\n");
								}
			}
			
			
		}
		$finishtime = date("Y-m-d H:i:s");
		
		$starttime_sec = strtotime($nowtime);
		$finishtime_sec = strtotime($finishtime);
		$delta = $finishtime_sec - $starttime_sec;
		$duration = get_time_difference($finishtime, $nowtime);
									
		$show = "";
		if($duration[0]!=0)
		{
			$show .= $duration[0] ." Day ";
		}
		if($duration[1]!=0)
		{
			$show .= $duration[1] ." Hour ";
		}
		if($duration[2]!=0)
		{
			 $show .= $duration[2] ." Min ";
		}
		if($show == "")
		{
			$show .= "0 Min";
		}
		
		$latency = $duration;
		
		//send telegram
		$title_name = "AUTOCHECK LOCATION HOUR NEW";
			$message = urlencode(
					"".$title_name." \n".
					"Date: ".$nowdate." \n".
					"Start Time: ".$nowtime." \n".
					"End Time: ".$finishtime." \n"
					//"Latency: ".$latency." \n"
				);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-969226710",$message); //FMS LOCATION HOUR
		//$sendtelegram = $this->telegram_direct("-742300146",$message); //telegram FMS AUTOCHECK
		printf("===SENT TELEGRAM OK\r\n");
		
		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH AUTOCHECK HOUR DATA %s to %s \r\n", $nowdate, $enddate);
		printf("============================== \r\n");

	}
	
	function autocheck_hour_data_bk($userid="", $order="asc")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
		printf("===STARTING AUTOCHECK HOURLY %s WIB %s WITA\r\n", $nowtime, $nowtime_wita);
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("company_name","asc");
		$this->db->select("company_name,company_id");
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", $userid);
		$q = $this->db->get("company");
		$rows = $q->result();
		$totalcompany = count($rows);
		printf("===TOTAL COMPANY %s \r\n", $totalcompany);
		
		
		/*
		$street_register = array(	"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
														
															"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
															
															"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
															"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
															"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
															"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31",
															
															"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
															"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
															"PORT BIB - Antrian","Port BIB - Antrian"
														);
														
								$port_register = array(	"BIB CP 1","BIB CP 7","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 2","BIB CP 6",
														"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
														"PORT BIB","PORT BIR","PORT TIA"
																				   
														);
																				   
								$rom_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom","Non BIB Anzawara","Non BIB TBR/SDJ"
										  
														  );
																			
								$pool_register = array( "POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
														"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT"
														);
		*/
														
		$street_register = $this->config->item('street_onduty_autocheck'); //only all street hauling
		$port_register = $this->config->item('port_register_autocheck'); //port legal
		$rom_register = $this->config->item('rombib_register_autocheck'); // all rom legal 
		$pool_register = $this->config->item('pool_register_autocheck'); // all pool/ws
		

		for ($i=0;$i<$totalcompany;$i++)
		{
			$j = $i+1;
			$total_unit = 0;
			$total_duty = 0;
			$total_duty_persen = 0;
			$total_idle = 0;
			
			printf("===PROCESS COMPANY %s of %s \r\n", $j, $totalcompany);
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id","asc");
			$this->db->select("vehicle_id,vehicle_no,vehicle_device,vehicle_user_id,vehicle_name,vehicle_company,vehicle_mv03,vehicle_type,vehicle_autocheck");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_company", $rows[$i]->company_id);
			$this->db->where("vehicle_user_id", $userid);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->result();
			$totalvehicle = count($rowvehicle);
			
			for ($x=0;$x<$totalvehicle;$x++)
			{
				$j2 = $x+1;
				printf("===PROCESS VEHICLE %s of %s, %s, %s, %s of %s\r\n", $j2, $totalvehicle, $rowvehicle[$x]->vehicle_no, $rows[$i]->company_name, $j, $totalcompany);
				$json = json_decode($rowvehicle[$x]->vehicle_autocheck);
				//print_r($json); exit();
			
				//grouping insert ke location_hour
				
								$location_report_vehicle_user_id = $rowvehicle[$x]->vehicle_user_id;
								$location_report_vehicle_id = $rowvehicle[$x]->vehicle_id;
								$location_report_vehicle_device = $rowvehicle[$x]->vehicle_device;
								$location_report_imei = $rowvehicle[$x]->vehicle_mv03;
								$location_report_vehicle_no = $rowvehicle[$x]->vehicle_no;
								$location_report_vehicle_name = $rowvehicle[$x]->vehicle_name;
								$location_report_vehicle_type = $rowvehicle[$x]->vehicle_type;
								$location_report_vehicle_company = $rowvehicle[$x]->vehicle_company;
								$location_report_company_name = $this->getCompanyName($location_report_vehicle_company);
								$location_report_speed = $json->auto_last_speed;
								$location_report_gpsstatus = $json->auto_last_gpsstatus;
								$location_report_gps_time = date("Y-m-d H:i:s", strtotime($json->auto_last_update)); //sudah wita
								$location_report_gps_date = date("Y-m-d",strtotime($json->auto_last_update));
								$location_report_gps_hour = date("H:00:00",strtotime($json->auto_last_update));
								$location_report_geofence_id = "";
								$location_report_geofence_name = "";
								$location_report_geofence_type = "";
								$location_report_jalur = $json->auto_last_road;
								$location_report_direction = $json->auto_last_course;
								$location_report_location = $json->auto_last_position;
								$location_report_coordinate = $json->auto_last_lat.",".$json->auto_last_long;
								$location_report_odometer = 0;
								$location_report_latitude = $json->auto_last_lat;
								$location_report_longitude = $json->auto_last_long;
								$location_report_fuel_data = 0;
								$location_report_gsm = "";
								$location_report_sat = "";
								$location_report_auto_status = $json->auto_status; 
								$location_report_engine = $json->auto_last_engine;
								$pool_id = 0;
									$ex_lastposition = explode(",",$location_report_location);
									
									$position_name = $ex_lastposition[0]; 
									if (in_array($position_name, $street_register)){
										$hauling = "in";
										$group = "STREET";
									}else if(in_array($position_name, $port_register)){
										$hauling = "in";
										$group = "PORT";
									}else if(in_array($position_name, $rom_register)){
										$hauling = "in";
										$group = "ROM";
									}else if(in_array($position_name, $pool_register)){
										$hauling = "in";
										$group = "POOL";
										$streetname = $position_name.",";
										$pool_id = $this->get_street_id($streetname,4408);
										printf("===POOL NAME %s \r\n", $position_name);
										printf("===POOL ID %s \r\n", $pool_id);
									}else{
										$group = "OUT";
										$hauling = "out";
									}
									
									
								
								
								unset($datainsert);
								$datainsert["location_report_vehicle_user_id"] = $location_report_vehicle_user_id;
								$datainsert["location_report_vehicle_id"] = $location_report_vehicle_id;
								$datainsert["location_report_vehicle_device"] = $location_report_vehicle_device;
								$datainsert["location_report_imei"] = $location_report_imei;
								$datainsert["location_report_vehicle_no"] = $location_report_vehicle_no;
								$datainsert["location_report_vehicle_name"] = $location_report_vehicle_name;
								$datainsert["location_report_vehicle_type"] = $location_report_vehicle_type;
								$datainsert["location_report_vehicle_company"] = $location_report_vehicle_company;
								$datainsert["location_report_company_name"] = $location_report_company_name;
								
								$datainsert["location_report_speed"] = $location_report_speed;
								$datainsert["location_report_gpsstatus"] = $location_report_gpsstatus;
								$datainsert["location_report_gps_time"] = $location_report_gps_time;
								$datainsert["location_report_gps_date"] = $location_report_gps_date;
								$datainsert["location_report_gps_hour"] = $location_report_gps_hour;
								$datainsert["location_report_geofence_id"] = $location_report_geofence_id;
								$datainsert["location_report_geofence_name"] = $location_report_geofence_name;
								$datainsert["location_report_geofence_type"] = $location_report_geofence_type;
								$datainsert["location_report_jalur"] = $location_report_jalur;
								$datainsert["location_report_direction"] = $location_report_direction;
								$datainsert["location_report_location"] = $position_name;
								$datainsert["location_report_coordinate"] = $location_report_coordinate;
								$datainsert["location_report_latitude"] = $location_report_latitude;
								$datainsert["location_report_longitude"] = $location_report_longitude;
								$datainsert["location_report_odometer"] = $location_report_odometer;
								$datainsert["location_report_fuel_data"] = $location_report_fuel_data;
								$datainsert["location_report_gsm"] = $location_report_gsm;
								$datainsert["location_report_sat"] = $location_report_sat;
								$datainsert["location_report_auto_status"] = $location_report_auto_status;
								$datainsert["location_report_engine"] = $location_report_engine;
								
								$datainsert["location_report_hauling"] = $hauling;
								$datainsert["location_report_group"] = $group;
								$datainsert["location_report_street_id"] = $pool_id;
								
								//get last data
								$this->dbts = $this->load->database("webtracking_ts",true); 
								$this->dbts->select("location_report_id");
								$this->dbts->where("location_report_vehicle_id", $location_report_vehicle_id);
								$this->dbts->where("location_report_gps_date",$location_report_gps_date);
								$this->dbts->where("location_report_gps_hour",$location_report_gps_hour);
								//$this->dbts->limit(1);
								$q_last = $this->dbts->get("ts_location_hour");
								$row_last = $q_last->row();
								$total_last = count($row_last);
								
								if($total_last>0){
									$this->dbts = $this->load->database("webtracking_ts",true); 
									$this->dbts->where("location_report_vehicle_id", $location_report_vehicle_id);
									$this->dbts->where("location_report_gps_date",$location_report_gps_date);
									$this->dbts->where("location_report_gps_hour",$location_report_gps_hour);
									//$this->dbts->limit(1);
									$this->dbts->update("ts_location_hour",$datainsert);
									printf("!==UPDATE OK \r\n ");
								}else{
									
									$this->dbts->insert("ts_location_hour",$datainsert);
									printf("===INSERT OK \r\n");
								}
			}
			
			
		}
		$finishtime = date("Y-m-d H:i:s");
		
		//send telegram
		$title_name = "AUTOCHECK DATA PER HOUR";
			$message = urlencode(
					"".$title_name." \n".
					"Date: ".$nowdate." \n".
					"Start Time: ".$nowtime." \n".
					"End Time: ".$finishtime." \n"
				);
		sleep(2);		
		//$sendtelegram = $this->telegram_direct("-671321211",$message); //autocheck hour
		$sendtelegram = $this->telegram_direct("-742300146",$message); //telegram FMS AUTOCHECK
		printf("===SENT TELEGRAM OK\r\n");
		
		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH AUTOCHECK HOUR DATA %s to %s \r\n", $nowdate, $enddate);
		printf("============================== \r\n");

	}
	
	function create_manual($userid="", $comid="", $date="", $hour="", $order="asc")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
		$days_report = date("d", strtotime($date));
		$month_report = date("F", strtotime($date));
		$year_report = date("Y", strtotime($date));
		$before_status = 0;
		$year_before = date('Y',strtotime('-1 year',strtotime($year_report)));
		
		$report = "location_hour_";
		$report_ritase = "location_hour_";
		
		switch ($month_report)
		{
			case "January":
            $dbtable = $report."januari_".$year_report;
			$dbtable_ritase = $report_ritase."januari_".$year_report;
			$dbtable_before = $report."desember_".$year_before;
			break;
			case "February":
            $dbtable = $report."februari_".$year_report;
			$dbtable_ritase = $report_ritase."februari_".$year_report;
			$dbtable_before = $report."januari_".$year_report;
			break;
			case "March":
            $dbtable = $report."maret_".$year_report;
			$dbtable_ritase = $report_ritase."maret_".$year_report;
			$dbtable_before = $report."februari_".$year_report;
			break;
			case "April":
            $dbtable = $report."april_".$year_report;
			$dbtable_ritase = $report_ritase."april_".$year_report;
			$dbtable_before = $report."maret_".$year_report;
			break;
			case "May":
            $dbtable = $report."mei_".$year_report;
			$dbtable_ritase = $report_ritase."mei_".$year_report;
			$dbtable_before = $report."april_".$year_report;
			break;
			case "June":
            $dbtable = $report."juni_".$year_report;
			$dbtable_ritase = $report_ritase."juni_".$year_report;
			$dbtable_before = $report."mei_".$year_report;
			break;
			case "July":
            $dbtable = $report."juli_".$year_report;
			$dbtable_ritase = $report_ritase."juli_".$year_report;
			$dbtable_before = $report."juni_".$year_report;
			break;
			case "August":
            $dbtable = $report."agustus_".$year_report;
			$dbtable_ritase = $report_ritase."agustus_".$year_report;
			$dbtable_before = $report."juli_".$year_report;
			break;
			case "September":
            $dbtable = $report."september_".$year_report;
			$dbtable_ritase = $report_ritase."september_".$year_report;
			$dbtable_before = $report."agustus_".$year_report;
			break;
			case "October":
            $dbtable = $report."oktober_".$year_report;
			$dbtable_ritase = $report_ritase."oktober_".$year_report;
			$dbtable_before = $report."september_".$year_report;
			break;
			case "November":
            $dbtable = $report."november_".$year_report;
			$dbtable_ritase = $report_ritase."november_".$year_report;
			$dbtable_before = $report."oktober_".$year_report;
			break;
			case "December":
            $dbtable = $report."desember_".$year_report;
			$dbtable_ritase = $report_ritase."desember_".$year_report;
			$dbtable_before = $report."november_".$year_report;
			break;
		}

		
		printf("===STARTING AUTOCHECK HOURLY %s WIB %s WITA\r\n", $nowtime, $nowtime_wita);
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("company_name","asc");
		$this->db->select("company_name,company_id");
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", $userid);
		//$this->db->where("company_id <>", "1834");
		if($comid != "all"){
			$this->db->where("company_id",$comid);
		}
		
		$q = $this->db->get("company");
		$rows = $q->result();
		$totalcompany = count($rows);
		printf("===TOTAL COMPANY %s \r\n", $totalcompany);
														
		$street_register = $this->config->item('street_onduty_autocheck'); //only all street hauling
		$port_register = $this->config->item('port_register_autocheck'); //port legal
		$rom_register = $this->config->item('rombib_register_autocheck'); // all rom legal 
		$pool_register = $this->config->item('pool_register_autocheck'); // all pool/ws
		
		$report_datetime = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($date." ".$hour)));
		printf("===PERIODE NEXT HOUR %s \r\n", $report_datetime); 
		
		$next_hour = date("H:i:s",strtotime($report_datetime));
		$next_date = date("Y-m-d",strtotime($report_datetime));
		

		for ($i=0;$i<$totalcompany;$i++)
		{
			$j = $i+1;
			$total_unit = 0;
			$total_duty = 0;
			$total_duty_persen = 0;
			$total_idle = 0;
			
			printf("===PROCESS COMPANY %s of %s \r\n", $j, $totalcompany);
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id","asc");
			$this->db->select("vehicle_id,vehicle_no,vehicle_device,vehicle_user_id,vehicle_name,vehicle_company,vehicle_mv03,vehicle_type,vehicle_autocheck");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_company", $rows[$i]->company_id);
			$this->db->where("vehicle_user_id", $userid);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->result();
			$totalvehicle = count($rowvehicle);
			
			for ($x=0;$x<$totalvehicle;$x++)
			{
				$j2 = $x+1;
				printf("===PROCESS VEHICLE %s of %s, %s, %s, %s, %s, %s, %s of %s\r\n", $j2, $totalvehicle, $rowvehicle[$x]->vehicle_no, $rows[$i]->company_name, $date, $hour, $dbtable, $j, $totalcompany);
				
				$data_nexthour = $this->getLocationHour_selected($rowvehicle[$x]->vehicle_id,$next_date,$next_hour,$dbtable);
				if(count($data_nexthour)> 0)
				{
					
				
								$location_report_vehicle_user_id = $data_nexthour->location_report_vehicle_user_id;
								$location_report_vehicle_id = $data_nexthour->location_report_vehicle_id;
								$location_report_vehicle_device = $data_nexthour->location_report_vehicle_device;
								$location_report_imei = $data_nexthour->location_report_imei;
								$location_report_vehicle_no = $data_nexthour->location_report_vehicle_no;
								$location_report_vehicle_name = $data_nexthour->location_report_vehicle_name;
								$location_report_vehicle_type = $data_nexthour->location_report_vehicle_type;
								$location_report_vehicle_company = $data_nexthour->location_report_vehicle_company;
								$location_report_company_name = $data_nexthour->location_report_company_name;
								$location_report_gpsstatus = $data_nexthour->location_report_gpsstatus;
								$location_report_group = $data_nexthour->location_report_group;
								
								if($data_nexthour->location_report_engine == "ON"){
									$location_report_speed = $data_nexthour->location_report_speed+1;
									$location_report_engine = "ON";
								}else{
									$location_report_speed = $data_nexthour->location_report_speed;
									$location_report_engine = $data_nexthour->location_report_engine;
								}
								
								$location_report_gps_time_create = date('Y-m-d H:i:s',strtotime('-1 hours',strtotime($data_nexthour->location_report_gps_time)));
								$location_report_gps_time = date('Y-m-d H:i:s',strtotime('-317 seconds',strtotime($location_report_gps_time_create)));
								
								$location_report_gps_date = date("Y-m-d",strtotime($location_report_gps_time));
								$location_report_gps_hour = date("H:00:00",strtotime($location_report_gps_time));
								
								printf("===NEXT DATETIME %s CREATE TIME %s \r\n", $location_report_gps_time, $location_report_gps_time_create);
								printf("===DATE %s HOUR %s \r\n", $location_report_gps_date, $location_report_gps_hour);
								
								$location_report_geofence_id = "";
								$location_report_geofence_name = "";
								$location_report_geofence_type = "";
								
								$location_report_jalur = $data_nexthour->location_report_jalur;
								$location_report_direction = $data_nexthour->location_report_direction-17;
								
								$position_name = $data_nexthour->location_report_location;
								
								$street_register = $this->config->item('street_only_autocheck'); //only all street hauling
								$port_register = $this->config->item('port_register_autocheck'); //port legal
								$rom_register = $this->config->item('rombib_register_autocheck'); // all rom legal 
								$pool_register = $this->config->item('pool_register_autocheck'); // all pool/ws
								
								
								//jika next data di pool maka isi di pool juga (sama lokasi)
								if(in_array($position_name, $pool_register))
								{
									$new_location_rule = $position_name;
								}
								else if(in_array($position_name, $rom_register))
								{
									$new_location_rule = $position_name;
									
								}
								else if(in_array($position_name, $street_register))
								{
									$new_location_rule = $position_name;
									
								}
								else if(in_array($position_name, $port_register))
								{
									$new_location_rule = "KM 1.5";
									
								}else{
									$new_location_rule = "";
								}
								
								$location_report_location = $new_location_rule;
								
								$location_report_coordinate = $data_nexthour->location_report_coordinate;
								$location_report_odometer = 0;
								$location_report_latitude = $data_nexthour->location_report_latitude;
								$location_report_longitude = $data_nexthour->location_report_longitude;
								$location_report_fuel_data = 0;
								$location_report_gsm = "";
								$location_report_sat = "";
								$location_report_auto_status = $data_nexthour->location_report_auto_status;
								
									$pool_id = 0;
									$ex_lastposition = explode(",",$location_report_location);
									
									$position_name = $ex_lastposition[0]; 
									if (in_array($position_name, $street_register)){
										$hauling = "in";
										$group = "STREET";
									}else if(in_array($position_name, $port_register)){
										$hauling = "in";
										$group = "PORT";
									}else if(in_array($position_name, $rom_register)){
										$hauling = "in";
										$group = "ROM";
									}else if(in_array($position_name, $pool_register)){
										$hauling = "in";
										$group = "POOL";
										$streetname = $position_name.",";
										$pool_id = $this->get_street_id($streetname,4408);
										printf("===POOL NAME %s \r\n", $position_name);
										printf("===POOL ID %s \r\n", $pool_id);
									}
									
									if($location_report_group == "OFFLINE"){
										
										$group = "OFFLINE";
										$hauling = "";
									}
									
								unset($datainsert);
								$datainsert["location_report_vehicle_user_id"] = $location_report_vehicle_user_id;
								$datainsert["location_report_vehicle_id"] = $location_report_vehicle_id;
								$datainsert["location_report_vehicle_device"] = $location_report_vehicle_device;
								$datainsert["location_report_imei"] = $location_report_imei;
								$datainsert["location_report_vehicle_no"] = $location_report_vehicle_no;
								$datainsert["location_report_vehicle_name"] = $location_report_vehicle_name;
								$datainsert["location_report_vehicle_type"] = $location_report_vehicle_type;
								$datainsert["location_report_vehicle_company"] = $location_report_vehicle_company;
								$datainsert["location_report_company_name"] = $location_report_company_name;
								
								$datainsert["location_report_speed"] = $location_report_speed;
								$datainsert["location_report_gpsstatus"] = $location_report_gpsstatus;
								$datainsert["location_report_gps_time"] = $location_report_gps_time;
								$datainsert["location_report_gps_date"] = $location_report_gps_date;
								$datainsert["location_report_gps_hour"] = $location_report_gps_hour;
								$datainsert["location_report_geofence_id"] = $location_report_geofence_id;
								$datainsert["location_report_geofence_name"] = $location_report_geofence_name;
								$datainsert["location_report_geofence_type"] = $location_report_geofence_type;
								$datainsert["location_report_jalur"] = $location_report_jalur;
								$datainsert["location_report_direction"] = $location_report_direction;
								$datainsert["location_report_location"] = $position_name;
								$datainsert["location_report_coordinate"] = $location_report_coordinate;
								$datainsert["location_report_latitude"] = $location_report_latitude;
								$datainsert["location_report_longitude"] = $location_report_longitude;
								$datainsert["location_report_odometer"] = $location_report_odometer;
								$datainsert["location_report_fuel_data"] = $location_report_fuel_data;
								$datainsert["location_report_gsm"] = $location_report_gsm;
								$datainsert["location_report_sat"] = $location_report_sat;
								$datainsert["location_report_auto_status"] = $location_report_auto_status;
								$datainsert["location_report_engine"] = $location_report_engine;
								
								$datainsert["location_report_hauling"] = $hauling;
								$datainsert["location_report_group"] = $group;
								$datainsert["location_report_street_id"] = $pool_id;
								
								//get last data
								$this->dbts = $this->load->database("tensor_report",true); 
								$this->dbts->select("location_report_id");
								$this->dbts->where("location_report_vehicle_id", $location_report_vehicle_id);
								$this->dbts->where("location_report_gps_date",$location_report_gps_date);
								$this->dbts->where("location_report_gps_hour",$location_report_gps_hour);
								$this->dbts->limit(1);
								$q_last = $this->dbts->get($dbtable);
								$row_last = $q_last->row();
								$total_last = count($row_last);
								
								if($total_last>0){
									$this->dbts = $this->load->database("tensor_report",true); 
									$this->dbts->where("location_report_vehicle_id", $location_report_vehicle_id);
									$this->dbts->where("location_report_gps_date",$location_report_gps_date);
									$this->dbts->where("location_report_gps_hour",$location_report_gps_hour);
									$this->dbts->limit(1);
									$this->dbts->update($dbtable,$datainsert);
									printf("!==UPDATE OK \r\n ");
								}else{
									
									$this->dbts->insert($dbtable,$datainsert);
									printf("===INSERT OK \r\n");
								}
								
				}
				else
				{
					printf("===NO DATA NEXT LOCATION HOUR \r\n");
				}
			}
		}
		$finishtime = date("Y-m-d H:i:s");
		
		//send telegram
		$title_name = "AUTOCHECK DATA PER HOUR RECREATE";
			$message = urlencode(
					"".$title_name." \n".
					"Date: ".$nowdate." \n".
					"Start Time: ".$nowtime." \n".
					"End Time: ".$finishtime." \n"
				);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-671321211",$message); //autocheck hour
		printf("===SENT TELEGRAM OK\r\n");
		
		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH AUTOCHECK HOUR DATA %s to %s \r\n", $nowdate, $enddate);
		printf("============================== \r\n");

	}
	
	function ritase_hour($userid="", $order="asc")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
		$startdate = date("Y-m-d");
		$starthour = "00:00:00";
		$enddate = date("Y-m-d");
		$endhour = "23:59:59";
		
		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate." ".$starthour))); //wita
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate." ".$endhour)));  //wita
		
		
		printf("===STARTING AUTOCHECK HOURLY %s WIB %s WITA\r\n", $nowtime, $nowtime_wita);
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("company_name","asc");
		$this->db->select("company_name,company_id");
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", $userid);
		$q = $this->db->get("company");
		$rows = $q->result();
		$totalcompany = count($rows);
		printf("===TOTAL COMPANY %s \r\n", $totalcompany);
		
		for ($i=0;$i<$totalcompany;$i++)
		{
			$j = $i+1;
			$total_unit = 0;
			$total_duty = 0;
			$total_duty_persen = 0;
			$total_idle = 0;
			
			printf("===PROCESS COMPANY %s of %s \r\n", $j, $totalcompany);
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id","asc");
			$this->db->select("vehicle_id,vehicle_no,vehicle_device,vehicle_user_id,vehicle_name,vehicle_company,vehicle_mv03,vehicle_type,vehicle_dbname_live");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_company", $rows[$i]->company_id);
			$this->db->where("vehicle_user_id", $userid);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->result();
			$totalvehicle = count($rowvehicle);
			
			for ($x=0;$x<$totalvehicle;$x++)
			{
				$j2 = $x+1;
				printf("===SDATE %s EDATE %s \r\n", $sdate, $edate);
				printf("===PROCESS VEHICLE %s of %s, %s, %s, %s of %s\r\n", $j2, $totalvehicle, $rowvehicle[$x]->vehicle_no, $rows[$i]->company_name, $j, $totalcompany);
						$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);
						
						$this->dblive = $this->load->database($rowvehicle[$x]->vehicle_dbname_live,true);
						$this->dblive->order_by("ritase_gpstime","asc");
						$this->dblive->group_by("ritase_last_site_datetime");
                        $this->dblive->where("ritase_device", $vehicle_device[0]);
                        $this->dblive->where("ritase_last_site_datetime >=", $sdate);
                        $this->dblive->where("ritase_last_site_datetime <=", $edate);
						$this->dblive->from("ritase");
                        $q_live = $this->dblive->get();
                        $rows_live = $q_live->result();
						$trows_live = count($rows_live);
						printf("TOTAL DATA DBLIVE : %s \r\n",$trows_live);
						
						//detail data
						if ($trows_live > 0)
						{
							for($z=0;$z<$trows_live;$z++)
							{
								$position_start_name =  $rows_live[$z]->ritase_last_site;
								$geofence_start_name =  $rows_live[$z]->ritase_last_site;

								$position_end_name = $rows_live[$z]->ritase_last_dest;
								$geofence_end_name = $rows_live[$z]->ritase_last_dest;

								$ritase_report_vehicle_user_id = $rowvehicle[$x]->vehicle_user_id;
								$ritase_report_vehicle_id = $rowvehicle[$x]->vehicle_id;
								$ritase_report_vehicle_device = $rowvehicle[$x]->vehicle_device;
								$ritase_report_vehicle_no = $rowvehicle[$x]->vehicle_no;
								$ritase_report_vehicle_name = $rowvehicle[$x]->vehicle_name;
								$ritase_report_vehicle_type = $rowvehicle[$x]->vehicle_type;
								$ritase_report_imei = $rowvehicle[$x]->vehicle_mv03;
								$ritase_report_vehicle_company = $rowvehicle[$x]->vehicle_company;
								$ritase_report_company_name = $rows[$i]->company_name;
								$ritase_report_type = 0;
								$ritase_report_name = "ritase";

								$ritase_report_start_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows_live[$z]->ritase_last_site_datetime))); //sudah wita
								$ritase_report_start_location = $position_start_name;
								$ritase_report_start_geofence = $geofence_start_name;
								$ritase_report_start_coordinate = "";

								$ritase_report_end_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows_live[$z]->ritase_gpstime))); //sudah wita
								$ritase_report_end_location = $position_end_name;
								$ritase_report_end_geofence = $geofence_end_name;
								$ritase_report_end_coordinate = "";

								$ritase_report_gps_date = date("Y-m-d",strtotime($ritase_report_end_time));
								$ritase_report_gps_hour = date("H:00:00",strtotime($ritase_report_end_time));
								
								$ritase_report_driver = 0;
								$ritase_report_driver_name = "";
								
								$duration = get_time_difference($ritase_report_start_time, $ritase_report_end_time);

									$start_1 = dbmaketime($ritase_report_start_time);
									$end_1 = dbmaketime($ritase_report_end_time);
									$duration_sec = $end_1 - $start_1;

                                    $show = "";
                                    if($duration[0]!=0)
                                    {
                                        $show .= $duration[0] ." Day ";
                                    }
                                    if($duration[1]!=0)
                                    {
                                        $show .= $duration[1] ." Hour ";
                                    }
                                    if($duration[2]!=0)
                                    {
                                        $show .= $duration[2] ." Min ";
                                    }
                                    if($show == "")
                                    {
                                        $show .= "0 Min";
                                    }

								$ritase_report_duration = $show;
								$ritase_report_duration_sec = $duration_sec;

								$datainsert["ritase_report_vehicle_user_id"] = $ritase_report_vehicle_user_id;
								$datainsert["ritase_report_vehicle_id"] = $ritase_report_vehicle_id;
								$datainsert["ritase_report_vehicle_device"] = $ritase_report_vehicle_device;
								$datainsert["ritase_report_vehicle_no"] = $ritase_report_vehicle_no;
								$datainsert["ritase_report_vehicle_name"] = $ritase_report_vehicle_name;
								$datainsert["ritase_report_vehicle_type"] = $ritase_report_vehicle_type;
								$datainsert["ritase_report_vehicle_company"] = $ritase_report_vehicle_company;
								$datainsert["ritase_report_company_name"] = $ritase_report_company_name;
								$datainsert["ritase_report_imei"] = $ritase_report_imei;
								$datainsert["ritase_report_type"] = $ritase_report_type;
								$datainsert["ritase_report_name"] = $ritase_report_name;

								$datainsert["ritase_report_from"] = $ritase_report_start_geofence;
								$datainsert["ritase_report_from_time"] = $ritase_report_start_time;
								$datainsert["ritase_report_to"] = $ritase_report_end_location;
								$datainsert["ritase_report_to_time"] = $ritase_report_end_time;
								
								$datainsert["ritase_report_gps_time"] = $ritase_report_end_time;
								$datainsert["ritase_report_gps_date"] = $ritase_report_gps_date;
								$datainsert["ritase_report_gps_hour"] = $ritase_report_gps_hour;
								

								$datainsert["ritase_report_driver"] = $ritase_report_driver;
								$datainsert["ritase_report_driver_name"] = $ritase_report_driver_name;
								$datainsert["ritase_report_duration"] = $ritase_report_duration;
								$datainsert["ritase_report_duration_sec"] = $ritase_report_duration_sec;
							
								//get last data
								$this->dbts = $this->load->database("webtracking_ts",true); 
								$this->dbts->select("ritase_report_id");
								$this->dbts->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
								$this->dbts->where("ritase_report_gps_date",$ritase_report_gps_date);
								$this->dbts->where("ritase_report_gps_hour",$ritase_report_gps_hour);
								//$this->dbts->limit(1);
								$q_last = $this->dbts->get("ts_ritase_hour");
								$row_last = $q_last->row();
								$total_last = count($row_last);
								
								if($total_last>0){
									$this->dbts = $this->load->database("webtracking_ts",true); 
									$this->dbts->where("ritase_report_vehicle_id", $ritase_report_vehicle_id);
									$this->dbts->where("ritase_report_gps_date",$ritase_report_gps_date);
									$this->dbts->where("ritase_report_gps_hour",$ritase_report_gps_hour);
									//$this->dbts->limit(1);
									$this->dbts->update("ts_ritase_hour",$datainsert);
									printf("!==UPDATE OK \r\n ");
								}else{
									
									$this->dbts->insert("ts_ritase_hour",$datainsert);
									printf("===INSERT OK \r\n");
								}

							}

							printf("============================================ \r\n");

						}
						else
						{
							printf("NO DATA RITASE \r\n");
						}
				
				
			}
			
			
		}
		$finishtime = date("Y-m-d H:i:s");
		
		//send telegram
		$title_name = "AUTOCHECK RITASE PER HOUR";
			$message = urlencode(
					"".$title_name." \n".
					"Date: ".$nowdate." \n".
					"Start Time: ".$nowtime." \n".
					"End Time: ".$finishtime." \n"
				);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-671321211",$message); //AUTOCHECK HOUR
		printf("===SENT TELEGRAM OK\r\n");
		
		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
		printf("===FINISH AUTOCHECK RITASE HOUR DATA %s to %s \r\n", $nowtime, $finishtime);
		printf("============================== \r\n");

	}
	
	function get_street_id($streetname,$userid)
	{
		$street_id = 0;
		$this->dbstreet = $this->load->database("default", true);
		$this->dbstreet->select("street_id,street_name");
        $this->dbstreet->order_by("street_created", "desc");
		$this->dbstreet->group_by("street_name");
		$this->dbstreet->where("street_name", $streetname);
		$this->dbstreet->where("street_creator", $userid);
        $qh = $this->dbstreet->get("street");
		$rowh = $qh->row();
		
		$totalh = count($rowh);
		if($totalh > 0){
			$street_id = $rowh->street_id;
		}
		
		$this->dbstreet->close();
		$this->dbstreet->cache_delete_all();
		
        return $street_id;
		
		
	}
	
	function getCompanyName($companyid)
	{
		
		$this->db = $this->load->database("default",true);
		$this->db->select("company_name");	
		$this->db->order_by("company_name", "desc");
		$this->db->where("company_id ", $companyid);
		$q = $this->db->get("company");
		$rows = $q->row();
		$total_rows = count($rows);
		
		if($total_rows > 0){
			$company_name = $rows->company_name;
		}else{
			$company_name = "";
		}
		
		return $company_name;
	
	}
	
	function autocheck_hour_summary($userid="",$orderby="",$date="")
	{
		
		printf("===AUTOCHECK HOUR SUMMARY \r\n");
		$nowtime = date("Y-m-d H:i:s");
		printf("STARTING... %s  \r\n",$nowtime);
		$start_time = date("Y-m-d H:i:s");
		
		if ($date == "") {
            $date = date("Y-m-d", strtotime("yesterday"));
            
        }
		
		//create kalender config report + 1 day
		printf("CREATE KALENDER REPORT... %s  \r\n",$nowtime);
		$this->create_monthly_report($date);
		
		
		$date_tgl = date("d", strtotime($date));
		$month = date("m", strtotime($date));
		$year = date("Y", strtotime($date));
		
		$company =  $this->getCompanyAll($userid);
		$totalcompany = count($company);
		
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("hour_name,hour_time");
		$this->dbts->where("hour_flag", 0);
        $result = $this->dbts->get("ts_hour_shift");
        $datahour = $result->result();
		$totaldatahour = count($datahour);
		printf("===TOTAL COMPANY %s \r\n", $totalcompany);
		//printf("===TOTAL HOUR %s \r\n", $totaldatahour);
		
		
		for ($x=0;$x<$totalcompany;$x++)
		{
			$nourut = $x+1;
			
			for ($y=0;$y<$totaldatahour;$y++)
			{
				$hour = $datahour[$y]->hour_time;
				$hour_show = date("H", strtotime($hour));
				$companyid = $company[$x]->company_id;
				$companyname = $company[$x]->company_name;
				$shift = $this->getshift_byhour($hour_show);
				printf("PROCESS HOUR : %s %s %s %s of %s \r\n",$company[$x]->company_name,$date,$hour, $nourut, $totalcompany);
				$totalunit = $this->get_totalvehicle_opr($companyid,$date,$hour);
				printf("TOTAL UNIT OPR : %s \r\n",$totalunit);
				//print_r($totalunit);exit();
				
				unset($datacheck);
				$datacheck["hour_company"] = $companyid;
				$datacheck["hour_company_name"] = $companyname;
				$datacheck["hour_time"] = $hour;
				$datacheck["hour_time_show"] = $hour_show;
				$datacheck["hour_shift"] = $shift;
				$datacheck["hour_month"] = $month;
				$datacheck["hour_year"] = $year;
				//$datacheck["hour_date"] = $date;
				$datacheck["hour_date_".$date_tgl] = $totalunit;
				
				
				//insert or delete 
				$this->dbts = $this->load->database("webtracking_ts", true);
				$this->dbts->select("hour_id,hour_month,hour_year,hour_company");
				$this->dbts->order_by("hour_id", "asc");
				$this->dbts->where("hour_company", $companyid);
				$this->dbts->where("hour_month", $month);
				$this->dbts->where("hour_year", $year);
				$this->dbts->where("hour_time", $hour);
				$this->dbts->limit(1);
				$q = $this->dbts->get("ts_location_hour_summary");
				$row = $q->row();
				
				//update
				if(count($row)>0){
					
					$this->dbts->where("hour_company", $companyid);
					$this->dbts->where("hour_month", $month);
					$this->dbts->where("hour_year", $year);
					$this->dbts->where("hour_time", $hour);
					$this->dbts->limit(1);
					$this->dbts->update("ts_location_hour_summary", $datacheck);
					printf("===UPDATED OK=== \r\n");
					
				}
				//insert 
				else{
					
					$this->dbts->insert("ts_location_hour_summary",$datacheck);
					printf("===INSERT OK=== \r\n");
				}
			}
			
			
			//insert per company
			printf("===END COMPANY=========\r\n");
			
			
			
		}
		
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("COMPLETED : %s - %s \r\n",$start_time,$finish_time);
		//send telegram
		$title_name = "AUTOCHECK HOUR SUMMARY ".$date;
			$message = urlencode(
					"".$title_name." \n".
					//"Date: ".$date." \n".
					"Start Time: ".$start_time." \n".
					"End Time: ".$finish_time." \n"
				);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-671321211",$message); //autocheck hour
		//$sendtelegram = $this->telegram_direct("-657527213",$message); //fms testing
	
		printf("===SENT TELEGRAM OK\r\n");	
		
		
	}
	
	function getLocationHour_selected($vid,$vdate,$vhour,$vtable)
	{
		
		$this->dbts = $this->load->database("tensor_report",true);
		$this->dbts->order_by("location_report_id", "desc");
		$this->dbts->where("location_report_vehicle_id ", $vid);
		$this->dbts->where("location_report_gps_date", $vdate);
		$this->dbts->where("location_report_gps_hour", $vhour);
		$this->dbts->limit(1);
		$q = $this->dbts->get($vtable);
		$rows = $q->row();
		$total_rows = count($rows);
		
		return $rows;
	
	}	
	
	function telegram($user,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        //$url = "http://lacak-mobil.com/telegram/telegrampost";
		//$url = "http://admintib.buddiyanto.my.id/telegram/telegram_directpost";
		//$url = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
		$url = $this->config->item('url_send_telegram');
        
        $data = array("id" => $user, "message" => $message);
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
		
		
		/* curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt($ch, CURLOPT_URL, 'http://admintib.pilartech.co.id/' );
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2 ); */

		//var_dump(curl_exec($ch));
		//var_dump(curl_getinfo($ch));
		//var_dump(curl_error($ch));
		
		/* curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt($ch, CURLOPT_URL, 'http://admintib.buddiyanto.my.id/' );
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2 ); */
		
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,true);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));  
        $result = curl_exec($ch);
        
        if ($result === FALSE) {
            die("Curl failed: " . curL_error($ch));
        }
        echo $result;
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
    }
	
	function get_time_difference($starttime, $endtime)
	{
		
		$start_1 = dbmaketime($starttime);
		$end_1 = dbmaketime($endtime);
		$duration_sec = $end_1 - $start_1;
									
		$show = "";
			if($duration[0]!=0)
			{
                $show .= $duration[0] ." Day ";
			}
			if($duration[1]!=0)
			{
				$show .= $duration[1] ." Hour ";
			}
			if($duration[2]!=0)
			{
                $show .= $duration[2] ." Min ";
			}
            if($show == "")
            {
                $show .= "0 Min";
            }
						
			return $show;
	}
		
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
