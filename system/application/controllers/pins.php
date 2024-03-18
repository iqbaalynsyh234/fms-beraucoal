<?php
include "base.php";

class Pins extends Base {

	function Pins()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}
	
	function lastposition()
	{
		printf("PROSES POST PINS -> SIIS >> START \r\n");
		//$url = "http://siis-vehicle-api-gis.apps.playcourt.id/lacak";
		$url = "http://siis-mobi-api.vsan-apps.playcourt.id/lacak";
		//$url_https = "https://siis-mobi.udata.id/lacak";
		$username =  "lacak";
		$password = "gxK82EoO4zSzqDzpjYJG";
		$userid = 3409; //user id pins_indonesia
		
		//all vehicle
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_no","B 1079 NRI");
		$this->db->where("vehicle_user_id", $userid);
		//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		//$this->db->limit(100);
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
		
		$x = 0;
		
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			//unset($rowv);
			//unset($rows);
			
			$usehistory = false;
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info,vehicle_imei");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
			$rowv[] = $qv->row();
				
			$table = "gps";
			$tableinfo = "gps_info";
			
			printf("VEHICLE TYPE : %s  \r\n",$rowvehicle->vehicle_type);
			
			if($rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "TK303" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK309PTO" || $rowvehicle->vehicle_type == "GT06PTO") 
			{
				//goblin, saintseiya, galactus
				$this->dbtraccar = $this->load->database("GPS_TRACCAR", TRUE);				
				$this->dbtraccar->where("uniqueid",$rowvehicle->vehicle_imei);
				$this->dbtraccar->limit(1);
				$q = $this->dbtraccar->get("devices");
				
				if ($q->num_rows() > 0)
				{
					printf("VEHICLE %s  TABLE POSITIONS \r\n",$v->vehicle_no);
					$vtraccar = $q->row();
					$this->dbtraccar->order_by("devicetime", "desc");
					$this->dbtraccar->where("deviceid", $vtraccar->id);
					if(isset($vtraccar->table) && $vtraccar->table != "")
					{
						$q = $this->dbtraccar->get($vtraccar->table);
					}
					else
					{
						if($vtraccar->server == "saintseiya")
						{
							$q = $this->dbtraccar->get("positions_gt06");
						}
						else if($vtraccar->server == "galactus")
						{
							$q = $this->dbtraccar->get("positions_tk309");
						}
						else
						{
							$q = $this->dbtraccar->get("positions");
						}
					}
					if ($q->num_rows() > 0)
					{
						$row = new stdclass();
						$dtraccar = $q->row();
						$row->gps_name = $vtraccar->uniqueid;
						$row->gps_host = $vtraccar->name;
						$row->gps_utc_coord = date("His",strtotime($dtraccar->devicetime));
						$row->gps_status = "A";
						$row->gps_latitude = number_format($dtraccar->latitude, 4, ".", "");
						$row->gps_ns = "";
						$row->gps_longitude = number_format($dtraccar->longitude, 4, ".", "");
						$row->gps_ew = "";
						$row->gps_speed = $dtraccar->speed;
						$row->gps_course = $dtraccar->course;
						$row->gps_utc_date = date("dmy",strtotime($dtraccar->devicetime));
						$row->gps_time = date("Y-m-d H:i:s",strtotime($dtraccar->devicetime));
						$row->gps_latitude_real = number_format($dtraccar->latitude, 4, ".", "");
						$row->gps_longitude_real = number_format($dtraccar->longitude, 4, ".", "");
						
						$row->gps_info_device = $vtraccar->uniqueid."@".$vtraccar->name;
						$row->gps_info_utc_coord = date("His",strtotime($dtraccar->devicetime));
						$row->gps_info_utc_date = date("dmy",strtotime($dtraccar->devicetime));
						$row->gps_info_time = date("Y-m-d H:i:s",strtotime($dtraccar->devicetime));

						$attributes = json_decode($dtraccar->attributes, true);
						if(isset($attributes['ignition']))
						{
							if($attributes['ignition'] == false) { $ignition = false; } else { $ignition = true; }
						}
						else
						{
							if($dtraccar->speed > 0) { $ignition = true; } else { $ignition = false; }
						}
						if($ignition == 1) { $row->gps_info_io_port = "0000100000"; } else { $row->gps_info_io_port = "0000000000"; }
						if(isset($attributes['totalDistance'])) { $row->gps_info_distance = $attributes['totalDistance']; }
						
						$rows[] = $row;
						$trows = count($rows);
					}
					else
					{
						//Seleksi Databases
						printf("VEHICLE %s  TABLE  \r\n",$v->vehicle_no);
						$tables = $this->gpsmodel->getTable($rowvehicle);
						if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
						{
							$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
						}
						else
						{
							$this->dbdata = $this->load->database($tables["dbname"], TRUE);
						}
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
						else
						{
							printf("VEHICLE %s  DB HISTORY  \r\n",$v->vehicle_no);
							$ex = explode("@",$rowvehicle->vehicle_device);
							printf("VEHICLE TYPE %s    \r\n",$v->vehicle_device);
							
							$histtableinfo = strtolower($ex[0])."@".strtolower($ex[1])."_info";
							$histtablegps = strtolower($ex[0])."@".strtolower($ex[1])."_gps";
							
							printf("TABLE GPS INFO %s    \r\n",$histtableinfo);
							printf("TABLE GPS %s    \r\n",$histtablegps);
							
							//print_r($histtableinfo." ~ ".$histtablegps);exit();
							
							$this->dbhist = $this->load->database("gpshistory",true);
							$this->dbhist->join($histtableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist->where("gps_info_device", $v->vehicle_device);
							$this->dbhist->order_by("gps_time", "desc");
							$this->dbhist->limit(1);
							$q = $this->dbhist->get($histtablegps);
							if($q->num_rows > 0)
							{
								$usehistory = true;
								$rows[] = $q->row();
								$trows = count($rows);
							}
						}
					}
				}
			}
			else 
			{
				//Seleksi Databases
				printf("VEHICLE %s  TABLE DATABASE 2 \r\n",$v->vehicle_no);
				$tables = $this->gpsmodel->getTable($rowvehicle);
				if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
				{
					$this->dbdata = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
				}
				else
				{
					$this->dbdata = $this->load->database($tables["dbname"], TRUE);
				}
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
				else
				{
					$this->dbdata = $this->load->database("GPS_TRACCAR", TRUE);
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
				}
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
				
				$feature["data"][$i]->dt = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
				
				if($usehistory)
				{
					$mydate = date("Y-m-d H:i:s");
					$feature["data"][$i]->dt = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($mydate)));
				}
				
				$feature["data"][$i]->vehicle_no = $v->vehicle_no;
				$feature["data"][$i]->vehicle_name = $v->vehicle_name;
			
			
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
				if (isset($rows[$i]->gps_longitude_real))
				{
					$feature["data"][$i]->lng = number_format($rows[$i]->gps_longitude, 4, ".", "");
				}
				
				if (isset($rows[$i]->gps_latitude_real))
				{
					$feature["data"][$i]->lat = number_format($rows[$i]->gps_latitude, 4, ".", "");
				}
			}
			else
			{
				$feature["data"][$i]->lng = number_format($rows[$i]->gps_longitude, 4, ".", "");
				$feature["data"][$i]->lat = number_format($rows[$i]->gps_latitude, 4, ".", "");
			}
			
			$feature["data"][$i]->speed =  number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			
			$feature["data"][$i]->deg = $rows[$i]->gps_course;
			if($feature["data"][$i]->deg == null)
			{
				$feature["data"][$i]->deg = 0;
			}
			
			if (isset($rows[$i]->gps_info_io_port))
			{
				$ioport = $rows[$i]->gps_info_io_port;
				$feature["data"][$i]->engine = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
				if($feature["data"][$i]->engine == false)
				{
					$feature["data"][$i]->engine = "OFF";
				}	
				else
				{
					$feature["data"][$i]->engine = "ON";
				}
			}
			else
			{
				$feature["data"][$i]->engine = "OFF";
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
				
				$feature["data"][$i]->location = $result_position->display_name;
			}
			}
		}
		
		
		//print_r($feature);exit;
		/*
		printf("POSTING PROSES \r\n");
		$content = json_encode($feature, JSON_NUMERIC_CHECK);          
		printf("Data JSON : %s \r \n",$content);
		                    
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "lacak:gxK82EoO4zSzqDzpjYJG");
		
		$json_response = curl_exec($curl);
		if ($json_response === FALSE) {
			die("Curl failed: " . curL_error($curl));
		}
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		printf("-------------------------- \r\n");
		*/
		
		//HTTPS / edit HTTP saja new link
		printf("POSTING PROSES HTTP NEW LINK \r\n");
		$content = json_encode($feature, JSON_NUMERIC_CHECK);          
		printf("Data JSON : %s \r \n",$content);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "lacak:gxK82EoO4zSzqDzpjYJG");
		
		//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		//curl_setopt($curl, CURLOPT_SSLVERSION, 4);
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		//curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST , 'TLSv1_2');
		curl_setopt($curl, CURLOPT_SSLVERSION, 6);
		
		$json_response = curl_exec($curl);
		
		if ($json_response === FALSE) {
			die("Curl failed: " . curL_error($curl));
		}
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		printf("-------------------------- \r\n");
		unset($rowv);
		unset($rows);
		unset($feature);
		$trows = 0;
		//Selesai HTTPS
		
		}
		else
		{
			printf("ATTR %s \r\n",$v->vehicle_device);
			printf("DATA NOT AVAILABLE \r\n");
			printf("-------------------------- \r\n");
		}
		
		}
		
		
		printf("TOTAL ATTR %s \r\n",$x);
		exit;
		
	}
	function curl_cek(){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.howsmyssl.com/a/check");
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		//echo $response;
		curl_close($ch);
		$tlsVer = json_decode($response, true);
		echo $tlsVer['tls_version'] ? $tlsVer['tls_version'] : 'no TLS support';
		
	}
	
	
	function lastposition_cek()
	{
		printf("PROSES POST PINS -> SIIS >> START \r\n");
		$url = "http://siis-vehicle-api-gis.apps.playcourt.id/lacak";
		$username =  "lacak";
		$password = "gxK82EoO4zSzqDzpjYJG";
		$userid = 3409; //user id pins_indonesia
		
		//all vehicle
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device","352312090176564@TK309");
		$this->db->where("vehicle_user_id", $userid);
		//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		//$this->db->limit(100);
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
		
		$x = 0;
		
		printf("TOTAL VEHICLE %s \r\n",count($rm));
		foreach($rm as $v)
		{
			//unset($rowv);
			//unset($rows);
			
			$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
			$this->db->order_by("vehicle_device", "asc");
			$this->db->where("vehicle_device", $v->vehicle_device);
			$this->db->limit(1);
			$qv = $this->db->get("vehicle");
			$rowvehicle = $qv->row();
			$rowv[] = $qv->row();
				
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
			
			//print_r($this->dbdata);exit;
			
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
				
				$feature["data"][$i]->dt = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
			
			$feature["data"][$i]->vehicle_no = $v->vehicle_no;
			$feature["data"][$i]->vehicle_name = $v->vehicle_name;
			
			
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
				if (isset($rows[$i]->gps_longitude_real))
				{
					$feature["data"][$i]->lng = number_format($rows[$i]->gps_longitude, 4, ".", "");
				}
				
				if (isset($rows[$i]->gps_latitude_real))
				{
					$feature["data"][$i]->lat = number_format($rows[$i]->gps_latitude, 4, ".", "");
				}
			}
			else
			{
				$feature["data"][$i]->lng = number_format($rows[$i]->gps_longitude, 4, ".", "");
				$feature["data"][$i]->lat = number_format($rows[$i]->gps_latitude, 4, ".", "");
			}
			
			$feature["data"][$i]->speed =  number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			
			$feature["data"][$i]->deg = $rows[$i]->gps_course;
			if($feature["data"][$i]->deg == null)
			{
				$feature["data"][$i]->deg = 0;
			}
			
			if (isset($rows[$i]->gps_info_io_port))
			{
				$ioport = $rows[$i]->gps_info_io_port;
				$feature["data"][$i]->engine = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
				if($feature["data"][$i]->engine == false)
				{
					$feature["data"][$i]->engine = "OFF";
				}	
				else
				{
					$feature["data"][$i]->engine = "ON";
				}
			}
			else
			{
				$feature["data"][$i]->engine = "OFF";
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
				
				$feature["data"][$i]->location = $result_position->display_name;
			}
			}
		}
		
		printf("POSTING PROSES \r\n");
		$content = json_encode($feature, JSON_NUMERIC_CHECK);          
		printf("Data JSON : %s \r \n",$content);
		                    
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "lacak:gxK82EoO4zSzqDzpjYJG");

		$json_response = curl_exec($curl);
		
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		printf("-------------------------- \r\n");
		unset($rowv);
		unset($rows);
		unset($feature);
		$trows = 0;
		
		}
		else
		{
			printf("ATTR %s \r\n",$v->vehicle_device);
			printf("DATA NOT AVAILABLE \r\n");
			printf("-------------------------- \r\n");
		}
		
		
		
		}
		
		
		printf("TOTAL ATTR %s \r\n",$x);
		exit;
		
	}
	
	function rekap_problem()
	{
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d H:i:s');
		$mydate = date("Y-m-d");
		$userid = 3409;
		
		printf("Run Cron Check Last Info at %s \r\n", $nowdate);
		printf("======================================\r\n");

		$this->dbtrans =  $this->load->database("transporter",true);
		
		$this->db->order_by("vehicle_id","asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $userid);
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0) { printf("No Vehicles \r\n"); return; }
		
		$rows = $q->result();
		$totalvehicle = count($rows);
		$this->db->close();
		
		$j = 1;
		for ($i=0;$i<count($rows);$i++)
		{
			printf("Process Cron For %s type %s (%d/%d)\n", $rows[$i]->vehicle_device, $rows[$i]->vehicle_type, $j, $totalvehicle);
			printf("%s searching . . . \r\n", $rows[$i]->vehicle_no);
			
			$vehicledevice = $rows[$i]->vehicle_device;
			$this->db->where("vehicle_status", 1);
			$this->db->where("vehicle_device", $vehicledevice);
			$qv = $this->db->get("vehicle");
			
			if ($qv->num_rows() == 0) { printf("No Data \r\n"); }
			$rowvehicle = $qv->row();
			$rowvehicles = $qv->result();
							
			$t = $rowvehicle->vehicle_active_date2;
			$now = date("Ymd");
			
			if ($t < $now) { printf("Mobil Expired \r\n"); }
							
			list($name, $host) = explode("@", $rowvehicle->vehicle_device);
			
			$gps = $this->gpsmodel->GetLastInfo($name, $host, true, false, 0, $rowvehicle->vehicle_type);
			if ($this->gpsmodel->fromsocket)
			{
				$datainfo = $this->gpsmodel->datainfo;
				$fromsocket = $this->gpsmodel->fromsocket;			
			}
			if (! $gps) { printf("====GPS Belum Aktif==== \r\n"); }
			
			$this->db = $this->load->database("default", TRUE);
			if(isset($gps->gps_timestamp))
			{
				$delta = ((mktime() - $gps->gps_timestamp)); //tidak dikurangi 3600 detik
				
				unset($dataproblem);
				$dataproblem["device_problem_vehicle_device"] = $rowvehicle->vehicle_device;
				$dataproblem["device_problem_vehicle_no"] = $rowvehicle->vehicle_no;
				$dataproblem["device_problem_user"] = $userid;
				$dataproblem["device_problem_date"] = $mydate;
				$dataproblem["device_problem_lastupdate"] = $gps->gps_time;
				$dataproblem["device_problem_created"] = date("Y-m-d H:i:s");
					
				//$delta_menit = $delta/3600;
				//printf("GPS time: %s <-> Now Time: %s . Selisih %s detik \r\n", $gps->gps_timestamp, $nowdate, $delta);
				//cek delay kurang dari 10 menit 
				if ($delta >= 3600 && $delta <= 86400) //lebih 1 jam kurang dari 24 jam //yellow condition ssi
				{
					printf("Vehicle No %s Tidak Update (KUNING) \r\n", $rowvehicle->vehicle_no);
					
					$dataproblem["device_problem_condition"] = 1; //Kuning
					
					$this->dbtrans->where("device_problem_date",$mydate);
					$this->dbtrans->where("device_problem_vehicle_device",$rowvehicle->vehicle_device);
					$q = $this->dbtrans->get("device_problem");
					if($q->num_rows > 0)
					{
						$this->dbtrans->where("device_problem_date",$mydate);
						$this->dbtrans->where("device_problem_vehicle_device",$rowvehicle->vehicle_device);
						$this->dbtrans->update("device_problem",$dataproblem);
					}
					else
					{
						$this->dbtrans->insert("device_problem",$dataproblem);
					}
				}
				else if($delta >= 86400) //lebih dari 24 jam //red condition 
				{
					printf("Vehicle No %s Tidak Update (MERAH) \r\n", $rowvehicle->vehicle_no);
					$dataproblem["device_problem_condition"] = 2; //Merah
					
					$this->dbtrans->where("device_problem_date",$mydate);
					$this->dbtrans->where("device_problem_vehicle_device",$rowvehicle->vehicle_device);
					$q = $this->dbtrans->get("device_problem");
					if($q->num_rows > 0)
					{
						$this->dbtrans->where("device_problem_date",$mydate);
						$this->dbtrans->where("device_problem_vehicle_device",$rowvehicle->vehicle_device);
						$this->dbtrans->update("device_problem",$dataproblem);
					}
					else
					{
						$this->dbtrans->insert("device_problem",$dataproblem);
					}
				}
			}
			else
			{
				printf("===NO DATA (Go to History)=== %s \r\n", $rowvehicle->vehicle_no);
			}
			printf("=============================================== \r\n");
			$j++;
		}
		$this->db->close();
		$this->db->cache_delete_all();
		$enddate = date('Y-m-d H:i:s');
		printf("FINISH Check at %s \r\n", $enddate);
		printf("============================== \r\n");
	}
	
	
	function gettripmileage()
	{
		header("Content-Type: application/json");
		$start = $this->input->get('start',true);
		$end = $this->input->get('end',true);
		$vehicle_no = $this->input->get('vehicle_no',true);
		$feature = array();
		$this->dbreport = $this->load->database("pins_report",true);
		$userid = 3409;
		
		if(!isset($start) || $start=="")
		{
			$feature["data"] = "Invalid Start Time";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		if(!isset($end) || $end=="")
		{
			$feature["data"] = "Invalid End Time";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		$start = date("Y-m-d H:i:s",strtotime("+7 hour",strtotime($start)));
		$end = date("Y-m-d H:i:s",strtotime("+7 hour",strtotime($end)));
		
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		if($vehicle_no != "")
		{
			$this->db->where("vehicle_no",$vehicle_no);
		}
		else
		{
			$this->db->where("vehicle_user_id", $userid);
		}
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
		
		if($qm->num_rows == 0)
		{
			$feature["data"] = "Vehicle Not Found!";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		foreach($rm as $v)
		{
			$this->dbreport->order_by("trip_mileage_vehicle_id","asc");
			$this->dbreport->where("trip_mileage_start_time >=",$start);
			$this->dbreport->where("trip_mileage_end_time <=",$end);
			$this->dbreport->where("trip_mileage_vehicle_id",$v->vehicle_device);
			$q = $this->dbreport->get("tripmileage");
			$data = $q->result();
			for($i=0;$i<$q->num_rows;$i++)
			{
				//$feature["data"][$i]->vehicle_no = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($rows[$i]->gps_time)));
				$feature["data"][$i]->vehicle_no = $v->vehicle_no;
				$feature["data"][$i]->vehicle_name = $v->vehicle_name;
				$feature["data"][$i]->trip = $data[$i]->trip_mileage_trip_no;
				$feature["data"][$i]->time_start = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($data[$i]->trip_mileage_start_time)));
				$feature["data"][$i]->time_end = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($data[$i]->trip_mileage_end_time)));
				$feature["data"][$i]->trip = $data[$i]->trip_mileage_trip_no;
				$feature["data"][$i]->duration = $data[$i]->trip_mileage_duration_second;
				$feature["data"][$i]->trip_mileage = $data[$i]->trip_mileage_trip_mileage;
				$feature["data"][$i]->cummulative_mileage = $data[$i]->trip_mileage_cummulative_mileage;
				$feature["data"][$i]->location_start = $data[$i]->trip_mileage_location_start;
				$feature["data"][$i]->location_end = $data[$i]->trip_mileage_location_end;
				$feature["data"][$i]->coord_start = $data[$i]->trip_mileage_latlng_start;
				$feature["data"][$i]->coord_end = $data[$i]->trip_mileage_latlng_end;
			}
		}
		
		if(count($feature) == 0)
		{
			$feature["data"] = "Data Not Avaliable!";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		echo json_encode($feature);
		exit;
	}
	
	function getparking()
	{
		header("Content-Type: application/json");
		$start = $this->input->get('start',true);
		$end = $this->input->get('end',true);
		$vehicle_no = $this->input->get('vehicle_no',true);
		$feature = array();
		$this->dbreport = $this->load->database("pins_report",true);
		$userid = 3409;
		
		if(!isset($start) || $start=="")
		{
			$feature["data"] = "Invalid Start Time";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		if(!isset($end) || $end=="")
		{
			$feature["data"] = "Invalid End Time";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		$start = date("Y-m-d H:i:s",strtotime("+7 hour",strtotime($start)));
		$end = date("Y-m-d H:i:s",strtotime("+7 hour",strtotime($end)));
		
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		if($vehicle_no != "")
		{
			$this->db->where("vehicle_no",$vehicle_no);
		}
		else
		{
			$this->db->where("vehicle_user_id", $userid);
		}
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
		
		if($qm->num_rows == 0)
		{
			$feature["data"] = "Vehicle Not Found!";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		$j = 0;
		foreach($rm as $v)
		{
			$this->dbreport->order_by("playback_vehicle_device","asc");
			$this->dbreport->where("playback_start_time >=",$start);
			$this->dbreport->where("playback_end_time <=",$end);
			$this->dbreport->where("playback_vehicle_device",$v->vehicle_device);
			$q = $this->dbreport->get("playback");
			$data = $q->result();
			
			for($i=0;$i<$q->num_rows;$i++)
			{
				if($data[$i]->playback_duration_second >= 3600)
				{
					$feature["data"][$j]->vehicle_no = $v->vehicle_no;
					$feature["data"][$j]->vehicle_name = $v->vehicle_name;
					$feature["data"][$j]->time_start = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($data[$i]->playback_start_time)));
					$feature["data"][$j]->time_end = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($data[$i]->playback_end_time)));
					$feature["data"][$j]->duration = $data[$i]->playback_duration_second;
					$feature["data"][$j]->location = $data[$i]->playback_location_start;
					$a = explode(",",$data[$i]->playback_latlng_start);
					$feature["data"][$j]->lng = $a[1];
					$feature["data"][$j]->lat = $a[0];
					$j = $j+1;
				}
			}
		}
		
		if(count($feature) == 0)
		{
			$feature["data"] = "Data Not Avaliable!";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		echo json_encode($feature);
		exit;
	}
	
	function gethistory()
	{
		header("Content-Type: application/json");
		$start = $this->input->get('start',true);
		$end = $this->input->get('end',true);
		$vehicle_no = $this->input->get('vehicle_no',true);
		$feature = array();
		$this->dbreport = $this->load->database("pins_report",true);
		$userid = 3409;
		
		if(!isset($start) || $start=="")
		{
			$feature["data"] = "Invalid Start Time";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		if(!isset($end) || $end=="")
		{
			$feature["data"] = "Invalid End Time";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		$start = date("Y-m-d H:i:s",strtotime("+7 hour",strtotime($start)));
		$end = date("Y-m-d H:i:s",strtotime("+7 hour",strtotime($end)));
		
		$this->db->select("vehicle_id,vehicle_user_id,vehicle_device,vehicle_no,vehicle_name,vehicle_type,vehicle_info");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		if($vehicle_no != "")
		{
			$this->db->where("vehicle_no",$vehicle_no);
		}
		else
		{
			$this->db->where("vehicle_user_id", $userid);
		}
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qm = $this->db->get("vehicle");
		$rm = $qm->result();
		
		if($qm->num_rows == 0)
		{
			$feature["data"] = "Vehicle Not Found!";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		foreach($rm as $v)
		{
			$this->dbreport->order_by("history_vehicle_id","asc");
			$this->dbreport->where("history_datetime >=",$start);
			$this->dbreport->where("history_datetime <=",$end);
			$this->dbreport->where("history_vehicle_id",$v->vehicle_device);
			$q = $this->dbreport->get("history");
			$data = $q->result();
			for($i=0;$i<$q->num_rows;$i++)
			{
				$feature["data"][$i]->dt = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($data[$i]->history_datetime)));
				$feature["data"][$i]->vehicle_no = $v->vehicle_no;
				$feature["data"][$i]->vehicle_name = $v->vehicle_name;
				$feature["data"][$i]->lat = $data[$i]->history_lat;
				$feature["data"][$i]->lng = $data[$i]->history_lng;
				$feature["data"][$i]->speed = $data[$i]->history_speed;
				$feature["data"][$i]->deg = $data[$i]->history_deg;
				$feature["data"][$i]->engine = $data[$i]->history_engine;
				$feature["data"][$i]->location = $data[$i]->history_position;
			}
		}
		
		if(count($feature) == 0)
		{
			$feature["data"] = "Data Not Avaliable!";
			$content = json_encode($feature);
			echo $content;
			exit;
		}
		
		echo json_encode($feature);
		exit;
	}
	
	function trip_mileage($userid="3409", $name = "", $host = "", $startdate = "", $enddate = "")
    {
        printf("PROSES AUTO REPORT TRIP MILEAGE KHUSUS PINS >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        $report_type = "trip_mileage";
        $process_date = date("Y-m-d H:i:s");
        $domain_server = "http://202.129.190.194/";
		$report = "tripmileage_";
        
        if ($startdate == "") 
        {
			$startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
        $sdate = date("Y-m-d H:i:s", strtotime($startdate));
        $edate = date("Y-m-d H:i:s", strtotime($enddate));
        $z =0;
		
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            unset($data_insert);
            //PORT Only
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ip) && isset($json->vehicle_port))
                {
                    
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");				
						$this->dbhist = $this->load->database($database, TRUE);
						
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
						}
						$this->dbhist2 = $this->load->database($istbl_history, TRUE);
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
						}
						$this->dbhist2 = $this->load->database($istbl_history, TRUE);
					}
					
					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
						
					if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                    {
                        $tablehist = $vehicle_device[0]."@t5_gps";
                        $tablehistinfo = $vehicle_device[0]."@t5_info";    
                    }
                    else
                    {
						$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
						$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                    }
					
              
                 
            
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
                
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
            
                        for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
									if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
									{
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
									}
                                    else
                                    {
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_latitude);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
									}
                                    
                                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;     
                                    if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
                                    {
										$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
									} 
									else
									{
										$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
									}              
                                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    { 
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}
										else
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}                                                                                       
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {
                                    if(!$on)
                                    {    
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}
                                        else
                                        {
											$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                         
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}   
										else
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}                    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }                                    
                                }
                                else
                                {            
                                    if($on)
                                    {
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}
										else
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));    
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data
                        $i=1;
                        $new = "";
                        printf("WRITE DATA EXCEL : ");
                        foreach($data as $vehicle_no=>$val)
                        {
                            if($new != $vehicle_no)
                            {
                                $cumm = 0;
                                $trip_no = 1;
                            }
                    
                            foreach($val as $no=>$report)
                            {
                                $mileage = $report['end_mileage']- $report['start_mileage'];
                                if($mileage != 0)
                                {
                                    $duration = get_time_difference($report['start_time'], $report['end_time']);
                                    $durationsecond = dbmaketime($report['end_time']) - dbmaketime($report['start_time']);
                                    
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
                                    $tm = round(($mileage/1000),2);
                                    
                                    if($tm < 0) { $tm = 0; }
                                    
                                    $cumm += $tm;
                                    
                                    printf("|%s|",$i);
									
									$notrip = $trip_no++;
                                    
                            
                                    //mileage
                                    $xme = $tm;
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                                    
                                    if( $x_mile < 0) {  $x_mile = 0; }
                            
                                    //cummulative
                                    $xcum = $cumm;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
									
									if( $x_cum < 0) {  $x_cum = 0; }
									
									unset($datainsert);
									$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
									$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
									$datainsert["trip_mileage_vehicle_name"] = $report['vehicle_name'];
									$datainsert["trip_mileage_trip_no"] = $notrip;
									$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report['start_time']));
									$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report['end_time']));
									$datainsert["trip_mileage_duration"] = $show;
									$datainsert["trip_mileage_duration_second"] = $durationsecond;
									$datainsert["trip_mileage_trip_mileage"] = $x_mile;
									$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
									$datainsert["trip_mileage_location_start"] = $report['start_position']->display_name;
									$datainsert["trip_mileage_location_end"] = $report['end_position']->display_name;
									$datainsert["trip_mileage_latlng_start"] = $report['start_latlng'];
									$datainsert["trip_mileage_latlng_end"] = $report['end_latlng'];
									
									$this->dbtrip = $this->load->database("pins_report",TRUE);
									$this->dbtrip->insert("tripmileage",$datainsert);
									
                                    $i++;
                                }
                            }
                        }
                        
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
            
                        printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
            
                }
                else
                {
                    printf("SKIP VEHICLE ( NO VEHICLE PORT ) \r\n");
                    printf("-------------------------------------- \r\n");    
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");
            }
        }
        
		$finishtime = date("d-m-Y H:i:s");
        printf("AUTOREPORT TRIP MILEAGE DONE %s\r\n",$finishtime);
        
    } 
     
    
    function playback($userid="3409", $name = "", $host = "", $startdate = "", $enddate = "")
    {
        printf("PROSES AUTO REPORT PLAYBACK KHUSUS PINS >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        $report_type = "trip_mileage";
        $process_date = date("Y-m-d H:i:s");
        $domain_server = "http://202.129.190.194/";
		$report = "tripmileage_";
        
        if ($startdate == "") 
        {
			$startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
        $sdate = date("Y-m-d H:i:s", strtotime($startdate));
        $edate = date("Y-m-d H:i:s", strtotime($enddate));
        $z =0;
		
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            unset($data_insert);
            //PORT Only
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ip) && isset($json->vehicle_port))
                {
                    
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");				
						$this->dbhist = $this->load->database($database, TRUE);
						
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
						}
						$this->dbhist2 = $this->load->database($istbl_history, TRUE);
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
						}
						$this->dbhist2 = $this->load->database($istbl_history, TRUE);
					}
					
					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
						
					if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                    {
                        $tablehist = $vehicle_device[0]."@t5_gps";
                        $tablehistinfo = $vehicle_device[0]."@t5_info";    
                    }
                    else
                    {
						$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
						$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                    }
					
              
                 
            
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
                
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
            
                        for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
									if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
									{
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
									}
                                    else
                                    {
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_latitude);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
									}
                                    
                                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {                                
                                    $trip_no = 1;     
                                    if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
                                    {
										$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
									} 
									else
									{
										$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
									}              
                                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    { 
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}
										else
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}                                                                                       
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {
                                    if(!$on)
                                    {    
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}
                                        else
                                        {
											$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                         
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}   
										else
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}                    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }                                    
                                }
                                else
                                {            
                                    if($on)
                                    {
										if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
										}
										else
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));    
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latlng'] = $rows[$i]->gps_latitude.",".$rows[$i]->gps_longitude;
										}
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data
                        $i=1;
                        $new = "";
                        printf("WRITE DATA EXCEL : ");
                        foreach($data as $vehicle_no=>$val)
                        {
                            if($new != $vehicle_no)
                            {
                                $cumm = 0;
                                $trip_no = 1;
                            }
                    
                            foreach($val as $no=>$report)
                            {
                                $mileage = $report['end_mileage']- $report['start_mileage'];
                                if($mileage != 0)
                                {
                                    $duration = get_time_difference($report['start_time'], $report['end_time']);
                                    $durationsecond = dbmaketime($report['end_time']) - dbmaketime($report['start_time']);
                                    
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
                                    $tm = round(($mileage/1000),2);
                                    
                                    if($tm < 0) { $tm = 0; }
                                    
                                    $cumm += $tm;
                                    
                                    printf("|%s|",$i);
									
									$notrip = $trip_no++;
                                    
                            
                                    //mileage
                                    $xme = $tm;
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                                    
                                    if( $x_mile < 0) {  $x_mile = 0; }
                            
                                    //cummulative
                                    $xcum = $cumm;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
									
									if( $x_cum < 0) {  $x_cum = 0; }
									
									unset($datainsert);
									$datainsert["playback_vehicle_device"] = $vehicle_dev;
									$datainsert["playback_vehicle_no"] = $vehicle_no;
									$datainsert["playback_vehicle_name"] = $report['vehicle_name'];
									$datainsert["playback_engine"] = "OFF";
									$datainsert["playback_start_time"] = date("Y-m-d H:i:s", strtotime($report['start_time']));
									$datainsert["playback_end_time"] = date("Y-m-d H:i:s", strtotime($report['end_time']));
									$datainsert["playback_duration"] = $show;
									$datainsert["playback_duration_second"] = $durationsecond;
									//$datainsert["playback_mileage"] = $x_mile;
									//$datainsert["playback_cumm_mileage"] = $x_cum;
									$datainsert["playback_location_start"] = $report['start_position']->display_name;
									$datainsert["playback_location_end"] = $report['end_position']->display_name;
									$datainsert["playback_latlng_start"] = $report['start_latlng'];
									$datainsert["playback_latlng_end"] = $report['end_latlng'];
									
									$this->dbtrip = $this->load->database("pins_report",TRUE);
									if($show != "0 Min")
									{
										$this->dbtrip->insert("playback",$datainsert);
									}
									
                                    $i++;
                                }
                            }
                        }
                        
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
            
                        printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
            
                }
                else
                {
                    printf("SKIP VEHICLE ( NO VEHICLE PORT ) \r\n");
                    printf("-------------------------------------- \r\n");    
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");
            }
        }
        
		$finishtime = date("d-m-Y H:i:s");
        printf("AUTOREPORT PLAYBACK DONE %s\r\n",$finishtime);
        
    } 
     
	
	function history($userid="3409", $name = "", $host = "", $startdate = "", $enddate = "")
    {
        printf("PROSES AUTO REPORT HISTORY KHUSUS PINS >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        $this->dbtrip = $this->load->database("pins_report",TRUE);
        
        if ($startdate == "") 
        {
			$startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
        $sdate = date("Y-m-d H:i:s", strtotime($startdate));
        $edate = date("Y-m-d H:i:s", strtotime($enddate));
        $z =0;
		
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		$this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            unset($data_insert);
            //PORT Only
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ip) && isset($json->vehicle_port))
                {
                    
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");				
						$this->dbhist = $this->load->database($database, TRUE);
						
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
						}
						$this->dbhist2 = $this->load->database($istbl_history, TRUE);
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
						}
						$this->dbhist2 = $this->load->database($istbl_history, TRUE);
					}
					
					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
						
					if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                    {
                        $tablehist = $vehicle_device[0]."@t5_gps";
                        $tablehistinfo = $vehicle_device[0]."@t5_info";    
                    }
                    else
                    {
						$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
						$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                    }
			
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->group_by("gps_time");
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->group_by("gps_time");
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
                
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
            
                        for($i=0;$i<$trows;$i++)
                        {
						   unset($insert);
						   
                           $data[$i]->vehicle_device = $rowvehicle[$x]->vehicle_device;
                           $data[$i]->vehicle_no = $rowvehicle[$x]->vehicle_no;
                           $data[$i]->vehicle_name = $rowvehicle[$x]->vehicle_name;
                           if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
                           {
								$data[$i]->dt = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						   }
						   else
						   {
							  $data[$i]->dt = date("Y-m-d H:i:s", strtotime($rows[$i]->gps_time));
						   }
						   $data[$i]->speed = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
						   $data[$i]->deg = $rows[$i]->gps_course;
						   $data[$i]->lat = $rows[$i]->gps_latitude_real;
						   $data[$i]->lng = $rows[$i]->gps_longitude;
						   
						   if (isset($rows[$i]->gps_info_io_port))
						   {
								$ioport = $rows[$i]->gps_info_io_port;
								$data[$i]->engine = ((strlen($ioport) > 4) && ($ioport[4] == 1));
								if($data[$i]->engine == false)
								{
									$data[$i]->engine = "OFF";
								}	
								else
								{
									$data[$i]->engine = "ON";
								}
						   }
						   
						   if($rowvehicle[$x]->vehicle_type != "GT06" && $rowvehicle[$x]->vehicle_type != "TJAM" && $rowvehicle[$x]->vehicle_type != "A13" && $rowvehicle[$x]->vehicle_type != "TK303" && $rowvehicle[$x]->vehicle_type != "TK309")
						   {
							 $data[$i]->position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						   }
						   else
						   {
							 $data[$i]->position = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
						   }
						   
						   $insert["history_vehicle_id"] = $data[$i]->vehicle_device;
						   $insert["history_vehicle_no"] = $data[$i]->vehicle_no;
						   $insert["history_vehicle_name"] = $data[$i]->vehicle_name;
						   $insert["history_datetime"] = $data[$i]->dt;
						   $insert["history_speed"] = $data[$i]->speed;
						   $insert["history_deg"] = $data[$i]->deg;
						   $insert["history_engine"] = $data[$i]->engine;
						   $insert["history_lat"] = $data[$i]->lat;
						   $insert["history_lng"] = $data[$i]->lng;
                           $insert["history_position"] = $data[$i]->position->display_name;
						   $this->dbtrip->insert("history",$insert);
                        }
                        
                        printf("DELETE CACHE HISTORY \r\n");
                        printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
                }
            }
        }
		$finishtime = date("d-m-Y H:i:s");
        printf("AUTOREPORT HISTORY DONE %s\r\n",$finishtime);
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

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
