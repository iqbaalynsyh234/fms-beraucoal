<?php
include "base.php";

class Ritase extends Base
{

	function summary()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$privilegecode = $this->sess->user_id_role;
		$rows_company                   = $this->get_company();
		$this->params["rows_company"]       = $rows_company;

		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["onload"]         = 1;
		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase_summary', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase_summary', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase_summary', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase_summary', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase_summary', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase_summary', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase_summary', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase_summary', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function search_summary() //source: webtracking_ts > kepmen_source
	{
		$company = $this->input->post("company");
		$periode = $this->input->post("periode");
		// $nowdate = date('Y-m-d');
		// $year = date('Y');
		// $mont = date('m');
		// $nowday = date('d');
		$nowdate = date("Y-m-d");
		$yesterday = date("Y-m-d", strtotime("yesterday"));
		// $lastdate = date('t');
		$err = false;
		if ($periode == "today") {
			$sdate = date("Y-m-d");
			$edate = $sdate;
			$datein = date("d-m-Y", strtotime($sdate));
		} else if ($periode == "yesterday") {
			$sdate = $yesterday;
			$edate = $yesterday;
			$datein = date("d-m-Y", strtotime("yesterday"));
		} else if ($periode == "last7") {
			// $nowday = $nowday - 1;
			// $firstday = $nowday - 7;
			// if ($nowday <= 7) {
			// 	$firstday = 1;
			// }
			$sdate = date("Y-m-d", strtotime(" -7 day", strtotime($yesterday)));
			$edate = date("Y-m-d", strtotime("yesterday"));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
		} else if ($periode == "last30") {
			$sdate = date("Y-m-d", strtotime(" -30 day", strtotime($yesterday)));
			$edate = $yesterday;

			// $sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-1"));
			// $edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));

			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
		} else if ($periode == "custom") {
			$sdate = $this->input->post("sdate");
			$edate = $this->input->post("edate");
			$sdate = date("Y-m-d", strtotime($sdate));
			$edate = date("Y-m-d", strtotime($edate));
			$diff = strtotime($nowdate) - strtotime($edate);
			if ($diff <= 0) {
				$edate = $yesterday;
			}
			$diff = strtotime($nowdate) - strtotime($sdate);
			if ($diff <= 0) {
				$sdate = $yesterday;
			}
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
			$diff = strtotime($edate) - strtotime($sdate);
			if ($diff < 0) {
				$err = true;
				$msg = "Date is not correct!";
			}
			$diff = strtotime($nowdate) - strtotime($sdate);
			if ($diff < 0) {
				$err = true;
				$msg = "Date is not correct!";
			}
			if ($company == "all") {
				$diff = strtotime($edate) - strtotime($sdate);
				// if ($diff > 604800) { //7 hari
				// if ($diff > 1209600) { //14 hari
				if ($diff > 1382400) { //16 hari
					// if ($diff > 1814400) { //21 hari
					// if ($diff > 2419200) { //28 hari
					$err = true;
					$msg = "Maximum date range for all contractors is 16 days!";
				}
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
			$year = date("Y", strtotime($sdate));
		}

		if ($err == true) {
			$callback['error'] = true;
			$callback['message'] = $msg;
			echo json_encode($callback);
			return;
		}

		$master_company = array();
		$rows_company                   = $this->get_company();
		for ($i = 0; $i < count($rows_company); $i++) {
			$master_company[$rows_company[$i]->company_id] = $rows_company[$i]->company_name;
		}
		$privilegecode   = $this->sess->user_id_role;
		$user_id         = $this->sess->user_id;
		$user_parent     = $this->sess->user_parent;
		$user_company    = $this->sess->user_company;
		$this->dbtrip = $this->load->database("webtracking_ts", true);
		$this->dbtrip->order_by("kepmen_date", "asc");
		$this->dbtrip->order_by("kepmen_vehicle_no", "asc");
		$this->dbtrip->order_by("kepmen_company_id", "asc");
		if ($company != 'all') {
			$this->dbtrip->where("kepmen_company_id", $company);
		}
		if ($privilegecode == 0) {
			$this->dbtrip->where("kepmen_vehicle_user_id", $user_id);
		} else if ($privilegecode == 1) {
			$this->dbtrip->where("kepmen_vehicle_user_id", $user_parent);
		} else if ($privilegecode == 2) {
			$this->dbtrip->where("kepmen_vehicle_user_id", $user_parent);
		} else if ($privilegecode == 3) {
			$this->dbtrip->where("kepmen_vehicle_user_id", $user_parent);
		} else if ($privilegecode == 4) {
			$this->dbtrip->where("kepmen_vehicle_user_id", $user_parent);
		} else if ($privilegecode == 5) {
			$this->dbtrip->where("kepmen_company_id", $user_company);
		} else if ($privilegecode == 6) {
			$this->dbtrip->where("kepmen_company_id", $user_company);
		}
		// $this->dbtrip->where("ritase_report_vehicle_id <>", 72150933); //jika pilih all bukan mobil trial
		// } else {
		// 	$this->dbtrip->where("ritase_report_vehicle_device", $vehicle);
		// }

		$this->dbtrip->where("kepmen_date >=", $sdate);
		$this->dbtrip->where("kepmen_date <=", $edate);
		// $this->dbtrip->where("ritase_report_type", $reporttype); //data fix (default) = 0
		$q = $this->dbtrip->get("ts_kepmen_source");
		// $callback['error'] = true;
		// $callback['message'] = "OK";
		// $callback['data'] = $q->result_array();
		// echo json_encode($callback);
		// return;
		$nr = $q->num_rows();
		if ($nr > 0) {
			// $rows = $q->result_array();
			// $check = array();
			// $tbl_summary = array();
			// for ($i = 0; $i < $nr; $i++) {
			// 	$date_time = $rows[$i]['ritase_report_start_time'];
			// 	$date = date("d-m-Y", strtotime($date_time));
			// 	$vehicle = $rows[$i]['ritase_report_vehicle_no'];

			// 	if (!isset($check[$date][$vehicle])) {
			// 		$check[$date][$vehicle] = 1;
			// 		$tbl_summary[$date]['1'] = 0;
			// 	} else {
			// 		$check[$date][$vehicle] += 1;
			// 		$rit = $check[$date][$vehicle];
			// 		$tbl_summary[$date][(string)$rit] = 0;
			// 	}
			// }

			// $this->params['summary'] = $tbl_summary;
			// $this->params['check'] = $check;
			$this->params['data'] = $q->result_array();
			$this->params['master_company'] = $master_company;
			$this->params['periode'] = $datein;
			// $callback['data'] = $check;
			$this->params['total_data'] = $nr;
			$html                    = $this->load->view('newdashboard/ritase/v_ritase_summary_result', $this->params, true);
			$callback['html'] = $html;
		} else {
			$callback['error'] = true;
			$callback['message'] = "Data empty.";
			echo json_encode($callback);
			return;
		}
		$callback['input'] = array(
			'company' => $company,
			'date_s' => $sdate,
			'date_e' => $edate
		);
		echo json_encode($callback);
	}

	function search_summary_backup1() //source:tensor report > ritase_bln_thn
	{
		$company = $this->input->post("company");
		$periode = $this->input->post("periode");
		// $nowdate = date('Y-m-d');
		$year = date('Y');
		$mont = date('m');
		$nowday = date('d');
		// $lastdate = date('t');
		$err = false;
		if ($periode == "today") {
			$sdate = date("Y-m-d 00:00:00");
			$edate = date("Y-m-d 23:59:59");
			$datein = date("d-m-Y", strtotime($sdate));
		} else if ($periode == "yesterday") {
			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
			$datein = date("d-m-Y", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}
			$sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-" . $firstday));
			$edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));
			$datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
		} else if ($periode == "last30") {

			// $sdate = date("Y-m-d H:i:s ", strtotime(" -30 day", strtotime($nowdate)));
			// $edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));

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
			if ($company == "all") {
				$diff = strtotime($edate) - strtotime($sdate);
				if ($diff > 604800) {
					$err = true;
					$msg = "Maximum date range for all contractors is 7 days!";
				}
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
			$year = date("Y", strtotime($sdate));
		}

		if ($err == true) {
			$callback['error'] = true;
			$callback['message'] = $msg;
			echo json_encode($callback);
			return;
		}
		$m1           = date("F", strtotime($sdate));
		$report         = "ritase_";
		switch ($m1) {
			case "January":
				$dbtable   = $report . "januari_" . $year;
				break;
			case "February":
				$dbtable   = $report . "februari_" . $year;
				break;
			case "March":
				$dbtable   = $report . "maret_" . $year;
				break;
			case "April":
				$dbtable   = $report . "april_" . $year;
				break;
			case "May":
				$dbtable   = $report . "mei_" . $year;
				break;
			case "June":
				$dbtable   = $report . "juni_" . $year;
				break;
			case "July":
				$dbtable   = $report . "juli_" . $year;
				break;
			case "August":
				$dbtable   = $report . "agustus_" . $year;
				break;
			case "September":
				$dbtable   = $report . "september_" . $year;
				break;
			case "October":
				$dbtable   = $report . "oktober_" . $year;
				break;
			case "November":
				$dbtable   = $report . "november_" . $year;
				break;
			case "December":
				$dbtable   = $report . "desember_" . $year;
				break;
		}
		$privilegecode   = $this->sess->user_id_role;
		$user_id         = $this->sess->user_id;
		$user_parent     = $this->sess->user_parent;
		$user_company    = $this->sess->user_company;
		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->order_by("ritase_report_start_time", "asc");
		$this->dbtrip->order_by("ritase_report_vehicle_no", "asc");
		$this->dbtrip->order_by("ritase_report_vehicle_company", "asc");
		if ($company != 'all') {
			$this->dbtrip->where("ritase_report_vehicle_company", $company);
		}
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
		}
		$this->dbtrip->where("ritase_report_vehicle_id <>", 72150933); //jika pilih all bukan mobil trial
		// } else {
		// 	$this->dbtrip->where("ritase_report_vehicle_device", $vehicle);
		// }

		$this->dbtrip->where("ritase_report_start_time >=", $sdate);
		$this->dbtrip->where("ritase_report_start_time <=", $edate);
		// $this->dbtrip->where("ritase_report_type", $reporttype); //data fix (default) = 0
		$q = $this->dbtrip->get($dbtable);
		$nr = $q->num_rows();
		if ($nr > 0) {
			$rows = $q->result_array();
			$check = array();
			$tbl_summary = array();
			for ($i = 0; $i < $nr; $i++) {
				$date_time = $rows[$i]['ritase_report_start_time'];
				$date = date("d-m-Y", strtotime($date_time));
				$vehicle = $rows[$i]['ritase_report_vehicle_no'];

				if (!isset($check[$date][$vehicle])) {
					$check[$date][$vehicle] = 1;
					$tbl_summary[$date]['1'] = 0;
				} else {
					$check[$date][$vehicle] += 1;
					$rit = $check[$date][$vehicle];
					$tbl_summary[$date][(string)$rit] = 0;
				}
			}

			$this->params['summary'] = $tbl_summary;
			$this->params['check'] = $check;
			$this->params['check'] = $check;
			$this->params['periode'] = $datein;
			// $callback['data'] = $check;
			// $this->params['total_data'] = $nr;
			$html                    = $this->load->view('newdashboard/ritase/v_ritase_summary_result', $this->params, true);
			$callback['html'] = $html;
		} else {
			$callback['error'] = true;
			$callback['message'] = "Data empty.";
			echo json_encode($callback);
			return;
		}
		$callback['input'] = array(
			'company' => $company,
			'date_s' => $sdate,
			'date_e' => $edate
		);
		echo json_encode($callback);
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

	function Ritase()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');

		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
	}

	function index()
	{
		if (!isset($this->sess->user_company)) {
			redirect(base_url());
		}

		$user_parent     = $this->sess->user_parent;
		$privilegecode   = $this->sess->user_id_role;

		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where("ritase_company", $this->sess->user_company);
		//$this->dbtransporter->where("ritase_status", 1);
		$q_ritase    = $this->dbtransporter->get("ritase", 10, 0);
		$rows_ritase = $q_ritase->result();
		$total       = count($rows_ritase);
		$this->dbtransporter->where("ritase_company", $this->sess->user_company);
		//$this->dbtransporter->where("ritase_status", 1);
		$qtotal    = $this->dbtransporter->get("ritase");
		$rowstotal = $qtotal->result();
		$total     = count($rowstotal);

		$config['total_rows']   = $total;

		$this->params['data']    = $rows_ritase;
		$this->params['total']   = $total;

		// GET DATA UNTUK ADD RITASE
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where("ritase_company", $this->sess->user_company);
		$this->dbtransporter->where("ritase_status", 1);
		$q_ritase = $this->dbtransporter->get("ritase");
		$rows_ritase = $q_ritase->result();

		if (count($rows_ritase) > 0) {
			foreach ($rows_ritase as $row_ritase) {
				$ritase_geofence_name[] = $row_ritase->ritase_geofence_name;
			}
		}

		$this->db->order_by("geofence_name", "asc");
		$this->db->where("geofence_status", "1");
		$this->db->where("geofence_name !=", "");
		if ($privilegecode == 1) {
			$this->db->where("geofence_user", $user_parent);
		} elseif ($privilegecode == 3) {
			$this->db->where("geofence_user", $user_parent);
		} elseif ($privilegecode == 4) {
			$this->db->where("geofence_user", $user_parent);
		} else {
			$this->db->where("geofence_user", $this->sess->user_id);
		}
		if (count($rows_ritase) > 0) {
			$this->db->where_not_in("geofence_name", $ritase_geofence_name);
		}
		$q = $this->db->get("geofence");
		$rows = $q->result();

		$this->params['dataforritase']  = $rows;
		$this->params['rows_ritase']    = $rows_ritase;
		$this->params['privilegecode']  = $privilegecode;
		$this->params['code_view_menu'] = "configuration";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/ritase/v_ritase', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function add()
	{
		if (!isset($this->sess->user_company)) {
			redirect(base_url());
		}

		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where("ritase_company", $this->sess->user_company);
		$this->dbtransporter->where("ritase_status", 1);
		$q_ritase = $this->dbtransporter->get("ritase");
		$rows_ritase = $q_ritase->result();

		if (count($rows_ritase) > 0) {
			foreach ($rows_ritase as $row_ritase) {
				$ritase_geofence_name[] = $row_ritase->ritase_geofence_name;
			}
		}

		$this->db->order_by("geofence_name", "asc");
		$this->db->where("geofence_status", "1");
		$this->db->where("geofence_name !=", "");
		$this->db->where("geofence_user", $this->sess->user_id);
		if (count($rows_ritase) > 0) {
			$this->db->where_not_in("geofence_name", $ritase_geofence_name);
		}
		$q = $this->db->get("geofence");
		$rows = $q->result();

		$this->params['data'] = $rows;
		$this->params["content"] = $this->load->view('ritase/add', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function save()
	{
		$this->dbtransporter          = $this->load->database("transporter", true);

		$company                      = $this->sess->user_company;
		$name                         = isset($_POST['ritase_name']) ? trim($_POST['ritase_name']) : "";
		unset($data);

		$data['ritase_company']       = $company;
		$data['ritase_geofence_name'] = $name;
		$data['ritase_status']        = 1;

		$this->dbtransporter->insert("ritase", $data);

		$callback['error']    = false;
		$callback['message']  = "Add Ritase Seccess";
		$callback['redirect'] = base_url() . "ritase";

		echo json_encode($callback);
		return;
	}

	function info_delete()
	{
		$id = $this->input->post("id");
		if ($id) {
			$this->dbtransporter = $this->load->database("transporter", true);
			$this->dbtransporter->where("ritase_id", $id);
			$this->dbtransporter->limit(1);
			$q = $this->dbtransporter->get("ritase");
			$row = $q->row();

			$params["row"] = $row;
			$html = $this->load->view("ritase/info_delete", $params, true);
			$callback["error"] = false;
			$callback["html"] = $html;

			echo json_encode($callback);
		}
	}


	function remove()
	{
		$id = $this->input->post("id_ritase");
		if ($id) {
			$this->dbtransporter = $this->load->database("transporter", true);
			$this->dbtransporter->where("ritase_id", $id);
			$this->dbtransporter->delete("ritase");
			$this->dbtransporter->cache_delete_all();
			echo json_encode(array("msg" => "success", "code" => "200"));
		}
	}

	function menu_ritase_report()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2) {
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}

		$q_vehicle = $this->db->get("vehicle");
		$row_vehicle = $q_vehicle->result();
		//print_r($row_vehicle);exit;

		$this->db->cache_delete_all();

		$this->dbtransporter = $this->load->database("transporter", true);

		$this->dbtransporter->order_by("ritase_geofence_name", "asc");
		$this->dbtransporter->where("ritase_company", $this->sess->user_company);
		$this->dbtransporter->where("ritase_status", "1");

		$q_ritase = $this->dbtransporter->get("ritase");
		$row_ritase = $q_ritase->result();

		$this->dbtransporter->cache_delete_all();

		$this->params["vehicle"] = $row_vehicle;
		$this->params["ritase"] = $row_ritase;
		$this->params["content"] = $this->load->view('ritase/mn_ritase_report', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function ritase_report()
	{
		$vehicle_device = $this->input->post("vehicle");

		$startdate = $this->input->post("date");
		$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . "00:00:00"));

		$enddate = $this->input->post("enddate");
		$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . "23:59:59"));

		$ritase = $this->input->post("ritase");
		$exRitase = explode(",", $ritase);
		$ritase_id = $exRitase[0];
		$ritase_name = $exRitase[1];

		$this->db->order_by("geoalert_time", "asc");
		$this->db->where("geoalert_vehicle", $vehicle_device);
		$this->db->where("geoalert_time >=", $sdate);
		$this->db->where("geoalert_time <=", $edate);
		$this->db->join("geofence", "geofence_id = geoalert_geofence", "leftouter");
		$this->db->where("geofence_name", $ritase_name);
		$q = $this->db->get("geofence_alert");
		$rows = $q->result();

		//print_r($rows);exit;

		$this->db->cache_delete_all();

		for ($i = 0; $i < count($rows); $i++) {
			$rows[$i]->geoalert_time_t = dbmaketime($rows[$i]->geoalert_time);
		}

		$params["data"] = $rows;
		$params["start_date"] = $startdate;
		$params["end_date"] = $enddate;

		$html = $this->load->view("ritase/ritase_report", $params, true);

		$callback["error"] = false;
		$callback["html"] = $html;

		echo json_encode($callback);
	}
}
