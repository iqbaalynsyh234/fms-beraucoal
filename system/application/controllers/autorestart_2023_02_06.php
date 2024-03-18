<?php
include "base.php";

class Autorestart extends Base {
	var $otherdb;
	function Autorestart()
	{
		parent::Base();	
		$this->load->model("gpsmodel_autosetting");
		$this->load->model("vehiclemodel");
		$this->load->model("smsmodel");
		$this->load->model("gpsmodel");
	}
	
	//tanpa kondisi fraud di PORT (25-jan- 2023)
	function autocheck_new_bk($groupname="", $userid="4408", $order="asc")
	{
		
		$nowtime = date("Y-m-d H:i:s");
		printf("===================== \r\n");
		
		
		printf("===Search SMS Modem Config at %s \r\n", $nowtime);
		printf("======================================\r\n");
		
		//select list sms modem cron aktif
		$this->db = $this->load->database("default", TRUE);
		//$this->db->select("modem_configdb,modem_cron_active,modem_cron_group");
		$this->db->where("modem_cron_group", $groupname);
		$this->db->where("modem_cronnew_active",1); //new config
		$this->db->where("modem_flag",0);
		$qmodem = $this->db->get("sms_modem");
		if ($qmodem->num_rows() == 0) return;

		$rowsmodem = $qmodem->result();
		$totalmodem = count($rowsmodem);
		
		$data_k = array();
		$data_m = array();
		$data_p = array();
		
		for($x=0;$x<$totalmodem;$x++)
		{
			$modem = $rowsmodem[$x]->modem_configdb;
			$modem_name = $rowsmodem[$x]->modem_name;
			$no_urut_modem = $x+1;
			$nowdate = date("Y-m-d H:i:s");
			$running = 0;
			printf("===STARTING AUTOCHECK Now %s startdate %s \r\n", $nowtime, $nowdate);
			printf("===Prepare Check Last Info Modem SMS : %s (%d/%d)\n", $modem, $no_urut_modem, $totalmodem); 
			
			$wa_token = $this->getWAToken($userid);
			printf("===GET TOKEN WA : %s \n", $wa_token->sess_value); 
			
			$this->db = $this->load->database("default",true); 
			$this->db->order_by("vehicle_id",$order);
			$this->db->where("vehicle_user_id", $userid);
			$this->db->where("vehicle_modem", $modem);
			$this->db->where("vehicle_status <>", 3);
			//$this->db->where("vehicle_no", "BBS 1215"); //tes only
			
			
			$this->db->from("vehicle");
			$q = $this->db->get();
			$rowvehicle = $q->result();
			$total_rows = count($rowvehicle);
			if($total_rows>0){
			
				printf("===TOTAL VEHICLE : %s \r\n", $total_rows);
				$feature = array();
				$running = 1;
				$vehicle_gotohistory = 0;
				for($i=0;$i<$total_rows;$i++)
				{
					$no_urut = $i+1;
					printf("===PROSES DB LIVE: %s (%s of %s) \r\n", $rowvehicle[$i]->vehicle_no, $no_urut, $total_rows);
					$devices = explode("@", $rowvehicle[$i]->vehicle_device);
					$vehicle_dblive = $rowvehicle[$i]->vehicle_dbname_live;
					$vehicle_imei = $devices[0];
					$vehicle_no = $rowvehicle[$i]->vehicle_no;
					$vehicledevice = $rowvehicle[$i]->vehicle_device;
					$vehicleuser = $rowvehicle[$i]->vehicle_user_id;
					$vehicleidfix = $rowvehicle[$i]->vehicle_id;
					$vehiclecompany = $rowvehicle[$i]->vehicle_company;
					
					$lastromname = $rowvehicle[$i]->vehicle_rom_name;
					$lastromtime = $rowvehicle[$i]->vehicle_rom_time; 
					
					$lastportname = $rowvehicle[$i]->vehicle_port_name;
					$lastporttime = $rowvehicle[$i]->vehicle_port_time;
					
					
					$lastromtime_sec = strtotime($lastromtime);
					
					$gps = $this->getlastposition_fromDBLive($vehicle_imei,$vehicle_dblive);
					
						if(count($gps)>0){
							
							$lastposition = $this->getPosition_other($gps->gps_longitude_real, $gps->gps_latitude_real);
							$lastposition_time = date("Y-m-d H:i:s", strtotime($gps->gps_time . "+7hours"));
							$gps_realtime = $lastposition_time;
							$speed = number_format($gps->gps_speed*1.852, 0, "", ".");
							printf("===Raw GPS Time: %s  \r\n", $gps->gps_time);
							printf("===Now Time: %s  \r\n", $nowtime);
							printf("===GPS Time: %s  \r\n", $lastposition_time);
							printf("===Vehicle No %s \r\n", $vehicle_no);
							printf("===Speed %s \r\n", $speed);
							
							$this->db = $this->load->database("default",true);
							
							$lastlat = $gps->gps_latitude_real;
							$lastlong = $gps->gps_longitude_real;
							$course = $gps->gps_course;
							$coordinate = $lastlat.",".$lastlong;
							if($gps->gps_status == "A"){
								$gpsvalidstatus = "OK";
							}else{
								$gpsvalidstatus = "NOT OK";
							}
							
							$datajson = json_decode($gps->vehicle_autocheck);
							if($speed > 0)
							{
								$engine = "ON";
								printf("===HARCODE ENGINE %s \r\n", $engine);
							}
							else
							{
								$engine = $datajson->auto_last_engine;
								
							}
							
							printf("===Engine %s \r\n", $engine);
							
										$street_register = $this->config->item('street_register_autocheck');
										$port_register = $this->config->item('port_register_autocheck');
										$rom_register = $this->config->item('rom_register_autocheck'); // all rom legal & ilegal
										$rombib_register = $this->config->item('rombib_register_autocheck'); // rom legal
										$wim_register = $this->config->item('wim_register_autocheck');
										$nonbib_register = $this->config->item('nonbib_register_autocheck');
										$bayah_muatan_register = $this->config->item('bayah_muatan_register_autocheck');
										$bayah_kosongan_register = $this->config->item('bayah_kosongan_register_autocheck');
										
										//filter in location array HAULING, ROM, PORT 
										$street_onduty = $this->config->item('street_onduty_autocheck'); 
										
							
							
							$lastposition_time_sec = strtotime($lastposition_time);
							$coordinate = $gps->gps_latitude_real.",".$gps->gps_longitude_real;
							$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
								
							$location = "-";
							
							//condition here
							if(isset($lastposition)){
								$ex_lastposition = explode(",",$lastposition->display_name);
								$street_name = $ex_lastposition[0];
								
								// TES HARDCODE LOCATION HERE
								//$street_name = "STU area"; //tes only
								
								$location = $street_name;
								//printf("===Location data %s \r\n", $lastposition->display_name);
								printf("===Location %s \r\n", $street_name);
								
								
								// in HAULING
								if (in_array($street_name, $street_register)){
									$hauling = "in";
								}else{
												
									$hauling = "out";
								}
								
									//IN ROM
									/* if (in_array($street_name, $rom_register))
										{
											$now_rom_name = $street_name;
											$now_rom_time = $gps_realtime;

											printf("X==ROM CHECKING  \r\n");
											//sementara untuk input all data
											
											if($auto_last_rom_name == ""){
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = $gps_realtime;
												printf("X==Data Awal ROM \r\n");
												
											}
											else
											{
												//update data tiap masuk ROM 
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = $gps_realtime;
												printf("X==Masih di ROM \r\n");
												
											}
											
										} */
										
										//IN PORT
										if (in_array($street_name, $port_register))
										{
											$now_port_name = $street_name;
											$now_port_time = $gps_realtime;
											printf("Y==PORT CHECKING \r\n");
											//sementara untuk input all data
											
											/* if($auto_last_port_name == ""){
												$auto_last_port_name = $street_name;
												$auto_last_port_time = $gps_realtime;
												printf("Y==Data Awal PORT \r\n");
												//exit();
											}
											else
											{
												//jika port lama beda dengan PORT sekarang, maka update ke yg baru
												$auto_last_port_name = $street_name;
												$auto_last_port_time = $gps_realtime;
												printf("Y==Masih di PORT \r\n");
											} */
											
											$port_status = 1;
											$lastportdata = $this->port_lastdata($rowvehicle[$i],$street_name,$gps_realtime);
											
										}
								
								//in ROM
								//if (in_array($street_name, $rombib_register))
								if (in_array($street_name, $rom_register)) // all rom legal & ilegal
								{
											
									$rom_status = 1;
									$lastromdata = $this->rom_lastdata($rowvehicle[$i],$street_name,$gps_realtime); //sudah wita
												
								}
								
								// SP BAYAH  MUATAN / KOSONGAN
								if (in_array($street_name, $bayah_muatan_register))
								{
									$jalur = "muatan";
									$hauling = "in";
								}
								else if(in_array($street_name, $bayah_kosongan_register))
								{
									$jalur = "kosongan";
									$hauling = "in";
								}
								else
								{
									$jalur = $this->get_jalurname($course);
								}
								
								printf("===Location %s, Jalur %s , Hauling %s \r\n", $street_name, $jalur, $hauling); //print_r("DISINI");exit();
								
										$redzone_status = 0;
										$warningzone_status = 0;
										$nonbib_status = 0; 
										$port_status = 0;
										$redzone_area = $this->config->item('redzone_area_autocheck');
										$warningzone_area = $this->config->item('warningzone_area_autocheck');
															  
										$lastdata_json = json_decode($rowvehicle[$i]->vehicle_autocheck);
									
										// ganti
										$auto_last_rom_name = $lastromname;
										$auto_last_rom_time = $lastromtime;
										$auto_last_port_name = $lastportname;
										$auto_last_port_time = $lastporttime;
																
										printf("===LAST ROM %s %s \r\n", $auto_last_rom_name, $auto_last_rom_time);
										printf("===LAST PORT %s %s \r\n", $auto_last_port_name, $auto_last_port_time);
										
										$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
										$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
										
										//red zone
										if (in_array($street_name, $redzone_area))
										{
													
											$redzone_type = "X";
											$limit_last_port = 3600*3; //3jam
											$limit_last_rom = 3600*3; //3jam
											
											printf("X==LIMIT TIME REDZONE: %s s\r\n", $limit_last_rom);
											printf("!==REDZONE DETECTED \r\n");
											
											//jika no data ROM maka Non BIB Activity (redzone = nol)
											if($auto_last_rom_name == "")
											{
												$redzone_status = 2;
												$lastrom_text = "No Data ROM";
												//$redzone_type = "Z1";
											}
											else
											{
												//$redzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = strtotime($gps_realtime);
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Delta Last ROM %s s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom)
												{
													$redzone_status = 2;
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text,  $gps_realtime, $delta_rom/3600 );
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													//$redzone_type = "Z1";
												}
												else
												{
													// jika asal ROM dari ROM BIB legal maka Redzone 1 
													if (in_array($auto_last_rom_name, $rombib_register))
													{
	
														/* $date_x = 1664487843;
														$date_y = 1664491138; */
														$date_x = $lastrom_time+3600;
														$date_y = $now_time+3600;
														$attachmentlink = "http://attachment.pilartech.co.id/attachment/playbackhistory/".$vehicleidfix.'/'.$date_x.'/'.$date_y;
														$redzone_status = 1;
														$title_name = "REDZONE DETECTED!!";
														$message = urlencode(
																	"".$title_name." \n".
																	"Time: ".$gps_realtime." \n".
																	"Vehicle No: ".$rowvehicle[$i]->vehicle_no." \n".
																	"Position: ".$street_name." \n".
																	"Coordinate: ".$url." \n".
																	"Speed: ".$speed." kph"." \n".
																	"Last ROM: ".$lastrom_text." \n".
																	"Last PORT: ".$lastport_text." \n".
																	"Link: ".$attachmentlink." \n"
																	
																	);
														sleep(2);		
														
														if($lastrom_text == "No Data ROM" || $lastport_text == "No Data PORT"){
															$sendtelegram = $this->telegram_direct("-657527213",$message); //telegram TESTING
															printf("X==Z1 SENT REDZONE OK");
															
														}else{
															
															$notif_wa_redzone = $this->sendnotif_wa_redzone($wa_token,$gps_realtime,$rowvehicle[$i]->vehicle_no,$street_name,$url,$speed,$lastrom_text,$lastport_text,$attachmentlink);
															//$sendtelegram = $this->telegram_direct("-731348063",$message); //telegram REDZONE CHECK
															//$sendtelegram = $this->telegram_direct("-657527213",$message); //telegram TESTING
															$sendtelegram = $this->telegram_direct("-632059478",$message); //telegram BIB REDZONE
															printf("===SENT REDZONE OK\r\n");	
														}
													}
													
													//selain dari ROM BIB LEGAL atau NO Data In ROM maka Non BIB activity
													else
													{
														$redzone_status = 2;
														
														
														
													}
													
												}
												
											
											}
											
											//printf("X==last ROM %s \r\n", $lastrom_text);
											
											if($auto_last_port_name == "")
											{
												$lastport_text = "No Data PORT";
												//$redzone_type = "Z1";
											}
											else
											{
												//$redzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = strtotime($gps_realtime);
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port/3600 );
													$lastport_text = "Tidak dari PORT sejak 3 jam terakhir";
													//$redzone_type = "Z1";
													
													
													//dimatikan semntara issue latency (5 - 10 - 2022)
													/* $lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicle_imei,$rowvehicle[$i]->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 3 jam terakhir";
													} */
													
													
												}
											}
											
											
										}
								
										//warning zone
										if (in_array($street_name, $warningzone_area))
										{
											$warningzone_status = 1;
											$warningzone_type = "X";
											$limit_last_port = 3600*3; //3jam
											$limit_last_rom = 3600*3; //3jam
											printf("===WARNINGZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$warningzone_type = "Z1";
												
											}
											else
											{
												$warningzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = strtotime($gps_realtime);
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, $gps_realtime, $delta_rom/3600 );
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$warningzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == "")
											{
												$lastport_text = "No Data PORT";
												$warningzone_type = "Z1";
											}
											else
											{
												$warningzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port/3600 );
													$lastport_text = "Tidak dari PORT sejak 3 jam terakhir";
													$warningzone_type = "Z1";
													
													$lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicle_imei,$rowvehicle[$i]->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 3 jam terakhir";
													}
													
													
												}
											}
											
											
										}
								
								
							
								//WIM KM 13
								if (in_array($street_name, $wim_register))
								{
									//update field master data wim time (vehicle_wim_stime)
									$wim_time = $this->wim_updatetime($rowvehicle[$i],$street_name, $gps_realtime);
											
								}
								$overspeed_status = 0;
								
								
								
								
									
									if($warningzone_status == 1)
									{
										
										$notif_wa_warningzone = $this->sendnotif_wa_warningzone($wa_token,$gps_realtime,$rowvehicle[$i]->vehicle_no,$street_name,$url,$speed,$lastrom_text,$lastport_text);
										
											$title_name = "WARNING ZONE DETECTED!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".$gps_realtime." \n".
														"Vehicle No: ".$rowvehicle[$i]->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n".
														"Last ROM: ".$lastrom_text." \n".
														"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											
											if($warningzone_type == "Z1"){
												$sendtelegram = $this->telegram_direct("-613149815",$message); //telegram BIB WARNING ZONE
												//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											}else{
												$sendtelegram = $this->telegram_direct("-613149815",$message); //telegram BIB WARNING ZONE
												//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											}
											
											printf("===SENT TELEGRAM OK\r\n");	
									
									}
									
								
								
								
								
							}
							
							
							//delta time gps VS gps now WITA
							$gps_realtime_sec = strtotime($gps_realtime);
							$nowtime_sec = strtotime($nowtime);
							$delta = $nowtime_sec - $gps_realtime_sec;
							$duration = get_time_difference($gps_realtime, $nowtime);
								
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
							
							printf("===Delta %s %s \r\n", $delta, $show);
							//cek delay kurang dari 1 jam
							if ($delta >= 1800 && $delta <= 86400) //default 1jam(3600) -> 24jam(86400)
							{
								printf("===GPS DELAY \r\n");
								$statuscode = "K";
								$info_k = $rowvehicle[$i]->vehicle_no;
								array_push($data_k,$info_k);
								
										//update master vehicle autocheck
										$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);
										$this->db->limit(1);
										$qcheck = $this->db->get("vehicle_autocheck");
										$rowcheck = $qcheck->row(); 			
										if ($qcheck->num_rows() == 0)
										{
											//insert
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//jika insert langsung di isi
											$datacheck["auto_change_engine_status"] = $engine;
											$datacheck["auto_change_engine_datetime"] = $gps_realtime;
											$datacheck["auto_change_position"] = $street_name;
											$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
											
											$this->db->insert("vehicle_autocheck",$datacheck);
											printf("===INSERT AUTOCHECK=== \r\n");	

											//json										
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 0;
											$feature["vehicle_gotohistory"] = 0;
											$vehicle_gotohistory = 0;	
															
										}
										else
										{
											//update
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//json
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 0;
											$feature["vehicle_gotohistory"] = 0;
											$vehicle_gotohistory = 0;	
											
											$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
											$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);	
											$this->db->update("vehicle_autocheck",$datacheck);
											printf("===UPDATE AUTOCHECK=== \r\n");	
											
											
											
												
										}
								
							}
							else if($delta >= 86400) //lebih dari 1 hari //red condition 
							{
								printf("===GPS OFFLINE \r\n");
								$statuscode = "M";
								$info_m = $rowvehicle[$i]->vehicle_no;
								array_push($data_m,$info_m);
								printf("======================RED CONDITION======================== \r\n");
								
										unset($datavehicle);
										$datavehicle["vehicle_isred"] = 1;
										$this->db->where("vehicle_device", $rowvehicle[$i]->vehicle_device);
										$this->db->update("vehicle", $datavehicle);
										printf("===UPDATED STATUS IS RED YES=== %s \r\n", $rowvehicle[$i]->vehicle_no);
										
										//update master vehicle autocheck
										$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);
										$this->db->limit(1);
										$qcheck = $this->db->get("vehicle_autocheck");
										$rowcheck = $qcheck->row(); 			
										if ($qcheck->num_rows() == 0)
										{
											//insert
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//jika insert langsung di isi
											$datacheck["auto_change_engine_status"] = $engine;
											$datacheck["auto_change_engine_datetime"] = $gps_realtime;
											$datacheck["auto_change_position"] = $street_name;
											$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
											
											$this->db->insert("vehicle_autocheck",$datacheck);
											printf("===INSERT AUTOCHECK=== \r\n");	

											//json										
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 1;
											$feature["vehicle_gotohistory"] = 1;
											$vehicle_gotohistory = 1;	
															
										}
										else
										{
											//update
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//json
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 1;
											$feature["vehicle_gotohistory"] = 1;
											$vehicle_gotohistory = 1;	
											
											$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
											$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);	
											$this->db->update("vehicle_autocheck",$datacheck);
											printf("===UPDATE AUTOCHECK=== \r\n");	
											
											
											
												
										}
										
											
													
							}
							else //gps update condition
							{
								printf("===GPS UPDATE \r\n");
								$statuscode = "P";
								$info_p = $rowvehicle[$i]->vehicle_no;
								array_push($data_p,$info_p);
										//NON BIB activity
										if (in_array($street_name, $nonbib_register))
										{
											$nonbib_status = 1;
											$company_telegram = $this->getTelegramID_nonbib($vehiclecompany);
											if(count($company_telegram)>0){
												$telegram_geofence = $company_telegram->company_telegram_geofence;
											}else{
												$telegram_geofence = "-495868829";
											}
											
										}
										
										if($redzone_status == 2)
										{
											$title_name = "NON BIB ACTIVITY!!";
														$message = urlencode(
																	"".$title_name." \n".
																	"Time: ".$gps_realtime." \n".
																	"Vehicle No: ".$rowvehicle[$i]->vehicle_no." \n".
																	"Position: ".$street_name." \n".
																	"Coordinate: ".$url." \n".
																	"Speed: ".$speed." kph"." \n".
																	"Last ROM: ".$lastrom_text." \n".
																	"Last PORT: ".$lastport_text." \n"
																	
																	);
														sleep(2);		
															
														if($lastrom_text == "No Data ROM" || $lastport_text == "No Data PORT"){
															//$sendtelegram = $this->telegram_direct("-652199789",$message); //non BIB Activity ALL
															$sendtelegram = $this->telegram_direct("-657527213",$message); //telegram FMS TESTING
															printf("X==Z1 SENT NON BIB OK");
														}else{
															
															$notif_wa_nonbibact_dummping = $this->sendnotif_wa_nonbibact_dumping($wa_token,$gps_realtime,$rowvehicle[$i]->vehicle_no,$street_name,$url,$speed,$company_telegram,$lastrom_text,$lastport_text);
															$sendtelegram = $this->telegram_direct("-652199789",$message); //non BIB Activity ALL															
															//insert to outgeofence alert
															$this->outofgeofence_dumping_insert($rowvehicle[$i],$street_name,$title_name,$gps_realtime,$speed,$jalur,$course,$lastlat,$lastlong,$auto_last_rom_name,$auto_last_rom_time);
															
															printf("===SENT NON BIB OK\r\n");	
														}
											
										}
										
										
										if($nonbib_status == 1)
										{
											
											$notif_wa_nonbibact = $this->sendnotif_wa_nonbibact($wa_token,$gps_realtime,$rowvehicle[$i]->vehicle_no,$street_name,$url,$speed,$company_telegram);
											
											$title_name = "NON BIB ACTIVITY!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".$gps_realtime." \n".
														"Vehicle No: ".$rowvehicle[$i]->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n"
														//"Last ROM: ".$lastrom_text." \n".
														//"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											$sendtelegram = $this->telegram_direct($telegram_geofence,$message);
											
											//insert to outgeofence alert
											$this->outofgeofence_insert($rowvehicle[$i],$street_name,$title_name,$gps_realtime,$speed,$jalur,$course,$lastlat,$lastlong);
											printf("===SENT TELEGRAM OK\r\n");	
											
											$sendtelegram = $this->telegram_direct("-652199789",$message); //non BIB Activity ALL
											printf("===SENT NON BIB OK\r\n");	
										}
										
										if($port_status == 1)
										{
											//get data WIM by NO lambung
											$lastdatawim = $this->getLastDataWIM($rowvehicle[$i],$gps_realtime);
											
										}
										
										if($gps->gps_status == "V")
										{
											printf("===Vehicle No %s NOT OK \r\n", $rowvehicle[$i]->vehicle_no);
											
											
										}
										else
										{
											printf("=================GPS UPDATE================ \r\n");
											unset($datavehicle);
											$datavehicle["vehicle_isred"] = 0;
											$this->db->where("vehicle_device", $rowvehicle[$i]->vehicle_device);
											$this->db->update("vehicle", $datavehicle);
											printf("===UPDATED STATUS DEFAULT=== %s \r\n", $rowvehicle[$i]->vehicle_no);
											
										}
										
										//update master vehicle autocheck
										$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);
										$this->db->limit(1);
										$qcheck = $this->db->get("vehicle_autocheck");
										$rowcheck = $qcheck->row(); 			
										if ($qcheck->num_rows() == 0)
										{
											//insert
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//jika insert langsung di isi
											$datacheck["auto_change_engine_status"] = $engine;
											$datacheck["auto_change_engine_datetime"] = $gps_realtime;
											$datacheck["auto_change_position"] = $street_name;
											$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
											
											$this->db->insert("vehicle_autocheck",$datacheck);
											printf("===INSERT AUTOCHECK=== \r\n");	

											//json										
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 0;
											$feature["vehicle_gotohistory"] = 0;
											$vehicle_gotohistory = 0;	
															
										}
										else
										{
											//update
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//json
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 0;
											$feature["vehicle_gotohistory"] = 0;
											$vehicle_gotohistory = 0;	
											
											//cek engine jika tidak sama dengan sebelumnya maka di update
											if($rowcheck->auto_change_engine_status != $engine){
												printf("===!!CHANGE ENGINE DETECTED=== \r\n");	
												$datacheck["auto_change_engine_status"] = $engine;
												$datacheck["auto_change_engine_datetime"] = $gps_realtime;
												$datacheck["auto_change_position"] = $lastposition->display_name;
												$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
												
												//json
												$feature["auto_change_engine_status"] = $engine;
												$feature["auto_change_engine_datetime"] = $gps_realtime;
												$feature["auto_change_position"] = $lastposition->display_name;
												$feature["auto_change_coordinate"] = $lastlat.",".$lastlong;
											}
											
											
											$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
											$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);	
											$this->db->update("vehicle_autocheck",$datacheck);
											printf("===UPDATE AUTOCHECK=== \r\n");	
											
											
											
												
										}
											
									
										
								
										
										
										
								
								
								
							}
								
								
							
						}
						else
						{
							
									printf("X==NO DATA IN DB LIVE !!\r\n");
								
									unset($datavehicle);
									$datavehicle["vehicle_isred"] = 1;
									$this->db->where("vehicle_device", $rowvehicle[$i]->vehicle_device);
									$this->db->update("vehicle", $datavehicle);
									printf("===UPDATED STATUS IS RED (NO DATA) YES=== %s \r\n", $rowvehicle[$i]->vehicle_no);
										
									//update master vehicle (khusus vehicle GO TO History)
									$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									//$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
										$datacheck["auto_status"] = "M";
										$datacheck["auto_last_update"] = "";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "Go to history";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "NO DATA";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_hauling"] = "";
										
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_flag"] = 0;
										
														
										$this->db->insert("vehicle_autocheck",$datacheck);
										printf("===INSERT AUTOCHECK=== \r\n");
										
										//json
										$feature["auto_status"] = "M";
										$feature["auto_last_update"] = "";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "Go to history";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "NO DATA";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										
										$feature["auto_flag"] = 0;
										$feature["vehicle_gotohistory"] = 1;
										$vehicle_gotohistory = 1;	
														
									}
									else
									{
										//update
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
										$datacheck["auto_status"] = "M";
										$datacheck["auto_last_update"] ="";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "Go to history";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "NO DATA";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_last_hauling"] = "";
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										
										$datacheck["auto_flag"] = 0;
										
										$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);	
										$this->db->update("vehicle_autocheck",$datacheck);
										printf("===UPDATE AUTOCHECK=== \r\n");	
										
										//for json
										$feature["auto_status"] = "M";
										$feature["auto_last_update"] ="";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "Go to history";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "NO DATA";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										$feature["auto_flag"] = 0;
										$feature["vehicle_gotohistory"] = 1;
										$vehicle_gotohistory = 1;	
											
									}
									
						}
						
						if($running == 1){
							unset($datajson);
							//update to master vehicle
							$content = json_encode($feature);
							$datajson["vehicle_autocheck"] = $content;
							$datajson["vehicle_gotohistory"] = $vehicle_gotohistory;
							
							$this->db->where("vehicle_id", $rowvehicle[$i]->vehicle_id);	
							$this->db->limit(1);	
							$this->db->update("vehicle",$datajson);
							printf("===UPDATE JSON MASTER VEHICLE=== \r\n");	
						}
					
					
					printf("===================== \r\n");
					//exit();
				}
				
			
			}
		
		
		}
		
		
		$finishtime = date("Y-m-d H:i:s");
		$start_1 = dbmaketime($nowtime);
		$end_1 = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		
		
		$total_p = count($data_p);
		$total_k = count($data_k);
		$total_m = count($data_m);
		
		//send telegram 
		$cron_name = "AUTOCHECK NEW ".$groupname;
		$statusname = "FINISH";
		$message =  urlencode(
			"".$cron_name." \n".
			"Start: ".$nowtime." \n".
			"Finish: ".$finishtime." \n".
			"Total Unit: ".$total_rows." \n".
			"GPS Update: ".$total_p." \n".
			"GPS Delay: ".$total_k." \n".
			"GPS Offline: ".$total_m." \n".
			"SMS Modem: ".$modem_name." \n".
			"Status: ".$statusname." \n".
			"Latency: ".$duration_sec." s"." \n"
			);
											
		$sendtelegram = $this->telegram_direct("-742300146",$message); //telegram FMS AUTOCHECK
		printf("===SENT TELEGRAM OK\r\n");
	
		printf("=====FINISH %s %s lat: %s s =========== \r\n", $nowtime, $finishtime, $duration_sec);
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	// alert Port to POrt (tanpa masuk ROM)
	function autocheck_new($groupname="", $userid="4408", $order="asc")
	{
		
		$nowtime = date("Y-m-d H:i:s");
		printf("===================== \r\n");
		
		
		printf("===Search SMS Modem Config at %s \r\n", $nowtime);
		printf("======================================\r\n");
		
		//select list sms modem cron aktif
		$this->db = $this->load->database("default", TRUE);
		//$this->db->select("modem_configdb,modem_cron_active,modem_cron_group");
		$this->db->where("modem_cron_group", $groupname);
		$this->db->where("modem_cronnew_active",1); //new config
		$this->db->where("modem_flag",0);
		$qmodem = $this->db->get("sms_modem");
		if ($qmodem->num_rows() == 0) return;

		$rowsmodem = $qmodem->result();
		$totalmodem = count($rowsmodem);
		
		$data_k = array();
		$data_m = array();
		$data_p = array();
		
		$DT_list_company = array("1959","1948","1947","1946","1945","1926","1839","1837","1835","1834");
		
		for($x=0;$x<$totalmodem;$x++)
		{
			$modem = $rowsmodem[$x]->modem_configdb;
			$modem_name = $rowsmodem[$x]->modem_name;
			$no_urut_modem = $x+1;
			$nowdate = date("Y-m-d H:i:s");
			$running = 0;
			printf("===STARTING AUTOCHECK Now %s startdate %s \r\n", $nowtime, $nowdate);
			printf("===Prepare Check Last Info Modem SMS : %s (%d/%d)\n", $modem, $no_urut_modem, $totalmodem); 
			
			$wa_token = $this->getWAToken($userid);
			printf("===GET TOKEN WA : %s \n", $wa_token->sess_value); 
			
			$this->db = $this->load->database("default",true); 
			$this->db->order_by("vehicle_id",$order);
			$this->db->where("vehicle_user_id", $userid);
			$this->db->where("vehicle_modem", $modem);
			$this->db->where("vehicle_status <>", 3);
			//$this->db->where("vehicle_no", "BBS 1215"); //tes only
			
			
			$this->db->from("vehicle");
			$q = $this->db->get();
			$rowvehicle = $q->result();
			$total_rows = count($rowvehicle);
			if($total_rows>0){
			
				printf("===TOTAL VEHICLE : %s \r\n", $total_rows);
				$feature = array();
				$running = 1;
				$vehicle_gotohistory = 0;
				for($i=0;$i<$total_rows;$i++)
				{
					$no_urut = $i+1;
					printf("===PROSES DB LIVE: %s (%s of %s) \r\n", $rowvehicle[$i]->vehicle_no, $no_urut, $total_rows);
					$devices = explode("@", $rowvehicle[$i]->vehicle_device);
					$vehicle_dblive = $rowvehicle[$i]->vehicle_dbname_live;
					$vehicle_imei = $devices[0];
					$vehicle_no = $rowvehicle[$i]->vehicle_no;
					$vehicledevice = $rowvehicle[$i]->vehicle_device;
					$vehicleuser = $rowvehicle[$i]->vehicle_user_id;
					$vehicleidfix = $rowvehicle[$i]->vehicle_id;
					$vehiclecompany = $rowvehicle[$i]->vehicle_company;
					
					$lastromname = $rowvehicle[$i]->vehicle_rom_name;
					$lastromtime = $rowvehicle[$i]->vehicle_rom_time; 
					
					$lastportname = $rowvehicle[$i]->vehicle_port_name;
					$lastporttime = $rowvehicle[$i]->vehicle_port_time;
					
					
					$lastromtime_sec = strtotime($lastromtime);
					
					$gps = $this->getlastposition_fromDBLive($vehicle_imei,$vehicle_dblive);
					
						if(count($gps)>0){
							
							$lastposition = $this->getPosition_other($gps->gps_longitude_real, $gps->gps_latitude_real);
							$lastposition_time = date("Y-m-d H:i:s", strtotime($gps->gps_time . "+7hours"));
							$gps_realtime = $lastposition_time;
							$speed = number_format($gps->gps_speed*1.852, 0, "", ".");
							printf("===Raw GPS Time: %s  \r\n", $gps->gps_time);
							printf("===Now Time: %s  \r\n", $nowtime);
							printf("===GPS Time: %s  \r\n", $lastposition_time);
							printf("===Vehicle No %s \r\n", $vehicle_no);
							printf("===Speed %s \r\n", $speed);
							
							$this->db = $this->load->database("default",true);
							
							$lastlat = $gps->gps_latitude_real;
							$lastlong = $gps->gps_longitude_real;
							$course = $gps->gps_course;
							$coordinate = $lastlat.",".$lastlong;
							if($gps->gps_status == "A"){
								$gpsvalidstatus = "OK";
							}else{
								$gpsvalidstatus = "NOT OK";
							}
							
							$datajson = json_decode($gps->vehicle_autocheck);
							if($speed > 0)
							{
								$engine = "ON";
								printf("===HARCODE ENGINE %s \r\n", $engine);
							}
							else
							{
								$engine = $datajson->auto_last_engine;
								
							}
							
							printf("===Engine %s \r\n", $engine);
							
										$street_register = $this->config->item('street_register_autocheck');
										$port_register = $this->config->item('port_register_autocheck');
										$rom_register = $this->config->item('rom_register_autocheck'); // all rom legal & ilegal
										$rombib_register = $this->config->item('rombib_register_autocheck'); // rom legal
										$wim_register = $this->config->item('wim_register_autocheck');
										$nonbib_register = $this->config->item('nonbib_register_autocheck');
										$bayah_muatan_register = $this->config->item('bayah_muatan_register_autocheck');
										$bayah_kosongan_register = $this->config->item('bayah_kosongan_register_autocheck');
										
										//filter in location array HAULING, ROM, PORT 
										$street_onduty = $this->config->item('street_onduty_autocheck'); 
										
							
							
							$lastposition_time_sec = strtotime($lastposition_time);
							$coordinate = $gps->gps_latitude_real.",".$gps->gps_longitude_real;
							$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
								
							$location = "-";
							$inPORT_status = 0;
							//condition here
							if(isset($lastposition)){
								$ex_lastposition = explode(",",$lastposition->display_name);
								$street_name = $ex_lastposition[0];
								
								// TES HARDCODE LOCATION HERE
								//$street_name = "STU area"; //tes only
								
								$location = $street_name;
								//printf("===Location data %s \r\n", $lastposition->display_name);
								printf("===Location %s \r\n", $street_name);
								
								
								// in HAULING
								if (in_array($street_name, $street_register)){
									$hauling = "in";
								}else{
												
									$hauling = "out";
								}
								
									//IN ROM
									/* if (in_array($street_name, $rom_register))
										{
											$now_rom_name = $street_name;
											$now_rom_time = $gps_realtime;

											printf("X==ROM CHECKING  \r\n");
											//sementara untuk input all data
											
											if($auto_last_rom_name == ""){
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = $gps_realtime;
												printf("X==Data Awal ROM \r\n");
												
											}
											else
											{
												//update data tiap masuk ROM 
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = $gps_realtime;
												printf("X==Masih di ROM \r\n");
												
											}
											
										} */
										
										//IN PORT
										if (in_array($street_name, $port_register))
										{
											$now_port_name = $street_name;
											$now_port_time = $gps_realtime;
											printf("Y==PORT CHECKING \r\n");
											//sementara untuk input all data
											
											/* if($auto_last_port_name == ""){
												$auto_last_port_name = $street_name;
												$auto_last_port_time = $gps_realtime;
												printf("Y==Data Awal PORT \r\n");
												//exit();
											}
											else
											{
												//jika port lama beda dengan PORT sekarang, maka update ke yg baru
												$auto_last_port_name = $street_name;
												$auto_last_port_time = $gps_realtime;
												printf("Y==Masih di PORT \r\n");
											} */
											
											$port_status = 1;
											$inPORT_status = 1;
											$lastportdata = $this->port_lastdata($rowvehicle[$i],$street_name,$gps_realtime);
											
										}
								
								//in ROM
								//if (in_array($street_name, $rombib_register))
								if (in_array($street_name, $rom_register)) // all rom legal & ilegal
								{
											
									$rom_status = 1;
									$lastromdata = $this->rom_lastdata($rowvehicle[$i],$street_name,$gps_realtime); //sudah wita
												
								}
								
								// SP BAYAH  MUATAN / KOSONGAN
								if (in_array($street_name, $bayah_muatan_register))
								{
									$jalur = "muatan";
									$hauling = "in";
								}
								else if(in_array($street_name, $bayah_kosongan_register))
								{
									$jalur = "kosongan";
									$hauling = "in";
								}
								else
								{
									$jalur = $this->get_jalurname($course);
								}
								
								printf("===Location %s, Jalur %s , Hauling %s \r\n", $street_name, $jalur, $hauling); //print_r("DISINI");exit();
								
										$redzone_status = 0;
										$warningzone_status = 0;
										$nonbib_status = 0; 
										$port_status = 0;
										$redzone_area = $this->config->item('redzone_area_autocheck');
										$warningzone_area = $this->config->item('warningzone_area_autocheck');
															  
										$lastdata_json = json_decode($rowvehicle[$i]->vehicle_autocheck);
									
										// ganti
										$auto_last_rom_name = $lastromname;
										$auto_last_rom_time = $lastromtime;
										$auto_last_port_name = $lastportname;
										$auto_last_port_time = $lastporttime;
																
										printf("===LAST ROM %s %s \r\n", $auto_last_rom_name, $auto_last_rom_time);
										printf("===LAST PORT %s %s \r\n", $auto_last_port_name, $auto_last_port_time);
										
										$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
										$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
										
										//red zone
										if (in_array($street_name, $redzone_area))
										{
													
											$redzone_type = "X";
											$limit_last_port = 3600*3; //3jam
											$limit_last_rom = 3600*3; //3jam
											
											printf("X==LIMIT TIME REDZONE: %s s\r\n", $limit_last_rom);
											printf("!==REDZONE DETECTED \r\n");
											
											//jika no data ROM maka Non BIB Activity (redzone = nol)
											if($auto_last_rom_name == "")
											{
												$redzone_status = 2;
												$lastrom_text = "No Data ROM";
												//$redzone_type = "Z1";
											}
											else
											{
												//$redzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = strtotime($gps_realtime);
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Delta Last ROM %s s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom)
												{
													$redzone_status = 2;
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text,  $gps_realtime, $delta_rom/3600 );
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													//$redzone_type = "Z1";
												}
												else
												{
													// jika asal ROM dari ROM BIB legal maka Redzone 1 
													if (in_array($auto_last_rom_name, $rombib_register))
													{
	
														/* $date_x = 1664487843;
														$date_y = 1664491138; */
														$date_x = $lastrom_time+3600;
														$date_y = $now_time+3600;
														$attachmentlink = "http://attachment.pilartech.co.id/attachment/playbackhistory/".$vehicleidfix.'/'.$date_x.'/'.$date_y;
														$redzone_status = 1;
														$title_name = "REDZONE DETECTED!!";
														$message = urlencode(
																	"".$title_name." \n".
																	"Time: ".$gps_realtime." \n".
																	"Vehicle No: ".$rowvehicle[$i]->vehicle_no." \n".
																	"Position: ".$street_name." \n".
																	"Coordinate: ".$url." \n".
																	"Speed: ".$speed." kph"." \n".
																	"Last ROM: ".$lastrom_text." \n".
																	"Last PORT: ".$lastport_text." \n".
																	"Link: ".$attachmentlink." \n"
																	
																	);
														sleep(2);		
														
														if($lastrom_text == "No Data ROM" || $lastport_text == "No Data PORT"){
															$sendtelegram = $this->telegram_direct("-657527213",$message); //telegram TESTING
															printf("X==Z1 SENT REDZONE OK");
															
														}else{
															
															$notif_wa_redzone = $this->sendnotif_wa_redzone($wa_token,$gps_realtime,$rowvehicle[$i]->vehicle_no,$street_name,$url,$speed,$lastrom_text,$lastport_text,$attachmentlink);
															//$sendtelegram = $this->telegram_direct("-731348063",$message); //telegram REDZONE CHECK
															//$sendtelegram = $this->telegram_direct("-657527213",$message); //telegram TESTING
															$sendtelegram = $this->telegram_direct("-632059478",$message); //telegram BIB REDZONE
															printf("===SENT REDZONE OK\r\n");	
														}
													}
													
													//selain dari ROM BIB LEGAL atau NO Data In ROM maka Non BIB activity
													else
													{
														$redzone_status = 2;
														
														
														
													}
													
												}
												
											
											}
											
											//printf("X==last ROM %s \r\n", $lastrom_text);
											
											if($auto_last_port_name == "")
											{
												$lastport_text = "No Data PORT";
												//$redzone_type = "Z1";
											}
											else
											{
												//$redzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = strtotime($gps_realtime);
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port/3600 );
													$lastport_text = "Tidak dari PORT sejak 3 jam terakhir";
													//$redzone_type = "Z1";
													
													
													//dimatikan semntara issue latency (5 - 10 - 2022)
													/* $lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicle_imei,$rowvehicle[$i]->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 3 jam terakhir";
													} */
													
													
												}
											}
											
											
										}
								
										//warning zone
										if (in_array($street_name, $warningzone_area))
										{
											$warningzone_status = 1;
											$warningzone_type = "X";
											$limit_last_port = 3600*3; //3jam
											$limit_last_rom = 3600*3; //3jam
											printf("===WARNINGZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$warningzone_type = "Z1";
												
											}
											else
											{
												$warningzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = strtotime($gps_realtime);
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, $gps_realtime, $delta_rom/3600 );
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$warningzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == "")
											{
												$lastport_text = "No Data PORT";
												$warningzone_type = "Z1";
											}
											else
											{
												$warningzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port/3600 );
													$lastport_text = "Tidak dari PORT sejak 3 jam terakhir";
													$warningzone_type = "Z1";
													
													$lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicle_imei,$rowvehicle[$i]->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 3 jam terakhir";
													}
													
													
												}
											}
											
											
										}
								
								
							
								//WIM KM 13
								if (in_array($street_name, $wim_register))
								{
									//update field master data wim time (vehicle_wim_stime)
									$wim_time = $this->wim_updatetime($rowvehicle[$i],$street_name, $gps_realtime);
											
								}
								$overspeed_status = 0;
								
								
									if($warningzone_status == 1)
									{
										
										$notif_wa_warningzone = $this->sendnotif_wa_warningzone($wa_token,$gps_realtime,$rowvehicle[$i]->vehicle_no,$street_name,$url,$speed,$lastrom_text,$lastport_text);
										
											$title_name = "WARNING ZONE DETECTED!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".$gps_realtime." \n".
														"Vehicle No: ".$rowvehicle[$i]->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n".
														"Last ROM: ".$lastrom_text." \n".
														"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											
											if($warningzone_type == "Z1"){
												$sendtelegram = $this->telegram_direct("-613149815",$message); //telegram BIB WARNING ZONE
												//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											}else{
												$sendtelegram = $this->telegram_direct("-613149815",$message); //telegram BIB WARNING ZONE
												//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											}
											
											printf("===SENT TELEGRAM OK\r\n");	
									
									}
									
								// IN PORT
								//jika now location berada di ROM BIB register 
								//jika delta last port dengan now port location < limit (30menit)
								//dan last rom < now location port maka trigger alert potential fraud
								//hanya company DT yg masuk rule ini
								if (in_array($vehiclecompany, $DT_list_company))
								{
									if($inPORT_status)
									{
										$fraud_anomali = 0;
										$limit_port_time = 60*60; //default 30 menit 
										$limit_periode = 3600 + 3600 + 1800; //2.5 jam
										//$limit_port_time = 3; //test only
										
										$lastport_time_new = strtotime($auto_last_port_time);
										$lastrom_time_new = strtotime($auto_last_rom_time);
										$now_time_new = strtotime($gps_realtime);
										$delta_port_new = $now_time_new - $lastport_time_new;
										printf("X==Delta Last PORT %s s \r\n", $delta_port_new);
										
										$now_port_date = date("Y-m-d", strtotime($now_time_new));
										$last_port_date_new = date("Y-m-d", strtotime($lastport_time_new));
										
										//jika selisih waktu lebih dari limit 
										if($delta_port_new > $limit_port_time)
										{
											//DATA ROM > data LAST PORT maka aman, unit ke ROM dahulu 
											if($lastrom_time_new > $lastport_time_new)
											{
												printf("X==AMAN Unit terdetek ke ROM: %s %s PORT: %s %s \r\n",$auto_last_rom_name, $auto_last_rom_time, $auto_last_port_name, $auto_last_port_time);
											}
											
											//potential fraud (PORT)
											else
											{
												//anomali checking 
												
												//jika PORT to PORT lebih dari limit periode asumsi rom baru)
												if($delta_port_new > $limit_periode)
												{
													$fraud_anomali = 1;
												}
												
												//jika beda tanggal PORT to PORT juga asumsi anomali
												if($now_port_date != $last_port_date_new)
												{
													$fraud_anomali = 1;
												}
												
												$lastrom_text_new = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
												$lastport_text_new = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
										
												printf("F==Potential Fraud %s now: %s delta: %s \r\n", $lastport_text_new,  $gps_realtime, $delta_port_new/3600);
												
												//send notif fraud PORT
												$date_x_new = $lastport_time_new+3600;
												$date_y_new = $now_time_new+3600;
												$attachmentlink = "http://attachment.pilartech.co.id/attachment/playbackhistory/".$vehicleidfix.'/'.$date_x_new.'/'.$date_y_new;
												$redzone_status = 1;
												$title_name = "INDIKASI FRAUD RITASE !!";
												$message = urlencode(
														"".$title_name." \n".
														"Time: ".$gps_realtime." \n".
														"Vehicle No: ".$rowvehicle[$i]->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n".
														"Last ROM: ".$lastrom_text_new." \n".
														"Last PORT: ".$lastport_text_new." \n".
														"Link: ".$attachmentlink." \n"
																		
														);
													sleep(2);		
																										
													if($fraud_anomali == 0)
													{
														$sendtelegram = $this->telegram_direct("-804067664",$message); //telegram BIB POTENTIAL FRAUD
														
													}
													//jika anomali masuk ke sini -823815743
													else
													{
														$sendtelegram = $this->telegram_direct("-823815743",$message); //telegram FRAUD TEST
													}
													
													$fraud_at_PORT = $this->fraud_at_PORT($rowvehicle[$i],$street_name,$gps_realtime,$auto_last_rom_name,date("Y-m-d H:i:s", strtotime($auto_last_rom_time)),
																						$auto_last_port_name, date("Y-m-d H:i:s", strtotime($auto_last_port_time)),$attachmentlink);
													printf("F==SENT TELE FRAUD AT PORT");
													
											}
											
										}
										//masih batas wajar di PORT
										else
										{
											printf("X==Masih Batas Wajar di Port. NOW: %s %s delta: %s s, limit: %s s \r\n",$street_name, $gps_realtime, $delta_port_new/3600, $limit_port_time);
											
										}
										
									}		
								}
								
								
							}
							
							
							//delta time gps VS gps now WITA
							$gps_realtime_sec = strtotime($gps_realtime);
							$nowtime_sec = strtotime($nowtime);
							$delta = $nowtime_sec - $gps_realtime_sec;
							$duration = get_time_difference($gps_realtime, $nowtime);
								
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
							
							printf("===Delta %s %s \r\n", $delta, $show);
							//cek delay kurang dari 1 jam
							if ($delta >= 1800 && $delta <= 86400) //default 1jam(3600) -> 24jam(86400)
							{
								printf("===GPS DELAY \r\n");
								$statuscode = "K";
								$info_k = $rowvehicle[$i]->vehicle_no;
								array_push($data_k,$info_k);
								
										//update master vehicle autocheck
										$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);
										$this->db->limit(1);
										$qcheck = $this->db->get("vehicle_autocheck");
										$rowcheck = $qcheck->row(); 			
										if ($qcheck->num_rows() == 0)
										{
											//insert
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//jika insert langsung di isi
											$datacheck["auto_change_engine_status"] = $engine;
											$datacheck["auto_change_engine_datetime"] = $gps_realtime;
											$datacheck["auto_change_position"] = $street_name;
											$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
											
											$this->db->insert("vehicle_autocheck",$datacheck);
											printf("===INSERT AUTOCHECK=== \r\n");	

											//json										
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 0;
											$feature["vehicle_gotohistory"] = 0;
											$vehicle_gotohistory = 0;	
															
										}
										else
										{
											//update
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//json
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 0;
											$feature["vehicle_gotohistory"] = 0;
											$vehicle_gotohistory = 0;	
											
											$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
											$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);	
											$this->db->update("vehicle_autocheck",$datacheck);
											printf("===UPDATE AUTOCHECK=== \r\n");	
											
											
											
												
										}
								
							}
							else if($delta >= 86400) //lebih dari 1 hari //red condition 
							{
								printf("===GPS OFFLINE \r\n");
								$statuscode = "M";
								$info_m = $rowvehicle[$i]->vehicle_no;
								array_push($data_m,$info_m);
								printf("======================RED CONDITION======================== \r\n");
								
										unset($datavehicle);
										$datavehicle["vehicle_isred"] = 1;
										$this->db->where("vehicle_device", $rowvehicle[$i]->vehicle_device);
										$this->db->update("vehicle", $datavehicle);
										printf("===UPDATED STATUS IS RED YES=== %s \r\n", $rowvehicle[$i]->vehicle_no);
										
										//update master vehicle autocheck
										$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);
										$this->db->limit(1);
										$qcheck = $this->db->get("vehicle_autocheck");
										$rowcheck = $qcheck->row(); 			
										if ($qcheck->num_rows() == 0)
										{
											//insert
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//jika insert langsung di isi
											$datacheck["auto_change_engine_status"] = $engine;
											$datacheck["auto_change_engine_datetime"] = $gps_realtime;
											$datacheck["auto_change_position"] = $street_name;
											$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
											
											$this->db->insert("vehicle_autocheck",$datacheck);
											printf("===INSERT AUTOCHECK=== \r\n");	

											//json										
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 1;
											$feature["vehicle_gotohistory"] = 1;
											$vehicle_gotohistory = 1;	
															
										}
										else
										{
											//update
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//json
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 1;
											$feature["vehicle_gotohistory"] = 1;
											$vehicle_gotohistory = 1;	
											
											$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
											$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);	
											$this->db->update("vehicle_autocheck",$datacheck);
											printf("===UPDATE AUTOCHECK=== \r\n");	
											
											
											
												
										}
										
											
													
							}
							else //gps update condition
							{
								printf("===GPS UPDATE \r\n");
								$statuscode = "P";
								$info_p = $rowvehicle[$i]->vehicle_no;
								array_push($data_p,$info_p);
										//NON BIB activity
										if (in_array($street_name, $nonbib_register))
										{
											$nonbib_status = 1;
											$company_telegram = $this->getTelegramID_nonbib($vehiclecompany);
											if(count($company_telegram)>0){
												$telegram_geofence = $company_telegram->company_telegram_geofence;
											}else{
												$telegram_geofence = "-495868829";
											}
											
										}
										
										if($redzone_status == 2)
										{
											$title_name = "NON BIB ACTIVITY!!";
														$message = urlencode(
																	"".$title_name." \n".
																	"Time: ".$gps_realtime." \n".
																	"Vehicle No: ".$rowvehicle[$i]->vehicle_no." \n".
																	"Position: ".$street_name." \n".
																	"Coordinate: ".$url." \n".
																	"Speed: ".$speed." kph"." \n".
																	"Last ROM: ".$lastrom_text." \n".
																	"Last PORT: ".$lastport_text." \n"
																	
																	);
														sleep(2);		
															
														if($lastrom_text == "No Data ROM" || $lastport_text == "No Data PORT"){
															//$sendtelegram = $this->telegram_direct("-652199789",$message); //non BIB Activity ALL
															$sendtelegram = $this->telegram_direct("-657527213",$message); //telegram FMS TESTING
															printf("X==Z1 SENT NON BIB OK");
														}else{
															
															$notif_wa_nonbibact_dummping = $this->sendnotif_wa_nonbibact_dumping($wa_token,$gps_realtime,$rowvehicle[$i]->vehicle_no,$street_name,$url,$speed,$company_telegram,$lastrom_text,$lastport_text);
															$sendtelegram = $this->telegram_direct("-652199789",$message); //non BIB Activity ALL															
															//insert to outgeofence alert
															$this->outofgeofence_dumping_insert($rowvehicle[$i],$street_name,$title_name,$gps_realtime,$speed,$jalur,$course,$lastlat,$lastlong,$auto_last_rom_name,$auto_last_rom_time);
															
															printf("===SENT NON BIB OK\r\n");	
														}
											
										}
										
										
										if($nonbib_status == 1)
										{
											
											$notif_wa_nonbibact = $this->sendnotif_wa_nonbibact($wa_token,$gps_realtime,$rowvehicle[$i]->vehicle_no,$street_name,$url,$speed,$company_telegram);
											
											$title_name = "NON BIB ACTIVITY!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".$gps_realtime." \n".
														"Vehicle No: ".$rowvehicle[$i]->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n"
														//"Last ROM: ".$lastrom_text." \n".
														//"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											$sendtelegram = $this->telegram_direct($telegram_geofence,$message);
											
											//insert to outgeofence alert
											$this->outofgeofence_insert($rowvehicle[$i],$street_name,$title_name,$gps_realtime,$speed,$jalur,$course,$lastlat,$lastlong);
											printf("===SENT TELEGRAM OK\r\n");	
											
											$sendtelegram = $this->telegram_direct("-652199789",$message); //non BIB Activity ALL
											printf("===SENT NON BIB OK\r\n");	
										}
										
										if($port_status == 1)
										{
											//get data WIM by NO lambung
											$lastdatawim = $this->getLastDataWIM($rowvehicle[$i],$gps_realtime);
											
										}
										
										if($gps->gps_status == "V")
										{
											printf("===Vehicle No %s NOT OK \r\n", $rowvehicle[$i]->vehicle_no);
											
											
										}
										else
										{
											printf("=================GPS UPDATE================ \r\n");
											unset($datavehicle);
											$datavehicle["vehicle_isred"] = 0;
											$this->db->where("vehicle_device", $rowvehicle[$i]->vehicle_device);
											$this->db->update("vehicle", $datavehicle);
											printf("===UPDATED STATUS DEFAULT=== %s \r\n", $rowvehicle[$i]->vehicle_no);
											
										}
										
										//update master vehicle autocheck
										$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);
										$this->db->limit(1);
										$qcheck = $this->db->get("vehicle_autocheck");
										$rowcheck = $qcheck->row(); 			
										if ($qcheck->num_rows() == 0)
										{
											//insert
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//jika insert langsung di isi
											$datacheck["auto_change_engine_status"] = $engine;
											$datacheck["auto_change_engine_datetime"] = $gps_realtime;
											$datacheck["auto_change_position"] = $street_name;
											$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
											
											$this->db->insert("vehicle_autocheck",$datacheck);
											printf("===INSERT AUTOCHECK=== \r\n");	

											//json										
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 0;
											$feature["vehicle_gotohistory"] = 0;
											$vehicle_gotohistory = 0;	
															
										}
										else
										{
											//update
											unset($datacheck);
											$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
											$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
											$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
											$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
											$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
											$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
											$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
											$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
											$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
											$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
											$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
											$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
											$datacheck["auto_status"] = $statuscode;
											$datacheck["auto_last_update"] = $gps_realtime;
											$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$datacheck["auto_last_position"] = $lastposition->display_name;
											$datacheck["auto_last_lat"] = $lastlat;
											$datacheck["auto_last_long"] = $lastlong;
											$datacheck["auto_last_engine"] = $engine;
											$datacheck["auto_last_gpsstatus"] = $gpsvalidstatus;
											$datacheck["auto_last_speed"] = $speed;
											$datacheck["auto_last_course"] = $course;
											$datacheck["auto_last_road"] = $jalur;
											$datacheck["auto_last_hauling"] = $hauling;
											$datacheck["auto_last_rom_name"] = $auto_last_rom_name;
											$datacheck["auto_last_rom_time"] = $auto_last_rom_time;
											$datacheck["auto_last_port_name"] = $auto_last_port_name;
											$datacheck["auto_last_port_time"] = $auto_last_port_time;
											$datacheck["auto_flag"] = 0;
											
											//json
											$feature["auto_status"] = $statuscode;
											$feature["auto_last_update"] = $gps_realtime;
											$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
											$feature["auto_last_position"] = $lastposition->display_name;
											$feature["auto_last_lat"] = $lastlat;
											$feature["auto_last_long"] = $lastlong;
											$feature["auto_last_engine"] = $engine;
											$feature["auto_last_gpsstatus"] = $gpsvalidstatus;
											$feature["auto_last_speed"] = $speed;
											$feature["auto_last_course"] = $course;
											$feature["auto_last_road"] = $jalur;
											$feature["auto_last_hauling"] = $hauling;
											
											$feature["auto_last_rom_name"] = $auto_last_rom_name;
											$feature["auto_last_rom_time"] = $auto_last_rom_time;
											$feature["auto_last_port_name"] = $auto_last_port_name;
											$feature["auto_last_port_time"] = $auto_last_port_time;
											
											$feature["auto_flag"] = 0;
											$feature["vehicle_gotohistory"] = 0;
											$vehicle_gotohistory = 0;	
											
											//cek engine jika tidak sama dengan sebelumnya maka di update
											if($rowcheck->auto_change_engine_status != $engine){
												printf("===!!CHANGE ENGINE DETECTED=== \r\n");	
												$datacheck["auto_change_engine_status"] = $engine;
												$datacheck["auto_change_engine_datetime"] = $gps_realtime;
												$datacheck["auto_change_position"] = $lastposition->display_name;
												$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
												
												//json
												$feature["auto_change_engine_status"] = $engine;
												$feature["auto_change_engine_datetime"] = $gps_realtime;
												$feature["auto_change_position"] = $lastposition->display_name;
												$feature["auto_change_coordinate"] = $lastlat.",".$lastlong;
											}
											
											
											$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
											$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);	
											$this->db->update("vehicle_autocheck",$datacheck);
											printf("===UPDATE AUTOCHECK=== \r\n");	
											
											
											
												
										}
											
									
										
								
										
										
										
								
								
								
							}
								
								
							
						}
						else
						{
							
									printf("X==NO DATA IN DB LIVE !!\r\n");
								
									unset($datavehicle);
									$datavehicle["vehicle_isred"] = 1;
									$this->db->where("vehicle_device", $rowvehicle[$i]->vehicle_device);
									$this->db->update("vehicle", $datavehicle);
									printf("===UPDATED STATUS IS RED (NO DATA) YES=== %s \r\n", $rowvehicle[$i]->vehicle_no);
										
									//update master vehicle (khusus vehicle GO TO History)
									$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									//$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
										$datacheck["auto_status"] = "M";
										$datacheck["auto_last_update"] = "";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "Go to history";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "NO DATA";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_hauling"] = "";
										
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_flag"] = 0;
										
														
										$this->db->insert("vehicle_autocheck",$datacheck);
										printf("===INSERT AUTOCHECK=== \r\n");
										
										//json
										$feature["auto_status"] = "M";
										$feature["auto_last_update"] = "";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "Go to history";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "NO DATA";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										
										$feature["auto_flag"] = 0;
										$feature["vehicle_gotohistory"] = 1;
										$vehicle_gotohistory = 1;	
														
									}
									else
									{
										//update
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle[$i]->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle[$i]->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle[$i]->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle[$i]->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle[$i]->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle[$i]->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle[$i]->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle[$i]->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle[$i]->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle[$i]->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle[$i]->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle[$i]->vehicle_card_no;
										$datacheck["auto_status"] = "M";
										$datacheck["auto_last_update"] ="";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "Go to history";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "NO DATA";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_last_hauling"] = "";
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										
										$datacheck["auto_flag"] = 0;
										
										$this->db->where("auto_user_id", $rowvehicle[$i]->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle[$i]->vehicle_device);	
										$this->db->update("vehicle_autocheck",$datacheck);
										printf("===UPDATE AUTOCHECK=== \r\n");	
										
										//for json
										$feature["auto_status"] = "M";
										$feature["auto_last_update"] ="";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "Go to history";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "NO DATA";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										$feature["auto_flag"] = 0;
										$feature["vehicle_gotohistory"] = 1;
										$vehicle_gotohistory = 1;	
											
									}
									
						}
						
						if($running == 1){
							unset($datajson);
							//update to master vehicle
							$content = json_encode($feature);
							$datajson["vehicle_autocheck"] = $content;
							$datajson["vehicle_gotohistory"] = $vehicle_gotohistory;
							
							$this->db->where("vehicle_id", $rowvehicle[$i]->vehicle_id);	
							$this->db->limit(1);	
							$this->db->update("vehicle",$datajson);
							printf("===UPDATE JSON MASTER VEHICLE=== \r\n");	
						}
					
					
					printf("===================== \r\n");
					//exit();
				}
				
			
			}
		
		
		}
		
		
		$finishtime = date("Y-m-d H:i:s");
		$start_1 = dbmaketime($nowtime);
		$end_1 = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		
		
		$total_p = count($data_p);
		$total_k = count($data_k);
		$total_m = count($data_m);
		
		//send telegram 
		$cron_name = "AUTOCHECK NEW ".$groupname;
		$statusname = "FINISH";
		$message =  urlencode(
			"".$cron_name." \n".
			"Start: ".$nowtime." \n".
			"Finish: ".$finishtime." \n".
			"Total Unit: ".$total_rows." \n".
			"GPS Update: ".$total_p." \n".
			"GPS Delay: ".$total_k." \n".
			"GPS Offline: ".$total_m." \n".
			"SMS Modem: ".$modem_name." \n".
			"Status: ".$statusname." \n".
			"Latency: ".$duration_sec." s"." \n"
			);
											
		$sendtelegram = $this->telegram_direct("-742300146",$message); //telegram FMS AUTOCHECK
		printf("===SENT TELEGRAM OK\r\n");
	
		printf("=====FINISH %s %s lat: %s s =========== \r\n", $nowtime, $finishtime, $duration_sec);
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function autocheck_hour_duty($userid="", $order="asc")
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
		$j = 1;
		for ($i=0;$i<$totalcompany;$i++)
		{
			$total_unit = 0;
			$total_duty = 0;
			$total_duty_persen = 0;
			$total_idle = 0;
			
			printf("===PROCESS COMPANY %s of %s \r\n", $j, $totalcompany);
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id","asc");
			$this->db->select("vehicle_id,vehicle_no,vehicle_name,vehicle_company,vehicle_autocheck");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_company", $rows[$i]->company_id);
			$this->db->where("vehicle_user_id", $userid);
			$qv = $this->db->get("vehicle");
			$rowsvehicle = $qv->result();
			$totalvehicle = count($rowsvehicle);
			for ($k=0;$k<$totalvehicle;$k++)
			{
				$json = json_decode($rowsvehicle[$k]->vehicle_autocheck);
				//print_r($json->auto_last_position);//exit();
			
				$street_hour = array(	"PORT BIB","PORT BIR","PORT TIA",
										"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
										"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST",
										"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
										//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
										//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS EST 32","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",
										"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
										"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
										"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
										"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31",
										"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
										"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
										"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
										"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
										"PORT BIB - Antrian","Port BIB - Antrian"
									);
														
				$ex_lastposition = explode(",",$json->auto_last_position);
				$street_name = $ex_lastposition[0]; 
				if (in_array($street_name, $street_hour)){
					$hauling = "duty";
					$total_duty = $total_duty + 1;
				}else{
					$hauling = "idle";
				}
				
									
				
			}
			
			printf("===TOTAL DUTY %s \r\n", $total_duty);	
			
			$total_unit = $totalvehicle;
			$total_idle = $total_unit - $total_duty;
			$total_duty_persen = ($total_duty / $total_unit) * 100;
			
			$autocheck_company = $rows[$i]->company_id;
			$autocheck_company_name = $rows[$i]->company_name;
			$autocheck_date = date("Y-m-d", strtotime($nowtime_wita));
			$autocheck_time = date("H:i:s", strtotime($nowtime_wita));
			$autocheck_hour = date("H", strtotime($nowtime_wita));
			
		
	
			unset($datacheck);
			$datacheck["autocheck_company"] = $autocheck_company;
			$datacheck["autocheck_company_name"] = $autocheck_company_name;
			$datacheck["autocheck_date"] = $autocheck_date;
			$datacheck["autocheck_time"] = $autocheck_time;
			$datacheck["autocheck_hour"] = $autocheck_hour;
			$datacheck["autocheck_total_unit"] = $total_unit;
			$datacheck["autocheck_total_duty"] = $total_duty;
			$datacheck["autocheck_total_duty_persen"] = $total_duty_persen;
			$datacheck["autocheck_total_idle"] = $total_idle;
			$datacheck["autocheck_creator"] = $userid;
			$datacheck["autocheck_created_date"] = date("Y-m-d H:i:s");		
			
			//check report
			$this->dbts = $this->load->database("webtracking_ts", TRUE);
			$this->dbts->where("autocheck_company", $rows[$i]->company_id);	
			$this->dbts->where("autocheck_date", $autocheck_date);
			$this->dbts->where("autocheck_hour", $autocheck_hour);
			$qcheck = $this->dbts->get("ts_autocheck_hour");
			$rowcheck = $qcheck->row(); 			
			if ($qcheck->num_rows() == 0)
			{
				$this->dbts->insert("ts_autocheck_hour",$datacheck);
				printf("===INSERT HOUR %s \r\n", $autocheck_hour);		
			}
			else
			{
				
				$this->dbts->where("autocheck_company", $rows[$i]->company_id);	
				$this->dbts->where("autocheck_date", $autocheck_date);
				$this->dbts->where("autocheck_hour", $autocheck_hour);
				$this->dbts->update("ts_autocheck_hour",$datacheck);
				printf("X==UPDATE HOUR %s \r\n", $autocheck_hour);		
			}
		}
		
		$finishtime = date("Y-m-d H:i:s");
		
		//send telegram
		$title_name = "AUTOCHECK DUTY PER HOUR";
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
		printf("===FINISH AUTOCHECK HOUR %s to %s \r\n", $nowdate, $enddate);
		printf("============================== \r\n");

	}
	
	function autocheck_hour_pool($userid="", $order="asc")
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
		$j = 1;
		for ($i=0;$i<$totalcompany;$i++)
		{
			$total_unit = 0;
			$total_parkir = 0;
			$total_parkir_persen = 0;
			$total_working = 0;
			
			printf("===PROCESS COMPANY %s of %s \r\n", $j, $totalcompany);
			$this->db = $this->load->database("default", TRUE);
			$this->db->order_by("vehicle_id","asc");
			$this->db->select("vehicle_id,vehicle_no,vehicle_name,vehicle_company,vehicle_autocheck");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_company", $rows[$i]->company_id);
			$this->db->where("vehicle_user_id", $userid);
			$qv = $this->db->get("vehicle");
			$rowsvehicle = $qv->result();
			$totalvehicle = count($rowsvehicle);
			for ($k=0;$k<$totalvehicle;$k++)
			{
				$json = json_decode($rowsvehicle[$k]->vehicle_autocheck);
				//print_r($json->auto_last_position);//exit();
				
				
		
				$street_park = array(	"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
										"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT"
									);
				
														
				$ex_lastposition = explode(",",$json->auto_last_position);
				$street_name = $ex_lastposition[0]; 
				if (in_array($street_name, $street_park)){
					$hauling = "parkir";
					$total_parkir = $total_parkir + 1;
				}else{
					$hauling = "work";
				}
				
									
				
			}
			
			printf("===TOTAL PARKIR %s \r\n", $total_parkir);	
			
			$total_unit = $totalvehicle;
			$total_working = $total_unit - $total_parkir;
			$total_parkir_persen = ($total_parkir / $total_unit) * 100;
			
			$autocheck_company = $rows[$i]->company_id;
			$autocheck_company_name = $rows[$i]->company_name;
			$autocheck_date = date("Y-m-d", strtotime($nowtime_wita));
			$autocheck_time = date("H:i:s", strtotime($nowtime_wita));
			$autocheck_hour = date("H", strtotime($nowtime_wita));
			
		
	
			unset($datacheck);
			$datacheck["autocheck_company"] = $autocheck_company;
			$datacheck["autocheck_company_name"] = $autocheck_company_name;
			$datacheck["autocheck_date"] = $autocheck_date;
			$datacheck["autocheck_time"] = $autocheck_time;
			$datacheck["autocheck_hour"] = $autocheck_hour;
			$datacheck["autocheck_total_unit"] = $total_unit;
			$datacheck["autocheck_total_parkir"] = $total_parkir;
			$datacheck["autocheck_total_parkir_persen"] = $total_parkir_persen;
			//$datacheck["autocheck_total_working"] = $total_working;
			$datacheck["autocheck_creator"] = $userid;
			$datacheck["autocheck_created_date"] = date("Y-m-d H:i:s");		
			
			//check report
			$this->dbts = $this->load->database("webtracking_ts", TRUE);
			$this->dbts->where("autocheck_company", $rows[$i]->company_id);	
			$this->dbts->where("autocheck_date", $autocheck_date);
			$this->dbts->where("autocheck_hour", $autocheck_hour);
			$qcheck = $this->dbts->get("ts_autocheck_hour_pool");
			$rowcheck = $qcheck->row(); 			
			if ($qcheck->num_rows() == 0)
			{
				$this->dbts->insert("ts_autocheck_hour_pool",$datacheck);
				printf("===INSERT HOUR %s \r\n", $autocheck_hour);		
			}
			else
			{
				
				$this->dbts->where("autocheck_company", $rows[$i]->company_id);	
				$this->dbts->where("autocheck_date", $autocheck_date);
				$this->dbts->where("autocheck_hour", $autocheck_hour);
				$this->dbts->update("ts_autocheck_hour_pool",$datacheck);
				printf("X==UPDATE HOUR %s \r\n", $autocheck_hour);		
			}
		}
		
		$finishtime = date("Y-m-d H:i:s");
		
		//send telegram
		$title_name = "AUTOCHECK POOL PER HOUR";
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
		printf("===FINISH AUTOCHECK HOUR %s to %s \r\n", $nowdate, $enddate);
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
	
	function radius_hour($userid="", $imeiexca="", $order="asc", $date="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
		printf("===STARTING AUTOCHECK HOURLY %s WIB %s WITA\r\n", $nowtime, $nowtime_wita); 
		
		if($date == ""){
			$startdate = date("Y-m-d");
			$enddate = date("Y-m-d");
		}else{
			$startdate = date("Y-m-d", strtotime($date));
			$enddate = date("Y-m-d", strtotime($date));
			
		}
		
		$this->dbts = $this->load->database("webtracking_ts", TRUE);
		$this->dbts->order_by("hour_name","asc");
		$this->dbts->select("hour_name");
		
		$this->dbts->where("hour_user", $userid);
		$qhour = $this->dbts->get("ts_hour");
		$rowshour = $qhour->result();
		$totalhour = count($rowshour);
	
		
		for ($i=0;$i<$totalhour;$i++)
		{
			printf("===PROCESS HOUR %s \r\n", $rowshour[$i]->hour_name, $date);
			$lastportdata = $this->dataradius_hour($userid,$imeiexca,$order,$date,$rowshour[$i]->hour_name);
		}
		
		$finishtime = date('Y-m-d H:i:s');
		printf("===FINISH AUTOCHECK RADIUS HOUR DATA %s to %s \r\n", $nowtime, $finishtime);
		printf("============================== \r\n");
		
		//send telegram
		$title_name = "AUTOCHECK RADIUS PER HOUR";
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
		
	}
	
	function dataradius_hour($userid="", $imeiexca="", $order="asc", $date="",$hour="")
	{
		
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
		if($date == ""){
			$startdate = date("Y-m-d");
			$enddate = date("Y-m-d");
		}else{
			$startdate = date("Y-m-d", strtotime($date));
			$enddate = date("Y-m-d", strtotime($date));
			
		}
		
		if($hour == ""){
			$starthour = "00:00:00";
			$endhour = "23:59:59";
			
		}else{
			$starthour = date("H:i:s", strtotime($hour.":00:00"));
			$endhour =  date("H:i:s", strtotime($hour.":59:59"));
		}
		
		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate." ".$starthour))); //wita
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate." ".$endhour)));  //wita
		
		/* print_r($startdate." ".$starthour." s/d ".$enddate." ".$endhour);
		print_r($sdate." ".$edate); exit(); */

		//printf("===STARTING AUTOCHECK HOURLY %s WIB %s WITA\r\n", $nowtime, $nowtime_wita); 
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("company_name","asc");
		$this->db->select("company_name,company_id");
		$this->db->where("company_flag", 0);
		$this->db->where("company_id", 1961); //AMM only
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
			$this->db->where("vehicle_device", $imeiexca."@VT200");
			//$this->db->where("vehicle_device", "869926046496002@VT200");
				
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->result();
			$totalvehicle = count($rowvehicle);
			
			for ($x=0;$x<$totalvehicle;$x++)
			{
				$nourut = $i+1;
				$vehicleid = $rowvehicle[$i]->vehicle_id;
				$vehicle_device = explode("@", $rowvehicle[$i]->vehicle_device);
				$deviceid = $vehicle_device[0];
				$vehicle_no = $rowvehicle[$i]->vehicle_no;
				$dblive =  $rowvehicle[$i]->vehicle_dbname_live;
				$dbhist =  "gpshistory_radius";
				
				$dbtable_live = "webtracking_radius";
				$dbtable_hist = $vehicle_device[0]."_".strtolower($vehicle_device[1])."_gps";
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $sdate, $edate, $vehicle_no, $nourut, $totalvehicle);
				
				$event_data = $this->getDataRadius_eventdata($deviceid,$sdate,$sdate,$dblive,$dbtable_live); 
				$event_data = $this->getDataRadius_eventdata($deviceid,$sdate,$edate,$dbhist,$dbtable_hist);
			
			}
			
		}
		
		
		return;

	}
	
	function getDataRadius_eventdata($deviceid,$sdate,$edate,$dbload,$dbtable)
	{
		
		//get list guest
		$this->dblive = $this->load->database($dbload,true);
		$this->dblive->distinct();
		$this->dblive->group_by("radius_host_time");
		$this->dblive->select('radius_guest');
		$this->dblive->order_by('radius_host_time',"asc");
		$this->dblive->where("radius_host", $deviceid);
		$this->dblive->where("radius_host_time >=",$sdate);
		$this->dblive->where("radius_host_time <=", $edate);
		$this->dblive->where("radius_event", "B");
		$qguest = $this->dblive->get($dbtable);
		$rowsguest = $qguest->result();
		$totalguest = count($rowsguest);
		printf("TOTAL GUEST %s guest table %s \r\n", $totalguest, $dbtable);
		if($totalguest>0)
		{
				
			for($g=0;$g<$totalguest;$g++)
			{
					
					//get max duration per guest 
					$norut_1 = $g+1;
					printf("===DATA UNIT %s of %s \r\n", $norut_1, $totalguest);
					
					$this->dblive->group_by("radius_host_time");
					$this->dblive->order_by('radius_host_time',"asc");
					$this->dblive->where("radius_host", $deviceid);
					$this->dblive->where("radius_guest", $rowsguest[$g]->radius_guest); 
					$this->dblive->where("radius_host_time >=",$sdate);
					$this->dblive->where("radius_host_time <=", $edate);
					$this->dblive->where("radius_event", "B");
					$qguest2 = $this->dblive->get($dbtable);
					$rowsguest2 = $qguest2->result();
					$total_rowsguest2 = count($rowsguest2);
					
					$duration_max = 0;
					$distance_min = 9999;
					$host_time_max = 0;
					$limit_sec = 30*60; //120 menit durasi in rom
					
					for($h=0;$h<$total_rowsguest2;$h++)
					{
						$last_duration = $rowsguest2[$h]->radius_event_delta;
						$host_time = $rowsguest2[$h]->radius_host_time;
						
						$position_start_name = $rowsguest2[$h]->radius_host_location;
						$geofence_start_name = $rowsguest2[$h]->radius_host_location;
						$geofence_coord = $rowsguest2[$h]->radius_host_coord;
						$guest_device = $rowsguest2[$h]->radius_guest."@VT200";
						$last_distance = $rowsguest2[$h]->radius_meter;
						
						printf("===DURATION SEC :%s %s s %s meter\r\n", $host_time, $last_duration, $last_distance);
						if($last_duration > $duration_max)
						{
							$duration_max = $last_duration;
							$host_time_max = $host_time;
						}
						
						if($last_distance < $distance_min)
						{
							$distance_min = $last_distance;
							
						}
					}
							
					printf("===GUEST DURATION MAX %s to %s %s meter \r\n", $guest_device, $duration_max, $distance_min);
		
					
					//insert guest data with max sec
					//jika data data per guest
					if($total_rowsguest2 > 0)
					{	
									$host_device = $deviceid."@VT200";
									
									$vehicle_host = $this->getvehicle($host_device);
									$radius_report_host_vehicle_user_id = $vehicle_host->vehicle_user_id;
									$radius_report_host_vehicle_id = $vehicle_host->vehicle_id;
									$radius_report_host_vehicle_device = $vehicle_host->vehicle_device;
									$radius_report_host_vehicle_no = $vehicle_host->vehicle_no;
									$radius_report_host_vehicle_name = $vehicle_host->vehicle_name;
									$radius_report_host_vehicle_company = $vehicle_host->vehicle_company;
									$host_company_name = $this->getCompanyName($radius_report_host_vehicle_company);
									
									$vehicle_guest = $this->getvehicle($guest_device);
									if(isset($vehicle_guest))
									{
										$radius_report_guest_vehicle_user_id = $vehicle_guest->vehicle_user_id;
										$radius_report_guest_vehicle_id = $vehicle_guest->vehicle_id;
										$radius_report_guest_vehicle_device = $vehicle_guest->vehicle_device;
										$radius_report_guest_vehicle_no = $vehicle_guest->vehicle_no;
										$radius_report_guest_vehicle_name = $vehicle_guest->vehicle_name;
										$radius_report_guest_vehicle_company = $vehicle_guest->vehicle_company;
										$guest_company_name = $this->getCompanyName($radius_report_guest_vehicle_company);
										
									}
									else
									{
										$radius_report_guest_vehicle_user_id = "";
										$radius_report_guest_vehicle_id = 0;
										$radius_report_guest_vehicle_device = 0;
										$radius_report_guest_vehicle_no = 0;
										$radius_report_guest_vehicle_name = "";
										$radius_report_guest_vehicle_company = 0;
										$guest_host_name = "";
										
									}
									
									$radius_report_start_geofence = $geofence_start_name;
									$radius_report_start_coordinate = $geofence_coord;
									$radius_report_end_geofence = $geofence_start_name;
									$radius_report_end_coordinate = $geofence_coord;
									
									$radius_report_driver = 0;
									$radius_report_driver_name = "";
									
						
					
						if($duration_max < $limit_sec)
						{
									
									
									//jika hanya ada 1 data detected radius
									if($total_rowsguest2 == 1)
									{
										//get end time wita 
										$radius_report_end_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($host_time))); //sudah wita
										$delta = 0;
										$endtime_sec = strtotime($radius_report_end_time);
										
										//get start time (endtime - delta);
										$starttime_sec = $endtime_sec - $delta;
										$radius_report_start_time = date("Y-m-d H:i:s", $starttime_sec); //sudah wita
									}
									else
									{
										//get end time wita 
										$radius_report_end_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($host_time_max))); //sudah wita
										$delta = $duration_max;
										$endtime_sec = strtotime($radius_report_end_time);
										
										//get start time (endtime - delta);
										$starttime_sec = $endtime_sec - $delta;
										$radius_report_start_time = date("Y-m-d H:i:s", $starttime_sec); //sudah wita
									}
									printf("===START TIME : %s END TIME %s DURATION %s s TOTAL DATA: %s \r\n",$radius_report_start_time, $radius_report_end_time, $delta, $total_rowsguest2);
									
									

									
					
						}
						else if($duration_max > $limit_sec)
						{
						
									//jika hanya ada 1 data detected radius
									if($total_rowsguest2 == 1)
									{
										//get end time wita 
										$radius_report_end_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($host_time))); //sudah wita
										$delta = 0;
										$endtime_sec = strtotime($radius_report_end_time);
										
										//get start time (endtime - delta);
										$starttime_sec = $endtime_sec - $delta;
										$radius_report_start_time = date("Y-m-d H:i:s", $starttime_sec); //sudah wita
									}
									else
									{
										
										$time_first = strtotime($rowsguest2[0]->radius_host_time);
										$time_last = strtotime($rowsguest2[$total_rowsguest2-1]->radius_host_time);

										$delta_rev = $time_last - $time_first; //sec
										
										//get end time wita 
										$radius_report_end_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($host_time_max))); //sudah wita
										$delta = $delta_rev;
										$endtime_sec = strtotime($radius_report_end_time);
										
										//get start time (endtime - delta);
										$starttime_sec = $endtime_sec - $delta;
										$radius_report_start_time = date("Y-m-d H:i:s", $starttime_sec); //sudah wita
									}
									printf("!==START TIME : %s END TIME %s DURATION %s s TOTAL DATA: %s \r\n",$radius_report_start_time, $radius_report_end_time, $delta, $total_rowsguest2);
									
									
							
						}
						else
						{
							printf("XX INVALID DURATION %s s %s TOTAL DATA : %s \r\n",$duration_max, $host_time, $total_rowsguest2);
							
							
							
						}
						
									$duration = get_time_difference($radius_report_start_time, $radius_report_end_time);

										$start_1 = dbmaketime($radius_report_start_time);
										$end_1 = dbmaketime($radius_report_end_time);
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

									$radius_report_duration = $show;
									$radius_report_duration_sec = $duration_sec;
									
									
									$this->dbreport = $this->load->database("tensor_report",true);
									$datainsert["radius_report_host_vehicle_user_id"] = $radius_report_host_vehicle_user_id;
									$datainsert["radius_report_host_vehicle_id"] = $radius_report_host_vehicle_id;
									$datainsert["radius_report_host_vehicle_device"] = $radius_report_host_vehicle_device;
									$datainsert["radius_report_host_vehicle_no"] = $radius_report_host_vehicle_no;
									$datainsert["radius_report_host_vehicle_name"] = $radius_report_host_vehicle_name;
									$datainsert["radius_report_host_vehicle_company"] = $radius_report_host_vehicle_company;
									$datainsert["radius_report_host_vehicle_company_name"] = $host_company_name;
									
									$datainsert["radius_report_guest_vehicle_user_id"] = $radius_report_guest_vehicle_user_id;
									$datainsert["radius_report_guest_vehicle_id"] = $radius_report_guest_vehicle_id;
									$datainsert["radius_report_guest_vehicle_device"] = $radius_report_guest_vehicle_device;
									$datainsert["radius_report_guest_vehicle_no"] = $radius_report_guest_vehicle_no;
									$datainsert["radius_report_guest_vehicle_name"] = $radius_report_guest_vehicle_name;
									$datainsert["radius_report_guest_vehicle_company"] = $radius_report_guest_vehicle_company;
									$datainsert["radius_report_guest_vehicle_company_name"] = $guest_company_name;
									
									$datainsert["radius_report_start_time"] = $radius_report_start_time;
									$datainsert["radius_report_start_geofence"] = $radius_report_start_geofence;
									$datainsert["radius_report_start_coordinate"] = $radius_report_start_coordinate;
									$datainsert["radius_report_end_time"] = $radius_report_end_time;
									$datainsert["radius_report_end_geofence"] = $radius_report_end_geofence;
									$datainsert["radius_report_end_coordinate"] = $radius_report_end_coordinate;
									
									$datainsert["radius_report_date"] = date("Y-m-d", strtotime($radius_report_start_time));
									$datainsert["radius_report_hour"] = date("H:00:00",strtotime($radius_report_start_time));

									$datainsert["radius_report_driver"] = $radius_report_driver;
									$datainsert["radius_report_driver_name"] = $radius_report_driver_name;
									$datainsert["radius_report_duration"] = $radius_report_duration;
									$datainsert["radius_report_duration_sec"] = $radius_report_duration_sec;
									$datainsert["radius_report_distance"] = $distance_min;
									
									//get last data
									$this->dbts = $this->load->database("webtracking_ts",true); 
									$this->dbts->select("radius_report_id");
									$this->dbts->where("radius_report_host_vehicle_id", $radius_report_host_vehicle_id);
									$this->dbts->where("radius_report_start_time",$radius_report_start_time);
									$this->dbts->where("radius_report_guest_vehicle_id",$radius_report_guest_vehicle_id);
									
									//$this->dbts->limit(1);
									$q_last = $this->dbts->get("ts_radius_hour");
									$row_last = $q_last->row();
									$total_last = count($row_last);
									
									if($total_last>0){
										$this->dbts = $this->load->database("webtracking_ts",true); 
										$this->dbts->where("radius_report_host_vehicle_id", $radius_report_host_vehicle_id);
										$this->dbts->where("radius_report_start_time",$radius_report_start_time);
										$this->dbts->where("radius_report_guest_vehicle_id",$radius_report_guest_vehicle_id);
										//$this->dbts->limit(1);
										$this->dbts->update("ts_radius_hour",$datainsert);
										printf("===UPDATE OK \r\n ");
									}else{
										
										$this->dbts->insert("ts_radius_hour",$datainsert);
										printf("===INSERT OK \r\n");
									}
									
						
					}
					
					
					
			}		
					
		}		
		else
		{
			printf("===TIDAK ADA DATA RADIUS LEVEL #2 di : %s ! \r\n",$dbload);
		}
		
		return;
		
	}
	
	function getvehicle($vehicle_device)
	{

		$this->db = $this->load->database("default",true);
		$this->db->select("vehicle_id,vehicle_device,vehicle_type,vehicle_name,vehicle_no,vehicle_mv03,vehicle_user_id,
							vehicle_company,vehicle_dbname_live,vehicle_info");
		$this->db->order_by("vehicle_id", "desc");
		//$this->db->where("vehicle_status <>", 3);
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
	
	function autocheck_hour_summary($userid="",$orderby="",$date=""){
		
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
	
	function create_monthly_report($newdate="")
	{
		$start_time = date("d-m-Y H:i:s");
		//printf("==START : %s \r\n", $start_time); 
		
		if($newdate == ""){
			$newdate = date("Y-m-d", strtotime("tomorrow"));
		}
		
		//print_r($newdate);exit();
		$this->dbts = $this->load->database("webtracking_ts",true);
		
		$m1 = date("F", strtotime($newdate));
		$month = date("m", strtotime($newdate));
		$d1 = date("D", strtotime($newdate));
		$monthly_year = date("Y", strtotime($newdate));
		
		switch ($m1)
		{
			case "January":
			$month_name = "Januari";
			break;
			case "February":
			$month_name = "Februari";
			break;
			case "March":
            $month_name = "Maret";
			break;
			case "April":
            $month_name = "April";
			break;
			case "May":
			$month_name = "Mei";
			break;
			case "June":
			$month_name = "Juni";
			break;
			case "July":
            $month_name = "Juli";
			break;
			case "August":
            $month_name = "Agustus";
			break;
			case "September":
			$month_name = "September";
			break;
			case "October":
            $month_name = "Oktober";
			break;
			case "November":
			$month_name = "November";
			break;
			case "December":
			$month_name = "Desember";
			break;
		}
		
		switch ($d1)
		{
			case "Mon":
			$day = "Senin";
			break;
			case "Tue":
			$day = "Selasa";
			break;
			case "Wed":
            $day = "Rabu";
			break;
			case "Thu":
            $day = "Kamis";
			break;
			case "Fri":
			$day = "Jumat";
			break;
			case "Sat":
			$day = "Sabtu";
			break;
			case "Sun":
            $day = "Minggu";
			break;
		}
		
		//search day - 1
		//1 hari sebelum
		$date = new DateTime($newdate);
		$interval = new DateInterval('P1D');
		$date->sub($interval);
		$olddate = $date->format('Y-m-d');
		
		//print_r($newdate." | ".$olddate);exit();
		
				unset($data);
				$data["monthly_date"] = $newdate;
				$data["monthly_day"] = $day;
				$data["monthly_name"] = $month_name;
				$data["monthly_year"] = $monthly_year;
				
				//cek data exists				
				$this->dbts->limit(1);
				$this->dbts->where("monthly_date",$newdate);
				$q_configreport_exists = $this->dbts->get("ts_config_monthly_report");
				$configreport_exists = $q_configreport_exists->row();
				$total_configreport_exists = count($configreport_exists);
				
				//update
				if($total_configreport_exists > 0){
					$this->dbts->limit(1);
					$this->dbts->where("monthly_date",$newdate);
					$this->dbts->update("ts_config_monthly_report",$data);
					printf("==UPDATE - OK : %s \r\n", $newdate); 
				}else{
					//insert
					$this->dbts->insert("ts_config_monthly_report",$data);
					printf("==INSERT - OK : %s \r\n", $newdate); 
				}
				
		$finish_time = date("d-m-Y H:i:s");
		printf("==FINISH : %s \r\n", date("d-m-Y H:i:s")); 
	}
	
	function get_totalvehicle_opr($company,$date,$hour)
	{
		$group_selected = array(
							"STREET",
							"ROM",
							"PORT"
							
							);
							
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("location_report_id,location_report_vehicle_no");
        $this->dbts->order_by("location_report_id", "asc");
		$this->dbts->group_by("location_report_vehicle_no");
		$this->dbts->where("location_report_vehicle_company", $company);
		$this->dbts->where("location_report_gps_date", $date);
		$this->dbts->where("location_report_gps_hour", $hour);
		$this->dbts->where_in("location_report_group", $group_selected);
        $qh = $this->dbts->get("ts_location_hour");
		$rowh = $qh->result();
		
		$totalh = count($rowh);
		
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
        return $totalh;
		
		
	}
	
	function getshift_byhour($hour)
	{
		$shift_name = "-";
		$shift1 = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17");
        $shift2 = array("18", "19", "20", "21", "22", "23", "00", "01", "02", "03", "04", "05");
		
		if (in_array($hour, $shift1)){
			$shift_name = "1";
		}else if (in_array($hour, $shift2)){
			$shift_name = "2";
		}else{
			$shift_name = "-";
		}
		//print_r($shift_name);exit();
		return $shift_name;
	}
	
	//by location
	function autocheck_history_hour($userid="",$orderby="",$startdate=""){
		
		printf("===STARTING AUTOCHECK HISTORY HOURLY \r\n");
		$start_time = date("Y-m-d H:i:s");
		$this->dbts = $this->load->database("webtracking_ts", TRUE);
		$this->dbts->order_by("hour_time","asc");
		$this->dbts->select("hour_time");
		$this->dbts->where("hour_flag",0);
		$this->dbts->where("hour_reqstatus",1);
		$this->dbts->where("hour_user", $userid);
		$q = $this->dbts->get("ts_hour");
		$rows = $q->result();
		$totalrows = count($rows);
		printf("===TOTAL HOUR %s \r\n", $totalrows);
		$j = 1;
		for ($i=0;$i<$totalrows;$i++)
		{
			printf("TOTAL DATE HOUR : %s %s \r\n",$startdate,$rows[$i]->hour_time);
			$processdata = $this->data_history_hour($userid,$orderby,$startdate,$rows[$i]->hour_time);
		}
		$finish_time = date("Y-m-d H:i:s");
		
		//send telegram
		$title_name = "AUTOCHECK HISTORY PER HOUR ".$startdate;
			$message = urlencode(
					"".$title_name." \n".
					"Date: ".$startdate." \n".
					"Start Time: ".$start_time." \n".
					"End Time: ".$finish_time." \n"
				);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-671321211",$message); //autocheck hour
		printf("===SENT TELEGRAM OK\r\n");	
		
		
	}
	
	//ga jadi lemot ambil dari history
	function data_history_hour($userid,$orderby,$startdate,$starttime){
		
		ini_set('memory_limit', '2G');
        printf("PROSES AUTOCHECK HISTORY >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
	
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "location_";
        
        if ($startdate == "") {
            $startdate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d H:i:s", strtotime($startdate." ".$starttime));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
		
		$enddate = date("Y-m-d H:i:s", strtotime($startdate . "+5minutes"));
		
        /* 
        if ($enddate != "")
        {
            $enddate = date("Y-m-d H:i:s", strtotime($enddate." ".$endtime));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d H:i:s", strtotime("yesterday"));
        }
		 */
		if ($orderby == "") {
            $orderby = "asc";
        }
		
        //print_r($startdate." ".$enddate);exit();
		
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));  //wita
        $z =0;
		$vtype = array("VT200","VT200BIB");
		printf("START DATE - END DATE : %s %s \r\n", $sdate, $edate);
		$this->db->order_by("vehicle_id", $orderby);
		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_user_id", $userid);
		$this->db->where_in("vehicle_type", $vtype);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		
		
		$street_register = array(						"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
														
														"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
														
														"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
														"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
														"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
														"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31",
														
														"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
														"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
														"PORT BIB - Antrian","Port BIB - Antrian"
								);
														
		$port_register = array(							"BIB CP 1","BIB CP 7","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 2","BIB CP 6",
														"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
														"PORT BIB","PORT BIR","PORT TIA"
														   
														   );
														   
		$rom_register	 = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom","Non BIB Anzawara","Non BIB TBR/SDJ"
										  
														  );
													
		$pool_register = array(							"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
														"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT"
									);
									
									
		
									
        
		$this->dbtrans = $this->load->database("webtracking_ts",true); 
        for($x=0;$x<count($rowvehicle);$x++){
			
			printf("PROSES VEHICLE : %s %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_no, $rowvehicle[$x]->vehicle_id, $startdate, $rowvehicle[$x]->user_name, ++$z, $total_process);
			$cron_username = strtoupper($rowvehicle[$x]->user_name);
			$company_username = $rowvehicle[$x]->user_company;
            unset($data_insert);
            //PORT Only
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
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
						//$this->dbhist2 = $this->load->database("gpshistory_rhino",true);								
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);
						//$this->dbhist2 = $this->load->database("gpshistory_rhino",true);						
					}
					
					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
					$vehicle_type = $rowvehicle[$x]->vehicle_type;
						
					if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                    {
                        $tablehist = $vehicle_device[0]."@t5_gps";
                        $tablehistinfo = $vehicle_device[0]."@t5_info";    
                    }
                    else
                    {
						$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
						$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                    }
					
                        //$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)"); 
						$this->dbhist->order_by("gps_time","asc");
						$this->dbhist->group_by("gps_time");						
                        $this->dbhist->where("gps_name", $vehicle_device[0]);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
						$this->dbhist->where("gps_status", "A");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
						$this->dbhist2->order_by("gps_time","asc");
						$this->dbhist2->group_by("gps_time");						
                        $this->dbhist2->where("gps_name", $vehicle_device[0]);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);
					  	$this->dbhist2->where("gps_status", "A");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
					    $trows = count($rows);
						
                        printf("TOTAL DATA : %s \r\n",$trows);
						
						
						
						//detail data
						if ($trows > 0){
								
								$position = $this->getPosition_other($rows[0]->gps_longitude_real,$rows[0]->gps_latitude_real);
								$ex_lastposition = explode(",",$position->display_name);
								if(isset($position)){
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
									}else{
										$group = "OUT";
										$hauling = "out";
									}
								}else{
									$position_name = $position->display_name;
									$hauling = "out";
									$group = "OUT";
								}
								
								$direction = $rows[0]->gps_course;
								$jalur = $this->get_jalurname_new($direction);
								
								$gpsspeed_kph = $rows[0]->gps_speed*1.852;
								
								
								if($rows[0]->gps_status == "A"){
									$gps_status = "OK";
								}else{
									$gps_status = "NOT OK";
								}
								
								$rowgeofence = $this->getGeofence_location_live($rows[0]->gps_longitude_real, $rows[0]->gps_latitude_real, $rowvehicle[$x]->vehicle_dbname_live);
								//print_r($rowgeofence);exit();
								
								if($rowgeofence == false){
									$geofence_id = 0;
									$geofence_name = "";
									$geofence_speed = 0;
									$geofence_speed_muatan = "";
									$geofence_type = "";
									
								
								}else{
									$geofence_id = $rowgeofence->geofence_id;
									$geofence_name = $rowgeofence->geofence_name;
									$geofence_speed = $rowgeofence->geofence_speed;
									$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
									$geofence_type = $rowgeofence->geofence_type;
									
								}
								
								$location_report_vehicle_user_id = $rowvehicle[$x]->vehicle_user_id;
								$location_report_vehicle_id = $rowvehicle[$x]->vehicle_id;
								$location_report_vehicle_device = $rowvehicle[$x]->vehicle_device;
								$location_report_imei = $rowvehicle[$x]->vehicle_mv03;
								$location_report_vehicle_no = $rowvehicle[$x]->vehicle_no;
								$location_report_vehicle_name = $rowvehicle[$x]->vehicle_name;
								$location_report_vehicle_type = $rowvehicle[$x]->vehicle_type;
								$location_report_vehicle_company = $rowvehicle[$x]->vehicle_company;
								$location_report_company_name = $this->getCompanyName($location_report_vehicle_company);
								
								
								
								$location_report_speed = $gpsspeed_kph;
								$location_report_gpsstatus = $gps_status;
								$location_report_gps_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[0]->gps_time))); //sudah wita
								$location_report_gps_date = date("Y-m-d",strtotime($location_report_gps_time));
								$location_report_gps_hour = date("H",strtotime($location_report_gps_time));
								$location_report_geofence_id = $geofence_id;
								$location_report_geofence_name = $geofence_name;
								$location_report_geofence_type = $geofence_type;
								$location_report_jalur = $jalur;
								$location_report_direction = $rows[0]->gps_course;
								$location_report_location = $position_name;
								$location_report_coordinate = $rows[0]->gps_latitude_real.",".$rows[0]->gps_longitude_real;
								$location_report_odometer = $rows[0]->gps_odometer;
								$location_report_latitude = $rows[0]->gps_latitude_real;
								$location_report_longitude = $rows[0]->gps_longitude_real;
								$location_report_fuel_data = $rows[0]->gps_mvd;
								
								$location_report_gsm = "";
								$location_report_sat = "";
								$parse_msgori = explode("&&", $rows[0]->gps_msg_ori);
								$gps_looping_master = explode(",",$parse_msgori[1]);
								
								if(count($gps_looping_master)>0){
									
									if(isset($gps_looping_master[16])){
										if($gps_looping_master[16] > 31){
											$location_report_gsm = 31;
										}else{
											$location_report_gsm = $gps_looping_master[16];
										}
									}
									
									if(isset($gps_looping_master[9])){
										$location_report_sat = $gps_looping_master[9]; 
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
								$datainsert["location_report_location"] = $location_report_location;
								$datainsert["location_report_coordinate"] = $location_report_coordinate;
								$datainsert["location_report_latitude"] = $location_report_latitude;
								$datainsert["location_report_longitude"] = $location_report_longitude;
								$datainsert["location_report_odometer"] = $location_report_odometer;
								$datainsert["location_report_fuel_data"] = $location_report_fuel_data;
								$datainsert["location_report_gsm"] = $location_report_gsm;
								$datainsert["location_report_sat"] = $location_report_sat;
								$datainsert["location_report_hauling"] = $hauling;
								$datainsert["location_report_group"] = $group;
								
								//get last data
								$this->dbtrans = $this->load->database("webtracking_ts",true); 
								$this->dbtrans->where("location_report_vehicle_id", $location_report_vehicle_id);
								$this->dbtrans->where("location_report_gps_date",$location_report_gps_date);
								$this->dbtrans->where("location_report_gps_hour",$location_report_gps_hour);
								$q_last = $this->dbtrans->get("ts_location_hour");
								$row_last = $q_last->row();
								$total_last = count($row_last);
								
								if($total_last>0){
									$this->dbtrans = $this->load->database("webtracking_ts",true); 
									$this->dbtrans->where("location_report_vehicle_id", $location_report_vehicle_id);
									$this->dbtrans->where("location_report_gps_date",$location_report_gps_date);
									$this->dbtrans->where("location_report_gps_hour",$location_report_gps_hour);
									$this->dbtrans->update("ts_location_hour",$datainsert);
									printf("!==UPDATE OK \r\n ");
								}else{
									
									$this->dbtrans->insert("ts_location_hour",$datainsert);
									printf("===INSERT OK \r\n");
								}
								
								
							printf("============================================ \r\n");
							
						}
					
				}
			}
		}
	
	}
	
	function autocheck_overspeed($userid="", $order="asc")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
									//filter in location array HAULING, ROM, PORT 
									$street_onduty = array( "PORT BIB","PORT BIR","PORT TIA",
															//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
															"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST",
															"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
															//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
															//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS EST 32","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",
															
															"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
															"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
															"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
															"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31",
															
															"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
															"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
															"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
															"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
															"PORT BIB - Antrian","Port BIB - Antrian"
														);
		
		printf("===STARTING AUTOCHECK %s WIB %s WITA\r\n", $nowtime, $nowtime_wita);
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
			//$this->db->select("vehicle_id,vehicle_no,vehicle_device,vehicle_user_id,vehicle_name,vehicle_company,vehicle_autocheck");
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
				//print_r($json);exit();
				$telegram_group = $this->get_telegramgroup_overspeed($rowvehicle[$x]->vehicle_company);
				$vehicle_dblive = $rowvehicle[$x]->vehicle_dbname_live;
				
			
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
								$position_name = $location_report_location;
								
								
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
										$datainsert["location_report_location"] = $location_report_location;
										$datainsert["location_report_coordinate"] = $location_report_coordinate;
										$datainsert["location_report_latitude"] = $location_report_latitude;
										$datainsert["location_report_longitude"] = $location_report_longitude;
										$datainsert["location_report_odometer"] = $location_report_odometer;
										$datainsert["location_report_fuel_data"] = $location_report_fuel_data;
										$datainsert["location_report_gsm"] = $location_report_gsm;
										$datainsert["location_report_sat"] = $location_report_sat;
										/* $datainsert["location_report_hauling"] = $hauling;
										$datainsert["location_report_group"] = $group; */
										
										$ex_lastposition = explode(",",$location_report_location);
										$position_name = $ex_lastposition[0];
										
										printf("===Location : %s \r\n", $position_name);
										
								if (in_array($position_name, $street_onduty)){
									$skip_sent = 0;
									$rowgeofence = $this->getGeofence_location_live($json->auto_last_long, $json->auto_last_lat, $vehicle_dblive);
									$jalur = $location_report_jalur;
									$gpsspeed_kph = $location_report_speed;
									
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
									printf("===Speed : %s Limit : %s \r\n", $gpsspeed_kph, $geofence_speed_limit);
									
									if($gpsspeed_kph <= $geofence_speed_limit){
										$skip_sent = 1;
									}else{
										$skip_sent = 0;
									}
									
									if($geofence_speed_limit == 0){
										$skip_sent = 1;
									}else{
										$skip_sent = 0;
									}
									
									
									if($skip_sent == 0)
									{
										$gps_time = date("Y-m-d H:i:s", strtotime($json->auto_last_update));
										$vehicle_no = $location_report_vehicle_no;
										$vehicle_name = $location_report_vehicle_name;
										$gpsspeed_kph = $gpsspeed_kph-3;
										$geofence_speed_limit = $geofence_speed_limit-3;
										$driver_name = "";
										$coordinate = $location_report_latitude.",".$location_report_longitude;
										$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
										
										$title_name = "OVERSPEED TELEGRAM";
										$message = urlencode(
											"".$title_name." \n".
											"Time: ".$gps_time." \n".
											"Vehicle No: ".$vehicle_no." ".$vehicle_name." \n".
											"Driver: ".$driver_name." \n".
											"Position: ".$position_name." \n".
											"Coordinate: ".$url." \n".
											"Speed (kph): ".$gpsspeed_kph." \n".
											"Limit (kph): ".$geofence_speed_limit." \n".
											"Rambu: ".$geofence_name." \n".
											"Jalur: ".$jalur." \n"
											
											);
										//printf("===Message : %s \r\n", $message);
										sleep(2);		
										$sendtelegram = $this->telegram_direct($telegram_group,$message);
										printf("===SENT TELEGRAM OK\r\n");	
										
										
										//get last data
										$this->dbts = $this->load->database("webtracking_ts",true); 
										$this->dbts->where("location_report_vehicle_id", $location_report_vehicle_id);
										$this->dbts->where("location_report_gps_time",$location_report_gps_time);
									
										$q_last = $this->dbts->get("ts_autocheck_overspeed");
										$row_last = $q_last->row();
										$total_last = count($row_last);
										
										if($total_last>0){
											$this->dbts = $this->load->database("webtracking_ts",true); 
											$this->dbts->where("location_report_vehicle_id", $location_report_vehicle_id);
											$this->dbts->where("location_report_gps_time",$location_report_gps_time);
											
											$this->dbts->update("ts_autocheck_overspeed",$datainsert);
											printf("!==UPDATE OK \r\n ");
										}else{
											
											$this->dbts->insert("ts_autocheck_overspeed",$datainsert);
											printf("===INSERT OK \r\n");
										}
								
								
									}else{
										
										printf("X==SKIP SENT TELEGRAM\r\n");	
									}
								}else{
									printf("X==DI LUAR AREA OPERASIONAL \r\n");	
								}
									
			}
			
			
		}
		$finishtime = date("Y-m-d H:i:s");
		
		
		$this->db->close();
		$this->db->cache_delete_all();
		
		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH AUTOCHECK HOUR DATA %s to %s \r\n", $nowdate, $enddate);
		printf("============================== \r\n");

	}
	
	function get_time_difference($starttime, $endtime){
		
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
	
	function getCompanyName($companyid){
		
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
	
	function getCompanyAll($userid){
		
		$this->db = $this->load->database("default",true);
		$this->db->select("company_id,company_name");	
		$this->db->order_by("company_name", "asc");
		$this->db->where("company_created_by ", $userid);
		$this->db->where("company_flag ", 0);
		$q = $this->db->get("company");
		$rows = $q->result();
		
		return $rows;
	
	}
	
	function getLastDataWIM($vehicle,$gpstime_local){
		
		$limit_delta = 7200; // 2jam
		$truckID = $vehicle->vehicle_no;
		$this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("integrationwim_id,integrationwim_transactionID,integrationwim_truckID,integrationwim_penimbanganStartLocal,integrationwim_penimbanganStartUTC");	
		$this->dbreport->order_by("integrationwim_penimbanganStartLocal", "desc");
		$this->dbreport->where("integrationwim_truckID", $truckID);
		$this->dbreport->where("integrationwim_status", "ACTUAL");
		$this->dbreport->limit(1);
		$q = $this->dbreport->get("historikal_integrationwim_unit");
		$rows = $q->row();
		$total_rows = count($rows);
		
		if($total_rows > 0)
		{
			
			$gpstime_local2 = date("Y-m-d H:i:s", $gpstime_local);
			$gpstime_local2_sec = strtotime($gpstime_local2);
			
			$wimtime_local =  date("Y-m-d H:i:s", strtotime($rows->integrationwim_penimbanganStartLocal));
			$wimtime_local_sec = strtotime($wimtime_local);
			
			
			printf("WIM==COMPARE GPS LOCAL %s WIM LOCAL%s \r\n", $gpstime_local2, $wimtime_local);
			$delta_wim = $gpstime_local2_sec - $wimtime_local_sec;
			printf("WIM===DELTA WIM %s \r\n", $delta_wim);
			
			if($delta_wim < $limit_delta){
				printf("WIM===ADA DATA WIM %s %s %s GPS: %s \r\n", $rows->integrationwim_transactionID, $rows->integrationwim_truckID, $rows->integrationwim_penimbanganStartLocal, date("Y-m-d H:i:s", $gpstime_local));
				return false;
			}else{
				printf("WIM===DATA WIM > 2jam %s %s %s GPS: %s \r\n", $rows->integrationwim_transactionID, $rows->integrationwim_truckID, $rows->integrationwim_penimbanganStartLocal, date("Y-m-d H:i:s", $gpstime_local));
				
				//prepare insert
				$localinsertwim = $this->localinsertWIM_average($vehicle,$gpstime_local);
				return true;
			}
		}
		else
		{
			printf("WIM===NO DATA WIM ACTUAL! \r\n"); 
			//prepare insert (hold) tunggu master data Tare
			//$localinsertwim = $this->localinsertWIM_master($vehicle,$gpstime_local);
			return true;
		}
		
		$this->dbreport->close();
		printf("==================================== \r\n");
	
	}
	
	//masih hardcord menunggu master data tonase
	function localinsertWIM_master($vehicle,$gpstime_local){
		
		$gpstime_local2 = date("Y-m-d H:i:s", $gpstime_local);
		$gpstime_UTC = date("Y-m-d H:i:s", strtotime($gpstime_local2 . "-7hours"));
		
		$gpstime_local2_finish = date("Y-m-d H:i:s", strtotime($gpstime_local2 . "+2minutes"));
		$gpstime_UTC_finish = date("Y-m-d H:i:s", strtotime($gpstime_UTC . "+2minutes"));
		
		$unicode1 = substr($vehicle->vehicle_id, 6, 2);
		$unicode2 = date("is", strtotime($gpstime_local2_finish));
		$localtransID = "99".$unicode1."".$unicode2;
		$haulingContractor = explode(" ", $vehicle->vehicle_no);
		
		unset($data);
		$data["integrationwim_transactionID"] = $localtransID;
		$data["integrationwim_penimbanganStartUTC"] = $gpstime_UTC;
		$data["integrationwim_penimbanganStartLocal"] = $gpstime_local2;
		$data["integrationwim_penimbanganFinishUTC"] = $gpstime_UTC_finish;
		$data["integrationwim_penimbanganFinishLocal"] = $gpstime_local2_finish;
		$data["integrationwim_beratTiapGandar"] = "";
		$data["integrationwim_totalGandar"] = "";
		$data["integrationwim_gross"] = 45000;
		$data["integrationwim_tare"] = 15000;
		$data["integrationwim_netto"] = 30000;
		$data["integrationwim_averageSpeed"] = "";
		
		$data["integrationwim_weightBalance"] = "";
		$data["integrationwim_rfid"] = "";
		$data["integrationwim_noMesin"] = "";
		$data["integrationwim_noRangka"] = "";
		$data["integrationwim_truckType"] = "";
		$data["integrationwim_providerId"] = 4408;
		$data["integrationwim_truckID"] = $vehicle->vehicle_no;
		$data["integrationwim_haulingContractor"] = $haulingContractor[0];
		$data["integrationwim_status"] = "AVERAGE FMS";
		$data["integrationwim_truckImage"] = "";
		$data["integrationwim_created_date"] = date("Y-m-d H:i:s");
											
		$this->dbreport->insert("historikal_integrationwim_unit",$data);
		printf("===LOCAL INSERT MASTER OK=== %s \r\n", $localtransID);
		$this->dbreport->close();
		
	}
	
	
	function localinsertWIM_average($vehicle,$gpstime_local){
		
		$limit_delta = 7200; // 2jam
		$truckID = $vehicle->vehicle_no;
		$this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("integrationwim_id,integrationwim_transactionID,integrationwim_gross,integrationwim_tare,integrationwim_netto,integrationwim_truckID,integrationwim_penimbanganStartLocal,integrationwim_penimbanganStartUTC");	
		$this->dbreport->order_by("integrationwim_penimbanganStartLocal", "desc");
		$this->dbreport->where("integrationwim_truckID", $truckID);
		$this->dbreport->where("integrationwim_status", "ACTUAL");
		$this->dbreport->limit(10);
		$q = $this->dbreport->get("historikal_integrationwim_unit");
		$rows = $q->result();
		$total_rows = count($rows);
		
		$gross_new = 0;
		$tare_new = 0;
		$netto_new = 0;	
		for ($t=0;$t<$total_rows;$t++)
		{
			
			$gross_new = $gross_new + $rows[$t]->integrationwim_gross;
			$tare_new = $tare_new + $rows[$t]->integrationwim_tare;
			$netto_new = $netto_new + $rows[$t]->integrationwim_netto;
		}
		
		$gross_avg = round($gross_new / $total_rows,0);
		$tare_avg = round($tare_new / $total_rows,0);
		$netto_avg = round($netto_new / $total_rows,0);
		
		$gpstime_local2 = date("Y-m-d H:i:s", $gpstime_local);
		$gpstime_UTC = date("Y-m-d H:i:s", strtotime($gpstime_local2 . "-7hours"));
		
		$gpstime_local2_finish = date("Y-m-d H:i:s", strtotime($gpstime_local2 . "+2minutes"));
		$gpstime_UTC_finish = date("Y-m-d H:i:s", strtotime($gpstime_UTC . "+2minutes"));
		
		$unicode1 = substr($vehicle->vehicle_id, 6, 2);
		$unicode2 = date("is", strtotime($gpstime_local2_finish));
		$localtransID = "99".$unicode1."".$unicode2;
		$haulingContractor = explode(" ", $vehicle->vehicle_no);
		
		unset($data);
		$data["integrationwim_transactionID"] = $localtransID;
		$data["integrationwim_penimbanganStartUTC"] = $gpstime_UTC;
		$data["integrationwim_penimbanganStartLocal"] = $gpstime_local2;
		$data["integrationwim_penimbanganFinishUTC"] = $gpstime_UTC_finish;
		$data["integrationwim_penimbanganFinishLocal"] = $gpstime_local2_finish;
		$data["integrationwim_beratTiapGandar"] = "";
		$data["integrationwim_totalGandar"] = "";
		$data["integrationwim_gross"] = $gross_avg;
		$data["integrationwim_tare"] = $tare_avg;
		$data["integrationwim_netto"] = $netto_avg;
		$data["integrationwim_averageSpeed"] = "";
		$data["integrationwim_weightBalance"] = "";
		$data["integrationwim_rfid"] = "";
		$data["integrationwim_noMesin"] = "";
		$data["integrationwim_noRangka"] = "";
		$data["integrationwim_truckType"] = "";
		$data["integrationwim_providerId"] = 4408;
		$data["integrationwim_truckID"] = $vehicle->vehicle_no;
		$data["integrationwim_haulingContractor"] = $haulingContractor[0];
		$data["integrationwim_status"] = "AVERAGE FMS";
		$data["integrationwim_truckImage"] = "";
		$data["integrationwim_created_date"] = date("Y-m-d H:i:s");
											
		$this->dbreport->insert("historikal_integrationwim_unit",$data);
		printf("===LOCAL INSERT AVG OK=== %s \r\n", $localtransID);
		$this->dbreport->close();
		
		
	}
		
	function getlastROM_fromRitaseDBlive($device,$dblive){

		$this->dblive = $this->load->database($dblive,true);
		$this->dblive->order_by("ritase_gpstime","desc");
		$this->dblive->where("ritase_device", $device);
		$this->dblive->where("ritase_last_dest !=", "");
		$this->dblive->where("ritase_last_dest !=", "PORT BBC");
		$q = $this->dblive->get("ritase");
		$rows = $q->row();
		
		return $rows;
		
	}
	
	function device_alert($user="", $order="asc")
	{
		$nowdate = date('Y-m-d H:i:s');
		$offset=0;
		/*$dateinterval = new DateTime($nowdate);
		$dateinterval->sub(new DateInterval('PT1H'));
		$nowdate = $dateinterval->format('Y-m-d H:i:s');*/
		$this->dbtransporter = $this->load->database("transporter",true);
		
		printf("===Starting cron . . . at %s \r\n", $nowdate);
		printf("======================================\r\n");
		
			$this->db->order_by("vehicle_id","asc");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_user_id", $user);
			$q = $this->db->get("vehicle");
			
			if ($q->num_rows() == 0)
			{
				printf("==No Vehicles \r\n");
				//return;
			}
			
			$rows = $q->result();
			$totalvehicle = count($rows);
			
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				printf("===Process Check Last Info For %s %s (%d/%d) User : %s \n", $rows[$i]->vehicle_no, $rows[$i]->vehicle_device, $j, $totalvehicle, $user);
				printf("===execute %s\r\n", $rows[$i]->vehicle_no);
				
								// last position
								$vehicledevice = $rows[$i]->vehicle_device;
								
								$this->db->where("vehicle_status", 1);
								$this->db->where("vehicle_device", $vehicledevice);
								$qv = $this->db->get("vehicle");
							
								if ($qv->num_rows() == 0)
								{
									printf("===No Data \r\n");
								}
							
								$rowvehicle = $qv->row();
								$rowvehicles = $qv->result();
								
								list($name, $host) = explode("@", $rowvehicle->vehicle_device);
								
								$gps = $this->gpsmodel->GetLastInfo($name, $host, true, false, 0, $rowvehicle->vehicle_type);
								if ($this->gpsmodel->fromsocket)
								{
									$datainfo = $this->gpsmodel->datainfo;
									$fromsocket = $this->gpsmodel->fromsocket;			
								}
										
								if (! $gps)
								{
									printf("===Gps Belum Aktif \r\n");
								}

								$gtps = $this->config->item("vehicle_gtp");

								//$dir = $gps->direction-1;
								$dirs = $this->config->item("direction");

								if (in_array(strtoupper($rowvehicle->vehicle_type), $gtps))
								{
									if (! isset($datainfo))
									{
										if (isset($gps) && $gps && date("Ymd", $gps->gps_timestamp) >= date("Ymd"))
										{
											$tables = $this->gpsmodel->getTable($rowvehicle);
											$this->db = $this->load->database($tables["dbname"], TRUE);

										}
										else
										{	
											$devices = explode("@", $rowvehicle->vehicle_device);
											$tables['info'] = sprintf("%s@%s_info", strtolower($devices[0]), strtolower($devices[1]));
											$this->db = $this->load->database("gpshistory", TRUE);
										}
										
										// ambil informasi di gps_info
										
										$this->db->order_by("gps_info_time", "DESC");
										$this->db->where("gps_info_device", $rowvehicle->vehicle_device);
										$q = $this->db->get($tables['info'], 1, 0);
									}
										
									if ((! isset($datainfo)) && ($q->num_rows() == 0))
									{
										$engine = "OFF";
									}
									else
									{
										$rowinfo = isset($datainfo) ? $datainfo : $q->row();					
										$ioport = $rowinfo->gps_info_io_port;
											
										$status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
										$status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
										$status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
											
										$engine = $status1 ? "ON" : "OFF";
			
									}			

								}

								$this->db = $this->load->database("default", TRUE);
								
								$alert_note = "";
								$alert_type = 0;
								if(isset($gps->gps_timestamp)){
									
									$delta = ((mktime() - $gps->gps_timestamp) - 3600); //dikurangi 3600 detik karena error time
									
									//cek delay kurang dari 10 menit 
									if ($delta >= 3600 && $delta <= 43200) //lebih 60 menit kurang dari 12 jam //yellow condition
									{
										printf("===Vehicle No %s Tidak Update >= 60 menit \r\n", $rowvehicle->vehicle_no);
											$alert_note = "DELAY TIME >= 60 Minutes";
											$alert_type = "1";
											
											//tambahan plus 1 jam karena eror jam server
											$nowdate_gps = $gps->gps_time;
											$dateinterval = new DateTime($nowdate_gps);
											$dateinterval->add(new DateInterval('PT7H'));
											$nowdate_gps = $dateinterval->format('Y-m-d H:i:s');
											
											unset($data);
											$data["alert_vehicle_id"] = $rowvehicle->vehicle_id;
											$data["alert_vehicle_user"] = $rowvehicle->vehicle_user_id;
											$data["alert_vehicle_device"] = $rowvehicle->vehicle_device;
											$data["alert_vehicle_no"] = $rowvehicle->vehicle_no;
											$data["alert_vehicle_name"] = $rowvehicle->vehicle_name;
											$data["alert_vehicle_company"] = $rowvehicle->vehicle_company;
											$data["alert_vehicle_group"] = $rowvehicle->vehicle_group;
											$data["alert_type"] = $alert_type;
											$data["alert_note"] = $alert_note;
											$data["alert_starttime"] = $nowdate_gps;
											$data["alert_create"] = $nowdate;
											
											$this->dbtransporter->insert("device_alert",$data);
											printf("===INSERT === %s \r\n", $alert_note);
										
										
									}
									else if($delta >= 43201) //lebih dari 1 hari //red condition 
									{
										printf("===Vehicle No %s Tidak Update >= 1 Hari \r\n", $rowvehicle->vehicle_no);
										$alert_note = "DELAY TIME >= 1 Days";
										$alert_type = "2";
										
										//tambahan plus 1 jam karena eror jam server
										$nowdate_gps = $gps->gps_time;
										$dateinterval = new DateTime($nowdate_gps);
										$dateinterval->add(new DateInterval('PT7H'));
										$nowdate_gps = $dateinterval->format('Y-m-d H:i:s');
											
										unset($data);
											$data["alert_vehicle_id"] = $rowvehicle->vehicle_id;
											$data["alert_vehicle_user"] = $rowvehicle->vehicle_user_id;
											$data["alert_vehicle_device"] = $rowvehicle->vehicle_device;
											$data["alert_vehicle_no"] = $rowvehicle->vehicle_no;
											$data["alert_vehicle_name"] = $rowvehicle->vehicle_name;
											$data["alert_vehicle_company"] = $rowvehicle->vehicle_company;
											$data["alert_vehicle_group"] = $rowvehicle->vehicle_group;
											$data["alert_type"] = $alert_type;
											$data["alert_note"] = $alert_note;
											$data["alert_starttime"] = $nowdate_gps;
											$data["alert_create"] = $nowdate;
											
											$this->dbtransporter->insert("device_alert",$data);
											printf("===INSERT === %s \r\n", $alert_note);
										
									}
									else
									{
										if($gps->gps_status == "V"){
											printf("===Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
											$alert_note = "GPS NOT OK";
											$alert_type = "3";
											
											//tambahan plus 1 jam karena eror jam server
											$nowdate_gps = $gps->gps_time;
											$dateinterval = new DateTime($nowdate_gps);
											$dateinterval->add(new DateInterval('PT7H'));
											$nowdate_gps = $dateinterval->format('Y-m-d H:i:s');
											
										unset($data);	
											$data["alert_vehicle_id"] = $rowvehicle->vehicle_id;
											$data["alert_vehicle_user"] = $rowvehicle->vehicle_user_id;
											$data["alert_vehicle_device"] = $rowvehicle->vehicle_device;
											$data["alert_vehicle_no"] = $rowvehicle->vehicle_no;
											$data["alert_vehicle_name"] = $rowvehicle->vehicle_name;
											$data["alert_vehicle_company"] = $rowvehicle->vehicle_company;
											$data["alert_vehicle_group"] = $rowvehicle->vehicle_group;
											$data["alert_type"] = $alert_type;
											$data["alert_note"] = $alert_note;
											$data["alert_starttime"] = $nowdate_gps;
											$data["alert_create"] = $nowdate;
											
											$this->dbtransporter->insert("device_alert",$data);
											printf("===INSERT === %s \r\n", $alert_note);
											
											
										}else{
											printf("===GPS UPDATE=== %s \r\n", $rowvehicle->vehicle_no);	
										}
									}
								}else{
									printf("===NO DATA=== \r\n");	
								}
								
								
				$j++;
			}
		
			
		
		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbtransporter->close();
		$this->dbtransporter->cache_delete_all();
		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH Cron start %s to %s \r\n", $nowdate, $enddate);
		printf("============================== \r\n");

	}
	
	function get_username($id) 
	{	
		$this->db->select("user_name");
		$this->db->where("user_id",$id);
		$q = $this->db->get("user");
		$data = $q->row();
		
        return $data;
		
    }
	
	function get_command($type,$port,$operator,$dblive) 
	{	
		//total produk
		$this->db->order_by("restart_id","asc");
		$this->db->where("restart_type",$type);
		$this->db->where("restart_port",$port);
		$this->db->like("restart_operator",$operator);
		if($type == "T5"){
			$this->db->where("restart_live",$dblive);
		}
		$q = $this->db->get("sms_restart");
		$data = $q->row();
		
        return $data;
		
    }
	
	function get_command_log($device,$nowdate,$limit) 
	{	
		
		$this->db->order_by("log_id","desc");
		$this->db->where("log_device",$device);
		$this->db->where("log_date",date("Y-m-d", strtotime($nowdate)));
		$this->db->where("log_flag",0);
		$q = $this->db->get("sms_restart_log");
		$data = $q->result();
		$total_data = count($data);
	
		//update flag jadi 0 -> for next running 
		$command_reset = $this->reset_command_log($device,$nowdate);
		
		return $total_data;
		
    }
	
	function reset_command_log($device,$nowdate) 
	{	
		$status = false;
		$this->db->order_by("log_id","desc");
		$this->db->where("log_device",$device);
		$this->db->where("log_date",date("Y-m-d", strtotime($nowdate)));
		$this->db->where("log_flag",0);
		$q = $this->db->get("sms_restart_log");
		$data = $q->result();
		$total_data = count($data);
		
		if($total_data > 0){
			
			$data_status["log_flag"] = 1;
			$this->db->where("log_device",$device);
			if($this->db->update("sms_restart_log", $data_status));
			{
				$status = true;
			}
			return $status;
		}
	}
	
	function getPosition($longitude, $ew, $latitude, $ns){
        $gps_longitude_real = getLongitude($longitude, $ew);
        $gps_latitude_real = getLatitude($latitude, $ns);
        
        $gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
        $gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");    
		$georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
        
        return $georeverse;
    }
	
	function getPosition_other($longitude, $latitude)
	{
		$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);	
		return $georeverse;
	}
	
	function sendnotif()
	{
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("user_id","asc");	
		$this->db->where("user_telegroup !=",0);
		$q = $this->db->get("user");
		$rows = $q->result();
		for ($i=0;$i<count($rows);$i++)
		{
			$username = $rows[$i]->user_name;
			$userid = $rows[$i]->user_id;
			printf("===USER NAME %s \r\n", $username);
			//start telegram autocheck //
			$view_autocheck = 0;
			$message = "No data information";
			$this->db->where("auto_user_id",$rows[$i]->user_id);
			$this->db->where("auto_flag",0);
			$q_auto = $this->db->get("vehicle_autocheck");
			$row_auto = $q_auto->result();
			$total_auto = count($row_auto); 
			if($total_auto > 0){
				$view_autocheck = 1;
				$total_k = 0;
				$total_m = 0;
				$total_p = 0;
				
				for($j=0;$j<count($row_auto);$j++){
					if($row_auto[$j]->auto_status == "K"){
						$total_k = $total_k + 1;
					}
					if($row_auto[$j]->auto_status == "M"){
						$total_m = $total_m + 1;
					}
					if($row_auto[$j]->auto_status == "P"){
						$total_p = $total_p + 1;
					}
					//$last_checked = date("d-m-Y H:i", strtotime($row_auto[$j]->auto_last_check));
				}
				
				$this->db->select("auto_last_check");
				$this->db->order_by("auto_last_check","desc");
				$this->db->where("auto_user_id",$rows[$i]->user_id);
				$this->db->where("auto_flag",0);
				$this->db->limit(1);
				$q_last_check = $this->db->get("vehicle_autocheck");
				$row_last_check = $q_last_check->row();
				if(count($row_last_check)>0){
					$last_checked = date("d-m-Y H:i", strtotime($row_last_check->auto_last_check));
				}
				
				$message =  urlencode(
							"TOTAL:".$total_auto." \n".
							"H:".$total_p." \n".
							"K:".$total_k." \n".
							"M:".$total_m." \n".
							"CHECKED: ".$last_checked);
				//print_r($message);exit();
				//end auto check //
				if($view_autocheck == 1){
					$sendtelegram = $this->telegram($userid,$message);
					printf("===SENT TELEGRAM OK %s \r\n", $username);
				}
				
			}else{
				printf("===SKIP %s \r\n", $rows[$i]->user_name);
			}
			
		}
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
	
	function wim_updatetime($vehicle,$location,$gpstime){
		$this->db = $this->load->database("default",TRUE);
		$this->db->select("vehicle_id,vehicle_no");
		$this->db->where("vehicle_id",$vehicle->vehicle_id);
		$qvehicle = $this->db->get("vehicle");
		$rvehicle = $qvehicle->row();
		if(count($rvehicle)>0){
			
			unset($datawim);
			$datawim["vehicle_wim_etime"] = $gpstime;
			
			$this->db->where("vehicle_id", $vehicle->vehicle_id);
			$this->db->limit(1);
			$this->db->update("vehicle", $datawim);
			printf("===WIM EndTime Updated === %s %s %s \r\n", $vehicle->vehicle_no,$location,$gpstime);
			return;
			
		}else{
			printf("===No Data Vehicle === %s %s %s \r\n", $vehicle->vehicle_no,$location,$gpstime);
			return;
		}
				
		
	}
	
	function port_lastdata($vehicle,$location,$gpstime){
		$this->db = $this->load->database("default",TRUE);
		$this->db->select("vehicle_id,vehicle_no");
		$this->db->where("vehicle_id",$vehicle->vehicle_id);
		$qvehicle = $this->db->get("vehicle");
		$rvehicle = $qvehicle->row();
		if(count($rvehicle)>0){
			
			unset($dataport);
			$dataport["vehicle_port_time"] = $gpstime;
			$dataport["vehicle_port_name"] = $location;
			
			$this->db->where("vehicle_id", $vehicle->vehicle_id);
			$this->db->limit(1);
			$this->db->update("vehicle", $dataport);
			printf("P==PORT EndTime Updated === %s %s %s \r\n", $vehicle->vehicle_no,$location,$gpstime);

			return;
			
		}else{
			printf("===No Data Vehicle === %s %s %s \r\n", $vehicle->vehicle_no,$location,$gpstime);
			
			return;
		}
				
		
	}
	
	function rom_lastdata($vehicle,$location,$gpstime){
		$this->db = $this->load->database("default",TRUE);
		$this->db->select("vehicle_id,vehicle_no");
		$this->db->where("vehicle_id",$vehicle->vehicle_id);
		$qvehicle = $this->db->get("vehicle");
		$rvehicle = $qvehicle->row();
		if(count($rvehicle)>0){
			
			unset($dataport);
			$dataport["vehicle_rom_time"] = $gpstime;
			$dataport["vehicle_rom_name"] = $location;
			
			$this->db->where("vehicle_id", $vehicle->vehicle_id);
			$this->db->limit(1);
			$this->db->update("vehicle", $dataport);
			printf("R==ROM EndTime Updated === %s %s %s \r\n", $vehicle->vehicle_no,$location,$gpstime);
			
			return;
			
		}else{
			printf("===No Data Vehicle === %s %s %s \r\n", $vehicle->vehicle_no,$location,$gpstime);
			
			return;
			
		}
				
		
	}
	
	function outofport_check_bk_no_wa($userid="",$orderby=""){
		
		$nowtime = date("Y-m-d H:i:s");
		$nowdate = date("Y-m-d 00:00:00");
		printf("===================== \r\n");
		printf("===STARTINGA OUT OF HAULING Now %s startdate %s \r\n", $nowtime, $nowdate);
		$this->db = $this->load->database("default",true); 
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_company,vehicle_rom_time,vehicle_rom_name,vehicle_port_time,vehicle_dbname_live");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_rom_time >= ", $nowdate);
		$this->db->where("vehicle_status <>", 3);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			$limit_sec = 3600; //1jam
			$limit_sec2 = 3600+1300; //1jam 30 menit
			
			//filter in location array HAULING, ROM, PORT 
										$street_onlocation = array( 
																
																"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
																"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
																
																"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
																"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
																"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
																"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31.5"
																
															);
			
			
			for($i=0;$i<$total_rows;$i++)
			{
				$no_urut = $i+1;
				printf("===PROSES DB LIVE: %s (%s of %s) \r\n", $rows[$i]->vehicle_no, $no_urut, $total_rows);
				$devices = explode("@", $rows[$i]->vehicle_device);
				$vehicle_dblive = $rows[$i]->vehicle_dbname_live;
				$vehicle_imei = $devices[0];
				$vehicle_no = $rows[$i]->vehicle_no;
				$vehicle_company = $rows[$i]->vehicle_company;
				
				$vehicle_rom_time = date("Y-m-d H:i:s", strtotime($rows[$i]->vehicle_rom_time . "-7hours"));
				printf("===UNIT MASUK KE ROM PADA  : %s  %s \r\n",$rows[$i]->vehicle_rom_time, $rows[$i]->vehicle_rom_name);
				
				$this->dblive = $this->load->database($vehicle_dblive,true);
				$this->dblive->select("ritase_device,ritase_gpstime,ritase_last_dest");	
				$this->dblive->order_by("ritase_gpstime", "desc");
				$this->dblive->where("ritase_device", $vehicle_imei);
				$this->dblive->where("ritase_gpstime >",$vehicle_rom_time);
				$this->dblive->limit(1);
				$qport = $this->dblive->get("ritase");
				$rowport = $qport->row();
				$totalport = count($rowport);
				
				$this->dblive->close();
				$this->dblive->cache_delete_all();
			
				if($totalport > 0){
					$lastporttime = date("Y-m-d H:i:s", strtotime($rowport->ritase_gpstime . "+7hours"));
					$lastportname = $rowport->ritase_last_dest;
					printf("===UNIT SUDAH MASUK KE PORT PADA  : %s  %s \r\n",$lastporttime, $lastportname);
					
				}else{
					printf("===NO DATA IN PORT : %s  \r\n", $rows[$i]->vehicle_no);
					$lastposition = $this->getlastposition_fromDBLive($vehicle_imei,$vehicle_dblive);
					
					if(count($lastposition)>0){
						$lastromname = $rows[$i]->vehicle_rom_name;
						$lastromtime = $rows[$i]->vehicle_rom_time; 
						$lastposition_name = $this->getPosition_other($lastposition->gps_longitude_real, $lastposition->gps_latitude_real);
						$lastposition_time = date("Y-m-d H:i:s", strtotime($lastposition->gps_time . "+7hours"));
						$speed = number_format($lastposition->gps_speed*1.852, 0, "", ".");
						
						$lastromtime_sec = strtotime($lastromtime);
						$lastposition_time_sec = strtotime($lastposition_time);
						
						$delta_rom_to_port = $lastposition_time_sec - $lastromtime_sec;
						$duration = get_time_difference($lastromtime, $lastposition_time);
							
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
									
						printf("===DELTA ROM TO PORT : %s  %s \r\n", $delta_rom_to_port, $show);
						
						//karena banyak data lompat semntara di range 1 - 1.30 jam
						if($delta_rom_to_port > $limit_sec && $delta_rom_to_port < $limit_sec2 ){
							printf("===PREPARE SEND ALERT !!\r\n");
							$coordinate = $lastposition->gps_latitude_real.",".$lastposition->gps_longitude_real;
							
							$company_telegram = $this->getTelegramID_nonbib($vehicle_company);
							if(count($company_telegram)>0){
								$telegram_geofence = $company_telegram->company_telegram_geofence;
								//$telegram_geofence = "-657527213"; //FMS TESTING
							}else{
								$telegram_geofence = "-657527213"; //FMS TESTING
							}
							
							$location = "-";
							if(isset($lastposition_name)){
								$ex_lastposition_name = explode(",",$lastposition_name->display_name);
								$street_name = $ex_lastposition_name[0];
								$location = $street_name;
								
								// in HAULING
								if (in_array($street_name, $street_onlocation)){
									$hauling = "in";
									
									$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
							
									//send telegram
									$title_name = "INFORMASI 1 JAM BELUM MASUK PORT";
									$message = urlencode(
										"".$title_name." \n".
										"Vehicle No: ".$vehicle_no." \n".
										"GPS Time: ".$lastposition_time." \n".
										"Location: ".$location." \n".
										"Speed (kph): ".$speed." \n".
										"Last ROM: ".$lastromname." ".$lastromtime." \n".
										"Duration: ".$show.""." \n".
										"Coordinate: ".$url." \n"
																				
										);
										sleep(2);		
										$sendtelegram = $this->telegram_direct($telegram_geofence,$message);
										printf("===SENT TELEGRAM OK\r\n");
									
								}else{
												
									$hauling = "out";
									printf("X==DI LUAR HAULING \r\n");
								}
								
							}
							
							
							
						}else{
							printf("X==MASIH BATAS WAJAR !!\r\n");
							
						}
						
					}else{
						
						printf("X==NO DATA IN DB LIVE !!\r\n");
					}
						
				}
				
				printf("===================== \r\n");
				
			}
			
		
		}
		
		$finishtime = date("Y-m-d H:i:s");
		
		//send telegram 
		$cron_name = "OUT OF PORT (1HOUR)";
		
		$message =  urlencode(
			"".$cron_name." \n".
			"Start: ".$nowtime." \n".
			"Finish: ".$finishtime." \n"
			);
											
		$sendtelegram = $this->telegram_direct("-671321211",$message); //autocheck hour
		printf("===SENT TELEGRAM OK\r\n");
	
		printf("=====FINISH %s %s=========== \r\n", $nowtime, $finishtime);
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function outofport_check($userid="",$orderby=""){
		
		$nowtime = date("Y-m-d H:i:s");
		$nowdate = date("Y-m-d 00:00:00");
		printf("===================== \r\n");
		printf("===STARTING OUT OF HAULING Now %s startdate %s \r\n", $nowtime, $nowdate);
		$this->db = $this->load->database("default",true); 
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_company,vehicle_rom_time,vehicle_rom_name,vehicle_port_time,vehicle_dbname_live");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_rom_time >= ", $nowdate);
		$this->db->where("vehicle_status <>", 3);
		
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			$limit_sec = 3600; //1jam
			$limit_sec2 = 3600+1300; //1jam 30 menit
			
			$street_onlocation = $this->config->item('street_rombib_autocheck');
		
			/* $street_onlocation = array( 
																
																"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
																"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
																
																"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5","KM 6","KM 6.5","KM 7",
																"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
																"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
																"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31.5"
																
			); */
			
			$wa_token = $this->getWAToken($userid);
			
			for($i=0;$i<$total_rows;$i++)
			{
				$no_urut = $i+1;
				printf("===PROSES DB LIVE: %s (%s of %s) \r\n", $rows[$i]->vehicle_no, $no_urut, $total_rows);
				$devices = explode("@", $rows[$i]->vehicle_device);
				$vehicle_dblive = $rows[$i]->vehicle_dbname_live;
				$vehicle_imei = $devices[0];
				$vehicle_no = $rows[$i]->vehicle_no;
				$vehicle_company = $rows[$i]->vehicle_company;
				
				$vehicle_rom_time = date("Y-m-d H:i:s", strtotime($rows[$i]->vehicle_rom_time . "-7hours"));
				printf("===UNIT MASUK KE ROM PADA  : %s  %s \r\n",$rows[$i]->vehicle_rom_time, $rows[$i]->vehicle_rom_name);
				
				$this->dblive = $this->load->database($vehicle_dblive,true);
				$this->dblive->select("ritase_device,ritase_gpstime,ritase_last_dest");	
				$this->dblive->order_by("ritase_gpstime", "desc");
				$this->dblive->where("ritase_device", $vehicle_imei);
				$this->dblive->where("ritase_gpstime >",$vehicle_rom_time);
				$this->dblive->limit(1);
				$qport = $this->dblive->get("ritase");
				$rowport = $qport->row();
				$totalport = count($rowport);
				
				$this->dblive->close();
				$this->dblive->cache_delete_all();
			
				if($totalport > 0){
					$lastporttime = date("Y-m-d H:i:s", strtotime($rowport->ritase_gpstime . "+7hours"));
					$lastportname = $rowport->ritase_last_dest;
					printf("===UNIT SUDAH MASUK KE PORT PADA  : %s  %s \r\n",$lastporttime, $lastportname);
					
				}else{
					printf("===NO DATA IN PORT : %s  \r\n", $rows[$i]->vehicle_no);
					$lastposition = $this->getlastposition_fromDBLive($vehicle_imei,$vehicle_dblive);
					
					if(count($lastposition)>0){
						$lastromname = $rows[$i]->vehicle_rom_name;
						$lastromtime = $rows[$i]->vehicle_rom_time; 
						$lastposition_name = $this->getPosition_other($lastposition->gps_longitude_real, $lastposition->gps_latitude_real);
						$lastposition_time = date("Y-m-d H:i:s", strtotime($lastposition->gps_time . "+7hours"));
						$speed = number_format($lastposition->gps_speed*1.852, 0, "", ".");
						
						$lastromtime_sec = strtotime($lastromtime);
						$lastposition_time_sec = strtotime($lastposition_time);
						
						$delta_rom_to_port = $lastposition_time_sec - $lastromtime_sec;
						$duration = get_time_difference($lastromtime, $lastposition_time);
							
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
									
						printf("===DELTA ROM TO PORT : %s  %s \r\n", $delta_rom_to_port, $show);
						
						//karena banyak data lompat semntara di range 1 - 1.30 jam
						if($delta_rom_to_port > $limit_sec && $delta_rom_to_port < $limit_sec2 ){
							printf("===PREPARE SEND ALERT !!\r\n");
							$coordinate = $lastposition->gps_latitude_real.",".$lastposition->gps_longitude_real;
							
							$company_telegram = $this->getTelegramID_nonbib($vehicle_company);
							if(count($company_telegram)>0){
								$telegram_geofence = $company_telegram->company_telegram_geofence;
								//$telegram_geofence = "-657527213"; //FMS TESTING
							}else{
								$telegram_geofence = "-657527213"; //FMS TESTING
							}
							
							$location = "-";
							if(isset($lastposition_name)){
								$ex_lastposition_name = explode(",",$lastposition_name->display_name);
								$street_name = $ex_lastposition_name[0];
								$location = $street_name;
								
								// in HAULING
								if (in_array($street_name, $street_onlocation)){
									$hauling = "in";
									
									$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
									
									
									$notif_wa_1hrport = $this->sendnotif_wa_1hrport($wa_token,$vehicle_no,$lastposition_time,$location,$speed,$lastromname,$lastromtime,$show,$url,$company_telegram);
							
									//send telegram
									$title_name = "INFORMASI 1 JAM BELUM MASUK PORT";
									$message = urlencode(
										"".$title_name." \n".
										"Vehicle No: ".$vehicle_no." \n".
										"GPS Time: ".$lastposition_time." \n".
										"Location: ".$location." \n".
										"Speed (kph): ".$speed." \n".
										"Last ROM: ".$lastromname." ".$lastromtime." \n".
										"Duration: ".$show.""." \n".
										"Coordinate: ".$url." \n"
																				
										);
										sleep(2);		
										$sendtelegram = $this->telegram_direct($telegram_geofence,$message);
										printf("===SENT TELEGRAM OK\r\n");
									
								}else{
												
									$hauling = "out";
									printf("X==DI LUAR HAULING \r\n");
								}
								
							}
							
							
							
						}else{
							printf("X==MASIH BATAS WAJAR !!\r\n");
							
						}
						
					}else{
						
						printf("X==NO DATA IN DB LIVE !!\r\n");
					}
						
				}
				
				printf("===================== \r\n");
				
			}
			
		
		}
		
		$finishtime = date("Y-m-d H:i:s");
		
		//send telegram 
		$cron_name = "OUT OF PORT (1HOUR)";
		
		$message =  urlencode(
			"".$cron_name." \n".
			"Start: ".$nowtime." \n".
			"Finish: ".$finishtime." \n"
			);
											
		$sendtelegram = $this->telegram_direct("-671321211",$message); //autocheck hour
		printf("===SENT TELEGRAM OK\r\n");
	
		printf("=====FINISH %s %s=========== \r\n", $nowtime, $finishtime);
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function getlastposition_fromDBLive($imei,$dblive){
		
		$this->dblive = $this->load->database($dblive,true);
		$this->dblive->select("gps_name,gps_time,gps_latitude_real,gps_longitude_real,gps_speed,
							   gps_status,gps_course,vehicle_autocheck");	
		$this->dblive->order_by("gps_time", "desc");
		$this->dblive->where("gps_name", $imei);
		$this->dblive->limit(1);
		$qpost = $this->dblive->get("gps");
		$rowpost = $qpost->row();
		
		$this->dblive->close();
		$this->dblive->cache_delete_all();
		
		return $rowpost;
		
	}
	
	function outofhauling_alert($vehicle,$location,$gpstime,$coordinate,$speed){
		
		$limit_delta = 30*60; // 30 menit
		$truckID = $vehicle->vehicle_id;
		$this->dbdefault = $this->load->database("default",true);
		$this->dbdefault->select("vehicle_id,vehicle_no,vehicle_rom_time,vehicle_rom_name,vehicle_port_time,vehicle_port_name");	
		$this->dbdefault->order_by("vehicle_id", "desc");
		$this->dbdefault->where("vehicle_id", $truckID);
		$q = $this->dbdefault->get("vehicle");
		$row = $q->row();
		$total_rows = count($row);
		
		if($total_rows >0){
			
			$last_port_name = $row->vehicle_port_name;
			$last_port_time = $row->vehicle_port_time;
			$last_port_date = date("Y-m-d", strtotime($last_port_time));
			$last_rom_name = $row->vehicle_rom_name;
			$last_rom_time = $row->vehicle_rom_time;
			$last_rom_date = date("Y-m-d", strtotime($last_rom_time));
			$last_rom_time_sec = strtotime($last_rom_time);
			$nowtime_sec = strtotime($gpstime);
			$nowdate_gps = date("Y-m-d",strtotime($gpstime));
			$delta_out = $nowtime_sec - $last_rom_time_sec;
			$delta_min = round($delta_out/60,0);
			
			printf("===LAST ROM %s %s VS NOW %s %s \r\n", $last_rom_name, $last_rom_time, $location, $gpstime);
			
			//khusus data hari ini
			if($last_port_date == $nowdate_gps && $last_rom_date == $nowdate_gps){
				
				if($last_rom_time !=  "" && $last_port_time  != ""){
				
					//hitung durasi mulai aktif jika unit sudah ke PORT kemudian ke ROM
					if($last_rom_time > $last_port_time){
						
						if($delta_out > $limit_delta){
							
							//$duration_alert = $this->get_time_difference( date("Y-m-d H:i:s",strtotime($last_rom_time)), $gpstime);
							$duration = get_time_difference($last_rom_time, $gpstime);
							
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
									
									
							$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
							printf("!==OUT OF HAULING ALERT DETECTED !!=== %s \r\n", $delta_out);
											
											//send telegram
											$title_name = "OUT OF HAULING DETECTED!!";
													$message = urlencode(
																"".$title_name." \n".
																"Time: ".$gpstime." \n".
																"Vehicle No: ".$vehicle->vehicle_no." \n".
																"Speed: ".$speed." kph"." \n".
																"Last ROM Name: ".$last_rom_name." ".$last_rom_time." \n".
																//"Last ROM Time: ".$last_rom_time." \n".
																"Last PORT Name: ".$last_port_name." ".$last_port_time." \n".
																//"Last PORT Time: ".$last_port_time." \n".
																"Duration: ".$show.""." \n".
																"Location: ".$location." \n".
																"Coordinate: ".$url." \n"
																
																
																);
													sleep(2);		
													$sendtelegram = $this->telegram_direct("-657527213",$message); //telegram FMS TESTING
													printf("===SENT TELEGRAM OK\r\n");	//exit();
						}else{
							
							printf("===MASIH AMAN === %s \r\n", $delta_out);
						}
					}else{
						
						printf("===Unit sudah lewati PORT === %s \r\n", $delta_out);
					}
					
					
				}else{
					
					printf("X==LAST ROM/PORT IS NULL === %s \r\n", $delta_out);
				}
			}else{
				printf("X==BUKAN DATA HARI INI === PORT %s ROM %s NOW %s \r\n", $last_port_date, $last_rom_date, $nowdate_gps);
			}
			
		}
		
		/* unset($data);
		$data["integrationwim_transactionID"] = $localtransID;
		$data["integrationwim_penimbanganStartUTC"] = $gpstime_UTC;
		$data["integrationwim_penimbanganStartLocal"] = $gpstime_local2;
		$data["integrationwim_penimbanganFinishUTC"] = $gpstime_UTC_finish;
		$data["integrationwim_penimbanganFinishLocal"] = $gpstime_local2_finish;
		$data["integrationwim_beratTiapGandar"] = "";
		$data["integrationwim_totalGandar"] = "";
		$data["integrationwim_gross"] = $gross_avg;
		$data["integrationwim_tare"] = $tare_avg;
		$data["integrationwim_netto"] = $netto_avg;
		$data["integrationwim_averageSpeed"] = "";
		$data["integrationwim_weightBalance"] = "";
		$data["integrationwim_rfid"] = "";
		$data["integrationwim_noMesin"] = "";
		$data["integrationwim_noRangka"] = "";
		$data["integrationwim_truckType"] = "";
		$data["integrationwim_providerId"] = 4408;
		$data["integrationwim_truckID"] = $vehicle->vehicle_no;
		$data["integrationwim_haulingContractor"] = $haulingContractor[0];
		$data["integrationwim_status"] = "AVERAGE FMS";
		$data["integrationwim_truckImage"] = "";
		$data["integrationwim_created_date"] = date("Y-m-d H:i:s");
											
		$this->dbreport->insert("historikal_integrationwim_unit",$data);
		printf("===LOCAL INSERT AVG OK=== %s \r\n", $localtransID);
		$this->dbreport->close(); */
		
		
	}
	
	function outofport_alert($vehicle,$location,$gpstime,$coordinate,$speed){
		
		$limit_delta = 3600*2; // 2 jam
		$truckID = $vehicle->vehicle_id;
		$this->dbdefault = $this->load->database("default",true);
		$this->dbdefault->select("vehicle_id,vehicle_no,vehicle_rom_time,vehicle_rom_name,vehicle_port_time,vehicle_port_name");	
		$this->dbdefault->order_by("vehicle_id", "desc");
		$this->dbdefault->where("vehicle_id", $truckID);
		$q = $this->dbdefault->get("vehicle");
		$row = $q->row();
		$total_rows = count($row);
		
		if($total_rows >0){
			
			$last_port_name = $row->vehicle_port_name;
			$last_port_time = $row->vehicle_port_time;
			$last_port_date = date("Y-m-d", strtotime($last_port_time));
			$last_rom_name = $row->vehicle_rom_name;
			$last_rom_time = $row->vehicle_rom_time;
			$last_rom_date = date("Y-m-d", strtotime($last_rom_time));
			$last_rom_time_sec = strtotime($last_rom_time);
			$nowtime_sec = strtotime($gpstime);
			$nowdate_gps = date("Y-m-d",strtotime($gpstime));
			$delta_out = $nowtime_sec - $last_rom_time_sec;
			$delta_min = round($delta_out/60,0);
			
			printf("===LAST ROM %s %s VS NOW %s %s \r\n", $last_rom_name, $last_rom_time, $location, $gpstime);
			
			//khusus data hari ini
			if($last_port_date == $nowdate_gps && $last_rom_date == $nowdate_gps){
			
			
				if($last_rom_time !=  "" && $last_port_time  != ""){
					
					//hitung durasi mulai aktif jika unit sudah ke PORT kemudian ke ROM
					if($last_rom_time > $last_port_time){
						
						if($delta_out > $limit_delta){
							//$duration_alert = $this->get_time_difference($last_rom_time, $gpstime);
							
							$duration = get_time_difference($last_rom_time, $gpstime);
								
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
										
										
										
							$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
							printf("!==OUT OF PORT ALERT DETECTED !!=== %s \r\n", $delta_out);
											
											//send telegram
											$title_name = "OUT OF PORT DETECTED!!";
													$message = urlencode(
																"".$title_name." \n".
																"Time: ".$gpstime." \n".
																"Vehicle No: ".$vehicle->vehicle_no." \n".
																"Speed: ".$speed." kph"." \n".
																"Last ROM Name: ".$last_rom_name." ".$last_rom_time." \n".
																//"Last ROM Time: ".$last_rom_time." \n".
																"Last PORT Name: ".$last_port_name." ".$last_port_time." \n".
																//"Last PORT Time: ".$last_port_time." \n".
																"Duration: ".$show.""." \n".
																"Location: ".$location." \n".
																"Coordinate: ".$url." \n"
																
																
																);
													sleep(2);		
													$sendtelegram = $this->telegram_direct("-657527213",$message); //telegram FMS TESTING
													printf("===SENT TELEGRAM OK\r\n");	//exit();
						}else{
							
							printf("===MASIH AMAN === %s \r\n", $delta_out);
						}
					}else{
						
						printf("===Unit sudah lewati PORT === %s \r\n", $delta_out);
					}
					
					
				}else{
					
					printf("X==LAST ROM/PORT IS NULL === %s \r\n", $delta_out);
				}
			
			}else{
				printf("X==BUKAN DATA HARI INI === PORT %s ROM %s NOW %s \r\n", $last_port_date, $last_rom_date, $nowdate_gps);
			}	
			
		}
		
		/* unset($data);
		$data["integrationwim_transactionID"] = $localtransID;
		$data["integrationwim_penimbanganStartUTC"] = $gpstime_UTC;
		$data["integrationwim_penimbanganStartLocal"] = $gpstime_local2;
		$data["integrationwim_penimbanganFinishUTC"] = $gpstime_UTC_finish;
		$data["integrationwim_penimbanganFinishLocal"] = $gpstime_local2_finish;
		$data["integrationwim_beratTiapGandar"] = "";
		$data["integrationwim_totalGandar"] = "";
		$data["integrationwim_gross"] = $gross_avg;
		$data["integrationwim_tare"] = $tare_avg;
		$data["integrationwim_netto"] = $netto_avg;
		$data["integrationwim_averageSpeed"] = "";
		$data["integrationwim_weightBalance"] = "";
		$data["integrationwim_rfid"] = "";
		$data["integrationwim_noMesin"] = "";
		$data["integrationwim_noRangka"] = "";
		$data["integrationwim_truckType"] = "";
		$data["integrationwim_providerId"] = 4408;
		$data["integrationwim_truckID"] = $vehicle->vehicle_no;
		$data["integrationwim_haulingContractor"] = $haulingContractor[0];
		$data["integrationwim_status"] = "AVERAGE FMS";
		$data["integrationwim_truckImage"] = "";
		$data["integrationwim_created_date"] = date("Y-m-d H:i:s");
											
		$this->dbreport->insert("historikal_integrationwim_unit",$data);
		printf("===LOCAL INSERT AVG OK=== %s \r\n", $localtransID);
		$this->dbreport->close(); */
		
		
	}
	
	function get_jalurname($direction){
		$arah = "";
		//utara
		$ruas1 = 0;
		$ruas2 = 90;
		$ruas3 = 360-90;
								
		//selatan
		$ruas4 = 180-90;
		$ruas5 = 180+90;
		$ruas6 = 180;
								
		if($direction >= $ruas1 && $direction <= $ruas2){ //0 - 90
			$arah = "utara";
			$jalur = "kosongan"; 
		}else if($direction >= $ruas3 && $direction <= 360){ // 360-90 s/d 360
			$arah = "utara";
			$jalur = "kosongan"; 
		}else if($direction >= $ruas6 && $direction <= $ruas5){ // 180 s/d 180+90
			$arah = "selatan";
			$jalur = "muatan"; 
		}else if($direction >= $ruas4 && $direction <= $ruas6){ // 180-90 s/d 180
			$arah = "selatan";
			$jalur = "muatan"; 
		}else{
			$arah = $direction;
			$jalur = "-"; 
		}
				
		return $jalur;
	}
	
	//non bib insert
	function outofgeofence_insert($rowvehicle,$street_name,$title_name,$gps_realtime,$speed,$jalur,$course,$lastlat,$lastlong){
		
		unset($datainsert);
		$datainsert["outgeofence_report_vehicle_user_id"] = $rowvehicle->vehicle_user_id;
		$datainsert["outgeofence_report_vehicle_id"] = $rowvehicle->vehicle_id;
		$datainsert["outgeofence_report_vehicle_device"] = $rowvehicle->vehicle_device;
		$datainsert["outgeofence_report_vehicle_no"] = $rowvehicle->vehicle_no;
		$datainsert["outgeofence_report_vehicle_name"] = $rowvehicle->vehicle_name;
		$datainsert["outgeofence_report_vehicle_type"] = $rowvehicle->vehicle_type;
		$datainsert["outgeofence_report_vehicle_company"] = $rowvehicle->vehicle_company;
		$datainsert["outgeofence_report_imei"] = $rowvehicle->vehicle_mv03;
		$datainsert["outgeofence_report_name"] = $title_name;
		$datainsert["outgeofence_report_speed"] = $speed;
		$datainsert["outgeofence_report_gps_time"] = $gps_realtime;
		$datainsert["outgeofence_report_jalur"] = $jalur;
		$datainsert["outgeofence_report_direction"] = $course;
		$datainsert["outgeofence_report_location"] = $street_name;
		$datainsert["outgeofence_report_coordinate"] = $lastlat.",".$lastlong;
		
		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->insert("webtracking_ts_outgeofence",$datainsert);
		
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		printf("===INSERT OUT GEOFENCE ALERT=== \r\n");
		
		
		
	}
	
	//non bib insert with Dumping info
	function outofgeofence_dumping_insert($rowvehicle,$dumping_name,$title_name,$dumping_time,$speed,$jalur,$course,$lastlat,$lastlong,$last_rom,$last_rom_time){
		
			$this->dbts = $this->load->database("webtracking_ts",TRUE);
			//delta time gps VS gps now WITA
			$start_time_sec = strtotime($last_rom_time);
			$end_time_sec = strtotime($dumping_time);
			$delta = $end_time_sec - $start_time_sec;
			$duration = get_time_difference($last_rom_time, $dumping_time);
									
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
								
			printf("===Delta Non BIB Activity %s %s \r\n", $delta, $show);
								
			unset($datainsert);
			$datainsert["outgeofence_report_vehicle_user_id"] = $rowvehicle->vehicle_user_id;
			$datainsert["outgeofence_report_vehicle_id"] = $rowvehicle->vehicle_id;
			$datainsert["outgeofence_report_vehicle_device"] = $rowvehicle->vehicle_device;
			$datainsert["outgeofence_report_vehicle_no"] = $rowvehicle->vehicle_no;
			$datainsert["outgeofence_report_vehicle_name"] = $rowvehicle->vehicle_name;
			$datainsert["outgeofence_report_vehicle_type"] = $rowvehicle->vehicle_type;
			$datainsert["outgeofence_report_vehicle_company"] = $rowvehicle->vehicle_company;
			$datainsert["outgeofence_report_imei"] = $rowvehicle->vehicle_mv03;
			$datainsert["outgeofence_report_name"] = $title_name;
			
			$datainsert["outgeofence_report_last_rom"] = $last_rom;
			$datainsert["outgeofence_report_last_rom_time"] = $last_rom_time;
			$datainsert["outgeofence_report_dumping"] = $dumping_name;
			$datainsert["outgeofence_report_dumping_time"] = $dumping_time;
			$datainsert["outgeofence_report_dumping_coord"] = $lastlat.",".$lastlong;
			
			$datainsert["outgeofence_report_speed"] = $speed;
			$datainsert["outgeofence_report_jalur"] = $jalur;
			$datainsert["outgeofence_report_direction"] = $course;
			
			$datainsert["outgeofence_report_duration_sec"] = $delta;
			$datainsert["outgeofence_report_duration"] = $show;
			
			
		//CHECK last data
		$this->dbts->where("outgeofence_report_vehicle_no", $rowvehicle->vehicle_no);
		$this->dbts->where("outgeofence_report_last_rom", $last_rom);
		$this->dbts->where("outgeofence_report_last_rom_time",$last_rom_time);
		$q_report = $this->dbts->get("webtracking_ts_outgeofence_dumping");
		$rows_report = $q_report->row();
		$total_report = count($rows_report);

		//jika tidak ada insert
		if($total_report == 0)
		{
		
			$this->dbts->insert("webtracking_ts_outgeofence_dumping",$datainsert);
			printf("===INSERT NON BIB with dumping OK %s \r\n", $rowvehicle->vehicle_no);
		
		}
		//jika sudah ada skip
		else
		{
			$this->dbts->where("outgeofence_report_vehicle_no", $rowvehicle->vehicle_no);
			$this->dbts->where("outgeofence_report_last_rom", $last_rom);
			$this->dbts->where("outgeofence_report_last_rom_time",$last_rom_time);
			$this->dbts->limit(1);
			$this->dbts->update("webtracking_ts_outgeofence_dumping",$datainsert);
			printf("===UPDATE NON BIB with dumping OK %s \r\n", $rowvehicle->vehicle_no);
		}
			
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		printf("===INSERT OUT GEOFENCE ALERT=== \r\n");
		
		
		
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
	
	function getTelegramID_nonbib($id) 
	{	
		$status = false;
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("company_id","asc");
		$this->db->select("company_id,company_telegram_geofence,company_telegram_speed,company_hp");
		$this->db->where("company_id",$id);
		$q = $this->db->get("company");
		$data = $q->row();
		
		$this->db->close();
		$this->db->cache_delete_all();
		
		return $data;
		
	}
		
	function getGeofence_location_live($longitude, $latitude, $vehicle_dblive) {
		
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
	
	function get_jalurname_new($direction){
		$arah = "";
						
		if($direction > 0 && $direction <= 180){ // arah ke kanan (muatan)
			$arah = "kanan";
			$jalur = "muatan"; 
		}else if($direction >= 181 && $direction <= 360){ // arah ke kiri (kosongan)
			$arah = "kiri";
			$jalur = "kosongan"; 
		}else{
			$arah = $direction;
			$jalur = ""; 
		}
		
		//printf("===Arah : %s \r\n", $arah);
				
		return $jalur;
	}
	
	function get_telegramgroup_overspeed($company_id){
		//get telegram group by company
		$this->db = $this->load->database("default",TRUE);
		$this->db->select("company_id,company_telegram_speed");
		$this->db->where("company_id",$company_id);
		$qcompany = $this->db->get("company");
		$rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_speed;
		}else{
			$telegram_group = 0;
		}
				
		return $telegram_group;
	}
	
	function sendnotif_wa_redzone($wa_token,$wa_gps_realtime,$wa_vehicle_no,$wa_street_name,$wa_url,$wa_speed,$wa_lastrom_text,$wa_lastport_text,$wa_attachmentlink){
		$mytoken = $wa_token->sess_value;
	
		$authorization = "token: Bearer ".$mytoken;
		$url = $this->config->item('WA_URL_POST_MESSAGE');
		
		$DataToUpload = array();
		unset($DataToUpload);
		
		$namespace = $this->config->item('WA_NAMESPACE');
		$nametemplate = $this->config->item('WA_TEMPLATE_REDZONE'); //8 param
		$recipient_dt = $this->config->item('WA_NOMOR_REDZONE_ZONE');
		
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
			$DataToUpload->template['components'][0]->parameters[0]['text'] = $wa_gps_realtime;
			$DataToUpload->template['components'][0]->parameters[1]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[1]['text'] = $wa_vehicle_no;
			$DataToUpload->template['components'][0]->parameters[2]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[2]['text'] = $wa_street_name;
			
			$DataToUpload->template['components'][0]->parameters[3]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[3]['text'] = $wa_url;
			$DataToUpload->template['components'][0]->parameters[4]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[4]['text'] = $wa_speed;
		
			$DataToUpload->template['components'][0]->parameters[5]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[5]['text'] = $wa_lastrom_text;
			$DataToUpload->template['components'][0]->parameters[6]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[6]['text'] = $wa_lastport_text;
			
			$DataToUpload->template['components'][0]->parameters[7]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[7]['text'] = $wa_attachmentlink;

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
	
	function sendnotif_wa_warningzone($wa_token,$wa_gps_realtime,$wa_vehicle_no,$wa_street_name,$wa_url,$wa_speed,$wa_lastrom_text,$wa_lastport_text){
		$mytoken = $wa_token->sess_value;
	
		$authorization = "token: Bearer ".$mytoken;
		$url = $this->config->item('WA_URL_POST_MESSAGE');
		
		$DataToUpload = array();
		unset($DataToUpload);
		
		$namespace = $this->config->item('WA_NAMESPACE');
		$nametemplate = $this->config->item('WA_TEMPLATE_WARNINGZONE'); //7 param
		$recipient_dt = $this->config->item('WA_NOMOR_WARNING_ZONE');
		
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
			$DataToUpload->template['components'][0]->parameters[0]['text'] = $wa_gps_realtime;
			$DataToUpload->template['components'][0]->parameters[1]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[1]['text'] = $wa_vehicle_no;
			$DataToUpload->template['components'][0]->parameters[2]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[2]['text'] = $wa_street_name;
			
			$DataToUpload->template['components'][0]->parameters[3]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[3]['text'] = $wa_url;
			$DataToUpload->template['components'][0]->parameters[4]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[4]['text'] = $wa_speed;
		
			$DataToUpload->template['components'][0]->parameters[5]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[5]['text'] = $wa_lastrom_text;
			$DataToUpload->template['components'][0]->parameters[6]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[6]['text'] = $wa_lastport_text;

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
	
	function sendnotif_wa_nonbibact($wa_token,$wa_gps_realtime,$wa_vehicle_no,$wa_street_name,$wa_url,$wa_speed,$wa_company_telegram){
		
		$mytoken = $wa_token->sess_value;
	
		$authorization = "token: Bearer ".$mytoken;
		$url = $this->config->item('WA_URL_POST_MESSAGE');
		
		$DataToUpload = array();
		unset($DataToUpload);
		
		$namespace = $this->config->item('WA_NAMESPACE');
		$nametemplate = $this->config->item('WA_TEMPLATE_NONBIBACT'); //5 param
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
			$DataToUpload->template['components'][0]->parameters[0]['text'] = $wa_gps_realtime;
			$DataToUpload->template['components'][0]->parameters[1]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[1]['text'] = $wa_vehicle_no;
			$DataToUpload->template['components'][0]->parameters[2]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[2]['text'] = $wa_street_name;
			
			$DataToUpload->template['components'][0]->parameters[3]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[3]['text'] = $wa_url;
			$DataToUpload->template['components'][0]->parameters[4]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[4]['text'] = $wa_speed;
		

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
	
	function sendnotif_wa_nonbibact_dumping($wa_token,$wa_gps_realtime,$wa_vehicle_no,$wa_street_name,$wa_url,$wa_speed,$wa_company_telegram,$wa_lastrom_text,$wa_lastport_text){
		
		$mytoken = $wa_token->sess_value;
	
		$authorization = "token: Bearer ".$mytoken;
		$url = $this->config->item('WA_URL_POST_MESSAGE');
		
		$DataToUpload = array();
		unset($DataToUpload);
		
		$namespace = $this->config->item('WA_NAMESPACE');
		$nametemplate = $this->config->item('WA_TEMPLATE_NONBIBACT_DUMPING'); //7 param
		$recipient_dt = $this->config->item('WA_NOMOR_NONBIB_ACTIVITY');
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
			$DataToUpload->template['components'][0]->parameters[0]['text'] = $wa_gps_realtime;
			$DataToUpload->template['components'][0]->parameters[1]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[1]['text'] = $wa_vehicle_no;
			$DataToUpload->template['components'][0]->parameters[2]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[2]['text'] = $wa_street_name;
			
			$DataToUpload->template['components'][0]->parameters[3]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[3]['text'] = $wa_url;
			$DataToUpload->template['components'][0]->parameters[4]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[4]['text'] = $wa_speed;
		
			$DataToUpload->template['components'][0]->parameters[5]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[5]['text'] = $wa_lastrom_text;
			$DataToUpload->template['components'][0]->parameters[6]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[6]['text'] = $wa_lastport_text;
			

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
															
	function sendnotif_wa_1hrport($wa_token,$wa_vehicle_no,$wa_lastposition_time,$wa_location,$wa_speed,$wa_lastromname,$wa_lastromtime,$wa_show,$wa_url,$wa_company_telegram){
		
		$mytoken = $wa_token->sess_value;
	
		$authorization = "token: Bearer ".$mytoken;
		$url = $this->config->item('WA_URL_POST_MESSAGE');
		
		$DataToUpload = array();
		unset($DataToUpload);
		
		$namespace = $this->config->item('WA_NAMESPACE');
		$nametemplate = $this->config->item('WA_TEMPLATE_1HRPORT'); //7 param
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
			$DataToUpload->template['components'][0]->parameters[0]['text'] = $wa_vehicle_no;
			$DataToUpload->template['components'][0]->parameters[1]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[1]['text'] = $wa_lastposition_time;
			$DataToUpload->template['components'][0]->parameters[2]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[2]['text'] = $wa_location;
			$DataToUpload->template['components'][0]->parameters[3]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[3]['text'] = $wa_speed;
			$DataToUpload->template['components'][0]->parameters[4]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[4]['text'] = $wa_lastromname." ".$wa_lastromtime;
			$DataToUpload->template['components'][0]->parameters[5]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[5]['text'] = $wa_show;
			$DataToUpload->template['components'][0]->parameters[6]['type'] = "text";
			$DataToUpload->template['components'][0]->parameters[6]['text'] = $wa_url;
		

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
	
	function fraud_at_PORT($dtvehicle,$dtstreet_name,$dtgps_realtime,$dtauto_last_rom_name,$dtauto_last_rom_time,$dtauto_last_port_name,$dtauto_last_port_time,$dtattachmentlink)
	{
		unset($dataF);
		$dataF["fraud_title"] = "INDIKASI FRAUD RITASE";
		$dataF["fraud_vehicle_id"] = $dtvehicle->vehicle_id;
		$dataF["fraud_vehicle_no"] = $dtvehicle->vehicle_no;
		$dataF["fraud_vehicle_device"] = $dtvehicle->vehicle_device;
		$dataF["fraud_company_id"] = $dtvehicle->vehicle_company;
		$dataF["fraud_company_name"] = "";
		$dataF["fraud_user"] = $dtvehicle->vehicle_user_id;
		$dataF["fraud_last_rom_name"] = $dtauto_last_rom_name;
		$dataF["fraud_last_rom_time"] = $dtauto_last_rom_time;
		$dataF["fraud_last_port_name"] = $dtauto_last_port_name;
		$dataF["fraud_last_port_time"] = $dtauto_last_port_time;
		$dataF["fraud_now_location"] = $dtstreet_name;
		$dataF["fraud_now_time"] = $dtgps_realtime;
		$dataF["fraud_history_link"] = $dtattachmentlink;
		$dataF["fraud_status"] = 1;
											
		$this->dbts->insert("ts_fraud_port",$dataF);
		printf("===INSERT DATA FRAUD at PORT OK=== \r\n");
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
