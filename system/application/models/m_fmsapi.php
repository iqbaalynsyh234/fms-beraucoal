<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class M_fmsapi extends Model {

	function M_fmsapi ()
	{
		parent::Model();
	}

  function checkintable($table, $transcationID){
    $this->dbreport = $this->load->database("tensor_report", true);
    $this->dbreport->where("integrationwim_TransactionID", $transcationID);
		return $this->dbreport->get($table)->result_array();
  }

  function insertData($table, $data){
    $this->dbreport = $this->load->database("tensor_report", true);
    return $this->dbreport->insert($table, $data);
  }

  function updateData($table, $where, $transcationID, $data){
    $this->dbreport = $this->load->database("tensor_report", true);
    $this->dbreport->where($where, $transcationID);
    return $this->dbreport->update($table, $data);
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
