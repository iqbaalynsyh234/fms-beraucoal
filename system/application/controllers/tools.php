<?php
include "base.php";

class Tools extends Base {

	function Tools()
	{
		parent::Base();	
		$this->load->helper("common");
		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("agenmodel");
	}
	
	// routine untuk check apakah vehicle type dengan table gps nya sinkron
	function checkvtype()
	{
		$vtypes = $this->config->item("vehicle_type");
		if (! is_array($vtypes))
		{
			echo "can't found vehicle type";
			return;
		}
		
		if (! count($vtypes))
		{
			echo "can't found vehicle type";
			return;
		}

			$this->db->where("vehicle_status", 1);
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0)
			{
				echo "can't found vehicle\r\n";
				return;
			}
			
			$uniqid = uniqid();

			$rows = $q->result();
			for($i=0; $i < count($rows); $i++)
			{
				$tbl = $this->gpsmodel->getGPSTable($rows[$i]->vehicle_type);

				$devices = explode("@", $rows[$i]->vehicle_device, 2);

				$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$tot = $this->db->count_all_results($tbl);

				if ($tot > 0) continue;
				
				$realtype = "";
				foreach($vtypes as $key=>$val)
				{
                                	$tbl = $this->gpsmodel->getGPSTable($key);

									$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);
                                	$this->db->where("gps_name", $devices[0]);
                                	$this->db->where("gps_host", $devices[1]);
                                	$tot = $this->db->count_all_results($tbl);
					
					if ($tot == 0) continue;
				
					$realtype = $key;
				}
				
				if (! $realtype) continue;

				$s = sprintf("%s => %s\r\n", $rows[$i]->vehicle_device, $realtype);
				echo $s;
				maillocalhost("vehicle type error", $s, "owner@adilahsoft.com");
                        }
	}
	
	function geofencespatial()
	{
		$this->db->where("geofence_polygon IS NULL", null);
		$this->db->where("geofence_id >", 11);
		$q = $this->db->get("geofence");
		
		if ($q->num_rows() == 0) 
		{
			echo "can't found geofence";
			return;
		}
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$geo = $rows[$i]->geofence_coordinate;
			
			$geo = str_replace(" ", "=====", $geo);
			$geo = str_replace(",", " ", $geo);
			$geo = str_replace("=====", ", ", $geo);
			
			
			$sql = "UPDATE ".$this->db->dbprefix."geofence SET geofence_polygon = GEOMFROMTEXT('POLYGON((".$geo."))') WHERE geofence_id = '".$rows[$i]->geofence_id."'";
			echo $sql."\r\n";
			//$this->db->query($sql);
			
		}
	}
	
	function deltrigger()
	{
		$this->db->query("DROP TRIGGER t1_oninserted");
		$this->db->query("DROP TRIGGER t1_1_oninserted");
		$this->db->query("DROP TRIGGER t1_pln_oninserted");
		$this->db->query("DROP TRIGGER t3_oninserted");
		$this->db->query("DROP TRIGGER t4_oninserted");
		$this->db->query("DROP TRIGGER t4_farrasindo_oninserted");
		$this->db->query("DROP TRIGGER indogps_oninserted");		
	}

	function kendaraan_andalas()
	{
		$this->db->order_by("user_login", "asc");
		$this->db->order_by("vehicle_device", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->distinct();
		$this->db->select("user_login, vehicle_device, vehicle_no");
		$this->db->where("user_agent", 3);
		$this->db->where("vehicle_status", 1);
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");

		$rows = $q->result();

		header("Content-type: text/text");

		for($i=0; $i < count($rows); $i++)
		{
			printf("%s;%s;%s;%s\r\n", $i, $rows[$i]->user_login, $rows[$i]->vehicle_device, $rows[$i]->vehicle_no);
		}
	}
	
	function address($lng, $lat)
	{
		
	}
	
	function yahoo($u, $t=14)
	{
		$url = "http://opi.yahoo.com/online?u=".$u."&t=".$t;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$img = curl_exec($ch);
		curl_close($ch);

		header("Content-type: image/gif");
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		echo $img;
		exit;
	}
	
	function delcache()
	{
		$this->db->cache_delete_all();
	}	
	
	function inithist()
	{
		$this->db->where("config_name", "maxhist");
		$q = $this->db->get("config");
		$row = $q->row();
		
		$t = mktime(-7, 0, 0, date('n')-$row->config_value, date('j'), date('Y'));
		
		$tblhists = $this->config->item("table_hist");
		
		$this->db->distinct();
		$this->db->select("vehicle_device, vehicle_type");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$vehicles[$rows[$i]->vehicle_device] = $rows[$i]->vehicle_type;
		}
		
		$offset = 0;
		$limit = 10000;
		$j = 0;
		while(1)
		{
			//$this->db->where("gps_id <", 159328293);
			$this->db->limit($limit, $offset);
			$this->db->distinct();
			$this->db->select("gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew, gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real, gps_odometer, gps_workhour");
			$this->db->order_by("gps_id", "desc");			
			$q = $this->db->get("gps_hist");
			
			$rows = $q->result();
			
			if (count($rows) < $limit)
			{
				$exit = true;
			}
			
			unset($insert);
			
			for($i=0; $i < count($rows); $i++)
			{			
				
				$t1 = dbmaketime($rows[$i]->gps_time);
				
				if ($t1 < $t)
				{
					$j++;
					//printf("Vehicle < %d month \r\n", $row->config_value);
					continue;
					//$exit = true;
					//break;
				}
				
				//printf("Processing....%d. %s@%s\r\n", $j+1, $rows[$i]->gps_name, $rows[$i]->gps_host);
				if (! isset($vehicles[$rows[$i]->gps_name.'@'.$rows[$i]->gps_host]))
				{
					//printf("Vehicle tidak ditemukan\r\n");
					$j++;
					continue;
				}
				
				if (! isset($tblhists[strtoupper($vehicles[$rows[$i]->gps_name.'@'.$rows[$i]->gps_host])]))
				{
					//printf("Tabel hist tidak ditemukan\r\n");
					$j++;
					continue;
				}
				
				$tblhist = $tblhists[strtoupper($vehicles[$rows[$i]->gps_name.'@'.$rows[$i]->gps_host])];
				
				if (! isset($insert[$tblhist]))
				{				
					$sql = sprintf
					("
						INSERT INTO %s%s (gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew, gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real, gps_odometer, gps_workhour)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
					", $this->db->dbprefix, $tblhist
					, $rows[$i]->gps_name, $rows[$i]->gps_host, $rows[$i]->gps_type, $rows[$i]->gps_utc_coord, $rows[$i]->gps_status, $rows[$i]->gps_latitude
					, $rows[$i]->gps_ns, $rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_speed, $rows[$i]->gps_course, $rows[$i]->gps_utc_date
					, $rows[$i]->gps_mvd, $rows[$i]->gps_mv, $rows[$i]->gps_cs, $rows[$i]->gps_msg_ori, $rows[$i]->gps_time, $rows[$i]->gps_latitude_real
					, $rows[$i]->gps_longitude_real, $rows[$i]->gps_odometer, $rows[$i]->gps_workhour
					);
					
					$insert[$tblhist] = $sql;
				}
				else
				{
					$sql = sprintf
					(" \r\n,('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')"
					, $rows[$i]->gps_name, $rows[$i]->gps_host, $rows[$i]->gps_type, $rows[$i]->gps_utc_coord, $rows[$i]->gps_status, $rows[$i]->gps_latitude
					, $rows[$i]->gps_ns, $rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_speed, $rows[$i]->gps_course, $rows[$i]->gps_utc_date
					, $rows[$i]->gps_mvd, $rows[$i]->gps_mv, $rows[$i]->gps_cs, $rows[$i]->gps_msg_ori, $rows[$i]->gps_time, $rows[$i]->gps_latitude_real
					, $rows[$i]->gps_longitude_real, $rows[$i]->gps_odometer, $rows[$i]->gps_workhour
					);				
					
					$insert[$tblhist] .= $sql;	
				}
					
				$j++;
			}
			
			printf("%d\r\n", $j);
			
			if (! $insert) break;
			
			foreach($insert as $sql)
			{
				printf("Insert into %s\r\n", $tblhist);				
			
				$fout = fopen("hist.sql", "w");
				fwrite($fout, $sql);
				fclose($fout);
				
				system("c:\\xampp\\mysql\\bin\\mysql.exe -uroot -pgpsjayatrackervilani webtracking < hist.sql");
				
				//$this->db->query($sql);
			}			
						
			if (isset($exit)) break;			
			
			$offset += $limit;
		}		
	}
	
	function initinfohist()
	{
		$this->db->where("config_name", "maxhist");
		$q = $this->db->get("config");
		$row = $q->row();
		
		$t = mktime(16, 59, 59, date('n')-$row->config_value, date('j'), date('Y'));
		
		$this->db->distinct();
		$this->db->select("vehicle_device, vehicle_type");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		
		$tblinfo = $this->config->item("table_hist_info");
		
		for($i=57; $i < count($rows); $i++)
		{
			printf("Processing...%d %s", $i+1, $rows[$i]->vehicle_device);
			
			if (! in_array(strtoupper($rows[$i]->vehicle_type), $this->config->item("vehicle_gtp"))) 
			{
				printf(" not gtp\r\n");
				continue;
			}
			
			$sql = sprintf(
				"INSERT INTO %s%s (gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data, gps_info_ad_input, gps_info_utc_coord, gps_info_utc_date, gps_info_alarm_alert, gps_info_time, gps_info_status, gps_info_gps)
				SELECT DISTINCT gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data, gps_info_ad_input, gps_info_utc_coord, gps_info_utc_date, gps_info_alarm_alert, gps_info_time, gps_info_status, gps_info_gps
				FROM %sgps_info_hist
				WHERE TRUE
						AND (gps_info_time > '%s')
						AND (gps_info_device = '%s')
			", $this->db->dbprefix, $tblinfo[strtoupper($rows[$i]->vehicle_type)], $this->db->dbprefix, date("Y-m-d H:i:s", $t), $rows[$i]->vehicle_device);
			
			$this->db->query($sql);
			printf(" %s OK\r\n", $tblinfo[strtoupper($rows[$i]->vehicle_type)]);
		}
	}
	
	function repairdistance()
	{
		$t = mktime(0, 0, 0, 6, 12, 2011);
		while(1)
		{
			unset($rows);
			//if ($t > mktime()) break;
			
			echo "date: ".date("d/m/Y", $t)."\r\n";
			
			$this->db->where("TIMESTAMPDIFF(DAY, gps_time, '".date("Y-m-d 00:00:00", $t)."') = 0", null, false);
			$q = $this->db->get("webtracking_gps_hist_t5");
			
			if ($q->num_rows() == 0) return;
			
			$rows = $q->result();
			
			for($i=0; $i < count($rows); $i++)
			{				
				
				$msg = $rows[$i]->gps_msg_ori;
				
				$distance = substr($msg, strlen($msg)-8);
				$distance = ltrim($distance, '0');
				
				if (strlen($distance) == 0)
				{
					$ldistance = 0;
				}
				else
				{
					$ldistance = hexdec($distance);
				}
				
				printf("%d %s@%s %s %s %s\r\n", $i+1, $rows[$i]->gps_name, $rows[$i]->gps_host, $msg, $distance, $ldistance);
				
				unset($update);
				
				$update['gps_info_distance'] = $ldistance;				
				
				$this->db->where("gps_info_gps", $rows[$i]->gps_id);
				$this->db->update("gps_info_hist_t5", $update);
			}
			
			$t += 24*3600;
			sleep(1);
		}
	}
	
	function gent5id($maxlen=12, $prefix="")
	{		
		$len = $maxlen-strlen($prefix);
		$lower = floor($len/2);
		$upper = $len-$lower;

		$max = "";
		for($i=0; $i < $lower; $i++)
		{
			$max .= "9";
		}
		
		$fmt1 = sprintf("%%0%dd", $lower);

		$max1 = "";
		for($i=0; $i < $upper; $i++)
		{
			$max1 .= "9";
		}

		$fmt2 = sprintf("%%0%dd", $upper);
		
		while(1)
		{
			$rand1 = rand(0, $max);
			$rand2 = rand(0, $max1);
			
			$val = $prefix.sprintf($fmt1, $rand1).sprintf($fmt2, $rand2);
			
			$this->db->where("vehicle_device", $val.'@T5');
			$num = $this->db->count_all_results("vehicle");
			
			if ($num) continue;
			
			echo $val.'@T5';
			return;
		}
		
	}
	
	function rollbackhistinfo()
	{
		$this->db->where("config_name", "maxhist");
		$q = $this->db->get("config");
		
		if ($q->num_rows() == 0)
		{
			die("Hist config is not define");
		}
		
		$row = $q->row();		
		$t = mktime(16, 59, 59, date('n')-$row->config_value, date('j'), date('Y'));
		
		// ambil data kendaraan		

		$this->db->distinct();
		$this->db->select("vehicle_device, vehicle_type");		
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			die("Vehicle is empty");
		}

		$tblhists = $this->config->item("table_hist_info");

		$i = 0;
		$vehicles = $q->result();
		foreach($vehicles as $vehicle)
		{
			printf("%d processing...%s", ++$i, $vehicle->vehicle_device);
			if (! isset($tblhists[strtoupper($vehicle->vehicle_type)]))
			{
				printf("vehicle type can't found\r\n");
				continue;
			}
						
			$tblhist = $tblhists[strtoupper($vehicle->vehicle_type)];
			
			printf("...%s\r\n", $tblhist);

			$this->db->limit(1);
			$this->db->order_by("gps_info_time", "asc");
			$this->db->where("gps_info_time >", date("Y-m-d H:i:s", $t));
			$this->db->where("gps_info_device", $vehicle->vehicle_device);
			$q = $this->db->get($tblhist);
			
			if ($q->num_rows() == 0)
			{
				printf("data hist is not found\r\n");
				continue;
			}

			$row = $q->row();
			$t1 = dbmaketime($row->gps_info_time);
			
			if (date("Ymd", $t1) == date("Ymd", $t))
			{
				printf("data hist is full\r\n");
				continue;
			}
			
			printf("%s-%s\r\n", date("Y-m-d H:i:s", $t), date("Y-m-d H:i:s", $t1));
			
			$sql = sprintf("
				INSERT INTO %s%s 
				(
						gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data, gps_info_ad_input, gps_info_utc_coord
					,	gps_info_utc_date, gps_info_alarm_alert, gps_info_time, gps_info_status, gps_info_gps
				)
				SELECT gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data, gps_info_ad_input, gps_info_utc_coord
					,	gps_info_utc_date, gps_info_alarm_alert, gps_info_time, gps_info_status, gps_info_gps
				FROM	%sgps_info_hist
				WHERE	1
						AND (gps_info_device = '%s')
						AND (gps_info_time >= '%s')
						AND (gps_info_time < '%s')					
			", $this->db->dbprefix, $tblhist, $this->db->dbprefix, $vehicle->vehicle_device, date("Y-m-d H:i:s", $t), date("Y-m-d H:i:s", $t1));
			
			$this->db->query($sql);
		}
	}
	
	function rollbackhist()
	{
		$this->db->distinct();
		$this->db->select("vehicle_device, vehicle_type");		
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			die("Vehicle is empty");
		}
		
		$tblhists = $this->config->item("table_hist");

		$i = 0;
		$vehicles = $q->result();
		foreach($vehicles as $vehicle)
		{
			printf("%d processing...%s\r\n", ++$i, $vehicle->vehicle_device);
			if (! isset($tblhists[strtoupper($vehicle->vehicle_type)]))
			{
				printf("vehicle type can't found\r\n");
				continue;
			}
			
			$tblhist = $tblhists[strtoupper($vehicle->vehicle_type)];
			
			// cari tanggal terakhir
			
			$devices = explode("@", $vehicle->vehicle_device);
			
			$this->db->limit(1);
			$this->db->order_by("gps_time", "asc");
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tblhist);
			
			if ($q->num_rows() == 0)
			{
				printf("data hist is not found");
				continue;
			}
			
			$row = $q->row();
			$t = dbmaketime($row->gps_time);
			$t -= 24*3600;
			
			printf("%s\r\n", date("Y-m-d H:i:s", $t));
			
			$this->rollbackhist1($devices[0], $devices[1], $t);
			
			sleep(1);
		}		
	}

	function rollbackhist1($name, $host, $t1)
	{		
		$this->db->where("config_name", "maxhist");
		$q = $this->db->get("config");
		
		if ($q->num_rows() == 0)
		{
			die("Hist config is not define");
		}
		
		$row = $q->row();		
		$t = mktime(16, 59, 59, date('n')-$row->config_value, date('j'), date('Y'));				
		
		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			die("vehicle can't found");
		}
		
		$row = $q->row();
		
		$tblhists = $this->config->item("table_hist");
		if (! isset($tblhists[strtoupper($row->vehicle_type)]))
		{
			die("vehicle type can't found");
		}				
		
		$tblhist = $tblhists[strtoupper($row->vehicle_type)];
		
		while(1)
		{
			if ($t1 < $t) break;

			$sql = sprintf(
				"	INSERT INTO %s%s (gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
											,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real
											,	gps_odometer, gps_workhour)			
					SELECT DISTINCT gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
											,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real
											,	gps_odometer, gps_workhour
					FROM 	%sgps_hist
					WHERE	((gps_utc_date%%10000)%%100+2000)*10000+(floor((gps_utc_date%%10000)/100))*100+(floor(gps_utc_date/10000)) = %s
								AND (gps_name = '%s') AND (gps_host = '%s')
			", $this->db->dbprefix, $tblhist, $this->db->dbprefix, date("Ymd", $t1), $name, $host
			);
	
			printf("%s\r\n", date("Ymd", $t1)); 
			
			$this->db->query($sql);
			$t1 -= 24*3600;
		}
	}
	
	function loaddataT1($dir, $re, $table)
	{
		if (! is_dir($dir))
		{
			printf("% is not directory\r\n", $dir);
			return;
		}

		$dh = opendir($dir);
		if (! $dh)
		{
			printf("can't open %\r\n", $dir);
			return;
		}
		
		$lensql = 0;
		$ifile = 0;
		while (($file = readdir($dh)) !== false) 
		{
			if (! preg_match($re, $file)) continue;
			
			printf("processing %s...\r\n", $file);
			
			$filename = sprintf("%s\\%s", $dir, $file);
			$fin = fopen($filename, "r");
			if (! $fin)
			{
				printf("can't open %s\r\n", $filename);
				continue;
			}
						
			while(! feof($fin))
			{
				$lines = explode(" ", trim(fgets($fin)));
				if (count($lines) < 3) continue;
				
				$data = trim($lines[2]);				
				$datas = explode(",", $data);			
				
				if (count($datas) < 12) 
				{
					printf("data not enough: %s\r\n", $data);
					return;
					continue;
				}
				
				$data1s = explode(":", $datas[0]);
				$devices = explode("@", $data1s[0]);
				
				unset($insert);
				
				//HY@HR:$GPRMC,120431.000,A,0614.9704,S,10656.8281,E,0.00,,090811,,,A*67
				
				$tgl = floor($datas[9]/10000);
				$bln = floor(($datas[9]%10000)/100);
				$thn = (($datas[9]%10000)%100)+2000;

				$jam = floor($datas[1]/10000);
				$min = floor(($datas[1]%10000)/100);
				$det = ($datas[1]%10000)%100;

				$t = mktime($jam, $min, $det, $bln, $tgl, $thn);				
				
				$insert['gps_name'] = $devices[0];
				$insert['gps_host'] = $devices[1];
				$insert['gps_type'] = $data1s[1];
				$insert['gps_utc_coord'] = $datas[1];
				$insert['gps_status'] = $datas[2];
				$insert['gps_latitude'] = $datas[3];
				$insert['gps_ns'] = $datas[4];
				$insert['gps_longitude'] = $datas[5];
				$insert['gps_ew'] = $datas[6];
				$insert['gps_speed'] = $datas[7];
				$insert['gps_course'] = $datas[8] ? $datas[8] : 0;
				$insert['gps_utc_date'] = $datas[9];
				$insert['gps_mvd'] = isset($datas[10]) ? $datas[10] : 0;
				$insert['gps_mv'] = isset($datas[11]) ? $datas[11] : 0;
				$insert['gps_cs'] = isset($datas[12]) ? $datas[12] : "";
				$insert['gps_msg_ori'] = $data;
				$insert['gps_time'] = date("Y-m-d H:i:s", $t);
				$insert['gps_latitude_real'] = getLatitude($datas[3], $datas[4]);
				$insert['gps_longitude_real'] = getLongitude($datas[5], $datas[6]);
				$insert['gps_odometer'] = 0;
				$insert['gps_workhour'] = 0;

				$sql = $this->db->insert_string($table, $insert);				
				
				$logfilename = sprintf("c:\www\T1.%03d.sql", $ifile);
				$this->append($logfilename, $sql.";");
				
				$lensql += strlen($sql);
				$maxsize = 1024*1024*10;
				
				if ($lensql > $maxsize)
				{
					//$ifile++;
					$lensql = 0;
				}
			}
			
			fclose($fin);
		}
		
		closedir($dh);
	}
	
	function loaddataT5($dir, $re, $table, $path, $logfilename)
	{
		if (! is_dir($dir))
		{
			printf("% is not directory\r\n", $dir);
			return;
		}

		$dh = opendir($dir);
		if (! $dh)
		{
			printf("can't open %\r\n", $dir);
			return;
		}
		
		while (($file = readdir($dh)) !== false) 
		{
			if (! preg_match($re, $file)) continue;
			
			printf("processing %s...\r\n", $file);
			
			$filename = sprintf("%s%s%s", $dir, $path, $file);
			$fin = fopen($filename, "r");
			if (! $fin)
			{
				printf("can't open %s\r\n", $filename);
				continue;
			}
						
			while(! feof($fin))
			{
				$lines = explode(" ", trim(fgets($fin)));
				if (count($lines) < 2) continue;
				
				$data = trim($lines[1]);				
				
				unset($insert);
				
				$pos = 1;				
				$id = substr($data, $pos, 12);
				
				$pos += 12;
				$command = substr($data, $pos, 4);
				
				$pos += 4;
				$ndigit = substr($data, $pos, 3);
				
				$pos += 3;
				$id1 = substr($data, $pos, 12);

				$pos += 12;
				$d = substr($data, $pos, 6);

				$pos += 6;
				$status = substr($data, $pos, 1);

				$pos += 1;
				$lat = substr($data, $pos, 9);

				$pos += 9;
				$ns = substr($data, $pos, 1);

				$pos += 1;
				$lng = substr($data, $pos, 10);

				$pos += 10;
				$ew = substr($data, $pos, 1);

				$pos += 1;
				$speed = substr($data, $pos, 5);

				$pos += 5;
				$t = substr($data, $pos, 6);

				$pos += 6;
				$direction = substr($data, $pos, 6);

				$pos += 6;
				$io = substr($data, $pos, 8);

				$pos += 8;
				$io = substr($data, $pos, 8);

				$pos += 8;
				$milflag = substr($data, $pos, 1);

				$pos += 1;
				$distance = substr($data, $pos, 8);
				
				//(002100000086BP05000002100000086110809A0332.4791N09837.7579E057.4053950118.1901000005L000FA39F
				
				$time = dbintmaketime(sprintf("20%d", $d), $t);
				
				$insert['gps_name'] = $id;
				$insert['gps_host'] = "T5";
				$insert['gps_type'] = "T5";
				$insert['gps_utc_coord'] = date("His", $time);
				$insert['gps_status'] = $status;
				$insert['gps_latitude'] = $lat;
				$insert['gps_ns'] = $ns;
				$insert['gps_longitude'] = $lng;
				$insert['gps_ew'] = $ew;
				$insert['gps_speed'] = $speed/1.852;
				$insert['gps_course'] = $direction;
				$insert['gps_utc_date'] = date("dmy", $time);
				$insert['gps_mvd'] = 0;
				$insert['gps_mv'] = 0;
				$insert['gps_cs'] = "";
				$insert['gps_msg_ori'] = $data;
				$insert['gps_time'] = date("Y-m-d H:i:s", $time);
				$insert['gps_latitude_real'] = getLatitude($lat, $ns);
				$insert['gps_longitude_real'] = getLongitude($lng, $ew);
				$insert['gps_odometer'] = 0;
				$insert['gps_workhour'] = 0;

				$sql = $this->db->insert_string($table, $insert);				

				$this->append($logfilename, $sql.";");
			}
			
			fclose($fin);
		}
		
		closedir($dh);
	}
	
	function doLoadDataT5()
	{
		$this->loaddataT5("/home/lacakmobil/server/T5_Pulse/log", "/streamT5Pulse_2011070(5|6|7)/", "gps_hist_t5_pulse", "/", "temp/log/t5.sql");
	}
	
	function append($filename, $log)
	{
		$fout = fopen($filename, "a");
		fwrite($fout, $log."\r\n");
		fclose($fout);
	}
	
	function gammu()
	{
		$filename = sprintf("%s../log/gammu/identify_%s_%d.log", BASEPATH, date("Ymd"), date("G"));
		readfile($filename);
	}
	
	function test()
	{
		unset($mail);
		
		$mail['subject'] = "test";
		$mail['message'] = "test";
		$mail['dest'] = "owner@adilahsoft.com"; 
		$mail['bcc'] = "owner@adilahsoft.com";
		$mail['sender'] = "support@lacak-mobil.com";
		
		lacakmobilmail($mail);	
	}	
	
	function T5($d1, $d2, $port=10000)
	{
		$dir = $this->config->item("T5_LOG_PATH");
		
		if (! is_dir($dir)) 
		{
			die($dir." is not found");
		}
			
		$dh = opendir($dir);
		if (! $dh)
		{
			die($dir." is invalid");
		}
		
		while (($file = readdir($dh)) !== false) 
		{
			$path = pathinfo($file);			
			$filename = $path['filename'];
			
			if (! preg_match("/tracking(.*)/", $filename, $matches)) continue;
			
			$dates = explode("_", $matches[1]);
			
			if (($dates[0] < $d1) || ($dates[0] > $d2))
			{
				continue;
			}
						
			$contents = file($dir."\\".$file);
			for($i=0; $i < count($contents); $i++)
			{
				$line = trim($contents[$i]);
				$pos = strpos($line, "ADD QUEUE:");
				if ($pos === FALSE) continue;
				
				$data = trim(substr($line, $pos+strlen("ADD QUEUE:"))).")";
				
				$fp = pfsockopen( "udp://119.235.20.251", $port, $errno, $errstr );
				
				if (!$fp)
				{
					die("ERROR: $errno - $errstr\n");
				}
				
				socket_set_timeout ($fp, 10);
				$write = fwrite( $fp, $data );

				fclose($fp);
				
				echo $data."\r\n";
				sleep(1);
			}
			
			//tracking20111221_234149.log
		}	
	}
	
	function T5Pulse($d1, $d2, $port=10001)
	{
		$dir = $this->config->item("T5_PULSE_LOG_PATH");
		
		if (! is_dir($dir)) 
		{
			die($dir." is not found");
		}
			
		$dh = opendir($dir);
		if (! $dh)
		{
			die($dir." is invalid");
		}
		
		while (($file = readdir($dh)) !== false) 
		{
			$path = pathinfo($file);			
			$filename = $path['filename'];
			
			if (! preg_match("/tracking(.*)/", $filename, $matches)) continue;
			
			$dates = explode("_", $matches[1]);
			
			if (($dates[0] < $d1) || ($dates[0] > $d2))
			{
				continue;
			}
						
			$contents = file($dir."\\".$file);
			for($i=0; $i < count($contents); $i++)
			{
				$line = trim($contents[$i]);
				$pos = strpos($line, "ADD QUEUE:");
				if ($pos === FALSE) continue;
				
				$data = trim(substr($line, $pos+strlen("ADD QUEUE:")));
				
				$fp = pfsockopen( "udp://119.235.20.251", $port, $errno, $errstr );
				
				if (!$fp)
				{
					die("ERROR: $errno - $errstr\n");
				}
				
				socket_set_timeout ($fp, 10);
				$write = fwrite( $fp, $data );

				fclose($fp);
				
				echo $data."\r\n";
				sleep(1);
			}
			
			//tracking20111221_234149.log
		}	
	}
    
    function create_tables_datareport($vdevice = "", $type="")
    {
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
        
        foreach(array("datagps_mei_2013","datagps_juni_2013") as $dbname)
        {
            $historydb = $this->load->database($dbname, TRUE);
            $i = 0;
            
           foreach($rows as $row)
            {
                printf("%d/%d create table %s for %s\n", ++$i, $total, $dbname, $row->vehicle_device);
                $tb = explode("@",$row->vehicle_device);
                
                if (isset($tb[0]) && isset($tb[1]))
                {
                    $tblnamegps = $tb[0].$tb[1]."_gps";
                    $tblnameinfo = $tb[0].$tb[1]."_info";
                    
                    $histtable = $this->load->view("db/gps", FALSE, TRUE);            
                    $sql = sprintf($histtable, strtolower($tblnamegps));                        
                    $historydb->query($sql);
                    printf("=== %s\n", strtolower($tblnamegps)); 
                    
                    $histtable = $this->load->view("db/info", FALSE, TRUE);            
                    $sql = sprintf($histtable, strtolower($tblnameinfo));                        
                    $historydb->query($sql);
                    printf("=== %s\n", strtolower($tblnameinfo));
                      
                }
                else
                {
                    printf("Invalid Device ID %s\n", $row->vehicle_device);        
                }
                
                printf("=== \n");     

            }
        }
    }
	
	function sinkronisasi_user()
	{
		printf("PROSES SINKRONISASI USER \r\n"); 
		$this->db->where("user_id > ",0);
		$q = $this->db->get("user");
		$rows = $q->result();
		$total = count($rows);
		printf("GET USER : %s \r\n", $total);        
		
		foreach($rows as $row)
		{
			printf("PROSES USER : %s \r\n", $row->user_id); 
			$this->dbold = $this->load->database("datagpsold", true);
			unset($data);
			
			$data['user_id'] = $row->user_id;
			$data['user_login'] = $row->user_login;
			$data['user_pass'] = $row->user_pass;
			$data['user_name'] = $row->user_name;
			$data['user_license_id'] = $row->user_license_id;
			$data['user_license_type'] = $row->user_license_type;
			$data['user_sex'] = $row->user_sex;
			$data['user_birth_date'] = $row->user_birth_date;
			$data['user_province'] = $row->user_province;
			$data['user_city'] = $row->user_city;
			$data['user_address'] = $row->user_address;
			$data['user_mobile'] = $row->user_mobile;
			$data['user_phone'] = $row->user_phone;
			$data['user_type'] = $row->user_type;
			$data['user_status'] = $row->user_status;
			$data['user_lastlogin_date'] = $row->user_lastlogin_date;
			$data['user_lastlogin_time'] = $row->user_lastlogin_time;
			$data['user_photo'] = $row->user_photo;
			$data['user_zipcode'] = $row->user_zipcode;
			$data['user_create_date'] = $row->user_create_date;
			$data['user_agent'] = $row->user_agent;
			$data['user_mail'] = $row->user_mail;
			$data['user_agent_admin'] = $row->user_agent_admin;
			$data['user_alarm'] = $row->user_alarm;
			$data['user_engine'] = $row->user_engine;
			$data['user_group'] = $row->user_group;
			$data['user_company'] = $row->user_company;
			$data['user_manage_password'] = $row->user_manage_password;
			$data['user_sms_notifikasi'] = $row->user_sms_notifikasi;
			$data['user_change_profile'] = $row->user_change_profile;
			$data['user_payment_type'] = $row->user_payment_type;
			$data['user_payment_period'] = $row->user_payment_period;
			$data['user_payment_amount'] = $row->user_payment_amount;
			$data['user_payment_pulsa'] = $row->user_payment_pulsa;
			$data['user_alert_geo_sms'] = $row->user_alert_geo_sms;
			$data['user_alert_geo_email'] = $row->user_alert_geo_email;
			$data['user_alert_speed_sms'] = $row->user_alert_speed_sms;
			$data['user_alert_speed_email'] = $row->user_alert_speed_email;
			$data['user_alert_parking_sms'] = $row->user_alert_parking_sms;
			$data['user_alert_parking_email'] = $row->user_alert_parking_email;
			$data['user_trans_tupper'] = $row->user_trans_tupper;
			
			$this->dbold->select("user_id");
			$this->dbold->where("user_id", $row->user_id);
			$qu = $this->dbold->get("user");
			$ru = $qu->row();
			if (count($ru)>0)
			{
				printf("UPDATE USER : %s \r\n", $row->user_id); 
				$this->dbold->where("user_id", $row->user_id);	
				$this->dbold->update("user",$data);
			}
			else
			{
				printf("INSERT USER : %s \r\n", $row->user_id); 
				$this->dbold->insert("user",$data);
			}
			printf("FINISH USER : %s \r\n", $row->user_id); 
			printf("=============================================== \r\n"); 
		}
		
		printf("PROSES SELESAI"); 
		
	}
	
	function sinkronisasi_vehicle()
	{
		printf("PROSES SINKRONISASI VEHICLE \r\n"); 
		$this->db->where("vehicle_id > ",0);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		$total = count($rows);
		printf("GET VEHICLE : %s \r\n", $total); 
		
		foreach($rows as $row)
		{
			printf("PROSES VEHICLE : %s \r\n", $row->vehicle_device); 
			$this->dbold = $this->load->database("datagpsold", true);
			unset($data);
			
			$data["vehicle_id"] = $row->vehicle_id;
			$data["vehicle_user_id"] = $row->vehicle_user_id;
			$data["vehicle_device"] = $row->vehicle_device;
			$data["vehicle_no"] = $row->vehicle_no;
			$data["vehicle_name"] = $row->vehicle_name;
			$data["vehicle_active_date2"] = $row->vehicle_active_date2;
			$data["vehicle_card_no"] = $row->vehicle_card_no;
			$data["vehicle_operator"] = $row->vehicle_operator;
			$data["vehicle_active_date"] = $row->vehicle_active_date;
			$data["vehicle_active_date1"] = $row->vehicle_active_date1;
			$data["vehicle_status"] = $row->vehicle_status;
			$data["vehicle_image"] = $row->vehicle_image;
			$data["vehicle_created_date"] = $row->vehicle_created_date;
			$data["vehicle_type"] = $row->vehicle_type;
			$data["vehicle_autorefill"] = $row->vehicle_autorefill;
			$data["vehicle_maxspeed"] = $row->vehicle_maxspeed;
			$data["vehicle_maxparking"] = $row->vehicle_maxparking;
			$data["vehicle_group"] = $row->vehicle_group;
			$data["vehicle_company"] = $row->vehicle_company;
			$data["vehicle_odometer"] = $row->vehicle_odometer;
			$data["vehicle_payment_type"] = $row->vehicle_payment_type;
			$data["vehicle_payment_amount"] = $row->vehicle_payment_amount;
			$data["vehicle_fuel_capacity"] = $row->vehicle_fuel_capacity;
			$data["vehicle_info"] = $row->vehicle_info;
			$data["vehicle_teknisi_id"] = $row->vehicle_teknisi_id;
			$data["vehicle_tanggal_pasang"] = $row->vehicle_tanggal_pasang;
			$data["vehicle_imei"] = $row->vehicle_imei;
			
			$this->dbold->select("vehicle_id");
			$this->dbold->where("vehicle_id", $row->vehicle_id);
			$qu = $this->dbold->get("vehicle");
			$ru = $qu->row();
			if (count($ru)>0)
			{
				printf("UPDATE VEHICLE : %s \r\n", $row->vehicle_device); 
				$this->dbold->where("vehicle_id", $row->vehicle_id);	
				$this->dbold->update("vehicle",$data);
			}
			else
			{
				printf("INSERT VEHICLE : %s \r\n", $row->vehicle_device); 
				$this->dbold->insert("vehicle",$data);
			}
			printf("FINISH VEHICLE : %s \r\n", $row->vehicle_device); 
			printf("=============================================== \r\n"); 
			
		}
		
		printf("SELESAI"); 
		
	}

	function sinkronisasi_company()
	{
		printf("PROSES SINKRONISASI COMPANY \r\n"); 
		$this->db->where("company_id > ",0);
		$q = $this->db->get("company");
		$rows = $q->result();
		$total = count($rows);
		printf("GET COMPANY : %s \r\n", $total);

		foreach($rows as $row)
		{
			printf("PROSES COMPANY : %s \r\n", $row->company_id); 
			$this->dbold = $this->load->database("datagpsold", true);
			unset($data);
			
			$data["company_id"] = $row->company_id;
			$data["company_name"] = $row->company_name;
			$data["company_agent"] = $row->company_agent;
			$data["company_created"] = $row->company_created;
			$data["company_site"] = $row->company_site;
			$data["company_created_by"] = $row->company_created_by;
			$data["company_flag"] = $row->company_flag;
			
			$this->dbold->select("company_id");
			$this->dbold->where("company_id", $row->company_id);
			$qu = $this->dbold->get("company");
			$ru = $qu->row();
			if (count($ru)>0)
			{
				printf("UPDATE COMPANY : %s \r\n", $row->company_id); 
				$this->dbold->where("company_id", $row->company_id);	
				$this->dbold->update("company",$data);
			}
			else
			{
				printf("INSERT COMPANY : %s \r\n", $row->company_id); 
				$this->dbold->insert("company",$data);
			}
			printf("FINISH COMPANY : %s \r\n", $row->company_id); 
			printf("=============================================== \r\n"); 
			
		}
		
		printf("SELESAI");
	}

	function sinkronisasi_group()
	{
		printf("PROSES SINKRONISASI GROUP \r\n"); 
		$this->db = $this->load->database("default",true);
		$this->db->where("group_id > ",0);
		$q = $this->db->get("group");
		$rows = $q->result();
		$total = count($rows);
		printf("GET GROUP : %s \r\n", $total);
		
		foreach($rows as $row)
		{
			printf("PROSES GROUP : %s \r\n", $row->group_id); 
			$this->dbold = $this->load->database("datagpsold", true);
			unset($data);
			
			$data["group_id"] = $row->group_id;
			$data["group_name"] = $row->group_name;
			$data["group_parent"] = $row->group_parent;
			$data["group_created"] = $row->group_created;
			$data["group_creator"] = $row->group_creator;
			$data["group_status"] = $row->group_status;
			$data["group_company"] = $row->group_company;
			
			$this->dbold->select("group_id");
			$this->dbold->where("group_id", $row->group_id);
			$qu = $this->dbold->get("group");
			$ru = $qu->row();
			if (count($ru)>0)
			{
				printf("UPDATE COMPANY : %s \r\n", $row->group_id); 
				$this->dbold->where("group_id", $row->group_id);	
				$this->dbold->update("group",$data);
			}
			else
			{
				printf("INSERT GROUP : %s \r\n", $row->group_id); 
				$this->dbold->insert("group",$data);
			}
			printf("FINISH GROUP : %s \r\n", $row->group_id); 
			printf("=============================================== \r\n"); 
			
		}
		
		printf("SELESAI");
	}

	//Create table server1 ( 27.111.40.250 )
	function create_tables1($vdevice = "", $type="")
	{
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
		
		foreach(array("gpshistory1") as $dbname)
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
	
	//Create table server2 ( 27.111.40.251 )
	function create_tables2($vdevice = "", $type="")
	{
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
		
		foreach(array("gpshistory2") as $dbname)
		{
			$historydb = $this->load->database($dbname, TRUE);
			$i = 0;
								
			foreach($rows as $row)
			{
				printf("%d/%d create table %s for %s\n", ++$i, $total, $dbname, $row->vehicle_device);
				
				$histtable = $this->load->view("db/gps", FALSE, TRUE);			
				$sql = sprintf($histtable, strtolower($row->vehicle_device)."_gps");						
				
				printf("=== %s_gps\n", strtolower($row->vehicle_device));
				
				if ($historydb->query($sql))
				{
					printf("=== OK\n");
				}
				else
				{
					printf("=== FAIL\n");
				}
				

				$histinfotable = $this->load->view("db/info", FALSE, TRUE);
				$sql = sprintf($histinfotable, strtolower($row->vehicle_device)."_info");			
				
				printf("=== %s_info\n", strtolower($row->vehicle_device));
				
				if ($historydb->query($sql))
				{
					printf("=== OK\n");
				}
				else
				{
					printf("=== FAIL\n");
				}
				//sleep(1);
				
			}
		}
	}
	
	//New Cron History Move To GPSHISTORY TMP TO GPSHISTORY
	function history_tmp($sort="asc", $name="", $host="", $maxdata=10000, $offset = 0)
	{
		$start_time = date("d-m-Y H:i:s");
		
		/* $this->db->where("config_name", "tmpsocket");
		$q = $this->db->get("config");
		
		if ($q->num_rows())
		{
			$row = $q->row();
			
			if ($row->config_value == 1) 
			{				
				$t = dbmaketime($row->config_lastmodified);
				$delta = mktime()-$t;
				
				if ($delta > 216000)
				{
					unset($update);
					
					$update['config_value'] = 0;
					
					$this->db->where("config_name", "tmpsocket");
					$this->db->update("config", $update);	

					print("masih proses\r\n");
					return;				
				}
				
				print("masih proses\r\n");		
				return;						
			}
			
		}
		else
		{
			unset($insert);
			
			$insert['config_name'] = "tmpsocket";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}
		
		$update['config_value'] = 1;
		$this->db = $this->load->database("default",true);
		$this->db->where("config_name", "tmpsocket");
		$this->db->update("config", $update);	 */
		
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		$this->db->distinct();
		$this->db->order_by("vehicle_id",$sort);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id <>", "860");
		$this->db->where("vehicle_user_id <>", "1095");
		$this->db->where("vehicle_user_id <>", "1122");
		$this->db->where("vehicle_user_id <>", "1261");
		$this->db->where("vehicle_user_id <>", "1078");
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
			printf("Move Temp To Master %s (%d/%d)\n", $row->vehicle_device, ++$i, $totalvehicle);
			
			$this->db = $this->load->database("gpshistory2", TRUE);
			$table = strtolower($row->vehicle_device)."_gps";
			$tableinfo = strtolower($row->vehicle_device)."_info";
			
			$start = mktime();
			$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
			$devices = explode("@", $row->vehicle_device);
					
			// gps
			$offset = 0;
			unset($isdelete);
			while(1)
			{
				$this->db->limit($maxdata, $offset);
				$this->db->order_by("gps_time","asc");
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$this->db->where("gps_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($table);					
				$total = $q->num_rows();
							
				printf("=== jumlah move data tmp gps: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
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
				printf("=== delete tmp data\r\n");
				$this->dblama = $this->load->database("gpshistory2",TRUE);
				$this->dblama->order_by("gps_time","asc");
				$this->dblama->where("gps_name", $devices[0]);
				$this->dblama->where("gps_host", $devices[1]);
				$this->dblama->where("gps_time <=", date("Y-m-d H:i:s", $now));
				$qlama = $this->dblama->get($table);
				$tot_lama = $qlama->num_rows();
				$lim = $tot_lama - 2;
				
				if ($tot_lama > 2)
				{
					$this->dblama->order_by("gps_time","asc");
					$this->dblama->where("gps_name", $devices[0]);
					$this->dblama->where("gps_host", $devices[1]);
					$this->dblama->limit($lim);
					$this->dblama->where("gps_time <=", date("Y-m-d H:i:s", $now));
					$this->dblama->delete($table);
				}
				
			}

			// gps info
			unset($isdelete);

			$offset = 0;
			while(1)
			{
				$this->db->limit($maxdata, $offset);
				$this->db->order_by("gps_info_time","asc");
				$this->db->where("gps_info_device", $row->vehicle_device);
				$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($tableinfo);					
				$total = $q->num_rows();
							
				printf("=== jumlah move data tmp info: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
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
				printf("=== delete tmp data\r\n");
				$this->dblama = $this->load->database("gpshistory2",TRUE);
				$this->dblama->order_by("gps_info_time","asc");
				$this->dblama->where("gps_info_device", $row->vehicle_device);
				$this->dblama->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$qlama = $this->dblama->get($tableinfo);
				$tot_lama = $qlama->num_rows();
				$lim = $tot_lama - 2;
				
				if ($tot_lama > 2)
				{
					$this->dblama->order_by("gps_info_time","asc");
					$this->dblama->where("gps_info_device", $row->vehicle_device);
					$this->dblama->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
					$this->dblama->limit($lim);
					$this->dblama->delete($tableinfo);					
				}
			}
			
			printf("=== selesai\n");	
		} //finish foreach 
		
		/* $update['config_value'] = 0;
		$this->db = $this->load->database("default",true);
		$this->db->where("config_name", "tmpsocket");
		$this->db->update("config", $update);	 */
		$finish_time = date("d-m-Y H:i:s");
		printf("=== FINISH\n");
		
		//Send Email
		$cron_name = "history_tmp";
		
		unset($mail);
		$mail['subject'] =  "Report Cron - ".$cron_name."";
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Status     : Finish

Thanks

";
		$mail['dest'] = "it-dept@lacak-mobil.com";
		$mail['bcc'] = "budiyanto@lacak-mobil.com";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK");
	}
	
	function history_tmp_hour($sort="asc", $name="", $host="", $maxdata=100000, $offset = 0)
	{
		if (strlen($name) > 0)
		{
			$this->db->where("vehicle_device", $name."@".$host);
		}
		
		$this->db->distinct();
		$this->db->order_by("vehicle_id",$sort);
		$this->db->select("vehicle_type, vehicle_device, vehicle_info");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id <>", "860");
		$this->db->where("vehicle_user_id <>", "1095");
		$this->db->where("vehicle_user_id <>", "1122");
		$this->db->join("user","user_id = vehicle_user_id","left_outer");
		$this->db->where("user_company >",0);
		
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
			printf("Move Temp To Master %s (%d/%d)\n", $row->vehicle_device, ++$i, $totalvehicle);
			
			$this->db = $this->load->database("gpshistory2", TRUE);
			$table = strtolower($row->vehicle_device)."_gps";
			$tableinfo = strtolower($row->vehicle_device)."_info";
			
			$start = mktime();
			$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
			$devices = explode("@", $row->vehicle_device);
					
			// gps
			$offset = 0;
			unset($isdelete);
			while(1)
			{
				$this->db->limit(100);
				$this->db->order_by("gps_time","asc");
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$q = $this->db->get($table);					
				$total = $q->num_rows();
							
				printf("=== jumlah move data tmp gps: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
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
				printf("=== delete tmp data\r\n");
				$this->dblama = $this->load->database("gpshistory2",TRUE);
				$this->dblama->limit(100);
				$this->dblama->order_by("gps_time","asc");
				$this->dblama->where("gps_name", $devices[0]);
				$this->dblama->where("gps_host", $devices[1]);
				$qlama = $this->dblama->get($table);
				$tot_lama = $qlama->num_rows();
				$lim = $tot_lama - 2;
				
				if ($tot_lama > 2)
				{
					$this->dblama->order_by("gps_time","asc");
					$this->dblama->where("gps_name", $devices[0]);
					$this->dblama->where("gps_host", $devices[1]);
					$this->dblama->limit($lim);
					$this->dblama->delete($table);
				}
				
			}

			// gps info
			unset($isdelete);

			$offset = 0;
			while(1)
			{
				$this->db->limit(100);
				$this->db->order_by("gps_info_time","asc");
				$this->db->where("gps_info_device", $row->vehicle_device);
				$q = $this->db->get($tableinfo);					
				$total = $q->num_rows();
							
				printf("=== jumlah move data tmp info: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
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
				printf("=== delete tmp data\r\n");
				$this->dblama = $this->load->database("gpshistory2",TRUE);
				$this->dblama->limit(100);
				$this->dblama->order_by("gps_info_time","asc");
				$this->dblama->where("gps_info_device", $row->vehicle_device);
				$qlama = $this->dblama->get($tableinfo);
				$tot_lama = $qlama->num_rows();
				$lim = $tot_lama - 2;
				
				if ($tot_lama > 2)
				{
					$this->dblama->order_by("gps_info_time","asc");
					$this->dblama->where("gps_info_device", $row->vehicle_device);
					$this->dblama->limit($lim);
					$this->dblama->delete($tableinfo);					
				}
			}
			
			printf("=== selesai\n");	
		} //finish foreach
		
		/* $update['config_value'] = 0;
		$this->db = $this->load->database("default",true);
		$this->db->where("config_name", "tmpsocket");
		$this->db->update("config", $update);	 */
		printf("=== FINISH\n");	
	}
	
	function check_card_no()
	{
		printf("PROSES CHECK CARD NO START\n");
		$this->dbtransporter = $this->load->database("transporter",true);
		
		$this->dbtransporter->where("card_no_status",1);
		$q = $this->dbtransporter->get("card_number");
		$rows = $q->result();
		$total = count($rows);
		
		if ($total > 0)
		{
			printf("TOTAL CARD NUMBER : %s\n",$total);
			for($i=0;$i<$total;$i++)
			{
				$myno = substr($rows[$i]->card_no,2);
				$mynumber = "0".$myno;
				$this->db->where("vehicle_card_no", $mynumber);
				$this->db->join("user", "user_id = vehicle_user_id");
				//$this->db->where("vehicle_active_date2 > ",date("Ymd"));
				$this->db->limit(1);
				$qc = $this->db->get("vehicle");
				$rowc = $qc->row();
				if ($qc->num_rows == 0)
				{
					printf("%s;TIDAK TERDAFTAR \n",$rows[$i]->card_no);
				}
				else
				{
					$mydate = date("Ymd");
					if ($rowc->vehicle_active_date2 < $mydate)
					{
						printf("%s;TIDAK AKTIF;",$rows[$i]->card_no);
						printf("%s \n",$rowc->user_name);
					}
				}
			}
		}
		else
		{
			printf("DATA TIDAK ADA \n");
		}
		
		printf("FINISH \n");
		
	}
	
	function delete_id_gps($database="",$vehicle="",$host="")
	{
		printf("Start Delete ID GPS \n");
		$this->db = $this->load->database($database,true);
		printf("Cek ID GPS \n");
		$my_vehicle = $vehicle."@".$host;
		$table = sprintf("%s_gps", strtolower($my_vehicle));
		$this->db->order_by("gps_time","desc");
		$this->db->where("gps_name",$vehicle);
		$this->db->limit(1);
		//$this->db->where("gps_status","V");
		//$this->db->where("gps_time >","2013-04-02 14:00:00");
		$q = $this->db->get($table);
		$row = $q->result();
		//$this->db->delete($table);
		print_r($row);
		printf("SUKSES \n");
		
	}

	function sinkronisasi_user_erp()
	{
		printf("PROSES SINKRONISASI USER \r\n"); 
		$this->dberp = $this->load->database("erp",true);
		$this->dberp->where("user_id > ",0);
		$q = $this->db->get("user");
		$rows = $q->result();
		$total = count($rows);
		printf("GET USER : %s \r\n", $total);        
		
		foreach($rows as $row)
		{
			printf("PROSES USER : %s \r\n", $row->user_id); 
			unset($data);
			
			$data['user_pass'] = $row->user_pass;
			
			$this->db->select("user_id");
			$this->db->where("user_id", $row->user_id);
			$qu = $this->db->get("user");
			$ru = $qu->row();
			if (count($ru)>0)
			{
				printf("UPDATE USER : %s \r\n", $row->user_id); 
				$this->db->where("user_id", $row->user_id);	
				$this->db->update("user",$data);
			}
			else
			{
				
			}
			printf("FINISH USER : %s \r\n", $row->user_id); 
			printf("=============================================== \r\n"); 
		}
		
		printf("PROSES SELESAI"); 
		
	}
	
	function repair_table()
	{
		$table = "002100003787@0040t5_info";
		$this->db = $this->load->database("gpshistory2",true);
		
		/* $this->load->dbutil();
		if ($this->dbutil->repair_table($table))
		{
			echo 'Success!';
		} 
		else
		{
			echo "fail";
		}
		 */
		$sql = sprintf("REPAIR TABLE %s", $table);
		printf("%s\r\n", $sql);
		/* if ($this->db->query($sql))
			{
			echo "sukses";
			}
			else
			{
				echo "fail";
			}
		 */
		$reptable = $this->load->view("db/repair_table", FALSE, TRUE);	
		$sql = sprintf($reptable, strtolower($table));						
		if ($this->db->query($sql))
		{
			printf("SUKSES\n");
		}
		else
		{
			printf("FAIL\n");
		}
		
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
