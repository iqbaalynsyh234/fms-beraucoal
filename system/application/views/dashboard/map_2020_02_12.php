<?php
include "base.php";

class Map extends Base {

	function Map()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("smsmodel");
	}

	function history($name, $host, $gpsid)
	{
		if (! $this->sess)
		{
			redirect(base_url());
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $name.'@'.$host);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$row = $q->row();

		$this->params["gpsid"] = $gpsid;
		$this->params["zoom"] = $this->config->item("zoom_history");
		$this->params["data"] = $row;
		$this->params["ishistory"] = "on";
		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["updateinfo"] =  $this->load->view('updateinfohistory', $this->params, true);
		$this->params["content"] = $this->load->view('map/realtime', $this->params, true);
		$this->load->view("templatesess", $this->params);

	}

	function realtime($name, $host="")
	{
		if (! $this->sess)
		{
			redirect(base_url());
		}

		if ($this->sess->user_type == 2)
		{
			$vehicleids = $this->vehiclemodel->getVehicleIds();
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $name.'@'.$host);
		if ($this->sess->user_type == 2)
		{
			// security, make sure bahwa yang dibuka benar kendaraan punyanya
			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $vehicleids);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}

			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
			$this->db->join("user", "vehicle_user_id = user_id");
		}

		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$row = $q->row();

		$this->params['title'] = $this->lang->line('ltracker').' '.$row->vehicle_name.'-'.$row->vehicle_no.' ';
		$this->params["ishistory"] = "off";
		$this->params["zoom"] = $this->config->item("zoom_realtime");
		$this->params["data"] = $row;
		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["updateinfo"] = $this->load->view('updateinfo', $this->params, true);
		$this->params["content"] = $this->load->view('map/realtime', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function lastinfo()
	{

		if (isset($_POST['session'])) //ndak
		{
                        $this->db->where("session_id", $_POST['session']);
                        $this->db->join("user", "user_id = session_user");
                        $q = $this->db->get("session");

                        if ($q->num_rows() == 0) return;

                        $this->sess = $q->row();
		}


		if (! $this->sess)
		{
			echo json_encode(array("info"=>"", "vehicle"=>"")); //ndak
			return;
		}

		$device = isset($_POST['device']) ? $_POST['device'] : "";    //dapet
		$lasttime = isset($_POST['lasttime']) ? $_POST['lasttime'] : 0;


		if ($this->sess->user_type == 2)
		{
			$vehicleids = $this->vehiclemodel->getVehicleIds(); //dapet
		}

		switch($this->sess->user_type)
		{
			case 2:
				if ($this->sess->user_company)
				{
					$this->db->where_in("vehicle_id", $vehicleids);	//dapet
				}
				else
				{
					$this->db->where("vehicle_user_id", $this->sess->user_id);
				}
			break;
			case 3:
				$this->db->where("user_agent", $this->sess->user_agent);
			break;
		}


		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_device", $device);
		$this->db->join("user", "vehicle_user_id = user_id");
		//edited
		//$this->db->join("bank", "user_agent = bank_agent", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			echo json_encode(array("info"=>"", "vehicle"=>""));
			return;
		}

		$row = $q->row();

		// cek expire
		if ($row->vehicle_active_date2 && ($row->vehicle_active_date2 < date("Ymd")))
		{
			$row->vehicle_active_date2_fmt = inttodate($row->vehicle_active_date2);

			echo json_encode(array("info"=>"expired", "vehicle"=>$row));
			return;

		}

		$arr = explode("@", $device);

		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";

		$gtps = $this->config->item("vehicle_gtp");

		if (! in_array(strtoupper($row->vehicle_type), $gtps))
		{
			$row->status = "-";

			$taktif = dbintmaketime($row->vehicle_active_date, 0);

			$json = json_decode($row->vehicle_info);
			if (isset($json->sisapulsa))
			{
				if (strlen($json->masaaktif) == 6)
				{
					$taktif = dbintmaketime1($json->masaaktif, 0);
				}
				else
				{
					$taktif = dbintmaketime2($json->masaaktif, 0);
				}

				$row->pulse = sprintf("Rp %s", number_format($json->sisapulsa, 0, "", "."));
				$row->masaaktif = date("d/m/Y", $taktif);
			}
			else
			{
				$row->pulse = false;
			}

		}
		else
		{
			//Seleksi Database;
			if ($row->vehicle_info)
			{
			$json = json_decode($row->vehicle_info);
			if (isset($json->vehicle_ip) && isset($json->vehicle_port))
			{
				$databases = $this->config->item('databases');

				if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
				{
					$database  = $databases[$json->vehicle_ip][$json->vehicle_port];

					$table     = $this->config->item("external_gpstable");
					$tableinfo = $this->config->item("external_gpsinfotable");
					$this->db  = $this->load->database($database, TRUE);
				}
			}
			}

			if (! isset($table))
			{
			$table     = $this->gpsmodel->getGPSTable($row->vehicle_type);
			$tableinfo = $this->gpsmodel->getGPSInfoTable($row->vehicle_type);
			}

			// ambil informasi di gps_info

			$this->db->order_by("gps_info_time", "DESC");
			$this->db->where("gps_info_device", $device);
			$q = $this->db->get($tableinfo, 1, 0);

			if ($q->num_rows() == 0)
			{
				$row->status = "-";
				$row->status1 = false;
				$row->status2 = false;
				$row->status3 = false;
				$row->pulse = "-";
			}
			else
			{
				$rowinfo = $q->row();

				$ioport = $rowinfo->gps_info_io_port;

				$row->status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
				$row->status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
				$row->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
				$row->status = $row->status2 || $row->status1 || $row->status3;

				$pulses = $this->config->item("vehicle_pulse");
				if (! in_array(strtoupper($row->vehicle_type), $pulses))
				{
					$json = json_decode($row->vehicle_info);
					if (isset($json->sisapulsa))
					{
						if (strlen($json->masaaktif) == 6)
						{
							$taktif = dbintmaketime1($json->masaaktif, 0);
						}
						else
						{
							$taktif = dbintmaketime2($json->masaaktif, 0);
						}

						$row->pulse = sprintf("Rp %s", number_format($json->sisapulsa, 0, "", "."));
						$row->masaaktif = date("d/m/Y", $taktif);
					}
					else
					{
						$row->pulse = false;
					}
				}
				else
				{
					//$rowinfo->gps_info_ad_input = "00B0742177";

					$pulsa = number_format(hexdec(substr($rowinfo->gps_info_ad_input, 0, 5)), 0, "", ".");
					$aktif = hexdec(substr($rowinfo->gps_info_ad_input, 5));

					$taktif = dbintmaketime1($aktif, 0);

					$row->pulse = sprintf("Rp %s", $pulsa);
					$row->masaaktif = date("d/m/Y", $taktif);
				}

				$fuels = $this->config->item("vehicle_fuel");
				if (! in_array(strtoupper($row->vehicle_type), $fuels))
				{
					$row->fuel = false;
				}
				else
				{
					$row->fuel = "-";
					if($rowinfo->gps_info_ad_input != ""){
						if($rowinfo->gps_info_ad_input != 'FFFFFF' || $rowinfo->gps_info_ad_input != '999999' || $rowinfo->gps_info_ad_input != 'YYYYYY'){
							$fuel_1 = hexdec(substr($rowinfo->gps_info_ad_input, 0, 4));
							$fuel_2 = (hexdec(substr($rowinfo->gps_info_ad_input, 0, 2))) * 0.1;

							$fuel = $fuel_1 + $fuel_2;

							$sql = "SELECT * FROM (
										(
											SELECT *
											FROM `webtracking_fuel`
											WHERE `fuel_tank_capacity` = ". $row->vehicle_fuel_capacity ."
											AND `fuel_led_resistance` >= ". $fuel ."
											ORDER BY fuel_led_resistance ASC
											LIMIT 1
										) UNION (
											SELECT *
											FROM `webtracking_fuel`
											WHERE `fuel_tank_capacity` = ". $row->vehicle_fuel_capacity ."
											AND `fuel_led_resistance` <= ". $fuel ."
											ORDER BY fuel_led_resistance DESC
											LIMIT 1
										)
									) tbldummy";

							$qfuel = $this->db->query($sql);
							if ($qfuel->num_rows() > 0){
   								$rfuel = $qfuel->result();

								if ($qfuel->num_rows() == 1){
									$row->blink = false;
									$row->fuel_scale = $rfuel[0]->fuel_gas_scale * 10;
									$row->fuel = $rfuel[0]->fuel_volume . "L";
								}else{
									$row->blink = true;
									$row->fuel_scale = $rfuel[1]->fuel_gas_scale * 10;
									$row->fuel = $rfuel[0]->fuel_volume . "L - " . $rfuel[1]->fuel_volume . "L";
								}
							}


						}
					}

				}
				$row->totalodometer = round(($rowinfo->gps_info_distance+$row->vehicle_odometer*1000)/1000);
				//$row->totalodometer = str_split($strodometer);
			}

		}

		$t = dbintmaketime($row->vehicle_active_date1, 0);
		$row->vehicle_active_date1_fmt = date("M, jS Y", $t);

		$t = dbintmaketime($row->vehicle_active_date2, 0);
		$row->vehicle_active_date2_fmt = date("M, jS Y", $t);

		$arr = explode("@", $device);

		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";


		$row->vehicle_device_name = $devices[0];
		$row->vehicle_device_host = $devices[1];
		// print_r($row);

		$params["vehicle"] = $row;
		$row->gps = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $row->vehicle_type);



		if (! $row->gps)
		{
			echo json_encode(array("info"=>"", "vehicle"=>$row));
			return;
		}

		$delayresatrt = mktime() - $row->gps->gps_timestamp;
		$kdelayrestart = $this->config->item("restart_delay")*60;

		if (true)
		{
			$restart = $this->smsmodel->restart($row->vehicle_type, $row->vehicle_operator);
			$row->restartcommand = $restart;
		}
		else
		{
			$row->restartcommand = "";
		}

		if (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_T1")))
		{
			$row->checkpulsa = $this->smsmodel->checkpulse($row->vehicle_operator);
		}
		else
		{
			$row->checkpulsa = "";
		}


		//get geofence location
		$row->geofence_location = $this->getGeofence_location($row->gps->gps_longitude, $row->gps->gps_ew, $row->gps->gps_latitude, $row->gps->gps_ns, $row->vehicle_device);
        	//print_r($row->vehicle_device);
        	//$params["geofence_location"] = $row->geofence_location;
		$row->driver = $this->getdriver($row->vehicle_id);

		//test
		$row->destination = $this->getdestination($row->vehicle_id);
		//end
		$row->since_geofence_in = "";

		//geofence alert off
		//$data_lokasi = $row->gps->georeverse->display_name;


		//$row->geofence_alert_off = $this->get_geofence_alert_off($row->vehicle_device, $row->gps->georeverse->display_name);
		$this->dbkim = $this->load->database('kim', TRUE);

		if(isset($row->geofence_location) && $row->geofence_location != ""){

			$row->since_geofence_in = $this->getInGeofence($row->vehicle_device, $row->geofence_location);

		}

		if($row->geofence_location == "" || $row->geofence_location == null){

			unset($dt_update);
			$dt_update['tanggal'] = "";
			$dt_update['geofence'] = "";
			$dt_update['duration'] = 0;
			$this->dbkim->where('vehicle_device', $device);
			$this->dbkim->update("table_time_gps", $dt_update);

		}

		$params["driver"] = $row->driver;
		$params["destination"] = $row->destination;
		//$params["geofence_alert_off"] = $row->geofence_alert_off;
		$params["devices"] = $devices;
		$params["data"] = $row->gps;
		$info = $this->load->view("map/info", $params, TRUE);

		echo json_encode(array("info"=>$info, "vehicle"=>$row));
	}

	function historyinfo()
	{
		$device = isset($_POST['device']) ? $_POST['device'] : "";
		$gpsid = isset($_POST['gpsid']) ? $_POST['gpsid'] : 0;

		if ($this->sess->user_type == 2)
		{
			$vehicleids = $this->vehiclemodel->getVehicleIds();
		}

		switch($this->sess->user_type)
		{
			case 2:
				if ($this->sess->user_company)
				{
					$this->db->where_in("vehicle_id", $vehicleids);
				}
				else
				{
					$this->db->where("vehicle_user_id", $this->sess->user_id);
				}
			break;
			case 3:
				$this->db->where("user_agent", $this->sess->user_agent);
			break;
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $device);
		$this->db->join("user", "vehicle_user_id = user_id");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
			return;
		}

		$row = $q->row();

		$t = dbintmaketime($row->vehicle_active_date1, 0);
		$row->vehicle_active_date1_fmt = date("M, jS Y", $t);

		$t = dbintmaketime($row->vehicle_active_date2, 0);
		$row->vehicle_active_date2_fmt = date("M, jS Y", $t);

		$this->db->where("gps_id", $gpsid);
		$q = $this->db->get($this->gpsmodel->getGPSTable($row->vehicle_type));

		if ($q->num_rows() == 0)
		{
			$tblhists = $this->config->item("table_hist");
			$tblhist = $tblhists[strtoupper($row->vehicle_type)];

			$this->db->where("gps_id", $gpsid);
			$q = $this->db->get($tblhist);
		}

		$row1 = $q->row();

		$arr = explode("@", $device);

		$gtps = $this->config->item("vehicle_gtp");

		if (! in_array(strtoupper($row->vehicle_type), $gtps))
		{
			$row1->status = "-";
		}
		else
		{
			// ambil informasi di gps_info

			$this->db->order_by("gps_info_time", "DESC");
			$this->db->where("gps_info_device", $device);
			$q = $this->db->get($this->gpsmodel->getGPSInfoTable($row->vehicle_type), 1, 0);
			if ($q->num_rows() == 0)
			{
				$row->status = "-";
				$row->status1 = false;
				$row->status2 = false;
				$row->status3 = false;
			}
			else
			{
				$rowinfo = $q->row();

				$ioport = $rowinfo->gps_info_io_port;

				$row->status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
				$row->status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1));
				$row->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1));
				$row->status = $row->status2 || $row->status1 || $row->status3;
			}
		}


		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";

		$row->gps = $this->gpsmodel->GetLastInfo("", "", true, $row1, 0, $row->vehicle_type);
		$params["devices"] = $devices;
		$params["vehicle"] = $row;
		$params["data"] = $row->gps;
		$info = $this->load->view("map/info", $params, TRUE);

		echo json_encode(array("info"=>$info, "vehicle"=>$row));
	}

	function kmllastcoord($lng, $lat, $id, $car, $history, $delay=-1, $nscale=-1, $hscale=-1)
	{

		$this->db->where("vehicle_id", $id);
		$q = $this->db->get("vehicle");

		$row = $q->row();

		$images = array_keys($this->config->item('vehicle_image'));
		$vimage = $row->vehicle_image ? $row->vehicle_image : $images[0];

		if ($history == "on")
		{
			$params["car"] = base_url().'assets/images/'.$vimage.'/car_front.png';
			$params["nscale"] = ($nscale != -1) ? $nscale : 0.75;
			$params["hscale"] = ($hscale != -1) ? $hscale : 1.25;
		}
		else
		if ($history == "on1")
		{
			switch($delay)
			{
				case 0:
					$params["car"] = base_url().'assets/images/'.$vimage.'/car4earth-red.png';
				break;
				case 1:
					$params["car"] = base_url().'assets/images/'.$vimage.'/car4earth-yellow.png';
				break;
				default:
					$params["car"] = base_url().'assets/images/'.$vimage.'/car_front.png';
			}

			$params["nscale"] = ($nscale != -1) ? $nscale : 0.5;
			$params["hscale"] = ($hscale != -1) ? $hscale : 1;
		}
		else
		{
			if ($car == 0)
			{
				$params["car"] = base_url().'assets/images/'.$vimage.'/car1.png';
			}
			else
			{
				$params["car"] = base_url().'assets/images/'.$vimage.'/car'.$car.'.gif';
			}

			$params["nscale"] = ($nscale != -1) ? $nscale : 1.5;
			$params["hscale"] = ($hscale != -1) ? $hscale : 2;
		}
		$params["lng"] = $lng;
		$params["lat"] = $lat;
		$params["vehicle"]  = $row;

		//header("Content-type: application/vnd.google-earth.kml+xml");
		$this->load->view('map/kmllastcoord', $params);
	}

	function historyfull()
	{
		if (! $this->sess)
		{
			redirect(base_url());
		}

		parse_str($_SERVER['QUERY_STRING'], $_GET);

		$this->db->where("vehicle_id", $_GET['vehicle']);
		$q = $this->db->get("vehicle");

		$row = $q->row();
		$this->params['row'] = $row;

		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["content"] = $this->load->view('map/history', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function poi()
	{
		header("Content-type: text/plain");
		echo "lat\tlon\ttitle\tdescription\ticonSize\ticonOffset\ticon\r\n";

		parse_str($_SERVER['QUERY_STRING'], $_GET);

		$bbox = $_GET['bbox'];
		list($west, $south, $east, $north) = explode(",", $bbox);

		$this->db->join("poi_category", "poi_cat_id = poi_category", "left outer join");
		$q = $this->db->get("poi");

		$rows = $q->result();

		for($i=0; $i < count($rows); $i++)
		{

			$lat = $rows[$i]->poi_latitude;
			$lng = $rows[$i]->poi_longitude;

			if ($lng < $west) continue;
			if ($lat < $south) continue;

			if ($lng > $east) continue;
			if ($lat > $north) continue;

			if ($rows[$i]->poi_cat_icon)
			{
				$rows[$i]->poi_cat_icon = base_url().'assets/images/poi/'.$rows[$i]->poi_cat_icon;
				$rows[$i]->poi_cat_icon_size = "16,16";
				$rows[$i]->poi_cat_icon_offset = "0,-16";
			}
			else
			{
				$rows[$i]->poi_cat_icon = "";
				$rows[$i]->poi_cat_icon_size = "";
				$rows[$i]->poi_cat_icon_offset = "";
			}

			printf("%s\t%s\t%s\t%s\t%s\t%s\t%s\r\n", $lat, $lng, $rows[$i]->poi_name, $rows[$i]->poi_name, $rows[$i]->poi_cat_icon_size, $rows[$i]->poi_cat_icon_offset, $rows[$i]->poi_cat_icon);
			//printf("%s,%s\t%s\t%s\t%s\r\n", $rows[$i]->poi_latitude, $rows[$i]->poi_longitude, $rows[$i]->poi_name, $rows[$i]->poi_name, $rows[$i]->poi_cat_icon);

		}

		$streeticon_png =  base_url().'assets/images/poi/'."highway.png";
		$streeticon_size = "16,16";
		$streeticon_offset = "0,-16";

		$q = $this->db->get("street");
		$rows = $q->result();

		for($i=0; $i < count($rows); $i++)
		{
			$data = json_decode($rows[$i]->street_serialize);
			$geometry = $data->geometry->coordinates;
			$polygon = $geometry[0];

			printf("%s\t%s\t%s\t%s\t%s\t%s\t%s\r\n", $polygon[0][1], $polygon[0][0], $rows[$i]->street_name, $rows[$i]->street_name, $streeticon_size, $streeticon_offset, $streeticon_png);
		}

		$cctv_png = base_url().'assets/images/poi/'."cctv.png";
		$cctv_size = "32,32";
		$cctv_offset = "0,-32";

		$this->db->where("cctv_status", 1);
		$q = $this->db->get("cctv");
		$rows = $q->result();

		for($i=0; $i < count($rows); $i++)
		{
			printf("%s\t%s\t%s\t%s\t%s\t%s\t%s\r\n", $rows[$i]->cctv_lat, $rows[$i]->cctv_lon, "cctv__".$rows[$i]->cctv_id, "cctv__".$rows[$i]->cctv_id, $cctv_size, $cctv_offset, $cctv_png);
		}
	}

	function googleearthservice($session, $vname, $vhost)
	{
		$this->db->where("session_id", $session);
		$this->db->join("user", "user_id = session_user");
		$q = $this->db->get("session");

		if ($q->num_rows() == 0) return;

		$row = $q->row();

		$this->googleearth($row->user_login, substr($row->user_pass, 1), $vname, $vhost);
	}

	function googleearth($user, $pass, $vname, $vhost)
	{
		$this->db->where("user_login", $user);
		$this->db->where("user_pass", '*'.$pass);
		$q = $this->db->get("user");

		if ($q->num_rows() == 0)
		{
			return;
		}

		$row = $q->row();

		switch ($row->user_type)
		{
			case 2:
				$this->db->where("user_id", $row->user_id);
			break;
			case 3:
				$this->db->where("user_agent", $row->user_agent);
			break;
		}

		$this->db->where("vehicle_device", $vname."@".$vhost);
		$this->db->join("user", "vehicle_user_id = user_id");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			return;
		}

		$row = $q->row();

		$t = dbintmaketime($row->vehicle_active_date1, 0);
		$row->vehicle_active_date1_fmt = date("M, jS Y", $t);

		$t = dbintmaketime($row->vehicle_active_date2, 0);
		$row->vehicle_active_date2_fmt = date("M, jS Y", $t);

		$row->gps = $this->gpsmodel->GetLastInfo($vname, $vhost, true, false, 0, $row->vehicle_type);
		$this->params['info'] = $row;

		// get all position

		$this->db->order_by("gps_time", "desc");
		$this->db->select("gps_latitude, gps_ns, gps_longitude, gps_ew");
		$this->db->where("gps_name", $vname);
		$this->db->where("gps_host", $vhost);
		$q = $this->db->get($this->gpsmodel->getGPSTable($row->vehicle_type));
		$rows = $q->result();

		$this->params['infoall'] = $rows;

		header("Content-type: application/vnd.google-earth.kmz");
		$this->load->view("map/googleearth", $this->params);
	}

	function georeverse($lat, $lng)
	{
		$urls = $this->config->item("google_georeverse_api");
		$url = sprintf($urls[$this->config->item("google_georeverse_active")], $lat, $lng);

		$lokasi = $this->gpsmodel->GeoReverseServiceA($url);
		if (isset($lokasi->results) && count($lokasi->results) > 0)
		{
			//echo "google ".$lokasi->results[0]->formatted_address;
			echo $lokasi->results[0]->formatted_address;
			return;
		}

		$lokasi = $this->gpsmodel->GeoReverseServiceA("http://nominatim.openstreetmap.org/reverse?format=json&lat=".$lat."&lon=".$lng);
		if (! isset($lokasi->display_name))
		{
			echo "Unknown address";
			return;
		}

		echo $lokasi->display_name;
		return;
	}

	function gpx()
	{
		parse_str($_SERVER['QUERY_STRING'], $_GET);

		$this->params['lon'] = $_GET['lon'];
		$this->params['lat'] = $_GET['lat'];

		$this->load->view("map/gpx", $this->params);
	}

	function geocode()
	{
		$text = isset($_POST['lokasi']) ? trim($_POST['lokasi']) : "";

		if (strlen($text) == 0)
		{
			unset($json);

			$json['error'] = true;
			$json['message'] = $this->lang->line("lempty_location");

			echo json_encode($json);
			return;
		}

		$data = sprintf("address=%s&sensor=true", urlencode($text));
		$url = "https://maps.googleapis.com/maps/api/geocode/json"."?".$data;

		$result = $this->gpsmodel->GeoReverseServiceA($url);

		if (! isset($result->results[0]->geometry->location->lat))
		{
			unset($json);

			$json['error'] = true;
			$json['message'] = $this->lang->line("lerr_location");

			echo json_encode($json);
			return;
		}

		if (! isset($result->results[0]->geometry->location->lng))
		{
			unset($json);

			$json['error'] = true;
			$json['message'] = $this->lang->line("lerr_location");

			echo json_encode($json);
			return;
		}

		$json['error'] = false;
		$json['lat'] = $result->results[0]->geometry->location->lat;
		$json['lng'] = $result->results[0]->geometry->location->lng;

		echo json_encode($json);
	}

	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_device) {
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);
        $geo_name = "''";

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence
					WHERE 	TRUE
                            AND (geofence_name <> %s)
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
                            AND (geofence_vehicle = '%s' )
                            AND (geofence_status = 1)
							ORDER BY (geofence_id = 'desc')
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $geo_name, $lng, $lat, $vehicle_device);
        //print_r($sql);
		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{
			$row = $q->row();

            $data = $row->geofence_name;

            return $data;


		}else
        {
            return false;
        }

	}

	function getInGeofence($vehicle_device, $geofence_location){

		/*$sql = "SELECT * FROM webtracking_geofence_alert a
				JOIN webtracking_geofence b ON b.geofence_id=a.geoalert_geofence
				WHERE a.geoalert_vehicle='" . $vehicle_device ."'
				AND b.geofence_name='" . $geofence_location ."'
				AND a.geoalert_direction = 1
				ORDER BY a.geoalert_time desc LIMIT 1";*/
				$wherein = array("860", "3013");
				$this->db = $this->load->database("default", TRUE);
				$this->db->select("*");
				$this->db->where("auto_vehicle_device", $vehicle_device);
				$this->db->where("auto_last_geofence_name", $geofence_location);
				$this->db->where("auto_change_geofence_datetime is NOT NULL");
				// $this->db->where("auto_last_engine", 'OFF');
				$this->db->where_in("auto_user_id", $wherein);
				$q      = $this->db->get("vehicle_autocheck");
				// print_r($q->row());exit;

		if($q->num_rows() > 0){

			$row = $q->row();
			//print_r($row);exit;

			$today = date("Y-m-d");
			$alert_date = substr($row->auto_change_geofence_datetime, 0,10);
			if($today == $alert_date){
				$data = "Since At :";
				$data .= substr($row->auto_change_geofence_datetime, -8);
			}else{
				$data = "Since At :";
				$data = date('d/m/Y H:i:s', strtotime($row->auto_change_geofence_datetime));
			}

			$this->load->helper('kopindosat');
			$duration = get_time_difference($row->auto_change_geofence_datetime, date("Y-m-d H:i:s"));


					//print_r($row->geoalert_time);exit;
					$time_detik = 0;
					$show = "";
					if($duration[0]!=0){
						$show .= $duration[0] ." Day ";
						$time_detik = $time_detik + ($duration[0] * 1440);
					}
					if($duration[1]!=0){
						$show .= $duration[1] ." Hour ";
						$time_detik = $time_detik + ($duration[1] * 3600);
					}
					if($duration[2]!=0){
						$show .= $duration[2] ." Min ";
						$time_detik = $time_detik + ($duration[2] * 60);
					}

					if($show == ""){
						$show .= "0 Min ";
					}

					$data .= " ". " Duration : " . $show;

			//$this->dbkim = $this->load->database('kim', TRUE);
			//$insert['data_asli'] = $data;
			//$this->dbkim->insert("DataRealtimeTableGeofence", $insert);

			$customer_geo = explode("#", $geofence_location);

			$this->dbkim = $this->load->database('kim', TRUE);


			if($data != "" || $data != null){

				if($geofence_location != 'office#kim pt' && $geofence_location != 'office#MBI, PT'){

						if($customer_geo[0] == 'customer'){
							unset($dt_update);
							$dt_update['tanggal'] = $data;
							$dt_update['geofence'] = $geofence_location;
							$dt_update['duration'] = $time_detik;
							$this->dbkim->where('vehicle_device', $vehicle_device);
							$this->dbkim->update("table_time_gps", $dt_update);
						}
				}

				if($geofence_location == 'office#kim pt' || $geofence_location == 'office#MBI, PT'){
					unset($dt_update);
					$dt_update['tanggal'] = "";
					$dt_update['geofence'] = "";
					$dt_update['duration'] = 0;
					$this->dbkim->where('vehicle_device', $vehicle_device);
					$this->dbkim->update("table_time_gps", $dt_update);
				}
			}




			return $data;
		}

		return false;
	}

	// function getInGeofence($vehicle_device, $geofence_location){
	//
	// 	/*$sql = "SELECT * FROM webtracking_geofence_alert a
	// 			JOIN webtracking_geofence b ON b.geofence_id=a.geoalert_geofence
	// 			WHERE a.geoalert_vehicle='" . $vehicle_device ."'
	// 			AND b.geofence_name='" . $geofence_location ."'
	// 			AND a.geoalert_direction = 1
	// 			ORDER BY a.geoalert_time desc LIMIT 1";*/
	//
	// 	$sql = "SELECT * FROM webtracking_geofence_alert a
	// 			JOIN webtracking_geofence b ON b.geofence_id=a.geoalert_geofence
	// 			WHERE a.geoalert_vehicle='" . $vehicle_device ."'
	// 			AND a.geoalert_direction = 1
	// 			ORDER BY a.geoalert_time desc LIMIT 1";
	//
	// 	$q = $this->db->query($sql);
	// 	//print_r($geofence_location);exit;
	//
	// 	if($q->num_rows() > 0){
	//
	// 		$row = $q->row();
	// 		//print_r($row);exit;
	//
	// 		$today = date("Y-m-d");
	// 		$alert_date = substr($row->geoalert_time, 0,10);
	// 		if($today == $alert_date){
	// 			$data = "At :";
	// 			$data .= substr($row->geoalert_time, -8);
	// 		}else{
	// 			$data = "At :";
	// 			$data = date('d/m/Y H:i:s', strtotime($row->geoalert_time));
	// 		}
	//
	// 		$this->load->helper('kopindosat');
	// 		$duration = get_time_difference($row->geoalert_time, date("Y-m-d H:i:s"));
	//
	//
	// 				//print_r($row->geoalert_time);exit;
	// 				$time_detik = 0;
	// 				$show = "";
	// 				if($duration[0]!=0){
	// 					$show .= $duration[0] ." Day ";
	// 					$time_detik = $time_detik + ($duration[0] * 1440);
	// 				}
	// 				if($duration[1]!=0){
	// 					$show .= $duration[1] ." Hour ";
	// 					$time_detik = $time_detik + ($duration[1] * 3600);
	// 				}
	// 				if($duration[2]!=0){
	// 					$show .= $duration[2] ." Min ";
	// 					$time_detik = $time_detik + ($duration[2] * 60);
	// 				}
	//
	// 				if($show == ""){
	// 					$show .= "0 Min ";
	// 				}
	//
	// 				$data .= " ". " Duration : " . $show;
	//
	// 		//$this->dbkim = $this->load->database('kim', TRUE);
	// 		//$insert['data_asli'] = $data;
	// 		//$this->dbkim->insert("DataRealtimeTableGeofence", $insert);
	//
	// 		$customer_geo = explode("#", $geofence_location);
	//
	// 		$this->dbkim = $this->load->database('kim', TRUE);
	//
	//
	// 		if($data != "" || $data != null){
	//
	// 			if($geofence_location != 'office#kim pt' && $geofence_location != 'office#MBI, PT'){
	//
	// 					if($customer_geo[0] == 'customer'){
	// 						unset($dt_update);
	// 						$dt_update['tanggal'] = $data;
	// 						$dt_update['geofence'] = $geofence_location;
	// 						$dt_update['duration'] = $time_detik;
	// 						$this->dbkim->where('vehicle_device', $vehicle_device);
	// 						$this->dbkim->update("table_time_gps", $dt_update);
	// 					}
	// 			}
	//
	// 			if($geofence_location == 'office#kim pt' || $geofence_location == 'office#MBI, PT'){
	// 				unset($dt_update);
	// 				$dt_update['tanggal'] = "";
	// 				$dt_update['geofence'] = "";
	// 				$dt_update['duration'] = 0;
	// 				$this->dbkim->where('vehicle_device', $vehicle_device);
	// 				$this->dbkim->update("table_time_gps", $dt_update);
	// 			}
	// 		}
	//
	//
	//
	//
	// 		return $data;
	// 	}
	//
	// 	return false;
	// }

	function getdriver($driver_vehicle) {

		$this->dbkim = $this->load->database('kim',true);
		$this->dbkim->select("*");
		$this->dbkim->from("driver");
		$this->dbkim->where("driver_vehicle", $driver_vehicle);
		$this->dbkim->limit("1");
		$q = $this->dbkim->get();

		if ($q->num_rows > 0 ){
			$row = $q->row();
			$data = $row->driver_id;
			$data .= "-";
			$data .= $row->driver_name;
			return $data;
			$this->dbkim->close();
		}
		else {
		$this->dbkim->close();
			$data = "";
			$data .= "-";
			$data .= "";
			return $data;
		}

	}

	function getdestination($destination_vehicle) {

		$this->dbkim = $this->load->database('kim',true);
		$this->dbkim->select("*");
		$this->dbkim->from("destination");
		$this->dbkim->where("destination_vehicle",$destination_vehicle);
		$this->dbkim->limit("1");
		$q = $this->dbkim->get();

		if ($q->num_rows > 0 ){
			$row = $q->row();
			$data = $row->destination_id;
			$data .= "-";
			$data .= $row->destination_name1;
			return $data;
			$this->dbkim->close();
		}
		else {
		$this->dbkim->close();
			$data = "";
			$data .= "-";
			$data .= "";
			return $data;
		}

	}

	function get_geofence_alert_off($vehicle_device, $data_lokasi){


		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $vehicle_device);
        $qm = $this->db->get("vehicle");
        $rm = $qm->row();


		$json = json_decode($rm->vehicle_info);

		if ($rm->vehicle_info)
		{

			if(isset($json->vehicle_port) && ($json->vehicle_port == '50016'))
			{
				$this->dbgps = $this->load->database($this->config->item('dbkim_2'),true);

			}else if(isset($json->vehicle_port) && ($json->vehicle_port == '50017')){

				$this->dbgps = $this->load->database($this->config->item('dbkim_3'),true);

			}else if(isset($json->vehicle_port) && ($json->vehicle_port == '50018')){

				$this->dbgps = $this->load->database($this->config->item('dbkim_4'),true);

			}else if(isset($json->vehicle_port) && ($json->vehicle_port == '50019')){

				$this->dbgps = $this->load->database($this->config->item('dbkim_5'),true);

			}else if(isset($json->vehicle_port) && ($json->vehicle_port == '50020')){

				$this->dbgps = $this->load->database($this->config->item('dbkim_6'),true);

			}else{

				$this->dbgps = $this->load->database($this->config->item('dbkim'),true);
			}

		}

		$table = $this->config->item("external_gpstable");
		$tableinfo = $this->config->item("external_gpsinfotable");
		$this->dbgps->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
		$this->dbgps->join("webtracking_vehicle", "vehicle_device=CONCAT(gps_name,'@',gps_host)");
		$this->dbgps->where("vehicle_device", $vehicle_device);
		$this->dbgps->order_by("vehicle_no", "asc");
		$this->dbgps->order_by("gps_time", "desc");
		$this->dbgps->limit(1);
		$this->dbgps->from($table);
		$q = $this->dbgps->get();
		$rows = $q->row();
		//print_r($rows);exit;

		$pangkalan_1 = strtolower("PANGKALAN 1 RAYA NAROGONG ENG OFF");
		$pangkalan_2 = strtolower("PANGKALAN 2 RAYA NAROGONG ENG OFF");
		$pangkalan_1ab = strtolower("PANGKALAN 1AB RAYA NAROGONG ENG OFF");
		$pangkalan_4 = strtolower("PANGKALAN 4 RAYA NAROGONG ENG OFF");
		$pangkalan_6 = strtolower("PANGKALAN 6 RAYA NAROGONG ENG OFF");
		$pangkalan_9 = strtolower("PANGKALAN 9 RAYA NAROGONG ENG OFF");
		$kim_cek = strtolower("PT. KIM");

		$data_lokasi_lower = strtolower($data_lokasi);
		$dt_lokasi = explode(",",$data_lokasi_lower);
		$data_lokasi_cek = $dt_lokasi[0];

		//$data = $rows;

		if ($rows->gps_info_io_port == "0000000000")
		{

			if($pangkalan_1 == $data_lokasi_cek || $pangkalan_2 == $data_lokasi_cek || $pangkalan_1ab == $data_lokasi_cek || $pangkalan_4 == $data_lokasi_cek || $pangkalan_6 == $data_lokasi_cek || $pangkalan_9 == $data_lokasi_cek){

				//print_r($rows);exit;
				$data = "Vehicle No : ";
				$data .= $rows->vehicle_no;
				$data .= "  ||  Engine :  OFF";
				$data .= "  ||  Location : ";
				$data .= $data_lokasi;

				return $data;
				//return $rows;

			}

		}

	}



	//export table
	function export_table(){

		$this->DB2 = $this->load->database('kim', TRUE);
		if($this->sess->user_group == 52){
			$this->DB2->where("kode_company",52);
		}else if($this->sess->user_group == 53){
			$this->DB2->where("kode_company",53);
		}

		$this->DB2->where("duration >=",7200);  //2jam
		$this->DB2->where("duration <=",21600); //6jam
		$q = $this->DB2->get("table_time_gps");
		$rows = $q->result();


		if($q->num_rows() > 0)
		{

			if (count($rows>0))
			{

				for ($x=0;$x<count($rows);$x++)
				{
					$data[] = $rows[$x];
				}
			}


			/** PHPExcel */
			include 'class/PHPExcel.php';

			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';

			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("GPS Table Report Report");
			$objPHPExcel->getProperties()->setSubject("GPS Table Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("GPS Table Report Lacak-mobil.com");

			$j = 0;

			$objPHPExcel->getActiveSheet()->setTitle();
			// set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);

			$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', "GPS TABLE REPORT");
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);

			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'VEHICLE NO');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'GEOFENCE');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'SINCE AT >= 2 JAM');


			$top = 5;
			$objPHPExcel->getActiveSheet()->getStyle('A'.$top.':D'.$top)->getFont()->setBold(true);
			$k=1;

			//hasil
			for ($i=0;$i<count($data);$i++)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.($top+$k), $data[$i]->vehicle_no);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.($top+$k), $data[$i]->geofence);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.($top+$k), $data[$i]->tanggal);
				$k = $k + 1;
			}


			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);

			$objPHPExcel->getActiveSheet()->getStyle('A5:D'.($top+$k+2))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:D'.($top+$k+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:D'.($top+$k+2))->getAlignment()->setWrapText(true);

			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);
			$filecreatedname = "GPS_Table_".date('YmdHis') . ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/" . $this->config->item("folder_system"). "/media/report/" . $filecreatedname.'"}';

		}else{
			$output = '{"success":false,"errMsg":"Data empty..."}';
		}

		echo $output;
	}

	function lastinfofortelegram($device, $lasttime)
	{
		// $device = isset($_POST['device']) ? $_POST['device'] : "";    //dapet
		// $lasttime = isset($_POST['lasttime']) ? $_POST['lasttime'] : 0;
		print_r($device, $lasttime);exit();
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_device", $device);
		$this->db->join("user", "vehicle_user_id = user_id");
		//edited
		//$this->db->join("bank", "user_agent = bank_agent", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			echo json_encode(array("info"=>"", "vehicle"=>""));
			return;
		}

		$row = $q->row();

		// cek expire
		if ($row->vehicle_active_date2 && ($row->vehicle_active_date2 < date("Ymd")))
		{
			$row->vehicle_active_date2_fmt = inttodate($row->vehicle_active_date2);

			echo json_encode(array("info"=>"expired", "vehicle"=>$row));
			return;

		}

		$arr = explode("@", $device);

		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";

		$gtps = $this->config->item("vehicle_gtp");

		if (! in_array(strtoupper($row->vehicle_type), $gtps))
		{
			$row->status = "-";

			$taktif = dbintmaketime($row->vehicle_active_date, 0);

			$json = json_decode($row->vehicle_info);
			if (isset($json->sisapulsa))
			{
				if (strlen($json->masaaktif) == 6)
				{
					$taktif = dbintmaketime1($json->masaaktif, 0);
				}
				else
				{
					$taktif = dbintmaketime2($json->masaaktif, 0);
				}

				$row->pulse = sprintf("Rp %s", number_format($json->sisapulsa, 0, "", "."));
				$row->masaaktif = date("d/m/Y", $taktif);
			}
			else
			{
				$row->pulse = false;
			}

		}
		else
		{
			//Seleksi Database;
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

			if (! isset($table))
			{
			$table = $this->gpsmodel->getGPSTable($row->vehicle_type);
			$tableinfo = $this->gpsmodel->getGPSInfoTable($row->vehicle_type);
			}

			// ambil informasi di gps_info

			$this->db->order_by("gps_info_time", "DESC");
			$this->db->where("gps_info_device", $device);
			$q = $this->db->get($tableinfo, 1, 0);

			if ($q->num_rows() == 0)
			{
				$row->status = "-";
				$row->status1 = false;
				$row->status2 = false;
				$row->status3 = false;
				$row->pulse = "-";
			}
			else
			{
				$rowinfo = $q->row();

				$ioport = $rowinfo->gps_info_io_port;

				$row->status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
				$row->status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
				$row->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
				$row->status = $row->status2 || $row->status1 || $row->status3;

				$pulses = $this->config->item("vehicle_pulse");
				if (! in_array(strtoupper($row->vehicle_type), $pulses))
				{
					$json = json_decode($row->vehicle_info);
					if (isset($json->sisapulsa))
					{
						if (strlen($json->masaaktif) == 6)
						{
							$taktif = dbintmaketime1($json->masaaktif, 0);
						}
						else
						{
							$taktif = dbintmaketime2($json->masaaktif, 0);
						}

						$row->pulse = sprintf("Rp %s", number_format($json->sisapulsa, 0, "", "."));
						$row->masaaktif = date("d/m/Y", $taktif);
					}
					else
					{
						$row->pulse = false;
					}
				}
				else
				{
					//$rowinfo->gps_info_ad_input = "00B0742177";

					$pulsa = number_format(hexdec(substr($rowinfo->gps_info_ad_input, 0, 5)), 0, "", ".");
					$aktif = hexdec(substr($rowinfo->gps_info_ad_input, 5));

					$taktif = dbintmaketime1($aktif, 0);

					$row->pulse = sprintf("Rp %s", $pulsa);
					$row->masaaktif = date("d/m/Y", $taktif);
				}

				$fuels = $this->config->item("vehicle_fuel");
				if (! in_array(strtoupper($row->vehicle_type), $fuels))
				{
					$row->fuel = false;
				}
				else
				{
					$row->fuel = "-";
					if($rowinfo->gps_info_ad_input != ""){
						if($rowinfo->gps_info_ad_input != 'FFFFFF' || $rowinfo->gps_info_ad_input != '999999' || $rowinfo->gps_info_ad_input != 'YYYYYY'){
							$fuel_1 = hexdec(substr($rowinfo->gps_info_ad_input, 0, 4));
							$fuel_2 = (hexdec(substr($rowinfo->gps_info_ad_input, 0, 2))) * 0.1;

							$fuel = $fuel_1 + $fuel_2;

							$sql = "SELECT * FROM (
										(
											SELECT *
											FROM `webtracking_fuel`
											WHERE `fuel_tank_capacity` = ". $row->vehicle_fuel_capacity ."
											AND `fuel_led_resistance` >= ". $fuel ."
											ORDER BY fuel_led_resistance ASC
											LIMIT 1
										) UNION (
											SELECT *
											FROM `webtracking_fuel`
											WHERE `fuel_tank_capacity` = ". $row->vehicle_fuel_capacity ."
											AND `fuel_led_resistance` <= ". $fuel ."
											ORDER BY fuel_led_resistance DESC
											LIMIT 1
										)
									) tbldummy";

							$qfuel = $this->db->query($sql);
							if ($qfuel->num_rows() > 0){
   								$rfuel = $qfuel->result();

								if ($qfuel->num_rows() == 1){
									$row->blink = false;
									$row->fuel_scale = $rfuel[0]->fuel_gas_scale * 10;
									$row->fuel = $rfuel[0]->fuel_volume . "L";
								}else{
									$row->blink = true;
									$row->fuel_scale = $rfuel[1]->fuel_gas_scale * 10;
									$row->fuel = $rfuel[0]->fuel_volume . "L - " . $rfuel[1]->fuel_volume . "L";
								}
							}


						}
					}

				}
				$row->totalodometer = round(($rowinfo->gps_info_distance+$row->vehicle_odometer*1000)/1000);
				//$row->totalodometer = str_split($strodometer);
			}

		}

		$t = dbintmaketime($row->vehicle_active_date1, 0);
		$row->vehicle_active_date1_fmt = date("M, jS Y", $t);

		$t = dbintmaketime($row->vehicle_active_date2, 0);
		$row->vehicle_active_date2_fmt = date("M, jS Y", $t);

		$arr = explode("@", $device);

		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";


		$row->vehicle_device_name = $devices[0];
		$row->vehicle_device_host = $devices[1];

		$params["vehicle"] = $row;
		$row->gps = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $row->vehicle_type);



		if (! $row->gps)
		{
			echo json_encode(array("info"=>"", "vehicle"=>$row));
			return;
		}

		$delayresatrt = mktime() - $row->gps->gps_timestamp;
		$kdelayrestart = $this->config->item("restart_delay")*60;

		if (true)
		{
			$restart = $this->smsmodel->restart($row->vehicle_type, $row->vehicle_operator);
			$row->restartcommand = $restart;
		}
		else
		{
			$row->restartcommand = "";
		}

		if (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_T1")))
		{
			$row->checkpulsa = $this->smsmodel->checkpulse($row->vehicle_operator);
		}
		else
		{
			$row->checkpulsa = "";
		}


		//get geofence location
		$row->geofence_location = $this->getGeofence_location($row->gps->gps_longitude, $row->gps->gps_ew, $row->gps->gps_latitude, $row->gps->gps_ns, $row->vehicle_device);
        	//print_r($row->vehicle_device);
        	//$params["geofence_location"] = $row->geofence_location;
		$row->driver = $this->getdriver($row->vehicle_id);

		//test
		$row->destination = $this->getdestination($row->vehicle_id);
		//end
		$row->since_geofence_in = "";

		//geofence alert off
		//$data_lokasi = $row->gps->georeverse->display_name;


		//$row->geofence_alert_off = $this->get_geofence_alert_off($row->vehicle_device, $row->gps->georeverse->display_name);
		$this->dbkim = $this->load->database('kim', TRUE);

		if(isset($row->geofence_location) && $row->geofence_location != ""){

			$row->since_geofence_in = $this->getInGeofence($row->vehicle_device, $row->geofence_location);

		}

		if($row->geofence_location == "" || $row->geofence_location == null){

			unset($dt_update);
			$dt_update['tanggal'] = "";
			$dt_update['geofence'] = "";
			$dt_update['duration'] = 0;
			$this->dbkim->where('vehicle_device', $device);
			$this->dbkim->update("table_time_gps", $dt_update);

		}

		$params["driver"] = $row->driver;
		$params["destination"] = $row->destination;
		//$params["geofence_alert_off"] = $row->geofence_alert_off;
		$params["devices"] = $devices;
		$params["data"] = $row->gps;
		$info = $this->load->view("map/info", $params, TRUE);

		echo json_encode(array("info"=>$info, "vehicle"=>$row));
	}


}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
