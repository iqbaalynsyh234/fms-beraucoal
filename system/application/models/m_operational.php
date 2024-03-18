<?php
class M_operational extends Model
{

  function getdevice()
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
    $user_id_fix     = "";

    if ($user_id == "1445") {
      $user_id_fix = $user_id;
    } else {
      $user_id_fix = $this->sess->user_id;
    }

    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no", "asc");

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
      $this->db->where("vehicle_subgroup", $user_subgroup);
    } else {
      $this->db->where("vehicle_no", 99999);
    }

    $this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
  }

  function getReport($table, $company, $vehicle, $sdate, $edate)
  {
    $this->dbts     = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    if ($vehicle != 0) {
      $this->dbts->where("kepmen_vehicle_device", $vehicle);
    }
    if ($company != 0) {
      $this->dbts->where("kepmen_company_id", $company);
    }
    $this->dbts->where("kepmen_date >=", $sdate);
    $this->dbts->where("kepmen_date <=", $edate);
    $this->dbts->order_by("kepmen_date", "ASC");
    $this->dbts->order_by("kepmen_vehicle_no", "ASC");
    $q             = $this->dbts->get($table);
    return  $q->result_array();
  }
}
