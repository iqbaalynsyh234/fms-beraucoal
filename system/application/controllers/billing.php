<?php
include "base.php";

class Billing extends Base {

	function Billing()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

	}

	function index()
	{
		ini_set('display_errors', 1);
    $resultactive                   = $this->dashboardmodel->vehicleactive();
    $resultexpired                  = $this->dashboardmodel->vehicleexpired();
    $resulttotaldev                 = $this->dashboardmodel->totaldevice();

		$this->params["resultactive"]   = $resultactive;
		$this->params['code_view_menu'] = "billing";

		// echo "<pre>";
		// var_dump($this->params["resultactive"]);die();
		// echo "<pre>";

		$this->params["header"]          = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]         = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]     = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]         = $this->load->view('dashboard/billing/v_active', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

  function active()
	{
		ini_set('display_errors', 1);
    $resultactive                   = $this->dashboardmodel->vehicleactive();

		$this->params["resultactive"]   = $resultactive;
		$this->params['code_view_menu'] = "billing";

		// echo "<pre>";
		// var_dump($this->params["resultactive"]);die();
		// echo "<pre>";

		$this->params["header"]          = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]         = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]     = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]         = $this->load->view('dashboard/billing/v_active', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

  function expired()
	{
		ini_set('display_errors', 1);
    $resultexpired                  = $this->dashboardmodel->vehicleexpired();

		$this->params["resultexpired"]   = $resultexpired;
		$this->params['code_view_menu'] = "billing";

		// echo "<pre>";
		// var_dump($this->params["resultactive"]);die();
		// echo "<pre>";

		$this->params["header"]          = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]         = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]     = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]         = $this->load->view('dashboard/billing/v_expired', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

  function devices()
	{
		ini_set('display_errors', 1);
    $resulttotaldev               = $this->dashboardmodel->totaldevice();

		$this->params["resulttotaldev"] = $resulttotaldev;
		$this->params['code_view_menu'] = "billing";

		// echo "<pre>";
		// var_dump($this->params["resultactive"]);die();
		// echo "<pre>";

		$this->params["header"]          = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]         = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]     = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]         = $this->load->view('dashboard/billing/v_totaldevice', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}
}
