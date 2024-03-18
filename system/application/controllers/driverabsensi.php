<?php
include "base.php";

class Driverabsensi extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Driverabsensi()
	{
		parent::Base();
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_poipoolmaster");
		$this->load->model("gpsmodel");
	}

  function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$privilegecode = $this->sess->user_id_role;

    $user_company  = $this->sess->user_company;
    $datadriver    = $this->getdriver($user_company);

		// echo "<pre>";
		// var_dump($datadriver);die();
		// echo "<pre>";

		$this->params['code_view_menu'] = "report";
    $this->params['datadriver']     = $datadriver;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverabsensi/v_home_driverabsensi', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverabsensi/v_home_driverabsensi', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverabsensi/v_home_driverabsensi', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverabsensi/v_home_driverabsensi', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverabsensi/v_home_driverabsensi', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driverabsensi/v_home_driverabsensi', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
  }

  function searchthis(){
    ini_set('display_errors', 1);
		//ini_set('memory_limit', '2G');
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$driver    = $this->input->post("driver");
		$startdate = $this->input->post("startdate");
		$enddate   = $this->input->post("enddate");
		$shour     = "00:00:00";
		$ehour     = "23:59:59";
		$shifttype = $this->input->post("shifttype");
		$periode   = $this->input->post("periode");

		$nowdate    = date("Y-m-d");
		$nowday     = date("d");
		$nowmonth   = date("m");
		$nowyear    = date("Y");
		$lastday    = date("t");

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

		// print_r($sdate." ".$edate);exit();

		$m1      = date("F", strtotime($sdate));
		$m2      = date("F", strtotime($edate));
		$year    = date("Y", strtotime($sdate));
		$year2   = date("Y", strtotime($edate));
		$rows    = array();
		$total_q = 0;

		$error = "";
		$rows_summary = "";

		if ($driver == "")
		{
			$error .= "- Invalid Driver. Silahkan Pilih salah satu driver! \n";
		}
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

    $datasearch 						= $this->m_poipoolmaster->searchthisabsensi("ts_driver_absensi", $driver, $sdate, $edate, $shifttype);
    // $driver.'-'.$sdate.'-'.$edate.'-'.$shifttype
    // echo "<pre>";
    // var_dump($datasearch);die();
    // echo "<pre>";

    $this->params['data'] 			     = $datasearch;
		$this->params['startdate'] 			 = $sdate;
		$this->params['enddate'] 			   = $edate;
		$html                            = $this->load->view('newdashboard/driverabsensi/v_driverabsensi_result', $this->params, true);
		$callback['html']                = $html;
		$callback['data']                = $datasearch;
		echo json_encode($callback);
  }

  function getdriver($company) {
  	$this->dbtransporter = $this->load->database('transporter',true);
  	$this->dbtransporter->select("*");
  	$this->dbtransporter->from("driver");
    $this->dbtransporter->where("driver_company", $company);
  	$q = $this->dbtransporter->get();
    return $q->result_array();
}



}
