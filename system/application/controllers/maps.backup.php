<?php
include "base.php";

class Maps extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function Maps()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_poipoolmaster");
	}

	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['sortby']  = "mobil_id";
		$this->params['orderby'] = "asc";
		$this->params['title']   = "Maps All";
		if($this->sess->user_id == "1445"){
			$user_id = 3212; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$companyid 			 = $this->uri->segment(3);

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();


		// echo "<pre>";
		// var_dump($result);die();
		// echo "<pre>";

		$datafix = array();
		for ($i=0; $i < sizeof($result); $i++) {
			$jsonnya[$i] = json_decode($result[$i]['vehicle_autocheck']);

			array_push($datafix, array(
				 "vehicle_id"             => $result[$i]['vehicle_id'],
				 "vehicle_user_id"        => $result[$i]['vehicle_user_id'],
				 "vehicle_device"         => $result[$i]['vehicle_device'],
				 "vehicle_no"             => $result[$i]['vehicle_no'],
				 "vehicle_name"           => $result[$i]['vehicle_name'],
				 "vehicle_active_date2"   => $result[$i]['vehicle_active_date2'],
				 "vehicle_card_no"        => $result[$i]['vehicle_card_no'],
				 "vehicle_operator"       => $result[$i]['vehicle_operator'],
				 "vehicle_active_date"    => $result[$i]['vehicle_active_date'],
				 "vehicle_active_date1"   => $result[$i]['vehicle_active_date1'],
				 "vehicle_status"         => $result[$i]['vehicle_status'],
				 "vehicle_image"          => $result[$i]['vehicle_image'],
				 "vehicle_created_date"   => $result[$i]['vehicle_created_date'],
				 "vehicle_type"           => $result[$i]['vehicle_type'],
				 "vehicle_autorefill"     => $result[$i]['vehicle_autorefill'],
				 "vehicle_maxspeed"       => $result[$i]['vehicle_maxspeed'],
				 "vehicle_maxparking"     => $result[$i]['vehicle_maxparking'],
				 "vehicle_company"        => $result[$i]['vehicle_company'],
				 "vehicle_subcompany"     => $result[$i]['vehicle_subcompany'],
				 "vehicle_group"          => $result[$i]['vehicle_group'],
				 "vehicle_subgroup"       => $result[$i]['vehicle_subgroup'],
				 "vehicle_odometer"       => $result[$i]['vehicle_odometer'],
				 "vehicle_payment_type"   => $result[$i]['vehicle_payment_type'],
				 "vehicle_payment_amount" => $result[$i]['vehicle_payment_amount'],
				 "vehicle_fuel_capacity"  => $result[$i]['vehicle_fuel_capacity'],
				 // "vehicle_info"           => $result[$i]['vehicle_info'],
				 "vehicle_sales"          => $result[$i]['vehicle_sales'],
				 "vehicle_teknisi_id"     => $result[$i]['vehicle_teknisi_id'],
				 "vehicle_tanggal_pasang" => $result[$i]['vehicle_tanggal_pasang'],
				 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $result[$i]['vehicle_imei']),
				 "vehicle_dbhistory"      => $result[$i]['vehicle_dbhistory'],
				 "vehicle_dbhistory_name" => $result[$i]['vehicle_dbhistory_name'],
				 "vehicle_dbname_live"    => $result[$i]['vehicle_dbname_live'],
				 "vehicle_isred"          => $result[$i]['vehicle_isred'],
				 "vehicle_modem"          => $result[$i]['vehicle_modem'],
				 "vehicle_card_no_status" => $result[$i]['vehicle_card_no_status'],
				 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_status),
		 		 "auto_last_update"       => $jsonnya[$i]->auto_last_update,
		 		 "auto_last_check"        => $jsonnya[$i]->auto_last_check,
		 		 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_position),
		 		 "auto_last_lat"          => substr($jsonnya[$i]->auto_last_lat, 0, 10),
		 		 "auto_last_long"         => substr($jsonnya[$i]->auto_last_long, 0, 10),
		 		 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_engine),
		 		 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_gpsstatus),
		 		 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_speed),
		 		 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_course),
		 		 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_flag)
			));
		}

		$this->params['vehicle']      = $datafix;
		$this->params['vehicletotal'] = $result;

		$this->params['poolmaster'] = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);


		// $hasilnya = json_encode($result);
		// $this->params['datanya'] = $hasilnya;

		// echo "<pre>";
		// var_dump($this->params['poolmaster'] );die();
		// echo "<pre>";

		//get company
		$company                     = $this->dashboardmodel->getcompany_byowner();
		$this->params['company']     = $company;
		$this->params['companyid']     = $companyid;


		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/trackers/maps_view', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

	function onevehicle(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['devices'] = $_POST['devices'];
		/*echo "<pre>";
		var_dump($this->params['devices']);die();
		echo "<pre>";
		*/
		exit();
		$html                       = $this->load->view("dashboard/trackers/onevehicle_view", $this->params, true);
		$callback['error']          = false;
		$callback['html']           = $html;
		echo json_encode($callback);
	}

	function area(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['sortby']  = "mobil_id";
		$this->params['orderby'] = "asc";
		$this->params['title']   = "Maps All";
		if($this->sess->user_id == "1445"){
			$user_id = 3212; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$companyid 			 = $this->uri->segment(3);

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_company", $companyid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();


		// echo "<pre>";
		// var_dump($result);die();
		// echo "<pre>";

		$datafix = array();
		for ($i=0; $i < sizeof($result); $i++) {
			$jsonnya[$i] = json_decode($result[$i]['vehicle_autocheck']);

			array_push($datafix, array(
				 "vehicle_id"             => $result[$i]['vehicle_id'],
				 "vehicle_user_id"        => $result[$i]['vehicle_user_id'],
				 "vehicle_device"         => $result[$i]['vehicle_device'],
				 "vehicle_no"             => $result[$i]['vehicle_no'],
				 "vehicle_name"           => $result[$i]['vehicle_name'],
				 "vehicle_active_date2"   => $result[$i]['vehicle_active_date2'],
				 "vehicle_card_no"        => $result[$i]['vehicle_card_no'],
				 "vehicle_operator"       => $result[$i]['vehicle_operator'],
				 "vehicle_active_date"    => $result[$i]['vehicle_active_date'],
				 "vehicle_active_date1"   => $result[$i]['vehicle_active_date1'],
				 "vehicle_status"         => $result[$i]['vehicle_status'],
				 "vehicle_image"          => $result[$i]['vehicle_image'],
				 "vehicle_created_date"   => $result[$i]['vehicle_created_date'],
				 "vehicle_type"           => $result[$i]['vehicle_type'],
				 "vehicle_autorefill"     => $result[$i]['vehicle_autorefill'],
				 "vehicle_maxspeed"       => $result[$i]['vehicle_maxspeed'],
				 "vehicle_maxparking"     => $result[$i]['vehicle_maxparking'],
				 "vehicle_company"        => $result[$i]['vehicle_company'],
				 "vehicle_subcompany"     => $result[$i]['vehicle_subcompany'],
				 "vehicle_group"          => $result[$i]['vehicle_group'],
				 "vehicle_subgroup"       => $result[$i]['vehicle_subgroup'],
				 "vehicle_odometer"       => $result[$i]['vehicle_odometer'],
				 "vehicle_payment_type"   => $result[$i]['vehicle_payment_type'],
				 "vehicle_payment_amount" => $result[$i]['vehicle_payment_amount'],
				 "vehicle_fuel_capacity"  => $result[$i]['vehicle_fuel_capacity'],
				 // "vehicle_info"           => $result[$i]['vehicle_info'],
				 "vehicle_sales"          => $result[$i]['vehicle_sales'],
				 "vehicle_teknisi_id"     => $result[$i]['vehicle_teknisi_id'],
				 "vehicle_tanggal_pasang" => $result[$i]['vehicle_tanggal_pasang'],
				 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $result[$i]['vehicle_imei']),
				 "vehicle_dbhistory"      => $result[$i]['vehicle_dbhistory'],
				 "vehicle_dbhistory_name" => $result[$i]['vehicle_dbhistory_name'],
				 "vehicle_dbname_live"    => $result[$i]['vehicle_dbname_live'],
				 "vehicle_isred"          => $result[$i]['vehicle_isred'],
				 "vehicle_modem"          => $result[$i]['vehicle_modem'],
				 "vehicle_card_no_status" => $result[$i]['vehicle_card_no_status'],
				 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_status),
		 		 "auto_last_update"       => $jsonnya[$i]->auto_last_update,
		 		 "auto_last_check"        => $jsonnya[$i]->auto_last_check,
		 		 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_position),
		 		 "auto_last_lat"          => substr($jsonnya[$i]->auto_last_lat, 0, 10),
		 		 "auto_last_long"         => substr($jsonnya[$i]->auto_last_long, 0, 10),
		 		 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_engine),
		 		 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_gpsstatus),
		 		 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_speed),
		 		 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_course),
		 		 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_flag)
			));
		}

		$this->params['vehicle']      = $datafix;
		$this->params['vehicletotal'] = $result;

		$this->params['poolmaster'] = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);

		//get company
		$company                     = $this->dashboardmodel->getcompany_byowner();
		$this->params['company']     = $company;
		$this->params['companyid']     = $companyid;


		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/trackers/maps_view', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

	function vehicle(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['sortby']  = "mobil_id";
		$this->params['orderby'] = "asc";
		$this->params['title']   = "Maps All";
		if($this->sess->user_id == "1445"){
			$user_id = 3212; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$user_company = $this->sess->user_company;
		$user_id_fix = $user_id;
		$id = $this->uri->segment(3);

		$where   = "user_level";
		$value   = 2;
		$this->db = $this->load->database("default", true);
		$sql     = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_id = '$id' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
		$sql2    = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_id = '$id' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";

		$q      	= $this->db->query($sql);
		$q2     	= $this->db->query($sql2);
		$result 	= $q->result_array();
		$result2  	= $q2->result_array();

		$datafix = array();
		for ($i=0; $i < sizeof($result); $i++) {
			array_push($datafix, array(
				"datafix" 					  => "true",
				"auto_id"                     => $result[$i]['auto_id'],
				"auto_user_id"                => $result[$i]['auto_user_id'],
				"auto_vehicle_id"             => $result[$i]['auto_vehicle_id'],
				"auto_vehicle_no"             => $result[$i]['auto_vehicle_no'],
				"auto_vehicle_name"           => $result[$i]['auto_vehicle_name'],
				"auto_vehicle_device"         => $result[$i]['auto_vehicle_device'],
				"auto_vehicle_type"           => $result[$i]['auto_vehicle_type'],
				"auto_vehicle_company"        => $result[$i]['auto_vehicle_company'],
				"auto_vehicle_subcompany"     => $result[$i]['auto_vehicle_subcompany'],
				"auto_vehicle_group"          => $result[$i]['auto_vehicle_group'],
				"auto_vehicle_subgroup"       => $result[$i]['auto_vehicle_subgroup'],
				"auto_vehicle_active_date2"   => $result[$i]['auto_vehicle_active_date2'],
				"auto_simcard"                => $result[$i]['auto_simcard'],
				"auto_status"          		  	=> $result[$i]['auto_status'],
				"auto_last_update"            => $result[$i]['auto_last_update'],
				"auto_last_check"             => $result[$i]['auto_last_check'],
				"auto_last_position"          => str_replace(array("\n","\r","'","'\'","/", "-"), "", $result[$i]['auto_last_position']),
				"auto_last_lat"               => $result[$i]['auto_last_lat'],
				"auto_last_long"              => $result[$i]['auto_last_long'],
				"auto_last_engine"            => $result[$i]['auto_last_engine'],
				"auto_last_gpsstatus"         => $result[$i]['auto_last_gpsstatus'],
				"auto_last_speed"             => $result[$i]['auto_last_speed'],
				"auto_last_course"            => $result[$i]['auto_last_course'],
				"auto_flag"                   => $result[$i]['auto_flag']
				//"auto_change_engine_status"   => $result[$i]['auto_change_engine_status'],
				//"auto_change_engine_datetime" => $result[$i]['auto_change_engine_datetime'],
				//"auto_change_position"        => str_replace(array("\n","\r", "'"), "", $result[$i]['auto_change_position']),
				//"auto_change_coordinate"      => $result[$i]['auto_change_coordinate']
			));
		}

		$this->params['vehicle']      = $datafix;
		$this->params['vehicletotal'] = $result2;

		// $hasilnya = json_encode($result);
		// $this->params['datanya'] = $hasilnya;

		// echo "<pre>";
		// var_dump($this->params['vehicletotal']);
		// echo "<pre>";

		//get company
		$company                     = $this->dashboardmodel->getcompany_byowner();
		$this->params['company']     = $company;

		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/trackers/maps_view', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

	function getvehiclebyvehiclegroup(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		    $vehicle_groupnya                 = $_POST['vehicle_group'];
			$where                            = "vehicle_group";
			$wherenya                         = $vehicle_groupnya;

			$getuserpart4                     = $this->m_maintenance->g_vehiclebysentra("webtracking_vehicle", $where, $wherenya);
			$this->params['vehiclesentra']    = $getuserpart4;
			$this->params['totalvehicle']     = sizeof($getuserpart4);
			$this->params['vehicle_groupnya'] = $vehicle_groupnya;

			 //echo "<pre>";
			 //var_dump($vehicle_groupnya);die();
			 //echo "<pre>";

			echo json_encode(array("msg" => 200, "jumlah" => sizeof($getuserpart4), "data" => $getuserpart4, "vehicle_groupnya" => $vehicle_groupnya));

	}

	function getvehiclebyuniversalsearch(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		 $company_gmop 		= $_POST['company_gmop'];
		 $subcompany_id 	= $_POST['subcompany_id'];
		 $user_group  		= $_POST['user_group'];
		 $vehiclenya 		= $_POST['vehiclenya'];
		 $user_id 			= $this->sess->user_id;
		 $user_level 		= $this->sess->user_level;

		 if($user_level == 1){
			 if($subcompany_id == "" || $subcompany_id == "undefined"){
				 $sikon 	= 1;
				 $where 	= 'auto_vehicle_company';
				 $sql 		= "SELECT * FROM `webtracking_vehicle_autocheck` where $where = '$company_gmop' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
				 $sql2      = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_user_id = '$user_id' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
			 }elseif($user_group == "" || $user_group == "undefined"){
				 $sikon 	= 2;
				 $where 	= 'auto_vehicle_subcompany';
				 $sql 		= "SELECT * FROM `webtracking_vehicle_autocheck` where $where = '$subcompany_id' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
				 $sql2      = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_subcompany = '$subcompany_id' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
			 }elseif($vehiclenya == "" || $vehiclenya == "undefined"){
				 $sikon 	= 3;
				 $where 	= 'auto_vehicle_group';
				 $sql 		= "SELECT * FROM `webtracking_vehicle_autocheck` where $where = '$user_group' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
				 $sql2      = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_group = '$user_group' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
			 }else{
				 $sikon 	= 4;
				 $where 	= 'auto_vehicle_no';
				 $sql 		= "SELECT * FROM `webtracking_vehicle_autocheck` where $where = '$vehiclenya' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
				 $sql2      = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_no = '$vehiclenya' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
			 }
		 }elseif($user_level == 2){
			 if($user_group == "" || $user_group == "undefined"){
				 $sikon 	= 1;
				 $where 	= 'auto_vehicle_subcompany';
				 $sql 		= "SELECT * FROM `webtracking_vehicle_autocheck` where $where = '$subcompany_id' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
				 $sql2      = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_subcompany = '$subcompany_id' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
			 }elseif($vehiclenya == "" || $vehiclenya == "undefined"){
				 $sikon 	= 2;
				 $where 	= 'auto_vehicle_group';
				 $sql 		= "SELECT * FROM `webtracking_vehicle_autocheck` where $where = '$user_group' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
				 $sql2      = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_group = '$user_group' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
			 }else{
				 $sikon 	= 3;
				 $where 	= 'auto_vehicle_no';
				 $sql 		= "SELECT * FROM `webtracking_vehicle_autocheck` where $where = '$vehiclenya' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
				 $sql2      = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_no = '$vehiclenya' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
			 }
		 }elseif($user_level == 3){
			 if($vehiclenya == "" || $vehiclenya == "undefined"){
				 $sikon 	= 2;
				 $where 	= 'auto_vehicle_group';
				 $sql 		= "SELECT * FROM `webtracking_vehicle_autocheck` where $where = '$user_group' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
				 $sql2      = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_group = '$user_group' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
			 }else{
				 $sikon 	= 3;
				 $where 	= 'auto_vehicle_no';
				 $sql 		= "SELECT * FROM `webtracking_vehicle_autocheck` where $where = '$vehiclenya' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
				 $sql2      = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_no = '$vehiclenya' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
			 }
		 }else{
				 $sikon 	= 1;
				 $where 	= 'auto_vehicle_no';
				 $sql 		= "SELECT * FROM `webtracking_vehicle_autocheck` where $where = '$vehiclenya' and auto_last_position != 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
				 $sql2      = "SELECT * FROM `webtracking_vehicle_autocheck` where auto_vehicle_no = '$vehiclenya' and auto_last_position = 'Go to history' and auto_flag = '0' order by auto_vehicle_no ASC";
		 }

		 //print_r($company_gmop.'-'.$subcompany_id.'-'.$user_group.'-'.$vehiclenya.'-'.$user_id);exit();
		$q      	= $this->db->query($sql);
		$q2       	= $this->db->query($sql2);
		$result 	= $q->result_array();
		$result2  	= $q2->result_array();

		$datafix = array();
		for ($i=0; $i < sizeof($result); $i++) {
			array_push($datafix, array(
				"sikon" 					  => $sikon,
				"datafix" 					  => "true",
				"auto_id"                     => $result[$i]['auto_id'],
				"auto_user_id"                => $result[$i]['auto_user_id'],
				"auto_vehicle_id"             => $result[$i]['auto_vehicle_id'],
				"auto_vehicle_no"             => $result[$i]['auto_vehicle_no'],
				"auto_vehicle_name"           => $result[$i]['auto_vehicle_name'],
				"auto_vehicle_device"         => $result[$i]['auto_vehicle_device'],
				"auto_vehicle_type"           => $result[$i]['auto_vehicle_type'],
				"auto_vehicle_company"        => $result[$i]['auto_vehicle_company'],
				"auto_vehicle_subcompany"     => $result[$i]['auto_vehicle_subcompany'],
				"auto_vehicle_group"          => $result[$i]['auto_vehicle_group'],
				"auto_vehicle_subgroup"       => $result[$i]['auto_vehicle_subgroup'],
				"auto_vehicle_active_date2"   => $result[$i]['auto_vehicle_active_date2'],
				"auto_simcard"                => $result[$i]['auto_simcard'],
				"auto_status"          		  	=> $result[$i]['auto_status'],
				"auto_last_update"            => $result[$i]['auto_last_update'],
				"auto_last_check"             => $result[$i]['auto_last_check'],
				"auto_last_position"          => str_replace(array("\n","\r","'","'\'","/", "-"), "", $result[$i]['auto_last_position']),
				"auto_last_lat"               => $result[$i]['auto_last_lat'],
				"auto_last_long"              => $result[$i]['auto_last_long'],
				"auto_last_engine"            => $result[$i]['auto_last_engine'],
				"auto_last_gpsstatus"         => $result[$i]['auto_last_gpsstatus'],
				"auto_last_speed"             => $result[$i]['auto_last_speed'],
				"auto_last_course"            => $result[$i]['auto_last_course'],
				"auto_flag"                   => $result[$i]['auto_flag']
				//"auto_change_engine_status"   => $result[$i]['auto_change_engine_status'],
				//"auto_change_engine_datetime" => $result[$i]['auto_change_engine_datetime'],
				//"auto_change_position"        => str_replace(array("\n","\r", "'"), "", $result[$i]['auto_change_position']),
				//"auto_change_coordinate"      => $result[$i]['auto_change_coordinate']
			));
		}

		//$company_gmop.'-'.$subcompany_id.'-'.$user_group.'-'.$vehiclenya

		 //echo "<pre>";
		 //var_dump($datafix);die();
		 //echo "<pre>";

		 $this->params['company_gmop'] 		= $company_gmop;
		 $this->params['subcompany_id'] 	= $subcompany_id;
		 $this->params['user_group'] 		= $user_group;
		 $this->params['vehiclenya'] 		= $vehiclenya;
		 $this->params['vehicletotal'] = sizeof($result2);

		$this->params['vehicle'] 		= $datafix;
		$this->params['total_vehicle'] 	= sizeof($datafix);
		$html                       	= $this->load->view("realtimemaps/v_universalview", $this->params, true);
		$callback['vehicletotal']       = sizeof($result2);
		$callback['total_vehicle']      = sizeof($datafix);
		$callback['error']          	= false;
		$callback['html']           	= $html;
		echo json_encode($callback);
	}

	function tracking($device){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle_id = $device;
		$this->db   = $this->load->database("default", true);
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;

		if($this->sess->user_id == "1445"){
			$user_id = 3212; //tag
		}else{
			$user_id = $this->sess->user_id;
		}
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_id", $vehicle_id);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();


		// echo "<pre>";
		// var_dump($result);die();
		// echo "<pre>";

		$datafix = array();
		for ($i=0; $i < sizeof($result); $i++) {
			$jsonnya[$i] = json_decode($result[$i]['vehicle_autocheck']);

			array_push($datafix, array(
				 "vehicle_id"             => $result[$i]['vehicle_id'],
				 "vehicle_user_id"        => $result[$i]['vehicle_user_id'],
				 "vehicle_device"         => $result[$i]['vehicle_device'],
				 "vehicle_no"             => $result[$i]['vehicle_no'],
				 "vehicle_name"           => $result[$i]['vehicle_name'],
				 "vehicle_active_date2"   => $result[$i]['vehicle_active_date2'],
				 "vehicle_card_no"        => $result[$i]['vehicle_card_no'],
				 "vehicle_operator"       => $result[$i]['vehicle_operator'],
				 "vehicle_active_date"    => $result[$i]['vehicle_active_date'],
				 "vehicle_active_date1"   => $result[$i]['vehicle_active_date1'],
				 "vehicle_status"         => $result[$i]['vehicle_status'],
				 "vehicle_image"          => $result[$i]['vehicle_image'],
				 "vehicle_created_date"   => $result[$i]['vehicle_created_date'],
				 "vehicle_type"           => $result[$i]['vehicle_type'],
				 "vehicle_autorefill"     => $result[$i]['vehicle_autorefill'],
				 "vehicle_maxspeed"       => $result[$i]['vehicle_maxspeed'],
				 "vehicle_maxparking"     => $result[$i]['vehicle_maxparking'],
				 "vehicle_company"        => $result[$i]['vehicle_company'],
				 "vehicle_subcompany"     => $result[$i]['vehicle_subcompany'],
				 "vehicle_group"          => $result[$i]['vehicle_group'],
				 "vehicle_subgroup"       => $result[$i]['vehicle_subgroup'],
				 "vehicle_odometer"       => $result[$i]['vehicle_odometer'],
				 "vehicle_payment_type"   => $result[$i]['vehicle_payment_type'],
				 "vehicle_payment_amount" => $result[$i]['vehicle_payment_amount'],
				 "vehicle_fuel_capacity"  => $result[$i]['vehicle_fuel_capacity'],
				 // "vehicle_info"           => $result[$i]['vehicle_info'],
				 "vehicle_sales"          => $result[$i]['vehicle_sales'],
				 "vehicle_teknisi_id"     => $result[$i]['vehicle_teknisi_id'],
				 "vehicle_tanggal_pasang" => $result[$i]['vehicle_tanggal_pasang'],
				 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $result[$i]['vehicle_imei']),
				 "vehicle_dbhistory"      => $result[$i]['vehicle_dbhistory'],
				 "vehicle_dbhistory_name" => $result[$i]['vehicle_dbhistory_name'],
				 "vehicle_dbname_live"    => $result[$i]['vehicle_dbname_live'],
				 "vehicle_isred"          => $result[$i]['vehicle_isred'],
				 "vehicle_modem"          => $result[$i]['vehicle_modem'],
				 "vehicle_card_no_status" => $result[$i]['vehicle_card_no_status'],
				 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_status),
		 		 "auto_last_update"       => $jsonnya[$i]->auto_last_update,
		 		 "auto_last_check"        => $jsonnya[$i]->auto_last_check,
		 		 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_position),
		 		 "auto_last_lat"          => substr($jsonnya[$i]->auto_last_lat, 0, 10),
		 		 "auto_last_long"         => substr($jsonnya[$i]->auto_last_long, 0, 10),
		 		 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_engine),
		 		 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_gpsstatus),
		 		 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_speed),
		 		 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_course),
		 		 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_flag)
			));
		}

		$this->params['vehicle']      = $datafix;
		$this->params['vehicletotal'] = $result;

		// echo "<pre>";
		// var_dump($this->params['vehicle']);die();
		// echo "<pre>";

		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/trackers/maps_view_onevehicle', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

	function online(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['sortby']  = "mobil_id";
		$this->params['orderby'] = "asc";
		$this->params['title']   = "Maps All";
		if($this->sess->user_id == "1445"){
			$user_id = 3212; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$companyid 			 = $this->uri->segment(3);

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}
		// $wherein = array("P", "K");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();


		// echo "<pre>";
		// var_dump($result);die();
		// echo "<pre>";

		$datafix = array();
		for ($i=0; $i < sizeof($result); $i++) {
			$jsonnya[$i] = json_decode($result[$i]['vehicle_autocheck']);

			if (str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_status) != "M") {
				array_push($datafix, array(
					 "vehicle_id"             => $result[$i]['vehicle_id'],
					 "vehicle_user_id"        => $result[$i]['vehicle_user_id'],
					 "vehicle_device"         => $result[$i]['vehicle_device'],
					 "vehicle_no"             => $result[$i]['vehicle_no'],
					 "vehicle_name"           => $result[$i]['vehicle_name'],
					 "vehicle_active_date2"   => $result[$i]['vehicle_active_date2'],
					 "vehicle_card_no"        => $result[$i]['vehicle_card_no'],
					 "vehicle_operator"       => $result[$i]['vehicle_operator'],
					 "vehicle_active_date"    => $result[$i]['vehicle_active_date'],
					 "vehicle_active_date1"   => $result[$i]['vehicle_active_date1'],
					 "vehicle_status"         => $result[$i]['vehicle_status'],
					 "vehicle_image"          => $result[$i]['vehicle_image'],
					 "vehicle_created_date"   => $result[$i]['vehicle_created_date'],
					 "vehicle_type"           => $result[$i]['vehicle_type'],
					 "vehicle_autorefill"     => $result[$i]['vehicle_autorefill'],
					 "vehicle_maxspeed"       => $result[$i]['vehicle_maxspeed'],
					 "vehicle_maxparking"     => $result[$i]['vehicle_maxparking'],
					 "vehicle_company"        => $result[$i]['vehicle_company'],
					 "vehicle_subcompany"     => $result[$i]['vehicle_subcompany'],
					 "vehicle_group"          => $result[$i]['vehicle_group'],
					 "vehicle_subgroup"       => $result[$i]['vehicle_subgroup'],
					 "vehicle_odometer"       => $result[$i]['vehicle_odometer'],
					 "vehicle_payment_type"   => $result[$i]['vehicle_payment_type'],
					 "vehicle_payment_amount" => $result[$i]['vehicle_payment_amount'],
					 "vehicle_fuel_capacity"  => $result[$i]['vehicle_fuel_capacity'],
					 // "vehicle_info"           => $result[$i]['vehicle_info'],
					 "vehicle_sales"          => $result[$i]['vehicle_sales'],
					 "vehicle_teknisi_id"     => $result[$i]['vehicle_teknisi_id'],
					 "vehicle_tanggal_pasang" => $result[$i]['vehicle_tanggal_pasang'],
					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $result[$i]['vehicle_imei']),
					 "vehicle_dbhistory"      => $result[$i]['vehicle_dbhistory'],
					 "vehicle_dbhistory_name" => $result[$i]['vehicle_dbhistory_name'],
					 "vehicle_dbname_live"    => $result[$i]['vehicle_dbname_live'],
					 "vehicle_isred"          => $result[$i]['vehicle_isred'],
					 "vehicle_modem"          => $result[$i]['vehicle_modem'],
					 "vehicle_card_no_status" => $result[$i]['vehicle_card_no_status'],
					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_status),
			 		 "auto_last_update"       => $jsonnya[$i]->auto_last_update,
			 		 "auto_last_check"        => $jsonnya[$i]->auto_last_check,
			 		 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_position),
			 		 "auto_last_lat"          => substr($jsonnya[$i]->auto_last_lat, 0, 10),
			 		 "auto_last_long"         => substr($jsonnya[$i]->auto_last_long, 0, 10),
			 		 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_engine),
			 		 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_gpsstatus),
			 		 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_speed),
			 		 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_course),
			 		 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_flag)
				));
			}
		}

		$this->params['vehicle']      = $datafix;
		$this->params['vehicletotal'] = $result;


		$this->params['poolmaster'] = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);


		// $hasilnya = json_encode($result);
		// $this->params['datanya'] = $hasilnya;

		// echo "<pre>";
		// var_dump($this->params['poolmaster']);die();
		// echo "<pre>";

		//get company
		$company                     = $this->dashboardmodel->getcompany_byowner();
		$this->params['company']     = $company;
		$this->params['companyid']   = $companyid;


		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/trackers/maps_view', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

	function offline(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['sortby']  = "mobil_id";
		$this->params['orderby'] = "asc";
		$this->params['title']   = "Maps All";
		if($this->sess->user_id == "1445"){
			$user_id = 3212; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$companyid 			 = $this->uri->segment(3);

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}
		// $wherein = array("P", "K");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();


		// echo "<pre>";
		// var_dump($result);die();
		// echo "<pre>";

		$datafix = array();
		for ($i=0; $i < sizeof($result); $i++) {
			$jsonnya[$i] = json_decode($result[$i]['vehicle_autocheck']);

			if (str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_status) == "M") {
				array_push($datafix, array(
					 "vehicle_id"             => $result[$i]['vehicle_id'],
					 "vehicle_user_id"        => $result[$i]['vehicle_user_id'],
					 "vehicle_device"         => $result[$i]['vehicle_device'],
					 "vehicle_no"             => $result[$i]['vehicle_no'],
					 "vehicle_name"           => $result[$i]['vehicle_name'],
					 "vehicle_active_date2"   => $result[$i]['vehicle_active_date2'],
					 "vehicle_card_no"        => $result[$i]['vehicle_card_no'],
					 "vehicle_operator"       => $result[$i]['vehicle_operator'],
					 "vehicle_active_date"    => $result[$i]['vehicle_active_date'],
					 "vehicle_active_date1"   => $result[$i]['vehicle_active_date1'],
					 "vehicle_status"         => $result[$i]['vehicle_status'],
					 "vehicle_image"          => $result[$i]['vehicle_image'],
					 "vehicle_created_date"   => $result[$i]['vehicle_created_date'],
					 "vehicle_type"           => $result[$i]['vehicle_type'],
					 "vehicle_autorefill"     => $result[$i]['vehicle_autorefill'],
					 "vehicle_maxspeed"       => $result[$i]['vehicle_maxspeed'],
					 "vehicle_maxparking"     => $result[$i]['vehicle_maxparking'],
					 "vehicle_company"        => $result[$i]['vehicle_company'],
					 "vehicle_subcompany"     => $result[$i]['vehicle_subcompany'],
					 "vehicle_group"          => $result[$i]['vehicle_group'],
					 "vehicle_subgroup"       => $result[$i]['vehicle_subgroup'],
					 "vehicle_odometer"       => $result[$i]['vehicle_odometer'],
					 "vehicle_payment_type"   => $result[$i]['vehicle_payment_type'],
					 "vehicle_payment_amount" => $result[$i]['vehicle_payment_amount'],
					 "vehicle_fuel_capacity"  => $result[$i]['vehicle_fuel_capacity'],
					 // "vehicle_info"           => $result[$i]['vehicle_info'],
					 "vehicle_sales"          => $result[$i]['vehicle_sales'],
					 "vehicle_teknisi_id"     => $result[$i]['vehicle_teknisi_id'],
					 "vehicle_tanggal_pasang" => $result[$i]['vehicle_tanggal_pasang'],
					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $result[$i]['vehicle_imei']),
					 "vehicle_dbhistory"      => $result[$i]['vehicle_dbhistory'],
					 "vehicle_dbhistory_name" => $result[$i]['vehicle_dbhistory_name'],
					 "vehicle_dbname_live"    => $result[$i]['vehicle_dbname_live'],
					 "vehicle_isred"          => $result[$i]['vehicle_isred'],
					 "vehicle_modem"          => $result[$i]['vehicle_modem'],
					 "vehicle_card_no_status" => $result[$i]['vehicle_card_no_status'],
					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_status),
					 "auto_last_update"       => $jsonnya[$i]->auto_last_update,
					 "auto_last_check"        => $jsonnya[$i]->auto_last_check,
					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_position),
					 "auto_last_lat"          => substr($jsonnya[$i]->auto_last_lat, 0, 10),
					 "auto_last_long"         => substr($jsonnya[$i]->auto_last_long, 0, 10),
					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_engine),
					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_gpsstatus),
					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_speed),
					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_course),
					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_flag)
				));
			}
		}

		$this->params['vehicle']      = $datafix;
		$this->params['vehicletotal'] = $result;

		$this->params['poolmaster'] = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);

		// $hasilnya = json_encode($result);
		// $this->params['datanya'] = $hasilnya;

		// echo "<pre>";
		// var_dump($this->params['vehicle'] );die();
		// echo "<pre>";

		//get company
		$company                     = $this->dashboardmodel->getcompany_byowner();
		$this->params['company']     = $company;
		$this->params['companyid']   = $companyid;


		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/trackers/maps_view', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

	function getdetailbydevid(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$device_id = $_POST['device_id'];

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $this->sess->user_id;

		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_id", $device_id);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();

		$datafix = array();
		for ($i=0; $i < sizeof($result); $i++) {
			$jsonnya[$i] = json_decode($result[$i]['vehicle_autocheck']);

			array_push($datafix, array(
				 "vehicle_id"             => $result[$i]['vehicle_id'],
				 "vehicle_user_id"        => $result[$i]['vehicle_user_id'],
				 "vehicle_device"         => $result[$i]['vehicle_device'],
				 "vehicle_no"             => $result[$i]['vehicle_no'],
				 "vehicle_name"           => $result[$i]['vehicle_name'],
				 "vehicle_active_date2"   => $result[$i]['vehicle_active_date2'],
				 "vehicle_card_no"        => $result[$i]['vehicle_card_no'],
				 "vehicle_operator"       => $result[$i]['vehicle_operator'],
				 "vehicle_active_date"    => $result[$i]['vehicle_active_date'],
				 "vehicle_active_date1"   => $result[$i]['vehicle_active_date1'],
				 "vehicle_status"         => $result[$i]['vehicle_status'],
				 "vehicle_image"          => $result[$i]['vehicle_image'],
				 "vehicle_created_date"   => $result[$i]['vehicle_created_date'],
				 "vehicle_type"           => $result[$i]['vehicle_type'],
				 "vehicle_autorefill"     => $result[$i]['vehicle_autorefill'],
				 "vehicle_maxspeed"       => $result[$i]['vehicle_maxspeed'],
				 "vehicle_maxparking"     => $result[$i]['vehicle_maxparking'],
				 "vehicle_company"        => $result[$i]['vehicle_company'],
				 "vehicle_subcompany"     => $result[$i]['vehicle_subcompany'],
				 "vehicle_group"          => $result[$i]['vehicle_group'],
				 "vehicle_subgroup"       => $result[$i]['vehicle_subgroup'],
				 "vehicle_odometer"       => $result[$i]['vehicle_odometer'],
				 "vehicle_payment_type"   => $result[$i]['vehicle_payment_type'],
				 "vehicle_payment_amount" => $result[$i]['vehicle_payment_amount'],
				 "vehicle_fuel_capacity"  => $result[$i]['vehicle_fuel_capacity'],
				 // "vehicle_info"           => $result[$i]['vehicle_info'],
				 "vehicle_sales"          => $result[$i]['vehicle_sales'],
				 "vehicle_teknisi_id"     => $result[$i]['vehicle_teknisi_id'],
				 "vehicle_tanggal_pasang" => $result[$i]['vehicle_tanggal_pasang'],
				 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $result[$i]['vehicle_imei']),
				 "vehicle_dbhistory"      => $result[$i]['vehicle_dbhistory'],
				 "vehicle_dbhistory_name" => $result[$i]['vehicle_dbhistory_name'],
				 "vehicle_dbname_live"    => $result[$i]['vehicle_dbname_live'],
				 "vehicle_isred"          => $result[$i]['vehicle_isred'],
				 "vehicle_modem"          => $result[$i]['vehicle_modem'],
				 "vehicle_card_no_status" => $result[$i]['vehicle_card_no_status'],
				 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_status),
		 		 "auto_last_update"       => $jsonnya[$i]->auto_last_update,
		 		 "auto_last_check"        => $jsonnya[$i]->auto_last_check,
		 		 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_position),
		 		 "auto_last_lat"          => substr($jsonnya[$i]->auto_last_lat, 0, 10),
		 		 "auto_last_long"         => substr($jsonnya[$i]->auto_last_long, 0, 10),
		 		 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_engine),
		 		 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_gpsstatus),
		 		 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_speed),
		 		 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_last_course),
		 		 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$i]->auto_flag)
			));
		}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";
		echo json_encode($datafix);
	}


}
