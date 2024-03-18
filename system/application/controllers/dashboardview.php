<?php
include "base.php";
setlocale(LC_ALL, 'IND');

class Dashboardview extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Dashboardview()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("dashboardmodel");
    $this->load->model("m_dashboardview");
		$this->load->model("m_poipoolmaster");
		$this->load->model("log_model");
		$this->load->model("m_securityevidence");
	}

  function index(){
    redirect(base_url());
  }

	function abdiwatch_dashboard(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;
		$user_excavator  = $this->sess->user_excavator;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 10) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_dashboardview->getmastervehicleforheatmap();

		$datafix                         = array();
		$deviceidygtidakada              = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				// code...
			$auto_status   = $jsonautocheck->auto_status;

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}
			}else {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}

				if ($auto_status != "M") {
					array_push($datafix, array(
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
					));
				}
			}
		}

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

					$datavehicleandcompany    = array();
					$datavehicleandcompanyfix = array();

						for ($d=0; $d < sizeof($company); $d++) {
							$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
							// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
							$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
							$totalengine       = explode("|", $totaldata);
								array_push($datavehicleandcompany, array(
									"company_id"   => $company[$d]->company_id,
									"company_name" => $company[$d]->company_name,
									"totalmobil"   => $totalengine[2],
									"vehicle"      => $vehicledata[$d]
								));
						}
				$this->params['company']   = $company;
				$this->params['companyid'] = $companyid;
				$this->params['vehicle']   = $datavehicleandcompany;
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "abdiwatch";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->m_dashboardview->masterabdiwatch();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
		$totalmobilnya                = sizeof($getvehicle_byowner);
		if ($totalmobilnya == 0) {
			$this->params['name']         = "0";
			$this->params['host']         = "0";
		}else {
			$arr          = explode("@", $getvehicle_byowner[0]['vehicle_device']);
			$this->params['name']         = $arr[0];
			$this->params['host']         = $arr[1];
		}

		// $this->params['resultactive']    = $this->dashboardmodel->vehicleactive();
		// $this->params['resultexpired']   = $this->dashboardmodel->vehicleexpired();
		// $this->params['resulttotaldev']  = $this->dashboardmodel->totaldevice();
		$this->params['masterabdiwatch'] = $getvehicle_byowner;
		// $this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		// $this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['masterabdiwatch']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/abdiwatch/v_dashboard_view', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/abdiwatch/v_dashboard_view', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/abdiwatch/v_dashboard_view', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/abdiwatch/v_dashboard_view', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5 && $user_excavator == 0) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/abdiwatch/v_dashboard_view', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 5 && $user_excavator == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/abdiwatch/v_dashboard_view', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
			}elseif ($privilegecode == 6 && $user_excavator == 0) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/abdiwatch/v_dashboard_view', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6 && $user_excavator == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/abdiwatch/v_dashboard_view', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
			}elseif ($privilegecode == 10) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/abdiwatch/v_dashboard_view', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/abdiwatch/v_dashboard_view', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}

	function lastdata(){
		ini_set('display_errors', 1);
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$error = "";
		$vehicle          = $_POST['device'];

		$nowdate          = date("Y-m-d");
		$nowday           = date("d");
		$nowmonth         = date("m");
		$nowyear          = date("Y");
		$lastday          = date("t");

		if ($vehicle == "") {
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		}

		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$user_id         = $this->sess->user_id;
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$privilegecode   = $this->sess->user_id_role;
		$user_dblive 	   = $this->sess->user_dblive;
		$user_id_fix     = $user_id;

		$rows1           = array();
		$rows2           = array();
		$rows3           = array();
		// $type_data       = array("HRATE", "HRBP", "STEP", "TEMP");
		$type_data       = array("HRBP", "STEP", "TEMP");
		$datafix 				 = array();

		$this->db  = $this->load->database("webtracking_gps_abdiwatch_live", true);
		$title_history = "update";
		$curr_date     = date("d");
			for ($i=0; $i < sizeof($type_data); $i++) {
				if($vehicle != "0"){
					$device_ex 		 = explode("@", $vehicle);
					$this->dbtrip->where("gps_name", $device_ex[0]);
				}
				// $this->dbtrip->where("gps_ht_time >=", $stime);
				$this->dbtrip->where("gps_ht_code", $type_data[$i]);
				$this->dbtrip->order_by("gps_ht_time", "DESC");
				$this->dbtrip->limit(1);
				$q1   = $this->dbtrip->get("gps_health");
				$rows = $q1->result_array();

				if (sizeof($rows) > 0) {
					// if ($type_data[$i] == "HRATE") {
					$date_lastdata = date("d", strtotime($rows[0]['gps_ht_time']));
					if ($date_lastdata != $curr_date) {
						$title_history = "hist";
					}

					echo "<pre>";
					var_dump($date_lastdata.'-'.$curr_date.'-'.$title_history);die();
					echo "<pre>";

						array_push($datafix, array(
							"gps_name"        => $rows[0]['gps_name'],
							"gps_host"        => $rows[0]['gps_host'],
							"gps_type"        => $rows[0]['gps_type'],
							"gps_ht_code"     => $rows[0]['gps_ht_code'],
							"gps_ht_time"     => $rows[0]['gps_ht_time'],
							"gps_hour"        => date("H", strtotime($rows[0]['gps_ht_time'])),
							"gps_minute"      => date("i", strtotime($rows[0]['gps_ht_time'])),
							"gps_hr_rate"     => $rows[0]['gps_hr_rate'],
							"gps_bp_sys"      => $rows[0]['gps_bp_sys'],
							"gps_bp_dia"      => $rows[0]['gps_bp_dia'],
							"gps_temp"        => $rows[0]['gps_temp'],
							"gps_step"        => $rows[0]['gps_step'],
							"gps_oxy"         => $rows[0]['gps_oxy'],
							"gps_sleep"       => $rows[0]['gps_sleep'],
							"lastdata_status" => $title_history,
							"lastrefresh"      => date("Y-m-d H:i:s", strtotime("+1 hour")),
						));
					// }
				}
			}

			// echo "<pre>";
			// var_dump($datafix);die();
			// echo "<pre>";

		$this->dbtrip->close();


		if (count($rows) == 0) {
			$error .= "- No Data ! \n";
		}

		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";

		$callback['data']           = $datafix;
		// $html = $this->load->view("newdashboard/swreport/vreport_result", $params, true);
		$callback['error'] = false;
		echo json_encode($callback);
	}

  function viewrom(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;
		$user_excavator  = $this->sess->user_excavator;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 10) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_dashboardview->getmastervehicleforheatmap();

		$datafix                         = array();
		$deviceidygtidakada              = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				// code...
			$auto_status   = $jsonautocheck->auto_status;

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}
			}else {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}

				if ($auto_status != "M") {
					array_push($datafix, array(
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
					));
				}
			}
		}

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

					$datavehicleandcompany    = array();
					$datavehicleandcompanyfix = array();

						for ($d=0; $d < sizeof($company); $d++) {
							$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
							// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
							$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
							$totalengine       = explode("|", $totaldata);
								array_push($datavehicleandcompany, array(
									"company_id"   => $company[$d]->company_id,
									"company_name" => $company[$d]->company_name,
									"totalmobil"   => $totalengine[2],
									"vehicle"      => $vehicledata[$d]
								));
						}
				$this->params['company']   = $company;
				$this->params['companyid'] = $companyid;
				$this->params['vehicle']   = $datavehicleandcompany;
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitorexca";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
		$totalmobilnya                = sizeof($getvehicle_byowner);
		if ($totalmobilnya == 0) {
			$this->params['name']         = "0";
			$this->params['host']         = "0";
		}else {
			$arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
			$this->params['name']         = $arr[0];
			$this->params['host']         = $arr[1];
		}

		$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
		$this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
		$this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['mapsetting']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5 && $user_excavator == 0) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 5 && $user_excavator == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
			}elseif ($privilegecode == 6 && $user_excavator == 0) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6 && $user_excavator == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
			}elseif ($privilegecode == 10) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}

  function getdatacontractor(){
  	$user_id      = $this->sess->user_id;
  	$user_company = $this->sess->user_company;
  	$user_parent  = $this->sess->user_parent;
  	$user_id_role = $this->sess->user_id_role;

  		if ($user_id_role == 0) {
  			$this->db->where("company_created_by", $user_id);
  		}elseif ($user_id_role == 1) {
  			$this->db->where("company_created_by", $user_parent);
  		}elseif ($user_id_role == 2) {
  			$this->db->where("company_created_by", $user_parent);
  		}elseif ($user_id_role == 3) {
  			$this->db->where("company_created_by", $user_parent);
  		}elseif ($user_id_role == 4) {
  			$this->db->where("company_created_by", $user_parent);
  		}elseif ($user_id_role == 5) {
  			$this->db->where("company_id", $user_company);
  		}elseif ($user_id_role == 6) {
  			$this->db->where("company_id", $user_company);
  		}elseif ($user_id_role == 10) {
  			$this->db->where("company_id", $user_company);
  		}

  	$this->db->where("company_flag", 0);
		$this->db->where_in("company_exca", array(1, 2));
  	$this->db->order_by("company_name", "ASC");
  	$q     = $this->db->get("company");
  	$rows  = $q->result_array();

  	// echo "<pre>";
  	// var_dump($rows);die();
  	// echo "<pre>";

  	echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
  }

  function getstreetautomaticbycompanyid(){
  	$typeofstreet 		 = $_POST['typeofstreet'];
  	$companyid 		 	   = $_POST['companyid'];
  	$masterdatavehicle = $this->m_dashboardview->getmastervehiclebycontractor($companyid);
  	$allStreet         = $this->m_dashboardview->getstreet_now($typeofstreet);
  	$dataRomFix        = array();
  	$lasttimecheck 		 = date("d-m-Y H:i:s", strtotime("+1 hour"));

  	for ($j=0; $j < sizeof($allStreet); $j++) {
  		$street_name                = explode(",", $allStreet[$j]['street_name']);
  		$street_namefix             = $street_name[0];
  		$dataState[$street_namefix] = 0;

  		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
  			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
  			$auto_last_position = explode(",", $autocheck->auto_last_position);
  			$datalastposition   = $auto_last_position[0];
  			$auto_status 		    = $autocheck->auto_status;

  			if ($auto_status != "M") {
  				if ($datalastposition == $street_namefix) {
  						$dataState[$street_namefix] += 1;
  				}
  			}
  		}
  	}

  	// LIMIT SETTING PER KM
  	$getdataFromStreet = $this->m_dashboardview->getstreet_now($typeofstreet);
  	$mapSettingType    = $this->m_dashboardview->getMapSettingByType($typeofstreet);

  	$postfix_middle_limit = "_middle_limit";
  	$postfix_top_limit    = "_top_limit";

  	if (isset($getdataFromStreet)) {
  		$datafixlimitperkm = array();
  		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
  			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
  			$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

  			$middlelimitname          = $streetfix.$postfix_middle_limit;
  			$toplimitname             = $streetfix.$postfix_top_limit;

  			$getMapSettingByLimitName = $this->m_dashboardview->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

  			if (sizeof($getMapSettingByLimitName) > 1) {
  					array_push($datafixlimitperkm, array(
  						"street_id"               => $getdataFromStreet[$i]['street_id'],
  						"street_name"             => $getdataFromStreet[$i]['street_name'],
  						"mapsetting_type"         => 1,
  						"mapsetting_name_alias"   => $streetremovecoma[0],
  						"mapsetting_name"         => $streetfix,
  						"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
  						"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
  					));
  			}else {
  				array_push($datafixlimitperkm, array(
  					"street_id"               => $getdataFromStreet[$i]['street_id'],
  					"street_name"             => $getdataFromStreet[$i]['street_name'],
  					"mapsetting_type"         => 1,
  					"mapsetting_name_alias"   => $streetremovecoma[0],
  					"mapsetting_name"         => $streetfix,
  					"mapsetting_middle_limit" => 0,
  					"mapsetting_top_limit"    => 0
  				));
  			}
  		}
  	}

  	// echo "<pre>";
  	// var_dump($dataState);die();
  	// echo "<pre>";

  	echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataState, "allstreet" => $allStreet, "lastcheck" => $lasttimecheck, "datafixlimit" => $datafixlimitperkm));
  }

  function getlistinrom(){
  	$allCompany 					  = $this->m_dashboardview->getAllCompany();
  	$dataContractor['BKAE'] = 0;
  	$dataContractor['KMB']  = 0;
  	$dataContractor['GECL'] = 0;
  	$dataContractor['STLI'] = 0;
  	$dataContractor['RAMB'] = 0;
  	$dataContractor['BBS']  = 0;
  	$dataContractor['MKS']  = 0;
  	$dataContractor['RBT']  = 0;
  	$dataContractor['MMS']  = 0;
  	$dataContractor['EST']  = 0;

  	$dataVehicleOnRom    = array();
  	$idrom 							 = $_POST['idrom'];
  	$contractor 				 = $_POST['contractor'];
  	$masterdatavehicle   = $this->m_dashboardview->getmastervehiclebycontractor($contractor);

  	$vCompany 					   = $this->dashboardmodel->getstreet_id("", $idrom);
  	$streetFix 					 = explode(",", $vCompany->street_name);

  	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
  		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
  		$auto_last_position = explode(",", $autocheck->auto_last_position);
  		$datalastposition   = $auto_last_position[0];

			$vehicledevice      = explode("@", $masterdatavehicle[$i]['vehicle_device']);
			$lastinfofix        = $this->gpsmodel->GetLastInfo($vehicledevice[0], $vehicledevice[1], true, false, 0, "");

  		// echo "<pre>";
  		// var_dump($lastinfofix);die();
  		// echo "<pre>";

  			if ($datalastposition == $streetFix[0]) {
  				array_push($dataVehicleOnRom, array(
  					"vehicle_no"       => $masterdatavehicle[$i]['vehicle_no'],
  					"vehicle_name"     => $masterdatavehicle[$i]['vehicle_name'],
  					"vehicle_company"  => $masterdatavehicle[$i]['vehicle_company'],
  					"auto_last_lat"    => $autocheck->auto_last_lat,
  					"auto_last_long"   => $autocheck->auto_last_long,
  					"auto_last_engine" => $autocheck->auto_last_engine,
  					"auto_last_speed"  => $autocheck->auto_last_speed,
						"gps_pto"          => $lastinfofix->gps_cs,
  					"auto_last_update" => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
  				));
  			}
  	}

  	for ($k=0; $k < sizeof($dataVehicleOnRom); $k++) {
  		for ($j=0; $j < sizeof($allCompany); $j++) {
  			if ($dataVehicleOnRom[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
  				if ($allCompany[$j]['company_name'] == "BKAE") {
  					$dataContractor['BKAE'] += 1;
  				}elseif ($allCompany[$j]['company_name'] == "KMB") {
  					$dataContractor['KMB'] += 1;
  				}elseif ($allCompany[$j]['company_name'] == "GECL") {
  					$dataContractor['GECL'] += 1;
  				}elseif ($allCompany[$j]['company_name'] == "STLI") {
  					$dataContractor['STLI'] += 1;
  				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
  					$dataContractor['RAMB'] += 1;
  				}elseif ($allCompany[$j]['company_name'] == "BBS") {
  					$dataContractor['BBS'] += 1;
  				}elseif ($allCompany[$j]['company_name'] == "MKS") {
  					$dataContractor['MKS'] += 1;
  				}elseif ($allCompany[$j]['company_name'] == "RBT") {
  					$dataContractor['RBT'] += 1;
  				}elseif ($allCompany[$j]['company_name'] == "MMS") {
  					$dataContractor['MMS'] += 1;
  				}elseif ($allCompany[$j]['company_name'] == "EST") {
  					$dataContractor['EST'] += 1;
  				}
  			}
  		}
  	}

  	// echo "<pre>";
  	// var_dump($dataContractor);die();
  	// echo "<pre>";
  	echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $dataVehicleOnRom, "romsent" => $streetFix[0]));
  }

  function viewmapsstandard(){
  		ini_set('max_execution_time', '300');
  		set_time_limit(300);
  		if (! isset($this->sess->user_type))
  		{
  			redirect(base_url());
  		}

  		$user_id       = $this->sess->user_id;
  		$user_parent   = $this->sess->user_parent;
  		$privilegecode = $this->sess->user_id_role;
  		$user_company  = $this->sess->user_company;
			$user_excavator  = $this->sess->user_excavator;

  		if($privilegecode == 0){
  			$user_id_fix = $user_id;
  		}elseif ($privilegecode == 1) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 2) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 3) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 4) {
  			$user_id_fix = $user_parent;
  		}elseif ($privilegecode == 5) {
  			$user_id_fix = $user_id;
  		}elseif ($privilegecode == 6) {
  			$user_id_fix = $user_id;
  		}else{
  			$user_id_fix = $user_id;
  		}

  		$companyid                       = $this->sess->user_company;
  		$user_dblive                     = $this->sess->user_dblive;
  		$mastervehicle                   = $this->m_dashboardview->getmastervehicleformapsstandard();

  		$datafix                         = array();
			$dataexca                        = array();
  		$deviceidygtidakada              = array();
  		$statusvehicle['engine_on']  = 0;
  		$statusvehicle['engine_off'] = 0;

  		for ($i=0; $i < sizeof($mastervehicle); $i++) {
  			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
  			if (isset($jsonautocheck->auto_status)) {
  				// code...
  			$auto_status   = $jsonautocheck->auto_status;

  			if ($privilegecode == 5 || $privilegecode == 6) {
  				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
  					if ($jsonautocheck->auto_last_engine == "ON") {
  						$statusvehicle['engine_on'] += 1;
  					}else {
  						$statusvehicle['engine_off'] += 1;
  					}
  				}
  			}else {
  				if ($jsonautocheck->auto_last_engine == "ON") {
  					$statusvehicle['engine_on'] += 1;
  				}else {
  					$statusvehicle['engine_off'] += 1;
  				}
  			}

  				if ($auto_status != "M" && $mastervehicle[$i]['vehicle_typeunit'] == 1) {
  					array_push($datafix, array(
  						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
  						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
  						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
  						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
  						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
  						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
  						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
  						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
  						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
  					));
  				}
  			}
  		}

  		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
  			if ($company) {

  					$datavehicleandcompany    = array();
  					$datavehicleandcompanyfix = array();

  						for ($d=0; $d < sizeof($company); $d++) {
  							$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
  							// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
  							$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
  							$totalengine       = explode("|", $totaldata);
  								array_push($datavehicleandcompany, array(
  									"company_id"   => $company[$d]->company_id,
  									"company_name" => $company[$d]->company_name,
  									"totalmobil"   => $totalengine[2],
  									"vehicle"      => $vehicledata[$d]
  								));
  						}
  				$this->params['company']   = $company;
  				$this->params['companyid'] = $companyid;
  				$this->params['vehicle']   = $datavehicleandcompany;
  			}else {
  				$this->params['company']   = 0;
  				$this->params['companyid'] = 0;
  				$this->params['vehicle']   = 0;
  			}

  		// echo "<pre>";
  		// var_dump($company);die();
  		// echo "<pre>";


  		$this->params['url_code_view']  = "1";
  		$this->params['code_view_menu'] = "monitorexca";
  		$this->params['maps_code']      = "morehundred";

  		$this->params['engine_on']      = $statusvehicle['engine_on'];
  		$this->params['engine_off']     = $statusvehicle['engine_off'];


  		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

  		$datastatus                     = explode("|", $rstatus);
  		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
  		$this->params['total_vehicle']  = $datastatus[3];
  		$this->params['total_offline']  = $datastatus[2];

  		$this->params['vehicledata']  = $datafix;
  		$this->params['vehicletotal'] = sizeof($mastervehicle);
  		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
  		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
  		// echo "<pre>";
  		// var_dump($getvehicle_byowner);die();
  		// echo "<pre>";
  		$totalmobilnya                = sizeof($getvehicle_byowner);
  		if ($totalmobilnya == 0) {
  			$this->params['name']         = "0";
  			$this->params['host']         = "0";
  		}else {
  			$arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
  			$this->params['name']         = $arr[0];
  			$this->params['host']         = $arr[1];
  		}

  		$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
  		$this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
  		$this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
  		$this->params['mapsetting']     = $this->m_dashboardview->getmapsetting();
  		$this->params['poolmaster']     = $this->m_dashboardview->getalldata("webtracking_poi_poolmaster");

  		// echo "<pre>";
  		// var_dump($this->params['mapsetting']);die();
  		// echo "<pre>";

  		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  			if ($privilegecode == 1) {
  				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_mapsexca', $this->params, true);
  				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  			}elseif ($privilegecode == 2) {
  				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_mapsexca', $this->params, true);
  				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  			}elseif ($privilegecode == 3) {
  				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_mapsexca', $this->params, true);
  				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  			}elseif ($privilegecode == 4) {
  				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_mapsexca', $this->params, true);
  				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  			}elseif ($privilegecode == 5 && $user_excavator == 0) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_mapsexca', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
				}elseif ($privilegecode == 5 && $user_excavator == 1) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_mapsexca', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
				}elseif ($privilegecode == 6 && $user_excavator == 0) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_mapsexca', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
				}elseif ($privilegecode == 6 && $user_excavator == 1) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_mapsexca', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
				}elseif ($privilegecode == 10) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_excavator', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_mapsexca', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_excavator", $this->params);
				}else {
  				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  				$this->params["content"]        = $this->load->view('newdashboard/dashboardview/v_dashboard_mapsexca', $this->params, true);
  				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  			}
  	}

    function getvehiclebycontractor(){
    	$user_id       = $this->sess->user_id;
    	$user_parent   = $this->sess->user_parent;
    	$privilegecode = $this->sess->user_id_role;
    	$user_company  = $this->sess->user_company;
    	$companyid     = $this->input->post('companyid');

    	$this->db->select("*");

    		if ($companyid == 0) {
    			if ($privilegecode == 0) {
    				$this->db->where("vehicle_user_id", $user_id);
    			}elseif ($privilegecode == 1) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 2) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 3) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 4) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 5) {
    				$this->db->where("vehicle_company", $user_company);
    			}elseif ($privilegecode == 6) {
    				$this->db->where("vehicle_company", $user_company);
    			}elseif ($privilegecode == 10) {
    				$this->db->where("vehicle_company", $user_company);
    			}
    		}else {
    			$this->db->where("vehicle_company", $companyid);
    		}

    	$this->db->where("vehicle_status <>", 3);
    	$this->db->where("vehicle_gotohistory", 0);
    	$this->db->where("vehicle_autocheck is not NULL");
    	$this->db->order_by("vehicle_no", "ASC");
    	$q    = $this->db->get("vehicle");
    	$rows = $q->result_array();

    	// echo "<pre>";
    	// var_dump($rows);die();
    	// echo "<pre>";

    	echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
    }

    function forsearchvehicle(){
      $user_dblive     = $this->sess->user_dblive;
      $key             = $_POST['key'];
      // $key             = "b 9442 wcb";
      // $keyfix          = str_replace(" ", "", $key);
      $keyfix          = $key;

      $mastervehicle   = $this->m_poipoolmaster->searchmasterdata("webtracking_vehicle", $keyfix);

      if (sizeof($mastervehicle) < 1) {
        echo json_encode(array("code" => "400"));
      }else {
        // echo "<pre>";
        // var_dump($mastervehicle);die();
        // echo "<pre>";

        $device          = explode("@", $mastervehicle[0]['vehicle_device']);
        $device0         = $device[0];
        $device1         = $device[1];
        $getdatalastinfo = $this->m_poipoolmaster->searchdblivedata("webtracking_gps", $user_dblive, $device0);
        $lastinfofix     = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");

        // echo "<pre>";
        // var_dump($lastinfofix);die();
        // echo "<pre>";

        $vehiclemv03 = $mastervehicle[0]['vehicle_mv03'];
        // if ($vehiclemv03 != "0000") {
        // 	$url       = "http://103.253.107.212:8080/808gps/open/player/video.html?lang=en&devIdno=".$vehiclemv03."&jsession=";
        // 	$username  = "temanindobara";
        // 	$password  = "123456";
        // 	// $url       = "http://103.253.107.212:8080/808gps/open/player/RealPlayVideo.html?account=".$username."&password=".$password."&PlateNum=".$devicefix."&lang=en";
        //
        // 	$getthissession  = $this->m_securityevidence->getsession();
        // 	$urlfix          = $url.$getthissession[0]['sess_value'];
        //
        // 	// GET LOGIN DENGAN SESSION LAMA
        // 	$loginlama       = file_get_contents("http://103.253.107.212:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
        // 		if ($loginlama) {
        // 			$loginlamadecode = json_decode($loginlama);
        // 			if (!$loginlamadecode) {
        // 				if ($loginlamadecode->message == "Session does not exist!") {
        // 					$loginbaru       = file_get_contents("http://103.253.107.212:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
        // 					$loginbarudecode = json_decode($loginbaru);
        // 					$fixsession      = $loginbarudecode->jsession;
        // 				}
        // 			}else {
        // 				$fixsession      = $getthissession[0]['sess_value'];
        // 			}
        // 		}
        //
        // 		// echo "<pre>";
        // 		// var_dump($fixsession);die();
        // 		// echo "<pre>";
        //
        // 		// GET DEVICE STATUS START
        // 		$urlcekdevicestatus = "http://103.253.107.212:8080/StandardApiAction_getDeviceOlStatus.action?jsession=".$fixsession."&devIdno=".$vehiclemv03;
        // 		$cekstatus          = file_get_contents($urlcekdevicestatus);
        // 		$loginbarudecode    = json_decode($cekstatus);
        // 		$statusfixnya       = $loginbarudecode->onlines[0]->online;
        // 		$devicestatusfixnya = $statusfixnya;
        // 		// echo "<pre>";
        // 		// var_dump($row->devicestatus);die();
        // 		// echo "<pre>";
        // }else {
          $devicestatusfixnya = "";
        // }



        // DRIVER DETAIL START
        $drivername     = $this->getdriver($mastervehicle[0]['vehicle_id']);

        if ($drivername) {
          $driverexplode  = explode("-", $drivername);
          $iddriver       = $driverexplode[0];
          $drivername     = $driverexplode[1];
          $getdriverimage = $this->getdriverdetail($iddriver);

          if (isset($getdriverimage[0]->driver_image_file_name)) {
            $driverimage = $getdriverimage[0]->driver_image_raw_name.$getdriverimage[0]->driver_image_file_ext;
          }else {
            $driverimage = 0;
          }
        }else {
          $drivername  = "";
          $driverimage = 0;
        }


        // echo "<pre>";
        // var_dump($drivername);die();
        // echo "<pre>";
        // DRIVER DETAIL END

        $datafix = array();
        if (sizeof($getdatalastinfo) > 0) {
          $jsonnya[0] = json_decode($getdatalastinfo[0]['vehicle_autocheck']);
            if (isset($jsonnya[0]->auto_last_snap)) {
              $snap     = $jsonnya[0]->auto_last_snap;
              $snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
            }else {
              $snap     = "";
              $snaptime = "";
            }

            if (isset($jsonnya[0]->auto_last_road)) {
              $autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
            }else {
              $autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
            }

            if (isset($jsonnya[0]->auto_last_ritase)) {
              $autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
            }else {
              $autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
            }

            array_push($datafix, array(
               "drivername"            	=> $drivername,
               "driverimage"            => $driverimage,
               "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
               "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
               "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
               "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
               "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
               "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
               "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
               "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
               "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
               "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
               "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
               "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
               "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
               "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
               "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
               "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
               "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
               "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
               "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
               "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
               "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
               "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
               "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
               "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
               "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
               "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
               // "vehicle_info"           => $result[$i]['vehicle_info'],
               "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
               "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
               "vehicle_port_time"      => date("d-m-Y H:i:s", strtotime($mastervehicle[0]['vehicle_port_time'])),
               "vehicle_port_name"      => $mastervehicle[0]['vehicle_port_name'],
               "vehicle_rom_time"       => date("d-m-Y H:i:s", strtotime($mastervehicle[0]['vehicle_rom_time'])),
               "vehicle_rom_name"       => $mastervehicle[0]['vehicle_rom_name'],
               "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
               "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
               "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
               "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
               "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
               "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
               "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
               "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
               "devicestatusfixnya" 	  => $devicestatusfixnya,
               "auto_last_road" 				=> $autolastroad,
               "autolastritase" 				=> $autolastritase,
               "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
               "auto_last_mvd"          => round($lastinfofix->gps_mvd),
               "auto_last_update"       => $lastinfofix->gps_date_fmt. " ". $lastinfofix->gps_time_fmt,
               "auto_last_check"        => $jsonnya[0]->auto_last_check,
               "auto_last_snap"         => $snap,
               "auto_last_snap_time"    => $snaptime,
               "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $lastinfofix->georeverse->display_name),
               "auto_last_lat"          => substr($lastinfofix->gps_latitude_real_fmt, 0, 10),
               "auto_last_long"         => substr($lastinfofix->gps_longitude_real_fmt, 0, 10),
               "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
               "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
               "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
               "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
               "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag),
               "gps_pto"                => $lastinfofix->gps_cs
            ));
        }else {
          $jsonnya[0] = json_decode($mastervehicle[0]['vehicle_autocheck']);
            if (isset($jsonnya[0]->auto_last_snap)) {
              $snap     = $jsonnya[0]->auto_last_snap;
              $snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
            }else {
              $snap     = "";
              $snaptime = "";
            }

            if (isset($jsonnya[0]->auto_last_road)) {
              $autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
            }else {
              $autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
            }

            if (isset($jsonnya[0]->auto_last_ritase)) {
              $autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
            }else {
              $autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
            }

            array_push($datafix, array(
               "drivername"            	=> $drivername,
               "driverimage"            => $driverimage,
               "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
               "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
               "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
               "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
               "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
               "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
               "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
               "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
               "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
               "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
               "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
               "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
               "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
               "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
               "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
               "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
               "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
               "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
               "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
               "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
               "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
               "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
               "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
               "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
               "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
               "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
               // "vehicle_info"           => $result[$i]['vehicle_info'],
               "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
               "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
               "vehicle_port_time"      => date("d-m-Y H:i:s", strtotime($mastervehicle[0]['vehicle_port_time'])),
               "vehicle_port_name"      => $mastervehicle[0]['vehicle_port_name'],
               "vehicle_rom_time"       => date("d-m-Y H:i:s", strtotime($mastervehicle[0]['vehicle_rom_time'])),
               "vehicle_rom_name"       => $mastervehicle[0]['vehicle_rom_name'],
               "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
               "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
               "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
               "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
               "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
               "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
               "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
               "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
               "devicestatusfixnya" 	  => $devicestatusfixnya,
               "auto_last_road" 					=> $autolastroad,
               "autolastritase" 				=> $autolastritase,
               "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
               "auto_last_mvd"          => round($lastinfofix->gps_mvd),
               "auto_last_update"       => $jsonnya[0]->auto_last_update,
               "auto_last_check"        => $jsonnya[0]->auto_last_check,
               "auto_last_snap"         => $snap,
               "auto_last_snap_time"    => $snaptime,
               "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_position),
               "auto_last_lat"          => substr($jsonnya[0]->auto_last_lat, 0, 10),
               "auto_last_long"         => substr($jsonnya[0]->auto_last_long, 0, 10),
               "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
               "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
               "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
               "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
               "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag),
               "gps_pto"                => $lastinfofix->gps_cs
            ));
        }

        // echo "<pre>";
        // var_dump($datafix);die();
        // echo "<pre>";
        echo json_encode($datafix);
      }
    }

    function mapsstandard(){ // maps with overlay seperti di history map
    	if (! isset($this->sess->user_type))
    	{
    		redirect(base_url());
    	}

    	$user_id       = $this->sess->user_id;
    	$user_parent   = $this->sess->user_parent;
    	$privilegecode = $this->sess->user_id_role;

    	if($privilegecode == 0){
    		$user_id_fix = $user_id;
    	}elseif ($privilegecode == 1) {
    		$user_id_fix = $user_parent;
    	}elseif ($privilegecode == 2) {
    		$user_id_fix = $user_parent;
    	}elseif ($privilegecode == 3) {
    		$user_id_fix = $user_parent;
    	}elseif ($privilegecode == 4) {
    		$user_id_fix = $user_parent;
    	}elseif ($privilegecode == 5) {
    		$user_id_fix = $user_id;
    	}elseif ($privilegecode == 6) {
    		$user_id_fix = $user_id;
    	}elseif ($privilegecode == 10) {
    		$user_id_fix = $user_id;
    	}else{
    		$user_id_fix = $user_id;
    	}

    	$companyid                       = $this->sess->user_company;
    	$user_dblive                     = $this->sess->user_dblive;
    	$companyid 											 = $_POST['companyid'];
    	$forclearmaps                    = $this->m_dashboardview->getmastervehicleformapsstandard();
    	$mastervehicle                   = $this->m_dashboardview->getmastervehiclebycontractor($companyid);

			// echo "<pre>";
			// var_dump($mastervehicle);die();
			// echo "<pre>";

    	$datafix            = array();
			$dataexca           = array();
    	$deviceidygtidakada = array();

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

    	for ($i=0; $i < sizeof($mastervehicle); $i++) {
    		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
    		$auto_status   = $jsonautocheck->auto_status;
        $arr           = explode("@", $mastervehicle[$i]['vehicle_device']);
  			$devices[0]    = (count($arr) > 0) ? $arr[0] : "";
  			$devices[1]    = (count($arr) > 1) ? $arr[1] : "";


				$typeunitfix = "";
				$typeunit    = $mastervehicle[$i]['vehicle_typeunit'];

					if ($typeunit == 0) {
						$typeunitfix   = "DT";
						$gps_pto       = "";
						$position_name = explode(",", $jsonautocheck->auto_last_position);

							if (in_array($position_name[0], $data_inrom)) {
								if ($auto_status != "M") {
									array_push($datafix, array(
										"vehicle_typeunitname" => $typeunitfix,
										"vehicle_typeunit"     => $mastervehicle[$i]['vehicle_typeunit'],
										"vehicle_id"           => $mastervehicle[$i]['vehicle_id'],
										"vehicle_user_id"      => $mastervehicle[$i]['vehicle_user_id'],
										"vehicle_device"       => $mastervehicle[$i]['vehicle_device'],
										"vehicle_no"           => $mastervehicle[$i]['vehicle_no'],
										"vehicle_name"         => $mastervehicle[$i]['vehicle_name'],
										"vehicle_active_date2" => $mastervehicle[$i]['vehicle_active_date2'],
										"auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
										"auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
										"auto_last_road"       => $jsonautocheck->auto_last_road,
										"auto_last_engine"     => $jsonautocheck->auto_last_engine,
										"auto_last_speed"      => $jsonautocheck->auto_last_speed,
										"auto_last_course"     => $jsonautocheck->auto_last_course,
										"gps_pto"              => $gps_pto,
									));
								}
							}

					}else {
						$typeunitfix   = "EXCA";
						$lastinfofix 	 = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, 0, "");
		  			$gps_pto       = $lastinfofix->gps_cs;

						if ($auto_status != "M") {
							array_push($datafix, array(
								"vehicle_typeunitname" => $typeunitfix,
								"vehicle_typeunit"     => $mastervehicle[$i]['vehicle_typeunit'],
								"vehicle_id"           => $mastervehicle[$i]['vehicle_id'],
								"vehicle_user_id"      => $mastervehicle[$i]['vehicle_user_id'],
								"vehicle_device"       => $mastervehicle[$i]['vehicle_device'],
								"vehicle_no"           => $mastervehicle[$i]['vehicle_no'],
								"vehicle_name"         => $mastervehicle[$i]['vehicle_name'],
								"vehicle_active_date2" => $mastervehicle[$i]['vehicle_active_date2'],
								"auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
								"auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
								"auto_last_road"       => $jsonautocheck->auto_last_road,
								"auto_last_engine"     => $jsonautocheck->auto_last_engine,
								"auto_last_speed"      => $jsonautocheck->auto_last_speed,
								"auto_last_course"     => $jsonautocheck->auto_last_course,
								"gps_pto"              => $gps_pto,
							));
						}
					}
    	}

    	// echo "<pre>";
    	// var_dump($datafix);die();
    	// echo "<pre>";

    	echo json_encode(array("code" => "success", "msg" => "success", "data" => $datafix, "alldataforclearmaps" => $forclearmaps));
    }

    function getdetailbydevid(){
  		if (! isset($this->sess->user_type))
  		{
  			redirect(base_url());
  		}

  		$user_dblive     = $this->sess->user_dblive;
  		$device_id       = $_POST['device_id'];
  		$device          = explode("@", $_POST['device_id']);
  		$device0         = $device[0];
  		$device1         = $device[1];

  		$mastervehicle   = $this->m_poipoolmaster->getmastervehiclebydevid($device_id);
  		$getdatalastinfo = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
  		$lastinfofix 	   = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");

      // echo "<pre>";
      // var_dump($lastinfofix);die();
      // echo "<pre>";

  		$datafix = array();
  		$deviceidfrommastervehicle = explode("@", $mastervehicle[0]['vehicle_device']);

  		$vehiclemv03 = $mastervehicle[0]['vehicle_mv03'];
  		if ($vehiclemv03 != "0000" || $vehiclemv03 != "69969039633231@TK510") {
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
  					$devicestatusfixnya = "";

  				}else {
  					$statusfixnya       = $loginbarudecode->onlines[0]->online;
  					$devicestatusfixnya = $statusfixnya;
  				}
  				// echo "<pre>";
  				// var_dump($loginbarudecode);die();
  				// echo "<pre>";
  		}else {
  			$devicestatusfixnya = "";
  		}

  		// DRIVER DETAIL START
  		$drivername     = $this->getdriver($mastervehicle[0]['vehicle_id']);
  		if ($drivername) {
  			$driverexplode  = explode("-", $drivername);
  			$iddriver       = $driverexplode[0];
  			$drivername     = $driverexplode[1];
  			$getdriverimage = $this->getdriverdetail($iddriver);

  			if (isset($getdriverimage[0]->driver_image_file_name)) {
  				$driverimage = $getdriverimage[0]->driver_image_raw_name.$getdriverimage[0]->driver_image_file_ext;
  			}else {
  				$driverimage = 0;
  			}
  		}else {
  			$driverimage = 0;
  		}

  		// echo "<pre>";
  		// var_dump($drivername.'-'.$driverimage);die();
  		// echo "<pre>";
  		// DRIVER DETAIL END

  		if (sizeof($getdatalastinfo) > 0) {
  			$jsonnya[0] = json_decode($getdatalastinfo[0]['vehicle_autocheck']);
  				if (isset($jsonnya[0]->auto_last_snap)) {
  					$snap     = $jsonnya[0]->auto_last_snap;
  					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
  				}else {
  					$snap     = "";
  					$snaptime = "";
  				}

  				if (isset($jsonnya[0]->auto_last_road)) {
  					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
  				}else {
  					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
  				}

  				if (isset($jsonnya[0]->auto_last_ritase)) {
  					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
  				}else {
  					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
  				}

  				if (isset($jsonnya[0]->auto_last_mvd)) {
  					$autolastfuel = $jsonnya[0]->auto_last_mvd;
  				}else {
  					$autolastfuel = "";
  				}

  				array_push($datafix, array(
  					 "drivername"            	=> $drivername,
  					 "driverimage"            => $driverimage,
						 "vehicle_typeunit"       => $mastervehicle[0]['vehicle_typeunit'],
  					 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
  					 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
  					 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
  					 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
  					 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
  					 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
  					 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
  					 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
  					 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
  					 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
  					 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
  					 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
  					 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
  					 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
  					 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
  					 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
  					 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
  					 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
  					 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
  					 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
  					 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
  					 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
  					 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
  					 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
  					 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
  					 "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
  					 // "vehicle_info"           => $result[$i]['vehicle_info'],
  					 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
  					 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
  					 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
  					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
  					 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
  					 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
  					 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
  					 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
  					 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
  					 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
  					 "devicestatusfixnya" 		=> $devicestatusfixnya,
  					 "auto_last_fuel"         => $autolastfuel,
  					 "auto_last_road"         => $autolastroad,
  					 "auto_last_ritase"       => $autolastritase,
  					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
  					 "auto_last_update"       => $lastinfofix->gps_date_fmt. " ". $lastinfofix->gps_time_fmt,
  					 "auto_last_check"        => $jsonnya[0]->auto_last_check,
  					 "auto_last_snap"         => $snap,
  					 "auto_last_snap_time"    => $snaptime,
  					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $lastinfofix->georeverse->display_name),
  					 "auto_last_lat"          => substr($lastinfofix->gps_latitude_real_fmt, 0, 10),
  					 "auto_last_long"         => substr($lastinfofix->gps_longitude_real_fmt, 0, 10),
  					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
  					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
  					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
  					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
  					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag),
             "gps_pto"                => $lastinfofix->gps_cs
  				));
  		}else {
  			$jsonnya[0] = json_decode($mastervehicle[0]['vehicle_autocheck']);
  				if (isset($jsonnya[0]->auto_last_snap)) {
  					$snap     = $jsonnya[0]->auto_last_snap;
  					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
  				}else {
  					$snap     = "";
  					$snaptime = "";
  				}

  				if (isset($jsonnya[0]->auto_last_road)) {
  					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
  				}else {
  					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
  				}

  				if (isset($jsonnya[0]->auto_last_ritase)) {
  					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
  				}else {
  					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
  				}

  				if (isset($jsonnya[0]->auto_last_mvd)) {
  					$autolastfuel = $jsonnya[0]->auto_last_mvd;
  				}else {
  					$autolastfuel = "";
  				}

  				array_push($datafix, array(
  					 "drivername"             => $drivername,
  				 	 "driverimage"            => $driverimage,
						 "vehicle_typeunit"       => $mastervehicle[0]['vehicle_typeunit'],
  					 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
  					 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
  					 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
  					 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
  					 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
  					 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
  					 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
  					 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
  					 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
  					 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
  					 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
  					 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
  					 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
  					 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
  					 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
  					 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
  					 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
  					 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
  					 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
  					 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
  					 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
  					 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
  					 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
  					 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
  					 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
  					 "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
  					 // "vehicle_info"           => $result[$i]['vehicle_info'],
  					 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
  					 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
  					 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
  					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
  					 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
  					 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
  					 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
  					 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
  					 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
  					 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
  					 "devicestatusfixnya" 		=> $devicestatusfixnya,
  					 "auto_last_road"         => $autolastroad,
  					 "auto_last_ritase"       => $autolastritase,
  					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
  					 "auto_last_update"       => $jsonnya[0]->auto_last_update,
  					 "auto_last_check"        => $jsonnya[0]->auto_last_check,
  					 "auto_last_fuel"         => $autolastfuel,
  					 "auto_last_snap"         => $snap,
  					 "auto_last_snap_time"    => $snaptime,
  					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_position),
  					 "auto_last_lat"          => substr($jsonnya[0]->auto_last_lat, 0, 10),
  					 "auto_last_long"         => substr($jsonnya[0]->auto_last_long, 0, 10),
  					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
  					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
  					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
  					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
  					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag),
             "gps_pto"                => $lastinfofix->gps_cs
  				));
  		}

  		// echo "<pre>";
  		// var_dump($jsonnya);die();
  		// echo "<pre>";
  		echo json_encode($datafix);
  	}

    function vehicleByContractor(){
    	$user_id         = $this->sess->user_id;
    	$user_parent     = $this->sess->user_parent;
    	$privilegecode   = $this->sess->user_id_role;
    	$user_company    = $this->sess->user_company;
    	$companyid       = $this->input->post('companyid');
    	$valueMapsOption = $this->input->post('valuemapsoption');

    	$this->db->select("*");
    		if ($companyid == 0) {
    			if ($privilegecode == 0) {
    				$this->db->where("vehicle_user_id", $user_id);
    			}elseif ($privilegecode == 1) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 2) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 3) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 4) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 5) {
    				$this->db->where("vehicle_company", $user_company);
    			}elseif ($privilegecode == 6) {
    				$this->db->where("vehicle_company", $user_company);
    			}elseif ($privilegecode == 10) {
    				$this->db->where("vehicle_company", $user_company);
    			}
    		}else {
    			$this->db->where("vehicle_company", $companyid);
    		}

    	$this->db->where("vehicle_status <>", 3);
    	$this->db->where("vehicle_gotohistory", 0);
    	$this->db->where("vehicle_autocheck is not NULL");
    	$this->db->order_by("vehicle_no", "ASC");
    	$q    = $this->db->get("vehicle");
    	$rows = $q->result_array();

    	if ($valueMapsOption == 1) {
    		$poolmaster        = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
    		$datavehicle       = array();

    			for ($i=0; $i < sizeof($rows); $i++) {
    				$autocheck         = json_decode($rows[$i]['vehicle_autocheck']);
    						array_push($datavehicle, array(
    							"vehicle_id"     => $rows[$i]['vehicle_id'],
    							"vehicle_no"     => $rows[$i]['vehicle_no'],
    							"vehicle_name"   => $rows[$i]['vehicle_name'],
    							"vehicle_device" => $rows[$i]['vehicle_device'],
    							"auto_last_lat"  => $autocheck->auto_last_lat,
    							"auto_last_long" => $autocheck->auto_last_long
    						));
    			}
    			echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows, "datavehicle" => $datavehicle, "poolmaster" => $poolmaster));
    	}else {
    		echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
    	}

    	// echo "<pre>";
    	// var_dump($datavehicle);die();
    	// echo "<pre>";

    }

    function vehicleByContractorheatmap(){
    	$user_id         = $this->sess->user_id;
    	$user_parent     = $this->sess->user_parent;
    	$privilegecode   = $this->sess->user_id_role;
    	$user_company    = $this->sess->user_company;
    	$companyid       = $this->input->post('companyid');
    	$valueMapsOption = $this->input->post('valuemapsoption');

    	$this->db->select("*");
    		if ($companyid == 0) {
    			if ($privilegecode == 0) {
    				$this->db->where("vehicle_user_id", $user_id);
    			}elseif ($privilegecode == 1) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 2) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 3) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 4) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 5) {
    				$this->db->where("vehicle_user_id", 4408);
    			}elseif ($privilegecode == 6) {
    				$this->db->where("vehicle_user_id", 4408);
    			}
    		}else {
    			$this->db->where("vehicle_company", $companyid);
    		}

    	$this->db->where("vehicle_status <>", 3);
    	$this->db->where("vehicle_gotohistory", 0);
    	$this->db->where("vehicle_autocheck is not NULL");
    	$this->db->order_by("vehicle_no", "ASC");
    	$q    = $this->db->get("vehicle");
    	$rows = $q->result_array();

    	if ($valueMapsOption == 1) {
    		$poolmaster        = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
    		$datavehicle       = array();

    			for ($i=0; $i < sizeof($rows); $i++) {
    				$autocheck         = json_decode($rows[$i]['vehicle_autocheck']);
    						array_push($datavehicle, array(
    							"vehicle_id"      => $rows[$i]['vehicle_id'],
    							"vehicle_no"      => $rows[$i]['vehicle_no'],
    							"vehicle_name"    => $rows[$i]['vehicle_name'],
    							"vehicle_device"  => $rows[$i]['vehicle_device'],
    							"vehicle_company" => $rows[$i]['vehicle_company'],
    							"auto_last_lat"   => $autocheck->auto_last_lat,
    							"auto_last_long"  => $autocheck->auto_last_long
    						));
    			}
    			echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows, "datavehicle" => $datavehicle, "poolmaster" => $poolmaster));
    	}else {
    		echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
    	}

    	// echo "<pre>";
    	// var_dump($datavehicle);die();
    	// echo "<pre>";

    }

    function km_quickcount_new(){
    	$companyid 				 = $_POST['companyid'];
    	$masterdatavehicle = $this->m_poipoolmaster->getmastervehiclebycontractor($companyid);
    	$dataKmMuatanFix   = array();
    	$dataKmKosonganFix = array();

    	// echo "<pre>";
    	// var_dump($masterdatavehicle);die();
    	// echo "<pre>";

    	$dataJumlahInKmKosongan_2['gb0_port_bib_kosongan_1']    = 0;
    	$dataJumlahInKmKosongan_2['gb1_port_bib_kosongan_2']    = 0;
    	$dataJumlahInKmKosongan_2['gb2_port_bir_kosongan_1']    = 0;
    	$dataJumlahInKmKosongan_2['gb3_port_bir_kosongan_2']    = 0;
    	$dataJumlahInKmKosongan_2['gb4_simpang_bayah_kosongan'] = 0;

    	$dataJumlahInKmMuatan_2['gb5_port_bib_antrian']         = 0;
    	$dataJumlahInKmMuatan_2['gb6_port_bir_antrian_wb']      = 0;

    	$dataJumlahInKmMuatan['KM_0']  = 0;
    	$dataJumlahInKmMuatan['KM_1']  = 0;
    	$dataJumlahInKmMuatan['KM_2']  = 0;
    	$dataJumlahInKmMuatan['KM_3']  = 0;
    	$dataJumlahInKmMuatan['KM_4']  = 0;
    	$dataJumlahInKmMuatan['KM_5']  = 0;
    	$dataJumlahInKmMuatan['KM_6']  = 0;
    	$dataJumlahInKmMuatan['KM_7']  = 0;
    	$dataJumlahInKmMuatan['KM_8']  = 0;
    	$dataJumlahInKmMuatan['KM_9']  = 0;
    	$dataJumlahInKmMuatan['KM_10'] = 0;
    	$dataJumlahInKmMuatan['KM_11'] = 0;
    	$dataJumlahInKmMuatan['KM_12'] = 0;
    	$dataJumlahInKmMuatan['KM_13'] = 0;
    	$dataJumlahInKmMuatan['KM_14'] = 0;
    	$dataJumlahInKmMuatan['KM_15'] = 0;
    	$dataJumlahInKmMuatan['KM_16'] = 0;
    	$dataJumlahInKmMuatan['KM_17'] = 0;
    	$dataJumlahInKmMuatan['KM_18'] = 0;
    	$dataJumlahInKmMuatan['KM_19'] = 0;
    	$dataJumlahInKmMuatan['KM_20'] = 0;
    	$dataJumlahInKmMuatan['KM_21'] = 0;
    	$dataJumlahInKmMuatan['KM_22'] = 0;
    	$dataJumlahInKmMuatan['KM_23'] = 0;
    	$dataJumlahInKmMuatan['KM_24'] = 0;
    	$dataJumlahInKmMuatan['KM_25'] = 0;
    	$dataJumlahInKmMuatan['KM_26'] = 0;
    	$dataJumlahInKmMuatan['KM_27'] = 0;
    	$dataJumlahInKmMuatan['KM_28'] = 0;
    	$dataJumlahInKmMuatan['KM_29'] = 0;
    	$dataJumlahInKmMuatan['KM_30'] = 0;

    	$dataJumlahInKmKosongan['KM_0']  = 0;
    	$dataJumlahInKmKosongan['KM_1']  = 0;
    	$dataJumlahInKmKosongan['KM_2']  = 0;
    	$dataJumlahInKmKosongan['KM_3']  = 0;
    	$dataJumlahInKmKosongan['KM_4']  = 0;
    	$dataJumlahInKmKosongan['KM_5']  = 0;
    	$dataJumlahInKmKosongan['KM_6']  = 0;
    	$dataJumlahInKmKosongan['KM_7']  = 0;
    	$dataJumlahInKmKosongan['KM_8']  = 0;
    	$dataJumlahInKmKosongan['KM_9']  = 0;
    	$dataJumlahInKmKosongan['KM_10'] = 0;
    	$dataJumlahInKmKosongan['KM_11'] = 0;
    	$dataJumlahInKmKosongan['KM_12'] = 0;
    	$dataJumlahInKmKosongan['KM_13'] = 0;
    	$dataJumlahInKmKosongan['KM_14'] = 0;
    	$dataJumlahInKmKosongan['KM_15'] = 0;
    	$dataJumlahInKmKosongan['KM_16'] = 0;
    	$dataJumlahInKmKosongan['KM_17'] = 0;
    	$dataJumlahInKmKosongan['KM_18'] = 0;
    	$dataJumlahInKmKosongan['KM_19'] = 0;
    	$dataJumlahInKmKosongan['KM_20'] = 0;
    	$dataJumlahInKmKosongan['KM_21'] = 0;
    	$dataJumlahInKmKosongan['KM_22'] = 0;
    	$dataJumlahInKmKosongan['KM_23'] = 0;
    	$dataJumlahInKmKosongan['KM_24'] = 0;
    	$dataJumlahInKmKosongan['KM_25'] = 0;
    	$dataJumlahInKmKosongan['KM_26'] = 0;
    	$dataJumlahInKmKosongan['KM_27'] = 0;
    	$dataJumlahInKmKosongan['KM_28'] = 0;
    	$dataJumlahInKmKosongan['KM_29'] = 0;
    	$dataJumlahInKmKosongan['KM_30'] = 0;

    	$lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour"));

    	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
    		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
    		$auto_last_position = explode(",", $autocheck->auto_last_position);
    		$jalur_name         = $autocheck->auto_last_road;
    		$datalastposition   = $auto_last_position[0];

    			if ($jalur_name == "kosongan") {
    				if ($datalastposition == "Port BIB - Kosongan 1") {
    					$dataJumlahInKmKosongan_2['gb0_port_bib_kosongan_1'] += 1;
    				}elseif ($datalastposition == "Port BIB - Kosongan 2") {
    					$dataJumlahInKmKosongan_2['gb1_port_bib_kosongan_2'] += 1;
    				}elseif ($datalastposition == "Port BIR - Kosongan 1") {
    					$dataJumlahInKmKosongan_2['gb2_port_bir_kosongan_1'] += 1;
    				}elseif ($datalastposition == "Port BIR - Kosongan 2") {
    					$dataJumlahInKmKosongan_2['gb3_port_bir_kosongan_2'] += 1;
    				}elseif ($datalastposition == "Simpang Bayah - Kosongan") {
    					$dataJumlahInKmKosongan_2['gb4_simpang_bayah_kosongan'] += 1;
    				}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
    					$dataJumlahInKmKosongan['KM_2'] += 1;
    				}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
    					$dataJumlahInKmKosongan['KM_3'] += 1;
    				}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
    					$dataJumlahInKmKosongan['KM_4'] += 1;
    				}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
    					$dataJumlahInKmKosongan['KM_5'] += 1;
    				}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
    					$dataJumlahInKmKosongan['KM_6'] += 1;
    				}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
    					$dataJumlahInKmKosongan['KM_7'] += 1;
    				}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
    					$dataJumlahInKmKosongan['KM_8'] += 1;
    				}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
    					$dataJumlahInKmKosongan['KM_9'] += 1;
    				}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
    					$dataJumlahInKmKosongan['KM_10'] += 1;
    				}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
    					$dataJumlahInKmKosongan['KM_11'] += 1;
    				}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
    					$dataJumlahInKmKosongan['KM_12'] += 1;
    				}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
    					$dataJumlahInKmKosongan['KM_13'] += 1;
    				}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
    					$dataJumlahInKmKosongan['KM_14'] += 1;
    				}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
    					$dataJumlahInKmKosongan['KM_15'] += 1;
    				}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
    					$dataJumlahInKmKosongan['KM_16'] += 1;
    				}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
    					$dataJumlahInKmKosongan['KM_17'] += 1;
    				}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
    					$dataJumlahInKmKosongan['KM_18'] += 1;
    				}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
    					$dataJumlahInKmKosongan['KM_19'] += 1;
    				}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
    					$dataJumlahInKmKosongan['KM_20'] += 1;
    				}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
    					$dataJumlahInKmKosongan['KM_21'] += 1;
    				}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
    					$dataJumlahInKmKosongan['KM_22'] += 1;
    				}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
    					$dataJumlahInKmKosongan['KM_23'] += 1;
    				}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
    					$dataJumlahInKmKosongan['KM_24'] += 1;
    				}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
    					$dataJumlahInKmKosongan['KM_25'] += 1;
    				}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
    					$dataJumlahInKmKosongan['KM_26'] += 1;
    				}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
    					$dataJumlahInKmKosongan['KM_27'] += 1;
    				}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
    					$dataJumlahInKmKosongan['KM_28'] += 1;
    				}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
    					$dataJumlahInKmKosongan['KM_29'] += 1;
    				}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
    					$dataJumlahInKmKosongan['KM_30'] += 1;
    				}
    			}else {
    				if ($datalastposition == "Port BIB - Antrian") {
    					$dataJumlahInKmMuatan_2['gb5_port_bib_antrian'] += 1;
    				}elseif ($datalastposition == "Port BIR - Antrian WB") {
    					$dataJumlahInKmMuatan_2['gb6_port_bir_antrian_wb'] += 1;
    				}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
    					$dataJumlahInKmMuatan['KM_2'] += 1;
    				}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
    					$dataJumlahInKmMuatan['KM_3'] += 1;
    				}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
    					$dataJumlahInKmMuatan['KM_4'] += 1;
    				}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
    					$dataJumlahInKmMuatan['KM_5'] += 1;
    				}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
    					$dataJumlahInKmMuatan['KM_6'] += 1;
    				}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
    					$dataJumlahInKmMuatan['KM_7'] += 1;
    				}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
    					$dataJumlahInKmMuatan['KM_8'] += 1;
    				}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
    					$dataJumlahInKmMuatan['KM_9'] += 1;
    				}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
    					$dataJumlahInKmMuatan['KM_10'] += 1;
    				}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
    					$dataJumlahInKmMuatan['KM_11'] += 1;
    				}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
    					$dataJumlahInKmMuatan['KM_12'] += 1;
    				}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
    					$dataJumlahInKmMuatan['KM_13'] += 1;
    				}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
    					$dataJumlahInKmMuatan['KM_14'] += 1;
    				}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
    					$dataJumlahInKmMuatan['KM_15'] += 1;
    				}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
    					$dataJumlahInKmMuatan['KM_16'] += 1;
    				}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
    					$dataJumlahInKmMuatan['KM_17'] += 1;
    				}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
    					$dataJumlahInKmMuatan['KM_18'] += 1;
    				}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
    					$dataJumlahInKmMuatan['KM_19'] += 1;
    				}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
    					$dataJumlahInKmMuatan['KM_20'] += 1;
    				}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
    					$dataJumlahInKmMuatan['KM_21'] += 1;
    				}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
    					$dataJumlahInKmMuatan['KM_22'] += 1;
    				}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
    					$dataJumlahInKmMuatan['KM_23'] += 1;
    				}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
    					$dataJumlahInKmMuatan['KM_24'] += 1;
    				}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
    					$dataJumlahInKmMuatan['KM_25'] += 1;
    				}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
    					$dataJumlahInKmMuatan['KM_26'] += 1;
    				}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
    					$dataJumlahInKmMuatan['KM_27'] += 1;
    				}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
    					$dataJumlahInKmMuatan['KM_28'] += 1;
    				}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
    					$dataJumlahInKmMuatan['KM_29'] += 1;
    				}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
    					$dataJumlahInKmMuatan['KM_30'] += 1;
    				}
    			}
    	}

    	// LIMIT SETTING PER KM
    	$arraynotin              = array("Port BIR - Kosongan 2", "Port BIB - Kosongan 2", "Simpang Bayah - Kosongan", "Port BIR - Antrian WB", "Port BIB - Kosongan 1",
    																		"Port BIB - Antrian", "Port BIR - Kosongan 1", "KM 1");

    	$arraynotinAllKMKosongan = array("Port BIR - Kosongan 2", "Port BIB - Kosongan 2", "Simpang Bayah - Kosongan", "Port BIR - Antrian WB", "Port BIB - Kosongan 1",
    																		"Port BIB - Antrian", "Port BIR - Kosongan 1");
      $arrayinKM1Muatan	 			 = array("KM 1");

    	$getdataFromStreet = $this->m_poipoolmaster->getstreet_now(1);
    	$mapSettingType    = $this->m_poipoolmaster->getMapSettingByType(1);

    	$postfix_middle_limit_allkmmuatan   = "_middle_limit_allkmmuatan";
    	$postfix_top_limit_allkmmuatan      = "_top_limit_allkmmuatan";
    	$postfix_middle_limit_allkmkosongan = "_middle_limit_allkmkosongan";
    	$postfix_top_limit_allkmkosongan    = "_top_limit_allkmkosongan";
    	$postfix_bottom_limit_km1muatan     = "_bottom_limit_km1muatan";
    	$postfix_middle_limit_km1muatan     = "_middle_limit_km1muatan";
    	$postfix_top_limit_km1muatan        = "_top_limit_km1muatan";

    	// LIMIT ONLY KM 1
    	if (isset($getdataFromStreet)) {
    		$datafixlimitkm1muatan = array();
    		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
    			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
    				if (in_array($streetremovecoma[0], $arrayinKM1Muatan)) {
    					$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);
    					$bottomlimitname          = $streetfix.$postfix_bottom_limit_km1muatan;
    					$middlelimitname          = $streetfix.$postfix_middle_limit_km1muatan;
    					$toplimitname             = $streetfix.$postfix_top_limit_km1muatan;
    					$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName_mapsetting_onlykm1($bottomlimitname, $middlelimitname, $toplimitname);

    					// echo "<pre>";
    					// var_dump($getMapSettingByLimitName);die();
    					// echo "<pre>";

    					if (sizeof($getMapSettingByLimitName) > 1) {
    							array_push($datafixlimitkm1muatan, array(
    								"street_id"               => $getdataFromStreet[$i]['street_id'],
    								"street_name"             => $getdataFromStreet[$i]['street_name'],
    								"mapsetting_type"         => 1,
    								"mapsetting_name_alias"   => $streetremovecoma[0],
    								"mapsetting_name"         => $streetfix,
    								"mapsetting_bottom_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
    								"mapsetting_middle_limit" => $getMapSettingByLimitName[1]['mapsetting_limit_value'],
    								"mapsetting_top_limit"    => $getMapSettingByLimitName[2]['mapsetting_limit_value']
    							));
    					}else {
    						array_push($datafixlimitkm1muatan, array(
    							"street_id"               => $getdataFromStreet[$i]['street_id'],
    							"street_name"             => $getdataFromStreet[$i]['street_name'],
    							"mapsetting_type"         => 1,
    							"mapsetting_name_alias"   => $streetremovecoma[0],
    							"mapsetting_name"         => $streetfix,
    							"mapsetting_bottom_limit" => 0,
    							"mapsetting_middle_limit" => 0,
    							"mapsetting_top_limit"    => 0
    						));
    					}
    				}
    		}
    	}

    	// LIMIT FOR ALL KM MUATAN EXCEPT KM 1
    	if (isset($getdataFromStreet)) {
    		$datafixlimitperkmallmuatan = array();
    		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
    			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
    				if (!in_array($streetremovecoma[0], $arraynotin)) {
    					$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

    					$middlelimitname          = $streetfix.$postfix_middle_limit_allkmmuatan;
    					$toplimitname             = $streetfix.$postfix_top_limit_allkmmuatan;

    					$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

    					if (sizeof($getMapSettingByLimitName) > 1) {
    							array_push($datafixlimitperkmallmuatan, array(
    								"street_id"               => $getdataFromStreet[$i]['street_id'],
    								"street_name"             => $getdataFromStreet[$i]['street_name'],
    								"mapsetting_type"         => 1,
    								"mapsetting_name_alias"   => $streetremovecoma[0],
    								"mapsetting_name"         => $streetfix,
    								"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
    								"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
    							));
    					}else {
    						array_push($datafixlimitperkmallmuatan, array(
    							"street_id"               => $getdataFromStreet[$i]['street_id'],
    							"street_name"             => $getdataFromStreet[$i]['street_name'],
    							"mapsetting_type"         => 1,
    							"mapsetting_name_alias"   => $streetremovecoma[0],
    							"mapsetting_name"         => $streetfix,
    							"mapsetting_middle_limit" => 0,
    							"mapsetting_top_limit"    => 0
    						));
    					}
    				}
    		}
    	}

    	// LIMIT ALL KOSONGAN
    	if (isset($getdataFromStreet)) {
    		$datafixlimitperkmallkosongan = array();
    		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
    			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
    				if (!in_array($streetremovecoma[0], $arraynotinAllKMKosongan)) {
    					$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

    					$middlelimitname          = $streetfix.$postfix_middle_limit_allkmkosongan;
    					$toplimitname             = $streetfix.$postfix_top_limit_allkmkosongan;

    					$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

    					if (sizeof($getMapSettingByLimitName) > 1) {
    							array_push($datafixlimitperkmallkosongan, array(
    								"street_id"               => $getdataFromStreet[$i]['street_id'],
    								"street_name"             => $getdataFromStreet[$i]['street_name'],
    								"mapsetting_type"         => 1,
    								"mapsetting_name_alias"   => $streetremovecoma[0],
    								"mapsetting_name"         => $streetfix,
    								"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
    								"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
    							));
    					}else {
    						array_push($datafixlimitperkmallkosongan, array(
    							"street_id"               => $getdataFromStreet[$i]['street_id'],
    							"street_name"             => $getdataFromStreet[$i]['street_name'],
    							"mapsetting_type"         => 1,
    							"mapsetting_name_alias"   => $streetremovecoma[0],
    							"mapsetting_name"         => $streetfix,
    							"mapsetting_middle_limit" => 0,
    							"mapsetting_top_limit"    => 0
    						));
    					}
    				}
    		}
    	}

    	// GET LIST IN ROM & PORT
    	$romType 		         = 3; //ROM TYPE
    	$portType 		       = 4; //PORT TYPE
    	$portTypeCPBIB 		   = 7; //portType CP BIB
    	$portTypeANTBIR 		 = 8; //portType ANT BIR
    	$romStreet           = $this->m_poipoolmaster->getstreet_now2($romType);
    	$portStreet          = $this->m_poipoolmaster->getstreet_now2($portType);
    	$portCPBIB           = $this->m_poipoolmaster->getstreet_now2($portTypeCPBIB);
    	$portANTBIR          = $this->m_poipoolmaster->getstreet_now2($portTypeANTBIR);
    	$dataRomFix          = array();
    	$dataPortFix         = array();
    	$dataPortCPBIBFix    = array();
    	$dataPortANTBIRFix   = array();

    	// echo "<pre>";
    	// var_dump($romStreet);die();
    	// echo "<pre>";

    	for ($j=0; $j < sizeof($romStreet); $j++) {
    		$street_name_rom                  = explode(",", $romStreet[$j]['street_name']);
    		$street_nameromfix                = $street_name_rom[0];
    		$dataStateRom[$street_nameromfix] = 0;

    		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
    			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
    			$auto_last_position = explode(",", $autocheck->auto_last_position);
    			$datalastposition   = $auto_last_position[0];
    			$auto_status 		    = $autocheck->auto_status;

    			if ($auto_status != "M") {
    				if ($datalastposition == $street_nameromfix) {
    						$dataStateRom[$street_nameromfix] += 1;
    				}
    			}
    		}
    	}

    	for ($k=0; $k < sizeof($portStreet); $k++) {
    		$street_name_port                   = explode(",", $portStreet[$k]['street_name']);
    		$street_nameportfix                 = $street_name_port[0];
    		$dataStatePort[$street_nameportfix] = 0;

    		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
    			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
    			$auto_last_position = explode(",", $autocheck->auto_last_position);
    			$datalastposition   = $auto_last_position[0];
    			$auto_status 		    = $autocheck->auto_status;

    			if ($auto_status != "M") {
    				if ($datalastposition == $street_nameportfix) {
    						$dataStatePort[$street_nameportfix] += 1;
    				}
    			}
    		}
    	}

    	for ($l=0; $l < sizeof($portCPBIB); $l++) {
    		$street_name_portCPBIB                   = explode(",", $portCPBIB[$l]['street_name']);
    		$street_nameportCPBIBfix                 = $street_name_portCPBIB[0];
    		$dataStatePortCPBIB[$street_nameportCPBIBfix] = 0;

    		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
    			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
    			$auto_last_position = explode(",", $autocheck->auto_last_position);
    			$datalastposition   = $auto_last_position[0];
    			$auto_status 		    = $autocheck->auto_status;

    			if ($auto_status != "M") {
    				if ($datalastposition == $street_nameportCPBIBfix) {
    						$dataStatePortCPBIB[$street_nameportCPBIBfix] += 1;
    				}
    			}
    		}
    	}

    	for ($m=0; $m < sizeof($portANTBIR); $m++) {
    		$street_name_portANTBIR                   = explode(",", $portANTBIR[$m]['street_name']);
    		$street_nameportANTBIRfix                 = $street_name_portANTBIR[0];
    		$dataStatePortANTBIR[$street_nameportANTBIRfix] = 0;

    		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
    			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
    			$auto_last_position = explode(",", $autocheck->auto_last_position);
    			$datalastposition   = $auto_last_position[0];
    			$auto_status 		    = $autocheck->auto_status;

    			if ($auto_status != "M") {
    				if ($datalastposition == $street_nameportANTBIRfix) {
    						$dataStatePortANTBIR[$street_nameportANTBIRfix] += 1;
    				}
    			}
    		}
    	}

    	// echo "<pre>";
    	// var_dump($dataJumlahInKmMuatan);die(); //dataJumlahInKmKosongan dataJumlahInKmMuatan
    	// echo "<pre>";
    	// LIMIT SETTING PER KM

    	// echo "<pre>";
    	// var_dump($dataStatePort);die();
    	// echo "<pre>";

    	echo json_encode(array("msg" => "success", "code" => 200, "dataPortCPBIB" => $dataStatePortCPBIB, "dataPortANTBIR" => $dataStatePortANTBIR, "dataRominQuickCount" => $dataStateRom, "dataPortinQuickCount" => $dataStatePort, "dataMuatan" => $dataJumlahInKmMuatan, "dataKosongan" => $dataJumlahInKmKosongan, "dataMuatan2" => $dataJumlahInKmMuatan_2, "dataKosongan2" => $dataJumlahInKmKosongan_2, "lastcheck" => $lasttimecheck, "datafixlimitperkmallmuatan" => $datafixlimitperkmallmuatan, "datafixlimitperkmallkosongan" => $datafixlimitperkmallkosongan, "datafixlimitkm1muatan" => $datafixlimitkm1muatan));
    }

    function getdriver($driver_vehicle) {
    	$this->dbtransporter = $this->load->database('transporter',true);
    	$this->dbtransporter->select("*");
    	$this->dbtransporter->from("driver");
    	$this->dbtransporter->order_by("driver_update_date","desc");
    	$this->dbtransporter->where("driver_vehicle", $driver_vehicle);
    	$this->dbtransporter->limit(1);
    	$q = $this->dbtransporter->get();

    	if ($q->num_rows > 0 ){
    		$row = $q->row();
    		$data = $row->driver_id;
    		$data .= "-";
    		$data .= $row->driver_name;
    		return $data;
    		$this->dbtransporter->close();
    	}
    	else {
    	$this->dbtransporter->close();
    	return false;
    	}
    }

    function getdriverdetail($iddriver){
    	$this->dbtransporter = $this->load->database('transporter',true);
    	$this->dbtransporter->select("*");
    	$this->dbtransporter->from("driver_image");
    	$this->dbtransporter->where("driver_image_driver_id", $iddriver);
    	$q   = $this->dbtransporter->get();
    	return $q->result();
    }


}