<?php
include "base.php";

class Wimreport extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->model("dashboardmodel");
    $this->load->model("m_wimreport");
	}

	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

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

		$rows                           = $this->dashboardmodel->getvehicle_report();
		$rows_company                   = $this->dashboardmodel->get_company_bylevel();
		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wimreport/v_home_wimreport', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wimreport/v_home_wimreport', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wimreport/v_home_wimreport', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wimreport/v_home_wimreport', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wimreport/v_home_wimreport', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		}elseif ($privilegecode == 8) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_useritws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wimreport/v_home_wimreport', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_useritws", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/wimreport/v_home_wimreport', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function search_report(){
		$vehicle   = $this->input->post("vehicle");
		$company   = $this->input->post("company");
		$startdate = $this->input->post("startdate");
		$shour     = "00:00:00";
		$enddate   = $this->input->post("enddate");
		$ehour     = "23:59:59";
		$statuswim = $this->input->post("statuswim");
		$periode   = $this->input->post("periode");

	 /*  $sdate     = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00") + 60*60*1);
	  $edate     = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":00") + 60*60*1); */

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
			/* $nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if($nowday <= 7){
				$firstday = 1;
			}

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59")); */

			$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-7days"));
			$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
		}
		else if($periode == "last30"){
			/* $firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59")); */

			$sdate = date("Y-m-d H:i:s", strtotime($sdate2 . "-30days"));
			$edate = date("Y-m-d H:i:s", strtotime($startdate." ".$ehour));
		}
		else{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}

		$m1      = date("F", strtotime($sdate));
		$year    = date("Y", strtotime($sdate));
		$report = "historikal_integrationwim_unit_";

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

		$dbtable = "historikal_integrationwim_unit";

		$getreport            = $this->m_wimreport->getreportnow($dbtable, $company, $vehicle, $statuswim, $sdate, $edate);
		$this->params['data'] = $getreport;
		// echo "<pre>";
		// var_dump($dbtable.'-'.$vehicle.'-'.$sdate.'-'.$edate.'-'.$statuswim);die();
		// echo "<pre>";

		$html = $this->load->view("newdashboard/wimreport/v_wimreport_result", $this->params, true);
		$callback['error'] = false;
		$callback['html']  = $html;
		$callback['data']  = $getreport;
		echo json_encode($callback);
	}

	function detail($transID){
		// $vehicleID = "72150903";

		$transdata = $this->m_wimreport->getTransByID($transID,"sample_month","sample_year");

		// echo "<pre>";
		// var_dump($vehicledata);die();
		// echo "<pre>";

		$this->params['transdata'] 		 = $transdata;
		$this->params['code_view_menu'] = "report";
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/wimreport/v_detail_wim', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}


















}
