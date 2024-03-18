<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class M_wimapi extends Model {

	function M_wimapi ()
	{
		parent::Model();
	}

  function checkintable($table, $transcationID){
    $this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
    $this->dbkalimantan->where("integrationwim_TransactionID", $transcationID);
		return $this->dbkalimantan->get($table)->result_array();
  }

  function insertData($table, $data){
    $this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
    return $this->dbkalimantan->insert($table, $data);
  }

  function updateData($table, $where, $transcationID, $data){
    $this->dbkalimantan = $this->load->database("webtracking_kalimantan", true);
    $this->dbkalimantan->where($where, $transcationID);
    return $this->dbkalimantan->update($table, $data);
  }

	function updateData2($table, $where, $wherenya, $data){
		$this->db = $this->load->database("default", true);
		$this->db->where($where, $wherenya);
		return $this->db->update($table, $data);
	}

	function checkInMasterPortal($table, $NoRangka, $TruckID){
		$this->db = $this->load->database("default", true);
		$this->db->like("master_portal_norangka", $NoRangka);
		$this->db->or_like("master_portal_nolambung", $TruckID);
		return $this->db->get($table)->result_array();
	}










}
