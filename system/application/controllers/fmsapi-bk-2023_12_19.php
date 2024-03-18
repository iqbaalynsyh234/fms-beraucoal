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












}
