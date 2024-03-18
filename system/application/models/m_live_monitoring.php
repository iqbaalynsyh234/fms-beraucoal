<?php
class M_live_monitoring extends Model{

  function data_site_bc(){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $q       = $this->db->get("webtracking_site");
    return  $q->result_array();
  }

  function vehicle_by_mitra($id, $vehicle_user_id, $privilegecode, $user_parent, $vehicle, $filter_unit, $site_option)
	{
    // echo "<pre>";
    // var_dump($id);die();
    // echo "<pre>";
    $this->db     = $this->load->database("default", true);
		$this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_no,vehicle_name,vehicle_active_date2,vehicle_mv03, vehicle_status, vehicle_server_mdvr");
    if ($privilegecode == 5 || $privilegecode == 6) {
      if ($id != "all") {
        $this->db->where("vehicle_company",$id);
      }else {
        $this->db->where("vehicle_company",$user_parent);
      }
    }else {
      if ($id != "all") {
        $this->db->where("vehicle_company",$id);
      }
    }

    if ($vehicle != "all") {
      $this->db->where_in("vehicle_no", $vehicle);
    }

    if ($filter_unit != "all") {
      $this->db->where_in("vehicle_name", $filter_unit);
    }

    if ($site_option != "all") {
      $this->db->where_in("vehicle_site", $site_option);
    }

		$this->db->where("vehicle_status <>",3);
    $this->db->where("vehicle_user_id",$vehicle_user_id);
		$q = $this->db->get("vehicle");
		$rows = $q->result_array();
		return $rows;
  }

  function get_type_unit(){
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
		$this->db->select("vehicle_name");

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
      // $this->db->where("vehicle_user_id", 4408);
      $this->db->where("vehicle_company", $user_parent);
		}else if($user_id_role == 6){
      // $this->db->where("vehicle_user_id", 4408);
      $this->db->where("vehicle_company", $user_parent);
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
    $this->db->group_by("vehicle_name");
    $this->db->order_by("vehicle_name","asc");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

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
		// $this->db->where("vehicle_gotohistory", 0);
		// $this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getalldata($table){
    $this->db->where("poi_creator_id", "4408");
		$this->db->where("poi_flag", 0);
		$q             = $this->db->get($table);
		return $result = $q->result_array();
  }

  function getalldatabypoiid($table, $where, $where2, $id){
    $where;
		$this->db->where("poi_flag", 0);
    $this->db->where($where2, $id);
		$q             = $this->db->get($table);
		return $result = $q->result_array();
  }

  function getmapsetting(){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("mapsetting_parent_id", 4408);
    $this->dbts->where("mapsetting_user_id", 4408); //$user_id
		$q          = $this->dbts->get("ts_mapsetting");
		return $q->result_array();
  }

  function searchmasterdata($table, $key, $filter_unit, $site_option){
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
    $user_parent     = $this->sess->user_parent;
    $user_id_role    = $this->sess->user_id_role;
		$user_dblive 	   = $this->sess->user_dblive;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
    // echo "<pre>";
		// var_dump($key);die();
		// echo "<pre>";
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where_in("vehicle_no", $key);

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
      // $this->db->where("vehicle_company", $user_company);
      // $this->db->or_where("vehicle_id_shareto", $user_company);
    }else if($user_id_role == 6){
      // $this->db->where("vehicle_company", $user_company);
      // $this->db->or_where("vehicle_id_shareto", $user_company);
    }else if($user_id_role == 10){
      $this->db->where("vehicle_company", $user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    if ($filter_unit != "all") {
      $this->db->where("vehicle_name", $filter_unit);
    }

    if ($site_option != "all") {
      $this->db->where("vehicle_site", $site_option);
    }

    $this->db->where("vehicle_status <>", 3);
    // $this->db->where("vehicle_gotohistory", 0);
    // $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function searchdblivedata($table, $dblive, $vehicle_device){
    $this->db->dblive = $this->load->database($dblive, true);
    $this->db->dblive->select("*");
    $this->db->dblive->where("gps_name", $vehicle_device);
		$q                  = $this->db->dblive->get($table);
		return $result      = $q->result_array();
  }

  function check_data_karyawan_by_sid2($table, $sid, $company){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
      if ($sid != "all") {
        $this->dbts->where("karyawan_bc_sid", $sid);
      }
    $this->dbts->where("karyawan_bc_company_id", $company);
    $result = $this->dbts->get($table)->result_array();
    return $result;
  }

  function insert_manual_intervention($table, $data){
    $this->db     = $this->load->database("tensor_report", true);
    return $this->db->insert($table, $data);
  }

  function getDataVehicleByDevice2($vehicle_device){
    $this->db->select("*");
    $this->db->where("vehicle_device", $vehicle_device);
    return $this->db->get("vehicle")->result_array();
  }

  function get_type_intervention(){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("intervention_type_id", "asc");
    $q       = $this->db->get("type_intervention");
    return  $q->result_array();
  }

  function get_type_note($parent){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("type_note_parent", $parent);
    $this->db->order_by("type_note_name", "asc");
    $q       = $this->db->get("type_note");
    return  $q->result_array();
  }

  function getvehicle_bycompany_master($id)
	{
		$this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_no,vehicle_name,vehicle_active_date2,vehicle_mv03,vehicle_site");
		$this->db->where("vehicle_company",$id);
		$this->db->where("vehicle_status <>",3);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		return $rows;
  }

  function vehicle_site_by_company($id)
	{
		$this->db->order_by("vehicle_site","asc");
		$this->db->select("vehicle_site");
		$this->db->where("vehicle_company",$id);
		$this->db->where("vehicle_status <>",3);
    $this->db->group_by("vehicle_site");
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		return $rows;
  }

  function getdevice(){
    $user_level      = $this->sess->user_level;
    $user_company    = $this->sess->user_company;
    $user_subcompany = $this->sess->user_subcompany;
    $user_group      = $this->sess->user_group;
    $user_subgroup   = $this->sess->user_subgroup;
    $user_parent     = $this->sess->user_parent;
    $user_id_role    = $this->sess->user_id_role;
    $privilegecode   = $this->sess->user_id_role;
    $user_id         = $this->sess->user_id;
    $user_id_fix     = "";

    if($user_id == "1445"){
      $user_id_fix = $user_id;
    }else{
      $user_id_fix = $this->sess->user_id;
    }

    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no","asc");

    if($privilegecode == 0){
      $this->db->where("vehicle_user_id", $user_id_fix);
    }else if($privilegecode == 1){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 2){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 3){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 4){
      $this->db->where("vehicle_user_id", $user_parent);
    }else if($privilegecode == 5){
      $this->db->where("vehicle_company", $user_company);
    }else if($privilegecode == 6){
      $this->db->where("vehicle_company", $user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_mv03 !=", "0000");
    // $this->db->where_in("vehicle_type", array("MV03"));
    $this->db->where("vehicle_status <>", 3);
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
  }

  function getdetailreport($table, $alertid, $sdate){
    $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
    $this->dbalarm->where("alarm_report_vehicle_id", $alertid);
    $this->dbalarm->where("alarm_report_datetime_cr", $sdate);
    $this->dbalarm->group_by("alarm_report_datetime_cr");
    $q             = $this->dbalarm->get($table);
    return  $q->result_array();
  }



}
