<?php
include "base.php";

class Securityevidence extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Securityevidence(){
		parent::Base();
    // DASHBOARD START
    $this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
    // DASHBOARD END
		$this->load->model("gpsmodel");
    $this->load->model("m_safetyhandle");
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

    $this->params['data']      = $this->m_safetyhandle->getdevice();
    $this->params['alarmtype'] = $this->m_safetyhandle->getalarmtype();

    $this->params['code_view_menu'] = "report";

    // echo "<pre>";
		// var_dump($this->params['alarmtype']);die();
		// echo "<pre>";

    $this->params["header"]   = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]  = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"]  = $this->load->view('dashboard/report/v_safetyhandle', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function searchreport(){
		$vehicle          = explode("@", $this->input->post("vehicle"));
		$startdate        = $this->input->post("startdate");
		$shour            = $this->input->post("shour");
		$startdatefix     = date("Y-m-d H:i:s", strtotime($startdate." ".$shour.":00"));
		$enddate          = $this->input->post("enddate");
		$ehour            = $this->input->post("ehour");
		$enddatefix       = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour.":00"));
		$alarmtype        = $this->input->post("alarmtype");
		$alarmfix 			  = $this->input->post("alarmfix");
		$alarmtypeexplode = explode(",", $alarmfix);
		$loopalarmtype    = "";
		$where            = array();
		$pratext          = "alarm_";
		$month            = date("F");
		$year             = date("Y");
		$table            = strtolower($pratext.$month.'_'.$year);

		// $vehicle.'-'.$startdate.'-'.$shour.'-'.$enddate.'-'.$ehour.'-'.$alarmtype

		if ($alarmtype != "All") {
			$thisreport = $this->m_safetyhandle->searchthisreport($table, $vehicle[0], $startdatefix, $enddatefix, $alarmtypeexplode);
		}else {
			$thisreport = $this->m_safetyhandle->searchthisreport($table, $vehicle[0], $startdatefix, $enddatefix, "ALL");
		}

		$this->params['content'] = $thisreport;
		$html                    = $this->load->view('dashboard/report/v_safetyhandle_reportresult', $this->params, true);
		$callback["html"]        = $html;
		$callback["report"]      = $thisreport;

		// echo "<pre>";
		// var_dump($getdata);die();
		// echo "<pre>";
		echo json_encode($callback);
	}

	function getinfodetail(){
		$alert_id   = $this->input->post("alert_id");
		$sdate      = $this->input->post("sdate");
		$pratext    = "alarm_";
		$month      = date("F");
		$year       = date("Y");
		$table      = strtolower($pratext.$month.'_'.$year);

		$reportdetail = $this->m_safetyhandle->getdetailreport($table, $alert_id, $sdate);

		// echo "<pre>";
		// var_dump($reportdetail);die();
		// echo "<pre>";

		$this->params['content'] = $reportdetail;
		$html                    = $this->load->view('dashboard/report/v_safetyhandle_informationdetail', $this->params, true);
		$callback["html"]        = $html;
		$callback["report"]      = $reportdetail;


		echo json_encode($callback);
	}

  function livestream(){
		if(! isset($this->sess->user_type)){
			redirect('dashboard');
		}

    $this->params['code_view_menu'] = "report";

    // echo "<pre>";
		// var_dump($this->params['alarmtype']);die();
		// echo "<pre>";

    $this->params["header"]   = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]  = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"]  = $this->load->view('dashboard/livestream/v_livestream', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function apilogin(){
		$url     = $_POST['url'];
		$content = file_get_contents($url);
		// echo "<pre>";
		// var_dump($content);die();
		// echo "<pre>";
		echo json_encode($content);
	}

	function apigetvehicledata(){
		$url     = $_POST['url'];
		$content = file_get_contents($url);
		// echo "<pre>";
		// var_dump($content);die();
		// echo "<pre>";
		echo json_encode($content);
	}

	function vehiclelive(){
		$url                     = $_POST['url'];
		$this->params['content'] = file_get_contents($url);
		$html                    = $this->load->view('dashboard/livestream/v_vehiclelive', $this->params, true);
		$callback["html"]        = $html;
		// echo "<pre>";
		// var_dump($html);die();
		// echo "<pre>";
		echo json_encode($callback);
	}

}
