<?php
include "base.php";

class Hseboard extends Base
{

	function Hseboard()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
	}

	function board()
	{

		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";

		$companydata = $this->getcompany_bycreator(4408);
		$vehicledata = $this->getAllVehicle_bycreator(4408);
		//$portdata = $this->getport_bycreator(4408);

		$this->params["rcompany"]    = $companydata;
		$this->params["rvehicle"]    = $vehicledata;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/hse/v_dashboard_hse_mn', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_chart_new", $this->params);
	}

	//source overspeed dari table alert (true false undifined + no filter location) 
	function violation()
	{

		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";

		$companydata = $this->get_company();
		$vehicledata = $this->get_vehicle();
		$rviolation = $this->getViolation();

		$this->params["rcompany"]    = $companydata;
		$this->params["rvehicle"]    = $vehicledata;
		$this->params["rviolation"]  = $rviolation;
		$this->params['code_view_menu'] = "dashboard";

		///new

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["onload"]         = 1;
		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}
	
	//source overspeed dari table alert (true false undifined + no filter location)
	function violation2()
	{

		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";

		$companydata = $this->get_company();
		$vehicledata = $this->get_vehicle();
		$rviolation = $this->getViolation();

		$this->params["rcompany"]    = $companydata;
		$this->params["rvehicle"]    = $vehicledata;
		$this->params["rviolation"]  = $rviolation;
		$this->params['code_view_menu'] = "dashboard";

		///new

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["onload"]         = 1;
		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hseboard/v_daily_violation2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}
	
	//menu summary table
	function summary()
	{

		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";

		$companydata = $this->get_company();
		$vehicledata = $this->get_vehicle();
		$rviolation = $this->getViolation();

		$this->params["rcompany"]    = $companydata;
		$this->params["rvehicle"]    = $vehicledata;
		$this->params["rviolation"]  = $rviolation;
		$this->params['code_view_menu'] = "dashboard";

		///new

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["onload"]         = 1;
		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_summary_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_summary_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_summary_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_summary_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_summary_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_summary_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_summary_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function board_dev_v2()
	{

		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";

		$companydata = $this->getcompany_bycreator(4408);
		$vehicledata = $this->getAllVehicle_bycreator(4408);
		$rviolation = $this->getViolation();

		//$portdata = $this->getport_bycreator(4408);

		$this->params["rcompany"]    = $companydata;
		$this->params["rvehicle"]    = $vehicledata;
		$this->params["rviolation"]  = $rviolation;


		// $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		// $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		// $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		// $this->params["content"]        = $this->load->view('newdashboard/hse/v_dashboard_hse_mn', $this->params, true);
		// $this->load->view("newdashboard/partial/template_dashboard_chart_new", $this->params);

		///new

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["onload"]         = 1;
		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_dashboard_hse_mn_dev_backup5', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_dashboard_hse_mn_dev_backup5', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_dashboard_hse_mn_dev_backup5', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_dashboard_hse_mn_dev_backup5', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_dashboard_hse_mn_dev_backup5', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_dashboard_hse_mn_dev_backup5', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_dashboard_hse_mn_dev_backup5', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function search()
	{
		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";

		$userid = 4408;
		$startdate  = $this->input->post("startdate");
		$enddate    = $this->input->post("enddate");
		$periode    = $this->input->post("periode");

		//$periode = "last7";
		//$company = "all";
		$company = $this->input->post("company");
		$violation = $this->input->post("violation");


		if ($company != "all") {

			$callback['error'] = true;
			$callback['message'] = "Data Per Contractor belum tersedia!";

			echo json_encode($callback);
			return;
		}


		/* if($violation == "all"){

			$callback['error'] = true;
			$callback['message'] = "Belum Tersedia untuk All Violation!";

			echo json_encode($callback);
			return;
		} */

		$shour = "00:00:00";
		$ehour = "23:59:59";


		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "overspeed_board_";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}


		$sdate_show = date("d-m-Y", strtotime($sdate));
		$edate_show = date("d-m-Y", strtotime($edate));
		$periode_show = "PERIODE: " . $sdate_show . " to " . $edate_show;
		$periode_show_percompany = $periode_show;

		//print_r($sdate." ".$edate);exit();
		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		$this->params["periode_show"]  = $periode_show;

		if ($violation == "overspeed") {
			$overspeed_board_company = $this->getOverspeedBoard_byCompany($userid, $company, $periode, $startdate, $enddate);
			$overspeed_board_company_bycompany = $this->getOverspeedBoard_byCompany_contractor($userid, $company, $periode, $startdate, $enddate);
			$overspeed_board_company_bystreet = $this->getOverspeedBoard_byStreet($userid, $company, $periode, $startdate, $enddate);

			$overspeed_board_company_byhourly = $this->getOverspeedBoard_byCompany_hourly($userid, $company, $periode, $startdate, $enddate);
			$overspeed_board_company_byhourly2 = $this->getOverspeedBoard_byCompany_hourly2($userid, $company, $periode, $startdate, $enddate);

			$this->params["content_all_overspeed"]    = $overspeed_board_company;
			$this->params["content_all_overspeed_bycontractor"]    = $overspeed_board_company_bycompany;
			$this->params["content_all_overspeed_bystreet"]    = $overspeed_board_company_bystreet;
			$this->params["content_all_overspeed_byhourly"]    = $overspeed_board_company_byhourly;
			$this->params["content_all_overspeed_byhourly2"]    = $overspeed_board_company_byhourly2;

			$html                    = $this->load->view('newdashboard/hse/v_dashboard_hse_result', $this->params, true);
		} else {

			$violation_board_company = $this->getViolationBoard_byCompany($userid, $company, $violation, $periode, $startdate, $enddate);
			$violation_board_all_bytype = $this->getAllViolationBoard_byType($userid, $company, $violation, $periode, $startdate, $enddate);

			$this->params["content_selected_violation"]    = $violation_board_company;
			$this->params["content_all_violation_bytype"]    = $violation_board_all_bytype;

			$html                    = $this->load->view('newdashboard/hse/v_dashboard_hse_result_new', $this->params, true);
		}

		$callback["html"]        = $html;

		echo json_encode($callback);
	}

	//prod
	function search_violation()
	{
		ini_set('memory_limit', "3G");
		ini_set('max_execution_time', 100); // 5 minutes
		// $datein    = $this->input->post("date");
		$company = $this->input->post("company");
		$violation = $this->input->post("violation");
		$vehicle = $this->input->post("vehicle");
		$status = $this->input->post("status");
		// $date = date("Y-m-d", strtotime($datein));
		// $month = date("F", strtotime($datein));
		// $monthforparam = date("m", strtotime($datein));
		// $year = date("Y", strtotime($datein));
		$report     = "alarm_evidence_";
		$overspeed  = "overspeed_hour_";
		$periode = $this->input->post("periode");
		$year = date("Y");
		$mont = date("m");
		$nowday = date("d");
		$err = false;
		$msg = '';
		if ($periode == "today") {
			$sdate = date("Y-m-d 00:00:00");
			$edate = date("Y-m-d H:i:s");
			$datein = date("d-m-Y", strtotime($sdate));
		} else if ($periode == "yesterday") {
			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
			$datein = date("d-m-Y", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$year = date("Y");
			$mont = date("m");
			$nowday = date("d");
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}
			$sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-" . $firstday));
			$edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
		} else if ($periode == "this_month") {
			$firstday = "1";
			$sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-1"));
			$edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
		} else if ($periode == "custom") {
			$sdate = $this->input->post("sdate");
			$edate = $this->input->post("edate");
			$sdate = date("Y-m-d 00:00:00", strtotime($sdate));
			$edate = date("Y-m-d 23:59:59", strtotime($edate));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
			$diff = strtotime($edate) - strtotime($sdate);
			if ($diff < 0) {
				$err = true;
				$msg = "Date is not correct!";
			}
			$diff = strtotime(date("Y-m-d")) - strtotime($sdate);
			if ($diff < 0) {
				$err = true;
				$msg = "Date is not correct!";
			}
			/* if ($company == "all") {
				$diff = strtotime($edate) - strtotime($sdate);
				if ($diff > 604800) {
					$err = true;
					$msg = "Maximum date range for all contractors is 7 days!";
				}
			} */
			$diff1 = date("m", strtotime($sdate));
			$diff2 = date("m", strtotime($edate));
			if ($diff1 != $diff2) {
				$err = true;
				$msg = "Date must be in the same month!";
			}
			$diff1 = date("Y", strtotime($sdate));
			$diff2 = date("Y", strtotime($edate));
			if ($diff1 != $diff2) {
				$err = true;
				$msg = "Date must be in the same year!";
			}
		}

		$month = date("F", strtotime($sdate));
		$year = date("Y", strtotime($sdate));

		if ($err == true) {
			$callback['error'] = true;
			$callback['message'] = $msg;
			echo json_encode($callback);
			return;
		}

		switch ($month) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dboverspeed = $overspeed . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dboverspeed = $overspeed . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dboverspeed = $overspeed . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dboverspeed = $overspeed . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dboverspeed = $overspeed . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dboverspeed = $overspeed . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dboverspeed = $overspeed . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dboverspeed = $overspeed . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dboverspeed = $overspeed . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dboverspeed = $overspeed . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dboverspeed = $overspeed . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dboverspeed = $overspeed . "desember_" . $year;
				break;
		}


		$input = array(
			'date_start' => $sdate,
			'date_end' => $edate,
			'db' => $dbtable,
			'db_overspeed' => $dboverspeed
		);
		//violation data

		$rviolation = $this->getViolation(); //ambil master data violation alrmmaster
		$dataviolation = array(); //untuk simpan data violation
		$master_violation = array(); //untuk simpan data violation format 2
		$allviolation = array(); //untuk simpan id violation
		$nr = count($rviolation);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$dataviolation[$rviolation[$i]["alarmmaster_id"]] = $rviolation[$i]["alarmmaster_name"];
				$master_violation[$i] = $rviolation[$i]["alarmmaster_name"];
				array_push($allviolation, $rviolation[$i]["alarmmaster_id"]);
			}
		}

		$dataalarmtype = array(); //untuk query where_in
		$dataviolationalarmtype = array(); //untuk ditampilkan di view
		if ($violation != "all") {
			$alarmtype = $this->getAlarmtype($violation);
		} else {
			$alarmtype = $this->getAlarmtype(null, $allviolation);
		}
		$nr = count($alarmtype);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				if (!isset($dataviolationalarmtype[$alarmtype[$i]["alarm_type"]])) {
					if ($violation == "all") {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$alarmtype[$i]["alarm_master_id"]];
					} else {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$violation];
					}
				}
				array_push($dataalarmtype, $alarmtype[$i]["alarm_type"]);
			}
		}


		//vehicle data
		$this->db->select("vehicle_name,vehicle_no,vehicle_company");
		$this->db->where("vehicle_status <>", 3);

		if ($company != "all") {
			$this->db->where("vehicle_company", $company);
		}
		$qd = $this->db->get("vehicle");
		$total_unit = $qd->num_rows();
		$rd = $qd->result();
		$total_unit_percontractor = array();
		for ($x = 0; $x < $total_unit; $x++) {
			if ($rd[$x]->vehicle_company != null) {
				if (!isset($total_unit_percontractor[$rd[$x]->vehicle_company])) {
					$total_unit_percontractor[$rd[$x]->vehicle_company] = 1;
				} else {
					$jml = (int)$total_unit_percontractor[$rd[$x]->vehicle_company] + 1;
					$total_unit_percontractor[$rd[$x]->vehicle_company] = $jml;
				}
			}
		}

		$s_date = date("Y-m-d", strtotime($sdate));
		$nowdate = date("Y-m-d");
		// $action = false;
		// if ($company == 'all') {
		// 	if ($s_date == $e_date) {
		// 		if ($s_date != $nowdate) {
		// 			$action = true;
		// 		} else {
		// 			$action = false;
		// 		}
		// 	} else {
		// 		$action = false;
		// 	}
		// } else {
		if ($s_date != $nowdate) {
			$action = true;
		} else {
			$action = false;
		}
		// }

		// if ($action == true) {
		// 	//vehicle operational data
		// 	$dt_operational = $this->getDTOperational($company, $s_date, $e_date);
		// 	$this->params['total_operational_units'] = $dt_operational['units']; //total violation units
		// }
		$this->params['ratio'] = $action;



		$data = array();
		$data2 = array();

		if ($vehicle != "all") {
			$exp = explode("/", $vehicle);
			$vehicle_imei = $exp[0];
			$vehicle_device = $exp[1];

			$companydata = $this->getcompany_bycreator(null, $exp[2]);
			$count_company = 1;
			$company = $exp[2];
			$data_company[$exp[2]] = $companydata[0]->company_name;
			$master_company[0] = $companydata[0]->company_name;
			$company = $exp[2];
			$opposite_company[$companydata[0]->company_name] = $exp[2];

			if ($violation == "6") {
				$data2 = $this->getOverspeed2_status($dboverspeed, $company, $vehicle_device, $sdate, $edate, $status);
			} else {
				if ($violation == "all") {
					$data2 = $this->getOverspeed2_status($dboverspeed, $company, $vehicle_device, $sdate, $edate, $status);
					$data = $this->getSecurityEvidence_status($dbtable, $company, $vehicle_imei, $violation, $dataalarmtype, $sdate, $edate, $status);
				} else {
					//$data2 = $this->getOverspeed2($dboverspeed, $company, $vehicle_device, $sdate, $edate); //additional dari ovs alert bisa kebuka untuk all mitra 1 bulan
					$data = $this->getSecurityEvidence_status($dbtable, $company, $vehicle_imei, $violation, $dataalarmtype, $sdate, $edate, $status);
				}
			}
		} else {
			//company data
			$companydata = $this->getcompany_bycreator(4408);
			$data_company = array(); //format 1
			$opposite_company = array(); //kebalikan format 1
			$master_company = array(); //format 2
			if ($company != "all") {
				$exp = explode("@", $company);
				$count_company = 1;
				$company = $exp[0];
				$data_company[$exp[0]] = $exp[1];
				$master_company[0] = $exp[1];
				$company = $exp[0];
				$opposite_company[$exp[1]] = $exp[0];
			} else {
				$count_company = count($companydata);
				for ($i = 0; $i < $count_company; $i++) {
					$data_company[$companydata[$i]->company_id] = $companydata[$i]->company_name;
					$opposite_company[$companydata[$i]->company_name] = $companydata[$i]->company_id;
					$master_company[$i] = $companydata[$i]->company_name;
				}
			}

			if ($violation == "6") {
				$data2 = $this->getOverspeed2_status($dboverspeed, $company, $vehicle, $sdate, $edate, $status);
			} else {
				if ($violation == "all") {
					$data2 = $this->getOverspeed2_status($dboverspeed, $company, $vehicle, $sdate, $edate, $status);
					$data = $this->getSecurityEvidence_status($dbtable, $company, $vehicle, $violation, $dataalarmtype, $sdate, $edate, $status);
				} else {
					//$data2 = $this->getOverspeed2_status($dboverspeed, $company, $vehicle_device, $sdate, $edate, $status); //additional dari ovs alert bisa kebuka untuk all mitra 1 bulan
					$data = $this->getSecurityEvidence_status($dbtable, $company, $vehicle, $violation, $dataalarmtype, $sdate, $edate , $status);
				}
			}
		}

		/* print_r($sdate." ".$edate);
		print_r($data2);exit(); */

		$numrows = count($data);
		// $numrows = 0;
		$numrows2 = count($data2);
		// $numrows2 = 0;
		$total_data = 0;
		$data_fix = array();
		$data_table = array();
		$dhour = array();
		$dcompany = array();
		$dviolation = array();
		$c = array();
		$l = array();
		$seleksi_data = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_overspeed = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table_overspeed = array(); //untuk seleksi multiple data yang sama
		$total_violation_units = array();
		$seleksi_unit = array();
		$top_ten = array();
		
		$datarekap_true = array();
		$datarekap_false = array();
		$datarekap_undefined = array();
		
		$datarekap_true_ovs = array();
		$datarekap_false_ovs = array();
		$datarekap_undefined_ovs = array();
		
		$total_undefined = 0;
		$total_false = 0;
		$total_true = 0;
		$total_rekap = 0;
		
		$total_undefined_ovs = 0;
		$total_false_ovs = 0;
		$total_true_ovs = 0;
		$total_rekap_ovs = 0;
				
		if ($company == "all") {
			if ($numrows > 0) {
				for ($i = 0; $i < $numrows; $i++) {
					if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
						$datetime = $data[$i]['alarm_report_start_time'];
						//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
						$vehiclen = $data[$i]['alarm_report_vehicle_no'];
						$loction = $data[$i]['alarm_report_location_start'];
						$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);
						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }

						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}

						if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
							$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
						} else {
							$vltion = $data[$i]['alarm_report_type'];
						}





						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {

							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0],
						);

						if (!isset($data_fix[$h[0]][$cmpny])) {
							$data_fix[$h[0]][$cmpny] = array();
							array_push($data_fix[$h[0]][$cmpny], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_fix[$h[0]][$cmpny], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							} else {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_fix[$h[0]][$cmpny], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							} else {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							}
						}
					}
				}
			}
			if ($numrows2 > 0) {
				for ($i = 0; $i < $numrows2; $i++) {
					if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
						// $datetime = $data[$i]['overspeed_report_gps_time'];
						$datetime = $data2[$i]['overspeed_report_gps_time'];
						// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));
						$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];

						$vltion = "Overspeed";
						$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
						$locationn = $data2[$i]['overspeed_report_location'];
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }


						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}


						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {
							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0],
							"level" => $data2[$i]['overspeed_report_level']
						);

						if (!isset($data_fix[$h[0]][$cmpny])) {
							$data_fix[$h[0]][$cmpny] = array();
							array_push($data_fix[$h[0]][$cmpny], $d);
							$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;

							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_fix[$h[0]][$cmpny], $d);
								if (!isset($top_ten[$vltion][$vehiclen])) {
									$top_ten[$vltion][$vehiclen] = 1;
								} else {
									$top_ten[$vltion][$vehiclen] += 1;
								}
								$total_data++;
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
						} else {
							if (!isset($seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_table[$vltion][$cmpny], $d);
							}
						}
					}
				}
			}
			if ($total_data == 0) {
				$callback['error'] = true;
				$callback['message'] = "Data Empty!";
				$callback['input'] = $input;
				echo json_encode($callback);
				return;
			}
		} else {
			
			if ($numrows > 0) {
				for ($i = 0; $i < $numrows; $i++) {
					
					if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
						$datetime = $data[$i]['alarm_report_start_time'];
						//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
						$vehiclen = $data[$i]['alarm_report_vehicle_no'];
						//$loction = $data[$i]['alarm_report_location_start']; //filter by location didisble sementara
						$loction = $data[$i]['alarm_report_id'];
						$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];

						if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
							$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
						} else {
							$vltion = $data[$i]['alarm_report_type'];
						}
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }

						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}


						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {

							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0],
						);
						
						
						//kondisi IF driver Abnormal didisable dahulu
						/* if (!isset($data_fix[$h[0]][$vltion])) {
							$data_fix[$h[0]][$vltion] = array();
							array_push($data_fix[$h[0]][$vltion], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_fix[$h[0]][$vltion], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							} else {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_fix[$h[0]][$vltion], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							} else {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							}
						} */
						
						
						if (!isset($data_fix[$h[0]][$vltion])) {
							$data_fix[$h[0]][$vltion] = array();
							array_push($data_fix[$h[0]][$vltion], $d);
							
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_fix[$h[0]][$vltion], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							
						} else {
							
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							
						}
						
						//data rekap
						if($data[$i]['alarm_report_truefalse_up'] == "1"){
							$info_true = $data[$i]['alarm_report_id'];
							array_push($datarekap_true,$info_true);
							
						}else if($data[$i]['alarm_report_truefalse_up'] == "2"){
							$info_false = $data[$i]['alarm_report_id'];
							array_push($datarekap_false,$info_false);
							
						}else{
							$info_undefined = $data[$i]['alarm_report_id'];
							array_push($datarekap_undefined,$info_undefined);
							
						}
						
						
						
					}
				}
				
				
				//summary rekap value
						$total_undefined = count($datarekap_undefined);
						$total_false = count($datarekap_false);
						$total_true = count($datarekap_true);
						$total_rekap = $total_undefined+$total_false+$total_true;
			
			}
			if ($numrows2 > 0) {
				for ($i = 0; $i < $numrows2; $i++) {
					if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
						// $datetime = $data[$i]['overspeed_report_gps_time'];
						$datetime = $data2[$i]['overspeed_report_gps_time'];
						// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));
						$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];

						$vltion = "Overspeed";
						$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
						//$locationn = $data2[$i]['overspeed_report_location']; //filter by location didisable dulu
						$locationn = $data2[$i]['overspeed_report_id'];
						
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }


						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}


						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {
							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0]
						);

						if (!isset($data_fix[$h[0]][$vltion])) {
							$data_fix[$h[0]][$vltion] = array();
							array_push($data_fix[$h[0]][$vltion], $d);
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
							$total_data++;
						} else {
							if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_fix[$h[0]][$vltion], $d);
								if (!isset($top_ten[$vltion][$vehiclen])) {
									$top_ten[$vltion][$vehiclen] = 1;
								} else {
									$top_ten[$vltion][$vehiclen] += 1;
								}
								$total_data++;
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
						} else {
							if (!isset($seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_table[$vltion][$cmpny], $d);
							}
						}
						
						
						//data rekap
						if($data2[$i]['overspeed_report_truefalse_up'] == "1"){
							
							$info_true_ovs = $data2[$i]['overspeed_report_id'];
							array_push($datarekap_true_ovs,$info_true_ovs);
							
						}else if($data2[$i]['overspeed_report_truefalse_up'] == "2"){
							
							$info_false_ovs = $data2[$i]['overspeed_report_id'];
							array_push($datarekap_false_ovs,$info_false_ovs);
						}else{
							$info_undefined_ovs = $data2[$i]['overspeed_report_id'];
							array_push($datarekap_undefined_ovs,$info_undefined_ovs);
						}
						
						
						
						
						
					}
				}
				
				//summary rekap value
						$total_undefined_ovs = count($datarekap_undefined_ovs);
						$total_false_ovs = count($datarekap_false_ovs);
						$total_true_ovs = count($datarekap_true_ovs);
						$total_rekap_ovs = $total_undefined_ovs+$total_false_ovs+$total_true_ovs;
			
			}
			
			$gtotal_rekap = $total_rekap+$total_rekap_ovs;
			$gtotal_undefined = $total_undefined+$total_undefined_ovs;
			$gtotal_false = $total_false+$total_false_ovs;
			$gtotal_true = $total_true+$total_true_ovs;
			
			if($gtotal_rekap >0){
				//%tase gtotal
				$persen_undefined = round($gtotal_undefined/$gtotal_rekap*100,2);
				$persen_false = round($gtotal_false/$gtotal_rekap*100,2);
				$persen_true = round($gtotal_true/$gtotal_rekap*100,2);
				
				$persen_total = $persen_undefined+$persen_false+$persen_true;
				
				
				$this->params['persen_undefined'] = $persen_undefined;
				$this->params['persen_false'] = $persen_false;
				$this->params['persen_true'] = $persen_true;
				$this->params['persen_total'] = $persen_total;
				
				$this->params['gtotal_rekap'] = $gtotal_rekap;
				$this->params['gtotal_undefined'] = $gtotal_undefined;
				$this->params['gtotal_false'] = $gtotal_false;
				$this->params['gtotal_true'] = $gtotal_true;
				
			}
			
				
			
			if ($total_data == 0) {
				$callback['error'] = true;
				$callback['message'] = "Data Empty!";
				echo json_encode($callback);
				return;
			}
		}


		$input = array(
			"company" => $company,
			"violation" => $violation,
			"vehicle" => $vehicle,
			"periode" => $periode,
			"date_start" => $sdate,
			"date_end" => $edate
		);

		sort($dhour);

		/* var_dump($total_violation_units);
		 exit(); */

		//print_r($numrows+$numrows2);exit();

		$this->params['input'] = $input;
		$this->params['sdate'] = $sdate;
		$this->params['edate'] = $edate;
		$this->params['opposite_company'] = $opposite_company;
		$this->params['total_data'] = $total_data;
		$this->params['date'] = $datein;
		$this->params['data_hour'] = $dhour; //data hour dari source
		$this->params['data_company'] = $dcompany; //data company dari source
		$this->params['data_alarm'] = $dviolation; //data violation dari source

		$this->params['data_fix'] = $data_fix;
		$this->params['data_table'] = $data_table;
		$this->params['top_violation'] = $top_ten; //master company format 2
		$this->params['master_company'] = $master_company; //master company format 2
		$this->params['total_violation_units'] = $total_violation_units; //total violation units
		$this->params['master_vehicle'] = $total_unit_percontractor; //master vehicle
		$this->params['master_violation'] = $master_violation; //master company format 2


		$html                    = $this->load->view('newdashboard/hseboard/v_daily_violation_result', $this->params, true);



		$callback['error'] = false;
		$callback['input'] = $input;
		$callback['data_fix'] = $data_fix;
		$callback["html"]        = $html;

		echo json_encode($callback);
	}
	
	//prod + behazard + berecord
	function search_violation2()
	{
		ini_set('memory_limit', "3G");
		ini_set('max_execution_time', 100); // 5 minutes
		// $datein    = $this->input->post("date");
		$company = $this->input->post("company");
		$violation = $this->input->post("violation");
		$vehicle = $this->input->post("vehicle");
		$status = $this->input->post("status");
		// $date = date("Y-m-d", strtotime($datein));
		// $month = date("F", strtotime($datein));
		// $monthforparam = date("m", strtotime($datein));
		// $year = date("Y", strtotime($datein));
		$report     = "alarm_evidence_";
		$overspeed  = "overspeed_hour_";
		$periode = $this->input->post("periode");
		$year = date("Y");
		$mont = date("m");
		$nowday = date("d");
		$err = false;
		$msg = '';
		if ($periode == "today") {
			$sdate = date("Y-m-d 00:00:00");
			$edate = date("Y-m-d H:i:s");
			$datein = date("d-m-Y", strtotime($sdate));
		} else if ($periode == "yesterday") {
			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
			$datein = date("d-m-Y", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$year = date("Y");
			$mont = date("m");
			$nowday = date("d");
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}
			$sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-" . $firstday));
			$edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
		} else if ($periode == "this_month") {
			$firstday = "1";
			$sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-1"));
			$edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
		} else if ($periode == "custom") {
			$sdate = $this->input->post("sdate");
			$edate = $this->input->post("edate");
			$sdate = date("Y-m-d 00:00:00", strtotime($sdate));
			$edate = date("Y-m-d 23:59:59", strtotime($edate));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
			$diff = strtotime($edate) - strtotime($sdate);
			if ($diff < 0) {
				$err = true;
				$msg = "Date is not correct!";
			}
			$diff = strtotime(date("Y-m-d")) - strtotime($sdate);
			if ($diff < 0) {
				$err = true;
				$msg = "Date is not correct!";
			}
			/* if ($company == "all") {
				$diff = strtotime($edate) - strtotime($sdate);
				if ($diff > 604800) {
					$err = true;
					$msg = "Maximum date range for all contractors is 7 days!";
				}
			} */
			$diff1 = date("m", strtotime($sdate));
			$diff2 = date("m", strtotime($edate));
			if ($diff1 != $diff2) {
				$err = true;
				$msg = "Date must be in the same month!";
			}
			$diff1 = date("Y", strtotime($sdate));
			$diff2 = date("Y", strtotime($edate));
			if ($diff1 != $diff2) {
				$err = true;
				$msg = "Date must be in the same year!";
			}
		}

		$month = date("F", strtotime($sdate));
		$year = date("Y", strtotime($sdate));

		if ($err == true) {
			$callback['error'] = true;
			$callback['message'] = $msg;
			echo json_encode($callback);
			return;
		}

		switch ($month) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dboverspeed = $overspeed . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dboverspeed = $overspeed . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dboverspeed = $overspeed . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dboverspeed = $overspeed . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dboverspeed = $overspeed . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dboverspeed = $overspeed . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dboverspeed = $overspeed . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dboverspeed = $overspeed . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dboverspeed = $overspeed . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dboverspeed = $overspeed . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dboverspeed = $overspeed . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dboverspeed = $overspeed . "desember_" . $year;
				break;
		}


		$input = array(
			'date_start' => $sdate,
			'date_end' => $edate,
			'db' => $dbtable,
			'db_overspeed' => $dboverspeed
		);
		//violation data

		$rviolation = $this->getViolation(); //ambil master data violation alrmmaster
		$dataviolation = array(); //untuk simpan data violation
		$master_violation = array(); //untuk simpan data violation format 2
		$allviolation = array(); //untuk simpan id violation
		$nr = count($rviolation);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$dataviolation[$rviolation[$i]["alarmmaster_id"]] = $rviolation[$i]["alarmmaster_name"];
				$master_violation[$i] = $rviolation[$i]["alarmmaster_name"];
				array_push($allviolation, $rviolation[$i]["alarmmaster_id"]);
			}
		}

		$dataalarmtype = array(); //untuk query where_in
		$dataviolationalarmtype = array(); //untuk ditampilkan di view
		if ($violation != "all") {
			$alarmtype = $this->getAlarmtype($violation);
		} else {
			$alarmtype = $this->getAlarmtype(null, $allviolation);
		}
		$nr = count($alarmtype);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				if (!isset($dataviolationalarmtype[$alarmtype[$i]["alarm_type"]])) {
					if ($violation == "all") {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$alarmtype[$i]["alarm_master_id"]];
					} else {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$violation];
					}
				}
				array_push($dataalarmtype, $alarmtype[$i]["alarm_type"]);
			}
		}


		//vehicle data
		$this->db->select("vehicle_name,vehicle_no,vehicle_company");
		$this->db->where("vehicle_status <>", 3);

		if ($company != "all") {
			$this->db->where("vehicle_company", $company);
		}
		$qd = $this->db->get("vehicle");
		$total_unit = $qd->num_rows();
		$rd = $qd->result();
		$total_unit_percontractor = array();
		for ($x = 0; $x < $total_unit; $x++) {
			if ($rd[$x]->vehicle_company != null) {
				if (!isset($total_unit_percontractor[$rd[$x]->vehicle_company])) {
					$total_unit_percontractor[$rd[$x]->vehicle_company] = 1;
				} else {
					$jml = (int)$total_unit_percontractor[$rd[$x]->vehicle_company] + 1;
					$total_unit_percontractor[$rd[$x]->vehicle_company] = $jml;
				}
			}
		}

		$s_date = date("Y-m-d", strtotime($sdate));
		$nowdate = date("Y-m-d");
		// $action = false;
		// if ($company == 'all') {
		// 	if ($s_date == $e_date) {
		// 		if ($s_date != $nowdate) {
		// 			$action = true;
		// 		} else {
		// 			$action = false;
		// 		}
		// 	} else {
		// 		$action = false;
		// 	}
		// } else {
		if ($s_date != $nowdate) {
			$action = true;
		} else {
			$action = false;
		}
		// }

		// if ($action == true) {
		// 	//vehicle operational data
		// 	$dt_operational = $this->getDTOperational($company, $s_date, $e_date);
		// 	$this->params['total_operational_units'] = $dt_operational['units']; //total violation units
		// }
		$this->params['ratio'] = $action;



		$data = array();
		$data2 = array();

		if ($vehicle != "all") {
			$exp = explode("/", $vehicle);
			$vehicle_imei = $exp[0];
			$vehicle_device = $exp[1];

			$companydata = $this->getcompany_bycreator(null, $exp[2]);
			$count_company = 1;
			$company = $exp[2];
			$data_company[$exp[2]] = $companydata[0]->company_name;
			$master_company[0] = $companydata[0]->company_name;
			$company = $exp[2];
			$opposite_company[$companydata[0]->company_name] = $exp[2];

			if ($violation == "6") {
				$data2 = $this->getOverspeed2_status($dboverspeed, $company, $vehicle_device, $sdate, $edate, $status);
			} else {
				if ($violation == "all") {
					$data2 = $this->getOverspeed2_status($dboverspeed, $company, $vehicle_device, $sdate, $edate, $status);
					$data = $this->getSecurityEvidence_status($dbtable, $company, $vehicle_imei, $violation, $dataalarmtype, $sdate, $edate, $status);
				} else {
					//$data2 = $this->getOverspeed2($dboverspeed, $company, $vehicle_device, $sdate, $edate); //additional dari ovs alert bisa kebuka untuk all mitra 1 bulan
					$data = $this->getSecurityEvidence_status($dbtable, $company, $vehicle_imei, $violation, $dataalarmtype, $sdate, $edate, $status);
				}
			}
		} else {
			//company data
			$companydata = $this->getcompany_bycreator(4408);
			$data_company = array(); //format 1
			$opposite_company = array(); //kebalikan format 1
			$master_company = array(); //format 2
			if ($company != "all") {
				$exp = explode("@", $company);
				$count_company = 1;
				$company = $exp[0];
				$data_company[$exp[0]] = $exp[1];
				$master_company[0] = $exp[1];
				$company = $exp[0];
				$opposite_company[$exp[1]] = $exp[0];
			} else {
				$count_company = count($companydata);
				for ($i = 0; $i < $count_company; $i++) {
					$data_company[$companydata[$i]->company_id] = $companydata[$i]->company_name;
					$opposite_company[$companydata[$i]->company_name] = $companydata[$i]->company_id;
					$master_company[$i] = $companydata[$i]->company_name;
				}
			}

			if ($violation == "6") {
				$data2 = $this->getOverspeed2_status($dboverspeed, $company, $vehicle, $sdate, $edate, $status);
			} else {
				if ($violation == "all") {
					$data2 = $this->getOverspeed2_status($dboverspeed, $company, $vehicle, $sdate, $edate, $status);
					$data = $this->getSecurityEvidence_status($dbtable, $company, $vehicle, $violation, $dataalarmtype, $sdate, $edate, $status);
				} else {
					//$data2 = $this->getOverspeed2_status($dboverspeed, $company, $vehicle_device, $sdate, $edate, $status); //additional dari ovs alert bisa kebuka untuk all mitra 1 bulan
					$data = $this->getSecurityEvidence_status($dbtable, $company, $vehicle, $violation, $dataalarmtype, $sdate, $edate , $status);
				}
			}
		}

		/* print_r($sdate." ".$edate);
		print_r($data2);exit(); */

		$numrows = count($data);
		// $numrows = 0;
		$numrows2 = count($data2);
		// $numrows2 = 0;
		$total_data = 0;
		$data_fix = array();
		$data_table = array();
		$dhour = array();
		$dcompany = array();
		$dviolation = array();
		$c = array();
		$l = array();
		$seleksi_data = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_overspeed = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table_overspeed = array(); //untuk seleksi multiple data yang sama
		$total_violation_units = array();
		$seleksi_unit = array();
		$top_ten = array();
		
		$datarekap_true = array();
		$datarekap_false = array();
		$datarekap_undefined = array();
		$datarekap_hazard = array();
		$datarekap_record = array();
		
		$datarekap_true_ovs = array();
		$datarekap_false_ovs = array();
		$datarekap_undefined_ovs = array();
		$datarekap_hazard_ovs = array();
		$datarekap_record_ovs = array();
		
		$total_undefined = 0;
		$total_false = 0;
		$total_true = 0;
		$total_rekap = 0;
		$total_hazard = 0;
		$total_record = 0;
		
		$total_undefined_ovs = 0;
		$total_false_ovs = 0;
		$total_true_ovs = 0;
		$total_rekap_ovs = 0;
		$total_hazard_ovs = 0;
		$total_record_ovs = 0;
				
		if ($company == "all") {
			if ($numrows > 0) {
				for ($i = 0; $i < $numrows; $i++) {
					if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
						$datetime = $data[$i]['alarm_report_start_time'];
						//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
						$vehiclen = $data[$i]['alarm_report_vehicle_no'];
						$loction = $data[$i]['alarm_report_location_start'];
						$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);
						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }

						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}

						if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
							$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
						} else {
							$vltion = $data[$i]['alarm_report_type'];
						}





						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {

							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0],
						);

						if (!isset($data_fix[$h[0]][$cmpny])) {
							$data_fix[$h[0]][$cmpny] = array();
							array_push($data_fix[$h[0]][$cmpny], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_fix[$h[0]][$cmpny], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							} else {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_fix[$h[0]][$cmpny], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							} else {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							}
						}
					}
				}
			}
			if ($numrows2 > 0) {
				for ($i = 0; $i < $numrows2; $i++) {
					if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
						// $datetime = $data[$i]['overspeed_report_gps_time'];
						$datetime = $data2[$i]['overspeed_report_gps_time'];
						// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));
						$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];

						$vltion = "Overspeed";
						$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
						$locationn = $data2[$i]['overspeed_report_location'];
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }


						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}


						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {
							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0],
							"level" => $data2[$i]['overspeed_report_level']
						);

						if (!isset($data_fix[$h[0]][$cmpny])) {
							$data_fix[$h[0]][$cmpny] = array();
							array_push($data_fix[$h[0]][$cmpny], $d);
							$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;

							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_fix[$h[0]][$cmpny], $d);
								if (!isset($top_ten[$vltion][$vehiclen])) {
									$top_ten[$vltion][$vehiclen] = 1;
								} else {
									$top_ten[$vltion][$vehiclen] += 1;
								}
								$total_data++;
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
						} else {
							if (!isset($seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_table[$vltion][$cmpny], $d);
							}
						}
					}
				}
			}
			if ($total_data == 0) {
				$callback['error'] = true;
				$callback['message'] = "Data Empty!";
				$callback['input'] = $input;
				echo json_encode($callback);
				return;
			}
		} else {
			
			if ($numrows > 0) {
				for ($i = 0; $i < $numrows; $i++) {
					
					if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
						$datetime = $data[$i]['alarm_report_start_time'];
						//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
						$vehiclen = $data[$i]['alarm_report_vehicle_no'];
						//$loction = $data[$i]['alarm_report_location_start']; //filter by location didisble sementara
						$loction = $data[$i]['alarm_report_id'];
						$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];

						if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
							$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
						} else {
							$vltion = $data[$i]['alarm_report_type'];
						}
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }

						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}


						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {

							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0],
						);
						
						
						//kondisi IF driver Abnormal didisable dahulu
						/* if (!isset($data_fix[$h[0]][$vltion])) {
							$data_fix[$h[0]][$vltion] = array();
							array_push($data_fix[$h[0]][$vltion], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_fix[$h[0]][$vltion], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							} else {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_fix[$h[0]][$vltion], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							} else {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							}
						} */
						
						
						if (!isset($data_fix[$h[0]][$vltion])) {
							$data_fix[$h[0]][$vltion] = array();
							array_push($data_fix[$h[0]][$vltion], $d);
							
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_fix[$h[0]][$vltion], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							
						} else {
							
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							
						}
						
						//data rekap
						if($data[$i]['alarm_report_truefalse_up'] == "1"){
							$info_true = $data[$i]['alarm_report_id'];
							array_push($datarekap_true,$info_true);
							
						}else if($data[$i]['alarm_report_truefalse_up'] == "2"){
							$info_false = $data[$i]['alarm_report_id'];
							array_push($datarekap_false,$info_false);
							
						}else{
							$info_undefined = $data[$i]['alarm_report_id'];
							array_push($datarekap_undefined,$info_undefined);
							
						}
						
						if($data[$i]['alarm_report_status_sendhazard'] == "1"){
							$info_hazard = $data[$i]['alarm_report_id'];
							array_push($datarekap_hazard,$info_hazard);
							
						}
						
						if($data[$i]['alarm_report_status_sendrecord'] == "1"){
							$info_record = $data[$i]['alarm_report_id'];
							array_push($datarekap_record,$info_record);
							
						}
						
						
						
					}
				}
				
				
				//summary rekap value
						$total_undefined = count($datarekap_undefined);
						$total_false = count($datarekap_false);
						$total_true = count($datarekap_true);
						$total_rekap = $total_undefined+$total_false+$total_true;
						
						$total_hazard = count($datarekap_hazard);
						$total_record = count($datarekap_record);
			
			}
			if ($numrows2 > 0) {
				for ($i = 0; $i < $numrows2; $i++) {
					if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
						// $datetime = $data[$i]['overspeed_report_gps_time'];
						$datetime = $data2[$i]['overspeed_report_gps_time'];
						// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));
						$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];

						$vltion = "Overspeed";
						$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
						//$locationn = $data2[$i]['overspeed_report_location']; //filter by location didisable dulu
						$locationn = $data2[$i]['overspeed_report_id'];
						
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }


						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}


						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {
							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0]
						);

						if (!isset($data_fix[$h[0]][$vltion])) {
							$data_fix[$h[0]][$vltion] = array();
							array_push($data_fix[$h[0]][$vltion], $d);
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
							$total_data++;
						} else {
							if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_fix[$h[0]][$vltion], $d);
								if (!isset($top_ten[$vltion][$vehiclen])) {
									$top_ten[$vltion][$vehiclen] = 1;
								} else {
									$top_ten[$vltion][$vehiclen] += 1;
								}
								$total_data++;
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
						} else {
							if (!isset($seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_table[$vltion][$cmpny], $d);
							}
						}
						
						
						//data rekap
						if($data2[$i]['overspeed_report_truefalse_up'] == "1"){
							
							$info_true_ovs = $data2[$i]['overspeed_report_id'];
							array_push($datarekap_true_ovs,$info_true_ovs);
							
						}else if($data2[$i]['overspeed_report_truefalse_up'] == "2"){
							
							$info_false_ovs = $data2[$i]['overspeed_report_id'];
							array_push($datarekap_false_ovs,$info_false_ovs);
						}else{
							$info_undefined_ovs = $data2[$i]['overspeed_report_id'];
							array_push($datarekap_undefined_ovs,$info_undefined_ovs);
						}
						
						if($data2[$i]['overspeed_report_status_sendhazard'] == "1"){
							$info_hazard_ovs = $data2[$i]['overspeed_report_id'];
							array_push($datarekap_hazard_ovs,$info_hazard_ovs);
							
						}
						
						if($data2[$i]['overspeed_report_status_sendrecord'] == "1"){
							$info_record_ovs = $data2[$i]['overspeed_report_id'];
							array_push($datarekap_record_ovs,$info_record_ovs);
							
						}
						
						
						
						
					}
				}
				
				//summary rekap value
						$total_undefined_ovs = count($datarekap_undefined_ovs);
						$total_false_ovs = count($datarekap_false_ovs);
						$total_true_ovs = count($datarekap_true_ovs);
						$total_rekap_ovs = $total_undefined_ovs+$total_false_ovs+$total_true_ovs;
						
						$total_hazard_ovs = count($datarekap_hazard_ovs);
						$total_record_ovs = count($datarekap_record_ovs);
			
			}
			
			$gtotal_rekap = $total_rekap+$total_rekap_ovs;
			$gtotal_undefined = $total_undefined+$total_undefined_ovs;
			$gtotal_false = $total_false+$total_false_ovs;
			$gtotal_true = $total_true+$total_true_ovs;
			$gtotal_hazard = $total_hazard+$total_hazard_ovs;
			$gtotal_record = $total_record+$total_record_ovs;
			
			
			if($gtotal_rekap >0){
				//%tase gtotal
				$persen_undefined = round($gtotal_undefined/$gtotal_rekap*100,2);
				$persen_false = round($gtotal_false/$gtotal_rekap*100,2);
				$persen_true = round($gtotal_true/$gtotal_rekap*100,2);
				
				$persen_total = $persen_undefined+$persen_false+$persen_true;
				
				
				$this->params['persen_undefined'] = $persen_undefined;
				$this->params['persen_false'] = $persen_false;
				$this->params['persen_true'] = $persen_true;
				$this->params['persen_total'] = $persen_total;
				
				$this->params['gtotal_rekap'] = $gtotal_rekap;
				$this->params['gtotal_undefined'] = $gtotal_undefined;
				$this->params['gtotal_false'] = $gtotal_false;
				$this->params['gtotal_true'] = $gtotal_true;
				$this->params['gtotal_hazard'] = $gtotal_hazard;
				$this->params['gtotal_record'] = $gtotal_record;
				
			}
			
				
			
			if ($total_data == 0) {
				$callback['error'] = true;
				$callback['message'] = "Data Empty!";
				echo json_encode($callback);
				return;
			}
		}


		$input = array(
			"company" => $company,
			"violation" => $violation,
			"vehicle" => $vehicle,
			"periode" => $periode,
			"date_start" => $sdate,
			"date_end" => $edate
		);

		sort($dhour);

		/* var_dump($total_violation_units);
		 exit(); */

		//print_r($numrows+$numrows2);exit();

		$this->params['input'] = $input;
		$this->params['sdate'] = $sdate;
		$this->params['edate'] = $edate;
		$this->params['opposite_company'] = $opposite_company;
		$this->params['total_data'] = $total_data;
		$this->params['date'] = $datein;
		$this->params['data_hour'] = $dhour; //data hour dari source
		$this->params['data_company'] = $dcompany; //data company dari source
		$this->params['data_alarm'] = $dviolation; //data violation dari source

		$this->params['data_fix'] = $data_fix;
		$this->params['data_table'] = $data_table;
		$this->params['top_violation'] = $top_ten; //master company format 2
		$this->params['master_company'] = $master_company; //master company format 2
		$this->params['total_violation_units'] = $total_violation_units; //total violation units
		$this->params['master_vehicle'] = $total_unit_percontractor; //master vehicle
		$this->params['master_violation'] = $master_violation; //master company format 2


		$html                    = $this->load->view('newdashboard/hseboard/v_daily_violation_result2', $this->params, true);



		$callback['error'] = false;
		$callback['input'] = $input;
		$callback['data_fix'] = $data_fix;
		$callback["html"]        = $html;

		echo json_encode($callback);
	}

	function search_summary()
	{
		ini_set('memory_limit', "5G");
		ini_set('max_execution_time', 300); // 5 minutes
		// $datein    = $this->input->post("date");
		$company = $this->input->post("company");
		$violation = $this->input->post("violation");
		$vehicle = $this->input->post("vehicle");
		$filter_true_false = $this->input->post("filter_true_false");
		// $date = date("Y-m-d", strtotime($datein));
		// $month = date("F", strtotime($datein));
		// $monthforparam = date("m", strtotime($datein));
		// $year = date("Y", strtotime($datein));
		$report     = "alarm_evidence_";
		$overspeed  = "overspeed_hour_";
		$ritase     = "ritase_full_";
		$periode = $this->input->post("periode");
		$year = date("Y");
		$mont = date("m");
		$nowday = date("d");
		$err = false;
		$msg = '';
		if ($periode == "today") {
			$sdate = date("Y-m-d 00:00:00");
			$edate = date("Y-m-d H:i:s");
			$datein = date("d-m-Y", strtotime($sdate));
		} else if ($periode == "yesterday") {
			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
			$datein = date("d-m-Y", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$year = date("Y");
			$mont = date("m");
			$nowday = date("d");
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}
			$sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-" . $firstday));
			$edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
		} else if ($periode == "this_month") {
			$firstday = "1";
			$sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-1"));
			$edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
		} else if ($periode == "custom") {
			$sdate = $this->input->post("sdate");
			$edate = $this->input->post("edate");
			$sdate = date("Y-m-d 00:00:00", strtotime($sdate));
			$edate = date("Y-m-d 23:59:59", strtotime($edate));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
			$diff = strtotime($edate) - strtotime($sdate);
			if ($diff < 0) {
				$err = true;
				$msg = "Date is not correct!";
			}
			$diff = strtotime(date("Y-m-d")) - strtotime($sdate);
			if ($diff < 0) {
				$err = true;
				$msg = "Date is not correct!";
			}
			/* if ($company == "all") {
				$diff = strtotime($edate) - strtotime($sdate);
				if ($diff > 604800) {
					$err = true;
					$msg = "Maximum date range for all contractors is 7 days!";
				}
			} */
			$diff1 = date("m", strtotime($sdate));
			$diff2 = date("m", strtotime($edate));
			if ($diff1 != $diff2) {
				$err = true;
				$msg = "Date must be in the same month!";
			}
			$diff1 = date("Y", strtotime($sdate));
			$diff2 = date("Y", strtotime($edate));
			if ($diff1 != $diff2) {
				$err = true;
				$msg = "Date must be in the same year!";
			}
		}

		$month = date("F", strtotime($sdate));
		$year = date("Y", strtotime($sdate));

		if ($err == true) {
			$callback['error'] = true;
			$callback['message'] = $msg;
			echo json_encode($callback);
			return;
		}

		switch ($month) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dboverspeed = $overspeed . "januari_" . $year;
				$dbritase = $ritase . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dboverspeed = $overspeed . "februari_" . $year;
				$dbritase = $ritase . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dboverspeed = $overspeed . "maret_" . $year;
				$dbritase = $ritase . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dboverspeed = $overspeed . "april_" . $year;
				$dbritase = $ritase . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dboverspeed = $overspeed . "mei_" . $year;
				$dbritase = $ritase . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dboverspeed = $overspeed . "juni_" . $year;
				$dbritase = $ritase . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dboverspeed = $overspeed . "juli_" . $year;
				$dbritase = $ritase . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dboverspeed = $overspeed . "agustus_" . $year;
				$dbritase = $ritase . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dboverspeed = $overspeed . "september_" . $year;
				$dbritase = $ritase . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dboverspeed = $overspeed . "oktober_" . $year;
				$dbritase = $ritase . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dboverspeed = $overspeed . "november_" . $year;
				$dbritase = $ritase . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dboverspeed = $overspeed . "desember_" . $year;
				$dbritase = $ritase . "desember_" . $year;
				break;
		}


		$input = array(
			'date_start' => $sdate,
			'date_end' => $edate,
			'db' => $dbtable,
			'db_overspeed' => $dboverspeed
		);
		//violation data

		$rviolation = $this->getViolation(); //ambil master data violation alrmmaster
		$dataviolation = array(); //untuk simpan data violation
		$master_violation = array(); //untuk simpan data violation format 2
		$allviolation = array(); //untuk simpan id violation
		$nr = count($rviolation);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$dataviolation[$rviolation[$i]["alarmmaster_id"]] = $rviolation[$i]["alarmmaster_name"];
				$master_violation[$i] = $rviolation[$i]["alarmmaster_name"];
				array_push($allviolation, $rviolation[$i]["alarmmaster_id"]);
			}
		}

		$dataalarmtype = array(); //untuk query where_in
		$dataviolationalarmtype = array(); //untuk ditampilkan di view
		if ($violation != "all") {
			$alarmtype = $this->getAlarmtype($violation);
		} else {
			$alarmtype = $this->getAlarmtype(null, $allviolation);
		}
		$nr = count($alarmtype);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				if (!isset($dataviolationalarmtype[$alarmtype[$i]["alarm_type"]])) {
					if ($violation == "all") {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$alarmtype[$i]["alarm_master_id"]];
					} else {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$violation];
					}
				}
				array_push($dataalarmtype, $alarmtype[$i]["alarm_type"]);
			}
		}


		//vehicle data
		$this->db->select("vehicle_name,vehicle_no,vehicle_company");
		$this->db->where("vehicle_status <>", 3);

		if ($company != "all") {
			$this->db->where("vehicle_company", $company);
		}
		$qd = $this->db->get("vehicle");
		$total_unit = $qd->num_rows();
		$rd = $qd->result();
		$total_unit_percontractor = array();
		for ($x = 0; $x < $total_unit; $x++) {
			if ($rd[$x]->vehicle_company != null) {
				if (!isset($total_unit_percontractor[$rd[$x]->vehicle_company])) {
					$total_unit_percontractor[$rd[$x]->vehicle_company] = 1;
				} else {
					$jml = (int)$total_unit_percontractor[$rd[$x]->vehicle_company] + 1;
					$total_unit_percontractor[$rd[$x]->vehicle_company] = $jml;
				}
			}
		}


		$s_date = date("Y-m-d", strtotime($sdate));
		$nowdate = date("Y-m-d");
		// $action = false;
		// if ($company == 'all') {
		// 	if ($s_date == $e_date) {
		// 		if ($s_date != $nowdate) {
		// 			$action = true;
		// 		} else {
		// 			$action = false;
		// 		}
		// 	} else {
		// 		$action = false;
		// 	}
		// } else {
		if ($s_date != $nowdate) {
			$action = true;
		} else {
			$action = false;
		}
		// }

		// if ($action == true) {
		// 	//vehicle operational data
		// 	$dt_operational = $this->getDTOperational($company, $s_date, $e_date);
		// 	$this->params['total_operational_units'] = $dt_operational['units']; //total violation units
		// }
		$this->params['ratio'] = $action;



		$data = array();
		$data2 = array();
		$data3 = array();

		if ($vehicle != "all") {
			$exp = explode("/", $vehicle);
			$vehicle_imei = $exp[0];
			$vehicle_device = $exp[1];

			$companydata = $this->getcompany_bycreator(null, $exp[2]);
			$count_company = 1;
			$company = $exp[2];
			$data_company[$exp[2]] = $companydata[0]->company_name;
			$master_company[0] = $companydata[0]->company_name;
			$company = $exp[2];
			$opposite_company[$companydata[0]->company_name] = $exp[2];

			if ($violation == "6") {
				$data2 = $this->getOverspeed2($dboverspeed, $company, $vehicle_device, $sdate, $edate, $filter_true_false);
				$data3 = $this->getRitase($dbritase, $company, $vehicle_device, $sdate, $edate);
			} else {
				if ($violation == "all") {
					$data2 = $this->getOverspeed2($dboverspeed, $company, $vehicle_device, $sdate, $edate, $filter_true_false);
					$data = $this->getSecurityEvidence($dbtable, $company, $vehicle_imei, $violation, $dataalarmtype, $sdate, $edate, $filter_true_false);
					$data3 = $this->getRitase($dbritase, $company, $vehicle_device, $sdate, $edate);
				} else {
					$data = $this->getSecurityEvidence($dbtable, $company, $vehicle_imei, $violation, $dataalarmtype, $sdate, $edate, $filter_true_false);
					$data3 = $this->getRitase($dbritase, $company, $vehicle_device, $sdate, $edate);
				}
			}
		} else {
			//company data
			$companydata = $this->getcompany_bycreator(4408);
			$data_company = array(); //format 1
			$opposite_company = array(); //kebalikan format 1
			$master_company = array(); //format 2
			if ($company != "all") {
				$exp = explode("@", $company);
				$count_company = 1;
				$company = $exp[0];
				$data_company[$exp[0]] = $exp[1];
				$master_company[0] = $exp[1];
				$company = $exp[0];
				$opposite_company[$exp[1]] = $exp[0];
			} else {
				$count_company = count($companydata);
				for ($i = 0; $i < $count_company; $i++) {
					$data_company[$companydata[$i]->company_id] = $companydata[$i]->company_name;
					$opposite_company[$companydata[$i]->company_name] = $companydata[$i]->company_id;
					$master_company[$i] = $companydata[$i]->company_name;
				}
			}

			if ($violation == "6") {
				$data2 = $this->getOverspeed2($dboverspeed, $company, $vehicle, $sdate, $edate, $filter_true_false);
				$data3 = $this->getRitase($dbritase, $company, $vehicle, $sdate, $edate);
			} else {
				if ($violation == "all") {
					$data2 = $this->getOverspeed2($dboverspeed, $company, $vehicle, $sdate, $edate, $filter_true_false);
					$data = $this->getSecurityEvidence($dbtable, $company, $vehicle, $violation, $dataalarmtype, $sdate, $edate, $filter_true_false);
					$data3 = $this->getRitase($dbritase, $company, $vehicle, $sdate, $edate);
				} else {
					$data = $this->getSecurityEvidence($dbtable, $company, $vehicle, $violation, $dataalarmtype, $sdate, $edate, $filter_true_false);
					$data3 = $this->getRitase($dbritase, $company, $vehicle, $sdate, $edate);
				}
			}
		}


		//total ritase per contractor
		$total_data3 = count($data3);
		$total_rit_percontractor = array();
		for ($z = 0; $z < $total_data3; $z++) {
			if ($data3[$z]->ritase_report_vehicle_company != null) {
				if (!isset($total_rit_percontractor[$data3[$z]->ritase_report_vehicle_company])) {
					$total_rit_percontractor[$data3[$z]->ritase_report_vehicle_company] = 1;
				} else {
					$jml_rit = (int)$total_rit_percontractor[$data3[$z]->ritase_report_vehicle_company] + 1;
					$total_rit_percontractor[$data3[$z]->ritase_report_vehicle_company] = $jml_rit;
				}
			}
		}

		/* print_r($sdate." ".$edate);
		print_r($data2);exit(); */

		$numrows = count($data);
		// $numrows = 0;
		$numrows2 = count($data2);
		// $numrows2 = 0;


		$total_data = 0;
		$data_fix = array();
		$data_table = array();
		$dhour = array();
		$dcompany = array();
		$dviolation = array();
		$c = array();
		$l = array();
		$seleksi_data = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_overspeed = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table_overspeed = array(); //untuk seleksi multiple data yang sama
		$total_violation_units = array();
		$seleksi_unit = array();
		$top_ten = array();
		if ($company == "all") {
			if ($numrows > 0) {
				for ($i = 0; $i < $numrows; $i++) {
					if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
						$datetime = $data[$i]['alarm_report_start_time'];
						//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
						$vehiclen = $data[$i]['alarm_report_vehicle_no'];
						$loction = $data[$i]['alarm_report_location_start'];
						$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);
						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }

						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}

						if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
							$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
						} else {
							$vltion = $data[$i]['alarm_report_type'];
						}

						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {

							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0],
						);

						if (!isset($data_fix[$h[0]][$cmpny])) {
							$data_fix[$h[0]][$cmpny] = array();
							array_push($data_fix[$h[0]][$cmpny], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_fix[$h[0]][$cmpny], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							} else {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_fix[$h[0]][$cmpny], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							} else {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							}
						}
					}
				}
			}
			if ($numrows2 > 0) {
				for ($i = 0; $i < $numrows2; $i++) {
					if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
						// $datetime = $data[$i]['overspeed_report_gps_time'];
						$datetime = $data2[$i]['overspeed_report_gps_time'];
						// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));
						$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];

						$vltion = "Overspeed";
						$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
						$locationn = $data2[$i]['overspeed_report_location'];
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }


						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}


						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {
							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0],
							"level" => $data2[$i]['overspeed_report_level']
						);

						if (!isset($data_fix[$h[0]][$cmpny])) {
							$data_fix[$h[0]][$cmpny] = array();
							array_push($data_fix[$h[0]][$cmpny], $d);
							$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;

							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_fix[$h[0]][$cmpny], $d);
								if (!isset($top_ten[$vltion][$vehiclen])) {
									$top_ten[$vltion][$vehiclen] = 1;
								} else {
									$top_ten[$vltion][$vehiclen] += 1;
								}
								$total_data++;
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
						} else {
							if (!isset($seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_table[$vltion][$cmpny], $d);
							}
						}
					}
				}
			}
			if ($total_data == 0) {
				$callback['error'] = true;
				$callback['message'] = "Data Empty!";
				$callback['input'] = $input;
				echo json_encode($callback);
				return;
			}
		} else {
			if ($numrows > 0) {
				for ($i = 0; $i < $numrows; $i++) {
					if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
						$datetime = $data[$i]['alarm_report_start_time'];
						//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
						$vehiclen = $data[$i]['alarm_report_vehicle_no'];
						$loction = $data[$i]['alarm_report_location_start'];
						$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];

						if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
							$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
						} else {
							$vltion = $data[$i]['alarm_report_type'];
						}
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }

						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}


						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {

							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0],
						);

						if (!isset($data_fix[$h[0]][$vltion])) {
							$data_fix[$h[0]][$vltion] = array();
							array_push($data_fix[$h[0]][$vltion], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$total_data++;
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_fix[$h[0]][$vltion], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							} else {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_fix[$h[0]][$vltion], $d);
									if (!isset($top_ten[$vltion][$vehiclen])) {
										$top_ten[$vltion][$vehiclen] = 1;
									} else {
										$top_ten[$vltion][$vehiclen] += 1;
									}
									$total_data++;
								}
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							} else {
								if (!isset($seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data_table[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_table[$vltion][$cmpny], $d);
								}
							}
						}
					}
				}
			}
			if ($numrows2 > 0) {
				for ($i = 0; $i < $numrows2; $i++) {
					if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
						// $datetime = $data[$i]['overspeed_report_gps_time'];
						$datetime = $data2[$i]['overspeed_report_gps_time'];
						// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));
						$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];

						$vltion = "Overspeed";
						$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
						$locationn = $data2[$i]['overspeed_report_location'];
						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						// if (!isset($total_violation_units[$cmpny][$vehiclen])) {
						// 	$total_violation_units[$cmpny][$vehiclen] = 1;
						// }


						if (!isset($total_violation_units[$cmpny])) {
							// $units[$cmpny][$vhicle] = 1;
							$total_violation_units[$cmpny] = 1;
							$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
						} else {
							if (!isset($seleksi_unit[$exp[0]][$cmpny][$vehiclen])) {
								$seleksi_unit[$exp[0]][$cmpny][$vehiclen] = 1;
								$total_violation_units[$cmpny] += 1;
							}
						}


						if (!isset($hour[$h[0]])) {
							$hour[$h[0]] = 1;
							$dhour[] = $h[0];
						}
						if (!isset($c[$cmpny])) {
							$c[$cmpny] = $cmpny;
							$dcompany[] = $cmpny;
						}
						if (!isset($l[$vltion])) {
							$l[$vltion] = $vltion;
							$dviolation[] = $vltion;
						}
						$d = array(
							"company" => $cmpny,
							"vehicle" => $vehiclen,
							"violation" => $vltion,
							"hour" => $h[0]
						);

						if (!isset($data_fix[$h[0]][$vltion])) {
							$data_fix[$h[0]][$vltion] = array();
							array_push($data_fix[$h[0]][$vltion], $d);
							if (!isset($top_ten[$vltion][$vehiclen])) {
								$top_ten[$vltion][$vehiclen] = 1;
							} else {
								$top_ten[$vltion][$vehiclen] += 1;
							}
							$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
							$total_data++;
						} else {
							if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_fix[$h[0]][$vltion], $d);
								if (!isset($top_ten[$vltion][$vehiclen])) {
									$top_ten[$vltion][$vehiclen] = 1;
								} else {
									$top_ten[$vltion][$vehiclen] += 1;
								}
								$total_data++;
							}
						}

						if (!isset($data_table[$vltion][$cmpny])) {
							$data_table[$vltion][$cmpny] = array();
							array_push($data_table[$vltion][$cmpny], $d);
							$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
						} else {
							if (!isset($seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
								$seleksi_data_table_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
								array_push($data_table[$vltion][$cmpny], $d);
							}
						}
					}
				}
			}
			if ($total_data == 0) {
				$callback['error'] = true;
				$callback['message'] = "Data Empty!";
				echo json_encode($callback);
				return;
			}
		}


		$input = array(
			"company" => $company,
			"violation" => $violation,
			"vehicle" => $vehicle,
			"periode" => $periode,
			"date_start" => $sdate,
			"date_end" => $edate
		);

		sort($dhour);

		/* var_dump($total_violation_units);
		 exit(); */

		//print_r($data_table);
		//print_r($opposite_company);
		//print_r($total_unit_percontractor);
		//exit();

		$this->params['input'] = $input;
		$this->params['sdate'] = $sdate;
		$this->params['edate'] = $edate;
		$this->params['opposite_company'] = $opposite_company;
		$this->params['total_data'] = $total_data;
		$this->params['date'] = $datein;
		$this->params['data_hour'] = $dhour; //data hour dari source
		$this->params['data_company'] = $dcompany; //data company dari source
		$this->params['data_alarm'] = $dviolation; //data violation dari source

		$this->params['data_fix'] = $data_fix;
		$this->params['data_table'] = $data_table;
		$this->params['top_violation'] = $top_ten; //master company format 2
		$this->params['master_company'] = $master_company; //master company format 2
		$this->params['total_violation_units'] = $total_violation_units; //total violation units
		$this->params['master_vehicle'] = $total_unit_percontractor; //master vehicle
		$this->params['master_violation'] = $master_violation; //master company format 2
		$this->params['master_ritase'] = $total_rit_percontractor;

		$html = $this->load->view('newdashboard/hse/v_summary_violation_result', $this->params, true);



		$callback['error'] = false;
		$callback['input'] = $input;
		$callback['data_fix'] = $data_fix;
		$callback["html"]        = $html;

		echo json_encode($callback);
	}

	function infodetail()
	{
		$sdate    = $this->input->post("start_date");
		$edate    = $this->input->post("end_date");
		$company = $this->input->post("company");
		$company_name = $this->input->post("company_name");
		$violation = $this->input->post("violation");
		$filter_true_false = $this->input->post("filter_true_false");
		$start_date = date("d-m-Y", strtotime($sdate));
		$end_date = date("d-m-Y", strtotime($edate));
		$userid = 4408;

		$report     = "alarm_evidence_";
		$overspeed  = "overspeed_";
		$month = date("F", strtotime($sdate));
		$monthprm = date("m", strtotime($sdate));
		$year = date("Y", strtotime($sdate));
		switch ($month) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dboverspeed = $overspeed . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dboverspeed = $overspeed . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dboverspeed = $overspeed . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dboverspeed = $overspeed . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dboverspeed = $overspeed . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dboverspeed = $overspeed . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dboverspeed = $overspeed . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dboverspeed = $overspeed . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dboverspeed = $overspeed . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dboverspeed = $overspeed . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dboverspeed = $overspeed . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dboverspeed = $overspeed . "desember_" . $year;
				break;
		}

		//violation data

		$rviolation = $this->getViolation(); //ambil master data violation alrmmaster
		$dataviolation = array(); //untuk simpan data violation
		$master_violation = array(); //untuk simpan data violation format 2
		$allviolation = array(); //untuk simpan id violation
		$nr = count($rviolation);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$dataviolation[$rviolation[$i]["alarmmaster_id"]] = $rviolation[$i]["alarmmaster_name"];
				$master_violation[$i] = $rviolation[$i]["alarmmaster_name"];
				array_push($allviolation, $rviolation[$i]["alarmmaster_id"]);
			}
		}

		$dataalarmtype = array(); //untuk query where_in
		$dataviolationalarmtype = array(); //untuk ditampilkan di view
		if ($violation != "all") {
			$alarmtype = $this->getAlarmtype($violation);
		} else {
			$alarmtype = $this->getAlarmtype(null, $allviolation);
		}
		$nr = count($alarmtype);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				if (!isset($dataviolationalarmtype[$alarmtype[$i]["alarm_type"]])) {
					if ($violation == "all") {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$alarmtype[$i]["alarm_master_id"]];
					} else {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$violation];
					}
				}
				array_push($dataalarmtype, $alarmtype[$i]["alarm_type"]);
			}
		}

		//company data
		$data_company = array(); //format 1
		$opposite_company = array(); //kebalikan format 1
		$master_company = array(); //format 2
		$company = $company;
		$data_company[$company] = $company_name;
		$master_company[0] = $company_name;
		$company = $company;
		$opposite_company[$company_name] = $company;




		$data = array();
		$data2 = array();
		if ($violation == "6") {
			$data2 = $this->getOverspeed($dboverspeed, $company, "all", $sdate, $edate);
		} else {
			if ($violation == "all") {
				$data2 = $this->getOverspeed($dboverspeed, $company, "all", $sdate, $edate);
				$data = $this->getSecurityEvidence($dbtable, $company, "all", $violation, $dataalarmtype, $sdate, $edate, $filter_true_false);
			} else {
				$data = $this->getSecurityEvidence($dbtable, $company, "all", $violation, $dataalarmtype, $sdate, $edate, $filter_true_false);
			}
		}

		$numrows = count($data);
		// $numrows = 0;
		$numrows2 = count($data2);
		// $numrows2 = 0;
		$total_data = 0;
		$seleksi_data = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_overspeed = array(); //untuk seleksi multiple data yang sama
		$master_video = array();
		if ($numrows > 0) {
			for ($i = 0; $i < $numrows; $i++) {
				if ($data[$i]['alarm_report_media'] == 0) {
					if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
						$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];
						$cmpny_nm = $data_company[$company];
						$datetime = $data[$i]['alarm_report_start_time'];
						//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
						$vehiclen = $data[$i]['alarm_report_vehicle_no'];
						$loction = $data[$i]['alarm_report_location_start'];
						$gpsstts = $data[$i]['alarm_report_gpsstatus'];
						if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
							$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
						} else {
							$vltion = $data[$i]['alarm_report_type'];
						}

						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						$h_shift = date('H', strtotime($exp[1]));
						$shift_nm = $this->getshift_byhour($h_shift);
						$reportdetaildecode = explode("|", $gpsstts);
						$speedgps = number_format($reportdetaildecode[4]/10, 0, '.', '');


						//prepare attachment
						$monthforparam = $monthprm;
						$user_id_role = 2;

						$imagealertid = $data[$i]['alarm_report_id'];
						$sdate_alert = $data[$i]['alarm_report_start_time'];
						$alertvehicleid = $data[$i]['alarm_report_vehicle_id'];

						$reportdetailvideo = $this->getvideo_alarmevidence($dbtable, $alertvehicleid, $sdate_alert);



						if(count($reportdetailvideo) > 0){
							$videoalertid = $reportdetailvideo[0]['alarm_report_id'];
							$attachmentlink = "http://attachment.pilartech.co.id/attachment/".$videoalertid.'/'.$imagealertid.'/'.$monthforparam.'/'.$year.'/'.$user_id_role.'/'.$userid;

						}else{
							$videoalertid = 0;
							$attachmentlink = "#";
						}


						//print_r($attachmentlink);exit();

						$info = $data[$i]['alarm_report_name'];
						$expinfo = explode("Level ", $info);
						$exinfo = explode(" ", $expinfo[1]);
						if ($exinfo[0] == "One") {
							$level = 1;
						} else if ($exinfo[0] == "Two") {
							$level = 2;
						} else if ($exinfo[0] == "Three") {
							$level = 3;
						} else if ($exinfo[0] == "Four") {
							$level = 4;
						} else {
							$level = " ";
						}

						$d = array(
							"company" => $cmpny_nm,
							"shift" => $shift_nm,
							"speed" => $speedgps,
							"jalur" => "",
							"vehicle" => $vehiclen,
							"location" => $loction,
							"violation" => $vltion,
							"date" => date("d-m-Y", strtotime($exp[0])),
							"time" => $exp[1],
							"coordinate" => $data[$i]['alarm_report_coordinate_start'],
							"info" => $info,
							"hour" => $h[0],
							"file_url" => $data[$i]['alarm_report_fileurl'],
							"attachment_url" => $attachmentlink,
							"level" => $level
						);

						if (!isset($data_fix)) {
							$data_fix = array();
							array_push($data_fix, $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
							$total_data++;
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_fix, $d);
									$total_data++;
								}
							} else {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_fix, $d);
									$total_data++;
								}
							}
						}
					}
				} else {
					$datetime = $data[$i]['alarm_report_start_time'];
					//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
					$vehiclen = $data[$i]['alarm_report_vehicle_no'];
					$master_video[$vehiclen][$datetime] = $data[$i]['alarm_report_id'];
				}
			}
		}
		if ($numrows2 > 0) {
			for ($i = 0; $i < $numrows2; $i++) {
				if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
					$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];
					$cmpny_nm = $data_company[$company];
					// $datetime = $data[$i]['overspeed_report_gps_time'];
					$datetime = $data2[$i]['overspeed_report_gps_time'];
					// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));

					$vltion = "Overspeed";
					$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
					$locationn = $data2[$i]['overspeed_report_location'];
					$exp = explode(" ", $datetime);
					$h = explode(":", $exp[1]);

					$h_shift = date('H', strtotime($exp[1]));
					$shift_nm = $this->getshift_byhour($h_shift);

					$d = array(
						"company" => $cmpny_nm,
						"shift" => $shift_nm,
						"speed" => $data2[$i]['overspeed_report_speed'],
						"jalur" => $data2[$i]['overspeed_report_jalur'],
						"vehicle" => $vehiclen,
						"location" => $locationn,
						"violation" => $vltion,
						"date" => date("d-m-Y", strtotime($exp[0])),
						"time" => $exp[1],
						"coordinate" => $data2[$i]['overspeed_report_coordinate'],
						"info" => "Speed: " . $data2[$i]['overspeed_report_speed'] . " Kph, Limit: " . $data2[$i]['overspeed_report_geofence_limit'] . "Kph, Jalur: " . $data2[$i]['overspeed_report_jalur'],
						"hour" => $h[0],
						"file_url" => "#",
						"attachment_url" => "#",
						"level" => $data2[$i]['overspeed_report_level']
					);

					if (!isset($data_fix)) {
						$data_fix = array();
						array_push($data_fix, $d);
						$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
						$total_data++;
					} else {
						if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
							$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
							array_push($data_fix, $d);
							$total_data++;
						}
					}
				}
			}
		}
		if ($total_data == 0) {
			$callback['error'] = true;
			$callback['message'] = "Data Empty!";
			echo json_encode($callback);
			return;
		}

		//print_r($data_fix);exit();

		$this->params['total_data'] = $total_data;
		$this->params['data'] = $data_fix;
		$this->params['sdate'] = $start_date;
		$this->params['edate'] = $end_date;
		$this->params['month'] = $monthprm;
		$this->params['year'] = $year;
		$this->params['company'] = $company_name;
		$this->params['db_table'] = $dbtable;
		$html                    = $this->load->view('newdashboard/hse/v_daily_violation_infodetail', $this->params, true);
		$callback['error'] = false;
		$callback["html"]        = $html;
		echo json_encode($callback);
		return;
	}

	function infodetail2()
	{
		$sdate    = $this->input->post("start_date");
		$edate    = $this->input->post("end_date");
		$company = $this->input->post("company");
		$company_name = $this->input->post("company_name");
		$violation = $this->input->post("violation");
		$start_date = date("d-m-Y", strtotime($sdate));
		$end_date = date("d-m-Y", strtotime($edate));
		$userid = 4408;

		$report     = "alarm_evidence_";
		$overspeed  = "overspeed_";
		$month = date("F", strtotime($sdate));
		$monthprm = date("m", strtotime($sdate));
		$year = date("Y", strtotime($sdate));
		switch ($month) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dboverspeed = $overspeed . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dboverspeed = $overspeed . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dboverspeed = $overspeed . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dboverspeed = $overspeed . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dboverspeed = $overspeed . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dboverspeed = $overspeed . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dboverspeed = $overspeed . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dboverspeed = $overspeed . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dboverspeed = $overspeed . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dboverspeed = $overspeed . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dboverspeed = $overspeed . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dboverspeed = $overspeed . "desember_" . $year;
				break;
		}

		//violation data

		$rviolation = $this->getViolation(); //ambil master data violation alrmmaster
		$dataviolation = array(); //untuk simpan data violation
		$master_violation = array(); //untuk simpan data violation format 2
		$allviolation = array(); //untuk simpan id violation
		$nr = count($rviolation);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$dataviolation[$rviolation[$i]["alarmmaster_id"]] = $rviolation[$i]["alarmmaster_name"];
				$master_violation[$i] = $rviolation[$i]["alarmmaster_name"];
				array_push($allviolation, $rviolation[$i]["alarmmaster_id"]);
			}
		}

		$dataalarmtype = array(); //untuk query where_in
		$dataviolationalarmtype = array(); //untuk ditampilkan di view
		if ($violation != "all") {
			$alarmtype = $this->getAlarmtype($violation);
		} else {
			$alarmtype = $this->getAlarmtype(null, $allviolation);
		}
		$nr = count($alarmtype);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				if (!isset($dataviolationalarmtype[$alarmtype[$i]["alarm_type"]])) {
					if ($violation == "all") {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$alarmtype[$i]["alarm_master_id"]];
					} else {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$violation];
					}
				}
				array_push($dataalarmtype, $alarmtype[$i]["alarm_type"]);
			}
		}

		//company data
		$data_company = array(); //format 1
		$opposite_company = array(); //kebalikan format 1
		$master_company = array(); //format 2
		$company = $company;
		$data_company[$company] = $company_name;
		$master_company[0] = $company_name;
		$company = $company;
		$opposite_company[$company_name] = $company;




		$data = array();
		$data2 = array();
		if ($violation == "6") {
			$data2 = $this->getOverspeed2($dboverspeed, $company, "all", $sdate, $edate, $filter_true_false);
		} else {
			if ($violation == "all") {
				$data2 = $this->getOverspeed2($dboverspeed, $company, "all", $sdate, $edate, $filter_true_false);
				$data = $this->getSecurityEvidence($dbtable, $company, "all", $violation, $dataalarmtype, $sdate, $edate, $filter_true_false);
			} else {
				$data = $this->getSecurityEvidence($dbtable, $company, "all", $violation, $dataalarmtype, $sdate, $edate, $filter_true_false);
			}
		}

		$numrows = count($data);
		// $numrows = 0;
		$numrows2 = count($data2);
		// $numrows2 = 0;
		$total_data = 0;
		$seleksi_data = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_overspeed = array(); //untuk seleksi multiple data yang sama
		$master_video = array();
		if ($numrows > 0) {
			for ($i = 0; $i < $numrows; $i++) {
				if ($data[$i]['alarm_report_media'] == 0) {
					if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
						$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];
						$cmpny_nm = $data_company[$company];
						$datetime = $data[$i]['alarm_report_start_time'];
						//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
						$vehiclen = $data[$i]['alarm_report_vehicle_no'];
						//$loction = $data[$i]['alarm_report_location_start']; //no filter location
						$loction = $data[$i]['alarm_report_id'];
						$gpsstts = $data[$i]['alarm_report_gpsstatus'];
						if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
							$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
						} else {
							$vltion = $data[$i]['alarm_report_type'];
						}

						$exp = explode(" ", $datetime);
						$h = explode(":", $exp[1]);

						$h_shift = date('H', strtotime($exp[1]));
						$shift_nm = $this->getshift_byhour($h_shift);
						$reportdetaildecode = explode("|", $gpsstts);
						$speedgps = number_format($reportdetaildecode[4]/10, 0, '.', '');


						//prepare attachment
						$monthforparam = $monthprm;
						$user_id_role = 2;

						$imagealertid = $data[$i]['alarm_report_id'];
						$sdate_alert = $data[$i]['alarm_report_start_time'];
						$alertvehicleid = $data[$i]['alarm_report_vehicle_id'];

						$reportdetailvideo = $this->getvideo_alarmevidence($dbtable, $alertvehicleid, $sdate_alert);



						if(count($reportdetailvideo) > 0){
							$videoalertid = $reportdetailvideo[0]['alarm_report_id'];
							$attachmentlink = "http://attachment.pilartech.co.id/attachment/".$videoalertid.'/'.$imagealertid.'/'.$monthforparam.'/'.$year.'/'.$user_id_role.'/'.$userid;

						}else{
							$videoalertid = 0;
							$attachmentlink = "#";
						}


						//print_r($attachmentlink);exit();

						$info = $data[$i]['alarm_report_name'];
						$expinfo = explode("Level ", $info);
						$exinfo = explode(" ", $expinfo[1]);
						if ($exinfo[0] == "One") {
							$level = 1;
						} else if ($exinfo[0] == "Two") {
							$level = 2;
						} else if ($exinfo[0] == "Three") {
							$level = 3;
						} else if ($exinfo[0] == "Four") {
							$level = 4;
						} else {
							$level = " ";
						}

						$d = array(
							"company" => $cmpny_nm,
							"shift" => $shift_nm,
							"speed" => $speedgps,
							"jalur" => "",
							"vehicle" => $vehiclen,
							"location" => $loction,
							"violation" => $vltion,
							"date" => date("d-m-Y", strtotime($exp[0])),
							"time" => $exp[1],
							"coordinate" => $data[$i]['alarm_report_coordinate_start'],
							"info" => $info,
							"hour" => $h[0],
							"file_url" => $data[$i]['alarm_report_fileurl'],
							"attachment_url" => $attachmentlink,
							"level" => $level
						);

						if (!isset($data_fix)) {
							$data_fix = array();
							array_push($data_fix, $d);
							if ($vltion == "Driver Abnormal") {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
							} else {
								$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
							}
							$total_data++;
						} else {
							if ($vltion == "Driver Abnormal") {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]] = 1;
									array_push($data_fix, $d);
									$total_data++;
								}
							} else {
								if (!isset($seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction])) {
									$seleksi_data[$vltion][$vehiclen][$exp[0]][$h[0]][$loction] = 1;
									array_push($data_fix, $d);
									$total_data++;
								}
							}
						}
					}
				} else {
					$datetime = $data[$i]['alarm_report_start_time'];
					//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
					$vehiclen = $data[$i]['alarm_report_vehicle_no'];
					$master_video[$vehiclen][$datetime] = $data[$i]['alarm_report_id'];
				}
			}
		}
		if ($numrows2 > 0) {
			for ($i = 0; $i < $numrows2; $i++) {
				if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
					$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];
					$cmpny_nm = $data_company[$company];
					// $datetime = $data[$i]['overspeed_report_gps_time'];
					$datetime = $data2[$i]['overspeed_report_gps_time'];
					// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));

					$vltion = "Overspeed";
					$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
					//$locationn = $data2[$i]['overspeed_report_location']; //no filter location
					$locationn = $data2[$i]['overspeed_report_id'];
					$exp = explode(" ", $datetime);
					$h = explode(":", $exp[1]);

					$h_shift = date('H', strtotime($exp[1]));
					$shift_nm = $this->getshift_byhour($h_shift);

					$d = array(
						"company" => $cmpny_nm,
						"shift" => $shift_nm,
						"speed" => $data2[$i]['overspeed_report_speed'],
						"jalur" => $data2[$i]['overspeed_report_jalur'],
						"vehicle" => $vehiclen,
						"location" => $locationn,
						"violation" => $vltion,
						"date" => date("d-m-Y", strtotime($exp[0])),
						"time" => $exp[1],
						"coordinate" => $data2[$i]['overspeed_report_coordinate'],
						"info" => "Speed: " . $data2[$i]['overspeed_report_speed'] . " Kph, Limit: " . $data2[$i]['overspeed_report_geofence_limit'] . "Kph, Jalur: " . $data2[$i]['overspeed_report_jalur'],
						"hour" => $h[0],
						"file_url" => "#",
						"attachment_url" => "#",
						"level" => $data2[$i]['overspeed_report_level']
					);

					if (!isset($data_fix)) {
						$data_fix = array();
						array_push($data_fix, $d);
						$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
						$total_data++;
					} else {
						if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn])) {
							$seleksi_data_overspeed[$vltion][$vehiclen][$exp[0]][$h[0]][$locationn] = 1;
							array_push($data_fix, $d);
							$total_data++;
						}
					}
				}
			}
		}
		
		if ($total_data == 0) {
			$callback['error'] = true;
			$callback['message'] = "Data Empty!";
			echo json_encode($callback);
			return;
		}

		//print_r($data_fix);exit();

		$this->params['total_data'] = $total_data;
		$this->params['data'] = $data_fix;
		$this->params['sdate'] = $start_date;
		$this->params['edate'] = $end_date;
		$this->params['month'] = $monthprm;
		$this->params['year'] = $year;
		$this->params['company'] = $company_name;
		$this->params['db_table'] = $dbtable;
		$html                    = $this->load->view('newdashboard/hse/v_daily_violation2_infodetail', $this->params, true);
		$callback['error'] = false;
		$callback["html"]        = $html;
		echo json_encode($callback);
		return;
	}

	function view_video($table, $vehicle, $date, $time)
	{
		//$datetime = date("Y-m-d H:i:s", strtotime($date . " " . $time) - (60 * 60));
		$datetime = date("Y-m-d H:i:s", strtotime($date . " " . $time));
		$this->dbalarm = $this->load->database("tensor_report", true);
		// $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
		$this->dbalarm->select("alarm_report_fileurl, alarm_report_id");
		$this->dbalarm->where("alarm_report_vehicle_no", $vehicle);
		$this->dbalarm->where("alarm_report_start_time", $datetime);
		$this->dbalarm->where("alarm_report_media", 1);
		$this->dbalarm->group_by("alarm_report_start_time");
		$q             = $this->dbalarm->get($table);
		if ($q->num_rows() > 0) {
			$data = $q->result_array();
			redirect($data[0]['alarm_report_fileurl']);
		} else {
			echo '<h1>No Video</h1>';
		}
		exit();
	}

	function getvideo_alarmevidence($table, $alertid, $sdate){
      $this->dbalarm = $this->load->database("webtracking_kalimantan", true);
      $this->dbalarm->select("alarm_report_downloadurl,alarm_report_id");
      $this->dbalarm->where("alarm_report_vehicle_id", $alertid);
      $this->dbalarm->where("alarm_report_start_time", $sdate);
      $this->dbalarm->where("alarm_report_media", 1);
      $this->dbalarm->group_by("alarm_report_start_time");
      $q             = $this->dbalarm->get($table);
	  $this->dbalarm->close();
	  $this->dbalarm->cache_delete_all();
      return  $q->result_array();
    }

	function getDTOperational($company, $sdate, $edate)
	{
		$data_fix = array();

		//SOURCE TRUCK ON DUTY
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_vehicle_company,location_report_gps_date,location_report_gps_hour");
		if ($company != "all") {
			$this->dbts->where("location_report_vehicle_company", $company);
		}
		$this->dbts->where_in("location_report_group", array("STREET", "ROM", "PORT"));
		$this->dbts->where("location_report_gps_date >=", $sdate);
		$this->dbts->where("location_report_gps_date <=", $edate);
		$this->dbts->order_by("location_report_gps_date", "asc");
		// $this->dbts->group_by("location_report_gps_date");
		$this->dbts->order_by("location_report_gps_hour", "asc");
		$this->dbts->order_by("location_report_company_name", "asc");
		$this->dbts->order_by("location_report_vehicle_no", "asc");
		// $this->dbts->group_by("location_report_vehicle_no");
		$result = $this->dbts->get("ts_location_hour");
		$data = $result->result_array();
		$nr = $result->num_rows();

		// echo $nr;
		// exit();

		$units = array();
		$hourly_units = array();
		$seleksi_data = array();
		$seleksi_unit = array();
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$hour = $data[$i]['location_report_gps_hour'];
				$exp = explode(":", $hour);
				$cmpny = $data[$i]['location_report_company_name'];
				$vhicle = $data[$i]['location_report_vehicle_no'];
				$date = $data[$i]['location_report_gps_date'];
				if (!isset($units[$cmpny])) {
					// $units[$cmpny][$vhicle] = 1;
					$units[$cmpny] = 1;
					$seleksi_unit[$date][$cmpny][$vhicle] = 1;
				} else {
					if (!isset($seleksi_unit[$date][$cmpny][$vhicle])) {
						$seleksi_unit[$date][$cmpny][$vhicle] = 1;
						$units[$cmpny] += 1;
					}
				}
				if (!isset($hourly_units[$exp[0]][$cmpny])) {
					$hourly_units[$exp[0]][$cmpny] = 1;
					$seleksi_data[$exp[0]][$cmpny][$vhicle] = 1;
				} else {
					if (!isset($seleksi_data[$exp[0]][$cmpny][$vhicle])) {
						$hourly_units[$exp[0]][$cmpny] += 1;
						$seleksi_data[$exp[0]][$cmpny][$vhicle] = 1;
					}
				}
			}
			$data_fix['units'] = $units;
			$data_fix['hourly_units'] = $hourly_units;
		}

		// var_dump($units);
		// exit();
		return	$data_fix;
	}

	function search_dev_backup5()
	{
		ini_set('memory_limit', "1G");
		$datein    = $this->input->post("date");
		$company = $this->input->post("company");
		$violation = $this->input->post("violation");
		$vehicle = $this->input->post("vehicle");
		$view = $this->input->post("view");
		$date = date("Y-m-d", strtotime($datein));
		$month = date("F", strtotime($datein));
		$monthforparam = date("m", strtotime($datein));
		$year = date("Y", strtotime($datein));
		$report     = "alarm_evidence_";
		$overspeed  = "overspeed_";

		switch ($month) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dboverspeed = $overspeed . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dboverspeed = $overspeed . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dboverspeed = $overspeed . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dboverspeed = $overspeed . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dboverspeed = $overspeed . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dboverspeed = $overspeed . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dboverspeed = $overspeed . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dboverspeed = $overspeed . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dboverspeed = $overspeed . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dboverspeed = $overspeed . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dboverspeed = $overspeed . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dboverspeed = $overspeed . "desember_" . $year;
				break;
		}

		//company data
		$companydata = $this->getcompany_bycreator(4408);
		$data_company = array(); //format 1
		$master_company = array(); //format 2
		if ($company != "all") {
			$exp = explode("@", $company);
			$count_company = 1;
			$company = $exp[0];
			$data_company[$exp[0]] = $exp[1];
			$master_company[0] = $exp[1];
		} else {
			$count_company = count($companydata);
			for ($i = 0; $i < $count_company; $i++) {
				$data_company[$companydata[$i]->company_id] = $companydata[$i]->company_name;
				$master_company[$i] = $companydata[$i]->company_name;
			}
		}

		//violation data

		$rviolation = $this->getViolation(); //ambil master data violation alrmmaster
		$dataviolation = array(); //untuk simpan data violation
		$master_violation = array(); //untuk simpan data violation format 2
		$allviolation = array(); //untuk simpan id violation
		$nr = count($rviolation);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$dataviolation[$rviolation[$i]["alarmmaster_id"]] = $rviolation[$i]["alarmmaster_name"];
				$master_violation[$i] = $rviolation[$i]["alarmmaster_name"];
				array_push($allviolation, $rviolation[$i]["alarmmaster_id"]);
			}
		}
		$dataalarmtype = array(); //untuk query where_in
		$dataviolationalarmtype = array(); //untuk ditampilkan di view
		if ($violation != "all") {
			$alarmtype = $this->getAlarmtype($violation);
		} else {
			$alarmtype = $this->getAlarmtype(null, $allviolation);
		}
		$nr = count($alarmtype);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				if (!isset($dataviolationalarmtype[$alarmtype[$i]["alarm_type"]])) {
					if ($violation == "all") {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$alarmtype[$i]["alarm_master_id"]];
					} else {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$violation];
					}
				}
				array_push($dataalarmtype, $alarmtype[$i]["alarm_type"]);
			}
		}
		$data = $this->getSecurityEvidence($dbtable, $company, $vehicle, $violation, $dataalarmtype, $date, $filter_true_false);
		$data2 = $this->getOverspeed($dboverspeed, $company, $vehicle, $date);
		$numrows = count($data);
		// $numrows = 0;
		$numrows2 = count($data2);
		// $numrows2 = 0;
		$total_data = 0;
		$input = array(
			"violation" => $violation,
			"company" => $company,
			"vehicle" => $vehicle,
			"date" => $date,
			"view" => $view
		);
		$data_fix = array();
		$data_table = array();
		$dhour = array();
		$dcompany = array();
		$dviolation = array();
		$c = array();
		$l = array();
		$seleksi_data = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_overspeed = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table_overspeed = array(); //untuk seleksi multiple data yang sama
		if ($numrows > 0) {
			for ($i = 0; $i < $numrows; $i++) {
				$datetime = $data[$i]['alarm_report_start_time'];
				//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
				$vehiclen = $data[$i]['alarm_report_vehicle_no'];
				$loction = $data[$i]['alarm_report_location_start'];
				if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
					$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];
				} else {
					$cmpny = $data[$i]['alarm_report_vehicle_company'];
				}
				if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
					$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
				} else {
					$vltion = $data[$i]['alarm_report_type'];
				}
				$exp = explode(" ", $datetime);
				$h = explode(":", $exp[1]);

				if (!isset($hour[$h[0]])) {
					$hour[$h[0]] = 1;
					$dhour[] = $h[0];
				}
				if (!isset($c[$cmpny])) {
					$c[$cmpny] = $cmpny;
					$dcompany[] = $cmpny;
				}
				if (!isset($l[$vltion])) {

					$l[$vltion] = $vltion;
					$dviolation[] = $vltion;
				}
				// $data_fix[$exp[0]][$data[$i]['location_report_company_name']][$data[$i]['location_report_location']] = array("vehicle" => $data[$i]['location_report_vehicle_no']);

				$d = array(
					"company" => $cmpny,
					"vehicle" => $vehiclen,
					"location" => $loction,
					"violation" => $vltion,
					"time" => $exp[1],
					"coordinate" => $data[$i]['alarm_report_coordinate_start'],
					"info" => $data[$i]['alarm_report_name'],
					"hour" => $h[0],
					"file_url" => $data[$i]['alarm_report_fileurl']
				);
				if ($view == 1) {
					if (!isset($data_fix[$h[0]][$cmpny])) {
						$data_fix[$h[0]][$cmpny] = array();
						array_push($data_fix[$h[0]][$cmpny], $d);
						if ($vltion == "Driver Abnormal") {
							$seleksi_data[$vltion][$vehiclen][$h[0]] = 1;
						} else {
							$seleksi_data[$vltion][$vehiclen][$h[0]][$loction] = 1;
						}
						$total_data++;
					} else {
						if ($vltion == "Driver Abnormal") {
							if (!isset($seleksi_data[$vltion][$vehiclen][$h[0]])) {
								$seleksi_data[$vltion][$vehiclen][$h[0]] = 1;
								array_push($data_fix[$h[0]][$cmpny], $d);
								$total_data++;
							}
						} else {
							if (!isset($seleksi_data[$vltion][$vehiclen][$h[0]][$loction])) {
								$seleksi_data[$vltion][$vehiclen][$h[0]][$loction] = 1;
								array_push($data_fix[$h[0]][$cmpny], $d);
								$total_data++;
							}
						}
					}
				} else {
					if (!isset($data_table[$vltion][$cmpny])) {
						$data_table[$vltion][$cmpny] = array();
						array_push($data_table[$vltion][$cmpny], $d);
						if ($vltion == "Driver Abnormal") {
							$seleksi_data_table[$vltion][$vehiclen][$h[0]] = 1;
						} else {

							$seleksi_data_table[$vltion][$vehiclen][$h[0]][$loction] = 1;
						}
						$total_data++;
					} else {
						if ($vltion == "Driver Abnormal") {
							if (!isset($seleksi_data_table[$vltion][$vehiclen][$h[0]])) {
								$seleksi_data_table[$vltion][$vehiclen][$h[0]] = 1;
								array_push($data_table[$vltion][$cmpny], $d);
								$total_data++;
							}
						} else {
							if (!isset($seleksi_data_table[$vltion][$vehiclen][$h[0]][$loction])) {
								$seleksi_data_table[$vltion][$vehiclen][$h[0]][$loction] = 1;
								array_push($data_table[$vltion][$cmpny], $d);
								$total_data++;
							}
						}
					}
				}
			}
		}
		if ($numrows2 > 0) {
			for ($i = 0; $i < $numrows2; $i++) {
				// $datetime = $data[$i]['overspeed_report_gps_time'];
				$datetime = $data2[$i]['overspeed_report_gps_time'];
				// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));
				if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
					$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];
				} else {
					$cmpny = $data2[$i]['overspeed_report_vehicle_company'];
				}
				$vltion = "Overspeed";
				$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
				$locationn = $data2[$i]['overspeed_report_location'];
				$exp = explode(" ", $datetime);
				$h = explode(":", $exp[1]);

				if (!isset($hour[$h[0]])) {
					$hour[$h[0]] = 1;
					$dhour[] = $h[0];
				}
				if (!isset($c[$cmpny])) {
					$c[$cmpny] = $cmpny;
					$dcompany[] = $cmpny;
				}
				if (!isset($l[$vltion])) {
					$l[$vltion] = $vltion;
					$dviolation[] = $vltion;
				}
				$d = array(
					"company" => $cmpny,
					"vehicle" => $vehiclen,
					"location" => $locationn,
					"violation" => $vltion,
					"time" => $exp[1],
					"coordinate" => $data2[$i]['overspeed_report_coordinate'],
					"info" => "Speed: " . $data2[$i]['overspeed_report_speed'] . " Kph, Limit: " . $data2[$i]['overspeed_report_geofence_limit'] . "Kph, alarm level: " . $data2[$i]['overspeed_report_level'] . ", Jalur: " . $data2[$i]['overspeed_report_jalur'],
					"hour" => $h[0],
					"file_url" => "#"
				);
				if ($view == 1) {
					if (!isset($data_fix[$h[0]][$cmpny])) {
						$data_fix[$h[0]][$cmpny] = array();
						array_push($data_fix[$h[0]][$cmpny], $d);
						$seleksi_data_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
						$total_data++;
					} else {
						if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$h[0]][$locationn])) {
							$seleksi_data_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
							array_push($data_fix[$h[0]][$cmpny], $d);
							$total_data++;
						}
					}
				} else {
					if (!isset($data_table[$vltion][$cmpny])) {
						$data_table[$vltion][$cmpny] = array();
						array_push($data_table[$vltion][$cmpny], $d);
						$seleksi_data_table_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
						$total_data++;
					} else {
						if (!isset($seleksi_data_table_overspeed[$vltion][$vehiclen][$h[0]][$locationn])) {
							$seleksi_data_table_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
							array_push($data_table[$vltion][$cmpny], $d);
							$total_data++;
						}
					}
				}
			}
		}
		if ($total_data == 0) {
			$callback['error'] = true;
			$callback['message'] = "Data Empty!";
			echo json_encode($callback);
			return;
		}
		$this->params['input'] = $input;
		$this->params['total_data'] = $total_data;
		$this->params['date'] = $datein;
		$this->params['data_hour'] = $dhour; //data hour dari source
		$this->params['data_company'] = $dcompany; //data company dari source
		$this->params['data_alarm'] = $dviolation; //data violation dari source
		if ($view == 1) {
			$this->params['data_fix'] = $data_fix;
		} else {
			$this->params['data_table'] = $data_table;
			$this->params['master_company'] = $master_company; //master company format 2
			$this->params['master_violation'] = $master_violation; //master company format 2
		}
		// $this->params['master_company'] = $data_company; //master company format 1
		// $this->params['master_alarm'] = $dataviolationalarmtype;

		$html                    = $this->load->view('newdashboard/hse/v_dashboard_hse_result_dev_backup5', $this->params, true);

		$callback["html"]        = $html;

		echo json_encode($callback);
	}

	function search_dev_backup1()
	{
		$datein    = $this->input->post("date");
		$company = $this->input->post("company");
		$violation = $this->input->post("violation");
		$vehicle = $this->input->post("vehicle");
		$date = date("Y-m-d", strtotime($datein));
		$month = date("F", strtotime($datein));
		$monthforparam = date("m", strtotime($datein));
		$year = date("Y", strtotime($datein));
		$report     = "alarm_evidence_";

		switch ($month) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				break;
		}

		//company data
		$companydata = $this->getcompany_bycreator(4408);
		$data_company = array(); //format 1
		$master_company = array(); //format 2
		if ($company != "all") {
			$exp = explode("@", $company);
			$count_company = 1;
			$company = $exp[0];
			$data_company[$exp[0]] = $exp[1];
			$master_company[0] = $exp[1];
		} else {
			$count_company = count($companydata);
			for ($i = 0; $i < $count_company; $i++) {
				$data_company[$companydata[$i]->company_id] = $companydata[$i]->company_name;
				$master_company[$i] = $companydata[$i]->company_name;
			}
		}

		//violation data

		$rviolation = $this->getViolation(); //ambil master data violation alrmmaster
		$dataviolation = array(); //untuk simpan data violation
		$master_violation = array(); //untuk simpan data violation format 2
		$allviolation = array(); //untuk simpan id violation
		$nr = count($rviolation);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$dataviolation[$rviolation[$i]["alarmmaster_id"]] = $rviolation[$i]["alarmmaster_name"];
				$master_violation[$i] = $rviolation[$i]["alarmmaster_name"];
				array_push($allviolation, $rviolation[$i]["alarmmaster_id"]);
			}
		}
		$dataalarmtype = array(); //untuk query where_in
		$dataviolationalarmtype = array(); //untuk ditampilkan di view
		if ($violation != "all") {
			$alarmtype = $this->getAlarmtype($violation);
		} else {
			$alarmtype = $this->getAlarmtype(null, $allviolation);
		}
		$nr = count($alarmtype);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				if (!isset($dataviolationalarmtype[$alarmtype[$i]["alarm_type"]])) {
					if ($violation == "all") {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$alarmtype[$i]["alarm_master_id"]];
					} else {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$violation];
					}
				}
				array_push($dataalarmtype, $alarmtype[$i]["alarm_type"]);
			}
		}
		$data = $this->getSecurityEvidence($dbtable, $company, $vehicle, $violation, $dataalarmtype, $date, $filter_true_false);
		$numrows = count($data);
		$callback['input'] = array(
			"violation" => $violation,
			"company" => $company,
			"vehicle" => $vehicle,
			"date" => $date,
		);

		if ($numrows > 0) {
			$callback['error'] = false;
			$callback['message'] = "OK";
			$callback["jumlah_data"] = $numrows;
			$data_fix = array();
			$data_table = array();
			$dhour = array();
			$dcompany = array();
			$dviolation = array();
			$c = array();
			$l = array();
			for ($i = 0; $i < $numrows; $i++) {
				$datetime = $data[$i]['alarm_report_start_time'];
				//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
				if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
					$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];
				} else {
					$cmpny = $data[$i]['alarm_report_vehicle_company'];
				}
				if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
					$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
				} else {
					$vltion = $data[$i]['alarm_report_type'];
				}
				$exp = explode(" ", $datetime);
				$h = explode(":", $exp[1]);

				if (!isset($hour[$h[0]])) {
					$hour[$h[0]] = 1;
					$dhour[] = $h[0];
				}
				if (!isset($c[$cmpny])) {
					$c[$cmpny] = $cmpny;
					$dcompany[] = $cmpny;
				}
				if (!isset($l[$vltion])) {

					$l[$vltion] = $vltion;
					$dviolation[] = $vltion;
				}
				// $data_fix[$exp[0]][$data[$i]['location_report_company_name']][$data[$i]['location_report_location']] = array("vehicle" => $data[$i]['location_report_vehicle_no']);

				$d = array(
					"company" => $cmpny,
					"vehicle" => $data[$i]['alarm_report_vehicle_no'],
					"location" => $data[$i]['alarm_report_location_start'],
					"violation" => $vltion,
					"time" => $exp[1],
					"coordinate" => $data[$i]['alarm_report_coordinate_start'],
					"info" => $data[$i]['alarm_report_name'],
					"hour" => $h[0],
					"file_url" => $data[$i]['alarm_report_fileurl']
				);
				if (!isset($data_fix[$h[0]][$cmpny])) {
					$data_fix[$h[0]][$cmpny] = array();

					array_push($data_fix[$h[0]][$cmpny], $d);
				} else {
					array_push($data_fix[$h[0]][$cmpny], $d);
				}
				if (!isset($data_table[$vltion][$cmpny])) {
					$data_table[$vltion][$cmpny] = array();
					array_push($data_table[$vltion][$cmpny], $d);
				} else {
					array_push($data_table[$vltion][$cmpny], $d);
				}
				// if (!isset($unit_location[$data[$i]['location_report_vehicle_no']])) {
				//     $unit_location[$data[$i]['location_report_vehicle_no']] = $data[$i]['location_report_vehicle_no'];
				// }
				$data[$i]['hour'] = $h[0];
			}
		} else {
			$callback['error'] = true;
			$callback['message'] = "Data Empty!";
			echo json_encode($callback);
			return;
		}
		$this->params['data_table'] = $data_table;
		$this->params['master_company'] = $data_company; //master company format 1
		$this->params['master_company2'] = $master_company; //master company format 2
		$this->params['master_violation'] = $master_violation; //master company format 2
		$this->params['master_alarm'] = $dataviolationalarmtype;

		$this->params['data_fix'] = $data_fix;
		$this->params['data_hour'] = $dhour; //data hour dari source
		$this->params['data_alarm'] = $dviolation; //data violation dari source
		$this->params['data_company'] = $dcompany; //data company dari source
		$this->params['input'] = $callback['input'];
		$this->params['total_data'] = $numrows;
		$this->params['date'] = $datein;
		$html                    = $this->load->view('newdashboard/hse/v_dashboard_hse_result_dev', $this->params, true);

		$callback["html"]        = $html;

		echo json_encode($callback);
	}

	function search_dev_backup2()
	{
		ini_set('memory_limit', "1G");
		// ini_set('display_errors', 1);
		$datein    = $this->input->post("date");
		$company = $this->input->post("company");
		$violation = $this->input->post("violation");
		$vehicle = $this->input->post("vehicle");
		$date = date("Y-m-d", strtotime($datein));
		$month = date("F", strtotime($datein));
		$monthforparam = date("m", strtotime($datein));
		$year = date("Y", strtotime($datein));
		$report     = "alarm_evidence_";
		$overspeed  = "overspeed_";

		switch ($month) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dboverspeed = $overspeed . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dboverspeed = $overspeed . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dboverspeed = $overspeed . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dboverspeed = $overspeed . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dboverspeed = $overspeed . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dboverspeed = $overspeed . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dboverspeed = $overspeed . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dboverspeed = $overspeed . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dboverspeed = $overspeed . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dboverspeed = $overspeed . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dboverspeed = $overspeed . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dboverspeed = $overspeed . "desember_" . $year;
				break;
		}

		//company data
		$companydata = $this->getcompany_bycreator(4408);
		$data_company = array(); //format 1
		$master_company = array(); //format 2
		if ($company != "all") {
			$exp = explode("@", $company);
			$count_company = 1;
			$company = $exp[0];
			$data_company[$exp[0]] = $exp[1];
			$master_company[0] = $exp[1];
		} else {
			$count_company = count($companydata);
			for ($i = 0; $i < $count_company; $i++) {
				$data_company[$companydata[$i]->company_id] = $companydata[$i]->company_name;
				$master_company[$i] = $companydata[$i]->company_name;
			}
		}

		//violation data

		$rviolation = $this->getViolation(); //ambil master data violation alrmmaster
		$dataviolation = array(); //untuk simpan data violation
		$master_violation = array(); //untuk simpan data violation format 2
		$allviolation = array(); //untuk simpan id violation
		$nr = count($rviolation);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$dataviolation[$rviolation[$i]["alarmmaster_id"]] = $rviolation[$i]["alarmmaster_name"];
				$master_violation[$i] = $rviolation[$i]["alarmmaster_name"];
				array_push($allviolation, $rviolation[$i]["alarmmaster_id"]);
			}
		}
		$dataalarmtype = array(); //untuk query where_in
		$dataviolationalarmtype = array(); //untuk ditampilkan di view
		if ($violation != "all") {
			$alarmtype = $this->getAlarmtype($violation);
		} else {
			$alarmtype = $this->getAlarmtype(null, $allviolation);
		}
		$nr = count($alarmtype);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				if (!isset($dataviolationalarmtype[$alarmtype[$i]["alarm_type"]])) {
					if ($violation == "all") {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$alarmtype[$i]["alarm_master_id"]];
					} else {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$violation];
					}
				}
				array_push($dataalarmtype, $alarmtype[$i]["alarm_type"]);
			}
		}
		$data = $this->getSecurityEvidence($dbtable, $company, $vehicle, $violation, $dataalarmtype, $date, $filter_true_false);
		$data2 = $this->getOverspeed($dboverspeed, $company, $vehicle, $date);
		$numrows = count($data);
		$numrows2 = count($data2);
		$total_data = $numrows + $numrows2;
		// $callback["data"] = $data;
		// $callback["data2"] = $data2;
		// $callback['input'] = array(
		// 	"violation" => $violation,
		// 	"company" => $company,
		// 	"vehicle" => $vehicle,
		// 	"date" => $date,
		// );
		// $callback['error'] = false;
		$data_fix = array();
		$data_table = array();
		$dhour = array();
		$dcompany = array();
		$dviolation = array();
		$c = array();
		$l = array();
		if ($numrows > 0) {
			for ($i = 0; $i < $numrows; $i++) {
				$datetime = $data[$i]['alarm_report_start_time'];
				//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
				if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
					$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];
				} else {
					$cmpny = $data[$i]['alarm_report_vehicle_company'];
				}
				if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
					$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
				} else {
					$vltion = $data[$i]['alarm_report_type'];
				}
				$exp = explode(" ", $datetime);
				$h = explode(":", $exp[1]);

				if (!isset($hour[$h[0]])) {
					$hour[$h[0]] = 1;
					$dhour[] = $h[0];
				}
				if (!isset($c[$cmpny])) {
					$c[$cmpny] = $cmpny;
					$dcompany[] = $cmpny;
				}
				if (!isset($l[$vltion])) {

					$l[$vltion] = $vltion;
					$dviolation[] = $vltion;
				}
				// $data_fix[$exp[0]][$data[$i]['location_report_company_name']][$data[$i]['location_report_location']] = array("vehicle" => $data[$i]['location_report_vehicle_no']);

				$d = array(
					"company" => $cmpny,
					"vehicle" => $data[$i]['alarm_report_vehicle_no'],
					"location" => $data[$i]['alarm_report_location_start'],
					"violation" => $vltion,
					"time" => $exp[1],
					"coordinate" => $data[$i]['alarm_report_coordinate_start'],
					"info" => $data[$i]['alarm_report_name'],
					"hour" => $h[0],
					"file_url" => $data[$i]['alarm_report_fileurl']
				);
				if (!isset($data_fix[$h[0]][$cmpny])) {
					$data_fix[$h[0]][$cmpny] = array();

					array_push($data_fix[$h[0]][$cmpny], $d);
				} else {
					array_push($data_fix[$h[0]][$cmpny], $d);
				}
				if (!isset($data_table[$vltion][$cmpny])) {
					$data_table[$vltion][$cmpny] = array();
					array_push($data_table[$vltion][$cmpny], $d);
				} else {
					array_push($data_table[$vltion][$cmpny], $d);
				}
				// if (!isset($unit_location[$data[$i]['location_report_vehicle_no']])) {
				//     $unit_location[$data[$i]['location_report_vehicle_no']] = $data[$i]['location_report_vehicle_no'];
				// }
				// $data[$i]['hour'] = $h[0];
			}
		}
		if ($numrows2 > 0) {
			for ($i = 0; $i < $numrows2; $i++) {
				$datetime = $data[$i]['overspeed_report_gps_time'];
				//$datetime = date("Y-m-d H:i:s", strtotime($data2[$i]['overspeed_report_gps_time']) + (60 * 60));
				if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
					$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];
				} else {
					$cmpny = $data2[$i]['overspeed_report_vehicle_company'];
				}
				$vltion = "Overspeed";
				$exp = explode(" ", $datetime);
				$h = explode(":", $exp[1]);

				if (!isset($hour[$h[0]])) {
					$hour[$h[0]] = 1;
					$dhour[] = $h[0];
				}
				if (!isset($c[$cmpny])) {
					$c[$cmpny] = $cmpny;
					$dcompany[] = $cmpny;
				}
				if (!isset($l[$vltion])) {
					$l[$vltion] = $vltion;
					$dviolation[] = $vltion;
				}
				$d = array(
					"company" => $cmpny,
					"vehicle" => $data2[$i]['overspeed_report_vehicle_no'],
					"location" => $data2[$i]['overspeed_report_location'],
					"violation" => $vltion,
					"time" => $exp[1],
					"coordinate" => $data2[$i]['overspeed_report_coordinate'],
					"info" => "Speed: " . $data2[$i]['overspeed_report_speed'] . "Kph, Limit: " . $data2[$i]['overspeed_report_geofence_limit'] . "Kph, Level: " . $data2[$i]['overspeed_report_level'] . " Jalur: " . $data2[$i]['overspeed_report_jalur'],
					"hour" => $h[0],
					"file_url" => "#"
				);
				if (!isset($data_fix[$h[0]][$cmpny])) {
					$data_fix[$h[0]][$cmpny] = array();

					array_push($data_fix[$h[0]][$cmpny], $d);
				} else {
					array_push($data_fix[$h[0]][$cmpny], $d);
				}
				if (!isset($data_table[$vltion][$cmpny])) {
					$data_table[$vltion][$cmpny] = array();
					array_push($data_table[$vltion][$cmpny], $d);
				} else {
					array_push($data_table[$vltion][$cmpny], $d);
				}
			}
		}
		if ($total_data == 0) {
			$callback['error'] = true;
			$callback['message'] = "Data Empty!";
			echo json_encode($callback);
			return;
		}
		// $callback['data_fix'] = $data_fix;
		// echo json_encode($callback);
		// return false;
		// $callback['data_fix'] = $data_fix;
		// echo json_encode($callback);
		// return;
		// $this->params['data_table'] = $data_table;
		$this->params['master_company'] = $data_company; //master company format 1
		$this->params['master_company2'] = $master_company; //master company format 2
		$this->params['master_violation'] = $master_violation; //master company format 2
		$this->params['master_alarm'] = $dataviolationalarmtype;

		$this->params['data_fix'] = $data_fix;
		$this->params['data_hour'] = $dhour; //data hour dari source
		$this->params['data_alarm'] = $dviolation; //data violation dari source
		$this->params['data_company'] = $dcompany; //data company dari source
		// $this->params['input'] = $callback['input'];
		$this->params['total_data'] = $total_data;
		$this->params['date'] = $datein;
		$html                    = $this->load->view('newdashboard/hse/v_dashboard_hse_result_dev', $this->params, true);

		$callback['error'] = false;
		$callback['message'] = "OK";
		$callback["html"]        = $html;

		echo json_encode($callback);
	}

	function search_dev_backup3()
	{
		ini_set('memory_limit', "1G");
		$datein    = $this->input->post("date");
		$company = $this->input->post("company");
		$violation = $this->input->post("violation");
		$vehicle = $this->input->post("vehicle");
		$filter_true_false = $this->input->post("filter_true_false");
		$date = date("Y-m-d", strtotime($datein));
		$month = date("F", strtotime($datein));
		$monthforparam = date("m", strtotime($datein));
		$year = date("Y", strtotime($datein));
		$report     = "alarm_evidence_";
		$overspeed  = "overspeed_";

		switch ($month) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dboverspeed = $overspeed . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dboverspeed = $overspeed . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dboverspeed = $overspeed . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dboverspeed = $overspeed . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dboverspeed = $overspeed . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dboverspeed = $overspeed . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dboverspeed = $overspeed . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dboverspeed = $overspeed . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dboverspeed = $overspeed . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dboverspeed = $overspeed . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dboverspeed = $overspeed . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dboverspeed = $overspeed . "desember_" . $year;
				break;
		}

		//company data
		$companydata = $this->getcompany_bycreator(4408);
		$data_company = array(); //format 1
		$master_company = array(); //format 2
		if ($company != "all") {
			$exp = explode("@", $company);
			$count_company = 1;
			$company = $exp[0];
			$data_company[$exp[0]] = $exp[1];
			$master_company[0] = $exp[1];
		} else {
			$count_company = count($companydata);
			for ($i = 0; $i < $count_company; $i++) {
				$data_company[$companydata[$i]->company_id] = $companydata[$i]->company_name;
				$master_company[$i] = $companydata[$i]->company_name;
			}
		}

		//violation data

		$rviolation = $this->getViolation(); //ambil master data violation alrmmaster
		$dataviolation = array(); //untuk simpan data violation
		$master_violation = array(); //untuk simpan data violation format 2
		$allviolation = array(); //untuk simpan id violation
		$nr = count($rviolation);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$dataviolation[$rviolation[$i]["alarmmaster_id"]] = $rviolation[$i]["alarmmaster_name"];
				$master_violation[$i] = $rviolation[$i]["alarmmaster_name"];
				array_push($allviolation, $rviolation[$i]["alarmmaster_id"]);
			}
		}
		$dataalarmtype = array(); //untuk query where_in
		$dataviolationalarmtype = array(); //untuk ditampilkan di view
		if ($violation != "all") {
			$alarmtype = $this->getAlarmtype($violation);
		} else {
			$alarmtype = $this->getAlarmtype(null, $allviolation);
		}
		$nr = count($alarmtype);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				if (!isset($dataviolationalarmtype[$alarmtype[$i]["alarm_type"]])) {
					if ($violation == "all") {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$alarmtype[$i]["alarm_master_id"]];
					} else {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$violation];
					}
				}
				array_push($dataalarmtype, $alarmtype[$i]["alarm_type"]);
			}
		}
		$data = $this->getSecurityEvidence($dbtable, $company, $vehicle, $violation, $dataalarmtype, $date, $filter_true_false);
		$data2 = $this->getOverspeed($dboverspeed, $company, $vehicle, $date);
		$numrows = count($data);
		// $numrows = 0;
		$numrows2 = count($data2);
		// $numrows2 = 0;
		$total_data = 0;
		// $callback['input'] = array(
		// 	"violation" => $violation,
		// 	"company" => $company,
		// 	"vehicle" => $vehicle,
		// 	"date" => $date,
		// );
		$data_fix = array();
		$data_table = array();
		$dhour = array();
		$dcompany = array();
		$dviolation = array();
		$c = array();
		$l = array();
		$seleksi_data = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_overspeed = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table_overspeed = array(); //untuk seleksi multiple data yang sama
		if ($numrows > 0) {
			for ($i = 0; $i < $numrows; $i++) {
				$datetime = $data[$i]['alarm_report_start_time'];
				//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
				$vehiclen = $data[$i]['alarm_report_vehicle_no'];
				if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
					$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];
				} else {
					$cmpny = $data[$i]['alarm_report_vehicle_company'];
				}
				if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
					$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
				} else {
					$vltion = $data[$i]['alarm_report_type'];
				}
				$exp = explode(" ", $datetime);
				$h = explode(":", $exp[1]);

				if (!isset($hour[$h[0]])) {
					$hour[$h[0]] = 1;
					$dhour[] = $h[0];
				}
				if (!isset($c[$cmpny])) {
					$c[$cmpny] = $cmpny;
					$dcompany[] = $cmpny;
				}
				if (!isset($l[$vltion])) {

					$l[$vltion] = $vltion;
					$dviolation[] = $vltion;
				}
				// $data_fix[$exp[0]][$data[$i]['location_report_company_name']][$data[$i]['location_report_location']] = array("vehicle" => $data[$i]['location_report_vehicle_no']);

				$d = array(
					"company" => $cmpny,
					"vehicle" => $vehiclen,
					"location" => $data[$i]['alarm_report_location_start'],
					"violation" => $vltion,
					"time" => $exp[1],
					"coordinate" => $data[$i]['alarm_report_coordinate_start'],
					"info" => $data[$i]['alarm_report_name'],
					"hour" => $h[0],
					"file_url" => $data[$i]['alarm_report_fileurl']
				);
				if (!isset($data_fix[$h[0]][$cmpny])) {
					$data_fix[$h[0]][$cmpny] = array();
					array_push($data_fix[$h[0]][$cmpny], $d);
					$seleksi_data[$vltion][$vehiclen][$h[0]] = 1;
					$total_data++;
				} else {
					if (!isset($seleksi_data[$vltion][$vehiclen][$h[0]])) {
						$seleksi_data[$vltion][$vehiclen][$h[0]] = 1;
						array_push($data_fix[$h[0]][$cmpny], $d);
						$total_data++;
					}
				}
				if (!isset($data_table[$vltion][$cmpny])) {
					$data_table[$vltion][$cmpny] = array();
					array_push($data_table[$vltion][$cmpny], $d);
					$seleksi_data_table[$vltion][$vehiclen][$h[0]] = 1;
				} else {
					if (!isset($seleksi_data_table[$vltion][$vehiclen][$h[0]])) {
						$seleksi_data_table[$vltion][$vehiclen][$h[0]] = 1;
						array_push($data_table[$vltion][$cmpny], $d);
					}
				}
				// if (!isset($unit_location[$data[$i]['location_report_vehicle_no']])) {
				//     $unit_location[$data[$i]['location_report_vehicle_no']] = $data[$i]['location_report_vehicle_no'];
				// }
				// $data[$i]['hour'] = $h[0];
			}
		}
		if ($numrows2 > 0) {
			for ($i = 0; $i < $numrows2; $i++) {
				// $datetime = $data[$i]['overspeed_report_gps_time'];
				$datetime = $data2[$i]['overspeed_report_gps_time'];
				// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));
				if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
					$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];
				} else {
					$cmpny = $data2[$i]['overspeed_report_vehicle_company'];
				}
				$vltion = "Overspeed";
				$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
				$locationn = $data2[$i]['overspeed_report_location'];
				$exp = explode(" ", $datetime);
				$h = explode(":", $exp[1]);

				if (!isset($hour[$h[0]])) {
					$hour[$h[0]] = 1;
					$dhour[] = $h[0];
				}
				if (!isset($c[$cmpny])) {
					$c[$cmpny] = $cmpny;
					$dcompany[] = $cmpny;
				}
				if (!isset($l[$vltion])) {
					$l[$vltion] = $vltion;
					$dviolation[] = $vltion;
				}
				$d = array(
					"company" => $cmpny,
					"vehicle" => $vehiclen,
					"location" => $locationn,
					"violation" => $vltion,
					"time" => $exp[1],
					"coordinate" => $data2[$i]['overspeed_report_coordinate'],
					"info" => "Speed: " . $data2[$i]['overspeed_report_speed'] . " Kph, Limit: " . $data2[$i]['overspeed_report_geofence_limit'] . "Kph, alarm level: " . $data2[$i]['overspeed_report_level'] . ", Jalur: " . $data2[$i]['overspeed_report_jalur'],
					"hour" => $h[0],
					"file_url" => "#"
				);
				if (!isset($data_fix[$h[0]][$cmpny])) {
					$data_fix[$h[0]][$cmpny] = array();
					array_push($data_fix[$h[0]][$cmpny], $d);
					$seleksi_data_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
					$total_data++;
				} else {
					if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$h[0]][$locationn])) {
						$seleksi_data_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
						array_push($data_fix[$h[0]][$cmpny], $d);
						$total_data++;
					}
				}
				if (!isset($data_table[$vltion][$cmpny])) {
					$data_table[$vltion][$cmpny] = array();
					array_push($data_table[$vltion][$cmpny], $d);
					$seleksi_data_table_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
				} else {
					if (!isset($seleksi_data_table_overspeed[$vltion][$vehiclen][$h[0]][$locationn])) {
						$seleksi_data_table_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
						array_push($data_table[$vltion][$cmpny], $d);
					}
				}
			}
		}
		if ($total_data == 0) {
			$callback['error'] = true;
			$callback['message'] = "Data Empty!";
			echo json_encode($callback);
			return;
		}
		$this->params['data_table'] = $data_table;
		$this->params['master_company'] = $data_company; //master company format 1
		$this->params['master_company2'] = $master_company; //master company format 2
		$this->params['master_violation'] = $master_violation; //master company format 2
		$this->params['master_alarm'] = $dataviolationalarmtype;

		$this->params['data_fix'] = $data_fix;
		$this->params['data_hour'] = $dhour; //data hour dari source
		$this->params['data_alarm'] = $dviolation; //data violation dari source
		$this->params['data_company'] = $dcompany; //data company dari source
		// $this->params['input'] = $callback['input'];
		$this->params['total_data'] = $total_data;
		$this->params['date'] = $datein;
		$html                    = $this->load->view('newdashboard/hse/v_dashboard_hse_result_dev', $this->params, true);

		$callback["html"]        = $html;

		echo json_encode($callback);
	}

	function search_dev_backup4()
	{
		ini_set('max_execution_time', 300); // 5 minutes
		ini_set('memory_limit', "2G");
		$datein    = $this->input->post("date");
		$company = $this->input->post("company");
		$violation = $this->input->post("violation");
		$vehicle = $this->input->post("vehicle");
		$filter_true_false = $this->input->post("filter_true_false");
		$date = date("Y-m-d", strtotime($datein));
		$month = date("F", strtotime($datein));
		$monthforparam = date("m", strtotime($datein));
		$year = date("Y", strtotime($datein));
		$report     = "alarm_evidence_";
		$overspeed  = "overspeed_";

		switch ($month) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dboverspeed = $overspeed . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dboverspeed = $overspeed . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dboverspeed = $overspeed . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dboverspeed = $overspeed . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dboverspeed = $overspeed . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dboverspeed = $overspeed . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dboverspeed = $overspeed . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dboverspeed = $overspeed . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dboverspeed = $overspeed . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dboverspeed = $overspeed . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dboverspeed = $overspeed . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dboverspeed = $overspeed . "desember_" . $year;
				break;
		}

		//company data
		$companydata = $this->getcompany_bycreator(4408);
		$data_company = array(); //format 1
		$master_company = array(); //format 2
		if ($company != "all") {
			$exp = explode("@", $company);
			$count_company = 1;
			$company = $exp[0];
			$data_company[$exp[0]] = $exp[1];
			$master_company[0] = $exp[1];
		} else {
			$count_company = count($companydata);
			for ($i = 0; $i < $count_company; $i++) {
				$data_company[$companydata[$i]->company_id] = $companydata[$i]->company_name;
				$master_company[$i] = $companydata[$i]->company_name;
			}
		}

		//violation data

		$rviolation = $this->getViolation(); //ambil master data violation alrmmaster
		$dataviolation = array(); //untuk simpan data violation
		$master_violation = array(); //untuk simpan data violation format 2
		$allviolation = array(); //untuk simpan id violation
		$nr = count($rviolation);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				$dataviolation[$rviolation[$i]["alarmmaster_id"]] = $rviolation[$i]["alarmmaster_name"];
				$master_violation[$i] = $rviolation[$i]["alarmmaster_name"];
				array_push($allviolation, $rviolation[$i]["alarmmaster_id"]);
			}
		}
		$dataalarmtype = array(); //untuk query where_in
		$dataviolationalarmtype = array(); //untuk ditampilkan di view
		if ($violation != "all") {
			$alarmtype = $this->getAlarmtype($violation);
		} else {
			$alarmtype = $this->getAlarmtype(null, $allviolation);
		}
		$nr = count($alarmtype);
		if ($nr > 0) {
			for ($i = 0; $i < $nr; $i++) {
				if (!isset($dataviolationalarmtype[$alarmtype[$i]["alarm_type"]])) {
					if ($violation == "all") {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$alarmtype[$i]["alarm_master_id"]];
					} else {
						$dataviolationalarmtype[$alarmtype[$i]["alarm_type"]] = $dataviolation[$violation];
					}
				}
				array_push($dataalarmtype, $alarmtype[$i]["alarm_type"]);
			}
		}
		$data = $this->getSecurityEvidence($dbtable, $company, $vehicle, $violation, $dataalarmtype, $date, $filter_true_false);
		$data2 = $this->getOverspeed($dboverspeed, $company, $vehicle, $date);
		$numrows = count($data);
		// $numrows = 0;
		$numrows2 = count($data2);
		// $numrows2 = 0;
		$total_data = 0;
		// $callback['input'] = array(
		// 	"violation" => $violation,
		// 	"company" => $company,
		// 	"vehicle" => $vehicle,
		// 	"date" => $date,
		// );
		$data_fix = array();
		$data_table = array();
		$dhour = array();
		$dcompany = array();
		$dviolation = array();
		$c = array();
		$l = array();
		$seleksi_data = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_overspeed = array(); //untuk seleksi multiple data yang sama
		$seleksi_data_table_overspeed = array(); //untuk seleksi multiple data yang sama
		if ($numrows > 0) {
			for ($i = 0; $i < $numrows; $i++) {
				$datetime = $data[$i]['alarm_report_start_time'];
				//$datetime = date("Y-m-d H:i:s", strtotime($data[$i]['alarm_report_start_time']) + (60 * 60));
				$vehiclen = $data[$i]['alarm_report_vehicle_no'];
				if (isset($data_company[$data[$i]['alarm_report_vehicle_company']])) {
					$cmpny = $data_company[$data[$i]['alarm_report_vehicle_company']];
				} else {
					$cmpny = $data[$i]['alarm_report_vehicle_company'];
				}
				if (isset($dataviolationalarmtype[$data[$i]['alarm_report_type']])) {
					$vltion = $dataviolationalarmtype[$data[$i]['alarm_report_type']];
				} else {
					$vltion = $data[$i]['alarm_report_type'];
				}
				$exp = explode(" ", $datetime);
				$h = explode(":", $exp[1]);

				if (!isset($hour[$h[0]])) {
					$hour[$h[0]] = 1;
					$dhour[] = $h[0];
				}
				if (!isset($c[$cmpny])) {
					$c[$cmpny] = $cmpny;
					$dcompany[] = $cmpny;
				}
				if (!isset($l[$vltion])) {

					$l[$vltion] = $vltion;
					$dviolation[] = $vltion;
				}
				// $data_fix[$exp[0]][$data[$i]['location_report_company_name']][$data[$i]['location_report_location']] = array("vehicle" => $data[$i]['location_report_vehicle_no']);

				$d = array(
					"company" => $cmpny,
					"vehicle" => $vehiclen,
					"location" => $data[$i]['alarm_report_location_start'],
					"violation" => $vltion,
					"time" => $exp[1],
					"coordinate" => $data[$i]['alarm_report_coordinate_start'],
					"info" => $data[$i]['alarm_report_name'],
					"hour" => $h[0],
					"file_url" => $data[$i]['alarm_report_fileurl']
				);
				if (!isset($data_fix[$h[0]][$cmpny])) {
					$data_fix[$h[0]][$cmpny] = array();
					array_push($data_fix[$h[0]][$cmpny], $d);
					$seleksi_data[$vltion][$vehiclen][$h[0]] = 1;
					$total_data++;
				} else {
					if (!isset($seleksi_data[$vltion][$vehiclen][$h[0]])) {
						$seleksi_data[$vltion][$vehiclen][$h[0]] = 1;
						array_push($data_fix[$h[0]][$cmpny], $d);
						$total_data++;
					}
				}
				if (!isset($data_table[$cmpny][$vltion])) {
					$data_table[$cmpny][$vltion] = array();
					array_push($data_table[$cmpny][$vltion], $d);
					$seleksi_data_table[$vltion][$vehiclen][$h[0]] = 1;
				} else {
					if (!isset($seleksi_data_table[$vltion][$vehiclen][$h[0]])) {
						$seleksi_data_table[$vltion][$vehiclen][$h[0]] = 1;
						array_push($data_table[$cmpny][$vltion], $d);
					}
				}
				// if (!isset($unit_location[$data[$i]['location_report_vehicle_no']])) {
				//     $unit_location[$data[$i]['location_report_vehicle_no']] = $data[$i]['location_report_vehicle_no'];
				// }
				// $data[$i]['hour'] = $h[0];
			}
		}
		if ($numrows2 > 0) {
			for ($i = 0; $i < $numrows2; $i++) {
				// $datetime = $data[$i]['overspeed_report_gps_time'];
				$datetime = $data2[$i]['overspeed_report_gps_time'];
				// $datetime = date("Y-m-d H:i:s", strtotime($datetime) + (60 * 60));
				if (isset($data_company[$data2[$i]['overspeed_report_vehicle_company']])) {
					$cmpny = $data_company[$data2[$i]['overspeed_report_vehicle_company']];
				} else {
					$cmpny = $data2[$i]['overspeed_report_vehicle_company'];
				}
				$vltion = "Overspeed";
				$vehiclen = $data2[$i]['overspeed_report_vehicle_no'];
				$locationn = $data2[$i]['overspeed_report_location'];
				$exp = explode(" ", $datetime);
				$h = explode(":", $exp[1]);

				if (!isset($hour[$h[0]])) {
					$hour[$h[0]] = 1;
					$dhour[] = $h[0];
				}
				if (!isset($c[$cmpny])) {
					$c[$cmpny] = $cmpny;
					$dcompany[] = $cmpny;
				}
				if (!isset($l[$vltion])) {
					$l[$vltion] = $vltion;
					$dviolation[] = $vltion;
				}
				$d = array(
					"company" => $cmpny,
					"vehicle" => $vehiclen,
					"location" => $locationn,
					"violation" => $vltion,
					"time" => $exp[1],
					"coordinate" => $data2[$i]['overspeed_report_coordinate'],
					"info" => "Speed: " . $data2[$i]['overspeed_report_speed'] . " Kph, Limit: " . $data2[$i]['overspeed_report_geofence_limit'] . "Kph, alarm level: " . $data2[$i]['overspeed_report_level'] . ", Jalur: " . $data2[$i]['overspeed_report_jalur'],
					"hour" => $h[0],
					"file_url" => "#"
				);
				if (!isset($data_fix[$h[0]][$cmpny])) {
					$data_fix[$h[0]][$cmpny] = array();
					array_push($data_fix[$h[0]][$cmpny], $d);
					$seleksi_data_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
					$total_data++;
				} else {
					if (!isset($seleksi_data_overspeed[$vltion][$vehiclen][$h[0]][$locationn])) {
						$seleksi_data_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
						array_push($data_fix[$h[0]][$cmpny], $d);
						$total_data++;
					}
				}
				if (!isset($data_table[$cmpny][$vltion])) {
					$data_table[$cmpny][$vltion] = array();
					array_push($data_table[$cmpny][$vltion], $d);
					$seleksi_data_table_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
				} else {
					if (!isset($seleksi_data_table_overspeed[$vltion][$vehiclen][$h[0]][$locationn])) {
						$seleksi_data_table_overspeed[$vltion][$vehiclen][$h[0]][$locationn] = 1;
						array_push($data_table[$cmpny][$vltion], $d);
					}
				}
			}
		}
		if ($total_data == 0) {
			$callback['error'] = true;
			$callback['message'] = "Data Empty!";
			echo json_encode($callback);
			return;
		}
		$this->params['data_table'] = $data_table;
		// $this->params['master_company'] = $data_company; //master company format 1
		$this->params['master_company2'] = $master_company; //master company format 2
		$this->params['master_violation'] = $master_violation; //master company format 2
		// $this->params['master_alarm'] = $dataviolationalarmtype;

		$this->params['data_fix'] = $data_fix;
		$this->params['data_hour'] = $dhour; //data hour dari source
		// $this->params['data_alarm'] = $dviolation; //data violation dari source
		// $this->params['data_company'] = $dcompany; //data company dari source
		// $this->params['input'] = $callback['input'];
		$this->params['total_data'] = $total_data;
		$this->params['date'] = $datein;
		$html                    = $this->load->view('newdashboard/hse/v_dashboard_hse_result_dev', $this->params, true);

		$callback["html"]        = $html;

		echo json_encode($callback);
	}

	function getSecurityEvidence($dbtable, $company, $vehicle, $violation, $dataalarmtype, $sdate, $edate, $filter_true_false)
	{
		//$start_date = date("Y-m-d H:i:s", strtotime($sdate) - (60 * 60));

		//$end_date = date("Y-m-d H:i:s", strtotime($edate) - (60 * 60));

		$start_date = date("Y-m-d H:i:s", strtotime($sdate));
		$end_date = date("Y-m-d H:i:s", strtotime($edate));

		$privilegecode   = $this->sess->user_id_role;
		$user_id         = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_parent     = $this->sess->user_parent;

		//tambahan distracted
		$black_list  = array(
			"401", "428", "451", "478", "602", "603", "608", "609", "652", "653", "658", "659",
			"600", "601", "650", "651", "630", "631",
			"624", "625", "637", "674", "675", "687"
		); //lane deviation & forward collation
		$hauling = $this->getAllStreetKM(4408); //HAULING
		$this->dbtrip = $this->load->database("tensor_report", true);

		$this->dbtrip->select("alarm_report_id,alarm_report_start_time,alarm_report_vehicle_no,alarm_report_location_start,alarm_report_vehicle_company,alarm_report_type,alarm_report_coordinate_start,alarm_report_name,alarm_report_media,alarm_report_fileurl,alarm_report_gpsstatus,alarm_report_vehicle_id");
		if ($company != "all") {
			$this->dbtrip->where("alarm_report_vehicle_company", $company);
		}

		if ($vehicle == "all") {
			if ($privilegecode == 0) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_id);
			} else if ($privilegecode == 1) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 2) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 3) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 4) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 5) {
				$this->dbtrip->where("alarm_report_vehicle_company", $user_company);
			} else if ($privilegecode == 6) {
				$this->dbtrip->where("alarm_report_vehicle_company", $user_company);
			} else {
				$this->dbtrip->where("alarm_report_vehicle_company", 99999);
			}
		} else {
			$this->dbtrip->where("alarm_report_vehicle_id", $vehicle);
		}

		$this->dbtrip->where("alarm_report_media", 0); //photo

		$this->dbtrip->where("alarm_report_start_time >=", $start_date);
		$this->dbtrip->where("alarm_report_start_time <=", $end_date);

		if ($violation != "all") {
			$this->dbtrip->where_in('alarm_report_type', $dataalarmtype);
		}

		if ($filter_true_false != "all") {
			$this->dbtrip->where_in('alarm_report_truefalse_up', $filter_true_false);
		}

		$this->dbtrip->where_not_in('alarm_report_type', $black_list);
		////$this->dbtrip->where("alarm_report_speed_status",1);		//buka untuk trial evalia
		// $this->dbtrip->like("alarm_report_location_start", "KM");   //buka untuk cari yang hanya di hauling KM
		// $this->dbtrip->where_in("alarm_report_location_start", $hauling); // HAULING
		$this->dbtrip->where("alarm_report_gpsstatus !=", "");
		//// $this->dbtrip->where_in('alarm_report_location_start', $street_register); //new filter

		$this->dbtrip->order_by("alarm_report_type", "asc");
		$this->dbtrip->order_by("alarm_report_start_time", "asc");
		//$this->dbtrip->group_by("alarm_report_start_time");
		//$this->dbtrip->group_by("alarm_report_location_start");
		$this->dbtrip->order_by("alarm_report_location_start", "asc");
		// $this->dbtrip->limit(800);
		$q = $this->dbtrip->get($dbtable);
		$rows = $q->result_array();

		return $rows;
	}
	function getSecurityEvidence_status($dbtable, $company, $vehicle, $violation, $dataalarmtype, $sdate, $edate, $status)
	{
		//$start_date = date("Y-m-d H:i:s", strtotime($sdate) - (60 * 60));

		//$end_date = date("Y-m-d H:i:s", strtotime($edate) - (60 * 60));

		$start_date = date("Y-m-d H:i:s", strtotime($sdate));
		$end_date = date("Y-m-d H:i:s", strtotime($edate));

		$privilegecode   = $this->sess->user_id_role;
		$user_id         = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_parent     = $this->sess->user_parent;

		//tambahan distracted
		/* $black_list  = array(
			"401", "428", "451", "478", "602", "603", "608", "609", "652", "653", "658", "659",
			"600", "601", "650", "651", "630", "631",
			"624", "625", "637", "674", "675", "687"
		); //lane deviation & forward collation */
		
		$black_list  = array(
			"630", "631"
		); //lane deviation & forward collation
		
		$hauling = $this->getAllStreetKM(4408); //HAULING
		$this->dbtrip = $this->load->database("tensor_report", true);

		$this->dbtrip->select("alarm_report_id,alarm_report_start_time,alarm_report_vehicle_no,alarm_report_location_start,
							   alarm_report_vehicle_company,alarm_report_type,alarm_report_coordinate_start,alarm_report_name,
							   alarm_report_media,alarm_report_fileurl,alarm_report_gpsstatus,alarm_report_vehicle_id,alarm_report_truefalse_up,
							   alarm_report_status_sendhazard,alarm_report_status_sendrecord
							   
							   ");
		if ($company != "all") {
			$this->dbtrip->where("alarm_report_vehicle_company", $company);
		}

		if ($vehicle == "all") {
			if ($privilegecode == 0) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_id);
			} else if ($privilegecode == 1) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 2) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 3) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 4) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 5) {
				$this->dbtrip->where("alarm_report_vehicle_company", $user_company);
			} else if ($privilegecode == 6) {
				$this->dbtrip->where("alarm_report_vehicle_company", $user_company);
			} else {
				$this->dbtrip->where("alarm_report_vehicle_company", 99999);
			}
		} else {
			$this->dbtrip->where("alarm_report_vehicle_id", $vehicle);
		}

		$this->dbtrip->where("alarm_report_media", 0); //photo

		//true or false
		if($status != "all")
		{
			$this->dbtrip->where("alarm_report_truefalse_up", $status);
		}

		$this->dbtrip->where("alarm_report_start_time >=", $start_date);
		$this->dbtrip->where("alarm_report_start_time <=", $end_date);

		if ($violation != "all") {
			$this->dbtrip->where_in('alarm_report_type', $dataalarmtype);
		}
		$this->dbtrip->where_not_in('alarm_report_type', $black_list); //only driver change 
		////$this->dbtrip->where("alarm_report_speed_status",1);		//buka untuk trial evalia
		// $this->dbtrip->like("alarm_report_location_start", "KM");   //buka untuk cari yang hanya di hauling KM
		// $this->dbtrip->where_in("alarm_report_location_start", $hauling); // HAULING
		$this->dbtrip->where("alarm_report_gpsstatus !=", "");
		//// $this->dbtrip->where_in('alarm_report_location_start', $street_register); //new filter
		$this->dbtrip->where("alarm_report_gpsstatus !=", "pushalert");
		$this->dbtrip->order_by("alarm_report_type", "asc");
		$this->dbtrip->order_by("alarm_report_start_time", "asc");
		$this->dbtrip->order_by("alarm_report_location_start", "asc");
		// $this->dbtrip->limit(800);
		$q = $this->dbtrip->get($dbtable);
		$rows = $q->result_array();

		return $rows;
	}

	function getSecurityEvidence_backup1($dbtable, $company, $vehicle, $violation, $dataalarmtype, $date)
	{
		$sdate = $date . " 00:00:00";
		$edate = $date . " 23:59:59";
		//$start_date = date("Y-m-d H:i:s", strtotime($sdate) - (60 * 60));
		//$end_date = date("Y-m-d H:i:s", strtotime($edate) - (60 * 60));

		$start_date = date("Y-m-d H:i:s", strtotime($sdate));
		$end_date = date("Y-m-d H:i:s", strtotime($edate));

		$privilegecode   = $this->sess->user_id_role;
		$user_id         = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_parent     = $this->sess->user_parent;

		$black_list  = array(
			"401", "428", "451", "478", "602", "603", "608", "609", "652", "653", "658", "659",
			"600", "601", "650", "651","630","631"
		); //lane deviation & forward collation
		$this->dbtrip = $this->load->database("tensor_report", true);

		$this->dbtrip->select("alarm_report_start_time,alarm_report_vehicle_no,alarm_report_location_start,alarm_report_vehicle_company,alarm_report_type,alarm_report_coordinate_start,alarm_report_name,alarm_report_fileurl");
		if ($company != "all") {
			$this->dbtrip->where("alarm_report_vehicle_company", $company);
		}

		if ($vehicle == "all") {
			if ($privilegecode == 0) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_id);
			} else if ($privilegecode == 1) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 2) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 3) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 4) {
				$this->dbtrip->where("alarm_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 5) {
				$this->dbtrip->where("alarm_report_vehicle_company", $user_company);
			} else if ($privilegecode == 6) {
				$this->dbtrip->where("alarm_report_vehicle_company", $user_company);
			} else {
				$this->dbtrip->where("alarm_report_vehicle_company", 99999);
			}
		} else {
			$this->dbtrip->where("alarm_report_vehicle_id", $vehicle);
		}

		$this->dbtrip->where("alarm_report_media", 0); //photo
		$this->dbtrip->where("alarm_report_start_time >=", $start_date);
		$this->dbtrip->where("alarm_report_start_time <=", $end_date);

		if ($violation != "all") {
			$this->dbtrip->where_in('alarm_report_type', $dataalarmtype);
		}
		$this->dbtrip->where_not_in('alarm_report_type', $black_list);
		////$this->dbtrip->where("alarm_report_speed_status",1);		//buka untuk trial evalia
		$this->dbtrip->like("alarm_report_location_start", "KM");   //buka untuk cari yang hanya di hauling KM
		$this->dbtrip->where("alarm_report_gpsstatus !=", "");
		//// $this->dbtrip->where_in('alarm_report_location_start', $street_register); //new filter
		$this->dbtrip->order_by("alarm_report_start_time", "asc");
		$this->dbtrip->group_by("alarm_report_start_time");
		$this->dbtrip->group_by("alarm_report_location_start");
		$this->dbtrip->order_by("alarm_report_location_start", "asc");
		// $this->dbtrip->limit(800);
		$q = $this->dbtrip->get($dbtable);
		$rows = $q->result_array();

		return $rows;
	}

	function getOverspeed($dbtable, $company, $vehicle, $sdate, $edate)
	{

		$nowdate_report = date("Y-m-d", strtotime($sdate));
		$now = date("Y-m-d");
		if ($now == $nowdate_report) {
			// jika kondisi alert hari ini
			$month = date('F');
			$year = date('Y');
			$overspeed = "overspeed_hour_";
			$report = "overspeed_hour_";
			switch ($month) {
				case "January":
					$dbtable = $report . "januari_" . $year;
					$dboverspeed = $overspeed . "januari_" . $year;
					break;
				case "February":
					$dbtable = $report . "februari_" . $year;
					$dboverspeed = $overspeed . "februari_" . $year;
					break;
				case "March":
					$dbtable = $report . "maret_" . $year;
					$dboverspeed = $overspeed . "maret_" . $year;
					break;
				case "April":
					$dbtable = $report . "april_" . $year;
					$dboverspeed = $overspeed . "april_" . $year;
					break;
				case "May":
					$dbtable = $report . "mei_" . $year;
					$dboverspeed = $overspeed . "mei_" . $year;
					break;
				case "June":
					$dbtable = $report . "juni_" . $year;
					$dboverspeed = $overspeed . "juni_" . $year;
					break;
				case "July":
					$dbtable = $report . "juli_" . $year;
					$dboverspeed = $overspeed . "juli_" . $year;
					break;
				case "August":
					$dbtable = $report . "agustus_" . $year;
					$dboverspeed = $overspeed . "agustus_" . $year;
					break;
				case "September":
					$dbtable = $report . "september_" . $year;
					$dboverspeed = $overspeed . "september_" . $year;
					break;
				case "October":
					$dbtable = $report . "oktober_" . $year;
					$dboverspeed = $overspeed . "oktober_" . $year;
					break;
				case "November":
					$dbtable = $report . "november_" . $year;
					$dboverspeed = $overspeed . "november_" . $year;
					break;
				case "December":
					$dbtable = $report . "desember_" . $year;
					$dboverspeed = $overspeed . "desember_" . $year;
					break;
			}
			//return array();
		//print_r($dbtable);exit();
		}


		$privilegecode   = $this->sess->user_id_role;
		$user_id         = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_parent     = $this->sess->user_parent;
		$hauling = $this->getAllStreetKM(4408); //HAULING
		// $start_date = date("Y-m-d H:i:s", strtotime($sdate) - (60 * 60));
		// $end_date = date("Y-m-d H:i:s", strtotime($edate) - (60 * 60));
		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->select("overspeed_report_id,overspeed_report_vehicle_company,overspeed_report_vehicle_no,overspeed_report_speed,overspeed_report_location,overspeed_report_gps_time, overspeed_report_coordinate, overspeed_report_jalur, overspeed_report_level, overspeed_report_geofence_limit ");
		$this->dbtrip->where("overspeed_report_gps_time >=", $sdate);
		$this->dbtrip->where("overspeed_report_gps_time <=", $edate);
		$this->dbtrip->where("overspeed_report_speed_status", 1); //valid data
		$this->dbtrip->where("overspeed_report_geofence_type", "road"); //khusus dijalan
		// $this->dbtrip->like("overspeed_report_location", "KM");
		$this->dbtrip->where_in("overspeed_report_location", $hauling); // HAULING
		$this->dbtrip->where("overspeed_report_event_status", 1);
		$this->dbtrip->order_by("overspeed_report_level", "asc");
		$this->dbtrip->order_by("overspeed_report_gps_time", "asc");
		$this->dbtrip->group_by("overspeed_report_gps_time");
		// $this->dbtrip->order_by("overspeed_report_location", "asc");
		// $this->dbtrip->group_by("overspeed_report_location");
		if ($company != "all") {
			$this->dbtrip->where("overspeed_report_vehicle_company", $company);
		}
		if ($vehicle == "all") {
			if ($privilegecode == 0) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_id);
			} else if ($privilegecode == 1) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 2) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 3) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 4) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 5) {
				$this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
			} else if ($privilegecode == 6) {
				$this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
			}			else {
				$this->dbtrip->where("overspeed_report_vehicle_company", 99999);
			}
			$this->dbtrip->where("overspeed_report_vehicle_id <>", 72150933); //jika pilih all bukan mobil trial
		} else {
			$this->dbtrip->where("overspeed_report_vehicle_device", $vehicle);
		}
		// $this->dbtrip->limit(200);
		$q = $this->dbtrip->get($dbtable);
		$rows = $q->result_array();

		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		return $rows;
	}

	//only overspeed alert // overspeed hour
	function getOverspeed2($dbtable, $company, $vehicle, $sdate, $edate, $filter_true_false)
	{


		$privilegecode   = $this->sess->user_id_role;
		$user_id         = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_parent     = $this->sess->user_parent;
		// $hauling = $this->getAllStreetKM(4408); //HAULING
		// $start_date = date("Y-m-d H:i:s", strtotime($sdate) - (60 * 60));
		// $end_date = date("Y-m-d H:i:s", strtotime($edate) - (60 * 60));
		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->select("overspeed_report_id,overspeed_report_vehicle_company,overspeed_report_vehicle_no,overspeed_report_speed,overspeed_report_location,overspeed_report_gps_time, overspeed_report_coordinate, overspeed_report_jalur, overspeed_report_level, overspeed_report_geofence_limit ");
		$this->dbtrip->where("overspeed_report_gps_time >=", $sdate);
		$this->dbtrip->where("overspeed_report_gps_time <=", $edate);
		$this->dbtrip->where("overspeed_report_speed_status", 1); //valid data
		$this->dbtrip->where("overspeed_report_geofence_type", "road"); //khusus dijalan
		// $this->dbtrip->like("overspeed_report_location", "KM");
		// $this->dbtrip->where_in("overspeed_report_location", $hauling); // HAULING
		$this->dbtrip->where("overspeed_report_event_status", 1); //lokasi yg sama dihitung 1 event
		$this->dbtrip->order_by("overspeed_report_level", "asc");
		$this->dbtrip->order_by("overspeed_report_gps_time", "asc");
		$this->dbtrip->group_by("overspeed_report_gps_time");
		// $this->dbtrip->order_by("overspeed_report_location", "asc");
		// $this->dbtrip->group_by("overspeed_report_location");
		if ($company != "all") {
			$this->dbtrip->where("overspeed_report_vehicle_company", $company);
		}

		if ($filter_true_false != "all") {
			$this->dbtrip->where('overspeed_report_truefalse_up', $filter_true_false); //$alarmtype $alarmbymaster[0]['alarm_type']
		}

		if ($vehicle == "all") {
			if ($privilegecode == 0) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_id);
			} else if ($privilegecode == 1) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 2) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 3) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 4) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 5) {
				$this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
			} else if ($privilegecode == 6) {
				$this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
			}			else {
				$this->dbtrip->where("overspeed_report_vehicle_company", 99999);
			}

		} else {
			$this->dbtrip->where("overspeed_report_vehicle_device", $vehicle);
		}
		// $this->dbtrip->limit(200);
		$q = $this->dbtrip->get($dbtable);
		$rows = $q->result_array();

		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		return $rows;
	}

	//only overspeed alert // overspeed hour
	function getOverspeed2_status($dbtable, $company, $vehicle, $sdate, $edate, $status)
	{

		$privilegecode   = $this->sess->user_id_role;
		$user_id         = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_parent     = $this->sess->user_parent;
		// $hauling = $this->getAllStreetKM(4408); //HAULING
		// $start_date = date("Y-m-d H:i:s", strtotime($sdate) - (60 * 60));
		// $end_date = date("Y-m-d H:i:s", strtotime($edate) - (60 * 60));
		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->select("overspeed_report_id,overspeed_report_vehicle_company,overspeed_report_vehicle_no,overspeed_report_speed,
							   overspeed_report_location,overspeed_report_gps_time, overspeed_report_coordinate, overspeed_report_jalur,
							   overspeed_report_level, overspeed_report_geofence_limit, overspeed_report_truefalse_up,
							   overspeed_report_status_sendhazard, overspeed_report_status_sendrecord
							  ");
		$this->dbtrip->where("overspeed_report_gps_time >=", $sdate);
		$this->dbtrip->where("overspeed_report_gps_time <=", $edate);
		$this->dbtrip->where("overspeed_report_speed_status", 1); //valid data
		$this->dbtrip->where("overspeed_report_geofence_type", "road"); //khusus dijalan
		// $this->dbtrip->like("overspeed_report_location", "KM");
		// $this->dbtrip->where_in("overspeed_report_location", $hauling); // HAULING
		$this->dbtrip->where("overspeed_report_event_status", 1); //lokasi yg sama dihitung 1 event
		$this->dbtrip->order_by("overspeed_report_level", "asc");
		$this->dbtrip->order_by("overspeed_report_gps_time", "asc");
		$this->dbtrip->group_by("overspeed_report_gps_time");
		// $this->dbtrip->order_by("overspeed_report_location", "asc");
		// $this->dbtrip->group_by("overspeed_report_location");
		if ($company != "all") {
			$this->dbtrip->where("overspeed_report_vehicle_company", $company);
		}
		//true or false
		if($status != "all")
		{
			$this->dbtrip->where("overspeed_report_truefalse_up", $status);
		}
		if ($vehicle == "all") {
			if ($privilegecode == 0) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_id);
			} else if ($privilegecode == 1) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 2) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 3) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 4) {
				$this->dbtrip->where("overspeed_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 5) {
				$this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
			} else if ($privilegecode == 6) {
				$this->dbtrip->where("overspeed_report_vehicle_company", $user_company);
			}			else {
				$this->dbtrip->where("overspeed_report_vehicle_company", 99999);
			}

		} else {
			$this->dbtrip->where("overspeed_report_vehicle_device", $vehicle);
		}
		// $this->dbtrip->limit(200);
		$q = $this->dbtrip->get($dbtable);
		$rows = $q->result_array();

		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		return $rows;
	}

	function getAlarmtype($alarmmaster_id = null, $alarmmaster = null)
	{
		//tambahan distracted
		$black_list  = array(
			"401", "428", "451", "478", "602", "603", "608", "609", "652", "653", "658", "659",
			"600", "601", "650", "651", "630","631",
			"624", "625", "637", "674", "675", "687"
		); //lane deviation & forward collation

		$this->dbalarm = $this->load->database("webtracking_ts", true);
		$this->dbalarm->select("alarm_type, alarm_master_id");
		if ($alarmmaster_id != null) {
			$this->dbalarm->where("alarm_master_id", $alarmmaster_id);
		}
		if ($alarmmaster != null) {
			$this->dbalarm->where_in("alarm_master_id", $alarmmaster);
		}
		//$this->dbalarm->where_not_in("alarm_type", $black_list); //follow post event
		$q        = $this->dbalarm->get("webtracking_ts_alarm");
		return  $q->result_array();
	}

	//getmaster alarmmaster
	function getViolation()
	{
		$this->dbalarm = $this->load->database("webtracking_ts", true);
		$this->dbalarm->select("alarmmaster_id, alarmmaster_name");
		$this->dbalarm->where("alarmmaster_status",1); //follow post-event
		//$this->dbalarm->where("alarmmaster_flag", 0);
		$q        = $this->dbalarm->get("webtracking_ts_alarmmaster");
		return  $q->result_array();
	}

	function getRitase($dbtable, $company, $vehicle, $sdate, $edate)
	{

		$privilegecode   = $this->sess->user_id_role;
		$user_id         = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_parent     = $this->sess->user_parent;

		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->select("ritase_report_id,ritase_report_vehicle_company");
		$this->dbtrip->where("ritase_report_end_time >=", $sdate);
		$this->dbtrip->where("ritase_report_end_time <=", $edate);
		$this->dbtrip->order_by("ritase_report_end_time", "asc");
		if ($company != "all") {
			$this->dbtrip->where("ritase_report_vehicle_company", $company);
		}
		if ($vehicle == "all") {
			if ($privilegecode == 0) {
				$this->dbtrip->where("ritase_report_vehicle_user_id", $user_id);
			} else if ($privilegecode == 1) {
				$this->dbtrip->where("ritase_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 2) {
				$this->dbtrip->where("ritase_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 3) {
				$this->dbtrip->where("ritase_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 4) {
				$this->dbtrip->where("ritase_report_vehicle_user_id", $user_parent);
			} else if ($privilegecode == 5) {
				$this->dbtrip->where("ritase_report_vehicle_company", $user_company);
			} else if ($privilegecode == 6) {
				$this->dbtrip->where("ritase_report_vehicle_company", $user_company);
			} else {
				$this->dbtrip->where("ritase_report_vehicle_company", 99999);
			}

		} else {
			$this->dbtrip->where("ritase_report_vehicle_device", $vehicle);
		}
		$q = $this->dbtrip->get($dbtable);
		$rows = $q->result();

		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		return $rows;
	}

	//alarm cmsv
	function getViolationBoard_byCompany($userid, $company, $violation, $periode, $startdate, $enddate)
	{

		$model = "alarmtype";
		$shour = "00:00:00";
		$ehour = "23:59:59";

		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "overspeed_board_";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));


		switch ($m1) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dbtable_sum = $report_sum . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dbtable_sum = $report_sum . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dbtable_sum = $report_sum . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dbtable_sum = $report_sum . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dbtable_sum = $report_sum . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dbtable_sum = $report_sum . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dbtable_sum = $report_sum . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dbtable_sum = $report_sum . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dbtable_sum = $report_sum . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dbtable_sum = $report_sum . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dbtable_sum = $report_sum . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dbtable_sum = $report_sum . "desember_" . $year;
				break;
		}

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();

		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->select("overspeed_board_total,overspeed_board_company_name");
		$this->dbtrip->order_by("overspeed_board_id", "asc");
		$this->dbtrip->where("overspeed_board_date >=", $sdate);
		$this->dbtrip->where("overspeed_board_date <=", $edate);
		/* if($company != "all"){
								$this->dbtrip->where("overspeed_board_company",$company); //default
							} */

		if ($violation != "all") {

			$this->dbtrip->where("overspeed_board_alarm", $violation);
		}

		$this->dbtrip->where("overspeed_board_type", 0); //default
		$this->dbtrip->where("overspeed_board_model", $model);
		$qdata = $this->dbtrip->get($dbtable);
		$totaldata = 0;

		if ($qdata->num_rows > 0) {
			$rows_data = $qdata->result();

			for ($i = 0; $i < count($rows_data); $i++) {
				$totaldata += $rows_data[$i]->overspeed_board_total;
				//$feature_level2[] = $rows_data[$i]->overspeed_board_company_name.",".$rows_data[$i]->overspeed_board_total;

			}
		}

		$feature_level1['name'] = 'Total ' . strtoupper($violation);
		$feature_level1['y'] = $totaldata;
		//$feature_level1['drilldown'] = 'contractor';
		//$feature_level1['drilldown'] = "level";
		$feature_level1['drilldown'] = null;
		//$content = json_encode($feature);
		$content_level1 = $feature_level1;

		//print_r($content_level1);exit();
		//print_r(json_encode($content_level1));//exit();
		//return $content_level1;
		return $content_level1;

		/*



					["RAM",0],["STLI",0]

					sample
						data: [{name: "01-Des-21",y: 99000,drilldown: "01"},{name: "01-Des-21",y: 99000,drilldown: "01"}]
					*/
	}

	function getViolationBoard_byCompany_contractor($userid, $company, $violation, $periode, $startdate, $enddate)
	{

		$model = "alarmecompany";
		$shour = "00:00:00";
		$ehour = "23:59:59";


		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "overspeed_board_";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));


		switch ($m1) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dbtable_sum = $report_sum . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dbtable_sum = $report_sum . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dbtable_sum = $report_sum . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dbtable_sum = $report_sum . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dbtable_sum = $report_sum . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dbtable_sum = $report_sum . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dbtable_sum = $report_sum . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dbtable_sum = $report_sum . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dbtable_sum = $report_sum . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dbtable_sum = $report_sum . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dbtable_sum = $report_sum . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dbtable_sum = $report_sum . "desember_" . $year;
				break;
		}

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();

		$rows_master = $this->getcompany_bycreator(4408);
		//print_r($rows_master);exit();
		$totaldata = 0;
		for ($x = 0; $x < count($rows_master); $x++) {

			$this->dbtrip = $this->load->database("tensor_report", true);
			$this->dbtrip->select("overspeed_board_total,overspeed_board_company_name");
			$this->dbtrip->order_by("overspeed_board_company_name", "asc");
			$this->dbtrip->where("overspeed_board_date >=", $sdate);
			$this->dbtrip->where("overspeed_board_date <=", $edate);
			$this->dbtrip->where("overspeed_board_vehicle_company", $rows_master[$x]->company_id);
			if ($violation != "all") {

				$this->dbtrip->where("overspeed_board_alarm", $violation);
			}

			$this->dbtrip->where("overspeed_board_type", 0); //default
			$this->dbtrip->where("overspeed_board_model", $model);
			$qdata = $this->dbtrip->get($dbtable);
			$totaldata = 0;

			if ($qdata->num_rows > 0) {
				$rows_data = $qdata->result();
				$totaldata = 0;
				for ($i = 0; $i < count($rows_data); $i++) {

					$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
				}
			}

			$feature_level1[$x]['name'] = $rows_master[$x]->company_name;
			$feature_level1[$x]['y'] = (int)$totaldata;
			//$feature_level1['drilldown'] = 'contractor';
			$feature_level1[$x]['drilldown'] = null;
		}

		//$content_level1 = json_encode($feature_level1);
		$content_level1 = $feature_level1;
		//print_r($content_level1);exit();
		//print_r(json_encode($content_level1));exit();

		return $content_level1;
	}

	function getAllViolationBoard_byType($userid, $company, $violation, $periode, $startdate, $enddate)
	{

		$model = "alarmtype";
		$shour = "00:00:00";
		$ehour = "23:59:59";


		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "overspeed_board_";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));


		switch ($m1) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dbtable_sum = $report_sum . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dbtable_sum = $report_sum . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dbtable_sum = $report_sum . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dbtable_sum = $report_sum . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dbtable_sum = $report_sum . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dbtable_sum = $report_sum . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dbtable_sum = $report_sum . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dbtable_sum = $report_sum . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dbtable_sum = $report_sum . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dbtable_sum = $report_sum . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dbtable_sum = $report_sum . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dbtable_sum = $report_sum . "desember_" . $year;
				break;
		}

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();

		$rows_master = $this->get_masteralarm_bycreator(4408);
		//print_r($rows_master);exit();
		$totaldata = 0;
		for ($x = 0; $x < count($rows_master); $x++) {

			$this->dbtrip = $this->load->database("tensor_report", true);
			$this->dbtrip->select("overspeed_board_total,overspeed_board_company_name");
			$this->dbtrip->order_by("overspeed_board_company_name", "asc");
			$this->dbtrip->where("overspeed_board_date >=", $sdate);
			$this->dbtrip->where("overspeed_board_date <=", $edate);
			$this->dbtrip->where("overspeed_board_alarm", $rows_master[$x]->alarmmaster_name);
			if ($violation != "all") {

				$this->dbtrip->where("overspeed_board_alarm", $violation);
			}

			$this->dbtrip->where("overspeed_board_type", 0); //default
			$this->dbtrip->where("overspeed_board_model", $model);
			$qdata = $this->dbtrip->get($dbtable);
			$totaldata = 0;

			if ($qdata->num_rows > 0) {
				$rows_data = $qdata->result();
				$totaldata = 0;
				for ($i = 0; $i < count($rows_data); $i++) {

					$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
				}
			}

			$feature_level1[$x]['name'] = $rows_master[$x]->alarmmaster_name;
			$feature_level1[$x]['y'] = (int)$totaldata;
			//$feature_level1['drilldown'] = 'contractor';
			$feature_level1[$x]['drilldown'] = null;
		}

		//$content_level1 = json_encode($feature_level1);
		$content_level1 = $feature_level1;
		//print_r($content_level1);exit();
		//print_r(json_encode($content_level1));exit();

		return $content_level1;
	}

	//overspeed
	function getOverspeedBoard_byCompany($userid, $company, $periode, $startdate, $enddate)
	{

		$model = "company";
		$shour = "00:00:00";
		$ehour = "23:59:59";

		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "overspeed_board_";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));


		switch ($m1) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dbtable_sum = $report_sum . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dbtable_sum = $report_sum . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dbtable_sum = $report_sum . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dbtable_sum = $report_sum . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dbtable_sum = $report_sum . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dbtable_sum = $report_sum . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dbtable_sum = $report_sum . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dbtable_sum = $report_sum . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dbtable_sum = $report_sum . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dbtable_sum = $report_sum . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dbtable_sum = $report_sum . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dbtable_sum = $report_sum . "desember_" . $year;
				break;
		}

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();

		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->select("overspeed_board_total,overspeed_board_company_name");
		$this->dbtrip->order_by("overspeed_board_id", "asc");
		$this->dbtrip->where("overspeed_board_date >=", $sdate);
		$this->dbtrip->where("overspeed_board_date <=", $edate);
		if ($company != "all") {
			$this->dbtrip->where("overspeed_board_company", $company); //default
		}
		$this->dbtrip->where("overspeed_board_type", 0); //default
		$this->dbtrip->where("overspeed_board_model", $model);
		$qdata = $this->dbtrip->get($dbtable);
		$totaloverspeed = 0;

		if ($qdata->num_rows > 0) {
			$rows_data = $qdata->result();

			for ($i = 0; $i < count($rows_data); $i++) {
				$totaloverspeed += $rows_data[$i]->overspeed_board_total;
				//$feature_level2[] = $rows_data[$i]->overspeed_board_company_name.",".$rows_data[$i]->overspeed_board_total;

			}
		}

		$feature_level1['name'] = 'Total Overspeed';
		$feature_level1['y'] = $totaloverspeed;
		//$feature_level1['drilldown'] = 'contractor';
		//$feature_level1['drilldown'] = "level";
		$feature_level1['drilldown'] = null;
		//$content = json_encode($feature);
		$content_level1 = $feature_level1;

		//print_r($content_level1);exit();
		//print_r(json_encode($content_level1));//exit();
		//return $content_level1;
		return $content_level1;

		/*



					["RAM",0],["STLI",0]

					sample
						data: [{name: "01-Des-21",y: 99000,drilldown: "01"},{name: "01-Des-21",y: 99000,drilldown: "01"}]
					*/
	}

	function getOverspeedBoard_byCompany_contractor($userid, $company, $periode, $startdate, $enddate)
	{

		$model = "company";
		$shour = "00:00:00";
		$ehour = "23:59:59";


		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "overspeed_board_";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));


		switch ($m1) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dbtable_sum = $report_sum . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dbtable_sum = $report_sum . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dbtable_sum = $report_sum . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dbtable_sum = $report_sum . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dbtable_sum = $report_sum . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dbtable_sum = $report_sum . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dbtable_sum = $report_sum . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dbtable_sum = $report_sum . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dbtable_sum = $report_sum . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dbtable_sum = $report_sum . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dbtable_sum = $report_sum . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dbtable_sum = $report_sum . "desember_" . $year;
				break;
		}

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();

		$rows_master = $this->getcompany_bycreator(4408);
		//print_r($rows_master);exit();
		$totaldata = 0;
		for ($x = 0; $x < count($rows_master); $x++) {

			$this->dbtrip = $this->load->database("tensor_report", true);
			$this->dbtrip->select("overspeed_board_total,overspeed_board_company_name");
			$this->dbtrip->order_by("overspeed_board_company_name", "asc");
			$this->dbtrip->where("overspeed_board_date >=", $sdate);
			$this->dbtrip->where("overspeed_board_date <=", $edate);
			$this->dbtrip->where("overspeed_board_vehicle_company", $rows_master[$x]->company_id);
			$this->dbtrip->where("overspeed_board_type", 0); //default
			$this->dbtrip->where("overspeed_board_model", $model);
			$qdata = $this->dbtrip->get($dbtable);
			$totaloverspeed = 0;

			if ($qdata->num_rows > 0) {
				$rows_data = $qdata->result();
				$totaldata = 0;
				for ($i = 0; $i < count($rows_data); $i++) {

					$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
				}
			}

			$feature_level1[$x]['name'] = $rows_master[$x]->company_name;
			$feature_level1[$x]['y'] = (int)$totaldata;
			//$feature_level1['drilldown'] = 'contractor';
			$feature_level1[$x]['drilldown'] = null;
		}

		//$content_level1 = json_encode($feature_level1);
		$content_level1 = $feature_level1;
		//print_r($content_level1);exit();
		//print_r(json_encode($content_level1));exit();

		return $content_level1;
	}

	function getOverspeedBoard_byStreet($userid, $company, $periode, $startdate, $enddate)
	{
		$model = "street";
		$shour = "00:00:00";
		$ehour = "23:59:59";


		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "overspeed_board_";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));


		switch ($m1) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dbtable_sum = $report_sum . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dbtable_sum = $report_sum . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dbtable_sum = $report_sum . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dbtable_sum = $report_sum . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dbtable_sum = $report_sum . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dbtable_sum = $report_sum . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dbtable_sum = $report_sum . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dbtable_sum = $report_sum . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dbtable_sum = $report_sum . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dbtable_sum = $report_sum . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dbtable_sum = $report_sum . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dbtable_sum = $report_sum . "desember_" . $year;
				break;
		}

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();

		$rows_master = $this->getstreetalias_bycreator(4408);
		//print_r($rows_master);exit();
		for ($x = 0; $x < count($rows_master); $x++) {

			$this->dbtrip = $this->load->database("tensor_report", true);
			$this->dbtrip->select("overspeed_board_total,overspeed_board_street_alias");
			$this->dbtrip->order_by("overspeed_board_street_alias", "asc");
			$this->dbtrip->where("overspeed_board_date >=", $sdate);
			$this->dbtrip->where("overspeed_board_date <=", $edate);
			$this->dbtrip->where("overspeed_board_type", 0); //default
			$this->dbtrip->where("overspeed_board_model", $model);
			$this->dbtrip->where("overspeed_board_street_alias", $rows_master[$x]->km_alias_value);
			$qdata = $this->dbtrip->get($dbtable);
			$totaldata = 0;

			if ($qdata->num_rows > 0) {
				$rows_data = $qdata->result();

				for ($i = 0; $i < count($rows_data); $i++) {
					$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
				}
			}

			$feature_level1[$x]['name'] = $rows_master[$x]->km_alias_name;
			$feature_level1[$x]['y'] = (int)$totaldata;
			//$feature_level1['drilldown'] = 'contractor';
			$feature_level1[$x]['drilldown'] = null;
		}

		//$content_level1 = json_encode($feature_level1);
		$content_level1 = $feature_level1;
		//print_r($content_level1);exit();
		//print_r(json_encode($content_level1));exit();

		return $content_level1;
	}

	function getOverspeedBoard_byCompany_hourly($userid, $company, $periode, $startdate, $enddate)
	{

		$model = "hour";
		$shour = "00:00:00";
		$ehour = "23:59:59";


		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "overspeed_board_";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));


		switch ($m1) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dbtable_sum = $report_sum . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dbtable_sum = $report_sum . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dbtable_sum = $report_sum . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dbtable_sum = $report_sum . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dbtable_sum = $report_sum . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dbtable_sum = $report_sum . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dbtable_sum = $report_sum . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dbtable_sum = $report_sum . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dbtable_sum = $report_sum . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dbtable_sum = $report_sum . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dbtable_sum = $report_sum . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dbtable_sum = $report_sum . "desember_" . $year;
				break;
		}

		$error = "";
		$rows_summary = "";

		$feature_hour = array();
		//$feature_level2 = array();

		$rows_master = $this->gethour_bycreator(4408);
		//print_r($rows_master);exit();

		for ($x = 0; $x < count($rows_master); $x++) {
			/*
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total,overspeed_board_hour_name");
							$this->dbtrip->order_by("overspeed_board_hour_name","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",0); //default
							$this->dbtrip->where("overspeed_board_model",$model);
							$this->dbtrip->where("overspeed_board_hour_name",$rows_master[$x]->hour_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totaldata = 0;

							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								//print_r($rows_data);exit();
								for($i=0; $i < count($rows_data); $i++)
								{

									$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
									//$feature_hour[$i] = $rows_data[$i]->overspeed_board_hour_name;
								}
							}

							*/

			$feature_hour[$x] = $rows_master[$x]->hour_name;
			$result = $feature_hour;
		}
		//print_r($result);exit();
		return $result;
	}

	function getOverspeedBoard_byCompany_hourly2($userid, $company, $periode, $startdate, $enddate)
	{

		$model = "hour";
		$shour = "00:00:00";
		$ehour = "23:59:59";


		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "overspeed_board_";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));


		switch ($m1) {
			case "January":
				$dbtable = $report . "januari_" . $year;
				$dbtable_sum = $report_sum . "januari_" . $year;
				break;
			case "February":
				$dbtable = $report . "februari_" . $year;
				$dbtable_sum = $report_sum . "februari_" . $year;
				break;
			case "March":
				$dbtable = $report . "maret_" . $year;
				$dbtable_sum = $report_sum . "maret_" . $year;
				break;
			case "April":
				$dbtable = $report . "april_" . $year;
				$dbtable_sum = $report_sum . "april_" . $year;
				break;
			case "May":
				$dbtable = $report . "mei_" . $year;
				$dbtable_sum = $report_sum . "mei_" . $year;
				break;
			case "June":
				$dbtable = $report . "juni_" . $year;
				$dbtable_sum = $report_sum . "juni_" . $year;
				break;
			case "July":
				$dbtable = $report . "juli_" . $year;
				$dbtable_sum = $report_sum . "juli_" . $year;
				break;
			case "August":
				$dbtable = $report . "agustus_" . $year;
				$dbtable_sum = $report_sum . "agustus_" . $year;
				break;
			case "September":
				$dbtable = $report . "september_" . $year;
				$dbtable_sum = $report_sum . "september_" . $year;
				break;
			case "October":
				$dbtable = $report . "oktober_" . $year;
				$dbtable_sum = $report_sum . "oktober_" . $year;
				break;
			case "November":
				$dbtable = $report . "november_" . $year;
				$dbtable_sum = $report_sum . "november_" . $year;
				break;
			case "December":
				$dbtable = $report . "desember_" . $year;
				$dbtable_sum = $report_sum . "desember_" . $year;
				break;
		}

		$error = "";
		$rows_summary = "";

		//$feature_level1 = array();
		$feature_total = array();

		$rows_master = $this->gethour_bycreator(4408);
		//print_r($rows_master);exit();

		for ($x = 0; $x < count($rows_master); $x++) {
			$this->dbtrip = $this->load->database("tensor_report", true);
			$this->dbtrip->select("overspeed_board_total,overspeed_board_hour_name");
			$this->dbtrip->order_by("overspeed_board_hour_name", "asc");
			$this->dbtrip->where("overspeed_board_date >=", $sdate);
			$this->dbtrip->where("overspeed_board_date <=", $edate);
			$this->dbtrip->where("overspeed_board_type", 0); //default
			$this->dbtrip->where("overspeed_board_model", $model);
			$this->dbtrip->where("overspeed_board_hour_name", $rows_master[$x]->hour_name);
			$qdata = $this->dbtrip->get($dbtable);
			$totaldata = 0;

			if ($qdata->num_rows > 0) {
				$rows_data = $qdata->result();
				//print_r($rows_data);exit();
				for ($i = 0; $i < count($rows_data); $i++) {

					$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
					//$feature_hour[$i] = $rows_data[$i]->overspeed_board_hour_name;
				}
			}

			$feature_total[$x] = (int)$totaldata;
			$result = $feature_total;
		}
		//print_r($result);exit();
		return $result;
	}

	function getstreetalias_bycreator($userid)
	{

		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->order_by("km_alias_value", "asc");
		$this->dbts->where("km_alias_flag ", 0);
		$this->dbts->where("km_alias_user", $userid);
		$q = $this->dbts->get("ts_km_alias");
		$rows = $q->result();

		return $rows;
	}

	function getcompany_bycreator($user_id = null, $company_id = null)
	{
		//only DT contractor
		$company_list = array("1959"); //demo company
		$this->db->select("company_id,company_name");
		$this->db->order_by("company_name", "asc");
		$this->db->where("company_flag ", 0);
		if ($user_id != null) {
			$this->db->where("company_created_by", $user_id);
			//$this->db->or_where_in("company_id", $company_list);
		}
		if ($company_id != null) {
			$this->db->where("company_id", $company_id);
		}

		$q = $this->db->get("company");
		$rows = $q->result();
		//$total_rows = count($rows);

		return $rows;
	}

	function getcompanyname_byID($id)
	{
		$name = "-";
		$this->db->select("company_id,company_name");
		$this->db->order_by("company_name", "asc");
		$this->db->where("company_id ", $id);
		$q = $this->db->get("company");
		$row = $q->row();
		if(count($row)>0){

			$name = $row->company_name;

		}else{

			$name = "-";
		}

		return $name;
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

	function getshift_byhour($hour)
	{
		$shift_name = "-";
		$shift1 = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17");
        $shift2 = array("18", "19", "20", "21", "22", "23", "00", "01", "02", "03", "04", "05");

		if (in_array($hour, $shift1)){
			$shift_name = "1";
		}else if (in_array($hour, $shift2)){
			$shift_name = "2";
		}else{
			$shift_name = "-";
		}
		//print_r($shift_name);exit();
		return $shift_name;
	}

	function get_vehicle()
	{

		$privilegecode = $this->sess->user_id_role;

		$this->db->select("vehicle_no,vehicle_id,vehicle_imei,vehicle_device,vehicle_company");
		$this->db->order_by("vehicle_no", "asc");
		if ($privilegecode == 0) {
			$this->db->where("vehicle_user_id", $this->sess->user_id);
		} elseif ($privilegecode == 1) {
			$this->db->where("vehicle_user_id", $this->sess->user_parent);
		} elseif ($privilegecode == 2) {
			$this->db->where("vehicle_user_id", $this->sess->user_parent);
		} elseif ($privilegecode == 3) {
			$this->db->where("vehicle_user_id", $this->sess->user_parent);
		} elseif ($privilegecode == 4) {
			$this->db->where("vehicle_user_id", $this->sess->user_parent);
		} elseif ($privilegecode == 5) {
			$this->db->where("vehicle_company", $this->sess->user_company);
		} elseif ($privilegecode == 6) {
			$this->db->where("vehicle_company", $this->sess->user_company);
		}
		$this->db->where("vehicle_status", 1);
		$q = $this->db->get("vehicle");
		$rows = $q->result();

		return $rows;
	}

	function getAllVehicle_bycreator($userid)
	{

		$this->db->select("vehicle_no,vehicle_id,vehicle_imei,vehicle_device,vehicle_company");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_user_id ", $userid);
		$this->db->where("vehicle_status", 1);
		$q = $this->db->get("vehicle");
		$rows = $q->result();

		return $rows;
	}

	function getvehicle_byCompany($company)
	{

		$this->db->select("vehicle_no,vehicle_id");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_company ", $company);
		$this->db->where("vehicle_status", 1);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		//$total_rows = count($rows);

		return $rows;
	}

	function get_vehicle_by_company($id)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$company_list = array("1959","1948","1947","1946","1945","1926","1839","1837","1835","1834");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,vehicle_imei,vehicle_company");
		if ($id == "all") {
			$this->db->where("vehicle_user_id ", 4408);
			$this->db->where("vehicle_status", 1);
			$this->db->where_in("vehicle_company", $company_list);
		} else {
			$this->db->where("vehicle_company", $id);
			$this->db->where("vehicle_status <>", 3);
		}


		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if ($qd->num_rows() > 0) {
			$options = "<option value='all' selected='selected' >--All Vehicle</option>";
			foreach ($rd as $obj) {
				$options .= "<option value='" . $obj->vehicle_imei . "/" . $obj->vehicle_device . "/" . $obj->vehicle_company . "'>" . $obj->vehicle_no . " - " . $obj->vehicle_name . "</option>";
			}

			echo $options;
			return;
		}
	}

	function gethour_bycreator($userid)
	{

		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->order_by("hour_name", "asc");
		$this->dbts->where("hour_type ", 1);
		$this->dbts->where("hour_flag ", 0);
		$this->dbts->where("hour_user", $userid);
		$q = $this->dbts->get("ts_hour");
		$rows = $q->result();

		return $rows;
	}

	function get_masteralarm_bycreator($userid)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->order_by("alarmmaster_name", "asc");
		$this->dbts->where("alarmmaster_creator", $userid);
		$this->dbts->where("alarmmaster_status", 1);
		$this->dbts->where("alarmmaster_flag", 0);
		$q = $this->dbts->get("ts_alarmmaster");
		$rows = $q->result();

		return $rows;
	}

	function getAllStreetKM($userid)
	{
		$feature = array();
		$street_type_list = array("1", "5", "8", "7", "4", "3"); //HAULING + ROM ROAD + PORT + CP + ANTRIAN BLC , ROM = 3
		$this->dbmaster = $this->load->database("default", true);
		$this->dbmaster->select("street_name,street_alias,street_type");
		$this->dbmaster->order_by("street_name", "asc");
		$this->dbmaster->group_by("street_name");
		$this->dbmaster->where("street_creator", $userid);
		$this->dbmaster->where_in("street_type", $street_type_list);
		$this->dbmaster->where("street_name !=", "PORT BBC,"); //selain port bbc

		$this->dbmaster->from("street");
		$q = $this->dbmaster->get();
		$rows = $q->result();
		$total = count($rows);
		for ($x = 0; $x < $total; $x++) {
			$street_name = str_replace(",", "", $rows[$x]->street_name);
			$feature[$x] = $street_name;
		}

		//print_r($feature);exit();
		$result = $feature;

		return $result;
	}
}
