<?php
include "base.php";

class InoutGeofence extends Base {
	var $otherdb;

	function InoutGeofence()
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

  function index(){
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

    $this->db->select("vehicle.*, user_name");
    $this->db->order_by("user_name", "asc");
    $this->db->order_by("vehicle_name", "asc");
    $this->db->order_by("vehicle_no", "asc");
    $this->db->where("vehicle_status <>", 3);

    if ($this->sess->user_type == 2)
    {
      $this->db->where("vehicle_user_id", $this->sess->user_id);
      $this->db->or_where("vehicle_company", $this->sess->user_company);
      $this->db->where("vehicle_active_date2 >=", date("Ymd"));
    }
    else
    if ($this->sess->user_type == 3)
    {
      $this->db->where("user_agent", $this->sess->user_agent);
    }

    $this->db->join("user", "vehicle_user_id = user_id", "left outer");
    $q = $this->db->get("vehicle");

    if ($q->num_rows() == 0)
    {
      redirect(base_url());
    }

    $rows = $q->result();
    //print_r($rows);exit;
    $this->params["vehicles"]       = $rows;
    $this->params['code_view_menu'] = "report";


    $this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]        = $this->load->view('powerblock/dashboard/sidebar', $this->params, true);
    $this->params["content"]        = $this->load->view('powerblock/dashboard/report/v_home_inoutgeofence', $this->params, true);
    $this->load->view("dashboard/template_dashboard_report", $this->params);
  }

function inout_geofence_detail_report()
{
  $startdate         = $this->input->post("startdate");
  $enddate           = $this->input->post("enddate");
  $ve                = $this->input->post("vehicle");

  // echo "<pre>";
  // var_dump($startdate.'-'.$enddate.'-'.$ve);die();
  // echo "<pre>";

  $this->db->where("vehicle_device", $ve);
  $q                 = $this->db->get("vehicle");
  $rowvehicle        = $q->row();

  $vehicle_nopol     = $rowvehicle->vehicle_no;

  $params['vehicle'] = $rowvehicle;

  $sdate             = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
  $edate             = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));

  $this->db->order_by("geoalert_time", "asc");
  $this->db->where("geoalert_vehicle", $ve);
  $this->db->where("geoalert_time >=", $sdate);
  $this->db->where("geoalert_time <=", $edate);
  $this->db->join("geofence", "geofence_id = geoalert_geofence", "left outer");
  $q    = $this->db->get("geofence_alert");
  $rows = $q->result();

  // echo "<pre>";
  // var_dump($rows);die();
  // echo "<pre>";

  for($i=0; $i < count($rows); $i++)
  {
    $rows[$i]->geoalert_time_t = dbmaketime($rows[$i]->geoalert_time);
  }

  $params['data']              = $rows;
  $html                        = $this->load->view('powerblock/dashboard/report/v_inoutgeofence_report', $params, true);

  // $html                     = $this->load->view("transporter/report/inout_geofence_detail_report", $params, true);
  $callback['error']           = false;
  $callback['html']            = $html;
  echo json_encode($callback);
}

}
