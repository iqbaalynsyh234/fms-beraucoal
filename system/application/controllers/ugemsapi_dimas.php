<?php
include "base.php";

class Ugemsapi_dimas extends Base {

	function Ugemsapi_dimas()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("dashboardmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
    $this->load->model('m_ugemsmodel');
	}

	/* LIST API
		1. getlocationreport
		2. getlocationhour
		3. getmasterpelanggaran
		4. API STATUS REPORT OVERSPEED -> getstatusoverspeedreport
		5. API STATUS REPORT LOCATION REPORT -> getstatuslocationreport
	*/

	function getmasterpelanggaran()
	{
		//ini_set('display_errors', 1);
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token      = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata   = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$payload    = "";
		$now        = date("Ymd");

		$headers = null;
		if (isset($_SERVER['Authorization']))
		{
            $headers = trim($_SERVER["Authorization"]);
        }
		else if (isset($_SERVER['HTTP_AUTHORIZATION']))
		{ //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        }
        else if (function_exists('apache_request_headers'))
        {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization']))
            {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
		//print_r($headers." || ".$token." || ".$postdata->UserId);exit();

		$payload      		    = array(
			"UserId"          => $postdata->UserId
		);

		if($headers != $token)
    {
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid Authorization Key ! ";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid User ID";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}else{

			if ($postdata->UserId != 4204) {
				$feature["code"]    = 400;
				$feature["msg"]     = "Invalid User ID";
				$feature["payload"] = $payload;
				echo json_encode($feature);
				exit;
			}else {
				//hanya user yg terdaftar yg bisa akes API
				$this->db->where("api_user",$postdata->UserId);
				$this->db->where("api_token",$headers);
				$this->db->where("api_status",1);
				$this->db->where("api_flag",0);
				$q = $this->db->get("api_user");
				if($q->num_rows == 0)
				{
					$feature["code"]    = 400;
					$feature["msg"]     = "User & Authorization Key is Not Available!";
					$feature["payload"] = $payload;
					echo json_encode($feature);
					exit;
				}else{

					$UserIDBIB    = 4408;
					$this->dbts = $this->load->database("webtracking_ts", true);
					$this->dbts->order_by("level_name","asc");
					$this->dbts->where("level_user", $UserIDBIB);
					$this->dbts->where("level_flag",0);
					$q          = $this->dbts->get("ts_speed_level");
					$speedlevel = $q->result();
				}
			}
		}


		//jika mobil lebih dari nol
		if(count($speedlevel) > 0)
		{

			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($speedlevel);$z++)
			{
				$DataToUpload[$z]->LevelName         = $speedlevel[$z]->level_name;
				$DataToUpload[$z]->LevelAlias        = $speedlevel[$z]->level_alias;
				$DataToUpload[$z]->LevelValue        = $speedlevel[$z]->level_value;
				$DataToUpload[$z]->LevelUser         = $speedlevel[$z]->level_user;
				$DataToUpload[$z]->LevelType         = $speedlevel[$z]->level_type;
				$DataToUpload[$z]->LevelValueMin     = $speedlevel[$z]->level_value_min;
				$DataToUpload[$z]->LevelValueMax     = $speedlevel[$z]->level_value_max;
				$DataToUpload[$z]->LevelSanksiLubang = $speedlevel[$z]->level_sanksi_lubang;
				$DataToUpload[$z]->LevelSanksiSkors  = $speedlevel[$z]->level_sanksi_skors;

				//$datajson["Data"] = $DataToUpload;
			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			// echo "<pre>";
			// var_dump($content);die();
			// echo "<pre>";

			// echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Master Data Pelanggaran Overspeed (IM KTT)",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();
		}
		exit;
	}

	function getlocationreport(){
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token            = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata         = json_decode(file_get_contents("php://input"));
		$allvehicle       = 0;
		$now              = date("Ymd");
		$payload          = "";
    $forbidden_symbol = array("'", ",", ".", "?", "!", ";", ":", "-");

		$headers = null;
		if (isset($_SERVER['Authorization']))
		{
        $headers = trim($_SERVER["Authorization"]);
    }
		else if (isset($_SERVER['HTTP_AUTHORIZATION']))
		{ //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    }
    else if (function_exists('apache_request_headers'))
    {
      $requestHeaders = apache_request_headers();
      // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
      $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
      if (isset($requestHeaders['Authorization']))
      {
        $headers = trim($requestHeaders['Authorization']);
      }
    }

		if($headers != $token)
    {
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid Authorization Key ! ";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid User ID";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}else{

			//hanya user yg terdaftar yg bisa akes API
			$this->db->where("api_user",$postdata->UserId);
			$this->db->where("api_token",$headers);
			$this->db->where("api_status",1);
			$this->db->where("api_flag",0);
			$q = $this->db->get("api_user");
			if($q->num_rows == 0)
			{
				$feature["code"]    = 400;
				$feature["msg"]     = "User & Authorization Key is Not Available!";
				$feature["payload"] = $payload;
				echo json_encode($feature);
				exit;
			}

		}

    $payload = array(
     "UserId"            => $postdata->UserId,
		 "VehicleNo"         => $postdata->VehicleNo,
     "StartTime"    	 	 => $postdata->StartTime,
		 "EndTime"    	 	   => $postdata->EndTime,
		 "Status" 		       => $postdata->Status,
   );

	 if(!isset($postdata->StartTime) || $postdata->StartTime == "")
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid Start Date Time";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }

	 if(!isset($postdata->EndTime) || $postdata->EndTime == "")
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid End Date Time";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }

	 if($postdata->StartTime != "" && $postdata->EndTime != ""){
		 $startdur = $postdata->StartTime * 60;
		 $enddur = $postdata->EndTime * 60;
	 }

	 // if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "")
	 // {
		//  $feature["code"]    = 400;
		//  $feature["msg"]     = "Invalid Vehicle No";
		//  $feature["payload"] = $payload;
		//  echo json_encode($feature);
		//  exit;
	 // }else {
		 $this->db->order_by("vehicle_id","desc");
		 
		 if ($postdata->VehicleNo != "" && $postdata->VehicleDevice != "") {
			 $this->db->where("vehicle_no",$postdata->VehicleNo);
			 $this->db->where("vehicle_device",$postdata->VehicleDevice);
		 }elseif ($postdata->VehicleNo != "") {
			 $this->db->where("vehicle_no",$postdata->VehicleNo);
		 }elseif ($postdata->VehicleDevice != "") {
			 $this->db->where("vehicle_device",$postdata->VehicleDevice);
		 }else {
  			 $feature["code"] = 400;
  			 $feature["msg"] = "Vehicle Not Found!";
  			 $feature["payload"]    = $payload;
  			 echo json_encode($feature);
  			 exit;
		 }
		 $this->db->where("vehicle_user_id",4408);
		 $this->db->where("vehicle_status",1);
		 //$this->db->where("vehicle_active_date2 >",$now); //tidak expired
		 $q = $this->db->get("vehicle");
		 $vehicle = $q->result();

		 if($q->num_rows == 0)
		 {
			 $feature["code"] = 400;
			 $feature["msg"] = "Vehicle Not Found!";
			 $feature["payload"]    = $payload;
			 echo json_encode($feature);
			 exit;
		 }
	 // }

	 	if (!isset($postdata->Status) || $postdata->Status == "") {
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid Status Type";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
	 	}else {
	 		$statusfix = "";
				if ($postdata->Status == "all") {
					$statusfix = "all";
				}elseif ($postdata->Status == "move") {
					$statusfix = "location";
				}elseif ($postdata->Status == "idle") {
					$statusfix = "location_idle";
				}elseif ($postdata->Status == "off") {
					$statusfix = "location_off";
				}
	 	}

		// echo "<pre>";
		// var_dump($statusfix);die();
		// echo "<pre>";

	 $report        = "location_"; // new report
	 $report_sum    = "summary_";

	 $sdate         = date("Y-m-d H:i:s", strtotime($postdata->StartTime));
	 $edate         = date("Y-m-d H:i:s", strtotime($postdata->EndTime));

	 $d1            = date("d", strtotime($postdata->StartTime));
	 $d2            = date("d", strtotime($postdata->EndTime));

	 $m1            = date("F", strtotime($postdata->StartTime));
	 $m2            = date("F", strtotime($postdata->EndTime));
	 $year          = date("Y", strtotime($postdata->StartTime));
	 $year2         = date("Y", strtotime($postdata->EndTime));
	 $rows          = array();
	 $rows2         = array();
	 $total_q       = 0;
	 $total_q2      = 0;

	 $error         = "";
	 $rows_summary  = "";

	 $location_list = array("location","location_off","location_idle");

	 if ($postdata->VehicleNo == "")
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid Vehicle No";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }

	 if ($d1 != $d2)
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid Date Time. Date time must be in the same date";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }

	 if ($m1 != $m2)
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid Date Time. Date time must be in the same month";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }

	 if ($year != $year2)
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid Date Time. Date time must be in the same year";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }

	 if ($error != "")
	 {
		 $callback['error'] = true;
		 $callback['message'] = $error;

		 echo json_encode($callback);
		 return;
	 }

	 switch ($m1)
	 {
		 case "January":
					 $dbtable = $report."januari_".$year;
		 $dbtable_sum = $report_sum."januari_".$year;
		 break;
		 case "February":
					 $dbtable = $report."februari_".$year;
		 $dbtable_sum = $report_sum."februari_".$year;
		 break;
		 case "March":
					 $dbtable = $report."maret_".$year;
		 $dbtable_sum = $report_sum."maret_".$year;
		 break;
		 case "April":
					 $dbtable = $report."april_".$year;
		 $dbtable_sum = $report_sum."april_".$year;
		 break;
		 case "May":
					 $dbtable = $report."mei_".$year;
		 $dbtable_sum = $report_sum."mei_".$year;
		 break;
		 case "June":
					 $dbtable = $report."juni_".$year;
		 $dbtable_sum = $report_sum."juni_".$year;
		 break;
		 case "July":
					 $dbtable = $report."juli_".$year;
		 $dbtable_sum = $report_sum."juli_".$year;
		 break;
		 case "August":
					 $dbtable = $report."agustus_".$year;
		 $dbtable_sum = $report_sum."agustus_".$year;
		 break;
		 case "September":
					 $dbtable = $report."september_".$year;
		 $dbtable_sum = $report_sum."september_".$year;
		 break;
		 case "October":
					 $dbtable = $report."oktober_".$year;
		 $dbtable_sum = $report_sum."oktober_".$year;
		 break;
		 case "November":
					 $dbtable = $report."november_".$year;
		 $dbtable_sum = $report_sum."november_".$year;
		 break;
		 case "December":
					 $dbtable = $report."desember_".$year;
		 $dbtable_sum = $report_sum."desember_".$year;
		 break;
	 }

	 switch ($m2)
	 {
		 case "January":
					 $dbtable2 = $report."januari_".$year;
		 $dbtable2_sum = $report_sum."januari_".$year;
		 break;
		 case "February":
					 $dbtable2 = $report."februari_".$year;
		 $dbtable2_sum = $report_sum."februari_".$year;
		 break;
		 case "March":
					 $dbtable2 = $report."maret_".$year;
		 $dbtable2_sum = $report_sum."maret_".$year;
		 break;
		 case "April":
					 $dbtable2 = $report."april_".$year;
		 $dbtable2_sum = $report_sum."april_".$year;
		 break;
		 case "May":
					 $dbtable2 = $report."mei_".$year;
		 $dbtable2_sum = $report_sum."mei_".$year;
		 break;
		 case "June":
					 $dbtable2 = $report."juni_".$year;
		 $dbtable2_sum = $report_sum."juni_".$year;
		 break;
		 case "July":
					 $dbtable2 = $report."juli_".$year;
		 $dbtable2_sum = $report_sum."juli_".$year;
		 break;
		 case "August":
					 $dbtable2 = $report."agustus_".$year;
		 $dbtable2_sum = $report_sum."agustus_".$year;
		 break;
		 case "September":
					 $dbtable2 = $report."september_".$year;
		 $dbtable2_sum = $report_sum."september_".$year;
		 break;
		 case "October":
					 $dbtable2 = $report."oktober_".$year;
		 $dbtable2_sum = $report_sum."oktober_".$year;
		 break;
		 case "November":
					 $dbtable2 = $report."november_".$year;
		 $dbtable2_sum = $report_sum."november_".$year;
		 break;
		 case "December":
					 $dbtable2 = $report."desember_".$year;
		 $dbtable2_sum = $report_sum."desember_".$year;
		 break;
	 }

	 // echo "<pre>";
	 // var_dump($dbtable);die();
	 // echo "<pre>";

		 $this->dbtrip = $this->load->database("tensor_report",true);
		 $this->dbtrip->order_by("location_report_gps_time","asc");

		 if ($postdata->VehicleNo != "" && $postdata->VehicleDevice != "") {
			 if($postdata->VehicleNo != "all"){
				$this->dbtrip->where("location_report_vehicle_no", $postdata->VehicleNo);
			}

			if($postdata->VehicleDevice != "all"){
				$this->dbtrip->where("location_report_vehicle_device",$postdata->VehicleDevice);
			}
		 }elseif ($postdata->VehicleNo != "") {
			 if($postdata->VehicleNo != "all"){
				$this->dbtrip->where("location_report_vehicle_no", $postdata->VehicleNo);
			}
		 }elseif ($postdata->VehicleDevice != "") {
			 if($postdata->VehicleNo != "all"){
				$this->dbtrip->where("location_report_vehicle_device", $postdata->VehicleDevice);
			}
		 }

		 if($statusfix != "all"){
			 $this->dbtrip->where("location_report_name", $statusfix);
		 }

		 $this->dbtrip->where("location_report_gps_time >=",$sdate);
		 $this->dbtrip->where("location_report_gps_time <=", $edate);

		 $q = $this->dbtrip->get($dbtable);
		 $rows = $q->result();

		 // $dbtable.'-'.$dbtable2.'-'.$dbtable2_sum
		 // $vehicle.'-'.$type_location.'-'.$location_end.'-'.$statusname.'-'.$type_speed

		 // echo "<pre>";
		 // var_dump($rows);die();
		 // echo "<pre>";

	 $datafix = array();

	 if(sizeof($rows) > 0)
	 {
		 for ($i=0; $i < sizeof($rows); $i++) {

			 if ($rows[$i]->location_report_name == "location") {
				 $reportnamefix = "move";
			 }elseif ($rows[$i]->location_report_name == "location_idle") {
				 $reportnamefix = "idle";
			 }elseif ($rows[$i]->location_report_name == "location_off") {
				 $reportnamefix = "off";
			 }

		 	array_push($datafix, array(
				 // "ReportId"                          => $rows[$i]->location_report_id,
	       "VehicleUserId"                     => $rows[$i]->location_report_vehicle_user_id,
	       "VehicleId"                         => $rows[$i]->location_report_vehicle_id,
	       "VehicleDevice"                     => $rows[$i]->location_report_vehicle_device,
	       "VehicleNo"                         => $rows[$i]->location_report_vehicle_no,
	       "VehicleName"                       => $rows[$i]->location_report_vehicle_name,
	       "VehicleType"                       => $rows[$i]->location_report_vehicle_type,
	       "VehicleCompany"                    => $rows[$i]->location_report_vehicle_company,
	       "VehicleImei" 		                   => $rows[$i]->location_report_imei,
	       // "ReportType"                        => $rows[$i]->location_report_type,
	       "ReportName"                        => $reportnamefix,
	       "ReportSpeed"                       => $rows[$i]->location_report_speed,
	       "ReportGpsStatus"                   => $rows[$i]->location_report_gpsstatus,
	       "ReportGpsTime"                     => $rows[$i]->location_report_gps_time,
	       // "ReportGeofenceId"                  => $rows[$i]->location_report_geofence_id,
	       "ReportGeofenceName"                => $rows[$i]->location_report_geofence_name,
	       // "ReportGeofenceLimit"               => $rows[$i]->location_report_geofence_limit,
	       "ReportGeofenceType"                => $rows[$i]->location_report_geofence_type,
	       "ReportJalur"                       => $rows[$i]->location_report_jalur,
	       "ReportDirection"                   => $rows[$i]->location_report_direction,
	       "ReportLocation"                    => $rows[$i]->location_report_location,
	       "ReportCoordinate"                  => $rows[$i]->location_report_coordinate,
	       // "location_report_latitude"       => $rows[$i]->location_report_latitude,
	       // "location_report_longitude"      => $rows[$i]->location_report_longitude,
	       "ReportOdometer"                    => $rows[$i]->location_report_odometer,
	       "Report_fuel_data"                  => $rows[$i]->location_report_fuel_data,
	       // "location_report_fuel_data_fix"  => $rows[$i]->location_report_fuel_data_fix,
	       // "location_report_fuel_liter"     => $rows[$i]->location_report_fuel_liter,
	       // "location_report_fuel_liter_fix" => $rows[$i]->location_report_fuel_liter_fix,
	       // "location_report_view"           => $rows[$i]->location_report_view,
	       // "location_report_event"          => $rows[$i]->location_report_event,
	       "ReportGsm"                         => $rows[$i]->location_report_gsm,
	       "ReportSat"                         => $rows[$i]->location_report_sat
			));
		 }
		 echo json_encode(array("code" => 200, "msg" => "success",  "data" => $datafix, "payload" => $payload), JSON_NUMERIC_CHECK);
	 }
	 else
	 {
		 echo json_encode(array("code" => 200, "msg" => "success",  "data" => array(), "payload" => $payload), JSON_NUMERIC_CHECK);
	 }

	 // INI DIAKTIFKAN UNTUK MENCATAT HIT DARI API
	 $nowendtime = date("Y-m-d H:i:s");
	 $this->insertHitAPI("API Location Report", $payload, $nowstarttime, $nowendtime);
	 $this->db->close();
	 $this->db->cache_delete_all();

	 exit;
	}

  function getlocationhour(){
    $nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token            = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata         = json_decode(file_get_contents("php://input"));
		$allvehicle       = 0;
		$now              = date("Ymd");
		$payload          = "";
    $forbidden_symbol = array("'", ",", ".", "?", "!", ";", ":", "-");

		$headers = null;
		if (isset($_SERVER['Authorization']))
		{
        $headers = trim($_SERVER["Authorization"]);
    }
		else if (isset($_SERVER['HTTP_AUTHORIZATION']))
		{ //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    }
    else if (function_exists('apache_request_headers'))
    {
      $requestHeaders = apache_request_headers();
      // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
      $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
      if (isset($requestHeaders['Authorization']))
      {
        $headers = trim($requestHeaders['Authorization']);
      }
    }

		if($headers != $token)
    {
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid Authorization Key ! ";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid User ID";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}else{

			//hanya user yg terdaftar yg bisa akes API
			$this->db->where("api_user",$postdata->UserId);
			$this->db->where("api_token",$headers);
			$this->db->where("api_status",1);
			$this->db->where("api_flag",0);
			$q = $this->db->get("api_user");
			if($q->num_rows == 0)
			{
				$feature["code"]    = 400;
				$feature["msg"]     = "User & Authorization Key is Not Available!";
				$feature["payload"] = $payload;
				echo json_encode($feature);
				exit;
			}

		}

    $payload = array(
     "UserId"    => $postdata->UserId,
     "Date" 	 	 => $postdata->Date,
		 "Hour" 		 => $postdata->Hour,
     "Shift"     => $postdata->Shift,
     "CompanyId" => $postdata->CompanyId
   );

   // echo "<pre>";
   // var_dump($payload);die();
   // echo "<pre>";

   if($postdata->Shift == "" || $postdata->Shift > 2 || (!is_numeric($postdata->Shift)))
   {
     $feature["code"]    = 400;
     $feature["msg"]     = "Invalid Shift";
     $feature["payload"] = $payload;
     echo json_encode($feature);
     exit;
   }else {
     $shiftfix = $postdata->Shift;
   }

    if($postdata->CompanyId == "")
		{
			$feature["code"]    = 400;
			$feature["msg"]     = "Company ID is empty";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}
		else
		{
      $company       = $postdata->CompanyId;

      // CEK SYMBOL TERLARANG
      if ($this->strposa($company, $forbidden_symbol, 1)) {
          $symbolfounded = 1;
      } else {
          $symbolfounded = 0;
      }

        if ($symbolfounded == 1) {
          $feature["code"]    = 400;
    			$feature["msg"]     = "CompanyID is only can be filled by ID or all";
    			$feature["payload"] = $payload;
    			echo json_encode($feature);
    			exit;
        }

        if ($company == "all") {
          $data_company = $postdata->CompanyId;
        }else {
          $data_company = $this->m_ugemsmodel->getcompanyname_byID($company);
            if ($data_company == "-") {
              $feature["code"]    = 400;
              $feature["msg"]     = "Invalid Company ID";
              $feature["payload"] = $payload;
              echo json_encode($feature);
              exit;
            }else {
              $data_company = $data_company[0]->company_id;
            }
        }
    }

    if($postdata->Date == "")
		{
			$feature["code"]    = 400;
			$feature["msg"]     = "Date can not be empty";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}
		else
		{
			$sdate = $postdata->Date;
		}

		if(!isset($postdata->Hour) || $postdata->Hour == "")
		{
			$feature["code"]    = 400;
			$feature["msg"]     = "Time can not be empty";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}else {
			$shourfix 		= "all";
			$shour        = $postdata->Hour;
				if ($shour != "all") {
					$checkformat  = $this->verify_time_format($shour);
						if ($checkformat == false) {
							$feature["code"]    = 400;
							$feature["msg"]     = "Invalid Hour Format";
							$feature["payload"] = $payload;
							echo json_encode($feature);
							exit;
						}else {
							$shourfix = $shour;
						}
				}
		}

		// echo "<pre>";
		// var_dump($shourfix);die();
		// echo "<pre>";

    // PENCARIAN DIMULAI
    $company  = $data_company;
    $datein   = $sdate;
    $shift    = $shiftfix;
    $date     = date("Y-m-d", strtotime($datein));

    $lastdate = date("Y-m-t", strtotime($datein));
    $year     = date("Y", strtotime($datein));
    $month    = date("m", strtotime($datein));
    $day      = date('d', strtotime($datein));
    $day++;
    $jmlday = strlen($day);
    if ($jmlday == 1) {
        $day = "0" . $day;
    }
    $next = $year . "-" . $month . "-" . $day;

    if ($next > $lastdate) {
        if ($month == 12) {
            $y = $year + 1;
            $next = $y . "-01-01";
        } else {
            $m = $month + 1;
            $jmlmonth = strlen($m);
            if ($jmlmonth == 1) {
                $m = "0" . $m;
            }
            $next = $year . "-" . $m . "-01";
        }
    }
    $arraydate = array("date" => $date, "next date" => $next, "last date" => $lastdate);
    $input = array(
        "company" => $company,
        "date"    => $arraydate,
        "shift"   => $shift
    );

    $this->dbts = $this->load->database("webtracking_ts", true);
    if ($shift == 1) {
				// $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
        $this->dbts->select("location_report_vehicle_user_id, location_report_vehicle_id, location_report_vehicle_device, location_report_vehicle_no,
														location_report_vehicle_name, location_report_vehicle_type, location_report_vehicle_company, location_report_imei,
														location_report_type, location_report_speed, location_report_engine, location_report_gpsstatus, location_report_gps_time,
														location_report_gps_date, location_report_gps_hour, location_report_jalur, location_report_direction, location_report_location,
														location_report_coordinate, location_report_hauling, location_report_group");
        $shift = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00");
        $this->dbts->where("location_report_gps_date", $date);
        if ($company != 0) {
            $this->dbts->where("location_report_vehicle_company", $company);
        }
					if ($shourfix != "all") {
						$this->dbts->where("location_report_gps_hour", $shourfix);
					}else {
						$this->dbts->where_in("location_report_gps_hour", $shift);
					}
        $this->dbts->order_by("location_report_gps_hour", "asc");
        $this->dbts->order_by("location_report_company_name", "asc");
        $result = $this->dbts->get("ts_location_hour");
        $data = $result->result_array();
        $nr = $result->num_rows();
    } else if ($shift == 2) {
        // $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
				$this->dbts->select("location_report_vehicle_user_id, location_report_vehicle_id, location_report_vehicle_device, location_report_vehicle_no,
														location_report_vehicle_name, location_report_vehicle_type, location_report_vehicle_company, location_report_imei,
														location_report_type, location_report_speed, location_report_engine, location_report_gpsstatus, location_report_gps_time,
														location_report_gps_date, location_report_gps_hour, location_report_jalur, location_report_direction, location_report_location,
														location_report_coordinate, location_report_hauling, location_report_group");
        $shift1 = array("18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
        $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
        $this->dbts->where("location_report_gps_date", $date);
        if ($company != 0) {
            $this->dbts->where("location_report_vehicle_company", $company);
        }
        // $this->dbts->where_in("location_report_gps_hour", $shift1);
					if ($shourfix != "all") {
						$this->dbts->where("location_report_gps_hour", $shourfix);
					}else {
						$this->dbts->where_in("location_report_gps_hour", $shift1);
					}
        $this->dbts->order_by("location_report_gps_hour", "asc");
        $this->dbts->order_by("location_report_company_name", "asc");
        $result = $this->dbts->get("ts_location_hour");
        $data1 = $result->result_array();
        $nr1 = $result->num_rows();
        $this->dbts->distinct();
        // $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
				$this->dbts->select("location_report_vehicle_user_id, location_report_vehicle_id, location_report_vehicle_device, location_report_vehicle_no,
														location_report_vehicle_name, location_report_vehicle_type, location_report_vehicle_company, location_report_imei,
														location_report_type, location_report_speed, location_report_engine, location_report_gpsstatus, location_report_gps_time,
														location_report_gps_date, location_report_gps_hour, location_report_jalur, location_report_direction, location_report_location,
														location_report_coordinate, location_report_hauling, location_report_group");
        $this->dbts->where("location_report_gps_date", $next);
        if ($company != 0) {
            $this->dbts->where("location_report_vehicle_company", $company);
        }
        // $this->dbts->where_in("location_report_gps_hour", $shift2);
					if ($shourfix != "all") {
						$this->dbts->where("location_report_gps_hour", $shourfix);
					}else {
						$this->dbts->where_in("location_report_gps_hour", $shift2);
					}
        $this->dbts->order_by("location_report_gps_hour", "asc");
        $this->dbts->order_by("location_report_company_name", "asc");
        $result = $this->dbts->get("ts_location_hour");
        $data2 = $result->result_array();
        $nr2 = $result->num_rows();
        $data = array_merge($data1, $data2);
        $nr = $nr1 +  $nr2;
    } else {
        // $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
				$this->dbts->select("location_report_vehicle_user_id, location_report_vehicle_id, location_report_vehicle_device, location_report_vehicle_no,
														location_report_vehicle_name, location_report_vehicle_type, location_report_vehicle_company, location_report_imei,
														location_report_type, location_report_speed, location_report_engine, location_report_gpsstatus, location_report_gps_time,
														location_report_gps_date, location_report_gps_hour, location_report_jalur, location_report_direction, location_report_location,
														location_report_coordinate, location_report_hauling, location_report_group");
        $shift1 = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
        $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
        $this->dbts->where("location_report_gps_date", $date);
        if ($company != 0) {
            $this->dbts->where("location_report_vehicle_company", $company);
        }
        // $this->dbts->where_in("location_report_gps_hour", $shift1);
					if ($shourfix != "all") {
						$this->dbts->where("location_report_gps_hour", $shourfix);
					}else {
						$this->dbts->where_in("location_report_gps_hour", $shift1);
					}
        $this->dbts->order_by("location_report_gps_hour", "asc");
        $this->dbts->order_by("location_report_company_name", "asc");
        $result = $this->dbts->get("ts_location_hour");
        $data1 = $result->result_array();
        $nr1 = $result->num_rows();
        $this->dbts->distinct();
        // $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
				$this->dbts->select("location_report_vehicle_user_id, location_report_vehicle_id, location_report_vehicle_device, location_report_vehicle_no,
														location_report_vehicle_name, location_report_vehicle_type, location_report_vehicle_company, location_report_imei,
														location_report_type, location_report_speed, location_report_engine, location_report_gpsstatus, location_report_gps_time,
														location_report_gps_date, location_report_gps_hour, location_report_jalur, location_report_direction, location_report_location,
														location_report_coordinate, location_report_hauling, location_report_group");
        $this->dbts->where("location_report_gps_date", $next);
        if ($company != 0) {
            $this->dbts->where("location_report_vehicle_company", $company);
        }
        // $this->dbts->where_in("location_report_gps_hour", $shift2);
				if ($shourfix != "all") {
					$this->dbts->where("location_report_gps_hour", $shourfix);
				}else {
					$this->dbts->where_in("location_report_gps_hour", $shift2);
				}
        $this->dbts->order_by("location_report_gps_hour", "asc");
        $this->dbts->order_by("location_report_company_name", "asc");
        $result = $this->dbts->get("ts_location_hour");
        $data2 = $result->result_array();
        $nr2 = $result->num_rows();
        $data = array_merge($data1, $data2);
        $nr = $nr1 +  $nr2;
    }

    $datafix = array();
    for ($i=0; $i < sizeof($data); $i++) {
      array_push($datafix, array(
        "VehicleUserId"    => $data[$i]['location_report_vehicle_user_id'],
        "VehicleId"        => $data[$i]['location_report_vehicle_id'],
        "VehicleDevice"    => $data[$i]['location_report_vehicle_device'],
        "VehicleNo"        => $data[$i]['location_report_vehicle_no'],
        "VehicleName"      => $data[$i]['location_report_vehicle_name'],
        "VehicleType"      => $data[$i]['location_report_vehicle_type'],
        "VehicleCompany"   => $data[$i]['location_report_vehicle_company'],
        "VehicleImei"      => $data[$i]['location_report_imei'],
        "ReportType"       => $data[$i]['location_report_type'],
				"ReportSpeed"      => $data[$i]['location_report_speed'],
				"ReportEngine"     => $data[$i]['location_report_engine'],
				"GpsStatus"        => $data[$i]['location_report_gpsstatus'],
				"GpsTime"          => $data[$i]['location_report_gps_time'],
				"GpsDate"          => $data[$i]['location_report_gps_date'],
				"GpsHour"          => $data[$i]['location_report_gps_hour'],
				"ReportJalur"      => $data[$i]['location_report_jalur'],
				"ReportDirection"  => $data[$i]['location_report_direction'],
				"ReportLocation"   => $data[$i]['location_report_location'],
				"ReportCoordinate" => $data[$i]['location_report_coordinate'],
				"ReportHauling"    => $data[$i]['location_report_hauling'],
				"ReportGroup"      => $data[$i]['location_report_group']
      ));
    }


    if ($nr > 0) {
				echo json_encode(array("code" => 200, "msg" => "success",  "data" => $datafix, "payload" => $payload), JSON_NUMERIC_CHECK);
    } else {
        echo json_encode(array("code" => 200, "msg" => "Data Empty"));
    }

    // INI DIAKTIFKAN UNTUK MENCATAT HIT DARI API
    $nowendtime = date("Y-m-d H:i:s");
    $this->insertHitAPI("API Location Hour", $payload, $nowstarttime, $nowendtime);
    $this->db->close();
    $this->db->cache_delete_all();

		exit;
  }

	function verify_time_format($value) {
	  $pattern1 = '/^(0?\d|1\d|2[0-3]):[0-5]\d:[0-5]\d$/';
	  $pattern2 = '/^(0?\d|1[0-2]):[0-5]\d\s(am|pm)$/i';
	  return preg_match ($pattern1, $value) || preg_match ($pattern2, $value);
	}

  function strposa($haystack, $needles=array(), $offset=0) {
        $chr = array();
        foreach($needles as $needle) {
                $res = strpos($haystack, $needle, $offset);
                if ($res !== false) $chr[$needle] = $res;
        }
        if(empty($chr)) return false;
        return min($chr);
}

  function getoverspeed_data($dbtable, $company, $vehicle, $sdate, $edate)
	{

		$nowdate_report = date("Y-m-d", strtotime($sdate));
		$now = date("Y-m-d");
		if ($now == $nowdate_report) {
			// jika kondisi alert hari ini
			$month = date('F');
			$year = date('Y');
			$overspeed = "overspeed_hour_";
			$report = "overspeed_hour_";
			switch ($month) {
				case "January":
					$dbtable = $report . "januari_" . $year;
					$dboverspeed = $overspeed . "januari_" . $year;
					break;
				case "February":
					$dbtable = $report . "februari_" . $year;
					$dboverspeed = $overspeed . "februari_" . $year;
					break;
				case "March":
					$dbtable = $report . "maret_" . $year;
					$dboverspeed = $overspeed . "maret_" . $year;
					break;
				case "April":
					$dbtable = $report . "april_" . $year;
					$dboverspeed = $overspeed . "april_" . $year;
					break;
				case "May":
					$dbtable = $report . "mei_" . $year;
					$dboverspeed = $overspeed . "mei_" . $year;
					break;
				case "June":
					$dbtable = $report . "juni_" . $year;
					$dboverspeed = $overspeed . "juni_" . $year;
					break;
				case "July":
					$dbtable = $report . "juli_" . $year;
					$dboverspeed = $overspeed . "juli_" . $year;
					break;
				case "August":
					$dbtable = $report . "agustus_" . $year;
					$dboverspeed = $overspeed . "agustus_" . $year;
					break;
				case "September":
					$dbtable = $report . "september_" . $year;
					$dboverspeed = $overspeed . "september_" . $year;
					break;
				case "October":
					$dbtable = $report . "oktober_" . $year;
					$dboverspeed = $overspeed . "oktober_" . $year;
					break;
				case "November":
					$dbtable = $report . "november_" . $year;
					$dboverspeed = $overspeed . "november_" . $year;
					break;
				case "December":
					$dbtable = $report . "desember_" . $year;
					$dboverspeed = $overspeed . "desember_" . $year;
					break;
			}
			//return array();
		//print_r($dbtable);exit();
		}


		/* $privilegecode   = $this->sess->user_id_role;
		$user_id         = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_parent     = $this->sess->user_parent; */
		$hauling = $this->getAllStreetKM(4408); //HAULING
		// $start_date = date("Y-m-d H:i:s", strtotime($sdate) - (60 * 60));
		// $end_date = date("Y-m-d H:i:s", strtotime($edate) - (60 * 60));
		$this->dbtrip = $this->load->database("tensor_report", true);
		//$this->dbtrip->select("overspeed_report_id,overspeed_report_vehicle_company,overspeed_report_vehicle_no,overspeed_report_vehicle_device,overspeed_report_vehicle_name,overspeed_report_speed,overspeed_report_location,overspeed_report_gps_time, overspeed_report_coordinate, overspeed_report_jalur, overspeed_report_level, overspeed_report_geofence_limit ");
		$this->dbtrip->where("overspeed_report_gps_time >=", $sdate);
		$this->dbtrip->where("overspeed_report_gps_time <=", $edate);
		$this->dbtrip->where("overspeed_report_speed_status", 1); //valid data
		$this->dbtrip->where("overspeed_report_geofence_type", "road"); //khusus dijalan
		// $this->dbtrip->like("overspeed_report_location", "KM");
		$this->dbtrip->where_in("overspeed_report_location", $hauling); // HAULING
		$this->dbtrip->where("overspeed_report_event_status", 1);
		$this->dbtrip->order_by("overspeed_report_level", "asc");
		$this->dbtrip->order_by("overspeed_report_gps_time", "asc");
		$this->dbtrip->group_by("overspeed_report_gps_time");
		// $this->dbtrip->order_by("overspeed_report_location", "asc");
		// $this->dbtrip->group_by("overspeed_report_location");
		if ($company != "all") {
			$this->dbtrip->where("overspeed_report_vehicle_company", $company);
		}
		if ($vehicle == "all") {
			$this->dbtrip->where("overspeed_report_vehicle_id <>", 72150933); //jika pilih all bukan mobil trial
		} else {
			$this->dbtrip->where("overspeed_report_vehicle_device", $vehicle);
		}
		// $this->dbtrip->limit(200);
		$q = $this->dbtrip->get($dbtable);
		$rows = $q->result();

		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		return $rows;
	}

	function getstatusoverspeedreport(){
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token            = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata         = json_decode(file_get_contents("php://input"));
		$allvehicle       = 0;
		$now              = date("Ymd");
		$payload          = "";
    $forbidden_symbol = array("'", ",", ".", "?", "!", ";", ":", "-");
		$ReportType 		  = "";

		$headers = null;
		if (isset($_SERVER['Authorization']))
		{
        $headers = trim($_SERVER["Authorization"]);
    }
		else if (isset($_SERVER['HTTP_AUTHORIZATION']))
		{ //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    }
    else if (function_exists('apache_request_headers'))
    {
      $requestHeaders = apache_request_headers();
      // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
      $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
      if (isset($requestHeaders['Authorization']))
      {
        $headers = trim($requestHeaders['Authorization']);
      }
    }

		if($headers != $token)
    {
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid Authorization Key ! ";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid User ID";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}else{

			//hanya user yg terdaftar yg bisa akes API
			$this->db->where("api_user",$postdata->UserId);
			$this->db->where("api_token",$headers);
			$this->db->where("api_status",1);
			$this->db->where("api_flag",0);
			$q = $this->db->get("api_user");
			if($q->num_rows == 0)
			{
				$feature["code"]    = 400;
				$feature["msg"]     = "User & Authorization Key is Not Available!";
				$feature["payload"] = $payload;
				echo json_encode($feature);
				exit;
			}
		}

    $payload = array(
     "UserId"            => $postdata->UserId,
     "VehicleDevice" 	 	 => $postdata->VehicleDevice,
		 "StartTime" 		     => $postdata->StartTime,
     "EndTime"           => $postdata->EndTime,
   );

	if ($postdata->VehicleDevice == 'all') {
		$ReportType = "OVERSPEED ALL";
	}else {
		$ReportType = "overspeed";
	}

  if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "")
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid Vehicle No";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}else {
		$this->db->order_by("vehicle_id","desc");
		if ($postdata->VehicleDevice != 'all') {
			$this->db->where("vehicle_device", $postdata->VehicleDevice);
		}
		$this->db->where("vehicle_user_id",4408);
		$this->db->where("vehicle_status",1);
		//$this->db->where("vehicle_active_date2 >",$now); //tidak expired
		$q = $this->db->get("vehicle");
		$vehicle = $q->result_array();

		if($q->num_rows == 0)
		{
			$feature["code"] = 400;
			$feature["msg"] = "Vehicle Not Found!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
	}

	if(!isset($postdata->StartTime) || $postdata->StartTime == "")
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid Start Date Time";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}

	if(!isset($postdata->EndTime) || $postdata->EndTime == "")
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid End Date Time";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}

	if($postdata->StartTime != "" && $postdata->EndTime != ""){
		$startdur = $postdata->StartTime * 60;
		$enddur = $postdata->EndTime * 60;
	}

	$sdate         = date("Y-m-d H:i:s", strtotime($postdata->StartTime));
	$edate         = date("Y-m-d H:i:s", strtotime($postdata->EndTime));

	$d1            = date("d", strtotime($postdata->StartTime));
	$d2            = date("d", strtotime($postdata->EndTime));

	$m1            = date("F", strtotime($postdata->StartTime));
	$m2            = date("F", strtotime($postdata->EndTime));
	$year          = date("Y", strtotime($postdata->StartTime));
	$year2         = date("Y", strtotime($postdata->EndTime));
	$rows          = array();
	$rows2         = array();
	$total_q       = 0;
	$total_q2      = 0;

	if ($d1 != $d2)
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid Date Time. Date time must be in the same date";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}

	if ($m1 != $m2)
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid Date Time. Date time must be in the same month";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}

	if ($year != $year2)
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid Date Time. Date time must be in the same year";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}

	if ($postdata->VehicleDevice == "all") {
		$content = $this->getthisruleovspeedreport($ReportType, $postdata->VehicleDevice, $postdata->StartTime, $postdata->EndTime);
	}else {
		$content = $this->getthisruleovspeedreport($ReportType, $postdata->VehicleDevice, $postdata->StartTime, $postdata->EndTime);
	}

	// echo "<pre>";
	// var_dump($content);die();
	// echo "<pre>";

		if (sizeof($content) > 0) {
			echo json_encode(array("code" => 200, "msg" => "ok","data" => "DONE", "payload" => $payload), JSON_NUMERIC_CHECK);
		}else {
			echo json_encode(array("code" => 200, "msg" => "ok","data" => "ON PROCESS", "payload" => $payload), JSON_NUMERIC_CHECK);
		}

		$nowendtime = date("Y-m-d H:i:s");
		$this->insertHitAPI("API Rule Overspeed Report",$payload,$nowstarttime,$nowendtime);
		$this->db->close();
		$this->db->cache_delete_all();
	}

	function getstatuslocationreport(){
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token            = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata         = json_decode(file_get_contents("php://input"));
		$allvehicle       = 0;
		$now              = date("Ymd");
		$payload          = "";
    $forbidden_symbol = array("'", ",", ".", "?", "!", ";", ":", "-");

		$headers = null;
		if (isset($_SERVER['Authorization']))
		{
        $headers = trim($_SERVER["Authorization"]);
    }
		else if (isset($_SERVER['HTTP_AUTHORIZATION']))
		{ //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    }
    else if (function_exists('apache_request_headers'))
    {
      $requestHeaders = apache_request_headers();
      // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
      $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
      if (isset($requestHeaders['Authorization']))
      {
        $headers = trim($requestHeaders['Authorization']);
      }
    }

		if($headers != $token)
    {
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid Authorization Key ! ";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["code"]    = 400;
			$feature["msg"]     = "Invalid User ID";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}else{

			//hanya user yg terdaftar yg bisa akes API
			$this->db->where("api_user",$postdata->UserId);
			$this->db->where("api_token",$headers);
			$this->db->where("api_status",1);
			$this->db->where("api_flag",0);
			$q = $this->db->get("api_user");
			if($q->num_rows == 0)
			{
				$feature["code"]    = 400;
				$feature["msg"]     = "User & Authorization Key is Not Available!";
				$feature["payload"] = $payload;
				echo json_encode($feature);
				exit;
			}
		}

    $payload = array(
     "UserId"            => $postdata->UserId,
     "VehicleDevice" 	 	 => $postdata->VehicleDevice,
		 "StartTime" 		     => $postdata->StartTime,
     "EndTime"           => $postdata->EndTime,
   );

	  if ($postdata->VehicleDevice == 'all') {
			$ReportTypeArray = array("LOCATION ALL", "LOCATION IDLE ALL", "LOCATION OFF ALL");
		}else {
			$ReportTypeArray = array("location", "location_off", "location_idle");
		}

  if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "")
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid Vehicle No";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}else {
		$this->db->order_by("vehicle_id","desc");
		if ($postdata->VehicleDevice != 'all') {
			$this->db->where("vehicle_device", $postdata->VehicleDevice);
		}
		$this->db->where("vehicle_user_id",4408);
		$this->db->where("vehicle_status",1);
		//$this->db->where("vehicle_active_date2 >",$now); //tidak expired
		$q = $this->db->get("vehicle");
		$vehicle = $q->result_array();

		if($q->num_rows == 0)
		{
			$feature["code"] = 400;
			$feature["msg"] = "Vehicle Not Found!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
	}

	if(!isset($postdata->StartTime) || $postdata->StartTime == "")
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid Start Date Time";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}

	if(!isset($postdata->EndTime) || $postdata->EndTime == "")
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid End Date Time";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}

	if($postdata->StartTime != "" && $postdata->EndTime != ""){
		$startdur = $postdata->StartTime * 60;
		$enddur = $postdata->EndTime * 60;
	}

	$sdate         = date("Y-m-d H:i:s", strtotime($postdata->StartTime));
	$edate         = date("Y-m-d H:i:s", strtotime($postdata->EndTime));

	$d1            = date("d", strtotime($postdata->StartTime));
	$d2            = date("d", strtotime($postdata->EndTime));

	$m1            = date("F", strtotime($postdata->StartTime));
	$m2            = date("F", strtotime($postdata->EndTime));
	$year          = date("Y", strtotime($postdata->StartTime));
	$year2         = date("Y", strtotime($postdata->EndTime));
	$rows          = array();
	$rows2         = array();
	$total_q       = 0;
	$total_q2      = 0;

	if ($d1 != $d2)
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid Date Time. Date time must be in the same date";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}

	if ($m1 != $m2)
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid Date Time. Date time must be in the same month";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}

	if ($year != $year2)
	{
		$feature["code"]    = 400;
		$feature["msg"]     = "Invalid Date Time. Date time must be in the same year";
		$feature["payload"] = $payload;
		echo json_encode($feature);
		exit;
	}

	if ($postdata->VehicleDevice == "all") {
		$content = $this->getthisrulelocationreport($ReportTypeArray, $postdata->VehicleDevice, $postdata->StartTime, $postdata->EndTime);
	}else {
		$content = $this->getthisrulelocationreport($ReportTypeArray, $postdata->VehicleDevice, $postdata->StartTime, $postdata->EndTime);
	}

	// echo "<pre>";
	// var_dump($content);die();
	// echo "<pre>";

		if (sizeof($content) > 2) {
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => "DONE", "payload" => $payload), JSON_NUMERIC_CHECK);
		}else {
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => "ON PROCESS", "payload" => $payload), JSON_NUMERIC_CHECK);
		}

		$nowendtime = date("Y-m-d H:i:s");
		$this->insertHitAPI("API Rule Location Report",$payload,$nowstarttime,$nowendtime);
		$this->db->close();
		$this->db->cache_delete_all();
	}

	function getthisruleovspeedreport($reportype, $vehicleid, $starttime, $endtime){
		// echo "<pre>";
		// var_dump($reportype.'-'. $vehicleid.'-'. $starttime.'-'. $endtime);die();
		// echo "<pre>";

		$this->dbtrip = $this->load->database("tensor_report",true);
		$this->dbtrip->order_by("autoreport_data_startdate","asc");

		if($vehicleid != "all"){
			$this->dbtrip->where("autoreport_vehicle_device", $vehicleid);
		}

		$this->dbtrip->where("autoreport_type", $reportype);
		$this->dbtrip->where("autoreport_data_startdate >=", $starttime);
		$this->dbtrip->where("autoreport_data_enddate <=", $endtime);
		$q = $this->dbtrip->get("autoreport_new")->result_array();
		return $q;
	}

	function getthisrulelocationreport($reportype, $vehicleid, $starttime, $endtime){
		$this->dbtrip = $this->load->database("tensor_report",true);
		$this->dbtrip->order_by("autoreport_data_startdate","asc");

		$this->dbtrip->select("autoreport_type");
		if($vehicleid != "all"){
			$this->dbtrip->where("autoreport_vehicle_device", $vehicleid);
		}

		$this->dbtrip->where_in("autoreport_type", $reportype);
		$this->dbtrip->where("autoreport_data_startdate >=", $starttime);
		$this->dbtrip->where("autoreport_data_enddate <=", $endtime);
		$q = $this->dbtrip->get("autoreport_new")->result_array();
		return $q;
	}

  function getAllStreetKM($userid)
	{
		$feature = array();
		$street_type_list = array("1", "5", "8", "7", "4", "3"); //HAULING + ROM ROAD + PORT + CP + ANTRIAN BLC , ROM = 3
		$this->dbmaster = $this->load->database("default", true);
		$this->dbmaster->select("street_name,street_alias,street_type");
		$this->dbmaster->order_by("street_name", "asc");
		$this->dbmaster->group_by("street_name");
		$this->dbmaster->where("street_creator", $userid);
		$this->dbmaster->where_in("street_type", $street_type_list);
		$this->dbmaster->where("street_name !=", "PORT BBC,"); //selain port bbc

		$this->dbmaster->from("street");
		$q = $this->dbmaster->get();
		$rows = $q->result();
		$total = count($rows);
		for ($x = 0; $x < $total; $x++) {
			$street_name = str_replace(",", "", $rows[$x]->street_name);
			$feature[$x] = $street_name;
		}

		//print_r($feature);exit();
		$result = $feature;

		return $result;
	}

  function insertHitAPI($apiname, $payload, $starttime, $endtime)
	{
		$latency                         = strtotime($endtime) - strtotime($starttime);
		$ipaddress                       = $_SERVER['REMOTE_ADDR'];

		$this->dbts                      = $this->load->database("webtracking_ts",true);
		$data_insert["hit_api"]          = $apiname;
		$data_insert["hit_user"]         = $payload['UserId'];
		$data_insert["hit_datetime_wib"] = $starttime;
		$data_insert["hit_latency"]      = $latency;
		$data_insert["hit_ip_address"]   = $ipaddress;
		$data_insert["hit_payload"]      = json_encode($payload);

		$this->dbts->insert("ts_api_hit",$data_insert);
	}

	function gettokenugems(){
		$url = "https://api.ugems.id/gpsdatapush/generate_token";
		$data = array("username" => "admin", "password" => "admin");

				$content = json_encode($data);

				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_HTTPHEADER,
				        array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

				$json_response = curl_exec($curl);

				$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

				// if ( $status != 201 ) {
				//     die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
				// }
				// curl_close($curl);
				// echo json_encode($json_response, JSON_NUMERIC_CHECK);
				echo $json_response;

			// echo "<pre>";
			// var_dump($json_response);die();
			// echo "<pre>";
	}




































}
