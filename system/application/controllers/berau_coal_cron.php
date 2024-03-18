<?php
include "base.php";

class Berau_coal_cron extends Base {
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

	//EMPLOYEE
	function sync_data_karyawan($codecompany="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON SYNC DATA KARYAWAN CHECK IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON SYNC DATA KARYAWAN CHECK Start WIB          : ". $cronstartdate . "\r\n");
		print_r("CRON SYNC DATA KARYAWAN CHECK Start WITA           : ". $nowtime_wita . "\r\n");

		$data_karyawan_beraucoal = $this->get_master_karyawan_beraucoal($codecompany);
		$data_json               = json_decode($data_karyawan_beraucoal);

		for ($i=0; $i < sizeof($data_json); $i++) {
		  $companyCode = $data_json[$i]->companyCode;
		  $companyId = $data_json[$i]->companyId;
		  $sidCode     = $data_json[$i]->sidCode;
		  $name        = $data_json[$i]->name;
		  $company     = $data_json[$i]->company;
		  $id          = $data_json[$i]->id;
		  $position    = $data_json[$i]->position;

		  $check_data = $this->check_data_karyawan($sidCode, $name);
		  $total_data = sizeof($check_data);

		  if ($total_data < 1) {
			// INSERT JIKA DATA BELUM ADA
			$data_insert = array(
			  "karyawan_bc_companycode" => $companyCode,
			  "karyawan_bc_company_id"  => $companyId,
			  "karyawan_bc_sid"         => $sidCode,
			  "karyawan_bc_name"        => $name,
			  "karyawan_bc_company"     => $company,
			  "karyawan_bc_id_sync"     => $id,
			  "karyawan_bc_position"    => $position
			);

			$insert = $this->insert_data_karyawan($data_insert, "ts_karyawan_beraucoal");
			  if ($insert) {
				printf("===== SUCCESS INSERT DATA \r\n");
			  }else {
				printf("===== FAILED INSERT DATA \r\n");
			  }
		  }else {
			printf("===== SKIP. ALREADY EXISTS \r\n");
		  }

		  // echo "<pre>";
		  // var_dump($total_data);die();
		  // echo "<pre>";
		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		print_r("CRON LATENCY : ". $duration_sec . " Second \r\n");
	}

	function check_data_karyawan($sid, $name)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->where("karyawan_bc_sid", $sid);
		$this->dbts->where("karyawan_bc_name", $name);
		$result = $this->dbts->get("ts_karyawan_beraucoal")->result_array();
		return $result;
	}

	function insert_data_karyawan($data, $table)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert($table, $data);
	}

	function get_master_karyawan_beraucoal($code)
	{
		//printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");
		// Company Code API Get Master Data Karyawan Berau Coal :
		// ZOQ9Q FAD
		// M5WRZ KDC
		// 0VRHO BUMA
		// SUBOD Ricobana
		// O3FFP MTN

		$apiKey        = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo3ODExOCwiaWQiOjYyOTIzLCJlbWFpbCI6ImlsaGFtLnRyaXBvZXRyYUBmdXNpMjQuY29tIiwidXNlcm5hbWUiOiJXNFFUTyJ9.hNbX6fIRq9jwyT2m5wftuF9zjJKVGIb4IUySbCulffg";
		$authorization = "Authorization:".$apiKey;
		$company_code  = $code;
		$url           = "http://beats-dev.beraucoal.co.id/sid2/employeeInfoDms/byCode?code=".$company_code;
		$feature       = array();

    $headers = array(
         'x-api-key: '.$apiKey
    );

    // Send request to Server
    $ch = curl_init($url);
    // To save response in a variable from server, set headers;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    // Get response
    $response = curl_exec($ch);
    // Decode
    // $result = json_decode($response);
    return $response;
	}

	//MASTER SITE
	function sync_master_site()
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON SYNC DATA MASTER SITE CHECK IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON SYNC DATA MASTER SITE CHECK Start WIB          : ". $cronstartdate . "\r\n");
		print_r("CRON SYNC DATA MASTER SITE CHECK Start WITA           : ". $nowtime_wita . "\r\n");

		$data = $this->get_master_site();
		$data_json               = json_decode($data);

		for ($i=0; $i < count($data_json); $i++) {
		  $idsync		 	= $data_json[$i]->id;
		  $name 			= $data_json[$i]->name;
		  $shortName     	= $data_json[$i]->shortName;
		  $isActive        	= $data_json[$i]->isActive;
		  $centerLatitude   = $data_json[$i]->centerLatitude;
		  $centerLongitude  = $data_json[$i]->centerLongitude;


		  $check_data = $this->check_master_site($idsync, $name);
		  $total_data = count($check_data);

		  if ($total_data < 1) {
			// INSERT JIKA DATA BELUM ADA
			$data_insert = array(
			  "master_site_id_sync" 	=> $idsync,
			  "master_site_name"  		=> $name,
			  "master_site_shortname"   => $shortName,
			  "master_site_active"     	=> $isActive,
			  "master_site_lat"     	=> $centerLatitude,
			  "master_site_long"     	=> $centerLongitude
			);

			$insert = $this->insert_master_site($data_insert, "ts_bc_master_site");
			  if ($insert) {
				printf("===== SUCCESS INSERT DATA \r\n");
			  }else {
				printf("===== FAILED INSERT DATA \r\n");
			  }
		  }else {
			printf("===== SKIP. ALREADY EXISTS \r\n");
		  }


		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		print_r("CRON LATENCY : ". $duration_sec . " Second \r\n");

	}

	function get_master_site()
	{

		$apiKey        = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo3ODExOCwiaWQiOjYyOTIzLCJlbWFpbCI6ImlsaGFtLnRyaXBvZXRyYUBmdXNpMjQuY29tIiwidXNlcm5hbWUiOiJXNFFUTyJ9.hNbX6fIRq9jwyT2m5wftuF9zjJKVGIb4IUySbCulffg";
		$authorization = "Authorization:".$apiKey;
		$url           = "http://beats-dev.beraucoal.co.id/beats2/api/location?filter[isActive]=true&filter[type.id]=100";
		$feature       = array();

		$headers = array(
			 'x-api-key: '.$apiKey
		);

		// Send request to Server
		$ch = curl_init($url);
		// To save response in a variable from server, set headers;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// Get response
		$response = curl_exec($ch);
		// Decode
		// $result = json_decode($response);
		return $response;
	}

	function check_master_site($sid, $name)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->where("master_site_id_sync", $sid);
		$this->dbts->where("master_site_name", $name);
		$result = $this->dbts->get("ts_bc_master_site")->result_array();
		return $result;
	}

	function insert_master_site($data, $table)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert($table, $data);
	}

	//MASTER LOCATION
	function sync_master_location()
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON SYNC DATA  CHECK IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON SYNC DATA CHECK Start WIB          : ". $cronstartdate . "\r\n");
		print_r("CRON SYNC DATA CHECK Start WITA           : ". $nowtime_wita . "\r\n");

		$data = $this->get_master_location();
		$data_json = json_decode($data);

		for ($i=0; $i < count($data_json); $i++) {

		  $parent 				= $data_json[$i]->parent;
		  $idsync		 		= $data_json[$i]->id;
		  $name 				= $data_json[$i]->name;
		  $shortName 			= $data_json[$i]->shortName;
		  $isActive        		= $data_json[$i]->isActive;
		  $centerLatitude       = $data_json[$i]->centerLatitude;
		  $centerLongitude      = $data_json[$i]->centerLongitude;

		  $parent_id        	= $parent->id;
		  $parent_name       	= $parent->name;
		  $parent_shortname     = $parent->shortName;

		  $check_data = $this->check_master_location($idsync, $name);
		  $total_data = count($check_data);

		  if ($total_data < 1) {
			// INSERT JIKA DATA BELUM ADA
			$data_insert = array(
			  "master_location_id_sync" 			=> $idsync,
			  "master_location_name"  				=> $name,
			  "master_location_short_name"  		=> $shortName,
			  "master_location_active"     			=> $isActive,
			  "master_location_parent_id"      		=> $parent_id,
			  "master_location_parent_name"  		=> $parent_name,
			  "master_location_parent_short_name"  	=> $parent_shortname,
			  "master_location_lat"  				=> $centerLatitude,
			  "master_location_long"			  	=> $centerLongitude

			);

			$insert = $this->insert_master_location($data_insert, "ts_bc_master_location");
			  if ($insert) {
				printf("===== SUCCESS INSERT DATA \r\n");
			  }else {
				printf("===== FAILED INSERT DATA \r\n");
			  }
		  }else {
			printf("===== SKIP. ALREADY EXISTS \r\n");
		  }


		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		print_r("CRON LATENCY : ". $duration_sec . " Second \r\n");

	}

	function get_master_location()
	{

		$apiKey        = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo3ODExOCwiaWQiOjYyOTIzLCJlbWFpbCI6ImlsaGFtLnRyaXBvZXRyYUBmdXNpMjQuY29tIiwidXNlcm5hbWUiOiJXNFFUTyJ9.hNbX6fIRq9jwyT2m5wftuF9zjJKVGIb4IUySbCulffg";
		$authorization = "Authorization:".$apiKey;
		$url           = "http://beats-dev.beraucoal.co.id/beats2/api/location?filter[isActive]=true&filter[type.id]=200&expand=parent";
		$feature       = array();

		$headers = array(
			 'x-api-key: '.$apiKey
		);

		// Send request to Server
		$ch = curl_init($url);
		// To save response in a variable from server, set headers;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// Get response
		$response = curl_exec($ch);
		// Decode
		// $result = json_decode($response);
		return $response;
	}

	function check_master_location($sid, $name)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->where("master_location_id_sync", $sid);
		$this->dbts->where("master_location_name", $name);
		$result = $this->dbts->get("ts_bc_master_location")->result_array();
		return $result;
	}

	function insert_master_location($data, $table)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert($table, $data);
	}

	// MASTER Object
	function sync_master_object()
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON SYNC DATA MASTER SITE CHECK IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON SYNC DATA MASTER SITE CHECK Start WIB          : ". $cronstartdate . "\r\n");
		print_r("CRON SYNC DATA MASTER SITE CHECK Start WITA           : ". $nowtime_wita . "\r\n");

		$data = $this->get_master_object();
		$data_json               = json_decode($data);

		for ($i=0; $i < count($data_json); $i++) {
		  $idsync		 	= $data_json[$i]->id;
		  $name 			= $data_json[$i]->name;
		  $isActive        	= $data_json[$i]->isActive;

		  $check_data = $this->check_master_object($idsync, $name);
		  $total_data = count($check_data);

		  if ($total_data < 1) {
			// INSERT JIKA DATA BELUM ADA
			$data_insert = array(
			  "master_object_id_sync" 		=> $idsync,
			  "master_object_name"  		=> $name,
			  "master_object_active"     	=> $isActive
			);

			$insert = $this->insert_master_object($data_insert, "ts_bc_master_object");
			  if ($insert) {
				printf("===== SUCCESS INSERT DATA \r\n");
			  }else {
				printf("===== FAILED INSERT DATA \r\n");
			  }
		  }else {
			printf("===== SKIP. ALREADY EXISTS \r\n");
		  }


		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		print_r("CRON LATENCY : ". $duration_sec . " Second \r\n");

	}

	function get_master_object()
	{

		$apiKey        = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo3ODExOCwiaWQiOjYyOTIzLCJlbWFpbCI6ImlsaGFtLnRyaXBvZXRyYUBmdXNpMjQuY29tIiwidXNlcm5hbWUiOiJXNFFUTyJ9.hNbX6fIRq9jwyT2m5wftuF9zjJKVGIb4IUySbCulffg";
		$authorization = "Authorization:".$apiKey;
		$url           = "http://beats-dev.beraucoal.co.id/beats2/api/hseObject?filter[isActive]=true";
		$feature       = array();

		$headers = array(
			 'x-api-key: '.$apiKey
		);

		// Send request to Server
		$ch = curl_init($url);
		// To save response in a variable from server, set headers;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// Get response
		$response = curl_exec($ch);
		// Decode
		// $result = json_decode($response);
		return $response;
	}

	function check_master_object($sid, $name)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->where("master_object_id_sync", $sid);
		$this->dbts->where("master_object_name", $name);
		$result = $this->dbts->get("ts_bc_master_object")->result_array();
		return $result;
	}

	function insert_master_object($data, $table)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert($table, $data);
	}

	// MASTER Object DETAIL
	function sync_master_objectdetail()
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON SYNC DATA CHECK IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON SYNC DATA CHECK Start WIB          : ". $cronstartdate . "\r\n");
		print_r("CRON SYNC DATA CHECK Start WITA           : ". $nowtime_wita . "\r\n");

		$data = $this->get_master_objectdetail();
		$data_json               = json_decode($data);

		for ($i=0; $i < count($data_json); $i++)
		{

			$parent = $data_json[$i]->object;
			$idsync		 	= $data_json[$i]->id;
			$name 			= $data_json[$i]->name;
			$isActive       = $data_json[$i]->isActive;
			$parent_id      = $parent->id;
			$parent_name    = $parent->name;

		  $check_data = $this->check_master_objectdetail($idsync, $name);
		  $total_data = count($check_data);

		  if ($total_data < 1) {
			// INSERT JIKA DATA BELUM ADA
			$data_insert = array(
			  "master_object_detail_id_sync" 		=> $idsync,
			  "master_object_detail_parent_id"  	=> $parent_id,
			  "master_object_detail_parent_name"    => $parent_name,
			  "master_object_detail_name"  			=> $name,
			  "master_object_detail_active"     	=> $isActive
			);

			$insert = $this->insert_master_objectdetail($data_insert, "ts_bc_master_object_detail");
			  if ($insert) {
				printf("===== SUCCESS INSERT DATA \r\n");
			  }else {
				printf("===== FAILED INSERT DATA \r\n");
			  }
		  }else {
			printf("===== SKIP. ALREADY EXISTS \r\n");
		  }


		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		print_r("CRON LATENCY : ". $duration_sec . " Second \r\n");

	}

	function get_master_objectdetail()
	{

		$apiKey        = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo3ODExOCwiaWQiOjYyOTIzLCJlbWFpbCI6ImlsaGFtLnRyaXBvZXRyYUBmdXNpMjQuY29tIiwidXNlcm5hbWUiOiJXNFFUTyJ9.hNbX6fIRq9jwyT2m5wftuF9zjJKVGIb4IUySbCulffg";
		$authorization = "Authorization:".$apiKey;
		$url           = "http://beats-dev.beraucoal.co.id/beats2/api/hseObjectDetail?filter[isActive]=true&expand=object,dataSource";
		$feature       = array();

		$headers = array(
			 'x-api-key: '.$apiKey
		);

		// Send request to Server
		$ch = curl_init($url);
		// To save response in a variable from server, set headers;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// Get response
		$response = curl_exec($ch);
		// Decode
		// $result = json_decode($response);
		return $response;
	}

	function check_master_objectdetail($sid, $name)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->where("master_object_detail_id_sync", $sid);
		$this->dbts->where("master_object_detail_name", $name);
		$result = $this->dbts->get("ts_bc_master_object_detail")->result_array();
		return $result;
	}

	function insert_master_objectdetail($data, $table)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert($table, $data);
	}

	// MASTER Quick Action
	function sync_master_quickaction()
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON SYNC DATA MASTER SITE CHECK IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON SYNC DATA MASTER SITE CHECK Start WIB          : ". $cronstartdate . "\r\n");
		print_r("CRON SYNC DATA MASTER SITE CHECK Start WITA           : ". $nowtime_wita . "\r\n");

		$data = $this->get_master_quickaction();
		$data_json               = json_decode($data);

		for ($i=0; $i < count($data_json); $i++) {
		  $idsync		 	= $data_json[$i]->id;
		  $name 			= $data_json[$i]->name;
		  $desc 			= $data_json[$i]->description;
		  $isActive        	= $data_json[$i]->isActive;

		  $check_data = $this->check_master_quickaction($idsync, $name);
		  $total_data = count($check_data);

		  if ($total_data < 1) {
			// INSERT JIKA DATA BELUM ADA
			$data_insert = array(
			  "master_quickaction_id_sync" 		=> $idsync,
			  "master_quickaction_name"  		=> $name,
			  "master_quickaction_desc"  		=> $desc,
			  "master_quickaction_active"     	=> $isActive
			);

			$insert = $this->insert_master_quickaction($data_insert, "ts_bc_master_quickaction");
			  if ($insert) {
				printf("===== SUCCESS INSERT DATA \r\n");
			  }else {
				printf("===== FAILED INSERT DATA \r\n");
			  }
		  }else {
			printf("===== SKIP. ALREADY EXISTS \r\n");
		  }


		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		print_r("CRON LATENCY : ". $duration_sec . " Second \r\n");

	}

	function get_master_quickaction()
	{

		$apiKey        = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo3ODExOCwiaWQiOjYyOTIzLCJlbWFpbCI6ImlsaGFtLnRyaXBvZXRyYUBmdXNpMjQuY29tIiwidXNlcm5hbWUiOiJXNFFUTyJ9.hNbX6fIRq9jwyT2m5wftuF9zjJKVGIb4IUySbCulffg";
		$authorization = "Authorization:".$apiKey;
		$url           = "http://beats-dev.beraucoal.co.id/beats2/api/quickAction?filter[isActive]=true";
		$feature       = array();

		$headers = array(
			 'x-api-key: '.$apiKey
		);

		// Send request to Server
		$ch = curl_init($url);
		// To save response in a variable from server, set headers;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// Get response
		$response = curl_exec($ch);
		// Decode
		// $result = json_decode($response);
		return $response;
	}

	function check_master_quickaction($sid, $name)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->where("master_quickaction_id_sync", $sid);
		$this->dbts->where("master_quickaction_name", $name);
		$result = $this->dbts->get("ts_bc_master_quickaction")->result_array();
		return $result;
	}

	function insert_master_quickaction($data, $table)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert($table, $data);
	}

	// MASTER CATEGORI TYPE
	function sync_master_categorytype()
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON SYNC DATA CHECK IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON SYNC DATA CHECK Start WIB          : ". $cronstartdate . "\r\n");
		print_r("CRON SYNC DATA CHECK Start WITA           : ". $nowtime_wita . "\r\n");

		$data = $this->get_master_categorytype();
		$data_json               = json_decode($data);

		for ($i=0; $i < count($data_json); $i++)
		{
		  $idsync		 	= $data_json[$i]->id;
		  $name 			= $data_json[$i]->name;
		  $desc 			= $data_json[$i]->description;
		  $isActive        	= $data_json[$i]->isActive;
		  $isClosed        	= $data_json[$i]->isClosed;

		  $check_data = $this->check_master_categorytype($idsync, $name);
		  $total_data = count($check_data);

		  if ($total_data < 1) {
			// INSERT JIKA DATA BELUM ADA
			$data_insert = array(
			  "master_categorytype_id_sync" 	=> $idsync,
			  "master_categorytype_name"  		=> $name,
			  "master_categorytype_desc"  		=> $desc,
			  "master_categorytype_active"     	=> $isActive,
			  "master_categorytype_closed"     	=> $isClosed,
			);

			$insert = $this->insert_master_categorytype($data_insert, "ts_bc_master_categorytype");
			  if ($insert) {
				printf("===== SUCCESS INSERT DATA \r\n");
			  }else {
				printf("===== FAILED INSERT DATA \r\n");
			  }
		  }else {
			printf("===== SKIP. ALREADY EXISTS \r\n");
		  }


		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		print_r("CRON LATENCY : ". $duration_sec . " Second \r\n");

	}

	function get_master_categorytype()
	{

		$apiKey        = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo3ODExOCwiaWQiOjYyOTIzLCJlbWFpbCI6ImlsaGFtLnRyaXBvZXRyYUBmdXNpMjQuY29tIiwidXNlcm5hbWUiOiJXNFFUTyJ9.hNbX6fIRq9jwyT2m5wftuF9zjJKVGIb4IUySbCulffg";
		$authorization = "Authorization:".$apiKey;
		$url           = "http://beats-dev.beraucoal.co.id/beats2/api/categoryType";
		$feature       = array();

		$headers = array(
			 'x-api-key: '.$apiKey
		);

		// Send request to Server
		$ch = curl_init($url);
		// To save response in a variable from server, set headers;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// Get response
		$response = curl_exec($ch);
		// Decode
		// $result = json_decode($response);
		return $response;
	}

	function check_master_categorytype($sid, $name)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->where("master_categorytype_id_sync", $sid);
		$this->dbts->where("master_categorytype_name", $name);
		$result = $this->dbts->get("ts_bc_master_categorytype")->result_array();
		return $result;
	}

	function insert_master_categorytype($data, $table)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert($table, $data);
	}

	//MASTER PJA
	function sync_master_pja()
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON SYNC DATA  CHECK IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON SYNC DATA CHECK Start WIB          : ". $cronstartdate . "\r\n");
		print_r("CRON SYNC DATA CHECK Start WITA           : ". $nowtime_wita . "\r\n");

		$data = $this->get_master_pja();
		$data_json = json_decode($data);

		for ($i=0; $i < count($data_json); $i++) {

		  $parent_location		= $data_json[$i]->location;
		  $parent_type			= $data_json[$i]->pjaType;

		  $idsync		 		= $data_json[$i]->id;
		  $name 				= $data_json[$i]->name;
		  $isActive        		= $data_json[$i]->isActive;

		  $parent_location_id   		= $parent_location->id;
		  $parent_location_name       	= $parent_location->name;
		  $parent_location_short_name   = $parent_location->shortName;
		  $parent_location_lat       	= $parent_location->centerLatitude;
		  $parent_location_long  		= $parent_location->centerLongitude;

		  $parent_type_id   		= $parent_type->id;
		  $parent_type_name       	= $parent_type->name;
		  $parent_type_desc   		= $parent_type->description;

		  $check_data = $this->check_master_pja($idsync, $name);
		  $total_data = count($check_data);

		  if ($total_data < 1) {
			// INSERT JIKA DATA BELUM ADA
			$data_insert = array(
			  "master_pja_id_sync" 				=> $idsync,
			  "master_pja_name"  				=> $name,
			  "master_pja_active"     			=> $isActive,
			  "master_pja_location_id"      	=> $parent_location_id,
			  "master_pja_location_name"  		=> $parent_location_name,
			  "master_pja_location_short_name"  => $parent_location_short_name,
			  "master_pja_location_lat"  		=> $parent_location_lat,
			  "master_pja_location_long"  		=> $parent_location_long,
			  "master_pja_type_id"  				=> $parent_type_id,
			  "master_pja_type_name"			  	=> $parent_type_name,
			  "master_pja_type_desc"			  	=> $parent_type_desc


			);

			$insert = $this->insert_master_location($data_insert, "ts_bc_master_pja");
			  if ($insert) {
				printf("===== SUCCESS INSERT DATA \r\n");
			  }else {
				printf("===== FAILED INSERT DATA \r\n");
			  }
		  }else {
			printf("===== SKIP. ALREADY EXISTS \r\n");
		  }


		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		print_r("CRON LATENCY : ". $duration_sec . " Second \r\n");

	}

	function get_master_pja()
	{

		$apiKey        = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo3ODExOCwiaWQiOjYyOTIzLCJlbWFpbCI6ImlsaGFtLnRyaXBvZXRyYUBmdXNpMjQuY29tIiwidXNlcm5hbWUiOiJXNFFUTyJ9.hNbX6fIRq9jwyT2m5wftuF9zjJKVGIb4IUySbCulffg";
		$authorization = "Authorization:".$apiKey;
		$url           = "http://beats-dev.beraucoal.co.id/beats2/api/pja?filter[isActive]=true&include=id,isActive,name,location,pjaType";
		$feature       = array();

		$headers = array(
			 'x-api-key: '.$apiKey
		);

		// Send request to Server
		$ch = curl_init($url);
		// To save response in a variable from server, set headers;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// Get response
		$response = curl_exec($ch);
		// Decode
		// $result = json_decode($response);
		return $response;
	}

	function check_master_pja($sid, $name)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->where("master_pja_id_sync", $sid);
		$this->dbts->where("master_pja_name", $name);
		$result = $this->dbts->get("ts_bc_master_pja")->result_array();
		return $result;
	}

	function insert_master_pja($data, $table)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		return $this->dbts->insert($table, $data);
	}

	// FOR HAZARD SEND START
	function hazard_send(){
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON HAZARD SEND IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON HAZARD SEND Start WIB : ". $cronstartdate . "\r\n");
		print_r("CRON HAZARD SEND Start WITA : ". $nowtime_wita . "\r\n");


		// $date_for_testing = "2023-07-01"; //TEST OVERSPEED HAZARD
		// $m1               = date("F", strtotime($date_for_testing)); //TEST OVERSPEED HAZARD
		// $year             = date("Y", strtotime($date_for_testing)); //TEST OVERSPEED HAZARD

		$date_for_testing = "2023-09-01"; //TEST NON OVERSPEED HAZARD
		$m1               = date("F", strtotime($date_for_testing)); //TEST NON OVERSPEED HAZARD
		$year             = date("Y", strtotime($date_for_testing)); //TEST NON OVERSPEED HAZARD
		$report           = "alarm_evidence_";
		$reportoverspeed  = "overspeed_hour_";
		$dbtable          = "";
		$dbtableoverspeed = "";

		switch ($m1)
		{
			case "January":
						$dbtable = $report."januari_".$year;
						$dbtableoverspeed = $reportoverspeed."januari_".$year;
			break;
			case "February":
						$dbtable = $report."februari_".$year;
						$dbtableoverspeed = $reportoverspeed."februari_".$year;
			break;
			case "March":
						$dbtable = $report."maret_".$year;
						$dbtableoverspeed = $reportoverspeed."maret_".$year;
			break;
			case "April":
						$dbtable = $report."april_".$year;
						$dbtableoverspeed = $reportoverspeed."april_".$year;
			break;
			case "May":
						$dbtable = $report."mei_".$year;
						$dbtableoverspeed = $reportoverspeed."mei_".$year;
			break;
			case "June":
						$dbtable = $report."juni_".$year;
						$dbtableoverspeed = $reportoverspeed."juni_".$year;
			break;
			case "July":
						$dbtable = $report."juli_".$year;
						$dbtableoverspeed = $reportoverspeed."juli_".$year;
			break;
			case "August":
						$dbtable = $report."agustus_".$year;
						$dbtableoverspeed = $reportoverspeed."agustus_".$year;
			break;
			case "September":
						$dbtable = $report."september_".$year;
						$dbtableoverspeed = $reportoverspeed."september_".$year;
			break;
			case "October":
						$dbtable = $report."oktober_".$year;
						$dbtableoverspeed = $reportoverspeed."oktober_".$year;
			break;
			case "November":
						$dbtable = $report."november_".$year;
						$dbtableoverspeed = $reportoverspeed."november_".$year;
			break;
			case "December":
						$dbtable = $report."desember_".$year;
						$dbtableoverspeed = $reportoverspeed."desember_".$year;
			break;
		}

		// $data_evidence        = $this->get_overspeed_for_hazardsend($dbtableoverspeed);
		$data_evidence = 0;

		// OVERSPEED HAZARD SEND
		// if (sizeof($data_evidence) > 0) {
			if ($data_evidence > 0) {
				// echo "<pre>";
				// var_dump($data_for_sent);die();
				// echo "<pre>";
			// $image = "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";

			$image = "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";
			$deskripsi_1 = "Test Deskripsi 1";
			$deskripsi_2 = "Test Deskripsi 2";
			// DATA ADA, LAKUKAN SEND
			for ($i=0; $i < sizeof($data_evidence); $i++) {
				$id_perusahaan    = $data_evidence[$i]['overspeed_report_id_perusahaan'];
				$id_site 			    = $data_evidence[$i]['overspeed_report_master_site'];
				$deskripsi			  = $data_evidence[$i]['overspeed_report_deskripsi'];
				$id_object        = $data_evidence[$i]['overspeed_report_id_object'];
				$id_objectdetail  = $data_evidence[$i]['overspeed_report_id_objectdetail'];
				$id_quick_action  = $data_evidence[$i]['overspeed_report_id_quick_action'];
				$id_lokasi        = $data_evidence[$i]['overspeed_report_id_lokasi'];
				$id_lokasi_detail = $data_evidence[$i]['overspeed_report_id_lokasi_detail'];
				$report_location  = $data_evidence[$i]['overspeed_report_location'];
				$mobileUUID       = null;
				$id_id_up        = explode("|", $data_evidence[$i]['overspeed_report_supervisor_cr']);
				$id_id_cr        = $data_evidence[$i]['overspeed_report_id_cr'];
				$id_master_site   = $data_evidence[$i]['overspeed_report_master_site'];
				$idOakRegister    = null;
				$gps_time         = $data_evidence[$i]['overspeed_report_gps_time'];
				$id_kategori      = $data_evidence[$i]['overspeed_report_id_kategori'];
				$goldenrule       = $data_evidence[$i]['overspeed_report_goldenrule'];
				$id_pja           = $data_evidence[$i]['overspeed_report_id_pja'];
				$id_pja_child     = $data_evidence[$i]['overspeed_report_id_pja_child'];
				$coordinate       = explode(",", $data_evidence[$i]['overspeed_report_coordinate']);

				$data_for_sent = array(
					// "idPerusahaan" => $id_perusahaan,
					"idPerusahaan" => 5384,
					"ffr" => array(
							"image"       => $image,
							"deskripsi"   => $deskripsi_1,
							"description" => $deskripsi_2
						),
						"deskripsi"         => $deskripsi,
						"idObyek"           => $id_object,
						"idObyekDetil"      => $id_objectdetail,
						"idPja"             => $id_pja,
						"idPjaChild"        => $id_pja_child,
						"idQuickAction"     => $id_quick_action,
						// "idLokasi"          => $id_lokasi, // INI YANG LIVE
						"idLokasi"          => "1141471377", // INI YANG TEST
						"idLokasiDetail"    => $id_pja_child,
						"ketLokasi"         => $report_location,
						"mobileUUID"        => $mobileUUID,
						"idPic"             => $id_id_up[0],
						"idPelapor"         => $id_id_cr,
						"idSite"            => $id_site,
						"idOakRegister"     => $idOakRegister,
						"createDate"        => $gps_time,
						"idKategori"        => $id_kategori,
						"idGoldenRule"      => $goldenrule,
						"locationLatitude"  => $coordinate[0],
						"locationLongitude" => $coordinate[1]
				);

				$submit_this_hazard = $this->submit_hazard($data_for_sent);

				// echo "<pre>";
				// var_dump($data_for_sent);die();
				// echo "<pre>";

				if ($submit_this_hazard->result == true) {
					print_r("SUCCESS SUBMIT HAZARD r\n");
					print_r($submit_this_hazard->message . " \r\n");
					$data_update_status = array(
						"overspeed_report_status_sendhazard" => 1
					);
					$update = $this->update_status_sendhazard($data_evidence[$i]['overspeed_report_id'], $data_update_status, $dbtableoverspeed);
						if ($update) {
							print_r("===SUCCESS UPDATE STATUS SEND HAZARD \r\n");
						}
				}else {
					print_r("===FAILED SUBMIT HAZARD \r\n");
				}
			}
		}else {
			print_r("===DATA TRUE ALERT OVERSPEED TIDAK DITEMUKAN \r\n");
		}

		$data_evidence_non_overspeed        = $this->get_nonoverspeed_for_hazardsend($dbtable);
		// // echo "<pre>";
		// // var_dump($data_evidence_non_overspeed);die();
		// // echo "<pre>";
		// // NON OVERSPEED HAZARD SEND
		if (sizeof($data_evidence_non_overspeed) > 0) {
		// 	// $image = "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";

			$image = "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";
			$deskripsi_1 = "Test Deskripsi 1";
			$deskripsi_2 = "Test Deskripsi 2";
			// DATA ADA, LAKUKAN SEND
			for ($i=0; $i < sizeof($data_evidence_non_overspeed); $i++) {
				$id_perusahaan    = $data_evidence_non_overspeed[$i]['alarm_report_id_perusahaan'];
				$id_site 			    = $data_evidence_non_overspeed[$i]['alarm_report_master_site'];
				$deskripsi			  = $data_evidence_non_overspeed[$i]['alarm_report_deskripsi'];
				$id_object        = $data_evidence_non_overspeed[$i]['alarm_report_id_object'];
				$id_objectdetail  = $data_evidence_non_overspeed[$i]['alarm_report_id_objectdetail'];
				$id_quick_action  = $data_evidence_non_overspeed[$i]['alarm_report_id_quick_action'];
				$id_lokasi        = $data_evidence_non_overspeed[$i]['alarm_report_id_lokasi'];
				$id_lokasi_detail = $data_evidence_non_overspeed[$i]['alarm_report_id_lokasi_detail'];
				$report_location  = $data_evidence_non_overspeed[$i]['alarm_report_location_start'];
				$mobileUUID       = null;
				$id_id_up        = explode("|", $data_evidence_non_overspeed[$i]['alarm_report_supervisor_cr']);
				$id_id_cr        = $data_evidence_non_overspeed[$i]['alarm_report_id_cr'];
				$id_master_site   = $data_evidence_non_overspeed[$i]['alarm_report_master_site'];
				$idOakRegister    = null;
				$gps_time         = $data_evidence_non_overspeed[$i]['alarm_report_start_time'];
				$id_kategori      = $data_evidence_non_overspeed[$i]['alarm_report_id_kategori'];
				$goldenrule       = $data_evidence_non_overspeed[$i]['alarm_report_goldenrule'];
				$id_pja           = $data_evidence_non_overspeed[$i]['alarm_report_id_pja'];
				$id_pja_child     = $data_evidence_non_overspeed[$i]['alarm_report_id_pja_child'];
				$coordinate       = explode(",", $data_evidence_non_overspeed[$i]['alarm_report_coordinate_start']);

				$data_for_sent = array(
					"idPerusahaan" => 5384,
					// "idPerusahaan" => $id_perusahaan,
					"ffr" => array(
							"image"       => $image,
							"deskripsi"   => $deskripsi_1,
							"description" => $deskripsi_2
						),
						"deskripsi"         => $deskripsi,
						"idObyek"           => $id_object,
						"idObyekDetil"      => $id_objectdetail,
						"idPja"             => $id_pja,
						"idPjaChild"        => $id_pja_child,
						"idQuickAction"     => $id_quick_action,
						"idLokasi"          => $id_lokasi, // INI YANG LIVE
						"idLokasi"          => "1141471377", // INI YANG TEST
						"idLokasiDetail"    => $id_pja_child,
						"ketLokasi"         => $report_location,
						"mobileUUID"        => $mobileUUID,
						"idPic"             => $id_id_up[0],
						"idPelapor"         => $id_id_cr,
						"idSite"            => $id_site,
						"idOakRegister"     => $idOakRegister,
						"createDate"        => $gps_time,
						"idKategori"        => $id_kategori,
						"idGoldenRule"      => $goldenrule,
						"locationLatitude"  => $coordinate[0],
						"locationLongitude" => $coordinate[1]
				);

				$submit_this_hazard = $this->submit_hazard($data_for_sent);

				// echo "<pre>";
				// var_dump($data_for_sent);die();
				// echo "<pre>";

				if ($submit_this_hazard->result == true) {
					print_r("SUCCESS SUBMIT HAZARD r\n");
					print_r($submit_this_hazard->message . " \r\n");
					$data_update_status = array(
						"alarm_report_status_sendhazard" => 1
					);
					$update = $this->update_status_sendhazard_nonoverspeed($data_evidence_non_overspeed[$i]['alarm_report_id'], $data_update_status, $dbtable);
						if ($update) {
							print_r("SUCCESS UPDATE STATUS SEND HAZARD \r\n");
						}
				}else {
					print_r("FAILED SUBMIT HAZARD \r\n");
				}
			}
		}else {
			print_r("DATA TRUE ALERT NON OVERSPEED TIDAK DITEMUKAN \r\n");
		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		print_r("CRON LATENCY : ". $duration_sec . " Second \r\n");

	}

	function update_status_sendhazard($alertid, $data, $table){
		$this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->where("overspeed_report_id", $alertid);
		return $this->dbtensor->update($table, $data);
  }

	function update_status_sendhazard_nonoverspeed($alertid, $data, $table){
		$this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->where("alarm_report_id", $alertid);
		return $this->dbtensor->update($table, $data);
  }

	function submit_hazard($dataforsent){
		$url_submit = "http://beats-dev.beraucoal.co.id/beats/mobile/input/hazard";
		$token 		  = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo0Mzg4NCwiaWQiOjIsImVtYWlsIjoiYXJpZi53aWR5YUBiZXJhdWNvYWwuY28uaWQiLCJ1c2VybmFtZSI6IkxTREVWIn0.ZgYBPYZgx5CdJAMm29T6_0Es5C199PULqOfwMwdGFz8";
		$data_param = json_encode($dataforsent, JSON_NUMERIC_CHECK);
		$data_decode = json_decode($data_param);
		// echo "<pre>";
		// var_dump($data_decode);die();
		// echo "<pre>";

		$datajson = '{
				"idPerusahaan": '.$data_decode->idPerusahaan.',
				"ffr": [
					{
						"image": "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",
						"deskripsi" : "'.strval($data_decode->ffr->deskripsi).'",
						"description" : "'.strval($data_decode->ffr->description).'"
					}
				],
				"deskripsi": "'.strval($data_decode->deskripsi).'",
				"idObyek": '.$data_decode->idObyek.',
				"idObyekDetil": '.$data_decode->idObyekDetil.',
				"idPja": '.$data_decode->idPja.',
				"idPjaChild": '.$data_decode->idPjaChild.',
				"idQuickAction": '.$data_decode->idQuickAction.',
				"idLokasi": '.$data_decode->idLokasi.',
				"ketLokasi": "'.strval($data_decode->ketLokasi).'",
				"mobileUUID": null,
				"idPic": 87946,
				"idPelapor": 87948,
				"idOakRegister" : null,
				"createDate" : "'.$data_decode->createDate.'",
				"idKategori" : "'.strval($data_decode->idKategori).'",
				"idGoldenRule" : null,
				"locationLatitude" : "'.strval($data_decode->locationLatitude).'",
				"locationLongitude" : "'.strval($data_decode->locationLongitude).'"
			}';
			// "idPic": 69313,
			// "idPelapor": 69303,
			// "idPic": '.$data_decode->idPic.',
			// "idPelapor": '.$data_decode->idPelapor.',
			// "idLokasiDetail": '.$data_decode->idLokasiDetail.', // ini save dulu siapa tau dibutuhin nanti
			// "idSite": '.$data_decode->idSite.', // ini save dulu siapa tau dibutuhin nanti



			print_r($datajson." \r\n");

			// echo "<pre>";
			// var_dump("MASUK");die();
			// echo "<pre>";


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
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datajson);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$result     = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);

		// echo "<pre>";
		// var_dump($result);die();
		// echo "<pre>";

		if ($result === FALSE) {
				die("Submit Hazard failed: " . curL_error($ch). " \r\n");
		}

		curl_close($ch);

		$obj = json_decode($result);
		print_r($obj); //exit();
		print_r(" \r\n");
		return $obj;
		// return "";
	}

	function get_overspeed_for_hazardsend($table){
		$this->dbtensor = $this->load->database("tensor_report", true);
    $this->dbtensor->select("*");
    $this->dbtensor->where("overspeed_report_status_sendhazard", 0);
    $this->dbtensor->where("overspeed_report_statusintervention_cr", 1);
    $this->dbtensor->where("overspeed_report_truefalse_up", 1);
    $this->dbtensor->order_by("overspeed_report_gps_time", "DESC");
    $q        = $this->dbtensor->get($table);
    return  $q->result_array();
	}

	function get_nonoverspeed_for_hazardsend($table){
		$this->dbtensor = $this->load->database("tensor_report", true);
		$this->dbtensor->select("*");
		$this->dbtensor->where("alarm_report_status_sendhazard", 0);
		$this->dbtensor->where("alarm_report_statusintervention_cr", 1);
		$this->dbtensor->where("alarm_report_truefalse_up", 1);
		$this->dbtensor->order_by("alarm_report_start_time", "DESC");
		$q        = $this->dbtensor->get($table);
		return  $q->result_array();
	}
	// FOR HAZARD SEND END

}
