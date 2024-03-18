<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Dashboardmodel extends Model {

	function Dashboardmodel(){

	parent::Model();
	$this->load->model("gpsmodel");
	$this->load->model("m_poipoolmaster");
	}

	function getvehicle_byownerforheatmap(){
		$user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
		$user_parent         = $this->sess->user_parent;
		$user_id_role 			 = $this->sess->user_id_role;
		$user_id_fix         = $this->sess->user_id;
		//GET DATA FROM DB
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_user_id", 4408);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_user_id", 4408);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		return $rows;
		}

	function getvehicle_byowner(){
		$user_level          = $this->sess->user_level;
		$user_company        = $this->sess->user_company;
		$user_subcompany     = $this->sess->user_subcompany;
		$user_group          = $this->sess->user_group;
		$user_subgroup       = $this->sess->user_subgroup;
		$user_parent         = $this->sess->user_parent;
		$user_id_role 			 = $this->sess->user_id_role;
		$user_id_fix         = $this->sess->user_id;
		//GET DATA FROM DB
		$this->db->select("*");
		$this->db->order_by("vehicle_no","asc");

		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_company", $user_company);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		return $rows;
    }

	function getvehicle_bycompany($id)
	{
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_company",$id);
		$this->db->where("vehicle_status <>",3);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		return $rows;
    }

	function getvehicle_bycompany_master($id)
	{
		$this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_no,vehicle_name,vehicle_active_date2,vehicle_mv03");
		$this->db->where("vehicle_company",$id);
		$this->db->where("vehicle_status <>",3);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		return $rows;
    }

	function getvehicle_bydefault()
	{

		if($this->sess->user_id == "1445"){
			$user_id =  $this->sess->user_id; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db->select("vehicle_id,vehicle_device,vehicle_no");
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
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		return $rows;
    }

	function getvehicle_bydefault2()
	{

		if($this->sess->user_id == "1445"){
			$user_id =  $this->sess->user_id; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$user_id_fix = $user_id;

		$companyid 			 = $this->uri->segment(3);


		$user_dblive    = $this->sess->user_dblive;
		$datafromdblive = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
		$mastervehicle  = $this->m_poipoolmaster->getmastervehicle();

		// echo "<pre>";
		// var_dump($datafromdblive);die();
		// echo "<pre>";

		$datafix    = array();
		$datafixbgt = array();
		$deviceidygtidakada = array();

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			// $device = $datafromdblive[$i]['gps_name'].'@'.$datafromdblive[$i]['gps_host'];
			$device = explode("@", $mastervehicle[$i]['vehicle_device']);
			$device0 = $device[0];
			$device1 = $device[1];

			// print_r("devicenya : ".$device0);
			// $getdata[] = $this->m_poipoolmaster->getmastervehiclebydevid($device);
			$getdata[]                 = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
			// $laspositionfromgpsmodel[] = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");
				if (sizeof($getdata[$i]) > 0) {
					// $jsonnya[] = json_decode($getdata[$i][0]['vehicle_autocheck'], true);
							array_push($datafix, array(
  						 "is_update" 						  => "yes",
							 "vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
		 					 "vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
		 					 "vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
		 					 "vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
		 					 "vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
		 					 "vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
		 					 "vehicle_card_no"        => $mastervehicle[$i]['vehicle_card_no'],
		 					 "vehicle_operator"       => $mastervehicle[$i]['vehicle_operator'],
		 					 "vehicle_active_date"    => $mastervehicle[$i]['vehicle_active_date'],
		 					 "vehicle_active_date1"   => $mastervehicle[$i]['vehicle_active_date1'],
		 					 "vehicle_status"         => $mastervehicle[$i]['vehicle_status'],
		 					 "vehicle_image"          => $mastervehicle[$i]['vehicle_image'],
		 					 "vehicle_created_date"   => $mastervehicle[$i]['vehicle_created_date'],
		 					 "vehicle_type"           => $mastervehicle[$i]['vehicle_type'],
		 					 "vehicle_autorefill"     => $mastervehicle[$i]['vehicle_autorefill'],
		 					 "vehicle_maxspeed"       => $mastervehicle[$i]['vehicle_maxspeed'],
		 					 "vehicle_maxparking"     => $mastervehicle[$i]['vehicle_maxparking'],
		 					 "vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
		 					 "vehicle_subcompany"     => $mastervehicle[$i]['vehicle_subcompany'],
		 					 "vehicle_group"          => $mastervehicle[$i]['vehicle_group'],
		 					 "vehicle_subgroup"       => $mastervehicle[$i]['vehicle_subgroup'],
		 					 "vehicle_odometer"       => $mastervehicle[$i]['vehicle_odometer'],
		 					 "vehicle_payment_type"   => $mastervehicle[$i]['vehicle_payment_type'],
		 					 "vehicle_payment_amount" => $mastervehicle[$i]['vehicle_payment_amount'],
		 					 "vehicle_fuel_capacity"  => $mastervehicle[$i]['vehicle_fuel_capacity'],
		 					 // "vehicle_info"           => $result[$i]['vehicle_info'],
		 					 "vehicle_sales"          => $mastervehicle[$i]['vehicle_sales'],
		 					 "vehicle_teknisi_id"     => $mastervehicle[$i]['vehicle_teknisi_id'],
		 					 "vehicle_tanggal_pasang" => $mastervehicle[$i]['vehicle_tanggal_pasang'],
		 					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[$i]['vehicle_imei']),
		 					 "vehicle_dbhistory"      => $mastervehicle[$i]['vehicle_dbhistory'],
		 					 "vehicle_dbhistory_name" => $mastervehicle[$i]['vehicle_dbhistory_name'],
		 					 "vehicle_dbname_live"    => $mastervehicle[$i]['vehicle_dbname_live'],
		 					 "vehicle_isred"          => $mastervehicle[$i]['vehicle_isred'],
		 					 "vehicle_modem"          => $mastervehicle[$i]['vehicle_modem'],
		 					 "vehicle_card_no_status" => $mastervehicle[$i]['vehicle_card_no_status'],
							 // "position"  	  				  => $laspositionfromgpsmodel[$i]->georeverse->display_name,
							 "vehicle_autocheck" 	 		=> $getdata[$i][0]['vehicle_autocheck']
							));
				}else {
					// $jsonnya2[$i] = json_decode($mastervehicle[$i]['vehicle_autocheck'], true);
					array_push($deviceidygtidakada, array(
						"is_update" 						 => "no",
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"vehicle_card_no"        => $mastervehicle[$i]['vehicle_card_no'],
						"vehicle_operator"       => $mastervehicle[$i]['vehicle_operator'],
						"vehicle_active_date"    => $mastervehicle[$i]['vehicle_active_date'],
						"vehicle_active_date1"   => $mastervehicle[$i]['vehicle_active_date1'],
						"vehicle_status"         => $mastervehicle[$i]['vehicle_status'],
						"vehicle_image"          => $mastervehicle[$i]['vehicle_image'],
						"vehicle_created_date"   => $mastervehicle[$i]['vehicle_created_date'],
						"vehicle_type"           => $mastervehicle[$i]['vehicle_type'],
						"vehicle_autorefill"     => $mastervehicle[$i]['vehicle_autorefill'],
						"vehicle_maxspeed"       => $mastervehicle[$i]['vehicle_maxspeed'],
						"vehicle_maxparking"     => $mastervehicle[$i]['vehicle_maxparking'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_subcompany"     => $mastervehicle[$i]['vehicle_subcompany'],
						"vehicle_group"          => $mastervehicle[$i]['vehicle_group'],
						"vehicle_subgroup"       => $mastervehicle[$i]['vehicle_subgroup'],
						"vehicle_odometer"       => $mastervehicle[$i]['vehicle_odometer'],
						"vehicle_payment_type"   => $mastervehicle[$i]['vehicle_payment_type'],
						"vehicle_payment_amount" => $mastervehicle[$i]['vehicle_payment_amount'],
						"vehicle_fuel_capacity"  => $mastervehicle[$i]['vehicle_fuel_capacity'],
						// "vehicle_info"           => $result[$i]['vehicle_info'],
						"vehicle_sales"          => $mastervehicle[$i]['vehicle_sales'],
						"vehicle_teknisi_id"     => $mastervehicle[$i]['vehicle_teknisi_id'],
						"vehicle_tanggal_pasang" => $mastervehicle[$i]['vehicle_tanggal_pasang'],
						"vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[$i]['vehicle_imei']),
						"vehicle_dbhistory"      => $mastervehicle[$i]['vehicle_dbhistory'],
						"vehicle_dbhistory_name" => $mastervehicle[$i]['vehicle_dbhistory_name'],
						"vehicle_dbname_live"    => $mastervehicle[$i]['vehicle_dbname_live'],
						"vehicle_isred"          => $mastervehicle[$i]['vehicle_isred'],
						"vehicle_modem"          => $mastervehicle[$i]['vehicle_modem'],
						"vehicle_card_no_status" => $mastervehicle[$i]['vehicle_card_no_status'],
						// "position"  	  				 => $laspositionfromgpsmodel[$i]->georeverse->display_name,
						"vehicle_autocheck" 	 	 => $mastervehicle[$i]['vehicle_autocheck']
					));
				}
		}

		// echo "<pre>";
		// var_dump($laspositionfromgpsmodel[0]->georeverse->display_name);die();
		// echo "<pre>";

		$datafixbgt = array_merge($datafix, $deviceidygtidakada);
		$throwdatatoview = array();
		for ($loop=0; $loop < sizeof($datafixbgt); $loop++) {
			$jsonnya[$loop] = json_decode($datafixbgt[$loop]['vehicle_autocheck'], true);

			array_push($throwdatatoview, array(
				"is_update" 						 => $datafixbgt[$loop]['is_update'],
				"vehicle_id"             => $datafixbgt[$loop]['vehicle_id'],
				"vehicle_user_id"        => $datafixbgt[$loop]['vehicle_user_id'],
				"vehicle_device"         => $datafixbgt[$loop]['vehicle_device'],
				"vehicle_no"             => $datafixbgt[$loop]['vehicle_no'],
				"vehicle_name"           => $datafixbgt[$loop]['vehicle_name'],
				"vehicle_active_date2"   => $datafixbgt[$loop]['vehicle_active_date2'],
				"vehicle_card_no"        => $datafixbgt[$loop]['vehicle_card_no'],
				"vehicle_operator"       => $datafixbgt[$loop]['vehicle_operator'],
				"vehicle_active_date"    => $datafixbgt[$loop]['vehicle_active_date'],
				"vehicle_active_date1"   => $datafixbgt[$loop]['vehicle_active_date1'],
				"vehicle_status"         => $datafixbgt[$loop]['vehicle_status'],
				"vehicle_image"          => $datafixbgt[$loop]['vehicle_image'],
				"vehicle_created_date"   => $datafixbgt[$loop]['vehicle_created_date'],
				"vehicle_type"           => $datafixbgt[$loop]['vehicle_type'],
				"vehicle_autorefill"     => $datafixbgt[$loop]['vehicle_autorefill'],
				"vehicle_maxspeed"       => $datafixbgt[$loop]['vehicle_maxspeed'],
				"vehicle_maxparking"     => $datafixbgt[$loop]['vehicle_maxparking'],
				"vehicle_company"        => $datafixbgt[$loop]['vehicle_company'],
				"vehicle_subcompany"     => $datafixbgt[$loop]['vehicle_subcompany'],
				"vehicle_group"          => $datafixbgt[$loop]['vehicle_group'],
				"vehicle_subgroup"       => $datafixbgt[$loop]['vehicle_subgroup'],
				"vehicle_odometer"       => $datafixbgt[$loop]['vehicle_odometer'],
				"vehicle_payment_type"   => $datafixbgt[$loop]['vehicle_payment_type'],
				"vehicle_payment_amount" => $datafixbgt[$loop]['vehicle_payment_amount'],
				"vehicle_fuel_capacity"  => $datafixbgt[$loop]['vehicle_fuel_capacity'],
				"vehicle_sales"          => $datafixbgt[$loop]['vehicle_sales'],
				"vehicle_teknisi_id"     => $datafixbgt[$loop]['vehicle_teknisi_id'],
				"vehicle_tanggal_pasang" => $datafixbgt[$loop]['vehicle_tanggal_pasang'],
				"vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $datafixbgt[$loop]['vehicle_imei']),
				"vehicle_dbhistory"      => $datafixbgt[$loop]['vehicle_dbhistory'],
				"vehicle_dbhistory_name" => $datafixbgt[$loop]['vehicle_dbhistory_name'],
				"vehicle_dbname_live"    => $datafixbgt[$loop]['vehicle_dbname_live'],
				"vehicle_isred"          => $datafixbgt[$loop]['vehicle_isred'],
				"vehicle_modem"          => $datafixbgt[$loop]['vehicle_modem'],
				"vehicle_card_no_status" => $datafixbgt[$loop]['vehicle_card_no_status'],
				// "auto_last_position"  	 => str_replace(array("\n","\r","'","'\'","/", "-"), "", $datafixbgt[$loop]['position']),
				"auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_status']),
				"auto_last_update"       => date("d F Y H:i:s", strtotime($jsonnya[$loop]['auto_last_update'])),
				"auto_last_check"        => $jsonnya[$loop]['auto_last_check'],
				// "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_position']),
				"auto_last_lat"          => substr($jsonnya[$loop]['auto_last_lat'], 0, 10),
				"auto_last_long"         => substr($jsonnya[$loop]['auto_last_long'], 0, 10),
				"auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_engine']),
				"auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_gpsstatus']),
				"auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_speed']),
				"auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_course']),
				"auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_flag'])
			));
		}
		return $throwdatatoview;
    }

	function getvehicle_report()
	{
		$userid          = $this->sess->user_id;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$privilegecode   = $this->sess->user_id_role;
		$user_level		   = $this->sess->user_level;

		$this->db = $this->load->database("default", true);
		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");

			if ($privilegecode == 0) {
				$this->db->where("vehicle_typeunit", 0);
				$this->db->where("vehicle_user_id", $userid);
			}elseif ($privilegecode == 1) {
				$this->db->where("vehicle_typeunit", 0);
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 2) {
				$this->db->where("vehicle_typeunit", 0);
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 3) {
				$this->db->where("vehicle_typeunit", 0);
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("vehicle_typeunit", 0);
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 5) {
				$this->db->where("vehicle_typeunit", 0);
				$this->db->where("vehicle_company", $user_company);
			}elseif ($privilegecode == 6) {
				$this->db->where("vehicle_typeunit", 0);
				$this->db->where("vehicle_company", $user_company);
			}elseif ($privilegecode == 10) {
				$this->db->where("vehicle_typeunit", 1);
				$this->db->where("vehicle_company", $user_company);
			}else {
				$this->db->where("vehicle_typeunit", 0);
				$this->db->where("vehicle_subgroup", $user_subgroup);
			}

			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_type <>", "TJAM");

		// if ($this->sess->user_type == 2)
		// {
		// 	$this->db->where("vehicle_user_id", $this->sess->user_id);
		// 	$this->db->or_where("vehicle_company", $this->sess->user_company);
		// 	$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		// }
		// else
		// if ($this->sess->user_type == 3)
		// {
		// 	$this->db->where("user_agent", $this->sess->user_agent);
		// }
		// //tambahan, user group yg open playback report
		// if ($this->sess->user_group <> 0)
		// {
		// 	$this->db->where("vehicle_group", $this->sess->user_group);
		// }
		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		return $rows;
	}

	function getjson_status($id){
		$this->db->select("vehicle_id,vehicle_autocheck");
		$this->db->where("vehicle_device",$id);
		$this->db->where("vehicle_status <>",3);
		$q = $this->db->get("vehicle");
		$row = $q->row();
		if(count($row)>0){
			$json = json_decode($row->vehicle_autocheck);
		}
		else{
			$json = "";
		}
		return $json;
    }

	function getjson_status2($id){
		$user_dblive   = $this->sess->user_dblive;
		$device        = explode("@", $id);
		$device0       = $device[0];
		$device1       = $device[1];
		$data          = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
		if(sizeof($data) > 0){
			$json = json_decode($data[0]['vehicle_autocheck']);
		}
		else{
			$json = "";
		}
		// echo "<pre>";
		// var_dump($json);die();
		// echo "<pre>";
		return $json;
  }

	function get_company_bylevel(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$privilegecode 						= $this->sess->user_id_role;


		$this->db->order_by("company_name","asc");
			if ($privilegecode == 0) {
				$this->db->where("company_created_by", $this->sess->user_id);
			}elseif ($privilegecode == 1) {
				$this->db->where("company_created_by", $this->sess->user_parent);
			}elseif ($privilegecode == 2) {
				$this->db->where("company_created_by", $this->sess->user_parent);
			}elseif ($privilegecode == 3) {
				$this->db->where("company_created_by", $this->sess->user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("company_created_by", $this->sess->user_parent);
			}elseif ($privilegecode == 5) {
				$this->db->where("company_created_by", $this->sess->user_company);
			}elseif ($privilegecode == 6) {
				$this->db->where("company_created_by", $this->sess->user_company);
			}

		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}

	function getcompany_byowner($privilegecode)
	{
		$this->db->order_by("company_name","asc");

			if ($privilegecode == 0) {
				$this->db->where("company_created_by", $this->sess->user_id);
			}elseif($privilegecode == 1) {
				$this->db->where("company_created_by", $this->sess->user_parent);
			}elseif($privilegecode == 2){
				$this->db->where("company_created_by",$this->sess->user_parent);
			}elseif($privilegecode == 3){
				$this->db->where("company_created_by",$this->sess->user_parent);
			}elseif($privilegecode == 4){
				$this->db->where("company_created_by",$this->sess->user_parent);
			}elseif($privilegecode == 5){
				// $this->db->where("company_created_by",$this->sess->user_company);
					$this->db->where("company_id", $this->sess->user_company);
			}elseif($privilegecode == 6){
				// $this->db->where("company_created_by",$this->sess->user_company);
					$this->db->where("company_id", $this->sess->user_company);
			}elseif($privilegecode == 10){
					$this->db->where("company_id", $this->sess->user_parent);
			}elseif($this->sess->user_level == 3){
				$this->db->where("company_id",$this->sess->user_company);
			}else{
				$this->db->where("company_id",0);
			}

		$this->db->where("company_flag",0);
		$q = $this->db->get("company");
		$rows = $q->result();
		return $rows;
    }

		function get_all_company()
		{
			$this->db->order_by("company_name","asc");
			$this->db->where("company_created_by", 4408);
			$this->db->where("company_flag",0);
			$q = $this->db->get("company");
			$rows = $q->result();
			return $rows;
			}

	function getcompany_name()
	{
		$this->db->order_by("company_name","asc");
		$this->db->select("company_id,company_name");
		$this->db->where("company_flag",0);
		$q = $this->db->get("company");
		$rows = $q->result();
		return $rows;
    }

	function getcompany_id($id)
	{
		$this->db->order_by("company_name","asc");
		$this->db->select("company_id,company_name");
		$this->db->where("company_id",$id);
		$this->db->where("company_flag",0);
		$q = $this->db->get("company");
		$rows = $q->row();
		return $rows;
    }

		function getcompany_idforevidence($id)
		{
			$this->dbdefault = $this->load->database("default", true);
			$this->dbdefault->order_by("company_name","asc");
			$this->dbdefault->select("company_id,company_name");
			$this->dbdefault->where("company_id",$id);
			$this->dbdefault->where("company_flag",0);
			$q               = $this->dbdefault->get("company");
			$rows            = $q->row();
			return $rows;
	    }

	function getstreet_id($param1, $id)
	{
		$this->db->order_by("street_name","asc");
		$this->db->select("street_id, street_name, street_alias");
		if ($param1 == "double") {
			$this->db->where_in("street_id",$id);
		}elseif ($param1 == "doublekm") {
			$this->db->where_in("street_name",$id);
		}else {
			$this->db->where("street_id",$id);
		}
		// $this->db->where("company_flag",0);
		$q = $this->db->get("street");
		$rows = $q->row();
		return $rows;
  }

	function gettotalengine($companyid)
	{
		$this->db->order_by("vehicle_id","asc");
		$this->db->select("vehicle_id,vehicle_company,vehicle_autocheck");
		$this->db->where("vehicle_company",$companyid);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		$rows = $q->result();

		$total_on = 0;
		$total_off = 0;
		$total_nodata = 0;

		for($i=0; $i < count($rows); $i++)
		{
			$json = json_decode($rows[$i]->vehicle_autocheck);
			if(isset($json)){
				if (isset($json->auto_last_engine)) {
					if($json->auto_last_engine == "OFF" ){
						$total_off = $total_off + 1;
					}
					if($json->auto_last_engine == "ON" ){
						$total_on = $total_on + 1;
					}
					if($json->auto_last_engine == "NO DATA" ){
						$total_nodata = $total_nodata + 1;
					}
				}
			}

		}
		return $total_off."|".$total_on."|".count($rows)."|".$total_nodata;
    }

	function gettotalstatus($userid)
	{
		$this->db->order_by("vehicle_id","asc");
		$this->db->select("vehicle_id,vehicle_autocheck");

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$user_id_fix     = $this->sess->user_id;
		$user_excavator  = $this->sess->user_excavator;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
		$this->db->order_by("vehicle_id","asc");

		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 10){
			$this->db->where("vehicle_company", $user_company);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		if ($user_excavator == 1) {
			$this->db->where("vehicle_typeunit", 1);
		}

		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_gotohistory", 0);
		//$this->db->where("vehicle_autocheck is not NULL");
		$q = $this->db->get("vehicle");
		$rows = $q->result();

		$total_p = 0;
		$total_k = 0;
		$total_m = 0;

		$total_on = 0;
		$total_off = 0;
		$total_nodata = 0;

		for($i=0; $i < count($rows); $i++)
		{

			$json = json_decode($rows[$i]->vehicle_autocheck);
			if(isset($json->auto_status)){
				if($json->auto_status == "P" ){
					$total_p = $total_p + 1;
				}
				if($json->auto_status == "K" ){
					$total_k = $total_k + 1;
				}
				if($json->auto_status == "M" ){
					$total_m = $total_m + 1;
				}
				if($json->auto_last_engine == "ON" ){
					$total_on = $total_on + 1;
				}
				if($json->auto_last_engine == "OFF" ){
					$total_off = $total_off + 1;
				}
				if($json->auto_last_engine == "NO DATA" ){
					$total_nodata = $total_nodata + 1;
				}
			}

		}
		return $total_p."|".$total_k."|".$total_m."|".count($rows)."|".$total_off."|".$total_on."|".$total_nodata;
    }

	function gettotalspeed($userid)
	{
		if($this->sess->user_id == "1445"){
			$user_id =  $this->sess->user_id; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("vehicle_id,vehicle_autocheck");
		$this->db->order_by("vehicle_id","asc");

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
		$q = $this->db->get("vehicle");
		$rows = $q->result();

		$total_0 = 0;
		$total_40 = 0;
		$total_80 = 0;
		$data = array();
		unset($data);
		for($i=0; $i < count($rows); $i++)
		{
			$json = json_decode($rows[$i]->vehicle_autocheck);
			if(isset($json)){

				if($json->auto_last_speed >= 0 && $json->auto_last_speed <= 39){
					$total_0 = $total_0 + 1;
					/*$data['data_0'][$i]->VehicleNo = $rows[$i]->auto_vehicle_no;
					$data['data_0'][$i]->Speed = $rows[$i]->auto_last_speed;
					$data['data_0'][$i]->Position = $rows[$i]->auto_last_position;*/
				}
				if($json->auto_last_speed >= 40 && $json->auto_last_speed <= 79){
					$total_40 = $total_40 + 1;
					/*$data['data_40'][$i]->VehicleNo = $rows[$i]->auto_vehicle_no;
					$data['data_40'][$i]->Speed = $rows[$i]->auto_last_speed;
					$data['data_40'][$i]->Position = $rows[$i]->auto_last_position;*/
				}
				if($json->auto_last_speed >= 80){
					$total_80 = $total_80 + 1;
					/*$data[$total_80-1]->VehicleNo = $rows[$i]->auto_vehicle_no;
					$data[$total_80-1]->Speed = $rows[$i]->auto_last_speed;
					$data[$total_80-1]->Position = $rows[$i]->auto_last_position;
					*/
				}
			}
		}
		return $total_0."|".$total_40."|".$total_80."|".count($rows);
    }

	function getoverspeed($userid)
	{
		$this->db->order_by("auto_vehicle_id","asc");
		$this->db->select("auto_vehicle_no,auto_last_speed,auto_last_position,auto_last_update");
		//khusus demo transporter
		if($this->sess->user_id == "1445"){
			$this->db->where("auto_user_id", $this->sess->user_id);
		}else{
			$this->db->where("auto_user_id",$this->sess->user_id);
		}
		$this->db->where("auto_last_speed >=",80);
		$this->db->where("auto_flag",0);
		$q = $this->db->get("vehicle_autocheck");
		$rows = $q->result();
		return $rows;
    }

	function getlastcheck()
	{
		$this->db->order_by("auto_last_check","desc");
		$this->db->limit(1);
		$this->db->select("auto_last_check");
		/*
		if($this->sess->user_level == 1){
			//khusus demo transporter
			if($this->sess->user_id == "1445"){
				$this->db->where("auto_user_id", $this->sess->user_id); // $this->sess->user_id tag
			}else{
				$this->db->where("auto_user_id",$this->sess->user_id);
			}
		}else if($this->sess->user_level == 2){
			$this->db->where("auto_vehicle_company",$this->sess->user_company);
		}else{
			$this->db->where("auto_user_id",0);
		}
		*/

		//khusus demo transporter
		if($this->sess->user_id == "1445"){
			$this->db->where("auto_user_id", $this->sess->user_id);
		}
		else{
			if ($this->sess->user_type == 2)
			{
				$this->db->where("auto_user_id", $this->sess->user_id);
				$this->db->or_where("auto_vehicle_company", $this->sess->user_company);
				//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			}

			//tambahan, user group yg open playback report
			if ($this->sess->user_group <> 0)
			{
				$this->db->where("auto_vehicle_group", $this->sess->user_group);
			}
		}

		$this->db->where("auto_flag",0);
		$q = $this->db->get("vehicle_autocheck");
		$rows = $q->row();
		return $rows;
    }

	function getlastcheck_masih_error()
	{
		if($this->sess->user_id == "1445"){
			$user_id =  $this->sess->user_id; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("vehicle_id,vehicle_autocheck");
		$this->db->order_by("vehicle_id","asc");

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
		$q = $this->db->get("vehicle");
		$rows = $q->result();

		$data = json_decode($rows);



    }

	function GetLastInfoON($vehicleid, $startdate="", $enddate="")
	{

		$this->db = $this->load->database("default", TRUE);
		$lastON = "-";

		$this->db->where("vehicle_id",$vehicleid);
		$qvehicle = $this->db->get("vehicle");
		$rowvehicle = $qvehicle->row();

		if(count($rowvehicle)>0)
		{
			$ex_device = explode("@",$rowvehicle->vehicle_device);
			$name = $ex_device[0];
			$host = $ex_device[1];
			//print_r($name);
			if($rowvehicle->vehicle_type == "TK315DOOR" || $rowvehicle->vehicle_type == "TK315N" || $rowvehicle->vehicle_type == "TK309N" || $rowvehicle->vehicle_type == "GT06N")
			{
				$ex_device = explode("@",$rowvehicle->vehicle_device);
				$device_imei = $ex_device[0];
				$device_host = $ex_device[1];

							$tables = $this->gpsmodel->getTable($rowvehicle); //print_r($tables);
							$this->db = $this->load->database($tables["dbname"], TRUE);
							$this->db->limit(1);
							$this->db->order_by("gps_info_time", "asc");
							$this->db->select("gps_info_id,gps_info_time");
							$this->db->where("gps_info_io_port", "0000100000");
							$this->db->where("gps_info_device", $name."@".$host);
							$this->db->where("gps_info_time >=", $startdate);
							$this->db->where("gps_info_time <=", $enddate);
							$q = $this->db->get($tables['info']);

							if ($q->num_rows() == 0)
							{	//cari dari history
								$tablehist = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));
								$json = json_decode($rowvehicle->vehicle_info);
								if (isset($json->vehicle_ws))
								{
									$this->db = $this->load->database("gpshistory2", TRUE);
								}
								else
								{
									$istbl_history = $this->config->item("dbhistory_default");
									if($this->config->item("is_dbhistory") == 1)
									{
										$istbl_history = $rowvehicle->vehicle_dbhistory_name;
									}
									$this->db = $this->load->database($istbl_history, TRUE);
								}

								$this->db->limit(1);
								$this->db->order_by("gps_info_time", "asc");
								$this->db->select("gps_info_id,gps_info_time");
								$this->db->where("gps_info_io_port", "0000100000");
								$this->db->where("gps_info_device", $name."@".$host);
								$this->db->where("gps_info_time >=", $startdate);
								$this->db->where("gps_info_time <=", $enddate);
								$q = $this->db->get($tablehist);
							}

							$row = $q->row();
							$lastON = $row->gps_info_time;

			}
			else
			{

				$startdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
				$enddate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));

					$tables = $this->gpsmodel->getTable($rowvehicle);

					$this->db = $this->load->database($tables["dbname"], TRUE);

					$this->db->limit(1);
					$this->db->order_by("gps_info_time", "asc");
					$this->db->select("gps_info_id,gps_info_time");
					$this->db->where("gps_info_io_port", "0000100000");
					$this->db->where("gps_info_device", $name."@".$host);
					$this->db->where("gps_info_time >=", $startdate);
					$this->db->where("gps_info_time <=", $enddate);
					$q = $this->db->get($tables['info']);



				if ($q->num_rows() == 0)
				{
					$tablehist = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

					$json = json_decode($rowvehicle->vehicle_info);
					if (isset($json->vehicle_ws))
					{
						$this->db = $this->load->database("gpshistory2", TRUE);
					}
					else
					{
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->db = $this->load->database($istbl_history, TRUE);
					}

					//alatberat
					if ($this->config->item("alatberat_app"))
					{
						$this->db = $this->load->database("gpshistory2", TRUE);
					}

					$this->db->limit(1);
					$this->db->order_by("gps_info_time", "asc");
					$this->db->select("gps_info_id,gps_info_time");
					$this->db->where("gps_info_io_port", "0000100000");
					$this->db->where("gps_info_device", $name."@".$host);
					$this->db->where("gps_info_time >=", $startdate);
					$this->db->where("gps_info_time <=", $enddate);
					$q = $this->db->get($tablehist);

					if ($q->num_rows() == 0) return;
				}

				$row = $q->row();
				$lastON =  date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($row->gps_info_time)));

			}


		}

		//print_r($lastON);exit();
		return $lastON;
	}

	function gettotalsummary_pervehicle($vehicledevice,$dbtable,$sdate,$edate)
	{
		$this->db = $this->load->database("operational_report", TRUE);
		$this->db->order_by("trip_mileage_id","asc");
		$this->db->select("trip_mileage_duration_sec,trip_mileage_trip_mileage");
		$this->db->where("trip_mileage_vehicle_id",$vehicledevice);
		$this->db->where("trip_mileage_engine",1); //only ON
		$this->db->where("trip_mileage_start_time >=",$sdate);
		$this->db->where("trip_mileage_start_time <=",$edate);
		$q = $this->db->get($dbtable);
		$rows = $q->result();

		$total_km = 0;
		$total_dur = 0;
		$gtotal_km = 0;
		$gtotal_dur = 0;
		for($i=0; $i < count($rows); $i++)
		{
			$gtotal_km = $gtotal_km + $rows[$i]->trip_mileage_trip_mileage;
			$gtotal_dur = $gtotal_dur + $rows[$i]->trip_mileage_duration_sec;
		}
		return $gtotal_dur."|".$gtotal_km;
    }

	function get_devicealert()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $this->sess->user_id;

		//GET DATA FROM DB
		$this->db->select("vehicle_device,vehicle_no,vehicle_name,vehicle_type");
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
		//$this->db->where("vehicle_device", "352312090371140@TK315");
		$this->db->where("vehicle_status <>", 3);
		$q             = $this->db->get("vehicle");
		$mastervehicle = $q->result_array();
		$dataalert_fix = array();
		for ($i=0; $i < sizeof($mastervehicle); $i++)
		{
			$ex_device    = explode("@", $mastervehicle[$i]['vehicle_device']);
			$device       = $ex_device[0];
			$host         = $ex_device[1];
			$type         = $mastervehicle[$i]['vehicle_type'];
			//get alert by imei
			$data_alert[] = $this->get_gpsalert($device,$host,$type);
			// echo "<pre>";
			// // var_dump($device.'-'.$host.'-'.$type);die();
			// var_dump($data_alert);die();
			// echo "<pre>";
				if (sizeof($data_alert[$i]) > 0){
					// if ($data_alert[$i][0]['gps_speed'] > $data_alert[$i][0]['gps_speed_limit']) {
						array_push($dataalert_fix, array(
						 "vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						 "vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						 "vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						 "vehicle_alert"          => $data_alert[$i][0]['gps_alert'],
						 "vehicle_lat"            => $data_alert[$i][0]['gps_latitude_real'],
						 "vehicle_lng"            => $data_alert[$i][0]['gps_longitude_real'],
						 "gps_alert"              => $data_alert[$i][0]['gps_alert'],
						 "gps_status"             => $data_alert[$i][0]['gps_status'],
						 "gps_speed"              => $data_alert[$i][0]['gps_speed'],
						 "gps_geofence"           => $data_alert[$i][0]['gps_geofence'],
						 "gps_speed_limit"        => $data_alert[$i][0]['gps_speed_limit'],
						 "gps_speed_status"       => $data_alert[$i][0]['gps_speed_status'],
						 "gps_time" 							=> $data_alert[$i][0]['gps_time']
						));
					// }
				}
		}
		// $data_alert = $dataalert_fix;
		return $dataalert_fix;
  }

	function get_devicealert_newmethod()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
			$data_alert = $this->get_gpsalert_newmethod();
			// echo "<pre>";
			// // var_dump($device.'-'.$host.'-'.$type);die();
			// var_dump($data_alert);die();
			// echo "<pre>";
			$dataalert_fix = array();
				if (sizeof($data_alert) > 0){
						array_push($dataalert_fix, array(
						 "vehicle_alert"          => $data_alert[0]['gps_alert'],
						 "vehicle_lat"            => $data_alert[0]['gps_latitude_real'],
						 "vehicle_lng"            => $data_alert[0]['gps_longitude_real'],
						 "gps_name"         		  => $data_alert[0]['gps_name'],
						 "gps_host"         		  => $data_alert[0]['gps_host'],
						 "gps_alert"              => $data_alert[0]['gps_alert'],
						 "gps_status"             => $data_alert[0]['gps_status'],
						 "gps_speed"              => $data_alert[0]['gps_speed'],
						 "gps_geofence"           => $data_alert[0]['gps_geofence'],
						 "gps_speed_limit"        => $data_alert[0]['gps_speed_limit'],
						 "gps_speed_status"       => $data_alert[0]['gps_speed_status'],
						 "gps_time" 							=> $data_alert[0]['gps_time']
						));
				}
		return $dataalert_fix;
  }

	function get_gpsalert_newmethod(){
		$this->dbalert = $this->load->database($this->sess->user_dblive, true);
		$datefrom  = date("Y-m-d")." 00:00:00";
		$dateuntil = date("Y-m-d")." 23:59:00";

		$table_alert = "webtracking_gps_alert";
		$this->dbalert->where("gps_speed_status", 1);
		$this->dbalert->where("gps_speed >= 10.7");
		$this->dbalert->where("gps_speed_limit > 0");
		$this->dbalert->where("gps_time >=", $datefrom);
		$this->dbalert->where("gps_time <=", $dateuntil);
		$this->dbalert->group_by("gps_time");
		$this->dbalert->order_by("gps_time", "DESC");
		$this->dbalert->limit("1");
		$qalert      = $this->dbalert->get($table_alert);
		$rowsalert   = $qalert->result_array();

		return $rowsalert;
	}

	function get_gpsalert($device,$host,$type){
		$this->dbalert = $this->load->database($this->sess->user_dblive, true);
		$datefrom  = date("Y-m-d")." 00:00:00";
		$dateuntil = date("Y-m-d")." 23:59:00";

		$table_alert = "webtracking_gps_alert";
		$this->dbalert->order_by("gps_time", "desc");
		$this->dbalert->where("gps_name", $device);
		$this->dbalert->where("gps_host", $host);
		$this->dbalert->where("gps_speed_status", 1);
		// $this->dbalert->where("gps_notif", 0);
		// $this->dbalert->where("gps_view", 0);
		$this->dbalert->where("gps_speed >= 10.7");
		$this->dbalert->where("gps_speed_limit > 0");
		$this->dbalert->where("gps_time >=", $datefrom);
		$this->dbalert->where("gps_time <=", $dateuntil);
		$qalert      = $this->dbalert->get($table_alert);
		$rowsalert   = $qalert->result_array();

		return $rowsalert;
	}

	function get_gpsalert2($device, $sdate, $enddate, $jalur, $geofencename){
		// echo "<pre>";
		// var_dump($sdate.' || '.$enddate);die();
		// echo "<pre>";
		$this->dbalert = $this->load->database($this->sess->user_dblive, true);
		$table_alert   = "webtracking_gps_alert";

		if ($device != "All") {
			$ex_device = explode("@", $device);
			$device2   = $ex_device[0];
			$host      = $ex_device[1];
			$this->dbalert->where("gps_name", $device2);
			$this->dbalert->where("gps_host", $host);
		}

		$this->dbalert->order_by("gps_time", "asc");
		if ($jalur != "All") {
			$this->dbalert->where("gps_last_road_type", $jalur);
		}

		if ($geofencename != "All") {
			$this->dbalert->where("gps_geofence", $geofencename);
		}
		$this->dbalert->where("gps_speed_status", 1);
		$this->dbalert->where("gps_speed >= 10.7");
		$this->dbalert->where("gps_speed_limit > 0");
		$this->dbalert->where("gps_time >= ", $sdate);
		$this->dbalert->where("gps_time <= ", $enddate);
		$qalert      = $this->dbalert->get($table_alert);
		$rowsalert   = $qalert->result_array();

		// echo "<pre>";
		// var_dump($rowsalert);die();
		// echo "<pre>";

		return $rowsalert;
	}

	function getPosition($longitude, $ew, $latitude, $ns){
		$gps_longitude_real = getLongitude($longitude, $ew);
		$gps_latitude_real = getLatitude($latitude, $ns);

		$gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
		$gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");

		$georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);

		return $georeverse;
	}

	function getPosition_other($longitude, $latitude)
	{
		$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);
		return $georeverse;
	}

	function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_user) {

		$this->db = $this->load->database("default", true);
		$lng = getLongitude($longitude, $ew);
		$lat = getLatitude($latitude, $ns);

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
							AND (geofence_user = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_user);

		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{
			$row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }

		}else
        {
            return false;
        }

	}

	function getGeofence_location_other($longitude, $latitude, $vehicle_user) {

		$this->db = $this->load->database("default", true);
		$lng = $longitude;
		$lat = $latitude;

		$sql = sprintf("
					SELECT 	*
					FROM 	%sgeofence
					WHERE 	TRUE
							AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
							AND (geofence_user = '%s' )
                            AND (geofence_status = 1)
					LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_user);
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
			$row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }

		}else
        {
            return false;
        }

	}

	function array_sort($array, $on, $order=SORT_ASC){

		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}

	function update_data($table, $data){
		$this->dbalert = $this->load->database($this->sess->user_dblive, true);

		$this->dbalert->where("gps_view", "0");
    return $this->dbalert->update($table, $data);
	}

	function forcounttotal($userid){
		$user_dblive    = $this->sess->user_dblive;
		$datafromdblive = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
		$mastervehicle  = $this->m_poipoolmaster->getmastervehicle();

		// echo "<pre>";
		// var_dump($datafromdblive);die();
		// echo "<pre>";

		$datafix    = array();
		$datafixbgt = array();
		$deviceidygtidakada = array();

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			// $device = $datafromdblive[$i]['gps_name'].'@'.$datafromdblive[$i]['gps_host'];
			$device = explode("@", $mastervehicle[$i]['vehicle_device']);
			$device0 = $device[0];
			$device1 = $device[1];

			// print_r("devicenya : ".$device0);
			// $getdata[] = $this->m_poipoolmaster->getmastervehiclebydevid($device);
			$getdata[]                 = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
			// $laspositionfromgpsmodel[] = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");
				if (sizeof($getdata[$i]) > 0) {
					// $jsonnya[] = json_decode($getdata[$i][0]['vehicle_autocheck'], true);
							array_push($datafix, array(
  						 "is_update" 						  => "yes",
							 "vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
		 					 "vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
		 					 "vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
		 					 "vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
		 					 "vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
		 					 "vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
		 					 "vehicle_card_no"        => $mastervehicle[$i]['vehicle_card_no'],
		 					 "vehicle_operator"       => $mastervehicle[$i]['vehicle_operator'],
		 					 "vehicle_active_date"    => $mastervehicle[$i]['vehicle_active_date'],
		 					 "vehicle_active_date1"   => $mastervehicle[$i]['vehicle_active_date1'],
		 					 "vehicle_status"         => $mastervehicle[$i]['vehicle_status'],
		 					 "vehicle_image"          => $mastervehicle[$i]['vehicle_image'],
		 					 "vehicle_created_date"   => $mastervehicle[$i]['vehicle_created_date'],
		 					 "vehicle_type"           => $mastervehicle[$i]['vehicle_type'],
		 					 "vehicle_autorefill"     => $mastervehicle[$i]['vehicle_autorefill'],
		 					 "vehicle_maxspeed"       => $mastervehicle[$i]['vehicle_maxspeed'],
		 					 "vehicle_maxparking"     => $mastervehicle[$i]['vehicle_maxparking'],
		 					 "vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
		 					 "vehicle_subcompany"     => $mastervehicle[$i]['vehicle_subcompany'],
		 					 "vehicle_group"          => $mastervehicle[$i]['vehicle_group'],
		 					 "vehicle_subgroup"       => $mastervehicle[$i]['vehicle_subgroup'],
		 					 "vehicle_odometer"       => $mastervehicle[$i]['vehicle_odometer'],
		 					 "vehicle_payment_type"   => $mastervehicle[$i]['vehicle_payment_type'],
		 					 "vehicle_payment_amount" => $mastervehicle[$i]['vehicle_payment_amount'],
		 					 "vehicle_fuel_capacity"  => $mastervehicle[$i]['vehicle_fuel_capacity'],
		 					 // "vehicle_info"           => $result[$i]['vehicle_info'],
		 					 "vehicle_sales"          => $mastervehicle[$i]['vehicle_sales'],
		 					 "vehicle_teknisi_id"     => $mastervehicle[$i]['vehicle_teknisi_id'],
		 					 "vehicle_tanggal_pasang" => $mastervehicle[$i]['vehicle_tanggal_pasang'],
		 					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[$i]['vehicle_imei']),
		 					 "vehicle_dbhistory"      => $mastervehicle[$i]['vehicle_dbhistory'],
		 					 "vehicle_dbhistory_name" => $mastervehicle[$i]['vehicle_dbhistory_name'],
		 					 "vehicle_dbname_live"    => $mastervehicle[$i]['vehicle_dbname_live'],
		 					 "vehicle_isred"          => $mastervehicle[$i]['vehicle_isred'],
		 					 "vehicle_modem"          => $mastervehicle[$i]['vehicle_modem'],
		 					 "vehicle_card_no_status" => $mastervehicle[$i]['vehicle_card_no_status'],
							 // "position"  	  				  => $laspositionfromgpsmodel[$i]->georeverse->display_name,
							 "vehicle_autocheck" 	 		=> $getdata[$i][0]['vehicle_autocheck']
							));
				}else {
					// $jsonnya2[$i] = json_decode($mastervehicle[$i]['vehicle_autocheck'], true);
					array_push($deviceidygtidakada, array(
						"is_update" 						 => "no",
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"vehicle_card_no"        => $mastervehicle[$i]['vehicle_card_no'],
						"vehicle_operator"       => $mastervehicle[$i]['vehicle_operator'],
						"vehicle_active_date"    => $mastervehicle[$i]['vehicle_active_date'],
						"vehicle_active_date1"   => $mastervehicle[$i]['vehicle_active_date1'],
						"vehicle_status"         => $mastervehicle[$i]['vehicle_status'],
						"vehicle_image"          => $mastervehicle[$i]['vehicle_image'],
						"vehicle_created_date"   => $mastervehicle[$i]['vehicle_created_date'],
						"vehicle_type"           => $mastervehicle[$i]['vehicle_type'],
						"vehicle_autorefill"     => $mastervehicle[$i]['vehicle_autorefill'],
						"vehicle_maxspeed"       => $mastervehicle[$i]['vehicle_maxspeed'],
						"vehicle_maxparking"     => $mastervehicle[$i]['vehicle_maxparking'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_subcompany"     => $mastervehicle[$i]['vehicle_subcompany'],
						"vehicle_group"          => $mastervehicle[$i]['vehicle_group'],
						"vehicle_subgroup"       => $mastervehicle[$i]['vehicle_subgroup'],
						"vehicle_odometer"       => $mastervehicle[$i]['vehicle_odometer'],
						"vehicle_payment_type"   => $mastervehicle[$i]['vehicle_payment_type'],
						"vehicle_payment_amount" => $mastervehicle[$i]['vehicle_payment_amount'],
						"vehicle_fuel_capacity"  => $mastervehicle[$i]['vehicle_fuel_capacity'],
						// "vehicle_info"           => $result[$i]['vehicle_info'],
						"vehicle_sales"          => $mastervehicle[$i]['vehicle_sales'],
						"vehicle_teknisi_id"     => $mastervehicle[$i]['vehicle_teknisi_id'],
						"vehicle_tanggal_pasang" => $mastervehicle[$i]['vehicle_tanggal_pasang'],
						"vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[$i]['vehicle_imei']),
						"vehicle_dbhistory"      => $mastervehicle[$i]['vehicle_dbhistory'],
						"vehicle_dbhistory_name" => $mastervehicle[$i]['vehicle_dbhistory_name'],
						"vehicle_dbname_live"    => $mastervehicle[$i]['vehicle_dbname_live'],
						"vehicle_isred"          => $mastervehicle[$i]['vehicle_isred'],
						"vehicle_modem"          => $mastervehicle[$i]['vehicle_modem'],
						"vehicle_card_no_status" => $mastervehicle[$i]['vehicle_card_no_status'],
						// "position"  	  				 => $laspositionfromgpsmodel[$i]->georeverse->display_name,
						"vehicle_autocheck" 	 	 => $mastervehicle[$i]['vehicle_autocheck']
					));
				}
		}

		$mastervehiclesize      = sizeof($mastervehicle);
		$datafixsize            = sizeof($datafix);
		$deviceidygtidakadasize = sizeof($deviceidygtidakada);

		return $mastervehiclesize."|".$datafixsize."|".$deviceidygtidakadasize;
	}

	function vehicleactive(){
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$user_id_fix     = $this->sess->user_id;
		// ACTIVE DEVICE
		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_company", $user_company);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->where("vehicle_status <>", 3);
		$q            = $this->db->get("vehicle");
		return $q->result_array();
	}

	function vehicleexpired(){
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$user_id_fix 		 = $this->sess->user_id;

		// EXPIRED DEVICE
		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_company", $user_company);
		}else{
			$this->db->where("vehicle_no",99999);
		}
		$datenow       = date("Ymd");
		$this->db->where("vehicle_active_date2 <", $datenow);
		$q2            = $this->db->get("vehicle");
		return $q2->result_array();
	}

	function totaldevice(){
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_parent     = $this->sess->user_parent;
		$user_id_role    = $this->sess->user_id_role;
		$user_id_fix 		 = $this->sess->user_id;

		// TOTAL DEVICE
		if($user_id_role == 0){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_id_role == 1){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 2){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 3){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 4){
			$this->db->where("vehicle_user_id", $user_parent);
		}else if($user_id_role == 5){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_id_role == 6){
			$this->db->where("vehicle_company", $user_company);
		}else{
			$this->db->where("vehicle_no",99999);
		}
		$q3             = $this->db->get("vehicle");
		return $q3->result_array();
	}

	function getvehicle_byowneringeofence(){
		$user_id =  $this->sess->user_id; //tag
		// echo "<pre>";
		// var_dump($user_id);die();
		// echo "<pre>";
		$this->db->distinct();
		$this->db->where("geofence_name <>", '');
		$this->db->where("geofence_user", $user_id);
		$q = $this->db->get("geofence");

		return $rowgeofencenames = $q->result();
	}

	function searchforreport($webtracking_gps_alert, $vehicle, $sdate, $enddate, $jalur, $geofencename){
		$this->db      = $this->load->database("default", true);

		$data_alert    = $this->get_gpsalert2($vehicle, $sdate, $enddate, $jalur, $geofencename);
		$dataalert_fix = array();

			if (sizeof($data_alert) > 0){
				for ($i=0; $i < sizeof($data_alert); $i++){
					$getmastervehicle[] = $this->getmastervehiclebydevid($data_alert[$i]['gps_name'].'@'.$data_alert[$i]['gps_host']);

					array_push($dataalert_fix, array(
					 "vehicle_no"             => $getmastervehicle[$i][0]['vehicle_no'],
					 "vehicle_name"           => $getmastervehicle[$i][0]['vehicle_name'],
					 "vehicle_device"         => $getmastervehicle[$i][0]['vehicle_device'],
					 "vehicle_lat"            => $data_alert[$i]['gps_latitude_real'],
					 "vehicle_lng"            => $data_alert[$i]['gps_longitude_real'],
					 "gps_alert"              => $data_alert[$i]['gps_alert'],
					 "gps_status"             => $data_alert[$i]['gps_status'],
					 "gps_speed"              => $data_alert[$i]['gps_speed'],
					 "gps_geofence"           => $data_alert[$i]['gps_geofence'],
					 "gps_speed_limit"        => $data_alert[$i]['gps_speed_limit'],
					 "gps_speed_status"       => $data_alert[$i]['gps_speed_status'],
					 "gps_last_road_type"     => $data_alert[$i]['gps_last_road_type'],
					 "vehicle_alert_datetime" => date("d-m-Y H:i:s", strtotime($data_alert[$i]['gps_time']))
					));
				}
			}

			// echo "<pre>";
			// var_dump($data_alert);die();
			// echo "<pre>";

		return $dataalert_fix;
	}

	function getmastervehiclebydevid($device_id){
		// echo "<pre>";
		// var_dump($device_id);die();
		// echo "<pre>";
		//GET DATA FROM DB
		$this->db     = $this->load->database("default", true);
		$this->db->select("*");
    $this->db->where("vehicle_device", $device_id);
		$q       = $this->db->get("vehicle");
		return $q->result_array();
  }

	function getallgeofence($dblive){
		$this->db->dblive = $this->load->database($dblive, true);
		$this->db->dblive->select("geofence_id, geofence_vehicle, geofence_coordinate,
															 geofence_user, geofence_created, geofence_name, geofence_type, geofence_speed, geofence_speed_muatan, geofence_status");
		$this->db->dblive->where("geofence_status", 1);
		$this->db->dblive->where("geofence_type", "road");
		$this->db->dblive->order_by("geofence_name", "asc");
		$q                  = $this->db->dblive->get("webtracking_geofence");
		return $result      = $q->result_array();
	}

	function getyesterdaydata($table, $startdate, $enddate){
		$sql = "SELECT alarm_report_type as report_type, alarm_report_name as report_name, count(alarm_report_name) as jumlah FROM $table where alarm_report_start_time >= '$startdate' and alarm_report_start_time <=  '$enddate' and alarm_report_media=0 group by alarm_report_type";
		$this->dbalarm = $this->load->database("webtracking_kalimantan", true);
		$q        = $this->dbalarm->query($sql);
		return  $q->result_array();
	}

	function checkbranchname($branchname){
		$this->db = $this->load->database('default', true);
		$this->db->select("*");
		$this->db->where("company_name", $branchname);
		$q        = $this->db->get("company");
		return $q->result_array();
	}

	function insertData($table, $data){
		$this->db = $this->load->database('default', true);
		return $this->db->insert($table, $data);
	}

	function updateData($table, $where, $wherenya, $data){
		$this->db = $this->load->database('default', true);
		$this->db->where($where, $wherenya);
		return $this->db->update($table, $data);
	}

	function getuseridrole(){
		$this->db = $this->load->database('default', true);
		$this->db->select("*");
		$this->db->order_by("privilege_id", "ASC");
		$q        = $this->db->get("masterdata_privilege");
		return $q->result_array();
	}

	function getallpjo(){
		$this->db = $this->load->database('default', true);
		$this->db->select("*");
		$this->db->where("company_created_by", 4408);
		$this->db->order_by("company_name", "ASC");
		$q        = $this->db->get("company");
		return $q->result_array();
	}

}
