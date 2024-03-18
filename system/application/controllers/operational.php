<?php
include "base.php";

class Operational extends Base
{
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Operational()
	{
		parent::Base();
		// DASHBOARD START
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
		// DASHBOARD END
		$this->load->model("gpsmodel");
		$this->load->model("m_securityevidence");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("m_operational");
	}

	function index()
	{
		if (!isset($this->sess->user_type)) {
			redirect('dashboard');
		}

		$privilegecode   = $this->sess->user_id_role;



		$rows                           = $this->get_vehicle_pjo();
		// $rows_company                   = $this->dashboardmodel->get_company_bylevel();
		$rows_company                   = $this->get_company();
		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/operational/v_operational_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/operational/v_operational_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/operational/v_operational_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/operational/v_operational_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/operational/v_operational_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/operational/v_operational_new', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function operational_search()
	{
		$company = $this->input->post('company');
		$vehicle = $this->input->post('vehicle');
		$periode = $this->input->post('periode');

		$nowdate  = date("Y-m-d");
		$nowday   = date("d");
		$nowmonth = date("m");
		$nowyear  = date("Y");
		$lastday  = date("d");
		$yesterday = date('Y-m-d', strtotime("-1 days"));
		$startdate = $this->input->post('startdate');
		$enddate = $this->input->post('enddate');

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate));
			$edate = date("Y-m-d H:i:s", strtotime($enddate));
		} else if ($periode == "yesterday") {
			$sdate = $yesterday;
			$edate = $yesterday;
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}
			$sdate = date("Y-m-d", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday));
			$edate = date("Y-m-d", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday));
		} else if ($periode == "last30") {
			// $firstday = "1";
			// $sdate = date("Y-m-d", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday));
			$sdate = date("Y-m-d", strtotime(" -30 day", strtotime($nowdate)));
			$edate = date("Y-m-d", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday));
		}

		$search = $this->m_operational->getReport("ts_kepmen_source", $company, $vehicle, $sdate, $edate);

		// echo "<pre>";
		// var_dump($search);die();
		// echo "<pre>";

		$this->params['data'] = $search;
		$html                    = $this->load->view('newdashboard/operational/v_operational_new_result', $this->params, true);
		$callback["html"]        = $html;
		$callback["report"]      = $search;

		echo json_encode($callback);
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
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		return  $q->result_array();
	}
}
