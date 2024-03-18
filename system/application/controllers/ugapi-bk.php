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

	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_user) {

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
			if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			}
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
							
							
								$DataToUpload[$i]->VehicleNo = $rows[$i]->overspeed_report_vehicle_no;
								$DataToUpload[$i]->VehicleDevice = $rows[$i]->overspeed_report_vehicle_device;
								$DataToUpload[$i]->VehicleName = $rows[$i]->overspeed_report_vehicle_name;
								
								if($rows[$i]->overspeed_report_vehicle_company > 0 ){
									$company_name = $this->getcompanyname_byID($rows[$i]->overspeed_report_vehicle_company);
								}else{
									$company_name = "";
								}
								
								$DataToUpload[$i]->VehicleCompany = $company_name;
								//$DataToUpload[$i]->SpeedStatus = $rows[$i]->overspeed_report_speed_status;
								$DataToUpload[$i]->ReportName = $rows[$i]->overspeed_report_name;
								
								$DataToUpload[$i]->GPSStatus = $rows[$i]->overspeed_report_gpsstatus;
								$DataToUpload[$i]->GPSTime = $rows[$i]->overspeed_report_gps_time;
								$DataToUpload[$i]->GPSSpeed = $rows[$i]->overspeed_report_speed;
								
								
								$DataToUpload[$i]->GeofenceName = $rows[$i]->overspeed_report_geofence_name;
								$DataToUpload[$i]->GeofenceLimit = $rows[$i]->overspeed_report_geofence_limit;
								$DataToUpload[$i]->GefenceType = $rows[$i]->overspeed_report_geofence_type;
								
								$DataToUpload[$i]->Jalur = $rows[$i]->overspeed_report_jalur;
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
	function requestpbi()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");

		$token = "PBIaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
		$authorization = "Authorization:".$token;
		$url = "http://api.lacak-mobil.com/api/lastpositionpbi";
		$feature = array();

		$feature["UserId"] = 1147; //pbi
		$feature["VehiclePlateNo"] = "A 9105 F;A 8035 H";
		//$feature["VehiclePlateNo"] = "all";

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

	function lastpositionpbi()
	{
		//ini_set('display_errors', 1);
		header("Content-Type: application/json");

		$token = "PBIaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
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
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID TOKEN";
			echo json_encode($feature);
			exit;
		}

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID USER ID";
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
				$feature["StatusCode"] = "FAILED";
				$feature["Message"] = "USER & TOKEN NOT AVAILABLE";
				echo json_encode($feature);
				exit;
			}

		}

		if(!isset($postdata->VehiclePlateNo) || $postdata->VehiclePlateNo == "all")
		{
			$allvehicle = 1;
		}

		if(!isset($postdata->VehiclePlateNo) || $postdata->VehiclePlateNo == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID VEHICLE ID";
			echo json_encode($feature);
			exit;
		}else{
			$check_vehicle = strpos($postdata->VehiclePlateNo,';');
			$ex_vehicle = explode(";",$postdata->VehiclePlateNo);

			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			}
			$this->db->where("vehicle_user_id",$postdata->UserId);
			$this->db->where("vehicle_status",1);
			$this->db->where("vehicle_active_date2 >",$now); //tidak expired
			$q = $this->db->get("vehicle");
			$vehicle = $q->result();

			if($q->num_rows == 0)
			{
				$feature["StatusCode"] = "FAILED";
				$feature["Message"] = "VEHICLE NOT FOUND";
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();

			}
		}


		//jika mobil lebih dari nol
		if(count($vehicle) > 0)
		{
			$DataToUpload = array();
			unset($DataToUpload);
			for($z=0;$z<count($vehicle);$z++)
			{
					$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
					$this->db->order_by("vehicle_device", "asc");
					$this->db->where("vehicle_device", $vehicle[$z]->vehicle_device);
					$this->db->limit(1);
					$qv = $this->db->get("vehicle");
					$rowvehicle = $qv->row();
					$rowv[] = $qv->row();

					//Seleksi Databases
					$tables = $this->gpsmodel->getTable($rowvehicle);
					$this->dbdata = $this->load->database($tables["dbname"], TRUE);

					$table = "gps";
					$tableinfo = "gps_info";

					$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->dbdata->where("gps_info_device", $vehicle[$z]->vehicle_device);
					$this->dbdata->order_by("gps_time", "desc");
					$this->dbdata->limit(1);
					$q = $this->dbdata->get($tables['gps']);
					if($q->num_rows > 0)
					{
						//$rows[] = $q->row();
						$rows = $q->row();
						$trows = count($rows);
					}

					if(isset($rows) && count($rows)>0)
					{
						//printf("ATTR %s \r\n",$vehicle[$z]->vehicle_no);

							if(isset($rows))
							{
								if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
								{
									$da = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows->gps_time)));
								}else{
									$da = date("Y-m-d H:i:s", strtotime($rows->gps_time));
								}

								$dt = strtotime($da);
								$lat = 0; $lng = 0;
								$gpslocation = "-";
								$geofence = "-";


								$DataToUpload[$z]->VehicleNo = $vehicle[$z]->vehicle_no;
								$DataToUpload[$z]->GPSTime = $da;
								$DataToUpload[$z]->Speed = number_format($rows->gps_speed*1.852, 0, "", ".");

								if (isset($rows->gps_info_io_port))
								{
									$statusengine = substr($rows->gps_info_io_port, 4, 1);
									if($statusengine == 1)
									{
										$DataToUpload[$z]->Engine = "ON";
									}
									else
									{
										$DataToUpload[$z]->Engine = "OFF";
									}
								}

								$DataToUpload[$z]->Course = $rows->gps_course;

								if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315DOOR")
								{
									/*
									if (isset($rows[$i]->gps_longitude))
									{
										$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
									}
									if (isset($rows[$i]->gps_latitude))
									{
										$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
									}
									*/

									if (isset($rows->gps_longitude_real))
									{
										$lng = number_format($rows->gps_longitude_real, 4, ".", "");
									}
									if (isset($rows->gps_latitude_real))
									{
										$lat = number_format($rows->gps_latitude_real, 4, ".", "");
									}
								}
								else
								{
									$lng = number_format($rows->gps_longitude, 4, ".", "");
									$lat = number_format($rows->gps_latitude, 4, ".", "");
								}

								$DataToUpload[$z]->Latitude = $lat;
								$DataToUpload[$z]->Longitude = $lng;


								if (isset($rows->gps_longitude))
								{
									if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "GT06N" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315DOOR")
									{
										$gpslocation = $this->getPosition($rows->gps_longitude, $rows->gps_ew, $rows->gps_latitude, $rows->gps_ns);
										$geofence = $this->getGeofence_location($rows->gps_longitude, $rows->gps_ew, $rows->gps_latitude, $rows->gps_ns, $vehicle[$z]->vehicle_user_id);
									}
									else
									{
										$gpslocation = $this->getPosition_other($rows->gps_longitude, $rows->gps_latitude);
										$geofence = $this->getGeofence_location_other($rows->gps_longitude, $rows->gps_latitude, $rowvehicle->vehicle_user_id);
									}

								}

								$DataToUpload[$z]->Location = $gpslocation->display_name;
								$DataToUpload[$z]->Geofence = $geofence;

								if((isset($rows->gps_status) && ($rows->gps_status) == "A"))
								{
									$signal = "OK";
								}
								else
								{
									$signal = "NOT OK";
								}

								$DataToUpload[$z]->Signal = $signal;
								$DataToUpload[$z]->StatusCode = "OK";


								$datajson["Data"] = $DataToUpload;




							}

					}

			}
			$content = json_encode($datajson);
			echo $content;

		}


		exit;
	}

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */