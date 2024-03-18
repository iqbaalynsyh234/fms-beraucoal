<?php
include "base.php";

class Project extends Base {
	var $otherdb;

	function Project()
	{
		parent::Base();
    $this->load->library('email');
    $this->load->helper('common_helper');
    $this->load->helper('kopindosat');
    $this->load->helper('email');
    $this->load->helper('common');
    $this->load->model("gpsmodel");
    $this->load->model("vehiclemodel");
    $this->load->model("configmodel");
    $this->load->model("dashboardmodel");
		$this->load->model("historymodel");
		$this->load->model("m_projectschedule");
		$this->load->model("m_poipoolmaster");
	}

	function schedule(){

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$userid       = $this->sess->user_id;
		$user_company = $this->sess->user_company;
		$datacompany  = array();
		$getcompany   = array();

		if ($userid == "389") {
			$this->params['dataproject'] = $this->m_projectschedule->getall('project_schedule', "project_user_id", $userid);
		}else {
			$this->params['dataproject'] = $this->m_projectschedule->getall('project_schedule', "project_vehicle_company", $user_company);
		}
			$getcompany = $this->m_projectschedule->getdatacompany("company", "company_created_by", "389");

			$this->params["company"] = $getcompany;

		// echo "<pre>";
		// var_dump($this->params['company']);die();
		// echo "<pre>";
    $this->params['code_view_menu'] = "configuration";

    $this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
    $this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
    $this->params["content"]     = $this->load->view('farrasindo/dashboard/project/v_home_projectschedule', $this->params, true);
    $this->load->view("dashboard/template_dashboard", $this->params);
	}

  function add_project(){
    $userid       = $this->sess->user_id;
    $user_company = $this->sess->user_company;

		if ($userid == "389") {
			$this->db->where("vehicle_user_id", $userid);
		}else {
			$this->db->where("vehicle_company", $user_company);
		}

    $this->db->select("*");
    $this->db->where("vehicle_status <>", "3");
    $this->db->order_by("vehicle_no", "asc");
    $q = $this->db->get("vehicle");
    $result = $q->result_array();

    $this->db->select("*");
    $this->db->where("group_creator", $userid);
    $this->db->order_by("group_name", "asc");
    $q2 = $this->db->get("group");
    $result2 = $q2->result_array();

    $this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where("driver_company", $user_company);
    $this->dbtransporter->select("*");
    $this->dbtransporter->order_by("driver_name", "asc");
    $q3 = $this->dbtransporter->get("driver");
    $result3 = $q3->result_array();

		$where                            = '$this->db->where("poi_creator_id", $userid)';
		$this->params['datapool']         = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $userid, $where);
		// $this->params['datapool']      = $this->m_projectschedule->getallpool('pool', "pool_user_id", $userid);
    $this->params["vehicles"]       = $result;
    $this->params["customer"]       = $result2;
    $this->params["driver"]         = $result3;
    $this->params['code_view_menu'] = "configuration";

    // echo "<pre>";
    // var_dump($this->params['datapool']);die();
    // echo "<pre>";
    $this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
    $this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
    $this->params["content"]     = $this->load->view('farrasindo/dashboard/project/v_home_addprojectschedule', $this->params, true);
    $this->load->view("dashboard/template_dashboard", $this->params);
  }

	function save_project(){
		$userid 							= $this->sess->user_id;
		$user_company 				= $this->sess->user_company;
		$latpool 			        = $this->input->post('latpool');
		$lngpool 			        = $this->input->post('lngpool');
		$select_pool	        = explode("=", $this->input->post('select_pool'));
		$pool_id           		= $select_pool[0];
		$pool_name         		= $select_pool[1];
		$project_name         = $this->input->post('project_name');
		$addschedule_vehicle  = explode("=", $this->input->post('addschedule_vehicle'));
		$vehicle_no           = $addschedule_vehicle[0];
		$vehicle_name         = $addschedule_vehicle[1];
		$vehicle_device       = $addschedule_vehicle[2];

		if ($userid == "389") {
			$this->db->where("vehicle_user_id", $userid);
		}else {
			$this->db->where("vehicle_company", $user_company);
		}

		$this->db->select("*");
    $this->db->where("vehicle_status <>", "3");
		$this->db->where("vehicle_device", $vehicle_device);
    $q      = $this->db->get("vehicle");
    $result = $q->result_array();


		$addschedule_customer = explode("=", $this->input->post('addschedule_customer'));
		$customer_no          = $addschedule_customer[0];
		$customer_name        = $addschedule_customer[1];
		$addschedule_driver   = explode("=", $this->input->post('addschedule_driver'));
		$driver_no            = $addschedule_driver[0];
		$driver_name          = $addschedule_driver[1];
		$project_price        = $this->input->post('project_price');
			if (is_numeric($project_price)) {
				$project_price = $project_price;
			}else {
				$project_price = "0";
			}

			// echo "<pre>";
			// var_dump($result);die();
			// echo "<pre>";
		$project_startdate    = $this->input->post('project_startdate');
		$project_enddate      = $this->input->post('project_enddate');
		$shour      					= $this->input->post('shour');
		$ehour      					= $this->input->post('ehour');
		$latitude             = $this->input->post('latitude');
		$longitude            = $this->input->post('longitude');
		$addressfix           = $this->input->post('addressfix');
		$allcordinates        = $this->input->post('allcordinates');

		$tanggal1 = date("Y-m-d H:i:s", strtotime($project_startdate." ".$shour.":00"));
		$tanggal2 = date("Y-m-d H:i:s", strtotime($project_enddate." ".$ehour.":00"));
		$detik    = strtotime($tanggal2) - strtotime($tanggal1);
		$abc      = $this->secondsToTime($detik);
		$day      = $abc['d'];
		$hours    = $abc['h'];
		$min      = $abc['m'];
		$sec      = $abc['s'];

		if ($day == 0) {
			$durationofwork = $hours.' H '.$min.' Min';
		}else {
			$durationofwork = $day.' D '.$hours.' H '.$min.' Min';
		}

		// echo "<pre>";
		// var_dump($durationofwork);die();
		// echo "<pre>";

		// $key = $this->config->item("GOOGLE_MAP_API_KEY");
		// if(isset($key) && $key != "") {
		// 	$apikey = $key;
		//  } else {
		// 	 $apikey = "http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false";
		// }

		$apikey = "AIzaSyDjkxkZrIVJbT6Bv2nmJlK9OvNYTBcA2z0";



		$distance_matrix = $this->distancematrix($latpool, $lngpool, $latitude, $longitude, $apikey);

		if(isset($distance_matrix)){
			$duration_text  = $distance_matrix['rows'][0]['elements'][0]['duration']['text'];
			$duration_value = $distance_matrix['rows'][0]['elements'][0]['duration']['value'];
			$distance_text  = $distance_matrix['rows'][0]['elements'][0]['distance']['text'];
			$distance_value = $distance_matrix['rows'][0]['elements'][0]['distance']['value'];
		}

		// echo "<pre>";
		// var_dump($duration_text.'-'.$duration_value.'-'.$distance_text.'-'.$distance_value);die();
		// echo "<pre>";

			if ($allcordinates != "") {
				$data = array(
					"project_user_id"              => $userid,
					"project_name"                 => $project_name,
					"project_vehicle_no"           => $vehicle_no,
					"project_vehicle_name"         => $vehicle_name,
					"project_vehicle_device"       => $vehicle_device,
					"project_customer_id"          => $customer_no,
					"project_customer_name"        => $customer_name,
					"project_driver_operator_id"   => $driver_no,
					"project_driver_operator_name" => $driver_name,
					"project_price"                => $project_price,
					"project_startdate"            => date("Y-m-d", strtotime($project_startdate." ".$shour.":00")),
					"project_enddate"              => date("Y-m-d", strtotime($project_enddate." ".$ehour.":00")),
					"project_latitude"             => $latitude,
					"project_durationofwork"       => $durationofwork,
					"project_longitude"            => $longitude,
					"project_address"              => $addressfix,
					"project_radius"               => round($allcordinates),
					"project_pool_lat"             => $latpool,
					"project_pool_lng"             => $lngpool,
					"project_pool_id"              => $pool_id,
					"project_pool_name"            => $pool_name,
					"project_user_company" 				 => $user_company,
					"project_vehicle_company" 		 => $result[0]['vehicle_company'],
					"project_duration_text"        => $duration_text,
					"project_duration_value"       => $duration_value,
					"project_distance_text"        => $distance_text,
					"project_distance_value"       => $distance_value,
					"project_created_date"         => date("Y-m-d H:i:s")
				);

				// echo "<pre>";
				// var_dump($data);die();
				// echo "<pre>";
				$insert = $this->m_projectschedule->insertdata("project_schedule", $data);
					if ($insert) {
						echo json_encode(array("status" => "success"));
					}else {
						echo json_encode(array("status" => "failed"));
					}
			}
	}

	function secondsToTime($inputSeconds) {

    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;

    // extract days
    $days = floor($inputSeconds / $secondsInADay);

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // return the final array
    $obj = array(
        'd' => (int) $days,
        'h' => (int) $hours,
        'm' => (int) $minutes,
        's' => (int) $seconds,
    );
    return $obj;
}

	function distancematrix($latitude1, $longitude1, $latitude2, $longitude2, $apikey){
        $dataJson = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$latitude1.",".$longitude1."&destinations=".$latitude2.",".$longitude2."&key=".$apikey."");
		$data = json_decode($dataJson,true);
		/*$api_status = $data['rows'][0]['elements'][0]['status']['value'];
		$eta = "";

		if($api_status == "O"){
			$duration_sec = $data['rows'][0]['elements'][0]['duration']['value'];
			$dateinterval = new DateTime($lastgpstime);
			$dateinterval->add(new DateInterval('PT'.$duration_sec.'S'));
			$eta = $dateinterval->format('Y-m-d H:i:s');
		}
		printf("ETA %s \r\n", $eta);*/
        //return $eta;
		return $data;

    }

	function edit($id){
		$userid       = $this->sess->user_id;
		$user_company = $this->sess->user_company;

		$this->params['dataproject'] = $this->m_projectschedule->getallbycode('project_schedule', "project_user_id", $userid, $id);

		if ($userid == "389") {
			$this->db->where("vehicle_user_id", $userid);
		}else {
			$this->db->where("vehicle_company", $user_company);
		}

		$this->db->select("*");
    $this->db->where("vehicle_status <>", "3");
    $this->db->order_by("vehicle_no", "asc");
    $q = $this->db->get("vehicle");
    $result = $q->result_array();

    $this->db->select("*");
    $this->db->where("group_creator", $userid);
    $this->db->order_by("group_name", "asc");
    $q2 = $this->db->get("group");
    $result2 = $q2->result_array();

    $this->dbtransporter = $this->load->database("transporter", true);
    $this->dbtransporter->select("*");
    $this->dbtransporter->where("driver_company", $user_company);
    $this->dbtransporter->order_by("driver_name", "asc");
    $q3 = $this->dbtransporter->get("driver");
    $result3 = $q3->result_array();

		$where                            = '$this->db->where("poi_creator_id", $userid)';
		$this->params['datapool']         = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $userid, $where);
		// $this->params['datapool']      = $this->m_projectschedule->getallpool('pool', "pool_user_id", $userid);
    $this->params["vehicles"]       = $result;
    $this->params["customer"]       = $result2;
    $this->params["driver"]         = $result3;
    $this->params['code_view_menu'] = "configuration";


		// echo "<pre>";
		// var_dump($this->params['dataproject']);die();
		// echo "<pre>";

    $this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
    $this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
    $this->params["content"]        = $this->load->view('farrasindo/dashboard/project/v_home_editprojectschedule', $this->params, true);
    $this->load->view("dashboard/template_dashboard", $this->params);
	}

	function update_project(){
		$userid 							= $this->sess->user_id;
		$user_company 				= $this->sess->user_company;
		$idforupdate        	= $this->input->post('idforupdate');
		$project_name        	= $this->input->post('project_name');
		$latpool 			        = $this->input->post('latpool');
		$lngpool 			        = $this->input->post('lngpool');
		$addschedule_vehicle  = explode("=", $this->input->post('addschedule_vehicle'));
		$vehicle_no           = $addschedule_vehicle[0];
		$vehicle_name         = $addschedule_vehicle[1];
		$vehicle_device       = $addschedule_vehicle[2];
		$addschedule_customer = explode("=", $this->input->post('addschedule_customer'));
		$customer_no          = $addschedule_customer[0];
		$customer_name        = $addschedule_customer[1];
		$addschedule_driver   = explode("=", $this->input->post('addschedule_driver'));
		$driver_no            = $addschedule_driver[0];
		$driver_name          = $addschedule_driver[1];
		$select_pool	        = explode("=", $this->input->post('select_pool'));
		$pool_id           		= $select_pool[0];
		$pool_name         		= $select_pool[1];
		$project_price        = $this->input->post('project_price');
			if (is_numeric($project_price)) {
				$project_price = $project_price;
			}else {
				$project_price = "0";
			}
		$project_startdate    = $this->input->post('project_startdate');
		$project_enddate      = $this->input->post('project_enddate');
		$shour      					= $this->input->post('shour');
		$ehour      					= $this->input->post('ehour');
		$tanggal1 = date("Y-m-d H:i:s", strtotime($project_startdate." ".$shour.":00"));
		$tanggal2 = date("Y-m-d H:i:s", strtotime($project_enddate." ".$ehour.":00"));

		$detik    = strtotime($tanggal2) - strtotime($tanggal1);
		$abc      = $this->secondsToTime($detik);
		$day      = $abc['d'];
		$hours    = $abc['h'];
		$min      = $abc['m'];
		$sec      = $abc['s'];

		if ($day == 0) {
			$durationofwork = $hours.' H '.$min.' Min';
		}else {
			$durationofwork = $day.' D '.$hours.' H '.$min.' Min';
		}

		$latitude             = $this->input->post('latitude');
		$longitude            = $this->input->post('longitude');
		$addressfix           = $this->input->post('addressfix');
		$allcordinates        = $this->input->post('allcordinates');

		if ($userid == "389") {
			$this->db->where("vehicle_user_id", $userid);
		}else {
			$this->db->where("vehicle_company", $user_company);
		}

		$this->db->select("*");
		$this->db->where("vehicle_status <>", "3");
		$this->db->where("vehicle_device", $vehicle_device);
		$q      = $this->db->get("vehicle");
		$result = $q->result_array();

		$apikey = "AIzaSyDjkxkZrIVJbT6Bv2nmJlK9OvNYTBcA2z0";



		$distance_matrix = $this->distancematrix($latpool, $lngpool, $latitude, $longitude, $apikey);

		if(isset($distance_matrix)){
			$duration_text  = $distance_matrix['rows'][0]['elements'][0]['duration']['text'];
			$duration_value = $distance_matrix['rows'][0]['elements'][0]['duration']['value'];
			$distance_text  = $distance_matrix['rows'][0]['elements'][0]['distance']['text'];
			$distance_value = $distance_matrix['rows'][0]['elements'][0]['distance']['value'];
		}

			if ($allcordinates != "") {
				$data = array(
					"project_user_id" 						 => $userid,
					"project_name"                 => $project_name,
					"project_vehicle_no"           => $vehicle_no,
					"project_vehicle_name"         => $vehicle_name,
					"project_vehicle_device"       => $vehicle_device,
					"project_customer_id"          => $customer_no,
					"project_customer_name"        => $customer_name,
					"project_driver_operator_id"   => $driver_no,
					"project_driver_operator_name" => $driver_name,
					"project_price"                => $project_price,
					"project_startdate"            => $tanggal1,
					"project_enddate"              => $tanggal2,
					"project_latitude"             => $latitude,
					"project_durationofwork"       => $durationofwork,
					"project_longitude"            => $longitude,
					"project_address"              => $addressfix,
					"project_radius"               => round($allcordinates),
					"project_pool_lat"             => $latpool,
					"project_pool_lng"             => $lngpool,
					"project_pool_id"              => $pool_id,
					"project_pool_name"            => $pool_name,
					"project_user_company" 				 => $user_company,
					"project_vehicle_company" 		 => $result[0]['vehicle_company'],
					"project_duration_text"        => $duration_text,
					"project_duration_value"       => $duration_value,
					"project_distance_text"        => $distance_text,
					"project_distance_value"       => $distance_value,
					"project_update_date" 				 => date("Y-m-d H:i:s")
				);

				// echo "<pre>";
				// var_dump($data);die();
				// echo "<pre>";
				$update = $this->m_projectschedule->update_date("project_schedule", "project_no", $idforupdate, $data);
					if ($update) {
						echo json_encode(array("status" => "success"));
					}else {
						echo json_encode(array("status" => "failed"));
					}
			}
	}

	function delete_project($id){
		$this->dbtransporter = $this->load->database("transporter",true);

		$data["project_flag"] = 1;
		$data["project_delete_date"] = date("Y-m-d H:i:s");

		$this->dbtransporter->where("project_no", $id);
		if($this->dbtransporter->update("project_schedule", $data)){
			$callback['message'] = "Data has been deleted.";
			$callback['error'] = false;
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;
		}
		echo json_encode($callback);
	}

	function completingproject(){
		$this->params["title"] = "Completing Project";
		$id = $this->input->post('id');

		$this->dbtransporter = $this->load->database("transporter",true);
		$data = array(
			"project_status" => 3
		);
		$this->dbtransporter->where('project_no', $id);
		$update = $this->dbtransporter->update("project_schedule", $data);
			if ($update) {
				$callback['error']        = false;
				echo json_encode($callback);
			}else {
				$callback['error']        = true;
				echo json_encode($callback);
			}
	}

	// POOL
	function pool_list(){
		$userid                           = $this->sess->user_id;
		$this->params['datapool']         = $this->m_projectschedule->getallpool('pool', "pool_user_id", $userid);
    $this->params['code_view_menu'] = "configuration";

    // echo "<pre>";
    // var_dump($result);die();
    // echo "<pre>";
    $this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
    $this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
    $this->params["content"]        = $this->load->view('farrasindo/dashboard/pool/v_home_poollist', $this->params, true);
    $this->load->view("dashboard/template_dashboard", $this->params);
  }

	function add_pool(){
		$this->params["nothing"]          = "";
    $this->params['code_view_menu'] = "configuration";

    // echo "<pre>";
    // var_dump($result);die();
    // echo "<pre>";
    $this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
    $this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
    $this->params["content"]        = $this->load->view('farrasindo/dashboard/pool/v_home_pool', $this->params, true);
    $this->load->view("dashboard/template_dashboard", $this->params);
  }

	function save_pool(){
		$userid        = $this->sess->user_id;
		$user_company  = $this->sess->user_company;
		$pool_name 	   = $this->input->post('pool_name');
		$latitude      = $this->input->post('latitude');
		$longitude     = $this->input->post('longitude');
		$addressfix    = $this->input->post('addressfix');
		$allcordinates = $this->input->post('allcordinates');
			if ($allcordinates != "") {
				$data = array(
					"pool_user_id"      => $userid,
					"pool_user_company" => $user_company,
					"pool_name"         => $pool_name,
					"pool_latitude"     => $latitude,
					"pool_longitude"    => $longitude,
					"pool_address"      => $addressfix,
					"pool_radius"       => $allcordinates,
					"pool_created_date" => date("Y-m-d H:i:s")
				);

				// echo "<pre>";
				// var_dump($data);die();
				// echo "<pre>";
				$insert = $this->m_projectschedule->insertdata("pool", $data);
					if ($insert) {
						echo json_encode(array("status" => "success"));
					}else {
						echo json_encode(array("status" => "failed"));
					}
			}
	}

	function edit_pool($id){
		$userid                           = $this->sess->user_id;
		$user_company                     = $this->sess->user_company;

		$this->params['datapool']         = $this->m_projectschedule->getallbycodepool('pool', "pool_user_id", $userid, $id);
    $this->params['code_view_menu'] = "configuration";


		// echo "<pre>";
		// var_dump($this->params['dataproject']);die();
		// echo "<pre>";

    $this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
    $this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
    $this->params["content"]        = $this->load->view('farrasindo/dashboard/pool/v_home_editpool', $this->params, true);
    $this->load->view("dashboard/template_dashboard", $this->params);
	}

	function update_pool(){
		$idforupdate   = $this->input->post('idforupdate');
		$pool_name     = $this->input->post('pool_name');
		$latitude      = $this->input->post('latitude');
		$longitude     = $this->input->post('longitude');
		$addressfix    = $this->input->post('addressfix');
		$allcordinates = $this->input->post('allcordinates');
			if ($allcordinates != "") {
				$data = array(
					"pool_name"         => $pool_name,
					"pool_latitude"     => $latitude,
					"pool_longitude"    => $longitude,
					"pool_address"      => $addressfix,
					"pool_radius"       => $allcordinates
				);

				// echo "<pre>";
				// var_dump($addschedule_vehicle);die();
				// echo "<pre>";
				$update = $this->m_projectschedule->update_date("pool", "pool_no", $idforupdate, $data);
					if ($update) {
						echo json_encode(array("status" => "success"));
					}else {
						echo json_encode(array("status" => "failed"));
					}
			}
	}

	function delete_pool($id){
		$this->dbtransporter = $this->load->database("transporter",true);

		$data["pool_flag"] = 1;
		// $data["project_delete_date"] = date("Y-m-d H:i:s");

		$this->dbtransporter->where("pool_no", $id);
		if($this->dbtransporter->update("pool", $data)){
			$callback['message'] = "Data has been deleted.";
			$callback['error'] = false;
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;
		}
		echo json_encode($callback);
	}

	function searchdatapool(){
		// header("Content-Type:application/json");
		// header('Access-Control-Allow-Origin: *');
		// header("Access-Control-Allow-Methods: GET, OPTIONS, POST");
		$json 			= file_get_contents("php://input");
    $obj 				= json_decode($json);
		$poi_id 		= $obj->pool_no;
		$getdata		= $this->m_projectschedule->getallpoolbyno2("poi_poolmaster", "poi_id", $poi_id);
		// echo "<pre>";
		// var_dump($getdata);die();
		// echo "<pre>";
		echo json_encode(array("data" => $getdata));

	}





}
