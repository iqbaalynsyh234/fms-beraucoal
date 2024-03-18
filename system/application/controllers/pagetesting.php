<?php
include "base.php";
setlocale(LC_ALL, 'IND');

class Pagetesting extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Pagetesting()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_poipoolmaster");
		$this->load->model("log_model");
		$this->load->model("m_securityevidence");
		$this->load->model("gpsmodel");
	}

  function portviewcss(){
  	ini_set('max_execution_time', '300');
  	set_time_limit(300);
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
  	}else{
  		$user_id_fix = $user_id;
  	}

  	$companyid                       = $this->sess->user_company;
  	$user_dblive                     = $this->sess->user_dblive;
  	$mastervehicle                   = $this->m_poipoolmaster->getmastervehicle();

  	$datafix                         = array();
  	$deviceidygtidakada              = array();
  	$statusvehicle['engine_on']  = 0;
  	$statusvehicle['engine_off'] = 0;

  	for ($i=0; $i < sizeof($mastervehicle); $i++) {
  		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
  		$auto_status   = $jsonautocheck->auto_status;

  		if ($jsonautocheck->auto_last_engine == "ON") {
  				$statusvehicle['engine_on'] += 1;
  			}else {
  				$statusvehicle['engine_off'] += 1;
  			}

  			if ($auto_status != "M") {
  				array_push($datafix, array(
  					"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
  					"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
  					"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
  					"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
  					"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
  					"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
  					"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
  					"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
  				));
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
  	$this->params['code_view_menu'] = "monitor";
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
  	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
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
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/pagetesting/v_testing_page_portcssview', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  }

	function quickcountviewcss(){
  	ini_set('max_execution_time', '300');
  	set_time_limit(300);
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
  	}else{
  		$user_id_fix = $user_id;
  	}

  	$companyid                       = $this->sess->user_company;
  	$user_dblive                     = $this->sess->user_dblive;
  	$mastervehicle                   = $this->m_poipoolmaster->getmastervehicle();

  	$datafix                         = array();
  	$deviceidygtidakada              = array();
  	$statusvehicle['engine_on']  = 0;
  	$statusvehicle['engine_off'] = 0;

  	for ($i=0; $i < sizeof($mastervehicle); $i++) {
  		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
  		$auto_status   = $jsonautocheck->auto_status;

  		if ($jsonautocheck->auto_last_engine == "ON") {
  				$statusvehicle['engine_on'] += 1;
  			}else {
  				$statusvehicle['engine_off'] += 1;
  			}

  			if ($auto_status != "M") {
  				array_push($datafix, array(
  					"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
  					"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
  					"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
  					"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
  					"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
  					"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
  					"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
  					"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
  				));
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
  	$this->params['code_view_menu'] = "monitor";
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
  	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
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
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/pagetesting/v_testing_page_quickcountview', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  }



}
