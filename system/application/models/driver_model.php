<?php
class Driver_model extends Model {

    function Driver_model()
    {
		parent::Model();
    }

    function getalldatabyuserid($table, $where, $wherenya){
      $user_level      = $this->sess->user_level;
  		$user_company    = $this->sess->user_company;
  		$user_subcompany = $this->sess->user_subcompany;
  		$user_group      = $this->sess->user_group;
  		$user_subgroup   = $this->sess->user_subgroup;
  		$user_id_fix     = $this->sess->user_id;
  		//GET DATA FROM DB
  		$this->db->select("*");
  		$this->db->order_by("vehicle_no","asc");

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
  		$q = $this->db->get("vehicle");
  		$rows = $q->result_array();
  		return $rows;
    }

    function index1(){
      $this->db = $this->load->database("default", true);
			$this->db->select("vehicle_id, vehicle_no, vehicle_name");
			$this->db->where("vehicle_status != '3'");
			$q2 = $this->db->get("vehicle");
			return $q2->result();
    }

    function get1($table, $where, $wherenya){
      return $this->db->query(
        "SELECT * FROM $table where $where = '$wherenya'"
      )->result_array();
    }

    function getalldatadbtransporter($tableprefix, $where, $wherenya){
      $this->dbtransporter = $this->load->database("transporter", true);
  		$this->dbtransporter->select("*");
  		$this->dbtransporter->from($tableprefix);
		  $this->dbtransporter->where($where, $wherenya);
      $q = $this->dbtransporter->get()->result_array();

      return $q;
      $this->dbtransporter->close();
    }

    function updateDatadbtransporter($tableprefix, $where, $wherenya, $datanya){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->where($where, $wherenya);
      return $this->dbtransporter->update($tableprefix, $datanya);
    }

    function insertDataDbTransporter($tableprefix, $datanya){
      $this->dbtransporter = $this->load->database("transporter", true);
      return $this->dbtransporter->insert($tableprefix, $datanya);
    }



}
?>
