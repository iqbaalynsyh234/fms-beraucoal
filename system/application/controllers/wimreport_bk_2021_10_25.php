<?php
include "base.php";

class Wimreport extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->model("dashboardmodel");
    $this->load->model("m_wimreport");
	}

	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$rows                           = $this->dashboardmodel->getvehicle_report();
		$rows_company                   = $this->dashboardmodel->get_company_bylevel();
		$this->params["vehicles"]       = $rows;
		$this->params["rcompany"]       = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/wimreport/v_home_wimreport', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function search_report(){
		$vehicle   = $this->input->post("vehicle");
	  $startdate = $this->input->post("startdate");
	  $shour     = $this->input->post("shour");
	  $enddate   = $this->input->post("enddate");
	  $ehour     = $this->input->post("ehour");

	  $sdate     = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00") + 60*60*1);
	  $edate     = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":00") + 60*60*1);

		$getreport            = $this->m_wimreport->getreportnow("historikal_integrationwim_unit", $vehicle, $sdate, $edate);
		$this->params['data'] = $getreport;
		// echo "<pre>";
		// var_dump($getreport);die();
		// echo "<pre>";

		$html = $this->load->view("newdashboard/wimreport/v_wimreport_result", $this->params, true);
    $callback['error'] = false;
    $callback['html']  = $html;
    echo json_encode($callback);
	}


















}
