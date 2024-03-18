<?php
include "base.php";

class Obuapi extends Base {

	function Obuapi()
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

	function getPosition($longitude, $ew, $latitude, $ns)
	{
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

	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_user)
	{

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

	function getGeofence_location_other($longitude, $latitude, $vehicle_user)
	{

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

	function req_overspeed()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://temansharing.borneo-indobara.com/ugapi/getoverspeed";
		$feature = array();

		$feature["UserId"] = 4204; //pbi
		//$feature["VehicleNo"] = "all";
		$feature["VehicleNo"] = "BMT 3148";
		$feature["StartTime"] = "2022-08-31 00:00:00";
		$feature["EndTime"] = "2022-08-31 23:59:59";

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
	
	//old source 
	function getoverspeed_old()
	{
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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
			$UserIDBIB = 4408;

			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			/* if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			} */
			$this->db->where("vehicle_no",$postdata->VehicleNo);
			$this->db->where("vehicle_user_id",$UserIDBIB);
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
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
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
			$report     = "alarm_evidence_";
			$overspeed  = "overspeed_hour_";

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

			if($allvehicle == 1){

				$diff1 = date("d", strtotime($sdate));
				$diff2 = date("d", strtotime($edate));

				if($diff1 != $diff2)
				{
					$feature["code"] = 400;
					$feature["msg"] = "All Vehicle must be in the same Date!";
					$feature["payload"]    = $payload;
					echo json_encode($feature);
					exit;
				}

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
				$company = $vehicle[$z]->vehicle_company;

					$rows = $this->getoverspeed_data($dboverspeed, $company, $vehicle_device, $sdate, $edate);
					//print_r($rows);exit();
					if(isset($rows) && count($rows)>0)
					{

						for($i=0;$i<count($rows);$i++)
						{

								$DataToUpload[$i]->VehicleUserId = $rows[$i]->overspeed_report_vehicle_user_id;
								$DataToUpload[$i]->VehicleId = $rows[$i]->overspeed_report_vehicle_id;
								$DataToUpload[$i]->VehicleDevice = $rows[$i]->overspeed_report_vehicle_device;
								$DataToUpload[$i]->VehicleNo = $rows[$i]->overspeed_report_vehicle_no;
								$DataToUpload[$i]->VehicleName = $rows[$i]->overspeed_report_vehicle_name;
								$DataToUpload[$i]->VehicleType = $rows[$i]->overspeed_report_vehicle_type;
								$DataToUpload[$i]->VehicleCompany = $rows[$i]->overspeed_report_vehicle_company;
								$DataToUpload[$i]->VehicleMV03Imei = $rows[$i]->overspeed_report_imei;

								$DataToUpload[$i]->ReportType = $rows[$i]->overspeed_report_type;
								$DataToUpload[$i]->ReportName = $rows[$i]->overspeed_report_name;

								$DataToUpload[$i]->GPSSpeed = $rows[$i]->overspeed_report_speed;
								$DataToUpload[$i]->GPSTime = $rows[$i]->overspeed_report_gps_time;
								$DataToUpload[$i]->GPSStatus = $rows[$i]->overspeed_report_gpsstatus;

								$DataToUpload[$i]->GeofenceName = $rows[$i]->overspeed_report_geofence_name;
								$DataToUpload[$i]->GeofenceLimit = $rows[$i]->overspeed_report_geofence_limit;
								$DataToUpload[$i]->GeofenceType = $rows[$i]->overspeed_report_geofence_type;

								$DataToUpload[$i]->OverspeedJalur = $rows[$i]->overspeed_report_jalur;
								$DataToUpload[$i]->OverspeedLevel = $rows[$i]->overspeed_report_level;
								$DataToUpload[$i]->OverspeedLevelAlias = $rows[$i]->overspeed_report_level_alias;

								$DataToUpload[$i]->Location = $rows[$i]->overspeed_report_location;
								$DataToUpload[$i]->Coordinate = $rows[$i]->overspeed_report_coordinate;
								//$DataToUpload[$i]->SpeedStatus = $rows[$i]->overspeed_report_speed_status;

								//$datajson["Data"] = $DataToUpload;



						}

					}

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Overspeed",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}
	
	//new source overspeed report
	function getoverspeed()
	{
		ini_set('memory_limit', "1G");
		ini_set('max_execution_time', 120); // 2 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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
			$UserIDBIB = 4408;

			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			/* if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			} */
			$this->db->where("vehicle_no",$postdata->VehicleNo);
			$this->db->where("vehicle_user_id",$UserIDBIB);
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
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
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
			$report     = "alarm_evidence_";
			$overspeed  = "overspeed_hour_";

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

			if($allvehicle == 1){

				$diff1 = date("d", strtotime($sdate));
				$diff2 = date("d", strtotime($edate));

				if($diff1 != $diff2)
				{
					$feature["code"] = 400;
					$feature["msg"] = "All Vehicle must be in the same Date!";
					$feature["payload"]    = $payload;
					echo json_encode($feature);
					exit;
				}

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
				$company = $vehicle[$z]->vehicle_company;

					$rows = $this->getoverspeed_data_hour($dboverspeed, $company, $vehicle_device, $sdate, $edate);
					//print_r($rows);exit();
					if(isset($rows) && count($rows)>0)
					{

						for($i=0;$i<count($rows);$i++)
						{

								$DataToUpload[$i]->VehicleUserId = $rows[$i]->overspeed_report_vehicle_user_id;
								$DataToUpload[$i]->VehicleId = $rows[$i]->overspeed_report_vehicle_id;
								$DataToUpload[$i]->VehicleDevice = $rows[$i]->overspeed_report_vehicle_device;
								$DataToUpload[$i]->VehicleNo = $rows[$i]->overspeed_report_vehicle_no;
								$DataToUpload[$i]->VehicleName = $rows[$i]->overspeed_report_vehicle_name;
								$DataToUpload[$i]->VehicleType = $rows[$i]->overspeed_report_vehicle_type;
								$DataToUpload[$i]->VehicleCompany = $rows[$i]->overspeed_report_vehicle_company;
								$DataToUpload[$i]->VehicleMV03Imei = $rows[$i]->overspeed_report_imei;

								$DataToUpload[$i]->ReportType = $rows[$i]->overspeed_report_type;
								$DataToUpload[$i]->ReportName = $rows[$i]->overspeed_report_name;

								$DataToUpload[$i]->GPSSpeed = $rows[$i]->overspeed_report_speed;
								$DataToUpload[$i]->GPSTime = $rows[$i]->overspeed_report_gps_time;
								$DataToUpload[$i]->GPSStatus = $rows[$i]->overspeed_report_gpsstatus;

								$DataToUpload[$i]->GeofenceName = $rows[$i]->overspeed_report_geofence_name;
								$DataToUpload[$i]->GeofenceLimit = $rows[$i]->overspeed_report_geofence_limit;
								$DataToUpload[$i]->GeofenceType = $rows[$i]->overspeed_report_geofence_type;

								$DataToUpload[$i]->OverspeedJalur = $rows[$i]->overspeed_report_jalur;
								$DataToUpload[$i]->OverspeedLevel = $rows[$i]->overspeed_report_level;
								$DataToUpload[$i]->OverspeedLevelAlias = $rows[$i]->overspeed_report_level_alias;

								$DataToUpload[$i]->Location = $rows[$i]->overspeed_report_location;
								$DataToUpload[$i]->Coordinate = $rows[$i]->overspeed_report_coordinate;
								//$DataToUpload[$i]->SpeedStatus = $rows[$i]->overspeed_report_speed_status;

								//$datajson["Data"] = $DataToUpload;



						}

					}

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Overspeed",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}

	function req_driverdetected()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://temansharing.borneo-indobara.com/ugapi/getdriverdetected";
		$feature = array();

		$feature["UserId"] = 4204;
		$feature["CompanyId"] = "all";
		$feature["StartTime"] = "2022-08-29 00:00:00";
		$feature["EndTime"] = "2022-08-29 23:59:59";

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

	function getdriverdetected()
	{
		//ini_set('display_errors', 1);
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allcompany = 0;
		$now = date("Ymd");
		$payload = "";
		$dbtable = "ts_driver_change_new";

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

		if(!isset($postdata->CompanyId) || $postdata->CompanyId == "all")
		{
			$allcompany = 1;
		}

		if(!isset($postdata->CompanyId) || $postdata->CompanyId == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Company ID!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			$check_company = strpos($postdata->CompanyId,';');
			$ex_company = explode(";",$postdata->CompanyId);
			$UserIDBIB = 4408;


			$this->db->order_by("company_name","asc");
			if($allcompany == 0){
				$this->db->where_in("company_id",$ex_company);
			}
			$this->db->where("company_flag",0);

			$q = $this->db->get("company");
			$data = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "CompanyId Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "CompanyId"   	=> $postdata->CompanyId,
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
		}

		//jika mobil lebih dari nol
		if(count($data) > 0)
		{
			$DataToUpload = array();

			for($z=0;$z<count($data);$z++)
			{


				$companyid = $data[$z]->company_id;

					$rows = $this->getdriverdetected_data($dbtable, $allcompany, $companyid, $sdate, $edate);

					if(isset($rows) && count($rows)>0)
					{

						for($i=0;$i<count($rows);$i++)
						{

								$DataToUpload[$i]->VehicleNo = $rows[$i]->change_driver_vehicle_no;
								$DataToUpload[$i]->CompanyId = $rows[$i]->change_driver_company;
								$DataToUpload[$i]->CompanyName = $rows[$i]->change_driver_company_name;
								$DataToUpload[$i]->ImeiCam = $rows[$i]->change_imei;
								$DataToUpload[$i]->DriverIdSimper = $rows[$i]->change_driver_id;
								$DataToUpload[$i]->DriverName = $rows[$i]->change_driver_name;
								$DataToUpload[$i]->DriverDetected = $rows[$i]->change_driver_time;

						}

					}

			}

			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Driver Detected",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
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
		//$this->dbtrip->group_by("overspeed_report_gps_time");
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
	
	//NEW source overspeed from overspeed hour (updated on 1-11 2023)
	function getoverspeed_data_hour($dbtable, $company, $vehicle, $sdate, $edate)
	{
		$this->dbtensor = $this->load->database("tensor_report", true);
		if ($company != 0) {
		  $this->dbtensor->where_in("overspeed_report_vehicle_company", $company);
		  // $this->dbtensor->where("overspeed_report_vehicle_company", $company);
		}

		if ($vehicle != 0) {
		  $this->dbtensor->where("overspeed_report_vehicle_device", $vehicle);
		}

		$this->dbtensor->where("overspeed_report_gps_time >=", $sdate);
		$this->dbtensor->where("overspeed_report_gps_time <=", $edate);
		$this->dbtensor->where("overspeed_report_speed_status", 1);
		$this->dbtensor->order_by("overspeed_report_gps_time", "DESC");
		$q        = $this->dbtensor->get($dbtable);
		$rows = $q->result();

		$this->dbtensor->close();
		$this->dbtensor->cache_delete_all();
		return $rows;
	}

	function getdriverdetected_data($dbtable, $companyall, $company, $sdate, $edate)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->where("change_driver_time >=", $sdate);
		$this->dbts->where("change_driver_time <=", $edate);
		if($companyall != 1){
			$this->dbts->where("change_driver_company", $company);
		}
		$this->dbts->where("change_driver_flag", 0);
		$this->dbts->order_by("change_driver_time", "asc");

		$q = $this->dbts->get($dbtable);
		$rows = $q->result();

		$this->dbts->close();
		$this->dbts->cache_delete_all();
		return $rows;
	}

	function req_contractor()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://temansharing.borneo-indobara.com/ugapi/getcontractor";
		$feature = array();

		$feature["UserId"] = 4204; //pbi

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

	function getcontractor()
	{
		//ini_set('display_errors', 1);
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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
			}else{

				$UserIDBIB = 4408;
				$this->db->order_by("company_name","asc");
				$this->db->where("company_created_by",$UserIDBIB);
				$this->db->where("company_flag",0);
				$q = $this->db->get("company");
				$company = $q->result();

				if($q->num_rows == 0)
				{
					$feature["code"] = 400;
					$feature["msg"] = "Contrator Not Found!";
					$feature["payload"]    = $payload;
					echo json_encode($feature);
					exit;
				}else{
					$company = $q->result();

					$payload      		    = array(
					  "UserId"          => $postdata->UserId


					);
				}


			}

		}


		//jika mobil lebih dari nol
		if(count($company) > 0)
		{

			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($company);$z++)
			{
				$DataToUpload[$z]->CompanyId = $company[$z]->company_id;
				$DataToUpload[$z]->CompanyName = $company[$z]->company_name;
				$DataToUpload[$z]->CompanySiteLogin = $company[$z]->company_site;
				$DataToUpload[$z]->CompanySiteLogout = $company[$z]->company_site_logout;
				$DataToUpload[$z]->CompanyExca = $company[$z]->company_exca;
				$DataToUpload[$z]->CompanyFlag = $company[$z]->company_flag;
				//$datajson["Data"] = $DataToUpload;
			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Master Contrator",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}

	function req_vehicle()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://temansharing.borneo-indobara.com/ugapi/getvehicle";
		$feature = array();

		$feature["UserId"] = 4204; //pbi
		$feature["VehicleNo"] = "BBS 1207";
		//$feature["CompanyId"] = "all";


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

	function getvehicle()
	{
		//ini_set('display_errors', 1);
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
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

		/* if(!isset($postdata->CompanyId) || $postdata->CompanyId == "all")
		{
			$allcompany = 1;
		} */

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
			/* if($allcompany == 0){
				$this->db->where("vehicle_company",$postdata->CompanyId);
			} */
			$this->db->where("vehicle_user_id",$UserIDBIB);
			//$this->db->where("vehicle_status",1);
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
			}else{
				$vehicle = $q->result();

				$payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "VehicleNo"   	=> $postdata->VehicleNo
				  //"CompanyId"   	=> $postdata->CompanyId


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

				//printf("ATTR %s \r\n",$vehicle[$z]->vehicle_no);

								$DataToUpload[$i]->VehicleId = $vehicle[$i]->vehicle_id;
								$DataToUpload[$i]->VehicleUserID = $vehicle[$i]->vehicle_user_id;
								$DataToUpload[$i]->VehicleDevice = $vehicle[$i]->vehicle_device;
								$DataToUpload[$i]->VehicleNo = $vehicle[$i]->vehicle_no;
								$DataToUpload[$i]->VehicleNoBackup = $vehicle[$i]->vehicle_no_bk;
								$DataToUpload[$i]->VehicleName = $vehicle[$i]->vehicle_name;

								$DataToUpload[$i]->VehicleCardNo = $vehicle[$i]->vehicle_card_no;
								$DataToUpload[$i]->VehicleOperator = $vehicle[$i]->vehicle_operator;
								$DataToUpload[$i]->VehicleStatus = $vehicle[$i]->vehicle_status;

								$DataToUpload[$i]->VehicleImage = $vehicle[$i]->vehicle_image;
								$DataToUpload[$i]->VehicleCreatedDate = $vehicle[$i]->vehicle_created_date;
								$DataToUpload[$i]->VehicleType = $vehicle[$i]->vehicle_type;

								$DataToUpload[$i]->VehicleCompany = $vehicle[$i]->vehicle_company;
								$DataToUpload[$i]->VehicleSubCompany = $vehicle[$i]->vehicle_subcompany;
								$DataToUpload[$i]->VehicleGroup = $vehicle[$i]->vehicle_group;
								$DataToUpload[$i]->VehicleSubGroup = $vehicle[$i]->vehicle_subgroup;

								$DataToUpload[$i]->VehicleTanggalPasang = $vehicle[$i]->vehicle_tanggal_pasang;
								$DataToUpload[$i]->VehicleImei = $vehicle[$i]->vehicle_imei;
								$DataToUpload[$i]->VehicleMV03 = $vehicle[$i]->vehicle_mv03;
								$DataToUpload[$i]->VehicleSensor = $vehicle[$i]->vehicle_sensor;
								$DataToUpload[$i]->VehicleSOS = $vehicle[$i]->vehicle_sos;

								$DataToUpload[$i]->VehiclePortalRangka = $vehicle[$i]->vehicle_portal_rangka;
								$DataToUpload[$i]->VehiclePortalMesin = $vehicle[$i]->vehicle_portal_mesin;
								$DataToUpload[$i]->VehiclePortalRfidSPI = $vehicle[$i]->vehicle_portal_rfid_spi;
								$DataToUpload[$i]->VehiclePortalRfidWIM = $vehicle[$i]->vehicle_portal_rfid_wim;
								$DataToUpload[$i]->VehiclePortalPortalTare = $vehicle[$i]->vehicle_portal_tare;

								$DataToUpload[$i]->VehiclePortTime = $vehicle[$i]->vehicle_port_time;
								$DataToUpload[$i]->VehiclePortName = $vehicle[$i]->vehicle_port_name;
								$DataToUpload[$i]->VehicleRomTime = $vehicle[$i]->vehicle_rom_time;
								$DataToUpload[$i]->VehicleRomName = $vehicle[$i]->vehicle_rom_name;

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

	function req_alarmmaster()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://temansharing.borneo-indobara.com/ugapi/getalarmmaster";
		$feature = array();

		$feature["UserId"] = 4204; //pbi

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

	function getalarmmaster()
	{
		//ini_set('display_errors', 1);
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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
			}else{

				$UserIDBIB = 4408;
				$this->dbts = $this->load->database("webtracking_ts",true);

				$this->dbts->order_by("alarmmaster_id","asc");
				$this->dbts->where("alarmmaster_creator",$UserIDBIB);
				$this->dbts->where("alarmmaster_status",1);
				$q = $this->dbts->get("ts_alarmmaster");
				$master = $q->result();

				if($q->num_rows == 0)
				{
					$feature["code"] = 400;
					$feature["msg"] = "Data Master Alarm Not Found!";
					$feature["payload"]    = $payload;
					echo json_encode($feature);
					exit;
				}else{
					$master = $q->result();

					$payload      		    = array(
					  "UserId"          => $postdata->UserId


					);
				}


			}

		}


		//jika mobil lebih dari nol
		if(count($master) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($master);$z++)
			{
				$DataToUpload[$z]->AlarmMasterId = $master[$z]->alarmmaster_id;
				$DataToUpload[$z]->AlarmMasterName = $master[$z]->alarmmaster_name;
				$DataToUpload[$z]->AlarmMasterCreator = $master[$z]->alarmmaster_creator;
				$DataToUpload[$z]->AlarmMasterCreated = $master[$z]->alarmmaster_created;
				$DataToUpload[$z]->AlarmMasterStatus = $master[$z]->alarmmaster_status;
				$DataToUpload[$z]->AlarmMasterFlag = $master[$z]->alarmmaster_flag;
				//$datajson["Data"] = $DataToUpload;
			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Master Alarm",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();
			$this->dbts->close();
			$this->dbts->cache_delete_all();
		}

		exit;
	}

	function req_alarmtype()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://temansharing.borneo-indobara.com/ugapi/getalarmtype";
		$feature = array();

		$feature["UserId"] = 4204; //pbi

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

	function getalarmtype()
	{
		//ini_set('display_errors', 1);
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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
			}else{

				$UserIDBIB = 4408;
				$this->dbts = $this->load->database("webtracking_ts",true);

				$this->dbts->order_by("alarm_type","asc");
				$this->dbts->where("alarm_master_id <>","");
				$q = $this->dbts->get("ts_alarm");
				$data = $q->result();

				if($q->num_rows == 0)
				{
					$feature["code"] = 400;
					$feature["msg"] = "Data Type Alarm Not Found!";
					$feature["payload"]    = $payload;
					echo json_encode($feature);
					exit;
				}else{
					$data = $q->result();

					$payload      		    = array(
					  "UserId"          => $postdata->UserId


					);
				}


			}

		}

		if(count($data) > 0)
		{

			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($data);$z++)
			{
				$DataToUpload[$z]->AlarmId = $data[$z]->alarm_id;
				$DataToUpload[$z]->AlarmType = $data[$z]->alarm_type;
				$DataToUpload[$z]->AlarmName = $data[$z]->alarm_name;
				$DataToUpload[$z]->AlarmDesc = $data[$z]->alarm_desc;
				$DataToUpload[$z]->AlarmMasterId = $data[$z]->alarm_master_id;
				//$datajson["Data"] = $DataToUpload;
			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Type Alarm",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();
			$this->dbts->close();
			$this->dbts->cache_delete_all();

		}


		exit;
	}

	function getcompanyname_byID($id)
	{
		$name = "-";
		$this->db->select("company_id,company_name");
		$this->db->order_by("company_name", "asc");
		$this->db->where("company_id ", $id);
		$q = $this->db->get("company");
		$row = $q->row();
		if(count($row)>0){

			$name = $row->company_name;

		}else{

			$name = "-";
		}

		return $name;
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

		$this->dbts->close();
		$this->dbts->cache_delete_all();
	}
	
	//old source table
	function getlocationhour_old()
	{
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
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
	
	//new source table
	function getlocationhour()
	{
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
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
		$days_report = date("d", strtotime($datein));
		$month_report = date("F", strtotime($datein));
		$year_report = date("Y", strtotime($datein));
		
		$report = "location_hour_";
		$report_ritase = "location_hour_";
		$not_reg_list = array('1961','1962','1963'); 
		
		switch ($month_report)
		{
			case "January":
            $dbtable = $report."januari_".$year_report;
			$dbtable_ritase = $report_ritase."januari_".$year_report;
			$dbtable_before = $report."desember_".$year_before;
			$dbtable_next = $report."februari_".$year_report;
			break;
			case "February":
            $dbtable = $report."februari_".$year_report;
			$dbtable_ritase = $report_ritase."februari_".$year_report;
			$dbtable_before = $report."januari_".$year_report;
			$dbtable_next = $report."maret_".$year_report;
			break;
			case "March":
            $dbtable = $report."maret_".$year_report;
			$dbtable_ritase = $report_ritase."maret_".$year_report;
			$dbtable_before = $report."februari_".$year_report;
			$dbtable_next = $report."april_".$year_report;
			break;
			case "April":
            $dbtable = $report."april_".$year_report;
			$dbtable_ritase = $report_ritase."april_".$year_report;
			$dbtable_before = $report."maret_".$year_report;
			$dbtable_next = $report."mei_".$year_report;
			break;
			case "May":
            $dbtable = $report."mei_".$year_report;
			$dbtable_ritase = $report_ritase."mei_".$year_report;
			$dbtable_before = $report."april_".$year_report;
			$dbtable_next = $report."juni_".$year_report;
			break;
			case "June":
            $dbtable = $report."juni_".$year_report;
			$dbtable_ritase = $report_ritase."juni_".$year_report;
			$dbtable_before = $report."mei_".$year_report;
			$dbtable_next = $report."juli_".$year_report;
			break;
			case "July":
            $dbtable = $report."juli_".$year_report;
			$dbtable_ritase = $report_ritase."juli_".$year_report;
			$dbtable_before = $report."juni_".$year_report;
			$dbtable_next = $report."agustus_".$year_report;
			break;
			case "August":
            $dbtable = $report."agustus_".$year_report;
			$dbtable_ritase = $report_ritase."agustus_".$year_report;
			$dbtable_before = $report."juli_".$year_report;
			$dbtable_next = $report."september_".$year_report;
			break;
			case "September":
            $dbtable = $report."september_".$year_report;
			$dbtable_ritase = $report_ritase."september_".$year_report;
			$dbtable_before = $report."agustus_".$year_report;
			$dbtable_next = $report."oktober_".$year_report;
			break;
			case "October":
            $dbtable = $report."oktober_".$year_report;
			$dbtable_ritase = $report_ritase."oktober_".$year_report;
			$dbtable_before = $report."september_".$year_report;
			$dbtable_next = $report."november_".$year_report;
			break;
			case "November":
            $dbtable = $report."november_".$year_report;
			$dbtable_ritase = $report_ritase."november_".$year_report;
			$dbtable_before = $report."oktober_".$year_report;
			$dbtable_next = $report."desember_".$year_report;
			break;
			case "December":
            $dbtable = $report."desember_".$year_report;
			$dbtable_ritase = $report_ritase."desember_".$year_report;
			$dbtable_before = $report."november_".$year_report;
			$dbtable_next = $report."januari_".$year_next;
			break;
		}


		//$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts = $this->load->database("tensor_report", true);
		
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
				$result = $this->dbts->get($dbtable);
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
				$result = $this->dbts->get($dbtable);
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
				$result = $this->dbts->get($dbtable);
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
				$result = $this->dbts->get($dbtable);
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
				$result = $this->dbts->get($dbtable);
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
			
			$payload      		    = array(
				"UserId"          => $postdata->UserId
			);

			if ($postdata->UserId == "") {
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

	function getlocationreport_bk_reguler_2022_10_31()
	{
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
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

	 if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "")
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid Vehicle No";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }else {
		 $this->db->order_by("vehicle_id","desc");
		 $this->db->where("vehicle_no",$postdata->VehicleNo);
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
	 }

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

	 // echo "<pre>";
	 // var_dump($m1.'-'.$m2);die();
	 // echo "<pre>";

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

		 if($postdata->VehicleNo != "all"){
			 // $this->dbtrip->where("location_report_vehicle_device", $vehicle);
			 $this->dbtrip->where("location_report_vehicle_no", $postdata->VehicleNo);
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

	function getlocationreport()
	{
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
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
		 // $this->db->where("vehicle_status",1);
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

		//NEW CONDITION
		$postdata_rom = "";
		$postdata_port = "";
		$only_rom = 0;
		$only_port = 0;
		if(isset($postdata->Rom) && $postdata->Rom == "all")
		{
			$rombib_register = $this->config->item('rombib_register_autocheck'); // rom legal
			$postdata_rom = $postdata->Rom;
			$only_rom = 1;

			$payload = array(
			 "UserId"          => $postdata->UserId,
				 "VehicleNo"     => $postdata->VehicleNo,
				 "VehicleDevice" => $postdata->VehicleDevice,
				 "StartTime" 	 	 => $postdata->StartTime,
				 "EndTime" 	 	   => $postdata->EndTime,
				 "Status" 		   => $postdata->Status,
				 "Rom" 	 	 		   => $postdata_rom,

			);
		}

		if(isset($postdata->Port) && $postdata->Port == "all")
		{
			$port_register = $this->config->item('port_register_autocheck');
			$postdata_port = $postdata->Port;
			$only_port = 1;

			$payload = array(
			 "UserId"            => $postdata->UserId,
				 "VehicleNo"         => $postdata->VehicleNo,
				 "VehicleDevice" => $postdata->VehicleDevice,
				 "StartTime"    	 	 => $postdata->StartTime,
				 "EndTime"    	 	   => $postdata->EndTime,
				 "Status" 		       => $postdata->Status,
				 "Port" 		       => $postdata_port,
			);

		}

		if($only_port == 1 && $only_rom == 1){

			$merge_register = array_merge($rombib_register, $port_register);

			$payload = array(
				 "UserId"            => $postdata->UserId,
				 "VehicleNo"         => $postdata->VehicleNo,
				 "VehicleDevice" => $postdata->VehicleDevice,
				 "StartTime"    	 	 => $postdata->StartTime,
				 "EndTime"    	 	   => $postdata->EndTime,
				 "Status" 		       => $postdata->Status,
				 "Rom"    	 	  		 => $postdata_rom,
				 "Port" 		       => $postdata_port,
			);
		}

		if($only_port == 0 && $only_rom == 0){

			$payload = array(
				 "UserId"            => $postdata->UserId,
				 "VehicleNo"         => $postdata->VehicleNo,
				 "VehicleDevice" => $postdata->VehicleDevice,
				 "StartTime"    	 	 => $postdata->StartTime,
				 "EndTime"    	 	   => $postdata->EndTime,
				 "Status" 		       => $postdata->Status,

			);
		}


		// echo "<pre>";
		// var_dump("masuk");die();
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

	 // echo "<pre>";
	 // var_dump($m1.'-'.$m2);die();
	 // echo "<pre>";

	 $location_list = array("location","location_off","location_idle");

	 // if ($postdata->VehicleNo == "")
	 // {
		//  $feature["code"]    = 400;
		//  $feature["msg"]     = "Invalid Vehicle No";
		//  $feature["payload"] = $payload;
		//  echo json_encode($feature);
		//  exit;
	 // }

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

		if($only_port == 0 && $only_rom == 1){
			$this->dbtrip->where_in("location_report_location", $rombib_register);

		}

		if($only_port == 1 && $only_rom == 0){
			$this->dbtrip->where_in("location_report_location", $port_register);
		}

		if($only_port == 1 && $only_rom == 1){
			$this->dbtrip->where_in("location_report_location", $merge_register);
		}

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

	function getlocationreport_old()
	{
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
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

	 if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "")
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid Vehicle No";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }else {
		 $this->db->order_by("vehicle_id","desc");
		 $this->db->where("vehicle_no",$postdata->VehicleNo);
		 $this->db->where("vehicle_user_id",4408);
		 // $this->db->where("vehicle_status",1);
		 //$this->db->where("vehicle_active_date2 >",$now); //tidak expired
		 $q = $this->db->get("vehicle");
		 $vehicle = $q->result();

	  // echo "<pre>";
 		// var_dump($vehicle);die();
 		// echo "<pre>";

		 if($q->num_rows == 0)
		 {
			 $feature["code"] = 400;
			 $feature["msg"] = "Vehicle Not Found!";
			 $feature["payload"]    = $payload;
			 echo json_encode($feature);
			 exit;
		 }
	 }

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

		//NEW CONDITION
		$postdata_rom = "";
		$postdata_port = "";
		$only_rom = 0;
		$only_port = 0;
		if(isset($postdata->Rom) && $postdata->Rom == "all")
		{
			$rombib_register = $this->config->item('rombib_register_autocheck'); // rom legal
			$postdata_rom = $postdata->Rom;
			$only_rom = 1;

			$payload = array(
			 "UserId"            => $postdata->UserId,
				 "VehicleNo"         => $postdata->VehicleNo,
				 "StartTime"    	 	 => $postdata->StartTime,
				 "EndTime"    	 	   => $postdata->EndTime,
				 "Status" 		       => $postdata->Status,
				 "Rom"    	 	  		 => $postdata_rom,

			);
		}

		if(isset($postdata->Port) && $postdata->Port == "all")
		{
			$port_register = $this->config->item('port_register_autocheck');
			$postdata_port = $postdata->Port;
			$only_port = 1;

			$payload = array(
			 "UserId"            => $postdata->UserId,
				 "VehicleNo"         => $postdata->VehicleNo,
				 "StartTime"    	 	 => $postdata->StartTime,
				 "EndTime"    	 	   => $postdata->EndTime,
				 "Status" 		       => $postdata->Status,
				 "Port" 		       => $postdata_port,
			);

		}

		if($only_port == 1 && $only_rom == 1){

			$merge_register = array_merge($rombib_register, $port_register);

			$payload = array(
				 "UserId"            => $postdata->UserId,
				 "VehicleNo"         => $postdata->VehicleNo,
				 "StartTime"    	 	 => $postdata->StartTime,
				 "EndTime"    	 	   => $postdata->EndTime,
				 "Status" 		       => $postdata->Status,
				 "Rom"    	 	  		 => $postdata_rom,
				 "Port" 		       => $postdata_port,
			);
		}

		if($only_port == 0 && $only_rom == 0){

			$payload = array(
				 "UserId"            => $postdata->UserId,
				 "VehicleNo"         => $postdata->VehicleNo,
				 "StartTime"    	 	 => $postdata->StartTime,
				 "EndTime"    	 	   => $postdata->EndTime,
				 "Status" 		       => $postdata->Status,

			);
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

	 // echo "<pre>";
	 // var_dump($m1.'-'.$m2);die();
	 // echo "<pre>";

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

		 if($postdata->VehicleNo != "all"){
			 // $this->dbtrip->where("location_report_vehicle_device", $vehicle);
			 $this->dbtrip->where("location_report_vehicle_no", $postdata->VehicleNo);
		 }

		 if($statusfix != "all"){
			 $this->dbtrip->where("location_report_name", $statusfix);
		 }

		 $this->dbtrip->where("location_report_gps_time >=",$sdate);
		 $this->dbtrip->where("location_report_gps_time <=", $edate);

		if($only_port == 0 && $only_rom == 1){
			$this->dbtrip->where_in("location_report_location", $rombib_register);

		}

		if($only_port == 1 && $only_rom == 0){
			$this->dbtrip->where_in("location_report_location", $port_register);
		}

		if($only_port == 1 && $only_rom == 1){
			$this->dbtrip->where_in("location_report_location", $merge_register);
		}

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

	function getlocationreportv2()
	{
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
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

	 if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "")
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid Vehicle No";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }else {
		 $this->db->order_by("vehicle_id","desc");
		 $this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_device,vehicle_company,vehicle_type,vehicle_mv03,vehicle_info");
		 $this->db->where("vehicle_no",$postdata->VehicleNo);
		 $this->db->where("vehicle_user_id",4408);
		 // $this->db->where("vehicle_status",1);
		 //$this->db->where("vehicle_active_date2 >",$now); //tidak expired
		 $q = $this->db->get("vehicle");
		 $rowvehicle = $q->row();

		 if($q->num_rows == 0)
		 {
			 $feature["code"] = 400;
			 $feature["msg"] = "Vehicle Not Found!";
			 $feature["payload"]    = $payload;
			 echo json_encode($feature);
			 exit;
		 }
	 }

			$payload = array(
				 "UserId"            => $postdata->UserId,
				 "VehicleNo"         => $postdata->VehicleNo,
				 "StartTime"    	 	 => $postdata->StartTime,
				 "EndTime"    	 	   => $postdata->EndTime,


			);


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

	 // echo "<pre>";
	 // var_dump($m1.'-'.$m2);die();
	 // echo "<pre>";

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

	$rows = $this->getdatahistory($rowvehicle,$sdate,$edate);
	$total_rows = count($rows);
	$datafix = array();
	if($total_rows > 0)
	 {
		 for ($i=0; $i < $total_rows; $i++)
		 {
				//jika street_name kosong maka get ulang
				if($rows[$i]->gps_street_name == ""){
					/* $position = $this->getPosition_other($rows[$i]->gps_longitude_real,$rows[$i]->gps_latitude_real);
					if(isset($position)){
						$ex_position = explode(",",$position->display_name);
						if(count($ex_position)>0){
							$position_name = $ex_position[0];
						}else{
							$position_name = $ex_position[0];
						}
					}else{
						$position_name = $position->display_name;
					} */
					$position_name = "Belum terdaftar/di luar Geofence";
				}
				//jika sudah ada maka hilangkan koma
				else
				{
					$position = $rows[$i]->gps_street_name;
					if(isset($position)){
						$ex_position = explode(",",$position);
						if(count($ex_position)>0){
							$position_name = $ex_position[0];
						}else{
							$position_name = $ex_position[0];
						}
					}else{
						$position_name = $position;
					}
				}

				$gpsspeed_kph = round($rows[$i]->gps_speed*1.852,0);
				if($rows[$i]->gps_status == "A"){
					$gps_status = "OK";
				}else{
					$gps_status = "NOT OK";
				}

				$gps_time = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))); //sudah wita
				$cardinal_direction = $this->wind_cardinal($rows[$i]->gps_course);
				$cardinal_dir_kosongan = array("W","WNW","NW","NNW","N","NNE","NE","ENE");
				$cardinal_dir_muatan = array("E","ESE","SE","SSE","S","SSW","SW","WSW");

				if (in_array($cardinal_direction, $cardinal_dir_kosongan))
				{
					$jalur_bycardinal = "kosongan";

				}
				else if(in_array($cardinal_direction, $cardinal_dir_muatan))
				{
					$jalur_bycardinal = "muatan";
				}
				else
				{
					$jalur_bycardinal = "";
				}

				$jalur = $jalur_bycardinal;


				$sat_status = $rows[$i]->gps_sat_qty;
				$door = "";
				$sensor_new = "";
				$engine_code = "";
				$gps_looping_master = explode(",", $rows[$i]->gps_msg_ori);
				$iodata = $gps_looping_master[18];
				$iodata_dec = hexdec($iodata);
				$iodata_bin = decbin($iodata_dec);
				$gps_io = substr($iodata_bin,0,6);
				$total_gps_io = strlen($gps_io);

						if($total_gps_io == 1)
						{
							if($gps_io == 1)
							{
								$engine_code = 0;
								$door = 0;
								$sensor_new = 1;
							}
							else
							{
								$engine_code = 0;
								$door = 0;
								$sensor_new = 0;
							}
						}

						if($total_gps_io == 2)
						{
							if($gps_io == 10){
								$engine_code = 1;
								$door = 0;
								$sensor_new = 0;
							}else if($gps_io == 11){
								$engine_code = 1;
								$door = 0;
								$sensor_new = 1;
							}else{
								$engine_code = 0;
								$door = 0;
								$sensor_new = 0;
							}
						}

						if($total_gps_io == 3)
						{
							if($gps_io == 100){
								$engine_code = 0;
								$door = 1;
								$sensor_new = 0;
							}else if($gps_io == 101){
								$engine_code = 0;
								$door = 1;
								$sensor_new = 1;
							}else if($gps_io == 110){
								$engine_code = 1;
								$door = 1;
								$sensor_new = 0;
							}else if($gps_io == 111){
								$engine_code = 1;
								$door = 1;
								$sensor_new = 1;
							}else{
								$engine_code = 0;
								$door = 0;
								$sensor_new = 0;
							}
						}

						//ENGINE
						if($engine_code == "1"){
							$gps_io_port = "0000100000";
							$engine = "ON";
						}else{
							$gps_io_port = "0000000000";
							$engine = "OFF";
						}


						$gsm_status = $rows[$i]->gps_gsm_csq;

						if($gsm_status == ""){
							$gsm_status = $gps_looping_master[16];

						}
						if($gsm_status > 31){
							$gsm_status = 1;
						}

						if($sat_status == ""){
							$sat_status = $gps_looping_master[9];

						}

				if($rows[$i]->gps_speed > 0 ){
					$report_name = "move";
				}else if($rows[$i]->gps_speed == 0 && $gps_io_port == "0000100000"){
					$report_name = "idle";
				}else if($rows[$i]->gps_speed == 0 && $gps_io_port == "0000000000"){
					$report_name = "off";
				}else{
					$report_name = "-";
				}

				$geofence_name = "";
				$geofence_type = "";
				if($rows[$i]->gps_geofence_name != ""){
					$geofence_name = $rows[$i]->gps_geofence_name;
					$geofence_type = $rows[$i]->gps_geofence_type;
				}



				array_push($datafix, array(
				"VehicleUserId"              		=> $rowvehicle->vehicle_user_id,
			    "VehicleId" 						=> $rowvehicle->vehicle_id,
			    "VehicleDevice"      				=> $rowvehicle->vehicle_device,
			    "VehicleNo"     					=> $rowvehicle->vehicle_no,
			    "VehicleName"    					=> $rowvehicle->vehicle_name,
			    "VehicleType"   					=> $rowvehicle->vehicle_type,
			    "VehicleCompany" 					=> $rowvehicle->vehicle_company,
				"VehicleImei" 						=> $rowvehicle->vehicle_mv03,
			    "ReportName"          			  	=> $report_name,
			    "ReportSpeed"          				=> $gpsspeed_kph,
			    "ReportGpsStatus"       			=> $gps_status,
			    "ReportGpsTime"        				=> $gps_time,
				"ReportGeofenceName"   				=> $geofence_name,
				"ReportGeofenceType"   				=> $geofence_type,
				"ReportJalur"           			=> $jalur,
				"ReportDirection"   				=> $rows[$i]->gps_course,
			    "ReportLocation"        			=> $position_name,
			    "ReportCoordinate"      			=> $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real,
			    "ReportOdometer"        			=> $rows[$i]->gps_odometer,
			    "ReportFuelData"      				=> $rows[$i]->gps_mvd,
				"ReportGsm"       	  				=> $gsm_status,
				"ReportSat"       	  				=> $sat_status
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
	 $this->insertHitAPI("API Location Report V2", $payload, $nowstarttime, $nowendtime);
	 $this->db->close();
	 $this->db->cache_delete_all();

	 exit;
	}
	
	function getritasereport()
	{
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
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

	 if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "")
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid Vehicle No";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }else {
		 if($postdata->VehicleNo == "all"){
			$vehicle_all = 1;
			$rowvehicle = array();
		 }else{
			 $vehicle_all =0;
			 $this->db->order_by("vehicle_id","desc");
			 $this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_device");
			 $this->db->where("vehicle_no",$postdata->VehicleNo);
			 $this->db->where("vehicle_user_id",4408);
			 $q = $this->db->get("vehicle");
			 $rowvehicle = $q->row();

			 if($q->num_rows == 0)
			 {
				 $feature["code"] = 400;
				 $feature["msg"] = "Vehicle Not Found!";
				 $feature["payload"]    = $payload;
				 echo json_encode($feature);
				 exit;
			 }
			 
		 }
		 
	 }

			$payload = array(
				 "UserId"            => $postdata->UserId,
				 "VehicleNo"         => $postdata->VehicleNo,
				 "StartTime"    	 => $postdata->StartTime,
				 "EndTime"    	 	 => $postdata->EndTime,


			);


	 $report        = "ritase_trial_"; // new report
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

	$data = $this->getdataritase($rowvehicle,$vehicle_all,$sdate,$edate,$dbtable); 
	$total_rows = count($data);
	$datafix = array();
	if($total_rows > 0)
	 {
		for ($i=0; $i < $total_rows; $i++)
		{	
			if($data[$i]['ritase_report_wim_start_time'] == "" || $data[$i]['ritase_report_wim_start_time'] == "0000-00-00 00:00:00"){
														
				$wimtime = "";
			}else{
														
				$wimtime = date("d-M-Y H:i", strtotime($data[$i]['ritase_report_wim_start_time']));
			}
													
			if($data[$i]['ritase_report_wim2_start_time'] == "" || $data[$i]['ritase_report_wim2_start_time'] == "0000-00-00 00:00:00"){
														
				$wimtime2 = "";
			}else{
														
				$wimtime2 = date("d-M-Y H:i", strtotime($data[$i]['ritase_report_wim2_start_time']));
			}
	
			array_push($datafix, array(
				"VehicleUserId"    => $data[$i]['ritase_report_vehicle_user_id'],
				"VehicleId"        => $data[$i]['ritase_report_vehicle_id'],
				"VehicleDevice"    => $data[$i]['ritase_report_vehicle_device'],
				"VehicleNo"        => $data[$i]['ritase_report_vehicle_no'],
				"VehicleName"      => $data[$i]['ritase_report_vehicle_name'],
				"VehicleType"      => $data[$i]['ritase_report_vehicle_type'],
				"VehicleCompany"   => $data[$i]['ritase_report_vehicle_company'],
				"VehicleImei"      => "",
				"ReportType"       => $data[$i]['ritase_report_type'],
				"ReportName"      	=> $data[$i]['ritase_report_name'],
				
				"RomName"     		=> $data[$i]['ritase_report_start_location'],
				"RomGpsTime"        => $data[$i]['ritase_report_start_time'],
				
				"PortName"          => $data[$i]['ritase_report_end_location'],
				"PortGpsTime"       => $data[$i]['ritase_report_end_time'],
				
				"GroupDate"      	=> $data[$i]['ritase_report_end_date'],
				"GroupHour"  		=> $data[$i]['ritase_report_end_hour'],
				
				"Duration"   		=> $data[$i]['ritase_report_duration'],
				"DurationSecond" 	=> $data[$i]['ritase_report_duration_sec'],
				
				"DriverId"   		=> $data[$i]['ritase_report_driver'],
				"DriverName"      	=> $data[$i]['ritase_report_driver_name'],
				
				"WimId"      		=> "",
				"WimNetto"     		=> "",
				
				"Wim1"      		=> $wimtime,
				"Wim2"     			=> $wimtime2,
				
				"TimeFormat"  		=> date("d-M-Y H:i", strtotime($data[$i]['ritase_report_end_time'])),
				"DateFormat"     	=> date("d-M-Y", strtotime($data[$i]['ritase_report_shift_date'])),
				"ShiftFormat"     	=> $data[$i]['ritase_report_shift_name']
				
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
	 $this->insertHitAPI("API Ritase Report", $payload, $nowstarttime, $nowendtime);
	 $this->db->close();
	 $this->db->cache_delete_all();

	 exit;
	}

	function getdatahistory($rowvehicle,$vstarttime,$vendtime)
	{
		//$statusspeed_knot = round($statusspeed/1.852,0);

		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($vstarttime)));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($vendtime)));


			$json = json_decode($rowvehicle->vehicle_info);

			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');

				if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {

					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					$table         = $this->config->item("external_gpstable");
					$tableinfo     = $this->config->item("external_gpsinfotable");

					$this->dbhist  = $this->load->database($database, TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);


				} else {
					$table         = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
					$tableinfo     = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);

					$this->dbhist  = $this->load->database("default", TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				}

				$vehicle_device = explode("@", $rowvehicle->vehicle_device);
				$vehicle_no     = $rowvehicle->vehicle_no;
				$vehicle_dev    = $rowvehicle->vehicle_device;
				$vehicle_name   = $rowvehicle->vehicle_name;
				$vehicle_type   = $rowvehicle->vehicle_type;

				$tablehist     = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_gps";
				$tablehistinfo = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_info";

						//get data dari PORT (gps_gsm_csq,gps_sat_qty,)
						//$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
						$this->dbhist->distinct("gps_time");
						$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,gps_course,gps_msg_ori,
											   gps_street_name,gps_geofence_name,gps_geofence_type,gps_mvd,gps_odometer,gps_gsm_csq,gps_sat_qty");

						$this->dbhist->where("gps_name", $vehicle_device[0]);
						$this->dbhist->where("gps_host", $vehicle_device[1]);
						$this->dbhist->where("gps_time >=", $sdate);
						$this->dbhist->where("gps_time <=", $edate);
						$this->dbhist->order_by("gps_time", "asc");
						$q1    = $this->dbhist->get($table);
						$rows1 = $q1->result();
						$this->dbhist->close();
						$this->dbhist->cache_delete_all();



						//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
						$this->dbhist2->distinct("gps_time");
						$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,gps_course,gps_msg_ori,
											   gps_street_name,gps_geofence_name,gps_geofence_type,gps_mvd,gps_odometer,gps_gsm_csq,gps_sat_qty");

						$this->dbhist2->where("gps_name", $vehicle_device[0]);
						$this->dbhist2->where("gps_host", $vehicle_device[1]);
						$this->dbhist2->where("gps_time >=", $sdate);
						$this->dbhist2->where("gps_time <=", $edate);
						$this->dbhist2->order_by("gps_time", "asc");

						$q2    = $this->dbhist2->get($tablehist);
						$rows2 = $q2->result();
						$this->dbhist2->close();
						$this->dbhist2->cache_delete_all();

						$rows = array_merge($rows2, $rows1);

						//sudah urutan asc
						/* $trows = count($rows);
						if($trows > 0)
						{
							$rows = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
						}
						 */


					return $rows;

			}

	}
	
	function getdataritase($rowvehicle,$all_status,$sdate,$edate,$dbtable)
	{
		//main data
		$reporttype = 0; //belum dipakai
		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->order_by("ritase_report_end_time", "desc");
		if ($all_status != 1) {
			$this->dbtrip->where("ritase_report_vehicle_device", $rowvehicle->vehicle_device);
		}
		$this->dbtrip->where("ritase_report_duration_sec >=", 100); // lebih dari 1 menit : 100 detik
		$this->dbtrip->where("ritase_report_duration_sec <=", 43200); // kurang dari 12 jam (1 shift)
		$this->dbtrip->where("ritase_report_end_time >=", $sdate);
		$this->dbtrip->where("ritase_report_end_time <=", $edate);
		$this->dbtrip->where("ritase_report_end_geofence !=", "PORT BBC");
		$this->dbtrip->where("ritase_report_end_geofence !=", "");
		$this->dbtrip->where("ritase_report_type", $reporttype); //data fix (default) = 0
		$q1 = $this->dbtrip->get($dbtable);
		$rows1 = $q1->result_array();
		$rows = $rows1;
		return $rows;
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
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
	// var_dump($thisrule);die();
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

	// if ($postdata->VehicleDevice == "all") {
		$content = $this->getthisrulelocationreport($ReportTypeArray, $postdata->VehicleDevice, $postdata->StartTime, $postdata->EndTime);
	// }else {
	// 	$content = $this->getthisrulelocationreport($ReportTypeArray, $postdata->VehicleDevice, $postdata->StartTime, $postdata->EndTime);
	// }

	// echo "<pre>";
	// var_dump($content);die();
	// echo "<pre>";

	$data_1 = 0;
	$data_2 = 0;
	$data_3 = 0;
	$data_array_rpoerttype = array();
	$data_content = array_map('current', $content);

				if (in_array($ReportTypeArray[0], $data_content)) {
					$data_1 += 1;
				}

				if (in_array($ReportTypeArray[1], $data_content)) {
					$data_2 += 1;
				}

				if (in_array($ReportTypeArray[2], $data_content)) {
					$data_3 += 1;
				}

	$total_data_fix = ($data_1 + $data_2 + $data_3);

		// echo "<pre>";
		// var_dump($data_1.'-'.$data_2.'-'.$data_3);die();
		// // var_dump($data_content);die();
		// echo "<pre>";

		if ($total_data_fix == 3) {
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

	function getalarmevidence()
	{
		//ini_set('display_errors', 1);
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
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



		if($postdata->MediaType == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "No Data Media Type";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->MediaType) || $postdata->MediaType == "all")
		{
			$allmedia = 1;
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
			/* if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			} */
			$this->db->where("vehicle_no",$postdata->VehicleNo);
			$this->db->where("vehicle_user_id",$UserIDBIB);
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
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "VehicleNo"   	=> $postdata->VehicleNo,
				  "MediaType"   		=> $postdata->MediaType,
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
			$mediatype = $postdata->MediaType;

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

			if($allvehicle == 1){

				$diff1 = date("d", strtotime($sdate));
				$diff2 = date("d", strtotime($edate));

				if($diff1 != $diff2)
				{
					$feature["code"] = 400;
					$feature["msg"] = "All Vehicle must be in the same Date!";
					$feature["payload"]    = $payload;
					echo json_encode($feature);
					exit;
				}

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

					$rows = $this->getalarmevidence_data($dbtable, $company, $vdeviceid, $mediatype, $sdate, $edate, $allvehicle, $allmedia);
					//print_r($rows);exit();
					if(isset($rows) && count($rows)>0)
					{

						for($i=0;$i<count($rows);$i++)
						{
								$DataToUpload[$i]->VehicleUserId = $rows[$i]->alarm_report_vehicle_user_id;
								$DataToUpload[$i]->VehicleDevice = $rows[$i]->alarm_report_vehicle_id;
								$DataToUpload[$i]->VehicleNo = $rows[$i]->alarm_report_vehicle_no;
								$DataToUpload[$i]->VehicleName = $rows[$i]->alarm_report_vehicle_name;
								$DataToUpload[$i]->VehicleType = $rows[$i]->alarm_report_vehicle_type;
								$DataToUpload[$i]->VehicleCompany = $rows[$i]->alarm_report_vehicle_company;
								$DataToUpload[$i]->VehicleCamImei = $rows[$i]->alarm_report_imei;


								$DataToUpload[$i]->Alarmtype = $rows[$i]->alarm_report_type;


								if($rows[$i]->alarm_report_media == 1)
								{
									$DataToUpload[$i]->AlarmName = "";
									$DataToUpload[$i]->AlarmLevel = "";
									$DataToUpload[$i]->GPSStatus = "";
									$DataToUpload[$i]->Speed = "";

								}
								else
								{
									$DataToUpload[$i]->AlarmName = $rows[$i]->alarm_report_name;
									$DataToUpload[$i]->AlarmLevel = $rows[$i]->alarm_report_level;
									$DataToUpload[$i]->GPSStatus = $rows[$i]->alarm_report_gpsstatus;
									$DataToUpload[$i]->Speed = $rows[$i]->alarm_report_speed;
								}


								$DataToUpload[$i]->AlarmMedia = $rows[$i]->alarm_report_media;
								$DataToUpload[$i]->AlarmChannel = $rows[$i]->alarm_report_channel;

								$DataToUpload[$i]->StartTime = $rows[$i]->alarm_report_start_time;
								$DataToUpload[$i]->EndTime = $rows[$i]->alarm_report_end_time;

								$DataToUpload[$i]->LocationStart = $rows[$i]->alarm_report_location_start;
								$DataToUpload[$i]->LocationEnd = $rows[$i]->alarm_report_location_end;

								$DataToUpload[$i]->CoordinateStart = $rows[$i]->alarm_report_coordinate_start;
								$DataToUpload[$i]->CoordinateEnd = $rows[$i]->alarm_report_coordinate_end;

								$DataToUpload[$i]->DownloadUrl = $rows[$i]->alarm_report_downloadurl;
								$DataToUpload[$i]->FilePath = $rows[$i]->alarm_report_path;
								$DataToUpload[$i]->FileUrl = $rows[$i]->alarm_report_fileurl;
								$DataToUpload[$i]->FileSize = $rows[$i]->alarm_report_size;

						}

					}

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Alarm Evidence",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}

	function getalarmevidence_data($dbtable, $company, $vehicledevice, $mediatype, $sdate, $edate, $allvehicle, $allmedia)
	{
		$sdate = date("Y-m-d H:i:s", strtotime($sdate) - (60 * 60));
		$edate = date("Y-m-d H:i:s", strtotime($edate) - (60 * 60));

		$nowday            = date("d");
		$end_day_fromEdate = date("d", strtotime($edate));

		if ($nowday == $end_day_fromEdate) {
			$edate = date("Y-m-d H:i:s");
		}

		//print_r($sdate." ".$edate." ".$vehicledevice);//exit();

		$black_list  = array("401","428","451","478","602","603","608","609","652","653","658","659",
							 "600","601","650","651"
							); //lane deviation & forward collation

		$street_register = $this->getAllStreetKM(4408); //HAULING



		$this->dbtrip = $this->load->database("tensor_report", true);
		/* if ($company != "all") {
			$this->dbtrip->where("alarm_report_vehicle_company", $company);
		} */

		if($allvehicle != 1){
			$this->dbtrip->where("alarm_report_vehicle_id", $vehicledevice);
		}

		if($allmedia != 1){
			$this->dbtrip->where("alarm_report_media",$mediatype); //photo = 0 , video = 1

			if($mediatype == 0){

				$this->dbtrip->where("alarm_report_gpsstatus !=", "");
			}
			else
			{
				//print_r("MEDIA : ". $mediatype);//exit();
			}
		}

		$this->dbtrip->where("alarm_report_start_time >=", $sdate);
		$this->dbtrip->where("alarm_report_start_time <=", $edate);
		$this->dbtrip->where_not_in('alarm_report_type', $black_list);

		$this->dbtrip->order_by("alarm_report_start_time","asc");
		//$this->dbtrip->group_by("alarm_report_start_time");
		$q = $this->dbtrip->get($dbtable);
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		$rows = $q->result();

		return $rows;
	}

	function req_geofencerambu()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://temanbib.borneo-indobara.com/ugapi/getgeorambu";
		$feature = array();

		$feature["UserId"] = 4204; //pbi

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

	function getgeorambu()
	{
		//ini_set('display_errors', 1);
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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
			}else{

				$UserIDBIB = 4408;
				$this->dblive = $this->load->database("webtracking_gps_temanindobara_live", true);
				$this->dblive->select("geofence_id,geofence_user,geofence_status,geofence_created,geofence_polygon,geofence_json,
									   geofence_name,geofence_type,geofence_speed,geofence_speed_muatan,geofence_speed_alias,geofence_speed_muatan_alias"
									 );
				$this->dblive->order_by("geofence_id","asc");
				$this->dblive->where("geofence_user",$UserIDBIB);
				//$this->dblive->where("geofence_status",1); all sent
				//$this->dblive->limit(1);
				$q = $this->dblive->get("geofence");
				$data = $q->result();

				if($q->num_rows == 0)
				{
					$feature["code"] = 400;
					$feature["msg"] = "Geofence Rambu Not Found!";
					$feature["payload"]    = $payload;
					echo json_encode($feature);
					exit;
				}else{
					$data = $q->result();

					$payload      		    = array(
					  "UserId"          => $postdata->UserId


					);
				}


			}

		}


		//jika mobil lebih dari nol
		if(count($data) > 0)
		{

			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($data);$z++)
			{
				$tes = mb_convert_encoding($data[$z]->geofence_polygon,'UTF-8','UTF-8');
				$DataToUpload[$z]->GeofenceId = $data[$z]->geofence_id;
				$DataToUpload[$z]->GeofenceUser = $data[$z]->geofence_user;
				$DataToUpload[$z]->GeofenceName = $data[$z]->geofence_name;
				//$DataToUpload[$z]->GeofencePolygon = $tes;
				//$DataToUpload[$z]->GeofencePolygon = $data[$z]->geofence_polygon;
				$DataToUpload[$z]->GeofencePolygon = json_decode($data[$z]->geofence_json);
				$DataToUpload[$z]->GeofenceType = $data[$z]->geofence_type;
				$DataToUpload[$z]->LimitKosongan = $data[$z]->geofence_speed;
				$DataToUpload[$z]->LimitKosonganAlias = $data[$z]->geofence_speed_alias;
				$DataToUpload[$z]->LimitMuatan = $data[$z]->geofence_speed_muatan;
				$DataToUpload[$z]->LimitMuatanAlias = $data[$z]->geofence_speed_muatan_alias;

				$DataToUpload[$z]->GeofenceStatus = $data[$z]->geofence_status;
				$DataToUpload[$z]->GeofenceCreated = $data[$z]->geofence_created;

			}

			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Geofence Rambu",$payload,$nowstarttime,$nowendtime);
			$this->dblive->close();
			$this->dblive->cache_delete_all();

		}


		exit;
	}

	function req_geofencehauling()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://temanbib.borneo-indobara.com/ugapi/getgeohauling";
		$feature = array();

		$feature["UserId"] = 4204; //pbi

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

	function getgeohauling()
	{
		//ini_set('display_errors', 1);
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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
			$gray_area = array("KM 6,","KM 6.5,","KM 7,");
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
			}else{

				$UserIDBIB = 4408;
				$this->db->select("street_id,street_creator,street_flag,street_name,street_alias,street_type,street_created,street_group,street_serialize,street_company_parent,
								   street_order

								 ");
				$this->db->order_by("street_id","asc");
				$this->db->where("street_creator",$UserIDBIB);
				//$this->db->where_in("street_name", $gray_area);
				//$this->dblive->where("geofence_status",1); all sent
				//$this->db->limit(1);
				$q = $this->db->get("street");
				$data = $q->result();

				if($q->num_rows == 0)
				{
					$feature["code"] = 400;
					$feature["msg"] = "Geofence Hauling Not Found!";
					$feature["payload"]    = $payload;
					echo json_encode($feature);
					exit;
				}else{
					$data = $q->result();

					$payload      		    = array(
					  "UserId"          => $postdata->UserId


					);
				}


			}

		}


		//jika mobil lebih dari nol
		if(count($data) > 0)
		{

			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($data);$z++)
			{
				//$tes = mb_convert_encoding($data[$z]->geofence_polygon,'UTF-8','UTF-8');
				$DataToUpload[$z]->StreetId = $data[$z]->street_id;
				$DataToUpload[$z]->StreetUser = $data[$z]->street_creator;
				$DataToUpload[$z]->StreetName = $data[$z]->street_name;
				$DataToUpload[$z]->StreetAlias = $data[$z]->street_alias;
				//$DataToUpload[$z]->GeofencePolygon = $tes;

				if (in_array($data[$z]->street_name, $gray_area)){
					$geom_rev = $this->get_polygon_street_bk($data[$z]->street_name,$UserIDBIB);

					//$geom_rev = $data[$z]->street_serialize;
					$geom = $geom_rev;
				}else{
					$geom = $data[$z]->street_serialize;
				}
				$DataToUpload[$z]->StreetPolygon = json_decode($geom);
				$DataToUpload[$z]->StreetType = $data[$z]->street_type;
				$DataToUpload[$z]->StreetGroup = $data[$z]->street_group;
				$DataToUpload[$z]->StreetCompany = $data[$z]->street_company_parent;
				$DataToUpload[$z]->StreetOrder = $data[$z]->street_order;
				$DataToUpload[$z]->StreetCreated = $data[$z]->street_created;

			}

			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Geofence Hauling",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}

	function get_polygon_street_bk($name,$UserIDBIB)
	{
		$this->db->select("street_id,street_name,street_serialize ");
		$this->db->order_by("street_id","asc");
		$this->db->where("street_creator",$UserIDBIB);
		$this->db->where("street_name", $name);
		$this->db->limit(1);
		$q_r = $this->db->get("street_bk");
		$data_r = $q_r->row();
		if(count($data_r)>0){
			$result = $data_r->street_serialize;
		}else{

			$result = "";
		}

		return $result;
	}

	function req_rawgps()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://temanbib.borneo-indobara.com/ugapi/getrawgps";
		$feature = array();

		$feature["UserId"] = 4204; //pbi
		$feature["VehicleNo"] = "BMT 3148";
		$feature["StartTime"] = "2022-09-31 00:00:00";
		$feature["EndTime"] = "2022-09-31 23:59:59";

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

	function getrawgps()
	{
		//ini_set('display_errors', 1);
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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
			$UserIDBIB = 4408;

			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			/* if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			} */
			$this->db->where("vehicle_no",$postdata->VehicleNo);
			$this->db->where("vehicle_user_id",$UserIDBIB);
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
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
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
				$feature["msg"] = "Date must be in the same Date!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
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
				$company = $vehicle[$z]->vehicle_company;
					$sdate_gmt = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($sdate))); //wita
					$edate_gmt = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($edate)));  //wita
					$rows = $this->getrawgps_data($vehicle[$z], $sdate_gmt, $edate_gmt);

					if(isset($rows) && count($rows)>0)
					{

						for($i=0;$i<count($rows);$i++)
						{
								if( $rows[$i]->gps_info_io_port == "0000100000"){
									$engine_bit = 1;
								}else{
									$engine_bit = 0;

								}

								if($rows[$i]->gps_speed > 1 ){
									$engine_bit = 1;
								}

								if($rows[$i]->gps_cs == 53){
									$io2_bit = 1;
								}else{
									$io2_bit = 0;
								}

								$gpstime_wta = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))); //wita

								$DataToUpload[$i]->Name = $rows[$i]->gps_name;
								$DataToUpload[$i]->Host = $rows[$i]->gps_host;
								$DataToUpload[$i]->Type = $rows[$i]->gps_type;
								$DataToUpload[$i]->Speed = $rows[$i]->gps_speed;
								$DataToUpload[$i]->Direction = $rows[$i]->gps_course;
								$DataToUpload[$i]->Fuel = $rows[$i]->gps_mvd;
								$DataToUpload[$i]->Engine = $engine_bit;
								$DataToUpload[$i]->Io2 = $io2_bit;
								$DataToUpload[$i]->GpsStatus = $rows[$i]->gps_status;
								$DataToUpload[$i]->GpsTime = $gpstime_wta;
								$DataToUpload[$i]->Latitude = $rows[$i]->gps_latitude_real;
								$DataToUpload[$i]->Longitude = $rows[$i]->gps_longitude_real;
								$DataToUpload[$i]->Odometer = $rows[$i]->gps_odometer;



						}

					}

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API RAW GPS",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}

	function getrawgpsv2()
	{
		//ini_set('display_errors', 1);
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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

		if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "all")
		{
			$allvehicle = 1;
		}

		if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Vehicle Device!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			$check_vehicle = strpos($postdata->VehicleDevice,';');
			$ex_vehicle = explode(";",$postdata->VehicleDevice);
			$UserIDBIB = 4408;

			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			/* if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			} */
			$this->db->where("vehicle_device",$postdata->VehicleDevice);
			//$this->db->where("vehicle_device","DISABLE DULU");
			$this->db->where("vehicle_user_id",$UserIDBIB);
			$this->db->where("vehicle_status",1);
			//$this->db->where("vehicle_active_date2 >",$now); //tidak expired
			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "API ini tidak bisa diakses untuk sementara waktu";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "VehicleDevice"   => $postdata->VehicleDevice,
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
				$feature["msg"] = "Date must be in the same Date!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
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
				$company = $vehicle[$z]->vehicle_company;
					$sdate_gmt = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($sdate))); //wita
					$edate_gmt = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($edate)));  //wita
					$rows = $this->getrawgps_datav2($vehicle[$z], $sdate_gmt, $edate_gmt);

					if(isset($rows) && count($rows)>0)
					{

						for($i=0;$i<count($rows);$i++)
						{
								if( $rows[$i]->gps_info_io_port == "0000100000"){
									$engine_bit = 1;
								}else{
									$engine_bit = 0;

								}

								if($rows[$i]->gps_speed > 1 ){
									$engine_bit = 1;
								}

								if($rows[$i]->gps_cs == 53){
									$io2_bit = 1;
								}else{
									$io2_bit = 0;
								}

								$gpstime_wta = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))); //wita

								$street_name = "-";
								$geofence_name = "-";
								if($rows[$i]->gps_street_name != ""){
									$street_name = $rows[$i]->gps_street_name;
								}

								if($rows[$i]->gps_geofence_name != ""){
									$geofence_name = $rows[$i]->gps_geofence_name;
								}

								$DataToUpload[$i]->Name = $rows[$i]->gps_name;
								$DataToUpload[$i]->Host = $rows[$i]->gps_host;
								$DataToUpload[$i]->Type = $rows[$i]->gps_type;
								$DataToUpload[$i]->Speed = $rows[$i]->gps_speed;
								$DataToUpload[$i]->Direction = $rows[$i]->gps_course;
								$DataToUpload[$i]->Fuel = $rows[$i]->gps_mvd;
								$DataToUpload[$i]->Engine = $engine_bit;
								$DataToUpload[$i]->Io2 = $io2_bit;
								$DataToUpload[$i]->GpsStatus = $rows[$i]->gps_status;
								$DataToUpload[$i]->GpsTime = $gpstime_wta;
								$DataToUpload[$i]->Latitude = $rows[$i]->gps_latitude_real;
								$DataToUpload[$i]->Longitude = $rows[$i]->gps_longitude_real;
								$DataToUpload[$i]->Odometer = $rows[$i]->gps_odometer;
								$DataToUpload[$i]->StreetName = $street_name;
								$DataToUpload[$i]->GeofenceName = $geofence_name;
								$DataToUpload[$i]->Odometer = $rows[$i]->gps_odometer;



						}

					}

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API RAW GPS",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}

	function getrawgps_data($rowvehicle, $sdate, $edate)
	{

		$rows = "";
        $json = json_decode($rowvehicle->vehicle_info);
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
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$this->dbhist = $this->load->database("default", TRUE);
				$this->dbhist2 = $this->load->database("gpshistory",true);

			}

				$vehicle_device = explode("@", $rowvehicle->vehicle_device);
               	$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
				$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";

				$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist->order_by("gps_time","asc");
				//$this->dbhist->group_by("gps_time");
				$this->dbhist->where("gps_name", $vehicle_device[0]);
                $this->dbhist->where("gps_time >=", $sdate);
                $this->dbhist->where("gps_time <=", $edate);
				$this->dbhist->select("gps_name,gps_host,gps_type,gps_speed,gps_course,gps_status,gps_mvd,gps_cs,gps_time,gps_latitude_real,gps_longitude_real,gps_odometer,gps_info_io_port");
				//$this->dbhist->limit(10);
				$this->dbhist->from($table);
                $q = $this->dbhist->get();
                $rows1 = $q->result();


				$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist2->order_by("gps_time","asc");
				//$this->dbhist2->group_by("gps_time");
                $this->dbhist2->where("gps_name", $vehicle_device[0]);
                $this->dbhist2->where("gps_time >=", $sdate);
                $this->dbhist2->where("gps_time <=", $edate);
				$this->dbhist2->select("gps_name,gps_host,gps_type,gps_speed,gps_course,gps_status,gps_mvd,gps_cs,gps_time,gps_latitude_real,gps_longitude_real,gps_odometer,gps_info_io_port");
				//$this->dbhist2->limit(10);
				$this->dbhist2->from($tablehist);
				$q2 = $this->dbhist2->get();
                $rows2 = $q2->result();

				$rows = array_merge($rows1, $rows2);
				$rows = $this->array_sort($rows, 'gps_time', SORT_ASC);
				$trows = count($rows);

                //printf("TOTAL DATA : %s \r\n",$trows);


		}

		return $rows;
	}

	function getrawgps_datav2($rowvehicle, $sdate, $edate)
	{

		$rows = "";
        $json = json_decode($rowvehicle->vehicle_info);
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
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$this->dbhist = $this->load->database("default", TRUE);
				$this->dbhist2 = $this->load->database("gpshistory",true);

			}

				$vehicle_device = explode("@", $rowvehicle->vehicle_device);
               	$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
				$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";

				$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist->order_by("gps_time","asc");
				//$this->dbhist->group_by("gps_time");
				$this->dbhist->where("gps_name", $vehicle_device[0]);
                $this->dbhist->where("gps_time >=", $sdate);
                $this->dbhist->where("gps_time <=", $edate);
				//$this->dbhist2->where("gps_speed >", 0); //di coba all data status
				$this->dbhist->select("gps_name,gps_host,gps_type,gps_speed,gps_course,gps_status,gps_mvd,gps_cs,gps_time,gps_latitude_real,gps_longitude_real,gps_odometer,gps_street_name,gps_geofence_name,gps_info_io_port");
				$this->dbhist->limit(4000);
				$this->dbhist->from($table);
                $q = $this->dbhist->get();
                $rows1 = $q->result();


				$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist2->order_by("gps_time","asc");
				//$this->dbhist2->group_by("gps_time");
                $this->dbhist2->where("gps_name", $vehicle_device[0]);
                $this->dbhist2->where("gps_time >=", $sdate);
                $this->dbhist2->where("gps_time <=", $edate);
				//$this->dbhist2->where("gps_speed >", 0);
				$this->dbhist2->select("gps_name,gps_host,gps_type,gps_speed,gps_course,gps_status,gps_mvd,gps_cs,gps_time,gps_latitude_real,gps_longitude_real,gps_odometer,gps_street_name,gps_geofence_name,gps_info_io_port");
				$this->dbhist2->limit(4000);
				$this->dbhist2->from($tablehist);
				$q2 = $this->dbhist2->get();
                $rows2 = $q2->result();

				$rows = array_merge($rows1, $rows2);
				$rows = $this->array_sort($rows, 'gps_time', SORT_ASC);
				$trows = count($rows);

                //printf("TOTAL DATA : %s \r\n",$trows);


		}

		return $rows;
	}

	function getritasehour()
	{
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
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
		$port_list = array("PORT BIB","PORT BIR","PORT TIA");
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
           // $this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_coordinate,ritase_report_latitude, ritase_report_longitude,ritase_report_from,ritase_report_to,ritase_report_duration");
            $shift = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00");
            $this->dbts->where("ritase_report_gps_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);

            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            //$this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_coordinate,ritase_report_latitude, ritase_report_longitude,ritase_report_from,ritase_report_to,ritase_report_duration");
            $shift1 = array("18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
            $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
            $this->dbts->where("ritase_report_gps_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift1);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            //$this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_coordinate,ritase_report_latitude, ritase_report_longitude,ritase_report_from,ritase_report_to,ritase_report_duration");
            $this->dbts->where("ritase_report_gps_date", $next);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift2);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {
            //$this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_coordinate,ritase_report_latitude, ritase_report_longitude,ritase_report_from,ritase_report_to,ritase_report_duration");
            $shift1 = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
            $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
            $this->dbts->where("ritase_report_gps_date", $date);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift1);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            //$this->dbts->select("ritase_report_vehicle_no,ritase_report_company_name,ritase_report_gps_date,ritase_report_gps_hour,ritase_report_coordinate,ritase_report_latitude, ritase_report_longitude,ritase_report_from,ritase_report_to,ritase_report_duration");
            $this->dbts->where("ritase_report_gps_date", $next);
			$this->dbts->where("ritase_report_duration_sec >", 0);
			$this->dbts->where_in("ritase_report_to", $port_list);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("ritase_report_gps_hour", $shift2);
            $this->dbts->order_by("ritase_report_gps_hour", "asc");
            $this->dbts->order_by("ritase_report_company_name", "asc");
            $result = $this->dbts->get("ts_ritase_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        }


		$datafix = array();
		for ($i=0; $i < sizeof($data); $i++) {
			array_push($datafix, array(
				"VehicleUserId"    => $data[$i]['ritase_report_vehicle_user_id'],
				"VehicleId"        => $data[$i]['ritase_report_vehicle_id'],
				"VehicleDevice"    => $data[$i]['ritase_report_vehicle_device'],
				"VehicleNo"        => $data[$i]['ritase_report_vehicle_no'],
				"VehicleName"      => $data[$i]['ritase_report_vehicle_name'],
				"VehicleType"      => $data[$i]['ritase_report_vehicle_type'],
				"VehicleCompany"   => $data[$i]['ritase_report_vehicle_company'],
				"VehicleImei"      => $data[$i]['ritase_report_imei'],
				"ReportType"       => $data[$i]['ritase_report_type'],
				"ReportName"      	=> $data[$i]['ritase_report_name'],
				"RomName"     		=> $data[$i]['ritase_report_from'],
				"RomGpsTime"        => $data[$i]['ritase_report_from_time'],
				"PortName"          => $data[$i]['ritase_report_to'],
				"PortGpsTime"       => $data[$i]['ritase_report_to_time'],
				"GroupDate"      	=> $data[$i]['ritase_report_gps_date'],
				"GroupHour"  		=> $data[$i]['ritase_report_gps_hour'],
				"Duration"   		=> $data[$i]['ritase_report_duration'],
				"DurationSecond" 	=> $data[$i]['ritase_report_duration_sec'],
				"DriverId"   		=> $data[$i]['ritase_report_driver'],
				"DriverName"      	=> $data[$i]['ritase_report_driver_name'],
				"WimId"      		=> $data[$i]['ritase_report_wim_id'],
				"WimNetto"     		=> $data[$i]['ritase_report_wim_netto']
			));
		}


		if ($nr > 0) {
				echo json_encode(array("code" => 200, "msg" => "success",  "data" => $datafix, "payload" => $payload), JSON_NUMERIC_CHECK);
		} else {
				echo json_encode(array("code" => 200, "msg" => "Data Empty"));
		}

		// INI DIAKTIFKAN UNTUK MENCATAT HIT DARI API
		$nowendtime = date("Y-m-d H:i:s");
		$this->insertHitAPI("API Ritase Hour", $payload, $nowstarttime, $nowendtime);
		$this->db->close();
		$this->db->cache_delete_all();

		exit;
	}

	function gettokenugems($userid="")
	{
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
				$result = json_decode($json_response);
				$data =  $result->data;
				$token = $data->token;

				unset($data);
				$data["sess_value"] = $token;
				$data["sess_type"] = "TOKEN";
				$data["sess_lastmodified"] = date("Y-m-d H:i:s");
				$data["sess_status"] = 1;
				$data["sess_user"] = $userid;

				$this->dbts = $this->load->database("webtracking_ts", true);
				$this->dbts->insert("ts_ugems_token",$data);

				$nowtime = date("Y-m-d H:i:s");
				printf("===GET TOKEN UGEMS SUCESS !! at %s \r\n", $nowtime);
				$this->dbts->close();
				$this->dbts->cache_delete_all();

	}

	function getLastToken($userid)
	{

		$this->db = $this->load->database("webtracking_ts", TRUE);
		$this->db->select("sess_value");
		$this->db->order_by("sess_id", "desc");
		$this->db->where("sess_user", $userid);
		$this->db->where("sess_status", 1);
		$q = $this->db->get("ts_ugems_token");
		$row = $q->row();
		if(count($row)>0){
			$sessid = $row->sess_value;
		}else{
			$sessid = "";
		}
		return $sessid;

	}

	function pushrawgps($userid="")
	{
		ini_set('display_errors', 1);
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date("Y-m-d H:i:s");
		printf("===Get API Service . . . at %s \r\n", $nowdate);
		printf("======================================\r\n");

		if($userid == ""){
			printf("NO DATA USER ID !! \r\n");
			return;
		}

		$this->db->order_by("vehicle_id","desc");
		$this->db->where("vehicle_user_id",$userid);
		$this->db->where("vehicle_status",1);
		//$this->db->where("vehicle_no","BBS 1210");
		//$this->db->limit(50);
		$q = $this->db->get("vehicle");

		$vehicle = $q->result();
		$totalvehicle = count($vehicle);

		$mytoken = $this->getLastToken($userid);

		//jika mobil lebih dari nol
		if(count($vehicle) > 0)
		{
			$DataToUpload = array();
			unset($DataToUpload);
			$j = 1;
			for($z=0;$z<count($vehicle);$z++)
			{

				printf("===Process Check Last Info For %s %s (%d/%d) \r\n", $vehicle[$z]->vehicle_no, $vehicle[$z]->vehicle_device, $j, $totalvehicle);

				$devices = explode("@", $vehicle[$z]->vehicle_device);
				$vehicle_dblive = $vehicle[$z]->vehicle_dbname_live;
				$vehicle_imei = $devices[0];
				$gps = $this->getlastposition_fromDBLive($vehicle_imei,$vehicle_dblive);

					if(isset($gps) && count($gps)>0)
					{
						$datajson = json_decode($gps->vehicle_autocheck);

							if($gps->gps_speed > 1 ){
								$engine = "ON";
							}else{
								$engine = $datajson->auto_last_engine;
							}

							if($engine == 'ON'){
								$engine_bit = 1;
							}else{
								$engine_bit = 0;
							}

								if($gps->gps_cs == 53){
									$io2_bit = 1;
								}else{
									$io2_bit = 0;
								}

								$gpstime_wta = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($gps->gps_time))); //wita

								$DataToUpload[$z]->name = $gps->gps_name;
								$DataToUpload[$z]->host = $gps->gps_host;
								$DataToUpload[$z]->type = $gps->gps_type;
								$DataToUpload[$z]->speed = $gps->gps_speed;
								$DataToUpload[$z]->direction = $gps->gps_course;
								$DataToUpload[$z]->fuel = $gps->gps_mvd;
								$DataToUpload[$z]->engine = $engine_bit;
								$DataToUpload[$z]->io2 = $io2_bit;
								$DataToUpload[$z]->gpsstatus = $gps->gps_status;
								$DataToUpload[$z]->gpstime = $gpstime_wta;
								$DataToUpload[$z]->latitude = $gps->gps_latitude_real;
								$DataToUpload[$z]->longitude = $gps->gps_longitude_real;
								$DataToUpload[$z]->odometer = $gps->gps_odometer;


								printf("GET LAST POSITION \r\n");

								$token = $mytoken;
								$authorization = "token: Bearer ".$token;
								$url = "https://api.ugems.id/gpsdatapush/send_data";

								$content = json_encode(array("data" => $DataToUpload));


					}


				$j++;
			}

			//printf("Data JSON : %s \r \n",$content);

								$curl = curl_init($url);
								curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
								curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
								curl_setopt($curl, CURLOPT_HEADER, false);
								curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
								curl_setopt($curl, CURLOPT_POST, true);
								curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
								curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

								$result = curl_exec($curl);

								echo $result;
								echo curl_getinfo($curl, CURLINFO_HTTP_CODE);

								// Get the POST request header status
								//$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

								// If header status is not Created or not OK, return error message
								/* if ( $status !== 201 || $status !== 200 ) {
								   die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
								} */
								printf("-------------------------- \r\n");


			$this->db->close();
			$this->db->cache_delete_all();

		}

		$finishdate = date("Y-m-d H:i:s");
		printf("===FINISH . . . %s to %s \r\n", $nowdate, $finishdate);
		printf("======================================\r\n");


		exit;
	}

	function getgpsoffline()
	{
		//ini_set('display_errors', 1);
		ini_set('memory_limit', "512M");
		//ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allcompany = 0;
		$now = date("Ymd");
		$payload = "";
		$dbtable = "report_gps_status_historikal";

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

		if(!isset($postdata->CompanyId) || $postdata->CompanyId == "all")
		{
			$allcompany = 1;
		}

		if(!isset($postdata->CompanyId) || $postdata->CompanyId == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Company ID!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			$check_company = strpos($postdata->CompanyId,';');
			$ex_company = explode(";",$postdata->CompanyId);
			$UserIDBIB = 4408;


			$this->db->order_by("company_name","asc");
			if($allcompany == 0){
				$this->db->where_in("company_id",$ex_company);
			}
			$this->db->where("company_flag",0);

			$q = $this->db->get("company");
			$data = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "CompanyId Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

				 $payload      		= array(
				  "UserId"          => $postdata->UserId,
				  "CompanyId"   	=> $postdata->CompanyId,
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
		}

		//jika mobil lebih dari nol
		if(count($data) > 0)
		{
			$DataToUpload = array();

			for($z=0;$z<count($data);$z++)
			{


				$companyid = $data[$z]->company_id;

					$rows = $this->getgpsoffline_data($dbtable, $allcompany, $companyid, $sdate, $edate);

					if(isset($rows) && count($rows)>0)
					{

						for($i=0;$i<count($rows);$i++)
						{

								$DataToUpload[$i]->VehicleNo = $rows[$i]->gpsoffline_vehicle_no;
								$DataToUpload[$i]->VehicleName = $rows[$i]->gpsoffline_vehicle_name;
								$DataToUpload[$i]->VehicleDevice = $rows[$i]->gpsoffline_vehicle_device;
								$DataToUpload[$i]->CompanyId = $rows[$i]->gpsoffline_vehicle_companyid;
								$DataToUpload[$i]->CompanyName = $rows[$i]->gpsoffline_vehicle_companyname;
								$DataToUpload[$i]->GpsLastUpdated = $rows[$i]->gpsoffline_lastupdate;
								$DataToUpload[$i]->GpsStatus = $rows[$i]->gpsoffline_status;
								$DataToUpload[$i]->LastChecked = $rows[$i]->gpsoffline_data_submited;

						}

					}

			}

			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API GPS Offline",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}

	function getgpsoffline_data($dbtable, $companyall, $company, $sdate, $edate)
	{
		$this->dbts = $this->load->database("tensor_report", true);
		$this->dbts->where("gpsoffline_data_submited >=", $sdate);
		$this->dbts->where("gpsoffline_data_submited <=", $edate);
		if($companyall != 1){
			$this->dbts->where("gpsoffline_vehicle_companyid", $company);
		}
		$this->dbts->where("gpsoffline_status", "OFFLINE");
		$this->dbts->order_by("gpsoffline_data_submited", "asc");

		$q = $this->dbts->get($dbtable);
		$rows = $q->result();

		$this->dbts->close();
		$this->dbts->cache_delete_all();
		return $rows;
	}

	function getlastposition_fromDBLive($imei,$dblive)
	{

		$this->dblive = $this->load->database($dblive,true);

		$this->dblive->order_by("gps_time", "desc");
		$this->dblive->where("gps_name", $imei);
		$this->dblive->limit(1);
		$qpost = $this->dblive->get("gps");
		$rowpost = $qpost->row();

		$this->dblive->close();
		$this->dblive->cache_delete_all();

		return $rowpost;

	}
	
	function getlastalert($vdevice,$dblive)
	{
		$nowdate = date("Y-m-d");
		$dboverspeed = "";
		$report     = "historikal_violation_";
		$overspeed  = "overspeed_hour_";
		
		$month = date("F", strtotime($nowdate));
		$year = date("Y", strtotime($nowdate));
			
		switch ($month) 
		{
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
		
		//get alert textmode
		$this->dbreport = $this->load->database("tensor_report", true);
		$this->dbreport->select("*");
		$this->dbreport->where("violation_vehicle_device", $vdevice);
		$this->dbreport->order_by("violation_update", "DESC");
		$this->dbreport->limit(1);
		$qalert = $this->dbreport->get($dbtable);
		$rowalert = $qalert->row();
	
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();

		return $rowalert;

	}
	
	function getlastoverspeed($vdevice,$dblive)
	{
		$nowdate = date("Y-m-d");
		$dboverspeed = "";
		$report     = "historikal_violation_";
		$overspeed  = "overspeed_hour_";
		
		$month = date("F", strtotime($nowdate));
		$year = date("Y", strtotime($nowdate));
			
		switch ($month) 
		{
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
		
		//get overspeed alert
		$this->dbreport = $this->load->database("tensor_report",true);
		$this->dbreport->select("*");
		$this->dbreport->where("overspeed_report_vehicle_device", $vdevice);
		$this->dbreport->where("overspeed_report_speed_status", 1);
		$this->dbreport->order_by("overspeed_report_gps_time", "DESC");
		$this->dbreport->limit(1);
		$qspeed = $this->dbreport->get($dboverspeed);
		$rowspeed = $qspeed->row();
	
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();

		return $rowspeed;

	}

	function array_sort($array, $on, $order=SORT_ASC)
	{

		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}

	function verify_time_format($value)
	{
		$pattern1 = '/^(0?\d|1\d|2[0-3]):[0-5]\d:[0-5]\d$/';
		$pattern2 = '/^(0?\d|1[0-2]):[0-5]\d\s(am|pm)$/i';
		return preg_match ($pattern1, $value) || preg_match ($pattern2, $value);
	}

	function strposa($haystack, $needles=array(), $offset=0)
	{
        $chr = array();
        foreach($needles as $needle) {
                $res = strpos($haystack, $needle, $offset);
                if ($res !== false) $chr[$needle] = $res;
        }
        if(empty($chr)) return false;
        return min($chr);
	}

	function getlaststatus()
	{
		//ini_set('display_errors', 1);
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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

		if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "all")
		{
			$allvehicle = 1;
		}


		if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Vehicle Device!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			//$check_vehicle = strpos($postdata->VehicleDevice,';');
			$UserIDBIB = 4408;

			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			$this->db->select("vehicle_device,vehicle_dbname_live,vehicle_user_id,vehicle_info");
			$this->db->where("vehicle_device",$postdata->VehicleDevice);
			//$this->db->where("vehicle_user_id",$UserIDBIB); //sementara diopen untuk all user
			$this->db->where("vehicle_status",1);

			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "Device Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "VehicleDevice"   => $postdata->VehicleDevice,

				);

			}
		}


		//jika mobil lebih dari nol
		if(count($vehicle) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($vehicle);$z++)
			{

				$devices = explode("@", $vehicle[$z]->vehicle_device);
				$vehicle_dblive = $vehicle[$z]->vehicle_dbname_live;
				$vehicle_imei = $devices[0];

					$gps = $this->getlastposition_fromDBLive($vehicle_imei,$vehicle_dblive);

					if(isset($gps) && count($gps)>0)
					{
						$datajson = json_decode($gps->vehicle_autocheck);
						$speed_kph = $datajson->auto_last_speed;

								if($speed_kph > 1 ){
									$engine = "ON";
								}else{
									$engine = $datajson->auto_last_engine;
								}

								if($engine == 'ON'){
									$engine_bit = 1;
								}else{
									$engine_bit = 0;
								}

								if($speed_kph > 1 ){
									$engine_bit = 1;
								}

								if($gps->gps_cs == 53){
									$io2_bit = 1;
								}else{
									$io2_bit = 0;
								}
								$street_name = "-";
								$geofence_name = "-";

								$position = $this->getPosition_other($gps->gps_longitude_real,$gps->gps_latitude_real);
								if(isset($position)){
									$ex_position = explode(",",$position->display_name);
									if(count($ex_position)>0){
										$street_name = $ex_position[0];
									}else{
										$street_name = $ex_position[0];
									}
								}else{
									$street_name = $position->display_name;
								}


								$geofence = $this->getGeofence_location_other($gps->gps_longitude_real,$gps->gps_latitude_real,$vehicle[$z]->vehicle_user_id);
								if($geofence){
									$geofence_name = $geofence;
								}

								$gpstime_wta = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($gps->gps_time))); //wita

								$DataToUpload[$z]->Name = $gps->gps_name;
								$DataToUpload[$z]->Host = $gps->gps_host;
								$DataToUpload[$z]->Type = $gps->gps_type;
								$DataToUpload[$z]->Speed = $speed_kph;
								$DataToUpload[$z]->Direction = $gps->gps_course;
								$DataToUpload[$z]->Fuel = $gps->gps_mvd;
								$DataToUpload[$z]->Engine = $engine_bit;
								$DataToUpload[$z]->Io2 = $io2_bit;
								$DataToUpload[$z]->GpsStatus = $gps->gps_status;
								$DataToUpload[$z]->GpsTime = $gpstime_wta;
								$DataToUpload[$z]->Latitude = $gps->gps_latitude_real;
								$DataToUpload[$z]->Longitude = $gps->gps_longitude_real;
								$DataToUpload[$z]->Odometer = $gps->gps_odometer;
								$DataToUpload[$z]->StreetName = $street_name;
								$DataToUpload[$z]->GeofenceName = $geofence_name;

					}

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			//$this->insertHitAPI("API Get Last Status",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}
	
	function getlaststatusv2()
	{
		//ini_set('display_errors', 1);
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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

		if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "all")
		{
			$allvehicle = 1;
		}
		


		
			$UserIDBIB = 4408;
			$this->db->order_by("vehicle_id","desc");
			$this->db->select("vehicle_device,vehicle_dbname_live,vehicle_user_id,vehicle_info");
			
			if($postdata->VehicleNo != "" && $postdata->VehicleDevice != "") {
				$this->db->where("vehicle_no",$postdata->VehicleNo);
				$this->db->where("vehicle_device",$postdata->VehicleDevice);
			}elseif ($postdata->VehicleNo != "" && $postdata->VehicleDevice == "") {
				$this->db->where("vehicle_no",$postdata->VehicleNo);
			}elseif ($postdata->VehicleNo == "" && $postdata->VehicleDevice != "") {
				$this->db->where("vehicle_device",$postdata->VehicleDevice);
			}else{
				$feature["code"] = 400;
				$feature["msg"] = "Vehicle Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}
			//$this->db->where("vehicle_user_id",$UserIDBIB); //sementara diopen untuk all user
			$this->db->where("vehicle_status",1); //only active

			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "Device Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "VehicleDevice"   => $postdata->VehicleDevice,
				  "VehicleNo"   => $postdata->VehicleNo,

				);

			}
		


		//jika mobil lebih dari nol
		if(count($vehicle) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($vehicle);$z++)
			{

				$devices = explode("@", $vehicle[$z]->vehicle_device);
				$vehicle_dblive = $vehicle[$z]->vehicle_dbname_live;
				$vehicle_imei = $devices[0];

					$gps = $this->getlastposition_fromDBLive($vehicle_imei,$vehicle_dblive);

					if(isset($gps) && count($gps)>0)
					{
						$datajson = json_decode($gps->vehicle_autocheck);
						$speed_kph = $datajson->auto_last_speed;

								if($speed_kph > 1 ){
									$engine = "ON";
								}else{
									$engine = $datajson->auto_last_engine;
								}

								if($engine == 'ON'){
									$engine_bit = 1;
								}else{
									$engine_bit = 0;
								}

								if($speed_kph > 1 ){
									$engine_bit = 1;
								}

								if($gps->gps_cs == 53){
									$io2_bit = 1;
								}else{
									$io2_bit = 0;
								}
								$street_name = "-";
								$geofence_name = "-";

								$position = $this->getPosition_other($gps->gps_longitude_real,$gps->gps_latitude_real);
								if(isset($position)){
									$ex_position = explode(",",$position->display_name);
									if(count($ex_position)>0){
										$street_name = $ex_position[0];
									}else{
										$street_name = $ex_position[0];
									}
								}else{
									$street_name = $position->display_name;
								}


								$geofence = $this->getGeofence_location_other($gps->gps_longitude_real,$gps->gps_latitude_real,$vehicle[$z]->vehicle_user_id);
								if($geofence){
									$geofence_name = $geofence;
								}

								$gpstime_wta = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($gps->gps_time))); //wita

								$DataToUpload[$z]->Name = $gps->gps_name;
								$DataToUpload[$z]->Host = $gps->gps_host;
								$DataToUpload[$z]->Type = $gps->gps_type;
								$DataToUpload[$z]->Speed = $speed_kph;
								$DataToUpload[$z]->Direction = $gps->gps_course;
								$DataToUpload[$z]->Fuel = $gps->gps_mvd;
								$DataToUpload[$z]->Engine = $engine_bit;
								$DataToUpload[$z]->Io2 = $io2_bit;
								$DataToUpload[$z]->GpsStatus = $gps->gps_status;
								$DataToUpload[$z]->GpsTime = $gpstime_wta;
								$DataToUpload[$z]->Latitude = $gps->gps_latitude_real;
								$DataToUpload[$z]->Longitude = $gps->gps_longitude_real;
								$DataToUpload[$z]->Odometer = $gps->gps_odometer;
								$DataToUpload[$z]->StreetName = $street_name;
								$DataToUpload[$z]->GeofenceName = $geofence_name;

					}

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			//$this->insertHitAPI("API Get Last Status",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}
	
	function getlaststatusv3()
	{
		//ini_set('display_errors', 1);
		ini_set('memory_limit', "2G");
		ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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

		if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "all")
		{
			$allvehicle = 1;
		}
		


		
			$UserIDBIB = 4408;
			$this->db->order_by("vehicle_id","desc");
			$this->db->select("vehicle_device,vehicle_dbname_live,vehicle_user_id,vehicle_info");
			
			if($postdata->VehicleNo != "" && $postdata->VehicleDevice != "") {
				$this->db->where("vehicle_no",$postdata->VehicleNo);
				$this->db->where("vehicle_device",$postdata->VehicleDevice);
			}elseif ($postdata->VehicleNo != "" && $postdata->VehicleDevice == "") {
				$this->db->where("vehicle_no",$postdata->VehicleNo);
			}elseif ($postdata->VehicleNo == "" && $postdata->VehicleDevice != "") {
				$this->db->where("vehicle_device",$postdata->VehicleDevice);
			}else{
				$feature["code"] = 400;
				$feature["msg"] = "Vehicle Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}
			//$this->db->where("vehicle_user_id",$UserIDBIB); //sementara diopen untuk all user
			$this->db->where("vehicle_status",1); //only active

			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "Device Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "VehicleDevice"   => $postdata->VehicleDevice,
				  "VehicleNo"   => $postdata->VehicleNo,

				);

			}
		


		//jika mobil lebih dari nol
		if(count($vehicle) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($vehicle);$z++)
			{

				$devices = explode("@", $vehicle[$z]->vehicle_device);
				$vehicle_dblive = $vehicle[$z]->vehicle_dbname_live;
				$vehicle_imei = $devices[0];

					$gps = $this->getlastposition_fromDBLive($vehicle_imei,$vehicle_dblive);
					
					//get violation historical
					$alert = $this->getlastalert($vehicle[$z]->vehicle_device,$vehicle_dblive);
					
					
					if(isset($gps) && count($gps)>0)
					{
						if(isset($alert) && count($alert)>0)
						{
							//$alert_vehicle_no = $alert->violation_vehicle_no;
							$alert_type_name = $alert->violation_type_name;
							$alert_position = $alert->violation_position;
							$alert_time = $alert->violation_update;
							$alert_speed = "";
						}else{
							
							//$alert_vehicle_no = "";
							$alert_type_name = "";
							$alert_position = "";
							$alert_time = "";
							$alert_speed = "";
						}
						
						$datajson = json_decode($gps->vehicle_autocheck);
						$speed_kph = $datajson->auto_last_speed;

								if($speed_kph > 1 ){
									$engine = "ON";
								}else{
									$engine = $datajson->auto_last_engine;
								}

								if($engine == 'ON'){
									$engine_bit = 1;
								}else{
									$engine_bit = 0;
								}

								if($speed_kph > 1 ){
									$engine_bit = 1;
								}

								if($gps->gps_cs == 53){
									$io2_bit = 1;
								}else{
									$io2_bit = 0;
								}
								$street_name = "-";
								$geofence_name = "-";

								$position = $this->getPosition_other($gps->gps_longitude_real,$gps->gps_latitude_real);
								if(isset($position)){
									$ex_position = explode(",",$position->display_name);
									if(count($ex_position)>0){
										$street_name = $ex_position[0];
									}else{
										$street_name = $ex_position[0];
									}
								}else{
									$street_name = $position->display_name;
								}


								$geofence = $this->getGeofence_location_other($gps->gps_longitude_real,$gps->gps_latitude_real,$vehicle[$z]->vehicle_user_id);
								if($geofence){
									$geofence_name = $geofence;
								}

								$gpstime_wta = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($gps->gps_time))); //wita
								
								

								$DataToUpload[$z]->Name = $gps->gps_name;
								$DataToUpload[$z]->Host = $gps->gps_host;
								$DataToUpload[$z]->Type = $gps->gps_type;
								$DataToUpload[$z]->Speed = $speed_kph;
								$DataToUpload[$z]->Direction = $gps->gps_course;
								$DataToUpload[$z]->Fuel = $gps->gps_mvd;
								$DataToUpload[$z]->Engine = $engine_bit;
								$DataToUpload[$z]->Io2 = $io2_bit;
								$DataToUpload[$z]->GpsStatus = $gps->gps_status;
								$DataToUpload[$z]->GpsTime = $gpstime_wta;
								$DataToUpload[$z]->Latitude = $gps->gps_latitude_real;
								$DataToUpload[$z]->Longitude = $gps->gps_longitude_real;
								$DataToUpload[$z]->Odometer = $gps->gps_odometer;
								$DataToUpload[$z]->StreetName = $street_name;
								$DataToUpload[$z]->GeofenceName = $geofence_name;
								
								//$DataToUpload[$z]->AlertVehicleNo = $alert_vehicle_no;
								$DataToUpload[$z]->AlertName = $alert_type_name;
								$DataToUpload[$z]->AlertTime = $alert_time;
								$DataToUpload[$z]->AlertPosition = $alert_position;
								$DataToUpload[$z]->AlertSpeed = $alert_speed;

					}

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			//$this->insertHitAPI("API Get Last Status",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}
	
	function getvehicledetail()
	{
		//ini_set('display_errors', 1);
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
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

		if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "all")
		{
			$allvehicle = 1;
		}

		/* if(!isset($postdata->CompanyId) || $postdata->CompanyId == "all")
		{
			$allcompany = 1;
		} */

		if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Vehicle Device!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			$check_vehicle = strpos($postdata->VehicleDevice,';');
			$ex_vehicle = explode(";",$postdata->VehicleDevice);
			$UserIDBIB = 4408;

			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			if($allvehicle == 0){
				$this->db->where_in("vehicle_device",$ex_vehicle);
			}
			/* if($allcompany == 0){
				$this->db->where("vehicle_company",$postdata->CompanyId);
			} */
			$this->db->where("vehicle_user_id",$UserIDBIB);
			$this->db->where("vehicle_status",1);
			//$this->db->where("vehicle_active_date2 >",$now); //tidak expired
			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "Device Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

				$payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "VehicleDevice"   	=> $postdata->VehicleDevice
				  //"CompanyId"   	=> $postdata->CompanyId


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

				//printf("ATTR %s \r\n",$vehicle[$z]->vehicle_no);

								$DataToUpload[$i]->VehicleId = $vehicle[$i]->vehicle_id;
								$DataToUpload[$i]->VehicleUserID = $vehicle[$i]->vehicle_user_id;
								$DataToUpload[$i]->VehicleDevice = $vehicle[$i]->vehicle_device;
								$DataToUpload[$i]->VehicleNo = $vehicle[$i]->vehicle_no;
								$DataToUpload[$i]->VehicleNoBackup = $vehicle[$i]->vehicle_no_bk;
								$DataToUpload[$i]->VehicleName = $vehicle[$i]->vehicle_name;

								$DataToUpload[$i]->VehicleCardNo = $vehicle[$i]->vehicle_card_no;
								$DataToUpload[$i]->VehicleOperator = $vehicle[$i]->vehicle_operator;
								$DataToUpload[$i]->VehicleStatus = $vehicle[$i]->vehicle_status;

								$DataToUpload[$i]->VehicleImage = $vehicle[$i]->vehicle_image;
								$DataToUpload[$i]->VehicleCreatedDate = $vehicle[$i]->vehicle_created_date;
								$DataToUpload[$i]->VehicleType = $vehicle[$i]->vehicle_type;

								$DataToUpload[$i]->VehicleCompany = $vehicle[$i]->vehicle_company;
								$DataToUpload[$i]->VehicleSubCompany = $vehicle[$i]->vehicle_subcompany;
								$DataToUpload[$i]->VehicleGroup = $vehicle[$i]->vehicle_group;
								$DataToUpload[$i]->VehicleSubGroup = $vehicle[$i]->vehicle_subgroup;

								$DataToUpload[$i]->VehicleTanggalPasang = $vehicle[$i]->vehicle_tanggal_pasang;
								$DataToUpload[$i]->VehicleImei = $vehicle[$i]->vehicle_imei;
								$DataToUpload[$i]->VehicleMV03 = $vehicle[$i]->vehicle_mv03;
								$DataToUpload[$i]->VehicleSensor = $vehicle[$i]->vehicle_sensor;
								$DataToUpload[$i]->VehicleSOS = $vehicle[$i]->vehicle_sos;

								$DataToUpload[$i]->VehiclePortalRangka = $vehicle[$i]->vehicle_portal_rangka;
								$DataToUpload[$i]->VehiclePortalMesin = $vehicle[$i]->vehicle_portal_mesin;
								$DataToUpload[$i]->VehiclePortalRfidSPI = $vehicle[$i]->vehicle_portal_rfid_spi;
								$DataToUpload[$i]->VehiclePortalRfidWIM = $vehicle[$i]->vehicle_portal_rfid_wim;
								$DataToUpload[$i]->VehiclePortalPortalTare = $vehicle[$i]->vehicle_portal_tare;

								$DataToUpload[$i]->VehiclePortTime = $vehicle[$i]->vehicle_port_time;
								$DataToUpload[$i]->VehiclePortName = $vehicle[$i]->vehicle_port_name;
								$DataToUpload[$i]->VehicleRomTime = $vehicle[$i]->vehicle_rom_time;
								$DataToUpload[$i]->VehicleRomName = $vehicle[$i]->vehicle_rom_name;
								$DataToUpload[$i]->VehicleAutoCheck = json_decode($vehicle[$i]->vehicle_autocheck);

								//$datajson["Data"] = $DataToUpload;




			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Master Vehicle Detail",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}

	function wind_cardinal($degree)
	{

            switch( $degree ) {

                case ( $degree >= 348.75 && $degree <= 360 ):
                    $cardinal = "N";
                break;

                case ( $degree >= 0 && $degree <= 11.249 ):
                    $cardinal = "N";
                break;

                case ( $degree >= 11.25 && $degree <= 33.749 ):
                    $cardinal = "NNE";
                break;

                case ( $degree >= 33.75 && $degree <= 56.249 ):
                    $cardinal = "NE";
                break;

                case ( $degree >= 56.25 && $degree <= 78.749 ):
                    $cardinal = "ENE";
                break;

                case ( $degree >= 78.75 && $degree <= 101.249 ):
                    $cardinal = "E";
                break;

                case ( $degree >= 101.25 && $degree <= 123.749 ):
                    $cardinal = "ESE";
                break;

                case ( $degree >= 123.75 && $degree <= 146.249 ):
                    $cardinal = "SE";
                break;

                case ( $degree >= 146.25 && $degree <= 168.749 ):
                    $cardinal = "N";
                break;

                case ( $degree >= 168.75 && $degree <= 191.249 ):
                    $cardinal = "S";
                break;

                case ( $degree >= 191.25 && $degree <= 213.749 ):
                    $cardinal = "SSW";
                break;

                case ( $degree >= 213.75 && $degree <= 236.249 ):
                    $cardinal = "SW";
                break;

                case ( $degree >= 236.25 && $degree <= 258.749 ):
                    $cardinal = "WSW";
                break;

                case ( $degree >= 258.75 && $degree <= 281.249 ):
                    $cardinal = "W";
                break;

                case ( $degree >= 281.25 && $degree <= 303.749 ):
                    $cardinal = "WNW";
                break;

                case ( $degree >= 303.75 && $degree <= 326.249 ):
                    $cardinal = "NW";
                break;

                case ( $degree >= 326.25 && $degree <= 348.749 ):
                    $cardinal = "NNW";
                break;

                default:
                    $cardinal = null;

            }

           return $cardinal;

    }
	
	//NEW OBU API
	function getdriverdetectedbydevice()
	{
		ini_set('display_errors', 1);
		//ini_set('memory_limit', "2G");
		//ini_set('max_execution_time', 60); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allcompany = 0;
		$now = date("Ymd");
		$payload = "";
		$dbtable = "ts_driver_change_new";

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
			$allcompany = 1;
		}

		if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Vehicle No!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			$this->db->order_by("vehicle_id","desc");
			$this->db->where("vehicle_no",$postdata->VehicleNo);
			$this->db->where("vehicle_status <>",3);
			$q = $this->db->get("vehicle");
			$data = $q->result();

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
				  "VehicleNo"  	 	=> $postdata->VehicleNo
				
				);

			}
		}

		//jika mobil lebih dari nol
		if(count($data) > 0)
		{
			$DataToUpload = array();

			for($z=0;$z<count($data);$z++)
			{


				$vehicle_imeicam = $data[$z]->vehicle_mv03;

					$rows = $this->getdriverdetected_bydevice_data($dbtable, $vehicle_imeicam);

					if(isset($rows) && count($rows)>0)
					{
						for($i=0;$i<count($rows);$i++)
						{
						
								$DataToUpload[$i]->VehicleNo = $rows[$i]->change_driver_vehicle_no;
								$DataToUpload[$i]->CompanyId = $rows[$i]->change_driver_company;
								$DataToUpload[$i]->CompanyName = $rows[$i]->change_driver_company_name;
								$DataToUpload[$i]->ImeiCam = $rows[$i]->change_imei;
								$DataToUpload[$i]->DriverIdSimper = $rows[$i]->change_driver_id;
								$DataToUpload[$i]->DriverName = $rows[$i]->change_driver_name;
								$DataToUpload[$i]->DriverDetected = $rows[$i]->change_driver_time;
						}
						
					}

			}

			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Driver Detected by Device",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}
	
	function getdriverdetected_bydevice_data($dbtable, $vcamimei)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->where("change_imei", $vcamimei);
		$this->dbts->where("change_driver_flag", 0);
		$this->dbts->order_by("change_driver_time", "desc");
		$this->dbts->limit(1);
		$q = $this->dbts->get($dbtable);
		$rows = $q->result();

		$this->dbts->close();
		$this->dbts->cache_delete_all();
		return $rows;
	}
	
	function getlaststatusv4()
	{
		//ini_set('display_errors', 1);
		//ini_set('memory_limit', "2G");
		//ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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

		if(!isset($postdata->VehicleDevice) || $postdata->VehicleDevice == "all")
		{
			$allvehicle = 1;
		}

			$UserIDBIB = 4408;
			$this->db->order_by("vehicle_id","desc");
			$this->db->select("vehicle_device,vehicle_mv03,vehicle_dbname_live,vehicle_user_id,vehicle_info");
			
			if($postdata->VehicleNo != "" && $postdata->VehicleDevice != "") {
				$this->db->where("vehicle_no",$postdata->VehicleNo);
				$this->db->where("vehicle_device",$postdata->VehicleDevice);
			}elseif ($postdata->VehicleNo != "" && $postdata->VehicleDevice == "") {
				$this->db->where("vehicle_no",$postdata->VehicleNo);
			}elseif ($postdata->VehicleNo == "" && $postdata->VehicleDevice != "") {
				$this->db->where("vehicle_device",$postdata->VehicleDevice);
			}else{
				$feature["code"] = 400;
				$feature["msg"] = "Vehicle Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}
			//$this->db->where("vehicle_user_id",$UserIDBIB); //sementara diopen untuk all user
			$this->db->where("vehicle_status",1); //only active

			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "Device Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "VehicleDevice"   => $postdata->VehicleDevice,
				  "VehicleNo"   => $postdata->VehicleNo,

				);

			}
		


		//jika mobil lebih dari nol
		if(count($vehicle) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($vehicle);$z++)
			{

				$devices = explode("@", $vehicle[$z]->vehicle_device);
				$vehicle_dblive = $vehicle[$z]->vehicle_dbname_live;
				$vehicle_imei = $devices[0];

					$gps = $this->getlastposition_fromDBLive($vehicle_imei,$vehicle_dblive);
					
					//get violation historical
					$alert = $this->getlastalertv4($vehicle[$z]->vehicle_mv03,$vehicle_dblive);
					
					
					if(isset($gps) && count($gps)>0)
					{
						if(isset($alert) && count($alert)>0)
						{
							//$alert_vehicle_no = $alert->violation_vehicle_no;
							$alert_type_name = $alert->violation_type_name;
							$alert_position = $alert->violation_position;
							$alert_time = $alert->violation_update;
							$alert_speed = "";
						}else{
							
							//$alert_vehicle_no = "";
							$alert_type_name = "";
							$alert_position = "";
							$alert_time = "";
							$alert_speed = "";
						}
						
						$datajson = json_decode($gps->vehicle_autocheck);
						$speed_kph = $datajson->auto_last_speed;

								if($speed_kph > 1 ){
									$engine = "ON";
								}else{
									$engine = $datajson->auto_last_engine;
								}

								if($engine == 'ON'){
									$engine_bit = 1;
								}else{
									$engine_bit = 0;
								}

								if($speed_kph > 1 ){
									$engine_bit = 1;
								}

								if($gps->gps_cs == 53){
									$io2_bit = 1;
								}else{
									$io2_bit = 0;
								}
								$street_name = "-";
								$geofence_name = "-";

								$position = $this->getPosition_other($gps->gps_longitude_real,$gps->gps_latitude_real);
								if(isset($position)){
									$ex_position = explode(",",$position->display_name);
									if(count($ex_position)>0){
										$street_name = $ex_position[0];
									}else{
										$street_name = $ex_position[0];
									}
								}else{
									$street_name = $position->display_name;
								}


								$geofence = $this->getGeofence_location_other($gps->gps_longitude_real,$gps->gps_latitude_real,$vehicle[$z]->vehicle_user_id);
								if($geofence){
									$geofence_name = $geofence;
								}

								$gpstime_wta = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($gps->gps_time))); //wita
								
								

								$DataToUpload[$z]->Name = $gps->gps_name;
								$DataToUpload[$z]->Host = $gps->gps_host;
								$DataToUpload[$z]->Type = $gps->gps_type;
								$DataToUpload[$z]->Speed = $speed_kph;
								$DataToUpload[$z]->Direction = $gps->gps_course;
								$DataToUpload[$z]->Fuel = $gps->gps_mvd;
								$DataToUpload[$z]->Engine = $engine_bit;
								$DataToUpload[$z]->Io2 = $io2_bit;
								$DataToUpload[$z]->GpsStatus = $gps->gps_status;
								$DataToUpload[$z]->GpsTime = $gpstime_wta;
								$DataToUpload[$z]->Latitude = $gps->gps_latitude_real;
								$DataToUpload[$z]->Longitude = $gps->gps_longitude_real;
								$DataToUpload[$z]->Odometer = $gps->gps_odometer;
								$DataToUpload[$z]->StreetName = $street_name;
								$DataToUpload[$z]->GeofenceName = $geofence_name;
								
								//$DataToUpload[$z]->AlertVehicleNo = $alert_vehicle_no;
								$DataToUpload[$z]->AlertName = $alert_type_name;
								$DataToUpload[$z]->AlertTime = $alert_time;
								$DataToUpload[$z]->AlertPosition = $alert_position;
								$DataToUpload[$z]->AlertSpeed = $alert_speed;

					}

			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Get Last Status V4",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}
	
	function getlastalertv4($vcam,$dblive)
	{
		$nowdate = date("Y-m-d");
		$dboverspeed = "";
		$report     = "historikal_violation_";
		$overspeed  = "overspeed_hour_";
		
		$month = date("F", strtotime($nowdate));
		$year = date("Y", strtotime($nowdate));
			
		switch ($month) 
		{
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
		
		//get alert textmode
		$this->dbreport = $this->load->database("tensor_report", true);
		$this->dbreport->select("*");
		$this->dbreport->where("violation_vehicle_mv03", $vcam);
		$this->dbreport->order_by("violation_update", "DESC");
		$this->dbreport->limit(1);
		$qalert = $this->dbreport->get($dbtable);
		$rowalert = $qalert->row();
	
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();

		return $rowalert;

	}
	
	function getritasereportbydevice()
	{
		//ini_set('memory_limit', "2G");
		//ini_set('max_execution_time', 180); // 3 minutes
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

	 if(!isset($postdata->VehicleNo) || $postdata->VehicleNo == "")
	 {
		 $feature["code"]    = 400;
		 $feature["msg"]     = "Invalid Vehicle No";
		 $feature["payload"] = $payload;
		 echo json_encode($feature);
		 exit;
	 }else {
		 if($postdata->VehicleNo == "all"){
			$vehicle_all = 1;
			$rowvehicle = array();
		 }else{
			 $vehicle_all = 0;
			 $this->db->order_by("vehicle_id","desc");
			 $this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_device");
			 $this->db->where("vehicle_no",$postdata->VehicleNo);
			 $this->db->where("vehicle_user_id",4408);
			 $q = $this->db->get("vehicle");
			 $rowvehicle = $q->row();

			 if($q->num_rows == 0)
			 {
				 $feature["code"] = 400;
				 $feature["msg"] = "Vehicle Not Found!";
				 $feature["payload"]    = $payload;
				 echo json_encode($feature);
				 exit;
			 }
			 
		 }
		 
	 }

			$payload = array(
				 "UserId"            => $postdata->UserId,
				 "VehicleNo"         => $postdata->VehicleNo,
				 "StartTime"    	 => $postdata->StartTime,
				 "EndTime"    	 	 => $postdata->EndTime,


			);


	 $report        = "ritase_trial_"; // new report
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

		$data = $this->getdataritase_bydevice($rowvehicle,$vehicle_all,$sdate,$edate,$dbtable); 
		$total_rows = count($data);
		$datafix = array();
	 if($total_rows > 0)
	 {
		for ($i=0; $i < $total_rows; $i++)
		{	
			if($data[$i]['ritase_report_wim_start_time'] == "" || $data[$i]['ritase_report_wim_start_time'] == "0000-00-00 00:00:00"){
														
				$wimtime = "";
			}else{
														
				$wimtime = date("d-M-Y H:i", strtotime($data[$i]['ritase_report_wim_start_time']));
			}
													
			if($data[$i]['ritase_report_wim2_start_time'] == "" || $data[$i]['ritase_report_wim2_start_time'] == "0000-00-00 00:00:00"){
														
				$wimtime2 = "";
			}else{
														
				$wimtime2 = date("d-M-Y H:i", strtotime($data[$i]['ritase_report_wim2_start_time']));
			}
	
			array_push($datafix, array(
				"VehicleUserId"    => $data[$i]['ritase_report_vehicle_user_id'],
				"VehicleId"        => $data[$i]['ritase_report_vehicle_id'],
				"VehicleDevice"    => $data[$i]['ritase_report_vehicle_device'],
				"VehicleNo"        => $data[$i]['ritase_report_vehicle_no'],
				"VehicleName"      => $data[$i]['ritase_report_vehicle_name'],
				"VehicleType"      => $data[$i]['ritase_report_vehicle_type'],
				"VehicleCompany"   => $data[$i]['ritase_report_vehicle_company'],
				"VehicleImei"      => "",
				"ReportType"       => $data[$i]['ritase_report_type'],
				"ReportName"      	=> $data[$i]['ritase_report_name'],
				
				"RomName"     		=> $data[$i]['ritase_report_start_location'],
				"RomGpsTime"        => $data[$i]['ritase_report_start_time'],
				
				"PortName"          => $data[$i]['ritase_report_end_location'],
				"PortGpsTime"       => $data[$i]['ritase_report_end_time'],
				
				"GroupDate"      	=> $data[$i]['ritase_report_end_date'],
				"GroupHour"  		=> $data[$i]['ritase_report_end_hour'],
				
				"Duration"   		=> $data[$i]['ritase_report_duration'],
				"DurationSecond" 	=> $data[$i]['ritase_report_duration_sec'],
				
				"DriverId"   		=> $data[$i]['ritase_report_driver'],
				"DriverName"      	=> $data[$i]['ritase_report_driver_name'],
				
				"WimId"      		=> "",
				"WimNetto"     		=> "",
				
				"Wim1"      		=> $wimtime,
				"Wim2"     			=> $wimtime2,
				
				"TimeFormat"  		=> date("d-M-Y H:i", strtotime($data[$i]['ritase_report_end_time'])),
				"DateFormat"     	=> date("d-M-Y", strtotime($data[$i]['ritase_report_shift_date'])),
				"ShiftFormat"     	=> $data[$i]['ritase_report_shift_name']
				
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
	 $this->insertHitAPI("API Ritase Report by Device", $payload, $nowstarttime, $nowendtime);
	 $this->db->close();
	 $this->db->cache_delete_all();

	 exit;
	}

	function getdataritase_bydevice($rowvehicle,$all_status,$sdate,$edate,$dbtable)
	{
		//main data
		$reporttype = 0; //belum dipakai 
		$this->dbtrip = $this->load->database("tensor_report_testing", true); //next only tensor_report
		$this->dbtrip->order_by("ritase_report_end_time", "desc");
		$this->dbtrip->where("ritase_report_vehicle_no", "BMT 3070"); //sementara hardcode dahulu sampai job ritase ready
		//$this->dbtrip->where("ritase_report_vehicle_device", $rowvehicle->vehicle_device);
		$this->dbtrip->where("ritase_report_duration_sec >=", 100); // lebih dari 1 menit : 100 detik
		$this->dbtrip->where("ritase_report_duration_sec <=", 43200); // kurang dari 12 jam (1 shift)
		$this->dbtrip->where("ritase_report_end_time >=", $sdate);
		$this->dbtrip->where("ritase_report_end_time <=", $edate);
		$this->dbtrip->where("ritase_report_end_geofence !=", "PORT BBC");
		$this->dbtrip->where("ritase_report_end_geofence !=", "");
		$this->dbtrip->where("ritase_report_type", $reporttype); //data fix (default) = 0
		$q1 = $this->dbtrip->get($dbtable);
		$rows1 = $q1->result_array();
		$rows = $rows1;
		return $rows;
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
	}
	
	function getpairingdevice()
	{
		//ini_set('display_errors', 1);
		//ini_set('memory_limit', "2G");
		//ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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

		if(!isset($postdata->Device3Imei) || $postdata->Device3Imei == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Device #3 ID";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}

			$UserIDBIB = 4408;
			$this->db->order_by("vehicle_id","desc");
			$this->db->select("vehicle_id,vehicle_no,vehicle_name,vehicle_device,vehicle_mv03,vehicle_dbname_live");
			$this->db->where("vehicle_user_id",$UserIDBIB);
			$this->db->where("vehicle_device3_id",$postdata->Device3Imei); 
			$this->db->where("vehicle_status",1); //only active
			$this->db->limit(1);
			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "No Paired Devices!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();
				  $payload         = array(
				  "UserId"        => $postdata->UserId,
				  "Device3Imei"   => $postdata->Device3Imei
				);
			}
		
		//jika mobil lebih dari nol
		if(count($vehicle) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($vehicle);$z++)
			{
				$DataToUpload[$z]->VehicleId = $vehicle[$z]->vehicle_id;
				$DataToUpload[$z]->VehicleNo = $vehicle[$z]->vehicle_no;
				$DataToUpload[$z]->VehicleName = $vehicle[$z]->vehicle_name;
				$DataToUpload[$z]->VehicleGps = $vehicle[$z]->vehicle_device;
				$DataToUpload[$z]->VehicleMdvr = $vehicle[$z]->vehicle_mv03;
				$DataToUpload[$z]->VehicleDblive = $vehicle[$z]->vehicle_dbname_live;
				
			}

		}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Get Pairing Device",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

	


		exit;
	}
	
	function getmdvrlaststatusv1()
	{
		//ini_set('display_errors', 1);
		//ini_set('memory_limit', "2G");
		//ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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

		if(!isset($postdata->MDVRimei) || $postdata->MDVRimei == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid MDVR IMEI";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		else
		{
			$UserIDBIB = 4408;
			$this->db->order_by("vehicle_id","desc");
			$this->db->select("vehicle_device,vehicle_no,vehicle_company,vehicle_mv03,vehicle_dbname_live,vehicle_user_id,vehicle_info");
			$this->db->where("vehicle_mv03",$postdata->MDVRimei);
			//$this->db->where("vehicle_user_id",$UserIDBIB); //sementara diopen untuk all user
			$this->db->where("vehicle_status",1); //only active

			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "Device Not Found!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

				 $payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "MDVRimei"   => $postdata->MDVRimei,
				

				);

			}
			
		}


		//jika mobil lebih dari nol
		if(count($vehicle) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($vehicle);$z++)
			{
				$vehicle_mdvr_imei = $vehicle[$z]->vehicle_mv03;
				$vehicle_no = $vehicle[$z]->vehicle_no;
				$vehicle_company = $vehicle[$z]->vehicle_company;
				$vehicle_user_id = $vehicle[$z]->vehicle_user_id;
				
				//get one time token
				$session_id = $this->getOneTimetokenAPI("temanindobara","000000","4408");
				$mdvr_stats = $this->devicestatusapi($session_id,$vehicle_mdvr_imei,$vehicle_no,$vehicle_company);
				$online_stats = $this->deviceonlineapi($session_id,$vehicle_mdvr_imei,$vehicle_no,$vehicle_company);
				
					$status_value = 0;
					$status_name = "";
					$mdvr_imei = "";
					$mdvr_lat = "";
					$mdvr_lng = "";
					$mdvr_driver_id = "";
					$mdvr_driver_time = ""; 
					$mdvr_gps_time = "";
					$mdvr_position = "";
					$street_name = "";
					$geofence_name = "-";
					
					if($online_stats != ""){
						
						$status_value = $online_stats['online'];
						if($status_value == 1){
							$status_name = "online";
						}else{
							$status_name = "offline";
						}
					
					}
					
					if($mdvr_stats != ""){
						
						$mdvr_imei = $mdvr_stats['id'];
						$mdvr_lat = $mdvr_stats['mlat'];
						$mdvr_lng = $mdvr_stats['mlng'];
						$mdvr_driver_id = trim($mdvr_stats['driJn']);
						$mdvr_driver_time = date("Y-m-d H:i:s", strtotime($mdvr_stats['driSwStr'])); 
						$mdvr_gps_time = date("Y-m-d H:i:s", strtotime($mdvr_stats['gt'])); 
						
						$position = $this->getPosition_other($mdvr_lng,$mdvr_lat);
						if(isset($position)){
							$ex_position = explode(",",$position->display_name);
							if(count($ex_position)>0){
								$street_name = $ex_position[0];
							}else{
								$street_name = $ex_position[0];
							}
						}else{
							$street_name = $position->display_name;
						}

						$geofence = $this->getGeofence_location_other($mdvr_lng,$mdvr_lat,$vehicle_user_id);
						if($geofence){
							$geofence_name = $geofence;
						}
						
					}
		
					$DataToUpload[$z]->MdvrImei = $mdvr_imei;
					$DataToUpload[$z]->MdvrStatus = $status_name;
					$DataToUpload[$z]->MdvrGpsTime = $mdvr_gps_time;
					$DataToUpload[$z]->MdvrLatitude = $mdvr_lat;
					$DataToUpload[$z]->MdvrLongitude = $mdvr_lng;
					$DataToUpload[$z]->MdvrStreetName = $street_name;
					$DataToUpload[$z]->MdvrGeofenceName = $geofence_name;
			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Get MDVR Last Status V1",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}
	
	function devicestatusapi($sess_id,$imei,$vno,$vcompany)
	{

		$feature = array();
		$host = "MV03";
		$dataJson = file_get_contents("http://172.16.1.2/StandardApiAction_getDeviceStatus.action?jsession=".$sess_id."&devIdno=".$imei."&toMap=1&driver=0&language=zh"); //IP baru Local

		$data = json_decode($dataJson,true);
		$result = $data['result'];
		$status = $data['status'][0];

		if($result == 0)
		{
			$response = $status;
		}
		else
		{
			$response = "";
		}
		
		//print_r($status);exit();

		return $response;

	}
	
	function deviceonlineapi($sess_id,$imei,$vno,$vcompany)
	{

		$feature = array();
		$host = "MV03";
		$dataJson = file_get_contents("http://172.16.1.2/StandardApiAction_getDeviceOlStatus.action?jsession=".$sess_id."&devIdno=".$imei); //IP baru Local
									 
		$data = json_decode($dataJson,true);
		$result = $data['result'];
		$status = $data['onlines'][0];
	
		if($result == 0)
		{
			$response = $status;
		}
		else
		{
			$response = "";
		}
		
		//print_r($status);exit();

		return $response;

	}
	
	function getOneTimetokenAPI($username,$password,$userid)
	{

		$feature = array();

		$dataJson = file_get_contents("http://172.16.1.2/StandardApiAction_login.action?account=".$username."&password=".$password."");

		$data = json_decode($dataJson,true);
		$result = $data["result"];
		$response = "";
		if($result == 0){

			$session_id = $data["JSESSIONID"];
			//printf("===LOGIN SUCCESS: %s \r\n", $session_id);
		}else{
			$err_message = $data["message"];
			//printf("===LOGIN FAILED: %s \r\n", $err_message);
		}

		$response = $session_id;
		return $response;

	}
	
	function req_mdvrlaststatusv1()
	{
		printf("PROSES REQUEST LAST STATUS MDVR \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://poc.pilartech.co.id/obuapi/getmdvrlaststatusv1";
		$feature = array();

		$feature["UserId"] = 4212; //pbi
		$feature["MDVRimei"] = "653220230158";
		
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
	
	function clockin()
	{
		//ini_set('allow_url_fopen', 'On');
		header("Content-Type: application/json");
		
		//$token      = "BCW5kNGhN5QJOnA99fbv778JKPmo2k0dA16";
		$postdata   = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$now        = date("Ymd");
		$nowstarttime = date("Y-m-d H:i:s");

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
			$this->db->where("api_user",$postdata->UserId);
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
		if(!isset($postdata->UserId) || $postdata->UserId == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA PLATFORM USER ID";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->DriverId) || $postdata->DriverId == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA DRIVER ID (INTERNAL SYSTEM)";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->DriverName) || $postdata->DriverName == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Nama Driver tidak diketahui! ";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->DriverShiftType) || $postdata->DriverShiftType == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Silahkan Pilih Shift!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->DriverShiftDate) || $postdata->DriverShiftDate == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Silahkan Pilih Tanggal Shift!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->DriverIdCard) || $postdata->DriverIdCard == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "ID Card/Simper tidak diketahui!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->Device3Imei) || $postdata->Device3Imei == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA IMEI DEVICE 3";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->VehicleId) || $postdata->VehicleId == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA VEHICLE ID, PLEASE CHECK PAIRING DEVICE!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->DriverCoord) || $postdata->DriverCoord == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Posisi tidak diketahui, silahkan login ulang atau gunakan browser lain!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->DriverPhotoText) || $postdata->DriverPhotoText == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Foto Selfie tidak ada! Mohon Foto Selfie dengan latar no lambung yang digunakan";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		//end check kondisi mandatory
		
		
		//define to varibel
		$driver_id  = $postdata->DriverId;
		$driver_name = $postdata->DriverName;
		$driver_idcard = $postdata->DriverIdCard;
		$nowtime_wita = $postdata->ClockInTime;
		$shift_type = $postdata->DriverShiftType;
		$shift_date = $postdata->DriverShiftDate;
		$shift_time = $postdata->DriverShiftTime;
		$driver_coord = $postdata->DriverCoord;
		$driver_photo_text = $postdata->DriverPhotoText;
		$device3_imei =  $postdata->Device3Imei;
		$vehicle_id =  $postdata->VehicleId;
		
		
		$data       = array(
			 "absensi_driver_id"           => $driver_id,
			 "absensi_driver_name"         => $driver_name,
			 "absensi_driver_idcard"       => $driver_idcard,
			 "absensi_driver_time"         => $nowtime_wita,
			 "absensi_shift_type"          => $shift_type,
			 "absensi_shift_time"          => $shift_time,
			 "absensi_shift_date"          => $shift_date,
			 "absensi_clock_in"            => $nowtime_wita,
			 "absensi_clock_in_status"	   => 1, 
			 "absensi_clock_in_coord"      => $driver_coord,
			 "absensi_photo_txt"		   => $driver_photo_text,
			 "absensi_device3_imei"		   => $device3_imei,
			 //"absensi_vehicle_id"		   => $vehicle_id, (finalisasi flow)
			 "absensi_status"         	   => 1
		 

		);

		$payload       = array(
		  "UserId"         			  => $postdata->UserId,
		  "DriverId"         		  => $postdata->DriverId,
		  "DriverIdCard"         	  => $postdata->DriverIdCard,
		  "DriverName"    		      => $postdata->DriverName,
		  "Device3Imei"		 		  => $postdata->Device3Imei,
		  "DriverShiftType"	    	 	  => $postdata->DriverShiftType,
		  "DriverShiftDate"	    	 	  => $postdata->DriverShiftDate,
		  "DriverShiftTime"	    	 	  => $postdata->DriverShiftTime,
		  "ClockInTime"		  	      => $postdata->ClockInTime,
		  "VehicleId"		 		  => $postdata->VehicleId
    
    );

		
		$insert = $this->ts_insertData("ts_driver_absensi", $data);
        if ($insert) {
           echo json_encode(array("code" => 200, "msg" => "Berhasil Absen Jam Masuk.", "payload" => $payload));
        }else {
           echo json_encode(array("code" => 400, "msg" => "Anda Gagal Absen. Pastikan Koneksi Anda Stabil.", "payload" => $payload));
        }
		  
		$nowendtime = date("Y-m-d H:i:s");
		$this->insertHitAPI("API Clock In",$payload,$nowstarttime,$nowendtime);
		$this->db->close();
		$this->db->cache_delete_all();
		  

	}
	
	function ts_insertData($table, $data)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->close();
		return $this->dbts->insert($table, $data);
	}
	
	function testclockin()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$authorization = "Authorization:".$token;
		$url = "https://poc.pilartech.co.id/obuapi/clockin";
		$feature = array();

		$feature["UserId"] = 4212;
		$feature["DriverId"] = 3035;
		$feature["DriverIdCard"] = "038017";
		$feature["DriverName"] = "MUHAMMAD AKBAR";
		$feature["DriverShiftType"] = "Pagi";
		$feature["DriverShiftTime"] = "06:00-18:00";
		$feature["ClockInTime"] = date("Y-m-d H:i:s");
		$feature["DriverCoord"] = "-3.7120555,115.6452571";
		$feature["DriverPhotoText"] = "-data:image/jpeg;base64,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";
		$feature["Device3Imei"] = "3945e8a8704f04be";
		
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
	
	//LAST ABSENSI ID
	function getlastabsensi()
	{
		//ini_set('display_errors', 1);
		//ini_set('memory_limit', "2G");
		//ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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

		if(!isset($postdata->Device3Imei) || $postdata->Device3Imei == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Device #3 ID";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}

			$UserIDBIB = 4408;
			$this->dbts = $this->load->database('webtracking_ts', true);
			$this->dbts->order_by("absensi_id", "desc");
			$this->dbts->where("absensi_device3_imei", $postdata->Device3Imei);
			$this->dbts->where("absensi_clock_in_status", 1);//sudah absen (jika kosong maka dianggap sudah close absensi)
			$this->dbts->where("absensi_flag", 0);
			$this->dbts->limit(1);
			$q = $this->dbts->get("ts_driver_absensi");
			$absensi = $q->result();
			
			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "Tidak ada Absensi yang masih Aktif!";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();
				  $payload         = array(
				  "UserId"        => $postdata->UserId,
				  "Device3Imei"   => $postdata->Device3Imei
				);
			}
		
		//jika mobil lebih dari nol
		if(count($absensi) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($absensi);$z++)
			{
				$DataToUpload[$z]->AbsensiId = $absensi[$z]->absensi_id;
				$DataToUpload[$z]->AbsensiDriverId = $absensi[$z]->absensi_driver_id;
				$DataToUpload[$z]->AbsensiDriverName = $absensi[$z]->absensi_driver_name;
				$DataToUpload[$z]->AbsensiDriverIdCard = $absensi[$z]->absensi_driver_idcard;
				$DataToUpload[$z]->ShiftType = $absensi[$z]->absensi_shift_type;
				$DataToUpload[$z]->AbsensiStatus = $absensi[$z]->absensi_status;
				
				$DataToUpload[$z]->ShiftTime = $absensi[$z]->absensi_shift_time;
				$DataToUpload[$z]->ClockInTime = $absensi[$z]->absensi_clock_in;
				$DataToUpload[$z]->ClockInStatus = $absensi[$z]->absensi_clock_in_status;
				$DataToUpload[$z]->ClockInCoord = $absensi[$z]->absensi_clock_in_coord;
				$DataToUpload[$z]->ClockOutTime = $absensi[$z]->absensi_clock_out;
				$DataToUpload[$z]->ClockOutStatus = $absensi[$z]->absensi_clock_out_status;
				
				$DataToUpload[$z]->VehicleId = $absensi[$z]->absensi_vehicle_id;
				$DataToUpload[$z]->VehicleNo = $absensi[$z]->absensi_vehicle_no;
				$DataToUpload[$z]->GpsImei = $absensi[$z]->absensi_vehicle_device;
				$DataToUpload[$z]->MdvrImei = $absensi[$z]->absensi_vehicle_mv03;
				$DataToUpload[$z]->FaceDetected = $absensi[$z]->absensi_face_detected;
				$DataToUpload[$z]->Device3Imei = $absensi[$z]->absensi_device3_imei;
				$DataToUpload[$z]->AbsensiPhotoText = $absensi[$z]->absensi_photo_txt;
				
				
			}

		}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Get Last Absensi",$payload,$nowstarttime,$nowendtime);
			$this->dbts->close();
			$this->dbts->cache_delete_all();

	


		exit;
	}
	
	//CLOCK OUT
	function clockout()
	{
		
		header("Content-Type: application/json");
		
		$postdata   = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$now        = date("Ymd");
		$nowstarttime = date("Y-m-d H:i:s");

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
			$this->db->where("api_user",$postdata->UserId);
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
		if(!isset($postdata->UserId) || $postdata->UserId == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA PLATFORM USER ID";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		/* if(!isset($postdata->AbsensiId) || $postdata->AbsensiId == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "ID Absensi tidak diketahui!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		} */
		
		if(!isset($postdata->VehicleId) || $postdata->VehicleId == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "ID Unit tidak diketahui!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->DriverId) || $postdata->DriverId == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Driver ID tidak diketahui!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->DriverCoord) || $postdata->DriverCoord == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Coordinate tidak diketahui!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->ClockInTime) || $postdata->ClockInTime == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Jam Masuk tidak diketahui!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->ClockOutTime) || $postdata->ClockOutTime == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Jam Keluar tidak diketahui!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->DriverShiftType) || $postdata->DriverShiftType == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Silahkan Pilih Shift!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->DriverShiftDate) || $postdata->DriverShiftDate == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "Silahkan Pilih Tanggal Shift!";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}
		
		//end check kondisi mandatory
		
		
		//define to varibel
		/* $absensi_id  = $postdata->AbsensiId; */
		$absensi_vehicle_id = $postdata->VehicleId;
		$absensi_clock_in_time = $postdata->ClockInTime;
		$absensi_clock_out_time = $postdata->ClockOutTime;
		$absensi_clock_out_coord = $postdata->DriverCoord;
		
		$absensi_shift_date = $postdata->DriverShiftDate;
		$absensi_shift_time = $postdata->DriverShiftTime;
		$absensi_shift_type = $postdata->DriverShiftType;
		
		$driver_id  = $postdata->DriverId;
		$driver_name = $postdata->DriverName;
		$driver_idcard = $postdata->DriverIdCard;
		
		$data       = array(
			 //"absensi_id"           	 => $absensi_id,
			 "absensi_clock_in"   		 => $absensi_clock_in_time,
			 "absensi_clock_out"         => $absensi_clock_out_time,
			 "absensi_clock_out_status"  => 1,
			 "absensi_clock_out_coord"   => $absensi_clock_out_coord,
			 "absensi_status"          	 => 2,
			 "absensi_driver_id"           => $driver_id,
			 "absensi_driver_name"         => $driver_name,
			 "absensi_driver_idcard"       => $driver_idcard,
			 
			 "absensi_shift_date"       => $absensi_shift_date,
			 "absensi_shift_time"       => $absensi_shift_time,
			 "absensi_shift_type"       => $absensi_shift_type,
			 
			 //"absensi_duration"        => //hitung saat query update
			 //"absensi_duration_sec"    => //hitung saat query update

		);

		$payload = array(
		  "UserId"         			  => $postdata->UserId,
		  "VehicleId"         		  => $postdata->VehicleId,
		  "DriverCoord"         	  => $postdata->DriverCoord,
		  "ClockInTime"	    	  	  => $postdata->ClockInTime,
		  "ClockOutTime"	    	  => $postdata->ClockOutTime,
		  "DriverShiftDate"	    	  => $postdata->DriverShiftDate,
		  "DriverShifTime"	    	  => $postdata->DriverShiftTime,
		  "DriverShiftType"	    	  => $postdata->DriverShiftType,
		  
		  "DriverId"         		  => $postdata->DriverId,
		  "DriverIdCard"         	  => $postdata->DriverIdCard,
		  "DriverName"    		      => $postdata->DriverName,
		  
		
		);
		
		$update = $this->ts_updateData("ts_driver_absensi", $data);
        if ($update) {
           echo json_encode(array("code" => 200, "msg" => "Berhasil Absen Jam Keluar.", "payload" => $payload));
        }else {
           echo json_encode(array("code" => 400, "msg" => "Anda Gagal Absen. Pastikan Koneksi Anda Stabil.", "payload" => $payload));
        }
		  
		$nowendtime = date("Y-m-d H:i:s");
		$this->insertHitAPI("API Clock Out",$payload,$nowstarttime,$nowendtime);
		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		  

	}
	
	function ts_updateData($table, $data)
	{
		
		//$absensi_id = $data['absensi_id']; //karena aplikasi semi online dilepas dari ID
		$absensi_clock_in = $data['absensi_clock_in'];
		$nowtime_wita = $data['absensi_clock_out'];
		$absensi_clock_out = $data['absensi_clock_out'];
		$driver_coord =  $data['absensi_clock_out_coord'];
		
		$driver_id  = $data['absensi_driver_id'];
		$driver_name = $data['absensi_driver_name'];
		$driver_idcard = $data['absensi_driver_idcard'];
		
		$absensi_shift_date = $data['absensi_shift_date'];
		$absensi_shift_time = $data['absensi_shift_time'];
		
		$absensi_shift_type = $data['absensi_shift_type'];
		
		$duration = get_time_difference($absensi_clock_in, $nowtime_wita);

									$start_1 = dbmaketime($absensi_clock_in);
									$end_1 = dbmaketime($nowtime_wita);
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
									
									
		$this->dbts = $this->load->database("webtracking_ts", true);
		unset($data);

		$data['absensi_clock_out']         = $nowtime_wita;
		$data['absensi_clock_out_status']  = 1; //status ada data time
		$data['absensi_clock_out_coord']   = $driver_coord;
		$data['absensi_duration']          = $show;
		$data['absensi_duration_sec']      = $duration_sec;
		$data['absensi_status']            = 2; //sudah absen keluar

		$this->dbts->where('absensi_driver_id', $driver_id);
		$this->dbts->where('absensi_driver_idcard', $driver_idcard);
		$this->dbts->where('absensi_shift_date', $absensi_shift_date);
		$this->dbts->where('absensi_shift_type', $absensi_shift_type);
		$this->dbts->where('absensi_status', 1);
		$this->dbts->limit(1);
		return $this->dbts->update('ts_driver_absensi', $data);
		
		
		//update vehicle tms (available) / belum diaktifkan
		/* unset($datav);
		$datav['vehicle_tms'] = "0000";
				
		$this->db->where('vehicle_id', $absensi_vehicle_id);
		$this->db->limit(1);
		$this->db->update('vehicle', $datav);

		$callback["error"] = false;
		$callback["message"] = "Berhasil Absen Jam Keluar";
		$callback["redirect"] = base_url()."driver";

		echo json_encode($callback);
		$this->dbts->close(); */
		
		
		
	}
	
	function loginuser()
	{
		//ini_set('display_errors', 1);
		//ini_set('memory_limit', "2G");
		//ini_set('max_execution_time', 180); // 3 minutes
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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

		if(!isset($postdata->UserName) || $postdata->UserName == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Username";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->UserPass) || $postdata->UserPass == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Password";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}
		
		if($postdata->UserPass != $postdata->UserName)
		{
			$feature["code"] = 400;
			$feature["msg"] = "Password yang Anda masukan salah!";
			$feature["payload"] = $payload;
			echo json_encode($feature);
			exit;
		}
		
			//table 
			$UserIDBIB = 4408;
			$this->dbts = $this->load->database("webtracking_ts", true);
			//$this->dbts->order_by("portal_nik","desc");
			$this->dbts->select("*");
			$this->dbts->where("driver_nrp",$postdata->UserName); 
			$this->dbts->where("driver_status",1); 
			$this->dbts->where("driver_flag",0);
			$this->dbts->limit(1);
			$q = $this->dbts->get("ts_driver_poc");
			$data = $q->result();
			
			$passtext = "******";
			
			 $payload         = array(
				  "UserId"        => $postdata->UserId,
				  "UserName"       => $postdata->UserName,
				  "UserPass"   	   => $passtext
				);
				
			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"] = "Akun tidak dapat ditemukan, Silakan hubungi Koordinator FMS";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}else{
				$data = $q->result();
				 
			}
		
		//jika mobil lebih dari nol
		if(count($data) > 0)
		{
			$DataToUpload = array();
			//unset($DataToUpload);
			for($z=0;$z<count($data);$z++)
			{
				$DataToUpload[$z]->DriverId = $data[$z]->driver_id;
				$DataToUpload[$z]->DriverName = $data[$z]->driver_name;
				$DataToUpload[$z]->DriverIdCard = $data[$z]->driver_nrp;
				$DataToUpload[$z]->DriverCompany = $data[$z]->driver_company_id;
			}

		}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Login User",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

	


		exit;
	}
	
	function getvehiclebycompany()
	{
		//ini_set('display_errors', 1);
		$nowstarttime = date("Y-m-d H:i:s");
		header("Content-Type: application/json");

		$token = "UGaW5kNkjhA782GBNS1616KbswQYa5372bsdexVNT16";
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
		
		if(!isset($postdata->CompanyId) || $postdata->CompanyId == "")
		{
			$feature["code"] = 400;
			$feature["msg"] = "Invalid Company ID";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			$UserIDBIB = 4408;
			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			$this->db->where("vehicle_company",$postdata->CompanyId);
			$this->db->where("vehicle_user_id",$UserIDBIB);
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

				$payload      		    = array(
				  "UserId"          => $postdata->UserId,
				  "CompanyId"   	=> $postdata->CompanyId
				  
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

				//printf("ATTR %s \r\n",$vehicle[$z]->vehicle_no);

								$DataToUpload[$i]->VehicleId = $vehicle[$i]->vehicle_id;
								$DataToUpload[$i]->VehicleUserID = $vehicle[$i]->vehicle_user_id;
								$DataToUpload[$i]->VehicleDevice = $vehicle[$i]->vehicle_device;
								$DataToUpload[$i]->VehicleNo = $vehicle[$i]->vehicle_no;
								$DataToUpload[$i]->VehicleNoBackup = $vehicle[$i]->vehicle_no_bk;
								$DataToUpload[$i]->VehicleName = $vehicle[$i]->vehicle_name;

								$DataToUpload[$i]->VehicleCardNo = $vehicle[$i]->vehicle_card_no;
								$DataToUpload[$i]->VehicleOperator = $vehicle[$i]->vehicle_operator;
								$DataToUpload[$i]->VehicleStatus = $vehicle[$i]->vehicle_status;

								$DataToUpload[$i]->VehicleImage = $vehicle[$i]->vehicle_image;
								$DataToUpload[$i]->VehicleCreatedDate = $vehicle[$i]->vehicle_created_date;
								$DataToUpload[$i]->VehicleType = $vehicle[$i]->vehicle_type;

								$DataToUpload[$i]->VehicleCompany = $vehicle[$i]->vehicle_company;
								$DataToUpload[$i]->VehicleSubCompany = $vehicle[$i]->vehicle_subcompany;
								$DataToUpload[$i]->VehicleGroup = $vehicle[$i]->vehicle_group;
								$DataToUpload[$i]->VehicleSubGroup = $vehicle[$i]->vehicle_subgroup;

								$DataToUpload[$i]->VehicleTanggalPasang = $vehicle[$i]->vehicle_tanggal_pasang;
								$DataToUpload[$i]->VehicleImei = $vehicle[$i]->vehicle_imei;
								$DataToUpload[$i]->VehicleMV03 = $vehicle[$i]->vehicle_mv03;
								$DataToUpload[$i]->VehicleSensor = $vehicle[$i]->vehicle_sensor;
								$DataToUpload[$i]->VehicleSOS = $vehicle[$i]->vehicle_sos;

								$DataToUpload[$i]->VehiclePortalRangka = $vehicle[$i]->vehicle_portal_rangka;
								$DataToUpload[$i]->VehiclePortalMesin = $vehicle[$i]->vehicle_portal_mesin;
								$DataToUpload[$i]->VehiclePortalRfidSPI = $vehicle[$i]->vehicle_portal_rfid_spi;
								$DataToUpload[$i]->VehiclePortalRfidWIM = $vehicle[$i]->vehicle_portal_rfid_wim;
								$DataToUpload[$i]->VehiclePortalPortalTare = $vehicle[$i]->vehicle_portal_tare;

								$DataToUpload[$i]->VehiclePortTime = $vehicle[$i]->vehicle_port_time;
								$DataToUpload[$i]->VehiclePortName = $vehicle[$i]->vehicle_port_name;
								$DataToUpload[$i]->VehicleRomTime = $vehicle[$i]->vehicle_rom_time;
								$DataToUpload[$i]->VehicleRomName = $vehicle[$i]->vehicle_rom_name;

								//$datajson["Data"] = $DataToUpload;




			}
			//$content = json_encode($datajson);
			$content = $DataToUpload;

			//echo $content;
			echo json_encode(array("code" => 200, "msg" => "ok", "data" => $content, "payload" => $payload), JSON_NUMERIC_CHECK);
			$nowendtime = date("Y-m-d H:i:s");
			$this->insertHitAPI("API Master Vehicle by Company",$payload,$nowstarttime,$nowendtime);
			$this->db->close();
			$this->db->cache_delete_all();

		}


		exit;
	}

	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
