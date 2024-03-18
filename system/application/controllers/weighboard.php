<?php
include "base.php";

class Weighboard extends Base {

	function __construct()
	{
		parent::Base();
		//$this->load->model("safetydashboardmodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_weighboard");
	}

  function index(){
    $this->params['code_view_menu'] = "monitor";
    $gettotalgross                  = $this->m_weighboard->totalgross();
    $totalgrossfix                  = number_format($gettotalgross[0]['total_gross'], "0",",",".");
    $gettotalnetto                  = $this->m_weighboard->totalnetto();
    $totalnettofix                  = number_format($gettotalnetto[0]['total_netto'], "0",",",".");
    $getgrossbyvehicle              = $this->m_weighboard->grossbyvehicle("webtracking_ts_bibtrans");
    $getnettobyvehicle              = $this->m_weighboard->nettobyvehicle("webtracking_ts_bibtrans");
    $gettotaldata                   = $this->m_weighboard->totaldata();

    $this->params['totalgross']        = $totalgrossfix;
    $this->params['totalnetto']        = $totalnettofix;
    $this->params['getgrossbyvehicle'] = $getgrossbyvehicle;
    $this->params['getnettobyvehicle'] = $getnettobyvehicle;
    $this->params['totaldata']         = $gettotaldata;

    // echo "<pre>";
    // var_dump($getgrossbyvehicle[4]['totalgrosspervehicle']);die();
    // echo "<pre>";

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/weighboard/v_home_weighboard', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
  }


}
?>
