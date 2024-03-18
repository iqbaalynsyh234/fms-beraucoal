<?php
include "base.php";
setlocale(LC_ALL, 'IND');

class Maps_development extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Maps_development()
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
    $this->load->model("m_maps_development");
	}

  function quickcount_pmo(){
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

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_maps_development->getmastervehicleforheatmap();

    // echo "<pre>";
    // var_dump($master_site);die();
    // echo "<pre>";

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

		$site_id = 7;
		$company = $this->m_maps_development->getcompany_by_parent_site($site_id);
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

		$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
		$this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
		$this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
    // $this->params['master_site']    = $master_site;
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// KONDISI BEACHING POINT START
		$street_beachingpoint   = 11;
		$street_beaching_point  = $this->m_maps_development->getstreet_now($street_beachingpoint);
		$this->params['beaching_point'] = $street_beaching_point;

		// KONDISI BEACHING POINT END

		// echo "<pre>";
		// var_dump($dataState);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

    if ($privilegecode == 1) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/development/maps/v_home_quickcount_pmo', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    }elseif ($privilegecode == 2) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/development/maps/v_home_quickcount_pmo', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    }elseif ($privilegecode == 3) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/development/maps/v_home_quickcount_pmo', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    }elseif ($privilegecode == 4) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/development/maps/v_home_quickcount_pmo', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    }elseif ($privilegecode == 5) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/development/maps/v_home_quickcount_pmo', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
    }elseif ($privilegecode == 6) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/development/maps/v_home_quickcount_pmo', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
    }else {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/development/maps/v_home_quickcount_pmo', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
    }
	}

	function km_quickcount_new(){
		$company_parent_site    = 7;
		$street_type            = 1;
		$street_beachingpoint   = 11;
		$companyid 				      = $_POST['companyid'];
		$masterdatavehicle      = $this->m_maps_development->getmastervehiclebycontractor($companyid);
		$street_hauling_data    = $this->m_maps_development->master_street_hauling($company_parent_site, $street_type);
		$street_beaching_point  = $this->m_maps_development->getstreet_now($street_beachingpoint);
		$data_kosongan          = array();
		$data_muatan            = array();
		$dataJumlahInKmMuatan   = array();
		$dataJumlahInKmKosongan = array();

		// KONFIGURASI STREET KOSONGAN & MUATAN
		// for ($x=0; $x < sizeof($street_hauling_data); $x++) {
		// 	$street_exp       = explode(",", $street_hauling_data[$x]['street_name']);
		// 	$street_name      = $street_exp[0];
		// 	$street_name_replace = str_replace(" ", "_", $street_name);
		//
		// 	array_push($data_kosongan, array(
		// 		$street_name_replace => 0
		// 	));
		//
		// 	array_push($data_muatan, array(
		// 		$street_name_replace => 0
		// 	));
		// }

		// echo "<pre>";
		// var_dump($street_beaching_point);die();
		// echo "<pre>";

		$data_lokasi       = array();

		// for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		// 	$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		// 	$auto_last_position = explode(",", $autocheck->auto_last_position);
		// 	// $jalur_name         = $autocheck->auto_last_road;
		// 	$jalur_name         = "kosongan";
		// 	// $datalastposition   = $auto_last_position[0];
		// 	$datalastposition   = "PMO_KM_0.5";
		// 	$auto_status 				= $autocheck->auto_status;
		//
		// 	if ($auto_status != "M") {
		// 		for ($j=0; $j < sizeof($street_hauling_data); $j++) {
		// 			$street_exp          = explode(",", $street_hauling_data[$j]['street_name']);
		// 			$street_name         = $street_exp[0];
		// 			$street_name_replace = str_replace(" ", "_", $street_name);
		//
		// 				if ($street_name_replace == $datalastposition) {
		// 					if ($jalur_name == "kosongan") {
		// 						for ($y=0; $y < sizeof($data_kosongan); $y++) {
		// 							if ($street_name_replace == $data_kosongan[$j][$datalastposition]) {
		// 								$data_kosongan[$j][$datalastposition] += 1;
		// 							}
		// 						}
		// 					}else {
		// 						for ($y=0; $y < sizeof($data_muatan); $y++) {
		// 							if ($street_name_replace == $data_muatan[$j][$datalastposition]) {
		// 								$data_muatan[$j][$datalastposition] += 1;
		// 							}
		// 						}
		// 					}
		// 				}
		//
		// 		}
		// 	}
		// }

		// KONDISI BEACHING POINT START
		// $data_beaching_point['BEACHING_POINT_P1'] = 0;
		// $data_beaching_point['BEACHING_POINT_P2'] = 0;
		// $data_beaching_point['BEACHING_POINT_P3'] = 0;
		// $data_beaching_point['BEACHING_POINT_B1'] = 0;
		// $data_beaching_point['BEACHING_POINT_B2'] = 0;
		// $data_beaching_point['BEACHING_POINT_B3'] = 0;
		// $data_beaching_point['KELAY_RIVER']		    = 0;

		for ($j=0; $j < sizeof($street_beaching_point); $j++) {
			$street_name                = explode(",", $street_beaching_point[$j]['street_name']);
			$streetnameformat           = $street_name[0];
			$street_namefix             = str_replace(" ", "_", $streetnameformat);
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

		// echo "<pre>";
		// var_dump($dataState);die();
		// echo "<pre>";
		// KONDISI BEACHING POINT END

		// $config_separate = 2; // INI NANTI AJA KALO UDAH LIVE
		$lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour"));

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

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$jalur_name         = $autocheck->auto_last_road;
			// $jalur_name         = "kosongan";
			$datalastposition   = $auto_last_position[0];
			// $datalastposition   = "PMO_KM_0.5";
			$auto_status 				= $autocheck->auto_status;

			// echo "<pre>";
			// var_dump($auto_status);die();
			// echo "<pre>";

				if ($auto_status != "M") {
				if ($jalur_name == "kosongan") {
					if ($datalastposition == "PMO_KM_0.5" || $datalastposition == "PMO_KM_1") {
						$dataJumlahInKmKosongan['KM_1'] += 1;
					}elseif ($datalastposition == "PMO_KM_1" || $datalastposition == "PMO_KM_1.5") {
						$dataJumlahInKmKosongan['KM_2'] += 1;
					}elseif ($datalastposition == "PMO_KM_2" || $datalastposition == "PMO_KM_2.5") {
						$dataJumlahInKmKosongan['KM_3'] += 1;
					}elseif ($datalastposition == "PMO_KM_3" || $datalastposition == "PMO_KM_3.5") {
						$dataJumlahInKmKosongan['KM_4'] += 1;
					}elseif ($datalastposition == "PMO_KM_4" || $datalastposition == "PMO_KM_4.5") {
						$dataJumlahInKmKosongan['KM_5'] += 1;
					}elseif ($datalastposition == "PMO_KM_5" || $datalastposition == "PMO_KM_5.5") {
						$dataJumlahInKmKosongan['KM_6'] += 1;
					}elseif ($datalastposition == "PMO_KM_6" || $datalastposition == "PMO_KM_6.5") {
						$dataJumlahInKmKosongan['KM_7'] += 1;
					}elseif ($datalastposition == "PMO_KM_7" || $datalastposition == "PMO_KM_7.5") {
						$dataJumlahInKmKosongan['KM_8'] += 1;
					}elseif ($datalastposition == "PMO_KM_8" || $datalastposition == "PMO_KM_8.5") {
						$dataJumlahInKmKosongan['KM_9'] += 1;
					}elseif ($datalastposition == "PMO_KM_9" || $datalastposition == "PMO_KM_9.5") {
						$dataJumlahInKmKosongan['KM_10'] += 1;
					}elseif ($datalastposition == "PMO_KM_10" || $datalastposition == "PMO_KM_10.5") {
						$dataJumlahInKmKosongan['KM_11'] += 1;
					}elseif ($datalastposition == "PMO_KM_11" || $datalastposition == "PMO_KM_11.5") {
						$dataJumlahInKmKosongan['KM_12'] += 1;
					}elseif ($datalastposition == "PMO_KM_12" || $datalastposition == "PMO_KM_12.5") {
						$dataJumlahInKmKosongan['KM_13'] += 1;
					}elseif ($datalastposition == "PMO_KM_13" || $datalastposition == "PMO_KM_13.5") {
						$dataJumlahInKmKosongan['KM_14'] += 1;
					}elseif ($datalastposition == "PMO_KM_14" || $datalastposition == "PMO_KM_14.5") {
						$dataJumlahInKmKosongan['KM_15'] += 1;
					}elseif ($datalastposition == "PMO_KM_15" || $datalastposition == "PMO_KM_15.5") {
						$dataJumlahInKmKosongan['KM_16'] += 1;
					}elseif ($datalastposition == "PMO_KM_16" || $datalastposition == "PMO_KM_16.5") {
						$dataJumlahInKmKosongan['KM_17'] += 1;
					}elseif ($datalastposition == "PMO_KM_17" || $datalastposition == "PMO_KM_17.5") {
						$dataJumlahInKmKosongan['KM_18'] += 1;
					}elseif ($datalastposition == "PMO_KM_18" || $datalastposition == "PMO_KM_18.5") {
						$dataJumlahInKmKosongan['KM_19'] += 1;
					}elseif ($datalastposition == "PMO_KM_19" || $datalastposition == "PMO_KM_19.5") {
						$dataJumlahInKmKosongan['KM_20'] += 1;
					}elseif ($datalastposition == "PMO_KM_20" || $datalastposition == "PMO_KM_20.5") {
						$dataJumlahInKmKosongan['KM_21'] += 1;
					}elseif ($datalastposition == "PMO_KM_21" || $datalastposition == "PMO_KM_21.5") {
						$dataJumlahInKmKosongan['KM_22'] += 1;
					}elseif ($datalastposition == "PMO_KM_22" || $datalastposition == "PMO_KM_22.5") {
						$dataJumlahInKmKosongan['KM_23'] += 1;
					}elseif ($datalastposition == "PMO_KM_23" || $datalastposition == "PMO_KM_23.5") {
						$dataJumlahInKmKosongan['KM_24'] += 1;
					}elseif ($datalastposition == "PMO_KM_24" || $datalastposition == "PMO_KM_24.5") {
						$dataJumlahInKmKosongan['KM_25'] += 1;
					}elseif ($datalastposition == "PMO_KM_25" || $datalastposition == "PMO_KM_25.5") {
						$dataJumlahInKmKosongan['KM_26'] += 1;
					}elseif ($datalastposition == "PMO_KM_26" || $datalastposition == "PMO_KM_26.5") {
						$dataJumlahInKmKosongan['KM_27'] += 1;
					}elseif ($datalastposition == "PMO_KM_27" || $datalastposition == "PMO_KM_27.5") {
						$dataJumlahInKmKosongan['KM_28'] += 1;
					}elseif ($datalastposition == "PMO_KM_28" || $datalastposition == "PMO_KM_28.5") {
						$dataJumlahInKmKosongan['KM_29'] += 1;
					}elseif ($datalastposition == "PMO_KM_29" || $datalastposition == "PMO_KM_29.5") {
						$dataJumlahInKmKosongan['KM_30'] += 1;
					}
				}else {
					if ($datalastposition == "PMO_KM_0.5" || $datalastposition == "PMO_KM_1") {
						$dataJumlahInKmMuatan['KM_1'] += 1;
					}elseif ($datalastposition == "PMO_KM_1" || $datalastposition == "PMO_KM_1.5") {
						$dataJumlahInKmMuatan['KM_2'] += 1;
					}elseif ($datalastposition == "PMO_KM_2" || $datalastposition == "PMO_KM_2.5") {
						$dataJumlahInKmMuatan['KM_3'] += 1;
					}elseif ($datalastposition == "PMO_KM_3" || $datalastposition == "PMO_KM_3.5") {
						$dataJumlahInKmMuatan['KM_4'] += 1;
					}elseif ($datalastposition == "PMO_KM_4" || $datalastposition == "PMO_KM_4.5") {
						$dataJumlahInKmMuatan['KM_5'] += 1;
					}elseif ($datalastposition == "PMO_KM_5" || $datalastposition == "PMO_KM_5.5") {
						$dataJumlahInKmMuatan['KM_6'] += 1;
					}elseif ($datalastposition == "PMO_KM_6" || $datalastposition == "PMO_KM_6.5") {
						$dataJumlahInKmMuatan['KM_7'] += 1;
					}elseif ($datalastposition == "PMO_KM_7" || $datalastposition == "PMO_KM_7.5") {
						$dataJumlahInKmMuatan['KM_8'] += 1;
					}elseif ($datalastposition == "PMO_KM_8" || $datalastposition == "PMO_KM_8.5") {
						$dataJumlahInKmMuatan['KM_9'] += 1;
					}elseif ($datalastposition == "PMO_KM_9" || $datalastposition == "PMO_KM_9.5") {
						$dataJumlahInKmMuatan['KM_10'] += 1;
					}elseif ($datalastposition == "PMO_KM_10" || $datalastposition == "PMO_KM_10.5") {
						$dataJumlahInKmMuatan['KM_11'] += 1;
					}elseif ($datalastposition == "PMO_KM_11" || $datalastposition == "PMO_KM_11.5") {
						$dataJumlahInKmMuatan['KM_12'] += 1;
					}elseif ($datalastposition == "PMO_KM_12" || $datalastposition == "PMO_KM_12.5") {
						$dataJumlahInKmMuatan['KM_13'] += 1;
					}elseif ($datalastposition == "PMO_KM_13" || $datalastposition == "PMO_KM_13.5") {
						$dataJumlahInKmMuatan['KM_14'] += 1;
					}elseif ($datalastposition == "PMO_KM_14" || $datalastposition == "PMO_KM_14.5") {
						$dataJumlahInKmMuatan['KM_15'] += 1;
					}elseif ($datalastposition == "PMO_KM_15" || $datalastposition == "PMO_KM_15.5") {
						$dataJumlahInKmMuatan['KM_16'] += 1;
					}elseif ($datalastposition == "PMO_KM_16" || $datalastposition == "PMO_KM_16.5") {
						$dataJumlahInKmMuatan['KM_17'] += 1;
					}elseif ($datalastposition == "PMO_KM_17" || $datalastposition == "PMO_KM_17.5") {
						$dataJumlahInKmMuatan['KM_18'] += 1;
					}elseif ($datalastposition == "PMO_KM_18" || $datalastposition == "PMO_KM_18.5") {
						$dataJumlahInKmMuatan['KM_19'] += 1;
					}elseif ($datalastposition == "PMO_KM_19" || $datalastposition == "PMO_KM_19.5") {
						$dataJumlahInKmMuatan['KM_20'] += 1;
					}elseif ($datalastposition == "PMO_KM_20" || $datalastposition == "PMO_KM_20.5") {
						$dataJumlahInKmMuatan['KM_21'] += 1;
					}elseif ($datalastposition == "PMO_KM_21" || $datalastposition == "PMO_KM_21.5") {
						$dataJumlahInKmMuatan['KM_22'] += 1;
					}elseif ($datalastposition == "PMO_KM_22" || $datalastposition == "PMO_KM_22.5") {
						$dataJumlahInKmMuatan['KM_23'] += 1;
					}elseif ($datalastposition == "PMO_KM_23" || $datalastposition == "PMO_KM_23.5") {
						$dataJumlahInKmMuatan['KM_24'] += 1;
					}elseif ($datalastposition == "PMO_KM_24" || $datalastposition == "PMO_KM_24.5") {
						$dataJumlahInKmMuatan['KM_25'] += 1;
					}elseif ($datalastposition == "PMO_KM_25" || $datalastposition == "PMO_KM_25.5") {
						$dataJumlahInKmMuatan['KM_26'] += 1;
					}elseif ($datalastposition == "PMO_KM_26" || $datalastposition == "PMO_KM_26.5") {
						$dataJumlahInKmMuatan['KM_27'] += 1;
					}elseif ($datalastposition == "PMO_KM_27" || $datalastposition == "PMO_KM_27.5") {
						$dataJumlahInKmMuatan['KM_28'] += 1;
					}elseif ($datalastposition == "PMO_KM_28" || $datalastposition == "PMO_KM_28.5") {
						$dataJumlahInKmMuatan['KM_29'] += 1;
					}elseif ($datalastposition == "PMO_KM_29" || $datalastposition == "PMO_KM_29.5") {
						$dataJumlahInKmMuatan['KM_30'] += 1;
					}
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

		// echo json_encode(array("msg" => "success", "code" => 200, "dataPortCPBIB" => $dataStatePortCPBIB, "dataPortANTBIR" => $dataStatePortANTBIR, "dataRominQuickCount" => $dataStateRom, "dataPortinQuickCount" => $dataStatePort, "dataMuatan" => $dataJumlahInKmMuatan, "dataKosongan" => $dataJumlahInKmKosongan, "dataMuatan2" => $dataJumlahInKmMuatan_2, "dataKosongan2" => $dataJumlahInKmKosongan_2, "lastcheck" => $lasttimecheck, "datafixlimitperkmallmuatan" => $datafixlimitperkmallmuatan, "datafixlimitperkmallkosongan" => $datafixlimitperkmallkosongan, "datafixlimitkm1muatan" => $datafixlimitkm1muatan));
		echo json_encode(array("msg" => "success", "code" => 200, "beaching_point" => $dataState, "dataMuatan" => $dataJumlahInKmMuatan, "dataKosongan" => $dataJumlahInKmKosongan, "lastcheck" => $lasttimecheck, "datafixlimitperkmallmuatan" => $datafixlimitperkmallmuatan, "datafixlimitperkmallkosongan" => $datafixlimitperkmallkosongan, "datafixlimitkm1muatan" => $datafixlimitkm1muatan));
	}


}
