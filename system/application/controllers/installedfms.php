<?php
include "base.php";

class Installedfms extends Base
{

	function Installedfms()
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
		$this->load->model("m_installedfms");

		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
	}

	function index()
	{
		ini_set('display_errors', 1);
    if (! isset($this->sess->user_type))
  	{
  		redirect(base_url());
  	}

  	$user_id       = $this->sess->user_id;
  	$user_parent   = $this->sess->user_parent;
  	$privilegecode = $this->sess->user_id_role;
  	$user_company  = $this->sess->user_company;

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

    $mastervehicle = $this->m_installedfms->getMastervehicle();
    $company       = $this->dashboardmodel->getcompany_byowner($privilegecode);

    // echo "<pre>";
  	// var_dump($company);die();
  	// echo "<pre>";


		$this->params['code_view_menu'] = "report";
		$this->params['privilegecode']  = $privilegecode;

    $this->params['vehicles']       = $mastervehicle;
    $this->params['company']        = $company;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

    if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/installedfms/v_home_installedfms', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/installedfms/v_home_installedfms', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/installedfms/v_home_installedfms', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/installedfms/v_home_installedfms', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/installedfms/v_home_installedfms', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/installedfms/v_home_installedfms', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/installedfms/v_home_installedfms', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

  function search_vehiclebycontractor(){
    $privilegecode = $this->sess->user_id_role;
		$company       = $this->input->post("companyid");

    $mastervehicle = $this->m_installedfms->getmastervehiclebycontractor($company);
    $company       = $this->dashboardmodel->getcompany_byowner($privilegecode);

    // TOTAL INSTALLED GPS
    $total_gps = sizeof($mastervehicle);

    // TOTAL MDVR
    $total_mdvr = 0;
      for ($i=0; $i < sizeof($mastervehicle); $i++) {
        $mdvr = $mastervehicle[$i]['vehicle_mv03'];
          if ($mdvr != 0000) {
            $total_mdvr += 1;
          }
      }

    // TOTAL FUEL SENSOR
    $total_fuelsensor = 0;
      for ($i=0; $i < sizeof($mastervehicle); $i++) {
        $fuel_sensor = $mastervehicle[$i]['vehicle_sensor'];
          if ($fuel_sensor == "Ultrasonic") {
            $total_fuelsensor += 1;
          }
      }

		// echo "<pre>";
		// var_dump($mastervehicle);die();
		// echo "<pre>";

		$this->params['data']             = $mastervehicle;
		$this->params["rcompany"]         = $company;
    $this->params["total_gps"]        = $total_gps;
    $this->params["total_mdvr"]       = $total_mdvr;
    $this->params["total_fuelsensor"] = $total_fuelsensor;

		$html = $this->load->view("newdashboard/installedfms/v_installedfms_result", $this->params, true);
		$callback['error'] = false;
		$callback['html']  = $html;
		$callback['data']  = $mastervehicle;
		echo json_encode($callback);
	}




























}
