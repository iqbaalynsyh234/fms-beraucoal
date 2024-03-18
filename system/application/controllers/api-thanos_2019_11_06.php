<?php
include "base.php";

class Api extends Base {

	function Api()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("dashboardmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}
	
	function request()
	{
		printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");
		
		$token = "CVaW5kNGhraTX0OnEVaNoxRkNHksdAWh7k0dA16";
		$authorization = "Authorization:".$token;
		$url = "http://api.lacak-mobil.com/api/lastpositionderek";
		$feature = array();
		
		$feature["UserId"] = 3893; //derek purbaleunyi
		$feature["VehiclePlateNo"] = "all"; 
		
		printf("POSTING PROSES \r\n");
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
	
	function lastposition()
	{
		header("Content-Type: application/json");
		
		//$token = "Token kaW5kNGhraTR0OnAwNXRkNHQ0a2k0dA16";
		$token = "BVaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
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
			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			if($allvehicle == 0){
				$this->db->where("vehicle_no",$postdata->VehiclePlateNo);	
			}
			$this->db->where("vehicle_user_id",$postdata->UserId);
			$this->db->where("vehicle_status",1);
			$this->db->where("vehicle_active_date2 >",$now); //tidak expired
			$q = $this->db->get("vehicle");
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
						printf("ATTR %s \r\n",$vehicle[$z]->vehicle_no);
						
							if(isset($rows))
							{
								if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
								{
									$da = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows->gps_time)));
								
								}else{
									$da = date("Y-m-d H:i:s", strtotime($rows->gps_time));
								
								}
								
								$dt = strtotime($da);
								$lat = 0; $lng = 0;
								$gpslocation = "";
								$feature["VehicleNo"] = $vehicle[$z]->vehicle_no;
								$feature["GPSTime"] = $da;
								$feature["Speed"] = number_format($rows->gps_speed*1.852, 0, "", ".");
								if (isset($rows->gps_info_io_port))
								{
									$ioport = $rows->gps_info_io_port;
									$feature["StatusEngine"] = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
									if($feature["StatusEngine"] == false)
									{
										$feature["StatusEngine"] = "OFF";
									}	
									else
									{
										$feature["statusEngine"] = "ON";
									}
								}
								else
								{
									$feature["statusEngine"] = "OFF";
								}
								
								$feature["Course"] = $rows->gps_course;
								
								if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" )
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
								$feature["Latitude"] = $lat;
								$feature["Longitude"] = $lng;
								
								if (isset($rows->gps_longitude))
								{
									if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
									{
										$gpslocation = $this->getPosition($rows->gps_longitude, $rows->gps_ew, $rows->gps_latitude, $rows->gps_ns);
									}
									else
									{
										$gpslocation = $this->getPosition_other($rows->gps_longitude, $rows->gps_latitude);
									}
									
								}
								
								$feature["Location"] = $gpslocation->display_name;
								
								if((isset($rows->gps_status) && ($rows->gps_status) == "A"))
								{
									$signal = "OK";
								}
								else
								{
									$signal = "NOT OK";
								}
								
								$feature["Signal"] = $signal;
								$feature["StatusCode"] = "OK";
								
								printf("POSTING PROSES \r\n");
								//$content = json_encode($feature, JSON_NUMERIC_CHECK);          
								$content = json_encode($feature);
								printf("Data JSON : %s \r \n",$content);
								
							}
						
					}
					
			}
			
			//$feature["Message"] = "Post Data Has Been Successfully";
		}
		
		//echo json_encode($feature);
		exit;
	}
	
	function lastposition_indahkiat()
	{
		printf("PROSES POST INDAHKIAT -> SAP >> START \r\n");
		$url = "https://messageprocessinggb7076d5f.jp1.hana.ondemand.com/messageprocessing/api/Lacak/1/readings";
		$token = "361ccb79bd8b3c54fef436799e5589";
		
		$authorization = "Authorization: Bearer ".$token;
		
		$this->db->where("poststart_status",1);
		$this->db->where("poststart_company","INDAHKIAT");
		$this->db->where("poststart_status_post",1);
		$q = $this->db->get("poststart_status");
		$isport = $q->result();
		if($q->num_rows > 0)
		{
		for($z=0;$z<count($isport);$z++)
		{
			//all vehicle
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info,vehicle_imei");
			$this->db->where("vehicle_no",$isport[$z]->poststart_vehicle);
			$this->db->where("vehicle_status", 1);
			$qm = $this->db->get("vehicle");
			$rm = $qm->result();
			
			$x = 0;
			
			foreach($rm as $v)
			{
				$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
				$this->db->order_by("vehicle_device", "asc");
				$this->db->where("vehicle_imei", $v->vehicle_imei);
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
				$this->dbdata->where("gps_info_device", $v->vehicle_device);
				$this->dbdata->order_by("gps_time", "desc");
				$this->dbdata->limit(1);
				$q = $this->dbdata->get($tables['gps']);
				if($q->num_rows > 0)
				{
					$rows[] = $q->row();
					$trows = count($rows);
				}
				
				$feature = array();
			
				if(isset($trows) && count($trows)>0)
				{
					printf("ATTR %s \r\n",$v->vehicle_no);
					$x = $x + 1;
					for($i=0;$i<$trows;$i++)
					{
						if(isset($rows[$i]))
						{
							$da = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$dt = strtotime($da);
							$lat = 0; $lng = 0;
							$imei = $v->vehicle_imei;
							$nopol = $v->vehicle_no;
							//$kutip = \\"o\\";
							$feature["format"] = "lacak";
							$feature["version"] = "1.0";
							$feature["vehicleId"] = $imei;
							$feature["VehicleRegistrationNo"] = $nopol;
							$feature["timestamp"] = $dt*1000;
							$feature["readings"]["voltLevel"] = 0;
							$feature["readings"]["gsmSignal"] = 0;
							$feature["readings"]["speed"] = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
							$feature["readings"]["heading"] = $rows[$i]->gps_course;
							if($feature["readings"]["heading"] == null)
							{
								$feature["readings"]["heading"] = 0;
							}
							$feature["readings"]["mileage"] = 0;
							$feature["readings"]["mileageRange"] = 0;
							
							if (isset($rows[$i]->gps_info_io_port))
							{
								$ioport = $rows[$i]->gps_info_io_port;
								$feature["readings"]["statusEngine"] = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
								if($feature["readings"]["statusEngine"] == false)
								{
									$feature["readings"]["statusEngine"] = "OFF";
								}	
								else
								{
									$feature["readings"]["statusEngine"] = "ON";
								}
							}
							else
							{
								$feature["readings"]["statusEngine"] = "OFF";
							}
							
							if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309")
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
								
								if (isset($rows[$i]->gps_longitude_real))
								{
									$lng = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
								}
								if (isset($rows[$i]->gps_latitude_real))
								{
									$lat = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
								}
							}
							else
							{
								$lng = number_format($rows[$i]->gps_longitude, 4, ".", "");
								$lat = number_format($rows[$i]->gps_latitude, 4, ".", "");
							}
							
							
							$feature["GPS"]["coordinates"] = array($lng,$lat);
							$feature["GPS"]["status"] = "A";
							$feature["GPS"]["active"] = false;
							$feature["GPS"]["totalSatellite"] = 0;
						}
					}
				}
				

				printf("POSTING PROSES \r\n");
				//$content = json_encode($feature, JSON_NUMERIC_CHECK);          
				$content = json_encode($feature);
				printf("Data JSON : %s \r \n",$content);
				//print_r($content);exit;
									
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				//curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				//curl_setopt($curl, CURLOPT_USERPWD, "lacak:gxK82EoO4zSzqDzpjYJG");
		
				$json_response = curl_exec($curl);
				
				echo $json_response;
				echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
				printf("-------------------------- \r\n");
				unset($rowv);
				unset($rows);
				unset($feature);
				$trows = 0;
				
			}
		}
		}
		else
		{
			printf("NO VEHICLE IN RULE \r\n");
		}
		exit;		
	}
	
	function lastpositionderek()
	{
		header("Content-Type: application/json");
		
		$token = "CVaW5kNGhraTX0OnEVaNoxRkNHksdAWh7k0dA16";
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
			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			if($allvehicle == 0){
				$this->db->where("vehicle_no",$postdata->VehiclePlateNo);	
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
						printf("ATTR %s \r\n",$vehicle[$z]->vehicle_no);
						
							if(isset($rows))
							{
								if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
								{
									$da = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows->gps_time)));
								}else{
									$da = date("Y-m-d H:i:s", strtotime($rows->gps_time));
								}
								
								$dt = strtotime($da);
								$lat = 0; $lng = 0;
								$gpslocation = "";
								$feature["VehicleNo"] = $vehicle[$z]->vehicle_no;
								$feature["GPSTime"] = $da;
								$feature["Speed"] = number_format($rows->gps_speed*1.852, 0, "", ".");
								if (isset($rows->gps_info_io_port))
								{
									$ioport = $rows->gps_info_io_port;
									$feature["StatusEngine"] = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
									if($feature["StatusEngine"] == false)
									{
										$feature["StatusEngine"] = "OFF";
									}	
									else
									{
										$feature["statusEngine"] = "ON";
									}
								}
								else
								{
									$feature["statusEngine"] = "OFF";
								}
								
								$feature["Course"] = $rows->gps_course;
								
								if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315DOOR")
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
								$feature["Latitude"] = $lat;
								$feature["Longitude"] = $lng;
								
								if (isset($rows->gps_longitude))
								{
									if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315DOOR")
									{
										$gpslocation = $this->getPosition($rows->gps_longitude, $rows->gps_ew, $rows->gps_latitude, $rows->gps_ns);
									}
									else
									{
										$gpslocation = $this->getPosition_other($rows->gps_longitude, $rows->gps_latitude);
									}
									
								}
								
								$feature["Location"] = $gpslocation->display_name;
								
								if((isset($rows->gps_status) && ($rows->gps_status) == "A"))
								{
									$signal = "OK";
								}
								else
								{
									$signal = "NOT OK";
								}
								
								$feature["Signal"] = $signal;
								$feature["StatusCode"] = "OK";
								
								printf("POSTING PROSES \r\n");
								//$content = json_encode($feature, JSON_NUMERIC_CHECK);          
								$content = json_encode($feature);
								printf("Data JSON : %s \r \n",$content);
								
							}
						
					}
					
			}
			
			//$feature["Message"] = "Post Data Has Been Successfully";
		}
		
		//echo json_encode($feature);
		exit;
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
	
	function getlast_overspeed()
	{
		header("Content-Type: application/json");
		
		$token = "LCKaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
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
        
        if($headers != $token)
        {
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID TOKEN";
			echo json_encode($feature);
			exit;
		}
		
		$feature = array();
		
		if(!isset($postdata->OverSpeed) || $postdata->OverSpeed == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID OVERSPEED CONFIG";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID USER ID";
			echo json_encode($feature);
			exit;
		}else{
			
			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			
			$this->db->where("vehicle_status",1);
			$q = $this->db->get("vehicle_autocheck");
			
			$this->db->order_by("auto_vehicle_no","asc");
			if($this->sess->user_level == 1){
				$this->db->where("auto_user_id",$this->sess->user_id);
			}else if($this->sess->user_level == 2){
				$this->db->where("auto_vehicle_company",$this->sess->user_company);
			}else{
				$this->db->where("auto_user_id",0);
			}
			
			$this->db->where("auto_last_speed >=",$postdata->OverSpeed);
			$this->db->where("auto_flag",0);
			$q = $this->db->get("vehicle_autocheck");
			
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
		
		
		//echo json_encode($feature);
		exit;
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
	
	function lastpositionpbi_old()
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
								$feature["VehicleNo"] = $vehicle[$z]->vehicle_no;
								$feature["GPSTime"] = $da;
								$feature["Speed"] = number_format($rows->gps_speed*1.852, 0, "", ".");
								if (isset($rows->gps_info_io_port))
								{
									$statusengine = substr($rows->gps_info_io_port, 4, 1);
									if($statusengine == 1)
									{
										$feature["Engine"] = "ON";
									}
									else
									{
										$feature["Engine"] = "OFF";
									}
								}
								
								
								$feature["Course"] = $rows->gps_course;
								
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
								$feature["Latitude"] = $lat;
								$feature["Longitude"] = $lng;
								
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
								
								$feature["Location"] = $gpslocation->display_name;
								$feature["Geofence"] = $geofence;
								
								if((isset($rows->gps_status) && ($rows->gps_status) == "A"))
								{
									$signal = "OK";
								}
								else
								{
									$signal = "NOT OK";
								}
								
								$feature["Signal"] = $signal;
								$feature["StatusCode"] = "OK";
								
								//printf("POSTING PROSES \r\n");
								//$content = json_encode($feature, JSON_NUMERIC_CHECK);          
								$content = json_encode($feature);
								//printf("Data JSON : %s \r \n",$content);
								echo json_encode($feature);
								
							}
						
					}
					
			}
			
			//$feature["Message"] = "Post Data Has Been Successfully";
		}
		
		//echo json_encode($feature);
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
	
	function postsjpbi()
	{
		//printf("PROSES POST SAMPLE -> PBI >> POST SJ \r\n");
		
		$token = "PBIaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
		$authorization = "Authorization:".$token;
		$url = "http://api.lacak-mobil.com/api/sjpbi";
		$feature = array();
		
		$feature["UserId"] = 1147; //pbi
		$feature["VehiclePlateNo"] = "A 8036 H"; //tes
		$feature["Driver"] = "Joni";
		$feature["SJNo"] = "SJ-12346";
		$feature["DINo"] = "DI-12346";
		$feature["SJDate"] = date('Y-m-d');
		$feature["UJDate"] = date('Y-m-d H:i:s');
		
		$feature["Category"] = "CAT-123456";
		$feature["Item"] = "ITEM-123456";
		/*
		$feature["CustCode1"] = "TR002";//code customer 1
		$feature["CustName1"] = "TRIAL2";
		$feature["CustUnique1"] = "A112";
		
		$feature["CustCode2"] = "TR003";//code customer 2
		$feature["CustName2"] = "TRIAL3";
		$feature["CustUnique2"] = "A113";
		
		$feature["CustCode3"] = "TR004";//code customer 3
		$feature["CustName3"] = "TRIAL4";
		$feature["CustUnique3"] = "A114";
		*/
		
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
		//curl_setopt($curl, CURLOPT_USERPWD, "ind4hki4t:p05td4t4ki4t");
		

		$json_response = curl_exec($curl);
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		printf("-------------------------- \r\n");
		
		exit;
	}
	
	function sjpbi()
	{
		ini_set('display_errors', 1);
		header("Content-Type: application/json");
		
		$token = "PBIaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
		$postdata = json_decode(file_get_contents("php://input"));
		
		$vehicle_device = "";
		$user_company = "";
		
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
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) 
            {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
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
			//cek dari db master user
			$this->db->select("user_id,user_company");
			$this->db->where("user_id",$postdata->UserId);
			$q = $this->db->get("user");
			if($q->num_rows == 0)
			{
				$feature["StatusCode"] = "FAILED";
				$feature["Message"] = "USER ID NOT FOUND";
				echo json_encode($feature);
				exit;
			}else{
				$user = $q->row();
				$user_company = $user->user_company;
			}
		}
		
		if(!isset($postdata->VehiclePlateNo) || $postdata->VehiclePlateNo == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID VEHICLE ID";
			echo json_encode($feature);
			exit;
		}else{
			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			$this->db->where("vehicle_no",$postdata->VehiclePlateNo);
			$this->db->where("vehicle_user_id",$postdata->UserId);
			$this->db->where("vehicle_status <>",3);
			$q = $this->db->get("vehicle");
			if($q->num_rows == 0)
			{
				$feature["StatusCode"] = "FAILED";
				$feature["Message"] = "VEHICLE NOT FOUND";
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->row();
				$vehicle_device = $vehicle->vehicle_device;
				$vehicle_no = $vehicle->vehicle_no;
				$vehicle_company = $vehicle->vehicle_company;
				$vehicle_group = $vehicle->vehicle_group;
			}
		}
		
		if(!isset($postdata->Driver) || $postdata->Driver == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID DRIVER";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->SJNo) || $postdata->SJNo == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID NO SJ";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->DINo) || $postdata->DINo == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID NO DI";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->SJDate) || $postdata->SJDate == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID SJ DATE";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->UJDate) || $postdata->UJDate == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID UJ DATE";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->Category) || $postdata->Category == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID UJ DATE";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->Item) || $postdata->Item == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID UJ DATE";
			echo json_encode($feature);
			exit;
		}
		
		//cust
		/*
		if(!isset($postdata->CustCode1) || $postdata->CustCode1 == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID CUSTOMER CODE 1";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->CustName1) || $postdata->CustName1 == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID CUSTOMER NAME 1";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->CustUnique1) || $postdata->CustUnique1 == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID CUSTOMER 1 UNIQUE KEY ";
			echo json_encode($feature);
			exit;
		}
		*/
		
		//print_r($sj_vehicle);exit();
		
		//Sudah ada blm di table sj
		if(isset($vehicle_device) && ($vehicle_device != "")) {
			
			//cek master cust
			$fib_customer_status = 0;
			$fib_customer2_status = 0;
			$fib_customer3_status = 0;
			
			/*
			if(isset($postdata->CustCode1) && ($postdata->CustCode1 != "")){
				$this->db->order_by("customer_id","desc");
				$this->db->where("customer_code",$postdata->CustCode1);
				$this->db->where("customer_unique",$postdata->CustUnique1);
				$q = $this->db->get("fib_cust");
				$row = $q->row();
				if(count($row)>0){
					//jika sudah ada UPDATE DATA CUSTOMER
					unset($data_cust);
					$data_cust["customer_code"] = $postdata->CustCode1;
					$data_cust["customer_name"] = $postdata->CustName1;
					$data_cust["customer_unique"] = $postdata->CustUnique1;
					$data_cust["customer_api"] = 1;
					$data_cust["customer_api_modified"] = date('Y-m-d H:i:s',strtotime($postdata->DateTime));
					$data_cust["customer_creator"] = $postdata->UserId;
					$data_cust["customer_status"] = 1;
					if($postdata->UserId == 860){
						$data_cust["customer_company"] = 38;
					}
					$data_cust["customer_flag"] = 0;
					$this->db->limit(1);
					$this->db->where("customer_code",$postdata->CustCode1);
					$this->db->where("customer_unique",$postdata->CustUnique1);
					$this->db->update("fib_cust",$data_cust);
					$fib_customer_status = 1;
					
				}else{
					//insert jika tidak ada
					unset($data_cust);
					$data_cust["customer_code"] = $postdata->CustCode1;
					$data_cust["customer_name"] = $postdata->CustName1;
					$data_cust["customer_unique"] = $postdata->CustUnique1;
					$data_cust["customer_api"] = 1;
					$data_cust["customer_api_modified"] = date('Y-m-d H:i:s',strtotime($postdata->DateTime));
					$data_cust["customer_creator"] = $postdata->UserId;
					$data_cust["customer_status"] = 1;
					if($postdata->UserId == 860){
						$data_cust["customer_company"] = 38;
					}
					$data_cust["customer_flag"] = 0;
					$this->db->insert("fib_cust",$data_cust);
					$fib_customer_status = 1;
				}
			}
			
			if(isset($postdata->CustCode2) && ($postdata->CustCode2 != "")){
				$this->db->order_by("customer_id","desc");
				$this->db->where("customer_code",$postdata->CustCode2);
				$this->db->where("customer_unique",$postdata->CustUnique2);
				$q = $this->db->get("fib_cust");
				$row = $q->row();
				if(count($row)>0){
					//jika sudah ada UPDATE DATA CUSTOMER
					unset($data_cust);
					$data_cust["customer_code"] = $postdata->CustCode2;
					$data_cust["customer_name"] = $postdata->CustName2;
					$data_cust["customer_unique"] = $postdata->CustUnique2;
					$data_cust["customer_api"] = 1;
					$data_cust["customer_api_modified"] = date('Y-m-d H:i:s',strtotime($postdata->DateTime));
					$data_cust["customer_creator"] = $postdata->UserId;
					$data_cust["customer_status"] = 1;
					if($postdata->UserId == 860){
						$data_cust["customer_company"] = 38;
					}
					$data_cust["customer_flag"] = 0;
					$this->db->limit(1);
					$this->db->where("customer_code",$postdata->CustCode2);
					$this->db->where("customer_unique",$postdata->CustUnique2);
					$this->db->update("fib_cust",$data_cust);
					$fib_customer2_status = 1;
				}else{
					//insert jika tidak ada
					unset($data_cust);
					$data_cust["customer_code"] = $postdata->CustCode2;
					$data_cust["customer_name"] = $postdata->CustName2;
					$data_cust["customer_unique"] = $postdata->CustUnique2;
					$data_cust["customer_api"] = 1;
					$data_cust["customer_api_modified"] = date('Y-m-d H:i:s',strtotime($postdata->DateTime));
					$data_cust["customer_creator"] = $postdata->UserId;
					$data_cust["customer_status"] = 1;
					if($postdata->UserId == 860){
						$data_cust["customer_company"] = 38;
					}
					$data_cust["customer_flag"] = 0;
					$this->db->insert("fib_cust",$data_cust);
					$fib_customer2_status = 1;
				}
			}
			
			if(isset($postdata->CustCode3) && ($postdata->CustCode3 != "")){
				$this->db->order_by("customer_id","desc");
				$this->db->where("customer_code",$postdata->CustCode3);
				$this->db->where("customer_unique",$postdata->CustUnique3);
				$q = $this->db->get("fib_cust");
				$row = $q->row();
				if(count($row)>0){
					//jika sudah ada UPDATE DATA CUSTOMER
					unset($data_cust);
					$data_cust["customer_code"] = $postdata->CustCode3;
					$data_cust["customer_name"] = $postdata->CustName3;
					$data_cust["customer_unique"] = $postdata->CustUnique3;
					$data_cust["customer_api"] = 1;
					$data_cust["customer_api_modified"] = date('Y-m-d H:i:s',strtotime($postdata->DateTime));
					$data_cust["customer_creator"] = $postdata->UserId;
					$data_cust["customer_status"] = 1;
					if($postdata->UserId == 860){
						$data_cust["customer_company"] = 38;
					}
					$data_cust["customer_flag"] = 0;
					$this->db->limit(1);
					$this->db->where("customer_code",$postdata->CustCode3);
					$this->db->where("customer_unique",$postdata->CustUnique3);
					$this->db->update("fib_cust",$data_cust);
					$fib_customer3_status = 1;
				}else{
					//insert jika tidak ada
					unset($data_cust);
					$data_cust["customer_code"] = $postdata->CustCode3;
					$data_cust["customer_name"] = $postdata->CustName3;
					$data_cust["customer_unique"] = $postdata->CustUnique3;
					$data_cust["customer_api"] = 1;
					$data_cust["customer_api_modified"] = date('Y-m-d H:i:s',strtotime($postdata->DateTime));
					$data_cust["customer_creator"] = $postdata->UserId;
					$data_cust["customer_status"] = 1;
					if($postdata->UserId == 860){
						$data_cust["customer_company"] = 38;
					}
					$data_cust["customer_flag"] = 0;
					$this->db->insert("fib_cust",$data_cust);
					$fib_customer3_status = 1;
				}
			}
			
			*/
			// cek data SJ
			$this->db->where("sj_vehicle_device",$vehicle_device);
			$this->db->where("sj_sj_no",$postdata->SJNo);
			$this->db->where("sj_status",1);
			$q = $this->db->get("sj");
			
			//jika belum ada insert
			if($q->num_rows == 0)
			{
				unset($data);
				$data["sj_user_id"] = $postdata->UserId;
				$data["sj_user_company"] = $user_company;
				$data["sj_vehicle_device"] = $vehicle_device;
				$data["sj_vehicle_no"] = $vehicle_no;
				$data["sj_driver"] = $postdata->Driver;
				$data["sj_sj_no"] = $postdata->SJNo;
				$data["sj_di_no"] = $postdata->DINo;
				$data["sj_sj_date"] = date('Y-m-d',strtotime($postdata->SJDate));
				$data["sj_uj_date"] = date('Y-m-d H:i:s',strtotime($postdata->UJDate));
				$data["sj_category"] = $postdata->Category;
				$data["sj_item"] = $postdata->Item;
				
				/*$data["sj_cust_1_code"] = $postdata->CustCode1;
				$data["sj_cust_2_code"] = $postdata->CustCode2;
				$data["sj_cust_3_code"] = $postdata->CustCode3;
				$data["sj_cust_1_name"] = $postdata->CustName1;
				$data["sj_cust_2_name"] = $postdata->CustName2;
				$data["sj_cust_3_name"] = $postdata->CustName3;
				$data["sj_cust_1_unique"] = $postdata->CustUnique1;
				$data["sj_cust_2_unique"] = $postdata->CustUnique2;
				$data["sj_cust_3_unique"] = $postdata->CustUnique3;*/
				
				$data["sj_flag"] = 0;
				$data["sj_isread"] = 0;
				$data["sj_api"] = 1;
				$data["sj_api_modified"] = date('Y-m-d H:i:s');
				$this->db->insert("sj",$data);
				
				//disable sampai master fib ada
				
				unset($data_fib);
				$data_fib["fib_noso"] = $postdata->SJNo;
				$data_fib["fib_noso_status"] = 1;
				$data_fib["fib_remark"] = "";
				$data_fib["fib_remark_status"] = 1;
				
				/*
				$data_fib["fib_customer"] = $postdata->CustName1;
				$data_fib["fib_customer_code"] = $postdata->CustCode1;
				$data_fib["fib_customer_status"] = $fib_customer_status;
				$data_fib["fib_customer_unique"] = $postdata->CustUnique1;
				$data_fib["fib_customer2"] = $postdata->CustName2;
				$data_fib["fib_customer2_code"] = $postdata->CustCode2;
				$data_fib["fib_customer2_status"] = $fib_customer2_status;
				$data_fib["fib_customer2_unique"] = $postdata->CustUnique2;
				$data_fib["fib_customer3"] = $postdata->CustName3;
				$data_fib["fib_customer3_code"] = $postdata->CustCode3;
				$data_fib["fib_customer3_status"] = $fib_customer3_status;
				$data_fib["fib_customer3_unique"] = $postdata->CustUnique3;
				*/
				$data_fib["fib_sj"] = date('Y-m-d H:i:s',strtotime($postdata->SJDate));
				$data_fib["fib_sj_status"] = 1;
				$data_fib["fib_uj"] = date('Y-m-d H:i:s',strtotime($postdata->UJDate));
				$data_fib["fib_uj_status"] = 1;
				
				$data_fib["fib_co_ischeck"] = 0;
				$this->db->limit(1);
				$this->db->where("fib_vehicle",$vehicle_device);
				$this->db->update("fib",$data_fib);
				
				$feature["Message"] = "Insert Data SJ Has Been Successfully";
				
			}
			//jika sudah ada update
			else
			{
				unset($data);
				$data["sj_user_id"] = $postdata->UserId;
				$data["sj_user_company"] = $user_company;
				$data["sj_vehicle_device"] = $vehicle_device;
				$data["sj_vehicle_no"] = $vehicle_no;
				$data["sj_driver"] = $postdata->Driver;
				$data["sj_sj_no"] = $postdata->SJNo;
				$data["sj_di_no"] = $postdata->DINo;
				$data["sj_sj_date"] = date('Y-m-d',strtotime($postdata->SJDate));
				$data["sj_uj_date"] = date('Y-m-d H:i:s',strtotime($postdata->UJDate));
				$data["sj_category"] = $postdata->Category;
				$data["sj_item"] = $postdata->Item;
				
				$data["sj_flag"] = 0;
				$data["sj_isread"] = 0;
				$data["sj_api"] = 1;
				$data["sj_api_modified"] = date('Y-m-d H:i:s');
				$this->db->limit(1);
				$this->db->where("sj_vehicle_device",$vehicle_device);
				$this->db->where("sj_sj_no",$postdata->SJNo);
				$this->db->update("sj",$data);
				
				
				unset($data_fib);
				$data_fib["fib_noso"] = $postdata->SJNo;
				$data_fib["fib_noso_status"] = 1;
				$data_fib["fib_remark"] = "";
				$data_fib["fib_remark_status"] = 1;
				
				/*
				$data_fib["fib_customer"] = $postdata->CustName1;
				$data_fib["fib_customer_code"] = $postdata->CustCode1;
				$data_fib["fib_customer_status"] = $fib_customer_status;
				$data_fib["fib_customer_unique"] = $postdata->CustUnique1;
				$data_fib["fib_customer2"] = $postdata->CustName2;
				$data_fib["fib_customer2_code"] = $postdata->CustCode2;
				$data_fib["fib_customer2_status"] = $fib_customer2_status;
				$data_fib["fib_customer2_unique"] = $postdata->CustUnique2;
				$data_fib["fib_customer3"] = $postdata->CustName3;
				$data_fib["fib_customer3_code"] = $postdata->CustCode3;
				$data_fib["fib_customer3_status"] = $fib_customer3_status;
				$data_fib["fib_customer3_unique"] = $postdata->CustUnique3;
				*/
				
				$data_fib["fib_sj"] = date('Y-m-d H:i:s',strtotime($postdata->SJDate));
				$data_fib["fib_sj_status"] = 1;
				$data_fib["fib_uj"] = date('Y-m-d H:i:s',strtotime($postdata->UJDate));
				$data_fib["fib_uj_status"] = 1;
				
				$data_fib["fib_co_ischeck"] = 0;
				$this->db->limit(1);
				$this->db->where("fib_vehicle",$vehicle_device);
				$this->db->update("fib",$data_fib);
				
				$feature["Message"] = "Update Data SJ Has Been Successfully";
			}
			$feature["StatusCode"] = "OK";
			
		}
		
		echo json_encode($feature);
		exit;
	}
	
	function postsj_selesai()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");
		
		$token = "PBIaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
		$authorization = "Authorization:".$token;
		$url = "http://api.lacak-mobil.com/api/sjpbiselesai";
		$feature = array();
		
		$feature["UserId"] = 1147; //pbi
		$feature["VehiclePlateNo"] = "A 8036 H";
		$feature["SJNo"] = "SJ-12346";
		$feature["Status"] = "selesai";
		
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
	
	function sjpbiselesai()
	{
		ini_set('display_errors', 1);
		header("Content-Type: application/json");
		
		$token = "PBIaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
		$postdata = json_decode(file_get_contents("php://input"));
		
		$vehicle_device = "";
		$user_company = "";
		
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
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) 
            {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
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
			//cek dari db master user
			$this->db->select("user_id,user_company");
			$this->db->where("user_id",$postdata->UserId);
			$q = $this->db->get("user");
			if($q->num_rows == 0)
			{
				$feature["StatusCode"] = "FAILED";
				$feature["Message"] = "USER ID NOT FOUND";
				echo json_encode($feature);
				exit;
			}else{
				$user = $q->row();
				$user_company = $user->user_company;
			}
		}
		
		if(!isset($postdata->SJNo) || $postdata->SJNo == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID NO SJ";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->VehiclePlateNo) || $postdata->VehiclePlateNo == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID VEHICLE ID";
			echo json_encode($feature);
			exit;
		}else{
			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			$this->db->where("vehicle_no",$postdata->VehiclePlateNo);
			$this->db->where("vehicle_user_id",$postdata->UserId);
			$this->db->where("vehicle_status <>",3);
			$q = $this->db->get("vehicle");
			if($q->num_rows == 0)
			{
				$feature["StatusCode"] = "FAILED";
				$feature["Message"] = "VEHICLE NOT FOUND";
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->row();
				$vehicle_device = $vehicle->vehicle_device;
				$vehicle_no = $vehicle->vehicle_no;
				$vehicle_company = $vehicle->vehicle_company;
				$vehicle_group = $vehicle->vehicle_group;
			}
		}
		
		if(!isset($postdata->Status) || $postdata->Status == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID DATA STATUS";
			echo json_encode($feature);
			exit;
		}else{
			if($postdata->Status != "selesai"){
				$feature["StatusCode"] = "FAILED";
				$feature["Message"] = "INVALID STATUS SELECTED";
				echo json_encode($feature);
				exit;
			}
		}
		
		//Sudah ada blm di table sj
		if(isset($vehicle_device) && ($vehicle_device != "")) {
			
			// cek data SJ
			$this->db->where("sj_vehicle_device",$vehicle_device);
			$this->db->where("sj_sj_no",$postdata->SJNo);
			$this->db->where("sj_status",1);
			$q = $this->db->get("sj");
			
			//jika belum ada insert
			if($q->num_rows == 0)
			{
				$feature["StatusCode"] = "FAILED";
				$feature["Message"] = "NO DATA SJ";
				echo json_encode($feature);
				exit;
				
			}
			//jika sudah ada update
			else
			{
				
				unset($data);
				$data["sj_status"] = 2;
				$data["sj_api_completed"] = date('Y-m-d H:i:s');
				$this->db->where("sj_vehicle_device",$vehicle_device);
				$this->db->where("sj_sj_no",$postdata->SJNo);
				$this->db->update("sj",$data);
				
				//update master fib
				unset($data_fib);
				$data_fib["fib_sj_status"] = 0;
				$data_fib["fib_noso_status"] = 0;
				$data_fib["fib_remark"] = "";
				$data_fib["fib_remark_status"] = 0;
				$data_fib["fib_uj_status"] = 0;
				
				$this->db->limit(1);
				$this->db->where("fib_vehicle",$vehicle_device);
				$this->db->update("fib",$data_fib);
				
				$feature["StatusCode"] = "OK";
				$feature["Message"] = "Update Status SJ Has Been Successfully";
			}
			
		}
		
		echo json_encode($feature);
		exit;
	}

	function tes_blsplg()
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");
		
		$token = "BLSaW5kNGhraT1877sBhskanAwNXRkNHQ0a2k0dA16";
		$authorization = "Authorization:".$token;
		$url = "http://api.lacak-mobil.com/api/lastposition_blsplg";
		$feature = array();
		
		$feature["UserId"] = 3398;
		$feature["VehiclePlateNo"] = "GPS 1"; 
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
	
	function lastposition_blsplg()
	{
		//ini_set('display_errors', 1);
		header("Content-Type: application/json");
		
		$token = "BLSaW5kNGhraT1877sBhskanAwNXRkNHQ0a2k0dA16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$now = date("Ymd");
		$googleapi = "";
		
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
			else
			{
				$rowapi = $q->row();
				$googleapi = $rowapi->api_googlemap;
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
			//$this->db->where("vehicle_active_date2 >",$now); //tidak expired
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
								if(!in_array($vehicle[$z]->vehicle_type, $this->config->item('vehicle_others')))
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
								
								if(!in_array($vehicle[$z]->vehicle_type, $this->config->item('vehicle_others')))
								{
										if (isset($rows->gps_longitude_real))
										{
											$lng = number_format($rows->gps_longitude_real, 4, ".", "");
										}
										if (isset($rows->gps_latitude_real))
										{
											$lat = number_format($rows->gps_latitude_real, 4, ".", "");
										}
										
										if($vehicle[$z]->vehicle_type == "TJAM")
										{
											$lat = "-".$lat;
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
									$gpslocation = $this->gpsmodel->getLocation_byGeoCode($lat, $lng, $googleapi);
									$geofence = $this->getGeofence_location_other($rows->gps_longitude_real, $rows->gps_latitude_real, $rowvehicle->vehicle_user_id);
										
								}
								
								$DataToUpload[$z]->Location = $gpslocation->display_name;
								//$DataToUpload[$z]->Geofence = $geofence;
								
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
