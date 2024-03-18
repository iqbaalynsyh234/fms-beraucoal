<?php
include "base.php";

class Balrich_cronjob extends Base {

	function Balrich_cronjob()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->model("historymodel");
		$this->load->library('ftp');
		$this->load->library('email');

	}
	
	//for all
    function getPosition($longitude, $ew, $latitude, $ns){
        $gps_longitude_real = getLongitude($longitude, $ew);
        $gps_latitude_real = getLatitude($latitude, $ns);
        
        $gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
        $gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");    
       // print_r($gps_longitude_real_fmt." ".$gps_latitude_real_fmt);exit();           
        $georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
        
        return $georeverse;
    }
	
	//for BALRICH
    function getGeofence_location_balrich($longitude, $ew, $latitude, $ns, $vehicle_device) 
    {
        
        $this->db = $this->load->database("default", true);
        $lng = getLongitude($longitude, $ew);
        $lat = getLatitude($latitude, $ns);
                                                                           
        $sql = sprintf("
                    SELECT     *
                    FROM     %sgeofence 
                    WHERE     TRUE
                            AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
                            AND (geofence_user = 1032 )
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
	//konditional
	function getGeofence_location_balrich_edit($longitude, $ew, $latitude, $ns, $vehicle_device) 
    {
        
        $this->db = $this->load->database("default", true);
        $lng = getLongitude($longitude, $ew);
        $lat = getLatitude($latitude, $ns);
                                                                           
        $sql = sprintf("
                    SELECT     *
                    FROM     %sgeofence 
                    WHERE     TRUE
                            AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
                            AND (geofence_user = 1032 )
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
	
	//for BALRICH
	function data_operasional_balrich($startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '-1');
        printf("PROSES AUTO REPORT DATA OPERASIONAL KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
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
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", 1032);
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
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
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
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
		
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
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
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i-1]->gps_latitude_real." ".$rows[$i-1]->gps_longitude_real;
		
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
									
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										
										
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
										
									//edit flag engine ON , nol km, lebih dari 4 menit = 240
									if($duration_sec > 239 ){
										$this->dbtrip = $this->load->database("balrich_report",TRUE);
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
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i-1]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i-1]->gps_latitude_real." ".$rows[$i-1]->gps_longitude_real;
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
										
									//edit flag engine OFF , nol km, lebih dari 4 menit = 240
									if($duration_sec > 239 ){
										$this->dbtrip = $this->load->database("balrich_report",TRUE);
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
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
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
		
		//Send Email
		$cron_name = "BALRICH - OPERASIONAL REPORT";
		$this->dbtrip = $this->load->database("balrich_report",TRUE);
        $this->dbtrip->select("trip_mileage_id");
        $this->dbtrip->where("trip_mileage_start_time >=",$startdate);
        $this->dbtrip->where("trip_mileage_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
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
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
			
		printf("Send Email OK");
        
    }
	
	//for BALRICH
	function door_balrich($startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '-1');
        printf("PROSES AUTO REPORT DOOR KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
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
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", 1032);
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
            printf("PROSES VEHICLE : %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, ++$z, $total_process);
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
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
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
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
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
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
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
									
									//edit durasi diatas 2 menit = 60*2 = 120
									if($duration_sec > 120 ){
										$this->dbreport = $this->load->database("balrich_report",TRUE);
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
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
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
		
		//Send Email
		$cron_name = "BALRICH - DOOR STATUS REPORT";
		$this->dbtrip = $this->load->database("balrich_report",TRUE);
        $this->dbtrip->select("door_id");
        $this->dbtrip->where("door_start_time >=",$startdate);
        $this->dbtrip->where("door_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
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
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
			
		printf("Send Email OK");
        
    }
	//konditional
	function data_operasional_balrich_edit($startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '-1');
        printf("PROSES AUTO REPORT DATA OPERASIONAL EDIT KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
        $report_type = "operasional_edit";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
		$report = "operasional_edit_";
        
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
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", 1032);
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
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
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
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
		
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
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
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i-1]->gps_latitude_real." ".$rows[$i-1]->gps_longitude_real;
		
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
									
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										
										
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
										
									//edit flag berdasarkan geofence 
									if($geofence_start == "TARGET-LOKASI" || $geofence_end == "TARGET-LOKASI" ){
										$this->dbtrip = $this->load->database("balrich_report",TRUE);
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
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i-1]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i-1]->gps_latitude_real." ".$rows[$i-1]->gps_longitude_real;
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
										
									//edit flag berdarkan geofence
									if($geofence_start_off == "TARGET-LOKASI" || $geofence_end_off == "TARGET-LOKASI" ){
										$this->dbtrip = $this->load->database("balrich_report",TRUE);
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
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
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
        printf("AUTOREPORT DATA OPERASIONAL EDIT DONE %s\r\n",$finish_time);
		
		//Send Email
		$cron_name = "BALRICH - OPERASIONAL EDIT REPORT";
		$this->dbtrip = $this->load->database("balrich_report",TRUE);
        $this->dbtrip->select("trip_mileage_id");
        $this->dbtrip->where("trip_mileage_start_time >=",$startdate);
        $this->dbtrip->where("trip_mileage_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
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
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
			
		printf("Send Email OK");
        
    }
	//konditional
	function data_operasional_balrich_edit_archive($startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '-1');
        printf("PROSES AUTO REPORT DATA OPERASIONAL EDIT ARCHIVE KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
        $report_type = "operasional_edit";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
		$report = "operasional_edit_";
        
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
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", 1032);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device <>", "002100005509@T5");
		$this->db->where("vehicle_device <>", "002100005510@T5");
		$this->db->where("vehicle_device <>", "002100005713@T5");
		$this->db->where("vehicle_device <>", "002100005680@T5");
		$this->db->where("vehicle_device <>", "002100005711@T5");
		$this->db->where("vehicle_device <>", "002100005738@T5");
		$this->db->where("vehicle_device <>", "002100005739@T5");
		$this->db->where("vehicle_device <>", "002100005740@T5");
		$this->db->where("vehicle_device <>", "002100005741@T5");
		$this->db->where("vehicle_device <>", "002100005742@T5");
		$this->db->where("vehicle_device <>", "002100005744@T5");
		$this->db->where("vehicle_device <>", "002100005745@T5");
		$this->db->where("vehicle_device <>", "002100005746@T5");
		$this->db->where("vehicle_device <>", "002100005737@T5");
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, ++$z, $total_process);
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
						$this->dbhist2 = $this->load->database("gpsarchive",true);		
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$this->dbhist2 = $this->load->database("gpsarchive",true);		
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
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
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
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
		
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
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
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i-1]->gps_latitude_real." ".$rows[$i-1]->gps_longitude_real;
		
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
									
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										
										
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
										
									//edit flag berdasarkan geofence 
									if($geofence_start == "TARGET-LOKASI" || $geofence_end == "TARGET-LOKASI" ){
										$this->dbtrip = $this->load->database("balrich_report",TRUE);
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
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i-1]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i-1]->gps_latitude_real." ".$rows[$i-1]->gps_longitude_real;
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_balrich_edit($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
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
									
									//edit flag berdarkan geofence
									if($geofence_start_off == "TARGET-LOKASI" || $geofence_end_off == "TARGET-LOKASI" ){
										$this->dbtrip = $this->load->database("balrich_report",TRUE);
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
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
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
        printf("AUTOREPORT DATA OPERASIONAL EDIT DONE %s\r\n",$finish_time);
		
		//Send Email
		$cron_name = "BALRICH - OPERASIONAL EDIT ARCHIVE REPORT";
		$this->dbtrip = $this->load->database("balrich_report",TRUE);
        $this->dbtrip->select("trip_mileage_id");
        $this->dbtrip->where("trip_mileage_start_time >=",$startdate);
        $this->dbtrip->where("trip_mileage_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
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
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
			
		printf("Send Email OK");
        
    }
	
	//for BALRICH konditional
	function parking_balrich_cileungsi($startdate = "", $enddate = "")
	{
	
		printf("PROSES AUTO REPORT PARKING >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        $start_time = date("Y-m-d H:i:s");
        
        $report_type = "parking";
        $process_date = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
        $z = 0;
		$name ="";
		$userid = "";
		$report = "parking_";
        
        if ($startdate == "")
		{
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilenamestart = date("Ymd", strtotime("yesterday")); 
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilenamestart = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $datefilenameend = date("Ymd", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $datefilenameend = date("Ymd", strtotime("yesterday"));
        }
        
		$datefilename = $datefilenamestart."_".$datefilenameend;
        
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
		
        $this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_user_id", 1032); //balrich logistics
		$this->db->where("vehicle_company", 68); //balrich cileungsi
		$this->db->where("vehicle_status <>", 3);
		
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbhist = $this->load->database("gpshistory",true);
        $this->dbhist2 = $this->load->database("gpshistory2",true);
        for($x=0;$x<count($rowvehicle);$x++) 
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
               
				unset($data_insert);
				//port only
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
					$vehicle_user_id = $rowvehicle[$x]->vehicle_user_id; //new
						
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
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {

                        $ex_vno = explode("/",$vehicle_no);

						$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						//edited
						//$rows = $rows1;
						$rows = array_merge($rows1, $rows2);
                        $data = array(); // initialization variable
                        $vehicle_device = "";
                        $engine = "";
                
                        foreach($rows as $obj)
                        {
                            if($vehicle_device != $rowvehicle[$x]->vehicle_device)
                            {
                                $no=0;
                                $no_data = 1;
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 1)
                            { //engine ON
                                if($engine != "ON") 
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
                                $engine = "ON";
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 0)
                            { //engine OFF
                                if($engine != "OFF") 
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
                                $engine = "OFF";
                            }
                    
                            $vehicle_device = $rowvehicle[$x]->vehicle_device;
                        }//end loop foreach rows
                
                        $i=1;
                        $cummulative = 0;
						$cummulative_dur = 0; 
                        printf("WRITE DATA : ");
                        foreach($data as $vehicles=>$value_vehicles)
                        {
                            foreach($value_vehicles as $number=>$value_number)
                            {
                                foreach($value_number as $engine=>$report)
                                {
                                    if(!isset($report['end']))
                                    {
                                        $report['end'] = $report['start'];
                                    }
									
                                    $data_report[$vehicles][$number][$engine]['start'] = $report['start'];
                                    $data_report[$vehicles][$number][$engine]['end'] = $report['end'];
                                    $duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
									
									$start_1 = dbmaketime($report['start']->gps_time);
									$end_1 = dbmaketime($report['end']->gps_time);
									$lama_durasi = $end_1 - $start_1;
										
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

                                    printf("|%s|",$i);
 
										unset($datainsert);
										$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
										
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										$datainsert["parking_location_start"] = $data_report[$vehicles][$number][$engine]['location_start']->display_name;
										$datainsert["parking_location_end"] = $data_report[$vehicles][$number][$engine]['location_end']->display_name;
										$datainsert["parking_geofence_start"] = $geofence_start;
										$datainsert["parking_geofence_end"] = $geofence_end;
										$datainsert["parking_coordinate_start"] = $coordinate_start;
										$datainsert["parking_coordinate_end"] = $coordinate_end;
									
										$datainsert["parking_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["parking_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["parking_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["parking_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["parking_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["parking_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["parking_engine"] = $engine;
										$datainsert["parking_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["parking_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["parking_duration_sec"] = $lama_durasi;
										$datainsert["parking_duration"] = $show_duration;
										
										//kondisi OFF // edit by request balrich 15 menit
										// 15 menit = 900 detik
										if($engine == "OFF" && $lama_durasi > 899){
											$this->dbparking = $this->load->database("balrich_report",TRUE);
											$this->dbparking->insert($dbtable,$datainsert);
											printf("DUR : ".$lama_durasi." ".$engine." : "."OK");	
										}								
									$i++;
                                }
                            }
                        }
            
                        printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->vehicle_user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->vehicle_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if (count($data) > 0){
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", count($data));
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
                    printf("SKIP VEHICLE ( NO VEHICLE WEB SOCKET ) \r\n");
                    printf("-------------------------------------- \r\n");
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");    
            }
        }//end loop vehicle
		
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA PARKING DONE %s\r\n",$finish_time);
		
		//Send Email
		$cron_name = "BALRICH - PARKING REPORT CILEUNGSI";
		$this->dbparking = $this->load->database("balrich_report",TRUE);
        $this->dbparking->select("parking_id");
        $this->dbparking->where("parking_start_time >=",$startdate);
        $this->dbparking->where("parking_end_time <=",$enddate);
        $qtrip = $this->dbparking->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_data."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbparking->close();
        $this->dbparking->cache_delete_all();
			
		printf("Send Email OK");
	}
	
	function parking_balrich_cikarang($startdate = "", $enddate = "")
	{
	
		printf("PROSES AUTO REPORT PARKING >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        $start_time = date("Y-m-d H:i:s");
        
        $report_type = "parking";
        $process_date = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
        $z = 0;
		$name ="";
		$userid = "";
		$report = "parking_";
        
        if ($startdate == "")
		{
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilenamestart = date("Ymd", strtotime("yesterday")); 
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilenamestart = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $datefilenameend = date("Ymd", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $datefilenameend = date("Ymd", strtotime("yesterday"));
        }
        
		$datefilename = $datefilenamestart."_".$datefilenameend;
        
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
		
        $this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_user_id", 1032); //balrich logistics
		$this->db->where("vehicle_company", 65); //balrich cikarang
		$this->db->where("vehicle_status <>", 3);
		
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		//exit();
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbhist = $this->load->database("gpshistory",true);
        $this->dbhist2 = $this->load->database("gpshistory2",true);
        for($x=0;$x<count($rowvehicle);$x++) 
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
               
				unset($data_insert);
				//port only
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
					$vehicle_user_id = $rowvehicle[$x]->vehicle_user_id; //new
						
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
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {

                        $ex_vno = explode("/",$vehicle_no);

						$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						//edited
						//$rows = $rows1;
						$rows = array_merge($rows1, $rows2);
                        $data = array(); // initialization variable
                        $vehicle_device = "";
                        $engine = "";
                
                        foreach($rows as $obj)
                        {
                            if($vehicle_device != $rowvehicle[$x]->vehicle_device)
                            {
                                $no=0;
                                $no_data = 1;
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 1)
                            { //engine ON
                                if($engine != "ON") 
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
                                $engine = "ON";
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 0)
                            { //engine OFF
                                if($engine != "OFF") 
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
                                $engine = "OFF";
                            }
                    
                            $vehicle_device = $rowvehicle[$x]->vehicle_device;
                        }//end loop foreach rows
                
                        $i=1;
                        $cummulative = 0;
						$cummulative_dur = 0; 
                        printf("WRITE DATA : ");
                        foreach($data as $vehicles=>$value_vehicles)
                        {
                            foreach($value_vehicles as $number=>$value_number)
                            {
                                foreach($value_number as $engine=>$report)
                                {
                                    if(!isset($report['end']))
                                    {
                                        $report['end'] = $report['start'];
                                    }
									
                                    $data_report[$vehicles][$number][$engine]['start'] = $report['start'];
                                    $data_report[$vehicles][$number][$engine]['end'] = $report['end'];
                                    $duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
									
									$start_1 = dbmaketime($report['start']->gps_time);
									$end_1 = dbmaketime($report['end']->gps_time);
									$lama_durasi = $end_1 - $start_1;
										
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

                                    printf("|%s|",$i);
 
										unset($datainsert);
										$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
										
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										$datainsert["parking_location_start"] = $data_report[$vehicles][$number][$engine]['location_start']->display_name;
										$datainsert["parking_location_end"] = $data_report[$vehicles][$number][$engine]['location_end']->display_name;
										$datainsert["parking_geofence_start"] = $geofence_start;
										$datainsert["parking_geofence_end"] = $geofence_end;
										$datainsert["parking_coordinate_start"] = $coordinate_start;
										$datainsert["parking_coordinate_end"] = $coordinate_end;
									
										$datainsert["parking_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["parking_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["parking_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["parking_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["parking_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["parking_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["parking_engine"] = $engine;
										$datainsert["parking_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["parking_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["parking_duration_sec"] = $lama_durasi;
										$datainsert["parking_duration"] = $show_duration;
										
										//kondisi OFF // edit by request balrich 15 menit
										// 15 menit = 900 detik
										if($engine == "OFF" && $lama_durasi > 899){
											$this->dbparking = $this->load->database("balrich_report",TRUE);
											$this->dbparking->insert($dbtable,$datainsert);
											printf("DUR : ".$lama_durasi." ".$engine." : "."OK");	
										}								
									$i++;
                                }
                            }
                        }
            
                        printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->vehicle_user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->vehicle_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if (count($data) > 0){
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", count($data));
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
                    printf("SKIP VEHICLE ( NO VEHICLE WEB SOCKET ) \r\n");
                    printf("-------------------------------------- \r\n");
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");    
            }
        }//end loop vehicle
		
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA PARKING DONE %s\r\n",$finish_time);
		
		//Send Email
		$cron_name = "BALRICH - PARKING REPORT CIKARANG";
		$this->dbparking = $this->load->database("balrich_report",TRUE);
        $this->dbparking->select("parking_id");
        $this->dbparking->where("parking_start_time >=",$startdate);
        $this->dbparking->where("parking_end_time <=",$enddate);
        $qtrip = $this->dbparking->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_data."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbparking->close();
        $this->dbparking->cache_delete_all();
			
		printf("Send Email OK");
	}
	
	function parking_balrich_cikande($startdate = "", $enddate = "")
	{
	
		printf("PROSES AUTO REPORT PARKING >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        $start_time = date("Y-m-d H:i:s");
        
        $report_type = "parking";
        $process_date = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
        $z = 0;
		$name ="";
		$userid = "";
		$report = "parking_";
        
        if ($startdate == "")
		{
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilenamestart = date("Ymd", strtotime("yesterday")); 
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilenamestart = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $datefilenameend = date("Ymd", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $datefilenameend = date("Ymd", strtotime("yesterday"));
        }
        
		$datefilename = $datefilenamestart."_".$datefilenameend;
        
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
		
        $this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_user_id", 1032); //balrich logistics
		$this->db->where("vehicle_company", 397); //balrich cikande
		$this->db->where("vehicle_status <>", 3);
		
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		//exit();
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbhist = $this->load->database("gpshistory",true);
        $this->dbhist2 = $this->load->database("gpshistory2",true);
        for($x=0;$x<count($rowvehicle);$x++) 
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
               
				unset($data_insert);
				//port only
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
					$vehicle_user_id = $rowvehicle[$x]->vehicle_user_id; //new
						
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
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {

                        $ex_vno = explode("/",$vehicle_no);

						$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						//edited
						//$rows = $rows1;
						$rows = array_merge($rows1, $rows2);
                        $data = array(); // initialization variable
                        $vehicle_device = "";
                        $engine = "";
                
                        foreach($rows as $obj)
                        {
                            if($vehicle_device != $rowvehicle[$x]->vehicle_device)
                            {
                                $no=0;
                                $no_data = 1;
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 1)
                            { //engine ON
                                if($engine != "ON") 
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
                                $engine = "ON";
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 0)
                            { //engine OFF
                                if($engine != "OFF") 
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
                                $engine = "OFF";
                            }
                    
                            $vehicle_device = $rowvehicle[$x]->vehicle_device;
                        }//end loop foreach rows
                
                        $i=1;
                        $cummulative = 0;
						$cummulative_dur = 0; 
                        printf("WRITE DATA : ");
                        foreach($data as $vehicles=>$value_vehicles)
                        {
                            foreach($value_vehicles as $number=>$value_number)
                            {
                                foreach($value_number as $engine=>$report)
                                {
                                    if(!isset($report['end']))
                                    {
                                        $report['end'] = $report['start'];
                                    }
									
                                    $data_report[$vehicles][$number][$engine]['start'] = $report['start'];
                                    $data_report[$vehicles][$number][$engine]['end'] = $report['end'];
                                    $duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
									
									$start_1 = dbmaketime($report['start']->gps_time);
									$end_1 = dbmaketime($report['end']->gps_time);
									$lama_durasi = $end_1 - $start_1;
										
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

                                    printf("|%s|",$i);
 
										unset($datainsert);
										$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
										
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										$datainsert["parking_location_start"] = $data_report[$vehicles][$number][$engine]['location_start']->display_name;
										$datainsert["parking_location_end"] = $data_report[$vehicles][$number][$engine]['location_end']->display_name;
										$datainsert["parking_geofence_start"] = $geofence_start;
										$datainsert["parking_geofence_end"] = $geofence_end;
										$datainsert["parking_coordinate_start"] = $coordinate_start;
										$datainsert["parking_coordinate_end"] = $coordinate_end;
									
										$datainsert["parking_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["parking_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["parking_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["parking_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["parking_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["parking_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["parking_engine"] = $engine;
										$datainsert["parking_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["parking_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["parking_duration_sec"] = $lama_durasi;
										$datainsert["parking_duration"] = $show_duration;
										
										//kondisi OFF // edit by request balrich 15 menit
										// 15 menit = 900 detik
										if($engine == "OFF" && $lama_durasi > 899){
											$this->dbparking = $this->load->database("balrich_report",TRUE);
											$this->dbparking->insert($dbtable,$datainsert);
											printf("DUR : ".$lama_durasi." ".$engine." : "."OK");	
										}								
									$i++;
                                }
                            }
                        }
            
                        printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->vehicle_user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->vehicle_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if (count($data) > 0){
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", count($data));
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
                    printf("SKIP VEHICLE ( NO VEHICLE WEB SOCKET ) \r\n");
                    printf("-------------------------------------- \r\n");
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");    
            }
        }//end loop vehicle
		
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA PARKING DONE %s\r\n",$finish_time);
		
		//Send Email
		$cron_name = "BALRICH - PARKING REPORT CIKANDE";
		$this->dbparking = $this->load->database("balrich_report",TRUE);
        $this->dbparking->select("parking_id");
        $this->dbparking->where("parking_start_time >=",$startdate);
        $this->dbparking->where("parking_end_time <=",$enddate);
        $qtrip = $this->dbparking->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_data."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbparking->close();
        $this->dbparking->cache_delete_all();
			
		printf("Send Email OK");
	}
	
	function parking_balrich_cikokol($startdate = "", $enddate = "")
	{
	
		printf("PROSES AUTO REPORT PARKING >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        $start_time = date("Y-m-d H:i:s");
        
        $report_type = "parking";
        $process_date = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
        $z = 0;
		$name ="";
		$userid = "";
		$report = "parking_";
        
        if ($startdate == "")
		{
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilenamestart = date("Ymd", strtotime("yesterday")); 
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilenamestart = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $datefilenameend = date("Ymd", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $datefilenameend = date("Ymd", strtotime("yesterday"));
        }
        
		$datefilename = $datefilenamestart."_".$datefilenameend;
        
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
		
        $this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_user_id", 1032); //balrich logistics
		$this->db->where("vehicle_company", 64); //balrich cikokol
		$this->db->where("vehicle_status <>", 3);
		
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		//exit();
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbhist = $this->load->database("gpshistory",true);
        $this->dbhist2 = $this->load->database("gpshistory2",true);
        for($x=0;$x<count($rowvehicle);$x++) 
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
               
				unset($data_insert);
				//port only
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
					$vehicle_user_id = $rowvehicle[$x]->vehicle_user_id; //new
						
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
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {

                        $ex_vno = explode("/",$vehicle_no);

						$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						//edited
						//$rows = $rows1;
						$rows = array_merge($rows1, $rows2);
                        $data = array(); // initialization variable
                        $vehicle_device = "";
                        $engine = "";
                
                        foreach($rows as $obj)
                        {
                            if($vehicle_device != $rowvehicle[$x]->vehicle_device)
                            {
                                $no=0;
                                $no_data = 1;
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 1)
                            { //engine ON
                                if($engine != "ON") 
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
                                $engine = "ON";
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 0)
                            { //engine OFF
                                if($engine != "OFF") 
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
                                $engine = "OFF";
                            }
                    
                            $vehicle_device = $rowvehicle[$x]->vehicle_device;
                        }//end loop foreach rows
                
                        $i=1;
                        $cummulative = 0;
						$cummulative_dur = 0; 
                        printf("WRITE DATA : ");
                        foreach($data as $vehicles=>$value_vehicles)
                        {
                            foreach($value_vehicles as $number=>$value_number)
                            {
                                foreach($value_number as $engine=>$report)
                                {
                                    if(!isset($report['end']))
                                    {
                                        $report['end'] = $report['start'];
                                    }
									
                                    $data_report[$vehicles][$number][$engine]['start'] = $report['start'];
                                    $data_report[$vehicles][$number][$engine]['end'] = $report['end'];
                                    $duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
									
									$start_1 = dbmaketime($report['start']->gps_time);
									$end_1 = dbmaketime($report['end']->gps_time);
									$lama_durasi = $end_1 - $start_1;
										
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

                                    printf("|%s|",$i);
 
										unset($datainsert);
										$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
										
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										$datainsert["parking_location_start"] = $data_report[$vehicles][$number][$engine]['location_start']->display_name;
										$datainsert["parking_location_end"] = $data_report[$vehicles][$number][$engine]['location_end']->display_name;
										$datainsert["parking_geofence_start"] = $geofence_start;
										$datainsert["parking_geofence_end"] = $geofence_end;
										$datainsert["parking_coordinate_start"] = $coordinate_start;
										$datainsert["parking_coordinate_end"] = $coordinate_end;
									
										$datainsert["parking_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["parking_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["parking_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["parking_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["parking_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["parking_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["parking_engine"] = $engine;
										$datainsert["parking_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["parking_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["parking_duration_sec"] = $lama_durasi;
										$datainsert["parking_duration"] = $show_duration;
										
										//kondisi OFF // edit by request balrich 15 menit
										// 15 menit = 900 detik
										if($engine == "OFF" && $lama_durasi > 899){
											$this->dbparking = $this->load->database("balrich_report",TRUE);
											$this->dbparking->insert($dbtable,$datainsert);
											printf("DUR : ".$lama_durasi." ".$engine." : "."OK");	
										}								
									$i++;
                                }
                            }
                        }
            
                        printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->vehicle_user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->vehicle_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if (count($data) > 0){
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", count($data));
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
                    printf("SKIP VEHICLE ( NO VEHICLE WEB SOCKET ) \r\n");
                    printf("-------------------------------------- \r\n");
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");    
            }
        }//end loop vehicle
		
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA PARKING DONE %s\r\n",$finish_time);
		
		//Send Email
		$cron_name = "BALRICH - PARKING REPORT CIKOKOL";
		$this->dbparking = $this->load->database("balrich_report",TRUE);
        $this->dbparking->select("parking_id");
        $this->dbparking->where("parking_start_time >=",$startdate);
        $this->dbparking->where("parking_end_time <=",$enddate);
        $qtrip = $this->dbparking->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_data."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbparking->close();
        $this->dbparking->cache_delete_all();
			
		printf("Send Email OK");
	}
	
	function parking_balrich_keradenan($startdate = "", $enddate = "")
	{
	
		printf("PROSES AUTO REPORT PARKING >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        $start_time = date("Y-m-d H:i:s");
        
        $report_type = "parking";
        $process_date = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
        $z = 0;
		$name ="";
		$userid = "";
		$report = "parking_";
        
        if ($startdate == "")
		{
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilenamestart = date("Ymd", strtotime("yesterday")); 
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilenamestart = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $datefilenameend = date("Ymd", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $datefilenameend = date("Ymd", strtotime("yesterday"));
        }
        
		$datefilename = $datefilenamestart."_".$datefilenameend;
        
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
		
        $this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_user_id", 1032); //balrich logistics
		$this->db->where("vehicle_company", 433); //balrich keradenan
		$this->db->where("vehicle_status <>", 3);
		
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		//exit();
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbhist = $this->load->database("gpshistory",true);
        $this->dbhist2 = $this->load->database("gpshistory2",true);
        for($x=0;$x<count($rowvehicle);$x++) 
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
               
				unset($data_insert);
				//port only
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
					$vehicle_user_id = $rowvehicle[$x]->vehicle_user_id; //new
						
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
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {

                        $ex_vno = explode("/",$vehicle_no);

						$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						//edited
						//$rows = $rows1;
						$rows = array_merge($rows1, $rows2);
                        $data = array(); // initialization variable
                        $vehicle_device = "";
                        $engine = "";
                
                        foreach($rows as $obj)
                        {
                            if($vehicle_device != $rowvehicle[$x]->vehicle_device)
                            {
                                $no=0;
                                $no_data = 1;
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 1)
                            { //engine ON
                                if($engine != "ON") 
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
                                $engine = "ON";
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 0)
                            { //engine OFF
                                if($engine != "OFF") 
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
                                $engine = "OFF";
                            }
                    
                            $vehicle_device = $rowvehicle[$x]->vehicle_device;
                        }//end loop foreach rows
                
                        $i=1;
                        $cummulative = 0;
						$cummulative_dur = 0; 
                        printf("WRITE DATA : ");
                        foreach($data as $vehicles=>$value_vehicles)
                        {
                            foreach($value_vehicles as $number=>$value_number)
                            {
                                foreach($value_number as $engine=>$report)
                                {
                                    if(!isset($report['end']))
                                    {
                                        $report['end'] = $report['start'];
                                    }
									
                                    $data_report[$vehicles][$number][$engine]['start'] = $report['start'];
                                    $data_report[$vehicles][$number][$engine]['end'] = $report['end'];
                                    $duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
									
									$start_1 = dbmaketime($report['start']->gps_time);
									$end_1 = dbmaketime($report['end']->gps_time);
									$lama_durasi = $end_1 - $start_1;
										
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

                                    printf("|%s|",$i);
 
										unset($datainsert);
										$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
										
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										$datainsert["parking_location_start"] = $data_report[$vehicles][$number][$engine]['location_start']->display_name;
										$datainsert["parking_location_end"] = $data_report[$vehicles][$number][$engine]['location_end']->display_name;
										$datainsert["parking_geofence_start"] = $geofence_start;
										$datainsert["parking_geofence_end"] = $geofence_end;
										$datainsert["parking_coordinate_start"] = $coordinate_start;
										$datainsert["parking_coordinate_end"] = $coordinate_end;
									
										$datainsert["parking_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["parking_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["parking_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["parking_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["parking_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["parking_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["parking_engine"] = $engine;
										$datainsert["parking_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["parking_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["parking_duration_sec"] = $lama_durasi;
										$datainsert["parking_duration"] = $show_duration;
										
										//kondisi OFF // edit by request balrich 15 menit
										// 15 menit = 900 detik
										if($engine == "OFF" && $lama_durasi > 899){
											$this->dbparking = $this->load->database("balrich_report",TRUE);
											$this->dbparking->insert($dbtable,$datainsert);
											printf("DUR : ".$lama_durasi." ".$engine." : "."OK");	
										}								
									$i++;
                                }
                            }
                        }
            
                        printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->vehicle_user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->vehicle_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if (count($data) > 0){
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", count($data));
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
                    printf("SKIP VEHICLE ( NO VEHICLE WEB SOCKET ) \r\n");
                    printf("-------------------------------------- \r\n");
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");    
            }
        }//end loop vehicle
		
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA PARKING DONE %s\r\n",$finish_time);
		
		//Send Email
		$cron_name = "BALRICH - PARKING REPORT KERADENAN";
		$this->dbparking = $this->load->database("balrich_report",TRUE);
        $this->dbparking->select("parking_id");
        $this->dbparking->where("parking_start_time >=",$startdate);
        $this->dbparking->where("parking_end_time <=",$enddate);
        $qtrip = $this->dbparking->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_data."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbparking->close();
        $this->dbparking->cache_delete_all();
			
		printf("Send Email OK");
	}
	
	function parking_balrich_parung($startdate = "", $enddate = "")
	{
	
		printf("PROSES AUTO REPORT PARKING >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        $start_time = date("Y-m-d H:i:s");
        
        $report_type = "parking";
        $process_date = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
        $z = 0;
		$name ="";
		$userid = "";
		$report = "parking_";
        
        if ($startdate == "")
		{
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilenamestart = date("Ymd", strtotime("yesterday")); 
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilenamestart = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $datefilenameend = date("Ymd", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $datefilenameend = date("Ymd", strtotime("yesterday"));
        }
        
		$datefilename = $datefilenamestart."_".$datefilenameend;
        
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
		
        $this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_user_id", 1032); //balrich logistics
		$this->db->where("vehicle_company", 432); //balrich parung
		$this->db->where("vehicle_status <>", 3);
		
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		//exit();
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbhist = $this->load->database("gpshistory",true);
        $this->dbhist2 = $this->load->database("gpshistory2",true);
        for($x=0;$x<count($rowvehicle);$x++) 
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
               
				unset($data_insert);
				//port only
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
					$vehicle_user_id = $rowvehicle[$x]->vehicle_user_id; //new
						
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
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {

                        $ex_vno = explode("/",$vehicle_no);

						$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						//edited
						//$rows = $rows1;
						$rows = array_merge($rows1, $rows2);
                        $data = array(); // initialization variable
                        $vehicle_device = "";
                        $engine = "";
                
                        foreach($rows as $obj)
                        {
                            if($vehicle_device != $rowvehicle[$x]->vehicle_device)
                            {
                                $no=0;
                                $no_data = 1;
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 1)
                            { //engine ON
                                if($engine != "ON") 
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
                                $engine = "ON";
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 0)
                            { //engine OFF
                                if($engine != "OFF") 
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
                                $engine = "OFF";
                            }
                    
                            $vehicle_device = $rowvehicle[$x]->vehicle_device;
                        }//end loop foreach rows
                
                        $i=1;
                        $cummulative = 0;
						$cummulative_dur = 0; 
                        printf("WRITE DATA : ");
                        foreach($data as $vehicles=>$value_vehicles)
                        {
                            foreach($value_vehicles as $number=>$value_number)
                            {
                                foreach($value_number as $engine=>$report)
                                {
                                    if(!isset($report['end']))
                                    {
                                        $report['end'] = $report['start'];
                                    }
									
                                    $data_report[$vehicles][$number][$engine]['start'] = $report['start'];
                                    $data_report[$vehicles][$number][$engine]['end'] = $report['end'];
                                    $duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
									
									$start_1 = dbmaketime($report['start']->gps_time);
									$end_1 = dbmaketime($report['end']->gps_time);
									$lama_durasi = $end_1 - $start_1;
										
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

                                    printf("|%s|",$i);
 
										unset($datainsert);
										$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
										$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
										
										$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;
										$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
										
										$datainsert["parking_location_start"] = $data_report[$vehicles][$number][$engine]['location_start']->display_name;
										$datainsert["parking_location_end"] = $data_report[$vehicles][$number][$engine]['location_end']->display_name;
										$datainsert["parking_geofence_start"] = $geofence_start;
										$datainsert["parking_geofence_end"] = $geofence_end;
										$datainsert["parking_coordinate_start"] = $coordinate_start;
										$datainsert["parking_coordinate_end"] = $coordinate_end;
									
										$datainsert["parking_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
										$datainsert["parking_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
										$datainsert["parking_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
										$datainsert["parking_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
										$datainsert["parking_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["parking_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["parking_engine"] = $engine;
										$datainsert["parking_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["parking_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["parking_duration_sec"] = $lama_durasi;
										$datainsert["parking_duration"] = $show_duration;
										
										//kondisi OFF // edit by request balrich 15 menit
										// 15 menit = 900 detik
										if($engine == "OFF" && $lama_durasi > 899){
											$this->dbparking = $this->load->database("balrich_report",TRUE);
											$this->dbparking->insert($dbtable,$datainsert);
											printf("DUR : ".$lama_durasi." ".$engine." : "."OK");	
										}								
									$i++;
                                }
                            }
                        }
            
                        printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->vehicle_user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->vehicle_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if (count($data) > 0){
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", count($data));
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
                    printf("SKIP VEHICLE ( NO VEHICLE WEB SOCKET ) \r\n");
                    printf("-------------------------------------- \r\n");
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");    
            }
        }//end loop vehicle
		
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA PARKING DONE %s\r\n",$finish_time);
		
		//Send Email
		$cron_name = "BALRICH - PARKING REPORT PARUNG";
		$this->dbparking = $this->load->database("balrich_report",TRUE);
        $this->dbparking->select("parking_id");
        $this->dbparking->where("parking_start_time >=",$startdate);
        $this->dbparking->where("parking_end_time <=",$enddate);
        $qtrip = $this->dbparking->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_data."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbparking->close();
        $this->dbparking->cache_delete_all();
			
		printf("Send Email OK");
	}
	
	//door 
	function door_balrich_edit_cileungsi($startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '-1');
        printf("PROSES AUTO REPORT DOOR KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
        $report_type = "door_edit";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "door_edit_";
        
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
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", 1032);
		$this->db->where("vehicle_type", "T5DOOR");
		$this->db->where("vehicle_company", 68); //cileungsi
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
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
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
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
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
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
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
										$datainsert["door_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["door_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["door_status"] = $door;
										$datainsert["door_duration_sec"] = $duration_sec;
										$datainsert["door_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["door_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["door_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["door_mileage"] = $x_mile;
										$datainsert["door_cumm_mileage"] = $x_cum;
										$datainsert["door_coordinate_start"] = $coordinate_start;
										$datainsert["door_coordinate_end"] = $coordinate_end;
									
									//edit durasi diatas 2 menit = 60*2 = 120
									if($duration_sec > 119){
										$this->dbreport = $this->load->database("balrich_report",TRUE);
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
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
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
		
		//Send Email
		$cron_name = "BALRICH - DOOR STATUS REPORT CILEUNGSI";
		$this->dbtrip = $this->load->database("balrich_report",TRUE);
        $this->dbtrip->select("door_id");
        $this->dbtrip->where("door_start_time >=",$startdate);
        $this->dbtrip->where("door_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
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
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
			
		printf("Send Email OK");
        
    }

	function door_balrich_edit_cikarang($startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '-1');
        printf("PROSES AUTO REPORT DOOR KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
        $report_type = "door_edit";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "door_edit_";
        
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
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", 1032);
		$this->db->where("vehicle_type", "T5DOOR");
		$this->db->where("vehicle_company", 65); //cikarang
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
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
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
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
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
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
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
										$datainsert["door_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["door_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["door_status"] = $door;
										$datainsert["door_duration_sec"] = $duration_sec;
										$datainsert["door_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["door_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["door_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["door_mileage"] = $x_mile;
										$datainsert["door_cumm_mileage"] = $x_cum;
										$datainsert["door_coordinate_start"] = $coordinate_start;
										$datainsert["door_coordinate_end"] = $coordinate_end;
									
									//edit durasi diatas 2 menit = 60*2 = 120
									if($duration_sec > 119){
										$this->dbreport = $this->load->database("balrich_report",TRUE);
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
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
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
		
		//Send Email
		$cron_name = "BALRICH - DOOR STATUS REPORT CIKARANG";
		$this->dbtrip = $this->load->database("balrich_report",TRUE);
        $this->dbtrip->select("door_id");
        $this->dbtrip->where("door_start_time >=",$startdate);
        $this->dbtrip->where("door_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
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
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
			
		printf("Send Email OK");
        
    }
	
	function door_balrich_edit_cikande($startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '-1');
        printf("PROSES AUTO REPORT DOOR KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
        $report_type = "door_edit";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "door_edit_";
        
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
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", 1032);
		$this->db->where("vehicle_type", "T5DOOR");
		$this->db->where("vehicle_company", 397); //cikande
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
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
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
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
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
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
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
										$datainsert["door_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["door_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["door_status"] = $door;
										$datainsert["door_duration_sec"] = $duration_sec;
										$datainsert["door_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["door_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["door_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["door_mileage"] = $x_mile;
										$datainsert["door_cumm_mileage"] = $x_cum;
										$datainsert["door_coordinate_start"] = $coordinate_start;
										$datainsert["door_coordinate_end"] = $coordinate_end;
									
									//edit durasi diatas 2 menit = 60*2 = 120
									if($duration_sec > 119){
										$this->dbreport = $this->load->database("balrich_report",TRUE);
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
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
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
		
		//Send Email
		$cron_name = "BALRICH - DOOR STATUS REPORT CIKANDE";
		$this->dbtrip = $this->load->database("balrich_report",TRUE);
        $this->dbtrip->select("door_id");
        $this->dbtrip->where("door_start_time >=",$startdate);
        $this->dbtrip->where("door_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
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
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
			
		printf("Send Email OK");
        
    }
	
	function door_balrich_edit_cikokol($startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '-1');
        printf("PROSES AUTO REPORT DOOR KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
        $report_type = "door_edit";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "door_edit_";
        
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
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", 1032);
		$this->db->where("vehicle_type", "T5DOOR");
		$this->db->where("vehicle_company", 64); //cikokol
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
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
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
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
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
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
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
										$datainsert["door_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["door_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["door_status"] = $door;
										$datainsert["door_duration_sec"] = $duration_sec;
										$datainsert["door_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["door_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["door_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["door_mileage"] = $x_mile;
										$datainsert["door_cumm_mileage"] = $x_cum;
										$datainsert["door_coordinate_start"] = $coordinate_start;
										$datainsert["door_coordinate_end"] = $coordinate_end;
									
									//edit durasi diatas 2 menit = 60*2 = 120
									if($duration_sec > 119){
										$this->dbreport = $this->load->database("balrich_report",TRUE);
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
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
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
		
		//Send Email
		$cron_name = "BALRICH - DOOR STATUS REPORT CIKOKOL";
		$this->dbtrip = $this->load->database("balrich_report",TRUE);
        $this->dbtrip->select("door_id");
        $this->dbtrip->where("door_start_time >=",$startdate);
        $this->dbtrip->where("door_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
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
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
			
		printf("Send Email OK");
        
    }
	
	function door_balrich_edit_keradenan($startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '-1');
        printf("PROSES AUTO REPORT DOOR KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
        $report_type = "door_edit";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "door_edit_";
        
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
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", 1032);
		$this->db->where("vehicle_type", "T5DOOR");
		$this->db->where("vehicle_company", 433); //keradenan
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
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
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
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
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
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
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
										$datainsert["door_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["door_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["door_status"] = $door;
										$datainsert["door_duration_sec"] = $duration_sec;
										$datainsert["door_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["door_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["door_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["door_mileage"] = $x_mile;
										$datainsert["door_cumm_mileage"] = $x_cum;
										$datainsert["door_coordinate_start"] = $coordinate_start;
										$datainsert["door_coordinate_end"] = $coordinate_end;
									
									//edit durasi diatas 2 menit = 60*2 = 120
									if($duration_sec > 119){
										$this->dbreport = $this->load->database("balrich_report",TRUE);
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
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
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
		
		//Send Email
		$cron_name = "BALRICH - DOOR STATUS REPORT KERADENAN";
		$this->dbtrip = $this->load->database("balrich_report",TRUE);
        $this->dbtrip->select("door_id");
        $this->dbtrip->where("door_start_time >=",$startdate);
        $this->dbtrip->where("door_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
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
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
			
		printf("Send Email OK");
        
    }
	
	function door_balrich_edit_parung($startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '-1');
        printf("PROSES AUTO REPORT DOOR KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		$userid = "";
		
        $report_type = "door_edit";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "door_edit_";
        
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
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_user_id", 1032);
		$this->db->where("vehicle_type", "T5DOOR");
		$this->db->where("vehicle_company", 432); //parung
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
                    $qrpt = $this->dbtrans->get("autoreport_balrich");
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
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
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
							
										$geofence_start = $this->getGeofence_location_balrich($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
										$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;
							
										$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
										$data_report[$vehicles][$number][$door]['location_end'] = $location_end;
							
										$geofence_end = $this->getGeofence_location_balrich($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
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
										$datainsert["door_vehicle_company"] = $rowvehicle[$x]->vehicle_company;
										$datainsert["door_vehicle_group"] = $rowvehicle[$x]->vehicle_group;
										$datainsert["door_status"] = $door;
										$datainsert["door_duration_sec"] = $duration_sec;
										$datainsert["door_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
										$datainsert["door_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
										$datainsert["door_duration"] = $data_report[$vehicles][$number][$door]['duration'];
										$datainsert["door_mileage"] = $x_mile;
										$datainsert["door_cumm_mileage"] = $x_cum;
										$datainsert["door_coordinate_start"] = $coordinate_start;
										$datainsert["door_coordinate_end"] = $coordinate_end;
									
									//edit durasi diatas 2 menit = 60*2 = 120
									if($duration_sec > 119){
										$this->dbreport = $this->load->database("balrich_report",TRUE);
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
							$this->dbtrans->insert("autoreport_balrich",$data_insert);
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
		
		//Send Email
		$cron_name = "BALRICH - DOOR STATUS REPORT PARUNG";
		$this->dbtrip = $this->load->database("balrich_report",TRUE);
        $this->dbtrip->select("door_id");
        $this->dbtrip->where("door_start_time >=",$startdate);
        $this->dbtrip->where("door_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
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
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
			
		printf("Send Email OK");
        
    }
	function out_of_geofence_pool()
	{

		$nowdate = date('Y-m-d H:i:s');
		$company = 397; // sementara hanya cikande
		if($company == 397){
			$company_name = "cikande";
		}else{
			$company_name = "no_data_company";
		}
		
		$geofence_name = "POOL CIKANDE";
		$balrich_id = 1032;
		$offset = 0; 
		$i = 0;
		
		$this->db->select("config_name,config_value");
		$this->db->where("config_name", "out_of_pool_balrich");
		$qconfig = $this->db->get("config");
		$rowconfig = $qconfig->row();
		
		$lastrunning = strtotime($rowconfig->config_value);
		
		$lastcheck = date("Y-m-d H:i:s", $lastrunning);

		printf("==== SEARCH VEHICLE ====\r\n");
		
		$this->db->select("geoalert_vehicle_company,geoalert_time,geoalert_vehicle,geoalert_vehicle_type,geoalert_direction,
						   geoalert_lat,geoalert_lng,geoalert_geofence,geofence_name, vehicle_name, vehicle_no");
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("geoalert_vehicle_company ", $company);
		$this->db->like("geofence_name", $geofence_name);
		$this->db->where("geoalert_time >=", $lastcheck);
		$this->db->where("geoalert_time <=", $nowdate);
		$this->db->where("geoalert_direction", 2);
		$this->db->join("geofence", "geoalert_geofence = geofence_id", "left outer");
		$this->db->join("vehicle", "geoalert_vehicle = vehicle_device", "left outer");
		$q = $this->db->get("geofence_alert_balrich");
		$rows = $q->result();
		$total = count($rows);

		if (count($rows)>0)
		{
			printf("==== TOTAL DATA : %s ==== \r\n", $total);
			printf("==== CREATE EXCEL ==== \r\n");
			
			/** PHPExcel */
			include 'class/PHPExcel.php';
				
			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';
				
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();
			
			$domain_server = "http://202.129.190.194/";
			$report_path = "/home/transporter/public_html/assets/media/balrich_report/"; //web server
			//$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/report/"; //cron server
			$pub_path = "assets/media/report/";
			
			// Set properties
			$objPHPExcel->getProperties()->setCreator("balrich.lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("balrich.lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("OUT OF GEOFENCE POOL REPORT");
			$objPHPExcel->getProperties()->setSubject("OUT OF GEOFENCE POOL REPORT");
			$objPHPExcel->getProperties()->setDescription("OUT OF GEOFENCE POOL REPORT");
			
			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);			
			
			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'NO');
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'VEHICLE NO');
			$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'KELUAR');
			$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'TIME');
			$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'COORDINATE');
			$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'LOCATION');
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$i = 1;
			for ($j=0;$j<count($rows);$j++)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(1+$i), $i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.(1+$i), $rows[$j]->vehicle_no);
				$objPHPExcel->getActiveSheet()->getStyle('B'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.(1+$i), date("Y-m-d",strtotime($rows[$j]->geoalert_time)));
				$objPHPExcel->getActiveSheet()->getStyle('C'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.(1+$i), date("H:i:s",strtotime($rows[$j]->geoalert_time)));
				$objPHPExcel->getActiveSheet()->getStyle('D'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(1+$i), number_format($rows[$j]->geoalert_lat, 4, ".", "")." ".number_format($rows[$j]->geoalert_lng, 4, ".", "") );
				$objPHPExcel->getActiveSheet()->getStyle('E'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				
				$geofence_fullname = explode(" ", $rows[$j]->geofence_name);
				
				$geofence_pool = $geofence_fullname[0];
				$geofence_name = $geofence_fullname[1];
			
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(1+$i), $geofence_name);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	
				$i++;
			}
			
			$styleArray = array(
					  'borders' => array(
						'allborders' => array(
						  'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					  )
					);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.(0+$i))->applyFromArray($styleArray);
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.(0+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.(0+$i))->getAlignment()->setWrapText(true);
				
				// Rename sheet
				$objPHPExcel->getActiveSheet()->setTitle('OUT OF GEOFENCE POOL');
				printf("===== CREATE FILE ===== \r\n");
				
				// Save Excel
				$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
				@mkdir($report_path, DIR_WRITE_MODE);
				$filedate = date("YmdHi");
				$filecreatedname = $company_name."_".$filedate.".xls";
				$objWriter->save($report_path.$filecreatedname); 
				printf("==== CREATE FILE DONE : %s \r\n",$filecreatedname);
				$public_path = $domain_server.$pub_path.$filecreatedname;
				$source = $report_path.$filecreatedname;
				$destination = '/';
				
				printf("===== FTP CONFIG ===== \r\n");
				$config['hostname'] = '110.5.109.246';
				$config['username'] = 'lacak';
				$config['password'] = 'lacak123';
				$config['port']     = '112';
				$config['debug'] 	= TRUE;
				$this->ftp->connect($config);
				printf("===== FTP CONNECTED ===== \r\n");
				$this->ftp->upload($report_path.$filecreatedname, '/'.$filecreatedname);
				$this->ftp->close();
				printf("===== UPLOAD OK ==== \r\n");
			
				//update config
				unset($updateconfig);
				
				$newlastcheck = date('Y-m-d H:i:s');
				$updateconfig['config_value'] = $newlastcheck;
				
				$this->db->where("config_name", "out_of_pool_balrich");
				$this->db->update("config", $updateconfig);
				printf("===== UPDATE CONFIG OK ==== \r\n");
				
		}
		else
		{
			printf("===== NO DATA ====== \r\n"); 
		}
		
		$this->db->close();	
		$this->db->cache_delete_all();	
		printf("======== FINISH ======== \r\n"); 
	}
	
	function out_of_geofence_pool_new()
	{
		$nowdate = date('Y-m-d H:i:s');
		$balrich_id = 1032;
		$offset = 0; 
		$i = 0;
		
		//edited select lebih dari 1 list Geofence
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->order_by("pool_name","asc");
		$this->dbtransporter->where("pool_flag", 0);
		$this->dbtransporter->where("pool_status", 1);
		$this->dbtransporter->where("pool_user_id", $balrich_id);
		$q_pool = $this->dbtransporter->get("balrich_pool");
		
		if ($q_pool->num_rows() == 0)
		{
			printf("No Data Pool Geofence !! \r\n");
			return;
		}
		
		$rows_pool = $q_pool->result();
		$total_pool = count($rows_pool);
		
		//select config sebagai batas waktu 
		$this->db->select("config_name,config_value");
		$this->db->where("config_name", "out_of_pool_balrich");
		$qconfig = $this->db->get("config");
		$rowconfig = $qconfig->row();
	
		$lastrunning = strtotime($rowconfig->config_value);
		$lastcheck = date("Y-m-d H:i:s", $lastrunning);
		
		for ($p=0;$p<count($rows_pool);$p++)
		{
			printf("PROCESS FOR POOL : %s\r\n", $rows_pool[$p]->pool_name);
			printf("==== SEARCH VEHICLE ====\r\n");
			$geofence_name = $rows_pool[$p]->pool_name;
			$geofence_folder = $rows_pool[$p]->pool_folder;
			$company = $rows_pool[$p]->pool_company;
			$company_name = $rows_pool[$p]->pool_folder;
			
			$this->db->select("geoalert_vehicle_company,geoalert_time,geoalert_vehicle,geoalert_vehicle_type,geoalert_direction,
							   geoalert_lat,geoalert_lng,geoalert_geofence,geofence_name, vehicle_name, vehicle_no");
			$this->db->order_by("vehicle_no","asc");
			$this->db->where("geoalert_vehicle_company ", $company);
			$this->db->like("geofence_name", $geofence_name);
			$this->db->where("geoalert_time >=", $lastcheck);
			$this->db->where("geoalert_time <=", $nowdate);
			$this->db->where("geoalert_direction", 2);
			$this->db->join("geofence", "geoalert_geofence = geofence_id", "left outer");
			$this->db->join("vehicle", "geoalert_vehicle = vehicle_device", "left outer");
			$q = $this->db->get("geofence_alert_balrich");
			$rows = $q->result();
			$total = count($rows);

			if (count($rows)>0)
			{
				printf("==== TOTAL DATA : %s ==== \r\n", $total);
				printf("==== CREATE EXCEL ==== \r\n");
				
				/** PHPExcel */
				include_once 'class/PHPExcel.php';
					
				/** PHPExcel_Writer_Excel2007 */
				include_once 'class/PHPExcel/Writer/Excel2007.php';
					
				// Create new PHPExcel object
				$objPHPExcel = new PHPExcel();
				
				$domain_server = "http://202.129.190.194/";
				$report_path = "/home/transporter/public_html/assets/media/balrich_report/"; //web server
				//$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/report/"; //cron server
				$pub_path = "assets/media/report/";
				
				// Set properties
				$objPHPExcel->getProperties()->setCreator("balrich.lacak-mobil.com");
				$objPHPExcel->getProperties()->setLastModifiedBy("balrich.lacak-mobil.com");
				$objPHPExcel->getProperties()->setTitle("OUT OF GEOFENCE POOL REPORT");
				$objPHPExcel->getProperties()->setSubject("OUT OF GEOFENCE POOL REPORT");
				$objPHPExcel->getProperties()->setDescription("OUT OF GEOFENCE POOL REPORT");
				
				//set document
				$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
				$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
				$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);			
				
				//Top Header
				$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'NO');
				$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'VEHICLE NO');
				$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'KELUAR');
				$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'TIME');
				$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'COORDINATE');
				$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'LOCATION');
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$i = 1;
				for ($j=0;$j<count($rows);$j++)
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.(1+$i), $i);
					$objPHPExcel->getActiveSheet()->getStyle('A'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.(1+$i), $rows[$j]->vehicle_no);
					$objPHPExcel->getActiveSheet()->getStyle('B'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					
					$objPHPExcel->getActiveSheet()->SetCellValue('C'.(1+$i), date("Y-m-d",strtotime($rows[$j]->geoalert_time)));
					$objPHPExcel->getActiveSheet()->getStyle('C'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					
					$objPHPExcel->getActiveSheet()->SetCellValue('D'.(1+$i), date("H:i:s",strtotime($rows[$j]->geoalert_time)));
					$objPHPExcel->getActiveSheet()->getStyle('D'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.(1+$i), number_format($rows[$j]->geoalert_lat, 4, ".", "")." ".number_format($rows[$j]->geoalert_lng, 4, ".", "") );
					$objPHPExcel->getActiveSheet()->getStyle('E'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					
					$geofence_fullname = explode(" ", $rows[$j]->geofence_name);

					$geofence_pool = $geofence_fullname[0];
					$geofence_name = $geofence_fullname[1];
				
					$objPHPExcel->getActiveSheet()->SetCellValue('F'.(1+$i), $geofence_name);
					$objPHPExcel->getActiveSheet()->getStyle('F'.(1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		
					$i++;
				}
				
				$styleArray = array(
						  'borders' => array(
							'allborders' => array(
							  'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						  )
						);
					
					$objPHPExcel->getActiveSheet()->getStyle('A1:F'.(0+$i))->applyFromArray($styleArray);
					$objPHPExcel->getActiveSheet()->getStyle('A1:F'.(0+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
					$objPHPExcel->getActiveSheet()->getStyle('A1:F'.(0+$i))->getAlignment()->setWrapText(true);
					
					// Rename sheet
					$objPHPExcel->getActiveSheet()->setTitle('OUT OF GEOFENCE POOL');
					printf("===== CREATE FILE ===== \r\n");
					
					// Save Excel
					$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
					@mkdir($report_path, DIR_WRITE_MODE);
					$filedate = date("YmdHi");
					$filecreatedname = $company_name."_".$filedate.".xls";
					$objWriter->save($report_path.$filecreatedname); 
					printf("==== CREATE FILE DONE : %s \r\n",$filecreatedname);
					$public_path = $domain_server.$pub_path.$filecreatedname;
					$source = $report_path.$filecreatedname;
					$destination = '/';
					
					printf("===== FTP CONFIG ===== \r\n");
					//$config['hostname'] = '110.5.109.246';
					$config['hostname'] = '103.58.101.223';
					$config['username'] = 'lacak';
					$config['password'] = 'lacak123';
					$config['port']     = '112';
					$config['debug'] 	= TRUE;
					$this->ftp->connect($config);
					printf("===== FTP CONNECTED ===== \r\n");
					$this->ftp->upload($report_path.$filecreatedname, '/'.$geofence_folder.'/'.$filecreatedname);
					$this->ftp->close();
					printf("===== UPLOAD OK ==== \r\n");

			}
			
			else
			{
				printf("===== NO DATA VEHICLE====== \r\n"); 
			}

		}
		
		//update config
		unset($updateconfig);
					
		$updateconfig['config_value'] = $nowdate;
					
		$this->db->where("config_name", "out_of_pool_balrich");
		$this->db->update("config", $updateconfig);
		printf("===== UPDATE CONFIG OK ==== \r\n");
		$this->db->close();	
		$this->db->cache_delete_all();
		
		$this->dbtransporter->close();
		$this->dbtransporter->cache_delete_all();
		$enddate = date('Y-m-d H:i:s');
		
		printf("FINISH CRON OUT OF GEOFENCE POOL at : %s \r\n", $enddate);
		printf("============================== \r\n"); 
	}
	
	function balrichsmu_conn()
	{
		$nowdate = date('Y-m-d H:i:s');
		$balrich_id = 1032;
		$offset = 0; 
		$i = 0;
		printf("SMU CONNECTION START !! \r\n");
		//edited select mobil smu balrich
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->order_by("smu_vehicle_no","asc");
		$this->dbtransporter->where("smu_flag", 0);
		$q_v = $this->dbtransporter->get("balrich_smu");
		
		if ($q_v->num_rows() == 0)
		{
			printf("No Data Vehicle !! \r\n");
			return;
		}
		
		$rows_v = $q_v->result();
		$total_vehicle = count($rows_v);
		
		$nowdate = date("Y-m-d H:i:s");
		$nowdate_gps = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($nowdate)));
		$dateinterval = new DateTime($nowdate_gps);
		$dateinterval->sub(new DateInterval('PT3M'));
		$mindate_gps = $dateinterval->format('Y-m-d H:i:s');
		
		$limitdate = "2016-02-03 14:00:00";
		$limitdate_gps = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($limitdate)));
	
		for ($p=0;$p<count($rows_v);$p++)
		{
			$this->db = $this->load->database("deafult", TRUE);
			$this->db->where("vehicle_id", $rows_v[$p]->smu_vehicle_id);
			$q_vehicle = $this->db->get("vehicle");
			$rowvehicle = $q_vehicle->row();
			
			$vehicle_front = substr($rowvehicle->vehicle_no,0,1);
			$vehicle_mid = substr($rowvehicle->vehicle_no,1,4);
			$vehicle_end = substr($rowvehicle->vehicle_no,5,3); 
			
			printf("PROCESS : %s,%s\r\n", $rowvehicle->vehicle_no, $rowvehicle->vehicle_device);
			
			$json = json_decode($rowvehicle->vehicle_info);
			
			if ($rowvehicle->vehicle_info)
			{	
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");				
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}
			
			if (! isset($table))
			{		
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
			
			printf("SEARCH DATA  : %s\r\n", $database);
			
			if($table){
				
				$this->db->select("	gps_id,gps_name,gps_status,gps_time,gps_latitude_real,gps_longitude_real,
									gps_speed,gps_course,gps_odometer,gps_sent,
									gps_info_id,gps_info_device,gps_info_time,gps_info_io_port");
				$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");	
				$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
				$this->db->where("gps_time <=", $mindate_gps);
				$this->db->where("gps_time >=", $limitdate_gps);
				$this->db->where("gps_sent",0);
				$this->db->order_by("gps_time","desc");
				$this->db->from($table);
				$q = $this->db->get();
				$this->db->flush_cache();
				$rows = $q->result();
				
				if(count($rows) > 0){
					for ($g=0;$g<count($rows);$g++)
					{
						//print data
						$gpsid = $rows[$g]->gps_id;
						$gpsinfoid = $rows[$g]->gps_info_id;
						$license_num = $vehicle_front." ".$vehicle_mid." ".$vehicle_end;			
						$time = date("Y-m-d H:i:s");
						$validgpstime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$g]->gps_time)));
						$longitude = $rows[$g]->gps_longitude_real;
						$latitude = $rows[$g]->gps_latitude_real;
						$speed = $rows[$g]->gps_speed;
						$direct = $rows[$g]->gps_course;
						$odometer = $rows[$g]->gps_odometer;
						$eng = $rows[$g]->gps_info_io_port;
						if($eng){
							if($eng == "0000100000"){
								$engine = "true";
							}else{
								$engine = "false";
							}
						}
						
						printf("UPDATE STATUS GPS ID: %s\r\n", $gpsid);
						printf("UPDATE STATUS GPS INFO ID: %s\r\n", $gpsinfoid);
						
						//update info sent
						unset($dataupdate);
						$dataupdate["gps_sent"] = 1;
						$this->db->where("gps_id",$gpsid);
						$this->db->update($table,$dataupdate);
						
						unset($datainfoupdate);
						$datainfoupdate["gps_info_sent"] = 1;
						$this->db->where("gps_info_id",$gpsinfoid);
						$this->db->update($tableinfo,$datainfoupdate);
					}
				}else{
					printf("NO DATA !! \r\n");
				}
				
			}
				
			//print_r($license_num." ".$time." ".$validgpstime." ".$longitude." ".$latitude." ".$speed." ".$direct." ".$odometer." ".$engine. " - ".$gpsid." ".$gpsinfoid);exit();
		} 
		
	}

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
