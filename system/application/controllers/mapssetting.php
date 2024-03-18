<?php
include "base.php";

class Mapssetting extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
    $this->load->model("m_poipoolmaster");
		$this->load->model("log_model");
		$this->load->helper('common');
	}

  // USER FUNCTION START
  function index()
	{
		if (!isset($this->sess->user_type))
		{
			redirect(base_url());
		}

    $privilegecode = $this->sess->user_id_role;

		$this->params['mapsetting_rom']    = $this->m_poipoolmaster->getmapsetting("ROM");
    $this->params['mapsetting_port']   = $this->m_poipoolmaster->getmapsetting("PORT");
    $this->params['mapsetting_poolws'] = $this->m_poipoolmaster->getmapsetting("POOL_WS");
		$this->params['code_view_menu']  = "configuration";


    // echo "<pre>";
    // var_dump($this->params['mapsetting']);die();
    // echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/mapssetting/v_mapssetting', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/mapssetting/v_mapssetting', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/mapssetting/v_mapssetting', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/mapssetting/v_mapssetting', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}


  function searchbytype(){
    $mapsoptionvalue = $_POST['mapsoptionvalue'];
      if ($mapsoptionvalue == "mapssettinginKM1Muatan") {
        $street_type = 1;
      }elseif ($mapsoptionvalue == "mapssettinginAllKMKosongan") {
        $street_type = 1;
      }elseif ($mapsoptionvalue == "mapssettinginAllKMMuatan") {
        $street_type = 1;
      }elseif ($mapsoptionvalue == "mapssettinginrom") {
        $street_type = 3;
      }elseif ($mapsoptionvalue == "mapssettinginport") {
        $street_type = 4;
      }elseif ($mapsoptionvalue == "mapssettingintiakosongan") {
				$street_type = 9;
      }elseif ($mapsoptionvalue == "mapssettingintiamuatan") {
				$street_type = 9;
      }

			$arraynotin 							= array("Port BIR - Kosongan 2", "Port BIB - Kosongan 2", "Simpang Bayah - Kosongan", "Port BIR - Antrian WB", "Port BIB - Kosongan 1",
																				"Port BIB - Antrian", "Port BIR - Kosongan 1", "KM 1");
			$arraynotinAllKMKosongan  = array("Port BIR - Kosongan 2", "Port BIB - Kosongan 2", "Simpang Bayah - Kosongan", "Port BIR - Antrian WB", "Port BIB - Kosongan 1",
																				"Port BIB - Antrian", "Port BIR - Kosongan 1");
			$arrayinKM1Muatan	 			  = array("KM 1");

      $getdataFromStreet = $this->m_poipoolmaster->getstreet_now_mapsetting($street_type);
			$mapSettingType    = $this->m_poipoolmaster->getMapSettingByType_mapsetting($street_type);

				// echo "<pre>";
				// var_dump($getdataFromStreet);die();
				// echo "<pre>";
				$postfix_bottom_limit = "_bottom_limit";
				$postfix_middle_limit = "_middle_limit";
				$postfix_top_limit    = "_top_limit";

				if ($mapsoptionvalue == "mapssettinginAllKMMuatan") {
					if (isset($getdataFromStreet)) {
						$datafix = array();
						for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
							$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
								if (!in_array($streetremovecoma[0], $arraynotin)) {
									$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);
									$middlelimitname          = $streetfix.$postfix_middle_limit."_allkmmuatan";
									$toplimitname             = $streetfix.$postfix_top_limit."_allkmmuatan";
									$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName_mapsetting($middlelimitname, $toplimitname);

									if (sizeof($getMapSettingByLimitName) > 1) {
											array_push($datafix, array(
												"street_id"               => $getdataFromStreet[$i]['street_id'],
												"street_name"             => $getdataFromStreet[$i]['street_name'],
												"mapsetting_type"         => $street_type,
												"mapsetting_name_alias"   => $streetremovecoma[0],
												"mapsetting_name"         => $streetfix,
												"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
												"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
											));
									}else {
										array_push($datafix, array(
											"street_id"               => $getdataFromStreet[$i]['street_id'],
											"street_name"             => $getdataFromStreet[$i]['street_name'],
											"mapsetting_type"         => $street_type,
											"mapsetting_name_alias"   => $streetremovecoma[0],
											"mapsetting_name"         => $streetfix,
											"mapsetting_middle_limit" => 0,
											"mapsetting_top_limit"    => 0
										));
									}
								}
						}
					}
				}elseif ($mapsoptionvalue == "mapssettinginAllKMKosongan") {
					if (isset($getdataFromStreet)) {
						$datafix = array();
						for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
							$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
								if (!in_array($streetremovecoma[0], $arraynotinAllKMKosongan)) {
									$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);
									$middlelimitname          = $streetfix.$postfix_middle_limit."_allkmkosongan";
									$toplimitname             = $streetfix.$postfix_top_limit."_allkmkosongan";
									$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName_mapsetting($middlelimitname, $toplimitname);

									if (sizeof($getMapSettingByLimitName) > 1) {
											array_push($datafix, array(
												"street_id"               => $getdataFromStreet[$i]['street_id'],
												"street_name"             => $getdataFromStreet[$i]['street_name'],
												"mapsetting_type"         => $street_type,
												"mapsetting_name_alias"   => $streetremovecoma[0],
												"mapsetting_name"         => $streetfix,
												"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
												"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
											));
									}else {
										array_push($datafix, array(
											"street_id"               => $getdataFromStreet[$i]['street_id'],
											"street_name"             => $getdataFromStreet[$i]['street_name'],
											"mapsetting_type"         => $street_type,
											"mapsetting_name_alias"   => $streetremovecoma[0],
											"mapsetting_name"         => $streetfix,
											"mapsetting_middle_limit" => 0,
											"mapsetting_top_limit"    => 0
										));
									}
								}
						}
					}
				}elseif ($mapsoptionvalue == "mapssettinginKM1Muatan") {
					if (isset($getdataFromStreet)) {
						$datafix = array();
						for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
							$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
								if (in_array($streetremovecoma[0], $arrayinKM1Muatan)) {
									$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);
									$bottomlimitname          = $streetfix.$postfix_bottom_limit."_km1muatan";
									$middlelimitname          = $streetfix.$postfix_middle_limit."_km1muatan";
									$toplimitname             = $streetfix.$postfix_top_limit."_km1muatan";
									$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName_mapsetting_onlykm1($bottomlimitname, $middlelimitname, $toplimitname);

									// echo "<pre>";
									// var_dump($getMapSettingByLimitName);die();
									// echo "<pre>";

									if (sizeof($getMapSettingByLimitName) > 1) {
											array_push($datafix, array(
												"street_id"               => $getdataFromStreet[$i]['street_id'],
												"street_name"             => $getdataFromStreet[$i]['street_name'],
												"mapsetting_type"         => $street_type,
												"mapsetting_name_alias"   => $streetremovecoma[0],
												"mapsetting_name"         => $streetfix,
												"mapsetting_bottom_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
												"mapsetting_middle_limit" => $getMapSettingByLimitName[1]['mapsetting_limit_value'],
												"mapsetting_top_limit"    => $getMapSettingByLimitName[2]['mapsetting_limit_value']
											));
									}else {
										array_push($datafix, array(
											"street_id"               => $getdataFromStreet[$i]['street_id'],
											"street_name"             => $getdataFromStreet[$i]['street_name'],
											"mapsetting_type"         => $street_type,
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
				}elseif ($mapsoptionvalue == "mapssettingintiakosongan") {
					if (isset($getdataFromStreet)) {
						$datafix = array();
						for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
							$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
								if (!in_array($streetremovecoma[0], $arraynotinAllKMKosongan)) {
									$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);
									$middlelimitname          = $streetfix.$postfix_middle_limit."_alltiakmkosongan";
									$toplimitname             = $streetfix.$postfix_top_limit."_alltiakmkosongan";
									$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName_mapsetting($middlelimitname, $toplimitname);

									if (sizeof($getMapSettingByLimitName) > 1) {
											array_push($datafix, array(
												"street_id"               => $getdataFromStreet[$i]['street_id'],
												"street_name"             => $getdataFromStreet[$i]['street_name'],
												"mapsetting_type"         => $street_type,
												"mapsetting_name_alias"   => $streetremovecoma[0],
												"mapsetting_name"         => $streetfix,
												"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
												"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
											));
									}else {
										array_push($datafix, array(
											"street_id"               => $getdataFromStreet[$i]['street_id'],
											"street_name"             => $getdataFromStreet[$i]['street_name'],
											"mapsetting_type"         => $street_type,
											"mapsetting_name_alias"   => $streetremovecoma[0],
											"mapsetting_name"         => $streetfix,
											"mapsetting_middle_limit" => 0,
											"mapsetting_top_limit"    => 0
										));
									}
								}
						}
					}
				}elseif ($mapsoptionvalue == "mapssettingintiamuatan") {
					if (isset($getdataFromStreet)) {
						$datafix = array();
						for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
							$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
								if (!in_array($streetremovecoma[0], $arraynotin)) {
									$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);
									$middlelimitname          = $streetfix.$postfix_middle_limit."_alltiakmmuatan";
									$toplimitname             = $streetfix.$postfix_top_limit."_alltiakmmuatan";
									$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName_mapsetting($middlelimitname, $toplimitname);

									if (sizeof($getMapSettingByLimitName) > 1) {
											array_push($datafix, array(
												"street_id"               => $getdataFromStreet[$i]['street_id'],
												"street_name"             => $getdataFromStreet[$i]['street_name'],
												"mapsetting_type"         => $street_type,
												"mapsetting_name_alias"   => $streetremovecoma[0],
												"mapsetting_name"         => $streetfix,
												"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
												"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
											));
									}else {
										array_push($datafix, array(
											"street_id"               => $getdataFromStreet[$i]['street_id'],
											"street_name"             => $getdataFromStreet[$i]['street_name'],
											"mapsetting_type"         => $street_type,
											"mapsetting_name_alias"   => $streetremovecoma[0],
											"mapsetting_name"         => $streetfix,
											"mapsetting_middle_limit" => 0,
											"mapsetting_top_limit"    => 0
										));
									}
								}
						}
					}
				}else {
					if (isset($getdataFromStreet)) {
						$datafix = array();
						for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
							$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
								if (!in_array($streetremovecoma[0], $arraynotin)) {
									$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);
									$middlelimitname          = $streetfix.$postfix_middle_limit;
									$toplimitname             = $streetfix.$postfix_top_limit;
									$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName_mapsetting($middlelimitname, $toplimitname);

									if (sizeof($getMapSettingByLimitName) > 1) {
											array_push($datafix, array(
												"street_id"               => $getdataFromStreet[$i]['street_id'],
												"street_name"             => $getdataFromStreet[$i]['street_name'],
												"mapsetting_type"         => $street_type,
												"mapsetting_name_alias"   => $streetremovecoma[0],
												"mapsetting_name"         => $streetfix,
												"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
												"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
											));
									}else {
										array_push($datafix, array(
											"street_id"               => $getdataFromStreet[$i]['street_id'],
											"street_name"             => $getdataFromStreet[$i]['street_name'],
											"mapsetting_type"         => $street_type,
											"mapsetting_name_alias"   => $streetremovecoma[0],
											"mapsetting_name"         => $streetfix,
											"mapsetting_middle_limit" => 0,
											"mapsetting_top_limit"    => 0
										));
									}
								}
						}
					}
				}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

    echo json_encode(array("code" => 200, "msg" => "success", "street_type" => $street_type, "data" => $datafix, "mapsoptionvalue" => $mapsoptionvalue));
  }

  function savethismapsetting(){
		$json            = file_get_contents('php://input');
		$dataarray       = json_decode($json, true);
		$mapsetting_type = $dataarray[0]['value'];
		$valueMapsOption = $dataarray[1]['value'];

		// echo "<pre>";
		// var_dump($dataarray);die();
		// echo "<pre>";

      if ($mapsetting_type == "1") { // street_type KM
        $street_type = 1;
      }elseif ($mapsetting_type == "3") { // street_type ROM
        $street_type = 3;
      }elseif ($mapsetting_type == "4") { // street_type PORT
        $street_type = 4;
      }elseif ($mapsetting_type == "9") { // street_type PORT
        $street_type = 9;
      }

			if ($valueMapsOption == "mapssettinginAllKMMuatan") {
				$postfix_middle_limit = "_middle_limit_allkmmuatan";
				$postfix_top_limit    = "_top_limit_allkmmuatan";
			}elseif ($valueMapsOption == "mapssettinginAllKMKosongan") {
				$postfix_middle_limit = "_middle_limit_allkmkosongan";
				$postfix_top_limit    = "_top_limit_allkmkosongan";
			}elseif ($valueMapsOption == "mapssettinginKM1Muatan") {
				$postfix_bottom_limit = "_bottom_limit_km1muatan";
				$postfix_middle_limit = "_middle_limit_km1muatan";
				$postfix_top_limit    = "_top_limit_km1muatan";
			}else {
				$postfix_middle_limit = "_middle_limit";
				$postfix_top_limit    = "_top_limit";
			}

			$datafix = array();
			for ($i=2; $i < sizeof($dataarray); $i++) {
				$mapsetting_name        = explode("_", $dataarray[$i]['name']);
				$datamapsetting_namefix = $mapsetting_name[0].'_'.$mapsetting_name[1];
					if ($datamapsetting_namefix.$postfix_middle_limit == $dataarray[$i]['name']) {
						array_push($datafix, array(
							"mapsetting_type"        => $street_type,
							"mapsetting_name"        => $datamapsetting_namefix,
							"mapsetting_limit_name"  => $dataarray[$i]['name'],
							"mapsetting_limit_value" => $dataarray[$i]['value'],
						));
					}else {
						array_push($datafix, array(
							"mapsetting_type"        => $street_type,
							"mapsetting_name"        => $datamapsetting_namefix,
							"mapsetting_limit_name"  => $dataarray[$i]['name'],
							"mapsetting_limit_value" => $dataarray[$i]['value'],
						));
					}
			}

			for ($i=0; $i < sizeof($datafix); $i++) {
				$mapsetting_name       = $datafix[$i]['mapsetting_name'];
				$mapsetting_limit_name = $datafix[$i]['mapsetting_limit_name'];
					$check               = $this->m_poipoolmaster->checkThisMapSetting($mapsetting_name, $mapsetting_limit_name);
						if ($check) {
							// UPDATE
							$dataupdate = array(
								"mapsetting_limit_value" => $datafix[$i]['mapsetting_limit_value']
							);

							$update = $this->m_poipoolmaster->update_data_webtracking_ts("ts_mapsetting", "mapsetting_limit_name", $datafix[$i]['mapsetting_limit_name'], $dataupdate);
						}else {
							// INSERT
							$datainsert = array(
								"mapsetting_parent_id"   => $this->sess->user_parent,
								"mapsetting_user_id"     => $this->sess->user_id,
								"mapsetting_type"        => $datafix[$i]['mapsetting_type'],
								"mapsetting_name"        => $datafix[$i]['mapsetting_name'],
								"mapsetting_limit_name"  => $datafix[$i]['mapsetting_limit_name'],
								"mapsetting_limit_value" => $datafix[$i]['mapsetting_limit_value']
							);

							$insert = $this->m_poipoolmaster->insert_data_webtracking_ts("ts_mapsetting", $datainsert);
						}
			}

			// $dataarray;
			// $datafix;
      // echo "<pre>";
      // var_dump("DONE");die();
      // echo "<pre>";
			echo json_encode(array("code" => 200, "msg" => "success"));
  }

}
