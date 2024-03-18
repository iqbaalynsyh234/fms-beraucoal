<?php
include "base.php";

class Maintenance extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Maintenance()
	{
		parent::Base();
    // DASHBOARD START
    $this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
    // DASHBOARD END
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
    $this->load->model("driver_model");
    $this->load->model("m_maintenance");
    $this->load->model("vehiclemodel");
		$this->load->model("log_model");
	}

  function index(){
		if(! isset($this->sess->user_type)){
			redirect('dashboard');
		}

    $privilegecode                       = $this->sess->user_id_role;
    $masterdatavehicle                   = $this->m_maintenance->getallvehicle();

    // GET ASSIGNED VEHICLE STATUS
  	$this->params["datavehicle"] 				 = $masterdatavehicle;
    $this->params['privilegecode']       = $privilegecode;
		$this->params['code_view_menu']      = "configuration";

  	// echo "<pre>";
  	// var_dump($this->params["workshop"]);die();
  	// echo "<pre>";

  	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  	if ($privilegecode == 1) {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_new', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  	}elseif ($privilegecode == 3) {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_new', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  	}elseif ($privilegecode == 4) {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_new', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  	}elseif ($privilegecode == 5) {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_new', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  	}elseif ($privilegecode == 6) {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_new', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  	}else {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_new', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  	}
  }

  function maintenance_type(){
    $maintenance_type                   = $this->m_maintenance->getallmaintenancetype();
    // echo "<pre>";
    // var_dump($maintenance_type);die();
    // echo "<pre>";
    echo json_encode(array("code" => 200, "msg" => "success", "data" => $maintenance_type));
  }

  function maintenance_cat(){
    $maintenance_type = $this->m_maintenance->getallmaintenancetype();
    $maintenance_cat  = $this->m_maintenance->getallmaintenancecat();
    // echo "<pre>";
    // var_dump($maintenance_type);die();
    // echo "<pre>";
    echo json_encode(array("code" => 200, "msg" => "success", "data" => $maintenance_cat, "dataType" => $maintenance_type));
  }

  function maintenance_type_save(){
    $type_name = $this->input->post('type_name');

    $data = array(
      "maintenance_type_name"       => $type_name,
      "maintenance_type_creator_id" => $this->sess->user_id
    );

    // echo "<pre>";
    // var_dump($data);die();
    // echo "<pre>";

    $insert = $this->m_maintenance->insertData("maintenance_type", $data);
      if ($insert) {
        $maintenance_type = $this->m_maintenance->getallmaintenancetype();
				$contentlog       = "Maintenance Type Successfuly Inserted";
				$insertlog        = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
        echo json_encode(array("code" => 200, "msg" => "success", "data" => $maintenance_type));
      }else {
        echo json_encode(array("code" => 400, "msg" => "failed"));
      }
  }

  function deleteMaintenanceType(){
    $id = $_POST['id'];

    $data = array(
      "maintenance_type_flag" => 1
    );

    // echo "<pre>";
    // var_dump($id);die();
    // echo "<pre>";

    $delete = $this->m_maintenance->deleteData("maintenance_type", $id, $data);
      if ($delete) {
        $maintenance_type = $this->m_maintenance->getallmaintenancetype();
				$contentlog       = "Maintenance Type Successfuly Deleted";
				$insertlog        = $this->log_model->insertlog($this->sess->user_name, $contentlog, "DELETE", "FUNCTIONAL");
        echo json_encode(array("code" => 200, "msg" => "success", "data" => $maintenance_type));
      }else {
        echo json_encode(array("code" => 400, "msg" => "failed"));
      }
  }

  function maintenance_cat_save(){
    $cat_name                = $this->input->post('cat_name');
    $maintenance_type_in_cat = $this->input->post('maintenance_type_in_cat');


    $data = array(
      "maintenance_cat_typeid"      => $maintenance_type_in_cat,
      "maintenance_cat_name"        => $cat_name,
      "maintenance_cat_creatorid"   => $this->sess->user_id
    );

    // echo "<pre>";
    // var_dump($data);die();
    // echo "<pre>";

    $insert = $this->m_maintenance->insertData("maintenance_category", $data);
      if ($insert) {
        $maintenance_type = $this->m_maintenance->getallmaintenancetype();
        $maintenance_cat  = $this->m_maintenance->getallmaintenancecat();

				$contentlog       = "Maintenance Category Successfuly Inserted";
				$insertlog        = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
        echo json_encode(array("code" => 200, "msg" => "success", "data" => $maintenance_cat, "dataType" => $maintenance_type));
      }else {
        echo json_encode(array("code" => 400, "msg" => "failed"));
      }
  }

  function deleteMaintenanceCat(){
    $id = $_POST['id'];

    $data = array(
      "maintenance_cat_flag" => 1
    );

    // echo "<pre>";
    // var_dump($id);die();
    // echo "<pre>";

    $delete = $this->m_maintenance->deleteDataCategory("maintenance_category", $id, $data);
      if ($delete) {
        $maintenance_type = $this->m_maintenance->getallmaintenancetype();
        $maintenance_cat  = $this->m_maintenance->getallmaintenancecat();

				$contentlog       = "Maintenance Category Successfuly Deleted";
				$insertlog        = $this->log_model->insertlog($this->sess->user_name, $contentlog, "DELETE", "FUNCTIONAL");
        echo json_encode(array("code" => 200, "msg" => "success", "data" => $maintenance_cat, "dataType" => $maintenance_type));
      }else {
        echo json_encode(array("code" => 400, "msg" => "failed"));
      }
  }

  function getTypeCatMaintenance(){
    $vdevice          = $_POST['vdevice'];
    $datavehicle      = $this->m_maintenance->getallvehicleByID($vdevice);
    $maintenance_type = $this->m_maintenance->getallmaintenancetype();
    $maintenance_cat  = $this->m_maintenance->getallmaintenancecat();
    echo json_encode(array("code" => 200, "msg" => "success", "data" => $maintenance_cat, "dataType" => $maintenance_type, "datavehicle" => $datavehicle));
  }

	function categorybyType(){
		$mTypeID         = $_POST['mTypeID'];
		$maintenance_cat = $this->m_maintenance->getCategoryByType($mTypeID);
		echo json_encode(array("code" => 200, "msg" => "success", "dataCat" => $maintenance_cat));
	}

	function savemaintenance_form(){
		$estimatedornot             = $this->input->post('estimatedornot');
		$mVehicle_form              = $this->input->post('mVehicle_form');
		$mVehicleDevice             = $this->input->post('mVehicleDevice');
		$mVehicleNo                 = $this->input->post('mVehicleNo');
		$mvehicle_mv03              = $this->input->post('mvehicle_mv03');
		$startdate                  = $this->input->post('startdate');
		$shour                      = $this->input->post('shour');
		$enddate                    = $this->input->post('enddate');
		$ehour                      = $this->input->post('ehour');
		$mNotes_form                = $this->input->post('mNotes_form');
		$mNotes_form                = $this->input->post('mNotes_form');
		$isotherscategory           = $this->input->post('isotherscategory');
		$maintenanceothers_category = $this->input->post('maintenanceothers_category');
		$mType_form                 = explode("|", $this->input->post('mType_form'));
		$mCat_form                  = explode("|", $this->input->post('mCat_form'));
		$startdatefinish 		        = date("Y-m-d", strtotime($startdate))." ".$shour.":00";
		$enddatefinish 		          = date("Y-m-d", strtotime($enddate))." ".$ehour.":59";

		if ($isotherscategory == 1) {
			$maintenanceothers_category = $maintenanceothers_category;
			$mCatIDFix                  = 1000;
			$mCatNameFix                = $maintenanceothers_category;
		}else {
			$maintenanceothers_category = "not others";
			$mCatIDFix                  = $mCat_form[0];
			$mCatNameFix                = $mCat_form[1];
		}

		if ($estimatedornot == 0) {
			$data = array(
				"breakdown_start_time"          => $startdatefinish,
				"breakdown_vehicle_device"      => $mVehicleDevice,
				"breakdown_vehicle_no"          => $mVehicleNo,
				"breakdown_vehicle_mv03"        => $mvehicle_mv03,
				"breakdown_info"                => $mNotes_form,
				"breakdown_creator_id"          => $this->sess->user_company,
				"breakdown_creator_name"        => $this->sess->user_name,
				"breakdown_kat_id"              => $mCatIDFix,
				"breakdown_kat_name"            => $mCatNameFix,
				"breakdown_others_categoryid"   => $isotherscategory,
				"breakdown_others_categoryname" => $maintenanceothers_category,
				"breakdown_type_id"             => $mType_form[0],
				"breakdown_type_name"           => $mType_form[1],
				"breakdown_mode" 			          => "FMS",
			);
		}else {
			$duration_sec    = strtotime($enddatefinish) - strtotime($startdatefinish);
			 $secondsInAMinute = 60;
			 $secondsInAnHour  = 60 * $secondsInAMinute;
			 $secondsInADay    = 24 * $secondsInAnHour;

			 // extract days
			 $days = floor($duration_sec / $secondsInADay);

			 // extract hours
			 $hourSeconds = $duration_sec % $secondsInADay;
			 $hours = floor($hourSeconds / $secondsInAnHour);

			 // extract minutes
			 $minuteSeconds = $hourSeconds % $secondsInAnHour;
			 $minutes = floor($minuteSeconds / $secondsInAMinute);

			  if ($days > 0) {
				 	$durationfix = $days.' Day '. $hours.' Hour '. $minutes.' Min';
				}else {
					$durationfix = $hours.' Hour '. $minutes.' Min';
				}

			$data = array(
				"breakdown_start_time"          => $startdatefinish,
				"breakdown_finish_time"         => $enddatefinish,
				"breakdown_vehicle_device"      => $mVehicleDevice,
				"breakdown_vehicle_no"          => $mVehicleNo,
				"breakdown_vehicle_mv03"        => $mvehicle_mv03,
				"breakdown_info"                => $mNotes_form,
				"breakdown_creator_id"          => $this->sess->user_id,
				"breakdown_creator_name"        => $this->sess->user_name,
				"breakdown_others_categoryid"   => $isotherscategory,
				"breakdown_others_categoryname" => $maintenanceothers_category,
				"breakdown_kat_id"              => $mCatIDFix,
				"breakdown_kat_name"            => $mCatNameFix,
				"breakdown_type_id"             => $mType_form[0],
				"breakdown_type_name"           => $mType_form[1],
				"breakdown_duration_sec"        => $duration_sec,
				"breakdown_duration" 	          => $durationfix,
				"breakdown_status" 			        => "1",
				"breakdown_mode" 			          => "FMS",
			);
		}

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$insert = $this->m_maintenance->insertDataUmum("ts_driver_breakdown", $data, "webtracking_ts");
			if ($insert) {
				$contentlog       = "Maintenance Breakdown Successfuly Inserted";
				$insertlog        = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
				echo json_encode(array("code" => 200, "msg" => "success"));
			}else {
				echo json_encode(array("code" => 400, "msg" => "failed"));
			}
	}

	function onprocess(){
		$dataonprocess                  = $this->m_maintenance->getonprocess("ts_driver_breakdown");
		$privilegecode                  = $this->sess->user_id_role;

		$this->params['privilegecode']  = $privilegecode;
		$this->params['dataonprocess']  = $dataonprocess;
		$this->params['code_view_menu'] = "configuration";

		// echo "<pre>";
		// var_dump($dataonprocess);die();
		// echo "<pre>";

  	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  	if ($privilegecode == 1) {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_onprocess', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  	}elseif ($privilegecode == 3) {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_onprocess', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  	}elseif ($privilegecode == 4) {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_onprocess', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  	}elseif ($privilegecode == 5) {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_onprocess', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  	}elseif ($privilegecode == 6) {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_onprocess', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  	}else {
  		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  		$this->params["content"]        = $this->load->view('newdashboard/maintenance/v_home_maintenance_onprocess', $this->params, true);
  		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  	}
	}

	function completedonprocess(){
		$breakdown_id         = $this->input->post('breakdown_id');
		$enddate              = $this->input->post('enddate');
		$ehour                = $this->input->post('ehour');
		$enddatefinish 		    = date("Y-m-d", strtotime($enddate))." ".$ehour.":59";

		$databreakdown        = $this->m_maintenance->getonprocessbyID($breakdown_id);

		$startdatefinish 			= $databreakdown[0]['breakdown_start_time'];

		if ($enddatefinish < $startdatefinish) {
			echo json_encode(array("code" => 400, "msg" => "failed2"));
			return false;
		}


	 $duration_sec    = strtotime($enddatefinish) - strtotime($startdatefinish);
	 $secondsInAMinute = 60;
	 $secondsInAnHour  = 60 * $secondsInAMinute;
	 $secondsInADay    = 24 * $secondsInAnHour;

	 // extract days
	 $days = floor($duration_sec / $secondsInADay);

	 // extract hours
	 $hourSeconds = $duration_sec % $secondsInADay;
	 $hours = floor($hourSeconds / $secondsInAnHour);

	 // extract minutes
	 $minuteSeconds = $hourSeconds % $secondsInAnHour;
	 $minutes = floor($minuteSeconds / $secondsInAMinute);

		if ($days > 0) {
			$durationfix = $days.' Day '. $hours.' Hour '. $minutes.' Min';
		}else {
			$durationfix = $hours.' Hour '. $minutes.' Min';
		}

		$data = array(
			"breakdown_status" 		   => 1,
			"breakdown_finish_time"  => $enddatefinish,
			"breakdown_duration_sec" => $duration_sec,
			"breakdown_duration"     => $durationfix
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$update = $this->m_maintenance->updateOnProcess("ts_driver_breakdown", "breakdown_id", $breakdown_id, $data);
			if ($update) {
				$contentlog       = "Maintenance Successfuly Completed ";
				$insertlog        = $this->log_model->insertlog($this->sess->user_name, $contentlog, "EDIT", "FUNCTIONAL");
				echo json_encode(array("code" => 200, "msg" => "success"));
			}else {
				echo json_encode(array("code" => 400, "msg" => "failed"));
			}
	}

	function deleteBreakdown(){
		$breakdownid = $_POST['id'];

		$data = array(
			"breakdown_flag" => 1
		);

		$delete = $this->m_maintenance->deleteDataBreakdown("ts_driver_breakdown", "breakdown_id", $breakdownid, $data);
			if ($delete) {
				$contentlog       = "Maintenance Successfuly Deleted ";
				$insertlog        = $this->log_model->insertlog($this->sess->user_name, $contentlog, "DELETE", "FUNCTIONAL");
				echo json_encode(array("code" => 200, "msg" => "success"));
			}else {
				echo json_encode(array("code" => 400, "msg" => "failed"));
			}
	}

	function getforalert(){
		$dataonprocess      = $this->m_maintenance->getonprocess("ts_driver_breakdown");
		$privilegecode      = $this->sess->user_id_role;

		$privilegecode      = $privilegecode;
		$totaldataonprocess = sizeof($dataonprocess);
		$dataonprocess      = $dataonprocess;

		// echo "<pre>";
		// var_dump($this->params['dataonprocess']);die();
		// echo "<pre>";

		echo json_encode(array("data" => $dataonprocess, "totaldata" => $totaldataonprocess));
	}






















}
