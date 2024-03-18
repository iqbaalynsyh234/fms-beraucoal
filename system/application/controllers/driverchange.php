<?php
include "base.php";

class Driverchange extends Base {
	var $otherdb;

	function Driverchange()
	{
		parent::Base();
		$this->load->library('email');
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->helper('common');
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_driverchange");
	}

	function index(){
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
		$this->db->where("vehicle_typeunit", 0);
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

		$this->params['driverportal'] = $this->m_driverchange->getFromPortalSimper();

		// echo "<pre>";
		// var_dump($this->params['datafromportal']);die();
		// echo "<pre>";

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;

		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverchange/v_home_driverchange', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverchange/v_home_driverchange', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverchange/v_home_driverchange', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverchange/v_home_driverchange', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverchange/v_home_driverchange', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverchange/v_home_driverchange', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverchange/v_home_driverchange', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
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
				$this->db->where("company_id", $this->sess->user_company);
			}elseif ($privilegecode == 6) {
				$this->db->where("company_id", $this->sess->user_company);
			}elseif ($privilegecode == 7) {
				$this->db->where("company_created_by", $this->sess->user_parent);
			}elseif ($privilegecode == 8) {
				$this->db->where("company_created_by", $this->sess->user_parent);
			}

			$this->db->where("company_flag", 0);
			$this->db->where("company_exca", 0);
				if ($this->sess->user_company == 1839 || $this->sess->user_parent == 4408 || $this->sess->user_id == 4408) {
					$this->db->or_where("company_exca", 2);
				}
			$qd = $this->db->get("company");
			$rd = $qd->result();

			return $rd;
		}

function index_old()
{
  if (! isset($this->sess->user_type))
  {
    redirect(base_url());
  }

	$userid          = $this->sess->user_id;
	$user_company    = $this->sess->user_company;
	$user_subcompany = $this->sess->user_subcompany;
	$user_group      = $this->sess->user_group;
	$user_subgroup   = $this->sess->user_subgroup;
	$user_parent     = $this->sess->user_parent;
	$user_id_role    = $this->sess->user_id_role;
	$privilegecode   = $this->sess->user_id_role;
	$user_level		   = $this->sess->user_level;
  $driver_company  = $this->sess->user_company;
  $driver_group    = $this->sess->user_group;
	$rows            = $this->getVehicleMV03();

	// echo "<pre>";
	// var_dump($rows);die();
	// echo "<pre>";

  $this->dbtransporter = $this->load->database('transporter', true);
  if($driver_group == 0){
    $this->dbtransporter->where("driver_company", $driver_company);
  }else{
    $this->dbtransporter->where("driver_group", $driver_group);
  }

  $this->dbtransporter->where("driver_status", 1);
  $this->dbtransporter->orderby("driver_name","asc");
  $q = $this->dbtransporter->get("driver");
  $driver = $q->result();

  $this->params["vehicles"]       = $rows;
  $this->params["drivers"]        = $driver;
  $this->params['code_view_menu'] = "report";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

	if ($privilegecode == 1) {
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/report/v_driver_change', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
	}elseif ($privilegecode == 2) {
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/report/v_driver_change', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
	}elseif ($privilegecode == 3) {
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/report/v_driver_change', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
	}elseif ($privilegecode == 4) {
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/report/v_driver_change', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
	}elseif ($privilegecode == 5) {
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/report/v_driver_change', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
	}else {
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/report/v_driver_change', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}
}

function search_report(){
	$user_privilege = $this->sess->user_id_role;
	$user_company   = $this->sess->user_company;

	$company          = $this->input->post("company");
	$vehicle          = $this->input->post("vehicle");
	$startdate        = $this->input->post("startdate");
	$shour            = "00:00:00";
	$enddate          = $this->input->post("enddate");
	$ehour            = "23:59:59";
	$driver           = $this->input->post("driver");
	$periode          = $this->input->post("periode");

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
			$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-7days"));
			$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

		}else if($periode == "last30"){
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

	$dbtable = "ts_driver_change_new";

	$getreport = $this->m_driverchange->getthisdata($dbtable, $company, $vehicle, $driver, $sdate, $edate, $user_privilege, $user_company);

	// echo "<pre>";
	// var_dump($company.'-'.$vehicle);die();
	// echo "<pre>";

	$this->params['data'] = $getreport;
	$html                 = $this->load->view("newdashboard/driverchange/v_driverchange_result", $this->params, true);
	$callback['error']    = false;
	$callback['html']     = $html;
	$callback['data']     = $getreport;
	echo json_encode($callback);
}

function searchold()
{
  $this->dbtransporter = $this->load->database("webtracking_ts", true);

  $vehicle           = $this->input->post("vehicle");
  $startdate         = $this->input->post("startdate");
  $enddate           = $this->input->post("enddate");
  $shour             = $this->input->post("shour");
  $ehour             = $this->input->post("ehour");
  $driver            = $this->input->post("driver");

  $sdate             = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
  $edate             = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":00"));


    if ($vehicle != 0)
    {
      $this->dbtransporter->where("change_imei", $vehicle);
    }
    if ($driver != 0)
    {
      $this->dbtransporter->where("change_driver_id", $driver);
    }

    $this->dbtransporter->where("change_driver_time >=", $sdate);
    $this->dbtransporter->where("change_driver_time <=", $edate);
    $this->dbtransporter->where("change_driver_flag", 0);
    $this->dbtransporter->order_by("change_driver_time","desc");
    $q                        = $this->dbtransporter->get("ts_driver_change");
    $rows                     = $q->result();

    $driver                   = $this->getDriver();
		$vehicle                  = $this->getVehicleMV03();

		// echo "<pre>";
		// var_dump($vehicle);die();
		// echo "<pre>";

		$datadriverid = array();
			for ($i=0; $i < sizeof($rows); $i++) {
				$drivernya       = $this->getDriver2($rows[$i]->change_driver_id);
				if (isset($drivernya[0])) {
					array_push($datadriverid, array(
						"driverid"     => $drivernya[0]->driver_id,
						"driveridcard" => $drivernya[0]->driver_idcard
					));
				}
			}

			$driverimagefix = array();
			for ($j=0; $j < sizeof($datadriverid); $j++) {
				$driverdetail = $this->getdriverdetail($datadriverid[$j]['driverid']);
					if ($driverdetail) {
						array_push($driverimagefix, array(
							"driverid"     => $datadriverid[$j]['driverid'],
							"driveridcard" => $datadriverid[$j]['driveridcard'],
							"driverimage"  => $driverdetail[0]->driver_image_raw_name.$driverdetail[0]->driver_image_file_ext
						));
					}
			}

		// echo "<pre>";
		// var_dump($driverimagefix);die();
		// echo "<pre>";

    $this->params['data']      	 = $rows;
    $this->params['drivers']   	 = $driver;
		$this->params['driverimage'] = $driverimagefix;
    $this->params['vehicles']  	 = $vehicle;

    $html                      	 = $this->load->view("newdashboard/report/v_driver_change_result", $this->params, true);
    $callback['error']         	 = false;
    $callback['html']          	 = $html;
    echo json_encode($callback);
}

function getDriver()
{
	$this->dbtransporter = $this->load->database("transporter", true);
	$this->dbtransporter->select('driver_id,driver_name,driver_idcard');
	$q = $this->dbtransporter->get("driver");
	$rows = $q->result();
	return $rows;
}

function getDriver2($driver_idcard)
{
	$this->dbtransporter = $this->load->database("transporter", true);
	$this->dbtransporter->select('driver_id,driver_name,driver_idcard');
	$this->dbtransporter->where('driver_idcard', $driver_idcard);
	$q = $this->dbtransporter->get("driver");
	$rows = $q->result();
	return $rows;
}

function getdriverdetail($driverid){
	$this->dbtransporter = $this->load->database('transporter',true);
	$this->dbtransporter->select("*");
	$this->dbtransporter->where("driver_image_driver_id", $driverid);
	$q   = $this->dbtransporter->get("driver_image");
	return $q->result();
}

function getVehicleMV03()
{
      $user_level      = $this->sess->user_level;
      $user_company    = $this->sess->user_company;
      $user_subcompany = $this->sess->user_subcompany;
      $user_group      = $this->sess->user_group;
      $user_subgroup   = $this->sess->user_subgroup;
			$user_parent     = $this->sess->user_parent;
			$user_id_role    = $this->sess->user_id_role;
			$privilegecode   = $this->sess->user_id_role;
      $user_id         = $this->sess->user_id;

      //GET DATA FROM DB
      $this->db     = $this->load->database("default", true);
      $this->db->select("vehicle_id,vehicle_name,vehicle_no,vehicle_mv03");
      $this->db->order_by("vehicle_name","asc");

      if($privilegecode == 0){
        $this->db->where("vehicle_user_id", $user_id);
      }else if($privilegecode == 1){
        $this->db->where("vehicle_user_id", $user_parent);
      }else if($privilegecode == 2){
        $this->db->where("vehicle_user_id", $user_parent);
      }else if($privilegecode == 3){
        $this->db->where("vehicle_user_id", $user_parent);
      }else if($privilegecode == 4){
        $this->db->where("vehicle_user_id", $user_parent);
      }else if($privilegecode == 5){
        $this->db->where("vehicle_company", $user_company);
      }else{
        $this->db->where("vehicle_no",99999);
      }

      $this->db->where("vehicle_mv03 !=", "0000");
	  $this->db->where("vehicle_status <>", 3);
      $q       = $this->db->get("vehicle");
      return  $q->result();
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
			$options .= "<option value='" . $obj->vehicle_no . "'>" . $i . ". " . $obj->vehicle_no . " - " . $obj->vehicle_name . " " . "(" . $obj->company_name . ")" . "</option>";
			$i++;
		}

		echo $options;
		return;
	}
}

}
 ?>
