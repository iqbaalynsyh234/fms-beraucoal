<?php
include "base.php";

class Driverhistory extends Base {
	var $otherdb;

	function Driverhistory()
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
	}

function index()
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
	$user_level		   = $this->sess->user_level;

  $this->db->select("vehicle.*, user_name");
  $this->db->order_by("user_name", "asc");
  $this->db->order_by("vehicle_name", "asc");
  $this->db->order_by("vehicle_no", "asc");
  $this->db->where("vehicle_status <>", 3);

	$this->db = $this->load->database("default", true);
		if ($user_level == 1) {
			$this->db->where("vehicle_user_id", $userid);
		}elseif ($user_level == 2) {
			$this->db->where("vehicle_company", $user_company);
		}elseif ($user_level == 3) {
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}elseif ($user_level == 4) {
			$this->db->where("vehicle_group", $user_group);
		}else {
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}

  // if ($this->sess->user_type == 2)
  // {
  //   $this->db->where("vehicle_user_id", $this->sess->user_id);
  //   $this->db->or_where("vehicle_company", $this->sess->user_company);
  //   $this->db->where("vehicle_active_date2 >=", date("Ymd"));
  // }
  // else
  // if ($this->sess->user_type == 3)
  // {
  //   $this->db->where("user_agent", $this->sess->user_agent);
  // }

  $this->db->join("user", "vehicle_user_id = user_id", "left outer");
  $q = $this->db->get("vehicle");

  if ($q->num_rows() == 0)
  {
    redirect(base_url());
  }

  $rows           = $q->result();

  $driver_company = $this->sess->user_company;
  $driver_group   = $this->sess->user_group;

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
  // print_r($driver->result());exit();

  //$driver = $this->getDriver();
  $this->params["vehicles"]    = $rows;
  $this->params["drivers"]     = $driver;
	$this->params['code_view_menu'] = "report";

  $this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
  $this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
  $this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
  $this->params["content"]     = $this->load->view('dashboard/report/v_driver_history', $this->params, true);
  $this->load->view("dashboard/template_dashboard_report", $this->params);

  // $this->params["content"]  = $this->load->view('transporter/report/mn_driver_hist', $this->params, true);
  // $this->load->view("templatesess", $this->params);
}

function driver_hist_report()
{
  $this->dbtransporter = $this->load->database("transporter", true);

  $vehicle           = $this->input->post("vehicle");
  $startdate         = $this->input->post("startdate");
  $enddate           = $this->input->post("enddate");
  $shour             = $this->input->post("shour");
  $ehour             = $this->input->post("ehour");
  $driver            = $this->input->post("driver");

  $sdate             = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
  $edate             = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":00"));

  // echo "<pre>";
  // var_dump($vehicle.'-'.$driver.'-'.$sdate.'-'.$edate);die();
  // echo "<pre>";

    // $this->dbtransporter->where("driver_history_tanggal_submit >=", $sdate);
    // $this->dbtransporter->where("driver_history_tanggal_submit <=", $edate);

    if ($vehicle != 0)
    {
      $this->dbtransporter->where("driver_history_vehicle_id", $vehicle);
    }
    if ($driver != 0)
    {
      $this->dbtransporter->where("driver_history_driver_id", $driver);
    }


    $this->dbtransporter->where("driver_history_tanggal_submit >=", $sdate);
    $this->dbtransporter->where("driver_history_tanggal_submit <=", $edate);
    $this->dbtransporter->where("driver_history_creator", $this->sess->user_id);
    $this->dbtransporter->order_by("driver_history_tanggal_submit","desc");
    $this->dbtransporter->order_by("driver_history_vehicle_id","asc");
    $this->dbtransporter->order_by("driver_history_driver_id","asc");
    $q    = $this->dbtransporter->get("driver_history");
    $rows = $q->result();

    // print_r($rows);exit();
      // print_r($vehicle.$startdate.$enddate.$shour.$ehour.$driver);

    $driver = $this->getDriver();

    $this->params['data']              = $rows;
    $this->params['drivers']           = $driver;
    $this->params['vehicle']           = $vehicle;
		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";
    // $html                        = $this->load->view("transporter/report/list_result_driver_hist", $params, true);
    $html = $this->load->view("dashboard/report/v_driver_history_result", $this->params, true);
    $callback['error'] = false;
    $callback['html']  = $html;
    echo json_encode($callback);
}

function getDriver()
{
	$this->dbtransporter = $this->load->database("transporter", true);
	$this->dbtransporter->where('driver_company', $this->sess->user_company);
	$q = $this->dbtransporter->get("driver");
	$rows = $q->result();
	return $rows;

}

}
 ?>
