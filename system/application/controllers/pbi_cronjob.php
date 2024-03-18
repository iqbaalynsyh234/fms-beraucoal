<?php
include "base.php";

class Pbi_cronjob extends Base {

	function Pbi_cronjob()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
		$this->load->model("smsmodel");
		$this->load->model("historymodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
		$this->load->helper('email');
		$this->load->helper('common');
		$this->load->library('email');
	}
	
	//for all
    function getPosition($longitude, $ew, $latitude, $ns){
        $gps_longitude_real = getLongitude($longitude, $ew);
        $gps_latitude_real = getLatitude($latitude, $ns);
        
        $gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
        $gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");           
        $georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
        
        return $georeverse;
    }
	
	//for powerblock
    function getGeofence_location_powerblock($longitude, $ew, $latitude, $ns, $vehicle_device) 
    {
        
        $this->db = $this->load->database("default", true);
        $lng = getLongitude($longitude, $ew);
        $lat = getLatitude($latitude, $ns);
                                                                           
        $sql = sprintf("
                    SELECT     *
                    FROM     %sgeofence 
                    WHERE     TRUE
                            AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
                            AND (geofence_user = 1147 )
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
	
	function getPosition_other($longitude, $latitude)
	{
		$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);	
		return $georeverse;
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
	
	function geofencealert_pbi()
	{ 
		$this->db->where("config_name", "geofencealertprocessing_powerblock");
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
					
					$this->db->where("config_name", "geofencealertprocessing_powerblock");
					$this->db->update("config", $update);	

					print("masih proses\r\n");
					return;				
				}
				
				print("masih proses\r\n");		
				return;						
			}
			
			unset($update);
			
			$update['config_value'] = 1;
			
			$this->db->where("config_name", "geofencealertprocessing_powerblock");
			$this->db->update("config", $update);
		}
		else
		{
			unset($insert);
			
			$insert['config_name'] = "geofencealertprocessing_powerblock";
			$insert['config_value'] = 1;
			
			$this->db->insert("config", $insert);
		}

		$this->db->distinct();
		$this->db->select("user.*, vehicle.*");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_active_date2 >=",date("Ymd"));
		$this->db->where("user_id",1147); //Croscheck Powerblock
		$this->db->join("vehicle", "vehicle_device = geofence_vehicle");
		$this->db->join("user", "user_id = vehicle_user_id");
		$this->db->where("geofence_status", 1);
		$q = $this->db->get("geofence");
		
		if ($q->num_rows() == 0)
		{
			echo "tidak ada user yang setting geofence\r\n";
			$this->geofencealert_release_pbi();
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

			$this->dogeofencealert_pbi($row);
			$i++;
		}


		$this->geofencealert_release_pbi();
	}
	
	function dogeofencealert_pbi($vehicle)
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
							AND (geofence_user = '1147' )
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
				$this->addgeofencealert_pbi($vehicle, 2, $gps, $rowgeo, $t);
			}
			else
			{
				$t = $rowgps[count($rowgps)-1]->gps_time;
				$this->addgeofencealert_pbi($vehicle, 2, $gps, FALSE, $t);
			}
			
			return;
		}
		$t = $rowgps[count($rowgps)-1]->gps_time;
		$this->addgeofencealert_pbi($vehicle, 1, $gps, $rowgeo, $t);
	}	
	
	function addgeofencealert_pbi($vehicle, $direction, $gps, $geofence, $t)
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
		
		
	}
	
	function geofencealert_release_pbi()
	{
		$update['config_value'] = 0;
		
		$this->db->where("config_name", "geofencealertprocessing_powerblock");
		$this->db->update("config", $update);		
	}
	
	//for powerblock
	function data_operasional_pbi($startdate = "", $enddate = "", $name="", $host="")
    {
		ini_set('memory_limit', '3G');
        printf("PROSES AUTO REPORT DATA OPERASIONAL KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		//$name = "";
		//$host = "";
		$userid = "1147";
		
        $report_type = "operasional";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "operasional_";
        
        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        $z =0;
		
		$this->db->order_by("vehicle_id", "asc");
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
		$this->db->where("vehicle_user_id", 1147);//powerblock
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_not_in("vehicle_type", $this->config->item('vehicle_others'));
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, ++$z, $total_process);
			$company_username = $rowvehicle[$x]->user_company;
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
						$this->dbhist2 = $this->load->database("gpshistory",true);		
						
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);	
					}
					
					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
					$vehicle_type = $rowvehicle[$x]->vehicle_type;
						
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
					
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_powerblock");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {
						
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)"); $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
						
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
					if($trows < 10000){
						
						////-------------KONDISI ON-------------////
						if ($trows > 0){
						for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i-1]->gps_latitude_real." ".$rows[$i-1]->gps_longitude_real;
		
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
									
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle //hanya data on
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {
									
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
										
										
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data On
                        $i=1;
                        $new = "";
                        printf("WRITE DATA ON : ");
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
                               // if($mileage != 0) // edit 0 km engine ON
                               // {
                                    $duration = get_time_difference($report['start_time'], $report['end_time']);
									
									$start_1 = dbmaketime($report['start_time']);
									$end_1 = dbmaketime($report['end_time']);
									$duration_sec = $end_1 - $start_1;
									
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
									
									$geofence_start = $report['start_geofence_location'];
									$geofence_end = $report['end_geofence_location'];
									
									
									
								if(isset($report['vehicle_name'])){
										unset($datainsert);

										$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
										$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
										$datainsert["trip_mileage_vehicle_name"] = $vehicle_name;
										$datainsert["trip_mileage_vehicle_type"] = $vehicle_type;
										$datainsert["trip_mileage_trip_no"] = $notrip;
										$datainsert["trip_mileage_engine"] = $report['engine'];
										$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report['start_time']));
										$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report['end_time']));
										$datainsert["trip_mileage_duration"] = $show;
										$datainsert["trip_mileage_duration_sec"] = $duration_sec;
										$datainsert["trip_mileage_trip_mileage"] = $x_mile;
										$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
										$datainsert["trip_mileage_location_start"] = $report['start_position']->display_name;
										$datainsert["trip_mileage_location_end"] = $report['end_position']->display_name;
										$datainsert["trip_mileage_geofence_start"] = $geofence_start;
										$datainsert["trip_mileage_geofence_end"] = $geofence_end;
										$datainsert["trip_mileage_coordinate_start"] = $report['start_coordinate'];
										$datainsert["trip_mileage_coordinate_end"] = $report['end_coordinate'];
										
									//edit flag engine ON , nol km, lebih dari 1 menit = 60
									if($duration_sec > 60 ){
										$this->dbtrip = $this->load->database("powerblock_report",TRUE);
										$this->dbtrip->insert($dbtable,$datainsert);
										$this->dbtrip2 = $this->load->database("powerblock_report2",TRUE);
										$this->dbtrip2->insert($dbtable,$datainsert);
										printf("OK");
									}
									
								}

                                    $i++;
								//}
                            }
                        }
						
                        unset($data);
            
                        printf("FINISH FOR VEHICLE ON : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
						}
						
						////---------------KONDISI OFF---------------///
						if ($trows > 0){
                        for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i-1]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i-1]->gps_latitude_real." ".$rows[$i-1]->gps_longitude_real;
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle //hanya data off
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {
									//print_r(substr($rows[$i]->gps_info_io_port, 4, 1));exit();
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_powerblock($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real." ".$rows[$i]->gps_longitude_real;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data off //kondisi tidak ada data ?
                        $i=1;
                        $new = "";
                        printf("WRITE DATA OFF : ");
                        foreach($data as $vehicle_no=>$val)
                        {
                            if($new != $vehicle_no)
                            {
                                $cumm = 0;
                                $trip_no = 1;
                            }
							
                            foreach($val as $no=>$report_off)
                            {
                                $mileage = $report_off['end_mileage']- $report_off['start_mileage'];
									$duration = get_time_difference($report_off['start_time'], $report_off['end_time']);
									
									$start_1 = dbmaketime($report_off['start_time']);
									$end_1 = dbmaketime($report_off['end_time']);
									$duration_sec = $end_1 - $start_1;
									
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
									$geofence_start_off = $report_off['start_geofence_location'];
									$geofence_end_off = $report_off['end_geofence_location'];

									//edit flag engine OFF , nol km, lebih dari 1 menit
								if (isset($report_off['vehicle_name'])){
										unset($datainsert);
										$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
										$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
										$datainsert["trip_mileage_vehicle_name"] = $vehicle_name;
										$datainsert["trip_mileage_vehicle_type"] = $vehicle_type;
										$datainsert["trip_mileage_trip_no"] = $notrip;
										$datainsert["trip_mileage_engine"] = $report_off['engine'];
										$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report_off['start_time']));
										$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report_off['end_time']));
										$datainsert["trip_mileage_duration"] = $show;
										$datainsert["trip_mileage_duration_sec"] = $duration_sec;
										$datainsert["trip_mileage_trip_mileage"] = $x_mile;
										$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
										$datainsert["trip_mileage_location_start"] = $report_off['start_position']->display_name;
										$datainsert["trip_mileage_location_end"] = $report_off['end_position']->display_name;
										$datainsert["trip_mileage_geofence_start"] = $geofence_start_off;
										$datainsert["trip_mileage_geofence_end"] = $geofence_end_off;
										$datainsert["trip_mileage_coordinate_start"] = $report_off['start_coordinate'];
										$datainsert["trip_mileage_coordinate_end"] = $report_off['end_coordinate'];
										
									//edit flag engine OFF , nol km, lebih dari 1 menit = 60
									if($duration_sec > 60 ){
										$this->dbtrip = $this->load->database("powerblock_report",TRUE);
										$this->dbtrip->insert($dbtable,$datainsert);
										$this->dbtrip2 = $this->load->database("powerblock_report2",TRUE);
										$this->dbtrip2->insert($dbtable,$datainsert);
										printf("OK");
									}
								}
                                    $i++;
                             
                            }
                        }
                        printf("FINISH FOR VEHICLE OFF : %s \r\n",$rowvehicle[$x]->vehicle_device);
						printf("============================================ \r\n");
						}
						
					}else{
						printf("TO MANY : %s \r\n", $trows);
						printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
					}
						printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_powerblock",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", $trows);
						}
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
                       
                        printf("============================================ \r\n");

                    }
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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA OPERASIONAL DONE %s\r\n",$finish_time);
		
		//Send Email
		$cron_name = "POWERBLOCK - OPERASIONAL REPORT";
		
		$this->dbtrip = $this->load->database("powerblock_report",TRUE);
        $this->dbtrip->select("trip_mileage_id");
        $this->dbtrip->where("trip_mileage_start_time >=",$startdate);
        $this->dbtrip->where("trip_mileage_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
		//get telegram group by company
		$this->db = $this->load->database("default",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$startdate." to ".$enddate." \n".
					"Total Data: ".$total_data." \n".
					"Start: ".$startproses." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Cron Name : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_data."
End Data   : "."( ".$z." / ".$total_process." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.lacakmobil@yahoo.com";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		//$this->dbtrip2->close();
		//$this->dbtrip2->cache_delete_all();
		
		$this->operational_other(1147, $startdate, $enddate);
			
		printf("Send Email OK");
        
    }
	
	function operational_other($userid=1147, $startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO REPORT DATA OPERASIONAL KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "operasional";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        //$domain_server = "http://202.129.190.194/";
		$report = "operasional_";
        
        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}
        
		$sdate = date("Y-m-d H:i:s", strtotime($startdate));
        $edate = date("Y-m-d H:i:s", strtotime($enddate));
        $z =0;
		
		$this->db->order_by("vehicle_id", "asc");
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
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_in("vehicle_type", $this->config->item('vehicle_others'));
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
       
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, $rowvehicle[$x]->user_name, ++$z, $total_process);
			$cron_username = strtoupper($rowvehicle[$x]->user_name);
			$company_username = strtoupper($rowvehicle[$x]->user_company);
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
						$this->dbhist2 = $this->load->database("gpshistory",true);		
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);		
					}
					
					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
					$vehicle_type = $rowvehicle[$x]->vehicle_type;
						
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
					
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_user_id",$rowvehicle[$x]->vehicle_user_id);
					$this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
					$this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_powerblock");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {

                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
						$this->dbhist->limit(3000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
		
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
						$this->dbhist2->limit(3000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
                
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
					if($trows < 10000){
						
						////-------------KONDISI ON-------------////
						if ($trows > 0){
						for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_latitude);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude;
									
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle //hanya data on
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {
									
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude;
										
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude,$rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id );
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data On
                        $i=1;
                        $new = "";
                        printf("WRITE DATA ON : ");
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
                               // if($mileage != 0) // edit 0 km engine ON
                               // {
                                    $duration = get_time_difference($report['start_time'], $report['end_time']);
									
									$start_1 = dbmaketime($report['start_time']);
									$end_1 = dbmaketime($report['end_time']);
									$duration_sec = $end_1 - $start_1;
									
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
									
									$geofence_start = $report['start_geofence_location'];
									$geofence_end = $report['end_geofence_location'];
									
									
									
								if(isset($report['vehicle_name'])){
										unset($datainsert);

										$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
										$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
										$datainsert["trip_mileage_vehicle_name"] = $vehicle_name;
										$datainsert["trip_mileage_vehicle_type"] = $vehicle_type;
										$datainsert["trip_mileage_trip_no"] = $notrip;
										$datainsert["trip_mileage_engine"] = $report['engine'];
										$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report['start_time']));
										$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report['end_time']));
										$datainsert["trip_mileage_duration"] = $show;
										$datainsert["trip_mileage_duration_sec"] = $duration_sec;
										$datainsert["trip_mileage_trip_mileage"] = $x_mile;
										$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
										$datainsert["trip_mileage_location_start"] = $report['start_position']->display_name;
										$datainsert["trip_mileage_location_end"] = $report['end_position']->display_name;
										$datainsert["trip_mileage_geofence_start"] = $geofence_start;
										$datainsert["trip_mileage_geofence_end"] = $geofence_end;
										$datainsert["trip_mileage_coordinate_start"] = $report['start_coordinate'];
										$datainsert["trip_mileage_coordinate_end"] = $report['end_coordinate'];
										//$datainsert["trip_mileage_door_start"] = $report['start_door'];
										//$datainsert["trip_mileage_door_end"] = $report['end_door'];
										//$datainsert["trip_mileage_lat"] = $report['start_latitude'];
										//$datainsert["trip_mileage_lng"] = $report['start_longitude'];
										//$datainsert["trip_mileage_totaldata"] = $trows;
										
									//edit flag engine ON , nol km, lebih dari 5 menit = 300  // 3 menit
									if($duration_sec > 180 ){
										$this->dbtrip = $this->load->database("powerblock_report",TRUE);
										$this->dbtrip->insert($dbtable,$datainsert);
										
										printf("OK");
									}
									
								}

                                    $i++;
								//}
                            }
                        }
						
                        unset($data);
            
                        printf("FINISH FOR VEHICLE ON : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
						}
						
						////---------------KONDISI OFF---------------///
						if ($trows > 0){
                        for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_latitude);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
									
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude;
									$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude;
                                    
									$on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle //hanya data off
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 0)
                                {
									//print_r(substr($rows[$i]->gps_info_io_port, 4, 1));exit();
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_longitude'] = $rows[$i]->gps_longitude;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }                                    
                                }
								//edit off
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s",strtotime($rows[$i]->gps_time));$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude,$rows[$i]->gps_latitude);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle[$x]->vehicle_user_id);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude." ".$rows[$i]->gps_longitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_door'] = substr($rows[$i]->gps_msg_ori, 79, 1);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_latitude'] = $rows[$i]->gps_latitude;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_longitude'] = $rows[$i]->gps_longitude;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data off //kondisi tidak ada data ?
                        $i=1;
                        $new = "";
                        printf("WRITE DATA OFF : ");
                        foreach($data as $vehicle_no=>$val)
                        {
                            if($new != $vehicle_no)
                            {
                                $cumm = 0;
                                $trip_no = 1;
                            }
							
                            foreach($val as $no=>$report_off)
                            {
                                $mileage = $report_off['end_mileage']- $report_off['start_mileage'];
                               // if($mileage != 0) // edit 0 km engine off
                               // {
                                    $duration = get_time_difference($report_off['start_time'], $report_off['end_time']);
									
									$start_1 = dbmaketime($report_off['start_time']);
									$end_1 = dbmaketime($report_off['end_time']);
									$duration_sec = $end_1 - $start_1;
									
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
									$geofence_start_off = $report_off['start_geofence_location'];
									$geofence_end_off = $report_off['end_geofence_location'];

									//edit flag engine OFF , nol km, lebih dari 10 menit
								if (isset($report_off['vehicle_name'])){
										unset($datainsert);
										$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
										$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
										$datainsert["trip_mileage_vehicle_name"] = $vehicle_name;
										$datainsert["trip_mileage_vehicle_type"] = $vehicle_type;
										$datainsert["trip_mileage_trip_no"] = $notrip;
										$datainsert["trip_mileage_engine"] = $report_off['engine'];
										$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report_off['start_time']));
										$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report_off['end_time']));
										$datainsert["trip_mileage_duration"] = $show;
										$datainsert["trip_mileage_duration_sec"] = $duration_sec;
										$datainsert["trip_mileage_trip_mileage"] = $x_mile;
										$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
										$datainsert["trip_mileage_location_start"] = $report_off['start_position']->display_name;
										$datainsert["trip_mileage_location_end"] = $report_off['end_position']->display_name;
										$datainsert["trip_mileage_geofence_start"] = $geofence_start_off;
										$datainsert["trip_mileage_geofence_end"] = $geofence_end_off;
										$datainsert["trip_mileage_coordinate_start"] = $report_off['start_coordinate'];
										$datainsert["trip_mileage_coordinate_end"] = $report_off['end_coordinate'];
										//$datainsert["trip_mileage_door_start"] = $report_off['start_door'];
										//$datainsert["trip_mileage_door_end"] = $report_off['end_door'];
										//$datainsert["trip_mileage_lat"] = $report['start_latitude'];
										//$datainsert["trip_mileage_lng"] = $report['start_longitude'];
										//$datainsert["trip_mileage_totaldata"] = $trows;
										
									//edit flag engine OFF , nol km, lebih dari 4 menit = 240
									if($duration_sec > 180 ){
										$this->dbtrip = $this->load->database("powerblock_report",TRUE);
										$this->dbtrip->insert($dbtable,$datainsert);
										printf("OK");
									}
								}
                                    $i++;
                             // }
                            }
                        }
                        printf("FINISH FOR VEHICLE OFF : %s \r\n",$rowvehicle[$x]->vehicle_device);
						printf("============================================ \r\n");
						}
						
					}else{
						printf("TO MANY : %s \r\n", $trows);
						printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
					}
						printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_new",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", $trows);
						}
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
                       
                        printf("============================================ \r\n");

                    }
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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT DATA OPERASIONAL OTHER DONE %s\r\n",$finish_time);
	
	if($total_process != 0){
		//Send Email
		$cron_name = $cron_username." - "."OPERATIONAL REPORT OTHER";
		$this->dbtrip = $this->load->database("operational_report",TRUE);
        $this->dbtrip->select("trip_mileage_id");
        $this->dbtrip->where("trip_mileage_start_time >=",$startdate);
        $this->dbtrip->where("trip_mileage_end_time <=",$enddate);
        $qtrip = $this->dbtrip->get($dbtable);
        $rtrip = $qtrip->result();
		$total_data = count($rtrip);
		
		//get telegram group by company
		$this->db = $this->load->database("default",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$startdate." to ".$enddate." \n".
					"Total Data: ".$total_data." \n".
					"Start: ".$startproses." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
		unset($mail);
		$mail['subject'] =  $cron_name.": ".$startdate." to ".$enddate;
		$mail['message'] = 
"
Cron Report Status :

Cron Name : ".$cron_name." : ".$startdate." to ".$enddate."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Total Data : ".$total_data."
End Data   : "."( ".$z." / ".$total_process." )"."
Status     : Finish

Thanks

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com,robi@lacak-mobil.com";
		$mail['bcc'] = "report.lacakmobil@yahoo.com";
		$mail['sender'] = "cron@lacak-mobil.com";
		lacakmobilmail($mail);
		
	}
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
	
		printf("Send Email OK");
        
    }
	
	function getlist_coordinate($userid="", $startdate = "", $enddate = "")
    {
		ini_set('memory_limit', '2G');
        printf("PROSES AUTO GET KOORDINAT FROM OPERASIONAL >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "operasional";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
        $report = "operasional_";
		$configdb = "powerblock_report";
		
        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}
        
		$this->db->order_by("vehicle_device", "asc");
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
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_not_in("vehicle_type", $this->config->item('vehicle_others'));
		
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		$z = 0;
		
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_no, $dbtable, $startdate, $enddate, ++$z, $total_process);
			
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
						$this->dbhist2 = $this->load->database("gpshistory",true);		
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);		
					}
					
					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
					$vehicle_type = $rowvehicle[$x]->vehicle_type;
						
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
					
                    $this->dbreport = $this->load->database($configdb,true);
					//$this->dbreport->select("trip_mileage_id,trip_mileage_vehicle_id,trip_mileage_start_time,trip_mileage_end_time");
					$this->dbreport->order_by("trip_mileage_id","desc");
					$this->dbreport->where("trip_mileage_vehicle_id",$rowvehicle[$x]->vehicle_device);
					$this->dbreport->where("trip_mileage_engine",1); //khusus engine ON
					$this->dbreport->where("trip_mileage_trip_mileage >=","0.5"); //khusus jarak lebih dari 1KM
					$this->dbreport->where("trip_mileage_start_time >=",$startdate);
					$this->dbreport->where("trip_mileage_start_time <=",$enddate);
					$qreport = $this->dbreport->get($dbtable);
                    $rowsreport = $qreport->result();
					$totalrowsreport = count($rowsreport);
					$sdate = "";
					$edate = "";
					for($r=0;$r<count($rowsreport);$r++)
					{
							printf("ID Report on CHECKING . . .: %s to %s ", $r+1, $totalrowsreport);
							$data_array = "";
							$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($rowsreport[$r]->trip_mileage_start_time)));
							$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($rowsreport[$r]->trip_mileage_end_time)));
							
							$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
							$this->dbhist->select("gps_time,gps_latitude_real,gps_longitude_real,gps_speed,gps_info_distance");
							$this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist->where("gps_time >=", $sdate);
							$this->dbhist->where("gps_time <=", $edate);    
							$this->dbhist->order_by("gps_time","asc");
							$this->dbhist->limit(3000);
							$this->dbhist->from($table);
							$q = $this->dbhist->get();
							$rows1 = $q->result();
					  
							$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist2->select("gps_time,gps_latitude_real,gps_longitude_real,gps_speed,gps_info_distance");							
							$this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist2->where("gps_time >=", $sdate);
							$this->dbhist2->where("gps_time <=", $edate);  							
							$this->dbhist2->order_by("gps_time","asc");
							$this->dbhist2->limit(3000);
							$this->dbhist2->from($tablehist);
							$q2 = $this->dbhist2->get();
							$rows2 = $q2->result();
							
							$rows = array_merge($rows1, $rows2); //limit data rows = 10000
							$trows = count($rows);
							
							$rowsummary[] = "";
							for($i=count($rows)-1; $i >= 0; $i--)
							{
								if (($i+1) >= count($rows))
								{
									$rowsummary[] = $rows[$i];
									continue;
								}
								$latbefore = $rows[$i+1]->gps_latitude_real;
								$lngbefore = $rows[$i+1]->gps_longitude_real;
								$latcurrent = $rows[$i]->gps_latitude_real;
								$lngcurrent = $rows[$i]->gps_longitude_real;
								if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
								{
									$rowsummary[] = $rows[$i];
									continue;
								}
								if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
								{
									$rowsummary[] = $rows[$i];
									continue;
								}
							}
							
							$data_sort = $this->array_sort($rowsummary, 'gps_time', SORT_ASC);
							$data_array = json_encode($data_sort);
							
							//update to db report
							unset($data);
							$data["trip_mileage_coordinate_list"] = $data_array;
							$this->dbreport->where("trip_mileage_id",$rowsreport[$r]->trip_mileage_id);
							$this->dbreport->where("trip_mileage_vehicle_id",$rowvehicle[$x]->vehicle_device);
							$this->dbreport->update($dbtable,$data);
							
							printf("-- %s OKE \r\n ",$rowsreport[$r]->trip_mileage_id);
							
					}

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
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT GET KOORDINAT LIST DONE %s\r\n",$finish_time);
		
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
	
	function telegram_direct($groupid,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $url = "http://lacak-mobil.com/telegram/telegram_directpost";
        
        $data = array("id" => $groupid, "message" => $message);
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
	
	function array_sort($array, $on, $order=SORT_ASC){

		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}
	
		return $new_array;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
