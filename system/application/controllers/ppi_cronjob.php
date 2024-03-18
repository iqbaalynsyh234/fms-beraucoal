<?php
include "base.php";

class Ppi_cronjob extends Base {

	function Ppi_cronjob()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->model("historymodel");
	}
	
	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_device) {
		
		$this->db = $this->load->database("default", true);
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_vehicle = '%s' )
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
	
	function getPosition($longitude, $ew, $latitude, $ns){
		$gps_longitude_real = getLongitude($longitude, $ew);
		$gps_latitude_real = getLatitude($latitude, $ns);
					
		$gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
		$gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");	
					
		$georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
		
		return $georeverse;
	}
	
	//for ppi
	function history_hourmeter_ppi($startdate = "", $enddate= "")
	{
		$start_time = date("d-m-Y H:i:s");
		
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbhistory = $this->load->database("gpshistory",true);
		$this->dbhistory2 = $this->load->database("gpshistory2",true);
		
		$starting_time = date("d-m-Y H:i:s");
		echo "Starting...\r\n"." ".$starting_time;

		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }

        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
		//print_r($enddate);exit();
		
		
		$this->db->where("vehicle_user_id", "1839");
		$q = $this->db->get('vehicle');
		$vehicle = $q->result();
		//print_r($rows);exit;

		for ($x=0;$x<count($vehicle);$x++)
		{
			//print_r($vehicle);exit;
			if (isset($vehicle[$x]))
			{
				$vexplode = explode("@",$vehicle[$x]->vehicle_device);
				$vex = $vexplode[0]."@t5";
				$vdevice = $vehicle[$x]->vehicle_device;
			}
			else
			{
				echo "FINISH!!";
	
			}
			
			$this->db->where("vehicle_device", $vdevice);
			$this->db->limit(1);
			$qve = $this->db->get("vehicle");
			$rowv = $qve->row(); 
			
			$table = $vex."_gps";
			$tableinfo = $vex."_info";

			$this->dbhistory->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = '".$vdevice."'");
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
			$this->dbhistory->where("gps_time >=", $sdate);
			$this->dbhistory->where("gps_time <=", $edate);
			$this->dbhistory->order_by("gps_time", "asc");
			$q = $this->dbhistory->get($table);
			$rows1 = $q->result();
			//print_r($rows1);exit;
			
			$this->dbhistory2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = '".$vdevice."'");
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
			$this->dbhistory2->where("gps_time >=", $sdate);
			$this->dbhistory2->where("gps_time <=", $edate);
			$this->dbhistory2->order_by("gps_time", "asc");
			$q2 = $this->dbhistory2->get($table);
			$rows2 = $q2->result();
			//print_r($rows2);exit;
			
			$rows = array_merge($rows1, $rows2);
			//print_r($rows);exit;
			
			$data = array();
		
			$nopol = "";
		
			$on = false;
			$trows = count($rows);
			//print_r($trows);exit();
			
			for($i=0;$i<$trows;$i++){
			//if($rows[$i]->gps_speed == 0) continue;
			
			if($nopol != $rowv->vehicle_no){ //new vehicle
				if($on && $i!=0){ 
					$data[$rowv->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					$data[$rowv->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
					$data[$rowv->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
					$data[$rowv->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
				}
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){								
					$trip_no = 1;					
					$data[$rowv->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					$data[$rowv->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
					$data[$rowv->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
					$data[$rowv->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
					$data[$rowv->vehicle_no][$trip_no-1]['vehicle_name'] = $rowv->vehicle_name;
					$data[$rowv->vehicle_no][$trip_no-1]['vehicle_device'] = $rowv->vehicle_device;
						
					$on = true;
					
					if($i==$trows-1){												
						$data[$rowv->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowv->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
						$data[$rowv->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowv->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					$trip_no = 1;
					$on = false;
										
				}
				
				
			}else{ //same vehicle
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					if(!$on){	
												
						$data[$rowv->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowv->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
						$data[$rowv->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowv->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowv->vehicle_no][$trip_no-1]['vehicle_name'] = $rowv->vehicle_name;
						$data[$rowv->vehicle_no][$trip_no-1]['vehicle_device'] = $rowv->vehicle_device;
						
					}
					
					$on = true;	
					if($i==$trows-1 && $on){												
						$data[$rowv->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowv->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowv->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
						$data[$rowv->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}									
				}else{			
					if($on){
						$data[$rowv->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));	
						$data[$rowv->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowv->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
						$data[$rowv->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
					$on = false;
													
				}
			}
			
			$nopol = $rowv->vehicle_no;
			$vehicle_name = $rowv->vehicle_name;
			$vehicle_device = $rowv->vehicle_device;
		}
		
		if(count($data) > 0)
		{
			$j=1;
			$new = "";
			unset($insert_data);
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
					
						$duration = get_time_difference($report['start_time'], $report['end_time']);                
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
						$tm = $mileage/1000;
						$cumm += $tm;
						$insert_data['history_trip_mileage_vehicle_id'] = $rowv->vehicle_device;
						$insert_data['history_trip_mileage_vehicle_no'] = $rowv->vehicle_no;
						$insert_data['history_trip_mileage_vehicle_name'] = $rowv->vehicle_name;
						$insert_data['history_trip_mileage_trip_no'] = $trip_no++;
						$insert_data['history_trip_mileage_start_time'] = date("Y-m-d H:i:s", strtotime($report['start_time']));
						$insert_data['history_trip_mileage_end_time'] = date("Y-m-d H:i:s", strtotime($report['end_time']));
						$insert_data['history_trip_mileage_duration'] = $show;
						$insert_data['history_trip_mileage_trip_mileage'] = $tm;
						$insert_data['history_trip_mileage_cummulative_mileage'] = $cumm;
						
						if ($report['start_geofence_location']) 
						{
							$arrGeo = explode("#", $report['start_geofence_location']);
							if(count($arrGeo)>1)
							{
								$geoname = $arrGeo[1];
							}
							else
							{
								$geoname = $arrGeo[0];
							}
							
							$insert_data['history_trip_mileage_location_start'] = strtoupper($geoname).",".$report['start_position']->display_name;
						}
						else
						{
							$insert_data['history_trip_mileage_location_start'] = $report['start_position']->display_name;
						}
				
						if ($report['end_geofence_location']) 
						{
							$arrGeoEnd = explode("#", $report['end_geofence_location']);
							if(count($arrGeoEnd)>1)
							{
								$geonameend = $arrGeoEnd[1];
							}
							else
							{
								$geonameend = $arrGeoEnd[0];
							}
							
							$insert_data['history_trip_mileage_location_end'] = strtoupper($geonameend).",".$report['end_position']->display_name;
						}
						else
						{
							$insert_data['history_trip_mileage_location_end'] = $report['end_position']->display_name;
						}
						//print_r($insert_data);exit;
						$this->dbtrans->insert('transporter_history_trip_mileage_ppi', $insert_data);
						$j++;
					
				}
			}
		}
		
		$this->dbhistory->cache_delete_all();
		$this->db->cache_delete_all();
		$this->dbtrans->cache_delete_all();
		
		}
		$finish_time = date("d-m-Y H:i:s");
		echo "Finish..."." ".$finish_time;
		
		//Send Email
		$cron_name = "CRON HOURMETER PPI";
		
		unset($mail);
		$mail['subject'] =  "Report Cron - ".$cron_name."";
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK");
		
		return;   
	}
	
	//for ppi
	function history_hourmeter_manual_ppi($startdate = "2014-10-08 00:00:00", $enddate= "2014-10-08 23:59:59")
	{
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbhistory = $this->load->database("gpshistory",true);
		$this->dbhistory2 = $this->load->database("gpsarchive2",true);
	
		$starting_time = date("d-m-Y H:i:s");
		echo "Starting...\r\n"." ".$starting_time;

		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }

        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
		
		$this->db->where("vehicle_user_id", "1839");
		$q = $this->db->get('vehicle');
		$vehicle = $q->result();
		//print_r($rows);exit;

		for ($x=0;$x<count($vehicle);$x++)
		{
			if (isset($vehicle[$x]))
			{
				$vexplode = explode("@",$vehicle[$x]->vehicle_device);
				$vex = $vexplode[0]."@t5";
				$vdevice = $vehicle[$x]->vehicle_device;
			}
			else
			{
				echo "FINISH!!";
	
			}
			
			$this->db->where("vehicle_device", $vdevice);
			$this->db->limit(1);
			$qve = $this->db->get("vehicle");
			$rowv = $qve->row(); 
			
			$table = $vex."_gps";
			$tableinfo = $vex."_info";

			$this->dbhistory->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = '".$vdevice."'");
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
			$this->dbhistory->where("gps_time >=", $sdate);
			$this->dbhistory->where("gps_time <=", $edate);
			$this->dbhistory->order_by("gps_time", "asc");
			$q = $this->dbhistory->get($table);
			$rows1 = $q->result();
			//print_r($rows1);exit;
			
			$this->dbhistory2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = '".$vdevice."'");
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
			$this->dbhistory2->where("gps_time >=", $sdate);
			$this->dbhistory2->where("gps_time <=", $edate);
			$this->dbhistory2->order_by("gps_time", "asc");
			$q2 = $this->dbhistory2->get($table);
			$rows2 = $q2->result();
			//print_r($rows2);exit;
			
			$rows = array_merge($rows1, $rows2);
			//print_r($rows);exit;
			
			$data = array();
		
			$nopol = "";
		
			$on = false;
			$trows = count($rows);
			//print_r($trows);exit();
			
			for($i=0;$i<$trows;$i++){
			//if($rows[$i]->gps_speed == 0) continue;
			
			if($nopol != $rowv->vehicle_no){ //new vehicle
				if($on && $i!=0){ 
					$data[$rowv->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					$data[$rowv->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
					$data[$rowv->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
					$data[$rowv->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
				}
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){								
					$trip_no = 1;					
					$data[$rowv->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					$data[$rowv->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
					$data[$rowv->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
					$data[$rowv->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
					$data[$rowv->vehicle_no][$trip_no-1]['vehicle_name'] = $rowv->vehicle_name;
					$data[$rowv->vehicle_no][$trip_no-1]['vehicle_device'] = $rowv->vehicle_device;
						
					$on = true;
					
					if($i==$trows-1){												
						$data[$rowv->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowv->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
						$data[$rowv->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowv->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					$trip_no = 1;
					$on = false;
										
				}
				
				
			}else{ //same vehicle
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					if(!$on){	
												
						$data[$rowv->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowv->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
						$data[$rowv->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowv->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowv->vehicle_no][$trip_no-1]['vehicle_name'] = $rowv->vehicle_name;
						$data[$rowv->vehicle_no][$trip_no-1]['vehicle_device'] = $rowv->vehicle_device;
						
					}
					
					$on = true;	
					if($i==$trows-1 && $on){												
						$data[$rowv->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowv->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowv->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
						$data[$rowv->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}									
				}else{			
					if($on){
						$data[$rowv->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));	
						$data[$rowv->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowv->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowv->vehicle_device);
						$data[$rowv->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
					$on = false;
													
				}
			}
			
			$nopol = $rowv->vehicle_no;
			$vehicle_name = $rowv->vehicle_name;
			$vehicle_device = $rowv->vehicle_device;
		}
		
		if(count($data) > 0)
		{
			$j=1;
			$new = "";
			unset($insert_data);
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
					
						$duration = get_time_difference($report['start_time'], $report['end_time']);                
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
						$tm = $mileage/1000;
						$cumm += $tm;
						$insert_data['history_trip_mileage_vehicle_id'] = $rowv->vehicle_device;
						$insert_data['history_trip_mileage_vehicle_no'] = $rowv->vehicle_no;
						$insert_data['history_trip_mileage_vehicle_name'] = $rowv->vehicle_name;
						$insert_data['history_trip_mileage_trip_no'] = $trip_no++;
						$insert_data['history_trip_mileage_start_time'] = date("Y-m-d H:i:s", strtotime($report['start_time']));
						$insert_data['history_trip_mileage_end_time'] = date("Y-m-d H:i:s", strtotime($report['end_time']));
						$insert_data['history_trip_mileage_duration'] = $show;
						$insert_data['history_trip_mileage_trip_mileage'] = $tm;
						$insert_data['history_trip_mileage_cummulative_mileage'] = $cumm;
						
						if ($report['start_geofence_location']) 
						{
							$arrGeo = explode("#", $report['start_geofence_location']);
							if(count($arrGeo)>1)
							{
								$geoname = $arrGeo[1];
							}
							else
							{
								$geoname = $arrGeo[0];
							}
							
							$insert_data['history_trip_mileage_location_start'] = strtoupper($geoname).",".$report['start_position']->display_name;
						}
						else
						{
							$insert_data['history_trip_mileage_location_start'] = $report['start_position']->display_name;
						}
				
						if ($report['end_geofence_location']) 
						{
							$arrGeoEnd = explode("#", $report['end_geofence_location']);
							if(count($arrGeoEnd)>1)
							{
								$geonameend = $arrGeoEnd[1];
							}
							else
							{
								$geonameend = $arrGeoEnd[0];
							}
							
							$insert_data['history_trip_mileage_location_end'] = strtoupper($geonameend).",".$report['end_position']->display_name;
						}
						else
						{
							$insert_data['history_trip_mileage_location_end'] = $report['end_position']->display_name;
						}
						//print_r($insert_data);exit;
						$this->dbtrans->insert('transporter_history_trip_mileage_ppi', $insert_data);
						$j++;
					
				}
			}
		}
		
		$this->dbhistory->cache_delete_all();
		$this->db->cache_delete_all();
		$this->dbtrans->cache_delete_all();
		
		}
		$finish_time = date("d-m-Y H:i:s");
		echo "Finish...."." ".$finish_time;
		
		return;    
	}
	
	function hourmeter_ppi_dbport($startdate = "", $enddate= "")
	{
		ini_set('memory_limit', '-1');
		$start_time = date("d-m-Y H:i:s");
		$process_date = date("Y-m-d H:i:s");
		$report_type = "hourmeter";

		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }

        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		//print_r($startdate." ".$enddate);exit();
		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        $z =0;
		
		$this->db->order_by("vehicle_device", "asc");
		$this->db->where("vehicle_user_id", "1839");
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get('vehicle');
		$rowvehicle = $q->result();

		$total_process = count($rowvehicle);
		printf("STARTING TIME	 : %s \r\n",$start_time);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		
		$this->dbtrans = $this->load->database("transporter",true);

		for ($x=0;$x<count($rowvehicle);$x++)
		{
			
			printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
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
					
					//cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_ppi");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_id);     
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
                        //print_r($rows);exit;
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
			
						$data = array();
						$nopol = "";
						$on = false;
						$trows = count($rows);
						
						printf("TOTAL DATA : %s \r\n",$trows);
			
						for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }                                    
                                }
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
						
		
						if(count($data) > 0)
						{
							$j=1;
							$new = "";
							unset($insert_data);
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
									//if($mileage != 0) edit for ppi
									//{
										$duration = get_time_difference($report['start_time'], $report['end_time']);                
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
										$tm = $mileage/1000;
										$cumm += $tm;
										$insert_data['history_trip_mileage_vehicle_id'] = $vehicle_dev;
										$insert_data['history_trip_mileage_vehicle_no'] = $vehicle_no;
										$insert_data['history_trip_mileage_vehicle_name'] = $report['vehicle_name'];
										$insert_data['history_trip_mileage_trip_no'] = $trip_no++;
										$insert_data['history_trip_mileage_start_time'] = date("Y-m-d H:i:s", strtotime($report['start_time']));
										$insert_data['history_trip_mileage_end_time'] = date("Y-m-d H:i:s", strtotime($report['end_time']));
										$insert_data['history_trip_mileage_duration'] = $show;
										$insert_data['history_trip_mileage_trip_mileage'] = $tm;
										$insert_data['history_trip_mileage_cummulative_mileage'] = $cumm;
										
										if ($report['start_geofence_location']) 
										{
											$arrGeo = explode("#", $report['start_geofence_location']);
											if(count($arrGeo)>1)
											{
												$geoname = $arrGeo[1];
											}
											else
											{
												$geoname = $arrGeo[0];
											}
											
											$insert_data['history_trip_mileage_location_start'] = strtoupper($geoname).",".$report['start_position']->display_name;
										}
										else
										{
											$insert_data['history_trip_mileage_location_start'] = $report['start_position']->display_name;
										}
								
										if ($report['end_geofence_location']) 
										{
											$arrGeoEnd = explode("#", $report['end_geofence_location']);
											if(count($arrGeoEnd)>1)
											{
												$geonameend = $arrGeoEnd[1];
											}
											else
											{
												$geonameend = $arrGeoEnd[0];
											}
											
											$insert_data['history_trip_mileage_location_end'] = strtoupper($geonameend).",".$report['end_position']->display_name;
										}
										else
										{
											$insert_data['history_trip_mileage_location_end'] = $report['end_position']->display_name;
										}
										
										$this->dbtrans->insert('history_trip_mileage_ppi', $insert_data);
										printf("INSERT DBM OK \r\n");
										printf("DELETE CACHE HISTORY \r\n");
										$this->dbhist->cache_delete_all();
										$this->dbtrans->cache_delete_all();
										
										unset($data);
										printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
										printf("============================================ \r\n");
									}
								}
							//} edit for ppi
						}
						
							printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
							$data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
							$data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
							$data_insert["autoreport_data_startdate"] = $startdate;
							$data_insert["autoreport_data_enddate"] = $enddate;
							$data_insert["autoreport_type"] = $report_type;
							$data_insert["autoreport_process_date"] = $process_date;
							$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
							if ($trows > 0){
								$this->dbtrans->insert("autoreport_ppi",$data_insert);
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
			
        
		$finish_time = date("d-m-Y H:i:s");
		printf("DONE TRIP MILEAGE DB PORT: %s \r\n",$finish_time);
		
		//count data
		$this->dbtrans->select("history_trip_mileage_vehicle_id");
		$this->dbtrans->where("history_trip_mileage_start_time >=", $startdate);
        $this->dbtrans->where("history_trip_mileage_end_time <=", $enddate);
        $qtrip = $this->dbtrans->get("history_trip_mileage_ppi");
        $rowtrip = $qtrip->result();
        
        $total_trip = count($rowtrip);
		//Send Email
		$cron_name = "PPI - CRON HOUR METER DB PORT";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_trip."
End Data   : "."( ".$z." / ".$total_process." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		return;   
	}
	
	//edit manual
	function hourmeter_ppi_dbport_manual_archive($startdate = "2014-11-01 00:00:00", $enddate= "2014-11-01 23:59:59")
	{
		ini_set('memory_limit', '-1');
		$start_time = date("d-m-Y H:i:s");
		$process_date = date("Y-m-d H:i:s");
		$report_type = "hourmeter";

		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }

        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		//print_r($startdate." ".$enddate);exit();
		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        $z =0;
		
		$this->db->order_by("vehicle_device", "asc");
		$this->db->where("vehicle_user_id", "1839");
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get('vehicle');
		$rowvehicle = $q->result();

		$total_process = count($rowvehicle);
		printf("STARTING TIME	 : %s \r\n",$start_time);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		
		$this->dbtrans = $this->load->database("transporter",true);

		for ($x=0;$x<count($rowvehicle);$x++)
		{
			
			printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
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
                    $qrpt = $this->dbtrans->get("autoreport_ppi");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_id);     
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
                        //print_r($rows);exit;
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
			
						$data = array();
						$nopol = "";
						$on = false;
						$trows = count($rows);
						
						printf("TOTAL DATA : %s \r\n",$trows);
			
						for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }                                    
                                }
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
						
		
						if(count($data) > 0)
						{
							$j=1;
							$new = "";
							unset($insert_data);
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
									//if($mileage != 0) edit for ppi
									//{
										$duration = get_time_difference($report['start_time'], $report['end_time']);                
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
										$tm = $mileage/1000;
										$cumm += $tm;
										$insert_data['history_trip_mileage_vehicle_id'] = $vehicle_dev;
										$insert_data['history_trip_mileage_vehicle_no'] = $vehicle_no;
										$insert_data['history_trip_mileage_vehicle_name'] = $report['vehicle_name'];
										$insert_data['history_trip_mileage_trip_no'] = $trip_no++;
										$insert_data['history_trip_mileage_start_time'] = date("Y-m-d H:i:s", strtotime($report['start_time']));
										$insert_data['history_trip_mileage_end_time'] = date("Y-m-d H:i:s", strtotime($report['end_time']));
										$insert_data['history_trip_mileage_duration'] = $show;
										$insert_data['history_trip_mileage_trip_mileage'] = $tm;
										$insert_data['history_trip_mileage_cummulative_mileage'] = $cumm;
										
										if ($report['start_geofence_location']) 
										{
											$arrGeo = explode("#", $report['start_geofence_location']);
											if(count($arrGeo)>1)
											{
												$geoname = $arrGeo[1];
											}
											else
											{
												$geoname = $arrGeo[0];
											}
											
											$insert_data['history_trip_mileage_location_start'] = strtoupper($geoname).",".$report['start_position']->display_name;
										}
										else
										{
											$insert_data['history_trip_mileage_location_start'] = $report['start_position']->display_name;
										}
								
										if ($report['end_geofence_location']) 
										{
											$arrGeoEnd = explode("#", $report['end_geofence_location']);
											if(count($arrGeoEnd)>1)
											{
												$geonameend = $arrGeoEnd[1];
											}
											else
											{
												$geonameend = $arrGeoEnd[0];
											}
											
											$insert_data['history_trip_mileage_location_end'] = strtoupper($geonameend).",".$report['end_position']->display_name;
										}
										else
										{
											$insert_data['history_trip_mileage_location_end'] = $report['end_position']->display_name;
										}
										
										$this->dbtrans->insert('history_trip_mileage_ppi', $insert_data);
										printf("INSERT DBM OK \r\n");
										printf("DELETE CACHE HISTORY \r\n");
										$this->dbhist->cache_delete_all();
										$this->dbtrans->cache_delete_all();
										
										unset($data);
										printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
										printf("============================================ \r\n");
									}
								}
							//} edit for ppi
						}
						
							printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
							$data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
							$data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
							$data_insert["autoreport_data_startdate"] = $startdate;
							$data_insert["autoreport_data_enddate"] = $enddate;
							$data_insert["autoreport_type"] = $report_type;
							$data_insert["autoreport_process_date"] = $process_date;
							$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
							if ($trows > 0){
								$this->dbtrans->insert("autoreport_ppi",$data_insert);
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
			
        
		$finish_time = date("d-m-Y H:i:s");
		printf("DONE TRIP MILEAGE DB PORT: %s \r\n",$finish_time);
		
		//count data
		$this->dbtrans->select("history_trip_mileage_vehicle_id");
		$this->dbtrans->where("history_trip_mileage_start_time >=", $startdate);
        $this->dbtrans->where("history_trip_mileage_end_time <=", $enddate);
        $qtrip = $this->dbtrans->get("history_trip_mileage_ppi");
        $rowtrip = $qtrip->result();
        
        $total_trip = count($rowtrip);
		//Send Email
		$cron_name = "PPI - CRON HOUR METER DB PORT";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_trip."
End Data   : "."( ".$z." / ".$total_process." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		return;   
	}
	
	function hourmeter_ppi_dbport_manual_history($startdate = "2015-01-21 00:00:00", $enddate= "2015-01-21 23:59:59")
	{
		ini_set('memory_limit', '-1');
		$start_time = date("d-m-Y H:i:s");
		$process_date = date("Y-m-d H:i:s");
		$report_type = "hourmeter";

		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }

        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		//print_r($startdate." ".$enddate);exit();
		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        $z =0;
		
		$this->db->order_by("vehicle_device", "asc");
		$this->db->where("vehicle_user_id", "1839");
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get('vehicle');
		$rowvehicle = $q->result();

		$total_process = count($rowvehicle);
		printf("STARTING TIME	 : %s \r\n",$start_time);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		
		$this->dbtrans = $this->load->database("transporter",true);

		for ($x=0;$x<count($rowvehicle);$x++)
		{
			
			printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
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
					
					//cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_ppi");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_id);     
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
                        //print_r($rows);exit;
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
			
						$data = array();
						$nopol = "";
						$on = false;
						$trows = count($rows);
						
						printf("TOTAL DATA : %s \r\n",$trows);
			
						for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }                                    
                                }
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
						
		
						if(count($data) > 0)
						{
							$j=1;
							$new = "";
							unset($insert_data);
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
									//if($mileage != 0) edit for ppi
									//{
										$duration = get_time_difference($report['start_time'], $report['end_time']);                
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
										$tm = $mileage/1000;
										$cumm += $tm;
										$insert_data['history_trip_mileage_vehicle_id'] = $vehicle_dev;
										$insert_data['history_trip_mileage_vehicle_no'] = $vehicle_no;
										$insert_data['history_trip_mileage_vehicle_name'] = $report['vehicle_name'];
										$insert_data['history_trip_mileage_trip_no'] = $trip_no++;
										$insert_data['history_trip_mileage_start_time'] = date("Y-m-d H:i:s", strtotime($report['start_time']));
										$insert_data['history_trip_mileage_end_time'] = date("Y-m-d H:i:s", strtotime($report['end_time']));
										$insert_data['history_trip_mileage_duration'] = $show;
										$insert_data['history_trip_mileage_trip_mileage'] = $tm;
										$insert_data['history_trip_mileage_cummulative_mileage'] = $cumm;
										
										if ($report['start_geofence_location']) 
										{
											$arrGeo = explode("#", $report['start_geofence_location']);
											if(count($arrGeo)>1)
											{
												$geoname = $arrGeo[1];
											}
											else
											{
												$geoname = $arrGeo[0];
											}
											
											$insert_data['history_trip_mileage_location_start'] = strtoupper($geoname).",".$report['start_position']->display_name;
										}
										else
										{
											$insert_data['history_trip_mileage_location_start'] = $report['start_position']->display_name;
										}
								
										if ($report['end_geofence_location']) 
										{
											$arrGeoEnd = explode("#", $report['end_geofence_location']);
											if(count($arrGeoEnd)>1)
											{
												$geonameend = $arrGeoEnd[1];
											}
											else
											{
												$geonameend = $arrGeoEnd[0];
											}
											
											$insert_data['history_trip_mileage_location_end'] = strtoupper($geonameend).",".$report['end_position']->display_name;
										}
										else
										{
											$insert_data['history_trip_mileage_location_end'] = $report['end_position']->display_name;
										}
										
										$this->dbtrans->insert('history_trip_mileage_ppi', $insert_data);
										printf("INSERT DBM OK \r\n");
										printf("DELETE CACHE HISTORY \r\n");
										$this->dbhist->cache_delete_all();
										$this->dbtrans->cache_delete_all();
										
										unset($data);
										printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
										printf("============================================ \r\n");
									}
								}
							//} edit for ppi
						}
						
							printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
							$data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
							$data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
							$data_insert["autoreport_data_startdate"] = $startdate;
							$data_insert["autoreport_data_enddate"] = $enddate;
							$data_insert["autoreport_type"] = $report_type;
							$data_insert["autoreport_process_date"] = $process_date;
							$data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
							if ($trows > 0){
								$this->dbtrans->insert("autoreport_ppi",$data_insert);
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
			
        
		$finish_time = date("d-m-Y H:i:s");
		printf("DONE TRIP MILEAGE DB PORT: %s \r\n",$finish_time);
		
		//count data
		$this->dbtrans->select("history_trip_mileage_vehicle_id");
		$this->dbtrans->where("history_trip_mileage_start_time >=", $startdate);
        $this->dbtrans->where("history_trip_mileage_end_time <=", $enddate);
        $qtrip = $this->dbtrans->get("history_trip_mileage_ppi");
        $rowtrip = $qtrip->result();
        
        $total_trip = count($rowtrip);
		//Send Email
		$cron_name = "PPI - CRON HOUR METER DB PORT";
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_trip."
End Data   : "."( ".$z." / ".$total_process." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
		
		printf("SEND EMAIL OK \r\n");
		
		return;   
	}
	
	//ppi
	function daily_hourmeter_ppi($startdate= "", $enddate= ""){
		
		$start_time = date("d-m-Y H:i:s");
		$offset = 0;
		$i = 0;
		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }

        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
		$this->db->order_by("vehicle_device", "desc");
		$this->db->where("vehicle_user_id", "1839");
		$q = $this->db->get('vehicle');
		$vehicle = $q->result();
		$totalv = count($vehicle);
		for ($x=0;$x<count($vehicle);$x++)
		{
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
		
			//print_r($vehicle);exit;
			if (isset($vehicle[$x]))
			{
				$vdevice = $vehicle[$x]->vehicle_device;
				$vid = $vehicle[$x]->vehicle_id;
				$vname = $vehicle[$x]->vehicle_name;
				$vno = $vehicle[$x]->vehicle_no; 
			}
			else
			{
				echo "FINISH!!";
	
			}
			
			$this->dbtrans = $this->load->database("transporter",true);
			$this->dbtrans->order_by("history_trip_mileage_start_time","asc");
			$this->dbtrans->where("history_trip_mileage_vehicle_id", $vdevice);
			$this->dbtrans->where("history_trip_mileage_start_time >=",$startdate);
			$this->dbtrans->where("history_trip_mileage_end_time <=", $enddate);
			$q = $this->dbtrans->get("history_trip_mileage_ppi");
			$rows = $q->result();
			//print_r($rows);exit();
			
			$cummulative = 0;
			$tot_hour_daily = 0;
			$tot_dur_daily = 0;
			$string_cum = "";
			//daily
			if ((isset($rows)) && (count($rows)>0))
			{

				//printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$totalv);
				printf("VEHICLE : %s \r\n", $vdevice);
				
				for ($i=0;$i<count($rows);$i++)
				{
					$dur = $rows[$i]->history_trip_mileage_duration;
					$tot_dur_daily = $tot_dur_daily + $rows[$i]->history_trip_mileage_duration;
				
					$ex = explode(" ",$dur);
					if (isset($ex[1]) && ($ex[1] == "Min"))
					{
						$detik = $ex[0] * 60;
					}
					elseif (isset($ex[1]) && ($ex[1] == "Hour"))
					{
					
						$detik = $ex[0] * 60 * 60;
						if (isset($ex[2]))
						{
							$det = $ex[2] * 60;
							$detik  = $detik + $det;
						}
					}
					//satuan detik
					$tot_hour_daily = $tot_hour_daily + $detik; 
					//print_r($tot_hour_daily);exit();
				}
			}
			
			//get data hourmeter
			$this->dbtrans = $this->load->database("transporter",true);
			$this->dbtrans->order_by("data_hm_daily_datetime", "desc");
			$this->dbtrans->where("data_hm_daily_vehicle_device", $vdevice);
			$this->dbtrans->limit(1);
			$q_hm = $this->dbtrans->get("transporter_data_hm_daily_ppi");
			$rows_hm = $q_hm->row();
			//print_r($rows_hm);exit();
			
			//if ($rows_hm->data_hm_datetime < $startdate)
		
				if ((isset($rows_hm)) && (count($rows_hm)>0)){
					$value_lastday = $rows_hm->data_hm_daily_cum;
				}else{
					$value_lastday = 0;
				}
				
				$cummulative = $tot_hour_daily + $value_lastday;
				//print_r($commulative);exit();
				
										$value_hour = "";
										$value_min = "";
										
										$conval = $cummulative;
										$seconds = $conval;
										
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
												$value_hour = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$value_hour = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$value_min = $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$value_min = $minutes." "."Minutes"." ";
											}
										}
										
									
					$string_cum = $value_hour." ".$value_min;	

			
			//insert ke daily hourmeter
			unset($rows);
			$rows['data_hm_daily_vehicle_id'] = $vid;
			$rows['data_hm_daily_vehicle_name'] = $vname;
			$rows['data_hm_daily_vehicle_no'] = $vno;
			$rows['data_hm_daily_vehicle_device'] = $vdevice;
            $rows['data_hm_daily_datetime'] = $enddate;
			$rows['data_hm_daily_value_sec'] = $tot_hour_daily;
			$rows['data_hm_daily_string_cum'] = $string_cum;
			if ((isset($rows_hm)) && (count($rows_hm)>0)){
					$rows['data_hm_daily_cum'] = $cummulative;
				}else{
					$rows['data_hm_daily_cum'] = $tot_hour_daily;
			}
			
			//insert / update
			if ((isset($rows_hm)) && (count($rows_hm)>0))
			{
				printf("UPDATE HOURMETER : %s \r\n", $vdevice); 
				$this->dbtrans->where("data_hm_daily_id", $rows_hm->data_hm_daily_id);
				$this->dbtrans->update("transporter_data_hm_daily_ppi",$rows);
			}
			else
			{
				printf("INSERT HOURMETER  : %s \r\n", $vdevice); 
				$this->dbtrans->insert("transporter_data_hm_daily_ppi",$rows);
			}
				
			$this->dbtrans->close();
			
			printf("FINISH 	: %s \r\n", $vdevice);
			printf("================================= \r\n");
		}
		
		//Send Email
		$cron_name = "Daily Hourmeter PPI";
		$finish_time = date("d-m-Y H:i:s");
		
		unset($mail);
		$mail['subject'] =  "Report Cron - ".$cron_name."";
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK \r\n");
			
		printf("DONE !! \r\n");
			

	}
	
	function alert_hourmeter_ppi($startdate= "", $enddate= ""){
		
		$start_time = date("d-m-Y H:i:s");
		$offset = 0;
		$i = 0;

		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }

        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
		$this->db->order_by("vehicle_device", "desc");
		$this->db->where("vehicle_user_id", "1839");
		$this->db->where("vehicle_status <>", "3");
		$q = $this->db->get('vehicle');
		$vehicle = $q->result();
		$totalv = count($vehicle);
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("data_hm_vehicle_device", "desc");
		$this->dbtransporter->where("data_hm_flag", 0);
		$q_lastservice = $this->dbtransporter->get("data_hm_ppi");
		$row_lastservice = $q_lastservice->result();
		
		for ($x=0;$x<count($vehicle);$x++)
		{
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
		
			//print_r($vehicle);exit;
			if (isset($vehicle[$x]))
			{
				$vdevice = $vehicle[$x]->vehicle_device;
				$vid = $vehicle[$x]->vehicle_id;
				$vname = $vehicle[$x]->vehicle_name;
				$vno = $vehicle[$x]->vehicle_no; 
			}
			else
			{
				echo "FINISH!!";
	
			}
			if (isset($row_lastservice[$x]))
			{
				$last_vdevice = $row_lastservice[$x]->data_hm_vehicle_device;
				$last_vid = $row_lastservice[$x]->data_hm_vehicle_id;
				$last_vservice = date("Y-m-d 00:00:00", strtotime($row_lastservice[$x]->data_hm_last_service . " " . "00:00:00"));
				$last_vvalue = $row_lastservice[$x]->data_hm_last_service_value;
				
			}
			else
			{
				echo "FINISH!!";
	
			}
			
			$this->dbtransporter->order_by("history_trip_mileage_start_time","asc");
			
			$this->dbtransporter->where("history_trip_mileage_vehicle_id", $vdevice);
			$this->dbtransporter->where("history_trip_mileage_start_time >=",$last_vservice);
			$this->dbtransporter->where("history_trip_mileage_end_time <=", $enddate);
			$q = $this->dbtransporter->get("history_trip_mileage_ppi");
			$rows = $q->result();
			//print_r($rows);exit();
			
			$cummulative = 0;
			$tot_hour_daily = 0;
			$tot_dur_daily = 0;
			$string_cum = "";
			//daily
			if ((isset($rows)) && (count($rows)>0))
			{

				//printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$totalv);
				printf("VEHICLE : %s \r\n", $vdevice);
				
				for ($i=0;$i<count($rows);$i++)
				{
					$dur = $rows[$i]->history_trip_mileage_duration;
					$tot_dur_daily = $tot_dur_daily + $rows[$i]->history_trip_mileage_duration;
				
					$ex = explode(" ",$dur);
					if (isset($ex[1]) && ($ex[1] == "Min"))
					{
						$detik = $ex[0] * 60;
					}
					elseif (isset($ex[1]) && ($ex[1] == "Hour"))
					{
					
						$detik = $ex[0] * 60 * 60;
						if (isset($ex[2]))
						{
							$det = $ex[2] * 60;
							$detik  = $detik + $det;
						}
					}
					//satuan detik
					$tot_hour_daily = $tot_hour_daily + $detik; 
					
				}
			}
			
			//get data hourmeter
			$this->dbtransporter = $this->load->database("transporter",true);
			$this->dbtransporter->order_by("data_hm_vehicle_device", "desc");
			$this->dbtransporter->where("data_hm_vehicle_device", $vdevice);
			$q_hm = $this->dbtransporter->get("data_hm_ppi");
			$rows_hm = $q_hm->row();
			//print_r($rows_hm);exit();
			
			//if ($rows_hm->data_hm_datetime < $startdate)
		
				if ((isset($rows_hm)) && (count($rows_hm)>0)){
					$value_lastsvc = $rows_hm->data_hm_last_service_value * 3600;
				}else{
					$value_lastsvc = 0;
				}
				
				$cummulative = $tot_hour_daily + $value_lastsvc;
				//dari detik convert ke jam 
				$tot_hour_cum = $cummulative / 3600;
				$tot_hour_daily_cum = $tot_hour_daily / 3600;
				//print_r($tot_hour_daily_cum);exit();
				
										$value_hour = "";
										$value_min = "";
										
										$conval = $cummulative;
										$seconds = $conval;
										
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
												$value_hour = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$value_hour = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$value_min = $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$value_min = $minutes." "."Minutes"." ";
											}
										}
										
									
					$string_cum = $value_hour." ".$value_min;	

			//get alert hourmeter
			$this->dbtransporter->order_by("hm_alert_vehicle_device", "desc");
			$this->dbtransporter->where("hm_alert_vehicle_device", $vdevice);
			$q_alert = $this->dbtransporter->get("data_hm_alert_ppi");
			$rows_alert = $q_alert->row();
			
			//get config hourmeter
			/* $this->dbtransporter->order_by("hm_config_vehicle_device", "desc");
			$this->dbtransporter->where("hm_config_vehicle_device", $vdevice);
			$q_config = $this->dbtransporter->get("data_hm_config_ppi");
			$rows_config = $q_config->row();
			$config = $rows_config->hm_config_value;
			$config_min = $rows_config->hm_config_value - 20; */
			$config = 250;
			$config_min = 230;
			
			//insert ke alert hourmeter
			unset($rows);
			$rows['hm_alert_vehicle_id'] = $vid;
			$rows['hm_alert_vehicle_name'] = $vname;
			$rows['hm_alert_vehicle_no'] = $vno;
			$rows['hm_alert_vehicle_device'] = $vdevice;
            $rows['hm_alert_datetime'] = $enddate;
			$rows['hm_alert_value'] = $tot_hour_cum;
			$rows['hm_alert_string'] = $string_cum;
			
			if ($tot_hour_daily_cum > $config_min && $tot_hour_daily_cum < $config)
			{
				$rows['hm_alert_status'] = 1;
			}
			if ($tot_hour_daily_cum > $config)
			{
				$rows['hm_alert_status'] = 2;
			}
			
			//insert / update
			if ((isset($rows_alert)) && (count($rows_alert)>0))
			{
				
				printf("UPDATE ALERT HOURMETER : %s \r\n", $vdevice); 
				$this->dbtransporter->where("hm_alert_vehicle_id", $vid);
				$this->dbtransporter->update("data_hm_alert_ppi",$rows);
			}
			else
			{
				printf("INSERT ALERT HOURMETER  : %s \r\n", $vdevice); 
				$this->dbtransporter->insert("data_hm_alert_ppi",$rows);
			}
				
			$this->dbtransporter->close();
			
			printf("FINISH 	: %s \r\n", $vdevice);
			printf("================================= \r\n");
		}
		
		//Send Email
		/* $cron_name = "Alert Hourmeter PPI";
		$finish_time = date("d-m-Y H:i:s");
		
		unset($mail);
		$mail['subject'] =  "Report Cron - ".$cron_name."";
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "cron@lacak-mobil.com"; */
		//lacakmobilmail($mail);
			
		//printf("Send Email OK \r\n");
			
		printf("DONE !! \r\n");
			

	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
