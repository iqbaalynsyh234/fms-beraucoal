<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class M_tms extends Model {

	function M_tms(){

  	parent::Model();
  	$this->load->model("gpsmodel");
	}

	function getAllticket($table, $where, $user_id){
    $this->db->select("*");
    $this->db->where($where, $user_id);
		$this->db->where("ticket_flag", "0");
		$this->db->order_by("ticket_name_number","asc");
		$this->db->order_by("ticket_created_date","desc");
    $q  = $this->db->get($table);
		return $q->result_array();
  }

	function getAllticketformaps($table, $where, $user_id){
    $this->db->select("*");
    $this->db->where($where, $user_id);
		$this->db->where("ticket_flag", "0");
		$this->db->where("ticket_status != ", 3);
		$this->db->order_by("ticket_name_number","asc");
		$this->db->order_by("ticket_created_date","desc");
    $q  = $this->db->get($table);
		return $q->result_array();
  }

	function getAllticketformapsbyid($table, $where, $deviceid, $user_id){
    $this->db->select("*");
    $this->db->where($where, $user_id);
		$this->db->where("ticket_vehicle_device", $deviceid);
		$this->db->where("ticket_flag", "0");
		$this->db->where("ticket_status != ", 3);
		$this->db->order_by("ticket_created_date","asc");
    $q  = $this->db->get($table);
		return $q->result_array();
  }

  function getAllGardu($table, $where, $user_id){
    $this->db->select("*");
    $this->db->where($where, $user_id);
		$this->db->where("gardu_flag", "0");
    $q  = $this->db->get($table);
		return $q->result_array();
  }

	function getAllTechnician($table, $where, $user_company){
		$this->db->select("*");
		$this->db->where($where, $user_company);
		$this->db->where("technician_status", "1");
		$q  = $this->db->get($table);
		return $q->result_array();
	}

	function getAllCustomer($table, $where, $user_id){
    $this->db->select("*");
    $this->db->where($where, $user_id);
		$this->db->where("webtracking_tms_customer_flag", "0");
    $q  = $this->db->get($table);
		return $q->result_array();
  }

	function insert_data($table, $data){
    return $this->db->insert($table, $data);
  }

	function update_date($table, $where, $id, $data){
    $this->db->where("gardu_id", $id);
    return $this->db->update($table, $data);
  }

	function delete_data($table, $where, $iddelete, $data){
		$this->db->where($where, $iddelete);
		return $this->db->update($table, $data);
	}

	function getalldatabygarduid($table, $where, $id){
		$this->db->select("*");
		$this->db->where("gardu_flag", 0);
    $this->db->where($where, $id);
		$q             = $this->db->get($table);
		return $result = $q->result_array();
  }

	function getalldatabycustid($table, $where, $id){
		$this->db->select("*");
		$this->db->where("webtracking_tms_customer_flag", 0);
		$this->db->where("webtracking_tms_customer_id", $id);
		$q             = $this->db->get($table);
		return $result = $q->result_array();
	}

	function getalldatabytechid($table, $where, $id, $user_company){
		$this->db->select("*");
		$this->db->where("technician_status", 1);
		$this->db->where("technician_company", $user_company);
		$this->db->where($where, $id);
		$q             = $this->db->get($table);
		return $result = $q->result_array();
  }

	function cekisvehicleexist($table, $where, $wherenya, $user_company){
		$this->db->select("*");
		$this->db->where($where, $wherenya);
		$this->db->where("technician_status", 1);
		$this->db->where("technician_company", $user_company);
		$q             = $this->db->get($table);
		return $result = $q->result_array();
	}

	function update_data_all($table, $where, $id, $data){
		// echo "<pre>";
		// var_dump($table.'-'.$where.'-'.$id);die();
		// echo "<pre>";
    $this->db->where($where, $id);
    return $this->db->update($table, $data);
  }

	function update_dataforassignvehicle($table, $where, $where2, $userid, $id, $data){
    $this->db->where($where, $id);
		$this->db->where($where2, $userid);
    return $this->db->update($table, $data);
  }

	function getmastervehiclebydevid($device_id){
    if($this->sess->user_id == "1445"){
      $user_id = 3212; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

    $user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
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
    $this->db->where("vehicle_id", $device_id);
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

	function getdataduplicatename($table, $where, $ticket_name){
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
    $this->db->where($where, $ticket_name);
		$q       = $this->db->get($table);
		return $q->result_array();
	}

	function getfromdblive($table, $dblive){
    $this->db->dblive = $this->load->database($dblive, true);
		$q                  = $this->db->dblive->get($table);
		return $result      = $q->result_array();
  }

	function getmastervehicle(){
    if($this->sess->user_id == "1445"){
      $user_id = 3212; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

    $user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
		$user_id_fix     = $user_id;

    // echo "<pre>";
		// var_dump($user_level.'-'.$user_company.'-'.$user_subcompany.'-'.$user_group.'-'.$user_subgroup.'-'.$user_dblive.'-'.$user_id_fix);die();
		// echo "<pre>";

		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
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
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$this->db->where("vehicle_tms != '0000'");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }


}
