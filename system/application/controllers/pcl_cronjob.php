<?php
include "base.php";

class Pcl_cronjob extends Base {

	function Pcl_cronjob()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->model("historymodel");
		$this->load->library('email');

	}
	
	//for all
    function getPosition($longitude, $ew, $latitude, $ns){
        $gps_longitude_real = getLongitude($longitude, $ew);
        $gps_latitude_real = getLatitude($latitude, $ns);
        
        $gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
        $gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");    
        $georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
        
		return $georeverse;
    }
	
	//for selected user
    function getGeofence_location_simarno($longitude, $ew, $latitude, $ns, $vehicle_device) 
    {
        
        $this->db = $this->load->database("default", true);
        $lng = getLongitude($longitude, $ew);
        $lat = getLatitude($latitude, $ns);
																		
        $sql = sprintf("
                    SELECT     *
                    FROM     %sgeofence 
                    WHERE     TRUE
                            AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
                            AND (geofence_user = 3238 )
                            AND (geofence_status = 1)
                    LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_device);

        $q = $this->db->query($sql);

        if ($q->num_rows() > 0)
        {            
            $row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }
            
        }else
        {
            return false;
        }

    }
	
	function getGeofence_location_gita($longitude, $ew, $latitude, $ns, $vehicle_device) 
    {
        
        $this->db = $this->load->database("default", true);
        $lng = getLongitude($longitude, $ew);
        $lat = getLatitude($latitude, $ns);
																	
        $sql = sprintf("
                    SELECT     *
                    FROM     %sgeofence 
                    WHERE     TRUE
                            AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
                            AND (geofence_user = 3041 )
                            AND (geofence_status = 1)
                    LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_device);

        $q = $this->db->query($sql);

        if ($q->num_rows() > 0)
        {            
            $row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }
            
        }else
        {
            return false;
        }

    }
	
	//for PCL
	function history_hour($startdate= "")
	{
		ini_set('memory_limit', '-1');
		printf("PROSES AUTO REPORT HISTORY HOURS >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report_type = "history_hour";
		$report = "history_hour_";
		$userid_simarno = 3238;
		$userid_gita = 3041;
		
	
		//looping per config jam 
		$this->dbreport = $this->load->database("pcl_report",true);
		$qconfigtime = $this->dbreport->get("history_hour_config_time");
        $rconfigtime = $qconfigtime->result();
		$totalconfigtime = count($rconfigtime);
		
		if(isset($totalconfigtime) && ($totalconfigtime>0)){
			
			//looping beruser yg didaftarkan
			for ($xxx=0;$xxx<count($rconfigtime);$xxx++)
			{
				printf("============================================ \r\n");
				printf("STARTING CONFIG TIME : %s to %s ==(%d/%d) \r\n",$rconfigtime[$xxx]->time_start, $rconfigtime[$xxx]->time_end, $xxx, $totalconfigtime);
				if ($startdate == "") {
					$startdate = date("Y-m-d"." ".$rconfigtime[$xxx]->time_start, strtotime("yesterday"));
					$datefilename = date("Ymd", strtotime("yesterday"));
					$month = date("F", strtotime("yesterday"));
					$year = date("Y", strtotime("yesterday"));

					$xconfigtime = explode(":",$rconfigtime[$xxx]->time_start);
					$configtime_head = $xconfigtime[0];
					$enddate = date("Y-m-d"." ".$rconfigtime[$xxx]->time_end, strtotime("yesterday"));
					
				}

				if ($startdate != "")
				{
					$datefilename = date("Ymd", strtotime($startdate));     
					$startdate = date("Y-m-d"." ".$rconfigtime[$xxx]->time_start, strtotime($startdate));
					$month = date("F", strtotime($startdate));
					$year = date("Y", strtotime($startdate));
					
					$xconfigtime = explode(":",$rconfigtime[$xxx]->time_start);
					$configtime_head = $xconfigtime[0];
					
					$enddate = date("Y-m-d"." ".$rconfigtime[$xxx]->time_end, strtotime($startdate));
				}
				
				
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
				
				$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
				$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
				$z =0;
				
				printf("REPORT DATE SELECTED : %s \r\n",$startdate);
				printf("GPS TIME SELECTED : %s to %s \r\n",$sdate, $edate);
				
				
				//looping user yang menggunakan report ini 
				$this->dbreport->where("config_active",1);
				$qconfig = $this->dbreport->get("history_hour_config");
				$rconfig = $qconfig->result();
				$totalconfig = count($rconfig);
				
				if(isset($totalconfig) && ($totalconfig>0)){
					$total_process_all = 0;
					//looping beruser yg didaftarkan
					for ($xx=0;$xx<count($rconfig);$xx++)
					{
					
						$this->db->order_by("vehicle_device", "asc");
						$this->db->where("vehicle_user_id", $rconfig[$xx]->config_user);
						$this->db->where("vehicle_status <>", 3);
						$q = $this->db->get('vehicle');
						$rowvehicle = $q->result();

						$total_process = count($rowvehicle);
						$total_process_all = $total_process_all + $total_process;
						printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
						
						for ($x=0;$x<count($rowvehicle);$x++)
						{
							
							printf("PROSES VEHICLE ID %s : %s (%d/%d)  \r\n",$rconfig[$xx]->config_user_name, $rowvehicle[$x]->vehicle_device, ++$z, $total_process_all);
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
									}
									else
									{
										$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
										$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
										$this->dbhist = $this->load->database("default", TRUE);
										$this->dbhist2 = $this->load->database("gpshistory",true);		
									}
									
									$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
									$vehicle_no = $rowvehicle[$x]->vehicle_no;
									$vehicle_dev = $rowvehicle[$x]->vehicle_device;
									$vehicle_name = $rowvehicle[$x]->vehicle_name;
										
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
										
										$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
										$this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
										$this->dbhist->where("gps_time >=", $sdate);
										$this->dbhist->where("gps_time <=", $edate);   
										$this->dbhist->limit(1);		
										$this->dbhist->order_by("gps_time","asc");
										$this->dbhist->from($table);
										$q = $this->dbhist->get();
										$rows1 = $q->result();
										
										$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
										$this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
										$this->dbhist2->where("gps_time >=", $sdate);
										$this->dbhist2->where("gps_time <=", $edate);    
										$this->dbhist2->limit(1); 
										$this->dbhist2->order_by("gps_time","asc");
										$this->dbhist2->from($tablehist);
										$q2 = $this->dbhist2->get();
										$rows2 = $q2->result();
										
										$rows = array_merge($rows1, $rows2);
							
										//$data = array();
										$nopol = "";
										$on = false;
										$trows = count($rows);
										
										printf("TOTAL DATA GPS : %s \r\n",$trows);
										//print_r($rows);exit();
										//jika ada data gps
										if(isset($trows) && ($trows > 0)){
											
											for($i=0;$i<$trows;$i++)
											{
												$history_hour_device = $rows[$i]->gps_info_device;
												$history_hour_no = 0;
												$history_hour_name = 0;
												$history_hour_user = 0;
												$history_hour_company = 0;
												$history_hour_group = 0;
												$history_hour_date = "";
												
												$history_hour_address = "";
												$history_hour_geofence = "";
												$history_hour_gps = "";
												
												//select data vehicle in report
												$this->db->select("vehicle_id,vehicle_no,vehicle_device,vehicle_name,vehicle_company,vehicle_group,vehicle_user_id");
												$this->db->where("vehicle_device",$history_hour_device);
												$this->db->where("vehicle_status <>",3);
												$this->db->limit(1);
												$qconfigvehicle = $this->db->get("vehicle");
												$rconfigvehicle = $qconfigvehicle->row();
												if(count($rconfigvehicle)>0){
													$history_hour_user = $rconfigvehicle->vehicle_user_id;
													$history_hour_no = $rconfigvehicle->vehicle_no;
													$history_hour_name = $rconfigvehicle->vehicle_name;
													$history_hour_company = $rconfigvehicle->vehicle_company;
													$history_hour_group = $rconfigvehicle->vehicle_group;
												}
												
												$history_hour_gps = date("H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
												$history_hour_address = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
												
												if($history_hour_user == $userid_simarno){
													$history_hour_geofence = $this->getGeofence_location_simarno($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
												}
												if($history_hour_user == $userid_gita){
													$history_hour_geofence = $this->getGeofence_location_gita($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
												}
												
												$history_hour_date = date("Y-m-d", strtotime($startdate));
												$history_hour_coor = $rows[$i]->gps_latitude_real.", ".$rows[$i]->gps_longitude_real;
												
												printf("SEARCHING AVAILABLE DATA VEHICLE IN DB REPORT . . .  \r\n");
												$this->dbreport->select("history_hour_device,history_hour_date");
												$this->dbreport->where("history_hour_device",$history_hour_device);
												$this->dbreport->where("history_hour_date",$history_hour_date);
												$this->dbreport->limit(1);
												$qconfigreport = $this->dbreport->get($dbtable);
												$rconfigreport = $qconfigreport->row();
												
												printf("PREPARE INSERT TO DB REPORT \r\n");
												
												if(count($rconfigreport)>0){
													//jika ada data maka update
													printf("AVAILABLE DATA - UPDATE . . .  \r\n");
													
													unset($data_hasil);
													
													$data_hasil["history_hour_".$configtime_head."_address"] = $history_hour_address->display_name;
													$data_hasil["history_hour_".$configtime_head."_geofence"] = $history_hour_geofence;
													$data_hasil["history_hour_".$configtime_head."_gps"] = $history_hour_gps;
													$data_hasil["history_hour_".$configtime_head."_coor"] = $history_hour_coor;
													
													$this->dbreport->where("history_hour_device",$history_hour_device);
													$this->dbreport->where("history_hour_date",$history_hour_date);
													$this->dbreport->update($dbtable,$data_hasil);
													printf("UPDATE OK !!  \r\n");
												}else{
													//jika tidak ada insert
													printf("NOT AVAILABLE DATA - INSERT . . .  \r\n");
													
													unset($data_hasil);
													$data_hasil["history_hour_device"] = $history_hour_device;
													$data_hasil["history_hour_no"] = $history_hour_no;
													$data_hasil["history_hour_name"] = $history_hour_name;
													$data_hasil["history_hour_user"] = $history_hour_user;
													$data_hasil["history_hour_company"] = $history_hour_company;
													$data_hasil["history_hour_group"] = $history_hour_group;
													$data_hasil["history_hour_date"] = $history_hour_date;
													
													$data_hasil["history_hour_".$configtime_head."_address"] = $history_hour_address->display_name;
													$data_hasil["history_hour_".$configtime_head."_geofence"] = $history_hour_geofence;
													$data_hasil["history_hour_".$configtime_head."_gps"] = $history_hour_gps;
													$data_hasil["history_hour_".$configtime_head."_coor"] = $history_hour_coor;
													
													
												
													$this->dbreport->insert($dbtable,$data_hasil);
													printf("INSERT OK !!  \r\n");
												}
											}
										
										}else{
											printf("NO DATA GPS \r\n");
											
										}
											
										printf("END HOUR \r\n");
										printf("============================================ \r\n");
								
								}
								else
								{
									printf("SKIP VEHICLE ( NO VEHICLE PORT ) \r\n");
									printf("-------------------------------------- \r\n");    
								}
								
							}
							else
							{
								printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
								printf("-------------------------------- \r\n");
							}
						}
							
						
					}
					
				}else{
					printf("TIDAK ADA DATA USER YG DIDAFTARKAN \r\n");
					printf("============================================ \r\n");
				}
				
				
			}
		}
	
		printf("DELETE CACHE DB HISTORY & REPORT \r\n");
		$this->dbhist->cache_delete_all();
		$this->dbreport->cache_delete_all();
										
		$finish_time = date("Y-m-d H:i:s");
		printf("DONE HISTORY HOURS: %s \r\n",$finish_time);
		
		//Send Email
		$cron_name = "PCL - CRON HISTORY HOURS";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$startdate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com";
		$mail['bcc'] = "report.dokar@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
		
		printf("SEND EMAIL OK \r\n");
		
		return;   
	}
	
	function history_hour_vehicle($startdate="",$vehicledevice="",$vehicletype="")
	{
		ini_set('memory_limit', '-1');
		printf("PROSES AUTO REPORT HISTORY HOURS >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report_type = "history_hour";
		$report = "history_hour_";
		$userid_simarno = 3238;
		$userid_gita = 3041;
		
	
		//looping per config jam 
		$this->dbreport = $this->load->database("pcl_report",true);
		$qconfigtime = $this->dbreport->get("history_hour_config_time");
        $rconfigtime = $qconfigtime->result();
		$totalconfigtime = count($rconfigtime);
		
		if(isset($totalconfigtime) && ($totalconfigtime>0)){
			
			//looping beruser yg didaftarkan
			for ($xxx=0;$xxx<count($rconfigtime);$xxx++)
			{
				printf("============================================ \r\n");
				printf("STARTING CONFIG TIME : %s to %s ==(%d/%d) \r\n",$rconfigtime[$xxx]->time_start, $rconfigtime[$xxx]->time_end, $xxx, $totalconfigtime);
				if ($startdate == "") {
					$startdate = date("Y-m-d"." ".$rconfigtime[$xxx]->time_start, strtotime("yesterday"));
					$datefilename = date("Ymd", strtotime("yesterday"));
					$month = date("F", strtotime("yesterday"));
					$year = date("Y", strtotime("yesterday"));

					$xconfigtime = explode(":",$rconfigtime[$xxx]->time_start);
					$configtime_head = $xconfigtime[0];
					$enddate = date("Y-m-d"." ".$rconfigtime[$xxx]->time_end, strtotime("yesterday"));
					
				}

				if ($startdate != "")
				{
					$datefilename = date("Ymd", strtotime($startdate));     
					$startdate = date("Y-m-d"." ".$rconfigtime[$xxx]->time_start, strtotime($startdate));
					$month = date("F", strtotime($startdate));
					$year = date("Y", strtotime($startdate));
					
					$xconfigtime = explode(":",$rconfigtime[$xxx]->time_start);
					$configtime_head = $xconfigtime[0];
					
					$enddate = date("Y-m-d"." ".$rconfigtime[$xxx]->time_end, strtotime($startdate));
				}
				
				
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
				
				$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
				$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
				$z =0;
				
				printf("REPORT DATE SELECTED : %s \r\n",$startdate);
				printf("GPS TIME SELECTED : %s to %s \r\n",$sdate, $edate);
				
				
				//looping user yang menggunakan report ini 
				$this->dbreport->where("config_active",1);
				$qconfig = $this->dbreport->get("history_hour_config");
				$rconfig = $qconfig->result();
				$totalconfig = count($rconfig);
				
				if(isset($totalconfig) && ($totalconfig>0)){
					$total_process_all = 0;
					//looping beruser yg didaftarkan
					for ($xx=0;$xx<count($rconfig);$xx++)
					{
					
						$this->db->order_by("vehicle_device", "asc");
						$this->db->where("vehicle_user_id", $rconfig[$xx]->config_user);
						$this->db->where("vehicle_device", $vehicledevice."@".$vehicletype);
						$this->db->where("vehicle_status <>", 3);
						$q = $this->db->get('vehicle');
						$rowvehicle = $q->result();

						$total_process = count($rowvehicle);
						$total_process_all = $total_process_all + $total_process;
						printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
						
						for ($x=0;$x<count($rowvehicle);$x++)
						{
							
							printf("PROSES VEHICLE ID %s : %s (%d/%d)  \r\n",$rconfig[$xx]->config_user_name, $rowvehicle[$x]->vehicle_device, ++$z, $total_process_all);
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
									}
									else
									{
										$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
										$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
										$this->dbhist = $this->load->database("default", TRUE);
										$this->dbhist2 = $this->load->database("gpshistory",true);		
									}
									
									$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
									$vehicle_no = $rowvehicle[$x]->vehicle_no;
									$vehicle_dev = $rowvehicle[$x]->vehicle_device;
									$vehicle_name = $rowvehicle[$x]->vehicle_name;
										
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
										
										$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
										$this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
										$this->dbhist->where("gps_time >=", $sdate);
										$this->dbhist->where("gps_time <=", $edate);   
										$this->dbhist->limit(1);		
										$this->dbhist->order_by("gps_time","asc");
										$this->dbhist->from($table);
										$q = $this->dbhist->get();
										$rows1 = $q->result();
										
										$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
										$this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
										$this->dbhist2->where("gps_time >=", $sdate);
										$this->dbhist2->where("gps_time <=", $edate);    
										$this->dbhist2->limit(1); 
										$this->dbhist2->order_by("gps_time","asc");
										$this->dbhist2->from($tablehist);
										$q2 = $this->dbhist2->get();
										$rows2 = $q2->result();
										
										$rows = array_merge($rows1, $rows2);
							
										//$data = array();
										$nopol = "";
										$on = false;
										$trows = count($rows);
										
										printf("TOTAL DATA GPS : %s \r\n",$trows);
										//print_r($rows);exit();
										//jika ada data gps
										if(isset($trows) && ($trows > 0)){
											
											for($i=0;$i<$trows;$i++)
											{
												$history_hour_device = $rows[$i]->gps_info_device;
												$history_hour_no = 0;
												$history_hour_name = 0;
												$history_hour_user = 0;
												$history_hour_company = 0;
												$history_hour_group = 0;
												$history_hour_date = "";
												
												$history_hour_address = "";
												$history_hour_geofence = "";
												$history_hour_gps = "";
												
												//select data vehicle in report
												$this->db->select("vehicle_id,vehicle_no,vehicle_device,vehicle_name,vehicle_company,vehicle_group,vehicle_user_id");
												$this->db->where("vehicle_device",$history_hour_device);
												$this->db->where("vehicle_status <>",3);
												$this->db->limit(1);
												$qconfigvehicle = $this->db->get("vehicle");
												$rconfigvehicle = $qconfigvehicle->row();
												if(count($rconfigvehicle)>0){
													$history_hour_user = $rconfigvehicle->vehicle_user_id;
													$history_hour_no = $rconfigvehicle->vehicle_no;
													$history_hour_name = $rconfigvehicle->vehicle_name;
													$history_hour_company = $rconfigvehicle->vehicle_company;
													$history_hour_group = $rconfigvehicle->vehicle_group;
												}
												
												$history_hour_gps = date("H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
												$history_hour_address = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
												
												if($history_hour_user == $userid_simarno){
													$history_hour_geofence = $this->getGeofence_location_simarno($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
												}
												if($history_hour_user == $userid_gita){
													$history_hour_geofence = $this->getGeofence_location_gita($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
												}
												
												$history_hour_date = date("Y-m-d", strtotime($startdate));
												$history_hour_coor = $rows[$i]->gps_latitude_real.", ".$rows[$i]->gps_longitude_real;
												
												printf("SEARCHING AVAILABLE DATA VEHICLE IN DB REPORT . . .  \r\n");
												$this->dbreport->select("history_hour_device,history_hour_date");
												$this->dbreport->where("history_hour_device",$history_hour_device);
												$this->dbreport->where("history_hour_date",$history_hour_date);
												$this->dbreport->limit(1);
												$qconfigreport = $this->dbreport->get($dbtable);
												$rconfigreport = $qconfigreport->row();
												
												printf("PREPARE INSERT TO DB REPORT \r\n");
												
												if(count($rconfigreport)>0){
													//jika ada data maka update
													printf("AVAILABLE DATA - UPDATE . . .  \r\n");
													
													unset($data_hasil);
													
													$data_hasil["history_hour_".$configtime_head."_address"] = $history_hour_address->display_name;
													$data_hasil["history_hour_".$configtime_head."_geofence"] = $history_hour_geofence;
													$data_hasil["history_hour_".$configtime_head."_gps"] = $history_hour_gps;
													$data_hasil["history_hour_".$configtime_head."_coor"] = $history_hour_coor;
													
													$this->dbreport->where("history_hour_device",$history_hour_device);
													$this->dbreport->where("history_hour_date",$history_hour_date);
													$this->dbreport->update($dbtable,$data_hasil);
													printf("UPDATE OK !!  \r\n");
												}else{
													//jika tidak ada insert
													printf("NOT AVAILABLE DATA - INSERT . . .  \r\n");
													
													unset($data_hasil);
													$data_hasil["history_hour_device"] = $history_hour_device;
													$data_hasil["history_hour_no"] = $history_hour_no;
													$data_hasil["history_hour_name"] = $history_hour_name;
													$data_hasil["history_hour_user"] = $history_hour_user;
													$data_hasil["history_hour_company"] = $history_hour_company;
													$data_hasil["history_hour_group"] = $history_hour_group;
													$data_hasil["history_hour_date"] = $history_hour_date;
													
													$data_hasil["history_hour_".$configtime_head."_address"] = $history_hour_address->display_name;
													$data_hasil["history_hour_".$configtime_head."_geofence"] = $history_hour_geofence;
													$data_hasil["history_hour_".$configtime_head."_gps"] = $history_hour_gps;
													$data_hasil["history_hour_".$configtime_head."_coor"] = $history_hour_coor;
													
													
												
													$this->dbreport->insert($dbtable,$data_hasil);
													printf("INSERT OK !!  \r\n");
												}
											}
										
										}else{
											printf("NO DATA GPS \r\n");
											
										}
											
										printf("END HOUR \r\n");
										printf("============================================ \r\n");
								
								}
								else
								{
									printf("SKIP VEHICLE ( NO VEHICLE PORT ) \r\n");
									printf("-------------------------------------- \r\n");    
								}
								
							}
							else
							{
								printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
								printf("-------------------------------- \r\n");
							}
						}
							
						
					}
					
				}else{
					printf("TIDAK ADA DATA USER YG DIDAFTARKAN \r\n");
					printf("============================================ \r\n");
				}
				
				
			}
		}
	
		printf("DELETE CACHE DB HISTORY & REPORT \r\n");
		$this->dbhist->cache_delete_all();
		$this->dbreport->cache_delete_all();
										
		$finish_time = date("Y-m-d H:i:s");
		printf("DONE HISTORY HOURS PER VEHICLE: %s \r\n",$finish_time);
		
		
		return;   
	}

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
