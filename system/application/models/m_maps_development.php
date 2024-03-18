<?php
class M_maps_development extends Model {

  function getmastervehicleforheatmap(){
    if($this->sess->user_id == "1445"){
      $user_id =  $this->sess->user_id; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

    $user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
    $user_parent 	   = $this->sess->user_parent;
    $user_id_role 	 = $this->sess->user_id_role;
		$user_id_fix     = $user_id;

    // echo "<pre>";
		// var_dump($user_id_role.'-'.$user_level.'-'.$user_company.'-'.$user_subcompany.'-'.$user_group.'-'.$user_subgroup.'-'.$user_dblive.'-'.$user_id_fix);die();
		// echo "<pre>";

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
      $this->db->where("vehicle_user_id", 4408);
		}else if($user_id_role == 6){
      $this->db->where("vehicle_user_id", 4408);
		}else if($user_id_role == 7){
      $this->db->where("vehicle_company", $user_parent);
		}else if($user_id_role == 8){
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else{
			$this->db->where("vehicle_no",99999);
		}

    $this->db->where("vehicle_typeunit", 0);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmastersite($site_id){
    $user_id 	 = $this->sess->user_id;

		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("site_name","asc");

		$this->db->where("site_created_by", $user_id);
    $this->db->where("site_id", $site_id);
    $this->db->where("site_status", 1);
    $this->db->where("site_flag", 0);
		$q       = $this->db->get("site");
		return $q->result_array();
  }

  function getcompany_by_parent_site($company_parent_site)
	{
		$this->db->order_by("company_name","asc");

		$this->db->where("company_parent_site",$company_parent_site);
		$q = $this->db->get("company");
		$rows = $q->result();
		return $rows;
  }

  function getmastervehiclebycontractor($companyid){
    if($this->sess->user_id == "1445"){
      $user_id =  $this->sess->user_id; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

    $user_level      = $this->sess->user_level;
    $user_parent     = $this->sess->user_parent;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
    $privilegecode 	 = $this->sess->user_id_role;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($privilegecode == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($privilegecode == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($privilegecode == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($privilegecode == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($privilegecode == 5){
			$this->db->where("vehicle_user_id", 4408); // TAMPILKAN SELURUH UNIT UNTUK MITRA
		}else if($privilegecode == 6){
			$this->db->where("vehicle_user_id", 4408); // TAMPILKAN SELURUH UNIT UNTUK MITRA
		}else if($privilegecode == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else{
			$this->db->where("vehicle_no",99999);
		}

    $this->db->where("vehicle_typeunit", 0);
		$this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");

    if ($companyid != 0) {
      $this->db->where("vehicle_company", $companyid);
      // $this->db->or_where("vehicle_id_shareto", $companyid);
    }

		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function master_street_hauling($site_id, $type)
  {
    $this->db->select("street_id, street_name, street_alias");
    $this->db->where("street_creator", 4408);

      if ($type == 3) {
        $this->db->where_in("street_type", array(3, 5));
      }elseif ($type == 1) {
        $this->db->where("street_type", $type);
      }elseif ($type == 4) {
        $this->db->where_in("street_type", array(4, 7, 8));
      }else {
        $this->db->where("street_type", $type);
      }

      if ($type == 1) {
        $this->db->where("street_flag", 0);
        $this->db->order_by("street_name", "ASC");
      }else {
        $this->db->where("street_flag", 1);
        $this->db->order_by("street_order", "ASC");
      }

    $q = $this->db->get("street");
    $rows = $q->result_array();
    return $rows;
  }

  function getstreet_now($type)
	{
		$this->db->select("street_id, street_name, street_alias");
      if ($type == 3) {
        $this->db->where_in("street_type", array(3, 5));
      }elseif ($type == 1) {
        $this->db->where("street_type", $type);
      }elseif ($type == 4) {
        $this->db->where_in("street_type", array(4, 7, 8));
      }else {
        $this->db->where("street_type", $type);
      }
    $this->db->order_by("street_order", "ASC");
		$this->db->where("street_creator", 4408);
		$q = $this->db->get("street");
		$rows = $q->result_array();
		return $rows;
  }

}
