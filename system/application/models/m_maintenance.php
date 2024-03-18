<?php
class M_maintenance extends Model {
  var $earthRadius = 6371;
	var $fromsocket;
	var $datainfo;
    function M_maintenance()
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

    function updateDatadbtransporter($tableprefix, $where, $wherenya, $datanya){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->where($where, $wherenya);
      return $this->dbtransporter->update($tableprefix, $datanya);
    }

    function insertDataDbTransporter($tableprefix, $datanya){
      $this->dbtransporter = $this->load->database("transporter", true);
      return $this->dbtransporter->insert($tableprefix, $datanya);
    }

    function cekvehiclenodbtransporter($tableprefix, $vehicle_no){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where("maintenance_conf_vehicle_no", $vehicle_no);
      return $this->dbtransporter->get($tableprefix);
    }

    function gogetservicetype($tableprefix){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where("service_type_status", "1");
      $this->dbtransporter->order_by("service_type", "asc");
      return $this->dbtransporter->get($tableprefix);
    }

    function gogetservicetype2($tableprefix){
      $this->dbtransporter = $this->load->database("transporter", true);
      $sql = "SELECT * FROM transporter_service_type where service_type_status != 0 order by service_type asc";
      // $query = $this->dbtransporter->query($sql);
      return $this->dbtransporter->query($sql);
    }

    function g_all($tableprefix, $where, $wherenya, $orderby, $orderbynya){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where($where, $wherenya);
      $this->dbtransporter->order_by($orderby, $orderbynya);
      return $this->dbtransporter->get($tableprefix)->result_array();
    }

    function getformaintenancehistory($tableprefix, $user_company, $vehicle_no, $tipeservice, $sdate, $enddate, $servicestatus){
      // echo "<pre>";
    	// var_dump($tableprefix.'-'.$user_company.'-'.$vehicle_no.'-'.$tipeservice.'-'.$sdate.'-'.$enddate.'-'.$servicestatus);die();
    	// echo "<pre>";
      $this->dbtransporter = $this->load->database("transporter", true);
  		$this->dbtransporter->join("transporter_workshop", "servicess_history.servicess_work_agencies = transporter_workshop.workshop_id");

  		if ($vehicle_no != "all"){
  			$this->dbtransporter->where("servicess_vehicle_no", $vehicle_no);
  		}

      if ($tipeservice !="all"){
        if ($tipeservice == 4) {
          if ($servicestatus != "all") {
            // echo "<pre>";
            // var_dump($servicestatus);die();
            // echo "<pre>";
            $this->dbtransporter->where("servicess_status", $servicestatus);
            $this->dbtransporter->where("servicess_estimateddate_from >=", $sdate);
            $this->dbtransporter->where("servicess_estimateddate_end <=", $enddate);
          }else {
            $this->dbtransporter->where("servicess_estimateddate_from >=", $sdate);
            $this->dbtransporter->where("servicess_estimateddate_end <=", $enddate);
          }
        }else {
          // echo "<pre>";
          // var_dump($tipeservice);die();
          // echo "<pre>";
          $this->dbtransporter->where("servicess_tipeservice", $tipeservice);
          $this->dbtransporter->where("servicess_date >=", $sdate);
          $this->dbtransporter->where("servicess_date <=", $enddate);
        }
  		}

      $this->dbtransporter->where("servicess_flag", 0);
  		$this->dbtransporter->where("servicess_user_company", $user_company);
  		$q = $this->dbtransporter->get($tableprefix);
  		return  $q->result_array();
    }

    function getstnkexpdate($tableprefix, $user_company){
      $this->dbtransporter = $this->load->database("transporter", true);
      $query = "SELECT maintenance_id, maintenance_conf_vehicle_no, maintenance_conf_vehicle_name,
                maintenance_conf_vehicle_type, maintenance_conf_stnk_no, maintenance_conf_stnkexpdate
                from transporter_maintenance_configuration
                where maintenance_conf_stnkexpdate >= DATE_ADD(DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY), INTERVAL - 12 MONTH)
                and maintenance_conf_stnkexpdate <= LAST_DAY(CURDATE() + INTERVAL 1 MONTH)
                and maintenance_conf_vehicle_user_company = '$user_company'";
  		$q = $this->dbtransporter->query($query);
  		return  $q->result_array();
    }

    function getkirexpdate($tableprefix, $user_company){
      $this->dbtransporter = $this->load->database("transporter", true);
      $query = "SELECT maintenance_id, maintenance_conf_vehicle_no, maintenance_conf_vehicle_name,
                maintenance_conf_vehicle_type, maintenance_conf_kir_no, maintenance_conf_kirexpdate
                from transporter_maintenance_configuration
                where maintenance_conf_kirexpdate >= DATE_ADD(DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY), INTERVAL - 12 MONTH)
                and maintenance_conf_kirexpdate <= LAST_DAY(CURDATE() + INTERVAL 1 MONTH)
                and maintenance_conf_vehicle_user_company = '$user_company'";
  		$q = $this->dbtransporter->query($query);
  		return  $q->result_array();
    }

    function getservicescheduleperkm($tableprefix, $user_company){
      $this->dbtransporter = $this->load->database("transporter", true);
      $query = "SELECT maintenance_id, maintenance_conf_vehicle_device, maintenance_conf_vehicle_type_gps, maintenance_conf_vehicle_no, maintenance_conf_vehicle_name,
                maintenance_conf_vehicle_type, maintenance_conf_kir_no, maintenance_conf_kirexpdate,
                maintenance_conf_servicedby, maintenance_conf_valueservicedby, maintenance_conf_lastodometer,
                maintenance_conf_last_service, maintenance_conf_alertlimit
                from transporter_maintenance_configuration
                -- where maintenance_conf_kirexpdate >= DATE_ADD(DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY), INTERVAL - 3 MONTH)
                -- and maintenance_conf_kirexpdate <= LAST_DAY(CURDATE() + INTERVAL 1 MONTH)
                where maintenance_conf_vehicle_user_company = '$user_company' and maintenance_conf_servicedby = 'perkm'";
      $q = $this->dbtransporter->query($query);
      return  $q->result_array();
    }

    function getserviceschedulepermonth($tableprefix, $user_company){
      $this->dbtransporter = $this->load->database("transporter", true);

        $query = "SELECT maintenance_id,  maintenance_conf_vehicle_no, maintenance_conf_vehicle_name,
                  maintenance_conf_servicedby, maintenance_conf_valueservicedby,
                  maintenance_conf_last_service, maintenance_conf_alertlimit
                  from transporter_maintenance_configuration
                  where maintenance_conf_last_service >= DATE_ADD(DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY), INTERVAL - 3 MONTH)
                  and maintenance_conf_last_service <= LAST_DAY(CURDATE() + INTERVAL maintenance_conf_alertlimit MONTH)
                  and maintenance_conf_vehicle_user_company = '$user_company'
                  and maintenance_conf_servicedby = 'permonth'";
        $q = $this->dbtransporter->query($query);
        return  $q->result_array();
    }

    function getodobyvehicledevice($table, $vehicle_device){
      $this->db = $this->load->database("default", true);
      $query    = "SELECT vehicle_odometer FROM $table where vehicle_device = '$vehicle_device' limit 1";
  		$q        = $this->db->query($query);
	    return $q->result_array();
    }

    function GetLastInfo($name, $host, $georeverse=true, $row=true, $lasttime=0, $type="")
  	{
      // 54684100330136-LT03-1--0-LT03
      // 002100001280-T5-1--0-T5SILVER
      // echo "<pre>";
  		// var_dump($name.'-'.$host.'-'.$georeverse.'-'.$row.'-'.$lasttime.'-'.$type);die();
  		// echo "<pre>";
  		$this->db = $this->load->database("default", TRUE);
      // print_r($name."@".$host."-".$row);exit();
  		if ($row === false)
  		{
        // print_r("1");exit();
  			$this->db->where("vehicle_device", $name."@".$host);
  		}
  		else
  		{
        // print_r("2");exit();
  			$this->db->where("vehicle_device", $row->gps_name."@".$row->gps_host);
  		}

  		$q = $this->db->get("vehicle");

  		if ($q->num_rows() == 0)
  		{
  			return;
  		}

  		$rowvehicle = $q->row();
      // print_r($rowvehicle);exit();
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


  			if($rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "TK303" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK309PTO" || $rowvehicle->vehicle_type == "GT06PTO" || $rowvehicle->vehicle_type == "TK315")
  			{

  				//goblin, saintseiya, galactus
  				if($rowvehicle->vehicle_type == "TK315")
  				{
  					$this->dbtraccar = $this->load->database("GPS_TRACCAR_TK315", TRUE);
  				}
  				else
  				{
  					$this->dbtraccar = $this->load->database("GPS_TRACCAR", TRUE);
  				}
  				$this->dbtraccar->where("uniqueid",$name);
  				$this->dbtraccar->limit(1);
  				$q = $this->dbtraccar->get("devices");
  				if ($q->num_rows() > 0)
  				{
  					$vtraccar = $q->row();
  					//print_r($vtraccar);exit;
  					$this->dbtraccar->order_by("devicetime", "desc");
  					$this->dbtraccar->where("latitude !=",0);
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
  							$this->datainfo->gps_info_distance = ($attributes['totalDistance'])/1000;
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

  			if($rowvehicle->vehicle_type == "TK315DOOR" || $rowvehicle->vehicle_type == "TK315N" || $rowvehicle->vehicle_type == "TK309N")
  			{
  				$ex_device = explode("@",$rowvehicle->vehicle_device);
  				$device_imei = $ex_device[0];
  				$device_host = $ex_device[1];

  				if(isset($rowvehicle->vehicle_dbname_live) && $rowvehicle->vehicle_dbname_live != "0")
  					{
  						$this->db = $this->load->database($rowvehicle->vehicle_dbname_live, TRUE);

  						//get table live
  						$this->db->order_by("id","desc");
  						$this->db->where("imei",$device_imei);
  						$this->db->limit(1);
  						$q = $this->db->get("gprmc");
  						if ($q->num_rows() > 0)
  						{
  								$dlive = $q->row();
  								$idlive = $dlive->id;
  								$row = new stdclass();
  								$row->gps_name           = $dlive->imei;
  								$row->gps_host           = $device_host;
  								$row->gps_utc_coord      = date("His",strtotime($dlive->date));
  								$row->gps_status         = $dlive->satelliteFixStatus;
  								$row->gps_latitude       = $dlive->latitudeDecimalDegrees;
  								$row->gps_ns             = $dlive->latitudeHemisphere;
  								$row->gps_longitude      = $dlive->longitudeDecimalDegrees;
  								$row->gps_ew             = $dlive->longitudeHemisphere;
  								$row->gps_speed          = $dlive->speed/1.852;
  								$row->gps_course         = 0; //course belum
  								$row->gps_utc_date       = date("dmy",strtotime($dlive->date));
  								$row->gps_time           = date("Y-m-d H:i:s",strtotime($dlive->date));
  								$row->gps_latitude_real  = $dlive->latitudeDecimalDegrees;
  								$row->gps_longitude_real = $dlive->longitudeDecimalDegrees;
  								$row->gps_msg_ori        = $dlive->msg_ori;
  								if($row->gps_msg_ori != ""){
  									$json = json_decode($row->gps_msg_ori,true);
  									if($json[3] == "13"){

  										$this->db->order_by("id","desc");
  										$this->db->select("id,gpsSignalIndicator,speed");
  										$this->db->where("gpsSignalIndicator","L");
  										$this->db->where("imei",$dlive->imei);
  										$this->db->limit(1);
  										$q_bf = $this->db->get("gprmc");
  										if ($q_bf->num_rows() > 0)
  										{
  											$dlive_bf = $q_bf->row();
  											$row->gps_speed = $dlive_bf->speed/1.852;
  										}
  										else
  										{
  											$row->gps_speed = $dlive->speed/1.852;
  										}
  									}

  								}

  								$this->datainfo = new stdclass();
  								$this->datainfo->gps_info_device = $dlive->imei."@".$device_host;
  								$this->datainfo->gps_info_utc_coord = date("His",strtotime($dlive->date));
  								$this->datainfo->gps_info_utc_date = date("dmy",strtotime($dlive->date));
  								$this->datainfo->gps_info_time = date("Y-m-d H:i:s",strtotime($dlive->date));
  								//if ligado S = Engine ON else OFF
  								$ligado = $dlive->ligado;
  								if($ligado == "S") { $this->datainfo->gps_info_io_port = "0000100000"; } else { $this->datainfo->gps_info_io_port = "0000000000"; }

  								//cek from bem (device) for hodometro
  								$this->db->select("imei,hodometro");
  								$this->db->where("imei",$device_imei);
  								$this->db->limit(1);
  								$q = $this->db->get("bem");
  								if ($q->num_rows() > 0)
  								{
  									$ddevice = $q->row();
  									$hodometro = $ddevice->hodometro;
  								}else{
  									$hodometro = 0;
  								}
  								$this->datainfo->gps_info_distance = $hodometro;

  								$this->fromsocket = true;
  						}
  						//cari dari port
  						else
  						{
  							$tables = $this->m_maintenance->getTable($rowvehicle); //print_r($tables);exit();
  							$this->db = $this->load->database($tables["dbname"], TRUE);
  							$this->db->limit(1);
                $this->db->select("*");
  							$this->db->where("gps_info_device", $name."@".$host);
                $this->db->order_by("gps_info_time", "desc");
  							$q = $this->db->get($tables['info']);

  							if ($q->num_rows() == 0)
  							{	//cari dari history
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

  						}
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
          $this->db->select("*");
          $this->db->where("gps_info_device", $name."@".$host);
          $this->db->order_by("gps_info_time", "desc");
          $q = $this->db->get($tables['info']);
  				if ($q->num_rows() == 0)
  				{
  					$this->db = $this->load->database($tables["dbname"], TRUE);
  					$this->db->limit(1);
            $this->db->select("*");
            $this->db->where("gps_info_device", $name."@".$host);
            $this->db->order_by("gps_info_time", "desc");
            $q = $this->db->get($tables['info']);

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
          $this->db->select("*");
          $this->db->where("gps_info_device", $name."@".$host);
          $this->db->order_by("gps_info_time", "desc");
          $q = $this->db->get($tables['info']);
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
  				// $this->db->order_by("gps_time", "desc");
  				// $this->db->where("gps_name", $name);
  				// $this->db->where("gps_host", $host);
  				// $this->db->where("gps_latitude <>", 0);
  				// $this->db->where("gps_longitude <>", 0);
  				// $q = $this->db->get($tablehist);
          $this->db->select("gps_info_device, gps_info_distance");
          $this->db->where("gps_info_device", $name."@".$host);
          $this->db->order_by("gps_info_time", "desc");
          $q = $this->db->get($tables['info']);

  				if ($q->num_rows() == 0) return;
  			}
      }

  			$row = $q->result_array();
        // print_r($row);exit();
        return $row;

  		// 	$q->free_result();
      //
  		// 	$tnow = dbmaketime($row->gps_info_time)+7*3600;
      //
  		// 	if (! isset($tableerr))
  		// 	{
  		// 		$tableerr = $this->getGPSTableError($type);
  		// 	}
      // //
  		// 	if (false)
  		// 	//if ($tableerr && ($tnow < mktime()))
  		// 	{
  		// 		$sql = "
  		// 				SELECT *
  		// 				FROM
  		// 				(
  		// 					SELECT 	*
  		// 					FROM  	`".$this->db->dbprefix.$tableerr."`
  		// 					WHERE  	1
  		// 							AND (`gps_name` =  '".$name."')
  		// 							AND (`gps_host` =  '".$host."')
  		// 					".($lasttime ? ("AND (gps_time > '".date("Y-m-d H:i:s", $lasttime)."')") : '')."
  		// 					) t1
  		// 				WHERE 	1
  		// 				ORDER BY 	gps_time DESC
  		// 				LIMIT 1 OFFSET 0
  		// 		";
      //
  		// 		$q = $this->db->query($sql);
      //
  		// 		if ($q->num_rows())
  		// 		{
  		// 			$rowerr = $q->row();
      //
  		// 			$t = dbmaketime($row->gps_info_time);
  		// 			$terr = dbmaketime($rowerr->gps_info_time);
      //
  		// 			if (($terr < mktime()) && ($terr > $t))
  		// 			{
  		// 				$row->gps_info_time = $rowerr->gps_info_time;
  		// 				$row->gps_info_utc_coord = $rowerr->gps_info_utc_coord;
  		// 				$row->gps_info_utc_date = $rowerr->gps_info_utc_date;
  		// 				//$row->gps_latitude = $rowerr->gps_latitude;
  		// 				//$row->gps_longitude = $rowerr->gps_longitude;
  		// 			}
  		// 		}
  		// 	}
      //
      // //
  		// 	$tv = dbmaketime($row->gps_info_time);
  		// 	$tv += 7*3600;
      // //
  		// 	$tvj = mktime(date("G", $tv), 0, 0, date("n", $tv), date('j', $tv), date('Y', $tv));
  		// 	$nowj = mktime(date('G'), 0, 0, date("n"), date("j"), date("Y"));
      // //
  		// 	if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type !="TK315" && $rowvehicle->vehicle_type !="TK315DOOR" && $rowvehicle->vehicle_type !="TK315N" && $rowvehicle->vehicle_type !="TK309N")
  		// 	{
  		// 	if (($row->gps_latitude*1 == 0) || ($row->gps_longitude*1 == 0) || ($tvj > $nowj))
  		// 	{
      // //
  		// 		//Case Dokar B1477BZN
  		// 		$this->db = $this->load->database($tables["dbname"], TRUE);
      //
  		// 		if ($lasttime)
  		// 		{
  		// 			$this->db->where("gps_time >", date("Y-m-d H:i:s", $lasttime));
  		// 		}
      //
  		// 		$this->db->limit(1);
      //     $this->db->select("*");
  		// 		$this->db->where("gps_info_time <=", date("Y-m-d H:i:s"));
      //     $this->db->where("gps_info_device", $name."@".$host);
      //     $this->db->order_by("gps_info_time", "desc");
  		// 		// $this->db->where("gps_latitude <>", 0);
  		// 		// $this->db->where("gps_longitude <>", 0);
  		// 		$q = $this->db->get($tables['info']);
      //
  		// 		if ($q->num_rows() == 0)
  		// 		{
  		// 			$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
      //
  		// 			$istbl_history = $this->config->item("dbhistory_default");
  		// 			if($this->config->item("is_dbhistory") == 1)
  		// 			{
  		// 				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
  		// 			}
  		// 			$this->db = $this->load->database($istbl_history, TRUE);
      //
  		// 			$this->db->limit(1);
      //       $this->db->select("*");
  		// 			$this->db->where("gps_info_device", $name."@".$host);
      //       $this->db->order_by("gps_time", "desc");
  		// 			// $this->db->where("gps_host", $host);
  		// 			// $this->db->where("gps_latitude <>", 0);
  		// 			// $this->db->where("gps_longitude <>", 0);
  		// 			$q = $this->db->get($tablehist);
      //
  		// 			if ($q->num_rows() == 0) return;
  		// 		}
      //
  		// 		$row1 = $row;
  		// 		$row = $q->row();
  		// 		$q->free_result();
  		// 		$row->gps_time = $row1->gps_info_time;
  		// 		$row->gps_status = "V";
  		// 		$row->gps_utc_date = $row1->gps_info_utc_date;
  		// 		$row->gps_utc_coord = $row1->gps_info_utc_coord;
  		// 	}
  		// 	}
  		// }
      //
      //
  		// $tgl = floor($row->gps_utc_date/10000);
  		// $bln = floor(($row->gps_utc_date%10000)/100);
  		// $thn = (($row->gps_utc_date%10000)%100)+2000;
      //
  		// $jam = floor($row->gps_utc_coord/10000);
  		// $min = floor(($row->gps_utc_coord%10000)/100);
  		// $det = ($row->gps_utc_coord%10000)%100;
      //
  		// $mtime = mktime($jam+7,$min, $det, $bln, $tgl, $thn);
  		// $mtimeori = mktime($jam,$min, $det, $bln, $tgl, $thn);
      //
  		// // cek apakah data updated
      //
  		// //$delays = $this->config->item("css_tracker_delay");
      //
  		// //for admin lacak
  		// if(isset($this->sess->user_type) && ($this->sess->user_type == 1))
  		// {
  		// 	$delays = $this->config->item("css_tracker_delay_admin");
  		// }
  		// else
  		// {
  		// 	$delays = $this->config->item("css_tracker_delay");
  		// }
  		// // for ssi 1933
  		// if(isset($rowvehicle) && ($rowvehicle->vehicle_user_id == 1933))
  		// {
  		// 	$delays = $this->config->item("css_tracker_delay_ssi");
  		// }
      //
  		// if(isset($this->sess->user_id))
  		// {
  		// 	if (in_array($this->sess->user_id, $this->config->item("user_pins")))
  		// 	{
  		// 		$delays = $this->config->item("css_tracker_delay_pins");
  		// 	}
  		// }
      //
  		// $delay = $delays[count($delays)-2][0]*60;
  		// if ((mktime() - $mtime) > $delay)
  		// {
  		// 	//$this->notice_datadelay($name, $host, $mtime);
  		// }
      //
  		// $row->gps_timestampori = $mtimeori;
  		// $row->gps_timestamp = $mtime;
      //
  		// $row->gps_date_fmt = date("d/m/Y", $mtime);
  		// $row->gps_time_fmt = date("H:i:s", $mtime);
      //
  		// //t6 invalid conditon
  		// if ($rowvehicle->vehicle_type == "T6" && $row->gps_status == "V")
  		// {
  		// 	$tables = $this->gpsmodel->getTable($rowvehicle);
  		// 	$this->db = $this->load->database($tables["dbname"], TRUE);
      //
  		// 	$this->db->limit(1);
  		// 	$this->db->order_by("gps_time", "desc");
  		// 	$this->db->where("gps_time <=", date("Y-m-d H:i:s"));
  		// 	$this->db->where("gps_name", $name);
  		// 	$this->db->where("gps_host", $host);
  		// 	$this->db->where("gps_latitude <>", 0);
  		// 	$this->db->where("gps_longitude <>", 0);
  		// 	$this->db->where("gps_status", "A");
  		// 	$q_lastvalid = $this->db->get($tables['gps']);
      //
  		// 	if ($q_lastvalid->num_rows() == 0)
  		// 	{
  		// 		$tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
      //
  		// 		$istbl_history = $this->config->item("dbhistory_default");
  		// 		if($this->config->item("is_dbhistory") == 1)
  		// 		{
  		// 			$istbl_history = $rowvehicle->vehicle_dbhistory_name;
  		// 		}
  		// 		$this->db = $this->load->database($istbl_history, TRUE);
      //
  		// 		$this->db->limit(1);
  		// 		$this->db->order_by("gps_time", "desc");
  		// 		$this->db->where("gps_name", $name);
  		// 		$this->db->where("gps_host", $host);
  		// 		$this->db->where("gps_latitude <>", 0);
  		// 		$this->db->where("gps_longitude <>", 0);
  		// 		$this->db->where("gps_status", "A");
  		// 		$q_lastvalid = $this->db->get($tablehist);
      //
  		// 		if ($q_lastvalid->num_rows() == 0) return;
  		// 	}
      //
  		// 	$row_lastvalid = $q_lastvalid->row();
  		// 	//print_r($row_lastvalid);exit();
  		// 	$row->gps_longitude_real = getLongitude($row_lastvalid->gps_longitude, $row_lastvalid->gps_ew);
  		// 	$row->gps_latitude_real = getLatitude($row_lastvalid->gps_latitude, $row_lastvalid->gps_ns);
  		// }
  		// else{
  		// 	if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type !="TK315" && $rowvehicle->vehicle_type !="TK315DOOR" && $rowvehicle->vehicle_type !="TK315N" && $rowvehicle->vehicle_type !="TK309N" )
  		// 	{
  		// 		$row->gps_longitude_real = getLongitude($row->gps_longitude, $row->gps_ew);
  		// 		$row->gps_latitude_real = getLatitude($row->gps_latitude, $row->gps_ns);
  		// 	}
  		// 	else
  		// 	{
  		// 		$a = explode("-",$row->gps_latitude);
  		// 		if(isset($a[1]))
  		// 		{
  		// 			$row->gps_latitude_real = number_format($row->gps_latitude, 4, ".", "");
  		// 		}
  		// 		else
  		// 		{
  		// 			if($row->gps_ns == "S")
  		// 			{
  		// 				$row->gps_latitude_real = number_format("-".$row->gps_latitude, 4, ".", "");
  		// 			}
  		// 			else
  		// 			{
  		// 				$row->gps_latitude_real = number_format($row->gps_latitude, 4, ".", "");
  		// 			}
  		// 		}
  		// 		$row->gps_longitude_real = $row->gps_longitude;
  		// 	}
  		// }
      //
      //
  		// if($rowvehicle->vehicle_type == "TJAM")
  		// {
  		// 	$a = explode("-",$row->gps_latitude_real);
  		// 	if(isset($a[1]))
  		// 	{
  		// 		$row->gps_latitude_real_fmt = number_format($row->gps_latitude_real, 4, ".", "");
  		// 	}
  		// 	else
  		// 	{
  		// 		if($row->gps_ns == "S")
  		// 		{
  		// 			$row->gps_latitude_real_fmt = number_format("-".$row->gps_latitude_real, 4, ".", "");
  		// 		}
  		// 		else
  		// 		{
  		// 			$row->gps_latitude_real_fmt = number_format($row->gps_latitude_real, 4, ".", "");
  		// 		}
  		// 	}
  		// }
  		// else
  		// {
  		// 	$row->gps_latitude_real_fmt = number_format($row->gps_latitude_real, 4, ".", "");
  		// }
      //
  		// $row->gps_longitude_real_fmt = number_format($row->gps_longitude_real, 4, ".", "");
  		// //$row->gps_latitude_real_fmt = number_format($row->gps_latitude_real, 4, ".", "");
      //
  		// if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type !="TK315" && $rowvehicle->vehicle_type !="TK315DOOR" && $rowvehicle->vehicle_type !="TK315N" && $rowvehicle->vehicle_type !="TK309N" )
  		// {
  		// 	$mtime = mktime($jam+7,$min, $det, $bln, $tgl, $thn);
  		// 	$nowtime = mktime(date('G'), date('i'), date('s'), date('n'), date('j'), date('Y'));
  		// }
  		// else
  		// {
  		// 	$mtime = mktime($jam,$min, $det, $bln, $tgl, $thn);
  		// 	$nowtime = mktime(date('G'), date('i'), date('s'), date('n'), date('j'), date('Y'));
  		// }
      //
  		// $arr = $this->lang->line('lmonth');
      //
  		// $row->gps_date_fmt = date("j ",$mtime).$arr[date("n", $mtime)-1].date(" Y", $mtime);
  		// $row->gps_time_fmt = date("H:i:s", $mtime);
  		// $row->gps_speed_fmt = number_format($row->gps_speed*1.852, 0, "", ".");
      //
  		// //$delays = $this->config->item("css_tracker_delay");
      //
  		// //for admin lacak
  		// if(isset($this->sess->user_type) && ($this->sess->user_type == 1))
  		// {
  		// 	$delays = $this->config->item("css_tracker_delay_admin");
  		// }
  		// else
  		// {
  		// 	$delays = $this->config->item("css_tracker_delay");
  		// }
  		// // for ssi 1933
  		// if(isset($rowvehicle) && ($rowvehicle->vehicle_user_id == 1933))
  		// {
  		// 	$delays = $this->config->item("css_tracker_delay_ssi");
  		// }
      //
  		// if(isset($this->sess->user_id))
  		// {
  		// 	if (in_array($this->sess->user_id, $this->config->item("user_pins")))
  		// 	{
  		// 		$delays = $this->config->item("css_tracker_delay_pins");
  		// 	}
  		// }
      //
  		// if (is_array($delays))
  		// {
  		// 	$found = false;
  		// 	for($i=0; $i < count($delays); $i++)
  		// 	{
  		// 		$deviasi = $nowtime-$mtime;
  		// 		if ($deviasi < ($delays[$i][0]*60)) continue;
      //
  		// 		//echo $rows[$i]->gps_name." :: ".$rows[$i]->gps_host." :: ".$val[0]." :: ".date("M, jS Y H:i:s", $mtime)." :: ".date("M, jS Y H:i:s", $nowtime)."<br />\r\n";
      //
  		// 		$row->css_delay_index = $i;
  		// 		$row->css_delay = $delays[$i];
  		// 		$row->css_delay_time = $deviasi." :: ".date('Ymd His', $nowtime)." :: ".date('Ymd His', $mtime);
  		// 		$found = true;
  		// 		break;
  		// 	}
      //
  		// 	if (! $found)
  		// 	{
  		// 		$row->css_delay = $delays[count($delays)-1];
  		// 		$row->css_delay_time = 0;
  		// 		$row->css_delay_index = count($delays)-1;
  		// 	}
  		// }
      //
  		// $row->direction = $this->GetDirection($row->gps_course);
      //
  		// if ($row->gps_speed*1)
  		// {
  		// 	$row->car_icon = $this->GetDirection($row->gps_course);
  		// }
  		// else
  		// {
  		// 	$row->car_icon = 0;
  		// }
      //
  		// $this->db = $this->load->database("default", TRUE);
      //
  		// if ($georeverse)
  		// {
  		// 	$row->georeverse = $this->GeoReverse($row->gps_latitude_real_fmt, $row->gps_longitude_real_fmt);
  		// }

  		// return $row;
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

      /*$lokasi = $this->GeoReverseService("http://".$this->config->item("georeverse_host")."/map/georeverse/".$lat."/".$lng);
      $temp->display_name = $lokasi;

      if ($lokasi == "Unknown address")
      {
        return $temp;
      }

      if ($lokasi == "Unknown address (err: CURL disabled)")
      {
        return $temp;
      }*/

      /*
      unset($data);
      $data['location_lat'] = $lat;
      $data['location_lng'] = $lng;
      $data['location_address'] = $temp->display_name;

      $mydb = $this->load->database("master", TRUE);
      $mydb->insert("location", $data);
      */

      //by pass unknown address
      /*
      $temp->display_name = "Unknown address";
      if ($lokasi == "Unknown address")
      {
        $temp->display_name = $lokasi;
        return $temp;
      }
      if ($lokasi == "Unknown address (err: CURL disabled)")
      {
        $temp->display_name = $lokasi;
        return $temp;
      }
      */

      //Apigoogle
      $apikey = "AIzaSyDgDxL_3CpFInoeSmGy-oZElFJeKtgEUWA"; //LACAKTRANSLOG (PREMIUM)

      $token = "BkaW5kNGhraTR0OnAwNXRkNHQ0a2k0dA16B";
      $authorization = "Authorization:".$token;
      $url = "http://www.lacak-mobil.com/api/geocode";
      $feature = array();

      $feature["lat"] = $lat;
      $feature["long"] = $lng;
      $feature["apikey"] = $apikey;

      $content = json_encode($feature);
      $total_content = count($content);

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

      $json_response = curl_exec($curl);
      $data_json = json_decode($json_response,true);
      $temp->display_name = trim($data_json["location"]);

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

	  function vehicleconfig($tableprefix, $user_company){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where("maintenance_conf_vehicle_user_company", $user_company);
      $this->dbtransporter->order_by("maintenance_conf_vehicle_no", "asc");
      return $this->dbtransporter->get($tableprefix)->result_array();
    }

    function getalerttable($table, $where, $wherenya){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where($where, $wherenya);
      $this->dbtransporter->order_by("transporter_alert_vehicleno", "asc");
      return $this->dbtransporter->get($table)->result_array();
    }

    function changethisisread($table, $datanya){
        $this->dbtransporter = $this->load->database("transporter", true);
        $this->dbtransporter->where("transporter_isread", "0");
        return $this->dbtransporter->update($table, $datanya);
    }

    function updatethisdata($table, $where, $id, $data){
        $this->dbtransporter = $this->load->database("transporter", true);
        $this->dbtransporter->where($where, $id);
        return $this->dbtransporter->update($table, $data);
    }

    function getunscheduledservice($table){
      $user_company = $this->sess->user_company;
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->where("servicess_tipeservice", "4");
      $this->dbtransporter->where("servicess_flag", "0");
      $this->dbtransporter->where("servicess_user_company", $user_company);
      return $this->dbtransporter->get($table)->result_array();
    }

    function updatemdt($table, $data, $id){
      $this->db = $this->load->database("default", true);
      $this->db->where("vehicle_id", $id);
      return $this->db->update($table, $data);
    }

    function getmdtnya($vid){
      $this->db     = $this->load->database("default", true);
      $this->db->select("vehicle_mdt");
      $this->db->where("vehicle_id", $vid);
      $q       = $this->db->get("vehicle");
      return  $q->result_array();
    }


    function getallvehicle(){
      ini_set('display_errors', 1);

      //print_r("DISINI");exit();
      $user_level      = $this->sess->user_level;
      $user_company    = $this->sess->user_company;
      $user_subcompany = $this->sess->user_subcompany;
      $user_group      = $this->sess->user_group;
      $user_subgroup   = $this->sess->user_subgroup;
      $user_parent     = $this->sess->user_parent;
      $privilegecode   = $this->sess->user_id_role;

      if($this->sess->user_id == "1445"){
        $user_id = $this->sess->user_id; //tag
      }else{
        $user_id = $this->sess->user_id;
      }

      $user_id_fix     = $user_id;
      //GET DATA FROM DB
      $this->db     = $this->load->database("default", true);
      $this->db->select("*");
      $this->db->order_by("vehicle_no","asc");

      if($privilegecode == 0){
        $this->db->where("vehicle_user_id", $user_id_fix);
      }else if($privilegecode == 1){
        $this->db->where("vehicle_user_id", $user_parent);
      }else if($privilegecode == 3){
        $this->db->where("vehicle_user_id", $user_parent);
      }else if($privilegecode == 4){
        $this->db->where("vehicle_user_id", $user_parent);
      }else if($privilegecode == 5){
        $this->db->where("vehicle_company", $user_company);
      }else if($privilegecode == 6){
        $this->db->where("vehicle_company", $user_company);
      }else{
        $this->db->where("vehicle_no",99999);
      }

      $this->db->where("vehicle_status <>", 3);
      $q       = $this->db->get("vehicle");
      return $result  = $q->result_array();
    }

    function getallvehicleByID($vdevice){
      ini_set('display_errors', 1);

      //print_r("DISINI");exit();
      $user_level      = $this->sess->user_level;
      $user_company    = $this->sess->user_company;
      $user_subcompany = $this->sess->user_subcompany;
      $user_group      = $this->sess->user_group;
      $user_subgroup   = $this->sess->user_subgroup;
      $user_parent     = $this->sess->user_parent;
      $privilegecode   = $this->sess->user_id_role;

      if($this->sess->user_id == "1445"){
        $user_id = $this->sess->user_id; //tag
      }else{
        $user_id = $this->sess->user_id;
      }

      $user_id_fix     = $user_id;
      //GET DATA FROM DB
      $this->db     = $this->load->database("default", true);
      $this->db->select("*");
      $this->db->order_by("vehicle_no","asc");

      if($privilegecode == 0){
        $this->db->where("vehicle_user_id", $user_id_fix);
      }else if($privilegecode == 1){
        $this->db->where("vehicle_user_id", $user_parent);
      }else if($privilegecode == 3){
        $this->db->where("vehicle_user_id", $user_parent);
      }else if($privilegecode == 4){
        $this->db->where("vehicle_user_id", $user_parent);
      }else if($privilegecode == 5){
        $this->db->where("vehicle_company", $user_company);
      }else if($privilegecode == 6){
        $this->db->where("vehicle_company", $user_company);
      }else{
        $this->db->where("vehicle_no",99999);
      }

      $this->db->where("vehicle_device", $vdevice);
      $this->db->where("vehicle_status <>", 3);
      $q       = $this->db->get("vehicle");
      return $result  = $q->result_array();
    }

    function getallmaintenancetype(){
      $this->db->order_by("maintenance_type_name","asc");
      $this->db->select("*");
      $this->db->where("maintenance_type_flag", 0);
      $q       = $this->db->get("maintenance_type");
      return $result  = $q->result_array();
    }

    function getallmaintenancecat(){
      $this->db->order_by("maintenance_cat_name","asc");
      $this->db->select("*");
      $this->db->where("maintenance_cat_flag", 0);
      $q       = $this->db->get("maintenance_category");
      return $result  = $q->result_array();
    }

    function getCategoryByType($mTypeid){
      $this->db->select("*");
      $this->db->where("maintenance_cat_typeid", $mTypeid);
      $this->db->where("maintenance_cat_flag", 0);
      $this->db->order_by("maintenance_cat_name","asc");
      $q       = $this->db->get("maintenance_category");
      return $result  = $q->result_array();
    }

    function insertData($table, $data){
      $this->db = $this->load->database("default", true);
      return $this->db->insert($table, $data);
    }

    function insertDataUmum($table, $data, $db){
      $this->dbnya = $this->load->database($db, true);
      return $this->dbnya->insert($table, $data);
    }

    function getonprocess($table){
      $user_level      = $this->sess->user_level;
      $user_company    = $this->sess->user_company;
      $user_subcompany = $this->sess->user_subcompany;
      $user_group      = $this->sess->user_group;
      $user_subgroup   = $this->sess->user_subgroup;
      $user_parent     = $this->sess->user_parent;
      $privilegecode   = $this->sess->user_id_role;

      if($this->sess->user_id == "1445"){
        $user_id = $this->sess->user_id; //tag
      }else{
        $user_id = $this->sess->user_id;
      }

      $user_id_fix     = $user_id;
      $array_id = array($user_company, $user_id);

      $this->dbts = $this->load->database("webtracking_ts", true);
      $this->dbts->where("breakdown_status", 0);
      $this->dbts->where("breakdown_flag", 0);
      $this->dbts->where("breakdown_mode", "FMS");

      // if($privilegecode == 0){
      //   $this->dbts->where("breakdown_creator_id", $user_id_fix);
      // }else if($privilegecode == 1){
      //   $this->dbts->where("breakdown_creator_id", $user_parent);
      // }else if($privilegecode == 3){
      //   $this->dbts->where("breakdown_creator_id", $user_parent);
      // }else if($privilegecode == 4){
      //   $this->dbts->where("breakdown_creator_id", $user_parent);
      // }else
      if($privilegecode == 5 || $privilegecode == 6){
        $this->dbts->where_in("breakdown_creator_id", $array_id);
      }
      $this->dbts->order_by("breakdown_start_time", "DESC");

      // else{
      //   $this->dbts->where("breakdown_creator_id",99999);
      // }

      return $this->dbts->get($table)->result_array();
    }

    function getonprocessbyID($id){
      $this->dbts = $this->load->database("webtracking_ts", true);
      $this->dbts->where("breakdown_id", $id);
      $this->dbts->where("breakdown_status", 0);
      $this->dbts->where("breakdown_mode", "FMS");
      return $this->dbts->get("ts_driver_breakdown")->result_array();
    }

    function updateOnProcess($tableprefix, $where, $wherenya, $datanya){
      $this->dbts = $this->load->database("webtracking_ts", true);
      $this->dbts->where($where, $wherenya);
      return $this->dbts->update($tableprefix, $datanya);
    }

    function deleteDataBreakdown($tableprefix, $where, $wherenya, $datanya){
      $this->dbts = $this->load->database("webtracking_ts", true);
      $this->dbts->where($where, $wherenya);
      return $this->dbts->update($tableprefix, $datanya);
    }

    function deleteData($table, $id, $data){
      $this->db = $this->load->database("default", true);
      $this->db->where("maintenance_type_id", $id);
      return $this->db->update("maintenance_type", $data);
    }

    function deleteDataCategory($table, $id, $data){
      $this->db = $this->load->database("default", true);
      $this->db->where("maintenance_cat_id", $id);
      return $this->db->update($table, $data);
    }

    function isShareCheck($vDevice){
      $this->db     = $this->load->database("default", true);
      $this->db->select("vehicle_is_share");
      $this->db->where("vehicle_device", $vDevice);
      $this->db->where("vehicle_status <>", 3);
      $q       = $this->db->get("vehicle");
      return $result  = $q->result_array();
    }

    function getAllShareRequest(){
      $this->dbts     = $this->load->database("webtracking_ts", true);
      $this->dbts->select("*");
      // $this->dbts->where("vehicle_share_flag", 0);
      $q       = $this->dbts->get("ts_vehicle_share");
      return $result  = $q->result_array();
    }

    function getDataRequestShare($idrequest){
      $this->dbts     = $this->load->database("webtracking_ts", true);
      $this->dbts->select("*");
      $this->dbts->where("vehicle_share_id", $idrequest);
      $q       = $this->dbts->get("ts_vehicle_share");
      return $result  = $q->result_array();
    }

    function approve_vehicle_share($table, $where, $wherenya, $datanya){
      $this->dbtransporter = $this->load->database("default", true);
      $this->dbtransporter->where($where, $wherenya);
      return $this->dbtransporter->update($table, $datanya);
    }

    function update_date_webtrackingts($table, $where, $wherenya, $datanya){
      $this->dbts = $this->load->database("webtracking_ts", true);
      $this->dbts->where($where, $wherenya);
      return $this->dbts->update($table, $datanya);
    }

}
?>
