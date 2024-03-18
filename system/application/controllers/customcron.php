<?php
include "base.php";

class Customcron extends Base {
	
	function Customcron()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("custommodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}
	
	function operational_realtime($userid="", $company="", $startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '1G');
		ini_set('date.timezone', 'Asia/Jakarta');
        printf("PROSES AUTO REPORT DATA OPERASIONAL KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "operasional";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "operasional_";
		
		$this->db->where("config_name", "operational_realtime");
		$this->db->where("config_user", $userid);
		$this->db->where("config_status",1);
        $q = $this->db->get("config");
        $rowconfig = $q->row();
		
		if(count($rowconfig) > 0){
			$lastmodified = date("Y-m-d H:i:s", strtotime($rowconfig->config_lastmodified));
			$nowdatetime = date("Y-m-d H:i:s");
		}
		
		$startdate = $lastmodified;
		$enddate = $nowdatetime;
		
		$dbtable = "operasional_realtime";
		
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        $z =0;
		
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
		if($company != ""){
			$this->db->where("vehicle_company", $company);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_type <>", "A13");
		$this->db->where("vehicle_type <>", "GT06");
		$this->db->where("vehicle_type <>", "GT06N");
		$this->db->where("vehicle_type <>", "TK315");
		$this->db->where("vehicle_type <>", "TK309");
		$this->db->where("vehicle_type <>", "TK309N");
		$this->db->where("vehicle_type <>", "TK315N");
		$this->db->where("vehicle_type <>", "TJAM");
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s (%d/%d) %s %s  \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, $rowvehicle[$x]->user_name, ++$z, $total_process, $lastmodified, $nowdatetime);
			$cron_username = strtoupper($rowvehicle[$x]->user_name);
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
					$vehicle_userid = $rowvehicle[$x]->vehicle_user_id;
						
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
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns,$rowvehicle[$x]->vehicle_user_id);
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
										$datainsert["trip_mileage_user"] = $vehicle_userid;
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
										$datainsert["trip_mileage_user"] = $vehicle_userid;
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
						
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
                       
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
		
		// others vehicle
		$sdate = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($enddate)));
		$other_opr = $this->operational_realtime_other($userid, $company, $startdate, $enddate);
		
		//update to config
		$this->db = $this->load->database("default",true);
	
		unset($dataconfig);
		$dataconfig["config_lastmodified"] = $nowdatetime;
		$this->db->limit(1);
		$this->db->where("config_name", "operational_realtime");
		$this->db->where("config_user", $userid);
		$this->db->where("config_status",1);
		$this->db->update("config",$dataconfig);
							
		printf("UPDATE CONFIG OKE \r\n ");
        
		$finish_time = date("Y-m-d H:i:s");
        printf("REALTIME DATA OPERASIONAL DONE %s\r\n",$finish_time);
	
        
    }
	
	function operational_realtime_delete($startdate = "", $enddate = "")
	{

		ini_set('date.timezone', 'Asia/Jakarta');
		printf("PROSES DELETE TRIPMILEAGE REALTIME >> START \r\n");
		
		$start_time = date("Y-m-d H:i:s");
		$now_date = date("Y-m-d");
		$table_report = "operasional_realtime";
		
		if($startdate == ""){
			//2 hari sebelum //edited 7
			$date = new DateTime($now_date);
			$interval = new DateInterval('P3D');
			$date->sub($interval);
			$startdate_ex = $date->format('Y-m-d');
			$limit_startdate = date("Y-m-d H:i:s", strtotime($startdate_ex." "."00"."00"."00"));
		}
		
		if($startdate != ""){
			$limit_startdate = date("Y-m-d H:i:s", strtotime($startdate." "."00"."00"."00"));
		}
		
		if($enddate != ""){
			$limit_enddate = date("Y-m-d H:i:s", strtotime($enddate." "."23"."59"."59"));
		}
		
		if($enddate == ""){
			//2 hari sebelum // edited 7
			$date = new DateTime($now_date);
			$interval = new DateInterval('P3D');
			$date->sub($interval);
			$enddate_ex = $date->format('Y-m-d');
			$limit_enddate = date("Y-m-d H:i:s", strtotime($enddate_ex." "."23"."59"."59"));
		}
		
		$this->dbreport = $this->load->database("operational_report",TRUE);
		$this->dbreport->order_by("trip_mileage_id","asc");
		$this->dbreport->where("trip_mileage_start_time >=",$limit_startdate);
		$this->dbreport->where("trip_mileage_start_time <=",$limit_enddate);
		$this->dbreport->delete($table_report);	

		$finish_time = date("Y-m-d H:i:s");
		printf("DONE DELETE OPERATIONAL REALTIME: %s - %s \r\n",$limit_startdate, $limit_enddate);
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
		
		return;   
	}
	
	function operational_realtime_other($userid, $company, $startdate, $enddate)
    {
		ini_set('memory_limit', '1G');
		ini_set('date.timezone', 'Asia/Jakarta');
        printf("PROSES AUTO REPORT DATA OPERASIONAL OTHER >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
		$dbtable = "operasional_realtime";
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate));
        $edate = date("Y-m-d H:i:s", strtotime($enddate));
		
        $z =0;
		
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
		if($company != ""){
			$this->db->where("vehicle_company", $company);
		}
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
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, $rowvehicle[$x]->user_name, ++$z, $total_process);
			$cron_username = strtoupper($rowvehicle[$x]->user_name);
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
						
					}
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
                       
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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA OPERASIONAL OTHER REALTIME DONE %s\r\n",$finish_time);
	
		
	}
	
	//belum dipakai
	function all_report($userid="", $startdate = "", $enddate = "")
	{
		$this->operational($userid, $startdate, $enddate);
		$this->operational_other($userid, $startdate, $enddate);
		//$this->getlist_coordinate($userid, $startdate, $enddate);
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
	
	//by config
	function sos_alert()
	{	
		ini_set('date.timezone', 'Asia/Jakarta');
		$start_time = date("Y-m-d H:i:s");
		$nowdate = date('Y-m-d H:i:s');
		$configname = "panicbutton";
		
		printf("CHECK GPS ALERT \r\n");
		$this->db->order_by("config_port_value","asc");
		$this->db->where("config_active", 1);
		$this->db->where("config_sos_alert", 1);
		$this->db->where("config_flag", 0);
		$qconfig = $this->db->get("config_port");
		$rowsconfig = $qconfig->result();
		$totalconfig = count($rowsconfig);
		printf("GET ALL CONFIG SOS : %s \r\n", $totalconfig);
		printf("================================ \r\n"); 	
		if(count($totalconfig)>0){
			
			for ($i=0;$i<count($rowsconfig);$i++)
			{
				
				//jika reguler (T5 , T8 dll)
				if($rowsconfig[$i]->config_port_type == "REGULER")
				{
					printf("PORT CONFIG SOS : %s \r\n", $rowsconfig[$i]->config_port_value);
					$this->dbalert = $this->load->database($rowsconfig[$i]->config_load_database,true); 
				
					$alertcode = "BO012";
					//Cari di table webtracking gps tag alert 
					printf("CHECK GPS ALERT \r\n");
					$this->dbalert->order_by("gps_id","asc");
					$this->dbalert->select("gps_id,gps_name,gps_host,gps_time,gps_latitude_real,gps_longitude_real,gps_notif");
					$this->dbalert->where("gps_notif", 0);
					$this->dbalert->where("gps_alert", $alertcode); //alert code
					$q = $this->dbalert->get($rowsconfig[$i]->config_sos_table_gps);
					$rows = $q->result();
					$total = count($rows);
					printf("GET ALERT SOS : %s \r\n", $total); 
					
					for ($h=0;$h<count($rows);$h++)
					{
						printf("PROSES GPS ID: %s %s \r\n", $rows[$h]->gps_id, $rows[$h]->gps_name);
						
						$vehicle_device = $rows[$h]->gps_name."@".$rows[$h]->gps_host;
						$gpstime = $rows[$h]->gps_time;
						$gpsalert_time = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($gpstime)));
						$gpscoordinate = $rows[$h]->gps_latitude_real.",".$rows[$h]->gps_longitude_real;
						
						//select in db ultron
						$this->db->select("vehicle_user_id,vehicle_device,vehicle_no,vehicle_group,group_name,user_phone,vehicle_modem,company_name,company_telegram_sos");
						$this->db->where("vehicle_device", $vehicle_device);
						$this->db->limit(1);
						$this->db->join("company", "vehicle_company = company_id", "left outer");
						$this->db->join("group", "vehicle_group = group_id", "left outer");
						$this->db->join("user", "vehicle_user_id = user_id", "left outer");
						$qvehicle = $this->db->get("vehicle");
						$rowvehicle = $qvehicle->row();
						
						if(count($rowvehicle) > 0){
							$vehicleuserid = $rowvehicle->vehicle_user_id;
							$group = $rowvehicle->vehicle_group;
							$group_name = $rowvehicle->group_name;
							$nopol = $rowvehicle->vehicle_no;
							$company_name = $rowvehicle->company_name;
							$telegram_group = $rowvehicle->company_telegram_sos;
							if($rows[$h]->gps_latitude_real != "" && $rows[$h]->gps_longitude_real != ""){
								$urlmap = "Location".": "."https://www.google.com/maps?q=".$gpscoordinate;
							}else{
								$urlmap = "";
							}
							
							if($telegram_group != "" || $telegram_group != 0)
							{
								//send telegram
								$content = "PANGGILAN DARURAT!! "."\n".
										   "SEGERA HUBUNGI DRIVER KENDARAAN: ".$nopol." \n".
										   "CABANG: ".strtoupper($company_name)." \n".
										   "Alert Time: ".$gpsalert_time." \n".
										   $urlmap
										   ; 
							
								$message =  urlencode($content);
							
								$sendtelegram = $this->telegram_direct($telegram_group,$message);
								printf("===SENT TELEGRAM OK\r\n");
							
							}
							else
							{
									
								printf("DATA KENDARAAN TIDAK SESUAI USER YG DIPILIH : %s \r\n", $rows[$h]->gps_name); 
								printf("================================ \r\n"); 			
							}
						}
					
						//update data true
						unset($data_alert);
						$data_alert["gps_notif"] = "1";
						$this->dbalert->where("gps_id", $rows[$h]->gps_id);
						$this->dbalert->update($rowsconfig[$i]->config_sos_table_gps, $data_alert);
						printf("UPDATE STATUS NOTIF OK: %s %s \r\n", $rows[$h]->gps_id, $rows[$h]->gps_name); 
						printf("================================ \r\n"); 
					}
					
					
					
				}
				//TK GT dll (OTHERS)
				else
				{
					printf("PORT CONFIG SOS : %s \r\n", $rowsconfig[$i]->config_port_value);
					$this->dbalert = $this->load->database($rowsconfig[$i]->config_load_database,true); 
					
					$alertcode = "SOS!";
					//Cari di table webtracking gps tag alert 
					printf("CHECK GPS ALERT \r\n");
					$this->dbalert->order_by("date","asc");
					$this->dbalert->where("notif", 0);
					$this->dbalert->where("message", $alertcode); //alert code
					$q = $this->dbalert->get($rowsconfig[$i]->config_sos_table_gps);
					$rows = $q->result();
					$total = count($rows);
					printf("GET ALERT SOS : %s \r\n", $total); 
					
					for ($h=0;$h<count($rows);$h++)
					{
						printf("PROSES GPS ID: %s \r\n", $rows[$h]->imei);
						$gpsalert_time = date("d-m-Y H:i:s", strtotime($rows[$h]->date));
						$gpscoordinate = $rows[$h]->latitude.",".$rows[$h]->longitude;
						//get vehicle
						$this->db->select("vehicle_user_id,vehicle_device,vehicle_no,vehicle_group,group_name,user_phone,vehicle_modem,company_name,company_telegram_sos");
						$this->db->order_by("vehicle_id", "asc");
						$this->db->like("vehicle_device", $rows[$h]->imei);
						$this->db->limit(1);
						$this->db->where("vehicle_status <>", 3);
						$this->db->join("company", "vehicle_company = company_id", "left outer");
						$this->db->join("group", "vehicle_group = group_id", "left outer");
						$this->db->join("user", "vehicle_user_id = user_id", "left outer");
						$qvehicle = $this->db->get("vehicle");
						$rowvehicle = $qvehicle->row();
						
						if(count($rowvehicle) > 0){
							
							$vehicle_device = $rowvehicle->vehicle_device;
							$vehicleuserid = $rowvehicle->vehicle_user_id;
							$group = $rowvehicle->vehicle_group;
							$group_name = $rowvehicle->group_name;
							$nopol = $rowvehicle->vehicle_no;
							$company_name = $rowvehicle->company_name;
							$telegram_group = $rowvehicle->company_telegram_sos;
							if($rows[$h]->latitude != "" && $rows[$h]->longitude != ""){
								$urlmap = "Location".": "."https://www.google.com/maps?q=".$gpscoordinate;
							}else{
								$urlmap = "";
							}
							
							if($telegram_group != "" || $telegram_group != 0)
							{
								//send telegram
								$content = "PANGGILAN DARURAT!! "."\n".
										   "SEGERA HUBUNGI DRIVER KENDARAAN: ".$nopol." \n".
										   "CABANG: ".strtoupper($company_name)." \n".
										   "Alert Time: ".$gpsalert_time." \n".
										   $urlmap
										   ; 
							
								$message =  urlencode($content);
							
								$sendtelegram = $this->telegram_direct($telegram_group,$message);
								printf("===SENT TELEGRAM OK\r\n");
							
							}
							else
							{
								printf("DATA USER TELEGRAM TIDAK ADA : %s \r\n", $rows[$h]->imei); 
								printf("================================ \r\n"); 			
							}
						}
					
						//update data true
						unset($data_alert);
						$data_alert["notif"] = "1";
						$this->dbalert->limit(1);
						$this->dbalert->where("id", $rows[$h]->id);
						$this->dbalert->update($rowsconfig[$i]->config_sos_table_gps, $data_alert);
						printf("UPDATE STATUS NOTIF OK: %s \r\n", $rows[$h]->imei); 
						printf("================================ \r\n"); 
					}
					
				}
			
			}
			
		}
		
		print("FINISH CHECK GPS ALERT \r\n"); 
		print("================================ \r\n");
		
		$this->dbalert->close();
		$this->dbalert->cache_delete_all();
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function parking_alert($userid = "",$company="")
	{	
		ini_set('date.timezone', 'Asia/Jakarta');
		$start_time = date("Y-m-d H:i:s");
		$nowdate = date('Y-m-d H:i:s');
		$dbtable = "operasional_realtime";
		$startdur = 360; // > 5 menit // 6 menit
		$enddur = 3600; //1 jam
		$km_start = 0;
		$km_end = 0.5;
		
		//select config
		$this->db->where("config_name", "parking_alert_telegram");
		$this->db->where("config_user", $userid);
		$this->db->where("config_status",1);
        $qconfig = $this->db->get("config");
        $rowconfig = $qconfig->row();
		
		if(count($rowconfig) > 0){
			$lastmodified = date("Y-m-d H:i:s", strtotime($rowconfig->config_lastmodified));
			$nowdatetime = date("Y-m-d H:i:s");
			
			$sdate = $lastmodified;
			$edate = $nowdatetime;
			
			//get vehicle by user
			$this->db->select("vehicle_id,vehicle_device,vehicle_no,user_name,vehicle_company");
			$this->db->where("vehicle_user_id", $userid);
			if($company != ""){
				$this->db->where("vehicle_company",$company);	
			}
			$this->db->where("vehicle_status <>",3);
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_type <>", "A13");
			$this->db->where("vehicle_type <>", "GT06");
			$this->db->where("vehicle_type <>", "GT06N");
			$this->db->where("vehicle_type <>", "TK315");
			$this->db->where("vehicle_type <>", "TK309");
			$this->db->where("vehicle_type <>", "TK309N");
			$this->db->where("vehicle_type <>", "TK315N");
			$this->db->where("vehicle_type <>", "TJAM");
			$this->db->join("user", "vehicle_user_id = user_id", "left outer");
			$q = $this->db->get("vehicle");
			$rows = $q->result();
			$totalvehicle = count($rows);
				
			printf("GET TOTAL VEHICLE : %s \r\n", $totalvehicle);
			
			//$geofence_list = $this->getGeofence_list($userid); print_r($geofence_list);exit();
			//$devices = array("002100001711@T5","002100001723@T5","002100001669@T5","006100001556@T5","006100001558@T5");
			$ignore = array("TAG LENTENG AGUNG");
				
			//looping vehicle cek on trip report
			for ($i=0;$i<count($rows);$i++)
			{
				printf("PROSES VEHICLE DEVICE: %s %s %s \r\n", $rows[$i]->vehicle_device, $rows[$i]->vehicle_no, $rows[$i]->user_name);
				
					
					$this->db->order_by("company_id","asc");
					$this->db->select("company_telegram_parkir");
					$this->db->where("company_id", $rows[$i]->vehicle_company);
					$qv = $this->db->get("company");
					$rowv = $qv->row();
					if(count($rowv)>0){
						$telegram_group = $rowv->company_telegram_parkir;
					}else{
						$telegram_group = 0;
					}
					
					//get geofence list
					
						
					$this->dbtrip = $this->load->database("operational_report",true);
					$this->dbtrip->order_by("trip_mileage_start_time","asc");
					$this->dbtrip->where_not_in("trip_mileage_geofence_start", $ignore);
					$this->dbtrip->where("trip_mileage_user", $userid);
					$this->dbtrip->where("trip_mileage_vehicle_id", $rows[$i]->vehicle_device);
					$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
					$this->dbtrip->where("trip_mileage_end_time <=", $edate);
					$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur); 
					$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
					$this->dbtrip->where("trip_mileage_trip_mileage >=", $km_start);
					$this->dbtrip->where("trip_mileage_trip_mileage <=", $km_end);
					$qtrip = $this->dbtrip->get($dbtable);
					$rowstrip = $qtrip->result();
					$totaltrip = count($rowstrip);
					
					//counting
					$totalcumm_on = 0;
					$totalcumm_off = 0;
					$totaldur_on = 0;
					$totaldur_off = 0;
					$totaldatagps = "-";
					
					//fisrt record 
					if(count($rowstrip)>0){
						$gpstime = date("d-m-Y H:i:s",strtotime($rowstrip[0]->trip_mileage_start_time));
						$coordinate = $rowstrip[0]->trip_mileage_coordinate_start;
						$coordinate_origin = explode(" ", $coordinate);
						$latitude = trim($coordinate_origin[0]);
						$longitude = trim($coordinate_origin[1]);
						$gpscoordinate = $latitude.",".$longitude;
						
						if($rowstrip[0]->trip_mileage_coordinate_start){
							$urlmap = "Lokasi".": "."https://www.google.com/maps?q=".$gpscoordinate;
						}
						
					}else{
						$urlmap = "";
					}
				
						for($j=0; $j < count($rowstrip); $j++)
						{	
							if($rowstrip[$j]->trip_mileage_location_start == $rowstrip[$j]->trip_mileage_location_end){
								if($rowstrip[$j]->trip_mileage_engine == 1 ){
									$totalcumm_on += $rowstrip[$j]->trip_mileage_trip_mileage;
									$totaldur_on += $rowstrip[$j]->trip_mileage_duration_sec;
								}
								if($rowstrip[$j]->trip_mileage_engine == 0 ){
									$totalcumm_off += $rowstrip[$j]->trip_mileage_trip_mileage;
									$totaldur_off += $rowstrip[$j]->trip_mileage_duration_sec;
								}
								$totaldatagps = $rowstrip[$j]->trip_mileage_totaldata;
							}
						}
					
					printf("RESULT : PARKIR OFF: %s s KM: %s , PARKIR ON: %s KM: %s \r\n", $totaldur_off, $totalcumm_off, $totaldur_on, $totalcumm_on);
					
					//jika engine off lebih dari 5 menit && km 0 ~ 1
					if( ($totaldur_off >= $startdur && $totaldur_off <= $enddur) && ($totalcumm_off >= $km_start && $totalcumm_off <= $km_end) )
					{
										$conval = $totaldur_off;
										$seconds = $conval;
										$jam_off = "";
										$menit_off = "";
										// extract hours
										$hours = floor($seconds / (60 * 60));
	 
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
	 
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);
										
										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												$jam_off = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$jam_off = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$menit_off =  $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$menit_off = $minutes." "."Minutes"." ";
											}
										}
										/*  if(isset($seconds) && $seconds > 0)
										{
											echo $seconds." "."Detik"." ";
										} */
										
							//send telegram
							$message = urlencode(
										"INFORMASI: \n".
										"Pada Tanggal: ".$gpstime. "\n".
										"Kendaraan dengan Nopol: ".$rows[$i]->vehicle_no." Berhenti MESIN OFF selama: ".$jam_off." ".$menit_off . "\n".
										$urlmap. "\n"
										);
							
							if($telegram_group != 0){
								$sendtelegram = $this->telegram_direct($telegram_group,$message); 
								printf("===SENT TELEGRAM PARKIR OFF OK \r\n");
							}else{
								printf("===SKIP NO DATA TELEGRAM \r\n");
							}
							
							
					}
					//jika engine on lebih dari 5 menit && km 0 ~ 1
					if( ($totaldur_on >= $startdur && $totaldur_on <= $enddur) && ($totalcumm_on >= $km_start && $totalcumm_on <= $km_end) )
					{
										$conval = $totaldur_on;
										$seconds = $conval;
										$jam_on = "";
										$menit_on = "";
										
										// extract hours
										$hours = floor($seconds / (60 * 60));
	 
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
	 
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);
										
										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												$jam_on = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$jam_on = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$menit_on =  $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$menit_on = $minutes." "."Minutes"." ";
											}
										}
										/*  if(isset($seconds) && $seconds > 0)
										{
											echo $seconds." "."Detik"." ";
										} */
									
									
							//send telegram
							$message = urlencode(
										"INFORMASI: \n".
										"Pada Tanggal: ".$gpstime. "\n".
										"Kendaraan dengan Nopol: ".$rows[$i]->vehicle_no." Berhenti MESIN ON selama: ".$jam_on." ".$menit_on . "\n".
										$urlmap. "\n"
										);
							
							if($telegram_group != 0){
								$sendtelegram = $this->telegram_direct($telegram_group,$message); 
								printf("===SENT TELEGRAM PARKING ON OK\r\n");
							}else{
								printf("===SKIP NO DATA TELEGRAM \r\n");
							}
					}
					
			}
			
			unset($dataconfig);
			$dataconfig["config_lastmodified"] = $nowdatetime;
			$this->db->limit(1);
			$this->db->where("config_name", "parking_alert_telegram");
			$this->db->where("config_user", $userid);
			$this->db->where("config_status",1);
			$this->db->update("config",$dataconfig);
			
			printf("UPDATE CONFIG OKE \r\n ");
		}
		
		print("FINISH CHECK GPS ALERT \r\n"); 
		print("================================ \r\n"); 
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function warning_area_alert($userid="",$company="")
	{	
		ini_set('date.timezone', 'Asia/Jakarta');
		$start_time = date("Y-m-d H:i:s");
		$nowdate = date('Y-m-d H:i:s');
		$dbtable = "operasional_realtime";
		
		//select config
		$this->db->where("config_name", "warning_area_alert");
		$this->db->where("config_user", $userid);
		$this->db->where("config_status",1);
        $qconfig = $this->db->get("config");
        $rowconfig = $qconfig->row();
		
		if(count($rowconfig) > 0){
			$lastmodified = date("Y-m-d H:i:s", strtotime($rowconfig->config_lastmodified));
			$nowdatetime = date("Y-m-d H:i:s");
			
			$sdate = $lastmodified;
			$edate = $nowdatetime;
			//alert > 5 menit di warning area (by config)
			$startdur = $rowconfig->config_value; // > 5 menit
			$enddur = 3600; //1 jam
			$km_start = 0;
			$km_end = 0.2;
			
			//select geofence
			/*$this->db->select("geofence_name");
			$this->db->like("geofence_name", "warning");
			$this->db->where("geofence_user", $userid);
			$this->db->where("geofence_status", 1);
			$qgeofence = $this->db->get("geofence");
			$rowsgeofence = $qgeofence->result();
			
			$ignore = array("TAG LENTENG AGUNG"); 
			
			print_r($rowsgeofence);exit();*/
			
			$warning = "warning";
		
			printf("PROSES ALL VEHICLE USER : %s \r\n", $userid);
					//get geofence list
					$this->dbtrip = $this->load->database("operational_report",true);
					$this->dbtrip->order_by("trip_mileage_start_time","asc");
					$this->dbtrip->group_by("trip_mileage_vehicle_id");
					$this->dbtrip->like("trip_mileage_geofence_start", $warning);
					$this->dbtrip->like("trip_mileage_geofence_end", $warning);
					$this->dbtrip->where("trip_mileage_user", $userid);
					$this->dbtrip->where("trip_mileage_duration_sec >=", $startdur);
					$this->dbtrip->where("trip_mileage_duration_sec <=", $enddur);
					$this->dbtrip->where("trip_mileage_trip_mileage >=", $km_start);
					$this->dbtrip->where("trip_mileage_trip_mileage <=", $km_end);
					$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
					$this->dbtrip->where("trip_mileage_end_time <=", $edate);
					$qtrip = $this->dbtrip->get($dbtable);
					$rowstrip = $qtrip->result();
					$totaltrip = count($rowstrip);
					//print_r($rowstrip);exit();
					
					if($totaltrip >0){
						
						for($i=0;$i<$totaltrip;$i++)
                        {
							$vehicleno = $rowstrip[$i]->trip_mileage_vehicle_no;
							$datetime = date("d-m-Y H:i:s", strtotime($rowstrip[$i]->trip_mileage_start_time));
							$geofence = $rowstrip[$i]->trip_mileage_geofence_start;
							$location = $rowstrip[$i]->trip_mileage_location_start;
							$duration = $rowstrip[$i]->trip_mileage_duration;
							$coordinate = $rowstrip[$i]->trip_mileage_coordinate_start;
							$coordinate_origin = explode(" ", $coordinate);
							$latitude = trim($coordinate_origin[0]);
							$longitude = trim($coordinate_origin[1]);
							$gpscoordinate = $latitude.",".$longitude;
							$urlmap = "Location".": "."https://www.google.com/maps?q=".$gpscoordinate;
							
							$message = "Pada Tanggal: ".$datetime.", Kendaraan ".$vehicleno.", Berhenti selama ".$duration.
									   ", di geofence: ".strtoupper($geofence).", Lokasi: ".strtoupper($location).". Google Map: ".$urlmap;
							
							//send email
							$subjectmail = "WARNING AREA ALERT "."#".$rowstrip[$i]->trip_mileage_vehicle_no;
							$contentmail = $message;
							$destemail = "ccr@dcmlogistic.co.id,almas@dcmlogistic.co.id,almasdeo@me.com,budiyanto@lacak-mobil.com,buddiyanto@gmail.com,robi@lacak-mobil.com";
							//$destemail = "budiyanto@lacak-mobil.com";
							$ccemail = "buddiyanto@gmail.com";
							$noreply = "no-reply@lacak-mobil.com";
							$sendmail = $this->custommodel->sendemail($destemail,$ccemail,$noreply,$subjectmail,$contentmail);
							printf("SEND EMAIL : %s \r\n", $i+1);
						}
					}else{
						printf("NO DATA ALERT \r\n ");
					}
					
			
			unset($dataconfig);
			$dataconfig["config_lastmodified"] = $nowdatetime;
			$this->db->limit(1);
			$this->db->where("config_name", "warning_area_alert");
			$this->db->where("config_user", $userid);
			$this->db->where("config_status",1);
			$this->db->update("config",$dataconfig);
			
			printf("UPDATE CONFIG OKE \r\n ");
		}
		
		print("FINISH CHECK GPS ALERT \r\n"); 
		print("================================ \r\n"); 
		
		/*$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		$this->db->close();
		$this->db->cache_delete_all(); */
	}
	
	function getGeofence_list($userid){
		
		$this->db->order_by("geofence_name","asc");
		$this->db->group_by("geofence_name");
		$this->db->select("geofence_id,geofence_name");
		$this->db->where("geofence_user", $userid);
		$this->db->where("geofence_status", 1);
		$qv = $this->db->get("geofence");
		$rowv = $qv->result();
					
		return $rowv;
	}
	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
