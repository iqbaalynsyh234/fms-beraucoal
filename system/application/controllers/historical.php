<?php
include "base.php";
setlocale(LC_ALL, 'IND');

class Historical extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Historical()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("dashboardmodel");
		$this->load->model("log_model");
    $this->load->model("m_historical");
		$this->load->model("m_poipoolmaster");
	}

  function index(){
    if (! isset($this->sess->user_type))
  	{
  		redirect(base_url());
  	}

    $user_id       = $this->sess->user_id;
  	$user_parent   = $this->sess->user_parent;
  	$privilegecode = $this->sess->user_id_role;
  	$user_company  = $this->sess->user_company;

    $company       = $this->dashboardmodel->getcompany_byowner($privilegecode);
    $mastervehicle = $this->m_poipoolmaster->getmastervehicleforheatmap();


    // echo "<pre>";
    // var_dump($mastervehicle);die();
    // echo "<pre>";

    $this->params['company']     = $company;
    $this->params['vehicle']     = $mastervehicle;
    $this->params["header"]      = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  	$this->params["chatsidebar"] = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

  		if ($privilegecode == 1) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/historical/v_home_historical', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
  		}elseif ($privilegecode == 2) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/historical/v_home_historical', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
  		}elseif ($privilegecode == 3) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/historical/v_home_historical', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
  		}elseif ($privilegecode == 4) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/historical/v_home_historical', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
  		}elseif ($privilegecode == 5) {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/historical/v_home_historical', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
  		}else {
  			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
  			$this->params["content"]        = $this->load->view('newdashboard/historical/v_home_historical', $this->params, true);
  			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  		}
  }

  function searchhistorical(){
    $date          = date("Y-m-d", strtotime($this->input->post('date')));
    $starttime     = $date . " " .date("H:i:s", strtotime("-5 Minutes", strtotime($this->input->post('starttime').":00")));
		$endtime       = $date . " ". $this->input->post('starttime').":00";
    $contractor    = $this->input->post('contractor');
    $mapsOptions   = $this->input->post('mapsOptions');
		$dateforsearch = $date.' '.$starttime;

		// $date.'-'.$starttime.'-'.$dateforsearch.'-'.$contractor.'-'.$mapsOptions

		$dataFromLocationHour = $this->m_historical->getDataLocationHour("ts_location_hour", $date, $starttime, $endtime, $contractor);

		// echo "<pre>";
		// var_dump($starttime.'-'.$endtime);die();
		// echo "<pre>";

		if ($mapsOptions == "0") {
			$mapsOptions = "showHeatmap";

			if (sizeof($dataFromLocationHour) > 0) {
				echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataFromLocationHour, "mapsoption" => $mapsOptions));
			}else {
				echo json_encode(array("msg" => "success", "code" => 400));
			}
		}elseif ($mapsOptions == "showHeatmap") {

			if (sizeof($dataFromLocationHour) > 0) {
				echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataFromLocationHour, "mapsoption" => $mapsOptions));
			}else {
				echo json_encode(array("msg" => "success", "code" => 400));
			}
		}elseif($mapsOptions == "showTableMuatan1") {
			if (sizeof($dataFromLocationHour) > 0) {
				$dataKmMuatanFix   = array();
				$dataKmKosonganFix = array();

				// echo "<pre>";
				// var_dump($dataFromLocationHour);die();
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

				for ($i=0; $i < sizeof($dataFromLocationHour); $i++) {
					$datalastposition = $dataFromLocationHour[$i]['location_report_location'];
					$jalur_name       = $dataFromLocationHour[$i]['location_report_jalur'];

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
				// var_dump($dataFromLocationHour);die();
				// echo "<pre>";

				for ($j=0; $j < sizeof($romStreet); $j++) {
					$street_name_rom                  = explode(",", $romStreet[$j]['street_name']);
					$street_nameromfix                = $street_name_rom[0];
					$dataStateRom[$street_nameromfix] = 0;

					for ($i=0; $i < sizeof($dataFromLocationHour); $i++) {
						$datalastposition   = $dataFromLocationHour[$i]['location_report_location'];
						$auto_status 		    = $dataFromLocationHour[$i]['location_report_auto_status'];

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

					for ($i=0; $i < sizeof($dataFromLocationHour); $i++) {
						$datalastposition   = $dataFromLocationHour[$i]['location_report_location'];
						$auto_status 		    = $dataFromLocationHour[$i]['location_report_auto_status'];

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

					for ($i=0; $i < sizeof($dataFromLocationHour); $i++) {
						$datalastposition   = $dataFromLocationHour[$i]['location_report_location'];
						$auto_status 		    = $dataFromLocationHour[$i]['location_report_auto_status'];

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

					for ($i=0; $i < sizeof($dataFromLocationHour); $i++) {
						$datalastposition   = $dataFromLocationHour[$i]['location_report_location'];
						$auto_status 		    = $dataFromLocationHour[$i]['location_report_auto_status'];

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

				echo json_encode(array("msg" => "success", "code" => 200,  "mapsoption" => $mapsOptions, "dataPortCPBIB" => $dataStatePortCPBIB, "dataPortANTBIR" => $dataStatePortANTBIR,
															 "dataRominQuickCount" => $dataStateRom, "dataPortinQuickCount" => $dataStatePort, "dataMuatan" => $dataJumlahInKmMuatan,
															 "dataKosongan" => $dataJumlahInKmKosongan, "dataMuatan2" => $dataJumlahInKmMuatan_2, "dataKosongan2" => $dataJumlahInKmKosongan_2,
															 "lastcheck" => $lasttimecheck, "datafixlimitperkmallmuatan" => $datafixlimitperkmallmuatan, "datafixlimitperkmallkosongan" => $datafixlimitperkmallkosongan,
															 "datafixlimitkm1muatan" => $datafixlimitkm1muatan));
			}else {
				echo json_encode(array("msg" => "failed", "code" => 400));
			}
		}elseif ($mapsOptions == "showTableRom") {
			if (sizeof($dataFromLocationHour) > 0) {
						$allStreet         = $this->m_poipoolmaster->getstreet_now(3);
						$dataRomFix        = array();
						$lasttimecheck 		 = date("d-m-Y H:i:s", strtotime("+1 hour"));

						for ($j=0; $j < sizeof($allStreet); $j++) {
							$street_name                = explode(",", $allStreet[$j]['street_name']);
							$street_namefix             = $street_name[0];
							$dataState[$street_namefix] = 0;

							for ($k=0; $k < sizeof($dataFromLocationHour); $k++) {
								$datalastposition   	= $dataFromLocationHour[$k]['location_report_location'];
								if (strpos($datalastposition, 'ROM') !== false) {
									$auto_status 		    = $dataFromLocationHour[$k]['location_report_auto_status'];

										if ($auto_status != "M") {
											if ($datalastposition == $street_namefix) {
													$dataState[$street_namefix] += 1;
											}
										}
									}
								}
							}
							// LIMIT SETTING PER KM
							$getdataFromStreet = $this->m_poipoolmaster->getstreet_now(3);
							$mapSettingType    = $this->m_poipoolmaster->getMapSettingByType(3);

							$postfix_middle_limit = "_middle_limit";
							$postfix_top_limit    = "_top_limit";

							if (isset($getdataFromStreet)) {
								$datafixlimitperkm = array();
								for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
									$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
									$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

									$middlelimitname          = $streetfix.$postfix_middle_limit;
									$toplimitname             = $streetfix.$postfix_top_limit;

									$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

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

				echo json_encode(array("msg" => "success", "code" => 200, "mapsoption" => $mapsOptions, "data" => $dataState, "allstreet" => $allStreet, "lastcheck" => $lasttimecheck, "datafixlimit" => $datafixlimitperkm));
			}else {
				echo json_encode(array("msg" => "failed", "code" => 400));
			}
		}elseif ($mapsOptions == "showTablePort") {
			$allStreet         = $this->m_poipoolmaster->getstreet_now(4);
			$dataRomFix        = array();
			$lasttimecheck 		 = date("d-m-Y H:i:s", strtotime("+1 hour"));

			for ($j=0; $j < sizeof($allStreet); $j++) {
				$street_name                = explode(",", $allStreet[$j]['street_name']);
				$street_namefix             = $street_name[0];
				$dataState[$street_namefix] = 0;

				for ($i=0; $i < sizeof($dataFromLocationHour); $i++) {
					$auto_last_position = explode(",", $dataFromLocationHour[$i]['location_report_location']);
					$datalastposition   = $auto_last_position[0];
					$auto_status 		    = $dataFromLocationHour[$i]['location_report_auto_status'];

					if ($auto_status != "M") {
						if ($datalastposition == $street_namefix) {
								$dataState[$street_namefix] += 1;
						}
					}
				}
			}

			// echo "<pre>";
			// var_dump($dataFromLocationHour);die();
			// echo "<pre>";

			// LIMIT SETTING PER KM
			$getdataFromStreet = $this->m_poipoolmaster->getstreet_now(4);
			$mapSettingType    = $this->m_poipoolmaster->getMapSettingByType(4);

			$postfix_middle_limit = "_middle_limit";
			$postfix_top_limit    = "_top_limit";

			if (isset($getdataFromStreet)) {
				$datafixlimitperkm = array();
				for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
					$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
					$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

					$middlelimitname          = $streetfix.$postfix_middle_limit;
					$toplimitname             = $streetfix.$postfix_top_limit;

					$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

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

			echo json_encode(array("msg" => "success", "code" => 200, "mapsoption" => $mapsOptions, "data" => $dataState, "allstreet" => $allStreet, "lastcheck" => $lasttimecheck, "datafixlimit" => $datafixlimitperkm));
		}elseif ($mapsOptions == "showTablePool") {
			$getallcompany                    = $this->m_poipoolmaster->getAllCompany();
			$masterdatavehicle                = $dataFromLocationHour;

			$lasttimecheck 		                = date("d-m-Y H:i:s", strtotime("+1 hour"));
			$dataoutofhauling['outofhauling'] = 0;

			// echo "<pre>";
			// var_dump($masterdatavehicle);die();
			// echo "<pre>";

			$datapool  = array();
			for ($i=0; $i < sizeof($getallcompany); $i++) {
				$getChild      = $this->m_poipoolmaster->getStreetByParent($getallcompany[$i]['company_id']);
					for ($j=0; $j < sizeof($getChild); $j++) {
						$child_name = explode(",", $getChild[$j]['street_name']);
						array_push($datapool, array(
							$getallcompany[$i]['company_name'] => $getallcompany[$i]['company_name'].'|'.$child_name[0]
						));
					}
			}

			for ($i=0; $i < sizeof($getallcompany); $i++) {
				$datacompany                = $getallcompany[$i]['company_name'];
				$street_namefix             = $datacompany;
				$dataState[$street_namefix] = 0;

					for ($j=0; $j < sizeof($datapool); $j++) {
						if (isset($datapool[$j][$datacompany])) {
							$datachild = explode("|", $datapool[$j][$datacompany]);

							if ($datacompany == $datachild[0]) {
									for ($k=0; $k < sizeof($masterdatavehicle); $k++) {
											$auto_last_position = explode(",", $masterdatavehicle[$k]['location_report_location']);
											$datalastposition   = $auto_last_position[0];
											$auto_status 		    = $masterdatavehicle[$k]['location_report_auto_status'];

											if ($auto_status != "M") {
												if ($datalastposition == $datachild[1]) {
													$dataState[$street_namefix] += 1;
												}
											}
										}
							}
						}
					}
			}

			// echo "<pre>";
			// var_dump($dataState);die();
			// echo "<pre>";

			for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				$auto_last_hauling = explode(",", $masterdatavehicle[$i]['location_report_hauling']);
				$datalastposition  = $auto_last_hauling[0];

					if ($datalastposition == "out") {
						$dataoutofhauling['outofhauling'] += 1;
					}
			}

			// echo "<pre>";
			// var_dump($dataoutofhauling);die();
			// echo "<pre>";

			echo json_encode(array("msg" => "success", "code" => 200, "mapsoption" => $mapsOptions, "data" => $dataState, "allcompany" => $getallcompany, "dataoutofhauling" => $dataoutofhauling, "lastcheck" => $lasttimecheck));
		}elseif($mapsOptions == "outofhauling") {
			if (sizeof($dataFromLocationHour) > 0) {
				//
				$companyid 		 	                  = $contractor;
				$dataforclear 										= $this->m_historical->getDataLocationHour("ts_location_hour", $date, $starttime, $endtime,  "0");
				$masterdatavehicle                = $dataFromLocationHour;
				$poolmaster                       = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
				$dataoutofhauling['outofhauling'] = 0;
				$dataVehicleOutofHauling          = array();
				$lasttimecheck 		                = date("d-m-Y H:i:s", strtotime("+1 hour"));

				// echo "<pre>";
				// var_dump($masterdatavehicle);die();
				// echo "<pre>";

					for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
						$datalastposition  = $dataFromLocationHour[$i]['location_report_hauling'];

							if ($datalastposition == "out") {
								$dataoutofhauling['outofhauling'] += 1;

								array_push($dataVehicleOutofHauling, array(
									"vehicle_id"         => $dataFromLocationHour[$i]['location_report_vehicle_id'],
									"vehicle_no"         => $dataFromLocationHour[$i]['location_report_vehicle_no'],
									"vehicle_name"       => $dataFromLocationHour[$i]['location_report_vehicle_name'],
									"vehicle_device"     => $dataFromLocationHour[$i]['location_report_vehicle_device'],
									"auto_last_lat"      => $dataFromLocationHour[$i]['location_report_latitude'],
									"auto_last_long"     => $dataFromLocationHour[$i]['location_report_longitude'],
									"auto_last_course"   => $dataFromLocationHour[$i]['location_report_direction'],
									"auto_last_position" => $dataFromLocationHour[$i]['location_report_location'],
									"auto_last_speed"    => $dataFromLocationHour[$i]['location_report_speed'],
								));
							}
					}

					// echo "<pre>";
					// var_dump($dataVehicleOutofHauling);die();
					// echo "<pre>";

					echo json_encode(array("msg" => "success", "code" => 200, "mapsoption" => $mapsOptions, "data" => $dataoutofhauling, "dataoutofhaulingmaps" => $dataVehicleOutofHauling, "poolmaster" => $poolmaster, "alldataforclearmaps" => $dataforclear, "lastcheck" => $lasttimecheck));
			}else {

			}
		}elseif($mapsOptions == "offlinevehicle") {
			$allCompany 					                = $this->m_poipoolmaster->getAllCompany();
			$dataContractor['BKAE']               = 0;
			$dataContractor['KMB']                = 0;
			$dataContractor['GECL']               = 0;
			$dataContractor['STLI']               = 0;
			$dataContractor['RAMB']               = 0;
			$dataContractor['BBS']                = 0;
			$dataContractor['MKS']                = 0;
			$dataContractor['RBT']                = 0;
			$dataContractor['MMS']                = 0;
			$dataContractor['EST']                = 0;
			$lasttimecheck 		                    = date("d-m-Y H:i:s", strtotime("+1 hour"));
			$masterdatavehicle                    = $dataFromLocationHour;
			$datavehicleoffline['offlinevehicle'] = 0;
			$offlinevehicle                       = array();

			// echo "<pre>";
			// var_dump($masterdatavehicle);die();
			// echo "<pre>";

			for($i=0; $i < count($masterdatavehicle); $i++){
				$auto_last_position       = explode(",", $masterdatavehicle[$i]['location_report_location']);
				$auto_last_positionfix    = $auto_last_position[0];
				$auto_statusofflinvehicle = $masterdatavehicle[$i]['location_report_auto_status'];

					if($auto_statusofflinvehicle == "M" ){
						array_push($offlinevehicle, array(
							"vehicle_no"            => $masterdatavehicle[$i]['location_report_vehicle_no'],
							"vehicle_name"          => $masterdatavehicle[$i]['location_report_vehicle_name'],
							"vehicle_company"       => $masterdatavehicle[$i]['location_report_vehicle_company'],
							"auto_last_lat"         => $masterdatavehicle[$i]['location_report_latitude'],
							"auto_last_long"        => $masterdatavehicle[$i]['location_report_longitude'],
							"auto_last_engine"      => $masterdatavehicle[$i]['location_report_engine'],
							"auto_last_speed"       => $masterdatavehicle[$i]['location_report_speed'],
							"auto_last_positionfix" => $masterdatavehicle[$i]['location_report_location'],
							"auto_last_update"      => date("d-m-Y H:i:s", strtotime($masterdatavehicle[$i]['location_report_gps_time'])),
							"status"                => "Status M"
						));
					}
			}

			for ($k=0; $k < sizeof($offlinevehicle); $k++) {
				for ($j=0; $j < sizeof($allCompany); $j++) {
					if ($offlinevehicle[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
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
				// var_dump($offlinevehicle);die();
				// echo "<pre>";
				echo json_encode(array("msg" => "success", "code" => 200, "mapsoption" => $mapsOptions, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $offlinevehicle, "lastcheck" => $lasttimecheck));
		}elseif($mapsOptions == "standardmaps") {
			$forclearmaps 										 = $this->m_historical->getDataLocationHour("ts_location_hour", $date, $starttime, $endtime, "0");
			$mastervehicle                     = $dataFromLocationHour;

			$datafix                           = array();
			$deviceidygtidakada                = array();

			for ($i=0; $i < sizeof($mastervehicle); $i++) {
				$auto_status = $mastervehicle[$i]['location_report_auto_status'];

					if ($auto_status != "M") {
						array_push($datafix, array(
							"vehicle_id"         => $mastervehicle[$i]['location_report_vehicle_id'],
							"vehicle_user_id"    => $mastervehicle[$i]['location_report_vehicle_user_id'],
							"vehicle_device"     => $mastervehicle[$i]['location_report_vehicle_device'],
							"vehicle_no"         => $mastervehicle[$i]['location_report_vehicle_no'],
							"vehicle_name"       => $mastervehicle[$i]['location_report_vehicle_name'],
							"auto_last_lat"      => $mastervehicle[$i]['location_report_latitude'],
							"auto_last_long"     => $mastervehicle[$i]['location_report_longitude'],
							"auto_last_road"     => $mastervehicle[$i]['location_report_jalur'],
							"auto_last_engine"   => $mastervehicle[$i]['location_report_engine'],
							"auto_last_speed"    => $mastervehicle[$i]['location_report_speed'],
							"auto_last_course"   => $mastervehicle[$i]['location_report_direction'],
							"auto_last_position" => $mastervehicle[$i]['location_report_location'],
							"fuel_liter_fix"     => $mastervehicle[$i]['location_report_fuel_liter_fix'],
						));
					}
			}

			// echo "<pre>";
			// var_dump(sizeof($datafix));die();
			// echo "<pre>";

			echo json_encode(array("code" => "200", "msg" => "success", "mapsoption" => $mapsOptions, "data" => $datafix, "alldataforclearmaps" => $forclearmaps));
		}
  }

	function getChildPool(){
		$date              = date("Y-m-d", strtotime($_POST['startdate']));

		$starttime         = $date . " " . date("H:i:s", strtotime("-5 Minutes", strtotime($_POST['starttime'] . ":00")));
    $endtime           = $date . " " . $_POST['starttime'] . ":00";
		$poolparent        = $_POST['poolparent'];
		$contractor 		 	 = $_POST['contractor'];
		$dateforsearch     = $date.' '.$starttime;

		// $date.'-'.$starttime.'-'.$dateforsearch.'-'.$contractor.'-'.$mapsOptions

		$dataFromLocationHour = $this->m_historical->getDataLocationHour("ts_location_hour", $date, $starttime, $endtime, $contractor);

		$masterdatavehicle                = $dataFromLocationHour;
		$masterdatavehiclebycontractor    = $dataFromLocationHour;
		$allStreet                        = $this->m_poipoolmaster->getstreet_now_byparent($poolparent);
		$lasttimecheck 		                = date("d-m-Y H:i:s", strtotime("+1 hour"));
		$dataoutofhauling['outofhauling'] = 0;

		for ($j=0; $j < sizeof($allStreet); $j++) {
			$street_name                = explode(",", $allStreet[$j]['street_name']);
			$street_namefix             = $street_name[0];
			$dataState[$street_namefix] = 0;

			for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				$auto_last_position = explode(",", $masterdatavehicle[$i]['location_report_location']);
				$datalastposition   = $auto_last_position[0];
				$auto_status 		    = $masterdatavehicle[$i]['location_report_auto_status'];

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

		// DATA OUT OF OTHERS -> OUT OF HAULING
		for ($i=0; $i < sizeof($masterdatavehiclebycontractor); $i++) {
			$auto_last_hauling = explode(",", $masterdatavehiclebycontractor[$i]['location_report_hauling']);
			$datalastposition  = $auto_last_hauling[0];

				if ($datalastposition == "out") {
					$dataoutofhauling['outofhauling'] += 1;
				}
		}

		// echo "<pre>";
		// var_dump($dataState);die();
		// echo "<pre>";
		echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataState, "allStreet" => $allStreet, "dataoutofhauling" => $dataoutofhauling, "lastcheck" => $lasttimecheck));
	}

	function vehicleonpool(){
		$allCompany 					 = $this->m_poipoolmaster->getAllCompany();

		$date                  = date("Y-m-d", strtotime($this->input->post('date')));
		$starttime    		     = $date . " " . date("H:i:s", strtotime("-5 Minutes", strtotime($this->input->post('starttime') . ":00")));
    $endtime	             = $date . " " . $this->input->post('starttime') . ":00";
		$contractor            = $this->input->post('contractor');
		$idpool                = $this->input->post('idpoolfix');
		$dateforsearch         = $date.' '.$starttime;

		// $date.'-'.$starttime.'-'.$dateforsearch.'-'.$contractor.'-'.$mapsOptions
		// $date.'-'.$starttime.'-'.$contractor.'-'.$idpool

		$dataFromLocationHour  = $this->m_historical->getDataLocationHourByPool("ts_location_hour", $date, $starttime, $endtime, $contractor, $idpool);

		$masterdatavehicle     = $dataFromLocationHour;
		$dataVehicleOnPool     = array();

		// echo "<pre>";
		// var_dump($dataFromLocationHour);die();
		// echo "<pre>";

			if ($idpool == "1839") {
				$street 					   = $this->dashboardmodel->getstreet_id("double", array("9309", "9401", "9402"));
			}else {
				$street 					   = $this->dashboardmodel->getstreet_id("", $idpool);
			}
		$streetFix 					     = explode(",", $street->street_name);
		$street_alias 					 = explode(",", $street->street_alias);

		// $lasttimecheck          = json_decode($masterdatavehicle[0]['vehicle_autocheck']);

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

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$auto_last_position = explode(",", $masterdatavehicle[$i]['location_report_location']);
			$datalastposition   = $auto_last_position[0];
			$auto_status   			= $masterdatavehicle[$i]['location_report_auto_status'];

			// echo "<pre>";
			// var_dump($autocheck);die();
			// echo "<pre>";

			if ($datalastposition == $streetFix[0]) {
					if ($auto_status != "M") {
						array_push($dataVehicleOnPool, array(
							"vehicle_no"       => $masterdatavehicle[$i]['location_report_vehicle_no'],
							"vehicle_name"     => $masterdatavehicle[$i]['location_report_vehicle_name'],
							"vehicle_company"  => $masterdatavehicle[$i]['location_report_vehicle_company'],
							"auto_last_lat"    => $masterdatavehicle[$i]['location_report_latitude'],
							"auto_last_long"   => $masterdatavehicle[$i]['location_report_longitude'],
							"auto_last_engine" => $masterdatavehicle[$i]['location_report_engine'],
							"auto_last_speed"  => $masterdatavehicle[$i]['location_report_speed'],
							"auto_last_update" => $masterdatavehicle[$i]['location_report_gps_time'],
						));
					}
			}
		}

		for ($k=0; $k < sizeof($dataVehicleOnPool); $k++) {
			for ($j=0; $j < sizeof($allCompany); $j++) {
				if ($dataVehicleOnPool[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
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

		echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $dataVehicleOnPool, "statesent" => $street_alias));
	}

	function getlistoutofhaulingByContractor(){
		$contractor 						 = $_POST['contractor'];
		$date                    = date("Y-m-d", strtotime($_POST['startdate']));
		$starttime    		    	 = $date . " " . date("H:i:s", strtotime("-5 Minutes", strtotime($_POST['starttime'] . ":00")));
    $endtime 	               = $date . " " . $_POST['starttime'] . ":00";
		$allCompany 					   = $this->m_poipoolmaster->getAllCompany();
		$dataContractor['BKAE']  = 0;
		$dataContractor['KMB']   = 0;
		$dataContractor['GECL']  = 0;
		$dataContractor['STLI']  = 0;
		$dataContractor['RAMB']  = 0;
		$dataContractor['BBS']   = 0;
		$dataContractor['MKS']   = 0;
		$dataContractor['RBT']   = 0;
		$dataContractor['MMS']   = 0;
		$dataContractor['EST']   = 0;

		$dataFromLocationHour    = $this->m_historical->getDataLocationHour("ts_location_hour", $date, $starttime, $endtime, $contractor);
		$masterdatavehicle       = $dataFromLocationHour;
		$dataVehicleOutofHauling = array();

		// echo "<pre>";
		// var_dump($date.'-'.$starttime.'-'.$contractor);die();
		// echo "<pre>";

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck             = json_decode($masterdatavehicle[$i]['location_report_location']);
			$auto_last_hauling     = explode(",", $masterdatavehicle[$i]['location_report_hauling']);
			$datalastposition      = $auto_last_hauling[0];
			$auto_last_position    = explode(",", $masterdatavehicle[$i]['location_report_location']);
			$auto_last_positionfix = $auto_last_position[0];

			// echo "<pre>";
			// var_dump($auto_last_positionfix);die();
			// echo "<pre>";

				if ($datalastposition == "out") {
					array_push($dataVehicleOutofHauling, array(
						"vehicle_no"            => $masterdatavehicle[$i]['location_report_vehicle_no'],
						"vehicle_name"          => $masterdatavehicle[$i]['location_report_vehicle_name'],
						"vehicle_company"       => $masterdatavehicle[$i]['location_report_vehicle_company'],
						"auto_last_lat"         => $masterdatavehicle[$i]['location_report_vehicle_company'],
						"auto_last_long"        => $masterdatavehicle[$i]['location_report_latitude'],
						"auto_last_engine"      => $masterdatavehicle[$i]['location_report_longitude'],
						"auto_last_speed"       => $masterdatavehicle[$i]['location_report_speed'],
						"auto_last_positionfix" => $auto_last_positionfix,
						"auto_last_update"      => date("d-m-Y H:i:s", strtotime($masterdatavehicle[$i]['location_report_gps_time']))
					));
				}
		}

		for ($k=0; $k < sizeof($dataVehicleOutofHauling); $k++) {
			for ($j=0; $j < sizeof($allCompany); $j++) {
				if ($dataVehicleOutofHauling[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
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
		echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $dataVehicleOutofHauling));
	}

	function getlistoutofhauling(){
		$allCompany 					 = $this->m_poipoolmaster->getAllCompany();

		$date                  = date("Y-m-d", strtotime($this->input->post('date')));
		$starttime   		    	 = $date . " " . date("H:i:s", strtotime("-5 Minutes", strtotime($this->input->post('starttime') . ":00")));
    $endtime 	             = $date . " " . $this->input->post('starttime') . ":00";
		$contractor            = $this->input->post('contractor');
		$dateforsearch         = $date.' '.$starttime;

		// $date.'-'.$starttime.'-'.$dateforsearch.'-'.$contractor.'-'.$mapsOptions

		$dataFromLocationHour  = $this->m_historical->getDataLocationHour("ts_location_hour", $date, $starttime, $endtime, $contractor);

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

		$masterdatavehicle       = $dataFromLocationHour;
		$dataVehicleOutofHauling = array();

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$auto_last_hauling     = explode(",", $masterdatavehicle[$i]['location_report_hauling']);
			$datalastposition      = $auto_last_hauling[0];
			$auto_last_position    = explode(",", $masterdatavehicle[$i]['location_report_location']);
			$auto_last_positionfix = $auto_last_position[0];

			// echo "<pre>";
			// var_dump($auto_last_positionfix);die();
			// echo "<pre>";

				if ($datalastposition == "out") {
					array_push($dataVehicleOutofHauling, array(
						"vehicle_no"            => $masterdatavehicle[$i]['location_report_vehicle_no'],
						"vehicle_name"          => $masterdatavehicle[$i]['location_report_vehicle_name'],
						"vehicle_company"       => $masterdatavehicle[$i]['location_report_vehicle_company'],
						"auto_last_lat"         => $masterdatavehicle[$i]['location_report_vehicle_company'],
						"auto_last_long"        => $masterdatavehicle[$i]['location_report_latitude'],
						"auto_last_engine"      => $masterdatavehicle[$i]['location_report_longitude'],
						"auto_last_speed"       => $masterdatavehicle[$i]['location_report_speed'],
						"auto_last_positionfix" => $auto_last_positionfix,
						"auto_last_update"      => date("d-m-Y H:i:s", strtotime($masterdatavehicle[$i]['location_report_gps_time']))
					));
				}
		}

		for ($k=0; $k < sizeof($dataVehicleOutofHauling); $k++) {
			for ($j=0; $j < sizeof($allCompany); $j++) {
				if ($dataVehicleOutofHauling[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
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
		echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $dataVehicleOutofHauling));
	}

	function getPoolOther(){
		$date          = date("Y-m-d", strtotime($_POST['startdate']));
		$starttime   	 = $date . " " . date("H:i:s", strtotime("-5 Minutes", strtotime($_POST['starttime'] . ":00")));
    $endtime	     = $date . " " . $_POST['starttime'] . ":00";
		$contractor    = $_POST['companyid'];
		$dateforsearch = $date.' '.$starttime;

		$dataFromLocationHour = $this->m_historical->getDataLocationHour("ts_location_hour", $date, $starttime, $endtime, $contractor);

		$dataforclear                     = $this->m_poipoolmaster->getmastervehicle();
		$masterdatavehicle                = $dataFromLocationHour;
		$poolmaster                       = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$dataoutofhauling['outofhauling'] = 0;
		$dataVehicleOutofHauling          = array();
		$lasttimecheck 		                = date("d-m-Y H:i:s", strtotime("+1 hour"));

			for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				$auto_last_hauling = explode(",", $masterdatavehicle[$i]['location_report_hauling']);
				$datalastposition  = $auto_last_hauling[0];

					if ($datalastposition == "out") {
						$dataoutofhauling['outofhauling'] += 1;

						array_push($dataVehicleOutofHauling, array(
							"vehicle_id"       => $masterdatavehicle[$i]['location_report_vehicle_id'],
							"vehicle_no"       => $masterdatavehicle[$i]['location_report_vehicle_no'],
							"vehicle_name"     => $masterdatavehicle[$i]['location_report_vehicle_name'],
							"vehicle_device"   => $masterdatavehicle[$i]['location_report_vehicle_device'],
							"vehicle_company"  => $masterdatavehicle[$i]['location_report_vehicle_company'],
							"auto_last_lat"    => $masterdatavehicle[$i]['location_report_latitude'],
							"auto_last_long"   => $masterdatavehicle[$i]['location_report_longitude'],
							"auto_last_course" => $masterdatavehicle[$i]['location_report_direction'],
						));
					}
			}

			$getallcompany     = $this->m_poipoolmaster->getAllCompany();

			$datapool  = array();
			for ($i=0; $i < sizeof($getallcompany); $i++) {
				$getChild      = $this->m_poipoolmaster->getStreetByParent($getallcompany[$i]['company_id']);
					for ($j=0; $j < sizeof($getChild); $j++) {
						$child_name = explode(",", $getChild[$j]['street_name']);
						array_push($datapool, array(
							$getallcompany[$i]['company_name'] => $getallcompany[$i]['company_name'].'|'.$child_name[0]
						));
					}
			}

			for ($i=0; $i < sizeof($getallcompany); $i++) {
				$datacompanyid              = $getallcompany[$i]['company_id'];
				$datacompanyname            = $getallcompany[$i]['company_name'];
				$street_namefix             = $datacompanyname;
				$dataState[$street_namefix] = 0;

				for ($k=0; $k < sizeof($dataVehicleOutofHauling); $k++) {
					$vehicle_company = $dataVehicleOutofHauling[$k]['vehicle_company'];
						if ($datacompanyid == $vehicle_company) {
							$dataState[$street_namefix] += 1;
						}
				}
			}

			// echo "<pre>";
			// var_dump($getallcompany);die();
			// echo "<pre>";

			echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataState, "company" => $getallcompany));
	}

	function getlistinkm(){
		$idkmfix 			     = $this->input->post('idkmfix');
		$date              = date("Y-m-d", strtotime($this->input->post('date')));
		$starttime   	 		 = $date . " " . date("H:i:s", strtotime("-5 Minutes", strtotime($this->input->post('starttime') . ":00")));
    $endtime           = $date . " " . $this->input->post('starttime').":00";
    $contractor        = $this->input->post('contractor');
    $mapsOptions       = $this->input->post('mapsOptions');
		$dateforsearch     = $date.' '.$starttime;

		// $date.'-'.$starttime.'-'.$dateforsearch.'-'.$contractor.'-'.$mapsOptions

		$dataFromLocationHour = $this->m_historical->getDataLocationHour("ts_location_hour", $date, $starttime, $endtime, $contractor);

		// echo "<pre>";
		// var_dump($dataFromLocationHour);die();
		// echo "<pre>";

		$dataVehicleOnKosongan = array();
		$dataVehicleOnMuatan   = array();
		$idkm 							   = $this->input->post('idkmfix');
		$kmonsearch 					 = array();

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

		for ($i=0; $i < sizeof($dataFromLocationHour); $i++) {
			$datalastposition = $dataFromLocationHour[$i]['location_report_location'];
			$jalur_name       = $dataFromLocationHour[$i]['location_report_jalur'];

			// echo "<pre>";
			// var_dump($datalastposition);die();
			// echo "<pre>";

				if (in_array($datalastposition, $kmonsearch)) {
					if ($jalur_name == "kosongan") {
						array_push($dataVehicleOnKosongan, array(
							"vehicle_no"            => $dataFromLocationHour[$i]['location_report_vehicle_no'],
							"vehicle_name"          => $dataFromLocationHour[$i]['location_report_vehicle_name'],
							"auto_last_lat"         => $dataFromLocationHour[$i]['location_report_latitude'],
							"auto_last_long"        => $dataFromLocationHour[$i]['location_report_longitude'],
							"auto_last_positionfix" => $datalastposition,
							"auto_last_engine"      => $dataFromLocationHour[$i]['location_report_engine'],
							"auto_last_speed"       => $dataFromLocationHour[$i]['location_report_speed'],
							"auto_last_update"      => date("d-m-Y H:i:s", strtotime($dataFromLocationHour[$i]['location_report_gps_time']))
						));
					}else {
						array_push($dataVehicleOnMuatan, array(
							"vehicle_no"            => $dataFromLocationHour[$i]['location_report_vehicle_no'],
							"vehicle_name"          => $dataFromLocationHour[$i]['location_report_vehicle_name'],
							"auto_last_lat"         => $dataFromLocationHour[$i]['location_report_latitude'],
							"auto_last_long"        => $dataFromLocationHour[$i]['location_report_longitude'],
							"auto_last_positionfix" => $datalastposition,
							"auto_last_engine"      => $dataFromLocationHour[$i]['location_report_engine'],
							"auto_last_speed"       => $dataFromLocationHour[$i]['location_report_speed'],
							"auto_last_update"      => date("d-m-Y H:i:s", strtotime($dataFromLocationHour[$i]['location_report_gps_time']))
						));
					}
				}
		}

		// echo "<pre>";
		// var_dump($dataVehicleOnKosongan);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "code" => 200, "dataKosongan" => $dataVehicleOnKosongan, "dataMuatan" => $dataVehicleOnMuatan, "kmsent" => $vCompanyFix));
	}

	function getlistinrom(){
		$allCompany 					  = $this->m_poipoolmaster->getAllCompany();
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

		$idrom 			       = $this->input->post('idromfix');
		$date              = date("Y-m-d", strtotime($this->input->post('date')));
		$starttime   		   = $date . " " . date("H:i:s", strtotime("-5 Minutes", strtotime($this->input->post('starttime') . ":00")));
		$endtime 	         = $date . " " . $this->input->post('starttime').":00";
		$contractor        = $this->input->post('contractor');
		$mapsOptions       = $this->input->post('mapsOptions');
		$dateforsearch     = $date.' '.$starttime;

		// $date.'-'.$starttime.'-'.$dateforsearch.'-'.$contractor.'-'.$mapsOptions

		$dataFromLocationHour = $this->m_historical->getDataLocationHour("ts_location_hour", $date, $starttime, $endtime, $contractor);
		$dataVehicleOnRom     = array();

		$vCompany 					  = $this->dashboardmodel->getstreet_id("", $idrom);
		$streetFix 					  = explode(",", $vCompany->street_name);

		for ($i=0; $i < sizeof($dataFromLocationHour); $i++) {
			$datalastposition   = $dataFromLocationHour[$i]['location_report_location'];

			// echo "<pre>";
			// var_dump($autocheck);die();
			// echo "<pre>";

				if ($datalastposition == $streetFix[0]) {
					array_push($dataVehicleOnRom, array(
						"vehicle_no"            => $dataFromLocationHour[$i]['location_report_vehicle_no'],
						"vehicle_name"          => $dataFromLocationHour[$i]['location_report_vehicle_name'],
						"vehicle_company"       => $dataFromLocationHour[$i]['location_report_vehicle_company'],
						"auto_last_lat"         => $dataFromLocationHour[$i]['location_report_latitude'],
						"auto_last_long"        => $dataFromLocationHour[$i]['location_report_longitude'],
						"auto_last_positionfix" => $datalastposition,
						"auto_last_engine"      => $dataFromLocationHour[$i]['location_report_engine'],
						"auto_last_speed"       => $dataFromLocationHour[$i]['location_report_speed'],
						"auto_last_update"      => date("d-m-Y H:i:s", strtotime($dataFromLocationHour[$i]['location_report_gps_time']))
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

	function getlistinport(){
		$allCompany 					  = $this->m_poipoolmaster->getAllCompany();
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

		$idportfix 			 = $_POST['idportfix'];
		$date            = date("Y-m-d", strtotime($_POST['startdate']));
		$starttime   		 = $date . " " . date("H:i:s", strtotime("-5 Minutes", strtotime($_POST['starttime'] . ":00")));
		$endtime 	       = $date . " " . $_POST['starttime'].":00";
		$contractor      = $_POST['contractor'];
		$mapsOptions     = $_POST['mapsOptions'];
		$dateforsearch   = $date.' '.$starttime;

		// $date.'-'.$starttime.'-'.$dateforsearch.'-'.$contractor.'-'.$mapsOptions

		$dataFromLocationHour = $this->m_historical->getDataLocationHour("ts_location_hour", $date, $starttime, $endtime, $contractor);

		$dataVehicleOnPort    = array();

		$vCompany 					     = $this->dashboardmodel->getstreet_id("", $idportfix);
		$streetFix 					     = explode(",", $vCompany->street_name);
		$street_alias 					 = explode(",", $vCompany->street_alias);

		for ($i=0; $i < sizeof($dataFromLocationHour); $i++) {
			$datalastposition   = $dataFromLocationHour[$i]['location_report_location'];

			// echo "<pre>";
			// var_dump($autocheck);die();
			// echo "<pre>";

				if ($datalastposition == $streetFix[0]) {
					array_push($dataVehicleOnPort, array(
						"vehicle_no"            => $dataFromLocationHour[$i]['location_report_vehicle_no'],
						"vehicle_name"          => $dataFromLocationHour[$i]['location_report_vehicle_name'],
						"vehicle_company"       => $dataFromLocationHour[$i]['location_report_vehicle_company'],
						"auto_last_lat"         => $dataFromLocationHour[$i]['location_report_latitude'],
						"auto_last_long"        => $dataFromLocationHour[$i]['location_report_longitude'],
						"auto_last_positionfix" => $datalastposition,
						"auto_last_engine"      => $dataFromLocationHour[$i]['location_report_engine'],
						"auto_last_speed"       => $dataFromLocationHour[$i]['location_report_speed'],
						"auto_last_update"      => date("d-m-Y H:i:s", strtotime($dataFromLocationHour[$i]['location_report_gps_time']))
					));
				}
		}

		for ($k=0; $k < sizeof($dataVehicleOnPort); $k++) {
			for ($j=0; $j < sizeof($allCompany); $j++) {
				if ($dataVehicleOnPort[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
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
		echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $dataVehicleOnPort, "portsent" => $street_alias));
	}





























}
