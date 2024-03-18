<?php
class M_installedfms extends Model {

  function getMastervehicle(){
    $this->db->order_by("vehicle_id","asc");
		$this->db->select("vehicle_id,vehicle_autocheck");

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$user_id_fix     = $this->sess->user_id;
		$user_excavator  = $this->sess->user_excavator;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_id","asc");

		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 10){
			$this->db->where("vehicle_company", $user_company);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		if ($user_excavator == 1) {
			$this->db->where("vehicle_typeunit", 1);
		}

		$this->db->where("vehicle_status <>", 3);
		$q    = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmastervehiclebycontractor($companyid){
    $this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_id,vehicle_autocheck");

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$user_id_fix     = $this->sess->user_id;
		$user_excavator  = $this->sess->user_excavator;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 10){
			$this->db->where("vehicle_company", $user_company);
		}else{
			$this->db->where("vehicle_no",99999);
		}

    if ($companyid != 0) {
      $this->db->where("vehicle_company", $companyid);
    }

		if ($user_excavator == 1) {
			$this->db->where("vehicle_typeunit", 1);
		}

		$this->db->where("vehicle_status <>", 3);
		$q    = $this->db->get("vehicle");
		return $q->result_array();
  }












}
