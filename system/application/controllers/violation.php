<?php
include "base.php";
setlocale(LC_ALL, 'IND');

class Violation extends Base {

	function Violation()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
    $this->load->model("dashboardmodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("log_model");
		$this->load->model("m_violation");
		$this->load->model("m_poipoolmaster");
	}

	function index(){
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

  	$companyid                   = $this->sess->user_company;
  	$user_dblive                 = $this->sess->user_dblive;
  	$mastervehicle               = $this->m_poipoolmaster->getmastervehicleforheatmap();
		$violationmaster             = $this->m_violation->getviolationmaster();

  	$datafix                     = array();
  	$deviceidygtidakada          = array();
  	$statusvehicle['engine_on']  = 0;
  	$statusvehicle['engine_off'] = 0;

  	for ($i=0; $i < sizeof($mastervehicle); $i++) {
  		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
				if (isset($jsonautocheck->auto_status)) {
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

		// GET ROM ROAD
		$romRoad                  = $this->m_poipoolmaster->getstreet_now2(5);
		$this->params['rom_road'] = $romRoad;

		// echo "<pre>";
  	// var_dump($romRoad);die();
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

  	$this->params['resultactive']    = $this->dashboardmodel->vehicleactive();
  	$this->params['resultexpired']   = $this->dashboardmodel->vehicleexpired();
  	$this->params['resulttotaldev']  = $this->dashboardmodel->totaldevice();
  	$this->params['mapsetting']      = $this->m_poipoolmaster->getmapsetting();
  	$this->params['poolmaster']      = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$this->params['violationmaster'] = $violationmaster;

  	// echo "<pre>";
  	// var_dump($this->params['violationmaster']);die();
  	// echo "<pre>";

  	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  		if ($privilegecode == 1) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  		}elseif ($privilegecode == 2) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  		}elseif ($privilegecode == 3) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  		}elseif ($privilegecode == 4) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  		}elseif ($privilegecode == 5) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  		}elseif ($privilegecode == 6) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  		}else {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  		}
	}

  function index_old(){
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

  	$companyid                   = $this->sess->user_company;
  	$user_dblive                 = $this->sess->user_dblive;
  	$mastervehicle               = $this->m_poipoolmaster->getmastervehicleforheatmap();
		$violationmaster             = $this->m_violation->getviolationmaster();

  	$datafix                     = array();
  	$deviceidygtidakada          = array();
  	$statusvehicle['engine_on']  = 0;
  	$statusvehicle['engine_off'] = 0;

  	for ($i=0; $i < sizeof($mastervehicle); $i++) {
  		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
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

  	$this->params['resultactive']    = $this->dashboardmodel->vehicleactive();
  	$this->params['resultexpired']   = $this->dashboardmodel->vehicleexpired();
  	$this->params['resulttotaldev']  = $this->dashboardmodel->totaldevice();
  	$this->params['mapsetting']      = $this->m_poipoolmaster->getmapsetting();
  	$this->params['poolmaster']      = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$this->params['violationmaster'] = $violationmaster;

  	// echo "<pre>";
  	// var_dump($this->params['violationmaster']);die();
  	// echo "<pre>";

  	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  		if ($privilegecode == 1) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  		}elseif ($privilegecode == 2) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  		}elseif ($privilegecode == 3) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  		}elseif ($privilegecode == 4) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  		}elseif ($privilegecode == 5) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  		}elseif ($privilegecode == 6) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  		}else {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violation', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  		}
	}

  function km_quickcount_new(){
		$simultantype 				         = $_POST['simultantype'];
		$lasttime 				             = $_POST['lasttime_violation'];
		$contractor 				           = $_POST['contractor'];
		$violationmasterselect 				 = $_POST['violationmaster'];
		$alarmtypefromaster            = array();
		$dataoverspeed 								 = array();
		$datafatigue                   = array();
		$dataKmMuatanFix               = array();
		$dataKmKosonganFix             = array();
		$violationmix                  = array();

		$street_onduty = array(
								// "PORT BIB","PORT BIR","PORT TIA",
								//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
								// "ROM A1","ROM B1","ROM B2","ROM B3","ROM EST",
								// "ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
								"ROM B3 ROAD",
								//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
								//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",

								"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5",
								"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
								"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
								"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5",

								// "BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
								// "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
								"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
								"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
								"PORT BIB - Antrian","Port BIB - Antrian"
							);

		if ($violationmasterselect == 6) {
			$alarmtypefromaster[] = 9999;
		}else {
			if ($violationmasterselect != "0") {
				$alarmbymaster = $this->m_violation->getalarmbytype($violationmasterselect);
				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
				}
			}
		}

		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id, user_dblive");
		$this->db->order_by("user_id","asc");
		$this->db->where("user_id", 4408);
		$q         = $this->db->get("user");
		$row       = $q->row();
		$total_row = count($row);

		$nowtime          = date("Y-m-d H:i:s");
		$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
		$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-60second"));

		$startdate        = $last_fiveminutes;
		$enddate          = $nowtime_wita;
		$sdate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
		$edate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

		//print_r($sdate." ".$edate);exit();
		if(count($row)>0){
			$user_dblive = $row->user_dblive;
		}

		$data_all_alert = array();

			// for ($i=1; $i < 6; $i++) {
				// $this->dbalert = $this->load->database("webtracking_gps_temanindobara_live_".$i, TRUE);
				$this->dbalert = $this->load->database("webtracking_gps_demo_live", TRUE);
				$this->dbalert->where("gps_time >=", $sdate);
						//$this->dbalert->where("gps_time <=", $edate);
				$this->dbalert->where("gps_speed >=", 11.3);  // >= 21 kph
				//$this->dbalert->where("gps_speed_status", 1);
				$this->dbalert->where("gps_alert", "Speeding Alarm");
				// $this->dbalert->where("gps_notif", 0); //belum ke send
				//$this->dbalert->limit(5); //limit
				$this->dbalert->order_by("gps_time","asc");
				$this->dbalert->group_by(array("gps_name"));
				$q           = $this->dbalert->get("gps_alert");
				$rows        = $q->result();
					if (sizeof($rows) > 0) {
						for ($j=0; $j < sizeof($rows); $j++) {
							array_push($data_all_alert, array(
								"gps_id"             => $rows[$j]->gps_id,
					      "gps_name"           => $rows[$j]->gps_name,
					      "gps_host"           => $rows[$j]->gps_host,
					      "gps_type"           => $rows[$j]->gps_type,
					      "gps_utc_coord"      => $rows[$j]->gps_utc_coord,
					      "gps_status"         => $rows[$j]->gps_status,
					      "gps_latitude"       => $rows[$j]->gps_latitude,
					      "gps_ns"             => $rows[$j]->gps_ns,
					      "gps_longitude"      => $rows[$j]->gps_longitude,
					      "gps_ew"             => $rows[$j]->gps_ew,
					      "gps_speed"          => $rows[$j]->gps_speed,
					      "gps_course"         => $rows[$j]->gps_course,
					      "gps_utc_date"       => $rows[$j]->gps_utc_date,
					      "gps_mvd"            => $rows[$j]->gps_mvd,
					      "gps_mv"             => $rows[$j]->gps_mv,
					      "gps_cs"             => $rows[$j]->gps_cs,
					      "gps_msg_ori"        => $rows[$j]->gps_msg_ori,
					      "gps_time"           => $rows[$j]->gps_time,
					      "gps_latitude_real"  => $rows[$j]->gps_latitude_real,
					      "gps_longitude_real" => $rows[$j]->gps_longitude_real,
					      "gps_odometer"       => $rows[$j]->gps_odometer,
					      "gps_workhour"       => $rows[$j]->gps_workhour,
					      "gps_geofence"       => $rows[$j]->gps_geofence,
					      "gps_last_road_type" => $rows[$j]->gps_last_road_type,
					      "gps_speed_limit"    => $rows[$j]->gps_speed_limit,
					      "gps_speed_status"   => $rows[$j]->gps_speed_status,
					      "gps_notif"          => $rows[$j]->gps_notif,
					      "gps_alert"          => $rows[$j]->gps_alert,
					      "gps_view"           => $rows[$j]->gps_view,
					      "gps_inserttime"     => $rows[$j]->gps_inserttime,
							));
						}
					}
			// }

			$rows        = $data_all_alert;
			$total_alert = count($rows);

			// echo "<pre>";
			// var_dump($data_all_alert);die();
			// echo "<pre>";

			$user_level      = $this->sess->user_level;
	    $user_parent     = $this->sess->user_parent;
			$user_company    = $this->sess->user_company;
			$user_subcompany = $this->sess->user_subcompany;
			$user_group      = $this->sess->user_group;
			$user_subgroup   = $this->sess->user_subgroup;
			$user_dblive 	   = $this->sess->user_dblive;
	    $privilegecode 	 = $this->sess->user_id_role;
			$user_id_fix     = $this->sess->user_id;

			if($privilegecode == 1){
				$contractor = $contractor;
			}else if($privilegecode == 2){
				$contractor = $contractor;
			}else if($privilegecode == 3){
				$contractor = $contractor;
			}else if($privilegecode == 4){
				$contractor = $contractor;
			}else if($privilegecode == 5){
				$contractor = $user_company;
			}else if($privilegecode == 6){
				$contractor = $user_company;
			}else if($privilegecode == 0){
				$contractor = $contractor;
			}else{
				$contractor = $contractor;
			}

			if($total_alert >0){
				$j = 1;
				for ($i=0;$i<count($rows);$i++){
					$title_name      = "OVERSPEED ALARM";
					$vehicle_device  = $rows[$i]['gps_name']."@".$rows[$i]['gps_host'];
					$data_vehicle    = $this->getvehicle2($vehicle_device, $contractor);

						if ($data_vehicle) {
							// echo "<pre>";
							// var_dump($data_vehicle);die();
							// echo "<pre>";

							$vehicle_id      = $data_vehicle->vehicle_id;
							$vehicle_no      = $data_vehicle->vehicle_no;
							$vehicle_name    = $data_vehicle->vehicle_name;
							$vehicle_company = $data_vehicle->vehicle_company;
							$vehicle_dblive  = $data_vehicle->vehicle_dbname_live;

							$driver_name = "-";

							// printf("===Process Alarm ID %s %s %s (%d/%d) \r\n", $rows[$i]->gps_id, $data_vehicle->vehicle_no, $data_vehicle->vehicle_device, $j, $total_alert);
							$skip_sent = 0;
							$position = $this->getPosition_other($rows[$i]['gps_longitude_real'],$rows[$i]['gps_latitude_real']);

								if(isset($position)){
									$ex_position = explode(",",$position->display_name);
									if(count($ex_position)>0){
										$position_name = $ex_position[0];
									}else{
										$position_name = $ex_position[0];
									}
								}else{
									$position_name = $position->display_name;
										$skip_sent = 1;
								}

									//filter in location array HAULING, ROM, PORT

									if (in_array($position_name, $street_onduty)){
										$skip_sent = 0;
									}else{
										$skip_sent = 1;
									}

							$gps_time   = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]['gps_time']))); //sudah wita
							$coordinate = $rows[$i]['gps_latitude_real'].",".$rows[$i]['gps_longitude_real'];
							//$url = "http://maps.google.com/maps?z=12&t=m&q=loc:".$coordinate;
							$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
							//https://www.google.com/maps/search/?api=1&query=-6.2915399,106.9660776 : ex
							$gpsspeed_kph = round($rows[$i]['gps_speed']*1.852,0);
							$direction    = $rows[$i]['gps_course'];
							$jalur        = $this->get_jalurname_new($direction);

							if($jalur == ""){
								$jalur = $rows[$i]['gps_last_road_type'];
							}

							$rowgeofence = $this->getGeofence_location_live($rows[$i]['gps_longitude_real'], $rows[$i]['gps_latitude_real'], $vehicle_dblive);

											if($rowgeofence == false){
												$geofence_id           = 0;
												$geofence_name         = "";
												$geofence_speed        = 0;
												$geofence_speed_muatan = "";
												$geofence_type         = "";
												$geofence_speed_limit  = 0;

											}else{
												$geofence_id           = $rowgeofence->geofence_id;
												$geofence_name         = $rowgeofence->geofence_name;
												$geofence_speed        = $rowgeofence->geofence_speed;
												$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
												$geofence_type         = $rowgeofence->geofence_type;

												if($jalur == "muatan"){
													$geofence_speed_limit = $geofence_speed_muatan;
												}else if($jalur == "kosongan"){
													$geofence_speed_limit = $geofence_speed;
												}else{
													$geofence_speed_limit = 0;

												}
											}
									// printf("===Position : %s Geofence : %s Jalur: %s \r\n", $position_name, $geofence_name, $jalur);
									// printf("===Speed : %s Limit : %s \r\n", $gpsspeed_kph, $geofence_speed_limit);

									if($gpsspeed_kph <= $geofence_speed_limit){
										$skip_sent = 1;
									}

									if($geofence_speed_limit == 0){
										$skip_sent = 1;
									}

									$gpsspeed_kph         = $gpsspeed_kph-3;
									$geofence_speed_limit = $geofence_speed_limit-3;

							if($skip_sent == 0){
								array_push($dataoverspeed, array(
									"isfatigue"          => "no",
									"jalur_name"         => $jalur,
									"vehicle_no"         => $vehicle_no,
									"vehicle_name"       => $vehicle_name,
									"vehicle_company"    => $vehicle_company,
									"violation" 				 => "Overspeed",
									"violation_type" 		 => "overspeed",
									"vehicle_device"     => $rows[$i]['gps_name'].'@'.$rows[$i]['gps_host'],
									"gps_latitude_real"  => $rows[$i]['gps_latitude_real'],
									"gps_longitude_real" => $rows[$i]['gps_longitude_real'],
									"gps_speed"          => $gpsspeed_kph,
									"gps_speed_limit"    => $geofence_speed_limit,
									"gps_time"           => $gps_time,
									"geofence"           => $geofence_name,
									"position"           => $position_name,
								));
							}
						}
						}
				}

				// $dataoverspeed = array(
				// 	array(
				// 		"geofence"           => "K30/M30 - 17",
				// 		"gps_latitude_real"  => "-3.659191",
				// 		"gps_longitude_real" => "115.649125",
				// 		"gps_speed"          => 56,
				// 		"gps_speed_limit"    => 30,
				// 		"gps_time"           => "29-05-2022 12:12:48",
				// 		"isfatigue"          => "yes",
				// 		"jalur_name"         => "muatan",
				// 		"position"           => "ROM B2 ROAD",
				// 		"vehicle_company"    => "1946",
				// 		"vehicle_device"     => "869926046535932@VT200",
				// 		"vehicle_name"       => "Hino 500",
				// 		"vehicle_no"         => "MKS 162",
				// 		"violation"          => "Fatigue Driving Alarm Level One",
				// 		"violation_type"     => "fatigue",
				// 	),
				// 	array(
				// 		"geofence"           => "K30/M30 - 17",
				// 		"gps_latitude_real"  => "-3.659191",
				// 		"gps_longitude_real" => "115.649125",
				// 		"gps_speed"          => 56,
				// 		"gps_speed_limit"    => 30,
				// 		"gps_time"           => "29-05-2022 12:12:48",
				// 		"isfatigue"          => "no",
				// 		"jalur_name"         => "muatan",
				// 		"position"           => "ROM B1 ROAD",
				// 		"vehicle_company"    => "1834",
				// 		"vehicle_device"     => "869926046535932@VT200",
				// 		"vehicle_name"       => "Hino 500",
				// 		"vehicle_no"         => "BKA 124",
				// 		"violation"          => "Overspeed",
				// 		"violation_type"     => "overspeed",
				// 	),
				// 	array(
				// 		"geofence"           => "K30/M30 - 17",
				// 		"gps_latitude_real"  => "-3.659191",
				// 		"gps_longitude_real" => "115.649125",
				// 		"gps_speed"          => 56,
				// 		"gps_speed_limit"    => 30,
				// 		"gps_time"           => "29-05-2022 12:12:48",
				// 		"isfatigue"          => "no",
				// 		"jalur_name"         => "kosongan",
				// 		"position"           => "EST ROAD",
				// 		"vehicle_company"    => "1839",
				// 		"vehicle_device"     => "869926046535932@VT200",
				// 		"vehicle_name"       => "Hino 500",
				// 		"vehicle_no"         => "STLI 348",
				// 		"violation"          => "Overspeed",
				// 		"violation_type"     => "overspeed",
				// 	)
				// );

				// echo "<pre>";
				// var_dump($dataoverspeed);die();
				// echo "<pre>";

				$totaldataoverspeed    = sizeof($dataoverspeed);
				$totaldataoverspeedfix = 0;
					if ($totaldataoverspeed < 10) {
						$totaldataoverspeedfix = "0".$totaldataoverspeed;
					}else {
						$totaldataoverspeedfix = $totaldataoverspeed;
					}

					// UNTUK UPDATE DATA ROM
					// $romStreet           = $this->m_poipoolmaster->getstreet_now2(5);
					// $dataRomFix          = array();
					// $dataPortFix         = array();
					// $dataPortCPBIBFix    = array();
					// $dataPortANTBIRFix   = array();

					//HITUNG DATA DIDALAM ROM
					// if ($totaldataoverspeed > 0) {
					// 	for ($j=0; $j < sizeof($romStreet); $j++) {
					// 		$street_name_rom                  = explode(",", $romStreet[$j]['street_name']);
					// 		$street_nameromfix                = $street_name_rom[0];
					// 		$dataStateRom[$street_nameromfix] = 0;
					//
					// 		for ($x=0; $x < sizeof($totaldataoverspeed); $x++) {
					// 			$positioninrom = $dataoverspeed[$x]['position'];
					//
					// 				if ($positioninrom == $street_nameromfix) {
					// 						$dataStateRom[$street_nameromfix] += 1;
					//
					// 						array_push($dataRomFix, array(
					// 							"isfatigue"          => "no",
					// 							"jalur_name"         => $dataoverspeed[$x]['jalur_name'],
					// 							"vehicle_no"         => $dataoverspeed[$x]['vehicle_no'],
					// 							"vehicle_name"       => $dataoverspeed[$x]['vehicle_name'],
					// 							"vehicle_company"    => $dataoverspeed[$x]['vehicle_company'],
					// 							"violation" 				 => $dataoverspeed[$x]['violation'],
					// 							"violation_type" 		 => $dataoverspeed[$x]['violation_type'],
					// 							"vehicle_device"     => $dataoverspeed[$x]['vehicle_device'],
					// 							"gps_latitude_real"  => $dataoverspeed[$x]['gps_latitude'],
					// 							"gps_longitude_real" => $dataoverspeed[$x]['gps_longitude'],
					// 							"gps_speed"          => $dataoverspeed[$x]['gps_speed'],
					// 							"gps_speed_limit"    => $dataoverspeed[$x]['gps_speed'],
					// 							"gps_time"           => $dataoverspeed[$x]['gps_time'],
					// 							"geofence"           => $dataoverspeed[$x]['geofence"'],
					// 							"position"           => $dataoverspeed[$x]['position"'],
					// 						));
					// 				}
					// 		}
					// 	}
					// }

			// echo "<pre>";
			// var_dump($dataoverspeed);die();
			// echo "<pre>";

			if ($simultantype == 0) {
				$sdate = date("Y-m-d H:i:s", strtotime("-3 minutes"));
			}else {
				// $sdate = $lasttime;
				// $sdate = $startdate;
				$nowtime          = date("Y-m-d H:i:s");
				$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
				$sdate            = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-3hours"));
			}

		$masterviolation   = $this->m_violation->getviolation("ts_violation", $sdate, $contractor, $alarmtypefromaster);

		// echo "<pre>";
		// var_dump($sdate);die();
		// echo "<pre>";
		$violation_call           = array();
		$violation_cardistance    = array();
		$violation_distracted     = array();
		$violation_fatigue        = array();
		$violation_smoking        = array();
		$violation_driverabnormal = array();

			if (sizeof($masterviolation) > 0) {
					for ($j=0; $j < sizeof($masterviolation); $j++) {
						if ($masterviolation[$j]['violation_fatigue'] != "") {
							$json_fatigue            = json_decode($masterviolation[$j]['violation_fatigue']);
							$forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
							$forcheck_gps_time       = $json_fatigue[0]->gps_time;
							$checkthis               = $this->m_violation->getfrommaster($forcheck_vehicledevice);
							$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
							// $jalurname               = $jsonautocheck->auto_last_road;
							$jalurname               = $masterviolation[$j]['violation_jalur'];

							$positionforfilter = $masterviolation[$j]['violation_position'];
								if ($positionforfilter != "") {
									// UNTUK UMUM START
									if (in_array($positionforfilter, $street_onduty)){
										$alarmreportnamefix = "";
										$alarmreporttype = $json_fatigue[0]->gps_alertid;
											if ($alarmreporttype == 626) {
												$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
											}elseif ($alarmreporttype == 627) {
												$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
											}else {
												$alarmreportnamefix = $json_fatigue[0]->gps_alert;
											}

											if ($alarmreporttype != 624 && $alarmreporttype != 625) {
												if (in_array($masterviolation[$j]['violation_position'], $street_onduty)) {
													array_push($datafatigue, array(
														 "isfatigue"          => "yes",
														 "jalur_name"         => $jalurname,
														 "vehicle_no"         => $json_fatigue[0]->vehicle_no,
														 "vehicle_name"       => $json_fatigue[0]->vehicle_name,
														 "vehicle_company"    => $json_fatigue[0]->vehicle_company,
														 "vehicle_device"     => $json_fatigue[0]->vehicle_device,
														 "vehicle_mv03"       => $json_fatigue[0]->vehicle_mv03,
														 "gps_alert"          => $alarmreportnamefix,
														 "violation" 				  => $alarmreportnamefix,
														 "violation_type" 		=> "not_overspeed",
														 "gps_time"           => $json_fatigue[0]->gps_time,
														 "auto_last_update"   => $jsonautocheck->auto_last_update,
														 "auto_last_check"    => $jsonautocheck->auto_last_check,
														 "gps_latitude_real"  => $json_fatigue[0]->gps_latitude_real,
														 "gps_longitude_real" => $json_fatigue[0]->gps_longitude_real,
														 "position"           => $masterviolation[$j]['violation_position'],
														 "auto_last_position" => $jsonautocheck->auto_last_position,
														 "gps_speed"          => $json_fatigue[0]->gps_speed,
													));
												}
											}
									}
									// UNTUK UMUM END
								}
					}
				}

				$lasttime     = $masterviolation[0]['violation_update'];
				// $lasttime     = date("Y-m-d H:i:s", strtotime($masterviolation[0]['violation_update']."-15 minutes"));
			}else {
				$lasttime = $sdate;
			}

			// alarmtypefromaster

			if ($violationmasterselect == 6) {
				$violationmix = array_merge($dataoverspeed);
			}elseif ($violationmasterselect == 0) {
				$violationmix = array_merge($dataoverspeed, $datafatigue);
			}else {
				$violationmix = array_merge($datafatigue);
			}

		// echo "<pre>";
		// var_dump($dataoverspeed);die();
		// echo "<pre>";

			if (sizeof($violationmix) > 0) {
				// ROM ROAD
				$dataJumlahInRomRoadMuatan['ROM_B1_ROAD'] = 0;
				$dataJumlahInRomRoadMuatan['ROM_B2_ROAD'] = 0;
				$dataJumlahInRomRoadMuatan['ROM_B3_ROAD'] = 0;
				$dataJumlahInRomRoadMuatan['EST_ROAD']    = 0;

				$dataJumlahInRomRoadKosongan['ROM_B1_ROAD'] = 0;
				$dataJumlahInRomRoadKosongan['ROM_B2_ROAD'] = 0;
				$dataJumlahInRomRoadKosongan['ROM_B3_ROAD'] = 0;
				$dataJumlahInRomRoadKosongan['EST_ROAD']    = 0;

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

				$masterdatavehicle = $violationmix; // SWITCH VARIABLE BIAR GAMPANG
				// $jalurarray = array();
				// echo "<pre>";
				// var_dump($masterdatavehicle);die();
				// echo "<pre>";

				for ($k=0; $k < sizeof($masterdatavehicle); $k++) {
					$datalastposition = $masterdatavehicle[$k]['position'];
					$jalur_name       = $masterdatavehicle[$k]['jalur_name'];

					// array_push($jalurarray, array(
					// 	"jalur"    => $datalastposition,
					// 	"position" => $jalur_name
					// ));

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
							}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5") {
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
							}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5" || $datalastposition == "KM 30" || $datalastposition == "KM 30.5") {
								$dataJumlahInKmKosongan['KM_30'] += 1;
							}elseif ($datalastposition == "ROM B3 ROAD") {
								$dataJumlahInRomRoadKosongan['ROM_B3_ROAD'] += 1;
							}
						}else {
							if ($datalastposition == "Port BIB - Antrian") {
								$dataJumlahInKmMuatan_2['gb5_port_bib_antrian'] += 1;
							}elseif ($datalastposition == "Port BIR - Antrian WB") {
								$dataJumlahInKmMuatan_2['gb6_port_bir_antrian_wb'] += 1;
							}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5") {
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
							}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5" || $datalastposition == "KM 30" || $datalastposition == "KM 30.5") {
								$dataJumlahInKmMuatan['KM_30'] += 1;
							}elseif ($datalastposition == "ROM B3 ROAD") {
								$dataJumlahInRomRoadMuatan['ROM_B3_ROAD'] += 1;
							}
						}
				}

				// echo "<pre>";
				// var_dump($dataJumlahInRomRoadKosongan);die();
				// echo "<pre>";

				// KHUSUS UNTUK SUMMARY START
				if (sizeof($datafatigue) > 0) {
					for ($x=0; $x < sizeof($datafatigue); $x++) {
						$violationalertnya = $datafatigue[$x]['gps_alert'];

						if (strpos($violationalertnya, "Call") !== false) {
						  array_push($violation_call, array(
						     "isfatigue"           => $datafatigue[$x]['isfatigue'],
						     "jalur_name"          => $datafatigue[$x]['jalur_name'],
						     "vehicle_no"          => $datafatigue[$x]['vehicle_no'],
						     "vehicle_name"        => $datafatigue[$x]['vehicle_name'],
						     "vehicle_company"     => $datafatigue[$x]['vehicle_company'],
						     "vehicle_device"      => $datafatigue[$x]['vehicle_device'],
						     "vehicle_mv03"        => $datafatigue[$x]['vehicle_mv03'],
						     "gps_alert"           => $datafatigue[$x]['gps_alert'],
						     "violation" 				   => $datafatigue[$x]['violation'],
						     "violation_type" 		 => $datafatigue[$x]['violation_type'],
						     "gps_time"            => $datafatigue[$x]['gps_time'],
						     "auto_last_update"    => $datafatigue[$x]['auto_last_update'],
						     "auto_last_check"     => $datafatigue[$x]['auto_last_check'],
						     "gps_latitude_real"   => $datafatigue[$x]['gps_latitude_real'],
						     "gps_longitude_real"  => $datafatigue[$x]['gps_longitude_real'],
						     "position"            => $datafatigue[$x]['position'],
						     "auto_last_position"  => $datafatigue[$x]['auto_last_position'],
						     "gps_speed"           => $datafatigue[$x]['gps_speed'],
						  ));
						}elseif (strpos($violationalertnya, "Distance") !== false) {
						  array_push($violation_cardistance, array(
						    "isfatigue"          => $datafatigue[$x]['isfatigue'],
						    "jalur_name"         => $datafatigue[$x]['jalur_name'],
						    "vehicle_no"         => $datafatigue[$x]['vehicle_no'],
						    "vehicle_name"       => $datafatigue[$x]['vehicle_name'],
						    "vehicle_company"    => $datafatigue[$x]['vehicle_company'],
						    "vehicle_device"     => $datafatigue[$x]['vehicle_device'],
						    "vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
						    "gps_alert"          => $datafatigue[$x]['gps_alert'],
						    "violation" 				 => $datafatigue[$x]['violation'],
						    "violation_type" 		 => $datafatigue[$x]['violation_type'],
						    "gps_time"           => $datafatigue[$x]['gps_time'],
						    "auto_last_update"   => $datafatigue[$x]['auto_last_update'],
						    "auto_last_check"    => $datafatigue[$x]['auto_last_check'],
						    "gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
						    "gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
						    "position"           => $datafatigue[$x]['position'],
						    "auto_last_position" => $datafatigue[$x]['auto_last_position'],
						    "gps_speed"          => $datafatigue[$x]['gps_speed'],
						  ));
						}
						// elseif (strpos($violationalertnya, "Distracted") !== false) {
						//   array_push($violation_distracted, array(
						//     "isfatigue"          => $datafatigue[$x]['isfatigue'],
						//     "jalur_name"         => $datafatigue[$x]['jalur_name'],
						//     "vehicle_no"         => $datafatigue[$x]['vehicle_no'],
						//     "vehicle_name"       => $datafatigue[$x]['vehicle_name'],
						//     "vehicle_company"    => $datafatigue[$x]['vehicle_company'],
						//     "vehicle_device"     => $datafatigue[$x]['vehicle_device'],
						//     "vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
						//     "gps_alert"          => $datafatigue[$x]['gps_alert'],
						//     "violation" 				 => $datafatigue[$x]['violation'],
						//     "violation_type" 		 => $datafatigue[$x]['violation_type'],
						//     "gps_time"           => $datafatigue[$x]['gps_time'],
						//     "auto_last_update"   => $datafatigue[$x]['auto_last_update'],
						//     "auto_last_check"    => $datafatigue[$x]['auto_last_check'],
						//     "gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
						//     "gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
						//     "position"           => $datafatigue[$x]['position'],
						//     "auto_last_position" => $datafatigue[$x]['auto_last_position'],
						//     "gps_speed"          => $datafatigue[$x]['gps_speed'],
						//   ));
						// }
						elseif (strpos($violationalertnya, "Fatigue") !== false) {
						  array_push($violation_fatigue, array(
						    "isfatigue"          => $datafatigue[$x]['isfatigue'],
						    "jalur_name"         => $datafatigue[$x]['jalur_name'],
						    "vehicle_no"         => $datafatigue[$x]['vehicle_no'],
						    "vehicle_name"       => $datafatigue[$x]['vehicle_name'],
						    "vehicle_company"    => $datafatigue[$x]['vehicle_company'],
						    "vehicle_device"     => $datafatigue[$x]['vehicle_device'],
						    "vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
						    "gps_alert"          => $datafatigue[$x]['gps_alert'],
						    "violation" 				 => $datafatigue[$x]['violation'],
						    "violation_type" 		 => $datafatigue[$x]['violation_type'],
						    "gps_time"           => $datafatigue[$x]['gps_time'],
						    "auto_last_update"   => $datafatigue[$x]['auto_last_update'],
						    "auto_last_check"    => $datafatigue[$x]['auto_last_check'],
						    "gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
						    "gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
						    "position"           => $datafatigue[$x]['position'],
						    "auto_last_position" => $datafatigue[$x]['auto_last_position'],
						    "gps_speed"          => $datafatigue[$x]['gps_speed'],
						  ));
						}elseif (strpos($violationalertnya, "Smoking") !== false) {
						  array_push($violation_smoking, array(
						    "isfatigue"          => $datafatigue[$x]['isfatigue'],
						    "jalur_name"         => $datafatigue[$x]['jalur_name'],
						    "vehicle_no"         => $datafatigue[$x]['vehicle_no'],
						    "vehicle_name"       => $datafatigue[$x]['vehicle_name'],
						    "vehicle_company"    => $datafatigue[$x]['vehicle_company'],
						    "vehicle_device"     => $datafatigue[$x]['vehicle_device'],
						    "vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
						    "gps_alert"          => $datafatigue[$x]['gps_alert'],
						    "violation" 				 => $datafatigue[$x]['violation'],
						    "violation_type" 		 => $datafatigue[$x]['violation_type'],
						    "gps_time"           => $datafatigue[$x]['gps_time'],
						    "auto_last_update"   => $datafatigue[$x]['auto_last_update'],
						    "auto_last_check"    => $datafatigue[$x]['auto_last_check'],
						    "gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
						    "gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
						    "position"           => $datafatigue[$x]['position'],
						    "auto_last_position" => $datafatigue[$x]['auto_last_position'],
						    "gps_speed"          => $datafatigue[$x]['gps_speed'],
						  ));
						}elseif (strpos($violationalertnya, "Undetected") !== false) {
						  array_push($violation_driverabnormal, array(
						    "isfatigue"          => $datafatigue[$x]['isfatigue'],
						    "jalur_name"         => $datafatigue[$x]['jalur_name'],
						    "vehicle_no"         => $datafatigue[$x]['vehicle_no'],
						    "vehicle_name"       => $datafatigue[$x]['vehicle_name'],
						    "vehicle_company"    => $datafatigue[$x]['vehicle_company'],
						    "vehicle_device"     => $datafatigue[$x]['vehicle_device'],
						    "vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
						    "gps_alert"          => $datafatigue[$x]['gps_alert'],
						    "violation" 				 => $datafatigue[$x]['violation'],
						    "violation_type" 		 => $datafatigue[$x]['violation_type'],
						    "gps_time"           => $datafatigue[$x]['gps_time'],
						    "auto_last_update"   => $datafatigue[$x]['auto_last_update'],
						    "auto_last_check"    => $datafatigue[$x]['auto_last_check'],
						    "gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
						    "gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
						    "position"           => $datafatigue[$x]['position'],
						    "auto_last_position" => $datafatigue[$x]['auto_last_position'],
						    "gps_speed"          => $datafatigue[$x]['gps_speed'],
						  ));
						}
					}
				}

				// echo "<pre>";
				// var_dump($totaldataoverspeedfix);die();
				// echo "<pre>";

				$totalviolation_call           = sizeof($violation_call);
				$totalviolation_cardistance    = sizeof($violation_cardistance);
				// $totalviolation_distracted     = sizeof($violation_distracted);
				$totalviolation_fatigue        = sizeof($violation_fatigue);
				$totalviolation_smoking        = sizeof($violation_smoking);
				$totalviolation_driverabnormal = sizeof($violation_driverabnormal);
				// $total_violationall            = ($totaldataoverspeedfix + $totalviolation_call + $totalviolation_cardistance + $totalviolation_distracted + $totalviolation_fatigue + $totalviolation_smoking + $totalviolation_driverabnormal);
				$total_violationall            = ($totaldataoverspeedfix + $totalviolation_call + $totalviolation_cardistance + $totalviolation_fatigue + $totalviolation_smoking + $totalviolation_driverabnormal);

				$tv_call = 0;
					if ($totalviolation_call < 10) {
						$tv_call = "0".$totalviolation_call;
					}else {
						$tv_call = $totalviolation_call;
					}
				$tv_cardistance = 0;
					if ($totalviolation_cardistance < 10) {
						$tv_cardistance = "0".$totalviolation_cardistance;
					}else {
						$tv_cardistance = $totalviolation_cardistance;
					}
				// $tv_distracted = 0;
				// 	if ($totalviolation_distracted < 10) {
				// 		$tv_distracted = "0".$totalviolation_distracted;
				// 	}else {
				// 		$tv_distracted = $totalviolation_distracted;
				// 	}
				$tv_fatigue = 0;
					if ($totalviolation_fatigue < 10) {
						$tv_fatigue = "0".$totalviolation_fatigue;
					}else {
						$tv_fatigue = $totalviolation_fatigue;
					}
				$tv_smoking = 0;
					if ($totalviolation_smoking < 10) {
						$tv_smoking = "0".$totalviolation_smoking;
					}else {
						$tv_smoking = $totalviolation_smoking;
					}
				$tv_driverabnormal = 0;
					if ($totalviolation_driverabnormal < 10) {
						$tv_driverabnormal = "0".$totalviolation_driverabnormal;
					}else {
						$tv_driverabnormal = $totalviolation_driverabnormal;
					}

					if ($violationmasterselect == 1) {
						$totaldataoverspeedfix = "00";
						$tv_call               = $tv_call;
						$tv_cardistance        = "00";
						// $tv_distracted         = "00";
						$tv_fatigue            = "00";
						$tv_smoking            = "00";
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 2) {
						$totaldataoverspeedfix = "00";
						$tv_call               = "00";
						$tv_cardistance        = $tv_cardistance;
						// $tv_distracted         = "00";
						$tv_fatigue            = "00";
						$tv_smoking            = "00";
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 3) {
						$totaldataoverspeedfix = "00";
						$tv_call               = "00";
						$tv_cardistance        = "00";
						$tv_distracted         = $tv_distracted;
						$tv_fatigue            = "00";
						$tv_smoking            = "00";
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 4) {
						$totaldataoverspeedfix = "00";
						$tv_call               = "00";
						$tv_cardistance        = "00";
						// $tv_distracted         = "00";
						$tv_fatigue            = $tv_fatigue;
						$tv_smoking            = "00";
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 5) {
						$totaldataoverspeedfix = "00";
						$tv_call               = "00";
						$tv_cardistance        = "00";
						// $tv_distracted         = "00";
						$tv_fatigue            = "00";
						$tv_smoking            = $tv_smoking;
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 6) {
						$totaldataoverspeedfix = $totaldataoverspeedfix;
						$tv_call               = "00";
						$tv_cardistance        = "00";
						// $tv_distracted         = "00";
						$tv_fatigue            = "00";
						$tv_smoking            = "00";
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 7) {
						$totaldataoverspeedfix = "00";
						$tv_call               = "00";
						$tv_cardistance        = "00";
						// $tv_distracted         = "00";
						$tv_fatigue            = "00";
						$tv_smoking            = "00";
						$tv_driverabnormal     = $tv_driverabnormal;
					}else {
						$totaldataoverspeedfix = $totaldataoverspeedfix;
						$tv_call               = $tv_call;
						$tv_cardistance        = $tv_cardistance;
						// $tv_distracted         = $tv_distracted;
						$tv_fatigue            = $tv_fatigue;
						$tv_smoking            = $tv_smoking;
						$tv_driverabnormal     = $tv_driverabnormal;
					}
				//KHUSUS UNTUK SUMMARY END


				echo json_encode(array(
					"msg"                      => "success",
					"code"                     => 200,
					// "lasttime"              => $lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour")),
					"lasttime"                 => $startdate,
					"simultantype"             => 1,
					"violationmix"             => $violationmix,
					"dataMuatan"               => $dataJumlahInKmMuatan,
					"dataKosongan"             => $dataJumlahInKmKosongan,
					"dataKosonganRomRoad"      => $dataJumlahInRomRoadKosongan,
					"dataMuatanRomRoad"        => $dataJumlahInRomRoadMuatan,
					"dataMuatan2"              => $dataJumlahInKmMuatan_2,
					"dataKosongan2"            => $dataJumlahInKmKosongan_2,
					"total_ov"			           => $totaldataoverspeedfix,
					"tv_call"                  => $tv_call,
					"tv_cardistance"           => $tv_cardistance,
					// "tv_distracted"            => $tv_distracted,
					"tv_fatigue"               => $tv_fatigue,
					"tv_smoking"               => $tv_smoking,
					"tv_driverabnormal"        => $tv_driverabnormal,
					"total_violationall"	     => $total_violationall,
					"violation_call"           => $violation_call,
					"violation_cardistance"    => $violation_cardistance,
					"violation_distracted"     => $violation_distracted,
					"violation_fatigue"        => $violation_fatigue,
					"violation_smoking"        => $violation_smoking,
					"violation_driverabnormal" => $violation_driverabnormal,
					// "dataRomFix"    => $dataRomFix
				));
			}else {
				echo json_encode(array(
					"msg"          => "failed",
					"code"         => 400,
					// "lasttime"  => $lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour")),
					"lasttime"     => $startdate,
					"simultantype" => 1,
					"violationmix" => $violationmix
				));
			}
  }

	function getlistinkm(){
		$dataVehicleOnKosongan             = array();
		$dataVehicleOnMuatan               = array();
		$idkm 							               = $_POST['idkm'];
		$contractor 							         = $_POST['contractor'];
		$lasttime_violation 							 = date("Y-m-d H:i:s", strtotime($_POST['lasttime_violation']));
		$kmonsearch 					             = array();
		$dataoverspeed 				             = array();
		$datafatigue 					             = array();

		// $sdate                     = date("Y-m-d")." 00:00:00";
		// $sdate = date("Y-m-d H:i:s", strtotime("-5 minutes"));

		// echo "<pre>";
		// var_dump($lasttime_violation);die();
		// echo "<pre>";

		// $masterdatavehicle         = $this->m_violation->getviolationbykm("ts_violation", $contractor, $sdate);
		$masterdatavehicle         = $this->m_violation->getviolationbykm("ts_violation", $contractor, $lasttime_violation);


		if ($idkm == 1) {
			$kmonsearch = array("Port BIB - Antrian", "Port BIB - Kosongan 1", "Port BIB - Kosongan 2", "Port BIR - Antrian WB", "Port BIR - Kosongan 1", "Port BIR - Kosongan 2", "Simpang Bayah - Kosongan");
		}elseif ($idkm == 2) {
			$kmonsearch = array("KM 1", "KM 1.5", "KM 0.5");
		}elseif ($idkm == 3) {
			$kmonsearch = array("KM 2", "KM 2.5");
		}elseif ($idkm == 4) {
			$kmonsearch = array("KM 3", "KM 3.5");
		}elseif ($idkm == 5) {
			$kmonsearch = array("KM 4", "KM 4.5");
		}elseif ($idkm == 6) {
			$kmonsearch = array("KM 5", "KM 5.5");
		}elseif ($idkm == 7) {
			$kmonsearch = array("KM 6", "KM 6.5");
		}elseif ($idkm == 8) {
			$kmonsearch = array("KM 7", "KM 7.5");
		}elseif ($idkm == 9) {
			$kmonsearch = array("KM 8", "KM 8.5");
		}elseif ($idkm == 10) {
			$kmonsearch = array("KM 9", "KM 9.5");
		}elseif ($idkm == 11) {
			$kmonsearch = array("KM 10", "KM 10.5");
		}elseif ($idkm == 12) {
			$kmonsearch = array("KM 11", "KM 11.5");
		}elseif ($idkm == 13) {
			$kmonsearch = array("KM 12", "KM 12.5");
		}elseif ($idkm == 14) {
			$kmonsearch = array("KM 13", "KM 13.5");
		}elseif ($idkm == 15) {
			$kmonsearch = array("KM 14", "KM 14.5");
		}elseif ($idkm == 16) {
			$kmonsearch = array("KM 15", "KM 15.5");
		}elseif ($idkm == 17) {
			$kmonsearch = array("KM 16", "KM 16.5");
		}elseif ($idkm == 18) {
			$kmonsearch = array("KM 17", "KM 17.5");
		}elseif ($idkm == 19) {
			$kmonsearch = array("KM 18", "KM 18.5");
		}elseif ($idkm == 20) {
			$kmonsearch = array("KM 19", "KM 19.5");
		}elseif ($idkm == 21) {
			$kmonsearch = array("KM 20", "KM 20.5");
		}elseif ($idkm == 22) {
			$kmonsearch = array("KM 21", "KM 21.5");
		}elseif ($idkm == 23) {
			$kmonsearch = array("KM 22", "KM 22.5");
		}elseif ($idkm == 24) {
			$kmonsearch = array("KM 23", "KM 23.5");
		}elseif ($idkm == 25) {
			$kmonsearch = array("KM 24", "KM 24.5");
		}elseif ($idkm == 26) {
			$kmonsearch = array("KM 25", "KM 25.5");
		}elseif ($idkm == 27) {
			$kmonsearch = array("KM 26", "KM 26.5");
		}elseif ($idkm == 28) {
			$kmonsearch = array("KM 27", "KM 27.5");
		}elseif ($idkm == 29) {
			$kmonsearch = array("KM 28", "KM 28.5");
		}elseif ($idkm == 30) {
			$kmonsearch = array("KM 29", "KM 29.5");
		}

		$vCompanyFix 					 = "KM " . $idkm;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			// CHECK OVERSPEED
			if ($masterdatavehicle[$i]['violation_overspeed'] != "") {
				$autocheck             = json_decode($masterdatavehicle[$i]['violation_overspeed']);
				$jalur_name            = $masterdatavehicle[$i]['violation_jalur'];
				$datalastposition      = $masterdatavehicle[$i]['violation_position'];
				$datavehiclefrommaster = $this->m_violation->getfrommaster($masterdatavehicle[$i]['violation_vehicle_device']);
				$speedlimitfix 		     = "";

				// echo "<pre>";
				// var_dump($speedlimitfix);die();
				// echo "<pre>";

					if (in_array($datalastposition, $kmonsearch)) {
						if ($jalur_name == "kosongan") {
							array_push($dataVehicleOnKosongan, array(
								"vehicle_no"            => $masterdatavehicle[$i]['violation_vehicle_no'],
								"vehicle_name"          => $masterdatavehicle[$i]['violation_vehicle_name'],
								"violation"             => "Overspeed",
								"violation_type"        => $masterdatavehicle[$i]['violation_type'],
								// "auto_last_lat"      => $autocheck->auto_last_lat,
								// "auto_last_long"     => $autocheck->auto_last_long,
								"auto_last_positionfix" => $datalastposition,
								// "auto_last_engine"   => $autocheck->auto_last_engine,
								"auto_last_speed"       => $autocheck->gps_speed,
								// "auto_last_speed"       => (round($autocheck->gps_speed*1.853) - 3),
								"auto_speed_limit"      => $autocheck->gps_speed_limit,
								"auto_last_update"      => date("d-m-Y H:i:s", strtotime($autocheck->gps_time))
							));
						}else {
							array_push($dataVehicleOnMuatan, array(
								"vehicle_no"            => $masterdatavehicle[$i]['violation_vehicle_no'],
								"vehicle_name"          => $masterdatavehicle[$i]['violation_vehicle_name'],
								"violation"             => "Overspeed",
								"violation_type"        => $masterdatavehicle[$i]['violation_type'],
								// "auto_last_lat"      => $autocheck->auto_last_lat,
								// "auto_last_long"     => $autocheck->auto_last_long,
								"auto_last_positionfix" => $datalastposition,
								// "auto_last_engine"   => $autocheck->auto_last_engine,
								"auto_last_speed"       => $autocheck->gps_speed,
								// "auto_last_speed"       => (round($autocheck->gps_speed*1.853) - 3),
								"auto_speed_limit"      => $autocheck->gps_speed_limit,
								"auto_last_update"      => date("d-m-Y H:i:s", strtotime($autocheck->gps_time))
							));
						}
					}
				}

				// CHECK FATIGUE
				if ($masterdatavehicle[$i]['violation_fatigue'] != "") {
					$json_fatigue            = json_decode($masterdatavehicle[$i]['violation_fatigue']);
					$forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
					$forcheck_gps_time       = $json_fatigue[0]->gps_time;
					$datalastposition        = $masterdatavehicle[$i]['violation_position'];
					$checkthis               = $this->m_violation->getfrommaster($forcheck_vehicledevice);
					$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
					// $jalur_name           = $jsonautocheck->auto_last_road;
					$jalur_name              = $masterdatavehicle[$i]['violation_jalur'];

					// echo "<pre>";
					// var_dump($json_fatigue[0]);die();
					// echo "<pre>";

						if (in_array($datalastposition, $kmonsearch)) {
							if ($jalur_name == "kosongan") {
								array_push($dataVehicleOnKosongan, array(
									"vehicle_no"            => $masterdatavehicle[$i]['violation_vehicle_no'],
									"vehicle_name"          => $masterdatavehicle[$i]['violation_vehicle_name'],
									"violation"             => $json_fatigue[0]->gps_alert,
									"violation_type"        => $masterdatavehicle[$i]['violation_type'],
									// "auto_last_lat"      => $autocheck->auto_last_lat,
									// "auto_last_long"     => $autocheck->auto_last_long,
									"auto_last_positionfix" => $datalastposition,
									// "auto_last_engine"   => $autocheck->auto_last_engine,
									"auto_last_speed"       => $json_fatigue[0]->gps_speed,
									"auto_last_update"      => date("d-m-Y H:i:s", strtotime($json_fatigue[0]->gps_time))
								));
							}else {
								array_push($dataVehicleOnMuatan, array(
									"vehicle_no"            => $masterdatavehicle[$i]['violation_vehicle_no'],
									"vehicle_name"          => $masterdatavehicle[$i]['violation_vehicle_name'],
									"violation"             => $json_fatigue[0]->gps_alert,
									"violation_type"        => $masterdatavehicle[$i]['violation_type'],
									// "auto_last_lat"      => $autocheck->auto_last_lat,
									// "auto_last_long"     => $autocheck->auto_last_long,
									"auto_last_positionfix" => $datalastposition,
									// "auto_last_engine"   => $autocheck->auto_last_engine,
									"auto_last_speed"       => $json_fatigue[0]->gps_speed,
									"auto_last_update"      => date("d-m-Y H:i:s", strtotime($json_fatigue[0]->gps_time))
								));
							}
						}
					}
			}

		// echo "<pre>";
		// var_dump($dataVehicleOnKosongan);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "code" => 200, "dataKosongan" => $dataVehicleOnKosongan, "dataMuatan" => $dataVehicleOnMuatan, "kmsent" => $vCompanyFix));
	}

	function quickcounttiaviolation(){
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

	  	$companyid                   = $this->sess->user_company;
	  	$user_dblive                 = $this->sess->user_dblive;
	  	$mastervehicle               = $this->m_poipoolmaster->getmastervehicleforheatmap();
			$violationmaster             = $this->m_violation->getviolationmaster();

	  	$datafix                     = array();
	  	$deviceidygtidakada          = array();
	  	$statusvehicle['engine_on']  = 0;
	  	$statusvehicle['engine_off'] = 0;

	  	for ($i=0; $i < sizeof($mastervehicle); $i++) {
	  		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
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

			// GET ROM ROAD
			$romRoad                  = $this->m_poipoolmaster->getstreet_now2(5);
			$this->params['rom_road'] = $romRoad;

			// echo "<pre>";
	  	// var_dump($romRoad);die();
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

	  	$this->params['resultactive']    = $this->dashboardmodel->vehicleactive();
	  	$this->params['resultexpired']   = $this->dashboardmodel->vehicleexpired();
	  	$this->params['resulttotaldev']  = $this->dashboardmodel->totaldevice();
	  	$this->params['mapsetting']      = $this->m_poipoolmaster->getmapsetting();
	  	$this->params['poolmaster']      = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
			$this->params['violationmaster'] = $violationmaster;

	  	// echo "<pre>";
	  	// var_dump($this->params['violationmaster']);die();
	  	// echo "<pre>";

	  	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	  	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

	  		if ($privilegecode == 1) {
	  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
	  			$this->params["content"]        = $this->load->view('newdashboard/violation/tia/v_home_violationtia', $this->params, true);
	  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
	  		}elseif ($privilegecode == 2) {
	  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
	  			$this->params["content"]        = $this->load->view('newdashboard/violation/tia/v_home_violationtia', $this->params, true);
	  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
	  		}elseif ($privilegecode == 3) {
	  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
	  			$this->params["content"]        = $this->load->view('newdashboard/violation/tia/v_home_violationtia', $this->params, true);
	  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
	  		}elseif ($privilegecode == 4) {
	  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
	  			$this->params["content"]        = $this->load->view('newdashboard/violation/tia/v_home_violationtia', $this->params, true);
	  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
	  		}elseif ($privilegecode == 5) {
	  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
	  			$this->params["content"]        = $this->load->view('newdashboard/violation/tia/v_home_violationtia', $this->params, true);
	  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
	  		}elseif ($privilegecode == 6) {
	  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
	  			$this->params["content"]        = $this->load->view('newdashboard/violation/tia/v_home_violationtia', $this->params, true);
	  			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
	  		}else {
	  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	  			$this->params["content"]        = $this->load->view('newdashboard/violation/tia/v_home_violationtia', $this->params, true);
	  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	  		}
		}

		function km_quickcount_newtia(){
			$simultantype 				         = $_POST['simultantype'];
			$lasttime 				             = $_POST['lasttime_violation'];
			$contractor 				           = $_POST['contractor'];
			$violationmasterselect 				 = $_POST['violationmaster'];
			$alarmtypefromaster            = array();
			$dataoverspeed 								 = array();
			$datafatigue                   = array();
			$dataKmMuatanFix               = array();
			$dataKmKosonganFix             = array();
			$violationmix                  = array();

			$street_onduty = array(
									// "PORT BIB","PORT BIR","PORT TIA",
									//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
									// "ROM A1","ROM B1","ROM B2","ROM B3","ROM EST",
									// "ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
									//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
									//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",
									"ROM B3 ROAD",
									"TIA KM 0","TIA KM 0.5","TIA KM 1","TIA KM 1.5","TIA KM 2","TIA KM 2.5","TIA KM 3","TIA KM 3.5","TIA KM 4","TIA KM 4.5","TIA KM 5","TIA KM 5.5",
									"TIA KM 7.5","TIA KM 8","TIA KM 8.5","TIA KM 9","TIA KM 9.5","TIA KM 10","TIA KM 10.5","TIA KM 11","TIA KM 11.5","TIA KM 12","TIA KM 12.5","TIA KM 13","TIA KM 13.5","TIA KM 14","TIA KM 14.5","TIA KM 15","TIA KM 15.5","TIA KM 16",
									"TIA KM 16.5","TIA KM 17","TIA KM 17.5","TIA KM 18","TIA KM 18.5","TIA KM 19","TIA KM 19.5","TIA KM 20","TIA KM 20.5","TIA KM 21","TIA KM 21.5","TIA KM 22","TIA KM 22.5","TIA KM 23","TIA KM 23.5","TIA KM 24","TIA KM 24.5","TIA KM 25","TIA KM 25.5","TIA KM 26",
									"TIA KM 26.5","TIA KM 27","TIA KM 27.5","TIA KM 28","TIA KM 28.5","TIA KM 29","TIA KM 29.5","TIA KM 30","TIA KM 30.5","TIA KM 31","TIA KM 31",

									// "BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
									// "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
									// "Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
									// "Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
									// "PORT BIB - Antrian","Port BIB - Antrian"
								);

			if ($violationmasterselect == 6) {
				$alarmtypefromaster[] = 9999;
			}else {
				if ($violationmasterselect != "0") {
					$alarmbymaster = $this->m_violation->getalarmbytype($violationmasterselect);
					for ($i=0; $i < sizeof($alarmbymaster); $i++) {
						$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
					}
				}
			}

			$this->db = $this->load->database("default", TRUE);
			$this->db->select("user_id, user_dblive");
			$this->db->order_by("user_id","asc");
			$this->db->where("user_id", 4408);
			$q         = $this->db->get("user");
			$row       = $q->row();
			$total_row = count($row);

			$nowtime          = date("Y-m-d H:i:s");
			$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
			$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-60second"));

			$startdate        = $last_fiveminutes;
			$enddate          = $nowtime_wita;
			$sdate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
			$edate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

			//print_r($sdate." ".$edate);exit();
			if(count($row)>0){
				$user_dblive = $row->user_dblive;
			}

			$this->dbalert = $this->load->database($user_dblive, TRUE);
			$this->dbalert->where("gps_time >=", $sdate);
					//$this->dbalert->where("gps_time <=", $edate);
			$this->dbalert->where("gps_speed >=", 11.3);  // >= 21 kph
			//$this->dbalert->where("gps_speed_status", 1);
			$this->dbalert->where("gps_alert", "Speeding Alarm");
			// $this->dbalert->where("gps_notif", 0); //belum ke send
			//$this->dbalert->limit(5); //limit
			$this->dbalert->order_by("gps_time","asc");
			$this->dbalert->group_by(array("gps_name"));
			$q           = $this->dbalert->get("gps_alert");
			$rows        = $q->result();
			$total_alert = count($rows);

				// echo "<pre>";
				// var_dump($rows);die();
				// echo "<pre>";

				$user_level      = $this->sess->user_level;
		    $user_parent     = $this->sess->user_parent;
				$user_company    = $this->sess->user_company;
				$user_subcompany = $this->sess->user_subcompany;
				$user_group      = $this->sess->user_group;
				$user_subgroup   = $this->sess->user_subgroup;
				$user_dblive 	   = $this->sess->user_dblive;
		    $privilegecode 	 = $this->sess->user_id_role;
				$user_id_fix     = $this->sess->user_id;

				if($privilegecode == 1){
					$contractor = $contractor;
				}else if($privilegecode == 2){
					$contractor = $contractor;
				}else if($privilegecode == 3){
					$contractor = $contractor;
				}else if($privilegecode == 4){
					$contractor = $contractor;
				}else if($privilegecode == 5){
					$contractor = $user_company;
				}else if($privilegecode == 6){
					$contractor = $user_company;
				}else if($privilegecode == 0){
					$contractor = $contractor;
				}else{
					$contractor = $contractor;
				}

				if($total_alert >0){
					$j = 1;
					for ($i=0;$i<count($rows);$i++){
						$title_name      = "OVERSPEED ALARM";
						$vehicle_device  = $rows[$i]->gps_name."@".$rows[$i]->gps_host;
						$data_vehicle    = $this->getvehicle2($vehicle_device, $contractor);

							if ($data_vehicle) {
								// echo "<pre>";
								// var_dump($data_vehicle);die();
								// echo "<pre>";

								$vehicle_id      = $data_vehicle->vehicle_id;
								$vehicle_no      = $data_vehicle->vehicle_no;
								$vehicle_name    = $data_vehicle->vehicle_name;
								$vehicle_company = $data_vehicle->vehicle_company;
								$vehicle_dblive  = $data_vehicle->vehicle_dbname_live;

								$driver_name = "-";

								// printf("===Process Alarm ID %s %s %s (%d/%d) \r\n", $rows[$i]->gps_id, $data_vehicle->vehicle_no, $data_vehicle->vehicle_device, $j, $total_alert);
								$skip_sent = 0;
								$position = $this->getPosition_other($rows[$i]->gps_longitude_real,$rows[$i]->gps_latitude_real);

									if(isset($position)){
										$ex_position = explode(",",$position->display_name);
										if(count($ex_position)>0){
											$position_name = $ex_position[0];
										}else{
											$position_name = $ex_position[0];
										}
									}else{
										$position_name = $position->display_name;
											$skip_sent = 1;
									}

										//filter in location array HAULING, ROM, PORT

										if (in_array($position_name, $street_onduty)){
											$skip_sent = 0;
										}else{
											$skip_sent = 1;
										}

								$gps_time   = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))); //sudah wita
								$coordinate = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
								//$url = "http://maps.google.com/maps?z=12&t=m&q=loc:".$coordinate;
								$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
								//https://www.google.com/maps/search/?api=1&query=-6.2915399,106.9660776 : ex
								$gpsspeed_kph = round($rows[$i]->gps_speed*1.852,0);
								$direction    = $rows[$i]->gps_course;
								$jalur        = $this->get_jalurname_new($direction);

								if($jalur == ""){
									$jalur = $rows[$i]->gps_last_road_type;
								}

								$rowgeofence = $this->getGeofence_location_live($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $vehicle_dblive);

												if($rowgeofence == false){
													$geofence_id           = 0;
													$geofence_name         = "";
													$geofence_speed        = 0;
													$geofence_speed_muatan = "";
													$geofence_type         = "";
													$geofence_speed_limit  = 0;

												}else{
													$geofence_id           = $rowgeofence->geofence_id;
													$geofence_name         = $rowgeofence->geofence_name;
													$geofence_speed        = $rowgeofence->geofence_speed;
													$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
													$geofence_type         = $rowgeofence->geofence_type;

													if($jalur == "muatan"){
														$geofence_speed_limit = $geofence_speed_muatan;
													}else if($jalur == "kosongan"){
														$geofence_speed_limit = $geofence_speed;
													}else{
														$geofence_speed_limit = 0;

													}
												}
										// printf("===Position : %s Geofence : %s Jalur: %s \r\n", $position_name, $geofence_name, $jalur);
										// printf("===Speed : %s Limit : %s \r\n", $gpsspeed_kph, $geofence_speed_limit);

										if($gpsspeed_kph <= $geofence_speed_limit){
											$skip_sent = 1;
										}

										if($geofence_speed_limit == 0){
											$skip_sent = 1;
										}

										$gpsspeed_kph         = $gpsspeed_kph-3;
										$geofence_speed_limit = $geofence_speed_limit-3;

								if($skip_sent == 0){
									array_push($dataoverspeed, array(
										"isfatigue"          => "no",
										"jalur_name"         => $jalur,
										"vehicle_no"         => $vehicle_no,
										"vehicle_name"       => $vehicle_name,
										"vehicle_company"    => $vehicle_company,
										"violation" 				 => "Overspeed",
										"violation_type" 		 => "overspeed",
										"vehicle_device"     => $rows[$i]->gps_name.'@'.$rows[$i]->gps_host,
										"gps_latitude_real"  => $rows[$i]->gps_latitude_real,
										"gps_longitude_real" => $rows[$i]->gps_longitude_real,
										"gps_speed"          => $gpsspeed_kph,
										"gps_speed_limit"    => $geofence_speed_limit,
										"gps_time"           => $gps_time,
										"geofence"           => $geofence_name,
										"position"           => $position_name,
									));
								}
							}
							}
					}

					// echo "<pre>";
					// var_dump($dataoverspeed);die();
					// echo "<pre>";

					$totaldataoverspeed    = sizeof($dataoverspeed);
					$totaldataoverspeedfix = 0;
						if ($totaldataoverspeed < 10) {
							$totaldataoverspeedfix = "0".$totaldataoverspeed;
						}else {
							$totaldataoverspeedfix = $totaldataoverspeed;
						}

				// echo "<pre>";
				// var_dump($dataoverspeed);die();
				// echo "<pre>";

				if ($simultantype == 0) {
					$sdate = date("Y-m-d H:i:s", strtotime("-3 minutes"));
				}else {
					$nowtime          = date("Y-m-d H:i:s");
					$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
					$sdate            = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-3hours"));
				}

			$masterviolation   = $this->m_violation->getviolation("ts_violation", $sdate, $contractor, $alarmtypefromaster);

			// echo "<pre>";
			// var_dump($sdate);die();
			// echo "<pre>";
			$violation_call           = array();
			$violation_cardistance    = array();
			$violation_distracted     = array();
			$violation_fatigue        = array();
			$violation_smoking        = array();
			$violation_driverabnormal = array();

				if (sizeof($masterviolation) > 0) {
						for ($j=0; $j < sizeof($masterviolation); $j++) {
							if ($masterviolation[$j]['violation_fatigue'] != "") {
								$json_fatigue            = json_decode($masterviolation[$j]['violation_fatigue']);
								$forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
								$forcheck_gps_time       = $json_fatigue[0]->gps_time;
								$checkthis               = $this->m_violation->getfrommaster($forcheck_vehicledevice);
								$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
								// $jalurname               = $jsonautocheck->auto_last_road;
								$jalurname               = $masterviolation[$j]['violation_jalur'];

								$positionforfilter = $masterviolation[$j]['violation_position'];
									if ($positionforfilter != "") {
										// UNTUK UMUM START
										if (in_array($positionforfilter, $street_onduty)){

											$alarmreportnamefix = "";
											$alarmreporttype = $json_fatigue[0]->gps_alertid;
												if ($alarmreporttype == 626) {
													$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
												}elseif ($alarmreporttype == 627) {
													$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
												}else {
													$alarmreportnamefix = $json_fatigue[0]->gps_alert;
												}

												if ($alarmreporttype != 624 && $alarmreporttype != 625) {
													if (in_array($masterviolation[$j]['violation_position'], $street_onduty)) {
														array_push($datafatigue, array(
															 "isfatigue"          => $json_fatigue[0]->isfatigue,
															 "jalur_name"         => $jalurname,
															 "vehicle_no"         => $json_fatigue[0]->vehicle_no,
															 "vehicle_name"       => $json_fatigue[0]->vehicle_name,
															 "vehicle_company"    => $json_fatigue[0]->vehicle_company,
															 "vehicle_device"     => $json_fatigue[0]->vehicle_device,
															 "vehicle_mv03"       => $json_fatigue[0]->vehicle_mv03,
															 "gps_alert"          => $alarmreportnamefix,
															 "violation" 				  => $alarmreportnamefix,
															 "violation_type" 		=> "not_overspeed",
															 "gps_time"           => $json_fatigue[0]->gps_time,
															 "auto_last_update"   => $jsonautocheck->auto_last_update,
															 "auto_last_check"    => $jsonautocheck->auto_last_check,
															 "gps_latitude_real"  => $json_fatigue[0]->gps_latitude_real,
															 "gps_longitude_real" => $json_fatigue[0]->gps_longitude_real,
															 "position"           => $masterviolation[$j]['violation_position'],
															 "auto_last_position" => $jsonautocheck->auto_last_position,
															 "gps_speed"          => $json_fatigue[0]->gps_speed,
														));
													}
												}
										}
										// UNTUK UMUM END
									}
						}
					}

					$lasttime     = $masterviolation[0]['violation_update'];
					// $lasttime     = date("Y-m-d H:i:s", strtotime($masterviolation[0]['violation_update']."-15 minutes"));
				}else {
					$lasttime = $sdate;
				}

				// alarmtypefromaster

				if ($violationmasterselect == 6) {
					$violationmix = array_merge($dataoverspeed);
				}elseif ($violationmasterselect == 0) {
					$violationmix = array_merge($dataoverspeed, $datafatigue);
				}else {
					$violationmix = array_merge($datafatigue);
				}

			// echo "<pre>";
			// var_dump($violationmasterselect);die();
			// echo "<pre>";

				if (sizeof($violationmix) > 0) {
					// ROM ROAD
					$dataJumlahInRomRoadMuatan['ROM_B1_ROAD'] = 0;
					$dataJumlahInRomRoadMuatan['ROM_B2_ROAD'] = 0;
					$dataJumlahInRomRoadMuatan['ROM_B3_ROAD'] = 0;
					$dataJumlahInRomRoadMuatan['EST_ROAD']    = 0;

					$dataJumlahInRomRoadKosongan['ROM_B1_ROAD'] = 0;
					$dataJumlahInRomRoadKosongan['ROM_B2_ROAD'] = 0;
					$dataJumlahInRomRoadKosongan['ROM_B3_ROAD'] = 0;
					$dataJumlahInRomRoadKosongan['EST_ROAD']    = 0;

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

					$masterdatavehicle = $violationmix; // SWITCH VARIABLE BIAR GAMPANG
					// $jalurarray = array();
					// echo "<pre>";
					// var_dump($masterdatavehicle);die();
					// echo "<pre>";

					for ($k=0; $k < sizeof($masterdatavehicle); $k++) {
						$datalastposition = $masterdatavehicle[$k]['position'];
						$jalur_name       = $masterdatavehicle[$k]['jalur_name'];

						// array_push($jalurarray, array(
						// 	"jalur"    => $datalastposition,
						// 	"position" => $jalur_name
						// ));

							if ($jalur_name == "kosongan") {
								if ($datalastposition == "TIA KM 0" || $datalastposition == "TIA KM 0.5") {
									$dataJumlahInKmKosongan['KM_1'] += 1;
								}elseif ($datalastposition == "TIA KM 1" || $datalastposition == "TIA KM 1.5") {
									$dataJumlahInKmKosongan['KM_2'] += 1;
								}elseif ($datalastposition == "TIA KM 2" || $datalastposition == "TIA KM 2.5") {
									$dataJumlahInKmKosongan['KM_3'] += 1;
								}elseif ($datalastposition == "TIA KM 3" || $datalastposition == "TIA KM 3.5") {
									$dataJumlahInKmKosongan['KM_4'] += 1;
								}elseif ($datalastposition == "TIA KM 4" || $datalastposition == "TIA KM 4.5") {
									$dataJumlahInKmKosongan['KM_5'] += 1;
								}elseif ($datalastposition == "TIA KM 5" || $datalastposition == "TIA KM 5.5") {
									$dataJumlahInKmKosongan['KM_6'] += 1;
								}elseif ($datalastposition == "TIA KM 6" || $datalastposition == "TIA KM 6.5") {
									$dataJumlahInKmKosongan['KM_7'] += 1;
								}elseif ($datalastposition == "TIA KM 7" || $datalastposition == "TIA KM 7.5") {
									$dataJumlahInKmKosongan['KM_8'] += 1;
								}elseif ($datalastposition == "TIA KM 8" || $datalastposition == "TIA KM 8.5") {
									$dataJumlahInKmKosongan['KM_9'] += 1;
								}elseif ($datalastposition == "TIA KM 9" || $datalastposition == "TIA KM 9.5") {
									$dataJumlahInKmKosongan['KM_10'] += 1;
								}elseif ($datalastposition == "TIA KM 10" || $datalastposition == "TIA KM 10.5") {
									$dataJumlahInKmKosongan['KM_11'] += 1;
								}elseif ($datalastposition == "TIA KM 11" || $datalastposition == "TIA KM 11.5") {
									$dataJumlahInKmKosongan['KM_12'] += 1;
								}elseif ($datalastposition == "TIA KM 12" || $datalastposition == "TIA KM 12.5") {
									$dataJumlahInKmKosongan['KM_13'] += 1;
								}elseif ($datalastposition == "TIA KM 13" || $datalastposition == "TIA KM 13.5") {
									$dataJumlahInKmKosongan['KM_14'] += 1;
								}elseif ($datalastposition == "TIA KM 14" || $datalastposition == "TIA KM 14.5") {
									$dataJumlahInKmKosongan['KM_15'] += 1;
								}elseif ($datalastposition == "TIA KM 15" || $datalastposition == "TIA KM 15.5") {
									$dataJumlahInKmKosongan['KM_16'] += 1;
								}elseif ($datalastposition == "TIA KM 16" || $datalastposition == "TIA KM 16.5") {
									$dataJumlahInKmKosongan['KM_17'] += 1;
								}elseif ($datalastposition == "TIA KM 17" || $datalastposition == "TIA KM 17.5") {
									$dataJumlahInKmKosongan['KM_18'] += 1;
								}elseif ($datalastposition == "TIA KM 18" || $datalastposition == "TIA KM 18.5") {
									$dataJumlahInKmKosongan['KM_19'] += 1;
								}elseif ($datalastposition == "TIA KM 19" || $datalastposition == "TIA KM 19.5") {
									$dataJumlahInKmKosongan['KM_20'] += 1;
								}elseif ($datalastposition == "TIA KM 20" || $datalastposition == "TIA KM 20.5") {
									$dataJumlahInKmKosongan['KM_21'] += 1;
								}elseif ($datalastposition == "TIA KM 21" || $datalastposition == "TIA KM 21.5") {
									$dataJumlahInKmKosongan['KM_22'] += 1;
								}elseif ($datalastposition == "TIA KM 22" || $datalastposition == "TIA KM 22.5") {
									$dataJumlahInKmKosongan['KM_23'] += 1;
								}elseif ($datalastposition == "TIA KM 23" || $datalastposition == "TIA KM 23.5") {
									$dataJumlahInKmKosongan['KM_24'] += 1;
								}elseif ($datalastposition == "TIA KM 24" || $datalastposition == "TIA KM 24.5") {
									$dataJumlahInKmKosongan['KM_25'] += 1;
								}elseif ($datalastposition == "TIA KM 25" || $datalastposition == "TIA KM 25.5") {
									$dataJumlahInKmKosongan['KM_26'] += 1;
								}elseif ($datalastposition == "TIA KM 26" || $datalastposition == "TIA KM 26.5") {
									$dataJumlahInKmKosongan['KM_27'] += 1;
								}elseif ($datalastposition == "TIA KM 27" || $datalastposition == "TIA KM 27.5") {
									$dataJumlahInKmKosongan['KM_28'] += 1;
								}elseif ($datalastposition == "TIA KM 28" || $datalastposition == "TIA KM 28.5") {
									$dataJumlahInKmKosongan['KM_29'] += 1;
								}elseif ($datalastposition == "TIA KM 29" || $datalastposition == "TIA KM 29.5" || $datalastposition == "TIA KM 30" || $datalastposition == "TIA KM 30.5") {
									$dataJumlahInKmKosongan['KM_30'] += 1;
								}elseif ($datalastposition == "ROM B1 ROAD") {
									$dataJumlahInRomRoadKosongan['ROM_B1_ROAD'] += 1;
								}elseif ($datalastposition == "ROM B2 ROAD") {
									$dataJumlahInRomRoadKosongan['ROM_B2_ROAD'] += 1;
								}elseif ($datalastposition == "ROM B3 ROAD") {
									$dataJumlahInRomRoadKosongan['ROM_B3_ROAD'] += 1;
								}elseif ($datalastposition == "EST ROAD") {
									$dataJumlahInRomRoadKosongan['EST_ROAD'] += 1;
								}
							}else {
								if ($datalastposition == "TIA KM 0" || $datalastposition == "TIA KM 0.5") {
									$dataJumlahInKmMuatan['KM_1'] += 1;
								}elseif ($datalastposition == "TIA KM 1" || $datalastposition == "KM 1.5") {
									$dataJumlahInKmMuatan['KM_2'] += 1;
								}elseif ($datalastposition == "TIA KM 2" || $datalastposition == "TIA KM 2.5") {
									$dataJumlahInKmMuatan['KM_3'] += 1;
								}elseif ($datalastposition == "TIA KM 3" || $datalastposition == "TIA KM 3.5") {
									$dataJumlahInKmMuatan['KM_4'] += 1;
								}elseif ($datalastposition == "TIA KM 4" || $datalastposition == "TIA KM 4.5") {
									$dataJumlahInKmMuatan['KM_5'] += 1;
								}elseif ($datalastposition == "TIA KM 5" || $datalastposition == "TIA KM 5.5") {
									$dataJumlahInKmMuatan['KM_6'] += 1;
								}elseif ($datalastposition == "TIA KM 6" || $datalastposition == "TIA KM 6.5") {
									$dataJumlahInKmMuatan['KM_7'] += 1;
								}elseif ($datalastposition == "TIA KM 7" || $datalastposition == "TIA KM 7.5") {
									$dataJumlahInKmMuatan['KM_8'] += 1;
								}elseif ($datalastposition == "TIA KM 8" || $datalastposition == "TIA KM 8.5") {
									$dataJumlahInKmMuatan['KM_9'] += 1;
								}elseif ($datalastposition == "TIA KM 9" || $datalastposition == "TIA KM 9.5") {
									$dataJumlahInKmMuatan['KM_10'] += 1;
								}elseif ($datalastposition == "TIA KM 10" || $datalastposition == "TIA KM 10.5") {
									$dataJumlahInKmMuatan['KM_11'] += 1;
								}elseif ($datalastposition == "TIA KM 11" || $datalastposition == "TIA KM 11.5") {
									$dataJumlahInKmMuatan['KM_12'] += 1;
								}elseif ($datalastposition == "TIA KM 12" || $datalastposition == "TIA KM 12.5") {
									$dataJumlahInKmMuatan['KM_13'] += 1;
								}elseif ($datalastposition == "TIA KM 13" || $datalastposition == "TIA KM 13.5") {
									$dataJumlahInKmMuatan['KM_14'] += 1;
								}elseif ($datalastposition == "TIA KM 14" || $datalastposition == "TIA KM 14.5") {
									$dataJumlahInKmMuatan['KM_15'] += 1;
								}elseif ($datalastposition == "TIA KM 15" || $datalastposition == "TIA KM 15.5") {
									$dataJumlahInKmMuatan['KM_16'] += 1;
								}elseif ($datalastposition == "TIA KM 16" || $datalastposition == "TIA KM 16.5") {
									$dataJumlahInKmMuatan['KM_17'] += 1;
								}elseif ($datalastposition == "TIA KM 17" || $datalastposition == "TIA KM 17.5") {
									$dataJumlahInKmMuatan['KM_18'] += 1;
								}elseif ($datalastposition == "TIA KM 18" || $datalastposition == "TIA KM 18.5") {
									$dataJumlahInKmMuatan['KM_19'] += 1;
								}elseif ($datalastposition == "TIA KM 19" || $datalastposition == "TIA KM 19.5") {
									$dataJumlahInKmMuatan['KM_20'] += 1;
								}elseif ($datalastposition == "TIA KM 20" || $datalastposition == "TIA KM 20.5") {
									$dataJumlahInKmMuatan['KM_21'] += 1;
								}elseif ($datalastposition == "TIA KM 21" || $datalastposition == "TIA KM 21.5") {
									$dataJumlahInKmMuatan['KM_22'] += 1;
								}elseif ($datalastposition == "TIA KM 22" || $datalastposition == "TIA KM 22.5") {
									$dataJumlahInKmMuatan['KM_23'] += 1;
								}elseif ($datalastposition == "TIA KM 23" || $datalastposition == "TIA KM 23.5") {
									$dataJumlahInKmMuatan['KM_24'] += 1;
								}elseif ($datalastposition == "TIA KM 24" || $datalastposition == "TIA KM 24.5") {
									$dataJumlahInKmMuatan['KM_25'] += 1;
								}elseif ($datalastposition == "TIA KM 25" || $datalastposition == "TIA KM 25.5") {
									$dataJumlahInKmMuatan['KM_26'] += 1;
								}elseif ($datalastposition == "TIA KM 26" || $datalastposition == "TIA KM 26.5") {
									$dataJumlahInKmMuatan['KM_27'] += 1;
								}elseif ($datalastposition == "TIA KM 27" || $datalastposition == "TIA KM 27.5") {
									$dataJumlahInKmMuatan['KM_28'] += 1;
								}elseif ($datalastposition == "TIA KM 28" || $datalastposition == "TIA KM 28.5") {
									$dataJumlahInKmMuatan['KM_29'] += 1;
								}elseif ($datalastposition == "TIA KM 29" || $datalastposition == "TIA KM 29.5" || $datalastposition == "TIA KM 30" || $datalastposition == "TIA KM 30.5") {
									$dataJumlahInKmMuatan['KM_30'] += 1;
								}elseif ($datalastposition == "ROM B1 ROAD") {
									$dataJumlahInRomRoadMuatan['ROM_B1_ROAD'] += 1;
								}elseif ($datalastposition == "ROM B2 ROAD") {
									$dataJumlahInRomRoadMuatan['ROM_B2_ROAD'] += 1;
								}elseif ($datalastposition == "ROM B3 ROAD") {
									$dataJumlahInRomRoadMuatan['ROM_B3_ROAD'] += 1;
								}elseif ($datalastposition == "EST ROAD") {
									$dataJumlahInRomRoadMuatan['EST_ROAD'] += 1;
								}
							}
					}

					// echo "<pre>";
					// var_dump($dataJumlahInRomRoadKosongan);die();
					// echo "<pre>";

					// KHUSUS UNTUK SUMMARY START
					if (sizeof($datafatigue) > 0) {
						for ($x=0; $x < sizeof($datafatigue); $x++) {
							$violationalertnya = $datafatigue[$x]['gps_alert'];

							if (strpos($violationalertnya, "Call") !== false) {
							  array_push($violation_call, array(
							     "isfatigue"           => $datafatigue[$x]['isfatigue'],
							     "jalur_name"          => $datafatigue[$x]['jalur_name'],
							     "vehicle_no"          => $datafatigue[$x]['vehicle_no'],
							     "vehicle_name"        => $datafatigue[$x]['vehicle_name'],
							     "vehicle_company"     => $datafatigue[$x]['vehicle_company'],
							     "vehicle_device"      => $datafatigue[$x]['vehicle_device'],
							     "vehicle_mv03"        => $datafatigue[$x]['vehicle_mv03'],
							     "gps_alert"           => $datafatigue[$x]['gps_alert'],
							     "violation" 				   => $datafatigue[$x]['violation'],
							     "violation_type" 		 => $datafatigue[$x]['violation_type'],
							     "gps_time"            => $datafatigue[$x]['gps_time'],
							     "auto_last_update"    => $datafatigue[$x]['auto_last_update'],
							     "auto_last_check"     => $datafatigue[$x]['auto_last_check'],
							     "gps_latitude_real"   => $datafatigue[$x]['gps_latitude_real'],
							     "gps_longitude_real"  => $datafatigue[$x]['gps_longitude_real'],
							     "position"            => $datafatigue[$x]['position'],
							     "auto_last_position"  => $datafatigue[$x]['auto_last_position'],
							     "gps_speed"           => $datafatigue[$x]['gps_speed'],
							  ));
							}elseif (strpos($violationalertnya, "Distance") !== false) {
							  array_push($violation_cardistance, array(
							    "isfatigue"          => $datafatigue[$x]['isfatigue'],
							    "jalur_name"         => $datafatigue[$x]['jalur_name'],
							    "vehicle_no"         => $datafatigue[$x]['vehicle_no'],
							    "vehicle_name"       => $datafatigue[$x]['vehicle_name'],
							    "vehicle_company"    => $datafatigue[$x]['vehicle_company'],
							    "vehicle_device"     => $datafatigue[$x]['vehicle_device'],
							    "vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
							    "gps_alert"          => $datafatigue[$x]['gps_alert'],
							    "violation" 				 => $datafatigue[$x]['violation'],
							    "violation_type" 		 => $datafatigue[$x]['violation_type'],
							    "gps_time"           => $datafatigue[$x]['gps_time'],
							    "auto_last_update"   => $datafatigue[$x]['auto_last_update'],
							    "auto_last_check"    => $datafatigue[$x]['auto_last_check'],
							    "gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
							    "gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
							    "position"           => $datafatigue[$x]['position'],
							    "auto_last_position" => $datafatigue[$x]['auto_last_position'],
							    "gps_speed"          => $datafatigue[$x]['gps_speed'],
							  ));
							}
							// elseif (strpos($violationalertnya, "Distracted") !== false) {
							//   array_push($violation_distracted, array(
							//     "isfatigue"          => $datafatigue[$x]['isfatigue'],
							//     "jalur_name"         => $datafatigue[$x]['jalur_name'],
							//     "vehicle_no"         => $datafatigue[$x]['vehicle_no'],
							//     "vehicle_name"       => $datafatigue[$x]['vehicle_name'],
							//     "vehicle_company"    => $datafatigue[$x]['vehicle_company'],
							//     "vehicle_device"     => $datafatigue[$x]['vehicle_device'],
							//     "vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
							//     "gps_alert"          => $datafatigue[$x]['gps_alert'],
							//     "violation" 				 => $datafatigue[$x]['violation'],
							//     "violation_type" 		 => $datafatigue[$x]['violation_type'],
							//     "gps_time"           => $datafatigue[$x]['gps_time'],
							//     "auto_last_update"   => $datafatigue[$x]['auto_last_update'],
							//     "auto_last_check"    => $datafatigue[$x]['auto_last_check'],
							//     "gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
							//     "gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
							//     "position"           => $datafatigue[$x]['position'],
							//     "auto_last_position" => $datafatigue[$x]['auto_last_position'],
							//     "gps_speed"          => $datafatigue[$x]['gps_speed'],
							//   ));
							// }
							elseif (strpos($violationalertnya, "Fatigue") !== false) {
							  array_push($violation_fatigue, array(
							    "isfatigue"          => $datafatigue[$x]['isfatigue'],
							    "jalur_name"         => $datafatigue[$x]['jalur_name'],
							    "vehicle_no"         => $datafatigue[$x]['vehicle_no'],
							    "vehicle_name"       => $datafatigue[$x]['vehicle_name'],
							    "vehicle_company"    => $datafatigue[$x]['vehicle_company'],
							    "vehicle_device"     => $datafatigue[$x]['vehicle_device'],
							    "vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
							    "gps_alert"          => $datafatigue[$x]['gps_alert'],
							    "violation" 				 => $datafatigue[$x]['violation'],
							    "violation_type" 		 => $datafatigue[$x]['violation_type'],
							    "gps_time"           => $datafatigue[$x]['gps_time'],
							    "auto_last_update"   => $datafatigue[$x]['auto_last_update'],
							    "auto_last_check"    => $datafatigue[$x]['auto_last_check'],
							    "gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
							    "gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
							    "position"           => $datafatigue[$x]['position'],
							    "auto_last_position" => $datafatigue[$x]['auto_last_position'],
							    "gps_speed"          => $datafatigue[$x]['gps_speed'],
							  ));
							}elseif (strpos($violationalertnya, "Smoking") !== false) {
							  array_push($violation_smoking, array(
							    "isfatigue"          => $datafatigue[$x]['isfatigue'],
							    "jalur_name"         => $datafatigue[$x]['jalur_name'],
							    "vehicle_no"         => $datafatigue[$x]['vehicle_no'],
							    "vehicle_name"       => $datafatigue[$x]['vehicle_name'],
							    "vehicle_company"    => $datafatigue[$x]['vehicle_company'],
							    "vehicle_device"     => $datafatigue[$x]['vehicle_device'],
							    "vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
							    "gps_alert"          => $datafatigue[$x]['gps_alert'],
							    "violation" 				 => $datafatigue[$x]['violation'],
							    "violation_type" 		 => $datafatigue[$x]['violation_type'],
							    "gps_time"           => $datafatigue[$x]['gps_time'],
							    "auto_last_update"   => $datafatigue[$x]['auto_last_update'],
							    "auto_last_check"    => $datafatigue[$x]['auto_last_check'],
							    "gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
							    "gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
							    "position"           => $datafatigue[$x]['position'],
							    "auto_last_position" => $datafatigue[$x]['auto_last_position'],
							    "gps_speed"          => $datafatigue[$x]['gps_speed'],
							  ));
							}elseif (strpos($violationalertnya, "Undetected") !== false) {
							  array_push($violation_driverabnormal, array(
							    "isfatigue"          => $datafatigue[$x]['isfatigue'],
							    "jalur_name"         => $datafatigue[$x]['jalur_name'],
							    "vehicle_no"         => $datafatigue[$x]['vehicle_no'],
							    "vehicle_name"       => $datafatigue[$x]['vehicle_name'],
							    "vehicle_company"    => $datafatigue[$x]['vehicle_company'],
							    "vehicle_device"     => $datafatigue[$x]['vehicle_device'],
							    "vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
							    "gps_alert"          => $datafatigue[$x]['gps_alert'],
							    "violation" 				 => $datafatigue[$x]['violation'],
							    "violation_type" 		 => $datafatigue[$x]['violation_type'],
							    "gps_time"           => $datafatigue[$x]['gps_time'],
							    "auto_last_update"   => $datafatigue[$x]['auto_last_update'],
							    "auto_last_check"    => $datafatigue[$x]['auto_last_check'],
							    "gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
							    "gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
							    "position"           => $datafatigue[$x]['position'],
							    "auto_last_position" => $datafatigue[$x]['auto_last_position'],
							    "gps_speed"          => $datafatigue[$x]['gps_speed'],
							  ));
							}
						}
					}

					// echo "<pre>";
					// var_dump("DONE");die();
					// echo "<pre>";

					$totalviolation_call           = sizeof($violation_call);
					$totalviolation_cardistance    = sizeof($violation_cardistance);
					// $totalviolation_distracted     = sizeof($violation_distracted);
					$totalviolation_fatigue        = sizeof($violation_fatigue);
					$totalviolation_smoking        = sizeof($violation_smoking);
					$totalviolation_driverabnormal = sizeof($violation_driverabnormal);
					// $total_violationall            = ($totaldataoverspeedfix + $totalviolation_call + $totalviolation_cardistance + $totalviolation_distracted + $totalviolation_fatigue + $totalviolation_smoking + $totalviolation_driverabnormal);
					$total_violationall            = ($totaldataoverspeedfix + $totalviolation_call + $totalviolation_cardistance + $totalviolation_fatigue + $totalviolation_smoking + $totalviolation_driverabnormal);

					$tv_call = 0;
						if ($totalviolation_call < 10) {
							$tv_call = "0".$totalviolation_call;
						}else {
							$tv_call = $totalviolation_call;
						}
					$tv_cardistance = 0;
						if ($totalviolation_cardistance < 10) {
							$tv_cardistance = "0".$totalviolation_cardistance;
						}else {
							$tv_cardistance = $totalviolation_cardistance;
						}
					// $tv_distracted = 0;
					// 	if ($totalviolation_distracted < 10) {
					// 		$tv_distracted = "0".$totalviolation_distracted;
					// 	}else {
					// 		$tv_distracted = $totalviolation_distracted;
					// 	}
					$tv_fatigue = 0;
						if ($totalviolation_fatigue < 10) {
							$tv_fatigue = "0".$totalviolation_fatigue;
						}else {
							$tv_fatigue = $totalviolation_fatigue;
						}
					$tv_smoking = 0;
						if ($totalviolation_smoking < 10) {
							$tv_smoking = "0".$totalviolation_smoking;
						}else {
							$tv_smoking = $totalviolation_smoking;
						}
					$tv_driverabnormal = 0;
						if ($totalviolation_driverabnormal < 10) {
							$tv_driverabnormal = "0".$totalviolation_driverabnormal;
						}else {
							$tv_driverabnormal = $totalviolation_driverabnormal;
						}

						if ($violationmasterselect == 1) {
							$totaldataoverspeedfix = "00";
							$tv_call               = $tv_call;
							$tv_cardistance        = "00";
							// $tv_distracted         = "00";
							$tv_fatigue            = "00";
							$tv_smoking            = "00";
							$tv_driverabnormal     = "00";
						}elseif ($violationmasterselect == 2) {
							$totaldataoverspeedfix = "00";
							$tv_call               = "00";
							$tv_cardistance        = $tv_cardistance;
							// $tv_distracted         = "00";
							$tv_fatigue            = "00";
							$tv_smoking            = "00";
							$tv_driverabnormal     = "00";
						}elseif ($violationmasterselect == 3) {
							$totaldataoverspeedfix = "00";
							$tv_call               = "00";
							$tv_cardistance        = "00";
							$tv_distracted         = $tv_distracted;
							$tv_fatigue            = "00";
							$tv_smoking            = "00";
							$tv_driverabnormal     = "00";
						}elseif ($violationmasterselect == 4) {
							$totaldataoverspeedfix = "00";
							$tv_call               = "00";
							$tv_cardistance        = "00";
							// $tv_distracted         = "00";
							$tv_fatigue            = $tv_fatigue;
							$tv_smoking            = "00";
							$tv_driverabnormal     = "00";
						}elseif ($violationmasterselect == 5) {
							$totaldataoverspeedfix = "00";
							$tv_call               = "00";
							$tv_cardistance        = "00";
							// $tv_distracted         = "00";
							$tv_fatigue            = "00";
							$tv_smoking            = $tv_smoking;
							$tv_driverabnormal     = "00";
						}elseif ($violationmasterselect == 6) {
							$totaldataoverspeedfix = $totaldataoverspeedfix;
							$tv_call               = "00";
							$tv_cardistance        = "00";
							// $tv_distracted         = "00";
							$tv_fatigue            = "00";
							$tv_smoking            = "00";
							$tv_driverabnormal     = "00";
						}elseif ($violationmasterselect == 7) {
							$totaldataoverspeedfix = "00";
							$tv_call               = "00";
							$tv_cardistance        = "00";
							// $tv_distracted         = "00";
							$tv_fatigue            = "00";
							$tv_smoking            = "00";
							$tv_driverabnormal     = $tv_driverabnormal;
						}else {
							$totaldataoverspeedfix = $totaldataoverspeedfix;
							$tv_call               = $tv_call;
							$tv_cardistance        = $tv_cardistance;
							// $tv_distracted         = $tv_distracted;
							$tv_fatigue            = $tv_fatigue;
							$tv_smoking            = $tv_smoking;
							$tv_driverabnormal     = $tv_driverabnormal;
						}
					//KHUSUS UNTUK SUMMARY END


					echo json_encode(array(
						"msg"                      => "success",
						"code"                     => 200,
						// "lasttime"              => $lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour")),
						"lasttime"                 => $startdate,
						"simultantype"             => 1,
						"total_violationall"	     => $total_violationall,
						"total_ov"			           => $totaldataoverspeedfix,
						"violationmix"             => $violationmix,
						"dataMuatan"               => $dataJumlahInKmMuatan,
						"dataKosongan"             => $dataJumlahInKmKosongan,
						"dataKosonganRomRoad"      => $dataJumlahInRomRoadKosongan,
						"dataMuatanRomRoad"        => $dataJumlahInRomRoadMuatan,
						"dataMuatan2"              => $dataJumlahInKmMuatan_2,
						"dataKosongan2"            => $dataJumlahInKmKosongan_2,
						"tv_call"                  => $tv_call,
						"tv_cardistance"           => $tv_cardistance,
						// "tv_distracted"            => $tv_distracted,
						"tv_fatigue"               => $tv_fatigue,
						"tv_smoking"               => $tv_smoking,
						"tv_driverabnormal"        => $tv_driverabnormal,
						"violation_call"           => $violation_call,
						"violation_cardistance"    => $violation_cardistance,
						"violation_distracted"     => $violation_distracted,
						"violation_fatigue"        => $violation_fatigue,
						"violation_smoking"        => $violation_smoking,
						"violation_driverabnormal" => $violation_driverabnormal,
						// "dataRomFix"    => $dataRomFix
					));
				}else {
					echo json_encode(array(
						"msg"          => "failed",
						"code"         => 400,
						// "lasttime"  => $lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour")),
						"lasttime"     => $startdate,
						"simultantype" => 1,
						"violationmix" => $violationmix
					));
				}
	  }

	function violation_dev(){
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

  	$companyid                   = $this->sess->user_company;
  	$user_dblive                 = $this->sess->user_dblive;
  	$mastervehicle               = $this->m_poipoolmaster->getmastervehicleforheatmap();
		$violationmaster             = $this->m_violation->getviolationmaster();

  	$datafix                     = array();
  	$deviceidygtidakada          = array();
  	$statusvehicle['engine_on']  = 0;
  	$statusvehicle['engine_off'] = 0;

  	for ($i=0; $i < sizeof($mastervehicle); $i++) {
  		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
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

			// GET ROM ROAD
			$romRoad                  = $this->m_poipoolmaster->getstreet_now2(5);
			$this->params['rom_road'] = $romRoad;

  	// echo "<pre>";
  	// var_dump($romRoad);die();
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

  	$this->params['resultactive']    = $this->dashboardmodel->vehicleactive();
  	$this->params['resultexpired']   = $this->dashboardmodel->vehicleexpired();
  	$this->params['resulttotaldev']  = $this->dashboardmodel->totaldevice();
  	$this->params['mapsetting']      = $this->m_poipoolmaster->getmapsetting();
  	$this->params['poolmaster']      = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$this->params['violationmaster'] = $violationmaster;

  	// echo "<pre>";
  	// var_dump($this->params['violationmaster']);die();
  	// echo "<pre>";

  	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  		if ($privilegecode == 1) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violationdev', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  		}elseif ($privilegecode == 2) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violationdev', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  		}elseif ($privilegecode == 3) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violationdev', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  		}elseif ($privilegecode == 4) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violationdev', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  		}elseif ($privilegecode == 5) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violationdev', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  		}elseif ($privilegecode == 6) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violationdev', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  		}else {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/violation/v_monitoring_violationdev', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  		}
	}

	function km_quickcount_new2(){
		$simultantype 				         = $_POST['simultantype'];
		$lasttime 				             = $_POST['lasttime_violation'];
		$contractor 				           = $_POST['contractor'];
		$violationmasterselect 				 = $_POST['violationmaster'];
		$alarmtypefromaster            = array();
		$dataoverspeed 								 = array();
		$datafatigue                   = array();
		$dataKmMuatanFix               = array();
		$dataKmKosonganFix             = array();
		$violationmix                  = array();

		$street_onduty = array("PORT BIB","PORT BIR","PORT TIA",
								//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
								"ROM A1","ROM B1","ROM B2","ROM B3","ROM EST",
								"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
								//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
								//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",

								"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5",
								"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
								"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
								"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31",

								"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
								"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
								"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
								"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
								"PORT BIB - Antrian","Port BIB - Antrian"
							);

		if ($violationmasterselect == 6) {
			$alarmtypefromaster[] = 9999;
		}else {
			if ($violationmasterselect != "0") {
				$alarmbymaster = $this->m_violation->getalarmbytype($violationmasterselect);
				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
				}
			}
		}

		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id, user_dblive");
		$this->db->order_by("user_id","asc");
		$this->db->where("user_id", 4408);
		$q         = $this->db->get("user");
		$row       = $q->row();
		$total_row = count($row);

		$nowtime          = date("Y-m-d H:i:s");
		$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
		$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-60second"));

		$startdate        = $last_fiveminutes;
		$enddate          = $nowtime_wita;
		$sdate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
		$edate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

		//print_r($sdate." ".$edate);exit();
		if(count($row)>0){
			$user_dblive = $row->user_dblive;
		}

		$this->dbalert = $this->load->database($user_dblive, TRUE);
		$this->dbalert->where("gps_time >=", $sdate);
				//$this->dbalert->where("gps_time <=", $edate);
		$this->dbalert->where("gps_speed >=", 11.3);  // >= 21 kph
		//$this->dbalert->where("gps_speed_status", 1);
		$this->dbalert->where("gps_alert", "Speeding Alarm");
		// $this->dbalert->where("gps_notif", 0); //belum ke send
		//$this->dbalert->limit(5); //limit
		$this->dbalert->order_by("gps_time","asc");
		$this->dbalert->group_by(array("gps_name"));
		$q           = $this->dbalert->get("gps_alert");
		$rows        = $q->result();
		$total_alert = count($rows);

			// echo "<pre>";
			// var_dump($rows);die();
			// echo "<pre>";

			$user_level      = $this->sess->user_level;
			$user_parent     = $this->sess->user_parent;
			$user_company    = $this->sess->user_company;
			$user_subcompany = $this->sess->user_subcompany;
			$user_group      = $this->sess->user_group;
			$user_subgroup   = $this->sess->user_subgroup;
			$user_dblive 	   = $this->sess->user_dblive;
			$privilegecode 	 = $this->sess->user_id_role;
			$user_id_fix     = $this->sess->user_id;

			if($privilegecode == 1){
				$contractor = $contractor;
			}else if($privilegecode == 2){
				$contractor = $contractor;
			}else if($privilegecode == 3){
				$contractor = $contractor;
			}else if($privilegecode == 4){
				$contractor = $contractor;
			}else if($privilegecode == 5){
				$contractor = $user_company;
			}else if($privilegecode == 6){
				$contractor = $user_company;
			}else if($privilegecode == 0){
				$contractor = $contractor;
			}else{
				$contractor = $contractor;
			}

			if($total_alert >0){
				$j = 1;
				for ($i=0;$i<count($rows);$i++){
					$title_name      = "OVERSPEED ALARM";
					$vehicle_device  = $rows[$i]->gps_name."@".$rows[$i]->gps_host;
					$data_vehicle    = $this->getvehicle2($vehicle_device, $contractor);

						if ($data_vehicle) {
							// echo "<pre>";
							// var_dump($data_vehicle);die();
							// echo "<pre>";

							$vehicle_id      = $data_vehicle->vehicle_id;
							$vehicle_no      = $data_vehicle->vehicle_no;
							$vehicle_name    = $data_vehicle->vehicle_name;
							$vehicle_company = $data_vehicle->vehicle_company;
							$vehicle_dblive  = $data_vehicle->vehicle_dbname_live;

							$driver_name = "-";

							// printf("===Process Alarm ID %s %s %s (%d/%d) \r\n", $rows[$i]->gps_id, $data_vehicle->vehicle_no, $data_vehicle->vehicle_device, $j, $total_alert);
							$skip_sent = 0;
							$position = $this->getPosition_other($rows[$i]->gps_longitude_real,$rows[$i]->gps_latitude_real);

								if(isset($position)){
									$ex_position = explode(",",$position->display_name);
									if(count($ex_position)>0){
										$position_name = $ex_position[0];
									}else{
										$position_name = $ex_position[0];
									}
								}else{
									$position_name = $position->display_name;
										$skip_sent = 1;
								}

									//filter in location array HAULING, ROM, PORT

									if (in_array($position_name, $street_onduty)){
										$skip_sent = 0;
									}else{
										$skip_sent = 1;
									}

							$gps_time   = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))); //sudah wita
							$coordinate = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
							//$url = "http://maps.google.com/maps?z=12&t=m&q=loc:".$coordinate;
							$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
							//https://www.google.com/maps/search/?api=1&query=-6.2915399,106.9660776 : ex
							$gpsspeed_kph = round($rows[$i]->gps_speed*1.852,0);
							$direction    = $rows[$i]->gps_course;
							$jalur        = $this->get_jalurname_new($direction);

							if($jalur == ""){
								$jalur = $rows[$i]->gps_last_road_type;
							}

							$rowgeofence = $this->getGeofence_location_live($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $vehicle_dblive);

											if($rowgeofence == false){
												$geofence_id           = 0;
												$geofence_name         = "";
												$geofence_speed        = 0;
												$geofence_speed_muatan = "";
												$geofence_type         = "";
												$geofence_speed_limit  = 0;

											}else{
												$geofence_id           = $rowgeofence->geofence_id;
												$geofence_name         = $rowgeofence->geofence_name;
												$geofence_speed        = $rowgeofence->geofence_speed;
												$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
												$geofence_type         = $rowgeofence->geofence_type;

												if($jalur == "muatan"){
													$geofence_speed_limit = $geofence_speed_muatan;
												}else if($jalur == "kosongan"){
													$geofence_speed_limit = $geofence_speed;
												}else{
													$geofence_speed_limit = 0;

												}
											}
									// printf("===Position : %s Geofence : %s Jalur: %s \r\n", $position_name, $geofence_name, $jalur);
									// printf("===Speed : %s Limit : %s \r\n", $gpsspeed_kph, $geofence_speed_limit);

									if($gpsspeed_kph <= $geofence_speed_limit){
										$skip_sent = 1;
									}

									if($geofence_speed_limit == 0){
										$skip_sent = 1;
									}

									$gpsspeed_kph         = $gpsspeed_kph-3;
									$geofence_speed_limit = $geofence_speed_limit-3;

							if($skip_sent == 0){
								array_push($dataoverspeed, array(
									"isfatigue"          => "no",
									"jalur_name"         => $jalur,
									"vehicle_no"         => $vehicle_no,
									"vehicle_name"       => $vehicle_name,
									"vehicle_company"    => $vehicle_company,
									"violation" 				 => "Overspeed",
									"violation_type" 		 => "overspeed",
									"vehicle_device"     => $rows[$i]->gps_name.'@'.$rows[$i]->gps_host,
									"gps_latitude_real"  => $rows[$i]->gps_latitude_real,
									"gps_longitude_real" => $rows[$i]->gps_longitude_real,
									"gps_speed"          => $gpsspeed_kph,
									"gps_speed_limit"    => $geofence_speed_limit,
									"gps_time"           => $gps_time,
									"geofence"           => $geofence_name,
									"position"           => $position_name,
								));
							}
						}
						}
				}

				// $dataoverspeed = array(
				// 	array(
				// 		"geofence"           => "K30/M30 - 17",
				// 		"gps_latitude_real"  => "-3.659191",
				// 		"gps_longitude_real" => "115.649125",
				// 		"gps_speed"          => 56,
				// 		"gps_speed_limit"    => 30,
				// 		"gps_time"           => "29-05-2022 12:12:48",
				// 		"isfatigue"          => "yes",
				// 		"jalur_name"         => "muatan",
				// 		"position"           => "ROM B2 ROAD",
				// 		"vehicle_company"    => "1946",
				// 		"vehicle_device"     => "869926046535932@VT200",
				// 		"vehicle_name"       => "Hino 500",
				// 		"vehicle_no"         => "MKS 162",
				// 		"violation"          => "Fatigue Driving Alarm Level One",
				// 		"violation_type"     => "fatigue",
				// 	),
				// 	array(
				// 		"geofence"           => "K30/M30 - 17",
				// 		"gps_latitude_real"  => "-3.659191",
				// 		"gps_longitude_real" => "115.649125",
				// 		"gps_speed"          => 56,
				// 		"gps_speed_limit"    => 30,
				// 		"gps_time"           => "29-05-2022 12:12:48",
				// 		"isfatigue"          => "no",
				// 		"jalur_name"         => "muatan",
				// 		"position"           => "ROM B1 ROAD",
				// 		"vehicle_company"    => "1834",
				// 		"vehicle_device"     => "869926046535932@VT200",
				// 		"vehicle_name"       => "Hino 500",
				// 		"vehicle_no"         => "BKA 124",
				// 		"violation"          => "Overspeed",
				// 		"violation_type"     => "overspeed",
				// 	),
				// 	array(
				// 		"geofence"           => "K30/M30 - 17",
				// 		"gps_latitude_real"  => "-3.659191",
				// 		"gps_longitude_real" => "115.649125",
				// 		"gps_speed"          => 56,
				// 		"gps_speed_limit"    => 30,
				// 		"gps_time"           => "29-05-2022 12:12:48",
				// 		"isfatigue"          => "no",
				// 		"jalur_name"         => "kosongan",
				// 		"position"           => "EST ROAD",
				// 		"vehicle_company"    => "1839",
				// 		"vehicle_device"     => "869926046535932@VT200",
				// 		"vehicle_name"       => "Hino 500",
				// 		"vehicle_no"         => "STLI 348",
				// 		"violation"          => "Overspeed",
				// 		"violation_type"     => "overspeed",
				// 	)
				// );

				// echo "<pre>";
				// var_dump($dataoverspeed);die();
				// echo "<pre>";

				$totaldataoverspeed    = sizeof($dataoverspeed);
				$totaldataoverspeedfix = 0;
					if ($totaldataoverspeed < 10) {
						$totaldataoverspeedfix = "0".$totaldataoverspeed;
					}else {
						$totaldataoverspeedfix = $totaldataoverspeed;
					}

					// UNTUK UPDATE DATA ROM
					// $romStreet           = $this->m_poipoolmaster->getstreet_now2(5);
					// $dataRomFix          = array();
					// $dataPortFix         = array();
					// $dataPortCPBIBFix    = array();
					// $dataPortANTBIRFix   = array();

					//HITUNG DATA DIDALAM ROM
					// if ($totaldataoverspeed > 0) {
					// 	for ($j=0; $j < sizeof($romStreet); $j++) {
					// 		$street_name_rom                  = explode(",", $romStreet[$j]['street_name']);
					// 		$street_nameromfix                = $street_name_rom[0];
					// 		$dataStateRom[$street_nameromfix] = 0;
					//
					// 		for ($x=0; $x < sizeof($totaldataoverspeed); $x++) {
					// 			$positioninrom = $dataoverspeed[$x]['position'];
					//
					// 				if ($positioninrom == $street_nameromfix) {
					// 						$dataStateRom[$street_nameromfix] += 1;
					//
					// 						array_push($dataRomFix, array(
					// 							"isfatigue"          => "no",
					// 							"jalur_name"         => $dataoverspeed[$x]['jalur_name'],
					// 							"vehicle_no"         => $dataoverspeed[$x]['vehicle_no'],
					// 							"vehicle_name"       => $dataoverspeed[$x]['vehicle_name'],
					// 							"vehicle_company"    => $dataoverspeed[$x]['vehicle_company'],
					// 							"violation" 				 => $dataoverspeed[$x]['violation'],
					// 							"violation_type" 		 => $dataoverspeed[$x]['violation_type'],
					// 							"vehicle_device"     => $dataoverspeed[$x]['vehicle_device'],
					// 							"gps_latitude_real"  => $dataoverspeed[$x]['gps_latitude'],
					// 							"gps_longitude_real" => $dataoverspeed[$x]['gps_longitude'],
					// 							"gps_speed"          => $dataoverspeed[$x]['gps_speed'],
					// 							"gps_speed_limit"    => $dataoverspeed[$x]['gps_speed'],
					// 							"gps_time"           => $dataoverspeed[$x]['gps_time'],
					// 							"geofence"           => $dataoverspeed[$x]['geofence"'],
					// 							"position"           => $dataoverspeed[$x]['position"'],
					// 						));
					// 				}
					// 		}
					// 	}
					// }

			// echo "<pre>";
			// var_dump($dataoverspeed);die();
			// echo "<pre>";

			if ($simultantype == 0) {
				$sdate = date("Y-m-d H:i:s", strtotime("-3 minutes"));
			}else {
				// $sdate = $lasttime;
				// $sdate = $startdate;
				$nowtime          = date("Y-m-d H:i:s");
				$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
				$sdate            = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-3hours"));
			}

		$masterviolation   = $this->m_violation->getviolation("ts_violation", $sdate, $contractor, $alarmtypefromaster);

		// echo "<pre>";
		// var_dump($sdate);die();
		// echo "<pre>";
		$violation_call           = array();
		$violation_cardistance    = array();
		$violation_distracted     = array();
		$violation_fatigue        = array();
		$violation_smoking        = array();
		$violation_driverabnormal = array();

			if (sizeof($masterviolation) > 0) {
					for ($j=0; $j < sizeof($masterviolation); $j++) {
						if ($masterviolation[$j]['violation_fatigue'] != "") {
							$json_fatigue            = json_decode($masterviolation[$j]['violation_fatigue']);
							$forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
							$forcheck_gps_time       = $json_fatigue[0]->gps_time;
							$checkthis               = $this->m_violation->getfrommaster($forcheck_vehicledevice);
							$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
							// $jalurname               = $jsonautocheck->auto_last_road;
							$jalurname               = $masterviolation[$j]['violation_jalur'];

							$positionforfilter = $masterviolation[$j]['violation_position'];
								if ($positionforfilter != "") {
									// UNTUK UMUM START
									if (in_array($positionforfilter, $street_onduty)){

										$alarmreportnamefix = "";
										$alarmreporttype = $json_fatigue[0]->gps_alertid;
											if ($alarmreporttype == 626) {
												$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
											}elseif ($alarmreporttype == 627) {
												$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
											}else {
												$alarmreportnamefix = $json_fatigue[0]->gps_alert;
											}

										array_push($datafatigue, array(
											 "isfatigue"          => $json_fatigue[0]->isfatigue,
											 "jalur_name"         => $jalurname,
											 "vehicle_no"         => $json_fatigue[0]->vehicle_no,
											 "vehicle_name"       => $json_fatigue[0]->vehicle_name,
											 "vehicle_company"    => $json_fatigue[0]->vehicle_company,
											 "vehicle_device"     => $json_fatigue[0]->vehicle_device,
											 "vehicle_mv03"       => $json_fatigue[0]->vehicle_mv03,
											 "gps_alert"          => $alarmreportnamefix,
											 "violation" 				  => $alarmreportnamefix,
											 "violation_type" 		=> "not_overspeed",
											 "gps_time"           => $json_fatigue[0]->gps_time,
											 "auto_last_update"   => $jsonautocheck->auto_last_update,
											 "auto_last_check"    => $jsonautocheck->auto_last_check,
											 "gps_latitude_real"  => $json_fatigue[0]->gps_latitude_real,
											 "gps_longitude_real" => $json_fatigue[0]->gps_longitude_real,
											 "position"           => $masterviolation[$j]['violation_position'],
											 "auto_last_position" => $jsonautocheck->auto_last_position,
											 "gps_speed"          => $json_fatigue[0]->gps_speed,
										));
									}
									// UNTUK UMUM END
								}
					}
				}

				$lasttime     = $masterviolation[0]['violation_update'];
				// $lasttime     = date("Y-m-d H:i:s", strtotime($masterviolation[0]['violation_update']."-15 minutes"));
			}else {
				$lasttime = $sdate;
			}

			// alarmtypefromaster

			if ($violationmasterselect == 6) {
				$violationmix = array_merge($dataoverspeed);
			}elseif ($violationmasterselect == 0) {
				$violationmix = array_merge($dataoverspeed, $datafatigue);
			}else {
				$violationmix = array_merge($datafatigue);
			}

		// echo "<pre>";
		// var_dump($violationmasterselect);die();
		// echo "<pre>";

			if (sizeof($violationmix) > 0) {
				// ROM ROAD
				$dataJumlahInRomRoadMuatan['ROM_B1_ROAD'] = 0;
				$dataJumlahInRomRoadMuatan['ROM_B2_ROAD'] = 0;
				$dataJumlahInRomRoadMuatan['ROM_B3_ROAD'] = 0;
				$dataJumlahInRomRoadMuatan['EST_ROAD']    = 0;

				$dataJumlahInRomRoadKosongan['ROM_B1_ROAD'] = 0;
				$dataJumlahInRomRoadKosongan['ROM_B2_ROAD'] = 0;
				$dataJumlahInRomRoadKosongan['ROM_B3_ROAD'] = 0;
				$dataJumlahInRomRoadKosongan['EST_ROAD']    = 0;

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

				$masterdatavehicle = $violationmix; // SWITCH VARIABLE BIAR GAMPANG
				// $jalurarray = array();
				// echo "<pre>";
				// var_dump($masterdatavehicle);die();
				// echo "<pre>";

				for ($k=0; $k < sizeof($masterdatavehicle); $k++) {
					$datalastposition = $masterdatavehicle[$k]['position'];
					$jalur_name       = $masterdatavehicle[$k]['jalur_name'];

					// array_push($jalurarray, array(
					// 	"jalur"    => $datalastposition,
					// 	"position" => $jalur_name
					// ));

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
							}elseif ($datalastposition == "ROM B1 ROAD") {
								$dataJumlahInRomRoadKosongan['ROM_B1_ROAD'] += 1;
							}elseif ($datalastposition == "ROM B2 ROAD") {
								$dataJumlahInRomRoadKosongan['ROM_B2_ROAD'] += 1;
							}elseif ($datalastposition == "ROM B3 ROAD") {
								$dataJumlahInRomRoadKosongan['ROM_B3_ROAD'] += 1;
							}elseif ($datalastposition == "EST ROAD") {
								$dataJumlahInRomRoadKosongan['EST_ROAD'] += 1;
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
							}elseif ($datalastposition == "ROM B1 ROAD") {
								$dataJumlahInRomRoadMuatan['ROM_B1_ROAD'] += 1;
							}elseif ($datalastposition == "ROM B2 ROAD") {
								$dataJumlahInRomRoadMuatan['ROM_B2_ROAD'] += 1;
							}elseif ($datalastposition == "ROM B3 ROAD") {
								$dataJumlahInRomRoadMuatan['ROM_B3_ROAD'] += 1;
							}elseif ($datalastposition == "EST ROAD") {
								$dataJumlahInRomRoadMuatan['EST_ROAD'] += 1;
							}
						}
				}

				// echo "<pre>";
				// var_dump($dataJumlahInRomRoadKosongan);die();
				// echo "<pre>";

				// KHUSUS UNTUK SUMMARY START
				if (sizeof($datafatigue) > 0) {
					for ($x=0; $x < sizeof($datafatigue); $x++) {
						$violationalertnya = $datafatigue[$x]['gps_alert'];

						if (strpos($violationalertnya, "Call") !== false) {
							array_push($violation_call, array(
								 "isfatigue"           => $datafatigue[$x]['isfatigue'],
								 "jalur_name"          => $datafatigue[$x]['jalur_name'],
								 "vehicle_no"          => $datafatigue[$x]['vehicle_no'],
								 "vehicle_name"        => $datafatigue[$x]['vehicle_name'],
								 "vehicle_company"     => $datafatigue[$x]['vehicle_company'],
								 "vehicle_device"      => $datafatigue[$x]['vehicle_device'],
								 "vehicle_mv03"        => $datafatigue[$x]['vehicle_mv03'],
								 "gps_alert"           => $datafatigue[$x]['gps_alert'],
								 "violation" 				   => $datafatigue[$x]['violation'],
								 "violation_type" 		 => $datafatigue[$x]['violation_type'],
								 "gps_time"            => $datafatigue[$x]['gps_time'],
								 "auto_last_update"    => $datafatigue[$x]['auto_last_update'],
								 "auto_last_check"     => $datafatigue[$x]['auto_last_check'],
								 "gps_latitude_real"   => $datafatigue[$x]['gps_latitude_real'],
								 "gps_longitude_real"  => $datafatigue[$x]['gps_longitude_real'],
								 "position"            => $datafatigue[$x]['position'],
								 "auto_last_position"  => $datafatigue[$x]['auto_last_position'],
								 "gps_speed"           => $datafatigue[$x]['gps_speed'],
							));
						}elseif (strpos($violationalertnya, "Distance") !== false) {
							array_push($violation_cardistance, array(
								"isfatigue"          => $datafatigue[$x]['isfatigue'],
								"jalur_name"         => $datafatigue[$x]['jalur_name'],
								"vehicle_no"         => $datafatigue[$x]['vehicle_no'],
								"vehicle_name"       => $datafatigue[$x]['vehicle_name'],
								"vehicle_company"    => $datafatigue[$x]['vehicle_company'],
								"vehicle_device"     => $datafatigue[$x]['vehicle_device'],
								"vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
								"gps_alert"          => $datafatigue[$x]['gps_alert'],
								"violation" 				 => $datafatigue[$x]['violation'],
								"violation_type" 		 => $datafatigue[$x]['violation_type'],
								"gps_time"           => $datafatigue[$x]['gps_time'],
								"auto_last_update"   => $datafatigue[$x]['auto_last_update'],
								"auto_last_check"    => $datafatigue[$x]['auto_last_check'],
								"gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
								"gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
								"position"           => $datafatigue[$x]['position'],
								"auto_last_position" => $datafatigue[$x]['auto_last_position'],
								"gps_speed"          => $datafatigue[$x]['gps_speed'],
							));
						}elseif (strpos($violationalertnya, "Distracted") !== false) {
							array_push($violation_distracted, array(
								"isfatigue"          => $datafatigue[$x]['isfatigue'],
								"jalur_name"         => $datafatigue[$x]['jalur_name'],
								"vehicle_no"         => $datafatigue[$x]['vehicle_no'],
								"vehicle_name"       => $datafatigue[$x]['vehicle_name'],
								"vehicle_company"    => $datafatigue[$x]['vehicle_company'],
								"vehicle_device"     => $datafatigue[$x]['vehicle_device'],
								"vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
								"gps_alert"          => $datafatigue[$x]['gps_alert'],
								"violation" 				 => $datafatigue[$x]['violation'],
								"violation_type" 		 => $datafatigue[$x]['violation_type'],
								"gps_time"           => $datafatigue[$x]['gps_time'],
								"auto_last_update"   => $datafatigue[$x]['auto_last_update'],
								"auto_last_check"    => $datafatigue[$x]['auto_last_check'],
								"gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
								"gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
								"position"           => $datafatigue[$x]['position'],
								"auto_last_position" => $datafatigue[$x]['auto_last_position'],
								"gps_speed"          => $datafatigue[$x]['gps_speed'],
							));
						}elseif (strpos($violationalertnya, "Fatigue") !== false) {
							array_push($violation_fatigue, array(
								"isfatigue"          => $datafatigue[$x]['isfatigue'],
								"jalur_name"         => $datafatigue[$x]['jalur_name'],
								"vehicle_no"         => $datafatigue[$x]['vehicle_no'],
								"vehicle_name"       => $datafatigue[$x]['vehicle_name'],
								"vehicle_company"    => $datafatigue[$x]['vehicle_company'],
								"vehicle_device"     => $datafatigue[$x]['vehicle_device'],
								"vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
								"gps_alert"          => $datafatigue[$x]['gps_alert'],
								"violation" 				 => $datafatigue[$x]['violation'],
								"violation_type" 		 => $datafatigue[$x]['violation_type'],
								"gps_time"           => $datafatigue[$x]['gps_time'],
								"auto_last_update"   => $datafatigue[$x]['auto_last_update'],
								"auto_last_check"    => $datafatigue[$x]['auto_last_check'],
								"gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
								"gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
								"position"           => $datafatigue[$x]['position'],
								"auto_last_position" => $datafatigue[$x]['auto_last_position'],
								"gps_speed"          => $datafatigue[$x]['gps_speed'],
							));
						}elseif (strpos($violationalertnya, "Smoking") !== false) {
							array_push($violation_smoking, array(
								"isfatigue"          => $datafatigue[$x]['isfatigue'],
								"jalur_name"         => $datafatigue[$x]['jalur_name'],
								"vehicle_no"         => $datafatigue[$x]['vehicle_no'],
								"vehicle_name"       => $datafatigue[$x]['vehicle_name'],
								"vehicle_company"    => $datafatigue[$x]['vehicle_company'],
								"vehicle_device"     => $datafatigue[$x]['vehicle_device'],
								"vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
								"gps_alert"          => $datafatigue[$x]['gps_alert'],
								"violation" 				 => $datafatigue[$x]['violation'],
								"violation_type" 		 => $datafatigue[$x]['violation_type'],
								"gps_time"           => $datafatigue[$x]['gps_time'],
								"auto_last_update"   => $datafatigue[$x]['auto_last_update'],
								"auto_last_check"    => $datafatigue[$x]['auto_last_check'],
								"gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
								"gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
								"position"           => $datafatigue[$x]['position'],
								"auto_last_position" => $datafatigue[$x]['auto_last_position'],
								"gps_speed"          => $datafatigue[$x]['gps_speed'],
							));
						}elseif (strpos($violationalertnya, "Undetected") !== false) {
							array_push($violation_driverabnormal, array(
								"isfatigue"          => $datafatigue[$x]['isfatigue'],
								"jalur_name"         => $datafatigue[$x]['jalur_name'],
								"vehicle_no"         => $datafatigue[$x]['vehicle_no'],
								"vehicle_name"       => $datafatigue[$x]['vehicle_name'],
								"vehicle_company"    => $datafatigue[$x]['vehicle_company'],
								"vehicle_device"     => $datafatigue[$x]['vehicle_device'],
								"vehicle_mv03"       => $datafatigue[$x]['vehicle_mv03'],
								"gps_alert"          => $datafatigue[$x]['gps_alert'],
								"violation" 				 => $datafatigue[$x]['violation'],
								"violation_type" 		 => $datafatigue[$x]['violation_type'],
								"gps_time"           => $datafatigue[$x]['gps_time'],
								"auto_last_update"   => $datafatigue[$x]['auto_last_update'],
								"auto_last_check"    => $datafatigue[$x]['auto_last_check'],
								"gps_latitude_real"  => $datafatigue[$x]['gps_latitude_real'],
								"gps_longitude_real" => $datafatigue[$x]['gps_longitude_real'],
								"position"           => $datafatigue[$x]['position'],
								"auto_last_position" => $datafatigue[$x]['auto_last_position'],
								"gps_speed"          => $datafatigue[$x]['gps_speed'],
							));
						}
					}
				}

				// echo "<pre>";
				// var_dump("DONE");die();
				// echo "<pre>";

				$totalviolation_call           = sizeof($violation_call);
				$totalviolation_cardistance    = sizeof($violation_cardistance);
				$totalviolation_distracted     = sizeof($violation_distracted);
				$totalviolation_fatigue        = sizeof($violation_fatigue);
				$totalviolation_smoking        = sizeof($violation_smoking);
				$totalviolation_driverabnormal = sizeof($violation_driverabnormal);

				$tv_call = 0;
					if ($totalviolation_call < 10) {
						$tv_call = "0".$totalviolation_call;
					}else {
						$tv_call = $totalviolation_call;
					}
				$tv_cardistance = 0;
					if ($totalviolation_cardistance < 10) {
						$tv_cardistance = "0".$totalviolation_cardistance;
					}else {
						$tv_cardistance = $totalviolation_cardistance;
					}
				$tv_distracted = 0;
					if ($totalviolation_distracted < 10) {
						$tv_distracted = "0".$totalviolation_distracted;
					}else {
						$tv_distracted = $totalviolation_distracted;
					}
				$tv_fatigue = 0;
					if ($totalviolation_fatigue < 10) {
						$tv_fatigue = "0".$totalviolation_fatigue;
					}else {
						$tv_fatigue = $totalviolation_fatigue;
					}
				$tv_smoking = 0;
					if ($totalviolation_smoking < 10) {
						$tv_smoking = "0".$totalviolation_smoking;
					}else {
						$tv_smoking = $totalviolation_smoking;
					}
				$tv_driverabnormal = 0;
					if ($totalviolation_driverabnormal < 10) {
						$tv_driverabnormal = "0".$totalviolation_driverabnormal;
					}else {
						$tv_driverabnormal = $totalviolation_driverabnormal;
					}

					if ($violationmasterselect == 1) {
						$totaldataoverspeedfix = "00";
						$tv_call               = $tv_call;
						$tv_cardistance        = "00";
						$tv_distracted         = "00";
						$tv_fatigue            = "00";
						$tv_smoking            = "00";
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 2) {
						$totaldataoverspeedfix = "00";
						$tv_call               = "00";
						$tv_cardistance        = $tv_cardistance;
						$tv_distracted         = "00";
						$tv_fatigue            = "00";
						$tv_smoking            = "00";
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 3) {
						$totaldataoverspeedfix = "00";
						$tv_call               = "00";
						$tv_cardistance        = "00";
						$tv_distracted         = $tv_distracted;
						$tv_fatigue            = "00";
						$tv_smoking            = "00";
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 4) {
						$totaldataoverspeedfix = "00";
						$tv_call               = "00";
						$tv_cardistance        = "00";
						$tv_distracted         = "00";
						$tv_fatigue            = $tv_fatigue;
						$tv_smoking            = "00";
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 5) {
						$totaldataoverspeedfix = "00";
						$tv_call               = "00";
						$tv_cardistance        = "00";
						$tv_distracted         = "00";
						$tv_fatigue            = "00";
						$tv_smoking            = $tv_smoking;
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 6) {
						$totaldataoverspeedfix = $totaldataoverspeedfix;
						$tv_call               = "00";
						$tv_cardistance        = "00";
						$tv_distracted         = "00";
						$tv_fatigue            = "00";
						$tv_smoking            = "00";
						$tv_driverabnormal     = "00";
					}elseif ($violationmasterselect == 7) {
						$totaldataoverspeedfix = "00";
						$tv_call               = "00";
						$tv_cardistance        = "00";
						$tv_distracted         = "00";
						$tv_fatigue            = "00";
						$tv_smoking            = "00";
						$tv_driverabnormal     = $tv_driverabnormal;
					}else {
						$totaldataoverspeedfix = $totaldataoverspeedfix;
						$tv_call               = $tv_call;
						$tv_cardistance        = $tv_cardistance;
						$tv_distracted         = $tv_distracted;
						$tv_fatigue            = $tv_fatigue;
						$tv_smoking            = $tv_smoking;
						$tv_driverabnormal     = $tv_driverabnormal;
					}
				//KHUSUS UNTUK SUMMARY END


				echo json_encode(array(
					"msg"                      => "success",
					"code"                     => 200,
					// "lasttime"              => $lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour")),
					"lasttime"                 => $startdate,
					"simultantype"             => 1,
					"total_ov"			           => $totaldataoverspeedfix,
					"violationmix"             => $violationmix,
					"dataMuatan"               => $dataJumlahInKmMuatan,
					"dataKosongan"             => $dataJumlahInKmKosongan,
					"dataKosonganRomRoad"      => $dataJumlahInRomRoadKosongan,
					"dataMuatanRomRoad"        => $dataJumlahInRomRoadMuatan,
					"dataMuatan2"              => $dataJumlahInKmMuatan_2,
					"dataKosongan2"            => $dataJumlahInKmKosongan_2,
					"tv_call"                  => $tv_call,
					"tv_cardistance"           => $tv_cardistance,
					"tv_distracted"            => $tv_distracted,
					"tv_fatigue"               => $tv_fatigue,
					"tv_smoking"               => $tv_smoking,
					"tv_driverabnormal"        => $tv_driverabnormal,
					"violation_call"           => $violation_call,
					"violation_cardistance"    => $violation_cardistance,
					"violation_distracted"     => $violation_distracted,
					"violation_fatigue"        => $violation_fatigue,
					"violation_smoking"        => $violation_smoking,
					"violation_driverabnormal" => $violation_driverabnormal,
					// "dataRomFix"    => $dataRomFix
				));
			}else {
				echo json_encode(array(
					"msg"          => "failed",
					"code"         => 400,
					// "lasttime"  => $lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour")),
					"lasttime"     => $startdate,
					"simultantype" => 1,
					"violationmix" => $violationmix
				));
			}
  }

	function getlistinkm2(){
		$dataVehicleOnKosongan     = array();
		$dataVehicleOnMuatan       = array();
		$idkm 							       = $_POST['idkm'];
		$contractor 							 = $_POST['contractor'];
		$kmonsearch 					     = array();
		$dataoverspeed 				     = array();
		$datafatigue 					     = array();

		$nowtime          = date("Y-m-d H:i:s");
		$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
		$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-60second"));

		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id, user_dblive");
		$this->db->order_by("user_id","asc");
		$this->db->where("user_id", 4408);
		$q         = $this->db->get("user");
		$row       = $q->row();
		$total_row = count($row);

		$startdate = $last_fiveminutes;
		$enddate   = $nowtime_wita;

		$sdate     = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
		// $sdate     = date("2022-05-10 14:00:00"); //wita
		$edate     = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

		//print_r($sdate." ".$edate);exit();
		if(count($row)>0){
			$user_dblive = $row->user_dblive;
		}

		$this->dbalert = $this->load->database($user_dblive, TRUE);
		$this->dbalert->where("gps_time >=", $sdate);
				//$this->dbalert->where("gps_time <=", $edate);
		$this->dbalert->where("gps_speed >=", 11.3);  // >= 21 kph
		//$this->dbalert->where("gps_speed_status", 1);
		$this->dbalert->where("gps_alert", "Speeding Alarm");
		// $this->dbalert->where("gps_notif", 0); //belum ke send
		//$this->dbalert->limit(5); //limit
		$this->dbalert->order_by("gps_time","asc");
		// $this->dbalert->group_by(array("gps_time", "gps_name"));
		$this->dbalert->group_by(array("gps_name"));
		$q           = $this->dbalert->get("gps_alert");
		$rows        = $q->result();
		$total_alert = count($rows);

		if($total_alert >0){
			$j = 1;
			for ($i=0;$i<count($rows);$i++)
			{
				$title_name      = "OVERSPEED ALARM";
				$vehicle_device  = $rows[$i]->gps_name."@".$rows[$i]->gps_host;
				$data_vehicle    = $this->getvehicle2($vehicle_device, $contractor);
				if ($data_vehicle) {
					$vehicle_id      = $data_vehicle->vehicle_id;
					$vehicle_no      = $data_vehicle->vehicle_no;
					$vehicle_name    = $data_vehicle->vehicle_name;
					$vehicle_company = $data_vehicle->vehicle_company;
					$vehicle_dblive  = $data_vehicle->vehicle_dbname_live;

					$driver_name = "-";

					// printf("===Process Alarm ID %s %s %s (%d/%d) \r\n", $rows[$i]->gps_id, $data_vehicle->vehicle_no, $data_vehicle->vehicle_device, $j, $total_alert);
					$skip_sent = 0;
					$position = $this->getPosition_other($rows[$i]->gps_longitude_real,$rows[$i]->gps_latitude_real);

						if(isset($position)){
							$ex_position = explode(",",$position->display_name);
							if(count($ex_position)>0){
								$position_name = $ex_position[0];
							}else{
								$position_name = $ex_position[0];
							}
						}else{
							$position_name = $position->display_name;
								$skip_sent = 1;
						}

							//filter in location array HAULING, ROM, PORT
							$street_onduty = array("PORT BIB","PORT BIR","PORT TIA",
													//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
													"ROM A1","ROM B1","ROM B2","ROM B3","ROM EST",
													"ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
													//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
													//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",

													"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5",
													"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
													"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
													"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5","KM 31","KM 31",

													"BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
													"BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
													"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
													"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
													"PORT BIB - Antrian","Port BIB - Antrian"
												);


							if (in_array($position_name, $street_onduty)){
								$skip_sent = 0;
							}else{
								$skip_sent = 1;
							}

					$gps_time   = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time))); //sudah wita
					$coordinate = $rows[$i]->gps_latitude_real.",".$rows[$i]->gps_longitude_real;
					//$url = "http://maps.google.com/maps?z=12&t=m&q=loc:".$coordinate;
					$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
					//https://www.google.com/maps/search/?api=1&query=-6.2915399,106.9660776 : ex
					$gpsspeed_kph = round($rows[$i]->gps_speed*1.852,0);
					$direction    = $rows[$i]->gps_course;
					$jalur        = $this->get_jalurname_new($direction);

					if($jalur == ""){
						$jalur = $rows[$i]->gps_last_road_type;
					}

					$rowgeofence = $this->getGeofence_location_live($rows[$i]->gps_longitude_real, $rows[$i]->gps_latitude_real, $vehicle_dblive);

									if($rowgeofence == false){
										$geofence_id           = 0;
										$geofence_name         = "";
										$geofence_speed        = 0;
										$geofence_speed_muatan = "";
										$geofence_type         = "";
										$geofence_speed_limit  = 0;

									}else{
										$geofence_id           = $rowgeofence->geofence_id;
										$geofence_name         = $rowgeofence->geofence_name;
										$geofence_speed        = $rowgeofence->geofence_speed;
										$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
										$geofence_type         = $rowgeofence->geofence_type;

										if($jalur == "muatan"){
											$geofence_speed_limit = $geofence_speed_muatan;
										}else if($jalur == "kosongan"){
											$geofence_speed_limit = $geofence_speed;
										}else{
											$geofence_speed_limit = 0;

										}
									}

							if($gpsspeed_kph <= $geofence_speed_limit){
								$skip_sent = 1;
							}

							if($geofence_speed_limit == 0){
								$skip_sent = 1;
							}

							$gpsspeed_kph         = $gpsspeed_kph-3;
							$geofence_speed_limit = $geofence_speed_limit-3;

					// sleep(2);
					if($skip_sent == 0){
						array_push($dataoverspeed, array(
							"isfatigue"          => "no",
							"jalur_name"         => $jalur,
							"vehicle_no"         => $vehicle_no,
							"vehicle_name"       => $vehicle_name,
							"vehicle_device"     => $rows[$i]->gps_name.'@'.$rows[$i]->gps_host,
							"gps_latitude_real"  => $rows[$i]->gps_latitude_real,
							"gps_longitude_real" => $rows[$i]->gps_longitude_real,
							"gps_speed"          => $gpsspeed_kph,
							"gps_speed_limit"    => $geofence_speed_limit,
							// "gps_alert"          => $json_overspeed->gps_alert,
							"gps_time"           => $gps_time,
							"geofence"           => $geofence_name,
							"position"           => $position_name,
						));
					}
				}
			}
		}

			// echo "<pre>";
			// var_dump($dataoverspeed);die();
			// echo "<pre>";

		$masterdatavehicle         = $this->m_violation->getviolationbykm2("ts_violation", $contractor);

		if ($idkm == 1) {
			$kmonsearch = array("Port BIB - Antrian", "Port BIB - Kosongan 1", "Port BIB - Kosongan 2", "Port BIR - Antrian WB", "Port BIR - Kosongan 1", "Port BIR - Kosongan 2", "Simpang Bayah - Kosongan");
		}elseif ($idkm == 2) {
			$kmonsearch = array("KM 1", "KM 1.5", "KM 0.5");
		}elseif ($idkm == 3) {
			$kmonsearch = array("KM 2", "KM 2.5");
		}elseif ($idkm == 4) {
			$kmonsearch = array("KM 3", "KM 3.5");
		}elseif ($idkm == 5) {
			$kmonsearch = array("KM 4", "KM 4.5");
		}elseif ($idkm == 6) {
			$kmonsearch = array("KM 5", "KM 5.5");
		}elseif ($idkm == 7) {
			$kmonsearch = array("KM 6", "KM 6.5");
		}elseif ($idkm == 8) {
			$kmonsearch = array("KM 7", "KM 7.5");
		}elseif ($idkm == 9) {
			$kmonsearch = array("KM 8", "KM 8.5");
		}elseif ($idkm == 10) {
			$kmonsearch = array("KM 9", "KM 9.5");
		}elseif ($idkm == 11) {
			$kmonsearch = array("KM 10", "KM 10.5");
		}elseif ($idkm == 12) {
			$kmonsearch = array("KM 11", "KM 11.5");
		}elseif ($idkm == 13) {
			$kmonsearch = array("KM 12", "KM 12.5");
		}elseif ($idkm == 14) {
			$kmonsearch = array("KM 13", "KM 13.5");
		}elseif ($idkm == 15) {
			$kmonsearch = array("KM 14", "KM 14.5");
		}elseif ($idkm == 16) {
			$kmonsearch = array("KM 15", "KM 15.5");
		}elseif ($idkm == 17) {
			$kmonsearch = array("KM 16", "KM 16.5");
		}elseif ($idkm == 18) {
			$kmonsearch = array("KM 17", "KM 17.5");
		}elseif ($idkm == 19) {
			$kmonsearch = array("KM 18", "KM 18.5");
		}elseif ($idkm == 20) {
			$kmonsearch = array("KM 19", "KM 19.5");
		}elseif ($idkm == 21) {
			$kmonsearch = array("KM 20", "KM 20.5");
		}elseif ($idkm == 22) {
			$kmonsearch = array("KM 21", "KM 21.5");
		}elseif ($idkm == 23) {
			$kmonsearch = array("KM 22", "KM 22.5");
		}elseif ($idkm == 24) {
			$kmonsearch = array("KM 23", "KM 23.5");
		}elseif ($idkm == 25) {
			$kmonsearch = array("KM 24", "KM 24.5");
		}elseif ($idkm == 26) {
			$kmonsearch = array("KM 25", "KM 25.5");
		}elseif ($idkm == 27) {
			$kmonsearch = array("KM 26", "KM 26.5");
		}elseif ($idkm == 28) {
			$kmonsearch = array("KM 27", "KM 27.5");
		}elseif ($idkm == 29) {
			$kmonsearch = array("KM 28", "KM 28.5");
		}elseif ($idkm == 30) {
			$kmonsearch = array("KM 29", "KM 29.5");
		}

		$vCompanyFix 					 = "KM " . $idkm;

		if (sizeof($dataoverspeed) > 0) {
			for ($i=0; $i < sizeof($dataoverspeed); $i++) {
				$jalur_name            = $dataoverspeed[$i]['jalur_name'];
				$datalastposition      = $dataoverspeed[$i]['position'];

				if (in_array($datalastposition, $kmonsearch)) {
					if ($jalur_name == "kosongan") {
						array_push($dataVehicleOnKosongan, array(
							"vehicle_no"            => $dataoverspeed[$i]['vehicle_no'],
							"vehicle_name"          => $dataoverspeed[$i]['vehicle_name'],
							"violation"             => "Overspeed",
							"violation_type"        => "overspeed",
							// "auto_last_lat"      => $autocheck->auto_last_lat,
							// "auto_last_long"     => $autocheck->auto_last_long,
							"auto_last_positionfix" => $datalastposition,
							// "auto_last_engine"   => $autocheck->auto_last_engine,
							"auto_last_speed"       => $dataoverspeed[$i]['gps_speed'],
							// "auto_last_speed"       => (round($autocheck->gps_speed*1.853) - 3),
							"auto_speed_limit"      => $dataoverspeed[$i]['gps_speed_limit'],
							"auto_last_update"      => date("d-m-Y H:i:s", strtotime($dataoverspeed[$i]['gps_time']))
						));
					}else {
						array_push($dataVehicleOnMuatan, array(
							"vehicle_no"            => $dataoverspeed[$i]['vehicle_no'],
							"vehicle_name"          => $dataoverspeed[$i]['vehicle_name'],
							"violation"             => "Overspeed",
							"violation_type"        => "overspeed",
							// "auto_last_lat"      => $autocheck->auto_last_lat,
							// "auto_last_long"     => $autocheck->auto_last_long,
							"auto_last_positionfix" => $datalastposition,
							// "auto_last_engine"   => $autocheck->auto_last_engine,
							"auto_last_speed"       => $dataoverspeed[$i]['gps_speed'],
							// "auto_last_speed"       => (round($autocheck->gps_speed*1.853) - 3),
							"auto_speed_limit"      => $dataoverspeed[$i]['gps_speed_limit'],
							"auto_last_update"      => date("d-m-Y H:i:s", strtotime($dataoverspeed[$i]['gps_time']))
						));
					}
				}
			}
		}

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				// CHECK FATIGUE
				if ($masterdatavehicle[$i]['violation_fatigue'] != "") {
					$json_fatigue            = json_decode($masterdatavehicle[$i]['violation_fatigue']);
					$forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
					$forcheck_gps_time       = $json_fatigue[0]->gps_time;
					$datalastposition        = $masterdatavehicle[$i]['violation_position'];
					$checkthis               = $this->m_violation->getfrommaster($forcheck_vehicledevice);
					$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
					// $jalur_name           = $jsonautocheck->auto_last_road;
					$jalur_name              = $masterdatavehicle[$i]['violation_jalur'];

					// echo "<pre>";
					// var_dump($json_fatigue[0]);die();
					// echo "<pre>";

						if (in_array($datalastposition, $kmonsearch)) {
							if ($jalur_name == "kosongan") {
								array_push($dataVehicleOnKosongan, array(
									"vehicle_no"            => $masterdatavehicle[$i]['violation_vehicle_no'],
									"vehicle_name"          => $masterdatavehicle[$i]['violation_vehicle_name'],
									"violation"             => $json_fatigue[0]->gps_alert,
									"violation_type"        => $masterdatavehicle[$i]['violation_type'],
									// "auto_last_lat"      => $autocheck->auto_last_lat,
									// "auto_last_long"     => $autocheck->auto_last_long,
									"auto_last_positionfix" => $datalastposition,
									// "auto_last_engine"   => $autocheck->auto_last_engine,
									"auto_last_speed"       => $json_fatigue[0]->gps_speed,
									"auto_last_update"      => date("d-m-Y H:i:s", strtotime($json_fatigue[0]->gps_time))
								));
							}else {
								array_push($dataVehicleOnMuatan, array(
									"vehicle_no"            => $masterdatavehicle[$i]['violation_vehicle_no'],
									"vehicle_name"          => $masterdatavehicle[$i]['violation_vehicle_name'],
									"violation"             => $json_fatigue[0]->gps_alert,
									"violation_type"        => $masterdatavehicle[$i]['violation_type'],
									// "auto_last_lat"      => $autocheck->auto_last_lat,
									// "auto_last_long"     => $autocheck->auto_last_long,
									"auto_last_positionfix" => $datalastposition,
									// "auto_last_engine"   => $autocheck->auto_last_engine,
									"auto_last_speed"       => $json_fatigue[0]->gps_speed,
									"auto_last_update"      => date("d-m-Y H:i:s", strtotime($json_fatigue[0]->gps_time))
								));
							}
						}
					}
			}

		// echo "<pre>";
		// var_dump($dataVehicleOnKosongan);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "code" => 200, "dataKosongan" => $dataVehicleOnKosongan, "dataMuatan" => $dataVehicleOnMuatan, "kmsent" => $vCompanyFix));
	}

	function getGeofence_location_live($longitude, $latitude, $vehicle_dblive) {

		$this->db = $this->load->database($vehicle_dblive, true);
		$lng = $longitude;
		$lat = $latitude;
		$geo_name = "''";
		$sql = sprintf("
					SELECT 	geofence_name,geofence_id,geofence_speed,geofence_speed_muatan,geofence_type
					FROM 	webtracking_geofence
					WHERE 	TRUE
							AND (geofence_name <> %s)
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
							AND (geofence_status = 1)
					ORDER BY geofence_id DESC LIMIT 1 OFFSET 0", $geo_name, $lng, $lat);
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
			$row = $q->row();
						/*$total = $q->num_rows();
						for ($i=0;$i<$total;$i++){
				$data = $row[$i]->geofence_name;
				$data = $row;
				return $data;
						}*/
			$data = $row;
			return $data;

		}
		else
				{
			$data = false;
						return $data;
				}

	}

	function get_jalurname_new($direction){
		$arah = "";

		if($direction > 0 && $direction <= 180){ // arah ke kanan (muatan)
			$arah = "kanan";
			$jalur = "muatan";
		}else if($direction >= 181 && $direction <= 360){ // arah ke kiri (kosongan)
			$arah = "kiri";
			$jalur = "kosongan";
		}else{
			$arah = $direction;
			$jalur = "";
		}

		//printf("===Arah : %s \r\n", $arah);

		return $jalur;
	}

	function getPosition_other($longitude, $latitude)
	{
		//$api = $this->config->item('GOOGLE_MAP_API_KEY');
		$api = "AIzaSyCGr6BW7vPItrWq95DxMvL292Kf6jHNA5c"; //lacaktranslog prem
		//$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);
		$georeverse = $this->gpsmodel->getLocation_byGeoCode($latitude, $longitude, $api);

		return $georeverse;
	}

	function getvehicle($vehicle_device){
		$this->db = $this->load->database("default",true);
		$this->db->select("vehicle_id,vehicle_device,vehicle_type,vehicle_name,vehicle_no,vehicle_company,vehicle_dbname_live,vehicle_info");
		$this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $vehicle_device);
		$q = $this->db->get("vehicle");
		$rows = $q->row();
		$total_rows = count($rows);

		if($total_rows > 0){
			$data_vehicle = $rows;
			return $data_vehicle;
		}else{
			return false;
		}
	}

	function getvehicle2($vehicle_device, $contractor){
		$user_id         = $this->sess->user_id;
		$user_parent     = $this->sess->user_parent;
		$privilegecode   = $this->sess->user_id_role;
		$user_company    = $this->sess->user_company;

		$this->db = $this->load->database("default",true);
		$this->db->select("vehicle_id,vehicle_device,vehicle_type,vehicle_name,vehicle_no,vehicle_company,vehicle_dbname_live,vehicle_info,vehicle_mv03");
		$this->db->order_by("vehicle_id", "asc");

		if ($contractor == 0) {
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
				$this->db->where("vehicle_user_id", 4408);
			}
		}else {
			$this->db->where("vehicle_company", $contractor);
		}

		// if ($contractor != 0) {
		// 	$this->db->where("vehicle_company", $contractor);
		// }

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $vehicle_device);
		$q = $this->db->get("vehicle");
		$rows = $q->row();
		$total_rows = count($rows);

		if($total_rows > 0){
			$data_vehicle = $rows;
			return $data_vehicle;
		}else{
			return false;
		}
	}

	function violation_historikal(){
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

  	$companyid                   = $this->sess->user_company;
  	$user_dblive                 = $this->sess->user_dblive;
  	$mastervehicle               = $this->m_poipoolmaster->getmastervehicleforheatmap();
		$violationmaster             = $this->m_violation->getviolationmaster();

  	$datafix                     = array();
  	$deviceidygtidakada          = array();
  	$statusvehicle['engine_on']  = 0;
  	$statusvehicle['engine_off'] = 0;

  	for ($i=0; $i < sizeof($mastervehicle); $i++) {
  		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
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

		// GET ROM ROAD
		$romRoad                  = $this->m_poipoolmaster->getstreet_now2(5);
		$this->params['rom_road'] = $romRoad;

		// echo "<pre>";
  	// var_dump($romRoad);die();
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

  	$this->params['resultactive']    = $this->dashboardmodel->vehicleactive();
  	$this->params['resultexpired']   = $this->dashboardmodel->vehicleexpired();
  	$this->params['resulttotaldev']  = $this->dashboardmodel->totaldevice();
  	$this->params['mapsetting']      = $this->m_poipoolmaster->getmapsetting();
  	$this->params['poolmaster']      = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$this->params['violationmaster'] = $violationmaster;

  	// echo "<pre>";
  	// var_dump($this->params['violationmaster']);die();
  	// echo "<pre>";

  	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  		if ($privilegecode == 1) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  			// $this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_tablehistorikal', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_intervention', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  		}elseif ($privilegecode == 2) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_intervention', $this->params, true);
  			// $this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_tablehistorikal', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  		}elseif ($privilegecode == 3) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_intervention', $this->params, true);
  			// $this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_tablehistorikal', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  		}elseif ($privilegecode == 4) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_intervention', $this->params, true);
  			// $this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_tablehistorikal', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  		}elseif ($privilegecode == 5) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_intervention', $this->params, true);
  			// $this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_tablehistorikal', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  		}elseif ($privilegecode == 6) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_intervention', $this->params, true);
  			// $this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_tablehistorikal', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
  		}else {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/development/dashboard/v_dashboard_intervention', $this->params, true);
  			// $this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_tablehistorikal', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  		}
	}

	function intervention_postevent(){
		$limitnya	    								 = $_POST['limit_show_data'];
		$simultantype 								 = $_POST['simultantype'];
		$last_time_violation 					 = $_POST['last_time_violation'];
		$contractor 				           = $_POST['contractor'];
		$violationmasterselect 				 = $_POST['violationmaster'];
		$alarmtypefromaster            = array();
		$dataoverspeed 								 = array();
		$datafatigue                   = array();
		$dataKmMuatanFix               = array();
		$dataKmKosonganFix             = array();
		$violationmix                  = array();

		$black_list       = array("401","451","478","608","609","652","653","658","659");
		$street_onduty    = $this->config->item('street_register');
		// $street_onduty = $this->config->item("street_onduty_autocheck");

		if ($violationmasterselect == 6) {
			$alarmtypefromaster[] = 9999;
		}else {
			if ($violationmasterselect != "0") {
				$alarmbymaster = $this->m_violation->getalarmbytype($violationmasterselect);
				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
				}
			}
		}

		// echo "<pre>";
		// var_dump($alarmtypefromaster);die();
		// echo "<pre>";

		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id, user_dblive");
		$this->db->order_by("user_id","asc");
		$this->db->where("user_id", 4408);
		$q         = $this->db->get("user");
		$row       = $q->row();
		$total_row = count($row);

		$nowtime          = date("Y-m-d H:i:s");
		$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
		$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-10 Hours"));

		$startdate        = $last_fiveminutes;
		$enddate          = $nowtime_wita;
		$sdate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
		$edate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

		//print_r($sdate." ".$edate);exit();
		if(count($row)>0){
			$user_dblive = $row->user_dblive;
		}

		// CHOOSE DBTABLE
		$current_date      = date("Y-m-d H:i:s", strtotime("+1 Hour"));
		$m1                = date("F", strtotime($current_date));
		$year              = date("Y", strtotime($current_date));
		$dbtable           = "";
		$dbtable_overspeed = "";
		$report            = "alarm_evidence_";
		$report_overspeed  = "overspeed_hour_";

		switch ($m1)
		{
			case "January":
						$dbtable           = $report."januari_".$year;
						$dbtable_overspeed = $report_overspeed."januari_".$year;
			break;
			case "February":
						$dbtable = $report."februari_".$year;
						$dbtable_overspeed = $report_overspeed."februari_".$year;
			break;
			case "March":
						$dbtable = $report."maret_".$year;
						$dbtable_overspeed = $report_overspeed."maret_".$year;
			break;
			case "April":
						$dbtable = $report."april_".$year;
						$dbtable_overspeed = $report_overspeed."april_".$year;
			break;
			case "May":
						$dbtable = $report."mei_".$year;
						$dbtable_overspeed = $report_overspeed."mei_".$year;
			break;
			case "June":
						$dbtable = $report."juni_".$year;
						$dbtable_overspeed = $report_overspeed."juni_".$year;
			break;
			case "July":
						$dbtable = $report."juli_".$year;
						$dbtable_overspeed = $report_overspeed."juli_".$year;
			break;
			case "August":
						$dbtable = $report."agustus_".$year;
						$dbtable_overspeed = $report_overspeed."agustus_".$year;
			break;
			case "September":
						$dbtable = $report."september_".$year;
						$dbtable_overspeed = $report_overspeed."september_".$year;
			break;
			case "October":
						$dbtable = $report."oktober_".$year;
						$dbtable_overspeed = $report_overspeed."oktober_".$year;
			break;
			case "November":
						$dbtable = $report."november_".$year;
						$dbtable_overspeed = $report_overspeed."november_".$year;
			break;
			case "December":
						$dbtable = $report."desember_".$year;
						$dbtable_overspeed = $report_overspeed."desember_".$year;
			break;
		}

		$user_level      = $this->sess->user_level;
		$user_parent     = $this->sess->user_parent;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
		$privilegecode 	 = $this->sess->user_id_role;
		$user_id_fix     = $this->sess->user_id;

		if($privilegecode == 1){
			$contractor = $contractor;
		}else if($privilegecode == 2){
			$contractor = $contractor;
		}else if($privilegecode == 3){
			$contractor = $contractor;
		}else if($privilegecode == 4){
			$contractor = $contractor;
		}else if($privilegecode == 5){
			$contractor = $user_company;
		}else if($privilegecode == 6){
			$contractor = $user_company;
		}else if($privilegecode == 0){
			$contractor = $contractor;
		}else{
			$contractor = $contractor;
		}

		$data_array_alert = array();
		$limit            = ($limitnya/2);
		// $data_overspeed   = $this->m_violation->get_overspeed_intensor($dbtable_overspeed, $limit, $contractor);
		//
		// for ($i=0; $i < sizeof($data_overspeed); $i++) {
		// 	$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
		// 	array_push($data_array_alert, array(
		// 		"isfatigue"          => "no",
		// 		"jalur_name"         => $data_overspeed[$i]['overspeed_report_jalur'],
		// 		"vehicle_no"         => $data_overspeed[$i]['overspeed_report_vehicle_no'],
		// 		"vehicle_name"       => $data_overspeed[$i]['overspeed_report_vehicle_name'],
		// 		"vehicle_company"    => $data_overspeed[$i]['overspeed_report_vehicle_company'],
		// 		"vehicle_device"     => $data_overspeed[$i]['overspeed_report_vehicle_device'],
		// 		"vehicle_mv03"       => "",
		// 		"gps_alert" 				 => "Overspeed",
		// 		"violation" 				 => "Overspeed",
		// 		"violation_level" 	 => $data_overspeed[$i]['overspeed_report_level_alias'],
		// 		"violation_type" 		 => "overspeed",
		// 		"gps_latitude_real"  => $coordinate[0],
		// 		"gps_longitude_real" => $coordinate[1],
		// 		"gps_speed"          => $data_overspeed[$i]['overspeed_report_speed'],
		// 		"gps_speed_limit"    => $data_overspeed[$i]['overspeed_report_geofence_limit'],
		// 		"gps_time"           => date("Y-m-d H:i:s", strtotime($data_overspeed[$i]['overspeed_report_gps_time'])),
		// 		"geofence"           => $data_overspeed[$i]['overspeed_report_geofence_name'],
		// 		"position"           => $data_overspeed[$i]['overspeed_report_location'],
		// 	));
		// }

			// echo "<pre>";
			// var_dump($data_array_alert);die();
			// echo "<pre>";

				$nowtime      = date("Y-m-d H:i:s");
				$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
				// $sdate        = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-3 Minutes"));
				$sdate        = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-15 Hours"));
				$limit        = ($limitnya/2);

		// $masterviolation   = $this->m_violation->getviolationhistorikal($dbtable, $sdate, $nowtime_wita, $contractor, $alarmtypefromaster, $limit);
		// $masterviolation   = $this->m_violation->getviolationhistorikal_type2($dbtable, $limit, $contractor, $alarmtypefromaster);

		$this->dbtrip = $this->load->database("tensor_report", true);

		if ($contractor != "0") {
			$this->dbtrip->where("alarm_report_vehicle_company", $contractor);
		}

		$this->dbtrip->where("alarm_report_media", 0);
		$this->dbtrip->where("alarm_report_start_time >=", $sdate);

		$nowday            = date("d");
		$end_day_fromEdate = date("d", strtotime($edate));

		if ($nowday == $end_day_fromEdate) {
			$edate = date("Y-m-d H:i:s");
		}

		$this->dbtrip->where("alarm_report_start_time <=", $edate);

		if ($violationmasterselect != "0") {
			$this->dbtrip->where_in('alarm_report_type', $alarmtypefromaster);
		}

		$this->dbtrip->where_not_in('alarm_report_type', $black_list);
		$this->dbtrip->where("alarm_report_gpsstatus !=","");
		// $this->dbtrip->where("alarm_report_intervensi_sid !=","");
		$this->dbtrip->order_by("alarm_report_start_time","ASC");
		$this->dbtrip->group_by("alarm_report_start_time");
		$masterviolation = $this->dbtrip->get($dbtable)->result();

		// echo "<pre>";
		// var_dump($dbtable.'-'.$contractor.'-'.$privilegecode.'-'.$sdate.'-'.$edate);die();
		// echo "<pre>";

		// echo "<pre>";
		// var_dump($masterviolation);die();
		// echo "<pre>";

			if (sizeof($masterviolation) > 0) {
					for ($j=0; $j < sizeof($masterviolation); $j++) {
						// if ($masterviolation[$j]->alarm_report_intervensi_sid != "" || $masterviolation[$j]->alarm_report_intervensi_sid != null) {
						// 	echo "<pre>";
						// 	var_dump($masterviolation[$j]->alarm_report_intervensi_sid);die();
						// 	echo "<pre>";
						// }
							$vehicle_id              = $masterviolation[$j]->alarm_report_vehicle_id;
							$vehicle_type            = $masterviolation[$j]->alarm_report_vehicle_type;
							$forcheck_vehicledevice  = $vehicle_id.'@'.$vehicle_type;
							$forcheck_gps_time       = $masterviolation[$j]->alarm_report_start_time;
							$checkthis               = $this->m_violation->getfrommaster($forcheck_vehicledevice);
							// echo "<pre>";
							// var_dump($masterviolation[$j]);die();
							// echo "<pre>";
							$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
							// $jalurname            = $jsonautocheck->auto_last_road;
							$jalurname               = $masterviolation[$j]->alarm_report_jalur;

							$positionforfilter       = $masterviolation[$j]->alarm_report_location_start;



								if ($positionforfilter != "") {
									// UNTUK UMUM START
									// if (in_array($positionforfilter, $street_onduty)){
										$alarmreportnamefix = "";
										$alarmreporttype = $masterviolation[$j]->alarm_report_type;
											if ($alarmreporttype == 626) {
												$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
											}elseif ($alarmreporttype == 627) {
												$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
											}elseif ($alarmreporttype == 702) {
												$alarmreportnamefix = "Distracted Driving Alarm Level One Start";
											}elseif ($alarmreporttype == 703) {
												$alarmreportnamefix = "Distracted Driving Alarm Level Two Start";
											}elseif ($alarmreporttype == 752) {
												$alarmreportnamefix = "Distracted Driving Alarm Level One End";
											}elseif ($alarmreporttype == 753) {
												$alarmreportnamefix = "Distracted Driving Alarm Level Two End";
											}else {
												$alarmreportnamefix = $masterviolation[$j]->alarm_report_name;
											}

											if ($alarmreporttype != 624 && $alarmreporttype != 625) {
												// if (in_array($masterviolation[$j]['violation_position'], $street_onduty)) {
												if (strpos($alarmreportnamefix, "Level") !== FALSE ) {
													$violation_split = explode("Level", $alarmreportnamefix);
													$violationlevel  = str_replace("Start", "", $violation_split[1]);
													if ($violationlevel == "One") {
														$violationfix = "1";
													}else {
														$violationfix = "2";
													}
													$alarmfix = $violation_split[0];
												}else {
													$violationfix = "";
													$alarmfix = $alarmreportnamefix;
												}

													// echo "<pre>";
													// var_dump($masterviolation[$j]->alarm_report_intervensi_sid);die();
													// echo "<pre>";

													// if ($masterviolation[$j]->alarm_report_intervensi_sid != "") {
													// 	echo "<pre>";
													// 	var_dump($masterviolation[$j]->alarm_report_intervensi_sid);die();
													// 	echo "<pre>";
													// }

													array_push($data_array_alert, array(
														 "isfatigue"                           => "yes",
														 "jalur_name"                          => $jalurname,
														 "vehicle_no"                          => $masterviolation[$j]->alarm_report_vehicle_no,
														 "vehicle_name"                        => $masterviolation[$j]->alarm_report_vehicle_name,
														 "vehicle_company"                     => $masterviolation[$j]->alarm_report_vehicle_company,
														 "vehicle_id" 		                     => $masterviolation[$j]->alarm_report_vehicle_id,
														 "vehicle_mv03"                        => $masterviolation[$j]->alarm_report_imei,
														 "gps_alert"                           => $alarmreportnamefix,
														 "violation" 				                   => $alarmfix,
														 "violation_level" 				             => "Level " . $violationfix,
														 "violation_type" 		                 => "not_overspeed",
														 "gps_time"                            => $masterviolation[$j]->alarm_report_start_time,
														 "auto_last_update"                    => $jsonautocheck->auto_last_update,
														 "auto_last_check"                     => $jsonautocheck->auto_last_check,
														 "gps_latitude_real"                   => $jsonautocheck->auto_last_lat,
														 "gps_longitude_real"                  => $jsonautocheck->auto_last_long,
														 "position"                            => $masterviolation[$j]->alarm_report_location_start,
														 "auto_last_position"                  => $jsonautocheck->auto_last_position,
														 "gps_speed"                           => $masterviolation[$j]->alarm_report_speed,
														 "alarm_report_status_intervensi" 	   => $masterviolation[$j]->alarm_report_status_intervensi,
								             "alarm_report_intervensi_by_id" 	     => $masterviolation[$j]->alarm_report_intervensi_by_id,
								             "alarm_report_intervensi_by_name" 	   => $masterviolation[$j]->alarm_report_intervensi_by_name,
								             "alarm_report_intervensi_sid" 	       => $masterviolation[$j]->alarm_report_intervensi_sid,
								             "alarm_report_intervensi_datetime" 	 => $masterviolation[$j]->alarm_report_intervensi_datetime,
								             "alarm_report_intervensi_note" 	     => $masterviolation[$j]->alarm_report_intervensi_note,
								             "alarm_report_true_false" 	           => $masterviolation[$j]->alarm_report_true_false,
													));
													// echo "<pre>";
													// var_dump($data_array_alert);die();
													// echo "<pre>";
												// }
											}


									// }
									// UNTUK UMUM END
								}
				}

				$lasttime     = $masterviolation[0]->alarm_report_start_time;
				// $lasttime     = date("Y-m-d H:i:s", strtotime($masterviolation[0]['violation_update']."-15 minutes"));
			}else {
				$lasttime = $sdate;
			}

			 usort($data_array_alert, function($a, $b) {
			    return strtotime($b['gps_time']) - strtotime($a['gps_time']);
			});

			// echo "<pre>";
			// var_dump($data_array_alert);die();
			// echo "<pre>";

			// $violationmix = $this->aasort($data_array_alert, "gps_time");
			$data_array_fix = array();
			if($violationmasterselect == 6) {
				for ($i=0; $i < sizeof($data_array_alert); $i++) {
				$violation_type = $data_array_alert[$i]['violation_type'];
					if ($violation_type == "overspeed") {
						array_push($data_array_fix, array(
							"isfatigue"                        => $data_array_alert[$i]['isfatigue'],
							"jalur_name"                       => $data_array_alert[$i]['jalur_name'],
							"vehicle_no"                       => $data_array_alert[$i]['vehicle_no'],
							"vehicle_name"                     => $data_array_alert[$i]['vehicle_name'],
							"vehicle_company"                  => $data_array_alert[$i]['vehicle_company'],
							"vehicle_device"                   => $data_array_alert[$i]['vehicle_device'],
							"vehicle_mv03"                     => $data_array_alert[$i]['vehicle_mv03'],
							"gps_alert" 				               => $data_array_alert[$i]['gps_alert'],
							"violation" 				               => $data_array_alert[$i]['violation'],
							"violation_level" 				         => $data_array_alert[$i]['violation_level'],
							"violation_type" 		               => $data_array_alert[$i]['violation_type'],
							"gps_latitude_real"                => $data_array_alert[$i]['gps_latitude_real'],
							"gps_longitude_real"               => $data_array_alert[$i]['gps_longitude_real'],
							"gps_speed"                        => $data_array_alert[$i]['gps_speed'],
							"gps_speed_limit"                  => $data_array_alert[$i]['gps_speed_limit'],
							"gps_time"                         => $data_array_alert[$i]['gps_time'],
							"geofence"                         => $data_array_alert[$i]['geofence'],
							"position"                         => $data_array_alert[$i]['position'],
							"alarm_report_status_intervensi"   => "",
							"alarm_report_intervensi_by_id"    => "",
							"alarm_report_intervensi_by_name"  => "",
							"alarm_report_intervensi_sid"      => "",
							"alarm_report_intervensi_datetime" => "",
							"alarm_report_intervensi_note"     => "",
							"alarm_report_true_false"          => "",
						));
					}
				}
			}elseif($violationmasterselect != "0") {
				for ($i=0; $i < sizeof($data_array_alert); $i++) {
					$violation_type = $data_array_alert[$i]['violation_type'];
						if ($violation_type == "not_overspeed") {
							array_push($data_array_fix, array(
								"isfatigue"                        => $data_array_alert[$i]['isfatigue'],
								"jalur_name"                       => $data_array_alert[$i]['jalur_name'],
								"vehicle_no"                       => $data_array_alert[$i]['vehicle_no'],
								"vehicle_name"                     => $data_array_alert[$i]['vehicle_name'],
								"vehicle_company"                  => $data_array_alert[$i]['vehicle_company'],
								"vehicle_device"                   => "",
								"vehicle_mv03"                     => $data_array_alert[$i]['vehicle_mv03'],
								"gps_alert"                        => $data_array_alert[$i]['gps_alert'],
								"violation" 				               => $data_array_alert[$i]['violation'],
								"violation_level" 	               => $data_array_alert[$i]['violation_level'],
								"violation_type" 		               => $data_array_alert[$i]['violation_type'],
								"gps_time"                         => $data_array_alert[$i]['gps_time'],
								"auto_last_update"                 => $data_array_alert[$i]['auto_last_update'],
								"auto_last_check"                  => $data_array_alert[$i]['auto_last_check'],
								"gps_latitude_real"                => $data_array_alert[$i]['gps_latitude_real'],
								"gps_longitude_real"               => $data_array_alert[$i]['gps_longitude_real'],
								"position"                         => $data_array_alert[$i]['position'],
								"auto_last_position"               => $data_array_alert[$i]['auto_last_position'],
								"gps_speed"                        => $data_array_alert[$i]['gps_speed'],
								"alarm_report_status_intervensi"   => $data_array_alert[$i]['alarm_report_status_intervensi'],
								"alarm_report_intervensi_by_id"    => $data_array_alert[$i]['alarm_report_intervensi_by_id'],
								"alarm_report_intervensi_by_name"  => $data_array_alert[$i]['alarm_report_intervensi_by_name'],
								"alarm_report_intervensi_sid"      => $data_array_alert[$i]['alarm_report_intervensi_sid'],
								"alarm_report_intervensi_datetime" => $data_array_alert[$i]['alarm_report_intervensi_datetime'],
								"alarm_report_intervensi_note"     => $data_array_alert[$i]['alarm_report_intervensi_note'],
								"alarm_report_true_false"          => $data_array_alert[$i]['alarm_report_true_false'],
							));
						}
				}
			}else {
				$data_array_fix = $data_array_alert;
			}

			// echo "<pre>";
			// var_dump($data_array_alert);die();
			// echo "<pre>";

				echo json_encode(array(
					"msg"                      => "success",
					"code"                     => 200,
					// "lasttime"              => $lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour")),
					// "lasttime"                 => $startdate,
					"simultantype"             => 1,
					"violationmix"             => $data_array_fix,
					"alarmtypefromaster" 			 => sizeof($alarmtypefromaster)
					// "total_ov"			           => $totaldataoverspeedfix,
					// "tv_call"                  => $tv_call,
					// "tv_cardistance"           => $tv_cardistance,
					// "tv_distracted"            => $tv_distracted,
					// "tv_fatigue"               => $tv_fatigue,
					// "tv_smoking"               => $tv_smoking,
					// "tv_driverabnormal"        => $tv_driverabnormal,
					// "total_violationall"	     => $total_violationall,
					// "violation_call"           => $violation_call,
					// "violation_cardistance"    => $violation_cardistance,
					// "violation_distracted"     => $violation_distracted,
					// "violation_fatigue"        => $violation_fatigue,
					// "violation_smoking"        => $violation_smoking,
					// "violation_driverabnormal" => $violation_driverabnormal,
					// "dataRomFix"    => $dataRomFix
				));
  }

	function getdatalisthistorikalnew(){
		$limitnya	    								 = $_POST['limit_show_data'];
		$simultantype 								 = $_POST['simultantype'];
		$last_time_violation 					 = $_POST['last_time_violation'];
		$contractor 				           = $_POST['contractor'];
		$violationmasterselect 				 = $_POST['violationmaster'];
		$alarmtypefromaster            = array();
		$dataoverspeed 								 = array();
		$datafatigue                   = array();
		$dataKmMuatanFix               = array();
		$dataKmKosonganFix             = array();
		$violationmix                  = array();

		$street_onduty = $this->config->item("street_onduty_autocheck");

		// $street_onduty = array(
		// 						// "PORT BIB","PORT BIR","PORT TIA",
		// 						//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
		// 						// "ROM A1","ROM B1","ROM B2","ROM B3","ROM EST",
		// 						// "ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
		// 						"ROM B3 ROAD",
		// 						//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
		// 						//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",
		//
		// 						"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5",
		// 						"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
		// 						"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
		// 						"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5",
		//
		// 						// "BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
		// 						// "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
		// 						"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
		// 						"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
		// 						"PORT BIB - Antrian","Port BIB - Antrian"
		// 					);

		if ($violationmasterselect == 6) {
			$alarmtypefromaster[] = 9999;
		}else {
			if ($violationmasterselect != "0") {
				$alarmbymaster = $this->m_violation->getalarmbytype($violationmasterselect);
				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
				}
			}
		}

		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id, user_dblive");
		$this->db->order_by("user_id","asc");
		$this->db->where("user_id", 4408);
		$q         = $this->db->get("user");
		$row       = $q->row();
		$total_row = count($row);

		$nowtime          = date("Y-m-d H:i:s");
		$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
		$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-3Minutes"));

		$startdate        = $last_fiveminutes;
		$enddate          = $nowtime_wita;
		$sdate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
		$edate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

		//print_r($sdate." ".$edate);exit();
		if(count($row)>0){
			$user_dblive = $row->user_dblive;
		}

		// CHOOSE DBTABLE
		$current_date      = date("Y-m-d H:i:s", strtotime("+1 Hour"));
		$m1                = date("F", strtotime($current_date));
		$year              = date("Y", strtotime($current_date));
		$dbtable           = "";
		$dbtable_overspeed = "";
		$report            = "historikal_violation_";
		$report_overspeed  = "overspeed_hour_";

		switch ($m1)
		{
			case "January":
						$dbtable           = $report."januari_".$year;
						$dbtable_overspeed = $report_overspeed."januari_".$year;
			break;
			case "February":
						$dbtable = $report."februari_".$year;
						$dbtable_overspeed = $report_overspeed."februari_".$year;
			break;
			case "March":
						$dbtable = $report."maret_".$year;
						$dbtable_overspeed = $report_overspeed."maret_".$year;
			break;
			case "April":
						$dbtable = $report."april_".$year;
						$dbtable_overspeed = $report_overspeed."april_".$year;
			break;
			case "May":
						$dbtable = $report."mei_".$year;
						$dbtable_overspeed = $report_overspeed."mei_".$year;
			break;
			case "June":
						$dbtable = $report."juni_".$year;
						$dbtable_overspeed = $report_overspeed."juni_".$year;
			break;
			case "July":
						$dbtable = $report."juli_".$year;
						$dbtable_overspeed = $report_overspeed."juli_".$year;
			break;
			case "August":
						$dbtable = $report."agustus_".$year;
						$dbtable_overspeed = $report_overspeed."agustus_".$year;
			break;
			case "September":
						$dbtable = $report."september_".$year;
						$dbtable_overspeed = $report_overspeed."september_".$year;
			break;
			case "October":
						$dbtable = $report."oktober_".$year;
						$dbtable_overspeed = $report_overspeed."oktober_".$year;
			break;
			case "November":
						$dbtable = $report."november_".$year;
						$dbtable_overspeed = $report_overspeed."november_".$year;
			break;
			case "December":
						$dbtable = $report."desember_".$year;
						$dbtable_overspeed = $report_overspeed."desember_".$year;
			break;
		}

		$user_level      = $this->sess->user_level;
		$user_parent     = $this->sess->user_parent;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
		$privilegecode 	 = $this->sess->user_id_role;
		$user_id_fix     = $this->sess->user_id;

		if($privilegecode == 1){
			$contractor = $contractor;
		}else if($privilegecode == 2){
			$contractor = $contractor;
		}else if($privilegecode == 3){
			$contractor = $contractor;
		}else if($privilegecode == 4){
			$contractor = $contractor;
		}else if($privilegecode == 5){
			$contractor = $user_company;
		}else if($privilegecode == 6){
			$contractor = $user_company;
		}else if($privilegecode == 0){
			$contractor = $contractor;
		}else{
			$contractor = $contractor;
		}

		$data_array_alert = array();
		$limit            = ($limitnya/2);
		// $data_overspeed   = $this->m_violation->get_overspeed_intensor($dbtable_overspeed, $limit, $contractor);
		//
		// for ($i=0; $i < sizeof($data_overspeed); $i++) {
		// 	$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
		// 	array_push($data_array_alert, array(
		// 		"isfatigue"          => "no",
		// 		"jalur_name"         => $data_overspeed[$i]['overspeed_report_jalur'],
		// 		"vehicle_no"         => $data_overspeed[$i]['overspeed_report_vehicle_no'],
		// 		"vehicle_name"       => $data_overspeed[$i]['overspeed_report_vehicle_name'],
		// 		"vehicle_company"    => $data_overspeed[$i]['overspeed_report_vehicle_company'],
		// 		"vehicle_device"     => $data_overspeed[$i]['overspeed_report_vehicle_device'],
		// 		"vehicle_mv03"       => "",
		// 		"gps_alert" 				 => "Overspeed",
		// 		"violation" 				 => "Overspeed",
		// 		"violation_level" 	 => $data_overspeed[$i]['overspeed_report_level_alias'],
		// 		"violation_type" 		 => "overspeed",
		// 		"gps_latitude_real"  => $coordinate[0],
		// 		"gps_longitude_real" => $coordinate[1],
		// 		"gps_speed"          => $data_overspeed[$i]['overspeed_report_speed'],
		// 		"gps_speed_limit"    => $data_overspeed[$i]['overspeed_report_geofence_limit'],
		// 		"gps_time"           => date("Y-m-d H:i:s", strtotime($data_overspeed[$i]['overspeed_report_gps_time'])),
		// 		"geofence"           => $data_overspeed[$i]['overspeed_report_geofence_name'],
		// 		"position"           => $data_overspeed[$i]['overspeed_report_location'],
		// 	));
		// }

			// echo "<pre>";
			// var_dump($data_array_alert);die();
			// echo "<pre>";

				$nowtime      = date("Y-m-d H:i:s");
				$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
				$sdate        = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-3 Minutes"));
				$limit        = ($limitnya/2);

		// $masterviolation   = $this->m_violation->getviolationhistorikal($dbtable, $sdate, $nowtime_wita, $contractor, $alarmtypefromaster, $limit);
		$masterviolation   = $this->m_violation->getviolationhistorikal_type2($dbtable, $limit, $contractor, $alarmtypefromaster);

		// echo "<pre>";
		// var_dump($masterviolation);die();
		// echo "<pre>";

			if (sizeof($masterviolation) > 0) {
					for ($j=0; $j < sizeof($masterviolation); $j++) {
						if ($masterviolation[$j]['violation_fatigue'] != "") {
							$json_fatigue            = json_decode($masterviolation[$j]['violation_fatigue']);
							$forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
							$forcheck_gps_time       = $json_fatigue[0]->gps_time;
							$checkthis               = $this->m_violation->getfrommaster($forcheck_vehicledevice);
							$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
							// $jalurname               = $jsonautocheck->auto_last_road;
							$jalurname               = $masterviolation[$j]['violation_jalur'];

							$positionforfilter = $masterviolation[$j]['violation_position'];
								if ($positionforfilter != "") {
									// UNTUK UMUM START
									// if (in_array($positionforfilter, $street_onduty)){
										$alarmreportnamefix = "";
										$alarmreporttype = $json_fatigue[0]->gps_alertid;
											if ($alarmreporttype == 626) {
												$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
											}elseif ($alarmreporttype == 627) {
												$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
											}elseif ($alarmreporttype == 702) {
												$alarmreportnamefix = "Distracted Driving Alarm Level One Start";
											}elseif ($alarmreporttype == 703) {
												$alarmreportnamefix = "Distracted Driving Alarm Level Two Start";
											}elseif ($alarmreporttype == 752) {
												$alarmreportnamefix = "Distracted Driving Alarm Level One End";
											}elseif ($alarmreporttype == 753) {
												$alarmreportnamefix = "Distracted Driving Alarm Level Two End";
											}else {
												$alarmreportnamefix = $json_fatigue[0]->gps_alert;
											}

											if ($alarmreporttype != 624 && $alarmreporttype != 625) {
												// if (in_array($masterviolation[$j]['violation_position'], $street_onduty)) {
													$violation_split = explode("Level", $alarmreportnamefix);
													$violationlevel  = str_replace("Start", "", $violation_split[1]);
													if ($violationlevel == "One") {
														$violationfix = "1";
													}else {
														$violationfix = "2";
													}

													// echo "<pre>";
													// var_dump($violation_split);die();
													// echo "<pre>";

													array_push($data_array_alert, array(
														 "isfatigue"               => "yes",
														 "jalur_name"              => $jalurname,
														 "vehicle_no"              => $json_fatigue[0]->vehicle_no,
														 "vehicle_name"            => $json_fatigue[0]->vehicle_name,
														 "vehicle_company"         => $json_fatigue[0]->vehicle_company,
														 "vehicle_device"          => $json_fatigue[0]->vehicle_device,
														 "vehicle_mv03"            => $json_fatigue[0]->vehicle_mv03,
														 "gps_alert"               => $alarmreportnamefix,
														 "violation" 				       => $violation_split[0],
														 "violation_level" 				 => "Level " . $violationfix,
														 "violation_type" 		     => "not_overspeed",
														 "gps_time"                => $json_fatigue[0]->gps_time,
														 "auto_last_update"        => $jsonautocheck->auto_last_update,
														 "auto_last_check"         => $jsonautocheck->auto_last_check,
														 "gps_latitude_real"       => $json_fatigue[0]->gps_latitude_real,
														 "gps_longitude_real"      => $json_fatigue[0]->gps_longitude_real,
														 "position"                => $masterviolation[$j]['violation_position'],
														 "auto_last_position"      => $jsonautocheck->auto_last_position,
														 "gps_speed"               => $json_fatigue[0]->gps_speed,
													));
												// }
											}


									// }
									// UNTUK UMUM END
								}
					}
				}

				$lasttime     = $masterviolation[0]['violation_update'];
				// $lasttime     = date("Y-m-d H:i:s", strtotime($masterviolation[0]['violation_update']."-15 minutes"));
			}else {
				$lasttime = $sdate;
			}

			 usort($data_array_alert, function($a, $b) {
			    return strtotime($b['gps_time']) - strtotime($a['gps_time']);
			});

			// echo "<pre>";
			// var_dump($data_array_alert);die();
			// echo "<pre>";

			// $violationmix = $this->aasort($data_array_alert, "gps_time");
			$data_array_fix = array();
			if($violationmasterselect == 6) {
				for ($i=0; $i < sizeof($data_array_alert); $i++) {
				$violation_type = $data_array_alert[$i]['violation_type'];
					if ($violation_type == "overspeed") {
						array_push($data_array_fix, array(
							"isfatigue"                => $data_array_alert[$i]['isfatigue'],
							"jalur_name"               => $data_array_alert[$i]['jalur_name'],
							"vehicle_no"               => $data_array_alert[$i]['vehicle_no'],
							"vehicle_name"             => $data_array_alert[$i]['vehicle_name'],
							"vehicle_company"          => $data_array_alert[$i]['vehicle_company'],
							"vehicle_device"           => $data_array_alert[$i]['vehicle_device'],
							"vehicle_mv03"             => $data_array_alert[$i]['vehicle_mv03'],
							"gps_alert" 				       => $data_array_alert[$i]['gps_alert'],
							"violation" 				       => $data_array_alert[$i]['violation'],
							"violation_level" 				 => $data_array_alert[$i]['violation_level'],
							"violation_type" 		       => $data_array_alert[$i]['violation_type'],
							"gps_latitude_real"        => $data_array_alert[$i]['gps_latitude_real'],
							"gps_longitude_real"       => $data_array_alert[$i]['gps_longitude_real'],
							"gps_speed"                => $data_array_alert[$i]['gps_speed'],
							"gps_speed_limit"          => $data_array_alert[$i]['gps_speed_limit'],
							"gps_time"                 => $data_array_alert[$i]['gps_time'],
							"geofence"                 => $data_array_alert[$i]['geofence'],
							"position"                 => $data_array_alert[$i]['position'],
						));
					}
				}
			}elseif($violationmasterselect != "0") {
				for ($i=0; $i < sizeof($data_array_alert); $i++) {
					$violation_type = $data_array_alert[$i]['violation_type'];
						if ($violation_type == "not_overspeed") {
							array_push($data_array_fix, array(
								"isfatigue"          => $data_array_alert[$i]['isfatigue'],
								"jalur_name"         => $data_array_alert[$i]['jalur_name'],
								"vehicle_no"         => $data_array_alert[$i]['vehicle_no'],
								"vehicle_name"       => $data_array_alert[$i]['vehicle_name'],
								"vehicle_company"    => $data_array_alert[$i]['vehicle_company'],
								"vehicle_device"     => $data_array_alert[$i]['vehicle_device'],
								"vehicle_mv03"       => $data_array_alert[$i]['vehicle_mv03'],
								"gps_alert"          => $data_array_alert[$i]['gps_alert'],
								"violation" 				 => $data_array_alert[$i]['violation'],
								"violation_level" 	 => $data_array_alert[$i]['violation_level'],
								"violation_type" 		 => $data_array_alert[$i]['violation_type'],
								"gps_time"           => $data_array_alert[$i]['gps_time'],
								"auto_last_update"   => $data_array_alert[$i]['auto_last_update'],
								"auto_last_check"    => $data_array_alert[$i]['auto_last_check'],
								"gps_latitude_real"  => $data_array_alert[$i]['gps_latitude_real'],
								"gps_longitude_real" => $data_array_alert[$i]['gps_longitude_real'],
								"position"           => $data_array_alert[$i]['position'],
								"auto_last_position" => $data_array_alert[$i]['auto_last_position'],
								"gps_speed"          => $data_array_alert[$i]['gps_speed'],
							));
						}
				}
			}else {
				$data_array_fix = $data_array_alert;
			}

			// echo "<pre>";
			// var_dump($data_array_alert);die();
			// echo "<pre>";

				echo json_encode(array(
					"msg"                      => "success",
					"code"                     => 200,
					// "lasttime"              => $lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour")),
					// "lasttime"                 => $startdate,
					"simultantype"             => 1,
					"violationmix"             => $data_array_fix,
					"alarmtypefromaster" 			 => sizeof($alarmtypefromaster)
					// "total_ov"			           => $totaldataoverspeedfix,
					// "tv_call"                  => $tv_call,
					// "tv_cardistance"           => $tv_cardistance,
					// "tv_distracted"            => $tv_distracted,
					// "tv_fatigue"               => $tv_fatigue,
					// "tv_smoking"               => $tv_smoking,
					// "tv_driverabnormal"        => $tv_driverabnormal,
					// "total_violationall"	     => $total_violationall,
					// "violation_call"           => $violation_call,
					// "violation_cardistance"    => $violation_cardistance,
					// "violation_distracted"     => $violation_distracted,
					// "violation_fatigue"        => $violation_fatigue,
					// "violation_smoking"        => $violation_smoking,
					// "violation_driverabnormal" => $violation_driverabnormal,
					// "dataRomFix"    => $dataRomFix
				));
  }

	function getdatalisthistorikal_old(){
		// $limitnya	    								 = $_POST['limitnya'];
		$limitnya			 								 = 20;
		$simultantype 								 = $_POST['simultantype'];
		$last_time_violation 					 = $_POST['last_time_violation'];
		$contractor 				           = $_POST['contractor'];
		$violationmasterselect 				 = $_POST['violationmaster'];
		$alarmtypefromaster            = array();
		$dataoverspeed 								 = array();
		$datafatigue                   = array();
		$dataKmMuatanFix               = array();
		$dataKmKosonganFix             = array();
		$violationmix                  = array();

		$street_onduty = $this->config->item("street_onduty_autocheck");

		// $street_onduty = array(
		// 						// "PORT BIB","PORT BIR","PORT TIA",
		// 						//"ST1","ST2","ST3","ST4","ST5","ST6","ST7","ST8","ST9","ST10","ST11","ST12",
		// 						// "ROM A1","ROM B1","ROM B2","ROM B3","ROM EST",
		// 						// "ROM B1 ROAD","ROM B2 ROAD","EST ROAD","ROM 06 ROAD",
		// 						"ROM B3 ROAD",
		// 						//"POOL BBS","POOL BKA","POOL BSL","POOL GECL","POOL MKS","POOL RAM","POOL RBT","POOL STLI","POOL RBT BRD","POOL GECL 2",
		// 						//"WS GECL","WS KMB","WS MKS","WS RBT","WS MMS","WS EST","WS KMB INDUK","WS GECL 3","WS BRD","WS BEP","WS BBB",
		//
		// 						"KM 0","KM 0.5","KM 1","KM 1.5","KM 2","KM 2.5","KM 3","KM 3.5","KM 4","KM 4.5","KM 5","KM 5.5",
		// 						"KM 7.5","KM 8","KM 8.5","KM 9","KM 9.5","KM 10","KM 10.5","KM 11","KM 11.5","KM 12","KM 12.5","KM 13","KM 13.5","KM 14","KM 14.5","KM 15","KM 15.5","KM 16",
		// 						"KM 16.5","KM 17","KM 17.5","KM 18","KM 18.5","KM 19","KM 19.5","KM 20","KM 20.5","KM 21","KM 21.5","KM 22","KM 22.5","KM 23","KM 23.5","KM 24","KM 24.5","KM 25","KM 25.5","KM 26",
		// 						"KM 26.5","KM 27","KM 27.5","KM 28","KM 28.5","KM 29","KM 29.5","KM 30","KM 30.5",
		//
		// 						// "BIB CP 1","BIB CP 2","BIB CP 3","BIB CP 4","BIB CP 5","BIB CP 6","BIB CP 7",
		// 						// "BIR Ant.S LS","BIR Ant.S LS 2","BIR LS","BIR Ant BLC","BIR Ant BLC 2",
		// 						"Port BIR - Kosongan 1","Port BIR - Kosongan 2","Simpang Bayah - Kosongan",
		// 						"Port BIB - Kosongan 2","Port BIB - Kosongan 1","Port BIR - Antrian WB",
		// 						"PORT BIB - Antrian","Port BIB - Antrian"
		// 					);

		if ($violationmasterselect == 6) {
			$alarmtypefromaster[] = 9999;
		}else {
			if ($violationmasterselect != "0") {
				$alarmbymaster = $this->m_violation->getalarmbytype($violationmasterselect);
				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
				}
			}
		}

		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id, user_dblive");
		$this->db->order_by("user_id","asc");
		$this->db->where("user_id", 4408);
		$q         = $this->db->get("user");
		$row       = $q->row();
		$total_row = count($row);

		$nowtime          = date("Y-m-d H:i:s");
		$nowtime_wita     = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
		$last_fiveminutes = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-3Minutes"));

		$startdate        = $last_fiveminutes;
		$enddate          = $nowtime_wita;
		$sdate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate))); //wita
		$edate            = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate))); //wita

		//print_r($sdate." ".$edate);exit();
		if(count($row)>0){
			$user_dblive = $row->user_dblive;
		}

		$data_all_alert = array();

		// if ($simultantype == 0) {
		// 	$limit = 20;
		// }else {
			$limit        = ($limitnya/2);
		// }
		$data_gps_overspeed = array();
			for ($i=1; $i < 6; $i++) {
				$this->dbalert = $this->load->database("webtracking_gps_temanindobara_live_".$i, TRUE);
				// $this->dbalert->where("gps_time >=", $sdate);
						//$this->dbalert->where("gps_time <=", $edate);
				$this->dbalert->where("gps_speed >=", 11.3);  // >= 21 kph
				//$this->dbalert->where("gps_speed_status", 1);
				$this->dbalert->where("gps_alert", "Speeding Alarm");
				// $this->dbalert->where("gps_notif", 0); //belum ke send
				$this->dbalert->limit($limit); //limit
				$this->dbalert->order_by("gps_time","desc");
				$this->dbalert->group_by(array("gps_name"));
				$q           = $this->dbalert->get("gps_alert");
				$rows        = $q->result();
					if (sizeof($rows) > 0) {
						for ($j=0; $j < sizeof($rows); $j++) {
							array_push($data_gps_overspeed, array(
								"gps_id"             => $rows[$j]->gps_id,
					      "gps_name"           => $rows[$j]->gps_name,
					      "gps_host"           => $rows[$j]->gps_host,
					      "gps_type"           => $rows[$j]->gps_type,
					      "gps_utc_coord"      => $rows[$j]->gps_utc_coord,
					      "gps_status"         => $rows[$j]->gps_status,
					      "gps_latitude"       => $rows[$j]->gps_latitude,
					      "gps_ns"             => $rows[$j]->gps_ns,
					      "gps_longitude"      => $rows[$j]->gps_longitude,
					      "gps_ew"             => $rows[$j]->gps_ew,
					      "gps_speed"          => $rows[$j]->gps_speed,
					      "gps_course"         => $rows[$j]->gps_course,
					      "gps_utc_date"       => $rows[$j]->gps_utc_date,
					      "gps_mvd"            => $rows[$j]->gps_mvd,
					      "gps_mv"             => $rows[$j]->gps_mv,
					      "gps_cs"             => $rows[$j]->gps_cs,
					      "gps_msg_ori"        => $rows[$j]->gps_msg_ori,
					      "gps_time"           => $rows[$j]->gps_time,
					      "gps_latitude_real"  => $rows[$j]->gps_latitude_real,
					      "gps_longitude_real" => $rows[$j]->gps_longitude_real,
					      "gps_odometer"       => $rows[$j]->gps_odometer,
					      "gps_workhour"       => $rows[$j]->gps_workhour,
					      "gps_geofence"       => $rows[$j]->gps_geofence,
					      "gps_last_road_type" => $rows[$j]->gps_last_road_type,
					      "gps_speed_limit"    => $rows[$j]->gps_speed_limit,
					      "gps_speed_status"   => $rows[$j]->gps_speed_status,
					      "gps_notif"          => $rows[$j]->gps_notif,
					      "gps_alert"          => $rows[$j]->gps_alert,
					      "gps_view"           => $rows[$j]->gps_view,
					      "gps_inserttime"     => $rows[$j]->gps_inserttime,
							));
						}
					}
			}

			$rows        = $data_gps_overspeed;
			$total_alert = count($rows);

			// echo "<pre>";
			// var_dump($data_all_alert);die();
			// echo "<pre>";

			$user_level      = $this->sess->user_level;
	    $user_parent     = $this->sess->user_parent;
			$user_company    = $this->sess->user_company;
			$user_subcompany = $this->sess->user_subcompany;
			$user_group      = $this->sess->user_group;
			$user_subgroup   = $this->sess->user_subgroup;
			$user_dblive 	   = $this->sess->user_dblive;
	    $privilegecode 	 = $this->sess->user_id_role;
			$user_id_fix     = $this->sess->user_id;

			if($privilegecode == 1){
				$contractor = $contractor;
			}else if($privilegecode == 2){
				$contractor = $contractor;
			}else if($privilegecode == 3){
				$contractor = $contractor;
			}else if($privilegecode == 4){
				$contractor = $contractor;
			}else if($privilegecode == 5){
				$contractor = $user_company;
			}else if($privilegecode == 6){
				$contractor = $user_company;
			}else if($privilegecode == 0){
				$contractor = $contractor;
			}else{
				$contractor = $contractor;
			}

			if($total_alert >0){
				$j = 1;
				$data_array_alert = array();
				for ($i=0;$i<count($rows);$i++){
					$title_name      = "OVERSPEED ALARM";
					$vehicle_device  = $rows[$i]['gps_name']."@".$rows[$i]['gps_host'];
					$data_vehicle    = $this->getvehicle2($vehicle_device, $contractor);

						if ($data_vehicle) {
							// echo "<pre>";
							// var_dump($data_vehicle);die();
							// echo "<pre>";

							$vehicle_id      = $data_vehicle->vehicle_id;
							$vehicle_no      = $data_vehicle->vehicle_no;
							$vehicle_name    = $data_vehicle->vehicle_name;
							$vehicle_company = $data_vehicle->vehicle_company;
							$vehicle_dblive  = $data_vehicle->vehicle_dbname_live;
							$vehicle_mv03    = $data_vehicle->vehicle_mv03;

							$driver_name = "-";

							// printf("===Process Alarm ID %s %s %s (%d/%d) \r\n", $rows[$i]->gps_id, $data_vehicle->vehicle_no, $data_vehicle->vehicle_device, $j, $total_alert);
							$skip_sent = 0;
							$position = $this->getPosition_other($rows[$i]['gps_longitude_real'],$rows[$i]['gps_latitude_real']);

								if(isset($position)){
									$ex_position = explode(",",$position->display_name);
									if(count($ex_position)>0){
										$position_name = $ex_position[0];
									}else{
										$position_name = $ex_position[0];
									}
								}else{
									$position_name = $position->display_name;
										$skip_sent = 1;
								}

									//filter in location array HAULING, ROM, PORT

									if (in_array($position_name, $street_onduty)){
										$skip_sent = 0;
									}else{
										$skip_sent = 1;
									}

							$gps_time   = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]['gps_time']))); //sudah wita
							$coordinate = $rows[$i]['gps_latitude_real'].",".$rows[$i]['gps_longitude_real'];
							//$url = "http://maps.google.com/maps?z=12&t=m&q=loc:".$coordinate;
							$url = "https://www.google.com/maps/search/?api=1&query=".$coordinate;
							//https://www.google.com/maps/search/?api=1&query=-6.2915399,106.9660776 : ex
							$gpsspeed_kph = round($rows[$i]['gps_speed']*1.852,0);
							$direction    = $rows[$i]['gps_course'];
							$jalur        = $this->get_jalurname_new($direction);

							if($jalur == ""){
								$jalur = $rows[$i]['gps_last_road_type'];
							}

							$rowgeofence = $this->getGeofence_location_live($rows[$i]['gps_longitude_real'], $rows[$i]['gps_latitude_real'], $vehicle_dblive);

											if($rowgeofence == false){
												$geofence_id           = 0;
												$geofence_name         = "";
												$geofence_speed        = 0;
												$geofence_speed_muatan = "";
												$geofence_type         = "";
												$geofence_speed_limit  = 0;

											}else{
												$geofence_id           = $rowgeofence->geofence_id;
												$geofence_name         = $rowgeofence->geofence_name;
												$geofence_speed        = $rowgeofence->geofence_speed;
												$geofence_speed_muatan = $rowgeofence->geofence_speed_muatan;
												$geofence_type         = $rowgeofence->geofence_type;

												if($jalur == "muatan"){
													$geofence_speed_limit = $geofence_speed_muatan;
												}else if($jalur == "kosongan"){
													$geofence_speed_limit = $geofence_speed;
												}else{
													$geofence_speed_limit = 0;

												}
											}
									// printf("===Position : %s Geofence : %s Jalur: %s \r\n", $position_name, $geofence_name, $jalur);
									// printf("===Speed : %s Limit : %s \r\n", $gpsspeed_kph, $geofence_speed_limit);

									if($gpsspeed_kph <= $geofence_speed_limit){
										$skip_sent = 1;
									}

									if($geofence_speed_limit == 0){
										$skip_sent = 1;
									}

									$gpsspeed_kph         = $gpsspeed_kph-3;
									$geofence_speed_limit = $geofence_speed_limit-3;

							if($skip_sent == 0){
								array_push($data_array_alert, array(
									"isfatigue"          => "no",
									"jalur_name"         => $jalur,
									"vehicle_no"         => $vehicle_no,
									"vehicle_name"       => $vehicle_name,
									"vehicle_company"    => $vehicle_company,
									"vehicle_device"     => $rows[$i]['gps_name'].'@'.$rows[$i]['gps_host'],
									"vehicle_mv03"       => $vehicle_mv03,
									"gps_alert" 				 => "Overspeed",
									"violation" 				 => "Overspeed",
									"violation_type" 		 => "overspeed",
									"gps_latitude_real"  => $rows[$i]['gps_latitude_real'],
									"gps_longitude_real" => $rows[$i]['gps_longitude_real'],
									"gps_speed"          => $gpsspeed_kph,
									"gps_speed_limit"    => $geofence_speed_limit,
									"gps_time"           => date("Y-m-d H:i:s", strtotime($gps_time)),
									"geofence"           => $geofence_name,
									"position"           => $position_name,
								));
							}
						}
						}
				}

				// echo "<pre>";
				// var_dump($data_array_alert);die();
				// echo "<pre>";

				// CHOOSE DBTABLE
				$current_date = date("Y-m-d H:i:s", strtotime("+1 Hour"));
				$m1           = date("F", strtotime($current_date));
				$year         = date("Y", strtotime($current_date));
				$dbtable      = "";
				$report       = "historikal_violation_";

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

				// echo "<pre>";
				// var_dump($dbtable);die();
				// echo "<pre>";

			// if ($simultantype == 0) {
			// 	$sdate = date("Y-m-d H:i:s", strtotime("-5 minutes"));
			// 	$limit = 20;
			// }else {
				// $sdate = $lasttime;
				// $sdate = $startdate;
				$nowtime      = date("Y-m-d H:i:s");
				$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));
				$sdate        = date("Y-m-d H:i:s", strtotime($nowtime_wita . "-3 Minutes"));
				$limit        = ($limitnya/2);
			// }

			// echo "<pre>";
			// var_dump($alarmtypefromaster);die();
			// echo "<pre>";

		// $masterviolation   = $this->m_violation->getviolationhistorikal($dbtable, $sdate, $nowtime_wita, $contractor, $alarmtypefromaster, $limit);
		$masterviolation   = $this->m_violation->getviolationhistorikal_type2($dbtable, $limit, $contractor, $alarmtypefromaster);

		$violation_call           = array();
		$violation_cardistance    = array();
		$violation_distracted     = array();
		$violation_fatigue        = array();
		$violation_smoking        = array();
		$violation_driverabnormal = array();

			if (sizeof($masterviolation) > 0) {
					for ($j=0; $j < sizeof($masterviolation); $j++) {
						if ($masterviolation[$j]['violation_fatigue'] != "") {
							$json_fatigue            = json_decode($masterviolation[$j]['violation_fatigue']);
							$forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
							$forcheck_gps_time       = $json_fatigue[0]->gps_time;
							$checkthis               = $this->m_violation->getfrommaster($forcheck_vehicledevice);
							$jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
							// $jalurname               = $jsonautocheck->auto_last_road;
							$jalurname               = $masterviolation[$j]['violation_jalur'];

							$positionforfilter = $masterviolation[$j]['violation_position'];
								if ($positionforfilter != "") {
									// UNTUK UMUM START
									if (in_array($positionforfilter, $street_onduty)){
										$alarmreportnamefix = "";
										$alarmreporttype = $json_fatigue[0]->gps_alertid;
											if ($alarmreporttype == 626) {
												$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
											}elseif ($alarmreporttype == 627) {
												$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
											}else {
												$alarmreportnamefix = $json_fatigue[0]->gps_alert;
											}

											if (in_array($masterviolation[$j]['violation_position'], $street_onduty)) {
												array_push($data_array_alert, array(
													 "isfatigue"          => "yes",
													 "jalur_name"         => $jalurname,
													 "vehicle_no"         => $json_fatigue[0]->vehicle_no,
													 "vehicle_name"       => $json_fatigue[0]->vehicle_name,
													 "vehicle_company"    => $json_fatigue[0]->vehicle_company,
													 "vehicle_device"     => $json_fatigue[0]->vehicle_device,
													 "vehicle_mv03"       => $json_fatigue[0]->vehicle_mv03,
													 "gps_alert"          => $alarmreportnamefix,
													 "violation" 				  => $alarmreportnamefix,
													 "violation_type" 		=> "not_overspeed",
													 "gps_time"           => $json_fatigue[0]->gps_time,
													 "auto_last_update"   => $jsonautocheck->auto_last_update,
													 "auto_last_check"    => $jsonautocheck->auto_last_check,
													 "gps_latitude_real"  => $json_fatigue[0]->gps_latitude_real,
													 "gps_longitude_real" => $json_fatigue[0]->gps_longitude_real,
													 "position"           => $masterviolation[$j]['violation_position'],
													 "auto_last_position" => $jsonautocheck->auto_last_position,
													 "gps_speed"          => $json_fatigue[0]->gps_speed,
												));
											}
									}
									// UNTUK UMUM END
								}
					}
				}

				$lasttime     = $masterviolation[0]['violation_update'];
				// $lasttime     = date("Y-m-d H:i:s", strtotime($masterviolation[0]['violation_update']."-15 minutes"));
			}else {
				$lasttime = $sdate;
			}

			 usort($data_array_alert, function($a, $b) {
			    return strtotime($b['gps_time']) - strtotime($a['gps_time']);
			});

			// $violationmix = $this->aasort($data_array_alert, "gps_time");
			$data_array_fix = array();
			if($violationmasterselect == 6) {
				for ($i=0; $i < sizeof($data_array_alert); $i++) {
				$violation_type = $data_array_alert[$i]['violation_type'];
					if ($violation_type == "overspeed") {
						array_push($data_array_fix, array(
							"isfatigue"          => $data_array_alert[$i]['isfatigue'],
							"jalur_name"         => $data_array_alert[$i]['jalur_name'],
							"vehicle_no"         => $data_array_alert[$i]['vehicle_no'],
							"vehicle_name"       => $data_array_alert[$i]['vehicle_name'],
							"vehicle_company"    => $data_array_alert[$i]['vehicle_company'],
							"vehicle_device"     => $data_array_alert[$i]['vehicle_device'],
							"vehicle_mv03"       => $data_array_alert[$i]['vehicle_mv03'],
							"gps_alert" 				 => $data_array_alert[$i]['gps_alert'],
							"violation" 				 => $data_array_alert[$i]['violation'],
							"violation_type" 		 => $data_array_alert[$i]['violation_type'],
							"gps_latitude_real"  => $data_array_alert[$i]['gps_latitude_real'],
							"gps_longitude_real" => $data_array_alert[$i]['gps_longitude_real'],
							"gps_speed"          => $data_array_alert[$i]['gps_speed'],
							"gps_speed_limit"    => $data_array_alert[$i]['gps_speed_limit'],
							"gps_time"           => $data_array_alert[$i]['gps_time'],
							"geofence"           => $data_array_alert[$i]['geofence'],
							"position"           => $data_array_alert[$i]['position'],
						));
					}
				}
			}elseif($violationmasterselect != "0") {
				for ($i=0; $i < sizeof($data_array_alert); $i++) {
					$violation_type = $data_array_alert[$i]['violation_type'];
						if ($violation_type == "not_overspeed") {
							array_push($data_array_fix, array(
								"isfatigue"          => $data_array_alert[$i]['isfatigue'],
								"jalur_name"         => $data_array_alert[$i]['jalur_name'],
								"vehicle_no"         => $data_array_alert[$i]['vehicle_no'],
								"vehicle_name"       => $data_array_alert[$i]['vehicle_name'],
								"vehicle_company"    => $data_array_alert[$i]['vehicle_company'],
								"vehicle_device"     => $data_array_alert[$i]['vehicle_device'],
								"vehicle_mv03"       => $data_array_alert[$i]['vehicle_mv03'],
								"gps_alert"          => $data_array_alert[$i]['gps_alert'],
								"violation" 				 => $data_array_alert[$i]['violation'],
								"violation_type" 		 => $data_array_alert[$i]['violation_type'],
								"gps_time"           => $data_array_alert[$i]['gps_time'],
								"auto_last_update"   => $data_array_alert[$i]['auto_last_update'],
								"auto_last_check"    => $data_array_alert[$i]['auto_last_check'],
								"gps_latitude_real"  => $data_array_alert[$i]['gps_latitude_real'],
								"gps_longitude_real" => $data_array_alert[$i]['gps_longitude_real'],
								"position"           => $data_array_alert[$i]['position'],
								"auto_last_position" => $data_array_alert[$i]['auto_last_position'],
								"gps_speed"          => $data_array_alert[$i]['gps_speed'],
							));
						}
				}
			}else {
				$data_array_fix = $data_array_alert;
			}

			// echo "<pre>";
			// var_dump($data_array_alert);die();
			// echo "<pre>";

				echo json_encode(array(
					"msg"                      => "success",
					"code"                     => 200,
					// "lasttime"              => $lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour")),
					// "lasttime"                 => $startdate,
					"simultantype"             => 1,
					"violationmix"             => $data_array_fix,
					"alarmtypefromaster" 			 => sizeof($alarmtypefromaster)
					// "total_ov"			           => $totaldataoverspeedfix,
					// "tv_call"                  => $tv_call,
					// "tv_cardistance"           => $tv_cardistance,
					// "tv_distracted"            => $tv_distracted,
					// "tv_fatigue"               => $tv_fatigue,
					// "tv_smoking"               => $tv_smoking,
					// "tv_driverabnormal"        => $tv_driverabnormal,
					// "total_violationall"	     => $total_violationall,
					// "violation_call"           => $violation_call,
					// "violation_cardistance"    => $violation_cardistance,
					// "violation_distracted"     => $violation_distracted,
					// "violation_fatigue"        => $violation_fatigue,
					// "violation_smoking"        => $violation_smoking,
					// "violation_driverabnormal" => $violation_driverabnormal,
					// "dataRomFix"    => $dataRomFix
				));
  }

	function violation_historikalreport(){
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

		$companyid                   = $this->sess->user_company;
		$user_dblive                 = $this->sess->user_dblive;
		$mastervehicle               = $this->m_poipoolmaster->getmastervehicleforheatmap();
		$violationmaster             = $this->m_violation->getviolationmaster();

		// echo "<pre>";
		// var_dump($mastervehicle);die();
		// echo "<pre>";

		$datafix                     = array();
		$deviceidygtidakada          = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);

			if (isset($jsonautocheck->auto_status)) {
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

		// GET ROM ROAD
		$romRoad                  = $this->m_poipoolmaster->getstreet_now2(5);
		$this->params['rom_road'] = $romRoad;

		// echo "<pre>";
		// var_dump($romRoad);die();
		// echo "<pre>";

		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "report";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicles']  = $datafix;
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

		$this->params['resultactive']    = $this->dashboardmodel->vehicleactive();
		$this->params['resultexpired']   = $this->dashboardmodel->vehicleexpired();
		$this->params['resulttotaldev']  = $this->dashboardmodel->totaldevice();
		$this->params['mapsetting']      = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']      = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$this->params['violationmaster'] = $violationmaster;

		// echo "<pre>";
		// var_dump($this->params['vehicles']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_historikalreport', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_historikalreport', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_historikalreport', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_historikalreport', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_historikalreport', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_historikalreport', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/violation/v_violation_historikalreport', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}

	function search_violationtablehistorikal(){
		$company               = $this->input->post("company");
		$vehicle               = $this->input->post("vehicle");
		$violationmasterselect = $this->input->post("violationmasterselect");
		$startdate             = $this->input->post("startdate");
		$shour                 = "00:00:00";
		$enddate               = $this->input->post("enddate");
		$ehour                 = "23:59:59";
		$periode               = $this->input->post("periode");
		$alarmtypefromaster    = array();


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

		$street_onduty = $this->config->item("street_onduty_autocheck");

		if ($violationmasterselect == 6) {
			$alarmtypefromaster[] = 9999;
		}else {
			if ($violationmasterselect != "all") {
				$alarmbymaster = $this->m_violation->getalarmbytype($violationmasterselect);
				for ($i=0; $i < sizeof($alarmbymaster); $i++) {
					$alarmtypefromaster[] = $alarmbymaster[$i]['alarm_type'];
				}
			}
		}

		// echo "<pre>";
		// var_dump($alarmtypefromaster);die();
		// echo "<pre>";

		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id, user_dblive");
		$this->db->order_by("user_id","asc");
		$this->db->where("user_id", 4408);
		$q         = $this->db->get("user");
		$row       = $q->row();
		$total_row = count($row);

		//print_r($sdate." ".$edate);exit();
		if(count($row)>0){
			$user_dblive = $row->user_dblive;
		}

		// CHOOSE DBTABLE
		// $current_date      = date("Y-m-d H:i:s", strtotime("+1 Hour"));
		$m1                = date("F", strtotime($sdate));
		$year              = date("Y", strtotime($sdate));
		$dbtable           = "";
		$dbtable_overspeed = "";
		$report            = "historikal_violation_";
		$report_overspeed  = "overspeed_hour_";

		switch ($m1)
		{
			case "January":
						$dbtable           = $report."januari_".$year;
						$dbtable_overspeed = $report_overspeed."januari_".$year;
			break;
			case "February":
						$dbtable = $report."februari_".$year;
						$dbtable_overspeed = $report_overspeed."februari_".$year;
			break;
			case "March":
						$dbtable = $report."maret_".$year;
						$dbtable_overspeed = $report_overspeed."maret_".$year;
			break;
			case "April":
						$dbtable = $report."april_".$year;
						$dbtable_overspeed = $report_overspeed."april_".$year;
			break;
			case "May":
						$dbtable = $report."mei_".$year;
						$dbtable_overspeed = $report_overspeed."mei_".$year;
			break;
			case "June":
						$dbtable = $report."juni_".$year;
						$dbtable_overspeed = $report_overspeed."juni_".$year;
			break;
			case "July":
						$dbtable = $report."juli_".$year;
						$dbtable_overspeed = $report_overspeed."juli_".$year;
			break;
			case "August":
						$dbtable = $report."agustus_".$year;
						$dbtable_overspeed = $report_overspeed."agustus_".$year;
			break;
			case "September":
						$dbtable = $report."september_".$year;
						$dbtable_overspeed = $report_overspeed."september_".$year;
			break;
			case "October":
						$dbtable = $report."oktober_".$year;
						$dbtable_overspeed = $report_overspeed."oktober_".$year;
			break;
			case "November":
						$dbtable = $report."november_".$year;
						$dbtable_overspeed = $report_overspeed."november_".$year;
			break;
			case "December":
						$dbtable = $report."desember_".$year;
						$dbtable_overspeed = $report_overspeed."desember_".$year;
			break;
		}

		$user_level      = $this->sess->user_level;
		$user_parent     = $this->sess->user_parent;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
		$privilegecode 	 = $this->sess->user_id_role;
		$user_id_fix     = $this->sess->user_id;

		if($privilegecode == 1){
			$contractor = $company;
		}else if($privilegecode == 2){
			$contractor = $company;
		}else if($privilegecode == 3){
			$contractor = $company;
		}else if($privilegecode == 4){
			$contractor = $company;
		}else if($privilegecode == 5){
			$contractor = $user_company;
		}else if($privilegecode == 6){
			$contractor = $user_company;
		}else if($privilegecode == 0){
			$contractor = $company;
		}else{
			$contractor = $company;
		}

		// echo "<pre>";
		// var_dump($contractor);die();
		// echo "<pre>";

		$datavehiclebycompany               = $this->m_violation->getMasterVehiclebycompany($contractor);

		// echo "<pre>";
		// var_dump($datavehiclebycompany);die();
		// echo "<pre>";

		if ($contractor != 0) {
			$contractor_array = array($contractor);
			for ($i=0; $i < sizeof($datavehiclebycompany); $i++) {
				$company_id = $datavehiclebycompany[$i]['vehicle_company'];
					if ($company_id != $contractor) {
						$contractor_array[] = $company_id;
					}else {
						$contractor_array[] = $contractor;
					}
			}
		}else {
			$contractor_array = array($contractor);
		}

		// echo "<pre>";
		// var_dump($datavehiclebycompany);die();
		// echo "<pre>";

		$data_array_alert = array();
		// $data_overspeed   = $this->m_violation->get_overspeed_intensor_historikal($dbtable_overspeed, $vehicle, $contractor_array, $sdate, $edate);
		$data_overspeed   = $this->m_violation->get_overspeed_intensor_historikal($dbtable_overspeed, $vehicle, $contractor, $sdate, $edate);

		// var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();

			// echo "<pre>";
			// var_dump($data_overspeed);die();
			// // var_dump($dbtable_overspeed.'-'.$vehicle.'-'.$contractor.'-'.$sdate.'-'.$edate);die();
			// echo "<pre>";

		for ($i=0; $i < sizeof($data_overspeed); $i++) {
			$coordinate = explode(",", $data_overspeed[$i]['overspeed_report_coordinate']);
			array_push($data_array_alert, array(
				"isfatigue"          => "no",
				"jalur_name"         => $data_overspeed[$i]['overspeed_report_jalur'],
				"vehicle_no"         => $data_overspeed[$i]['overspeed_report_vehicle_no'],
				"vehicle_name"       => $data_overspeed[$i]['overspeed_report_vehicle_name'],
				"vehicle_company"    => $data_overspeed[$i]['overspeed_report_vehicle_company'],
				"vehicle_device"     => $data_overspeed[$i]['overspeed_report_vehicle_device'],
				"vehicle_mv03"       => "",
				"gps_alert" 				 => "Overspeed",
				"violation" 				 => "Overspeed",
				"violation_level" 	 => $data_overspeed[$i]['overspeed_report_level_alias'],
				"violation_type" 		 => "overspeed",
				"gps_latitude_real"  => $coordinate[0],
				"gps_longitude_real" => $coordinate[1],
				"gps_speed"          => $data_overspeed[$i]['overspeed_report_speed'],
				"gps_speed_limit"    => $data_overspeed[$i]['overspeed_report_geofence_limit'],
				"gps_time"           => date("Y-m-d H:i:s", strtotime($data_overspeed[$i]['overspeed_report_gps_time'])),
				"geofence"           => $data_overspeed[$i]['overspeed_report_geofence_name'],
				"position"           => $data_overspeed[$i]['overspeed_report_location'],
			));
		}

			// echo "<pre>";
			// // var_dump($data_array_alert);die();
			// // var_dump($dbtable.'-'.$vehicle.'-'.$contractor.'-'.$alarmtypefromaster.'-'.$sdate.'-'.$edate);die();
			// var_dump($alarmtypefromaster);die();
			// echo "<pre>";


		// $masterviolation   = $this->m_violation->getviolationhistorikal($dbtable, $sdate, $nowtime_wita, $contractor, $alarmtypefromaster, $limit);
		// $masterviolation   = $this->m_violation->getviolationhistorikal_type2_report($dbtable, $vehicle, $contractor_array, $alarmtypefromaster, $sdate, $edate);

		// echo "<pre>";
		// var_dump($alarmtypefromaster);die();
		// echo "<pre>";

		$masterviolation   = $this->m_violation->getviolationhistorikal_type2_report($dbtable, $vehicle, $contractor, $alarmtypefromaster, $sdate, $edate);


		// echo "<pre>";
		// var_dump($masterviolation);die();
		// echo "<pre>";

			if (sizeof($masterviolation) > 0) {
					for ($j=0; $j < sizeof($masterviolation); $j++) {
						if ($masterviolation[$j]['violation_fatigue'] != "") {
							$positionforfilter = $masterviolation[$j]['violation_position'];
								// if ($positionforfilter != "") {
									// UNTUK UMUM START
									// if (in_array($positionforfilter, $street_onduty)){
										$json_fatigue            = json_decode($masterviolation[$j]['violation_fatigue']);
										$forcheck_vehicledevice  = $json_fatigue[0]->vehicle_device;
										$forcheck_gps_time       = $json_fatigue[0]->gps_time;
										// echo "<pre>";
										// var_dump($json_fatigue);die();
										// echo "<pre>";
										// $jsonautocheck 					 = json_decode($checkthis[0]['vehicle_autocheck']);
										// $jalurname               = $jsonautocheck->auto_last_road;
										$jalurname               = $masterviolation[$j]['violation_jalur'];
										$alarmreportnamefix = "";
										$alarmreporttype = $json_fatigue[0]->gps_alertid;
											if ($alarmreporttype == 626) {
												$alarmreportnamefix = "Driver Undetected Alarm Level One Start";
											}elseif ($alarmreporttype == 627) {
												$alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
											}elseif ($alarmreporttype == 702) {
												$alarmreportnamefix = "Distracted Driving Alarm Level One Start";
											}elseif ($alarmreporttype == 703) {
												$alarmreportnamefix = "Distracted Driving Alarm Level Two Start";
											}elseif ($alarmreporttype == 752) {
												$alarmreportnamefix = "Distracted Driving Alarm Level One End";
											}elseif ($alarmreporttype == 753) {
												$alarmreportnamefix = "Distracted Driving Alarm Level Two End";
											}else {
												$alarmreportnamefix = $json_fatigue[0]->gps_alert;
											}

											// echo "<pre>";
											// var_dump($alarmreporttype);die();
											// echo "<pre>";

											// [{"isfatigue":"yes","vehicle_user_id":"4408","vehicle_no":"BMT 3690","vehicle_name":"Hino 500","vehicle_company":"1839",
											// 	"vehicle_device":"869926046534273@VT200","vehicle_mv03":"819051868196","gps_alertid":618,"gps_alert":
											// 	"Fatigue Driving Alarm Level One Start","gps_time":"2023-02-18 13:35:51","gps_latitude_real":"-3.715216","gps_longitude_real":"115.645760","position":"KM 3","gps_speed":23}]

											// [{"isfatigue":"yes","vehicle_user_id":"4408","vehicle_no":"BMT 3690","vehicle_name":"Hino 500",
											// 	"vehicle_company":"1839","vehicle_device":"869926046534273@VT200","vehicle_mv03":"819051868196","gps_alertid":604,"gps_alert":"Car Distance Near AlarmLevel One","gps_time":"2023-02-18 14:26:27","gps_latitude_real":"-3.716664","gps_longitude_real":"115.645625","position":"KM 3","gps_speed":31}]

											if ($alarmreporttype != 624 && $alarmreporttype != 625) {
												// if (in_array($masterviolation[$j]['violation_position'], $street_onduty)) {
													$violation_split = explode("Level", $alarmreportnamefix);
													// $violationlevel  = explode("Alarm", "", $alarmreportnamefix);
													if (strpos($alarmreportnamefix, "One")) {
														$violationfix = "1";
													}else {
														$violationfix = "2";
													}

													// echo "<pre>";
													// var_dump($violation_split);die();
													// echo "<pre>";

													array_push($data_array_alert, array(
														 "isfatigue"               => "yes",
														 "jalur_name"              => $jalurname,
														 "vehicle_no"              => $json_fatigue[0]->vehicle_no,
														 "vehicle_name"            => $json_fatigue[0]->vehicle_name,
														 "vehicle_company"         => $json_fatigue[0]->vehicle_company,
														 "vehicle_device"          => $json_fatigue[0]->vehicle_device,
														 "vehicle_mv03"            => $json_fatigue[0]->vehicle_mv03,
														 "alert_id" 							 => $alarmreporttype,
														 "gps_alert"               => $alarmreportnamefix,
														 "violation" 				       => $violation_split[0],
														 "violation_level" 				 => "Level " . $violationfix,
														 "violation_type" 		     => "not_overspeed",
														 "gps_time"                => $json_fatigue[0]->gps_time,
														 "gps_latitude_real"       => $json_fatigue[0]->gps_latitude_real,
														 "gps_longitude_real"      => $json_fatigue[0]->gps_longitude_real,
														 "position"                => $masterviolation[$j]['violation_position'],
														 // "auto_last_position"      => $jsonautocheck->auto_last_position,
														 "gps_speed"               => $json_fatigue[0]->gps_speed,
													));
												// } NANTI DI AKTIFIN KALO AUTOCHECK UDAH READY
											}
									// }
									// UNTUK UMUM END
								// }
					}
				}

				// echo "<pre>";
				// var_dump($data_array_alert);die();
				// echo "<pre>";

				$lasttime     = $masterviolation[0]['violation_update'];
				// $lasttime     = date("Y-m-d H:i:s", strtotime($masterviolation[0]['violation_update']."-15 minutes"));
			}else {
				$lasttime = $sdate;
			}

			 usort($data_array_alert, function($a, $b) {
					return strtotime($b['gps_time']) - strtotime($a['gps_time']);
			});

			// $violationmix = $this->aasort($data_array_alert, "gps_time");
			$data_array_fix = array();
			if($violationmasterselect == 6) {
				for ($i=0; $i < sizeof($data_array_alert); $i++) {
				$violation_type = $data_array_alert[$i]['violation_type'];
					if ($violation_type == "overspeed") {
						array_push($data_array_fix, array(
							"isfatigue"                => $data_array_alert[$i]['isfatigue'],
							"jalur_name"               => $data_array_alert[$i]['jalur_name'],
							"vehicle_no"               => $data_array_alert[$i]['vehicle_no'],
							"vehicle_name"             => $data_array_alert[$i]['vehicle_name'],
							"vehicle_company"          => $data_array_alert[$i]['vehicle_company'],
							"vehicle_device"           => $data_array_alert[$i]['vehicle_device'],
							"vehicle_mv03"             => $data_array_alert[$i]['vehicle_mv03'],
							"gps_alert" 				       => $data_array_alert[$i]['gps_alert'],
							"violation" 				       => $data_array_alert[$i]['violation'],
							"violation_level" 				 => $data_array_alert[$i]['violation_level'],
							"violation_type" 		       => $data_array_alert[$i]['violation_type'],
							"gps_latitude_real"        => $data_array_alert[$i]['gps_latitude_real'],
							"gps_longitude_real"       => $data_array_alert[$i]['gps_longitude_real'],
							"gps_speed"                => $data_array_alert[$i]['gps_speed'],
							"gps_speed_limit"          => $data_array_alert[$i]['gps_speed_limit'],
							"gps_time"                 => $data_array_alert[$i]['gps_time'],
							"geofence"                 => $data_array_alert[$i]['geofence'],
							"position"                 => $data_array_alert[$i]['position'],
						));
					}
				}
			}elseif($violationmasterselect != "0") {
				for ($i=0; $i < sizeof($data_array_alert); $i++) {
					$violation_type = $data_array_alert[$i]['violation_type'];
						if ($violation_type == "not_overspeed") {
							array_push($data_array_fix, array(
								"isfatigue"          => $data_array_alert[$i]['isfatigue'],
								"jalur_name"         => $data_array_alert[$i]['jalur_name'],
								"vehicle_no"         => $data_array_alert[$i]['vehicle_no'],
								"vehicle_name"       => $data_array_alert[$i]['vehicle_name'],
								"vehicle_company"    => $data_array_alert[$i]['vehicle_company'],
								"vehicle_device"     => $data_array_alert[$i]['vehicle_device'],
								"vehicle_mv03"       => $data_array_alert[$i]['vehicle_mv03'],
								"gps_alert"          => $data_array_alert[$i]['gps_alert'],
								"violation" 				 => $data_array_alert[$i]['violation'],
								"violation_level" 	 => $data_array_alert[$i]['violation_level'],
								"violation_type" 		 => $data_array_alert[$i]['violation_type'],
								"gps_time"           => $data_array_alert[$i]['gps_time'],
								// "auto_last_update"   => $data_array_alert[$i]['auto_last_update'],
								// "auto_last_check"    => $data_array_alert[$i]['auto_last_check'],
								"gps_latitude_real"  => $data_array_alert[$i]['gps_latitude_real'],
								"gps_longitude_real" => $data_array_alert[$i]['gps_longitude_real'],
								"position"           => $data_array_alert[$i]['position'],
								// "auto_last_position" => $data_array_alert[$i]['auto_last_position'],
								"gps_speed"          => $data_array_alert[$i]['gps_speed'],
							));
						}
				}
			}else {
				$data_array_fix = $data_array_alert;
			}

		// echo "<pre>";
		// var_dump($data_array_fix);die();
		// echo "<pre>";

		$this->params['data']       = $data_array_fix;
		$rows_company               = $this->get_company();
		$this->params["rcompany"]   = $rows_company;
		$this->params["contractor"] = $contractor;

		$html = $this->load->view("newdashboard/violation/v_violation_historikalreportresult", $this->params, true);
		$callback['error'] = false;
		$callback['html']  = $html;
		$callback['data']  = $data_array_fix;
		echo json_encode($callback);
	}

	function get_vehicle_by_company_with_vdevice($id)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
			if ($id != "all") {
				$this->db->where("vehicle_company", $id);
			}
			if ($this->sess->user_group > 0) {
				$this->db->where("vehicle_group", $this->sess->user_group);
			}
		$this->db->where("vehicle_status <>", 3);
		$this->db->join("company", "vehicle_company = company_id", "left");
		$this->db->or_where("vehicle_id_shareto", $id);
		$this->db->order_by("vehicle_no", "asc");
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

	function get_company()
	{
	    if (!isset($this->sess->user_type)) {
	        redirect(base_url());
	    }

	    $privilegecode = $this->sess->user_id_role;


	    $this->db->order_by("company_name", "asc");
	    // if ($privilegecode == 0) {
	    //     $this->db->where("company_created_by", $this->sess->user_id);
	    // } elseif ($privilegecode == 1) {
	    //     $this->db->where("company_created_by", $this->sess->user_parent);
	    // } elseif ($privilegecode == 2) {
	    //     $this->db->where("company_created_by", $this->sess->user_parent);
	    // } elseif ($privilegecode == 3) {
	    //     $this->db->where("company_created_by", $this->sess->user_parent);
	    // } elseif ($privilegecode == 4) {
	    //     $this->db->where("company_created_by", $this->sess->user_parent);
	    // } elseif ($privilegecode == 5) {
	    //     $this->db->where("company_id", $this->sess->user_company);
	    // } elseif ($privilegecode == 6) {
	    //     $this->db->where("company_id", $this->sess->user_company);
	    // }

	    $this->db->where("company_flag", 0);
	    $qd = $this->db->get("company");
	    $rd = $qd->result();

	    return $rd;
	}

	function aasort (&$array, $key) {
    $sorter = array();
    $ret = array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii] = $va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii] = $array[$ii];
    }
    $array = $ret;
	}

}
