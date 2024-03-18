<?php
class M_joborder extends Model {

  function getdevice(){
    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_id         = $this->sess->user_id;
    $user_id_fix     = "";

    if($user_id == "1445"){
      $user_id_fix = $user_id; //tag
   // }elseif ($user_id == "4203") { // INI NANTI DIILANGIN KLO SUDAH LIVE
    //  $user_id_fix = "4201"; //demo kalimantan
    }else{
      $user_id_fix = $this->sess->user_id;
    }

    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_name","asc");

    if($user_level == 1){
      $this->db->where("vehicle_user_id", $user_id_fix);
    }else if($user_level == 2){
      $this->db->where("vehicle_company", $user_company);
    }else if($user_level == 3){
      $this->db->where("vehicle_subcompany", $user_subcompany);
    }else if($user_level == 4){
      $this->db->where("vehicle_group", $user_group);
    }else if($user_level == 5){
      $this->db->where("vehicle_subgroup", $user_subgroup);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_status <>", 3);
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
  }

  function insertdata($table, $data){
    $this->dbwebtrackingts     = $this->load->database("webtracking_ts", true);
    return $this->dbwebtrackingts->insert($table, $data);
  }

  function alldatajob(){
    $this->dbwebtrackingts     = $this->load->database("webtracking_ts", true);
    $this->dbwebtrackingts->select("*");
    $this->dbwebtrackingts->order_by("order_datetime","desc");
    $this->dbwebtrackingts->where("order_flag", 0);
    $q       = $this->dbwebtrackingts->get("ts_order");
    return  $q->result_array();
  }

  function getdevicebyvdevice($vdevice){
    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("vehicle_device", $vdevice);
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
  }


}
