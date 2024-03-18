<?php
include "base.php";

class Dashboard2 extends Base {

	function Dashboard2()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

	}

	function production()
	{
		ini_set('display_errors', 1);

		

		$this->params['code_view_menu'] = "monitoring";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/board/v_dashboard_production', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_production", $this->params);
	}
	
	function truck()
	{
		ini_set('display_errors', 1);

		
		$this->params['code_view_menu'] = "monitoring";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/board/v_dashboard_truck', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_truck", $this->params);
	}
	
	function production2()
	{
		ini_set('display_errors', 1);

		
		$this->params['code_view_menu'] = "monitoring";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/board/v_dashboard_production2', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_production2", $this->params);
	}
	
	function truck2()
	{
		ini_set('display_errors', 1);

		
		$this->params['code_view_menu'] = "monitoring";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/board/v_dashboard_truck2', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_truck2", $this->params);
	}
	
	function violation2()
	{
		ini_set('display_errors', 1);

		
		$this->params['code_view_menu'] = "monitoring";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/board/v_dashboard_violation2', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_violation2", $this->params);
	}

	




















}
