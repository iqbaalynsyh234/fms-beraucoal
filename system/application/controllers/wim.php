<?php
include "base.php";

class Wim extends Base {

	function Wim()
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
		$this->load->model("m_wimreport");
		$this->load->model("m_masterdata");

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

	}

	function index()
	{
		ini_set('display_errors', 1);

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
		$user_id_fix     = 4408;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($privilegecode == 0) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		} else if ($privilegecode == 1) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 2) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 3) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 4) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 5) {
			$this->db->where("vehicle_company", $user_company);
		} else if ($privilegecode == 6) {
			$this->db->where("vehicle_company", $user_company);
		} else if ($privilegecode == 7) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		} else if ($privilegecode == 8) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else {
			$this->db->where("vehicle_no", 99999);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
		}

		$rows = $q->result();
		$rows_company = $this->get_company_bylevel();

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;

		//$this->params["data"] 		    = $result;
		$this->params['code_view_menu'] = "wimmenu";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/v_wim_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}elseif ($privilegecode == 8) {
			$this->operatoritws();
			// $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
			// $this->params["content"]        = $this->load->view('newdashboard/wim/operator/v_wim_list', $this->params, true);
			// $this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/v_wim_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function search_replacementreport(){
		$company          = $this->input->post("company");
		$vehicle          = $this->input->post("vehicle");
		$startdate        = $this->input->post("startdate");
		$shour            = "00:00:00";
		$enddate          = $this->input->post("enddate");
		$ehour            = "23:59:59";
		$periode          = $this->input->post("periode");

		// echo "<pre>";
		// var_dump($transactionidfix);die();
		// echo "<pre>";
		$sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
		$edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

		$nowdate   = date("Y-m-d");
		$nowday    = date("d");
		$nowmonth  = date("m");
		$nowyear   = date("Y");
		$lastday   = date("t");

		if($periode == "custom"){
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}else if($periode == "yesterday"){
			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		}else if($periode == "last7"){
			/* $nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if($nowday <= 7){
				$firstday = 1;
			}
	*/
			/* $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59")); */

			$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-7days"));
			$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

		}else if($periode == "last30"){
			/* $firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59")); */

			$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-30days"));
			$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

		}else if($periode == "today"){
			$sdate1 = date("Y-m-d");
			$sdate2 = "00:00:00";

			$edate1 = date("Y-m-d");
			$edate2 = "23:59:59";

			$sdate = $sdate1." ".$sdate2;
			$edate = $edate1." ".$edate2;
		}else{

			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}

		$data_replacement = $this->m_wimreport->dataReplacementReport("historikal_replacement_wim", $company, $vehicle, $sdate, $edate);

		$this->params['data'] = $data_replacement;

		// echo "<pre>";
		// var_dump($data_replacement);die();
		// echo "<pre>";

		$html = $this->load->view("newdashboard/wim/v_wim_replacement_reportresult", $this->params, true);
		$callback['error'] = false;
		$callback['html']  = $html;
		$callback['data']  = $data_replacement;
		echo json_encode($callback);
	}

	function get_vehicle()
	{
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_company", $this->sess->user_company);
		$qvehicle = $this->db->get("vehicle");
		$row_vehicle = $qvehicle->result();
		return $row_vehicle;
	}

	function workshop(){
		$this->params['title']          = "Workshop List";
		$user_company                   = $this->sess->user_company;
		$this->dbtransporter            = $this->load->database("transporter", true);
		$this->dbtransporter->where("workshop_company", $user_company);
		$this->dbtransporter->where("workshop_status", 1);
		$q                              = $this->dbtransporter->get("workshop");
		$this->params['workshop']       = $q->result_array();
		$this->params['code_view_menu'] = "configuration";
		// echo "<pre>";
		// var_dump($workshop);die();
		// echo "<pre>";
		// $this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		// $this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		// $this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
		// $this->params["content"]        = $this->load->view('dashboard/workshop/v_workshop', $this->params, true);
		// $this->load->view("dashboard/template_dashboard_report", $this->params);

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/workshop/v_workshop', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function save_workshop()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company          = $this->sess->user_company;
		unset($data);

		$workshop_name            = isset($_POST['workshop_name']) ? $_POST['workshop_name']:       "";
		$workshop_telp            = isset($_POST['workshop_telp']) ? $_POST['workshop_telp']:       "";
		$workshop_fax             = isset($_POST['workshop_fax']) ? $_POST['workshop_fax']:         "";
		$workshop_address         = isset($_POST['workshop_address']) ? $_POST['workshop_address']: "";
		$workshop_company         = $my_company;

		$data['workshop_name']    = $workshop_name;
		$data['workshop_telp']    = $workshop_telp;
		$data['workshop_fax']     = $workshop_fax;
		$data['workshop_address'] = $workshop_address;
		$data['workshop_company'] = $workshop_company;

		//Insert
		$this->dbtransporter->insert('workshop',$data);
		$this->dbtransporter->close();

		$callback['error'] = false;
		$callback['message'] = "Workshop Successfully Submitted";
		$callback['redirect'] = base_url()."vehicles/workshop";

		echo json_encode($callback);
		return;
	}

	function forconfigservicess(){
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
			}else {
				// ADA ISINYA
				$callback['vehicle'] = $result;
				$callback['row']     = $valueafterchcking;
				$callback['data']    = $cekvehiclenonya;
				$callback['isirow']  = $valueafterchcking;
				echo json_encode($callback);
			}
	}

	function savethisconfiguration(){
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
					}else {
						$status = "failed";
					}
				$callback['data']   = $data;
				$callback['status'] = $status;
				$callback['msg']    = "Configuration Inserted";
				echo json_encode($callback);
			}else {
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
					}else {
						$status = "failed";
					}
				$callback['data']   = $data;
				$callback['status'] = $status;
				$callback['msg']    = "Configuration Updated";
				echo json_encode($callback);
			}
	}

	function getfornotif(){
		date_default_timezone_set("Asia/Bangkok");
		$datanotifstnk    = array();
		$datanotifkir     = array();
		$datanotifservice = array();
		$user_company     = $this->sess->user_company;



		// GET STNK EXP DATE
		$getstnkexpdate = $this->m_maintenance->getstnkexpdate("maintenance_configuration", $user_company);
		for ($i=0; $i < sizeof($getstnkexpdate); $i++) {
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
		for ($j=0; $j < sizeof($getkirexpdate); $j++) {
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
		for ($i=0; $i < sizeof($getservicescheduleperkm); $i++) {
			$lasttime           = 0;
			$device             = $getservicescheduleperkm[$i]['maintenance_conf_vehicle_device'];
			$type_gps           = $getservicescheduleperkm[$i]['maintenance_conf_vehicle_type_gps'];
			$arr                = explode("@", $device);
			$devices[0]         = (count($arr) > 0) ? $arr[0]: "";
			$devices[1]         = (count($arr) > 1) ? $arr[1]: "";
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
				"lastodometerfromgps"              => round(($finaldata[$i]['data'][0]['gps_info_distance'])/1000 + $finaldata[$i]['vehicle_odometer'][0]['vehicle_odometer']),
				"maintenance_conf_valueservicedby" => $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby'],
				"maintenance_conf_lastodometer"    => $getservicescheduleperkm[$i]['maintenance_conf_lastodometer'],
				"maintenance_conf_last_service"    => $getservicescheduleperkm[$i]['maintenance_conf_last_service'],
				"finalodometer"                    => round(($getservicescheduleperkm[$i]['maintenance_conf_lastodometer'] + $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby']) - $getservicescheduleperkm[$i]['maintenance_conf_alertlimit']),
			));

			$odometerforservice = "";
			if (round($getservicescheduleperkm[$i]['maintenance_conf_lastodometer']) == "") {
				$odometerforservice = round(($finaldata[$i]['data'][0]['gps_info_distance'])/1000 + $finaldata[$i]['vehicle_odometer'][0]['vehicle_odometer'] +  $getservicescheduleperkm[$i]['maintenance_conf_valueservicedby']);
			}else {
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
					"lastodometerfromgps"   => round(($finaldata[$i]['data'][0]['gps_info_distance'])/1000 + $finaldata[$i]['vehicle_odometer'][0]['vehicle_odometer']),
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
		for ($b=0; $b < $sizepermont; $b++) {
			if (date("Y-m-d") >= date("Y-m-d", strtotime($getserviceschedulepermonth[$b]['maintenance_conf_last_service']."+".$getserviceschedulepermonth[$b]['maintenance_conf_alertlimit']."Month"))) {
				array_push($servicedbymonth, array(
					"kondisi" 	 	 	 => "2",
					"vehicle_no"     => $getserviceschedulepermonth[$b]['maintenance_conf_vehicle_no'],
					"vehicle_name"   => $getserviceschedulepermonth[$b]['maintenance_conf_vehicle_name'],
					"service_setiap" => $getserviceschedulepermonth[$b]['maintenance_conf_valueservicedby'],
					"servicedby"     => $getserviceschedulepermonth[$b]['maintenance_conf_servicedby'],
					"last_service"   => date("Y-m-d", strtotime($getserviceschedulepermonth[$b]['maintenance_conf_last_service'])),
					"next_service"   => date("Y-m-d", strtotime($getserviceschedulepermonth[$b]['maintenance_conf_last_service']."+".$getserviceschedulepermonth[$b]['maintenance_conf_valueservicedby']."Month")),
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

	function forsetservicess(){
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

	function savetomaintenancehistory(){
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
				"servicess_date"           => date("Y-m-d", strtotime($v_kirdate_setservicess))." 00:00:00",
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
						}else {
							$status = "failed";
						}
						$callback['status'] = $status;
						$callback['msg']    = "Data Succesfully Inserted To Servicess History";
						echo json_encode($callback);
				}else {
					$status = "failed";
					$callback['status'] = $status;
					$callback['msg']    = "Data Succesfully Inserted To Servicess History";
					echo json_encode($callback);
				}
		}elseif ($tipeservice == 3) {
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
				"servicess_date"          => date("Y-m-d", strtotime($v_perpstnk_date_setservicess))." 00:00:00",
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
						}else {
							$status = "failed";
						}
						$callback['status'] = $status;
						$callback['msg']    = "Data Succesfully Inserted To Servicess History";
						echo json_encode($callback);
				}else {
					$status = "failed";
					$callback['status'] = $status;
					$callback['msg']    = "Data Succesfully Inserted To Servicess History";
					echo json_encode($callback);
				}
		}else {
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
				"servicess_date"           => date("Y-m-d", strtotime($v_service_date_setservicess))." 00:00:00",
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
					"maintenance_conf_last_service" => date("Y-m-d", strtotime($v_service_date_setservicess))." 00:00:00"
				);
				$update = $this->m_maintenance->updateDatadbtransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $v_service_vehicle_no, $dataforupdate);
					if ($update) {
						$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
							if ($insert) {
								$status = "success";
							}else {
								$status = "failed";
							}
							$callback['status'] = $status;
							$callback['msg']    = "Data Succesfully Inserted To Servicess History";
							echo json_encode($callback);
					}else {
						$status = "failed";
						$callback['status'] = $status;
						$callback['msg']    = "Data Succesfully Inserted To Servicess History";
						echo json_encode($callback);
					}
			}else {
				// ALERT PER KM
				$dataforupdate = array(
					"maintenance_conf_lastodometer" => $v_service_lastodometer,
					"maintenance_conf_last_service" => date("Y-m-d", strtotime($v_service_date_setservicess))." 00:00:00"
				);
				$update = $this->m_maintenance->updateDatadbtransporter("maintenance_configuration", "maintenance_conf_vehicle_no", $v_service_vehicle_no, $dataforupdate);
					if ($update) {
						$insert = $this->m_maintenance->insertDataDbTransporter("servicess_history", $data);
							if ($insert) {
								$status = "success";
							}else {
								$status = "failed";
							}
							$callback['status'] = $status;
							$callback['msg']    = "Data Succesfully Inserted To Servicess History";
							echo json_encode($callback);
					}else {
						$status = "failed";
						$callback['status'] = $status;
						$callback['msg']    = "Data Succesfully Inserted To Servicess History";
						echo json_encode($callback);
					}
			}
	}
}

	function maintenance(){
	$user_level      = $this->sess->user_level;
	$user_company    = $this->sess->user_company;
	$user_subcompany = $this->sess->user_subcompany;
	$user_group      = $this->sess->user_group;
	$user_subgroup   = $this->sess->user_subgroup;

	if($this->sess->user_id == "1445"){
		$user_id = $this->sess->user_id; //tag
	}else{
		$user_id = $this->sess->user_id;
	}

	$user_id_fix     = $user_id;
	//GET DATA FROM DB
	$this->db     = $this->load->database("default", true);
	$this->db->select("*");
	$this->db->order_by("vehicle_name","asc");

	if($user_level == 1){
		$this->db->where("vehicle_user_id", $user_id_fix);
	}else if($user_level == 2){
		$this->db->where("vehicle_company", $user_company);
	}else if($user_level == 3){
		$this->db->where("vehicle_subcompany", $user_subcompany);
	}else if($user_level == 4){
		$this->db->where("vehicle_group", $user_group);
	}else if($user_level == 5){
		$this->db->where("vehicle_subgroup", $user_subgroup);
	}else{
		$this->db->where("vehicle_no",99999);
	}

	$this->db->where("vehicle_status <>", 3);
	$q       = $this->db->get("vehicle");
	$result  = $q->result_array();

	// GET ASSIGNED VEHICLE STATUS
	$this->params["datavehicle"] 				= $result;

	// GET BRANCH
	$this->db->where("company_created_by", $user_id_fix);
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
	$qsubgroup                          = $this->db->get("subgroup");
	$ressubgroup                        = $qsubgroup->result_array();
	$this->params["subgroup"]           = $ressubgroup;
	$this->params["unscheduledservice"] = $this->m_maintenance->getunscheduledservice("servicess_history");

	$getservicetype                  = $this->m_maintenance->gogetservicetype("service_type");
	$resultservicetype               = $getservicetype->result_array();
	$getworkshop                     = $this->m_maintenance->g_all("workshop", "workshop_company", $user_company, "workshop_name", "asc");

	$this->params['workshop']        = $getworkshop;
	$this->params['dataservicetype'] = $resultservicetype;
	$this->params['code_view_menu'] = "configuration";

	// echo "<pre>";
	// var_dump($this->params["workshop"]);die();
	// echo "<pre>";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
	$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance', $this->params, true);
	$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
}

	function maintenancehistory(){
	$getservicetype                 = $this->m_maintenance->gogetservicetype2("service_type");
	$resultservicetype              = $getservicetype->result_array();
	$user_id                        = $this->sess->user_id;

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

	$this->params["header"]          = $this->load->view('dashboard/header', $this->params, true);
	$this->params["sidebar"]         = $this->load->view('dashboard/sidebar', $this->params, true);
	$this->params["chatsidebar"]     = $this->load->view('dashboard/chatsidebar', $this->params, true);
	$this->params["content"]         = $this->load->view('dashboard/vehicles/v_maintenance_history', $this->params, true);
	$this->load->view("dashboard/template_dashboard_report", $this->params);
}

	function showmaintenancehistory(){
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

	function formvehicle(){
	$vehicleids    = $this->vehiclemodel->getVehicleIds();
	$vid           = isset($_POST['id']) ? $_POST['id']:   "";
	$uid           = isset($_POST['uid']) ? $_POST['uid']: "";

	$params['uid'] = $uid;

	if ($vid){
		if ($this->sess->user_type == 2){
			$this->db->where_in("vehicle_id", $vehicleids);
		}
		$this->db->where("vehicle_id", $vid);
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0){
			$callback['error'] = true;
			echo json_encode($callback);
			return;
		}
		$row                         = $q->row();
		$row->vehicle_active_date1_t = dbintmaketime($row->vehicle_active_date1, 0);
		$row->vehicle_active_date2_t = dbintmaketime($row->vehicle_active_date2, 0);
		$row->vehicle_active_date_t  = dbintmaketime($row->vehicle_active_date, 0);

		$json                        = json_decode($row->vehicle_info);
		$row->vehicle_ip             = isset($json->vehicle_ip) ? $json->vehicle_ip: $this->config->item("ip_colo");

		$params['vehicle'] = $row;
		$params['owner']   = $row->vehicle_user_id;
	}else{
		$params['owner']   = $uid;
	}

	if ($this->sess->user_type == 2){
		$this->db->where("user_id", $this->sess->user_id);
	}

	$this->db->order_by("user_name", "asc");
	$q = $this->db->get("user");

	$params["users"] = $q->result();

	//Get Company
	//$this->db->where("company_id", $this->sess->user_company);
	$this->db->where("company_id = '".$this->sess->user_company."' OR company_created_by = '".$this->sess->user_id."'");
	$this->db->order_by("company_name", "asc");
	$q                   = $this->db->get("company");
	$rowcompanies        = $q->result();
	//print_r($rowcompanies);exit;
	$params["companies"] = $rowcompanies;
	$params['selected'] = 0;

	$this->db->distinct();
	$this->db->select("fuel_tank_capacity");
	$qfuel = $this->db->get("fuel");

	if($qfuel->num_rows()>0){
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
	}else {
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
	}else {
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
	}else {
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
	}else {
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

	$html = $this->load->view("dashboard/vehicles/v_formvehicle", $params, true);
	$callback['error'] = false;
	$callback['html'] = $html;
	echo json_encode($callback);
}

	function vfuelcalibration(){
	$vehicleids            = $this->vehiclemodel->getVehicleIds();
	$vid                   = isset($_POST['id']) ? $_POST['id']: "";

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

	function savecalibration(){
	$vehicle_id            = isset($_POST['vehicle_id']) ? $_POST['vehicle_id']                      : "";
	$vehicle_fuel_capacity = isset($_POST['vehicle_fuel_capacity']) ? $_POST['vehicle_fuel_capacity']: "";
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
	}else {
		$callback['error']   = true;
		$callback['message'] = "Failed Submit Fuel Calibration";
		echo json_encode($callback);
	}

}

	function getAllDriver(){
	$this->dbtransporter = $this->load->database('transporter', true);
	$this->dbtransporter->select("*");
	$this->dbtransporter->where("driver_company", $this->sess->user_company);
	$this->dbtransporter->from("driver");
	$qdriver = $this->dbtransporter->get();
	$qrow = $qdriver->result();
	return $qrow;
	$this->dbtransporter->close();
}

	function savevehicle($isman=0)
	{
	$vehicle_id = isset($_POST['vehicle_id']) ? trim($_POST['vehicle_id']) : "";
	// echo "<pre>";
	// var_dump($vehicle_id);die();
	// echo "<pre>";
		$vehicleids = $this->vehiclemodel->getVehicleIds();

		if (! in_array($vehicle_id, $vehicleids))
		{
			redirect(base_url());
		}

	$vehicle_user_id    = isset($_POST['vehicle_user_id']) ? trim($_POST['vehicle_user_id']):       "";
	$vehicle_device     = isset($_POST['vehicle_device']) ? trim($_POST['vehicle_device']):         "";
	$vehicle_type       = isset($_POST['vehicle_type']) ? trim($_POST['vehicle_type']):             "";
	$vehicle_no         = isset($_POST['vehicle_no']) ? trim($_POST['vehicle_no']):                 "";
	$vehicle_name       = isset($_POST['vehicle_name']) ? trim($_POST['vehicle_name']):             "";

	$vehicle_card_no    = isset($_POST['vehicle_card_no']) ? trim($_POST['vehicle_card_no']):       "";
	$vehicle_card_no    = str_replace(" ", "", $vehicle_card_no);

	$vehicle_operator   = isset($_POST['vehicle_operator']) ? trim($_POST['vehicle_operator']):     "";

	$vehicle_maxspeed   = isset($_POST['vehicle_maxspeed']) ? trim($_POST['vehicle_maxspeed']):     "";
	$vehicle_maxspeed   = str_replace(",", ".", $vehicle_maxspeed);

	$vehicle_maxparking = isset($_POST['vehicle_maxparking']) ? trim($_POST['vehicle_maxparking']): "";
	$vehicle_maxparking = str_replace(",", ".", $vehicle_maxparking);

	$vehicle_odometer   = isset($_POST['vehicle_odometer']) ? trim($_POST['vehicle_odometer']):     0;
	$vehicle_odometer   = str_replace(",", ".", $vehicle_odometer);

	$vehicle_image      = isset($_POST['vehicle_image']) ? trim($_POST['vehicle_image']):           "";
	$vehicle_group      = isset($_POST['group']) ? trim($_POST['group']):                           "";
	$vehicle_company    = isset($_POST['usersite']) ? trim($_POST['usersite']):                     0;

	$driver_id          = isset($_POST['driver']) ? trim($_POST['driver']):                         "";

		if (strlen($vehicle_device) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_vehicle_device');
			echo json_encode($callback);
			return;
		}

		if ($vehicle_id)
		{
			$this->db->where("vehicle_id <>", $vehicle_id);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $vehicle_device);
		$total = $this->db->count_all_results("vehicle");

		if ($total)
		{
			/* $callback['error'] = true;
			$callback['message'] = $this->lang->line('lexist_vehicle_device');

			echo json_encode($callback);
			return; */
		}


	if (strlen($vehicle_no) == 0)
	{
		$callback['error'] = true;
		$callback['message'] = $this->lang->line('lempty_vehicle_no');

		echo json_encode($callback);
		return;
	}

	if ($vehicle_id)
	{
		$this->db->where("vehicle_id <>", $vehicle_id);
	}

	$this->db->where("vehicle_status <>", 3);
	$this->db->where("vehicle_no", $vehicle_no);
	$total = $this->db->count_all_results("vehicle");
	if ($total)
	{
		/* $callback['error'] = true;
		$callback['message'] = $this->lang->line('lexist_vehicle_no');

		echo json_encode($callback);
		return; */
	}

	if (strlen($vehicle_name) == 0)
	{
		$callback['error'] = true;
		$callback['message'] = $this->lang->line('lempty_vehicle_name');

		echo json_encode($callback);
		return;
	}

	if (strlen($vehicle_odometer))
	{
		if ((! is_numeric($vehicle_odometer)) || ($vehicle_odometer < 0))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('linvalid_initialodometer');

			echo json_encode($callback);
			return;
		}
	}

	if (strlen($vehicle_maxspeed))
	{
		if ((! is_numeric($vehicle_maxspeed)) || ($vehicle_maxspeed < 0))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('linvalid_maxspeed');

			echo json_encode($callback);
			return;
		}
	}

	if (strlen($vehicle_maxparking))
	{
		if ((! is_numeric($vehicle_maxparking)) || ($vehicle_maxparking < 0))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('linvalid_maxparkingtime');

			echo json_encode($callback);
			return;
		}
	}

		if (strlen($vehicle_card_no) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lvehicle_card_no_empty');

			echo json_encode($callback);
			return;
		}

		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_card_no", $vehicle_card_no);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() > 0)
		{
			$rowsimcard = $q->row();
			if ($rowsimcard->vehicle_id != $vehicle_id)
			{
				/* $callback['error'] = true;
				$callback['message'] = $this->lang->line('lvehicle_card_no_exist');

				echo json_encode($callback);
				return; */
			}
		}

	//Khusus Tupperware Transporter
	if ($this->sess->user_trans_tupper == 1)
	{
		$booking_id = $this->cek_booking_id($vehicle_device);
	}

	unset($data);

		$data['vehicle_status'] = 1;
		if ($this->sess->user_trans_tupper == 1)
		{
			if ($booking_id == "false")
			{
				$data['vehicle_group']    = $vehicle_group;
				$data['vehicle_image']    = $vehicle_image;
			}
		}else{
			$data['vehicle_group']     = $vehicle_group;
			$data['vehicle_image']     = $vehicle_image;
		}
		$data['vehicle_company']    = $vehicle_company;
		$data['vehicle_no']         = $vehicle_no;
		$data['vehicle_name']       = $vehicle_name;
		$data['vehicle_maxspeed']   = $vehicle_maxspeed;
		$data['vehicle_maxparking'] = $vehicle_maxparking;
		$data['vehicle_odometer']   = $vehicle_odometer;

		$branchoffice                = isset($_POST['branchoffice']) ? $_POST['branchoffice']:                       0;
		$subbranchoffice             = isset($_POST['subbranchoffice']) ? $_POST['subbranchoffice']:                 0;
		$customer                    = isset($_POST['customer']) ? $_POST['customer']:                               0;
		$subcustomer                 = isset($_POST['subcustomer']) ? $_POST['subcustomer']:                         0;

		$cur_branchoffice_old        = isset($_POST['cur_branchoffice_id']) ? $_POST['cur_branchoffice_id']:       0;
		$cur_subbranchoffice_old     = isset($_POST['cur_subbranchoffice_id']) ? $_POST['cur_subbranchoffice_id']: 0;
		$cur_customer_old            = isset($_POST['cur_customer_id']) ? $_POST['cur_customer_id']:               0;
		$cur_subcustomer_old         = isset($_POST['cur_subcustomer_id']) ? $_POST['cur_subcustomer_id']:         0;

		$branchofficefixforupdate    = "";
		$subbranchofficefixforupdate = "";
		$customerfixforupdate        = "";
		$subcustomerfixforupdate     = "";

		// JIKA BRANCH OFFICE SAMA DGN YG LAMA
		if ($branchoffice == $cur_branchoffice_old || $branchoffice == "") {
			$branchofficefixforupdate = $cur_branchoffice_old;
		}else {
			$branchofficefixforupdate = $branchoffice;
		}

		// JIKA SUB BRANCH OFFICE SAMA DGN YG LAMA
		if ($subbranchoffice == $cur_subbranchoffice_old || $subbranchoffice == 0) {
			$subbranchofficefixforupdate = $cur_subbranchoffice_old;
		}else {
			$subbranchofficefixforupdate = $subbranchoffice;
		}

		// JIKA CUSTOMER SAMA DGN YG LAMA
		if ($customer == $cur_customer_old || $customer == 0) {
			$customerfixforupdate = $cur_customer_old;
		}else {
			$customerfixforupdate = $customer;
		}

		// JIKA SUB CUSTOMER SAMA DGN YG LAMA
		if ($subcustomer == $cur_subcustomer_old || $subcustomer == 0) {
			$subcustomerfixforupdate = $cur_subcustomer_old;
		}else {
			$subcustomerfixforupdate = $subcustomer;
		}

		if ($subbranchoffice == "empty") {
			// INPUT USER BRANCH OFFICE
			$data['vehicle_company']    = $branchofficefixforupdate;
			$data['vehicle_subcompany'] = 0;
			$data['vehicle_group']      = 0;
			$data['vehicle_subgroup']   = 0;
		}elseif ($customer == "empty") {
			// INPUT USER CUSTOMER
			$data['vehicle_company']    = $branchofficefixforupdate;
			$data['vehicle_subcompany'] = $subbranchofficefixforupdate;
			$data['vehicle_group']      = 0;
			$data['vehicle_subgroup']   = 0;
		}else {
			// INPUT USER SUB CUSTOMER
			$data['vehicle_company']    = $branchofficefixforupdate;
			$data['vehicle_subcompany'] = $subbranchofficefixforupdate;
			$data['vehicle_group']      = $customerfixforupdate;
			if ($subcustomer == "empty") {
				$data['vehicle_subgroup']   = 0;
			}else {
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
	if (isset($app_route) && $app_route ==1){

	}else{
		$driver_update = $this->update_driver($vehicle_id, $driver_id);
		//Add History
		if ($driver_id != 0)
		{
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
	$this->dbtransporter->where("booking_vehicle",$v);
	$this->dbtransporter->where("booking_status",1);
	$this->dbtransporter->where("booking_delivery_status",1);
	$qb = $this->dbtransporter->get("id_booking");
	$rb = $qb->result();
	$tb = count($rb);
	if ($tb > 0)
	{
		$my_r = "true";
	}
	else
	{
		$my_r = "false";
	}
	return $my_r;

}

function getimage()
{
	$images = array_keys($this->config->item('vehicle_image'));

	$vimage = isset($_POST['vimage']) ? trim($_POST['vimage']): $images[0];

	if (! $vimage)
	{
		$callback['message'] = 'Access denied';
		$callback['error'] = true;

		echo json_encode($callback);
		return;
	}

	$folder = BASEPATH."../assets/images/".$vimage;

	if (! is_dir($folder))
	{
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

function update_driver($vehicle_id, $driver_id) {
	$this->dbtransporter = $this->load->database("transporter", true);

	//unset($driver_update);

	 if ($driver_id == 0) {

		 $driver_update['driver_vehicle'] = 0;
		 $this->dbtransporter->where("driver_vehicle", $vehicle_id);
		 $this->dbtransporter->update('driver', $driver_update);
	 }
	 else {

		$driver_update['driver_vehicle'] = $vehicle_id;
		$this->dbtransporter->where("driver_id", $driver_id);
		$this->dbtransporter->update('driver', $driver_update);
	}

	$this->dbtransporter->close();
}

function deleteworkshop(){
	$iddelete = $this->input->post('iddelete');
	$data["workshop_status"] = 2;

	$this->dbtransporter = $this->load->database("transporter", true);
	$this->dbtransporter->where("workshop_id", $iddelete);
	$q = $this->dbtransporter->update('workshop', $data);

		if ($q) {
			$this->session->set_flashdata('notif', 'Data successfully deleted');
			redirect('vehicles/workshop');
		}else {
			$this->session->set_flashdata('notif', 'Data failed deleted');
			redirect('vehicles/workshop');
		}
}

function getbranchofficebyid($id){
	$this->db       = $this->load->database('default', true);
	$this->db->where("company_flag", 0);
	$this->db->where("company_id", $id);
	$q             = $this->db->get("company");
	$rows          = $q->result_array();
	return $rows;
}

function getsubbranchofficebyid($id){
	$this->db       = $this->load->database('default', true);
	$this->db->where("subcompany_flag", 0);
	$this->db->where("subcompany_id", $id);
	$q             = $this->db->get("subcompany");
	$rows          = $q->result_array();
	return $rows;
}

function getcustomerbyid($id){
	$this->db       = $this->load->database('default', true);
	$this->db->where("group_flag", 0);
	$this->db->where("group_id", $id);
	$q             = $this->db->get("group");
	$rows          = $q->result_array();
	return $rows;
}

function getsubcustomerbyid($id){
	$this->db       = $this->load->database('default', true);
	$this->db->where("subgroup_flag", 0);
	$this->db->where("subgroup_id", $id);
	$q             = $this->db->get("subgroup");
	$rows          = $q->result_array();
	return $rows;
}

function saveserviceworks(){
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
	}else {
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
	}else {
		$status = "failed";
	}
// $callback['data']   = $data;
$callback['status'] = $status;
$callback['msg']    = "Unscheduled Service Inserted";
echo json_encode($callback);
}

function changestatusunscheduledservice(){
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
	}else {
		$status = "failed";
	}

	$callback['status'] = $status;
	$callback['msg']    = "Status Now Completed";
	echo json_encode($callback);
}

function deleteunscheduledservice(){
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
	}else {
		$status = "failed";
	}

	$callback['status'] = $status;
	$callback['msg']    = "Data Successfully Deleted";
	echo json_encode($callback);
}

function mdtimei(){
	$vehicleids          = $this->vehiclemodel->getVehicleIds();
	$vid                 = isset($_POST['id']) ? $_POST['id']: "";

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

function savemdt(){
	$vehicle_id  = $_POST['vehicle_id'];
	$vehicle_mdt = $_POST['vehicle_mdt'];

	$data = array(
		"vehicle_mdt" => $vehicle_mdt
	);

	$update              = $this->m_maintenance->updatemdt("vehicle", $data, $vehicle_id);
		if ($update) {
			$callback['message'] = "Success update MDT Imei";
			$callback['error']   = false;
		}else {
			$callback['message'] = "Failed update MDT Imei";
			$callback['error']   = true;
		}
		echo json_encode($callback);
}

function detail($TransID){
	// $vehicleID = "72150903";
	$privilegecode = $this->sess->user_id_role;
	$fileid = 0;
	$truckimage = "";
	//$transdata = $this->m_wimreport->getTransByID(30,"historikal_integrationwim_unit"); //$TransID
	$transdata = $this->m_wimreport->getTransByID($TransID,"historikal_integrationwim_unit"); //$TransID
	if(sizeof($transdata)>0){
		$fileid = $transdata[0]['integrationwim_truckImage'];
		$fileid2 = $transdata[0]['integrationwim_truckImage2'];
	}
	// echo "<pre>";
	// var_dump($transdata);die();
	// echo "<pre>";

	$last_oauth = $this->getlast_OAUTH(4408); //temanindobara

	$getImage_from_Gdrive = $this->getImage_from_Gdrive($last_oauth,$fileid);
	$getImage_from_Gdrive2 = $this->getImage_from_Gdrive($last_oauth,$fileid2);

	//print_r($getImage_from_Gdrive);exit();
	$gdrivedata = json_decode($getImage_from_Gdrive);
	$gdrivedata2 = json_decode($getImage_from_Gdrive2);

	if(isset($gdrivedata->thumbnailLink)){

		$truckimage = $gdrivedata->thumbnailLink;
	}

	if(isset($gdrivedata2->thumbnailLink)){

		$truckimage2 = $gdrivedata2->thumbnailLink;
	}
	//print_r($truckimage);exit();
	$this->params['transdata'] 		  = $transdata;
	$this->params['gdrive_image']   = $truckimage;
	$this->params['gdrive_image2']   = $truckimage2;
	//$this->params['gdrive_image'] = $getImage_from_Gdrive;

	$this->params['code_view_menu'] = "configuration";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

	$this->params['data_dumping']  = $this->m_masterdata->getAllDumping("master_dumping");
	$this->params['data_client']   = $this->m_masterdata->getAllClient("master_client");
	$this->params['data_material'] = $this->m_masterdata->getAllMaterial("master_material");

	$html                      = $this->load->view('newdashboard/wim/v_detail_wim', $this->params, true);
	$callback['datafix']       = $html;

	echo json_encode($callback);

	// if ($privilegecode == 1) {
	// 	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
	// 	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim', $this->params, true);
	// 	$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
	// }elseif ($privilegecode == 2) {
	// 	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
	// 	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim', $this->params, true);
	// 	$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
	// }elseif ($privilegecode == 3) {
	// 	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
	// 	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim', $this->params, true);
	// 	$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
	// }elseif ($privilegecode == 4) {
	// 	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
	// 	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim', $this->params, true);
	// 	$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
	// }elseif ($privilegecode == 5) {
	// 	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
	// 	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim', $this->params, true);
	// 	$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
	// }elseif ($privilegecode == 6) {
	// 	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
	// 	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim', $this->params, true);
	// 	$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
	// }elseif ($privilegecode == 7) {
	// 	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
	// 	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim', $this->params, true);
	// 	$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
	// }elseif ($privilegecode == 8) {
	// 	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
	// 	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim', $this->params, true);
	// 	$this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
	// }else {
	// 	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	// 	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim', $this->params, true);
	// 	$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	// }
}

function show($TransID){

	$fileid = 0;
	$truckimage = "";
	$transdata = $this->m_wimreport->getTransByID($TransID,"historikal_integrationwim_unit");
	if(sizeof($transdata)>0){
		$fileid = $transdata[0]['integrationwim_truckImage'];
	}

	$last_oauth = $this->getlast_OAUTH(4408); //temanindobara
	$getImage_from_Gdrive = $this->getImage_from_Gdrive_show($last_oauth,$fileid);

	$this->params['transdata'] 		 = $transdata;
	$this->params['gdrive_image']    = $getImage_from_Gdrive;
	$this->params['code_view_menu'] = "configuration";

	/* $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true); */
	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim2', $this->params, true);
	$this->load->view("newdashboard/wim/v_detail_wim2", $this->params);
}

function show_2($TransID){

	$fileid = 0;
	$truckimage = "";
	$transdata = $this->m_wimreport->getTransByID($TransID,"historikal_integrationwim_unit");
	if(sizeof($transdata)>0){
		$fileid = $transdata[0]['integrationwim_truckImage2'];
	}

	$last_oauth = $this->getlast_OAUTH(4408); //temanindobara
	$getImage_from_Gdrive = $this->getImage_from_Gdrive_show($last_oauth,$fileid);

	$this->params['transdata'] 		 = $transdata;
	$this->params['gdrive_image']    = $getImage_from_Gdrive;
	$this->params['code_view_menu'] = "configuration";

	/* $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true); */
	$this->params["content"]        = $this->load->view('newdashboard/wim/v_detail_wim2', $this->params, true);
	$this->load->view("newdashboard/wim/v_detail_wim2", $this->params);
}

function getlast_OAUTH($userid){
	$key = 0;
	$this->dbts       = $this->load->database('webtracking_ts', true);
	$this->dbts->select("token_access,");
	$this->dbts->order_by("token_created", "desc");
	$this->dbts->where("token_status", 1);
	$this->dbts->where("token_user", $userid);
	$this->dbts->limit(1);
	$q             = $this->dbts->get("ts_access_token");
	$row    = $q->row();
	if(count($row)>0){
		$key = $row->token_access;
	}

	return $key;
}

function getImage_from_Gdrive($access_token,$fileid){
		//phpinfo();exit();
		//$urlGetImage = "https://www.googleapis.com/drive/v2/files/".$fileid."?alt=media";
		$urlGetImage = "https://www.googleapis.com/drive/v2/files/".$fileid."";
		$curl = curl_init($urlGetImage);
		curl_setopt($curl, CURLOPT_URL, $urlGetImage);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
			"Accept: application/json",
				   "Authorization: Bearer " .$access_token,
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		//add

		$resp = curl_exec($curl);


		curl_close($curl);
		return $resp;


}

function getImage_from_Gdrive_show($access_token,$fileid){
		$ctype = "";
		$userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36';
		//phpinfo();exit();
		$urlGetImage = "https://www.googleapis.com/drive/v2/files/".$fileid."?alt=media";
		//$urlGetImage = "https://www.googleapis.com/drive/v2/files/".$fileid."";
		$curl = curl_init($urlGetImage);
		curl_setopt($curl, CURLOPT_URL, $urlGetImage);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
			"Accept: application/json",
				   "Authorization: Bearer " .$access_token,
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_VERBOSE, 0);
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
		$filename = basename($urlGetImage);
		$file_extension = strtolower(substr(strrchr($filename,"."),1));
		//print_r($file_extension);exit();
		switch( $file_extension ) {
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpeg"; break;
			default:
		}

		header('Content-type: ' . $ctype);
		$output = curl_exec($curl);
		//print_r($output);exit();
		curl_close($curl);

		return $output;

}

function TextToImage($text){

	$newline_after_letters=40;
	  $font='./myfont.ttf';
	  $size=24;
	  $rotate=0;
	  $padding=2;
	  $transparent=true;
	  $color=array('red'=>0,'grn'=>0,'blu'=>0);
	  $bg_color=array('red'=>255,'grn'=>255,'blu'=>255);

	//other version: pastebin(dot).com/XVVUyWGD
	$amount_of_lines= ceil(strlen($text)/$newline_after_letters)+substr_count($text, '\n')+1;
	$all_lines=explode("\n", $text); $text=""; $amount_of_lines = count($all_lines);
	foreach($all_lines as $key=>$value){
		while( mb_strlen($value,'utf-8')>$newline_after_letters){
			$text_final .= mb_substr($value, 0, $newline_after_letters, 'utf-8')."\n";
			$value = mb_substr($value, $newline_after_letters, null, 'utf-8');
		}
		$text .= mb_substr($value, 0, $newline_after_letters, 'utf-8') . ( $amount_of_lines-1 == $key ? "" : "\n");
	}

	//
    Header("Content-type: image/jpeg");
    $width=$height=$offset_x=$offset_y = 0;
    if(!is_file($font)) { file_put_contents($font,file_get_contents('https://github.com/potyt/fonts/raw/master/macfonts/Arial%20Unicode%20MS/Arial%20Unicode.ttf')); }

    // get the font height.
    $bounds = ImageTTFBBox($size, $rotate, $font, "W");
    if ($rotate < 0)        {$font_height = abs($bounds[7]-$bounds[1]); }
    elseif ($rotate > 0)    {$font_height = abs($bounds[1]-$bounds[7]); }
    else { $font_height = abs($bounds[7]-$bounds[1]);}
    // determine bounding box.
    $bounds = ImageTTFBBox($size, $rotate, $font, $text);
    if ($rotate < 0){       $width = abs($bounds[4]-$bounds[0]);                    $height = abs($bounds[3]-$bounds[7]);
                            $offset_y = $font_height;                               $offset_x = 0;
    }
    elseif ($rotate > 0) {  $width = abs($bounds[2]-$bounds[6]);                    $height = abs($bounds[1]-$bounds[5]);
                            $offset_y = abs($bounds[7]-$bounds[5])+$font_height;    $offset_x = abs($bounds[0]-$bounds[6]);
    }
    else{                   $width = abs($bounds[4]-$bounds[6]);                    $height = abs($bounds[7]-$bounds[1]);
                            $offset_y = $font_height;                               $offset_x = 0;
    }

    $image = imagecreate($width+($padding*2)+1,$height+($padding*2)+1);

    $background = ImageColorAllocate($image, $bg_color['red'], $bg_color['grn'], $bg_color['blu']);
    $foreground = ImageColorAllocate($image, $color['red'], $color['grn'], $color['blu']);

    if ($transparent) ImageColorTransparent($image, $background);
    ImageInterlace($image, true);
  // render the image
    ImageTTFText($image, $size, $rotate, $offset_x+$padding, $offset_y+$padding, $foreground, $font, $text);
    imagealphablending($image, true);
    imagesavealpha($image, true);
  // output PNG object.
    imagePNG($image);
}

function updateDetailTruck()
{
	$integrationwim_id                         = $this->input->post('integrationwim_id');
	// DATA LAMA
	$integrationwim_transactionID_old          = $this->input->post('integrationwim_transactionID_old');
	$integrationwim_penimbanganStartUTC_old    = $this->input->post('integrationwim_penimbanganStartUTC_old');
	$integrationwim_penimbanganStartLocal_old  = $this->input->post('integrationwim_penimbanganStartLocal_old');
	$integrationwim_penimbanganFinishUTC_old   = $this->input->post('integrationwim_penimbanganFinishUTC_old');
	$integrationwim_penimbanganFinishLocal_old = $this->input->post('integrationwim_penimbanganFinishLocal_old');
	$integrationwim_beratTiapGandar_old        = $this->input->post('integrationwim_beratTiapGandar_old');
	$integrationwim_totalGandar_old            = $this->input->post('integrationwim_totalGandar_old');
	$integrationwim_gross_old                  = $this->input->post('integrationwim_gross_old');
	$integrationwim_gross_manual_old           = $this->input->post('integrationwim_gross_manual_old');
	$integrationwim_tare_old                   = $this->input->post('integrationwim_tare_old');
	$integrationwim_tare_manual_old            = $this->input->post('integrationwim_tare_manual_old');
	$integrationwim_netto_old                  = $this->input->post('integrationwim_netto_old');
	$integrationwim_netto_manual_old           = $this->input->post('integrationwim_netto_manual_old');
	$integrationwim_averageSpeed_old           = $this->input->post('integrationwim_averageSpeed_old');
	$integrationwim_weightBalance_old          = $this->input->post('integrationwim_weightBalance_old');
	$integrationwim_rfid_old                   = $this->input->post('integrationwim_rfid_old');
	$integrationwim_rfid_master_old            = $this->input->post('integrationwim_rfid_master_old');
	$integrationwim_gps_device_old             = $this->input->post('integrationwim_gps_device_old');
	$integrationwim_gps_mv03_old               = $this->input->post('integrationwim_gps_mv03_old');
	$integrationwim_noRangka_old               = $this->input->post('integrationwim_noRangka_old');
	$integrationwim_noMesin_old                = $this->input->post('integrationwim_noMesin_old');
	$integrationwim_truckType_old              = $this->input->post('integrationwim_truckType_old');
	$integrationwim_providerId_old             = $this->input->post('integrationwim_providerId_old');
	$integrationwim_truckID_old                = $this->input->post('integrationwim_truckID_old');
	$integrationwim_haulingContractor_old      = $this->input->post('integrationwim_haulingContractor_old');
	$integrationwim_driver_name_old            = $this->input->post('integrationwim_driver_name_old');
	$integrationwim_driver_id_old              = $this->input->post('integrationwim_driver_id_old');
	$integrationwim_status_old                 = $this->input->post('integrationwim_status_old');
	$integrationwim_distanceWB_old             = $this->input->post('integrationwim_distanceWB_old');
	$integrationwim_distanceWB_status_old      = $this->input->post('integrationwim_distanceWB_status_old');
	$integrationwim_truckImage_old             = $this->input->post('integrationwim_truckImage_old');
	$integrationwim_created_date_old           = $this->input->post('integrationwim_created_date_old');
	$integrationwim_last_rom_old               = $this->input->post('integrationwim_last_rom_old');
	$integrationwim_material_id_old            = $this->input->post('integrationwim_material_id_old');
	$integrationwim_hauling_id_old             = $this->input->post('integrationwim_hauling_id_old');
	$integrationwim_itws_coal_old              = $this->input->post('integrationwim_itws_coal_old');
	$integrationwim_client_id_old              = $this->input->post('integrationwim_client_id_old');
	$integrationwim_dumping_id_old             = $this->input->post('integrationwim_dumping_id_old');
	$integrationwim_dumping_name_old           = $this->input->post('integrationwim_dumping_name_old');

	$integrationwim_dumping_fms_port_old            = $this->input->post('integrationwim_dumping_fms_port_old');
	$integrationwim_dumping_fms_cp_old              = $this->input->post('integrationwim_dumping_fms_cp_old');
	$integrationwim_dumping_fms_time_old            = $this->input->post('integrationwim_dumping_fms_time_old');
	$integrationwim_dumping_fms_status_old          = $this->input->post('integrationwim_dumping_fms_status_old');
	$integrationwim_dumping_fms_status_datetime_old = $this->input->post('integrationwim_dumping_fms_status_datetime_old');

	$integrationwim_other_text1_old            = $this->input->post('integrationwim_other_text1_old');
	$integrationwim_other_text2_old            = $this->input->post('integrationwim_other_text2_old');
	$integrationwim_approval_status_old        = $this->input->post('integrationwim_approval_status_old');
	$integrationwim_flag_old                   = $this->input->post('integrationwim_flag_old');
	$integrationwim_itws_trans_old             = $this->input->post('integrationwim_itws_trans_old');
	$integrationwim_itws_datetimetrans_old     = $this->input->post('integrationwim_itws_datetimetrans_old');
	$integrationwim_itws_slip_old              = $this->input->post('integrationwim_itws_slip_old');
	$integrationwim_itws_mode_old              = $this->input->post('integrationwim_itws_mode_old');
	$integrationwim_operator_status_old        = $this->input->post('integrationwim_operator_status_old');
	$integrationwim_operator_user_id_old       = $this->input->post('integrationwim_operator_user_id_old');
	$integrationwim_operator_user_name_old     = $this->input->post('integrationwim_operator_user_name_old');
	$integrationwim_operator_datetime_old      = $this->input->post('integrationwim_operator_datetime_old');
	$integrationwim_last_rom_stime_old         = $this->input->post('integrationwim_last_rom_stime_old');
	$integrationwim_last_rom_etime_old      	 = $this->input->post('integrationwim_last_rom_etime_old');
	$integrationwim_fms_status_old      			 = $this->input->post('integrationwim_fms_status_old');
	$integrationwim_fms_status_datetime_old    = $this->input->post('integrationwim_fms_status_datetime_old');




	// DATA BARU
	$detail_integrationwim_grossmanual  = $this->input->post('detail_integrationwim_grossmanual');
	$detail_integrationwim_taremanual   = $this->input->post('detail_integrationwim_taremanual');
	$detail_integrationwim_nettomanual  = $this->input->post('detail_integrationwim_nettomanual');
	$detail_dumping                     = explode('|', $this->input->post('detail_dumping'));
	$detail_material                    = $this->input->post('detail_material');
	$detail_hauling                     = $this->input->post('detail_hauling');
	$detail_coal                        = $this->input->post('detail_coal');
	$detail_client                      = $this->input->post('detail_client');
	$integrationwim_other_text1         = $this->input->post('integrationwim_other_text1');
	$integrationwim_other_text2         = $this->input->post('integrationwim_other_text2');
	$detail_status                      = $this->input->post('detail_status');
	$datetimewita 										  = date("Y-m-d H:i:s", strtotime("+1 hours"));

	$data_historikal = array(
		"integrationwim_transactionID"               => $integrationwim_transactionID_old,
		"integrationwim_penimbanganStartUTC"         => $integrationwim_penimbanganStartUTC_old,
		"integrationwim_penimbanganStartLocal"       => $integrationwim_penimbanganStartLocal_old,
		"integrationwim_penimbanganFinishUTC"        => $integrationwim_penimbanganFinishUTC_old,
		"integrationwim_penimbanganFinishLocal"      => $integrationwim_penimbanganFinishLocal_old,
		"integrationwim_beratTiapGandar"             => $integrationwim_beratTiapGandar_old,
		"integrationwim_totalGandar"                 => $integrationwim_totalGandar_old,
		"integrationwim_gross"                       => $integrationwim_gross_old,
		"integrationwim_gross_manual"                => $integrationwim_gross_manual_old,
		"integrationwim_tare"                        => $integrationwim_tare_old,
		"integrationwim_tare_manual"                 => $integrationwim_tare_manual_old,
		"integrationwim_netto"                       => $integrationwim_netto_old,
		"integrationwim_netto_manual"                => $integrationwim_netto_manual_old,
		"integrationwim_averageSpeed"                => $integrationwim_averageSpeed_old,
		"integrationwim_weightBalance"               => $integrationwim_weightBalance_old,
		"integrationwim_rfid"                        => $integrationwim_rfid_old,
		"integrationwim_rfid_master"                 => $integrationwim_rfid_master_old,
		"integrationwim_gps_device"                  => $integrationwim_gps_device_old,
		"integrationwim_gps_mv03"                    => $integrationwim_gps_mv03_old,
		"integrationwim_noRangka"                    => $integrationwim_noRangka_old,
		"integrationwim_noMesin"                     => $integrationwim_noMesin_old,
		"integrationwim_truckType"                   => $integrationwim_truckType_old,
		"integrationwim_providerId"                  => $integrationwim_providerId_old,
		"integrationwim_truckID"                     => $integrationwim_truckID_old,
		"integrationwim_haulingContractor"           => $integrationwim_haulingContractor_old,
		"integrationwim_driver_name"                 => $integrationwim_driver_name_old,
		"integrationwim_driver_id"                   => $integrationwim_driver_id_old,
		"integrationwim_status"                      => $integrationwim_status_old,
		"integrationwim_distanceWB"                  => $integrationwim_distanceWB_old,
		"integrationwim_distanceWB_status"           => $integrationwim_distanceWB_status_old,
		"integrationwim_truckImage"                  => $integrationwim_truckImage_old,
		"integrationwim_created_date"                => $integrationwim_created_date_old,
		"integrationwim_last_rom"                    => $integrationwim_last_rom_old,
		"integrationwim_material_id"                 => $integrationwim_material_id_old,
		"integrationwim_hauling_id"                  => $integrationwim_hauling_id_old,
		"integrationwim_itws_coal"                   => $integrationwim_itws_coal_old,
		"integrationwim_client_id"                   => $integrationwim_client_id_old,
		"integrationwim_dumping_id"                  => $integrationwim_dumping_id_old,
		"integrationwim_dumping_name"                => $integrationwim_dumping_name_old,

		"integrationwim_dumping_fms_port"            => $integrationwim_dumping_fms_port_old,
		"integrationwim_dumping_fms_cp"              => $integrationwim_dumping_fms_cp_old,
		"integrationwim_dumping_fms_time"            => $integrationwim_dumping_fms_time_old,
		"integrationwim_dumping_fms_status"          => $integrationwim_dumping_fms_status_old,
		"integrationwim_dumping_fms_status_datetime" => $integrationwim_dumping_fms_status_datetime_old,

		"integrationwim_other_text1"                 => $integrationwim_other_text1_old,
		"integrationwim_other_text2"                 => $integrationwim_other_text2_old,
		"integrationwim_approval_status"             => $integrationwim_approval_status_old,
		"integrationwim_flag"                        => $integrationwim_flag_old,
		"integrationwim_itws_trans"                  => $integrationwim_itws_trans_old,
		"integrationwim_itws_datetimetrans"          => $integrationwim_itws_datetimetrans_old,
		"integrationwim_itws_slip"                   => $integrationwim_itws_slip_old,
		"integrationwim_itws_mode"                   => $integrationwim_itws_mode_old,
		"integrationwim_operator_status"             => $integrationwim_operator_status_old,
		"integrationwim_operator_user_id"            => $integrationwim_operator_user_id_old,
		"integrationwim_operator_user_name"          => $integrationwim_operator_user_name_old,
		"integrationwim_operator_datetime"           => $integrationwim_operator_datetime_old,
		"integrationwim_adminupdate_user_id"         => 2,
		"integrationwim_adminupdate_user_name"       => $this->sess->user_name,
		"integrationwim_adminupdate_datetime"        => $datetimewita,
		"integrationwim_last_rom_stime" 		         => $integrationwim_last_rom_stime_old,
		"integrationwim_last_rom_etime" 		         => $integrationwim_last_rom_etime_old,
		"integrationwim_fms_status" 				         => $integrationwim_fms_status_old,
		"integrationwim_fms_status_datetime"         => $integrationwim_fms_status_datetime_old,
	);

	$data_update = array(
		"integrationwim_gross_manual"          => $detail_integrationwim_grossmanual,
		"integrationwim_tare_manual"           => $detail_integrationwim_taremanual,
		"integrationwim_netto_manual"          => $detail_integrationwim_nettomanual,
		"integrationwim_material_id"           => $detail_material,
		"integrationwim_hauling_id"            => $detail_hauling,
		"integrationwim_itws_coal"             => $detail_coal,
		"integrationwim_client_id"             => $detail_client,
		"integrationwim_dumping_id"            => $detail_dumping[0],
		"integrationwim_dumping_name"          => $detail_dumping[1],
		"integrationwim_other_text1"           => $integrationwim_other_text1,
		"integrationwim_other_text2"           => $integrationwim_other_text2,
		"integrationwim_approval_status"       => $detail_status,
		"integrationwim_operator_status"       => 2,
		"integrationwim_adminupdate_user_id"   => $this->sess->user_id,
		"integrationwim_adminupdate_user_name" => $this->sess->user_name,
		"integrationwim_adminupdate_datetime"  => $datetimewita,
	);

	// echo "<pre>";
	// var_dump($data_historikal);die();
	// echo "<pre>";

	$update_historikal = $this->m_wimreport->insert_historikal_adminupdate("historikal_integrationwim_unit_adminupdate", $data_historikal);
	if ($update_historikal) {
		$update_datanew = $this->m_wimreport->updatedatawim("tensor_report", "historikal_integrationwim_unit", $integrationwim_id, $data_update);
			if ($update_datanew) {
				echo json_encode(array("msg" => "success", "code" => 200));
			}else {
				echo json_encode(array("msg" => "failed", "code" => 400));
			}
	} else {
		echo json_encode(array("msg" => "failed", "code" => 400));
	}
}

function testDownloadGoogleDrive(){
	// require_once 'vendor/autoload';
	// $this->load->library('vendor/autoload');
	$transactionid = $this->input->post('id');

	echo "<pre>";
	var_dump($transactionid);die();
	echo "<pre>";
}

function search_report(){
	$company          = $this->input->post("company");
	$vehicle          = $this->input->post("vehicle");
	$startdate        = $this->input->post("startdate");
	$shour            = "00:00:00";
	$enddate          = $this->input->post("enddate");
	$ehour            = "23:59:59";
	$statuswim        = $this->input->post("statuswim");
	$modewim          = $this->input->post("modewim");
	$periode          = $this->input->post("periode");
	$first_load       = $this->input->post("first_load");
	$transactionid    = $this->input->post("transactionid_select");

	// KONDISI TRANSACTION ID  START
	if ($transactionid != "") {
		$findthissymbol = array(".","-","_","!");
		$symbolfounded  = (str_replace($findthissymbol, '', $transactionid) != $transactionid);
		if ($symbolfounded) {
			$transactionidfix    = 0;
			$callback['error']   = true;
			$callback['message'] = "Gunakan koma sebagai pembatas untuk mencari lebih dari satu Transaction ID";
			echo json_encode($callback);
			return;
		}else {
			$transactionidfix = $transactionid;
		}
	}else {
		$transactionidfix = 0;
	}

	// echo "<pre>";
	// var_dump($transactionidfix);die();
	// echo "<pre>";
	// KONDISI TRANSACTION ID END


	/* $sdate     = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00") + 60*60*1);
	$edate     = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":00") + 60*60*1); */

	$sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
	$edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

	$nowdate   = date("Y-m-d");
	$nowday    = date("d");
	$nowmonth  = date("m");
	$nowyear   = date("Y");
	$lastday   = date("t");

	// if ($first_load == 1) {
		$sdate1 = date("Y-m-d");
		$sdate2 = "00:00:00";

		$edate1 = date("Y-m-d");
		$edate2 = "23:59:59";

		$sdate = $sdate1." ".$sdate2;
		$edate = $edate1." ".$edate2;
	// }else {
	// 	if($periode == "custom"){
	// 		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
	// 		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
	// 	}else if($periode == "yesterday"){
	// 		$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
	// 		$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
	// 	}else if($periode == "last7"){
	// 		/* $nowday = $nowday - 1;
	// 		$firstday = $nowday - 7;
	// 		if($nowday <= 7){
	// 			$firstday = 1;
	// 		}
	// */
	// 		/* $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59")); */
  //
	// 		$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-7days"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
  //
	// 	}else if($periode == "last30"){
	// 		/* $firstday = "1";
	// 		$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59")); */
  //
	// 		$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-30days"));
	// 		$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
  //
	// 	}else if($periode == "today"){
	// 		$sdate1 = date("Y-m-d");
	// 		$sdate2 = "00:00:00";
  //
	// 		$edate1 = date("Y-m-d");
	// 		$edate2 = "23:59:59";
  //
	// 		$sdate = $sdate1." ".$sdate2;
	// 		$edate = $edate1." ".$edate2;
	// 	}else{
  //
	// 		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
	// 		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
	// 	}
	// }

	$m1      = date("F", strtotime($sdate));
	$year    = date("Y", strtotime($sdate));
	$report = "historikal_integrationwim_unit_";

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

	//print_r($sdate." ".$edate);exit();
	$dbtable = "historikal_integrationwim_unit";
	//$getreport            = $this->m_wimreport->getreportnow($dbtable, $vehicle, $statuswim, $sdate, $edate);

		$this->dbreport = $this->load->database("tensor_report", true);

    // if ($first_load == 1) {
      $this->dbreport->limit(100, 0);
    // }else {
    //   if ($transactionidfix != 0) {
    //     $this->dbreport->where_in("integrationwim_transactionID", $transactionidfix);
    //   }else {
    //     if ($vehicle != "all") {
    //       $this->dbreport->where("integrationwim_TruckID", $vehicle);
    //     }
    //
    //     if ($modewim != "all") {
    //       $this->dbreport->where("integrationwim_status", $modewim);
    //     }
    //
    //     if ($statuswim != "all") {
    //       $this->dbreport->where("integrationwim_operator_status", $statuswim);
    //     }
    //
    //     $this->dbreport->where("integrationwim_PenimbanganStartLocal >= ", $sdate);
    //     $this->dbreport->where("integrationwim_PenimbanganFinishLocal <= ", $edate);
    //   }
    // }

		//$this->dbreport->where("integrationwim_flag", 0);//bukan data dihapus
		// $this->dbreport->order_by("integrationwim_operator_status", 0); // INI DIAKTIFKAN SESUAI PERMINTAAN
		$this->dbreport->order_by("integrationwim_PenimbanganStartLocal", "DESC");
		$q = $this->dbreport->get($dbtable);
		$getreport = $q->result_array();

		//print_r($sdate." ".$edate." ".$dbtable);

	$this->params['data'] = $getreport;

	//print_r($getreport);exit();
	// $dbtable.'-'.$vehicle.'-'.$sdate.'-'.$edate
	// echo "<pre>";
	// var_dump($getreport);die();
	// echo "<pre>";

	$html = $this->load->view("newdashboard/wim/v_wim_result", $this->params, true);
	$callback['error'] = false;
	$callback['html']  = $html;
	$callback['data']  = $getreport;
	echo json_encode($callback);
}

function getTransactionID(){
	$transactionID     = $_POST['transactionid'];

	$datatransactionID = $this->m_wimreport->getAllTransactionID($transactionID);

	$callback['data']  = $datatransactionID;
	echo json_encode($callback);
}


function get_company_bylevel()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$privilegecode 						= $this->sess->user_id_role;

		$this->db->order_by("company_name", "asc");
		if ($privilegecode == 0) {
			$this->db->where("company_created_by", $this->sess->user_id);
		} elseif ($privilegecode == 1) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 2) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 3) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 4) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 5) {
			$this->db->where("company_created_by", $this->sess->user_company);
		}elseif ($privilegecode == 6) {
			$this->db->where("company_created_by", $this->sess->user_company);
		}elseif ($privilegecode == 7) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		}elseif ($privilegecode == 8) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		}

		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}

	function get_vehicle_by_company_with_numberorder($id)
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
			$options = "<option value='all' selected='selected' >--All Vehicle--</option>";
			$i = 1;
			foreach ($rd as $obj) {
				$options .= "<option value='" . $obj->vehicle_device . "'>" . $i . ". " . $obj->vehicle_no . " - " . $obj->vehicle_name . " " . "(" . $obj->company_name . ")" . "</option>";
				$i++;
			}

			echo $options;
			return;
		}
	}

	function replacement()
	{
		ini_set('display_errors', 1);

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
		$user_id_fix     = 4408;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($privilegecode == 0) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		} else if ($privilegecode == 1) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 2) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 3) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 4) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 5) {
			$this->db->where("vehicle_company", $user_company);
		} else if ($privilegecode == 6) {
			$this->db->where("vehicle_company", $user_company);
		} else if ($privilegecode == 7) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		} else if ($privilegecode == 8) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else {
			$this->db->where("vehicle_no", 99999);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
		}

		$rows = $q->result();
		$rows_company = $this->get_company_bylevel();

		// echo "<pre>";
		// var_dump($rows_company);die();
		// echo "<pre>";

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;

		//$this->params["data"] 		    = $result;
		$this->params['code_view_menu'] = "wimmenu";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/v_wim_replacement', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}elseif ($privilegecode == 8) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/v_wim_replacement', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/v_wim_replacement', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function searchforreplacement(){
		$filterdatevalue       = $this->input->post('filterdatevalue');
		$limit 					       = $this->input->post('limitvalue');
		$contractorexplode 		 = explode("|", $this->input->post('contractor'));
		$contractor 					 = $contractorexplode[0];
		$vehicle    					 = $this->input->post('vehicle');
		$startdate             = date("Y-m-d", strtotime($this->input->post('startdate')));
		$month 					       = date("n", strtotime($startdate));
		$year 					       = date("Y", strtotime($startdate));

		// $filterdatevalue.'-'.$startdate.'-'.$month.'-'.$year

		$dataactualarray = array();
		$dataactual      = $this->m_wimreport->dataactual("historikal_integrationwim_unit_testing", $filterdatevalue, $startdate, $month, $year, $limit, $contractor, $vehicle);
		if (sizeof($dataactual) > 0) {
			for ($i=0; $i < sizeof($dataactual); $i++) {
				$d1               = strtotime($dataactual[$i]['integrationwim_created_date']);
				$d2               = strtotime($dataactual[$i]['integrationwim_penimbanganFinishLocal']);
				$totalSecondsDiff = $d1-$d2;
				$totalMinutesDiff = round($totalSecondsDiff/60);
				$totalHoursDiff   = round($totalSecondsDiff/60/60);
				$totalDaysDiff    = round($totalSecondsDiff/60/60/24);

				$selisih = "";
					if ($totalDaysDiff == 0 &&  $totalHoursDiff == 0 && $totalMinutesDiff == 0) {
						$selisih = $totalSecondsDiff.' Detik';
					}elseif ($totalDaysDiff == 0 &&  $totalHoursDiff == 0 && $totalMinutesDiff != 0) {
						$selisih = $totalMinutesDiff.' Menit';
					}elseif ($totalDaysDiff == 0 &&  $totalHoursDiff != 0 && $totalMinutesDiff != 0) {
						$selisih = $totalHoursDiff.' Jam '.$totalMinutesDiff.' Menit';
					}else {
						$selisih = $totalDaysDiff.' Hari '.$totalHoursDiff.' Jam '.$totalMinutesDiff.' Menit';
					}

					// if ($totalMinutesDiff >= 5) { //INI YANG ASLI
						if ($totalMinutesDiff >= 1) { // INI DIGANTI YG DIATAS KALO LIVE
						array_push($dataactualarray, array(
							"integrationwim_created_date"           => $dataactual[$i]['integrationwim_created_date'],
							"integrationwim_penimbanganFinishLocal" => $dataactual[$i]['integrationwim_penimbanganFinishLocal'],
							"integrationwim_transactionID"          => $dataactual[$i]['integrationwim_transactionID'],
							"integrationwim_id"                     => $dataactual[$i]['integrationwim_id'],
							"integrationwim_haulingContractor"      => $dataactual[$i]['integrationwim_haulingContractor'],
							"integrationwim_truckID"                => $dataactual[$i]['integrationwim_truckID'],
							"integrationwim_penimbanganStartLocal"  => $dataactual[$i]['integrationwim_penimbanganStartLocal'],
							"integrationwim_penimbanganFinishLocal" => $dataactual[$i]['integrationwim_penimbanganFinishLocal'],
							"integrationwim_created_date"           => $dataactual[$i]['integrationwim_created_date'],
							"selisih" 									            => $selisih,
						));
					}
			}
		}
		$params['dataactual']  = $dataactualarray;

		$dataaverage           = $this->m_wimreport->dataaverage("historikal_integrationwim_unit_testing", $filterdatevalue, $startdate, $month, $year, $limit, $contractor, $vehicle);
		$params['dataaverage'] = $dataaverage;
		// echo "<pre>";
		// var_dump($filterdatevalue.'-'.$startdate.'-'.$month.'-'.$year.'-'.$contractor.'-'.$vehicle);die();
		// echo "<pre>";

		if ($dataaverage) {
			$html                    = $this->load->view('newdashboard/wim/v_wim_replacement_result', $params, true);
			$callback['error']       = false;
			$callback['html']        = $html;
			$callback['dataactual']  = $dataactualarray;
			$callback['dataaverage'] = $dataaverage;
		}else {
			$callback['error'] = true;
		}

		echo json_encode($callback);

		// echo "<pre>";
		// var_dump($existingwimdata);die();
		// echo "<pre>";
	}

	function doreplacement(){
		$transIDAverage    = $_POST['transactionIDAverage'];
		$transIDActual     = $_POST['transactionIDActual'];
		$transIDAveragefix = $_POST['transactionIDAveragereal'];
		$transIDActualfix  = $_POST['transactionIDActualreal'];
		$contractor        = $_POST['contractor'];
		$vehicle           = $_POST['vehicle'];

		$dataActual = array(
			"integrationwim_replacement_status" => "R",
			"integrationwim_remark"             => "Transaction ID " . $transIDAveragefix . " digantikan dengan Transaction ID " . $transIDActualfix
		);

		$dataAverage = array(
			"integrationwim_replacement_status" => "R",
			"integrationwim_remark"             => "Transaction ID " . $transIDActualfix . " menggantikan Transaction ID " . $transIDAveragefix
		);

		$update_1 = $this->m_wimreport->updatereplacement("historikal_integrationwim_unit_testing", "integrationwim_id", $transIDAverage, $dataActual);
			if ($update_1) {
				$update_2 = $this->m_wimreport->updatereplacement("historikal_integrationwim_unit_testing", "integrationwim_id", $transIDActual, $dataAverage);
					if ($update_2) {
						$getdataAverage = $this->m_wimreport->getthistransactionid("historikal_integrationwim_unit_testing", $transIDAverage);
						$getdataActual  = $this->m_wimreport->getthistransactionid("historikal_integrationwim_unit_testing", $transIDActual);

						// echo "<pre>";
						// var_dump($getdataActual);die();
						// echo "<pre>";

						$datahistorikal = array(
							"hist_replacementwim_vehicleno_awal"     => $getdataAverage[0]['integrationwim_truckID'],
							"hist_replacementwim_vehiclenopengganti" => $getdataActual[0]['integrationwim_truckID'],
							"hist_replacementwim_companyawal"        => $getdataAverage[0]['integrationwim_haulingContractor'],
							"hist_replacementwim_companypengganti"   => $getdataActual[0]['integrationwim_haulingContractor'],
							"hist_replacementwim_transIDawal"        => $transIDAveragefix,
							"hist_replacementwim_transIDpengganti"   => $transIDActualfix,
							"hist_replacementwim_detailawal"         => json_encode($getdataAverage),
							"hist_replacementwim_detailpengganti"    => json_encode($getdataActual),
							"hist_replacementwim_created_by"         => $this->sess->user_name,
							"hist_replacementwim_created_date"       => date("Y-m-d H:i:s", strtotime("+1hours"))
						);
						$inserttohistorikal = $this->m_wimreport->insert_historikal_replacement("historikal_replacement_wim", $datahistorikal);
							if ($inserttohistorikal) {
								$filterdatevalue       = $_POST['filterdatevalue'];
								$limit 					       = $_POST['limitvalue'];
								$startdate             = date("Y-m-d", strtotime($_POST['startdate']));
								$month 					       = date("n", strtotime($startdate));
								$year 					       = date("Y", strtotime($startdate));

								$dataactualarray = array();
								$dataactual      = $this->m_wimreport->dataactual("historikal_integrationwim_unit_testing", $filterdatevalue, $startdate, $month, $year, $limit, $contractor, $vehicle);
								if (sizeof($dataactual) > 0) {
									for ($i=0; $i < sizeof($dataactual); $i++) {
										$d1               = strtotime($dataactual[$i]['integrationwim_created_date']);
										$d2               = strtotime($dataactual[$i]['integrationwim_penimbanganFinishLocal']);
										$totalSecondsDiff = $d1-$d2;
										$totalMinutesDiff = round($totalSecondsDiff/60);
										$totalHoursDiff   = round($totalSecondsDiff/60/60);
										$totalDaysDiff    = round($totalSecondsDiff/60/60/24);

										$selisih = "";
											if ($totalDaysDiff == 0 &&  $totalHoursDiff == 0 && $totalMinutesDiff == 0) {
												$selisih = $totalSecondsDiff.' Detik';
											}elseif ($totalDaysDiff == 0 &&  $totalHoursDiff == 0 && $totalMinutesDiff != 0) {
												$selisih = $totalMinutesDiff.' Menit';
											}elseif ($totalDaysDiff == 0 &&  $totalHoursDiff != 0 && $totalMinutesDiff != 0) {
												$selisih = $totalHoursDiff.' Jam '.$totalMinutesDiff.' Menit';
											}else {
												$selisih = $totalDaysDiff.' Hari '.$totalHoursDiff.' Jam '.$totalMinutesDiff.' Menit';
											}

											if ($totalMinutesDiff >= 5) {
												array_push($dataactualarray, array(
													"integrationwim_created_date"           => $dataactual[$i]['integrationwim_created_date'],
													"integrationwim_penimbanganFinishLocal" => $dataactual[$i]['integrationwim_penimbanganFinishLocal'],
													"integrationwim_transactionID"          => $dataactual[$i]['integrationwim_transactionID'],
													"integrationwim_id"                     => $dataactual[$i]['integrationwim_id'],
													"integrationwim_haulingContractor"      => $dataactual[$i]['integrationwim_haulingContractor'],
													"integrationwim_truckID"                => $dataactual[$i]['integrationwim_truckID'],
													"integrationwim_penimbanganStartLocal"  => $dataactual[$i]['integrationwim_penimbanganStartLocal'],
													"integrationwim_penimbanganFinishLocal" => $dataactual[$i]['integrationwim_penimbanganFinishLocal'],
													"integrationwim_created_date"           => $dataactual[$i]['integrationwim_created_date'],
													"selisih" 									            => $selisih,
												));
											}
									}
								}
								$params['dataactual']  = $dataactualarray;

								$dataaverage           = $this->m_wimreport->dataaverage("historikal_integrationwim_unit_testing", $filterdatevalue, $startdate, $month, $year, $limit, $contractor, $vehicle);
								$params['dataaverage'] = $dataaverage;

								$html                    = $this->load->view('newdashboard/wim/v_wim_replacement_result', $params, true);
								$callback['error']       = false;
								$callback['html']        = $html;
								$callback['dataactual']  = $dataactualarray;
								$callback['dataaverage'] = $dataaverage;
								$callback['msg']         = "Berhasil merubah data";
								echo json_encode($callback);
							}else {
								$callback['error'] = true;
								$callback['msg']   = "Gagal insert historikal replacement wim. Error 1003";
								echo json_encode($callback);
							}
					}else {
						$callback['error'] = true;
						$callback['msg']   = "Gagal merubah data. Error 1002";
						echo json_encode($callback);
					}
			}else {
				$callback['error'] = true;
				$callback['msg']   = "Gagal merubah data. Error 1001";
				echo json_encode($callback);
			}
	}

	function replacementreport()
	{
		ini_set('display_errors', 1);

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
		$user_id_fix     = 4408;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($privilegecode == 0) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		} else if ($privilegecode == 1) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 2) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 3) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 4) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 5) {
			$this->db->where("vehicle_company", $user_company);
		} else if ($privilegecode == 6) {
			$this->db->where("vehicle_company", $user_company);
		} else if ($privilegecode == 7) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		} else if ($privilegecode == 8) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else {
			$this->db->where("vehicle_no", 99999);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
		}

		$rows = $q->result();
		$rows_company = $this->get_company_bylevel();

		// echo "<pre>";
		// var_dump($rows_company);die();
		// echo "<pre>";

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;

		//$this->params["data"] 		    = $result;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/v_wim_replacement_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}elseif ($privilegecode == 8) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/v_wim_replacement_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/v_wim_replacement_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function get_vehicle_by_company($id)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$this->db->order_by("vehicle_no", "asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,vehicle_imei,vehicle_company");

			if ($id != "all") {
				$this->db->where("vehicle_company", $id);
			}else {
				$this->db->where("vehicle_user_id ", 4408);
			}

		$this->db->where("vehicle_status <>", 3);
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if ($qd->num_rows() > 0) {
			$options = "<option value='all' selected='selected' >--All Vehicle</option>";
			foreach ($rd as $obj) {
				$options .= "<option value='" . $obj->vehicle_no . "'>" . $obj->vehicle_no . "</option>";
			}

			echo $options;
			return;
		}
	}

	function operatoritws()
	{
		ini_set('display_errors', 1);

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
    $user_id_fix     = 4408;

    $this->db->select("vehicle.*, user_name");
    $this->db->order_by("vehicle_no", "asc");
    $this->db->where("vehicle_status <>", 3);

    if ($privilegecode == 0) {
      $this->db->where("vehicle_user_id", $user_id_fix);
    } else if ($privilegecode == 1) {
      $this->db->where("vehicle_user_id", $user_parent);
    } else if ($privilegecode == 2) {
      $this->db->where("vehicle_user_id", $user_parent);
    } else if ($privilegecode == 3) {
      $this->db->where("vehicle_user_id", $user_parent);
    } else if ($privilegecode == 4) {
      $this->db->where("vehicle_user_id", $user_parent);
    } else if ($privilegecode == 5) {
      $this->db->where("vehicle_company", $user_company);
    } else if ($privilegecode == 6) {
      $this->db->where("vehicle_company", $user_company);
    } else if ($privilegecode == 7) {
      $this->db->where("vehicle_user_id", $user_id_fix);
    } else if ($privilegecode == 8) {
      $this->db->where("vehicle_user_id", $user_id_fix);
    }else {
      $this->db->where("vehicle_no", 99999);
    }

    $this->db->join("user", "vehicle_user_id = user_id", "left outer");
    $q = $this->db->get("vehicle");

    if ($q->num_rows() == 0) {
      redirect(base_url());
    }

    $rows          = $q->result_array();
    $rows_company  = $this->get_company_bylevel();

    $dataClient    = $this->m_wimreport->allDataClient();
    $dataMaterial  = $this->m_wimreport->allDataMaterial();
    $streetRom     = $this->m_wimreport->getstreet_now(3);
    $alldriveritws = $this->m_wimreport->alldriveritws();

    $data_rom = array();
    for ($i=0; $i < sizeof($streetRom); $i++) {
      if ($streetRom[$i]['street_type'] == 3) {
        array_push($data_rom, array(
          "street_id"   => $streetRom[$i]['street_id'],
          "street_name" => str_replace(",", "", $streetRom[$i]['street_name']),
        ));
      }
    }

    // echo "<pre>";
    // var_dump($alldriveritws);die();
    // echo "<pre>";

    $this->params["vehicles"]        = $rows;
    $this->params["rcompany"]        = $rows_company;
    $this->params["data_client"]     = $dataClient;
    $this->params["data_material"]   = $dataMaterial;
    $this->params["data_rom"]			   = $data_rom;
    $this->params["datadriveritws"]	 = $alldriveritws;

    //$this->params["data"] 		    = $result;
    $this->params['code_view_menu'] = "wimmenu";

	  $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	  $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/operator/v_wim_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}elseif ($privilegecode == 8) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/operator/v_wim_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wim/operator/v_wim_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function search_report_operator(){
		$company          = $this->input->post("company");
		$vehicle          = $this->input->post("vehicle");
		$startdate        = $this->input->post("startdate");
		$shour            = "00:00:00";
		$enddate          = $this->input->post("enddate");
		$ehour            = "23:59:59";
		$statuswim        = $this->input->post("statuswim");
		$modewim          = $this->input->post("modewim");
		$periode          = $this->input->post("periode");
		$first_load       = $this->input->post("first_load");
		$transactionid    = $this->input->post("transactionid_select");

		// KONDISI TRANSACTION ID  START
		if ($transactionid != "") {
			$findthissymbol = array(".","-","_","!");
			$symbolfounded  = (str_replace($findthissymbol, '', $transactionid) != $transactionid);
			if ($symbolfounded) {
				$transactionidfix    = 0;
				$callback['error']   = true;
				$callback['message'] = "Gunakan koma sebagai pembatas untuk mencari lebih dari satu Transaction ID";
				echo json_encode($callback);
				return;
			}else {
				$transactionidfix = $transactionid;
			}
		}else {
			$transactionidfix = 0;
		}

		// echo "<pre>";
		// var_dump($transactionidfix);die();
		// echo "<pre>";
		// KONDISI TRANSACTION ID END


		/* $sdate     = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00") + 60*60*1);
		$edate     = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":00") + 60*60*1); */

		$sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
		$edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

		$nowdate   = date("Y-m-d");
		$nowday    = date("d");
		$nowmonth  = date("m");
		$nowyear   = date("Y");
		$lastday   = date("t");

			$sdate1 = date("Y-m-d");
			$sdate2 = "00:00:00";

			$edate1 = date("Y-m-d");
			$edate2 = "23:59:59";

			$sdate = $sdate1." ".$sdate2;
			$edate = $edate1." ".$edate2;

		$m1      = date("F", strtotime($sdate));
		$year    = date("Y", strtotime($sdate));
		$report = "historikal_integrationwim_unit_";

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

		//print_r($sdate." ".$edate);exit();
		$dbtable = "historikal_integrationwim_unit";
		//$getreport            = $this->m_wimreport->getreportnow($dbtable, $vehicle, $statuswim, $sdate, $edate);

			$this->dbreport = $this->load->database("tensor_report", true);

	      $this->dbreport->limit(100, 0);

			//$this->dbreport->where("integrationwim_flag", 0);//bukan data dihapus
			// $this->dbreport->order_by("integrationwim_operator_status", 0); // INI DIAKTIFKAN SESUAI PERMINTAAN
			$this->dbreport->order_by("integrationwim_PenimbanganStartLocal", "DESC");
			$q = $this->dbreport->get($dbtable);
			$getreport = $q->result_array();

			//print_r($sdate." ".$edate." ".$dbtable);

		$this->params['data'] = $getreport;

		//print_r($getreport);exit();
		// $dbtable.'-'.$vehicle.'-'.$sdate.'-'.$edate
		// echo "<pre>";
		// var_dump($getreport);die();
		// echo "<pre>";

		$html = $this->load->view("newdashboard/wim/operator/v_wim_result", $this->params, true);
		$callback['error'] = false;
		$callback['html']  = $html;
		$callback['data']  = $getreport;
		echo json_encode($callback);
	}

	function getVehicleByInput(){
	  $keyword     = $_POST['keyword'];
	  $datavehicle = $this->m_wimreport->getDataVehicle($keyword);

	  $datafix = array();
	  if (sizeof($datavehicle) > 0) {
	    $datafix = array_map('current', $datavehicle);
	  }
	  echo json_encode(array("data" => $datafix));
	}

	function getClientByInput(){
	  $keyword     = $_POST['keyword'];
	  $data = $this->m_wimreport->getDataClient($keyword);

	  $datafix = array();
	  if (sizeof($data) > 0) {
	    $datafix = array_map('current', $data);
	  }
	  echo json_encode(array("data" => $datafix));
	}

	function getMaterialByInput(){
	  $keyword     = $_POST['keyword'];
	  $data = $this->m_wimreport->getDataMaterial($keyword);

	  $datafix = array();
	  if (sizeof($data) > 0) {
	    $datafix = array_map('current', $data);
	  }
	  echo json_encode(array("data" => $datafix));
	}

	function getMaterialValue(){
	  $keyword = $_POST['materialid'];
	  $data    = $this->m_wimreport->getThisMaterial($keyword);

	  if (sizeof($data) > 0) {
	    echo json_encode(array("data" => $data));
	  }else {
	    echo json_encode(array("data" => 0));
	  }
	}

	function recallThisVehicle() {
	  $keyword     = $_POST['vehicleno'];
	  $dataRecall  = $this->m_wimreport->recallToLast($keyword);
	  $company     = $dataRecall[0]['integrationwim_haulingContractor'];
	  $company_fix = "";

	  if ($company == "HTM") {
	    $company_fix = "MKS";
	  }elseif ($company == "RAM") {
	    $company_fix = "RAMB";
	  }elseif ($company == "STL") {
	    $company_fix = "STLI";
	  }elseif ($company == "GEC") {
	    $company_fix = "GECL";
	  }else {
	    $company_fix = $company;
	  }

	  $alldriveritws = $this->m_wimreport->driveritwsbycompany($company_fix);

	  // echo "<pre>";
	  // var_dump($alldriveritws);die();
	  // echo "<pre>";

	  if (sizeof($dataRecall) > 0) {
	    $dataforprocess  = $dataRecall[0];
	    echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_driver_itws" => $alldriveritws));
	  }else {
	    echo json_encode(array("code" => 400));
	  }

	  // if (sizeof($dataRecall) > 0 && sizeof($dataRecall) > 1) {
	  //   $dataforprocess  = $dataRecall[0];
	  //   $datalastrecall  = $dataRecall[1];
	  //   // $datalastrecall2 = $dataRecall[2];
	  //   // echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => $datalastrecall, "data_lastrecall2" => $datalastrecall2));
	  //   echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => $datalastrecall));
	  // }elseif (sizeof($dataRecall) == 1) {
	  //   $dataforprocess  = $dataRecall[0];
	  //
	  //   echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => array()));
	  // }else {
	  //   echo json_encode(array("code" => 400));
	  // }
	}

	function itws_update_data(){
	  $itws_transID       = $this->input->post('itws_transID');
	  $itws_nolambung     = $this->input->post('itws_nolambung');
	  $itws_rom           = $this->input->post('itws_rom');
	  $itws_driver        = explode("|", $this->input->post('itws_driver'));
	  $driver_id_cron     = $this->input->post('driver_id_cron');
	  $driver_name_cron   = $this->input->post('driver_name_cron');
	  $itws_client        = $this->input->post('itws_client');
	  $itws_material      = $this->input->post('itws_material');
	  $itws_hauling       = $this->input->post('itws_hauling');
	  $itws_coal          = $this->input->post('itws_coal');

	  if ($itws_transID == "" || $itws_nolambung == "") {
	    $callback['error'] = true;
	    $callback['message'] = "Transaction ID / No Lambung tidak boleh kosong";
	    echo json_encode($callback);
	    return;
	  }

	  if ($itws_rom == "" || $itws_rom == "0") {
	    $callback['error'] = true;
	    $callback['message'] = "Harap memilih ROM terlebih dahulu";
	    echo json_encode($callback);
	    return;
	  }

	  $dataforupdate = array(
	    "integrationwim_last_rom"           => $itws_rom,
	    "integrationwim_client_id"          => $itws_client,
	    "integrationwim_material_id"        => $itws_material,
	    "integrationwim_hauling_id"         => $itws_hauling,
	    "integrationwim_itws_coal"          => $itws_coal,
	    "integrationwim_operator_status"    => 1,
	    "integrationwim_driver_iditws"      => $itws_driver[0],
	    "integrationwim_driver_nameitws"    => $itws_driver[1],
	    "integrationwim_driver_id"          => $driver_id_cron,
	    "integrationwim_driver_name"        => $driver_name_cron,
	    "integrationwim_operator_user_id"   => $this->sess->user_id,
	    "integrationwim_operator_user_name" => $this->sess->user_name,
	    "integrationwim_operator_datetime"  => date("Y-m-d H:i:s", strtotime("+1 hour")),
	  );

	  // echo "<pre>";
	  // var_dump($dataforupdate);die();
	  // echo "<pre>";

	  $update = $this->m_wimreport->updateitwsnow($itws_transID, $dataforupdate);
	    if ($update) {
	      echo json_encode(array("code" => 200, "message" => "Successfully update data"));
	    }else {
	      echo json_encode(array("code" => 400, "message" => "Failed update data"));
	    }
	}

	function getDriverItws(){
	  $keyword     = $_POST['keyword'];
	  $data = $this->m_wimreport->getDataDriverItws($keyword);

	  $datafix = array();
	  if (sizeof($data) > 0) {
	    $datafix = array_map('current', $data);
	  }
	  echo json_encode(array("data" => $datafix));
	}

	function getDriverItwsValue(){
	  $keyword = $_POST['driveritwsid'];
	  $data    = $this->m_wimreport->getThisDriverItws($keyword);

	  if (sizeof($data) > 0) {
	    echo json_encode(array("data" => $data));
	  }else {
	    echo json_encode(array("data" => 0));
	  }
	}

	// OTHER PORT
	function otherport(){
	  ini_set('display_errors', 1);

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
	  $user_id_fix     = 4408;

	  $this->db->select("vehicle.*, user_name");
	  $this->db->order_by("vehicle_no", "asc");
	  $this->db->where("vehicle_status <>", 3);

	  if ($privilegecode == 0) {
	    $this->db->where("vehicle_user_id", $user_id_fix);
	  } else if ($privilegecode == 1) {
	    $this->db->where("vehicle_user_id", $user_parent);
	  } else if ($privilegecode == 2) {
	    $this->db->where("vehicle_user_id", $user_parent);
	  } else if ($privilegecode == 3) {
	    $this->db->where("vehicle_user_id", $user_parent);
	  } else if ($privilegecode == 4) {
	    $this->db->where("vehicle_user_id", $user_parent);
	  } else if ($privilegecode == 5) {
	    $this->db->where("vehicle_company", $user_company);
	  } else if ($privilegecode == 6) {
	    $this->db->where("vehicle_company", $user_company);
	  } else if ($privilegecode == 7) {
	    $this->db->where("vehicle_user_id", $user_id_fix);
	  } else if ($privilegecode == 8) {
	    $this->db->where("vehicle_user_id", $user_id_fix);
	  }else if ($privilegecode == 11) {
	    $this->db->where("vehicle_user_id", $user_id_fix);
	  }else {
	    $this->db->where("vehicle_no", 99999);
	  }

	  $this->db->join("user", "vehicle_user_id = user_id", "left outer");
	  $q = $this->db->get("vehicle");

	  if ($q->num_rows() == 0) {
	    redirect(base_url());
	  }

	  $rows          = $q->result_array();
	  $rows_company  = $this->get_company_bylevel();

	  $dataClient    = $this->m_wimreport->allDataClient();
	  $dataMaterial  = $this->m_wimreport->allDataMaterial();
	  $streetRom     = $this->m_wimreport->getstreet_now(3);
	  $streetPort    = $this->m_wimreport->getstreet_now(4);
	  $alldriveritws = $this->m_wimreport->alldriveritws();

	  $data_rom = array();
	  for ($i=0; $i < sizeof($streetRom); $i++) {
	    if ($streetRom[$i]['street_type'] == 3) {
	      array_push($data_rom, array(
	        "street_id"   => $streetRom[$i]['street_id'],
	        "street_name" => str_replace(",", "", $streetRom[$i]['street_name']),
	      ));
	    }
	  }

	  $data_port = array();
	  for ($i=0; $i < sizeof($streetPort); $i++) {
	    if ($streetPort[$i]['street_type'] == 4) {
	      array_push($data_port, array(
	        "street_id"   => $streetPort[$i]['street_id'],
	        "street_name" => str_replace(",", "", $streetPort[$i]['street_name']),
	      ));
	    }
	  }

	  // echo "<pre>";
	  // var_dump($data_port);die();
	  // echo "<pre>";

	  $this->params["vehicles"]        = $rows;
	  $this->params["rcompany"]        = $rows_company;
	  $this->params["data_client"]     = $dataClient;
	  $this->params["data_material"]   = $dataMaterial;
	  $this->params["data_rom"]			   = $data_rom;
	  $this->params["data_port"]			 = $data_port;
	  $this->params["datadriveritws"]	 = $alldriveritws;

	  //$this->params["data"] 		    = $result;
	  $this->params['code_view_menu'] = "monitoring";

	  $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	  $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

	  if ($privilegecode == 11 || $user_id == 5024) {
	    $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
	    $this->params["content"]        = $this->load->view('newdashboard/wim/otherport/v_home_otherport', $this->params, true);
	    $this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
	  }else {
	    $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	    $this->params["content"]        = $this->load->view('newdashboard/wim/otherport/v_home_otherport', $this->params, true);
	    $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	  }
	}

	function search_report_otherport(){
		$company          = $this->input->post("company");
		$vehicle          = $this->input->post("vehicle");
		$startdate        = $this->input->post("startdate");
		$shour            = "00:00:00";
		$enddate          = $this->input->post("enddate");
		$ehour            = "23:59:59";
		$statuswim        = $this->input->post("statuswim");
		$modewim          = $this->input->post("modewim");
		$periode          = $this->input->post("periode");
		$first_load       = $this->input->post("first_load");
		$transactionid    = $this->input->post("transactionid_select");

		// KONDISI TRANSACTION ID  START
		if ($transactionid != "") {
			$findthissymbol = array(".","-","_","!");
			$symbolfounded  = (str_replace($findthissymbol, '', $transactionid) != $transactionid);
			if ($symbolfounded) {
				$transactionidfix    = 0;
				$callback['error']   = true;
				$callback['message'] = "Gunakan koma sebagai pembatas untuk mencari lebih dari satu Transaction ID";
				echo json_encode($callback);
				return;
			}else {
				$transactionidfix = $transactionid;
			}
		}else {
			$transactionidfix = 0;
		}

		// echo "<pre>";
		// var_dump($transactionidfix);die();
		// echo "<pre>";
		// KONDISI TRANSACTION ID END


		/* $sdate     = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00") + 60*60*1);
		$edate     = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":00") + 60*60*1); */

		$sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
		$edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

		$nowdate   = date("Y-m-d");
		$nowday    = date("d");
		$nowmonth  = date("m");
		$nowyear   = date("Y");
		$lastday   = date("t");

		// if ($first_load == 1) {
			$sdate1 = date("Y-m-d");
			$sdate2 = "00:00:00";

			$edate1 = date("Y-m-d");
			$edate2 = "23:59:59";

			$sdate = $sdate1." ".$sdate2;
			$edate = $edate1." ".$edate2;
		// }else {
		// 	if($periode == "custom"){
		// 		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
		// 		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		// 	}else if($periode == "yesterday"){
		// 		$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
		// 		$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		// 	}else if($periode == "last7"){
		// 		/* $nowday = $nowday - 1;
		// 		$firstday = $nowday - 7;
		// 		if($nowday <= 7){
		// 			$firstday = 1;
		// 		}
		// */
		// 		/* $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
		// 		$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59")); */
	  //
		// 		$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-7days"));
		// 		$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
	  //
		// 	}else if($periode == "last30"){
		// 		/* $firstday = "1";
		// 		$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
		// 		$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59")); */
	  //
		// 		$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-30days"));
		// 		$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
	  //
		// 	}else if($periode == "today"){
		// 		$sdate1 = date("Y-m-d");
		// 		$sdate2 = "00:00:00";
	  //
		// 		$edate1 = date("Y-m-d");
		// 		$edate2 = "23:59:59";
	  //
		// 		$sdate = $sdate1." ".$sdate2;
		// 		$edate = $edate1." ".$edate2;
		// 	}else{
	  //
		// 		$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
		// 		$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		// 	}
		// }

		$m1      = date("F", strtotime($sdate));
		$year    = date("Y", strtotime($sdate));
		$report = "historikal_integrationwim_unit_";

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

		//print_r($sdate." ".$edate);exit();
		$dbtable = "historikal_integrationwim_unit";
		//$getreport            = $this->m_wimreport->getreportnow($dbtable, $vehicle, $statuswim, $sdate, $edate);

			$this->dbreport = $this->load->database("tensor_report", true);

	    // if ($first_load == 1) {
	      $this->dbreport->limit(100, 0);
	    // }else {
	    //   if ($transactionidfix != 0) {
	    //     $this->dbreport->where_in("integrationwim_transactionID", $transactionidfix);
	    //   }else {
	    //     if ($vehicle != "all") {
	    //       $this->dbreport->where("integrationwim_TruckID", $vehicle);
	    //     }
	    //
	    //     if ($modewim != "all") {
	    //       $this->dbreport->where("integrationwim_status", $modewim);
	    //     }
	    //
	    //     if ($statuswim != "all") {
	    //       $this->dbreport->where("integrationwim_operator_status", $statuswim);
	    //     }
	    //
	    //     $this->dbreport->where("integrationwim_PenimbanganStartLocal >= ", $sdate);
	    //     $this->dbreport->where("integrationwim_PenimbanganFinishLocal <= ", $edate);
	    //   }
	    // }

			//$this->dbreport->where("integrationwim_flag", 0);//bukan data dihapus
			// $this->dbreport->order_by("integrationwim_operator_status", 0); // INI DIAKTIFKAN SESUAI PERMINTAAN
	    $this->dbreport->where("integrationwim_dumping_fms_port !=", "");//bukan data dihapus
	    $this->dbreport->where("integrationwim_dumping_fms_port !=", "PORT BIB");//bukan data dihapus
			$this->dbreport->order_by("integrationwim_PenimbanganStartLocal", "DESC");
			$q = $this->dbreport->get($dbtable);
			$getreport = $q->result_array();

			//print_r($sdate." ".$edate." ".$dbtable);

		$this->params['data'] = $getreport;

		//print_r($getreport);exit();
		// $dbtable.'-'.$vehicle.'-'.$sdate.'-'.$edate
		// echo "<pre>";
		// var_dump($getreport);die();
		// echo "<pre>";

		$html = $this->load->view("newdashboard/wim/otherport/v_otherport_result", $this->params, true);
		$callback['error'] = false;
		$callback['html']  = $html;
		$callback['data']  = $getreport;
		echo json_encode($callback);
	}

	function itwsotherport_update_data(){
	  $itws_transID = $this->input->post('itws_transID');
	  $itws_gross_manual   = $this->input->post('itws_gross_manual');

	  if ($itws_gross_manual == "" || $itws_gross_manual == "0") {
	    $callback['error'] = true;
	    $callback['message'] = "Harap mengisi Gross terlebih dahulu";
	    echo json_encode($callback);
	    return;
	  }

	  $dataforupdate = array(
	    "integrationwim_gross_manual"        => $itws_gross_manual,
	    "integrationwim_otherport_status"    => 1,
	    "integrationwim_otherport_user_id"   => $this->sess->user_id,
	    "integrationwim_otherport_user_name" => $this->sess->user_name,
	    "integrationwim_otherport_datetime"  => date("Y-m-d H:i:s")
	  );

	  // echo "<pre>";
	  // var_dump($dataforupdate);die();
	  // echo "<pre>";

	  $update = $this->m_wimreport->updateitwsnow($itws_transID, $dataforupdate);
	    if ($update) {
	      echo json_encode(array("code" => 200, "message" => "Successfully update data"));
	    }else {
	      echo json_encode(array("code" => 400, "message" => "Failed update data"));
	    }
	}

	function recallThisVehicleotherport() {
	  $keyword     = $_POST['vehicleno'];
	  $dataRecall  = $this->m_wimreport->recallToLastOtherPort($keyword);
	  $company     = $dataRecall[0]['integrationwim_haulingContractor'];
	  $company_fix = "";

	  if ($company == "HTM") {
	    $company_fix = "MKS";
	  }elseif ($company == "RAM") {
	    $company_fix = "RAMB";
	  }elseif ($company == "STL") {
	    $company_fix = "STLI";
	  }elseif ($company == "GEC") {
	    $company_fix = "GECL";
	  }else {
	    $company_fix = $company;
	  }

	  $alldriveritws = $this->m_wimreport->driveritwsbycompany($company_fix);

	  // echo "<pre>";
	  // var_dump($alldriveritws);die();
	  // echo "<pre>";

	  if (sizeof($dataRecall) > 0) {
	    $dataforprocess  = $dataRecall[0];
	    echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_driver_itws" => $alldriveritws));
	  }else {
	    echo json_encode(array("code" => 400));
	  }

	  // if (sizeof($dataRecall) > 0 && sizeof($dataRecall) > 1) {
	  //   $dataforprocess  = $dataRecall[0];
	  //   $datalastrecall  = $dataRecall[1];
	  //   // $datalastrecall2 = $dataRecall[2];
	  //   // echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => $datalastrecall, "data_lastrecall2" => $datalastrecall2));
	  //   echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => $datalastrecall));
	  // }elseif (sizeof($dataRecall) == 1) {
	  //   $dataforprocess  = $dataRecall[0];
	  //
	  //   echo json_encode(array("code" => 200, "data_forprocess" => $dataforprocess, "data_lastrecall" => array()));
	  // }else {
	  //   echo json_encode(array("code" => 400));
	  // }
	}

















}
