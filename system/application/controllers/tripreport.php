<?php
include "base.php";

class Tripreport extends Base
{
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function __construct()
	{
		parent::Base();
		$this->load->model("dashboardmodel");
		$this->load->model("historymodel");
	}

	//new summary
	function history()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$privilegecode                         = $this->sess->user_id_role;

		$rows                           = $this->dashboardmodel->getvehicle_report();
		$rows_company                   = $this->dashboardmodel->get_company_bylevel();
		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	//new summary
	function search_history($name = "", $host = "", $startdate = "", $shour = "", $ehour = "", $enddate = "")
	{
		ini_set('display_errors', 1);
		$company          = $this->input->post("company");
		$vehicle          = $this->input->post("vehicle");
		$startdate        = $this->input->post("startdate");
		$enddate          = $this->input->post("enddate");
		$shour            = $this->input->post("shour");
		$ehour            = $this->input->post("ehour");
		$mapview          = 1;
		$tableview        = ""; //disable sementara

		$vehicle_no       = "-";
		$vehicle_odometer = 0;
		$vehicle_type     = "-";
		$vehicle_user_id  = 0;
		$error            = "";

		/*if ($company == "" || $company == 0)
		{
			$error = "- Invalid Vehicle. Silahkan Pilih Area/Cabang! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}*/

		if ($vehicle == "" || $vehicle == 0) {
			$error = "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		} else {
			$datavehicle = explode("@", $vehicle);
			$name = $datavehicle[0];
			$host = $datavehicle[1];

			$this->db->order_by("vehicle_id", "asc");
			$this->db->where("vehicle_device", $name . "@" . $host);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			$rowvehicle = $q->row();

			if (count($rowvehicle) > 0) {

				$vehicle_no = $rowvehicle->vehicle_no;
				$vehicle_odometer = $rowvehicle->vehicle_odometer;
				$vehicle_type = $rowvehicle->vehicle_type;
				$vehicle_user_id = $rowvehicle->vehicle_user_id;

				/*if (isset($rowvehicle->vehicle_type) && (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))) {
					$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
					$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
				}else{
					$sdate = date("Y-m-d H:i:s", strtotime("-6 hour", strtotime($startdate." ".$shour))); //wita
					$edate = date("Y-m-d H:i:s", strtotime("-6 hour", strtotime($enddate." ".$ehour)));	//wita
				}*/

				$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour))); //wita
				$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate . " " . $ehour)));    //wita
			}
		}

		if ($startdate == "" || $enddate == "") {
			$error = "- Invalid Vehicle. Silahkan Tanggal Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($shour == "" || $ehour == "") {
			$error = "- Invalid Vehicle. Silahkan Jam Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($mapview == "1") {
			if ($startdate != $enddate) {
				$diff = strtotime($enddate) - strtotime($startdate);
				if ($diff < 0) {
					$error = " Tanggal tidak benar \n";
					$err = true;
				} else if ($diff > 86400) {
					$error = " maksimal jarak tanggal yang dipilih hanya boleh satu hari \n";
					$err = true;
				}
				if ($err) {
					$callback['error'] = true;
					$callback['message'] = $error;

					echo json_encode($callback);
					return;
				}
			}
		}
		if ($tableview == "1") {
			if ($startdate != $enddate) {
				$diff = strtotime($enddate) - strtotime($startdate);
				if ($diff < 0) {
					$error = " Tanggal tidak benar \n";
					$err = true;
				} else if ($diff > 86400) {
					$error = " maksimal jarak tanggal yang dipilih hanya boleh satu hari \n";
					$err = true;
				}
				if ($err) {
					$callback['error'] = true;
					$callback['message'] = $error;

					echo json_encode($callback);
					return;
				}
			}
		}
		//PORT Only
		if (isset($rowvehicle->vehicle_info)) {
			$json = json_decode($rowvehicle->vehicle_info);
			if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
				$databases = $this->config->item('databases');
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					$table = $this->config->item("external_gpstable");
					$tableinfo = $this->config->item("external_gpsinfotable");
					$this->dbhist = $this->load->database($database, TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				} else {
					$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
					$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
					$this->dbhist = $this->load->database("default", TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				}

				$vehicle_device = explode("@", $rowvehicle->vehicle_device);
				$vehicle_no = $rowvehicle->vehicle_no;
				$vehicle_dev = $rowvehicle->vehicle_device;
				$vehicle_name = $rowvehicle->vehicle_name;
				$vehicle_type = $rowvehicle->vehicle_type;

				if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE") {
					$tablehist = $vehicle_device[0] . "@t5_gps";
					$tablehistinfo = $vehicle_device[0] . "@t5_info";
				} else {
					$tablehist = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_gps";
					$tablehistinfo = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_info";
				}


				//$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
												   gps_longitude,gps_latitude,gps_ew,gps_ns");
				$this->dbhist->where("gps_name", $vehicle_device[0]);
				$this->dbhist->where("gps_speed >", 0);
				$this->dbhist->where("gps_time >=", $sdate);
				$this->dbhist->where("gps_time <=", $edate);
				$this->dbhist->order_by("gps_time", "asc");
				$this->dbhist->group_by("gps_time");
				//$this->dbhist->limit(6000);
				$this->dbhist->from($table);
				$q = $this->dbhist->get();
				$rows1 = $q->result();


				//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
												   gps_longitude,gps_latitude,gps_ew,gps_ns");
				$this->dbhist2->where("gps_name", $vehicle_device[0]);
				$this->dbhist2->where("gps_speed >", 0);
				$this->dbhist2->where("gps_time >=", $sdate);
				$this->dbhist2->where("gps_time <=", $edate);
				$this->dbhist2->order_by("gps_time", "asc");
				$this->dbhist2->group_by("gps_time");
				//$this->dbhist2->limit(6000);
				$this->dbhist2->from($tablehist);
				$q2 = $this->dbhist2->get();
				$rows2 = $q2->result();

				$rows = array_merge($rows1, $rows2); //limit data rows = 3000
				$trows = count($rows);

				$totaldata = $trows;
				$data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
				//print_r($totaldata);exit();
				/*if(($trows > 6000) && ($mapview == 1)){

								$error = "- Cannot View, Silahkan kurangi tanggal Report yang dipilih! \n";
								$callback['error'] = true;
								$callback['message'] = $error;

								echo json_encode($callback);
								return;
							}*/

				/*for($i=count($rows)-1; $i >= 0; $i--)
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
							if(isset($rowsummary)){
								$data = $rowsummary;
								$data = $this->dashboardmodel->array_sort($data, 'gps_time', SORT_ASC);
								$totaldata = count($data);
							}else{

								$data = "";
								$totaldata = 0;
							}*/

				/*
							$data = json_encode($rowsummary);
							$data2 = json_decode($data);*/

				//$data = json_encode($data);
				//print_r($data);exit();
			}
		}


		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$params['mapview']          = $mapview;
		$params['tableview']        = $tableview;
		$params['vehicle_no']       = $vehicle_no;
		$params['vehicle_odometer'] = $vehicle_odometer;
		$params['vehicle_type']     = $vehicle_type;
		$params['vehicle_user_id']  = $vehicle_user_id;
		$params['totalgps']         = $trows;
		$params['data']             = $data;
		$params['totaldata']        = $totaldata;
		$params['sdate']            = $sdate;
		$params['edate']            = $edate;
		$html                       = $this->load->view("newdashboard/report/vhistory_report_result_std", $params, true);
		// $html                       = $this->load->view("dashboard/report/vhistory_report_result", $params, true);

		$callback['error']          = false;
		$callback['html']           = $html;
		echo json_encode($callback);
	}

	function playback()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$userid          = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_level           = $this->sess->user_level;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		$this->db = $this->load->database("default", true);
		if ($user_level == 1) {
			$this->db->where("vehicle_user_id", $userid);
		} elseif ($user_level == 2) {
			$this->db->where("vehicle_company", $user_company);
		} elseif ($user_level == 3) {
			$this->db->where("vehicle_subcompany", $user_subcompany);
		} elseif ($user_level == 4) {
			$this->db->where("vehicle_group", $user_group);
		} else {
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}

		// if ($this->sess->user_type == 2)
		// {
		// 	$this->db->where("vehicle_user_id", $this->sess->user_id);
		// 	$this->db->or_where("vehicle_company", $this->sess->user_company);
		// 	$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		// }
		// else
		// if ($this->sess->user_type == 3)
		// {
		// 	$this->db->where("user_agent", $this->sess->user_agent);
		// }
		// //tambahan, user group yg open playback report
		// if ($this->sess->user_group <> 0)
		// {
		// 	$this->db->where("vehicle_group", $this->sess->user_group);
		// }

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
		}

		$rows = $q->result();
		//print_r($rows);exit;
		$this->params["vehicles"] = $rows;
		$this->params['code_view_menu'] = "report";

		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/vplayback_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_playback()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");

		$this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate . " " . $ehour . ":59")));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":59"));
		}


		$now = date("Y-m-d");
		$cek_date = date("Y-m-d", strtotime($startdate));
		$cek_enddate = date("Y-m-d", strtotime($enddate));

		$json = json_decode($rowvehicle->vehicle_info);


		if (($cek_date == $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws))) {
			if ($rowvehicle->vehicle_info) {
				if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
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

			if (!isset($table)) {
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		} else if (($cek_date < $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws))) {
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if ($this->config->item("is_dbhistory") == 1) {
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);

			$action = "history";
		} else {

			if ($rowvehicle->vehicle_info && (!isset($json->vehicle_ws))) {
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
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

			if (!isset($table)) {
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			if (isset($json->vehicle_ws)) {
				$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
				$this->db = $this->load->database("gpshistory2", TRUE);
			}



			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time", "asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			if (isset($json->vehicle_ws)) {
				$this->db = $this->load->database("default", TRUE);
			}

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if ($this->config->item("is_dbhistory") == 1) {
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);


			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time", "asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if (((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) &&
			(!isset($json->vehicle_ws))
		) {
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time", "asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();


			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows) == 0) {

				if (isset($action) && $action == "history") {
					if ($rowvehicle->vehicle_info) {
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							} else {
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

					if (!isset($table)) {
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
					$this->db->order_by("gps_time", "asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan
		} else {
			$rows = array_merge($rows2, $rows1);
		}
		$data_report = array();



		$data = array(); // initialization variable
		$vehicle_device = "";
		$engine = "";

		/* start looping for process data - data dikelompokkan berdasarkan engine status on/off */
		foreach ($rows as $obj) {
			if ($vehicle_device != $rowvehicle->vehicle_device) {
				$no = 0;
				$no_data = 1;
			}

			if (substr($obj->gps_info_io_port, 4, 1) == 1) { //engine ON
				if ($engine != "ON") {
					$no++;
					$no_data = 1;
				}
				if ($no == 0) $no++;

				if ($no_data == 1) {
					$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['ON']['start'] = $obj;
				} else {
					$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['ON']['end'] = $obj;
				}
				$no_data++;
				$engine = "ON";
			}

			if (substr($obj->gps_info_io_port, 4, 1) == 0) { //engine OFF
				if ($engine != "OFF") {
					$no++;
					$no_data = 1;
				}
				if ($no == 0) $no++;

				if ($no_data == 1) {
					$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['OFF']['start'] = $obj;
				} else {
					$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['OFF']['end'] = $obj;
				}

				$no_data++;
				$engine = "OFF";
			}

			$vehicle_device = $rowvehicle->vehicle_device;
		}
		/* end loop */


		foreach ($data as $vehicles => $value_vehicles) {
			foreach ($value_vehicles as $number => $value_number) {
				foreach ($value_number as $engine => $report) {
					if (!isset($report['end'])) {
						$report['end'] = $report['start'];
					}
					$data_report[$vehicles][$number][$engine]['start'] = $report['start'];
					$data_report[$vehicles][$number][$engine]['end'] = $report['end'];



					$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);

					$show_duration = "";
					if ($duration[0] != 0) {
						$show_duration .= $duration[0] . " Day ";
					}
					if ($duration[1] != 0) {
						$show_duration .= $duration[1] . " Hour ";
					}
					if ($duration[2] != 0) {
						$show_duration .= $duration[2] . " Min";
					}
					if ($show_duration == "") {
						$show_duration .= "0 Min";
					}
					$data_report[$vehicles][$number][$engine]['duration'] = $show_duration;

					$mileage = ($report['end']->gps_info_distance - $report['start']->gps_info_distance) / 1000;
					if ($mileage < 0) {
						$mileage = ($report['start']->gps_info_distance - $report['end']->gps_info_distance) / 1000;
					}

					$data_report[$vehicles][$number][$engine]['mileage'] = round($mileage, 2);


					if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {

						if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others_coordinate"))) {
							$location_start = $this->getPosition_other($report['start']->gps_longitude_real, $report['start']->gps_latitude_real);
							$geofence_start = $this->getGeofence_location_other($report['start']->gps_longitude_real, $report['start']->gps_latitude_real, $rowvehicle->vehicle_user_id);
						} else {
							$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
							$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $rowvehicle->vehicle_user_id);
						}
					} else {
						$location_start = $this->getPosition_other($report['start']->gps_longitude, $report['start']->gps_latitude);
						$geofence_start = $this->getGeofence_location_other($report['start']->gps_longitude, $report['start']->gps_latitude, $rowvehicle->vehicle_user_id);
					}

					$data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
					$data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;


					if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {

						if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others_coordinate"))) {
							$location_end = $this->getPosition_other($report['end']->gps_longitude_real, $report['end']->gps_latitude_real);
							$geofence_end = $this->getGeofence_location_other($report['end']->gps_longitude_real, $report['end']->gps_latitude_real, $rowvehicle->vehicle_user_id);
						} else {
							$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
							$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $rowvehicle->vehicle_user_id);
						}
					} else {
						$location_end = $this->getPosition_other($report['end']->gps_longitude, $report['end']->gps_latitude);
						$geofence_end = $this->getGeofence_location_other($report['end']->gps_longitude, $report['end']->gps_latitude, $rowvehicle->vehicle_user_id);
					}

					$data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
					$data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
				}
			}
		}

		$params['data'] = $data_report;
		$params['vehicle'] = $rowvehicle;

		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
			$html = $this->load->view("dashboard/report/vplayback_result", $params, true);
		} else {
			if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_odometer_portable"))) {
				$html = $this->load->view("dashboard/report/vplayback_result_other_portable", $params, true);
			} else {
				$html = $this->load->view("dashboard/report/vplayback_result_other", $params, true);
			}
		}

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;
	}

	function tripmileage()
	{

		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$userid          = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_level           = $this->sess->user_level;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		$this->db = $this->load->database("default", true);
		if ($user_level == 1) {
			$this->db->where("vehicle_user_id", $userid);
		} elseif ($user_level == 2) {
			$this->db->where("vehicle_company", $user_company);
		} elseif ($user_level == 3) {
			$this->db->where("vehicle_subcompany", $user_subcompany);
		} elseif ($user_level == 4) {
			$this->db->where("vehicle_group", $user_group);
		} else {
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}

		// if ($this->sess->user_type == 2)
		// {
		// 	$this->db->where("vehicle_user_id", $this->sess->user_id);
		// 	$this->db->or_where("vehicle_company", $this->sess->user_company);
		// 	$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		// }
		// else
		// if ($this->sess->user_type == 3)
		// {
		// 	$this->db->where("user_agent", $this->sess->user_agent);
		// }

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
		}

		$rows = $q->result();
		//print_r($rows);exit;
		$this->params["vehicles"] = $rows;
		$this->params['code_view_menu'] = "report";

		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/vtripmileage_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_tripmileage()
	{

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");

		$this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate . " " . $ehour . ":59")));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":59"));
		}

		$now = date("Y-m-d");
		$cek_enddate = date("Y-m-d", strtotime($enddate));
		$cek_date = date("Y-m-d", strtotime($startdate));

		$json = json_decode($rowvehicle->vehicle_info);

		if (($cek_date == $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws))) {
			if ($rowvehicle->vehicle_info) {
				if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
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

			if (!isset($table)) {
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		} else if (($cek_date < $now) && ($cek_date == $cek_enddate) && (!isset($json->vehicle_ws))) {
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if ($this->config->item("is_dbhistory") == 1) {
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);

			$action = "history";
		} else {

			if ($rowvehicle->vehicle_info && (!isset($json->vehicle_ws))) {
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
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

			if (!isset($table)) {
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			if (isset($json->vehicle_ws)) {
				$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
				$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
				$this->db = $this->load->database("gpshistory2", TRUE);
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time", "asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			if (isset($json->vehicle_ws)) {
				$this->db = $this->load->database("default", TRUE);
			}

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

			$istbl_history = $this->config->item("dbhistory_default");
			if ($this->config->item("is_dbhistory") == 1) {
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time", "asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if (((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) &&
			(!isset($json->vehicle_ws))
		) {
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time", "asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();


			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows) == 0) {

				if (isset($action) && $action == "history") {
					if ($rowvehicle->vehicle_info) {
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							} else {
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

					if (!isset($table)) {
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
					$this->db->order_by("gps_time", "asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan
		} else {
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;
		}


		$data = array();

		$nopol = "";

		$on = false;
		$trows = count($rows);
		//print_r($trows);exit;

		for ($i = 0; $i < $trows; $i++) {
			//if($rows[$i]->gps_speed == 0) continue;

			if ($nopol != $rowvehicle->vehicle_no) { //new vehicle
				if ($on && $i != 0) {
					//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
					if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					} else {
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
					}
					//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
					if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
						if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others_coordinate"))) {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $rowvehicle->vehicle_user_id);

							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition_other($rows[$i - 1]->gps_longitude_real, $rows[$i - 1]->gps_latitude_real);
						} else {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);

							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition($rows[$i - 1]->gps_longitude, $rows[$i - 1]->gps_ew, $rows[$i - 1]->gps_latitude, $rows[$i - 1]->gps_ns);
						}
					} else {
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);

						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition_other($rows[$i - 1]->gps_longitude, $rows[$i - 1]->gps_latitude);
					}
					$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_mileage'] = $rows[$i - 1]->gps_info_distance;
					$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_latitude'] = $rows[$i - 1]->gps_latitude_real;
					$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_longitude'] = $rows[$i - 1]->gps_longitude_real;
				}
				if (substr($rows[$i]->gps_info_io_port, 4, 1) == 1) {
					$trip_no = 1;
					//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
					if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
					} else {
						$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
					}
					//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
					if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {

						if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others_coordinate"))) {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $rowvehicle->vehicle_user_id);

							$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real);
						} else {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);

							$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
						}
					} else {
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);

						$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
					}
					$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_mileage'] = $rows[$i]->gps_info_distance;
					$data[$rowvehicle->vehicle_no][$trip_no - 1]['vehicle_name'] = $rowvehicle->vehicle_name;
					$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_latitude'] = $rows[$i]->gps_latitude_real;
					$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_longitude'] = $rows[$i]->gps_longitude_real;
					$on = true;

					if ($i == $trows - 1) {
						//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" &&$rowvehicle->vehicle_type != "GT06N"){
						if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						} else {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
						if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
							if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others_coordinate"))) {
								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $rowvehicle->vehicle_user_id);

								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real);
							} else {
								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);

								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						} else {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);

							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
						}
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_latitude'] = $rows[$i]->gps_latitude_real;
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_longitude'] = $rows[$i]->gps_longitude_real;
					}
				} else {
					$trip_no = 1;
					$on = false;
				}
			} else { //same vehicle
				if (substr($rows[$i]->gps_info_io_port, 4, 1) == 1) {
					if (!$on) {
						//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
						if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
							$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						} else {
							$data[$rowvehicle->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
						if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
							if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others_coordinate"))) {
								$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $rowvehicle->vehicle_user_id);

								$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real);
							} else {
								$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);

								$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
							}
						} else {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);

							$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);
						}
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['vehicle_name'] = $rowvehicle->vehicle_name;
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_latitude'] = $rows[$i]->gps_latitude_real;
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['start_longitude'] = $rows[$i]->gps_longitude_real;
					}

					$on = true;
					if ($i == $trows - 1 && $on) {
						//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
						if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						} else {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
						if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
							if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others_coordinate"))) {
								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real);

								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $rowvehicle->vehicle_user_id);
							} else {
								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);

								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
							}
						} else {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);

							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						}
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_latitude'] = $rows[$i]->gps_latitude_real;
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_longitude'] = $rows[$i]->gps_longitude_real;
					}
				} else {
					if ($on) {
						//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
						if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
						} else {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_time'] = date("d-m-Y H:i:s", strtotime($rows[$i]->gps_time));
						}
						//if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK315" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR" && $rowvehicle->vehicle_type != "GT06N"){
						if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {

							if (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others_coordinate"))) {
								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real);

								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $rowvehicle->vehicle_user_id);
							} else {
								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);

								$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle->vehicle_user_id);
							}
						} else {
							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_position'] = $this->getPosition_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude);

							$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_geofence_location'] = $this->getGeofence_location_other($rows[$i]->gps_longitude, $rows[$i]->gps_latitude, $rowvehicle->vehicle_user_id);
						}
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_mileage'] = $rows[$i]->gps_info_distance;
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_latitude'] = $rows[$i]->gps_latitude_real;
						$data[$rowvehicle->vehicle_no][$trip_no - 1]['end_longitude'] = $rows[$i]->gps_longitude_real;
					}
					$on = false;
				}
			}
			$nopol = $rowvehicle->vehicle_no;
		}

		$params['data']    = $data;
		$params['vehicle'] = $rowvehicle;
		$html              = $this->load->view("dashboard/report/vtripmileage_result", $params, true);
		$callback['error'] = false;
		$callback['html']  = $html;
		echo json_encode($callback);
		return;
	}

	function pto()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$userid          = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_level           = $this->sess->user_level;

		$door_list = array("T5PTO", "X3_PTO");

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_in("vehicle_type", $door_list);

		$this->db = $this->load->database("default", true);
		if ($user_level == 1) {
			$this->db->where("vehicle_user_id", $userid);
		} elseif ($user_level == 2) {
			$this->db->where("vehicle_company", $user_company);
		} elseif ($user_level == 3) {
			$this->db->where("vehicle_subcompany", $user_subcompany);
		} elseif ($user_level == 4) {
			$this->db->where("vehicle_group", $user_group);
		} else {
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}

		// if ($this->sess->user_type == 2)
		// {
		// 	$this->db->where("vehicle_user_id", $this->sess->user_id);
		// 	$this->db->or_where("vehicle_company", $this->sess->user_company);
		// 	$this->db->where_in("vehicle_type", $door_list);
		// 	$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		// }
		// else
		// if ($this->sess->user_type == 3)
		// {
		// 	$this->db->where("user_agent", $this->sess->user_agent);
		// }

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		/*if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}*/
		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params['code_view_menu'] = "report";

		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/vpto_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_pto()
	{
		$door_others = array("X3_PTO");

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");

		$error = "";

		if ($vehicle == "" || $vehicle == 0) {
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		}
		if ($startdate != $enddate) {
			$error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam tanggal yang sama! \n";
		}

		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate . " " . $ehour . ":59")));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":59"));
		}

		$now = date("Y-m-d");
		$cek_date = date("Y-m-d", strtotime($startdate));
		$cek_enddate = date("Y-m-d", strtotime($enddate));

		if (($cek_date == $now) && ($cek_date == $cek_enddate)) {
			if ($rowvehicle->vehicle_info) {
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
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

			if (!isset($table)) {
				$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists = $this->config->item("table_hist");
				$this->tblhist = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		} else if (($cek_date < $now) && ($cek_date == $cek_enddate)) {
			//History
			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if ($this->config->item("is_dbhistory") == 1) {
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$action = "history";
		} else {

			if ($rowvehicle->vehicle_info) {
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
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

			if (!isset($table)) {
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
			$this->db->order_by("gps_time", "asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows1 = $q->result();

			$table = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if ($this->config->item("is_dbhistory") == 1) {
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time", "asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if ((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) {
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time", "asc");
			$this->db->from($table);
			$q = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();

			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows) == 0) {

				if (isset($action) && $action == "history") {
					if ($rowvehicle->vehicle_info) {
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
								$database = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table = $this->config->item("external_gpstable");
								$tableinfo = $this->config->item("external_gpsinfotable");
								$this->tblhist = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist = $this->tblinfohist;
								$this->db = $this->load->database($database, TRUE);
							} else {
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

					if (!isset($table)) {
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
					$this->db->order_by("gps_time", "asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan

		} else {
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;
		}

		$data_report = array();
		$data = array(); // initialization variable
		$vehicle_device = "";
		$door = "";
		//$this->getFanStatus($row->gps->gps_msg_ori);
		//$value = substr($val, 79, 1);


		/* start looping for process data - data dikelompokkan berdasarkan PTO Status ON/OFF */
		foreach ($rows as $obj) {
			if ($vehicle_device != $rowvehicle->vehicle_device) {
				$no = 0;
				$no_data = 1;
			}
			if (in_array(strtoupper($rowvehicle->vehicle_type), $door_others)) {
				if ($obj->gps_cs == 53) { //Door ON
					if ($door != "ON") {
						$no++;
						$no_data = 1;
					}
					if ($no == 0) $no++;

					if ($no_data == 1) {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['ON']['start'] = $obj;
					} else {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['ON']['end'] = $obj;
					}
					$no_data++;
					$door = "ON";
				}
				if ($obj->gps_cs != 53) { //Door OFF
					if ($door != "OFF") {
						$no++;
						$no_data = 1;
					}
					if ($no == 0) $no++;

					if ($no_data == 1) {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['OFF']['start'] = $obj;
					} else {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['OFF']['end'] = $obj;
					}

					$no_data++;
					$door = "OFF";
				}
			} else {
				if (substr($obj->gps_msg_ori, 79, 1) == 1) { //Door ON
					if ($door != "ON") {
						$no++;
						$no_data = 1;
					}
					if ($no == 0) $no++;

					if ($no_data == 1) {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['ON']['start'] = $obj;
					} else {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['ON']['end'] = $obj;
					}
					$no_data++;
					$door = "ON";
				}

				if (substr($obj->gps_msg_ori, 79, 1) == 0) { //Door OFF
					if ($door != "OFF") {
						$no++;
						$no_data = 1;
					}
					if ($no == 0) $no++;

					if ($no_data == 1) {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['OFF']['start'] = $obj;
					} else {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['OFF']['end'] = $obj;
					}

					$no_data++;
					$door = "OFF";
				}
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

		foreach ($data as $vehicles => $value_vehicles) {
			foreach ($value_vehicles as $number => $value_number) {
				foreach ($value_number as $door => $report) {
					if (!isset($report['end'])) {
						$report['end'] = $report['start'];
					}
					$data_report[$vehicles][$number][$door]['start'] = $report['start'];
					$data_report[$vehicles][$number][$door]['end'] = $report['end'];

					$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);

					$show_duration = "";
					if ($duration[0] != 0) {
						$show_duration .= $duration[0] . " Day ";
					}
					if ($duration[1] != 0) {
						$show_duration .= $duration[1] . " Hour ";
					}
					if ($duration[2] != 0) {
						$show_duration .= $duration[2] . " Min";
					}
					if ($show_duration == "") {
						$show_duration .= "0 Min";
					}
					$data_report[$vehicles][$number][$door]['duration'] = $show_duration;

					/*$mileage = ($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000;
						$data_report[$vehicles][$number][$door]['mileage'] = round($mileage, 2); */


					if (in_array(strtoupper($rowvehicle->vehicle_type), $door_others)) {
						//start report
						$location_start = $this->getPosition_other($report['start']->gps_longitude_real, $report['start']->gps_latitude_real);
						$data_report[$vehicles][$number][$door]['location_start'] = $location_start;

						$geofence_start = $this->getGeofence_location_other($report['start']->gps_longitude_real, $report['start']->gps_latitude_real, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;

						//end report
						$location_end = $this->getPosition_other($report['end']->gps_longitude_real, $report['end']->gps_latitude_real);
						$data_report[$vehicles][$number][$door]['location_end'] = $location_end;

						$geofence_end = $this->getGeofence_location_other($report['end']->gps_longitude_real, $report['end']->gps_latitude_real, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;
					} else {
						//start report
						$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
						$data_report[$vehicles][$number][$door]['location_start'] = $location_start;

						$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;

						//end report
						$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
						$data_report[$vehicles][$number][$door]['location_end'] = $location_end;

						$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;
					}

					$coordinate_start = $report['start']->gps_latitude_real . " " . $report['start']->gps_longitude_real;
					$coordinate_end = $report['end']->gps_latitude_real . " " . $report['end']->gps_longitude_real;
				}
			}
		}

		$params['data'] = $data_report;
		$params['vehicle'] = $rowvehicle;
		$html = $this->load->view("dashboard/report/vpto_result", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;
	}

	function door()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$userid          = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_level           = $this->sess->user_level;

		$door_list = array("T5DOOR", "X3_DOOR", "TK315DOOR_NEW", "TK510CAMDOOR", "TK510DOOR");

		$this->db = $this->load->database("default", true);
		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");


		if ($user_level == 1) {
			$this->db->where("vehicle_user_id", $userid);
		} elseif ($user_level == 2) {
			$this->db->where("vehicle_company", $user_company);
		} elseif ($user_level == 3) {
			$this->db->where("vehicle_subcompany", $user_subcompany);
		} elseif ($user_level == 4) {
			$this->db->where("vehicle_group", $user_group);
		} else {
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where_in("vehicle_type", $door_list);
		// if ($this->sess->user_type == 2)
		// {
		// 	$this->db->where("vehicle_user_id", $this->sess->user_id);
		// 	$this->db->or_where("vehicle_company", $this->sess->user_company);
		// 	$this->db->where_in("vehicle_type", $door_list);
		// 	$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		// }
		// else
		// if ($this->sess->user_type == 3)
		// {
		// 	$this->db->where("user_agent", $this->sess->user_agent);
		// }

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		/*if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}*/

		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params['code_view_menu'] = "report";

		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/vdoor_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_door()
	{
		$door_others = array("X3_DOOR", "TK315DOOR_NEW", "TK510CAMDOOR", "TK510DOOR");

		$vehicle     = $this->input->post("vehicle");
		$startdate   = $this->input->post("startdate");
		$enddate     = $this->input->post("enddate");
		$shour       = $this->input->post("shour");
		$ehour       = $this->input->post("ehour");

		$error = "";
		if ($vehicle == "" || $vehicle == 0) {
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		}

		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$this->db->where("vehicle_device", $vehicle);
		$q          = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others"))) {
			$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
			$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate . " " . $ehour . ":59")));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":59"));
		}

		/*
		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate ." " . $ehour . ":59")));
		*/

		$now         = date("Y-m-d");
		$cek_date    = date("Y-m-d", strtotime($startdate));
		$cek_enddate = date("Y-m-d", strtotime($enddate));

		if (($cek_date == $now) && ($cek_date == $cek_enddate)) {
			if ($rowvehicle->vehicle_info) {
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
						$database          = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table             = $this->config->item("external_gpstable");
						$tableinfo         = $this->config->item("external_gpsinfotable");
						$this->tblhist     = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist       = $this->tblinfohist;
						$this->db          = $this->load->database($database, TRUE);
					}
				}
			}

			if (!isset($table)) {
				$table             = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo         = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists          = $this->config->item("table_hist");
				$this->tblhist     = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos      = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}
		} else if (($cek_date < $now) && ($cek_date == $cek_enddate)) {
			//History
			$table         = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo     = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if ($this->config->item("is_dbhistory") == 1) {
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
			$action = "history";
		} else {

			if ($rowvehicle->vehicle_info) {
				$json = json_decode($rowvehicle->vehicle_info);
				if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
						$database          = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table             = $this->config->item("external_gpstable");
						$tableinfo         = $this->config->item("external_gpsinfotable");
						$this->tblhist     = $this->config->item("external_gpstable_history");
						$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
						$tblinfohist       = $this->tblinfohist;
						$this->db          = $this->load->database($database, TRUE);
					}
				}
			}

			if (!isset($table)) {
				$table             = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
				$tableinfo         = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
				$tblhists          = $this->config->item("table_hist");
				$this->tblhist     = $tblhists[strtoupper($rowvehicle->vehicle_type)];
				$tblhistinfos      = $this->config->item("table_hist_info");
				$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
			}

			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time", "asc");
			$this->db->from($table);
			$q             = $this->db->get();
			$this->db->flush_cache();
			$rows1         = $q->result();

			$table         = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
			$tableinfo     = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
			$istbl_history = $this->config->item("dbhistory_default");
			if ($this->config->item("is_dbhistory") == 1) {
				$istbl_history = $rowvehicle->vehicle_dbhistory_name;
			}
			$this->db2 = $this->load->database($istbl_history, TRUE);

			$this->db2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db2->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db2->where("gps_time >=", $sdate);
			$this->db2->where("gps_time <=", $edate);
			$this->db2->order_by("gps_time", "asc");
			$this->db2->from($table);
			$q2 = $this->db2->get();
			$this->db2->flush_cache();
			$rows2 = $q2->result();
		}

		if ((($cek_date == $now) && ($cek_date == $cek_enddate)) || (($cek_date < $now) && ($cek_date == $cek_enddate))) {
			$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
			$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
			$this->db->where("gps_time >=", $sdate);
			$this->db->where("gps_time <=", $edate);
			$this->db->order_by("gps_time", "asc");
			$this->db->from($table);
			$q    = $this->db->get();
			$this->db->flush_cache();
			$rows = $q->result();

			//Dikondisikan jika ambil data dari history == 0
			//atau cron history tidak jalan
			if (count($rows) == 0) {

				if (isset($action) && $action == "history") {
					if ($rowvehicle->vehicle_info) {
						$json = json_decode($rowvehicle->vehicle_info);
						if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
							$databases = $this->config->item('databases');
							if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
								$database          = $databases[$json->vehicle_ip][$json->vehicle_port];
								$table             = $this->config->item("external_gpstable");
								$tableinfo         = $this->config->item("external_gpsinfotable");
								$this->tblhist     = $this->config->item("external_gpstable_history");
								$this->tblinfohist = $this->config->item("external_gpsinfotable_history");
								$tblinfohist       = $this->tblinfohist;
								$this->db          = $this->load->database($database, TRUE);
							} else {
								$table             = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
								$tableinfo         = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
								$tblhists          = $this->config->item("table_hist");
								$this->tblhist     = $tblhists[strtoupper($rowvehicle->vehicle_type)];
								$tblhistinfos      = $this->config->item("table_hist_info");
								$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
								$this->db          = $this->load->database("default", TRUE);
							}
						}
					}

					if (!isset($table)) {
						$table             = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
						$tableinfo         = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
						$tblhists          = $this->config->item("table_hist");
						$this->tblhist     = $tblhists[strtoupper($rowvehicle->vehicle_type)];
						$tblhistinfos      = $this->config->item("table_hist_info");
						$this->tblinfohist = $tblhistinfos[strtoupper($rowvehicle->vehicle_type)];
						$this->db          = $this->load->database("default", TRUE);
					}


					$this->db->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->db->where_in("gps_info_device", $rowvehicle->vehicle_device);
					$this->db->where("gps_time >=", $sdate);
					$this->db->where("gps_time <=", $edate);
					$this->db->order_by("gps_time", "asc");
					$this->db->from($table);
					$q = $this->db->get();
					$this->db->flush_cache();
					$rows = $q->result();
				}
			}
			//Finish Condition Jika data history NOL
			//atau cron history ga jalan

		} else {
			$rows = array_merge($rows2, $rows1);
			//print_r($rows);exit;
		}

		$data_report    = array();
		$data           = array(); // initialization variable
		$vehicle_device = "";
		$door           = "";
		//$this->getFanStatus($row->gps->gps_msg_ori);
		//$value = substr($val, 79, 1);


		/* start looping for process data - data dikelompokkan berdasarkan Door Status Open/Close */
		foreach ($rows as $obj) {
			if ($vehicle_device != $rowvehicle->vehicle_device) {
				$no = 0;
				$no_data = 1;
			}
			if (in_array(strtoupper($rowvehicle->vehicle_type), $door_others)) {
				if ($obj->gps_cs == 53) { //Door Open
					if ($door != "OPEN") {
						$no++;
						$no_data = 1;
					}
					if ($no == 0) $no++;

					if ($no_data == 1) {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['OPEN']['start'] = $obj;
					} else {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['OPEN']['end'] = $obj;
					}
					$no_data++;
					$door = "OPEN";
				}
				if ($obj->gps_cs != 53) { //Door Close
					if ($door != "CLOSE") {
						$no++;
						$no_data = 1;
					}
					if ($no == 0) $no++;

					if ($no_data == 1) {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['CLOSE']['start'] = $obj;
					} else {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['CLOSE']['end'] = $obj;
					}

					$no_data++;
					$door = "CLOSE";
				}
			} else {
				if (substr($obj->gps_msg_ori, 79, 1) == 1) { //Door Open
					if ($door != "OPEN") {
						$no++;
						$no_data = 1;
					}
					if ($no == 0) $no++;

					if ($no_data == 1) {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['OPEN']['start'] = $obj;
					} else {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['OPEN']['end'] = $obj;
					}
					$no_data++;
					$door = "OPEN";
				}

				if (substr($obj->gps_msg_ori, 79, 1) == 0) { //Door Close
					if ($door != "CLOSE") {
						$no++;
						$no_data = 1;
					}
					if ($no == 0) $no++;

					if ($no_data == 1) {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['CLOSE']['start'] = $obj;
					} else {
						$data[$rowvehicle->vehicle_no . "#" . $rowvehicle->vehicle_name][$no]['CLOSE']['end'] = $obj;
					}

					$no_data++;
					$door = "CLOSE";
				}
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

		foreach ($data as $vehicles => $value_vehicles) {
			foreach ($value_vehicles as $number => $value_number) {
				foreach ($value_number as $door => $report) {
					if (!isset($report['end'])) {
						$report['end'] = $report['start'];
					}
					$data_report[$vehicles][$number][$door]['start'] = $report['start'];
					$data_report[$vehicles][$number][$door]['end'] = $report['end'];

					$duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);

					$show_duration = "";
					if ($duration[0] != 0) {
						$show_duration .= $duration[0] . " Day ";
					}
					if ($duration[1] != 0) {
						$show_duration .= $duration[1] . " Hour ";
					}
					if ($duration[2] != 0) {
						$show_duration .= $duration[2] . " Min";
					}
					if ($show_duration == "") {
						$show_duration .= "0 Min";
					}
					$data_report[$vehicles][$number][$door]['duration'] = $show_duration;

					/*$mileage = ($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000;
						$data_report[$vehicles][$number][$door]['mileage'] = round($mileage, 2); */


					if (in_array(strtoupper($rowvehicle->vehicle_type), $door_others)) {
						//start report
						$location_start = $this->getPosition_other($report['start']->gps_longitude_real, $report['start']->gps_latitude_real);
						$data_report[$vehicles][$number][$door]['location_start'] = $location_start;

						$geofence_start = $this->getGeofence_location_other($report['start']->gps_longitude_real, $report['start']->gps_latitude_real, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;

						//end report
						$location_end = $this->getPosition_other($report['end']->gps_longitude_real, $report['end']->gps_latitude_real);
						$data_report[$vehicles][$number][$door]['location_end'] = $location_end;

						$geofence_end = $this->getGeofence_location_other($report['end']->gps_longitude_real, $report['end']->gps_latitude_real, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;
					} else {
						//start report
						$location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
						$data_report[$vehicles][$number][$door]['location_start'] = $location_start;

						$geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$door]['geofence_start'] = $geofence_start;

						//end report
						$location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
						$data_report[$vehicles][$number][$door]['location_end'] = $location_end;

						$geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $rowvehicle->vehicle_user_id);
						$data_report[$vehicles][$number][$door]['geofence_end'] = $geofence_end;
					}

					$coordinate_start = $report['start']->gps_latitude_real . " " . $report['start']->gps_longitude_real;
					$coordinate_end = $report['end']->gps_latitude_real . " " . $report['end']->gps_longitude_real;
				}
			}
		}



		$params['data']    = $data_report;
		$params['vehicle'] = $rowvehicle;
		$html              = $this->load->view("dashboard/report/vdoor_result", $params, true);
		$callback['error'] = false;
		$callback['html']  = $html;
		echo json_encode($callback);
		return;
	}

	function geofence()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$userid          = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_level           = $this->sess->user_level;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		$this->db = $this->load->database("default", true);
		if ($user_level == 1) {
			$this->db->where("vehicle_user_id", $userid);
		} elseif ($user_level == 2) {
			$this->db->where("vehicle_company", $user_company);
		} elseif ($user_level == 3) {
			$this->db->where("vehicle_subcompany", $user_subcompany);
		} elseif ($user_level == 4) {
			$this->db->where("vehicle_group", $user_group);
		} else {
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
		}

		$rows = $q->result();
		//print_r($rows);exit;
		$this->params["vehicles"]       = $rows;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/report/vgeofence_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_geofence()
	{
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");

		$ve = $this->input->post("vehicle");

		$this->db->where("vehicle_device", $ve);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$vehicle_nopol = $rowvehicle->vehicle_no;

		$params['vehicle'] = $rowvehicle;

		$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . "00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . "23:59:59"));
		//print_r($edate);exit;

		$this->db->order_by("geoalert_time", "asc");
		$this->db->where("geoalert_vehicle", $ve);
		$this->db->where("geoalert_time >=", $sdate);
		$this->db->where("geoalert_time <=", $edate);
		$this->db->join("geofence", "geofence_id = geoalert_geofence", "left outer");
		$q = $this->db->get("geofence_alert");
		$rows = $q->result();

		//print_r($rows);exit;

		for ($i = 0; $i < count($rows); $i++) {
			$rows[$i]->geoalert_time_t = dbmaketime($rows[$i]->geoalert_time);
		}

		$params['data'] = $rows;
		$html = $this->load->view("dashboard/report/vgeofence_result", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
	}

	function poweroff()
	{

		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2) {
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		} else
		if ($this->sess->user_type == 3) {
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
		}

		$rows = $q->result();
		$this->params["vehicles"] = $rows;

		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/vpoweroff_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_poweroff()
	{
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		$type = $this->input->post("type");

		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour . ":00")));
		$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate . " " . $ehour . ":59")));
		$user_dblive = $this->sess->user_dblive;


		//get table port
		$vehicle_ex = explode("@", $rowvehicle->vehicle_device);
		$name = $vehicle_ex[0];
		$host = $vehicle_ex[1];

		$alert_code = array('BO010', 'dt');
		$tables = $this->gpsmodel->getTable($rowvehicle);

		$this->db = $this->load->database($user_dblive, TRUE);
		$this->db->select("gps_name,gps_alert,gps_time,gps_latitude_real,gps_longitude_real,gps_longitude,gps_latitude,gps_ew,gps_ns");
		$this->db->order_by("gps_id", "asc");
		$this->db->where("gps_name", $name);
		$this->db->where("gps_host", $host);
		$this->db->where_in("gps_alert", $alert_code);
		$this->db->where("gps_time >=", $sdate);
		$this->db->where("gps_time <=", $edate);
		$qalert = $this->db->get("gps_alert");
		$rowalert = $qalert->result();


		$params['data'] = $rowalert;
		$params['vehicle'] = $rowvehicle;
		$html = $this->load->view("dashboard/report/vpoweroff_result", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;

		echo json_encode($callback);

		return;
	}

	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_user)
	{

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

		if ($q->num_rows() > 0) {
			$row = $q->result();
			$total = $q->num_rows();
			for ($i = 0; $i < $total; $i++) {
				$data = $row[$i]->geofence_name;
				return $data;
			}
		} else {
			return false;
		}
	}

	function getGeofence_location_other($longitude, $latitude, $vehicle_user)
	{

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
		if ($q->num_rows() > 0) {
			$row = $q->result();
			$total = $q->num_rows();
			for ($i = 0; $i < $total; $i++) {
				$data = $row[$i]->geofence_name;
				return $data;
			}
		} else {
			return false;
		}
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

	function getPosition_other($longitude, $latitude)
	{
		$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);
		return $georeverse;
	}

	function get_vehicle_by_company($id)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$this->db->order_by("vehicle_no", "asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
		$this->db->where("vehicle_company", $id);
		if ($this->sess->user_group > 0) {
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->join("company", "vehicle_company = company_id", "left");
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if ($qd->num_rows() > 0) {
			$options = "<option value='0' selected='selected' >--Select Vehicle--</option>";
			foreach ($rd as $obj) {
				$options .= "<option value='" . $obj->vehicle_device . "'>" . $obj->vehicle_no . " - " . $obj->vehicle_name . " " . "(" . $obj->company_name . ")" . "</option>";
			}

			echo $options;
			return;
		}
	}

	function ritase()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$userid          = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_level           = $this->sess->user_level;

		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		$this->db = $this->load->database("default", true);
		if ($user_level == 1) {
			$this->db->where("vehicle_user_id", $userid);
		} elseif ($user_level == 2) {
			$this->db->where("vehicle_company", $user_company);
		} elseif ($user_level == 3) {
			$this->db->where("vehicle_subcompany", $user_subcompany);
		} elseif ($user_level == 4) {
			$this->db->where("vehicle_group", $user_group);
		} else {
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}

		// if ($this->sess->user_type == 2)
		// {
		//     $this->db->where("vehicle_user_id", $this->sess->user_id);
		//     $this->db->or_where("vehicle_company", $this->sess->user_company);
		//     $this->db->where("vehicle_active_date2 >=", date("Ymd"));
		// }

		$q_vehicle = $this->db->get("vehicle");
		$row_vehicle = $q_vehicle->result();
		//print_r($row_vehicle);exit;

		$this->db->cache_delete_all();

		$this->dbtransporter = $this->load->database("transporter", true);

		$this->dbtransporter->order_by("ritase_geofence_name", "asc");
		$this->dbtransporter->where("ritase_company", $this->sess->user_company);
		$this->dbtransporter->where("ritase_status", "1");

		$q_ritase                    = $this->dbtransporter->get("ritase");
		$row_ritase                  = $q_ritase->result();

		$this->dbtransporter->cache_delete_all();

		$this->params["vehicle"]     = $row_vehicle;
		$this->params["ritase"]      = $row_ritase;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/report/v_ritase_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function ritase_report()
	{
		$vehicle_device = $this->input->post("vehicle");

		$startdate      = $this->input->post("sdate");
		$sdate          = date("Y-m-d H:i:s", strtotime($startdate . " " . "00:00:00"));

		$enddate        = $this->input->post("enddate");
		$edate          = date("Y-m-d H:i:s", strtotime($enddate . " " . "23:59:59"));

		$ritase         = $this->input->post("ritase");
		$exRitase       = explode(",", $ritase);
		$ritase_id      = $exRitase[0];
		$ritase_name    = $exRitase[1];

		$this->db->order_by("geoalert_time", "asc");
		$this->db->where("geoalert_vehicle", $vehicle_device);
		$this->db->where("geoalert_time >=", $sdate);
		$this->db->where("geoalert_time <=", $edate);
		$this->db->join("geofence", "geofence_id = geoalert_geofence", "leftouter");
		$this->db->where("geofence_name", $ritase_name);
		$q    = $this->db->get("geofence_alert");
		$rows = $q->result();

		//print_r($rows);exit;

		$this->db->cache_delete_all();

		for ($i = 0; $i < count($rows); $i++) {
			$rows[$i]->geoalert_time_t = dbmaketime($rows[$i]->geoalert_time);
		}

		$params["data"]       = $rows;
		$params["start_date"] = $startdate;
		$params["end_date"]   = $enddate;

		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";

		$html = $this->load->view("dashboard/report/v_ritase_report_result", $params, true);

		$callback["error"] = false;
		$callback["html"] = $html;

		echo json_encode($callback);
	}


	function brake()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		// if ($this->sess->user_level != 1)
		// {
		// 	redirect(base_url());
		// }

		$rows                           = $this->dashboardmodel->getvehicle_report();
		$rows_company                   = $this->dashboardmodel->get_company_bylevel();
		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/report/vbrake_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_brake($name = "", $host = "", $startdate = "", $shour = "", $ehour = "", $enddate = "")
	{
		ini_set('display_errors', 1);
		$company          = $this->input->post("company");
		$vehicle          = $this->input->post("vehicle");
		$startdate        = $this->input->post("startdate");
		$enddate          = $this->input->post("enddate");
		$shour            = $this->input->post("shour");
		$ehour            = $this->input->post("ehour");
		$speedfilterbrake = $this->input->post("speedfilterbrake");

		$mapview          = 0;
		$tableview        = 1;

		$vehicle_no       = "-";
		$vehicle_odometer = 0;
		$vehicle_type     = "-";
		$vehicle_user_id  = 0;
		$error            = "";

		/*if ($company == "" || $company == 0)
		{
			$error = "- Invalid Vehicle. Silahkan Pilih Area/Cabang! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}*/

		if ($vehicle == "" || $vehicle == 0) {
			$error = "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		} else {
			$datavehicle = explode("@", $vehicle);
			$name        = $datavehicle[0];
			$host        = $datavehicle[1];

			$this->db->order_by("vehicle_id", "asc");
			$this->db->where("vehicle_device", $name . "@" . $host);
			$this->db->where("vehicle_status <>", 3);
			$q           = $this->db->get("vehicle");
			$rowvehicle  = $q->row();

			if (count($rowvehicle) > 0) {

				$vehicle_no       = $rowvehicle->vehicle_no;
				$vehicle_odometer = $rowvehicle->vehicle_odometer;
				$vehicle_type     = $rowvehicle->vehicle_type;
				$vehicle_user_id  = $rowvehicle->vehicle_user_id;

				if (isset($rowvehicle->vehicle_type) && (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))) {
					$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
					$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
				} else {
					$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour)));
					$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate . " " . $ehour)));
				}
			}
		}

		if ($startdate == "" || $enddate == "") {
			$error = "- Invalid Vehicle. Silahkan Tanggal Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($shour == "" || $ehour == "") {
			$error = "- Invalid Vehicle. Silahkan Jam Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($mapview == "1") {
			if ($startdate != $enddate) {
				$error = "- Untuk menampilkan History Map, silahkan pilih di tanggal report yang sama! \n";
				$callback['error'] = true;
				$callback['message'] = $error;

				echo json_encode($callback);
				return;
			}
		}
		if ($tableview == "1") {
			if ($startdate != $enddate) {
				$error = "- Untuk menampilkan Table, silahkan pilih di tanggal report yang sama! \n";
				$callback['error'] = true;
				$callback['message'] = $error;

				echo json_encode($callback);
				return;
			}
		}
		//PORT Only
		if (isset($rowvehicle->vehicle_info)) {
			$json = json_decode($rowvehicle->vehicle_info);
			if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
				$databases = $this->config->item('databases');
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
					$database      = $databases[$json->vehicle_ip][$json->vehicle_port];
					$table         = $this->config->item("external_gpstable");
					$tableinfo     = $this->config->item("external_gpsinfotable");
					$this->dbhist  = $this->load->database($database, TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				} else {
					$table         = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
					$tableinfo     = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
					$this->dbhist  = $this->load->database("default", TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				}

				$vehicle_device = explode("@", $rowvehicle->vehicle_device);
				$vehicle_no     = $rowvehicle->vehicle_no;
				$vehicle_dev    = $rowvehicle->vehicle_device;
				$vehicle_name   = $rowvehicle->vehicle_name;
				$vehicle_type   = $rowvehicle->vehicle_type;

				if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE") {
					$tablehist     = $vehicle_device[0] . "@t5_gps";
					$tablehistinfo = $vehicle_device[0] . "@t5_info";
				} else {
					$tablehist     = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_gps";
					$tablehistinfo = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_info";
				}


				$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,gps_cs,
												   gps_info_device,gps_info_io_port,gps_info_distance");
				$this->dbhist->where("gps_info_device", $rowvehicle->vehicle_device);
				$this->dbhist->where("gps_time >=", $sdate);
				$this->dbhist->where("gps_time <=", $edate);
				$this->dbhist->where("gps_cs", 53);

				if ($speedfilterbrake == 0) {
					$this->dbhist->where("gps_speed =", 0);
				} else {
					$this->dbhist->where("gps_speed >=", 0);
					$this->dbhist->where("gps_speed <=", 2);
				}

				$this->dbhist->order_by("gps_time", "asc");
				$this->dbhist->group_by("gps_time");
				$this->dbhist->limit(3000);
				$this->dbhist->from($table);
				$q     = $this->dbhist->get();
				$rows1 = $q->result();


				$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,gps_cs,
												   gps_info_device,gps_info_io_port,gps_info_distance");
				$this->dbhist2->where("gps_info_device", $rowvehicle->vehicle_device);
				$this->dbhist2->where("gps_time >=", $sdate);
				$this->dbhist2->where("gps_time <=", $edate);
				$this->dbhist2->where("gps_cs", 53);
				if ($speedfilterbrake == 0) {
					$this->dbhist->where("gps_speed =", 0);
				} else {
					$this->dbhist->where("gps_speed >=", 0);
					$this->dbhist->where("gps_speed <=", 2);
				}
				$this->dbhist2->order_by("gps_time", "asc");
				$this->dbhist2->group_by("gps_time");
				$this->dbhist2->limit(3000);
				$this->dbhist2->from($tablehist);
				$q2    = $this->dbhist2->get();
				$rows2 = $q2->result();

				$rows  = array_merge($rows1, $rows2); //limit data rows = 3000
				$trows = count($rows);

				if ($trows > 3000) {

					$error               = "- Tidak dapat menampilkan Report. Silahkan kurangi tanggal Report yang dipilih! \n";
					$callback['error']   = true;
					$callback['message'] = $error;

					echo json_encode($callback);
					return;
				}


				/*
							$data = json_encode($rowsummary);
							$data2 = json_decode($data);*/

				//$data = json_encode($data);
				//print_r($data);exit();
			}
		}


		if ($error != "") {
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$datafix = array();
		for ($x = 0; $x < sizeof($rows); $x++) {
			$position = $this->gpsmodel->GeoReverse($rows[$x]->gps_latitude_real, $rows[$x]->gps_longitude_real);

			array_push($datafix, array(
				"gps_id"             => $rows[$x]->gps_id,
				"gps_name"           => $rows[$x]->gps_name,
				"gps_host"           => $rows[$x]->gps_host,
				"gps_speed"          => $rows[$x]->gps_speed,
				"gps_status"         => $rows[$x]->gps_status,
				"gps_latitude_real"  => $rows[$x]->gps_latitude_real,
				"gps_longitude_real" => $rows[$x]->gps_longitude_real,
				"gps_time"           => $rows[$x]->gps_time,
				"gps_cs"             => $rows[$x]->gps_cs,
				"gps_info_device"    => $rows[$x]->gps_info_device,
				"gps_info_io_port"   => $rows[$x]->gps_info_io_port,
				"gps_info_distance"  => $rows[$x]->gps_info_distance,
				"gps_position"       => $position->display_name
			));
		}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

		$params['tableview']        = $tableview;
		$params['vehicle_no']       = $vehicle_no;
		$params['vehicle_name']     = $vehicle_name;
		$params['vehicle_odometer'] = $vehicle_odometer;
		$params['vehicle_type']     = $vehicle_type;
		$params['vehicle_user_id']  = $vehicle_user_id;
		$params['totalgps']         = $trows;
		$params['data']             = $datafix;
		$params['sdate']            = $sdate;
		$params['edate']            = $edate;
		$html                       = $this->load->view("dashboard/report/vbrake_report_result", $params, true);

		$callback['error']          = false;
		$callback['html']           = $html;
		echo json_encode($callback);
	}

	function historywithdirection()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		// if ($this->sess->user_level != 1)
		// {
		// 	redirect(base_url());
		// }

		$rows                           = $this->dashboardmodel->getvehicle_report();
		$rows_company                   = $this->dashboardmodel->get_company_bylevel();
		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/report/vhistory_report_withdirection', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search_history_direction($name = "", $host = "", $startdate = "", $shour = "", $ehour = "", $enddate = "")
	{
		ini_set('display_errors', 1);
		$company = $this->input->post("company");
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		$mapview = 1;
		$tableview = ""; //disable sementara

		$vehicle_no = "-";
		$vehicle_odometer = 0;
		$vehicle_type = "-";
		$vehicle_user_id = 0;
		$error = "";

		/*if ($company == "" || $company == 0)
		{
			$error = "- Invalid Vehicle. Silahkan Pilih Area/Cabang! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}*/

		if ($vehicle == "" || $vehicle == 0) {
			$error = "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		} else {
			$datavehicle = explode("@", $vehicle);
			$name = $datavehicle[0];
			$host = $datavehicle[1];

			$this->db->order_by("vehicle_id", "asc");
			$this->db->where("vehicle_device", $name . "@" . $host);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			$rowvehicle = $q->row();

			if (count($rowvehicle) > 0) {

				$vehicle_no = $rowvehicle->vehicle_no;
				$vehicle_odometer = $rowvehicle->vehicle_odometer;
				$vehicle_type = $rowvehicle->vehicle_type;
				$vehicle_user_id = $rowvehicle->vehicle_user_id;

				if (isset($rowvehicle->vehicle_type) && (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))) {
					$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
					$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
				} else {
					$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate . " " . $shour)));
					$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate . " " . $ehour)));
				}
			}
		}

		if ($startdate == "" || $enddate == "") {
			$error = "- Invalid Vehicle. Silahkan Tanggal Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($shour == "" || $ehour == "") {
			$error = "- Invalid Vehicle. Silahkan Jam Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($mapview == "1") {
			if ($startdate != $enddate) {
				$error = "- Untuk menampilkan History Map, silahkan pilih di tanggal report yang sama! \n";
				$callback['error'] = true;
				$callback['message'] = $error;

				echo json_encode($callback);
				return;
			}
		}
		if ($tableview == "1") {
			if ($startdate != $enddate) {
				$error = "- Untuk menampilkan Table, silahkan pilih di tanggal report yang sama! \n";
				$callback['error'] = true;
				$callback['message'] = $error;

				echo json_encode($callback);
				return;
			}
		}
		//PORT Only
		if (isset($rowvehicle->vehicle_info)) {
			$json = json_decode($rowvehicle->vehicle_info);
			if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
				$databases = $this->config->item('databases');
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					$table = $this->config->item("external_gpstable");
					$tableinfo = $this->config->item("external_gpsinfotable");
					$this->dbhist = $this->load->database($database, TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				} else {
					$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
					$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
					$this->dbhist = $this->load->database("default", TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				}

				$vehicle_device = explode("@", $rowvehicle->vehicle_device);
				$vehicle_no = $rowvehicle->vehicle_no;
				$vehicle_dev = $rowvehicle->vehicle_device;
				$vehicle_name = $rowvehicle->vehicle_name;
				$vehicle_type = $rowvehicle->vehicle_type;

				if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE") {
					$tablehist = $vehicle_device[0] . "@t5_gps";
					$tablehistinfo = $vehicle_device[0] . "@t5_info";
				} else {
					$tablehist = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_gps";
					$tablehistinfo = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_info";
				}


				$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
												   gps_longitude,gps_latitude,gps_ew,gps_ns,
												   gps_info_device,gps_info_io_port,gps_info_distance");
				$this->dbhist->where("gps_info_device", $rowvehicle->vehicle_device);
				$this->dbhist->where("gps_time >=", $sdate);
				$this->dbhist->where("gps_time <=", $edate);
				$this->dbhist->order_by("gps_time", "asc");
				$this->dbhist->limit(3000);
				$this->dbhist->from($table);
				$q = $this->dbhist->get();
				$rows1 = $q->result();


				$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
												   gps_longitude,gps_latitude,gps_ew,gps_ns,
												   gps_info_device,gps_info_io_port,gps_info_distance");
				$this->dbhist2->where("gps_info_device", $rowvehicle->vehicle_device);
				$this->dbhist2->where("gps_time >=", $sdate);
				$this->dbhist2->where("gps_time <=", $edate);
				$this->dbhist2->order_by("gps_time", "asc");
				$this->dbhist2->limit(3000);
				$this->dbhist2->from($tablehist);
				$q2 = $this->dbhist2->get();
				$rows2 = $q2->result();

				$rows = array_merge($rows1, $rows2); //limit data rows = 3000
				$trows = count($rows);

				if (($trows > 3000) && ($mapview == 1)) {

					$error = "- Cannot View, Silahkan kurangi tanggal Report yang dipilih! \n";
					$callback['error'] = true;
					$callback['message'] = $error;

					echo json_encode($callback);
					return;
				}

				for ($i = count($rows) - 1; $i >= 0; $i--) {
					if (($i + 1) >= count($rows)) {
						$rowsummary[] = $rows[$i];
						continue;
					}
					$latbefore = $rows[$i + 1]->gps_latitude_real;
					$lngbefore = $rows[$i + 1]->gps_longitude_real;
					$latcurrent = $rows[$i]->gps_latitude_real;
					$lngcurrent = $rows[$i]->gps_longitude_real;
					if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent)) {
						$rowsummary[] = $rows[$i];
						continue;
					}
					if ($rows[$i + 1]->gps_speed != $rows[$i]->gps_speed) {
						$rowsummary[] = $rows[$i];
						continue;
					}
				}
				if (isset($rowsummary)) {
					$data = $rowsummary;
					$data = $this->dashboardmodel->array_sort($data, 'gps_time', SORT_ASC);
					$totaldata = count($data);
				} else {

					$data = "";
					$totaldata = 0;
				}

				/*
							$data = json_encode($rowsummary);
							$data2 = json_decode($data);*/

				//$data = json_encode($data);
				//print_r($data);exit();
			}
		}


		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$params['mapview'] = $mapview;
		$params['tableview'] = $tableview;
		$params['vehicle_no'] = $vehicle_no;
		$params['vehicle_odometer'] = $vehicle_odometer;
		$params['vehicle_type'] = $vehicle_type;
		$params['vehicle_user_id'] = $vehicle_user_id;
		$params['totalgps'] = $trows;
		$params['data'] = $data;
		$params['totaldata'] = $totaldata;
		$params['sdate'] = $sdate;
		$params['edate'] = $edate;
		$html = $this->load->view("dashboard/report/vhistory_report_resultdirection", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
	}

	function history_std()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$privilegecode                         = $this->sess->user_id_role;

		$rows                           = $this->dashboardmodel->getvehicle_report();
		$rows_company                   = $this->dashboardmodel->get_company_bylevel();
		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_std', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function search_history_std($name = "", $host = "", $startdate = "", $shour = "", $ehour = "", $enddate = "")
	{
		ini_set('display_errors', 1);
		$company          = $this->input->post("company");
		$vehicle          = $this->input->post("vehicle");
		$startdate        = $this->input->post("startdate");
		$enddate          = $this->input->post("enddate");
		$shour            = $this->input->post("shour");
		$ehour            = $this->input->post("ehour");
		$mapview          = 1;
		$tableview        = ""; //disable sementara

		$vehicle_no       = "-";
		$vehicle_odometer = 0;
		$vehicle_type     = "-";
		$vehicle_user_id  = 0;
		$error            = "";

		/*if ($company == "" || $company == 0)
		{
			$error = "- Invalid Vehicle. Silahkan Pilih Area/Cabang! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}*/

		if ($vehicle == "" || $vehicle == 0) {
			$error = "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		} else {
			$datavehicle = explode("@", $vehicle);
			$name = $datavehicle[0];
			$host = $datavehicle[1];

			$this->db->order_by("vehicle_id", "asc");
			$this->db->where("vehicle_device", $name . "@" . $host);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			$rowvehicle = $q->row();

			if (count($rowvehicle) > 0) {

				$vehicle_no = $rowvehicle->vehicle_no;
				$vehicle_odometer = $rowvehicle->vehicle_odometer;
				$vehicle_type = $rowvehicle->vehicle_type;
				$vehicle_user_id = $rowvehicle->vehicle_user_id;

				/*if (isset($rowvehicle->vehicle_type) && (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))) {
					$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
					$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
				}else{
					$sdate = date("Y-m-d H:i:s", strtotime("-6 hour", strtotime($startdate." ".$shour))); //wita
					$edate = date("Y-m-d H:i:s", strtotime("-6 hour", strtotime($enddate." ".$ehour)));	//wita
				}*/

				$sdate = date("Y-m-d H:i:s", strtotime("-6 hour", strtotime($startdate . " " . $shour))); //wita
				$edate = date("Y-m-d H:i:s", strtotime("-6 hour", strtotime($enddate . " " . $ehour)));    //wita
			}
		}

		if ($startdate == "" || $enddate == "") {
			$error = "- Invalid Vehicle. Silahkan Tanggal Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($shour == "" || $ehour == "") {
			$error = "- Invalid Vehicle. Silahkan Jam Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($mapview == "1") {
			if ($startdate != $enddate) {
				$error = "- Untuk menampilkan History Map, silahkan pilih di tanggal report yang sama! \n";
				$callback['error'] = true;
				$callback['message'] = $error;

				echo json_encode($callback);
				return;
			}
		}
		if ($tableview == "1") {
			if ($startdate != $enddate) {
				$error = "- Untuk menampilkan Table, silahkan pilih di tanggal report yang sama! \n";
				$callback['error'] = true;
				$callback['message'] = $error;

				echo json_encode($callback);
				return;
			}
		}
		//PORT Only
		if (isset($rowvehicle->vehicle_info)) {
			$json = json_decode($rowvehicle->vehicle_info);
			if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
				$databases = $this->config->item('databases');
				if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {
					$database = $databases[$json->vehicle_ip][$json->vehicle_port];
					$table = $this->config->item("external_gpstable");
					$tableinfo = $this->config->item("external_gpsinfotable");
					$this->dbhist = $this->load->database($database, TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				} else {
					$table = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
					$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
					$this->dbhist = $this->load->database("default", TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				}

				$vehicle_device = explode("@", $rowvehicle->vehicle_device);
				$vehicle_no = $rowvehicle->vehicle_no;
				$vehicle_dev = $rowvehicle->vehicle_device;
				$vehicle_name = $rowvehicle->vehicle_name;
				$vehicle_type = $rowvehicle->vehicle_type;

				if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE") {
					$tablehist = $vehicle_device[0] . "@t5_gps";
					$tablehistinfo = $vehicle_device[0] . "@t5_info";
				} else {
					$tablehist = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_gps";
					$tablehistinfo = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_info";
				}


				//$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
												   gps_longitude,gps_latitude,gps_ew,gps_ns");
				$this->dbhist->where("gps_name", $vehicle_device[0]);
				$this->dbhist->where("gps_speed >", 0);
				$this->dbhist->where("gps_time >=", $sdate);
				$this->dbhist->where("gps_time <=", $edate);
				$this->dbhist->order_by("gps_time", "asc");
				$this->dbhist->group_by("gps_time");
				//$this->dbhist->limit(6000);
				$this->dbhist->from($table);
				$q = $this->dbhist->get();
				$rows1 = $q->result();


				//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
				$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
												   gps_longitude,gps_latitude,gps_ew,gps_ns");
				$this->dbhist2->where("gps_name", $vehicle_device[0]);
				$this->dbhist2->where("gps_speed >", 0);
				$this->dbhist2->where("gps_time >=", $sdate);
				$this->dbhist2->where("gps_time <=", $edate);
				$this->dbhist2->order_by("gps_time", "asc");
				$this->dbhist2->group_by("gps_time");
				//$this->dbhist2->limit(6000);
				$this->dbhist2->from($tablehist);
				$q2 = $this->dbhist2->get();
				$rows2 = $q2->result();

				$rows = array_merge($rows1, $rows2); //limit data rows = 3000
				$trows = count($rows);

				$totaldata = $trows;
				$data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
				//print_r($totaldata);exit();
				/*if(($trows > 6000) && ($mapview == 1)){

								$error = "- Cannot View, Silahkan kurangi tanggal Report yang dipilih! \n";
								$callback['error'] = true;
								$callback['message'] = $error;

								echo json_encode($callback);
								return;
							}*/

				/*for($i=count($rows)-1; $i >= 0; $i--)
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
							if(isset($rowsummary)){
								$data = $rowsummary;
								$data = $this->dashboardmodel->array_sort($data, 'gps_time', SORT_ASC);
								$totaldata = count($data);
							}else{

								$data = "";
								$totaldata = 0;
							}*/

				/*
							$data = json_encode($rowsummary);
							$data2 = json_decode($data);*/

				//$data = json_encode($data);
				//print_r($data);exit();
			}
		}


		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		// echo "<pre>";
		// var_dump($vehicle_name);die();
		// echo "<pre>";

		$params['mapview']          = $mapview;
		$params['tableview']        = $tableview;
		$params['vehicle_no']       = $vehicle_no;
		$params['vehicle_name']     = $vehicle_name;
		$params['vehicle_odometer'] = $vehicle_odometer;
		$params['vehicle_type']     = $vehicle_type;
		$params['vehicle_user_id']  = $vehicle_user_id;
		$params['totalgps']         = $trows;
		$params['data']             = $data;
		$params['totaldata']        = $totaldata;
		$params['sdate']            = $sdate;
		$params['edate']            = $edate;
		$html                       = $this->load->view("newdashboard/report/vhistory_report_result_std", $params, true);

		$callback['error']          = false;
		$callback['html']           = $html;
		echo json_encode($callback);
	}

	function playbackhistory()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$privilegecode                                     = $this->sess->user_id_role;

		$rows                           = $this->dashboardmodel->getvehicle_report();
		$rows_company                   = $this->dashboardmodel->get_company_bylevel();
		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_withplayback', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_withplayback', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_withplayback', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_withplayback', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_withplayback', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_withplayback', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vhistory_report_withplayback', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function search_history_withplayback($name = "", $host = "", $startdate = "", $shour = "", $ehour = "", $enddate = "")
	{
		ini_set('display_errors', 1);
		$company          = $this->input->post("company");
		$vehicle          = $this->input->post("vehicle");
		$startdate        = $this->input->post("startdate");
		$enddate          = $this->input->post("enddate");
		$shour            = $this->input->post("shour");
		$ehour            = $this->input->post("ehour");
		$mapview          = 1;
		$tableview        = ""; //disable sementara

		$vehicle_no       = "-";
		$vehicle_odometer = 0;
		$vehicle_type     = "-";
		$vehicle_user_id  = 0;
		$error            = "";

		/*if ($company == "" || $company == 0)
		{
			$error = "- Invalid Vehicle. Silahkan Pilih Area/Cabang! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}*/

		if ($vehicle == "" || $vehicle == 0) {
			$error = "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		} else {
			$datavehicle = explode("@", $vehicle);
			$name = $datavehicle[0];
			$host = $datavehicle[1];

			$this->db->order_by("vehicle_id", "asc");
			$this->db->where("vehicle_device", $name . "@" . $host);
			$this->db->where("vehicle_status <>", 3);
			$q = $this->db->get("vehicle");
			$rowvehicle = $q->row();

			if (count($rowvehicle) > 0) {

				$vehicle_no       = $rowvehicle->vehicle_no;
				$vehicle_odometer = $rowvehicle->vehicle_odometer;
				$vehicle_type     = $rowvehicle->vehicle_type;
				$vehicle_user_id  = $rowvehicle->vehicle_user_id;

				/*if (isset($rowvehicle->vehicle_type) && (in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))) {
					$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
					$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
				}else{
					$sdate = date("Y-m-d H:i:s", strtotime("-6 hour", strtotime($startdate." ".$shour))); //wita
					$edate = date("Y-m-d H:i:s", strtotime("-6 hour", strtotime($enddate." ".$ehour)));	//wita
				}*/

				$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour)- 7*3600); //wita
				$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour)- 7*3600);    //wita
			}
		}


		if ($startdate == "" || $enddate == "") {
			$error = "- Invalid Vehicle. Silahkan Tanggal Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($shour == "" || $ehour == "") {
			$error = "- Invalid Vehicle. Silahkan Jam Report yang ingin ditampilkan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		// if ($mapview == 1) {
		// if ($startdate != $enddate) {
		$err = false;
		$diff = strtotime($enddate) - strtotime($startdate);
		if ($diff < 0) {
			$error = " Tanggal tidak benar \n";
			$err = true;
		} else if ($diff > 86400) {
			$error = " maksimal tanggal yang dipilih hanya boleh dua hari \n";
			$err = true;
		}
		if ($err) {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}
		// }
		// }

		// if ($tableview == "1") {
		//     if ($startdate != $enddate) {
		//         $diff = strtotime($enddate) - strtotime($startdate);
		//         if ($diff < 0) {
		//             $error = " Tanggal tidak benar \n";
		//             $err = true;
		//         } else if ($diff > 86400) {
		//             $error = " maksimal jarak tanggal yang dipilih hanya boleh satu hari \n";
		//             $err = true;
		//         }
		//         if ($err) {
		//             $callback['error'] = true;
		//             $callback['message'] = $error;

		//             echo json_encode($callback);
		//             return;
		//         }
		//     }
		// }

		//PORT Only
		// echo "<pre>";
		// var_dump($rowvehicle->vehicle_info);die();
		// echo "<pre>";

		if (isset($rowvehicle->vehicle_info)) {
			$json = json_decode($rowvehicle->vehicle_info);

			if (isset($json->vehicle_ip) && isset($json->vehicle_port)) {
				$databases = $this->config->item('databases');

				if (isset($databases[$json->vehicle_ip][$json->vehicle_port])) {

					$database = $databases[$json->vehicle_ip][$json->vehicle_port];

					$table         = $this->config->item("external_gpstable");
					$tableinfo     = $this->config->item("external_gpsinfotable");


					$this->dbhist  = $this->load->database($database, TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				} else {
					$table         = $this->gpsmodel->getGPSTable($rowvehicle->vehicle_type);
					$tableinfo     = $this->gpsmodel->getGPSInfoTable($rowvehicle->vehicle_type);
					$this->dbhist  = $this->load->database("default", TRUE);
					$this->dbhist2 = $this->load->database("gpshistory", true);
				}

				$vehicle_device = explode("@", $rowvehicle->vehicle_device);
				$vehicle_no     = $rowvehicle->vehicle_no;
				$vehicle_dev    = $rowvehicle->vehicle_device;
				$vehicle_name   = $rowvehicle->vehicle_name;
				$vehicle_type   = $rowvehicle->vehicle_type;

				if ($rowvehicle->vehicle_type == "T5" || $rowvehicle->vehicle_type == "T5 PULSE") {
					$tablehist     = $vehicle_device[0] . "@t5_gps";
					$tablehistinfo = $vehicle_device[0] . "@t5_info";
				} else {
					$tablehist     = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_gps";
					$tablehistinfo = strtolower($vehicle_device[0]) . "@" . strtolower($vehicle_device[1]) . "_info";
				}

				// echo "<pre>";
				// var_dump($table.'-'.$tablehist.'-'.$tablehistinfo);die();
				// echo "<pre>";

				// echo "<pre>";
				// var_dump($tablehist.'-'.$tablehistinfo);die();
				// echo "<pre>";

				$rows1 = array();
				$rows2 = array();
				$rows3 = array();
				$rows4 = array();

				// echo "<pre>";
				// var_dump($sdate.'-'.$edate);die();
				// echo "<pre>";

				if ($startdate == $enddate)
				{
					if ($rowvehicle->vehicle_imei = "869926046501587")
					{
						//$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
						$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
	                								   gps_longitude,gps_latitude,gps_ew,gps_ns");
						$this->dbhist->where("gps_name", $vehicle_device[0]);
						$this->dbhist->where("gps_host", $vehicle_device[1]);
						// $this->dbhist->where("gps_speed >", 0);
						// $this->dbhist2->where("gps_status", "A");
						$this->dbhist->where("gps_time >=", $sdate);
						$this->dbhist->where("gps_time <=", $edate);
						$this->dbhist->order_by("gps_time", "asc");
						$this->dbhist->group_by("gps_time");
						//$this->dbhist->limit(6000);
						// $this->dbhist->from($table);
						$q     = $this->dbhist->get($table);
						$rows1 = $q->result();

						// KONDISI KHUSUS RBT BEP 638
						// $this->db638  = $this->load->database("GPS_VT200_TIB_17008", TRUE);
						// $this->db638->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
	          //       								   gps_longitude,gps_latitude,gps_ew,gps_ns");
						// $this->db638->where("gps_name", $vehicle_device[0]);
						// $this->db638->where("gps_host", $vehicle_device[1]);
						// // $this->db638->where("gps_speed >", 0);
						// // $this->db6382->where("gps_status", "A");
						// $this->db638->where("gps_time >=", $sdate);
						// $this->db638->where("gps_time <=", $edate);
						// $this->db638->order_by("gps_time", "asc");
						// $this->db638->group_by("gps_time");
						// //$this->db638->limit(6000);
						// // $this->db638->from($table);
						// $q       = $this->db638->get("gps");
						// $rows1638 = $q->result();

						// $sdate.'-'.$edate

						// echo "<pre>";
						// var_dump($rows638);die();
						// echo "<pre>";

						//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
						$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
	                								   gps_longitude,gps_latitude,gps_ew,gps_ns");
						$this->dbhist2->where("gps_name", $vehicle_device[0]);
						$this->dbhist2->where("gps_host", $vehicle_device[1]);
						// $this->dbhist2->where("gps_speed >", 0);
						// $this->dbhist22->where("gps_status", "A");
						$this->dbhist2->where("gps_time >=", $sdate);
						$this->dbhist2->where("gps_time <=", $edate);
						$this->dbhist2->order_by("gps_time", "asc");
						$this->dbhist2->group_by("gps_time");
						$q2    = $this->dbhist2->get($tablehist);
						$rows2 = $q2->result();

						// KONDISI KHUSUS RBT BEP 638
						// $this->db6382  = $this->load->database("gpshistory", TRUE);
						// $this->db6382->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
	          //       								   gps_longitude,gps_latitude,gps_ew,gps_ns");
						// $this->db6382->where("gps_name", $vehicle_device[0]);
						// $this->db6382->where("gps_host", $vehicle_device[1]);
						// // $this->db6382->where("gps_speed >", 0);
						// // $this->db63822->where("gps_status", "A");
						// $this->db6382->where("gps_time >=", $sdate);
						// $this->db6382->where("gps_time <=", $edate);
						// $this->db6382->order_by("gps_time", "asc");
						// $this->db6382->group_by("gps_time");
						// $q2       = $this->db6382->get($tablehist);
						// $rows2638 = $q2->result();

						// $vehicle_device[0].'-'.$tablehist

						$rows  = array_merge($rows1, $rows2);
						// $rows  = array_merge($rows1, $rows2, $rows1638, $rows2638);
						$trows = count($rows);

						// echo "<pre>";
						// var_dump($rows2638);die();
						// echo "<pre>";

						$totaldata = $trows;
						$data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
					}
					else
					{
						//$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
						$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
	                								   gps_longitude,gps_latitude,gps_ew,gps_ns");
						$this->dbhist->where("gps_name", $vehicle_device[0]);
						$this->dbhist->where("gps_host", $vehicle_device[1]);
						// $this->dbhist->where("gps_speed >", 0);
						// $this->dbhist2->where("gps_status", "A");
						$this->dbhist->where("gps_time >=", $sdate);
						$this->dbhist->where("gps_time <=", $edate);
						$this->dbhist->order_by("gps_time", "asc");
						$this->dbhist->group_by("gps_time");
						//$this->dbhist->limit(6000);
						// $this->dbhist->from($table);
						$q     = $this->dbhist->get($table);
						$rows1 = $q->result();

						// $sdate.'-'.$edate

						// echo "<pre>";
						// var_dump($rows1);die();
						// echo "<pre>";

						//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
						$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
	                								   gps_longitude,gps_latitude,gps_ew,gps_ns");
						$this->dbhist2->where("gps_name", $vehicle_device[0]);
						$this->dbhist2->where("gps_host", $vehicle_device[1]);
						// $this->dbhist2->where("gps_speed >", 0);
						// $this->dbhist22->where("gps_status", "A");
						$this->dbhist2->where("gps_time >=", $sdate);
						$this->dbhist2->where("gps_time <=", $edate);
						$this->dbhist2->order_by("gps_time", "asc");
						$this->dbhist2->group_by("gps_time");
						$q2    = $this->dbhist2->get($tablehist);
						$rows2 = $q2->result();

						// $vehicle_device[0].'-'.$tablehist

						$rows  = array_merge($rows1, $rows2);
						$trows = count($rows);

						// echo "<pre>";
						// var_dump($tablehist);die();
						// echo "<pre>";

						$totaldata = $trows;
						$data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
					}

				}
				else
				{
					// $edate1 = date("Y-m-d 23:59:59", strtotime($sdate));
					// $sdate1 = date("Y-m-d 00:00:00", strtotime($edate));

					$sdate1 = date("Y-m-d H:i:s", strtotime($sdate));
					$edate1 = date("Y-m-d H:i:s", strtotime($edate));
					// $callback['data']            = "TESTING";
					// $callback['error']          = false;
					// echo json_encode($callback);
					// return false;
					//$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");

					$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
                    							   gps_longitude,gps_latitude,gps_ew,gps_ns");
					$this->dbhist->where("gps_name", $vehicle_device[0]);
					// $this->dbhist->where("gps_speed >", 0);
					//$this->dbhist2->where("gps_status", "A");
					$this->dbhist->where("gps_time >=", $sdate);
					$this->dbhist->where("gps_time <=", $edate1);
					$this->dbhist->where("gps_longitude_real <>", "11.0000");
					$this->dbhist->order_by("gps_time", "asc");
					$this->dbhist->group_by("gps_time");
					//$this->dbhist->limit(6000);
					$this->dbhist->from($table);
					$q = $this->dbhist->get();
					$rows1 = $q->result();

					$this->dbhist->distinct();

					$this->dbhist->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
                                        gps_longitude,gps_latitude,gps_ew,gps_ns");
					$this->dbhist->where("gps_name", $vehicle_device[0]);
					// $this->dbhist->where("gps_speed >", 0);
					//$this->dbhist2->where("gps_status", "A");
					$this->dbhist->where("gps_time >=", $sdate1);
					$this->dbhist->where("gps_time <=", $edate);
					$this->dbhist->where("gps_longitude_real <>", "11.0000");
					$this->dbhist->order_by("gps_time", "asc");
					$this->dbhist->group_by("gps_time");
					//$this->dbhist->limit(6000);
					$this->dbhist->from($table);
					$q2 = $this->dbhist->get();
					$rows2 = $q2->result();


					//$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
					$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
                								   gps_longitude,gps_latitude,gps_ew,gps_ns");
					$this->dbhist2->where("gps_name", $vehicle_device[0]);
					// $this->dbhist2->where("gps_speed >", 0);
					//$this->dbhist2->where("gps_status", "A");
					$this->dbhist2->where("gps_time >=", $sdate);
					$this->dbhist2->where("gps_time <=", $edate1);
					$this->dbhist2->where("gps_longitude_real <>", "11.0000");
					$this->dbhist2->order_by("gps_time", "asc");
					$this->dbhist2->group_by("gps_time");
					//$this->dbhist2->limit(6000);
					$this->dbhist2->from($tablehist);
					$q3 = $this->dbhist2->get();
					$rows3 = $q3->result();

					$this->dbhist2->distinct();

					$this->dbhist2->select("gps_id,gps_name,gps_host,gps_speed,gps_status,gps_latitude_real,gps_longitude_real,gps_time,
                								   gps_longitude,gps_latitude,gps_ew,gps_ns");
					$this->dbhist2->where("gps_name", $vehicle_device[0]);
					// $this->dbhist2->where("gps_speed >", 0);
					//$this->dbhist2->where("gps_status", "A");
					$this->dbhist2->where("gps_time >=", $sdate1);
					$this->dbhist2->where("gps_time <=", $edate);
					$this->dbhist2->where("gps_longitude_real <>", "11.0000");
					$this->dbhist2->order_by("gps_time", "asc");
					$this->dbhist2->group_by("gps_time");
					//$this->dbhist2->limit(6000);
					$this->dbhist2->from($tablehist);
					$q4 = $this->dbhist2->get();
					$rows4 = $q4->result();



					$rows = array_merge($rows1, $rows2, $rows3, $rows4); //limit data rows = 3000
					$trows = count($rows);

					$totaldata = $trows;
					$data = $this->dashboardmodel->array_sort($rows, 'gps_time', SORT_ASC);
				}




				//print_r($totaldata);exit();
				/*if(($trows > 6000) && ($mapview == 1)){

								$error = "- Cannot View, Silahkan kurangi tanggal Report yang dipilih! \n";
								$callback['error'] = true;
								$callback['message'] = $error;

								echo json_encode($callback);
								return;
							}*/

				/*for($i=count($rows)-1; $i >= 0; $i--)
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
							if(isset($rowsummary)){
								$data = $rowsummary;
								$data = $this->dashboardmodel->array_sort($data, 'gps_time', SORT_ASC);
								$totaldata = count($data);
							}else{

								$data = "";
								$totaldata = 0;
							}*/

				/*
							$data = json_encode($rowsummary);
							$data2 = json_decode($data);*/

				//$data = json_encode($data);
				//print_r($data);exit();
			}
		}


		// if ($error != "") {
		//     $callback['error'] = true;
		//     $callback['message'] = $error;

		//     echo json_encode($callback);
		//     return;
		// }

		// echo "<pre>";
		// var_dump($vehicle_name);die();
		// echo "<pre>";


		// $callback['startdate']            = $startdate;
		// $callback['enddate']            = $enddate;
		// $callback['sdate']            = $sdate;
		// $callback['sdate2']            = date("Y-m-d 23:59:59", strtotime($sdate));
		// $callback['edate']            = $edate;
		// $callback['edate2']            = date("Y-m-d 00:00:00", strtotime($edate));
		// $callback['db']            = $database;
		// $callback['table']            = $table;
		// $callback['table_info']            = $tableinfo;
		// $callback['table_hist']            = $tablehist;
		// $callback['vehicle']            = $rowvehicle->vehicle_device;
		// $callback['vehicle_info']            = $rowvehicle->vehicle_info;
		// $callback['gps_name']            = $vehicle_device[0];
		// $callback['table_view']            = $tableview;
		// $callback['map_view']            = $mapview;
		// $callback['row_1']            = $rows1;
		// $callback['row_2']            = $rows2;
		// $callback['row_3']            = $rows3;
		// $callback['row_4']            = $rows4;
		// $callback['rows']            = $rows;
		// $callback['data']            = $data;
		// $callback['error']          = false;
		// echo json_encode($callback);
		// return false;

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";
		$datafixhistory = array();
		if (sizeof($data) > 0) {
			$before = 0;

			for ($i=0; $i < sizeof($data); $i++) {
				// if ($before == 0) {
				// 	if ($data[$i]->gps_latitude_real.','.$data[$i]->gps_longitude_real == $data[$i]->gps_latitude_real.','.$data[$i]->gps_longitude_real) {
						array_push($datafixhistory, array(
		 				 "gps_id"             => $data[$i]->gps_id,
		 				 "gps_name"           => $data[$i]->gps_name,
		 				 "gps_host"           => $data[$i]->gps_host,
		 				 "gps_speed"          => $data[$i]->gps_speed,
		 				 "gps_status"         => $data[$i]->gps_status,
		 				 "gps_latitude_real"  => $data[$i]->gps_latitude_real,
		 				 "gps_longitude_real" => $data[$i]->gps_longitude_real,
		 				 "gps_time"           => $data[$i]->gps_time,
		 				 "gps_longitude"      => $data[$i]->gps_longitude,
		 				 "gps_latitude"       => $data[$i]->gps_latitude,
		 				 "gps_ew"             => $data[$i]->gps_ew,
		 				 "gps_ns"             => $data[$i]->gps_ns
		 			 ));
					 // $before += 1;
					// }
				// }else {
					// if ($data[$i]->gps_latitude_real.','.$data[$i]->gps_longitude_real == $data[$before-1]->gps_latitude_real.','.$data[$before-1]->gps_longitude_real) {
					//
					// }else {
						// array_push($datafixhistory, array(
		 				//  "gps_id"             => $data[$i]->gps_id,
		 				//  "gps_name"           => $data[$i]->gps_name,
		 				//  "gps_host"           => $data[$i]->gps_host,
		 				//  "gps_speed"          => $data[$i]->gps_speed,
		 				//  "gps_status"         => $data[$i]->gps_status,
		 				//  "gps_latitude_real"  => $data[$i]->gps_latitude_real,
		 				//  "gps_longitude_real" => $data[$i]->gps_longitude_real,
		 				//  "gps_time"           => $data[$i]->gps_time,
		 				//  "gps_longitude"      => $data[$i]->gps_longitude,
		 				//  "gps_latitude"       => $data[$i]->gps_latitude,
		 				//  "gps_ew"             => $data[$i]->gps_ew,
		 				//  "gps_ns"             => $data[$i]->gps_ns
		 			 // ));
					//  $before += 1;
					// }
				// }
			}

			// echo "<pre>";
			// var_dump($datafixhistory);die();
			// echo "<pre>";

			$params['mapview']          = $mapview;
			$params['tableview']        = $tableview;
			$params['vehicle_no']       = $vehicle_no;
			$params['vehicle_name']     = $vehicle_name;
			$params['vehicle_odometer'] = $vehicle_odometer;
			$params['vehicle_type']     = $vehicle_type;
			$params['vehicle_user_id']  = $vehicle_user_id;
			$params['totalgps']         = $trows;
			$params['datacoordinate']   = json_encode($datafixhistory);
			$params['totaldata']        = $totaldata;
			$params['sdate']            = $sdate;
			$params['edate']            = $edate;
			$html                       = $this->load->view("newdashboard/report/vhistory_report_result_withplayback", $params, true);

			$callback['sdate']          = $sdate;
			$callback['edate']          = $edate;
			$callback['table']          = $table;
			$callback['tablehist']      = $tablehist;
			$callback['error']          = false;
			$callback['html']           = $html;
			echo json_encode($callback);
		}else {
			$callback['error']   = true;
			$callback['message'] = "Data is empty";
			echo json_encode($callback);
		}
	}

}
