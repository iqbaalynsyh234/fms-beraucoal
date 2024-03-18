<?php
class M_devicepanel extends Model {
    function M_devicepanel()
    {
		parent::Model();
    	$this->fromsocket = false;
    }


function device(){
  $user_level      = $this->sess->user_level;
  $user_company    = $this->sess->user_company;
  $user_subcompany = $this->sess->user_subcompany;
  $user_group      = $this->sess->user_group;
  $user_subgroup   = $this->sess->user_subgroup;

  if($this->sess->user_id == "1445"){
    $user_id = $this->sess->user_id; //tag
  }else{
    $user_id = $this->sess->user_id;
  }

  $user_id_fix     = $user_id;

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

function insertdata($table, $dblive, $data){
  // print_r($table.'-'.$dblive);exit();
  $this->db     = $this->load->database($dblive, true);
  return $this->db->insert($table, $data);
}

function getdatavehicle($device){
  $user_level      = $this->sess->user_level;
  $user_company    = $this->sess->user_company;
  $user_subcompany = $this->sess->user_subcompany;
  $user_group      = $this->sess->user_group;
  $user_subgroup   = $this->sess->user_subgroup;

  if($this->sess->user_id == "1445"){
    $user_id = $this->sess->user_id; //tag
  }else{
    $user_id = $this->sess->user_id;
  }

  $user_id_fix     = $user_id;

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

  $this->db->where("vehicle_device", $device);
  $this->db->where("vehicle_status <>", 3);
  $q       = $this->db->get("vehicle");
  return  $q->result_array();
}

}
?>
