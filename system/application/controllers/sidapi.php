<?php
include "base.php";

class Sidapi extends Base {
	function __construct()
	{
		parent::__construct();

		$this->load->model("gpsmodel");
		$this->load->model("configmodel");
		$this->load->model("m_securityevidence");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');

	}
	
	//Get Sync
	function post_sid($vusername="XIIKMs",$vpassname="XIIKM")
	{
		
		$data = $this->sync_sid($vusername,$vpassname);
		//$data_json = json_decode($data);
		
		print_r($data->message);exit();

	}

	function sync_sid($vusername,$vpassname){
		
		$dataforsend = array(
			"username"  => $vusername,
			"password"  => $vpassname
		);
				
		$url_submit = "http://beats-dev.beraucoal.co.id/beats2/h2h/access/login";
		$token 		  = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo3ODExOCwiaWQiOjYyOTIzLCJlbWFpbCI6ImlsaGFtLnRyaXBvZXRyYUBmdXNpMjQuY29tIiwidXNlcm5hbWUiOiJXNFFUTyJ9.hNbX6fIRq9jwyT2m5wftuF9zjJKVGIb4IUySbCulffg";
		$data_param = json_encode($dataforsend, JSON_NUMERIC_CHECK);
		$ch = curl_init($url_submit);
		$headers = array(
					 "Accept: application/json",
					 "Content-Type: application/json",
					 "x-api-key:".$token,
				);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$result     = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);
		
		if ($result === FALSE) {
				die("SID Sync failed: " . curL_error($ch). " \r\n");
		}

		curl_close($ch);

		$obj = json_decode($result); 
		
		return $obj;
		
	}
}
