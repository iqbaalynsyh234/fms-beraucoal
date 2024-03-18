<?php
include "base.php";

class Kepmen_cronjob extends Base {
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("gpsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		
	}
	
	// operasional location = location , location_idle, location_off -> operational_bylocation, compare_km, speed_avg_from_operasional
	// operasional breakdown = location_breakdown_view (inc getOperational_bylocation_idle, getOperational_bylocation_move)  
	//  					-> operational_bylocation_idle_breakdown , operational_bylocation_move_breakdown
	
	//http://jsfiddle.net/izothep/myork5sa/  
	//http://semantia.com.au/articles/highcharts-drill-down-stacked-columns/
	//http://jsfiddle.net/bge14m3a/1/
	
	function daily_perunit($userid="",$orderby="",$typereport="",$startdate="",$enddate="",$imei="",$vtype="")
	{
		$startproses = date("Y-m-d H:i:s");
		$report = "webtracking_ts_kepmen_source";
		$report_location = "location_";
		$report_ritase = "ritase_";
		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != ""){
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
		
		if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($enddate != ""){
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
			$month = date("F", strtotime($enddate));
			$year = date("Y", strtotime($enddate));
        }
       
		//print_r($startdate." ".$enddate);exit();
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_location = $report_location."januari_".$year;
			$dbtable_ritase = $report_ritase."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_location = $report_location."februari_".$year;
			$dbtable_ritase = $report_ritase."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_location = $report_location."maret_".$year;
			$dbtable_ritase = $report_ritase."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_location = $report_location."april_".$year;
			$dbtable_ritase = $report_ritase."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_location = $report_location."mei_".$year;
			$dbtable_ritase = $report_ritase."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_location = $report_location."juni_".$year;
			$dbtable_ritase = $report_ritase."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_location = $report_location."juli_".$year;
			$dbtable_ritase = $report_ritase."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_location = $report_location."agustus_".$year;
			$dbtable_ritase = $report_ritase."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_location = $report_location."september_".$year;
			$dbtable_ritase = $report_ritase."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_location = $report_location."oktober_".$year;
			$dbtable_ritase = $report_ritase."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_location = $report_location."november_".$year;
			$dbtable_ritase = $report_ritase."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_location = $report_location."desember_".$year;
			$dbtable_ritase = $report_ritase."desember_".$year;
			break;
		}
		
		printf("===STARTING REPORT %s %s \r\n", $startdate, $enddate);
		$this->db = $this->load->database("default",true); 
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_mv03,vehicle_type,vehicle_company,vehicle_sensor");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		if($imei != "" && $vtype != ""){
			$this->db->where("vehicle_device", $imei."@".$vtype);
		}
		//$this->db->where("vehicle_company", 1839);
		//$this->db->limit(3);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		//print_r($rows);exit();
		
		$street_list = $this->getAllStreetHauling($userid);
		$street_ROM_list = $this->getAllStreetROM($userid);
		$street_PORT_list = $this->getAllStreetPort($userid);
		$street_HAULING_list = $this->getAllStreetKM($userid);
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			$working_total_sec = 0;
			$breakdown_total_sec = 0;
			$day_total_sec = 24*3600; 
			$model_data = "daily_perunit";
			$total_rit = "";
			$total_distance = "";
			$total_ton = "";
 			//exit();
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$vehicleid = $rows[$i]->vehicle_id;
				$vehicleuserid = $rows[$i]->vehicle_user_id;
				$deviceid = $rows[$i]->vehicle_device;
				$vehicleno = $rows[$i]->vehicle_no;
				$vehiclename = $rows[$i]->vehicle_name;
				$vehiclemv03 = $rows[$i]->vehicle_mv03;
				$vehicle_company = $rows[$i]->vehicle_company;
				$vehicletype = $rows[$i]->vehicle_type;
				$vehiclesensor = $rows[$i]->vehicle_sensor;
				$company_name = $this->getCompanyName($vehicle_company);
				
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $vehicleno, $nourut, $total_rows);
				$working_total_sec = $this->getLocationMove_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$street_list); //move tidak pakai location
				//$idle_total_sec = $this->getLocationIdle_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$street_list); //tidak di pakai
				
				$idle_ROM_total_sec = $this->getLocationIdle_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$street_ROM_list);
				$idle_PORT_total_sec = $this->getLocationIdle_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$street_PORT_list);
				$idle_HAULING_total_sec = $this->getLocationIdle_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$street_HAULING_list);
				$idle_OTHERS_total_sec = $this->getLocationIdle_OTHERSreport($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$street_list); 
				
				
				$breakdown_total_sec = $this->getBreakdown_report($deviceid,$startdate,$enddate);
				if($breakdown_total_sec == 86400){
					
					$breakdown_total_sec = $breakdown_total_sec - $working_total_sec - $idle_ROM_total_sec - $idle_PORT_total_sec - $idle_HAULING_total_sec - $idle_OTHERS_total_sec;
				}
				$total_rit = $this->getDataRitase_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_ritase);
				$total_ton = $total_rit*30; 
				$total_distance = $this->getDataOdometer_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location);
				
				//$standby_total_sec = $day_total_sec - $working_total_sec - $breakdown_total_sec - $idle_total_sec;
				$standby_total_sec = $day_total_sec - $working_total_sec - $breakdown_total_sec - $idle_ROM_total_sec - $idle_PORT_total_sec - $idle_HAULING_total_sec - $idle_OTHERS_total_sec;
				
				$idle_total_sec = $idle_ROM_total_sec + $idle_PORT_total_sec + $idle_HAULING_total_sec + $idle_OTHERS_total_sec;
				
				$workhour = round($working_total_sec / 3600,2);
				$idlehour = round($idle_total_sec / 3600,2);
				$breakhour = round($breakdown_total_sec / 3600,2);
				$standbyhour = round($standby_total_sec / 3600,2);
				$distance_km = round($total_distance / 1000,2);
				
				printf("===WH: %s, IDL %s, R: %s, S: %s \r\n", $workhour, $idlehour,$breakhour,$standbyhour);
				printf("===ROM: %s, PORT %s, HAULING: %s, OTHERS: %s \r\n", $idle_ROM_total_sec, $idle_PORT_total_sec,$idle_HAULING_total_sec,$idle_OTHERS_total_sec);
				//cek jika 
				if($vehiclesensor != "" ){
					
					if($workhour > 0){
						$total_fuel_cons = $this->getDataFuel_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location);
						$total_cons_hour = $workhour+$idlehour;
						$fuel_liter_jam = round($total_fuel_cons / $total_cons_hour,2);
						$fuel_liter_km = round($total_fuel_cons / $distance_km,2);
						printf("===Cons %s, L/Jam: %s, L/KM %s \r\n", $total_fuel_cons, $fuel_liter_jam, $fuel_liter_km);
						
						$data_fuel_isi = $this->getDataFuelISI_report_minmax($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location);
						$ex_isi_fuel = explode("|",$data_fuel_isi);
						$total_fuel_isi = $ex_isi_fuel[0];
						$total_isi_pagi = $ex_isi_fuel[1];
						$time_isi_pagi = $ex_isi_fuel[2];
						$total_isi_malam = $ex_isi_fuel[3];
						$time_isi_malam = $ex_isi_fuel[4];
						
						$total_fuel_isi = round($total_fuel_isi,0);
						printf("===Total Isi %s \r\n", $total_fuel_isi);
						$fuel_liter_jam_isi = round($total_fuel_isi / $total_cons_hour,2);
						$fuel_liter_km_isi = round($total_fuel_isi / $distance_km,2);
						printf("===ISI %s, L/Jam NEW: %s, L/KM NEW: %s \r\n", $total_fuel_cons, $fuel_liter_jam_isi, $fuel_liter_km_isi);
						
						
						//
						$data_fuel_isi_avg = $this->getDataFuelISI_report_avg($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location);
						$ex_isi_fuel_avg = explode("|",$data_fuel_isi_avg);
						$total_fuel_isi_avg = $ex_isi_fuel_avg[0];
						$total_isi_pagi_avg = $ex_isi_fuel_avg[1];
						$time_isi_pagi_avg = $ex_isi_fuel_avg[2];
						$total_isi_malam_avg = $ex_isi_fuel_avg[3];
						$time_isi_malam_avg = $ex_isi_fuel_avg[4];
						
						$total_fuel_isi_avg = round($total_fuel_isi_avg,0);
						printf("===Total Isi %s \r\n", $total_fuel_isi_avg);
						$fuel_liter_jam_isi_avg = round($total_fuel_isi_avg / $total_cons_hour,2);
						$fuel_liter_km_isi_avg = round($total_fuel_isi_avg / $distance_km,2);
						printf("=== AVG ISI %s, L/Jam NEW: %s, L/KM NEW: %s \r\n", $total_fuel_cons, $fuel_liter_jam_isi_avg, $fuel_liter_km_isi_avg);
						
					}else{
						
						printf("X=Unit tidak operasional \r\n ");
						$total_fuel_cons = "-";
						$fuel_liter_jam = "-";
						$fuel_liter_km = "-";
						
						$total_fuel_isi = "-";
						$total_isi_pagi = "-";
						$total_isi_malam = "-";
						$time_isi_pagi = "-";
						$time_isi_malam = "-";
						$fuel_liter_jam_isi = "-";
						$fuel_liter_km_isi = "-";
						
						$total_fuel_isi_avg = "-";
						$total_isi_pagi_avg = "-";
						$total_isi_malam_avg = "-";
						$time_isi_pagi_avg = "-";
						$time_isi_malam_avg = "-";
						$fuel_liter_jam_isi_avg = "-";
						$fuel_liter_km_isi_avg = "-";
						
						
					}
					
				}else{
					printf("X=Belum pasang fuel sensor \r\n ");
					$total_fuel_cons = "-";
					$fuel_liter_jam = "-";
					$fuel_liter_km = "-";
					
					$total_fuel_isi = "-";
					$total_isi_pagi = "-";
					$total_isi_malam = "-";
					$time_isi_pagi = "-";
					$time_isi_malam = "-";
					$fuel_liter_jam_isi = "-";
					$fuel_liter_km_isi = "-";
					
					$total_fuel_isi_avg = "-";
					$total_isi_pagi_avg = "-";
					$total_isi_malam_avg = "-";
					$time_isi_pagi_avg = "-";
					$time_isi_malam_avg = "-";
					$fuel_liter_jam_isi_avg = "-";
					$fuel_liter_km_isi_avg = "-";
					
					
				}
				
				/* print_r("WH: ".$workhour." IT: ".$idlehour." ");
				print_r($total_cons_hour." ".$fuel_liter_jam." ".$fuel_liter_km);exit(); */
				
				
				
				
				//exit();
				
					unset($datainsert);
					$datainsert["kepmen_vehicle_user_id"] = $vehicleuserid;
					$datainsert["kepmen_vehicle_id"] = $vehicleid;
					$datainsert["kepmen_vehicle_device"] = $deviceid;
					$datainsert["kepmen_vehicle_mv03"] = $vehiclemv03;
					$datainsert["kepmen_vehicle_no"] = $vehicleno;
					$datainsert["kepmen_vehicle_name"] = $vehiclename;
					$datainsert["kepmen_vehicle_type"] = $vehicletype;
					$datainsert["kepmen_company_id"] = $vehicle_company;
					$datainsert["kepmen_company_name"] = $company_name;
					$datainsert["kepmen_model"] = $model_data;
					$datainsert["kepmen_date"] = date("Y-m-d", strtotime($startdate));
					$datainsert["kepmen_working_time"] = $working_total_sec;
					$datainsert["kepmen_breakdown_time"] = $breakdown_total_sec;
					$datainsert["kepmen_idle_time"] = $idle_total_sec;
					
					$datainsert["kepmen_idle_rom_time"] = $idle_ROM_total_sec;
					$datainsert["kepmen_idle_port_time"] = $idle_PORT_total_sec;
					$datainsert["kepmen_idle_hauling_time"] = $idle_HAULING_total_sec;
					$datainsert["kepmen_idle_others_time"] = $idle_OTHERS_total_sec;
					
					$datainsert["kepmen_standby_time"] = $standby_total_sec;
					$datainsert["kepmen_total_time"] = $day_total_sec;
					$datainsert["kepmen_total_rit"] = $total_rit;
					$datainsert["kepmen_total_ton"] = $total_ton;
					$datainsert["kepmen_total_distance"] = $total_distance;
					$datainsert["kepmen_fuel_cons"] = $total_fuel_cons;
					$datainsert["kepmen_fuel_liter_jam"] = $fuel_liter_jam;
					$datainsert["kepmen_fuel_liter_km"] = $fuel_liter_km;
					
					$datainsert["kepmen_fuel_liter_isi"] = $total_fuel_isi;
					$datainsert["kepmen_fuel_liter_jam_isi"] = $fuel_liter_jam_isi;
					$datainsert["kepmen_fuel_liter_km_isi"] = $fuel_liter_km_isi;
					
					$datainsert["kepmen_fuel_liter_isi_pagi"] = $total_isi_pagi;
					$datainsert["kepmen_fuel_time_isi_pagi"] = $time_isi_pagi;
					$datainsert["kepmen_fuel_liter_isi_malam"] = $total_isi_malam;
					$datainsert["kepmen_fuel_time_isi_malam"] = $time_isi_malam;
					
					$datainsert["kepmen_fuel_liter_isi_pagi_avg"] = $total_isi_pagi_avg;
					$datainsert["kepmen_fuel_time_isi_pagi_avg"] = $time_isi_pagi_avg;
					$datainsert["kepmen_fuel_liter_isi_malam_avg"] = $total_isi_malam_avg;
					$datainsert["kepmen_fuel_time_isi_malam_avg"] = $time_isi_malam_avg;
					
					$datainsert["kepmen_fuel_liter_jam_isi_avg"] = $fuel_liter_jam_isi_avg;
					$datainsert["kepmen_fuel_liter_km_isi_avg"] = $fuel_liter_km_isi_avg;
					
					$datainsert["kepmen_updated"] = date("Y-m-d H:i:s");
					$datainsert["kepmen_flag"] = 0;
					
					//get last data
					$this->dbts = $this->load->database("webtracking_ts",true); 
					$this->dbts->where("kepmen_vehicle_id", $vehicleid);
					$this->dbts->where("kepmen_date",date("Y-m-d", strtotime($startdate)));
					$this->dbts->where("kepmen_model",$model_data);
					$this->dbts->where("kepmen_flag",0);
					$q_last = $this->dbts->get($report);
					$row_last = $q_last->row();
					$total_last = count($row_last);
					
					if($total_last>0){
						$this->dbts = $this->load->database("webtracking_ts",true); 
						$this->dbts->where("kepmen_vehicle_id", $vehicleid);
						$this->dbts->where("kepmen_date",date("Y-m-d", strtotime($startdate)));
						$this->dbts->where("kepmen_model",$model_data);
						$this->dbts->where("kepmen_flag",0);
						$this->dbts->update($report,$datainsert);
						printf("!==UPDATE OK \r\n ");
					}else{
						
						$this->dbts->insert($report,$datainsert);
						printf("===INSERT OK \r\n");
					}
			}
			
			$this->dbts->close();
			$this->dbts->cache_delete_all();
		}else{
			printf("===========TIDAK ADA DATA VEHICLE======== \r\n"); 
		}
		
		//send telegram 
		$cron_name = "OPERATIONAL REPORT";
		$finish_time = date("Y-m-d H:i:s");
		$message =  urlencode(
			"".$cron_name." \n".
			"Periode: ".$startdate." to ".$enddate." \n".
			"Start: ".$startproses." \n".
			"Finish: ".$finish_time." \n"
			);
											
		$sendtelegram = $this->telegram_direct("-653753878",$message); //cron report
		printf("===SENT TELEGRAM OK\r\n");
		
		printf("===========SELESAI======== \r\n"); 
		
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function getLocationMove_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location,$street_list){
		
		$master_report = array("location");
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_location,location_report_name,location_report_speed");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		$this->dbreport->where("location_report_speed >", 0);
		$this->dbreport->where_in("location_report_name",$master_report);
		//$this->dbreport->where_in("location_report_location", $street_list); //ambil semua perjalanan
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		
		//print_r($rows_loc);exit();
		
		if(count($rows_loc)>0)
		{
			
			$total_delta = 0;
			for($x=0;$x<count($rows_loc);$x++)
			{
				$norut = $x+1;
				//printf("===DATA LOCATION MOVE & IDLE ke %s of %s \r\n", $norut, $total_loc);
				$id_report_loc = $rows_loc[$x]->location_report_id;
				
				if($norut != $total_loc){
					
					$locationnext = $rows_loc[$x+1]->location_report_location;
					$locationcurrent = $rows_loc[$x]->location_report_location;
					
					$timenext = strtotime($rows_loc[$x+1]->location_report_gps_time);
					$timecurrent = strtotime($rows_loc[$x]->location_report_gps_time);
					
					$currentposition = $locationcurrent;
					$nextposition = $locationnext;
					
					$delta = $timenext - $timecurrent; //sec
					$limit_sec = 10*60; // sec to menit
					
					if(($currentposition == $nextposition) && ($delta < $limit_sec))
					{
						$event = "A";
						$total_delta = $total_delta + $delta;
						//printf("===COMPARE : %s || %s EVENT : %s DELTA %s TOTAL %s \r\n",$currentposition, $nextposition, $event, $delta, $total_delta);
					}
					else
					{
						//printf("===DIFF : %s || %s DELTA %s \r\n", $currentposition, $nextposition, $delta);
						$event = "B";
					}
					
				}
				else
				{
					$event = "B";
				
				}
					
				
			}
			
			
			
		}else{
			$total_delta = 0;
		}
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
							
		return $total_delta;
	}
	
	function getLocationIdle_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location,$street_list){
		
		$master_report = array("location_idle");
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_location,location_report_name,location_report_speed");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->where_in("location_report_name",$master_report);
		$this->dbreport->where_in("location_report_location", $street_list);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		
		//print_r($rows_loc);exit();
		
		if(count($rows_loc)>0)
		{
			
			$total_delta = 0;
			for($x=0;$x<count($rows_loc);$x++)
			{
				$norut = $x+1;
				//printf("===DATA LOCATION MOVE & IDLE ke %s of %s \r\n", $norut, $total_loc);
				$id_report_loc = $rows_loc[$x]->location_report_id;
				
				if($norut != $total_loc){
					
					$locationnext = $rows_loc[$x+1]->location_report_location;
					$locationcurrent = $rows_loc[$x]->location_report_location;
					
					$timenext = strtotime($rows_loc[$x+1]->location_report_gps_time);
					$timecurrent = strtotime($rows_loc[$x]->location_report_gps_time);
					
					$currentposition = $locationcurrent;
					$nextposition = $locationnext;
					
					$delta = $timenext - $timecurrent; //sec
					$limit_sec = 5*60; // sec to menit
					
					if(($currentposition == $nextposition) && ($delta < $limit_sec))
					{
						$event = "A";
						$total_delta = $total_delta + $delta;
						//printf("===COMPARE : %s || %s EVENT : %s DELTA %s TOTAL %s \r\n",$currentposition, $nextposition, $event, $delta, $total_delta);
					}
					else
					{
						//printf("===DIFF : %s || %s DELTA %s \r\n", $currentposition, $nextposition, $delta);
						$event = "B";
					}
					
				}
				else
				{
					$event = "B";
				
				}
					
				
			}
			
			
			
		}else{
			$total_delta = 0;
		}
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
							
		return $total_delta;
	}
	
	function getLocationIdle_OTHERSreport($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location,$street_list){
		
		$master_report = array("location_idle");
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_location,location_report_name,location_report_speed");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->where_in("location_report_name",$master_report);
		$this->dbreport->where_not_in("location_report_location", $street_list);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		
		//print_r($rows_loc);exit();
		
		if(count($rows_loc)>0)
		{
			
			$total_delta = 0;
			for($x=0;$x<count($rows_loc);$x++)
			{
				$norut = $x+1;
				//printf("===DATA LOCATION MOVE & IDLE ke %s of %s \r\n", $norut, $total_loc);
				$id_report_loc = $rows_loc[$x]->location_report_id;
				
				if($norut != $total_loc){
					
					$locationnext = $rows_loc[$x+1]->location_report_location;
					$locationcurrent = $rows_loc[$x]->location_report_location;
					
					$timenext = strtotime($rows_loc[$x+1]->location_report_gps_time);
					$timecurrent = strtotime($rows_loc[$x]->location_report_gps_time);
					
					$currentposition = $locationcurrent;
					$nextposition = $locationnext;
					
					$delta = $timenext - $timecurrent; //sec
					$limit_sec = 5*60; // sec to menit
					
					if(($currentposition == $nextposition) && ($delta < $limit_sec))
					{
						$event = "A";
						$total_delta = $total_delta + $delta;
						//printf("===COMPARE : %s || %s EVENT : %s DELTA %s TOTAL %s \r\n",$currentposition, $nextposition, $event, $delta, $total_delta);
					}
					else
					{
						//printf("===DIFF : %s || %s DELTA %s \r\n", $currentposition, $nextposition, $delta);
						$event = "B";
					}
					
				}
				else
				{
					$event = "B";
				
				}
					
				
			}
			
			
			
		}else{
			$total_delta = 0;
		}
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
							
		return $total_delta;
	}
	
	function getBreakdown_report($deviceid,$startdate,$enddate){
		
		$this->dbts = $this->load->database("webtracking_ts",true); 
		$this->dbts->select("breakdown_duration_sec");
		$this->dbts->order_by("breakdown_start_time","asc");
		$this->dbts->group_by("breakdown_start_time");
		$this->dbts->where("breakdown_vehicle_device", $deviceid);
		$this->dbts->where("breakdown_start_time >=", $startdate);
		$this->dbts->where("breakdown_start_time <=", $enddate);
		$this->dbts->where("breakdown_flag",0); //data tidak dihapus
		$this->dbts->from("ts_driver_breakdown");
        $q = $this->dbts->get();
        $rows = $q->result();
		$total = count($rows);
		
		//print_r($rows_loc);exit();
		
		if($total>0)
		{
			$total_delta = 0;
			for($x=0;$x<count($rows);$x++)
			{
				$delta = $rows[$x]->breakdown_duration_sec;
				$total_delta = $total_delta + $delta;
			}
			
			if($total_delta > 86400){
				$total_delta = 86400;
			}
		}
		else
		{
			$total_delta = 0;
		}
		
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
		return $total_delta;
	}
	
	function grouping_bylocation_move($deviceid,$startdate,$enddate,$dbtable_location){
	
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_view,location_report_location,location_report_jalur");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_device", $deviceid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		$this->dbreport->where("location_report_name", "location");
		$this->dbreport->where("location_report_speed >", 0);
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
				printf("===DATA LOCATION MOVE ke %s of %s \r\n", $norut, $total_loc);
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
				$data_loc["location_report_view"] = "MOVE";
				$data_loc["location_report_event"] = $event;
				
				$this->dbreport->where("location_report_id", $id_report_loc);
				$this->dbreport->limit(1);	
				$this->dbreport->update($dbtable_location,$data_loc);
				
				printf("===UPDATE VIEW OK=== \r\n"); 
			}
							
			
		}else{
			printf("===NO DATA \r\n"); 
		}
	}
	
	function getAllStreetHauling($userid){
		
		
		$feature = array();
		$street_type_list = array("1","3","4","5","7","8");
		$this->dbmaster = $this->load->database("default",true); 
		$this->dbmaster->select("street_name,street_alias,street_type");
		$this->dbmaster->order_by("street_name","asc");
		$this->dbmaster->group_by("street_name");
		$this->dbmaster->where("street_creator", $userid);
		$this->dbmaster->where_in("street_type", $street_type_list);
		$this->dbmaster->from("street");
        $q = $this->dbmaster->get();
        $rows = $q->result();
		$total = count($rows);
		for($x=0;$x<$total;$x++)
		{
			$street_name = str_replace(",", "", $rows[$x]->street_name);
			$feature[$x] = $street_name;
			
		}
		
		//print_r($feature);exit();
		$result = $feature;
		
		return $result;
	}
	
	function getAllStreetData($userid){
		
		
		$feature = array();
		$street_type_list = array("1","2","3","4","5","6","7","8");
		$this->dbmaster = $this->load->database("default",true); 
		$this->dbmaster->select("street_name,street_alias,street_type");
		$this->dbmaster->order_by("street_name","asc");
		$this->dbmaster->group_by("street_name");
		$this->dbmaster->where("street_creator", $userid);
		$this->dbmaster->where_in("street_type", $street_type_list);
		$this->dbmaster->from("street");
        $q = $this->dbmaster->get();
        $rows = $q->result();
		$total = count($rows);
		for($x=0;$x<$total;$x++)
		{
			$street_name = str_replace(",", "", $rows[$x]->street_name);
			$feature[$x] = $street_name;
			
		}
		
		//print_r($feature);exit();
		$result = $feature;
		
		return $result;
	}
	
	function getAllStreetPool($userid){
		
		
		$feature = array();
		$street_type_list = array("2");
		$this->dbmaster = $this->load->database("default",true); 
		$this->dbmaster->select("street_name,street_alias,street_type");
		$this->dbmaster->order_by("street_name","asc");
		$this->dbmaster->group_by("street_name");
		$this->dbmaster->where("street_creator", $userid);
		$this->dbmaster->where_in("street_type", $street_type_list);
		$this->dbmaster->from("street");
        $q = $this->dbmaster->get();
        $rows = $q->result();
		$total = count($rows);
		for($x=0;$x<$total;$x++)
		{
			$street_name = str_replace(",", "", $rows[$x]->street_name);
			$feature[$x] = $street_name;
			
		}
		
		//print_r($feature);exit();
		$result = $feature;
		
		return $result;
	}
	
	function getAllStreetROM($userid){
		
		
		$feature = array();
		$street_type_list = array("3");
		$this->dbmaster = $this->load->database("default",true); 
		$this->dbmaster->select("street_name,street_alias,street_type");
		$this->dbmaster->order_by("street_name","asc");
		$this->dbmaster->group_by("street_name");
		$this->dbmaster->where("street_creator", $userid);
		$this->dbmaster->where_in("street_type", $street_type_list);
		$this->dbmaster->from("street");
        $q = $this->dbmaster->get();
        $rows = $q->result();
		$total = count($rows);
		for($x=0;$x<$total;$x++)
		{
			$street_name = str_replace(",", "", $rows[$x]->street_name);
			$feature[$x] = $street_name;
			
		}
		
		//print_r($feature);exit();
		$result = $feature;
		
		return $result;
	}
	
	function getAllStreetPort($userid){
		
		
		$feature = array();
		$street_type_list = array("8","7","4"); //PORT + CP + ANTRIAN BLC
		$this->dbmaster = $this->load->database("default",true); 
		$this->dbmaster->select("street_name,street_alias,street_type");
		$this->dbmaster->order_by("street_name","asc");
		$this->dbmaster->group_by("street_name");
		$this->dbmaster->where("street_creator", $userid);
		$this->dbmaster->where_in("street_type", $street_type_list);
		$this->dbmaster->where("street_name !=", "PORT BBC,");
		$this->dbmaster->from("street");
        $q = $this->dbmaster->get();
        $rows = $q->result();
		$total = count($rows);
		for($x=0;$x<$total;$x++)
		{
			$street_name = str_replace(",", "", $rows[$x]->street_name);
			$feature[$x] = $street_name;
			
		}
		
		//print_r($feature);exit();
		$result = $feature;
		
		return $result;
	}
	
	function getAllStreetKM($userid){
		
		
		$feature = array();
		$street_type_list = array("1","5"); //HAULING + ROM ROAD
		$this->dbmaster = $this->load->database("default",true); 
		$this->dbmaster->select("street_name,street_alias,street_type");
		$this->dbmaster->order_by("street_name","asc");
		$this->dbmaster->group_by("street_name");
		$this->dbmaster->where("street_creator", $userid);
		$this->dbmaster->where_in("street_type", $street_type_list);
		
		$this->dbmaster->from("street");
        $q = $this->dbmaster->get();
        $rows = $q->result();
		$total = count($rows);
		for($x=0;$x<$total;$x++)
		{
			$street_name = str_replace(",", "", $rows[$x]->street_name);
			$feature[$x] = $street_name;
			
		}
		
		//print_r($feature);exit();
		$result = $feature;
		
		return $result;
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
	
	function getDataRitase_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_ritase){
		
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("ritase_report_id,ritase_report_start_time,ritase_report_end_geofence,ritase_report_start_geofence");
		$this->dbreport->order_by("ritase_report_start_time","asc");
		//$this->dbreport->group_by("ritase_report_start_time");
		$this->dbreport->where("ritase_report_vehicle_id", $vehicleid);
		$this->dbreport->where("ritase_report_start_time >=", $startdate);
		$this->dbreport->where("ritase_report_start_time <=", $enddate);
		$this->dbreport->where("ritase_report_duration_sec >", 0);
		$this->dbreport->where("ritase_report_type", $typereport);
		$this->dbreport->from($dbtable_ritase);
        $q_rit = $this->dbreport->get();
        $rows_rit = $q_rit->result();
		$total_rit = count($rows_rit);
		//print_r($rows_rit);//exit();
		
		printf("===RITASE: %s  \r\n", $total_rit);
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
							
		return $total_rit;
	}

	function getDataOdometer_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location){
		
		$total_distance = 0;
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_odometer");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		$this->dbreport->where("location_report_odometer >", 0);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		
		if($total_loc > 0){
			$first_data = $rows_loc[0]->location_report_odometer;
			$last_data = $rows_loc[$total_loc-1]->location_report_odometer;
			$total_distance = $last_data - $first_data;
			
			//jika data invalid (minus) hitung manual per data
			if($total_distance < 0){
				$total_distance = $this->getDataOdometer_report_level2($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location);
			}else{
				//1000 KM invalid
				if($total_distance > 1000000){
					$total_distance = $this->getDataOdometer_report_manual_calc($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location);
				}
				
			}
			
		}
		
		
		printf("===DISTANCE: %s Meter \r\n", $total_distance);
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
							
		return $total_distance;
	}
	
	function getDataOdometer_report_level2($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location){
		
		$total_distance = 0;
		$total_delta = 0;
		printf("===DISTANCE REV LEVEL 2 !! \r\n");
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_odometer");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		$this->dbreport->where("location_report_odometer >", 100);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		
		if($total_loc > 0){
			$first_data = $rows_loc[0]->location_report_odometer;
			$last_data = $rows_loc[$total_loc-1]->location_report_odometer;
			$total_distance = $last_data - $first_data;
			
		}
		
		printf("===DISTANCE: %s Meter \r\n", $total_distance);
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
							
		return $total_distance;
	}
	
	function getDataOdometer_report_manual_calc($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location){
		
		$total_dist = 0;
		printf("===DISTANCE REV CALC MANUAL!! \r\n");
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_odometer");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		$this->dbreport->where("location_report_odometer >", 0);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		
		if($total_loc > 0){
		
			for($x=0;$x<$total_loc;$x++)
			{
				$nosort = $x+1;
				
				//printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
				if($nosort == $total_loc)
				{
					//printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
				}
				else
				{
					$first_data = $rows_loc[$x]->location_report_odometer;
					$next_data = $rows_loc[$x+1]->location_report_odometer;
					$delta = $next_data - $first_data;
					
					
							
							if($delta > 1000000){
								printf("===Asumsi invalid: %s to %s delta %s \r\n", $first_data,$next_data, $delta);
								
							}else{
								//normal
								if($delta < 0){
									printf("===Minus ODO: %s to %s delta %s \r\n", $first_data,$next_data, $delta);
								}else{
									$total_dist = $total_dist + $delta;
								}
								
							}
					
				}
					
			}
		
		}
		
		printf("===TOTAL ODO CALC: %s  \r\n", $total_dist);
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
							
		return $total_dist;
	}
	
	
	function getDataFuel_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location){
		
		$total_cons = 0;
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_fuel_data,location_report_gps_time");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		$this->dbreport->where("location_report_fuel_data >", 0);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		//print_r($total_loc);exit();
		for($x=0;$x<$total_loc;$x++)
		{
			$nosort = $x+1;
			
			//printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
			if($nosort == $total_loc)
			{
				//printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
			}
			else
			{
				$first_data = $rows_loc[$x]->location_report_fuel_data;
				$next_data = $rows_loc[$x+1]->location_report_fuel_data;
				$delta_cons = $first_data - $next_data;
				
				$first_time = $rows_loc[$x]->location_report_gps_time;
				$next_time = $rows_loc[$x+1]->location_report_gps_time;
				
				$first_time_sec = strtotime($first_time);
				$next_time_sec = strtotime($next_time);
				$delta_time_sec = $next_time_sec - $first_time_sec;  
				
				
				/*
				Kl sy pahamnya 1jam 10 -12 ltr
				0.5 lt/km kali ya   
				 
				LT/KM = 0.5 LT
				
				LT/JAM = 10-12 LT
				*/
				if($delta_cons != 0){
					if($next_data > $first_data){
						//printf("===Pengisian: %s  %s \r\n", $delta_cons, $rows_loc[$x]->location_report_gps_time);
					}else{
						
						if($delta_cons > 15){
							//printf("===Asumsi invalid: %s %s delta min %s \r\n", $delta_cons, $rows_loc[$x]->location_report_gps_time,$delta_time_sec/60);
							
						}else{
							//printf("===Konsumsi BBM: %s %s \r\n", $delta_cons, $rows_loc[$x]->location_report_gps_time);
							$total_cons = $total_cons + $delta_cons;
						}
						
					}
				}
				
			}
				
		}
		
		
		
		printf("===TOTAL CONS: %s  \r\n", $total_cons);
		/* if($total_cons > 0){
			
			exit();
		} */
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
							
		return $total_cons;
	}
	
	//min_max
	function getDataFuelISI_report_minmax($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location){
		
		$xdate = date("Y-m-d", strtotime($startdate));
		$shift1_start = "00:00:00";
		$shift1_end = "11:59:59";
		
		$shift2_start = "12:00:00";
		$shift2_end = "23:59:59";
		
		$shift1_sdate = date("Y-m-d H:i:s", strtotime($xdate." ".$shift1_start));
		$shift1_edate = date("Y-m-d H:i:s", strtotime($xdate." ".$shift1_end));
		
		$shift2_sdate = date("Y-m-d H:i:s", strtotime($xdate." ".$shift2_start));
		$shift2_edate = date("Y-m-d H:i:s", strtotime($xdate." ".$shift2_end));
	
		
		/* $total_isi_pagi = $this->countfuel_ISI($vehicleid,$shift1_sdate,$shift1_edate,$dbtable_location); 
		$total_isi_malam = $this->countfuel_ISI($vehicleid,$shift2_sdate,$shift2_edate,$dbtable_location);  */
		
		$data_isi_pagi = $this->countfuel_ISI_minmax($vehicleid,$shift1_sdate,$shift1_edate,$dbtable_location); 
		$data_isi_malam = $this->countfuel_ISI_minmax($vehicleid,$shift2_sdate,$shift2_edate,$dbtable_location); 
		
		$ex_isi_pagi = explode("|",$data_isi_pagi);
		$total_isi_pagi = $ex_isi_pagi[0];
		$time_isi_pagi = $ex_isi_pagi[1];
		
		$ex_isi_malam = explode("|",$data_isi_malam);
		$total_isi_malam = $ex_isi_malam[0];
		$time_isi_malam = $ex_isi_malam[1];
	
		printf("===TOTAL ISI SHIFT PAGI: %s %s \r\n", $total_isi_pagi, $time_isi_pagi);
		printf("===TOTAL ISI SHIFT MALAM: %s  %s\r\n", $total_isi_malam, $time_isi_malam);
		
		$total_isi = $total_isi_pagi + $total_isi_malam;
			
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
							
		return $total_isi."|".$total_isi_pagi."|".$time_isi_pagi."|".$total_isi_malam."|".$time_isi_malam;
	}
	
	//avg
	function getDataFuelISI_report_avg($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location){
		
		$xdate = date("Y-m-d", strtotime($startdate));
		$shift1_start = "00:00:00";
		$shift1_end = "11:59:59";
		
		$shift2_start = "12:00:00";
		$shift2_end = "23:59:59";
		
		$shift1_sdate = date("Y-m-d H:i:s", strtotime($xdate." ".$shift1_start));
		$shift1_edate = date("Y-m-d H:i:s", strtotime($xdate." ".$shift1_end));
		
		$shift2_sdate = date("Y-m-d H:i:s", strtotime($xdate." ".$shift2_start));
		$shift2_edate = date("Y-m-d H:i:s", strtotime($xdate." ".$shift2_end));
	
		
		/* $total_isi_pagi = $this->countfuel_ISI($vehicleid,$shift1_sdate,$shift1_edate,$dbtable_location); 
		$total_isi_malam = $this->countfuel_ISI($vehicleid,$shift2_sdate,$shift2_edate,$dbtable_location);  */
		
		$data_isi_pagi = $this->countfuel_ISI_avg($vehicleid,$shift1_sdate,$shift1_edate,$dbtable_location); 
		$data_isi_malam = $this->countfuel_ISI_avg($vehicleid,$shift2_sdate,$shift2_edate,$dbtable_location); 
		
		$ex_isi_pagi = explode("|",$data_isi_pagi);
		$total_isi_pagi = $ex_isi_pagi[0];
		$time_isi_pagi = $ex_isi_pagi[1];
		
		$ex_isi_malam = explode("|",$data_isi_malam);
		$total_isi_malam = $ex_isi_malam[0];
		$time_isi_malam = $ex_isi_malam[1];
	
		printf("===TOTAL ISI SHIFT PAGI AVG: %s %s \r\n", $total_isi_pagi, $time_isi_pagi);
		printf("===TOTAL ISI SHIFT MALAM AVG: %s  %s\r\n", $total_isi_malam, $time_isi_malam);
		
		$total_isi = $total_isi_pagi + $total_isi_malam;
			
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
							
		return $total_isi."|".$total_isi_pagi."|".$time_isi_pagi."|".$total_isi_malam."|".$time_isi_malam;
	}
	
	//min max
	function countfuel_ISI_minmax($vehicleid,$sdate,$edate,$dbtable_location){
		
		//print_r($sdate." ".$edate." ".$dbtable_location." ".$vehicleid);exit();
		$total_isi = 0;
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_fuel_data,location_report_gps_time");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $sdate);
		$this->dbreport->where("location_report_gps_time <=", $edate);
		$this->dbreport->where("location_report_fuel_data >", 0);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result_array();
		$total_loc = count($rows_loc);
		$array = $rows_loc;
		$isi_value = "";
		$time_isi = "";
		foreach ($array as $k => $v) {
		  $tArray[$k] = $v['location_report_fuel_data'];
		}
		
		if($total_loc>0){
			$min_value = min($tArray);
			$max_value = max($tArray);
			$isi_value = $max_value - $min_value;
			printf("===Min: %s MAx: %s Delta: %s \r\n", $min_value, $max_value, $isi_value);
			
			
			$this->dbreport = $this->load->database("tensor_report",true); 
			$this->dbreport->select("location_report_fuel_data,location_report_gps_time");
			$this->dbreport->order_by("location_report_gps_time","asc");
			$this->dbreport->group_by("location_report_gps_time");
			$this->dbreport->where("location_report_vehicle_id", $vehicleid);
			$this->dbreport->where("location_report_gps_time >=", $sdate);
			$this->dbreport->where("location_report_gps_time <=", $edate);
			$this->dbreport->where("location_report_fuel_data ", $max_value);
			$this->dbreport->where("location_report_speed", 0);
			$this->dbreport->from($dbtable_location);
			$q_loc_t = $this->dbreport->get();
			$rows_loc_t = $q_loc_t->row();
			if(count($rows_loc_t)>0){
				$time_isi = $rows_loc_t->location_report_gps_time;
				printf("===Time ISI: %s \r\n", $time_isi);
			}
		}
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
			
		return $isi_value."|".$time_isi;
	}
	
	//avg
	function countfuel_ISI_avg($vehicleid,$sdate,$edate,$dbtable_location){
		
		//print_r($sdate." ".$edate." ".$dbtable_location." ".$vehicleid);exit();
		$total_isi = 0;
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_fuel_data,location_report_gps_time");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $sdate);
		$this->dbreport->where("location_report_gps_time <=", $edate);
		$this->dbreport->where("location_report_fuel_data >", 0);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		$total_loc = count($rows_loc);
		$delta_ar = array();
		$time_isi = ""; 
		for($x=0;$x<$total_loc;$x++)
		{
			$nosort = $x+1;
			$a = 0;
			//printf("==Data Loop: %s : %s  \r\n", $nosort, $total_loc);
			if($nosort == $total_loc)
			{
				//printf("==Akhir: %s : %s  \r\n", $nosort, $total_loc);
			}
			else
			{	
				$first_data = $rows_loc[$x]->location_report_fuel_data;
				$next_data = $rows_loc[$x+1]->location_report_fuel_data;
				$delta_cons = $first_data - $next_data;
				$delta_isi = $next_data - $first_data;
				
				$first_time = $rows_loc[$x]->location_report_gps_time;
				$next_time = $rows_loc[$x+1]->location_report_gps_time;
				
				$first_time_sec = strtotime($first_time);
				$next_time_sec = strtotime($next_time);
				$delta_time_sec = $next_time_sec - $first_time_sec;  
				
				if($delta_isi != 0){
					
					if(($next_data > $first_data) && ($delta_isi > 15)){
						
						printf("===Pengisian: %s  %s %s s \r\n", $delta_isi, $rows_loc[$x]->location_report_gps_time, $delta_time_sec);
						$total_isi = $total_isi + $delta_isi;
						array_push($delta_ar,$delta_isi);
						$time_isi = $first_time; 
					}else{
						
						//printf("===Konsumsi BBM: %s %s \r\n", $delta_cons, $rows_loc[$x]->location_report_gps_time);
						//$total_cons = $total_cons + $delta_cons;
					}
				}
				
			}
		}
		
		printf("=== ===\r\n");
		
		$sum = array_sum($delta_ar); 
		$count = count($delta_ar); 
		if($count >0){
			$avg = $sum / $count;	
		}else{
			
			$avg = 0;
		}
		
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
			
		return $avg."|".$time_isi;
	}
	
	
	
	function pool_perunit($userid="",$orderby="",$typereport="",$startdate="",$enddate="",$starttime="",$endtime="")
	{
		
		$report = "webtracking_ts_req_report";
		$report_location = "location_";
		$report_ritase = "ritase_";
		if ($startdate == "") {
            $startdate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != ""){
            $startdate = date("Y-m-d H:i:s", strtotime($startdate." ".$starttime));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
		
		if ($enddate == "") {
            $enddate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($enddate != ""){
            $enddate = date("Y-m-d H:i:s", strtotime($enddate." ".$endtime));
			$month = date("F", strtotime($enddate));
			$year = date("Y", strtotime($enddate));
        }
       
		//print_r($startdate." ".$enddate);exit();
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_location = $report_location."januari_".$year;
			$dbtable_ritase = $report_ritase."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_location = $report_location."februari_".$year;
			$dbtable_ritase = $report_ritase."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_location = $report_location."maret_".$year;
			$dbtable_ritase = $report_ritase."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_location = $report_location."april_".$year;
			$dbtable_ritase = $report_ritase."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_location = $report_location."mei_".$year;
			$dbtable_ritase = $report_ritase."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_location = $report_location."juni_".$year;
			$dbtable_ritase = $report_ritase."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_location = $report_location."juli_".$year;
			$dbtable_ritase = $report_ritase."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_location = $report_location."agustus_".$year;
			$dbtable_ritase = $report_ritase."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_location = $report_location."september_".$year;
			$dbtable_ritase = $report_ritase."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_location = $report_location."oktober_".$year;
			$dbtable_ritase = $report_ritase."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_location = $report_location."november_".$year;
			$dbtable_ritase = $report_ritase."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_location = $report_location."desember_".$year;
			$dbtable_ritase = $report_ritase."desember_".$year;
			break;
		}
		
		printf("===STARTING REPORT %s %s \r\n", $startdate, $enddate);
		$this->db = $this->load->database("default",true); 
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_mv03,vehicle_type,vehicle_company,vehicle_sensor");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_no", "GECL 916");
		
		//$this->db->limit(3);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		//print_r($rows);exit();
		
		$street_list = $this->getAllStreetPool($userid);
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			$total_dur_in_pool = 0;
			$model_data = "pool_perunit";
		
 			//exit();
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$vehicleid = $rows[$i]->vehicle_id;
				$vehicleuserid = $rows[$i]->vehicle_user_id;
				$deviceid = $rows[$i]->vehicle_device;
				$vehicleno = $rows[$i]->vehicle_no;
				$vehiclename = $rows[$i]->vehicle_name;
				$vehiclemv03 = $rows[$i]->vehicle_mv03;
				$vehicle_company = $rows[$i]->vehicle_company;
				$vehicletype = $rows[$i]->vehicle_type;
				$vehiclesensor = $rows[$i]->vehicle_sensor;
				$company_name = $this->getCompanyName($vehicle_company);
				
				$req_location = "";
				$req_geofence_in = "";
				$req_geofence_out = "";
				$duration_sec = "";
				$show = "";
				
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $vehicleno, $nourut, $total_rows);
				$data_in_pool = $this->getDataInPool_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$street_list);
				$total_data_in_pool = count($data_in_pool);
				
				if(count($data_in_pool)>0){
					
					$req_location = $data_in_pool[$total_data_in_pool-1]->location_report_location;
					$req_geofence_in = $data_in_pool[0]->location_report_gps_time;
					$req_geofence_out = $data_in_pool[$total_data_in_pool-1]->location_report_gps_time;
					
									$duration = get_time_difference($startdate, $enddate); 
									 
									$start_1 = dbmaketime($startdate);
									$end_1 = dbmaketime($enddate);
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
					
					printf("===LOCATION: %s DURATION %s \r\n", $req_location, $show);
				}
				
				
					unset($datainsert);
					$datainsert["req_vehicle_user_id"] = $vehicleuserid;
					$datainsert["req_vehicle_id"] = $vehicleid;
					$datainsert["req_vehicle_device"] = $deviceid;
					$datainsert["req_vehicle_mv03"] = $vehiclemv03;
					$datainsert["req_vehicle_no"] = $vehicleno;
					$datainsert["req_vehicle_name"] = $vehiclename;
					$datainsert["req_vehicle_type"] = $vehicletype;
					$datainsert["req_company_id"] = $vehicle_company;
					$datainsert["req_company_name"] = $company_name;
					$datainsert["req_model"] = $model_data;
					$datainsert["req_report_date"] = date("Y-m-d", strtotime($startdate));
					$datainsert["req_report_stime"] = date("H:i:s", strtotime($startdate));
					$datainsert["req_report_etime"] = date("H:i:s", strtotime($enddate));
					$datainsert["req_location"] = $req_location;
					$datainsert["req_geofence_in"] = $req_geofence_in;
					$datainsert["req_geofence_out"] = $req_geofence_out;
					$datainsert["req_duration_sec"] = $duration_sec;
					$datainsert["req_duration_text"] = $show;
					
					//get last data
					$this->dbts = $this->load->database("webtracking_ts",true); 
					$this->dbts->where("req_vehicle_id", $vehicleid);
					$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
					$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
					$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
					$this->dbts->where("req_model",$model_data);
					$q_last = $this->dbts->get($report);
					$row_last = $q_last->row();
					$total_last = count($row_last);
					
					if($total_last>0){
						$this->dbts = $this->load->database("webtracking_ts",true); 
						$this->dbts->where("req_vehicle_id", $vehicleid);
						$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
						$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
						$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
						$this->dbts->where("req_model",$model_data);
						$this->dbts->update($report,$datainsert);
						printf("!==UPDATE OK \r\n ");
					}else{
						
						$this->dbts->insert($report,$datainsert);
						printf("===INSERT OK \r\n");
					}
			}
			
			$this->dbts->close();
			$this->dbts->cache_delete_all();
		}else{
			printf("===========TIDAK ADA DATA VEHICLE======== \r\n"); 
		}
		
		printf("===========SELESAI======== \r\n"); 
		
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function rom_perunit($userid="",$orderby="",$typereport="",$startdate="",$enddate="",$starttime="",$endtime="")
	{
		
		$report = "webtracking_ts_req_report";
		$report_location = "location_";
		$report_ritase = "ritase_";
		if ($startdate == "") {
            $startdate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != ""){
            $startdate = date("Y-m-d H:i:s", strtotime($startdate." ".$starttime));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
		
		if ($enddate == "") {
            $enddate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($enddate != ""){
            $enddate = date("Y-m-d H:i:s", strtotime($enddate." ".$endtime));
			$month = date("F", strtotime($enddate));
			$year = date("Y", strtotime($enddate));
        }
       
		//print_r($startdate." ".$enddate);exit();
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_location = $report_location."januari_".$year;
			$dbtable_ritase = $report_ritase."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_location = $report_location."februari_".$year;
			$dbtable_ritase = $report_ritase."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_location = $report_location."maret_".$year;
			$dbtable_ritase = $report_ritase."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_location = $report_location."april_".$year;
			$dbtable_ritase = $report_ritase."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_location = $report_location."mei_".$year;
			$dbtable_ritase = $report_ritase."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_location = $report_location."juni_".$year;
			$dbtable_ritase = $report_ritase."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_location = $report_location."juli_".$year;
			$dbtable_ritase = $report_ritase."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_location = $report_location."agustus_".$year;
			$dbtable_ritase = $report_ritase."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_location = $report_location."september_".$year;
			$dbtable_ritase = $report_ritase."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_location = $report_location."oktober_".$year;
			$dbtable_ritase = $report_ritase."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_location = $report_location."november_".$year;
			$dbtable_ritase = $report_ritase."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_location = $report_location."desember_".$year;
			$dbtable_ritase = $report_ritase."desember_".$year;
			break;
		}
		
		printf("===STARTING REPORT %s %s \r\n", $startdate, $enddate);
		$this->db = $this->load->database("default",true); 
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_mv03,vehicle_type,vehicle_company,vehicle_sensor");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_no", "GECL 916");
		
		//$this->db->limit(3);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		//print_r($rows);exit();
		
		$rom_list = $this->getAllStreetROM($userid);
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			$total_dur_in_pool = 0;
			$model_data = "rom_perunit";
		
 			//exit();
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$vehicleid = $rows[$i]->vehicle_id;
				$vehicleuserid = $rows[$i]->vehicle_user_id;
				$deviceid = $rows[$i]->vehicle_device;
				$vehicleno = $rows[$i]->vehicle_no;
				$vehiclename = $rows[$i]->vehicle_name;
				$vehiclemv03 = $rows[$i]->vehicle_mv03;
				$vehicle_company = $rows[$i]->vehicle_company;
				$vehicletype = $rows[$i]->vehicle_type;
				$vehiclesensor = $rows[$i]->vehicle_sensor;
				$company_name = $this->getCompanyName($vehicle_company);
				
				$req_location = "";
				$req_geofence_in = "";
				$req_geofence_out = "";
				$duration_sec = "";
				$show = "";
				
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $vehicleno, $nourut, $total_rows);
				$data_in_pool = $this->getDataInROM_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$rom_list);
				$total_data_in_pool = count($data_in_pool);
				//print_r($total_data_in_pool);exit();
				if(count($data_in_pool)>0){
					
					$req_location = $data_in_pool[$total_data_in_pool-1]->location_report_location;
					$req_geofence_in = $data_in_pool[0]->location_report_gps_time;
					$req_geofence_out = $data_in_pool[$total_data_in_pool-1]->location_report_gps_time;
					
									$duration = get_time_difference($startdate, $enddate); 
									 
									$start_1 = dbmaketime($startdate);
									$end_1 = dbmaketime($enddate);
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
					
					printf("===LOCATION: %s DURATION %s \r\n", $req_location, $show);
				}
				
				
					unset($datainsert);
					$datainsert["req_vehicle_user_id"] = $vehicleuserid;
					$datainsert["req_vehicle_id"] = $vehicleid;
					$datainsert["req_vehicle_device"] = $deviceid;
					$datainsert["req_vehicle_mv03"] = $vehiclemv03;
					$datainsert["req_vehicle_no"] = $vehicleno;
					$datainsert["req_vehicle_name"] = $vehiclename;
					$datainsert["req_vehicle_type"] = $vehicletype;
					$datainsert["req_company_id"] = $vehicle_company;
					$datainsert["req_company_name"] = $company_name;
					$datainsert["req_model"] = $model_data;
					$datainsert["req_report_date"] = date("Y-m-d", strtotime($startdate));
					$datainsert["req_report_stime"] = date("H:i:s", strtotime($startdate));
					$datainsert["req_report_etime"] = date("H:i:s", strtotime($enddate));
					$datainsert["req_location"] = $req_location;
					$datainsert["req_geofence_in"] = $req_geofence_in;
					$datainsert["req_geofence_out"] = $req_geofence_out;
					$datainsert["req_duration_sec"] = $duration_sec;
					$datainsert["req_duration_text"] = $show;
					
					//get last data
					$this->dbts = $this->load->database("webtracking_ts",true); 
					$this->dbts->where("req_vehicle_id", $vehicleid);
					$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
					$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
					$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
					$this->dbts->where("req_model",$model_data);
					$q_last = $this->dbts->get($report);
					$row_last = $q_last->row();
					$total_last = count($row_last);
					
					if($total_last>0){
						$this->dbts = $this->load->database("webtracking_ts",true); 
						$this->dbts->where("req_vehicle_id", $vehicleid);
						$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
						$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
						$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
						$this->dbts->where("req_model",$model_data);
						$this->dbts->update($report,$datainsert);
						printf("!==UPDATE OK \r\n ");
					}else{
						
						$this->dbts->insert($report,$datainsert);
						printf("===INSERT OK \r\n");
					}
			}
			
			$this->dbts->close();
			$this->dbts->cache_delete_all();
		}else{
			printf("===========TIDAK ADA DATA VEHICLE======== \r\n"); 
		}
		
		printf("===========SELESAI======== \r\n"); 
		
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function port_perunit($userid="",$orderby="",$typereport="",$startdate="",$enddate="",$starttime="",$endtime="")
	{
		
		$report = "webtracking_ts_req_report";
		$report_location = "location_";
		$report_ritase = "ritase_";
		if ($startdate == "") {
            $startdate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != ""){
            $startdate = date("Y-m-d H:i:s", strtotime($startdate." ".$starttime));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
		
		if ($enddate == "") {
            $enddate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($enddate != ""){
            $enddate = date("Y-m-d H:i:s", strtotime($enddate." ".$endtime));
			$month = date("F", strtotime($enddate));
			$year = date("Y", strtotime($enddate));
        }
       
		//print_r($startdate." ".$enddate);exit();
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_location = $report_location."januari_".$year;
			$dbtable_ritase = $report_ritase."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_location = $report_location."februari_".$year;
			$dbtable_ritase = $report_ritase."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_location = $report_location."maret_".$year;
			$dbtable_ritase = $report_ritase."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_location = $report_location."april_".$year;
			$dbtable_ritase = $report_ritase."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_location = $report_location."mei_".$year;
			$dbtable_ritase = $report_ritase."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_location = $report_location."juni_".$year;
			$dbtable_ritase = $report_ritase."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_location = $report_location."juli_".$year;
			$dbtable_ritase = $report_ritase."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_location = $report_location."agustus_".$year;
			$dbtable_ritase = $report_ritase."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_location = $report_location."september_".$year;
			$dbtable_ritase = $report_ritase."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_location = $report_location."oktober_".$year;
			$dbtable_ritase = $report_ritase."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_location = $report_location."november_".$year;
			$dbtable_ritase = $report_ritase."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_location = $report_location."desember_".$year;
			$dbtable_ritase = $report_ritase."desember_".$year;
			break;
		}
		
		printf("===STARTING REPORT %s %s \r\n", $startdate, $enddate);
		$this->db = $this->load->database("default",true); 
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_mv03,vehicle_type,vehicle_company,vehicle_sensor");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_no", "GECL 916");
		
		//$this->db->limit(3);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		//print_r($rows);exit();
		
		$port_list = $this->getAllStreetPort($userid);
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			$total_dur_in_pool = 0;
			$model_data = "port_perunit";
		
 			//exit();
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$vehicleid = $rows[$i]->vehicle_id;
				$vehicleuserid = $rows[$i]->vehicle_user_id;
				$deviceid = $rows[$i]->vehicle_device;
				$vehicleno = $rows[$i]->vehicle_no;
				$vehiclename = $rows[$i]->vehicle_name;
				$vehiclemv03 = $rows[$i]->vehicle_mv03;
				$vehicle_company = $rows[$i]->vehicle_company;
				$vehicletype = $rows[$i]->vehicle_type;
				$vehiclesensor = $rows[$i]->vehicle_sensor;
				$company_name = $this->getCompanyName($vehicle_company);
				
				$req_location = "";
				$req_geofence_in = "";
				$req_geofence_out = "";
				$duration_sec = "";
				$show = "";
				
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $vehicleno, $nourut, $total_rows);
				$data_in_port = $this->getDataInPORT_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$port_list);
				$total_data_in_port = count($data_in_port);
				//print_r($total_data_in_pool);exit();
				if(count($data_in_port)>0){
					
					$req_location = $data_in_port[$total_data_in_port-1]->location_report_location;
					$req_geofence_in = $data_in_port[0]->location_report_gps_time;
					$req_geofence_out = $data_in_port[$total_data_in_port-1]->location_report_gps_time;
					
									$duration = get_time_difference($startdate, $enddate); 
									 
									$start_1 = dbmaketime($startdate);
									$end_1 = dbmaketime($enddate);
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
					
					printf("===LOCATION: %s DURATION %s \r\n", $req_location, $show);
				}
				
				
					unset($datainsert);
					$datainsert["req_vehicle_user_id"] = $vehicleuserid;
					$datainsert["req_vehicle_id"] = $vehicleid;
					$datainsert["req_vehicle_device"] = $deviceid;
					$datainsert["req_vehicle_mv03"] = $vehiclemv03;
					$datainsert["req_vehicle_no"] = $vehicleno;
					$datainsert["req_vehicle_name"] = $vehiclename;
					$datainsert["req_vehicle_type"] = $vehicletype;
					$datainsert["req_company_id"] = $vehicle_company;
					$datainsert["req_company_name"] = $company_name;
					$datainsert["req_model"] = $model_data;
					$datainsert["req_report_date"] = date("Y-m-d", strtotime($startdate));
					$datainsert["req_report_stime"] = date("H:i:s", strtotime($startdate));
					$datainsert["req_report_etime"] = date("H:i:s", strtotime($enddate));
					$datainsert["req_location"] = $req_location;
					$datainsert["req_geofence_in"] = $req_geofence_in;
					$datainsert["req_geofence_out"] = $req_geofence_out;
					$datainsert["req_duration_sec"] = $duration_sec;
					$datainsert["req_duration_text"] = $show;
					
					//get last data
					$this->dbts = $this->load->database("webtracking_ts",true); 
					$this->dbts->where("req_vehicle_id", $vehicleid);
					$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
					$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
					$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
					$this->dbts->where("req_model",$model_data);
					$q_last = $this->dbts->get($report);
					$row_last = $q_last->row();
					$total_last = count($row_last);
					
					if($total_last>0){
						$this->dbts = $this->load->database("webtracking_ts",true); 
						$this->dbts->where("req_vehicle_id", $vehicleid);
						$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
						$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
						$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
						$this->dbts->where("req_model",$model_data);
						$this->dbts->update($report,$datainsert);
						printf("!==UPDATE OK \r\n ");
					}else{
						
						$this->dbts->insert($report,$datainsert);
						printf("===INSERT OK \r\n");
					}
			}
			
			$this->dbts->close();
			$this->dbts->cache_delete_all();
		}else{
			printf("===========TIDAK ADA DATA VEHICLE======== \r\n"); 
		}
		
		printf("===========SELESAI======== \r\n"); 
		
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function hauling_perunit($userid="",$orderby="",$typereport="",$startdate="",$enddate="",$starttime="",$endtime="")
	{
		
		$report = "webtracking_ts_req_report";
		$report_location = "location_";
		$report_ritase = "ritase_";
		if ($startdate == "") {
            $startdate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != ""){
            $startdate = date("Y-m-d H:i:s", strtotime($startdate." ".$starttime));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
		
		if ($enddate == "") {
            $enddate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($enddate != ""){
            $enddate = date("Y-m-d H:i:s", strtotime($enddate." ".$endtime));
			$month = date("F", strtotime($enddate));
			$year = date("Y", strtotime($enddate));
        }
       
		//print_r($startdate." ".$enddate);exit();
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_location = $report_location."januari_".$year;
			$dbtable_ritase = $report_ritase."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_location = $report_location."februari_".$year;
			$dbtable_ritase = $report_ritase."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_location = $report_location."maret_".$year;
			$dbtable_ritase = $report_ritase."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_location = $report_location."april_".$year;
			$dbtable_ritase = $report_ritase."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_location = $report_location."mei_".$year;
			$dbtable_ritase = $report_ritase."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_location = $report_location."juni_".$year;
			$dbtable_ritase = $report_ritase."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_location = $report_location."juli_".$year;
			$dbtable_ritase = $report_ritase."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_location = $report_location."agustus_".$year;
			$dbtable_ritase = $report_ritase."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_location = $report_location."september_".$year;
			$dbtable_ritase = $report_ritase."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_location = $report_location."oktober_".$year;
			$dbtable_ritase = $report_ritase."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_location = $report_location."november_".$year;
			$dbtable_ritase = $report_ritase."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_location = $report_location."desember_".$year;
			$dbtable_ritase = $report_ritase."desember_".$year;
			break;
		}
		
		printf("===STARTING REPORT %s %s \r\n", $startdate, $enddate);
		$this->db = $this->load->database("default",true); 
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_mv03,vehicle_type,vehicle_company,vehicle_sensor");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_no", "GECL 916");
		
		//$this->db->limit(3);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		//print_r($rows);exit();
		
		$hauling_list = $this->getAllStreetKM($userid);
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			$total_dur_in_pool = 0;
			$model_data = "hauling_perunit";
		
 			//exit();
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$vehicleid = $rows[$i]->vehicle_id;
				$vehicleuserid = $rows[$i]->vehicle_user_id;
				$deviceid = $rows[$i]->vehicle_device;
				$vehicleno = $rows[$i]->vehicle_no;
				$vehiclename = $rows[$i]->vehicle_name;
				$vehiclemv03 = $rows[$i]->vehicle_mv03;
				$vehicle_company = $rows[$i]->vehicle_company;
				$vehicletype = $rows[$i]->vehicle_type;
				$vehiclesensor = $rows[$i]->vehicle_sensor;
				$company_name = $this->getCompanyName($vehicle_company);
				
				$req_location = "";
				$req_geofence_in = "";
				$req_geofence_out = "";
				$duration_sec = "";
				$show = "";
				
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $vehicleno, $nourut, $total_rows);
				$data_in_street = $this->getDataInHauling_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$hauling_list);
				$total_data_in_street = count($data_in_street);
				//print_r($total_data_in_pool);exit();
				if(count($data_in_street)>0){
					
					$req_location = $data_in_street[$total_data_in_street-1]->location_report_location;
					$req_geofence_in = $data_in_street[0]->location_report_gps_time;
					$req_geofence_out = $data_in_street[$total_data_in_street-1]->location_report_gps_time;
					
									$duration = get_time_difference($startdate, $enddate); 
									 
									$start_1 = dbmaketime($startdate);
									$end_1 = dbmaketime($enddate);
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
					
					printf("===LOCATION: %s DURATION %s \r\n", $req_location, $show);
				}
				
				
					unset($datainsert);
					$datainsert["req_vehicle_user_id"] = $vehicleuserid;
					$datainsert["req_vehicle_id"] = $vehicleid;
					$datainsert["req_vehicle_device"] = $deviceid;
					$datainsert["req_vehicle_mv03"] = $vehiclemv03;
					$datainsert["req_vehicle_no"] = $vehicleno;
					$datainsert["req_vehicle_name"] = $vehiclename;
					$datainsert["req_vehicle_type"] = $vehicletype;
					$datainsert["req_company_id"] = $vehicle_company;
					$datainsert["req_company_name"] = $company_name;
					$datainsert["req_model"] = $model_data;
					$datainsert["req_report_date"] = date("Y-m-d", strtotime($startdate));
					$datainsert["req_report_stime"] = date("H:i:s", strtotime($startdate));
					$datainsert["req_report_etime"] = date("H:i:s", strtotime($enddate));
					$datainsert["req_location"] = $req_location;
					$datainsert["req_geofence_in"] = $req_geofence_in;
					$datainsert["req_geofence_out"] = $req_geofence_out;
					$datainsert["req_duration_sec"] = $duration_sec;
					$datainsert["req_duration_text"] = $show;
					
					//get last data
					$this->dbts = $this->load->database("webtracking_ts",true); 
					$this->dbts->where("req_vehicle_id", $vehicleid);
					$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
					$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
					$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
					$this->dbts->where("req_model",$model_data);
					$q_last = $this->dbts->get($report);
					$row_last = $q_last->row();
					$total_last = count($row_last);
					
					if($total_last>0){
						$this->dbts = $this->load->database("webtracking_ts",true); 
						$this->dbts->where("req_vehicle_id", $vehicleid);
						$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
						$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
						$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
						$this->dbts->where("req_model",$model_data);
						$this->dbts->update($report,$datainsert);
						printf("!==UPDATE OK \r\n ");
					}else{
						
						$this->dbts->insert($report,$datainsert);
						printf("===INSERT OK \r\n");
					}
			}
			
			$this->dbts->close();
			$this->dbts->cache_delete_all();
		}else{
			printf("===========TIDAK ADA DATA VEHICLE======== \r\n"); 
		}
		
		printf("===========SELESAI======== \r\n"); 
		
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function outhauling_perunit($userid="",$orderby="",$typereport="",$startdate="",$enddate="",$starttime="",$endtime="")
	{
		
		$report = "webtracking_ts_req_report";
		$report_location = "location_";
		$report_ritase = "ritase_";
		if ($startdate == "") {
            $startdate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != ""){
            $startdate = date("Y-m-d H:i:s", strtotime($startdate." ".$starttime));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
		
		if ($enddate == "") {
            $enddate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($enddate != ""){
            $enddate = date("Y-m-d H:i:s", strtotime($enddate." ".$endtime));
			$month = date("F", strtotime($enddate));
			$year = date("Y", strtotime($enddate));
        }
       
		//print_r($startdate." ".$enddate);exit();
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_location = $report_location."januari_".$year;
			$dbtable_ritase = $report_ritase."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_location = $report_location."februari_".$year;
			$dbtable_ritase = $report_ritase."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_location = $report_location."maret_".$year;
			$dbtable_ritase = $report_ritase."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_location = $report_location."april_".$year;
			$dbtable_ritase = $report_ritase."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_location = $report_location."mei_".$year;
			$dbtable_ritase = $report_ritase."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_location = $report_location."juni_".$year;
			$dbtable_ritase = $report_ritase."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_location = $report_location."juli_".$year;
			$dbtable_ritase = $report_ritase."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_location = $report_location."agustus_".$year;
			$dbtable_ritase = $report_ritase."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_location = $report_location."september_".$year;
			$dbtable_ritase = $report_ritase."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_location = $report_location."oktober_".$year;
			$dbtable_ritase = $report_ritase."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_location = $report_location."november_".$year;
			$dbtable_ritase = $report_ritase."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_location = $report_location."desember_".$year;
			$dbtable_ritase = $report_ritase."desember_".$year;
			break;
		}
		
		printf("===STARTING REPORT %s %s \r\n", $startdate, $enddate);
		$this->db = $this->load->database("default",true); 
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_mv03,vehicle_type,vehicle_company,vehicle_sensor");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_no", "GECL 916");
		
		//$this->db->limit(3);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		//print_r($rows);exit();
		
		$hauling_list = $this->getAllStreetData($userid);
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			$total_dur_in_pool = 0;
			$model_data = "outhauling_perunit";
		
 			//exit();
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$vehicleid = $rows[$i]->vehicle_id;
				$vehicleuserid = $rows[$i]->vehicle_user_id;
				$deviceid = $rows[$i]->vehicle_device;
				$vehicleno = $rows[$i]->vehicle_no;
				$vehiclename = $rows[$i]->vehicle_name;
				$vehiclemv03 = $rows[$i]->vehicle_mv03;
				$vehicle_company = $rows[$i]->vehicle_company;
				$vehicletype = $rows[$i]->vehicle_type;
				$vehiclesensor = $rows[$i]->vehicle_sensor;
				$company_name = $this->getCompanyName($vehicle_company);
				
				$req_location = "";
				$req_geofence_in = "";
				$req_geofence_out = "";
				$duration_sec = "";
				$show = "";
				
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $vehicleno, $nourut, $total_rows);
				$data_in_haulingout = $this->getDataInHaulingOut_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$hauling_list);
				$total_data_in_haulingout= count($data_in_haulingout);
				//print_r($total_data_in_pool);exit();
				if(count($data_in_haulingout)>0){
					
					$req_location = $data_in_haulingout[$total_data_in_haulingout-1]->location_report_location;
					$req_geofence_in = $data_in_haulingout[0]->location_report_gps_time;
					$req_geofence_out = $data_in_haulingout[$total_data_in_haulingout-1]->location_report_gps_time;
					
									$duration = get_time_difference($startdate, $enddate); 
									 
									$start_1 = dbmaketime($startdate);
									$end_1 = dbmaketime($enddate);
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
					
					printf("===LOCATION: %s DURATION %s \r\n", $req_location, $show);
				}
				
				
					unset($datainsert);
					$datainsert["req_vehicle_user_id"] = $vehicleuserid;
					$datainsert["req_vehicle_id"] = $vehicleid;
					$datainsert["req_vehicle_device"] = $deviceid;
					$datainsert["req_vehicle_mv03"] = $vehiclemv03;
					$datainsert["req_vehicle_no"] = $vehicleno;
					$datainsert["req_vehicle_name"] = $vehiclename;
					$datainsert["req_vehicle_type"] = $vehicletype;
					$datainsert["req_company_id"] = $vehicle_company;
					$datainsert["req_company_name"] = $company_name;
					$datainsert["req_model"] = $model_data;
					$datainsert["req_report_date"] = date("Y-m-d", strtotime($startdate));
					$datainsert["req_report_stime"] = date("H:i:s", strtotime($startdate));
					$datainsert["req_report_etime"] = date("H:i:s", strtotime($enddate));
					$datainsert["req_location"] = $req_location;
					$datainsert["req_geofence_in"] = $req_geofence_in;
					$datainsert["req_geofence_out"] = $req_geofence_out;
					$datainsert["req_duration_sec"] = $duration_sec;
					$datainsert["req_duration_text"] = $show;
					
					//get last data
					$this->dbts = $this->load->database("webtracking_ts",true); 
					$this->dbts->where("req_vehicle_id", $vehicleid);
					$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
					$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
					$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
					$this->dbts->where("req_model",$model_data);
					$q_last = $this->dbts->get($report);
					$row_last = $q_last->row();
					$total_last = count($row_last);
					
					if($total_last>0){
						$this->dbts = $this->load->database("webtracking_ts",true); 
						$this->dbts->where("req_vehicle_id", $vehicleid);
						$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
						$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
						$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
						$this->dbts->where("req_model",$model_data);
						$this->dbts->update($report,$datainsert);
						printf("!==UPDATE OK \r\n ");
					}else{
						
						$this->dbts->insert($report,$datainsert);
						printf("===INSERT OK \r\n");
					}
			}
			
			$this->dbts->close();
			$this->dbts->cache_delete_all();
		}else{
			printf("===========TIDAK ADA DATA VEHICLE======== \r\n"); 
		}
		
		printf("===========SELESAI======== \r\n"); 
		
		$this->db->close();
		$this->db->cache_delete_all();
	}
	
	function antrian_port_perunit($userid="",$orderby="",$startdate=""){
		
		printf("===STARTING ANTRIAN \r\n");
		$start_time = date("Y-m-d H:i:s");
		$this->dbts = $this->load->database("webtracking_ts", TRUE);
		$this->dbts->order_by("hour_time","asc");
		$this->dbts->select("hour_time,hour_name");
		$this->dbts->where("hour_flag",0);
		$this->dbts->where("hour_user", $userid);
		$q = $this->dbts->get("ts_hour");
		$rows = $q->result();
		$totalrows = count($rows);
		printf("===TOTAL HOUR %s \r\n", $totalrows);
		$j = 1;
		
		for ($i=0;$i<$totalrows;$i++)
		{
			$starttime = $rows[$i]->hour_time;
			$endtime = date("H:i:s",strtotime($rows[$i]->hour_name.":"."59".":"."59"));
			printf("TOTAL DATE HOUR : %s %s %s \r\n",$startdate,$starttime,$endtime);
			$processdata = $this->antrian_port_data($userid,$orderby,0,$startdate,$startdate,$starttime,$endtime);
		}
		$finish_time = date("Y-m-d H:i:s");
		
		//send telegram
		$title_name = "PORB BIB ANTRIAN PER HOUR";
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
	
	function antrian_port_data($userid,$orderby,$typereport,$startdate,$enddate,$starttime,$endtime)
	{
		
		$report = "webtracking_ts_req_report";
		$report_location = "location_";
		$report_ritase = "ritase_";
		if ($startdate == "") {
            $startdate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != ""){
            $startdate = date("Y-m-d H:i:s", strtotime($startdate." ".$starttime));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
		
		if ($enddate == "") {
            $enddate = date("Y-m-d H:i:s", strtotime("yesterday"));
            $month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($enddate != ""){
            $enddate = date("Y-m-d H:i:s", strtotime($enddate." ".$endtime));
			$month = date("F", strtotime($enddate));
			$year = date("Y", strtotime($enddate));
        }
       
		//print_r($startdate." ".$enddate);exit();
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_location = $report_location."januari_".$year;
			$dbtable_ritase = $report_ritase."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_location = $report_location."februari_".$year;
			$dbtable_ritase = $report_ritase."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_location = $report_location."maret_".$year;
			$dbtable_ritase = $report_ritase."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_location = $report_location."april_".$year;
			$dbtable_ritase = $report_ritase."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_location = $report_location."mei_".$year;
			$dbtable_ritase = $report_ritase."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_location = $report_location."juni_".$year;
			$dbtable_ritase = $report_ritase."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_location = $report_location."juli_".$year;
			$dbtable_ritase = $report_ritase."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_location = $report_location."agustus_".$year;
			$dbtable_ritase = $report_ritase."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_location = $report_location."september_".$year;
			$dbtable_ritase = $report_ritase."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_location = $report_location."oktober_".$year;
			$dbtable_ritase = $report_ritase."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_location = $report_location."november_".$year;
			$dbtable_ritase = $report_ritase."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_location = $report_location."desember_".$year;
			$dbtable_ritase = $report_ritase."desember_".$year;
			break;
		}
		
		printf("===STARTING REPORT %s %s \r\n", $startdate, $enddate);
		$this->db = $this->load->database("default",true); 
		$this->db->order_by("vehicle_id",$orderby);
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_name,vehicle_device,vehicle_no,vehicle_mv03,vehicle_type,vehicle_company,vehicle_sensor");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_no", "GECL 916");
		
		//$this->db->limit(3);
		$this->db->from("vehicle");
        $q = $this->db->get();
        $rows = $q->result();
		//print_r($rows);exit();
		
		$antrian_list = array("Port BIB - Antrian");
		
		if(count($rows)>0){
			$total_rows = count($rows);
			printf("===JUMLAH VEHICLE : %s \r\n", $total_rows);
			$total_dur_in_pool = 0;
			$model_data = "antrian_perunit";
		
 			//exit();
			for($i=0;$i<$total_rows;$i++)
			{
				$nourut = $i+1;
				$vehicleid = $rows[$i]->vehicle_id;
				$vehicleuserid = $rows[$i]->vehicle_user_id;
				$deviceid = $rows[$i]->vehicle_device;
				$vehicleno = $rows[$i]->vehicle_no;
				$vehiclename = $rows[$i]->vehicle_name;
				$vehiclemv03 = $rows[$i]->vehicle_mv03;
				$vehicle_company = $rows[$i]->vehicle_company;
				$vehicletype = $rows[$i]->vehicle_type;
				$vehiclesensor = $rows[$i]->vehicle_sensor;
				$company_name = $this->getCompanyName($vehicle_company);
				
				$req_location = "";
				$req_geofence_in = "";
				$req_geofence_out = "";
				$duration_sec = "";
				$show = "";
				
				printf("===PERIODE : %s to %s : %s (%s of %s) \r\n", $startdate, $enddate, $vehicleno, $nourut, $total_rows);
				$data_in_pool = $this->getDataInPortBIBAntrian_report($vehicleid,$vehicle_company,$startdate,$enddate,$typereport,$dbtable_location,$antrian_list);
				$total_data_in_pool = count($data_in_pool);
				//print_r($total_data_in_pool);exit();
				if(count($data_in_pool)>0){
					
					$req_location = $data_in_pool[$total_data_in_pool-1]->location_report_location;
					$req_geofence_in = $data_in_pool[0]->location_report_gps_time;
					$req_geofence_out = $data_in_pool[$total_data_in_pool-1]->location_report_gps_time;
					
									$duration = get_time_difference($startdate, $enddate); 
									 
									$start_1 = dbmaketime($startdate);
									$end_1 = dbmaketime($enddate);
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
					
					printf("===LOCATION: %s DURATION %s \r\n", $req_location, $show);
				}
				
				
					unset($datainsert);
					$datainsert["req_vehicle_user_id"] = $vehicleuserid;
					$datainsert["req_vehicle_id"] = $vehicleid;
					$datainsert["req_vehicle_device"] = $deviceid;
					$datainsert["req_vehicle_mv03"] = $vehiclemv03;
					$datainsert["req_vehicle_no"] = $vehicleno;
					$datainsert["req_vehicle_name"] = $vehiclename;
					$datainsert["req_vehicle_type"] = $vehicletype;
					$datainsert["req_company_id"] = $vehicle_company;
					$datainsert["req_company_name"] = $company_name;
					$datainsert["req_model"] = $model_data;
					$datainsert["req_report_date"] = date("Y-m-d", strtotime($startdate));
					$datainsert["req_report_stime"] = date("H:i:s", strtotime($startdate));
					$datainsert["req_report_etime"] = date("H:i:s", strtotime($enddate));
					$datainsert["req_location"] = $req_location;
					$datainsert["req_geofence_in"] = $req_geofence_in;
					$datainsert["req_geofence_out"] = $req_geofence_out;
					$datainsert["req_duration_sec"] = $duration_sec;
					$datainsert["req_duration_text"] = $show;
					
					//get last data
					$this->dbts = $this->load->database("webtracking_ts",true); 
					$this->dbts->where("req_vehicle_id", $vehicleid);
					$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
					$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
					$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
					$this->dbts->where("req_model",$model_data);
					$q_last = $this->dbts->get($report);
					$row_last = $q_last->row();
					$total_last = count($row_last);
					
					if($total_last>0){
						$this->dbts = $this->load->database("webtracking_ts",true); 
						$this->dbts->where("req_vehicle_id", $vehicleid);
						$this->dbts->where("req_report_date",date("Y-m-d", strtotime($startdate)));
						$this->dbts->where("req_report_stime",date("H:i:s", strtotime($starttime)));
						$this->dbts->where("req_report_etime",date("H:i:s", strtotime($endtime)));
						$this->dbts->where("req_model",$model_data);
						$this->dbts->update($report,$datainsert);
						printf("!==UPDATE OK \r\n ");
					}else{
						
						$this->dbts->insert($report,$datainsert);
						printf("===INSERT OK \r\n");
					}
			}
			
			$this->dbts->close();
			$this->dbts->cache_delete_all();
		}else{
			printf("===========TIDAK ADA DATA VEHICLE======== \r\n"); 
		}
		
		printf("===========SELESAI======== \r\n"); 
		
		$this->db->close();
		$this->db->cache_delete_all();
	}
								
	function getDataInPool_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location,$street_list){
		
		$master_report = array("location_idle","location_off");
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_location,location_report_name,location_report_speed");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		$this->dbreport->where("location_report_speed", 0);
		$this->dbreport->where_in("location_report_name",$master_report);
		$this->dbreport->where_in("location_report_location", $street_list);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
					
		return $rows_loc;
	}
	
	function getDataInROM_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location,$street_list){
		
		$master_report = array("location_idle","location_off","location");
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_location,location_report_name,location_report_speed");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		//$this->dbreport->where("location_report_speed", 0);
		//$this->dbreport->where_in("location_report_name",$master_report);
		$this->dbreport->where_in("location_report_location", $street_list);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
					
		return $rows_loc;
	}
	
	function getDataInPORT_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location,$street_list){
		
		$master_report = array("location_idle","location_off","location");
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_location,location_report_name,location_report_speed");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		//$this->dbreport->where("location_report_speed", 0);
		//$this->dbreport->where_in("location_report_name",$master_report);
		$this->dbreport->where_in("location_report_location", $street_list);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
					
		return $rows_loc;
	}
	
	function getDataInHauling_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location,$street_list){
		
		$master_report = array("location_idle","location_off","location");
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_location,location_report_name,location_report_speed");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		//$this->dbreport->where("location_report_speed", 0);
		//$this->dbreport->where_in("location_report_name",$master_report);
		$this->dbreport->where_in("location_report_location", $street_list);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
					
		return $rows_loc;
	}
	
	function getDataInHaulingOut_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location,$street_list){
		
		$master_report = array("location_idle","location_off","location");
		$not_in = array("KM 5.5","KM 6","ST1","ST1","ST3","ST4","ST5","ST6","ST7","ST8","ST9");
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_location,location_report_name,location_report_speed");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		//$this->dbreport->where("location_report_speed", 0);
		//$this->dbreport->where_in("location_report_name",$master_report);
		$this->dbreport->where_not_in("location_report_location", $street_list);
		$this->dbreport->where_not_in("location_report_location", $not_in);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
					
		return $rows_loc;
	}
	
	function getDataInPortBIBAntrian_report($vehicleid,$companyid,$startdate,$enddate,$typereport,$dbtable_location,$street_list){
		
		$master_report = array("location_idle","location_off","location");
		
		$this->dbreport = $this->load->database("tensor_report",true); 
		$this->dbreport->select("location_report_id,location_report_gps_time,location_report_location,location_report_name,location_report_speed");
		$this->dbreport->order_by("location_report_gps_time","asc");
		$this->dbreport->group_by("location_report_gps_time");
		$this->dbreport->where("location_report_vehicle_id", $vehicleid);
		$this->dbreport->where("location_report_gps_time >=", $startdate);
		$this->dbreport->where("location_report_gps_time <=", $enddate);
		//$this->dbreport->where("location_report_speed", 0);
		//$this->dbreport->where_in("location_report_name",$master_report);
		$this->dbreport->where_in("location_report_location", $street_list);
		$this->dbreport->from($dbtable_location);
        $q_loc = $this->dbreport->get();
        $rows_loc = $q_loc->result();
		
		return $rows_loc;
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
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
