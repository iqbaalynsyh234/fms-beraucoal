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
	
	function ceklastinfo($modemname="", $order="asc")
	{
		$nowdate = date('Y-m-d H:i:s');
		$offset=0;
		$dateinterval = new DateTime($nowdate);
		$dateinterval->sub(new DateInterval('PT1H'));
		$nowdate = $dateinterval->format('Y-m-d H:i:s');
		
		printf("Search SMS Modem Config at %s \r\n", $nowdate);
		printf("======================================\r\n");
		
		//select list sms modem cron aktif
		$this->db = $this->load->database("default", TRUE);
		$this->db->select("modem_configdb,modem_cron_active");
		if (isset($modemname) && ($modemname != ""))
		{
			$this->db->where("modem_configdb", $modemname);
		}
		$this->db->where("modem_cron_active",1);
		$this->db->where("modem_flag",0);
		$qmodem = $this->db->get("sms_modem");
		if ($qmodem->num_rows() == 0) return;

		$rowsmodem = $qmodem->result();
		$totalmodem = count($rowsmodem);
		$m = 0;
		
		foreach($rowsmodem as $rowmodem)
		{
				if (($m+1) < $offset)
				{
					$m++;
					continue;
				}
				
			printf("Prepare Check Last Info Modem SMS : %s (%d/%d)\n", $rowmodem->modem_configdb, ++$m, $totalmodem); 
			$modem = $rowmodem->modem_configdb;
			
			$this->db->order_by("vehicle_id","asc");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_type <>", "T5DOOR");
			$this->db->where("vehicle_type <>", "TJAM");
			$this->db->where("vehicle_device <>", "006100001017@T5");
			$this->db->where("vehicle_modem", $modem);
			$q = $this->db->get("vehicle");
			
			if ($q->num_rows() == 0)
			{
				printf("No Vehicles \r\n");
				//return;
			}
			
			$rows = $q->result();
			$totalvehicle = count($rows);
			
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				printf("Process Check Last Info For %s %s (%d/%d) Modem : %s \n", $rows[$i]->vehicle_no, $rows[$i]->vehicle_device, $j, $totalvehicle, $modem);
				printf("execute %s\r\n", $rows[$i]->vehicle_no);
				
								// last position
								$vehicledevice = $rows[$i]->vehicle_device;
								
								$this->db->where("vehicle_status", 1);
								$this->db->where("vehicle_device", $vehicledevice);
								$qv = $this->db->get("vehicle");
							
								if ($qv->num_rows() == 0)
								{
									printf("No Data \r\n");
								}
							
								$rowvehicle = $qv->row();
								$rowvehicles = $qv->result();
								
								$t = $rowvehicle->vehicle_active_date2;
								$now = date("Ymd");
								
								if ($t < $now)
								{
									printf("Mobil Expired \r\n");
								}
								
								list($name, $host) = explode("@", $rowvehicle->vehicle_device);
								
								$gps = $this->gpsmodel->GetLastInfo($name, $host, true, false, 0, $rowvehicle->vehicle_type);
								if ($this->gpsmodel->fromsocket)
								{
									$datainfo = $this->gpsmodel->datainfo;
									$fromsocket = $this->gpsmodel->fromsocket;			
								}
										
								if (! $gps)
								{
									printf("Gps Belum Aktif \r\n");
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
								$this->dbsms = $this->load->database("smscolo",true);
								$skip = 0;
								
								if(isset($gps->gps_timestamp)){
									
									$delta = ((mktime() - $gps->gps_timestamp) - 3600); //dikurangi 3600 detik karena error time
									
									//cek delay kurang dari 10 menit 
									if ($delta >= 600 && $delta <= 43200) //lebih 10 menit kurang dari 12 jam //yellow condition
									{
										printf("Vehicle No %s Tidak Update \r\n", $rowvehicle->vehicle_no);
										//cek outbox
										printf("Check Outbox SMS Alat %s \r\n", $rowvehicle->vehicle_modem);
										
										$this->dbsmsalat = $this->load->database($rowvehicle->vehicle_modem,true);
										$this->dbsmsalat->select("count(*) as total");
										$qt = $this->dbsmsalat->get("outbox");
										$rt = $qt->row();
										$total = $rt->total;
										
										if(isset($total))
										{
											if ($total > 41 )
											{
												printf("OUTBOX LEBIH BESAR DARI 40 SMS ! \r\n");
												printf("SKIP INSERT ! \r\n");
												$skip = 1;
											}
										}
										if($skip == 0){
											
											printf("Send Restart Command To : %s \r\n", $rowvehicle->vehicle_card_no);	
										
											unset($datasms);
											$datasms["SenderNumber"] = $rowvehicle->vehicle_card_no;
											
											if($rows[$i]->vehicle_type == "T5SILVER")
											{

												if ($rows[$i]->vehicle_operator == "Telkomsel Hallo" || $rows[$i]->vehicle_operator == "Telkomsel Simpati" || 
													$rows[$i]->vehicle_operator == "Telkomsel Halo" || $rows[$i]->vehicle_operator == "Telkomsel"){
													$datasms["TextDecoded"] = "APN114477 TELKOMSEL";
												}
												if ($rows[$i]->vehicle_operator == "Indosat Matrix" || $rows[$i]->vehicle_operator == "Indosat IM3" || 
													$rows[$i]->vehicle_operator == "Indosat Mentari" || $rows[$i]->vehicle_operator == "Indosat"){
													$datasms["TextDecoded"] = "APN114477 INDOSATGPRS";
												}
											}
											if($rows[$i]->vehicle_type == "T5 PULSE" || $rows[$i]->vehicle_type == "T5")
											{
												//$datasms["TextDecoded"] = "Protocol114477 UDP";
												if($rows[$i]->vehicle_dbname_live == "0")
												{ 
													$datasms["TextDecoded"] = "Protocol114477 UDP";
												}else{
													$datasms["TextDecoded"] = "Protocol114477 TCP";
												}
											}
											if($rows[$i]->vehicle_type == "T8_2")
											{
												$datasms["TextDecoded"] = "reset#";
											}
											if($rows[$i]->vehicle_type == "T8")
											{
												$datasms["TextDecoded"] = "reset#";
											}
											
											//tambahan plus 1 jam karena eror jam server
											$nowdate_sms = 	date("Y-m-d H:i:s");
											$dateinterval = new DateTime($nowdate_sms);
											$dateinterval->sub(new DateInterval('PT1H'));
											$nowdate_sms = $dateinterval->format('Y-m-d H:i:s');
		
											$datasms["ReceivingDateTime"] = $nowdate_sms;
											$datasms["RecipientID"] = $rowvehicle->vehicle_modem;
											
											$this->dbsms->insert("inbox",$datasms);
											printf("===INSERT=== %s \r\n", $datasms["TextDecoded"]);
										}	
										
									}
									else if($delta >= 43201) //lebih dari 1 hari //red condition 
									{
										printf("===RED CONDITION=== \r\n");
									}
									else
									{
										if($gps->gps_status == "V"){
											printf("Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
											
										}else{
											printf("===GPS UPDATE=== \r\n");	
										}
									}
								}else{
									printf("===NO DATA=== \r\n");	
								}
								
								
				$j++;
			}
		
			
		}
		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbsms->close();
		$this->dbsms->cache_delete_all();
		$this->dbsmsalat->close();
		$this->dbsmsalat->cache_delete_all();
		$enddate = date('Y-m-d H:i:s');
		printf("FINISH Check Last Info at %s \r\n", $enddate);
		printf("============================== \r\n");

	}
	
	function ceklastinfo_user($userid="", $modemname="", $order="asc")
	{
		$nowdate = date('Y-m-d H:i:s');
		$offset=0;
		$dateinterval = new DateTime($nowdate);
		$dateinterval->sub(new DateInterval('PT1H'));
		$nowdate = $dateinterval->format('Y-m-d H:i:s');
		
		printf("Search SMS Modem Config at %s \r\n", $nowdate);
		printf("======================================\r\n");
		
			$this->db->order_by("vehicle_id","asc");
			$this->db->where("vehicle_status <>", 3);
			//$this->db->where("vehicle_type <>", "TK315");
			//$this->db->where("vehicle_type <>", "TK309");
			//$this->db->where("vehicle_type <>", "A13");
			/*$this->db->where("vehicle_type <>", "T5");
			$this->db->where("vehicle_type <>", "T5SILVER");
			$this->db->where("vehicle_type <>", "T5DOOR");
			$this->db->where("vehicle_type <>", "T8");
			$this->db->where("vehicle_type <>", "T8_2");*/
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_user_id",$userid);
			$q = $this->db->get("vehicle");
			
			if ($q->num_rows() == 0)
			{
				printf("No Vehicles \r\n");
				//return;
			}
			
			$rows = $q->result();
			$totalvehicle = count($rows);
			
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				printf("Check Last Info For %s %s %s %s (%d/%d) \n", $userid, $rows[$i]->vehicle_no, $rows[$i]->vehicle_device, $rows[$i]->vehicle_type, $j, $totalvehicle);
				printf("execute %s\r\n", $rows[$i]->vehicle_no);
				
								// last position
								$vehicledevice = $rows[$i]->vehicle_device;
								
								$this->db->where("vehicle_status", 1);
								$this->db->where("vehicle_device", $vehicledevice);
								$qv = $this->db->get("vehicle");
							
								if ($qv->num_rows() == 0)
								{
									printf("No Data \r\n");
								}
							
								$rowvehicle = $qv->row();
								$rowvehicles = $qv->result();
								
								$t = $rowvehicle->vehicle_active_date2;
								$now = date("Ymd");
								
								if ($t < $now)
								{
									printf("Mobil Expired \r\n");
								}
								
								list($name, $host) = explode("@", $rowvehicle->vehicle_device);
								
								$gps = $this->gpsmodel->GetLastInfo($name, $host, true, false, 0, $rowvehicle->vehicle_type);
								if ($this->gpsmodel->fromsocket)
								{
									$datainfo = $this->gpsmodel->datainfo;
									$fromsocket = $this->gpsmodel->fromsocket;			
								}
										
								if (! $gps)
								{
									printf("Gps Belum Aktif \r\n");
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
								$this->dbsms = $this->load->database("smscolo",true);
								
								if($rowvehicle->vehicle_modem == "0" || $rowvehicle->vehicle_modem == "" ){
									$this->dbsmsalat = $this->load->database("smsalat",true);
								}else{
									$this->dbsmsalat = $this->load->database($rowvehicle->vehicle_modem,true);
								}
								
								$skip = 0;
								
								if(isset($gps->gps_timestamp)){
									if($rowvehicle->vehicle_type == "TK315" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK315N" || $rowvehicle->vehicle_type == "TK309N" || $rowvehicle->vehicle_type == "A13")
									{
										$gps_realtime = ($gps->gps_timestamp-7*3600);
									}else{
										$gps_realtime = $gps->gps_timestamp;
									}
									$delta = ((mktime() - $gps_realtime) - 3600); //dikurangi 3600 detik karena error time
									
									printf("GPS Time: %s  \r\n", date("Y-m-d H:i:s", $gps_realtime));
									
									//cek delay kurang dari 10 menit 
									if ($delta >= 600 && $delta <= 43200) //lebih 10 menit kurang dari 12 jam //yellow condition
									{
										printf("Vehicle No %s Tidak Update \r\n", $rowvehicle->vehicle_no);
										//cek outbox
										printf("Check Outbox SMS Alat %s \r\n", $rowvehicle->vehicle_modem);
										
										$this->dbsmsalat->select("count(*) as total");
										$qt = $this->dbsmsalat->get("outbox");
										$rt = $qt->row();
										$total = $rt->total;
										
										if(isset($total))
										{
											if ($total > 41 )
											{
												printf("OUTBOX LEBIH BESAR DARI 40 SMS ! \r\n");
												printf("SKIP INSERT ! \r\n");
												$skip = 1;
											}
										}
										if($skip == 0){
											
											printf("Send Restart Command To : %s \r\n", $rowvehicle->vehicle_card_no);	
										
											unset($datasms);
											$datasms["SenderNumber"] = $rowvehicle->vehicle_card_no;
											
											if($rows[$i]->vehicle_type == "T5SILVER")
											{
												if ($rows[$i]->vehicle_operator == "Telkomsel Hallo" || $rows[$i]->vehicle_operator == "Telkomsel Simpati" || 
													$rows[$i]->vehicle_operator == "Telkomsel Halo" || $rows[$i]->vehicle_operator == "Telkomsel"){
													$datasms["TextDecoded"] = "APN114477 TELKOMSEL";
												}
												if ($rows[$i]->vehicle_operator == "Indosat Matrix" || $rows[$i]->vehicle_operator == "Indosat IM3" || 
													$rows[$i]->vehicle_operator == "Indosat Mentari" || $rows[$i]->vehicle_operator == "Indosat"){
													$datasms["TextDecoded"] = "APN114477 INDOSATGPRS";
												}
											}
											if($rows[$i]->vehicle_type == "T5 PULSE" || $rows[$i]->vehicle_type == "T5" || $rows[$i]->vehicle_type == "T5DOOR")
											{
												if($rows[$i]->vehicle_dbname_live == "0")
												{ 
													$datasms["TextDecoded"] = "Protocol114477 UDP";
												}else{
													$datasms["TextDecoded"] = "Protocol114477 TCP";
												}
											}
											if($rows[$i]->vehicle_type == "T8_2" || $rows[$i]->vehicle_type == "T8" || $rows[$i]->vehicle_type == "A13")
											{
												$datasms["TextDecoded"] = "reset#";
											}
											if($rows[$i]->vehicle_type == "TK315" || $rows[$i]->vehicle_type == "TK315N" || $rows[$i]->vehicle_type == "TK315DOOR" ||
											   $rows[$i]->vehicle_type == "TK309" || $rows[$i]->vehicle_type == "TK309N")
											{
												$datasms["TextDecoded"] = "#reboot#123456#";
											}
											
											//tambahan plus 1 jam karena eror jam server
											$nowdate_sms = 	date("Y-m-d H:i:s");
											$dateinterval = new DateTime($nowdate_sms);
											$dateinterval->sub(new DateInterval('PT1H'));
											$nowdate_sms = $dateinterval->format('Y-m-d H:i:s');
		
											$datasms["ReceivingDateTime"] = $nowdate_sms;
											$datasms["RecipientID"] = $rowvehicle->vehicle_modem;
											
											$this->dbsms->insert("inbox",$datasms);
											printf("===INSERT=== %s \r\n", $datasms["TextDecoded"]);
										}	
										
									}
									else if($delta >= 43201) //lebih dari 1 hari //red condition 
									{
										printf("===RED CONDITION=== \r\n");
									}
									else
									{
										if($gps->gps_status == "V"){
											printf("Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
											
										}else{
											printf("===GPS UPDATE=== \r\n");	
										}
									}
								}else{
									printf("===NO DATA=== \r\n");	
								}
								
								
				$j++;
			}
		
			
		
		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbsms->close();
		$this->dbsms->cache_delete_all();
		$this->dbsmsalat->close();
		$this->dbsmsalat->cache_delete_all();
		$enddate = date('Y-m-d H:i:s');
		printf("FINISH Check Last Info at %s \r\n", $enddate);
		printf("============================== \r\n");

	}
	
	function autocheck_bk_standard_2022_04_28($groupname="", $userid="", $order="asc")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d H:i:s');
		$offset=0;
		
		printf("===Search SMS Modem Config at %s \r\n", $nowdate);
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
		$m = 0;
		
		foreach($rowsmodem as $rowmodem)
		{
				if (($m+1) < $offset)
				{
					$m++;
					continue;
				}
				
			printf("===Prepare Check Last Info Modem SMS : %s (%d/%d)\n", $rowmodem->modem_configdb, ++$m, $totalmodem); 
			$modem = $rowmodem->modem_configdb;
			
			$this->db->order_by("vehicle_id","asc");
			$this->db->group_by("vehicle_device");
			$this->db->where("vehicle_status !=", 3);
			if($userid != ""){
				$this->db->where("vehicle_user_id", $userid);
			}
			$this->db->where("vehicle_modem", $modem);
			//$this->db->where("vehicle_device", "860294045083605@VT200");
			
			$q = $this->db->get("vehicle");
			
			if ($q->num_rows() == 0)
			{
				printf("===No Vehicles \r\n");
				//return;
			}
			
			$rows = $q->result();
			$totalvehicle = count($rows);
			printf("===Total Vehicle %s \r\n", $totalvehicle);
			
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				printf("===Check Last Info For %s %s %s %s (%d/%d) \n", $userid, $rows[$i]->vehicle_no, $rows[$i]->vehicle_device, $rows[$i]->vehicle_type, $j, $totalvehicle);
				$feature = array();
				$running = 0;
				
								// last position
								$vehicledevice = $rows[$i]->vehicle_device;
								$vehicleuser = $rows[$i]->vehicle_user_id;
								$vehiclecompany = $rows[$i]->vehicle_company;
								$vehicle_dblive = $rows[$i]->vehicle_dbname_live;
								$engine = "-";
								$speed = "0";
								$course = "0";
								$jalur = "";
								$hauling = "";
								
								$this->db->order_by("vehicle_id","asc");
								$this->db->where("vehicle_status !=", 3);
								//$this->db->where("vehicle_user_id", $vehicleuser);
								//$this->db->where("vehicle_device", $vehicledevice);
								$this->db->where("vehicle_id", $rows[$i]->vehicle_id);
								$qv = $this->db->get("vehicle");
							
								if ($qv->num_rows() == 0)
								{
									printf("===No Data Vehicle \r\n");
									$running = 0;
								}
								else{
									$running = 1;
								}
							
								$rowvehicle = $qv->row();
								//$rowvehicles = $qv->result();
								/*
								$t = $rowvehicle->vehicle_active_date2;
								$now = date("Ymd");
								
								if ($t < $now)
								{
									printf("Mobil Expired \r\n");
								}
								*/
					
						if($rowvehicle->vehicle_status != 3)
						{
									
								//list($name, $host) = explode("@", $rowvehicle->vehicle_device);
								$vehicledata = explode("@", $rowvehicle->vehicle_device);
								
								$gps = $this->gpsmodel->GetLastInfo($vehicledata[0], $vehicledata[1], true, false, 0, $rowvehicle->vehicle_type);
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
											
										/*$status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
										$status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
										$status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
											
										$engine = $status1 ? "ON" : "OFF";
										*/
										if(substr($rowinfo->gps_info_io_port, 4, 1) == 1){
											$engine = "ON";
										}else{
											$engine = "OFF";
										}
			
									}			
									
								}

								$this->db = $this->load->database("default", TRUE);
								$this->dbsms = $this->load->database("smscolo",true);
								
								if($rowvehicle->vehicle_modem == "0" || $rowvehicle->vehicle_modem == "" ){
									$this->dbsmsalat = $this->load->database("smsalat",true);
								}else{
									$this->dbsmsalat = $this->load->database($rowvehicle->vehicle_modem,true);
								}
							
								$skip = 0;
								
								if(isset($gps->gps_timestamp))
								{
									//if($rowvehicle->vehicle_type == "TK315" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK315N" || $rowvehicle->vehicle_type == "TK309N" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "GT06N"){
									if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))){
										$gps_realtime = ($gps->gps_timestamp-7*3600);
									}else{
										$gps_realtime = $gps->gps_timestamp;
									}
									//$delta = ((mktime() - $gps_realtime) - 3600); //dikurangi 3600 detik karena error time
									$delta = ((mktime() - $gps_realtime));
									
									printf("===GPS Time: %s  \r\n", date("Y-m-d H:i:s", $gps_realtime));
									printf("===Vehicle No %s \r\n", $rowvehicle->vehicle_no);
									printf("===Speed %s, Engine %s \r\n", $gps->gps_speed, $engine);
									
									
									//get parameter gps
									//if($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5PULSE" || $rowvehicle->vehicle_type == "T5DOOR" || 	$rowvehicle->vehicle_type == "T5SILVER" || $rowvehicle->vehicle_type == "T8" || $rowvehicle->vehicle_type == "T8_2"){
									if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))){
										$lastposition = $this->getPosition_other($gps->gps_longitude, $gps->gps_latitude);
									}else{
										$lastposition = $this->getPosition($gps->gps_longitude, $gps->gps_ew, $gps->gps_latitude, $gps->gps_ns);
									}
									
									
									if($gps->gps_status == "A"){
										$gpsvalidstatus = "OK";
									}else{
										$gpsvalidstatus = "NOT OK";
									}
									
									$street_register = array("PORT BIB","PORT BIR","PORT TIA",
														//"ROM 01","ROM 01/02 ROAD","ROM 02","ROM 03","ROM 03/04 ROAD","ROM 04","ROM 05","ROM 06","ROM 06 ROAD",
														//"ROM O7","ROM 07/08 ROAD","ROM 08","ROM 09","ROM 10",
														"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
														"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST",
														"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
														
														"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
														"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT",
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
														"PORT BIB - Antrian","Port BIB - Antrian");
														
									$port_register = array("BIB CP 1","BIB CP 7","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 2","BIB CP 6",
														   "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
														   "PORT BIB","PORT BIR","PORT TIA"
														   
														   );
														   
									$rom_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom", "Non BIB Angsana","Non BIB TBR/SDJ"
										
													      
														  );
														  
									$nonbib_register = array("SUNGAI DANAU","KINTAB","sungai danau","kintab","Non BIB Angsana","Non BIB TBR/SDJ"
																);
														  
									$bayah_muatan_register = array("Port BIB - Antrian","PORT BIB - Antrian","Port BIR - Antrian WB" );
									$bayah_kosongan_register = array("Port BIB - Kosongan 1","Port BIB - Kosongan 2","Port BIR - Kosongan 1",
																	 "Port BIR - Kosongan 2", "Simpang Bayah - Kosongan");
																	 
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
									
									
									if(isset($lastposition)){
										$ex_lastposition = explode(",",$lastposition->display_name);
										$street_name = $ex_lastposition[0]; 
										
										//$street_name = "Jalur TIA Utara"; //hardoce
										
										if (in_array($street_name, $street_register)){
											$hauling = "in";
										}else{
											
											$hauling = "out";
										}
										
										printf("===Location %s \r\n", $street_name);
										
										//redzone check here
										$redzone_status = 0;
										$warningzone_status = 0;
										$nonbib_status = 0; 
										$redzone_area = array("STU area","PCN area");
										$warningzone_area = array("Jalur TIA Utara","Jalur TIA Selatan","WR 01","WR 02","WR 03","WR 04","WR 05","WR 06","WR 07");
															  
															  
										$lastdata_json = json_decode($rowvehicle->vehicle_autocheck);
										//print_r($lastdata_json=>auto_last_rom_name);exit();
										
										$auto_last_rom_name = $lastdata_json->auto_last_rom_name;
										$auto_last_rom_time = $lastdata_json->auto_last_rom_time;
										$auto_last_port_name = $lastdata_json->auto_last_port_name;
										$auto_last_port_time = $lastdata_json->auto_last_port_time;
										
										/* $last_rom_name = "";
										$last_rom_time = "";
										$last_port_name = "";
										$last_port_time = ""; */
										
										if (in_array($street_name, $rom_register))
										{
											$now_rom_name = $street_name;
											$now_rom_time = date("Y-m-d H:i:s", $gps_realtime);
											printf("X==ROM CHECKING  \r\n");
											//sementara untuk input all data
											
											if($auto_last_rom_name == ""){
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("X==Data Awal ROM \r\n");
												
											}
											else
											{
												//update data tiap masuk ROM 
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("X==Masih di ROM \r\n");
												
											}
											
											
										}
										
										if (in_array($street_name, $port_register))
										{
											$now_port_name = $street_name;
											$now_port_time = date("Y-m-d H:i:s", $gps_realtime);
											printf("Y==PORT CHECKING \r\n");
											//sementara untuk input all data
											
											if($auto_last_port_name == ""){
												$auto_last_port_name = $street_name;
												$auto_last_port_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("Y==Data Awal PORT \r\n");
												//exit();
											}
											else
											{
												//jika port lama beda dengan PORT sekarang, maka update ke yg baru
												$auto_last_port_name = $street_name;
												$auto_last_port_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("Y==Masih di PORT \r\n");
											}
										}
										
										//rssult redzone
										if (in_array($street_name, $redzone_area)){
										//if (in_array($street_name, $redzone_area)){											
											$redzone_status = 1;
											$redzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===REDZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, date("Y-m-d H:i:s", $gps_realtime), $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$redzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													$redzone_type = "Z1";
													
													$lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicledata[0],$rowvehicle->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													}
													
													
												}
											}
											
											
										}
										
										//rssult warningzone
										if (in_array($street_name, $warningzone_area)){
											$warningzone_status = 1;
											$warningzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===WARNINGZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, date("Y-m-d H:i:s", $gps_realtime), $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$warningzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													$warningzone_type = "Z1";
													
													$lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicledata[0],$rowvehicle->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													}
													
													
												}
											}
											
											
										}
										
										$overspeed_status = 0;
										//send overspeed alert
										/* if (in_array($street_name, $street_onduty)){
											$overspeed_status = 0;
										}else{
											$overspeed_status = 0;
										} */
										
									}
									
									$lastlat = $gps->gps_latitude_real;
									$lastlong = $gps->gps_longitude_real;
									$speed = number_format($gps->gps_speed*1.852, 0, "", ".");
									$course = $gps->gps_course;
									
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
									
									$coordinate = $lastlat.",".$lastlong;
									$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
									printf("===Location %s, Jalur %s , Hauling %s \r\n", $street_name, $jalur, $hauling); //print_r("DISINI");exit();
									
									
									if($redzone_status == 1){
										
											$title_name = "REDZONE DETECTED!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n".
														"Last ROM: ".$lastrom_text." \n".
														"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											
											if($redzone_type == "Z1"){
												//$sendtelegram = $this->telegram_direct("-738419382",$message); //telegram RED ZONE CHECK
												$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											}else{
												//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
												$sendtelegram = $this->telegram_direct("-632059478",$message); //telegram BIB REDZONE
											}
											
											
											printf("===SENT TELEGRAM OK\r\n");	
											
										
									}
									
									if($warningzone_status == 1){
										
											$title_name = "WARNING ZONE DETECTED!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
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
										
									
									if ($overspeed_status == 1){
										$rowgeofence = $this->getGeofence_location_live($gps->gps_longitude_real, $gps->gps_latitude_real, $vehicle_dblive);
										$telegram_group = $this->get_telegramgroup_overspeed($vehiclecompany);
										
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
											
											$gpsspeed_kph = $speed;
										}
										printf("===GEO CHECKING, Position : %s Geofence : %s Jalur: %s \r\n", $street_name, $geofence_name, $jalur);
										printf("===GEO CHECKING, Speed : %s Limit : %s \r\n", $gpsspeed_kph, $geofence_speed_limit);
										
										if($gpsspeed_kph <= $geofence_speed_limit){
											$skip_spd_sent = 1;
										}else{
											$skip_spd_sent = 0;
											
										}
										
										if($geofence_speed_limit == 0){
											$skip_spd_sent = 1;
										}else{
											$skip_spd_sent = 0;
										}
										
										if($skip_spd_sent == 0){
											$gpsspeed_kph = $gpsspeed_kph-3;
											$geofence_speed_limit = $geofence_speed_limit-3;
											$driver_name = "";
											$title_name = "OVERSPEED ALARM";
											$message = urlencode(
												"".$title_name." \n".
												"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
												"Vehicle No: ".$rowvehicle->vehicle_no." \n".
												"Driver: ".$driver_name." \n".
												"Position: ".$street_name." \n".
												"Coordinate: ".$url." \n".
												"Speed (kph): ".$gpsspeed_kph." \n".
												"Rambu (kph): ".$geofence_speed_limit." \n".
												"Geofence: ".$geofence_name." \n".
												"Jalur: ".$jalur." \n"
												
												);
												
											//printf("===Message : %s \r\n", $message);
											sleep(2);
											$sendtelegram = $this->telegram_direct($telegram_group,$message);
											printf("===SENT TELEGRAM OVERSPEED OK\r\n");	
										}else{
											
											printf("X==SKIP SENT OVERSPEED TELEGRAM\r\n");	
										}
									}
									
									//Kondisi Engine Error
									if($gps->gps_speed > 4 && $engine == "OFF"){
										printf("===SEND EMAIL ENGINE ERROR TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."ENGINE ERROR";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: GPS ENGINE OFF dan Mobil Jalan, Segera cek Historynya!!

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT ENGINE ERROR \r\n"); 
									}
									
									//cek vehicle INFO
									if ($rowvehicle->vehicle_info)
									{
										$json = json_decode($rowvehicle->vehicle_info);
											
										if (isset($json->vehicle_ip) && isset($json->vehicle_port))
										{
											$vehicle_port = $json->vehicle_port;
										}
									}
											
									$vehicle_operator_ex = explode(" ", $rowvehicle->vehicle_operator);
									$vehicle_operator = strtolower($vehicle_operator_ex[0]);
								
									if($rowvehicle->vehicle_dbname_live == "0")
									{ 
										$dblive = "0";
									}else{
										$dblive = "1";
									}
									
									printf("===Vehicle Data %s %s %s \r\n", $rowvehicle->vehicle_type, $vehicle_port, $vehicle_operator, $dblive);
									
									
									//cek delay kurang dari 1 jam
									if ($delta >= 3600 && $delta <= 86400) //lebih jam kurang dari 24 jam //yellow condition
									{
										printf("=================GPS KUNING================ \r\n");
										printf("===Vehicle No %s Tidak Update \r\n", $rowvehicle->vehicle_no);
										$statuscode = "K";
										
										//cek log pengiriman hari ini
										$command_log = $this->get_command_log($rowvehicle->vehicle_device,$nowdate,$rowmodem->modem_limit_time); 
										
										//jika 0 belum ada kirim sms maka send command
										if($command_log == "0"){
											$command_restart = $this->get_command($rowvehicle->vehicle_type,$vehicle_port,$vehicle_operator,$dblive);
											
											//cek outbox
											printf("===Check Outbox SMS Alat %s \r\n", $rowvehicle->vehicle_modem);
											
											$this->dbsmsalat->select("count(*) as total");
											$qt = $this->dbsmsalat->get("outbox");
											$rt = $qt->row();
											$total = $rt->total;
											
											if(isset($total))
											{
												if ($total > 41 )
												{
													printf("===OUTBOX LEBIH BESAR DARI 40 SMS ! \r\n");
													printf("===SKIP INSERT ! \r\n");
													$skip = 1;
												}
											}
											$skip = 1;///DI UJI COBA(R12)
											if($skip == 0){
												
												printf("===Send Restart Command To : %s \r\n", $rowvehicle->vehicle_card_no);	
												
												if($command_restart->restart_step1_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+2 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_1);
													$datasms_1["UpdatedInDB"] = $nowdate_sms;
													$datasms_1["InsertIntoDB"] = $nowdate_sms;
													$datasms_1["SendingDateTime"] = $nowdate_sms;
													$datasms_1["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_1["TextDecoded"] = $command_restart->restart_step1_command;
													$datasms_1["SendingTimeOut"] = $nowdate_sms;
													//$datasms_1["RecipientID"] = $rowvehicle->vehicle_modem;
													
													$this->dbsmsalat->insert("outbox",$datasms_1);
													printf("===INSERT STEP 1=== %s \r\n",$command_restart->restart_step1_command );
												}
												if($command_restart->restart_step2_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+5 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_2);
													$datasms_2["UpdatedInDB"] = $nowdate_sms;
													$datasms_2["InsertIntoDB"] = $nowdate_sms;
													$datasms_2["SendingDateTime"] = $nowdate_sms;
													$datasms_2["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_2["TextDecoded"] = $command_restart->restart_step2_command;
													$datasms_2["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_2);
													printf("===INSERT STEP 2=== %s \r\n",$command_restart->restart_step2_command );
												}
												if($command_restart->restart_step3_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+8 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_3);
													$datasms_3["UpdatedInDB"] = $nowdate_sms;
													$datasms_3["InsertIntoDB"] = $nowdate_sms;
													$datasms_3["SendingDateTime"] = $nowdate_sms;
													$datasms_3["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_3["TextDecoded"] = $command_restart->restart_step3_command;
													$datasms_3["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_3);
													printf("===INSERT STEP 3=== %s \r\n",$command_restart->restart_step3_command );
												}
												if($command_restart->restart_step4_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+11 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_4);
													$datasms_4["UpdatedInDB"] = $nowdate_sms;
													$datasms_4["InsertIntoDB"] = $nowdate_sms;
													$datasms_4["SendingDateTime"] = $nowdate_sms;
													$datasms_4["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_4["TextDecoded"] = $command_restart->restart_step4_command;
													$datasms_4["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_4);
													printf("===INSERT STEP 4=== %s \r\n",$command_restart->restart_step4_command );
												}
												
													//insert log
													unset($datasms_log);
													$datasms_log["log_user"] = $rowvehicle->vehicle_user_id;
													$datasms_log["log_vehicle"] = $rowvehicle->vehicle_no;
													$datasms_log["log_device"] = $rowvehicle->vehicle_device;
													$datasms_log["log_type"] = $rowvehicle->vehicle_type;
													$datasms_log["log_simcard"] = $rowvehicle->vehicle_card_no;
													$datasms_log["log_command"] = $command_restart->restart_step1_command."|".$command_restart->restart_step2_command."|".								$command_restart->restart_step3_command."|".$command_restart->restart_step4_command;
													$datasms_log["log_date"] = date("Y-m-d", strtotime($nowdate));
													$datasms_log["log_created"] = $nowdate;
													
													$this->db->insert("sms_restart_log",$datasms_log);
													printf("===INSERT LOG OK=== \r\n");		
													
												
											}else{
												printf("===SKIP INSERT OUTBOX PENUH=== \r\n");
											}

										}else{
												printf("===SKIP INSERT SUDAH ADA DI LOG HARI INI=== \r\n");
												//printf("===SEND EMAIL TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS KUNING";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem tidak bisa diupdate

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT YELLOW \r\n"); 
													
										}
										
									}
									else if($delta >= 43201) //lebih dari 1 hari //red condition 
									{
										$statuscode = "M";
										printf("======================RED CONDITION======================== \r\n");
										
										unset($datavehicle);
										$datavehicle["vehicle_isred"] = 1;
										$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
										$this->db->update("vehicle", $datavehicle);
										printf("===UPDATED STATUS IS RED YES=== %s \r\n", $rowvehicle->vehicle_no);
										
										
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS MERAH";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem GPS MERAH

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT RED \r\n"); 
									}
									else //gps update condition
									{
										$statuscode = "P";
										
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
										
										if($nonbib_status == 1)
										{
										
											$title_name = "NON BIB ACTIVITY!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n"
														//"Last ROM: ".$lastrom_text." \n".
														//"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											$sendtelegram = $this->telegram_direct($telegram_geofence,$message);
											
											printf("===SENT TELEGRAM OK\r\n");	
										}
										
										
										if($gps->gps_status == "V"){
											printf("===Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
											//printf("===SEND EMAIL TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS NOT OK";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem GPS NOT OK

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT NOT OK \r\n"); 
											
										}else{
											printf("=================GPS UPDATE================ \r\n");
											//update log command flag == 1
											$command_reset = $this->reset_command_log($rowvehicle->vehicle_device,$nowdate);
											if($command_reset == true){
												printf("===UPDATE CONFIG \r\n");
											}
											
											unset($datavehicle);
											$datavehicle["vehicle_isred"] = 0;
											$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
											$this->db->update("vehicle", $datavehicle);
											printf("===UPDATED STATUS DEFAULT=== %s \r\n", $rowvehicle->vehicle_no);
											
										}
									}
									
									//update master vehicle autocheck
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = $statuscode;
										$datacheck["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$datacheck["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
										$datacheck["auto_change_position"] = $lastposition->display_name;
										$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
										
										$this->db->insert("vehicle_autocheck",$datacheck);
										printf("===INSERT AUTOCHECK=== \r\n");	

										//json										
										$feature["auto_status"] = $statuscode;
										$feature["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = $statuscode;
										$datacheck["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$feature["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
											$datacheck["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
											$datacheck["auto_change_position"] = $lastposition->display_name;
											$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
											
											//json
											$feature["auto_change_engine_status"] = $engine;
											$feature["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
											$feature["auto_change_position"] = $lastposition->display_name;
											$feature["auto_change_coordinate"] = $lastlat.",".$lastlong;
										}
										
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
										$this->db->update("vehicle_autocheck",$datacheck);
										printf("===UPDATE AUTOCHECK=== \r\n");	
										
										
										
											
									}
										
								
								
								
								
								
								
								
								
								
								}
								else
								{
									printf("===NO DATA=== \r\n");	
									unset($datavehicle);
									$datavehicle["vehicle_isred"] = 1;
									$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
									$this->db->update("vehicle", $datavehicle);
									printf("===UPDATED STATUS IS RED (NO DATA) YES=== %s \r\n", $rowvehicle->vehicle_no);
										
									//update master vehicle (khusus vehicle GO TO History)
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									//$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
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
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
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
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
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
						}
						else
						{
							
									//update master vehicle (khusus vehicle HIDE)
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									//$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = "";
										$datacheck["auto_last_update"] = "";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_last_hauling"] = "";
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										$datacheck["auto_flag"] = 1;
										
														
										$this->db->insert("vehicle_autocheck",$datacheck);
										printf("===INSERT AUTOCHECK=== \r\n");

										//json										
										$feature["auto_status"] = "";
										$feature["auto_last_update"] = "";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										$feature["auto_flag"] = 1;
										$feature["vehicle_gotohistory"] = 1;
										$vehicle_gotohistory = 1;	
														
									}else{
										//update
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = "";
										$datacheck["auto_last_update"] = "";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_last_hauling"] = "";
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										$datacheck["auto_flag"] = 1;
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
										$this->db->update("vehicle_autocheck",$datacheck);
										printf("===UPDATE AUTOCHECK=== \r\n");	
										
										//json
										$feature["auto_status"] = "";
										$feature["auto_last_update"] = "";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										
										$feature["auto_flag"] = 1;
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
						
						$this->db->where("vehicle_id", $rows[$i]->vehicle_id);	
						$this->db->limit(1);	
						$this->db->update("vehicle",$datajson);
						printf("===UPDATE JSON MASTER VEHICLE=== \r\n");	
					}
						
						
				$j++;
				
			}
			
		}

		
		
		$this->db->close();
		$this->db->cache_delete_all();
		if(isset($this->dbsms)){
			$this->dbsms->close();
			$this->dbsms->cache_delete_all();
		}
		if(isset($this->dbsms)){
			$this->dbsmsalat->close();
			$this->dbsmsalat->cache_delete_all();
		}
		
		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH Check Last Info %s to %s \r\n", $nowdate, $enddate);
				$title_name = "AUTOCHECK ".$groupname;
				$statusname = "FINISH";
				$message = urlencode(
						"".$title_name." \n".
						"Start: ".$nowdate." \n".
						"End: ".$enddate." \n".
						"Total Unit: ".$totalvehicle." \n".
						"Status: ".$statusname." \n"
				);
				sleep(2);		
				$sendtelegram = $this->telegram_direct("-742300146",$message); //telegram FMS AUTOCHECK
				printf("===SENT TELEGRAM OK\r\n");	
				printf("============================== \r\n");

	}
	
	function autocheck_backup($groupname="", $userid="", $order="asc") //+wim
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d H:i:s');
		$offset=0;
		
		printf("===Search SMS Modem Config at %s \r\n", $nowdate);
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
		$m = 0;
		
		foreach($rowsmodem as $rowmodem)
		{
				if (($m+1) < $offset)
				{
					$m++;
					continue;
				}
				
			printf("===Prepare Check Last Info Modem SMS : %s (%d/%d)\n", $rowmodem->modem_configdb, ++$m, $totalmodem); 
			$modem = $rowmodem->modem_configdb;
			
			$this->db->order_by("vehicle_id","asc");
			$this->db->group_by("vehicle_device");
			$this->db->where("vehicle_status !=", 3);
			if($userid != ""){
				$this->db->where("vehicle_user_id", $userid);
			}
			$this->db->where("vehicle_modem", $modem);
			//$this->db->where("vehicle_device", "860294045083605@VT200");
			//$this->db->where("vehicle_no", "GEC 914");
			
			$q = $this->db->get("vehicle");
			
			if ($q->num_rows() == 0)
			{
				printf("===No Vehicles \r\n");
				//return;
			}
			
			$rows = $q->result();
			$totalvehicle = count($rows);
			printf("===Total Vehicle %s \r\n", $totalvehicle);
			
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				printf("===Check Last Info For %s %s %s %s (%d/%d) \n", $userid, $rows[$i]->vehicle_no, $rows[$i]->vehicle_device, $rows[$i]->vehicle_type, $j, $totalvehicle);
				$feature = array();
				$running = 0;
				
								// last position
								$vehicledevice = $rows[$i]->vehicle_device;
								$vehicleuser = $rows[$i]->vehicle_user_id;
								$vehiclecompany = $rows[$i]->vehicle_company;
								$vehicle_dblive = $rows[$i]->vehicle_dbname_live;
								$engine = "-";
								$speed = "0";
								$course = "0";
								$jalur = "";
								$hauling = "";
								
								$this->db->order_by("vehicle_id","asc");
								$this->db->where("vehicle_status !=", 3);
								//$this->db->where("vehicle_user_id", $vehicleuser);
								//$this->db->where("vehicle_device", $vehicledevice);
								$this->db->where("vehicle_id", $rows[$i]->vehicle_id);
								$qv = $this->db->get("vehicle");
							
								if ($qv->num_rows() == 0)
								{
									printf("===No Data Vehicle \r\n");
									$running = 0;
								}
								else{
									$running = 1;
								}
							
								$rowvehicle = $qv->row();
								//$rowvehicles = $qv->result();
								/*
								$t = $rowvehicle->vehicle_active_date2;
								$now = date("Ymd");
								
								if ($t < $now)
								{
									printf("Mobil Expired \r\n");
								}
								*/
					
						if($rowvehicle->vehicle_status != 3)
						{
									
								//list($name, $host) = explode("@", $rowvehicle->vehicle_device);
								$vehicledata = explode("@", $rowvehicle->vehicle_device);
								
								$gps = $this->gpsmodel->GetLastInfo($vehicledata[0], $vehicledata[1], true, false, 0, $rowvehicle->vehicle_type);
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
											
										/*$status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
										$status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
										$status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
											
										$engine = $status1 ? "ON" : "OFF";
										*/
										if(substr($rowinfo->gps_info_io_port, 4, 1) == 1){
											$engine = "ON";
										}else{
											$engine = "OFF";
										}
			
									}			
									
								}

								$this->db = $this->load->database("default", TRUE);
								$this->dbsms = $this->load->database("smscolo",true);
								
								if($rowvehicle->vehicle_modem == "0" || $rowvehicle->vehicle_modem == "" ){
									$this->dbsmsalat = $this->load->database("smsalat",true);
								}else{
									$this->dbsmsalat = $this->load->database($rowvehicle->vehicle_modem,true);
								}
							
								$skip = 0;
								
								if(isset($gps->gps_timestamp))
								{
									//if($rowvehicle->vehicle_type == "TK315" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK315N" || $rowvehicle->vehicle_type == "TK309N" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "GT06N"){
									if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))){
										$gps_realtime = ($gps->gps_timestamp-7*3600);
									}else{
										$gps_realtime = $gps->gps_timestamp;
									}
									//$delta = ((mktime() - $gps_realtime) - 3600); //dikurangi 3600 detik karena error time
									$delta = ((mktime() - $gps_realtime));
									
									printf("===GPS Time: %s  \r\n", date("Y-m-d H:i:s", $gps_realtime));
									printf("===Vehicle No %s \r\n", $rowvehicle->vehicle_no);
									printf("===Speed %s, Engine %s \r\n", $gps->gps_speed, $engine);
									
									
									//get parameter gps
									//if($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5PULSE" || $rowvehicle->vehicle_type == "T5DOOR" || 	$rowvehicle->vehicle_type == "T5SILVER" || $rowvehicle->vehicle_type == "T8" || $rowvehicle->vehicle_type == "T8_2"){
									if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))){
										$lastposition = $this->getPosition_other($gps->gps_longitude, $gps->gps_latitude);
									}else{
										$lastposition = $this->getPosition($gps->gps_longitude, $gps->gps_ew, $gps->gps_latitude, $gps->gps_ns);
									}
									
									
									if($gps->gps_status == "A"){
										$gpsvalidstatus = "OK";
									}else{
										$gpsvalidstatus = "NOT OK";
									}
									
									$street_register = array("PORT BIB","PORT BIR","PORT TIA",
														//"ROM 01","ROM 01/02 ROAD","ROM 02","ROM 03","ROM 03/04 ROAD","ROM 04","ROM 05","ROM 06","ROM 06 ROAD",
														//"ROM O7","ROM 07/08 ROAD","ROM 08","ROM 09","ROM 10",
														"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
														"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST",
														"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
														
														"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
														"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT",
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
														"PORT BIB - Antrian","Port BIB - Antrian");
														
									$port_register = array("BIB CP 1","BIB CP 7","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 2","BIB CP 6",
														   "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
														   "PORT BIB","PORT BIR","PORT TIA"
														   
														   );
														   
									$rom_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom", "Non BIB Angsana","Non BIB TBR/SDJ"
										
													      
														  );
														  
									$nonbib_register = array("SUNGAI DANAU","KINTAB","sungai danau","kintab","Non BIB Angsana","Non BIB TBR/SDJ"
																);
														  
									$bayah_muatan_register = array("Port BIB - Antrian","PORT BIB - Antrian","Port BIR - Antrian WB" );
									$bayah_kosongan_register = array("Port BIB - Kosongan 1","Port BIB - Kosongan 2","Port BIR - Kosongan 1",
																	 "Port BIR - Kosongan 2", "Simpang Bayah - Kosongan");
																	 
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
									
									
									if(isset($lastposition)){
										$ex_lastposition = explode(",",$lastposition->display_name);
										$street_name = $ex_lastposition[0]; 
										
										//$street_name = "Jalur TIA Utara"; //hardoce
										
										if (in_array($street_name, $street_register)){
											$hauling = "in";
										}else{
											
											$hauling = "out";
										}
										
										printf("===Location %s \r\n", $street_name);
										
										//redzone check here
										$redzone_status = 0;
										$warningzone_status = 0;
										$nonbib_status = 0; 
										$port_status = 0;
										$redzone_area = array("STU area","PCN area");
										$warningzone_area = array("Jalur TIA Utara","Jalur TIA Selatan","WR 01","WR 02","WR 03","WR 04","WR 05","WR 06","WR 07");
															  
															  
										$lastdata_json = json_decode($rowvehicle->vehicle_autocheck);
										//print_r($lastdata_json=>auto_last_rom_name);exit();
										
										$auto_last_rom_name = $lastdata_json->auto_last_rom_name;
										$auto_last_rom_time = $lastdata_json->auto_last_rom_time;
										$auto_last_port_name = $lastdata_json->auto_last_port_name;
										$auto_last_port_time = $lastdata_json->auto_last_port_time;
										
										/* $last_rom_name = "";
										$last_rom_time = "";
										$last_port_name = "";
										$last_port_time = ""; */
										
										if (in_array($street_name, $rom_register))
										{
											$now_rom_name = $street_name;
											$now_rom_time = date("Y-m-d H:i:s", $gps_realtime);
											printf("X==ROM CHECKING  \r\n");
											//sementara untuk input all data
											
											if($auto_last_rom_name == ""){
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("X==Data Awal ROM \r\n");
												
											}
											else
											{
												//update data tiap masuk ROM 
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("X==Masih di ROM \r\n");
												
											}
											
											
										}
										
										if (in_array($street_name, $port_register))
										{
											$now_port_name = $street_name;
											$now_port_time = date("Y-m-d H:i:s", $gps_realtime);
											printf("Y==PORT CHECKING \r\n");
											//sementara untuk input all data
											
											if($auto_last_port_name == ""){
												$auto_last_port_name = $street_name;
												$auto_last_port_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("Y==Data Awal PORT \r\n");
												//exit();
											}
											else
											{
												//jika port lama beda dengan PORT sekarang, maka update ke yg baru
												$auto_last_port_name = $street_name;
												$auto_last_port_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("Y==Masih di PORT \r\n");
											}
											
											$port_status = 1;
											
											
										}
										
										//rssult redzone
										if (in_array($street_name, $redzone_area)){
										//if (in_array($street_name, $redzone_area)){											
											$redzone_status = 1;
											$redzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===REDZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, date("Y-m-d H:i:s", $gps_realtime), $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$redzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													$redzone_type = "Z1";
													
													$lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicledata[0],$rowvehicle->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													}
													
													
												}
											}
											
											
										}
										
										//rssult warningzone
										if (in_array($street_name, $warningzone_area)){
											$warningzone_status = 1;
											$warningzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===WARNINGZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, date("Y-m-d H:i:s", $gps_realtime), $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$warningzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													$warningzone_type = "Z1";
													
													$lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicledata[0],$rowvehicle->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													}
													
													
												}
											}
											
											
										}
										
										$overspeed_status = 0;
										//send overspeed alert
										/* if (in_array($street_name, $street_onduty)){
											$overspeed_status = 0;
										}else{
											$overspeed_status = 0;
										} */
										
									}
									
									$lastlat = $gps->gps_latitude_real;
									$lastlong = $gps->gps_longitude_real;
									$speed = number_format($gps->gps_speed*1.852, 0, "", ".");
									$course = $gps->gps_course;
									
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
									
									$coordinate = $lastlat.",".$lastlong;
									$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
									printf("===Location %s, Jalur %s , Hauling %s \r\n", $street_name, $jalur, $hauling); //print_r("DISINI");exit();
									
									
									if($redzone_status == 1){
										
											$title_name = "REDZONE DETECTED!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n".
														"Last ROM: ".$lastrom_text." \n".
														"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											
											if($redzone_type == "Z1"){
												//$sendtelegram = $this->telegram_direct("-738419382",$message); //telegram RED ZONE CHECK
												$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											}else{
												//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
												$sendtelegram = $this->telegram_direct("-632059478",$message); //telegram BIB REDZONE
											}
											
											
											printf("===SENT TELEGRAM OK\r\n");	
											
										
									}
									
									if($warningzone_status == 1){
										
											$title_name = "WARNING ZONE DETECTED!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
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
										
									
									if ($overspeed_status == 1){
										$rowgeofence = $this->getGeofence_location_live($gps->gps_longitude_real, $gps->gps_latitude_real, $vehicle_dblive);
										$telegram_group = $this->get_telegramgroup_overspeed($vehiclecompany);
										
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
											
											$gpsspeed_kph = $speed;
										}
										printf("===GEO CHECKING, Position : %s Geofence : %s Jalur: %s \r\n", $street_name, $geofence_name, $jalur);
										printf("===GEO CHECKING, Speed : %s Limit : %s \r\n", $gpsspeed_kph, $geofence_speed_limit);
										
										if($gpsspeed_kph <= $geofence_speed_limit){
											$skip_spd_sent = 1;
										}else{
											$skip_spd_sent = 0;
											
										}
										
										if($geofence_speed_limit == 0){
											$skip_spd_sent = 1;
										}else{
											$skip_spd_sent = 0;
										}
										
										if($skip_spd_sent == 0){
											$gpsspeed_kph = $gpsspeed_kph-3;
											$geofence_speed_limit = $geofence_speed_limit-3;
											$driver_name = "";
											$title_name = "OVERSPEED ALARM";
											$message = urlencode(
												"".$title_name." \n".
												"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
												"Vehicle No: ".$rowvehicle->vehicle_no." \n".
												"Driver: ".$driver_name." \n".
												"Position: ".$street_name." \n".
												"Coordinate: ".$url." \n".
												"Speed (kph): ".$gpsspeed_kph." \n".
												"Rambu (kph): ".$geofence_speed_limit." \n".
												"Geofence: ".$geofence_name." \n".
												"Jalur: ".$jalur." \n"
												
												);
												
											//printf("===Message : %s \r\n", $message);
											sleep(2);
											$sendtelegram = $this->telegram_direct($telegram_group,$message);
											printf("===SENT TELEGRAM OVERSPEED OK\r\n");	
										}else{
											
											printf("X==SKIP SENT OVERSPEED TELEGRAM\r\n");	
										}
									}
									
									//Kondisi Engine Error
									if($gps->gps_speed > 4 && $engine == "OFF"){
										printf("===SEND EMAIL ENGINE ERROR TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."ENGINE ERROR";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: GPS ENGINE OFF dan Mobil Jalan, Segera cek Historynya!!

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT ENGINE ERROR \r\n"); 
									}
									
									//cek vehicle INFO
									if ($rowvehicle->vehicle_info)
									{
										$json = json_decode($rowvehicle->vehicle_info);
											
										if (isset($json->vehicle_ip) && isset($json->vehicle_port))
										{
											$vehicle_port = $json->vehicle_port;
										}
									}
											
									$vehicle_operator_ex = explode(" ", $rowvehicle->vehicle_operator);
									$vehicle_operator = strtolower($vehicle_operator_ex[0]);
								
									if($rowvehicle->vehicle_dbname_live == "0")
									{ 
										$dblive = "0";
									}else{
										$dblive = "1";
									}
									
									printf("===Vehicle Data %s %s %s \r\n", $rowvehicle->vehicle_type, $vehicle_port, $vehicle_operator, $dblive);
									
									
									//cek delay kurang dari 1 jam
									if ($delta >= 3600 && $delta <= 86400) //lebih jam kurang dari 24 jam //yellow condition
									{
										printf("=================GPS KUNING================ \r\n");
										printf("===Vehicle No %s Tidak Update \r\n", $rowvehicle->vehicle_no);
										$statuscode = "K";
										
										//cek log pengiriman hari ini
										$command_log = $this->get_command_log($rowvehicle->vehicle_device,$nowdate,$rowmodem->modem_limit_time); 
										
										//jika 0 belum ada kirim sms maka send command
										if($command_log == "0"){
											$command_restart = $this->get_command($rowvehicle->vehicle_type,$vehicle_port,$vehicle_operator,$dblive);
											
											//cek outbox
											printf("===Check Outbox SMS Alat %s \r\n", $rowvehicle->vehicle_modem);
											
											$this->dbsmsalat->select("count(*) as total");
											$qt = $this->dbsmsalat->get("outbox");
											$rt = $qt->row();
											$total = $rt->total;
											
											if(isset($total))
											{
												if ($total > 41 )
												{
													printf("===OUTBOX LEBIH BESAR DARI 40 SMS ! \r\n");
													printf("===SKIP INSERT ! \r\n");
													$skip = 1;
												}
											}
											$skip = 1;///DI UJI COBA(R12)
											if($skip == 0){
												
												printf("===Send Restart Command To : %s \r\n", $rowvehicle->vehicle_card_no);	
												
												if($command_restart->restart_step1_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+2 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_1);
													$datasms_1["UpdatedInDB"] = $nowdate_sms;
													$datasms_1["InsertIntoDB"] = $nowdate_sms;
													$datasms_1["SendingDateTime"] = $nowdate_sms;
													$datasms_1["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_1["TextDecoded"] = $command_restart->restart_step1_command;
													$datasms_1["SendingTimeOut"] = $nowdate_sms;
													//$datasms_1["RecipientID"] = $rowvehicle->vehicle_modem;
													
													$this->dbsmsalat->insert("outbox",$datasms_1);
													printf("===INSERT STEP 1=== %s \r\n",$command_restart->restart_step1_command );
												}
												if($command_restart->restart_step2_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+5 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_2);
													$datasms_2["UpdatedInDB"] = $nowdate_sms;
													$datasms_2["InsertIntoDB"] = $nowdate_sms;
													$datasms_2["SendingDateTime"] = $nowdate_sms;
													$datasms_2["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_2["TextDecoded"] = $command_restart->restart_step2_command;
													$datasms_2["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_2);
													printf("===INSERT STEP 2=== %s \r\n",$command_restart->restart_step2_command );
												}
												if($command_restart->restart_step3_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+8 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_3);
													$datasms_3["UpdatedInDB"] = $nowdate_sms;
													$datasms_3["InsertIntoDB"] = $nowdate_sms;
													$datasms_3["SendingDateTime"] = $nowdate_sms;
													$datasms_3["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_3["TextDecoded"] = $command_restart->restart_step3_command;
													$datasms_3["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_3);
													printf("===INSERT STEP 3=== %s \r\n",$command_restart->restart_step3_command );
												}
												if($command_restart->restart_step4_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+11 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_4);
													$datasms_4["UpdatedInDB"] = $nowdate_sms;
													$datasms_4["InsertIntoDB"] = $nowdate_sms;
													$datasms_4["SendingDateTime"] = $nowdate_sms;
													$datasms_4["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_4["TextDecoded"] = $command_restart->restart_step4_command;
													$datasms_4["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_4);
													printf("===INSERT STEP 4=== %s \r\n",$command_restart->restart_step4_command );
												}
												
													//insert log
													unset($datasms_log);
													$datasms_log["log_user"] = $rowvehicle->vehicle_user_id;
													$datasms_log["log_vehicle"] = $rowvehicle->vehicle_no;
													$datasms_log["log_device"] = $rowvehicle->vehicle_device;
													$datasms_log["log_type"] = $rowvehicle->vehicle_type;
													$datasms_log["log_simcard"] = $rowvehicle->vehicle_card_no;
													$datasms_log["log_command"] = $command_restart->restart_step1_command."|".$command_restart->restart_step2_command."|".								$command_restart->restart_step3_command."|".$command_restart->restart_step4_command;
													$datasms_log["log_date"] = date("Y-m-d", strtotime($nowdate));
													$datasms_log["log_created"] = $nowdate;
													
													$this->db->insert("sms_restart_log",$datasms_log);
													printf("===INSERT LOG OK=== \r\n");		
													
												
											}else{
												printf("===SKIP INSERT OUTBOX PENUH=== \r\n");
											}

										}else{
												printf("===SKIP INSERT SUDAH ADA DI LOG HARI INI=== \r\n");
												//printf("===SEND EMAIL TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS KUNING";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem tidak bisa diupdate

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT YELLOW \r\n"); 
													
										}
										
									}
									else if($delta >= 43201) //lebih dari 1 hari //red condition 
									{
										$statuscode = "M";
										printf("======================RED CONDITION======================== \r\n");
										
										unset($datavehicle);
										$datavehicle["vehicle_isred"] = 1;
										$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
										$this->db->update("vehicle", $datavehicle);
										printf("===UPDATED STATUS IS RED YES=== %s \r\n", $rowvehicle->vehicle_no);
										
										
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS MERAH";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem GPS MERAH

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT RED \r\n"); 
									}
									else //gps update condition
									{
										$statuscode = "P";
										
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
										
										if($nonbib_status == 1)
										{
										
											$title_name = "NON BIB ACTIVITY!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n"
														//"Last ROM: ".$lastrom_text." \n".
														//"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											$sendtelegram = $this->telegram_direct($telegram_geofence,$message);
											
											printf("===SENT TELEGRAM OK\r\n");	
										}
										
										if($port_status == 1)
										{
											//get data WIM by NO lambung
											$lastdatawim = $this->getLastDataWIM($rowvehicle,$gps_realtime);
											
										}
										
										
										if($gps->gps_status == "V"){
											printf("===Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
											//printf("===SEND EMAIL TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS NOT OK";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem GPS NOT OK

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT NOT OK \r\n"); 
											
										}else{
											printf("=================GPS UPDATE================ \r\n");
											//update log command flag == 1
											$command_reset = $this->reset_command_log($rowvehicle->vehicle_device,$nowdate);
											if($command_reset == true){
												printf("===UPDATE CONFIG \r\n");
											}
											
											unset($datavehicle);
											$datavehicle["vehicle_isred"] = 0;
											$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
											$this->db->update("vehicle", $datavehicle);
											printf("===UPDATED STATUS DEFAULT=== %s \r\n", $rowvehicle->vehicle_no);
											
										}
									}
									
									//update master vehicle autocheck
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = $statuscode;
										$datacheck["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$datacheck["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
										$datacheck["auto_change_position"] = $lastposition->display_name;
										$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
										
										$this->db->insert("vehicle_autocheck",$datacheck);
										printf("===INSERT AUTOCHECK=== \r\n");	

										//json										
										$feature["auto_status"] = $statuscode;
										$feature["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = $statuscode;
										$datacheck["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$feature["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
											$datacheck["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
											$datacheck["auto_change_position"] = $lastposition->display_name;
											$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
											
											//json
											$feature["auto_change_engine_status"] = $engine;
											$feature["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
											$feature["auto_change_position"] = $lastposition->display_name;
											$feature["auto_change_coordinate"] = $lastlat.",".$lastlong;
										}
										
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
										$this->db->update("vehicle_autocheck",$datacheck);
										printf("===UPDATE AUTOCHECK=== \r\n");	
										
										
										
											
									}
										
								
									
								
								
								
								
								
								
								
								}
								else
								{
									printf("===NO DATA=== \r\n");	
									unset($datavehicle);
									$datavehicle["vehicle_isred"] = 1;
									$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
									$this->db->update("vehicle", $datavehicle);
									printf("===UPDATED STATUS IS RED (NO DATA) YES=== %s \r\n", $rowvehicle->vehicle_no);
										
									//update master vehicle (khusus vehicle GO TO History)
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									//$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
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
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
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
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
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
						}
						else
						{
							
									//update master vehicle (khusus vehicle HIDE)
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									//$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = "";
										$datacheck["auto_last_update"] = "";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_last_hauling"] = "";
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										$datacheck["auto_flag"] = 1;
										
														
										$this->db->insert("vehicle_autocheck",$datacheck);
										printf("===INSERT AUTOCHECK=== \r\n");

										//json										
										$feature["auto_status"] = "";
										$feature["auto_last_update"] = "";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										$feature["auto_flag"] = 1;
										$feature["vehicle_gotohistory"] = 1;
										$vehicle_gotohistory = 1;	
														
									}else{
										//update
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = "";
										$datacheck["auto_last_update"] = "";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_last_hauling"] = "";
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										$datacheck["auto_flag"] = 1;
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
										$this->db->update("vehicle_autocheck",$datacheck);
										printf("===UPDATE AUTOCHECK=== \r\n");	
										
										//json
										$feature["auto_status"] = "";
										$feature["auto_last_update"] = "";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										
										$feature["auto_flag"] = 1;
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
						
						$this->db->where("vehicle_id", $rows[$i]->vehicle_id);	
						$this->db->limit(1);	
						$this->db->update("vehicle",$datajson);
						printf("===UPDATE JSON MASTER VEHICLE=== \r\n");	
					}
						
						
				$j++;
				
			}
			
		}

		
		
		$this->db->close();
		$this->db->cache_delete_all();
		if(isset($this->dbsms)){
			$this->dbsms->close();
			$this->dbsms->cache_delete_all();
		}
		if(isset($this->dbsms)){
			$this->dbsmsalat->close();
			$this->dbsmsalat->cache_delete_all();
		}
		
		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH Check Last Info %s to %s \r\n", $nowdate, $enddate);
				$title_name = "AUTOCHECK ".$groupname;
				$statusname = "FINISH";
				$message = urlencode(
						"".$title_name." \n".
						"Start: ".$nowdate." \n".
						"End: ".$enddate." \n".
						"Total Unit: ".$totalvehicle." \n".
						"Status: ".$statusname." \n"
				);
				sleep(2);		
				$sendtelegram = $this->telegram_direct("-742300146",$message); //telegram FMS AUTOCHECK
				printf("===SENT TELEGRAM OK\r\n");	
				printf("============================== \r\n");

	}
	
	function autocheck_bk_2022_05_18($groupname="", $userid="", $order="asc") //+wim + wim etime
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d H:i:s');
		$offset=0;
		
		printf("===Search SMS Modem Config at %s \r\n", $nowdate);
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
		$m = 0;
		
		foreach($rowsmodem as $rowmodem)
		{
				if (($m+1) < $offset)
				{
					$m++;
					continue;
				}
				
			printf("===Prepare Check Last Info Modem SMS : %s (%d/%d)\n", $rowmodem->modem_configdb, ++$m, $totalmodem); 
			$modem = $rowmodem->modem_configdb;
			
			$this->db->order_by("vehicle_id","asc");
			$this->db->group_by("vehicle_device");
			$this->db->where("vehicle_status !=", 3);
			if($userid != ""){
				$this->db->where("vehicle_user_id", $userid);
			}
			$this->db->where("vehicle_modem", $modem);
			//$this->db->where("vehicle_device", "860294045083605@VT200");
			//$this->db->where("vehicle_no", "GEC 914");
			
			$q = $this->db->get("vehicle");
			
			if ($q->num_rows() == 0)
			{
				printf("===No Vehicles \r\n");
				//return;
			}
			
			$rows = $q->result();
			$totalvehicle = count($rows);
			printf("===Total Vehicle %s \r\n", $totalvehicle);
			
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				printf("===Check Last Info For %s %s %s %s (%d/%d) \n", $userid, $rows[$i]->vehicle_no, $rows[$i]->vehicle_device, $rows[$i]->vehicle_type, $j, $totalvehicle);
				$feature = array();
				$running = 0;
				
								// last position
								$vehicledevice = $rows[$i]->vehicle_device;
								$vehicleuser = $rows[$i]->vehicle_user_id;
								$vehiclecompany = $rows[$i]->vehicle_company;
								$vehicle_dblive = $rows[$i]->vehicle_dbname_live;
								$engine = "-";
								$speed = "0";
								$course = "0";
								$jalur = "";
								$hauling = "";
								
								$this->db->order_by("vehicle_id","asc");
								$this->db->where("vehicle_status !=", 3);
								//$this->db->where("vehicle_user_id", $vehicleuser);
								//$this->db->where("vehicle_device", $vehicledevice);
								$this->db->where("vehicle_id", $rows[$i]->vehicle_id);
								$qv = $this->db->get("vehicle");
							
								if ($qv->num_rows() == 0)
								{
									printf("===No Data Vehicle \r\n");
									$running = 0;
								}
								else{
									$running = 1;
								}
							
								$rowvehicle = $qv->row();
								//$rowvehicles = $qv->result();
								/*
								$t = $rowvehicle->vehicle_active_date2;
								$now = date("Ymd");
								
								if ($t < $now)
								{
									printf("Mobil Expired \r\n");
								}
								*/
					
						if($rowvehicle->vehicle_status != 3)
						{
									
								//list($name, $host) = explode("@", $rowvehicle->vehicle_device);
								$vehicledata = explode("@", $rowvehicle->vehicle_device);
								
								$gps = $this->gpsmodel->GetLastInfo($vehicledata[0], $vehicledata[1], true, false, 0, $rowvehicle->vehicle_type);
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
											
										/*$status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
										$status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
										$status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
											
										$engine = $status1 ? "ON" : "OFF";
										*/
										if(substr($rowinfo->gps_info_io_port, 4, 1) == 1){
											$engine = "ON";
										}else{
											$engine = "OFF";
										}
			
									}			
									
								}

								$this->db = $this->load->database("default", TRUE);
								$this->dbsms = $this->load->database("smscolo",true);
								
								if($rowvehicle->vehicle_modem == "0" || $rowvehicle->vehicle_modem == "" ){
									$this->dbsmsalat = $this->load->database("smsalat",true);
								}else{
									$this->dbsmsalat = $this->load->database($rowvehicle->vehicle_modem,true);
								}
							
								$skip = 0;
								
								if(isset($gps->gps_timestamp))
								{
									//if($rowvehicle->vehicle_type == "TK315" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK315N" || $rowvehicle->vehicle_type == "TK309N" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "GT06N"){
									if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))){
										$gps_realtime = ($gps->gps_timestamp-7*3600);
									}else{
										$gps_realtime = $gps->gps_timestamp;
									}
									//$delta = ((mktime() - $gps_realtime) - 3600); //dikurangi 3600 detik karena error time
									$delta = ((mktime() - $gps_realtime));
									
									printf("===GPS Time: %s  \r\n", date("Y-m-d H:i:s", $gps_realtime));
									printf("===Vehicle No %s \r\n", $rowvehicle->vehicle_no);
									printf("===Speed %s, Engine %s \r\n", $gps->gps_speed, $engine);
									
									
									//get parameter gps
									//if($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5PULSE" || $rowvehicle->vehicle_type == "T5DOOR" || 	$rowvehicle->vehicle_type == "T5SILVER" || $rowvehicle->vehicle_type == "T8" || $rowvehicle->vehicle_type == "T8_2"){
									if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))){
										$lastposition = $this->getPosition_other($gps->gps_longitude, $gps->gps_latitude);
									}else{
										$lastposition = $this->getPosition($gps->gps_longitude, $gps->gps_ew, $gps->gps_latitude, $gps->gps_ns);
									}
									
									
									if($gps->gps_status == "A"){
										$gpsvalidstatus = "OK";
									}else{
										$gpsvalidstatus = "NOT OK";
									}
									
									$street_register = array("PORT BIB","PORT BIR","PORT TIA",
														//"ROM 01","ROM 01/02 ROAD","ROM 02","ROM 03","ROM 03/04 ROAD","ROM 04","ROM 05","ROM 06","ROM 06 ROAD",
														//"ROM O7","ROM 07/08 ROAD","ROM 08","ROM 09","ROM 10",
														"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
														"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST",
														"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
														
														"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
														"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT",
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
														"PORT BIB - Antrian","Port BIB - Antrian");
														
									$port_register = array("BIB CP 1","BIB CP 7","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 2","BIB CP 6",
														   "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
														   "PORT BIB","PORT BIR","PORT TIA"
														   
														   );
														   
									$rom_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom", "Non BIB Angsana","Non BIB TBR/SDJ"
										
													      
														  );
									$wim_register = array("KM 13","KM 13.5"
														 );
														  
									$nonbib_register = array("SUNGAI DANAU","KINTAB","sungai danau","kintab","Non BIB Angsana","Non BIB TBR/SDJ"
															);
															
														  
									$bayah_muatan_register = array("Port BIB - Antrian","PORT BIB - Antrian","Port BIR - Antrian WB" );
									$bayah_kosongan_register = array("Port BIB - Kosongan 1","Port BIB - Kosongan 2","Port BIR - Kosongan 1",
																	 "Port BIR - Kosongan 2", "Simpang Bayah - Kosongan");
																	 
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
									
									
									if(isset($lastposition)){
										$ex_lastposition = explode(",",$lastposition->display_name);
										$street_name = $ex_lastposition[0]; 
										
										//$street_name = "Jalur TIA Utara"; //hardoce
										
										if (in_array($street_name, $street_register)){
											$hauling = "in";
										}else{
											
											$hauling = "out";
										}
										
										printf("===Location %s \r\n", $street_name);
										
										//redzone check here
										$redzone_status = 0;
										$warningzone_status = 0;
										$nonbib_status = 0; 
										$port_status = 0;
										$redzone_area = array("STU area","PCN area");
										$warningzone_area = array("Jalur TIA Utara","Jalur TIA Selatan","WR 01","WR 02","WR 03","WR 04","WR 05","WR 06","WR 07");
															  
															  
										$lastdata_json = json_decode($rowvehicle->vehicle_autocheck);
										//print_r($lastdata_json=>auto_last_rom_name);exit();
										
										$auto_last_rom_name = $lastdata_json->auto_last_rom_name;
										$auto_last_rom_time = $lastdata_json->auto_last_rom_time;
										$auto_last_port_name = $lastdata_json->auto_last_port_name;
										$auto_last_port_time = $lastdata_json->auto_last_port_time;
										
										/* $last_rom_name = "";
										$last_rom_time = "";
										$last_port_name = "";
										$last_port_time = ""; */
										
										if (in_array($street_name, $rom_register))
										{
											$now_rom_name = $street_name;
											$now_rom_time = date("Y-m-d H:i:s", $gps_realtime);
											printf("X==ROM CHECKING  \r\n");
											//sementara untuk input all data
											
											if($auto_last_rom_name == ""){
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("X==Data Awal ROM \r\n");
												
											}
											else
											{
												//update data tiap masuk ROM 
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("X==Masih di ROM \r\n");
												
											}
											
											
										}
										
										if (in_array($street_name, $port_register))
										{
											$now_port_name = $street_name;
											$now_port_time = date("Y-m-d H:i:s", $gps_realtime);
											printf("Y==PORT CHECKING \r\n");
											//sementara untuk input all data
											
											if($auto_last_port_name == ""){
												$auto_last_port_name = $street_name;
												$auto_last_port_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("Y==Data Awal PORT \r\n");
												//exit();
											}
											else
											{
												//jika port lama beda dengan PORT sekarang, maka update ke yg baru
												$auto_last_port_name = $street_name;
												$auto_last_port_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("Y==Masih di PORT \r\n");
											}
											
											$port_status = 1;
											
											
										}
										
										//rssult redzone
										if (in_array($street_name, $redzone_area)){
										//if (in_array($street_name, $redzone_area)){											
											$redzone_status = 1;
											$redzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===REDZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, date("Y-m-d H:i:s", $gps_realtime), $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$redzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													$redzone_type = "Z1";
													
													$lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicledata[0],$rowvehicle->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													}
													
													
												}
											}
											
											
										}
										
										//rssult warningzone
										if (in_array($street_name, $warningzone_area)){
											$warningzone_status = 1;
											$warningzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===WARNINGZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, date("Y-m-d H:i:s", $gps_realtime), $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$warningzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													$warningzone_type = "Z1";
													
													$lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicledata[0],$rowvehicle->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													}
													
													
												}
											}
											
											
										}
										
										
										//result km 13 wim
										if (in_array($street_name, $wim_register))
										{
											//update field master data wim time (vehicle_wim_stime)
											$wim_time = $this->wim_updatetime($rowvehicle,$street_name,date("Y-m-d H:i:s", $gps_realtime));
											
										}
										
										$overspeed_status = 0;
										//send overspeed alert
										/* if (in_array($street_name, $street_onduty)){
											$overspeed_status = 0;
										}else{
											$overspeed_status = 0;
										} */
										
									}
									
									$lastlat = $gps->gps_latitude_real;
									$lastlong = $gps->gps_longitude_real;
									$speed = number_format($gps->gps_speed*1.852, 0, "", ".");
									$course = $gps->gps_course;
									
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
									
									$coordinate = $lastlat.",".$lastlong;
									$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
									printf("===Location %s, Jalur %s , Hauling %s \r\n", $street_name, $jalur, $hauling); //print_r("DISINI");exit();
									
									
									if($redzone_status == 1){
										
											$title_name = "REDZONE DETECTED!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n".
														"Last ROM: ".$lastrom_text." \n".
														"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											
											if($redzone_type == "Z1"){
												//$sendtelegram = $this->telegram_direct("-738419382",$message); //telegram RED ZONE CHECK
												$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											}else{
												//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
												$sendtelegram = $this->telegram_direct("-632059478",$message); //telegram BIB REDZONE
											}
											
											
											printf("===SENT TELEGRAM OK\r\n");	
											
										
									}
									
									if($warningzone_status == 1){
										
											$title_name = "WARNING ZONE DETECTED!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
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
										
									
									if ($overspeed_status == 1){
										$rowgeofence = $this->getGeofence_location_live($gps->gps_longitude_real, $gps->gps_latitude_real, $vehicle_dblive);
										$telegram_group = $this->get_telegramgroup_overspeed($vehiclecompany);
										
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
											
											$gpsspeed_kph = $speed;
										}
										printf("===GEO CHECKING, Position : %s Geofence : %s Jalur: %s \r\n", $street_name, $geofence_name, $jalur);
										printf("===GEO CHECKING, Speed : %s Limit : %s \r\n", $gpsspeed_kph, $geofence_speed_limit);
										
										if($gpsspeed_kph <= $geofence_speed_limit){
											$skip_spd_sent = 1;
										}else{
											$skip_spd_sent = 0;
											
										}
										
										if($geofence_speed_limit == 0){
											$skip_spd_sent = 1;
										}else{
											$skip_spd_sent = 0;
										}
										
										if($skip_spd_sent == 0){
											$gpsspeed_kph = $gpsspeed_kph-3;
											$geofence_speed_limit = $geofence_speed_limit-3;
											$driver_name = "";
											$title_name = "OVERSPEED ALARM";
											$message = urlencode(
												"".$title_name." \n".
												"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
												"Vehicle No: ".$rowvehicle->vehicle_no." \n".
												"Driver: ".$driver_name." \n".
												"Position: ".$street_name." \n".
												"Coordinate: ".$url." \n".
												"Speed (kph): ".$gpsspeed_kph." \n".
												"Rambu (kph): ".$geofence_speed_limit." \n".
												"Geofence: ".$geofence_name." \n".
												"Jalur: ".$jalur." \n"
												
												);
												
											//printf("===Message : %s \r\n", $message);
											sleep(2);
											$sendtelegram = $this->telegram_direct($telegram_group,$message);
											printf("===SENT TELEGRAM OVERSPEED OK\r\n");	
										}else{
											
											printf("X==SKIP SENT OVERSPEED TELEGRAM\r\n");	
										}
									}
									
									//Kondisi Engine Error
									if($gps->gps_speed > 4 && $engine == "OFF"){
										printf("===SEND EMAIL ENGINE ERROR TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."ENGINE ERROR";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: GPS ENGINE OFF dan Mobil Jalan, Segera cek Historynya!!

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT ENGINE ERROR \r\n"); 
									}
									
									//cek vehicle INFO
									if ($rowvehicle->vehicle_info)
									{
										$json = json_decode($rowvehicle->vehicle_info);
											
										if (isset($json->vehicle_ip) && isset($json->vehicle_port))
										{
											$vehicle_port = $json->vehicle_port;
										}
									}
											
									$vehicle_operator_ex = explode(" ", $rowvehicle->vehicle_operator);
									$vehicle_operator = strtolower($vehicle_operator_ex[0]);
								
									if($rowvehicle->vehicle_dbname_live == "0")
									{ 
										$dblive = "0";
									}else{
										$dblive = "1";
									}
									
									printf("===Vehicle Data %s %s %s \r\n", $rowvehicle->vehicle_type, $vehicle_port, $vehicle_operator, $dblive);
									
									
									//cek delay kurang dari 1 jam
									if ($delta >= 3600 && $delta <= 86400) //lebih jam kurang dari 24 jam //yellow condition
									{
										printf("=================GPS KUNING================ \r\n");
										printf("===Vehicle No %s Tidak Update \r\n", $rowvehicle->vehicle_no);
										$statuscode = "K";
										
										//cek log pengiriman hari ini
										$command_log = $this->get_command_log($rowvehicle->vehicle_device,$nowdate,$rowmodem->modem_limit_time); 
										
										//jika 0 belum ada kirim sms maka send command
										if($command_log == "0"){
											$command_restart = $this->get_command($rowvehicle->vehicle_type,$vehicle_port,$vehicle_operator,$dblive);
											
											//cek outbox
											printf("===Check Outbox SMS Alat %s \r\n", $rowvehicle->vehicle_modem);
											
											$this->dbsmsalat->select("count(*) as total");
											$qt = $this->dbsmsalat->get("outbox");
											$rt = $qt->row();
											$total = $rt->total;
											
											if(isset($total))
											{
												if ($total > 41 )
												{
													printf("===OUTBOX LEBIH BESAR DARI 40 SMS ! \r\n");
													printf("===SKIP INSERT ! \r\n");
													$skip = 1;
												}
											}
											$skip = 1;///DI UJI COBA(R12)
											if($skip == 0){
												
												printf("===Send Restart Command To : %s \r\n", $rowvehicle->vehicle_card_no);	
												
												if($command_restart->restart_step1_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+2 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_1);
													$datasms_1["UpdatedInDB"] = $nowdate_sms;
													$datasms_1["InsertIntoDB"] = $nowdate_sms;
													$datasms_1["SendingDateTime"] = $nowdate_sms;
													$datasms_1["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_1["TextDecoded"] = $command_restart->restart_step1_command;
													$datasms_1["SendingTimeOut"] = $nowdate_sms;
													//$datasms_1["RecipientID"] = $rowvehicle->vehicle_modem;
													
													$this->dbsmsalat->insert("outbox",$datasms_1);
													printf("===INSERT STEP 1=== %s \r\n",$command_restart->restart_step1_command );
												}
												if($command_restart->restart_step2_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+5 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_2);
													$datasms_2["UpdatedInDB"] = $nowdate_sms;
													$datasms_2["InsertIntoDB"] = $nowdate_sms;
													$datasms_2["SendingDateTime"] = $nowdate_sms;
													$datasms_2["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_2["TextDecoded"] = $command_restart->restart_step2_command;
													$datasms_2["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_2);
													printf("===INSERT STEP 2=== %s \r\n",$command_restart->restart_step2_command );
												}
												if($command_restart->restart_step3_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+8 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_3);
													$datasms_3["UpdatedInDB"] = $nowdate_sms;
													$datasms_3["InsertIntoDB"] = $nowdate_sms;
													$datasms_3["SendingDateTime"] = $nowdate_sms;
													$datasms_3["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_3["TextDecoded"] = $command_restart->restart_step3_command;
													$datasms_3["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_3);
													printf("===INSERT STEP 3=== %s \r\n",$command_restart->restart_step3_command );
												}
												if($command_restart->restart_step4_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+11 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_4);
													$datasms_4["UpdatedInDB"] = $nowdate_sms;
													$datasms_4["InsertIntoDB"] = $nowdate_sms;
													$datasms_4["SendingDateTime"] = $nowdate_sms;
													$datasms_4["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_4["TextDecoded"] = $command_restart->restart_step4_command;
													$datasms_4["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_4);
													printf("===INSERT STEP 4=== %s \r\n",$command_restart->restart_step4_command );
												}
												
													//insert log
													unset($datasms_log);
													$datasms_log["log_user"] = $rowvehicle->vehicle_user_id;
													$datasms_log["log_vehicle"] = $rowvehicle->vehicle_no;
													$datasms_log["log_device"] = $rowvehicle->vehicle_device;
													$datasms_log["log_type"] = $rowvehicle->vehicle_type;
													$datasms_log["log_simcard"] = $rowvehicle->vehicle_card_no;
													$datasms_log["log_command"] = $command_restart->restart_step1_command."|".$command_restart->restart_step2_command."|".								$command_restart->restart_step3_command."|".$command_restart->restart_step4_command;
													$datasms_log["log_date"] = date("Y-m-d", strtotime($nowdate));
													$datasms_log["log_created"] = $nowdate;
													
													$this->db->insert("sms_restart_log",$datasms_log);
													printf("===INSERT LOG OK=== \r\n");		
													
												
											}else{
												printf("===SKIP INSERT OUTBOX PENUH=== \r\n");
											}

										}else{
												printf("===SKIP INSERT SUDAH ADA DI LOG HARI INI=== \r\n");
												//printf("===SEND EMAIL TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS KUNING";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem tidak bisa diupdate

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT YELLOW \r\n"); 
													
										}
										
									}
									else if($delta >= 43201) //lebih dari 1 hari //red condition 
									{
										$statuscode = "M";
										printf("======================RED CONDITION======================== \r\n");
										
										unset($datavehicle);
										$datavehicle["vehicle_isred"] = 1;
										$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
										$this->db->update("vehicle", $datavehicle);
										printf("===UPDATED STATUS IS RED YES=== %s \r\n", $rowvehicle->vehicle_no);
										
										
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS MERAH";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem GPS MERAH

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT RED \r\n"); 
									}
									else //gps update condition
									{
										$statuscode = "P";
										
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
										
										if($nonbib_status == 1)
										{
										
											$title_name = "NON BIB ACTIVITY!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n"
														//"Last ROM: ".$lastrom_text." \n".
														//"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											$sendtelegram = $this->telegram_direct($telegram_geofence,$message);
											
											printf("===SENT TELEGRAM OK\r\n");	
										}
										
										if($port_status == 1)
										{
											//get data WIM by NO lambung
											$lastdatawim = $this->getLastDataWIM($rowvehicle,$gps_realtime);
											
										}
										
										
										if($gps->gps_status == "V"){
											printf("===Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
											//printf("===SEND EMAIL TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS NOT OK";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem GPS NOT OK

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT NOT OK \r\n"); 
											
										}else{
											printf("=================GPS UPDATE================ \r\n");
											//update log command flag == 1
											$command_reset = $this->reset_command_log($rowvehicle->vehicle_device,$nowdate);
											if($command_reset == true){
												printf("===UPDATE CONFIG \r\n");
											}
											
											unset($datavehicle);
											$datavehicle["vehicle_isred"] = 0;
											$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
											$this->db->update("vehicle", $datavehicle);
											printf("===UPDATED STATUS DEFAULT=== %s \r\n", $rowvehicle->vehicle_no);
											
										}
									}
									
									//update master vehicle autocheck
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = $statuscode;
										$datacheck["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$datacheck["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
										$datacheck["auto_change_position"] = $lastposition->display_name;
										$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
										
										$this->db->insert("vehicle_autocheck",$datacheck);
										printf("===INSERT AUTOCHECK=== \r\n");	

										//json										
										$feature["auto_status"] = $statuscode;
										$feature["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = $statuscode;
										$datacheck["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$feature["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
											$datacheck["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
											$datacheck["auto_change_position"] = $lastposition->display_name;
											$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
											
											//json
											$feature["auto_change_engine_status"] = $engine;
											$feature["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
											$feature["auto_change_position"] = $lastposition->display_name;
											$feature["auto_change_coordinate"] = $lastlat.",".$lastlong;
										}
										
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
										$this->db->update("vehicle_autocheck",$datacheck);
										printf("===UPDATE AUTOCHECK=== \r\n");	
										
										
										
											
									}
										
								
									
								
								
								
								
								
								
								
								}
								else
								{
									printf("===NO DATA=== \r\n");	
									unset($datavehicle);
									$datavehicle["vehicle_isred"] = 1;
									$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
									$this->db->update("vehicle", $datavehicle);
									printf("===UPDATED STATUS IS RED (NO DATA) YES=== %s \r\n", $rowvehicle->vehicle_no);
										
									//update master vehicle (khusus vehicle GO TO History)
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									//$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
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
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
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
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
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
						}
						else
						{
							
									//update master vehicle (khusus vehicle HIDE)
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									//$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = "";
										$datacheck["auto_last_update"] = "";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_last_hauling"] = "";
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										$datacheck["auto_flag"] = 1;
										
														
										$this->db->insert("vehicle_autocheck",$datacheck);
										printf("===INSERT AUTOCHECK=== \r\n");

										//json										
										$feature["auto_status"] = "";
										$feature["auto_last_update"] = "";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										$feature["auto_flag"] = 1;
										$feature["vehicle_gotohistory"] = 1;
										$vehicle_gotohistory = 1;	
														
									}else{
										//update
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = "";
										$datacheck["auto_last_update"] = "";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_last_hauling"] = "";
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										$datacheck["auto_flag"] = 1;
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
										$this->db->update("vehicle_autocheck",$datacheck);
										printf("===UPDATE AUTOCHECK=== \r\n");	
										
										//json
										$feature["auto_status"] = "";
										$feature["auto_last_update"] = "";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										
										$feature["auto_flag"] = 1;
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
						
						$this->db->where("vehicle_id", $rows[$i]->vehicle_id);	
						$this->db->limit(1);	
						$this->db->update("vehicle",$datajson);
						printf("===UPDATE JSON MASTER VEHICLE=== \r\n");	
					}
						
						
				$j++;
				
			}
			
		}

		
		
		$this->db->close();
		$this->db->cache_delete_all();
		if(isset($this->dbsms)){
			$this->dbsms->close();
			$this->dbsms->cache_delete_all();
		}
		if(isset($this->dbsms)){
			$this->dbsmsalat->close();
			$this->dbsmsalat->cache_delete_all();
		}
		
		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH Check Last Info %s to %s \r\n", $nowdate, $enddate);
				$title_name = "AUTOCHECK ".$groupname;
				$statusname = "FINISH";
				$message = urlencode(
						"".$title_name." \n".
						"Start: ".$nowdate." \n".
						"End: ".$enddate." \n".
						"Total Unit: ".$totalvehicle." \n".
						"Status: ".$statusname." \n"
				);
				sleep(2);		
				$sendtelegram = $this->telegram_direct("-742300146",$message); //telegram FMS AUTOCHECK
				printf("===SENT TELEGRAM OK\r\n");	
				printf("============================== \r\n");

	}
	
	function autocheck($groupname="", $userid="", $order="asc") //+wim + wim etime + tele alert
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d H:i:s');
		$offset=0;
		
		printf("===Search SMS Modem Config at %s \r\n", $nowdate);
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
		$m = 0;
		
		foreach($rowsmodem as $rowmodem)
		{
				if (($m+1) < $offset)
				{
					$m++;
					continue;
				}
				
			printf("===Prepare Check Last Info Modem SMS : %s (%d/%d)\n", $rowmodem->modem_configdb, ++$m, $totalmodem); 
			$modem = $rowmodem->modem_configdb;
			
			$this->db->order_by("vehicle_id","asc");
			$this->db->group_by("vehicle_device");
			$this->db->where("vehicle_status !=", 3);
			if($userid != ""){
				$this->db->where("vehicle_user_id", $userid);
			}
			$this->db->where("vehicle_modem", $modem);
			//$this->db->where("vehicle_device", "860294045083605@VT200");
			//$this->db->where("vehicle_no", "GEC 914");
			
			$q = $this->db->get("vehicle");
			
			if ($q->num_rows() == 0)
			{
				printf("===No Vehicles \r\n");
				//return;
			}
			
			$rows = $q->result();
			$totalvehicle = count($rows);
			printf("===Total Vehicle %s \r\n", $totalvehicle);
			
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				printf("===Check Last Info For %s %s %s %s (%d/%d) \n", $userid, $rows[$i]->vehicle_no, $rows[$i]->vehicle_device, $rows[$i]->vehicle_type, $j, $totalvehicle);
				$feature = array();
				$running = 0;
				
								// last position
								$vehicledevice = $rows[$i]->vehicle_device;
								$vehicleuser = $rows[$i]->vehicle_user_id;
								$vehiclecompany = $rows[$i]->vehicle_company;
								$vehicle_dblive = $rows[$i]->vehicle_dbname_live;
								$engine = "-";
								$speed = "0";
								$course = "0";
								$jalur = "";
								$hauling = "";
								
								$this->db->order_by("vehicle_id","asc");
								$this->db->where("vehicle_status !=", 3);
								//$this->db->where("vehicle_user_id", $vehicleuser);
								//$this->db->where("vehicle_device", $vehicledevice);
								$this->db->where("vehicle_id", $rows[$i]->vehicle_id);
								$qv = $this->db->get("vehicle");
							
								if ($qv->num_rows() == 0)
								{
									printf("===No Data Vehicle \r\n");
									$running = 0;
								}
								else{
									$running = 1;
								}
							
								$rowvehicle = $qv->row();
								//$rowvehicles = $qv->result();
								/*
								$t = $rowvehicle->vehicle_active_date2;
								$now = date("Ymd");
								
								if ($t < $now)
								{
									printf("Mobil Expired \r\n");
								}
								*/
					
						if($rowvehicle->vehicle_status != 3)
						{
									
								//list($name, $host) = explode("@", $rowvehicle->vehicle_device);
								$vehicledata = explode("@", $rowvehicle->vehicle_device);
								
								$gps = $this->gpsmodel->GetLastInfo($vehicledata[0], $vehicledata[1], true, false, 0, $rowvehicle->vehicle_type);
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
											
										/*$status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
										$status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
										$status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
											
										$engine = $status1 ? "ON" : "OFF";
										*/
										if(substr($rowinfo->gps_info_io_port, 4, 1) == 1){
											$engine = "ON";
										}else{
											$engine = "OFF";
										}
			
									}			
									
								}

								$this->db = $this->load->database("default", TRUE);
								$this->dbsms = $this->load->database("smscolo",true);
								
								if($rowvehicle->vehicle_modem == "0" || $rowvehicle->vehicle_modem == "" ){
									$this->dbsmsalat = $this->load->database("smsalat",true);
								}else{
									$this->dbsmsalat = $this->load->database($rowvehicle->vehicle_modem,true);
								}
							
								$skip = 0;
								
								if(isset($gps->gps_timestamp))
								{
									//if($rowvehicle->vehicle_type == "TK315" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK315N" || $rowvehicle->vehicle_type == "TK309N" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "GT06N"){
									if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))){
										$gps_realtime = ($gps->gps_timestamp-7*3600);
									}else{
										$gps_realtime = $gps->gps_timestamp;
									}
									//$delta = ((mktime() - $gps_realtime) - 3600); //dikurangi 3600 detik karena error time
									$delta = ((mktime() - $gps_realtime));
									
									printf("===GPS Time: %s  \r\n", date("Y-m-d H:i:s", $gps_realtime));
									printf("===Vehicle No %s \r\n", $rowvehicle->vehicle_no);
									printf("===Speed %s, Engine %s \r\n", $gps->gps_speed, $engine);
									
									
									//get parameter gps
									//if($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5PULSE" || $rowvehicle->vehicle_type == "T5DOOR" || 	$rowvehicle->vehicle_type == "T5SILVER" || $rowvehicle->vehicle_type == "T8" || $rowvehicle->vehicle_type == "T8_2"){
									if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))){
										$lastposition = $this->getPosition_other($gps->gps_longitude, $gps->gps_latitude);
									}else{
										$lastposition = $this->getPosition($gps->gps_longitude, $gps->gps_ew, $gps->gps_latitude, $gps->gps_ns);
									}
									
									
									if($gps->gps_status == "A"){
										$gpsvalidstatus = "OK";
									}else{
										$gpsvalidstatus = "NOT OK";
									}
									
									$street_register = array("PORT BIB","PORT BIR","PORT TIA",
														//"ROM 01","ROM 01/02 ROAD","ROM 02","ROM 03","ROM 03/04 ROAD","ROM 04","ROM 05","ROM 06","ROM 06 ROAD",
														//"ROM O7","ROM 07/08 ROAD","ROM 08","ROM 09","ROM 10",
														"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
														"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST",
														"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
														
														"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
														"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT",
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
														"PORT BIB - Antrian","Port BIB - Antrian");
														
									$port_register = array("BIB CP 1","BIB CP 7","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 2","BIB CP 6",
														   "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
														   "PORT BIB","PORT BIR","PORT TIA"
														   
														   );
														   
									$rom_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom","Non BIB Angsana","Non BIB TBR/SDJ"
										  
														  );
									$rombib_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK"
														
										  
														  );
														  
									$wim_register = array("KM 13","KM 13.5"
														 );
														  
									$nonbib_register = array("SUNGAI DANAU","KINTAB","sungai danau","kintab","Non BIB Angsana","Non BIB TBR/SDJ"
																);
															
														  
									$bayah_muatan_register = array("Port BIB - Antrian","PORT BIB - Antrian","Port BIR - Antrian WB" );
									$bayah_kosongan_register = array("Port BIB - Kosongan 1","Port BIB - Kosongan 2","Port BIR - Kosongan 1",
																	 "Port BIR - Kosongan 2", "Simpang Bayah - Kosongan");
																	 
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
									
									
									if(isset($lastposition)){
										$ex_lastposition = explode(",",$lastposition->display_name);
										$street_name = $ex_lastposition[0]; 
										
										//$street_name = "Jalur TIA Utara"; //hardoce
										
										if (in_array($street_name, $street_register)){
											$hauling = "in";
										}else{
											
											$hauling = "out";
										}
										
										printf("===Location %s \r\n", $street_name);
										
										//redzone check here
										$redzone_status = 0;
										$warningzone_status = 0;
										$nonbib_status = 0; 
										$port_status = 0;
										$redzone_area = array("STU area","PCN area");
										$warningzone_area = array("Jalur TIA Utara","Jalur TIA Selatan","WR 01","WR 02","WR 03","WR 04","WR 05","WR 06","WR 07");
															  
															  
										$lastdata_json = json_decode($rowvehicle->vehicle_autocheck);
										//print_r($lastdata_json=>auto_last_rom_name);exit();
										
										$auto_last_rom_name = $lastdata_json->auto_last_rom_name;
										$auto_last_rom_time = $lastdata_json->auto_last_rom_time;
										$auto_last_port_name = $lastdata_json->auto_last_port_name;
										$auto_last_port_time = $lastdata_json->auto_last_port_time;
										
										/* $last_rom_name = "";
										$last_rom_time = "";
										$last_port_name = "";
										$last_port_time = ""; */
										
										if (in_array($street_name, $rom_register))
										{
											$now_rom_name = $street_name;
											$now_rom_time = date("Y-m-d H:i:s", $gps_realtime);
											printf("X==ROM CHECKING  \r\n");
											//sementara untuk input all data
											
											if($auto_last_rom_name == ""){
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("X==Data Awal ROM \r\n");
												
											}
											else
											{
												//update data tiap masuk ROM 
												$auto_last_rom_name = $street_name;
												$auto_last_rom_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("X==Masih di ROM \r\n");
												
											}
											
										}
										
										//Last Data ROM BIB
										//if (in_array($street_name, $rombib_register))
										if (in_array($street_name, $rom_register)) //all rom legal & ilegal
										{
										
											$rom_status = 1;
											$lastromdata = $this->rom_lastdata($rowvehicle,$street_name,date("Y-m-d H:i:s", $gps_realtime));
											
										}
										
										if (in_array($street_name, $port_register))
										{
											$now_port_name = $street_name;
											$now_port_time = date("Y-m-d H:i:s", $gps_realtime);
											printf("Y==PORT CHECKING \r\n");
											//sementara untuk input all data
											
											if($auto_last_port_name == ""){
												$auto_last_port_name = $street_name;
												$auto_last_port_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("Y==Data Awal PORT \r\n");
												//exit();
											}
											else
											{
												//jika port lama beda dengan PORT sekarang, maka update ke yg baru
												$auto_last_port_name = $street_name;
												$auto_last_port_time = date("Y-m-d H:i:s", $gps_realtime);
												printf("Y==Masih di PORT \r\n");
											}
											
											$port_status = 1;
											//dipisah cron 
											$lastportdata = $this->port_lastdata($rowvehicle,$street_name,date("Y-m-d H:i:s", $gps_realtime));
											
										}
										
										//rssult redzone
										if (in_array($street_name, $redzone_area)){
										//if (in_array($street_name, $redzone_area)){											
											$redzone_status = 1;
											$redzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===REDZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, date("Y-m-d H:i:s", $gps_realtime), $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$redzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													$redzone_type = "Z1";
													
													$lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicledata[0],$rowvehicle->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													}
													
													
												}
											}
											
											
										}
										
										//rssult warningzone
										if (in_array($street_name, $warningzone_area)){
											$warningzone_status = 1;
											$warningzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===WARNINGZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, date("Y-m-d H:i:s", $gps_realtime), $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$warningzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													$warningzone_type = "Z1";
													
													$lastport_from_rit = $this->getlastROM_fromRitaseDBlive($vehicledata[0],$rowvehicle->vehicle_dbname_live);
													
													if(count($lastport_from_rit)>0){
														
														$data_lastport = $lastport_from_rit->ritase_last_dest;
														$data_lastport_time = date("Y-m-d H:i:s", strtotime($lastport_from_rit->ritase_gpstime . "+7hours"));
														
														//cek selisih
														$lastport_time = strtotime($data_lastport_time);
														$delta_port_rit = $now_time - $lastport_time;
														$lastport_text = $data_lastport." ".$data_lastport_time;
														
														printf("X==CHECKING FROM RIT. param %s now: %s delta: %s \r\n", $lastport_text, date("Y-m-d H:i:s", $gps_realtime), $delta_port_rit/3600 );
														
													}else{
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													}
													
													
												}
											}
											
											
										}
										
										
										//result km 13 wim
										if (in_array($street_name, $wim_register))
										{
											//update field master data wim time (vehicle_wim_stime)
											$wim_time = $this->wim_updatetime($rowvehicle,$street_name,date("Y-m-d H:i:s", $gps_realtime));
											
										}
										
										
										$overspeed_status = 0;
										//send overspeed alert
										/* if (in_array($street_name, $street_onduty)){
											$overspeed_status = 0;
										}else{
											$overspeed_status = 0;
										} */
										
									}
									
									$lastlat = $gps->gps_latitude_real;
									$lastlong = $gps->gps_longitude_real;
									$speed = number_format($gps->gps_speed*1.852, 0, "", ".");
									$course = $gps->gps_course;
									$coordinate = $lastlat.",".$lastlong;
									
									if (in_array($street_name, $bayah_muatan_register))
									{
										$jalur = "muatan";
										$hauling = "in";
										//$alert_out_port = $this->outofport_alert($rowvehicle,$street_name,date("Y-m-d H:i:s", $gps_realtime),$coordinate,$speed); //dipisah cron
										
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
									
									
									$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
									printf("===Location %s, Jalur %s , Hauling %s \r\n", $street_name, $jalur, $hauling); //print_r("DISINI");exit();
									
									
									if($redzone_status == 1){
										
											$title_name = "REDZONE DETECTED!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n".
														"Last ROM: ".$lastrom_text." \n".
														"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											
											if($redzone_type == "Z1"){
												//$sendtelegram = $this->telegram_direct("-738419382",$message); //telegram RED ZONE CHECK
												$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											}else{
												//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
												$sendtelegram = $this->telegram_direct("-632059478",$message); //telegram BIB REDZONE
											}
											
											
											printf("===SENT TELEGRAM OK\r\n");	
											
										
									}
									
									if($warningzone_status == 1){
										
											$title_name = "WARNING ZONE DETECTED!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
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
										
									if($overspeed_status == 1){
										$rowgeofence = $this->getGeofence_location_live($gps->gps_longitude_real, $gps->gps_latitude_real, $vehicle_dblive);
										$telegram_group = $this->get_telegramgroup_overspeed($vehiclecompany);
										
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
											
											$gpsspeed_kph = $speed;
										}
										printf("===GEO CHECKING, Position : %s Geofence : %s Jalur: %s \r\n", $street_name, $geofence_name, $jalur);
										printf("===GEO CHECKING, Speed : %s Limit : %s \r\n", $gpsspeed_kph, $geofence_speed_limit);
										
										if($gpsspeed_kph <= $geofence_speed_limit){
											$skip_spd_sent = 1;
										}else{
											$skip_spd_sent = 0;
											
										}
										
										if($geofence_speed_limit == 0){
											$skip_spd_sent = 1;
										}else{
											$skip_spd_sent = 0;
										}
										
										if($skip_spd_sent == 0){
											$gpsspeed_kph = $gpsspeed_kph-3;
											$geofence_speed_limit = $geofence_speed_limit-3;
											$driver_name = "";
											$title_name = "OVERSPEED ALARM";
											$message = urlencode(
												"".$title_name." \n".
												"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
												"Vehicle No: ".$rowvehicle->vehicle_no." \n".
												"Driver: ".$driver_name." \n".
												"Position: ".$street_name." \n".
												"Coordinate: ".$url." \n".
												"Speed (kph): ".$gpsspeed_kph." \n".
												"Rambu (kph): ".$geofence_speed_limit." \n".
												"Geofence: ".$geofence_name." \n".
												"Jalur: ".$jalur." \n"
												
												);
												
											//printf("===Message : %s \r\n", $message);
											sleep(2);
											$sendtelegram = $this->telegram_direct($telegram_group,$message);
											printf("===SENT TELEGRAM OVERSPEED OK\r\n");	
										}else{
											
											printf("X==SKIP SENT OVERSPEED TELEGRAM\r\n");	
										}
									}
									
									
									//dipisah cron 
									/*if($hauling == 'out'){
										//check condition jika sudah masuk ROM > 30 menit keluar Geofence maka alert
										//$alert_out_hauling = $this->outofhauling_alert($rowvehicle,$street_name,date("Y-m-d H:i:s", $gps_realtime),$coordinate,$speed);
									}
									*/
										
									//Kondisi Engine Error
									if($gps->gps_speed > 4 && $engine == "OFF"){
										printf("===SEND EMAIL ENGINE ERROR TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."ENGINE ERROR";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: GPS ENGINE OFF dan Mobil Jalan, Segera cek Historynya!!

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT ENGINE ERROR \r\n"); 
									}
									
									//cek vehicle INFO
									if ($rowvehicle->vehicle_info)
									{
										$json = json_decode($rowvehicle->vehicle_info);
											
										if (isset($json->vehicle_ip) && isset($json->vehicle_port))
										{
											$vehicle_port = $json->vehicle_port;
										}
									}
											
									$vehicle_operator_ex = explode(" ", $rowvehicle->vehicle_operator);
									$vehicle_operator = strtolower($vehicle_operator_ex[0]);
								
									if($rowvehicle->vehicle_dbname_live == "0")
									{ 
										$dblive = "0";
									}else{
										$dblive = "1";
									}
									
									printf("===Vehicle Data %s %s %s \r\n", $rowvehicle->vehicle_type, $vehicle_port, $vehicle_operator, $dblive);
									
									
									//cek delay kurang dari 1 jam
									if ($delta >= 3600 && $delta <= 86400) //lebih jam kurang dari 24 jam //yellow condition
									{
										printf("=================GPS KUNING================ \r\n");
										printf("===Vehicle No %s Tidak Update \r\n", $rowvehicle->vehicle_no);
										$statuscode = "K";
										
										//cek log pengiriman hari ini
										$command_log = $this->get_command_log($rowvehicle->vehicle_device,$nowdate,$rowmodem->modem_limit_time); 
										
										//jika 0 belum ada kirim sms maka send command
										if($command_log == "0"){
											$command_restart = $this->get_command($rowvehicle->vehicle_type,$vehicle_port,$vehicle_operator,$dblive);
											
											//cek outbox
											printf("===Check Outbox SMS Alat %s \r\n", $rowvehicle->vehicle_modem);
											
											$this->dbsmsalat->select("count(*) as total");
											$qt = $this->dbsmsalat->get("outbox");
											$rt = $qt->row();
											$total = $rt->total;
											
											if(isset($total))
											{
												if ($total > 41 )
												{
													printf("===OUTBOX LEBIH BESAR DARI 40 SMS ! \r\n");
													printf("===SKIP INSERT ! \r\n");
													$skip = 1;
												}
											}
											$skip = 1;///DI UJI COBA(R12)
											if($skip == 0){
												
												printf("===Send Restart Command To : %s \r\n", $rowvehicle->vehicle_card_no);	
												
												if($command_restart->restart_step1_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+2 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_1);
													$datasms_1["UpdatedInDB"] = $nowdate_sms;
													$datasms_1["InsertIntoDB"] = $nowdate_sms;
													$datasms_1["SendingDateTime"] = $nowdate_sms;
													$datasms_1["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_1["TextDecoded"] = $command_restart->restart_step1_command;
													$datasms_1["SendingTimeOut"] = $nowdate_sms;
													//$datasms_1["RecipientID"] = $rowvehicle->vehicle_modem;
													
													$this->dbsmsalat->insert("outbox",$datasms_1);
													printf("===INSERT STEP 1=== %s \r\n",$command_restart->restart_step1_command );
												}
												if($command_restart->restart_step2_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+5 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_2);
													$datasms_2["UpdatedInDB"] = $nowdate_sms;
													$datasms_2["InsertIntoDB"] = $nowdate_sms;
													$datasms_2["SendingDateTime"] = $nowdate_sms;
													$datasms_2["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_2["TextDecoded"] = $command_restart->restart_step2_command;
													$datasms_2["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_2);
													printf("===INSERT STEP 2=== %s \r\n",$command_restart->restart_step2_command );
												}
												if($command_restart->restart_step3_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+8 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_3);
													$datasms_3["UpdatedInDB"] = $nowdate_sms;
													$datasms_3["InsertIntoDB"] = $nowdate_sms;
													$datasms_3["SendingDateTime"] = $nowdate_sms;
													$datasms_3["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_3["TextDecoded"] = $command_restart->restart_step3_command;
													$datasms_3["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_3);
													printf("===INSERT STEP 3=== %s \r\n",$command_restart->restart_step3_command );
												}
												if($command_restart->restart_step4_command != ""){
													$nowdate_sms = 	date("Y-m-d H:i:s");
													$nowdate_sms = date('Y-m-d H:i:s',strtotime('+11 minutes',strtotime($nowdate_sms)));
													
													unset($datasms_4);
													$datasms_4["UpdatedInDB"] = $nowdate_sms;
													$datasms_4["InsertIntoDB"] = $nowdate_sms;
													$datasms_4["SendingDateTime"] = $nowdate_sms;
													$datasms_4["DestinationNumber"] = $rowvehicle->vehicle_card_no;
													$datasms_4["TextDecoded"] = $command_restart->restart_step4_command;
													$datasms_4["SendingTimeOut"] = $nowdate_sms;
													
													$this->dbsmsalat->insert("outbox",$datasms_4);
													printf("===INSERT STEP 4=== %s \r\n",$command_restart->restart_step4_command );
												}
												
													//insert log
													unset($datasms_log);
													$datasms_log["log_user"] = $rowvehicle->vehicle_user_id;
													$datasms_log["log_vehicle"] = $rowvehicle->vehicle_no;
													$datasms_log["log_device"] = $rowvehicle->vehicle_device;
													$datasms_log["log_type"] = $rowvehicle->vehicle_type;
													$datasms_log["log_simcard"] = $rowvehicle->vehicle_card_no;
													$datasms_log["log_command"] = $command_restart->restart_step1_command."|".$command_restart->restart_step2_command."|".								$command_restart->restart_step3_command."|".$command_restart->restart_step4_command;
													$datasms_log["log_date"] = date("Y-m-d", strtotime($nowdate));
													$datasms_log["log_created"] = $nowdate;
													
													$this->db->insert("sms_restart_log",$datasms_log);
													printf("===INSERT LOG OK=== \r\n");		
													
												
											}else{
												printf("===SKIP INSERT OUTBOX PENUH=== \r\n");
											}

										}else{
												printf("===SKIP INSERT SUDAH ADA DI LOG HARI INI=== \r\n");
												//printf("===SEND EMAIL TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS KUNING";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem tidak bisa diupdate

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT YELLOW \r\n"); 
													
										}
										
									}
									else if($delta >= 43201) //lebih dari 1 hari //red condition 
									{
										$statuscode = "M";
										printf("======================RED CONDITION======================== \r\n");
										
										unset($datavehicle);
										$datavehicle["vehicle_isred"] = 1;
										$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
										$this->db->update("vehicle", $datavehicle);
										printf("===UPDATED STATUS IS RED YES=== %s \r\n", $rowvehicle->vehicle_no);
										
										
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS MERAH";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem GPS MERAH

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT RED \r\n"); 
									}
									else //gps update condition
									{
										$statuscode = "P";
										
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
										
										if($nonbib_status == 1)
										{
										
											$title_name = "NON BIB ACTIVITY!!";
											$message = urlencode(
														"".$title_name." \n".
														"Time: ".date("Y-m-d H:i:s", $gps_realtime)." \n".
														"Vehicle No: ".$rowvehicle->vehicle_no." \n".
														"Position: ".$street_name." \n".
														"Coordinate: ".$url." \n".
														"Speed: ".$speed." kph"." \n"
														//"Last ROM: ".$lastrom_text." \n".
														//"Last PORT: ".$lastport_text." \n"
														
														);
											sleep(2);		
											//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											$sendtelegram = $this->telegram_direct($telegram_geofence,$message);
											
											printf("===SENT TELEGRAM OK\r\n");	
										}
										
										if($port_status == 1)
										{
											//get data WIM by NO lambung
											$lastdatawim = $this->getLastDataWIM($rowvehicle,$gps_realtime);
											
										}
										
										
										if($gps->gps_status == "V"){
											printf("===Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
											//printf("===SEND EMAIL TO MONITORING=== \r\n");
													$username_data = $this->get_username($rowvehicle->vehicle_user_id);
													if(isset($username_data)){
														$user_name = $username_data->user_name;
													}else{
														$user_name = "-";
													}
													unset($mail);
													$mail['subject'] =  "[".$rowvehicle->vehicle_no."]"." ".$user_name." "."GPS NOT OK";
													$mail['message'] = 
"
	Dear Monitoring Team,"."

	SEGERA DI CEK Kendaraan Berikut :

	Nomor Polisi	: ".$rowvehicle->vehicle_no."
	Device ID		: ".$rowvehicle->vehicle_device."
	User			: ".$user_name."
	Nomor Simcard	: ".$rowvehicle->vehicle_card_no."
	Status 			: Pengecekan otomatis dari sistem GPS NOT OK

	Terima Kasih
													
	";
													$mail['dest'] = $this->config->item("autocheck_dest");
													$mail['bcc'] = $this->config->item("autocheck_cc");
													$mail['sender'] = $this->config->item("autocheck_sender");
													//lacakmobilmail($mail);
													
													//printf("===EMAIL SENT NOT OK \r\n"); 
											
										}else{
											printf("=================GPS UPDATE================ \r\n");
											//update log command flag == 1
											$command_reset = $this->reset_command_log($rowvehicle->vehicle_device,$nowdate);
											if($command_reset == true){
												printf("===UPDATE CONFIG \r\n");
											}
											
											unset($datavehicle);
											$datavehicle["vehicle_isred"] = 0;
											$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
											$this->db->update("vehicle", $datavehicle);
											printf("===UPDATED STATUS DEFAULT=== %s \r\n", $rowvehicle->vehicle_no);
											
										}
									}
									
									//update master vehicle autocheck
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = $statuscode;
										$datacheck["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$datacheck["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
										$datacheck["auto_change_position"] = $lastposition->display_name;
										$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
										
										$this->db->insert("vehicle_autocheck",$datacheck);
										printf("===INSERT AUTOCHECK=== \r\n");	

										//json										
										$feature["auto_status"] = $statuscode;
										$feature["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = $statuscode;
										$datacheck["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
										$feature["auto_last_update"] = date("Y-m-d H:i:s", $gps_realtime);
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
											$datacheck["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
											$datacheck["auto_change_position"] = $lastposition->display_name;
											$datacheck["auto_change_coordinate"] = $lastlat.",".$lastlong;
											
											//json
											$feature["auto_change_engine_status"] = $engine;
											$feature["auto_change_engine_datetime"] = date("Y-m-d H:i:s", $gps_realtime);
											$feature["auto_change_position"] = $lastposition->display_name;
											$feature["auto_change_coordinate"] = $lastlat.",".$lastlong;
										}
										
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
										$this->db->update("vehicle_autocheck",$datacheck);
										printf("===UPDATE AUTOCHECK=== \r\n");	
										
										
										
											
									}
										
								
									
								
								
								
								
								
								
								
								}
								else
								{
									printf("===NO DATA=== \r\n");	
									unset($datavehicle);
									$datavehicle["vehicle_isred"] = 1;
									$this->db->where("vehicle_device", $rowvehicle->vehicle_device);
									$this->db->update("vehicle", $datavehicle);
									printf("===UPDATED STATUS IS RED (NO DATA) YES=== %s \r\n", $rowvehicle->vehicle_no);
										
									//update master vehicle (khusus vehicle GO TO History)
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									//$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
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
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
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
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
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
						}
						else
						{
							
									//update master vehicle (khusus vehicle HIDE)
									$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
									$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);
									$this->db->limit(1);
									$qcheck = $this->db->get("vehicle_autocheck");
									//$rowcheck = $qcheck->row(); 			
									if ($qcheck->num_rows() == 0)
									{
										//insert
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = "";
										$datacheck["auto_last_update"] = "";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_last_hauling"] = "";
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										$datacheck["auto_flag"] = 1;
										
														
										$this->db->insert("vehicle_autocheck",$datacheck);
										printf("===INSERT AUTOCHECK=== \r\n");

										//json										
										$feature["auto_status"] = "";
										$feature["auto_last_update"] = "";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										$feature["auto_flag"] = 1;
										$feature["vehicle_gotohistory"] = 1;
										$vehicle_gotohistory = 1;	
														
									}else{
										//update
										unset($datacheck);
										$datacheck["auto_user_id"] = $rowvehicle->vehicle_user_id;
										$datacheck["auto_vehicle_id"] = $rowvehicle->vehicle_id;
										$datacheck["auto_vehicle_name"] = $rowvehicle->vehicle_name;
										$datacheck["auto_vehicle_no"] = $rowvehicle->vehicle_no;
										$datacheck["auto_vehicle_device"] = $rowvehicle->vehicle_device;
										$datacheck["auto_vehicle_type"] = $rowvehicle->vehicle_type;
										$datacheck["auto_vehicle_company"] = $rowvehicle->vehicle_company;
										$datacheck["auto_vehicle_subcompany"] = $rowvehicle->vehicle_subcompany;
										$datacheck["auto_vehicle_group"] = $rowvehicle->vehicle_group;
										$datacheck["auto_vehicle_subgroup"] = $rowvehicle->vehicle_subgroup;
										$datacheck["auto_vehicle_active_date2"] = $rowvehicle->vehicle_active_date2;
										$datacheck["auto_simcard"] = $rowvehicle->vehicle_card_no;
										$datacheck["auto_status"] = "";
										$datacheck["auto_last_update"] = "";
										$datacheck["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$datacheck["auto_last_position"] = "";
										$datacheck["auto_last_lat"] = "";
										$datacheck["auto_last_long"] = "";
										$datacheck["auto_last_engine"] = "";
										$datacheck["auto_last_gpsstatus"] = "";
										$datacheck["auto_last_speed"] = 0;
										$datacheck["auto_last_course"] = 0;
										$datacheck["auto_last_road"] = "";
										$datacheck["auto_last_hauling"] = "";
										$datacheck["auto_last_rom_name"] = "";
										$datacheck["auto_last_rom_time"] = "";
										$datacheck["auto_last_port_name"] = "";
										$datacheck["auto_last_port_time"] = "";
										$datacheck["auto_flag"] = 1;
										
										$this->db->where("auto_user_id", $rowvehicle->vehicle_user_id);	
										$this->db->where("auto_vehicle_device", $rowvehicle->vehicle_device);	
										$this->db->update("vehicle_autocheck",$datacheck);
										printf("===UPDATE AUTOCHECK=== \r\n");	
										
										//json
										$feature["auto_status"] = "";
										$feature["auto_last_update"] = "";
										$feature["auto_last_check"] = date("Y-m-d H:i:s", strtotime($nowdate));
										$feature["auto_last_position"] = "";
										$feature["auto_last_lat"] = "";
										$feature["auto_last_long"] = "";
										$feature["auto_last_engine"] = "";
										$feature["auto_last_gpsstatus"] = "";
										$feature["auto_last_speed"] = 0;
										$feature["auto_last_course"] = 0;
										$feature["auto_last_road"] = "";
										$feature["auto_last_hauling"] = "";
										
										$feature["auto_last_rom_name"] = "";
										$feature["auto_last_rom_time"] = "";
										$feature["auto_last_port_name"] = "";
										$feature["auto_last_port_time"] = "";
										
										$feature["auto_flag"] = 1;
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
						
						$this->db->where("vehicle_id", $rows[$i]->vehicle_id);	
						$this->db->limit(1);	
						$this->db->update("vehicle",$datajson);
						printf("===UPDATE JSON MASTER VEHICLE=== \r\n");	
					}
						
						
				$j++;
				
			}
			
		}

		
		
		$this->db->close();
		$this->db->cache_delete_all();
		if(isset($this->dbsms)){
			$this->dbsms->close();
			$this->dbsms->cache_delete_all();
		}
		if(isset($this->dbsms)){
			$this->dbsmsalat->close();
			$this->dbsmsalat->cache_delete_all();
		}
		
		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH Check Last Info %s to %s \r\n", $nowdate, $enddate);
				$title_name = "AUTOCHECK ".$groupname;
				$statusname = "FINISH";
				$message = urlencode(
						"".$title_name." \n".
						"Start: ".$nowdate." \n".
						"End: ".$enddate." \n".
						"Total Unit: ".$totalvehicle." \n".
						"Status: ".$statusname." \n"
				);
				sleep(2);		
				$sendtelegram = $this->telegram_direct("-742300146",$message); //telegram FMS AUTOCHECK
				printf("===SENT TELEGRAM OK\r\n");	
				printf("============================== \r\n");

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
	
	//2022-06-07
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
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom","Non BIB Angsana","Non BIB TBR/SDJ"
										  
														  );
																			
								$pool_register = array( "POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
														"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT"
														);
		
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
	
	function autocheck_hour_data($userid="", $order="asc")
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
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom","Non BIB Angsana","Non BIB TBR/SDJ"
										  
														  );
																			
								$pool_register = array( "POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
														"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT"
														);
		
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
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom","Non BIB Angsana","Non BIB TBR/SDJ"
										  
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
		$url = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
        
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
	
	//no insert nonbib (2022-06-06)
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
		
		for($x=0;$x<$totalmodem;$x++)
		{
			$modem = $rowsmodem[$x]->modem_configdb;
			$no_urut_modem = $x+1;
			$nowdate = date("Y-m-d");
			$running = 0;
			printf("===STARTING AUTOCHECK Now %s startdate %s \r\n", $nowtime, $nowdate);
			printf("===Prepare Check Last Info Modem SMS : %s (%d/%d)\n", $modem, $no_urut_modem, $totalmodem); 
			
			$this->db = $this->load->database("default",true); 
			$this->db->order_by("vehicle_id",$order);
			$this->db->where("vehicle_user_id", $userid);
			$this->db->where("vehicle_modem", $modem);
			$this->db->where("vehicle_status <>", 3);
			//$this->db->where("vehicle_no", "GEC 943");
			
			$this->db->from("vehicle");
			$q = $this->db->get();
			$rowvehicle = $q->result();
			$total_rows = count($rowvehicle);
			if(count($rowvehicle)>0){
			
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
							printf("===Now Time: %s  \r\n", $nowtime);
							printf("===GPS Time: %s  \r\n", $lastposition_time);
							printf("===Vehicle No %s \r\n", $vehicle_no);
							printf("===Speed %s \r\n", $speed);
							
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
							$engine = $datajson->auto_last_engine;
							printf("===Engine %s \r\n", $engine);
							
										$street_register = array("PORT BIB","PORT BIR","PORT TIA",
															//"ROM 01","ROM 01/02 ROAD","ROM 02","ROM 03","ROM 03/04 ROAD","ROM 04","ROM 05","ROM 06","ROM 06 ROAD",
															//"ROM O7","ROM 07/08 ROAD","ROM 08","ROM 09","ROM 10",
															"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
															"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
															"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
															
															"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
															"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT",
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
															"PORT BIB - Antrian","Port BIB - Antrian");
															
										$port_register = array("BIB CP 1","BIB CP 7","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 2","BIB CP 6",
															   "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
															   "PORT BIB","PORT BIR","PORT TIA"
															   
															   );
															   
										$rom_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom","Non BIB Angsana","Non BIB TBR/SDJ"
										  
														  );
										$rombib_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK"
															
											  
															  );
															  
										$wim_register = array("KM 13","KM 13.5"
															 );
															  
										$nonbib_register = array("SUNGAI DANAU","KINTAB","sungai danau","kintab","Non BIB Angsana","Non BIB TBR/SDJ"
																);
																
															  
										$bayah_muatan_register = array("Port BIB - Antrian","PORT BIB - Antrian","Port BIR - Antrian WB" );
										$bayah_kosongan_register = array("Port BIB - Kosongan 1","Port BIB - Kosongan 2","Port BIR - Kosongan 1",
																		 "Port BIR - Kosongan 2", "Simpang Bayah - Kosongan");
																		 
										//filter in location array HAULING, ROM, PORT 
										$street_onduty = array( "PORT BIB","PORT BIR","PORT TIA",
																//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
																"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
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
										
							
							
							$lastposition_time_sec = strtotime($lastposition_time);
							$coordinate = $gps->gps_latitude_real.",".$gps->gps_longitude_real;
							$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
								
							$location = "-";
							
							//condition here
							if(isset($lastposition)){
								$ex_lastposition = explode(",",$lastposition->display_name);
								$street_name = $ex_lastposition[0];
								$location = $street_name;
								printf("===Location %s \r\n", $street_name);
								
								// in HAULING
								if (in_array($street_name, $street_register)){
									$hauling = "in";
								}else{
												
									$hauling = "out";
								}
								
									//IN ROM
									if (in_array($street_name, $rom_register))
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
											
										}
										
										//IN PORT
										if (in_array($street_name, $port_register))
										{
											$now_port_name = $street_name;
											$now_port_time = $gps_realtime;
											printf("Y==PORT CHECKING \r\n");
											//sementara untuk input all data
											
											if($auto_last_port_name == ""){
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
											}
											
											$port_status = 1;
											$lastportdata = $this->port_lastdata($rowvehicle[$i],$street_name,$gps_realtime);
											
										}
								
								//in ROM
								if (in_array($street_name, $rombib_register))
								{
											
									$rom_status = 1;
									$lastromdata = $this->rom_lastdata($rowvehicle[$i],$street_name,$gps_realtime); //sudah wita
												
								}
								
										$redzone_status = 0;
										$warningzone_status = 0;
										$nonbib_status = 0; 
										$port_status = 0;
										$redzone_area = array("STU area","PCN area");
										$warningzone_area = array("Jalur TIA Utara","Jalur TIA Selatan","WR 01","WR 02","WR 03","WR 04","WR 05","WR 06","WR 07");
															  
										$lastdata_json = json_decode($rowvehicle[$i]->vehicle_autocheck);
										//print_r($lastdata_json=>auto_last_rom_name);exit();
										
										/* $auto_last_rom_name = $lastdata_json->auto_last_rom_name;
										$auto_last_rom_time = $lastdata_json->auto_last_rom_time;
										$auto_last_port_name = $lastdata_json->auto_last_port_name;
										$auto_last_port_time = $lastdata_json->auto_last_port_time; */
										
										// ganti
										$auto_last_rom_name = $lastromname;
										$auto_last_rom_time = $lastromtime;
										$auto_last_port_name = $lastportname;
										$auto_last_port_time = $lastporttime;
										
										
										/* $last_rom_name = "";
										$last_rom_time = "";
										$last_port_name = "";
										$last_port_time = ""; */
										
										
										
										//red zone
										if (in_array($street_name, $redzone_area)){
										//if (in_array($street_name, $redzone_area)){											
											$redzone_status = 1;
											$redzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===REDZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text,  $gps_realtime, $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$redzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													$redzone_type = "Z1";
													
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
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													}
													
													
												}
											}
											
											
										}
								
								
								
										//warning zone
										if (in_array($street_name, $warningzone_area)){
											$warningzone_status = 1;
											$warningzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===WARNINGZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, $gps_realtime, $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$warningzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
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
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
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
								
								
										if($redzone_status == 1){
										
											$title_name = "REDZONE DETECTED!!";
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
											
											if($redzone_type == "Z1"){
												//$sendtelegram = $this->telegram_direct("-738419382",$message); //telegram RED ZONE CHECK
												$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
											}else{
												//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
												$sendtelegram = $this->telegram_direct("-632059478",$message); //telegram BIB REDZONE
											}
											
											
											printf("===SENT TELEGRAM OK\r\n");	
											
										
									}
									
									if($warningzone_status == 1){
										
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
							if ($delta >= 3600 && $delta <= 86400)
							{
								printf("===GPS DELAY \r\n");
								$statuscode = "K";
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
							else if($delta >= 43201) //lebih dari 1 hari //red condition 
							{
								printf("===GPS OFFLINE \r\n");
								$statuscode = "M";
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
										
										if($nonbib_status == 1)
										{
										
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
											
											printf("===SENT TELEGRAM OK\r\n");	
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
		//send telegram 
		$cron_name = "AUTOCHECK NEW ".$groupname;
		$statusname = "FINISH";
		$message =  urlencode(
			"".$cron_name." \n".
			"Start: ".$nowtime." \n".
			"Finish: ".$finishtime." \n".
			"Total Unit: ".$total_rows." \n".
			"Status: ".$statusname." \n"
			);
											
		$sendtelegram = $this->telegram_direct("-742300146",$message); //telegram FMS AUTOCHECK
		printf("===SENT TELEGRAM OK\r\n");
	
		printf("=====FINISH %s %s=========== \r\n", $nowtime, $finishtime);
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
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
		
		for($x=0;$x<$totalmodem;$x++)
		{
			$modem = $rowsmodem[$x]->modem_configdb;
			$no_urut_modem = $x+1;
			$nowdate = date("Y-m-d");
			$running = 0;
			printf("===STARTING AUTOCHECK Now %s startdate %s \r\n", $nowtime, $nowdate);
			printf("===Prepare Check Last Info Modem SMS : %s (%d/%d)\n", $modem, $no_urut_modem, $totalmodem); 
			
			$this->db = $this->load->database("default",true); 
			$this->db->order_by("vehicle_id",$order);
			$this->db->where("vehicle_user_id", $userid);
			$this->db->where("vehicle_modem", $modem);
			$this->db->where("vehicle_status <>", 3);
			//$this->db->where("vehicle_no", "EST 1480");
			
			$this->db->from("vehicle");
			$q = $this->db->get();
			$rowvehicle = $q->result();
			$total_rows = count($rowvehicle);
			if(count($rowvehicle)>0){
			
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
							$engine = $datajson->auto_last_engine;
							printf("===Engine %s \r\n", $engine);
							
										$street_register = array("PORT BIB","PORT BIR","PORT TIA",
															//"ROM 01","ROM 01/02 ROAD","ROM 02","ROM 03","ROM 03/04 ROAD","ROM 04","ROM 05","ROM 06","ROM 06 ROAD",
															//"ROM O7","ROM 07/08 ROAD","ROM 08","ROM 09","ROM 10",
															"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
															"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
															"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
															
															"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL GECL 2","POOL MKS","POOL RAM","POOL RBT BRD","POOL RBT","POOL STLI",
															"WS BEP","WS BBB","WS EST","WS EST 32","WS GECL","WS GECL 2","WS GECL 3","WS KMB INDUK","WS KMB","WS MKS","WS MMS","WS RBT",
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
															"PORT BIB - Antrian","Port BIB - Antrian");
															
										$port_register = array("BIB CP 1","BIB CP 7","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 2","BIB CP 6",
															   "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
															   "PORT BIB","PORT BIR","PORT TIA"
															   
															   );
															   
										$rom_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
														  "Non BIB KM 11","Non BIB KM 9","Non BIB Simp Telkom","Non BIB Angsana","Non BIB TBR/SDJ"
										  
														  );
										$rombib_register = array("ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK"
															
											  
															  );
															  
										$wim_register = array("KM 13","KM 13.5"
															 );
															  
										$nonbib_register = array("SUNGAI DANAU","KINTAB","sungai danau","kintab","Non BIB Angsana","Non BIB TBR/SDJ"
																);
																
															  
										$bayah_muatan_register = array("Port BIB - Antrian","PORT BIB - Antrian","Port BIR - Antrian WB" );
										$bayah_kosongan_register = array("Port BIB - Kosongan 1","Port BIB - Kosongan 2","Port BIR - Kosongan 1",
																		 "Port BIR - Kosongan 2", "Simpang Bayah - Kosongan");
																		 
										//filter in location array HAULING, ROM, PORT 
										$street_onduty = array( "PORT BIB","PORT BIR","PORT TIA",
																//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
																"ROM A1","ROM A2","ROM B1","ROM B2","ROM B3","ROM EST","PIT CK",
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
										
							
							
							$lastposition_time_sec = strtotime($lastposition_time);
							$coordinate = $gps->gps_latitude_real.",".$gps->gps_longitude_real;
							$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
								
							$location = "-";
							
							//condition here
							if(isset($lastposition)){
								$ex_lastposition = explode(",",$lastposition->display_name);
								$street_name = $ex_lastposition[0];
								//$street_name = "PCN area";
								
								$location = $street_name;
								printf("===Location %s \r\n", $street_name);
								
								// in HAULING
								if (in_array($street_name, $street_register)){
									$hauling = "in";
								}else{
												
									$hauling = "out";
								}
								
									//IN ROM
									if (in_array($street_name, $rom_register))
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
											
										}
										
										//IN PORT
										if (in_array($street_name, $port_register))
										{
											$now_port_name = $street_name;
											$now_port_time = $gps_realtime;
											printf("Y==PORT CHECKING \r\n");
											//sementara untuk input all data
											
											if($auto_last_port_name == ""){
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
											}
											
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
								
										$redzone_status = 0;
										$warningzone_status = 0;
										$nonbib_status = 0; 
										$port_status = 0;
										$redzone_area = array("STU area","PCN area");
										$warningzone_area = array("Jalur TIA Utara","Jalur TIA Selatan","WR 01","WR 02","WR 03","WR 04","WR 05","WR 06","WR 07");
															  
										$lastdata_json = json_decode($rowvehicle[$i]->vehicle_autocheck);
										//print_r($lastdata_json=>auto_last_rom_name);exit();
										
										/* $auto_last_rom_name = $lastdata_json->auto_last_rom_name;
										$auto_last_rom_time = $lastdata_json->auto_last_rom_time;
										$auto_last_port_name = $lastdata_json->auto_last_port_name;
										$auto_last_port_time = $lastdata_json->auto_last_port_time; */
										
										// ganti
										$auto_last_rom_name = $lastromname;
										$auto_last_rom_time = $lastromtime;
										$auto_last_port_name = $lastportname;
										$auto_last_port_time = $lastporttime;
										
										
										/* $last_rom_name = "";
										$last_rom_time = "";
										$last_port_name = "";
										$last_port_time = ""; */
										
										printf("===LAST ROM %s %s \r\n", $auto_last_rom_name, $auto_last_rom_time);
										
										//red zone
										if (in_array($street_name, $redzone_area)){
										//if (in_array($street_name, $redzone_area)){											
											$redzone_status = 1;
											$redzone_type = "X";
											$limit_last_port = 3600*3; //3jam
											$limit_last_rom = 3600*2; //2jam
											
											printf("X==LIMIT TIME REDZONE: %s \r\n", $limit_last_rom);
											printf("===REDZONE DETECTED \r\n");
											
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Delta Last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text,  $gps_realtime, $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir";
													$redzone_type = "Z1";
													
													
												}
												//print_r($lastrom_text);exit();
											}
											
											//printf("X==last ROM %s \r\n", $lastrom_text);
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$redzone_type = "Z1";
											}else{
												$redzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													$redzone_type = "Z1";
													
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
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
													}
													
													
												}
											}
											
											
										}
								
								
								
										//warning zone
										if (in_array($street_name, $warningzone_area)){
											$warningzone_status = 1;
											$warningzone_type = "X";
											$limit_last_port = 120*60; //3jam
											$limit_last_rom = 180*60; //2jam
											printf("===WARNINGZONE DETECTED \r\n");
											
											$lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time));
											/* $lastrom_text = $auto_last_rom_name." ".date("Y-m-d H:i:s", strtotime($auto_last_rom_time));
											$lastport_text = $auto_last_port_name." ".date("Y-m-d H:i:s", strtotime($auto_last_port_time)); */
											
											if($auto_last_rom_name == ""){
												$lastrom_text = "No Data ROM";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "A";
												$lastrom_time = strtotime($auto_last_rom_time);
												$now_time = $gps_realtime;
												$delta_rom = $now_time - $lastrom_time;
												printf("X==Selisih last ROM %s \r\n", $delta_rom);
												
												if($delta_rom > $limit_last_rom){
													printf("X==Tidak dari ROM sejak 3 jam terakhir. param %s now: %s delta: %s \r\n", $lastrom_text, $gps_realtime, $delta_rom/3600 );
													//$lastrom_text = "Tidak dari ROM sejak 2 jam terakhir. param: ".$lastrom_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_rom/3600,0);
													$lastrom_text = "Tidak dari ROM sejak 3 jam terakhir";
													$warningzone_type = "Z1";
													
												}
												//print_r($lastrom_text);exit();
											}
											
											if($auto_last_port_name == ""){
												$lastport_text = "No Data PORT";
												$warningzone_type = "Z1";
											}else{
												$warningzone_type = "B";
												$lastport_time = strtotime($auto_last_port_time);
												$now_time = $gps_realtime;
												$delta_port = $now_time - $lastport_time;
												printf("Y==Selisih last PORT %s \r\n", $delta_port);
												//exit();
												if($delta_port > $limit_last_port){
													printf("X==Tidak dari PORT sejak 2 jam terakhir. param %s now: %s delta: %s \r\n", $lastport_text, $gps_realtime, $delta_port/3600 );
													//$lastport_text = "Tidak dari PORT sejak 2 jam terakhir. param: ".$lastport_text.", NOW: ".date("Y-m-d H:i:s", $now_time).", DELTA: ". round($delta_port/3600,0);
													$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
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
														$lastport_text = "Tidak dari PORT sejak 2 jam terakhir";
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
								
								
										if($redzone_status == 1){
										
											$title_name = "REDZONE DETECTED!!";
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
												//$sendtelegram = $this->telegram_direct("-738419382",$message); //telegram RED ZONE CHECK
												//$sendtelegram = $this->telegram_direct("-495868829",$message); //telegram testing anything
												printf("X==Z1 SKIP TELEGRAM ");
												
											}else{
												$sendtelegram = $this->telegram_direct("-657527213",$message); //telegram FMS TESTING
												//$sendtelegram = $this->telegram_direct("-632059478",$message); //telegram BIB REDZONE
												printf("===SENT TELEGRAM OK\r\n");	
											}
											
											
											
											
										
									}
									
									if($warningzone_status == 1){
										
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
							if ($delta >= 3600 && $delta <= 86400)
							{
								printf("===GPS DELAY \r\n");
								$statuscode = "K";
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
							else if($delta >= 43201) //lebih dari 1 hari //red condition 
							{
								printf("===GPS OFFLINE \r\n");
								$statuscode = "M";
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
										
										if($nonbib_status == 1)
										{
										
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
		
		
		//send telegram 
		$cron_name = "AUTOCHECK NEW ".$groupname;
		$statusname = "FINISH";
		$message =  urlencode(
			"".$cron_name." \n".
			"Start: ".$nowtime." \n".
			"Finish: ".$finishtime." \n".
			"Total Unit: ".$total_rows." \n".
			"Status: ".$statusname." \n".
			"Latency: ".$duration_sec." s"." \n"
			);
											
		$sendtelegram = $this->telegram_direct("-742300146",$message); //telegram FMS AUTOCHECK
		printf("===SENT TELEGRAM OK\r\n");
	
		printf("=====FINISH %s %s lat: %s s =========== \r\n", $nowtime, $finishtime, $duration_sec);
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	
	function outofport_check($userid="",$orderby=""){
		
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
	
	function telegram_direct($groupid,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        //$url = "http://lacak-mobil.com/telegram/telegram_directpost";
		//$url = "http://admintib.buddiyanto.my.id/telegram/telegram_directpost";
		//$url = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
		$url = "http://admin.abditrack.com/telegram/telegram_directpost";
        
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
	
	function getTelegramID_nonbib($id) 
	{	
		$status = false;
		$this->db->order_by("company_id","asc");
		$this->db->select("company_id,company_telegram_geofence","company_telegram_speed");
		$this->db->where("company_id",$id);
		$q = $this->db->get("company");
		$data = $q->row();
		
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
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
