<?php
class M_nonbib extends Model {

  function getmastervehicle(){
    $user_id         = $this->sess->user_id;
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$privilegecode   = $this->sess->user_id_role;
		$user_dblive 	   = $this->sess->user_dblive;
		$user_id_fix     = $user_id;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($privilegecode == 0) {
			$this->db->where("vehicle_user_id", $user_id_fix);
		} else if ($privilegecode == 1) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 2) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 3) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 4) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else if ($privilegecode == 5) {
			$this->db->where("vehicle_company", $user_company);
		} else if ($privilegecode == 6) {
			$this->db->where("vehicle_company", $user_company);
		} else {
			$this->db->where("vehicle_no", 99999);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q    = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getdatanonbibact($company, $vehicle, $sdate, $edate){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");

    $user_id      = $this->sess->user_id;
  	$user_company = $this->sess->user_company;
  	$user_parent  = $this->sess->user_parent;
  	$user_id_role = $this->sess->user_id_role;

  		if ($user_id_role == 0) {
  			$company = $company;
  		}elseif ($user_id_role == 1) {
  			$company = $company;
  		}elseif ($user_id_role == 2) {
  			$company = $company;
  		}elseif ($user_id_role == 3) {
  			$company = $company;
  		}elseif ($user_id_role == 4) {
  			$company = $company;
  		}elseif ($user_id_role == 5) {
  			$company = $user_company;
  		}elseif ($user_id_role == 6) {
  			$company = $user_company;
  		}

      if ($company != 0) {
        $this->dbts->where("outgeofence_report_vehicle_company", $company);
      }

      if ($vehicle != 0) {
        $this->dbts->where("outgeofence_report_vehicle_device", $vehicle);
      }

      $this->dbts->where("outgeofence_report_gps_time >=", $sdate);
      $this->dbts->where("outgeofence_report_gps_time <=", $edate);

    return $this->dbts->get("webtracking_ts_outgeofence")->result_array();
  }

  function nonbibact_dumping($company, $vehicle, $sdate, $edate){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");

    $user_id      = $this->sess->user_id;
  	$user_company = $this->sess->user_company;
  	$user_parent  = $this->sess->user_parent;
  	$user_id_role = $this->sess->user_id_role;

  		if ($user_id_role == 0) {
  			$company = $company;
  		}elseif ($user_id_role == 1) {
  			$company = $company;
  		}elseif ($user_id_role == 2) {
  			$company = $company;
  		}elseif ($user_id_role == 3) {
  			$company = $company;
  		}elseif ($user_id_role == 4) {
  			$company = $company;
  		}elseif ($user_id_role == 5) {
  			$company = $user_company;
  		}elseif ($user_id_role == 6) {
  			$company = $user_company;
  		}

      if ($company != 0) {
        $this->dbts->where("outgeofence_report_vehicle_company", $company);
      }

      if ($vehicle != 0) {
        $this->dbts->where("outgeofence_report_vehicle_device", $vehicle);
      }

      $this->dbts->where("outgeofence_report_dumping_time >=", $sdate);
      $this->dbts->where("outgeofence_report_dumping_time <=", $edate);

    return $this->dbts->get("webtracking_ts_outgeofence_dumping")->result_array();
  }

}
