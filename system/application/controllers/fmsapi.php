<?php
include "base.php";

class Fmsapi extends Base {

	function Fmsapi()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("dashboardmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
		$this->load->model('m_fmsapi');
	}

	function pushalert()
	{
		ini_set('allow_url_fopen', 'On');
		header("Content-Type: application/json");
		
		
		//$token      = "BCW5kNGhN5QJOnA99fbv778JKPmo2k0dA16";
		$postdata   = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$now        = date("Ymd");

		$headers = null;
		if (isset($_SERVER['Authorization'])) {
		  $headers = trim($_SERVER["Authorization"]);
		}else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
		  $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		}else if (function_exists('apache_request_headers')) {
		  $requestHeaders = apache_request_headers();
		  // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
		  $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
		  if (isset($requestHeaders['Authorization']))
		  {
			$headers = trim($requestHeaders['Authorization']);
		  }
		}

		$feature = array();
		$payload = "";
		$provider_id = "";
		$provider_name = "";
		$provider_token = "";
	
			//hanya user yg terdaftar yg bisa akes API
			$this->db->where("api_user",$postdata->ProviderID);
			$this->db->where("api_token",$headers);
			$this->db->where("api_status",1);
			$this->db->where("api_flag",0);
			$q = $this->db->get("api_user");
			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"]    = "INVALID USER & TOKEN";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$row_apiuser = $q->row(); 
				$provider_id = $row_apiuser->api_user;
				$provider_name = $row_apiuser->api_note;
				$provider_token = $row_apiuser->api_token;
				
				//condition checking
				if($headers != $provider_token){
					$feature["code"] = 400;
					$feature["msg"]    = "INVALID TOKEN";
					$feature["payload"]    = $payload;
					echo json_encode($feature);
					exit;
				}
			}
			
			
		//check kondisi mandatory
		if(!isset($postdata->ProviderID) || $postdata->ProviderID == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA PROVIDER ID";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->ContractorID) || $postdata->ContractorID == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA CONTRACTOR ID";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA VEHICLE NO";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->VehicleType) || $postdata->VehicleType == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA VEHICLE TYPE";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->DriverName) || $postdata->DriverName == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA DRIVER NAME";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->DriverSID) || $postdata->DriverSID == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA DRIVER SID";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		
		if(!isset($postdata->DeviceImei) || $postdata->DeviceImei == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA DEVICE IMEI";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->AlertDate) || $postdata->AlertDate == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA ALERT DATE";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->AlertTime) || $postdata->AlertTime == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA ALERT TIME";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->AlertName) || $postdata->AlertName == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA ALERT NAME";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->Shift) || $postdata->Shift == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA SHIFT";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->Longitude) || $postdata->Longitude == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA LONGITUDE";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->Latitude) || $postdata->Latitude == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA LATITUDE";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->Direction) || $postdata->Direction == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA DIRECTION";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->SpeedGps) || $postdata->SpeedGps == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA SPEED GPS";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->Location) || $postdata->Location == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA LOCATION";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		/* if(!isset($postdata->Jalur) || $postdata->Jalur == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA JALUR";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		} */

		if(!isset($postdata->ImageLink) || $postdata->ImageLink == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA IMAGE LINK";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->VideoLink) || $postdata->VideoLink == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA VIDEO LINK";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		//end check kondisi mandatory


	if ($postdata->AlertDate != "" && $postdata->AlertTime != ""){
		$alert_date_time = date("Y-m-d H:i:s", strtotime($postdata->AlertDate." ".$postdata->AlertTime));
	}
	
	if($postdata->Longitude != "" && $postdata->Latitude){
		$coord = $postdata->Latitude.",".$postdata->Longitude;
	}
	
	if($postdata->ContractorID != "" && $postdata->ContractorID){
		
		$companyid = $this->get_company_bySID($postdata->ContractorID);
	}
	
	$userid = 4408;
	$mediatype = 0; //pic only
	
	//belum dimapping
	$AlertCategory = 1;
    $AlertType = 618;
	
    $data       = array(
	 "alarm_report_vehicle_user_id"           => $userid,
	 "alarm_report_media"         	  		  => $mediatype,
	 "alarm_report_vehicle_company"           => $companyid,
	 "alarm_report_insert_time"         	  => date("Y-m-d H:i:s"),
	 "alarm_report_insert_type"         	  => "pushalert",
	 "alarm_report_fileurl"         	  	  => $postdata->ImageLink,
	 "alarm_report_gpsstatus"         	  	  => "pushalert",
	 "alarm_report_vehicle_id"		  		  => $postdata->DeviceImei,
	 "alarm_report_provider_id"         	  => $provider_id,
	 "alarm_report_provider_name"		      => $provider_name,
      "alarm_report_vehicle_no"         	  => $postdata->VehicleNo,
      "alarm_report_vehicle_name"    		  => $postdata->VehicleType,
      "alarm_report_imei"		  			  => $postdata->DeviceImei,
      "alarm_report_driver_name"	    	  => $postdata->DriverName,
      "alarm_report_sid_driver"		    	  => $postdata->DriverSID,
      "alarm_report_start_time"        		  => $alert_date_time,
	  "alarm_report_end_time"        		  => $alert_date_time,
	  "alarm_report_category"                 => $AlertCategory,
      "alarm_report_type"      			      => $AlertType,
	  "alarm_report_name"     			      => $postdata->AlertName,
      "alarm_report_shift"                    => $postdata->Shift,
      /* "alarm_report_week"                     => $postdata->Week,
      "alarm_report_month"                    => $postdata->Month, */
      "alarm_report_coordinate_start"         => $coord,
	  "alarm_report_coordinate_end"           => $coord,
      "alarm_report_direction"                => $postdata->Direction,
      "alarm_report_location_start"           => $postdata->Location,
	  "alarm_report_location_end"             => $postdata->Location,
	  "alarm_report_jalur" 		              => $postdata->Jalur,
      "alarm_report_speed"  	      	  	  => $postdata->SpeedGps,
      "alarm_report_speed_limit" 		      => $postdata->SpeedLimit,
 	  "alarm_report_image_link"			   	  => $postdata->ImageLink,
	  "alarm_report_video_link"			   	  => $postdata->VideoLink

    );

    $payload       = array(
	  "ProviderID"         			  => $postdata->ProviderID,
	  "ContractorID"         		  => $postdata->ContractorID,
      "VehicleNo"         			  => $postdata->VehicleNo,
      "VehicleType"    		 		  => $postdata->VehicleType,
      "DeviceImei"		  			  => $postdata->DeviceImei,
      "DriverName"	    	 		  => $postdata->DriverName,
      "DriverSID"		    	      => $postdata->DriverSID,
	  "AlertDate"     			      => $postdata->AlertDate,
	  "AlertTime"     			      => $postdata->AlertTime,
	  "AlertName"     			      => $postdata->AlertName
      
 	  
    );

		$m1     = date("F", strtotime($alert_date_time));
		$year   = date("Y", strtotime($alert_date_time));
		$report = "alarm_evidence_";

		switch ($m1)
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

		//hardcode ke 1 table
		//print_r($dbtable);exit();

		$insert = $this->m_fmsapi->insertData($dbtable, $data);
          if ($insert) {
            echo json_encode(array("code" => 200, "msg" => "ok", "payload" => $payload));
          }else {
            echo json_encode(array("code" => 400, "msg" => "Failed Insert Data", "payload" => $payload));
          }

	}

	function testpush()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "BCW5kNGhN5QJOnA99fbv778JKPmo2k0dA16";
		$authorization = "Authorization:".$token;
		//$url = "https://fmspoc.abditrack.com/fmsapi/pushalert";
		$url = "http://fms.abditrack.com/fmsapi/pushalert";
		$feature = array();

		$feature["ProviderId"] = 4410;
		$feature["VehicleNo"] = "BM-4410-TES";
		$feature["VehicleType"] = "DT";
		$feature["DeviceImei"] = 653220230058;
		
		$feature["DriverName"] = "AGUSTINUS RURU PARINDING";
		$feature["DriverSID"] = "QJBFW";
		$feature["AlertDate"] = "21-09-2023";
		$feature["AlertTime"] = "23:03:03";
		
		$feature["AlertCategory"] = 1;
		$feature["AlertType"] = 618;
		$feature["AlertName"] = "Fatigue Driving Alarm Level One";
		$feature["Shift"] = 2;
		
		$feature["Week"] = 38; 
		$feature["Month"] = "September";
		$feature["Latitude"] = "1.997376";
		$feature["Longitude"] = "117.368137";
		
		$feature["Direction"] = 105; 
		$feature["Location"] = "LMO KM 5";
		$feature["Jalur"] = "kosongan";
		$feature["SpeedGps"] = 20;
		
		$feature["SpeedLimit"] = 40;
		$feature["ImageLink"] = "http://182.253.236.246:6611/3/5?DownType=3&DevIDNO=653220230058&FLENGTH=85884&FOFFSET=889574924&MTYPE=1&FPATH=E%3A%2FgStorage%2FJPEG_FILE%2F2023-09-01%2F20230901-000000_DS1.picfile&jsession=6216d2c9709d4c2c91038b6d04d60522";

		//printf("POSTING PROSES \r\n");
		$content = json_encode($feature);
		$total_content = count($content);

		printf("Data JSON : %s \r \n",$content);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		$json_response = curl_exec($curl);
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		printf("-------------------------- \r\n");

		exit;
	}

	function get_company_bySID($sid)
	{
		
		$this->db->select("company_id");
		$this->db->where("company_sid", $sid);
		$this->db->where("company_flag", 0);
		$q = $this->db->get("company");
		if($q->num_rows == 0)
		{
			$result = 0;
		}else{
			$row = $q->row(); 
			$companyid = $row->company_id;
			$result = $companyid;
			
		}
		return $result;
	}

	//start getalert for tableau
	function get_companyname_byID($id)
	{
		
		$this->db->select("company_name");
		$this->db->where("company_id", $id);
		$this->db->where("company_flag", 0);
		$q = $this->db->get("company");
		if($q->num_rows == 0)
		{
			$result = 0;
		}else{
			$row = $q->row(); 
			$companyname = $row->company_name;
			$result = $companyname;
			
		}
		return $result;
	}
	
	function get_alarm_alias($name)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("alarm_alias");
		$this->dbts->where("alarm_name", $name);
		$this->dbts->order_by("alarm_id", "desc");
		$q = $this->dbts->get("ts_alarm");
		if($q->num_rows == 0)
		{
			$result = "";
		}else{
			$row = $q->row(); 
			$alername = $row->alarm_alias;
			$result = $alername;
			
		}
		return $result;
	}
	
	function get_shift_byTime($datetime)
	{
		$thishour = date("H",strtotime($datetime));
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("hour_shift");
		$this->dbts->where("hour_name", $thishour);
		$this->dbts->order_by("hour_flag", 0);
		$q = $this->dbts->get("ts_hour_shift");
		$row = $q->row();
		
		if(count($row)>0)
		{
			$result = $row->hour_shift;
		}else{
			
			$result = 0;
		}
		return $result;
	}
	
	//LOOP BACK HASIL PUSH ALERT
	function getdataalert()
	{
		ini_set('allow_url_fopen', 'On');
		//ini_set('display_errors', 1);
		ini_set('memory_limit', "1G");
		ini_set('max_execution_time', 120); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");
		
		$token = "BCW5kNGhN5BRCnA88fbv338JKLmo2k0dA22";
		
		
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$allmedia = 0;
		$now = date("Ymd");
		$payload = "";

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

		if($headers != $token)
        {
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Authorization Key ! ";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid User ID";
			$feature["payload"]    = $payload;
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
				$feature["code"] = 400;
				$feature["msg"] = "User & Authorization Key is Not Available!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}

		}

		if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "all")
		{
			$allvehicle = 1;
		}
		
		if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Vehicle No!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			
			$check_vehicle = strpos($postdata->VehicleNo,';');
			$ex_vehicle = explode(";",$postdata->VehicleNo);
			$rootfms = 4408;

			$this->db->order_by("vehicle_id","desc");
			$this->db->where("vehicle_no",$postdata->VehicleNo);
			$this->db->where("vehicle_user_id",$rootfms);
			$this->db->where("vehicle_status",1);
			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "Vehicle Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();
				 $payload      		= array(
				  "UserId"          => $postdata->UserId,
				  "VehicleNo"   	=> $postdata->VehicleNo,
				  "StartTime" 	 	=> $postdata->StartTime,
				  "EndTime"   		=> $postdata->EndTime

				);

			}
		}



		if($postdata->StartTime == "" || $postdata->EndTime == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "No Data Periode Start or Periode End";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		else
		{
			$sdate = $postdata->StartTime;
			$edate = $postdata->EndTime;
			

			$dboverspeed = "";
			$dbtable = "";
			$report     = "alarm_evidence_";
			$overspeed  = "overspeed_";

			$month = date("F", strtotime($sdate));
			$year = date("Y", strtotime($sdate));


			$diff = strtotime($edate) - strtotime($sdate);
			if ($diff < 0) {
				$feature["code"] = 400;
				$feature["msg"] = "Date is not correct!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}

			$diff1 = date("m", strtotime($sdate));
			$diff2 = date("m", strtotime($edate));

			if ($diff1 != $diff2) {
				$feature["code"] = 400;
				$feature["msg"] = "Date must be in the same month!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}

			$diff1 = date("Y", strtotime($sdate));
			$diff2 = date("Y", strtotime($edate));

			if ($diff1 != $diff2) {

				$feature["code"] = 400;
				$feature["msg"] = "Date must be in the same year!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}

			

				$diff1 = date("d", strtotime($sdate));
				$diff2 = date("d", strtotime($edate));

				if($diff1 != $diff2)
				{
					$feature["code"] = 400;
					$feature["msg"] = "DateTime must be in the same Date!";
					$feature["payload"]    = $payload;
					echo json_encode($feature);
					exit;
				}

			

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

		}

		//jika mobil lebih dari nol
		if(count($vehicle) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($vehicle);$z++)
			{

				//printf("ATTR %s \r\n",$vehicle[$z]->vehicle_no);

				$vehicle_device = $vehicle[$z]->vehicle_device;
				$ex_vehicle = explode("@",$vehicle_device);
				$vdeviceid = $ex_vehicle[0];
				$company = $vehicle[$z]->vehicle_company;
					
					//source only by alert data foto
					$rows = $this->getalarmevidence_data($dbtable, $company, $vdeviceid, $sdate, $edate, $allvehicle, 0);
					//print_r($rows);exit();
					if(isset($rows) && count($rows)>0)
					{

						for($i=0;$i<count($rows);$i++)
						{
								$shift_value = $this->get_shift_byTime($rows[$i]->alarm_report_start_time);
								$company_name = $this->get_companyname_byID($rows[$i]->alarm_report_vehicle_company);
								$mitra_name = $company_name;
								
								$data_coordinate = explode(",",$rows[$i]->alarm_report_coordinate_start);
								$lat = $data_coordinate[0];
								$long = $data_coordinate[1];
								
								if($rows[$i]->alarm_report_statusintervention_cr != 0)
								{
									$alarm_alias = $this->get_alarm_alias($rows[$i]->alarm_report_fatiguecategory_cr);
									$driver_code = $rows[$i]->alarm_report_sid_cr;
									$driver_name = $rows[$i]->alarm_report_name_cr;
									
									$data_controlroom = explode("|",$rows[$i]->alarm_report_supervisor_cr);
									
									$intervensi_time = date('Y-m-d H:i:s', strtotime($rows[$i]->alarm_report_datetime_cr . '+1 hours'));
									$intervensi_by = $data_controlroom[1];
									$intervensi_notes = $rows[$i]->alarm_report_note_cr;
									$intervensi_gap = "";
									$intervensi_status = "Sudah Diintervensi";
									
									if($rows[$i]->alarm_report_truefalse_up == 1)
									{
										$alert_status = "True";
									}
									else if($rows[$i]->alarm_report_truefalse_up == 2)
									{
										$alert_status = "False";
									}
									else
									{
										$alert_status = "Belum Tervalidasi";
									}
									
								}
								else
								{
									$alarm_alias = $this->get_alarm_alias($rows[$i]->alarm_report_name);
									$driver_code = "";
									$driver_name = "";
									
									$intervensi_time = "";
									$intervensi_by = "";
									$intervensi_notes = "";
									$intervensi_gap = "";
									$intervensi_status = "Belum Diintervensi";
									$alert_status = "Belum Tervalidasi";
								}
								
								$data_gps = explode("|",$rows[$i]->alarm_report_gpsstatus);
								$speed_value = $data_gps[4]/10;
								
								$DataToUpload[$i]->ID = $rows[$i]->alarm_report_id;
								$DataToUpload[$i]->Plate_Number = $rows[$i]->alarm_report_vehicle_no;
								$DataToUpload[$i]->Device_ID = $rows[$i]->alarm_report_imei;
								//$DataToUpload[$i]->Time = date("d-m-Y H:i:s", strtotime($rows[$i]->alarm_report_start_time));
								$DataToUpload[$i]->Time = $rows[$i]->alarm_report_start_time;
								$DataToUpload[$i]->Shift = $shift_value;
								$DataToUpload[$i]->Mitra = $mitra_name;
								$DataToUpload[$i]->Warning_type = $alarm_alias;
								//$DataToUpload[$i]->Warning_alias = $rows[$i]->alarm_report_name;
								$DataToUpload[$i]->Speed = $speed_value;
								$DataToUpload[$i]->Nama_Area = $rows[$i]->alarm_report_location_start;
								$DataToUpload[$i]->Kode_Driver = $driver_code;
								$DataToUpload[$i]->Nama_Driver = $driver_name;
								$DataToUpload[$i]->Dir = 0;
								$DataToUpload[$i]->Lat = $lat;
								$DataToUpload[$i]->Lng = $long;	
								$DataToUpload[$i]->Acc = 1;		
								$DataToUpload[$i]->Altitude = 0;	
								$DataToUpload[$i]->Media = $rows[$i]->alarm_report_fileurl;
								//$DataToUpload[$i]->ID_Warning_Type = 0;
								//$DataToUpload[$i]->Flag_Alarm = 0;
								//$DataToUpload[$i]->DN = "";
								$DataToUpload[$i]->Jam_Intervensi = $intervensi_time;
								$DataToUpload[$i]->Intervensi_By = $intervensi_by;
								$DataToUpload[$i]->Intervensi_Notes = $intervensi_notes;
								//$DataToUpload[$i]->Gap_Intervensi = $intervensi_gap;
								$DataToUpload[$i]->Intervensi_Status = $intervensi_status;
								$DataToUpload[$i]->Status_Alert = $alert_status;
						}

					}

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Data Alert",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}

	function getalarmevidence_data($dbtable, $company, $vehicledevice, $sdate, $edate, $allvehicle, $typemedia)
	{	
		$sdate = date("Y-m-d H:i:s", strtotime($sdate));
		$edate = date("Y-m-d H:i:s", strtotime($edate));

		$nowday            = date("d");
		$end_day_fromEdate = date("d", strtotime($edate));
		
		//saatbelt, lane deviation
		$black_list  = array("401","451","478","608","609","652","653","658","659","602","603");
		
		

		$street_register = $this->config->item('street_register');

		$this->dbtrip = $this->load->database("tensor_report", true);
		//$this->dbtrip->where("alarm_report_id",809268); //test hardcode
		$this->dbtrip->where("alarm_report_vehicle_id", $vehicledevice);
		$this->dbtrip->where("alarm_report_media",$typemedia); //only foto for BC
		$this->dbtrip->where("alarm_report_start_time >=", $sdate);
		$this->dbtrip->where("alarm_report_start_time <=", $edate);
		$this->dbtrip->where_not_in('alarm_report_type', $black_list);
		$this->dbtrip->where("alarm_report_gpsstatus !=","pushalert");
		$this->dbtrip->order_by("alarm_report_start_time","asc");
		$q = $this->dbtrip->get($dbtable);
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		$rows = $q->result();

		return $rows;
	}
	
	function insertHitAPI($apiname,$payload,$starttime,$endtime)
	{
		$latency = strtotime($endtime) - strtotime($starttime);
		$ipaddress = $_SERVER['REMOTE_ADDR'];

		$this->dbts = $this->load->database("webtracking_ts",true);
		$data_insert["hit_api"] = $apiname;
		$data_insert["hit_user"] = $payload['UserId'];
		$data_insert["hit_datetime_wib"] = $starttime;
		$data_insert["hit_latency"] = $latency;
		$data_insert["hit_ip_address"] = $ipaddress;
		$data_insert["hit_payload"] = json_encode($payload);

		$this->dbts->insert("ts_api_hit",$data_insert);
		//printf("INSERT OK \r\n");
	}

	function getvehicle()
	{
		//ini_set('display_errors', 1);
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "BCW5kNGhN5BRCnA88fbv338JKLmo2k0dA22";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$allcompany = 0;
		$payload = "";
		$now = date("Ymd");

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

		if($headers != $token)
        {
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Authorization Key ! ";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid User ID";
			$feature["payload"]    = $payload;
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
				$feature["code"] = 400;
				$feature["msg"] = "User & Authorization Key is Not Available!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}

		}

		if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "all")
		{
			$allvehicle = 1;
		}

		if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Vehicle No!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			$check_vehicle = strpos($postdata->VehicleNo,';');
			$ex_vehicle = explode(";",$postdata->VehicleNo);
			$UserIDBIB = 4408;

			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			}
			
			$this->db->where("vehicle_user_id",$UserIDBIB);
			//$this->db->where("vehicle_status",1);
			$this->db->join("company", "vehicle_company = company_id", "left");
			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "Vehicle Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

				$payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "VehicleNo"   	=> $postdata->VehicleNo
				
				);

			}
		}

		//print_r($vehicle);exit();

		//jika mobil lebih dari nol
		if(count($vehicle) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($i=0;$i<count($vehicle);$i++)
			{
				$ex_vehicle = explode("@",$vehicle[$i]->vehicle_device);
				$imei_gps = $ex_vehicle[0];

								$DataToUpload[$i]->VehicleId = $vehicle[$i]->vehicle_id;
								//$DataToUpload[$i]->VehicleUserID = $vehicle[$i]->vehicle_user_id;
								$DataToUpload[$i]->VehicleGPSImei = $imei_gps;
								$DataToUpload[$i]->VehicleMDVRImei = $vehicle[$i]->vehicle_mv03;
								$DataToUpload[$i]->VehicleNo = $vehicle[$i]->vehicle_no;
								//$DataToUpload[$i]->VehicleNoBackup = $vehicle[$i]->vehicle_no_bk;
								$DataToUpload[$i]->VehicleName = $vehicle[$i]->vehicle_name;

								//$DataToUpload[$i]->VehicleCardNo = $vehicle[$i]->vehicle_card_no;
								//$DataToUpload[$i]->VehicleOperator = $vehicle[$i]->vehicle_operator;
								$DataToUpload[$i]->VehicleStatus = $vehicle[$i]->vehicle_status;

								//$DataToUpload[$i]->VehicleImage = $vehicle[$i]->vehicle_image;
								//$DataToUpload[$i]->VehicleCreatedDate = $vehicle[$i]->vehicle_created_date;
								//$DataToUpload[$i]->VehicleType = $vehicle[$i]->vehicle_type;

								$DataToUpload[$i]->VehicleCompanyIDFMS = $vehicle[$i]->vehicle_company;
								$DataToUpload[$i]->VehicleCompanyName = $vehicle[$i]->company_name;
								$DataToUpload[$i]->VehicleCompanySID = $vehicle[$i]->company_sid;
								//$DataToUpload[$i]->VehicleSubCompany = $vehicle[$i]->vehicle_subcompany;
								//$DataToUpload[$i]->VehicleGroup = $vehicle[$i]->vehicle_group;
								//$DataToUpload[$i]->VehicleSubGroup = $vehicle[$i]->vehicle_subgroup;

								$DataToUpload[$i]->VehicleTanggalPasang = $vehicle[$i]->vehicle_tanggal_pasang;
								//$datajson["Data"] = $DataToUpload;

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Master Vehicle",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}











}
