<?php
include "base.php";

class History_others extends Base {
	function History_others()
	{
		parent::Base();	
		
		$this->load->model("gpsmodel");
	}

	function create_tables($vdevice = "", $type="")
	{
		$this->db = $this->load->database("webtracking_others", TRUE);
		$this->db->distinct();
		$this->db->select("vehicle_device");
		if ($vdevice != "" && $type != "")
		{
			$vedev = $vdevice."@".$type;
			$this->db->where("vehicle_device", $vedev);
		}
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0) return;
		
		$total = $q->num_rows();
		$rows = $q->result();
		
		foreach(array("gpshistory_others") as $dbname)
		{
			$historydb = $this->load->database($dbname, TRUE);
			$i = 0;
								
			foreach($rows as $row)
			{
				printf("%d/%d create table %s for %s\n", ++$i, $total, $dbname, $row->vehicle_device);
				
				$histtable = $this->load->view("db/gps", FALSE, TRUE);			
				$sql = sprintf($histtable, strtolower($row->vehicle_device)."_gps");						
				$historydb->query($sql);
				
				printf("=== %s_gps\n", strtolower($row->vehicle_device));

				$histinfotable = $this->load->view("db/info", FALSE, TRUE);
				$sql = sprintf($histinfotable, strtolower($row->vehicle_device)."_info");			
				$historydb->query($sql);
				
				printf("=== %s_info\n", strtolower($row->vehicle_device));
				
				//sleep(1);
				
			}
		}
	}
	
	function move_position($type = "", $order="asc", $limit=0, $delete="yes")
	{
		printf("=== MUlai \n");
		printf("Get Data TRACCAR OTHERS \n");
		$this->db = $this->load->database("GPS_TRACCAR_OTHERS", TRUE); //tbl position
		$this->concox = $this->load->database("GPS_5100", TRUE); //tbl port
		$attributes = "";
		
		//Get Device GT06 Dulu
		$this->db->order_by("id",$order);
		$this->db->select("id,name,uniqueid,group");
		if($type != "")
		{
			$this->db->where("name",$type);
		}
		$q = $this->db->get("devices");
		$devices = $q->result();
		for($j=0;$j<count($devices);$j++)
		{
			foreach(array("positions_gt06_5100") as $postable)
			{
				$ignition = 0;
				$battery = 0;
				$device_id = $devices[$j]->id;
				printf("============= Start Device %s \n",$devices[$j]->uniqueid);
				$this->db->group_by("fixtime");
				$this->db->order_by("fixtime",$order);
				$this->db->where("deviceid",$devices[$j]->id);
				$this->db->where("latitude <>",0);
				$this->db->where("longitude <>",0);
				if($limit > 0)
				{
					$this->db->limit($limit);
				}
				else
				{
					$this->db->limit(1000);
				}
				$q = $this->db->get($postable);
				$data = $q->result();
				$total_data = count($data);
				$limit_data = $total_data - 1;
				
				printf("Total Data %s \n",$total_data);
				printf("Limit Process %s \n",$limit_data);
				if($limit_data > 0){
					$next = 1;
					printf("============= Start Proses Data");
					//print_r($data);exit;
					for($i=0;$i<$limit_data;$i++)
					{
						printf("Data %s \n",$i+1);
						
						unset($val);
						unset($valinfo);
					
						$attributes = json_decode($data[$i]->attributes, true);
						//printf("Attributes %s \n",$attributes);
						
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
							//printf("Ignition : %s \n",$attributes['ignition']);
						}
						
						if($ignition == 1)
						{
							$valinfo["gps_info_io_port"] = "0000100000";
						}
						else
						{
							$valinfo["gps_info_io_port"] = "0000000000";
						}
						
						/* if(isset($attributes['battery']))
						{
							if($attributes['battery'] == false)
							{
								$battery = false;
							}
							else
							{
								$battery = true;
							}
							printf("battery : %s \n",$attributes['battery']);
						}
						
						if($battery == 1)
						{
							$val["gps_cs"] = $attributes['battery'];
						}
						else
						{
							$val["gps_cs"] = "-";
						} */
					
						$val["gps_name"] = $devices[$j]->uniqueid;
						$val["gps_host"] = $devices[$j]->name;
						$val["gps_type"] = $devices[$j]->name;
						$val["gps_utc_coord"] = date("His",strtotime($data[$i]->servertime));
						$val["gps_status"] = "A";
						$val["gps_latitude"] =  number_format($data[$i]->latitude, 4, ".", "");
						//$val["gps_ns"] = "";
						$val["gps_longitude"] = number_format($data[$i]->longitude, 4, ".", "");
						//$val["gps_ew"] = 
						$val["gps_speed"] = $data[$i]->speed; 
						$val["gps_course"] = $data[$i]->course; 
						$val["gps_utc_date"] = date("dmy",strtotime($data[$i]->servertime));
						//$val["gps_mvd"] =
						//$val["gps_mv"] =
						
						$val["gps_msg_ori"] = $data[$i]->attributes; 
						$val["gps_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->servertime));
						$val["gps_latitude_real"] =	number_format($data[$i]->latitude, 4, ".", "");
						$val["gps_longitude_real"] = number_format($data[$i]->longitude, 4, ".", "");
						//$val["gps_odometer"] =	
						//$val["gps_workhour"] =				
						
						$valinfo["gps_info_device"] = $devices[$j]->uniqueid."@".$devices[$j]->name;
						$valinfo["gps_info_utc_coord"] = date("His",strtotime($data[$i]->servertime));
						$valinfo["gps_info_utc_date"] = date("dmy",strtotime($data[$i]->servertime));
						$valinfo["gps_info_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->servertime));
						
						if(isset($attributes['totalDistance']))
						{
							$valinfo["gps_info_distance"] = $attributes['totalDistance'];
						}
						
						if(isset($attributes['battery']))
						{
							$battery = substr($attributes['battery'], 0, 1);
							$val["gps_cs"] = $battery;
							printf("battery : %s \n",$battery);
						}
					
						printf("Proses Insert ");
						
						//no condition
						/*
						if($devices[$j]->group == "TK309")
						{
							$this->concox = $this->load->database("GPS_GLOBAL_TK309", TRUE);
						}
						if($devices[$j]->group == "TK309PINS")
						{
							$this->concox = $this->load->database("GPS_GLOBAL_TK309", TRUE);
						}
						if($devices[$j]->group == "TK303")
						{
							$this->concox = $this->load->database("GPS_GLOBAL_TK303", TRUE);
						}
						if($devices[$j]->group == "A13")
						{	
							$this->concox = $this->load->database("GPS_GLOBAL_A13", TRUE);
						}
						*/
						
						$this->concox->insert("gps",$val);
						$this->concox->insert("gps_info",$valinfo);
						printf("------- Insert DONE \n");
						
						if($delete == "yes")
						{
							printf("Proses Delete");
							$this->db->where("fixtime",$data[$i]->fixtime);
							$this->db->where("deviceid",$devices[$j]->id);
							$this->db->delete($postable);
							printf("------ DELETE DONE \n");
						}
						printf("____________ \n");
					}
					
					$this->db->where("latitude",0);
					$this->db->where("longitude",0);
					$this->db->delete($postable);
					printf("------ DELETE LAT LNG 0 DONE \n");
					
				}else{
					printf("============= End Proses Data \n");
				}
				
				
				
		
			}
		}
		
		printf("=== FINISH \n");
	}
	
	function daily($name="", $host="", $maxdata=7000)
	{
		$this->dodaily($name, $host, $maxdata);
	}

	function dodaily($name="", $host="", $maxdata=7000, $offset = 0)
	{
		$start_time = date("d-m-Y H:i:s");
		$this->db = $this->load->database("webtracking_others", TRUE);
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		$this->db->distinct();
		$this->db->order_by("vehicle_id","desc");
		$this->db->select("vehicle_type, vehicle_device, vehicle_info");
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0) return;

		$rows = $q->result();
		$totalvehicle = count($rows);
		$i = 0;
		foreach($rows as $row)
		{
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}

			unset($repairs);
			printf("history daily for %s (%d/%d)\n", $row->vehicle_device, ++$i, $totalvehicle);
			
			//$this->db = $this->load->database("default", TRUE);
			//$this->db = $this->load->database("datagpsold_others", TRUE);
			$this->db = $this->load->database("webtracking_others", TRUE);
			$table = $this->gpsmodel->getGPSTable($row->vehicle_type);
			$tableinfo = $this->gpsmodel->getGPSInfoTable($row->vehicle_type);
		
			if ($row->vehicle_info)
			{
				$json = json_decode($row->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
											
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}
			
			//print_r($table." ".$tableinfo." ".$database);exit();
			
			if (isset($row->vehicle_info) || !isset($row->vehicle_info))
			{
				$json = json_decode($row->vehicle_info);
				if (!isset($json->vehicle_ws))
				{
					$start = mktime();
					$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
					$devices = explode("@", $row->vehicle_device);
					
					// gps
					$offset = 0;
					unset($isdelete);
					while(1)
					{
						$this->db->limit($maxdata, $offset);
						$this->db->where("gps_name", $devices[0]);
						$this->db->where("gps_host", $devices[1]);
						$this->db->where("gps_time <=", date("Y-m-d H:i:s", $now));
						$q = $this->db->get($table);					
						$total = $q->num_rows();
							
						printf("=== jumlah data gps: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
						if ($q->num_rows() == 0) break;
				
						$historydb = $this->load->database("gpshistory_others", TRUE);
				
						$gpses = $q->result_array();
						$q->free_result();
						foreach($gpses as $gps)
						{
							unset($gps['gps_id']);
					
							$historydb->insert(strtolower($row->vehicle_device)."_gps", $gps);
							$isdelete = true;
						}
				
						if ($total < $maxdata) break;
				
						$offset += $maxdata;
					}
			
					if (isset($isdelete))
					{
						printf("=== delete old data\r\n");
				
						$this->db->where("gps_name", $devices[0]);
						$this->db->where("gps_host", $devices[1]);
						$this->db->where("gps_time <=", date("Y-m-d H:i:s", $now));
						$this->db->delete($table);
				
						$repairs[$table] = true;
					}

					// gps info

					unset($isdelete);

					$offset = 0;
					while(1)
					{
						$this->db->limit($maxdata, $offset);
						$this->db->where("gps_info_device", $row->vehicle_device);
						$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
						$q = $this->db->get($tableinfo);					
						$total = $q->num_rows();
							
						printf("=== jumlah data info: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
						if ($q->num_rows() == 0) break;
				
						$historydb = $this->load->database("gpshistory_others", TRUE);
				
						$gpses = $q->result_array();
						$q->free_result();
						foreach($gpses as $gps)
						{
							unset($gps['gps_info_id']);
					
							$historydb->insert(strtolower($row->vehicle_device)."_info", $gps);
							$isdelete = true;
						}
				
						if ($total < $maxdata) break;
				
						$offset += $maxdata;
					}
			
					if (isset($isdelete))
					{
						printf("=== delete old data\r\n");
				
						$this->db->where("gps_info_device", $row->vehicle_device);
						$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
						$this->db->delete($tableinfo);					
				
						$repairs[$tableinfo] = true;
					}
			
					/* if (isset($repairs))
					{
						foreach(array_keys($repairs) as $repair)
						{
							$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
							printf("%s\r\n", $sql);
					
							$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
						}
				
						unset($repairs);
					} */
			
					printf("=== selesai\n");	
					
				}
				else
				{
					printf("=== Skip Websocket \n");
				}
				
			}
		} //finish foreach
	
		$finish_time = date("d-m-Y H:i:s");
		
		
	}

	function deletetraccar_event()
	{
		printf("=========== Start Auto Delete Traccar Events OTHERS ================\r\n");
		$this->db = $this->load->database("GPS_TRACCAR_OTHERS",true);
		$this->db->select("id");
		$q = $this->db->get("events");
		$data = $q->result();
		if($q->num_rows > 0)
		{
			printf("=== PROSES DELETE DATA EVENTS : %s ", $q->num_rows);
			$this->db->where("id <>",0);
			$this->db->delete("events");
			printf(" === DONE \n");
		}
		printf("=== FINISH");
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
