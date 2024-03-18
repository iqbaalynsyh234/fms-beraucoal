<?php
include "base.php";

class Vehicles extends Base
{

	function Vehicles()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
		$this->load->model("driver_model");
		$this->load->model("m_maintenance");
		$this->load->model("vehiclemodel");
		$this->load->model("log_model");
		$this->load->model("m_wimreport");

		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
	}

	function index()
	{
		ini_set('display_errors', 1);

		//print_r("DISINI");exit();
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$privilegecode   = $this->sess->user_id_role;

		if ($this->sess->user_id == "1445") {
			$user_id = $this->sess->user_id; //tag
		} else {
			$user_id = $this->sess->user_id;
		}

		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no", "asc");

		if ($privilegecode == 0) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		} else if ($privilegecode == 1) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 3) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 4) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else {
			$this->db->where("vehicle_no", 99999);
		}

		// $this->db->where("vehicle_company", 1926);

		//$this->db->where("vehicle_status <>", 3);
		// $this->db->where("vehicle_status <>", 3);
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();

		// echo "<pre>";
		// var_dump($result);die();
		// echo "<pre>";

		// GET ASSIGNED VEHICLE STATUS
		$this->params["datavehicle"] 				= $result;

		// GET BRANCH
		$this->db->where("company_created_by", 4408);
		$qcompany                = $this->db->get("company");
		$rescompany              = $qcompany->result_array();
		$this->params["company"] = $rescompany;

		// GET SUBBRANCH
		$this->db->where("subcompany_creator", $user_id_fix);
		$qsubcompany                = $this->db->get("subcompany");
		$ressubcompany              = $qsubcompany->result_array();
		$this->params["subcompany"] = $ressubcompany;

		// GET GROUP
		$this->db->where("group_creator", $user_id_fix);
		$qgroup                = $this->db->get("group");
		$resqgroup             = $qgroup->result_array();
		$this->params["group"] = $resqgroup;

		// GET GROUP
		$this->db->where("subgroup_creator", $user_id_fix);
		$qsubgroup                = $this->db->get("subgroup");
		$ressubgroup              = $qsubgroup->result_array();
		$this->params["subgroup"] = $ressubgroup;

		// echo "<pre>";
		// var_dump($this->params["datavehicle"]);die();
		// echo "<pre>";

		$getservicetype                  = $this->m_maintenance->gogetservicetype("service_type");
		$resultservicetype               = $getservicetype->result_array();
		$getworkshop                     = $this->m_maintenance->g_all("workshop", "workshop_company", $user_company, "workshop_name", "asc");

		$this->params['workshop']        = $getworkshop;
		$this->params['dataservicetype'] = $resultservicetype;
		$this->params['code_view_menu']  = "masterdata";
		$this->params['privilegecode']   = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/vehicles/v_vehicles', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/vehicles/v_vehicles', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/vehicles/v_vehicles', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/vehicles/v_vehicles', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function status()
	{
		ini_set('display_errors', 1);

		//print_r("DISINI");exit();
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$privilegecode   = $this->sess->user_id_role;

		if ($this->sess->user_id == "1445") {
			$user_id = $this->sess->user_id; //tag
		} else {
			$user_id = $this->sess->user_id;
		}

		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no", "asc");

		if ($privilegecode == 0) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		} else if ($privilegecode == 1) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 3) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 4) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else {
			$this->db->where("vehicle_no", 99999);
		}

		// $this->db->where("vehicle_company", 1926);

		//$this->db->where("vehicle_status <>", 3);
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();

		// echo "<pre>";
		// var_dump($result);die();
		// echo "<pre>";

		// GET ASSIGNED VEHICLE STATUS
		$this->params["datavehicle"] 				= $result;

		// GET BRANCH
		$this->db->where("company_created_by", 4408);
		$qcompany                = $this->db->get("company");
		$rescompany              = $qcompany->result_array();
		$this->params["company"] = $rescompany;

		// GET SUBBRANCH
		$this->db->where("subcompany_creator", $user_id_fix);
		$qsubcompany                = $this->db->get("subcompany");
		$ressubcompany              = $qsubcompany->result_array();
		$this->params["subcompany"] = $ressubcompany;

		// GET GROUP
		$this->db->where("group_creator", $user_id_fix);
		$qgroup                = $this->db->get("group");
		$resqgroup             = $qgroup->result_array();
		$this->params["group"] = $resqgroup;

		// GET GROUP
		$this->db->where("subgroup_creator", $user_id_fix);
		$qsubgroup                = $this->db->get("subgroup");
		$ressubgroup              = $qsubgroup->result_array();
		$this->params["subgroup"] = $ressubgroup;

		// echo "<pre>";
		// var_dump($this->params["datavehicle"]);die();
		// echo "<pre>";

		$getservicetype                  = $this->m_maintenance->gogetservicetype("service_type");
		$resultservicetype               = $getservicetype->result_array();
		$getworkshop                     = $this->m_maintenance->g_all("workshop", "workshop_company", $user_company, "workshop_name", "asc");

		$this->params['workshop']        = $getworkshop;
		$this->params['dataservicetype'] = $resultservicetype;
		$this->params['code_view_menu']  = "masterdata";
		$this->params['privilegecode']   = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/vehicles/v_vehicles_status', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/vehicles/v_vehicles_status', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/vehicles/v_vehicles_status', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/vehicles/v_vehicles_status', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function get_vehicle()
	{
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_company", $this->sess->user_company);
		$qvehicle = $this->db->get("vehicle");
		$row_vehicle = $qvehicle->result();
		return $row_vehicle;
	}

	function workshop()
	{
		$this->params['title']          = "Workshop List";
		$user_company                   = $this->sess->user_company;
		$privilegecode                  = $this->sess->user_id_role;
		$this->dbtransporter            = $this->load->database("transporter", true);
		$this->dbtransporter->where("workshop_company", $user_company);
		$this->dbtransporter->where("workshop_status", 1);
		$q                              = $this->dbtransporter->get("workshop");
		$this->params['workshop']       = $q->result_array();
		$this->params['code_view_menu'] = "configuration";
		$this->params['privilegecode']  = $privilegecode;
		// echo "<pre>";
		// var_dump($workshop);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/workshop/v_workshop', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/workshop/v_workshop', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/workshop/v_workshop', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/workshop/v_workshop', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function save_workshop()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company          = $this->sess->user_company;
		unset($data);

		$workshop_name            = isset($_POST['workshop_name']) ? $_POST['workshop_name'] :       "";
		$workshop_telp            = isset($_POST['workshop_telp']) ? $_POST['workshop_telp'] :       "";
		$workshop_fax             = isset($_POST['workshop_fax']) ? $_POST['workshop_fax'] :         "";
		$workshop_address         = isset($_POST['workshop_address']) ? $_POST['workshop_address'] : "";
		$workshop_company         = $my_company;

		$data['workshop_name']    = $workshop_name;
		$data['workshop_telp']    = $workshop_telp;
		$data['workshop_fax']     = $workshop_fax;
		$data['workshop_address'] = $workshop_address;
		$data['workshop_company'] = $workshop_company;

		//Insert
		$this->dbtransporter->insert('workshop', $data);
		$this->dbtransporter->close();

		$callback['error'] = false;
		$callback['message'] = "Workshop Successfully Submitted";
		$callback['redirect'] = base_url() . "vehicles/workshop";

		echo json_encode($callback);
		return;
	}

	function forconfigservicess()
	{
		$vehicle_id = $this->input->post('id');
		$user_id = $this->sess->user_id;

		$user_id                 = $this->sess->user_id;
		//$sql                     = "SELECT * FROM `webtracking_vehicle` where vehicle_id = '$vehicle_id' and vehicle_user_id = '$user_id' ORDER BY `vehicle_no` ASC ";
		$sql                     = "SELECT * FROM `webtracking_vehicle` where vehicle_id = '$vehicle_id' ORDER BY `vehicle_no` ASC ";
		$q                       = $this->db->query($sql);
		$result                  = $q->result_array();

		$cekvehiclenonya = $this->m_maintenance->cekvehiclenodbtransporter("maintenance_configuration", $result[0]['vehicle_no'])->result_array();
		$valueafterchcking = sizeof($cekvehiclenonya);
		// echo "<pre>";
		// var_dump($valueafterchcking);die();
		// echo "<pre>";
		if ($valueafterchcking == 0) {
			// GX ADA ISINYA
			$callback['vehicle'] = $result;
			$callback['row']     = $valueafterchcking;
			$callback['isirow']  = $valueafterchcking;
			echo json_encode($callback);
		} else {
			// ADA ISINYA
			$callback['vehicle'] = $result;
			$callback['row']     = $valueafterchcking;
			$callback['data']    = $cekvehiclenonya;
			$callback['isirow']  = $valueafterchcking;
			echo json_encode($callback);
		}
	}

	function savethisconfiguration()
	{
		$vehicle_no      = $this->input->post('vehicle_no');
		$vehicle_name    = $this->input->post('vehicle_name');
		$vehicle_type    = $this->input->post('vehicle_type');
		$vehicle_year    = $this->input->post('vehicle_year');
		$no_rangka       = $this->input->post('no_rangka');
		$no_mesin        = $this->input->post('no_mesin');
		$stnk_no         = $this->input->post('stnk_no');
		$stnkexpdate     = $this->input->post('stnkexpdatefix');
		$kir_no          = $this->input->post('kir_no');
		$kirexpdate      = $this->input->post('kirexpdatefix');
		$servicedby      = $this->input->post('servicedby');
		$valueservicedby = $this->input->post('valueservicedby');
		$vehicle_device   = $this->input->post('vehicle_device');
		$vehicle_type_gps = $this->input->post('vehicle_type_gps');
		$alertlimit       = $this->input->post('alertlimit');

		// CEK VEHICLE NO
		$cekvehiclenonya = $this->m_maintenance->cekvehiclenodbtransporter("maintenance_configuration", $vehicle_no)->result_array();
		$valueafterchcking = sizeof($cekvehiclenonya);
		// echo "<pre>";
		// var_dump($valueafterchcking);die();
		// echo "<pre>";
		if ($valueafterchcking == 0) {
			// DATA TIDAK ADA MAKA INPUT
			$data = array(
				"maintenance_conf_vehicle_user_company" => $this->sess->user_company,
				"maintenance_conf_vehicle_no"           => $vehicle_no,
				"maintenance_conf_vehicle_name"         => $vehicle_name,
				"maintenance_conf_vehicle_type"         => $vehicle_type,
				"maintenance_conf_vehicle_year"         => $vehicle_year,
				"maintenance_conf_no_rangka"            => $no_rangka,
				"maintenance_conf_no_mesin"             => $no_mesin,
				"maintenance_conf_stnk_no"              => $stnk_no,
				"maintenance_conf_stnkexpdate"          => date("Y-m-d", strtotime($stnkexpdate)),
				"maintenance_conf_kir_no"               => $kir_no,
				"maintenance_conf_kirexpdate"           => date("Y-m-d", strtotime($kirexpdate)),
				"maintenance_conf_servicedby"           => $servicedby,
				"maintenance_conf_valueservicedby"      => $valueservicedby,
				"maintenance_conf_vehicle_device"       => $vehicle_device,
				"maintenance_conf_vehicle_type_gps"     => $vehicle_type_gps,
				"maintenance_conf_alertlimit"           => $alertlimit
			);

			$insert = $this->m_maintenance->insertDataDbTransporter("maintenance_configuration", $data);
			if ($insert) {
				$status = "success";
			} else {
				$status = "failed";
			}
			$callback['data']   = $data;
			$callback['status'] = $status;
			$callback['msg']    = "Configuration Inserted";
			echo json_encode($callback);
		} else {
			// DATA ADA MAKA UPDATE
			$data = array(
				"maintenance_conf_vehicle_no"      			=> $vehicle_no,
				"maintenance_conf_vehicle_name"    			=> $vehicle_name,
				"maintenance_conf_vehicle_type"    			=> $vehicle_type,
				"maintenance_conf_vehicle_year"    			=> $vehicle_year,
				"maintenance_conf_no_rangka"       			=> $no_rangka,
				"maintenance_conf_no_mesin"        			=> $no_mesin,
				"maintenance_conf_stnk_no"         			=> $stnk_no,
				"maintenance_conf_stnkexpdate"     			=> date("Y-m-d", strtotime($stnkexpdate)),
				"maintenance_conf_kir_no"          			=> $kir_no,
				"maintenance_conf_kirexpdate"      			=> date("Y-m-d", strtotime($kirexpdate)),
				"maintenance_conf_servicedby"      			=> $servicedby,
				"maintenance_conf_valueservicedby" 			=> $valueservicedby,
				"maintenance_conf_vehicle_device"       => $vehicle_device,
				"maintenance_conf_vehicle_type_gps"     => $vehicle_type_gps,
				"maintenance_conf_alertlimit"           => $alertlimit
			);

			$update = $this->m_maintenance->updateDataDbTransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $vehicle_no, $data);
			if ($update) {
				$status = "success";
			} else {
				$status = "failed";
			}
			$callback['data']   = $data;
			$callback['status'] = $status;
			$callback['msg']    = "Configuration Updated";
			echo json_encode($callback);
		}
	}

	function getfornotif()
	{
		date_default_timezone_set("Asia/Bangkok");
		$datanotifstnk    = array();
		$datanotifkir     = array();
		$datanotifservice = array();
		$user_company     = $this->sess->user_company;



		// GET STNK EXP DATE
		$getstnkexpdate = $this->m_maintenance->getstnkexpdate("maintenance_configuration", $user_company);
		for ($i = 0; $i < sizeof($getstnkexpdate); $i++) {
			array_push($datanotifstnk, array(
				"vehicle_no"          => $getstnkexpdate[$i]['maintenance_conf_vehicle_no'],
				"vehicle_name"        => $getstnkexpdate[$i]['maintenance_conf_vehicle_name'],
				"vehicle_type"        => $getstnkexpdate[$i]['maintenance_conf_vehicle_type'],
				"vehicle_stnkno"      => $getstnkexpdate[$i]['maintenance_conf_stnk_no'],
				"vehicle_stnkexpdate" => $getstnkexpdate[$i]['maintenance_conf_stnkexpdate']
			));
		}

		// GET STNK EXP DATE
		$getkirexpdate = $this->m_maintenance->getkirexpdate("maintenance_configuration", $user_company);
		for ($j = 0; $j < sizeof($getkirexpdate); $j++) {
			array_push($datanotifkir, array(
				"vehicle_no"         => $getkirexpdate[$j]['maintenance_conf_vehicle_no'],
				"vehicle_name"       => $getkirexpdate[$j]['maintenance_conf_vehicle_name'],
				"vehicle_type"       => $getkirexpdate[$j]['maintenance_conf_vehicle_type'],
				"vehicle_kirno"      => $getkirexpdate[$j]['maintenance_conf_kir_no'],
				"vehicle_kirexpdate" => $getkirexpdate[$j]['maintenance_conf_kirexpdate']
			));
		}

		// GET SERVICE SCHEDULE
		$finaldata    = array();
		$finaldatafix = array();
		$servicebykm  = array();
		$getservicescheduleperkm = $this->m_maintenance->getservicescheduleperkm("maintenance_configuration", $user_company);
		// echo "<pre>";
		// var_dump($getservicescheduleperkm);die();
		// echo "<pre>";
		// $arr                = explode("@", $getservicescheduleperkm[1]['maintenance_conf_vehicle_device']);
		// $devices[0]         = (count($arr) > 0) ? $arr[0]: "";
		// $devices[1]         = (count($arr) > 1) ? $arr[1]: "";
		// $lasttime           = 0;
		// $type_gps           = $getservicescheduleperkm[1]['maintenance_conf_vehicle_type_gps'];
		// $v_location         = $this->m_maintenance->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $type_gps);
		// echo "<pre>";
		// var_dump($v_location);die();
		// echo "<pre>";
		for ($i = 0; $i < sizeof($getservicescheduleperkm); $i++) {
			$lasttime           = 0;
			$device             = $getservicescheduleperkm[$i]['maintenance_conf_vehicle_device'];
			$type_gps           = $getservicescheduleperkm[$i]['maintenance_conf_vehicle_type_gps'];
			$arr                = explode("@", $device);
			$devices[0]         = (count($arr) > 0) ? $arr[0] : "";
			$devices[1]         = (count($arr) > 1) ? $arr[1] : "";
			$v_location         = $this->m_maintenance->GetLastInfo($devices[0], $devices[1], true, false, $lasttime, $type_gps);
			$getvehicleodometer = $this->m_maintenance->getodobyvehicledevice("webtracking_vehicle", $getservicescheduleperkm[$i]['maintenance_conf_vehicle_device']);

			array_push($finaldata, array(
				"data"             => $v_location,
				"vehicle_odometer" => $getvehicleodometer
			));

			// get alertvalue
			// sisaodometer = (lastodometerfromgps - lastodometerfrominput)
			// jika sisaodometer mendekati atau melebihi alertvalue maka munculkan alert
			// jika tidak alert tidak muncul
			array_push($finaldatafix, array(
				"maintenance_conf_vehicle_no"      => $getservicescheduleperkm[$i]['maintenance_conf_vehicle_no'],
				"maintenance_conf_vehicle_name"    => $getservicescheduleperkm[$i]['maintenance_conf_vehicle_name'],
				"device"                           => $device,
				"type_gps"                         => $type_gps,
				"maintenance_conf_servicedby"      => $getservicescheduleperkm[$i]['maintenance_conf_servicedby'],
				"lastodometerfromgps"              => round(($finaldata[$i]['data'][0]['gps_info_distance']) / 1000 + $finaldata[$i]['vehicle_odometer'][0]['vehicle_odometer']),
				"maintenance_conf_valueservicedby" => $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby'],
				"maintenance_conf_lastodometer"    => $getservicescheduleperkm[$i]['maintenance_conf_lastodometer'],
				"maintenance_conf_last_service"    => $getservicescheduleperkm[$i]['maintenance_conf_last_service'],
				"finalodometer"                    => round(($getservicescheduleperkm[$i]['maintenance_conf_lastodometer'] + $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby']) - $getservicescheduleperkm[$i]['maintenance_conf_alertlimit']),
			));

			$odometerforservice = "";
			if (round($getservicescheduleperkm[$i]['maintenance_conf_lastodometer']) == "") {
				$odometerforservice = round(($finaldata[$i]['data'][0]['gps_info_distance']) / 1000 + $finaldata[$i]['vehicle_odometer'][0]['vehicle_odometer'] +  $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby']);
			} else {
				$odometerforservice = round(($getservicescheduleperkm[$i]['maintenance_conf_lastodometer'] + $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby']));
			}

			if ($finaldatafix[$i]['lastodometerfromgps'] >= $finaldatafix[$i]['finalodometer']) {
				array_push($servicebykm, array(
					"kondisi"               => "1",
					"vehicle_no"            => $getservicescheduleperkm[$i]['maintenance_conf_vehicle_no'],
					"vehicle_name"          => $getservicescheduleperkm[$i]['maintenance_conf_vehicle_name'],
					"device"                => $device,
					"type_gps"              => $type_gps,
					"servicedby"            => $getservicescheduleperkm[$i]['maintenance_conf_servicedby'],
					"lastodometerfromgps"   => round(($finaldata[$i]['data'][0]['gps_info_distance']) / 1000 + $finaldata[$i]['vehicle_odometer'][0]['vehicle_odometer']),
					"alertperkm"            => $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby'],
					"lastodometerfrominput" => $getservicescheduleperkm[$i]['maintenance_conf_lastodometer'],
					"last_service"          => $getservicescheduleperkm[$i]['maintenance_conf_last_service'],
					"odometerforservice"    => $odometerforservice,
				));
			}
		}

		$getserviceschedulepermonth = $this->m_maintenance->getserviceschedulepermonth("maintenance_configuration", $user_company);
		$sizepermont                = sizeof($getserviceschedulepermonth);
		$servicedbymonth            = array();
		for ($b = 0; $b < $sizepermont; $b++) {
			if (date("Y-m-d") >= date("Y-m-d", strtotime($getserviceschedulepermonth[$b]['maintenance_conf_last_service'] . "+" . $getserviceschedulepermonth[$b]['maintenance_conf_alertlimit'] . "Month"))) {
				array_push($servicedbymonth, array(
					"kondisi" 	 	 	 => "2",
					"vehicle_no"     => $getserviceschedulepermonth[$b]['maintenance_conf_vehicle_no'],
					"vehicle_name"   => $getserviceschedulepermonth[$b]['maintenance_conf_vehicle_name'],
					"service_setiap" => $getserviceschedulepermonth[$b]['maintenance_conf_valueservicedby'],
					"servicedby"     => $getserviceschedulepermonth[$b]['maintenance_conf_servicedby'],
					"last_service"   => date("Y-m-d", strtotime($getserviceschedulepermonth[$b]['maintenance_conf_last_service'])),
					"next_service"   => date("Y-m-d", strtotime($getserviceschedulepermonth[$b]['maintenance_conf_last_service'] . "+" . $getserviceschedulepermonth[$b]['maintenance_conf_valueservicedby'] . "Month")),
					"current_date"   => date("Y-m-d")
				));
			}
		}

		// IF USERID == POWERBLOCK
		$user_id                 = $this->sess->user_id;
		if ($user_id == "1147") {
			$getfromtable = $this->m_maintenance->getalerttable("powerblock_alert", "transporter_isread", "0");
			$callback['total_oogpbi']               = sizeof($getfromtable);
			$callback['data_oogpbi']                = $getfromtable;
		}

		$callback['total_stnkexpdate']          = sizeof($datanotifstnk);
		$callback['data_notifstnk']             = $datanotifstnk;
		$callback['total_kirexpdate']           = sizeof($datanotifkir);
		$callback['data_notifkir']              = $datanotifkir;
		$callback['total_notifserviceperkm']    = sizeof($servicebykm);
		$callback['data_notifserviceperkm']     = $servicebykm;
		$callback['total_notifservicepermonth'] = sizeof($servicedbymonth);
		$callback['data_notifservicepermonth']  = $servicedbymonth;

		// echo "<pre>";
		// var_dump($callback['total_stnkexpdate']);die();
		// echo "<pre>";
		echo json_encode($callback);
	}

	function forsetservicess()
	{
		$vehicle_id        = $this->input->post('id');
		$user_id         	 = $this->sess->user_id;
		$user_company      = $this->sess->user_company;

		$getservicetype    = $this->m_maintenance->gogetservicetype("service_type");
		$resultservicetype = $getservicetype->result_array();

		//$sql             	 = "SELECT * FROM `webtracking_vehicle` where vehicle_id = '$vehicle_id' and vehicle_user_id = '$user_id' ORDER BY `vehicle_no` ASC ";
		$sql             	 = "SELECT * FROM `webtracking_vehicle` where vehicle_id = '$vehicle_id' ORDER BY `vehicle_no` ASC ";
		$q               	 = $this->db->query($sql);
		$result          	 = $q->result_array();
		$cekvehiclenonya   = $this->m_maintenance->cekvehiclenodbtransporter("maintenance_configuration", $result[0]['vehicle_no'])->result_array();
		$valueafterchcking = sizeof($cekvehiclenonya);

		$getworkshop 			= $this->m_maintenance->g_all("workshop", "workshop_company", $user_company, "workshop_name", "asc");
		// echo "<pre>";
		// var_dump($getworkshop);die();
		// echo "<pre>";
		$callback['data']                  = $resultservicetype;
		$callback['dataconfigmaintenance'] = $cekvehiclenonya;
		$callback['sizeconfig']            = $valueafterchcking;
		$callback['workshop']              = $getworkshop;
		$callback['vehicledata']           = $result;
		echo json_encode($callback);
	}

	function savetomaintenancehistory()
	{
		date_default_timezone_set("Asia/Bangkok");
		$user_id        = $this->sess->user_id;
		$user_company   = $this->sess->user_company;
		$tipeservice    = $this->input->post('tipeservice');
		$vehicle_device = $this->input->post('vehicle_device');
		// echo "<pre>";
		// var_dump($user_id.'-'.$user_company.'-'.$tipeservice.'-'.$vehicle_device);die();
		// echo "<pre>";
		$data = array();

		if ($tipeservice == 2) {
			// KIR
			$v_kirno_setservicess          = $this->input->post('v_kirno_setservicess');
			$v_kirdate_setservicess        = $this->input->post('v_kirdate_setservicess');
			$v_kir_exp_date_setservicess   = $this->input->post('v_kir_exp_date_setservicess');
			$v_kirnote_setservicess        = $this->input->post('v_kirnote_setservicess');
			$v_kirvehicle_no               = $this->input->post('v_kirvehicle_no');
			$v_kirvehicle_name             = $this->input->post('v_kirvehicle_name');
			$v_kir_pelaksana               = $this->input->post('v_kir_pelaksana');
			$v_kir_biaya                   = $this->input->post('v_kir_biaya');
			$v_work_agenc_kir_setservicess = $this->input->post('work_agenc_kir_setservicess');

			$data = array(
				"servicess_tipeservice"    => $tipeservice,
				"servicess_name"           => "KIR",
				"servicess_vehicle_device" => $vehicle_device,
				"servicess_vehicle_no"     => $v_kirvehicle_no,
				"servicess_vehicle_name"   => $v_kirvehicle_name,
				"servicess_nol"            => $v_kirno_setservicess,
				"servicess_date"           => date("Y-m-d", strtotime($v_kirdate_setservicess)) . " 00:00:00",
				"servicess_pelaksana"      => $v_kir_pelaksana,
				"servicess_biaya"          => $v_kir_biaya,
				"servicess_note"           => $v_kirnote_setservicess,
				"servicess_work_agencies"  => $v_work_agenc_kir_setservicess,
				"servicess_user_company"   => $user_company
			);

			$dataforupdate = array(
				"maintenance_conf_kir_extendsdate" => date("Y-m-d", strtotime($v_kirdate_setservicess)),
				"maintenance_conf_kirexpdate"      => date("Y-m-d", strtotime($v_kir_exp_date_setservicess))
			);
			$update = $this->m_maintenance->updateDatadbtransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $v_kirvehicle_no, $dataforupdate);
			if ($update) {
				$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
				if ($insert) {
					$status = "success";
				} else {
					$status = "failed";
				}
				$callback['status'] = $status;
				$callback['msg']    = "Data Succesfully Inserted To Servicess History";
				echo json_encode($callback);
			} else {
				$status = "failed";
				$callback['status'] = $status;
				$callback['msg']    = "Data Succesfully Inserted To Servicess History";
				echo json_encode($callback);
			}
		} elseif ($tipeservice == 3) {
			// PERPANJANG STNK
			$v_perpstnk_vehicle_no           = $this->input->post('v_perpstnk_vehicle_no');
			$v_perpstnk_vehicle_name         = $this->input->post('v_perpstnk_vehicle_name');
			$v_perpstnk_no_setservicess      = $this->input->post('v_perpstnk_no_setservicess');
			$v_perpstnk_date_setservicess    = $this->input->post('v_perpstnk_date_setservicess');
			$v_perpstnk_expdate_setservicess = $this->input->post('v_perpstnk_expdate_setservicess');
			$v_perpstnk_pelaksana            = $this->input->post('v_perpstnk_pelaksana');
			$v_perpstnk_biaya                = $this->input->post('v_perpstnk_biaya');
			$v_perpstnk_note_setservicess    = $this->input->post('v_perpstnk_note_setservicess');
			$work_agenc_stnk_setservicess    = $this->input->post('work_agenc_stnk_setservicess');

			$data = array(
				"servicess_tipeservice"   => $tipeservice,
				"servicess_name"          => "PERPANJANG STNK",
				"servicess_vehicle_device" => $vehicle_device,
				"servicess_vehicle_no"    => $v_perpstnk_vehicle_no,
				"servicess_vehicle_name"  => $v_perpstnk_vehicle_name,
				"servicess_nol"           => $v_perpstnk_no_setservicess,
				"servicess_date"          => date("Y-m-d", strtotime($v_perpstnk_date_setservicess)) . " 00:00:00",
				"servicess_pelaksana"     => $v_perpstnk_pelaksana,
				"servicess_biaya"         => $v_perpstnk_biaya,
				"servicess_note"          => $v_perpstnk_note_setservicess,
				"servicess_work_agencies" => $work_agenc_stnk_setservicess,
				"servicess_user_company"  => $user_company
			);

			$dataforupdate = array(
				"maintenance_conf_stnk_extendsdate" => date("Y-m-d", strtotime($v_perpstnk_date_setservicess)),
				"maintenance_conf_stnkexpdate"      => date("Y-m-d", strtotime($v_perpstnk_date_setservicess))
			);
			$update = $this->m_maintenance->updateDatadbtransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $v_perpstnk_vehicle_no, $dataforupdate);
			if ($update) {
				$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
				if ($insert) {
					$status = "success";
				} else {
					$status = "failed";
				}
				$callback['status'] = $status;
				$callback['msg']    = "Data Succesfully Inserted To Servicess History";
				echo json_encode($callback);
			} else {
				$status = "failed";
				$callback['status'] = $status;
				$callback['msg']    = "Data Succesfully Inserted To Servicess History";
				echo json_encode($callback);
			}
		} else {
			// SERVICE
			$v_service_vehicle_no        = $this->input->post('v_service_vehicle_no');
			$v_service_vehicle_name      = $this->input->post('v_service_vehicle_name');
			$v_service_date_setservicess = $this->input->post('v_service_date_setservicess');
			$v_service_pelaksana         = $this->input->post('v_service_pelaksana');
			$v_service_biaya             = $this->input->post('v_service_biaya');
			$v_service_lastodometer      = $this->input->post('v_service_lastodometer');
			$v_service_note_setservicess = $this->input->post('v_service_note_setservicess');
			$work_agenc_setservicess     = $this->input->post('work_agenc_setservicess');

			$data = array(
				"servicess_tipeservice"    => $tipeservice,
				"servicess_name"           => "MAINTENANCE SERVICE",
				"servicess_vehicle_device" => $vehicle_device,
				"servicess_vehicle_no"     => $v_service_vehicle_no,
				"servicess_vehicle_name"   => $v_service_vehicle_name,
				"servicess_nol"            => $v_service_lastodometer,
				"servicess_date"           => date("Y-m-d", strtotime($v_service_date_setservicess)) . " 00:00:00",
				"servicess_pelaksana"      => $v_service_pelaksana,
				"servicess_biaya"          => $v_service_biaya,
				"servicess_note"           => $v_service_note_setservicess,
				"servicess_work_agencies"  => $work_agenc_setservicess,
				"servicess_user_company"   => $user_company
			);

			$getconfigbyvehicle_no = $this->m_maintenance->g_all("maintenance_configuration", "maintenance_conf_vehicle_no", $v_service_vehicle_no, "maintenance_conf_vehicle_no", "asc");
			if ($getconfigbyvehicle_no[0]['maintenance_conf_servicedby'] == "permonth") {
				// JIKA ALERT PER MONTH
				$dataforupdate = array(
					"maintenance_conf_lastodometer" => $v_service_lastodometer,
					"maintenance_conf_last_service" => date("Y-m-d", strtotime($v_service_date_setservicess)) . " 00:00:00"
				);
				$update = $this->m_maintenance->updateDatadbtransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $v_service_vehicle_no, $dataforupdate);
				if ($update) {
					$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
					if ($insert) {
						$status = "success";
					} else {
						$status = "failed";
					}
					$callback['status'] = $status;
					$callback['msg']    = "Data Succesfully Inserted To Servicess History";
					echo json_encode($callback);
				} else {
					$status = "failed";
					$callback['status'] = $status;
					$callback['msg']    = "Data Succesfully Inserted To Servicess History";
					echo json_encode($callback);
				}
			} else {
				// ALERT PER KM
				$dataforupdate = array(
					"maintenance_conf_lastodometer" => $v_service_lastodometer,
					"maintenance_conf_last_service" => date("Y-m-d", strtotime($v_service_date_setservicess)) . " 00:00:00"
				);
				$update = $this->m_maintenance->updateDatadbtransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $v_service_vehicle_no, $dataforupdate);
				if ($update) {
					$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
					if ($insert) {
						$status = "success";
					} else {
						$status = "failed";
					}
					$callback['status'] = $status;
					$callback['msg']    = "Data Succesfully Inserted To Servicess History";
					echo json_encode($callback);
				} else {
					$status = "failed";
					$callback['status'] = $status;
					$callback['msg']    = "Data Succesfully Inserted To Servicess History";
					echo json_encode($callback);
				}
			}
		}
	}

	function maintenance()
	{
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$privilegecode   = $this->sess->user_id_role;

		if ($this->sess->user_id == "1445") {
			$user_id = $this->sess->user_id; //tag
		} else {
			$user_id = $this->sess->user_id;
		}

		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no", "asc");

		if ($privilegecode == 0) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 1) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 3) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 4) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else {
			$this->db->where("vehicle_no", 99999);
		}

		$this->db->where("vehicle_status <>", 3);
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();

		// GET ASSIGNED VEHICLE STATUS
		$this->params["datavehicle"] 				= $result;

		// GET BRANCH
		$this->db->where("company_created_by", $user_parent);
		$qcompany                = $this->db->get("company");
		$rescompany              = $qcompany->result_array();
		$this->params["company"] = $rescompany;

		// GET SUBBRANCH
		$this->db->where("subcompany_creator", $user_parent);
		$qsubcompany                = $this->db->get("subcompany");
		$ressubcompany              = $qsubcompany->result_array();
		$this->params["subcompany"] = $ressubcompany;

		// GET GROUP
		$this->db->where("group_creator", $user_parent);
		$qgroup                = $this->db->get("group");
		$resqgroup             = $qgroup->result_array();
		$this->params["group"] = $resqgroup;

		// GET GROUP
		$this->db->where("subgroup_creator", $user_parent);
		$qsubgroup                          = $this->db->get("subgroup");
		$ressubgroup                        = $qsubgroup->result_array();
		$this->params["subgroup"]           = $ressubgroup;
		$this->params["unscheduledservice"] = $this->m_maintenance->getunscheduledservice("servicess_history");

		$getservicetype                  = $this->m_maintenance->gogetservicetype("service_type");
		$resultservicetype               = $getservicetype->result_array();
		$getworkshop                     = $this->m_maintenance->g_all("workshop", "workshop_company", $user_company, "workshop_name", "asc");

		$this->params['workshop']        = $getworkshop;
		$this->params['dataservicetype'] = $resultservicetype;
		$this->params['code_view_menu']  = "configuration";
		$this->params['privilegecode']   = $privilegecode;

		// echo "<pre>";
		// var_dump($this->params["workshop"]);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function maintenancehistory()
	{
		$getservicetype                 = $this->m_maintenance->gogetservicetype2("service_type");
		$resultservicetype              = $getservicetype->result_array();
		$user_id                        = $this->sess->user_id;
		$privilegecode 									= $this->sess->user_id_role;

		$sql                            = "SELECT * FROM `webtracking_vehicle` where vehicle_user_id = '$user_id' AND `vehicle_status` <> 3 ORDER BY `vehicle_no` ASC ";
		$q                              = $this->db->query($sql);
		$result                         = $q->result_array();

		$this->params['vehicle']        = $result;
		$this->params['sortby']         = "mobil_id";
		$this->params['orderby']        = "asc";
		$this->params['title']          = "Maintenance History";
		$this->params['servicetype']    = $resultservicetype;
		$this->params['code_view_menu'] = "report";
		// echo "<pre>";
		// var_dump($resultservicetype);die();
		// echo "<pre>";

		$this->params["header"]          = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]     = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]         = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]         = $this->load->view('newdashboard/vehicles/v_maintenance_history', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_maintenance_history', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_maintenance_history', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} else {
			$this->params["sidebar"]         = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]         = $this->load->view('newdashboard/vehicles/v_maintenance_history', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function showmaintenancehistory()
	{
		$user_company    = $this->sess->user_company;
		$selectservicess = $this->input->post('selectservicess');
		$selectvehicle   = $this->input->post('selectvehicle');
		$servicestatus   = $this->input->post('servicestatus');
		$date            = date("Y-m-d", strtotime($this->input->post('date')));
		$enddate         = date("Y-m-d", strtotime($this->input->post('enddate')));
		$gethistory      = $this->m_maintenance->getformaintenancehistory("servicess_history", $user_company, $selectvehicle, $selectservicess, $date, $enddate, $servicestatus);

		// $selectservicess.'-'.$selectvehicle.'-'.$date.'-'.$enddate
		// echo "<pre>";
		// var_dump($gethistory);die();
		// echo "<pre>";
		$callback['tipeservices'] = $selectservicess;
		$callback['data']         = $gethistory;
		$callback["start_date"]   = $date;
		$callback["end_date"]     = $enddate;
		$callback['error']        = false;
		echo json_encode($callback);
	}

	function formvehicle()
	{
		$vehicleids    = $this->vehiclemodel->getVehicleIds();

		$vid           = isset($_POST['id']) ? $_POST['id'] :   "";
		$uid           = isset($_POST['uid']) ? $_POST['uid'] : "";

		$params['uid'] = $uid;

		if ($vid) {
			if ($this->sess->user_type == 2) {
				$this->db->where_in("vehicle_id", $vehicleids);
			}
			$this->db->where("vehicle_id", $vid);
			$this->db->join("user", "user_id = vehicle_user_id");
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0) {
				$callback['error'] = true;
				echo json_encode($callback);
				return;
			}
			$row                         = $q->row();
			$row->vehicle_active_date1_t = dbintmaketime($row->vehicle_active_date1, 0);
			$row->vehicle_active_date2_t = dbintmaketime($row->vehicle_active_date2, 0);
			$row->vehicle_active_date_t  = dbintmaketime($row->vehicle_active_date, 0);

			$json                        = json_decode($row->vehicle_info);
			$row->vehicle_ip             = isset($json->vehicle_ip) ? $json->vehicle_ip : $this->config->item("ip_colo");

			$params['vehicle'] = $row;
			$params['owner']   = $row->vehicle_user_id;
		} else {
			$params['owner']   = $uid;
		}

		if ($this->sess->user_type == 2) {
			$this->db->where("user_id", $this->sess->user_id);
		}

		$this->db->order_by("user_name", "asc");
		$q = $this->db->get("user");

		$params["users"] = $q->result();

		//Get Company
		//$this->db->where("company_id", $this->sess->user_company);
		$this->db->where("company_id = '" . $this->sess->user_company . "' OR company_created_by = '" . $this->sess->user_id . "'");
		$this->db->order_by("company_name", "asc");
		$q                   = $this->db->get("company");
		$rowcompanies        = $q->result();
		//print_r($rowcompanies);exit;
		$params["companies"] = $rowcompanies;
		$params['selected'] = 0;

		$this->db->distinct();
		$this->db->select("fuel_tank_capacity");
		$qfuel = $this->db->get("fuel");

		if ($qfuel->num_rows() > 0) {
			$rfuel = $qfuel->result();

			$params['fuel'] = $rfuel;
		}

		//Get Driver
		$rows_driver = $this->getAllDriver();
		$params["drivers"] = $rows_driver;

		$this->db->where("group_status", 1);
		$this->db->where("group_company", $this->sess->user_company);
		$this->db->order_by("group_name", "asc");
		$customer           = $this->db->get("group")->result_array();
		$params["customer"] = $customer;

		$vehicle_branchoffice    = $row->vehicle_company;
		$vehicle_subbranchoffice = $row->vehicle_subcompany;
		$vehicle_customer        = $row->vehicle_group;
		$vehicle_subcustomer     = $row->vehicle_subgroup;

		// GET DATA FOR SETTING COMPANY START
		$getbranchofficeby_vid    = $this->getbranchofficebyid($vehicle_branchoffice);
		$getsubbranchofficeby_vid = $this->getsubbranchofficebyid($vehicle_subbranchoffice);
		$getcustomerby_vid        = $this->getcustomerbyid($vehicle_customer);
		$getsubcustomerby_vid     = $this->getsubcustomerbyid($vehicle_subcustomer);

		$branchofficedata    = array();
		$subbranchofficedata = array();
		$customer            = array();
		$subcustomer         = array();
		// BRANCH OFFICE DATA
		if (sizeof($getbranchofficeby_vid) > 0) {
			$branchofficedata = array(
				"company_id"   => $getbranchofficeby_vid[0]['company_id'],
				"company_name" => $getbranchofficeby_vid[0]['company_name']
			);
		} else {
			$branchofficedata = array(
				"company_id"   => "0",
				"company_name" => "Not Set"
			);
		}

		// BRANCH OFFICE DATA
		if (sizeof($getsubbranchofficeby_vid) > 0) {
			$subbranchofficedata = array(
				"subcompany_id"   => $getsubbranchofficeby_vid[0]['subcompany_id'],
				"subcompany_name" => $getsubbranchofficeby_vid[0]['subcompany_name']
			);
		} else {
			$subbranchofficedata = array(
				"subcompany_id"   => "0",
				"subcompany_name" => "Not Set"
			);
		}

		// CUSTOMER DATA
		if (sizeof($getcustomerby_vid) > 0) {
			$customer = array(
				"group_id"   => $getcustomerby_vid[0]['group_id'],
				"group_name" => $getcustomerby_vid[0]['group_name']
			);
		} else {
			$customer = array(
				"group_id"   => "0",
				"group_name" => "Not Set"
			);
		}

		// CUSTOMER DATA
		if (sizeof($getsubcustomerby_vid) > 0) {
			$subcustomer = array(
				"subgroup_id"   => $getsubcustomerby_vid[0]['subgroup_id'],
				"subgroup_name" => $getsubcustomerby_vid[0]['subgroup_name']
			);
		} else {
			$subcustomer = array(
				"subgroup_id"   => "0",
				"subgroup_name" => "Not Set"
			);
		}
		// GET DATA FOR SETTING COMPANY START
		$params['branchoffice']    = $branchofficedata;
		$params['subbranchoffice'] = $subbranchofficedata;
		$params['customer']        = $customer;
		$params['subcustomer']     = $subcustomer;

		// echo "<pre>";
		// var_dump($params['subbranchoffice']);die();
		// echo "<pre>";

		// $html = $this->load->view("dashboard/vehicles/v_formvehicle", $params, true);
		$html = $this->load->view("newdashboard/vehicles/v_formvehicle", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
	}

	function vfuelcalibration()
	{
		$vehicleids            = $this->vehiclemodel->getVehicleIds();
		$vid                   = isset($_POST['id']) ? $_POST['id'] : "";

		$this->db->where("vehicle_id", $vid);
		$this->db->where("vehicle_status <>", 3);
		$params['datavehicle'] = $this->db->get("vehicle")->result_array();

		// echo "<pre>";
		// var_dump($vehicledata);die();
		// echo "<pre>";

		$html              = $this->load->view("dashboard/vehicles/v_formfuelcalibration", $params, true);
		$callback['error'] = false;
		$callback['html']  = $html;
		echo json_encode($callback);
	}

	function savecalibration()
	{
		$vehicle_id            = isset($_POST['vehicle_id']) ? $_POST['vehicle_id']                      : "";
		$vehicle_fuel_capacity = isset($_POST['vehicle_fuel_capacity']) ? $_POST['vehicle_fuel_capacity'] : "";
		$vehicle_fuel_volt     = isset($_POST['vehicle_fuel_volt']) ? $_POST['vehicle_fuel_volt']        : "";

		// echo "<pre>";
		// var_dump($vehicle_id.'-'.$vehicle_fuel_capacity.'-'.$vehicle_fuel_volt);die();
		// echo "<pre>";

		$data = array(
			"vehicle_fuel_capacity" => $vehicle_fuel_capacity,
			"vehicle_fuel_volt"     => $vehicle_fuel_volt
		);

		$this->db->where("vehicle_id", $vehicle_id);
		$update = $this->db->update("vehicle", $data);

		if ($update) {
			$callback['error']   = false;
			$callback['message'] = "Success Submit Fuel Calibration";
			echo json_encode($callback);
		} else {
			$callback['error']   = true;
			$callback['message'] = "Failed Submit Fuel Calibration";
			echo json_encode($callback);
		}
	}

	function getAllDriver()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$this->dbtransporter->select("*");
		$this->dbtransporter->where("driver_company", $this->sess->user_company);
		$this->dbtransporter->from("driver");
		$qdriver = $this->dbtransporter->get();
		$qrow = $qdriver->result();
		return $qrow;
		$this->dbtransporter->close();
	}

	function savevehicle($isman = 0)
	{
		$vehicle_id = isset($_POST['vehicle_id']) ? trim($_POST['vehicle_id']) : "";
		// echo "<pre>";
		// var_dump($vehicle_id);die();
		// echo "<pre>";
		$vehicleids = $this->vehiclemodel->getVehicleIds();

		if (!in_array($vehicle_id, $vehicleids)) {
			redirect(base_url());
		}

		$vehicle_user_id    = isset($_POST['vehicle_user_id']) ? trim($_POST['vehicle_user_id']) :       "";
		$vehicle_device     = isset($_POST['vehicle_device']) ? trim($_POST['vehicle_device']) :         "";
		$vehicle_type       = isset($_POST['vehicle_type']) ? trim($_POST['vehicle_type']) :             "";
		$vehicle_no         = isset($_POST['vehicle_no']) ? trim($_POST['vehicle_no']) :                 "";
		$vehicle_no_new     = isset($_POST['vehicle_no_new']) ? trim($_POST['vehicle_no_new']) :                 "";
		$vehicle_name       = isset($_POST['vehicle_name']) ? trim($_POST['vehicle_name']) :             "";

		$vehicle_card_no    = isset($_POST['vehicle_card_no']) ? trim($_POST['vehicle_card_no']) :       "";
		$vehicle_card_no    = str_replace(" ", "", $vehicle_card_no);

		$vehicle_operator   = isset($_POST['vehicle_operator']) ? trim($_POST['vehicle_operator']) :     "";

		$vehicle_maxspeed   = isset($_POST['vehicle_maxspeed']) ? trim($_POST['vehicle_maxspeed']) :     "";
		$vehicle_maxspeed   = str_replace(",", ".", $vehicle_maxspeed);

		$vehicle_maxparking = isset($_POST['vehicle_maxparking']) ? trim($_POST['vehicle_maxparking']) : "";
		$vehicle_maxparking = str_replace(",", ".", $vehicle_maxparking);

		$vehicle_odometer   = isset($_POST['vehicle_odometer']) ? trim($_POST['vehicle_odometer']) :     0;
		$vehicle_odometer   = str_replace(",", ".", $vehicle_odometer);

		$vehicle_image      = isset($_POST['vehicle_image']) ? trim($_POST['vehicle_image']) :           "";
		$vehicle_group      = isset($_POST['group']) ? trim($_POST['group']) :                           "";
		$vehicle_company    = isset($_POST['usersite']) ? trim($_POST['usersite']) :                     0;

		$driver_id          = isset($_POST['driver']) ? trim($_POST['driver']) :                         "";

		if (strlen($vehicle_device) == 0) {
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_vehicle_device');
			echo json_encode($callback);
			return;
		}

		if ($vehicle_id) {
			$this->db->where("vehicle_id <>", $vehicle_id);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $vehicle_device);
		$total = $this->db->count_all_results("vehicle");

		if ($total) {
			/* $callback['error'] = true;
			$callback['message'] = $this->lang->line('lexist_vehicle_device');

			echo json_encode($callback);
			return; */
		}


		if (strlen($vehicle_no) == 0) {
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_vehicle_no');

			echo json_encode($callback);
			return;
		}

		if ($vehicle_id) {
			$this->db->where("vehicle_id <>", $vehicle_id);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_no", $vehicle_no);
		$total = $this->db->count_all_results("vehicle");
		if ($total) {
			/* $callback['error'] = true;
		$callback['message'] = $this->lang->line('lexist_vehicle_no');

		echo json_encode($callback);
		return; */
		}

		if (strlen($vehicle_name) == 0) {
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_vehicle_name');

			echo json_encode($callback);
			return;
		}

		if (strlen($vehicle_odometer)) {
			if ((!is_numeric($vehicle_odometer)) || ($vehicle_odometer < 0)) {
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_initialodometer');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($vehicle_maxspeed)) {
			if ((!is_numeric($vehicle_maxspeed)) || ($vehicle_maxspeed < 0)) {
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_maxspeed');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($vehicle_maxparking)) {
			if ((!is_numeric($vehicle_maxparking)) || ($vehicle_maxparking < 0)) {
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('linvalid_maxparkingtime');

				echo json_encode($callback);
				return;
			}
		}

		if (strlen($vehicle_card_no) == 0) {
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lvehicle_card_no_empty');

			echo json_encode($callback);
			return;
		}

		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_card_no", $vehicle_card_no);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() > 0) {
			$rowsimcard = $q->row();
			if ($rowsimcard->vehicle_id != $vehicle_id) {
				/* $callback['error'] = true;
				$callback['message'] = $this->lang->line('lvehicle_card_no_exist');

				echo json_encode($callback);
				return; */
			}
		}

		//Khusus Tupperware Transporter
		if ($this->sess->user_trans_tupper == 1) {
			$booking_id = $this->cek_booking_id($vehicle_device);
		}

		unset($data);

		// echo "<pre>";
		// var_dump($vehicle_no.'-'.$vehicle_no_new);die();
		// echo "<pre>";

		$vehicle_nofix     = "";
		$vehicle_no_bk_fix = "";

		if ($vehicle_no_new == "") {
			$vehicle_nofix     = $vehicle_no;
			$vehicle_no_bk_fix = "";
		}else {
			$vehicle_nofix     = $vehicle_no_new;
			$vehicle_no_bk_fix = $vehicle_no;
		}

		$this->db->where("vehicle_status <> ", 3);
		$this->db->where("vehicle_no", $vehicle_nofix);
		$vehiclenonew = $this->db->get("vehicle")->result_array();

		// echo "<pre>";
		// var_dump($vehiclenonew);die();
		// echo "<pre>";

		if (sizeof($vehiclenonew) > 0) {
			$callback['error'] = true;
			$callback['message'] = "Vehicle Not available";

			echo json_encode($callback);
			return;
		}

		// echo "<pre>";
		// var_dump($vehiclenonew);die();
		// echo "<pre>";

		$data['vehicle_status'] = 1;
		if ($this->sess->user_trans_tupper == 1) {
			if ($booking_id == "false") {
				$data['vehicle_group']    = $vehicle_group;
				$data['vehicle_image']    = $vehicle_image;
			}
		} else {
			$data['vehicle_group']     = $vehicle_group;
			$data['vehicle_image']     = $vehicle_image;
		}
		$data['vehicle_company']    = $vehicle_company;
		$data['vehicle_no']         = $vehicle_nofix;
		$data['vehicle_no_bk']      = $vehicle_no_bk_fix;
		$data['vehicle_name']       = $vehicle_name;
		$data['vehicle_maxspeed']   = $vehicle_maxspeed;
		$data['vehicle_maxparking'] = $vehicle_maxparking;
		$data['vehicle_odometer']   = $vehicle_odometer;

		$branchoffice                = isset($_POST['branchoffice']) ? $_POST['branchoffice'] :                       0;
		$subbranchoffice             = isset($_POST['subbranchoffice']) ? $_POST['subbranchoffice'] :                 0;
		$customer                    = isset($_POST['customer']) ? $_POST['customer'] :                               0;
		$subcustomer                 = isset($_POST['subcustomer']) ? $_POST['subcustomer'] :                         0;

		$cur_branchoffice_old        = isset($_POST['cur_branchoffice_id']) ? $_POST['cur_branchoffice_id'] :       0;
		$cur_subbranchoffice_old     = isset($_POST['cur_subbranchoffice_id']) ? $_POST['cur_subbranchoffice_id'] : 0;
		$cur_customer_old            = isset($_POST['cur_customer_id']) ? $_POST['cur_customer_id'] :               0;
		$cur_subcustomer_old         = isset($_POST['cur_subcustomer_id']) ? $_POST['cur_subcustomer_id'] :         0;

		$branchofficefixforupdate    = "";
		$subbranchofficefixforupdate = "";
		$customerfixforupdate        = "";
		$subcustomerfixforupdate     = "";

		// JIKA BRANCH OFFICE SAMA DGN YG LAMA
		if ($branchoffice == $cur_branchoffice_old || $branchoffice == "") {
			$branchofficefixforupdate = $cur_branchoffice_old;
		} else {
			$branchofficefixforupdate = $branchoffice;
		}

		// JIKA SUB BRANCH OFFICE SAMA DGN YG LAMA
		if ($subbranchoffice == $cur_subbranchoffice_old || $subbranchoffice == 0) {
			$subbranchofficefixforupdate = $cur_subbranchoffice_old;
		} else {
			$subbranchofficefixforupdate = $subbranchoffice;
		}

		// JIKA CUSTOMER SAMA DGN YG LAMA
		if ($customer == $cur_customer_old || $customer == 0) {
			$customerfixforupdate = $cur_customer_old;
		} else {
			$customerfixforupdate = $customer;
		}

		// JIKA SUB CUSTOMER SAMA DGN YG LAMA
		if ($subcustomer == $cur_subcustomer_old || $subcustomer == 0) {
			$subcustomerfixforupdate = $cur_subcustomer_old;
		} else {
			$subcustomerfixforupdate = $subcustomer;
		}

		if ($subbranchoffice == "empty") {
			// INPUT USER BRANCH OFFICE
			$data['vehicle_company']    = $branchofficefixforupdate;
			$data['vehicle_subcompany'] = 0;
			$data['vehicle_group']      = 0;
			$data['vehicle_subgroup']   = 0;
		} elseif ($customer == "empty") {
			// INPUT USER CUSTOMER
			$data['vehicle_company']    = $branchofficefixforupdate;
			$data['vehicle_subcompany'] = $subbranchofficefixforupdate;
			$data['vehicle_group']      = 0;
			$data['vehicle_subgroup']   = 0;
		} else {
			// INPUT USER SUB CUSTOMER
			$data['vehicle_company']    = $branchofficefixforupdate;
			$data['vehicle_subcompany'] = $subbranchofficefixforupdate;
			$data['vehicle_group']      = $customerfixforupdate;
			if ($subcustomer == "empty") {
				$data['vehicle_subgroup']   = 0;
			} else {
				$data['vehicle_subgroup']   = $subcustomerfixforupdate;
			}
		}

		// echo "<pre>";
		// var_dump($data['vehicle_subgroup']);die();
		// echo "<pre>";

		$this->db->where("vehicle_id", $vehicle_id);
		$this->db->update("vehicle", $data);

		//UPdate Driver
		$app_route = $this->config->item("app_route");
		if (isset($app_route) && $app_route == 1) {
		} else {
			$driver_update = $this->update_driver($vehicle_id, $driver_id);
			//Add History
			if ($driver_id != 0) {
				$history_driver = $this->driver_history($vehicle_id, $vehicle_name, $vehicle_no, $driver_id);
			}
		}

		$this->db->cache_delete_all();

		$callback['message'] = $this->lang->line('lvehicle_updated');
		$callback['error']   = false;
		echo json_encode($callback);
	}

	function driver_history($vehicle_id, $vehicle_name, $vehicle_no, $driver_id)
	{
		$this->dbtransporter = $this->load->database("transporter", true);
		$date_hist = date("d-m-Y H:i:s");
		unset($data);
		$data['driver_hist_company'] = $this->sess->user_company;
		$data['driver_hist_vehicle'] = $vehicle_id;
		$data['driver_hist_vehicle_name'] = $vehicle_name;
		$data['driver_hist_vehicle_no'] = $vehicle_no;
		$data['driver_hist_driver'] = $driver_id;
		$data['driver_hist_date'] = $date_hist;
		$this->dbtransporter->insert("hist_driver", $data);
		$this->dbtransporter->close();
	}

	function cek_booking_id($v)
	{
		$my_r = "";
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where("booking_vehicle", $v);
		$this->dbtransporter->where("booking_status", 1);
		$this->dbtransporter->where("booking_delivery_status", 1);
		$qb = $this->dbtransporter->get("id_booking");
		$rb = $qb->result();
		$tb = count($rb);
		if ($tb > 0) {
			$my_r = "true";
		} else {
			$my_r = "false";
		}
		return $my_r;
	}

	function getimage()
	{
		$images = array_keys($this->config->item('vehicle_image'));

		$vimage = isset($_POST['vimage']) ? trim($_POST['vimage']) : $images[0];

		if (!$vimage) {
			$callback['message'] = 'Access denied';
			$callback['error'] = true;

			echo json_encode($callback);
			return;
		}

		$folder = BASEPATH . "../assets/images/" . $vimage;

		if (!is_dir($folder)) {
			$callback['message'] = 'Access denied';
			$callback['error'] = true;

			echo json_encode($callback);
			return;
		}

		$this->params['vimage'] = $vimage;

		$callback['html'] = $this->load->view("vehicle/image", $this->params, true);
		$callback['error'] = false;

		echo json_encode($callback);
	}

	function update_driver($vehicle_id, $driver_id)
	{
		$this->dbtransporter = $this->load->database("transporter", true);

		//unset($driver_update);

		if ($driver_id == 0) {

			$driver_update['driver_vehicle'] = 0;
			$this->dbtransporter->where("driver_vehicle", $vehicle_id);
			$this->dbtransporter->update('driver', $driver_update);
		} else {

			$driver_update['driver_vehicle'] = $vehicle_id;
			$this->dbtransporter->where("driver_id", $driver_id);
			$this->dbtransporter->update('driver', $driver_update);
		}

		$this->dbtransporter->close();
	}

	function deleteworkshop()
	{
		$iddelete = $this->input->post('iddelete');
		$data["workshop_status"] = 2;

		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where("workshop_id", $iddelete);
		$q = $this->dbtransporter->update('workshop', $data);

		if ($q) {
			$this->session->set_flashdata('notif', 'Data successfully deleted');
			redirect('vehicles/workshop');
		} else {
			$this->session->set_flashdata('notif', 'Data failed deleted');
			redirect('vehicles/workshop');
		}
	}

	function getbranchofficebyid($id)
	{
		$this->db       = $this->load->database('default', true);
		$this->db->where("company_flag", 0);
		$this->db->where("company_id", $id);
		$q             = $this->db->get("company");
		$rows          = $q->result_array();
		return $rows;
	}

	function getsubbranchofficebyid($id)
	{
		$this->db       = $this->load->database('default', true);
		$this->db->where("subcompany_flag", 0);
		$this->db->where("subcompany_id", $id);
		$q             = $this->db->get("subcompany");
		$rows          = $q->result_array();
		return $rows;
	}

	function getcustomerbyid($id)
	{
		$this->db       = $this->load->database('default', true);
		$this->db->where("group_flag", 0);
		$this->db->where("group_id", $id);
		$q             = $this->db->get("group");
		$rows          = $q->result_array();
		return $rows;
	}

	function getsubcustomerbyid($id)
	{
		$this->db       = $this->load->database('default', true);
		$this->db->where("subgroup_flag", 0);
		$this->db->where("subgroup_id", $id);
		$q             = $this->db->get("subgroup");
		$rows          = $q->result_array();
		return $rows;
	}

	function saveserviceworks()
	{
		$serviceworks_vehicle_no              = explode(".", $this->input->post('serviceworks_vehicle_no'));
		$serviceworks_work_agenc_setservicess = $this->input->post('serviceworks_work_agenc_setservicess');
		$serviceworks_service_date            = $this->input->post('serviceworks_service_date');
		$serviceworks_estimateddate_from 			= $this->input->post('serviceworks_estimateddate_from');
		$serviceworks_estimateddate_end 			= $this->input->post('serviceworks_estimateddate_end');
		$estimatedornot 											= $this->input->post('estimatedornot');
		$serviceworks_lastodometer            = $this->input->post('serviceworks_lastodometer');
		$serviceworks_pelaksana               = $this->input->post('serviceworks_pelaksana');
		$serviceworks_biaya                   = $this->input->post('serviceworks_biaya');
		$serviceworks_note                    = $this->input->post('serviceworks_note');

		$singledate = "";
		$datefrom   = "";
		$dateend    = "";
		$flag       = "";

		if ($estimatedornot == 0) {
			$singledate = date("Y-m-d", strtotime($serviceworks_service_date));
			$datefrom   = date("Y-m-d", strtotime($serviceworks_service_date));
			$dateend    = date("Y-m-d", strtotime($serviceworks_service_date));
			$status     = "1"; // COMPLETED
		} else {
			$singledate = date("Y-m-d", strtotime($serviceworks_estimateddate_from));
			$datefrom   = date("Y-m-d", strtotime($serviceworks_estimateddate_from));
			$dateend    = date("Y-m-d", strtotime($serviceworks_estimateddate_end));
			$status     = "0"; // PROCESS
		}

		$vehicle_id     = $serviceworks_vehicle_no[0];
		$vehicle_device = $serviceworks_vehicle_no[1];
		$vehicle_no     = $serviceworks_vehicle_no[2];
		$vehicle_name   = $serviceworks_vehicle_no[3];
		$user_company   = $this->sess->user_company;

		$data = array(
			"servicess_tipeservice"           => "4",
			"servicess_vehicle_device"        => $vehicle_device,
			"servicess_name"                  => "UNSCHEDULED SERVICE",
			"servicess_vehicle_no"            => $vehicle_no,
			"servicess_vehicle_name"          => $vehicle_name,
			"servicess_nol"                   => $serviceworks_lastodometer,
			"servicess_date"                  => $singledate,
			"servicess_estimateddate_from"    => $datefrom,
			"servicess_estimateddate_end"     => $dateend,
			"servicess_pelaksana"             => $serviceworks_pelaksana,
			"servicess_biaya"                 => $serviceworks_biaya,
			"servicess_note"                  => $serviceworks_note,
			"servicess_work_agencies"         => $serviceworks_work_agenc_setservicess,
			"servicess_flag"                  => 0,
			"servicess_status"                => $status,
			"servicess_user_company"          => $user_company,
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";
		$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
		if ($insert) {
			$status = "success";
		} else {
			$status = "failed";
		}
		// $callback['data']   = $data;
		$callback['status'] = $status;
		$callback['msg']    = "Unscheduled Service Inserted";
		echo json_encode($callback);
	}

	function changestatusunscheduledservice()
	{
		$id   = $_POST["idscheduledservice"];
		$data = array(
			"servicess_status" => 1
		);
		// echo "<pre>";
		// var_dump($id);die();
		// echo "<pre>";
		$update = $this->m_maintenance->updatethisdata("servicess_history", "servicess_id", $id, $data);
		if ($update) {
			$status = "success";
		} else {
			$status = "failed";
		}

		$callback['status'] = $status;
		$callback['msg']    = "Status Now Completed";
		echo json_encode($callback);
	}

	function deleteunscheduledservice()
	{
		$id   = $_POST["idscheduledservice"];
		$data = array(
			"servicess_flag" => 1
		);
		// echo "<pre>";
		// var_dump($id);die();
		// echo "<pre>";
		$update = $this->m_maintenance->updatethisdata("servicess_history", "servicess_id", $id, $data);
		if ($update) {
			$status = "success";
		} else {
			$status = "failed";
		}

		$callback['status'] = $status;
		$callback['msg']    = "Data Successfully Deleted";
		echo json_encode($callback);
	}

	function mdtimei()
	{
		$vehicleids          = $this->vehiclemodel->getVehicleIds();
		$vid                 = isset($_POST['id']) ? $_POST['id'] : "";

		$getmdtimei 				 = $this->m_maintenance->getmdtnya($vid);

		$params['vehicleid'] = $vid;
		$params['mdtnow']    = $getmdtimei;

		// echo "<pre>";
		// var_dump($params['vehicleid']);die();
		// echo "<pre>";

		$html = $this->load->view("dashboard/vehicles/v_vehiclemdtform", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
	}

	function savemdt()
	{
		$vehicle_id  = $_POST['vehicle_id'];
		$vehicle_mdt = $_POST['vehicle_mdt'];

		$data = array(
			"vehicle_mdt" => $vehicle_mdt
		);

		$update              = $this->m_maintenance->updatemdt("vehicle", $data, $vehicle_id);
		if ($update) {
			$callback['message'] = "Success update MDT Imei";
			$callback['error']   = false;
		} else {
			$callback['message'] = "Failed update MDT Imei";
			$callback['error']   = true;
		}
		echo json_encode($callback);
	}

	function detail($vehicleID)
	{
		// $vehicleID = "72150903";

		$vehicledata = $this->vehiclemodel->getVehicleByID($vehicleID);

		// echo "<pre>";
		// var_dump($vehicledata);die();
		// echo "<pre>";

		$this->params['vehicledata'] 		 = $vehicledata;

		$this->params['code_view_menu'] = "configuration";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/vehicles/v_detail_vehicles', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function updateDetailTruck()
	{
		$integrationwim_id                            = $this->input->post('integrationwim_id');
		$detail_integrationwim_grossmanual            = $this->input->post('detail_integrationwim_grossmanual');
		$detail_integrationwim_taremanual             = $this->input->post('detail_integrationwim_taremanual');
		$detail_integrationwim_nettomanual            = $this->input->post('detail_integrationwim_nettomanual');
		$detail_dumping                               = explode('|', $this->input->post('detail_dumping'));
		$detail_material                              = explode('|', $this->input->post('detail_material'));
		$detail_client                                = explode('|', $this->input->post('detail_client'));
		$integrationwim_other_text1                   = $this->input->post('integrationwim_other_text1');
		$integrationwim_other_text2                   = $this->input->post('integrationwim_other_text2');
		$detail_status                                = $this->input->post('detail_status');

		$data = array(
			"integrationwim_gross_manual"      => $detail_integrationwim_grossmanual,
			"integrationwim_tare_manual"       => $detail_integrationwim_taremanual,
			"integrationwim_netto_manual"      => $detail_integrationwim_nettomanual,
			"integrationwim_material_id"       => $detail_material[0],
			"integrationwim_material_name"     => $detail_material[1],
			"integrationwim_client_id"         => $detail_client[0],
			"integrationwim_client_name"       => $detail_client[1],
			"integrationwim_operator_status"   => 1,
			"integrationwim_operator_datetime" => date("Y-m-d H:i:s"),
			"integrationwim_operator_user_id"  => $this->sess->user_id,
			"integrationwim_dumping_id"        => $detail_dumping[0],
			"integrationwim_dumping_name"      => $detail_dumping[1],
			"integrationwim_other_text1"       => $integrationwim_other_text1,
			"integrationwim_other_text2"       => $integrationwim_other_text2,
			"integrationwim_approval_status"   => $detail_status,
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$update = $this->m_wimreport->updatedatawim("tensor_report", "historikal_integrationwim_unit", $integrationwim_id, $data);
		if ($update) {
			echo json_encode(array("msg" => "success", "code" => 200));
		} else {
			echo json_encode(array("msg" => "failed", "code" => 400));
		}
	}

	function share()
	{
		$datavehicle   = $this->m_maintenance->getAllVehicle();
		$company       = $this->dashboardmodel->get_all_company();
		$privilegecode = $this->sess->user_id_role;

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";

		$this->params['code_view_menu'] = "configuration";
		$this->params['privilegecode']  = $privilegecode;
		$this->params['datavehicle']    = $datavehicle;
		$this->params['company']        = $company;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/vehicles/sharevehicle/v_home_share', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
	}

	function share_process()
	{
		$share_type       = $this->input->post("share_type");
		$dataForShare     = $this->input->post("vehicle_for_share");
		$vehicle_sharedto = $this->input->post("vehicle_sharedto");

		if ($share_type == 0) {
			echo json_encode(array("code" => 400, "msg" => "Please select type for share vehicle"));
			return false;
		}

		if ($vehicle_sharedto == 0) {
			echo json_encode(array("code" => 400, "msg" => "Please select contractor for shared to"));
			return false;
		}

		$data_request = array(
			"vehicle_share_contractorid" => $this->sess->user_company,
			"vehicle_share_sharedto"     => $vehicle_sharedto,
			"vehicle_share_type"         => $share_type,
			"vehicle_share_datajson"     => json_encode($dataForShare),
			"vehicle_share_submit"       => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data_request);die();
		// echo "<pre>";

		$insert = $this->m_maintenance->insertDataUmum("ts_vehicle_share", $data_request, "webtracking_ts");
		if ($insert) {
			echo json_encode(array("code" => 200, "msg" => "success"));
			$contentlog = "Vehicle Share Successfuly Sent to BIB";
			$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
		} else {
			echo json_encode(array("code" => 400, "msg" => "failed"));
			$contentlog = "Vehicle Share Failed Sent to BIB";
			$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
		}
	}

	function requestshare()
	{
		$requestshare  = $this->m_maintenance->getAllShareRequest();
		$company       = $this->dashboardmodel->get_all_company();
		$privilegecode = $this->sess->user_id_role;

		// echo "<pre>";
		// var_dump($requestshare);die();
		// echo "<pre>";

		$this->params['code_view_menu'] = "configuration";
		$this->params['privilegecode']  = $privilegecode;
		$this->params['company']        = $company;
		$this->params['requestshare']   = $requestshare;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/vehicles/sharevehicle/v_home_sharerequest', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
	}

	function getDetailData()
	{
		$idrequest           = $_POST['idrequest'];
		$data                = $this->m_maintenance->getDataRequestShare($idrequest);
		$share_type 				 = $data[0]['vehicle_share_type'];
		$companyShareRequest = $this->dashboardmodel->getcompany_id($data[0]['vehicle_share_contractorid']);
		$companySharedTo 		 = $this->dashboardmodel->getcompany_id($data[0]['vehicle_share_sharedto']);
		$datavehicle         = json_decode($data[0]['vehicle_share_datajson']);
		$datafix 		         = array();

		for ($i = 0; $i < sizeof($datavehicle); $i++) {
			$getdatafromdb = $this->m_maintenance->getallvehicleByID($datavehicle[$i]);

			array_push($datafix, array(
				"vehicle_no"     => $getdatafromdb[0]['vehicle_no'],
				"vehicle_name"   => $getdatafromdb[0]['vehicle_name'],
				"vehicle_device" => $getdatafromdb[0]['vehicle_device'],
			));
		}

		// echo "<pre>";
		// var_dump($getdatafromdb);die();
		// echo "<pre>";

		echo json_encode(array("code" => 200, "data" => $datafix, "requestfrom" => $companyShareRequest, "sharedto" => $companySharedTo, "share_type" => $share_type, "idrequest" => $idrequest));
	}

	function bib_share_process(){
		$requestAction           = $this->input->post("requestAction");
		$share_to                = $this->input->post("share_to");
		$id_request              = $this->input->post("id_request");
		$vehicle_shared_approved = $this->input->post("vehicle_shared_approved");

		if ($requestAction == "approved") {
			for ($i=0; $i < sizeof($vehicle_shared_approved); $i++) {
				$vehicle_device = $vehicle_shared_approved[$i];
					$data = array(
						"vehicle_is_share"       => 1,
						"vehicle_id_shareto"     => $share_to,
					);
					$this->m_maintenance->approve_vehicle_share("vehicle", "vehicle_device", $vehicle_device, $data);
			}

			$data_request = array(
				"vehicle_share_flag"         => 1,
			);

			$this->m_maintenance->update_date_webtrackingts("ts_vehicle_share", "vehicle_share_id", $id_request, $data_request);
			echo json_encode(array("code" => 200, "msg" => "Vehicle share berhasil diproses"));
			$contentlog = "Vehicle Share Approved";
			$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
		}else {
			$data_request = array(
				"vehicle_share_flag"         => 2,
			);

			$this->m_maintenance->update_date_webtrackingts("ts_vehicle_share", "vehicle_share_id", $id_request, $data_request);
			echo json_encode(array("code" => 200, "msg" => "Vehicle share berhasil ditolak"));
			$contentlog = "Vehicle Share Rejected";
			$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
		}
	}





















}
