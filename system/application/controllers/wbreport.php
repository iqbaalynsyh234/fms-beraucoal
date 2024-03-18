<?php
include "base.php";

class Wbreport extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Wbreport()
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
    $this->load->model("m_wbreport");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
	}

	function index(){
		if(! isset($this->sess->user_type)){
			redirect('dashboard');
		}

		// REDIRECT LANGSUNG KE PAGE TMS
		if ($this->sess->user_id == "4098") {
			redirect(base_url()."tms/");
		}

		$this->params['vehicle']             = $this->m_wbreport->getdevice();

		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/weighboard/v_wbreport', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

  function searchreport(){
		ini_set('display_errors', 1);
		//ini_set('memory_limit', '2G');
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle    = explode("-", $this->input->post("vehicle"));
		$startdate  = $this->input->post("startdate");
		$enddate    = $this->input->post("enddate");
		$shour      = $this->input->post("shour");
		$ehour      = $this->input->post("ehour");
		$periode    = $this->input->post("periode");

    $nowdate  = date("Y-m-d");
		$nowday   = date("d");
		$nowmonth = date("m");
		$nowyear  = date("Y");
		$lastday  = date("t");

    if($periode == "custom"){
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}else if($periode == "yesterday"){
			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		}else if($periode == "last7"){
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if($nowday <= 7){
				$firstday = 1;
			}
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59"));
		}
		else if($periode == "last30"){
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59"));
		}
		else{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}

    $m1           = date("F", strtotime($startdate));
		$m2           = date("F", strtotime($enddate));
		$year         = date("Y", strtotime($startdate));
		$year2        = date("Y", strtotime($enddate));
		$rows         = array();
		$total_q      = 0;
		$error        = "";
		$rows_summary = "";

		if ($vehicle == "")
		{
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
		}
		if ($m1 != $m2)
		{
			$error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
		}

		if ($year != $year2)
		{
			$error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
		}

    if ($vehicle[0] == "all") {
      $thisreport = $this->m_wbreport->searchthis($vehicle[0], $sdate, $edate);
    }else {
      $vehiclefix = $vehicle[1].' '.$vehicle[2];
      $thisreport = $this->m_wbreport->searchthis($vehiclefix, $sdate, $edate);
    }

    // echo "<pre>";
    // var_dump($thisreport);die();
    // echo "<pre>";

		$this->params['content'] = $thisreport;
		$html                    = $this->load->view('newdashboard/weighboard/v_wbreport_result', $this->params, true);
		$callback["html"]        = $html;
		$callback["report"]      = $thisreport;

		echo json_encode($callback);
	}

}
