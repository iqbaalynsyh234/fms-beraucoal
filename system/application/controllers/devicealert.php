<?php
include "base.php";

class Devicealert extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Devicealert()
	{
		parent::Base();
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_poipoolmaster");
		$this->load->model("gpsmodel");
	}

  function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$user_id             = $this->sess->user_id;
		$user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
		$user_parent         = $this->sess->user_parent;
		$user_id_role        = $this->sess->user_id_role;
		$privilegecode 			 = $this->sess->user_id_role;
		$user_dblive 	       = $this->sess->user_dblive;
		$user_id_fix         = $user_id;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_company", $user_company);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$rows_company                   = $this->get_company_bylevel();
		$rows_geofence                  = $this->get_geofence_bydblive($user_dblive);//print_r($rows_geofence);exit();

		$datadevicealert 								= $this->m_poipoolmaster->getalertalias("ts_alertalias");

		// echo "<pre>";
		// var_dump($rows_geofence);die();
		// echo "<pre>";

		$this->params["vehicles"]        = $rows;
		$this->params["rcompany"]        = $rows_company;
		$this->params["rgeofence"]       = $rows_geofence;
		$this->params["devicealertlist"] = $datadevicealert;
		$this->params['code_view_menu']  = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);


		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/devicealert/v_home_devicealert', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/devicealert/v_home_devicealert', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/devicealert/v_home_devicealert', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/devicealert/v_home_devicealert', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/devicealert/v_home_devicealert', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/devicealert/v_home_devicealert', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else{
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/devicealert/v_home_devicealert', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

	function searchthisalert(){
		ini_set('display_errors', 1);
		//ini_set('memory_limit', '2G');
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$vehicle    = $this->input->post("vehicle");
		$startdate  = $this->input->post("startdate");
		$enddate    = $this->input->post("enddate");
		$shour      = $this->input->post("shour");
		$ehour      = $this->input->post("ehour");
		$alertype   = $this->input->post("alertype");
		$periode   = $this->input->post("periode");

		$nowdate    = date("Y-m-d");
		$nowday     = date("d");
		$nowmonth   = date("m");
		$nowyear    = date("Y");
		$lastday    = date("t");

		if($periode == "custom"){
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}else if($periode == "yesterday"){

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));

		}else if($periode == "last7"){
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if($nowday <= 7){
				$firstday = 1;
			}

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59"));
		}
		else if($periode == "last30"){
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59"));
		}
		else{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}

		// print_r($sdate." ".$edate);exit();

		$m1      = date("F", strtotime($sdate));
		$m2      = date("F", strtotime($edate));
		$year    = date("Y", strtotime($sdate));
		$year2   = date("Y", strtotime($edate));
		$rows    = array();
		$total_q = 0;

		$error = "";
		$rows_summary = "";

		if ($vehicle == "0")
		{
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		}
		if ($m1 != $m2)
		{
			$error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
		}

		if ($year != $year2)
		{
			$error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
		}

		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$datadevicealertfull 						 = $this->m_poipoolmaster->getalertalias("ts_alertalias");
		$datadevicealert 								 = $this->m_poipoolmaster->getalertaliasnameonly("ts_alertalias");
		$alerttypefix                    = array_map('current', $datadevicealert);
		$getalert                        = $this->m_poipoolmaster->getalertnow("webtracking_gps_temanindobara_live_2", "gps_alert", $vehicle, $alertype, $sdate, $edate, $alerttypefix);
		$datafix = array();

		// echo "<pre>";
		// var_dump($alerttypefix);die();
		// echo "<pre>";
		// $vehicle.'-'. $alertype.'-'. $sdate.'-'. $edate.'-'. $alerttypefix
		// echo "<pre>";
		// var_dump($getalert);die();
		// echo "<pre>";

			for ($i=0; $i < sizeof($getalert); $i++) {
				$vehicledevice = $getalert[$i]['gps_name'].'@'.$getalert[$i]['gps_host'];
					$vehicledata = $this->m_poipoolmaster->getmastervehiclebydevid($vehicledevice);
					$datagps     = $this->gpsmodel->GetLastInfo($getalert[$i]['gps_name'], $getalert[$i]['gps_host'], true, false, date("Y-m-d H:i:s", strtotime('+7 hours', strtotime($getalert[$i]['gps_time']))), $vehicledata[0]['vehicle_type']);

					array_push($datafix, array(
						"vehicle_no"         => $vehicledata[0]['vehicle_no'],
						"vehicle_name"       => $vehicledata[0]['vehicle_name'],
						"gps_alert"          => $getalert[$i]['gps_alert'],
						"gps_time"           => date("Y-m-d H:i:s", strtotime('+7 hours', strtotime($getalert[$i]['gps_time']))),
						"gps_latitude_real"  => $getalert[$i]['gps_latitude_real'],
						"gps_longitude_real" => $getalert[$i]['gps_longitude_real'],
						"gps_speed"          => $getalert[$i]['gps_speed'],
						"gps_address"        => $datagps->georeverse->display_name,
					));
			}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

		$this->params['data'] 			           = $datafix;
		$this->params['startdate'] 			       = $sdate;
		$this->params['enddate'] 			         = $edate;
		$this->params['datadevicealert'] 			 = $datadevicealertfull;
		$html                                  = $this->load->view('newdashboard/devicealert/v_result_devicealert', $this->params, true);
		$callback['html']                      = $html;
		$callback['devicealert']               = $getalert;
		echo json_encode($callback);
	}

  function getallalert(){
    $getdatanya 		     = $this->dashboardmodel->get_devicealert_newmethod();

		$user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
		$user_parent         = $this->sess->user_parent;
		$user_id_role = $this->sess->user_id_role;
		$user_id_fix         = $this->sess->user_id;

		//GET DATA FROM DB
		$this->db->select("vehicle_device,vehicle_no,vehicle_name,vehicle_type");
		$this->db->order_by("vehicle_no","asc");

		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}
		$this->db->where("vehicle_device", $getdatanya[0]['gps_name'].'@'.$getdatanya[0]['gps_host']);
		$this->db->where("vehicle_status <>", 3);
		$q             = $this->db->get("vehicle");
		$mastervehicle = $q->result_array();

		// echo "<pre>";
		// var_dump($mastervehicle);die();
		// echo "<pre>";

		$datafixbgt = array();
		for ($i=0; $i < sizeof($getdatanya); $i++) {
			$position        = $this->gpsmodel->GeoReverse($getdatanya[$i]['vehicle_lat'], $getdatanya[$i]['vehicle_lng']);
			$positionexplode = explode(",", $position->display_name);
			array_push($datafixbgt, array(
			 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
			 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
			 "vehicle_alert"          => $getdatanya[0]['vehicle_alert'],
			 "vehicle_lat"            => $getdatanya[0]['vehicle_lat'],
			 "vehicle_lng"            => $getdatanya[0]['vehicle_lng'],
			 "position"            		=> $positionexplode[0],
			 "gps_geofence"           => $getdatanya[0]['gps_geofence'],
			 "gps_speed_limit"        => $getdatanya[0]['gps_speed_limit'],
			 "gps_speed_status"       => $getdatanya[0]['gps_speed_status'],
			 "gps_speed"              => number_format($getdatanya[0]['gps_speed']*1.852, 0, "","."),
			 "vehicle_alert_datetime" => date("Y-m-d H:i:s", strtotime('+7 hours', strtotime($getdatanya[0]['gps_time'])))
			));
		}

    // echo "<pre>";
    // var_dump($datafixbgt);die();
    // echo "<pre>";

    if (sizeof($getdatanya) > 0) {
      echo json_encode(array("code" => "200", "data" => $datafixbgt));
    }else {
      echo json_encode(array("code" => "400", "data" => "empty"));
    }
  }

  function listalert(){
    $this->params['title']     = "";

    $getdatanya = $this->dashboardmodel->get_devicealert();


		$datafixbgt = array();
		for ($i=0; $i < sizeof($getdatanya); $i++) {
			$lastinfofix = $this->gpsmodel->GeoReverse($getdatanya[$i]['vehicle_lat'], $getdatanya[$i]['vehicle_lng']);
			array_push($datafixbgt, array(
			 "vehicle_no"             => $getdatanya[$i]['vehicle_no'],
			 "vehicle_name"           => $getdatanya[$i]['vehicle_name'],
			 "vehicle_alert"          => $getdatanya[$i]['vehicle_alert'],
			 "vehicle_device"         => $getdatanya[$i]['vehicle_device'],
			 "vehicle_lat"            => $getdatanya[$i]['vehicle_lat'],
			 "vehicle_lng"            => $getdatanya[$i]['vehicle_lng'],
			 "address"                => $lastinfofix->display_name,
			 "gps_alert"              => $getdatanya[$i]['gps_alert'],
			 "gps_status"             => $getdatanya[$i]['gps_status'],
			 "gps_speed"              => $getdatanya[$i]['gps_speed'],
			 "vehicle_alert_datetime" => date("d-m-Y H:i:s", strtotime($getdatanya[$i]['vehicle_alert_datetime']) + 420*60)
			));
		}

		// echo "<pre>";
		// var_dump($datafixbgt);die();
		// echo "<pre>";

    $this->params['devicealert']     = $datafixbgt;
		$this->params['code_view_menu']  = "report";

		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/trackers/list_alert', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
  }

	function listdevicealert(){
		$dblive                   = $this->sess->user_dblive;
    $this->params['title']  = "";

		$rows                     = $this->dashboardmodel->getvehicle_report();
		$this->params["geofence"] = $this->dashboardmodel->getallgeofence($dblive);

		$this->params["vehicle"]       = $rows;
		$this->params['code_view_menu'] = "report";

		// echo "<pre>";
		// var_dump($this->params['geofence']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/trackers/list_device_alert', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
  }

  function clearnotif(){
    $data = array(
      "gps_view" => 1
    );
    $changeviewedalert = $this->dashboardmodel->update_data("webtracking_gps_alert", $data);
    if ($changeviewedalert) {
      echo json_encode(array("code" => 200));
    }else {
      echo json_encode(array("code" => 400));
    }
  }

	function searchreport(){
		$vehicle             = $this->input->post('vehicle');
		$shour               = str_replace(" ", "", $this->input->post('shour'));
		$ehour               = str_replace(" ", "", $this->input->post('ehour'));
		$jalurmuatankosongan = $this->input->post('jalurmuatankosongan');
		$geofencefilter      = $this->input->post('geofencefilter');


		$sdate               = $this->input->post('sdate')." ".$shour.":00";
		$enddate             = $this->input->post('enddate')." ".$ehour.":00";
		$sdatefix            = date("Y-m-d H:i:s", strtotime($sdate) - 420*60);
		$enddatefix          = date("Y-m-d H:i:s", strtotime($enddate) - 420*60);
	  $devicealert        = $this->dashboardmodel->searchforreport("webtracking_gps_alert", $vehicle, $sdatefix, $enddatefix, $jalurmuatankosongan, $geofencefilter);
		// echo "<pre>";
		// var_dump($devicealert);die();
		// echo "<pre>";
		$datafixbgt   = array();
			if (sizeof($devicealert) > 0) {
				for ($i=0; $i < sizeof($devicealert); $i++) {
					if ($vehicle == "All") {
						$lastinfofix = $this->gpsmodel->GeoReverse($devicealert[$i]['vehicle_lat'], $devicealert[$i]['vehicle_lng']);
						array_push($datafixbgt, array(
						 "vehicle_no"              => $devicealert[$i]['vehicle_no'],
						 "vehicle_name"            => $devicealert[$i]['vehicle_name'],
						 "vehicle_device"          => $devicealert[$i]['vehicle_device'],
						 "vehicle_lat"             => $devicealert[$i]['vehicle_lat'],
						 "vehicle_lng"             => $devicealert[$i]['vehicle_lng'],
						 "address"                 => $lastinfofix->display_name,
						 "gps_alert"               => $devicealert[$i]['gps_alert'],
						 "gps_status"              => $devicealert[$i]['gps_status'],
						 "gps_speed"               => $devicealert[$i]['gps_speed'],
						 "gps_geofence"            => $devicealert[$i]['gps_geofence'],
						 "gps_speed_limit"         => $devicealert[$i]['gps_speed_limit'],
						 "gps_speed_status"        => $devicealert[$i]['gps_speed_status'],
						 "gps_last_road_type"      => $devicealert[$i]['gps_last_road_type'],
						 "vehicle_alert_datetime"  => date("d-m-Y H:i:s", strtotime($devicealert[$i]['vehicle_alert_datetime']) + 420*60)
						));
					}else {
						if ($devicealert[$i]['vehicle_device'] == $vehicle) {
							$sikon = 1;
							$lastinfofix = $this->gpsmodel->GeoReverse($devicealert[$i]['vehicle_lat'], $devicealert[$i]['vehicle_lng']);
							array_push($datafixbgt, array(
							 "vehicle_no"              => $devicealert[$i]['vehicle_no'],
		 					 "vehicle_name"            => $devicealert[$i]['vehicle_name'],
		 					 "vehicle_device"          => $devicealert[$i]['vehicle_device'],
							 "vehicle_lat"             => $devicealert[$i]['vehicle_lat'],
							 "vehicle_lng"             => $devicealert[$i]['vehicle_lng'],
							 "address"                 => $lastinfofix->display_name,
		 					 "gps_alert"               => $devicealert[$i]['gps_alert'],
							 "gps_status"              => $devicealert[$i]['gps_status'],
							 "gps_speed"               => $devicealert[$i]['gps_speed'],
							 "gps_geofence"            => $devicealert[$i]['gps_geofence'],
							 "gps_speed_limit"         => $devicealert[$i]['gps_speed_limit'],
							 "gps_speed_status"        => $devicealert[$i]['gps_speed_status'],
							 "gps_last_road_type"      => $devicealert[$i]['gps_last_road_type'],
		 					 "vehicle_alert_datetime" => date("d-m-Y H:i:s", strtotime($devicealert[$i]['vehicle_alert_datetime']) + 420*60)
							));
						}
					}
				}
			}
		// echo "<pre>";
		// var_dump($datafixbgt);die();
		// echo "<pre>";
		$this->params['devicealert']   = $datafixbgt;
		$html                          = $this->load->view('dashboard/trackers/list_devicealert_result', $this->params, true);
		$callback['html']              = $html;
		$callback['devicealert']              = $datafixbgt;
		echo json_encode($callback);
	}

	function get_gpsalert($device,$host,$type){
		$this->dbalert = $this->load->database($this->sess->user_dblive, true);

		$table_alert = "webtracking_gps_alert";
		$this->dbalert->where("gps_name", $device);
		$this->dbalert->where("gps_host", $host);
		$this->dbalert->where("gps_notif", 0);
		$this->dbalert->where("gps_view", 0);
		$qalert      = $this->dbalert->get($table_alert);
		$rowsalert   = $qalert->result_array();

		return $rowsalert;
	}

	function get_company_bylevel(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$user_id             = $this->sess->user_id;
		$user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
		$user_parent         = $this->sess->user_parent;
		$user_id_role        = $this->sess->user_id_role;
		$privilegecode 			 = $this->sess->user_id_role;
		$user_dblive 	       = $this->sess->user_dblive;
		$user_id_fix         = $user_id;

		$this->db->order_by("company_name","asc");
		if($user_id_role == 0){
			$this->db->where("company_created_by", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("company_created_by", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("company_created_by", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("company_created_by", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("company_created_by", 4408);
		}else if($user_id_role == 5){
			$this->db->where("company_created_by", $user_company);
		}else if($user_id_role == 6){
			$this->db->where("company_created_by", $user_company);
		}else{
			$this->db->where("company_created_by",99999);
		}

		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}

	function get_geofence_bydblive($dblive){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->dblive = $this->load->database($dblive,true);
		$this->dblive->select("geofence_name");
		$this->dblive->order_by("geofence_name","asc");
		$this->dblive->where("geofence_user", 4203); //khusus bib
		$this->dblive->where("geofence_status", 1);
		$this->dblive->where("geofence_type", "road");
		$qd = $this->dblive->get("geofence");
		$rd = $qd->result();

		return $rd;
	}

}
