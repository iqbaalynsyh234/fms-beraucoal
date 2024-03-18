<?php
include "base.php";

class History extends Base {
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("gpsmodel");
		set_time_limit(0);
	}

	function create_tables($vdevice = "", $type="")
	{
		$this->db->distinct();
		$this->db->order_by("vehicle_id","desc");
		$this->db->select("vehicle_device");
		if ($vdevice != "" && $type != "")
		{
			$vedev = $vdevice."@".$type;
			$this->db->where("vehicle_device", $vedev);
		}
		$this->db->where("vehicle_device <>", "003100000967@T5");
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0) return;
		
		$total = $q->num_rows();
		$rows = $q->result();
		
		foreach(array("gpshistory", "gpsarchive") as $dbname)
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
	
	function create_tables_selected($vdevice = "", $type="")
	{
		
		$device = $vdevice."@".$type;
		foreach(array("gpshistory", "gpsarchive") as $dbname)
		{
			
			$i = 0;
			$dbname = $this->config->item("dbhistory_default");
			$historydb = $this->load->database($dbname, TRUE);
			printf("%d create table %s for %s\n", ++$i, $dbname, $device);
				$histtable = $this->load->view("db/gps", FALSE, TRUE);			
				$sql = sprintf($histtable, strtolower($device)."_gps");						
				$historydb->query($sql);
				
				printf("=== %s_gps\n", strtolower($device));

				$histinfotable = $this->load->view("db/info", FALSE, TRUE);
				$sql = sprintf($histinfotable, strtolower($device)."_info");			
				$historydb->query($sql);
				printf("=== %s_info\n", strtolower($device));
		}
	}
	
	function daily($name="", $host="", $maxdata=10000)
	{
		$this->dodaily($name, $host, $maxdata);
	}
	
	function dailyoffset($offset)
	{
		$this->dodaily("", "", 10000, $offset);
	}

	function dodaily($name="", $host="", $maxdata=10000, $offset = 0)
	{
		$start_time = date("d-m-Y H:i:s");

		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		$this->db->distinct();
		$this->db->order_by("vehicle_id","desc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id <>", "860");
		$this->db->where("vehicle_user_id <>", "1095");
		$this->db->where("vehicle_user_id <>", "1122");//dokar
		$this->db->where("vehicle_user_id <>", "1032");//balrich
		$this->db->where("vehicle_user_id <>", "1933");//ssi
		$this->db->where("vehicle_type <>", "T6");
		$this->db->where("vehicle_type <>", "T8");
		$this->db->where("vehicle_type <>", "T8_2");
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
			$this->db = $this->load->database("datagpsold", TRUE);
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
		
		
		//Send Email
		$cron_name = "Daily History";
		
		unset($mail);
		$mail['subject'] =  "Report Cron - ".$cron_name."";
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
End Data   : "."( ".$i." / ".$totalvehicle." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "it-dept@lacak-mobil.com";
		$mail['bcc'] = "budiyanto@lacak-mobil.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK");
		
	}

	//History Intan Utama
	function daily_intan($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_intan($name, $host, $maxdata);
	}
	
	//History Intan Utama
	function dodaily_intan($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$user_id_intan = "631";
		$this->db->distinct();
		$this->db->where("vehicle_user_id", $user_id_intan);
		//$this->db->where("vehicle_device", "002100000073@T5");
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}

	//History Dokar
	function daily_dokar($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_dokar($name, $host, $maxdata);
	}
	
	//History Dokar
	function dodaily_dokar($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$user_id_dokar = "1122";
		$this->db->distinct();
		$this->db->where("vehicle_user_id", $user_id_dokar);
		//$this->db->where("vehicle_device", "002100000073@T5");
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}

	//History Kopindosat
	function daily_kopindosat($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_kopindosat($name, $host, $maxdata);
	}
	
	//History Kopindosat
	function dodaily_kopindosat($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$user_id_kopindosat = "703";
		$this->db->distinct();
		$this->db->where("vehicle_user_id", $user_id_kopindosat);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}

	//History vehicle Port 50000
	function daily_port_50000($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_port_50000($name, $host, $maxdata);
	}
	
	//History vehicle Port 50000
	function dodaily_port_50000($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$vehicle_port = "50000";
		$this->db->distinct();
		$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}

	//History vehicle Port 50002
	function daily_port_50002($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_port_50002($name, $host, $maxdata);
	}
	
	//History vehicle Port 50002
	function dodaily_port_50002($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$vehicle_port = "50002";
		$this->db->distinct();
		$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}
	
	//History vehicle Port 50003
	function daily_port_50003($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_port_50003($name, $host, $maxdata);
	}
	
	//History vehicle Port 50003
	function dodaily_port_50003($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$vehicle_port = "50003";
		$this->db->distinct();
		$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}


	//History vehicle Port 50005
	function daily_port_50005($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_port_50005($name, $host, $maxdata);
	}
	
	//History vehicle Port 50005
	function dodaily_port_50005($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$vehicle_port = "50005";
		$this->db->distinct();
		$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}


	
	//History vehicle Port 50008
	function daily_port_50008($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_port_50008($name, $host, $maxdata);
	}
	
	//History vehicle Port 50008
	function dodaily_port_50008($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$vehicle_port = "50008";
		$this->db->distinct();
		$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}

	//History vehicle Port 50012
	function daily_port_50012($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_port_50012($name, $host, $maxdata);
	}
	
	//History vehicle Port 50012
	function dodaily_port_50012($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$vehicle_port = "50012";
		$this->db->distinct();
		$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}

	//History vehicle Port 50013
	function daily_port_50013($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_port_50013($name, $host, $maxdata);
	}
	
	//History vehicle Port 50013
	function dodaily_port_50013($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$vehicle_port = "50013";
		$this->db->distinct();
		$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}


	
	//History vehicle Port 50014
	function daily_port_50014($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_port_50014($name, $host, $maxdata);
	}
	
	//History vehicle Port 50014
	function dodaily_port_50014($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$vehicle_port = "50014";
		$this->db->distinct();
		$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}


	
	
	//History vehicle Port 50015
	function daily_port_50015($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_port_50015($name, $host, $maxdata);
	}
	
	//History vehicle Port 50015
	function dodaily_port_50015($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$vehicle_port = "50015";
		$this->db->distinct();
		$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}


	
	//History Data Dokar
	function daily_datadokar($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_datadokar($name, $host, $maxdata);
	}
	
	//History Data Dokar
	function dodaily_datadokar($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$user_id_dokar = "1122";
		$this->db->distinct();
		$this->db->where("vehicle_user_id", $user_id_dokar);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
						$database = "backup_db";
						//$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
											
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}
			
			
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}

	
	//History KIM
	function daily_kim($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_kim($name, $host, $maxdata);
	}
	
	//History KIM
	function dodaily_kim($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$user_id_kim = "860";
		$user_id_mbi = "1095";
		$user_id_mmi = "2318";

		$this->db->distinct();
		$this->db->order_by("vehicle_id", "desc");
		$this->db->where("vehicle_user_id", $user_id_kim);
		$this->db->or_where("vehicle_user_id", $user_id_mbi);
		$this->db->or_where("vehicle_user_id", $user_id_mmi);
		$this->db->where("vehicle_status", 1);


		$this->db->select("vehicle_type, vehicle_device, vehicle_info");
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0) return;

		$rows = $q->result();
		//print_r($rows);exit;

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
			
			$this->db = $this->load->database("default", TRUE);
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
			
		
			$start = mktime();
			$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
			//$a = date("Y-m-d H:i:s", $now);		
			//print_r($a);exit;								

			$devices = explode("@", $row->vehicle_device);
			
			//gps

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
				//print_r($total);exit;				
			
				printf("=== jumlah data gps: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
				if ($q->num_rows() == 0) break;
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			/*if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}*/
			
			printf("=== selesai\n");			
		}
			
	}

	
	//History Andalas
	function daily_andalas($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_andalas($name, $host, $maxdata);
	}
	
	//History Andalas
	function dodaily_andalas($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$user_id_rahayu = "284";
		$user_id_transrajawali = "500";
		$user_id_tanjungsari = "314";
		$this->db->distinct();
		$this->db->where("vehicle_user_id", $user_id_rahayu);
		$this->db->or_where("vehicle_user_id", $user_id_transrajawali);
		$this->db->or_where("vehicle_user_id", $user_id_tanjungsari);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}


	//History Daily T1 & T1_U1
	function daily_t1($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_t1($name, $host, $maxdata);
	}
	
	//History Daily T1 & T1_U1
	function dodaily_t1($name="", $host="", $maxdata=10000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$vehicle_port = "50015";
		$this->db->distinct();
		$this->db->where("vehicle_type","T1");
		$this->db->or_where("vehicle_type","T1_U1");
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
			if (isset($repairs))
			{
				foreach(array_keys($repairs) as $repair)
				{
					$sql = "REPAIR TABLE ".$this->db->dbprefix.$repair;
					printf("%s\r\n", $sql);
					
					$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$repair);
				}
				
				unset($repairs);
			}
			
			printf("=== selesai\n");			
		}
			
	}

	//History Daily By Type
	function daily_bytype($type="", $maxdata=10000)
	{
		$this->dodaily_bytype($type, $maxdata);
	}
	
	//History Daily By Type
	function dodaily_bytype($type, $maxdata=10000, $offset = 0)
	{
		if (strlen($type) > 0)
		{
			$v = $type; 
		}
		else
		{
			$v = "T5 PULSE";
		}
		
		$this->db->distinct();
		$this->db->where("vehicle_type", $v);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
	}


	
	function rehist($maxdata=10000)
	{
		$table_hist = $this->config->item("table_hist");
		$table_hist_info = $this->config->item("table_hist_info");
		
		$this->db->distinct();
		$this->db->select("vehicle_type, vehicle_device, vehicle_info, user_login");
		$this->db->where("user_login <>", "transrajawali");
		$this->db->where("vehicle_no <>", "B1202TKP");
		$this->db->not_like("user_login", "farrasindo");
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0) return;

		$rows = $q->result();
		$totalvehicle = count($rows);
		$i = 0;
		foreach($rows as $row)
		{
			$i++;
			
			if (strpos($row->user_login, "intan_utama") !== FALSE)
			{
				continue;
			}

			if ($i < 747)
			{
				continue;
			}
						
			printf("history rehist for %s:%s (%d/%d)\n", $row->user_login, $row->vehicle_device, $i, $totalvehicle);

			$vtype = strtoupper($row->vehicle_type);
			if (! isset($table_hist[$vtype])) continue;
			if (! isset($table_hist_info[$vtype])) continue;
			
			$this->db = $this->load->database("colo1", TRUE);
			
			$table = $table_hist[$vtype];
			$tableinfo = $table_hist_info[$vtype];
			
			if ($row->vehicle_info)
			{
				$json = json_decode($row->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable_history");
						$tableinfo = $this->config->item("external_gpsinfotable_history");
											
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}
			
			
			$start = mktime();
			$now = mktime(0, 0, 0, date('n'), date('j', $start)-2, date('Y'))-7*3600;									

			$devices = explode("@", $row->vehicle_device);
			
			// gps

			$offset = 0;
			while(1)
			{
				printf("=== ambil gps... %d/%d\n", $offset, $maxdata);
				
				$this->db->limit($maxdata, $offset);
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$this->db->where("gps_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($table);					
				$total = $q->num_rows();
							
				printf("=== jumlah data gps: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				if ($q->num_rows() == 0) break;
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
				$tothist = $historydb->count_all_results(strtolower($row->vehicle_device)."_gps");
				if (($offset == 0) && ($tothist > 0)) 
				{
					printf("=== sudah pernah %d\r\n", $tothist);
					break;
				}

				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_id']);
					
					$historydb->insert(strtolower($row->vehicle_device)."_gps", $gps);
				}
				
				if ($total < $maxdata) break;
				
				$offset += $maxdata;
			}

			// gps info
			
			$offset = 0;
			while(1)
			{
				printf("=== ambil info... %d/%d\n", $offset, $maxdata);
				
				$this->db->limit($maxdata, $offset);
				$this->db->where("gps_info_device", $row->vehicle_device);
				$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($tableinfo);					
				$total = $q->num_rows();
							
				printf("=== jumlah data info: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);

				if ($q->num_rows() == 0) break;
				
				$historydb = $this->load->database("gpshistory", TRUE);

				$tothist = $historydb->count_all_results(strtolower($row->vehicle_device)."_info");
				if (($offset == 0) && ($tothist > 0)) 
				{
					printf("=== sudah pernah %d\r\n", $tothist);
					break;
				}
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_info_id']);
					
					$historydb->insert(strtolower($row->vehicle_device)."_info", $gps);
				}
				
				if ($total < $maxdata) break;
				
				$offset += $maxdata;
			}
			
			printf("=== selesai\n");			
		}
			
	}

	function archive($maxdata=1000)
	{
		// configurasi 
	
		$this->db->limit(1);
		$this->db->order_by("config_lastmodified", "desc");
		$this->db->where("config_name", "maxhist");
		$q = $this->db->get("config");
		
		if ($q->num_rows() == 0) return;
		
		$row = $q->row();
		if (! $row->config_value) return;
		
		$thist = mktime(-7, 0, 0, date('n')-$row->config_value, date('j'), date('Y'));
		
		printf("[%s] archive < %s\r\n", date("Ymd H:i:s"), date("d/m/Y", $thist)); 
		
		$this->db->distinct();
		$this->db->select("vehicle_type, vehicle_device, vehicle_info, user_login");
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0) return;

		$rows = $q->result();
		$totvehicle = count($rows);
		
		$i = 0;		
		foreach($rows as $row)
		{
			$table = strtolower($row->vehicle_device)."_gps";
			$tableinfo = strtolower($row->vehicle_device)."_info";
			
			printf("[%s] archiving  %s:%s (%d/%d)\r\n", date("Ymd H:i:s"), $row->user_login, $row->vehicle_device, ++$i, $totvehicle); 
			
			// gps

			$offset = 0;
			$isdelete = false;
			
			while(1)
			{
				printf("=== gps %d - %d\r\n", $offset, $maxdata+$offset);
				
				$historydb = $this->load->database("gpshistory", TRUE);			
				$historydb->limit($maxdata, $offset);
				$historydb->where("gps_time <", date("Y-m-d", $thist));
				$q = $historydb->get($table);
				
				if ($q->num_rows() == 0) break;
				
				$total = $q->num_rows();
				$gpses = $q->result_array();									
				$q->free_result();
				
				$archivedb = $this->load->database("gpsarchive", TRUE);			
				foreach($gpses as $gps)
				{
					unset($gps["gps_id"]);							
					$archivedb->insert($table, $gps);
					$isdelete = true;
				}
				
				if ($total < $maxdata) break;				
				$offset += $maxdata;
			}
			
			if ($isdelete)
			{
				printf("=== delete gps data\r\n");
				
				$historydb = $this->load->database("gpshistory", TRUE);			
				$historydb->where("gps_time <", date("Y-m-d", $thist));
				$historydb->delete($table);			
				
				if (date("w") == 0)
				{
					$historydb->query("OPTIMIZE TABLE `".$table."`");
				}
			}
				
			// info
			
			$offset = 0;
			$isdelete = false;
			
			while(1)
			{
				printf("=== info %d - %d\r\n", $offset, $maxdata+$offset);
				
				$historydb = $this->load->database("gpshistory", TRUE);
				$historydb->limit($maxdata, $offset);
				$historydb->where("gps_info_time <", date("Y-m-d", $thist));
				$q = $historydb->get($tableinfo);
				
				if ($q->num_rows() == 0) break;
				
				$total = $q->num_rows();
				$infos = $q->result_array();						
				$q->free_result();
				
				$archivedb = $this->load->database("gpsarchive", TRUE);
				foreach($infos as $info)
				{
					unset($info["gps_info_id"]);								
					$archivedb->insert($tableinfo, $info);
					$isdelete = true;
				}

				if ($total < $maxdata) break;				
				$offset += $maxdata;
			}
			
			if ($isdelete)
			{
				printf("=== delete info data\r\n");

				$historydb = $this->load->database("gpshistory", TRUE);
				$historydb->where("gps_info_time <", date("Y-m-d", $thist));
				$historydb->delete($tableinfo);			

				if (date("w") == 0)
				{
					$historydb->query("OPTIMIZE TABLE `".$tableinfo."`");
				}
			}
		}
	}

	function movelog()
	{
		$basedir = "/var/www/html/lacak-mobil.com/logs";
		
		@rename($basedir."/daily.log", $basedir."/daily/".date("Ymd").".log");
		@rename($basedir."/archive.log", $basedir."/archive/".date("Ymd").".log");
		@rename($basedir."/oldvehicle.log", $basedir."/oldvehicle/".date("Ymd").".log");
		@rename($basedir."/geofencealert.log", $basedir."/geofence/".date("Ymd").".log");
		@rename($basedir."/speedalert.log", $basedir."/speed/".date("Ymd").".log");
		@rename($basedir."/parkalert.log", $basedir."/park/".date("Ymd").".log");
		@rename($basedir."/invoice.log", $basedir."/invoice/".date("Ymd").".log");
		@rename($basedir."/invoice_flat.log", $basedir."/flatinvoice/".date("Ymd").".log");
		@rename($basedir."/trash.log", $basedir."/trash/".date("Ymd").".log");
	}
	
	function rehist_colo2($t, $name, $host, $max=1000)
	{
		$t = dbintmaketime($t, 0);
		
		$t1 = mktime(-7, 0, 0, date('n', $t), date('j', $t), date("Y", $t));
		$t2 = mktime(-7, 0, 0, date('n', $t), date('j', $t)+1, date("Y", $t));
		
		$databases = $this->config->item("databases");
		foreach($databases as $database)
		{
			foreach($database as $dbname)
			{
				if ($dbname == "T52WAYCOLO2_P3315") continue;

				printf("database name: %s\r\n", $dbname);

				$colo2db = $this->load->database($dbname, TRUE);

				$offset = 0;
				while(1)
				{
					printf("=== get gps core data %d %d\r\n", $offset, $offset+$max);
					
					$colo2db->limit($max, $offset);
					$colo2db->where("gps_name", $name);
					$colo2db->where("gps_host", $host);
					$colo2db->where("gps_time >=", date("Y-m-d H:i:s", $t1));
					$colo2db->where("gps_time <=", date("Y-m-d H:i:s", $t2));
					$q = $colo2db->get("gps");
					
					if ($q->num_rows() == 0) break;
					
					$rows = $q->result_array();
					$q->free_result();
					
					$historydb = $this->load->database("gpshistory", TRUE);
					
					foreach($rows as $row)
					{
						unset($row['gps_id']);												
						$historydb->insert(strtolower($name)."@".strtolower($host)."_gps", $row);						
					}
					
					if (count($rows) <= $max) break;
					
					$offset += $max;
				}
				
				$colo2db = $this->load->database($dbname, TRUE);

				$offset = 0;
				while(1)
				{
					printf("=== get gps info data %d %d\r\n", $offset, $offset+$max);
					
					$colo2db->limit($max, $offset);
					$colo2db->where("gps_info_device", $name."@".$host);
					$colo2db->where("gps_info_time >=", date("Y-m-d H:i:s", $t1));
					$colo2db->where("gps_info_time <=", date("Y-m-d H:i:s", $t2));
					$q = $colo2db->get("gps_info");
					
					if ($q->num_rows() == 0) break;
					
					$rows = $q->result_array();
					$q->free_result();
					
					$historydb = $this->load->database("gpshistory", TRUE);
					
					foreach($rows as $row)
					{
						unset($row['gps_info_id']);												
						$historydb->insert(strtolower($name)."@".strtolower($host)."_info", $row);						
					}
					
					if (count($rows) <= $max) break;
					
					$offset += $max;
				}				
			}
		}
	}
	
	function trash()
	{
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		$rows = $q->result();
				
		foreach($rows as $row)
		{
			$vehicles[strtoupper($row->vehicle_device)] = $row;
		}
				
		unset($trashes);
		$count = 0;
		$totvehicle = count($rows);
		
		foreach($rows as $row)
		{
			printf("[%s] trashing  %s:%s (%d/%d)\r\n", date("Ymd H:i:s"), $row->user_login, $row->vehicle_device, ++$count, $totvehicle); 
			
			$database = "default";
			
			$this->db = $this->load->database($database, TRUE);
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
			
			$t = mktime(0, 0, 0, date('n')+1, date('j'), date('Y'));
			$t1 = mktime(0, 0, 0, date('n')-1, date('j'), date('Y'));
			if (! isset($trashes[$database][$table]))
			{				
				printf("1 month proses table %s:%s\n", $database, $table);
				
				$this->db->where("gps_time >=", date("Y-m-d H:i:s", $t));
				$this->db->delete($table);
				
				printf("%d terhapus\n", $this->db->affected_rows());

				printf("1 month ago proses table %s:%s\n", $database, $table);
				
				$this->db->where("gps_time <=", date("Y-m-d H:i:s", $t1));
				$this->db->delete($table);
				
				printf("%d terhapus\n", $this->db->affected_rows());

				$trashes[$database][$table] = true;
				
				// cek apakah data yang tidak ada di table vehicle
				//if ($database == "T5COLOSERVICE_LACAKMOBIL")
				if (true)
				{
				
					$this->db->distinct();
					$this->db->select("gps_name, gps_host");
					$q = $this->db->get($table);
					
					$rowsvehicle = $q->result();
					for($i=0; $i < count($rowsvehicle); $i++)
					{
						if (! isset($vehicles[strtoupper($rowsvehicle[$i]->gps_name.'@'.$rowsvehicle[$i]->gps_host)]))
						{
							printf("proses kendaraan yang tidak ada di table vehicle %s\n", $rowsvehicle[$i]->gps_name.'@'.$rowsvehicle[$i]->gps_host);
							
							$this->db->where("gps_name", $rowsvehicle[$i]->gps_name);
							$this->db->where("gps_host", $rowsvehicle[$i]->gps_host);
							$this->db->delete($table);
							
							printf("%d gps terhapus\n", $this->db->affected_rows());

							$this->db->where("gps_info_device", $rowsvehicle[$i]->gps_name.'@'.$rowsvehicle[$i]->gps_host);
							$this->db->delete($tableinfo);
							
							printf("%d info terhapus\n", $this->db->affected_rows());
							
							continue;
						}
						
						$myvehicle = $vehicles[strtoupper($rowsvehicle[$i]->gps_name.'@'.$rowsvehicle[$i]->gps_host)];
						$mydb = "default";
						
						if ($myvehicle->vehicle_info)
						{
							$myjson = json_decode($myvehicle->vehicle_info);
							if (isset($myjson->vehicle_ip) && isset($myjson->vehicle_port))
							{
								$databases = $this->config->item('databases');
								if (isset($databases[$myjson->vehicle_ip][$myjson->vehicle_port]))
								{
									$mydb = $databases[$myjson->vehicle_ip][$myjson->vehicle_port];
								}								
							}
						}
												
						if ($mydb == $database) continue;
						
						printf("%s\n", $rowsvehicle[$i]->gps_name.'@'.$rowsvehicle[$i]->gps_host.' -> '.$mydb);						
						// copy to history
						
						$offset = 0;
						while(1)
						{
							$this->db->limit(1000, $offset);
							$this->db->where("gps_name", $rowsvehicle[$i]->gps_name);
							$this->db->where("gps_host", $rowsvehicle[$i]->gps_host);
							$q = $this->db->get($table);
							
							$total = $q->num_rows();							
							
							printf("gps wrong db: move %d %d\n", $offset, $total);
							if ($total == 0)
							{
								break;
							}
							
							$rowstrash = $q->result_array();
							foreach($rowstrash as $trash)
							{
								unset($trash["gps_id"]);
								$historydb = $this->load->database("gpshistory", TRUE);
								$historydb->insert(strtolower($rowsvehicle[$i]->gps_name.'@'.$rowsvehicle[$i]->gps_host)."_gps", $trash);
							}
							
							if ($total < 1000)
							{
								break;
							}
							
							$offset += 1000;
						}
						
						$this->db->where("gps_name", $rowsvehicle[$i]->gps_name);
						$this->db->where("gps_host", $rowsvehicle[$i]->gps_host);
						$this->db->delete($table);
						
						printf("gps wrong db: %d terhapus\n", $this->db->affected_rows());
						
						$offset = 0;
						while(1)
						{
							$this->db->limit(1000, $offset);
							$this->db->where("gps_info_device", $rowsvehicle[$i]->gps_name.'@'.$rowsvehicle[$i]->gps_host);
							$q = $this->db->get($tableinfo);
							
							$total = $q->num_rows();							
							
							printf("gps info wrong db: move %d\n", $total);
							if ($total == 0)
							{
								break;
							}
							
							$rowstrash = $q->result_array();
							foreach($rowstrash as $trash)
							{
								unset($trash["gps_info_id"]);
								$historydb = $this->load->database("gpshistory", TRUE);
								$historydb->insert(strtolower($rowsvehicle[$i]->gps_name.'@'.$rowsvehicle[$i]->gps_host)."_info", $trash);
							}
							
							if ($total < 1000)
							{
								break;
							}
							
							$offset += 1000;
						}
						
						$this->db->where("gps_info_device", $rowsvehicle[$i]->gps_name.'@'.$rowsvehicle[$i]->gps_host);
						$this->db->delete($tableinfo);
						
						printf("gps info wrong db: %d terhapus\n", $this->db->affected_rows());
					}
				}
			}								
			
			if (! isset($trashes[$database][$tableinfo]))
			{
				printf("1 month proses table info %s:%s\n", $database, $tableinfo);
				
				$this->db->where("gps_info_time >=", date("Y-m-d H:i:s", $t));
				$this->db->delete($tableinfo);			
				
				printf("%d terhapus\n", $this->db->affected_rows());
					
				printf("1 month ago table info %s:%s\n", $database, $tableinfo);
				
				$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $t1));
				$this->db->delete($tableinfo);			
				
				printf("%d terhapus\n", $this->db->affected_rows());

				$trashes[$database][$tableinfo] = true;				
			}
			
			if ($row->vehicle_status == 3)
			{
				printf("inactive vehicle %s:%s\n", $row->user_login, $row->vehicle_device);				
				
				list($name, $host) = explode("@", $row->vehicle_device);
				
				$this->db->where("gps_name", $name);
				$this->db->where("gps_host", $host);				
				$this->db->delete($table);
				
				printf("%d terhapus\n", $this->db->affected_rows());
				
				printf("inactive vehicle info %s:%s\n", $database, $tableinfo);

				$this->db->where("gps_info_device", $row->vehicle_device);
				$this->db->delete($tableinfo);

				printf("%d terhapus\n", $this->db->affected_rows());
			}						
		}
		
		
	}

	//history new dokar (16-10-2013)
	function daily_newdokar($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_newdokar($name, $host, $maxdata);
	}

	function dodaily_newdokar($name="", $host="", $maxdata=7500, $offset = 0)
	{
		$start_time = date("d-m-Y H:i:s");
		
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		$this->db->distinct();
		$this->db->order_by("vehicle_id","desc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", "1122");
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
			$this->db = $this->load->database("datagpsold", TRUE);
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
		} 
			
		$finish_time = date("d-m-Y H:i:s");
		echo "Selesai...."." ".$finish_time;
		//finish foreach
		
		//Send Email
		
		$cron_name = "DAILY NEWDOKAR";
		$message =  urlencode(
					"".$cron_name." \n".
					"Start: ".$start_time." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram("3916",$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		unset($mail);
		$mail['subject'] =  "DOKAR - ".$cron_name."";
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
End Data   : "."( ".$i." / ".$totalvehicle." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK");
			
	}

	function daily_port($port="", $order="asc", $maxdata=7500, $offset=0)
	{
		$this->db->distinct();
		$this->db->order_by("vehicle_id",$order);
		if (strlen($port) > 0)
		{
			$vehicle_port = $port;
			$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
		}
		//$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_id']);
					if(isset($gps['gps_sent']))
					{
						unset($gps['gps_sent']);
					}
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_info_id']);
					if(isset($gps['gps_info_sent']))
					{
						unset($gps['gps_info_sent']);
					}
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
			
	}
	
	function daily_multiport($databasename="", $order="asc", $maxdata=7500, $offset=0)
	{
		$startproses = date("Y-m-d H:i:s");
		//select list port
		$this->db = $this->load->database("default", TRUE);
		$this->db->select("port_value,port_database");
		if (isset($databasename))
		{
			$this->db->where("port_database", $databasename);
		}
		$qport = $this->db->get("cron_port");
		if ($qport->num_rows() == 0) return;

		$rowsport = $qport->result();
		$totalport = count($rowsport);
		$p = 0;
		
		foreach($rowsport as $rowport)
		{
			if (($p+1) < $offset)
			{
				$p++;
				continue;
			}
			printf("history daily Port : %s (%d/%d)\n", $rowport->port_value, ++$p, $totalport);
			$port = $rowport->port_value;
			
			//--//
			$this->db = $this->load->database("default", TRUE);
			$this->db->distinct();
			$this->db->order_by("vehicle_id",$order);
			if (strlen($port) > 0)
			{
				$vehicle_port = $port;
				$this->db->where("vehicle_info LIKE '%".$vehicle_port."%'",null);
			}
			$this->db->where("vehicle_status <>", 3);
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
				printf("history daily %s for %s (%d/%d)\n", $rowport->port_value ,$row->vehicle_device, ++$i, $totalvehicle);
				
				$this->db = $this->load->database("default", TRUE);
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
					
					$historydb = $this->load->database("gpshistory", TRUE);
					
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
					
					$historydb = $this->load->database("gpshistory", TRUE);
					
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
				
				//--//
				
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
			
			
		}
		$finish_time = date("Y-m-d H:i:s");
		$cron_name = "DAILY MULTIPORT"." ".$databasename;
		$message =  urlencode(
					"".$cron_name." \n".
					"Start: ".$startproses." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram("3916",$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		printf("=== FINISH \n");
			
	}
	
	function daily_user($user="", $order="asc", $maxdata=8500, $offset=0)
	{
		$this->db->distinct();
		$this->db->order_by("vehicle_id",$order);
		if (strlen($user) > 0)
		{
			$vehicle_user_id = $user;
			$this->db->where("vehicle_user_id", $vehicle_user_id);
		}
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
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
			
	}
	
	function daily_db($dbname="", $histdb="gpshistory", $maxdata=7500, $offset=0)
	{
		$this->db = $this->load->database($dbname,true);
		$this->db->group_by("gps_name");
		$this->db->select("gps_name,gps_host");
		$q = $this->db->get("gps");
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
			printf("history daily for %s (%d/%d)\n", $row->gps_name, ++$i, $totalvehicle);
			$start = mktime();
			$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
			$devices = $row->gps_name."@".$row->gps_host;
			$offset = 0;
			unset($isdelete);
			$table = $this->config->item("external_gpstable");
			$tableinfo = $this->config->item("external_gpsinfotable");
			while(1)
			{
				$this->db->limit($maxdata, $offset);
				$this->db->where("gps_name", $row->gps_name);
				$this->db->where("gps_host", $row->gps_host);
				$this->db->where("gps_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($table);					
				$total = $q->num_rows();
				printf("=== jumlah data gps: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				if ($q->num_rows() == 0) break;
				
				$istbl_history = $histdb;
				$historydb = $this->load->database($istbl_history, TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_id']);
					if(isset($gps['gps_sent']))
					{
						unset($gps['gps_sent']);
					}
					$historydb->insert(strtolower($devices)."_gps", $gps);
					$isdelete = true;
				}
				if ($total < $maxdata) break;
				$offset += $maxdata;
			}
			if (isset($isdelete))
			{
				printf("=== delete old data\r\n");
				$this->db->where("gps_name", $row->gps_name);
				$this->db->where("gps_host", $row->gps_host);
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
				$this->db->where("gps_info_device", $devices);
				$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($tableinfo);					
				$total = $q->num_rows();
				printf("=== jumlah data info: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				if ($q->num_rows() == 0) break;
				$istbl_history = $histdb;
				$historydb = $this->load->database($istbl_history, TRUE);
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_info_id']);
					if(isset($gps['gps_info_sent']))
					{
						unset($gps['gps_info_sent']);
					}
					$historydb->insert(strtolower($devices)."_info", $gps);
					$isdelete = true;
				}
				if ($total < $maxdata) break;
				$offset += $maxdata;
			}
			if (isset($isdelete))
			{
				printf("=== delete old data\r\n");
				$this->db->where("gps_info_device", $devices);
				$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$this->db->delete($tableinfo);					
				$repairs[$tableinfo] = true;
			}
		printf("=== selesai\n");	
		}
	}
	
	//kim
	function daily_newkim($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_newkim($name, $host, $maxdata);
	}

	function dodaily_newkim($name="", $host="", $maxdata=10000, $offset = 0)
	{
		$start_time = date("d-m-Y H:i:s");
		
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		$this->db->distinct();
		$this->db->order_by("vehicle_id","desc");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_user_id", "860");
		$this->db->or_where("vehicle_user_id", "1095");
		$this->db->or_where("vehicle_user_id", "2318");
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
			$this->db = $this->load->database("datagpsold", TRUE);
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
		} 
			
		$finish_time = date("d-m-Y H:i:s");
		echo "Selesai...."." ".$finish_time;
		//finish foreach
		
		//Send Email
		
		$cron_name = "daily_kim";
		
		
		unset($mail);
		$mail['subject'] =  "Report Cron - ".$cron_name."";
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "surahman@lacak-mobil.com";
		$mail['bcc'] = "report.daily_kim@gmail.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK");
			
	}
	
	//ssi
	function daily_newssi($name="", $host="", $maxdata=10000)
	{
		$this->dodaily_newssi($name, $host, $maxdata);
	}

	function dodaily_newssi($name="", $host="", $maxdata=10000, $offset = 0)
	{
		$start_time = date("d-m-Y H:i:s");
		
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		$this->db->distinct();
		$this->db->order_by("vehicle_id","desc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", "1933");
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
			$this->db = $this->load->database("datagpsold", TRUE);
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
		} 
			
		$finish_time = date("d-m-Y H:i:s");
		echo "Selesai...."." ".$finish_time;
		//finish foreach
		
		//Send Email
		
		$cron_name = "HISTORY DAILY";
		
		
		unset($mail);
		$mail['subject'] =  "SSI - ".$cron_name."";
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
End Data   : "."( ".$i." / ".$totalvehicle." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK");
			
	}

	//balrich
	function daily_newbalrich($name="", $host="", $maxdata=7000)
	{
		$this->dodaily_newbalrich($name, $host, $maxdata);
	}

	function dodaily_newbalrich($name="", $host="", $maxdata=7000, $offset = 0)
	{
		$start_time = date("d-m-Y H:i:s");
		
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		$this->db->distinct();
		$this->db->order_by("vehicle_id","desc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", "1032"); //balrich log
		$this->db->or_where("vehicle_user_id", "2397");	//balrich backup
		$this->db->or_where("vehicle_user_id", "2331");	//it balrich
		$this->db->or_where("vehicle_user_id", "2306");	//ast mgr
		$this->db->or_where("vehicle_user_id", "2307");	//mgr
		$this->db->or_where("vehicle_user_id", "3499");	//radja
		$this->db->or_where("vehicle_user_id", "3495");	//kiani
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
			$this->db = $this->load->database("datagpsold", TRUE);
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
						$gpses = $q->result_array();
						$q->free_result();
						foreach($gpses as $gps)
						{
							unset($gps['gps_id']);
							if(isset($gps['gps_sent']))
							{
								unset($gps['gps_sent']);
							}
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
						$gpses = $q->result_array();
						$q->free_result();
						foreach($gpses as $gps)
						{
							unset($gps['gps_info_id']);
							if(isset($gps['gps_info_sent']))
							{
								unset($gps['gps_info_sent']);
							}
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
		} 
			
		$finish_time = date("d-m-Y H:i:s");
		echo "Selesai...."." ".$finish_time;
		//finish foreach
		
		//Send Email
		
		$cron_name = "HISTORY DAILY";
		
		
		unset($mail);
		$mail['subject'] =  "BALRICH - ".$cron_name."";
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
End Data   : "."( ".$i." / ".$totalvehicle." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK");
			
	}
	
	function daily_asc($name="", $host="", $maxdata=5000)
	{
		$this->dodaily_asc($name, $host, $maxdata);
	}
	
	function dailyoffset_asc($offset)
	{
		$this->dodaily_asc("", "", 5000, $offset);
	}

	function dodaily_asc($name="", $host="", $maxdata=5000, $offset = 0)
	{
		$start_time = date("d-m-Y H:i:s");

		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		$this->db->distinct();
		$this->db->order_by("vehicle_id","asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id <>", "860");
		$this->db->where("vehicle_user_id <>", "1095");
		$this->db->where("vehicle_user_id <>", "1122");//dokar
		$this->db->where("vehicle_user_id <>", "1032");//balrich
		$this->db->where("vehicle_user_id <>", "1933");//ssi
		$this->db->where("vehicle_type <>", "T6");
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
			$this->db = $this->load->database("datagpsold", TRUE);
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
		
		
		//Send Email
		$cron_name = "Daily History ASC";
		
		unset($mail);
		$mail['subject'] =  "Report Cron - ".$cron_name."";
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
End Data   : "."( ".$i." / ".$totalvehicle." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "it-dept@lacak-mobil.com";
		$mail['bcc'] = "budiyanto@lacak-mobil.com";
		$mail['sender'] = "no-reply@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK");
		
	}
	
	//ssi
	function daily_newkct($name="", $host="", $maxdata=7000)
	{
		$this->dodaily_newkct($name, $host, $maxdata);
	}

	function dodaily_newkct($name="", $host="", $maxdata=7000, $offset = 0)
	{
		$start_time = date("d-m-Y H:i:s");
		
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		$this->db->distinct();
		$this->db->order_by("vehicle_id","desc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", "1643");//user kct
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
			$this->db = $this->load->database("datagpsold", TRUE);
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
				
						$historydb = $this->load->database("gpshistory", TRUE);
				
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
		} 
			
		$finish_time = date("d-m-Y H:i:s");
		echo "Selesai...."." ".$finish_time;
		//finish foreach
	}
	
	function daily_eid($port="", $order="asc", $maxdata=7500, $offset=0)
	{
		$this->db->distinct();
		$this->db->order_by("vehicle_id",$order);
		
		$this->db->where("vehicle_group", 1171);
		$this->db->where("vehicle_status <>", 3);
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_id']);
					if(isset($gps['gps_sent']))
					{
						unset($gps['gps_sent']);
					}
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
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_info_id']);
					if(isset($gps['gps_info_sent']))
					{
						unset($gps['gps_info_sent']);
					}
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
			
	}
	function daily_vehicle($vehicledevice = "", $vehicletype = "", $maxdata=7000, $offset=0)
	{
		$this->db->distinct();
		
		if (isset($vehicledevice)){
		
			$vehicle_device_new = $vehicledevice;
			$vehicle_type_new = $vehicletype;
			
			$this->db->where("vehicle_device", $vehicle_device_new."@".$vehicle_type_new);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->select("vehicle_type, vehicle_device, vehicle_info, vehicle_dbhistory_name");
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
			
			$this->db = $this->load->database("default", TRUE);
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
				
				$istbl_history = $this->config->item("dbhistory_default");
								if($this->config->item("is_dbhistory") == 1)
								{
									$istbl_history = $row->vehicle_dbhistory_name;
								}
								$historydb = $this->load->database($istbl_history, TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_id']);
					if(isset($gps['gps_sent']))
					{
						unset($gps['gps_sent']);
					}
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
				
				$istbl_history = $this->config->item("dbhistory_default");
								if($this->config->item("is_dbhistory") == 1)
								{
									$istbl_history = $row->vehicle_dbhistory_name;
								}
								$historydb = $this->load->database($istbl_history, TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_info_id']);
					if(isset($gps['gps_info_sent']))
					{
						unset($gps['gps_info_sent']);
					}
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
			
	}
	
	function movegt06_user($userid=0, $type="", $order="asc", $limit=0, $delete="yes")
	{
		ini_set('memory_limit', '2G');
		printf("=== MUlai \n");
		printf("Get Data TRACCAR \n");
		$this->db = $this->load->database("GPS_TRACCAR", TRUE);
		$this->concox = $this->load->database("GPS_GLOBAL", TRUE);
		$attributes = "";
		$device_id = "";
		
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
			$device_id = $devices[$j]->uniqueid."@".$devices[$j]->name;
			
			//select unique id berdasarkan user ID()
			//jika cocok maka data dipindahkan
			$this->dbwebtracking = $this->load->database("default", TRUE);
			$this->dbwebtracking->where("vehicle_device", $device_id);
			$this->dbwebtracking->where("vehicle_user_id", $userid);
			$qv = $this->dbwebtracking->get("vehicle");
			
			//jika ada 
			if ($qv->num_rows() > 0)
			{
				printf("Valid USERID : %s %s \n",$device_id, $userid);
				
				foreach(array("positions", "positions_a13","positions_gt06","positions_tk309","positions_tk315","positions_a14") as $postable)
				{
					$ignition = 0;
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
					printf("Total Data %s \n",count($data));
					printf("============= Start Proses Data");
					//print_r($data);exit;
					for($i=0;$i<count($data);$i++)
					{
						printf("Data %s of %s : %s \n",$i+1,count($data), $device_id);
						
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
					
						$val["gps_name"] = $devices[$j]->uniqueid;
						$val["gps_host"] = $devices[$j]->name;
						$val["gps_type"] = $devices[$j]->name;
						$val["gps_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
						$val["gps_status"] = "A";
						$val["gps_latitude"] =  number_format($data[$i]->latitude, 4, ".", "");
						//$val["gps_ns"] = "";
						$val["gps_longitude"] = number_format($data[$i]->longitude, 4, ".", "");
						//$val["gps_ew"] = 
						$val["gps_speed"] = $data[$i]->speed; 
						$val["gps_course"] = $data[$i]->course; 
						$val["gps_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
						//$val["gps_mvd"] =
						//$val["gps_mv"] =
						//$val["gps_cs"] =
						$val["gps_msg_ori"] = $data[$i]->attributes; 
						$val["gps_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
						$val["gps_latitude_real"] =	number_format($data[$i]->latitude, 4, ".", "");
						$val["gps_longitude_real"] = number_format($data[$i]->longitude, 4, ".", "");
						//$val["gps_odometer"] =	
						//$val["gps_workhour"] =				
						
						$valinfo["gps_info_device"] = $devices[$j]->uniqueid."@".$devices[$j]->name;
						$valinfo["gps_info_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
						$valinfo["gps_info_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
						$valinfo["gps_info_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
						
						if(isset($attributes['totalDistance']))
						{
							$valinfo["gps_info_distance"] = $attributes['totalDistance'];
						}
					
						printf("Proses Insert ");
					
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
						if($devices[$j]->group == "TK315")
						{
							$this->concox = $this->load->database("GPS_GLOBAL_TK315", TRUE);
						}
						if($devices[$j]->group == "A14")
						{
							$this->concox = $this->load->database("GPS_GLOBAL_A14", TRUE);
						}
						
						$this->concox->insert("gps",$val);
						$this->concox->insert("gps_info",$valinfo);
						printf("------- Insert DONE \n");
						
						if($delete == "yes")
						{
							printf("Proses Delete");
							$this->db->where("fixtime",$data[$i]->fixtime);
							$this->db->delete($postable);
							printf("------ DELETE DONE \n");
						}
						printf("____________ \n");
					}
					
					$this->db->where("latitude",0);
					$this->db->where("longitude",0);
					$this->db->delete($postable);
					printf("------ DELETE LAT LNG 0 DONE \n");
			
				}
			}
			
			//jika tidak ada
			else{
				printf("Bukan USERID yg dimaksud : %s \n",$device_id);
			}
			
			//end user
		}
		
		printf("=== FINISH \n");
	}


	function movegt06($type = "", $order="asc", $limit=0, $delete="yes")
	{
		printf("=== MUlai \n");
		printf("Get Data TRACCAR \n");
		$this->db = $this->load->database("GPS_TRACCAR", TRUE);
		$this->concox = $this->load->database("GPS_GLOBAL", TRUE);
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
			foreach(array("positions","positions_gt06","positions_tk309_3") as $postable)
			{
				$ignition = 0;
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
				printf("Total Data %s \n",count($data));
				printf("============= Start Proses Data");
				//print_r($data);exit;
				for($i=0;$i<count($data);$i++)
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
				
					$val["gps_name"] = $devices[$j]->uniqueid;
					$val["gps_host"] = $devices[$j]->name;
					$val["gps_type"] = $devices[$j]->name;
					$val["gps_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
					$val["gps_status"] = "A";
					$val["gps_latitude"] =  number_format($data[$i]->latitude, 4, ".", "");
					//$val["gps_ns"] = "";
					$val["gps_longitude"] = number_format($data[$i]->longitude, 4, ".", "");
					//$val["gps_ew"] = 
					$val["gps_speed"] = $data[$i]->speed; 
					$val["gps_course"] = $data[$i]->course; 
					$val["gps_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
					//$val["gps_mvd"] =
					//$val["gps_mv"] =
					//$val["gps_cs"] =
					$val["gps_msg_ori"] = $data[$i]->attributes; 
					$val["gps_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
					$val["gps_latitude_real"] =	number_format($data[$i]->latitude, 4, ".", "");
					$val["gps_longitude_real"] = number_format($data[$i]->longitude, 4, ".", "");
					//$val["gps_odometer"] =	
					//$val["gps_workhour"] =				
					
					$valinfo["gps_info_device"] = $devices[$j]->uniqueid."@".$devices[$j]->name;
					$valinfo["gps_info_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
					$valinfo["gps_info_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
					$valinfo["gps_info_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
					
					if(isset($attributes['totalDistance']))
					{
						$valinfo["gps_info_distance"] = $attributes['totalDistance'];
					}
				
					printf("Proses Insert ");
				
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
					
					$this->concox->insert("gps",$val);
					$this->concox->insert("gps_info",$valinfo);
					printf("------- Insert DONE \n");
					
					if($delete == "yes")
					{
						printf("Proses Delete");
						$this->db->where("fixtime",$data[$i]->fixtime);
						$this->db->delete($postable);
						printf("------ DELETE DONE \n");
					}
					printf("____________ \n");
				}
				
				$this->db->where("latitude",0);
				$this->db->where("longitude",0);
				$this->db->delete($postable);
				printf("------ DELETE LAT LNG 0 DONE \n");
		
			}
		}
		
		printf("=== FINISH \n");
	}
	
	function movegt06_perdevice($device = "", $order="asc", $limit=0, $delete="yes")
	{
		printf("=== MUlai \n");
		printf("Get Data TRACCAR \n");
		$this->db = $this->load->database("GPS_TRACCAR", TRUE);
		$this->concox = $this->load->database("GPS_GLOBAL", TRUE);
		$attributes = "";
		
		//Get Device GT06 Dulu
		$this->db->order_by("id",$order);
		$this->db->select("id,name,uniqueid,group");
		if($device != "")
		{
			$this->db->where("uniqueid",$device);
		}
		$q = $this->db->get("devices");
		$devices = $q->result();
		for($j=0;$j<count($devices);$j++)
		{
			foreach(array("positions", "positions_a13","positions_gt06","positions_tk309") as $postable)
			{
				$ignition = 0;
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
				printf("Total Data %s \n",count($data));
				printf("============= Start Proses Data");
				//print_r($data);exit;
				for($i=0;$i<count($data);$i++)
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
				
					$val["gps_name"] = $devices[$j]->uniqueid;
					$val["gps_host"] = $devices[$j]->name;
					$val["gps_type"] = $devices[$j]->name;
					$val["gps_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
					$val["gps_status"] = "A";
					$val["gps_latitude"] =  number_format($data[$i]->latitude, 4, ".", "");
					//$val["gps_ns"] = "";
					$val["gps_longitude"] = number_format($data[$i]->longitude, 4, ".", "");
					//$val["gps_ew"] = 
					$val["gps_speed"] = $data[$i]->speed; 
					$val["gps_course"] = $data[$i]->course; 
					$val["gps_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
					//$val["gps_mvd"] =
					//$val["gps_mv"] =
					//$val["gps_cs"] =
					$val["gps_msg_ori"] = $data[$i]->attributes; 
					$val["gps_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
					$val["gps_latitude_real"] =	number_format($data[$i]->latitude, 4, ".", "");
					$val["gps_longitude_real"] = number_format($data[$i]->longitude, 4, ".", "");
					//$val["gps_odometer"] =	
					//$val["gps_workhour"] =				
					
					$valinfo["gps_info_device"] = $devices[$j]->uniqueid."@".$devices[$j]->name;
					$valinfo["gps_info_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
					$valinfo["gps_info_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
					$valinfo["gps_info_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
					
					if(isset($attributes['totalDistance']))
					{
						$valinfo["gps_info_distance"] = $attributes['totalDistance'];
					}
				
					printf("Proses Insert ");
				
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
					
					$this->concox->insert("gps",$val);
					$this->concox->insert("gps_info",$valinfo);
					printf("------- Insert DONE \n");
					
					if($delete == "yes")
					{
						printf("Proses Delete");
						$this->db->where("fixtime",$data[$i]->fixtime);
						$this->db->delete($postable);
						printf("------ DELETE DONE \n");
					}
					printf("____________ \n");
				}
				
				$this->db->where("latitude",0);
				$this->db->where("longitude",0);
				$this->db->delete($postable);
				printf("------ DELETE LAT LNG 0 DONE \n");
		
			}
		}
		
		printf("=== FINISH \n");
	}
	
	function movegt06_table($targettable = "positions", $targetmove="GPS_CONCOX_GT06")
	{
		printf("=== MUlai \n");
		printf("Get Data TRACCAR \n");
		$this->db = $this->load->database("GPS_TRACCAR", TRUE);
		//$this->concox = $this->load->database("GPS_GLOBAL", TRUE);
		$this->concox = $this->load->database($targetmove, TRUE);
		$attributes = "";
		
		$this->db->select("id,name,uniqueid");
		$q = $this->db->get("devices");
		$devices = $q->result();
		for($j=0;$j<count($devices);$j++)
		{
			$ignition = 0;
			printf("============= Start Device %s \n",$devices[$j]->uniqueid);
			$this->db->group_by("devicetime");
			$this->db->order_by("devicetime","asc");
			$this->db->where("deviceid",$devices[$j]->id);
			$this->db->limit(1000);
			$q = $this->db->get($targettable);
			$data = $q->result();
			printf("Total Data %s \n",count($data));
			printf("============= Start Proses Data");
			//print_r($data);exit;
			for($i=0;$i<count($data);$i++)
			{
				printf("Data %s \n",$i+1);
				
				unset($val);
				unset($valinfo);
				
				$attributes = json_decode($data[$i]->attributes, true);
				//printf("Attributes %s \n",$attributes);
				
				if(isset($attributes['ignition']))
				{
					if($attributes['ignition'] == false) { $ignition = false; } else { $ignition = true; }
				}
				
				if(isset($attributes['totalDistance']))
				{
					$totaldistance = ($attributes['totalDistance'])/1000;
					$totaldistance = (int)$totaldistance;
				}
				
				if($ignition == 1) 
				{ 
					$valinfo["gps_info_io_port"] = "0000100000"; 
				}
				else 
				{ 
					$valinfo["gps_info_io_port"] = "0000000000"; 	
				}
				
				$val["gps_name"] = $devices[$j]->uniqueid;
				$val["gps_host"] = $devices[$j]->name;
				$val["gps_type"] = $devices[$j]->name;
				$val["gps_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
				$val["gps_status"] = "A";
				$val["gps_latitude"] =  number_format($data[$i]->latitude, 4, ".", "");
				//$val["gps_latitude"] = $data[$i]->latitude;
				//$val["gps_ns"] = "";
				//$val["gps_longitude"] = $data[$i]->longitude;
				$val["gps_longitude"] = number_format($data[$i]->longitude, 4, ".", "");
				//$val["gps_ew"] = 
				$val["gps_speed"] = $data[$i]->speed; 
				$val["gps_course"] = $data[$i]->course; 
				$val["gps_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
				//$val["gps_mvd"] =
				//$val["gps_mv"] =
				//$val["gps_cs"] =
				$val["gps_msg_ori"] = $data[$i]->attributes; 
				/*
				$dt = date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
				if($dt > date("Y-m-d H:i:s"))
				{
					print_r(date("Y-m-d H:i:s"));exit;
					$dt = date("Y-m-d H:i:s",strtotime("-7", strtotime($data[$i]->devicetime)));
				}
				*/
				
				$val["gps_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
				//$val["gps_latitude_real"] =	$data[$i]->latitude;
				//$val["gps_longitude_real"] = $data[$i]->longitude;
				$val["gps_latitude_real"] =	number_format($data[$i]->latitude, 4, ".", "");
				$val["gps_longitude_real"] = number_format($data[$i]->longitude, 4, ".", "");
				//$val["gps_odometer"] =	
				//$val["gps_workhour"] =				
				
				$valinfo["gps_info_device"] = $devices[$j]->uniqueid."@".$devices[$j]->name;
				$valinfo["gps_info_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
				$valinfo["gps_info_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
				$valinfo["gps_info_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
				if(isset($totaldistance))
				{
					$valinfo["gps_info_distance"] = $totaldistance;
				}
				
				if ($data[$i]->latitude != 0)
				{
					printf("Proses Insert ");
					$this->concox->insert("gps",$val);
					$this->concox->insert("gps_info",$valinfo);
					printf("------- Insert DONE \n");
				}
				else
				{
					printf("Latitude = 0 \n");
				}
				
				printf("Proses Delete");
				
				$this->db->where("fixtime",$data[$i]->fixtime);
				$this->db->delete($targettable);
				printf("------ DELETE DONE \n");
					
				/*if(isset($data[$i+1]->fixtime))
				{
					$this->db->where("fixtime",$data[$i]->fixtime);
					$this->db->delete($targettable);
					printf("------ DELETE DONE \n");
					printf("____________ \n");
				}*/
			}
		}
		
		printf("=== FINISH \n");
		
	}
	
	function movegt06_table_user($userid=0, $targettable = "positions", $targetmove="GPS_CONCOX_GT06")
	{
		printf("=== MUlai \n");
		printf("Get Data TRACCAR \n");
		$this->db = $this->load->database("GPS_TRACCAR", TRUE);
		$this->concox = $this->load->database($targetmove, TRUE);
		$attributes = "";
		
		$this->db->select("id,name,uniqueid");
		$q = $this->db->get("devices");
		$devices = $q->result();
		for($j=0;$j<count($devices);$j++)
		{
			$device_id = $devices[$j]->uniqueid."@".$devices[$j]->name;
			
			//select unique id berdasarkan user ID()
			//jika cocok maka data dipindahkan
			$this->dbwebtracking = $this->load->database("default", TRUE);
			$this->dbwebtracking->where("vehicle_device", $device_id);
			$this->dbwebtracking->where("vehicle_user_id", $userid);
			$qv = $this->dbwebtracking->get("vehicle");
			
			//jika ada 
			if ($qv->num_rows() > 0)
			{
				printf("Valid USERID : %s %s \n",$device_id, $userid);
				$ignition = 0;
				printf("============= Start Device %s \n",$devices[$j]->uniqueid);
				$this->db->group_by("devicetime");
				$this->db->order_by("devicetime","asc");
				$this->db->where("deviceid",$devices[$j]->id);
				$this->db->limit(1000);
				$q = $this->db->get($targettable);
				$data = $q->result();
				printf("Total Data %s \n",count($data));
				printf("============= Start Proses Data");
				
				for($i=0;$i<count($data);$i++)
				{
					printf("Data %s \n",$i+1);
					
					unset($val);
					unset($valinfo);
					
					$attributes = json_decode($data[$i]->attributes, true);
					//printf("Attributes %s \n",$attributes);
					
					if(isset($attributes['ignition']))
					{
						if($attributes['ignition'] == false) { $ignition = false; } else { $ignition = true; }
					}
					
					if(isset($attributes['totalDistance']))
					{
						$totaldistance = ($attributes['totalDistance'])/1000;
						$totaldistance = (int)$totaldistance;
					}
					
					if($ignition == 1) 
					{ 
						$valinfo["gps_info_io_port"] = "0000100000"; 
					}
					else 
					{ 
						$valinfo["gps_info_io_port"] = "0000000000"; 	
					}
					
					$val["gps_name"] = $devices[$j]->uniqueid;
					$val["gps_host"] = $devices[$j]->name;
					$val["gps_type"] = $devices[$j]->name;
					$val["gps_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
					$val["gps_status"] = "A";
					$val["gps_latitude"] =  number_format($data[$i]->latitude, 4, ".", "");
					//$val["gps_latitude"] = $data[$i]->latitude;
					//$val["gps_ns"] = "";
					//$val["gps_longitude"] = $data[$i]->longitude;
					$val["gps_longitude"] = number_format($data[$i]->longitude, 4, ".", "");
					//$val["gps_ew"] = 
					$val["gps_speed"] = $data[$i]->speed; 
					$val["gps_course"] = $data[$i]->course; 
					$val["gps_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
					//$val["gps_mvd"] =
					//$val["gps_mv"] =
					//$val["gps_cs"] =
					$val["gps_msg_ori"] = $data[$i]->attributes; 
					/*
					$dt = date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
					if($dt > date("Y-m-d H:i:s"))
					{
						print_r(date("Y-m-d H:i:s"));exit;
						$dt = date("Y-m-d H:i:s",strtotime("-7", strtotime($data[$i]->devicetime)));
					}
					*/
					
					$val["gps_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
					//$val["gps_latitude_real"] =	$data[$i]->latitude;
					//$val["gps_longitude_real"] = $data[$i]->longitude;
					$val["gps_latitude_real"] =	number_format($data[$i]->latitude, 4, ".", "");
					$val["gps_longitude_real"] = number_format($data[$i]->longitude, 4, ".", "");
					//$val["gps_odometer"] =	
					//$val["gps_workhour"] =				
					
					$valinfo["gps_info_device"] = $devices[$j]->uniqueid."@".$devices[$j]->name;
					$valinfo["gps_info_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
					$valinfo["gps_info_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
					$valinfo["gps_info_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
					if(isset($totaldistance))
					{
						$valinfo["gps_info_distance"] = $totaldistance;
					}
					
					if ($data[$i]->latitude != 0)
					{
						printf("Proses Insert ");
						$this->concox->insert("gps",$val);
						$this->concox->insert("gps_info",$valinfo);
						printf("------- Insert DONE \n");
					}
					else
					{
						printf("Latitude = 0 \n");
					}
					
					printf("Proses Delete");
					
					$this->db->where("fixtime",$data[$i]->fixtime);
					$this->db->delete($targettable);
					printf("------ DELETE DONE \n");
						
					/*if(isset($data[$i+1]->fixtime))
					{
						$this->db->where("fixtime",$data[$i]->fixtime);
						$this->db->delete($targettable);
						printf("------ DELETE DONE \n");
						printf("____________ \n");
					}*/
				}
				
			}
			
			
			
		}
		
		printf("=== FINISH \n");
		
	}
	
	function move_multi($groupname="",$userid="",$targettable="",$targetmove="")
	{
		printf("=== Mulai \n");
		printf("Get Multi Port TRACCAR \n");
		$startdate = date("Y-m-d H:i:s");
		
		//select list port
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("port_id","asc");
		$this->db->where("port_status",1);
		$this->db->where("port_group",$groupname);
		$qport = $this->db->get("cron_port_other");
		if ($qport->num_rows() == 0) return;

		$rowsport = $qport->result();
		$totalport = count($rowsport);
		$p = 0;
		$offset = 0;
		
		// looping start here //
		foreach($rowsport as $rowport)
		{
			if (($p+1) < $offset)
			{
				$p++;
				continue;
			}
			printf("TRACCAR Port : %s (%d/%d)\n", $rowport->port_value, ++$p, $totalport);
			$port = $rowport->port_value;
			$targetmove = $rowport->port_targetmove;
			$targettable = $rowport->port_targettable;
			$userid = $rowport->port_user;
			
				//---- ----//
				$this->db = $this->load->database("GPS_TRACCAR", TRUE);
				$this->concox = $this->load->database($targetmove, TRUE);
				$attributes = "";
				
				$this->db->select("id,name,uniqueid");
				$this->db->order_by("id","desc");
				$this->db->where("port", $port);
				$q = $this->db->get("devices");
				$devices = $q->result();
				
				$totaldevices = count($devices);//print_r($totaldevices);exit();
				for($j=0;$j<count($devices);$j++)
				{
					$device_id = $devices[$j]->uniqueid."@".$devices[$j]->name;
					
					//select unique id berdasarkan user ID()
					//jika cocok maka data dipindahkan
					$this->dbwebtracking = $this->load->database("default", TRUE);
					$this->dbwebtracking->where("vehicle_device", $device_id);
					if($userid != ""){
						$this->dbwebtracking->where("vehicle_user_id", $userid);
					}
					$qv = $this->dbwebtracking->get("vehicle");
					
					//jika ada 
					if ($qv->num_rows() > 0)
					{
						printf("Valid USERID Target & Move : %s %s %s %s %s of %s \n",$device_id,$userid,$targettable,$targetmove,$j+1,$totaldevices);
						$ignition = 0;
						printf("============= Start Device %s \n",$devices[$j]->uniqueid);
						$this->db->group_by("devicetime");
						$this->db->order_by("devicetime","asc");
						$this->db->where("deviceid",$devices[$j]->id);
						$this->db->limit(1000);
						$q = $this->db->get($targettable);
						$data = $q->result();
						$totaldata = count($data);
						printf("Total Data %s \n",count($data));
						printf("============= Start Proses Data \n");
						
						for($i=0;$i<count($data);$i++)
						{
							printf("Data -- %s of %s %s -- %s %s %s %s \n",$i+1,$totaldata,$groupname,$userid,$device_id,$targettable,$targetmove);
							
							unset($val);
							unset($valinfo);
							
							$attributes = json_decode($data[$i]->attributes, true);
							//printf("Attributes %s \n",$attributes);
							
							if(isset($attributes['ignition']))
							{
								if($attributes['ignition'] == false) { $ignition = false; } else { $ignition = true; }
							}
							
							if(isset($attributes['totalDistance']))
							{
								$totaldistance = ($attributes['totalDistance'])/1000;
								$totaldistance = (int)$totaldistance;
							}
							
							if($ignition == 1) 
							{ 
								$valinfo["gps_info_io_port"] = "0000100000"; 
							}
							else 
							{ 
								$valinfo["gps_info_io_port"] = "0000000000"; 	
							}
							
							$val["gps_name"] = $devices[$j]->uniqueid;
							$val["gps_host"] = $devices[$j]->name;
							$val["gps_type"] = $devices[$j]->name;
							$val["gps_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
							$val["gps_status"] = "A";
							$val["gps_latitude"] =  number_format($data[$i]->latitude, 4, ".", "");
							//$val["gps_latitude"] = $data[$i]->latitude;
							//$val["gps_ns"] = "";
							//$val["gps_longitude"] = $data[$i]->longitude;
							$val["gps_longitude"] = number_format($data[$i]->longitude, 4, ".", "");
							//$val["gps_ew"] = 
							$val["gps_speed"] = $data[$i]->speed; 
							$val["gps_course"] = $data[$i]->course; 
							$val["gps_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
							//$val["gps_mvd"] =
							//$val["gps_mv"] =
							//$val["gps_cs"] =
							$val["gps_msg_ori"] = $data[$i]->attributes; 
							/*
							$dt = date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
							if($dt > date("Y-m-d H:i:s"))
							{
								print_r(date("Y-m-d H:i:s"));exit;
								$dt = date("Y-m-d H:i:s",strtotime("-7", strtotime($data[$i]->devicetime)));
							}
							*/
							
							$val["gps_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
							//$val["gps_latitude_real"] =	$data[$i]->latitude;
							//$val["gps_longitude_real"] = $data[$i]->longitude;
							$val["gps_latitude_real"] =	number_format($data[$i]->latitude, 4, ".", "");
							$val["gps_longitude_real"] = number_format($data[$i]->longitude, 4, ".", "");
							//$val["gps_odometer"] =	
							//$val["gps_workhour"] =				
							
							$valinfo["gps_info_device"] = $devices[$j]->uniqueid."@".$devices[$j]->name;
							$valinfo["gps_info_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
							$valinfo["gps_info_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
							$valinfo["gps_info_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
							if(isset($totaldistance))
							{
								$valinfo["gps_info_distance"] = $totaldistance;
							}
							
							if ($data[$i]->latitude != 0)
							{
								printf("Proses Insert ");
								$this->concox->insert("gps",$val);
								$this->concox->insert("gps_info",$valinfo);
								printf("------- Insert DONE \n");
							}
							else
							{
								printf("Latitude = 0 \n");
							}
							
							printf("Proses Delete");
							
							$this->db->where("fixtime",$data[$i]->fixtime);
							$this->db->delete($targettable);
							printf("------ DELETE DONE \n");
								
							/*if(isset($data[$i+1]->fixtime))
							{
								$this->db->where("fixtime",$data[$i]->fixtime);
								$this->db->delete($targettable);
								printf("------ DELETE DONE \n");
								printf("____________ \n");
							}*/
						}
						
					}
					
				}
		}
		
		$enddate = date("Y-m-d H:i:s");
		printf("=== FINISH %s , %s \n", $startdate,$enddate);
	}
	
	function movedb_multi($groupname="",$userid="",$targettable="",$targetmove="")
	{
		printf("=== Mulai \n");
		printf("Get Multi Port TRACCAR \n");
		$startdate = date("Y-m-d H:i:s");
		
		//select list port
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("port_id","asc");
		$this->db->where("port_status",1);
		$this->db->where("port_group",$groupname);
		$qport = $this->db->get("cron_port_other");
		if ($qport->num_rows() == 0) return;

		$rowsport = $qport->result();
		$totalport = count($rowsport);
		$p = 0;
		$offset = 0;
		
		// looping start here //
		foreach($rowsport as $rowport)
		{
			if (($p+1) < $offset)
			{
				$p++;
				continue;
			}
			printf("TRACCAR Port : %s (%d/%d)\n", $rowport->port_value, ++$p, $totalport);
			$port = $rowport->port_value;
			$targetmove = $rowport->port_targetmove;
			$targettable = $rowport->port_targettable;
			$userid = $rowport->port_user;
			$configdb = $rowport->port_configdb;
			
				//---- ----//
				$this->db = $this->load->database($configdb, TRUE);
				$this->concox = $this->load->database($targetmove, TRUE);
				$attributes = "";
				
				$this->db->group_by("deviceid");
				$this->db->select("deviceid");
				$q = $this->db->get($targettable);
				$devices = $q->result();
				
				$totaldevices = count($devices);//print_r($totaldevices);exit();
				for($j=0;$j<count($devices);$j++)
				{
					//cek table devices
					$this->db->select("id,name,uniqueid");
					$this->db->where("id",$devices[$j]->deviceid);
					$qdevices = $this->db->get("devices");
					$master_devices = $qdevices->row();
					
					
					if(count($master_devices)>0){
						$device_id = $master_devices->uniqueid."@".$master_devices->name;
					
						//select unique id berdasarkan user ID()
						//jika cocok maka data dipindahkan
						$this->dbwebtracking = $this->load->database("default", TRUE);
						$this->dbwebtracking->where("vehicle_device", $device_id);
						if($userid != ""){
							$this->dbwebtracking->where("vehicle_user_id", $userid);
						}
						$qv = $this->dbwebtracking->get("vehicle");
						
						//jika ada 
						if ($qv->num_rows() > 0)
						{
							printf("Valid USERID Target & Move : %s %s %s %s %s of %s \n",$device_id,$userid,$targettable,$targetmove,$j+1,$totaldevices);
							$ignition = 0;
							printf("============= Start Device %s \n",$master_devices->uniqueid);
							$this->db->group_by("devicetime");
							$this->db->order_by("devicetime","asc");
							$this->db->where("deviceid",$master_devices->id);
							$this->db->limit(1000);
							$q = $this->db->get($targettable);
							$data = $q->result();
							$totaldata = count($data);
							printf("Total Data %s \n",count($data));
							printf("============= Start Proses Data \n");
							//print_r($data);exit();
							for($i=0;$i<count($data);$i++)
							{
								printf("Data -- %s of %s %s -- %s %s %s %s \n",$i+1,$totaldata,$groupname,$userid,$device_id,$targettable,$targetmove);
								
								unset($val);
								unset($valinfo);
								
								$attributes = json_decode($data[$i]->attributes, true);
								//printf("Attributes %s \n",$attributes);
								
								if(isset($attributes['ignition']))
								{
									if($attributes['ignition'] == false) { $ignition = false; } else { $ignition = true; }
								}
								
								if(isset($attributes['totalDistance']))
								{
									$totaldistance = ($attributes['totalDistance'])/1000;
									$totaldistance = (int)$totaldistance;
								}
								
								if($ignition == 1) 
								{ 
									$valinfo["gps_info_io_port"] = "0000100000"; 
								}
								else 
								{ 
									$valinfo["gps_info_io_port"] = "0000000000"; 	
								}
								
								$val["gps_name"] = $master_devices->uniqueid;
								$val["gps_host"] = $master_devices->name;
								$val["gps_type"] = $master_devices->name;
								$val["gps_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
								$val["gps_status"] = "A";
								$val["gps_latitude"] =  number_format($data[$i]->latitude, 4, ".", "");
								//$val["gps_latitude"] = $data[$i]->latitude;
								//$val["gps_ns"] = "";
								//$val["gps_longitude"] = $data[$i]->longitude;
								$val["gps_longitude"] = number_format($data[$i]->longitude, 4, ".", "");
								//$val["gps_ew"] = 
								$val["gps_speed"] = $data[$i]->speed; 
								$val["gps_course"] = $data[$i]->course; 
								$val["gps_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
								//$val["gps_mvd"] =
								//$val["gps_mv"] =
								//$val["gps_cs"] =
								$val["gps_msg_ori"] = $data[$i]->attributes; 
								/*
								$dt = date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
								if($dt > date("Y-m-d H:i:s"))
								{
									print_r(date("Y-m-d H:i:s"));exit;
									$dt = date("Y-m-d H:i:s",strtotime("-7", strtotime($data[$i]->devicetime)));
								}
								*/
								
								$val["gps_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
								//$val["gps_latitude_real"] =	$data[$i]->latitude;
								//$val["gps_longitude_real"] = $data[$i]->longitude;
								$val["gps_latitude_real"] =	number_format($data[$i]->latitude, 4, ".", "");
								$val["gps_longitude_real"] = number_format($data[$i]->longitude, 4, ".", "");
								//$val["gps_odometer"] =	
								//$val["gps_workhour"] =				
								
								$valinfo["gps_info_device"] = $master_devices->uniqueid."@".$master_devices->name;
								$valinfo["gps_info_utc_coord"] = date("His",strtotime($data[$i]->devicetime));
								$valinfo["gps_info_utc_date"] = date("dmy",strtotime($data[$i]->devicetime));
								$valinfo["gps_info_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->devicetime));
								if(isset($totaldistance))
								{
									$valinfo["gps_info_distance"] = $totaldistance;
								}
								
								if ($data[$i]->latitude != 0)
								{
									printf("Proses Insert ");
									$this->concox->insert("gps",$val);
									$this->concox->insert("gps_info",$valinfo);
									printf("------- Insert DONE \n");
								}
								else
								{
									printf("Latitude = 0 \n");
								}
								
								printf("Proses Delete");
								
								$this->db->where("fixtime",$data[$i]->fixtime);
								$this->db->delete($targettable);
								printf("------ DELETE DONE \n");
									
								/*if(isset($data[$i+1]->fixtime))
								{
									$this->db->where("fixtime",$data[$i]->fixtime);
									$this->db->delete($targettable);
									printf("------ DELETE DONE \n");
									printf("____________ \n");
								}*/
							}
							
						}
					}
					
					
				}
		}
		
		$enddate = date("Y-m-d H:i:s");
		printf("=== FINISH %s , %s \n", $startdate,$enddate);
	}
	
	function movedb_multi_2($groupname="",$userid="",$targettable="",$targetmove="")
	{
		printf("=== Mulai \n");
		printf("Get Multi Port TK315 PHP \n");
		$startdate = date("Y-m-d H:i:s");
		
		//select list port
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("port_id","asc");
		$this->db->where("port_status",1);
		$this->db->where("port_group",$groupname);
		$qport = $this->db->get("cron_port_other");
		if ($qport->num_rows() == 0) return;

		$rowsport = $qport->result();
		$totalport = count($rowsport);
		$p = 0;
		$offset = 0;
		
		// looping start here //
		foreach($rowsport as $rowport)
		{
			if (($p+1) < $offset)
			{
				$p++;
				continue;
			}
			printf("TK Port : %s (%d/%d)\n", $rowport->port_value, ++$p, $totalport);
			$port = $rowport->port_value;
			$targettable = $rowport->port_targettable; //table from
			$targetmove = $rowport->port_targetmove; //table end
			$userid = $rowport->port_user;
			
				//---- ----//
				$this->db = $this->load->database($targettable, TRUE);
				$this->concox = $this->load->database($targetmove, TRUE);
				$attributes = "";
				
				$this->db->group_by("imei");
				$this->db->select("imei");
				$q = $this->db->get("gprmc");
				$devices = $q->result();
				
				$totaldevices = count($devices);
				for($j=0;$j<count($devices);$j++)
				{
					
					if(count($devices)>0){
						//select unique id berdasarkan user ID()
						//jika cocok maka data dipindahkan
						$this->dbwebtracking = $this->load->database("default", TRUE);
						$this->dbwebtracking->select("vehicle_id,vehicle_device");
						$this->dbwebtracking->where("vehicle_device LIKE '%".$devices[$j]->imei."%'",null);
						if($userid != ""){
							$this->dbwebtracking->where("vehicle_user_id", $userid);
						}
						$qv = $this->dbwebtracking->get("vehicle");
						
						//jika ada 
						if ($qv->num_rows() > 0)
						{
							$master_devices = $qv->row();
							$devices_ex = explode("@", $master_devices->vehicle_device);
							$device_id = $master_devices->vehicle_device;
							$device_host = $devices_ex[1];
							
							printf("Valid USERID Target & Move : %s %s %s %s %s of %s \n",$device_id,$userid,$targettable,$targetmove,$j+1,$totaldevices);
							
							$ignition = 0;
							printf("============= Start Device %s \n",$devices[$j]->imei);
							$this->db->group_by("date");
							$this->db->order_by("date","asc");
							$this->db->where("imei",$devices[$j]->imei);
							$this->db->limit(1000);
							$q = $this->db->get("gprmc");
							$data = $q->result();
							$totaldata = count($data);
							printf("Total Data %s \n",count($data));
							printf("============= Start Proses Data \n");
							
							for($i=0;$i<count($data);$i++)
							{
								printf("Data -- %s of %s %s -- %s %s %s %s \n",$i+1,$totaldata,$groupname,$userid,$device_id,$targettable,$targetmove);
								
								unset($val);
								unset($valinfo);
								$odometro = 0;
								$val["gps_name"] = $devices[$j]->imei;
								$val["gps_host"] = $device_host;
								$val["gps_type"] = $device_host;
								$val["gps_utc_coord"] = date("His",strtotime($data[$i]->date));
								$val["gps_status"] = $data[$i]->satelliteFixStatus;
								$val["gps_latitude"] = $data[$i]->latitudeDecimalDegrees;
								$val["gps_ns"] = $data[$i]->latitudeHemisphere;
								$val["gps_longitude"] = $data[$i]->longitudeDecimalDegrees;
								$val["gps_ew"] = $data[$i]->longitudeHemisphere;
								$val["gps_speed"] = $data[$i]->speed/1.852; 
								$val["gps_course"] = $data[$i]->course; 
								$val["gps_utc_date"] = date("dmy",strtotime($data[$i]->date));
								$val["gps_msg_ori"] = $data[$i]->msg_ori;
								
								$odometro = $data[$i]->km_rodado;
								
								//speed & odometer cek dari loc_actual
								if($data[$i]->msg_ori != ""){
									$json = json_decode($data[$i]->msg_ori,true);
									if($json[3] == "13"){
										$this->db->order_by("id","desc");
										$this->db->where("imei",$data[$i]->imei);
										$this->db->limit(1);
										$q_loc = $this->db->get("loc_atual");
										if ($q_loc->num_rows() > 0)
										{
											$dlive_loc = $q_loc->row();
											$val["gps_course"] = $dlive_loc->course;
											$val["gps_speed"] =  $dlive_loc->speed/1.852;
											$odometro = $dlive_loc->odometro;											
										}
										else
										{
											$val["gps_course"] = $data[$i]->course;
											$val["gps_speed"] = $data[$i]->speed/1.852; 
											$odometro = $data[$i]->km_rodado;
										}
									}
									
								}
								
								$val["gps_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->date));
								$val["gps_latitude_real"] =	$data[$i]->latitudeDecimalDegrees;
								$val["gps_longitude_real"] = $data[$i]->longitudeDecimalDegrees;
								$val["gps_odometer"] = $odometro;
											
								
								$valinfo["gps_info_device"] = $devices[$j]->imei."@".$device_host;
								$valinfo["gps_info_utc_coord"] = date("His",strtotime($data[$i]->date));
								$valinfo["gps_info_utc_date"] = date("dmy",strtotime($data[$i]->date));
								$valinfo["gps_info_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->date));
								
								//if ligado S = Engine ON else OFF
								$ignition = $data[$i]->ligado;
								if($ignition == "S") 
								{ 
									$valinfo["gps_info_io_port"] = "0000100000"; 
								}
								else 
								{ 
									$valinfo["gps_info_io_port"] = "0000000000"; 	
								}
								
								$valinfo["gps_info_distance"] = $odometro;
								
								
								if ($data[$i]->latitudeDecimalDegrees != 0)
								{
									printf("Proses Insert ");
									$this->concox->insert("gps",$val);
									$this->concox->insert("gps_info",$valinfo);
									printf("------- Insert DONE \n");
								}
								else
								{
									printf("Latitude = 0 \n");
								}
								
								printf("Proses Delete");
								
								$this->db->where("imei",$data[$i]->imei);
								$this->db->where("date",$data[$i]->date);
								$this->db->delete("gprmc");
								printf("------ DELETE DONE \n");
								
							}
							
						}
					}
					
					
				}
		}
		
		$enddate = date("Y-m-d H:i:s");
		printf("=== FINISH %s , %s \n", $startdate,$enddate);
	}
	
	function movedb_multi_3($groupname="",$userid="",$targettable="",$targetmove="")
	{
		printf("=== Mulai \n");
		printf("Get Multi Port TK315 PHP \n");
		$startdate = date("Y-m-d H:i:s");
		
		//select list port
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("port_id","asc");
		$this->db->where("port_status",1);
		$this->db->where("port_group",$groupname);
		$qport = $this->db->get("cron_port_other");
		if ($qport->num_rows() == 0) return;

		$rowsport = $qport->result();
		$totalport = count($rowsport);
		$p = 0;
		$offset = 0;
		
		// looping start here //
		foreach($rowsport as $rowport)
		{
			if (($p+1) < $offset)
			{
				$p++;
				continue;
			}
			printf("TK Port : %s (%d/%d)\n", $rowport->port_value, ++$p, $totalport);
			$port = $rowport->port_value;
			$targettable = $rowport->port_targettable; //table from
			$targetmove = $rowport->port_targetmove; //table end
			$userid = $rowport->port_user;
			
				//---- ----//
				$this->db = $this->load->database($targettable, TRUE);
				$this->concox = $this->load->database($targetmove, TRUE);
				$attributes = "";
				
				$this->db->group_by("imei");
				$this->db->select("imei");
				$q = $this->db->get("gprmc");
				$devices = $q->result();
				
				$totaldevices = count($devices);
				for($j=0;$j<count($devices);$j++)
				{
					
					if(count($devices)>0){
						//select unique id berdasarkan user ID()
						//jika cocok maka data dipindahkan
						$this->dbwebtracking = $this->load->database("default", TRUE);
						$this->dbwebtracking->select("vehicle_id,vehicle_device");
						$this->dbwebtracking->where("vehicle_device LIKE '%".$devices[$j]->imei."%'",null);
						if($userid != ""){
							$this->dbwebtracking->where("vehicle_user_id", $userid);
						}
						$qv = $this->dbwebtracking->get("vehicle");
						
						//jika ada 
						if ($qv->num_rows() > 0)
						{
							$master_devices = $qv->row();
							$devices_ex = explode("@", $master_devices->vehicle_device);
							$device_id = $master_devices->vehicle_device;
							$device_host = $devices_ex[1];
							
							printf("Valid USERID Target & Move : %s %s %s %s %s of %s \n",$device_id,$userid,$targettable,$targetmove,$j+1,$totaldevices);
							
							$ignition = 0;
							printf("============= Start Device %s \n",$devices[$j]->imei);
							$this->db->group_by("date");
							$this->db->order_by("date","asc");
							$this->db->where("imei",$devices[$j]->imei);
							$this->db->limit(1000);
							$q = $this->db->get("gprmc");
							$data = $q->result();
							$totaldata = count($data);
							printf("Total Data %s \n",count($data));
							printf("============= Start Proses Data \n");
							
							for($i=0;$i<count($data);$i++)
							{
								printf("Data -- %s of %s %s -- %s %s %s %s \n",$i+1,$totaldata,$groupname,$userid,$device_id,$targettable,$targetmove);
								
								unset($val);
								unset($valinfo);
								$odometro = 0;
								$val["gps_name"] = $devices[$j]->imei;
								$val["gps_host"] = $device_host;
								$val["gps_type"] = $device_host;
								$val["gps_utc_coord"] = date("His",strtotime($data[$i]->date));
								$val["gps_status"] = $data[$i]->satelliteFixStatus;
								$val["gps_latitude"] = $data[$i]->latitudeDecimalDegrees;
								$val["gps_ns"] = $data[$i]->latitudeHemisphere;
								$val["gps_longitude"] = $data[$i]->longitudeDecimalDegrees;
								$val["gps_ew"] = $data[$i]->longitudeHemisphere;
								$val["gps_speed"] = $data[$i]->speed/1.852; 
								$val["gps_course"] = $data[$i]->course; 
								$val["gps_utc_date"] = date("dmy",strtotime($data[$i]->date));
								$val["gps_msg_ori"] = $data[$i]->msg_ori;
								
								$odometro = $data[$i]->km_rodado;
								
								//speed & odometer cek dari loc_actual
								if($data[$i]->msg_ori != ""){
									$json = json_decode($data[$i]->msg_ori,true);
									if($json[3] == "13"){
										
										//update ambil data odo terkahir dari gps port
										$this->concox->order_by("gps_id","desc");
										$this->concox->select("gps_id,gps_odometer");
										$this->concox->where("gps_odometer >",0);
										$this->concox->where("gps_name",$devices[$j]->imei);
										$this->concox->limit(1);
										$qconcox = $this->concox->get("webtracking_gps");
										$dataconcox = $qconcox->row();
										if(count($dataconcox)>0){
											$odometro = $dataconcox->gps_odometer;
										}else{
											$odometro = 0;
										}
										
										$this->db->order_by("id","desc");
										$this->db->where("imei",$data[$i]->imei);
										$this->db->limit(1);
										$q_loc = $this->db->get("loc_atual");
										if ($q_loc->num_rows() > 0)
										{
											$dlive_loc = $q_loc->row();
											$val["gps_course"] = $dlive_loc->course;
											$val["gps_speed"] =  $dlive_loc->speed/1.852;
																					
										}
										else
										{
											$val["gps_course"] = $data[$i]->course;
											$val["gps_speed"] = $data[$i]->speed/1.852; 
											
										}
										
									}
									
								}
								
								$val["gps_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->date));
								$val["gps_latitude_real"] =	$data[$i]->latitudeDecimalDegrees;
								$val["gps_longitude_real"] = $data[$i]->longitudeDecimalDegrees;
								$val["gps_odometer"] = $odometro;
											
								
								$valinfo["gps_info_device"] = $devices[$j]->imei."@".$device_host;
								$valinfo["gps_info_utc_coord"] = date("His",strtotime($data[$i]->date));
								$valinfo["gps_info_utc_date"] = date("dmy",strtotime($data[$i]->date));
								$valinfo["gps_info_time"] =	date("Y-m-d H:i:s",strtotime($data[$i]->date));
								
								//if ligado S = Engine ON else OFF
								$ignition = $data[$i]->ligado;
								if($ignition == "S") 
								{ 
									$valinfo["gps_info_io_port"] = "0000100000"; 
								}
								else 
								{ 
									$valinfo["gps_info_io_port"] = "0000000000"; 	
								}
								
								$valinfo["gps_info_distance"] = $odometro;
								
								
								if ($data[$i]->latitudeDecimalDegrees != 0)
								{
									printf("Proses Insert ");
									$this->concox->insert("gps",$val);
									$this->concox->insert("gps_info",$valinfo);
									printf("------- Insert DONE \n");
								}
								else
								{
									printf("Latitude = 0 \n");
								}
								
								printf("Proses Delete");
								
								$this->db->where("imei",$data[$i]->imei);
								$this->db->where("date",$data[$i]->date);
								$this->db->delete("gprmc");
								printf("------ DELETE DONE \n");
								
							}
							
						}
					}
					
					
				}
		}
		
		$enddate = date("Y-m-d H:i:s");
		printf("=== FINISH %s , %s \n", $startdate,$enddate);
	}
	
	function daily_multiport_new($groupname="",$maxdata=1000)
	{
		$userid = "";
		$starttime = date("Y-m-d H:i:s");
		$start = mktime();
		$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
		/*$now1 = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'));
		$a = date("Y-m-d H:i:s", $now);
		$b = date("Y-m-d H:i:s", $now1);
		print_r($a." ".$b);exit();*/
		
		printf("[%s] Multiport < %s\r\n", date("Y-m-d H:i:s"), date("d/m/Y", $now)); 
		
		//---- get Multiport ----//
		
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("port_id","asc");
		$this->db->where("port_status",1);
		$this->db->where("port_database",$groupname);
		$this->db->where("port_config !=","");
		$qport = $this->db->get("cron_port");
		if ($qport->num_rows() == 0) return;

		$rowsport = $qport->result();
		$totalport = count($rowsport);
		printf("Total Port : %s \r\n",$totalport);
		$offset=0;
		$p = 0;		
		//looping vehicle
		foreach($rowsport as $rowport)
		{
			if (($p+1) < $offset)
			{
				$p++;
				continue;
			}
			
			$port = $rowport->port_value;
			$portdb = $rowport->port_config;
				
			$this->db = $this->load->database("default", TRUE);
			$this->db->distinct();
			$this->db->order_by("vehicle_id","asc");
			$this->db->select("vehicle_type, vehicle_device, vehicle_info, vehicle_dbhistory_name,user_login");
			if (strlen($port) > 0)
			{
				$this->db->where("vehicle_info LIKE '%".$port."%'",null);
			}
			if($userid != ""){
				$this->db->where("vehicle_user_id", $userid);
			}
			//$this->db->limit(2);
			$this->db->where("vehicle_status <>", 3);
			//$this->db->where("vehicle_device", "061453831992@T8");
			$this->db->join("user", "user_id = vehicle_user_id");
			$q = $this->db->get("vehicle");
			if ($q->num_rows() == 0) return;
			$rows = $q->result();
			$totvehicle = count($rows);
			$pnow = ++$p;
			printf("Port : %s \r\n",$port);
			printf("Total Vehicle : %s \r\n",$totvehicle);
			
			//looping vehicle
			
				$i = 0;		
				foreach($rows as $row)
				{
					$devices = explode("@", $row->vehicle_device);
					
					$table = "webtracking_gps";
					$tableinfo = "webtracking_gps_info";
					
					$table_hist = strtolower($row->vehicle_device)."_gps";
					$tableinfo_hist = strtolower($row->vehicle_device)."_info";
					
					printf("[%s] Moving Data %s:%s (%d/%d) - port %s (%s/%s) \r\n", date("Y-m-d H:i:s"), $row->user_login, $row->vehicle_device, ++$i, $totvehicle, $port, $pnow, $totalport); 
					
					// gps

					$offset = 0;
					$isdelete = false;
					
					while(1)
					{
						printf("=== gps %d - %d", $offset, $maxdata+$offset);
						
						$historydb = $this->load->database($row->vehicle_dbhistory_name, TRUE);
						
						$configportdb = $this->load->database($portdb, TRUE);					
						$configportdb->limit($maxdata, $offset);
						$configportdb->where("gps_name", $devices[0]);
						$configportdb->where("gps_host", $devices[1]);
						$configportdb->where("gps_time <=", date("Y-m-d H:i:s", $now));
						$q = $configportdb->get($table);
						printf(" - data %d \r\n", $q->num_rows());
						if ($q->num_rows() == 0) break;
						
						$total = $q->num_rows();
						$gpses = $q->result_array();									
						$q->free_result();
							
						$confighistorydb = $this->load->database($row->vehicle_dbhistory_name, TRUE);					
						foreach($gpses as $gps)
						{
							unset($gps["gps_id"]);							
							$confighistorydb->insert($table_hist, $gps);
							$isdelete = true;
						}
						
						//if ($total < $maxdata) break;				
						$offset += $maxdata;
					}
					
					if ($isdelete)
					{
						printf("=== delete gps data\r\n");
						
						$configportdb = $this->load->database($portdb, TRUE);							
						$configportdb->where("gps_name", $devices[0]);
						$configportdb->where("gps_host", $devices[1]);
						$configportdb->where("gps_time <=", date("Y-m-d H:i:s", $now));
						$configportdb->delete($table);			
						
						if (date("w") == 0)
						{
							$configportdb->query("OPTIMIZE TABLE `".$table."`");
						}
					}
						
					// info
					
					$offset = 0;
					$isdelete = false;
					
					while(1)
					{
						printf("=== info %d - %d ", $offset, $maxdata+$offset);
						
						$configportdb = $this->load->database($portdb, TRUE);
						$configportdb->limit($maxdata, $offset);
						$configportdb->where("gps_info_device", $row->vehicle_device);
						$configportdb->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
						$q = $configportdb->get($tableinfo);
						printf(" - data %d \r\n", $q->num_rows());
						if ($q->num_rows() == 0) break;
						
						$total = $q->num_rows();
						$infos = $q->result_array();						
						$q->free_result();
						$confighistorydb = $this->load->database($row->vehicle_dbhistory_name, TRUE);
						foreach($infos as $info)
						{
							unset($info["gps_info_id"]);								
							$confighistorydb->insert($tableinfo_hist, $info);
							$isdelete = true;
						}

						//if ($total < $maxdata) break;				
						$offset += $maxdata;
					}
					
					if ($isdelete)
					{
						printf("=== delete info data\r\n");

						$configportdb = $this->load->database($portdb, TRUE);
						$configportdb->where("gps_info_device", $row->vehicle_device);
						$configportdb->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
						$configportdb->delete($tableinfo);			

						if (date("w") == 0)
						{
							$configportdb->query("OPTIMIZE TABLE `".$tableinfo."`");
						}
					}
					
				}
				
		}
		
		$finishtime = date("Y-m-d H:i:s");
		printf("FINISH : %s to %s \r\n", $starttime, $finishtime); 
	}
	
	function dailydb_multiport_new($groupname="",$timezone="",$maxdata=1000)
	{
		$userid = "";
		$starttime = date("Y-m-d H:i:s");
		$start = mktime();
		if($timezone == "WIB"){
			$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'));
		}else{
			$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
		}
		/*$a = date("Y-m-d H:i:s", $now);
		$b = date("Y-m-d H:i:s", $now1);
		print_r($a." ".$b);exit();*/
		
		printf("[%s] Multiport < %s\r\n", date("Y-m-d H:i:s"), date("d/m/Y H:i:s", $now)); 
		
		//---- get Multiport ----//
		
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("port_id","asc");
		$this->db->where("port_status",1);
		$this->db->where("port_database",$groupname);
		$this->db->where("port_config !=","");
		$qport = $this->db->get("cron_port");
		if ($qport->num_rows() == 0) return;

		$rowsport = $qport->result();
		$totalport = count($rowsport);
		printf("Total Port : %s \r\n",$totalport);
		$offset=0;
		$p = 0;		
		
		//looping vehicle
		for($z=0;$z<count($rowsport);$z++)
		{
			$port = $rowsport[$z]->port_value;
			$portdb = $rowsport[$z]->port_config;
				
			$this->db = $this->load->database($portdb,true);
			$this->db->group_by("gps_name");
			$this->db->select("gps_name,gps_host");
			//$this->db->limit(2);
			$q = $this->db->get("gps");
			if ($q->num_rows() == 0) return;
			$rows = $q->result();
		
			$totvehicle = count($rows);
			//$pnow = ++$p;
			printf("Port : %s \r\n",$port);
			printf("Total Vehicle : %s \r\n",$totvehicle);
			
			//looping vehicle
			
				$i = 0;		
				foreach($rows as $row)
				{
					$vehicle_device_string = $row->gps_name."@".$row->gps_host;
					$devices = explode("@", $vehicle_device_string);
					
					$this->db = $this->load->database("default",true);
					$this->db->select("vehicle_id,vehicle_device,vehicle_dbhistory_name,user_login");
					$this->db->where("vehicle_device",$vehicle_device_string);
					$this->db->join("user", "user_id = vehicle_user_id");
					$qdatav = $this->db->get("vehicle");
					$rowdatav = $qdatav->row();
					
					if ($qdatav->num_rows() > 0){
						$user_login_name = $rowdatav->user_login;
						$rows = $q->result();
					
						$table = "webtracking_gps";
						$tableinfo = "webtracking_gps_info";
						
						$table_hist = strtolower($vehicle_device_string)."_gps";
						$tableinfo_hist = strtolower($vehicle_device_string)."_info";
						
						printf("[%s] Moving Data %s:%s (%d/%d) - port %s (%s/%s) \r\n", date("Y-m-d H:i:s"), $user_login_name, $vehicle_device_string, ++$i, $totvehicle, $port, $z+1, $totalport); 
						
						// gps

						$offset = 0;
						$isdelete = false;
						
						while(1)
						{
							printf("=== gps %d - %d", $offset, $maxdata+$offset);
							
							$historydb = $this->load->database($rowdatav->vehicle_dbhistory_name, TRUE);
							
							$configportdb = $this->load->database($portdb, TRUE);					
							$configportdb->limit($maxdata, $offset);
							$configportdb->where("gps_name", $devices[0]);
							$configportdb->where("gps_host", $devices[1]);
							$configportdb->where("gps_time <=", date("Y-m-d H:i:s", $now));
							$q = $configportdb->get($table);
							printf(" - data %d \r\n", $q->num_rows());
							if ($q->num_rows() == 0) break;
							
							$total = $q->num_rows();
							$gpses = $q->result_array();									
							$q->free_result();
								
							$confighistorydb = $this->load->database($rowdatav->vehicle_dbhistory_name, TRUE);					
							foreach($gpses as $gps)
							{
								unset($gps["gps_id"]);							
								$confighistorydb->insert($table_hist, $gps);
								$isdelete = true;
							}
							
							//if ($total < $maxdata) break;				
							$offset += $maxdata;
						}
						
						if ($isdelete)
						{
							printf("=== delete gps data\r\n");
							
							$configportdb = $this->load->database($portdb, TRUE);							
							$configportdb->where("gps_name", $devices[0]);
							$configportdb->where("gps_host", $devices[1]);
							$configportdb->where("gps_time <=", date("Y-m-d H:i:s", $now));
							$configportdb->delete($table);			
							
							if (date("w") == 0)
							{
								$configportdb->query("OPTIMIZE TABLE `".$table."`");
							}
						}
							
						// info
						
						$offset = 0;
						$isdelete = false;
						
						while(1)
						{
							printf("=== info %d - %d ", $offset, $maxdata+$offset);
							
							$configportdb = $this->load->database($portdb, TRUE);
							$configportdb->limit($maxdata, $offset);
							$configportdb->where("gps_info_device", $rowdatav->vehicle_device);
							$configportdb->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
							$q = $configportdb->get($tableinfo);
							printf(" - data %d \r\n", $q->num_rows());
							if ($q->num_rows() == 0) break;
							
							$total = $q->num_rows();
							$infos = $q->result_array();						
							$q->free_result();
							$confighistorydb = $this->load->database($rowdatav->vehicle_dbhistory_name, TRUE);
							foreach($infos as $info)
							{
								unset($info["gps_info_id"]);								
								$confighistorydb->insert($tableinfo_hist, $info);
								$isdelete = true;
							}

							//if ($total < $maxdata) break;				
							$offset += $maxdata;
						}
						
						if ($isdelete)
						{
							printf("=== delete info data\r\n");

							$configportdb = $this->load->database($portdb, TRUE);
							$configportdb->where("gps_info_device", $rowdatav->vehicle_device);
							$configportdb->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
							$configportdb->delete($tableinfo);			

							if (date("w") == 0)
							{
								$configportdb->query("OPTIMIZE TABLE `".$tableinfo."`");
							}
						}
					
					}else{
						printf("==X SKIP NO data Valid ID %d \r\n", $vehicle_device_string);
						//break;
					}
					
					
				}
				
		}
		
		$finishtime = date("Y-m-d H:i:s");
		
		$cron_name = "DAILY DB MULTIPORT"." ".$groupname;
		$message =  urlencode(
					"".$cron_name." \n".
					"Start: ".$starttime." \n".
					"Finish: ".$finishtime." \n"
					);
					
		$sendtelegram = $this->telegram("3916",$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		printf("FINISH : %s to %s \r\n", $starttime, $finishtime); 
	}
	
	function dailydb_port_new($port="",$maxdata=1000)
	{
		$userid = "";
		$starttime = date("Y-m-d H:i:s");
		$start = mktime();
		$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
		/*$now1 = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'));
		$a = date("Y-m-d H:i:s", $now);
		$b = date("Y-m-d H:i:s", $now1);
		print_r($a." ".$b);exit();*/
		
		printf("[%s] Multiport < %s\r\n", date("Y-m-d H:i:s"), date("d/m/Y", $now)); 
		
		//---- get Multiport ----//
		
		$this->db = $this->load->database("default", TRUE);
		$this->db->order_by("port_id","asc");
		$this->db->where("port_status",1);
		$this->db->where("port_value",$port);
		$this->db->where("port_config !=","");
		$qport = $this->db->get("cron_port");
		if ($qport->num_rows() == 0) return;

		$rowsport = $qport->result();
		$totalport = count($rowsport);
		printf("Total Port : %s \r\n",$totalport);
		$offset=0;
		$p = 0;		
		
		//looping vehicle
		for($z=0;$z<count($rowsport);$z++)
		{
			$port = $rowsport[$z]->port_value;
			$portdb = $rowsport[$z]->port_config;
				
			$this->db = $this->load->database($portdb,true);
			$this->db->group_by("gps_name");
			$this->db->select("gps_name,gps_host");
			//$this->db->limit(2);
			$q = $this->db->get("gps");
			if ($q->num_rows() == 0) return;
			$rows = $q->result();
		
			$totvehicle = count($rows);
			//$pnow = ++$p;
			printf("Port : %s \r\n",$port);
			printf("Total Vehicle : %s \r\n",$totvehicle);
			
			//looping vehicle
			
				$i = 0;		
				foreach($rows as $row)
				{
					$vehicle_device_string = $row->gps_name."@".$row->gps_host;
					$devices = explode("@", $vehicle_device_string);
					
					$this->db = $this->load->database("default",true);
					$this->db->select("vehicle_id,vehicle_device,vehicle_dbhistory_name,user_login");
					$this->db->where("vehicle_device",$vehicle_device_string);
					$this->db->join("user", "user_id = vehicle_user_id");
					$qdatav = $this->db->get("vehicle");
					$rowdatav = $qdatav->row();
					
					if ($qdatav->num_rows() > 0){
						$user_login_name = $rowdatav->user_login;
						$rows = $q->result();
					
						$table = "webtracking_gps";
						$tableinfo = "webtracking_gps_info";
						
						$table_hist = strtolower($vehicle_device_string)."_gps";
						$tableinfo_hist = strtolower($vehicle_device_string)."_info";
						
						printf("[%s] Moving Data %s:%s (%d/%d) - port %s (%s/%s) \r\n", date("Y-m-d H:i:s"), $user_login_name, $vehicle_device_string, ++$i, $totvehicle, $port, $z+1, $totalport); 
						
						// gps

						$offset = 0;
						$isdelete = false;
						
						while(1)
						{
							printf("=== gps %d - %d", $offset, $maxdata+$offset);
							
							$historydb = $this->load->database($rowdatav->vehicle_dbhistory_name, TRUE);
							
							$configportdb = $this->load->database($portdb, TRUE);					
							$configportdb->limit($maxdata, $offset);
							$configportdb->where("gps_name", $devices[0]);
							$configportdb->where("gps_host", $devices[1]);
							$configportdb->where("gps_time <=", date("Y-m-d H:i:s", $now));
							$q = $configportdb->get($table);
							printf(" - data %d \r\n", $q->num_rows());
							if ($q->num_rows() == 0) break;
							
							$total = $q->num_rows();
							$gpses = $q->result_array();									
							$q->free_result();
								
							$confighistorydb = $this->load->database($rowdatav->vehicle_dbhistory_name, TRUE);					
							foreach($gpses as $gps)
							{
								unset($gps["gps_id"]);							
								$confighistorydb->insert($table_hist, $gps);
								$isdelete = true;
							}
							
							//if ($total < $maxdata) break;				
							$offset += $maxdata;
						}
						
						if ($isdelete)
						{
							printf("=== delete gps data\r\n");
							
							$configportdb = $this->load->database($portdb, TRUE);							
							$configportdb->where("gps_name", $devices[0]);
							$configportdb->where("gps_host", $devices[1]);
							$configportdb->where("gps_time <=", date("Y-m-d H:i:s", $now));
							$configportdb->delete($table);			
							
							if (date("w") == 0)
							{
								$configportdb->query("OPTIMIZE TABLE `".$table."`");
							}
						}
							
						// info
						
						$offset = 0;
						$isdelete = false;
						
						while(1)
						{
							printf("=== info %d - %d ", $offset, $maxdata+$offset);
							
							$configportdb = $this->load->database($portdb, TRUE);
							$configportdb->limit($maxdata, $offset);
							$configportdb->where("gps_info_device", $rowdatav->vehicle_device);
							$configportdb->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
							$q = $configportdb->get($tableinfo);
							printf(" - data %d \r\n", $q->num_rows());
							if ($q->num_rows() == 0) break;
							
							$total = $q->num_rows();
							$infos = $q->result_array();						
							$q->free_result();
							$confighistorydb = $this->load->database($rowdatav->vehicle_dbhistory_name, TRUE);
							foreach($infos as $info)
							{
								unset($info["gps_info_id"]);								
								$confighistorydb->insert($tableinfo_hist, $info);
								$isdelete = true;
							}

							//if ($total < $maxdata) break;				
							$offset += $maxdata;
						}
						
						if ($isdelete)
						{
							printf("=== delete info data\r\n");

							$configportdb = $this->load->database($portdb, TRUE);
							$configportdb->where("gps_info_device", $rowdatav->vehicle_device);
							$configportdb->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
							$configportdb->delete($tableinfo);			

							if (date("w") == 0)
							{
								$configportdb->query("OPTIMIZE TABLE `".$tableinfo."`");
							}
						}
					
					}else{
						printf("==X SKIP NO data Valid ID %d \r\n", $vehicle_device_string);
						//break;
					}
					
					
				}
				
		}
		
		$finishtime = date("Y-m-d H:i:s");
		printf("FINISH : %s to %s \r\n", $starttime, $finishtime); 
	}
	
	function deletetraccar_event_tk315()
	{
		printf("=========== Start Auto Delete Traccar Events ================\r\n");
		$this->db = $this->load->database("GPS_TRACCAR_TK315",true);
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
	
	function deletetraccar_event()
	{
		printf("=========== Start Auto Delete Traccar Events ================\r\n");
		$this->db = $this->load->database("GPS_TRACCAR",true);
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
	
	function telegram($user,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $url = "http://lacak-mobil.com/telegram/telegrampost";
        
        $data = array("id" => $user, "message" => $message);
        $data_string = json_encode($data);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);                                                           
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);                        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));  
        $result = curl_exec($ch);
        
        if ($result === FALSE) {
            die("Curl failed: " . curL_error($ch));
        }
        echo $result;
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
    }
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
