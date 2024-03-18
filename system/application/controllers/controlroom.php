<?php
include "base.php";

class controlroom extends Base
{

    function __construct()
    {
        parent::Base();
        // $this->load->helper('common_helper');
        // $this->load->helper('email');
        // $this->load->library('email');
        $this->load->model("dashboardmodel");
        $this->load->model("m_production");
        // $this->load->model("log_model");
        // $this->load->helper('common');
    }

    function index()
    {
        $this->load->model('dashboardmodel');
        $data['alarm_data'] = $this->dashboardmodel->get_alarm_data();
        $this->room();
    }

    function room()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();

        $this->params["rcompany"]       = $rows_company;
        $this->params["rlocation"]       = $this->get_location();
        $this->params['code_view_menu'] = "dashboard";

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
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

			$html                    = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
		} else {

			$violation_board_company = $this->getViolationBoard_byCompany($userid, $company, $violation, $periode, $startdate, $enddate);
			$violation_board_all_bytype = $this->getAllViolationBoard_byType($userid, $company, $violation, $periode, $startdate, $enddate);

			$this->params["content_selected_violation"]    = $violation_board_company;
			$this->params["content_all_violation_bytype"]    = $violation_board_all_bytype;

			$html             = $this->load->view('newdashboard/hse/v_dashboard_hse_result_new', $this->params, true);
		}

		$callback["html"]        = $html;

		echo json_encode($callback);
    }

	function violation()
	{

		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";

		// $companydata = $this->getcompany_bycreator(4408);
		$companydata = $this->get_company();
		// $vehicledata = $this->getAllVehicle_bycreator(4408);
		$vehicledata = $this->get_vehicle();
		$rviolation = $this->getViolation();

		$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY); # querry url ci 2
		parse_str($queryString, $_GET); # querry String url ci 2

		$this->params['code_view_menu'] = "dashboard";

		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["onload"]         = 1;
		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/hse/v_daily_violation', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}	


    function search_violation2()
	{
		ini_set('memory_limit', "5G");
		ini_set('max_execution_time', 300); // 5 minutes

		$company = isset($_POST["company"]) ? $_POST["company"] : "all";
		$violation = isset($_POST["violation"]) ? $_POST["violation"] : "all";
		$vehicle = isset($_POST["vehicle"]) ? $_POST["vehicle"] : "all";
		$periode = isset($_POST["periode"]) ? $_POST["periode"] : "today";
		$sdate = isset($_POST["sdate"]) ? $_POST["sdate"] : "";
		$edate = isset($_POST["edate"]) ? $_POST["edate"] : "";

			$year = date("Y");
			$mont = date("m");
			$nowday = date("d");
			$err = false;
			$msg = '';

			if ($periode == "today") {
				$sdate = date("Y-m-d 00:00:00");
				$edate = date("Y-m-d H:i:s");
				$datein = date("d-m-Y", strtotime($sdate));
			} elseif ($periode == "yesterday") {
				$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
				$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
				$datein = date("d-m-Y", strtotime("yesterday"));
			} elseif ($periode == "last7") {
				$firstday = $nowday - 7;
				if ($nowday <= 7) {
					$firstday = 1;
				}
				$sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-" . $firstday));
				$edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));
				$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
			} elseif ($periode == "this_month") {
				$sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-1"));
				$edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));
				$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
			} elseif ($periode == "custom") {
				$sdate = $sdate;
				$edate = $edate;
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
					$dbtable = "alarm_evidence_januari_" . $year;
					break;
				case "February":
					$dbtable = "alarm_evidence_februari_" . $year;
					break;
				case "March":
					$dbtable = "alarm_evidence_maret_" . $year;
					break;
				case "April":
					$dbtable = "alarm_evidence_april_" . $year;
					break;
				case "May":
					$dbtable = "alarm_evidence_mei_" . $year;
					break;
				case "June":
					$dbtable = "alarm_evidence_juni_" . $year;
					break;
				case "July":
					$dbtable = "alarm_evidence_juli_" . $year;
					break;
				case "August":
					$dbtable = "alarm_evidence_agustus_" . $year;
					break;
				case "September":
					$dbtable = "alarm_evidence_september_" . $year;
					break;
				case "October":
					$dbtable = "alarm_evidence_oktober_" . $year;
					break;
				case "November":
					$dbtable = "alarm_evidence_november_" . $year;
					break;
				case "December":
					$dbtable = "alarm_evidence_desember_" . $year;
					break;
			}

			$this->dbts = $this->load->database("tensor_report", true);
			$this->dbts->distinct();
			$this->dbts->select("DATE_FORMAT(alarm_report_start_time, '%d %M %Y') as date",  FALSE );
			if ($company != "" && $company != "all") {
				$this->dbts->where("alarm_report_vehicle_company", $company);
			}
			if ($violation != "" && $violation != "all") {
				$this->dbts->where("alarm_report_violation", $violation);
			}
			if ($vehicle != "" && $vehicle != "all") {
				$this->dbts->where("alarm_report_vehicle", $vehicle);
			}
			$this->dbts->where("alarm_report_start_time >=", $sdate);
			$this->dbts->where("alarm_report_start_time <=", $edate);
			$this->dbts->from($dbtable);
			$query = $this->dbts->get();
			$list_periode = $query->result();
			
			$list_alarm_true = array();
			$list_alarm_false = array();
			$tempPeriode = array();

			foreach($list_periode as $item){
				$total_true_alarms = 0;
				$this->dbts->where("alarm_report_statusintervention_cr", "1");
				$this->dbts->where("DATE_FORMAT(alarm_report_start_time, '%d %M %Y') = ", @$item->date);
				$this->dbts->from($dbtable);
				$total_true_alarms = $this->dbts->count_all_results();
				array_push($list_alarm_true, $total_true_alarms);
				
				$total_false_alarms = 0;
				$this->dbts->where("alarm_report_statusintervention_cr", "0");
				$this->dbts->where("DATE_FORMAT(alarm_report_start_time, '%d %M %Y') = ", @$item->date);
				$this->dbts->from($dbtable);
				$total_false_alarms = $this->dbts->count_all_results();
				array_push($list_alarm_false, $total_false_alarms);

				array_push($tempPeriode, @$item->date);
			}

			$callback = array(
				"list_periode" => $tempPeriode,
				"list_alarm_true" => array_map('intval', $list_alarm_true), //ubah array string menjadi array int
				"list_alarm_false" => array_map('intval', $list_alarm_false), //ubah array string menjadi array int
			);

			$callback['error'] = false;
			$callback['message'] = "Succesfully get data.";

			echo json_encode($callback);
			return;
	}

	function month()
	{

		if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();

        $this->params["rcompany"]       = $rows_company;
        $this->params["rlocation"]      = $this->get_location();

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }

	}

	function searchmonth()
	{
		ini_set('display_errors', 1);
		ini_set('memory_limit', '2G');
		$this->params['code_view_menu'] = "dashboard";

		$userid = 4408;
		$startdate  = $this->input->post("startdate");
		$enddate    = $this->input->post("enddate");
		$periode    = $this->input->post("periode");

		$company = $this->input->post("contractor");
		$error = "";


		/* if($company != "all"){

			$callback['error'] = true;
			$callback['message'] = "Data Per Contractor belum tersedia!";

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


		$sdate_show = date("d-m-Y", strtotime($sdate));
		$edate_show = date("d-m-Y", strtotime($edate));
		$periode_show = "PERIODE: ".$sdate_show." to ".$edate_show;
		$periode_show_percompany = $periode_show;

		//print_r($sdate." ".$edate);exit();
		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));

		if($m2 != $m1){

			$callback['error'] = true;
			$callback['message'] = "Periode dapat dipilih dalam bulan yang sama!";

			echo json_encode($callback);
			return;
		}

		if($year != $year2){

			$callback['error'] = true;
			$callback['message'] = "Periode dapat dipilih dalam tahun yang sama!";

			echo json_encode($callback);
			return;
		}

		$company_name = $this->getcompanyname_byID($company);
		$total_unit = $this->gettotalvehicle_bycompany($company);

		//exit();
		/* $this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("hour_name,hour_time");
		if($shift != "all"){
			$this->dbts->where("hour_shift", $shift);
		}
        $this->dbts->where("hour_flag", 0);
        $result = $this->dbts->get("ts_hour_shift");
        $datahour = $result->result_array(); */



		$sdate_tgl = date('Y-m-d', strtotime($sdate));
		$edate_tgl = date('Y-m-d', strtotime($edate));
		$month = date("m", strtotime($sdate_tgl));
		$year = date("Y", strtotime($sdate_tgl));
		$begin = new DateTime($sdate_tgl);
		$end = new DateTime($edate_tgl);

		//config monthly report
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->order_by("monthly_date","asc");
		$this->dbts->select("monthly_date");
		$this->dbts->where("monthly_date >=", $sdate_tgl);
		$this->dbts->where("monthly_date <=", $edate_tgl);
        $resultdate = $this->dbts->get("ts_config_monthly_report");
        $datadate = $resultdate->result();


		//print_r(print_r($data_fix));exit();

		$this->dbts = $this->load->database("webtracking_ts", true);
		//$this->dbts->select("hour_name,hour_time");
		if($shift != "all"){
			$this->dbts->where("hour_shift", $shift);
		}
        $this->dbts->where("hour_flag", 0);
		$this->dbts->where("hour_month",$month);
		$this->dbts->where("hour_year",$year);
		$this->dbts->where("hour_company",$company);
        $result = $this->dbts->get("ts_location_hour_summary");
        $datahour = $result->result();

		//print_r($datahour);exit();

		$this->params["data"]  = $datahour;
		$this->params["startdate"]  = $sdate_show;
		$this->params["enddate"]  = $edate_show;

		$this->params["sdate"]  = $sdate;
		$this->params["edate"]  = $edate;
		$this->params["company"]  = $company;


		$this->params["datadate"]  = $datadate;
		$this->params["company_name"]  = $company_name;
		$this->params["total_unit"]  = $total_unit;

		$html                    = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
		$callback["html"]        = $html;

		echo json_encode($callback);
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
			$data2 = $this->getOverspeed2($dboverspeed, $company, "all", $sdate, $edate);
		} else {
			if ($violation == "all") {
				$data2 = $this->getOverspeed2($dboverspeed, $company, "all", $sdate, $edate);
				$data = $this->getSecurityEvidence($dbtable, $company, "all", $violation, $dataalarmtype, $sdate, $edate);
			} else {
				$data = $this->getSecurityEvidence($dbtable, $company, "all", $violation, $dataalarmtype, $sdate, $edate);
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

		$this->params['data'] = $data_fix;
		$this->params['sdate'] = $start_date;
		$this->params['edate'] = $end_date;
		$this->params['month'] = $monthprm;
		$this->params['year'] = $year;
		$this->params['company'] = $company_name;
		$this->params['db_table'] = $dbtable;
		$html                    = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
		$callback['error'] = false;
		$callback["html"]        = $html;
		echo json_encode($callback);
		return;
	}


    function hourdev1()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();

        $this->params["rcompany"]       = $rows_company;
        $this->params["rlocation"]      = $this->get_location();

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_dev_truck_hour_backup1', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    // USER FUNCTION START
    function hourv1()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;
        $rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

        $rows_company                   = $this->get_company();
        // $datastatus                     = explode("|", $rstatus);
        // $this->params['total_online']   = $datastatus[0] + $datastatus[1]; //p + K
        // $this->params['total_vehicle']  = $datastatus[3];
        // $this->params['total_offline']  = $datastatus[2];
        $this->params["rcompany"]       = $rows_company;

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }


    function searchv1()
    {
        $company = $this->input->post('company');
        $datein = $this->input->post('date');
        $shift = $this->input->post('shift');
        $date = date("Y-m-d", strtotime($datein));
        $lastdate = date("Y-m-t", strtotime($datein));
        $year = date("Y", strtotime($datein));
        $month = date("m", strtotime($datein));
        $day = date('d', strtotime($datein));
        $day++;
        $jmlday = strlen($day);
        if ($jmlday == 1) {
            $day = "0" . $day;
        }
        $next = $year . "-" . $month . "-" . $day;

        if ($next > $lastdate) {
            if ($month == 12) {
                $y = $year + 1;
                $next = $y . "-01-01";
            } else {
                $m = $month + 1;
                $jmlmonth = strlen($m);
                if ($jmlmonth == 1) {
                    $m = "0" . $m;
                }
                $next = $year . "-" . $m . "-01";
            }
        }
        $arraydate = array("date" => $date, "next date" => $next, "last date" => $lastdate);

        $this->db->select("vehicle_name,vehicle_no,company_name");
        // $this->db->group_by("vehicle_company");
        $this->db->order_by("company_name", "asc");
        $this->db->where("vehicle_status <>", 3);

        if ($company != 0) {
            $this->db->where("vehicle_company", $company);
        }
        $this->db->join("company", "vehicle_company = company_id", "left");
        $qd = $this->db->get("vehicle");
        $total_unit = $qd->num_rows();
        $rd = $qd->result();
        $total_unit_percontractor = array();
        if ($company == 0) {
            for ($x = 0; $x < $total_unit; $x++) {
                if ($rd[$x]->company_name != null) {
                    if (!isset($total_unit_percontractor[$rd[$x]->company_name])) {
                        $total_unit_percontractor[$rd[$x]->company_name] = 1;
                    } else {
                        $jml = (int)$total_unit_percontractor[$rd[$x]->company_name] + 1;
                        $total_unit_percontractor[$rd[$x]->company_name] = $jml;
                    }
                }
            }
        }


        // $dateym = date('Y-m-', $date);
        // $tomorrow = date('d', $date) + 1;
        // $nextday = $dateym . $tomorrow;
        $this->dbts = $this->load->database("webtracking_ts", true);
        $this->dbts->select("*");
        if ($shift == 1) {
            $shift = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17");
            $this->dbts->where("autocheck_date", $date);
            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }
            $this->dbts->where_in("autocheck_hour", $shift);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            $shift1 = array("18", "19", "20", "21", "22", "23");
            $shift2 = array("00", "01", "02", "03", "04", "05");
            $this->dbts->where("autocheck_date", $date);
            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }
            $this->dbts->where_in("autocheck_hour", $shift1);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->where("autocheck_date", $next);
            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }
            $this->dbts->where_in("autocheck_hour", $shift2);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {
            $shift1 = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23");
            $shift2 = array("00", "01", "02", "03", "04", "05");
            $this->dbts->where("autocheck_date", $date);
            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }
            $this->dbts->where_in("autocheck_hour", $shift1);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->where("autocheck_date", $next);
            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }
            $this->dbts->where_in("autocheck_hour", $shift2);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        }
        // $vehicle = $this->dashboardmodel->getvehicle_report();
        // $totalvehicle = array();
        $company = array();
        $hour = array();
        $result = array();
        $c = array();
        // $cid = array();
        $h = array();
        if ($nr > 0) {
            for ($i = 0; $i < $nr; $i++) {
                if (!isset($company[$data[$i]['autocheck_company_name']])) {
                    $company[$data[$i]['autocheck_company_name']] = $data[$i]['autocheck_company_name'];
                    $c[] = $data[$i]['autocheck_company_name'];
                }
                if (!isset($hour[$data[$i]['autocheck_hour']])) {
                    $hour[$data[$i]['autocheck_hour']] = $data[$i]['autocheck_hour'];
                    $h[] = $data[$i]['autocheck_hour'];
                }
                $result[$data[$i]['autocheck_hour']][$data[$i]['autocheck_company_name']] = $data[$i]['autocheck_total_duty'] . " (" . $data[$i]['autocheck_total_duty_persen'] . "%)";
            }
        }
        // for ($i = 0; $i < $clength; $i++) {
        //     for($j=0; $j<count($vehicle); $i++){
        //         if (!isset($totalvehicle[$cid[$i]]));
        //     }
        // }

        // $total_unit_percontractor = $c;


        echo json_encode(array("code" => 200, "msg" => "success", "data" => $result, "company" => $c, "hour" => $h, "total" => $nr, "clength" => count($company), "hlength" => count($hour), "date" => $arraydate, "total_unit" => $total_unit, "vehicle" => $rd, "total_unit_contractor" => $total_unit_percontractor));
    }

    function pool()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;
        $rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

        $rows_company                   = $this->get_company();
        // $datastatus                     = explode("|", $rstatus);
        // $this->params['total_online']   = $datastatus[0] + $datastatus[1]; //p + K
        // $this->params['total_vehicle']  = $datastatus[3];
        // $this->params['total_offline']  = $datastatus[2];
        $this->params["rcompany"]       = $rows_company;

        $this->params['code_view_menu'] = "dashboard";

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_pool', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function search_on_pool()
    {
        $company = $this->input->post('company');
        $datein = $this->input->post('date');
        $shift = $this->input->post('shift');
        $date = date("Y-m-d", strtotime($datein));
        $lastdate = date("Y-m-t", strtotime($datein));
        $year = date("Y", strtotime($datein));
        $month = date("m", strtotime($datein));
        $day = date('d', strtotime($datein));
        $day++;
        $jmlday = strlen($day);
        if ($jmlday == 1) {
            $day = "0" . $day;
        }
        $next = $year . "-" . $month . "-" . $day;

        if ($next > $lastdate) {
            if ($month == 12) {
                $y = $year + 1;
                $next = $y . "-01-01";
            } else {
                $m = $month + 1;
                $jmlmonth = strlen($m);
                if ($jmlmonth == 1) {
                    $m = "0" . $m;
                }
                $next = $year . "-" . $m . "-01";
            }
        }
        $arraydate = array("date" => $date, "next date" => $next, "last date" => $lastdate);

        $this->db->select("vehicle_name,vehicle_no,company_name");
        // $this->db->group_by("vehicle_company");
        $this->db->order_by("company_name", "asc");
        $this->db->where("vehicle_status <>", 3);
        if ($company != 0) {
            $this->db->where("vehicle_company", $company);
        }
        $this->db->join("company", "vehicle_company = company_id", "left");
        $qd = $this->db->get("vehicle");
        $total_unit = $qd->num_rows();
        $rd = $qd->result();
        $total_unit_percontractor = array();

        if ($company == 0) {
            for ($x = 0; $x < $total_unit; $x++) {
                if ($rd[$x]->company_name != null) {
                    if (!isset($total_unit_percontractor[$rd[$x]->company_name])) {
                        $total_unit_percontractor[$rd[$x]->company_name] = 1;
                    } else {
                        $jml = (int)$total_unit_percontractor[$rd[$x]->company_name] + 1;
                        $total_unit_percontractor[$rd[$x]->company_name] = $jml;
                    }
                }
            }
        }


        // $dateym = date('Y-m-', $date);
        // $tomorrow = date('d', $date) + 1;
        // $nextday = $dateym . $tomorrow;
        $this->dbts = $this->load->database("webtracking_ts", true);
        $this->dbts->select("*");
        if ($shift == 1) {
            $shift = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17");
            $this->dbts->where("autocheck_date", $date);

            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }

            $this->dbts->where_in("autocheck_hour", $shift);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour_pool");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            $shift1 = array("18", "19", "20", "21", "22", "23");
            $shift2 = array("00", "01", "02", "03", "04", "05");
            $this->dbts->where("autocheck_date", $date);

            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }

            $this->dbts->where_in("autocheck_hour", $shift1);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour_pool");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->where("autocheck_date", $next);

            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }

            $this->dbts->where_in("autocheck_hour", $shift2);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour_pool");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {
            $shift1 = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23");
            $shift2 = array("00", "01", "02", "03", "04", "05");
            $this->dbts->where("autocheck_date", $date);

            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }

            $this->dbts->where_in("autocheck_hour", $shift1);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour_pool");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->where("autocheck_date", $next);

            if ($company != 0) {
                $this->dbts->where("autocheck_company", $company);
            }

            $this->dbts->where_in("autocheck_hour", $shift2);
            $this->dbts->order_by("autocheck_hour", "asc");
            $this->dbts->order_by("autocheck_company_name", "asc");
            $result = $this->dbts->get("ts_autocheck_hour_pool");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        }
        // $vehicle = $this->dashboardmodel->getvehicle_report();
        // $totalvehicle = array();
        $company = array();
        $hour = array();
        $result = array();
        $c = array();
        // $cid = array();
        $h = array();
        if ($nr > 0) {
            for ($i = 0; $i < $nr; $i++) {
                if (!isset($company[$data[$i]['autocheck_company_name']])) {
                    $company[$data[$i]['autocheck_company_name']] = $data[$i]['autocheck_company_name'];
                    $c[] = $data[$i]['autocheck_company_name'];
                }
                if (!isset($hour[$data[$i]['autocheck_hour']])) {
                    $hour[$data[$i]['autocheck_hour']] = $data[$i]['autocheck_hour'];
                    $h[] = $data[$i]['autocheck_hour'];
                }
                $result[$data[$i]['autocheck_hour']][$data[$i]['autocheck_company_name']] = $data[$i]['autocheck_total_parkir'] . " (" . $data[$i]['autocheck_total_parkir_persen'] . "%)";
            }
        }

        echo json_encode(array("code" => 200, "msg" => "success", "data" => $result, "company" => $c, "hour" => $h, "total" => $nr, "clength" => count($company), "hlength" => count($hour), "date" => $arraydate, "total_unit" => $total_unit, "vehicle" => $rd, "total_unit_contractor" => $total_unit_percontractor));
    }

    function summarynew()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();

        $this->params["rcompany"]       = $rows_company;
        $this->params["rlocation"]      = $this->get_location();
        $this->params['code_view_menu'] = "dashboard";

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month_summary', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month_summary', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month_summary', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month_summary', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month_summary', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/truckhour/v_truck_month_summary', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/controlroom/v_controlroom', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function searchmonthsummarynew()
  	{
  		ini_set('display_errors', 1);
  		ini_set('memory_limit', '2G');
  		$this->params['code_view_menu'] = "report";

  		$userid    = 4408;
  		$startdate = $this->input->post("startdate");
  		$enddate   = $this->input->post("enddate");
  		$periode   = $this->input->post("periode");

  		// $shift     = $this->input->post("shift"); // AKTIFKAN SESUAI INSTRUKSI
      $shift     = 3;
  		$company   = $this->input->post("contractor");
  		$error     = "";

  		/* if($company != "all"){

  			$callback['error'] = true;
  			$callback['message'] = "Data Per Contractor belum tersedia!";

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

  		if($periode == "custom"){
        if ($shift == 1) {
          $sdate = date("Y-m-d H:i:s", strtotime($startdate." 06:00:00"));
    			$edate = date("Y-m-d H:i:s", strtotime($enddate." 18:00:00"));
        }elseif ($shift == 2) {
          $sdate = date("Y-m-d H:i:s", strtotime($startdate." 18:00:00"));
    			$edate = date("Y-m-d H:i:s", strtotime($enddate." 06:00:00"."+ 1 Day"));
        }else {
          // $sdate = date("Y-m-d H:i:s", strtotime($startdate." 00:00:00"));
    			// $edate = date("Y-m-d H:i:s", strtotime($enddate." 24:00:00"."+ 1 Day"));
          $sdate = date("Y-m-d H:i:s", strtotime($startdate." 06:00:00"));
    			$edate = date("Y-m-d H:i:s", strtotime($enddate." 24:00:00"."+ 1 Day"));
        }
  		}else if($periode == "yesterday"){
        if ($shift == 1) {
          $sdate = date("Y-m-d 06:00:00", strtotime("yesterday"));
          $edate = date("Y-m-d 18:00:00", strtotime("yesterday"));
        }elseif ($shift == 2) {
          $sdate = date("Y-m-d 18:00:00", strtotime("yesterday"));
          $edate = date("Y-m-d 06:00:00");
        }else {
          $sdate = date("Y-m-d 06:00:00", strtotime("yesterday"));
          $edate = date("Y-m-d 06:00:00");
        }

  		}else if($periode == "last7"){
  			$nowday = $nowday - 1;
  			$firstday = $nowday - 7;
  			if($nowday <= 7){
  				$firstday = 1;
  			}

        // echo "<pre>";
        // var_dump($nowday.'-'.$firstday);die();
        // echo "<pre>";

  			/*if($firstday > $nowday){
  				$firstday = 1;
  			}*/

        if ($shift == 1) {
          $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."06:00:00"));
          $edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."18:00:00"));
        }elseif ($shift == 2) {
          $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."18:00:00"));
    			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."06:00:00"));
        }else {
          $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."06:00:00"));
    			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."24:00:00"."+ 1 Day"));
        }

  		}
  		else if($periode == "last30"){
  			$firstday = "1";
        if ($shift == 1) {
          $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."06:00:00"));
    			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."18:00:00"));
        }elseif ($shift == 2) {
          $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."18:00:00"));
          $edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."06:00:00"));
        }else {
          $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
    			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."24:00:00"."+ 1 Day"));
        }


  		}
  		else{
  			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
  			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
  		}

      // print_r($sdate." ".$edate);exit();

      // KONDISI SHIFT
      // if ($shift == 1) {
      //   $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."06:00:00"));
  		// 	$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."18:00:00"));
      // }elseif ($shift == 2) {
      //   $sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."18:00:00"));
      //   $edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."06:00:00"));
      // }

  		$sdate_show              = date("d-m-Y", strtotime($sdate));
  		$edate_show              = date("d-m-Y", strtotime($edate."-2 Days"));
  		$periode_show            = "PERIODE: ".$sdate_show." to ".$edate_show;
  		$periode_show_percompany = $periode_show;

  		//print_r($sdate." ".$edate);exit();
  		$m1                      = date("F", strtotime($sdate));
  		$m2                      = date("F", strtotime($edate));
  		$year                    = date("Y", strtotime($sdate));
  		$year2                   = date("Y", strtotime($edate));

  		$dbtable   = "";
  		$report    = "location_hour_";

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

  		// if($m2 != $m1){
      //
  		// 	$callback['error'] = true;
  		// 	$callback['message'] = "Periode dapat dipilih dalam bulan yang sama!";
      //
  		// 	echo json_encode($callback);
  		// 	return;
  		// }

  		if($year != $year2){

  			$callback['error'] = true;
  			$callback['message'] = "Periode dapat dipilih dalam tahun yang sama!";

  			echo json_encode($callback);
  			return;
  		}

  		$company_name = $this->getcompanyname_byID($company);
  		$total_unit   = $this->gettotalvehicle_bycompany($company);

      $data_location_hour  = $this->getLocationHour($dbtable, $company, $sdate, $edate);
      $lochour_groupbydate = $this->getLocationHour_groupby_date($dbtable, $company, $sdate, $edate);
      $totaldate           = sizeof($lochour_groupbydate);
        $totalhari = array();
          for ($i=0; $i < $totaldate; $i++) {
            $totalhari[] = date("d", strtotime($lochour_groupbydate[$i]['location_report_gps_date']));
          }
      $lochour_groupbyhour = $this->getLocationHour_groupby_hour($dbtable, $company, $sdate, $edate);
      $totalhour           = sizeof($lochour_groupbyhour);
      $totaljam = array();
        for ($i=0; $i < $totalhour; $i++) {
          $totaljam[] = date("H", strtotime($lochour_groupbyhour[$i]['location_report_gps_hour']));
        }
      $data_olah           = array();

      // echo "<pre>";
      // var_dump($totaljam);die();
      // echo "<pre>";

        if (sizeof($data_location_hour) > 0) {
          for ($i=0; $i < sizeof($data_location_hour); $i++) {
            // for ($i=0; $i < 4; $i++) {
              $shift1_1  = strtotime("06:00:00");
              $shift1_2  = strtotime("18:00:00");
              $shift2_1  = strtotime("18:00:00");
              $shift2_2  = strtotime("06:00:00");
              $shift_fix = "";

              $gps_time           = date("H:i:s", strtotime($data_location_hour[$i]['location_report_gps_time']));
              $gps_time_strtotime = strtotime($gps_time);

              if ($gps_time_strtotime > $shift1_1 && $gps_time_strtotime < $shift1_2) {
                $shift_fix = 1;
              }else {
                $shift_fix = 2;
              }

              array_push($data_olah, array(
                "location_report_vehicle_device" => $data_location_hour[$i]['location_report_vehicle_device'],
                "location_report_vehicle_no"     => $data_location_hour[$i]['location_report_vehicle_no'],
                "location_report_shift_format"   => $shift_fix,
                "location_report_gps_date"       => $data_location_hour[$i]['location_report_gps_date'],
                "location_report_gps_hour"       => $data_location_hour[$i]['location_report_gps_hour'],
                "location_report_gps_time"       => $data_location_hour[$i]['location_report_gps_time'],
                "location_report_gps_time2"      => $gps_time,
              ));
            }
          }

          $datafix = array();
          if (sizeof($data_olah) > 0) {
            if ($shift == 1) {
              for ($j=0; $j < sizeof($data_olah); $j++) {
                $shift_search = $data_olah[$j]['location_report_shift_format'];
                  if ($shift_search == 1) {
                    array_push($datafix, array(
                      "location_report_vehicle_device" => $data_olah[$j]['location_report_vehicle_device'],
                      "location_report_vehicle_no"     => $data_olah[$j]['location_report_vehicle_no'],
                      "location_report_shift_format"   => $data_olah[$j]['location_report_shift_format'],
                      "location_report_gps_date"       => $data_olah[$j]['location_report_gps_date'],
                      "location_report_gps_hour"       => $data_olah[$j]['location_report_gps_hour'],
                      "location_report_gps_time"       => $data_olah[$j]['location_report_gps_time'],
                      "location_report_gps_time2"      => $data_olah[$j]['location_report_gps_time2'],
                    ));
                  }
              }
            }elseif ($shift == 2) {
              for ($j=0; $j < sizeof($data_olah); $j++) {
                $shift_search = $data_olah[$j]['location_report_shift_format'];
                  if ($shift_search == 2) {
                    array_push($datafix, array(
                      "location_report_vehicle_device" => $data_olah[$j]['location_report_vehicle_device'],
                      "location_report_vehicle_no"     => $data_olah[$j]['location_report_vehicle_no'],
                      "location_report_shift_format"   => $data_olah[$j]['location_report_shift_format'],
                      "location_report_gps_date"       => $data_olah[$j]['location_report_gps_date'],
                      "location_report_gps_hour"       => $data_olah[$j]['location_report_gps_hour'],
                      "location_report_gps_time"       => $data_olah[$j]['location_report_gps_time'],
                      "location_report_gps_time2"      => $data_olah[$j]['location_report_gps_time2'],
                    ));
                  }
              }
            }else {
              $datafix = $data_olah;
            }

            // KONDISI COUNT NYA
            $totaldatafix      = sizeof($datafix);
            $datatanggal       = array();
            // $dataforshow       = array();
            $counttotal        = 0;

            for ($k=0; $k < sizeof($totalhari); $k++) {
              $tanggal = $totalhari[$k];
              for ($l=0; $l < $totaldatafix; $l++) {
                $loc_report_date = date("d", strtotime($datafix[$l]['location_report_gps_date']));
                $loc_report_hour = date("H", strtotime($datafix[$l]['location_report_gps_hour']));
                if ($tanggal == $loc_report_date) {
                  for ($m=0; $m < sizeof($totaljam); $m++) {
                    $hour           = $totaljam[$m];
                    $data_array_jam = array();
                      if ($hour == $loc_report_hour) {
                        // $dataforshow[$tanggal] = array_push($data_array_jam, array(
                        //   $hour => $hour
                        // ));
                        // $counttotal += 1;
                        $dataforshow['tanggal'][$tanggal]['jam'][$hour][] = $loc_report_date.'-'.$loc_report_hour;
                      }
                  }
                }
              }
            }
          }


          sort($totalhari);
          $clength = count($totalhari);
          for($x = 0; $x < $clength; $x++) {
            $totalhari[$x];
          }
          // $totalhari_2 = array_values($totalhari);
          // $totalhari_3 = array_reverse($totalhari_1);
      // echo "<pre>";
      // var_dump($totalhari);die();
      // echo "<pre>";

      $this->params["shift"]        = $shift;
  	  $this->params["data"]         = $dataforshow;
      $this->params["datahari"]     = $totalhari;
      $this->params["datajam"]      = $totaljam;
      $this->params["totalhari"]    = sizeof($totalhari);
      $this->params["totaljam"]     = sizeof($totaljam);
  	  $this->params["startdate"]    = $sdate_show;
  	  $this->params["enddate"]      = $edate_show;

  		$this->params["sdate"]        = $sdate_show;
  		$this->params["edate"]        = $edate_show;
  		$this->params["company"]      = $company;


  		// $this->params["datadate"]  = $datadate;
  		$this->params["company_name"] = $company_name;
  		$this->params["total_unit"]   = $total_unit;

  		$html                         = $this->load->view('newdashboard/truckhour/v_truck_month_summary_result', $this->params, true);
  		$callback["html"]             = $html;
      	$callback["data"]             = $dataforshow;
      	$callback["datahari"]         = $totalhari;

  		echo json_encode($callback);
  	}

    function getViolation()
     {
          $this->dbalarm = $this->load->database("webtracking_ts", true);
          $this->dbalarm->select("alarmmaster_id, alarmmaster_name");
          $this->dbalarm->where("alarmmaster_status",1);
          $q        = $this->dbalarm->get("webtracking_ts_alarmmaster");
          return  $q->result_array();
     }
  

    function getLocationHour($dbtable, $company, $sdate, $edate){
      $this->dbts = $this->load->database("tensor_report", true);
      $this->dbts->where("location_report_vehicle_company", $company);
      $this->dbts->where("location_report_group", "STREET");
      $this->dbts->where("location_report_gps_time >= ", $sdate);
      $this->dbts->where("location_report_gps_time <= ", $edate);
      $this->dbts->order_by("location_report_gps_hour", "asc");
      $result = $this->dbts->get($dbtable)->result_array();
      return $result;
    }

    function getLocationHour_groupby_date($dbtable, $company, $sdate, $edate){
      $this->dbts = $this->load->database("tensor_report", true);
      $this->dbts->where("location_report_vehicle_company", $company);
      $this->dbts->where("location_report_group", "STREET");
      $this->dbts->where("location_report_gps_time >= ", $sdate);
      $this->dbts->where("location_report_gps_time <= ", $edate);
      $this->dbts->order_by("location_report_gps_hour", "asc");
      $this->dbts->group_by("location_report_gps_date");
      $result = $this->dbts->get($dbtable)->result_array();
      return $result;
    }

    function getLocationHour_groupby_hour($dbtable, $company, $sdate, $edate){
      $this->dbts = $this->load->database("tensor_report", true);
      $this->dbts->where("location_report_vehicle_company", $company);
      $this->dbts->where("location_report_group", "STREET");
      $this->dbts->where("location_report_gps_time >= ", $sdate);
      $this->dbts->where("location_report_gps_time <= ", $edate);
      $this->dbts->order_by("location_report_gps_hour", "asc");
      $this->dbts->group_by("location_report_gps_hour");
      $result = $this->dbts->get($dbtable)->result_array();
      return $result;
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
        } elseif ($privilegecode == 4) {
            $this->db->where("company_created_by", 4408);
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

    function get_company_pool()
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

    
	function getSecurityEvidence($dbtable, $company, $vehicle, $violation, $dataalarmtype, $sdate, $edate)
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


    function get_location()
    {
        $this->db->select("street_alias");
        $this->db->order_by("street_alias", "asc");
        $this->db->where_in("street_type", array(3, 4));
        $q = $this->db->get("street");

        return $q->result();
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

	function get_periode_date($company,$sdate,$edate)
    {
		$sdate_tgl = date('Y-m-d', strtotime($sdate));
		$edate_tgl = date('Y-m-d', strtotime($edate));

		$begin = new DateTime($sdate_tgl);
		$end = new DateTime($edate_tgl);

		for($i = $begin; $i <= $end; $i->modify('+1 day')){
			$date_loop =  $i->format("Y-m-d");

			print_r($date_loop."===");
			$total = $this->get_vehile_opr_bydate($company,$begin,$end);
			print_r($total."===");

		}


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

	function get_totalvehicle_opr($company,$date,$hour)
	{

		$group_selected = array('STREET','ROM','PORT');

		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("location_report_id,location_report_vehicle_no");
        $this->dbts->order_by("location_report_id", "asc");
		$this->dbts->group_by("location_report_vehicle_no");
        $this->dbts->where("location_report_vehicle_company", $company);
		$this->dbts->where("location_report_gps_date", $date);
		$this->dbts->where("location_report_gps_hour", $hour);
		$this->dbts->where_in("location_report_group", $group_selected);
        $q = $this->dbts->get("ts_location_hour");
		$row = $q->result();

		$total = count($row);

		//print_r($row);exit();

		$this->dbts->close();
		$this->dbts->cache_delete_all();

        return $total;


	}

	function getcompanyname_byID($id)
	{
		$name = "-";
		$this->db = $this->load->database("default", true);
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
		$this->db->close();
		$this->db->cache_delete_all();
		return $name;
	}

	function gettotalvehicle_bycompany($id)
	{
		$total = 0;
		$this->db = $this->load->database("default", true);
		$this->db->select("vehicle_id");
		$this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id ", 4408);
		$this->db->where("vehicle_company ", $id);
		$q = $this->db->get("vehicle");
		$row = $q->result();

		$total = count($row);

		$this->db->close();
		$this->db->cache_delete_all();
		return $total;
	}

    function getdatachart()
    { 
        $data = array(
            "category" => array(),
            "alarmDataTrue" => array(),
            "alarmDataFalse" => array()
        );
        $category = $this->dashboardmodel->get_alarm_time_data();

        if ($category) {
            $alarmDataTrue = array();
            $alarmDataFalse = array();
            foreach ($category as $item) {
                $dataTrue = $this->dashboardmodel->get_alarm_data($item->start_time, 1);
                $dataTrue = ($dataTrue !== null) ? $dataTrue : 0;

                $dataFalse = $this->dashboardmodel->get_alarm_data($item->start_time, 0);
                $dataFalse = ($dataFalse !== null) ? $dataFalse : 0;

                array_push($alarmDataTrue, $dataTrue);
                array_push($alarmDataFalse, $dataFalse);
            }

            $data["category"] = $category;
            $data["alarmDataTrue"] = $alarmDataTrue;
            $data["alarmDataFalse"] = $alarmDataFalse;
        }

        return $data;
    }
}
    

