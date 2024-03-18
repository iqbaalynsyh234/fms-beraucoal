<?php
include "base.php";

class Apimaps extends Base {

	function Apimaps()
	{
		parent::Base();
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Credentials: false');
		header('Access-Control-Allow-Methods: POST');
		header('Access-Control-Allow-Headers: Content-Type');
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

	function request()
	{
		ini_set('display_errors', 1);
		$url = "http://api.lacak-mobil.com/apilocal/lastposition";
		$feature = array();

		$feature["UserId"] = 1147; //pbi
		//$feature["VehiclePlateNo"] = "A 9105 F;A 8035 H";
		$feature["VehiclePlateNo"] = "all";
		$feature["Indeks"] = 1;
		$feature["LimitVehicle"] = 5;
		$feature["LimitGps"] = 5;
		$feature["UserLevel"] = 1;

		$content = json_encode($feature);
		$total_content = count($content);

		printf("Data JSON : %s \r \n",$content);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		$json_response = curl_exec($curl);
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		printf("-------------------------- \r\n");

		exit;
	}

	function lastposition()
	{
		ini_set('display_errors', 1);
		date_default_timezone_set("Asia/Bangkok");
		header("Content-Type: application/json");

		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$page = 0;
		$limit = 0;
		$limitgps = 1;
		$now = date("Ymd");
		$sdate = date("Y-m-d H:i:s");

		$UserCompany = "";
		$UserSubCompany = "";
		$UserGroup = "";
		$UserSubGroup = "";

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

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID USER ID";
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->Indeks))
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID Indeks";
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->LimitVehicle))
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID LIMIT VEHICLE";
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->LimitGps))
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID LIMIT GPS DATA";
			echo json_encode($feature);
			exit;
		}

		if(isset($postdata->Indeks) && isset($postdata->LimitVehicle)){
			$page = $postdata->Indeks * $postdata->LimitVehicle;
			$limit = $postdata->LimitVehicle;
		}

		if(!isset($postdata->VehiclePlateNo) || $postdata->VehiclePlateNo == "all")
		{
			$allvehicle = 1;
		}

		if(!isset($postdata->UserLevel) || $postdata->UserLevel == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID USER LEVEL";
			echo json_encode($feature);
			exit;
		}

		if(isset($postdata->UserCompany))
		{
			$UserCompany = $postdata->UserCompany;
		}

		if(isset($postdata->UserSubCompany))
		{
			$UserSubCompany = $postdata->UserSubCompany;
		}

		if(isset($postdata->UserGroup))
		{
			$UserGroup = $postdata->UserGroup;
		}

		if(isset($postdata->UserSubGroup))
		{
			$UserSubGroup = $postdata->UserSubGroup;
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
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_no,vehicle_name,vehicle_device,vehicle_type");
			$this->db->order_by("vehicle_id","asc");
			if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			}else if($allvehicle == 1){
				$this->db->limit($limit,$page);
			}else{
				$this->db->where("vehicle_no",$postdata->VehiclePlateNo);
			}
			if($postdata->UserLevel == 1){
				$this->db->where("vehicle_user_id",$postdata->UserId);
			}else if($postdata->UserLevel == 2){
				$this->db->where("vehicle_company",$UserCompany);
			}else if($postdata->UserLevel == 3){
				$this->db->where("vehicle_subcompany",$UserSubCompany);
			}else if($postdata->UserLevel == 4){
				$this->db->where("vehicle_group",$UserGroup);
			}else if($postdata->UserLevel == 5){
				$this->db->where("vehicle_subgroup",$UserSubGroup);
			}else{
				$this->db->where("vehicle_no",99999);
			}
			$this->db->where("vehicle_status",1);
			$this->db->where("vehicle_active_date2 >",$now); //tidak expired
			$q = $this->db->get("vehicle");
			$vehicle = $q->result();
			//print_r($vehicle);exit();
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
					$this->db->order_by("vehicle_id", "asc");
					$this->db->where("vehicle_device", $vehicle[$z]->vehicle_device);
					$this->db->where("vehicle_user_id", $vehicle[$z]->vehicle_user_id);
					$this->db->limit(1);
					$qv = $this->db->get("vehicle");
					$rowvehicle = $qv->row();
					//$rowv[] = $qv->row();

					//Seleksi Databases
					$tables = $this->gpsmodel->getTable($rowvehicle);
					$this->dbdata = $this->load->database($tables["dbname"], TRUE);

					$table = "gps";
					$tableinfo = "gps_info";


					$this->dbdata->order_by("gps_time", "desc");
					$this->dbdata->limit($postdata->LimitGps);
					$this->dbdata->select("gps_info_device,gps_time,gps_course,gps_speed,gps_latitude_real,gps_longitude_real,gps_info_io_port,gps_status,
										   gps_longitude,gps_latitude,gps_ew,gps_ns");
					$this->dbdata->where("gps_info_device", $vehicle[$z]->vehicle_device);
					$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$q = $this->dbdata->get($tables['gps']);
					$rows = $q->result();
					$trows = count($rows);
					$rows = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
					//print_r($rows);exit();

					if(isset($rows) && $trows>0)
					{
						$DataToUpload = array();
						unset($DataToUpload);
						for ($i=0;$i<$trows;$i++){
						//for ($i=($trows-1); $i>=0; $i--){
								$lat = 0; $lng = 0;
								$gpslocation = "-";
								$geofence = "-";

								if(!in_array($vehicle[$z]->vehicle_type, $this->config->item('vehicle_others'))){
									$da = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
									if (isset($rows[$i]->gps_longitude_real))
									{
										$lng = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
									}
									if (isset($rows[$i]->gps_latitude_real))
									{
										$lat = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
									}

									$gpslocation = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
									$geofence = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $vehicle[$z]->vehicle_user_id);
								}else{
									$da = date("Y-m-d H:i:s", strtotime($rows[$i]->gps_time));
									$lng = number_format($rows[$i]->gps_longitude, 4, ".", "");
									$lat = number_format($rows[$i]->gps_latitude, 4, ".", "");
									$gpslocation = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
									$geofence = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
								}

								//$DataToUpload[$i]->VehicleDevice = $rows[$i]->gps_info_device;
								$DataToUpload[$i]->VehicleNo = $vehicle[$z]->vehicle_no;
								$DataToUpload[$i]->VehicleName = $vehicle[$z]->vehicle_name;
								$DataToUpload[$i]->GPSTime = $da;
								$DataToUpload[$i]->Speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");

								if (isset($rows[$i]->gps_info_io_port))
								{
									$statusengine = substr($rows[$i]->gps_info_io_port, 4, 1);
									if($statusengine == 1)
									{
										$DataToUpload[$i]->Engine = "ON";
									}
									else
									{
										$DataToUpload[$i]->Engine = "OFF";
									}
								}

								$DataToUpload[$i]->Course = $rows[$i]->gps_course;
								$DataToUpload[$i]->Latitude = $lat;
								$DataToUpload[$i]->Longitude = $lng;
								$DataToUpload[$i]->Location = $gpslocation->display_name;
								if($geofence == false){
									$geofence = "-";
								}
								$DataToUpload[$i]->Geofence= $geofence;

								if((isset($rows[$i]->gps_status) && ($rows[$i]->gps_status) == "A"))
								{
									$signal = "OK";
								}
								else
								{
									$signal = "NOT OK";
								}

								$DataToUpload[$i]->Signal = $signal;
								$DataToUpload[$i]->StatusCode = "OK";

						}
						$datajson[$vehicle[$z]->vehicle_device] = $DataToUpload;
					}else{
						//print_r("NO DATA");exit();
					}
					//$datajson["Data"] = $DataToUpload;
			}
			$content = json_encode($datajson);
			echo $content;

		}


		exit;
	}

	function lastpositionjs_old()
	{
		ini_set('display_errors', 1);
		date_default_timezone_set("Asia/Bangkok");
		header("Content-Type: application/json");

		$postdata = json_decode(file_get_contents("php://input"));
			$UserId         = $postdata->UserId;
      		$VehiclePlateNo = $postdata->VehiclePlateNo;
      		$Indeks         = $postdata->Indeks;
      		$LimitVehicle   = $postdata->LimitVehicle;
      		$LimitGps       = $postdata->LimitGps;
      		$UserLevel      = $postdata->UserLevel;
      		$UserCompany    = $postdata->UserCompany;
		//.$VehiclePlateNo.$Indeks.$LimitVehicle.$LimitGps.$UserLevel.$UserCompany
		//print_r($UserId.$VehiclePlateNo.$Indeks.$LimitVehicle.$LimitGps.$UserLevel.$UserCompany);exit();
		$allvehicle = 0;
		$page = 0;
		$limit = 0;
		$limitgps = 1;
		$now = date("Ymd");
		$sdate = date("Y-m-d H:i:s");

		$UserCompany = "";
		$UserSubCompany = "";
		$UserGroup = "";
		$UserSubGroup = "";

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

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID USER ID";
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->Indeks))
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID Indeks";
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->LimitVehicle))
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID LIMIT VEHICLE";
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->LimitGps))
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID LIMIT GPS DATA";
			echo json_encode($feature);
			exit;
		}

		if(isset($postdata->Indeks) && isset($postdata->LimitVehicle)){
			$page = $postdata->Indeks * $postdata->LimitVehicle;
			$limit = $postdata->LimitVehicle;
		}

		if(!isset($postdata->VehiclePlateNo) || $postdata->VehiclePlateNo == "all")
		{
			$allvehicle = 1;
		}

		if(!isset($postdata->UserLevel) || $postdata->UserLevel == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID USER LEVEL";
			echo json_encode($feature);
			exit;
		}

		if(isset($postdata->UserCompany))
		{
			$UserCompany = $postdata->UserCompany;
		}

		if(isset($postdata->UserSubCompany))
		{
			$UserSubCompany = $postdata->UserSubCompany;
		}

		if(isset($postdata->UserGroup))
		{
			$UserGroup = $postdata->UserGroup;
		}

		if(isset($postdata->UserSubGroup))
		{
			$UserSubGroup = $postdata->UserSubGroup;
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
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_no,vehicle_device,vehicle_type");
			$this->db->order_by("vehicle_id","asc");
			if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			}else if($allvehicle == 1){
				$this->db->limit($limit,$page);
			}else{
				$this->db->where("vehicle_no",$postdata->VehiclePlateNo);
			}
			if($postdata->UserLevel == 1){
				$this->db->where("vehicle_user_id",$postdata->UserId);
			}else if($postdata->UserLevel == 2){
				$this->db->where("vehicle_company",$UserCompany);
			}else if($postdata->UserLevel == 3){
				$this->db->where("vehicle_subcompany",$UserSubCompany);
			}else if($postdata->UserLevel == 4){
				$this->db->where("vehicle_group",$UserGroup);
			}else if($postdata->UserLevel == 5){
				$this->db->where("vehicle_subgroup",$UserSubGroup);
			}else{
				$this->db->where("vehicle_no",99999);
			}
			$this->db->where("vehicle_status",1);
			$this->db->where("vehicle_active_date2 >",$now); //tidak expired
			$q = $this->db->get("vehicle");
			$vehicle = $q->result();
			//print_r($vehicle);exit();
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
					$this->db->order_by("vehicle_id", "asc");
					$this->db->where("vehicle_device", $vehicle[$z]->vehicle_device);
					$this->db->where("vehicle_user_id", $vehicle[$z]->vehicle_user_id);
					$this->db->limit(1);
					$qv = $this->db->get("vehicle");
					$rowvehicle = $qv->row();
					//$rowv[] = $qv->row();

					//Seleksi Databases
					$tables = $this->gpsmodel->getTable($rowvehicle);
					$this->dbdata = $this->load->database($tables["dbname"], TRUE);

					$table = "gps";
					$tableinfo = "gps_info";


					$this->dbdata->order_by("gps_time", "desc");
					$this->dbdata->limit($postdata->LimitGps);
					$this->dbdata->select("gps_info_device,gps_time,gps_course,gps_speed,gps_latitude_real,gps_longitude_real,gps_info_io_port,gps_status,
										   gps_longitude,gps_latitude,gps_ew,gps_ns");
					$this->dbdata->where("gps_info_device", $vehicle[$z]->vehicle_device);
					$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$q = $this->dbdata->get($tables['gps']);
					$rows = $q->result();
					$trows = count($rows);
					$rows = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
					//print_r($rows);exit();

					if(isset($rows) && $trows>0)
					{
						for ($i=0;$i<$trows;$i++){
						//for ($i=($trows-1); $i>=0; $i--){
								$lat = 0; $lng = 0;
								$gpslocation = "-";
								$geofence = "-";

								if(!in_array($vehicle[$z]->vehicle_type, $this->config->item('vehicle_others'))){
									$da = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
									if (isset($rows[$i]->gps_longitude_real))
									{
										$lng = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
									}
									if (isset($rows[$i]->gps_latitude_real))
									{
										$lat = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
									}

									$gpslocation = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
									$geofence = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $vehicle[$z]->vehicle_user_id);
								}else{
									$da = date("Y-m-d H:i:s", strtotime($rows[$i]->gps_time));
									$lng = number_format($rows[$i]->gps_longitude, 4, ".", "");
									$lat = number_format($rows[$i]->gps_latitude, 4, ".", "");
									$gpslocation = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
									$geofence = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
								}

								$DataToUpload[$z][$i]->VehicleDevice = $rows[$i]->gps_info_device;
								$DataToUpload[$z][$i]->VehicleNo = $vehicle[$z]->vehicle_no;
								$DataToUpload[$z][$i]->VehicleName = $vehicle[$z]->vehicle_name;
								$DataToUpload[$z][$i]->GPSTime = $da;
								$DataToUpload[$z][$i]->Speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");

								if (isset($rows[$i]->gps_info_io_port))
								{
									$statusengine = substr($rows[$i]->gps_info_io_port, 4, 1);
									if($statusengine == 1)
									{
										$DataToUpload[$z][$i]->Engine = "ON";
									}
									else
									{
										$DataToUpload[$z][$i]->Engine = "OFF";
									}
								}

								$DataToUpload[$z][$i]->Course = $rows[$i]->gps_course;
								$DataToUpload[$z][$i]->Latitude = $lat;
								$DataToUpload[$z][$i]->Longitude = $lng;
								$DataToUpload[$z][$i]->Location = $gpslocation->display_name;
								if($geofence == false){
									$geofence = "-";
								}
								$DataToUpload[$z][$i]->Geofence = $geofence;

								if((isset($rows[$i]->gps_status) && ($rows[$i]->gps_status) == "A"))
								{
									$signal = "OK";
								}
								else
								{
									$signal = "NOT OK";
								}

								$DataToUpload[$z][$i]->Signal = $signal;
								$DataToUpload[$z][$i]->StatusCode = "OK";


						}

					}else{
						//print_r("NO DATA");exit();
					}
					$datajson["Data"] = $DataToUpload;
			}
			$content = json_encode($datajson);
			echo $content;

		}


		exit;
	}


	function lastpositionjs()
	{
		ini_set('display_errors', 1);
		date_default_timezone_set("Asia/Bangkok");
		header("Content-Type: application/json");

		$postdata = json_decode(file_get_contents("php://input"));
		$useridnya =  $postdata->UserId;
			$UserId         = $useridnya - 1;
      		$VehiclePlateNo = $postdata->VehiclePlateNo;
      		$Indeks         = $postdata->Indeks;
      		$LimitVehicle   = $postdata->LimitVehicle;
      		$LimitGps       = $postdata->LimitGps;
      		$UserLevel      = $postdata->UserLevel;
      		$UserCompany    = $postdata->UserCompany;
		//.$VehiclePlateNo.$Indeks.$LimitVehicle.$LimitGps.$UserLevel.$UserCompany
		//print_r($UserId.$VehiclePlateNo.$Indeks.$LimitVehicle.$LimitGps.$UserLevel.$UserCompany);exit();
		$allvehicle = 0;
		$page = 0;
		$limit = 0;
		$limitgps = 1;
		$now = date("Ymd");
		$sdate = date("Y-m-d H:i:s");

		$UserCompany = "";
		$UserSubCompany = "";
		$UserGroup = "";
		$UserSubGroup = "";

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

		$feature = array();

		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID USER ID";
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->Indeks))
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID Indeks";
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->LimitVehicle))
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID LIMIT VEHICLE";
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->LimitGps))
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID LIMIT GPS DATA";
			echo json_encode($feature);
			exit;
		}

		if(isset($postdata->Indeks) && isset($postdata->LimitVehicle)){
			$page = $postdata->Indeks * $postdata->LimitVehicle;
			$limit = $postdata->LimitVehicle;
		}

		if(!isset($postdata->VehiclePlateNo) || $postdata->VehiclePlateNo == "all")
		{
			$allvehicle = 1;
		}

		if(!isset($postdata->UserLevel) || $postdata->UserLevel == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID USER LEVEL";
			echo json_encode($feature);
			exit;
		}

		if(isset($postdata->UserCompany))
		{
			$UserCompany = $postdata->UserCompany;
		}

		if(isset($postdata->UserSubCompany))
		{
			$UserSubCompany = $postdata->UserSubCompany;
		}

		if(isset($postdata->UserGroup))
		{
			$UserGroup = $postdata->UserGroup;
		}

		if(isset($postdata->UserSubGroup))
		{
			$UserSubGroup = $postdata->UserSubGroup;
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
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_no,vehicle_name,vehicle_device,vehicle_type");
			$this->db->order_by("vehicle_id","asc");
			if($allvehicle == 0){
				$this->db->where_in("vehicle_no",$ex_vehicle);
			}else if($allvehicle == 1){
				$this->db->limit($limit,$page);
			}else{
				$this->db->where("vehicle_no",$postdata->VehiclePlateNo);
			}
			if($postdata->UserLevel == 1){
				$this->db->where("vehicle_user_id",$postdata->UserId);
			}else if($postdata->UserLevel == 2){
				$this->db->where("vehicle_company",$UserCompany);
			}else if($postdata->UserLevel == 3){
				$this->db->where("vehicle_subcompany",$UserSubCompany);
			}else if($postdata->UserLevel == 4){
				$this->db->where("vehicle_group",$UserGroup);
			}else if($postdata->UserLevel == 5){
				$this->db->where("vehicle_subgroup",$UserSubGroup);
			}else{
				$this->db->where("vehicle_no",99999);
			}
			$this->db->where("vehicle_status",1);
			$this->db->where("vehicle_active_date2 >",$now); //tidak expired
			$q = $this->db->get("vehicle");
			$vehicle = $q->result();
			//print_r($vehicle);exit();
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
					$this->db->order_by("vehicle_id", "asc");
					$this->db->where("vehicle_device", $vehicle[$z]->vehicle_device);
					$this->db->where("vehicle_user_id", $vehicle[$z]->vehicle_user_id);
					$this->db->limit(1);
					$qv = $this->db->get("vehicle");
					$rowvehicle = $qv->row();
					//$rowv[] = $qv->row();

					//Seleksi Databases
					$tables = $this->gpsmodel->getTable($rowvehicle);
					$this->dbdata = $this->load->database($tables["dbname"], TRUE);

					$table = "gps";
					$tableinfo = "gps_info";


					$this->dbdata->order_by("gps_time", "desc");
					$this->dbdata->limit($postdata->LimitGps);
					$this->dbdata->select("gps_info_device,gps_time,gps_course,gps_speed,gps_latitude_real,gps_longitude_real,gps_info_io_port,gps_status,
										   gps_longitude,gps_latitude,gps_ew,gps_ns");
					$this->dbdata->where("gps_info_device", $vehicle[$z]->vehicle_device);
					$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$q = $this->dbdata->get($tables['gps']);
					$rows = $q->result();
					$trows = count($rows);
					$rows = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
					//print_r($rows);exit();

					if(isset($rows) && $trows>0)
					{
						$DataToUpload = array();
						unset($DataToUpload);
						for ($i=0;$i<$trows;$i++){
						//for ($i=($trows-1); $i>=0; $i--){
								$lat = 0; $lng = 0;
								$gpslocation = "-";
								$geofence = "-";

								if(!in_array($vehicle[$z]->vehicle_type, $this->config->item('vehicle_others'))){
									$da = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
									if (isset($rows[$i]->gps_longitude_real))
									{
										$lng = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
									}
									if (isset($rows[$i]->gps_latitude_real))
									{
										$lat = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
									}

									$gpslocation = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
									$geofence = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $vehicle[$z]->vehicle_user_id);
								}else{
									$da = date("Y-m-d H:i:s", strtotime($rows[$i]->gps_time));
									$lng = number_format($rows[$i]->gps_longitude, 4, ".", "");
									$lat = number_format($rows[$i]->gps_latitude, 4, ".", "");
									$gpslocation = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
									$geofence = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
								}

								//$DataToUpload[$i]->VehicleDevice = $rows[$i]->gps_info_device;
								$DataToUpload[$i]->VehicleNo = $vehicle[$z]->vehicle_no;
								$DataToUpload[$i]->VehicleName = $vehicle[$z]->vehicle_name;
								$DataToUpload[$i]->GPSTime = $da;
								$DataToUpload[$i]->Speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");

								if (isset($rows[$i]->gps_info_io_port))
								{
									$statusengine = substr($rows[$i]->gps_info_io_port, 4, 1);
									if($statusengine == 1)
									{
										$DataToUpload[$i]->Engine = "ON";
									}
									else
									{
										$DataToUpload[$i]->Engine = "OFF";
									}
								}

								$DataToUpload[$i]->Course = $rows[$i]->gps_course;
								$DataToUpload[$i]->Latitude = $lat;
								$DataToUpload[$i]->Longitude = $lng;
								$DataToUpload[$i]->Location = $gpslocation->display_name;
								if($geofence == false){
									$geofence = "-";
								}
								$DataToUpload[$i]->Geofence= $geofence;

								if((isset($rows[$i]->gps_status) && ($rows[$i]->gps_status) == "A"))
								{
									$signal = "OK";
								}
								else
								{
									$signal = "NOT OK";
								}

								$DataToUpload[$i]->Signal = $signal;
								$DataToUpload[$i]->StatusCode = "OK";

						}
						$datajson[$vehicle[$z]->vehicle_device] = $DataToUpload;
					}else{
						//print_r("NO DATA");exit();
					}
					//$datajson["Data"] = $DataToUpload;
			}
			$content = json_encode($datajson);
			echo $content;

		}

		exit;
	}



	}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
