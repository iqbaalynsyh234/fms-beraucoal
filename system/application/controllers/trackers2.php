<?php
include "base.php";

class Trackers2 extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Trackers2()
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
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("m_projectschedule");
	}

  function index(){
    $this->params['code_view_menu'] = "monitor";
    $this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
    $this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
    $this->params["content"]        = $this->load->view('dashboard/error/v_cantloadpage', $this->params, true);
    $this->load->view("dashboard/template_dashboard", $this->params);
  }

}
