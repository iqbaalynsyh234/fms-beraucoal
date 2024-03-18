<?php
include "base.php";

class Autosetting extends Base {
	var $otherdb;
	function Autosetting()
	{
		parent::Base();	
		$this->load->model("gpsmodel_autosetting");
		$this->load->model("vehiclemodel");
		$this->load->model("smsmodel");
		$this->load->model("gpsmodel");
	}
	
	function ceklastinfo()
	{
		printf("Run Cron Check Last Info \r\n");
		printf("======================================\r\n");
		$lasttime = isset($_POST['lasttime']) ? $_POST['lasttime'] : 0;	
		
		/* $this->db->where("config_name","cron_autorestart");
		$qc = $this->db->get("config");
		$rc = $qc->row();
		
		if(isset($rc))
		{
			if($rc->config_value == 1)
			{
				printf("CRON MASIH AKTIF ! \r\n");
				return;
			}
		} */
		$this->dbsmsalat = $this->load->database("smsalat",true);
		$this->dbsmsalat->select("count(*) as total");
		$qt = $this->dbsmsalat->get("outbox");
		$rt = $qt->row();
		$total = $rt->total;
		//print_r($total);exit();
		
		if(isset($total))
		{
			if ($total > 11 )
			{
				printf("OUTBOX LEBIH BESAR DARI 10 SMS ! \r\n");
				printf("CRON BERHENTI ! \r\n");
				return;		
			}
		}
		
		/* unset($dataconfig);
		$dataconfig["config_value"] = 1;
		$this->db->where("config_name","cron_autorestart");
		$this->db->update("config",$dataconfig); */
		
		$this->db->order_by("vehicle_id","asc");
		$this->db->where("vehicle_user_id <>", 752); //user csa sementara disable
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_type", "T5");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			printf("No Vehicles \r\n");
			return;
		}
		
		$rows = $q->result();
		$totalvehicle = count($rows);
		$this->db->close();
		$this->dbsms = $this->load->database("smscolo",true);
		$j = 1;
		for($i=0;$i<$q->num_rows();$i++)
		{
			printf("Process Cron For %s (%d/%d)\n", $rows[$i]->vehicle_device, $j, $totalvehicle);
			printf("execute %s\r\n", $rows[$i]->vehicle_no);
			
			
			$device = $rows[$i]->vehicle_device;
			$arr = explode("@", $device);
			$devices[0] = (count($arr) > 0) ? $arr[0] : "";
			$devices[1] = (count($arr) > 1) ? $arr[1] : "";
			
			printf("Check Data GPS %s\r\n", $rows[$i]->vehicle_device);
			$gps = $this->gpsmodel_autosetting->GetLastInfo_autosetting($devices[0], $devices[1], true, false, $lasttime, $rows[$i]->vehicle_type);
			
			if (! $gps)
			{
				printf("Data In History \r\n");	
			}
			
			$this->dbsms = $this->load->database("smscolo",true);
			
			if(isset($gps->css_delay_index) && $gps->css_delay_index == 0)
			{
				printf("Vehicle No %s Tidak Update \r\n", $rows[$i]->vehicle_no);
				printf("Send Message Process To : %s \r\n", $rows[$i]->vehicle_card_no);	
				
				unset($datasms);
				$datasms["SenderNumber"] = $rows[$i]->vehicle_card_no;
				
				if($rows[$i]->vehicle_type == "T5")
				{
					$datasms["TextDecoded"] = "Protocol114477 UDP";
				}
				
				$datasms["ReceivingDateTime"] = date("Y-m-d H:i:s");
				$datasms["RecipientID"] = "alat";
				$this->dbsms->insert("inbox",$datasms);
				printf("INSERT OK \r\n");
			}
			else
			{
				printf("Vehicle No %s Update \r\n", $rows[$i]->vehicle_no);
			}
			printf("======================================\r\n");
			++$j;
		}
		
		/* unset($dataconfig);
		$dataconfig["config_value"] = 0;
		$this->db = $this->load->database("default",true);
		$this->db->where("config_name","cron_autorestart");
		$this->db->update("config",$dataconfig); */
		
		printf("============================================ \r\n");	
		printf("FINISH CRONJOBS \r\n");	
		
		exit;
	}

	function setting_admin()
	{
		printf("Run Setting Admin Automatically \r\n");
		printf("============================================ \r\n");
		
		$this->db->order_by("vehicle_id","asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_type", "T5");
		
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0)
		{
			printf("No Vehicles \r\n");
			return;
		}
		$rows = $q->result();
		$totalvehicle = count($rows);
		$this->dbsms = $this->load->database("smscolo",true);
		for($i=0;$i<$q->num_rows();$i++)
		{
			printf("Send Admin Message %s (%d/%d)\n", $rows[$i]->vehicle_device, ++$i, $totalvehicle);
			unset($message);
			$message["SenderNumber"] = $rows[$i]->vehicle_card_no;
			$message["TextDecoded"] = "admin114477 6285772802346";
			$message["ReceivingDateTime"] = date("Y-m-d H:i:s");
			$message["RecipientID"] = "alat";
			$this->dbsms->insert("inbox",$message);
		}
		printf("============================================ \r\n");	
		printf("FINISH CRONJOBS \r\n");	
	}
	
	function ceklastinfo_dokar()
	{
		$nowdate = date('Y-m-d H:i:s');
		printf("Run Cron Check Last Info at %s \r\n", $nowdate);
		printf("======================================\r\n");
		
		$this->dbsmsalat = $this->load->database("smsdokaralat",true);
		$this->dbsmsalat->select("count(*) as total");
		$qt = $this->dbsmsalat->get("outbox");
		$rt = $qt->row();
		$total = $rt->total;
		
		if(isset($total))
		{
			if ($total > 41 )
			{
				printf("OUTBOX LEBIH BESAR DARI 20 SMS ! \r\n");
				printf("CRON BERHENTI ! \r\n");
				return;		
			}
		}
		
		$nowdate = date("Y-m-d H:i:s");
		$this->db->order_by("vehicle_id","asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", 1122);
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			printf("No Vehicles \r\n");
			return;
		}
		
		$rows = $q->result();
		$totalvehicle = count($rows);
		$this->db->close();
		
		$j = 1;
		for ($i=0;$i<count($rows);$i++)
		{
			printf("Process Cron For %s (%d/%d)\n", $rows[$i]->vehicle_device, $j, $totalvehicle);
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
							
							if(isset($gps->gps_timestamp)){
								
								$delta = mktime() - $gps->gps_timestamp;
								//cek delay kurang dari 10 menit 
								if ($delta >= 600 && $delta <= 43200) //lebih 10 menit kurang dari 12 jam //yellow condition
								{
									printf("Vehicle No %s Tidak Update \r\n", $rowvehicle->vehicle_no);
									printf("Send Message Process To : %s \r\n", $rowvehicle->vehicle_card_no);	
									
									unset($datasms);
									$datasms["SenderNumber"] = $rowvehicle->vehicle_card_no;
									
									if($rows[$i]->vehicle_type == "T5SILVER")
									{

										if ($rows[$i]->vehicle_operator == "Telkomsel Hallo" || $rows[$i]->vehicle_operator == "Telkomsel Simpati" || 
											$rows[$i]->vehicle_operator == "Telkomsel"){
											$datasms["TextDecoded"] = "APN114477 TELKOMSEL";
										}
										if ($rows[$i]->vehicle_operator == "Indosat Matrix" || $rows[$i]->vehicle_operator == "Indosat IM3" || 
											$rows[$i]->vehicle_operator == "Indosat Mentari" || $rows[$i]->vehicle_operator == "Indosat"){
											$datasms["TextDecoded"] = "APN114477 INDOSATGPRS";
										}
									}
									if($rows[$i]->vehicle_type == "T5 PULSE" || $rows[$i]->vehicle_type == "T5")
									{
										$datasms["TextDecoded"] = "Protocol114477 UDP";
									}
									if($rows[$i]->vehicle_type == "T8_2")
									{
										$datasms["TextDecoded"] = "reset#";
									}
									if($rows[$i]->vehicle_type == "T8")
									{
										$datasms["TextDecoded"] = "reset#";
									}
				
									$datasms["ReceivingDateTime"] = date("Y-m-d H:i:s");
									$datasms["RecipientID"] = "dokaralat";
									$this->dbsms->insert("inbox",$datasms);
									printf("===INSERT=== %s \r\n", $datasms["TextDecoded"]);
								}
								else if($delta >= 43201) //lebih dri 1 hari //red condition 
								{
									printf("===RED CONDITION=== \r\n");
								}
								else
								{
									if($gps->gps_status == "V"){
									
										printf("Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
										/*printf("Send Message Process To : %s \r\n", $rowvehicle->vehicle_card_no);	
										
										unset($datasms);
										$datasms["SenderNumber"] = $rowvehicle->vehicle_card_no;
										
										if($rows[$i]->vehicle_type == "T5")
										{
											$datasms["TextDecoded"] = "Protocol114477 UDP";
										}
										if($rows[$i]->vehicle_type == "T5 PULSE")
										{
											$datasms["TextDecoded"] = "Protocol114477 UDP";
										}
										if($rows[$i]->vehicle_type == "T8_2")
										{
											$datasms["TextDecoded"] = "reset#";
										}
										if($rows[$i]->vehicle_type == "T8")
										{
											$datasms["TextDecoded"] = "reset#";
										}
										
										$datasms["ReceivingDateTime"] = date("Y-m-d H:i:s");
										$datasms["RecipientID"] = "dokaralat";
										$this->dbsms->insert("inbox",$datasms);
										printf("===INSERT=== \r\n"); */
									}else{
										printf("===UPDATE=== \r\n");	
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
		printf("FINISH Delay Check at %s \r\n", $enddate);
		printf("============================== \r\n");

	}
	
	function ceklastinfo_new($vehicletype="", $userid="")
	{
		$nowdate = date('Y-m-d H:i:s');
		printf("Run Cron Check Last Info at %s \r\n", $nowdate);
		printf("======================================\r\n");
		
		$this->dbsmsalat = $this->load->database("smscolo",true);
		$this->dbsmsalat->select("count(*) as total");
		$qt = $this->dbsmsalat->get("outbox");
		$rt = $qt->row();
		$total = $rt->total;
		
		if(isset($total))
		{
			if ($total > 41 )
			{
				printf("OUTBOX LEBIH BESAR DARI 40 SMS ! \r\n");
				printf("CRON BERHENTI ! \r\n");
				return;		
			}
		}
		
		$nowdate = date("Y-m-d H:i:s");
		$this->db->order_by("vehicle_id","asc");
		if (isset($userid) && strlen($userid) > 0)
		{
			$this->db->where("vehicle_user_id",$userid);
		}
		if (isset($vehicletype) && $vehicletype != "")
		{
			$this->db->where("vehicle_type",$vehicletype);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id <>", 1122); //dokar tidak ikut cron ini
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			printf("No Vehicles \r\n");
			return;
		}
		
		$rows = $q->result();
		$totalvehicle = count($rows);
		$this->db->close();
		
		$j = 1;
		for ($i=0;$i<count($rows);$i++)
		{
			printf("Process Cron For %s (%d/%d)\n", $rows[$i]->vehicle_device, $j, $totalvehicle);
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
							
							if(isset($gps->gps_timestamp)){
								
								$delta = mktime() - $gps->gps_timestamp;
								//cek delay kurang dari 10 menit 
								if ($delta >= 600 && $delta <= 43200) //lebih 10 menit kurang dari 12 jam //yellow condition
								{
									printf("Vehicle No %s Tidak Update \r\n", $rowvehicle->vehicle_no);
									printf("Send Message Process To : %s \r\n", $rowvehicle->vehicle_card_no);	
									
									unset($datasms);
									$datasms["SenderNumber"] = $rowvehicle->vehicle_card_no;
									
									if($rows[$i]->vehicle_type == "T5SILVER")
									{

										if ($rows[$i]->vehicle_operator == "Telkomsel Hallo" || $rows[$i]->vehicle_operator == "Telkomsel Simpati" || 
											$rows[$i]->vehicle_operator == "Telkomsel"){
											$datasms["TextDecoded"] = "APN114477 TELKOMSEL";
										}
										if ($rows[$i]->vehicle_operator == "Indosat Matrix" || $rows[$i]->vehicle_operator == "Indosat IM3" || 
											$rows[$i]->vehicle_operator == "Indosat Mentari" || $rows[$i]->vehicle_operator == "Indosat"){
											$datasms["TextDecoded"] = "APN114477 INDOSATGPRS";
										}
									}
									if($rows[$i]->vehicle_type == "T5 PULSE" || $rows[$i]->vehicle_type == "T5")
									{
										$datasms["TextDecoded"] = "Protocol114477 UDP";
									}
									if($rows[$i]->vehicle_type == "T8_2")
									{
										$datasms["TextDecoded"] = "reset#";
									}
									if($rows[$i]->vehicle_type == "T8")
									{
										$datasms["TextDecoded"] = "reset#";
									}
				
									$datasms["ReceivingDateTime"] = date("Y-m-d H:i:s");
									$datasms["RecipientID"] = "alat";
									$this->dbsms->insert("inbox",$datasms);
									printf("===INSERT=== %s \r\n", $datasms["TextDecoded"]);
								}
								else if($delta >= 43201) //lebih dri 1 hari //red condition 
								{
									printf("===RED CONDITION=== \r\n");
								}
								else
								{
									if($gps->gps_status == "V"){
									
										printf("Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
										
									}else{
										printf("===UPDATE=== \r\n");	
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
		printf("FINISH Delay Check at %s \r\n", $enddate);
		printf("============================== \r\n");

	}
	
	function ceklastcomment()
	{
		$nowdate = date('Y-m-d H:i:s');
		printf("Run Cron Check Last Comment at %s \r\n", $nowdate);
		printf("======================================\r\n");
		
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->order_by("comment_id","asc");
		$this->dbtransporter->where("comment_flag", 0);
		$this->dbtransporter->where("comment_status", 0);
		$q = $this->dbtransporter->get("vehicle_comment");
		
		if ($q->num_rows() == 0)
		{
			printf("No Data \r\n");
			return;
		}
		
		$rows = $q->result();
		$totalvehicle = count($rows);
		$this->db->close();
		
		$j = 1;
		for ($i=0;$i<count($rows);$i++)
		{
			printf("Process Cron For %s (%d/%d)\n", $rows[$i]->comment_vehicle_device, $j, $totalvehicle);
			printf("execute %s\r\n", $rows[$i]->comment_vehicle_no);
			
							// last position
							$vehicledevice = $rows[$i]->comment_vehicle_device;
							$commentid = $rows[$i]->comment_id;
							
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
								printf("GPS Belum Aktif!! \r\n");
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

							if(isset($gps->gps_timestamp)){
								
								$delta = mktime() - $gps->gps_timestamp;
								//cek delay kurang dari 10 menit 
								if ($delta >= 600 && $delta <= 43200) //lebih 10 menit kurang dari 12 jam //yellow condition
								{
									printf("Vehicle No %s Yellow Condition \r\n", $rowvehicle->vehicle_no);
									
									unset($data);
									$data["comment_status"] = 1;
									$this->dbtransporter->where("comment_id", $commentid);
									$this->dbtransporter->update("vehicle_comment", $data);
									printf("===UPDATE STATUS COMMENT=== %s \r\n", $rowvehicle->vehicle_no);
								}
								else if($delta >= 43201) //lebih dri 1 hari //red condition 
								{
									printf("===STILL RED CONDITION=== \r\n");
								}
								else
								{
									if($gps->gps_status == "V"){
									
										printf("Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
										
									}else{
										printf("===UPDATE=== \r\n");
										
										unset($data);
										$data["comment_status"] = 1;
										$this->dbtransporter->where("comment_id", $commentid);
										$this->dbtransporter->update("vehicle_comment", $data);
										printf("===UPDATE STATUS COMMENT=== %s \r\n", $rowvehicle->vehicle_no);
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
		printf("FINISH Check Last Comment at %s \r\n", $enddate);
		printf("============================== \r\n");

	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
