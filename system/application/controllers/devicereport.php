<?php
include "base.php";

class Devicereport extends Base
{
    function __construct()
    {
        parent::Base();
        $this->load->model("m_devicereport");
        $this->load->model("dashboardmodel");
    }

    function mdvrreportstatus(){
      $privilegecode   = $this->sess->user_id_role;

      $rows           = $this->m_devicereport->getdevice();

      // echo "<pre>";
    	// var_dump($rows);die();
    	// echo "<pre>";

      $rows_company                   = $this->get_company();
      $this->params["vehicles"]       = $rows;
      $this->params["vehicledata"]       = $rows;
      $this->params["rcompany"]       = $rows_company;
      $this->params['code_view_menu'] = "report";

      $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
      $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

      if ($privilegecode == 1) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_summarymdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
      } elseif ($privilegecode == 2) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_summarymdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
      } elseif ($privilegecode == 3) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_summarymdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
      } elseif ($privilegecode == 4) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_summarymdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
      } elseif ($privilegecode == 5) {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_summarymdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
      } else {
          $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
          $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_summarymdvr', $this->params, true);
          $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
      }
    }

    function search_summarymdvr(){
      $privilegecode   = $this->sess->user_id_role;
        if ($privilegecode == 5 || $privilegecode == 6) {
          $company = $this->sess->user_company;
        }else {
          $company          = $this->input->post("company");
        }

      $vehicle          = $this->input->post("vehicle");
      $frekuensianomali = $this->input->post("frekuensianomali");
      $startdate        = $this->input->post("startdate");
      // $shour         = "00:00:00";
      $enddate          = $this->input->post("enddate");
      // $ehour         = "23:59:59";
      $periode          = $this->input->post("periode");

      // $sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
      // $edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

      $sdate2 = date("Y-m-d H:i:s", strtotime($startdate));
      $edate2 = date("Y-m-d H:i:s", strtotime($startdate));

      $nowdate   = date("Y-m-d");
      $nowday    = date("d");
      $nowmonth  = date("m");
      $nowyear   = date("Y");
      $lastday   = date("t");

        if($periode == "custom"){
          $sdate = date("Y-m-d", strtotime($startdate));
          $edate = date("Y-m-d", strtotime($enddate));
        }else if($periode == "yesterday"){
          $sdate = date("Y-m-d", strtotime("yesterday"));
          $edate = date("Y-m-d", strtotime("yesterday"));
        }else if($periode == "last7"){
          $sdate = date("Y-m-d", strtotime($sdate2 . "-7days"));
          $edate = date("Y-m-d", strtotime($startdate));
        }else if($periode == "last30"){
          $sdate = date("Y-m-01", strtotime($startdate));
          $edate = date("Y-m-d", strtotime($startdate));
        }else if($periode == "today"){
          $sdate1 = date("Y-m-d");
          $sdate2 = "00:00:00";

          $edate1 = date("Y-m-d");
          $edate2 = "23:59:59";

          $sdate = $sdate1;
          $edate = $edate1;
        }else{
          $sdate = date("Y-m-d", strtotime($startdate));
          $edate = date("Y-m-d", strtotime($enddate));
        }

      $m1     = date("F", strtotime($sdate));
      $year   = date("Y", strtotime($sdate));
      $report = "report_device_status_summary_";

      switch ($m1)
      {
        case "January":
              $dbtable = $report."januari_".$year;
        break;
        case "February":
              $dbtable = $report."februari_".$year;
        break;
        case "March":
              $dbtable = $report."maret_".$year;
        break;
        case "April":
              $dbtable = $report."april_".$year;
        break;
        case "May":
              $dbtable = $report."mei_".$year;
        break;
        case "June":
              $dbtable = $report."juni_".$year;
        break;
        case "July":
              $dbtable = $report."juli_".$year;
        break;
        case "August":
              $dbtable = $report."agustus_".$year;
        break;
        case "September":
              $dbtable = $report."september_".$year;
        break;
        case "October":
              $dbtable = $report."oktober_".$year;
        break;
        case "November":
              $dbtable = $report."november_".$year;
        break;
        case "December":
              $dbtable = $report."desember_".$year;
        break;
      }

        $data_company = $this->get_company_bylevel();

      // echo "<pre>";
      // var_dump($dbtable.'-'.$company.'-'.$vehicle.'-'.$frekuensianomali.'-'.$sdate.'-'.$edate);die();
      // echo "<pre>";

      $data_summary = $this->m_devicereport->getdatasummarymdvr($dbtable, $company, $vehicle, $frekuensianomali, $sdate, $edate);
      // $dbtable.'-'.$company.'-'.$vehicle.'-'.$frekuensianomali.'-'.$sdate.'-'.$edate
      // echo "<pre>";
      // var_dump($data_company);die();
      // echo "<pre>";

      $this->params['data']         = $data_summary;
      $this->params['rcompany'] = $data_company;

      $html = $this->load->view("newdashboard/devicereport/v_summarymdvr_result", $this->params, true);
      $callback['error'] = false;
      $callback['html']  = $html;
      $callback['data']  = $data_summary;
      echo json_encode($callback);
    }

  function mdvrreport(){
    if (!isset($this->sess->user_type)) {
        redirect('dashboard');
    }

    $privilegecode   = $this->sess->user_id_role;

    $rows           = $this->m_devicereport->getdevice();


    // echo "<pre>";
  	// var_dump($rows);die();
  	// echo "<pre>";

    $rows_company                   = $this->get_company();
    $this->params["vehicles"]       = $rows;
    $this->params["rcompany"]       = $rows_company;
    $this->params['code_view_menu'] = "report";

    $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
    $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

    if ($privilegecode == 1) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_devicereport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    } elseif ($privilegecode == 2) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_devicereport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    } elseif ($privilegecode == 3) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_devicereport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    } elseif ($privilegecode == 4) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_devicereport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    } elseif ($privilegecode == 5) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_devicereport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
    } else {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_devicereport', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
    }
  }

  function search_devicereport(){
    $privilegecode   = $this->sess->user_id_role;
      if ($privilegecode == 5 || $privilegecode == 6) {
        $company = $this->sess->user_company;
      }else {
        $company          = $this->input->post("company");
      }

  	$vehicle          = $this->input->post("vehicle");
  	$startdate        = $this->input->post("startdate");
  	$shour            = "00:00:00";
  	$enddate          = $this->input->post("enddate");
  	$ehour            = "23:59:59";
    $periode          = $this->input->post("periode");

  	$sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
  	$edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

  	$nowdate   = date("Y-m-d");
  	$nowday    = date("d");
  	$nowmonth  = date("m");
  	$nowyear   = date("Y");
  	$lastday   = date("t");

  		if($periode == "custom"){
  			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
  			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
  		}else if($periode == "yesterday"){
  			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
  			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
  		}else if($periode == "last7"){
  			$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-7days"));
  			$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
  		}else if($periode == "last30"){
  			$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-30days"));
  			$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
  		}else if($periode == "today"){
  			$sdate1 = date("Y-m-d");
  			$sdate2 = "00:00:00";

  			$edate1 = date("Y-m-d");
  			$edate2 = "23:59:59";

  			$sdate = $sdate1." ".$sdate2;
  			$edate = $edate1." ".$edate2;
  		}else{
  			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
  			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
  		}

  	$m1     = date("F", strtotime($sdate));
  	$year   = date("Y", strtotime($sdate));
  	$report = "report_device_status_";

    // echo "<pre>";
  	// var_dump($sdate.'-'.$edate);die();
  	// echo "<pre>";

  	switch ($m1)
  	{
  		case "January":
  					$dbtable = $report."januari_".$year;
  		break;
  		case "February":
  					$dbtable = $report."februari_".$year;
  		break;
  		case "March":
  					$dbtable = $report."maret_".$year;
  		break;
  		case "April":
  					$dbtable = $report."april_".$year;
  		break;
  		case "May":
  					$dbtable = $report."mei_".$year;
  		break;
  		case "June":
  					$dbtable = $report."juni_".$year;
  		break;
  		case "July":
  					$dbtable = $report."juli_".$year;
  		break;
  		case "August":
  					$dbtable = $report."agustus_".$year;
  		break;
  		case "September":
  					$dbtable = $report."september_".$year;
  		break;
  		case "October":
  					$dbtable = $report."oktober_".$year;
  		break;
  		case "November":
  					$dbtable = $report."november_".$year;
  		break;
  		case "December":
  					$dbtable = $report."desember_".$year;
  		break;
  	}

    $datavehicle = $this->m_devicereport->getthisvehicle($company, $vehicle);
    // echo "<pre>";
    // var_dump($datavehicle);die();
    // echo "<pre>";
    $datafix = array();
    $getreport = "";
      for ($i=0; $i < sizeof($datavehicle); $i++) {
        $vehicleno = $datavehicle[$i]['vehicle_device'];
        $getreport = $this->m_devicereport->getallreport($dbtable, $sdate, $edate, $vehicleno);

        array_push($datafix, array(
          "data" => $getreport
        ));
      }

  	// echo "<pre>";
  	// var_dump($datafix);die();
  	// echo "<pre>";
    $this->params['data'] = $datafix;

  	$html = $this->load->view("newdashboard/devicereport/v_devicereport_result", $this->params, true);
  	$callback['error'] = false;
  	$callback['html']  = $html;
  	$callback['data']  = $getreport;
  	echo json_encode($callback);
  }

  function search_gpsoffline(){
    $privilegecode   = $this->sess->user_id_role;
      if ($privilegecode == 5 || $privilegecode == 6) {
        $company = $this->sess->user_company;
      }else {
        $company          = $this->input->post("company");
      }
  	$vehicle          = $this->input->post("vehicle");
  	$startdate        = $this->input->post("startdate");
  	$shour            = "00:00:00";
  	$enddate          = $this->input->post("enddate");
  	$ehour            = "23:59:59";
    $periode          = $this->input->post("periode");

  	$sdate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
  	$edate2 = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));

  	$nowdate   = date("Y-m-d");
  	$nowday    = date("d");
  	$nowmonth  = date("m");
  	$nowyear   = date("Y");
  	$lastday   = date("t");

  		if($periode == "custom"){
  			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
  			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
  		}else if($periode == "yesterday"){
  			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
  			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
  		}else if($periode == "last7"){
  			$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-7days"));
  			$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
  		}else if($periode == "last30"){
  			$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-30days"));
  			$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
  		}else if($periode == "today"){
  			$sdate1 = date("Y-m-d");
  			$sdate2 = "00:00:00";

  			$edate1 = date("Y-m-d");
  			$edate2 = "23:59:59";

  			$sdate = $sdate1." ".$sdate2;
  			$edate = $edate1." ".$edate2;
  		}else{
  			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
  			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
  		}

  	$m1      = date("F", strtotime($sdate));
  	$year    = date("Y", strtotime($sdate));
  	$dbtable = "report_gps_status_historikal";

    $datareport = $this->m_devicereport->getgpsoffline($dbtable, $company, $vehicle, $sdate, $edate);

  	// echo "<pre>";
  	// var_dump($vehicle);die();
  	// echo "<pre>";

    $this->params['data'] = $datareport;

  	$html = $this->load->view("newdashboard/devicereport/v_gpsoffline_result", $this->params, true);
  	$callback['error'] = false;
  	$callback['html']  = $html;
  	$callback['data']  = $datareport;
  	echo json_encode($callback);
  }

  function gpsoffline(){
    if (!isset($this->sess->user_type)) {
        redirect('dashboard');
    }

    $privilegecode   = $this->sess->user_id_role;

    $rows                           = $this->get_vehicle_pjo();

    // echo "<pre>";
  	// var_dump($rows);die();
  	// echo "<pre>";

    $rows_company                   = $this->get_company();
    $this->params["vehicles"]       = $rows;
    $this->params["rcompany"]       = $rows_company;
    $this->params['code_view_menu'] = "report";

    $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
    $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

    if ($privilegecode == 1) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_gpsoffline', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    } elseif ($privilegecode == 2) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_gpsoffline', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    } elseif ($privilegecode == 3) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_gpsoffline', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    } elseif ($privilegecode == 4) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_gpsoffline', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    } elseif ($privilegecode == 5) {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_gpsoffline', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
    } else {
        $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
        $this->params["content"]        = $this->load->view('newdashboard/devicereport/v_home_gpsoffline', $this->params, true);
        $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
    }
  }

  function get_company()
  {
      if (!isset($this->sess->user_type)) {
          redirect(base_url());
      }

      $privilegecode = $this->sess->user_id_role;


      $this->db->order_by("company_name", "asc");
      if ($privilegecode == 0) {
          $this->db->where("company_created_by", $this->sess->user_id);
      } elseif ($privilegecode == 1) {
          $this->db->where("company_created_by", $this->sess->user_parent);
      } elseif ($privilegecode == 2) {
          $this->db->where("company_created_by", $this->sess->user_parent);
      } elseif ($privilegecode == 3) {
          $this->db->where("company_created_by", $this->sess->user_parent);
      } elseif ($privilegecode == 4) {
          $this->db->where("company_created_by", $this->sess->user_parent);
      } elseif ($privilegecode == 5) {
          $this->db->where("company_id", $this->sess->user_company);
      } elseif ($privilegecode == 6) {
          $this->db->where("company_id", $this->sess->user_company);
      }

      $this->db->where("company_flag", 0);
      $qd = $this->db->get("company");
      $rd = $qd->result();

      return $rd;
  }

  function get_vehicle_pjo()
  {
      $user_company    = $this->sess->user_company;
      $user_parent     = $this->sess->user_parent;
      $privilegecode   = $this->sess->user_id_role;
      $user_id         = $this->sess->user_id;
      $user_id_fix     = "";

      if ($user_id == "1445") {
          $user_id_fix = $user_id;
      } else {
          $user_id_fix = $this->sess->user_id;
      }

      //GET DATA FROM DB
      $this->db     = $this->load->database("default", true);
      $this->db->select("*");
      $this->db->order_by("vehicle_no", "asc");

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

      $this->db->where("vehicle_status <>", 3);
      $this->db->where("vehicle_gotohistory", 0);
      $this->db->where("vehicle_typeunit", 0);
      $this->db->where("vehicle_autocheck is not NULL");
      $q       = $this->db->get("vehicle");
      return  $q->result_array();
  }

  function get_vehicle_by_company_with_numberorder($id)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$this->db->order_by("vehicle_no", "asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
    if ($id != 0) {
      $this->db->where("vehicle_company", $id);
    }
		if ($this->sess->user_group > 0) {
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->join("company", "vehicle_company = company_id", "left");
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if ($qd->num_rows() > 0) {
			$options = "<option value='all' selected='selected' >--All Vehicle--</option>";
			$i = 1;
			foreach ($rd as $obj) {
				$options .= "<option value='" . $obj->vehicle_device . "'>" . $i . ". " . $obj->vehicle_no . " - " . $obj->vehicle_name . " " . "(" . $obj->company_name . ")" . "</option>";
				$i++;
			}

			echo $options;
			return;
		}
	}

  function get_company_bylevel()
  	{
  		if (!isset($this->sess->user_type)) {
  			redirect(base_url());
  		}
  		$privilegecode 						= $this->sess->user_id_role;

  		$this->db->order_by("company_name", "asc");
  		if ($privilegecode == 0) {
  			$this->db->where("company_created_by", $this->sess->user_id);
  		} elseif ($privilegecode == 1) {
  			$this->db->where("company_created_by", $this->sess->user_parent);
  		} elseif ($privilegecode == 2) {
  			$this->db->where("company_created_by", $this->sess->user_parent);
  		} elseif ($privilegecode == 3) {
  			$this->db->where("company_created_by", $this->sess->user_parent);
  		} elseif ($privilegecode == 4) {
  			$this->db->where("company_created_by", $this->sess->user_parent);
  		} elseif ($privilegecode == 5) {
  			$this->db->where("company_created_by", $this->sess->user_company);
  		}elseif ($privilegecode == 6) {
  			$this->db->where("company_created_by", $this->sess->user_company);
  		}elseif ($privilegecode == 7) {
  			$this->db->where("company_created_by", $this->sess->user_parent);
  		}elseif ($privilegecode == 8) {
  			$this->db->where("company_created_by", $this->sess->user_parent);
  		}

  		$this->db->where_in("company_exca", array(0,2));
      $this->db->where("company_flag", 0);
  		$qd = $this->db->get("company");
  		$rd = $qd->result();

  		return $rd;
  	}

    function vehicleByContractor(){
    	$user_id         = $this->sess->user_id;
    	$user_parent     = $this->sess->user_parent;
    	$privilegecode   = $this->sess->user_id_role;
    	$user_company    = $this->sess->user_company;
    	$companyid       = $this->input->post('companyid');
    	$valueMapsOption = $this->input->post('valuemapsoption');

    	$this->db->select("*");
    		if ($companyid == 0 || $companyid == "all") {
    			if ($privilegecode == 0) {
    				$this->db->where("vehicle_user_id", $user_id);
    			}elseif ($privilegecode == 1) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 2) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 3) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 4) {
    				$this->db->where("vehicle_user_id", $user_parent);
    			}elseif ($privilegecode == 5) {
    				$this->db->where("vehicle_company", $user_company);
    			}elseif ($privilegecode == 6) {
    				$this->db->where("vehicle_company", $user_company);
    			}
    		}else {
    			$this->db->where("vehicle_company", $companyid);
    		}

        $this->db->where("vehicle_mv03 !=", "0000");
        // $this->db->where_in("vehicle_type", array("MV03"));
        $this->db->where("vehicle_status <>", 3);
    	$this->db->order_by("vehicle_no", "ASC");
    	$q    = $this->db->get("vehicle");
    	$rows = $q->result_array();

    	if ($valueMapsOption == 1) {
    		$poolmaster        = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
    		$datavehicle       = array();

    			for ($i=0; $i < sizeof($rows); $i++) {
    				$autocheck         = json_decode($rows[$i]['vehicle_autocheck']);
    						array_push($datavehicle, array(
    							"vehicle_id"     => $rows[$i]['vehicle_id'],
    							"vehicle_no"     => $rows[$i]['vehicle_no'],
    							"vehicle_name"   => $rows[$i]['vehicle_name'],
    							"vehicle_device" => $rows[$i]['vehicle_device'],
    							"auto_last_lat"  => $autocheck->auto_last_lat,
    							"auto_last_long" => $autocheck->auto_last_long
    						));
    			}
    			echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows, "datavehicle" => $datavehicle, "poolmaster" => $poolmaster));
    	}else {
    		echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
    	}

    	// echo "<pre>";
    	// var_dump($datavehicle);die();
    	// echo "<pre>";

    }










}
