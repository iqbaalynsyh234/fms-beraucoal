<?php
class M_destmaster extends Model {

  function getalldata($table, $user_id){
    $this->db->where("dest_creator_id", $user_id);
		$this->db->where("dest_flag", 0);
		$q             = $this->db->get($table);
		return $result = $q->result_array();
  }

  function getalldatabydestid($table, $where, $id){
		$this->db->where("dest_flag", 0);
    $this->db->where($where, $id);
		$q             = $this->db->get($table);
		return $result = $q->result_array();
  }

  function update_date($table, $where, $id, $data){
    $this->db->where("dest_id", $id);
    return $this->db->update($table, $data);
  }

  function delete_data($table, $where, $iddelete, $data){
    $this->db->where($where, $iddelete);
    return $this->db->update($table, $data);
  }

  function getforreport($table, $userid, $user_company, $report_vehicle, $report_driver, $report_startdate, $report_enddate){
    if ($this->sess->user_type == 1) {
      $this->db->where("dest_creator_id", $userid);
    }else {
      $this->db->where("dest_company_id", $user_company);
    }

    if ($report_vehicle != "All") {
      $this->db->where("dest_vehicle_device", $report_vehicle);
    }

    if ($report_vehicle != "All") {
      $this->db->where("dest_driver_id", $report_driver);
    }

    $this->db->where("dest_flag", 0);
    $this->db->where("dest_endshowing_date >=", $report_startdate);
    $this->db->where("dest_endshowing_date <=", $report_enddate);
    $q             = $this->db->get($table);
    return $result = $q->result_array();
  }


}
