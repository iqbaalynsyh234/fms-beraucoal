<?php
class M_violation extends Model {

  function getviolation($table, $sdate, $contractor, $alarmtypefromaster){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->select("*");

    if ($contractor != 0) {
      $this->dbalarm->where("violation_vehicle_companyid", $contractor);
    }

    if (sizeof($alarmtypefromaster) != 0) {
      $this->dbalarm->where_in("violation_type_id", $alarmtypefromaster);
    }

    $this->dbalarm->where("violation_status", 1);
    $this->dbalarm->where("violation_update >=", $sdate);  // REVISIAN TGL. 11-05-2022
    $this->dbalarm->order_by("violation_update", "DESC");
    $this->dbalarm->group_by("violation_update");
    $q        = $this->dbalarm->get($table);
    return  $q->result_array();
  }

  function getviolationhistorikal($table, $sdate, $edate, $contractor, $alarmtypefromaster, $limit){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if ($contractor != 0) {
      $this->dbtensor->where("violation_vehicle_companyid", $contractor);
    }

    if (sizeof($alarmtypefromaster) != 0) {
      $this->dbtensor->where_in("violation_type_id", $alarmtypefromaster);
    }

    // $this->dbtensor->where("violation_status", 1);
    $this->dbtensor->where("violation_update >=", $sdate);
    $this->dbtensor->where("violation_update <=", $edate);
    $this->dbtensor->order_by("violation_update", "DESC");
    $this->dbtensor->limit($limit);
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function getviolationhistorikal_type2($table, $limit, $contractor, $alarmtypefromaster){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if ($contractor != 0) {
      $this->dbtensor->where("violation_vehicle_companyid", $contractor);
    }

    if (sizeof($alarmtypefromaster) != 0) {
      $this->dbtensor->where_in("violation_type_id", $alarmtypefromaster);
    }

    // $this->dbtensor->where("violation_status", 1);
    // $this->dbtensor->where("violation_update >=", $sdate);
    // $this->dbtensor->where("violation_update <=", $edate);
    $this->dbtensor->order_by("violation_update", "DESC");
    $this->dbtensor->group_by("violation_vehicle_no");
    $this->dbtensor->group_by("violation_update");
    $this->dbtensor->limit($limit);
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  // TIPE 3 INI UNTUK GET HISTORIKAL YANG BELUM DIKIRIM DI TELE
  function getviolationhistorikal_type3($table, $sdate, $edate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    $this->dbtensor->where("violation_status_tele", 0);
    $this->dbtensor->where("violation_update >=", $sdate);
    $this->dbtensor->where("violation_update <=", $edate);
    $this->dbtensor->order_by("violation_update", "DESC");
    $this->dbtensor->group_by("violation_vehicle_no");
    $this->dbtensor->group_by("violation_update");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function get_overspeed_intensor($table, $limit, $contractor){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if ($contractor != 0) {
      $this->dbtensor->where("overspeed_report_vehicle_company", $contractor);
    }

    // $this->dbtensor->where("violation_status", 1);
    // $this->dbtensor->where("violation_update >=", $sdate);
    // $this->dbtensor->where("violation_update <=", $edate);
    $this->dbtensor->where("overspeed_report_speed_status", 1);
    $this->dbtensor->order_by("overspeed_report_gps_time", "DESC");
    // $this->dbtensor->group_by("violation_update");
    $this->dbtensor->limit($limit);
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function getviolationforcrontele($table, $sdate, $edate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");
    $this->dbtensor->where("violation_status_tele", 0);
    $this->dbtensor->where("violation_update >=", $sdate);
    $this->dbtensor->where("violation_update <=", $edate);
    $this->dbtensor->order_by("violation_update", "DESC");
    $this->dbtensor->group_by("violation_update");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function violationbackdate($table, $date){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");
    $this->dbtensor->where("DATE(violation_update)", $date);
    $this->dbtensor->where("violation_status_tele", 0);
    // $this->dbtensor->where("violation_position", "");
    $this->dbtensor->order_by("violation_update", "DESC");
    $this->dbtensor->group_by("violation_update");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function updateTeleStatusHistorikal($table, $violation_id, $data){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->where("violation_id", $violation_id);
    return $this->dbtensor->update($table, $data);
  }

  function updateTeleStatus($table, $violation_id, $data){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->where("violation_id", $violation_id);
    return $this->dbalarm->update($table, $data);
  }

  function getviolationbykm($table, $contractor, $lasttime_violation){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->select("*");

      if ($contractor != 0) {
        $this->dbalarm->where("violation_vehicle_companyid", $contractor);
      }

    $this->dbalarm->where("violation_status", 1);
    $this->dbalarm->where("violation_update >=", $lasttime_violation); // REVISIAN TGL. 11-05-2022
    $this->dbalarm->order_by("violation_update", "DESC");
    $this->dbalarm->group_by("violation_update");
    $q        = $this->dbalarm->get($table);
    return  $q->result_array();
  }

  // function getviolationbykm2($table, $contractor, $lasttime_violation){
  function getviolationbykm2($table, $contractor){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->select("*");

      if ($contractor != 0) {
        $this->dbalarm->where("violation_vehicle_companyid", $contractor);
      }

    $this->dbalarm->where("violation_status", 1);
    // $this->dbalarm->where("violation_update >=", $lasttime_violation); // REVISIAN TGL. 11-05-2022
    $this->dbalarm->order_by("violation_update", "DESC");
    $this->dbalarm->group_by("violation_update");
    $q        = $this->dbalarm->get($table);
    return  $q->result_array();
  }

  function getfrommaster($vdevice){
    //GET DATA FROM DB
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("vehicle_device", $vdevice);
    $this->db->where("vehicle_status <>", 3);
    $q       = $this->db->get("vehicle");
    return  $q->result_array();
  }

  function getviolationmaster()
	{
		$this->dbalarm = $this->load->database("webtracking_ts", true);
		$this->dbalarm->select("alarmmaster_id, alarmmaster_name");
    $this->dbalarm->where("alarmmaster_status", 1);
		$q        = $this->dbalarm->get("webtracking_ts_alarmmaster");
		return  $q->result_array();
	}

  function getalarmbytype($alarmtype){
    $this->dbalarm = $this->load->database("webtracking_ts", true);
    $this->dbalarm->select("*");
    $this->dbalarm->where("alarm_master_id", $alarmtype);
    $q        = $this->dbalarm->get("webtracking_ts_alarm");
    return  $q->result_array();
  }

  function get_overspeed_intensor_historikal($table, $vehicle, $contractor, $sdate, $edate){
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if ($contractor != 0) {
      // $this->dbtensor->where_in("overspeed_report_vehicle_company", $contractor);
      $this->dbtensor->where("overspeed_report_vehicle_company", $contractor);
    }

    if ($vehicle != 0) {
      $this->dbtensor->where("overspeed_report_vehicle_device", $vehicle);
    }

    $this->dbtensor->where("overspeed_report_gps_time >=", $sdate);
    $this->dbtensor->where("overspeed_report_gps_time <=", $edate);
    $this->dbtensor->where("overspeed_report_speed_status", 1);
    $this->dbtensor->order_by("overspeed_report_gps_time", "DESC");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
  }

  function getviolationhistorikal_type2_report($table, $vehicle, $contractor, $alarmtypefromaster, $sdate, $edate){
    // echo "<pre>";
		// var_dump($table.'-'. $vehicle.'-'. $contractor.'-'. $alarmtypefromaster.'-'. $sdate.'-'. $edate);die();
		// echo "<pre>";
    $this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");

    if ($vehicle != 0) {
      $this->dbtensor->where("violation_vehicle_device", $vehicle);
    }

    if ($contractor != 0 || $contractor != "all") {
      // $this->dbtensor->where_in("violation_vehicle_companyid", $contractor);
      $this->dbtensor->where("violation_vehicle_companyid", $contractor);
    }

    if (sizeof($alarmtypefromaster) != 0) {
      $this->dbtensor->where_in("violation_type_id", $alarmtypefromaster);
    }

    $this->dbtensor->where("violation_status", 1);
    // $this->dbtensor->where("violation_position != ", ""); // NANTI DIAKTIFIN KALO AUTOCHECK UDAH READY
    $this->dbtensor->where("violation_update >=", $sdate);
    $this->dbtensor->where("violation_update <=", $edate);
    $this->dbtensor->order_by("violation_update", "DESC");
    // $this->dbtensor->group_by("violation_vehicle_no");
    // $this->dbtensor->group_by("violation_update");
    $q        = $this->dbtensor->get($table);
    // $q        = $this->dbtensor->get("historikal_violation_desember_2022_bckp_15122022");
    return  $q->result_array();
  }

  function getMasterVehiclebycompany($company){
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
		// // var_dump($user_id_role.'-'.$user_level.'-'.$user_company.'-'.$user_subcompany.'-'.$user_group.'-'.$user_subgroup.'-'.$user_dblive.'-'.$user_id_fix);die();
    // var_dump($company);die();
		// echo "<pre>";

		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");

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
    // $this->db->where("vehicle_id_shareto != ", "");
		// $this->db->where("vehicle_gotohistory", 0);
		// $this->db->where("vehicle_autocheck is not NULL");

    if ($company != 0 || $company != "all") {
      $this->db->where("vehicle_company", $company);
      $this->db->or_where("vehicle_id_shareto", $company);
    }

    // $this->db->group_by("vehicle_company");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

}
