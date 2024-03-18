<?php
include "base.php";

class Customreport extends Base {

	function Customreport()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}
	
	function operational($userid="", $orderby="", $startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO REPORT DATA OPERASIONAL KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "operasional";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
		$report = "operasional_";
        
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
		
		$this->db->order_by("vehicle_id", $orderby);
		if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_not_in("vehicle_type", $this->config->item('vehicle_no_engine'));
		$this->db->where_not_in("vehicle_type", $this->config->item('vehicle_others'));
		
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, $rowvehicle[$x]->user_name, ++$z, $total_process);
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
					
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_user_id",$rowvehicle[$x]->vehicle_user_id);
					$this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
					$this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_new");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {

                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
						$this->dbhist->limit(3000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
		
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
						$this->dbhist2->limit(3000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
                
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
					if($trows < 10000){
						
						////-------------KONDISI ON-------------////
						if ($trows > 0){
						for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude_real;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude_real;
		
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude_real;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude_real;
									
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude_real;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle //hanya data on
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {
									
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude_real;
										
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude_real;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude_real;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data On
                        $i=1;
                        $new = "";
                        printf("WRITE DATA ON : ");
                        foreach($data as $vehicle_no=>$val)
                        {
                            if($new != $vehicle_no)
                            {
                                $cumm = 0;
                                $trip_no = 1;
                            }
							
                            foreach($val as $no=>$report)
                            {
                                $mileage = $report['end_mileage']- $report['start_mileage'];
                               // if($mileage != 0) // edit 0 km engine ON
                               // {
                                    $duration = get_time_difference($report['start_time'], $report['end_time']);
									
									$start_1 = dbmaketime($report['start_time']);
									$end_1 = dbmaketime($report['end_time']);
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
                                    $tm = round(($mileage/1000),2);
                                    $cumm += $tm;
                                    printf("|%s|",$i);
									
									$notrip = $trip_no++;
								 
                                    //mileage
                                    $xme = $tm;
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                            
                                    //cummulative
                                    $xcum = $cumm;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
									
									$geofence_start = $report['start_geofence_location'];
									$geofence_end = $report['end_geofence_location'];
									
									
									
								if(isset($report['vehicle_name'])){
										unset($datainsert);

										$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
										$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
										$datainsert["trip_mileage_vehicle_name"] = $vehicle_name;
										$datainsert["trip_mileage_vehicle_type"] = $vehicle_type;
										$datainsert["trip_mileage_trip_no"] = $notrip;
										$datainsert["trip_mileage_engine"] = $report['engine'];
										$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report['start_time']));
										$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report['end_time']));
										$datainsert["trip_mileage_duration"] = $show;
										$datainsert["trip_mileage_duration_sec"] = $duration_sec;
										$datainsert["trip_mileage_trip_mileage"] = $x_mile;
										$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
										$datainsert["trip_mileage_location_start"] = $report['start_position']->display_name;
										$datainsert["trip_mileage_location_end"] = $report['end_position']->display_name;
										$datainsert["trip_mileage_geofence_start"] = $geofence_start;
										$datainsert["trip_mileage_geofence_end"] = $geofence_end;
										$datainsert["trip_mileage_coordinate_start"] = $report['start_coordinate'];
										$datainsert["trip_mileage_coordinate_end"] = $report['end_coordinate'];
										$datainsert["trip_mileage_door_start"] = $report['start_door'];
										$datainsert["trip_mileage_door_end"] = $report['end_door'];
										$datainsert["trip_mileage_lat"] = $report['start_latitude'];
										$datainsert["trip_mileage_lng"] = $report['start_longitude'];
										$datainsert["trip_mileage_totaldata"] = $trows;
										
										
										
									//edit flag engine ON , nol km, lebih dari 4 menit = 240 -> edit 1 menit = 30 detik //27-9-17 //10-12-17
									if($duration_sec > 29 ){
										$this->dbtrip = $this->load->database("operational_report",TRUE);
										$this->dbtrip->insert($dbtable,$datainsert);
										
										printf("OK");
									}
									
								}

                                    $i++;
								//}
                            }
                        }
						
                        unset($data);
            
                        printf("FINISH FOR VEHICLE ON : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
						}
						
						////---------------KONDISI OFF---------------///
						if ($trows > 0){
                        for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude_real;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude_real;
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude_real;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude_real;
                                    
									$on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude_real;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle //hanya data off
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {
									//print_r(substr($rows[$i]->gps_info_io_port, 4, 1));exit();
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude_real;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude_real;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude_real;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data off //kondisi tidak ada data ?
                        $i=1;
                        $new = "";
                        printf("WRITE DATA OFF : ");
                        foreach($data as $vehicle_no=>$val)
                        {
                            if($new != $vehicle_no)
                            {
                                $cumm = 0;
                                $trip_no = 1;
                            }
							
                            foreach($val as $no=>$report_off)
                            {
                                $mileage = $report_off['end_mileage']- $report_off['start_mileage'];
                               // if($mileage != 0) // edit 0 km engine off
                               // {
                                    $duration = get_time_difference($report_off['start_time'], $report_off['end_time']);
									
									$start_1 = dbmaketime($report_off['start_time']);
									$end_1 = dbmaketime($report_off['end_time']);
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
                                    $tm = round(($mileage/1000),2);
                                    $cumm += $tm;
                                    printf("|%s|",$i);
									
									$notrip = $trip_no++;
								 
                                    //mileage
                                    $xme = $tm;
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                            
                                    //cummulative
                                    $xcum = $cumm;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
									$geofence_start_off = $report_off['start_geofence_location'];
									$geofence_end_off = $report_off['end_geofence_location'];

									//edit flag engine OFF , nol km, lebih dari 10 menit
								if (isset($report_off['vehicle_name'])){
										unset($datainsert);
										$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
										$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
										$datainsert["trip_mileage_vehicle_name"] = $vehicle_name;
										$datainsert["trip_mileage_vehicle_type"] = $vehicle_type;
										$datainsert["trip_mileage_trip_no"] = $notrip;
										$datainsert["trip_mileage_engine"] = $report_off['engine'];
										$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report_off['start_time']));
										$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report_off['end_time']));
										$datainsert["trip_mileage_duration"] = $show;
										$datainsert["trip_mileage_duration_sec"] = $duration_sec;
										$datainsert["trip_mileage_trip_mileage"] = $x_mile;
										$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
										$datainsert["trip_mileage_location_start"] = $report_off['start_position']->display_name;
										$datainsert["trip_mileage_location_end"] = $report_off['end_position']->display_name;
										$datainsert["trip_mileage_geofence_start"] = $geofence_start_off;
										$datainsert["trip_mileage_geofence_end"] = $geofence_end_off;
										$datainsert["trip_mileage_coordinate_start"] = $report_off['start_coordinate'];
										$datainsert["trip_mileage_coordinate_end"] = $report_off['end_coordinate'];
										$datainsert["trip_mileage_door_start"] = $report_off['start_door'];
										$datainsert["trip_mileage_door_end"] = $report_off['end_door'];
										$datainsert["trip_mileage_lat"] = $report['start_latitude'];
										$datainsert["trip_mileage_lng"] = $report['start_longitude'];
										$datainsert["trip_mileage_totaldata"] = $trows;
										
									//edit flag engine OFF , nol km, lebih dari 4 menit = 240 -> edit 1 menit = 30 detik //27-9-17 //10-12-17
									if($duration_sec > 29 ){
										$this->dbtrip = $this->load->database("operational_report",TRUE);
										$this->dbtrip->insert($dbtable,$datainsert);
										printf("OK");
									}
								}
                                    $i++;
                             // }
                            }
                        }
                        printf("FINISH FOR VEHICLE OFF : %s \r\n",$rowvehicle[$x]->vehicle_device);
						printf("============================================ \r\n");
						}
						
					}else{
						printf("TO MANY : %s \r\n", $trows);
						printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
					}
						printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_new",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", $trows);
						}
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
                       
                        printf("============================================ \r\n");

                    }
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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA OPERASIONAL DONE %s\r\n",$finish_time);
	
	if($total_process != 0){
		
		//Send Email
		$cron_name = $cron_username." - "."OPERATIONAL REPORT";
		$this->dbtrip = $this->load->database("operational_report",TRUE);
        $this->dbtrip->select("trip_mileage_id");
        $this->dbtrip->where("trip_mileage_start_time >=",$startdate);
        $this->dbtrip->where("trip_mileage_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
		//get telegram group by company
		$this->db = $this->load->database("default",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$startdate." to ".$enddate." \n".
					"Total Data: ".$total_data." \n".
					"Start: ".$startproses." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Cron Name : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_data."
End Data   : "."( ".$z." / ".$total_process." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.lacakmobil@yahoo.com";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
	}
			
		printf("Send Email OK");
        
    }
	
	function operational_other($userid="", $orderby="", $startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO REPORT DATA OPERASIONAL KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "operasional";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
		$report = "operasional_";
        
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
        
		$sdate = date("Y-m-d H:i:s", strtotime($startdate));
        $edate = date("Y-m-d H:i:s", strtotime($enddate));
        $z =0;
		
		$this->db->order_by("vehicle_id", $orderby);
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_not_in("vehicle_type", $this->config->item('vehicle_no_engine'));
		$this->db->where_in("vehicle_type", $this->config->item('vehicle_others'));
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
       
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, $rowvehicle[$x]->user_name, ++$z, $total_process);
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
					
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_user_id",$rowvehicle[$x]->vehicle_user_id);
					$this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
					$this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_new");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {

                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
						$this->dbhist->limit(4000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
		
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
						$this->dbhist2->limit(4000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
                
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
					if($trows < 10000){
						
						////-------------KONDISI ON-------------////
						if ($trows > 0){
						for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_latitude);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude;
									
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle //hanya data on
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {
									
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude;
										
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude,$rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id );
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data On
                        $i=1;
                        $new = "";
                        printf("WRITE DATA ON : ");
                        foreach($data as $vehicle_no=>$val)
                        {
                            if($new != $vehicle_no)
                            {
                                $cumm = 0;
                                $trip_no = 1;
                            }
							
                            foreach($val as $no=>$report)
                            {
                                $mileage = $report['end_mileage']- $report['start_mileage'];
                               // if($mileage != 0) // edit 0 km engine ON
                               // {
                                    $duration = get_time_difference($report['start_time'], $report['end_time']);
									
									$start_1 = dbmaketime($report['start_time']);
									$end_1 = dbmaketime($report['end_time']);
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
                                    $tm = round(($mileage/1000),2);
                                    $cumm += $tm;
                                    printf("|%s|",$i);
									
									$notrip = $trip_no++;
								 
                                    //mileage
                                    $xme = $tm;
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                            
                                    //cummulative
                                    $xcum = $cumm;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
									
									$geofence_start = $report['start_geofence_location'];
									$geofence_end = $report['end_geofence_location'];
									
									
									
								if(isset($report['vehicle_name'])){
										unset($datainsert);

										$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
										$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
										$datainsert["trip_mileage_vehicle_name"] = $vehicle_name;
										$datainsert["trip_mileage_vehicle_type"] = $vehicle_type;
										$datainsert["trip_mileage_trip_no"] = $notrip;
										$datainsert["trip_mileage_engine"] = $report['engine'];
										$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report['start_time']));
										$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report['end_time']));
										$datainsert["trip_mileage_duration"] = $show;
										$datainsert["trip_mileage_duration_sec"] = $duration_sec;
										$datainsert["trip_mileage_trip_mileage"] = $x_mile;
										$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
										$datainsert["trip_mileage_location_start"] = $report['start_position']->display_name;
										$datainsert["trip_mileage_location_end"] = $report['end_position']->display_name;
										$datainsert["trip_mileage_geofence_start"] = $geofence_start;
										$datainsert["trip_mileage_geofence_end"] = $geofence_end;
										$datainsert["trip_mileage_coordinate_start"] = $report['start_coordinate'];
										$datainsert["trip_mileage_coordinate_end"] = $report['end_coordinate'];
										$datainsert["trip_mileage_door_start"] = $report['start_door'];
										$datainsert["trip_mileage_door_end"] = $report['end_door'];
										$datainsert["trip_mileage_lat"] = $report['start_latitude'];
										$datainsert["trip_mileage_lng"] = $report['start_longitude'];
										$datainsert["trip_mileage_totaldata"] = $trows;
										
									//edit flag engine ON , nol km, lebih dari 4 menit = 240 -> edit 1 menit = 30 detik //27-9-17 //10-12-17
									if($duration_sec > 240 ){
										$this->dbtrip = $this->load->database("operational_report",TRUE);
										$this->dbtrip->insert($dbtable,$datainsert);
										
										printf("OK");
									}
									
								}

                                    $i++;
								//}
                            }
                        }
						
                        unset($data);
            
                        printf("FINISH FOR VEHICLE ON : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
						}
						
						////---------------KONDISI OFF---------------///
						if ($trows > 0){
                        for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_latitude);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude;
                                    
									$on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle //hanya data off
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {
									//print_r(substr($rows[$i]->gps_info_io_port, 4, 1));exit();
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s",strtotime($rows[$i]->gps_time));$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude,$rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data off //kondisi tidak ada data ?
                        $i=1;
                        $new = "";
                        printf("WRITE DATA OFF : ");
                        foreach($data as $vehicle_no=>$val)
                        {
                            if($new != $vehicle_no)
                            {
                                $cumm = 0;
                                $trip_no = 1;
                            }
							
                            foreach($val as $no=>$report_off)
                            {
                                $mileage = $report_off['end_mileage']- $report_off['start_mileage'];
                               // if($mileage != 0) // edit 0 km engine off
                               // {
                                    $duration = get_time_difference($report_off['start_time'], $report_off['end_time']);
									
									$start_1 = dbmaketime($report_off['start_time']);
									$end_1 = dbmaketime($report_off['end_time']);
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
                                    $tm = round(($mileage/1000),2);
                                    $cumm += $tm;
                                    printf("|%s|",$i);
									
									$notrip = $trip_no++;
								 
                                    //mileage
                                    $xme = $tm;
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                            
                                    //cummulative
                                    $xcum = $cumm;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
									$geofence_start_off = $report_off['start_geofence_location'];
									$geofence_end_off = $report_off['end_geofence_location'];

									//edit flag engine OFF , nol km, lebih dari 10 menit
								if (isset($report_off['vehicle_name'])){
										unset($datainsert);
										$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
										$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
										$datainsert["trip_mileage_vehicle_name"] = $vehicle_name;
										$datainsert["trip_mileage_vehicle_type"] = $vehicle_type;
										$datainsert["trip_mileage_trip_no"] = $notrip;
										$datainsert["trip_mileage_engine"] = $report_off['engine'];
										$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report_off['start_time']));
										$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report_off['end_time']));
										$datainsert["trip_mileage_duration"] = $show;
										$datainsert["trip_mileage_duration_sec"] = $duration_sec;
										$datainsert["trip_mileage_trip_mileage"] = $x_mile;
										$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
										$datainsert["trip_mileage_location_start"] = $report_off['start_position']->display_name;
										$datainsert["trip_mileage_location_end"] = $report_off['end_position']->display_name;
										$datainsert["trip_mileage_geofence_start"] = $geofence_start_off;
										$datainsert["trip_mileage_geofence_end"] = $geofence_end_off;
										$datainsert["trip_mileage_coordinate_start"] = $report_off['start_coordinate'];
										$datainsert["trip_mileage_coordinate_end"] = $report_off['end_coordinate'];
										$datainsert["trip_mileage_door_start"] = $report_off['start_door'];
										$datainsert["trip_mileage_door_end"] = $report_off['end_door'];
										$datainsert["trip_mileage_lat"] = $report['start_latitude'];
										$datainsert["trip_mileage_lng"] = $report['start_longitude'];
										$datainsert["trip_mileage_totaldata"] = $trows;
										
									//edit flag engine OFF , nol km, lebih dari 4 menit = 240 -> edit 1 menit = 30 detik //27-9-17 //10-12-17
									if($duration_sec > 240 ){
										$this->dbtrip = $this->load->database("operational_report",TRUE);
										$this->dbtrip->insert($dbtable,$datainsert);
										printf("OK");
									}
								}
                                    $i++;
                             // }
                            }
                        }
                        printf("FINISH FOR VEHICLE OFF : %s \r\n",$rowvehicle[$x]->vehicle_device);
						printf("============================================ \r\n");
						}
						
					}else{
						printf("TO MANY : %s \r\n", $trows);
						printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
					}
						printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_new",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", $trows);
						}
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
                       
                        printf("============================================ \r\n");

                    }
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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA OPERASIONAL OTHER DONE %s\r\n",$finish_time);
	
	if($total_process != 0){
		//Send Email
		$cron_name = $cron_username." - "."OPERATIONAL REPORT OTHER";
		$this->dbtrip = $this->load->database("operational_report",TRUE);
        $this->dbtrip->select("trip_mileage_id");
        $this->dbtrip->where("trip_mileage_start_time >=",$startdate);
        $this->dbtrip->where("trip_mileage_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
		//get telegram group by company
		$this->db = $this->load->database("default",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$startdate." to ".$enddate." \n".
					"Total Data: ".$total_data." \n".
					"Start: ".$startproses." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Cron Name : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_data."
End Data   : "."( ".$z." / ".$total_process." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.lacakmobil@yahoo.com";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
	}
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
	
		printf("Send Email OK");
        
    }
	
	function door($userid="",$startdate="", $enddate="")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO REPORT DOOR KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$user_fullname = "";
		
        $report_type = "door";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "door_";
        
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
		
		$this->db->order_by("vehicle_device", "asc");
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", $userid); // per user
		$this->db->where("vehicle_type", "T5DOOR");
		$this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
			$user_fullname = $rowvehicle[$x]->user_name;
            printf("PROSES VEHICLE %s : %s %s (%d/%d) \r\n",$user_fullname, $rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, ++$z, $total_process);
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
					
                    //cek apakah sudah pernah ada filenya
					$this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_user_id",$rowvehicle[$x]->vehicle_user_id);
					$this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
					$this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_new");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {

                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
						$this->dbhist->limit(5000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
						$this->dbhist2->limit(5000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
                
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
						if($trows < 10000){
							
							//start here
							$data = array(); // initialization variable
							$vehicle_device = "";
							$door = "";
					
							foreach($rows as $obj)
							{
								if($vehicle_device != $rowvehicle[$x]->vehicle_device)
								{
									$no=0;
									$no_data = 1;
								}
					
								if(substr($obj->gps_msg_ori, 79, 1) == 1) //Door Open
								{ 
									if($door != "OPEN") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OPEN']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OPEN']['end'] = $obj;
									}
									$no_data++;
									$door = "OPEN";
								}
					
								if(substr($obj->gps_msg_ori, 79, 1) == 0) //Door Close
								{ 
									if($door != "CLOSE") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['CLOSE']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['CLOSE']['end'] = $obj;
									}
						
									$no_data++;
									$door = "CLOSE";
								}
						
								$vehicle_device = $rowvehicle[$x]->vehicle_device;
							}//end loop foreach rows
							
							$i=1;
							$cummulative = 0; 
							printf("WRITE DATA : ");
							foreach($data as $vehicles=>$value_vehicles)
							{
								foreach($value_vehicles as $number=>$value_number)
								{
									foreach($value_number as $door=>$report)
									{
										if(!isset($report['end']))
										{
											$report['end'] = $report['start'];
										}
										
										$data_report[$vehicles][$number][$door]['start'] = $report['start'];
										$data_report[$vehicles][$number][$door]['end'] = $report['end'];
										$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
										
										$start_1 = dbmaketime($report['start']->gps_time);
										$end_1 = dbmaketime($report['end']->gps_time);
										
										$duration_sec = $end_1 - $start_1;
						   
										$show_duration = "";
										if($duration[0]!=0)
										{
											$show_duration .= $duration[0] ." Day ";
										}
							
										if($duration[1]!=0)
										{
											$show_duration .= $duration[1] ." Hour ";
										}
							
										if($duration[2]!=0)
										{
											$show_duration .= $duration[2] ." Min";
										}
							
										if($show_duration == "")
										{
											$show_duration .= "0 Min";
										}
										//coordinate start / end
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										unset($datainsert);
										$data_report[$vehicles][$number][$door]['duration'] = $show_duration;
										$mileage = round(($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000, 2);
										$data_report[$vehicles][$number][$door]['mileage'] = round($mileage, 2);
							
										$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_start'] = $location_start;
										
										$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $rowvehicle[$x]->vehicle_user_id);
										$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $rowvehicle[$x]->vehicle_user_id);
										
									
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
										$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;
							
										$cummulative += $data_report[$vehicles][$number][$door]['mileage'];
										//print_r($data_report[$vehicles][$number][$door]['location_start']->display_name);exit();
							
										printf("|%s|",$i);
								
										//mileage
										$xme = $data_report[$vehicles][$number][$door]['mileage'];
										$xxme = explode(".",$xme);
										if (isset($xxme[1]))
										{
											$xsub = substr($xxme[1],0,2); 
											$x_mile = $xxme[0].".".$xsub;     
										}
										else
										{
											$x_mile = $xxme[0];
										}
								
										//cummulative mileage
										$xcum = $cummulative;
										$xxcum = explode(".",$xcum);
										if (isset($xxcum[1]))
										{
											$xcumsub = substr($xxcum[1],0,2); 
											$x_cum = $xxcum[0].".".$xcumsub;     
										}
										else
										{
											$x_cum = $xxcum[0];
										}
							
										$datainsert["door_location_start"] = $data_report[$vehicles][$number][$door]['location_start']->display_name;
										$datainsert["door_location_end"] = $data_report[$vehicles][$number][$door]['location_end']->display_name;
										$datainsert["door_geofence_start"] = $data_report[$vehicles][$number][$door]['geofence_start'];
										$datainsert["door_geofence_end"] = $data_report[$vehicles][$number][$door]['geofence_end'];
										$datainsert["door_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["door_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["door_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["door_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["door_vehicle_type"] = $rowvehicle[$x]->vehicle_type;
										$datainsert["door_status"] = $door;
										$datainsert["door_duration_sec"] = $duration_sec;
										$datainsert["door_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["door_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["door_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["door_mileage"] = $x_mile;
										$datainsert["door_cumm_mileage"] = $x_cum;
										$datainsert["door_coordinate_start"] = $coordinate_start;
										$datainsert["door_coordinate_end"] = $coordinate_end;
										$datainsert["door_totaldata"] = $trows;
										$this->dbreport = $this->load->database("operational_report",TRUE);
										$this->dbreport->insert($dbtable,$datainsert);
										//edit lebih besar dari 5 detik cetak door open
										
										/*if($door == "OPEN"){
											if($duration_sec > 0 ){
												
												printf("OK ");
											}
										}else{
											$this->dbreport->insert($dbtable,$datainsert);
											printf("OK ");
										} */
										
										$i++;
									}
								}
							}

						}else{
							printf("TO MANY : %s \r\n", $trows);
							printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
						}
							printf("INSERT TO DATABASE TRANSPORTER \r\n");
							$data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
							$data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
							$data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
							$data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
							$data_insert["autoreport_data_startdate"] = $startdate;
							$data_insert["autoreport_data_enddate"] = $enddate;
							$data_insert["autoreport_type"] = $report_type;
							$data_insert["autoreport_process_date"] = $process_date;
							$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
							
							if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_new",$data_insert);
								printf("INSERT CONFIG OK \r\n");
							}else{
								printf("SKIP DATA : %s \r\n", $trows);
							}
				
							printf("DELETE CACHE HISTORY \r\n");
							$this->dbhist->cache_delete_all();
							$this->dbtrans->cache_delete_all();
							unset($data);
				
							printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
							printf("============================================ \r\n");

                    }
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
        
		
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DOOR STATUS DONE %s\r\n",$finish_time);
		
		if($total_process != 0){
			//Send Email
			$cron_name = "(".$user_fullname.")"." - DOOR REPORT";
			$this->dbtrip = $this->load->database("operational_report",TRUE);
			$this->dbtrip->select("door_id");
			$this->dbtrip->where("door_start_time >=",$startdate);
			$this->dbtrip->where("door_end_time <=",$enddate);
			$qtrip = $this->dbtrip->get($dbtable);
			$rtrip = $qtrip->result();
			$total_data = count($rtrip);
			
			//get telegram group by company
			$this->db = $this->load->database("default",TRUE);
			$this->db->select("company_id,company_telegram_cron");
			$this->db->where("company_id",$company_username);
			$qcompany = $this->db->get("company");
			$rcompany = $qcompany->row();
			if(count($rcompany)>0){
				$telegram_group = $rcompany->company_telegram_cron;
			}else{
				$telegram_group = 0;
			}
			
			$message =  urlencode(
						"".$cron_name." \n".
						"Periode: ".$startdate." to ".$enddate." \n".
						"Total Data: ".$total_data." \n".
						"Start: ".$startproses." \n".
						"Finish: ".$finish_time." \n"
						);
						
			$sendtelegram = $this->telegram_direct($telegram_group,$message);
			printf("===SENT TELEGRAM OK\r\n");
			
			unset($mail);
			$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
			$mail['message'] = 
	"
	Cron Report Status :

	Cron Name : ".$cron_name." : ".$startdate." to ".$enddate."
	Start Cron : ".$start_time."
	End Cron   : ".$finish_time."
	Total Data : ".$total_data."
	End Data   : "."( ".$z." / ".$total_process." )"."
	Status     : Finish

	Thanks

	";
			$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
			$mail['bcc'] = "report.lacakmobil@yahoo.com";
			$mail['sender'] = "cron@lacak-mobil.com";
			lacakmobilmail($mail);
			
			$this->dbtrip->close();
			$this->dbtrip->cache_delete_all();
				
			printf("Send Email OK");
			
		}
        
    }
	
	function door_other($userid="", $startdate="", $enddate="")
    {
		ini_set('memory_limit', '3G');
        printf("PROSES AUTO REPORT DOOR OTHERS KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$sortir = "asc";
		
        $report_type = "door";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "door_";
        
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
        
		$sdate = $startdate;
        $edate = $enddate;
		
        $z =0;
		$type_list = array("TK315DOOR","TK315DOOR_NEW");
		
		$this->db->order_by("vehicle_id", $sortir);
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_user_id",$userid);
		$this->db->where_in("vehicle_type", $type_list);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle); 
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, ++$z, $total_process);
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
					
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_new");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {

                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
						$this->dbhist->limit(6000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
						$this->dbhist2->limit(6000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
                
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
						if($trows < 10000){
							
							//start here
							$data = array(); // initialization variable
							$vehicle_device = "";
							$door = "";
					
							foreach($rows as $obj)
							{
								if($vehicle_device != $rowvehicle[$x]->vehicle_device)
								{
									$no=0;
									$no_data = 1;
								}
								
								$door_msg = $this->getDoorStatus($obj->gps_msg_ori);
								if($door_msg == "1"){
									if($door != "OPEN") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OPEN']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OPEN']['end'] = $obj;
									}
									$no_data++;
									$door = "OPEN";
								}else{
									if($door != "CLOSE") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['CLOSE']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['CLOSE']['end'] = $obj;
									}
									$no_data++;
									$door = "CLOSE";
								}
								/*if(substr($obj->gps_msg_ori, 79, 1) == 1) //Door Open
								{ 
									if($door != "OPEN") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OPEN']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OPEN']['end'] = $obj;
									}
									$no_data++;
									$door = "OPEN";
								} */
					
						
								$vehicle_device = $rowvehicle[$x]->vehicle_device;
							}//end loop foreach rows
							
							$i=1;
							$cummulative = 0; 
							printf("WRITE DATA : ");
							foreach($data as $vehicles=>$value_vehicles)
							{
								foreach($value_vehicles as $number=>$value_number)
								{
									foreach($value_number as $door=>$report)
									{
										if(!isset($report['end']))
										{
											$report['end'] = $report['start'];
										}
										
										$data_report[$vehicles][$number][$door]['start'] = $report['start'];
										$data_report[$vehicles][$number][$door]['end'] = $report['end'];
										$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
										
										$start_1 = dbmaketime($report['start']->gps_time);
										$end_1 = dbmaketime($report['end']->gps_time);
										
										$duration_sec = $end_1 - $start_1;
						   
										$show_duration = "";
										if($duration[0]!=0)
										{
											$show_duration .= $duration[0] ." Day ";
										}
							
										if($duration[1]!=0)
										{
											$show_duration .= $duration[1] ." Hour ";
										}
							
										if($duration[2]!=0)
										{
											$show_duration .= $duration[2] ." Min";
										}
							
										if($show_duration == "")
										{
											$show_duration .= "0 Min";
										}
										//coordinate start / end
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										unset($datainsert);
										$data_report[$vehicles][$number][$door]['duration'] = $show_duration;
										$mileage = round(($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000, 2);
										$data_report[$vehicles][$number][$door]['mileage'] = round($mileage, 2);
							
										$location_start = $this->getPosition_other($report['start']->gps_longitude, $report['start']->gps_latitude);
										$data_report[$vehicles][$number][$door]['location_start'] = $location_start;
							
										$geofence_start = $this->getGeofence_location_other($report['start']->gps_longitude, $report['start']->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition_other($report['end']->gps_longitude, $report['end']->gps_latitude);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_other($report['end']->gps_longitude, $report['end']->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;
							
										$cummulative += $data_report[$vehicles][$number][$door]['mileage'];
										
										printf("|%s|",$i);
								
										//mileage
										$xme = $data_report[$vehicles][$number][$door]['mileage'];
										$xxme = explode(".",$xme);
										if (isset($xxme[1]))
										{
											$xsub = substr($xxme[1],0,2); 
											$x_mile = $xxme[0].".".$xsub;     
										}
										else
										{
											$x_mile = $xxme[0];
										}
								
										//cummulative mileage
										$xcum = $cummulative;
										$xxcum = explode(".",$xcum);
										if (isset($xxcum[1]))
										{
											$xcumsub = substr($xxcum[1],0,2); 
											$x_cum = $xxcum[0].".".$xcumsub;     
										}
										else
										{
											$x_cum = $xxcum[0];
										}
							
										$datainsert["door_location_start"] = $data_report[$vehicles][$number][$door]['location_start']->display_name;
										$datainsert["door_location_end"] = $data_report[$vehicles][$number][$door]['location_end']->display_name;
										$datainsert["door_geofence_start"] = $data_report[$vehicles][$number][$door]['geofence_start'];
										$datainsert["door_geofence_end"] = $data_report[$vehicles][$number][$door]['geofence_end'];
										$datainsert["door_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["door_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["door_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["door_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["door_vehicle_type"] = $rowvehicle[$x]->vehicle_type;
										$datainsert["door_status"] = $door;
										$datainsert["door_duration_sec"] = $duration_sec;
										$datainsert["door_start_time"] = date("Y-m-d H:i:s", strtotime($report['start']->gps_time));
										$datainsert["door_end_time"] = date("Y-m-d H:i:s", strtotime($report['end']->gps_time));
										$datainsert["door_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["door_mileage"] = $x_mile;
										$datainsert["door_cumm_mileage"] = $x_cum;
										$datainsert["door_coordinate_start"] = $coordinate_start;
										$datainsert["door_coordinate_end"] = $coordinate_end;
										$this->dbreport = $this->load->database("operational_report",TRUE);
										
										//all status insert > 180 s
										if($duration_sec > 180 ){
											$this->dbreport->insert($dbtable,$datainsert);
											printf("OK ");
										}
										
										$i++;
									}
								}
							}

						}else{
							printf("TO MANY : %s \r\n", $trows);
							printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
						}
							printf("INSERT TO DATABASE TRANSPORTER \r\n");
							$data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
							$data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
							$data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
							$data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
							$data_insert["autoreport_data_startdate"] = $startdate;
							$data_insert["autoreport_data_enddate"] = $enddate;
							$data_insert["autoreport_type"] = $report_type;
							$data_insert["autoreport_process_date"] = $process_date;
							$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
							
							if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_new",$data_insert);
								printf("INSERT CONFIG OK \r\n");
							}else{
								printf("SKIP DATA : %s \r\n", $trows);
							}
				
							printf("DELETE CACHE HISTORY \r\n");
							$this->dbhist->cache_delete_all();
							$this->dbtrans->cache_delete_all();
							unset($data);
				
							printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
							printf("============================================ \r\n");

                    }
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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DOOR STATUS DONE %s\r\n",$finish_time);
		
		if($total_process > 0){
			
			//Send Email
			$cron_name = $userid." - DOOR STATUS REPORT OTHER"." - ".$sortir."";
			$this->dbtrip = $this->load->database("operational_report",TRUE);
			$this->dbtrip->select("door_id");
			$this->dbtrip->where("door_start_time >=",$startdate);
			$this->dbtrip->where("door_end_time <=",$enddate);
			$qtrip = $this->dbtrip->get($dbtable);
			$rtrip = $qtrip->result();
			$total_data = count($rtrip);
			
			//get telegram group by company
			$this->db = $this->load->database("default",TRUE);
			$this->db->select("company_id,company_telegram_cron");
			$this->db->where("company_id",$company_username);
			$qcompany = $this->db->get("company");
			$rcompany = $qcompany->row();
			if(count($rcompany)>0){
				$telegram_group = $rcompany->company_telegram_cron;
			}else{
				$telegram_group = 0;
			}
			
			$message =  urlencode(
						"".$cron_name." \n".
						"Periode: ".$startdate." to ".$enddate." \n".
						"Total Data: ".$total_data." \n".
						"Start: ".$startproses." \n".
						"Finish: ".$finish_time." \n"
						);
						
			$sendtelegram = $this->telegram_direct($telegram_group,$message);
			printf("===SENT TELEGRAM OK\r\n");
			
			unset($mail);
			$mail['subject'] =  $cron_name." : ".$startdate." to ".$enddate;
			$mail['message'] = 
	"
	Cron Report Status :

	Cron Name : ".$cron_name." : ".$startdate." to ".$enddate."
	Start Cron : ".$start_time."
	End Cron   : ".$finish_time."
	Total Data : ".$total_data."
	End Data   : "."( ".$z." / ".$total_process." )"."
	Status     : Finish

	Thanks

	";
			$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
			$mail['bcc'] = "report.lacakmobil@yahoo.com,alfa_funky@yahoo.com";
			$mail['sender'] = "cron@lacak-mobil.com";
			lacakmobilmail($mail);
			$this->dbtrip->close();
			$this->dbtrip->cache_delete_all();
			printf("Send Email OK");
			
		}
		   
    }
	
	function door_x3($userid="", $startdate="", $enddate="")
    {
		ini_set('memory_limit', '3G');
        printf("PROSES AUTO REPORT DOOR X3 KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$sortir = "asc";
		
        $report_type = "door";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "door_";
        
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
        
		$sdate = $startdate;
        $edate = $enddate;
		
        $z =0;
		$type_list = array("X3_DOOR");
		
		$this->db->order_by("vehicle_id", $sortir);
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_user_id",$userid);
		$this->db->where_in("vehicle_type", $type_list);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, ++$z, $total_process);
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
					
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_new");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {

                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
						$this->dbhist->limit(6000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
						$this->dbhist2->limit(6000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
                
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
						if($trows < 10000){
							
							//start here
							$data = array(); // initialization variable
							$vehicle_device = "";
							$door = "";
					
							foreach($rows as $obj)
							{
								if($vehicle_device != $rowvehicle[$x]->vehicle_device)
								{
									$no=0;
									$no_data = 1;
								}
								
								$door_msg = $this->getDoorStatus_X3($obj->gps_cs);
								if($door_msg == "1"){
									if($door != "OPEN") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OPEN']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OPEN']['end'] = $obj;
									}
									$no_data++;
									$door = "OPEN";
								}else{
									if($door != "CLOSE") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['CLOSE']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['CLOSE']['end'] = $obj;
									}
									$no_data++;
									$door = "CLOSE";
								}
								$vehicle_device = $rowvehicle[$x]->vehicle_device;
							}//end loop foreach rows
							
							$i=1;
							$cummulative = 0; 
							printf("WRITE DATA : ");
							foreach($data as $vehicles=>$value_vehicles)
							{
								foreach($value_vehicles as $number=>$value_number)
								{
									foreach($value_number as $door=>$report)
									{
										if(!isset($report['end']))
										{
											$report['end'] = $report['start'];
										}
										
										$data_report[$vehicles][$number][$door]['start'] = $report['start'];
										$data_report[$vehicles][$number][$door]['end'] = $report['end'];
										$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
										
										$start_1 = dbmaketime($report['start']->gps_time);
										$end_1 = dbmaketime($report['end']->gps_time);
										
										$duration_sec = $end_1 - $start_1;
						   
										$show_duration = "";
										if($duration[0]!=0)
										{
											$show_duration .= $duration[0] ." Day ";
										}
							
										if($duration[1]!=0)
										{
											$show_duration .= $duration[1] ." Hour ";
										}
							
										if($duration[2]!=0)
										{
											$show_duration .= $duration[2] ." Min";
										}
							
										if($show_duration == "")
										{
											$show_duration .= "0 Min";
										}
										//coordinate start / end
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										unset($datainsert);
										$data_report[$vehicles][$number][$door]['duration'] = $show_duration;
										$mileage = round(($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000, 2);
										$data_report[$vehicles][$number][$door]['mileage'] = round($mileage, 2);
							
										$location_start = $this->getPosition_other($report['start']->gps_longitude, $report['start']->gps_latitude);
										$data_report[$vehicles][$number][$door]['location_start'] = $location_start;
							
										$geofence_start = $this->getGeofence_location_other($report['start']->gps_longitude, $report['start']->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition_other($report['end']->gps_longitude, $report['end']->gps_latitude);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_other($report['end']->gps_longitude, $report['end']->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;
							
										$cummulative += $data_report[$vehicles][$number][$door]['mileage'];
										
										printf("|%s|",$i);
								
										//mileage
										$xme = $data_report[$vehicles][$number][$door]['mileage'];
										$xxme = explode(".",$xme);
										if (isset($xxme[1]))
										{
											$xsub = substr($xxme[1],0,2); 
											$x_mile = $xxme[0].".".$xsub;     
										}
										else
										{
											$x_mile = $xxme[0];
										}
								
										//cummulative mileage
										$xcum = $cummulative;
										$xxcum = explode(".",$xcum);
										if (isset($xxcum[1]))
										{
											$xcumsub = substr($xxcum[1],0,2); 
											$x_cum = $xxcum[0].".".$xcumsub;     
										}
										else
										{
											$x_cum = $xxcum[0];
										}
							
										$datainsert["door_location_start"] = $data_report[$vehicles][$number][$door]['location_start']->display_name;
										$datainsert["door_location_end"] = $data_report[$vehicles][$number][$door]['location_end']->display_name;
										$datainsert["door_geofence_start"] = $data_report[$vehicles][$number][$door]['geofence_start'];
										$datainsert["door_geofence_end"] = $data_report[$vehicles][$number][$door]['geofence_end'];
										$datainsert["door_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["door_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["door_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["door_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["door_vehicle_type"] = $rowvehicle[$x]->vehicle_type;
										$datainsert["door_status"] = $door;
										$datainsert["door_duration_sec"] = $duration_sec;
										$datainsert["door_start_time"] = date("Y-m-d H:i:s", strtotime($report['start']->gps_time));
										$datainsert["door_end_time"] = date("Y-m-d H:i:s", strtotime($report['end']->gps_time));
										$datainsert["door_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["door_mileage"] = $x_mile;
										$datainsert["door_cumm_mileage"] = $x_cum;
										$datainsert["door_coordinate_start"] = $coordinate_start;
										$datainsert["door_coordinate_end"] = $coordinate_end;
										$this->dbreport = $this->load->database("operational_report",TRUE);
											
										//all status insert > 120 s
										if($duration_sec > 120 ){
											$this->dbreport->insert($dbtable,$datainsert);
											printf("OK ");
										}
										
										$i++;
									}
								}
							}

						}else{
							printf("TO MANY : %s \r\n", $trows);
							printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
						}
							printf("INSERT TO DATABASE TRANSPORTER \r\n");
							$data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
							$data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
							$data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
							$data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
							$data_insert["autoreport_data_startdate"] = $startdate;
							$data_insert["autoreport_data_enddate"] = $enddate;
							$data_insert["autoreport_type"] = $report_type;
							$data_insert["autoreport_process_date"] = $process_date;
							$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
							
							if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_new",$data_insert);
								printf("INSERT CONFIG OK \r\n");
							}else{
								printf("SKIP DATA : %s \r\n", $trows);
							}
				
							printf("DELETE CACHE HISTORY \r\n");
							$this->dbhist->cache_delete_all();
							$this->dbtrans->cache_delete_all();
							unset($data);
				
							printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
							printf("============================================ \r\n");

                    }
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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DOOR STATUS DONE %s\r\n",$finish_time);
		
		if($total_process > 0){
			
			//Send Email
			$cron_name = $userid." - DOOR STATUS REPORT X3"." - ".$sortir."";
			$this->dbtrip = $this->load->database("operational_report",TRUE);
			$this->dbtrip->select("door_id");
			$this->dbtrip->where("door_start_time >=",$startdate);
			$this->dbtrip->where("door_end_time <=",$enddate);
			$qtrip = $this->dbtrip->get($dbtable);
			$rtrip = $qtrip->result();
			$total_data = count($rtrip);
			
			//get telegram group by company
			$this->db = $this->load->database("default",TRUE);
			$this->db->select("company_id,company_telegram_cron");
			$this->db->where("company_id",$company_username);
			$qcompany = $this->db->get("company");
			$rcompany = $qcompany->row();
			if(count($rcompany)>0){
				$telegram_group = $rcompany->company_telegram_cron;
			}else{
				$telegram_group = 0;
			}
			
			$message =  urlencode(
						"".$cron_name." \n".
						"Periode: ".$startdate." to ".$enddate." \n".
						"Total Data: ".$total_data." \n".
						"Start: ".$startproses." \n".
						"Finish: ".$finish_time." \n"
						);
						
			$sendtelegram = $this->telegram_direct($telegram_group,$message);
			printf("===SENT TELEGRAM OK\r\n");
			
			unset($mail);
			$mail['subject'] =  $cron_name." : ".$startdate." to ".$enddate;
			$mail['message'] = 
	"
	Cron Report Status :

	Cron Name : ".$cron_name." : ".$startdate." to ".$enddate."
	Start Cron : ".$start_time."
	End Cron   : ".$finish_time."
	Total Data : ".$total_data."
	End Data   : "."( ".$z." / ".$total_process." )"."
	Status     : Finish

	Thanks

	";
			$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
			$mail['bcc'] = "report.lacakmobil@yahoo.com,alfa_funky@yahoo.com";
			$mail['sender'] = "cron@lacak-mobil.com";
			lacakmobilmail($mail);
			$this->dbtrip->close();
			$this->dbtrip->cache_delete_all();
			printf("Send Email OK");
			
		}
		   
    }
	
	function pto($userid="",$startdate="", $enddate="")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO REPORT PTO KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$user_fullname = "";
		
        $report_type = "pto";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "pto_";
        
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
		
		$this->db->order_by("vehicle_device", "asc");
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", $userid); // per user
		$this->db->where("vehicle_type", "T5PTO");
		$this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
			$user_fullname = $rowvehicle[$x]->user_name;
            printf("PROSES VEHICLE %s : %s %s (%d/%d) \r\n",$user_fullname, $rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, ++$z, $total_process);
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
					
                    //cek apakah sudah pernah ada filenya
					$this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_user_id",$rowvehicle[$x]->vehicle_user_id);
					$this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
					$this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_new");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {

                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
						$this->dbhist->limit(6000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
						$this->dbhist2->limit(6000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
                
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
						if($trows < 10000){
							
							//start here
							$data = array(); // initialization variable
							$vehicle_device = "";
							$door = "";
					
							foreach($rows as $obj)
							{
								if($vehicle_device != $rowvehicle[$x]->vehicle_device)
								{
									$no=0;
									$no_data = 1;
								}
					
								if(substr($obj->gps_msg_ori, 79, 1) == 1) //PTO ON
								{ 
									if($door != "ON") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['end'] = $obj;
									}
									$no_data++;
									$door = "ON";
								}
					
								if(substr($obj->gps_msg_ori, 79, 1) == 0) //PTO OFF
								{ 
									if($door != "OFF") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['end'] = $obj;
									}
						
									$no_data++;
									$door = "OFF";
								}
						
								$vehicle_device = $rowvehicle[$x]->vehicle_device;
							}//end loop foreach rows
							
							$i=1;
							$cummulative = 0; 
							printf("WRITE DATA : ");
							foreach($data as $vehicles=>$value_vehicles)
							{
								foreach($value_vehicles as $number=>$value_number)
								{
									foreach($value_number as $door=>$report)
									{
										if(!isset($report['end']))
										{
											$report['end'] = $report['start'];
										}
										
										$data_report[$vehicles][$number][$door]['start'] = $report['start'];
										$data_report[$vehicles][$number][$door]['end'] = $report['end'];
										$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
										
										$start_1 = dbmaketime($report['start']->gps_time);
										$end_1 = dbmaketime($report['end']->gps_time);
										
										$duration_sec = $end_1 - $start_1;
						   
										$show_duration = "";
										if($duration[0]!=0)
										{
											$show_duration .= $duration[0] ." Day ";
										}
							
										if($duration[1]!=0)
										{
											$show_duration .= $duration[1] ." Hour ";
										}
							
										if($duration[2]!=0)
										{
											$show_duration .= $duration[2] ." Min";
										}
							
										if($show_duration == "")
										{
											$show_duration .= "0 Min";
										}
										//coordinate start / end
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										unset($datainsert);
										$data_report[$vehicles][$number][$door]['duration'] = $show_duration;
										$mileage = round(($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000, 2);
										$data_report[$vehicles][$number][$door]['mileage'] = round($mileage, 2);
							
										$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_start'] = $location_start;
										
										$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $rowvehicle[$x]->vehicle_user_id);
										$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $rowvehicle[$x]->vehicle_user_id);
										
									
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
										$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;
							
										$cummulative += $data_report[$vehicles][$number][$door]['mileage'];
										//print_r($data_report[$vehicles][$number][$door]['location_start']->display_name);exit();
							
										printf("|%s|",$i);
								
										//mileage
										$xme = $data_report[$vehicles][$number][$door]['mileage'];
										$xxme = explode(".",$xme);
										if (isset($xxme[1]))
										{
											$xsub = substr($xxme[1],0,2); 
											$x_mile = $xxme[0].".".$xsub;     
										}
										else
										{
											$x_mile = $xxme[0];
										}
								
										//cummulative mileage
										$xcum = $cummulative;
										$xxcum = explode(".",$xcum);
										if (isset($xxcum[1]))
										{
											$xcumsub = substr($xxcum[1],0,2); 
											$x_cum = $xxcum[0].".".$xcumsub;     
										}
										else
										{
											$x_cum = $xxcum[0];
										}
							
										$datainsert["pto_location_start"] = $data_report[$vehicles][$number][$door]['location_start']->display_name;
										$datainsert["pto_location_end"] = $data_report[$vehicles][$number][$door]['location_end']->display_name;
										$datainsert["pto_geofence_start"] = $data_report[$vehicles][$number][$door]['geofence_start'];
										$datainsert["pto_geofence_end"] = $data_report[$vehicles][$number][$door]['geofence_end'];
										$datainsert["pto_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["pto_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["pto_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["pto_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["pto_vehicle_type"] = $rowvehicle[$x]->vehicle_type;
										$datainsert["pto_user_id"] = $rowvehicle[$x]->user_id;
										$datainsert["pto_status"] = $door;
										$datainsert["pto_duration_sec"] = $duration_sec;
										$datainsert["pto_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["pto_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["pto_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["pto_mileage"] = $x_mile;
										$datainsert["pto_cumm_mileage"] = $x_cum;
										$datainsert["pto_coordinate_start"] = $coordinate_start;
										$datainsert["pto_coordinate_end"] = $coordinate_end;
										$datainsert["pto_totaldata"] = $trows;
										$this->dbreport = $this->load->database("operational_report",TRUE);
										$this->dbreport->insert($dbtable,$datainsert);
										/*if($door == "ON"){
											if($duration_sec > 0 ){
												
												printf("OK ");
											}
										}else{
											$this->dbreport->insert($dbtable,$datainsert);
											printf("OK ");
										} */
										
										$i++;
									}
								}
							}

						}else{
							printf("TO MANY : %s \r\n", $trows);
							printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
						}
							printf("INSERT TO DATABASE TRANSPORTER \r\n");
							$data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
							$data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
							$data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
							$data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
							$data_insert["autoreport_data_startdate"] = $startdate;
							$data_insert["autoreport_data_enddate"] = $enddate;
							$data_insert["autoreport_type"] = $report_type;
							$data_insert["autoreport_process_date"] = $process_date;
							$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
							
							if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_new",$data_insert);
								printf("INSERT CONFIG OK \r\n");
							}else{
								printf("SKIP DATA : %s \r\n", $trows);
							}
				
							printf("DELETE CACHE HISTORY \r\n");
							$this->dbhist->cache_delete_all();
							$this->dbtrans->cache_delete_all();
							unset($data);
				
							printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
							printf("============================================ \r\n");

                    }
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
        
		
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT PTO STATUS DONE %s\r\n",$finish_time);
		
		if($total_process != 0){
			//Send Email
			$cron_name = "(".$user_fullname.")"." - PTO REPORT";
			$this->dbtrip = $this->load->database("operational_report",TRUE);
			$this->dbtrip->select("pto_id");
			$this->dbtrip->where("pto_start_time >=",$startdate);
			$this->dbtrip->where("pto_end_time <=",$enddate);
			$qtrip = $this->dbtrip->get($dbtable);
			$rtrip = $qtrip->result();
			$total_data = count($rtrip);
			
			//get telegram group by company
			$this->db = $this->load->database("default",TRUE);
			$this->db->select("company_id,company_telegram_cron");
			$this->db->where("company_id",$company_username);
			$qcompany = $this->db->get("company");
			$rcompany = $qcompany->row();
			if(count($rcompany)>0){
				$telegram_group = $rcompany->company_telegram_cron;
			}else{
				$telegram_group = 0;
			}
			
			$message =  urlencode(
						"".$cron_name." \n".
						"Periode: ".$startdate." to ".$enddate." \n".
						"Total Data: ".$total_data." \n".
						"Start: ".$startproses." \n".
						"Finish: ".$finish_time." \n"
						);
						
			$sendtelegram = $this->telegram_direct($telegram_group,$message);
			printf("===SENT TELEGRAM OK\r\n");
			
			unset($mail);
			$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
			$mail['message'] = 
	"
	Cron Report Status :

	Cron Name : ".$cron_name." : ".$startdate." to ".$enddate."
	Start Cron : ".$start_time."
	End Cron   : ".$finish_time."
	Total Data : ".$total_data."
	End Data   : "."( ".$z." / ".$total_process." )"."
	Status     : Finish

	Thanks

	";
			$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
			$mail['bcc'] = "report.lacakmobil@yahoo.com";
			$mail['sender'] = "cron@lacak-mobil.com";
			lacakmobilmail($mail);
			
			$this->dbtrip->close();
			$this->dbtrip->cache_delete_all();
				
			printf("Send Email OK");
			
		}
        
    }
	
	function pto_other($userid="", $startdate="", $enddate="")
    {
		ini_set('memory_limit', '3G');
        printf("PROSES AUTO REPORT PTO OTHER KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$sortir = "asc";
		
        $report_type = "pto";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "pto_";
        
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
        
		$sdate = $startdate;
        $edate = $enddate;
		
        $z =0;
		$type_list = array("X3_PTO");
		
		$this->db->order_by("vehicle_id", $sortir);
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_user_id",$userid);
		$this->db->where_in("vehicle_type", $type_list);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, ++$z, $total_process);
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
					
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_new");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {

                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
						$this->dbhist->limit(6000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
						$this->dbhist2->limit(6000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
                
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
						if($trows < 10000){
							
							//start here
							$data = array(); // initialization variable
							$vehicle_device = "";
							$door = "";
					
							foreach($rows as $obj)
							{
								if($vehicle_device != $rowvehicle[$x]->vehicle_device)
								{
									$no=0;
									$no_data = 1;
								}
								
								$door_msg = $this->getDoorStatus_X3($obj->gps_cs);
								if($door_msg == "1"){
									if($door != "ON") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['end'] = $obj;
									}
									$no_data++;
									$door = "ON";
								}else{
									if($door != "OFF") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['end'] = $obj;
									}
									$no_data++;
									$door = "OFF";
								}
								$vehicle_device = $rowvehicle[$x]->vehicle_device;
							}//end loop foreach rows
							
							$i=1;
							$cummulative = 0; 
							printf("WRITE DATA : ");
							foreach($data as $vehicles=>$value_vehicles)
							{
								foreach($value_vehicles as $number=>$value_number)
								{
									foreach($value_number as $door=>$report)
									{
										if(!isset($report['end']))
										{
											$report['end'] = $report['start'];
										}
										
										$data_report[$vehicles][$number][$door]['start'] = $report['start'];
										$data_report[$vehicles][$number][$door]['end'] = $report['end'];
										$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
										
										$start_1 = dbmaketime($report['start']->gps_time);
										$end_1 = dbmaketime($report['end']->gps_time);
										
										$duration_sec = $end_1 - $start_1;
						   
										$show_duration = "";
										if($duration[0]!=0)
										{
											$show_duration .= $duration[0] ." Day ";
										}
							
										if($duration[1]!=0)
										{
											$show_duration .= $duration[1] ." Hour ";
										}
							
										if($duration[2]!=0)
										{
											$show_duration .= $duration[2] ." Min";
										}
							
										if($show_duration == "")
										{
											$show_duration .= "0 Min";
										}
										//coordinate start / end
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										unset($datainsert);
										$data_report[$vehicles][$number][$door]['duration'] = $show_duration;
										$mileage = round(($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000, 2);
										$data_report[$vehicles][$number][$door]['mileage'] = round($mileage, 2);
							
										$location_start = $this->getPosition_other($report['start']->gps_longitude, $report['start']->gps_latitude);
										$data_report[$vehicles][$number][$door]['location_start'] = $location_start;
							
										$geofence_start = $this->getGeofence_location_other($report['start']->gps_longitude, $report['start']->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition_other($report['end']->gps_longitude, $report['end']->gps_latitude);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_other($report['end']->gps_longitude, $report['end']->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;
							
										$cummulative += $data_report[$vehicles][$number][$door]['mileage'];
										
										printf("|%s|",$i);
								
										//mileage
										$xme = $data_report[$vehicles][$number][$door]['mileage'];
										$xxme = explode(".",$xme);
										if (isset($xxme[1]))
										{
											$xsub = substr($xxme[1],0,2); 
											$x_mile = $xxme[0].".".$xsub;     
										}
										else
										{
											$x_mile = $xxme[0];
										}
								
										//cummulative mileage
										$xcum = $cummulative;
										$xxcum = explode(".",$xcum);
										if (isset($xxcum[1]))
										{
											$xcumsub = substr($xxcum[1],0,2); 
											$x_cum = $xxcum[0].".".$xcumsub;     
										}
										else
										{
											$x_cum = $xxcum[0];
										}
							
										$datainsert["pto_location_start"] = $data_report[$vehicles][$number][$door]['location_start']->display_name;
										$datainsert["pto_location_end"] = $data_report[$vehicles][$number][$door]['location_end']->display_name;
										$datainsert["pto_geofence_start"] = $data_report[$vehicles][$number][$door]['geofence_start'];
										$datainsert["pto_geofence_end"] = $data_report[$vehicles][$number][$door]['geofence_end'];
										$datainsert["pto_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["pto_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["pto_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["pto_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["pto_vehicle_type"] = $rowvehicle[$x]->vehicle_type;
										$datainsert["pto_user_id"] = $rowvehicle[$x]->user_id;
										$datainsert["pto_status"] = $door;
										$datainsert["pto_duration_sec"] = $duration_sec;
										$datainsert["pto_start_time"] = date("Y-m-d H:i:s", strtotime($report['start']->gps_time));
										$datainsert["pto_end_time"] = date("Y-m-d H:i:s", strtotime($report['end']->gps_time));
										$datainsert["pto_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["pto_mileage"] = $x_mile;
										$datainsert["pto_cumm_mileage"] = $x_cum;
										$datainsert["pto_coordinate_start"] = $coordinate_start;
										$datainsert["pto_coordinate_end"] = $coordinate_end;
										$datainsert["pto_totaldata"] = $trows;
										$this->dbreport = $this->load->database("operational_report",TRUE);
										$this->dbreport->insert($dbtable,$datainsert);
										printf("OK ");
										//all status insert > 120 s
										/*
										if($duration_sec > 120 ){
											$this->dbreport->insert($dbtable,$datainsert);
											printf("OK ");
										}
										*/
										
										$i++;
									}
								}
							}

						}else{
							printf("TO MANY : %s \r\n", $trows);
							printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
						}
							printf("INSERT TO DATABASE TRANSPORTER \r\n");
							$data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
							$data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
							$data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
							$data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
							$data_insert["autoreport_data_startdate"] = $startdate;
							$data_insert["autoreport_data_enddate"] = $enddate;
							$data_insert["autoreport_type"] = $report_type;
							$data_insert["autoreport_process_date"] = $process_date;
							$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
							
							if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_new",$data_insert);
								printf("INSERT CONFIG OK \r\n");
							}else{
								printf("SKIP DATA : %s \r\n", $trows);
							}
				
							printf("DELETE CACHE HISTORY \r\n");
							$this->dbhist->cache_delete_all();
							$this->dbtrans->cache_delete_all();
							unset($data);
				
							printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
							printf("============================================ \r\n");

                    }
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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT PTO OTHER STATUS DONE %s\r\n",$finish_time);
		
		if($total_process > 0){
			
			//Send Email
			$cron_name = $userid." - PTO OTHER "." - ".$sortir."";
			$this->dbtrip = $this->load->database("operational_report",TRUE);
			$this->dbtrip->select("pto_id");
			$this->dbtrip->where("pto_start_time >=",$startdate);
			$this->dbtrip->where("pto_end_time <=",$enddate);
			$qtrip = $this->dbtrip->get($dbtable);
			$rtrip = $qtrip->result();
			$total_data = count($rtrip);
			
			//get telegram group by company
			$this->db = $this->load->database("default",TRUE);
			$this->db->select("company_id,company_telegram_cron");
			$this->db->where("company_id",$company_username);
			$qcompany = $this->db->get("company");
			$rcompany = $qcompany->row();
			if(count($rcompany)>0){
				$telegram_group = $rcompany->company_telegram_cron;
			}else{
				$telegram_group = 0;
			}
			
			$message =  urlencode(
						"".$cron_name." \n".
						"Periode: ".$startdate." to ".$enddate." \n".
						"Total Data: ".$total_data." \n".
						"Start: ".$startproses." \n".
						"Finish: ".$finish_time." \n"
						);
						
			$sendtelegram = $this->telegram_direct($telegram_group,$message);
			printf("===SENT TELEGRAM OK\r\n");
			
			unset($mail);
			$mail['subject'] =  $cron_name." : ".$startdate." to ".$enddate;
			$mail['message'] = 
	"
	Cron Report Status :

	Cron Name : ".$cron_name." : ".$startdate." to ".$enddate."
	Start Cron : ".$start_time."
	End Cron   : ".$finish_time."
	Total Data : ".$total_data."
	End Data   : "."( ".$z." / ".$total_process." )"."
	Status     : Finish

	Thanks

	";
			$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
			$mail['bcc'] = "report.lacakmobil@yahoo.com,alfa_funky@yahoo.com";
			$mail['sender'] = "cron@lacak-mobil.com";
			lacakmobilmail($mail);
			$this->dbtrip->close();
			$this->dbtrip->cache_delete_all();
			printf("Send Email OK");
			
		}
		   
    }
	
	function pto_new($userid="",$startdate="", $enddate="")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO REPORT PTO NEW >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$user_fullname = "";
		
        $report_type = "pto";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "pto_";
        
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
		$type_list = array("GT08SPTO","GT08PTO","TK510PTO");
		
		$this->db->order_by("vehicle_device", "asc");
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", $userid); // per user
		$this->db->where_in("vehicle_type", $type_list);
		$this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
			$user_fullname = $rowvehicle[$x]->user_name;
            printf("PROSES VEHICLE %s : %s %s (%d/%d) \r\n",$user_fullname, $rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, ++$z, $total_process);
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
					
                    //cek apakah sudah pernah ada filenya
					$this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_user_id",$rowvehicle[$x]->vehicle_user_id);
					$this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
					$this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_new");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {

                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
						$this->dbhist->limit(6000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
						$this->dbhist2->limit(6000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
                
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
						if($trows < 10000){
							
							//start here
							$data = array(); // initialization variable
							$vehicle_device = "";
							$door = "";
					
							foreach($rows as $obj)
							{
								if($vehicle_device != $rowvehicle[$x]->vehicle_device)
								{
									$no=0;
									$no_data = 1;
								}
					
								$door_msg = $this->getDoorStatus_X3($obj->gps_cs);
								if($door_msg == "1")
								{ 
									if($door != "ON") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['end'] = $obj;
									}
									$no_data++;
									$door = "ON";
								}
								else
								{ 
									if($door != "OFF") 
									{
										$no++;
										$no_data = 1;
									}
						
									if($no == 0) $no++;
									if($no_data == 1)
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['start'] = $obj;
									}
									else
									{
										$data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['end'] = $obj;
									}
						
									$no_data++;
									$door = "OFF";
								}
						
								$vehicle_device = $rowvehicle[$x]->vehicle_device;
							}//end loop foreach rows
							
							$i=1;
							$cummulative = 0; 
							printf("WRITE DATA : ");
							foreach($data as $vehicles=>$value_vehicles)
							{
								foreach($value_vehicles as $number=>$value_number)
								{
									foreach($value_number as $door=>$report)
									{
										if(!isset($report['end']))
										{
											$report['end'] = $report['start'];
										}
										
										$data_report[$vehicles][$number][$door]['start'] = $report['start'];
										$data_report[$vehicles][$number][$door]['end'] = $report['end'];
										$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
										
										$start_1 = dbmaketime($report['start']->gps_time);
										$end_1 = dbmaketime($report['end']->gps_time);
										
										$duration_sec = $end_1 - $start_1;
						   
										$show_duration = "";
										if($duration[0]!=0)
										{
											$show_duration .= $duration[0] ." Day ";
										}
							
										if($duration[1]!=0)
										{
											$show_duration .= $duration[1] ." Hour ";
										}
							
										if($duration[2]!=0)
										{
											$show_duration .= $duration[2] ." Min";
										}
							
										if($show_duration == "")
										{
											$show_duration .= "0 Min";
										}
										//coordinate start / end
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										unset($datainsert);
										$data_report[$vehicles][$number][$door]['duration'] = $show_duration;
										$mileage = round(($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000, 2);
										$data_report[$vehicles][$number][$door]['mileage'] = round($mileage, 2);
							
										$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_start'] = $location_start;
										
										$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $rowvehicle[$x]->vehicle_user_id);
										$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $rowvehicle[$x]->vehicle_user_id);
										
									
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
										$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;
							
										$cummulative += $data_report[$vehicles][$number][$door]['mileage'];
										//print_r($data_report[$vehicles][$number][$door]['location_start']->display_name);exit();
							
										printf("|%s|",$i);
								
										//mileage
										$xme = $data_report[$vehicles][$number][$door]['mileage'];
										$xxme = explode(".",$xme);
										if (isset($xxme[1]))
										{
											$xsub = substr($xxme[1],0,2); 
											$x_mile = $xxme[0].".".$xsub;     
										}
										else
										{
											$x_mile = $xxme[0];
										}
								
										//cummulative mileage
										$xcum = $cummulative;
										$xxcum = explode(".",$xcum);
										if (isset($xxcum[1]))
										{
											$xcumsub = substr($xxcum[1],0,2); 
											$x_cum = $xxcum[0].".".$xcumsub;     
										}
										else
										{
											$x_cum = $xxcum[0];
										}
							
										$datainsert["pto_location_start"] = $data_report[$vehicles][$number][$door]['location_start']->display_name;
										$datainsert["pto_location_end"] = $data_report[$vehicles][$number][$door]['location_end']->display_name;
										$datainsert["pto_geofence_start"] = $data_report[$vehicles][$number][$door]['geofence_start'];
										$datainsert["pto_geofence_end"] = $data_report[$vehicles][$number][$door]['geofence_end'];
										$datainsert["pto_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["pto_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["pto_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["pto_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["pto_vehicle_type"] = $rowvehicle[$x]->vehicle_type;
										$datainsert["pto_user_id"] = $rowvehicle[$x]->user_id;
										$datainsert["pto_status"] = $door;
										$datainsert["pto_duration_sec"] = $duration_sec;
										$datainsert["pto_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["pto_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["pto_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["pto_mileage"] = $x_mile;
										$datainsert["pto_cumm_mileage"] = $x_cum;
										$datainsert["pto_coordinate_start"] = $coordinate_start;
										$datainsert["pto_coordinate_end"] = $coordinate_end;
										$datainsert["pto_totaldata"] = $trows;
										$this->dbreport = $this->load->database("operational_report",TRUE);
										$this->dbreport->insert($dbtable,$datainsert);
										/*if($door == "ON"){
											if($duration_sec > 0 ){
												
												printf("OK ");
											}
										}else{
											$this->dbreport->insert($dbtable,$datainsert);
											printf("OK ");
										} */
										
										$i++;
									}
								}
							}

						}else{
							printf("TO MANY : %s \r\n", $trows);
							printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
						}
							printf("INSERT TO DATABASE TRANSPORTER \r\n");
							$data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
							$data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
							$data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
							$data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
							$data_insert["autoreport_data_startdate"] = $startdate;
							$data_insert["autoreport_data_enddate"] = $enddate;
							$data_insert["autoreport_type"] = $report_type;
							$data_insert["autoreport_process_date"] = $process_date;
							$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
							
							if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_new",$data_insert);
								printf("INSERT CONFIG OK \r\n");
							}else{
								printf("SKIP DATA : %s \r\n", $trows);
							}
				
							printf("DELETE CACHE HISTORY \r\n");
							$this->dbhist->cache_delete_all();
							$this->dbtrans->cache_delete_all();
							unset($data);
				
							printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
							printf("============================================ \r\n");

                    }
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
        
		
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT PTO NEW DONE %s\r\n",$finish_time);
		
		if($total_process != 0){
			//Send Email
			$cron_name = "(".$user_fullname.")"." - PTO NEW REPORT";
			$this->dbtrip = $this->load->database("operational_report",TRUE);
			$this->dbtrip->select("pto_id");
			$this->dbtrip->where("pto_start_time >=",$startdate);
			$this->dbtrip->where("pto_end_time <=",$enddate);
			$qtrip = $this->dbtrip->get($dbtable);
			$rtrip = $qtrip->result();
			$total_data = count($rtrip);
			
			//get telegram group by company
			$this->db = $this->load->database("default",TRUE);
			$this->db->select("company_id,company_telegram_cron");
			$this->db->where("company_id",$company_username);
			$qcompany = $this->db->get("company");
			$rcompany = $qcompany->row();
			if(count($rcompany)>0){
				$telegram_group = $rcompany->company_telegram_cron;
			}else{
				$telegram_group = 0;
			}
			
			$message =  urlencode(
						"".$cron_name." \n".
						"Periode: ".$startdate." to ".$enddate." \n".
						"Total Data: ".$total_data." \n".
						"Start: ".$startproses." \n".
						"Finish: ".$finish_time." \n"
						);
						
			$sendtelegram = $this->telegram_direct($telegram_group,$message);
			printf("===SENT TELEGRAM OK\r\n");
			
			unset($mail);
			$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
			$mail['message'] = 
	"
	Cron Report Status :

	Cron Name : ".$cron_name." : ".$startdate." to ".$enddate."
	Start Cron : ".$start_time."
	End Cron   : ".$finish_time."
	Total Data : ".$total_data."
	End Data   : "."( ".$z." / ".$total_process." )"."
	Status     : Finish

	Thanks

	";
			$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
			$mail['bcc'] = "report.lacakmobil@yahoo.com";
			$mail['sender'] = "cron@lacak-mobil.com";
			lacakmobilmail($mail);
			
			$this->dbtrip->close();
			$this->dbtrip->cache_delete_all();
				
			printf("Send Email OK");
			
		}
        
    }
	
	function all_report($userid="", $orderby="", $startdate = "", $enddate = "")
	{
		$this->operational($userid, $orderby, $startdate, $enddate);//T5
		$this->operational_other($userid, $orderby, $startdate, $enddate);//CONCOX
		$this->door($userid, $startdate, $enddate);
		$this->door_other($userid, $startdate, $enddate);
		$this->door_x3($userid, $startdate, $enddate);
	}
	
	function all_pto($userid="", $orderby="", $startdate = "", $enddate = "")
	{
		$this->pto($userid, $startdate, $enddate);
		$this->pto_other($userid, $startdate, $enddate);
		$this->pto_new($userid, $startdate, $enddate);
	}
	
	function getlist_coordinate($userid="", $startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO GET KOORDINAT FROM OPERASIONAL >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "operasional";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "operasional_";
		$configdb = "operational_report";
        
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
        
		$this->db->order_by("vehicle_device", "asc");
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_not_in("vehicle_type", $this->config->item('vehicle_others'));
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		$z = 0;
		
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, $dbtable, ++$z, $total_process);
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
					
                    $this->dbreport = $this->load->database($configdb,true); 
					$this->dbreport->select("trip_mileage_id,trip_mileage_vehicle_id,trip_mileage_start_time,trip_mileage_end_time");
                    $this->dbreport->where("trip_mileage_vehicle_id",$rowvehicle[$x]->vehicle_device);
					$this->dbreport->where("trip_mileage_engine",1); //only engine ON
					$this->dbreport->where("trip_mileage_start_time >=",$startdate);
					$this->dbreport->where("trip_mileage_start_time <=",$enddate);
                    $qreport = $this->dbreport->get($dbtable);
                    $rowsreport = $qreport->result();
					$totalrowsreport = count($rowsreport);
						
					if ($totalrowsreport > 0){
                        for($i=0;$i<$totalrowsreport;$i++)
                        {
							//printf("ID Report : %s \r\n",$rowsreport[$i]->trip_mileage_id);
							$data_array = "";
							$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($rowsreport[$i]->trip_mileage_start_time)));
							$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($rowsreport[$i]->trip_mileage_end_time)));
							
							$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
							$this->dbhist->select("gps_time,gps_latitude_real,gps_longitude_real");
							$this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist->where("gps_time >=", $sdate);
							$this->dbhist->where("gps_time <=", $edate);    
							$this->dbhist->order_by("gps_time","asc");
							$this->dbhist->limit(1000);
							$this->dbhist->from($table);
							$q = $this->dbhist->get();
							$rows1 = $q->result();
					  
							$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist2->select("gps_time,gps_latitude_real,gps_longitude_real");							
							$this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist2->where("gps_time >=", $sdate);
							$this->dbhist2->where("gps_time <=", $edate);  							
							$this->dbhist2->order_by("gps_time","asc");
							$this->dbhist2->limit(1000);
							$this->dbhist2->from($tablehist);
							$q2 = $this->dbhist2->get();
							$rows2 = $q2->result();
							
							$rows = array_merge($rows1, $rows2); //limit data rows = 10000
							$trows = count($rows);
							$data_array[$i] = json_encode($rows);
							
							//update to db report
							unset($data);
							$data["trip_mileage_coordinate_list"] = $data_array[$i];
							$data["trip_mileage_dbtable"] = $dbtable;
							
							
							$this->dbreport->where("trip_mileage_vehicle_id",$rowvehicle[$x]->vehicle_device);
							$this->dbreport->where("trip_mileage_engine",1); //only engine ON
							$this->dbreport->where("trip_mileage_start_time >=",$rowsreport[$i]->trip_mileage_start_time);
							$this->dbreport->where("trip_mileage_start_time <=",$rowsreport[$i]->trip_mileage_end_time);
							$this->dbreport->update($dbtable,$data);
							
							printf("ID %s OKE \r\n ",$rowsreport[$i]->trip_mileage_id);
						}
					}

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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT GET KOORDINAT LIST DONE %s\r\n",$finish_time);
		
    }
	
	function getlist_coordinate_other($userid="", $startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO GET KOORDINAT OTHER FROM OPERASIONAL >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "operasional";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "operasional_";
		$configdb = "operational_report";
        
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
        
		$this->db->order_by("vehicle_device", "asc");
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_type <>", "T5");
		$this->db->where("vehicle_type <>", "T5SILVER");
		$this->db->where("vehicle_type <>", "T8");
		$this->db->where("vehicle_type <>", "T8_2");
		$this->db->where("vehicle_type <>", "TJAM");
		
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		$z = 0;
		
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, $dbtable, ++$z, $total_process);
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
					
                    $this->dbreport = $this->load->database($configdb,true); 
					$this->dbreport->select("trip_mileage_id,trip_mileage_vehicle_id,trip_mileage_start_time,trip_mileage_end_time");
                    $this->dbreport->where("trip_mileage_vehicle_id",$rowvehicle[$x]->vehicle_device);
					$this->dbreport->where("trip_mileage_engine",1); //only engine ON
					$this->dbreport->where("trip_mileage_start_time >=",$startdate);
					$this->dbreport->where("trip_mileage_start_time <=",$enddate);
                    $qreport = $this->dbreport->get($dbtable);
                    $rowsreport = $qreport->result();
					$totalrowsreport = count($rowsreport);
						
					if ($totalrowsreport > 0){
                        for($i=0;$i<$totalrowsreport;$i++)
                        {
							//printf("ID Report : %s \r\n",$rowsreport[$i]->trip_mileage_id);
							$data_array = "";
							$sdate = date("Y-m-d H:i:s", strtotime($startdate));
							$edate = date("Y-m-d H:i:s", strtotime($enddate));
							
							$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
							$this->dbhist->select("gps_time,gps_latitude_real,gps_longitude_real");
							$this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist->where("gps_time >=", $sdate);
							$this->dbhist->where("gps_time <=", $edate);    
							$this->dbhist->order_by("gps_time","asc");
							$this->dbhist->limit(1000);
							$this->dbhist->from($table);
							$q = $this->dbhist->get();
							$rows1 = $q->result();
					  
							$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist2->select("gps_time,gps_latitude_real,gps_longitude_real");							
							$this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist2->where("gps_time >=", $sdate);
							$this->dbhist2->where("gps_time <=", $edate);  							
							$this->dbhist2->order_by("gps_time","asc");
							$this->dbhist2->limit(1000);
							$this->dbhist2->from($tablehist);
							$q2 = $this->dbhist2->get();
							$rows2 = $q2->result();
							
							$rows = array_merge($rows1, $rows2); //limit data rows = 10000
							$trows = count($rows);
							$data_array[$i] = json_encode($rows);
							
							//update to db report
							unset($data);
							$data["trip_mileage_coordinate_list"] = $data_array[$i];
							$data["trip_mileage_dbtable"] = $dbtable;
							
							
							$this->dbreport->where("trip_mileage_vehicle_id",$rowvehicle[$x]->vehicle_device);
							$this->dbreport->where("trip_mileage_engine",1); //only engine ON
							$this->dbreport->where("trip_mileage_start_time >=",$rowsreport[$i]->trip_mileage_start_time);
							$this->dbreport->where("trip_mileage_start_time <=",$rowsreport[$i]->trip_mileage_end_time);
							$this->dbreport->update($dbtable,$data);
							
							printf("ID %s OKE \r\n ",$rowsreport[$i]->trip_mileage_id);
						}
					}

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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT GET KOORDINAT LIST OTHER DONE %s\r\n",$finish_time);
		
    }
	
	function getlist_coordinate_summary($userid="", $startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO GET KOORDINAT FROM OPERASIONAL >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "operasional";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "operasional_";
		$configdb = "operational_report";
        
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
        
		$this->db->order_by("vehicle_device", "asc");
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_not_in("vehicle_type", $this->config->item('vehicle_others'));
		
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		$z = 0;
		
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, $dbtable, ++$z, $total_process);
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
					
                    $this->dbreport = $this->load->database($configdb,true); 
					$this->dbreport->select("trip_mileage_id,trip_mileage_vehicle_id,trip_mileage_start_time,trip_mileage_end_time");
					$this->dbreport->order_by("trip_mileage_id","desc");
					$this->dbreport->limit(1);
                    $this->dbreport->where("trip_mileage_vehicle_id",$rowvehicle[$x]->vehicle_device);
					$this->dbreport->where("trip_mileage_start_time >=",$startdate);
					$this->dbreport->where("trip_mileage_start_time <=",$enddate);
					$qreport = $this->dbreport->get($dbtable);
                    $rowsreport = $qreport->row();
					$totalrowsreport = count($rowsreport);
						
					if ($totalrowsreport > 0){
                        
							printf("ID Report on CHECKING . . .: %s \r\n",$rowsreport->trip_mileage_id);
							$data_array = "";
							$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
							$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
							
							$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
							$this->dbhist->select("gps_time,gps_latitude_real,gps_longitude_real,gps_speed");
							$this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist->where("gps_time >=", $sdate);
							$this->dbhist->where("gps_time <=", $edate);    
							$this->dbhist->order_by("gps_time","asc");
							$this->dbhist->limit(5000);
							$this->dbhist->from($table);
							$q = $this->dbhist->get();
							$rows1 = $q->result();
					  
							$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist2->select("gps_time,gps_latitude_real,gps_longitude_real,gps_speed");							
							$this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist2->where("gps_time >=", $sdate);
							$this->dbhist2->where("gps_time <=", $edate);  							
							$this->dbhist2->order_by("gps_time","asc");
							$this->dbhist2->limit(5000);
							$this->dbhist2->from($tablehist);
							$q2 = $this->dbhist2->get();
							$rows2 = $q2->result();
							
							$rows = array_merge($rows1, $rows2); //limit data rows = 10000
							$trows = count($rows);
							
							for($i=count($rows)-1; $i >= 0; $i--)
							{
								if (($i+1) >= count($rows))
								{
									$rowsummary[] = $rows[$i];
									continue;
								}
								$latbefore = $rows[$i+1]->gps_latitude_real;
								$lngbefore = $rows[$i+1]->gps_longitude_real;
								$latcurrent = $rows[$i]->gps_latitude_real;
								$lngcurrent = $rows[$i]->gps_longitude_real;
								if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
								{
									$rowsummary[] = $rows[$i];
									continue;
								}
								if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
								{
									$rowsummary[] = $rows[$i];
									continue;
								}
							}
							
							$data_array = json_encode($rowsummary);
							
							//update to db report
							unset($data);
							$data["trip_mileage_coordinate_list"] = $data_array;
							$data["trip_mileage_dbtable"] = $dbtable;
							
							$this->dbreport->where("trip_mileage_id",$rowsreport->trip_mileage_id);
							$this->dbreport->where("trip_mileage_vehicle_id",$rowvehicle[$x]->vehicle_device);
							$this->dbreport->update($dbtable,$data);
							
							printf("ID %s OKE \r\n ",$rowsreport->trip_mileage_id);
						
					}

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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT GET KOORDINAT LIST DONE %s\r\n",$finish_time);
		
    }
	
	function summary($userid="", $startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO REPORT DATA SUMMARY KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "summary";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "summary_";
        
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
		
		//$this->db->group_by("vehicle_device");
		$this->db->order_by("vehicle_id", "asc");
		if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_not_in("vehicle_type", $this->config->item('vehicle_others'));
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
		$this->dbtrip = $this->load->database("operational_report",TRUE);
			//PORT Only
            for($x=0;$x<count($rowvehicle);$x++)
			{
				printf("PROSES VEHICLE : %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, ++$z, $total_process);
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
						
						//cek apakah sudah pernah ada filenya
						$this->dbtrans->select("autoreport_vehicle_id");
						$this->dbtrans->where("autoreport_user_id",$rowvehicle[$x]->vehicle_user_id);
						$this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
						$this->dbtrans->where("autoreport_data_startdate",$startdate);
						$this->dbtrans->where("autoreport_type",$report_type);
						$this->dbtrans->limit(1);
						$qrpt = $this->dbtrans->get("autoreport_new");
						$rrpt = $qrpt->row();
						
						if (count($rrpt) > 0)
						{
							printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
							printf("------------------------------------------------------------- \r\n");
						}
						else
						{

							$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
							$this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist->where("gps_time >=", $sdate);
							$this->dbhist->where("gps_time <=", $edate);    
							$this->dbhist->order_by("gps_time","asc");
							$this->dbhist->limit(5000);
							$this->dbhist->from($table);
							$q = $this->dbhist->get();
							$rows1 = $q->result();
					  
			
							$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
							$this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist2->where("gps_time >=", $sdate);
							$this->dbhist2->where("gps_time <=", $edate);    
							$this->dbhist2->order_by("gps_time","asc");
							$this->dbhist2->limit(5000);
							$this->dbhist2->from($tablehist);
							$q2 = $this->dbhist2->get();
							$rows2 = $q2->result();
							
							$rows = array_merge($rows1, $rows2); //limit data rows = 10000
					
							$data = array();
							$nopol = "";
							$on = false;
							$trows = count($rows);
							
							printf("TOTAL DATA : %s \r\n",$trows);
							if($trows < 10000){
										for($y=0;$y<count($rows);$y++)
										{
											if(substr($rows[$y]->gps_info_io_port, 4, 1) == 1){
												$summary_engine = "ON";
											}else{
												$summary_engine = "OFF";
											}
											
											if($rows[$y]->gps_status = "A"){
												$summary_gps_status = "OK";
											}
											else{
												$summary_gps_status = "NOT OK";
											}
											
											$summary_gps_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$y]->gps_time)));
											$summary_speed = $rows[$y]->gps_speed;
											
											$summary_location = $this->getPosition($rows[$y]->gps_longitude, $rows[$y]->gps_ew, $rows[$y]->gps_latitude, $rows[$y]->gps_ns);
											
											$summary_geofence = $this->getGeofence_location($rows[$y]->gps_longitude, $rows[$y]->gps_ew, $rows[$y]->gps_latitude, $rows[$y]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
											$summary_door = "";
											$summary_lat = $rows[$y]->gps_latitude_real;
											$summary_lng = $rows[$y]->gps_longitude_real;
											
											$summary_odometer = round(($rows[$y]->gps_info_distance*1000)/1000); 
											
											//update to db report
											unset($data);
											$data["summary_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
											$data["summary_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
											$data["summary_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
											$data["summary_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
											$data["summary_vehicle_type"] = $rowvehicle[$x]->vehicle_type;
											$data["summary_engine"] = $summary_engine;
											$data["summary_gps_time"] = $summary_gps_time;
											$data["summary_speed"] = $summary_speed;
											$data["summary_location"] = $summary_location->display_name;
											$data["summary_geofence"] = $summary_geofence;
											$data["summary_door"] = $summary_door;
											$data["summary_lat"] = $summary_lat;
											$data["summary_lng"] = $summary_lng;
											$data["summary_gps_status"] = $summary_gps_status;
											$data["summary_odometer"] = $summary_odometer;
											
											$this->dbtrip->insert($dbtable,$data);
										}
											
										printf("INSERT OK : %s %s \r\n", $rowvehicle[$x]->vehicle_device,$rowvehicle[$x]->vehicle_no);
								
							}else{
								printf("TO MANY : %s \r\n", $trows);
								printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
							}
							printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
							$data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
							$data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
							$data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
							$data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
							$data_insert["autoreport_data_startdate"] = $startdate;
							$data_insert["autoreport_data_enddate"] = $enddate;
							$data_insert["autoreport_type"] = $report_type;
							$data_insert["autoreport_process_date"] = $process_date;
							$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
							if ($trows > 0 && $trows < 10000){
								$this->dbtrans->insert("autoreport_new",$data_insert);
								printf("INSERT CONFIG OK \r\n");
							}else{
								printf("SKIP DATA : %s \r\n", $trows);
							}
				
							printf("DELETE CACHE HISTORY \r\n");
							$this->dbhist->cache_delete_all();
							$this->dbtrans->cache_delete_all();
							unset($data);
						   
							printf("============================================ \r\n");

						}
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
        
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA SUMMARY DONE %s\r\n",$finish_time);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		
		printf("Send Email OK");
        
    }
	
    function getPosition($longitude, $ew, $latitude, $ns)
	{
		$api = $this->config->item('GOOGLE_MAP_API_KEY');
		$gps_longitude_real = getLongitude($longitude, $ew);
		$gps_latitude_real = getLatitude($latitude, $ns);
					
		$gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
		$gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");	
					
		//$georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
		$georeverse = $this->gpsmodel->getLocation_byGeoCode($gps_latitude_real_fmt, $gps_longitude_real_fmt, $api);
		
		return $georeverse;
	}
	
	function getPosition_other($longitude, $latitude)
	{
		$api = $this->config->item('GOOGLE_MAP_API_KEY');
		//$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);
		$georeverse = $this->gpsmodel->getLocation_byGeoCode($latitude, $longitude, $api);
				
		return $georeverse;
	}
	
	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_user) {
		
		$this->db = $this->load->database("default", true);
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_user = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_user);

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
	
	function getGeofence_location_other($longitude, $latitude, $vehicle_user) {
		
		$this->db = $this->load->database("default", true);
		$lng = $longitude;
		$lat = $latitude;

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_user = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_user);
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
	
	//khusus TK315 DOOR
	function getDoorStatus($val)
	{
		//0 = close, else open
		$val_new = json_decode($val);
		$value = hexdec($val_new[9]);
	
		return($value);
	}
	
	//khusus X3 DOOR
	function getDoorStatus_X3($val)
	{
		//jika 53 = OPEN else CLOSE
		if($val == 53){
			$value = 1;
		}else{
			$value = 0;
		}
	
		return($value);
	}
	
	function telegram($user,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $url = "http://lacak-mobil.com/telegram/telegrampost";
        
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
        
        $url = "http://lacak-mobil.com/telegram/telegram_directpost";
        
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
