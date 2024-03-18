<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class GPSModel extends Model {
	var $earthRadius = 6371;
	var $fromsocket;
	var $datainfo;	
	
	function GPSModel() 
	{				
		parent::Model();	
		$this->fromsocket = false;	
	}	
	
	function GetDirection($course)
	{
		if (($course < 11.25) || ($course > 348.75))
		{
			return 1;
		}

		$car = 1;
		while(1)
		{
			if ($course <= 11.25) break;
			
			$car++;
			$course -= 22.5;
		}
		
		return $car;
	}
	
	function getLocation($latlngs)
	{
		$sql = " SELECT * FROM ".$this->db->dbprefix."location WHERE (0 = 1) ";
		for($i=0; $i < count($latlngs); $i++)
		{
			$sql .= " OR ((location_lat = '".$latlngs[$i][1]."') AND (location_lng = '".$latlngs[$i][0]."')) ";
		}
		
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return false;
		
		$rows = $q->result();		
		for($i=0; $i < count($rows); $i++)
		{
			$arr[$rows[$i]->location_lng][$rows[$i]->location_lat] = $rows[$i]->location_address;
		}
		
		return $arr;
	}
	
	function GeoReverseServiceA($url)
	{
		if (! function_exists("curl_init")) 
		{
			$data->error = 1;
			$data->display_name = "Unknown addrees (err: CURL disabled)";
			return $data;
		}
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$lokasi = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);		
		
		if ($status != 200)
		{
			$data->error = 1;
			$data->display_name = "Unknown addrees";
			return $data;			
		}
		
		return json_decode($lokasi);		
	}

	function GeoReverseService($url)
	{
		if (! function_exists("curl_init")) 
		{
			echo "Unknown address (err: CURL disabled)";
			return;
		}
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$lokasi = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);		

		if ($status != 200)
		{
			echo "Unknown address";
			return;			
		}
	
		return $lokasi;		
	}
	
	function GeoReverse($lat, $lng)
	{
		// cari dulu di db lokal
		
		$lokasi = new stdClass();

		$this->db = $this->load->database("default", TRUE);
		
		$isgooglecity = false;
		
		$googlecity = $this->config->item("googlecity");
		$googlecities = explode(",", $googlecity);

		$this->db->where("CONTAINS( street_line, GEOMFROMTEXT(  'Point(".$lng." ".$lat.")'))");
		$q = $this->db->get("street");
		$this->db->flush_cache();
		
		$streetname = "";
		$isstreetname = $q->num_rows() > 0;
		$isincludestreet = false;
		
		if ($q->num_rows() > 0)
		{
			$rowstreet=$q->result();
			foreach($rowstreet as $obj)
			{
				$obj_serialize = json_decode($obj->street_serialize);
				$count_coordinates = count($obj_serialize->geometry->coordinates[0]);
				$arr[$count_coordinates] = $obj->street_name;
			}
			krsort($arr);
			$streetname = end($arr)." ";
		}
		
		$this->db->where("CONTAINS( ogc_geom, GEOMFROMTEXT(  'Point(".$lng." ".$lat.")'))");
		$q = $this->db->get("desa", 1);
		$this->db->flush_cache();
		
		$address = "";
		
		if ($q->num_rows() > 0)
		{					
			$rowdesa = $q->row();			
			$address = $streetname.$rowdesa->DESA." ".$rowdesa->KECAMATAN." ".$rowdesa->KAB_KOTA." ".$rowdesa->PROPINSI;//." ".$rowdesa->KODE;
			$isincludestreet = true;
			
			if (in_array(strtoupper($rowdesa->KAB_KOTA), $googlecities))
			{
				$isgooglecity = true;
			}
		}
		else
		{
						
			$this->db->where("CONTAINS( ogc_geom, GEOMFROMTEXT(  'Point(".$lng." ".$lat.")'))");
			$q = $this->db->get("kecamatan", 1);
			$this->db->flush_cache();
			
			if ($q->num_rows() > 0)
			{
				$rowkec = $q->row();
				$address = $streetname.$rowkec->LABEL." ".$rowkec->KABUPATEN;
				$isincludestreet = true;
				
				if (in_array(strtoupper($rowkec->KABUPATEN), $googlecities))
				{
					$isgooglecity = true;
				}				
			}

			$this->db->where("kabkota_status", 1);
			$this->db->where("CONTAINS( ogc_geom, GEOMFROMTEXT(  'Point(".$lng." ".$lat.")'))");
			$q = $this->db->get("kabkota", 1);
			$this->db->flush_cache();
			
			if ($q->num_rows() > 0)
			{
				$rowkabkota = $q->row();
				
				if (in_array(strtoupper($rowkabkota->KAB_KOTA), $googlecities))
				{
					$isgooglecity = true;
				}
								
				$address .= " ".$rowkabkota->KAB_KOTA." ".$rowkabkota->PROPINSI;
				if (! $isincludestreet)
				{
					$address = $streetname.$address;
					$isincludestreet = true;
				}
			}						
		}
		
		if (! $isgooglecity)
		{
			if (! $isstreetname)
			{
				$this->db->where("CONTAINS( ogc_geom, GEOMFROMTEXT(  'Point(".$lng." ".$lat.")'))");
				$q = $this->db->get("jalan", 1);
				$this->db->flush_cache();
				
				if ($q->num_rows() > 0)
				{
					$rowjalan = $q->row();			
					if ($rowjalan->LABEL)
					{
						$address = $rowjalan->LABEL.", ".$address;			
					}
				}
		
				$this->db->where("CONTAINS( ogc_geom, GEOMFROMTEXT(  'Point(".$lng." ".$lat.")'))");
				$q = $this->db->get("jalanext", 1);
				$this->db->flush_cache();
				
				if ($q->num_rows() > 0)
				{
					$rowjalan = $q->row();						
					if ($rowjalan->LABEL)
					{
						$address = $rowjalan->LABEL.", ".$address;						
					}
				}
			}
			
			if (strlen($address) > 0)
			{
				//$lokasi->display_name = 'lokal: '.$address;
				$lokasi->display_name = trim($address);
				if (strlen($lokasi->display_name)) 
				{
					return $lokasi;
				}
			}	
		}
		
		if ($isstreetname)
		{
			$lokasi->display_name = trim($streetname);
			if (strlen($lokasi->display_name)) 
			{
				return $lokasi;
			}
		}
		
		$this->db->where("location_lat", $lat);
		$this->db->where("location_lng", $lng);
		$q = $this->db->get("location");
		
		if ($q->num_rows() > 0)
		{			
			$row = $q->row();
			$row->display_name = $row->location_address;
			
			return $row;
		}
				
		$lokasi = $this->GeoReverseService("http://".$this->config->item("georeverse_host")."/map/georeverse/".$lat."/".$lng);	
		$temp->display_name = $lokasi;

		if ($lokasi == "Unknown address") 
		{			
			return $temp;
		}
		
		if ($lokasi == "Unknown address (err: CURL disabled)")
		{
			return $temp;
		}
		
		unset($data);
		$data['location_lat'] = $lat;
		$data['location_lng'] = $lng;
		$data['location_address'] = $temp->display_name;
		
		$mydb = $this->load->database("master", TRUE);
		$mydb->insert("location", $data);		
		
		$this->db->cache_delete_all();
		
		return $temp;				
	}
	
	
	function getGPSTableError($type)
	{
		$t = strtoupper($type);
		
		if ($t == "T1") return "gps_error";
		if ($t == "T1_1") return "gps_t1_1_error";
		if ($t == "T1 PLN") return "gps_pln_error";		
		if ($t == "T4") return "gps_gtp_error";
		
		return "";
	}
	
	function getGPSTable($type)
	{
		$arr = $this->config->item('vehicle_type');
		$temp = $arr;
		
		foreach($temp as $key=>$val)
		{
			$arr[strtoupper($key)] = $val;
			$arr[strtolower($key)] = $val;
		}
		
		if (($type == "T1") || ($type == ""))
		{
			return $this->config->item('default_gpstable');
		}
		
		return "gps_".strtolower($arr[strtolower($type)]);
	}

	function getGPSInfoTable($type)
	{
		$arr = $this->config->item('vehicle_type');

                foreach($arr as $key=>$val)
                {
                        $arr[strtoupper($key)] = $val;
                        $arr[strtolower($key)] = $val;
                }
		
		if (($type == "T1") || ($type == ""))
		{
			return "gps_info";
		}
		
		return "gps_info_".strtolower($arr[strtolower($type)]);
	}
	
	function GetLastInfo($name, $host, $georeverse=true, $row=false, $lasttime=0, $type="")
	{
		$this->db = $this->load->database("default", TRUE);
		
		if ($row === false)
		{		
			$this->db->where("vehicle_device", $name."@".$host);			
		}
		else
		{
			$this->db->where("vehicle_device", $row->gps_name."@".$row->gps_host);
		}
		
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0) 
		{
			return;
		}
		
		$rowvehicle = $q->row();
		
		if (!$this->config->item("alatberat_app"))
		{
			if (! $row)
			{
				$gpsdata = $this->getGPSData($rowvehicle);
				if (($gpsdata !== FALSE) && (strlen($gpsdata) > 0))
				{
					$gpsdatas = explode("|", $gpsdata);
			
					if (count($gpsdatas) > 30)
					{
						$row = new stdclass();
				
						$row->gps_name = substr($gpsdatas[0], 2);
						$row->gps_host = $gpsdatas[1];
						$row->gps_utc_coord = $gpsdatas[3];
						$row->gps_status = $gpsdatas[4];
						$row->gps_latitude = $gpsdatas[5];
						$row->gps_ns = $gpsdatas[6];
						$row->gps_longitude = $gpsdatas[7];
						$row->gps_ew = $gpsdatas[8];
						$row->gps_speed = $gpsdatas[9];
						$row->gps_course = $gpsdatas[10];
						$row->gps_utc_date = $gpsdatas[11];
						$row->gps_mvd = $gpsdatas[12];
						$row->gps_mv = $gpsdatas[13];
						$row->gps_cs = $gpsdatas[14];
						$row->gps_time = $gpsdatas[15];
						$row->gps_latitude_real = $gpsdatas[16];
						$row->gps_longitude_real = $gpsdatas[17];
						$row->gps_odometer = $gpsdatas[18];
						$row->gps_workhour = $gpsdatas[19];
					
						$this->datainfo = new stdclass();
				
						$this->datainfo->gps_info_device = $gpsdatas[20];
						$this->datainfo->gps_info_hdop = $gpsdatas[21];
						$this->datainfo->gps_info_io_port = $gpsdatas[22];
						$this->datainfo->gps_info_distance = $gpsdatas[23];
						$this->datainfo->gps_info_alarm_data = $gpsdatas[24];
						$this->datainfo->gps_info_ad_input = $gpsdatas[25];
						$this->datainfo->gps_info_utc_coord = $gpsdatas[26];
						$this->datainfo->gps_info_utc_date = $gpsdatas[27];
						$this->datainfo->gps_info_alarm_alert = $gpsdatas[28];
						$this->datainfo->gps_info_time = $gpsdatas[29];
						$this->datainfo->gps_info_status = $gpsdatas[30];
					
						$this->fromsocket = true;			
					}
					else
					{
						return;
					}
				}
			
			}
		}
		
		if (! $row)
        {
			
			if($rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "TK303" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK309PTO" || $rowvehicle->vehicle_type == "GT06PTO" || $rowvehicle->vehicle_type == "TK315" || $rowvehicle->vehicle_type == "A14") 
			{
				//goblin, saintseiya, galactus
				$this->dbtraccar = $this->load->database("GPS_TRACCAR", TRUE);
				$this->dbtraccar->where("uniqueid",$name);
				$this->dbtraccar->limit(1);
				$q = $this->dbtraccar->get("devices");
				if ($q->num_rows() > 0)
				{
					$vtraccar = $q->row();
					//print_r($vtraccar);exit;
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
						else if ($vtraccar->server == "galactus2")
							{
							$q = $this->dbtraccar->get("positions_tk315");
							}
						else if($vtraccar->server == "goblin2")
							{
							$q = $this->dbtraccar->get("positions_a14");
							}
						else
							{
							$q = $this->dbtraccar->get("positions");
							}
							
					}
							
					if ($q->num_rows() > 0)
					{
						
						$dtraccar = $q->row();

						$row = new stdclass();
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
						//$row->gps_mvd = $gpsdatas[12];
						//$row->gps_mv = $gpsdatas[13];
						//$row->gps_cs = $gpsdatas[14];
						$row->gps_time = date("Y-m-d H:i:s",strtotime($dtraccar->devicetime));
						$row->gps_latitude_real = number_format($dtraccar->latitude, 4, ".", "");
						$row->gps_longitude_real = number_format($dtraccar->longitude, 4, ".", "");
						//$row->gps_odometer = $gpsdatas[18];
						//$row->gps_workhour = $gpsdatas[19];
						
						$this->datainfo = new stdclass();
				
						$this->datainfo->gps_info_device = $vtraccar->uniqueid."@".$vtraccar->name;
						//$this->datainfo->gps_info_hdop = $gpsdatas[21];
						//$this->datainfo->gps_info_alarm_data = $gpsdatas[24];
						//$this->datainfo->gps_info_ad_input = $gpsdatas[25];
						$this->datainfo->gps_info_utc_coord = date("His",strtotime($dtraccar->devicetime));
						$this->datainfo->gps_info_utc_date = date("dmy",strtotime($dtraccar->devicetime));
						//$this->datainfo->gps_info_alarm_alert = $gpsdatas[28];
						$this->datainfo->gps_info_time = date("Y-m-d H:i:s",strtotime($dtraccar->devicetime));
						//$this->datainfo->gps_info_status = $gpsdatas[30];
						
						$attributes = json_decode($dtraccar->attributes, true);
				
						if(isset($attributes['ignition']))
						{
							if($attributes['ignition'] == false)
							{
								$ignition = false;
							}
							else
							{
								$ignition = true;
							}
						}
						else
						{
							if($dtraccar->speed > 0)
							{
								$ignition = true;
							}
							else
							{
								$ignition = false;
							}
						}
						
						if($ignition == 1)
						{
							$this->datainfo->gps_info_io_port = "0000100000";
						}
						else
						{
							$this->datainfo->gps_info_io_port = "0000000000";
						}
						
						if(isset($attributes['totalDistance']))
						{
							$this->datainfo->gps_info_distance = $attributes['totalDistance'];
						}
						$this->fromsocket = true;		
					}
					//print_r($row);exit;
				}
				else
				{
					return;
				}
			}
		}
		
		
		
		if (! $row || count($row) == 0)
		{		
			
			$tables = $this->gpsmodel->getTable($rowvehicle);
			//Get LIVE DATA
			if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
			{
				$this->db = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);
				if ($lasttime)
				{
					//$this->db->where("gps_time >", date("Y-m-d H:i:s", $lasttime));
				}
				$this->db->limit(1);
				$this->db->order_by("gps_time", "desc");
				$this->db->where("gps_name", $name);
				$this->db->where("gps_host", $host);
				$q = $this->db->get($tables['gps']);
				if ($q->num_rows() == 0)
				{
					$this->db = $this->load->database($tables["dbname"], TRUE);
					$this->db->limit(1);
					$this->db->order_by("gps_time", "desc");
					$this->db->where("gps_name", $name);
					$this->db->where("gps_host", $host);
					$q = $this->db->get($tables['gps']);
					
				}
			}
			else
			{
				$this->db = $this->load->database($tables["dbname"], TRUE);
				if ($lasttime)
				{
					//$this->db->where("gps_time >", date("Y-m-d H:i:s", $lasttime));
				}
				$this->db->limit(1);
				$this->db->order_by("gps_time", "desc");
				$this->db->where("gps_name", $name);
				$this->db->where("gps_host", $host);
				$q = $this->db->get($tables['gps']);
			}
			//END UPDATE LIVE DATA
			
			if ($q->num_rows() == 0)
			{
				$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ws))
				{
					$this->db = $this->load->database("gpshistory2", TRUE);
				}
				else
				{
					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
				}
				
				//alatberat
				if ($this->config->item("alatberat_app"))
				{
					$this->db = $this->load->database("gpshistory2", TRUE);
				}
				
				$this->db->limit(1);
				$this->db->order_by("gps_time", "desc");
				$this->db->where("gps_name", $name);
				$this->db->where("gps_host", $host);
				$this->db->where("gps_latitude <>", 0);
				$this->db->where("gps_longitude <>", 0);
				$q = $this->db->get($tablehist);
				
				if ($q->num_rows() == 0) return;				
			}
			
			$row = $q->row();
			$q->free_result();
			
			$tnow = dbmaketime($row->gps_time)+7*3600;
			
			if (! isset($tableerr))
			{
				$tableerr = $this->getGPSTableError($type);
			}
			
			if (false)
			//if ($tableerr && ($tnow < mktime()))
			{
				$sql = "
						SELECT *
						FROM
						(
							SELECT 	* 
							FROM  	`".$this->db->dbprefix.$tableerr."` 
							WHERE  	1
									AND (`gps_name` =  '".$name."')
									AND (`gps_host` =  '".$host."')
							".($lasttime ? ("AND (gps_time > '".date("Y-m-d H:i:s", $lasttime)."')") : '')."
							) t1
						WHERE 	1
						ORDER BY 	gps_time DESC 
						LIMIT 1 OFFSET 0
				";
				
				$q = $this->db->query($sql);
				
				if ($q->num_rows())
				{
					$rowerr = $q->row();
					
					$t = dbmaketime($row->gps_time);
					$terr = dbmaketime($rowerr->gps_time);
					
					if (($terr < mktime()) && ($terr > $t))
					{
						$row->gps_time = $rowerr->gps_time;
						$row->gps_utc_coord = $rowerr->gps_utc_coord;
						$row->gps_utc_date = $rowerr->gps_utc_date;
						//$row->gps_latitude = $rowerr->gps_latitude;
						//$row->gps_longitude = $rowerr->gps_longitude;
					}
				}
			}
			
			$tv = dbmaketime($row->gps_time);
			$tv += 7*3600;
			
			$tvj = mktime(date("G", $tv), 0, 0, date("n", $tv), date('j', $tv), date('Y', $tv));
			$nowj = mktime(date('G'), 0, 0, date("n"), date("j"), date("Y"));
			
			if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type !="TK315" && $rowvehicle->vehicle_type !="A14")
			{
			if (($row->gps_latitude*1 == 0) || ($row->gps_longitude*1 == 0) || ($tvj > $nowj))
			{	

				//Case Dokar B1477BZN
				$this->db = $this->load->database($tables["dbname"], TRUE);
				
				if ($lasttime)
				{
					$this->db->where("gps_time >", date("Y-m-d H:i:s", $lasttime));
				}
				
				$this->db->limit(1);
				$this->db->order_by("gps_time", "desc");
				$this->db->where("gps_time <=", date("Y-m-d H:i:s"));
				$this->db->where("gps_name", $name);
				$this->db->where("gps_host", $host);
				$this->db->where("gps_latitude <>", 0);
				$this->db->where("gps_longitude <>", 0);
				$q = $this->db->get($tables['gps']);
				
				if ($q->num_rows() == 0) 
				{
					$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
					
					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
					
					$this->db->limit(1);
					$this->db->order_by("gps_time", "desc");
					$this->db->where("gps_name", $name);
					$this->db->where("gps_host", $host);
					$this->db->where("gps_latitude <>", 0);
					$this->db->where("gps_longitude <>", 0);
					$q = $this->db->get($tablehist);
					
					if ($q->num_rows() == 0) return;
				}
				
				$row1 = $row;
				$row = $q->row();
				$q->free_result();
				$row->gps_time = $row1->gps_time;
				$row->gps_status = "V";
				$row->gps_utc_date = $row1->gps_utc_date;
				$row->gps_utc_coord = $row1->gps_utc_coord;
			}
			}
		}						
		
		
		$tgl = floor($row->gps_utc_date/10000);
		$bln = floor(($row->gps_utc_date%10000)/100);
		$thn = (($row->gps_utc_date%10000)%100)+2000;

		$jam = floor($row->gps_utc_coord/10000);
		$min = floor(($row->gps_utc_coord%10000)/100);
		$det = ($row->gps_utc_coord%10000)%100;

		$mtime = mktime($jam+7,$min, $det, $bln, $tgl, $thn);
		$mtimeori = mktime($jam,$min, $det, $bln, $tgl, $thn);
		
		// cek apakah data updated		
		
		//$delays = $this->config->item("css_tracker_delay");
		
		//for admin lacak
		if(isset($this->sess->user_type) && ($this->sess->user_type == 1))
		{
			$delays = $this->config->item("css_tracker_delay_admin");
		}
		else
		{
			$delays = $this->config->item("css_tracker_delay");
		}
		// for ssi 1933
		if(isset($rowvehicle) && ($rowvehicle->vehicle_user_id == 1933))
		{
			$delays = $this->config->item("css_tracker_delay_ssi");
		}
		
		if (in_array($this->sess->user_id, $this->config->item("user_pins"))) 
		{ 
			$delays = $this->config->item("css_tracker_delay_pins");
		}	

		$delay = $delays[count($delays)-2][0]*60;
		if ((mktime() - $mtime) > $delay)
		{
			//$this->notice_datadelay($name, $host, $mtime);
		}
		
		$row->gps_timestampori = $mtimeori;
		$row->gps_timestamp = $mtime;
					
		$row->gps_date_fmt = date("d/m/Y", $mtime);
		$row->gps_time_fmt = date("H:i:s", $mtime);
		
		//t6 invalid conditon
		if ($rowvehicle->vehicle_type == "T6" && $row->gps_status == "V")
		{
			$tables = $this->gpsmodel->getTable($rowvehicle);
			$this->db = $this->load->database($tables["dbname"], TRUE);
			
			$this->db->limit(1);
			$this->db->order_by("gps_time", "desc");
			$this->db->where("gps_time <=", date("Y-m-d H:i:s"));
			$this->db->where("gps_name", $name);
			$this->db->where("gps_host", $host);
			$this->db->where("gps_latitude <>", 0);
			$this->db->where("gps_longitude <>", 0);
			$this->db->where("gps_status", "A");
			$q_lastvalid = $this->db->get($tables['gps']);
				
			if ($q_lastvalid->num_rows() == 0) 
			{
				$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				
				$istbl_history = $this->config->item("dbhistory_default");
				if($this->config->item("is_dbhistory") == 1)
				{
					$istbl_history = $rowvehicle->vehicle_dbhistory_name;
				}
				$this->db = $this->load->database($istbl_history, TRUE);
					
				$this->db->limit(1);
				$this->db->order_by("gps_time", "desc");
				$this->db->where("gps_name", $name);
				$this->db->where("gps_host", $host);
				$this->db->where("gps_latitude <>", 0);
				$this->db->where("gps_longitude <>", 0);
				$this->db->where("gps_status", "A");
				$q_lastvalid = $this->db->get($tablehist);
					
				if ($q_lastvalid->num_rows() == 0) return;
			}
				
			$row_lastvalid = $q_lastvalid->row();
			//print_r($row_lastvalid);exit();
			$row->gps_longitude_real = getLongitude($row_lastvalid->gps_longitude, $row_lastvalid->gps_ew);
			$row->gps_latitude_real = getLatitude($row_lastvalid->gps_latitude, $row_lastvalid->gps_ns);	
		}
		else{
			if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type !="TK315" && $rowvehicle->vehicle_type !="A14")
			{
				$row->gps_longitude_real = getLongitude($row->gps_longitude, $row->gps_ew);
				$row->gps_latitude_real = getLatitude($row->gps_latitude, $row->gps_ns);	
			}
			else
			{
				$a = explode("-",$row->gps_latitude);
				if(isset($a[1]))
				{
					$row->gps_latitude_real = number_format($row->gps_latitude, 4, ".", "");
				}
				else
				{
					if($row->gps_ns == "S")
					{
						$row->gps_latitude_real = number_format("-".$row->gps_latitude, 4, ".", "");
					}
					else
					{
						$row->gps_latitude_real = number_format($row->gps_latitude, 4, ".", "");
					}
				}
				$row->gps_longitude_real = $row->gps_longitude;
			}
		}
		
		
		if($rowvehicle->vehicle_type == "TJAM")
		{
			$a = explode("-",$row->gps_latitude_real);
			if(isset($a[1]))
			{
				$row->gps_latitude_real_fmt = number_format($row->gps_latitude_real, 4, ".", "");
			}
			else
			{
				if($row->gps_ns == "S")
				{
					$row->gps_latitude_real_fmt = number_format("-".$row->gps_latitude_real, 4, ".", "");
				}
				else
				{
					$row->gps_latitude_real_fmt = number_format($row->gps_latitude_real, 4, ".", "");
				}
			}
		}
		else
		{
			$row->gps_latitude_real_fmt = number_format($row->gps_latitude_real, 4, ".", "");		
		}
		
		$row->gps_longitude_real_fmt = number_format($row->gps_longitude_real, 4, ".", "");
		//$row->gps_latitude_real_fmt = number_format($row->gps_latitude_real, 4, ".", "");		

		if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type !="TK315" && $rowvehicle->vehicle_type !="A14")
		{
			$mtime = mktime($jam+7,$min, $det, $bln, $tgl, $thn);				
			$nowtime = mktime(date('G'), date('i'), date('s'), date('n'), date('j'), date('Y'));
		}
		else
		{
			$mtime = mktime($jam,$min, $det, $bln, $tgl, $thn);				
			$nowtime = mktime(date('G'), date('i'), date('s'), date('n'), date('j'), date('Y'));
		}
		
		$arr = $this->lang->line('lmonth');
		
		$row->gps_date_fmt = date("j ",$mtime).$arr[date("n", $mtime)-1].date(" Y", $mtime);
		$row->gps_time_fmt = date("H:i:s", $mtime);
		$row->gps_speed_fmt = number_format($row->gps_speed*1.852, 0, "", ".");
		
		//$delays = $this->config->item("css_tracker_delay");
		
		//for admin lacak
		if(isset($this->sess->user_type) && ($this->sess->user_type == 1))
		{
			$delays = $this->config->item("css_tracker_delay_admin");
		}
		else
		{
			$delays = $this->config->item("css_tracker_delay");
		}
		// for ssi 1933
		if(isset($rowvehicle) && ($rowvehicle->vehicle_user_id == 1933))
		{
			$delays = $this->config->item("css_tracker_delay_ssi");
		}
		
		if (in_array($this->sess->user_id, $this->config->item("user_pins"))) 
		{ 
			$delays = $this->config->item("css_tracker_delay_pins");
		}	
		
		if (is_array($delays))
		{
			$found = false;
			for($i=0; $i < count($delays); $i++)
			{
				$deviasi = $nowtime-$mtime;
				if ($deviasi < ($delays[$i][0]*60)) continue;
				
				//echo $rows[$i]->gps_name." :: ".$rows[$i]->gps_host." :: ".$val[0]." :: ".date("M, jS Y H:i:s", $mtime)." :: ".date("M, jS Y H:i:s", $nowtime)."<br />\r\n";
				
				$row->css_delay_index = $i;
				$row->css_delay = $delays[$i];
				$row->css_delay_time = $deviasi." :: ".date('Ymd His', $nowtime)." :: ".date('Ymd His', $mtime);
				$found = true;
				break;
			}
			
			if (! $found)
			{
				$row->css_delay = $delays[count($delays)-1];
				$row->css_delay_time = 0;
				$row->css_delay_index = count($delays)-1;
			}
		}		
		
		$row->direction = $this->GetDirection($row->gps_course);
		
		if ($row->gps_speed*1)
		{
			$row->car_icon = $this->GetDirection($row->gps_course);	
		}
		else
		{
			$row->car_icon = 0;	
		}

		$this->db = $this->load->database("default", TRUE);
		
		if ($georeverse)
		{
			$row->georeverse = $this->GeoReverse($row->gps_latitude_real_fmt, $row->gps_longitude_real_fmt);
		}
		
		return $row;
	}
	
    function distanceByRadian($lat1, $lng1, $lat2, $lng2) {
        if (
            $lat1 and $lat2 and $lng1 and $lng2
            and (($lat1 != $lat2) and ($lng1 != $lng2))
        ) {
            return acos(sin($lat1)*sin($lat2)+cos($lat1)*cos($lat2)*cos($lng2-$lng1))* $this->earthRadius;
        } else {
            return 0;
        }
    }
    
    function distanceByDegree($lat1, $lng1, $lat2, $lng2) {
        return $this->distanceByRadian(deg2rad($lat1), deg2rad($lng1), deg2rad($lat2), deg2rad($lng2));
    }    
    
    function notice_datadelay($name, $host, $mtime)
    {
    	$this->load->helper('common');
    	
    	$this->db->where("vehicle_device", $name.'@'.$host);
    	$q = $this->db->get("vehicle");
    	
    	if ($q->num_rows() == 0) return;
    	
    	$vehicle = $q->row();
    	
    	// cek apakah hari ini sudah dikirim notice    	    	
/*    	
    	$this->db->where("notice_status", 1);
    	$this->db->where("notice_vehicle", $vehicle->vehicle_device);
    	$this->db->where("notice_created >=", date("Y-m-d 00:00:00"));
    	$this->db->where("notice_type", "datadelay");	
    	$total = $this->db->count_all_results("notice");
    	
    	if ($total > 0) return;
*/    	    	
    	$this->noticedelaydata($vehicle->vehicle_device, $mtime);
    }
    
	function noticedelaydata($device, $time)
	{
		if (! $device) return;
		
    	$this->db->where("vehicle_device", $device);
    	$this->db->join("user", "user_id = vehicle_user_id");
    	$this->db->join("agent", "agent_id = user_agent", "left outer");
    	$q = $this->db->get("vehicle");
    	
    	if ($q->num_rows() == 0) return;    	    	
    	
    	$vehicle = $q->row();    	
    	
    	if ($vehicle->user_agent == $this->config->item("GPSANDALASID"))
    	{
    		$tos = $this->config->item("GPSANDALAS_MAIL");
    	}
    	
    	$params['vehicle'] = $vehicle;		
    	$params['lastreceive'] = $time;		
    	
    	if ($vehicle->user_agent == $this->config->item("GPSANDALASID"))
    	{
			$params['ownerurl'] = "http://www.gpsandalas.com";
			$params['owner'] = "GPS Andalas Coorp.";
			
			$mailservice = "http://tracker.gpsandalas.com/cron/sendmail";
			$sender = "support@gpsandalas.com";
			$sendername = "GPS Andalas Coorp.";
			$subject = sprintf($this->lang->line('lnoticedelay_subject'), $sendername, $vehicle->vehicle_name." ".$vehicle->vehicle_no );			
		}
		else
		{
			$params['ownerurl'] = "http://www.lacak-mobil.com";
			$params['owner'] = "lacak-mobil.com";
			
			$mailservice = "http://www.lacak-mobil.com/cron/sendmail";
			$sender = "support@lacak-mobil.com";
			$sendername = "lacak-mobil.com";
			$subject = sprintf($this->lang->line('lnoticedelay_subject'), $sendername, $vehicle->vehicle_name." ".$vehicle->vehicle_no );			
		}
    	
		$message = $this->load->view("vehicle/noticedelay", $params, true);
		    	
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper("common");
		
		$emails = get_valid_emails($vehicle->user_mail);
		if ($emails !== FALSE)
		{
			foreach($emails as $email)
			{
				$tos[] = $email;
			}
		}
				
    	// get admin
    	
    	$this->db->distinct();
    	$this->db->select("user_mail");
    	$this->db->where("user_type", 1);
    	$q = $this->db->get("user");
    	$rows = $q->result();
    	    	
    	for($i=0; $i < count($rows); $i++)
    	{
			$emails = get_valid_emails($rows[$i]->user_mail);
			if ($emails !== FALSE)
			{
				foreach($emails as $email)
				{
					$ccs[] = $email;
				}
			}
    	}
    	
    	// get agent
    	
    	$this->db->distinct();
    	$this->db->select("user_mail");
    	$this->db->where("user_agent", $vehicle->user_agent);
    	$this->db->where("user_type", 3);    
    	$q = $this->db->get("user");    	
    	$rows = $q->result();

    	for($i=0; $i < count($rows); $i++)
    	{
    		$emails = get_valid_emails($rows[$i]->user_mail);
    		if ($emails === FALSE) continue;
    		
			foreach($emails as $email)
			{
				$tos[] = $email;
			}    		
    	}
    	
    	if ((! (isset($tos))) && (! (isset($ccs))))
    	{
    		return;
    	}
    	
    	if (isset($tos))
    	{    		
    		$json['to'] = $tos;
    		if (isset($ccs))
    		{
    			$json['ccs'] = $ccs;
    			maillocalhost($subject, $message, implode(",", $tos), $mailservice, $sender, $sendername, true, implode(",", $ccs));
    		}
    		else
    		{
    			maillocalhost($subject, $message, implode(",", $tos), $mailservice, $sender, $sendername, true);
    		}
    	}
    	else
    	{
    		$json['to'] = $ccs;
    		maillocalhost($subject, $message, implode(",", $ccs), $mailservice, $sender, $sendername, true);
    	}
    	    	 
		
		/*    	
		$config['protocol'] = "mail";
		$config['mailtype'] = "html";		
		$this->email->initialize($config);
    	    	    	 
    	if (isset($tos))
    	{
    		$this->email->to($tos);
    		$json['to'] = $tos;
    		
    		if (isset($ccs))
    		{
    			$this->email->bcc($ccs);
    			$json['ccs'] = $ccs;
    		}
    	}
    	else
    	{
    		$this->email->to($ccs);
    		$json['to'] = $ccs;
    	}

		if (valid_email($vehicle->agent_mail))
		{
    		$this->email->from($vehicle->agent_mail, $vehicle->agent_mail_name);
    	}
    	else
    	{
    		$this->email->from($this->config->item("admin_mail"), $this->config->item("admin_name"));
    	}
    	
    	$this->email->subject($subject);
    	$this->email->message($message);    	    	
    	
    	if (@$this->email->send()) 
    	{
    		$insert['notice_status'] = 1;
    	}
    	else
    	{
    		$insert['notice_status'] = 2;
    		$json['error'] = $this->email->print_debugger();
    	}
    	*/    	

		$insert['notice_status'] = 1;
		$insert['notice_desc'] = json_encode($json);
    	$insert['notice_vehicle'] = $vehicle->vehicle_device;    	
    	$insert['notice_created'] = date("Y-m-d H:i:s");
    	$insert['notice_type'] = "datadelay";

		$mydb = $this->load->database("master", TRUE);
    	$mydb->insert("notice", $insert);    	
    	
    	$this->db->cache_delete_all();
	}    
	
	function getTable($vehicle)
	{
		$db = $this->getDatabase($vehicle);
		
		if ($db)
		{
			$dbs = explode("|", $db);	
			
			unset($db);
			
			$db['hostname'] = substr($dbs[0], 2);
			$db['username'] = $dbs[2];
			$db['password'] = trim($dbs[3]);
			$db['database'] = $dbs[1];
			$db['dbdriver'] = "mysql";
			$db['dbprefix'] = "";
			$db['pconnect'] = TRUE;
			$db['db_debug'] = TRUE;
			$db['cache_on'] = FALSE;
			$db['cachedir'] = "cache";
			$db['char_set'] = "utf8";
			$db['dbcollat'] = "utf8_general_ci";			
			
			$this->session->set_userdata("dbsession", $db);
					
			$res["dbname"] = "dbsession";
			$res["gps"] = strtolower(str_replace("@", "@", $vehicle->vehicle_device))."_gps";
			$res["info"] = strtolower(str_replace("@", "@", $vehicle->vehicle_device))."_info";
			return $res;
		}
		
		$tblhists = $this->config->item("table_hist");
		
		$json = json_decode($vehicle->vehicle_info);
		
		$dbname = "default";
		
		if (isset($json->vehicle_ws))
		{
			$table = strtolower(str_replace("@", "@", $vehicle->vehicle_device))."_gps";
			$tableinfo = strtolower(str_replace("@", "@", $vehicle->vehicle_device))."_info";
		}
		else
		{
			$table = $this->getGPSTable($vehicle->vehicle_type);
			$tableinfo = $this->getGPSInfoTable($vehicle->vehicle_type);
		}

		if ($vehicle->vehicle_info)
		{
			$json = json_decode($vehicle->vehicle_info);
			
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					
					$table = $this->config->item("external_gpstable");
					$tableinfo = $this->config->item("external_gpsinfotable");
					$dbname = $database;										
				}
			}			
		}
		
		if (isset($json->vehicle_ws))
		{	
			$database = "gpshistory2";
			$table = strtolower(str_replace("@", "@", $vehicle->vehicle_device))."_gps";
			$tableinfo = strtolower(str_replace("@", "@", $vehicle->vehicle_device))."_info";
			$dbname = $database;	
		}
		
		//SEMENTARA 
		//OLD VEHICLE
		if ((isset($json->vehicle_ip)) && ($json->vehicle_ip == "119.235.20.251") && ((!isset($json->vehicle_port)) || $json->vehicle_port == ""))
		{
			if ($vehicle->vehicle_type == "T1" || $vehicle->vehicle_type == "T1_U1" || $vehicle->vehicle_type == "T1_1" || $vehicle->vehicle_type == "T4 NEW" ||
		    $vehicle->vehicle_type == "T4" || $vehicle->vehicle_type == "T4 Farrasindo" || $vehicle->vehicle_type == "T4 New" || $vehicle->vehicle_type == "T5 PULSE" || $vehicle->vehicle_type == "T5" ||
			$vehicle->vehicle_type == "INDOGPS" || $vehicle->vehicle_type == "indogps" )
			{

				$database = "datagpsold";
				$vtype = $vehicle->vehicle_type;
				switch ($vtype)
				{
					case "T1_U1":
						$table = "gps_t1_2";
						$tableinfo = "gps_info_t1_2";
					break;
					case "T1_1":
						$table = "gps_t1_1";
						$tableinfo = "gps_info_t1_1";
					break;
					case "T4 NEW":
						$table = "gps_gtp_new";
						$tableinfo = "gps_info_gtp_new";
					break;
					case "T4 New":
						$table = "gps_gtp_new";
						$tableinfo = "gps_info_gtp_new";
					break;
					case "T4 new":
						$table = "gps_gtp_new";
						$tableinfo = "gps_info_gtp_new";
					break;
					case "T4":
						$table = "gps_gtp";
						$tableinfo = "gps_info_gtp";
					break;
					case "T4 Farrasindo":
						$table = "gps_farrasindo";
						$tableinfo = "gps_info_farrasindo";
					break;
                    case "T5 PULSE":
                        $table = "gps_t5_pulse";
                        $tableinfo = "gps_info_t5_pulse";
                    break;
                    case "T5":
                        $table = "gps_t5";
                        $tableinfo = "gps_info_t5";
                    break;
					case "INDOGPS":
					case "indogps":
                        $table = "gps_indogps";
                        $tableinfo = "gps_info_indogps";
                    break;
					default:
						$table = "gps";
						$tableinfo = "gps_info";
				}
				
				$dbname = $database;	
			}
		}
		
		$res["dbname"] = $dbname;
		$res["gps"] = $table;
		$res["info"] = $tableinfo;
		
		return $res;
		
	}
	
	function getGPSData($vehicle)
	{
		$json = json_decode($vehicle->vehicle_info);
		if (! isset($json->vehicle_ws)) return FALSE;
		
		$url = explode(":", $json->vehicle_ws);
		
		$service_port = $url[1];
		$address = $url[0];

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) return FALSE;

		socket_set_option($socket, SOL_SOCKET,  SO_SNDTIMEO, array("sec"=>2, "usec"=>0));

		$result = @socket_connect($socket, $address, $service_port);
		if ($result === false) return;

		//socket_set_timeout($result, 60,0);
		$in = "lastinfo|".$vehicle->vehicle_device."\n";
		socket_write($socket, $in, strlen($in));

		$out = socket_read($socket, 1024, PHP_NORMAL_READ);
		
		socket_close($socket);
		
		return $out;
	}

	function getDatabase($vehicle)
	{
		$json = json_decode($vehicle->vehicle_info);
		if (! isset($json->vehicle_ws)) return FALSE;
		
		$url = explode(":", $json->vehicle_ws);
		
		$service_port = $url[1];
		$address = $url[0];

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) return FALSE;

		//socket_set_timeout($socket, 0,0);
		socket_set_option($socket, SOL_SOCKET,  SO_SNDTIMEO, array("sec"=>2, "usec"=>0));
		$result = @socket_connect($socket, $address, $service_port);
		if ($result === false) return;

		$in = "database|".$vehicle->vehicle_device."\n";
		socket_write($socket, $in, strlen($in));

		$out = socket_read($socket, 1024, PHP_NORMAL_READ);
		
		socket_close($socket);
		
		return $out;
	}
    
    function getHist($host, $deviceid, $t1, $t2)
        {
			$message = sprintf("HISTORY|%s|%s|%s|0|0|\n", $deviceid, date("Ymd", $t1), date("Ymd", $t2));
			$gpses = getTCPStream($host, $message);
			
			array_shift($gpses);
			array_pop($gpses);		
			array_pop($gpses);
			
			$totals = explode("|", array_pop($gpses));
			
			$gps = new stdClass;
			
			$gps->total = $totals[1];
			$gps->data = array();
			
			for($i=0; $i < count($gpses); $i++)
			{
				$gpsdatas = explode("|", $gpses[$i]);
				
				if (count($gpsdatas) < 30) continue;

				$row = new stdclass();
			
				$row->gps_name = substr($gpsdatas[0], 2);
				$row->gps_host = $gpsdatas[1];
				$row->gps_utc_coord = $gpsdatas[3];
				$row->gps_status = $gpsdatas[4];
				$row->gps_latitude = $gpsdatas[5];
				$row->gps_ns = $gpsdatas[6];
				$row->gps_longitude = $gpsdatas[7];
				$row->gps_ew = $gpsdatas[8];
				$row->gps_speed = $gpsdatas[9];
				$row->gps_course = $gpsdatas[10];
				$row->gps_utc_date = $gpsdatas[11];
				$row->gps_mvd = $gpsdatas[12];
				$row->gps_mv = $gpsdatas[13];
				$row->gps_cs = $gpsdatas[14];
				$row->gps_time = $gpsdatas[15];
				$row->gps_latitude_real = $gpsdatas[16];
				$row->gps_longitude_real = $gpsdatas[17];
				$row->gps_odometer = $gpsdatas[18];
				$row->gps_workhour = $gpsdatas[19];
			
				$row->gps_info_device = $gpsdatas[20];
				$row->gps_info_hdop = $gpsdatas[21];
				$row->gps_info_io_port = $gpsdatas[22];
				$row->gps_info_distance = $gpsdatas[23];
				$row->gps_info_alarm_data = $gpsdatas[24];
				$row->gps_info_ad_input = $gpsdatas[25];
				$row->gps_info_utc_coord = $gpsdatas[26];
				$row->gps_info_utc_date = $gpsdatas[27];
				$row->gps_info_alarm_alert = $gpsdatas[28];
				$row->gps_info_time = $gpsdatas[29];
				$row->gps_info_status = $gpsdatas[30];				


				$gps->data[] = $row;
			}
			
			return $gps;
		}
		
        function polygon($vehicle, $xcoord, $ycoord)
        {
			if(! ($sock = socket_create(AF_INET, SOCK_DGRAM, 0))) return FALSE;

			$server = "119.235.20.252";
			$port = 7171;
			$input = sprintf("GEOFENCE|%s|%s|%s\n", $vehicle, $xcoord, $ycoord);

			if (! socket_sendto($sock, $input , strlen($input) , 0 , $server , $port)) 
			{
				socket_close($sock);
				return FALSE;
			}

			if(socket_recv ( $sock , $reply , 2045 , 0 /*MSG_WAITALL*/ ) === FALSE) 
			{
				socket_close($sock);
				return FALSE;			
			}
		
			socket_close($sock);

	        return $reply;
        }
}
