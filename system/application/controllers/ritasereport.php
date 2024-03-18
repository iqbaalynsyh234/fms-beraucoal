<?php
include "base.php";

class Ritasereport extends Base
{
	var $otherdb;

	function Ritasereport()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
	}

	function index()
	{
		if (!isset($this->sess->user_type)) {
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
		} else if ($privilegecode == 7) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else {
			$this->db->where("vehicle_no", 99999);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
		}

		$rows = $q->result();

		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";

		$rows_company = $this->get_company_bylevel();
		//$rows_geofence = $this->get_geofence_bydblive($user_dblive);

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;
		//$this->params["rgeofence"] = $rows_geofence;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}
	
	function full()
	{
		if (!isset($this->sess->user_type)) {
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
		} else if ($privilegecode == 7) {
			$this->db->where("vehicle_user_id", $user_parent);
		} else {
			$this->db->where("vehicle_no", 99999);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
		}

		$rows = $q->result();

		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";

		$rows_company = $this->get_company_bylevel();
		//$rows_geofence = $this->get_geofence_bydblive($user_dblive);

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;
		//$this->params["rgeofence"] = $rows_geofence;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report_full', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report_full', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report_full', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report_full', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report_full', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report_full', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} elseif ($privilegecode == 7) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report_full', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/report/vritase_report_full', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function get_vehicle_by_company_with_numberorder($id)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$this->db->order_by("vehicle_no", "asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
		$this->db->where("vehicle_company", $id);
		if ($this->sess->user_group > 0) {
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->join("company", "vehicle_company = company_id", "left");
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if ($qd->num_rows() > 0) {
			$options = "<option value='0' selected='selected' >--All Vehicle--</option>";
			$i = 1;
			foreach ($rd as $obj) {
				$options .= "<option value='" . $obj->vehicle_device . "'>" . $i . ". " . $obj->vehicle_no . " - " . $obj->vehicle_name . " " . "(" . $obj->company_name . ")" . "</option>";
				$i++;
			}

			echo $options;
			return;
		}
	}

	function search()
	{
		ini_set('display_errors', 1);
		//ini_set('memory_limit', '2G');
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$company = $this->input->post('company');
		$vehicle   = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate   = $this->input->post("enddate");
		$shour     = $this->input->post("shour");
		$ehour     = $this->input->post("ehour");
		//$jalur = $this->input->post("jalur");
		//$geofence = $this->input->post("geofence");
		$periode    = $this->input->post("periode");
		$km         = $this->input->post("km");
		$reporttype = $this->input->post("reporttype");

		$nowdate    = date("Y-m-d");
		$nowday     = date("d");
		$nowmonth   = date("m");
		$nowyear    = date("Y");
		$lastday    = date("t");

		$report         = "ritase_";
		$report_sum     = "summary_";
		$locationreport = "location_";

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

		$m1           = date("F", strtotime($sdate));
		$m2           = date("F", strtotime($edate));
		$year         = date("Y", strtotime($sdate));
		$year2        = date("Y", strtotime($edate));
		$rows         = array();
		$total_q      = 0;

		$error        = "";
		$rows_summary = "";

		// if ($vehicle == "") {
		// 	$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		// }
		if ($m1 != $m2) {
			$error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
		}

		if ($year != $year2) {
			$error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
		}

		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		switch ($m1) {
			case "January":
				$dbtable   = $report . "januari_" . $year;
				$dbtable_sum     = $report_sum . "januari_" . $year;
				$dbtablelocation = $locationreport . "januari_" . $year;
				break;
			case "February":
				$dbtable   = $report . "februari_" . $year;
				$dbtable_sum     = $report_sum . "februari_" . $year;
				$dbtablelocation = $locationreport . "februari_" . $year;
				break;
			case "March":
				$dbtable   = $report . "maret_" . $year;
				$dbtable_sum     = $report_sum . "maret_" . $year;
				$dbtablelocation = $locationreport . "maret_" . $year;
				break;
			case "April":
				$dbtable   = $report . "april_" . $year;
				$dbtable_sum     = $report_sum . "april_" . $year;
				$dbtablelocation = $locationreport . "april_" . $year;
				break;
			case "May":
				$dbtable   = $report . "mei_" . $year;
				$dbtable_sum     = $report_sum . "mei_" . $year;
				$dbtablelocation = $locationreport . "mei_" . $year;
				break;
			case "June":
				$dbtable   = $report . "juni_" . $year;
				$dbtable_sum     = $report_sum . "juni_" . $year;
				$dbtablelocation = $locationreport . "juni_" . $year;
				break;
			case "July":
				$dbtable   = $report . "juli_" . $year;
				$dbtable_sum     = $report_sum . "juli_" . $year;
				$dbtablelocation = $locationreport . "juli_" . $year;
				break;
			case "August":
				$dbtable   = $report . "agustus_" . $year;
				$dbtable_sum     = $report_sum . "agustus_" . $year;
				$dbtablelocation = $locationreport . "agustus_" . $year;
				break;
			case "September":
				$dbtable   = $report . "september_" . $year;
				$dbtable_sum     = $report_sum . "september_" . $year;
				$dbtablelocation = $locationreport . "september_" . $year;
				break;
			case "October":
				$dbtable   = $report . "oktober_" . $year;
				$dbtable_sum     = $report_sum . "oktober_" . $year;
				$dbtablelocation = $locationreport . "oktober_" . $year;
				break;
			case "November":
				$dbtable   = $report . "november_" . $year;
				$dbtable_sum     = $report_sum . "november_" . $year;
				$dbtablelocation = $locationreport . "november_" . $year;
				break;
			case "December":
				$dbtable   = $report . "desember_" . $year;
				$dbtable_sum     = $report_sum . "desember_" . $year;
				$dbtablelocation = $locationreport . "desember_" . $year;
				break;
		}

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

		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->order_by("ritase_report_end_time", "asc");
		if ($vehicle == "0") {
			if ($company != 0) {
				$this->dbtrip->where("ritase_report_vehicle_company", $company);
			}
			if ($privilegecode == 0) {
				$this->dbtrip->where("ritase_report_vehicle_user_id", $user_id_fix);
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
			} else if ($privilegecode == 7) {
				$this->dbtrip->where("ritase_report_vehicle_user_id", $user_parent);
			}else {
				$this->dbtrip->where("ritase_report_vehicle_company", 99999);
			}
			$this->dbtrip->where("ritase_report_vehicle_id <>", 72150933); //jika pilih all bukan mobil trial
		} else {
			$this->dbtrip->where("ritase_report_vehicle_device", $vehicle);
		}
		$this->dbtrip->where("ritase_report_duration_sec >=", 100);
		$this->dbtrip->where("ritase_report_end_time >=", $sdate);
		$this->dbtrip->where("ritase_report_end_time <=", $edate);
		$this->dbtrip->where("ritase_report_end_geofence !=", "PORT BBC");
		$this->dbtrip->where("ritase_report_type", $reporttype); //data fix (default) = 0
		$q = $this->dbtrip->get($dbtable);

		if ($q->num_rows > 0) {
			$rows = $q->result();
		} else {
			$error .= "- No Data Ritase ! \n";
		}

		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$params['data']      = $rows;
		$params['dbtable']   = $dbtable;
		$params['startdate'] = $sdate;
		$params['enddate']   = $edate;

		// GET TO LOCATION REPORT START
		/* if(count($rows)>0){
			for ($loop=0; $loop < sizeof($rows); $loop++) {
				$vehicle_id          = $rows[$loop]->ritase_report_vehicle_device;
				$vehicle_no          = $rows[$loop]->ritase_report_vehicle_no;
				$start_date          = $rows[$loop]->ritase_report_start_time;
				$end_date            = $rows[$loop]->ritase_report_end_time;

				$datenya[] = $start_date.'||'.$end_date;

				$datadetailhistory[] = $this->getDetailHistory2($vehicle_id,$start_date,$end_date,$dbtablelocation,9);
			}
			// echo "<pre>";
			// var_dump($datenya);die();
			// echo "<pre>";
			$params['detailhistory']   = $datadetailhistory;
		} */
		// GET TO LOCATION REPORT END
		// echo "<pre>";
		// var_dump($params['detailhistory']);die();
		// echo "<pre>";

		$html = $this->load->view("newdashboard/report/vritase_result", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;
	}
	
	function search_full()
	{
		ini_set('display_errors', 1);
		//ini_set('memory_limit', '2G');
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$company = $this->input->post('company');
		$vehicle   = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate   = $this->input->post("enddate");
		/* $shour     = $this->input->post("shour");
		$ehour     = $this->input->post("ehour"); */
		$periode    = $this->input->post("periode");
		$km         = $this->input->post("km");
		$reporttype = $this->input->post("reporttype");

		$nowdate    = date("Y-m-d");
		$nowday     = date("d");
		$nowmonth   = date("m");
		$nowyear    = date("Y");
		$lastday    = date("t");
		
		$yesterdate    = date("Y-m-d", strtotime("yesterday"));
		$start_yesterdate    = date("Y-m-d H:i:s", strtotime($yesterdate." "."00:00:00"));
		$end_yesterdate    = date("Y-m-d H:i:s", strtotime($yesterdate." "."23:59:59"));
		
		$start_nowdate    = date("Y-m-d H:i:s", strtotime($nowdate." "."00:00:00"));
		$end_nowdate    = date("Y-m-d H:i:s", strtotime($nowdate." "."23:59:59"));

		$report         = "ritase_full_";
		$report_today   = "ritase_hour_";
		$report_sum     = "summary_";
		$locationreport = "location_";
		
		if ($periode == "custom") {
			$sdate = date("Y-m-d", strtotime($startdate));
			$edate = date("Y-m-d", strtotime($enddate));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d", strtotime("yesterday"));
			$edate = date("Y-m-d", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			$sdate = date("Y-m-d", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday));
			$edate = date("Y-m-d", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday));
			$edate = date("Y-m-d", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday ));
		} else {
			$sdate = date("Y-m-d", strtotime($startdate));
			$edate = date("Y-m-d", strtotime($enddate));
		}


		//print_r($sdate." ".$edate);exit();

		$m1           = date("F", strtotime($sdate));
		$m2           = date("F", strtotime($edate));
		$year         = date("Y", strtotime($sdate));
		$year2        = date("Y", strtotime($edate));
		$rows         = array();
		$total_q      = 0;
		$data_nowdate  = 0;
		$data_yesterday = 0;
		$error        = "";
		$rows_summary = "";

		// if ($vehicle == "") {
		// 	$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		// }
		if ($m1 != $m2) {
			$error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
		}

		if ($year != $year2) {
			$error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
		}

		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		switch ($m1) {
			case "January":
				$dbtable   = $report . "januari_" . $year;
				$dbtable_today   = $report_today . "januari_" . $year;
				$dbtable_sum     = $report_sum . "januari_" . $year;
				$dbtablelocation = $locationreport . "januari_" . $year;
				break;
			case "February":
				$dbtable   = $report . "februari_" . $year;
				$dbtable_today   = $report_today . "februari_" . $year;
				$dbtable_sum     = $report_sum . "februari_" . $year;
				$dbtablelocation = $locationreport . "februari_" . $year;
				break;
			case "March":
				$dbtable   = $report . "maret_" . $year;
				$dbtable_today   = $report_today . "maret_" . $year;
				$dbtable_sum     = $report_sum . "maret_" . $year;
				$dbtablelocation = $locationreport . "maret_" . $year;
				break;
			case "April":
				$dbtable   = $report . "april_" . $year;
				$dbtable_today   = $report_today . "april_" . $year;
				$dbtable_sum     = $report_sum . "april_" . $year;
				$dbtablelocation = $locationreport . "april_" . $year;
				break;
			case "May":
				$dbtable   = $report . "mei_" . $year;
				$dbtable_today   = $report_today . "mei_" . $year;
				$dbtable_sum     = $report_sum . "mei_" . $year;
				$dbtablelocation = $locationreport . "mei_" . $year;
				break;
			case "June":
				$dbtable   = $report . "juni_" . $year;
				$dbtable_today   = $report_today . "juni_" . $year;
				$dbtable_sum     = $report_sum . "juni_" . $year;
				$dbtablelocation = $locationreport . "juni_" . $year;
				break;
			case "July":
				$dbtable   = $report . "juli_" . $year;
				$dbtable_today   = $report_today . "juli_" . $year;
				$dbtable_sum     = $report_sum . "juli_" . $year;
				$dbtablelocation = $locationreport . "juli_" . $year;
				break;
			case "August":
				$dbtable   = $report . "agustus_" . $year;
				$dbtable_today   = $report_today . "agustus_" . $year;
				$dbtable_sum     = $report_sum . "agustus_" . $year;
				$dbtablelocation = $locationreport . "agustus_" . $year;
				break;
			case "September":
				$dbtable   = $report . "september_" . $year;
				$dbtable_today   = $report_today . "september_" . $year;
				$dbtable_sum     = $report_sum . "september_" . $year;
				$dbtablelocation = $locationreport . "september_" . $year;
				break;
			case "October":
				$dbtable   = $report . "oktober_" . $year;
				$dbtable_today   = $report_today . "oktober_" . $year;
				$dbtable_sum     = $report_sum . "oktober_" . $year;
				$dbtablelocation = $locationreport . "oktober_" . $year;
				break;
			case "November":
				$dbtable   = $report . "november_" . $year;
				$dbtable_today   = $report_today . "november_" . $year;
				$dbtable_sum     = $report_sum . "november_" . $year;
				$dbtablelocation = $locationreport . "november_" . $year;
				break;
			case "December":
				$dbtable   = $report . "desember_" . $year;
				$dbtable_today   = $report_today . "desember_" . $year;
				$dbtable_sum     = $report_sum . "desember_" . $year;
				$dbtablelocation = $locationreport . "desember_" . $year;
				break;
		}
		
		//cari location status data kemarin dahulu
		$sdate_loc = date("Y-m-d H:i:s", strtotime($yesterdate." "."00:00:00"));
		$edate_loc = date("Y-m-d H:i:s", strtotime($yesterdate." "."23:59:59"));
		
		$loc_result = $this->getStatusLocReport("4408","all",$sdate_loc,$edate_loc);
		
		if($loc_result == "ON PROCESS")
		{
			$data_yesterday = 1;
			$sdate_additional_yesterday = $start_yesterdate;
			$edate_additional_yesterday = $end_yesterdate;
		}
		
		if($edate == $nowdate)
		{
			$data_nowdate = 1;
			$sdate_additional_nowdate = $start_nowdate;
			$edate_additional_nowdate = $end_nowdate;
			
		}
		
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
		
		$rows1 = array();
		$rows2 = array();
		$rows3 = array();
		
		$this->dbtrip = $this->load->database("tensor_report", true);
		
		//main data
		$this->dbtrip->order_by("ritase_report_end_time", "asc");
		if ($vehicle == "0") {
			if ($company != 0) {
				$this->dbtrip->where("ritase_report_vehicle_company", $company);
			}
			if ($privilegecode == 0) {
				$this->dbtrip->where("ritase_report_vehicle_user_id", $user_id_fix);
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
			} else if ($privilegecode == 7) {
				$this->dbtrip->where("ritase_report_vehicle_user_id", $user_parent);
			}else {
				$this->dbtrip->where("ritase_report_vehicle_company", 99999);
			}
			$this->dbtrip->where("ritase_report_vehicle_id <>", 72150933); //jika pilih all bukan mobil trial
		} else {
			$this->dbtrip->where("ritase_report_vehicle_device", $vehicle);
		}
		$this->dbtrip->where("ritase_report_duration_sec >=", 600); //10 * 60 = 600s
		$this->dbtrip->where("ritase_report_shift_date >=", $sdate);
		$this->dbtrip->where("ritase_report_shift_date <=", $edate);
		$this->dbtrip->where("ritase_report_end_geofence !=", "PORT BBC");
		$this->dbtrip->where("ritase_report_end_geofence !=", "");
		$this->dbtrip->where("ritase_report_type", $reporttype); //data fix (default) = 0
		$q1 = $this->dbtrip->get($dbtable);
		$rows1 = $q1->result();
		
		//jika data kmarin (berdasarkan loc report blum selesai maka : 
		if($data_yesterday == 1)
		{
			$this->dbtrip->order_by("ritase_report_end_time", "asc");
			if ($vehicle == "0") {
				if ($company != 0) {
					$this->dbtrip->where("ritase_report_vehicle_company", $company);
				}
				if ($privilegecode == 0) {
					$this->dbtrip->where("ritase_report_vehicle_user_id", $user_id_fix);
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
				} else if ($privilegecode == 7) {
					$this->dbtrip->where("ritase_report_vehicle_user_id", $user_parent);
				}else {
					$this->dbtrip->where("ritase_report_vehicle_company", 99999);
				}
				$this->dbtrip->where("ritase_report_vehicle_id <>", 72150933); //jika pilih all bukan mobil trial
			} else {
				$this->dbtrip->where("ritase_report_vehicle_device", $vehicle);
			}
			//$this->dbtrip->where("ritase_report_duration_sec >=", 100);
			$this->dbtrip->where("ritase_report_end_time >=", $sdate_additional_yesterday);
			$this->dbtrip->where("ritase_report_end_time <=", $edate_additional_yesterday);
			$this->dbtrip->where("ritase_report_end_geofence !=", "PORT BBC");
			$this->dbtrip->where("ritase_report_end_geofence !=", "");
			$this->dbtrip->where("ritase_report_type", $reporttype); //data fix (default) = 0
			$q2 = $this->dbtrip->get($dbtable_today);
			$rows2 = $q2->result();
		}
		
		//jika data kmarin (berdasarkan loc report blum selesai maka : 
		if($data_nowdate == 1)
		{
			$this->dbtrip->order_by("ritase_report_end_time", "asc");
			if ($vehicle == "0") {
				if ($company != 0) {
					$this->dbtrip->where("ritase_report_vehicle_company", $company);
				}
				if ($privilegecode == 0) {
					$this->dbtrip->where("ritase_report_vehicle_user_id", $user_id_fix);
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
				} else if ($privilegecode == 7) {
					$this->dbtrip->where("ritase_report_vehicle_user_id", $user_parent);
				}else {
					$this->dbtrip->where("ritase_report_vehicle_company", 99999);
				}
				$this->dbtrip->where("ritase_report_vehicle_id <>", 72150933); //jika pilih all bukan mobil trial
			} else {
				$this->dbtrip->where("ritase_report_vehicle_device", $vehicle);
			}
			//$this->dbtrip->where("ritase_report_duration_sec >=", 100);
			$this->dbtrip->where("ritase_report_end_time >=", $sdate_additional_nowdate);
			$this->dbtrip->where("ritase_report_end_time <=", $edate_additional_nowdate);
			$this->dbtrip->where("ritase_report_end_geofence !=", "PORT BBC");
			$this->dbtrip->where("ritase_report_end_geofence !=", "");
			$this->dbtrip->where("ritase_report_type", $reporttype); //data fix (default) = 0
			$q3 = $this->dbtrip->get($dbtable_today);
			$rows3 = $q3->result();
		}
		
		$rows = array_merge($rows1,$rows2,$rows3);
		
		if (count($rows) == 0) {
			$error .= "- No Data Ritase ! \n";
		}

		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$params['data']      = $rows;
		$params['dbtable']   = $dbtable;
		$params['startdate'] = $sdate;
		$params['enddate']   = $edate;
		$params['data_yesterday'] = $data_yesterday;
		$params['data_nowdate'] = $data_nowdate;
		
		$html = $this->load->view("newdashboard/report/vritase_result_full", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;
	}
	
	function getStatusLocReport($userid,$vdevice,$sdate,$edate)
	{
		
		if ($vdevice == 'all') {
			$ReportTypeArray = array("LOCATION ALL", "LOCATION IDLE ALL", "LOCATION OFF ALL");
		}else {
			$ReportTypeArray = array("location", "location_off", "location_idle");
		}

		$content = $this->getthisrulelocationreport($ReportTypeArray, $vdevice, $sdate, $edate);

		$data_1 = 0;
		$data_2 = 0;
		$data_3 = 0;
		$data_array_rpoerttype = array();
		$data_content = array_map('current', $content);

					if (in_array($ReportTypeArray[0], $data_content)) {
						$data_1 += 1;
					}

					if (in_array($ReportTypeArray[1], $data_content)) {
						$data_2 += 1;
					}

					if (in_array($ReportTypeArray[2], $data_content)) {
						$data_3 += 1;
					}

			$total_data_fix = ($data_1 + $data_2 + $data_3);

		
			if ($total_data_fix == 3) {
				$result = "DONE";
			}else {
				$result = "ON PROCESS";
			}
	
			return $result;
	}
	
	function getthisrulelocationreport($reportype, $vehicleid, $starttime, $endtime)
	{
		$this->dbtrip = $this->load->database("tensor_report",true);
		$this->dbtrip->order_by("autoreport_data_startdate","asc");

		$this->dbtrip->select("autoreport_type");
		if($vehicleid != "all"){
			$this->dbtrip->where("autoreport_vehicle_device", $vehicleid);
		}

		$this->dbtrip->where_in("autoreport_type", $reportype);
		$this->dbtrip->where("autoreport_data_startdate >=", $starttime);
		$this->dbtrip->where("autoreport_data_enddate <=", $endtime);
		$q = $this->dbtrip->get("autoreport_new")->result_array();
		
		$this->dbtrip->close();
		$this->dbtrip->cache_delete_all();
		return $q;
	}
	

	function linechartfl($id = 0)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		if ($id) {
			$dbtable      = "ritase_april_2021";
			$this->dbtrip = $this->load->database("tensor_report", true);
			$this->dbtrip->where("ritase_report_id", $id);
			$q            = $this->dbtrip->get($dbtable);
			$row          = $q->row();

			if (count($row) > 0) {
				$vehicle_id = $row->ritase_report_vehicle_id;
				$vehicle_no = $row->ritase_report_vehicle_no;
				$start_date = $row->ritase_report_start_time;
				$end_date = $row->ritase_report_end_time;

				//get detail history location
				$data = $this->getDetailHistory($vehicle_id, $start_date, $end_date, "location_april_2021", 9);

				$content_data = array();
				$content_label = array();
				$content_label2 = array();
				$content_time = array();

				$datafix = array();
				for ($loop = 0; $loop < sizeof($data); $loop++) {
					if (strpos($data[$loop]->location_report_location, "ROM") !== false) {
						array_push($datafix, array(
							"location_report_id"              => $data[$loop]->location_report_id,
							"location_report_vehicle_user_id" => $data[$loop]->location_report_vehicle_user_id,
							"location_report_vehicle_id"      => $data[$loop]->location_report_vehicle_id,
							"location_report_vehicle_device"  => $data[$loop]->location_report_vehicle_device,
							"location_report_vehicle_no"      => $data[$loop]->location_report_vehicle_no,
							"location_report_vehicle_name"    => $data[$loop]->location_report_vehicle_name,
							"location_report_vehicle_type"    => $data[$loop]->location_report_vehicle_type,
							"location_report_vehicle_company" => $data[$loop]->location_report_vehicle_company,
							"location_report_imei"            => $data[$loop]->location_report_imei,
							"location_report_type"            => $data[$loop]->location_report_type,
							"location_report_name"            => $data[$loop]->location_report_name,
							"location_report_speed"           => $data[$loop]->location_report_speed,
							"location_report_gpsstatus"       => $data[$loop]->location_report_gpsstatus,
							"location_report_gps_time"        => $data[$loop]->location_report_gps_time,
							"location_report_geofence_id"     => $data[$loop]->location_report_geofence_id,
							"location_report_geofence_name"   => $data[$loop]->location_report_geofence_name,
							"location_report_geofence_limit"  => $data[$loop]->location_report_geofence_limit,
							"location_report_geofence_type"   => $data[$loop]->location_report_geofence_type,
							"location_report_jalur"           => $data[$loop]->location_report_jalur,
							"location_report_direction"       => $data[$loop]->location_report_direction,
							"location_report_location"        => $data[$loop]->location_report_location,
							"location_report_coordinate"      => $data[$loop]->location_report_coordinate,
							"location_report_latitude"        => $data[$loop]->location_report_latitude,
							"location_report_longitude"       => $data[$loop]->location_report_longitude,
							"location_report_odometer"        => $data[$loop]->location_report_odometer,
							"location_report_fuel_data"       => $data[$loop]->location_report_fuel_data,
							"location_report_fuel_data_fix"   => $data[$loop]->location_report_fuel_data_fix,
							"location_report_fuel_liter"      => $data[$loop]->location_report_fuel_liter,
							"location_report_fuel_liter_fix"  => $data[$loop]->location_report_fuel_liter_fix,
							"location_report_view"            => $data[$loop]->location_report_view,
							"location_report_event"           => $data[$loop]->location_report_event,
						));
					} elseif (strpos($data[$loop]->location_report_location, "PORT") !== false) {
						array_push($datafix, array(
							"location_report_id"              => $data[$loop]->location_report_id,
							"location_report_vehicle_user_id" => $data[$loop]->location_report_vehicle_user_id,
							"location_report_vehicle_id"      => $data[$loop]->location_report_vehicle_id,
							"location_report_vehicle_device"  => $data[$loop]->location_report_vehicle_device,
							"location_report_vehicle_no"      => $data[$loop]->location_report_vehicle_no,
							"location_report_vehicle_name"    => $data[$loop]->location_report_vehicle_name,
							"location_report_vehicle_type"    => $data[$loop]->location_report_vehicle_type,
							"location_report_vehicle_company" => $data[$loop]->location_report_vehicle_company,
							"location_report_imei"            => $data[$loop]->location_report_imei,
							"location_report_type"            => $data[$loop]->location_report_type,
							"location_report_name"            => $data[$loop]->location_report_name,
							"location_report_speed"           => $data[$loop]->location_report_speed,
							"location_report_gpsstatus"       => $data[$loop]->location_report_gpsstatus,
							"location_report_gps_time"        => $data[$loop]->location_report_gps_time,
							"location_report_geofence_id"     => $data[$loop]->location_report_geofence_id,
							"location_report_geofence_name"   => $data[$loop]->location_report_geofence_name,
							"location_report_geofence_limit"  => $data[$loop]->location_report_geofence_limit,
							"location_report_geofence_type"   => $data[$loop]->location_report_geofence_type,
							"location_report_jalur"           => $data[$loop]->location_report_jalur,
							"location_report_direction"       => $data[$loop]->location_report_direction,
							"location_report_location"        => $data[$loop]->location_report_location,
							"location_report_coordinate"      => $data[$loop]->location_report_coordinate,
							"location_report_latitude"        => $data[$loop]->location_report_latitude,
							"location_report_longitude"       => $data[$loop]->location_report_longitude,
							"location_report_odometer"        => $data[$loop]->location_report_odometer,
							"location_report_fuel_data"       => $data[$loop]->location_report_fuel_data,
							"location_report_fuel_data_fix"   => $data[$loop]->location_report_fuel_data_fix,
							"location_report_fuel_liter"      => $data[$loop]->location_report_fuel_liter,
							"location_report_fuel_liter_fix"  => $data[$loop]->location_report_fuel_liter_fix,
							"location_report_view"            => $data[$loop]->location_report_view,
							"location_report_event"           => $data[$loop]->location_report_event,
						));
					} elseif (strpos($data[$loop]->location_report_location, "KM") !== false) {
						array_push($datafix, array(
							"location_report_id"              => $data[$loop]->location_report_id,
							"location_report_vehicle_user_id" => $data[$loop]->location_report_vehicle_user_id,
							"location_report_vehicle_id"      => $data[$loop]->location_report_vehicle_id,
							"location_report_vehicle_device"  => $data[$loop]->location_report_vehicle_device,
							"location_report_vehicle_no"      => $data[$loop]->location_report_vehicle_no,
							"location_report_vehicle_name"    => $data[$loop]->location_report_vehicle_name,
							"location_report_vehicle_type"    => $data[$loop]->location_report_vehicle_type,
							"location_report_vehicle_company" => $data[$loop]->location_report_vehicle_company,
							"location_report_imei"            => $data[$loop]->location_report_imei,
							"location_report_type"            => $data[$loop]->location_report_type,
							"location_report_name"            => $data[$loop]->location_report_name,
							"location_report_speed"           => $data[$loop]->location_report_speed,
							"location_report_gpsstatus"       => $data[$loop]->location_report_gpsstatus,
							"location_report_gps_time"        => $data[$loop]->location_report_gps_time,
							"location_report_geofence_id"     => $data[$loop]->location_report_geofence_id,
							"location_report_geofence_name"   => $data[$loop]->location_report_geofence_name,
							"location_report_geofence_limit"  => $data[$loop]->location_report_geofence_limit,
							"location_report_geofence_type"   => $data[$loop]->location_report_geofence_type,
							"location_report_jalur"           => $data[$loop]->location_report_jalur,
							"location_report_direction"       => $data[$loop]->location_report_direction,
							"location_report_location"        => $data[$loop]->location_report_location,
							"location_report_coordinate"      => $data[$loop]->location_report_coordinate,
							"location_report_latitude"        => $data[$loop]->location_report_latitude,
							"location_report_longitude"       => $data[$loop]->location_report_longitude,
							"location_report_odometer"        => $data[$loop]->location_report_odometer,
							"location_report_fuel_data"       => $data[$loop]->location_report_fuel_data,
							"location_report_fuel_data_fix"   => $data[$loop]->location_report_fuel_data_fix,
							"location_report_fuel_liter"      => $data[$loop]->location_report_fuel_liter,
							"location_report_fuel_liter_fix"  => $data[$loop]->location_report_fuel_liter_fix,
							"location_report_view"            => $data[$loop]->location_report_view,
							"location_report_event"           => $data[$loop]->location_report_event,
						));
					}
				}

				for ($u = 0; $u < count($datafix); $u++) {
					//ultrasonic fuel
					$fullcap             = 200; // liter
					$fullpercent         = 100; // percentage
					$fullvolt		     = 3.54;

					if ($datafix[$u]['location_report_fuel_data'] > $fullvolt) {
						$ad1_volt = $fullvolt;
					} else {
						// $ad1_volt = round($datafix[$u]['location_report_fuel_data'],2, PHP_ROUND_HALF_DOWN);
						$ad1_volt = $datafix[$u]['location_report_fuel_data'];
					}

					$currentvolt         = $ad1_volt;

					$percenvoltase   = $currentvolt * ($fullpercent / $fullvolt); // persentase yg didapat dari perubahan voltase;
					$sisaliterbensin = ($percenvoltase * $fullcap) / $fullpercent;

					// $percenvoltase = round($percenvoltase,0, PHP_ROUND_HALF_DOWN);
					// $sisaliterbensin = round($sisaliterbensin,4, PHP_ROUND_HALF_DOWN);

					$percenvoltase   = $percenvoltase;
					$sisaliterbensin = $sisaliterbensin;

					$content_data[]   = $sisaliterbensin;
					$content_label[]  = $datafix[$u]['location_report_location'] . " (" . date("H:i:s", strtotime($datafix[$u]['location_report_gps_time'])) . ") " . $datafix[$u]['location_report_speed'] . "kph";
					$content_label2[] = $datafix[$u]['location_report_location'];
					$content_time[]   = date("H.i", strtotime($datafix[$u]['location_report_gps_time']));
					//$content_time[] = "ABC"; //$data[$u]->location_report_gps_time;
				}

				$this->params["content_data"]   = $content_data;
				$this->params["content_label"]  = $content_label;
				$this->params["content_time"]   = $content_time;
				$this->params["content_label2"] = $content_label2;
			}
		}

		$this->params['vehicle_no']     = $vehicle_no;
		$this->params['dataforexcel']   = $datafix;
		$this->params['start_date']     = date("d-m-Y H:i:s", strtotime($start_date));
		$this->params['end_date']       = date("d-m-Y H:i:s", strtotime($end_date));
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/report/vritase_line', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function linechartst($id = 0)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		if ($id) {

			$dbtable = "ritase_april_2021";
			$this->dbtrip = $this->load->database("tensor_report", true);
			$this->dbtrip->where("ritase_report_id", $id);
			$q = $this->dbtrip->get($dbtable);
			$row = $q->row();

			if (count($row) > 0) {
				$vehicle_id = $row->ritase_report_vehicle_id;
				$vehicle_no = $row->ritase_report_vehicle_no;
				$start_date = $row->ritase_report_start_time;
				$end_date = $row->ritase_report_end_time;

				//get detail history location
				$data = $this->getDetailHistory($vehicle_id, $start_date, $end_date, "location_april_2021", 0);

				$content_data = array();
				$content_label = array();
				for ($u = 0; $u < count($data); $u++) {
					//ultrasonic fuel
					$fullcap             = 200; // liter
					$fullpercent         = 100; // percentage
					$fullvolt		     = 3.54;

					if ($data[$u]->location_report_fuel_data > $fullvolt) {
						$ad1_volt = $fullvolt;
					} else {
						$ad1_volt = round($data[$u]->location_report_fuel_data, 2, PHP_ROUND_HALF_DOWN);
					}

					$currentvolt         = $ad1_volt;

					$percenvoltase   = $currentvolt * ($fullpercent / $fullvolt); // persentase yg didapat dari perubahan voltase;
					$sisaliterbensin = ($percenvoltase * $fullcap) / $fullpercent;

					$percenvoltase = round($percenvoltase, 0, PHP_ROUND_HALF_DOWN);
					$sisaliterbensin = round($sisaliterbensin, 0, PHP_ROUND_HALF_DOWN);

					$content_data[] = $sisaliterbensin;
					$content_label[] = $data[$u]->location_report_location;
				}

				$this->params["content_data"] = $content_data;
				$this->params["content_label"] = $content_label;
			}
		}

		$this->params['vehicle_no'] = $vehicle_no;
		$this->params['start_date'] = date("d-m-Y H:i:s", strtotime($start_date));
		$this->params['end_date'] = date("d-m-Y H:i:s", strtotime($end_date));
		$this->params['code_view_menu'] = "report";

		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/report/vritase_line', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function getDistanceBetween($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi')
	{
		$theta = $longitude1 - $longitude2;
		$distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)))  + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
		$distance = acos($distance);
		$distance = rad2deg($distance);
		$distance = $distance * 60 * 1.1515;
		switch ($unit) {
			case 'Mi':
				break;
			case 'Km':
				$distance = $distance * 1.609344;
		}
		return (round($distance, 2));
	}

	function getDetailHistory($vid, $sdate, $edate, $sourcetable, $type)
	{
		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->order_by("location_report_gps_time", "asc");
		$this->dbtrip->group_by("location_report_location");
		// $this->dbtrip->select("location_report_location,location_report_fuel_data,location_report_gps_time,location_report_speed");
		$this->dbtrip->select("*");
		$this->dbtrip->where("location_report_vehicle_id", $vid);
		$this->dbtrip->where("location_report_gps_time >=", date("Y-m-d H:i:s", strtotime($sdate)));
		$this->dbtrip->where("location_report_gps_time <=", date("Y-m-d H:i:s", strtotime($edate)));
		if ($type == 0) {
			$this->dbtrip->where("location_report_speed", 0);
		}

		$qresult = $this->dbtrip->get($sourcetable);
		$rows_result = $qresult->result();

		return $rows_result;
	}

	function getDetailHistory2($vid, $sdate, $edate, $sourcetable, $type)
	{
		// echo "<pre>";
		// var_dump($vid.'||'.$sdate.'||'.$edate.'||'.$sourcetable.'||'.$type);die();
		// echo "<pre>";
		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->order_by("location_report_gps_time", "desc");
		$this->dbtrip->group_by("location_report_location");
		// $this->dbtrip->select("location_report_location,location_report_fuel_data,location_report_gps_time,location_report_speed");
		$this->dbtrip->select("*"); //location_report_fuel_data
		$this->dbtrip->where("location_report_vehicle_device", $vid);
		$this->dbtrip->where("location_report_gps_time >=", date("Y-m-d H:i:s", strtotime($sdate)));
		$this->dbtrip->where("location_report_gps_time <=", date("Y-m-d H:i:s", strtotime($edate)));
		// $this->dbtrip->limit(1);

		if ($type == 0) {
			$this->dbtrip->where("location_report_speed", 0);
		}

		$qresult     = $this->dbtrip->get($sourcetable);
		$rows_result = $qresult->result();

		return $rows_result;
	}

	function get_company_all()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$this->db->order_by("company_name", "asc");
		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
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
		}

		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}

	function get_geofence_bydblive($dblive)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$this->dblive = $this->load->database($dblive, true);
		$this->dblive->select("geofence_name");
		$this->dblive->order_by("geofence_name", "asc");
		$this->dblive->where("geofence_user", 4203); //khusus bib
		$this->dblive->where("geofence_status", 1);
		$this->dblive->where("geofence_type", "road");
		$qd = $this->dblive->get("geofence");
		$rd = $qd->result();

		return $rd;
	}
}
