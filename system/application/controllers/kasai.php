<?php
include "base.php";

class Kasai extends Base {

	function Kasai()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}
	
	function mn_daily_report()
	{
		if (! isset($this->sess->user_type)) { redirect(base_url()); }
		
		$this->db->select("vehicle.*, user_name");		
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		
		if ($this->sess->user_type == 2)
		{			
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);			
		}
		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}
		
		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('transporter/report/dailyreport_kasai', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}
	
	function daily_report()
	{
		if (! isset($this->sess->user_type)) { redirect(base_url()); }
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		
		$sdate = date("Y-m-d H:i:s",strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s",strtotime($startdate." "."23:59:59"));
		
		//get week
		$tgl = date("d",strtotime($startdate));
		$month = date("m",strtotime($startdate));
		$year = date("Y",strtotime($startdate));
		
		$ew = date("Y-m-d H:i:s",strtotime("-1 day", strtotime($startdate." "."00:00:00")));
		$dew = date("d",strtotime($ew));
			
		if($tgl >= 1 && $tgl <= 7)
		{
			$start_week = date("Y-m-d H:i:s",strtotime("01-".$month."-".$year." "."00:00:00"));
		}	
		if($tgl > 7 && $tgl <= 14)
		{
			$start_week = date("Y-m-d H:i:s",strtotime("08-".$month."-".$year." "."00:00:00"));
		}
		if($tgl > 14 && $tgl <= 21)
		{
			$start_week = date("Y-m-d H:i:s",strtotime("15-".$month."-".$year." "."00:00:00"));
		}
		if($tgl > 21 && $tgl <= 31)
		{
			$start_week = date("Y-m-d H:i:s",strtotime("22-".$month."-".$year." "."00:00:00"));
		}
		$end_week = date("Y-m-d H:i:s",strtotime($dew."-".$month."-".$year." "."23:59:59"));
		
		$total_day = cal_days_in_month(CAL_GREGORIAN,$month,$year);
		
		$start_month = date("Y-m-d H:i:s",strtotime("01-".$month."-".$year." "."00:00:00"));
		$end_month = date("Y-m-d H:i:s",strtotime($dew."-".$month."-".$year." "."23:59:59"));
		
		//Get Vehicle
		$this->db->select("vehicle_device,vehicle_no,vehicle_name");
		$this->db->where("vehicle_status",1);
		$this->db->where("vehicle_user_id",$this->sess->user_id);
		$this->db->where("vehicle_device",$vehicle);
		$q = $this->db->get("vehicle");
		$vehicle = $q->result();
		
		$this->dbkasai = $this->load->database("kasai_report",true);
		
		
		for($i=0;$i<count($vehicle);$i++)
		{
			$tw = 0; $tm = 0;
			$vehicle[$i]->sdt = ""; //Traveltime Start Time
			$vehicle[$i]->edt = ""; //Traveltime End Time
			$vehicle[$i]->total_time = "";
			$vehicle[$i]->slocation = "";
			$vehicle[$i]->elocation = "";
			$vehicle[$i]->sodo = 0;
			$vehicle[$i]->eodo = 0;
			$vehicle[$i]->daily_distance = 0;
			$vehicle[$i]->odo_week = 0;
			$vehicle[$i]->odo_month = 0;
				
			$this->dbkasai->order_by("trip_mileage_start_time","asc");
			$this->dbkasai->where("trip_mileage_vehicle_id",$vehicle[$i]->vehicle_device);
			$this->dbkasai->where("trip_mileage_start_time >=",$sdate);
			$this->dbkasai->where("trip_mileage_start_time <=",$edate);
			$q = $this->dbkasai->get("tripmileage");
			$trip  = $q->result();
			if(count($trip) > 0)
			{
				$vehicle[$i]->tripmileage = $trip;
				$vehicle[$i]->sdt = date("Y-m-d H:i:s",strtotime($trip[0]->trip_mileage_start_time));
				$vehicle[$i]->edt = date("Y-m-d H:i:s",strtotime($trip[count($trip)-1]->trip_mileage_end_time));
				
				$xtime = $trip[count($trip)-1]->trip_mileage_duration_second;
				
				$xtime = 0; $dailyodo = 0;
				foreach($trip as $ss)
				{
					$xtime = $xtime + $ss->trip_mileage_duration_second;
					$dailyodo = $dailyodo + $ss->trip_mileage_trip_mileage;
				}
				$xtime = $this->sec2hms($xtime);
				$vehicle[$i]->total_time = $xtime;
				$vehicle[$i]->slocation = $trip[0]->trip_mileage_location_start;
				$vehicle[$i]->elocation = $trip[count($trip)-1]->trip_mileage_location_end;
				$vehicle[$i]->sodo = $trip[0]->trip_mileage_trip_mileage;
				$vehicle[$i]->eodo = $trip[count($trip)-1]->trip_mileage_cummulative_mileage;
				$vehicle[$i]->daily_distance = $dailyodo;
			}
			
			$this->dbkasai->order_by("trip_mileage_start_time","asc");
			$this->dbkasai->where("trip_mileage_vehicle_id",$vehicle[$i]->vehicle_device);
			$this->dbkasai->where("trip_mileage_start_time >=",$start_week);
			$this->dbkasai->where("trip_mileage_end_time <=",$end_week);
			$q = $this->dbkasai->get("tripmileage");
			$trip  = $q->result();
			if(count($trip) > 0)
			{
				foreach($trip as $v)
				{
					$tw = $tw + $v->trip_mileage_trip_mileage;
				}
				$vehicle[$i]->odo_week = $tw;
			}
			
			$this->dbkasai->order_by("trip_mileage_start_time","asc");
			$this->dbkasai->where("trip_mileage_vehicle_id",$vehicle[$i]->vehicle_device);
			$this->dbkasai->where("trip_mileage_start_time >=",$start_month);
			$this->dbkasai->where("trip_mileage_end_time <=",$end_month);
			$q = $this->dbkasai->get("tripmileage");
			$trip  = $q->result();
			if(count($trip) > 0)
			{
				foreach($trip as $v)
				{
					$tm = $tm + $v->trip_mileage_trip_mileage;
				}
				$vehicle[$i]->odo_month = $tm;
			}
			
		}
		
		$params['vehicle'] = $vehicle;
		$html = $this->load->view("transporter/report/dailyreport_result_kasai", $params, true);
		
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		
	}

	function trip_mileage($userid="3721", $startdate = "", $enddate = "", $name = "", $host = "")
    {
        printf("PROSES AUTO REPORT TRIP MILEAGE KHUSUS KASAI >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        $report_type = "trip_mileage";
        $process_date = date("Y-m-d H:i:s");
        $domain_server = "http://202.129.190.194/";
		$report = "tripmileage_";
        
        if ($startdate == "") 
        {
			$startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        $z =0;
		
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
		
		$this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
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
						
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
						}
						$this->dbhist2 = $this->load->database($istbl_history, TRUE);
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
									if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
									{
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
									}
                                    else
                                    {
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_latitude);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
									}
                                    
                                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;     
                                    if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
                                    {
										$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
									} 
									else
									{
										$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
									}              
                                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    { 
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}
										else
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}                                                                                       
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
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}
                                        else
                                        {
											$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                         
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}   
										else
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}                    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }                                    
                                }
                                else
                                {            
                                    if($on)
                                    {
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}
										else
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));    
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data
                        $i=1;
                        $new = "";
                        printf("WRITE DATA EXCEL : ");
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
                                if($mileage != 0)
                                {
                                    $duration = get_time_difference($report['start_time'], $report['end_time']);
                                    $durationsecond = dbmaketime($report['end_time']) - dbmaketime($report['start_time']);
                                    
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
                                    
                                    if($tm < 0) { $tm = 0; }
                                    
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
                                    
                                    if( $x_mile < 0) {  $x_mile = 0; }
                            
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
									
									if( $x_cum < 0) {  $x_cum = 0; }
									
									unset($datainsert);
									$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
									$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
									$datainsert["trip_mileage_vehicle_name"] = $report['vehicle_name'];
									$datainsert["trip_mileage_trip_no"] = $notrip;
									$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report['start_time']));
									$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report['end_time']));
									$datainsert["trip_mileage_duration"] = $show;
									$datainsert["trip_mileage_duration_second"] = $durationsecond;
									$datainsert["trip_mileage_trip_mileage"] = $x_mile;
									$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
									$datainsert["trip_mileage_location_start"] = $report['start_position']->display_name;
									$datainsert["trip_mileage_location_end"] = $report['end_position']->display_name;
									$datainsert["trip_mileage_latlng_start"] = $report['start_latlng'];
									$datainsert["trip_mileage_latlng_end"] = $report['end_latlng'];
									$datainsert["trip_mileage_odo_before"] = $report['start_mileage'];
									$datainsert["trip_mileage_odo_after"] = $report['end_mileage'];
									
									
									$this->dbtrip = $this->load->database("kasai_report",TRUE);
									$this->dbtrip->insert("tripmileage",$datainsert);
									
                                    $i++;
                                }
                            }
                        }
                        
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
            
                        printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
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
        
		$finishtime = date("d-m-Y H:i:s");
        printf("AUTOREPORT TRIP MILEAGE DONE %s\r\n",$finishtime);
        
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
	
	function sec2hms($secs) {
    $secs = round($secs);
    $secs = abs($secs);
    $hours = floor($secs / 3600) . ':';
    if ($hours == '0:') $hours = '';
    $minutes = substr('00' . floor(($secs / 60) % 60), -2) . ':';
    $seconds = substr('00' . $secs % 60, -2);
	return ltrim($hours . $minutes . $seconds, '0');
	}

}

