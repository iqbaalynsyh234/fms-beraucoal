<?php
include "base.php";

class Linfox extends Base {

	function Linfox()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}
	
	function testasep()
	{
		printf("PROSES POST GPS ASEP -> TEST >> START \r\n");

		//$url = "http://logtracker.marvacipta.co.id/application/api/location/";
		//$url = "http://lt-updater-proxy.herokuapp.com/application/api/location/";
		
		$userid = 3409; //user id pins
		$idate = date("d M Y H:i");
		$idate_utc = date("d M Y H:i", strtotime("-7 hour", strtotime($idate)));
		
		$datajson = array();
		$devices = array("002100001711@T5","002100001723@T5","002100001669@T5","006100001556@T5","006100001558@T5");
				
		//all vehicle
		$this->db->group_by("vehicle_device");
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_user_id", $userid);
		$this->db->where_in("vehicle_device", $devices);
		//$this->db->limit(10);
		//$this->db->where("vehicle_device","352312090032429@TK309");
		
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			unset($datajson);
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
			$table = "gps";
			$tableinfo = "gps_info";
			
			$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->dbdata->where("gps_info_device", $v->vehicle_device);
			$this->dbdata->order_by("gps_time", "desc");
			$this->dbdata->limit(1);
			$q = $this->dbdata->get($tables['gps']);
			if($q->num_rows > 0)
			{
				$rows = $q->result();
				$trows = count($rows);
				
				printf("GET LOCATION ATTRIBUTE START : %s \r\n",$v->vehicle_no);
				
				for($i=0;$i<$trows;$i++)
				{
					if(isset($rows[$i]->gps_time) && $rows[$i]->gps_time != "")
					{				
						
						//revisi budy (2018 03 01)
						$id = explode("@",$v->vehicle_device);
						$deviceid = $id[0];
						$da = date("Y-m-d H:i:s", strtotime($rows[$i]->gps_time));
						$dt = strtotime($da);
						
						
						
						if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309")
						{
							if (isset($rows[$i]->gps_longitude))
							{
								$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
							}
						
							if (isset($rows[$i]->gps_latitude))
							{
								$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							if (isset($rows[$i]->gps_latitude_real))
							{
								$datajson["latitude"] = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
							}
				
							if (isset($rows[$i]->gps_longitude_real))
							{
								
								$datajson["longitude"] = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
							}
						}
						else
						{
							$datajson["latitude"] = number_format($rows[$i]->gps_latitude, 4, ".", "");
							$datajson["longitude"] = number_format($rows[$i]->gps_longitude, 4, ".", "");
						}
						$datajson["speed"] = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
						$datajson["time"] = $dt;
					}
				}
				
				$url = "http://lt-updater-proxy.herokuapp.com/application/api/location/";
				$url = $url.$deviceid."/update";
				//$datajson = array("latitude"=>$dlat, "longitude"=>$dlong, "speed"=>$dspeed);
				
				printf("POSTING PROSES : %s \r\n",$v->vehicle_no);
				printf("POSTING URL : %s \r\n",$url);
				//print_r($datajson);
				
				$request_headers = array("Content-Type"=>"application/json","L-IMEI"=>$deviceid,"Accept"=>"application/json,text/plain,text/html");
				//$datajson = array("latitude"=>$dlat,"longitude"=>$dlong,"speed"=>$dspeed);
				
				$datajson = json_encode($datajson);
				printf("DATA : %s \r\n",$datajson);

				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, true);
				//curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);
				curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $datajson);
		
				$json_response = curl_exec($curl);
				
				echo $json_response;
				echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
				$url = "http://lt-updater-proxy.herokuapp.com/application/api/location/";
				printf("POSTING DONE : %s \r\n",$v->vehicle_no);
			}
			else
			{
				printf("DATA UNAVAILABLE : %s \r\n",$v->vehicle_no);
			}
		}
		exit;
	}
	
	function smr()
	{
		printf("PROSES POST LINFOX -> SMR >> START \r\n");
		//$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RestServices/GPSDataService.svc";
		$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RESTServices/GPSDataService.svc/Upload";
		$username =  "Lacak";
		$password = "P@ssw0rd";
		$provider = "Lacak";
		$userid = 3102; //user id sms_

		$idate = date("d M Y H:i");
		$idate_utc = date("d M Y H:i", strtotime("-7 hour", strtotime($idate)));
		
		/*$idate = date("Y-m-d H:i:s");
		$idate_utc = date("Y-m-d H:i:s", strtotime("-8 hour", strtotime($idate)));*/
		
		
		$datajson = array();
		$datajson["UserName"] = $username;
		$datajson["Password"] = $password;
		$datajson["GPSProvider"] = $provider;
		
		//all vehicle
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $userid);
		//$this->db->where("vehicle_device","061453812412@T8");
		//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		//$this->db->where("vehicle_no <>","B 9483 FRU");
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
			$table = "gps";
			$tableinfo = "gps_info";
			
			$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->dbdata->where("gps_info_device", $v->vehicle_device);
			$this->dbdata->order_by("gps_time", "desc");
			$this->dbdata->limit(1);
			$q = $this->dbdata->get($tables['gps']);
			if($q->num_rows > 0)
			{
				$rows = $q->result();
				$trows = count($rows);
				$DataToUpload = array();
				unset($DataToUpload);
				printf("GET LOCATION ATTRIBUTE START : %s \r\n",$v->vehicle_no);
				
				for($i=0;$i<$trows;$i++)
				{
					if(isset($rows[$i]->gps_time) && $rows[$i]->gps_time != "")
					{				
						/*$id = explode("@",$rm[$i]->vehicle_device);
						$DataToUpload[$i]->DeviceId = $id[0];
						$DataToUpload[$i]->VehicleNumber = $rm[$i]->vehicle_no;
						*/
						
						//revisi budy (2018 03 01)
						$id = explode("@",$v->vehicle_device);
						$DataToUpload[$i]->DeviceId = $id[0];
						$DataToUpload[$i]->VehicleNumber = $v->vehicle_no;
						
						$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime($rows[$i]->gps_time));
						$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
						
						
						/*$DataToUpload[$i]->GpsDateTime = date("Y-m-d H:i:s", strtotime($rows[$i]->gps_time));
						$DataToUpload[$i]->GpsDateTimeUtc = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));*/
						
			
						if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309")
						{
							if (isset($rows[$i]->gps_longitude))
							{
								$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
							}
						
							if (isset($rows[$i]->gps_latitude))
							{
								$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							if (isset($rows[$i]->gps_latitude_real))
							{
								$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
							}
				
							if (isset($rows[$i]->gps_longitude_real))
							{
								$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
							}
						}
						else
						{
							$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude, 4, ".", "");
							$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude, 4, ".", "");
						}
			
						if (isset($rows[$i]->gps_longitude))
						{
							if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309")
							{
								$result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
							else
							{
								$result_position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							}
						
							$DataToUpload[$i]->Location = $result_position->display_name;
						}
						$DataToUpload[$i]->Speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
					}
					$DataToUpload[$i]->TransferDateTime =  $idate;
					$DataToUpload[$i]->TransferDateTimeUtc = $idate_utc;
				}
			
				$datajson["DataToUpload"] = $DataToUpload;
				$content = json_encode($datajson);
				//$content = json_encode($datajson,JSON_NUMERIC_CHECK); 
				printf("Data JSON : %s \r \n",$content);//exit;
				
				printf("POSTING PROSES : %s \r\n",$v->vehicle_no);
								
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				//curl_setopt($curl, CURLOPT_USERPWD, "Lacak:P@ssw0rd");
		
				$json_response = curl_exec($curl);
				
				echo $json_response;
				echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
				printf("POSTING DONE : %s \r\n",$v->vehicle_no);
			}
			else
			{
				printf("DATA UNAVAILABLE : %s \r\n",$v->vehicle_no);
			}
		}
		exit;
	}
	
	function smr_backup()
	{
		printf("PROSES POST LINFOX -> SMR >> START \r\n");
		//$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RestServices/GPSDataService.svc";
		$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RESTServices/GPSDataService.svc/Upload";
		$username =  "Lacak";
		$password = "P@ssw0rd";
		$provider = "Lacak";
		$userid = 3102; //user id sms_
		$idate = date("d M Y H:i");
		$idate_utc = date("d M Y H:i", strtotime("-7 hour", strtotime($idate)));
		
		
		$datajson = array();
		$datajson["UserName"] = $username;
		$datajson["Password"] = $password;
		$datajson["GPSProvider"] = $provider;
		
		//all vehicle
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $userid);
		//$this->db->where("vehicle_device","061453812412@T8");
		//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		//$this->db->where("vehicle_no <>","B 9483 FRU");
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
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
			}
		}
		
		
		$trows = count($rows);
		
		$DataToUpload = array();
		
		printf("GET LOCATION ATTRIBUTE START \r\n");
		for($i=0;$i<$trows;$i++)
		{
			if(isset($rows[$i]->gps_time) && $rows[$i]->gps_time != "")
			{
				
			$id = explode("@",$rm[$i]->vehicle_device);
			
			$DataToUpload[$i]->DeviceId = $id[0];
			$DataToUpload[$i]->VehicleNumber = $rm[$i]->vehicle_no;
			//$DataToUpload[$i]->vehicle_name = $rm[$i]->vehicle_name;
			$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime($rows[$i]->gps_time));
			$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
			
			
			if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309")
			{
				if (isset($rows[$i]->gps_longitude))
				{
					$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
				}
				
				if (isset($rows[$i]->gps_latitude))
				{
					$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
				}
				
				if (isset($rows[$i]->gps_latitude_real))
				{
					$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
				}
				
				if (isset($rows[$i]->gps_longitude_real))
				{
					$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
				}
				
			}
			else
			{
				$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude, 4, ".", "");
				$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude, 4, ".", "");
			}
			
			
			if (isset($rows[$i]->gps_longitude))
			{
				if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309")
				{
					$result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
				}
				else
				{
					$result_position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
				}
				
				$DataToUpload[$i]->Location = $result_position->display_name;
			}
			
			$DataToUpload[$i]->Speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			}
			
			$DataToUpload[$i]->TransferDateTime =  $idate;
			$DataToUpload[$i]->TransferDateTimeUtc = $idate_utc;
		}
		
		
		$datajson["DataToUpload"] = $DataToUpload;
		$content = json_encode($datajson);
		//$content = json_encode($datajson,JSON_NUMERIC_CHECK); 
		//printf("Data JSON : %s \r \n",$content);exit;
		         
		
		printf("POSTING PROSES \r\n");
		                    
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "Lacak:P@ssw0rd");
	
		$json_response = curl_exec($curl);
		
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		exit;
		
	}
	
	function smr_new()
	{
		printf("PROSES POST LINFOX -> SMR >> START \r\n");
		date_default_timezone_set("Asia/Bangkok");
		$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RESTServices/GPSDataService.svc/Upload";
		$username =  "Lacak";
		$password = "P@ssw0rd";
		$provider = "Lacak";
		$userid = 3102; //user id smr

		$idate = date("d M Y H:i");
		$idate_utc = date("d M Y H:i", strtotime("-7 hour", strtotime($idate)));
		
		$datajson = array();
		$datajson["UserName"] = $username;
		$datajson["Password"] = $password;
		$datajson["GPSProvider"] = $provider;
		
		//all vehicle T5/T8
		printf("STARTING VEHICLE PRIME \r\n");
		$type_main = array("T8_2","T5","T5SILVER");
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_device <>", "352312090341960@TK315");
		$this->db->where("vehicle_device <>", "1453843108@A13");
		$this->db->where("vehicle_device <>", "1453843091@A13");
		$this->db->where("vehicle_device <>", "1453843057@A13");
		$this->db->where("vehicle_device <>", "352312090342521@TK315");
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$this->db->where_in("vehicle_type",$type_main);
		$this->db->where("vehicle_status <>", 3);
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
			$table = "gps";
			$tableinfo = "gps_info";
			
			$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->dbdata->where("gps_info_device", $v->vehicle_device);
			$this->dbdata->order_by("gps_time", "desc");
			$this->dbdata->limit(1);
			$q = $this->dbdata->get($tables['gps']);
			if($q->num_rows > 0)
			{
				$rows = $q->result();
				$trows = count($rows);
				$DataToUpload = array();
				unset($DataToUpload);
				printf("GET LOCATION ATTRIBUTE START : %s \r\n",$v->vehicle_no);
				
				for($i=0;$i<$trows;$i++)
				{
					if(isset($rows[$i]->gps_time) && $rows[$i]->gps_time != "")
					{	
						date_default_timezone_set("Asia/Bangkok");		
						//revisi budy (2018 03 01)
						$id = explode("@",$v->vehicle_device);
						$DataToUpload[$i]->DeviceId = $id[0];
						$DataToUpload[$i]->VehicleNumber = $v->vehicle_no;
						
						if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
						{
							$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime($rows[$i]->gps_time));
							$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							
							$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime($rows[$i]->gps_time)); 
						}
						//print_r($rows[$i]->gps_time." ".$DataToUpload[$i]->GpsDateTime." ".$DataToUpload[$i]->GpsDateTimeUtc);exit();
						
						if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
						{
							$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude, 4, ".", "");
							$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude, 4, ".", "");
							
						}
						else
						{
							if (isset($rows[$i]->gps_longitude))
							{
								$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
							}
						
							if (isset($rows[$i]->gps_latitude))
							{
								$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							if (isset($rows[$i]->gps_latitude_real))
							{
								$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
							}
				
							if (isset($rows[$i]->gps_longitude_real))
							{
								$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
							}
						}
			
						if (isset($rows[$i]->gps_longitude))
						{
							if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
							{
								
								$result_position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							}
							else
							{
								$result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							$DataToUpload[$i]->Location = $result_position->display_name;
						}
						$DataToUpload[$i]->Speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
					}
					$DataToUpload[$i]->TransferDateTime =  $idate;
					$DataToUpload[$i]->TransferDateTimeUtc = $idate_utc;
				}
			
				$datajson["DataToUpload"] = $DataToUpload;
				$content = json_encode($datajson);
				//$content = json_encode($datajson,JSON_NUMERIC_CHECK); 
				printf("Data JSON : %s \r \n",$content);//exit;
				
				printf("POSTING PROSES : %s ",$v->vehicle_no);
								
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				//curl_setopt($curl, CURLOPT_USERPWD, "Lacak:P@ssw0rd");
		
				$json_response = curl_exec($curl);
				
				echo $json_response;
				echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
				printf("POSTING DONE : %s \r\n",$v->vehicle_no);
			}
			else
			{
				printf("DATA UNAVAILABLE : %s \r\n",$v->vehicle_no);
			}
		}
		printf("STARTING VEHICLE OTHERS \r\n");
		//all vehicle Others
		$type_others = array("TK309","TK315","A13");
		
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_device <>", "352312090341960@TK315");
		$this->db->where("vehicle_device <>", "1453843108@A13");
		$this->db->where("vehicle_device <>", "1453843091@A13");
		$this->db->where("vehicle_device <>", "1453843057@A13");
		$this->db->where("vehicle_device <>", "352312090342521@TK315");
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$this->db->where_in("vehicle_type",$type_others);
		$this->db->where("vehicle_status <>", 3);
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
			$table = "gps";
			$tableinfo = "gps_info";
			
			$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->dbdata->where("gps_info_device", $v->vehicle_device);
			$this->dbdata->order_by("gps_time", "desc");
			$this->dbdata->limit(1);
			$q = $this->dbdata->get($tables['gps']);
			if($q->num_rows > 0)
			{
				$rows = $q->result();
				$trows = count($rows);
				$DataToUpload = array();
				unset($DataToUpload);
				printf("GET LOCATION ATTRIBUTE START : %s \r\n",$v->vehicle_no);
				
				for($i=0;$i<$trows;$i++)
				{
					if(isset($rows[$i]->gps_time) && $rows[$i]->gps_time != "")
					{	
						date_default_timezone_set("Asia/Bangkok");		
						//revisi budy (2018 03 01)
						$id = explode("@",$v->vehicle_device);
						$DataToUpload[$i]->DeviceId = $id[0];
						$DataToUpload[$i]->VehicleNumber = $v->vehicle_no;
						
						if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
						{
							$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime($rows[$i]->gps_time));
							$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							
							$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime($rows[$i]->gps_time)); 
						}
						//print_r($rows[$i]->gps_time." ".$DataToUpload[$i]->GpsDateTime." ".$DataToUpload[$i]->GpsDateTimeUtc);exit();
						
						if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
						{
							$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude, 4, ".", "");
							$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude, 4, ".", "");
							
						}
						else
						{
							if (isset($rows[$i]->gps_longitude))
							{
								$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
							}
						
							if (isset($rows[$i]->gps_latitude))
							{
								$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							if (isset($rows[$i]->gps_latitude_real))
							{
								$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
							}
				
							if (isset($rows[$i]->gps_longitude_real))
							{
								$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
							}
						}
			
						if (isset($rows[$i]->gps_longitude))
						{
							if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
							{
								
								$result_position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							}
							else
							{
								$result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							$DataToUpload[$i]->Location = $result_position->display_name;
						}
						$DataToUpload[$i]->Speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
					}
					$DataToUpload[$i]->TransferDateTime =  $idate;
					$DataToUpload[$i]->TransferDateTimeUtc = $idate_utc;
				}
			
				$datajson["DataToUpload"] = $DataToUpload;
				$content = json_encode($datajson);
				//$content = json_encode($datajson,JSON_NUMERIC_CHECK); 
				printf("Data JSON : %s \r \n",$content);//exit;
				
				printf("POSTING PROSES : %s ",$v->vehicle_no);
								
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				//curl_setopt($curl, CURLOPT_USERPWD, "Lacak:P@ssw0rd");
		
				$json_response = curl_exec($curl);
				
				echo $json_response;
				echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
				printf("POSTING DONE : %s \r\n",$v->vehicle_no);
			}
			else
			{
				printf("DATA UNAVAILABLE : %s \r\n",$v->vehicle_no);
			}
		}
		
		exit;
	}
	
	function smr_parsial()
	{
		printf("PROSES POST LINFOX -> SMR >> START \r\n");
		date_default_timezone_set("Asia/Bangkok");
		$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RESTServices/GPSDataService.svc/Upload";
		$username =  "Lacak";
		$password = "P@ssw0rd";
		$provider = "Lacak";
		$userid = 3102; //user id smr

		$idate = date("d M Y H:i");
		$idate_utc = date("d M Y H:i", strtotime("-7 hour", strtotime($idate)));
		
		$myvehicle = array("352312090341960@TK315","1453843108@A13","1453843091@A13","1453843057@A13","352312090342521@TK315");
		
		$datajson = array();
		$datajson["UserName"] = $username;
		$datajson["Password"] = $password;
		$datajson["GPSProvider"] = $provider;
		
		//all vehicle
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where_in("vehicle_device", $myvehicle);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
			$table = "gps";
			$tableinfo = "gps_info";
			
			$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->dbdata->where("gps_info_device", $v->vehicle_device);
			$this->dbdata->order_by("gps_time", "desc");
			$this->dbdata->limit(1);
			$q = $this->dbdata->get($tables['gps']);
			if($q->num_rows > 0)
			{
				$rows = $q->result();
				$trows = count($rows);
				$DataToUpload = array();
				unset($DataToUpload);
				printf("GET LOCATION ATTRIBUTE START : %s \r\n",$v->vehicle_no);
				
				for($i=0;$i<$trows;$i++)
				{
					if(isset($rows[$i]->gps_time) && $rows[$i]->gps_time != "")
					{	
						date_default_timezone_set("Asia/Bangkok");		
						//revisi budy (2018 03 01)
						$id = explode("@",$v->vehicle_device);
						$DataToUpload[$i]->DeviceId = $id[0];
						$DataToUpload[$i]->VehicleNumber = $v->vehicle_no;
						
						if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309" && $rm[$i]->vehicle_type != "TK315")
						{
							$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime($rows[$i]->gps_time)); 
						}
						else
						{
							$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime($rows[$i]->gps_time));
							$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
						}
						
						if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309" && $rm[$i]->vehicle_type != "TK315")
						{
							if (isset($rows[$i]->gps_longitude))
							{
								$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
							}
						
							if (isset($rows[$i]->gps_latitude))
							{
								$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							if (isset($rows[$i]->gps_latitude_real))
							{
								$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
							}
				
							if (isset($rows[$i]->gps_longitude_real))
							{
								$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
							}
						}
						else
						{
							$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude, 4, ".", "");
							$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude, 4, ".", "");
						}
			
						if (isset($rows[$i]->gps_longitude))
						{
							if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309" && $rm[$i]->vehicle_type != "TK315")
							{
								$result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
							else
							{
								$result_position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							}
						
							$DataToUpload[$i]->Location = $result_position->display_name;
						}
						$DataToUpload[$i]->Speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
					}
					$DataToUpload[$i]->TransferDateTime =  $idate;
					$DataToUpload[$i]->TransferDateTimeUtc = $idate_utc;
				}
			
				$datajson["DataToUpload"] = $DataToUpload;
				$content = json_encode($datajson);
				//$content = json_encode($datajson,JSON_NUMERIC_CHECK); 
				printf("Data JSON : %s \r \n",$content);//exit;
				
				printf("POSTING PROSES : %s ",$v->vehicle_no);
								
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				//curl_setopt($curl, CURLOPT_USERPWD, "Lacak:P@ssw0rd");
		
				$json_response = curl_exec($curl);
				
				echo $json_response;
				echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
				printf("POSTING DONE : %s \r\n",$v->vehicle_no);
			}
			else
			{
				printf("DATA UNAVAILABLE : %s \r\n",$v->vehicle_no);
			}
		}
		exit;
	}
	
	function kabut()
	{
		printf("PROSES POST LINFOX -> KABUT >> START \r\n");

		$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RESTServices/GPSDataService.svc/Upload";
		//$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RESTServices/GPSDataService.svc/Upload/help/operations/Upload";
		$username =  "Kabut";
		//$username =  "Lacak";
		$password = "P@ssw0rd";
		$provider = "Kabut";
		//$provider = "Lacak";
		$userid = 3238; //user id sms_
		
		$myvehicle = array("061453838099@T8","061453838125@T8","061453838089@T8","061453838107@T8","061453838123@T8",
						   "061453838109@T8","061453838105@T8","061453838098@T8","088888000149@T8","002100004564@T5",
						   "088888000151@T8","002100004563@T5","088888000150@T8","002100004561@T5","061452086489@T8",
						   "4700460439@A13"
						   );
		
		$datajson = array();
		$datajson["UserName"] = $username;
		$datajson["Password"] = $password;
		$datajson["GPSProvider"] = $provider;
		
		//all vehicle
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_in("vehicle_device", $myvehicle);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
			//$rowv[] = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
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
			}
		}
		
		
		$trows = count($rows);
		
		$DataToUpload = array();
		
		printf("GET LOCATION ATTRIBUTE START \r\n");
		for($i=0;$i<$trows;$i++)
		{
			if(isset($rows[$i]))
			{
				
			$id = explode("@",$rm[$i]->vehicle_device);
			
			$DataToUpload[$i]->DeviceId = $id[0];
			$DataToUpload[$i]->VehicleNumber = $rm[$i]->vehicle_no;
			//$DataToUpload[$i]->vehicle_name = $rm[$i]->vehicle_name;
			
			if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309" && $rm[$i]->vehicle_type != "TK315")
			{		
				$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
				$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime($rows[$i]->gps_time));
			}else{
				$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime("+1 hour", strtotime($rows[$i]->gps_time)));
				$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime("-6 hour", strtotime($rows[$i]->gps_time)));
			}
			
			if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309" && $rm[$i]->vehicle_type != "TK315")
			{
				if (isset($rows[$i]->gps_longitude))
				{
					$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
				}
				
				if (isset($rows[$i]->gps_latitude))
				{
					$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
				}
				
				if (isset($rows[$i]->gps_latitude_real))
				{
					$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
				}
				
				if (isset($rows[$i]->gps_longitude_real))
				{
					$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
				}
				
			}
			else
			{
				$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude, 4, ".", "");
				$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude, 4, ".", "");
			}
			
			
			if (isset($rows[$i]->gps_longitude))
			{
				if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309" && $rm[$i]->vehicle_type != "TK315")
				{
					$result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
				}
				else
				{
					$result_position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
				}
				
				$DataToUpload[$i]->Location = $result_position->display_name;
			}
			
			$DataToUpload[$i]->Speed =  number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			
			//$idate = date("d M Y H:i");
			$nowdate = date("d M Y H:i");
			$idate = date("d M Y H:i", strtotime("-1 hour", strtotime($nowdate)));
			
			$DataToUpload[$i]->TransferDateTime = $idate;
			$DataToUpload[$i]->TransferDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($idate)));
			
			}
		}
		
		
		$datajson["DataToUpload"] = $DataToUpload;
		$content = json_encode($datajson);
		printf("Data JSON : %s \r \n",$content);
		//$content = json_encode($DataToUpload, JSON_NUMERIC_CHECK);          
		
		printf("POSTING PROSES \r\n");
		                    
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "Lacak:P@ssw0rd");
	
		$json_response = curl_exec($curl);
		
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		exit;
	}
	
	function kabut_new()
	{
		printf("PROSES POST LINFOX -> KABUT >> START \r\n");
		date_default_timezone_set("Asia/Bangkok");
		$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RESTServices/GPSDataService.svc/Upload";
		$username =  "Kabut";
		$password = "P@ssw0rd";
		$provider = "Kabut";
		$userid = 3238; //user id smr

		$idate = date("d M Y H:i");
		$idate_utc = date("d M Y H:i", strtotime("-7 hour", strtotime($idate)));
		
		$datajson = array();
		$datajson["UserName"] = $username;
		$datajson["Password"] = $password;
		$datajson["GPSProvider"] = $provider;
		
		//all vehicle T5/T8
		printf("STARTING VEHICLE PRIME \r\n");
		$type_main = array("T8_2","T5");
		$myvehicle1 = array("061453838099@T8","061453838125@T8","061453838089@T8","061453838107@T8","061453838123@T8",
						   "061453838109@T8","061453838105@T8","061453838098@T8","088888000149@T8","002100004564@T5",
						   "088888000151@T8","002100004563@T5","088888000150@T8","002100004561@T5","061452086489@T8"
						   );
		
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$this->db->where_in("vehicle_device",$myvehicle1);
		$this->db->where("vehicle_status <>", 3);
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
			$table = "gps";
			$tableinfo = "gps_info";
			
			$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->dbdata->where("gps_info_device", $v->vehicle_device);
			$this->dbdata->order_by("gps_time", "desc");
			$this->dbdata->limit(1);
			$q = $this->dbdata->get($tables['gps']);
			if($q->num_rows > 0)
			{
				$rows = $q->result();
				$trows = count($rows);
				$DataToUpload = array();
				unset($DataToUpload);
				printf("GET LOCATION ATTRIBUTE START : %s \r\n",$v->vehicle_no);
				
				for($i=0;$i<$trows;$i++)
				{
					if(isset($rows[$i]->gps_time) && $rows[$i]->gps_time != "")
					{	
						date_default_timezone_set("Asia/Bangkok");		
						//revisi budy (2018 03 01)
						$id = explode("@",$v->vehicle_device);
						$DataToUpload[$i]->DeviceId = $id[0];
						$DataToUpload[$i]->VehicleNumber = $v->vehicle_no;
						
						if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
						{
							$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime($rows[$i]->gps_time));
							$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							
							$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime($rows[$i]->gps_time)); 
						}
						//print_r($rows[$i]->gps_time." ".$DataToUpload[$i]->GpsDateTime." ".$DataToUpload[$i]->GpsDateTimeUtc);exit();
						
						if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
						{
							$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude, 4, ".", "");
							$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude, 4, ".", "");
							
						}
						else
						{
							if (isset($rows[$i]->gps_longitude))
							{
								$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
							}
						
							if (isset($rows[$i]->gps_latitude))
							{
								$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							if (isset($rows[$i]->gps_latitude_real))
							{
								$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
							}
				
							if (isset($rows[$i]->gps_longitude_real))
							{
								$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
							}
						}
			
						if (isset($rows[$i]->gps_longitude))
						{
							if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
							{
								
								$result_position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							}
							else
							{
								$result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							$DataToUpload[$i]->Location = $result_position->display_name;
						}
						$DataToUpload[$i]->Speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
					}
					$DataToUpload[$i]->TransferDateTime =  $idate;
					$DataToUpload[$i]->TransferDateTimeUtc = $idate_utc;
				}
			
				$datajson["DataToUpload"] = $DataToUpload;
				$content = json_encode($datajson);
				//$content = json_encode($datajson,JSON_NUMERIC_CHECK); 
				printf("Data JSON : %s \r \n",$content);//exit;
				
				printf("POSTING PROSES : %s ",$v->vehicle_no);
								
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				//curl_setopt($curl, CURLOPT_USERPWD, "Lacak:P@ssw0rd");
		
				$json_response = curl_exec($curl);
				
				echo $json_response;
				echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
				printf("POSTING DONE : %s \r\n",$v->vehicle_no);
			}
			else
			{
				printf("DATA UNAVAILABLE : %s \r\n",$v->vehicle_no);
			}
		}
		printf("STARTING VEHICLE OTHERS \r\n");
		//all vehicle Others
		$type_others = array("TK315","A13");
		$myvehicle2 = array("4700460439@A13","353701091463725@GT06","353701091496055@GT06","353701091461695@GT06");
						   
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$this->db->where_in("vehicle_device",$myvehicle2);
		$this->db->where("vehicle_status <>", 3);
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
			$table = "gps";
			$tableinfo = "gps_info";
			
			$this->dbdata->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->dbdata->where("gps_info_device", $v->vehicle_device);
			$this->dbdata->order_by("gps_time", "desc");
			$this->dbdata->limit(1);
			$q = $this->dbdata->get($tables['gps']);
			if($q->num_rows > 0)
			{
				$rows = $q->result();
				$trows = count($rows);
				$DataToUpload = array();
				unset($DataToUpload);
				printf("GET LOCATION ATTRIBUTE START : %s \r\n",$v->vehicle_no);
				
				for($i=0;$i<$trows;$i++)
				{
					if(isset($rows[$i]->gps_time) && $rows[$i]->gps_time != "")
					{	
						date_default_timezone_set("Asia/Bangkok");		
						//revisi budy (2018 03 01)
						$id = explode("@",$v->vehicle_device);
						$DataToUpload[$i]->DeviceId = $id[0];
						$DataToUpload[$i]->VehicleNumber = $v->vehicle_no;
						
						if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
						{
							$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime($rows[$i]->gps_time));
							$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							
							$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime($rows[$i]->gps_time)); 
						}
						//print_r($rows[$i]->gps_time." ".$DataToUpload[$i]->GpsDateTime." ".$DataToUpload[$i]->GpsDateTimeUtc);exit();
						
						if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
						{
							$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude, 4, ".", "");
							$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude, 4, ".", "");
							
						}
						else
						{
							if (isset($rows[$i]->gps_longitude))
							{
								$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
							}
						
							if (isset($rows[$i]->gps_latitude))
							{
								$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							if (isset($rows[$i]->gps_latitude_real))
							{
								$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
							}
				
							if (isset($rows[$i]->gps_longitude_real))
							{
								$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
							}
						}
			
						if (isset($rows[$i]->gps_longitude))
						{
							if($rm[$i]->vehicle_type == "GT06" || $rm[$i]->vehicle_type == "TJAM" || $rm[$i]->vehicle_type == "A13" || $rm[$i]->vehicle_type == "TK303" || $rm[$i]->vehicle_type == "TK309" || $rm[$i]->vehicle_type == "TK315")
							{
								
								$result_position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							}
							else
							{
								$result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						
							$DataToUpload[$i]->Location = $result_position->display_name;
						}
						$DataToUpload[$i]->Speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
					}
					$DataToUpload[$i]->TransferDateTime =  $idate;
					$DataToUpload[$i]->TransferDateTimeUtc = $idate_utc;
				}
			
				$datajson["DataToUpload"] = $DataToUpload;
				$content = json_encode($datajson);
				//$content = json_encode($datajson,JSON_NUMERIC_CHECK); 
				printf("Data JSON : %s \r \n",$content);//exit;
				
				printf("POSTING PROSES : %s ",$v->vehicle_no);
								
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				//curl_setopt($curl, CURLOPT_USERPWD, "Lacak:P@ssw0rd");
		
				$json_response = curl_exec($curl);
				
				echo $json_response;
				echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
				printf("POSTING DONE : %s \r\n",$v->vehicle_no);
			}
			else
			{
				printf("DATA UNAVAILABLE : %s \r\n",$v->vehicle_no);
			}
		}
		
		exit;
	}
	
	function nassaba()
	{
		printf("PROSES POST LINFOX -> KABUT >> START \r\n");

		$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RESTServices/GPSDataService.svc/Upload";
		$username =  "NASSABA";
		$password = "P@ssw0rd";
		$provider = "NASSABA";
		$userid = 3102; //user id sms_
		
		$myvehicle = array("061451461108@T8","061452086531@T8","061453838179@T8");
		
		$datajson = array();
		$datajson["UserName"] = $username;
		$datajson["Password"] = $password;
		$datajson["GPSProvider"] = $provider;
		
		//all vehicle
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_id", "desc");
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_user_id", $userid);
		$this->db->where_in("vehicle_device", $myvehicle);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
			//$rowv[] = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
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
			}
		}
		
		
		$trows = count($rows);
		
		$DataToUpload = array();
		
		printf("GET LOCATION ATTRIBUTE START \r\n");
		for($i=0;$i<$trows;$i++)
		{
			if(isset($rows[$i]))
			{
				
			$id = explode("@",$rm[$i]->vehicle_device);
			
			$DataToUpload[$i]->DeviceId = $id[0];
			$DataToUpload[$i]->VehicleNumber = $rm[$i]->vehicle_no;
			//$DataToUpload[$i]->vehicle_name = $rm[$i]->vehicle_name;
			$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime($rows[$i]->gps_time));
			$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
			
			
			if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309")
			{
				if (isset($rows[$i]->gps_longitude))
				{
					$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
				}
				
				if (isset($rows[$i]->gps_latitude))
				{
					$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
				}
				
				if (isset($rows[$i]->gps_latitude_real))
				{
					$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
				}
				
				if (isset($rows[$i]->gps_longitude_real))
				{
					$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
				}
				
			}
			else
			{
				$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude, 4, ".", "");
				$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude, 4, ".", "");
			}
			
			
			if (isset($rows[$i]->gps_longitude))
			{
				if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309")
				{
					$result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
				}
				else
				{
					$result_position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
				}
				
				$DataToUpload[$i]->Location = $result_position->display_name;
			}
			
			$DataToUpload[$i]->Speed =  number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			
			$idate = date("d M Y H:i");
			$DataToUpload[$i]->TransferDateTime = $idate;
			$DataToUpload[$i]->TransferDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($idate)));
			
			}
		}
		
		
		$datajson["DataToUpload"] = $DataToUpload;
		$content = json_encode($datajson);
		printf("Data JSON : %s \r \n",$content);
		//$content = json_encode($DataToUpload, JSON_NUMERIC_CHECK);          
		
		printf("POSTING PROSES \r\n");
		                    
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "Lacak:P@ssw0rd");
	
		$json_response = curl_exec($curl);
		
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		exit;
	}
	
	function mandiri()
	{
		printf("PROSES POST LINFOX -> MANDIRI >> START \r\n");

		$url = "http://linfox.southeastasia.cloudapp.azure.com/LinfoxINDO/RESTServices/GPSDataService.svc/Upload";
		$username =  "Mandiri";
		$password = "P@ssw0rd";
		$provider = "Mandiri";
		$userid = 3102; //user id sms_
		
		$myvehicle = array("061453838112@T8","352312090342265@TK315"); //simarno
		
		$datajson = array();
		$datajson["UserName"] = $username;
		$datajson["Password"] = $password;
		$datajson["GPSProvider"] = $provider;
		
		//all vehicle
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_imei,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_user_id", $userid);
		$this->db->where_in("vehicle_device", $myvehicle);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
			
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
			//$rowv[] = $qv->row();
				
			//Seleksi Databases
			$tables = $this->gpsmodel->getTable($rowvehicle);
			
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
			}
			else
			{
				$this->dbdata = $this->load->database($tables["dbname"], TRUE);
			}
			
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
			}
		}
		
		
		$trows = count($rows);
		
		$DataToUpload = array();
		
		printf("GET LOCATION ATTRIBUTE START \r\n");
		for($i=0;$i<$trows;$i++)
		{
			if(isset($rows[$i]))
			{
				
			$id = explode("@",$rm[$i]->vehicle_device);
			
			$DataToUpload[$i]->DeviceId = $id[0];
			$DataToUpload[$i]->VehicleNumber = $rm[$i]->vehicle_no;
			//$DataToUpload[$i]->vehicle_name = $rm[$i]->vehicle_name;
			$DataToUpload[$i]->GpsDateTime = date("d M Y H:i", strtotime($rows[$i]->gps_time));
			$DataToUpload[$i]->GpsDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
			
			
			if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309")
			{
				if (isset($rows[$i]->gps_longitude))
				{
					$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
				}
				
				if (isset($rows[$i]->gps_latitude))
				{
					$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
				}
				
				if (isset($rows[$i]->gps_latitude_real))
				{
					$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
				}
				
				if (isset($rows[$i]->gps_longitude_real))
				{
					$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
				}
				
			}
			else
			{
				$DataToUpload[$i]->Latitude = number_format($rows[$i]->gps_latitude, 4, ".", "");
				$DataToUpload[$i]->Longitude = number_format($rows[$i]->gps_longitude, 4, ".", "");
			}
			
			
			if (isset($rows[$i]->gps_longitude))
			{
				if($rm[$i]->vehicle_type != "GT06" && $rm[$i]->vehicle_type != "TJAM" && $rm[$i]->vehicle_type != "A13" && $rm[$i]->vehicle_type != "TK303" && $rm[$i]->vehicle_type != "TK309")
				{
					$result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
				}
				else
				{
					$result_position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
				}
				
				$DataToUpload[$i]->Location = $result_position->display_name;
			}
			
			$DataToUpload[$i]->Speed =  number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			
			$idate = date("d M Y H:i");
			$DataToUpload[$i]->TransferDateTime = $idate;
			$DataToUpload[$i]->TransferDateTimeUtc = date("d M Y H:i", strtotime("-7 hour", strtotime($idate)));
			
			}
		}
		
		
		$datajson["DataToUpload"] = $DataToUpload;
		$content = json_encode($datajson);
		printf("Data JSON : %s \r \n",$content);
		//$content = json_encode($DataToUpload, JSON_NUMERIC_CHECK);          
		
		printf("POSTING PROSES \r\n");
		                    
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "Lacak:P@ssw0rd");
	
		$json_response = curl_exec($curl);
		
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
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
	
	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_device) {
		
		$this->db = $this->load->database("default", true);
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_vehicle = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_device);

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
	
}
