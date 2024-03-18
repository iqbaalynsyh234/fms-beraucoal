<?php
  class M_simcard extends Model{

    function getAllData($table){
      $this->dbreport = $this->load->database("default", true);
      $this->dbreport->where("simcard_flag", 0);
      $this->dbreport->order_by("simcard_vehicle_no", "ASC");
      $q = $this->dbreport->get($table);
      return $q->result_array();
    }

    function chekcthissimcard($simcard_no){
      $dbtable        = "simcard";
  		$this->dbreport = $this->load->database("default", true);
      $this->dbreport->where("simcard_number", $simcard_no);
      $this->dbreport->where("simcard_flag", 0);
  		$q = $this->dbreport->get($dbtable);
  		return $q->result_array();
    }

    function insertData($table, $data){
      $this->db = $this->load->database("default", true);
      return $this->db->insert($table, $data);
    }

    function insertDatatoTensor($table, $data){
      $this->dbtensor = $this->load->database("tensor_report", true);
      return $this->dbtensor->insert($table, $data);
    }





















  }
