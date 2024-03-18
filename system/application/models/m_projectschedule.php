<?php
class M_projectschedule extends Model {
    function M_projectschedule()
    {
		parent::Model();
    	$this->fromsocket = false;
    }

    function updateDatadbtransporter($tableprefix, $where, $wherenya, $datanya){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->where($where, $wherenya);
      return $this->dbtransporter->update($tableprefix, $datanya);
    }

    function insertdata($tableprefix, $datanya){
      $this->dbtransporter = $this->load->database("transporter", true);
      return $this->dbtransporter->insert($tableprefix, $datanya);
    }

    function update_date($tableprefix, $where, $idforupdate, $data){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->where($where, $idforupdate);
      return $this->dbtransporter->update($tableprefix, $data);
    }

    function getall($tableprefix, $where, $userid){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where($where, $userid);
      $this->dbtransporter->where("project_flag", "0");
      $this->dbtransporter->order_by("project_created_date", "desc");
      return $this->dbtransporter->get($tableprefix)->result_array();
    }

    function getprojectdilist($tableprefix, $where, $userid){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where($where, $userid);
      $this->dbtransporter->where("project_flag", "0");
      $this->dbtransporter->where("project_status", "!=3");
      $this->dbtransporter->order_by("project_startdate", "asc");
      return $this->dbtransporter->get($tableprefix)->result_array();
    }

    function dataprojecupdatecont($tableprefix, $where, $userid, $vehicle_device){
      // print_r($tableprefix.'-'.$where.'-'.$userid.'-'.$vehicle_device);exit();
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where($where, $userid);
      $this->dbtransporter->where("project_vehicle_device", $vehicle_device);
      $this->dbtransporter->where("project_flag", "0");
      $this->dbtransporter->where("project_status", "!=3");
      $this->dbtransporter->order_by("project_startdate", "asc");
      return $this->dbtransporter->get($tableprefix)->result_array();
    }

    function getallpool($tableprefix, $where, $userid){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where($where, $userid);
      $this->dbtransporter->where('pool_flag', '0');
      $this->dbtransporter->order_by("pool_name", "asc");
      return $this->dbtransporter->get($tableprefix)->result_array();
    }

    function getallpoolbyno($tableprefix, $where, $pool_no){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where($where, $pool_no);
      $this->dbtransporter->where('pool_flag', '0');
      return $this->dbtransporter->get($tableprefix)->result_array();
    }

    function getallpoolbyno2($tableprefix, $where, $poi_id){
      $this->db = $this->load->database("default", true);
      $this->db->select("*");
      $this->db->where($where, $poi_id);
      $this->db->where('poi_flag', '0');
      return $this->db->get($tableprefix)->result_array();
    }

    function getallbycode($tableprefix, $where, $userid, $id){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where($where, $userid);
      $this->dbtransporter->where("project_no", $id);
      $this->dbtransporter->order_by("project_name", "asc");
      return $this->dbtransporter->get($tableprefix)->result_array();
    }

    function getallbycodepool($tableprefix, $where, $userid, $id){
      $this->dbtransporter = $this->load->database("transporter", true);
      $this->dbtransporter->select("*");
      $this->dbtransporter->where($where, $userid);
      $this->dbtransporter->where("pool_no", $id);
      $this->dbtransporter->order_by("pool_name", "asc");
      return $this->dbtransporter->get($tableprefix)->result_array();
    }

    function getdatacompany($table, $where, $wherenya){
      $this->db->select("*");
      $this->db->where($where, $wherenya);
      return $this->db->get($table)->result_array();
    }


}
?>
