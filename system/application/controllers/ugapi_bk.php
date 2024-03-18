<?php
include "base.php";

class Ugapi extends Base {

	function Ugapi()
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
		$feature["VehicleNo"] = "BBS 1227;BBS 1229";
		$feature["StartTime"] = "2022-08-15 00:00:00";
		$feature["EndTime"] = "2022-08-15 23:59:59";

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

	function getoverspeed()
	{
		//ini_set('display_errors', 1);
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
	}

	function getlocationhour()
	{
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

	function getlocationreport()
	{
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
	
	function getalarmevidence()
	{
		//ini_set('display_errors', 1);
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
				$this->dblive = $this->load->database("webtracking_gps_temanindobara_live_2", true);
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

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
