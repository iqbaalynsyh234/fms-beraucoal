<?php
include "base.php";

class Beats extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
		$this->load->model("log_model");
		$this->load->helper('common');
	}

	function index()
	{
		redirect(base_url());
	}

	function employee()
	{
		$user_id             = $this->sess->user_id;
		$user_parent         = $this->sess->user_parent;
		$privilegecode 		 = $this->sess->user_id_role;

		$rows_branch         = $this->get_employee();

		$this->params["data"]              = $rows_branch;
		$this->params['code_view_menu']    = "masterdata";
		$this->params['code_view_submenu'] = "employee";
		$this->params['privilegecode']     = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_employee', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_employee', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_employee', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_employee', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_employee', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function site()
	{
		$user_id             = $this->sess->user_id;
		$user_parent         = $this->sess->user_parent;
		$privilegecode 		 = $this->sess->user_id_role;

		$rows_branch         = $this->get_site();

		$this->params["data"]              = $rows_branch;
		$this->params['code_view_menu']    = "masterdata";
		$this->params['code_view_submenu'] = "site";
		$this->params['privilegecode']     = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_site', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_site', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_site', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_site', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_site', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function location()
	{
		$user_id             = $this->sess->user_id;
		$user_parent         = $this->sess->user_parent;
		$privilegecode 		 = $this->sess->user_id_role;

		$rows_branch         = $this->get_location();

		$this->params["data"]              = $rows_branch;
		$this->params['code_view_menu']    = "masterdata";
		$this->params['code_view_submenu'] = "location";
		$this->params['privilegecode']     = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_location', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_location', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_location', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_location', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_location', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function object()
	{
		$user_id             = $this->sess->user_id;
		$user_parent         = $this->sess->user_parent;
		$privilegecode 		 = $this->sess->user_id_role;

		$rows_branch         = $this->get_object();

		$this->params["data"]              = $rows_branch;
		$this->params['code_view_menu']    = "masterdata";
		$this->params['code_view_submenu'] = "object";
		$this->params['privilegecode']     = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_object', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_object', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_object', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_object', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_object', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function objectdetail()
	{
		$user_id             = $this->sess->user_id;
		$user_parent         = $this->sess->user_parent;
		$privilegecode 		 = $this->sess->user_id_role;

		$rows_branch         = $this->get_object_detail();

		$this->params["data"]              = $rows_branch;
		$this->params['code_view_menu']    = "masterdata";
		$this->params['code_view_submenu'] = "objectdetail";
		$this->params['privilegecode']     = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_object_detail', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_object_detail', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_object_detail', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_object_detail', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_object_detail', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function quickaction()
	{
		$user_id             = $this->sess->user_id;
		$user_parent         = $this->sess->user_parent;
		$privilegecode 		 = $this->sess->user_id_role;

		$rows_branch         = $this->get_quick_action();

		$this->params["data"]              = $rows_branch;
		$this->params['code_view_menu']    = "masterdata";
		$this->params['code_view_submenu'] = "objectdetail";
		$this->params['privilegecode']     = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_quick_action', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_quick_action', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_quick_action', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_quick_action', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_quick_action', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function categorytype()
	{
		$user_id             = $this->sess->user_id;
		$user_parent         = $this->sess->user_parent;
		$privilegecode 		 = $this->sess->user_id_role;

		$rows_branch         = $this->get_category_type();

		$this->params["data"]              = $rows_branch;
		$this->params['code_view_menu']    = "masterdata";
		$this->params['code_view_submenu'] = "objectdetail";
		$this->params['privilegecode']     = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_category_type', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_category_type', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_category_type', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_category_type', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_category_type', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function pja()
	{
		$user_id             = $this->sess->user_id;
		$user_parent         = $this->sess->user_parent;
		$privilegecode 		 = $this->sess->user_id_role;

		$rows_branch         = $this->get_pja();

		$this->params["data"]              = $rows_branch;
		$this->params['code_view_menu']    = "masterdata";
		$this->params['code_view_submenu'] = "objectdetail";
		$this->params['privilegecode']     = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_pja', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_pja', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_pja', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_pja', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/beats/v_pja', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function get_employee()
	{
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->select("*");
		$this->dbts->from("ts_karyawan_beraucoal");
		$qbranch     = $this->dbts->get();
		$rows_branch = $qbranch->result();
		return $rows_branch;
	}

	function get_site()
	{
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->select("*");
		$this->dbts->from("ts_bc_master_site");
		$qbranch     = $this->dbts->get();
		$rows_branch = $qbranch->result();
		return $rows_branch;
	}

	function get_location()
	{
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->select("*");
		$this->dbts->from("ts_bc_master_location");
		$qbranch     = $this->dbts->get();
		$rows_branch = $qbranch->result();
		return $rows_branch;
	}

	function get_object()
	{
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->select("*");
		$this->dbts->from("ts_bc_master_object");
		$qbranch     = $this->dbts->get();
		$rows_branch = $qbranch->result();
		return $rows_branch;
	}

	function get_object_detail()
	{
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->select("*");
		$this->dbts->from("ts_bc_master_object_detail");
		$qbranch     = $this->dbts->get();
		$rows_branch = $qbranch->result();
		return $rows_branch;
	}

	function get_pja()
	{
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->select("*");
		$this->dbts->from("ts_bc_master_pja");
		$qbranch     = $this->dbts->get();
		$rows_branch = $qbranch->result();
		return $rows_branch;
	}

	function get_quick_action()
	{
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->select("*");
		$this->dbts->from("ts_bc_master_quickaction");
		$qbranch     = $this->dbts->get();
		$rows_branch = $qbranch->result();
		return $rows_branch;
	}

	function get_category_type()
	{
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->select("*");
		$this->dbts->from("ts_bc_master_categorytype");
		$qbranch     = $this->dbts->get();
		$rows_branch = $qbranch->result();
		return $rows_branch;
	}

	function berecord()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id 	       = $this->sess->user_id;
		$user_level      = $this->sess->user_level;
		$user_parent     = $this->sess->user_parent;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
		$privilegecode 	 = $this->sess->user_id_role;
		$user_id_fix     = $user_id;

		/* $this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_type <>", "TJAM");
 */
		/* if ($this->sess->user_type == 2)
		{
			if($privilegecode == 1){
			$this->db->where("vehicle_user_id", $user_parent);
				$this->db->or_where("vehicle_company", $user_parent);
	    }else if($privilegecode == 2){
			$this->db->where("vehicle_user_id", $user_parent);
				$this->db->or_where("vehicle_company", $user_parent);
	    }else if($privilegecode == 3){
			$this->db->where("vehicle_user_id", $user_parent);
				$this->db->or_where("vehicle_company", $user_parent);
	    }else if($privilegecode == 4){
			$this->db->where("vehicle_user_id", $user_parent);
				$this->db->or_where("vehicle_company", $user_parent);
	    }else if($privilegecode == 5){
			$this->db->where("vehicle_company", $user_company);
	    }else if($privilegecode == 6){
			$this->db->where("vehicle_company", $user_company);
	    }else if($privilegecode == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
				$this->db->or_where("vehicle_company", $this->sess->user_company);
	    }else{
			$this->db->where("vehicle_no",99999);
	    }

			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		//tambahan, user group yg open playback report
		if ($this->sess->user_group <> 0)
		{
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status", 1);
		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();

		*/

		$rows = array();

		$rows_company = $this->get_company_bylevel();
		$rows_operator = array();

		$this->params["vehicles"] = $rows;
		$this->params["operators"] = $rows_operator;
		$this->params["rcompany"] = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);


			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/beats/v_berecord', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/beats/v_berecord', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/beats/v_berecord', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/beats/v_berecord', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/beats/v_berecord', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/beats/v_berecord', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
			}else {
				$this->params["sidebar"] = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"] = $this->load->view('newdashboard/beats/v_berecord', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}

	function search_berecord()
	{

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$vehicle        = $this->input->post("vehicle");
		$startdate      = $this->input->post("startdate");
		$enddate        = $this->input->post("enddate");
		// $shour          = $this->input->post("shour");
		// $ehour          = $this->input->post("ehour");

		$location_start = $this->input->post("location_start");
		$location_end   = $this->input->post("location_end");
		$startdur       = $this->input->post("s_minute");
		$enddur         = $this->input->post("e_minute");
		$km_start       = $this->input->post("km_start");
		$km_end         = $this->input->post("km_end");

		$type_speed     = $this->input->post("type_speed");
		$type_location  = $this->input->post("type_location");
		$type_duration  = $this->input->post("type_duration");
		$type_km        = $this->input->post("type_km");
		$statusname     = $this->input->post("statusname");
		$statusspeed    = $this->input->post("statusspeed");
		$company        = $this->input->post("company");
		$operator        = $this->input->post("operator");

		if($startdur != "" && $enddur != ""){
			$startdur = $startdur * 60;
			$enddur = $enddur * 60;
		}

		$report = "berecord_"; // new report
		$report_sum = "berecord_summary_";

		// $sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
		// $edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));

		$m1 = date("F", strtotime($startdate));
		$m2 = date("F", strtotime($enddate));
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;

		$error = "";
		$rows_summary = "";

		$location_list = array("location","location_off","location_idle");

			// INI DIHILANGKAN NANTI KALAU SUDAH TEST PERFORMA
			if ($this->sess->user_id != 4408) {
				if ($vehicle == "")
				{
					$error .= "- Invalid Vehicle. Silahkan Pilih Kendaraan! \n";
				}
			}
			// INI DIHILANGKAN NANTI KALAU SUDAH TEST PERFORMA

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

		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
				// $dbtable = "location_januari_2023_bak";
			$dbtable2_sum = $report_sum."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			$dbtable2_sum = $report_sum."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			$dbtable2_sum = $report_sum."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			$dbtable2_sum = $report_sum."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			$dbtable2_sum = $report_sum."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			$dbtable2_sum = $report_sum."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			$dbtable2_sum = $report_sum."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			$dbtable2_sum = $report_sum."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			$dbtable2_sum = $report_sum."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			$dbtable2_sum = $report_sum."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			$dbtable2_sum = $report_sum."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			$dbtable2_sum = $report_sum."desember_".$year;
			break;
		}
			$privilegecode 	 = $this->sess->user_id_role;

			$this->dbtrip = $this->load->database("tensor_report",true);
			$this->dbtrip->order_by("berecord_vehicle_no","asc");
			//$this->dbtrip->order_by("berecord_vehicle_no","asc");
			$this->dbtrip->where("berecord_vehicle_company", $company);
			$this->dbtrip->where("berecord_date >=", date("Y-m-d", strtotime($startdate)));
			$this->dbtrip->where("berecord_date <=", date("Y-m-d", strtotime($enddate)));
			// $this->dbtrip->where("berecord_flag", 0);
			$q       = $this->dbtrip->get($dbtable);
			$rows    = $q->result();
			$rowsall = $rows;

			// echo "<pre>";
			// // var_dump($dbtable.'-'.$company.'-'.$startdate.'-'.$enddate);die();
			// var_dump($dbtable_sum);die();
			// echo "<pre>";


			$params['data']        = $rowsall;
			$params['dbtable']     = $dbtable;
			$params['dbtable_sum'] = $dbtable_sum;

			$params['startdate']   = $startdate;
			$params['enddate']     = $enddate;
			$html                  = $this->load->view("newdashboard/beats/v_berecord_result", $params, true);

			$callback['error']     = false;
			$callback['html']      = $html;
			echo json_encode($callback);
			//return;

	}

	function get_company_bylevel(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");

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
			}elseif ($privilegecode == 4) {
				$this->db->where("company_created_by", $this->sess->user_parent);
			}elseif ($privilegecode == 5) {
				$this->db->where("company_id", $this->sess->user_company);
			}elseif ($privilegecode == 6) {
				$this->db->where("company_id", $this->sess->user_company);
			}


		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}

	function get_data_operator($idcompany){

		//khusus FAD
		if($idcompany == 1963){
			$idcompany_sync = 5418;
		}else{
			$idcompany_sync = 0;
		}

		$jabatan_list = array("Operator","Mekanik");
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->order_by("karyawan_bc_name","asc");
		$this->dbts->where("karyawan_bc_company_id", $idcompany_sync);
		$this->dbts->where_in("karyawan_bc_position", $jabatan_list);
		$qd = $this->dbts->get("ts_karyawan_beraucoal");
		$rd = $qd->result();
		if($qd->num_rows() > 0){
			$options = "<option value='0' selected='selected' >--Select Operator--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->karyawan_bc_sid . "'>".$obj->karyawan_bc_name." "."(".$obj->karyawan_bc_sid.")"."</option>";
			}

			echo $options;
			return;
		}

		return $rd;
	}

}
