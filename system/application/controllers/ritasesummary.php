<?php
include "base.php";

class Ritasesummary extends Base
{
	var $otherdb;

	function Ritasesummary()
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
		$user_dblive 	   = $this->sess->user_dblive;
		$privilegecode   = $this->sess->user_id_role;
		$user_parent 	   = $this->sess->user_parent;
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
			$this->db->where("vehicle_subgroup", $user_subgroup);
		} else {
			$this->db->where("vehicle_no", 99999);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0) {
			redirect(base_url());
		}

		$rows = $q->result();

		$rows_company = $this->get_company_bylevel();

		$this->params["rcompany"] = $rows_company;
		$this->params["vehicles"] = $rows;
		$this->params['code_view_menu'] = "report";
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"] = $this->load->view('newdashboard/report/vritasesummary_report', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function search()
	{
		ini_set('display_errors', 1);
		//ini_set('memory_limit', '2G');
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$company = $this->input->post('company');
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$periode = $this->input->post("periode");
		$reporttype = $this->input->post("reporttype");

		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "ritase_new_";
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
		$rows = array();
		$total_q = 0;

		$error = "";
		$rows_summary = "";

		if ($vehicle == "") {
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		}
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

		//get vehicle
		$user_id = $this->sess->user_id;
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	  = $this->sess->user_dblive;
		$user_id_fix     = $user_id;


		$this->dbtrip = $this->load->database("tensor_report", true);
		$this->dbtrip->order_by("ritase_report_start_time", "asc");
		if ($vehicle == "all") {
			if ($user_level == 1) {
				$this->dbtrip->where("ritase_report_vehicle_user_id", 4408);
			} else if ($user_level == 2) {
				$this->dbtrip->where("ritase_report_vehicle_company", $user_company);
			} else {
				$this->dbtrip->where("ritase_report_vehicle_company", 99999);
			}
			$this->dbtrip->where("ritase_report_vehicle_id <>", 72150933); //jika pilih all bukan mobil trial
		} else {
			$this->dbtrip->where("ritase_report_vehicle_device", $vehicle);
		}


		if ($company != 0) {
			$this->dbtrip->where("ritase_report_vehicle_company", $company);
		}

		if ($periode == "yesterday") {
			$this->dbtrip->where("ritase_report_end_time >=", $sdate);
			$this->dbtrip->where("ritase_report_end_time <=", $edate);
		} else {
			$this->dbtrip->where("ritase_report_start_time >=", $sdate);
			$this->dbtrip->where("ritase_report_end_time <=", $edate);
		}
		if ($reporttype != "all") {
			$this->dbtrip->where("ritase_report_type", $reporttype);
		}
		$this->dbtrip->where("ritase_report_odometer >", 0);
		$this->dbtrip->where("ritase_report_start_geofence !=", "");
		$this->dbtrip->where("ritase_report_end_geofence !=", "");
		$q = $this->dbtrip->get($dbtable);

		if ($q->num_rows > 0) {
			$rows = $q->result();
		} else {
			$error .= "- No Data Ritase ! \n";
		}

		if ($error != "") {
			$callback['error'] = true;
			$callback['message'] = $error;
			$callback['vehicle'] = $vehicle;

			echo json_encode($callback);
			return;
		}

		$params['data'] = $rows;
		$params['dbtable'] = $dbtable;
		$params['startdate'] = $sdate;
		$params['enddate'] = $edate;

		$html = $this->load->view("newdashboard/report/vritasesummary_result", $params, true);

		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;

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
		}

		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
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

	function get_geofence_bydblive($dblive)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$this->dblive = $this->load->database($dblive, true);
		$this->dblive->select("geofence_name");
		$this->dblive->order_by("geofence_name", "asc");
		$this->dblive->where("geofence_user", 4392); //khusus tms
		$this->dblive->where("geofence_status", 1);
		$this->dblive->where("geofence_type", "road");
		$qd = $this->dblive->get("geofence");
		$rd = $qd->result();

		return $rd;
	}
}
