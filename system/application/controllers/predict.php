<?php
include "base.php";

class Predict extends Base {
	var $vindex;
	var $m_last_smsreceive;
	
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		
		$this->m_last_smsreceive = 0;
	}

	function park_notmove_alert($vtype="all")
	{
		$parkalert = "";
		$this->db->where("config_name", "park_on_not_move_predict");
		$q = $this->db->get("config");
		
		$rowlast = $q->row();
		$lastrunning = dbmaketime($rowlast->config_value);
		
		echo "config on alert: ".date("d/m/Y H:i:s", $lastrunning)."\r\n";
		
		$this->db->order_by("logs_created", "asc");
		$this->db->where("logs_type", "park_on_not_move_predict");
		$q = $this->db->get("logs");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$parkalert[$rows[$i]->logs_content] = dbmaketime($rows[$i]->logs_created);
		}
		
		if($vtype != "all")
		{
			$this->db->where("vehicle_type", $vtype);
		}
		$this->db->where("vehicle_user_id","2548"); //PREDICT
		$this->db->where("vehicle_status", 1);
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();

		unset($update);
		$update['config_value'] = date("Y-m-d H:i:s", mktime());
		$this->db->where("config_name", "park_on_not_move_predict");
		$this->db->update("config", $update);

		for($i=0; $i < count($rows); $i++)
		{
			$jsonws = json_decode($rows[$i]->vehicle_info);
			if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $rows[$i]->vehicle_no);
				continue;
			}			


			printf("[%s] %04d %s %s %sm...\r\n", date("Ymd H:i:s"), $i+1, $rows[$i]->user_login, $rows[$i]->vehicle_no, $rows[$i]->vehicle_maxparking);
			
			$maxpark = 1800; //30 Menit
			
			if ($rows[$i]->vehicle_info)
			{
				$json = json_decode($rows[$i]->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$tablegps = $this->config->item("external_gpstable");					
											
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}
			

			if (! isset($tablegps))
			{
				$tablegps = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);
			}
			
			$devices = explode("@", $rows[$i]->vehicle_device);
			
			$this->db->order_by("gps_time", "asc");	
			$this->db->join("gps_info", "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where("gps_time >=", date("Y-m-d H:i:s", $lastrunning-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);			
			$q = $this->db->get($tablegps);
			
			$this->db = $this->load->database("master", TRUE);
			
			if ($q->num_rows() == 0) continue;
			
			$rowgps = $q->result();
			
			printf("=== %d record\r\n", count($rowgps)); 
			
			$this->db->where("parkir_device", $rows[$i]->vehicle_device);
			$q = $this->db->get("parkir_on_predict");
			
			if ($q->num_rows() == 0)
			{
				$lastlat = -999999;
				$lastlng = -999999;
				$lastpark = 0;
			}
			else
			{
				$rowparkir = $q->row();
				
				$lastlat = $rowparkir->parkir_lat;
				$lastlng = $rowparkir->parkir_lng;
				$lastpark = dbmaketime($rowparkir->parkir_time);
				
				printf("=== last: %s,%s %s\r\n", $lastlat, $lastlng, date("d/m/Y H:i:s", $lastpark+7*3600)); 
			}
			
			// looping data
						
			for($j=0; $j < count($rowgps); $j++)
			{
				
				$lat = sprintf("%.4f", getLatitude($rowgps[$j]->gps_latitude, $rowgps[$j]->gps_ns));
				$lng = sprintf("%.4f", getLongitude($rowgps[$j]->gps_longitude, $rowgps[$j]->gps_ew));
				
				$ioport = $rowgps[$j]->gps_info_io_port;
				$ison = ((strlen($ioport) > 4) && ($ioport[4] == 1)); //ON || OFF
				
				$myposition = $this->getPosition($rowgps[$j]->gps_longitude, $rowgps[$j]->gps_ew, $rowgps[$j]->gps_latitude, $rowgps[$j]->gps_ns);
				
				if (($lat == $lastlat) && ($lastlng == $lng) && $ison == true) 
				{					
					if (($j+1) == count($rowgps))
					{
						$t = dbmaketime($rowgps[$j]->gps_time);
						$parklength = $t-$lastpark;						
						
						printf("=== current=%d s <-> max=%d s\r\n", $parklength, $maxpark);
						
						if ($parklength < $maxpark)
						{
							continue;
						}
						
						// alert park
						
						$lastpark = $t;
						$this->doalertparkon($rows[$i], $rowgps[$j], $parklength, $parkalert, $myposition->display_name);
					}
					
					continue;
				}			
				
				$lastlat = $lat;
				$lastlng = $lng;
				
				$prevpark = $lastpark;
				$lastpark = dbmaketime($rowgps[$j]->gps_time);
				
				if ($j == 0)
				{					
					continue;
				}
				
				$t = dbmaketime($rowgps[$j-1]->gps_time);
				$parklength = $t-$prevpark;
				
				printf("=== current=%d s <-> max=%d s\r\n", $parklength, $maxpark);
				
				if ($parklength < $maxpark)
				{
					continue;
				}
				$this->doalertparkon($rows[$i], $rowgps[$j-1], $parklength, $parkalert, $myposition->display_name);
			}
			
			$this->db->where("parkir_device", $rows[$i]->vehicle_device);
			$total = $this->db->count_all_results("parkir_on_predict");
			
			unset($update);
			
			$update['parkir_device'] = $rows[$i]->vehicle_device;
			$update['parkir_lat'] = $lastlat;
			$update['parkir_lng'] = $lastlng;
			$update['parkir_time'] = date("Y-m-d H:i:s", $lastpark);
			
			if ($total > 0)
			{
				$this->db->where("parkir_device", $rows[$i]->vehicle_device);
				$this->db->update("parkir_on_predict", $update);
			}
			else
			{
				$this->db->insert("parkir_on_predict", $update);
			}
		}
	}
	
	function doalertparkon($vehicle, $gps, $parklength, $parkalert, $position)
	{
		$isparkalert = false;
		$t = dbmaketime($gps->gps_time);
		
		if (! isset($parkalert[$vehicle->vehicle_device]))
		{
			$parkalert[$vehicle->vehicle_device] = $t;
			$isparkalert = true;
		}
		else
		{
			$delta = mktime() - $parkalert[$vehicle->vehicle_device];
			$isparkalert = $delta > 3600;
		}
		
		if (! $isparkalert) 
		{
			printf("=== alerted on at %s\r\n", date("d/m/Y H:i:s", $parkalert[$vehicle->vehicle_device]));
			return;
		}

		unset($insert);
		
		$insert['parkir_alert_device'] = $vehicle->vehicle_device;
		$insert['parkir_alert_time'] = date("Y-m-d H:i:s", $t);
		$insert['parkir_alert_length'] = round($parklength/60);
		$insert['parkir_alert_max'] = 5;
		$insert['parkir_alert_created'] = date("Y-m-d H:i:s");
		
		$this->db->insert("parkir_on_alert_predict", $insert);
		
		$emails = array("dian@predict-indonesia.com","budiyanto@lacak-mobil.com","williamdjo.mta@gmail.com","muchtar.mta@gmail.com");
			
			foreach($emails as $email)
			{
				unset($mail);
				printf("=== send email to %s \r\n", $email);
				$mail['subject'] = sprintf("Parking ON Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = sprintf(("Pada %s, lama parkir %s %s adalah %s menit dimana ambang batas lama parkir dengan engine ON adalah %s menit, Lokasi %s."), 
											 date("d/m/Y H:i:s", $t+7*3600), $vehicle->vehicle_name, $vehicle->vehicle_no, round($parklength/60), "5", $position
									      );
				$mail['dest'] = $email; 
				$mail['bcc'] = "";
				$mail['sender'] = "noreply-alert@lacak-mobil.com";
			
				lacakmobilmail($mail);								
			}
		
		
		$parkalert[$vehicle->vehicle_device] = mktime();

		unset($update);
							
		$update['logs_created'] = date("Y-m-d H:i:s");
		
		$this->db->where("logs_content", $vehicle->vehicle_device);
		$this->db->where("logs_type", "parkir_on_alert_predict");
		$this->db->update("logs", $update);
		
		if ($this->db->affected_rows() == 0)
		{
			unset($insert);
			
			$insert['logs_created'] = date("Y-m-d H:i:s");
			$insert['logs_content'] = $vehicle->vehicle_device;
			$insert['logs_type'] = "parkir_on_alert_predict";
			$this->db->insert("logs", $insert);
		}
	}

	//contoh dari skrip kim lastposition
	function lastposition($usercode="")
	{
		$nowdate = date('Y-m-d H:i:s');
		$now = date("Y-m-d");
		$offset=0;
		//$emails = array("owner@buddiyanto.com","budiyanto@lacak-mobil.com");
		$emails = array("dian@predict-indonesia.com","budiyanto@lacak-mobil.com","williamdjo.mta@gmail.com","muchtar.mta@gmail.com","monitoring@lacak-mobil.com");
		printf("Search USER CODE at %s \r\n", $nowdate);
		printf("======================================\r\n");
		
		//select list user code
		$this->db = $this->load->database("default", TRUE);
		$this->db->where("user_id", $usercode);
		$this->db->where("user_status",1);
		$quser = $this->db->get("user");
		if ($quser->num_rows() == 0) return;

		$rowsuser = $quser->result();
		$totaluser = count($rowsuser);
		$m = 0;
		
		foreach($rowsuser as $rowuser)
		{
				if (($m+1) < $offset)
				{
					$m++;
					continue;
				}
				
			printf("Prepare Check Last POSITION USER : %s (%d/%d)\n", $rowuser->user_name, ++$m, $totaluser); 
			$user = $rowuser->user_id;
			
			$this->db->order_by("vehicle_no","asc");
			$this->db->where("vehicle_status",1);
			$this->db->where("vehicle_user_id",$user);
			$q = $this->db->get("vehicle");
			
			if ($q->num_rows() == 0)
			{
				printf("No Vehicles \r\n");
				//return;
			}
			
			$rows = $q->result();
			$totalvehicle = count($rows);
			printf("Total Vehicle:  %s \r\n", $totalvehicle);
			
			$j = 1;
			
			for ($i=0;$i<count($rows);$i++)
			{
				//select from db master
				
				
				printf("Process Check Last POSITION For %s %s (%d/%d) USER : %s \n", $rows[$i]->vehicle_no, $rows[$i]->vehicle_device, $j, $totalvehicle, $rowuser->user_name);
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
								
								/*if ($this->gpsmodel->fromsocket)
								{
									$datainfo = $this->gpsmodel->datainfo;
									$fromsocket = $this->gpsmodel->fromsocket;			
								}*/
										
								if (! $gps)
								{
									printf("===GO TO HISTORY=== \r\n");
									foreach($emails as $email)
									{
										unset($mail);
										printf("=== send email to %s \r\n", $email);
										$mail['subject'] = sprintf("GPS Merah (Go To History) : %s", $rowvehicle->vehicle_no);
										$mail['message'] = sprintf(("Kendaraan dengan Nomor Polisi %s, Status GPS Offline selama lebih dari 1 Minggu."), 
																     $rowvehicle->vehicle_no
																  );
										$mail['dest'] = $email; 
										$mail['bcc'] = "";
										$mail['sender'] = "noreply-alert@lacak-mobil.com";
										
										lacakmobilmail($mail);								
									}
									
								}

								$gtps = $this->config->item("vehicle_gtp");

								//$dir = $gps->direction-1;
								$dirs = $this->config->item("direction");
								
								//io status
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
								$skip = 0;
								
								if(isset($gps->gps_timestamp)){
									
									$delta = ((mktime() - $gps->gps_timestamp - 3600 )); // tidak dikurangi 3600 detik
									
									//lebih 10 menit kurang dari 24 jam //yellow condition
									if ($delta >= 600 && $delta <= 43200) 
									{
										printf("Vehicle No %s GPS YELOW \r\n", $rowvehicle->vehicle_no);
									
									}
									else if($delta >= 43201) //lebih dari 1 hari //red condition 
									{
										$gpstime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($gps->gps_time)));
										
										printf("===GPS RED=== \r\n");
										foreach($emails as $email)
										{
											unset($mail);
											printf("=== send email to %s \r\n", $email);
											$mail['subject'] = sprintf("GPS Merah : %s", $rowvehicle->vehicle_no);
											$mail['message'] = sprintf(("Kendaraan dengan Nomor Polisi %s, Status GPS Tidak Aktif Sejak %s ."), 
																		 $rowvehicle->vehicle_no, date("d-m-Y H:i:s",strtotime($gpstime))
																      );
											$mail['dest'] = $email; 
											$mail['bcc'] = "";
											$mail['sender'] = "noreply-alert@lacak-mobil.com";
										
											lacakmobilmail($mail);								
										}
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
									printf("===NO DATA GPS=== \r\n");	
								}
				$j++;
			}
		
			
		}
		$this->db->close();
		$this->db->cache_delete_all();
		$enddate = date('Y-m-d H:i:s');
		printf("FINISH Check Last POSITION from %s to %s \r\n", $nowdate, $enddate);
		printf("============================== \r\n");

	}
	
	function getPosition($longitude, $ew, $latitude, $ns)
	{
		$gps_longitude_real = getLongitude($longitude, $ew);
		$gps_latitude_real = getLatitude($latitude, $ns);
					
		$gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
		$gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");	
					
		$georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
		
		return $georeverse;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
