<?php
class M_common_report extends Model {

function get_company_bylevel(){
  if (! isset($this->sess->user_type))
  {
    redirect(base_url());
  }

  $privilegecode 						= $this->sess->user_id_role;

  $this->db->order_by("company_name","asc");
    if ($privilegecode == 0) {
      $this->db->where("company_created_by", $this->sess->user_id);
    }elseif ($privilegecode == 1) {
      $this->db->where("company_created_by", $this->sess->user_parent);
    }elseif ($privilegecode == 2) {
      $this->db->where("company_created_by", $this->sess->user_parent);
    }elseif ($privilegecode == 3) {
      $this->db->where("company_created_by", $this->sess->user_parent);
    }elseif ($privilegecode == 4) {
      $this->db->where("company_created_by", $this->sess->user_parent);
    }elseif ($privilegecode == 5) {
      $this->db->where("company_created_by", $this->sess->user_company);
    }elseif ($privilegecode == 6) {
      $this->db->where("company_created_by", $this->sess->user_company);
    }

  $this->db->where("company_flag", 0);
  $this->db->where("company_exca != ", 1);
  // $this->db->where("company_exca", 2);
  $qd = $this->db->get("company");
  $rd = $qd->result();

  return $rd;
}







}
