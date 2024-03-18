<?php
include "base.php";

class Cron extends Base {
	var $vindex;
	var $m_last_smsreceive;

	function Cron()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		
		$this->m_last_smsreceive = 0;
	}
	
	function movetohist($tablegps, $tablegpshist, $vehicletype)
	{
		$t = mktime()-24*3600;
		$s = date("Y-m-d 15:59:59", $t);
		
		$log = "";
			
		$sql = sprintf("DELETE FROM %s%s WHERE gps_time = '0000-00-00 00:00:00' OR gps_time > '%s'", $this->db->dbprefix, $tablegps, date("Y-m-d H:i:s"));		
		printf("execute %s\r\n", $sql);
		$this->db->query($sql);
			
		$log .= $sql.";\r\n";
						
		$sql = "
			INSERT INTO ".$this->db->dbprefix.$tablegpshist."
			(
					gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
				,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real
				,	gps_odometer, gps_workhour
			)						
			SELECT DISTINCT	gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
					,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real
					,	gps_odometer, gps_workhour			
			FROM 	".$this->db->dbprefix.$tablegps."
			WHERE 	gps_time <= '".$s."' 
		";

		printf("execute %s\r\n", $sql);
		$this->db->query($sql);

		$log .= $sql.";\r\n";

		$this->db->where("gps_time <=", $s);
		$this->db->delete($tablegps);
													
		$sql = "OPTIMIZE TABLE ".$this->db->dbprefix.$tablegps;
		$this->db->query($sql);									
		
		$log .= $sql.";\r\n";			
		printf("execute %s\r\n", $sql);			

		sendlocalhost("schedule history: ".$vehicletype, $log);
	}

	function movetotemp()
	{
		$t2 = mktime()-24*3600;
		$s2 = date("Y-m-d 15:59:59", $t2);

		$t1 = mktime()-2*24*3600;
		$s1 = date("Y-m-d 15:59:59", $t1);
		
		$tblhists = $this->config->item("table_hist");
		
		foreach($tblhists as $val)
		{		
			$sql = "
				INSERT INTO ".$this->db->dbprefix."gps_temp
				(
						gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
					,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real
					,	gps_odometer, gps_workhour
				)						
				SELECT 		gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
						,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real
						,	gps_odometer, gps_workhour			
				FROM 	".$this->db->dbprefix.$val."
				WHERE 	TRUE
						AND (gps_time <= '".$s2."')
						AND (gps_time >= '".$s1."')
			";
			
			
			echo $sql.".....\r\n";
			$this->db->query($sql);

			//$log .= $sql.";\r\n";			
			
			//$this->addlog($log);
		}			
	}
	
	function addlog($log)
	{
		/*
		$filename = BASEPATH."../assets/data/summary.log";
		$fout = fopen($filename, "a");
		fwrite($fout, "[".date("Ymd His")."] ".$log."\r\n");
		fclose($fout);
		*/
	}

	function repair()
	{
		$tables = array_keys($this->config->item('vehicle_type'));
		foreach($tables as $table)
		{
			$tbls["gps_id"] = $this->gpsmodel->getGPSTable($table);
			$tbls["gps_info_id"] = $this->gpsmodel->getGPSInfoTable($table);

			foreach($tbls as $key=>$tbl)
			{
				echo "Process vehicle type ".$tbl."\r\n";

				$this->db->query("REPAIR TABLE ".$this->db->dbprefix.$tbl);

				$this->db->select($key." auto_inc");
				$this->db->limit(1);
				$this->db->order_by($key, "desc");
				$q = $this->db->get($tbl);

				if ($q->num_rows() > 0)
				{
					$row = $q->row();
					$auto_inc = $row->auto_inc + 1;
					
					$this->db->query("ALTER TABLE ".$this->db->dbprefix.$tbl." AUTO_INCREMENT = ".$auto_inc);
				}
			}
		}
	}
	
	function lastgps($vehicletype, $tablegps, $tablegpshist, $output="db")
	{
		$this->db->select("gps_name, gps_host, count(*) tot" );
		$this->db->where("gps_status <>", "V");
		$this->db->group_by("gps_name, gps_host");
		$q = $this->db->get($tablegps);
		
		$rows = $q->result();
		unset($totaldatagps);
		for($i=0; $i < count($rows); $i++)
		{
			$totaldatagps[strtoupper($rows[$i]->gps_name)][strtoupper($rows[$i]->gps_host)] = $rows[$i]->tot;
		}
		
		if (! isset($totaldatagps)) return;
		
		if ($vehicletype == "T1")
		{
			$this->db->where("vehicle_type = '".$vehicletype."' OR vehicle_type IS NULL", NULL);
		}
		else
		{
			$this->db->where("vehicle_type", $vehicletype);
		}

		$this->db->order_by("vehicle_device", "asc");
		$q = $this->db->get("vehicle");
		if ($q->num_rows() == 0) return;											
		
		$rows = $q->result();
		$totalvehicle = count($rows);		
		$j = 0;
		for($i=0; $i < $totalvehicle; $i++)
		{
			$devices = explode("@", $rows[$i]->vehicle_device);
			if (count($devices) == 1) continue; 
			
			if (isset($totaldatagps[strtoupper($devices[0])][strtoupper($devices[1])])) 
			{				
				if ($totaldatagps[strtoupper($devices[0])][strtoupper($devices[1])] > 0) 
				{
					$s = ++$j." dari ".$totalvehicle." :: data gps (".$vehicletype.") ".$rows[$i]->vehicle_device." found\r\n";

					echo $s;
					continue;
				}
			}
			
			$this->db->limit(1);
			$this->db->order_by("gps_time", "desc");
			$this->db->where("gps_name", $devices[0]);
			$this->db->where("gps_host", $devices[1]);
			$q = $this->db->get($tablegpshist);	
			
			if ($q->num_rows() == 0)
			{
				$s = ++$j." dari ".$totalvehicle." :: data gps (".$vehicletype.") ".$rows[$i]->vehicle_device." not found\r\n";
				continue;
			}
							
			$row = $q->row_array();
			
			$s = ++$j." dari (".$vehicletype.") ".$totalvehicle." ::".$rows[$i]->vehicle_device." ".$row['gps_time']."\r\n";							
			echo $s;
			
			$gpsid = $row['gps_id'];
			unset($row['gps_id']);
			
			if ($output == "stdout")
			{
				print_r($row);
				continue;
			}
			
			$mydb = $this->load->database("master", TRUE);
			$mydb->insert($tablegps, $row);
		}
	}	
	
	function removeoldhist()
	{
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

	}
	
	function histinfo($tableinfo, $tableinfohist, $vehicletype)
	{						
		$t = mktime()-24*3600;
		$s = date("Y-m-d 15:59:59", $t);
		
		$log = "";
		
		$sql = "
			INSERT INTO ".$this->db->dbprefix.$tableinfohist." 
			(
					gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data, gps_info_ad_input, gps_info_utc_coord
				,	gps_info_utc_date, gps_info_alarm_alert, gps_info_time, gps_info_status, gps_info_gps
			)						
			SELECT 	DISTINCT	gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data, gps_info_ad_input, gps_info_utc_coord
					,	gps_info_utc_date, gps_info_alarm_alert, gps_info_time, gps_info_status, gps_info_gps
			FROM 	".$this->db->dbprefix.$tableinfo."
			WHERE 	gps_info_time <= '".$s."' 
		";
		$log .= $sql."\r\n";				
		echo $sql."\r\n";
		$this->db->query($sql);							
					
		$sql = "DELETE FROM ".$this->db->dbprefix.$tableinfo." WHERE gps_info_time <= '".$s."'";
		$this->db->query($sql);									
		echo $sql."\r\n";
		$log .= $sql."\r\n";
		
		$sql = "OPTIMIZE TABLE ".$this->db->dbprefix.$tableinfo;
		$this->db->query($sql);									
		echo $sql."\r\n";
		$log .= $sql."\r\n";			
		
		sendlocalhost("schedule history info: ".$vehicletype, $log);			
	}	
	
	function movehistinfo()
	{						
		$t2 = mktime()-24*3600;
		$s2 = date("Y-m-d 15:59:59", $t2);

		$t1 = mktime()-2*24*3600;
		$s1 = date("Y-m-d 15:59:59", $t1);
		
		$tblhists = $this->config->item("table_hist_info");
		foreach($tblhists as $val)
		{		
			$sql = "
				INSERT INTO ".$this->db->dbprefix."gps_info_hist 
				(
						gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data, gps_info_ad_input, gps_info_utc_coord
					,	gps_info_utc_date, gps_info_alarm_alert, gps_info_time, gps_info_status, gps_info_gps
				)						
				SELECT 	DISTINCT	gps_info_device, gps_info_hdop, gps_info_io_port, gps_info_distance, gps_info_alarm_data, gps_info_ad_input, gps_info_utc_coord
						,	gps_info_utc_date, gps_info_alarm_alert, gps_info_time, gps_info_status, gps_info_gps
				FROM 	".$this->db->dbprefix.$val."
				WHERE 	TRUE
						AND (gps_info_time >= '".$s1."')
						AND (gps_info_time <= '".$s2."')
			";
			
			echo $sql."\r\n";			
			$this->db->query($sql);			
		}	
	}	

	
	function histcleanup()
	{
		while(1)
		{
			$sql = "SELECT gps_name, gps_host, gps_time, COUNT(*) tot FROM webtracking_gps_hist GROUP BY gps_name, gps_host, gps_time HAVING COUNT(*) > 1 LIMIT 1000";			
			$q = $this->db->query($sql);
			
			if ($q->num_rows() == 0) break;
			
			$rows = $q->result();
			for($i=0; $i < count($rows); $i++)
			{
				$this->db->where("gps_name", $rows[$i]->gps_name);
				$this->db->where("gps_host", $rows[$i]->gps_host);
				$this->db->where("gps_time", $rows[$i]->gps_time);
				$q = $this->db->get("gps_hist");
				if ($q->num_rows() <= 1) 
				{
					continue;
				}
				
				$rows1 = $q->result();
				for($j=0; $j < count($rows1)-1; $j++)
				{
					$ids[] = $rows1[$j]->gps_id;
				}
				
				$this->db->where_in("gps_id", $ids);
				$this->db->delete("gps_hist");
				$this->db->cache_delete_all();
			}			
		}
	}
	
	function appendlog($log)
	{
		/*
		$fout = fopen("lastgps".date("Ymd").".log", "a");
		fwrite($fout, $log);
		fclose($fout);
		*/
	}
	
	function longtime($s)
	{
		$jam = round($s/3600);
		$hari = floor($jam/24);
		$jam = $jam%24;
		
		if ($hari)
		{
			if ($jam)
			{
				return sprintf("%d hari %d jam", $hari, $jam);
			}
			
			return sprintf("%d hari", $hari);
		}
		
		return sprintf("%d jam", $jam);
	}
	
	function servicedown($name)
	{	
		// cek apakah histori lagi berjalan
			
		$this->db->where("config_name", "runhist");
		$q = $this->db->get("config");
		if ($q->num_rows())
		{
			$row = $q->row();
			if ($row->config_value == 1)
			{				
				return;
			}
		}		
		
		$body = "service ".$name." is down at ".date("d/m/Y H:i:s")."\r\n<br />";

		$start = "sc start ".$name;
		exec($start);
				
		$body .= "automatically restart [ ".$start." ] at ".date("d/m/Y H:i:s")."\r\n<br />";
		$body .= "\r\n<br />\r\n<br />To make sure, please check manually";
		
		sendlocalhost("service is down", $body);
	}
	
	function alert()
	{
		$lsleep = 2*60;
		
		while(1)
		{	
			echo exec("c:\\xampp\\php\\php.exe c:\\www\\dmap\\index.php cron doalert");			
			echo "[".date("Y-m-d H:i:s")."] SLEEPING...(".$lsleep.")\r\n";			
			sleep($lsleep);
		}
	}
	
	function doalert()
	{
		$this->smsreceive();			
		$this->maxspeedalert();			
		$this->parkalert();		
		$this->geofencealert();	
		$this->sosalert();			
		//$this->signalalert();
	}
	
	function alertdata()
	{
		$lsleep = 10*60;
		while (1)
		{
			$this->delayalert();
			break;
			echo "sleep 10 menit....\r\n";
			sleep($lsleep);
		}
	}
	
	function smsreceive()
	{
		echo "[".date("Y-m-d H:i:s")."] PROCESSING SMS RECEIVE...\r\n";			
		if ($this->m_last_smsreceive > 0)
		{
			$dt = mktime() - $this->m_last_smsreceive;
			if ($dt < 3600) return;
		}
		
		echo "Force sms receive.";
		
		$params['did'] = "yes";
		curl_post_async("http://tracker.gpsandalas.com/sms/receive", $params);		
		
		$this->m_last_smsreceive = mktime();
	}
	
	function sosalert()
	{
		$tbls = array("sms_sos_t4", "sms_sos_t4_farrasindo", "sms_sos_t4_new", "sms_sos_t5", "sms_sos_t5_pulse");
		
		$uniqid = uniqid();
		
		for($i=0; $i < count($tbls); $i++)
		{
			echo "[".date("Y-m-d H:i:s")."] PROCESSING ".$tbls[$i]."... \r\n";
			
			// cek jumlah sms hari ini
			
			$this->db->select("sms_sos_vehicle, COUNT(*) total");
			$this->db->group_by("sms_sos_vehicle");
			$this->db->where("TIMESTAMPDIFF(DAY, sms_sos_alerted, NOW()) = 0", null, false);
			$this->db->where("sms_sos_status", 2);
			$this->db->join("vehicle", "vehicle_device = sms_sos_vehicle");
			$this->db->join("user", "vehicle_user_id = user_id");
			$q = $this->db->get($tbls[$i]);
			
			$rows = $q->result();
			unset($sents);
			for($j=0; $j < count($rows); $j++)
			{
				$sents[$rows[$j]->sms_sos_vehicle] = $rows[$j]->total;
			}
			
			$this->db->where("TIMESTAMPDIFF(DAY, sms_sos_alerted, NOW()) = 0", null, false);
			$this->db->where("sms_sos_status", 1);
			$this->db->join("vehicle", "vehicle_device = sms_sos_vehicle");
			$this->db->join("user", "vehicle_user_id = user_id");
			$q = $this->db->get($tbls[$i]);
			
			if ($q->num_rows() == 0) continue;
			
			$rows = $q->result();
			
			for($j=0; $j < count($rows); $j++)
			{
				
				if (isset($sents[$rows[$j]->vehicle_device]) && ($sents[$rows[$j]->vehicle_device] > 5))
				{
					unset($update);
				
					$update['sms_sos_alerted'] = date("Y-m-d H:i:s");
					$update['sms_sos_status'] = 2;
					$this->db->where("sms_sos_id", $rows[$j]->sms_sos_id);
					$this->db->update($tbls[$i], $update);
				
					continue;
				}
				
				// send sms to user

				$hpuser = valid_mobile($rows[$j]->user_mobile);
				$created_t = dbmaketime($rows[$j]->sms_sos_created);

				if ($hpuser && ($rows[$j]->user_sms_notifikasi == 1))
				{
					echo "send sms to ".$hpuser."\r\n";
				
					if ($rows[$j]->user_agent == $this->config->item("GPSANDALASID"))
					{						
						$owner = "GPS Andalas Coorp";
					}
					else
					{
						$owner = "www.lacak-mobil.com";
					}
					
					$message = sprintf("Pada %s tombol SOS kendaraan %s %s ditekan. %s. U/ berhenti dikirim alert notifikasi kirim NOTIFIKASI OFF.", date("d/m/Y H:i:s", $created_t), $rows[$j]->user_login, $rows[$j]->vehicle_no, $owner);
					
					$this->smsmodel->sendsms1(array($hpuser), $message);
					sleep(10);
				}
				
				if (isset($sents[$rows[$j]->vehicle_device]))
				{
					$sents[$rows[$j]->vehicle_device]++;
				}
				else
				{
					$sents[$rows[$j]->vehicle_device] = 1;
				}
				
				unset($update);
				
				echo "update ".$rows[$j]->sms_sos_id."\r\n";

				$update['sms_sos_alerted'] = date("Y-m-d H:i:s");
				$update['sms_sos_status'] = 2;
				$this->db->where("sms_sos_id", $rows[$j]->sms_sos_id);
				$this->db->update($tbls[$i], $update);
			}
		}
	}
	
	function delayalert()
	{
		echo "Processing delay alert...\r\n";
		
		// init
		unset($logs);
		unset($hpagents);
		unset($mailagents);
		unset($reminders);
		
		$dexp = $this->config->item("SMS_REMINDER");
		// ambil log

		$uniqid = uniqid();
		
		$this->db->where_in("logs_type", array("2hexp", "exp"));
		$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);					
		$this->db->where("TIMESTAMPDIFF(DAY, logs_created, NOW()) = 0", null, false);
		$q = $this->db->get("logs");
		
		$rows = $q->result();
				
		for($i=0; $i < count($rows); $i++)
		{
			$logs[$rows[$i]->logs_content][$rows[$i]->logs_type] = $rows[$i]->logs_content;
		}

		$this->db->where_in("logs_type", array("delay1", "delay2"));
		$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);					
		//$this->db->where("TIMESTAMPDIFF(DAY, logs_created, NOW()) = 0", null, false);
		$q = $this->db->get("logs");
		
		$rows = $q->result();
		
		for($i=0; $i < count($rows); $i++)
		{
			$logs[$rows[$i]->logs_content][$rows[$i]->logs_type] = $rows[$i]->logs_content;
		}
		
		// ambil data agent
		
		$this->db->where("user_type", 3);
		$this->db->join("agent", "agent_id = user_agent");
		$q = $this->db->get("user");
		
		$rows = $q->result();
		
		for($i=0; $i < count($rows); $i++)
		{
			$hpagent = valid_mobile($rows[$i]->user_mobile);
			if ($hpagent)
			{
				$hpagents[$rows[$i]->user_agent][] = $hpagent;
			}
			
			if (valid_email($rows[$i]->user_mail))
			{
				$mailagents[$rows[$i]->user_agent][] = $rows[$i]->user_mail;
			}
			
			$agent_sms_1_expired[$rows[$i]->user_agent] = $rows[$i]->agent_sms_1_expired;
			$agent_sms_n_expired[$rows[$i]->user_agent] = $rows[$i]->agent_sms_n_expired;
		}				
		
		// ambil semua kendaraan
		
		$this->db->where("vehicle_status", 1);
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		
		$q->free_result();
		
		$this->vindex = 0;		
		for($i=0; $i < count($rows); $i++)
		{
			// cek expired
			$this->vindex++;
			printf("%d %s %s\r\n", $i+1, $rows[$i]->vehicle_device, $rows[$i]->vehicle_no);
			
			$rows[$i]->agent_sms_1_expired = isset($agent_sms_1_expired[$rows[$i]->user_agent]) ? $agent_sms_1_expired[$rows[$i]->user_agent] : $this->config->item("SMS_EXPIRED_1_VEHICLE");
			$rows[$i]->agent_sms_n_expired = isset($agent_sms_n_expired[$rows[$i]->user_agent]) ? $agent_sms_n_expired[$rows[$i]->user_agent] : $this->config->item("SMS_EXPIRED_N_VEHICLE");

			if ($rows[$i]->user_agent == $this->config->item("GPSANDALASID"))			
			{
				$rows[$i]->ownermail = "zad_anwar@gpsandalas.com";
				$rows[$i]->hpagent = "031-51503261";
				$rows[$i]->owner = "www.gpsandalas.com";
				
				$rows[$i]->mailservice = "http://tracker.gpsandalas.com/cron/sendmail";
				$rows[$i]->sender = "support@gpsandalas.com";
				$rows[$i]->sendername = "GPS Andalas Coorp.";
				$rows[$i]->url = "http://www.gpsandalas.com";
				$rows[$i]->smsagent = "08123281232";
				
			}
			else
			{
				$rows[$i]->ownermail = "jaya@vilanishop.com";
				$rows[$i]->owner = "www.lacak-mobil.com";
				$rows[$i]->mailservice = "http://www.lacak-mobil.com/cron/sendmail";
				$rows[$i]->sender = "support@lacak-mobil.com";
				$rows[$i]->sendername = "lacak-mobil.com";
				$rows[$i]->url = "http://www.lacak-mobil.com";
				
				if (isset($hpagents[$rows[$i]->user_agent]))
				{
					$rows[$i]->smsagent = $hpagents[$rows[$i]->user_agent][0];
					$rows[$i]->hpagent = implode(",", $hpagents[$rows[$i]->user_agent]);
				}
				else
				{
					$rows[$i]->hpagent = "081317884830";
					$rows[$i]->smsagent = "081317884830";
				}
			}
			
			if (! isset($logs[$rows[$i]->vehicle_device]['exp']))
			{				
				$t = dbintmaketime($rows[$i]->vehicle_active_date2, 0);
								
				if (date("Ymd", $t) < date("Ymd"))
				{
					// hanya dikirim 2 hari sekali
					
					if ((date('d')%2) == 1)
					{				
						$rows[$i]->expired = $t;
						$reminders['exp'][$rows[$i]->user_id][] = $rows[$i];
						$logs[$rows[$i]->vehicle_device]['exp'] = $rows[$i]->vehicle_device;
					}
				}
			}
						
			// cek n day lagi expired
			
			if (! isset($logs[$rows[$i]->vehicle_device]['2hexp']))
			{
				$t = dbintmaketime($rows[$i]->vehicle_active_date2, 0);
				$t2h = mktime() + 3600*24*$dexp;
				
				if (date("Ymd", $t2h) == date("Ymd", $t))
				{
					$rows[$i]->expired = $t2h;
					$rows[$i]->nexpired = $dexp;
					$reminders['2hexp'][$rows[$i]->user_id][] = $rows[$i];
					$logs[$rows[$i]->vehicle_device]['2hexp'] = $rows[$i]->vehicle_device;
				}
			}
			
			// cek delay data					
			
			$devices = explode("@", $rows[$i]->vehicle_device);
			if (count($devices) < 2) continue;
			
			$rows[$i]->info = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], false, false, 0, $rows[$i]->vehicle_type);
			
			//if (isset($logs[$rows[$i]->vehicle_device]['delay2'])) continue;
			
			if (! isset($rows[$i]->info->css_delay_index))
			{
				//belum ditangani
				continue;
			}
			
			if ($rows[$i]->info->css_delay_index > 1)
			{
				if ((! isset($logs[$rows[$i]->vehicle_device]['delay1'])) && (! isset($logs[$rows[$i]->vehicle_device]['delay2']))) continue;
				
				$this->reminderupdate($rows[$i], $mailagents);
				continue;
			}
			
			if (isset($logs[$rows[$i]->vehicle_device]['delay2'])) continue; 
			
			if ($rows[$i]->info->css_delay_index == 1)
			{
				if (! isset($logs[$rows[$i]->vehicle_device]['delay1']))
				{
					$this->reminderdelay("delay1", $rows[$i], $mailagents);
					continue;
				}
				
				continue;
			}
			
			$this->reminderdelay("delay2", $rows[$i], $mailagents);
		}
		
		if (! isset($reminders))
		{			
			sleep(1);
			return;
		}
		
		foreach($reminders as $key=>$val)
		{
			switch($key)
			{
				case "exp":
					$this->reminderexpired($val, $mailagents);
				break;
				case "2hexp":
					$this->reminderexpireinnday($val, $mailagents);
				break;
			}
		}
		
		sleep(1);
	}
	
	function reminderupdate($vehicle, $mailagents)
	{
		return;
		
		$t = dbmaketime($vehicle->info->gps_time)+7*3600;
		
		$message = sprintf($this->config->item("SMS_DATA_UPDATE"), date("d/m/Y H:i", $t), $vehicle->user_login, $vehicle->vehicle_no, $vehicle->owner);
		$subject = sprintf("[%s] Kend %s %s telah ter-update.", $vehicle->owner, $vehicle->user_login, $vehicle->vehicle_no);
		
		echo $this->vindex." :: ".$message."\r\n";

		$this->db->where_in("logs_type", array("delay1", "delay2"));
		$this->db->where("logs_content", $vehicle->vehicle_device);
		$this->db->delete("logs");

		// send sms
		
		$hpuser = valid_mobile($vehicle->user_mobile);

		if ($hpuser && ($vehicle->user_sms_notifikasi == 1))
		{
			echo "send sms to ".$hpuser."\r\n";
			
			$this->smsmodel->sendsms1(array($hpuser), $message);
			sleep(10);
		}
		
		// send email ke user dan agent
		
		unset($bcc);
		if (valid_email($vehicle->user_mail) && ($vehicle->user_sms_notifikasi == 1))
		{
			if (isset($mailagents[$vehicle->user_agent]))
			{
				$bcc = $mailagents[$vehicle->user_agent];
				$bcc[] = "cron@adilahsoft.com";
				$bcc[] = "prastgtx@gmail.com";
				$bcc[] = $vehicle->ownermail;
				maillocalhost($subject, $message, $vehicle->user_mail, $vehicle->mailservice, $vehicle->sender, $vehicle->sendername, false, implode(",", array_unique($bcc)));
				
				echo "send mail to ".$vehicle->user_mail." ".implode(",", $bcc)."\r\n";
			}
			else
			{
				$bcc[] = "cron@adilahsoft.com";
				$bcc[] = "prastgtx@gmail.com";
				$bcc[] = $vehicle->ownermail;
				maillocalhost($subject, $message, $vehicle->user_mail, $vehicle->mailservice, $vehicle->sender, $vehicle->sendername, false, implode(",", array_unique($bcc)));
				
				echo "send mail to ".$vehicle->user_mail."\r\n";
			}
									
		}
		else
		if (isset($mailagents[$vehicle->user_agent]))
		{
			$bcc = $mailagents[$vehicle->user_agent];
			$bcc[] = "cron@adilahsoft.com";
			$bcc[] = "prastgtx@gmail.com";
			$bcc[] = $vehicle->ownermail;

			maillocalhost($subject, $message, $vehicle->sender, $vehicle->mailservice, $vehicle->sender, $vehicle->sendername, false, implode(",", array_unique($bcc)));
			
			echo "send mail to ".implode(",", $bcc)."\r\n";
		}			
			
		echo "\r\n";
		sleep(1);
	}
	
	function reminderdelay($key, $vehicle, $mailagents)
	{
		$t = dbmaketime($vehicle->info->gps_time)+7*3600;
		$dt = mktime()-$t;

		$msg = sprintf($this->config->item('SMS_DATA_TERLAMBAT_MESSAGE'), $vehicle->vehicle_no, $vehicle->user_login, $this->longtime($dt), date("d/m/Y H:i", $t), $vehicle->hpagent, $vehicle->owner);
		$subject = sprintf($this->lang->line('lnoticedelay_subject'), $vehicle->sendername, $vehicle->vehicle_name." ".$vehicle->vehicle_no );

    	$params['vehicle'] = $vehicle;		
    	$params['lastreceive'] = $t;				
		$params['ownerurl'] = $vehicle->url;
		$params['owner'] = $vehicle->sendername;
		
		$message = $this->load->view("vehicle/noticedelay", $params, true);
				
		echo $this->vindex." :: ".$msg."\r\n";

		// send sms
		
		$hpuser = valid_mobile($vehicle->user_mobile);

		if ($hpuser && ($vehicle->user_sms_notifikasi == 1))
		{
			echo "send sms to ".$hpuser."\r\n";
			
			//$this->smsmodel->sendsms1(array($hpuser), $msg);
			//sleep(10);
		}
		
		// send email ke user dan agent
		
		unset($bcc);
		if (valid_email($vehicle->user_mail) && ($vehicle->user_sms_notifikasi == 1))
		{
			if (isset($mailagents[$vehicle->user_agent]))
			{
				$bcc = $mailagents[$vehicle->user_agent];
				$bcc[] = "cron@adilahsoft.com";
				$bcc[] = "prastgtx@gmail.com";
				$bcc[] = $vehicle->ownermail;
				maillocalhost($subject, $message, $vehicle->user_mail, $vehicle->mailservice, $vehicle->sender, $vehicle->sendername, false, implode(",", array_unique($bcc)));
				
				echo "send mail to ".$vehicle->user_mail." ".implode(",", $bcc)."\r\n";
			}
			else
			{
				$bcc[] = "cron@adilahsoft.com";
				$bcc[] = "prastgtx@gmail.com";
				$bcc[] = $vehicle->ownermail;
				maillocalhost($subject, $message, $vehicle->user_mail, $vehicle->mailservice, $vehicle->sender, $vehicle->sendername, false, implode(",", array_unique($bcc)));
				
				echo "send mail to ".$vehicle->user_mail."\r\n";
			}
									
		}
		else
		if (isset($mailagents[$vehicle->user_agent]))
		{
			$bcc = $mailagents[$vehicle->user_agent];
			$bcc[] = "cron@adilahsoft.com";
			$bcc[] = "prastgtx@gmail.com";
			$bcc[] = $vehicle->ownermail;

			maillocalhost($subject, $message, $vehicle->sender, $vehicle->mailservice, $vehicle->sender, $vehicle->sendername, false, implode(",", array_unique($bcc)));
			
			echo "send mail to ".implode(",", $bcc)."\r\n";
		}			
		
		echo "\r\n";

		unset($insert);

		$insert['logs_type'] =  $key;
		$insert['logs_created'] =  date("Y-m-d H:i:s");
		$insert['logs_content'] =  $vehicle->vehicle_device;
		
		$this->db->insert("logs", $insert);				

		sleep(10);
	}
	
	function reminderexpired($users, $mailagents)
	{
		foreach($users as $userid=>$vehicles)
		{
			if (count($vehicles) == 1)
			{	
				$msg = sprintf($vehicles[0]->agent_sms_1_expired, $vehicles[0]->user_login, $vehicles[0]->vehicle_no, date("d/m/Y", $vehicles[0]->expired), $vehicles[0]->hpagent);
				$subject = sprintf("[%s] Masa aktif layanan %s %s telah habis", $vehicles[0]->sendername, $vehicles[0]->user_login, $vehicles[0]->vehicle_no );

				unset($insert);

				$insert['logs_type'] =  "exp";
				$insert['logs_created'] =  date("Y-m-d H:i:s");
				$insert['logs_content'] =  $vehicles[0]->vehicle_device;
				
				$this->db->insert("logs", $insert);				
			}
			else
			{
				$svehicle = "";
				foreach($vehicles as $vehicle)
				{					
					$t = dbintmaketime($vehicle->vehicle_active_date2, 0);
					
					if (strlen($svehicle) > 0)
					{
						$svehicle .= ",";
					}
					
					$svehicle .= $vehicle->vehicle_no." ".date("d/m/Y", $t);

					unset($insert);
					
					$insert['logs_type'] =  "exp";
					$insert['logs_created'] =  date("Y-m-d H:i:s");
					$insert['logs_content'] =  $vehicle->vehicle_device;
					
					$this->db->insert("logs", $insert);							
				}
				
				$msg = sprintf($vehicles[0]->agent_sms_n_expired, $vehicles[0]->user_login, $svehicle, $vehicles[0]->hpagent);
				$subject = sprintf("[%s] Masa aktif layanan %s telah habis", $vehicles[0]->sendername, $vehicles[0]->user_login );

			}
			
			echo $msg."\r\n";

			// send sms
			
			$hpuser = valid_mobile($vehicles[0]->user_mobile);

			if ($hpuser && ($vehicles[0]->user_sms_notifikasi == 1))
			{
				echo "send sms to ".$hpuser."\r\n";
				
				$this->smsmodel->sendsms1(array($hpuser), $msg);
				sleep(10);
			}
			
			// send email ke user dan agent
			
			unset($bcc);
			if (valid_email($vehicles[0]->user_mail) && ($vehicles[0]->user_sms_notifikasi == 1))
			{
				if (isset($mailagents[$vehicles[0]->user_agent]))
				{
					$bcc = $mailagents[$vehicles[0]->user_agent];
					$bcc[] = "cron@adilahsoft.com";
					$bcc[] = "prastgtx@gmail.com";
					$bcc[] = $vehicle->ownermail;
					maillocalhost($subject, $msg, $vehicles[0]->user_mail, $vehicles[0]->mailservice, $vehicles[0]->sender, $vehicles[0]->sendername, false, implode(",", $bcc));
					
					echo "send mail to ".$vehicles[0]->user_mail." ".implode(",", $bcc)."\r\n";
				}
				else
				{
					$bcc[] = $vehicle->ownermail;
					$bcc[] = "cron@adilahsoft.com";
					$bcc[] = "prastgtx@gmail.com";
					maillocalhost($subject, $msg, $vehicles[0]->user_mail, $vehicles[0]->mailservice, $vehicles[0]->sender, $vehicles[0]->sendername, false, implode(",", $bcc));
					
					echo "send mail to ".$vehicles[0]->user_mail."\r\n";
				}
										
			}
			else
			if (isset($mailagents[$vehicles[0]->user_agent]))
			{
				$bcc = $mailagents[$vehicles[0]->user_agent];
				$bcc[] = "cron@adilahsoft.com";
				$bcc[] = "prastgtx@gmail.com";
				$bcc[] = $vehicle->ownermail;

				maillocalhost($subject, $msg, $vehicles[0]->sender, $vehicles[0]->mailservice, $vehicles[0]->sender, $vehicles[0]->sendername, false, implode(",", $bcc));
				
				echo "send mail to ".implode(",", $bcc)."\r\n";
			}			
			
			echo "\r\n";
			sleep(1);
		}
	}
	
	function reminderexpireinnday($users, $mailagents)
	{		
		foreach($users as $userid=>$vehicles)
		{
			if (count($vehicles) == 1)
			{	
				$msg = sprintf($this->config->item("SMS_REMINDER_IN_N_DAY_1_VEHICLE"), $vehicles[0]->user_login, $vehicles[0]->vehicle_no, $vehicles[0]->nexpired, date("d/m/Y", $vehicles[0]->expired), $vehicles[0]->hpagent, $vehicles[0]->owner);
				$subject = sprintf("[%s] Peringatan masa aktif layanan %s %s", $vehicles[0]->sendername, $vehicles[0]->user_login, $vehicles[0]->vehicle_no );
				
				unset($insert);

				$insert['logs_type'] =  "2hexp";
				$insert['logs_created'] =  date("Y-m-d H:i:s");
				$insert['logs_content'] =  $vehicles[0]->vehicle_device;
				
				$this->db->insert("logs", $insert);				
			}
			else
			{
				$svehicle = "";
				foreach($vehicles as $vehicle)
				{					
					if (strlen($svehicle) > 0)
					{
						$svehicle .= ",";
					}
					
					$svehicle .= $vehicle->vehicle_no;

					unset($insert);
					
					$insert['logs_type'] =  "2hexp";
					$insert['logs_created'] =  date("Y-m-d H:i:s");
					$insert['logs_content'] =  $vehicle->vehicle_device;
					
					$this->db->insert("logs", $insert);							
				}
				
				$msg = sprintf($this->config->item("SMS_REMINDER_IN_N_DAY_N_VEHICLE"), $vehicles[0]->user_login, $vehicles[0]->nexpired, date("d/m/Y", $vehicles[0]->expired), $svehicle, $vehicles[0]->hpagent, $vehicles[0]->owner);
				$subject = sprintf("[%s] Peringatan masa aktif layanan %s", $vehicles[0]->sendername, $vehicles[0]->user_login );
			}
			
			echo $msg."\r\n";							
			
			// send sms
			
			$hpuser = valid_mobile($vehicles[0]->user_mobile);

			if ($hpuser && ($vehicles[0]->user_sms_notifikasi == 1))
			{
				echo "send sms to ".$hpuser."\r\n";
				
				$this->smsmodel->sendsms1(array($hpuser), $msg);
				sleep(10);
			}
			
			// send email ke user dan agent
			
			unset($bcc);
			if (valid_email($vehicles[0]->user_mail) && ($vehicles[0]->user_sms_notifikasi == 1))
			{
				if (isset($mailagents[$vehicles[0]->user_agent]))
				{
					$bcc = $mailagents[$vehicles[0]->user_agent];
					$bcc[] = "cron@adilahsoft.com";
					$bcc[] = "prastgtx@gmail.com";
					$bcc[] = $vehicles[0]->ownermail;
					maillocalhost($subject, $msg, $vehicles[0]->user_mail, $vehicles[0]->mailservice, $vehicles[0]->sender, $vehicles[0]->sendername, false, implode(",", $bcc));
					
					echo "send mail to ".$vehicles[0]->user_mail." ".implode(",", $bcc)."\r\n";
				}
				else
				{
					$bcc[] = "cron@adilahsoft.com";
					$bcc[] = "prastgtx@gmail.com";
					$bcc[] = $vehicles[0]->ownermail;

					maillocalhost($subject, $msg, $vehicles[0]->user_mail, $vehicles[0]->mailservice, $vehicles[0]->sender, $vehicles[0]->sendername, false, implode(",", $bcc));
					
					echo "send mail to ".$vehicles[0]->user_mail."\r\n";
				}
										
			}
			else
			if (isset($mailagents[$vehicles[0]->user_agent]))
			{
				$bcc = $mailagents[$vehicles[0]->user_agent];
				$bcc[] = "cron@adilahsoft.com";
				$bcc[] = "prastgtx@gmail.com";
				$bcc[] = $vehicles[0]->ownermail;

				maillocalhost($subject, $msg, "support@adilahsoft.com", $vehicles[0]->mailservice, $vehicles[0]->sender, $vehicles[0]->sendername, false, implode(",", $bcc));
				
				echo "send mail to ".implode(",", $bcc)."\r\n";
			}
						
			echo "\r\n";															
			sleep(1);
		}		
	}

	function runcheckgps($sleep=500)
	{
		while(1)
		{
			$this->checkgps();
			sleep($sleep);
		}
	}
	
	function checkgps()
	{	
		echo "[".date("Y-m-d H:i:s")."] CHECKING GPS....\r\n";
			
		// cek apakah histori lagi berjalan
			
		$this->db->where("config_name", "runhist");
		$q = $this->db->get("config");
		if ($q->num_rows())
		{
			$row = $q->row();
			if ($row->config_value == 1)
			{
				echo "Process historical sedang berlangsung...checkgps diskip.";
				return;
			}
		}
		
		$max = 5;
		$tbls = array("gps", "gps_farrasindo", "gps_gtp", "gps_indogps", "gps_sms", "gps_t1_1", "gps_gtp_new", "gps_t1_2", "gps_t5", "gps_t5_pulse", "gps_pln");
				
		for($i=0; $i < count($tbls); $i++)
		{						
			echo "[".date("Y-m-d H:i:s")."] PROCESSING ".$tbls[$i]."... \r\n";
			
			$q = $this->db->query("SELECT gps_time FROM ".$this->db->dbprefix.$tbls[$i]." ORDER BY gps_time DESC LIMIT 1 OFFSET 0");
			if ($q->num_rows() == 0) continue;

			$row = $q->row();
			
			$t = dbmaketime($row->gps_time)+7*3600;
			$dt = mktime()-$t;
			
			if ($dt < $max*60) continue;
			
			echo "WARNING!!! GPS MATI!!!! ".$tbls[$i]."\r\n";
			
			$servicenames = $this->config->item("service");
			foreach($servicenames[$tbls[$i]] as $servicename)
			{
				$this->servicerestart($servicename);
			}
			
			//system("c:\\www\\dmap\\bat\\".$tbls[$i].".bat");
			
			unset($params);
			
			$hp = array();
			$hp = array_merge($hp, $this->config->item("SMS_LACAKMOBIL"));
			$hp = array_merge($hp, $this->config->item("SMS_GPSANDALAS"));
			$hp = array_merge($hp, $this->config->item("SMS_OWNER"));
			$hp[] = "081703559911";
			$hp[] = "02197878136";
			
			$params['content'] = sprintf("GPS Server (%s) tidak terupdate selama %d menit. Server has been restart automatically.\r\nMohon dilakukan pengecekan.\r\n support adilahsoft.com %s", $tbls[$i], $max, "085717019778");
			$params['dest'] = $hp;		
			$xml = $this->load->view("sms/send", $params, true);
			
			//$this->smsmodel->sendsms($xml);
			//maillocalhost("GPS Server mati", $params['content'], 'support@adilahsoft.com, jaya@vilanishop.com, jayatriyadi@hotmail.com, prastgtx@gmail.com');
			maillocalhost("GPS Server mati", $params['content'], 'support@adilahsoft.com');
		}
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
	
	function servicerestart($servicename)
	{
		// kill
		
		$pid = $this->getpid($servicename);
		if ($pid)
		{
				
			printf("%s: PID=%d\r\n", $servicename, $pid); 
			
			$kill = sprintf("taskkill /PID %d /F", $pid);		
			exec($kill);					
			printf("%s: killed\r\n", $servicename); 
			
			sleep(10);
			
		}
		
		// start 
		
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
	
	
	function parkalert()
	{
		$uniqid = uniqid();
		$tbls = array("smsparking_indogps", "smsparking_t1", "smsparking_t1_1", "smsparking_t1_pln", "smsparking_t3", "smsparking_t4", "smsparking_t4_farrasindo", "smsparking_t4_new", "smsparking_t1_2", "smsparking_t5", "smsparking_t5_pulse");
		
		echo "[".date("Y-m-d H:i:s")."] START MAX SPEED ALERT.....\r\n";

		for($j=0; $j < count($tbls); $j++)
		{
			echo "[".date("Y-m-d H:i:s")."] PROCESSING ".$tbls[$j]."... \r\n";

			$this->db->select($tbls[$j].".*, vehicle.*, t_owner.*, t_agent.user_mobile agent_mobile");		
			$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);
			$this->db->where("MINUTE(TIMEDIFF(smsparking_end, smsparking_begin)) >= smsparking_setting", null);
			$this->db->where("smsparking_alert", 0);
			$this->db->join("vehicle", "vehicle_device = smsparking_vehicle");
			$this->db->join("user t_owner", "t_owner.user_id = vehicle_user_id");
			$this->db->join("user t_agent", "t_owner.user_agent = t_agent.user_agent AND t_agent.user_type = 3", "left outer");
			$q = $this->db->get($tbls[$j]);
			
			if ($q->num_rows() == 0)
			{
				continue;
			}
			
			$rows = $q->result();
			for($i=0; $i < count($rows); $i++)
			{
				$hp = valid_mobile($rows[$i]->user_mobile);
				$hp1 = valid_mobile($rows[$i]->agent_mobile);
				if ((! $hp) && (! $hp1)) continue;		
				
				$tbegin = dbmaketime($rows[$i]->smsparking_begin);
				$tend = dbmaketime($rows[$i]->smsparking_end);
				
				$diff = floor(($tend-$tbegin)/60);
				
				unset($params);
				
				$params['content'] = sprintf($this->config->item('SMS_ALERT_MAX_PARK'), date("d/m/Y H:i", $tend+7*3600), $rows[$i]->vehicle_no, number_format($diff, 0, "", ""), number_format($rows[$i]->smsparking_setting, 0, "", ""));
				$params['dest'] = array_unique(array_filter(array($hp, $hp1)));	
				$xml = $this->load->view("sms/send", $params, true);
				
				$this->smsmodel->sendsms($xml);						
				echo $xml."\r\n";
				
				unset($update);
				$update['smsparking_alerted'] = date("Y-m-d H:i:s");
				$update['smsparking_alert'] = 1;
				
				$this->db->where("smsparking_int", $rows[$i]->smsparking_int);
				$this->db->update($tbls[$j], $update);
								
				sleep(10);
			}
		}
			
	}

	function signalalert()
	{
		$uniqid = uniqid();
		$tbls = array("smssignal_indogps", "smssignal_t1", "smssignal_t1_1", "smssignal_t1_pln", "smssignal_t3", "smssignal_t4", "smssignal_t4_farrasindo", "smssignal_t4_new", "smssignal_t1_2", "smssignal_t5", "smssignal_t5_pulse");
		
		echo "[".date("Y-m-d H:i:s")."] START SIGNAL ALERT.....\r\n";

		for($j=0; $j < count($tbls); $j++)
		{
			echo "[".date("Y-m-d H:i:s")."] PROCESSING ".$tbls[$j]."... \r\n";
			
			$this->db->select("smssignal_vehicle, count(*) total");
			$this->db->group_by("smssignal_vehicle");
			$this->db->where("TIMESTAMPDIFF(DAY, smssignal_created, NOW()) = 0", null, false);
			$this->db->where("smssignal_status", 2);
			$q = $this->db->get($tbls[$j]);
			
			$rows = $q->result();
			for($i=0; $i < count($rows); $i++)
			{
				$nsms[$rows[$i]->smssignal_vehicle] = $rows[$i]->total;
			}
			
			$this->db->order_by("smssignal_created", "asc");
			$this->db->select($tbls[$j].".*, vehicle.*, t_owner.*, t_agent.user_mobile agent_mobile");		
			$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);
			$this->db->where("smssignal_status", 1);
			$this->db->join("vehicle", "vehicle_device = smssignal_vehicle");
			$this->db->join("user t_owner", "t_owner.user_id = vehicle_user_id");
			$this->db->join("user t_agent", "t_owner.user_agent = t_agent.user_agent AND t_agent.user_type = 3", "left outer");
			$q = $this->db->get($tbls[$j]);
			
			if ($q->num_rows() == 0)
			{
				continue;
			}
			
			$rows = $q->result();
			for($i=0; $i < count($rows); $i++)
			{		
				if (! isset($nsms[$rows[$i]->smssignal_vehicle]))
				{
					$nsms[$rows[$i]->smssignal_vehicle] = 0;
				}
				
				if ($nsms[$rows[$i]->smssignal_vehicle] > 10)
				{
					continue;
				}
													
				$hp = valid_mobile($rows[$i]->user_mobile);
				$hp1 = valid_mobile($rows[$i]->agent_mobile);
				if ((! $hp) && (! $hp1)) continue;	
				
				unset($dest);
				
				if ($hp)
				{
					if (! isset($notified[$rows[$i]->smssignal_vehicle][$hp]))
					{
						$dest[] = $hp;
						$notified[$rows[$i]->smssignal_vehicle][$hp] = true;
					}
				}	
				
				if ($hp1)
				{
					if (! isset($notified[$rows[$i]->smssignal_vehicle][$hp1]))
					{
						$dest[] = $hp1;
						$notified[$rows[$i]->smssignal_vehicle][$hp1] = true;
					}
				}	
				
				if (! isset($dest))
				{
					continue;
				}
				
				$rows[$i]->smssignal_created_t = dbmaketime($rows[$i]->smssignal_created);
				
				if ($rows[$i]->user_agent == $this->config->item("GPSANDALASID"))
				{
					$ttd = "www.gpsandalas.com";
				}
				else
				{
					$ttd = "www.lacak-mobil.com";
				}
				
				unset($params);
				
				if ($rows[$i]->smssignal_type == 1)
				{
					$params['content'] = sprintf("Kend %s %s pd %s memasuki blank spot. Signal NOK. %s", $rows[$i]->vehicle_no, $rows[$i]->user_login, date("d/m/Y H:i", $rows[$i]->smssignal_created_t), $ttd);
				}
				else
				{
					$params['content'] = sprintf("Kend %s %s pd %s meninggalkan blank spot. Signal OK. %s", $rows[$i]->vehicle_no, $rows[$i]->user_login, date("d/m/Y H:i", $rows[$i]->smssignal_created_t), $ttd);
				}
								
				$params['dest'] = array_unique($dest);	
				$xml = $this->load->view("sms/send", $params, true);
				
				$this->smsmodel->sendsms($xml);						
				echo $xml."\r\n";
				
				$nsms[$rows[$i]->smssignal_vehicle]++;
				
				unset($update);
				$update['smssignal_status'] = 2;
				$update['smssignal_alerted'] = date("Y-m-d H:i:s");
				$this->db->where("smssignal_vehicle", $rows[$i]->vehicle_device);
				$this->db->update($tbls[$j], $update);

				sleep(10);
			}
		}
			
	}
	
	function geofencealert()
	{
		$tbls = array("smsgeofence_indogps", "smsgeofence_t1", "smsgeofence_t1_1", "smsgeofence_t1_pln", "smsgeofence_t3", "smsgeofence_t4", "smsgeofence_t4_farrasindo", "smsgeofence_t4_new", "smsgeofence_t1_2", "smsgeofence_t5", "smsgeofence_t5_pulse");
		
		echo "[".date("Y-m-d H:i:s")."] START MAX GEOFENCE ALERT\r\n";
		
		$uniqid = uniqid();
		
		for($j=0; $j < count($tbls); $j++)
		{
			echo "[".date("Y-m-d H:i:s")."] PROCESSING ".$tbls[$j]."... \r\n";
			
			$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);
			$this->db->where("smsgeofence_alert", 1);
			$this->db->join("geofence", "geofence_id = smsgeofence_geofence", "left outer");  		
			$q = $this->db->get($tbls[$j]);
			
			if ($q->num_rows() == 0)
			{
				continue;
			}
			
			$rowgeo = $q->row();			
			$t = dbmaketime($rowgeo->smsgeofence_time)+7*3600;
			
			// update
			
			unset($update);
			
			$update['smsgeofence_alerttime'] = date("Y-m-d H:i:s");
			$update['smsgeofence_alert'] = 2;
			
			$this->db->where("smsgeofence_id", $rowgeo->smsgeofence_id);
			$this->db->update($tbls[$j], $update);
			
			// process alert
			
			$this->db->select("vehicle.*, t_owner.*, t_agent.user_mobile agent_mobile");
			$this->db->where("vehicle_device", $rowgeo->smsgeofence_device);
			$this->db->join("user t_owner", "t_owner.user_id = vehicle_user_id");
			$this->db->join("user t_agent", "t_owner.user_agent = t_agent.user_agent AND t_agent.user_type = 3", "left outer");
			
			$q = $this->db->get("vehicle");
			
			if ($q->num_rows() == 0)
			{
				continue;
			}
			
			$rowuser = $q->row();
			
			$hp = valid_mobile($rowuser->user_mobile);
			$hp1 = valid_mobile($rowuser->agent_mobile);
			if ((! $hp) && (! $hp1))
			{
				continue;
			}			
			
			unset($params);
			
			if ($rowuser->user_agent == $this->config->item("GPSANDALASID"))
			{
				$ttd = "www.gpsandalas.com";
			}
			else
			{
				$ttd = "www.lacak-mobil.com";
			}
		
			if ($rowgeo->geofence_name)
			{
				$geofencename = $rowgeo->geofence_name;
			}
			else
			{
				$geofencename = "batas area yang Anda tandai.";
			}
	
			if ($rowgeo->smsgeofence_status == 1)
			{
				$params['content'] = sprintf($this->config->item('SMS_ALERT_GEOFENCE_OUT'), date("d/m/Y H:i", $t), $rowuser->vehicle_no, $geofencename, $rowuser->user_login, $rowuser->vehicle_no, $ttd);
			}
			else 
			{
				$params['content'] = sprintf($this->config->item('SMS_ALERT_GEOFENCE_IN'), date("d/m/Y H:i", $t), $rowuser->vehicle_no, $geofencename, $rowuser->user_login, $rowuser->vehicle_no, $ttd);
			}			
			$params['dest'] = array_unique(array_filter(array($hp, $hp1)));
			$xml = $this->load->view("sms/send", $params, true);

			$this->smsmodel->sendsms($xml);						
			echo $xml."\r\n";			
		}		
	}
		
	function maxspeedalert()
	{
		$tbls = array("smsmaxspeed_indogps", "smsmaxspeed_t1", "smsmaxspeed_t1_1", "smsmaxspeed_t1_pln", "smsmaxspeed_t3", "smsmaxspeed_t4", "smsmaxspeed_t4_farrasindo", "smsmaxspeed_t4_new", "smsmaxspeed_t1_2", "smsmaxspeed_t5", "smsmaxspeed_t5_pulse");
		
		echo "[".date("Y-m-d H:i:s")."] START MAX SPEED ALERT\r\n";
		
		$uniqid = uniqid();
		
		for($j=0; $j < count($tbls); $j++)
		{
			echo "[".date("Y-m-d H:i:s")."] PROCESSING ".$tbls[$j]."... \r\n";
			
			$this->db->select($tbls[$j].".*, vehicle.*, t_owner.*, t_agent.user_mobile agent_mobile");
			$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);
			$this->db->where("(smsmaxspeed_status = 1) or (smsmaxspeed_alert = '0000-00-00 00:00:00')", null);
			$this->db->join("vehicle", "vehicle_device = smsmaxspeed_vehicle");
			$this->db->join("user t_owner", "t_owner.user_id = vehicle_user_id");
			$this->db->join("user t_agent", "t_owner.user_agent = t_agent.user_agent AND t_agent.user_type = 3", "left outer");
			
			$q = $this->db->get($tbls[$j]);
			
			if ($q->num_rows() == 0)
			{
				continue;
			}
			
			$rows = $q->result();
			for($i=0; $i < count($rows); $i++)
			{
				$hp = valid_mobile($rows[$i]->user_mobile);
				$hp1 = valid_mobile($rows[$i]->agent_mobile);
				if ((! $hp) && (! $hp1)) continue;								
				
				$t = dbmaketime($rows[$i]->smsmaxspeed_created);
				
				unset($params);
				
				$params['content'] = sprintf($this->config->item('SMS_ALERT_MAX_SPEED'), date("d/m/Y H:i", $t), $rows[$i]->vehicle_no, number_format($rows[$i]->smsmaxspeed_speed, 0, "", ""), number_format($rows[$i]->smsmaxspeed_max, 0, "", ""));
				$params['dest'] = array_unique(array_filter(array($hp, $hp1)));		
				$xml = $this->load->view("sms/send", $params, true);
				
				$this->smsmodel->sendsms($xml);						
				echo $xml."\r\n";
				
				unset($update);
				$update['smsmaxspeed_alert'] = date("Y-m-d H:i:s");
				$update['smsmaxspeed_status'] = 2;
				
				$this->db->where("smsmaxspeed_id", $rows[$i]->smsmaxspeed_id);
				$this->db->update($tbls[$j], $update);
								
				sleep(10);
			}
		}
	}

	function clean()
	{
		$this->db->where("session_user >", 0);
		$this->db->delete("session");	

		$this->db->query("OPTIMIZE TABLE ".$this->db->dbprefix."session");

		$this->db->where("smsannouncement_send <", date('Y-m-d 00:00:01'), null);
		$this->db->delete("smsannouncement");		
		
		$this->db->query("OPTIMIZE TABLE ".$this->db->dbprefix."smsannouncement");
	}

	function parkinghist($tablepark)
	{
		$sql = "INSERT INTO ".$this->db->dbprefix."smsparking_hist (smsparking_vehicle, smsparking_begin, smsparking_end, smsparking_status, smsparking_alert, smsparking_alerted, smsparking_setting) SELECT smsparking_vehicle, smsparking_begin, smsparking_end, smsparking_status, smsparking_alert, smsparking_alerted, smsparking_setting FROM ".$this->db->dbprefix.$tablepark;
		printf("execute %s\r\n", $sql);
		$this->db->query($sql);

		$this->db->where("smsparking_int >", 0);
		$this->db->delete($tablepark);
			
		$sql = "OPTIMIZE TABLE ".$this->db->dbprefix.$tablepark;
		$this->db->query($sql);		
		printf("execute %s\r\n", $sql);
	}
	
	function maxspeedhist($tablespeed)
	{
		$sql = "INSERT INTO ".$this->db->dbprefix."smsmaxspeed_hist (smsmaxspeed_vehicle, smsmaxspeed_speed, smsmaxspeed_max, smsmaxspeed_status, smsmaxspeed_alert, smsmaxspeed_normal, smsmaxspeed_created) SELECT smsmaxspeed_vehicle, smsmaxspeed_speed, smsmaxspeed_max, smsmaxspeed_status, smsmaxspeed_alert, smsmaxspeed_normal, smsmaxspeed_created FROM ".$this->db->dbprefix.$tablespeed;
		printf("execute %s\r\n", $sql);
		$this->db->query($sql);
		

		$this->db->where("smsmaxspeed_id >", 0);
		$this->db->delete($tablespeed);
		
		$sql = "OPTIMIZE TABLE ".$this->db->dbprefix.$tablespeed;
		printf("execute %s\r\n", $sql);
		$this->db->query($sql);		
	}

	function movetemptohist()
	{
		$sql = "INSERT INTO webtracking_gps_hist
				(
						gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
					,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real
					,	gps_odometer, gps_workhour
				)						
				SELECT 		gps_name, gps_host, gps_type, gps_utc_coord, gps_status, gps_latitude, gps_ns, gps_longitude, gps_ew
						,	gps_speed, gps_course, gps_utc_date, gps_mvd, gps_mv, gps_cs, gps_msg_ori, gps_time, gps_latitude_real, gps_longitude_real
						,	gps_odometer, gps_workhour			
				FROM 	webtracking_gps_temp
		";
		
		$this->db->query($sql);
		
		$sql = "DELETE FROM webtracking_gps_temp";
		$this->db->query($sql);
		
		$sql = "OPTIMIZE TABLE webtracking_gps_temp";
		$this->db->query($sql);
	}
	
	function historical()
	{	
		$tblhists = $this->config->item("table_hist");
		$tblinfos = $this->config->item("table_hist_info");
		$vehicletypes = array_keys($this->config->item('vehicle_type'));
		$allservicenames = $this->config->item("service");
		$maxspeeds = $this->config->item("maxspeed");
		$parkings = $this->config->item("parking");
		
		foreach($vehicletypes as $vehicletype)
		{
			$tablegps = $this->gpsmodel->getGPSTable($vehicletype);
			$tableinfo = $this->gpsmodel->getGPSInfoTable($vehicletype);
			
			if (! isset($tblhists[strtoupper($vehicletype)])) continue;
			if (! isset($tblinfos[strtoupper($vehicletype)])) continue;
			if (! isset($maxspeeds[$tablegps])) continue;
			if (! isset($parkings[$tablegps])) continue;
			if (! isset($allservicenames[$tablegps])) continue;
			
			$tablegpshist = $tblhists[strtoupper($vehicletype)];
			$tableinfohist = $tblinfos[strtoupper($vehicletype)];
			
			$tablespeed = $maxspeeds[$tablegps];
			$tableparking = $parkings[$tablegps];
			$servicenames = $allservicenames[$tablegps];
			
			foreach($servicenames as $servicename)
			{				
				$this->servicekill($servicename);
			}
						
			$this->movetohist($tablegps, $tablegpshist, $vehicletype);
			$this->lastgps($vehicletype, $tablegps, $tablegpshist);
			$this->histinfo($tableinfo, $tableinfohist, $vehicletype);
			$this->maxspeedhist($tablespeed);
			$this->parkinghist($tableparking);

			foreach($servicenames as $servicename)
			{
				$this->servicestart($servicename);
			}

		}
	}
	
	function setrunhist($status)
	{
		unset($update);
		$update['config_value'] = $status;
		
		$this->db->where("config_name", "runhist");
		$this->db->update("config", $update);
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
		mail("jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com", "dump", $message);
		
	}
	
	function sendmail()
	{
		$subject = isset($_POST['subject']) ? $_POST['subject'] : "";
		$message = isset($_POST['body']) ? $_POST['body'] : "";
		$dest = isset($_POST['to']) ? $_POST['to'] : "";
		$bcc = isset($_POST['bcc']) ? $_POST['bcc'] : "";
		$sender = isset($_POST['sender']) ? $_POST['sender'] : "";
		$sendername = isset($_POST['sendername']) ? $_POST['sendername'] : "";
		
		//$config['protocol'] = 'sendmail';
		$config['charset'] = 'iso-8859-1';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = "html";
		
		$this->email->initialize($config);
		
		$this->email->from($sender, $sendername);
		$this->email->reply_to($sender, $sendername);
		$this->email->to($dest);
		if ($bcc)
		{
			$this->email->bcc($bcc);
		}
		
		$this->email->subject($subject);
		$this->email->message($message);
		$this->email->send();
	}
	
	function delcache()
	{
		$params['did'] = "yes";
		curl_post_async("http://tracker.gpsandalas.com/tools/delcache", $params);
		curl_post_async("http://www.lacak-mobil.com/tools/delcache", $params);
	}
	
	function removeoldvehicle($month=6)
	{
		printf("removing vehicle with expire date > %d month(s)\r\n", $month);
		
		$sixmonthbefore = mktime(0, 0, 0, date('n')-$month, date('j'), date('Y'));
		
		unset($update);
		$update['vehicle_status'] = 2;
		
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 <", date("Ymd", $sixmonthbefore));
		$this->db->update("vehicle", $update);		
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
