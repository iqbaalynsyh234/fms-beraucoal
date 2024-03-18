<?php
include "base.php";

class Overspeedreport extends Base {
	var $otherdb;

	function Overspeedreport()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
		$this->load->model("m_report");
	}

	function index()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$user_id             = $this->sess->user_id;
		$user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
		$user_parent         = $this->sess->user_parent;
		$user_id_role        = $this->sess->user_id_role;
		$privilegecode			 = $this->sess->user_id_role;
		$user_dblive 	       = $this->sess->user_dblive;
		$user_id_fix         = $user_id;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

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

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows                           = $q->result();
		$rows_company                   = $this->get_company_bylevel();
		$rows_geofence                  = $this->get_geofence_bydblive($user_dblive);//print_r($rows_geofence);exit();

		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params["rgeofence"]      = $rows_geofence;
		$this->params['code_view_menu'] = "report";


		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function search_overspeedreport()
  {
    ini_set('display_errors', 1);
    ini_set('memory_limit', '6G');
		ini_set('max_execution_time', '1000');
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }
    $company           = $this->input->post("company");
    $vehicle           = $this->input->post("vehicle");
    $startdate         = $this->input->post("startdate");
    $enddate           = $this->input->post("enddate");
    $shour             = $this->input->post("shour");
    $ehour             = $this->input->post("ehour");
    $jalur             = $this->input->post("jalur");
    $geofence          = $this->input->post("geofence");
		$rambu             = $this->input->post("rambu");
    $periode           = $this->input->post("periode");

    $km_checkbox       = $this->input->post("km_checkbox");
    $kmselected_select = $this->input->post("kmselected_select");

    $kmstart           = $this->input->post("kmstart");
    $kmend             = $this->input->post("kmend");

    $datakm = array();
    if ($km_checkbox == 1) {
      // KONDISI RANGE KM OVERSPEED UI BARU
        if ($kmstart == "all") {
          $datakm[] = "KM ";
        }else {
          if ($kmend == 0) {
            // $datakm[] = "KM " . $kmstart;
            for ($i=$kmstart; $i <= $kmstart; $i++) {
              $datakm[] = "KM " . $i;
            }
          }else {
            for ($i=$kmstart; $i <= $kmend; $i ++) {
              $datakm[] = "KM " . $i;
            }
          }
        }
        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_report->similarkminstreet($datakm[$j]);
            for ($k=0; $k < sizeof($get_similar_km); $k++) {
              $street_group = $get_similar_km[$k]['street_group'];
                if ($street_group == $datakm[$j]) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($datakm, $datakmfix);
    }else {
      // KONDISI SELECTED KM OVERSPEED UI BARU
      $data_selected_kmfix = array();
      if ($kmselected_select == false) {
        $datakm = array("KM ", "TIA KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_report->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }else {
              $get_similar_km = $this->m_report->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "all") {
        $datakm = array("KM ", "TIA KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_report->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }else {
              $get_similar_km = $this->m_report->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "allkm") {
        $datakm = array("KM ");

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_report->similarkminstreet($datakm[$j]);
              for ($k=0; $k < sizeof($get_similar_km); $k++) {
                $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
              }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }elseif ($kmselected_select[0] == "alltia") {
        $datakm = array("TIA KM ");
        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            $get_similar_km = $this->m_report->similarkminstreet($datakm[$j]);
              for ($k=0; $k < sizeof($get_similar_km); $k++) {
                $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
              }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }else {
        for ($k=0; $k < sizeof($kmselected_select); $k++) {
            $data_selected_kmfix[] = $kmselected_select[$k];
        }
        // $data_merge = array_merge($data_selected_kmfix, $increase_selected_km);
        $datakm = $data_selected_kmfix;

        $datakmfix = array();
          for ($j=0; $j < sizeof($datakm); $j++) {
            if (strpos($datakm[$j], "TIA") !== FALSE) {
              $get_similar_km = $this->m_report->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $streetnameexpl = explode("TIA ", $datakm[$j]);
                  $streetnamefix  = $streetnameexpl[1];
                  $street_group   = $get_similar_km[$k]['street_group'];
                    if ($street_group == $streetnamefix) {
                      $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                    }
                }
            }else {
              $get_similar_km = $this->m_report->similarkminstreet($datakm[$j]);
                for ($k=0; $k < sizeof($get_similar_km); $k++) {
                  $street_group = $get_similar_km[$k]['street_group'];
                    if ($street_group == $datakm[$j]) {
                      $datakmfix[] = str_replace(",", "", $get_similar_km[$k]['street_name']);
                    }
                }
            }
          }
          $datakm = array_merge($data_selected_kmfix, $datakmfix);
      }
    }


      // echo "<pre>";
      // var_dump($datakm);die();
      // // var_dump($kmstart.'-'.$kmend);die();
      // echo "<pre>";

    // $km 				= $this->input->post("km");


    $nowdate    = date("Y-m-d");
    $nowday     = date("d");
    $nowmonth   = date("m");
    $nowyear    = date("Y");
    $lastday    = date("t");

    $report     = "overspeed_";
    $report_sum = "summary_";

    if($periode == "custom"){
      $sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
      $edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
    }else if($periode == "yesterday"){

      $sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
      $edate = date("Y-m-d 23:59:59", strtotime("yesterday"));

    }else if($periode == "last7"){
      $nowday = $nowday - 1;
      $firstday = $nowday - 7;
      if($nowday <= 7){
        $firstday = 1;
      }

      /*if($firstday > $nowday){
        $firstday = 1;
      }*/

      $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
      $edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59"));

    }
    else if($periode == "last30"){
      $firstday = "1";
      $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
      $edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59"));
    }
    else{
      $sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
      $edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
    }

    //print_r($sdate." ".$edate);exit();

    $m1 = date("F", strtotime($sdate));
    $m2 = date("F", strtotime($edate));
    $year = date("Y", strtotime($sdate));
    $year2 = date("Y", strtotime($edate));
    $rows = array();
    $total_q = 0;

    $error = "";
    $rows_summary = "";

    if ($vehicle == "")
    {
      $error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
    }
    if ($m1 != $m2)
    {
      $error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
    }

    if ($year != $year2)
    {
      $error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
    }

    if ($error != "")
    {
      $callback['error'] = true;
      $callback['message'] = $error;

      echo json_encode($callback);
      return;
    }

    switch ($m1)
    {
      case "January":
            $dbtable = $report."januari_".$year;
      $dbtable_sum = $report_sum."januari_".$year;
      break;
      case "February":
            $dbtable = $report."februari_".$year;
      $dbtable_sum = $report_sum."februari_".$year;
      break;
      case "March":
            $dbtable = $report."maret_".$year;
      $dbtable_sum = $report_sum."maret_".$year;
      break;
      case "April":
            $dbtable = $report."april_".$year;
      $dbtable_sum = $report_sum."april_".$year;
      break;
      case "May":
            $dbtable = $report."mei_".$year;
      $dbtable_sum = $report_sum."mei_".$year;
      break;
      case "June":
            $dbtable = $report."juni_".$year;
      $dbtable_sum = $report_sum."juni_".$year;
      break;
      case "July":
            $dbtable = $report."juli_".$year;
      $dbtable_sum = $report_sum."juli_".$year;
      break;
      case "August":
            $dbtable = $report."agustus_".$year;
      $dbtable_sum = $report_sum."agustus_".$year;
      break;
      case "September":
            $dbtable = $report."september_".$year;
      $dbtable_sum = $report_sum."september_".$year;
      break;
      case "October":
            $dbtable = $report."oktober_".$year;
      $dbtable_sum = $report_sum."oktober_".$year;
      break;
      case "November":
            $dbtable = $report."november_".$year;
      $dbtable_sum = $report_sum."november_".$year;
      break;
      case "December":
            $dbtable = $report."desember_".$year;
      $dbtable_sum = $report_sum."desember_".$year;
      break;
    }

    // echo "<pre>";
    // var_dump($dbtable.'-'.$vehicle.'-'.$sdate.'-'.$edate.'-'.$reporttype.'-'.$company.'-'.$jalur.'-'.$geofence.'-'.$km);die();
    // echo "<pre>";

    //get vehicle
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

      $this->dbtrip = $this->load->database("webtracking_kalimantan",true);
      //$this->dbtrip->order_by("overspeed_report_vehicle_no","asc");
      $this->dbtrip->order_by("overspeed_report_gps_time","asc");
      if($vehicle == "0"){
        if($privilegecode == 0){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_id_fix);
        }else if($privilegecode == 1){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 2){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 3){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 4){
          $this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
        }else if($privilegecode == 5){
          $this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
        }else if($privilegecode == 6){
          $this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
        }else{
          $this->dbtrip->where("overspeed_report_vehicle_company",99999);
        }
        $this->dbtrip->where("overspeed_report_vehicle_id <>",72150933); //jika pilih all bukan mobil trial
      }else{
        $this->dbtrip->where("overspeed_report_vehicle_device", $vehicle);
      }

      $this->dbtrip->where("overspeed_report_gps_time >=",$sdate);
      $this->dbtrip->where("overspeed_report_gps_time <=", $edate);
      $this->dbtrip->where("overspeed_report_speed_status", 1); //valid data
      $this->dbtrip->where("overspeed_report_geofence_type", "road"); //khusus dijalan
      // $this->dbtrip->where("overspeed_report_type", $reporttype); //data fix (default) = 0
      $this->dbtrip->where("overspeed_report_type", 0); //data fix (default) = 0


      if($company != "all"){
        $this->dbtrip->where("overspeed_report_vehicle_company", $company);
      }

      if($jalur != "all"){
        $this->dbtrip->where("overspeed_report_jalur", $jalur);
      }

      if($geofence != "all"){
        $this->dbtrip->where("overspeed_report_geofence_name", $geofence);
      }

      if($rambu != "all"){
      	$this->dbtrip->where("overspeed_report_geofence_limit >= ", $rambu);
      }

      if($datakm[0] != "KM "){
        $this->dbtrip->where_in("overspeed_report_location", $datakm);
      }
      $this->dbtrip->where("overspeed_report_event_status",1);
      $q = $this->dbtrip->get($dbtable);

      if ($q->num_rows>0)
      {
        $rows = $q->result();
      }else{
        $error .= "- No Data Overspeed ! \n";
      }

    if ($error != "")
    {
      $callback['error'] = true;
      $callback['message'] = $error;

      echo json_encode($callback);
      return;
    }

    $params['data']      = $rows;
    $params['dbtable']   = $dbtable;
    $params['startdate'] = $sdate;
    $params['enddate']   = $edate;

    $html = $this->load->view("newdashboard/report/vspeed_result", $params, true);

    $callback['error'] = false;
    $callback['html'] = $html;
    echo json_encode($callback);
    //return;

  }

	function create_half_loop(){
    $kmstart = $_POST['kmstart'];
    $kmendhtml = "<option value='0'>--Select KM End</option>";
      for ($i=($kmstart+1); $i <= 35; $i++) {
         $kmendhtml .= "<option value=".$i.">KM ".$i."</option>";
       }
       echo $kmendhtml;
 			return;
  }

	function index_old()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$user_id             = $this->sess->user_id;
		$user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
		$user_parent         = $this->sess->user_parent;
		$user_id_role        = $this->sess->user_id_role;
		$privilegecode			 = $this->sess->user_id_role;
		$user_dblive 	       = $this->sess->user_dblive;
		$user_id_fix         = $user_id;

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

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

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows                           = $q->result();
		$rows_company                   = $this->get_company_bylevel();
		$rows_geofence                  = $this->get_geofence_bydblive($user_dblive);//print_r($rows_geofence);exit();

		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params["rgeofence"]      = $rows_geofence;
		$this->params['code_view_menu'] = "report";


		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vspeed_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function search_old()
	{
		ini_set('display_errors', 1);
		//ini_set('memory_limit', '2G');
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$company    = $this->input->post("company");
		$vehicle    = $this->input->post("vehicle");
		$startdate  = $this->input->post("startdate");
		$enddate    = $this->input->post("enddate");
		$shour      = $this->input->post("shour");
		$ehour      = $this->input->post("ehour");
		$jalur      = $this->input->post("jalur");
		$geofence   = $this->input->post("geofence");
		$periode    = $this->input->post("periode");
		// $reporttype = $this->input->post("reporttype");

		$km         = explode(",",$this->input->post("km"));
		$datakm = array();
			if (sizeof($km) > 1) {
				for ($x=0; $x < sizeof($km); $x++) {
					$datakm[$x] = "KM " . $km[$x];
				}
			}else {
				$datakm[] = "KM " . $km[0];
			}

			// echo "<pre>";
			// var_dump($datakm);die();
			// echo "<pre>";

		// $km 				= $this->input->post("km");


		$nowdate    = date("Y-m-d");
		$nowday     = date("d");
		$nowmonth   = date("m");
		$nowyear    = date("Y");
		$lastday    = date("t");

		$report     = "overspeed_";
		$report_sum = "summary_";

		if($periode == "custom"){
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}else if($periode == "yesterday"){

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));

		}else if($periode == "last7"){
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if($nowday <= 7){
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59"));

		}
		else if($periode == "last30"){
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59"));
		}
		else{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		$rows = array();
		$total_q = 0;

		$error = "";
		$rows_summary = "";

		if ($vehicle == "")
		{
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		}
		if ($m1 != $m2)
		{
			$error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
		}

		if ($year != $year2)
		{
			$error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
		}

		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_sum = $report_sum."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_sum = $report_sum."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_sum = $report_sum."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_sum = $report_sum."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_sum = $report_sum."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_sum = $report_sum."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_sum = $report_sum."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_sum = $report_sum."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_sum = $report_sum."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_sum = $report_sum."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_sum = $report_sum."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_sum = $report_sum."desember_".$year;
			break;
		}

		// echo "<pre>";
		// var_dump($dbtable.'-'.$vehicle.'-'.$sdate.'-'.$edate.'-'.$reporttype.'-'.$company.'-'.$jalur.'-'.$geofence.'-'.$km);die();
		// echo "<pre>";

		//get vehicle
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

			$this->dbtrip = $this->load->database("webtracking_kalimantan",true);
			//$this->dbtrip->order_by("overspeed_report_vehicle_no","asc");
			$this->dbtrip->order_by("overspeed_report_gps_time","asc");
			if($vehicle == "0"){
				if($privilegecode == 0){
					$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_id_fix);
				}else if($privilegecode == 1){
					$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
				}else if($privilegecode == 2){
					$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
				}else if($privilegecode == 3){
					$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
				}else if($privilegecode == 4){
					$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
				}else if($privilegecode == 5){
					$this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
				}else if($privilegecode == 6){
					$this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
				}else{
					$this->dbtrip->where("overspeed_report_vehicle_company",99999);
				}
				$this->dbtrip->where("overspeed_report_vehicle_id <>",72150933); //jika pilih all bukan mobil trial
			}else{
				$this->dbtrip->where("overspeed_report_vehicle_device", $vehicle);
			}

			$this->dbtrip->where("overspeed_report_gps_time >=",$sdate);
			$this->dbtrip->where("overspeed_report_gps_time <=", $edate);
			$this->dbtrip->where("overspeed_report_speed_status", 1); //valid data
			$this->dbtrip->where("overspeed_report_geofence_type", "road"); //khusus dijalan
			// $this->dbtrip->where("overspeed_report_type", $reporttype); //data fix (default) = 0
			$this->dbtrip->where("overspeed_report_type", 0); //data fix (default) = 0


			if($company != "all"){
				$this->dbtrip->where("overspeed_report_vehicle_company", $company);
			}

			if($jalur != "all"){
				$this->dbtrip->where("overspeed_report_jalur", $jalur);
			}

			if($geofence != "all"){
				$this->dbtrip->where("overspeed_report_geofence_name", $geofence);
			}
			// if($km != ""){
			// 	$this->dbtrip->where_in("overspeed_report_location", "KM ".$km);
			// }

			if($datakm[0] != "KM "){
				$this->dbtrip->where_in("overspeed_report_location", $datakm);
			}
			$this->dbtrip->where("overspeed_report_event_status",1);
			$q = $this->dbtrip->get($dbtable);

			if ($q->num_rows>0)
			{
				$rows = $q->result();
			}else{
				$error .= "- No Data Overspeed ! \n";
			}

		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$params['data']      = $rows;
		$params['dbtable']   = $dbtable;
		$params['startdate'] = $sdate;
		$params['enddate']   = $edate;

		$html = $this->load->view("newdashboard/report/vspeed_result", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;

	}

	function getDistanceBetween($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi')
	{
		$theta = $longitude1 - $longitude2;
		$distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)))  + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
		$distance = acos($distance);
		$distance = rad2deg($distance);
		$distance = $distance * 60 * 1.1515;
		switch($unit)
		{
			case 'Mi': break;
			case 'Km' : $distance = $distance * 1.609344;
		}
		return (round($distance,2));
	}

	function get_company_all(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}
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
			}
		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}

	function get_geofence_bydblive($dblive){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->dblive = $this->load->database($dblive,true);
		$this->dblive->select("geofence_name");
		$this->dblive->order_by("geofence_name","asc");
		$this->dblive->where("geofence_user", 4408); //khusus bib
		$this->dblive->where("geofence_status", 1);
		$this->dblive->where("geofence_type", "road");
		$qd = $this->dblive->get("geofence");
		$rd = $qd->result();

		return $rd;
	}

}
