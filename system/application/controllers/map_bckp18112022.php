<?php
include "base.php";

class Map extends Base {

	var $otherdb;
	function __construct()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("smsmodel");
		$this->load->model("m_poipoolmaster");
		$this->load->model("m_securityevidence");
		$this->load->model("m_dashboardview");
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

		if (!$this->config->item("app_tupperware"))
		{
			if ($this->sess->user_type == 2)
			{
			// security, make sure bahwa yang dibuka benar kendaraan punyanya

			if ($this->sess->user_company)
			{
				$this->db->where_in("vehicle_id", $vehicleids);
			}
			else
			if ($this->sess->user_login == "lacakmobil")
			{
				$this->db->where("vehicle_info LIKE '%\"vehicle_ws\":\"%'", null);
			}
			else
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}

			/* if ($this->config->item("app_tupperware"))
			{
				$this->db->or_where("vehicle_group",422);
			} */

			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			}
			else
			if ($this->sess->user_type == 3)
			{
			$this->db->where("user_agent", $this->sess->user_agent);
			$this->db->join("user", "vehicle_user_id = user_id");
			}
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
		if (isset($_POST['session']))
		{
                        $this->db->where("session_id", $_POST['session']);
                        $this->db->join("user", "user_id = session_user");
                        $q = $this->db->get("session");

                        if ($q->num_rows() == 0) return;

                        $this->sess = $q->row();
		}

		if (! $this->sess)
		{
			echo json_encode(array("info"=>"", "vehicle"=>""));
			return;
		}

		$device        = isset($_POST['device']) ? $_POST['device']:     "";


		if (strpos($device, '@') !== false) {
			$device   = isset($_POST['device']) ? $_POST['device']:     "";
		}else {
			$insearch      = isset($_POST['insearch']) ? $_POST['insearch']: "";

			if ($insearch == 1) {
				// $sikon = 1;
				$mastervehicle = $this->m_poipoolmaster->searchmasterdata("webtracking_vehicle", $insearch);
				$device        = $mastervehicle[0]['vehicle_device'];
			}else {
				// $sikon = 2;
				$this->db->where("vehicle_status <>", 3);
				$this->db->where("vehicle_device", $device);
				$query       = $this->db->get("vehicle");
				$result      = $query->result_array();
				$device      = $result[0]['vehicle_device'];
			}
		}


		$lasttime = isset($_POST['lasttime']) ? $_POST['lasttime']: 0;
		// print_r($sikon);exit();

		if ($this->sess->user_type == 2)
		{
			$vehicleids = $this->vehiclemodel->getVehicleIds();
		}

		if (!$this->config->item("app_tupperware"))
		{
			switch($this->sess->user_type)
			{
				case 2:
					if ($this->sess->user_company)
					{
						$this->db->where_in("vehicle_id", $vehicleids);
					}
					else
					if ($this->sess->user_login == "lacakmobil")
					{
						$this->db->where("vehicle_info LIKE '%\"vehicle_ws\":\"%'", null);
					}
					else
					{
						$this->db->or_where("vehicle_user_id", $this->sess->user_id);
					}
				break;
				case 3:
					$this->db->where("user_agent", $this->sess->user_agent);
				break;
			}
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $device);
		$this->db->join("user", "vehicle_user_id = user_id");
		$this->db->join("bank", "user_agent = bank_agent", "left outer");
		$q = $this->db->get("vehicle");
		// print_r($q->result_array());exit();

		// echo "<pre>";
		// var_dump($q->result_array());die();
		// echo "<pre>";

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
			// print_r("asd");exit();
			$json = json_decode($row->vehicle_info);

				echo json_encode(array("info"=>"expired", "vehicle"=>$row));
				return;
		}

		$arr = explode("@", $device);

		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";

		$row->gps = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $row->vehicle_type);
		if ($this->gpsmodel->fromsocket)
		{
			$datainfo = $this->gpsmodel->datainfo;
			$fromsocket = $this->gpsmodel->fromsocket;
		}


		$gtps = $this->config->item("vehicle_gtp");

		if (! in_array(strtoupper($row->vehicle_type), $gtps) && $row->vehicle_type != "TK309PTO" && $row->vehicle_type != "GT06PTO")
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

				if (date("Y", $taktif) < 2000)
				{
					$row->pulse = false;
				}
				else
				{
					$row->pulse = sprintf("Rp %s", number_format($json->sisapulsa, 0, "", "."));
					$row->masaaktif = date("d/m/Y", $taktif);
				}
			}
			else
			{
				$row->pulse = false;
			}

		}
		else
		{
			if (isset($row->gps) && $row->gps && date("Ymd", $row->gps->gps_timestamp) >= date("Ymd"))
			{
				if (! isset($fromsocket))
				{
					$tables = $this->gpsmodel->getTable($row);
					$this->db = $this->load->database($tables["dbname"], TRUE);
				}

			}
			else
			if (! isset($fromsocket))
			{
				$tables['info'] = sprintf("%s@%s_info", strtolower($devices[0]), strtolower($devices[1]));
				$istbl_history = $this->config->item("dbhistory_default");
				if($this->config->item("is_dbhistory") == 1)
				{
					$istbl_history = $row->vehicle_dbhistory_name;
				}
				$this->db = $this->load->database($istbl_history, TRUE);
			}

			// ambil informasi di gps_info

			if (! isset($datainfo))
			{
				$this->db->order_by("gps_info_time", "DESC");
				$this->db->where("gps_info_device", $device);
				$q = $this->db->get($tables['info'], 1, 0);
				$totalinfo = $q->num_rows();
				if ($totalinfo)
				{
					$rowinfo = $q->row();
				}
			}
			else
			{
				$rowinfo = $datainfo;
				$totalinfo = 1;
			}

			if ($totalinfo == 0)
			{
				$row->status = "-";
				$row->status1 = false;
				$row->status2 = false;
				$row->status3 = false;
				$row->pulse = "-";
			}
			else
			{
				$ioport = $rowinfo->gps_info_io_port;

				$row->status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
				$row->status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
				if(isset($devices[1]) && ($devices[1] == "GT06" || $devices[1] == "A13" || $devices[1] == "TK309" || $devices[1] == "TK309PTO" || $devices[1] == "GT06PTO"))
				{
					if(isset($row->gps->gps_speed_fmt) && $row->gps->gps_speed_fmt > 0)
					{
						$row->status1 = true;
					}
					else
					{
						$row->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
					}
				}
				else
				{
					$row->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
				}
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

						if (date("Y", $taktif) < 2000)
						{
							$row->pulse = false;
						}
						else
						{
							$row->pulse = sprintf("Rp %s", number_format($json->sisapulsa, 0, "", "."));
							$row->masaaktif = date("d/m/Y", $taktif);
						}
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

					if (date("Y", $taktif) < 2000)
					{
						$row->pulse = false;
					}
					else
					{
						$row->pulse = sprintf("Rp %s", $pulsa);
						$row->masaaktif = date("d/m/Y", $taktif);
					}
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
							//print_r($fuel);exit;
							//Deteksi Fuel Capacity

							$this->db = $this->load->database("default", TRUE);

							if ($row->vehicle_fuel_capacity != 0 && $row->vehicle_fuel_capacity == 300)
							{

								$sql = "SELECT * FROM (
										(
											SELECT *
											FROM `webtracking_fuel`
											WHERE `fuel_tank_capacity` = ". $row->vehicle_fuel_capacity ."
											AND `fuel_led_resistance` <= ". $fuel ."
											ORDER BY fuel_led_resistance DESC
											LIMIT 1
										)
									) tbldummy";
							}
							else
							{
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
							}
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



			//since at ssi

			/*if($this->sess->user_id == 1933){

				$dev = explode("@", $device);

				$json = json_decode($row->vehicle_info);
				if ($row->vehicle_info)
				{
					if (isset($json->vehicle_ip) && isset($json->vehicle_port))
					{
						$databases = $this->config->item('databases');

						if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
						{
							$database = $databases[$json->vehicle_ip][$json->vehicle_port];
							$tablegps = $this->config->item("external_gpstable");
							$tablegpsinfo = $this->config->item("external_gpsinfotable");
							$tablegpshist = $this->config->item("external_gpstable_history");
							$this->db = $this->load->database($database, TRUE);
						}
					}

					if(isset($json->vehicle_ws))
					{
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $row->vehicle_dbhistory_name;
						}
						$tablegps = strtolower($dev[0]."@".$dev[1]."_gps");
						$tablegpsinfo = strtolower($dev[0]."@".$dev[1]."_info");
						$this->db = $this->load->database($istbl_history, TRUE);
					}

					if(! isset($tablegps))
					{
						$table_hist = $this->config->item("table_hist");
						$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
						$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
					}

				}

				$this->db->select("gps_info_io_port, gps_info_time, gps_info_device, gps_speed");
				$this->db->order_by("gps_info_time", "asc");
				$this->db->where("gps_info_device", $device);
				$this->db->join($tablegps, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$q = $this->db->get($tablegpsinfo);
				$rowstartoff = $q->result();


				for ($i=0;$i<count($rowstartoff);$i++)
				{
					if ($rowstartoff[$i]->gps_info_io_port == "0000000000")
					{
						if (isset($rowstartoff[$i-1]->gps_info_io_port))
						{
							if ($rowstartoff[$i-1]->gps_info_io_port == "0000000000"){}
							else if ($rowstartoff[$i-1]->gps_info_io_port == "0000100000")
							{
								$startoff_ssi = $rowstartoff[$i]->gps_info_time;
							}
							else{}
						}
					}

					if($rowstartoff[$i]->gps_info_io_port == "0000100000" && $rowstartoff[$i]->gps_speed == 0)
					{
						if (isset($rowstartoff[$i-1]->gps_info_io_port))
						{
							if ($rowstartoff[$i-1]->gps_info_io_port == "0000100000" && $rowstartoff[$i]->gps_speed == 0){}
							else if ($rowstartoff[$i-1]->gps_info_io_port == "0000000000")
							{
								$starton_ssi = $rowstartoff[$i]->gps_info_time;
							}
							else{}
						}
					}
				}


				if(isset($starton_ssi))
				{
					$ontime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($starton_ssi)));
					$timenow = date("Y-m-d H:i:s");
					$oncalculation = get_time_difference($ontime, $timenow);

					$showon_ssi = " ".$ontime;
					$showdurationon_ssi = " "." ";
					if($oncalculation[0]!=0){
						$showdurationon_ssi .= $oncalculation[0] ." Day ";
					}
					if($oncalculation[1]!=0){
						$showdurationon_ssi .= $oncalculation[1] ." Hour ";
					}
					if($oncalculation[2]!=0){
						$showdurationon_ssi .= $oncalculation[2] ." Min ";
					}

					if($showdurationon_ssi == " "." "){
						$showdurationon_ssi .= "0 Min ";
					}

				}

				if(isset($startoff_ssi))
				{
					$offtime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($startoff_ssi)));
					$timenow = date("Y-m-d H:i:s");
					$offcalculation = get_time_difference($offtime, $timenow);
					$showoff_ssi = " ".$offtime;
					$showduration_ssi = " "." ";
					if($offcalculation[0]!=0){
						$showduration_ssi .= $offcalculation[0] ." Day ";
					}
					if($offcalculation[1]!=0){
						$showduration_ssi .= $offcalculation[1] ." Hour ";
					}
					if($offcalculation[2]!=0){
						$showduration_ssi .= $offcalculation[2] ." Min ";
					}

					if($showduration_ssi == " "." "){
						$showduration_ssi .= "0 Min ";
					}

				}

			} */

			//since at iwatani
			if($this->sess->user_id == 1837){

				$this->db->order_by("gps_info_time", "asc");
				$this->db->where("gps_info_device", $device);
				$q = $this->db->get($tables['info']);
				$rowstartoff = $q->result();
				//print_r($rowstartoff);exit;

				for ($i=0;$i<count($rowstartoff);$i++)
				{
					if ($rowstartoff[$i]->gps_info_io_port == "0000000000")
					{
						if (isset($rowstartoff[$i-1]->gps_info_io_port))
						{
							if ($rowstartoff[$i-1]->gps_info_io_port == "0000000000"){}
							else if ($rowstartoff[$i-1]->gps_info_io_port == "0000100000")
							{
								$startoff_iwatani = $rowstartoff[$i]->gps_info_time;
							}
							else{}
						}
					}
				}

				if (isset($startoff_iwatani))
				{
					$offtime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($startoff_iwatani)));
					$timenow = date("Y-m-d H:i:s");
					$offcalculation = get_time_difference($offtime, $timenow);
					//print_r($offcalculation);exit;
					$showoff_iwatani = " ".$offtime;
					$showduration_iwatani = " "." ";
					if($offcalculation[0]!=0){
						$showduration_iwatani .= $offcalculation[0] ." Day ";
					}
					if($offcalculation[1]!=0){
						$showduration_iwatani .= $offcalculation[1] ." Hour ";
					}
					if($offcalculation[2]!=0){
						$showduration_iwatani .= $offcalculation[2] ." Min ";
					}

					if($showduration_iwatani == " "." "){
						$showduration_iwatani .= "0 Min ";
					}
				}

				//print_r($showduration);exit;

			}
			//end

				/*
				//Get Start Off
				$this->db->order_by("gps_info_time", "asc");
				$this->db->where("gps_info_device", $device);
				$q = $this->db->get($tables['info']);

				$rowstartoff = $q->result();
				//print_r($rowstartoff);exit;

				for ($i=0;$i<count($rowstartoff);$i++)
				{
					if ($rowstartoff[$i]->gps_info_io_port == "0000000000")
					{
						if (isset($rowstartoff[$i-1]->gps_info_io_port))
						{
							if ($rowstartoff[$i-1]->gps_info_io_port == "0000000000"){}
							else if ($rowstartoff[$i-1]->gps_info_io_port == "0000100000")
							{
								$startoff = $rowstartoff[$i]->gps_info_time;
							}
							else{}
						}
					}

					if ($rowstartoff[$i]->gps_info_io_port == "0000100000")
					{
						if (isset($rowstartoff[$i-1]->gps_info_io_port))
						{
							if ($rowstartoff[$i-1]->gps_info_io_port == "0000100000"){}
							else if ($rowstartoff[$i-1]->gps_info_io_port == "0000000000")
							{
								$starton = $rowstartoff[$i]->gps_info_time;
							}
							else{}
						}
					}
				}

				if (isset($startoff))
				{
					$offtime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($startoff)));
					$timenow = date("Y-m-d H:i:s");
					$offcalculation = get_time_difference($offtime, $timenow);

					$showoff = " ".$offtime;
					$showduration = " "." ";
					if($offcalculation[0]!=0){
						$showduration .= $offcalculation[0] ." Day ";
					}
					if($offcalculation[1]!=0){
						$showduration .= $offcalculation[1] ." Hour ";
					}
					if($offcalculation[2]!=0){
						$showduration .= $offcalculation[2] ." Min ";
					}

					if($showduration == " "." "){
						$showduration .= "0 Min ";
					}
				}

				if (isset($starton))
				{
					$ontime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($starton)));
					$timenow = date("Y-m-d H:i:s");
					$oncalculation = get_time_difference($ontime, $timenow);

					$showon = " ".$ontime;
					$showdurationon = " "." ";
					if($oncalculation[0]!=0){
						$showdurationon .= $oncalculation[0] ." Day ";
					}
					if($oncalculation[1]!=0){
						$showdurationon .= $oncalculation[1] ." Hour ";
					}
					if($oncalculation[2]!=0){
						$showdurationon .= $oncalculation[2] ." Min ";
					}

					if($showdurationon == " "." "){
						$showdurationon .= "0 Min ";
					}
				}
				*/
		}

		// echo "<pre>";
		// var_dump($tables["dbname"]);die();
		// echo "<pre>";


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
		//$row->gps = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $row->vehicle_type);

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

		//khusus user powerblock (pengurus)
		if($this->sess->user_company == "1724" || $this->sess->user_company == "1723"){
			$row->geofence_location = $this->getGeofence_location_pbi($row->gps->gps_longitude, $row->gps->gps_ew, $row->gps->gps_latitude, $row->gps->gps_ns, $row->vehicle_device);
		}else{

			if (!in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_others"))){
				$row->geofence_location = $this->getGeofence_location($row->gps->gps_longitude, $row->gps->gps_ew, $row->gps->gps_latitude, $row->gps->gps_ns, $row->vehicle_user_id);
			}
			else
			{
				$row->geofence_location = $this->getGeofence_location_others($row->gps->gps_longitude_real, $row->gps->gps_latitude_real, $row->vehicle_user_id);
			}
		}

		//APP CO Reksa
		$app_co = $this->config->item("app_co");
		if ($app_co && $app_co == 1)
		{
			$row->destination = $this->getdestination($row->vehicle_device);
		}
		//end

		//RENTCAR
		$apprentcar = $this->config->item("rentcar_app");
		$appdosj = $this->config->item("app_dosj");
		$appfan = $this->config->item("fan_app");

		//untuk semua transporter company terdaftar
		$app_dosj_all = $this->config->item("app_dosj_all");

		//Balrich
		$appvehicle_detail = $this->config->item("vehicle_detail_app");

		if ($appvehicle_detail && $appvehicle_detail == 1)
		{
			$row->v_detail = $this->getvehicledetail($row->vehicle_device);
		}

		if ($row->vehicle_type == "T5DOOR" || $row->vehicle_type == "T5FAN" || $row->vehicle_type == "T5PTO")
		{
			$row->fan = $this->getFanStatus($row->gps->gps_msg_ori);
		}

		if($row->vehicle_type == "T5SUHU")
		{
			$row->suhu = $this->getSuhu($row->gps->gps_msg_ori);
		}

		if ($apprentcar && $apprentcar == 1)
		{
			$row->customer = $this->getcustomer($row->vehicle_no);
			$x = explode("," ,$row->customer);
			$y = $x[0];
			$row->tenant = $this->get_tenant($y);
			$params["tenant"] = $row->tenant;
			$params["customer"]= $row->customer;
			//print_r ($row->tenant);exit;
		}
		//END RENTCAR

		if (isset($appdosj) && ($appdosj == 1))
		{
			//Get Driver by schedule DO/SJ
			$row->driver = $this->getdriver_dosj($row->vehicle_device);
			$row->dosj = $this->get_dosj($row->vehicle_device);
		}
		else
		{
			$row->driver = $this->getdriver($row->vehicle_id);

		}

		//get driver rf id
		$apprfid = $this->config->item("app_driver_rdif");
		if($this->sess->user_id == 3986){
			$apprfid = 1;
			$row->driver_idcard = $this->getdriver_idcard($row->vehicle_device);
		}

		//dosj untuk semua transporter company terdaftar
		if (isset($app_dosj_all) && ($app_dosj_all == 1) && in_array(strtoupper($this->sess->user_company), $this->config->item("company_view_dosj")))
		{

			//Get Driver by schedule DO/SJ
			$row->driver = $this->getdriver_dosj_all($row->vehicle_device);
			$row->dosj = $this->get_dosj_all($row->vehicle_device);
			//$row->customer_groups = $this->getcustomer_dosj_all($row->vehicle_device);
		}
		else
		{
			$row->driver = $this->getdriver($row->vehicle_id);

		}


		//Transporter Powerblock
		$app_powerblock = $this->config->item("app_powerblock");
		if(isset($app_powerblock) && $app_powerblock == 1){

			//$row->id_so = $this->get_so($row->vehicle_device);
			$row->no_sj = $this->get_new_sj($row->vehicle_device);
			$row->driver_sj = $this->get_new_driver($row->vehicle_device);

			/*print_r($row->id_sj);
			print_r($row->driver);
			exit();*/

		}

		// DATA PROJECT
		if (in_array(strtoupper($this->sess->user_id), $this->config->item("user_view_pto"))) {
				$row->dataproject = $this->getdataproject($row->vehicle_device);
				// print_r("masuk");exit();
		}

		$app_tag = $this->config->item("app_tag");
		if(isset($app_tag) && $app_tag == 1){
			$row->off_autocheck = $this->get_off_autocheck($row->vehicle_device);
		}

		//Transporter Tupperware
		$app_tupperware = $this->config->item("app_tupperware");
		if ($this->sess->user_trans_tupper == 1 || (isset($app_tupperware) && $app_tupperware == 1))
		{
			$row->id_booking = $this->get_id_booking($row->vehicle_device);

			if (isset($row->id_booking) && $row->id_booking != "")
			{
				$row->noso = $this->get_noso($row->vehicle_device);
				//$row->slcars = $this->get_slcars($row->id_booking);
			}
			if (isset($row->id_booking) && $row->id_booking != "")
			{
				$row->nodr = $this->get_nodr($row->vehicle_device);
			}
			if (isset($row->noso) && $row->noso != "")
			{
				if (isset($row->nodr) && $row->nodr != "")
				{
					$row->dbcode = $this->get_dbcode($row->vehicle_device);
				}
			}
		}

        $row->customer_groups = $this->getcustomer_groups($row->vehicle_group);
		$row->since_geofence_in = "";

		if ($row->vehicle_company != 0)
		{
			$row->company = $this->getCompany($row->vehicle_company);
			//print_r($row->company);exit;
		}
		else
		{
			$row->company = 0;
		}

		//INFO : Since At Geofence
		$app_sinceat = $this->config->item("since_at_geofence");
		if ($app_sinceat && $app_sinceat==1)
		{
			if(isset($row->geofence_location) && $row->geofence_location != "")
			{
				$row->since_geofence_in = $this->getInGeofence($row->vehicle_device, $row->geofence_location);
			}
		}


		//since at iwatani
		if($this->sess->user_id == 1837){

			if (isset($showoff_iwatani))
			{
				$row->startoff_iwatani = $showoff_iwatani;
				$row->offduration_iwatani = $showduration_iwatani;
			}

		}

		//since at ssi
		/*if($this->sess->user_id == 1933){

			if (isset($showoff_ssi))
			{
				$row->startoff_ssi = $showoff_ssi;
				$row->offduration_ssi = $showduration_ssi;
			}else if(isset($showon_ssi)){
				$row->starton_ssi = $showon_ssi;
				$row->onduration_ssi = $showdurationon_ssi;
			}
		} */

		//tcontinent
		$apptcontinent = $this->config->item("tcontinent_app");
		if ($apptcontinent && $apptcontinent == 1)
		{
			$row->jobfile = $this->getjobfile($row->vehicle_id);

			$params["jobfile"]= $row->jobfile;
			//print_r($row->jobfile);exit;
		}
		//END tcontinent

		// Khusus BANGUN CILEGON  ID
		if($this->sess->user_id == 1540){

			$row->muatan = $this->getmuatan($row->vehicle_id);

			$params["muatan"]= $row->muatan;

		}
		//END BANGUN CILEGON

		//SSI
		$appssi = $this->config->item("ssi_app");
		if ($appssi && $appssi == 1)
		{
			$row->team = $this->getteam($row->vehicle_id);
			$params["team"]= $row->team;

			//comment
			$row->comment = $this->getcomment($row->vehicle_id);
			$params["comment"]= $row->comment;
		}
		//END SSI

		//comment app
		$appcomment = $this->config->item("comment_app");
		if ($appcomment && $appcomment == 1)
		{
			$row->comment = $this->getcomment($row->vehicle_id);
			$params["comment"]= $row->comment;
		}

		$app = $this->config->item("alatberat_app");
		if ($app && $app==1)
		{
			$row->hourmeter = $this->getHourmeter($row->gps->gps_msg_ori);
		}

		if (isset($showoff))
		{
			$row->startoff = $showoff;
			$row->offduration = $showduration;
		}

		if (isset($showon))
		{
			$row->starton = $showon;
			$row->onduration = $showdurationon;
		}

		if($row->vehicle_type == "TK309PTO" || $row->vehicle_type == "GT06PTO")
		{
			$row->traccarPTO = $this->getTraccarPTO($rowinfo->gps_info_io_port);
		}

		if($row->vehicle_type == "TK315DOOR")
		{
			$row->fan = $this->getDoorStatus($row->gps->gps_msg_ori);
		}

		if($row->vehicle_type == "X3_DOOR" || $row->vehicle_type == "TK315DOOR_NEW" || $row->vehicle_type == "X3_PTO" || $row->vehicle_type == "TK510DOOR" || $row->vehicle_type == "TK510CAMDOOR" || $row->vehicle_type == "TK315FAN" || $row->vehicle_type == "GT08SDOOR" ||
		   $row->vehicle_type == "GT08DOOR" || $row->vehicle_type == "GT08CAMDOOR" || $row->vehicle_type == "GT08SPTO" || $row->vehicle_type == "GT08PTO")
		{
			$row->fan = $row->gps->gps_cs;
		}

		if($row->vehicle_type == "TJAM")
		{
			$parse = explode(",",$row->gps->gps_msg_ori);
			if(isset($parse[13]))
			{
				$row->battery = $parse[13];
			}
		}

		if($row->vehicle_type == "AT5")
		{
			$row->battery = $this->getPersen($row->gps->gps_cs);
		}

		if($row->vehicle_type == "GT08SRFID" || $row->vehicle_type == "GT08SRFIDDOOR" || $row->vehicle_type == "GT08SRFIDPTO")
		{
			$row->driver_rfid = $this->getdriver_rfid($row->vehicle_device,$row->vehicle_dbname_live);
		}


		if ($row->vehicle_user_id == 389 && $row->vehicle_type != "A13") //khusus farrasindo
		{
			$row->cutpower = $this->getCutPower($row->vehicle_id,$row->gps->gps_time);
			$params["cutpower"] = $row->cutpower;
		}

		//app workhour (engine on dan off
		if (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_workhour"))){
			$row->workhour = $this->getWorkhour($row->gps->gps_workhour);
		}else{
			$row->workhour = $this->getWorkhour(0);
		}

		if (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_cam")))
		{
			$row->snap = $this->getLastSnap($row->vehicle_device,$row->vehicle_dbname_live);
			$exp = explode("|", $row->snap);
			$row->snapimage = $exp[0];
			$row->snaptime = date("d F Y H:i:s", strtotime($exp[1]));
			// echo "<pre>";
			// var_dump($row->snaptime);die();
			// echo "<pre>";
		}

		// GET DATA VEHICLE MV03
		$this->db->select("vehicle_mv03, vehicle_device");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $device);
		$query       = $this->db->get("vehicle");
		$result      = $query->result_array();
		$vehiclemv03 = $result[0]['vehicle_mv03'];
		$vdevice     = explode("@",$result[0]['vehicle_device']);

		// DRIVER DETAIL START
		if ($vehiclemv03 != "0000") {
			// METODE BARU
			$this->dbwebts = $this->load->database("webtracking_ts",true);
			$this->dbwebts->select("*");
			$this->dbwebts->where("change_imei", $vehiclemv03);
			$this->dbwebts->where("change_driver_flag", 0);
			$this->dbwebts->order_by("change_driver_time", "DESC");
			$this->dbwebts->limit(1);
			$q               = $this->dbwebts->get("ts_driver_change");
			$resultnewmethod = $q->result();

			$changedrivertime                  = $resultnewmethod[0]->change_driver_time;

			$report     = "alarm_";
			$report_sum = "summary_";
			$m1         = date("F", strtotime($changedrivertime));
			$year       = date("Y", strtotime($changedrivertime));
			switch ($m1)
			{
				case "January":
	            $dbtable = $report."januari_".$year;
				$dbtable_sum = $report_sum."januari_".$year;
				break;
				case "February":
	            $dbtable = $report."februari_".$year;
				$dbtable_sum = $report_sum."februari_".$year;
				break;
				case "March":
	            $dbtable = $report."maret_".$year;
				$dbtable_sum = $report_sum."maret_".$year;
				break;
				case "April":
	            $dbtable = $report."april_".$year;
				$dbtable_sum = $report_sum."april_".$year;
				break;
				case "May":
	            $dbtable = $report."mei_".$year;
				$dbtable_sum = $report_sum."mei_".$year;
				break;
				case "June":
	            $dbtable = $report."juni_".$year;
				$dbtable_sum = $report_sum."juni_".$year;
				break;
				case "July":
	            $dbtable = $report."juli_".$year;
				$dbtable_sum = $report_sum."juli_".$year;
				break;
				case "August":
	            $dbtable = $report."agustus_".$year;
				$dbtable_sum = $report_sum."agustus_".$year;
				break;
				case "September":
	            $dbtable = $report."september_".$year;
				$dbtable_sum = $report_sum."september_".$year;
				break;
				case "October":
	            $dbtable = $report."oktober_".$year;
				$dbtable_sum = $report_sum."oktober_".$year;
				break;
				case "November":
	            $dbtable = $report."november_".$year;
				$dbtable_sum = $report_sum."november_".$year;
				break;
				case "December":
	            $dbtable = $report."desember_".$year;
				$dbtable_sum = $report_sum."desember_".$year;
				break;
			}

			$iddriverchange 									 = 631; // ID DRIVER CHANGE LEVEL 2
			$getdriverchangeinsecurityevidence = $this->fromsecurityevidence($dbtable, $vehiclemv03, $iddriverchange);
			$datefromsecurityevidence          = date("Y-m-d H:i:s", strtotime($getdriverchangeinsecurityevidence[0]['alarm_report_start_time'])+60*60);

			// $vehiclemv03.'||'.$changedrivertime.'||'.$datefromsecurityevidence.'-before : '.$getdriverchangeinsecurityevidence[0]['alarm_report_start_time']

			// echo "<pre>";
			// var_dump($dbtable.'||'.$vehiclemv03.'||'.$iddriverchange);die();
			// echo "<pre>";

			// NONE DRIVER
			if ($datefromsecurityevidence > $changedrivertime) {
				$row->driverimage = 0;
				$row->driver = ""; // nanti ilangin kalo udah live ini khusus evalia
				$params["driver"] = $row->driver;
			}else {
				$changedriverid  = $resultnewmethod[0]->change_driver_id;
				$getdriverdetail = $this->getdriverdetailnewmethod($changedriverid);
					if (sizeof($getdriverdetail) > 0) {
						$getdriverimage  = $this->getdriverdetail($getdriverdetail[0]->driver_id);
						if (isset($getdriverimage[0]->driver_image_file_name)) {
							$row->driverimage = $getdriverimage[0]->driver_image_raw_name.$getdriverimage[0]->driver_image_file_ext;
						}else {
							$row->driverimage = 0;
						}
						$row->driver = $getdriverdetail[0]->driver_name; // nanti ilangin kalo udah live ini khusus evalia
						$params["driver"] = $row->driver;
					}else {
						$row->driverimage = 0;
						$row->driver = ""; // nanti ilangin kalo udah live ini khusus evalia
						$params["driver"] = $row->driver;
					}
			}
		}else {
			// METODE LAMA
			$driverexplode  = explode("-", $row->driver);
			$iddriver       = $driverexplode[0];
			$getdriverimage = $this->getdriverdetail($iddriver);

			if (isset($getdriverimage[0]->driver_image_file_name)) {
				$row->driverimage = $getdriverimage[0]->driver_image_raw_name.$getdriverimage[0]->driver_image_file_ext;
			}else {
				$row->driverimage = 0;
			}
			$params["driver"] = $row->driver;
		}
		// echo "<pre>";
		// var_dump($row->driver);die();
		// echo "<pre>";
		// DRIVER DETAIL END

		$params["company"] = $row->company;
		//APP CO REKSA
		if ($app_co && $app_co == 1)
		{
			$params["destination"] = $row->destination;
		}
		//end
		$params["devices"] = $devices;
		$params["data"] = $row->gps;

		// GET LAST ROAD BY SESSION DB LIVE
		$this->dblive = $this->load->database($this->sess->user_dblive,true);
		$this->dblive->select("vehicle_autocheck");
		$this->dblive->where("gps_name", $vdevice[0]);
		$q_live       = $this->dblive->get('webtracking_gps');
		$row_gps      = $q_live->row();
		$rowgpsdecode = json_decode($row_gps->vehicle_autocheck);

		if (isset($rowgpsdecode->auto_last_road)) {
			$row->jalur   = $rowgpsdecode->auto_last_road;
			$row->ritase   = $rowgpsdecode->auto_last_ritase;
		}else {
			$row->jalur   = "-";
		}

		// echo "<pre>";
		// var_dump($jalur);die();
		// echo "<pre>";

		if ($vehiclemv03 != "0000") {
			$url       = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=".$vehiclemv03."&jsession=";
			$username  = "IND.LacakMobil";
			$password  = "000000";
			// $url       = "http://47.91.108.9:8080/808gps/open/player/RealPlayVideo.html?account=".$username."&password=".$password."&PlateNum=".$devicefix."&lang=en";

			$getthissession  = $this->m_securityevidence->getsession();
			$urlfix          = $url.$getthissession[0]['sess_value'];
			// echo "<pre>";
			// var_dump($result);die();
			// echo "<pre>";

			// GET LOGIN DENGAN SESSION LAMA
			$loginlama       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
				if ($loginlama) {
					$loginlamadecode = json_decode($loginlama);
					if (!$loginlamadecode) {
						if ($loginlamadecode->message == "Session does not exist!") {
							$loginbaru       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
							$loginbarudecode = json_decode($loginbaru);
							$fixsession      = $loginbarudecode->jsession;
						}
					}else {
						$fixsession      = $getthissession[0]['sess_value'];
					}
				}

				// GET DEVICE STATUS START
				$urlcekdevicestatus = "http://47.91.108.9:8080/StandardApiAction_getDeviceOlStatus.action?jsession=".$fixsession."&devIdno=".$vehiclemv03;
				$cekstatus          = file_get_contents($urlcekdevicestatus);
				$loginbarudecode    = json_decode($cekstatus);
					if ($loginbarudecode->result == 0) {
						$row->devicestatus  = "";
					}else {
						$statusfixnya       = $loginbarudecode->onlines[0]->online;
						$row->devicestatus  = $statusfixnya;
					}

				// echo "<pre>";
				// var_dump($loginbarudecode);die();
				// echo "<pre>";
		}

		// echo "<pre>";
		// var_dump($row);die();
		// echo "<pre>";

		$info = $this->load->view("map/info", $params, TRUE);

		echo json_encode(array("info"=>$info, "vehicle"=>$row));
	}

	function lastinfoall(){
		if (isset($_POST['session']))
		{
      $this->db->where("session_id", $_POST['session']);
      $this->db->join("user", "user_id = session_user");
      $q = $this->db->get("session");

      if ($q->num_rows() == 0) return;

      $this->sess = $q->row();
		}

		if (! $this->sess)
		{
			echo json_encode(array("info"=>"", "vehicle"=>""));
			return;
		}

		// echo "<pre>";
		// var_dump("masuk");die();
		// echo "<pre>";

		$datavehicleall = $this->getAllVehicle();
		$datafix        = array();
		$dataexpired    = array();

		for ($i=0; $i < sizeof($datavehicleall); $i++) {
			$arr = explode("@", $datavehicleall[$i]->vehicle_device);
			$devices[0] = (count($arr) > 0) ? $arr[0] : "";
			$devices[1] = (count($arr) > 1) ? $arr[1] : "";
			// echo "<pre>";
			// var_dump($datavehicleall[$i]->vehicle_dbname_live);die();
			// echo "<pre>";
			$datafromdblive = $this->gpsmodel->getfromDBLIVE($datavehicleall[$i]->vehicle_dbname_live, $devices[0], $devices[1]);
				if ($datafromdblive) {
					array_push($datafix, array(
						"gps_latitude_real"  => $datafromdblive[0]->gps_latitude_real,
						"gps_longitude_real" => $datafromdblive[0]->gps_longitude_real
					));
				}
		}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "data" => $datafix));
	}

	function lastinfoall_new(){
		echo "<pre>";
		var_dump("masuk");die();
		echo "<pre>";

		if (isset($_POST['session']))
		{
      $this->db->where("session_id", $_POST['session']);
      $this->db->join("user", "user_id = session_user");
      $q = $this->db->get("session");

      if ($q->num_rows() == 0) return;

      $this->sess = $q->row();
		}

		echo "<pre>";
		var_dump("masuk");die();
		echo "<pre>";

		if (! $this->sess)
		{
			echo json_encode(array("info"=>"", "vehicle"=>""));
			return;
		}

		$companyid       = $this->input->post('companyid');
		$valueMapsOption = $this->input->post('valuemapsoption');

		$user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
		$user_parent         = $this->sess->user_parent;
		$user_privilege_code = $this->sess->user_id_role;
		$user_id             = $this->sess->user_id;

		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_name","asc");
		if ($companyid != 0) {
			$this->db->where("vehicle_company", $companyid);
		}else {
			if($user_privilege_code == 0){
				$this->db->where("vehicle_user_id", $user_id);
			}else if($user_privilege_code == 1){
				$this->db->where("vehicle_user_id", $user_parent);
			}else if($user_privilege_code == 2){
				$this->db->where("vehicle_user_id", $user_parent);
			}else if($user_privilege_code == 3){
				$this->db->where("vehicle_user_id", $user_parent);
			}else if($user_privilege_code == 4){
				$this->db->where("vehicle_user_id", $user_parent);
			}else if($user_privilege_code == 5){
				$this->db->where("vehicle_user_id", 4408);
			}else if($user_privilege_code == 6){
				$this->db->where("vehicle_user_id", 4408);
			}else{
				$this->db->where("vehicle_no",99999);
			}
		}

		$this->db->where("vehicle_typeunit", 0);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q              = $this->db->get("vehicle");
		$datavehicleall = $q->result();

		$datafix        = array();
		$dataexpired    = array();

		for ($i=0; $i < sizeof($datavehicleall); $i++) {
			$arr = explode("@", $datavehicleall[$i]->vehicle_device);
			$devices[0] = (count($arr) > 0) ? $arr[0] : "";
			$devices[1] = (count($arr) > 1) ? $arr[1] : "";
			// echo "<pre>";
			// var_dump($datavehicleall[$i]->vehicle_dbname_live);die();
			// echo "<pre>";
			$datafromdblive = $this->gpsmodel->getfromDBLIVE($datavehicleall[$i]->vehicle_dbname_live, $devices[0], $devices[1]);
				if ($datafromdblive) {
					array_push($datafix, array(
						"gps_latitude_real"  => $datafromdblive[0]->gps_latitude_real,
						"gps_longitude_real" => $datafromdblive[0]->gps_longitude_real
					));
				}
		}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "data" => $datafix));
	}

	function mapsstandardvehiclebiblastinfoall(){
		if (isset($_POST['session']))
		{
      $this->db->where("session_id", $_POST['session']);
      $this->db->join("user", "user_id = session_user");
      $q = $this->db->get("session");

      if ($q->num_rows() == 0) return;

      $this->sess = $q->row();
		}

		if (! $this->sess)
		{
			echo json_encode(array("info"=>"", "vehicle"=>""));
			return;
		}

		$companyid      = $_POST['companyid'];
		$forclearmaps   = $this->m_poipoolmaster->getmastervehiclebib();
		$datavehicleall = $this->m_poipoolmaster->getmastervehiclebibbycontractor($companyid);
		$datafix        = array();
		$dataexpired    = array();

		for ($i=0; $i < sizeof($datavehicleall); $i++) {
			$arr = explode("@", $datavehicleall[$i]['vehicle_device']);
			$devices[0] = (count($arr) > 0) ? $arr[0] : "";
			$devices[1] = (count($arr) > 1) ? $arr[1] : "";

			$jsonautocheck = json_decode($datavehicleall[$i]['vehicle_autocheck']);
			$auto_status   = $jsonautocheck->auto_status;

			if ($auto_status == "P") {
				$datafromdblive = $this->gpsmodel->getfromDBLIVE($datavehicleall[$i]['vehicle_dbname_live'], $devices[0], $devices[1]);
					if ($datafromdblive) {
						array_push($datafix, array(
							"auto_last_lat"        => substr($datafromdblive[0]->gps_latitude_real, 0, 10),
							"auto_last_long"       => substr($datafromdblive[0]->gps_longitude_real, 0, 10),
							"vehicle_id"           => $datavehicleall[$i]['vehicle_id'],
							"vehicle_user_id"      => $datavehicleall[$i]['vehicle_user_id'],
							"vehicle_device"       => $datavehicleall[$i]['vehicle_device'],
							"vehicle_no"           => $datavehicleall[$i]['vehicle_no'],
							"vehicle_name"         => $datavehicleall[$i]['vehicle_name'],
							"vehicle_active_date2" => $datavehicleall[$i]['vehicle_active_date2'],
							"auto_last_road"       => $jsonautocheck->auto_last_road,
							"auto_last_engine"     => $jsonautocheck->auto_last_engine,
							"auto_last_speed"      => $jsonautocheck->auto_last_speed,
							"auto_last_course"     => $jsonautocheck->auto_last_course,
						));
				}
			}
		}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "data" => $datafix, "alldataforclearmaps" => $forclearmaps));
	}

	function mapsstandardlastinfoall(){
		if (isset($_POST['session']))
		{
      $this->db->where("session_id", $_POST['session']);
      $this->db->join("user", "user_id = session_user");
      $q = $this->db->get("session");

      if ($q->num_rows() == 0) return;

      $this->sess = $q->row();
		}

		if (! $this->sess)
		{
			echo json_encode(array("info"=>"", "vehicle"=>""));
			return;
		}

		$companyid      = $_POST['companyid'];
		$forclearmaps   = $this->m_poipoolmaster->getmastervehicle();
		$datavehicleall = $this->m_poipoolmaster->getmastervehiclebycontractor($companyid);
		$datafix        = array();
		$dataexpired    = array();

		for ($i=0; $i < sizeof($datavehicleall); $i++) {
			$arr = explode("@", $datavehicleall[$i]['vehicle_device']);
			$devices[0] = (count($arr) > 0) ? $arr[0] : "";
			$devices[1] = (count($arr) > 1) ? $arr[1] : "";

			$jsonautocheck = json_decode($datavehicleall[$i]['vehicle_autocheck']);
			$auto_status   = $jsonautocheck->auto_status;

			if ($auto_status == "P") {
				$datafromdblive = $this->gpsmodel->getfromDBLIVE($datavehicleall[$i]['vehicle_dbname_live'], $devices[0], $devices[1]);
					if ($datafromdblive) {
						array_push($datafix, array(
							"auto_last_lat"        => substr($datafromdblive[0]->gps_latitude_real, 0, 10),
							"auto_last_long"       => substr($datafromdblive[0]->gps_longitude_real, 0, 10),
							"vehicle_id"           => $datavehicleall[$i]['vehicle_id'],
							"vehicle_user_id"      => $datavehicleall[$i]['vehicle_user_id'],
							"vehicle_device"       => $datavehicleall[$i]['vehicle_device'],
							"vehicle_no"           => $datavehicleall[$i]['vehicle_no'],
							"vehicle_name"         => $datavehicleall[$i]['vehicle_name'],
							"vehicle_active_date2" => $datavehicleall[$i]['vehicle_active_date2'],
							"auto_last_road"       => $jsonautocheck->auto_last_road,
							"auto_last_engine"     => $jsonautocheck->auto_last_engine,
							"auto_last_speed"      => $jsonautocheck->auto_last_speed,
							"auto_last_course"     => $jsonautocheck->auto_last_course,
						));
				}
			}
		}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "data" => $datafix, "alldataforclearmaps" => $forclearmaps));
	}

	function mapsstandardexcalastinfoall(){
		if (isset($_POST['session']))
		{
      $this->db->where("session_id", $_POST['session']);
      $this->db->join("user", "user_id = session_user");
      $q = $this->db->get("session");

      if ($q->num_rows() == 0) return;

      $this->sess = $q->row();
		}

		if (! $this->sess)
		{
			echo json_encode(array("info"=>"", "vehicle"=>""));
			return;
		}

		$companyid      = $_POST['companyid'];
		$forclearmaps   = $this->m_dashboardview->getmastervehicleformapsstandard();
		$datavehicleall = $this->m_dashboardview->getmastervehiclebycontractor($companyid);
		$datafix        = array();
		$dataexpired    = array();
		$deviceidygtidakada = array();

		// echo "<pre>";
		// var_dump($datavehicleall);die();
		// echo "<pre>";

		$data_inrom = array(
								// "PORT BIB","PORT BIR","PORT TIA",
								//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
								// "ROM A1","ROM B1","ROM B2","ROM B3","ROM EST",
								// "ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
								"ROM A2",
								//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
								//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",

								// "KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5",
								// "KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
								// "KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
								// "KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5",

								// "BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
								// "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
								// "Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
								// "Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
								// "PORT BIB - Antrian","Port BIB - Antrian"
							);

		for ($i=0; $i < sizeof($datavehicleall); $i++) {
			$arr           = explode("@", $datavehicleall[$i]['vehicle_device']);
			$devices[0]    = (count($arr) > 0) ? $arr[0] : "";
			$devices[1]    = (count($arr) > 1) ? $arr[1] : "";


      // echo "<pre>";
      // var_dump($lastinfofix);die();
      // echo "<pre>";

			$jsonautocheck = json_decode($datavehicleall[$i]['vehicle_autocheck']);
			$auto_status   = $jsonautocheck->auto_status;

			$typeunitfix = "";
			$typeunit    = $datavehicleall[$i]['vehicle_typeunit'];

			if ($typeunit == 0) {
				$typeunitfix   = "DT";
				$gps_pto       = "";
				$position_name = explode(",", $jsonautocheck->auto_last_position);

					if (in_array($position_name[0], $data_inrom)) {
						if ($auto_status == "P") {
							// $datafromdblive = $this->gpsmodel->getfromDBLIVE($datavehicleall[$i]['vehicle_dbname_live'], $devices[0], $devices[1]);
							// echo "<pre>";
				      // var_dump($datafromdblive);die();
				      // echo "<pre>";
							// if ($datafromdblive) {
								array_push($datafix, array(
									"vehicle_typeunitname" => $typeunitfix,
									"vehicle_typeunit"     => $datavehicleall[$i]['vehicle_typeunit'],
									"auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
									"auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
									// "auto_last_lat"        => substr($datafromdblive[0]->gps_latitude_real, 0, 10),
									// "auto_last_long"       => substr($datafromdblive[0
									"vehicle_id"           => $datavehicleall[$i]['vehicle_id'],
									"vehicle_user_id"      => $datavehicleall[$i]['vehicle_user_id'],
									"vehicle_device"       => $datavehicleall[$i]['vehicle_device'],
									"vehicle_no"           => $datavehicleall[$i]['vehicle_no'],
									"vehicle_name"         => $datavehicleall[$i]['vehicle_name'],
									"vehicle_active_date2" => $datavehicleall[$i]['vehicle_active_date2'],
									"auto_last_road"       => $jsonautocheck->auto_last_road,
									"auto_last_engine"     => $jsonautocheck->auto_last_engine,
									"auto_last_speed"      => $jsonautocheck->auto_last_speed,
									"auto_last_course"     => $jsonautocheck->auto_last_course,
									"gps_pto"              => $gps_pto,
								));
						 // }
						}
					}

			}else {
				$typeunitfix   = "EXCA";
				$lastinfofix 	 = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, 0, "");
				$gps_pto       = $lastinfofix->gps_cs;
				if ($auto_status == "P") {
					// $datafromdblive = $this->gpsmodel->getfromDBLIVE($datavehicleall[$i]['vehicle_dbname_live'], $devices[0], $devices[1]);
						// if ($datafromdblive) {
							array_push($datafix, array(
								"vehicle_typeunitname" => $typeunitfix,
								"vehicle_typeunit"     => $datavehicleall[$i]['vehicle_typeunit'],
								"auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
								"auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
								// "auto_last_lat"        => substr($datafromdblive[0]->gps_latitude_real, 0, 10),
								// "auto_last_long"       => substr($datafromdblive[0]->gps_longitude_real, 0, 10),
								"vehicle_id"           => $datavehicleall[$i]['vehicle_id'],
								"vehicle_user_id"      => $datavehicleall[$i]['vehicle_user_id'],
								"vehicle_device"       => $datavehicleall[$i]['vehicle_device'],
								"vehicle_no"           => $datavehicleall[$i]['vehicle_no'],
								"vehicle_name"         => $datavehicleall[$i]['vehicle_name'],
								"vehicle_active_date2" => $datavehicleall[$i]['vehicle_active_date2'],
								"auto_last_road"       => $jsonautocheck->auto_last_road,
								"auto_last_engine"     => $jsonautocheck->auto_last_engine,
								"auto_last_speed"      => $jsonautocheck->auto_last_speed,
								"auto_last_course"     => $jsonautocheck->auto_last_course,
								"gps_pto"              => $gps_pto,
							));
					// }
				}
			}
		}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "data" => $datafix, "alldataforclearmaps" => $forclearmaps));
	}

	function lastinfonew()
	{
		if (isset($_POST['session']))
		{
                        $this->db->where("session_id", $_POST['session']);
                        $this->db->join("user", "user_id = session_user");
                        $q = $this->db->get("session");

                        if ($q->num_rows() == 0) return;

                        $this->sess = $q->row();
		}

		if (! $this->sess)
		{
			echo json_encode(array("info"=>"", "vehicle"=>""));
			return;
		}

		$device        = isset($_POST['device']) ? $_POST['device']:     "";
		$vuserid        = isset($_POST['vuserid']) ? $_POST['vuserid']:     0;

		// echo "<pre>";
		// var_dump(strpos($device, '@'));die();
		// echo "<pre>";

		if (strpos($device, '@') !== false) {
			// $sikon = 1;
			$device   = isset($_POST['device']) ? $_POST['device']:     "";
		}else {
			$insearch      = isset($_POST['insearch']) ? $_POST['insearch']: "";

			if ($insearch == 1) {
				// $sikon = 2;
				$mastervehicle = $this->m_poipoolmaster->searchmasterdata("webtracking_vehicle", $insearch);
				$device        = $mastervehicle[0]['vehicle_device'];
			}
		}

		$lasttime = isset($_POST['lasttime']) ? $_POST['lasttime']: 0;

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $device);
		$q = $this->db->get("vehicle");

		// echo "<pre>";
		// var_dump($q->result_array());die();
		// echo "<pre>";

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
			// print_r("asd");exit();
			$json = json_decode($row->vehicle_info);

				echo json_encode(array("info"=>"expired", "vehicle"=>$row));
				return;
		}

		$arr = explode("@", $device);

		$devices[0] = (count($arr) > 0) ? $arr[0] : "";
		$devices[1] = (count($arr) > 1) ? $arr[1] : "";

		//$row->gps = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $row->vehicle_type);
		$row->gps = $this->gpsmodel->GetLastInfoNew($devices[0], $devices[1], true, false, $lasttime, $row->vehicle_type, $vuserid);

		// echo "<pre>";
		// var_dump($row->gps);die();
		// echo "<pre>";

		if ($this->gpsmodel->fromsocket)
		{
			$datainfo = $this->gpsmodel->datainfo;
			$fromsocket = $this->gpsmodel->fromsocket;
		}


		$gtps = $this->config->item("vehicle_gtp");

		if (! in_array(strtoupper($row->vehicle_type), $gtps) && $row->vehicle_type != "TK309PTO" && $row->vehicle_type != "GT06PTO")
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

				if (date("Y", $taktif) < 2000)
				{
					$row->pulse = false;
				}
				else
				{
					$row->pulse = sprintf("Rp %s", number_format($json->sisapulsa, 0, "", "."));
					$row->masaaktif = date("d/m/Y", $taktif);
				}
			}
			else
			{
				$row->pulse = false;
			}

		}
		else
		{
			if (isset($row->gps[0]) && $row->gps[0] && date("Ymd", $row->gps[0]->gps_timestamp) >= date("Ymd"))
			{
				if (! isset($fromsocket))
				{
					$tables = $this->gpsmodel->getTable($row);
					$this->db = $this->load->database($tables["dbname"], TRUE);
				}

			}
			else
			if (! isset($fromsocket))
			{
				$tables['info'] = sprintf("%s@%s_info", strtolower($devices[0]), strtolower($devices[1]));
				$istbl_history = $this->config->item("dbhistory_default");
				if($this->config->item("is_dbhistory") == 1)
				{
					$istbl_history = $row->vehicle_dbhistory_name;
				}
				$this->db = $this->load->database($istbl_history, TRUE);
			}

			// ambil informasi di gps_info

			if (! isset($datainfo))
			{
				$this->db->order_by("gps_info_time", "DESC");
				$this->db->where("gps_info_device", $device);
				$q = $this->db->get($tables['info'], 1, 0);
				$totalinfo = $q->num_rows();
				if ($totalinfo)
				{
					$rowinfo = $q->row();
				}
			}
			else
			{
				$rowinfo = $datainfo;
				$totalinfo = 1;
			}

			if ($totalinfo == 0)
			{
				$row->status = "-";
				$row->status1 = false;
				$row->status2 = false;
				$row->status3 = false;
				$row->pulse = "-";
			}
			else
			{
				$ioport = $rowinfo->gps_info_io_port;

				$row->status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
				$row->status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
				if(isset($devices[1]) && ($devices[1] == "GT06" || $devices[1] == "A13" || $devices[1] == "TK309" || $devices[1] == "TK309PTO" || $devices[1] == "GT06PTO"))
				{
					if(isset($row->gps[0]->gps_speed_fmt) && $row->gps[0]->gps_speed_fmt > 0)
					{
						$row->status1 = true;
					}
					else
					{
						$row->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
					}
				}
				else
				{
					$row->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
				}
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

						if (date("Y", $taktif) < 2000)
						{
							$row->pulse = false;
						}
						else
						{
							$row->pulse = sprintf("Rp %s", number_format($json->sisapulsa, 0, "", "."));
							$row->masaaktif = date("d/m/Y", $taktif);
						}
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

					if (date("Y", $taktif) < 2000)
					{
						$row->pulse = false;
					}
					else
					{
						$row->pulse = sprintf("Rp %s", $pulsa);
						$row->masaaktif = date("d/m/Y", $taktif);
					}
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
							//print_r($fuel);exit;
							//Deteksi Fuel Capacity

							$this->db = $this->load->database("default", TRUE);

							if ($row->vehicle_fuel_capacity != 0 && $row->vehicle_fuel_capacity == 300)
							{

								$sql = "SELECT * FROM (
										(
											SELECT *
											FROM `webtracking_fuel`
											WHERE `fuel_tank_capacity` = ". $row->vehicle_fuel_capacity ."
											AND `fuel_led_resistance` <= ". $fuel ."
											ORDER BY fuel_led_resistance DESC
											LIMIT 1
										)
									) tbldummy";
							}
							else
							{
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
							}
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



			//since at ssi

			/*if($this->sess->user_id == 1933){

				$dev = explode("@", $device);

				$json = json_decode($row->vehicle_info);
				if ($row->vehicle_info)
				{
					if (isset($json->vehicle_ip) && isset($json->vehicle_port))
					{
						$databases = $this->config->item('databases');

						if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
						{
							$database = $databases[$json->vehicle_ip][$json->vehicle_port];
							$tablegps = $this->config->item("external_gpstable");
							$tablegpsinfo = $this->config->item("external_gpsinfotable");
							$tablegpshist = $this->config->item("external_gpstable_history");
							$this->db = $this->load->database($database, TRUE);
						}
					}

					if(isset($json->vehicle_ws))
					{
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $row->vehicle_dbhistory_name;
						}
						$tablegps = strtolower($dev[0]."@".$dev[1]."_gps");
						$tablegpsinfo = strtolower($dev[0]."@".$dev[1]."_info");
						$this->db = $this->load->database($istbl_history, TRUE);
					}

					if(! isset($tablegps))
					{
						$table_hist = $this->config->item("table_hist");
						$tablegps = $this->gpsmodel->getGPSTable($vehicle->vehicle_type);
						$tablegpshist = $table_hist[strtoupper($vehicle->vehicle_type)];
					}

				}

				$this->db->select("gps_info_io_port, gps_info_time, gps_info_device, gps_speed");
				$this->db->order_by("gps_info_time", "asc");
				$this->db->where("gps_info_device", $device);
				$this->db->join($tablegps, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$q = $this->db->get($tablegpsinfo);
				$rowstartoff = $q->result();


				for ($i=0;$i<count($rowstartoff);$i++)
				{
					if ($rowstartoff[$i]->gps_info_io_port == "0000000000")
					{
						if (isset($rowstartoff[$i-1]->gps_info_io_port))
						{
							if ($rowstartoff[$i-1]->gps_info_io_port == "0000000000"){}
							else if ($rowstartoff[$i-1]->gps_info_io_port == "0000100000")
							{
								$startoff_ssi = $rowstartoff[$i]->gps_info_time;
							}
							else{}
						}
					}

					if($rowstartoff[$i]->gps_info_io_port == "0000100000" && $rowstartoff[$i]->gps_speed == 0)
					{
						if (isset($rowstartoff[$i-1]->gps_info_io_port))
						{
							if ($rowstartoff[$i-1]->gps_info_io_port == "0000100000" && $rowstartoff[$i]->gps_speed == 0){}
							else if ($rowstartoff[$i-1]->gps_info_io_port == "0000000000")
							{
								$starton_ssi = $rowstartoff[$i]->gps_info_time;
							}
							else{}
						}
					}
				}


				if(isset($starton_ssi))
				{
					$ontime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($starton_ssi)));
					$timenow = date("Y-m-d H:i:s");
					$oncalculation = get_time_difference($ontime, $timenow);

					$showon_ssi = " ".$ontime;
					$showdurationon_ssi = " "." ";
					if($oncalculation[0]!=0){
						$showdurationon_ssi .= $oncalculation[0] ." Day ";
					}
					if($oncalculation[1]!=0){
						$showdurationon_ssi .= $oncalculation[1] ." Hour ";
					}
					if($oncalculation[2]!=0){
						$showdurationon_ssi .= $oncalculation[2] ." Min ";
					}

					if($showdurationon_ssi == " "." "){
						$showdurationon_ssi .= "0 Min ";
					}

				}

				if(isset($startoff_ssi))
				{
					$offtime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($startoff_ssi)));
					$timenow = date("Y-m-d H:i:s");
					$offcalculation = get_time_difference($offtime, $timenow);
					$showoff_ssi = " ".$offtime;
					$showduration_ssi = " "." ";
					if($offcalculation[0]!=0){
						$showduration_ssi .= $offcalculation[0] ." Day ";
					}
					if($offcalculation[1]!=0){
						$showduration_ssi .= $offcalculation[1] ." Hour ";
					}
					if($offcalculation[2]!=0){
						$showduration_ssi .= $offcalculation[2] ." Min ";
					}

					if($showduration_ssi == " "." "){
						$showduration_ssi .= "0 Min ";
					}

				}

			} */

			//since at iwatani
			if($this->sess->user_id == 1837){

				$this->db->order_by("gps_info_time", "asc");
				$this->db->where("gps_info_device", $device);
				$q = $this->db->get($tables['info']);
				$rowstartoff = $q->result();
				//print_r($rowstartoff);exit;

				for ($i=0;$i<count($rowstartoff);$i++)
				{
					if ($rowstartoff[$i]->gps_info_io_port == "0000000000")
					{
						if (isset($rowstartoff[$i-1]->gps_info_io_port))
						{
							if ($rowstartoff[$i-1]->gps_info_io_port == "0000000000"){}
							else if ($rowstartoff[$i-1]->gps_info_io_port == "0000100000")
							{
								$startoff_iwatani = $rowstartoff[$i]->gps_info_time;
							}
							else{}
						}
					}
				}

				if (isset($startoff_iwatani))
				{
					$offtime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($startoff_iwatani)));
					$timenow = date("Y-m-d H:i:s");
					$offcalculation = get_time_difference($offtime, $timenow);
					//print_r($offcalculation);exit;
					$showoff_iwatani = " ".$offtime;
					$showduration_iwatani = " "." ";
					if($offcalculation[0]!=0){
						$showduration_iwatani .= $offcalculation[0] ." Day ";
					}
					if($offcalculation[1]!=0){
						$showduration_iwatani .= $offcalculation[1] ." Hour ";
					}
					if($offcalculation[2]!=0){
						$showduration_iwatani .= $offcalculation[2] ." Min ";
					}

					if($showduration_iwatani == " "." "){
						$showduration_iwatani .= "0 Min ";
					}
				}

				//print_r($showduration);exit;

			}
			//end

				/*
				//Get Start Off
				$this->db->order_by("gps_info_time", "asc");
				$this->db->where("gps_info_device", $device);
				$q = $this->db->get($tables['info']);

				$rowstartoff = $q->result();
				//print_r($rowstartoff);exit;

				for ($i=0;$i<count($rowstartoff);$i++)
				{
					if ($rowstartoff[$i]->gps_info_io_port == "0000000000")
					{
						if (isset($rowstartoff[$i-1]->gps_info_io_port))
						{
							if ($rowstartoff[$i-1]->gps_info_io_port == "0000000000"){}
							else if ($rowstartoff[$i-1]->gps_info_io_port == "0000100000")
							{
								$startoff = $rowstartoff[$i]->gps_info_time;
							}
							else{}
						}
					}

					if ($rowstartoff[$i]->gps_info_io_port == "0000100000")
					{
						if (isset($rowstartoff[$i-1]->gps_info_io_port))
						{
							if ($rowstartoff[$i-1]->gps_info_io_port == "0000100000"){}
							else if ($rowstartoff[$i-1]->gps_info_io_port == "0000000000")
							{
								$starton = $rowstartoff[$i]->gps_info_time;
							}
							else{}
						}
					}
				}

				if (isset($startoff))
				{
					$offtime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($startoff)));
					$timenow = date("Y-m-d H:i:s");
					$offcalculation = get_time_difference($offtime, $timenow);

					$showoff = " ".$offtime;
					$showduration = " "." ";
					if($offcalculation[0]!=0){
						$showduration .= $offcalculation[0] ." Day ";
					}
					if($offcalculation[1]!=0){
						$showduration .= $offcalculation[1] ." Hour ";
					}
					if($offcalculation[2]!=0){
						$showduration .= $offcalculation[2] ." Min ";
					}

					if($showduration == " "." "){
						$showduration .= "0 Min ";
					}
				}

				if (isset($starton))
				{
					$ontime = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($starton)));
					$timenow = date("Y-m-d H:i:s");
					$oncalculation = get_time_difference($ontime, $timenow);

					$showon = " ".$ontime;
					$showdurationon = " "." ";
					if($oncalculation[0]!=0){
						$showdurationon .= $oncalculation[0] ." Day ";
					}
					if($oncalculation[1]!=0){
						$showdurationon .= $oncalculation[1] ." Hour ";
					}
					if($oncalculation[2]!=0){
						$showdurationon .= $oncalculation[2] ." Min ";
					}

					if($showdurationon == " "." "){
						$showdurationon .= "0 Min ";
					}
				}
				*/
		}

		// echo "<pre>";
		// var_dump($tables["dbname"]);die();
		// echo "<pre>";


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
		//$row->gps[0] = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $row->vehicle_type);

		if (! $row->gps)
		{
			echo json_encode(array("info"=>"", "vehicle"=>$row));
			return;
		}

		$delayresatrt = mktime() - $row->gps[0]->gps_timestamp;
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

		//khusus user powerblock (pengurus)
		if($this->sess->user_company == "1724" || $this->sess->user_company == "1723"){
			$row->geofence_location = $this->getGeofence_location_pbi($row->gps[0]->gps_longitude, $row->gps[0]->gps_ew, $row->gps[0]->gps_latitude, $row->gps[0]->gps_ns, $row->vehicle_device);
		}else{
			if (!in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_others"))){
				$row->geofence_location = $this->getGeofence_location($row->gps[0]->gps_longitude, $row->gps[0]->gps_ew, $row->gps[0]->gps_latitude, $row->gps[0]->gps_ns, $row->vehicle_user_id);
			}
			else
			{
				$row->geofence_location = $this->getGeofence_location_others($row->gps[0]->gps_longitude_real, $row->gps[0]->gps_latitude_real, $row->vehicle_user_id);
			}
		}

		//APP CO Reksa
		$app_co = $this->config->item("app_co");
		if ($app_co && $app_co == 1)
		{
			$row->destination = $this->getdestination($row->vehicle_device);
		}
		//end

		//RENTCAR
		$apprentcar = $this->config->item("rentcar_app");
		$appdosj = $this->config->item("app_dosj");
		$appfan = $this->config->item("fan_app");

		//untuk semua transporter company terdaftar
		$app_dosj_all = $this->config->item("app_dosj_all");

		//Balrich
		$appvehicle_detail = $this->config->item("vehicle_detail_app");

		if ($appvehicle_detail && $appvehicle_detail == 1)
		{
			$row->v_detail = $this->getvehicledetail($row->vehicle_device);
		}

		if ($row->vehicle_type == "T5DOOR" || $row->vehicle_type == "T5FAN" || $row->vehicle_type == "T5PTO")
		{
			$row->fan = $this->getFanStatus($row->gps[0]->gps_msg_ori);
		}

		if($row->vehicle_type == "T5SUHU")
		{
			$row->suhu = $this->getSuhu($row->gps[0]->gps_msg_ori);
		}

		if ($apprentcar && $apprentcar == 1)
		{
			$row->customer = $this->getcustomer($row->vehicle_no);
			$x = explode("," ,$row->customer);
			$y = $x[0];
			$row->tenant = $this->get_tenant($y);
			$params["tenant"] = $row->tenant;
			$params["customer"]= $row->customer;
			//print_r ($row->tenant);exit;
		}
		//END RENTCAR

		if (isset($appdosj) && ($appdosj == 1))
		{
			//Get Driver by schedule DO/SJ
			$row->driver = $this->getdriver_dosj($row->vehicle_device);
			$row->dosj = $this->get_dosj($row->vehicle_device);
		}
		else
		{
			$row->driver = $this->getdriver($row->vehicle_id);

		}

		//get driver rf id
		$apprfid = $this->config->item("app_driver_rdif");
		if($this->sess->user_id == 3986){
			$apprfid = 1;
			$row->driver_idcard = $this->getdriver_idcard($row->vehicle_device);
		}

		//dosj untuk semua transporter company terdaftar
		if (isset($app_dosj_all) && ($app_dosj_all == 1) && in_array(strtoupper($this->sess->user_company), $this->config->item("company_view_dosj")))
		{

			//Get Driver by schedule DO/SJ
			$row->driver = $this->getdriver_dosj_all($row->vehicle_device);
			$row->dosj = $this->get_dosj_all($row->vehicle_device);
			//$row->customer_groups = $this->getcustomer_dosj_all($row->vehicle_device);
		}
		else
		{
			$row->driver = $this->getdriver($row->vehicle_id);

		}


		//Transporter Powerblock
		$app_powerblock = $this->config->item("app_powerblock");
		if(isset($app_powerblock) && $app_powerblock == 1){

			//$row->id_so = $this->get_so($row->vehicle_device);
			$row->no_sj = $this->get_new_sj($row->vehicle_device);
			$row->driver_sj = $this->get_new_driver($row->vehicle_device);

			/*print_r($row->id_sj);
			print_r($row->driver);
			exit();*/

		}

		// DATA PROJECT
		if (in_array(strtoupper($this->sess->user_id), $this->config->item("user_view_pto"))) {
				$row->dataproject = $this->getdataproject($row->vehicle_device);
				// print_r("masuk");exit();
		}

		$app_tag = $this->config->item("app_tag");
		if(isset($app_tag) && $app_tag == 1){
			$row->off_autocheck = $this->get_off_autocheck($row->vehicle_device);
		}

		//Transporter Tupperware
		$app_tupperware = $this->config->item("app_tupperware");
		if ($this->sess->user_trans_tupper == 1 || (isset($app_tupperware) && $app_tupperware == 1))
		{
			$row->id_booking = $this->get_id_booking($row->vehicle_device);

			if (isset($row->id_booking) && $row->id_booking != "")
			{
				$row->noso = $this->get_noso($row->vehicle_device);
				//$row->slcars = $this->get_slcars($row->id_booking);
			}
			if (isset($row->id_booking) && $row->id_booking != "")
			{
				$row->nodr = $this->get_nodr($row->vehicle_device);
			}
			if (isset($row->noso) && $row->noso != "")
			{
				if (isset($row->nodr) && $row->nodr != "")
				{
					$row->dbcode = $this->get_dbcode($row->vehicle_device);
				}
			}
		}

        $row->customer_groups = $this->getcustomer_groups($row->vehicle_group);
		$row->since_geofence_in = "";

		if ($row->vehicle_company != 0)
		{
			$row->company = $this->getCompany($row->vehicle_company);
			//print_r($row->company);exit;
		}
		else
		{
			$row->company = 0;
		}

		//INFO : Since At Geofence
		$app_sinceat = $this->config->item("since_at_geofence");
		if ($app_sinceat && $app_sinceat==1)
		{
			if(isset($row->geofence_location) && $row->geofence_location != "")
			{
				$row->since_geofence_in = $this->getInGeofence($row->vehicle_device, $row->geofence_location);
			}
		}

		//since at iwatani
		if($this->sess->user_id == 1837){

			if (isset($showoff_iwatani))
			{
				$row->startoff_iwatani = $showoff_iwatani;
				$row->offduration_iwatani = $showduration_iwatani;
			}

		}

		//since at ssi
		/*if($this->sess->user_id == 1933){

			if (isset($showoff_ssi))
			{
				$row->startoff_ssi = $showoff_ssi;
				$row->offduration_ssi = $showduration_ssi;
			}else if(isset($showon_ssi)){
				$row->starton_ssi = $showon_ssi;
				$row->onduration_ssi = $showdurationon_ssi;
			}
		} */

		//tcontinent
		$apptcontinent = $this->config->item("tcontinent_app");
		if ($apptcontinent && $apptcontinent == 1)
		{
			$row->jobfile = $this->getjobfile($row->vehicle_id);

			$params["jobfile"]= $row->jobfile;
			//print_r($row->jobfile);exit;
		}
		//END tcontinent

		// Khusus BANGUN CILEGON  ID
		if($this->sess->user_id == 1540){

			$row->muatan = $this->getmuatan($row->vehicle_id);

			$params["muatan"]= $row->muatan;

		}
		//END BANGUN CILEGON

		//SSI
		$appssi = $this->config->item("ssi_app");
		if ($appssi && $appssi == 1)
		{
			$row->team = $this->getteam($row->vehicle_id);
			$params["team"]= $row->team;

			//comment
			$row->comment = $this->getcomment($row->vehicle_id);
			$params["comment"]= $row->comment;
		}
		//END SSI

		//comment app
		$appcomment = $this->config->item("comment_app");
		if ($appcomment && $appcomment == 1)
		{
			$row->comment = $this->getcomment($row->vehicle_id);
			$params["comment"]= $row->comment;
		}

		$app = $this->config->item("alatberat_app");
		if ($app && $app==1)
		{
			$row->hourmeter = $this->getHourmeter($row->gps[0]->gps_msg_ori);
		}

		if (isset($showoff))
		{
			$row->startoff = $showoff;
			$row->offduration = $showduration;
		}

		if (isset($showon))
		{
			$row->starton = $showon;
			$row->onduration = $showdurationon;
		}

		if($row->vehicle_type == "TK309PTO" || $row->vehicle_type == "GT06PTO")
		{
			$row->traccarPTO = $this->getTraccarPTO($rowinfo->gps_info_io_port);
		}

		if($row->vehicle_type == "TK315DOOR")
		{
			$row->fan = $this->getDoorStatus($row->gps[0]->gps_msg_ori);
		}

		if($row->vehicle_type == "X3_DOOR" || $row->vehicle_type == "TK315DOOR_NEW" || $row->vehicle_type == "X3_PTO" || $row->vehicle_type == "TK510DOOR" || $row->vehicle_type == "TK510CAMDOOR" || $row->vehicle_type == "TK315FAN" || $row->vehicle_type == "GT08SDOOR" ||
		   $row->vehicle_type == "GT08DOOR" || $row->vehicle_type == "GT08CAMDOOR" || $row->vehicle_type == "GT08SPTO" || $row->vehicle_type == "GT08PTO")
		{
			$row->fan = $row->gps[0]->gps_cs;
		}

		if($row->vehicle_type == "TJAM")
		{
			$parse = explode(",",$row->gps[0]->gps_msg_ori);
			if(isset($parse[13]))
			{
				$row->battery = $parse[13];
			}
		}

		if($row->vehicle_type == "AT5")
		{
			$row->battery = $this->getPersen($row->gps[0]->gps_cs);
		}

		if($row->vehicle_type == "GT08SRFID" || $row->vehicle_type == "GT08SRFIDDOOR" || $row->vehicle_type == "GT08SRFIDPTO")
		{
			$row->driver_rfid = $this->getdriver_rfid($row->vehicle_device,$row->vehicle_dbname_live);
		}


		if ($row->vehicle_user_id == 389 && $row->vehicle_type != "A13") //khusus farrasindo
		{
			$row->cutpower = $this->getCutPower($row->vehicle_id,$row->gps[0]->gps_time);
			$params["cutpower"] = $row->cutpower;
		}

		//app workhour (engine on dan off
		if (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_workhour"))){
			$row->workhour = $this->getWorkhour($row->gps[0]->gps_workhour);
		}else{
			$row->workhour = $this->getWorkhour(0);
		}

		if (in_array(strtoupper($row->vehicle_type), $this->config->item("vehicle_cam")))
		{
			$row->snap = $this->getLastSnap($row->vehicle_device,$row->vehicle_dbname_live);
			$exp = explode("|", $row->snap);
			$row->snapimage = $exp[0];
			$row->snaptime = date("d F Y H:i:s", strtotime($exp[1]));
			// echo "<pre>";
			// var_dump($row->snaptime);die();
			// echo "<pre>";
		}

		// GET DATA VEHICLE MV03
		$this->db->select("vehicle_mv03, vehicle_device");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $device);
		$query       = $this->db->get("vehicle");
		$result      = $query->result_array();
		$vehiclemv03 = $result[0]['vehicle_mv03'];
		$vdevice     = explode("@",$result[0]['vehicle_device']);

		// DRIVER DETAIL START
		if ($vehiclemv03 != "0000") {
			// METODE BARU
			$this->dbwebts = $this->load->database("webtracking_ts",true);
			$this->dbwebts->select("*");
			$this->dbwebts->where("change_imei", $vehiclemv03);
			$this->dbwebts->where("change_driver_flag", 0);
			$this->dbwebts->order_by("change_driver_time", "DESC");
			$this->dbwebts->limit(1);
			$q               = $this->dbwebts->get("ts_driver_change");
			$resultnewmethod = $q->result();

			$changedrivertime                  = $resultnewmethod[0]->change_driver_time;

			$report     = "alarm_";
			$report_sum = "summary_";
			$m1         = date("F", strtotime($changedrivertime));
			$year       = date("Y", strtotime($changedrivertime));
			switch ($m1)
			{
				case "January":
	            $dbtable = $report."januari_".$year;
				$dbtable_sum = $report_sum."januari_".$year;
				break;
				case "February":
	            $dbtable = $report."februari_".$year;
				$dbtable_sum = $report_sum."februari_".$year;
				break;
				case "March":
	            $dbtable = $report."maret_".$year;
				$dbtable_sum = $report_sum."maret_".$year;
				break;
				case "April":
	            $dbtable = $report."april_".$year;
				$dbtable_sum = $report_sum."april_".$year;
				break;
				case "May":
	            $dbtable = $report."mei_".$year;
				$dbtable_sum = $report_sum."mei_".$year;
				break;
				case "June":
	            $dbtable = $report."juni_".$year;
				$dbtable_sum = $report_sum."juni_".$year;
				break;
				case "July":
	            $dbtable = $report."juli_".$year;
				$dbtable_sum = $report_sum."juli_".$year;
				break;
				case "August":
