<?php
class M_ugemsmodel extends Model {

  function getcompanyname_byID($id)
  {
    $name = "-";
    $this->db->select("company_id,company_name");
    $this->db->order_by("company_name", "asc");
      if ($id != "all") {
        $this->db->where_in("company_id ", $id);
      }
      $this->db->where("company_flag", 0);

    $q = $this->db->get("company");
    $row = $q->result();
    if(count($row)>0){

      $name = $row;

    }else{

      $name = "-";
    }

    return $name;
  }






















}
