<?php
include "base.php";

class Maxreport extends Base {
	var $otherdb;

	function Maxreport()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}

	function index(){

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		//print_r($rows);exit;
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('transporter/report/list', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function tripmileage_report()
	{

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
		{
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		}
		else
		{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate ." " . $ehour . ":59"));
		}

		$now = date("Y-m-d");
		$cek_enddate = date("Y-m-d", strtotime($enddate));
		$cek_date = date("Y-m-d", strtotime($startdate));

		$json = json_decode($rowvehicle->vehicle_info);

		if (($cek_date == $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			if ($rowvehicle->vehicle_info)
			{
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		}
		else if(($cek_date < $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);

			$action = "history";
		}
		else
		{

			if ($rowvehicle->vehicle_info && (!isset($json->vehicle_ws)))
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			if (isset($json->vehicle_ws))
			{
				$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
				$this->db = $this->load->database("gpshistory2", TRUE);
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			if (isset($json->vehicle_ws))
			{
				$this->db = $this->load->database("default", TRUE);
			}

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time","asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if (((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) &&
			(!isset($json->vehicle_ws)))
		{
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();


			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows)==0)
			{

				if(isset($action) && $action=="history")
				{
					if ($rowvehicle->vehicle_info)
					{
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							}
							else
							{
								$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists = $this->config->item("table_hist");
								$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db = $this->load->database("default", TRUE);
							}
						}
					}

					if (! isset($table))
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists = $this->config->item("table_hist");
						$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db = $this->load->database("default", TRUE);
					}


					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time","asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan
		}
		else
		{
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;
		}


		$data = array();

		$nopol = "";

		$on = false;
		$trows = count($rows);
		//print_r($trows);exit;

		for($i=0;$i<$trows;$i++){
			//if($rows[$i]->gps_speed == 0) continue;

			if($nopol != $rowvehicle->vehicle_no){ //new vehicle
				if($on && $i!=0){
					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
					}
					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_latitude);
					}
					$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
				}
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					$trip_no = 1;
					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
					{
						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
					}
					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
					}
					$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
					$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] =$rowvehicle->vehicle_name;
					$on = true;

					if($i==$trows-1){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					$trip_no = 1;
					$on = false;
				}
			}else{ //same vehicle
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					if(!$on){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
						{
							$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle->vehicle_name;
					}

					$on = true;
					if($i==$trows-1 && $on){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					if($on){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
					$on = false;

				}
			}
			$nopol = $rowvehicle->vehicle_no;
		}

			$params['data'] = $data;
			$params['vehicle'] = $rowvehicle;
			$html = $this->load->view("transporter/report/list_result_trip_mileage", $params, true);
			$callback['error'] = false;
			$callback['html'] = $html;

			echo json_encode($callback);

		return;
	}

	//trip mileage report summary
	function tripmileage_report_summary()
	{

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
		{
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		}
		else
		{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate ." " . $ehour . ":59"));
		}

		$now = date("Y-m-d");
		$cek_enddate = date("Y-m-d", strtotime($enddate));
		$cek_date = date("Y-m-d", strtotime($startdate));

		$json = json_decode($rowvehicle->vehicle_info);

		if (($cek_date == $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			if ($rowvehicle->vehicle_info)
			{
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		}
		else if(($cek_date < $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);

			$action = "history";
		}
		else
		{

			if ($rowvehicle->vehicle_info && (!isset($json->vehicle_ws)))
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			if (isset($json->vehicle_ws))
			{
				$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
				$this->db = $this->load->database("gpshistory2", TRUE);
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			if (isset($json->vehicle_ws))
			{
				$this->db = $this->load->database("default", TRUE);
			}

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time","asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if (((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) &&
			(!isset($json->vehicle_ws)))
		{
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();


			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows)==0)
			{

				if(isset($action) && $action=="history")
				{
					if ($rowvehicle->vehicle_info)
					{
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							}
							else
							{
								$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists = $this->config->item("table_hist");
								$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db = $this->load->database("default", TRUE);
							}
						}
					}

					if (! isset($table))
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists = $this->config->item("table_hist");
						$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db = $this->load->database("default", TRUE);
					}


					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time","asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan
		}
		else
		{
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;
		}


		$data = array();

		$nopol = "";

		$on = false;
		$trows = count($rows);
		//print_r($trows);exit;

		for($i=0;$i<$trows;$i++){
			//if($rows[$i]->gps_speed == 0) continue;

			if($nopol != $rowvehicle->vehicle_no){ //new vehicle
				if($on && $i!=0){
					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
					}

					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_latitude);
					}
					$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
				}
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					$trip_no = 1;
					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
					{
						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
					}
					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
					}
					$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
					$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] =$rowvehicle->vehicle_name;
					$on = true;

					if($i==$trows-1){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					$trip_no = 1;
					$on = false;

				}


			}else{ //same vehicle
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					if(!$on){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle->vehicle_name;
					}

					$on = true;
					if($i==$trows-1 && $on){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					if($on){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}

						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
					$on = false;

				}
			}
			$nopol = $rowvehicle->vehicle_no;
		}
			$params['data'] = $data;
			$params['vehicle'] = $rowvehicle;
			$html = $this->load->view("transporter/report/list_result_trip_mileage_summary", $params, true);
			$callback['error'] = false;
			$callback['html'] = $html;

			echo json_encode($callback);

		return;
	}
	//

	//for data KML
	function tripmileage_report_kml(){

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
		{
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		}
		else
		{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate ." " . $ehour . ":59"));
		}

		$now = date("Y-m-d");
		$cek_enddate = date("Y-m-d", strtotime($enddate));
		$cek_date = date("Y-m-d", strtotime($startdate));

		$json = json_decode($rowvehicle->vehicle_info);

		if(($cek_date == $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			if($rowvehicle->vehicle_info)
			{
				if(isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if(isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if(!isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		}
		else if(($cek_date < $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);

			$action = "history";
		}
		else
		{
			if($rowvehicle->vehicle_info && (!isset($json->vehicle_ws)))
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if(isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if(isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if(!isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			if(isset($json->vehicle_ws))
			{
				$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
				$this->db = $this->load->database("gpshistory2", TRUE);
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			if(isset($json->vehicle_ws))
			{
				$this->db = $this->load->database("default", TRUE);
			}

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time","asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if(((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) && (!isset($json->vehicle_ws)))
		{
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();

			if(count($rows) == 0)
			{
				if(isset($action) && $action == "history")
				{
					if ($rowvehicle->vehicle_info)
					{
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							}
							else
							{
								$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists = $this->config->item("table_hist");
								$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db = $this->load->database("default", TRUE);
							}
						}
					}

					if(!isset($table))
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists = $this->config->item("table_hist");
						$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db = $this->load->database("default", TRUE);
					}

					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time","asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
		}
		else
		{
			$rows = array_merge($rows2, $rows1);
		}


		$kml = array();
		$kml[] = '<?xml version="1.0" encoding="UTF-8"?>';
		$kml[] = '<kml xmlns="http://earth.google.com/kml/2.2">';
		$kml[] = '<Document>';
		$kml[] = '<name>'."tes".'</name>';
		$kml[] = '<description>'."test".'</description>';
		$kml[] = '<Style id="style3">';
		$kml[] = '<linestyle>';
		$kml[] = '<color>'."73FF0000".'</color>';
		$kml[] = '<width>5</width>';
		$kml[] = '</linestyle>';
		$kml[] = '</Style>';
		$kml[] = '<Style id="exampleBalloonStyle">';
		$kml[] = '<IconStyle>';
		$kml[] = '<Icon>';
		$kml[] = '<href>'.base_url().'assets/images/car/car_front.png</href>';
		$kml[] = '<scale>1.0</scale>';
		$kml[] = '</Icon>';
		$kml[] = '</IconStyle>';
		$kml[] = '<BalloonStyle>';
		$kml[] = '<bgColor>'."00664422".'</bgColor>';
		$kml[] = '</BalloonStyle>';
		$kml[] = '</Style>';
		$a = "";

		$no_urut = 1;
		for($i=0;$i < count($rows); $i++)
		{
			$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
			$rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);
			$rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;


			if($i > 0){

				if($rows[$i]->gps_latitude_real_fmt != $rows[$i-1]->gps_latitude_real_fmt){
					$kml[] = '<Placemark>';
					$kml[] = '<name>'. $no_urut++ .'</name>';
					$kml[] = '<description>' . $rows[$i]->gpsaddress . '</description>';
					$kml[] = '<styleUrl>#exampleBalloonStyle</styleUrl>';
					$kml[] = '<Point>';
					$kml[] = '<coordinates>'. $rows[$i]->gps_longitude_real_fmt .','. $rows[$i]->gps_latitude_real_fmt .'</coordinates>';
					$kml[] = '</Point>';
					$kml[] = '</Placemark>';
				}

			}else if($i == 0){
					$kml[] = '<Placemark>';
					$kml[] = '<name>'. $i .'</name>';
					$kml[] = '<description>' . $rows[$i]->gpsaddress . '</description>';
					$kml[] = '<styleUrl>#exampleBalloonStyle</styleUrl>';
					$kml[] = '<Point>';
					$kml[] = '<coordinates>'. $rows[$i]->gps_longitude_real_fmt .','. $rows[$i]->gps_latitude_real_fmt .'</coordinates>';
					$kml[] = '</Point>';
					$kml[] = '</Placemark>';
			}


			if($i == (count($rows)-1)){
				//$kml[] = '<Placemark id="point'. $rowvehicle->vehicle_id .'">';
				$kml[] = '<Placemark>';
				$kml[] = '<styleUrl>#style3</styleUrl>';
				$kml[] = '<LineString>';
				$kml[] = '<tessellate>1</tessellate>';
				$kml[] = '<coordinates>';
			}

			if($i > 0){

				if($rows[$i]->gps_latitude_real_fmt != $rows[$i-1]->gps_latitude_real_fmt){
					$a .= $rows[$i]->gps_longitude_real_fmt .','. $rows[$i]->gps_latitude_real_fmt ." ";
				}

			}else if($i == 0){
				$a .= $rows[$i]->gps_longitude_real_fmt .','. $rows[$i]->gps_latitude_real_fmt ." ";
			}


			if($i == (count($rows)-1)){
				$kml[] = $a;
				$kml[] = '</coordinates>';
				$kml[] = '</LineString>';
				$kml[] = '</Placemark>';
			}

		}

		$kml[] = '</Document>';
		$kml[] = '</kml>';
		$kmlOutput = join("\n", $kml);
		header('Content-type:application/vnd.google-earth.kml+xml kml');
		header('Content-disposition:attachment;filename="trip_mileage.kml"');
		echo $kmlOutput;
	}


	function tripmileage_report_excel()
	{
		$jobfile = $this->input->post("jobfile");
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
		{
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		}
		else
		{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate ." " . $ehour . ":59"));
		}

		$now = date("Y-m-d");
		$cek_enddate = date("Y-m-d", strtotime($enddate));
		$cek_date = date("Y-m-d", strtotime($startdate));

		$json = json_decode($rowvehicle->vehicle_info);

		if (($cek_date == $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			if ($rowvehicle->vehicle_info)
			{
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		}
		else if(($cek_date < $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$action = "history";
		}
		else
		{

			if ($rowvehicle->vehicle_info && (!isset($json->vehicle_ws)))
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			if (isset($json->vehicle_ws))
			{
				$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
				$this->db = $this->load->database("gpshistory2", TRUE);
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			if (isset($json->vehicle_ws))
			{
				$this->db = $this->load->database("default", TRUE);
			}

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);


			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time","asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if (((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) &&
			(!isset($json->vehicle_ws)))
		{
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();


			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows)==0)
			{

				if(isset($action) && $action=="history")
				{
					if ($rowvehicle->vehicle_info)
					{
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							}
							else
							{
								$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists = $this->config->item("table_hist");
								$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db = $this->load->database("default", TRUE);
							}
						}
					}

					if (! isset($table))
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists = $this->config->item("table_hist");
						$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db = $this->load->database("default", TRUE);
					}


					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time","asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan
		}
		else
		{
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;
		}


		$data = array();

		$nopol = "";

		$on = false;
		$trows = count($rows);

		for($i=0;$i<$trows;$i++){
			//if($rows[$i]->gps_speed == 0) continue;

			if($nopol != $rowvehicle->vehicle_no){ //new vehicle
				if($on && $i!=0){
					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
					}

					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_latitude);
					}
					$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
				}
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					$trip_no = 1;
					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
					{
						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
					}


					if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
					}
					else
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
					}
					$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
					$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] =$rowvehicle->vehicle_name;
					$on = true;

					if($i==$trows-1){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}

						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					$trip_no = 1;
					$on = false;

				}


			}else{ //same vehicle
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					if(!$on){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}

						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle->vehicle_name;
					}

					$on = true;
					if($i==$trows-1 && $on){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					if($on){
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315")
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
						}
						else
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						}
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
					$on = false;

				}
			}

			$nopol = $rowvehicle->vehicle_no;
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
			$objPHPExcel->getProperties()->setTitle("Trip Mileage Detail Report");
			$objPHPExcel->getProperties()->setSubject("Trip Mileage Detail Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Trip Mileage Detail Repor Lacak-mobil.com");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Trip Mileage Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$filter_date = kopindosatformatdatetime($startdate ." " . $shour . ":00") . " ~ " . kopindosatformatdatetime($enddate ." " . $ehour . ":59");
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);
			$objPHPExcel->getActiveSheet()->SetCellValue('J3', 'Total Record :');
			$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);



			if(isset($jobfile)){
				$objPHPExcel->getActiveSheet()->SetCellValue('A3', "JOB FILE : " .$jobfile);
			}

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Trip No');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Start Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'End Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Duration');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Trip Mileage');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Cumulative Mileage');
			$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Location Start');
			$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'Location End');

			$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//Write Data
			$i=1;
			$new = "";
			foreach($data as $vehicle_no=>$val){
				if($new != $vehicle_no){
					$cumm = 0;
					$trip_no = 1;
				}
				foreach($val as $no=>$report){
					$mileage = $report['end_mileage']- $report['start_mileage'];
					if($mileage != 0){
						$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
						$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $vehicle_no);
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $report['vehicle_name']);
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $trip_no++);
						$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $report['start_time']);
						$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $report['end_time']);
						$duration = get_time_difference($report['start_time'], $report['end_time']);

							$show = "";
							if($duration[0]!=0){
								$show .= $duration[0] ." Day ";
							}
							if($duration[1]!=0){
								$show .= $duration[1] ." Hour ";
							}
							if($duration[2]!=0){
								$show .= $duration[2] ." Min ";
							}

							if($show == ""){
								$show .= "0 Min";
							}
						$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $show);
						$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$tm = round(($mileage/1000),2) . " km";
						$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $tm);
						$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$cumm += $tm;
						$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $cumm . " km");
						$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $report['start_position']->display_name);
						$objPHPExcel->getActiveSheet()->SetCellValue('K'.(5+$i), $report['end_position']->display_name);

						$objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getFont()->setSize(8);
						$objPHPExcel->getActiveSheet()->getStyle('K'.(5+$i))->getFont()->setSize(8);
						$i++;
					}
				}
			}

			$objPHPExcel->getActiveSheet()->SetCellValue('K3', $i-1);
			$objPHPExcel->getActiveSheet()->getStyle('K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('trip_mileage');


			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "Trip_Mileage_".date('YmdHis') . ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
		return;
	}

	function mn_playback()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		//tambahan, user group yg open playback report
		if ($this->sess->user_group <> 0)
		{
			$this->db->where("vehicle_group", $this->sess->user_group);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		//print_r($rows);exit;
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('transporter/report/mn_playback', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function playback_report()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");


		//print_r($vehicle." ".$startdate." ".$enddate." ".$shour." ".$ehour);
		//exit;

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
		{
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		}
		else
		{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate ." " . $ehour . ":59"));
		}


		$now = date("Y-m-d");
		$cek_date = date("Y-m-d", strtotime($startdate));
		$cek_enddate = date("Y-m-d", strtotime($enddate));

		$json = json_decode($rowvehicle->vehicle_info);


		if (($cek_date == $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			if ($rowvehicle->vehicle_info)
			{
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		}
		else if(($cek_date < $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);

			$action = "history";
		}
		else
		{

			if ($rowvehicle->vehicle_info && (!isset($json->vehicle_ws)))
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			if (isset($json->vehicle_ws))
			{
				$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
				$this->db = $this->load->database("gpshistory2", TRUE);
			}



			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			if (isset($json->vehicle_ws))
			{
				$this->db = $this->load->database("default", TRUE);
			}

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);


			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time","asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if (((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) &&
			(!isset($json->vehicle_ws)))
		{
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();


			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows)==0)
			{

				if(isset($action) && $action=="history")
				{
					if ($rowvehicle->vehicle_info)
					{
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							}
							else
							{
								$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists = $this->config->item("table_hist");
								$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db = $this->load->database("default", TRUE);
							}
						}
					}

					if (! isset($table))
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists = $this->config->item("table_hist");
						$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db = $this->load->database("default", TRUE);
					}


					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time","asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan
		}
		else
		{
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;
		}


		//print_r($rows);exit;

			$data = array(); // initialization variable
			$vehicle_device = "";
			$engine = "";

			/* start looping for process data - data dikelompokkan berdasarkan engine status on/off */
			foreach($rows as $obj){
				if($vehicle_device != $rowvehicle->vehicle_device){
					$no=0;
					$no_data = 1;
				}

				if(substr($obj->gps_info_io_port, 4, 1) == 1){ //engine ON
					if($engine != "ON") {
						$no++;
						$no_data = 1;
					}
					if($no == 0) $no++;

					if($no_data == 1){
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['ON']['start'] = $obj;
					}else{
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['ON']['end'] = $obj;
					}
					$no_data++;
					$engine = "ON";
				}

				if(substr($obj->gps_info_io_port, 4, 1) == 0){ //engine OFF
					if($engine != "OFF") {
						$no++;
						$no_data = 1;
					}
					if($no == 0) $no++;

					if($no_data == 1){
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OFF']['start'] = $obj;
					}else{
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OFF']['end'] = $obj;
					}

					$no_data++;
					$engine = "OFF";
				}

				$vehicle_device = $rowvehicle->vehicle_device;
			}
			/* end loop */


			/*
			echo "<pre>";
			print_r($data['engine']);
			echo "</pre>";
			exit;
			*/

			foreach($data as $vehicles=>$value_vehicles){
				foreach($value_vehicles as $number=>$value_number){
					foreach($value_number as $engine=>$report){
						if(!isset($report['end'])){
							$report['end'] = $report['start'];
						}
						$data_report[$vehicles][$number][$engine]['start'] = $report['start'];
						$data_report[$vehicles][$number][$engine]['end'] = $report['end'];

						//print_r($report['end']);exit;

						$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);

		                $show_duration = "";
						if($duration[0]!=0){
		                    $show_duration .= $duration[0] ." Day ";
		                }
		                if($duration[1]!=0){
		                    $show_duration .= $duration[1] ." Hour ";
		                }
		                if($duration[2]!=0){
		                    $show_duration .= $duration[2] ." Min";
		                }
		                if($show_duration == ""){
		                    $show_duration .= "0 Min";
		                }
						$data_report[$vehicles][$number][$engine]['duration'] = $show_duration;

						$mileage = ($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000;
						if($mileage < 0)
						{
							$mileage = ($report['start']->gps_info_distance - $report['end']->gps_info_distance)/1000;
						}

						$data_report[$vehicles][$number][$engine]['mileage'] = round($mileage, 2);

						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
						{
							$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
						}
						else
						{
							$location_start = $this->getPosition_other($report['start']->gps_longitude, $report['start']->gps_latitude);
						}

						$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;

						$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;


						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
						{
							$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
						}
						else
						{
							$location_end = $this->getPosition_other($report['end']->gps_longitude, $report['end']->gps_latitude);
						}

						$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;

						$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
					}
				}
			}

		//print_r($data_report);exit;

		$params['data'] = $data_report;
		$params['vehicle'] = $rowvehicle;

		if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
		{
			$html = $this->load->view("transporter/report/list_result_playback", $params, true);
		}
		else
		{
			$html = $this->load->view("transporter/report/list_result_playback_other", $params, true);
		}

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;

	}

	function playback_report_excel()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");


		//print_r($vehicle." ".$startdate." ".$enddate." ".$shour." ".$ehour);
		//exit;

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315")
		{
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		}
		else
		{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate ." " . $ehour . ":59"));
		}
		$now = date("Y-m-d");
		$cek_date = date("Y-m-d", strtotime($startdate));
		$cek_enddate = date("Y-m-d", strtotime($enddate));

		$json = json_decode($rowvehicle->vehicle_info);

		if (($cek_date == $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			if ($rowvehicle->vehicle_info)
			{
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		}
		else if(($cek_date < $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$action = "history";
		}
		else
		{

			if ($rowvehicle->vehicle_info && (!isset($json->vehicle_ws)))
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			if (isset($json->vehicle_ws))
			{
				$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
				$this->db = $this->load->database("gpshistory2", TRUE);
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			if (isset($json->vehicle_ws))
			{
				$this->db = $this->load->database("default", TRUE);
			}

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time","asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if (((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) &&
			(!isset($json->vehicle_ws)))
		{
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();


			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows)==0)
			{

				if(isset($action) && $action=="history")
				{
					if ($rowvehicle->vehicle_info)
					{
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							}
							else
							{
								$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists = $this->config->item("table_hist");
								$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db = $this->load->database("default", TRUE);
							}
						}
					}

					if (! isset($table))
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists = $this->config->item("table_hist");
						$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db = $this->load->database("default", TRUE);
					}


					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time","asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan
		}
		else
		{
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;
		}

			$data = array(); // initialization variable
			$vehicle_device = "";
			$engine = "";

			/* start looping for process data - data dikelompokkan berdasarkan engine status on/off */
			foreach($rows as $obj){
				if($vehicle_device != $rowvehicle->vehicle_device){
					$no=0;
					$no_data = 1;
				}

				if(substr($obj->gps_info_io_port, 4, 1) == 1){ //engine ON
					if($engine != "ON") {
						$no++;
						$no_data = 1;
					}
					if($no == 0) $no++;

					if($no_data == 1){
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['ON']['start'] = $obj;
					}else{
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['ON']['end'] = $obj;
					}
					$no_data++;
					$engine = "ON";
				}

				if(substr($obj->gps_info_io_port, 4, 1) == 0){ //engine OFF
					if($engine != "OFF") {
						$no++;
						$no_data = 1;
					}
					if($no == 0) $no++;

					if($no_data == 1){
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OFF']['start'] = $obj;
					}else{
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OFF']['end'] = $obj;
					}

					$no_data++;
					$engine = "OFF";
				}

				$vehicle_device = $rowvehicle->vehicle_device;
			}
			/* end loop */


			/*
			echo "<pre>";
			print_r($data['engine']);
			echo "</pre>";
			exit;
			*/

			foreach($data as $vehicles=>$value_vehicles){
				foreach($value_vehicles as $number=>$value_number){
					foreach($value_number as $engine=>$report){
						if(!isset($report['end'])){
							$report['end'] = $report['start'];
						}
						$data_report[$vehicles][$number][$engine]['start'] = $report['start'];
						$data_report[$vehicles][$number][$engine]['end'] = $report['end'];

						$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);

		                $show_duration = "";
						if($duration[0]!=0){
		                    $show_duration .= $duration[0] ." Day ";
		                }
		                if($duration[1]!=0){
		                    $show_duration .= $duration[1] ." Hour ";
		                }
		                if($duration[2]!=0){
		                    $show_duration .= $duration[2] ." Min";
		                }
		                if($show_duration == ""){
		                    $show_duration .= "0 Min";
		                }
						$data_report[$vehicles][$number][$engine]['duration'] = $show_duration;

						$mileage = ($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000;

						if($mileage < 0)
						{
							$mileage = ($report['start']->gps_info_distance - $report['end']->gps_info_distance)/1000;
						}


						$data_report[$vehicles][$number][$engine]['mileage'] = round($mileage, 2);




						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315")
						{
							$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
						}
						else
						{
							$location_start = $this->getPosition_other($report['start']->gps_longitude, $report['start']->gps_latitude);
						}

						$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;

						$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;


						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315")
						{
							$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
						}
						else
						{
							$location_end = $this->getPosition_other($report['end']->gps_longitude, $report['end']->gps_latitude);
						}
						$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;

						$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
					}
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
			$objPHPExcel->getProperties()->setTitle("Playback Report");
			$objPHPExcel->getProperties()->setSubject("Playback Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Playback Report Lacak-mobil.com");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Playback Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Engine');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Start Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'End Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Duration');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Trip Mileage');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Cummulative Mileage');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Location Start');
			$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Location End');

			$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()->getStartColor()->setRGB('bfbfbf;');

			$i=1;

			foreach($data as $vehicles=>$value_vehicles){
				foreach($value_vehicles as $number=>$value_number){
					foreach($value_number as $engine=>$report){
						if(!isset($report['end'])){
							$report['end'] = $report['start'];
						}
						$data_report[$vehicles][$number][$engine]['start'] = $report['start'];
						$data_report[$vehicles][$number][$engine]['end'] = $report['end'];

						$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);

		                $show_duration = "";
						if($duration[0]!=0){
		                    $show_duration .= $duration[0] ." Day ";
		                }
		                if($duration[1]!=0){
		                    $show_duration .= $duration[1] ." Hour ";
		                }
		                if($duration[2]!=0){
		                    $show_duration .= $duration[2] ." Min";
		                }
		                if($show_duration == ""){
		                    $show_duration .= "0 Min";
		                }
						$data_report[$vehicles][$number][$engine]['duration'] = $show_duration;

						$mileage = ($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000;
						$data_report[$vehicles][$number][$engine]['mileage'] = round($mileage, 2);

						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315")
						{
							$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
						}
						else
						{
							$location_start = $this->getPosition_other($report['start']->gps_longitude, $report['start']->gps_latitude);
						}
						$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;

						$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;


						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315")
						{
							$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
						}
						else
						{
							$location_end = $this->getPosition_other($report['end']->gps_longitude, $report['end']->gps_latitude);
						}
						$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;

						$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;

						$cummulative = 0;
						$cummulative += $data_report[$vehicles][$number][$engine]['mileage'];
						//print_r($data_report[$vehicles][$number][$engine]['location_start']->display_name);exit();


						$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
						$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $rowvehicle->vehicle_no);
						$objPHPExcel->getActiveSheet()->getStyle('B'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $engine);
						$objPHPExcel->getActiveSheet()->getStyle('C'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315")
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), date("d/m/Y H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time))));
							$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
						else
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), date("d/m/Y H:i:s", strtotime($report['start']->gps_time)));
							$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}

						if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309PTO" && $rowvehicle->vehicle_type != "GT06PTO" && $rowvehicle->vehicle_type != "TK315")
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), date("d/m/Y H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time))));
							$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
						else
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), date("d/m/Y H:i:s", strtotime($report['end']->gps_time)));
							$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}

						$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $data_report[$vehicles][$number][$engine]['duration']);
						$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $data_report[$vehicles][$number][$engine]['mileage']." "."KM");
						$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $cummulative." "."KM");
						$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

						if (isset($data_report[$vehicles][$number][$engine]['geofence_start']) && ($data_report[$vehicles][$number][$engine]['geofence_start'] != ""))
						{
							$x = $data_report[$vehicles][$number][$engine]['geofence_start'];
							$y = explode("#",$x);

							$valexcel = "Geofence : "." ".$y[1]." ".($data_report[$vehicles][$number][$engine]['location_start']->display_name);
							$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $valexcel);
							$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
						else
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), ($data_report[$vehicles][$number][$engine]['location_start']->display_name));
							$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}

						if (isset($data_report[$vehicles][$number][$engine]['geofence_end']) && ($data_report[$vehicles][$number][$engine]['geofence_end'] != ""))
						{
							$j = $data_report[$vehicles][$number][$engine]['geofence_end'];
							$k = explode("#",$j);
							$valexcel = "Geofence : "." ".$k[1]." ".($data_report[$vehicles][$number][$engine]['location_end']->display_name);
							$objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $valexcel);
							$objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
						else
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), ($data_report[$vehicles][$number][$engine]['location_end']->display_name));
							$objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
					$i++;
					}
				}
			}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Playback_Report');


			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);
			$filecreatedname = "Playback_Report".date('YmdHis') . ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
	}




	//upadte shu 9/23/2012
	function mn_last_location()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		//print_r($rows);exit;
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('indahkiat/report/mn_last_location', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function last_location(){ //$vehicle, $date

			$date = $this->input->post("startdate");
			$type = "";

			//print_r($type);exit;

			$now = date("Y-m-d");
			$fdate = date("Y-m-d", strtotime("-7 hour", strtotime($date)));
			$cek_date = date("Y-m-d", strtotime("-7 hour", strtotime($date)));
			//$cek_date = date("Y-m-d", strtotime($date));


			//all vehicle
			$this->db->order_by("vehicle_name", "asc");
			$this->db->order_by("vehicle_no", "asc");
			$this->db->where("vehicle_status <>", 3);

			if ($this->sess->user_type == 2)
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
				$this->db->or_where("vehicle_company", $this->sess->user_company);
				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			}
			else
			if ($this->sess->user_type == 3)
			{
				$this->db->where("user_agent", $this->sess->user_agent);
			}

			$this->db->join("user", "vehicle_user_id = user_id", "left outer");
			$qm = $this->db->get("vehicle");
			$rm = $qm->result();


			foreach($rm as $v){

				if ($cek_date < $now)
				{
					$this->db->order_by("vehicle_device", "asc");
					$this->db->where("vehicle_device", $v->vehicle_device);
					$this->db->limit(1);
					$qv = $this->db->get("vehicle");
					$rowvehicle = $qv->row();
					$rowv[] = $qv->row();

						//Seleksi Databases
						$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
						$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->dbhistory = $this->load->database($istbl_history, TRUE);

						$this->dbhistory->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
						//$this->db->join("vehicle", "vehicle_device=CONCAT(gps_name,'@',gps_host)");
						$this->dbhistory->where("gps_info_device", $v->vehicle_device);
						$this->dbhistory->order_by("gps_time", "desc");
						$this->dbhistory->limit(1);
						$q = $this->dbhistory->get($table);
						$rows[] = $q->row();
				}
				else
				{
					//print_r("ane disini");exit;
					$this->db->order_by("vehicle_device", "asc");
					$this->db->where("vehicle_device", $v->vehicle_device);
					$this->db->limit(1);
					$qv = $this->db->get("vehicle");
					$rowvehicle = $qv->row();
					$rowv[] = $qv->row();

					if ($rowvehicle->vehicle_info)
						{
							$json = json_decode($rowvehicle->vehicle_info);
							if (isset($json->vehicle_ip) && isset($json->vehicle_port))
							{
								$databases = $this->config->item('databases');
								if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
								{
									$database = $databases[$json->vehicle_ip][$json->vehicle_port];
									$table = $this->config->item("external_gpstable");
									$tableinfo = $this->config->item("external_gpsinfotable");
									$this->tblhist = $this->config->item("external_gpstable_history");
									$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
									$tblinfohist = $this->tblinfohist;
									$this->db = $this->load->database($database, TRUE);
								}
							}
						}

						if (! isset($table))
						{
							$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
							$tableinfo = $this->gpsmodel->getGPSInfoTable($v->vehicle_type);
							$tblhists = $this->config->item("table_hist");
							$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
							$tblhistinfos = $this->config->item("table_hist_info");
							$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						}

					$this->db->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
					//$this->db->join("vehicle", "vehicle_device=CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $v->vehicle_device);
					$this->db->order_by("gps_time", "desc");
					$this->db->limit(1);
					$q = $this->db->get($table);
					$rows[] = $q->row();
					//print_r($rows);exit;
				}
			}

		//end if allvehicle
		//print_r($rows);exit;
		$trows = count($rows);

		for($i=0;$i<$trows;$i++){

			$rows[$i]->result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);

			//Find Vehicle Odometer
			foreach($rowv as $vodo)
			{
				if($vodo->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
				{
					$vodometer = $vodo->vehicle_odometer;
				}
			}

			$rows[$i]->result_gps_odometer = round(($rows[$i]->gps_info_distance+$vodometer*1000)/1000);
			$ioport = $rows[$i]->gps_info_io_port;
			$rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off

			$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
			$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);

			$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");

			foreach($rowv as $vodo)
			{
				if($vodo->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
				{
					$vdevice = $vodo->vehicle_device;
				}
			}

			$rows[$i]->geofence_location = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $vodo->vehicle_device);
			//print_r($rows[$i]->geofence_location);exit;
		}


		if($type=='excel'){

			/** PHPExcel */
			include 'class/PHPExcel.php';

			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';

			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("Last Location Detail Report");
			$objPHPExcel->getProperties()->setSubject("Last Location Detail Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Last Location Detail Repor Lacak-mobil.com");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Last Location Detail Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			//$filter_date = kopindosatformatdatetime($date ." " . $hour . ":59");
			$filter_date = kopindosatformatdatetime($date);
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);
			$objPHPExcel->getActiveSheet()->SetCellValue('J3', 'Total Record :');
			$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('K3', count($rows));
			$objPHPExcel->getActiveSheet()->getStyle('K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Card No');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Location');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'GPS Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Speed (kph)');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
			$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Engine');
			$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'GPS');
			$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//Write Data
			for($i=0;$i<$trows;$i++){
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
				foreach($rowv as $vvehicle)
				{
					if($vvehicle->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $vvehicle->vehicle_no);
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $vvehicle->vehicle_name);
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $vvehicle->vehicle_card_no . " (" . $vvehicle->vehicle_operator . ")");
					}
				}

				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);

				if (isset($rows[$i]->geofence_location) && ($rows[$i]->geofence_location != ""))
				{
					$x = $rows[$i]->geofence_location;
					$y = explode("#",$x);
					$z = $y[1];

					$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), "Geofence :" . " " . $z . " " . $rows[$i]->result_position->display_name);
				}
				else
				{
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->result_position->display_name);
				}
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))));
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), number_format($rows[$i]->gps_speed*1.852, 0, "", ","));
				$objPHPExcel->getActiveSheet()->getStyle('H'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), number_format($rows[$i]->result_gps_odometer, 0,"","."));
				$objPHPExcel->getActiveSheet()->getStyle('I'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$engine = ($rows[$i]->status1) ? $this->lang->line('lon') : $this->lang->line('loff');
				$objPHPExcel->getActiveSheet()->SetCellValue('J'.(6+$i), $engine);
				$gps = ($rows[$i]->gps_status == "V") ? "NO" : "OK";
				$objPHPExcel->getActiveSheet()->SetCellValue('K'.(6+$i), $gps);
			}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(5+$trows))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(5+$trows))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(5+$trows))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('last_location_report');


			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);//REPORT_PATH ada di config/constant.php
			$filecreatedname = "Last_Location_Detail_".date("d-m-Y", strtotime($date )). ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/" . $this->config->item("folder_system"). "/media/report/" . $filecreatedname.'"}';
			echo $output;

		}else{

			//print_r($rows);exit;
			$params['data'] = $rows;
			$params['data_vehicle'] = $rowv;
			$html = $this->load->view("indahkiat/report/list_result_last_location", $params, true);

			$callback['error'] = false;
			$callback['html'] = $html;
			$this->db = $this->load->database("default", TRUE);
			echo json_encode($callback);
		}

		return;

	}


	function last_location_excel(){ //$vehicle, $date

			$date = $this->input->post("startdate");
			$type = $this->input->post("excel");

			//print_r($type);exit;

			$now = date("Y-m-d");
			$fdate = date("Y-m-d", strtotime("-7 hour", strtotime($date)));
			$cek_date = date("Y-m-d", strtotime("-7 hour", strtotime($date)));

			//all vehicle
			$this->db->order_by("vehicle_name", "asc");
			$this->db->order_by("vehicle_no", "asc");
			$this->db->where("vehicle_status <>", 3);

			if ($this->sess->user_type == 2)
			{
				$this->db->where("vehicle_user_id", $this->sess->user_id);
				$this->db->or_where("vehicle_company", $this->sess->user_company);
				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			}
			else
			if ($this->sess->user_type == 3)
			{
				$this->db->where("user_agent", $this->sess->user_agent);
			}

			$this->db->join("user", "vehicle_user_id = user_id", "left outer");
			$qm = $this->db->get("vehicle");
			$rm = $qm->result();


			foreach($rm as $v){

				if ($cek_date < $now)
				{
					$this->db->order_by("vehicle_device", "asc");
					$this->db->where("vehicle_device", $v->vehicle_device);
					$this->db->limit(1);
					$qv = $this->db->get("vehicle");
					$rowvehicle = $qv->row();
					$rowv[] = $qv->row();

						//Seleksi Databases
						$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
						$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
						$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->dbhistory = $this->load->database($istbl_history, TRUE);

						$this->dbhistory->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
						//$this->db->join("vehicle", "vehicle_device=CONCAT(gps_name,'@',gps_host)");
						$this->dbhistory->where("gps_info_device", $v->vehicle_device);
						$this->dbhistory->order_by("gps_time", "desc");
						$this->dbhistory->limit(1);
						$q = $this->dbhistory->get($table);
						$rows[] = $q->row();
				}
				else
				{

					$this->db->order_by("vehicle_device", "asc");
					$this->db->where("vehicle_device", $v->vehicle_device);
					$this->db->limit(1);
					$qv = $this->db->get("vehicle");
					$rowvehicle = $qv->row();
					$rowv[] = $qv->row();

					if ($rowvehicle->vehicle_info)
						{
							$json = json_decode($rowvehicle->vehicle_info);
							if (isset($json->vehicle_ip) && isset($json->vehicle_port))
							{
								$databases = $this->config->item('databases');
								if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
								{
									$database = $databases[$json->vehicle_ip][$json->vehicle_port];
									$table = $this->config->item("external_gpstable");
									$tableinfo = $this->config->item("external_gpsinfotable");
									$this->tblhist = $this->config->item("external_gpstable_history");
									$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
									$tblinfohist = $this->tblinfohist;
									$this->db = $this->load->database($database, TRUE);
								}
							}
						}

						if (! isset($table))
						{
							$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
							$tableinfo = $this->gpsmodel->getGPSInfoTable($v->vehicle_type);
							$tblhists = $this->config->item("table_hist");
							$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
							$tblhistinfos = $this->config->item("table_hist_info");
							$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						}

					$this->db->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
					//$this->db->join("vehicle", "vehicle_device=CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $v->vehicle_device);
					$this->db->order_by("gps_time", "desc");
					$this->db->limit(1);
					$q = $this->db->get($table);
					$rows[] = $q->row();
					//print_r($rows);exit;
				}
			}

		//end if allvehicle
		//print_r($rows);exit;
		$trows = count($rows);

		for($i=0;$i<$trows;$i++){

			$rows[$i]->result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);

			//Find Vehicle Odometer
			foreach($rowv as $vodo)
			{
				if($vodo->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
				{
					$vodometer = $vodo->vehicle_odometer;
				}
			}

			$rows[$i]->result_gps_odometer = round(($rows[$i]->gps_info_distance+$vodometer*1000)/1000);
			$ioport = $rows[$i]->gps_info_io_port;
			$rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off

			$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
			$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);

			$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");

			foreach($rowv as $vodo)
			{
				if($vodo->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
				{
					$vdevice = $vodo->vehicle_device;
				}
			}

			$rows[$i]->geofence_location = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $vodo->vehicle_device);
			//print_r($rows[$i]->geofence_location);exit;
		}


		if($type=='excel'){

			/** PHPExcel */
			include 'class/PHPExcel.php';

			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';

			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("Last Location Detail Report");
			$objPHPExcel->getProperties()->setSubject("Last Location Detail Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Last Location Detail Repor Lacak-mobil.com");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Last Location Detail Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			//$filter_date = kopindosatformatdatetime($date ." " . $hour . ":59");
			$filter_date = kopindosatformatdatetime($date);
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);
			$objPHPExcel->getActiveSheet()->SetCellValue('J3', 'Total Record :');
			$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('K3', count($rows));
			$objPHPExcel->getActiveSheet()->getStyle('K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Card No');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Location');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'GPS Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Speed (kph)');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
			$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Engine');
			$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'GPS');
			$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//Write Data
			for($i=0;$i<$trows;$i++){
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
				foreach($rowv as $vvehicle)
				{
					if($vvehicle->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $vvehicle->vehicle_no);
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $vvehicle->vehicle_name);
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $vvehicle->vehicle_card_no . " (" . $vvehicle->vehicle_operator . ")");
					}
				}

				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);

				if (isset($rows[$i]->geofence_location) && ($rows[$i]->geofence_location != ""))
				{
					$x = $rows[$i]->geofence_location;
					$y = explode("#",$x);
					$z = $y[1];

					$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), "Geofence :" . " " . $z . " " . $rows[$i]->result_position->display_name);
				}
				else
				{
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->result_position->display_name);
				}
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))));
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), number_format($rows[$i]->gps_speed*1.852, 0, "", ","));
				$objPHPExcel->getActiveSheet()->getStyle('H'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), number_format($rows[$i]->result_gps_odometer, 0,"","."));
				$objPHPExcel->getActiveSheet()->getStyle('I'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$engine = ($rows[$i]->status1) ? $this->lang->line('lon') : $this->lang->line('loff');
				$objPHPExcel->getActiveSheet()->SetCellValue('J'.(6+$i), $engine);
				$gps = ($rows[$i]->gps_status == "V") ? "NO" : "OK";
				$objPHPExcel->getActiveSheet()->SetCellValue('K'.(6+$i), $gps);
			}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(5+$trows))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(5+$trows))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(5+$trows))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('last_location_report');


			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);//REPORT_PATH ada di config/constant.php
			$filecreatedname = "Last_Location_Detail_".date("d-m-Y", strtotime($date )). ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/" . $this->config->item("folder_system"). "/media/report/" . $filecreatedname.'"}';
			echo $output;

		}else{


			//print_r($rows);exit;
			$params['data'] = $rows;
			$params['data_vehicle'] = $rowv;
			$html = $this->load->view("indahkiat/report/list_result_last_location", $params, true);

			$callback['error'] = false;
			$callback['html'] = $html;
			$this->db = $this->load->database("default", TRUE);
			echo json_encode($callback);
		}

		return;

	}


	function mn_inout_geofence()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		//print_r($rows);exit;
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('transporter/report/mn_inout_geofence', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function mn_problem_rekap()
	{
		if (! isset($this->sess->user_type)){ redirect(base_url()); }
		$this->params["content"] = $this->load->view('transporter/report/mn_problem_rekap', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function rekap_problem_report()
	{
		$startdate = $this->input->post("startdate");
        $enddate = $this->input->post("enddate");

		$sdate = date("Y-m-d", strtotime($startdate));
        $edate = date("Y-m-d", strtotime($enddate));

        $this->dbtrans = $this->load->database("transporter",true);
        $this->dbtrans->order_by("device_problem_date","desc");
        $this->dbtrans->where("device_problem_status",1);
        $this->dbtrans->where("device_problem_date >=",$sdate);
        $this->dbtrans->where("device_problem_date <=",$edate);
		$q = $this->dbtrans->get("device_problem");
		$rows = $q->result();

		$params['data'] = $rows;
		$html = $this->load->view("transporter/report/rekap_problem_report", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
	}

	function inout_geofence_detail_report()
	{
		$startdate = $this->input->post("startdate");
        $enddate = $this->input->post("enddate");

		$ve = $this->input->post("vehicle");

		$this->db->where("vehicle_device", $ve);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

        $vehicle_nopol = $rowvehicle->vehicle_no;

		$params['vehicle'] = $rowvehicle;

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
        $edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
        //print_r($edate);exit;

        $this->db->order_by("geoalert_time", "asc");
		$this->db->where("geoalert_vehicle", $ve);
		$this->db->where("geoalert_time >=", $sdate);
        $this->db->where("geoalert_time <=", $edate);
		$this->db->join("geofence", "geofence_id = geoalert_geofence", "left outer");
		$q = $this->db->get("geofence_alert");
		$rows = $q->result();

        //print_r($rows);exit;

		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->geoalert_time_t = dbmaketime($rows[$i]->geoalert_time);
		}

		$params['data'] = $rows;
		$html = $this->load->view("transporter/report/inout_geofence_detail_report", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
	}

	function inout_geofence_detail_report_excel()
	{
		$startdate = $this->input->post("startdate");
        $enddate = $this->input->post("enddate");

		$ve = $this->input->post("vehicle");

		$this->db->where("vehicle_device", $ve);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
        $vehicle_nopol = $rowvehicle->vehicle_no;

		$params['vehicle'] = $rowvehicle;

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
        $edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$this->db->order_by("geoalert_time", "asc");
        $this->db->where("geoalert_vehicle", $ve);
        $this->db->where("geoalert_time >=", $sdate);
        $this->db->where("geoalert_time <=", $edate);
        $this->db->join("geofence", "geofence_id = geoalert_geofence", "left outer");
        $q = $this->db->get("geofence_alert");
		$data = $q->result();

		for($i=0; $i < count($data); $i++)
		{
			$data[$i]->geoalert_time_t = dbmaketime($data[$i]->geoalert_time);
		}

		/** PHPExcel */
        include 'class/PHPExcel.php';

        /** PHPExcel_Writer_Excel2007 */
        include 'class/PHPExcel/Writer/Excel2007.php';

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("InOut Geofence Report");
        $objPHPExcel->getProperties()->setSubject("InOut Geofence Report");
		$objPHPExcel->getProperties()->setDescription("InOut Geofence Report");

		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        //$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

		//Header
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'IN-OUT GEOFENCE REPORT');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'NO');
        $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'KELUAR');
        $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'MASUK');
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'DURATION');
        $objPHPExcel->getActiveSheet()->getStyle('A5:D5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A5:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$j = 0;
			for($i=0; $i < count($data); $i++)
			{
                if ($data[$i]->geoalert_direction == 2)
                {
                    if ($data[$i]->geoalert_direction == 2)
                    {
                        $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$j), $j+1);
                        if ($data[$i]->geofence_name)
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$j), $data[$i]->geofence_name . " " . "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t));
                        }
                        else
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$j), $data[$i]->geofence_coordinate . " " . "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t));
                        }
                    }

                    if ($data[$i]->geoalert_direction == 2)
                    {
                        if (isset($data[$i+1]->geofence_name))
                        {
                            if ($data[$i+1]->geofence_name)
                            {
                                $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$j), $data[$i+1]->geofence_name." "."Finish ". " ". date("d/m/Y H:i:s", $data[$i+1]->geoalert_time_t));
                            }
                            else
                            {
                                $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$j), $data[$i+1]->geofence_coordinate." "."Finish ". " ". date("d/m/Y H:i:s", $data[$i+1]->geoalert_time_t));
                            }
                        }
                    }

                    if (isset($data[$i+1]->geofence_name) && $data[$i]->geoalert_direction == 2)
                    {
                        $startdate = new DateTime(date("d-m-Y H:i:s", $data[$i]->geoalert_time_t));
                        $enddate = new DateTime(date("d-m-Y H:i:s", $data[$i+1]->geoalert_time_t));
                        $duration = $startdate->diff($enddate);
                        $d_day = $duration->format('%d');
                        $d_hour = $duration->format('%h');
                        $d_minute = $duration->format('%i');
                        $d_second = $duration->format('%s');
                        if (isset($d_day) && ($d_day > 0))
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$j), $d_day ." ". "Day" ." ".$d_hour ." "."Jam" ." ". $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
                        }
                        else if (isset($d_hour) && ($d_hour > 0))
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$j), $d_hour ." "."Jam" ." ". $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
                        }
                        else
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$j), $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
                        }
                    }
                    $j = $j + 1;
                }
			}

			$styleArray = array(
            'borders' => array(
            'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
            )
            )
            );

            $this->db->cache_delete_all();

            $objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$j))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$j))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$j))->getAlignment()->setWrapText(true);
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            @mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
            $filecreatedname = "InOutGeofenceReport_" . ".xls";

            $objWriter->save(REPORT_PATH.$filecreatedname);

            $output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
            echo $output;
            return;
	}

	function mn_hourmeter()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('transporter/report/mn_hourmeter', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function mn_history_hourmeter()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('transporter/report/mn_history_hourmeter', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function hourmeter_report()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();
		$x = explode("@", $rowvehicle->vehicle_device);
		$name = $x[0];
		$host = $x[1];

		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		$now = date("Y-m-d");
		$cek_enddate = date("Y-m-d", strtotime($enddate));
		$cek_date = date("Y-m-d", strtotime($startdate));

		$json = json_decode($rowvehicle->vehicle_info);

		if (($cek_date == $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			if ($rowvehicle->vehicle_info)
			{
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		}
		else if(($cek_date < $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$action = "history";
		}
		else
		{

			if ($rowvehicle->vehicle_info && (!isset($json->vehicle_ws)))
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			if (isset($json->vehicle_ws))
			{
				$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
				$this->db = $this->load->database("gpshistory2", TRUE);
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			if (isset($json->vehicle_ws))
			{
				$this->db = $this->load->database("default", TRUE);
			}

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time","asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if (((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) &&
			(!isset($json->vehicle_ws)))
		{
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();


			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows)==0)
			{

				if(isset($action) && $action=="history")
				{
					if ($rowvehicle->vehicle_info)
					{
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							}
							else
							{
								$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists = $this->config->item("table_hist");
								$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db = $this->load->database("default", TRUE);
							}
						}
					}

					if (! isset($table))
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists = $this->config->item("table_hist");
						$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db = $this->load->database("default", TRUE);
					}


					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time","asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan
		}
		else
		{
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;

		}


		$data = array();

		$nopol = "";

		$on = false;
		$trows = count($rows);
		//print_r($trows);exit;

		for($i=0;$i<$trows;$i++){
			//if($rows[$i]->gps_speed == 0) continue;

			if($nopol != $rowvehicle->vehicle_no){ //new vehicle
				if($on && $i!=0){
					$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
				}
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					$trip_no = 1;
					$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
					$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
					$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
					$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] =$rowvehicle->vehicle_name;
					$on = true;

					if($i==$trows-1){
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					$trip_no = 1;
					$on = false;

				}


			}else{ //same vehicle
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					if(!$on){

						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle->vehicle_name;
					}

					$on = true;
					if($i==$trows-1 && $on){
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					if($on){
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
					$on = false;

				}
			}

			$nopol = $rowvehicle->vehicle_no;
		}

			$getlast_hourmeter = $this->getlasthourmeter($rowvehicle->vehicle_info, $name, $rowvehicle->vehicle_type);
			$hourmeter = $this->getHourmeter($getlast_hourmeter);
			$now = date("d-m-Y");

			$sdate = date("d-m-Y", strtotime($startdate));
			$ndate = date("d-m-Y", strtotime($enddate));

			$params['startdate'] = $sdate;
			$params['starttime'] = $shour . ":00";
			$params['enddate'] = $ndate;
			$params['endtime'] = $ehour . ":00";
			$params['now'] = $now;
			$params['total_hourmeter'] = $hourmeter;
			$params['data'] = $data;
			$params['vehicle'] = $rowvehicle;
			$html = $this->load->view("transporter/report/list_result_hourmeter", $params, true);
			$callback['error'] = false;
			$callback['html'] = $html;

			echo json_encode($callback);

		return;
	}

	function history_hourmeter_report()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		//print_r($vehicle);exit;
		//Sinkronisasi data kedalam database
		$x_start = $startdate." "."00:00:00";
		$x_end = $enddate." "."23:59:59";


		//Load DB Report Berdikari
		$this->db_report = $this->load->database("report_berdikari", true);

		$this->db_report->where("report_hourmeter_vehicle_no", $vehicle);
		$this->db_report->where("report_hourmeter_start >= ", $x_start);
		$this->db_report->where("report_hourmeter_end <= ", $x_end);

		$q = $this->db_report->get("report_hourmeter");
		$rows = $q->result();

		$params['data'] = $rows;
		$html = $this->load->view("transporter/report/list_result_history_hourmeter", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);
		return;
	}

	//cron report hourmeter berdikari
	function cron_hourmeter_report_berdikari()
	{
		$user_berdikari = "1077";
		$this->db_berdikari = $this->load->database("report_berdikari", true);
		$this->db->where("vehicle_user_id", $user_berdikari);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->result();
		//print_r($rowvehicle);exit;

		$startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
		$enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));

		//Create manual date
		//$startdate = date("2012-08-03 00:00:00");
		//$enddate = date("2012-08-03 23:59:59");

		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));

		for ($x=0;$x<count($rowvehicle);$x++)
		{
			if ($rowvehicle[$x]->vehicle_info)
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
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($$rowvehicle[$x]->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($$rowvehicle[$x]->vehicle_type);
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle[$x]->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();

			$data = array();
			$nopol = "";
			$on = false;
			$trows = count($rows);
			//print_r($trows);exit;

			for($i=0;$i<count($rows);$i++)
			{
				if($nopol != $rowvehicle[$x]->vehicle_no)
				{ //new vehicle
					if($on && $i!=0)
					{
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
					}
					if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
					{
						$trip_no = 1;
						$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
						$on = true;

						if($i==$trows-1)
						{
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
						}
					}
					else
					{
						$trip_no = 1;
						$on = false;
					}
				}
				else
				{ //same vehicle
					if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
					{
						if(!$on)
						{
							$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
						}
						$on = true;
						if($i==$trows-1 && $on)
						{
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
						}
					}
					else
					{
						if($on)
						{
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
						}
						$on = false;
					}
				}
				$nopol = $rowvehicle[$x]->vehicle_no;
			} //end looping
			//print_r($data);exit;
			if(count($data) > 0)
			{

				$j=1;
				$new = "";
				foreach($data as $vehicle_no=>$val)
				{
					if($new != $vehicle_no)
					{
						$cumtime = 0;
						$trip_no = 1;
					}
					foreach($val as $no=>$report)
					{
						unset($insert_data);
						$mileage = $report['end_mileage']- $report['start_mileage'];
						$insert_data["report_hourmeter_vehicle_no"] = $vehicle_no ;
						$insert_data["report_hourmeter_name"] = $report['vehicle_name'];
						$insert_data["report_hourmeter_active"] = $trip_no++;
						$insert_data["report_hourmeter_start"] = $report['start_time'];
						$insert_data["report_hourmeter_end"] = $report['end_time'];
						$duration = get_time_difference($report['start_time'], $report['end_time']);
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
						if ($duration[3]!=0)
						{
							$show .= $duration[3] ." Detik";
						}
						if($show == "")
						{
							$show .= "0 Min";
						}
						$insert_data["report_hourmeter_duration"] = $show;
						$ex = explode(" ",$show);
						if ($ex[1]=="Day")
						{
							$val = $ex[0];
						}
						if ($ex[1]=="Hour")
						{
							$val = $ex[0]*60*60;
							if (isset($ex[2]))
							{
								$val += $ex[2]*60;
							}
							if (isset($ex[4]))
							{
								$val += $ex[4];
							}
						}
						if ($ex[1]=="Min")
						{
							$val = $ex[0]*60;
							if (isset($ex[2]))
							{
								$val += $ex[2];
							}
						}
						if ($ex[1] == "Detik")
						{
							$val = $ex[0];
						}
						if (isset($val))
						{
							$cumtime += $val;
							$cummulative_time = gmdate("H:i:s", $cumtime);
							$insert_data["report_hourmeter_cumulative"] = $cummulative_time;
						}
						else
						{
							$insert_data["report_hourmeter_cumulative"] = "-";
						}
						if ($report['start_geofence_location'])
						{
							$arrGeo = explode("#", $report['start_geofence_location']);
							if(count($arrGeo)>1)
							{
								$geoname = $arrGeo[1];
							}
							else
							{
								$geoname = $arrGeo[0];
							}
							//echo strtoupper($geoname);
							$insert_data["report_hourmeter_location"] = strtoupper($geoname)." ".$report['start_position']->display_name;
						}
						else
						{
							$insert_data["report_hourmeter_location"] = $report['start_position']->display_name;
						}
						$j++;
						$this->db_berdikari->insert('report_hourmeter', $insert_data);
						print_r("Success Insert Data");
						print_r($data);
					}
				}
			}

		} //end looping vehicle
		return;
	}

	//cron report hourmeter berdikari
	//Data dan vehicle diset secara manual
	function cron_hourmeter_report_berdikari_manual()
	{
		//Set Manual Variable
		$gps_name = "002100000980"; //set gps name tanpa type vehicle ex : 002100000001
		$tanggal = "2012-07-06";
		$startdate = date($tanggal." "."00:00:00");
		$enddate = date($tanggal." "."23:59:59");

		//Sinkronisai variable manual ke dalam sistem
		$vehicle_dev = $gps_name."@T5";
		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));

		//Select Vehicle
		$this->db->where("vehicle_device", $vehicle_dev);
		$this->db->limit(1);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		//Load Database History
		$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->dbhistory = $this->load->database($istbl_history, TRUE);

		$this->db_berdikari = $this->load->database("report_berdikari", true);

		//Definisi Table pada database history
		$table = $gps_name."@t5_gps";
		$tableinfo = $gps_name."@t5_info";
		$gps_name_info = $rowvehicle->vehicle_device;

		//Get Data
		$this->dbhistory->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
		$this->dbhistory->where("gps_info_device", $gps_name_info);
		$this->dbhistory->where("gps_time >=", $sdate);
		$this->dbhistory->where("gps_time <=", $edate);
		$this->dbhistory->order_by("gps_time","asc");
		$this->dbhistory->from($table);
		$q = $this->dbhistory->get();
		$this->dbhistory->flush_cache();
		$rows = $q->result();

		$data = array();
		$nopol = "";
		$on = false;
		$trows = count($rows);
		//print_r($trows);exit;

			for($i=0;$i<count($rows);$i++)
			{
				if($nopol != $rowvehicle->vehicle_no)
				{ //new vehicle
					if($on && $i!=0)
					{
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
					}
					if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
					{
						$trip_no = 1;
						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle->vehicle_name;
						$on = true;

						if($i==$trows-1)
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
						}
					}
					else
					{
						$trip_no = 1;
						$on = false;
					}
				}
				else
				{ //same vehicle
					if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
					{
						if(!$on)
						{
							$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
							$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle->vehicle_name;
						}
						$on = true;
						if($i==$trows-1 && $on)
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
						}
					}
					else
					{
						if($on)
						{
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
						}
						$on = false;
					}
				}
				$nopol = $rowvehicle->vehicle_no;
			} //end looping
			//print_r($data);exit;
			if(count($data) > 0)
			{

				$j=1;
				$new = "";
				foreach($data as $vehicle_no=>$val)
				{
					if($new != $vehicle_no)
					{
						$cumtime = 0;
						$trip_no = 1;
					}
					foreach($val as $no=>$report)
					{
						unset($insert_data);
						$mileage = $report['end_mileage']- $report['start_mileage'];
						$insert_data["report_hourmeter_vehicle_no"] = $vehicle_no ;
						$insert_data["report_hourmeter_name"] = $report['vehicle_name'];
						$insert_data["report_hourmeter_active"] = $trip_no++;
						$insert_data["report_hourmeter_start"] = $report['start_time'];
						$insert_data["report_hourmeter_end"] = $report['end_time'];
						$duration = get_time_difference($report['start_time'], $report['end_time']);
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
						if ($duration[3]!=0)
						{
							$show .= $duration[3] ." Detik";
						}
						if($show == "")
						{
							$show .= "0 Min";
						}
						$insert_data["report_hourmeter_duration"] = $show;
						$ex = explode(" ",$show);
						if ($ex[1]=="Day")
						{
							$val = $ex[0];
						}
						if ($ex[1]=="Hour")
						{
							$val = $ex[0]*60*60;
							if (isset($ex[2]))
							{
								$val += $ex[2]*60;
							}
							if (isset($ex[4]))
							{
								$val += $ex[4];
							}
						}
						if ($ex[1]=="Min")
						{
							$val = $ex[0]*60;
							if (isset($ex[2]))
							{
								$val += $ex[2];
							}
						}
						if ($ex[1] == "Detik")
						{
							$val = $ex[0];
						}
						if (isset($val))
						{
							$cumtime += $val;
							$cummulative_time = gmdate("H:i:s", $cumtime);
							$insert_data["report_hourmeter_cumulative"] = $cummulative_time;
						}
						else
						{
							$insert_data["report_hourmeter_cumulative"] = "-";
						}
						if ($report['start_geofence_location'])
						{
							$arrGeo = explode("#", $report['start_geofence_location']);
							if(count($arrGeo)>1)
							{
								$geoname = $arrGeo[1];
							}
							else
							{
								$geoname = $arrGeo[0];
							}
							//echo strtoupper($geoname);
							$insert_data["report_hourmeter_location"] = strtoupper($geoname)." ".$report['start_position']->display_name;
						}
						else
						{
							$insert_data["report_hourmeter_location"] = $report['start_position']->display_name;
						}
						$j++;
						$this->db_berdikari->insert('report_hourmeter', $insert_data);
						print_r("Success Insert Data");
						print_r($data);
					}
				}
			}

		print_r("Finish Process Vehicle".$rowvehicle->vehicle_no." ".$rowvehicle->vehicle_device);
		print_r("Date :"." ".$startdate."-".$enddate);
		print_r("Success");
		return;
	}


	//cron report hourmeter berdikari
	//Data dan vehicle diset secara manual Per User
	function cron_hourmeter_report_berdikari_manual_peruser()
	{
		$user_berdikari = "1077";

		//$this->dbhistory = $this->load->database("gpshistory", true);
		$this->db_berdikari = $this->load->database("report_berdikari", true);

		$this->db->where("vehicle_user_id", $user_berdikari);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->result();
		//print_r($rowvehicle);exit;

		//Create manual date ***
		$startdate = date("2012-07-07 00:00:00");
		$enddate = date("2012-07-07 23:59:59");

		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));

		for ($x=0;$x<count($rowvehicle);$x++)
		{
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
			}
			$this->dbhistory = $this->load->database($istbl_history, TRUE);

			$xi = explode("@",$rowvehicle[$x]->vehicle_device);

			$table = $xi[0]."@t5_gps";
			$tableinfo = $xi[0]."@t5_info";

			$this->dbhistory->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->dbhistory->where_in("gps_info_device", $rowvehicle[$x]->vehicle_device);
			$this->dbhistory->where("gps_time >=", $sdate);
			$this->dbhistory->where("gps_time <=", $edate);
			$this->dbhistory->order_by("gps_time","asc");
			$this->dbhistory->from($table);
			$q = $this->dbhistory->get();
			$this->dbhistory->flush_cache();
			$rows = $q->result();

			$data = array();
			$nopol = "";
			$on = false;
			$trows = count($rows);
			//print_r($trows);exit;

			for($i=0;$i<count($rows);$i++)
			{
				if($nopol != $rowvehicle[$x]->vehicle_no)
				{ //new vehicle
					if($on && $i!=0)
					{
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
					}
					if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
					{
						$trip_no = 1;
						$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
						$on = true;

						if($i==$trows-1)
						{
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
						}
					}
					else
					{
						$trip_no = 1;
						$on = false;
					}
				}
				else
				{ //same vehicle
					if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
					{
						if(!$on)
						{
							$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
						}
						$on = true;
						if($i==$trows-1 && $on)
						{
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
						}
					}
					else
					{
						if($on)
						{
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
							$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
						}
						$on = false;
					}
				}
				$nopol = $rowvehicle[$x]->vehicle_no;
			} //end looping
			//print_r($data);exit;
			if(count($data) > 0)
			{

				$j=1;
				$new = "";
				foreach($data as $vehicle_no=>$val)
				{
					if($new != $vehicle_no)
					{
						$cumtime = 0;
						$trip_no = 1;
					}
					foreach($val as $no=>$report)
					{
						unset($insert_data);
						$mileage = $report['end_mileage']- $report['start_mileage'];
						$insert_data["report_hourmeter_vehicle_no"] = $vehicle_no ;
						$insert_data["report_hourmeter_name"] = $report['vehicle_name'];
						$insert_data["report_hourmeter_active"] = $trip_no++;
						$insert_data["report_hourmeter_start"] = $report['start_time'];
						$insert_data["report_hourmeter_end"] = $report['end_time'];
						$duration = get_time_difference($report['start_time'], $report['end_time']);
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
						if ($duration[3]!=0)
						{
							$show .= $duration[3] ." Detik";
						}
						if($show == "")
						{
							$show .= "0 Min";
						}
						$insert_data["report_hourmeter_duration"] = $show;
						$ex = explode(" ",$show);
						if ($ex[1]=="Day")
						{
							$val = $ex[0];
						}
						if ($ex[1]=="Hour")
						{
							$val = $ex[0]*60*60;
							if (isset($ex[2]))
							{
								$val += $ex[2]*60;
							}
							if (isset($ex[4]))
							{
								$val += $ex[4];
							}
						}
						if ($ex[1]=="Min")
						{
							$val = $ex[0]*60;
							if (isset($ex[2]))
							{
								$val += $ex[2];
							}
						}
						if ($ex[1] == "Detik")
						{
							$val = $ex[0];
						}
						if (isset($val))
						{
							$cumtime += $val;
							$cummulative_time = gmdate("H:i:s", $cumtime);
							$insert_data["report_hourmeter_cumulative"] = $cummulative_time;
						}
						else
						{
							$insert_data["report_hourmeter_cumulative"] = "-";
						}
						if ($report['start_geofence_location'])
						{
							$arrGeo = explode("#", $report['start_geofence_location']);
							if(count($arrGeo)>1)
							{
								$geoname = $arrGeo[1];
							}
							else
							{
								$geoname = $arrGeo[0];
							}
							//echo strtoupper($geoname);
							$insert_data["report_hourmeter_location"] = strtoupper($geoname)." ".$report['start_position']->display_name;
						}
						else
						{
							$insert_data["report_hourmeter_location"] = $report['start_position']->display_name;
						}
						$j++;
						$this->db_berdikari->insert('report_hourmeter', $insert_data);
						print_r("Success Insert Data");
						print_r($data);
					}
				}
			}

		} //end looping vehicle
		return;
	}

	function hourmeter_report_excel()
	{

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		$now = date("Y-m-d");
		$cek_enddate = date("Y-m-d", strtotime($enddate));
		$cek_date = date("Y-m-d", strtotime($startdate));

		$json = json_decode($rowvehicle->vehicle_info);

		if (($cek_date == $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			if ($rowvehicle->vehicle_info)
			{
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		}
		else if(($cek_date < $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws)))
		{
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$action = "history";
		}
		else
		{

			if ($rowvehicle->vehicle_info && (!isset($json->vehicle_ws)))
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			if (isset($json->vehicle_ws))
			{
				$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
				$this->db = $this->load->database("gpshistory2", TRUE);
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			if (isset($json->vehicle_ws))
			{
				$this->db = $this->load->database("default", TRUE);
			}

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time","asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if (((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) &&
			(!isset($json->vehicle_ws)))
		{
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();


			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows)==0)
			{

				if(isset($action) && $action=="history")
				{
					if ($rowvehicle->vehicle_info)
					{
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							}
							else
							{
								$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists = $this->config->item("table_hist");
								$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db = $this->load->database("default", TRUE);
							}
						}
					}

					if (! isset($table))
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists = $this->config->item("table_hist");
						$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db = $this->load->database("default", TRUE);
					}


					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time","asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan
		}
		else
		{
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;

		}

		$data = array();

		$nopol = "";

		$on = false;
		$trows = count($rows);

		for($i=0;$i<$trows;$i++){
			//if($rows[$i]->gps_speed == 0) continue;

			if($nopol != $rowvehicle->vehicle_no){ //new vehicle
				if($on && $i!=0){
					$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
				}
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					$trip_no = 1;
					$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
					$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
					$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
					$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] =$rowvehicle->vehicle_name;
					$on = true;

					if($i==$trows-1){
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					$trip_no = 1;
					$on = false;

				}


			}else{ //same vehicle
				if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1){
					if(!$on){

						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						$data[$rowvehicle->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle->vehicle_name;
					}

					$on = true;
					if($i==$trows-1 && $on){
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
				}else{
					if($on){
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						$data[$rowvehicle->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
					}
					$on = false;

				}
			}

			$nopol = $rowvehicle->vehicle_no;
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
			$objPHPExcel->getProperties()->setTitle("Hourmeter Report");
			$objPHPExcel->getProperties()->setSubject("Hourmeter Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Hourmeter Report Lacak-mobil.com");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);

			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Hourmeter Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$filter_date = kopindosatformatdatetime($startdate ." " . $shour . ":00") . " ~ " . kopindosatformatdatetime($enddate ." " . $ehour . ":59");
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Act');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Start Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'End Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Duration');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Cummulative');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Location');

			$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//Write Data
			$i=1;
			$new = "";
			foreach($data as $vehicle_no=>$val){
				if($new != $vehicle_no){
					$cumtime = 0;
					$trip_no = 1;
				}
				foreach($val as $no=>$report){
					$mileage = $report['end_mileage']- $report['start_mileage'];

						$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
						$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $vehicle_no);
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $report['vehicle_name']);
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $trip_no++);
						$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $report['start_time']);
						$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $report['end_time']);
						$duration = get_time_difference($report['start_time'], $report['end_time']);

							$show = "";
							if($duration[0]!=0){
								$show .= $duration[0] ." Day ";
							}
							if($duration[1]!=0){
								$show .= $duration[1] ." Hour ";
							}
							if($duration[2]!=0){
								$show .= $duration[2] ." Min ";
							}
							if ($duration[3]!=0){
								$show .= $duration[3] ." Detik";
								}
								if($show == ""){
									$show .= "0 Min";
									}

						$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $show);


						$ex = explode(" ",$show);
						if ($ex[1]=="Day")
						{
							$val = $ex[0];
						}
						if ($ex[1]=="Hour")
						{
						$val = $ex[0]*60*60;

						if (isset($ex[2]))
						{
							$val += $ex[2]*60;
						}

						if (isset($ex[4]))
						{
							$val += $ex[4];
						}

						}
						if ($ex[1]=="Min")
						{
						$val = $ex[0]*60;
						if (isset($ex[2]))
						{
							$val += $ex[2];
						}
						}
						if ($ex[1] == "Detik")
						{
						$val = $ex[0];
						}

						$cumtime += $val;
						$cummulative_time = gmdate("H:i:s", $cumtime);

						$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $cummulative_time);
						$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $report['start_position']->display_name);
						$i++;

				}
			}


			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Hourmeter');


			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "Hourmeter_Report_".date('YmdHis') . ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
		return;
	}

	function history_hourmeter_report_excel()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		//print_r($vehicle);exit;
		//Sinkronisasi data kedalam database
		$x_start = $startdate." "."00:00:00";
		$x_end = $enddate." "."23:59:59";

		//Load DB Report Berdikari
		$this->db_report = $this->load->database("report_berdikari", true);

		$this->db_report->where("report_hourmeter_vehicle_no", $vehicle);
		$this->db_report->where("report_hourmeter_start >= ", $x_start);
		$this->db_report->where("report_hourmeter_end <= ", $x_end);

		$q = $this->db_report->get("report_hourmeter");
		$rows = $q->result();

		/** PHPExcel */
			include 'class/PHPExcel.php';

			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';

			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("Hourmeter Report");
			$objPHPExcel->getProperties()->setSubject("Hourmeter Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Hourmeter Report Lacak-mobil.com");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(45);

			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'History Hourmeter Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Act');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Start Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'End Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Duration');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Cummulative');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Location');

			$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$i = 1;
			for ($j=0;$j<count($rows);$j++)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $rows[$j]->report_hourmeter_vehicle_no);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $rows[$j]->report_hourmeter_name);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $rows[$j]->report_hourmeter_active);
				$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $rows[$j]->report_hourmeter_start);
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $rows[$j]->report_hourmeter_end);
				$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $rows[$j]->report_hourmeter_duration);
				$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $rows[$j]->report_hourmeter_cumulative);
				$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $rows[$j]->report_hourmeter_location);
				$i++;
			}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('History Hourmeter Report');

			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "History_Hourmeter_Report_".date('YmdHis') . ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
	}

	function ritase_report_excel()
	{
		$vehicle_device = $this->input->post("vehicle");

		$startdate = $this->input->post("date");
        $sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));

        $enddate = $this->input->post("enddate");
        $edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$ritase = $this->input->post("ritase");
		$exRitase = explode(",",$ritase);
		$ritase_id = $exRitase[0];
		$ritase_name = $exRitase[1];

		$this->db->order_by("geoalert_time", "asc");
        $this->db->where("geoalert_vehicle", $vehicle_device);
        $this->db->where("geoalert_time >=", $sdate);
        $this->db->where("geoalert_time <=", $edate);
        $this->db->join("geofence", "geofence_id = geoalert_geofence", "leftouter");
        $this->db->where("geofence_name", $ritase_name);
        $q = $this->db->get("geofence_alert");
        $data = $q->result();

		$this->db->cache_delete_all();

		for ($i=0;$i<count($data);$i++)
		{
			$data[$i]->geoalert_time_t = dbmaketime($data[$i]->geoalert_time);
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
			$objPHPExcel->getProperties()->setTitle("Ritase Report");
			$objPHPExcel->getProperties()->setSubject("Ritase Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Ritase Report  Lacak-mobil.com");

		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);

		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'RITASE REPORT');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->SetCellValue('A5', '*');
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'KELUAR');
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'MASUK');
		$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'DURATION');
		$objPHPExcel->getActiveSheet()->getStyle('A5:D5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A5:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$totalritase = 0;
		for($i=0; $i < count($data); $i++)
		{
			if ($data[$i]->geoalert_direction == 2 && isset($data[$i+1]->geofence_name))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), "*");
				if ($data[$i]->geofence_name)
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $data[$i]->geofence_name . " " . "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t));
				}
				else
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $data[$i]->geofence_coordinate . " " . "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t));
				}

				if ($data[$i]->geoalert_direction == 2)
				{
					if (isset($data[$i+1]->geofence_name))
					{
						if ($data[$i+1]->geofence_name)
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $data[$i+1]->geofence_name." "."Finish ". " ". date("d/m/Y H:i:s", $data[$i+1]->geoalert_time_t));
						}
						else
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $data[$i+1]->geofence_coordinate." "."Finish ". " ". date("d/m/Y H:i:s", $data[$i+1]->geoalert_time_t));
						}
					}
					else
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), "-");
					}
				}
				if (isset($data[$i+1]->geofence_name))
				{
					$startdate = new DateTime(date("d-m-Y H:i:s", $data[$i]->geoalert_time_t));
					$enddate = new DateTime(date("d-m-Y H:i:s", $data[$i+1]->geoalert_time_t));
					$duration = $startdate->diff($enddate);
					$d_day = $duration->format('%d');
					$d_hour = $duration->format('%h');
					$d_minute = $duration->format('%i');
					$d_second = $duration->format('%s');
					if (isset($d_day) && ($d_day > 0))
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $d_day ." ". "Day" ." ".$d_hour ." "."Jam" ." ". $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
					}
					else if (isset($d_hour) && ($d_hour > 0))
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $d_hour ." "."Jam" ." ". $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
					}
					else
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
					}
				}
				else
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), "-");
				}
				$totalritase += 1;
				$j = $i+1;
			}
		}
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.(7+$j), "TOTAL RITASE : "." ".$totalritase);

		$styleArray = array(
			'borders' => array(
            'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
            )
            )
            );

		$objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$i))->getAlignment()->setWrapText(true);
		// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "Ritase_Report_".date('YmdHis') . ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
		return;
	}

	function driver_hist_report()
	{
		$this->dbtransporter = $this->load->database("transporter", true);

		$vehicle           = $this->input->post("vehicle");
		$startdate         = $this->input->post("startdate");
		$enddate           = $this->input->post("enddate");
		$shour             = $this->input->post("shour");
		$ehour             = $this->input->post("ehour");
		$driver            = $this->input->post("driver");

		$sdate             = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
		$edate             = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":00"));

		// echo "<pre>";
		// var_dump($vehicle.'-'.$driver.'-'.$sdate.'-'.$edate);die();
		// echo "<pre>";

			// $this->dbtransporter->where("driver_history_tanggal_submit >=", $sdate);
			// $this->dbtransporter->where("driver_history_tanggal_submit <=", $edate);

			if ($vehicle != 0)
			{
				$this->dbtransporter->where("driver_history_vehicle_id", $vehicle);
			}
			if ($driver != 0)
			{
				$this->dbtransporter->where("driver_history_driver_id", $driver);
			}


			$this->dbtransporter->where("driver_history_tanggal_submit >=", $sdate);
			$this->dbtransporter->where("driver_history_tanggal_submit <=", $edate);
			$this->dbtransporter->where("driver_history_creator", $this->sess->user_id);
			$this->dbtransporter->order_by("driver_history_tanggal_submit","desc");
			$this->dbtransporter->order_by("driver_history_vehicle_id","asc");
			$this->dbtransporter->order_by("driver_history_driver_id","asc");
			$q    = $this->dbtransporter->get("driver_history");
			$rows = $q->result();

			// print_r($rows);exit();
			 	// print_r($vehicle.$startdate.$enddate.$shour.$ehour.$driver);

			$driver = $this->getDriver();

			$params['data']    = $rows;
			$params['drivers'] = $driver;
			$params['vehicle'] = $vehicle;
			$html              = $this->load->view("transporter/report/list_result_driver_hist", $params, true);
			$callback['error'] = false;
			$callback['html']  = $html;
			echo json_encode($callback);
	}

	function driver_hist_report_excel()
	{
		$vehicle   = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate   = $this->input->post("enddate");
		$shour     = $this->input->post("shour");
		$ehour     = $this->input->post("ehour");
		$driver    = $this->input->post("driver");

		$sdate = date("d-m-Y H:i:s", strtotime($startdate . " " . $shour . ":00"));
		$edate = date("d-m-Y H:i:s", strtotime($enddate . " " . $ehour . ":00"));

		//print_r($sdate." ".$edate);
		//print_r($vehicle.$startdate.$enddate.$shour.$ehour.$driver);

		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where("driver_hist_company", $this->sess->user_company);
		$this->dbtransporter->where("driver_hist_date >=", $sdate);
		$this->dbtransporter->where("driver_hist_date <=", $edate);

		if ($vehicle != 0)
		{
			$this->dbtransporter->where("driver_hist_vehicle", $vehicle);
		}
		if ($driver != 0)
		{
			$this->dbtransporter->where("driver_hist_driver", $driver);
		}

		$this->dbtransporter->order_by("driver_hist_date","desc");
		$this->dbtransporter->order_by("driver_hist_vehicle","asc");
		$this->dbtransporter->order_by("driver_hist_driver","asc");
		$q = $this->dbtransporter->get("hist_driver");
		$rows = $q->result();
		$driver = $this->getDriver();

		/** PHPExcel */
			include 'class/PHPExcel.php';
			include 'class/PHPExcel/Writer/Excel2007.php';
			$objPHPExcel = new PHPExcel();

			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("Driver History Report");
			$objPHPExcel->getProperties()->setSubject("Driver History Report");
			$objPHPExcel->getProperties()->setDescription("Driver History Report");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);

			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Driver History Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$filter_date = kopindosatformatdatetime($startdate ." " . $shour . ":00") . " ~ " . kopindosatformatdatetime($enddate ." " . $ehour . ":59");
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Driver');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Working Date');

			$objPHPExcel->getActiveSheet()->getStyle('A5:E5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:E5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		for ($i=0;$i<count($rows);$i++)
		{
			$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $i+1);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $rows[$i]->driver_hist_vehicle_name." ".$rows[$i]->driver_hist_vehicle_no);

			if (isset($drivers))
					{
						foreach($drivers as $driver)
						{
							if ($driver->driver_id == $data[$i]->driver_hist_driver)
							{
								$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $driver->driver_name);
							}
						}
					}

			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $rows[$i]->driver_hist_date);
			$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}

		$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Driver History');


			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "Driver_History_Report_".date('YmdHis') . ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
	}

	function delete_hist_driver()
	{
		$id = $this->uri->segment(3);
		//print_r($id);
		if ($id)
		{
			$this->dbtransporter = $this->load->database("transporter", true);
			$this->dbtransporter->where("driver_hist_id", $id);
			$this->dbtransporter->delete("hist_driver");
			$this->dbtransporter->flush_cache();
			$this->dbtransporter->close();
		}
		redirect(base_url());
	}

	function mn_driver_hist()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();

		$driver_company = $this->sess->user_company;
		$driver_group = $this->sess->user_group;

		$this->dbtransporter = $this->load->database('transporter', true);
		if($driver_group == 0){
			$this->dbtransporter->where("driver_company", $driver_company);
		}else{
			$this->dbtransporter->where("driver_group", $driver_group);
		}
		$this->dbtransporter->where("driver_status", 1);
		$this->dbtransporter->orderby("driver_name","asc");
		$q = $this->dbtransporter->get("driver");
		$driver = $q->result();
		// print_r($driver->result());exit();

		//$driver = $this->getDriver();
		$this->params["vehicles"] = $rows;
		$this->params["drivers"] = $driver;
		$this->params["content"] = $this->load->view('transporter/report/mn_driver_hist', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function getDriver()
	{
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where('driver_company', $this->sess->user_company);
		$q = $this->dbtransporter->get("driver");
		$rows = $q->result();
		return $rows;

	}

	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_user) {

		$this->db = $this->load->database("default", true);
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);

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

	function getPosition($longitude, $ew, $latitude, $ns){
		$gps_longitude_real = getLongitude($longitude, $ew);
		$gps_latitude_real = getLatitude($latitude, $ns);

		$gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
		$gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");

		$georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);

		return $georeverse;
	}

	function getPosition_other($longitude, $latitude)
	{
		$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);
		return $georeverse;
	}

	function getHourmeter($value)
	{
			$totstring = strlen($value);
			$getstr = substr($value, -8);
			$conval = hexdec($getstr);

			if ($conval > 172800)
			{
				$format = 'j \d\a\y\s H:i:s';
			}
			else if ($conval > 86400)
			{
				$format = 'j \d\a\y H:i:s';
			}
			else
			{
				$format = 'H:i:s';
			}
			$val = gmdate($format, $conval);

			return $val;
	}

	function getlasthourmeter($v, $name, $vtype)
	{
		if ($v)
			{
				$json = json_decode($v);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($vtype);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($vtype);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($vtype)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($vtype)];
			}

			$this->db->select("gps_msg_ori");
			$this->db->where("gps_name", $name);
			$this->db->order_by("gps_time","desc");
			$this->db->limit(1);
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->row();
			if (isset($rows->gps_msg_ori))
			{
				return $rows->gps_msg_ori;
			}
	}

	function tool_move_data_gps()
	{
		$this->dbhistory = $this->load->database("gpshistory", true);

		$gps_name = "002100000980";
		$this->db->select('*');
		$this->db->where('gps_name',$gps_name);
		$q = $this->db->get('gps_t5');

		$rows = $q->result();
		//print_r($rows);exit;

		unset($data);
		for ($i=0;$i<count($rows);$i++)
		{
		$data['gps_name'] = $rows[$i]->gps_name;
		$data['gps_host'] = $rows[$i]->gps_host;
		$data['gps_type'] = $rows[$i]->gps_type;
		$data['gps_utc_coord'] = $rows[$i]->gps_utc_coord;
		$data['gps_status'] = $rows[$i]->gps_status;
		$data['gps_latitude'] = $rows[$i]->gps_latitude;
		$data['gps_ns'] = $rows[$i]->gps_ns;
		$data['gps_longitude'] = $rows[$i]->gps_longitude;
		$data['gps_ew'] = $rows[$i]->gps_ew;
		$data['gps_speed'] = $rows[$i]->gps_speed;
		$data['gps_course'] = $rows[$i]->gps_course;
		$data['gps_utc_date'] = $rows[$i]->gps_utc_date;
		$data['gps_mvd'] = $rows[$i]->gps_mvd;
		$data['gps_mv'] = $rows[$i]->gps_mv;
		$data['gps_cs'] = $rows[$i]->gps_cs;
		$data['gps_msg_ori'] = $rows[$i]->gps_msg_ori;
		$data['gps_time'] = $rows[$i]->gps_time;
		$data['gps_latitude_real'] = $rows[$i]->gps_latitude_real;
		$data['gps_longitude_real'] = $rows[$i]->gps_longitude_real;
		$data['gps_odometer'] = $rows[$i]->gps_odometer;
		$data['gps_workhour'] = $rows[$i]->gps_workhour;

		$this->dbhistory->insert('002100000980@t5_gps', $data);
		}
		echo "SUCCES !";

	}

	function tool_move_data_info()
	{
		$this->dbhistory = $this->load->database("gpshistory", true);

		$gps_info_device = "002100000987@T5";
		$this->db->select('*');
		$this->db->where('gps_info_device',$gps_info_device);
		$q = $this->db->get('gps_info_t5');

		$rows = $q->result();
		//print_r($rows);exit;

		unset($data);
		for ($i=0;$i<count($rows);$i++)
		{
		$data['gps_info_device'] = $rows[$i]->gps_info_device;
		$data['gps_info_hdop'] = $rows[$i]->gps_info_hdop;
		$data['gps_info_io_port'] = $rows[$i]->gps_info_io_port;
		$data['gps_info_distance'] = $rows[$i]->gps_info_distance;
		$data['gps_info_alarm_data'] = $rows[$i]->gps_info_alarm_data;
		$data['gps_info_ad_input'] = $rows[$i]->gps_info_ad_input;
		$data['gps_info_utc_coord'] = $rows[$i]->gps_info_utc_coord;
		$data['gps_info_utc_date'] = $rows[$i]->gps_info_utc_date;
		$data['gps_info_alarm_alert'] = $rows[$i]->gps_info_alarm_alert;
		$data['gps_info_time'] = $rows[$i]->gps_info_time;
		$data['gps_info_status'] = $rows[$i]->gps_info_status;
		$data['gps_info_gps'] = $rows[$i]->gps_info_gps;


		$this->dbhistory->insert('002100000987@t5_info', $data);
		}
		echo "SUCCES !";

	}

	function vehiclelist_excel()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "mobil_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";

		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("mobil_company", $my_company);

		switch($field)
		{
			case "mobil_no":
				$this->dbtransporter->where("mobil_no LIKE '%".$keyword."%'", null);
			break;
			case "mobil_name":
				$this->dbtransporter->where("mobil_name LIKE '%".$keyword."%'", null);
			break;
			case "mobil_model":
				$this->dbtransporter->where("mobil_model LIKE '%".$keyword."%'", null);
			break;
			case "mobil_year":
				$this->dbtransporter->where("mobil_year LIKE '%".$keyword."%'", null);
			break;
			case "mobil_insurance_no":
				$this->dbtransporter->where("mobil_insurance_no LIKE '%".$keyword."%'", null);
			break;
			case "mobil_stnk_no":
				$this->dbtransporter->where("mobil_stnk_no LIKE '%".$keyword."%'", null);
			break;
			case "mobil_no_rangka":
				$this->dbtransporter->where("mobil_no_rangka LIKE '%".$keyword."%'", null);
			break;
			case "mobil_no_mesin":
				$this->dbtransporter->where("mobil_no_mesin LIKE '%".$keyword."%'", null);
			break;
			case "mobil_no_kir":
				$this->dbtransporter->where("mobil_no_kir LIKE '%".$keyword."%'", null);
			break;

		}

		$q = $this->dbtransporter->get("mobil");
		$rows = $q->result();

		//Get fuel
		$this->dbtransporter->where("fuel_status",1);
		$qfuel = $this->dbtransporter->get("fuel_type");
		$row_fuel = $qfuel->result();


			/** PHPExcel */
			include 'class/PHPExcel.php';

			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';

			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("Vehicle List");
			$objPHPExcel->getProperties()->setSubject("Vehicle List");
			$objPHPExcel->getProperties()->setDescription("Vehicle List");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(25);


			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:V1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Vehicle List');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Mobil Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Mobil No');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Model');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Engine Capacity');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Year');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Receive Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Fuel Type');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Insurance No');
			$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Insurance Expired Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'Fuel Consumption');
			$objPHPExcel->getActiveSheet()->SetCellValue('L5', 'STNK');
			$objPHPExcel->getActiveSheet()->SetCellValue('M5', 'STNK Expired Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('N5', 'No Rangka');
			$objPHPExcel->getActiveSheet()->SetCellValue('O5', 'No Mesin');
			$objPHPExcel->getActiveSheet()->SetCellValue('P5', 'No KIR');
			$objPHPExcel->getActiveSheet()->SetCellValue('Q5', 'KIR Expired Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('R5', 'No SIPA');
			$objPHPExcel->getActiveSheet()->SetCellValue('S5', 'SIPA Expired Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('T5', 'No IBM');
			$objPHPExcel->getActiveSheet()->SetCellValue('U5', 'IBM Expired Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('V5', 'Merk');


			$objPHPExcel->getActiveSheet()->getStyle('A5:V5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:V5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			for ($i=0; $i < count($rows); $i++)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $rows[$i]->mobil_name);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $rows[$i]->mobil_no);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rows[$i]->mobil_model);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->mobil_engine_capacity);
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->mobil_year);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->mobil_registration_date);

				foreach ($row_fuel as $fuel)
				{
					if ($fuel->fuel_type_id == $rows[$i]->mobil_fuel_type)
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), $fuel->fuel_type);
					}
				}

				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->mobil_insurance_no);
				$objPHPExcel->getActiveSheet()->SetCellValue('J'.(6+$i), $rows[$i]->mobil_insurance_expired_date);
				$objPHPExcel->getActiveSheet()->SetCellValue('K'.(6+$i), $rows[$i]->mobil_fuel_consumption);
				$objPHPExcel->getActiveSheet()->SetCellValue('L'.(6+$i), $rows[$i]->mobil_stnk_no);
				$objPHPExcel->getActiveSheet()->SetCellValue('M'.(6+$i), $rows[$i]->mobil_stnk_expired);
				$objPHPExcel->getActiveSheet()->SetCellValue('N'.(6+$i), $rows[$i]->mobil_no_rangka);
				$objPHPExcel->getActiveSheet()->SetCellValue('O'.(6+$i), $rows[$i]->mobil_no_mesin);
				$objPHPExcel->getActiveSheet()->SetCellValue('P'.(6+$i), $rows[$i]->mobil_no_kir);
				$objPHPExcel->getActiveSheet()->SetCellValue('Q'.(6+$i), $rows[$i]->mobil_kir_active_date);
				$objPHPExcel->getActiveSheet()->SetCellValue('R'.(6+$i), $rows[$i]->mobil_no_sipa);
				$objPHPExcel->getActiveSheet()->SetCellValue('S'.(6+$i), $rows[$i]->mobil_sipa_expired);
				$objPHPExcel->getActiveSheet()->SetCellValue('T'.(6+$i), $rows[$i]->mobil_no_ibm);
				$objPHPExcel->getActiveSheet()->SetCellValue('U'.(6+$i), $rows[$i]->mobil_ibm_expired);
				$objPHPExcel->getActiveSheet()->SetCellValue('V'.(6+$i), $rows[$i]->mobil_merk);

			}

			$objPHPExcel->getActiveSheet()->getStyle('K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);

			$objPHPExcel->getActiveSheet()->getStyle('A5:V'.(5+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:V'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:V'.(5+$i))->getAlignment()->setWrapText(true);

			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Vehicle List');

			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "Vehicle_List".date('YmdHis') . ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
	}

	function getvehicle()
	{
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$this->db->where("vehicle_user_id",$this->sess->user_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);
		$this->db->where("vehicle_status <>",3);
		$qv = $this->db->get("vehicle");
		$rv = $qv->result();

		$params["myvehicles"] = $rv;

		$html = $this->load->view('trackers/positionsummary', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;

		echo json_encode($callback);
	}

	function positionsummary()
	{
		$date = isset($_POST['date']) ? $_POST['date'] : 0;
		$hour = isset($_POST['hour']) ? $_POST['hour'] : 0;
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;

		$now = date("Y-m-d");
		$fdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));
		$cek_date = date("Y-m-d", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));


		if( $vehicle == 0 )
		{
			//all vehicle
			$this->db->order_by("vehicle_name", "asc");
			$this->db->order_by("vehicle_no", "asc");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			$qm = $this->db->get("vehicle");
			$rm = $qm->result();

			foreach($rm as $v)
			{
				$this->db->order_by("vehicle_device", "asc");
				$this->db->where("vehicle_device", $v->vehicle_device);
				$this->db->limit(1);
				$qv = $this->db->get("vehicle");
				$rowvehicle = $qv->row();
				$rowv[] = $qv->row();

						//Seleksi Databases
						$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
						$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

						$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->dbhistory = $this->load->database($istbl_history, TRUE);


						$this->dbhistory->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
						$this->dbhistory->where("gps_info_device", $v->vehicle_device);
						$this->dbhistory->where("gps_time <=",$fdate);
						$this->dbhistory->order_by("gps_time", "desc");
						$this->dbhistory->limit(1);
						$q = $this->dbhistory->get($table);
						$rows[] = $q->row();
			}
		} //end if allvehicle

		//per vehicle atau lebih
		else
		{
			$this->db->order_by("vehicle_name", "asc");
			$this->db->order_by("vehicle_no", "asc");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_device", $vehicle);
			$this->db->limit(1);
			$qm = $this->db->get("vehicle");
			$rm = $qm->row();
			$rowv[] = $qm->row();

					//Seleksi Databases
					$table = sprintf("%s_gps", strtolower($rm->vehicle_device));
					$tableinfo = sprintf("%s_info", strtolower($rm->vehicle_device));
					$this->dbhistory = $this->load->database("gpshistory2", TRUE);

					$this->dbhistory->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->dbhistory->where("gps_info_device", $rm->vehicle_device);
					$this->dbhistory->where("gps_time <=",$fdate);
					$this->dbhistory->order_by("gps_time", "desc");
					$this->dbhistory->limit(1);
					$q = $this->dbhistory->get($table);
					$rows[] = $q->row();


		}



		$trows = count($rows);

		for($i=0;$i<$trows;$i++){

			if (isset($rows[$i]->gps_longitude))
			{
				$rows[$i]->result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			}


			//Find Vehicle Odometer
			foreach($rowv as $vodo)
			{
				if (isset($rows[$i]->gps_name))
				{
					if($vodo->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
					{
						$vodometer = $vodo->vehicle_odometer;
					}
				}
			}

			if (isset($rows[$i]->gps_info_distance))
			{
				$rows[$i]->result_gps_odometer = round(($rows[$i]->gps_info_distance+$vodometer*1000)/1000);
			}

			if (isset($rows[$i]->gps_info_io_port))
			{
				$ioport = $rows[$i]->gps_info_io_port;
				$rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
			}

			if (isset($rows[$i]->gps_longitude))
			{
				$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
			}

			if (isset($rows[$i]->gps_latitude))
			{
				$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			}

			if (isset($rows[$i]->gps_longitude_real))
			{
				$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			}

			if (isset($rows[$i]->gps_latitude_real))
			{
				$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
			}


			foreach($rowv as $vodo)
			{
				if (isset($rows[$i]->gps_name))
				{
					if($vodo->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
					{
						$vdevice = $vodo->vehicle_device;
					}
				}
			}

			if (isset($rows[$i]->gps_longitude))
			{
				$rows[$i]->geofence_location = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $vodo->vehicle_device);
				//print_r($rows[$i]->geofence_location);exit;
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
			$objPHPExcel->getProperties()->setTitle("Last Location Detail Report");
			$objPHPExcel->getProperties()->setSubject("Last Location Detail Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Last Location Detail Repor Lacak-mobil.com");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Last Location Detail Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$filter_date = kopindosatformatdatetime($date ." " . $hour . ":59");
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);
			$objPHPExcel->getActiveSheet()->SetCellValue('J3', 'Total Record :');
			$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('K3', count($rows));
			$objPHPExcel->getActiveSheet()->getStyle('K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Card No');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Location');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'GPS Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Speed (kph)');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
			$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Engine');
			$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'GPS');
			$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//Write Data
			for($i=0;$i<$trows;$i++){
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
				foreach($rowv as $vvehicle)
				{
					if (isset($rows[$i]->gps_name))
					{
						if($vvehicle->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $vvehicle->vehicle_no);
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $vvehicle->vehicle_name);
							$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $vvehicle->vehicle_card_no . " (" . $vvehicle->vehicle_operator . ")");
						}
					}
				}

				if (isset($rows[$i]->gps_latitude_real_fmt))
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);
				}


				if (isset($rows[$i]->geofence_location) && ($rows[$i]->geofence_location != ""))
				{
					$x = $rows[$i]->geofence_location;
					$z = $x;

					$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), "Geofence :" . " " . $z . " " . $rows[$i]->result_position->display_name);
				}
				else
				{
					if (isset($rows[$i]->result_position->display_name))
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->result_position->display_name);
					}
				}

				if (isset($rows[$i]->gps_time))
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))));
				}

				if (isset($rows[$i]->gps_speed))
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), number_format($rows[$i]->gps_speed*1.852, 0, "", ","));
					$objPHPExcel->getActiveSheet()->getStyle('H'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				}

				if (isset($rows[$i]->result_gps_odometer))
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), number_format($rows[$i]->result_gps_odometer, 0,"","."));
					$objPHPExcel->getActiveSheet()->getStyle('I'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				}

				if (isset($rows[$i]->status1))
				{
					$engine = ($rows[$i]->status1) ? $this->lang->line('lon') : $this->lang->line('loff');
					$objPHPExcel->getActiveSheet()->SetCellValue('J'.(6+$i), $engine);
				}

				if (isset($rows[$i]->gps_status))
				{
					$gps = ($rows[$i]->gps_status == "V") ? "NO" : "OK";
					$objPHPExcel->getActiveSheet()->SetCellValue('K'.(6+$i), $gps);
				}
			}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(5+$trows))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(5+$trows))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(5+$trows))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('last_location_report');

			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "Last_Location_Detail_".date("d-m-Y", strtotime($date )). ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
	}

	function mn_history_trip_mileage()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('transporter/report/mn_history_trip_mileage', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function mn_history_trip_mileage_kml()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('transporter/report/mn_history_trip_mileage_kml', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	//for trip summary
	function mn_trip_mileage_summary()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params["content"] = $this->load->view('transporter/report/mn_history_trip_mileage_summary', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	//

	//export trip summary rexa
	function history_trip_mileage_summary_excel(){

		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$no_cn = "";
		$total = 0;

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		$date_compare = date("Y-m-d", strtotime($startdate));

		$this->DB2 = $this->load->database('transporter', TRUE);

		$this->DB2->order_by("history_trip_mileage_id", "asc");
		$this->DB2->where("history_trip_mileage_start_time >=", $sdate);
		$this->DB2->where("history_trip_mileage_end_time <=", $edate);
		$this->DB2->where("history_trip_mileage_vehicle_no <>", "0");

		if($startdate == $enddate){
			$this->DB2->or_where("history_trip_mileage_date_create", $date_compare);
		}

		$q = $this->DB2->get("transporter_trip_mileage_summary");
		$rows = $q->result();
//		print_r(count($rows));exit;


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
			$objPHPExcel->getProperties()->setTitle("Trip Mileage Summary Report");
			$objPHPExcel->getProperties()->setSubject("Trip Mileage Summary Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Trip Mileage Summary Report Lacak-mobil.com");

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
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(55);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(55);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(55);

			$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', "TRIP MILEAGE SUMMARY REPORT");
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);

			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'NO');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'VEHICLE NO');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'NO CO');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'VEHICLE NAME');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'START TIME');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'END TIME');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'DURATION');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'TRIP MILEAGE ');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'LOCATION START');
			$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'LOCATION END');

			$top = 5;
			$objPHPExcel->getActiveSheet()->getStyle('A'.$top.':i'.$top)->getFont()->setBold(true);
			$k=1;
			$no_urut = 1;

			for ($i=0;$i<count($data);$i++)
			{

				if (isset($data[$i]->history_trip_mileage_start_time))
				{
					$date_cn = date("Y-m-d", strtotime($data[$i]->history_trip_mileage_start_time));
					//Cari CN
					$this->DB2->where("destination_vehicle_no",$data[$i]->history_trip_mileage_vehicle_no);
					$this->DB2->where("destination_date",$date_cn);
					$qc = $this->DB2->get("destination_reksa");
					$rc = $qc->result();
					$total = count($rc);

				}

				if($total > 0)
				{
					for($x=0; $x < $total; $x++)
					{
						$no_cn = $rc[$x]->destination_name1;
						if (isset($rc[$x+1]->destination_name1))
						{
							$no_cn .= ",";
						}
					}
				}
				else
				{
					$no_cn = "";
				}


				$objPHPExcel->getActiveSheet()->SetCellValue('A'.($top+$k), $no_urut);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.($top+$k), $data[$i]->history_trip_mileage_vehicle_no);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.($top+$k), $no_cn);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.($top+$k), $data[$i]->history_trip_mileage_vehicle_name);


				$t_start = $data[$i]->history_trip_mileage_start_time;

				if($t_start != "" || $t_start != null){
					$time_start_trip = date("d-m-Y H:i:s", strtotime($t_start));
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.($top+$k), $time_start_trip);
				}else{
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.($top+$k), "");
				}


				$t_end = $data[$i]->history_trip_mileage_end_time;

				if($t_end != "" || $t_end != null){
					$time_start_trip = date("d-m-Y H:i:s", strtotime($t_end));
					$objPHPExcel->getActiveSheet()->SetCellValue('F'.($top+$k), $time_start_trip);
				}else{
					$objPHPExcel->getActiveSheet()->SetCellValue('F'.($top+$k), "");
				}



				$objPHPExcel->getActiveSheet()->SetCellValue('G'.($top+$k), $data[$i]->history_trip_mileage_duration);
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.($top+$k), $data[$i]->history_trip_mileage_trip_mileage);
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.($top+$k), $data[$i]->history_trip_mileage_location_start);
				$objPHPExcel->getActiveSheet()->SetCellValue('J'.($top+$k), $data[$i]->history_trip_mileage_location_end);

				$no_urut++;
				$k++;

			}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);

			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.($top+$k))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.($top+$k))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.($top+$k))->getAlignment()->setWrapText(true);

			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);
			$filecreatedname = "Trip Mileage Summary Report".date('YmdHis') . ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/" . $this->config->item("folder_system"). "/media/report/" . $filecreatedname.'"}';

		}else{
			$output = '{"success":false,"errMsg":"Data empty..."}';
		}

		echo $output;

	}

	function history_trip_mileage()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$report = "tripmileage_";

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$m1 = date("F", strtotime($startdate));
		$m2 = date("F", strtotime($enddate));
		$year = date("Y", strtotime($startdate));
		$rows = array();
		$rows2 = array();

		switch ($m1)
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

		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			break;
		}

		$this->db->select("vehicle_device");
		$this->db->from("vehicle");

		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();

		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}

		$rv = $qv->result();
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrip = $this->load->database("tripmileage",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			$q = $this->dbtrip->get($dbtable);
			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbtrip = $this->load->database("tripmileage",true);
				$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
				$q2 = $this->dbtrip->get($dbtable2);
				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}
		}

		if($m1 != $m2)
		{
			$params['data'] = $rowsall;
		}
		else
		{
			$params['data'] = $rows;
		}

		$html = $this->load->view("transporter/report/list_result_history_trip_mileage", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;

	}

	function history_trip_mileage_excel()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$report = "tripmileage_";

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$m1 = date("F", strtotime($startdate));
		$m2 = date("F", strtotime($enddate));
		$year = date("Y", strtotime($startdate));
		$rows = array();
		$rows2 = array();
		$data = array();

		switch ($m1)
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

		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			break;
		}

		$this->db->select("vehicle_device");
		$this->db->from("vehicle");

		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();

		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}

		$rv = $qv->result();
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrip = $this->load->database("tripmileage",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			$q = $this->dbtrip->get($dbtable);
			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbtrip = $this->load->database("tripmileage",true);
				$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
				$q2 = $this->dbtrip->get($dbtable2);
				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}
		}

		if($m1 != $m2)
		{
			$data = $rowsall;
		}
		else
		{
			$data = $rows;
		}

		$total = count($data);

		$this->db->cache_delete_all();
		$this->dbtrip->cache_delete_all();

		/** PHPExcel */
		include 'class/PHPExcel.php';

		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("Trip Mileage Detail Report");
		$objPHPExcel->getProperties()->setSubject("Trip Mileage Detail Report Lacak-mobil.com");
		$objPHPExcel->getProperties()->setDescription("Trip Mileage Detail Repor Lacak-mobil.com");

		//set document
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Trip Mileage Report');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('C3', $startdate." "."-"." ".$enddate);
		$objPHPExcel->getActiveSheet()->SetCellValue('J3', "Total Record :");
		$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);

		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle No');
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Trip No');
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Start Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'End Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Duration');
		$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Trip Mileage');
		$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Cumulative Mileage');
		$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Location Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'Location End');

		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$i=1;
		for($j=0;$j<count($data);$j++)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $data[$j]->trip_mileage_vehicle_no);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $data[$j]->trip_mileage_vehicle_name);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $data[$j]->trip_mileage_trip_no);
			$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $data[$j]->trip_mileage_start_time);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $data[$j]->trip_mileage_end_time);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $data[$j]->trip_mileage_duration);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $data[$j]->trip_mileage_trip_mileage." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $data[$j]->trip_mileage_cummulative_mileage." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $data[$j]->trip_mileage_location_start);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.(5+$i), $data[$j]->trip_mileage_location_end);
			$objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getFont()->setSize(8);
			$objPHPExcel->getActiveSheet()->getStyle('K'.(5+$i))->getFont()->setSize(8);
			$i++;
		}

		$objPHPExcel->getActiveSheet()->SetCellValue('K3', $i-1);
		$objPHPExcel->getActiveSheet()->getStyle('K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$styleArray = array(
				'borders' => array(
				'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);

		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setWrapText(true);
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('trip_mileage');

		// Save Excel
		$filedate = date("Ymd",strtotime($startdate))."_".date("Ymd",strtotime($enddate));
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
		$filecreatedname = "Trip_Mileage_".$filedate . ".xls";

		$objWriter->save(REPORT_PATH.$filecreatedname);

		$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
		echo $output;
		return;


	}

	function mn_door_status()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_type", "T5DOOR");

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_type", "T5DOOR");
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('transporter/report/mn_door_status', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}


	function doorstatus_report()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");


		//print_r($vehicle." ".$startdate." ".$enddate." ".$shour." ".$ehour);
		//exit;

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		$now = date("Y-m-d");
		$cek_date = date("Y-m-d", strtotime($startdate));
		$cek_enddate = date("Y-m-d", strtotime($enddate));

		if (($cek_date == $now) && ($cek_date == $cek_enddate))
		{
			if ($rowvehicle->vehicle_info)
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		}
		else if(($cek_date < $now) && ($cek_date == $cek_enddate))
		{
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$action = "history";
		}
		else
		{

			if ($rowvehicle->vehicle_info)
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time","asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if ((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate)))
		{
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();

			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows)==0)
			{

				if(isset($action) && $action=="history")
				{
					if ($rowvehicle->vehicle_info)
					{
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							}
							else
							{
								$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists = $this->config->item("table_hist");
								$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db = $this->load->database("default", TRUE);
							}
						}
					}

					if (! isset($table))
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists = $this->config->item("table_hist");
						$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db = $this->load->database("default", TRUE);
					}


					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time","asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan

		}
		else
		{
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;
		}

			$data = array(); // initialization variable
			$vehicle_device = "";
			$door = "";
			//$this->getFanStatus($row->gps->gps_msg_ori);
			//$value = substr($val, 79, 1);


			/* start looping for process data - data dikelompokkan berdasarkan Door Status Open/Close */
			foreach($rows as $obj){
				if($vehicle_device != $rowvehicle->vehicle_device){
					$no=0;
					$no_data = 1;
				}

				if(substr($obj->gps_msg_ori, 79, 1) == 1){ //Door Open
					if($door != "OPEN") {
						$no++;
						$no_data = 1;
					}
					if($no == 0) $no++;

					if($no_data == 1){
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OPEN']['start'] = $obj;
					}else{
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OPEN']['end'] = $obj;
					}
					$no_data++;
					$door = "OPEN";
				}

				if(substr($obj->gps_msg_ori, 79, 1) == 0){ //Door Close
					if($door != "CLOSE") {
						$no++;
						$no_data = 1;
					}
					if($no == 0) $no++;

					if($no_data == 1){
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['CLOSE']['start'] = $obj;
					}else{
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['CLOSE']['end'] = $obj;
					}

					$no_data++;
					$door = "CLOSE";
				}

				$vehicle_device = $rowvehicle->vehicle_device;
			}
			/* end loop */


			/*
			echo "<pre>";
			print_r($data['door']);
			echo "</pre>";
			exit;
			*/

			foreach($data as $vehicles=>$value_vehicles){
				foreach($value_vehicles as $number=>$value_number){
					foreach($value_number as $door=>$report){
						if(!isset($report['end'])){
							$report['end'] = $report['start'];
						}
						$data_report[$vehicles][$number][$door]['start'] = $report['start'];
						$data_report[$vehicles][$number][$door]['end'] = $report['end'];

						$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);

		                $show_duration = "";
						if($duration[0]!=0){
		                    $show_duration .= $duration[0] ." Day ";
		                }
		                if($duration[1]!=0){
		                    $show_duration .= $duration[1] ." Hour ";
		                }
		                if($duration[2]!=0){
		                    $show_duration .= $duration[2] ." Min";
		                }
		                if($show_duration == ""){
		                    $show_duration .= "0 Min";
		                }
						$data_report[$vehicles][$number][$door]['duration'] = $show_duration;

						/*$mileage = ($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000;
						$data_report[$vehicles][$number][$door]['mileage'] = round($mileage, 2); */

						//start report
						$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
						$data_report[$vehicles][$number][$door]['location_start'] = $location_start;

						$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
						$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;

						$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;

						//end report

						$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
						$data_report[$vehicles][$number][$door]['location_end'] = $location_end;

						$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
						$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;

						$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;

					}
				}
			}



		$params['data'] = $data_report;
		$params['vehicle'] = $rowvehicle;
		$html = $this->load->view("transporter/report/list_result_door_status", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;

	}

	function doorstatus_report_excel()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");


		//print_r($vehicle." ".$startdate." ".$enddate." ".$shour." ".$ehour);
		//exit;

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		$now = date("Y-m-d");
		$cek_date = date("Y-m-d", strtotime($startdate));
		$cek_enddate = date("Y-m-d", strtotime($enddate));

		if (($cek_date == $now) && ($cek_date == $cek_enddate))
		{
			if ($rowvehicle->vehicle_info)
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		}
		else if(($cek_date < $now) && ($cek_date == $cek_enddate))
		{
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$action = "history";
		}
		else
		{

			if ($rowvehicle->vehicle_info)
			{
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port))
				{
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->tblhist = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist = $this->tblinfohist;
						$this->db = $this->load->database($database, TRUE);
					}
				}
			}

			if (! isset($table))
			{
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time","asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if ((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate)))
		{
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time","asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();

			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows)==0)
			{

				if(isset($action) && $action=="history")
				{
					if ($rowvehicle->vehicle_info)
					{
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port))
						{
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
							{
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							}
							else
							{
								$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists = $this->config->item("table_hist");
								$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db = $this->load->database("default", TRUE);
							}
						}
					}

					if (! isset($table))
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists = $this->config->item("table_hist");
						$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db = $this->load->database("default", TRUE);
					}


					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time","asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan

		}
		else
		{
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;
		}

			$data = array(); // initialization variable
			$vehicle_device = "";
			$door = "";
			//$this->getFanStatus($row->gps->gps_msg_ori);
			//$value = substr($val, 79, 1);


			/* start looping for process data - data dikelompokkan berdasarkan Door Status Open/Close */
			foreach($rows as $obj){
				if($vehicle_device != $rowvehicle->vehicle_device){
					$no=0;
					$no_data = 1;
				}

				if(substr($obj->gps_msg_ori, 79, 1) == 1){ //Door Open
					if($door != "OPEN") {
						$no++;
						$no_data = 1;
					}
					if($no == 0) $no++;

					if($no_data == 1){
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OPEN']['start'] = $obj;
					}else{
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['OPEN']['end'] = $obj;
					}
					$no_data++;
					$door = "OPEN";
				}

				if(substr($obj->gps_msg_ori, 79, 1) == 0){ //Door Close
					if($door != "CLOSE") {
						$no++;
						$no_data = 1;
					}
					if($no == 0) $no++;

					if($no_data == 1){
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['CLOSE']['start'] = $obj;
					}else{
						$data[$rowvehicle->vehicle_no."#".$rowvehicle->vehicle_name][$no]['CLOSE']['end'] = $obj;
					}

					$no_data++;
					$door = "CLOSE";
				}

				$vehicle_device = $rowvehicle->vehicle_device;
			}
			/* end loop */


			/*
			echo "<pre>";
			print_r($data['door']);
			echo "</pre>";
			exit;
			*/

			foreach($data as $vehicles=>$value_vehicles){
				foreach($value_vehicles as $number=>$value_number){
					foreach($value_number as $door=>$report){
						if(!isset($report['end'])){
							$report['end'] = $report['start'];
						}
						$data_report[$vehicles][$number][$door]['start'] = $report['start'];
						$data_report[$vehicles][$number][$door]['end'] = $report['end'];

						$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);

		                $show_duration = "";
						if($duration[0]!=0){
		                    $show_duration .= $duration[0] ." Day ";
		                }
		                if($duration[1]!=0){
		                    $show_duration .= $duration[1] ." Hour ";
		                }
		                if($duration[2]!=0){
		                    $show_duration .= $duration[2] ." Min";
		                }
		                if($show_duration == ""){
		                    $show_duration .= "0 Min";
		                }
						$data_report[$vehicles][$number][$door]['duration'] = $show_duration;

						/*$mileage = ($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000;
						$data_report[$vehicles][$number][$door]['mileage'] = round($mileage, 2); */

						$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
						$data_report[$vehicles][$number][$door]['location_start'] = $location_start;

						$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
						$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;

						$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;

						$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
						$data_report[$vehicles][$number][$door]['location_end'] = $location_end;

						$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
						$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;

						$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;
					}
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
			$objPHPExcel->getProperties()->setTitle("Door Status Report");
			$objPHPExcel->getProperties()->setSubject("Door Status Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Door Status Report Lacak-mobil.com");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Door Status Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Door');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Start Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'End Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Duration');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Coordinate Start');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Location Start');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Coordinate End');
			$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Location End');

			$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()->getStartColor()->setRGB('bfbfbf;');

			$i=1;

			foreach($data as $vehicles=>$value_vehicles){
				foreach($value_vehicles as $number=>$value_number){
					foreach($value_number as $engine=>$report){
						if(!isset($report['end'])){
							$report['end'] = $report['start'];
						}
						$data_report[$vehicles][$number][$engine]['start'] = $report['start'];
						$data_report[$vehicles][$number][$engine]['end'] = $report['end'];

						$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);

		                $show_duration = "";
						if($duration[0]!=0){
		                    $show_duration .= $duration[0] ." Day ";
		                }
		                if($duration[1]!=0){
		                    $show_duration .= $duration[1] ." Hour ";
		                }
		                if($duration[2]!=0){
		                    $show_duration .= $duration[2] ." Min";
		                }
		                if($show_duration == ""){
		                    $show_duration .= "0 Min";
		                }
						$data_report[$vehicles][$number][$engine]['duration'] = $show_duration;

						//$mileage = ($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000;
						//$data_report[$vehicles][$number][$engine]['mileage'] = round($mileage, 2);

						$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
						$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;

						$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
						$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
						$coordinate_start = $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real;

						$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
						$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;

						$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
						$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
						$coordinate_end = $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real;

						//$cummulative = 0;
						//$cummulative += $data_report[$vehicles][$number][$engine]['mileage'];
						//print_r($data_report[$vehicles][$number][$engine]['location_start']->display_name);exit();


						$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
						$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $rowvehicle->vehicle_no);
						$objPHPExcel->getActiveSheet()->getStyle('B'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $engine);
						$objPHPExcel->getActiveSheet()->getStyle('C'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), date("d/m/Y H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time))));
						$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), date("d/m/Y H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time))));
						$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $data_report[$vehicles][$number][$engine]['duration']);
						$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

						$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $report['start']->gps_latitude_real." ".$report['start']->gps_longitude_real);
						$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

						if (isset($data_report[$vehicles][$number][$engine]['geofence_start']) && ($data_report[$vehicles][$number][$engine]['geofence_start'] != ""))
						{
							$x = $data_report[$vehicles][$number][$engine]['geofence_start'];
							$y = explode("#",$x);

							$valexcel = "Geofence : "." ".$y[1]." ".($data_report[$vehicles][$number][$engine]['location_start']->display_name);
							$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $valexcel);
							$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
						else
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), ($data_report[$vehicles][$number][$engine]['location_start']->display_name));
							$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}

						$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $report['end']->gps_latitude_real." ".$report['end']->gps_longitude_real);
						$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

						if (isset($data_report[$vehicles][$number][$engine]['geofence_end']) && ($data_report[$vehicles][$number][$engine]['geofence_end'] != ""))
						{
							$j = $data_report[$vehicles][$number][$engine]['geofence_end'];
							$k = explode("#",$j);
							$valexcel = "Geofence : "." ".$k[1]." ".($data_report[$vehicles][$number][$engine]['location_end']->display_name);
							$objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $valexcel);
							$objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
						else
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), ($data_report[$vehicles][$number][$engine]['location_end']->display_name));
							$objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
					$i++;
					}
				}
			}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Door_Status_Report');


			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);
			$filecreatedname = "DoorStatus_Report".date('YmdHis') . ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
	}

	function export_id_booking()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "booking_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$booking_vehicle = isset($_POST['booking_vehicle']) ? $_POST['booking_vehicle'] : "";
		$booking_driver = isset($_POST['booking_driver']) ? $_POST['booking_driver'] : "";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$startdate_loading = isset($_POST['startdate_loading']) ? $_POST['startdate_loading'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$enddate_loading = isset($_POST['enddate_loading']) ? $_POST['enddate_loading'] : "";
		$booking_delivery_status = isset($_POST["booking_delivery_status"]) ? $_POST["booking_delivery_status"] : "";
		$starttime = isset($_POST["starttime"]) ? $_POST["starttime"] : "";
		$starttime_loading = isset($_POST["starttime_loading"]) ? $_POST["starttime_loading"] : "";
		$endtime = isset($_POST["endtime"]) ? $_POST["endtime"] : "";
		$endtime_loading = isset($_POST["endtime_loading"]) ? $_POST["endtime_loading"] : "";
		$booking_loading = isset($_POST["booking_loading"]) ? $_POST["booking_loading"] : 0;

		$startdate_fmt = date("Y-m-d",strtotime($startdate));
		$enddate_fmt = date("Y-m-d",strtotime($enddate));

		$startdate_fmt_loading = date("Y-m-d",strtotime($startdate_loading));
		$enddate_fmt_loading = date("Y-m-d",strtotime($enddate_loading));

		$t1 = date("Y-m-d H:i:s",strtotime($startdate." ".$starttime.":00"));
		$t2 = date("Y-m-d H:i:s",strtotime($enddate." ".$endtime.":00"));

		$t1_loading = date("Y-m-d H:i:s",strtotime($startdate_loading." ".$starttime_loading.":00"));
		$t2_loading = date("Y-m-d H:i:s",strtotime($enddate_loading." ".$endtime_loading.":00"));

		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;


			$vehicle = $this->get_all_vehicle();
			$driver = $this->get_all_driver();
			$all_company = $this->get_all_company();
			$timecontrol = $this->get_timecontrol();
			$company = $all_company;


		$this->dbtransporter->order_by("booking_id","desc");

		switch($field)
		{
			case "booking_id":
				$this->dbtransporter->where("booking_id like", '%'.$keyword.'%');
			break;
			case "booking_destination":
				$this->dbtransporter->where("booking_destination like", '%'.$keyword.'%');
			break;
			case "booking_armada_type":
				$this->dbtransporter->where("booking_armada_type like", '%'.$keyword.'%');
			break;
			case "booking_cbm_loading":
				$this->dbtransporter->where("booking_cbm_loading like", '%'.$keyword.'%');
			break;
			case "booking_vehicle":
				$this->dbtransporter->where("booking_vehicle like", '%'.$booking_vehicle.'%');
			break;
			case "booking_driver":
				$this->dbtransporter->where("booking_driver like", '%'.$booking_driver.'%');
			break;
			case "booking_date_in":
				$this->dbtransporter->where("booking_date_in >= ", $startdate_fmt);
				$this->dbtransporter->where("booking_date_in <= ", $enddate_fmt);
			break;
			case "booking_time_in":
				$this->dbtransporter->where("booking_time_in like", '%'.$keyword.'%');
			break;
			case "booking_warehouse":
				$this->dbtransporter->where("booking_warehouse like", '%'.$keyword.'%');
			break;
			case "booking_delivery_status":
				$this->dbtransporter->where("booking_delivery_status",$booking_delivery_status);
			break;
			case "booking_datetime_in":
				$this->dbtransporter->where("booking_datetime_in >=",$t1);
				$this->dbtransporter->where("booking_datetime_in <=",$t2);
			break;
			case "booking_loading":
				$this->dbtransporter->where("booking_loading",$booking_loading);
			break;
			case "booking_loading_date":
				$this->dbtransporter->where("booking_loading_date >=",$t1_loading);
				$this->dbtransporter->where("booking_loading_date <=",$t2_loading);
			break;
		}

		if (!$this->config->item("app_tupperware"))
		{
			$this->dbtransporter->where("booking_company", $my_company);
		}

		$this->dbtransporter->where("booking_status", 1);
		$this->dbtransporter->join("tupper_barcode","transporter_barcode = booking_id","left_outer");
		$this->dbtransporter->join("tupper_dr","transporter_dr_booking_id = booking_id","left_outer");
		$q = $this->dbtransporter->get("id_booking");
		$data = $q->result();

		$typearmada = $this->get_typearmada();

		/** PHPExcel */
		include 'class/PHPExcel.php';

		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("ID Booking");
		$objPHPExcel->getProperties()->setSubject("ID Booking");
		$objPHPExcel->getProperties()->setDescription("ID Booking");

		//set document
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(55);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(55);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(55);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);

		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:S1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'ID BOOKING REPORT');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);

		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Transporter');
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'ID Booking');
		$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Destination');
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Armada Type');
		$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Vehicle');
		$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Driver');
		$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'CDM Loading');
		$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'TGL Masuk Gudang');
		$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Jam Masuk Gudang');
		$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'Tujuan Gudang');
		$objPHPExcel->getActiveSheet()->SetCellValue('L5', 'Loading Date Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('M5', 'Delivered Date');
		$objPHPExcel->getActiveSheet()->SetCellValue('N5', 'Delivered Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('O5', 'Note');
		$objPHPExcel->getActiveSheet()->SetCellValue('P5', 'Slcars');

		$objPHPExcel->getActiveSheet()->SetCellValue('Q5', 'SO');
		$objPHPExcel->getActiveSheet()->SetCellValue('R5', 'DR');
		$objPHPExcel->getActiveSheet()->SetCellValue('S5', 'DBCode');

		if(count($data) > 0)
		{
			for($i=0; $i < count($data); $i++)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
				$objPHPExcel->getActiveSheet()->getStyle('A'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				if (isset($company))
				{
					foreach ($company as $c)
					{
						if ($c->company_id == $data[$i]->booking_company)
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $c->company_name);
						}
					}
				}

				if ($data[$i]->booking_delivery_status == 2)
				{
					$a = $data[$i]->booking_id.","."Delivered";
				}
				else if ($data[$i]->booking_loading == 1)
				{
					$a = $data[$i]->booking_id.","."Loading";
				}
				else
				{
					$a = $data[$i]->booking_id;
				}
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $a);
				$objPHPExcel->getActiveSheet()->getStyle('C'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

				$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $data[$i]->booking_destination);

				if (isset($data))
				{
					for($x=0;$x<count($typearmada);$x++)
					{
						if ($typearmada[$x]->typearmada_id == $data[$i]->booking_armada_type)
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $typearmada[$x]->typearmada_name);
						}
					}
				}

				if (isset($vehicle))
				{
					foreach($vehicle as $v)
					{
						if ($v->vehicle_device == $data[$i]->booking_vehicle)
						{
							$a = $v->vehicle_name." ".$v->vehicle_no;
							$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $a);
						}
					}
				}

				if (isset($driver))
				{
					foreach($driver as $d)
					{
						if ($d->driver_id == $data[$i]->booking_driver)
						{
							$a = $d->driver_name.",".$d->driver_mobile;
							$objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $a);
						}
					}
				}

				$objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), $data[$i]->booking_cbm_loading);

				if (isset($data))
				{
					$a = date("d-m-Y",strtotime($data[$i]->booking_date_in));
					$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $a);
				}

				foreach($timecontrol as $t)
				{
					if (isset($data) && $data[$i]->booking_time_in == $t->time)
					{
						$a = $t->time;
						$objPHPExcel->getActiveSheet()->SetCellValue('J'.(6+$i), $a);
					}
				}

				$objPHPExcel->getActiveSheet()->SetCellValue('K'.(6+$i), $data[$i]->booking_warehouse);

				if (isset($data[$i]->booking_loading_date))
				{
					$a = date("d-m-Y H:i:s",strtotime($data[$i]->booking_loading_date));
					$objPHPExcel->getActiveSheet()->SetCellValue('L'.(6+$i), $a);
				}

				if (isset($data[$i]->booking_delivered_datetime))
				{
					$a = date("d/m/Y",strtotime($data[$i]->booking_delivered_datetime));
					$objPHPExcel->getActiveSheet()->SetCellValue('M'.(6+$i), $a);
					$objPHPExcel->getActiveSheet()->getStyle('M'.(6+$i))->getNumberFormat()->setFormatCode('dd/mm/yyyy');
				}

				if (isset($data[$i]->booking_delivered_datetime))
				{
					$a = date("H:i:s",strtotime($data[$i]->booking_delivered_datetime));
					$objPHPExcel->getActiveSheet()->SetCellValue('N'.(6+$i), $a);
				}

				$objPHPExcel->getActiveSheet()->SetCellValue('O'.(6+$i), $data[$i]->booking_notes);
				$objPHPExcel->getActiveSheet()->SetCellValue('P'.(6+$i), $data[$i]->transporter_barcode_slcars);

				$objPHPExcel->getActiveSheet()->SetCellValue('Q'.(6+$i), $data[$i]->transporter_dr_so);
				$objPHPExcel->getActiveSheet()->SetCellValue('R'.(6+$i), $data[$i]->transporter_dr_dr);
				$objPHPExcel->getActiveSheet()->SetCellValue('S'.(6+$i), $data[$i]->transporter_db_code);
			}
		}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);

		$objPHPExcel->getActiveSheet()->getStyle('A5:S'.(5+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:S'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:S'.(5+$i))->getAlignment()->setWrapText(true);

		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('ID BOOKING REPORT');

		// Save Excel
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
		$filecreatedname = "ID_BOOKING_REPORT".date('YmdHis') . ".xls";

		$objWriter->save(REPORT_PATH.$filecreatedname);

		$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
		echo $output;
		return;
	}

	function export_dr()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "transporter_dr_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$content = "";

		$startdate_fmt = date("Y-m-d",strtotime($startdate));
		$enddate_fmt = date("Y-m-d",strtotime($enddate));

		$this->dbtransporter = $this->load->database('transporter', true);

		$this->dbtransporter->order_by("transporter_dr_id","desc");

		switch($field)
		{
			case "transporter_dr_so":
				$this->dbtransporter->where("transporter_dr_so like", '%'.$keyword.'%');
			break;
			case "transporter_dr_dr":
				$this->dbtransporter->where("transporter_dr_dr like", '%'.$keyword.'%');
			break;
			case "dist_code":
				$this->dbtransporter->where("dist_code like", '%'.$keyword.'%');
			break;
			case "delivered":
				$this->dbtransporter->where("booking_delivery_status",2);
			break;
			case "booking_date_in":
				$this->dbtransporter->where("booking_date_in >= ", $startdate_fmt);
				$this->dbtransporter->where("booking_date_in <= ", $enddate_fmt);
			break;
		}


		if ($field == "delivered" || $field == "booking_date_in")
		{
			$this->dbtransporter->join("id_booking","booking_id = transporter_dr_booking_id","left_outer");
		}
		else
		{
			$this->dbtransporter->join("dist_tupper","dist_id = transporter_db_code","left_outer");
		}

		$this->dbtransporter->where("transporter_dr_status", 1);
		$q = $this->dbtransporter->get("tupper_dr");
		$data = $q->result();


		/** PHPExcel */
		include 'class/PHPExcel.php';

		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("SO_DR");
		$objPHPExcel->getProperties()->setSubject("SO_DR");
		$objPHPExcel->getProperties()->setDescription("SO_DR");

		//set document
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);

		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'SO/DR REPORT');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'NO');
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'SO');
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'DR');
		$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'ID BOOKING');
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'SO TYPE');
		$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'DB');

		if(count($data) > 0)
		{
			for($i=0; $i < count($data); $i++)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $data[$i]->transporter_dr_so);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $data[$i]->transporter_dr_dr);

				$content = $data[$i]->transporter_dr_booking_id;

				if (isset($data[$i]->booking_delivery_status))
				{
					if ($data[$i]->booking_delivery_status == 2)
					{
						$content .= ",";
						$content .= " ";
						$content .= "DELIVERED :";
						$content .= " ";
						$content .= date("d-m-Y H:i:s",strtotime($data[$i]->booking_delivered_datetime));
					}
				}

				$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $content);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $data[$i]->transporter_so_type);

				if (isset($data[$i]->dist_name))
				{
					$content_db =  $data[$i]->dist_name;
				}
				else
				{
					$content_db = "DB NOT SET";
				}

				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $content_db);
			}
		}

		$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);

		$objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$i))->getAlignment()->setWrapText(true);

		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('SO_DR REPORT');

		// Save Excel
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
		$filecreatedname = "SO_DR_REPORT".date('YmdHis') . ".xls";

		$objWriter->save(REPORT_PATH.$filecreatedname);

		$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
		echo $output;
		return;
	}

	function get_all_vehicle()
	{
		$user_id = $this->sess->user_id;
		$user_company = $this->sess->user_company;
		$user_group = $this->sess->user_group;

		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("booking_status",1);
		$q = $this->dbtransporter->get("id_booking");
		$rows = $q->result();
		$total = count($rows);

		if ($q->num_rows > 0)
		{
			for ($i=0;$i<$total;$i++)
			{
				$booking_vehicle[] = $rows[$i]->booking_vehicle;
			}
		}

		$this->db->order_by("vehicle_no", "asc");
		if (isset($booking_vehicle))
		{
			$this->db->where_in("vehicle_device",$booking_vehicle);
		}
		$this->db->or_where("vehicle_user_id", $user_id);

		if (isset($user_company) || isset($user_group))
		{
			if ($user_company > 0)
			{
				$this->db->or_where('vehicle_company', $user_company);
			}

			if ($user_group > 0)
			{
				$this->db->or_where('vehicle_group', $user_group);
			}
		}


		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$this->db->where("vehicle_status <>", 3);

		$qv = $this->db->get("vehicle");
		$rv = $qv->result();
		return $rv;

	}

	function get_all_driver()
	{
		$driver_company = $this->sess->user_company;

		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("booking_status",1);
		$q = $this->dbtransporter->get("id_booking");
		$rows = $q->result();
		$total = count($rows);

		if ($q->num_rows > 0)
		{
			for ($i=0;$i<$total;$i++)
			{
				$booking_driver[] = $rows[$i]->booking_driver;
			}
		}

		$nodata = 0;
		$this->dbtransporter->order_by('driver_name', 'asc');
		if (isset($booking_driver))
		{
			$this->dbtransporter->where_in('driver_id', $booking_driver);
		}
		$this->dbtransporter->or_where('driver_company', $driver_company);
		$q_driver = $this->dbtransporter->get('driver');
		$rows_driver = $q_driver->result();

		if (count($rows_driver)>0)
		{
			return $rows_driver;
		}
		else
		{
			return $nodata;
		}

	}

	function get_all_company()
	{
		$my_company = $this->sess->user_company;

		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("booking_status",1);
		$q = $this->dbtransporter->get("id_booking");
		$rows = $q->result();
		$total = count($rows);

		if ($q->num_rows > 0)
		{
			for ($i=0;$i<$total;$i++)
			{
				$booking_company[] = $rows[$i]->booking_company;
			}
		}

		$this->db->order_by("company_name","asc");
		if (isset($booking_company))
		{
			$this->db->where_in("company_id",$booking_company);
		}
		$this->db->or_where("company_id",$my_company);
		$q = $this->db->get("company");
		$rows = $q->result();
		return $rows;

	}

	function get_timecontrol()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->where("time_status",1);
		$qtime = $this->dbtransporter->get("timecontrol");
		$rtime = $qtime->result();
		return $rtime;
	}

	 function get_typearmada()
        {
            $this->dbtransporter = $this->load->database('transporter', true);
            $this->dbtransporter->where("typearmada_status",1);
            $q = $this->dbtransporter->get("typearmada");
            $rows = $q->result();
            return $rows;
        }

	function getDoorStatus($val)
	{
		//$val = "(000000001271BP05000000000001271120804A0617.4940S10657.9536E000.004514179.73001100000L00000000";
		$totstring = strlen($val);
		$value = substr($val, 79, 1);
		//print_r($value);
		return($value);
	}

	//for ppi
	function mn_history_hourmeter_ppi()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('transporter/report/mn_history_hourmeter_ppi', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	//for ppi
	function history_hourmeter_report_ppi()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$this->db->select("vehicle_device");
		$this->db->from("vehicle");

		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();

		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}

		$rv = $qv->result();
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrans = $this->load->database("transporter",true);
			$this->dbtrans->order_by("history_trip_mileage_vehicle_id","asc");
			$this->dbtrans->order_by("history_trip_mileage_start_time","asc");
			if ($vehicle != 0)
			{
				$this->dbtrans->where("history_trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
			}
			$this->dbtrans->where("history_trip_mileage_start_time >=",$sdate);
			$this->dbtrans->where("history_trip_mileage_end_time <=", $edate);
			$q = $this->dbtrans->get("history_trip_mileage_ppi");
			$rows = $q->result();

		}


		$params['data'] = $rows;

		$html = $this->load->view("transporter/report/list_result_history_hourmeter_ppi", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;

	}

	//for ppi
	function history_hourmeter_excel_ppi()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");

		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

		$this->db->select("vehicle_device");
		$this->db->from("vehicle");

		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();

		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}

		$rv = $qv->result();
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrans = $this->load->database("transporter",true);
			$this->dbtrans->order_by("history_trip_mileage_vehicle_id","asc");
			$this->dbtrans->order_by("history_trip_mileage_start_time","asc");
			if ($vehicle != 0)
			{
				$this->dbtrans->where("history_trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
			}
			$this->dbtrans->where("history_trip_mileage_start_time >=",$sdate);
			$this->dbtrans->where("history_trip_mileage_end_time <=", $edate);
			$q = $this->dbtrans->get("history_trip_mileage_ppi");
			$rows = $q->result();

		}

		$tot_hour = 0;
			$tot_dur = 0;
			if ((isset($rows)) && (count($rows)>0))
			{
				for ($i=0;$i<count($rows);$i++)
				{
					$dur = $rows[$i]->history_trip_mileage_duration;
					$tot_dur = $tot_dur + $rows[$i]->history_trip_mileage_duration;

					$ex = explode(" ",$dur);
					if (isset($ex[1]) && ($ex[1] == "Min"))
					{
						$detik = $ex[0] * 60;
					}
					elseif (isset($ex[1]) && ($ex[1] == "Hour"))
					{

						$detik = $ex[0] * 60 * 60;
						if (isset($ex[2]))
						{
							$det = $ex[2] * 60;
							$detik  = $detik + $det;
						}
					}

					$tot_hour = $tot_hour + $detik;
				}
			}

		/** PHPExcel */
		include 'class/PHPExcel.php';
		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setTitle("History Hour Meter");
        	$objPHPExcel->getProperties()->setSubject("History Hour Meter");
		$objPHPExcel->getProperties()->setDescription("History Hour Meter");

		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'History Trip Mileage');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
		if (isset($tot_hour))
									{
										$conval = $tot_hour;
										$seconds = $conval;

										// extract hours
										$hours = floor($seconds / (60 * 60));

										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);

										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);

										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												//echo $hours." "."Hour"." ";
												$objPHPExcel->getActiveSheet()->SetCellValue('C3', $hours." Hour");
											}
											if($hours >= 2)
											{
												//echo $hours." "."Hours"." ";
												$objPHPExcel->getActiveSheet()->SetCellValue('C3', $hours." Hours");
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												//echo $minutes." "."Minute"." ";
												$objPHPExcel->getActiveSheet()->SetCellValue('C3', $minutes." Minute");
											}
											if($minutes >= 2)
											{
												//echo $minutes." "."Minutes"." ";
												$objPHPExcel->getActiveSheet()->SetCellValue('C3', $minutes." Minutes");
											}
										}
										 if(isset($seconds) && $seconds > 0)
										{
											//echo $seconds." "."Detik"." ";
											$objPHPExcel->getActiveSheet()->SetCellValue('C3', $seconds. " Detik");
										}
									}


		$objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('H3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('I3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle');
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Trip No');
		$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Start Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'End Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Duration');
		$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Trip Mileage (km)');
		$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Cummulative Mileage (km)');
		$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Location Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Location End');
		$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$j = 0;
		for($i=0; $i < count($rows); $i++)
		{
			if (isset($rows[$i]->history_trip_mileage_vehicle_no))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $j+1);
			}
			if ((isset($rows[$i]->history_trip_mileage_vehicle_no)) && (isset($rows[$i]->history_trip_mileage_vehicle_name)))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $rows[$i]->history_trip_mileage_vehicle_no.' - '.$rows[$i]->history_trip_mileage_vehicle_name);
			}
			if (isset($rows[$i]->history_trip_mileage_trip_no))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $rows[$i]->history_trip_mileage_trip_no);
			}
			if (isset($rows[$i]->history_trip_mileage_start_time))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), date("d-m-Y H:i:s", strtotime($rows[$i]->history_trip_mileage_start_time)));
			}
			if (isset($rows[$i]->history_trip_mileage_end_time))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), date("d-m-Y H:i:s", strtotime($rows[$i]->history_trip_mileage_end_time)));
			}
			if (isset($rows[$i]->history_trip_mileage_duration))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->history_trip_mileage_duration);
			}
			if (isset($rows[$i]->history_trip_mileage_trip_mileage))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->history_trip_mileage_trip_mileage);
			}
			if (isset($rows[$i]->history_trip_mileage_cummulative_mileage))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), $rows[$i]->history_trip_mileage_cummulative_mileage);
			}
			if (isset($rows[$i]->history_trip_mileage_location_start))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->history_trip_mileage_location_start);
			}
			if (isset($rows[$i]->history_trip_mileage_location_end))
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('J'.(6+$i), $rows[$i]->history_trip_mileage_location_end);
			}
			$j++;
		}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.(5+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J'.(5+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('History Hour Meter');
			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);
			$filecreatedname = "HistoryHourMeter_".$vehicle.".xls";
			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;

	}

	function vehicleonline()
	{
		$date = isset($_POST['date']) ? $_POST['date'] : 0;
		$hour = isset($_POST['hour']) ? $_POST['hour'] : 0;
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;

		$now = date("Y-m-d");
		$fdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));
		$cek_date = date("Y-m-d", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));


		if( $vehicle == 0 )
		{
			//all vehicle
			$this->db->order_by("vehicle_no","asc");
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			if ($this->sess->user_group != 0){
				$this->db->where("vehicle_group", $this->sess->user_group);
			}if($this->sess->user_id == 2232 || $this->sess->user_id == 2239){
				$this->db->where("vehicle_company", $this->sess->user_company);
			}if($this->sess->user_id == 1933){
				$this->db->where("vehicle_user_id", $this->sess->user_id);
			}if($this->sess->user_id == 2288 && $this->sess->user_type == 5){
				$this->db->where("vehicle_user_id", 1933);
			}
			$this->db->where("vehicle_status <>",3);
			$qm = $this->db->get("vehicle");
			$rm = $qm->result();

			foreach($rm as $v)
			{
				$this->db->order_by("vehicle_device", "asc");
				$this->db->where("vehicle_device", $v->vehicle_device);
				$this->db->limit(1);
				$this->db->join("group", "vehicle_group = group_id", "left outer");
				$qv = $this->db->get("vehicle");
				$rowvehicle = $qv->row();
				$rowv[] = $qv->row();

						//Seleksi Databases
						$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
						$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
						$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->dbhistory = $this->load->database($istbl_history, TRUE);

						$this->dbhistory->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
						$this->dbhistory->where("gps_info_device", $v->vehicle_device);
						$this->dbhistory->where("gps_time <=",$fdate);
						$this->dbhistory->order_by("gps_time", "desc");
						$this->dbhistory->limit(1);
						$q = $this->dbhistory->get($table);
						$rows[] = $q->row();
			}
		} //end if allvehicle

		//per vehicle atau lebih
		else
		{
			$this->db->order_by("vehicle_name", "asc");
			$this->db->order_by("vehicle_no", "asc");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_device", $vehicle);
			$this->db->limit(1);
			$this->db->join("group", "vehicle_group = group_id", "left outer");
			$qm = $this->db->get("vehicle");
			$rm = $qm->row();
			$rowv[] = $qm->row();

					//Seleksi Databases
					$table = sprintf("%s_gps", strtolower($rm->vehicle_device));
					$tableinfo = sprintf("%s_info", strtolower($rm->vehicle_device));
					$this->dbhistory = $this->load->database("gpshistory2", TRUE);

					$this->dbhistory->join($tableinfo, "gps_info_time = gps_time and gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->dbhistory->where("gps_info_device", $rm->vehicle_device);
					$this->dbhistory->where("gps_time <=",$fdate);
					$this->dbhistory->order_by("gps_time", "desc");
					$this->dbhistory->limit(1);
					$q = $this->dbhistory->get($table);
					$rows[] = $q->row();


		}



		$trows = count($rows);

		for($i=0;$i<$trows;$i++){

			if (isset($rows[$i]->gps_longitude))
			{
				$rows[$i]->result_position = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			}


			//Find Vehicle Odometer
			foreach($rowv as $vodo)
			{
				if (isset($rows[$i]->gps_name))
				{
					if($vodo->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
					{
						$vodometer = $vodo->vehicle_odometer;
					}
				}
			}

			/* if (isset($rows[$i]->gps_info_distance))
			{
				$rows[$i]->result_gps_odometer = round(($rows[$i]->gps_info_distance+$vodometer*1000)/1000);
			} */

			if (isset($rows[$i]->gps_info_io_port))
			{
				$ioport = $rows[$i]->gps_info_io_port;
				$rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off
			}

			if (isset($rows[$i]->gps_longitude))
			{
				$rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
			}

			if (isset($rows[$i]->gps_latitude))
			{
				$rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
			}

			if (isset($rows[$i]->gps_longitude_real))
			{
				$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			}

			if (isset($rows[$i]->gps_latitude_real))
			{
				$rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");
			}


			foreach($rowv as $vodo)
			{
				if (isset($rows[$i]->gps_name))
				{
					if($vodo->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
					{
						$vdevice = $vodo->vehicle_device;
					}
				}
			}

			if (isset($rows[$i]->gps_longitude))
			{
				$rows[$i]->geofence_location = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $vodo->vehicle_device);
				//print_r($rows[$i]->geofence_location);exit;
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
			$objPHPExcel->getProperties()->setTitle("Vehicle Online Report");
			$objPHPExcel->getProperties()->setSubject("Vehicle Online Detail Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Vehicle Online Detail Repor Lacak-mobil.com");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			/* $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10); */

			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Vehicle Online Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$filter_date = kopindosatformatdatetime($date ." " . $hour . ":59");
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);
			$objPHPExcel->getActiveSheet()->SetCellValue('F3', 'Total Record :');
			$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('G3', count($rows));
			$objPHPExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Customer');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Sim Card');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'GPS Time');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'GPS');
			$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//Write Data
			for($i=0;$i<$trows;$i++){
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
				foreach($rowv as $vvehicle)
				{
					if (isset($rows[$i]->gps_name))
					{
						if($vvehicle->vehicle_device == ($rows[$i]->gps_name.'@'.$rows[$i]->gps_host))
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $vvehicle->vehicle_no);
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $vvehicle->vehicle_name);
							$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $vvehicle->group_name);
							$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $vvehicle->vehicle_card_no . " (" . $vvehicle->vehicle_operator . ")");
						}
					}
				}

				/* if (isset($rows[$i]->gps_latitude_real_fmt))
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);
				} */


				/* if (isset($rows[$i]->geofence_location) && ($rows[$i]->geofence_location != ""))
				{
					$x = $rows[$i]->geofence_location;
					$z = $x;

					$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), "Geofence :" . " " . $z . " " . $rows[$i]->result_position->display_name);
				}
				else
				{
					if (isset($rows[$i]->result_position->display_name))
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->result_position->display_name);
					}
				} */

				if (isset($rows[$i]->gps_time))
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))));
				}

				/* if (isset($rows[$i]->status1))
				{
					$engine = ($rows[$i]->status1) ? $this->lang->line('lon') : $this->lang->line('loff');
					$objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), $engine);
				} */

				if (isset($rows[$i]->gps_status))
				{
					$gps = ($rows[$i]->gps_status == "V") ? "NOT OK" : "OK";
					$objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $gps);
				}


			}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:G'.(5+$trows))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:G'.(5+$trows))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:G'.(5+$trows))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('vehicle_online_report');

			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "vehicle_online_".date("dmY", strtotime($date)). ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
	}

	function vehicleonline2()
	{
		$date = isset($_POST['date']) ? $_POST['date'] : 0;
		$hour = isset($_POST['hour']) ? $_POST['hour'] : 0;
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;

		$now = date("Y-m-d");
		$fdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));
		$cek_date = date("Y-m-d", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));

			//all vehicle
			$this->db->order_by("vehicle_no","asc");
			//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->where("vehicle_status <>",3);
			$this->db->join("group", "vehicle_group = group_id", "left outer");
			$qm = $this->db->get("vehicle");
			$rows = $qm->result();

			$trows = count($rows);
			//print_r($trows);exit();
			/** PHPExcel */
			include 'class/PHPExcel.php';

			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';

			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("Vehicle Online Report");
			$objPHPExcel->getProperties()->setSubject("Vehicle Online Detail Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Vehicle Online Detail Repor Lacak-mobil.com");

			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);;
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			/* $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10); */

			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Vehicle Online Report2');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$filter_date = kopindosatformatdatetime($date ." " . $hour . ":59");
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);
			$objPHPExcel->getActiveSheet()->SetCellValue('F3', 'Total Record :');
			$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);


			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A7', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C7', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Sentra');
			$objPHPExcel->getActiveSheet()->SetCellValue('E7', 'Sim Card');
			$objPHPExcel->getActiveSheet()->SetCellValue('F7', 'Operator');
			$objPHPExcel->getActiveSheet()->SetCellValue('G7', 'Tanggal Pasang');
			$objPHPExcel->getActiveSheet()->getStyle('A7:G7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A7:G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//Write Data
			$i = 1;
			for ($j=0;$j<count($rows);$j++)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(7+$i), $i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				/* $vehicle_front = substr($rows[$j]->vehicle_no,0,1);
				$vehicle_mid = substr($rows[$j]->vehicle_no,1,4);
				$vehicle_end = substr($rows[$j]->vehicle_no,5,3); */

				$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), $rows[$j]->vehicle_no);
				//$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), $vehicle_front." ".$vehicle_mid." ".$vehicle_end);
				$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


				$objPHPExcel->getActiveSheet()->SetCellValue('C'.(7+$i), $rows[$j]->vehicle_name);
				$objPHPExcel->getActiveSheet()->getStyle('C'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.(7+$i), $rows[$j]->group_name);
				$objPHPExcel->getActiveSheet()->getStyle('D'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(7+$i), $rows[$j]->vehicle_card_no);
				$objPHPExcel->getActiveSheet()->getStyle('E'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(7+$i), $rows[$j]->vehicle_operator);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(7+$i), $rows[$j]->vehicle_tanggal_pasang);
				$objPHPExcel->getActiveSheet()->getStyle('G'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				$i++;
			}

			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);

			$objPHPExcel->getActiveSheet()->getStyle('A7:G'.(6+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A7:G'.(6+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A7:G'.(6+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('vehicle_online_report2');

			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "vehicle_online_".date("dmY", strtotime($date)). ".xls";

			$objWriter->save(REPORT_PATH.$filecreatedname);

			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
	}

}
