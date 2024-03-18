<?php
include "base.php";

class Beats_cronjob extends Base {
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

	
	// FOR HAZARD SEND START (PRODUCTION)
	function hazard_send_prod($company_id="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON HAZARD SEND IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON HAZARD SEND Start WIB : ". $cronstartdate . "\r\n");
		print_r("CRON HAZARD SEND Start WITA : ". $nowtime_wita . "\r\n");

		$date_for_testing = date("Y-m-d"); //TEST NON OVERSPEED HAZARD
		$m1               = date("F", strtotime($date_for_testing)); //TEST NON OVERSPEED HAZARD
		$year             = date("Y", strtotime($date_for_testing)); //TEST NON OVERSPEED HAZARD
		$report           = "alarm_evidence_";
		$reportoverspeed  = "overspeed_hour_";
		$dbtable          = "";
		$dbtableoverspeed = "";
		$company_data = $this->get_company_beats($company_id);

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

	/* 	//OVERSPEED DI DISABLE DAHULU
		// $data_evidence        = $this->get_overspeed_for_hazardsend($dbtableoverspeed);
		$data_evidence = 0;

		// OVERSPEED HAZARD SEND
		// if (sizeof($data_evidence) > 0) {
		if ($data_evidence > 0) 
		{
			
			$image = $this->config->item('BASE64_DEFAULT');
			$deskripsi_1 = "Test Deskripsi 1";
			$deskripsi_2 = "Test Deskripsi 2";
			// DATA ADA, LAKUKAN SEND
			for ($i=0; $i < sizeof($data_evidence); $i++) 
			{
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

		 */
		// ================================================================================================= //
		
		// MDVR ALERT ACTIVE
		$data_evidence_non_overspeed        = $this->get_nonoverspeed_for_hazardsend($dbtable,$company_data);
		
		// // NON OVERSPEED HAZARD SEND
		if (sizeof($data_evidence_non_overspeed) > 0)
		{
			//$image = $this->config->item('BASE64_DEFAULT');
			
			// DATA ADA, LAKUKAN SEND
			for ($i=0; $i < sizeof($data_evidence_non_overspeed); $i++) {
				
				$id_perusahaan    = $company_data->company_sid_code;
				//$id_site 		  = $data_evidence_non_overspeed[$i]['alarm_report_master_site'];
				$id_site 		  = 114; //LMO
				//$deskripsi		  = $data_evidence_non_overspeed[$i]['alarm_report_deskripsi'];
				$deskripsi		  = "Test PUSH HAZARD with Mapping Code";
				
				/* $id_object        = $data_evidence_non_overspeed[$i]['alarm_report_id_object'];
				$id_objectdetail  = $data_evidence_non_overspeed[$i]['alarm_report_id_objectdetail']; */
				
				$id_object        = 3080; //Pengoperasian Kendaraan 
				$id_objectdetail  = 30031637; //Fatigue
				
				//$id_quick_action  = $data_evidence_non_overspeed[$i]['alarm_report_id_quick_action'];
				$id_quick_action  = 1; //POC
				
				/* $id_lokasi        = $data_evidence_non_overspeed[$i]['alarm_report_id_lokasi'];
				$id_lokasi_detail = $data_evidence_non_overspeed[$i]['alarm_report_id_lokasi_detail']; */
				
				$id_lokasi        = 1172172661; //POC
				$id_lokasi_detail = 1172172675; //POC
				
				$report_location  = $data_evidence_non_overspeed[$i]['alarm_report_location_start'];
				$mobileUUID       = null;
				
				//hold dulu belum fix 
				$id_id_up        = explode("|", $data_evidence_non_overspeed[$i]['alarm_report_supervisor_cr']);
				$id_id_cr        = $data_evidence_non_overspeed[$i]['alarm_report_id_cr'];
				$sid_user_up = explode("|", $data_evidence_non_overspeed[$i]['alarm_report_sid_up']);
				
				//get idsync user CR & UP
				$id_pic_data = $this->get_id_sync($data_evidence_non_overspeed[$i]['alarm_report_sid_cr']); //SID terlapor
				$id_pelapor_data =  $this->get_id_sync($sid_user_up[0]); //SID pelapor
				
				$id_master_site   = $data_evidence_non_overspeed[$i]['alarm_report_master_site'];
				$idOakRegister    = null;
				$gps_time         = $data_evidence_non_overspeed[$i]['alarm_report_start_time'];
				
				//$id_kategori      = $data_evidence_non_overspeed[$i]['alarm_report_id_kategori'];
				$id_kategori      = 888; //Kondisi Tidak Aman	
				
				$goldenrule       = $data_evidence_non_overspeed[$i]['alarm_report_goldenrule'];
				
				/* $id_pja           = $data_evidence_non_overspeed[$i]['alarm_report_id_pja'];
				$id_pja_child     = $data_evidence_non_overspeed[$i]['alarm_report_id_pja_child']; */
				
				$id_pja           = 1206; //PJA COAL GETTING & HAULING LMO
				$id_pja_child     = 1206;//PJA COAL GETTING & HAULING LMO
				
				$coordinate       = explode(",", $data_evidence_non_overspeed[$i]['alarm_report_coordinate_start']);
				$intervention_note = $data_evidence_non_overspeed[$i]['alarm_report_note_cr'];
				
				$alert_name_data = explode("Level", $data_evidence_non_overspeed[$i]['alarm_report_name']);
				$alert_name = $alert_name_data[0];
				
				$message_1 = $data_evidence_non_overspeed[$i]['alarm_report_vehicle_no'].""." Alert ".$alert_name."".
						  	 $data_evidence_non_overspeed[$i]['alarm_report_fatiguecategory_cr'];
				$message_2 = "Waktu Kejadian ".date("d-m-Y H:i:s", strtotime($data_evidence_non_overspeed[$i]['alarm_report_start_time'])).", ".
							 "Lokasi ".$data_evidence_non_overspeed[$i]['alarm_report_location_start'].". ".
							 "Intervention Note: ".$intervention_note;
							  
				/* $deskripsi_1 = "Alert DMS 1";
				$deskripsi_2 = "Alert DSM 2"; */
				
				$deskripsi_1 = $message_1;
				$deskripsi_2 = $message_2;
			
				$url_image = $data_evidence_non_overspeed[$i]['alarm_report_downloadurl'];
				// Get the image and convert into string 
				$img = file_get_contents($url_image);
				// Encode the image string data into base64 
				$data_img = base64_encode($img); 
				//$data_img = $url_image; 
				
//////////////////////////////////////////////
				
				/*
				//POC
				$datajson = '{
						"idPerusahaan": 5384,
						"ffr": [
							{
								"image": "/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tMC0oMCUoKSj/2wBDAQcHBwoIChMKChMoGhYaKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCj/wAARCAA5ADkDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD528GeF7nxNqHkQZjgTBmmIyFHoPU+1evab8NPD1mgE8El446tK5GfwGBTvhFp62fg63lC4e6ZpWP44H8q7ORscDJPbFbQhZXZ8BnGc4iWIlRoycYx006nPxeDfDkQ40ez/wCBJu/nVhfDHh8DA0bTPxtkP9K9K8L+AL3VUS4v2NrbMMqoGXYf0rs4vhtoSrtkWeRv7xkIq+eC6GeHyzMsVHn5mvVs+fpPCXh6TIbRrD/gMKj+VUrnwB4bnQg6ZHGT0aNmUj9f6V7zrXwxh8t30i6dJByI5TuB/rXm+pWF1pd29rfRGOZOx6H3HqKE4y6GGIpZjl796Tt3ueHeOfhsdLs5L/Rnkmt48tLE/LIvqDjkf5+nm2xvQ/lX1gyh0ZWGVYEHNcH/AMK6sP7qfkKylCz0PWy3iTkpuOKd2up0XgNPL8GaOP8Ap1Q/mM/1r074Y+H11bVHvLpQ1tbYwp/ifr+grzXwgNvhDRcdPsUP/oAr334RRInhbzF+88rk/hWstInn5Xh44nMZc/Rt/idoBsGBgDpXF+IvHUenamLayga7EJzdMnRBnGAfX/P0d4s1q+n1AaHoasLx/wDWzEECNT7/AI9a1vDvhuy0jTmt1jSWSUfv5XXJkOO+e3Xj3rBLqz6+rVq1pOlh3a27/QZb+K9Pub+wtbYvKb1C6OoG0YzkHng8VW+IPh1Na0eRo1UXkILRN645x+PSuX1vQz4U1+21uyiaXTInLPEp5izkHHt/k+tekWtzFfafHcRZMUqbl3DHBHpTWj0MaUpYuFTD4lar8u582J75B9DT+fU1Z1dFi1i/RBhVnfH5mqtdNj83rR5JuK6GL4Iff4O0Y/8ATpGPyUCvafg5qsf2e60yRwJFbzUBPUHGf1rwr4aXAuPBOllTkpGYz7EEj/Cut02+uNMv4ruzbZNEcj3HcH2qH70T2KOJeX5hKT7u/wB5638WQqeHlkUBXMyAsODjmrEfxC0JI1DTTbgBn90etL4c8W6T4hgENz5aXIHzQygc+4z1ro1sdPYbha2pHtGtY9LH2FNyrVHXw1RWfQ8+l1uy1/x9o7WbNJAEeN1dcZyG4wetd7rN/DpelXNzMQkcUZOPw4Aqvf3Gj6PE1zOLS32chtqhunavJ/HHi6TX5PItgY7CM5GePM9zTUbnNiMWsvpTdSalOXY5aSVp7iaZz80jFz+JzTaAB2qr/aFp/wA/Ef51vex8FyzqtyPIfhH4ut9LZ9K1OTy7eVt0UrHhGPGD7H17V7OjrIu5CCp5BHIr5PT7yfh/OvbvhZ/yD4v90fyrCnJrQ+t4iy2nG+Ki7N7o9B288fWrkep6jEgWO/uVUdvMb/Gqo6mgVutT5CNacPhdh00kk8heeSSVz3dif503FOHeqGs/8g2X/dob5dhwvVl7zMXxv4ts/D2myBZEkv3UiGFTkg9MtjoB1/SvCf7f1H/n4k/OneJ/+Qzcf739ayq5ZTctWfo+V5ZRw1BaXb11P//Z",
								"deskripsi" : "Deskripsi 1",
								"description" : "Deskripsi 2"
							}
						],
						"deskripsi": "tes laporan di site LATI",
						"idObyek": 3017,
						"idObyekDetil": 1061,
						"idPja": 1353,
						"idPjaChild": 1368,
						"idQuickAction": 1,
						"idLokasi": 1172172661,
						"idLokasiDetail": 1172172675,
						"ketLokasi": "R Atas Selatan",
						"mobileUUID": null,
						"idPic": 69313,
						"idPelapor": 69303,
						"idSite": 114,
						"idOakRegister" : null,
						"createDate" : "2023-08-16 14:01:12",
						"idKategori" : "888",
						"idGoldenRule" : null,
						"locationLatitude" : "-6.237596",
						"locationLongitude" :"106.843117"
					}';
				*/

				$data_for_sent = array(
					"idPerusahaan" => $id_perusahaan,
					"ffr" => array(
							"image"       => $data_img,
							"deskripsi"   => $deskripsi_1,
							"description" => $deskripsi_2
						),
						"deskripsi"         => $deskripsi,
						"idObyek"           => $id_object,
						"idObyekDetil"      => $id_objectdetail,
						"idPja"             => $id_pja,
						"idPjaChild"        => $id_pja_child,
						"idQuickAction"     => $id_quick_action,
						"idLokasi"          => $id_lokasi,
						"idLokasiDetail"    => $id_lokasi_detail,
						"ketLokasi"         => $report_location,
						"mobileUUID"        => $mobileUUID,
						
						/* "idPic"             => $id_id_up[0], //yang melanggar : gunakan id sync data employe 
						"idPelapor"         => $id_id_cr,	//yg melanggar : pelapor gunakan id sync data employe  layer user CR atau layer UP  */
						
						"idPic"             => $id_pic_data->karyawan_bc_id_sync, //yang melanggar : gunakan id sync data employee
						"idPelapor"         => $id_pelapor_data->karyawan_bc_id_sync, //yg melapor : pelapor gunakan id sync data employe  layer user CR atau layer UP ?
						
						"idSite"            => $id_site,
						"idOakRegister"     => $idOakRegister,
						"createDate"        => $gps_time,
						"idKategori"        => $id_kategori,
						"idGoldenRule"      => $goldenrule,
						"locationLatitude"  => $coordinate[0],
						"locationLongitude" => $coordinate[1]
				);
				
				$submit_this_hazard = $this->submit_hazard($data_for_sent);
				
				if ($submit_this_hazard->result == true)
				{
					print_r("SUCCESS SUBMIT HAZARD \r\n");
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
	
	//ALERT OVERSPEED
	function update_status_sendhazard($alertid, $data, $table)
	{
		$this->dbtensor = $this->load->database("tensor_report", true);
		$this->dbtensor->where("overspeed_report_id", $alertid);
		return $this->dbtensor->update($table, $data);
	}
	
	//ALERT MDVR
	function update_status_sendhazard_nonoverspeed($alertid, $data, $table)
	{
		$this->dbtensor = $this->load->database("tensor_report", true);
		$this->dbtensor->where("alarm_report_id", $alertid);
		return $this->dbtensor->update($table, $data);
	}

	function submit_hazard_bk($dataforsent)
	{
		$url_submit = "http://beats-dev.beraucoal.co.id/beats/mobile/input/hazard";
		//$token 		  = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo0Mzg4NCwiaWQiOjIsImVtYWlsIjoiYXJpZi53aWR5YUBiZXJhdWNvYWwuY28uaWQiLCJ1c2VybmFtZSI6IkxTREVWIn0.ZgYBPYZgx5CdJAMm29T6_0Es5C199PULqOfwMwdGFz8";
		$token = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo0Mzg4NCwiaWQiOjIsImVtYWlsIjoiYXJpZi53aWR5YUBiZXJhdWNvYWwuY28uaWQiLCJ1c2VybmFtZSI6IkxTREVWIn0.ZgYBPYZgx5CdJAMm29T6_0Es5C199PULqOfwMwdGFz8";
		$data_param = json_encode($dataforsent, JSON_NUMERIC_CHECK);
		$data_decode = json_decode($data_param);
		
		$datajson = '{
				"idPerusahaan": '.$data_decode->idPerusahaan.',
				"ffr": [
					{
						"image": '.$data_decode->ffr->image.',
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
				"idPic": '.$data_decode->idPic.',
				"idPelapor": '.$data_decode->idPelapor.',
				"idOakRegister" : null,
				"createDate" : "'.$data_decode->createDate.'",
				"idKategori" : "'.strval($data_decode->idKategori).'",
				"idGoldenRule" : null,
				"locationLatitude" : "'.strval($data_decode->locationLatitude).'",
				"locationLongitude" : "'.strval($data_decode->locationLongitude).'"
			}';
			
			//"idPic": 87946,
			//"idPelapor": 87948,
			// "idPic": 69313,
			// "idPelapor": 69303,
			// "idPic": '.$data_decode->idPic.',
			// "idPelapor": '.$data_decode->idPelapor.',
			// "idLokasiDetail": '.$data_decode->idLokasiDetail.', // ini save dulu siapa tau dibutuhin nanti
			// "idSite": '.$data_decode->idSite.', // ini save dulu siapa tau dibutuhin nanti

			print_r($datajson." \r\n");

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

		if ($result == FALSE) {
				die("Submit Hazard failed: " . curL_error($ch). " \r\n");
		}

		curl_close($ch);
		print_r($result);
		$obj = json_decode($result);
		print_r($obj); //exit();
		print_r(" \r\n");
		return $obj;
		// return "";
	}
	
	function submit_hazard($dataforsent)
	{
		$url = "http://beats-dev.beraucoal.co.id/beats/mobile/input/hazard";
		$token = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo0Mzg4NCwiaWQiOjIsImVtYWlsIjoiYXJpZi53aWR5YUBiZXJhdWNvYWwuY28uaWQiLCJ1c2VybmFtZSI6IkxTREVWIn0.ZgYBPYZgx5CdJAMm29T6_0Es5C199PULqOfwMwdGFz8";
		$authorization = "x-api-key:".$token;
		
		$data_param = json_encode($dataforsent, JSON_NUMERIC_CHECK);
		$data_decode = json_decode($data_param);
		
		$datajson = '{
				"idPerusahaan": '.$data_decode->idPerusahaan.',
				"ffr": [
					{
						"image": "'.strval($data_decode->ffr->image).'",
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
				"idLokasiDetail": '.$data_decode->idLokasiDetail.',
				"ketLokasi": "'.strval($data_decode->ketLokasi).'",
				"mobileUUID": null,
				"idPic": '.$data_decode->idPic.',
				"idPelapor": '.$data_decode->idPelapor.',
				"idSite": '.$data_decode->idSite.',
				"idOakRegister" : null,
				"createDate" : "'.$data_decode->createDate.'",
				"idKategori" : "'.strval($data_decode->idKategori).'",
				"idGoldenRule" : null,
				"locationLatitude" : "'.strval($data_decode->locationLatitude).'",
				"locationLongitude" : "'.strval($data_decode->locationLongitude).'"
			}';
			
			
			//POC
			/* $datajson = '{
					"idPerusahaan": 5384,
					"ffr": [
						{
							"image": "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",
							"deskripsi" : "Deskripsi 1",
							"description" : "Deskripsi 2"
						}
					],
								"deskripsi": "Test PUSH HAZARD with Mapping Code",
                                "idObyek": 3080,
                                "idObyekDetil": 30031637,
                                "idPja": 1353,
                                "idPjaChild": 1368,
                                "idQuickAction": 1,
                                "idLokasi": 1172172597,
                                "idLokasiDetail": 114,
                                "ketLokasi": "PIT SE 2",
                                "mobileUUID": null,
                                "idPic": 88106,
                                "idPelapor": 88237,
                                "idSite": 114,
                                "idOakRegister" : null,
                                "createDate" : "2023-12-12 13:10:14",
                                "idKategori" : "888",
                                "idGoldenRule" : null,
                                "locationLatitude" : "2.276704",
                                "locationLongitude" : "117.58959"
				}'; */
				
				
				/*
				//POC SAMPLE
					$datajson = '{
					"idPerusahaan": 5384,
					"ffr": [
						{
							"image": "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",
							"deskripsi" : "Deskripsi 1",
							"description" : "Deskripsi 2"
						}
					],
					"deskripsi": "tes laporan di site LATI",
					"idObyek": 3017,
					"idObyekDetil": 1061,
					"idPja": 1353,
					"idPjaChild": 1368,
					"idQuickAction": 1,
					"idLokasi": 1172172661,
					"idLokasiDetail": 1172172675,
					"ketLokasi": "R Atas Selatan",
					"mobileUUID": null,
					"idPic": 69313,
					"idPelapor": 69303,
					"idSite": 114,
					"idOakRegister" : null,
					"createDate" : "2023-08-16 14:01:12",
					"idKategori" : "888",
					"idGoldenRule" : null,
					"locationLatitude" : "2.276704",
                     "locationLongitude" : "117.58959"
				}';
				
				*/
				
			

			printf("Data JSON : %s \r \n",$datajson);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $datajson);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		$json_response = curl_exec($curl);
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		printf("-------------------------- \r\n");
				
		return json_decode($json_response);
	}
	
	// SEND BERECORD (PROD mode)
	function record_send_prod($company_id="")
	{
		date_default_timezone_set("Asia/Jakarta");
		$cronstartdate = date("Y-m-d H:i:s");
		print_r("CRON BERECORD SEND IS START : ". $cronstartdate . "\r\n");
		$nowtime_wita  = date('Y-m-d H:i:s', strtotime($cronstartdate . '+1 hours'));
		print_r("CRON BERECORD SEND Start WIB : ". $cronstartdate . "\r\n");
		print_r("CRON BERECORD SEND Start WITA : ". $nowtime_wita . "\r\n");

		$date_for_testing = date("Y-m-d"); //TEST NON OVERSPEED HAZARD
		$m1               = date("F", strtotime($date_for_testing)); //TEST NON OVERSPEED HAZARD
		$year             = date("Y", strtotime($date_for_testing)); //TEST NON OVERSPEED HAZARD
		$report           = "alarm_evidence_";
		$reportoverspeed  = "overspeed_hour_";
		$dbtable          = "";
		$dbtableoverspeed = "";
		$company_data = $this->get_company_beats($company_id);

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

	
		// ================================================================================================= //
		
		// MDVR ALERT ACTIVE
		$data_evidence_overspeed        = $this->get_overspeed_for_berecord($dbtableoverspeed,$company_data);
		//print_r($data_evidence_overspeed);exit();
		
		// // NON OVERSPEED HAZARD SEND
		if (sizeof($data_evidence_overspeed) > 0)
		{
			//$image = $this->config->item('BASE64_DEFAULT');
			
			// DATA ADA, LAKUKAN SEND
			for ($i=0; $i < sizeof($data_evidence_overspeed); $i++) 
			{
				
				//maping all code here
				
				/*
				$datajson = 
				'{
					"data": [
						{ 
							"id_alarm": '.$data_decode->id_alarm.',
							"alarmId" : "'.strval($data_decode->alarmId).'",
							"alarm_group" : "'.strval($data_decode->alarm_group).'",
							"alarm_name" : "'.strval($data_decode->alarm_name).'",
							"alarm_type": '.$data_decode->alarm_type.',
							
							"sid_code_drv" : "'.strval($data_decode->sid_code_drv).'",
							"id_drv": '.$data_decode->id_drv.',
							"name_drv" : "'.strval($data_decode->name_drv).'",
							
							"id_org": '.$data_decode->id_org.',
							"plate_no" : "'.strval($data_decode->plate_no).'",
							"dev_id" : "'.strval($data_decode->dev_id).'",
							
							"shift": '.$data_decode->shift.',
							"counts": '.$data_decode->counts.',
							"datetime" : "'.strval($data_decode->datetime).'",
							"lat": '.$data_decode->lat.',
							"lng": '.$data_decode->lng.',
								
							"limit_speed" : "'.$data_decode->limit_speed.'",
							"status" : "'.strval($data_decode->status).'",
							"dir" : "'.$data_decode->dir.'",
							"id_site" : "'.$data_decode->id_site.'",
							
							"type_unit" : "'.strval($data_decode->type_unit).'",
							"spv_sid_code" : "'.strval($data_decode->spv_sid_code).'",
							"field_sid_code" : "'.strval($data_decode->field_sid_code).'",
						
							"itrv_id": '.$data_decode->itrv_id.',
							"itrv_judgment": "'.strval($data_decode->itrv_judgment).'",
							"itrv_notes": "'.strval($data_decode->itrv_notes).'",
							"itrv_set_date": "'.strval($data_decode->itrv_set_date).'",
							"itrv_set_name": "'.strval($data_decode->itrv_set_name).'",
							"itrv_type": '.$data_decode->itrv_type.',
							
							"filter_id" : null,
							"filter_note" : null,
							"filter_sid_code" : null,
							"filter_set_name" : null,
							"filter_set_date" : null,
						
							"imageUrl": "'.strval($data_decode->imageUrl).'",
							"videoUrl": "'.strval($data_decode->videoUrl).'",
							"status_alarm": "'.strval($data_decode->status_alarm).'",
							
							"ID_lokasi": '.$data_decode->ID_lokasi.',
							"id_PJA": '.$data_decode->id_PJA.',
							"id_pja_child": '.$data_decode->id_pja_child.'
						}
					],
					"error": 0,
					"message": ""
				}';
				
				*/
				
				
								
				//sample json prod data alert
				$data_for_sent =
					'{
						"data": [
							{
								"id_alarm": 22612681,
								"alarmId": "f326774d47a44072aad79a22c62f9d43",
								"alarm_group": "Overspeed",
								"alarm_name": "Overspeed Kategori A",
								"alarm_type": 8,
								"area_name": "LMO KM 6",
								"sid_code_drv": "EL1DM",
								"id_drv": 5183,
								"name_drv": "Hasnan Saputra",
								"id_org": 5418,
								"plate_no": "GDT-125",
								"dev_id": "653220230139",
								"shift": 1,
								"counts": 3,
								"datetime": "2023-12-24 11:22:25",
								"lat": 2.227631,
								"lng": 117.588485,
								"limit_speed": 53,
								"status": "kosongan",
								"dir": 4,
								"id_site": 114,
								"type_unit": "HAULER",
								"spv_sid_code": "LZFEO",
								"field_sid_code": "I9EAN",
								
								"itrv_id": 1630,
								"itrv_judgment": "medium risk",
								"itrv_notes": "Komunikasi 2 arah",
								"itrv_set_date": "2023-12-27 11:42:35",
								"itrv_set_name": "Didit Prasetyo",
								
								"itrv_type": 1,
								"filter_id" : null,
								"filter_note" : null,
								"filter_sid_code" : null,
								"filter_set_name" : null,
								"filter_set_date" : null,
								
								"imageUrl": "https://fmspoc.abditrack.com/assets/abditrack/images/overspeeddefault.png",
								"videoUrl": null,
								"status_alarm": "TRUE",
								"ID_lokasi": 1172172661,
								"id_PJA": 1206,
								"id_pja_child": 1206
							}
						],
						"error": 0,
						"message": "Test Push BeRecord"
					}';
					
				
				
				$submit_this_record = $this->submit_record($data_for_sent);
				//print_r($submit_this_record);exit();
				
				if ($submit_this_record->message == "success" && $submit_this_record->code == "00")
				{
					print_r("SUCCESS SUBMIT BERECORD \r\n");
				
					$data_update_status = array(
						"alarm_report_status_sendrecord" => 1
					);
					
					//belum tersedia
					/* $update = $this->update_status_berecord_overspeed($data_evidence_overspeed[$i]['alarm_report_id'], $data_update_status, $dbtable);
						if ($update) {
							print_r("SUCCESS UPDATE STATUS SEND BERECORD \r\n");
						} */
				}else {
					print_r("FAILED SUBMIT BERECORD \r\n");
				}
			}
		}else {
			print_r("DATA TRUE ALERT OVERSPEED TIDAK DITEMUKAN \r\n");
		}

		print_r("CRON START : ". $cronstartdate . "\r\n");
		print_r("CRON FINISH : ". date("Y-m-d H:i:s") . "\r\n");
		$finishtime   = date("Y-m-d H:i:s");
		$start_1      = dbmaketime($cronstartdate);
		$end_1        = dbmaketime($finishtime);
		$duration_sec = $end_1 - $start_1;
		print_r("CRON LATENCY : ". $duration_sec . " Second \r\n");

	}
	
	function submit_record($dataforsent)
	{
		$url = "http://beats-dev.beraucoal.co.id/beats2/h2h/dmsAlarm/apiSave";
		$token = "eyJhbGciOiJIUzI1NiJ9.eyJpZEthcnlhd2FuIjo2OTMwMywiaWQiOjU1MzE0LCJlbWFpbCI6Im5hZGlhLnNhYmlsYUBmdXNpMjQuY29tIiwidXNlcm5hbWUiOiJYSUlLTSJ9.weBnRx4aDQ3P1lSVUcw5PZUARBOeIbxbHVxz6I_4BWc";
		$authorization = "x-api-key:".$token;
		
		$data_param = json_encode($dataforsent, JSON_NUMERIC_CHECK);
		$data_decode = json_decode($data_param);
		//print_r($data_decode);exit();
		$datajson = $data_decode;

		printf("Data JSON : %s \r \n",$datajson);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $datajson);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		$json_response = curl_exec($curl);
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		printf("-------------------------- \r\n");
				
		return json_decode($json_response);
	}
	
	// SEND BERECORD
	
	function get_overspeed_for_hazardsend($table)
	{
		$this->dbtensor = $this->load->database("tensor_report", true);
		$this->dbtensor->select("*");
		$this->dbtensor->where("overspeed_report_status_sendhazard", 0);
		$this->dbtensor->where("overspeed_report_statusintervention_cr", 1);
		$this->dbtensor->where("overspeed_report_truefalse_up", 1);
		$this->dbtensor->order_by("overspeed_report_gps_time", "DESC");
		$q        = $this->dbtensor->get($table);
		return  $q->result_array();
	}

	function get_nonoverspeed_for_hazardsend($table,$companydata)
	{
		$this->dbtensor = $this->load->database("tensor_report", true);
		$this->dbtensor->select("*");
		$this->dbtensor->where("alarm_report_status_sendhazard ", null); //default bukan nol
		$this->dbtensor->where("alarm_report_statusintervention_cr", 1);
		$this->dbtensor->where("alarm_report_truefalse_up", 1);
		$this->dbtensor->where("alarm_report_vehicle_company", $companydata->company_id);
		$this->dbtensor->where("alarm_report_gpsstatus !=", "pushalert");
		$this->dbtensor->where("alarm_report_type",618); //only fatigue
		$this->dbtensor->order_by("alarm_report_start_time", "DESC");
		$this->dbtensor->limit("5");
		$q = $this->dbtensor->get($table);
		return  $q->result_array();
	}
	// FOR HAZARD SEND END
	
	function get_company_beats($idcom)
	{
		$this->db->select("*");
		$this->db->where("company_id", $idcom);
		$this->db->where("company_flag", 0);
		$this->db->where("company_status", 1);
		$this->db->order_by("company_name", "ASC");
		$q = $this->db->get("company");
		return  $q->row();
	}
	
	function get_id_sync($sid)
	{
		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("karyawan_bc_id_sync,karyawan_bc_sid,karyawan_bc_name");
		$this->dbts->where("karyawan_bc_sid", $sid);
		$this->dbts->order_by("karyawan_bc_id", "DESC");
		$q = $this->dbts->get("ts_karyawan_beraucoal");
		return  $q->row();
	}
	
	//FOR BE RECORD
	function get_overspeed_for_berecord($table,$companydata)
	{
		$list_level = array("2");
		$this->dbtensor = $this->load->database("tensor_report", true);
		$this->dbtensor->select("*");
		$this->dbtensor->where_in("overspeed_report_level", $list_level);
		$this->dbtensor->where("overspeed_report_vehicle_company", $companydata->company_id);
		$this->dbtensor->order_by("overspeed_report_gps_time", "ASC");
		$this->dbtensor->limit("1");
		$q        = $this->dbtensor->get($table);
		return  $q->result_array();
	}
}
