<?php
include "base.php";

class Livestream extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Livestream(){
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
    $this->load->model("m_securityevidence");
	}

	function index(){
		if(! isset($this->sess->user_type)){
			redirect('dashboard');
		}

		// REDIRECT LANGSUNG KE PAGE TMS
		if ($this->sess->user_id == "4098") {
			redirect(base_url()."tms/");
		}

    $this->params['code_view_menu'] = "monitor";
    // $session                     = $this->getsessionlogin();
    $this->params['session']        = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=020200360002&jsession=64695a0d-93bb-49c8-9b47-3994135cbaf4";

    // echo "<pre>";
		// var_dump($session);die();
		// echo "<pre>";

    $this->load->view("dashboard/livestream/v_livestream", $this->params);


    // $this->params["header"]   = $this->load->view('dashboard/header', $this->params, true);
		// // $this->params["sidebar"]  = $this->load->view('dashboard/sidebar', $this->params, true);
		// $this->params["content"]  = $this->load->view('dashboard/livestream/v_livestream', $this->params, true);
		// $this->load->view("dashboard/template_dashboard_report", $this->params);
	}

  function getsessionlogin(){
		$device          = "020200360002";
		$url             = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=".$device."&jsession=";
		$username        = "IND.LacakMobil";
		$password        = "000000";

		$getthissession  = $this->m_securityevidence->getsession();
		$urlfix          = $url.$getthissession[0]['sess_value'];

		// GET LOGIN DENGAN SESSION LAMA
		$loginlama       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
    if ($loginlama) {
      $getlogindecode = json_decode($loginlama);
      if (!$getlogindecode) {
        if ($getlogindecode->message == "Session does not exist!") {
          $loginbaru       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
          $loginbarudecode = json_decode($loginbaru);
					$urlfix          = $loginbarudecode->jsession;
        }
      }else {
          $urlfix = $urlfix;
        }
      }

      return $urlfix;
			// echo "<pre>";
			// var_dump($urlfix);die();
			// echo "<pre>";
	}


}
