<?php
class M_dashboardview extends Model {

  function masterabdiwatch(){
    $user_id         = 4543; //abdiwatch on webtracking_abditrack (POC)
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

		$this->dbaw = $this->load->database("webtracking_abdiwatch", TRUE);
		$this->dbaw->order_by("vehicle_no", "asc");
		$this->dbaw->where("vehicle_status <>", 3);

		/* if ($privilegecode == 0) {
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
		} else if ($privilegecode == 7) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else {
			$this->db->where("vehicle_no", 99999);
		} */
		$this->dbaw->where("vehicle_user_id", $user_id_fix);
		return $this->dbaw->get("vehicle")->result_array();
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
    }else if($user_id_role == 10){
      $this->db->where("vehicle_company", $user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_status <>", 3);
    $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_typeunit", 1);
    $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function getmastervehicleformapsstandard(){
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
    }else if($user_id_role == 10){
      $this->db->where("vehicle_company", $user_company);
    }else{
      $this->db->where("vehicle_no",99999);
    }

    $this->db->where("vehicle_status <>", 3);
    $this->db->where_in("vehicle_typeunit", array(0,1));
    $this->db->where("vehicle_gotohistory", 0);
    $this->db->where("vehicle_autocheck is not NULL");
    $q       = $this->db->get("vehicle");
    return $q->result_array();
  }

  function getalldata($table){
    $this->db->where("poi_creator_id", "4408");
    $this->db->where("poi_flag", 0);
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
			$this->db->where("vehicle_company", $user_company);
		}else if($privilegecode == 6){
			$this->db->where("vehicle_company", $user_company);
		}else if($privilegecode == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($privilegecode == 10){
			$this->db->where("vehicle_company", $user_company);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
    $this->db->where_in("vehicle_typeunit", array(0,1));

    if ($companyid != 0) {
      $this->db->where("vehicle_company", $companyid);
    }
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

  function getstreet_now($type)
	{
		$this->db->select("street_id, street_name");
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

  function getMapSettingByType($type){
    $this->dbts = $this->load->database("webtracking_ts", true);
    $this->dbts->select("*");
    $this->dbts->where("mapsetting_type", $type);
    $this->dbts->where("mapsetting_parent_id", 4408);
    $this->dbts->where("mapsetting_user_id", 4408); //$user_id
    $q          = $this->dbts->get("ts_mapsetting");
    return $q->result_array();
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

  function getAllCompany(){
    $this->db->select("company_id, company_name");
		$this->db->where("company_created_by", 4408);
    $this->db->where("company_flag", 0);
    $this->db->order_by("company_name", "ASC");
		$q    = $this->db->get("company");
		$rows = $q->result_array();
		return $rows;
  }

  function mastervehicle_livemonitoring($user_id){
    $this->db     = $this->load->database("default", true);
    $this->db->select("*");
    $this->db->where("user_id", $user_id);
    $data_user       = $this->db->get("user")->result();

    if($data_user[0]->user_id == "1445"){
      $user_id =  $data_user[0]->user_id; //tag
    }else{
      $user_id = $data_user[0]->user_id;
    }

    $user_level      = $data_user[0]->user_level;
		$user_company    = $data_user[0]->user_company;
		$user_subcompany = $data_user[0]->user_subcompany;
		$user_group      = $data_user[0]->user_group;
		$user_subgroup   = $data_user[0]->user_subgroup;
		$user_dblive 	   = $data_user[0]->user_dblive;
    $user_parent 	   = $data_user[0]->user_parent;
    $user_id_role 	 = $data_user[0]->user_id_role;
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

  function getcompany_byowner($data_user)
	{
    $privilegecode = $data_user[0]->user_id_role;
		$this->db->order_by("company_name","asc");

			if ($privilegecode == 0) {
				$this->db->where("company_created_by", $data_user[0]->user_id);
			}elseif($privilegecode == 1) {
				$this->db->where("company_created_by", $data_user[0]->user_parent);
			}elseif($privilegecode == 2){
				$this->db->where("company_created_by",$data_user[0]->user_parent);
			}elseif($privilegecode == 3){
				$this->db->where("company_created_by",$data_user[0]->user_parent);
			}elseif($privilegecode == 4){
				$this->db->where("company_created_by",$data_user[0]->user_parent);
			}elseif($privilegecode == 5){
				// $this->db->where("company_created_by",$data_user[0]->user_company);
					$this->db->where("company_id", $data_user[0]->user_company);
			}elseif($privilegecode == 6){
				// $this->db->where("company_created_by",$data_user[0]->user_company);
					$this->db->where("company_id", $data_user[0]->user_company);
			}elseif($privilegecode == 10){
					$this->db->where("company_id", $data_user[0]->user_parent);
			}elseif($data_user[0]->user_level == 3){
				$this->db->where("company_id",$data_user[0]->user_company);
			}else{
				$this->db->where("company_id",0);
			}

		$this->db->where("company_flag",0);
		$q = $this->db->get("company");
		$rows = $q->result();
		return $rows;
    }

    function gettotalstatus($data_user)
    {
      $this->db->order_by("vehicle_id","asc");
      $this->db->select("vehicle_id,vehicle_autocheck");

      $user_level      = $data_user[0]->user_level;
      $user_company    = $data_user[0]->user_company;
      $user_subcompany = $data_user[0]->user_subcompany;
      $user_group      = $data_user[0]->user_group;
      $user_subgroup   = $data_user[0]->user_subgroup;
      $user_parent     = $data_user[0]->user_parent;
      $user_id_role    = $data_user[0]->user_id_role;
      $user_id_fix     = $data_user[0]->user_id;
      $user_excavator  = $data_user[0]->user_excavator;
      //GET DATA FROM DB
      $this->db     = $this->load->database("default", true);
      $this->db->select("*");
      $this->db->order_by("vehicle_id","asc");

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
      }else if($user_id_role == 10){
        $this->db->where("vehicle_company", $user_company);
      }else{
        $this->db->where("vehicle_no",99999);
      }

      if ($user_excavator == 1) {
        $this->db->where("vehicle_typeunit", 1);
      }

      $this->db->where("vehicle_status <>", 3);
      //$this->db->where("vehicle_gotohistory", 0);
      //$this->db->where("vehicle_autocheck is not NULL");
      $q = $this->db->get("vehicle");
      $rows = $q->result();

      $total_p = 0;
      $total_k = 0;
      $total_m = 0;

      $total_on = 0;
      $total_off = 0;
      $total_nodata = 0;

      for($i=0; $i < count($rows); $i++)
      {

        $json = json_decode($rows[$i]->vehicle_autocheck);
        if(isset($json->auto_status)){
          if($json->auto_status == "P" ){
            $total_p = $total_p + 1;
          }
          if($json->auto_status == "K" ){
            $total_k = $total_k + 1;
          }
          if($json->auto_status == "M" ){
            $total_m = $total_m + 1;
          }
          if($json->auto_last_engine == "ON" ){
            $total_on = $total_on + 1;
          }
          if($json->auto_last_engine == "OFF" ){
            $total_off = $total_off + 1;
          }
          if($json->auto_last_engine == "NO DATA" ){
            $total_nodata = $total_nodata + 1;
          }
        }

      }
      return $total_p."|".$total_k."|".$total_m."|".count($rows)."|".$total_off."|".$total_on."|".$total_nodata;
      }

      function getvehicle_byownerforheatmap($data_user){
    		$user_level          = $data_user[0]->user_level;
    		$user_company        = $data_user[0]->user_company;
    		$user_subcompany     = $data_user[0]->user_subcompany;
    		$user_group          = $data_user[0]->user_group;
    		$user_subgroup       = $data_user[0]->user_subgroup;
    		$user_parent         = $data_user[0]->user_parent;
    		$user_id_role 			 = $data_user[0]->user_id_role;
    		$user_id_fix         = $data_user[0]->user_id;
    		//GET DATA FROM DB
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
    		}else{
    			$this->db->where("vehicle_no",99999);
    		}

    		$this->db->where("vehicle_status <>", 3);
    		$q = $this->db->get("vehicle");
    		$rows = $q->result();
    		return $rows;
    		}

        function vehicleactive($data_user){
          $user_level      = $data_user[0]->user_level;
          $user_company    = $data_user[0]->user_company;
          $user_subcompany = $data_user[0]->user_subcompany;
          $user_group      = $data_user[0]->user_group;
          $user_subgroup   = $data_user[0]->user_subgroup;
          $user_parent     = $data_user[0]->user_parent;
          $user_id_role    = $data_user[0]->user_id_role;
          $user_id_fix     = $data_user[0]->user_id;
          // ACTIVE DEVICE
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
          }else{
            $this->db->where("vehicle_no",99999);
          }

          $this->db->where("vehicle_status <>", 3);
          $q            = $this->db->get("vehicle");
          return $q->result_array();
        }

        function vehicleexpired($data_user){
          $user_level      = $data_user[0]->user_level;
          $user_company    = $data_user[0]->user_company;
          $user_subcompany = $data_user[0]->user_subcompany;
          $user_group      = $data_user[0]->user_group;
          $user_subgroup   = $data_user[0]->user_subgroup;
          $user_parent     = $data_user[0]->user_parent;
          $user_id_role    = $data_user[0]->user_id_role;
          $user_id_fix 		 = $data_user[0]->user_id;

          // EXPIRED DEVICE
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
          }else{
            $this->db->where("vehicle_no",99999);
          }
          $datenow       = date("Ymd");
          $this->db->where("vehicle_active_date2 <", $datenow);
          $q2            = $this->db->get("vehicle");
          return $q2->result_array();
        }

        function totaldevice($data_user){
          $user_level      = $data_user[0]->user_level;
          $user_company    = $data_user[0]->user_company;
          $user_subcompany = $data_user[0]->user_subcompany;
          $user_group      = $data_user[0]->user_group;
          $user_subgroup   = $data_user[0]->user_subgroup;
          $user_parent     = $data_user[0]->user_parent;
          $user_id_role    = $data_user[0]->user_id_role;
          $user_id_fix 		 = $data_user[0]->user_id;

          // TOTAL DEVICE
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
          }else{
            $this->db->where("vehicle_no",99999);
          }
          $q3             = $this->db->get("vehicle");
          return $q3->result_array();
        }





}
