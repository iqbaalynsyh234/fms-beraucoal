<?php
class M_poipoolmaster extends Model {

  function getfromdblive($table, $dblive){
    $this->db->dblive = $this->load->database($dblive, true);
		$q                  = $this->db->dblive->get($table);
		return $result      = $q->result_array();
  }

  function searchdblivedata($table, $dblive, $vehicle_device){
    $this->db->dblive = $this->load->database($dblive, true);
    $this->db->dblive->select("*");
    $this->db->dblive->where("gps_name", $vehicle_device);
		$q                  = $this->db->dblive->get($table);
		return $result      = $q->result_array();
  }

  function getmastervehiclebyarea($companyid){
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
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_company", $companyid);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmastervehiclebycontractorforheatmap($companyid){
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
      $this->db->where("vehicle_user_id", 4408);
    }else if($privilegecode == 6){
      $this->db->where("vehicle_user_id", 4408);
    }else if($privilegecode == 0){
      $this->db->where("vehicle_user_id", $user_id_fix);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_status <>", 3);
    if ($companyid != 0) {
      $this->db->where("vehicle_company", $companyid);
    }
    $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
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



  function getmastervehicletest(){
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
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}

    $this->db->where("vehicle_no", "TA130 B9288TCN");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
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

  function getmastervehicle(){
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
      $this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
      $this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 7){
      $this->db->where("vehicle_company", $user_parent);
		}else if($user_id_role == 8){
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmasterofflinevehicle($companyid){
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
      $this->db->where("vehicle_company", $user_company);
    }else if($user_id_role == 6){
      $this->db->where("vehicle_company", $user_company);
    }else if($user_id_role == 7){
      $this->db->where("vehicle_company", $user_parent);
    }else if($user_id_role == 8){
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_status <>", 3);
    if ($companyid != 0) {
      $this->db->where("vehicle_company", $companyid);
    }
    // $this->db->where("vehicle_gotohistory", 1);
    // $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function getmastervehicle2(){
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
		$user_id_fix     = $user_id;

    // echo "<pre>";
		// var_dump($user_level.'-'.$user_company.'-'.$user_subcompany.'-'.$user_group.'-'.$user_subgroup.'-'.$user_dblive.'-'.$user_id_fix);die();
		// echo "<pre>";

		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else{
			$this->db->where("vehicle_no",99999);
		}

    $this->db->where("vehicle_device <>", "69969039633231@TK510");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmastervehiclebydevid($device_id){
    if($this->sess->user_id == "1445"){
      $user_id =  $this->sess->user_id; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

    $user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
    $user_parent         = $this->sess->user_parent;
    $user_id_role = $this->sess->user_id_role;
		$user_dblive 	       = $this->sess->user_dblive;
		$user_id_fix         = $user_id;
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
			$this->db->where("vehicle_group", $user_group);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else if($user_id_role == 10){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_device", $device_id);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmastervehiclebymv03($device_id){
    if($this->sess->user_id == "1445"){
      $user_id =  $this->sess->user_id; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

    $user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
    $user_parent         = $this->sess->user_parent;
    $user_id_role = $this->sess->user_id_role;
    $privilegecode       = $this->sess->user_id_role;
		$user_dblive 	       = $this->sess->user_dblive;
		$user_id_fix         = $user_id;

		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($privilegecode == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($privilegecode == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($privilegecode == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}else if($privilegecode == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($privilegecode == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else if($privilegecode == 6){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_mv03", $device_id);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getLastPosition($table, $dblive, $gps_name){
    // print_r("devicenya : ".$gps_name);
    $this->db->dblive = $this->load->database($dblive, true);
    // $this->db->dblive->select("gps_name, vehicle_autocheck");
    $this->db->dblive->where("gps_name", $gps_name);
		$q                  = $this->db->dblive->get($table);
		return $result      = $q->result_array();
  }

  function insert_data($table, $data){
    return $this->db->insert($table, $data);
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

  function update_date($table, $where, $id, $data){
    $this->db->where("poi_id", $id);
    return $this->db->update($table, $data);
  }

  function delete_data($table, $where, $iddelete, $data){
    $this->db->where($where, $iddelete);
    return $this->db->update($table, $data);
  }

  function getmastervehiclefivereport(){
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
    $user_id_fix     = $user_id;
    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->order_by("vehicle_no","asc");

    if($user_level == 1){
      $this->db->where("vehicle_user_id", $user_id_fix);
    }else if($user_level == 2){
      $this->db->where("vehicle_company", $user_company);
    }else if($user_level == 3){
      $this->db->where("vehicle_subcompany", $user_subcompany);
    }else if($user_level == 4){
      $this->db->where("vehicle_group", $user_group);
    }else if($user_level == 5){
      $this->db->where("vehicle_subgroup", $user_subgroup);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_status <>", 3);
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function searchmasterdata($table, $key){
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
    $this->db->where("vehicle_no", $key);

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

		$this->db->where("vehicle_status <>", 3);
		// $this->db->where("vehicle_gotohistory", 0);
		// $this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_user) {
    // echo "<pre>";
		// var_dump($longitude.'-'.$ew.'-'.$latitude.'-'.$ns.'-'.$vehicle_user);die();
		// echo "<pre>";
		$this->db = $this->load->database("default", true);
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
							AND (geofence_user = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_user);

		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{
			$row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }

		}else
        {
            return false;
        }

	}

  function getGeofence_location_other_live($longitude, $latitude, $vehicle_user, $userdblive) {
		$this->db = $this->load->database($userdblive, true);
		$lng = $longitude;
		$lat = $latitude;

		$sql = sprintf("
					SELECT 	geofence_name, geofence_id, geofence_speed, geofence_speed_muatan, geofence_type, geofence_speed_alias, geofence_speed_muatan_alias
					FROM 	%sgeofence
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
							AND (geofence_user = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_user);
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
      $total = $q->num_rows();
      $row   = $q->result();
      $data  = $row;
      return $data;
            // for ($i=0;$i<$total;$i++){
            // // $data = $row[$i]->geofence_name;
            // $data = $row[$i];
            // return $data;
            // }
		}else{
      return false;
    }

	}

  function getGeofence_location_other($longitude, $latitude, $vehicle_user) {
		$this->db = $this->load->database("default", true);
		$lng = $longitude;
		$lat = $latitude;

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
							AND (geofence_user = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_user);
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
			$row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }
		}else
        {
            return false;
        }

	}

  // DESTINATION MASTER
  function getdestinationbyid($table, $where, $v_device){
    $this->db->where($where, $v_device);
    $this->db->where("dest_endshowing_date", date("Y-m-d"));
		$q       = $this->db->get($table);
		return $q->result_array();
  }


  // TESTING FOR JS
  function getmastervehiclejs(){
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
		$user_id_fix     = $user_id;

    // $user_level      = 1;
		// $user_company    = 1806;
		// $user_subcompany = 0;
		// $user_group      = 0;
		// $user_subgroup   = 0;
		// $user_dblive 	   = "webtracking_gps_tag_live";
		// $user_id_fix     = 3212;
    // 1-48-0-0-0-webtracking_gps_powerblock_live-1147
    // 1-1806-0-0-0-webtracking_gps_tag_live-3212

    // echo "<pre>";
		// var_dump($user_level.'-'.$user_company.'-'.$user_subcompany.'-'.$user_group.'-'.$user_subgroup.'-'.$user_dblive.'-'.$user_id_fix);die();
		// echo "<pre>";

		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmuatanperkm($table, $userparent, $privilegecode, $userid){
  	$this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
      if ($privilegecode == 0) {
        $this->dbts->where("minidashboard_user_id", $userid);
      }else {
        $this->dbts->where("minidashboard_user_id", $userparent);
      }
    $this->dbts->where("minidashboard_type", "muatan");
    $this->dbts->order_by("minidashboard_created_date", "DESC");
    $this->dbts->group_by("minidashboard_created_date");
    $this->dbts->limit(1);
		$q          = $this->dbts->get($table);
		return $q->result_array();
  }

  function getkosonganperkm($table, $userparent, $privilegecode, $userid){
  	$this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
      if ($privilegecode == 0) {
        $this->dbts->where("minidashboard_user_id", $userid);
      }else {
        $this->dbts->where("minidashboard_user_id", $userparent);
      }
    $this->dbts->where("minidashboard_type", "kosongan");
    $this->dbts->order_by("minidashboard_created_date", "DESC");
    $this->dbts->group_by("minidashboard_created_date");
    $this->dbts->limit(1);
		$q          = $this->dbts->get($table);
		return $q->result_array();
  }

  function getvehicleinrom($table, $userparent, $privilege, $userid){
  	$this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
      if ($privilege == 0) {
        $this->dbts->where("minidashboard_user_id", $userid);
      }else {
        $this->dbts->where("minidashboard_user_id", $userparent);
      }
    $this->dbts->where("minidashboard_type", "rom");
    $this->dbts->order_by("minidashboard_created_date", "DESC");
    $this->dbts->group_by("minidashboard_created_date");
    $this->dbts->limit(1);
		$q          = $this->dbts->get($table);
		return $q->result_array();
  }

  function getvehicleinport($table, $userparent, $privilege, $userid){
  	$this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    if ($privilege == 0) {
      $this->dbts->where("minidashboard_user_id", $userid);
    }else {
      $this->dbts->where("minidashboard_user_id", $userparent);
    }
    $this->dbts->where("minidashboard_type", "port");
    $this->dbts->order_by("minidashboard_created_date", "DESC");
    $this->dbts->group_by("minidashboard_created_date");
    $this->dbts->limit(1);
		$q          = $this->dbts->get($table);
		return $q->result_array();
  }

  function getvehicleinpool($table, $userparent, $privilege, $userid){
  	$this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    if ($privilege == 0) {
      $this->dbts->where("minidashboard_user_id", $userid);
    }else {
      $this->dbts->where("minidashboard_user_id", $userparent);
    }
    $this->dbts->where("minidashboard_type", "pool_ws");
    $this->dbts->order_by("minidashboard_created_date", "DESC");
    $this->dbts->group_by("minidashboard_created_date");
    $this->dbts->limit(1);
		$q          = $this->dbts->get($table);
		return $q->result_array();
  }

  function getvehicleoutofhauling($table, $userparent, $privilege, $userid){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    if ($privilege == 0) {
      $this->dbts->where("minidashboard_user_id", $userid);
    }else {
      $this->dbts->where("minidashboard_user_id", $userparent);
    }
    $this->dbts->where("minidashboard_type", "outofhauling");
    $this->dbts->order_by("minidashboard_created_date", "DESC");
    $this->dbts->group_by("minidashboard_created_date");
    $this->dbts->limit(1);
		$q          = $this->dbts->get($table);
		return $q->result_array();
  }

  function update_common($database, $table, $where, $wherenya, $data){
    $this->dbts = $this->load->database($database, true);
    $this->dbts->where($where, $wherenya);
    return $this->dbts->update($table, $data);
  }

  function getmapsetting(){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("mapsetting_parent_id", 4408);
    $this->dbts->where("mapsetting_user_id", 4408); //$user_id
		$q          = $this->dbts->get("ts_mapsetting");
		return $q->result_array();
  }

  function getMapSettingByType($type){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("mapsetting_type", $type);
    $this->dbts->where("mapsetting_parent_id", 4408);
    $this->dbts->where("mapsetting_user_id", 4408); //$user_id
    $q          = $this->dbts->get("ts_mapsetting");
    return $q->result_array();
  }

  function getMapSettingByType_mapsetting($type){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("mapsetting_type", $type);
    $this->dbts->where("mapsetting_parent_id", 4408);
    $this->dbts->where("mapsetting_user_id", 4408); //$user_id
    $q          = $this->dbts->get("ts_mapsetting");
    return $q->result_array();
  }

  function getMapSettingRow($type, $name){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("mapsetting_type", $type);
    $this->dbts->where("mapsetting_name", $name);
    $this->dbts->where("mapsetting_parent_id", 4408);
    $this->dbts->where("mapsetting_user_id", 4408); //$user_id
    $q          = $this->dbts->get("ts_mapsetting");
    return $q->result_array();
  }

  function getalertalias($table){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("alerttype_flag", 0);
		$q          = $this->dbts->get($table);
		return $q->result_array();
  }

  function getalertaliasnameonly($table){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("alerttype_name");
    $this->dbts->where("alerttype_flag", 0);
		$q          = $this->dbts->get($table);
		return $q->result_array();
  }

  function getalertnow($database, $table, $vehicle, $alertype, $sdate, $edate, $alertarray){
    $this->dbwilive = $this->load->database($database, true);

      if ($vehicle != "all") {
        $device = explode("@", $vehicle);
        $this->dbwilive->where("gps_name", $device[0]);
        $this->dbwilive->where("gps_host", $device[1]);
      }

      if ($alertype != "all") {
        $this->dbwilive->where("gps_alert", $alertype);
      }else {
        $this->dbwilive->where_in("gps_alert", $alertarray);
      }

    $this->dbwilive->where("gps_time >= ", $sdate);
    $this->dbwilive->where("gps_time <= ", $edate);
    return $this->dbwilive->get($table)->result_array();
  }

  function searchthisabsensi($table, $driver, $sdate, $edate, $shifttype){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
      if ($driver != "all") {
        $this->dbts->where("absensi_driver_id", $driver);
      }

      if ($shifttype != "all") {
        $this->dbts->where("absensi_shift_type", $shifttype);
      }

    $this->dbts->where("absensi_driver_time >=", $sdate);
    $this->dbts->where("absensi_driver_time <=", $edate);
    $this->dbts->where("absensi_flag", 0);
    $this->dbts->order_by("absensi_id", "DESC");
		$q          = $this->dbts->get($table);
		return $q->result_array();
  }

  function searchthisbreakdown($table, $driver, $vehicle, $sdate, $edate){
    $privilegecode = $this->sess->user_id_role;
    $user_id       = $this->sess->user_id;
    $user_parent   = $this->sess->user_parent;
    $user_company  = $this->sess->user_company;

    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
      if ($driver != "all") {
        $this->dbts->where("breakdown_driver_id", $driver);
      }

      if ($vehicle != "all") {
        $this->dbts->where("breakdown_vehicle_device", $vehicle);
      }

      if ($privilegecode == 5 || $privilegecode == 6) {
        $this->dbts->where("breakdown_creator_id", $user_company);
        $this->dbts->or_where("breakdown_creator_id", $user_id);
      }

		$this->dbts->where("breakdown_start_time >=", $sdate);
		$this->dbts->where("breakdown_start_time <=", $edate);
		$this->dbts->where("breakdown_flag", 0);
		$this->dbts->order_by("breakdown_id", "DESC");
		$q          = $this->dbts->get($table);
		return $q->result_array();
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
    $this->db->where("street_flag", 1);
		$q = $this->db->get("street");
		$rows = $q->result_array();
		return $rows;
  }

  function getstreet_now_mapsetting($type)
	{
		$this->db->select("street_id, street_name");
      if ($type == 3) {
        $this->db->where_in("street_type", array(3, 5));
      }elseif ($type == 1) {
        $this->db->where("street_type", $type);
      }elseif ($type == 4) {
        $this->db->where_in("street_type", array(4, 7, 8));
      }elseif ($type == 9) {
        $this->db->where("street_type", $type);
      }else {
        $this->db->where("street_type", $type);
      }
    $this->db->order_by("street_order", "ASC");
		$this->db->where("street_creator", 4408);
    $this->db->where("street_flag", 1);
		$q = $this->db->get("street");
		$rows = $q->result_array();
		return $rows;
  }

  function getstreet_now2($type)
  {
    $this->db     = $this->load->database("default", true);
    $this->db->select("street_id, street_name");
      if ($type == 3) {
        $this->db->where_in("street_type", 3);
      }elseif ($type == 1) {
        $this->db->where("street_type", $type);
      }elseif ($type == 4) {
        $this->db->where_in("street_id", array(9839, 9335));
      }else {
        $this->db->where("street_type", $type);
      }
    $this->db->order_by("street_order", "ASC");
    $this->db->where("street_creator", 4408);
    $this->db->where("street_flag", 1);
    $q = $this->db->get("street");
    $rows = $q->result_array();
    return $rows;
  }

  function getstreet_now_byparent($parent){
    $this->db->select("street_id, street_name, street_alias");
    $this->db->where_in("street_company_parent", $parent);
    $this->db->order_by("street_order", "ASC");
		$this->db->where("street_creator", 4408);
    $this->db->where("street_flag", 1);
		$q = $this->db->get("street");
		$rows = $q->result_array();
		return $rows;
  }

  function getAllCompany(){
    $this->db->select("company_id, company_name");
		$this->db->where("company_created_by", 4408);
    $this->db->where("company_flag", 0);
    $this->db->order_by("company_name", "ASC");
		$q    = $this->db->get("company");
		$rows = $q->result_array();
		return $rows;
  }

  function checkThisMapSetting($mapsetting_name, $mapsetting_limit_name){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("mapsetting_name", $mapsetting_name);
    $this->dbts->where("mapsetting_limit_name", $mapsetting_limit_name); //$user_id
    $q          = $this->dbts->get("ts_mapsetting");
    return $q->result_array();
  }

  function insert_data_webtracking_ts($table, $data){
    $this->dbts = $this->load->database("webtracking_ts", true);
    return $this->dbts->insert($table, $data);
  }

  function update_data_webtracking_ts($table, $where, $id, $data){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->where($where, $id);
    return $this->dbts->update($table, $data);
  }

  function getThisMapSettingByLimitName($middle_limitname, $top_limitname){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("mapsetting_limit_name", $middle_limitname);
    $this->dbts->or_where("mapsetting_limit_name", $top_limitname);
    $this->dbts->where("mapsetting_parent_id", 4408);
    $this->dbts->where("mapsetting_user_id", 4408); //$user_id
    $q          = $this->dbts->get("ts_mapsetting");
    return $q->result_array();
  }

  function getThisMapSettingByLimitName_mapsetting($middle_limitname, $top_limitname){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("mapsetting_limit_name", $middle_limitname);
    $this->dbts->or_where("mapsetting_limit_name", $top_limitname);
    $this->dbts->where("mapsetting_parent_id", 4408);
    $this->dbts->where("mapsetting_user_id", 4408); //$user_id
    $q          = $this->dbts->get("ts_mapsetting");
    return $q->result_array();
  }

  function getThisMapSettingByLimitName_mapsetting_onlykm1($bottom_limitname, $middle_limitname, $top_limitname){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where_in("mapsetting_limit_name", array($bottom_limitname, $middle_limitname, $top_limitname));
    $this->dbts->where("mapsetting_parent_id", 4408);
    $this->dbts->where("mapsetting_user_id", 4408); //$user_id
    $q          = $this->dbts->get("ts_mapsetting");
    return $q->result_array();
  }

  function getStreetByParent($parentID){
		$this->db->select("street_name");
    $this->db->where("street_company_parent", $parentID);
    $this->db->where("street_type", 2);
		$this->db->where("street_creator", 4408);
    $this->db->where("street_flag", 1);
    $this->db->order_by("street_order", "ASC");
		$q = $this->db->get("street");
		$rows = $q->result_array();
		return $rows;
  }

  // ADMIN ONLY
  function mastervehicleadminonly(){
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

  function getmastervehiclebycontractoradminonly($companyid){
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
    if ($companyid != 0) {
      $this->db->where("vehicle_company", $companyid);
    }
    $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function getmastervehicleforbibvehicle(){
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
		}else{
			$this->db->where("vehicle_no",99999);
		}

    $this->db->where("vehicle_typeunit", 2);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmastervehiclebib(){
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
      $this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
      $this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 7){
      $this->db->where("vehicle_company", $user_parent);
		}else if($user_id_role == 8){
      $this->db->where("vehicle_user_id", $this->sess->user_parent);
		}else{
			$this->db->where("vehicle_no",99999);
		}

    $this->db->where("vehicle_typeunit", 2);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmastervehiclebibbycontractor($companyid){
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

    $this->db->where("vehicle_typeunit", 2);
		$this->db->where("vehicle_status <>", 3);
    if ($companyid != 0) {
      $this->db->where("vehicle_company", $companyid);
    }
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmastervehiclebibbydevid($device_id){
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
			$this->db->where("vehicle_group", $user_group);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else if($user_id_role == 10){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_device", $device_id);
    $this->db->where("vehicle_typeunit", 2);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getmastervehicleHRM(){
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
      $this->db->where("vehicle_company", 1963);
    }else if($user_id_role == 1){
      $this->db->where("vehicle_company", 1963);
    }else if($user_id_role == 2){
      $this->db->where("vehicle_company", 1963);
    }else if($user_id_role == 3){
      $this->db->where("vehicle_company", 1963);
    }else if($user_id_role == 4){
      $this->db->where("vehicle_company", 1963);
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


















}
