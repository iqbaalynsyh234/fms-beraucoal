<?php
class M_devicereport extends Model{

  function getallreport($dbtable, $sdate, $edate, $vehicle){
		$this->dbreport = $this->load->database("tensor_report", true);

    $this->dbreport->where("devicestatus_vehicle_vehicle_device", $vehicle);

    $this->dbreport->where("devicestatus_submited_date >=", $sdate);
    $this->dbreport->where("devicestatus_submited_date <=", $edate);
		return $this->dbreport->get($dbtable)->result_array();
  }

  function getthisvehicle($company, $vehicle)
  {
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no", "asc");

    if ($company != "all") {
      $this->db->where("vehicle_company", $company);
    }

    if ($vehicle != "all") {
      $this->db->where("vehicle_device", $vehicle);
    }

    $this->db->where("vehicle_mv03 !=", "0000");
    // $this->db->where_in("vehicle_type", array("MV03"));
    $this->db->where("vehicle_status <>", 3);
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
  }

  function getgpsoffline($table, $company, $vehicle, $sdate, $edate){
    $this->dbreport = $this->load->database("tensor_report", true);

    if ($company != "all") {
      $this->dbreport->where("gpsoffline_vehicle_companyid", $company);
    }

    if ($vehicle != "all") {
      $this->dbreport->where("gpsoffline_vehicle_device", $vehicle);
    }

    $this->dbreport->where("gpsoffline_data_submited >=", $sdate);
    $this->dbreport->where("gpsoffline_data_submited <=", $edate);
    return $this->dbreport->get($table)->result_array();
  }

  function getdatasummarymdvr($table, $company, $vehicle, $frekuensianomali, $sdate, $edate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

      if ($company != "all") {
        $this->dbtensor->where("devicestatus_summary_vehicle_company", $company);
      }

      if ($vehicle != "all") {
        $this->dbtensor->where("devicestatus_summary_vehicle_device", $vehicle);
      }

      if ($frekuensianomali != "all") {
        $this->dbtensor->where("devicestatus_summary_frekuensi_anomali >=", $frekuensianomali);
      }

    $this->dbtensor->where("devicestatus_summary_isformitra", 0);
    $this->dbtensor->where("devicestatus_summary_submited_date >=", $sdate);
    $this->dbtensor->where("devicestatus_summary_submited_date <=", $edate);
    $this->dbtensor->order_by("devicestatus_summary_frekuensi_anomali", "ASC");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function getdevice(){
    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_parent     = $this->sess->user_parent;
    $user_id_role    = $this->sess->user_id_role;
    $privilegecode   = $this->sess->user_id_role;
    $user_id         = $this->sess->user_id;
    $user_id_fix     = "";

    if($user_id == "1445"){
      $user_id_fix = $user_id;
    }else{
      $user_id_fix = $this->sess->user_id;
    }

    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no","asc");

    if($privilegecode == 0){
      $this->db->where("vehicle_user_id", $user_id_fix);
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
    }else if($privilegecode == 6){
      $this->db->where("vehicle_company", $user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_mv03 !=", "0000");
    // $this->db->where_in("vehicle_type", array("MV03"));
    $this->db->where("vehicle_status <>", 3);
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
  }














}
