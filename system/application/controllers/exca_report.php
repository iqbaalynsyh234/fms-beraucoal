<?php
include "base.php";

class Exca_report extends Base {
	function __construct()
	{
		parent::__construct();

		$this->load->model("gpsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');

	}
	
	function hour_report($userid="", $imeiexca="", $hostexca="", $order="asc", $date="", $radiusmeter="")
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
		$report = "location_";
		
		$month = date("F", strtotime($startdate));
		$year = date("Y", strtotime($startdate));
		
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

		$hour_selected = array("09:00:00","10:00:00","11:00:00","12:00:00","13:00:00","14:00:00","15:00:00","16:00:00","17:00:00","18:00:00");
		$this->dbts = $this->load->database("webtracking_ts", TRUE);
		$this->dbts->order_by("hour_name","asc");
		$this->dbts->where("hour_user", $userid);
		$this->dbts->where_in("hour_time", $hour_selected);
		$qhour = $this->dbts->get("ts_hour");
		$rowshour = $qhour->result();
		$totalhour = count($rowshour);
	
		
		for ($i=0;$i<$totalhour;$i++)
		{
			printf("===PROCESS HOUR %s \r\n", $rowshour[$i]->hour_name, $date);
			$sdate = date("Y-m-d H:i:s",strtotime($startdate." ".$rowshour[$i]->hour_time));
			$edate = date("Y-m-d H:i:s",strtotime($startdate." ".$rowshour[$i]->hour_value.":59:59"));
			printf("===STARTDATE %s, ENDDATE %s \r\n", $sdate, $edate);
			
			$datareport = $this->dataradius_hour_bylocationreport($userid,$imeiexca,$hostexca,$order,$sdate,$edate,$rowshour[$i]->hour_name,$dbtable,$radiusmeter);
			
		}
		
		$finishtime = date('Y-m-d H:i:s');
		printf("===FINISH AUTOCHECK RADIUS HOUR DATA %s to %s \r\n", $nowtime, $finishtime);
		printf("============================== \r\n");
		
		//send telegram
		$title_name = "AUTOCHECK RADIUS PER HOUR ".$imeiexca;
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
	
	function dataradius_hour_bylocationreport($userid,$imeiexca,$hostexca,$order,$sdate,$edate,$hour,$dbtable_location,$radiusmeter)
	{
		$excadata = array('location','location_idle');
		$this->dbreport = $this->load->database("tensor_report",true);
		/* $this->dbreport->select("location_report_id,location_report_gps_time,location_report_vehicle_device,location_report_vehicle_no,
								 location_report_vehicle_company,location_report_geofence_name,location_report_latitude,
								 location_report_longitude","location_report_vehicle_name","location_report_vehicle_user_id","location_report_vehicle_id",
								 "location_report_vehicle_company","location_report_coordinate",""
								); */
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_device", $imeiexca."@".$hostexca);
		$this->dbreport->where("location_report_gps_time >=", $sdate);
		$this->dbreport->where("location_report_gps_time <=", $edate);
		//$this->dbreport->where("location_report_gps_cs", 53); //PTO ON
		$this->dbreport->where_in("location_report_name", $excadata); //data move dan idle
		//$this->dbreport->limit(3); //PTO ON
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		if(count($rows_loc)>0){

			$data_radius = array();
			for($x=0;$x<$total_loc;$x++)
			{
				$norut = $x+1;
				printf("===DATA LOCATION WITH PTO ON ke %s of %s \r\n", $norut, $total_loc);
				$id_report_loc = $rows_loc[$x]->location_report_id;
				$host_starttime = $rows_loc[$x]->location_report_gps_time;
				$host_endtime = date("Y-m-d H:i:s", strtotime("+1 minute", strtotime($host_starttime)));
				$host_geofence = $rows_loc[$x]->location_report_geofence_name;
				$host_latitude = $rows_loc[$x]->location_report_latitude;
				$host_longitude = $rows_loc[$x]->location_report_longitude;
				$host_company = $rows_loc[$x]->location_report_vehicle_company;
				$host_no = $rows_loc[$x]->location_report_vehicle_no;
				
				
				
				//FIND DT IN THE SAME GEOFENCE WITH THE EXCA
				printf("===HOST DATA LOC IN %s %s %s %s \r\n", $host_no,$host_geofence,$host_starttime,$host_endtime);
				$guestdata = $this->getLocationReport_bygeofence($host_geofence,$host_starttime,$host_endtime,$dbtable_location,$host_company);
				$total_guestdata = count($guestdata);
				if($total_guestdata >0 )
				{
					
					//COMPARE 1 TO ALL DATA IN GEOFENCE at THE SAME MOMENT 
					for ($y=0;$y<$total_guestdata;$y++)
					{
						$norut_y = $y+1;
						printf("===GUEST LOC SEARCHING..  %s of %s \r\n", $norut_y, $total_guestdata);
						$guest_latitude = $guestdata[$y]->location_report_latitude;
						$guest_longitude = $guestdata[$y]->location_report_longitude;
						$guest_gpstime = $guestdata[$y]->location_report_gps_time;
						$guest_no = $guestdata[$y]->location_report_vehicle_no;
						
						//get distance START HERE
						$getdistance = $this->getDistance($host_latitude,$host_longitude,$guest_latitude,$guest_longitude);
						$distance_result = round($getdistance*1000,0);
						
						printf("===HOST %s || GUEST %s at %s || %s \r\n", $host_no,$guest_no,$host_starttime,$guest_gpstime);
						printf("===HOST COORD %s, %s GUEST COORD %s, %s = %s meter \r\n", $host_latitude,$host_longitude,$guest_latitude,$guest_longitude,$distance_result);
						
						$insertRawRadius = $this->insertRawRadius($rows_loc[$x],$guestdata[$y],$distance_result);
						
					}
				}
				else
				{
					printf("======NO DATA GUEST.. \r\n");
				}
				
				printf("================================================= \r\n");
				
				
			}
			
			//print_r($data_radius);exit();
			
			$this->dbreport->close();
			$this->dbreport->cache_delete_all();

		}else{
			printf("===NO DATA \r\n");
		}

	}
	
	function getLocationReport_bygeofence($host_geofence,$host_startime,$host_endtime,$dbtable_location,$host_company)
	{

		$this->dbreport = $this->load->database("tensor_report",true);
		//$this->dbreport->select("location_report_id,location_report_vehicle_device,location_report_vehicle_no,location_report_gps_time,location_report_geofence_name,location_report_latitude,location_report_longitude,location_report_gps_time");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->order_by("location_report_vehicle_no","asc");
		//$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_gps_time >=", $host_startime);
		$this->dbreport->where("location_report_gps_time <=", $host_endtime);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->where("location_report_geofence_name", $host_geofence);
		$this->dbreport->where("location_report_vehicle_company <>", $host_company);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
			
		/* $this->dbreport->close();
		$this->dbreport->cache_delete_all(); */
			
		return $rows_loc;
		
	}
	
	function insertRawRadius($hostdata,$guestdata,$distance)
	{
		$radius_report_host_vehicle_user_id = $hostdata->location_report_vehicle_user_id;
		$radius_report_host_vehicle_id = $hostdata->location_report_vehicle_id;
		$radius_report_host_vehicle_device = $hostdata->location_report_vehicle_device;
		$radius_report_host_vehicle_no = $hostdata->location_report_vehicle_no;
		$radius_report_host_vehicle_name = $hostdata->location_report_vehicle_name;
		$radius_report_host_vehicle_company = $hostdata->location_report_vehicle_company;
		$host_company_name =  $this->getCompanyName($radius_report_host_vehicle_company);
		
		$radius_report_guest_vehicle_user_id = $guestdata->location_report_vehicle_user_id;
		$radius_report_guest_vehicle_id = $guestdata->location_report_vehicle_id;
		$radius_report_guest_vehicle_device = $guestdata->location_report_vehicle_device;
		$radius_report_guest_vehicle_no = $guestdata->location_report_vehicle_no;
		$radius_report_guest_vehicle_name = $guestdata->location_report_vehicle_name;
		$radius_report_guest_vehicle_company = $guestdata->location_report_vehicle_company;
		$guest_company_name = $this->getCompanyName($radius_report_guest_vehicle_company);
		
		
		$radius_report_start_time = $hostdata->location_report_gps_time;
		$radius_report_start_geofence = $hostdata->location_report_geofence_name;
		$radius_report_start_coordinate = $hostdata->location_report_coordinate;
		
		$radius_report_end_geofence = $guestdata->location_report_geofence_name;
		$radius_report_end_coordinate =  $guestdata->location_report_coordinate;
		$radius_report_end_time = date("Y-m-d H:i:s", strtotime("+1 minute", strtotime($radius_report_start_time)));
		
		$radius_report_driver = 0;
		$radius_report_driver_name = "";
		
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
		
		$distance_min = $distance;
									
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
		$q_last = $this->dbts->get("ts_radius_hour_raw");
		$row_last = $q_last->row();
		$total_last = count($row_last);
									
		if($total_last>0){
			$this->dbts = $this->load->database("webtracking_ts",true); 
			$this->dbts->where("radius_report_host_vehicle_id", $radius_report_host_vehicle_id);
			$this->dbts->where("radius_report_start_time",$radius_report_start_time);
			$this->dbts->where("radius_report_guest_vehicle_id",$radius_report_guest_vehicle_id);
			//$this->dbts->limit(1);
			$this->dbts->update("ts_radius_hour_raw",$datainsert);
			printf("===UPDATE OK \r\n ");
		}else{
										
			$this->dbts->insert("ts_radius_hour_raw",$datainsert);
			printf("===INSERT OK \r\n");
		}
		
		return;
		
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
	
	
	function hour_summary($userid="", $imeiexca="", $hostexca="", $order="asc", $date="", $radiusmeter="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d');
		$nowtime = date('Y-m-d H:i:s');
		$nowtime_wita = date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($nowtime)));
		
		printf("===STARTING SUMMARY HOURLY %s WIB %s WITA\r\n", $nowtime, $nowtime_wita); 
		
		if($date == ""){
			$startdate = date("Y-m-d");
			$enddate = date("Y-m-d");
		}else{
			$startdate = date("Y-m-d", strtotime($date));
			$enddate = date("Y-m-d", strtotime($date));
			
		}
		$report = "location_";
		
		$month = date("F", strtotime($startdate));
		$year = date("Y", strtotime($startdate));
		
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
		
		$dbtable = "ts_radius_hour_raw";
		
		$hour_selected = array("09:00:00","10:00:00","11:00:00");
		//$hour_selected = array("10:00:00");
		$this->dbts = $this->load->database("webtracking_ts", TRUE);
		$this->dbts->order_by("hour_name","asc");
		$this->dbts->where("hour_user", $userid);
		$this->dbts->where_in("hour_time", $hour_selected);
		$qhour = $this->dbts->get("ts_hour");
		$rowshour = $qhour->result();
		$totalhour = count($rowshour);
	
		printf("===START ===================== \r\n");
		for ($i=0;$i<$totalhour;$i++)
		{
			printf("===PROCESS HOUR %s %s\r\n", $rowshour[$i]->hour_time, $date);
			printf("===PARAM %s %s %s %s \r\n", $imeiexca, $hostexca,$startdate,$rowshour[$i]->hour_time);
			$datareport = $this->getRawVehicleRadiusHour($userid,$imeiexca,$hostexca,$startdate,$rowshour[$i]->hour_time,$dbtable,$radiusmeter);
			
		}
		
		$finishtime = date('Y-m-d H:i:s');
		printf("===FINISH SUMMARY RADIUS HOUR DATA %s to %s \r\n", $nowtime, $finishtime);
		printf("============================== \r\n");
		
		//send telegram
		$title_name = "SUMMARY RADIUS PER HOUR ".$imeiexca;
			$message = urlencode(
					"".$title_name." \n".
					"Date: ".$date." \n".
					"Start Time: ".$nowtime." \n".
					"End Time: ".$finishtime." \n"
				);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-671321211",$message); //AUTOCHECK HOUR
		printf("===SENT TELEGRAM OK\r\n");
		
		
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
	}
	
	function getRawVehicleRadiusHour($userid,$imeiexca,$hostexca,$startdate,$hour,$dbtable,$radiusmeter)
	{
		
		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->select("radius_report_guest_vehicle_no,radius_report_host_vehicle_no,radius_report_guest_vehicle_device,radius_report_host_vehicle_device,radius_report_distance");
		$this->dbts->group_by("radius_report_guest_vehicle_device");
		$this->dbts->where("radius_report_host_vehicle_device", $imeiexca."@".$hostexca);
		$this->dbts->where("radius_report_date", $startdate);
		$this->dbts->where("radius_report_hour", $hour);
		$this->dbts->where("radius_report_distance <=", $radiusmeter);
		$this->dbts->from($dbtable);
        $q_loc = $this->dbts->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		if(count($rows_loc)>0){

			for($x=0;$x<$total_loc;$x++)
			{
				$norut = $x+1;
				$guestvehicle = $rows_loc[$x]->radius_report_guest_vehicle_device;
				$guestvehicleno = $rows_loc[$x]->radius_report_guest_vehicle_no;
				$hostexcano = $rows_loc[$x]->radius_report_host_vehicle_no;
				printf("===GET INFO %s x %s (%s of %s) \r\n", $hostexcano, $guestvehicleno, $norut, $total_loc);
				
				$detail_radius = $this->getRawDetailRadiusHour($userid,$imeiexca,$hostexca,$guestvehicle,$startdate,$hour,$dbtable,$radiusmeter);
				
			}
			printf("===END ===================== \r\n");
			//print_r($data_radius);exit();
			
			$this->dbts->close();
			$this->dbts->cache_delete_all();

		}else{
			printf("===NO DATA \r\n");
		}

	}
	
	function getRawDetailRadiusHour($userid,$imeiexca,$hostexca,$guestvehicle,$sdate,$hour,$dbtable,$radiusmeter)
	{
		
		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->order_by("radius_report_start_time","asc");
		$this->dbts->where("radius_report_host_vehicle_device", $imeiexca."@".$hostexca);
		$this->dbts->where("radius_report_guest_vehicle_device", $guestvehicle);
		$this->dbts->where("radius_report_date", $sdate);
		$this->dbts->where("radius_report_hour", $hour);
		$this->dbts->where("radius_report_distance <=", $radiusmeter);
		$this->dbts->from($dbtable);
        $q_loc2 = $this->dbts->get();
        $rows_loc2 = $q_loc2->result();
		$total_loc2 = count($rows_loc2);
		if(count($rows_loc2)>0){
			$total_duration = 0;
			$distance_min = 999999;
			$coordinate_end = "";
			for($y=0;$y<$total_loc2;$y++)
			{
				//$total_duration = $total_duration + $rows_loc2[$y]->radius_report_duration_sec;
				$last_distance = $rows_loc2[$y]->radius_report_distance;
				
				if($last_distance < $distance_min){
					$coordinate_end = $rows_loc2[$y]->radius_report_end_coordinate;
					$distance_min = $last_distance;
				}
			}
			$distance_min = round($distance_min,0);
			$duration_min = $total_loc2;
			
			$radius_report_host_vehicle_user_id = $rows_loc2[$total_loc2-1]->radius_report_host_vehicle_user_id;
			$radius_report_host_vehicle_id = $rows_loc2[$total_loc2-1]->radius_report_host_vehicle_id;
			$radius_report_host_vehicle_device = $rows_loc2[$total_loc2-1]->radius_report_host_vehicle_device;
			$radius_report_host_vehicle_no = $rows_loc2[$total_loc2-1]->radius_report_host_vehicle_no;
			$radius_report_host_vehicle_name = $rows_loc2[$total_loc2-1]->radius_report_host_vehicle_name;
			$radius_report_host_vehicle_company = $rows_loc2[$total_loc2-1]->radius_report_host_vehicle_company;
			$host_company_name =  $rows_loc2[$total_loc2-1]->radius_report_host_vehicle_company_name;
			
			$radius_report_guest_vehicle_user_id = $rows_loc2[$total_loc2-1]->radius_report_guest_vehicle_user_id;
			$radius_report_guest_vehicle_id = $rows_loc2[$total_loc2-1]->radius_report_guest_vehicle_id;
			$radius_report_guest_vehicle_device = $rows_loc2[$total_loc2-1]->radius_report_guest_vehicle_device;
			$radius_report_guest_vehicle_no = $rows_loc2[$total_loc2-1]->radius_report_guest_vehicle_no;
			$radius_report_guest_vehicle_name = $rows_loc2[$total_loc2-1]->radius_report_guest_vehicle_name;
			$radius_report_guest_vehicle_company = $rows_loc2[$total_loc2-1]->radius_report_guest_vehicle_company;
			$guest_company_name = $rows_loc2[$total_loc2-1]->radius_report_guest_vehicle_company_name;
			
			
			$radius_report_start_time = $rows_loc2[$total_loc2-1]->radius_report_start_time;
			$radius_report_start_geofence = $rows_loc2[$total_loc2-1]->radius_report_start_geofence;
			$radius_report_start_coordinate = $rows_loc2[$total_loc2-1]->radius_report_start_coordinate;
			
			$radius_report_end_geofence = $rows_loc2[$total_loc2-1]->radius_report_start_geofence;
			$radius_report_end_coordinate = $coordinate_end;
			$radius_report_end_time = date("Y-m-d H:i:s", strtotime("+".$duration_min." minute", strtotime($radius_report_start_time)));
			
			$radius_report_driver = 0;
			$radius_report_driver_name = "";
			
			$radius_report_type = $radiusmeter;
			$radius_report_totaldata = $total_loc2;
			
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
			
			$datainsert["radius_report_type"] = $radius_report_type;
			$datainsert["radius_report_totaldata"] = $radius_report_totaldata;
										
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
				$this->dbts->where("radius_report_type",$radius_report_type);
				$this->dbts->where("radius_report_guest_vehicle_id",$radius_report_guest_vehicle_id);
				//$this->dbts->limit(1);
				$this->dbts->update("ts_radius_hour",$datainsert);
				printf("===UPDATE OK \r\n ");
			}else{
											
				$this->dbts->insert("ts_radius_hour",$datainsert);
				printf("===INSERT OK \r\n");
			}
			
			printf("===GET DETAIL %s x %s %s %s \r\n", $radius_report_host_vehicle_no, $radius_report_guest_vehicle_no, $distance_min, $radius_report_start_time);
			
			
			$this->dbts->close();
			$this->dbts->cache_delete_all();

		}else{
			printf("===NO DATA DETAIL \r\n");
		}
		
		return;

	}
	
	//OLD VERSION
	//ritase report by geofence EXCA VS DT
	function radius_breakdown($userid="", $imeiexca="", $orderby="",$startdate="",$enddate="")
	{
		$report = "radius_";
		if ($startdate == "") {
            /*$startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday")); */
			
			$startdate = date("Y-m-d 00:00:00");
			$month = date("F");
			$year = date("Y");
        }

        if ($startdate != ""){
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }

		if ($enddate == "") {
           /*  $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday")); */
			
			$enddate = date("Y-m-d 00:00:00");
			$month = date("F");
			$year = date("Y");
        }

        if ($enddate != ""){
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
			$month = date("F", strtotime($enddate));
			$year = date("Y", strtotime($enddate));
        }
		
		$startdate_utc = date("Y-m-d H:i:s",strtotime($startdate . "-7hours"));
		$enddate_utc = date("Y-m-d H:i:s",strtotime($enddate . "-7hours"));
		printf("===PERIODE UTC : %s to %s \r\n", $startdate_utc, $enddate_utc); 
		
		printf("===STARTING REPORT %s %s \r\n", $startdate, $enddate);
		$this->db = $this->load->database("default",true);
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_device,vehicle_no,vehicle_dbname_live");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_type", "VT200PTO");
		$this->db->where("vehicle_device", $imeiexca."@VT200");
		$this->db->where("vehicle_status <>", 3);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$vehicleid = $rows[$i]->vehicle_id;
				$vehicle_device = explode("@", $rows[$i]->vehicle_device);
				$deviceid = $vehicle_device[0];
				$vehicle_no = $rows[$i]->vehicle_no;
				$dblive =  $rows[$i]->vehicle_dbname_live;
				$dbhist =  "gpshistory_radius";
				
				$dbtable_live = "radius";
				$dbtable_hist = $vehicle_device[0]."_".strtolower($vehicle_device[1])."_gps";
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $vehicle_no, $nourut, $total_rows);
			
				$update_event_data = $this->getDataRadius($deviceid,$startdate_utc,$enddate_utc,$dblive,$dbtable_live); 
				$update_event_data = $this->getDataRadius($deviceid,$startdate_utc,$enddate_utc,$dbhist,$dbtable_hist); 
				
				$update_event_data = $this->getDataRadius_level2($deviceid,$startdate_utc,$enddate_utc,$dblive,$dbtable_live); 
				$update_event_data = $this->getDataRadius_level2($deviceid,$startdate_utc,$enddate_utc,$dbhist,$dbtable_hist);
			}
		}else{
			printf("===========TIDAK ADA DATA VEHICLE======== \r\n");
		}

		printf("===========SELESAI======== \r\n");

	}
	
	function getDataRadius($deviceid,$sdate,$edate,$dbload,$dbtable)
	{

		$this->dblive = $this->load->database($dbload,true);
		$this->dblive->order_by("radius_host_time","asc");
		$this->dblive->group_by("radius_host_time");
		$this->dblive->where("radius_host", $deviceid);
		$this->dblive->where("radius_host_time >=",$sdate);
		$this->dblive->where("radius_host_time <=", $edate);
		$q = $this->dblive->get($dbtable);
        $rows = $q->result();
		$total = count($rows);
		//print_r($total);exit();
		if($total>0){

			$total_event_same = 0;
			$delta = 0;
			$totaldelta = 0;
			for($x=0;$x<$total;$x++)
			{
				$norut = $x+1;
				printf("===DATA RADIUS ke %s of %s \r\n", $norut, $total);
				$id_report = $rows[$x]->radius_id;

				if($norut != $total){
					$guest_next = $rows[$x+1]->radius_guest;
					$guest_current = $rows[$x]->radius_guest;

					$time_next = strtotime($rows[$x+1]->radius_host_time);
					$time_current = strtotime($rows[$x]->radius_host_time);

					$delta = $time_next - $time_current; //sec
					$limit_sec = 30*60; // sec to menit (limit 30 menit)
					//$totaldelta = $totaldelta + $delta;
					//if(($guest_current == $guest_next) && ($delta < $limit_sec)) //jika guest sama dan kurang dari limit maka dianggap 1 event
					if($guest_current == $guest_next) //jika guest masih sama maka dianggap 1  event
					{
						$event_same = "A";
						$total_event_same = $total_event_same + 1;
						$totaldelta = $totaldelta + $delta;
						printf("===GUEST YG SAMA : %s || %s EVENT : %s DELTA %s s \r\n",$guest_current, $guest_next, $event_same, $delta);
						$status = 0;
						$status_end = 0;
					}
					else
					{
						//jika BEDA LOKASI, INSERT LOKASI BEFORE
						$event_same = "B";
						$totaldelta = $totaldelta + $delta;
						printf("===GUEST BEDA : %s || %s EVENT : %s DELTA %s s \r\n",$guest_current, $guest_next, $event_same, $delta);
						$status = 1;
						$status_end = 0;

					}

				}
				else
				{
					$event_same = "B";
					printf("===END EVENT : %s \r\n",$event_same);
					$status = 1;
					$status_end = 1;
				}

				//update
				/* if($status == 1)
				{
				*/
					unset($data);
					
					$data["radius_event"] = $event_same;
					$data["radius_event_delta"] = 0;
					$data["radius_event_total"] = 0;
					$this->dblive->where("radius_id", $id_report);
					$this->dblive->update($dbtable,$data);
					printf("===UPDATE OK: %s %s s \r\n",$guest_current,$totaldelta);
					printf("============== \r\n");

					//clear
					$delta = 0;
					$totaldelta = 0;
					$total_event_same_loc = 0;
				//}
				/* else
				{

					unset($data);

					$data["radius_event"] = "";
					$data["radius_event_delta"] = "";
					$data["radius_event_total"] = "";
					$this->dblive->where("radius_id", $id_report);
					//$this->dblive->limit(1);
					$this->dblive->update($dbtable,$data);
					printf("===CLEAR OK: %s \r\n",$guest_current);
					printf("============== \r\n");

				} */
			}
		}
		else
		{
			printf("===TIDAK ADA DATA RADIUS di : %s ! \r\n",$dbload);
		}
		
		return;
	}
	
	function getDataRadius_level2($deviceid,$sdate,$edate,$dbload,$dbtable)
	{
		$this->dblive = $this->load->database($dbload,true);
		$this->dblive->distinct();
		$this->dblive->group_by("radius_host_time");
		$this->dblive->select('radius_guest');
		$this->dblive->order_by('radius_host_time',"asc");
		$this->dblive->where("radius_host", $deviceid);
		$this->dblive->where("radius_host_time >=",$sdate);
		$this->dblive->where("radius_host_time <=", $edate);
		$this->dblive->where("radius_event", "B");
		$q = $this->dblive->get($dbtable);
		$rows = $q->result();
		$total = count($rows);
		if($total>0){
			
			
			for($x=0;$x<$total;$x++)
			{
				$norut_1 = $x+1;
				printf("===DATA UNIT %s of %s \r\n", $norut_1, $total);
				$this->dblive->select('radius_id,radius_host,radius_guest,radius_host_time');
				$this->dblive->group_by("radius_host_time");
				$this->dblive->order_by('radius_host_time',"asc");
				$this->dblive->where("radius_host", $deviceid);
				$this->dblive->where("radius_guest", $rows[$x]->radius_guest); 
			
				$this->dblive->where("radius_host_time >=",$sdate);
				$this->dblive->where("radius_host_time <=", $edate);
				$this->dblive->where("radius_event", "B");
				$q2 = $this->dblive->get($dbtable);
				$rows2 = $q2->result();
				$total_rows2 = count($rows2);
				$dataresult1 = array();
				$dataresult2 = array();
				$totaldelta_lev2_a = 0;
				$totaldelta_lev2_b = 0;
				for($y=0;$y<$total_rows2;$y++)
				{
					$norut_lev2 = $y+1;
					//printf("===DATA LEV #2 ke %s of %s \r\n", $norut_lev2, $total_rows2);
					$id_report = $rows2[$y]->radius_id;
					
					if($norut_lev2 != $total_rows2){
						
						$time_next_lev2 = strtotime($rows2[$y+1]->radius_host_time);
						$time_current_lev2 = strtotime($rows2[$y]->radius_host_time);

						$delta_lev2 = $time_next_lev2 - $time_current_lev2; //sec
						$limit_sec_lev2 = 20*60; // sec to menit (limit 30 menit)
						//printf("===RESULT LEV #2 : %s at %s %s s \r\n",$rows2[$y]->radius_guest, $rows2[$y]->radius_host_time, $delta_lev2);
						
						
						//jika data kurang dari limit sec maka dianggap 1 event
						if($delta_lev2 < $limit_sec_lev2){
							
							$deltatype = "A";
							$totaldelta_lev2_a = $totaldelta_lev2_a + $delta_lev2;
							printf("===DATA LEV #2 A : %s at %s %s s \r\n",$rows2[$y]->radius_guest, $rows2[$y]->radius_host_time,$totaldelta_lev2_a);
							$info1 = $rows2[$y]->radius_host."-".$rows2[$y]->radius_guest."-".$rows2[$y]->radius_host_time."-".$delta_lev2."-".$deltatype." \n";
							array_push($dataresult1,$info1);
							
							unset($data);
							$data["radius_event_delta"] = $totaldelta_lev2_a;
									
							$this->dblive->where("radius_id", $id_report);
							$this->dblive->update($dbtable,$data);
							printf("===UPDATE DURATION OK: %s %s s \r\n",$rows[$x]->radius_guest,$totaldelta_lev2_a);
							printf("============== \r\n");
							
							
						}else{
							
							//$totaldelta_lev2 = $delta_lev2;
							$deltatype = "B";
							$totaldelta_lev2_b = $totaldelta_lev2_b + $delta_lev2;
							printf("===DATA LEV #2 B : %s at %s \r\n",$rows2[$y]->radius_guest, $rows2[$y]->radius_host_time,$totaldelta_lev2_b);
							$info2 = $rows2[$y]->radius_host."-".$rows2[$y]->radius_guest."-".$rows2[$y]->radius_host_time."-".$delta_lev2."-".$deltatype." \n";
							array_push($dataresult2,$info2);
							
							unset($data);
							$data["radius_event_delta"] = $totaldelta_lev2_b;
									
							$this->dblive->where("radius_id", $id_report);
							$this->dblive->update($dbtable,$data);
							printf("===UPDATE DURATION OK: %s %s s \r\n",$rows[$x]->radius_guest,$totaldelta_lev2_b);
							printf("============== \r\n");
							
						}
					}else{
						
						printf("===END DATA LEV #2 : %s at %s \r\n",$rows2[$y]->radius_guest, $rows2[$y]->radius_host_time);
					}
					
					
				}
				//print_r($dataresult1); 
				printf("============== \r\n");
				//print_r($dataresult2);
				printf("============== \r\n");
			}
			
			
		}else{
			printf("===TIDAK ADA DATA RADIUS LEVEL #2 di : %s ! \r\n",$dbload);
		}
		
		return;
		
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die("Curl failed: " . curL_error($ch));
        }
        echo $result;
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);

    }

	function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
	{
	  $earth_radius = 6371;

	  $dLat = deg2rad($latitude2 - $latitude1);
	  $dLon = deg2rad($longitude2 - $longitude1);

	  $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
	  $c = 2 * asin(sqrt($a));
	  $d = $earth_radius * $c;

	  return $d;
	}

	//pecahan dari move (low)
	function getOperational_bylocation_idle($deviceid,$startdate,$enddate,$dbtable_location)
	{

		$this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_view,location_report_location,location_report_jalur");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_device", $deviceid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		$this->dbreport->where("location_report_name", "location_idle");
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->like("location_report_location", "KM");
		$this->dbreport->where("location_report_location <>", "POOL TMS");
		$this->dbreport->where("location_report_jalur <>", "");

		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		if(count($rows_loc)>0){


			for($x=0;$x<count($rows_loc);$x++)
			{
				$norut = $x+1;
				printf("===DATA LOCATION IDLE ke %s of %s \r\n", $norut, $total_loc);
				$id_report_loc = $rows_loc[$x]->location_report_id;

				if($norut != $total_loc){
					$jalurnext = $rows_loc[$x+1]->location_report_jalur;
					$jalurcurrent = $rows_loc[$x]->location_report_jalur;

					$locationnext = $rows_loc[$x+1]->location_report_location;
					$locationcurrent = $rows_loc[$x]->location_report_location;

					$timenext = strtotime($rows_loc[$x+1]->location_report_gps_time);
					$timecurrent = strtotime($rows_loc[$x]->location_report_gps_time);

					$currentposition = $jalurcurrent.",".$locationcurrent;
					$nextposition = $jalurnext.",".$locationnext;

					$delta = $timenext - $timecurrent; //sec
					$limit_sec = 60*60; // sec to menit

					if(($currentposition == $nextposition) && ($delta < $limit_sec))
					{
						$event = "A";
					}
					else
					{

						$event = "B";
					}
					printf("===COMPARE : %s || %s EVENT : %s \r\n",$currentposition, $nextposition, $event);
					printf("===LIMIT : %s || %s \r\n", $limit_sec, $delta);
					/* printf("===TIME CURRENT : %s \r\n",$rows_loc[$x]->location_report_gps_time);
					printf("===TIME NEXT : %s \r\n",$rows_loc[$x+1]->location_report_gps_time); */
				}
				else
				{
					$event = "B";
					printf("===END EVENT : %s \r\n",$event);
				}



				unset($data);
				$data_loc["location_report_view"] = "IDLE";
				$data_loc["location_report_event"] = $event;

				$this->dbreport->where("location_report_id", $id_report_loc);
				$this->dbreport->limit(1);
				$this->dbreport->update($dbtable_location,$data_loc);

				printf("===UPDATE VIEW OK=== \r\n");
			}


						/* for($x=count($rows_loc)-1; $x >= 0; $x--)
							{
								if (($x+1) >= count($rows_loc))
								{
									$rowsummary[] = $rows_loc[$x];
									continue;
								}
								$jalurbefore = $rows_loc[$x+1]->location_report_jalur;
								$locbefore = $rows_loc[$x+1]->location_report_location;
								$jalurcurrent = $rows_loc[$x]->location_report_jalur;
								$loccurrent = $rows_loc[$x]->location_report_location;
								if (sprintf("%.4f,%.4f", $jalurbefore, $locbefore) != sprintf("%.4f,%.4f", $jalurcurrent, $loccurrent))
								{
									$rowsummary[] = $rows_loc[$x];
									continue;
								}

							}
							print_r($rowsummary); */


		}else{
			printf("===NO DATA \r\n");
		}
	}

	



}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
