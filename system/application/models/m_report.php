<?php
class M_report extends Model{

  function similarkminstreet($data){
    $this->db     = $this->load->database("default", true);
    $this->db->select("street_name, street_group");
    $this->db->like("street_name", $data);
    $this->db->where("street_creator", 4408);
    $this->db->where("street_type", 1);
    $this->db->where("street_flag", 0);
    $this->db->order_by("street_name", "asc");
    $q       = $this->db->get("webtracking_street");
    return  $q->result_array();
  }

}
