<?php
include "base.php";

class Masterdata extends Base {

	function Masterdata()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->model("m_poipoolmaster");
		$this->load->helper('common');
		$this->load->model("driver_model");
		$this->load->model("m_masterdata");
		$this->load->model("log_model");

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	}

	function mdvrlevel(){
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

		$useridbib       = 4408;
		$privilegecode 	 = $this->sess->user_id_role;

		$master_speed_level = $this->m_masterdata->getallmdvrlevel();

    $this->params['code_view_menu']          = "masterdata";
    $this->params['speedlevel'] 					   = $master_speed_level;
		$this->params['privilegecode'] 					 = $privilegecode;

		$this->params["header"]                  = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]             = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/mdvrlevel/v_home_mdvrlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/mdvrlevel/v_home_mdvrlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/mdvrlevel/v_home_mdvrlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/mdvrlevel/v_home_mdvrlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/mdvrlevel/v_home_mdvrlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

	function speedlevel(){
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

		$useridbib       = 4408;
		$privilegecode 	 = $this->sess->user_id_role;

		$master_speed_level = $this->m_masterdata->getallspeedlevel();

    $this->params['code_view_menu']          = "masterdata";
    $this->params['speedlevel'] 					   = $master_speed_level;
		$this->params['privilegecode'] 					 = $privilegecode;

		$this->params["header"]                  = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]             = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_home_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_home_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_home_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_home_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_home_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

	function addspeedlevel(){
    $this->params['code_view_menu'] = "masterdata";
    $this->params['title']          = "Master Data Material";
    // $this->params['data'] 					= $datafix;
    // $this->params['rcompany'] 			= $rows_company;
		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_add_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_add_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_add_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_add_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_add_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

	function speedlevel_save(){
		$level_name          = $this->input->post("level_name");
		$level_alias         = $this->input->post("level_alias");
		$level_value         = $this->input->post("level_value");
		$level_value_min     = $this->input->post("level_value_min");
		$level_value_max     = $this->input->post("level_value_max");
		$level_sanksi_lubang = $this->input->post("level_sanksi_lubang");
		$level_sanksi_skors  = $this->input->post("level_sanksi_skors");


		if ($level_name == "" || $level_alias == "" || $level_value == "" || $level_value_min == "" || $level_value_max == "" || $level_sanksi_lubang == "" || $level_sanksi_skors == "") {
			$error               = "Harap isi semua kolom dengan benar";
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
		}else {
			$cekSpeedlevel       = $this->m_masterdata->cekSpeedLevelByName("ts_speed_level", "level_name", $level_name);

			// echo "<pre>";
			// var_dump($cekSpeedlevel);die();
			// echo "<pre>";

				if (sizeof($cekSpeedlevel) > 0) {
					$error               = "Data Overspeed Level is Available";
					$callback['error']   = true;
					$callback['message'] = $error;

					echo json_encode($callback);
				}else {
					$data = array(
						"level_user" 			    => $this->sess->user_parent,
						"level_type" 			    => 1,
						"level_name"          => $level_name,
						"level_alias"         => $level_alias,
						"level_value"         => $level_value,
						"level_value_min"     => $level_value_min,
						"level_value_max"     => $level_value_max,
						"level_sanksi_lubang" => $level_sanksi_lubang,
						"level_sanksi_skors"  => $level_sanksi_skors,
					);

					// echo "<pre>";
					// var_dump($data);die();
					// echo "<pre>";

					$insert = $this->m_masterdata->insertDatadbts("ts_speed_level", $data);
						if ($insert) {
							$error               = "Successfuly Save Data Material";
							$callback['error']   = false;
							$callback['message'] = $error;

							echo json_encode($callback);
						}else {
							$error               = "Failed Save Data Material";
							$callback['error']   = true;
							$callback['message'] = $error;

							echo json_encode($callback);
						}
				}
				$contentlog = "Master Data Overspeed Level Successfuly Inserted";
				$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
		}
	}

	function editspeedLevel($level_id){
		$getdataovspeedlevel = $this->m_masterdata->getOvSpeedLevel("ts_speed_level", $level_id);
		// echo "<pre>";
		// var_dump($getdataovspeedlevel);die();
		// echo "<pre>";
		$this->params['code_view_menu']        = "masterdata";
		$this->params['title']                 = "Edit Data Overspesd Level";
		$this->params['data_ovspeedlevel'] 		 = $getdataovspeedlevel;
		// $this->params['rcompany'] 			     = $rows_company;

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_edit_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_edit_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_edit_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_edit_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/overspeedlevel/v_edit_ovspeedlevel', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function ovspeedlevel_update(){
		$level_id_update     = $this->input->post("level_id_update");
		$level_name          = $this->input->post("level_name");
		$level_alias         = $this->input->post("level_alias");
		$level_value         = $this->input->post("level_value");
		$level_value_min     = $this->input->post("level_value_min");
		$level_value_max     = $this->input->post("level_value_max");
		$level_sanksi_lubang = $this->input->post("level_sanksi_lubang");
		$level_sanksi_skors  = $this->input->post("level_sanksi_skors");

		if ($level_name == "" || $level_alias == "" || $level_value == "" || $level_value_min == "" || $level_value_max == "" || $level_sanksi_lubang == "" || $level_sanksi_skors == "") {
			$error               = "Harap isi semua kolom dengan benar";
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
		}else {
			$data = array(
				"level_name"          => $level_name,
				"level_alias"         => $level_alias,
				"level_value"         => $level_value,
				"level_value_min"     => $level_value_min,
				"level_value_max"     => $level_value_max,
				"level_sanksi_lubang" => $level_sanksi_lubang,
				"level_sanksi_skors"  => $level_sanksi_skors,
			);

			// echo "<pre>";
			// var_dump($data);die();
			// echo "<pre>";

			$update = $this->m_masterdata->updateDatadbts("ts_speed_level", "level_id", $level_id_update, $data);
				if ($update) {
					$error               = "Successfuly Update Data Overspeed Level";
					$callback['error']   = false;
					$callback['message'] = $error;

					echo json_encode($callback);
				}else {
					$error               = "Failed Update Data Overspeed Level";
					$callback['error']   = true;
					$callback['message'] = $error;

					echo json_encode($callback);
				}
				$contentlog = "Master Data Overspeed Level Successfuly Updated";
				$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "EDIT", "FUNCTIONAL");
		}
	}

	function deletespeedLevel($levelid){
		$data = array(
			"level_flag" => 1
		);
		$update = $this->m_masterdata->updateDatadbts("ts_speed_level", "level_id", $levelid, $data);
			if ($update) {
				redirect(base_url()."masterdata/speedlevel");
			}else {
				redirect(base_url()."masterdata/speedlevel");
			}
			$contentlog = "Master Data Overspeed Level Successfuly Deleted";
			$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "DELETE", "FUNCTIONAL");
	}

	function driveritws(){
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

		$alldriveritws = $this->m_masterdata->alldriveritws();

	  $this->params["datadriveritws"]	 = $alldriveritws;
		$this->params["privilegecode"]	 = $privilegecode;

	  //$this->params["data"] 		    = $result;
	  $this->params['code_view_menu'] = "masterdata";

	  $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	  $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_driveritws', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    }elseif ($privilegecode == 2) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_driveritws', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    }elseif ($privilegecode == 3) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_driveritws', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    }elseif ($privilegecode == 4) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_driveritws', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    }elseif ($privilegecode == 7) {
	    $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
	    $this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_driveritws', $this->params, true);
	    $this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
	  }elseif ($privilegecode == 8) {
	    $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
	    $this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_driveritws', $this->params, true);
	    $this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
	  }else {
	    $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	    $this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_driveritws', $this->params, true);
	    $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	  }
	}

  function dumping(){
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

		$user_id                                 = $this->sess->user_id;
		$user_parent                             = $this->sess->user_parent;
		$privilegecode                           = $this->sess->user_id_role;

		$data_dumping = $this->m_masterdata->getAllDumping("master_dumping");

		$this->params['code_view_menu']          = "masterdata";
    $this->params['title']                   = "Master Data Dumping";
		$this->params['data_dumping'] 					 = $data_dumping;
		$this->params['privilegecode'] 					 = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}elseif ($privilegecode == 8) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

  function addDumping(){
		// $data_geofence = $this->m_masterdata->getAllGeofence("geofence", $this->sess->user_id);
		//
		// echo "<pre>";
		// var_dump($this->sess->user_id);die();
		// echo "<pre>";

    $this->params['code_view_menu'] = "masterdata";
    $this->params['title']          = "Master Data Dumping";
    // $this->params['data'] 					= $datafix;
    // $this->params['rcompany'] 			= $rows_company;

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

	function dumping_save(){
		// $add_id_dumping          = $this->input->post("add_id_dumping");
		$add_dumping             = $this->input->post("add_dumping");
		$add_reg_date            = $this->input->post("add_reg_date");
		$add_dumping_description = $this->input->post("add_dumping_description");

		if ($add_dumping == "" || $add_reg_date == "" || $add_dumping_description == "") {
			$error               = "Harap isi semua kolom dengan benar";
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
		}else {
			$cekDumping        = $this->m_masterdata->cekDumpingByName("master_dumping", $add_dumping);
			$totaldatadumping = $this->m_masterdata->totalmasterdumping("master_dumping");
			$shortcut          = ($totaldatadumping[0]['dumping_no'] + 1);

				if (sizeof($cekDumping) > 0) {
					$error               = "Data Dumping is Available";
					$callback['error']   = true;
					$callback['message'] = $error;

					echo json_encode($callback);
				}else {
					$data = array(
						"dumping_parent_id"    => $this->sess->user_parent,
						"dumping_user_id"      => $this->sess->user_id,
						"dumping_id"           => $add_dumping,
						"dumping_name"         => $add_dumping,
						"dumping_shortcut"     => $shortcut,
						"dumping_reg_date"     => $add_reg_date,
						"dumping_description"  => $add_dumping_description,
						"dumping_created_date" => date("Y-m-d H:i:s")
					);

					// echo "<pre>";
					// var_dump($data);die();
					// echo "<pre>";

					$insert = $this->m_masterdata->insertData("master_dumping", $data);
						if ($insert) {
							$error               = "Successfuly Save Data Dumping";
							$callback['error']   = false;
							$callback['message'] = $error;

							echo json_encode($callback);
						}else {
							$error               = "Failed Save Data Dumping";
							$callback['error']   = true;
							$callback['message'] = $error;

							echo json_encode($callback);
						}
				}
				$contentlog = "Master Data Dumping Successfuly Inserted";
				$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
		}
	}

	function editDumping($dumping_no){
		$getDataDumping                          = $this->m_masterdata->getDumpingByID("master_dumping", $dumping_no);
		// echo "<pre>";
		// var_dump($getDataDumping);die();
		// echo "<pre>";
		$this->params['code_view_menu']          = "masterdata";
		$this->params['title']                   = "Edit Data Dumping";
		$this->params['data_dumping'] 					 = $getDataDumping;
		// $this->params['rcompany'] 			     = $rows_company;

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_dumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function dumping_update(){
		$edit_dumping_no          = $this->input->post("edit_dumping_no");
		// $edit_dumping_id_old      = $this->input->post("edit_dumping_id_old");
		// $edit_dumping_id          = $this->input->post("edit_dumping_id");
		// $edit_dumping_name_old    = strtoupper($this->input->post("edit_dumping_name_old"));
		$edit_dumping_name        = $this->input->post("edit_dumping_name");
		$edit_dumping_geofence    = $this->input->post("edit_dumping_geofence");
		$edit_dumping_reg_date    = $this->input->post("edit_dumping_reg_date");
		$edit_dumping_description = $this->input->post("edit_dumping_description");

		if ($edit_dumping_name == "" || $edit_dumping_reg_date == "" || $edit_dumping_description == "") {
			$error               = "Harap isi semua kolom dengan benar";
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
		}else {
			$data = array(
				"dumping_id"          => $edit_dumping_name,
				"dumping_name"        => $edit_dumping_name,
				"dumping_geofence"    => $edit_dumping_geofence,
				"dumping_reg_date"    => $edit_dumping_reg_date,
				"dumping_description" => $edit_dumping_description
			);

			// echo "<pre>";
			// var_dump($data);die();
			// echo "<pre>";

			$update = $this->m_masterdata->updateData("master_dumping", "dumping_no", $edit_dumping_no, $data);
				if ($update) {
					$error               = "Successfuly Update Data Dumping";
					$callback['error']   = false;
					$callback['message'] = $error;

					echo json_encode($callback);
				}else {
					$error               = "Failed Update Data Dumping";
					$callback['error']   = true;
					$callback['message'] = $error;

					echo json_encode($callback);
				}
				$contentlog = "Master Data Dumping Successfuly Updated";
				$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "EDIT", "FUNCTIONAL");
		}
	}

	function deleteDumping($dumping_no){
		$data = array(
			"dumping_flag" => 1
		);
		$update = $this->m_masterdata->updateData("master_dumping", "dumping_no", $dumping_no, $data);
			if ($update) {
				redirect(base_url()."masterdata/dumping");
			}else {
				redirect(base_url()."masterdata/dumping");
			}
			$contentlog = "Master Data Dumping Successfuly Deleted";
			$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "DELETE", "FUNCTIONAL");
	}

  function material(){
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;

		$data_material = $this->m_masterdata->getAllMaterial("master_material");

		$streetRom     = $this->m_masterdata->getstreet_now(3);

		$data_rom = array();
		for ($i=0; $i < sizeof($streetRom); $i++) {
			if ($streetRom[$i]['street_type'] == 3) {
				array_push($data_rom, array(
					"street_id"   => $streetRom[$i]['street_id'],
					"street_name" => str_replace(",", "", $streetRom[$i]['street_name']),
				));
			}
		}

		$this->params["data_rom"]			           = $data_rom;
    $this->params['code_view_menu']          = "masterdata";
    $this->params['title']                   = "Master Data Material";
    $this->params['data_material'] 					 = $data_material;
		$this->params['privilegecode'] 					 = $privilegecode;

		$this->params["header"]                  = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]             = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}elseif ($privilegecode == 8) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

	function romtomaterial(){
		$material_no       = $this->input->post("material_no");
		$select_rom        = $this->input->post("select_rom");

		$data = array(
			"material_geofence" => ""
		);

		$data2 = array(
			"material_geofence" => $select_rom
		);

		$emptythisgeofence = $this->m_masterdata->geofenceforempty("master_material", $select_rom, $data);

		if ($emptythisgeofence) {
			$update            = $this->m_masterdata->updatethisgeofence("master_material", $material_no, $data2);

			// echo "<pre>";
			// var_dump($emptythisgeofence);die();
			// echo "<pre>";

			if ($update) {
				$error               = "Success set Geofence to this material";
				$callback['error']   = false;
				$callback['message'] = $error;

				echo json_encode($callback);
			}else {
				$error               = "Failed set Geofence to this material";
				$callback['error']   = true;
				$callback['message'] = $error;

				echo json_encode($callback);
			}
		}else {
			$error               = "Failed empty geofence";
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
		}
	}

  function addMaterial(){
    $this->params['code_view_menu'] = "masterdata";
    $this->params['title']          = "Master Data Material";
    // $this->params['data'] 					= $datafix;
    // $this->params['rcompany'] 			= $rows_company;
		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

	function material_save(){
		$add_material_id          = $this->input->post("add_material_id");
		$add_material_shortcut    = $this->input->post("add_material_id");
		$add_material_hauling     = strtoupper($this->input->post("add_material_hauling"));
		$add_material_coal        = strtoupper($this->input->post("add_material_coal"));
		$add_material_reg_date    = $this->input->post("add_material_reg_date");
		$add_material_description = $this->input->post("add_material_description");

		if ($add_material_id == "" || $add_material_hauling == "" || $add_material_coal == "" || $add_material_reg_date == "" || $add_material_description == "") {
			$error               = "Harap isi semua kolom dengan benar";
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
		}else {
			$cekMaterial       = $this->m_masterdata->cekMaterialByMaterialID("master_material", $add_material_id, $add_material_id);
			$totaldatamaterial = $this->m_masterdata->totalmastermaterial("master_material");
			$shortcut          = ($totaldatamaterial[0]['material_no'] + 1);

			// echo "<pre>";
			// var_dump($shortcut);die();
			// echo "<pre>";

				if (sizeof($cekMaterial) > 0) {
					$error               = "Data Material is Available";
					$callback['error']   = true;
					$callback['message'] = $error;

					echo json_encode($callback);
				}else {
					$data = array(
						"material_parent_id"    => $this->sess->user_parent,
						"material_user_id"      => $this->sess->user_id,
						"material_id"           => $add_material_id,
						"material_shortcut"     => $shortcut,
						"material_hauling"      => $add_material_hauling,
						"material_coal"         => $add_material_coal,
						"material_reg_date"     => $add_material_reg_date,
						"material_description"  => $add_material_description,
						"material_created_date" => date("Y-m-d H:i:s")
					);

					// echo "<pre>";
					// var_dump($cekClient);die();
					// echo "<pre>";

					$insert = $this->m_masterdata->insertData("master_material", $data);
						if ($insert) {
							$error               = "Successfuly Save Data Material";
							$callback['error']   = false;
							$callback['message'] = $error;

							echo json_encode($callback);
						}else {
							$error               = "Failed Save Data Material";
							$callback['error']   = true;
							$callback['message'] = $error;

							echo json_encode($callback);
						}
				}
				$contentlog = "Master Data Material Successfuly Inserted";
				$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
		}
	}

	function editMaterial($material_no){
		$detDataMaterial = $this->m_masterdata->getMaterialByID("master_material", $material_no);
		// echo "<pre>";
		// var_dump($detDataMaterial);die();
		// echo "<pre>";
		$this->params['code_view_menu']        = "masterdata";
    $this->params['title']                 = "Edit Data Material";
    $this->params['data_material']  			 = $detDataMaterial;
    // $this->params['rcompany'] 			     = $rows_company;

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_material', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function material_update(){
		$edit_material_no          = $this->input->post("edit_material_no");
		$edit_material_id          = $this->input->post("edit_material_id");
		$edit_material_shortcut    = $this->input->post("edit_material_id");
		$edit_material_hauling     = strtoupper($this->input->post("edit_material_hauling"));
		$edit_material_coal        = strtoupper($this->input->post("edit_material_coal"));
		$edit_material_reg_date    = $this->input->post("edit_material_reg_date");
		$edit_material_description = $this->input->post("edit_material_description");

		if ($edit_material_id == "" || $edit_material_hauling == "" || $edit_material_coal == "" || $edit_material_reg_date == "" || $edit_material_description == "") {
			$error               = "Harap isi semua kolom dengan benar";
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
		}else {
			$data = array(
				"material_id"          => $edit_material_id,
				"material_shortcut"    => $edit_material_shortcut,
				"material_hauling"     => $edit_material_hauling,
				"material_coal"        => $edit_material_coal,
				"material_reg_date"    => $edit_material_reg_date,
				"material_description" => $edit_material_description
			);

			// echo "<pre>";
			// var_dump($data);die();
			// echo "<pre>";

			$update = $this->m_masterdata->updateData("master_material", "material_no", $edit_material_no, $data);
				if ($update) {
					$error               = "Successfuly Update Data Material";
					$callback['error']   = false;
					$callback['message'] = $error;

					echo json_encode($callback);
				}else {
					$error               = "Failed Update Data Material";
					$callback['error']   = true;
					$callback['message'] = $error;

					echo json_encode($callback);
				}
				$contentlog = "Master Data Material Successfuly Updated";
				$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "EDIT", "FUNCTIONAL");
		}
	}

	function deleteMaterial($material_no){
		$data = array(
			"material_flag" => 1
		);
		$update = $this->m_masterdata->updateData("master_material", "material_no", $material_no, $data);
			if ($update) {
				redirect(base_url()."masterdata/material");
			}else {
				redirect(base_url()."masterdata/material");
			}
			$contentlog = "Master Data Material Successfuly Deleted";
			$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "DELETE", "FUNCTIONAL");
	}

  function client(){
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;

		$data_client = $this->m_masterdata->getAllClient("master_client");

    $this->params['code_view_menu']        = "masterdata";
    $this->params['title']                 = "Master Data Client";
    $this->params['data_client'] 					 = $data_client;
		$this->params['privilegecode']         = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}elseif ($privilegecode == 8) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_masterdata_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

  function addClient(){
    $this->params['code_view_menu'] = "masterdata";
    $this->params['title']          = "Master Data Client";
    // $this->params['data'] 					= $datafix;
    // $this->params['rcompany'] 			= $rows_company;

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_add_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

	function client_save(){
		$add_client         = $this->input->post("add_client");
		$client_reg_date    = $this->input->post("client_reg_date");
		$client_description = $this->input->post("client_description");

		if ($add_client == "" || $client_reg_date == "" || $client_description == "") {
			$error               = "Harap isi semua kolom dengan benar";
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
		}else {
			$cekClient       = $this->m_masterdata->cekClientByName("master_client", $add_client, $add_client);
			$totaldataclient = $this->m_masterdata->totalmasterclient("master_client");
			$shortcut        = ($totaldataclient[0]['client_no'] + 1);

				if (sizeof($cekClient) > 0) {
					$error               = "Data Client is Available";
					$callback['error']   = true;
					$callback['message'] = $error;

					echo json_encode($callback);
				}else {
					$data = array(
						"client_parent_id"    => $this->sess->user_parent,
						"client_user_id"      => $this->sess->user_id,
						"client_id"           => $add_client,
						"client_shortcut"     => $shortcut,
						"client_reg_date"     => $client_reg_date,
						"client_description"  => $client_description,
						"client_created_date" => date("Y-m-d H:i:s")
					);

					// echo "<pre>";
					// var_dump($data);die();
					// echo "<pre>";

					$insert = $this->m_masterdata->insertData("master_client", $data);
						if ($insert) {
							$error               = "Successfuly Save Data Client";
							$callback['error']   = false;
							$callback['message'] = $error;

							echo json_encode($callback);
						}else {
							$error               = "Failed Save Data Client";
							$callback['error']   = true;
							$callback['message'] = $error;

							echo json_encode($callback);
						}
						$contentlog = "Master Data Client Successfuly Inserted";
						$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "ADD", "FUNCTIONAL");
				}
		}

	}

	function editclient($client_no){
		$getDataClient = $this->m_masterdata->getClientByID("master_client", $client_no);
		// echo "<pre>";
		// var_dump($getDataClient);die();
		// echo "<pre>";
		$this->params['code_view_menu']        = "masterdata";
    $this->params['title']                 = "Edit Data Client";
    $this->params['data_client'] 					 = $getDataClient;
    // $this->params['rcompany'] 			     = $rows_company;

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/masterdata/v_edit_client', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function client_update(){
		$edit_client_no          = $this->input->post("edit_client_no");
		$edit_client_id_old      = $this->input->post("edit_client_id_old");
		$edit_client_id          = $this->input->post("edit_client_id");
		$edit_client_name_old    = strtoupper($this->input->post("edit_client_name_old"));
		$edit_client_name        = strtoupper($this->input->post("edit_client_name"));
		$edit_client_reg_date    = $this->input->post("edit_client_reg_date");
		$edit_client_description = $this->input->post("edit_client_description");

		if ($edit_client_id == "" || $edit_client_reg_date == "" || $edit_client_description == "") {
			$error               = "Harap isi semua kolom dengan benar";
			$callback['error']   = true;
			$callback['message'] = $error;

			echo json_encode($callback);
		}else {
			$data = array(
				"client_id"           => $edit_client_id,
				"client_name"         => $edit_client_name,
				"client_reg_date"     => $edit_client_reg_date,
				"client_description"  => $edit_client_description
			);

			// echo "<pre>";
			// var_dump($data);die();
			// echo "<pre>";

			$update = $this->m_masterdata->updateData("master_client", "client_no", $edit_client_no, $data);
				if ($update) {
					$error               = "Successfuly Update Data Client";
					$callback['error']   = false;
					$callback['message'] = $error;

					echo json_encode($callback);
				}else {
					$error               = "Failed Update Data Client";
					$callback['error']   = true;
					$callback['message'] = $error;

					echo json_encode($callback);
				}
			$contentlog = "Master Data Client Successfuly Updated";
			$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "EDIT", "FUNCTIONAL");
		}
	}

	function deleteClient($client_no){
		$data = array(
			"client_flag" => 1
		);
		$update = $this->m_masterdata->updateData("master_client", "client_no", $client_no, $data);
			if ($update) {
				$contentlog = "Master Data Client Successfuly Deleted";
				$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "DELETE", "FUNCTIONAL");
				redirect(base_url()."masterdata/client");
			}else {
				redirect(base_url()."masterdata/client");
			}
	}



























}
