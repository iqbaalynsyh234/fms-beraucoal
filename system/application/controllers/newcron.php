<?php
include "base.php";

class NewCron extends Base {
	var $vindex;
	var $m_last_smsreceive;

	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		
		$this->m_last_smsreceive = 0;
	}
	
	function newhistory()
	{
		$this->db->distinct();
		$this->db->select("vehicle_device");
		$this->db->where("vehicle_status", 1);
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			printf("history for %s\n", $rows[$i]->vehicle_device);
			
			$historydb = $this->load->database("gpshistory", TRUE);
			
			// === CREATE TABLE HISTORY PER VEHICLE
			
			$histtable = $this->load->view("db/gps", FALSE, TRUE);			
			$sql = sprintf($histtable, strtolower($rows[$i]->vehicle_device)."_gps");

			$historydb->query($sql);
			
			// === CREATE TABLE INFO HISTORY PER VEHICLE

			$histtable = $this->load->view("db/info", FALSE, TRUE);			
			$sql = sprintf($histtable, strtolower($rows[$i]->vehicle_device)."_info");

			$historydb->query($sql);
			
			// === AMBIL DATAB GPS < HARI INI
						
			
			$this->load->database("default", TRUE);
		}
	}
	
	function history($vtype="")
	{
		$this->db->order_by("vehicle_type", "asc");
		$this->db->where("vehicle_status", 1);
		$this->db->select("vehicle_device, vehicle_type");	
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			die("vehicle is empty\r\n");
		}
		
		$rows = $q->result();
		
		for($i=0; $i < count($rows); $i++)
		{
			$vehiclesbytype[strtoupper($rows[$i]->vehicle_type)][] = $rows[$i]->vehicle_device; 
		}
		
		$tblhists = $this->config->item("table_hist");
		$tblinfos = $this->config->item("table_hist_info");
		$allservicenames = $this->config->item("service");

		foreach($vehiclesbytype as $vehicletype=>$vehicleids)
		{
			$start = time();
			
			if ($vtype)
			{
				$vtype1 = str_replace("_", " ", $vtype);
				if (! in_array($vehicletype, array($vtype, $vtype1))) continue;
			}
			
			printf("Processing %s (%d)....\r\n", $vehicletype, count($vehicleids));
			
			$tablegps = $this->gpsmodel->getGPSTable($vehicletype);
			$tableinfo = $this->gpsmodel->getGPSInfoTable($vehicletype);
			
			if (! isset($allservicenames[$tablegps]))
			{
				printf("can't found service for table %s\r\n", $tablegps);
				continue;
			}
			$servicenames = $allservicenames[$tablegps];
			
			if (! isset($tblhists[$vehicletype]))
			{
				printf("can't found table hist for %s\r\n", $vehicletype);
				continue;
			}
			$tablegpshist = $tblhists[$vehicletype];			


			if (! isset($tblinfos[$vehicletype]))
			{
				printf("can't found table hist info for %s\r\n", $vehicletype);
				continue;
			}
			$tableinfohist = $tblinfos[$vehicletype];
			
			foreach($servicenames as $servicename)
			{				
				$this->servicekill($servicename);
			}
			
			$yesterday = mktime(-7, 0, 0, date('n'), date('j')-1, date('Y'));
			
			unset($updateds);
			unset($infoupdateds);
			
			$i = 0;
			foreach($vehicleids as $vehicleid)
			{
				printf(">>> %03d/%d Processing %s \r\n", ++$i, count($vehicleids), $vehicleid);
				
				$ids = explode("@", $vehicleid);
				
				// gps
				
				$this->db->limit(1);
				$this->db->order_by("gps_time", "desc");
				$this->db->where("gps_name", $ids[0]);
				$this->db->where("gps_host", $ids[1]);				
				$q = $this->db->get($tablegps);
				
				if ($q->num_rows() > 0) 
				{										
					$row = $q->row();
					
					$updateds[] = $row->gps_id;
				}
				else
				{				
					printf(">>> %03d/%d %s historically\r\n", ++$i, count($vehicleids), $vehicleid);

					$this->db->limit(1);
					$this->db->order_by("gps_time", "desc");
					$this->db->where("gps_name", $ids[0]);
					$this->db->where("gps_host", $ids[1]);				
					$q = $this->db->get($tablegpshist);
					
					if ($q->num_rows() > 0)
					{
						unset($row);
						$row = $q->row_array();
						
						unset($row['gps_id']);
						$this->db->insert($tablegps, $row);
						
						$updateds[] = $this->db->insert_id();
					}
				}
				
				// gps info
				
				$this->db->limit(1);
				$this->db->order_by("gps_info_time", "desc");
				$this->db->where("gps_info_device", $vehicleid);
				$q = $this->db->get($tableinfo);
				
				if ($q->num_rows() > 0) 
				{										
					$row = $q->row();
					
					$infoupdateds[] = $row->gps_info_id;
				}
				else
				{				
					$this->db->limit(1);
					$this->db->order_by("gps_info_time", "desc");
					$this->db->where("gps_info_device", $ids[0]);
					$q = $this->db->get($tableinfohist);
					
					if ($q->num_rows() > 0)
					{
						unset($row);
						$row = $q->row_array();
						
						unset($row['gps_info_id']);
						$this->db->insert($tableinfo, $row);
						
						$infoupdateds[] = $this->db->insert_id();
					}
				}				
			}
			
			if (isset($updateds))
			{
				$sql = sprintf("
							INSERT INTO %s%s
								(		gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
									,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real
									, 	gps_longitude_real,	gps_odometer, gps_workhour
								)							
							SELECT 		gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
									,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real
									, 	gps_longitude_real,	gps_odometer, gps_workhour 
							FROM 	%s%s 
							WHERE 	gps_time > '%s'"
							,$this->db->dbprefix, $tablegpshist, $this->db->dbprefix, $tablegps, date('Y-m-d H:i:s', $yesterday)
						);
				
				printf(">>> move to tabel history\r\n");
				$q = $this->db->query($sql);
				
				printf(">>> delete history data from current table\r\n");
				$this->db->where_not_in("gps_id", $updateds);
				$this->db->delete($tablegps);
								
				$sql = sprintf("OPTIMIZE TABLE %s%s", $this->db->dbprefix, $tablegps);
				$this->db->query($sql);				
			}

			if (isset($infoupdateds))
			{
				$sql = sprintf("
							INSERT INTO %s%s
								(		gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data
									, 	gps_info_ad_input, gps_info_utc_coord, gps_info_utc_date, gps_info_alarm_alert, gps_info_time
									, 	gps_info_status, gps_info_gps

								)							
							SELECT 		gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data
									, 	gps_info_ad_input, gps_info_utc_coord, gps_info_utc_date, gps_info_alarm_alert, gps_info_time
									, 	gps_info_status, gps_info_gps
							FROM 	%s%s 
							WHERE 	gps_info_time > '%s'"
							,$this->db->dbprefix, $tableinfohist, $this->db->dbprefix, $tableinfo, date('Y-m-d H:i:s', $yesterday)
						);
				
				printf(">>> move to tabel info history\r\n");
				$q = $this->db->query($sql);
				
				printf(">>> delete history info data from current table\r\n");
				$this->db->where_not_in("gps_info_id", $infoupdateds);
				$this->db->delete($tableinfo);
								
				$sql = sprintf("OPTIMIZE TABLE %s%s", $this->db->dbprefix, $tableinfo);
				$this->db->query($sql);				
			}

			foreach($servicenames as $servicename)
			{				
				$this->servicestart($servicename);
			}
			
			$mail['subject'] = sprintf("proses history data: %s", $vehicletype);
			$mail['message'] = sprintf("mulai: %s, berakhir: %s, lama proses: %d menit", date("d/m/Y H:i:s", $start), date("d/m/Y H:i:s"), round((time()-$start)/60));
			$mail['dest'] = "report.lacakmobil@gmail.com,owner@adilahsoft.com"; 
			
			lacakmobilmail($mail);
		}
	}
	
	function oldvehicle($month=6)
	{
		$sixmonthbefore = mktime(0, 0, 0, date('n')-$month, date('j'), date('Y'));

		$this->db->select("vehicle_device, vehicle_no");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 <", date("Ymd", $sixmonthbefore));
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			printf("[%s] tidak ada data\r\n", date("Ymd H:i:s"));
			return;
		}
		
		$rows = $q->result();
		foreach($rows as $row)
		{
			printf("[%s] %s %s\r\n", date("Ymd H:i:s"), $row->vehicle_device, $row->vehicle_no);
		}
		
		unset($update);
		$update['vehicle_status'] = 2;
		
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 <", date("Ymd", $sixmonthbefore));
		$this->db->update("vehicle", $update);				
	}
	
	function oldhist()
	{
		$start = mktime();
		
		$this->db->where("config_name", "maxhist");
		$q = $this->db->get("config");
		
		if ($q->num_rows() == 0)
		{
			die("Hist config is not define");
		}

		$row = $q->row();		
		$t = mktime(16, 59, 59, date('n')-$row->config_value, date('j'), date('Y'));

		$tblinfos = $this->config->item("table_hist_info");
		foreach($tblinfos as $tblinfo)
		{
			printf("move to archive %s < %s\r\n", $tblinfo, date("Y-m-d H:i:s", $t));

			$sql = sprintf("
					INSERT INTO %sgps_info_archive 
					(
							gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data, gps_info_ad_input, gps_info_utc_coord
						,	gps_info_utc_date, gps_info_alarm_alert, gps_info_time, gps_info_status, gps_info_gps
					)						
					SELECT 	gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data, gps_info_ad_input, gps_info_utc_coord
							,	gps_info_utc_date, gps_info_alarm_alert, gps_info_time, gps_info_status, gps_info_gps
					FROM 	%s%s
					WHERE 	gps_info_time < '%s' 
				", $this->db->dbprefix, $this->db->dbprefix, $tblinfo, date("Y-m-d H:i:s", $t));
			
			$this->db->query($sql);
			
			printf("delete %s < %s\r\n", $tblinfo, date("Y-m-d H:i:s", $t));
			
			$this->db->where("gps_info_time <", date("Y-m-d H:i:s", $t));
			$this->db->delete($tblinfo);
			
			$sql = sprintf("OPTIMIZE TABLE %s%s", $this->db->dbprefix, $tblinfo);
			printf("%s\r\n", $sql);
			
			$this->db->query($sql);						
		}
		
		$tblhists = $this->config->item("table_hist");
		foreach($tblhists as $tblhist)
		{
			printf("move to archive %s < %s\r\n", $tblhist, date("Y-m-d H:i:s", $t));
			
			$sql = sprintf("
				INSERT INTO %sgps_archive
				(
						gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
					,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real
					,	gps_odometer, gps_workhour
				)						
				SELECT 		gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
						,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real
						,	gps_odometer, gps_workhour			
				FROM 	%s%s
				WHERE	gps_time < '%s'
			", $this->db->dbprefix, $this->db->dbprefix, $tblhist, date("Y-m-d H:i:s", $t));
			
			$this->db->query($sql);
			
			
			printf("delete %s < %s\r\n", $tblhist, date("Y-m-d H:i:s", $t));
			
			$this->db->where("gps_time <", date("Y-m-d H:i:s", $t));
			$this->db->delete($tblhist);
			
			$sql = sprintf("OPTIMIZE TABLE %s%s", $this->db->dbprefix, $tblhist);
			printf("%s\r\n", $sql);
			
			$this->db->query($sql);						
		}

		$mail['subject'] = sprintf("remove history data");
		$mail['message'] = sprintf("mulai: %s, 
		berakhir: %s, 
		lama proses: %d menit
		
		=======================
		
		job ini jalan setiap hari sabtu jam 03:00, lokasi ada di colo 2
		konfigurasi maximum history ada di table config
		
		=======================
				
		", date("d/m/Y H:i:s", $start), date("d/m/Y H:i:s"), round((time()-$start)/60));
		$mail['dest'] = "report.lacakmobil@gmail.com,owner@adilahsoft.com"; 
		
		lacakmobilmail($mail);
		
	}

	function servicekill($servicename)
	{
		$pid = $this->getpid($servicename);
		if (! $pid) return;
				
		printf("%s: PID=%d\r\n", $servicename, $pid); 
		
		$kill = sprintf("taskkill /PID %d /F", $pid);		
		exec($kill);					
		printf("%s: killed\r\n", $servicename); 			
	}
	
	function servicestart($servicename)
	{
		$start = sprintf("sc start %s", $servicename);
		exec($start);		
		printf("%s: started\r\n", $servicename); 
	}

	function getpid($servicename)
	{
		exec("sc queryex ".$servicename, $lines);

		for($i=0; $i < count($lines); $i++)
		{
			$line = trim($lines[$i]);
			$pos = strpos($line, ":");
			
			$key = trim(substr($line, 0, $pos-1));
			$val = trim(substr($line, $pos+1));
			
			if ($key == "PID") return $val;
		}
		
		return 0;
	}
	
	function dump()
	{
		$tables = $this->config->item("DUMP_TABLE");
		$path = $this->config->item("MYSQL_DUMP_PATH");
		$cli = $this->config->item("MYSQL_DUMP_CLI");
		
		$tablename = "";
		foreach($tables as $tbl)
		{
			$tablename .= " ".$this->db->dbprefix.$tbl;
		}
		
		$filename = "dump_".date("Ymd").".sql";

		$cli = sprintf($cli, $tablename, $path, $filename);
		system($cli);
		
		$message = sprintf("dump %s to %s%s", implode(",", $tables), $path, $filename);
		mail("report.lacakmobil@gmail.com,owner@adilahsoft.com", "dump", $message);
		
	}

	function delayalert()
	{
		$tblhists = $this->config->item("table_hist");
		$delays = $this->config->item("css_tracker_delay");		
		
		$this->db->order_by("user_login", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status", 1);
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{			
			printf("%04d %s %s...\r\n", $i+1, $rows[$i]->user_login, $rows[$i]->vehicle_no);
			
			$this->db = $this->load->database("master", TRUE);

			if ($rows[$i]->vehicle_info)
			{
				$json = json_decode($rows[$i]->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$tablegps = $this->config->item("external_gpstable");					
						$tablegpshist = $this->config->item("external_gpstable_history");
											
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}
			
			if (! isset($tablegps))
			{			
				$tablegps = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);
				$tablegpshist = $tblhists[strtoupper($rows[$i]->vehicle_type)];
			}
			
			$devices = explode("@", $rows[$i]->vehicle_device);
			
			$this->db->limit(1);
			$this->db->order_by("gps_time", "desc");
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			
			$q = $this->db->get($tablegps);
			
			if ($q->num_rows() == 0)
			{
				// ambil dari history
				
				$this->db->limit(1);
				$this->db->order_by("gps_time", "desc");
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[0]);
				
				$q = $this->db->get($tablegpshist);
				
				if ($q->num_rows() == 0)
				{
					$rows[$i]->time_fmt = "-";
					$rows[$i]->delays = $delays[0];
					
					if ($rows[$i]->user_agent == $this->config->item("GPSANDALASID"))
					{
						$datas['gpsandalas'][] = $rows[$i];
					}
					else
					{
						$datas['lacakmobil'][] = $rows[$i];
					}
					continue;
				}				
			}
			
			$row = $q->row();
			
			$time = dbmaketime($row->gps_time)+7*3600;
			$delta = mktime()-$time;
			
			for($j=0; $j < count($delays); $j++)
			{
				if ($delta > ($delays[$j][0]*60)) break;				
			}
			
			if ($j > 1) continue;	
			
			$rows[$i]->time_fmt = date("d/m/Y H:i:s", $time);
			$rows[$i]->delays = $delays[$j];
			
			if ($rows[$i]->user_agent == $this->config->item("GPSANDALASID"))
			{
				$datas['gpsandalas'][] = $rows[$i];
			}
			else
			{
				$datas['lacakmobil'][] = $rows[$i];
			}						
		}
		
		$this->db = $this->load->database("master", TRUE);
		
		if (! isset($datas)) return;

		unset($params);		
		$params['datas'] = $datas['gpsandalas'];
		$html = $this->load->view("sms/delayalert", $params, TRUE);

		$mail['format'] = "html";
		$mail['subject'] = sprintf("data delay alert");
		$mail['message'] = $html;
		$mail['dest'] = "norman_ab@gpsandalas.com,zad_anwar@gpsandalas.com,owner@adilahsoft.com";		
		
		lacakmobilmail($mail);

		unset($params);
		$params['datas'] = $datas['lacakmobil'];
		$html = $this->load->view("sms/delayalert", $params, TRUE);

		$mail['format'] = "html";
		$mail['subject'] = sprintf("data delay alert");
		$mail['message'] = $html;
		$mail['dest'] = "report.lacakmobil@gmail.com,owner@adilahsoft.com"; 
	
		lacakmobilmail($mail);		
		
	}
	
	function restartvehicle($nextyellow=30)
	{
		$try = 0;
		while($this->dorestartvehicle($nextyellow))
		{
			if (++$try > 5000) break;
		}		
	}
	
	function dorestartvehicle($nextyellow=15)
	{
		$this->db = $this->load->database("default", TRUE);

		$this->db->where("config_name", "lastrestart");
		$q = $this->db->get("config");
		
		if ($q->num_rows() == 0)
		{
			$lastid = 0;
			$isexist = false;
		}
		else
		{
			$isexist = true;
			$row = $q->row();
			$lastid = $row->config_value;
		}
		
		$this->db->order_by("vehicle_id", "asc");
		$this->db->limit(1);
		$this->db->where("vehicle_id >", $lastid);
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			$this->updatelastrestart($isexist, 0);			
			return false;
		}
		
		$row = $q->row();		
		
		$this->updatelastrestart($isexist, $row->vehicle_id);
		
		// cek terlebih dahulu apakah restart terakhir sudah 30 menit

		$this->db->order_by("logs_created", "desc");
		$this->db->limit(1);
		$this->db->where("logs_type", "restartdevice");
		$this->db->where("logs_content", $row->vehicle_device);
		$q = $this->db->get("logs");
		if ($q->num_rows() > 0)
		{
			$rowlog = $q->row();
			$time = dbmaketime($rowlog->logs_created);
			
			if ($time+30*60 > mktime()) 
			{
				echo "baru saja direstart.\n";
				return true;
			}
		}
		
		printf("%s %s...\r\n", $row->user_login, $row->vehicle_no);		
		
		if ($row->vehicle_info)
		{
			$json = json_decode($row->vehicle_info);
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					$tablegps = $this->config->item("external_gpstable");					
					$tablegpshist = $this->config->item("external_gpstable_history");
										
					$this->db = $this->load->database($database, TRUE);
				}
			}			
		}
		
		if (! isset($tablegps))
		{				
			$tablegps = $this->gpsmodel->getGPSTable($row->vehicle_type);
		}
		
		if (! $tablegps) 
		{
			echo "table tidak terdefinisi\n";
			return true;
		}
		
		$devices = explode("@", $row->vehicle_device);
		if (count($devices) < 2) 
		{
			echo "error device id\n";
			return true;
		}
		
		$this->db->limit(1);
		$this->db->order_by("gps_time", "desc");
		$this->db->where("gps_name", $devices[0]);
		$this->db->where("gps_host", $devices[1]);
		
		$q = $this->db->get($tablegps);
		if ($q->num_rows() == 0) 
		{
			echo "data not found\n";
			return true;
		}

		$rowgps = $q->row();
		
		$css_tracker_delay = $this->config->item("css_tracker_delay");
		$late = ($css_tracker_delay[1][0]-$nextyellow)*60;
		
		$time = dbmaketime($rowgps->gps_time)+7*3600;
		$delta = mktime()-$time;
		if ($delta < $late) 
		{
			echo "updated.\n";
			return true;
		}
		
		$this->db = $this->load->database("master", TRUE);
		
		// send restart
		
		$restart = $this->smsmodel->restart($row->vehicle_type, $row->vehicle_operator);
		
		if (strlen($restart) == 0)
		{
			echo "not support\n";
			return true;
		}
		
		if (strcasecmp($restart, "NOT SUPPORT") == 0)
		{
			$mail['subject'] = sprintf("operator dengan no sim card %s tidak support restart via sms", $row->vehicle_card_no);
			$mail['message'] = "pastikan memasukkan nama operator yang benar di form kendaraan";
			$mail['dest'] = "report.lacakmobil@gmail.com,owner@adilahsoft.com"; 
			
			lacakmobilmail($mail);
			return true;					
		}

		$hp = valid_mobile($row->vehicle_card_no);
		
		if (! $hp) 
		{
			printf("invalid no: %s\r\n", $hp);
			return true;
		}

		$xml = sprintf("%s\1%s", $row->vehicle_card_no, $restart);
		printf("%s\n", $xml);
		$this->smsmodel->sendsms($xml, 1);
	
		unset($update);
		
		$update['logs_type'] = "restartdevice";
		$update['logs_created'] = date("Y-m-d H:i:s");
		$update['logs_content'] = $row->vehicle_device;
		
		if (isset($rowlog))
		{
			$this->db->where("logs_content", $row->vehicle_device);
			$this->db->update("logs", $update);
			
			return false;
		}
			
		$this->db->insert("logs", $update);
		return false;
	}
	
	function updatelastrestart($isexist, $nextid)
	{
		unset($update);
		
		$update['config_name'] = "lastrestart";
		$update['config_value'] = $nextid;
		$update['config_lastmodified'] = date("Y-m-d H:i:s");
		$update['config_lastmodifier'] = 0;
		
		if ($isexist)
		{
			$this->db->where("config_name", "lastrestart");
			$this->db->update("config", $update);
		}
		else
		{
			$this->db->insert("config", $update);
		}
	}

	//Cron Geofence Khusus KIM
	function geofencealert_kim()
	{			
		  $this->db = $this->load->database("master_kim", true);
                $this->db->select("geofence_vehicle");
                $this->db->distinct();
                $q = $this->db->get("geofence");                
                $total = $q->num_rows();

		$time = mktime();
		for($i=0; $i < 50; $i++)
		{
			$this->dogeofencealert_kim();
			$delta = mktime()-$time;
			if ($delta >= 40) break;
		}
	}

	//Cron Geofence Khusus Indah Kiat
	function geofencealert_indahkiat()
	{			
		  $this->db = $this->load->database("master_indahkiat", true);
                $this->db->select("geofence_vehicle");
                $this->db->distinct();
                $q = $this->db->get("geofence");                
                $total = $q->num_rows();

		$time = mktime();
		for($i=0; $i < 50; $i++)
		{
			$this->dogeofencealert_indahkiat();
			$delta = mktime()-$time;
			if ($delta >= 40) break;
		}
	}
	
	function geofencealert()
	{ 
		$this->db->where("config_name", "geofencealertprocessing");
		$q = $this->db->get("config");
		
		if ($q->num_rows())
		{
			$row = $q->row();
			
			if ($row->config_value == 1) 
			{				
				$t = dbmaketime($row->config_lastmodified);
				$delta = mktime()-$t;
				
				if ($delta > 3600)
				{
					unset($update);
					
					$update['config_value'] = 0;
					
					$this->db->where("config_name", "geofencealertprocessing");
					$this->db->update("config", $update);	

					print("masih proses\r\n");
					return;				
				}
				
				print("masih proses\r\n");		
				return;						
			}
			
			unset($update);
			
			$update['config_value'] = 1;
			
			$this->db->where("config_name", "geofencealertprocessing");
			$this->db->update("config", $update);
		}
		else
		{
			unset($insert);
			
			$insert['config_name'] = "geofencealertprocessing";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}

		$this->db->distinct();
		$this->db->select("user.*, vehicle.*");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 >=",date("Ymd"));
		//$this->db->where("user_company >", 0);
		$this->db->where("user_id <>",1493); //User Tupperware tidak ikut cron ini
		$this->db->where("user_id <>",1488); //Trans Continent
		$this->db->where("user_id <>",1168); //Takari
		$this->db->where("user_id <>",2331); //user it.balrich tidak ikut cron ini
		$this->db->where("user_id <>",1032); //user balrich tidak ikut cron ini
		$this->db->join("vehicle", "vehicle_device = geofence_vehicle");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->where("geofence_status", 1);
		$q = $this->db->get("geofence");
		
		if ($q->num_rows() == 0)
		{
			echo "tidak ada user yang setting geofence\r\n";
			$this->geofencealert_release();
			return;
		}				
		
		$rows = $q->result();
		$i = 0;
		foreach($rows as $row)
		{			
			/* $jsonws = json_decode($row->vehicle_info);
			if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $row->vehicle_no);
				continue;
			}			 */		
			
			printf("\r\n\r\n[%s] %02d %s %s\r\n", date("Y-m-d H:i:s"), $i, $row->vehicle_device, $row->vehicle_no);

			$this->dogeofencealert($row);
			$i++;
		}


		$this->geofencealert_release();
	}
	
	function dogeofencealert($vehicle)
	{
		// ambil user yg setting geofence
			
		$devices = explode("@", $vehicle->vehicle_device);
		if (count($devices) < 2) 
		{
			printf("----- Invalid device\r\n");
			return;
		}
			
		// ambil data terakhir alert geofence
			
		$this->db->limit(1);
		$this->db->order_by("geofence_lastchecked", "desc");		
		$this->db->where("geoalert_vehicle", $vehicle->vehicle_device);
		$q = $this->db->get("geofence_alert");
		if ($q->num_rows() == 0)
		{
			$lastdir = -1;
			$lastchecked = mktime()-7*3600;
			$lastgeofence = -1;
		}
		else
		{
			$row = $q->row();				
			$lastdir = $row->geoalert_direction;
			$lastchecked = dbmaketime($row->geofence_lastchecked);
			$geoalert_id = $row->geoalert_id;
			$lastgeofence = $row->geoalert_geofence;
			
			if ($lastchecked < (mktime()-2*24*3600-7*3600))
			{
				$lastchecked = mktime()-2*24*3600-7*3600;
			}
		}
		
		$me = date("Y-m-d H:i:s", $lastchecked-7*3600);
		printf("Lastchecked : %s\r\n",$me);
		// ambil data gps
		$json = json_decode($vehicle->vehicle_info);
		if ($vehicle->vehicle_info)
		{
			
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					echo "database: ".$database."\n";
					$tablegps = $this->config->item("external_gpstable");					
					$tablegpshist = $this->config->item("external_gpstable_history");
										
					$this->db = $this->load->database($database, TRUE);
				}
			}

			//Jika WebSocket
			if (isset($json->vehicle_ws)) 
			{	
				$database = "gpshistory2";
				$tablegps = strtolower($devices[0]."@".$devices[1]."_gps");
				$tablegpshist = strtolower($devices[0]."@".$devices[1]."_info");
				$this->db = $this->load->database($database, TRUE);	
			}
		}			

		if (! isset($tablegps))
		{		
			$table_hist = $this->config->item("table_hist");	
						
			$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
			$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
		}
		
		if (! $tablegps) 
		{
			printf("----- tabel %s tidak ada\r\n", $tablegps);
			$this->db->cache_delete_all();
			return;
		}
		
		$getgpsstart = mktime();			
		$tyesterday = mktime(-7, 59, 59, date('n', $getgpsstart), date('j', $getgpsstart), date('Y', $getgpsstart));		
		
		if ($lastchecked < $tyesterday && (!isset($json->vehicle_ws)))
		{
			printf("----- check to history\n");
			
			$sql = sprintf("SELECT * FROM (SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s') UNION SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s'))  tbl1 ORDER BY gps_time ASC "
				, $this->db->dbprefix
				, $tablegps
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)
				, $this->db->dbprefix
				, $tablegpshist
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)

			);
			$q = $this->db->query($sql);
		}
		elseif($lastchecked < $tyesterday && (isset($json->vehicle_ws)))
		{
			printf("----- Websocket vehicle check to history\n");
			
			$this->db = $this->load->database("gpshistory",true);
			$this->db->order_by("gps_time", "asc");					
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);	
		}
		else
		{	
			if (!isset($json->vehicle_ws))
			{
				printf("----- check to current\n");
				$this->db->order_by("gps_time", "asc");					
				$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$q = $this->db->get($tablegps);			
			}
			else
			{
				printf("----- Ambil Dari Database TMP Socket \n");
				$this->db2 = $this->load->database("gpshistory2",true);
				$this->db2->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
				$this->db2->where("gps_name", $devices[0]);
				$this->db2->where("gps_host", $devices[1]);
				$q = $this->db2->get($tablegps);			
			}
		}
		
		if ($q->num_rows() == 0 && (isset($json->vehicle_ws)))
		{
			printf("----- Ambil Dari Database TMP Socket \n");
			$this->db2 = $this->load->database("gpshistory2",true);
			$this->db2->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db2->where("gps_name", $devices[0]);
			$this->db2->where("gps_host", $devices[1]);
			$q = $this->db2->get($tablegps);			
		}
			
		if ($q->num_rows() == 0) 
		{
			$this->db = $this->load->database("master", TRUE);
			
			printf("----- tidak ada sejak %s\r\n", date("d/m/Y H:i:s", $lastchecked));
			return;
		}
			
		$rowgps = $q->result();
		
		$this->db = $this->load->database("master", TRUE);
		
		printf("----- lama ambil data gps (%d): %d second\r\n", count($rowgps), mktime()-$getgpsstart);
			
		$checkarea = mktime();	
		$found = false;		
		
		for($i=0; $i < count($rowgps); $i++)
		{	
			$lat = getLatitude($rowgps[$i]->gps_latitude, $rowgps[$i]->gps_ns);
			$lng = getLongitude($rowgps[$i]->gps_longitude, $rowgps[$i]->gps_ew);
			
			$gps = $rowgps[$i];
			$gps->lat = $lat;
			$gps->lng = $lng;
				
			$sql = sprintf("
					SELECT 	* 
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_vehicle = '%s' )
							AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle->vehicle_device);

			$q = $this->db->query($sql);
			
			//Timesheet Report
			//Khusus KUMIS LOGISTICS
			if ($vehicle->vehicle_user_id == "1261")
			{
				printf("----- Proses Vehicle KUMIS LOGISTICS \r\n");
				
				$sqlstreet = sprintf("
					SELECT 	street_name
					FROM 	%sstreet
					WHERE 	TRUE
							AND CONTAINS(street_line, GEOMFROMTEXT('POINT(%s %s)')) 
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat);
				$qstreet = $this->db->query($sqlstreet);
				
				if ($qstreet->num_rows() > 0)
				{
					$rowstreet = $qstreet->row();
					$str_name = $rowstreet->street_name;
					$exs = explode(",",$str_name);
					$tlg = date("Y-m-d");
				
					if (isset($exs[0]))
					{
						//select timesheet
						$this->dbtransporter = $this->load->database("transporter",true);
						$this->dbtransporter->where("timesheet_status","1");
						$this->dbtransporter->where("timesheet_vehicle",$vehicle->vehicle_device);
						$this->dbtransporter->where("timesheet_geo_name",$exs[0]);
						$this->dbtransporter->limit(1);
						$qtime = $this->dbtransporter->get("timesheet");
						$rtimesheet = $qtime->row();
						
						if($qtime->num_rows() == 0)
						{
							printf("----- Timesheet Not In Schedule \r\n");
						}
						else
						{
						
							//Cek Data Terakhir
							$this->dbtransporter->order_by("timeplan_report_id","desc");
							$this->dbtransporter->select("timeplan_report_geo");
							$this->dbtransporter->where("timeplan_report_vehicle_device",$vehicle->vehicle_device);
							$this->dbtransporter->limit(1);
							$qreport = $this->dbtransporter->get("timeplan_report");
							$rreport = $qreport->row();
							
							if(($qtime->num_rows() == 0) || ($rreport->timeplan_report_geo != $exs[0]))
							{
								//Insert To Report
								unset($timesheet_report);						
								$timesheet_report["timeplan_report_vehicle_id"] = $vehicle->vehicle_id;
								$timesheet_report["timeplan_report_vehicle_device"] = $vehicle->vehicle_device;
								$timesheet_report["timeplan_report_company"] = $rtimesheet->timesheet_company;
								$timesheet_report["timeplan_report_route"] = $rtimesheet->timesheet_route;
								$timesheet_report["timeplan_report_geo"] = $rtimesheet->timesheet_geo_name;
								$timesheet_report["timeplan_report_driver"] = $rtimesheet->timesheet_driver;
								$timesheet_report["timeplan_report_timeplan_in"] = $rtimesheet->timesheet_time;
								$timesheet_report["timeplan_report_timeplan_out"] = $rtimesheet->timesheet_time_out;
								$timesheet_report["timeplan_report_time_in"] = date("H:i:s", dbmaketime($rowgps[$i]->gps_time)+7*3600);
								//$timesheet_report["timeplan_report_time_out"] = ;
								$timesheet_report["timeplan_cycle"] = $rtimesheet->timesheet_cycle;
								$timesheet_report["timeplan_report_date"] = date("Y-m-d H:i:s", dbmaketime($rowgps[$i]->gps_time)+7*3600);

								$this->dbtransporter->insert("timeplan_report",$timesheet_report);
								printf("----- Insert Timesheet Report OK* \r\n");
							}
							else
							{
								printf("----- Posisi Masih Sama Dengan Timesheet Sebelumnya \r\n");
							}
						}
						
						$this->dbtransporter->cache_delete_all();
						$this->dbtransporter->close();
					}
				}
			}
			//End Timesheet Report
			//Kumis Logistics
			
			if ($q->num_rows() == 0)
			{
				if ($lastdir == 2) continue;

				$found = true;
				break;
			}
			
			if ($lastdir == 1) continue;
						
			$rowgeo = $q->row();					
			$found = true;
			
			break;
		}
		
		printf("----- lama check area: %d second\r\n", mktime()-$checkarea);
		
		if (! $found) 
		{
			unset($update);
			
			$t = dbmaketime($rowgps[count($rowgps)-1]->gps_time);
			$update['geofence_lastchecked'] = date("Y-m-d H:i:s", $t+7*3600);
			
			$this->db->where("geoalert_id", $geoalert_id);
			$this->db->update("geofence_alert", $update);			
			
			if ($lastdir == 1)
			{				
				printf("----- posisi kendaraan masih didalam area geofence\r\n");
				return;				
			}
			
			printf("----- posisi kendaraan masih di luar area geofence\r\n");
			return;
		}
		
		if ($lastdir != 2)
		{
			$this->db->where("geofence_status", 1);
			$this->db->where("geofence_id", $lastgeofence);
			$q = $this->db->get("geofence");
			
			if ($q->num_rows() > 0)
			{
				$rowgeo = $q->row();
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert($vehicle, 2, $gps, $rowgeo, $t);
			}
			else
			{
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert($vehicle, 2, $gps, FALSE, $t);
			}
			
			return;
		}
		$t = $rowgps[count($rowgps)-1]->gps_time;
		$this->addgeofencealert($vehicle, 1, $gps, $rowgeo, $t);
	}	
	
	function addgeofencealert($vehicle, $direction, $gps, $geofence, $t)
	{
		unset($insert);
			
		$insert['geoalert_vehicle'] = $vehicle->vehicle_device;
		$insert['geoalert_direction'] = $direction;
		$insert['geoalert_time'] = date("Y-m-d H:i:s", dbmaketime($gps->gps_time)+7*3600);
		$insert['geoalert_lat'] = $gps->lat;
		$insert['geoalert_lng'] = $gps->lng;
		$insert['geoalert_geofence'] = ($geofence === FALSE) ? 0 : $geofence->geofence_id;
		$insert['geofence_created'] = date("Y-m-d H:i:s");
		$insert['geofence_lastchecked'] = date("Y-m-d H:i:s", dbmaketime($t)+7*3600);
			
		$this->db->insert("geofence_alert", $insert);		
		
		if ($vehicle->user_alert_geo_email == 2)
		{
			print("----- User Tidak Inginkan Terima Email Alert \r\n");
			return;
		}
		
		$hp = valid_mobiles($vehicle->user_mobile);
		
		$t = dbmaketime($gps->gps_time)+7*3600;
			
		if ($direction == 2)
		{
			if ($geofence === FALSE)
			{
				$geofence_name = "geofence";				
			}
			else
			{
				$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			}
			
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		else
		{
			$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		
		$emails = get_valid_emails($vehicle->user_mail);
		if (is_array($emails) && count($emails))
		{
			foreach($emails as $email)
			{
				unset($mail);
			
				$mail['subject'] = sprintf("Geofence Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = $contentmail;
				$mail['dest'] = $email; 
				$mail['bcc'] = "owner@adilahsoft.com,report.lacakmobil@gmail.com";
				if ($vehicle->user_agent == 3)
				{
					$mail['sender'] = "support@gpsandalas.com";
				}
				else
				{
					$mail['sender'] = "support@lacak-mobil.com";
				}
			
				printf("----- sending email to %s %s %s\r\n", $mail['dest'], $mail['subject'], $mail['message']);
			
				lacakmobilmail($mail);
			}
		}

		if (! isON($vehicle->user_sms_notifikasi, 15))
		{
			print("----- User Disable SMS Notifikasi\r\n");
			return;
		}
		

		if ($hp === FALSE) 
		{
			print("----- Invalid user mobile\r\n");
			return;
		}
		
		if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}

		$this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}
		
		if ($vehicle->user_alert_geo_sms == 2)
		{
			print("----- User Tidak Inginkan Terima SMS Alert \r\n");
			return;
		}
		
		print("----- SMS DI DISABLE SEMENTARA WAKTU \r\n");
		return;
		
		 
		$params['device'] = "alert";
		$params['dest'] = $hp;	
		$xml = $this->load->view("sms/send", $params, true);
		$this->smsmodel->sendsms($xml);
		
		
	}
	
	function geofencealert_release()
	{
		$update['config_value'] = 0;
		
		$this->db->where("config_name", "geofencealertprocessing");
		$this->db->update("config", $update);		
	}
	
	
	function geofencealert_tcontinent()
	{ 
		$this->db->where("config_name", "geofencealertprocessing_tcontinent");
		$q = $this->db->get("config");
		
		if ($q->num_rows())
		{
			$row = $q->row();
			
			if ($row->config_value == 1) 
			{				
				$t = dbmaketime($row->config_lastmodified);
				$delta = mktime()-$t;
				
				if ($delta > 3600)
				{
					unset($update);
					
					$update['config_value'] = 0;
					
					$this->db->where("config_name", "geofencealertprocessing_tcontinent");
					$this->db->update("config", $update);	

					print("masih proses\r\n");
					return;				
				}
				
				print("masih proses\r\n");		
				return;						
			}
			
			unset($update);
			
			$update['config_value'] = 1;
			
			$this->db->where("config_name", "geofencealertprocessing_tcontinent");
			$this->db->update("config", $update);
		}
		else
		{
			unset($insert);
			
			$insert['config_name'] = "geofencealertprocessing_tcontinent";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}

		$this->db->distinct();
		$this->db->select("user.*, vehicle.*");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 >=",date("Ymd"));
		$this->db->where("user_id",1488); //Croscheck Trans Continent
		$this->db->join("vehicle", "vehicle_device = geofence_vehicle");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->where("geofence_status", 1);
		$q = $this->db->get("geofence");
		
		if ($q->num_rows() == 0)
		{
			echo "tidak ada user yang setting geofence\r\n";
			$this->geofencealert_release_tcontinent();
			return;
		}				
		
		$rows = $q->result();
		$i = 0;
		foreach($rows as $row)
		{			
			/* $jsonws = json_decode($row->vehicle_info);
			if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $row->vehicle_no);
				continue;
			}			 */		
			
			printf("\r\n\r\n[%s] %02d %s %s\r\n", date("Y-m-d H:i:s"), $i, $row->vehicle_device, $row->vehicle_no);

			$this->dogeofencealert_tcontinent($row);
			$i++;
		}


		$this->geofencealert_release_tcontinent();
	}
	
	function dogeofencealert_tcontinent($vehicle)
	{
		// ambil user yg setting geofence
			
		$devices = explode("@", $vehicle->vehicle_device);
		if (count($devices) < 2) 
		{
			printf("----- Invalid device\r\n");
			return;
		}
			
		// ambil data terakhir alert geofence
			
		$this->db->limit(1);
		$this->db->order_by("geofence_lastchecked", "desc");		
		$this->db->where("geoalert_vehicle", $vehicle->vehicle_device);
		$q = $this->db->get("geofence_alert");
		if ($q->num_rows() == 0)
		{
			$lastdir = -1;
			$lastchecked = mktime()-7*3600;
			$lastgeofence = -1;
		}
		else
		{
			$row = $q->row();				
			$lastdir = $row->geoalert_direction;
			$lastchecked = dbmaketime($row->geofence_lastchecked);
			$geoalert_id = $row->geoalert_id;
			$lastgeofence = $row->geoalert_geofence;
			
			if ($lastchecked < (mktime()-2*24*3600-7*3600))
			{
				$lastchecked = mktime()-2*24*3600-7*3600;
			}
		}
		
		$me = date("Y-m-d H:i:s", $lastchecked-7*3600);
		printf("Lastchecked : %s\r\n",$me);
		// ambil data gps
		$json = json_decode($vehicle->vehicle_info);
		if ($vehicle->vehicle_info)
		{
			
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					echo "database: ".$database."\n";
					$tablegps = $this->config->item("external_gpstable");					
					$tablegpshist = $this->config->item("external_gpstable_history");
										
					$this->db = $this->load->database($database, TRUE);
				}
			}

			//Jika WebSocket
			if (isset($json->vehicle_ws)) 
			{	
				$database = "gpshistory2";
				$tablegps = strtolower($devices[0]."@".$devices[1]."_gps");
				$tablegpshist = strtolower($devices[0]."@".$devices[1]."_info");
				$this->db = $this->load->database($database, TRUE);	
			}
		}			

		if (! isset($tablegps))
		{		
			$table_hist = $this->config->item("table_hist");	
						
			$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
			$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
		}
		
		if (! $tablegps) 
		{
			printf("----- tabel %s tidak ada\r\n", $tablegps);
			$this->db->cache_delete_all();
			return;
		}
		
		$getgpsstart = mktime();			
		$tyesterday = mktime(-7, 59, 59, date('n', $getgpsstart), date('j', $getgpsstart), date('Y', $getgpsstart));		
		
		if ($lastchecked < $tyesterday && (!isset($json->vehicle_ws)))
		{
			printf("----- check to history\n");
			
			$sql = sprintf("SELECT * FROM (SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s') UNION SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s'))  tbl1 ORDER BY gps_time ASC "
				, $this->db->dbprefix
				, $tablegps
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)
				, $this->db->dbprefix
				, $tablegpshist
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)

			);
			$q = $this->db->query($sql);
		}
		elseif($lastchecked < $tyesterday && (isset($json->vehicle_ws)))
		{
			printf("----- Websocket vehicle check to history\n");
			
			$this->db = $this->load->database("gpshistory",true);
			$this->db->order_by("gps_time", "asc");					
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);	
		}
		else
		{	
			if (!isset($json->vehicle_ws))
			{
				printf("----- check to current\n");
				$this->db->order_by("gps_time", "asc");					
				$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$q = $this->db->get($tablegps);			
			}
			else
			{
				printf("----- Ambil Dari Database TMP Socket \n");
				$this->db2 = $this->load->database("gpshistory2",true);
				$this->db2->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
				$this->db2->where("gps_name", $devices[0]);
				$this->db2->where("gps_host", $devices[1]);
				$q = $this->db2->get($tablegps);			
			}
		}
		
		if ($q->num_rows() == 0 && (isset($json->vehicle_ws)))
		{
			printf("----- Ambil Dari Database TMP Socket \n");
			$this->db2 = $this->load->database("gpshistory2",true);
			$this->db2->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db2->where("gps_name", $devices[0]);
			$this->db2->where("gps_host", $devices[1]);
			$q = $this->db2->get($tablegps);			
		}
			
		if ($q->num_rows() == 0) 
		{
			$this->db = $this->load->database("master", TRUE);
			
			printf("----- tidak ada sejak %s\r\n", date("d/m/Y H:i:s", $lastchecked));
			return;
		}
			
		$rowgps = $q->result();
		
		$this->db = $this->load->database("master", TRUE);
		
		printf("----- lama ambil data gps (%d): %d second\r\n", count($rowgps), mktime()-$getgpsstart);
			
		$checkarea = mktime();	
		$found = false;		
		
		for($i=0; $i < count($rowgps); $i++)
		{	
			$lat = getLatitude($rowgps[$i]->gps_latitude, $rowgps[$i]->gps_ns);
			$lng = getLongitude($rowgps[$i]->gps_longitude, $rowgps[$i]->gps_ew);
			
			$gps = $rowgps[$i];
			$gps->lat = $lat;
			$gps->lng = $lng;
				
			$sql = sprintf("
					SELECT 	* 
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_vehicle = '%s' )
							AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle->vehicle_device);

			$q = $this->db->query($sql);
			
			if ($q->num_rows() == 0)
			{
				if ($lastdir == 2) continue;

				$found = true;
				break;
			}
			
			if ($lastdir == 1) continue;
						
			$rowgeo = $q->row();					
			$found = true;
			
			break;
		}
		
		printf("----- lama check area: %d second\r\n", mktime()-$checkarea);
		
		if (! $found) 
		{
			unset($update);
			
			$t = dbmaketime($rowgps[count($rowgps)-1]->gps_time);
			$update['geofence_lastchecked'] = date("Y-m-d H:i:s", $t+7*3600);
			
			$this->db->where("geoalert_id", $geoalert_id);
			$this->db->update("geofence_alert", $update);			
			
			if ($lastdir == 1)
			{				
				printf("----- posisi kendaraan masih didalam area geofence\r\n");
				return;				
			}
			
			printf("----- posisi kendaraan masih di luar area geofence\r\n");
			return;
		}
		
		if ($lastdir != 2)
		{
			$this->db->where("geofence_status", 1);
			$this->db->where("geofence_id", $lastgeofence);
			$q = $this->db->get("geofence");
			
			if ($q->num_rows() > 0)
			{
				$rowgeo = $q->row();
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert_tcontinent($vehicle, 2, $gps, $rowgeo, $t);
			}
			else
			{
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert_tcontinent($vehicle, 2, $gps, FALSE, $t);
			}
			
			return;
		}
		$t = $rowgps[count($rowgps)-1]->gps_time;
		$this->addgeofencealert_tcontinent($vehicle, 1, $gps, $rowgeo, $t);
	}	
	
	function addgeofencealert_tcontinent($vehicle, $direction, $gps, $geofence, $t)
	{
		unset($insert);
			
		$insert['geoalert_vehicle'] = $vehicle->vehicle_device;
		$insert['geoalert_direction'] = $direction;
		$insert['geoalert_time'] = date("Y-m-d H:i:s", dbmaketime($gps->gps_time)+7*3600);
		$insert['geoalert_lat'] = $gps->lat;
		$insert['geoalert_lng'] = $gps->lng;
		$insert['geoalert_geofence'] = ($geofence === FALSE) ? 0 : $geofence->geofence_id;
		$insert['geofence_created'] = date("Y-m-d H:i:s");
		$insert['geofence_lastchecked'] = date("Y-m-d H:i:s", dbmaketime($t)+7*3600);
			
		$this->db->insert("geofence_alert", $insert);		
		
		if ($vehicle->user_alert_geo_email == 2)
		{
			print("----- User Tidak Inginkan Terima Email Alert \r\n");
			return;
		}
		
		$hp = valid_mobiles($vehicle->user_mobile);
		
		$t = dbmaketime($gps->gps_time)+7*3600;
			
		if ($direction == 2)
		{
			if ($geofence === FALSE)
			{
				$geofence_name = "geofence";				
			}
			else
			{
				$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			}
			
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		else
		{
			$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		
		$emails = get_valid_emails($vehicle->user_mail);
		if (is_array($emails) && count($emails))
		{
			foreach($emails as $email)
			{
				unset($mail);
			
				$mail['subject'] = sprintf("Geofence Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = $contentmail;
				$mail['dest'] = $email; 
				$mail['bcc'] = "owner@adilahsoft.com,report.lacakmobil@gmail.com";
				if ($vehicle->user_agent == 3)
				{
					$mail['sender'] = "support@gpsandalas.com";
				}
				else
				{
					$mail['sender'] = "support@lacak-mobil.com";
				}
			
				printf("----- sending email to %s %s %s\r\n", $mail['dest'], $mail['subject'], $mail['message']);
			
				lacakmobilmail($mail);
			}
		}

		if (! isON($vehicle->user_sms_notifikasi, 15))
		{
			print("----- User Disable SMS Notifikasi\r\n");
			return;
		}
		

		if ($hp === FALSE) 
		{
			print("----- Invalid user mobile\r\n");
			return;
		}
		
		if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}

		$this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}
		
		if ($vehicle->user_alert_geo_sms == 2)
		{
			print("----- User Tidak Inginkan Terima SMS Alert \r\n");
			return;
		}
		
		print("----- SMS DI DISABLE SEMENTARA WAKTU \r\n");
		return;
		
		 
		$params['device'] = "alert";
		$params['dest'] = $hp;	
		$xml = $this->load->view("sms/send", $params, true);
		$this->smsmodel->sendsms($xml);
		
		
	}
	
	function geofencealert_release_tcontinent()
	{
		$update['config_value'] = 0;
		
		$this->db->where("config_name", "geofencealertprocessing_tcontinent");
		$this->db->update("config", $update);		
	}
	
	function geofencealert_tupperware()
	{ 
		$this->db->where("config_name", "geofencealertprocessing_tupperware");
		$q = $this->db->get("config");
		
		if ($q->num_rows())
		{
			$row = $q->row();
			
			if ($row->config_value == 1) 
			{				
				$t = dbmaketime($row->config_lastmodified);
				$delta = mktime()-$t;
				
				if ($delta > 3600)
				{
					unset($update);
					
					$update['config_value'] = 0;
					
					$this->db->where("config_name", "geofencealertprocessing_tupperware");
					$this->db->update("config", $update);	

					print("masih proses\r\n");
					return;				
				}
				
				print("masih proses\r\n");		
				return;						
			}
			
			unset($update);
			
			$update['config_value'] = 1;
			
			$this->db->where("config_name", "geofencealertprocessing_tupperware");
			$this->db->update("config", $update);
		}
		else
		{
			unset($insert);
			
			$insert['config_name'] = "geofencealertprocessing_tupperware";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}

		$this->db->distinct();
		$this->db->select("user.*, vehicle.*");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 >=",date("Ymd"));
		$this->db->where("user_company >", 0);
		$this->db->where("user_trans_tupper",1); //User Tupperware tidak ikut cron ini
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			echo "tidak ada user yang setting geofence\r\n";
			$this->geofencealert_release_tupperware();
			return;
		}				
		
		$rows = $q->result();
		
		$i = 0;
		foreach($rows as $row)
		{			
			printf("\r\n\r\n[%s] %02d %s %s\r\n", date("Y-m-d H:i:s"), $i, $row->vehicle_device, $row->vehicle_no);

			$this->dogeofencealert_tupperware($row);
			$i++;
		}

		$this->geofencealert_release_tupperware();
	}
	
	function dogeofencealert_tupperware($vehicle)
	{
		// ambil user yg setting geofence
		$devices = explode("@", $vehicle->vehicle_device);
		if (count($devices) < 2) 
		{
			printf("----- Invalid device\r\n");
			return;
		}
			
		// ambil data terakhir alert geofence
		$this->db->limit(1);
		$this->db->order_by("geofence_lastchecked", "desc");		
		$this->db->where("geoalert_vehicle", $vehicle->vehicle_device);
		$q = $this->db->get("geofence_alert");
		if ($q->num_rows() == 0)
		{
			$lastdir = -1;
			$lastchecked = mktime()-7*3600;
			$lastgeofence = -1;
		}
		else
		{
			$row = $q->row();				
			$lastdir = $row->geoalert_direction;
			$lastchecked = dbmaketime($row->geofence_lastchecked);
			$geoalert_id = $row->geoalert_id;
			$lastgeofence = $row->geoalert_geofence;
			
			if ($lastchecked < (mktime()-2*24*3600-7*3600))
			{
				$lastchecked = mktime()-2*24*3600-7*3600;
			}
		}

		// ambil data gps
		$json = json_decode($vehicle->vehicle_info);
		if ($vehicle->vehicle_info)
		{
			
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					echo "database: ".$database."\n";
					$tablegps = $this->config->item("external_gpstable");					
					$tablegpshist = $this->config->item("external_gpstable_history");
										
					$this->db = $this->load->database($database, TRUE);
				}
			}

			//Jika WebSocket
			if (isset($json->vehicle_ws)) 
			{
				$database = "gpshistory2";
				$tablegps = strtolower($devices[0]."@".$devices[1]."_gps");
				$tablegpshist = strtolower($devices[0]."@".$devices[1]."_info");
				$this->db = $this->load->database($database, TRUE);	
			} 			
		}			

		if (! isset($tablegps))
		{		
			$table_hist = $this->config->item("table_hist");	
						
			$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
			$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
		}
		
		if (! $tablegps) 
		{
			printf("----- tabel %s tidak ada\r\n", $tablegps);
			return;
		}
		
		$getgpsstart = mktime();			
		$tyesterday = mktime(-7, 59, 59, date('n', $getgpsstart), date('j', $getgpsstart), date('Y', $getgpsstart));		
		
		if ($lastchecked < $tyesterday && (!isset($json->vehicle_ws)))
		{
			printf("----- check to history\n");
			
			$sql = sprintf("SELECT * FROM (SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s') UNION SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s'))  tbl1 ORDER BY gps_time ASC "
				, $this->db->dbprefix
				, $tablegps
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)
				, $this->db->dbprefix
				, $tablegpshist
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)

			);
			$q = $this->db->query($sql);
		}
		elseif($lastchecked < $tyesterday && (isset($json->vehicle_ws)))
		{
			printf("----- Websocket vehicle check to current\n");
			
			$this->db->order_by("gps_time", "asc");					
			//$this->db->where("gps_time >", $lastchecked-7*3600);
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);	
		}
		else
		{	
			printf("----- check to current\n");
			
			$this->db->order_by("gps_time", "asc");					
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);			
		}
		
		if ($q->num_rows() == 0 && (isset($json->vehicle_ws)))
		{
			$this->db2 = $this->load->database("gpshistory2",true);
			$this->db2->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db2->where("gps_name", $devices[0]);
			$this->db2->where("gps_host", $devices[1]);
			$q = $this->db2->get($tablegps);			
			
		}
			
		if ($q->num_rows() == 0) 
		{
			$this->db = $this->load->database("master", TRUE);
			
			printf("----- tidak ada sejak %s\r\n", date("d/m/Y H:i:s", $lastchecked));
			return;
		}
			
		$rowgps = $q->result();
		
		$this->db = $this->load->database("master", TRUE);
		
		printf("----- lama ambil data gps (%d): %d second\r\n", count($rowgps), mktime()-$getgpsstart);
			
		$checkarea = mktime();	
		$found = false;		
		
		for($i=0; $i < count($rowgps); $i++)
		{	
			$lat = getLatitude($rowgps[$i]->gps_latitude, $rowgps[$i]->gps_ns);
			$lng = getLongitude($rowgps[$i]->gps_longitude, $rowgps[$i]->gps_ew);
			
			$gps = $rowgps[$i];
			$gps->lat = $lat;
			$gps->lng = $lng;
				
			$sql = sprintf("
					SELECT 	* 
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_user = 1493 )
							AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle->vehicle_device);

			$q = $this->db->query($sql);
			
			if ($q->num_rows() == 0)
			{
				if ($lastdir == 2) continue;

				$found = true;
				break;
			}
			
			if ($lastdir == 1) continue;
						
			$rowgeo = $q->row();					
			$found = true;
			
			break;
		}
		
		printf("----- lama check area: %d second\r\n", mktime()-$checkarea);
		
		if (! $found) 
		{
			unset($update);
			
			$t = dbmaketime($rowgps[count($rowgps)-1]->gps_time);
			$update['geofence_lastchecked'] = date("Y-m-d H:i:s", $t+7*3600);
			
			$this->db->where("geoalert_id", $geoalert_id);
			$this->db->update("geofence_alert", $update);			
			
			if ($lastdir == 1)
			{				
				printf("----- posisi kendaraan masih didalam area geofence\r\n");
				return;				
			}
			
			printf("----- posisi kendaraan masih di luar area geofence\r\n");
			return;
		}
		
		if ($lastdir != 2)
		{
			$this->db->where("geofence_status", 1);
			$this->db->where("geofence_id", $lastgeofence);
			$q = $this->db->get("geofence");
			
			if ($q->num_rows() > 0)
			{
				$rowgeo = $q->row();
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert_tupperware($vehicle, 2, $gps, $rowgeo, $t);
			}
			else
			{
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert_tupperware($vehicle, 2, $gps, FALSE, $t);
			}
			
			return;
		}
		$t = $rowgps[count($rowgps)-1]->gps_time;
		$this->addgeofencealert_tupperware($vehicle, 1, $gps, $rowgeo, $t);
	}	

	//Cron Geofence geofencealert_release_indahkiat Khusus Indah kiat
	function geofencealert_release_indahkiat()
	{
		$this->db = $this->load->database("master_indahkiat", true);
		$update['config_value'] = 0;
		
		$this->db->where("config_name", "geofencealertprocessing");
		$this->db->update("config", $update);		
	}


	//Cron Geofence addgeofencealert_indahkiat Khusus indahkiat
	function addgeofencealert_indahkiat($vehicle, $direction, $gps, $geofence)
	{
		$this->db = $this->load->database("master_indahkiat", true);
		unset($insert);
			
		$insert['geoalert_vehicle'] = $vehicle->vehicle_device;
		$insert['geoalert_direction'] = $direction;
		$insert['geoalert_time'] = date("Y-m-d H:i:s", dbmaketime($gps->gps_time)+7*3600);
		$insert['geoalert_lat'] = $gps->lat;
		$insert['geoalert_lng'] = $gps->lng;
		$insert['geoalert_geofence'] = ($geofence === FALSE) ? 0 : $geofence->geofence_id;
		$insert['geofence_created'] = date("Y-m-d H:i:s");
		$insert['geofence_lastchecked'] = $insert['geoalert_time'];
			
		$this->db->insert("geofence_alert", $insert);		
		
		$hp = valid_mobiles($vehicle->user_mobile);
		
		$t = dbmaketime($gps->gps_time)+7*3600;
			
		if ($direction == 2)
		{
			if ($geofence === FALSE)
			{
				$geofence_name = "geofence";				
			}
			else
			{
				$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			}
			
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		else
		{
			$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		
		$emails = get_valid_emails($vehicle->user_mail);
		if (is_array($emails) && count($emails))
		{
			foreach($emails as $email)
			{
				unset($mail);
			
				$mail['subject'] = sprintf("Geofence Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = $contentmail;
				$mail['dest'] = $email; 
				$mail['bcc'] = "owner@adilahsoft.com,report.lacakmobil@gmail.com";
				if ($vehicle->user_agent == 3)
				{
					$mail['sender'] = "support@gpsandalas.com";
				}
				else
				{
					$mail['sender'] = "support@lacak-mobil.com";
				}
			
				printf("----- sending email to %s %s %s\r\n", $mail['dest'], $mail['subject'], $mail['message']);
			
				lacakmobilmail($mail);			
			}
		}

		if (! isON($vehicle->user_sms_notifikasi, 15))
		{
			print("----- User Disable SMS Notifikasi\r\n");
			return;
		}
		

		if ($hp === FALSE) 
		{
			print("----- Invalid user mobile\r\n");
			return;
		}

		if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}

		$this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}
			
		$params['device'] = "alat";
		$params['dest'] = $hp;	
		$xml = $this->load->view("sms/send", $params, true);

		$this->smsmodel->sendsms($xml);
	}
	
	//Cron Geofence dogeofencealert Khusus Indahkiat
	function dogeofencealert_indahkiat($host="", $name='')
	{
		// hindari overlapping cron
		$this->db = $this->load->database("master_indahkiat", true);
		
		$this->db->where("config_name", "geofencealertprocessing");
		$q = $this->db->get("config");
		
		if ($q->num_rows())
		{
			$row = $q->row();
			
			if ($row->config_value == 1) 
			{				
				$t = dbmaketime($row->config_lastmodified);
				$delta = mktime()-$t;
				
				if ($delta > 3600)
				{
					unset($update);
					
					$update['config_value'] = 0;
					
					$this->db->where("config_name", "geofencealertprocessing");
					$this->db->update("config", $update);					
				}
				
				return;						
			}
			
			unset($update);
			
			$update['config_value'] = 1;
			
			$this->db->where("config_name", "geofencealertprocessing");
			$this->db->update("config", $update);
		}
		else
		{
			unset($insert);
			
			$insert['config_name'] = "geofencealertprocessing";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}
		
		// proses hanya 1 kendaraan, optimize consume cpu
				
		$this->db->where("config_name", "geofencealert");
		$q = $this->db->get("config");
		
		if ($q->num_rows() == 0)
		{
			$offset = 0;

			unset($insert);
			
			$insert['config_name'] = "geofencealert";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}
		else
		{
			$row = $q->row();
			$offset = $row->config_value;

			unset($update);
			
			$update['config_value'] = $offset+1;
			
			$this->db->where("config_name", "geofencealert");
			$this->db->update("config", $update);
		}
		
		// ambil user yg setting geofence
		
		$this->db->distinct();
		if ($host && $name)
		{
			$this->db->where("vehicle_device", sprintf('%s@%s', $host, $name));
		}

		$this->db->select("user.*, vehicle.*");
		$this->db->where("vehicle_status", 1);
		$this->db->join("vehicle", "vehicle_device = geofence_vehicle");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->where("geofence_status", 1);
		$q = $this->db->get("geofence");
		
		if ($q->num_rows() == 0)
		{
			echo "tidak ada user yang setting geofence\r\n";
			$this->geofencealert_release_indahkiat();
			return;
		}				
		
		$rows = $q->result();				
		
		if ($offset >= $q->num_rows())
		{
			unset($update);
			
			$update['config_value'] = 1;
			
			$this->db->where("config_name", "geofencealert");
			$this->db->update("config", $update);			
			
			$offset = 0;
		}		

		if ($host && $name)
		{
			$offset = 0;
		}
				
		$vehicle = $rows[$offset];

		$jsonws = json_decode($vehicle->vehicle_info);
		if (isset($jsonws->vehicle_ws)) 
		{
			printf("skip web socket: %s", $vehicle->vehicle_no);
			return;
		}
		printf("\r\n\r\n[%s] %02d %s %s\r\n", date("Y-m-d H:i:s"), $offset+1, $vehicle->vehicle_device, $vehicle->vehicle_no);
			
		$devices = explode("@", $vehicle->vehicle_device);
		if (count($devices) < 2) 
		{
			printf("----- Invalid device\r\n");
			$this->geofencealert_release_indahkiat();
			return;
		}
			
		// ambil data terakhir alert geofence
			
		$this->db->limit(1);
		$this->db->order_by("geofence_lastchecked", "desc");		
		$this->db->where("geoalert_vehicle", $vehicle->vehicle_device);
		$q = $this->db->get("geofence_alert");
		if ($q->num_rows() == 0)
		{
			$lastdir = -1;
			$lastchecked = mktime()-7*3600;
			$lastgeofence = -1;
		}
		else
		{
			$row = $q->row();				
			$lastdir = $row->geoalert_direction;
			$lastchecked = dbmaketime($row->geofence_lastchecked);
			$geoalert_id = $row->geoalert_id;
			$lastgeofence = $row->geoalert_geofence;
			
			if ($lastchecked < (mktime()-2*24*3600-7*3600))
			{
				$lastchecked = mktime()-2*24*3600-7*3600;
			}
		}

		// ambil data gps
	
		if ($vehicle->vehicle_info)
		{
			$json = json_decode($vehicle->vehicle_info);
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					echo "database: ".$database."\n";
					$tablegps = $this->config->item("external_gpstable");					
					$tablegpshist = $this->config->item("external_gpstable_history");
										
					$this->db = $this->load->database($database, TRUE);
				}
			}			
		}			

		if (! isset($tablegps))
		{		
			$table_hist = $this->config->item("table_hist");	
						
			$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
			$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
		}
		
		if (! $tablegps) 
		{
			printf("----- tabel %s tidak ada\r\n", $tablegps);
			$this->geofencealert_release_indahkiat();
			return;
		}
		
		
		$getgpsstart = mktime();			
		$tyesterday = mktime(-7, 59, 59, date('n', $getgpsstart), date('j', $getgpsstart), date('Y', $getgpsstart));		
		
		if ($lastchecked < $tyesterday)
		{
			printf("----- check to history\n");
			
			$sql = sprintf("SELECT * FROM (SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s') UNION SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s'))  tbl1 ORDER BY gps_time ASC "
				, $this->db->dbprefix
				, $tablegps
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)
				, $this->db->dbprefix
				, $tablegpshist
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)

			);
			$q = $this->db->query($sql);
		}
		else
		{	
			printf("----- check to current\n");
			
			$this->db->order_by("gps_time", "asc");					
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);			
		}
			
		if ($q->num_rows() == 0) 
		{
			$this->db = $this->load->database("master_indahkiat", TRUE);
			
			printf("----- tidak ada sejak %s\r\n", date("d/m/Y H:i:s", $lastchecked));
			$this->geofencealert_release_indahkiat();
			return;
		}
			
		$rowgps = $q->result();
		
		$this->db = $this->load->database("master_indahkiat", TRUE);
		
		printf("----- lama ambil data gps (%d): %d second\r\n", count($rowgps), mktime()-$getgpsstart);
			
		$checkarea = mktime();	
		$found = false;		
		
		for($i=0; $i < count($rowgps); $i++)
		{	
			$lat = getLatitude($rowgps[$i]->gps_latitude, $rowgps[$i]->gps_ns);
			$lng = getLongitude($rowgps[$i]->gps_longitude, $rowgps[$i]->gps_ew);
			
			$gps = $rowgps[$i];
			$gps->lat = $lat;
			$gps->lng = $lng;
				
			$sql = sprintf("
					SELECT 	* 
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_vehicle = '%s' )
							AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle->vehicle_device);

			$q = $this->db->query($sql);
			
			if ($q->num_rows() == 0)
			{
				if ($lastdir == 2) continue;

				$found = true;
				break;
			}
			
			if ($lastdir == 1) continue;
						
			$rowgeo = $q->row();					
			$found = true;
			
			break;
		}
		
		printf("----- lama check area: %d second\r\n", mktime()-$checkarea);
		
		if (! $found) 
		{
			unset($update);
			
			$t = dbmaketime($rowgps[count($rowgps)-1]->gps_time);
			$update['geofence_lastchecked'] = date("Y-m-d H:i:s", $t+7*3600);
			
			$this->db->where("geoalert_id", $geoalert_id);
			$this->db->update("geofence_alert", $update);			
			
			if ($lastdir == 1)
			{				
				printf("----- posisi kendaraan masih didalam area geofence\r\n");
				$this->geofencealert_release_indahkiat();
				return;				
			}
			
			printf("----- posisi kendaraan masih di luar area geofence\r\n");
			$this->geofencealert_release_indahkiat();
			return;
		}
		
		if ($lastdir != 2)
		{
			$this->db->where("geofence_status", 1);
			$this->db->where("geofence_id", $lastgeofence);
			$q = $this->db->get("geofence");
			
			if ($q->num_rows() > 0)
			{
				$rowgeo = $q->row();
				$this->addgeofencealert_indahkiat($vehicle, 2, $gps, $rowgeo);
			}
			else
			{
				$this->addgeofencealert_indahkiat($vehicle, 2, $gps, FALSE);
			}
			$this->geofencealert_release_indahkiat();			
			
			return;
		}
		
		$this->addgeofencealert_indahkiat($vehicle, 1, $gps, $rowgeo);
		$this->geofencealert_release_indahkiat();			
	}
	
	//Cron Geofence dogeofencealert Khusus KIM
	function dogeofencealert_kim($host="", $name='')
	{
		//hindari overlapping cron
		$this->db = $this->load->database("master_kim", true);
		
		$this->db->where("config_name", "geofencealertprocessing");
		$q = $this->db->get("config");
		
		if ($q->num_rows())
		{
			$row = $q->row();
			
			if ($row->config_value == 1) 
			{				
				$t = dbmaketime($row->config_lastmodified);
				$delta = mktime()-$t;
				
				if ($delta > 3600)
				{
					unset($update);
					
					$update['config_value'] = 0;
					
					$this->db->where("config_name", "geofencealertprocessing");
					$this->db->update("config", $update);					
				}
				
				return;						
			}
			
			unset($update);
			
			$update['config_value'] = 1;
			
			$this->db->where("config_name", "geofencealertprocessing");
			$this->db->update("config", $update);
		}
		else
		{
			unset($insert);
			
			$insert['config_name'] = "geofencealertprocessing";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}
		
		//proses hanya 1 kendaraan, optimize consume cpu
				
		$this->db->where("config_name", "geofencealert");
		$q = $this->db->get("config");
		
		if ($q->num_rows() == 0)
		{
			$offset = 0;

			unset($insert);
			
			$insert['config_name'] = "geofencealert";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}
		else
		{
			$row = $q->row();
			$offset = $row->config_value;

			unset($update);
			
			$update['config_value'] = $offset+1;
			
			$this->db->where("config_name", "geofencealert");
			$this->db->update("config", $update);
		}
		
		// ambil user yg setting geofence
		
		$this->db->distinct();
		if ($host && $name)
		{
			$this->db->where("vehicle_device", sprintf('%s@%s', $host, $name));
		}
		$this->db->select("user.*, vehicle.*");
		$this->db->where("vehicle_status", 1);
		$this->db->join("vehicle", "vehicle_device = geofence_vehicle");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->where("geofence_status", 1);
		$q = $this->db->get("geofence");
		
		if ($q->num_rows() == 0)
		{
			echo "tidak ada user yang setting geofence\r\n";
			$this->geofencealert_release_kim();
			return;
		}				
		
		$rows = $q->result();				
		
		if ($offset >= $q->num_rows())
		{
			unset($update);
			
			$update['config_value'] = 1;
			
			$this->db->where("config_name", "geofencealert");
			$this->db->update("config", $update);			
			
			$offset = 0;
		}		

		if ($host && $name)
		{
			$offset = 0;
		}
				
		$vehicle = $rows[$offset];

		$jsonws = json_decode($vehicle->vehicle_info);
		if (isset($jsonws->vehicle_ws)) 
		{
			printf("skip web socket: %s", $vehicle->vehicle_no);
			return;
		}
		printf("\r\n\r\n[%s] %02d %s %s\r\n", date("Y-m-d H:i:s"), $offset+1, $vehicle->vehicle_device, $vehicle->vehicle_no);
			
		$devices = explode("@", $vehicle->vehicle_device);
		if (count($devices) < 2) 
		{
			printf("----- Invalid device\r\n");
			$this->geofencealert_release_kim();
			return;
		}
			
		// ambil data terakhir alert geofence
			
		$this->db->limit(1);
		$this->db->order_by("geofence_lastchecked", "desc");		
		$this->db->where("geoalert_vehicle", $vehicle->vehicle_device);
		$q = $this->db->get("geofence_alert");
		if ($q->num_rows() == 0)
		{
			$lastdir = -1;
			$lastchecked = mktime()-7*3600;
			$lastgeofence = -1;
		}
		else
		{
			$row = $q->row();				
			$lastdir = $row->geoalert_direction;
			$lastchecked = dbmaketime($row->geofence_lastchecked);
			$geoalert_id = $row->geoalert_id;
			$lastgeofence = $row->geoalert_geofence;
			
			if ($lastchecked < (mktime()-2*24*3600-7*3600))
			{
				$lastchecked = mktime()-2*24*3600-7*3600;
			}
		}

		// ambil data gps
	
		if ($vehicle->vehicle_info)
		{
			$json = json_decode($vehicle->vehicle_info);
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					echo "database: ".$database."\n";
					$tablegps = $this->config->item("external_gpstable");					
					$tablegpshist = $this->config->item("external_gpstable_history");
										
					$this->db = $this->load->database($database, TRUE);
				}
			}			
		}			

		if (! isset($tablegps))
		{		
			$table_hist = $this->config->item("table_hist");	
						
			$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
			$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
		}
		
		if (! $tablegps) 
		{
			printf("----- tabel %s tidak ada\r\n", $tablegps);
			$this->geofencealert_release_kim();
			return;
		}
		
		$getgpsstart = mktime();			
		$tyesterday = mktime(-7, 59, 59, date('n', $getgpsstart), date('j', $getgpsstart), date('Y', $getgpsstart));		
		
		if ($lastchecked < $tyesterday)
		{
			printf("----- check to history\n");
			
			$sql = sprintf("SELECT * FROM (SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s') UNION SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s'))  tbl1 ORDER BY gps_time ASC "
				, $this->db->dbprefix
				, $tablegps
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)
				, $this->db->dbprefix
				, $tablegpshist
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)

			);
			$q = $this->db->query($sql);
		}
		else
		{	
			printf("----- check to current\n");
			
			$this->db->order_by("gps_time", "asc");					
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);			
		}
			
		if ($q->num_rows() == 0) 
		{
			$this->db = $this->load->database("master", TRUE);
			
			printf("----- tidak ada sejak %s\r\n", date("d/m/Y H:i:s", $lastchecked));
			$this->geofencealert_release_kim();
			return;
		}
			
		$rowgps = $q->result();
		
		$this->db = $this->load->database("master", TRUE);
		
		printf("----- lama ambil data gps (%d): %d second\r\n", count($rowgps), mktime()-$getgpsstart);
			
		$checkarea = mktime();	
		$found = false;		
		
		for($i=0; $i < count($rowgps); $i++)
		{	
			$lat = getLatitude($rowgps[$i]->gps_latitude, $rowgps[$i]->gps_ns);
			$lng = getLongitude($rowgps[$i]->gps_longitude, $rowgps[$i]->gps_ew);
			
			$gps = $rowgps[$i];
			$gps->lat = $lat;
			$gps->lng = $lng;
				
			$sql = sprintf("
					SELECT 	* 
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_vehicle = '%s' )
							AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle->vehicle_device);

			$q = $this->db->query($sql);
			
			if ($q->num_rows() == 0)
			{
				if ($lastdir == 2) continue;

				$found = true;
				break;
			}
			
			if ($lastdir == 1) continue;
						
			$rowgeo = $q->row();					
			$found = true;
			
			break;
		}
		
		printf("----- lama check area: %d second\r\n", mktime()-$checkarea);
		
		if (! $found) 
		{
			unset($update);
			
			$t = dbmaketime($rowgps[count($rowgps)-1]->gps_time);
			$update['geofence_lastchecked'] = date("Y-m-d H:i:s", $t+7*3600);
			
			$this->db->where("geoalert_id", $geoalert_id);
			$this->db->update("geofence_alert", $update);			
			
			if ($lastdir == 1)
			{				
				printf("----- posisi kendaraan masih didalam area geofence\r\n");
				$this->geofencealert_release_kim();
				return;				
			}
			
			printf("----- posisi kendaraan masih di luar area geofence\r\n");
			$this->geofencealert_release_kim();
			return;
		}
		
		if ($lastdir != 2)
		{
			$this->db->where("geofence_status", 1);
			$this->db->where("geofence_id", $lastgeofence);
			$q = $this->db->get("geofence");
			
			if ($q->num_rows() > 0)
			{
				$rowgeo = $q->row();
				$this->addgeofencealert_kim($vehicle, 2, $gps, $rowgeo);
			}
			else
			{
				$this->addgeofencealert_kim($vehicle, 2, $gps, FALSE);
			}
			$this->geofencealert_release_kim();			
			
			return;
		}
		
		$this->addgeofencealert_kim($vehicle, 1, $gps, $rowgeo);
		$this->geofencealert_release_kim();			
	}	
	
		function addgeofencealert_tupperware($vehicle, $direction, $gps, $geofence, $t)
	{
		unset($insert);
			
		$insert['geoalert_vehicle'] = $vehicle->vehicle_device;
		$insert['geoalert_direction'] = $direction;
		$insert['geoalert_time'] = date("Y-m-d H:i:s", dbmaketime($gps->gps_time)+7*3600);
		$insert['geoalert_lat'] = $gps->lat;
		$insert['geoalert_lng'] = $gps->lng;
		$insert['geoalert_geofence'] = ($geofence === FALSE) ? 0 : $geofence->geofence_id;
		$insert['geofence_created'] = date("Y-m-d H:i:s");
		$insert['geofence_lastchecked'] = date("Y-m-d H:i:s", dbmaketime($t)+7*3600);
			
		$this->db->insert("geofence_alert", $insert);		
		
		if ($vehicle->user_alert_geo_email == 2)
		{
			print("----- User Tidak Inginkan Terima Email Alert \r\n");
			return;
		}
		
		$hp = valid_mobiles($vehicle->user_mobile);
		
		$t = dbmaketime($gps->gps_time)+7*3600;
			
		if ($direction == 2)
		{
			if ($geofence === FALSE)
			{
				$geofence_name = "geofence";				
			}
			else
			{
				$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			}
			
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		else
		{
			$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		
		$emails = get_valid_emails($vehicle->user_mail);
		if (is_array($emails) && count($emails))
		{
			foreach($emails as $email)
			{
				unset($mail);
			
				$mail['subject'] = sprintf("Geofence Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = $contentmail;
				$mail['dest'] = $email; 
				$mail['bcc'] = "arisisdarwanto@tupperware.com,report.lacakmobil@gmail.com";
				if ($vehicle->user_agent == 3)
				{
					$mail['sender'] = "support@gpsandalas.com";
				}
				else
				{
					$mail['sender'] = "support@lacak-mobil.com";
				}
			
				printf("----- sending email to %s %s %s\r\n", $mail['dest'], $mail['subject'], $mail['message']);
			
				lacakmobilmail($mail);			
			}
		}

		if (! isON($vehicle->user_sms_notifikasi, 15))
		{
			print("----- User Disable SMS Notifikasi\r\n");
			return;
		}
		

		if ($hp === FALSE) 
		{
			print("----- Invalid user mobile\r\n");
			return;
		}
		
		if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}

		$this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}
		
		if ($vehicle->user_alert_geo_sms == 2)
		{
			print("----- User Tidak Inginkan Terima SMS Alert \r\n");
			return;
		}
		
		 
		$params['device'] = "alert";
		$params['dest'] = $hp;	
		$xml = $this->load->view("sms/send", $params, true);
		$this->smsmodel->sendsms($xml);
		
		
	}


	//Cron Geofence addgeofencealert_kim Khusus KIM
	function addgeofencealert_kim($vehicle, $direction, $gps, $geofence)
	{
		$this->db = $this->load->database("master_kim", true);
		unset($insert);
			
		$insert['geoalert_vehicle'] = $vehicle->vehicle_device;
		$insert['geoalert_direction'] = $direction;
		$insert['geoalert_time'] = date("Y-m-d H:i:s", dbmaketime($gps->gps_time)+7*3600);
		$insert['geoalert_lat'] = $gps->lat;
		$insert['geoalert_lng'] = $gps->lng;
		$insert['geoalert_geofence'] = ($geofence === FALSE) ? 0 : $geofence->geofence_id;
		$insert['geofence_created'] = date("Y-m-d H:i:s");
		$insert['geofence_lastchecked'] = $insert['geoalert_time'];
			
		$this->db->insert("geofence_alert", $insert);		
		
		$hp = valid_mobiles($vehicle->user_mobile);
		
		$t = dbmaketime($gps->gps_time)+7*3600;
			
		if ($direction == 2)
		{
			if ($geofence === FALSE)
			{
				$geofence_name = "geofence";				
			}
			else
			{
				$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			}
			
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		else
		{
			$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		
		$emails = get_valid_emails($vehicle->user_mail);
		if (is_array($emails) && count($emails))
		{
			foreach($emails as $email)
			{
				unset($mail);
			
				$mail['subject'] = sprintf("Geofence Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = $contentmail;
				$mail['dest'] = $email; 
				$mail['bcc'] = "owner@adilahsoft.com,report.lacakmobil@gmail.com";
				if ($vehicle->user_agent == 3)
				{
					$mail['sender'] = "support@gpsandalas.com";
				}
				else
				{
					$mail['sender'] = "support@lacak-mobil.com";
				}
			
				printf("----- sending email to %s %s %s\r\n", $mail['dest'], $mail['subject'], $mail['message']);
			
				lacakmobilmail($mail);			
			}
		}

		if (! isON($vehicle->user_sms_notifikasi, 15))
		{
			print("----- User Disable SMS Notifikasi\r\n");
			return;
		}
		

		if ($hp === FALSE) 
		{
			print("----- Invalid user mobile\r\n");
			return;
		}

		if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}

		$this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}
			
		$params['device'] = "alat";
		$params['dest'] = $hp;	
		$xml = $this->load->view("sms/send", $params, true);

		$this->smsmodel->sendsms($xml);
	}
	
	
	function geofencealert_release_tupperware()
	{
		$update['config_value'] = 0;
		
		$this->db->where("config_name", "geofencealertprocessing_tupperware");
		$this->db->update("config", $update);		
	}


	//Cron Geofence geofencealert_release_kim Khusus KIM
	function geofencealert_release_kim()
	{
		$this->db = $this->load->database("master_kim", true);
		$update['config_value'] = 0;
		
		$this->db->where("config_name", "geofencealertprocessing");
		$this->db->update("config", $update);		
	}
	
	
	function speedalert()
	{
		
		//date_default_timezone_set('Asia/Jakarta');
		
		$this->db->where("config_name", "lastspeed");
		$q = $this->db->get("config");
		
		$rowlast = $q->row();
		$lastrunning = dbmaketime($rowlast->config_value)-7*3600;
		
		$this->db->order_by("logs_created", "asc");
		$this->db->where("logs_type", "speedalert");
		$q = $this->db->get("logs");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$speedalert[$rows[$i]->logs_content] = dbmaketime($rows[$i]->logs_created);
		}

		$this->db->where("vehicle_maxspeed >", 0);
		$this->db->where("vehicle_status", 1);
		$this->db->where("user_company >", 0);
		//gt06 kecuali 
		$this->db->where("vehicle_type <>", "GT06");
		$this->db->where("vehicle_user_id <>", "1032"); //balrich.logistics tidak ikut cron ini
		$this->db->where("vehicle_user_id <>", "2331"); //it.balrich tidak ikut cron ini
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();

		unset($update);
		$update['config_value'] = date("Y-m-d H:i:s", mktime());
		$this->db->where("config_name", "lastspeed");
		$this->db->update("config", $update);

		for($i=0; $i < count($rows); $i++)
		{
			$devices = explode("@", $rows[$i]->vehicle_device);
			$jsonws = json_decode($rows[$i]->vehicle_info);
			/* if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $rows[$i]->vehicle_no);
				continue;
			} */
			
			printf("[%s] %04d %s %s %skph...\r\n", date("Ymd H:i:s"), $i+1, $rows[$i]->user_login, $rows[$i]->vehicle_no, $rows[$i]->vehicle_maxspeed);
			
			$hp = valid_mobiles($rows[$i]->user_mobile);			

			if ($rows[$i]->vehicle_info)
			{
				$json = json_decode($rows[$i]->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$tablegps = $this->config->item("external_gpstable");					
						$this->db = $this->load->database($database, TRUE);
					}
				}
	
			}

			//Jika WebSocket
				if (isset($jsonws->vehicle_ws)) 
				{
					$database = "gpshistory";
					if ($devices[1] == "T5")
					{
						$tablegps = $devices[0]."@t5_gps";
						$tablegpshist = $devices[0]."@t5_gps";
						$this->db = $this->load->database($database, TRUE);						
					}
				} 		
			
			if (! isset($tablegps))
			{					
				$tablegps = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);
				$devices = explode("@", $rows[$i]->vehicle_device);
			}
			
			$this->db->order_by("gps_time", "desc");				
			$this->db->where("gps_time >=", date("Y-m-d H:i:s", $lastrunning-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$this->db->where("gps_speed*1.852 >=", $rows[$i]->vehicle_maxspeed, false);
			
			$q = $this->db->get($tablegps);
			
			
			
			if ($q->num_rows() > 0) 
			{				
				$rowgps = $q->result();
				$this->db = $this->load->database("master", TRUE);
				for($j=0; $j < count($rowgps); $j++)
				{
					$row = $rowgps[$j];
					
					$t = dbmaketime($row->gps_time)+7*3600;
					$device = sprintf("%s@%s", $row->gps_name, $row->gps_host);
					$isspeedalert = false;
					
					if (! isset($speedalert[$device]))
					{
						$speedalert[$device] = $t;
						$isspeedalert = true;
					}
					else
					{
						$delta = $t - $speedalert[$device];
						$isspeedalert = $delta > 3600;
					}
					
					
					if ($isspeedalert)
					{
						// send sms max alert
						
						unset($insert);
						$myposition = $this->getPosition($row->gps_longitude, $row->gps_ew, $row->gps_latitude, $row->gps_ns);
						
						$insert['speed_alert_device'] = $rows[$i]->vehicle_device;
						$insert['speed_alert_time'] = date("Y-m-d H:i:s", $t);
						$insert['speed_alert_speed'] = $row->gps_speed*1.852;
						$insert['speed_alert_max'] = $rows[$i]->vehicle_maxspeed;
						$insert['speed_alert_created'] = date("Y-m-d H:i:s");
						
						$this->db->insert("speed_alert", $insert);
						
						unset($insert);
						
						$insert['logs_type'] = "speedalert";
						$insert['logs_created'] = date("Y-m-d H:i:s");
						$insert['logs_content '] = $device;
						
						$this->db->insert("logs", $insert);
						
						$emails = get_valid_emails($rows[$i]->user_mail);
						
						if ($rows[$i]->user_alert_speed_email == 1)
						{
							foreach($emails as $email)
							{
								unset($mail);
							
								$myspeed = $row->gps_speed*1.852;
								$exspeed = explode(".",$myspeed);
							
								if (isset($exspeed[0]))
								{
									$fixspeed = $exspeed[0];
								}
								else
								{
									$fixspeed = $myspeed;
								}
							
								$mymessage = sprintf($this->config->item("MAIL_ALERT_MAX_SPEED"), date("d/m/Y H:i:s", $t), $rows[$i]->vehicle_no, $fixspeed);
								$mypos = $myposition->display_name;
							
								$mail['subject'] = sprintf("Speed Alert: %s", $rows[$i]->vehicle_no);
								$mail['message'] = $mymessage.$mypos;
								$mail['dest'] = $email; 
								$mail['bcc'] = "owner@adilahsoft.com,report.lacakmobil@gmail.com";
								$mail['sender'] = "support@lacak-mobil.com";
							
								lacakmobilmail($mail);
							}
						}
						
						if ($rows[$i]->user_payment_period >= 12)
						{
							print("----- user tahunan tidak diperbolehkan\r\n");
							
							$speedalert[$device] = mktime();
							continue;
						}
						
						if ($rows[$i]->user_alert_speed_sms == 2)
						{
							print("----- User Tidak Inginkan Terima SMS Alert\r\n");
							
							$speedalert[$device] = mktime();
							continue;
						}
						
						$this->db->where("agent_pss", 1);
						$this->db->where("agent_id", $rows[$i]->user_agent);
						$q = $this->db->get("agent");

						if ($q->num_rows() == 0)
						{
							print("----- agent tidak diijinkan\r\n");
							
							$speedalert[$device] = mktime();
							continue;
						}						
						
						if (($hp !== FALSE) && isON($rows[$i]->user_sms_notifikasi, 14))
						{		

							$myspeed = $row->gps_speed*1.852;
							$exspeed = explode(".",$myspeed);
							
							if (isset($exspeed[0]))
							{
								$fixspeed = $exspeed[0];
							}
							else
							{
								$fixspeed = $myspeed;
							}
							
							$mymessage = sprintf($this->config->item("SMS_ALERT_MAX_SPEED"), date("d/m/Y H:i:s", $t), $rows[$i]->vehicle_no, $fixspeed);
							$mypos = $myposition->display_name;
							
							$params["device"] = "alert";			
							$params['content'] = $mymessage.$mypos;
							$params['dest'] = $hp;	
							$xml = $this->load->view("sms/send", $params, true);
							
							$this->smsmodel->sendsms($xml);	
						}
						
						$speedalert[$device] = mktime();
					}
				}
			}
		}
	}

	
	function pulsealert($issummary = 1, $minpulsa = 1000, $masaaktifexpired=3)
	{
		$pulsetype = $this->config->item("vehicle_pulse");
		$pulsetype = array_merge($pulsetype, $this->config->item("vehicle_T1"));

		$this->db->order_by("logs_created", "asc");
		$this->db->where_in("logs_type", array("pulsealert", "masaaktifalert"));
		$this->db->where("logs_created >", date("Y-m-d 00:00:00"));
		$q = $this->db->get("logs");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			if ($rows[$i]->logs_type == "pulsealert")
			{
				$pulsealert[$rows[$i]->logs_content] = dbmaketime($rows[$i]->logs_created);
				continue;
			}
			
			$masaaktifalert[$rows[$i]->logs_content] = dbmaketime($rows[$i]->logs_created);
		}
		
		$this->db->where("user_type", 3);
		$q = $this->db->get("user");
		$rowagents = $q->result();
		for($i=0; $i < count($rowagents); $i++)
		{
			$hp = valid_mobiles($rowagents[$i]->user_mobile);
			if ($hp === FALSE) continue;
			
			foreach($hp as $h)
			{
				$agents[$rowagents[$i]->user_agent][] = $h;
			}
		}
	
		foreach($pulsetype as $val)
		{	
			if (! in_array($val, $this->config->item("vehicle_pulse")))
			{
				$this->db->where("vehicle_info LIKE", '%"masaaktif"%');
			}
			
			if ($issummary == 0)
			{								
				$this->db->where("agent_alert_pulsa", 1);
			}
			
			$this->db->where("vehicle_type", $val);	
			$this->db->where("vehicle_status", 1);
			$this->db->join("user", "user_id = vehicle_user_id");
			$this->db->join("agent", "agent_id = user_agent");
			$q = $this->db->get("vehicle");
			
			if ($q->num_rows() == 0) continue;						
			
			$tableinfogps = $this->gpsmodel->getGPSInfoTable($val);
			
			$rows = $q->result();

			$i = 0;
			foreach($rows as $row)
			{
				printf("%04d. %s %s ...\r\n", ++$i, $row->user_login, $row->vehicle_no);

				$hp = valid_mobiles($row->user_mobile);
				if (($hp === FALSE)  && (! isset($agents[$row->user_agent])))
				{
					continue;
				}

				if (! in_array($val, $this->config->item("vehicle_pulse")))
				{
					$json = json_decode($row->vehicle_info);
					
					if (strlen($json->masaaktif) == 6)
					{
						$masaaktif = dbintmaketime1($json->masaaktif, 0);
					}
					else
					{
						$masaaktif = dbintmaketime2($json->masaaktif, 0);
					}
					
					$rowinfo->pulsa = $json->sisapulsa;
					$rowinfo->masaaktif = $masaaktif;					
				}
				else
				{	
					if ($row->vehicle_info)
					{
						$json = json_decode($row->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
						
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$tableinfogps = $this->config->item("external_gpsinfotable");					
													
								$this->db = $this->load->database($database, TRUE);
							}
						}			
					}
									
					$this->db->limit(1);
					$this->db->select("CONV(SUBSTRING(gps_info_ad_input, 6), 16, 10) masaaktif, CONV(SUBSTRING(gps_info_ad_input, 1, 5), 16, 10) pulsa", false);
					$this->db->order_by("gps_info_time", "desc");				
					$this->db->where("gps_info_device", $row->vehicle_device);
					$q = $this->db->get($tableinfogps);
					
					if ($q->num_rows() == 0) continue;
					
					$rowinfo = $q->row();	
					
					$this->db = $this->load->database("master", TRUE);
					
					$masaaktif = dbintmaketime1($rowinfo->masaaktif, 0);
					$row->masaaktif = $masaaktif;
					
				}
								
				$nextexpired = $masaaktif-$masaaktifexpired*24*3600;				
				$deltamasaaktif = $nextexpired-mktime();
				
				//printf("masa aktif: %s\r\n", date("d/m/Y", $masaaktif));
				
				if ($masaaktif && ($deltamasaaktif <= 0) && ($deltamasaaktif >= -1*$masaaktifexpired*24*3600))
				{
					if ($row->user_agent == $this->config->item("GPSANDALASID"))
					{
						$masaaktifalertgpsandalas[] = $row;
					}
					else
					{
						$masaaktifalertlacakmobil[] = $row;
					}
					
					if (! isset($masaaktifalert[$row->vehicle_device]))
					{
						unset($insert);
						
						$insert['logs_type'] = "masaaktifalert";
						$insert['logs_created'] = date("Y-m-d H:i:s");
						$insert['logs_content'] = $row->vehicle_device;
						
						$this->db->insert("logs", $insert);
					}
					else
					{					
						$delta = mktime() - $masaaktifalert[$row->vehicle_device];
						if ($delta < (24*3600)) continue;
						
						unset($update);
						
						$update['logs_created'] = date("Y-m-d H:i:s");
						
						$this->db->where("logs_type", "masaaktifalert");
						$this->db->where("logs_content", $row->vehicle_device);
						$this->db->update("logs", $update);										
					}
					
					$masaaktifalert[$row->vehicle_device] = mktime();
/*
					// send sms
					
					unset($params);
					$params['content'] = sprintf("Masa aktif kartu GSM (%s) pada kendaraan %s adalah %s. Silahkan untuk mengisi ulang pulsa. ", $row->vehicle_card_no, $row->vehicle_no, date("d/m/Y", $masaaktif));
					
					if ($row->user_payment_pulsa)
					{
						$params['dest'] = array($hp);	
					}
					else
					if (isset($agents[$rows->user_agent]))
					{						
						$params['dest'] = $agents[$rows->user_agent];
					}
					
					$xml = $this->load->view("sms/send", $params, true);					
					$this->smsmodel->sendsms($xml);	
*/					
				}
				
				if ($rowinfo->pulsa > $minpulsa) continue;

				$row->pulsa = $rowinfo->pulsa;				
				if ($row->user_agent == $this->config->item("GPSANDALASID"))
				{
					$alertgpsandalas[] = $row;
				}
				else
				{
					$alertlacakmobil[] = $row;
				}
				
				// send sms
				
				if (! isset($pulsealert[$row->vehicle_device]))
				{
					unset($insert);
					
					$insert['logs_type'] = "pulsealert";
					$insert['logs_created'] = date("Y-m-d H:i:s");
					$insert['logs_content'] = $row->vehicle_device;
					
					$this->db->insert("logs", $insert);
				}
				else
				{					
					$delta = mktime() - $pulsealert[$row->vehicle_device];
					if ($delta < (24*3600)) continue;
					
					unset($update);
					
					$update['logs_created'] = date("Y-m-d H:i:s");
					
					$this->db->where("logs_type", "pulsealert");
					$this->db->where("logs_content", $row->vehicle_device);
					$this->db->update("logs", $update);										
				}
				
				$pulsealert[$row->vehicle_device] = mktime();
				
				// send sms
				
				if ($issummary == 0)
				{
					unset($destnumbers);
					
					if ($row->user_payment_pulsa)
					{
						if ($hp === FALSE)
						{
							$destnumbers = $agents[$row->user_agent];
						}
						else
						{
							$destnumbers = $hp;
						}
					}
					else
					if (isset($agents[$row->user_agent]))
					{
						$destnumbers = $agents[$row->user_agent];
					}
					
					if (isset($destnumbers))
					{					
						$params['content'] = sprintf("Pada %s pulsa kartu GSM %s pada kendaraan %s tinggal Rp %d. Silahkan untuk mengisi ulang kembali. ", date("d/m/Y H:i:s"), $row->vehicle_card_no, $row->vehicle_no, number_format($row->pulsa, 0, "", ","));
						$params['dest'] = $destnumbers;
						$xml = $this->load->view("sms/send", $params, true);
						
						$this->smsmodel->sendsms($xml);	
					}
				}
			}					
		}
		
		if (! $issummary) return;
		
		if (isset($alertgpsandalas))
		{
			$i = 0;
			$message = sprintf("Berikut daftar kendaraan dimana pulsa < %d\r\n", $minpulsa);
			foreach($alertgpsandalas as $val)
			{
				$message .= sprintf("%03d %s %s Rp. %s\r\n", ++$i, $val->user_login, $val->vehicle_no, number_format($val->pulsa, 0, "", ","));
			}
			
			$mail['subject'] = sprintf("alert pulsa");
			$mail['message'] = $message;
			$mail['dest'] = "norman_ab@gpsandalas.com,zad_anwar@gpsandalas.com,owner@adilahsoft.com";
			
			lacakmobilmail($mail);			
		}
		
		if (isset($alertlacakmobil))
		{
			$i = 0;
			$message = sprintf("Berikut daftar kendaraan dimana pulsa < %d\r\n", $minpulsa);
			foreach($alertlacakmobil as $val)
			{
				$message .= sprintf("%03d %s %s Rp. %s\r\n", ++$i, $val->user_login, $val->vehicle_no, number_format($val->pulsa, 0, "", ","));
			}
			
			$mail['subject'] = sprintf("alert pulsa");
			$mail['message'] = $message;
			$mail['dest'] = "report.lacakmobil@gmail.com,owner@adilahsoft.com"; 
			
			lacakmobilmail($mail);			
		}

		if (isset($masaaktifalertgpsandalas))
		{
			$i = 0;
			$message = sprintf("Berikut daftar kendaraan dimana masa aktif akan habis %s hari lagi\r\n", $masaaktifexpired);
			foreach($masaaktifalertgpsandalas as $val)
			{
				$message .= sprintf("%03d %s %s %s\r\n", ++$i, $val->user_login, $val->vehicle_no, date("Y-m-d", $val->masaaktif));
			}
			
			$mail['subject'] = sprintf("alert pulsa");
			$mail['message'] = $message;
			$mail['dest'] = "norman_ab@gpsandalas.com,zad_anwar@gpsandalas.com,owner@adilahsoft.com";
			
			lacakmobilmail($mail);			
		}

		if (isset($masaaktifalertlacakmobil))
		{
			$i = 0;
			$message = sprintf("Berikut daftar kendaraan dimana masa aktif akan habis %s lagi\r\n", $masaaktifexpired);
			foreach($masaaktifalertlacakmobil as $val)
			{
				$message .= sprintf("%03d %s %s %s\r\n", ++$i, $val->user_login, $val->vehicle_no, date("Y-m-d", $val->masaaktif));
			}
			
			$mail['subject'] = sprintf("alert pulsa");
			$mail['message'] = $message;
			$mail['dest'] = "report.lacakmobil@gmail.com,owner@adilahsoft.com"; 
			
			lacakmobilmail($mail);			
		}

	}
	
	function freespace()
	{
		$df_c = disk_free_space("C:");

		$i = 0;
		while(1)
		{			
			if ($df_c < 1024) break;
			
			$df_c = round($df_c/1024);
			$i++;
			
			if ($i >= 4) break;
		}
		
		switch($i)
		{
			case 0:
				$free = sprintf("%s bytes", number_format($df_c, 0, "", "."));
			break;
			case 1:
				$free = sprintf("%s KB", number_format($df_c, 0, "", "."));
			break;
			case 2:
				$free = sprintf("%s MB", number_format($df_c, 0, "", "."));
			break;
			case 3:
				$free = sprintf("%s GB", number_format($df_c, 0, "", "."));
			break;
			case 4:
				$free = sprintf("%s TB", number_format($df_c, 0, "", "."));
			break;

		}
		
		$message = sprintf("Free space pada dir C adalah %s
		
		schedule per minggu:
		1. zip log-log yang bukan hari ini  pada c:\service\
		2. hapus log-log yang sudah di zip
		3. pindahkan zip ke d:/logarchive. untuk penamaan zip, lihat pada zip yang sudah ada.
				
		
		", $free);
	
		
		$mail['format'] = "html";
		$mail['subject'] = sprintf("space colo 1");
		$mail['message'] = nl2br($message);
		$mail['dest'] = "report.lacakmobil@gmail.com,owner@adilahsoft.com"; 
		
		lacakmobilmail($mail);			
		
	}
	
	function parkalert()
	{
		$this->db->where("config_name", "parkalert");
		$q = $this->db->get("config");
		
		$rowlast = $q->row();
		$lastrunning = dbmaketime($rowlast->config_value);
		
		echo "config: ".date("d/m/Y H:i:s", $lastrunning)."\r\n";
		
		$this->db->order_by("logs_created", "asc");
		$this->db->where("logs_type", "parkalert");
		$q = $this->db->get("logs");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$parkalert[$rows[$i]->logs_content] = dbmaketime($rows[$i]->logs_created);
		}

		$this->db->where("vehicle_maxparking >", 0);
		$this->db->where("vehicle_status", 1);
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();

		unset($update);
		$update['config_value'] = date("Y-m-d H:i:s", mktime());
		$this->db->where("config_name", "parkalert");
		$this->db->update("config", $update);

		for($i=0; $i < count($rows); $i++)
		{
			$jsonws = json_decode($rows[$i]->vehicle_info);
			if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $rows[$i]->vehicle_no);
				continue;
			}			


			printf("[%s] %04d %s %s %sm...\r\n", date("Ymd H:i:s"), $i+1, $rows[$i]->user_login, $rows[$i]->vehicle_no, $rows[$i]->vehicle_maxparking);
			
			$maxpark = $rows[$i]->vehicle_maxparking*60;
			
			if ($rows[$i]->vehicle_info)
			{
				$json = json_decode($rows[$i]->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$tablegps = $this->config->item("external_gpstable");					
											
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}

			if (! isset($tablegps))
			{
				$tablegps = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);
			}
			
			$devices = explode("@", $rows[$i]->vehicle_device);
			
			$this->db->order_by("gps_time", "asc");				
			$this->db->where("gps_time >=", date("Y-m-d H:i:s", $lastrunning-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);			
			$q = $this->db->get($tablegps);
			
			$this->db = $this->load->database("master", TRUE);
			
			if ($q->num_rows() == 0) continue;
			
			$rowgps = $q->result();
			
			printf("=== %d record\r\n", count($rowgps)); 
			
			$this->db->where("parkir_device", $rows[$i]->vehicle_device);
			$q = $this->db->get("parkir");
			
			if ($q->num_rows() == 0)
			{
				$lastlat = -999999;
				$lastlng = -999999;
				$lastpark = 0;
			}
			else
			{
				$rowparkir = $q->row();
				
				$lastlat = $rowparkir->parkir_lat;
				$lastlng = $rowparkir->parkir_lng;
				$lastpark = dbmaketime($rowparkir->parkir_time);
				
				printf("=== last: %s,%s %s\r\n", $lastlat, $lastlng, date("d/m/Y H:i:s", $lastpark+7*3600)); 
			}
			
			// looping data
						
			for($j=0; $j < count($rowgps); $j++)
			{
				$lat = sprintf("%.4f", getLatitude($rowgps[$j]->gps_latitude, $rowgps[$j]->gps_ns));
				$lng = sprintf("%.4f", getLongitude($rowgps[$j]->gps_longitude, $rowgps[$j]->gps_ew));

				if (($lat == $lastlat) && ($lastlng == $lng)) 
				{					
					if (($j+1) == count($rowgps))
					{
						$t = dbmaketime($rowgps[$j]->gps_time);
						$parklength = $t-$lastpark;						
						
						printf("=== current=%d s <-> max=%d s\r\n", $parklength, $maxpark);
						
						if ($parklength < $maxpark)
						{
							continue;
						}
						
						// alert park
						
						$lastpark = $t;
						$this->doalertpark($rows[$i], $rowgps[$j], $parklength, $parkalert);
					}
					
					continue;
				}			
				
				$lastlat = $lat;
				$lastlng = $lng;
				
				$prevpark = $lastpark;
				$lastpark = dbmaketime($rowgps[$j]->gps_time);
				
				if ($j == 0)
				{					
					continue;
				}
				
				$t = dbmaketime($rowgps[$j-1]->gps_time);
				$parklength = $t-$prevpark;
				
				printf("=== current=%d s <-> max=%d s\r\n", $parklength, $maxpark);
				
				if ($parklength < $maxpark)
				{
					continue;
				}
				
				// alert park
				
				$this->doalertpark($rows[$i], $rowgps[$j-1], $parklength, $parkalert);
			}
			
			$this->db->where("parkir_device", $rows[$i]->vehicle_device);
			$total = $this->db->count_all_results("parkir");
			
			unset($update);
			
			$update['parkir_device'] = $rows[$i]->vehicle_device;
			$update['parkir_lat'] = $lastlat;
			$update['parkir_lng'] = $lastlng;
			$update['parkir_time'] = date("Y-m-d H:i:s", $lastpark);
			
			if ($total > 0)
			{
				$this->db->where("parkir_device", $rows[$i]->vehicle_device);
				$this->db->update("parkir", $update);
			}
			else
			{
				$this->db->insert("parkir", $update);
			}
		}
	}
	
	function doalertpark($vehicle, $gps, $parklength, $parkalert)
	{
		$isparkalert = false;
		$t = dbmaketime($gps->gps_time);

		if (! isset($parkalert[$vehicle->vehicle_device]))
		{
			$parkalert[$vehicle->vehicle_device] = $t;
			$isparkalert = true;
		}
		else
		{
			$delta = mktime() - $parkalert[$vehicle->vehicle_device];
			$isparkalert = $delta > 3600;
		}
		
		if (! $isparkalert) 
		{
			printf("=== alerted at %s\r\n", date("d/m/Y H:i:s", $parkalert[$vehicle->vehicle_device]));
			return;
		}

		unset($insert);
		
		$insert['parkir_alert_device'] = $vehicle->vehicle_device;
		$insert['parkir_alert_time'] = date("Y-m-d H:i:s", $t);
		$insert['parkir_alert_length'] = round($parklength/60);
		$insert['parkir_alert_max'] = $vehicle->vehicle_maxparking;
		$insert['parkir_alert_created'] = date("Y-m-d H:i:s");
		
		$this->db->insert("parkir_alert", $insert);
		
		$emails = get_valid_emails($vehicle->user_mail);
		if ($vehicle->user_alert_parking_email == 1)
		{
			foreach($emails as $email)
			{
				unset($mail);
			
				$mail['subject'] = sprintf("Parkir Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = sprintf($this->config->item("MAIL_ALERT_MAX_PARK"), date("d/m/Y H:i:s", $t+7*3600), $vehicle->vehicle_no, round($parklength/60), $vehicle->vehicle_maxparking);
				$mail['dest'] = $email; 
				$mail['bcc'] = "owner@adilahsoft.com,report.lacakmobil@gmail.com";
				$mail['sender'] = "support@lacak-mobil.com";
			
				lacakmobilmail($mail);								
			}
		}
		
		if (! isON($vehicle->user_sms_notifikasi, 13))
		{
			printf("=== notifikasi off\r\n");
			return;
		}
		
		$hp = valid_mobiles($vehicle->user_mobile);
		if ($hp === FALSE) 
		{
			printf("=== invalid mobile no: %s\r\n", $vehicle->user_mobile);
			return;
		}
		
		
		if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}
		
		if ($vehicle->user_alert_parking_sms == 2)
		{
			print("----- user tidak inginkan terima alert\r\n");
			return;
		}
		
		$this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}	
			
		$content = sprintf($this->config->item("SMS_ALERT_MAX_PARK"), date("d/m/Y H:i:s", $t+7*3600), $vehicle->vehicle_no, round($parklength/60), $vehicle->vehicle_maxparking);
						
		foreach($hp as $h)
		{				
			$xml = sprintf("%s\1%s\alat", $h, $content);
			$this->smsmodel->sendsms($xml);
			
			printf("%s\r\n", $xml);
		}								
							
		$parkalert[$vehicle->vehicle_device] = mktime();

		unset($update);
							
		$update['logs_created'] = date("Y-m-d H:i:s");
		
		$this->db->where("logs_content", $vehicle->vehicle_device);
		$this->db->where("logs_type", "parkalert");
		$this->db->update("logs", $update);
		
		if ($this->db->affected_rows() == 0)
		{
			unset($insert);
			
			$insert['logs_created'] = date("Y-m-d H:i:s");
			$insert['logs_content'] = $vehicle->vehicle_device;
			$insert['logs_type'] = "parkalert";
			
			$this->db->insert("logs", $insert);
		}
		
	}

	function stop_service()
	{
		exec("ps aux | grep java", $results); 
		
		for($i=0; $i < count($results); $i++)
		{
			$result = $results[$i];
			$datas = preg_split("/\s+/", $result);

			$id = $datas[1];

			$kill = sprintf("kill -9 %d", $id);
			
			printf("%s\n", $kill);
			system($kill);	
		}
	}
	
	function oldlink($age=7)
	{
		$smscolodb = $this->load->database("smscolo", TRUE);
		
		$t = mktime()-$age*24*3600;
		$smscolodb->where("created <", date("Y-m-d 00:00:00", $t));
		$smscolodb->delete("link");
		
		$this->load->dbutil();		
		$this->dbutil->optimize_table("link"); 		
	}
	
	function pulseT1()
	{
		$this->db->where("vehicle_status", 1);
		$this->db->where_in("vehicle_type", $this->config->item("vehicle_T1"));
		$total = $this->db->count_all_results("vehicle");

		$this->db->where("config_name", "pulset1");
		$q = $this->db->get("config");
		
		if ($q->num_rows() == 0)
		{
			unset($insert);
			
			$insert['config_name'] = 'pulset1';
			$insert['config_value'] = 0;
			$insert['config_lastmodified'] = date("Y-m-d H:i:s");
			
			$this->db->insert("config", $insert);
			
			$offset = 0;
		}
		else
		{
			$row = $q->row();			
			$offset = $row->config_value;
		}
		
		if ($offset >= $total)
		{
			$offset = 0;
		}
		
		$this->db->limit(1, $offset);
		$this->db->where("vehicle_status", 1);
		$this->db->where_in("vehicle_type", $this->config->item("vehicle_T1"));
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			$offset = 0;
		}
		else
		{
			$row = $q->row();
			
			$hp = valid_mobile($row->vehicle_card_no);
			
			if ($hp)
			{
				$cekpulsa = $this->smsmodel->checkpulse($row->vehicle_operator);
				
				if ($cekpulsa)
				{
					$xml = sprintf("%s\1%s", $row->vehicle_card_no, $cekpulsa);
					printf("%s %s\n", date("Ymd"), $xml);
					$this->smsmodel->sendsms($xml, 1);
				}								
			}
			
			$offset++;
		}
		
		unset($update);
		
		$update['config_value'] = $offset;
		
		$this->db->where("config_name", "pulset1");
		$this->db->update("config", $update);
		
	}

	function lock($maxspeed = 10)
	{
		$this->db->where("vehicle_info LIKE", '%"lock":1%');
		$this->db->where("vehicle_status", 1);
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			printf("tidak ada kendaraan dengan settingan lock\n");
			return;
		}
		
		$rows = $q->result();		
		for($i=0; $i < count($rows); $i++)
		{
			// cek engine on?
			
			$this->db = $this->load->database("master", TRUE);

			if ($rows[$i]->vehicle_info)
			{
				$json = json_decode($rows[$i]->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						
						$tablegps = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
											
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}
			
			printf("cek %s\r\n", $rows[$i]->vehicle_no);
			
			$vtype = strtoupper($rows[$i]->vehicle_type);
			if (in_array($vtype, $this->config->item("vehicle_gtp")))
			{
				if (! isset($tableinfo))
				{				
					$tableinfo = $this->gpsmodel->getGPSInfoTable($vtype);
				}
				
				$this->db->order_by("gps_info_time", "DESC");
				$this->db->where("gps_info_device", $rows[$i]->vehicle_device);
				$q = $this->db->get($tableinfo, 1, 0);
				
				if ($q->num_rows() == 0)
				{
					printf("=== tidak ada data info\n");
					continue;					
				}
				
				$rowinfo = $q->row();
				
				$ioport = $rowinfo->gps_info_io_port;				
				$row->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1));
				
				if (! $row->status1) 
				{
					printf("=== mesin mati\n");
					continue;
				}
			}
			else
			{
				// cek kecepatan
				
				if (! isset($tablegps))
				{
					$tablegps = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);
				}
				
				$devices = explode("@", $rows[$i]->vehicle_device);
				
				$this->db->limit(1);
				$this->db->order_by("gps_time", "desc");
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$q = $this->db->get($tablegps);
				
				if ($q->num_rows() == 0)
				{
					printf("=== tidak ada data GPS\n");
					continue;					
				}
				
				$rowgps = $q->row();				
				$speed = $rowgps->gps_speed*1.852;
				
				if ($speed < $maxspeed)
				{
					printf("=== kecepatan dibawah maksimum %d < %d \n", $speed, $maxspeed);
					continue;										
				}
			}
							
			$this->db = $this->load->database("master", TRUE);				
							
			// matikan mesin				
			
			$command = $this->smsmodel->cutoffengine($rows[$i]->vehicle_type);
			$gsm = valid_mobile($rows[$i]->vehicle_card_no);
			
			if ($gsm)
			{				
				$xml = sprintf("%s\1%s", $gsm, $command);
				printf("%s\n", $xml);
				$this->smsmodel->sendsms($xml, 1);
			}				
			
			$json = json_decode($rows[$i]->vehicle_info);
			
			unset($json->lock);
			
			unset($update);
			$update['vehicle_info'] = json_encode($json);

			$this->db->where("vehicle_id", $rows[$i]->vehicle_id);
			$this->db->update("vehicle", $update);
			
		}
	}
	
	function test()
	{
		echo $this->history("T5 FUEL");
	}
	
	function alertexpired()
	{
		print("PROSES ALERT EXPIRED \r\n");
		print("__________________________ \r\n");
		
		$dt_before = 5;
		$dt_now = date("Ymd");
		$dt_select = date("Ymd",strtotime("+".$dt_before." "."day", strtotime($dt_now)));
		
		$this->db->order_by("vehicle_id","asc");
		$this->db->where("vehicle_status <>",3);
		$this->db->where("vehicle_active_date2 >=",$dt_now);
		$this->db->where("vehicle_active_date2 <=",$dt_select);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		
		foreach($rows as $vehicle)
		{
			printf("----- Proses Vehicle %s\r\n",$vehicle->vehicle_no);
			
			$this->db->where("user_id",$vehicle->vehicle_user_id);
			$this->db->limit(1);
			$qu = $this->db->get("user");
			$user = $qu->row();
			
			if ($qu->num_rows > 0)
			{
				$emails = get_valid_emails($user->user_mail);
				$hp = valid_mobiles($user->user_mobile);
				
				if (is_array($emails) && count($emails))
				{
					foreach($emails as $email)
					{
						unset($mail);
						
						if ($user->user_agent == 3)
						{
							$smscontent = $this->config->item("SMS_EXPIRED_GPSANDALAS");
						}
						else if ($user->user_agent == 18)
						{
							$smscontent = $this->config->item("SMS_EXPIRED_ILHAM");
						}
						else if ($user->user_agent == 4)
						{
							$smscontent = $this->config->item("SMS_EXPIRED_SUNU");
						}
						else if ($user->user_agent == 19)
						{
							$smscontent = $this->config->item("SMS_EXPIRED_INDRALAMPUNG");
						}
						else if ($user->user_agent == 28)
						{
							$smscontent = $this->config->item("SMS_EXPIRED_ENDANG");
						}
						else if ($user->user_agent == 9)
						{
							$smscontent = $this->config->item("SMS_EXPIRED_SULAIMAN");
						}
						else if ($user->user_agent == 34)
						{
							$smscontent = $this->config->item("SMS_EXPIRED_SOPIADEWI");
						}
						else if ($user->user_agent == 14)
						{
							$smscontent = $this->config->item("SMS_EXPIRED_IBNU");
						}
						else
						{
							$smscontent = $this->config->item("SMS_EXPIRED_LACAKMOBIL");
						}
						
						$contentmail = sprintf($smscontent, $vehicle->vehicle_no, date("d-m-Y",strtotime($vehicle->vehicle_active_date2)));
						
						$mail['subject'] = sprintf("Alert Masa Aktif Vehicle : %s", $vehicle->vehicle_no);
						$mail['message'] = $contentmail;
						$mail['dest'] = $email;
						$mail['bcc'] = "report.lacakmobil@gmail.com";
						
						if ($user->user_agent == 3)
						{
							$mail['sender'] = "support@gpsandalas.com";
						}
						else
						{
							$mail['sender'] = "support@lacak-mobil.com";
						}
				
						printf("----- Sending Email To %s %s %s\r\n", $mail['dest'], $mail['subject'], $mail['message']);
						lacakmobilmail($mail);			
						printf("----- Sending Email OK \r\n");
					}
				}
				else
				{
					printf("----- User Mail Tidak Valid !\r\n");
				}
				
				if ($hp !== FALSE)
				{
					$params["device"] = "alert";			
					$params['content'] = sprintf($smscontent, $vehicle->vehicle_no, date("d-m-Y",strtotime($vehicle->vehicle_active_date2)));
					$params['dest'] = $hp;	
					$xml = $this->load->view("sms/send", $params, true);
					$this->smsmodel->sendsms($xml);	
					printf("----- Sending Phone OK \r\n");
				}
				else
				{
					printf("----- User Mobile Tidak Valid !\r\n");
				}
			}
			else
			{
				printf("----- User Tidak Ditemukan !\r\n");
			}
			
			$this->db->cache_delete_all();
		}
		
		$this->db->cache_delete_all();
		print("SELESAI \r\n");
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
	
	function tupperware_set_delivered()
	{
		$this->dbtransporter = $this->load->database("transporter",true);
		$code_dist = 0;
		
		//422 = Group Tupperwere
		$this->db->where("vehicle_group",422);
		$this->db->or_where("vehicle_user_id",1493);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		$total_vehicle = $q->num_rows();
		printf("Total Vehicle : %s \r\n",$total_vehicle);
		printf("********************************* \r\n");
		
		for ($i=0;$i<$total_vehicle;$i++)
		{
			printf("Proses Vehicle : %s \r\n",$rows[$i]->vehicle_no);
			printf("Cek Alert Geofence \r\n");
			
			//Cek Geofence Alert
			$this->db->order_by("geoalert_id","desc");
			$this->db->where("geoalert_direction",2);
			$this->db->where("geoalert_vehicle", $rows[$i]->vehicle_device);
			$this->db->limit(1);
			$q = $this->db->get("geofence_alert");
			$row_alert = $q->row();
			
			if (isset($row_alert->geoalert_geofence))
			{
				printf("Cek Data Geofence \r\n");
				
				//Select Geofence
				$this->db->select("geofence_name");
				$this->db->where("geofence_status",1);
				$this->db->where("geofence_id",$row_alert->geoalert_geofence);
				$this->db->limit(1);
				$q = $this->db->get("geofence");
				$row_geofence = $q->row();
				
				if (isset($row_geofence->geofence_name))
				{
					printf("Proses Cek Kode Distributor \r\n");
					//Get Kode Distributor
					$ex_code = explode("|", $row_geofence->geofence_name);
					
					if (isset($ex_code[0]) && isset($ex_code[1])) 
					{ 
						$code_dist = $ex_code[0]; 
						//Get Booking ID by Table DR
						$this->dbtransporter->order_by("transporter_dr_id","desc");
						$this->dbtransporter->where("transporter_dr_status",1);
						$this->dbtransporter->where("transporter_db_code",$ex_code[0]);
						$this->dbtransporter->limit(1);
						$q = $this->dbtransporter->get("tupper_dr");
						$row_dr = $q->row();
						if (isset($row_dr->transporter_dr_booking_id))
						{
							//Update ID Booking Set To Delivered
							$this->dbtransporter->where("booking_id",$row_dr->transporter_dr_booking_id);
							$this->dbtransporter->where("booking_status",1);
							$this->dbtransporter->where("booking_delivery_status",1);
							$this->dbtransporter->limit(1);
							$q = $this->dbtransporter->get("id_booking");
							$row_id = $q->row();
						
							if (isset($row_id->booking_id) && ($row_id->booking_datetime_in <= $row_alert->geoalert_time))
							{
								printf("Proses Set Delivered : %s \r\n", $row_id->booking_id );
								unset($update_delivered);
								$update_delivered["booking_delivery_status"] = 2;
								$update_delivered["booking_delivered_datetime"] = date("Y-m-d H:i:s",strtotime($row_alert->geoalert_time));
								$update_delivered["booking_set_delivered_datetime"] = date("Y-m-d H:i:s",strtotime($row_alert->geoalert_time));
								$this->dbtransporter->where("booking_id",$row_id->booking_id);
								$this->dbtransporter->update("id_booking",$update_delivered);
							
								printf("Delivered Status Set To : %s \r\n", $update_delivered["booking_delivery_status"]);
								printf("Delivered DateTime : %s \r\n", $update_delivered["booking_delivered_datetime"]);
								printf("Proses Set Delivered - OKE \r\n");
							
								printf("Proses Set Vehicle Group To Default \r\n");
								$this->dbtransporter->where("booking_vehicle",$rows[$i]->vehicle_device);
								$this->dbtransporter->where("booking_status",1);
								$this->dbtransporter->where("booking_delivery_status",1);
								$qb = $this->dbtransporter->get("id_booking");
								$row_booking = $qb->row();
							
								//print_r($row_booking);exit;
							
								if (!isset($row_booking->booking_id))
								{
									unset($update_group);
									$update_group["vehicle_group"] = 0;
									$update_group["vehicle_image"] = "car";
									$this->db->where("vehicle_device",$rows[$i]->vehicle_device);
									$this->db->update("vehicle",$update_group);
									printf("Proses Set Vehicle Group To Default - OKE \r\n");
								}
								else
								{
									printf("Vehicle %s Masih Ada Pengiriman Ke Distributor Lain \r\n", $rows[$i]->vehicle_no);
								}
							}
						else
						{
							printf("ID Booking Tidak Ada \r\n");
						}
					}
					else
					{
						printf("No DR Tidak Ada \r\n");
					}
					}
					else
					{
						printf("Geofence Bukan Distributor \r\n");
					}
				}
				else
				{
					printf("Data Geofence Tidak Ada \r\n");
				}
			}
			else
			{
				printf("Tidak Ada Alert \r\n");
			}
			
			printf("================================= \r\n");
		}
		
		printf("Proses Finish \r\n");
	}
	
	
	//for lemo
	function geofencealert_lemo()
	{ 
		$this->db->where("config_name", "geofencealertprocessing_lemo");
		$q = $this->db->get("config");
		
		if ($q->num_rows())
		{
			$row = $q->row();
			
			if ($row->config_value == 1) 
			{				
				$t = dbmaketime($row->config_lastmodified);
				$delta = mktime()-$t;
				
				if ($delta > 3600)
				{
					unset($update);
					
					$update['config_value'] = 0;
					
					$this->db->where("config_name", "geofencealertprocessing_lemo");
					$this->db->update("config", $update);	

					print("masih proses\r\n");
					return;				
				}
				
				print("masih proses\r\n");		
				return;						
			}
			
			unset($update);
			
			$update['config_value'] = 1;
			
			$this->db->where("config_name", "geofencealertprocessing_lemo");
			$this->db->update("config", $update);
		}
		else
		{
			unset($insert);
			
			$insert['config_name'] = "geofencealertprocessing_lemo";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}

		$this->db->distinct();
		$this->db->select("user.*, vehicle.*");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 >=",date("Ymd"));
		$this->db->where("user_id",2110); //Croscheck lemo
		$this->db->join("vehicle", "vehicle_device = geofence_vehicle");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->where("geofence_status", 1);
		$q = $this->db->get("geofence");
		
		if ($q->num_rows() == 0)
		{
			echo "tidak ada user yang setting geofence\r\n";
			$this->geofencealert_release_lemo();
			return;
		}				
		
		$rows = $q->result();
		$i = 0;
		foreach($rows as $row)
		{			
			/* $jsonws = json_decode($row->vehicle_info);
			if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $row->vehicle_no);
				continue;
			}			 */		
			
			printf("\r\n\r\n[%s] %02d %s %s\r\n", date("Y-m-d H:i:s"), $i, $row->vehicle_device, $row->vehicle_no);

			$this->dogeofencealert_lemo($row);
			$i++;
		}


		$this->geofencealert_release_lemo();
	}
	
	function dogeofencealert_lemo($vehicle)
	{
		// ambil user yg setting geofence
			
		$devices = explode("@", $vehicle->vehicle_device);
		if (count($devices) < 2) 
		{
			printf("----- Invalid device\r\n");
			return;
		}
			
		// ambil data terakhir alert geofence
			
		$this->db->limit(1);
		$this->db->order_by("geofence_lastchecked", "desc");		
		$this->db->where("geoalert_vehicle", $vehicle->vehicle_device);
		$q = $this->db->get("geofence_alert");
		if ($q->num_rows() == 0)
		{
			$lastdir = -1;
			$lastchecked = mktime()-7*3600;
			$lastgeofence = -1;
		}
		else
		{
			$row = $q->row();				
			$lastdir = $row->geoalert_direction;
			$lastchecked = dbmaketime($row->geofence_lastchecked);
			$geoalert_id = $row->geoalert_id;
			$lastgeofence = $row->geoalert_geofence;
			
			if ($lastchecked < (mktime()-2*24*3600-7*3600))
			{
				$lastchecked = mktime()-2*24*3600-7*3600;
			}
		}
		
		$me = date("Y-m-d H:i:s", $lastchecked-7*3600);
		printf("Lastchecked : %s\r\n",$me);
		// ambil data gps
		$json = json_decode($vehicle->vehicle_info);
		if ($vehicle->vehicle_info)
		{
			
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					echo "database: ".$database."\n";
					$tablegps = $this->config->item("external_gpstable");					
					$tablegpshist = $this->config->item("external_gpstable_history");
										
					$this->db = $this->load->database($database, TRUE);
				}
			}

			//Jika WebSocket
			if (isset($json->vehicle_ws)) 
			{	
				$database = "gpshistory2";
				$tablegps = strtolower($devices[0]."@".$devices[1]."_gps");
				$tablegpshist = strtolower($devices[0]."@".$devices[1]."_info");
				$this->db = $this->load->database($database, TRUE);	
			}
		}			

		if (! isset($tablegps))
		{		
			$table_hist = $this->config->item("table_hist");	
						
			$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
			$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
		}
		
		if (! $tablegps) 
		{
			printf("----- tabel %s tidak ada\r\n", $tablegps);
			$this->db->cache_delete_all();
			return;
		}
		
		$getgpsstart = mktime();			
		$tyesterday = mktime(-7, 59, 59, date('n', $getgpsstart), date('j', $getgpsstart), date('Y', $getgpsstart));		
		
		if ($lastchecked < $tyesterday && (!isset($json->vehicle_ws)))
		{
			printf("----- check to history\n");
			
			$sql = sprintf("SELECT * FROM (SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s') UNION SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s'))  tbl1 ORDER BY gps_time ASC "
				, $this->db->dbprefix
				, $tablegps
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)
				, $this->db->dbprefix
				, $tablegpshist
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)

			);
			$q = $this->db->query($sql);
		}
		elseif($lastchecked < $tyesterday && (isset($json->vehicle_ws)))
		{
			printf("----- Websocket vehicle check to history\n");
			
			$this->db = $this->load->database("gpshistory",true);
			$this->db->order_by("gps_time", "asc");					
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);	
		}
		else
		{	
			if (!isset($json->vehicle_ws))
			{
				printf("----- check to current\n");
				$this->db->order_by("gps_time", "asc");					
				$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$q = $this->db->get($tablegps);			
			}
			else
			{
				printf("----- Ambil Dari Database TMP Socket \n");
				$this->db2 = $this->load->database("gpshistory2",true);
				$this->db2->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
				$this->db2->where("gps_name", $devices[0]);
				$this->db2->where("gps_host", $devices[1]);
				$q = $this->db2->get($tablegps);			
			}
		}
		
		if ($q->num_rows() == 0 && (isset($json->vehicle_ws)))
		{
			printf("----- Ambil Dari Database TMP Socket \n");
			$this->db2 = $this->load->database("gpshistory2",true);
			$this->db2->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db2->where("gps_name", $devices[0]);
			$this->db2->where("gps_host", $devices[1]);
			$q = $this->db2->get($tablegps);			
		}
			
		if ($q->num_rows() == 0) 
		{
			$this->db = $this->load->database("master", TRUE);
			
			printf("----- tidak ada sejak %s\r\n", date("d/m/Y H:i:s", $lastchecked));
			return;
		}
			
		$rowgps = $q->result();
		
		$this->db = $this->load->database("master", TRUE);
		
		printf("----- lama ambil data gps (%d): %d second\r\n", count($rowgps), mktime()-$getgpsstart);
			
		$checkarea = mktime();	
		$found = false;		
		
		for($i=0; $i < count($rowgps); $i++)
		{	
			$lat = getLatitude($rowgps[$i]->gps_latitude, $rowgps[$i]->gps_ns);
			$lng = getLongitude($rowgps[$i]->gps_longitude, $rowgps[$i]->gps_ew);
			
			$gps = $rowgps[$i];
			$gps->lat = $lat;
			$gps->lng = $lng;
				
			$sql = sprintf("
					SELECT 	* 
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_vehicle = '%s' )
							AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle->vehicle_device);

			$q = $this->db->query($sql);
			
			if ($q->num_rows() == 0)
			{
				if ($lastdir == 2) continue;

				$found = true;
				break;
			}
			
			if ($lastdir == 1) continue;
						
			$rowgeo = $q->row();					
			$found = true;
			
			break;
		}
		
		printf("----- lama check area: %d second\r\n", mktime()-$checkarea);
		
		if (! $found) 
		{
			unset($update);
			
			$t = dbmaketime($rowgps[count($rowgps)-1]->gps_time);
			$update['geofence_lastchecked'] = date("Y-m-d H:i:s", $t+7*3600);
			
			$this->db->where("geoalert_id", $geoalert_id);
			$this->db->update("geofence_alert", $update);			
			
			if ($lastdir == 1)
			{				
				printf("----- posisi kendaraan masih didalam area geofence\r\n");
				return;				
			}
			
			printf("----- posisi kendaraan masih di luar area geofence\r\n");
			return;
		}
		
		if ($lastdir != 2)
		{
			$this->db->where("geofence_status", 1);
			$this->db->where("geofence_id", $lastgeofence);
			$q = $this->db->get("geofence");
			
			if ($q->num_rows() > 0)
			{
				$rowgeo = $q->row();
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert_lemo($vehicle, 2, $gps, $rowgeo, $t);
			}
			else
			{
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert_lemo($vehicle, 2, $gps, FALSE, $t);
			}
			
			return;
		}
		$t = $rowgps[count($rowgps)-1]->gps_time;
		$this->addgeofencealert_lemo($vehicle, 1, $gps, $rowgeo, $t);
	}
	
	//
	function addgeofencealert_lemo($vehicle, $direction, $gps, $geofence, $t)
	{
		unset($insert);
			
		$insert['geoalert_vehicle'] = $vehicle->vehicle_device;
		$insert['geoalert_direction'] = $direction;
		$insert['geoalert_time'] = date("Y-m-d H:i:s", dbmaketime($gps->gps_time)+7*3600);
		$insert['geoalert_lat'] = $gps->lat;
		$insert['geoalert_lng'] = $gps->lng;
		$insert['geoalert_geofence'] = ($geofence === FALSE) ? 0 : $geofence->geofence_id;
		$insert['geofence_created'] = date("Y-m-d H:i:s");
		$insert['geofence_lastchecked'] = date("Y-m-d H:i:s", dbmaketime($t)+7*3600);
			
		$this->db->insert("geofence_alert", $insert);		
		
		if ($vehicle->user_alert_geo_email == 2)
		{
			print("----- User Tidak Inginkan Terima Email Alert \r\n");
			return;
		}
		
		$hp = valid_mobiles($vehicle->user_mobile);
		
		$t = dbmaketime($gps->gps_time)+7*3600;
			
		if ($direction == 2)
		{
			if ($geofence === FALSE)
			{
				$geofence_name = "geofence";				
			}
			else
			{
				$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			}
			
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		else
		{
			$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		
		$emails = get_valid_emails($vehicle->user_mail);
		if (is_array($emails) && count($emails))
		{
			foreach($emails as $email)
			{
				unset($mail);
			
				$mail['subject'] = sprintf("Geofence Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = $contentmail;
				$mail['dest'] = $email; 
				$mail['bcc'] = "owner@adilahsoft.com,report.lacakmobil@gmail.com";
				if ($vehicle->user_agent == 3)
				{
					$mail['sender'] = "support@gpsandalas.com";
				}
				else
				{
					$mail['sender'] = "support@lacak-mobil.com";
				}
			
				printf("----- sending email to %s %s %s\r\n", $mail['dest'], $mail['subject'], $mail['message']);
			
				lacakmobilmail($mail);
			}
		}

		if (! isON($vehicle->user_sms_notifikasi, 15))
		{
			print("----- User Disable SMS Notifikasi\r\n");
			return;
		}
		

		if ($hp === FALSE) 
		{
			print("----- Invalid user mobile\r\n");
			return;
		}
		
		if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}

		$this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}
		
		if ($vehicle->user_alert_geo_sms == 2)
		{
			print("----- User Tidak Inginkan Terima SMS Alert \r\n");
			return;
		}
		
		print("----- SMS DI DISABLE SEMENTARA WAKTU \r\n");
		return;
		
		 
		$params['device'] = "alert";
		$params['dest'] = $hp;	
		$xml = $this->load->view("sms/send", $params, true);
		$this->smsmodel->sendsms($xml);
		
	}
	
	function geofencealert_release_lemo()
	{
		$update['config_value'] = 0;
		
		$this->db->where("config_name", "geofencealertprocessing_lemo");
		$this->db->update("config", $update);		
	}

	//test
	function geofencealert_lemo_new()
	{
	
		$this->db->where("config_name", "geofencealertprocessing_lemo");
		$q = $this->db->get("config");
		
		if ($q->num_rows())
		{
			$row = $q->row();
			
			if ($row->config_value == 1) 
			{				
				$t = dbmaketime($row->config_lastmodified);
				$delta = mktime()-$t;
				
				if ($delta > 3600)
				{
					unset($update);
					$update['config_value'] = 0;
					$this->db->where("config_name", "geofencealertprocessing_lemo");
					$this->db->update("config", $update);	

					print("masih proses\r\n");
					return;				
				}
				
				print("masih proses\r\n");		
				return;						
			}
			
			unset($update);
			$update['config_value'] = 1;
			$this->db->where("config_name", "geofencealertprocessing_lemo");
			$this->db->update("config", $update);
		}
		else
		{
		
			unset($insert);
			$insert['config_name'] = "geofencealertprocessing_lemo";
			$insert['config_value'] = 1;
			$this->db->insert("config", $insert);
		}

		$this->db->distinct();
		$this->db->select("user.*, vehicle.*");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_user_id", 2110);
		$this->db->join("vehicle", "vehicle_device = geofence_vehicle");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->where("geofence_status", 1);
		$q = $this->db->get("geofence");
		
		
		if ($q->num_rows() == 0)
		{
			echo "tidak ada user yang setting geofence\r\n";
			$this->geofencealert_release_lemo();
			return;
		}
		
		$rows = $q->result();
		
		$i = 0;
		foreach($rows as $row)
		{			
			$jsonws = json_decode($row->vehicle_info);
			if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $row->vehicle_no);
				continue;
			}			
			
			printf("\r\n\r\n[%s] %02d %s %s\r\n", date("Y-m-d H:i:s"), $i, $row->vehicle_device, $row->vehicle_no);

			$this->dogeofencealert_lemo_new($row);
			$i++;			
		}
		
		$this->geofencealert_release_lemo_new();
	}

	function dogeofencealert_lemo_new($vehicle)
	{
	
		//ambil user yg setting geofence
		$devices = explode("@", $vehicle->vehicle_device);
		
		if (count($devices) < 2)
		{
			printf("----- Invalid device\r\n");
			return;
		}
			
		//ambil data terakhir alert geofence
		$this->db->limit(1);
		$this->db->order_by("geofence_lastchecked", "desc");		
		$this->db->where("geoalert_vehicle", $vehicle->vehicle_device);
		$q = $this->db->get("geofence_alert");
		
		if ($q->num_rows() == 0)
		{
			$lastdir = -1;
			$lastchecked = mktime()-7*3600;
			$lastgeofence = -1;
		}
		else
		{
			$row = $q->row();				
			$lastdir = $row->geoalert_direction;
			$lastchecked = dbmaketime($row->geofence_lastchecked);
			$geoalert_id = $row->geoalert_id;
			$lastgeofence = $row->geoalert_geofence;
			
			if ($lastchecked < (mktime()-2*24*3600-7*3600))
			{
				$lastchecked = mktime()-2*24*3600-7*3600;
			}
		}

		// ambil data gps
		if ($vehicle->vehicle_info)
		{
			$json = json_decode($vehicle->vehicle_info);
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];

					echo "database: ".$database."\n";
					$tablegps = $this->config->item("external_gpstable");					
					$tablegpshist = $this->config->item("external_gpstable_history");				
					//edited
					$tableinfo = $this->config->item("external_gpsinfotable");
					$this->db = $this->load->database($database, TRUE);
				}
			}			
		}
				

		if (! isset($tablegps))
		{		
			$table_hist = $this->config->item("table_hist");
			$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
			$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
		}
		
		if (! $tablegps) 
		{
			printf("----- tabel %s tidak ada\r\n", $tablegps);
			return;
		}
		
		$getgpsstart = mktime();			
		$tyesterday = mktime(-7, 59, 59, date('n', $getgpsstart), date('j', $getgpsstart), date('Y', $getgpsstart));		
		
		if ($lastchecked < $tyesterday)
		{
			printf("----- check to history\n");
			$sql = sprintf("SELECT * FROM (SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s') UNION SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s'))  tbl1 ORDER BY gps_time ASC "
				, $this->db->dbprefix
				, $tablegps
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)
				, $this->db->dbprefix
				, $tablegpshist
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)

			);
			$q = $this->db->query($sql);
		}
		else
		{	
			printf("----- check to current\n");
			$this->db->order_by("gps_time", "asc");					
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);	
		}
			
		if ($q->num_rows() == 0)
		{
			$this->db = $this->load->database("master", TRUE);
			
			printf("----- tidak ada sejak %s\r\n", date("d/m/Y H:i:s", $lastchecked));
			return;
		}

		$rowgps = $q->result();
		
		$this->db = $this->load->database("master", TRUE);
		
		printf("----- lama ambil data gps (%d): %d second\r\n", count($rowgps), mktime()-$getgpsstart);
			
		$checkarea = mktime();	
		$found = false;		
		
		for($i=0; $i < count($rowgps); $i++)
		{	
			
			$lat = $rowgps[$i]->gps_latitude_real;
			$lng = $rowgps[$i]->gps_longitude_real;
			
			$gps = $rowgps[$i];
			$gps->lat = $lat;
			$gps->lng = $lng;
				
			$sql = sprintf("
					SELECT 	* 
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_vehicle = '%s' )
							AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle->vehicle_device);

			$q = $this->db->query($sql);
			
			
			if ($q->num_rows() == 0)
			{
				if ($lastdir == 2) continue;

				$found = true;
				break;
			}
			
			if ($lastdir == 1) continue;
						
			$rowgeo = $q->row();					
			$found = true;
			
			break;
		}
		
		printf("----- lama check area: %d second\r\n", mktime()-$checkarea);
		
		if (! $found) 
		{
			unset($update);
			
			$t = dbmaketime($rowgps[count($rowgps)-1]->gps_time);
			$update['geofence_lastchecked'] = date("Y-m-d H:i:s", $t+7*3600);
			
			$this->db->where("geoalert_id", $geoalert_id);
			$this->db->update("geofence_alert", $update);			
			
			if ($lastdir == 1)
			{				
				printf("----- posisi kendaraan masih didalam area geofence\r\n");
				return;				
			}
			
			printf("----- posisi kendaraan masih di luar area geofence\r\n");
			return;
		}
		
		if ($lastdir != 2)
		{
			$this->db->where("geofence_status", 1);
			$this->db->where("geofence_id", $lastgeofence);
			$q = $this->db->get("geofence");
			
			if ($q->num_rows() > 0)
			{
				$rowgeo = $q->row();
				$this->addgeofencealert_lemo_new($vehicle, 2, $gps, $rowgeo);
			}
			else
			{
				$this->addgeofencealert_lemo_new($vehicle, 2, $gps, FALSE);
			}
			
			return;
		}
		
		$this->addgeofencealert_lemo_new($vehicle, 1, $gps, $rowgeo);
	}	

	
	function addgeofencealert_lemo_new($vehicle, $direction, $gps, $geofence)
	{
	
		unset($insert);
		$insert['geoalert_vehicle'] = $vehicle->vehicle_device;
		$insert['geoalert_direction'] = $direction;
		$insert['geoalert_time'] = date("Y-m-d H:i:s", dbmaketime($gps->gps_time)+7*3600);
		$insert['geoalert_lat'] = $gps->lat;
		$insert['geoalert_lng'] = $gps->lng;
		$insert['geoalert_geofence'] = ($geofence === FALSE) ? 0 : $geofence->geofence_id;
		$insert['geofence_created'] = date("Y-m-d H:i:s");
		$insert['geofence_lastchecked'] = $insert['geoalert_time'];
		$this->db->insert("geofence_alert", $insert);
		
		//
		if ($vehicle->user_alert_geo_email == 2)
		{
			print("----- User Tidak Inginkan Terima Email Alert \r\n");
			return;
		}
		
		$hp = valid_mobiles($vehicle->user_mobile);
		
		$t = dbmaketime($gps->gps_time)+7*3600;
			
		if ($direction == 2)
		{
			if ($geofence === FALSE)
			{
				$geofence_name = "geofence";				
			}
			else
			{
				$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			}
			
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		else
		{
			$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		
		$emails = get_valid_emails($vehicle->user_mail);
		if (is_array($emails) && count($emails))
		{
			foreach($emails as $email)
			{
				unset($mail);
			
				$mail['subject'] = sprintf("Geofence Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = $contentmail;
				$mail['dest'] = $email; 
				$mail['bcc'] = "owner@adilahsoft.com,report.lacakmobil@gmail.com";
				if ($vehicle->user_agent == 3)
				{
					$mail['sender'] = "support@gpsandalas.com";
				}
				else
				{
					$mail['sender'] = "support@lacak-mobil.com";
				}
			
				printf("----- sending email to %s %s %s\r\n", $mail['dest'], $mail['subject'], $mail['message']);
			
				lacakmobilmail($mail);
			}
		}

		if (! isON($vehicle->user_sms_notifikasi, 15))
		{
			print("----- User Disable SMS Notifikasi\r\n");
			return;
		}
		

		if ($hp === FALSE) 
		{
			print("----- Invalid user mobile\r\n");
			return;
		}
		
		if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}

		$this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}
		
		if ($vehicle->user_alert_geo_sms == 2)
		{
			print("----- User Tidak Inginkan Terima SMS Alert \r\n");
			return;
		}
		
		print("----- SMS DI DISABLE SEMENTARA WAKTU \r\n");
		return;
		
		 
		$params['device'] = "alert";
		$params['dest'] = $hp;	
		$xml = $this->load->view("sms/send", $params, true);
		$this->smsmodel->sendsms($xml);
		//
	}

	function geofencealert_release_lemo_new()
	{
		$update['config_value'] = 0;
		$this->db->where("config_name", "geofencealertprocessing");
		$this->db->update("config", $update);		
	}
	
	//for balrich
	function geofencealert_balrich()
	{ 
		$start_time = date("Y-m-d H:i:s");		
		printf("START !!"." ".$start_time. "\r\n");
		printf("====================== \r\n"); 
		
		$this->db->where("config_name", "geofencealertprocessing_balrich");
		$q = $this->db->get("config");
		
		if ($q->num_rows())
		{
			$row = $q->row();
			
			if ($row->config_value == 1) 
			{				
				$t = dbmaketime($row->config_lastmodified);
				$delta = mktime()-$t;
				
				if ($delta > 3600)
				{
					unset($update);
					
					$update['config_value'] = 0;
					
					$this->db->where("config_name", "geofencealertprocessing_balrich");
					$this->db->update("config", $update);	

					print("masih proses\r\n");
					return;				
				}
				
				print("masih proses\r\n");		
				return;						
			}
			
			unset($update);
			
			$update['config_value'] = 1;
			
			$this->db->where("config_name", "geofencealertprocessing_balrich");
			$this->db->update("config", $update);
		}
		else
		{
			unset($insert);
			
			$insert['config_name'] = "geofencealertprocessing_balrich";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}

		$this->db->distinct();
		$this->db->select("user.*, vehicle.*, company.*");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 >=",date("Ymd"));
		$this->db->where("vehicle_user_id", 1032); //user balrich
		$this->db->or_where("vehicle_user_id", 2331); //user it balrich
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->join("company", "vehicle_company = company_id", "left outer");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			echo "tidak ada user yang setting geofence\r\n";
			$this->geofencealert_release_balrich();
			return;
		}				
		
		$rows = $q->result();
		
		$i = 0;
		foreach($rows as $row)
		{			
			printf("\r\n\r\n[%s] %02d %s %s\r\n", date("Y-m-d H:i:s"), $i, $row->vehicle_device, $row->vehicle_no);

			$this->dogeofencealert_balrich($row);
			$i++;
		}

		$this->geofencealert_release_balrich();
	}
	
	function dogeofencealert_balrich($vehicle)
	{
		// ambil user yg setting geofence
		$devices = explode("@", $vehicle->vehicle_device);
		if (count($devices) < 2) 
		{
			printf("----- Invalid device\r\n");
			return;
		}
		// ambil data terakhir alert geofence
		$this->db->limit(1);
		$this->db->order_by("geofence_lastchecked", "desc");		
		$this->db->where("geoalert_vehicle", $vehicle->vehicle_device);
		$q = $this->db->get("geofence_alert_balrich");
		if ($q->num_rows() == 0)
		{
			$lastdir = -1;
			$lastchecked = mktime()-7*3600;
			$lastgeofence = -1;
		}
		else
		{
			$row = $q->row();				
			$lastdir = $row->geoalert_direction;
			$lastchecked = dbmaketime($row->geofence_lastchecked);
			$geoalert_id = $row->geoalert_id;
			$lastgeofence = $row->geoalert_geofence;
			
			if ($lastchecked < (mktime()-2*24*3600-7*3600))
			{
				$lastchecked = mktime()-2*24*3600-7*3600;
			}
		}

		// ambil data gps
		$json = json_decode($vehicle->vehicle_info);
		if ($vehicle->vehicle_info)
		{
			
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					echo "database: ".$database."\n";
					$tablegps = $this->config->item("external_gpstable");	
					$tablegpsinfo = $this->config->item("external_gpsinfotable");					
					$tablegpshist = $this->config->item("external_gpstable_history");
					$tablegpshistinfo = $this->config->item("external_gpsinfotable_history");
										
					$this->db = $this->load->database($database, TRUE);
				}
			}

			//Jika WebSocket
			if (isset($json->vehicle_ws)) 
			{
				$database = "gpshistory2";
				$tablegps = strtolower($devices[0]."@".$devices[1]."_gps");
				$tablegpsinfo = strtolower($devices[0]."@".$devices[1]."_info");
				$this->db = $this->load->database($database, TRUE);	
			} 			
		}			

		if (! isset($tablegps))
		{		
			$table_hist = $this->config->item("table_hist");
			$table_histinfos = $this->config->item("table_hist_info");
						
			$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
			$tablegpsinfo = $this->gpsmodel->getGPSInfoTable($vehicle->vehicle_type);
			$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
			$tablegpshistinfo = $table_histinfos[strtoupper($vehicle->vehicle_type)];
		}
		
		if (! $tablegps) 
		{
			printf("----- tabel %s tidak ada\r\n", $tablegps);
			return;
		}
		
		if (! $tablegpsinfo) 
		{
			printf("----- tabel %s info tidak ada\r\n", $tablegpsinfo);
			return;
		}
		
		$getgpsstart = mktime();			
		$tyesterday = mktime(-7, 59, 59, date('n', $getgpsstart), date('j', $getgpsstart), date('Y', $getgpsstart));		
		
		if($lastchecked < $tyesterday && (isset($json->vehicle_ws)))
		{
			printf("----- Websocket vehicle check to current\n");
			$this->db->join($tablegpsinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $vehicle->vehicle_device);
			$this->db->order_by("gps_time", "asc");					
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);	
		}
		else
		{	
			printf("----- check to current 1 \n");
			$this->db->join($tablegpsinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $vehicle->vehicle_device);
			$this->db->order_by("gps_time", "asc");					
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);			
		}
		
		if ($q->num_rows() == 0 && (isset($json->vehicle_ws)))
		{
			printf("----- check to current 2 \n"); 
			$this->db2 = $this->load->database("gpshistory2",true);
			$this->db2->join($tablegpsinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $vehicle->vehicle_device);
			$this->db2->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db2->where("gps_name", $devices[0]);
			$this->db2->where("gps_host", $devices[1]);
			$q = $this->db2->get($tablegps);			
		}
			
		if ($q->num_rows() == 0) 
		{
			$this->db = $this->load->database("master", TRUE);
			
			printf("----- tidak ada sejak %s\r\n", date("d/m/Y H:i:s", $lastchecked));
			return;
		}
			
		$rowgps = $q->result();
		
		$this->db = $this->load->database("master", TRUE);
		
		printf("----- lama ambil data gps (%d): %d second\r\n", count($rowgps), mktime()-$getgpsstart);
			
		$checkarea = mktime();	
		$found = false;		
		
		for($i=0; $i < count($rowgps); $i++)
		{	
			$lat = getLatitude($rowgps[$i]->gps_latitude, $rowgps[$i]->gps_ns);
			$lng = getLongitude($rowgps[$i]->gps_longitude, $rowgps[$i]->gps_ew);
			$lat_real = $rowgps[$i]->gps_latitude_real;
			$lng_real = $rowgps[$i]->gps_longitude_real;
			
			$door = substr($rowgps[$i]->gps_msg_ori, 79, 1); //door 1 == open ,else close
			$engine = substr($rowgps[$i]->gps_info_io_port, 4, 1); //engine 1 on, else off
			$speed = $rowgps[$i]->gps_speed;
			
			$gps = $rowgps[$i];
			$gps->lat = $lat;
			$gps->lng = $lng;
			$gps->lat_real = $lat_real;
			$gps->lng_real = $lng_real;
			
			$gps->door = $door;
			$gps->engine = $engine;
			
			if($gps->door == 1){
				$statusdoor = "OPEN";
			}else{
				$statusdoor = "CLOSE";
			}
			if($gps->engine == 1){
				$statusengine = "ON";
			}else{
				$statusengine = "OFF";
			}
			$gps->speed = $speed;
			
			if ($vehicle->vehicle_user_id == 2331){ //it balrich 
				$sql = sprintf("
					SELECT 	* 
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_user = 2331 )
							AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle->vehicle_device);
			}else{
				$sql = sprintf("
					SELECT 	* 
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_user = 1032 )
							AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle->vehicle_device);
			}
			

			$q = $this->db->query($sql);
			
			if ($q->num_rows() == 0)
			{
				if ($lastdir == 2) continue;

				$found = true;
				break;
			}
			
			if ($lastdir == 1) continue;
						
			$rowgeo = $q->row();					
			$found = true;
			
			break;
		}
		
		printf("----- lama check area: %d second\r\n", mktime()-$checkarea);
		
		if (! $found) 
		{
			unset($update);
			
			$t = dbmaketime($rowgps[count($rowgps)-1]->gps_time);
			$update['geofence_lastchecked'] = date("Y-m-d H:i:s", $t+7*3600);
			
			$this->db->where("geoalert_id", $geoalert_id);
			$this->db->update("geofence_alert_balrich", $update);			
			
			if ($lastdir == 1)
			{				
				printf("----- posisi kendaraan masih didalam area geofence\r\n");
				return;				
			}
			
			printf("----- posisi kendaraan masih di luar area geofence\r\n");
			return;
		}
		
		if ($lastdir != 2)
		{
			$this->db->where("geofence_status", 1);
			$this->db->where("geofence_id", $lastgeofence);
			$q = $this->db->get("geofence");
			
			if ($q->num_rows() > 0)
			{
				$rowgeo = $q->row();
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert_balrich($vehicle, 2, $gps, $rowgeo, $t, $statusdoor, $statusengine);
			}
			else
			{
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert_balrich($vehicle, 2, $gps, FALSE, $t, $statusdoor, $statusengine);
			}
			
			return;
		}
		$t = $rowgps[count($rowgps)-1]->gps_time;
		$this->addgeofencealert_balrich($vehicle, 1, $gps, $rowgeo, $t, $statusdoor, $statusengine);
	}	
	
	function addgeofencealert_balrich($vehicle, $direction, $gps, $geofence, $t, $statusdoor, $statusengine)
	{
		unset($insert);
			
		$insert['geoalert_vehicle'] = $vehicle->vehicle_device;
		$insert['geoalert_vehicle_company'] = $vehicle->vehicle_company;
		$insert['geoalert_vehicle_type'] = $vehicle->vehicle_type;
		$insert['geoalert_direction'] = $direction;
		$insert['geoalert_time'] = date("Y-m-d H:i:s", dbmaketime($gps->gps_time)+7*3600);
		$insert['geoalert_lat'] = $gps->lat;
		$insert['geoalert_lng'] = $gps->lng;
		$insert['geoalert_door'] = $gps->door;
		$insert['geoalert_engine'] = $gps->engine;
		$insert['geoalert_speed'] = $gps->speed;
		$insert['geoalert_geofence'] = ($geofence === FALSE) ? 0 : $geofence->geofence_id;
		$insert['geofence_created'] = date("Y-m-d H:i:s");
		$insert['geofence_lastchecked'] = date("Y-m-d H:i:s", dbmaketime($t)+7*3600);
			
		$this->db->insert("geofence_alert_balrich", $insert);		
		
		if ($vehicle->user_alert_geo_email == 2)
		{
			print("----- User Tidak Inginkan Terima Email Alert \r\n");
			return;
		}
		
		$hp = valid_mobiles($vehicle->user_mobile);
		
		$t = dbmaketime($gps->gps_time)+7*3600;

		if ($vehicle->vehicle_type == "T5DOOR"){
			if ($direction == 2) //keluar area geofence
			{
				if ($geofence === FALSE)
				{	
					$geofence_name = "geofence";
				}
				else
				{
					$geofence_names = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
					if(preg_match("/#/", $geofence_names)) {
						$geofence_rute = explode("#",$geofence_names);
						$geofence_name = "RUTE : ".$geofence_rute[1];
					}else{
						$geofence_name = $geofence_names;
					}
				}
				//GEOFENCE OUT DOOR
				$contentmail = sprintf("Pada %s, Kendaraan %s %s , Door Status: %s , Engine: %s, Speed: %s kph, Keluar dari Area %s, Coordinate: %s %s", date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $vehicle->company_name, $statusdoor, $statusengine, $gps->gps_speed, $geofence_name, $gps->lat_real, $gps->lng_real);
			}
			else
			{
				// GEOFENCE IN DOOR
					$geofence_names = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
					if(preg_match("/#/", $geofence_names)) {
						$geofence_rute = explode("#",$geofence_names);
						$geofence_name = "RUTE : ".$geofence_rute[1];
					}else{
						$geofence_name = $geofence_names;
					}
				$contentmail = sprintf("Pada %s, Kendaraan %s %s , Door Status: %s , Engine: %s, Speed: %s kph, Masuk Area %s, Coordinate: %s %s", date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $vehicle->company_name, $statusdoor, $statusengine, $gps->gps_speed, $geofence_name, $gps->lat_real, $gps->lng_real);
			}
		}else{
			if ($direction == 2) //keluar area geofence
			{
				if ($geofence === FALSE)
				{
					$geofence_name = "geofence";				
				}
				else
				{
					$geofence_names = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
					if(preg_match("/#/", $geofence_names)) {
						$geofence_rute = explode("#",$geofence_names);
						$geofence_name = "RUTE : ".$geofence_rute[1];
					}else{
						$geofence_name = $geofence_names;
					}
				}
				// GEOFENCE OUT
				$contentmail = sprintf("Pada %s, Kendaraan %s %s, Engine: %s, Speed: %s kph, Keluar dari Area %s, Coordinate: %s %s", date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $vehicle->company_name, $statusengine, $gps->gps_speed, $geofence_name, $gps->lat_real, $gps->lng_real);
			}
			else
			{	// GEOFENCE IN 
					$geofence_names = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
					if(preg_match("/#/", $geofence_names)) {
						$geofence_rute = explode("#",$geofence_names);
						$geofence_name = "RUTE : ".$geofence_rute[1];
					}else{
						$geofence_name = $geofence_names;
					}
				$contentmail = sprintf("Pada %s, Kendaraan %s %s, Engine: %s, Speed: %s kph, Masuk Area %s, Coordinate: %s %s", date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $vehicle->company_name, $statusengine, $gps->gps_speed, $geofence_name, $gps->lat_real, $gps->lng_real);
			}
		}

		$emails = get_valid_emails($vehicle->user_mail);
		if (is_array($emails) && count($emails))
		{
			foreach($emails as $email)
			{
				unset($mail);
			
				$mail['subject'] = sprintf("Balrich Geofence Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = $contentmail;
				$mail['dest'] = $email;
				
				//untuk mobil balrich cikarang
				if ($vehicle->vehicle_company == 65) {
					$mail['bcc'] = "gps.cikarang@balrich.co.id,report.lacakmobil@gmail.com";
				}else{
					$mail['bcc'] = "report.lacakmobil@gmail.com";
				}
				
				if ($vehicle->user_agent == 3)
				{
					$mail['sender'] = "support@gpsandalas.com";
				}
				else
				{
					$mail['sender'] = "support@lacak-mobil.com";
				}
			
				printf("----- sending email to %s %s %s\r\n", $mail['dest'], $mail['subject'], $mail['message']);
			
				lacakmobilmail($mail);			
			}
		}
	}
	
	function geofencealert_release_balrich()
	{
		$update['config_value'] = 0;
		
		$this->db->where("config_name", "geofencealertprocessing_balrich");
		$this->db->update("config", $update);

		$finish_time = date("Y-m-d H:i:s");
		
		printf("FINISH !!"." ".$finish_time. "\r\n");
		printf("====================== \r\n"); 		
	}
	
	
	function geofencealert_ssi()
	{
	
		$this->db->where("config_name", "geofencealertprocessing_ssi");
		$q = $this->db->get("config");
		
		if ($q->num_rows())
		{
			$row = $q->row();
			
			if ($row->config_value == 1) 
			{				
				$t = dbmaketime($row->config_lastmodified);
				$delta = mktime()-$t;
				
				if ($delta > 3600)
				{
					unset($update);
					$update['config_value'] = 0;
					$this->db->where("config_name", "geofencealertprocessing_ssi");
					$this->db->update("config", $update);	

					print("masih proses\r\n");
					return;				
				}
				
				print("masih proses\r\n");		
				return;						
			}
			
			unset($update);
			$update['config_value'] = 1;
			$this->db->where("config_name", "geofencealertprocessing_ssi");
			$this->db->update("config", $update);
		}
		else
		{
		
			unset($insert);
			$insert['config_name'] = "geofencealertprocessing_ssi";
			$insert['config_value'] = 1;
			$this->db->insert("config", $insert);
		}

		$this->db->distinct();
		$this->db->select("user.*, vehicle.*");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_user_id", 1933); //ssi
		//$this->db->join("vehicle", "vehicle_device = geofence_vehicle");
		$this->db->join("user", "user_id = vehicle_user_id");
		//$this->db->where("geofence_status", 1);
		$q = $this->db->get("vehicle");
		
		
		if ($q->num_rows() == 0)
		{
			echo "tidak ada user yang setting geofence\r\n";
			$this->geofencealert_release_ssi();
			return;
		}
		
		$rows = $q->result();
		
		$i = 0;
		foreach($rows as $row)
		{			
			$jsonws = json_decode($row->vehicle_info);
			if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $row->vehicle_no);
				continue;
			}			
			
			printf("\r\n\r\n[%s] %02d %s %s\r\n", date("Y-m-d H:i:s"), $i, $row->vehicle_device, $row->vehicle_no);

			$this->dogeofencealert_ssi($row);
			$i++;			
		}
		
		$this->geofencealert_release_ssi();
	}

	function dogeofencealert_ssi($vehicle)
	{
	
		//ambil user yg setting geofence
		$devices = explode("@", $vehicle->vehicle_device);
		
		if (count($devices) < 2)
		{
			printf("----- Invalid device\r\n");
			return;
		}
			
		//ambil data terakhir alert geofence
		$this->db->limit(1);
		$this->db->order_by("geofence_lastchecked", "desc");		
		$this->db->where("geoalert_vehicle", $vehicle->vehicle_device);
		$q = $this->db->get("geofence_alert");
		
		if ($q->num_rows() == 0)
		{
			$lastdir = -1;
			$lastchecked = mktime()-7*3600;
			$lastgeofence = -1;
		}
		else
		{
			$row = $q->row();				
			$lastdir = $row->geoalert_direction;
			$lastchecked = dbmaketime($row->geofence_lastchecked);
			$geoalert_id = $row->geoalert_id;
			$lastgeofence = $row->geoalert_geofence;
			
			if ($lastchecked < (mktime()-2*24*3600-7*3600))
			{
				$lastchecked = mktime()-2*24*3600-7*3600;
			}
		}

		// ambil data gps
		if ($vehicle->vehicle_info)
		{
			$json = json_decode($vehicle->vehicle_info);
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');
			
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];

					echo "database: ".$database."\n";
					$tablegps = $this->config->item("external_gpstable");					
					$tablegpshist = $this->config->item("external_gpstable_history");				
					//edited
					$tableinfo = $this->config->item("external_gpsinfotable");
					$this->db = $this->load->database($database, TRUE);
				}
			}			
		}
				

		if (! isset($tablegps))
		{		
			$table_hist = $this->config->item("table_hist");
			$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
			$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
		}
		
		if (! $tablegps) 
		{
			printf("----- tabel %s tidak ada\r\n", $tablegps);
			return;
		}
		
		$getgpsstart = mktime();			
		$tyesterday = mktime(-7, 59, 59, date('n', $getgpsstart), date('j', $getgpsstart), date('Y', $getgpsstart));		
		
		if ($lastchecked < $tyesterday)
		{
			printf("----- check to history\n");
			$sql = sprintf("SELECT * FROM (SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s') UNION SELECT * FROM %s%s WHERE (gps_name = '%s') AND (gps_host = '%s') AND (gps_time > '%s'))  tbl1 ORDER BY gps_time ASC "
				, $this->db->dbprefix
				, $tablegps
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)
				, $this->db->dbprefix
				, $tablegpshist
				, $devices[0]
				, $devices[1]
				, date("Y-m-d H:i:s", $lastchecked-7*3600)

			);
			$q = $this->db->query($sql);
		}
		else
		{	
			printf("----- check to current\n");
			$this->db->order_by("gps_time", "asc");					
			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lastchecked-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegps);	
		}
			
		if ($q->num_rows() == 0)
		{
			$this->db = $this->load->database("master", TRUE);
			
			printf("----- tidak ada sejak %s\r\n", date("d/m/Y H:i:s", $lastchecked));
			return;
		}

		$rowgps = $q->result();
		
		$this->db = $this->load->database("master", TRUE);
		
		printf("----- lama ambil data gps (%d): %d second\r\n", count($rowgps), mktime()-$getgpsstart);
			
		$checkarea = mktime();	
		$found = false;		
		
		for($i=0; $i < count($rowgps); $i++)
		{	
			
			$lat = $rowgps[$i]->gps_latitude_real;
			$lng = $rowgps[$i]->gps_longitude_real;
			
			$gps = $rowgps[$i];
			$gps->lat = $lat;
			$gps->lng = $lng;
				
			$sql = sprintf("
					SELECT 	* 
					FROM 	%sgeofence 
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
							AND (geofence_user = '1933' )
							AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle->vehicle_device);

			$q = $this->db->query($sql);
			
			
			if ($q->num_rows() == 0)
			{
				if ($lastdir == 2) continue;

				$found = true;
				break;
			}
			
			if ($lastdir == 1) continue;
						
			$rowgeo = $q->row();					
			$found = true;
			
			break;
		}
		
		printf("----- lama check area: %d second\r\n", mktime()-$checkarea);
		
		if (! $found) 
		{
			unset($update);
			
			$t = dbmaketime($rowgps[count($rowgps)-1]->gps_time);
			$update['geofence_lastchecked'] = date("Y-m-d H:i:s", $t+7*3600);
			
			$this->db->where("geoalert_id", $geoalert_id);
			$this->db->update("geofence_alert", $update);			
			
			if ($lastdir == 1)
			{				
				printf("----- posisi kendaraan masih didalam area geofence\r\n");
				return;				
			}
			
			printf("----- posisi kendaraan masih di luar area geofence\r\n");
			return;
		}
		
		if ($lastdir != 2)
		{
			$this->db->where("geofence_status", 1);
			$this->db->where("geofence_id", $lastgeofence);
			$q = $this->db->get("geofence");
			
			if ($q->num_rows() > 0)
			{
				$rowgeo = $q->row();
				$this->addgeofencealert_ssi($vehicle, 2, $gps, $rowgeo);
			}
			else
			{
				$this->addgeofencealert_ssi($vehicle, 2, $gps, FALSE);
			}
			
			return;
		}
		
		$this->addgeofencealert_ssi($vehicle, 1, $gps, $rowgeo);
	}	

	
	function addgeofencealert_ssi($vehicle, $direction, $gps, $geofence)
	{
	
		unset($insert);
		$insert['geoalert_vehicle'] = $vehicle->vehicle_device;
		$insert['geoalert_direction'] = $direction;
		$insert['geoalert_time'] = date("Y-m-d H:i:s", dbmaketime($gps->gps_time)+7*3600);
		$insert['geoalert_lat'] = $gps->lat;
		$insert['geoalert_lng'] = $gps->lng;
		$insert['geoalert_geofence'] = ($geofence === FALSE) ? 0 : $geofence->geofence_id;
		$insert['geofence_created'] = date("Y-m-d H:i:s");
		$insert['geofence_lastchecked'] = $insert['geoalert_time'];
		$this->db->insert("geofence_alert", $insert);
		
		//
		if ($vehicle->user_alert_geo_email == 2)
		{
			print("----- User Tidak Inginkan Terima Email Alert \r\n");
			return;
		}
		
		$hp = valid_mobiles($vehicle->user_mobile);
		
		$t = dbmaketime($gps->gps_time)+7*3600;
			
		if ($direction == 2)
		{
			if ($geofence === FALSE)
			{
				$geofence_name = "geofence";				
			}
			else
			{
				$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			}
			
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_OUT"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		else
		{
			$geofence_name = strlen($geofence->geofence_name) ? $geofence->geofence_name : "geofence";
			$params['content'] = sprintf($this->config->item("SMS_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name, $vehicle->user_login, $vehicle->vehicle_no);
			$contentmail = sprintf($this->config->item("MAIL_ALERT_GEOFENCE_IN"), date("d/m/Y H:i:s", $t), $vehicle->vehicle_no, $geofence_name);
		}
		
		$emails = get_valid_emails($vehicle->user_mail);
		if (is_array($emails) && count($emails))
		{
			foreach($emails as $email)
			{
				unset($mail);
			
				$mail['subject'] = sprintf("Geofence Alert: %s", $vehicle->vehicle_no);
				$mail['message'] = $contentmail;
				$mail['dest'] = $email; 
				$mail['bcc'] = "report.lacakmobil@gmail.com";
				if ($vehicle->user_agent == 3)
				{
					$mail['sender'] = "support@gpsandalas.com";
				}
				else
				{
					$mail['sender'] = "support@lacak-mobil.com";
				}
			
				printf("----- sending email to %s %s %s\r\n", $mail['dest'], $mail['subject'], $mail['message']);
			
				lacakmobilmail($mail);
			}
		}

		if (! isON($vehicle->user_sms_notifikasi, 15))
		{
			print("----- User Disable SMS Notifikasi\r\n");
			return;
		}
		

		if ($hp === FALSE) 
		{
			print("----- Invalid user mobile\r\n");
			return;
		}
		
		if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}

		$this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}
		
		if ($vehicle->user_alert_geo_sms == 2)
		{
			print("----- User Tidak Inginkan Terima SMS Alert \r\n");
			return;
		}
		
		print("----- SMS DI DISABLE SEMENTARA WAKTU \r\n");
		return;
		
		 
		$params['device'] = "alert";
		$params['dest'] = $hp;	
		$xml = $this->load->view("sms/send", $params, true);
		$this->smsmodel->sendsms($xml);
		//
	}

	function geofencealert_release_ssi()
	{
		$update['config_value'] = 0;
		$this->db->where("config_name", "geofencealertprocessing_ssi");
		$this->db->update("config", $update);		
	}
	
	function parkalert_balrich()
	{
		$this->db->where("config_name", "parkalert_balrich");
		$q = $this->db->get("config");
		
		$rowlast = $q->row();
		$lastrunning = dbmaketime($rowlast->config_value);
		
		echo "config: ".date("d/m/Y H:i:s", $lastrunning)."\r\n";
		
		$this->db->order_by("logs_created", "asc");
		$this->db->where("logs_type", "parkalert_balrich");
		$q = $this->db->get("logs");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$parkalert[$rows[$i]->logs_content] = dbmaketime($rows[$i]->logs_created);
		}

		$this->db->where("vehicle_maxparking >", 0);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id ", 1032);// balrich.logistic
		$this->db->or_where("vehicle_user_id ", 2331);// it.balrich
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();

		unset($update);
		$update['config_value'] = date("Y-m-d H:i:s", mktime());
		$this->db->where("config_name", "parkalert_balrich");
		$this->db->update("config", $update);

		for($i=0; $i < count($rows); $i++)
		{
			$jsonws = json_decode($rows[$i]->vehicle_info);
			if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $rows[$i]->vehicle_no);
				continue;
			}			


			printf("[%s] %04d %s %s %sm...\r\n", date("Ymd H:i:s"), $i+1, $rows[$i]->user_login, $rows[$i]->vehicle_no, $rows[$i]->vehicle_maxparking);
			
			$maxpark = $rows[$i]->vehicle_maxparking*60;
			
			if ($rows[$i]->vehicle_info)
			{
				$json = json_decode($rows[$i]->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$tablegps = $this->config->item("external_gpstable");					
											
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}

			if (! isset($tablegps))
			{
				$tablegps = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);
			}
			
			$devices = explode("@", $rows[$i]->vehicle_device);
			
			$this->db->order_by("gps_time", "asc");				
			$this->db->where("gps_time >=", date("Y-m-d H:i:s", $lastrunning-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);			
			$q = $this->db->get($tablegps);
			
			$this->db = $this->load->database("master", TRUE);
			
			if ($q->num_rows() == 0) continue;
			
			$rowgps = $q->result();
			
			printf("=== %d record\r\n", count($rowgps)); 
			
			$this->db->where("parkir_device", $rows[$i]->vehicle_device);
			$q = $this->db->get("parkir");
			
			if ($q->num_rows() == 0)
			{
				$lastlat = -999999;
				$lastlng = -999999;
				$lastpark = 0;
			}
			else
			{
				$rowparkir = $q->row();
				
				$lastlat = $rowparkir->parkir_lat;
				$lastlng = $rowparkir->parkir_lng;
				$lastpark = dbmaketime($rowparkir->parkir_time);
				
				printf("=== last: %s,%s %s\r\n", $lastlat, $lastlng, date("d/m/Y H:i:s", $lastpark+7*3600)); 
			}
			
			// looping data
						
			for($j=0; $j < count($rowgps); $j++)
			{
				$lat = sprintf("%.4f", getLatitude($rowgps[$j]->gps_latitude, $rowgps[$j]->gps_ns));
				$lng = sprintf("%.4f", getLongitude($rowgps[$j]->gps_longitude, $rowgps[$j]->gps_ew));
				
				//add geofence condition
				$latreal = $rowgps[$j]->gps_latitude_real;
				$lngreal = $rowgps[$j]->gps_longitude_real;
				
				$gps = $rowgps[$j];
				$gps->lat = $latreal;
				$gps->lng = $lngreal;
	
				if ($rows[$i]->vehicle_user_id == 2331){ //it balrich 
					$sql = sprintf("
						SELECT 	* 
						FROM 	%sgeofence 
						WHERE 	TRUE
								AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
								AND (geofence_user = 2331 )
								AND (geofence_status = 1)
						LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $rows[$i]->vehicle_device);
				}else{
					$sql = sprintf("
						SELECT 	* 
						FROM 	%sgeofence 
						WHERE 	TRUE
								AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
								AND (geofence_user = 1032 )
								AND (geofence_status = 1)
						LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $rows[$i]->vehicle_device);
				}

				$q = $this->db->query($sql);
				$rowgeo = $q->row();
				if (count($rowgeo) > 0){
					$geofence = $rowgeo->geofence_name;
				}else{
					$geofence = "";
				}
				//end add geofence

				if (($lat == $lastlat) && ($lastlng == $lng)) 
				{					
					if (($j+1) == count($rowgps))
					{
						$t = dbmaketime($rowgps[$j]->gps_time);
						$parklength = $t-$lastpark;						
						
						printf("=== current=%d s <-> max=%d s\r\n", $parklength, $maxpark);
						
						if ($parklength < $maxpark)
						{
							continue;
						}
						
						// alert park
						
						$lastpark = $t;
						$this->doalertpark_balrich($rows[$i], $rowgps[$j], $parklength, $parkalert, $geofence, $latreal, $lngreal);
					}
					
					continue;
				}			
				
				$lastlat = $lat;
				$lastlng = $lng;
				
				$prevpark = $lastpark;
				$lastpark = dbmaketime($rowgps[$j]->gps_time);
				
				if ($j == 0)
				{					
					continue;
				}
				
				$t = dbmaketime($rowgps[$j-1]->gps_time);
				$parklength = $t-$prevpark;
				
				printf("=== current=%d s <-> max=%d s\r\n", $parklength, $maxpark);
				
				if ($parklength < $maxpark)
				{
					continue;
				}
				
				// alert park
				
				$this->doalertpark_balrich($rows[$i], $rowgps[$j-1], $parklength, $parkalert, $geofence, $latreal, $lngreal);
			}
			
			$this->db->where("parkir_device", $rows[$i]->vehicle_device);
			$total = $this->db->count_all_results("parkir");
			
			unset($update);
			
			$update['parkir_device'] = $rows[$i]->vehicle_device;
			$update['parkir_lat'] = $lastlat;
			$update['parkir_lng'] = $lastlng;
			$update['parkir_time'] = date("Y-m-d H:i:s", $lastpark);
			
			if ($total > 0)
			{
				$this->db->where("parkir_device", $rows[$i]->vehicle_device);
				$this->db->update("parkir", $update);
			}
			else
			{
				$this->db->insert("parkir", $update);
			}
		}
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !!"." ".$finish_time. "\r\n");
	}
	
	function doalertpark_balrich($vehicle, $gps, $parklength, $parkalert, $geofence, $latreal, $lngreal)
	{
		
		$isparkalert = false;
		$t = dbmaketime($gps->gps_time);

		if (! isset($parkalert[$vehicle->vehicle_device]))
		{
			$parkalert[$vehicle->vehicle_device] = $t;
			$isparkalert = true;
		}
		else
		{
			$delta = mktime() - $parkalert[$vehicle->vehicle_device];
			$isparkalert = $delta > 3600;
		}
		
		if (! $isparkalert) 
		{
			printf("=== alerted at %s\r\n", date("d/m/Y H:i:s", $parkalert[$vehicle->vehicle_device]));
			return;
		}

		unset($insert);
		
		$insert['parkir_alert_device'] = $vehicle->vehicle_device;
		$insert['parkir_alert_time'] = date("Y-m-d H:i:s", $t);
		$insert['parkir_alert_length'] = round($parklength/60);
		$insert['parkir_alert_max'] = $vehicle->vehicle_maxparking;
		$insert['parkir_alert_created'] = date("Y-m-d H:i:s");
		
		$this->db->insert("parkir_alert", $insert);
		
		$emails = get_valid_emails($vehicle->user_mail);
		
		if($geofence == ""){
			if ($vehicle->user_alert_parking_email == 1)
			{
				foreach($emails as $email)
				{
					unset($mail);
				
					$mail['subject'] = sprintf("Balrich Parkir Alert: %s", $vehicle->vehicle_no);
					$mail['message'] = sprintf("Pada %s, lama parkir %s adalah %s menit diluar area geofence pada koordinat : %s %s dimana ambang batas lama parkir adalah %s menit", date("d/m/Y H:i:s", $t+7*3600), $vehicle->vehicle_no, round($parklength/60), $latreal, $lngreal, $vehicle->vehicle_maxparking);
					$mail['dest'] = $email; 
					
					//untuk mobil balrich cikarang
					if ($vehicle->vehicle_company == 65) {
						$mail['bcc'] = "gps.cikarang@balrich.co.id,report.lacakmobil@gmail.com";
					}else{
						$mail['bcc'] = "report.lacakmobil@gmail.com";
					}
					$mail['sender'] = "support@lacak-mobil.com";
					lacakmobilmail($mail);
					printf("=== send email ok\r\n");				
				}
			}
		}else{
			printf("=== masih di dalam geofence %s \r\n", $geofence);
		}
		
		
		if (! isON($vehicle->user_sms_notifikasi, 13))
		{
			printf("=== notifikasi off\r\n");
			return;
		}
		
		$hp = valid_mobiles($vehicle->user_mobile);
		if ($hp === FALSE) 
		{
			printf("=== invalid mobile no: %s\r\n", $vehicle->user_mobile);
			return;
		}
		
		
		if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}
		
		if ($vehicle->user_alert_parking_sms == 2)
		{
			print("----- user tidak inginkan terima alert\r\n");
			return;
		}
		
		$this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}	
			
		$content = sprintf($this->config->item("SMS_ALERT_MAX_PARK"), date("d/m/Y H:i:s", $t+7*3600), $vehicle->vehicle_no, round($parklength/60), $vehicle->vehicle_maxparking);
						
		/* foreach($hp as $h)
		{				
			$xml = sprintf("%s\1%s\alat", $h, $content);
			$this->smsmodel->sendsms($xml);
			
			printf("%s\r\n", $xml);
		} */								
							
		$parkalert[$vehicle->vehicle_device] = mktime();

		unset($update);
							
		$update['logs_created'] = date("Y-m-d H:i:s");
		
		$this->db->where("logs_content", $vehicle->vehicle_device);
		$this->db->where("logs_type", "parkalert_balrich");
		$this->db->update("logs", $update);
		
		if ($this->db->affected_rows() == 0)
		{
			unset($insert);
			
			$insert['logs_created'] = date("Y-m-d H:i:s");
			$insert['logs_content'] = $vehicle->vehicle_device;
			$insert['logs_type'] = "parkalert_balrich";
			
			$this->db->insert("logs", $insert);
		}
		
	}
	
	function speedalert_balrich()
	{
		
		//date_default_timezone_set('Asia/Jakarta');
		
		$this->db->where("config_name", "lastspeed_balrich");
		$q = $this->db->get("config");
		
		$rowlast = $q->row();
		$lastrunning = dbmaketime($rowlast->config_value)-7*3600;
		
		$this->db->order_by("logs_created", "asc");
		$this->db->where("logs_type", "speedalert_balrich");
		$q = $this->db->get("logs");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$speedalert[$rows[$i]->logs_content] = dbmaketime($rows[$i]->logs_created);
		}

		$this->db->where("vehicle_maxspeed >", 0);
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_user_id", "1032");
		$this->db->or_where("vehicle_user_id", "2331");
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();

		unset($update);
		$update['config_value'] = date("Y-m-d H:i:s", mktime());
		$this->db->where("config_name", "lastspeed_balrich");
		$this->db->update("config", $update);

		for($i=0; $i < count($rows); $i++)
		{
			$devices = explode("@", $rows[$i]->vehicle_device);
			$jsonws = json_decode($rows[$i]->vehicle_info);
			/* if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $rows[$i]->vehicle_no);
				continue;
			} */
			
			printf("[%s] %04d %s %s %skph...\r\n", date("Ymd H:i:s"), $i+1, $rows[$i]->user_login, $rows[$i]->vehicle_no, $rows[$i]->vehicle_maxspeed);
			
			$hp = valid_mobiles($rows[$i]->user_mobile);			

			if ($rows[$i]->vehicle_info)
			{
				$json = json_decode($rows[$i]->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$tablegps = $this->config->item("external_gpstable");					
						$this->db = $this->load->database($database, TRUE);
					}
				}
	
			}

			//Jika WebSocket
				if (isset($jsonws->vehicle_ws)) 
				{
					$database = "gpshistory";
					if ($devices[1] == "T5")
					{
						$tablegps = $devices[0]."@t5_gps";
						$tablegpshist = $devices[0]."@t5_gps";
						$this->db = $this->load->database($database, TRUE);						
					}
				} 		
			
			if (! isset($tablegps))
			{					
				$tablegps = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);
				$devices = explode("@", $rows[$i]->vehicle_device);
			}
			
			$this->db->order_by("gps_time", "desc");				
			$this->db->where("gps_time >=", date("Y-m-d H:i:s", $lastrunning-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$this->db->where("gps_speed*1.852 >=", $rows[$i]->vehicle_maxspeed, false);
			
			$q = $this->db->get($tablegps);
			
			
			
			if ($q->num_rows() > 0) 
			{				
				$rowgps = $q->result();
				$this->db = $this->load->database("master", TRUE);
				for($j=0; $j < count($rowgps); $j++)
				{
					$row = $rowgps[$j];
					
					$t = dbmaketime($row->gps_time)+7*3600;
					$device = sprintf("%s@%s", $row->gps_name, $row->gps_host);
					$isspeedalert = false;
					
					if (! isset($speedalert[$device]))
					{
						$speedalert[$device] = $t;
						$isspeedalert = true;
					}
					else
					{
						$delta = $t - $speedalert[$device];
						$isspeedalert = $delta > 3600;
					}
					
					
					if ($isspeedalert)
					{
						// send sms max alert
						
						unset($insert);
						$myposition = $this->getPosition($row->gps_longitude, $row->gps_ew, $row->gps_latitude, $row->gps_ns);
						
						$insert['speed_alert_device'] = $rows[$i]->vehicle_device;
						$insert['speed_alert_time'] = date("Y-m-d H:i:s", $t);
						$insert['speed_alert_speed'] = $row->gps_speed*1.852;
						$insert['speed_alert_max'] = $rows[$i]->vehicle_maxspeed;
						$insert['speed_alert_created'] = date("Y-m-d H:i:s");
						
						$this->db->insert("speed_alert", $insert);
						
						unset($insert);
						
						$insert['logs_type'] = "speedalert_balrich";
						$insert['logs_created'] = date("Y-m-d H:i:s");
						$insert['logs_content '] = $device;
						
						$this->db->insert("logs", $insert);
						
						$emails = get_valid_emails($rows[$i]->user_mail);
						
						if ($rows[$i]->user_alert_speed_email == 1)
						{
							foreach($emails as $email)
							{
								unset($mail);
							
								$myspeed = $row->gps_speed*1.852;
								$exspeed = explode(".",$myspeed);
							
								if (isset($exspeed[0]))
								{
									$fixspeed = $exspeed[0];
								}
								else
								{
									$fixspeed = $myspeed;
								}
							
								$mymessage = sprintf($this->config->item("MAIL_ALERT_MAX_SPEED"), date("d/m/Y H:i:s", $t), $rows[$i]->vehicle_no, $fixspeed);
								$mypos = $myposition->display_name;
							
								$mail['subject'] = sprintf("Balrich Speed Alert: %s", $rows[$i]->vehicle_no);
								$mail['message'] = $mymessage.$mypos;
								$mail['dest'] = $email;
								//khusus balrich cikarang
								if ($rows[$i]->vehicle_company == 65) {
									$mail['bcc'] = "gps.cikarang@balrich.co.id,report.lacakmobil@gmail.com";
								}else{
									$mail['bcc'] = "report.lacakmobil@gmail.com";
								}
								
								$mail['sender'] = "support@lacak-mobil.com";
							
								lacakmobilmail($mail);
								print("----- send email ok\r\n");
							}
						}
						
						if ($rows[$i]->user_payment_period >= 12)
						{
							print("----- user tahunan tidak diperbolehkan\r\n");
							
							$speedalert[$device] = mktime();
							continue;
						}
						
						if ($rows[$i]->user_alert_speed_sms == 2)
						{
							print("----- User Tidak Inginkan Terima SMS Alert\r\n");
							
							$speedalert[$device] = mktime();
							continue;
						}
						
						$this->db->where("agent_pss", 1);
						$this->db->where("agent_id", $rows[$i]->user_agent);
						$q = $this->db->get("agent");

						if ($q->num_rows() == 0)
						{
							print("----- agent tidak diijinkan\r\n");
							
							$speedalert[$device] = mktime();
							continue;
						}						
						
						if (($hp !== FALSE) && isON($rows[$i]->user_sms_notifikasi, 14))
						{		

							$myspeed = $row->gps_speed*1.852;
							$exspeed = explode(".",$myspeed);
							
							if (isset($exspeed[0]))
							{
								$fixspeed = $exspeed[0];
							}
							else
							{
								$fixspeed = $myspeed;
							}
							
							$mymessage = sprintf($this->config->item("SMS_ALERT_MAX_SPEED"), date("d/m/Y H:i:s", $t), $rows[$i]->vehicle_no, $fixspeed);
							$mypos = $myposition->display_name;
							
							$params["device"] = "alert";			
							$params['content'] = $mymessage.$mypos;
							$params['dest'] = $hp;	
							$xml = $this->load->view("sms/send", $params, true);
							
							$this->smsmodel->sendsms($xml);	
						}
						
						$speedalert[$device] = mktime();
					}
				}
			}
		}
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !!"." ".$finish_time. "\r\n");
	}
	
	function dooralert_balrich()
	{
		$this->db->where("config_name", "dooralert_balrich");
		$q = $this->db->get("config");
		
		$rowlast = $q->row();
		$lastrunning = dbmaketime($rowlast->config_value);
		
		echo "config: ".date("d/m/Y H:i:s", $lastrunning)."\r\n";
		
		$this->db->order_by("logs_created", "asc");
		$this->db->where("logs_type", "dooralert_balrich");
		$q = $this->db->get("logs");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$dooralert[$rows[$i]->logs_content] = dbmaketime($rows[$i]->logs_created);
		}

		$this->db->where("vehicle_type", "T5DOOR");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id ", 1032);// balrich.logistic
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->join("company", "company_id = vehicle_company");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();

		unset($update);
		$update['config_value'] = date("Y-m-d H:i:s", mktime());
		$this->db->where("config_name", "dooralert_balrich");
		$this->db->update("config", $update);

		for($i=0; $i < count($rows); $i++)
		{
			$jsonws = json_decode($rows[$i]->vehicle_info);
			if (isset($jsonws->vehicle_ws)) 
			{
				printf("skip web socket: %s", $rows[$i]->vehicle_no);
				continue;
			}			

			$vehiclemaxdoor = 1; //menit
			
			printf("[%s] %04d %s %s %sm...\r\n", date("Ymd H:i:s"), $i+1, $rows[$i]->user_login, $rows[$i]->vehicle_no, $vehiclemaxdoor);
			
			//$maxpark = $rows[$i]->vehicle_maxparking*60;
			$maxdoor = $vehiclemaxdoor * 60;
			
			/* if ($rows[$i]->vehicle_info)
			{
				$json = json_decode($rows[$i]->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$tablegps = $this->config->item("external_gpstable");					
											
						$this->db = $this->load->database($database, TRUE);
					}
				}			
			}

			if (! isset($tablegps))
			{
				$tablegps = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);
			}			
			
			*/
			
			//ambil data gps new
			$json = json_decode($rows[$i]->vehicle_info);
			if ($rows[$i]->vehicle_info)
			{
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
				
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						echo "database: ".$database."\n";
						$tablegps = $this->config->item("external_gpstable");	
						$tablegpsinfo = $this->config->item("external_gpsinfotable");					
						$tablegpshist = $this->config->item("external_gpstable_history");
						$tablegpshistinfo = $this->config->item("external_gpsinfotable_history");
											
						$this->dbgps = $this->load->database($database, TRUE);
					}
				}

				//Jika WebSocket
				if (isset($json->vehicle_ws)) 
				{
					$database = "gpshistory2";
					$tablegps = strtolower($devices[0]."@".$devices[1]."_gps");
					$tablegpsinfo = strtolower($devices[0]."@".$devices[1]."_info");
					$this->dbgps = $this->load->database($database, TRUE);	
				}  		
			}
			if (! isset($tablegps))
			{		
				$table_hist = $this->config->item("table_hist");
				$table_histinfos = $this->config->item("table_hist_info");
							
				$tablegps = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);
				$tablegpsinfo = $this->gpsmodel->getGPSInfoTable($rows[$i]->vehicle_type);
				$tablegpshist = $table_hist[strtoupper($rows[$i]->vehicle_type)];
				$tablegpshistinfo = $table_histinfos[strtoupper($rows[$i]->vehicle_type)];
			}
			
			if (! $tablegps) 
			{
				printf("----- tabel %s tidak ada\r\n", $tablegps);
				return;
			}
			
			if (! $tablegpsinfo) 
			{
				printf("----- tabel %s info tidak ada\r\n", $tablegpsinfo);
				return;
			}
			
			$devices = explode("@", $rows[$i]->vehicle_device);
			
			if(isset($json->vehicle_ws))
			{
				printf("----- Websocket vehicle check to current\n");
				$this->dbgps->join($tablegpsinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbgps->where_in("gps_info_device", $rows[$i]->vehicle_device);
				$this->dbgps->order_by("gps_time", "asc");					
				$this->dbgps->where("gps_time >", date("Y-m-d H:i:s", $lastrunning-7*3600));
				$this->dbgps->where("gps_name", $devices[0]);
				$this->dbgps->where("gps_host", $devices[1]);
				$qgps = $this->dbgps->get($tablegps);	
			}
			else
			{	
				printf("----- check to current 1 \n");
				$this->dbgps->join($tablegpsinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbgps->where_in("gps_info_device", $rows[$i]->vehicle_device);
				$this->dbgps->order_by("gps_time", "asc");					
				$this->dbgps->where("gps_time >", date("Y-m-d H:i:s", $lastrunning-7*3600));
				$this->dbgps->where("gps_name", $devices[0]);
				$this->dbgps->where("gps_host", $devices[1]);
				$qgps = $this->dbgps->get($tablegps);			
			}
			
			if ($q->num_rows() == 0 && (isset($json->vehicle_ws)))
			{
				printf("----- check to current 2 \n"); 
				$this->db2 = $this->load->database("gpshistory2",true);
				$this->db2->join($tablegpsinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->db2->where_in("gps_info_device", $rows[$i]->vehicle_device);
				$this->db2->where("gps_time >", date("Y-m-d H:i:s", $lastrunning-7*3600));
				$this->db2->where("gps_name", $devices[0]);
				$this->db2->where("gps_host", $devices[1]);
				$qgps = $this->db2->get($tablegps);			
			}
			
			//end new ambil data gps
			
			/* $devices = explode("@", $rows[$i]->vehicle_device);
			
			$this->db->order_by("gps_time", "asc");				
			$this->db->where("gps_time >=", date("Y-m-d H:i:s", $lastrunning-7*3600));
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);			
			$q = $this->db->get($tablegps);
			
			$this->db = $this->load->database("master", TRUE); */
			
			if ($qgps->num_rows() == 0) continue;
			
			$rowgps = $qgps->result();
			
			printf("=== %d record\r\n", count($rowgps)); 
			
			//select table webtracking door
			$this->db->where("door_device", $rows[$i]->vehicle_device);
			$q = $this->db->get("door");
			
			if ($q->num_rows() == 0)
			{
				$lastlat = -999999;
				$lastlng = -999999;
				$lastdoor = 0;
				$lastdoorstat = "";
			}
			else
			{
				$rowdoor = $q->row();
				
				$lastlat = $rowdoor->door_lat;
				$lastlng = $rowdoor->door_lng;
				$lastdoor = dbmaketime($rowdoor->door_time);
				$lastdoorstat = $rowdoor->door_status;
				
				printf("=== last: %s,%s %s %s\r\n", $lastlat, $lastlng, date("d/m/Y H:i:s", $lastdoor+7*3600), $lastdoorstat); 
			}
			
			// looping data
						
			for($j=0; $j < count($rowgps); $j++)
			{
				$lat = sprintf("%.4f", getLatitude($rowgps[$j]->gps_latitude, $rowgps[$j]->gps_ns));
				$lng = sprintf("%.4f", getLongitude($rowgps[$j]->gps_longitude, $rowgps[$j]->gps_ew));
				$doorstat = substr($rowgps[$j]->gps_msg_ori, 79, 1); //door 1 == open ,else close
				if($doorstat == 1){
					$doorstatinfo = "OPEN";
				}else{
					$doorstatinfo = "CLOSE";
				}
				/* $dataposition = $this->getPosition($rowgps[$j]->gps_longitude, $rowgps[$j]->gps_ew, $rowgps[$j]->gps_latitude, $rowgps[$j]->gps_ns);
				if(isset($dataposition)){
					$position = $dataposition;
				}else{
					$position = "";
				} */
				
				//add geofence condition
				$latreal = $rowgps[$j]->gps_latitude_real;
				$lngreal = $rowgps[$j]->gps_longitude_real;
				
				$gps = $rowgps[$j];
				$gps->lat = $latreal;
				$gps->lng = $lngreal;
	
				
					$sql = sprintf("
						SELECT 	* 
						FROM 	%sgeofence 
						WHERE 	TRUE
								AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
								AND (geofence_user = 1032 )
								AND (geofence_status = 1)
						LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $rows[$i]->vehicle_device);

				$q = $this->db->query($sql);
				$rowgeo = $q->row();
				
				if (count($rowgeo) > 0){
					$geofence = $rowgeo->geofence_name;
				}else{
					$geofence = "";
				}
				//end add geofence

				if (($doorstatinfo == $lastdoorstat) && $doorstatinfo == "OPEN") //only open condition
				{					
					if (($j+1) == count($rowgps))
					{
						$t = dbmaketime($rowgps[$j]->gps_time);
						$doorlength = $t-$lastdoor;						
						
						printf("=== current=%d s <-> max=%d s %d\r\n", $doorlength, $maxdoor, $doorstatinfo);
						
						if (($doorlength < $maxdoor))
						{
							continue;
						}
						
						// alert door
						
						$lastdoor = $t;
						$this->doalertdoor_balrich($rows[$i], $rowgps[$j], $doorlength, $dooralert, $geofence, $latreal, $lngreal, $doorstatinfo, $vehiclemaxdoor);
					}
					
					continue;
				}			
				
				$lastlat = $lat;
				$lastlng = $lng;
				$lastdoorstat = $doorstatinfo;
				
				$prevdoor = $lastdoor;
				$lastdoor = dbmaketime($rowgps[$j]->gps_time);
				
				if ($j == 0)
				{					
					continue;
				}
				
				$t = dbmaketime($rowgps[$j-1]->gps_time);
				$doorlength = $t-$prevdoor;
				
				printf("=== current=%d s <-> max=%d s %d \r\n", $doorlength, $maxdoor, $doorstatinfo);
				
				if (($doorlength < $maxdoor))
				{
					continue;
				}
				
				// alert park
				
				$this->doalertdoor_balrich($rows[$i], $rowgps[$j-1], $doorlength, $dooralert, $geofence, $latreal, $lngreal, $doorstatinfo, $vehiclemaxdoor);
			}
			
			$this->db->where("door_device", $rows[$i]->vehicle_device);
			$total = $this->db->count_all_results("door");
			
			unset($update);
			
			$update['door_device'] = $rows[$i]->vehicle_device;
			$update['door_lat'] = $lastlat;
			$update['door_lng'] = $lastlng;
			$update['door_time'] = date("Y-m-d H:i:s", $lastdoor);
			$update['door_status'] = $lastdoorstat;
			
			if ($total > 0)
			{
				$this->db->where("door_device", $rows[$i]->vehicle_device);
				$this->db->update("door", $update);
			}
			else
			{
				$this->db->insert("door", $update);
			}
		}
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !!"." ".$finish_time. "\r\n");
	}
	
	function doalertdoor_balrich($vehicle, $gps, $doorlength, $dooralert, $geofence, $latreal, $lngreal, $doorstatinfo, $vehiclemaxdoor)
	{
		
		$isdooralert = false;
		$t = dbmaketime($gps->gps_time);

		if (! isset($dooralert[$vehicle->vehicle_device]))
		{
			$dooralert[$vehicle->vehicle_device] = $t;
			$isdooralert = true;
		}
		else
		{
			$delta = mktime() - $dooralert[$vehicle->vehicle_device];
			$isdooralert = $delta > 3600;
		}
		
		if (! $isdooralert) 
		{
			printf("=== alerted at %s %s \r\n", date("d/m/Y H:i:s", $dooralert[$vehicle->vehicle_device]), $doorstatinfo);
			return;
		}

		unset($insert);
		
		$insert['door_alert_device'] = $vehicle->vehicle_device;
		$insert['door_alert_company'] = $vehicle->vehicle_company;
		$insert['door_alert_time'] = date("Y-m-d H:i:s", $t);
		$insert['door_alert_length'] = round($doorlength/60);
		$insert['door_alert_max'] = $vehicle->vehicle_maxparking;
		$insert['door_alert_created'] = date("Y-m-d H:i:s");
		$insert['door_alert_status'] = $doorstatinfo;
		
		$this->db->insert("door_alert", $insert);
		
		$emails = get_valid_emails($vehicle->user_mail);
		
		if($geofence == "" && $doorstatinfo == "OPEN"){
			if ($vehicle->user_alert_parking_email == 1)
			{
				foreach($emails as $email)
				{
					unset($mail);
				
					$mail['subject'] = sprintf("Balrich Door Alert: %s", $vehicle->vehicle_no);
					$mail['message'] = sprintf("Pada %s, kendaraan %s %s, status Door %s selama %s menit diluar area geofence pada koordinat : %s %s, dimana ambang batas status Door OPEN adalah %s menit", 
									   date("d/m/Y H:i:s", $t+7*3600), $vehicle->vehicle_no, $vehicle->company_name, $doorstatinfo, round($doorlength/60), $latreal, $lngreal, $vehiclemaxdoor);
					$mail['dest'] = $email; 
					//untuk mobil balrich cikarang
					if ($vehicle->vehicle_company == 65) {
						$mail['bcc'] = "gps.cikarang@balrich.co.id,report.lacakmobil@gmail.com";
					}else{
						$mail['bcc'] = "report.lacakmobil@gmail.com";
					}
					$mail['sender'] = "support@lacak-mobil.com";
					lacakmobilmail($mail);
					printf("=== send email ok\r\n");				
				}
			}
		}else{
			printf("=== masih di dalam geofence %s %s \r\n", $geofence, $doorstatinfo);
		}
		
		
		/* if (! isON($vehicle->user_sms_notifikasi, 13))
		{
			printf("=== notifikasi off\r\n");
			return;
		}
		
		$hp = valid_mobiles($vehicle->user_mobile);
		if ($hp === FALSE) 
		{
			printf("=== invalid mobile no: %s\r\n", $vehicle->user_mobile);
			return;
		} */
		
		
		/* if ($vehicle->user_payment_period >= 12)
		{
			print("----- user tahunan tidak diperbolehkan\r\n");
			return;
		}
		
		if ($vehicle->user_alert_parking_sms == 2)
		{
			print("----- user tidak inginkan terima alert\r\n");
			return;
		} */
		
		/* $this->db->where("agent_pss", 1);
		$this->db->where("agent_id", $vehicle->user_agent);
		$q = $this->db->get("agent");

		if ($q->num_rows() == 0)
		{
			print("----- agent tidak diijinkan\r\n");
			return;
		}	
			
		$content = sprintf($this->config->item("SMS_ALERT_MAX_PARK"), date("d/m/Y H:i:s", $t+7*3600), $vehicle->vehicle_no, round($parklength/60), $vehicle->vehicle_maxparking); */
						
		/* foreach($hp as $h)
		{				
			$xml = sprintf("%s\1%s\alat", $h, $content);
			$this->smsmodel->sendsms($xml);
			
			printf("%s\r\n", $xml);
		} */								
							
		$dooralert[$vehicle->vehicle_device] = mktime();

		unset($update);
							
		$update['logs_created'] = date("Y-m-d H:i:s");
		
		$this->db->where("logs_content", $vehicle->vehicle_device);
		$this->db->where("logs_type", "dooralert_balrich");
		$this->db->update("logs", $update);
		
		if ($this->db->affected_rows() == 0)
		{
			unset($insert);
			
			$insert['logs_created'] = date("Y-m-d H:i:s");
			$insert['logs_content'] = $vehicle->vehicle_device;
			$insert['logs_type'] = "dooralert_balrich";
			
			$this->db->insert("logs", $insert);
		}
		
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
