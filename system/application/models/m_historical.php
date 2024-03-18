<?php
class M_historical extends Model {

  function getDataLocationHour($table, $date, $starttime, $endtime, $contractor){
    $user_level      = $this->sess->user_level;
    $user_parent     = $this->sess->user_parent;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
    $privilegecode 	 = $this->sess->user_id_role;
		$user_id_fix     = $this->sess->user_id;

    $this->db->webts = $this->load->database("webtracking_ts", true);
    $this->db->webts->select("*");

      if($privilegecode == 1){
  			$this->db->webts->where("location_report_vehicle_user_id", $user_parent);
  		}else if($privilegecode == 2){
  			$this->db->webts->where("location_report_vehicle_user_id", $user_parent);
  		}else if($privilegecode == 3){
  			$this->db->webts->where("location_report_vehicle_user_id", $user_parent);
  		}else if($privilegecode == 4){
  			$this->db->webts->where("location_report_vehicle_user_id", $user_parent);
  		}else if($privilegecode == 5){
  			$this->db->webts->where("location_report_vehicle_company", $user_company);
  		}else if($privilegecode == 6){
  			$this->db->webts->where("location_report_vehicle_company", $user_company);
  		}else if($privilegecode == 0){
  			$this->db->webts->where("location_report_vehicle_user_id", $user_id_fix);
  		}else{
  			$this->db->webts->where("location_report_vehicle_no",99999);
  		}

      if ($contractor != 0) {
        $this->db->webts->where("location_report_vehicle_company", $contractor);
      }

    // $this->db->webts->where("location_report_gps_date", $date);
    // $this->db->webts->where("location_report_gps_hour", $starttime);
    $this->db->webts->where("location_report_gps_time >=", $starttime);
    $this->db->webts->where("location_report_gps_time <=", $endtime);
    $this->db->webts->group_by("location_report_vehicle_no");
		return $this->db->webts->get($table)->result_array();
  }

  function getDataLocationHourByPool($table, $date, $starttime, $endtime, $contractor, $idpool){
    $user_level      = $this->sess->user_level;
    $user_parent     = $this->sess->user_parent;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
    $privilegecode 	 = $this->sess->user_id_role;
		$user_id_fix     = $this->sess->user_id;

    $this->db->webts = $this->load->database("webtracking_ts", true);
    $this->db->webts->select("*");

      if($privilegecode == 1){
  			$this->db->webts->where("location_report_vehicle_user_id", $user_parent);
  		}else if($privilegecode == 2){
  			$this->db->webts->where("location_report_vehicle_user_id", $user_parent);
  		}else if($privilegecode == 3){
  			$this->db->webts->where("location_report_vehicle_user_id", $user_parent);
  		}else if($privilegecode == 4){
  			$this->db->webts->where("location_report_vehicle_user_id", $user_parent);
  		}else if($privilegecode == 5){
  			$this->db->webts->where("location_report_vehicle_company", $user_company);
  		}else if($privilegecode == 6){
  			$this->db->webts->where("location_report_vehicle_company", $user_company);
  		}else if($privilegecode == 0){
  			$this->db->webts->where("location_report_vehicle_user_id", $user_id_fix);
  		}else{
  			$this->db->webts->where("location_report_vehicle_no",99999);
  		}

      if ($contractor != 0) {
        $this->db->webts->where("location_report_vehicle_company", $contractor);
      }

      $this->db->webts->where("location_report_street_id", $idpool);

    // $this->db->webts->where("location_report_gps_date", $date);
    // $this->db->webts->where("location_report_gps_hour", $starttime);
    $this->db->webts->where("location_report_gps_time >=", $starttime);
    $this->db->webts->where("location_report_gps_time <=", $endtime);
    $this->db->webts->group_by("location_report_vehicle_no");
		return $this->db->webts->get($table)->result_array();
  }


}
