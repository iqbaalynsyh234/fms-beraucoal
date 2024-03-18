<?php
include "base.php";

class Adminonly extends Base {
var $otherdb;

function Adminonly()
{
  parent::Base();
  $this->load->model("gpsmodel");
  $this->load->model("vehiclemodel");
  $this->load->model("configmodel");
  $this->load->helper('common_helper');
  $this->load->model("dashboardmodel");
  $this->load->model("m_adminonly");
}

// FUNCTION YANG ADA DI CONTROLLER ADMIN ONLY
// 1. locationreport

function locationreport()
{
  if (! isset($this->sess->user_type))
  {
    redirect(base_url());
  }

  $user_id 	       = $this->sess->user_id;
  $user_level      = $this->sess->user_level;
  $user_parent     = $this->sess->user_parent;
  $user_company    = $this->sess->user_company;
  $user_subcompany = $this->sess->user_subcompany;
  $user_group      = $this->sess->user_group;
  $user_subgroup   = $this->sess->user_subgroup;
  $user_dblive 	   = $this->sess->user_dblive;
  $privilegecode 	 = $this->sess->user_id_role;
  $user_id_fix     = $user_id;

  $this->db->select("vehicle.*, user_name");
  $this->db->order_by("vehicle_no", "asc");
  $this->db->where("vehicle_status <>", 3);
  $this->db->where("vehicle_type <>", "TJAM");

  if ($this->sess->user_type == 2)
  {
    if($privilegecode == 1){
      $this->db->where("vehicle_user_id", $user_parent);
      $this->db->or_where("vehicle_company", $user_parent);
    }else if($privilegecode == 2){
      $this->db->where("vehicle_user_id", $user_parent);
      $this->db->or_where("vehicle_company", $user_parent);
    }else if($privilegecode == 3){
      $this->db->where("vehicle_user_id", $user_parent);
      $this->db->or_where("vehicle_company", $user_parent);
    }else if($privilegecode == 4){
      $this->db->where("vehicle_user_id", $user_parent);
      $this->db->or_where("vehicle_company", $user_parent);
    }else if($privilegecode == 5){
      $this->db->where("vehicle_company", $user_company);
    }else if($privilegecode == 6){
      $this->db->where("vehicle_company", $user_company);
    }else if($privilegecode == 0){
      $this->db->where("vehicle_user_id", $user_id_fix);
      $this->db->or_where("vehicle_company", $this->sess->user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_active_date2 >=", date("Ymd"));
  }
  else
  if ($this->sess->user_type == 3)
  {
    $this->db->where("user_agent", $this->sess->user_agent);
  }
  //tambahan, user group yg open playback report
  if ($this->sess->user_group <> 0)
  {
    $this->db->where("vehicle_group", $this->sess->user_group);
  }

  $this->db->join("user", "vehicle_user_id = user_id", "left outer");
  $q = $this->db->get("vehicle");

  if ($q->num_rows() == 0)
  {
    redirect(base_url());
  }

  $rows = $q->result();

  $rows_company = $this->get_company_bylevel();

  $this->params["vehicles"] = $rows;
  $this->params["rcompany"] = $rows_company;
  $this->params['code_view_menu'] = "report";

  $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
  $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);


    if ($privilegecode == 1) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/adminonly/v_home_locationreport', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    }elseif ($privilegecode == 2) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/adminonly/v_home_locationreport', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    }elseif ($privilegecode == 3) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/adminonly/v_home_locationreport', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    }elseif ($privilegecode == 4) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/adminonly/v_home_locationreport', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    }elseif ($privilegecode == 5) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/adminonly/v_home_locationreport', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
    }elseif ($privilegecode == 6) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/adminonly/v_home_locationreport', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
    }else {
      $this->params["sidebar"] = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
      $this->params["content"] = $this->load->view('newdashboard/adminonly/v_home_locationreport', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
    }


}

function search_locationreport(){
  ini_set('memory_limit','3048M');

  if (! isset($this->sess->user_type))
  {
    redirect(base_url());
  }

  $company        = $this->input->post("company");
  $vehicle        = $this->input->post("vehicle");
  $startdate      = $this->input->post("startdate");
  $enddate        = $this->input->post("enddate");
  $shour          = $this->input->post("shour");
  $ehour          = $this->input->post("ehour");

  $location_start = $this->input->post("location_start");
  $location_end   = $this->input->post("location_end");
  $startdur       = $this->input->post("s_minute");
  $enddur         = $this->input->post("e_minute");
  $km_start       = $this->input->post("km_start");
  $km_end         = $this->input->post("km_end");

  $type_speed     = $this->input->post("type_speed");
  $type_location  = $this->input->post("type_location");
  $type_duration  = $this->input->post("type_duration");
  $type_km        = $this->input->post("type_km");
  $statusname     = $this->input->post("statusname");
  $statusspeed    = $this->input->post("statusspeed");

  // $maxvoltage    = $this->input->post("maxvoltage");

  if($startdur != "" && $enddur != ""){
    $startdur = $startdur * 60;
    $enddur = $enddur * 60;
  }

  $report = "location_"; // new report
  $report_sum = "summary_";

  $sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
  $edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));

  $d1 = date("d", strtotime($startdate));
  $d2 = date("d", strtotime($enddate));
  $m1 = date("F", strtotime($startdate));
  $m2 = date("F", strtotime($enddate));
  $year = date("Y", strtotime($startdate));
  $year2 = date("Y", strtotime($enddate));
  $rows = array();
  $rows2 = array();
  $total_q = 0;
  $total_q2 = 0;

  $error = "";
  $rows_summary = "";

  $location_list = array("location","location_off","location_idle");

  if ($vehicle == "")
  {
    $error .= "- Invalid Vehicle. Silahkan Pilih Kendaraan! \n";
  }

  if ($d1 != $d2)
  {
    $error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam tanggal yang sama! \n";
  }

  if ($m1 != $m2)
  {
    $error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
  }

  if ($year != $year2)
  {
    $error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
  }

  if ($error != "")
  {
    $callback['error'] = true;
    $callback['message'] = $error;

    echo json_encode($callback);
    return;
  }

  switch ($m1)
  {
    case "January":
          $dbtable = $report."januari_".$year;
    $dbtable_sum = $report_sum."januari_".$year;
    break;
    case "February":
          $dbtable = $report."februari_".$year;
    $dbtable_sum = $report_sum."februari_".$year;
    break;
    case "March":
          $dbtable = $report."maret_".$year;
    $dbtable_sum = $report_sum."maret_".$year;
    break;
    case "April":
          $dbtable = $report."april_".$year;
    $dbtable_sum = $report_sum."april_".$year;
    break;
    case "May":
          $dbtable = $report."mei_".$year;
    $dbtable_sum = $report_sum."mei_".$year;
    break;
    case "June":
          $dbtable = $report."juni_".$year;
    $dbtable_sum = $report_sum."juni_".$year;
    break;
    case "July":
          $dbtable = $report."juli_".$year;
    $dbtable_sum = $report_sum."juli_".$year;
    break;
    case "August":
          $dbtable = $report."agustus_".$year;
    $dbtable_sum = $report_sum."agustus_".$year;
    break;
    case "September":
          $dbtable = $report."september_".$year;
    $dbtable_sum = $report_sum."september_".$year;
    break;
    case "October":
          $dbtable = $report."oktober_".$year;
    $dbtable_sum = $report_sum."oktober_".$year;
    break;
    case "November":
          $dbtable = $report."november_".$year;
    $dbtable_sum = $report_sum."november_".$year;
    break;
    case "December":
          $dbtable = $report."desember_".$year;
    $dbtable_sum = $report_sum."desember_".$year;
    break;
  }

  switch ($m2)
  {
    case "January":
          $dbtable2 = $report."januari_".$year;
    $dbtable2_sum = $report_sum."januari_".$year;
    break;
    case "February":
          $dbtable2 = $report."februari_".$year;
    $dbtable2_sum = $report_sum."februari_".$year;
    break;
    case "March":
          $dbtable2 = $report."maret_".$year;
    $dbtable2_sum = $report_sum."maret_".$year;
    break;
    case "April":
          $dbtable2 = $report."april_".$year;
    $dbtable2_sum = $report_sum."april_".$year;
    break;
    case "May":
          $dbtable2 = $report."mei_".$year;
    $dbtable2_sum = $report_sum."mei_".$year;
    break;
    case "June":
          $dbtable2 = $report."juni_".$year;
    $dbtable2_sum = $report_sum."juni_".$year;
    break;
    case "July":
          $dbtable2 = $report."juli_".$year;
    $dbtable2_sum = $report_sum."juli_".$year;
    break;
    case "August":
          $dbtable2 = $report."agustus_".$year;
    $dbtable2_sum = $report_sum."agustus_".$year;
    break;
    case "September":
          $dbtable2 = $report."september_".$year;
    $dbtable2_sum = $report_sum."september_".$year;
    break;
    case "October":
          $dbtable2 = $report."oktober_".$year;
    $dbtable2_sum = $report_sum."oktober_".$year;
    break;
    case "November":
          $dbtable2 = $report."november_".$year;
    $dbtable2_sum = $report_sum."november_".$year;
    break;
    case "December":
          $dbtable2 = $report."desember_".$year;
    $dbtable2_sum = $report_sum."desember_".$year;
    break;
  }

  // echo "<pre>";
  // var_dump($company.'-'.$vehicle.'-'.$location_start.'-'.$location_end.'-'.$statusname.'-'.$statusspeed.'-'.$sdate.'-'.$edate);die();
  // echo "<pre>";

    $this->dbtrip = $this->load->database("tensor_report",true);
    $this->dbtrip->order_by("location_report_gps_time","asc");

    if($company != "all"){
      $this->dbtrip->where("location_report_vehicle_company", $company);
    }

    if($vehicle != "all"){
      $this->dbtrip->where("location_report_vehicle_device", $vehicle);
    }
    // $this->dbtrip->where("location_report_fuel_data <=", $maxvoltage);

    if($type_location == "1"){
      if($location_start != ""){
        $this->dbtrip->like("location_report_location", $location_start);
      }
      if($location_end != ""){
        $this->dbtrip->like("location_report_location", $location_end);
      }
    }

    if($statusname != "all"){
      $this->dbtrip->where("location_report_name", $statusname);
    }

    if($type_speed == "1"){
      if($statusspeed != ""){
        $this->dbtrip->where("location_report_speed", $statusspeed);
      }
    }

    $this->dbtrip->where("location_report_gps_time >=",$sdate);
    $this->dbtrip->where("location_report_gps_time <=", $edate);

    $q = $this->dbtrip->get($dbtable);
    $rows = $q->result();

    // $dbtable.'-'.$dbtable2.'-'.$dbtable2_sum
    // $vehicle.'-'.$type_location.'-'.$location_end.'-'.$statusname.'-'.$type_speed

    // echo "<pre>";
    // var_dump($vehicle);die();
    // echo "<pre>";

  if($m1 != $m2)
  {
    $params['data']        = $rowsall;

    $dataformap = array();
    for ($i=0; $i < sizeof($rows); $i++) {
      array_push($dataformap, array(
        "location_report_id"            	=> $rowsall[$i]->location_report_id,
        "location_report_vehicle_user_id" => $rowsall[$i]->location_report_vehicle_user_id,
        "location_report_vehicle_id"      => $rowsall[$i]->location_report_vehicle_id,
        "location_report_vehicle_device"  => $rowsall[$i]->location_report_vehicle_device,
        "location_report_vehicle_no"      => $rowsall[$i]->location_report_vehicle_no,
        "location_report_vehicle_name"    => $rowsall[$i]->location_report_vehicle_name,
        "location_report_vehicle_type"    => $rowsall[$i]->location_report_vehicle_type,
        "location_report_vehicle_company" => $rowsall[$i]->location_report_vehicle_company,
        "location_report_imei"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $rowsall[$i]->location_report_imei),
        "location_report_type"            => $rowsall[$i]->location_report_type,
        "location_report_name"            => $rowsall[$i]->location_report_name,
        "location_report_speed"           => $rowsall[$i]->location_report_speed,
        "location_report_gpsstatus"       => $rowsall[$i]->location_report_gpsstatus,
        "location_report_gps_time"        => $rowsall[$i]->location_report_gps_time,
        "location_report_geofence_id"     => $rowsall[$i]->location_report_geofence_id,
        "location_report_geofence_name"   => $rowsall[$i]->location_report_geofence_name,
        "location_report_geofence_limit"  => $rowsall[$i]->location_report_geofence_limit,
        "location_report_geofence_type"   => $rowsall[$i]->location_report_geofence_type,
        "location_report_jalur"           => $rowsall[$i]->location_report_jalur,
        "location_report_direction"       => $rowsall[$i]->location_report_direction,
        "location_report_location"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $rowsall[$i]->location_report_location),
        "location_report_coordinate"      => $rowsall[$i]->location_report_coordinate,
        "location_report_latitude"        => $rowsall[$i]->location_report_latitude,
        "location_report_longitude"       => $rowsall[$i]->location_report_longitude,
        "location_report_odometer"        => $rowsall[$i]->location_report_odometer,
        "location_report_fuel_data"       => $rowsall[$i]->location_report_fuel_data,
        "location_report_fuel_data_fix"   => $rowsall[$i]->location_report_fuel_data_fix,
        "location_report_fuel_liter"      => $rowsall[$i]->location_report_fuel_liter,
        "location_report_fuel_liter_fix"  => $rowsall[$i]->location_report_fuel_liter_fix,
        "location_report_view"            => $rowsall[$i]->location_report_view,
        "location_report_event"           => $rowsall[$i]->location_report_event
      ));
    }
    $params['dataformaps'] = $dataformap;
  }
  else
  {
    $params['data']        = $rows;

    $dataformap = array();
    for ($i=0; $i < sizeof($rows); $i++) {
      array_push($dataformap, array(
        "location_report_id"            	=> $rows[$i]->location_report_id,
        "location_report_vehicle_user_id" => $rows[$i]->location_report_vehicle_user_id,
        "location_report_vehicle_id"      => $rows[$i]->location_report_vehicle_id,
        "location_report_vehicle_device"  => $rows[$i]->location_report_vehicle_device,
        "location_report_vehicle_no"      => $rows[$i]->location_report_vehicle_no,
        "location_report_vehicle_name"    => $rows[$i]->location_report_vehicle_name,
        "location_report_vehicle_type"    => $rows[$i]->location_report_vehicle_type,
        "location_report_vehicle_company" => $rows[$i]->location_report_vehicle_company,
        "location_report_imei"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $rows[$i]->location_report_imei),
        "location_report_type"            => $rows[$i]->location_report_type,
        "location_report_name"            => $rows[$i]->location_report_name,
        "location_report_speed"           => $rows[$i]->location_report_speed,
        "location_report_gpsstatus"       => $rows[$i]->location_report_gpsstatus,
        "location_report_gps_time"        => $rows[$i]->location_report_gps_time,
        "location_report_geofence_id"     => $rows[$i]->location_report_geofence_id,
        "location_report_geofence_name"   => $rows[$i]->location_report_geofence_name,
        "location_report_geofence_limit"  => $rows[$i]->location_report_geofence_limit,
        "location_report_geofence_type"   => $rows[$i]->location_report_geofence_type,
        "location_report_jalur"           => $rows[$i]->location_report_jalur,
        "location_report_direction"       => $rows[$i]->location_report_direction,
        "location_report_location"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $rows[$i]->location_report_location),
        "location_report_coordinate"      => $rows[$i]->location_report_coordinate,
        "location_report_latitude"        => $rows[$i]->location_report_latitude,
        "location_report_longitude"       => $rows[$i]->location_report_longitude,
        "location_report_odometer"        => $rows[$i]->location_report_odometer,
        "location_report_fuel_data"       => $rows[$i]->location_report_fuel_data,
        "location_report_fuel_data_fix"   => $rows[$i]->location_report_fuel_data_fix,
        "location_report_fuel_liter"      => $rows[$i]->location_report_fuel_liter,
        "location_report_fuel_liter_fix"  => $rows[$i]->location_report_fuel_liter_fix,
        "location_report_view"            => $rows[$i]->location_report_view,
        "location_report_event"           => $rows[$i]->location_report_event
      ));
    }
    $params['dataformaps'] = $dataformap;
  }

  // echo "<pre>";
  // var_dump($params['dataformaps']);die();
  // echo "<pre>";

  // $params['maxvoltage']  = $maxvoltage;
  $params['dbtable']     = $dbtable;
  $params['dbtable_sum'] = $dbtable_sum;

  $params['startdate']   = $startdate;
  $params['enddate']     = $enddate;
  $html                  = $this->load->view("newdashboard/adminonly/v_locationreport_result", $params, true);

  $callback['error']     = false;
  $callback['html']      = $html;
  echo json_encode($callback);
  //return;

}

function get_company_bylevel(){
  if (! isset($this->sess->user_type))
  {
    redirect(base_url());
  }
  $this->db->order_by("company_name","asc");
  /*if($this->sess->user_level == "1"){
    $this->db->where("company_created_by", $this->sess->user_id);
  }*/
  $privilegecode 						= $this->sess->user_id_role;


  $this->db->order_by("company_name","asc");
    if ($privilegecode == 0) {
      $this->db->where("company_created_by", $this->sess->user_id);
    }elseif ($privilegecode == 1) {
      $this->db->where("company_created_by", $this->sess->user_parent);
    }elseif ($privilegecode == 2) {
      $this->db->where("company_created_by", $this->sess->user_parent);
    }elseif ($privilegecode == 3) {
      $this->db->where("company_created_by", $this->sess->user_parent);
    }elseif ($privilegecode == 4) {
      $this->db->where("company_created_by", $this->sess->user_parent);
    }elseif ($privilegecode == 5) {
      $this->db->where("company_created_by", $this->sess->user_company);
    }elseif ($privilegecode == 6) {
      $this->db->where("company_created_by", $this->sess->user_company);
    }

  // $this->db->where("company_created_by", $this->sess->user_id);
  $this->db->where("company_flag", 0);
  $qd = $this->db->get("company");
  $rd = $qd->result();

  return $rd;
}

















}
