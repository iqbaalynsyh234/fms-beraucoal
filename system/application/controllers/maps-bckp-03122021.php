<?php
include "base.php";
setlocale(LC_ALL, 'IND');

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
		$this->load->model("log_model");
		$this->load->model("m_securityevidence");
		$this->load->model("gpsmodel");
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
			$user_id = $this->sess->user_id; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$user_id_fix        = $user_id;

		$companyid          = $this->uri->segment(3);

		$user_dblive        = $this->sess->user_dblive;
		$datafromdblive     = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
		$mastervehicle      = $this->m_poipoolmaster->getmastervehiclebyarea($companyid);

		//
		$datafix            = array();
		$datafixbgt         = array();
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
							 "vehicle_fuel_volt" 		 	=> $mastervehicle[$i]['vehicle_fuel_volt'],
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
						"vehicle_fuel_volt" 		 => $mastervehicle[$i]['vehicle_fuel_volt'],
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
				"vehicle_fuel_volt" 		 => $datafixbgt[$loop]['vehicle_fuel_volt'],
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

		$company                  = $this->dashboardmodel->getcompany_byowner();
		$datavehicleandcompany    = array();
		$datavehicleandcompanyfix = array();

			for ($d=0; $d < sizeof($company); $d++) {
				$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
				// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
				$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
				$totalengine       = explode("|", $totaldata);
					array_push($datavehicleandcompany, array(
						"company_id"   => $company[$d]->company_id,
						"company_name" => $company[$d]->company_name,
						"totalmobil"   => $totalengine[2],
						"vehicle"      => $vehicledata[$d]
					));
			}

			// echo "<pre>";
			// var_dump($vehicledata);die();
			// echo "<pre>";

		$this->params['company']        = $company;
		$this->params['companyid']      = $companyid;
		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicle']      = $datavehicleandcompany;
		$this->params['vehicledata']  = $throwdatatoview;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
		$totalmobilnya                = sizeof($getvehicle_byowner);
		if ($totalmobilnya == 0) {
	    $this->params['name']         = "0";
	    $this->params['host']         = "0";
	  }else {
	    $arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
	    $this->params['name']         = $arr[0];
	    $this->params['host']         = $arr[1];
	  }

		$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
	  $this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
	  $this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();

		// echo "<pre>";
		// var_dump($this->params['vehicledata']);die();
		// echo "<pre>";

		// KONDISI BUAT MAPS
		if ($this->config->item('app_powerblock') == 1) {
			// print_r("disini 1");exit();
			$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('powerblock/dashboard/sidebar_maps', $this->params, true);
			$this->params["content"]        = $this->load->view('powerblock/dashboard/maps/maps_view', $this->params, true);
			$this->load->view("dashboard/template_dashboard_report", $this->params);
		}elseif ($this->config->item('app_default') == 1) {
			// print_r("disini 2");exit();
			if ($user_id == "389") {
				$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
				$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps', $this->params, true);
				$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('farrasindo/dashboard/maps/maps_view', $this->params, true);
				$this->load->view("dashboard/template_dashboard_report", $this->params);
			}else {
				$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
				$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps', $this->params, true);
				$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('dashboard/trackers/maps_view', $this->params, true);
				$this->load->view("dashboard/template_dashboard_report", $this->params);
			}
		}else {
			// print_r("disini 3");exit();
			$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps', $this->params, true);
			$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('dashboard/trackers/maps_view', $this->params, true);
			$this->load->view("dashboard/template_dashboard_report", $this->params);
		}
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
			$user_id = $this->sess->user_id; //tag
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

		$vehicle_id      = $device;
		$this->db        = $this->load->database("default", true);
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive     = $this->sess->user_dblive;

		if($this->sess->user_id == "1445"){
			$user_id = $this->sess->user_id; //tag
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
		$q               = $this->db->get("vehicle");
		$mastervehicle   = $q->result_array();
		$devicefordblive = explode("@", $mastervehicle[0]['vehicle_device']);
		$getdatalastinfo = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $devicefordblive[0]);
		$lastinfofix 	   = $this->gpsmodel->GetLastInfo($devicefordblive[0], $devicefordblive[1], true, false, 0, "");

		// echo "<pre>";
		// var_dump($getdatalastinfo);die();
		// echo "<pre>";

		$datafix = array();
		$vehiclemv03 = $mastervehicle[0]['vehicle_mv03'];
		if ($vehiclemv03 != "0000") {
			$url       = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=".$vehiclemv03."&jsession=";
			$username  = "IND.LacakMobil";
			$password  = "000000";
			// $url       = "http://47.91.108.9:8080/808gps/open/player/RealPlayVideo.html?account=".$username."&password=".$password."&PlateNum=".$devicefix."&lang=en";

			$getthissession  = $this->m_securityevidence->getsession();
			$urlfix          = $url.$getthissession[0]['sess_value'];
			// echo "<pre>";
			// var_dump("!=");die();
			// echo "<pre>";

			// GET LOGIN DENGAN SESSION LAMA
			$loginlama       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
				if ($loginlama) {
					$loginlamadecode = json_decode($loginlama);
					if (!$loginlamadecode) {
						if ($loginlamadecode->message == "Session does not exist!") {
							$loginbaru       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
							$loginbarudecode = json_decode($loginbaru);
							$fixsession      = $loginbarudecode->jsession;
						}
					}else {
						$fixsession      = $getthissession[0]['sess_value'];
					}
				}

				// GET DEVICE STATUS START
				$urlcekdevicestatus = "http://47.91.108.9:8080/StandardApiAction_getDeviceOlStatus.action?jsession=".$fixsession."&devIdno=".$vehiclemv03;
				$cekstatus          = file_get_contents($urlcekdevicestatus);
				$loginbarudecode    = json_decode($cekstatus);
				$statusfixnya       = $loginbarudecode->onlines[0]->online;
				$devicestatusfixnya = $statusfixnya;
				// echo "<pre>";
				// var_dump($row->$loginbarudecode);die();
				// echo "<pre>";
		}else {
			$devicestatusfixnya = "";
		}

		if (sizeof($getdatalastinfo) > 0) {
			$jsonnya[0] = json_decode($getdatalastinfo[0]['vehicle_autocheck']);
				if (isset($jsonnya[0]->auto_last_snap)) {
					$snap     = $jsonnya[0]->auto_last_snap;
					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
				}else {
					$snap     = "";
					$snaptime = "";
				}

				if (isset($jsonnya[0]->auto_last_road)) {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
				}else {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[0]->auto_last_ritase)) {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
				}else {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				array_push($datafix, array(
					 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
					 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
					 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
					 "vehicle_mv03"           => $mastervehicle[0]['vehicle_mv03'],
					 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
					 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
					 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
					 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
					 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
					 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
					 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
					 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
					 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
					 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
					 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
					 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
					 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
					 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
					 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
					 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
					 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
					 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
					 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
					 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
					 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
					 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
					 "vehicle_fuel_volt" 			=> $mastervehicle[0]['vehicle_fuel_volt'],
					 // "vehicle_info"           => $result[$i]['vehicle_info'],
					 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
					 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
					 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
					 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
					 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
					 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
					 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
					 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
					 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
					 "devicestatusfixnya" 		=> $devicestatusfixnya,
					 "auto_last_road"         => $autolastroad,
					 "auto_last_ritase"       => $autolastritase,
					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
					 "auto_last_update"       => $lastinfofix->gps_date_fmt. " ". $lastinfofix->gps_time_fmt,
					 "auto_last_check"        => $jsonnya[0]->auto_last_check,
					 "auto_last_snap"         => $snap,
					 "auto_last_snap_time"    => $snaptime,
					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $lastinfofix->georeverse->display_name),
					 "auto_last_lat"          => substr($lastinfofix->gps_latitude_real_fmt, 0, 10),
					 "auto_last_long"         => substr($lastinfofix->gps_longitude_real_fmt, 0, 10),
					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
				));
		}else {
			$jsonnya[0] = json_decode($mastervehicle[0]['vehicle_autocheck']);
				if (isset($jsonnya[0]->auto_last_snap)) {
					$snap     = $jsonnya[0]->auto_last_snap;
					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
				}else {
					$snap     = "";
					$snaptime = "";
				}

				if (isset($jsonnya[0]->auto_last_road)) {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
				}else {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[0]->auto_last_ritase)) {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
				}else {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				array_push($datafix, array(
					 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
					 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
					 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
					 "vehicle_mv03"           => $mastervehicle[0]['vehicle_mv03'],
					 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
					 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
					 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
					 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
					 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
					 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
					 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
					 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
					 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
					 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
					 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
					 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
					 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
					 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
					 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
					 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
					 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
					 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
					 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
					 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
					 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
					 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
					 "vehicle_fuel_volt" 			=> $mastervehicle[0]['vehicle_fuel_volt'],
					 // "vehicle_info"           => $result[$i]['vehicle_info'],
					 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
					 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
					 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
					 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
					 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
					 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
					 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
					 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
					 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
					 "devicestatusfixnya" 		=> $devicestatusfixnya,
					 "auto_last_road"         => $autolastroad,
					 "auto_last_ritase"       => $autolastritase,
					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
					 "auto_last_update"       => $jsonnya[0]->auto_last_update,
					 "auto_last_check"        => $jsonnya[0]->auto_last_check,
					 "auto_last_snap"         => $snap,
					 "auto_last_snap_time"    => $snaptime,
					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_position),
					 "auto_last_lat"          => substr($jsonnya[0]->auto_last_lat, 0, 10),
					 "auto_last_long"         => substr($jsonnya[0]->auto_last_long, 0, 10),
					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
				));
		}

		$this->params['vehicledatafix'] = $datafix;
		$this->params['vehicletotal']   = $mastervehicle;

		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);
		$v_device                       = $datafix[0]['vehicle_device'];
		$this->params['dest']           = $this->m_poipoolmaster->getdestinationbyid("webtracking_destination_master", "dest_vehicle_device", $v_device);

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";
		$this->params['url_code_view']  = 1;
		$this->params['code_view_menu'] = "monitor";

		// KALIMANTAN START
		$explode      = explode("@", $datafix[0]['vehicle_device']);
		$devicefix    = $explode[0];
		$vehiclemv03  = $datafix[0]['vehicle_mv03'];
		// $devicefix = $datafix[0]['vehicle_no'];
		$url          = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=".$vehiclemv03."&jsession=";
		$username     = "IND.LacakMobil";
		$password     = "000000";
		// $url       = "http://47.91.108.9:8080/808gps/open/player/RealPlayVideo.html?account=".$username."&password=".$password."&PlateNum=".$devicefix."&lang=en";

		$getthissession  = $this->m_securityevidence->getsession();
		$urlfix          = $url.$getthissession[0]['sess_value'];

		// GET LOGIN DENGAN SESSION LAMA
		if ($vehiclemv03 != "0000") {
			$loginlama       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
				if ($loginlama) {
					$loginlamadecode = json_decode($loginlama);
					if (!$loginlamadecode) {
						if ($loginlamadecode->message == "Session does not exist!") {
							$loginbaru       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
							$loginbarudecode = json_decode($loginbaru);
							$urlfix          = $url.$loginbarudecode->jsession;
						}
					}else {
						$urlfix          = $url.$getthissession[0]['sess_value'];
					}
				}
				$this->params['urlfix']  = $urlfix;
		}else {
			$this->params['urlfix']  = "";
		}

			// echo "<pre>";
			// var_dump($urlfix);die();
			// echo "<pre>";

			$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
			$datastatus                     = explode("|", $rstatus);
			$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
			$this->params['total_vehicle']  = $datastatus[3];
			$this->params['total_offline']  = $datastatus[2];

			$companyid                = $this->sess->user_company;
			$company                  = $this->dashboardmodel->getcompany_byowner();
			$datavehicleandcompany    = array();
			$datavehicleandcompanyfix = array();

				for ($d=0; $d < sizeof($company); $d++) {
					$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
					// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
					$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
					$totalengine       = explode("|", $totaldata);
						array_push($datavehicleandcompany, array(
							"company_id"   => $company[$d]->company_id,
							"company_name" => $company[$d]->company_name,
							"totalmobil"   => $totalengine[2],
							"vehicle"      => $vehicledata[$d]
						));
				}

			$this->params['company']   = $company;
			$this->params['companyid'] = $companyid;
			$this->params['vehicle']   = $datavehicleandcompany;


			// echo "<pre>";
			// var_dump($this->params['vehicle']);die();
			// echo "<pre>";

		// KALIMANTAN END

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps_kalimantan', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/trackers/maps_view_onevehicle_kalimantan', $this->params, true);
		$this->load->view("dashboard/template_dashboard_kalimantan", $this->params);
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
			$user_id = $this->sess->user_id; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$user_id_fix = $user_id;

		// $companyid 			 = $this->uri->segment(3);


		$companyid                      = $this->sess->user_company;
		$user_dblive                    = $this->sess->user_dblive;
		$datafromdblive                 = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
		$mastervehicle                  = $this->m_poipoolmaster->getmastervehicle();
		//
		$datafix            = array();
		$datafixbgt         = array();
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
							 "vehicle_fuel_volt" 			=> $mastervehicle[$i]['vehicle_fuel_volt'],
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
							 "vehicle_mv03" 					=> $mastervehicle[$i]['vehicle_mv03'],
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
						"vehicle_fuel_volt" 		 => $mastervehicle[$i]['vehicle_fuel_volt'],
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
						"vehicle_mv03" 					 => $mastervehicle[$i]['vehicle_mv03'],
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
				"vehicle_fuel_volt" 		 => $datafixbgt[$loop]['vehicle_fuel_volt'],
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
				"vehicle_mv03" 					 => $datafixbgt[$loop]['vehicle_mv03'],
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

		$company                  = $this->dashboardmodel->getcompany_byowner();
		$datavehicleandcompany    = array();
		$datavehicleandcompanyfix = array();

			for ($d=0; $d < sizeof($company); $d++) {
				$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
				// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
				$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
				$totalengine       = explode("|", $totaldata);
					array_push($datavehicleandcompany, array(
						"company_id"   => $company[$d]->company_id,
						"company_name" => $company[$d]->company_name,
						"totalmobil"   => $totalengine[2],
						"vehicle"      => $vehicledata[$d]
					));
			}

			// echo "<pre>";
			// var_dump($vehicledata);die();
			// echo "<pre>";

		$this->params['company']        = $company;
		$this->params['companyid']      = $companyid;
		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicle']      = $datavehicleandcompany;
		$this->params['vehicledata']  = $throwdatatoview;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
		$totalmobilnya                = sizeof($getvehicle_byowner);
		if ($totalmobilnya == 0) {
	    $this->params['name']         = "0";
	    $this->params['host']         = "0";
	  }else {
	    $arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
	    $this->params['name']         = $arr[0];
	    $this->params['host']         = $arr[1];
	  }

		$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
	  $this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
	  $this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();

		// echo "<pre>";
		// var_dump($this->params['vehicledata']);die();
		// echo "<pre>";

		// KONDISI BUAT MAPS
		if ($this->config->item('app_powerblock') == 1) {
			// print_r("disini 1");exit();
			$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('powerblock/dashboard/sidebar_maps', $this->params, true);
			$this->params["content"]        = $this->load->view('powerblock/dashboard/maps/maps_view', $this->params, true);
			$this->load->view("dashboard/template_dashboard_report", $this->params);
		}elseif ($this->config->item('app_default') == 1) {
			// print_r("disini 2");exit();
			if ($user_id == "389") {
				$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
				$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps', $this->params, true);
				$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('farrasindo/dashboard/maps/maps_view', $this->params, true);
				$this->load->view("dashboard/template_dashboard_report", $this->params);
			}else {
				$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
				$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps_kalimantan', $this->params, true);
				$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('dashboard/trackers/maps_view_kalimantan', $this->params, true);
				$this->load->view("dashboard/template_dashboard_kalimantan", $this->params);
			}
		}else {
			// print_r("disini 3");exit();
			$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps_kalimantan', $this->params, true);
			$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('dashboard/trackers/maps_view_kalimantan', $this->params, true);
			$this->load->view("dashboard/template_dashboard_kalimantan", $this->params);
		}
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
			$user_id = $this->sess->user_id; //tag
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
					 "vehicle_fuel_volt" 		  => $result[$i]['vehicle_fuel_volt'],
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
					 "vehicle_mv03"  					=> $result[$i]['vehicle_mv03'],
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
		$this->params['url_code_view'] = "1";
		$this->params['code_view_menu'] = "monitor";

		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/trackers/maps_view_offline', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function getdetailbydevid_0(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		if($this->sess->user_id == "1445"){
      $user_id = $this->sess->user_id; //tag
    }else{
      $user_id = $this->sess->user_id;
    }

		$user_dblive     = $this->sess->user_dblive;
		$device_id       = $_POST['device_id'];

		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	   = $this->sess->user_dblive;
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
    $this->db->where("vehicle_id", $device_id);
		$this->db->where("vehicle_gotohistory", 0);
		$this->db->where("vehicle_autocheck is not NULL");
		$q       = $this->db->get("vehicle");
		$result  = $q->result_array();

		$vehiclemv03 = $result[0]['vehicle_mv03'];
		if ($vehiclemv03 != "0000" || $vehiclemv03 != "69969039633231@TK510") {
			$url       = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=".$vehiclemv03."&jsession=";
			$username  = "IND.LacakMobil";
			$password  = "000000";
			// $url       = "http://47.91.108.9:8080/808gps/open/player/RealPlayVideo.html?account=".$username."&password=".$password."&PlateNum=".$devicefix."&lang=en";

			$getthissession  = $this->m_securityevidence->getsession();
			$urlfix          = $url.$getthissession[0]['sess_value'];
			// echo "<pre>";
			// var_dump($result);die();
			// echo "<pre>";

			// GET LOGIN DENGAN SESSION LAMA
			$loginlama       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
				if ($loginlama) {
					$loginlamadecode = json_decode($loginlama);
					if (!$loginlamadecode) {
						if ($loginlamadecode->message == "Session does not exist!") {
							$loginbaru       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
							$loginbarudecode = json_decode($loginbaru);
							$fixsession      = $loginbarudecode->jsession;
						}
					}else {
						$fixsession      = $getthissession[0]['sess_value'];
					}
				}

				// GET DEVICE STATUS START
				$urlcekdevicestatus = "http://47.91.108.9:8080/StandardApiAction_getDeviceOlStatus.action?jsession=".$fixsession."&devIdno=".$vehiclemv03;
				$cekstatus          = file_get_contents($urlcekdevicestatus);
				$loginbarudecode    = json_decode($cekstatus);
				if ($loginbarudecode->result == 0) {
					$devicestatusfixnya = "";
				}else {
					$statusfixnya       = $loginbarudecode->onlines[0]->online;
					$devicestatusfixnya = $statusfixnya;
				}

				// echo "<pre>";
				// var_dump($devicestatusfixnya);die();
				// echo "<pre>";
		}else {
			$devicestatusfixnya = "";
		}

		// DRIVER DETAIL START
		$drivername     = $this->getdriver($result[0]['vehicle_id']);
			if ($drivername) {
				$driverexplode  = explode("-", $drivername);
				$iddriver       = $driverexplode[0];
				$drivername     = $driverexplode[1];
				$getdriverimage = $this->getdriverdetail($iddriver);

				if (isset($getdriverimage[0]->driver_image_file_name)) {
					$driverimage = $getdriverimage[0]->driver_image_raw_name.$getdriverimage[0]->driver_image_file_ext;
				}else {
					$driverimage = 0;
				}
			}else {
				$driverimage = 0;
			}


		// echo "<pre>";
		// var_dump($drivername.'-'.$driverimage);die();
		// echo "<pre>";
		// DRIVER DETAIL END

		$device          = explode("@", $result[0]['vehicle_device']);
		$device0         = $device[0];
		$device1         = $device[1];

		$mastervehicle   = $this->m_poipoolmaster->getmastervehiclebydevid($result[0]['vehicle_device']);
		$getdatalastinfo = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
		$lastinfofix 	   = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");

		$datafix = array();
		$deviceidfrommastervehicle = explode("@", $mastervehicle[0]['vehicle_device']);

		if (sizeof($getdatalastinfo) > 0) {
			$jsonnya[0] = json_decode($getdatalastinfo[0]['vehicle_autocheck']);
				if (isset($jsonnya[0]->auto_last_snap)) {
					$snap     = $jsonnya[0]->auto_last_snap;
					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
				}else {
					$snap     = "";
					$snaptime = "";
				}

				if (isset($jsonnya[0]->auto_last_road)) {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
				}else {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[0]->auto_last_ritase)) {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
				}else {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[0]->auto_last_mvd)) {
					$autolastfuel = $jsonnya[0]->auto_last_mvd;
				}else {
					$autolastfuel = "";
				}

				array_push($datafix, array(
					 "drivername"            	=> $drivername,
					 "driverimage"            => $driverimage,
					 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
					 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
					 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
					 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
					 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
					 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
					 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
					 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
					 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
					 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
					 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
					 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
					 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
					 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
					 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
					 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
					 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
					 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
					 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
					 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
					 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
					 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
					 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
					 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
					 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
					 "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
					 // "vehicle_info"           => $result[$i]['vehicle_info'],
					 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
					 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
					 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
					 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
					 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
					 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
					 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
					 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
					 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
					 "vehicle_mv03" 					=> $mastervehicle[0]['vehicle_mv03'],
					 "devicestatusfixnya" 	  => $devicestatusfixnya,
					 "auto_last_road"         => $autolastroad,
					 "auto_last_ritase"       => $autolastritase,
					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
					 "auto_last_fuel"         => $autolastfuel,
					 "auto_last_update"       => $lastinfofix->gps_date_fmt. " ". $lastinfofix->gps_time_fmt,
					 "auto_last_check"        => $jsonnya[0]->auto_last_check,
					 "auto_last_snap"         => $snap,
					 "auto_last_snap_time"    => $snaptime,
					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $lastinfofix->georeverse->display_name),
					 "auto_last_lat"          => substr($lastinfofix->gps_latitude_real_fmt, 0, 10),
					 "auto_last_long"         => substr($lastinfofix->gps_longitude_real_fmt, 0, 10),
					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
				));
		}else {
			$jsonnya[0] = json_decode($mastervehicle[0]['vehicle_autocheck']);
				if (isset($jsonnya[0]->auto_last_snap)) {
					$snap     = $jsonnya[0]->auto_last_snap;
					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
				}else {
					$snap     = "";
					$snaptime = "";
				}

				if (isset($jsonnya[0]->auto_last_road)) {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
				}else {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[0]->auto_last_ritase)) {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
				}else {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[0]->auto_last_mvd)) {
					$autolastfuel = $jsonnya[0]->auto_last_mvd;
				}else {
					$autolastfuel = "";
				}

				array_push($datafix, array(
					 "drivername"            	=> $drivername,
				 	 "driverimage"            => $driverimage,
					 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
					 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
					 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
					 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
					 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
					 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
					 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
					 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
					 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
					 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
					 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
					 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
					 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
					 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
					 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
					 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
					 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
					 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
					 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
					 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
					 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
					 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
					 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
					 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
					 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
					 "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
					 // "vehicle_info"           => $result[$i]['vehicle_info'],
					 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
					 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
					 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
					 "vehicle_mv03" 					=> $mastervehicle[0]['vehicle_mv03'],
					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
					 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
					 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
					 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
					 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
					 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
					 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
					 "devicestatusfixnya" 	  => $devicestatusfixnya,
					 "auto_last_road"         => $autolastroad,
					 "auto_last_ritase"       => $autolastritase,
					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
					 "auto_last_fuel"         => $autolastfuel,
					 "auto_last_update"       => $jsonnya[0]->auto_last_update,
					 "auto_last_check"        => $jsonnya[0]->auto_last_check,
					 "auto_last_snap"         => $snap,
					 "auto_last_snap_time"    => $snaptime,
					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_position),
					 "auto_last_lat"          => substr($jsonnya[0]->auto_last_lat, 0, 10),
					 "auto_last_long"         => substr($jsonnya[0]->auto_last_long, 0, 10),
					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
				));
		}

		// echo "<pre>";
		// var_dump($jsonnya);die();
		// echo "<pre>";
		echo json_encode($datafix);
	}

	function getdetailbydevid(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_dblive     = $this->sess->user_dblive;
		$device_id       = $_POST['device_id'];
		$device          = explode("@", $_POST['device_id']);
		$device0         = $device[0];
		$device1         = $device[1];

		$mastervehicle   = $this->m_poipoolmaster->getmastervehiclebydevid($device_id);
		$getdatalastinfo = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
		$lastinfofix 	   = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");

		$datafix = array();
		$deviceidfrommastervehicle = explode("@", $mastervehicle[0]['vehicle_device']);

		$vehiclemv03 = $mastervehicle[0]['vehicle_mv03'];
		if ($vehiclemv03 != "0000" || $vehiclemv03 != "69969039633231@TK510") {
			$url       = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=".$vehiclemv03."&jsession=";
			$username  = "IND.LacakMobil";
			$password  = "000000";
			// $url       = "http://47.91.108.9:8080/808gps/open/player/RealPlayVideo.html?account=".$username."&password=".$password."&PlateNum=".$devicefix."&lang=en";

			$getthissession  = $this->m_securityevidence->getsession();
			$urlfix          = $url.$getthissession[0]['sess_value'];
			// echo "<pre>";
			// var_dump($result);die();
			// echo "<pre>";

			// GET LOGIN DENGAN SESSION LAMA
			$loginlama       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
				if ($loginlama) {
					$loginlamadecode = json_decode($loginlama);
					if (!$loginlamadecode) {
						if ($loginlamadecode->message == "Session does not exist!") {
							$loginbaru       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
							$loginbarudecode = json_decode($loginbaru);
							$fixsession      = $loginbarudecode->jsession;
						}
					}else {
						$fixsession      = $getthissession[0]['sess_value'];
					}
				}

				// GET DEVICE STATUS START
				$urlcekdevicestatus = "http://47.91.108.9:8080/StandardApiAction_getDeviceOlStatus.action?jsession=".$fixsession."&devIdno=".$vehiclemv03;
				$cekstatus          = file_get_contents($urlcekdevicestatus);
				$loginbarudecode    = json_decode($cekstatus);
				if ($loginbarudecode->result == 0) {
					$devicestatusfixnya = "";

				}else {
					$statusfixnya       = $loginbarudecode->onlines[0]->online;
					$devicestatusfixnya = $statusfixnya;
				}
				// echo "<pre>";
				// var_dump($loginbarudecode);die();
				// echo "<pre>";
		}else {
			$devicestatusfixnya = "";
		}

		// DRIVER DETAIL START
		$drivername     = $this->getdriver($mastervehicle[0]['vehicle_id']);
		if ($drivername) {
			$driverexplode  = explode("-", $drivername);
			$iddriver       = $driverexplode[0];
			$drivername     = $driverexplode[1];
			$getdriverimage = $this->getdriverdetail($iddriver);

			if (isset($getdriverimage[0]->driver_image_file_name)) {
				$driverimage = $getdriverimage[0]->driver_image_raw_name.$getdriverimage[0]->driver_image_file_ext;
			}else {
				$driverimage = 0;
			}
		}else {
			$driverimage = 0;
		}

		// echo "<pre>";
		// var_dump($drivername.'-'.$driverimage);die();
		// echo "<pre>";
		// DRIVER DETAIL END

		if (sizeof($getdatalastinfo) > 0) {
			$jsonnya[0] = json_decode($getdatalastinfo[0]['vehicle_autocheck']);
				if (isset($jsonnya[0]->auto_last_snap)) {
					$snap     = $jsonnya[0]->auto_last_snap;
					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
				}else {
					$snap     = "";
					$snaptime = "";
				}

				if (isset($jsonnya[0]->auto_last_road)) {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
				}else {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[0]->auto_last_ritase)) {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
				}else {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[0]->auto_last_mvd)) {
					$autolastfuel = $jsonnya[0]->auto_last_mvd;
				}else {
					$autolastfuel = "";
				}

				array_push($datafix, array(
					 "drivername"            	=> $drivername,
					 "driverimage"            => $driverimage,
					 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
					 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
					 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
					 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
					 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
					 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
					 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
					 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
					 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
					 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
					 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
					 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
					 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
					 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
					 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
					 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
					 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
					 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
					 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
					 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
					 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
					 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
					 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
					 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
					 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
					 "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
					 // "vehicle_info"           => $result[$i]['vehicle_info'],
					 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
					 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
					 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
					 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
					 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
					 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
					 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
					 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
					 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
					 "devicestatusfixnya" 		=> $devicestatusfixnya,
					 "auto_last_fuel"         => $autolastfuel,
					 "auto_last_road"         => $autolastroad,
					 "auto_last_ritase"       => $autolastritase,
					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
					 "auto_last_update"       => $lastinfofix->gps_date_fmt. " ". $lastinfofix->gps_time_fmt,
					 "auto_last_check"        => $jsonnya[0]->auto_last_check,
					 "auto_last_snap"         => $snap,
					 "auto_last_snap_time"    => $snaptime,
					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $lastinfofix->georeverse->display_name),
					 "auto_last_lat"          => substr($lastinfofix->gps_latitude_real_fmt, 0, 10),
					 "auto_last_long"         => substr($lastinfofix->gps_longitude_real_fmt, 0, 10),
					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
				));
		}else {
			$jsonnya[0] = json_decode($mastervehicle[0]['vehicle_autocheck']);
				if (isset($jsonnya[0]->auto_last_snap)) {
					$snap     = $jsonnya[0]->auto_last_snap;
					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
				}else {
					$snap     = "";
					$snaptime = "";
				}

				if (isset($jsonnya[0]->auto_last_road)) {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
				}else {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[0]->auto_last_ritase)) {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
				}else {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[0]->auto_last_mvd)) {
					$autolastfuel = $jsonnya[0]->auto_last_mvd;
				}else {
					$autolastfuel = "";
				}

				array_push($datafix, array(
					 "drivername"             => $drivername,
				 	 "driverimage"            => $driverimage,
					 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
					 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
					 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
					 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
					 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
					 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
					 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
					 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
					 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
					 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
					 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
					 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
					 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
					 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
					 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
					 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
					 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
					 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
					 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
					 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
					 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
					 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
					 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
					 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
					 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
					 "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
					 // "vehicle_info"           => $result[$i]['vehicle_info'],
					 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
					 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
					 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
					 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
					 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
					 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
					 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
					 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
					 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
					 "devicestatusfixnya" 		=> $devicestatusfixnya,
					 "auto_last_road"         => $autolastroad,
					 "auto_last_ritase"       => $autolastritase,
					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
					 "auto_last_update"       => $jsonnya[0]->auto_last_update,
					 "auto_last_check"        => $jsonnya[0]->auto_last_check,
					 "auto_last_fuel"         => $autolastfuel,
					 "auto_last_snap"         => $snap,
					 "auto_last_snap_time"    => $snaptime,
					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_position),
					 "auto_last_lat"          => substr($jsonnya[0]->auto_last_lat, 0, 10),
					 "auto_last_long"         => substr($jsonnya[0]->auto_last_long, 0, 10),
					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
				));
		}

		// echo "<pre>";
		// var_dump($jsonnya);die();
		// echo "<pre>";
		echo json_encode($datafix);
	}

	function updateallinfo(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['sortby']  = "mobil_id";
		$this->params['orderby'] = "asc";
		$this->params['title']   = "Maps All";
		if($this->sess->user_id == "1445"){
			$user_id = $this->sess->user_id; //tag
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

			// print_r("devicenya : ".$device0);
			// $getdata[] = $this->m_poipoolmaster->getmastervehiclebydevid($device);
			$getdata[] = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
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
							 "vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
						"vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
						"vehicle_autocheck" 	 	 => $mastervehicle[$i]['vehicle_autocheck']
					));
				}
		}

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
				"vehicle_fuel_volt" 		  => $datafixbgt[$loop]['vehicle_fuel_volt'],
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
				"auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_status']),
				"auto_last_update"       => $jsonnya[$loop]['auto_last_update'],
				"auto_last_check"        => $jsonnya[$loop]['auto_last_check'],
				"auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_position']),
				"auto_last_lat"          => substr($jsonnya[$loop]['auto_last_lat'], 0, 10),
				"auto_last_long"         => substr($jsonnya[$loop]['auto_last_long'], 0, 10),
				"auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_engine']),
				"auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_gpsstatus']),
				"auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_speed']),
				"auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_course']),
				"auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_flag'])
			));
		}

		$this->params['vehicle']      = $throwdatatoview;
		$this->params['vehicletotal'] = sizeof($mastervehicle);

		// echo "<pre>";
		// var_dump($throwdatatoview);die();
		// echo "<pre>";
		//get company
		$company                     = $this->dashboardmodel->getcompany_byowner();
		echo json_encode($throwdatatoview);
	}

	function getlastinfonya(){
		$device                    = explode("@", $_POST['device']);
		$device0                   = $device[0];
		$device1                   = $device[1];
		$laspositionfromgpsmodel   = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");
		$throwdatatoview 					 = array();
		// echo "<pre>";
		// var_dump($laspositionfromgpsmodel);die();
		// echo "<pre>";
		for ($loop=0; $loop < sizeof($laspositionfromgpsmodel); $loop++) {
			$jsonnya[$loop] = json_decode($laspositionfromgpsmodel->vehicle_autocheck, true);
				if (isset($jsonnya[$loop]['auto_last_snap'])) {
					$snap     = $jsonnya[$loop]['auto_last_snap'];
					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[$loop]['auto_last_snap_time']));
				}else {
					$snap     = "";
					$snaptime = "";
				}

			array_push($throwdatatoview, array(
				"gps_id"                  => $laspositionfromgpsmodel->gps_id,
				"gps_name"                => $laspositionfromgpsmodel->gps_name,
				"gps_host"                => $laspositionfromgpsmodel->gps_host,
				"gps_type"                => $laspositionfromgpsmodel->gps_type,
				"gps_utc_coord"           => $laspositionfromgpsmodel->gps_utc_coord,
				"gps_status"              => $laspositionfromgpsmodel->gps_status,
				"gps_latitude"            => $laspositionfromgpsmodel->gps_latitude,
				"gps_ns"                  => $laspositionfromgpsmodel->gps_ns,
				"gps_longitude"           => $laspositionfromgpsmodel->gps_longitude,
				"gps_ew"                  => $laspositionfromgpsmodel->gps_ew,
				"gps_speed"               => $laspositionfromgpsmodel->gps_speed,
				"gps_course"              => $laspositionfromgpsmodel->gps_course,
				"gps_utc_date"            => $laspositionfromgpsmodel->gps_utc_date,
				"gps_mvd"                 => $laspositionfromgpsmodel->gps_mvd,
				"gps_mv"                  => $laspositionfromgpsmodel->gps_mv,
				"gps_cs"                  => $laspositionfromgpsmodel->gps_cs,
				"gps_msg_ori"             => $laspositionfromgpsmodel->gps_msg_ori,
				"gps_time"                => $laspositionfromgpsmodel->gps_time,
				"gps_latitude_real"       => $laspositionfromgpsmodel->gps_latitude_real,
				"gps_longitude_real"      => $laspositionfromgpsmodel->gps_longitude_real,
			  "gps_odometer"           => $laspositionfromgpsmodel->gps_odometer,
			  "gps_workhour"           => $laspositionfromgpsmodel->gps_workhour,
			  "gps_timestampori"       => $laspositionfromgpsmodel->gps_timestampori,
			  "gps_timestamp"          => $laspositionfromgpsmodel->gps_timestamp,
			  "gps_date_fmt"           => $laspositionfromgpsmodel->gps_date_fmt,
			  "gps_time_fmt"           => $laspositionfromgpsmodel->gps_time_fmt,
			  "gps_latitude_real_fmt"  => $laspositionfromgpsmodel->gps_latitude_real_fmt,
			  "gps_longitude_real_fmt" => $laspositionfromgpsmodel->gps_longitude_real_fmt,
			  "gps_speed_fmt"          => $laspositionfromgpsmodel->gps_speed_fmt,
			  "css_delay_index"        => $laspositionfromgpsmodel->css_delay_index,
			  "css_delay"              => $laspositionfromgpsmodel->css_delay,
			  "css_delay_time"         => $laspositionfromgpsmodel->css_delay_time,
			  "direction"              => $laspositionfromgpsmodel->direction,
			  "car_icon"               => $laspositionfromgpsmodel->car_icon,
			  "georeverse"             => $laspositionfromgpsmodel->georeverse,
				"auto_last_snap"         => $snap,
				"auto_last_snap_time"    => $snaptime,
				"auto_last_update"       => $jsonnya[$loop]['auto_last_update'],
				"auto_last_check"        => $jsonnya[$loop]['auto_last_check'],
				"auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_position']),
				"auto_last_lat"          => substr($jsonnya[$loop]['auto_last_lat'], 0, 10),
				"auto_last_long"         => substr($jsonnya[$loop]['auto_last_long'], 0, 10),
				"auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_engine']),
				"auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_gpsstatus']),
				"auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_speed']),
				"auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_course']),
				"auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_flag'])
			));
		}

		// echo "<pre>";
		// var_dump($throwdatatoview);die();
		// echo "<pre>";
		echo json_encode($throwdatatoview);
	}

	function getallvehicle(){
		$mastervehicle  = $this->m_poipoolmaster->getmastervehiclefivereport();
		echo json_encode(array("data" => $mastervehicle));
	}

	function forsearchvehicle(){
		$user_dblive     = $this->sess->user_dblive;
		$key             = $_POST['key'];
		// $key             = "b 9442 wcb";
		// $keyfix          = str_replace(" ", "", $key);
		$keyfix          = $key;

		$mastervehicle   = $this->m_poipoolmaster->searchmasterdata("webtracking_vehicle", $keyfix);

		if (sizeof($mastervehicle) < 1) {
			echo json_encode(array("code" => "400"));
		}else {
			// echo "<pre>";
			// var_dump($mastervehicle);die();
			// echo "<pre>";

			$device          = explode("@", $mastervehicle[0]['vehicle_device']);
			$device0         = $device[0];
			$device1         = $device[1];
			$getdatalastinfo = $this->m_poipoolmaster->searchdblivedata("webtracking_gps", $user_dblive, $device0);
			$lastinfofix     = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");

			$vehiclemv03 = $mastervehicle[0]['vehicle_mv03'];
			if ($vehiclemv03 != "0000") {
				$url       = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=".$vehiclemv03."&jsession=";
				$username  = "IND.LacakMobil";
				$password  = "000000";
				// $url       = "http://47.91.108.9:8080/808gps/open/player/RealPlayVideo.html?account=".$username."&password=".$password."&PlateNum=".$devicefix."&lang=en";

				$getthissession  = $this->m_securityevidence->getsession();
				$urlfix          = $url.$getthissession[0]['sess_value'];
				// echo "<pre>";
				// var_dump($result);die();
				// echo "<pre>";

				// GET LOGIN DENGAN SESSION LAMA
				$loginlama       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
					if ($loginlama) {
						$loginlamadecode = json_decode($loginlama);
						if (!$loginlamadecode) {
							if ($loginlamadecode->message == "Session does not exist!") {
								$loginbaru       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
								$loginbarudecode = json_decode($loginbaru);
								$fixsession      = $loginbarudecode->jsession;
							}
						}else {
							$fixsession      = $getthissession[0]['sess_value'];
						}
					}

					// GET DEVICE STATUS START
					$urlcekdevicestatus = "http://47.91.108.9:8080/StandardApiAction_getDeviceOlStatus.action?jsession=".$fixsession."&devIdno=".$vehiclemv03;
					$cekstatus          = file_get_contents($urlcekdevicestatus);
					$loginbarudecode    = json_decode($cekstatus);
					$statusfixnya       = $loginbarudecode->onlines[0]->online;
					$devicestatusfixnya = $statusfixnya;
					// echo "<pre>";
					// var_dump($row->devicestatus);die();
					// echo "<pre>";
			}else {
				$devicestatusfixnya = "";
			}

			// DRIVER DETAIL START
			$drivername     = $this->getdriver($mastervehicle[0]['vehicle_id']);

			if ($drivername) {
				$driverexplode  = explode("-", $drivername);
				$iddriver       = $driverexplode[0];
				$drivername     = $driverexplode[1];
				$getdriverimage = $this->getdriverdetail($iddriver);

				if (isset($getdriverimage[0]->driver_image_file_name)) {
					$driverimage = $getdriverimage[0]->driver_image_raw_name.$getdriverimage[0]->driver_image_file_ext;
				}else {
					$driverimage = 0;
				}
			}else {
				$drivername  = "";
				$driverimage = 0;
			}


			// echo "<pre>";
			// var_dump($drivername);die();
			// echo "<pre>";
			// DRIVER DETAIL END

			$datafix = array();
			if (sizeof($getdatalastinfo) > 0) {
				$jsonnya[0] = json_decode($getdatalastinfo[0]['vehicle_autocheck']);
					if (isset($jsonnya[0]->auto_last_snap)) {
						$snap     = $jsonnya[0]->auto_last_snap;
						$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
					}else {
						$snap     = "";
						$snaptime = "";
					}

					if (isset($jsonnya[0]->auto_last_road)) {
						$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
					}else {
						$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
					}

					if (isset($jsonnya[0]->auto_last_ritase)) {
						$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
					}else {
						$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
					}

					array_push($datafix, array(
						 "drivername"            	=> $drivername,
						 "driverimage"            => $driverimage,
						 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
						 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
						 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
						 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
						 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
						 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
						 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
						 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
						 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
						 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
						 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
						 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
						 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
						 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
						 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
						 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
						 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
						 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
						 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
						 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
						 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
						 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
						 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
						 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
						 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
						 "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
						 // "vehicle_info"           => $result[$i]['vehicle_info'],
						 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
						 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
						 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
						 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
						 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
						 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
						 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
						 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
						 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
						 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
						 "devicestatusfixnya" 	  => $devicestatusfixnya,
						 "auto_last_road" 					=> $autolastroad,
						 "autolastritase" 				=> $autolastritase,
						 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
						 "auto_last_update"       => $lastinfofix->gps_date_fmt. " ". $lastinfofix->gps_time_fmt,
						 "auto_last_check"        => $jsonnya[0]->auto_last_check,
						 "auto_last_snap"         => $snap,
						 "auto_last_snap_time"    => $snaptime,
						 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $lastinfofix->georeverse->display_name),
						 "auto_last_lat"          => substr($lastinfofix->gps_latitude_real_fmt, 0, 10),
						 "auto_last_long"         => substr($lastinfofix->gps_longitude_real_fmt, 0, 10),
						 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
						 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
						 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
						 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
						 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
					));
			}else {
				$jsonnya[0] = json_decode($mastervehicle[0]['vehicle_autocheck']);
					if (isset($jsonnya[0]->auto_last_snap)) {
						$snap     = $jsonnya[0]->auto_last_snap;
						$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
					}else {
						$snap     = "";
						$snaptime = "";
					}

					if (isset($jsonnya[0]->auto_last_road)) {
						$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_road);
					}else {
						$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
					}

					if (isset($jsonnya[0]->auto_last_ritase)) {
						$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_ritase);
					}else {
						$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
					}

					array_push($datafix, array(
						 "drivername"            	=> $drivername,
						 "driverimage"            => $driverimage,
						 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
						 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
						 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
						 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
						 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
						 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
						 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
						 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
						 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
						 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
						 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
						 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
						 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
						 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
						 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
						 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
						 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
						 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
						 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
						 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
						 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
						 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
						 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
						 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
						 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
						 "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
						 // "vehicle_info"           => $result[$i]['vehicle_info'],
						 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
						 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
						 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
						 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
						 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
						 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
						 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
						 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
						 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
						 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
						 "devicestatusfixnya" 	  => $devicestatusfixnya,
						 "auto_last_road" 					=> $autolastroad,
						 "autolastritase" 				=> $autolastritase,
						 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
						 "auto_last_update"       => $jsonnya[0]->auto_last_update,
						 "auto_last_check"        => $jsonnya[0]->auto_last_check,
						 "auto_last_snap"         => $snap,
						 "auto_last_snap_time"    => $snaptime,
						 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_position),
						 "auto_last_lat"          => substr($jsonnya[0]->auto_last_lat, 0, 10),
						 "auto_last_long"         => substr($jsonnya[0]->auto_last_long, 0, 10),
						 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
						 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
						 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
						 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
						 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
					));
			}

			// echo "<pre>";
			// var_dump($datafix);die();
			// echo "<pre>";
			echo json_encode($datafix);
		}
	}

	function outofgeofence(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['sortby']  = "mobil_id";
		$this->params['orderby'] = "asc";
		$this->params['title']   = "Maps All";
		if($this->sess->user_id == "1445"){
			$user_id = $this->sess->user_id; //tag
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

		$datafix            = array();
		$datafixbgt         = array();
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
						 "gps_ns"             		=> $getdata[$i][0]['gps_ns'],
						 "gps_ew"             		=> $getdata[$i][0]['gps_ew'],
						 "gps_latitude"           => $getdata[$i][0]['gps_latitude'],
						 "gps_longitude"          => $getdata[$i][0]['gps_longitude'],
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
						 "vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
						"gps_ns"             		 => "",
						"gps_ew"             		 => "",
						"gps_latitude"           => "",
						"gps_longitude"          => "",
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
						"vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
					"gps_ns" 		 						 => $datafixbgt[$loop]['gps_ns'],
					"gps_ew" 		 						 => $datafixbgt[$loop]['gps_ew'],
					"gps_latitude"           => $datafixbgt[$loop]['gps_latitude'],
					"gps_longitude"          => $datafixbgt[$loop]['gps_longitude'],
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
					"vehicle_fuel_volt" 		 => $datafixbgt[$loop]['vehicle_fuel_volt'],
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
					"auto_last_lat"          => $jsonnya[$loop]['auto_last_lat'],
					"auto_last_long"         => $jsonnya[$loop]['auto_last_long'],
					"auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_engine']),
					"auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_gpsstatus']),
					"auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_speed']),
					"auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_course']),
					"auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_flag'])
				));
		}

		$dataformaps = array();
		for ($loopx=0; $loopx < sizeof($throwdatatoview); $loopx++) {
			if ($throwdatatoview[$loopx]['gps_ns'] != "") {
				if (in_array(strtoupper($throwdatatoview[$loopx]['vehicle_type']), $this->config->item("vehicle_others"))){
					// echo "disini";
					$geofence_location[] = $this->m_poipoolmaster->getGeofence_location_other($throwdatatoview[$loopx]['auto_last_long'], $throwdatatoview[$loopx]['auto_last_lat'], $throwdatatoview[$loopx]['vehicle_user_id']);
						array_push($dataformaps, array(
							"geofence" 		 					 => $geofence_location[$loopx],
							"gps_ns" 		 						 => $throwdatatoview[$loopx]['gps_ns'],
							"gps_ew" 		 						 => $throwdatatoview[$loopx]['gps_ew'],
							"gps_latitude"           => $throwdatatoview[$loopx]['gps_latitude'],
							"gps_longitude"          => $throwdatatoview[$loopx]['gps_longitude'],
							"is_update" 						 => $throwdatatoview[$loopx]['is_update'],
							"vehicle_id"             => $throwdatatoview[$loopx]['vehicle_id'],
							"vehicle_user_id"        => $throwdatatoview[$loopx]['vehicle_user_id'],
							"vehicle_device"         => $throwdatatoview[$loopx]['vehicle_device'],
							"vehicle_no"             => $throwdatatoview[$loopx]['vehicle_no'],
							"vehicle_name"           => $throwdatatoview[$loopx]['vehicle_name'],
							"vehicle_active_date2"   => $throwdatatoview[$loopx]['vehicle_active_date2'],
							"vehicle_card_no"        => $throwdatatoview[$loopx]['vehicle_card_no'],
							"vehicle_operator"       => $throwdatatoview[$loopx]['vehicle_operator'],
							"vehicle_active_date"    => $throwdatatoview[$loopx]['vehicle_active_date'],
							"vehicle_active_date1"   => $throwdatatoview[$loopx]['vehicle_active_date1'],
							"vehicle_status"         => $throwdatatoview[$loopx]['vehicle_status'],
							"vehicle_image"          => $throwdatatoview[$loopx]['vehicle_image'],
							"vehicle_created_date"   => $throwdatatoview[$loopx]['vehicle_created_date'],
							"vehicle_type"           => $throwdatatoview[$loopx]['vehicle_type'],
							"vehicle_autorefill"     => $throwdatatoview[$loopx]['vehicle_autorefill'],
							"vehicle_maxspeed"       => $throwdatatoview[$loopx]['vehicle_maxspeed'],
							"vehicle_maxparking"     => $throwdatatoview[$loopx]['vehicle_maxparking'],
							"vehicle_company"        => $throwdatatoview[$loopx]['vehicle_company'],
							"vehicle_subcompany"     => $throwdatatoview[$loopx]['vehicle_subcompany'],
							"vehicle_group"          => $throwdatatoview[$loopx]['vehicle_group'],
							"vehicle_subgroup"       => $throwdatatoview[$loopx]['vehicle_subgroup'],
							"vehicle_odometer"       => $throwdatatoview[$loopx]['vehicle_odometer'],
							"vehicle_payment_type"   => $throwdatatoview[$loopx]['vehicle_payment_type'],
							"vehicle_payment_amount" => $throwdatatoview[$loopx]['vehicle_payment_amount'],
							"vehicle_fuel_capacity"  => $throwdatatoview[$loopx]['vehicle_fuel_capacity'],
							"vehicle_fuel_volt" 		 => $throwdatatoview[$loopx]['vehicle_fuel_volt'],
							"vehicle_sales"          => $throwdatatoview[$loopx]['vehicle_sales'],
							"vehicle_teknisi_id"     => $throwdatatoview[$loopx]['vehicle_teknisi_id'],
							"vehicle_tanggal_pasang" => $throwdatatoview[$loopx]['vehicle_tanggal_pasang'],
							"vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['vehicle_imei']),
							"vehicle_dbhistory"      => $throwdatatoview[$loopx]['vehicle_dbhistory'],
							"vehicle_dbhistory_name" => $throwdatatoview[$loopx]['vehicle_dbhistory_name'],
							"vehicle_dbname_live"    => $throwdatatoview[$loopx]['vehicle_dbname_live'],
							"vehicle_isred"          => $throwdatatoview[$loopx]['vehicle_isred'],
							"vehicle_modem"          => $throwdatatoview[$loopx]['vehicle_modem'],
							"vehicle_card_no_status" => $throwdatatoview[$loopx]['vehicle_card_no_status'],
							// "auto_last_position"  	 => str_replace(array("\n","\r","'","'\'","/", "-"), "", $datafixbgt[$loop]['position']),
							"auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_status']),
							"auto_last_update"       => date("d F Y H:i:s", strtotime($throwdatatoview[$loopx]['auto_last_update'])),
							"auto_last_check"        => $throwdatatoview[$loopx]['auto_last_check'],
							// "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_position']),
							"auto_last_lat"          => $throwdatatoview[$loopx]['auto_last_lat'],
							"auto_last_long"         => $throwdatatoview[$loopx]['auto_last_long'],
							"auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_last_engine']),
							"auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_last_gpsstatus']),
							"auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_last_speed']),
							"auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_last_course']),
							"auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_flag'])
						));
				}else{
					// echo "disini 1";
					// LONGLAT yg dikirim adalah coordinat mentah dari gps
					$geofence_location[] = $this->m_poipoolmaster->getGeofence_location($throwdatatoview[$loopx]['gps_longitude'], $throwdatatoview[$loopx]['gps_ew'], $throwdatatoview[$loopx]['gps_latitude'], $throwdatatoview[$loopx]['gps_ns'], $throwdatatoview[$loopx]['vehicle_user_id']);
					array_push($dataformaps, array(
						"geofence" 		 					 => $geofence_location[$loopx],
						"gps_ns" 		 						 => $throwdatatoview[$loopx]['gps_ns'],
						"gps_ew" 		 						 => $throwdatatoview[$loopx]['gps_ew'],
						"gps_latitude"           => $throwdatatoview[$loopx]['gps_latitude'],
						"gps_longitude"          => $throwdatatoview[$loopx]['gps_longitude'],
						"is_update" 						 => $throwdatatoview[$loopx]['is_update'],
						"vehicle_id"             => $throwdatatoview[$loopx]['vehicle_id'],
						"vehicle_user_id"        => $throwdatatoview[$loopx]['vehicle_user_id'],
						"vehicle_device"         => $throwdatatoview[$loopx]['vehicle_device'],
						"vehicle_no"             => $throwdatatoview[$loopx]['vehicle_no'],
						"vehicle_name"           => $throwdatatoview[$loopx]['vehicle_name'],
						"vehicle_active_date2"   => $throwdatatoview[$loopx]['vehicle_active_date2'],
						"vehicle_card_no"        => $throwdatatoview[$loopx]['vehicle_card_no'],
						"vehicle_operator"       => $throwdatatoview[$loopx]['vehicle_operator'],
						"vehicle_active_date"    => $throwdatatoview[$loopx]['vehicle_active_date'],
						"vehicle_active_date1"   => $throwdatatoview[$loopx]['vehicle_active_date1'],
						"vehicle_status"         => $throwdatatoview[$loopx]['vehicle_status'],
						"vehicle_image"          => $throwdatatoview[$loopx]['vehicle_image'],
						"vehicle_created_date"   => $throwdatatoview[$loopx]['vehicle_created_date'],
						"vehicle_type"           => $throwdatatoview[$loopx]['vehicle_type'],
						"vehicle_autorefill"     => $throwdatatoview[$loopx]['vehicle_autorefill'],
						"vehicle_maxspeed"       => $throwdatatoview[$loopx]['vehicle_maxspeed'],
						"vehicle_maxparking"     => $throwdatatoview[$loopx]['vehicle_maxparking'],
						"vehicle_company"        => $throwdatatoview[$loopx]['vehicle_company'],
						"vehicle_subcompany"     => $throwdatatoview[$loopx]['vehicle_subcompany'],
						"vehicle_group"          => $throwdatatoview[$loopx]['vehicle_group'],
						"vehicle_subgroup"       => $throwdatatoview[$loopx]['vehicle_subgroup'],
						"vehicle_odometer"       => $throwdatatoview[$loopx]['vehicle_odometer'],
						"vehicle_payment_type"   => $throwdatatoview[$loopx]['vehicle_payment_type'],
						"vehicle_payment_amount" => $throwdatatoview[$loopx]['vehicle_payment_amount'],
						"vehicle_fuel_capacity"  => $throwdatatoview[$loopx]['vehicle_fuel_capacity'],
						"vehicle_fuel_volt" 		 => $throwdatatoview[$loopx]['vehicle_fuel_volt'],
						"vehicle_sales"          => $throwdatatoview[$loopx]['vehicle_sales'],
						"vehicle_teknisi_id"     => $throwdatatoview[$loopx]['vehicle_teknisi_id'],
						"vehicle_tanggal_pasang" => $throwdatatoview[$loopx]['vehicle_tanggal_pasang'],
						"vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['vehicle_imei']),
						"vehicle_dbhistory"      => $throwdatatoview[$loopx]['vehicle_dbhistory'],
						"vehicle_dbhistory_name" => $throwdatatoview[$loopx]['vehicle_dbhistory_name'],
						"vehicle_dbname_live"    => $throwdatatoview[$loopx]['vehicle_dbname_live'],
						"vehicle_isred"          => $throwdatatoview[$loopx]['vehicle_isred'],
						"vehicle_modem"          => $throwdatatoview[$loopx]['vehicle_modem'],
						"vehicle_card_no_status" => $throwdatatoview[$loopx]['vehicle_card_no_status'],
						// "auto_last_position"  	 => str_replace(array("\n","\r","'","'\'","/", "-"), "", $datafixbgt[$loop]['position']),
						"auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_status']),
						"auto_last_update"       => date("d F Y H:i:s", strtotime($throwdatatoview[$loopx]['auto_last_update'])),
						"auto_last_check"        => $throwdatatoview[$loopx]['auto_last_check'],
						// "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_position']),
						"auto_last_lat"          => $throwdatatoview[$loopx]['auto_last_lat'],
						"auto_last_long"         => $throwdatatoview[$loopx]['auto_last_long'],
						"auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_last_engine']),
						"auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_last_gpsstatus']),
						"auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_last_speed']),
						"auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_last_course']),
						"auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $throwdatatoview[$loopx]['auto_flag'])
					));
				}
			}
		}

		$lastfixdata = array();
		for ($loopfix=0; $loopfix < sizeof($dataformaps); $loopfix++) {
			if ($dataformaps[$loopfix]['geofence'] == false) {
				array_push($lastfixdata, array(
					"geofence" 		 					 => $dataformaps[$loopfix]['geofence'],
					"gps_ns" 		 						 => $dataformaps[$loopfix]['gps_ns'],
					"gps_ew" 		 						 => $dataformaps[$loopfix]['gps_ew'],
					"gps_latitude"           => $dataformaps[$loopfix]['gps_latitude'],
					"gps_longitude"          => $dataformaps[$loopfix]['gps_longitude'],
					"is_update" 						 => $dataformaps[$loopfix]['is_update'],
					"vehicle_id"             => $dataformaps[$loopfix]['vehicle_id'],
					"vehicle_user_id"        => $dataformaps[$loopfix]['vehicle_user_id'],
					"vehicle_device"         => $dataformaps[$loopfix]['vehicle_device'],
					"vehicle_no"             => $dataformaps[$loopfix]['vehicle_no'],
					"vehicle_name"           => $dataformaps[$loopfix]['vehicle_name'],
					"vehicle_active_date2"   => $dataformaps[$loopfix]['vehicle_active_date2'],
					"vehicle_card_no"        => $dataformaps[$loopfix]['vehicle_card_no'],
					"vehicle_operator"       => $dataformaps[$loopfix]['vehicle_operator'],
					"vehicle_active_date"    => $dataformaps[$loopfix]['vehicle_active_date'],
					"vehicle_active_date1"   => $dataformaps[$loopfix]['vehicle_active_date1'],
					"vehicle_status"         => $dataformaps[$loopfix]['vehicle_status'],
					"vehicle_image"          => $dataformaps[$loopfix]['vehicle_image'],
					"vehicle_created_date"   => $dataformaps[$loopfix]['vehicle_created_date'],
					"vehicle_type"           => $dataformaps[$loopfix]['vehicle_type'],
					"vehicle_autorefill"     => $dataformaps[$loopfix]['vehicle_autorefill'],
					"vehicle_maxspeed"       => $dataformaps[$loopfix]['vehicle_maxspeed'],
					"vehicle_maxparking"     => $dataformaps[$loopfix]['vehicle_maxparking'],
					"vehicle_company"        => $dataformaps[$loopfix]['vehicle_company'],
					"vehicle_subcompany"     => $dataformaps[$loopfix]['vehicle_subcompany'],
					"vehicle_group"          => $dataformaps[$loopfix]['vehicle_group'],
					"vehicle_subgroup"       => $dataformaps[$loopfix]['vehicle_subgroup'],
					"vehicle_odometer"       => $dataformaps[$loopfix]['vehicle_odometer'],
					"vehicle_payment_type"   => $dataformaps[$loopfix]['vehicle_payment_type'],
					"vehicle_payment_amount" => $dataformaps[$loopfix]['vehicle_payment_amount'],
					"vehicle_fuel_capacity"  => $dataformaps[$loopfix]['vehicle_fuel_capacity'],
					"vehicle_fuel_volt" 		 => $dataformaps[$loopfix]['vehicle_fuel_volt'],
					"vehicle_sales"          => $dataformaps[$loopfix]['vehicle_sales'],
					"vehicle_teknisi_id"     => $dataformaps[$loopfix]['vehicle_teknisi_id'],
					"vehicle_tanggal_pasang" => $dataformaps[$loopfix]['vehicle_tanggal_pasang'],
					"vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $dataformaps[$loopfix]['vehicle_imei']),
					"vehicle_dbhistory"      => $dataformaps[$loopfix]['vehicle_dbhistory'],
					"vehicle_dbhistory_name" => $dataformaps[$loopfix]['vehicle_dbhistory_name'],
					"vehicle_dbname_live"    => $dataformaps[$loopfix]['vehicle_dbname_live'],
					"vehicle_isred"          => $dataformaps[$loopfix]['vehicle_isred'],
					"vehicle_modem"          => $dataformaps[$loopfix]['vehicle_modem'],
					"vehicle_card_no_status" => $dataformaps[$loopfix]['vehicle_card_no_status'],
					// "auto_last_position"  	 => str_replace(array("\n","\r","'","'\'","/", "-"), "", $datafixbgt[$loop]['position']),
					"auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $dataformaps[$loopfix]['auto_status']),
					"auto_last_update"       => date("d F Y H:i:s", strtotime($dataformaps[$loopfix]['auto_last_update'])),
					"auto_last_check"        => $dataformaps[$loopfix]['auto_last_check'],
					// "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_position']),
					"auto_last_lat"          => $dataformaps[$loopfix]['auto_last_lat'],
					"auto_last_long"         => $dataformaps[$loopfix]['auto_last_long'],
					"auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $dataformaps[$loopfix]['auto_last_engine']),
					"auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $dataformaps[$loopfix]['auto_last_gpsstatus']),
					"auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $dataformaps[$loopfix]['auto_last_speed']),
					"auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $dataformaps[$loopfix]['auto_last_course']),
					"auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $dataformaps[$loopfix]['auto_flag'])
				));
			}
		}


		$this->params['poolmaster'] = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);


		$this->params['vehicle']      = $lastfixdata;
		$this->params['jumlahdata']   = sizeof($lastfixdata);
		$this->params['vehicletotal'] = sizeof($dataformaps);

		// echo "<pre>";
		// var_dump($this->params['vehicle']);die();
		// echo "<pre>";

		//get company
		$company                       = $this->dashboardmodel->getcompany_byowner();
		$this->params['company']       = $company;
		$this->params['companyid']     = $companyid;
		$this->params['url_code_view'] = 1;
		$this->params['code_view_menu'] = "monitor";

		$this->params["header"]        = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]       = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]   = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]       = $this->load->view('dashboard/trackers/maps_view_outofgeofence', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function datagps(){
		// 1147-webtracking_gps_powerblock_live

		$user_id_fix    = $this->sess->user_id;
		$companyid      = $this->uri->segment(3);
		$user_dblive    = $this->sess->user_dblive;

		// $user_id_fix = 3212;
		// $user_dblive = "webtracking_gps_tag_live";
		$datafromdblive = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
		$mastervehicle  = $this->m_poipoolmaster->getmastervehiclejs();

		// string(47) "1-48-0-0-0-webtracking_gps_powerblock_live-1147"
		// 1-1806-0-0-0-webtracking_gps_tag_live-3212

		// echo "<pre>";
		// var_dump($mastervehicle);die();
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
							 "vehicle_fuel_volt" 		 	=> $mastervehicle[$i]['vehicle_fuel_volt'],
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
						"vehicle_fuel_volt" 		 	=> $mastervehicle[$i]['vehicle_fuel_volt'],
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

		$datafixbgt = array_merge($datafix, $deviceidygtidakada);
		// echo "<pre>";
		// var_dump($deviceidygtidakada);die();
		// echo "<pre>";
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
				"vehicle_fuel_volt" 		 => $datafixbgt[$loop]['vehicle_fuel_volt'],
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
		// echo "string";
		$poolmaster = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);

		echo json_encode(array("total" => sizeof($throwdatatoview), "data" => $throwdatatoview, "poolmaster" => $poolmaster));
	}

	function clusteringmaps(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		if($this->sess->user_id == "1445"){
			$user_id = $this->sess->user_id; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$user_id_fix 										= $user_id;

		$companyid                      = $this->sess->user_company;
		$user_dblive                    = $this->sess->user_dblive;
		$datafromdblive                 = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
		$mastervehicle                  = $this->m_poipoolmaster->getmastervehicle();
		//
		$datafix            = array();
		$datafixbgt         = array();
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
							 "vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
						"vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
				"vehicle_fuel_volt" 		  => $datafixbgt[$loop]['vehicle_fuel_volt'],
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

		$company                  = $this->dashboardmodel->getcompany_byowner();
		$datavehicleandcompany    = array();
		$datavehicleandcompanyfix = array();

			for ($d=0; $d < sizeof($company); $d++) {
				$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
				// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
				$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
				$totalengine       = explode("|", $totaldata);
					array_push($datavehicleandcompany, array(
						"company_id"   => $company[$d]->company_id,
						"company_name" => $company[$d]->company_name,
						"totalmobil"   => $totalengine[2],
						"vehicle"      => $vehicledata[$d]
					));
			}

			// echo "<pre>";
			// var_dump($vehicledata);die();
			// echo "<pre>";

		$this->params['company']        = $company;
		$this->params['companyid']      = $companyid;
		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicle']      = $datavehicleandcompany;
		$this->params['vehicledata']  = $throwdatatoview;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
		$totalmobilnya                = sizeof($getvehicle_byowner);
		if ($totalmobilnya == 0) {
	    $this->params['name']         = "0";
	    $this->params['host']         = "0";
	  }else {
	    $arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
	    $this->params['name']         = $arr[0];
	    $this->params['host']         = $arr[1];
	  }

		$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
	  $this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
	  $this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();

		// echo "<pre>";
		// var_dump($this->params['vehicledata']);die();
		// echo "<pre>";

		// KONDISI BUAT MAPS
		if ($this->config->item('app_powerblock') == 1) {
			// print_r("disini 1");exit();
			$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('powerblock/dashboard/sidebar_maps', $this->params, true);
			$this->params["content"]        = $this->load->view('powerblock/dashboard/maps/maps_view_clusterring', $this->params, true);
			$this->load->view("dashboard/template_dashboard_report", $this->params);
		}elseif ($this->config->item('app_default') == 1) {
			// print_r("disini 2");exit();
			if ($user_id == "389") {
				$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
				$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps', $this->params, true);
				$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('farrasindo/dashboard/maps/maps_view', $this->params, true);
				$this->load->view("dashboard/template_dashboard_report", $this->params);
			}else {
				$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
				$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps', $this->params, true);
				$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('dashboard/trackers/maps_view', $this->params, true);
				$this->load->view("dashboard/template_dashboard_report", $this->params);
			}
		}else {
			// print_r("disini 3");exit();
			$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps', $this->params, true);
			$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('dashboard/trackers/maps_view', $this->params, true);
			$this->load->view("dashboard/template_dashboard_report", $this->params);
		}
	}

	function index(){ // maps with overlay seperti di history map
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		if($this->sess->user_id == "1445"){
			$user_id = $this->sess->user_id; //tag
		}else{
			$user_id = $this->sess->user_id;
		}

		$user_id_fix 										= $user_id;

		$companyid                      = $this->sess->user_company;
		$user_dblive                    = $this->sess->user_dblive;
		$datafromdblive                 = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
		$mastervehicle                  = $this->m_poipoolmaster->getmastervehicle();
		//
		$datafix            = array();
		$datafixbgt         = array();
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
							 "vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
							 "vehicle_mv03" 					=> $mastervehicle[$i]['vehicle_mv03'],
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
						"vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
						"vehicle_mv03" 					 => $mastervehicle[$i]['vehicle_mv03'],
						// "position"  	  				 => $laspositionfromgpsmodel[$i]->georeverse->display_name,
						"vehicle_autocheck" 	 	 => $mastervehicle[$i]['vehicle_autocheck']
					));
				}
		}

		// echo "<pre>";
		// var_dump($datafix[2]['vehicle_autocheck']);die();
		// echo "<pre>";

		$datafixbgt = array_merge($datafix, $deviceidygtidakada);
		$throwdatatoview = array();
		for ($loop=0; $loop < sizeof($datafixbgt); $loop++) {
			$jsonnya[$loop] = json_decode($datafixbgt[$loop]['vehicle_autocheck'], true);
				if (isset($jsonnya[$loop]['auto_last_road'])) {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_road']);
				}else {
					$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[$loop]['auto_last_ritase'])) {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_ritase']);
				}else {
					$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
				}

				if (isset($jsonnya[$loop]['auto_last_mvd'])) {
					$autolastfuel = $jsonnya[$loop]['auto_last_mvd'];
				}else {
					$autolastfuel = "";
				}

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
				"vehicle_fuel_volt" 		 => $datafixbgt[$loop]['vehicle_fuel_volt'],
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
				"vehicle_mv03" 					 => $datafixbgt[$loop]['vehicle_mv03'],
				"auto_last_road"         => $autolastroad,
				"auto_last_ritase"       => $autolastritase,
				"auto_last_fuel"         => $autolastfuel,
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

		$company                  = $this->dashboardmodel->getcompany_byowner();
		$datavehicleandcompany    = array();
		$datavehicleandcompanyfix = array();

			for ($d=0; $d < sizeof($company); $d++) {
				$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
				// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
				$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
				$totalengine       = explode("|", $totaldata);
					array_push($datavehicleandcompany, array(
						"company_id"   => $company[$d]->company_id,
						"company_name" => $company[$d]->company_name,
						"totalmobil"   => $totalengine[2],
						"vehicle"      => $vehicledata[$d]
					));
			}

			// echo "<pre>";
			// var_dump($jsonnya);die();
			// echo "<pre>";

		$this->params['company']        = $company;
		$this->params['companyid']      = $companyid;
		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicle']      = $datavehicleandcompany;
		$this->params['vehicledata']  = $throwdatatoview;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
		$totalmobilnya                = sizeof($getvehicle_byowner);
		if ($totalmobilnya == 0) {
	    $this->params['name']         = "0";
	    $this->params['host']         = "0";
	  }else {
	    $arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
	    $this->params['name']         = $arr[0];
	    $this->params['host']         = $arr[1];
	  }

		$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
	  $this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
	  $this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();

		// echo "<pre>";
		// var_dump($this->params['vehicledata']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_kalimantan', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function getthisvideo(){
		// echo "<pre>";
		// var_dump($_POST['deviceid']);die();
		// echo "<pre>";
		$user_dblive     = $this->sess->user_dblive;
		$device_id       = $_POST['deviceid'];
		$device          = explode("@", $_POST['deviceid']);
		$device0         = $device[0];
		$device1         = $device[1];
		$devicefix 			 = $_POST['imei'];

		$mastervehicle   = $this->m_poipoolmaster->getmastervehiclebydevid($device_id);
		$getdatalastinfo = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
		$lastinfofix 	   = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");

		$datafix = array();
		$deviceidfrommastervehicle = explode("@", $mastervehicle[0]['vehicle_device']);

		if (sizeof($getdatalastinfo) > 0) {
			$jsonnya[0] = json_decode($getdatalastinfo[0]['vehicle_autocheck']);
				if (isset($jsonnya[0]->auto_last_snap)) {
					$snap     = $jsonnya[0]->auto_last_snap;
					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
				}else {
					$snap     = "";
					$snaptime = "";
				}
				array_push($datafix, array(
					 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
					 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
					 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
					 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
					 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
					 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
					 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
					 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
					 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
					 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
					 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
					 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
					 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
					 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
					 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
					 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
					 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
					 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
					 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
					 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
					 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
					 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
					 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
					 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
					 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
					 "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
					 // "vehicle_info"           => $result[$i]['vehicle_info'],
					 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
					 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
					 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
					 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
					 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
					 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
					 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
					 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
					 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
					 "auto_last_update"       => $lastinfofix->gps_date_fmt. " ". $lastinfofix->gps_time_fmt,
					 "auto_last_check"        => $jsonnya[0]->auto_last_check,
					 "auto_last_snap"         => $snap,
					 "auto_last_snap_time"    => $snaptime,
					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $lastinfofix->georeverse->display_name),
					 "auto_last_lat"          => substr($lastinfofix->gps_latitude_real_fmt, 0, 10),
					 "auto_last_long"         => substr($lastinfofix->gps_longitude_real_fmt, 0, 10),
					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
				));
		}else {
			$jsonnya[0] = json_decode($mastervehicle[0]['vehicle_autocheck']);
				if (isset($jsonnya[0]->auto_last_snap)) {
					$snap     = $jsonnya[0]->auto_last_snap;
					$snaptime = date("d F Y H:i:s", strtotime($jsonnya[0]->auto_last_snap_time));
				}else {
					$snap     = "";
					$snaptime = "";
				}
				array_push($datafix, array(
					 "vehicle_id"             => $mastervehicle[0]['vehicle_id'],
					 "vehicle_user_id"        => $mastervehicle[0]['vehicle_user_id'],
					 "vehicle_device"         => $mastervehicle[0]['vehicle_device'],
					 "vehicle_no"             => $mastervehicle[0]['vehicle_no'],
					 "vehicle_name"           => $mastervehicle[0]['vehicle_name'],
					 "vehicle_active_date2"   => $mastervehicle[0]['vehicle_active_date2'],
					 "vehicle_card_no"        => $mastervehicle[0]['vehicle_card_no'],
					 "vehicle_operator"       => $mastervehicle[0]['vehicle_operator'],
					 "vehicle_active_date"    => $mastervehicle[0]['vehicle_active_date'],
					 "vehicle_active_date1"   => $mastervehicle[0]['vehicle_active_date1'],
					 "vehicle_status"         => $mastervehicle[0]['vehicle_status'],
					 "vehicle_image"          => $mastervehicle[0]['vehicle_image'],
					 "vehicle_created_date"   => $mastervehicle[0]['vehicle_created_date'],
					 "vehicle_type"           => $mastervehicle[0]['vehicle_type'],
					 "vehicle_autorefill"     => $mastervehicle[0]['vehicle_autorefill'],
					 "vehicle_maxspeed"       => $mastervehicle[0]['vehicle_maxspeed'],
					 "vehicle_maxparking"     => $mastervehicle[0]['vehicle_maxparking'],
					 "vehicle_company"        => $mastervehicle[0]['vehicle_company'],
					 "vehicle_subcompany"     => $mastervehicle[0]['vehicle_subcompany'],
					 "vehicle_group"          => $mastervehicle[0]['vehicle_group'],
					 "vehicle_subgroup"       => $mastervehicle[0]['vehicle_subgroup'],
					 "vehicle_odometer"       => $mastervehicle[0]['vehicle_odometer'],
					 "vehicle_payment_type"   => $mastervehicle[0]['vehicle_payment_type'],
					 "vehicle_payment_amount" => $mastervehicle[0]['vehicle_payment_amount'],
					 "vehicle_fuel_capacity"  => $mastervehicle[0]['vehicle_fuel_capacity'],
					 "vehicle_fuel_volt" 		  => $mastervehicle[0]['vehicle_fuel_volt'],
					 // "vehicle_info"           => $result[$i]['vehicle_info'],
					 "vehicle_sales"          => $mastervehicle[0]['vehicle_sales'],
					 "vehicle_teknisi_id"     => $mastervehicle[0]['vehicle_teknisi_id'],
					 "vehicle_tanggal_pasang" => $mastervehicle[0]['vehicle_tanggal_pasang'],
					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
					 "vehicle_dbhistory"      => $mastervehicle[0]['vehicle_dbhistory'],
					 "vehicle_dbhistory_name" => $mastervehicle[0]['vehicle_dbhistory_name'],
					 "vehicle_dbname_live"    => $mastervehicle[0]['vehicle_dbname_live'],
					 "vehicle_isred"          => $mastervehicle[0]['vehicle_isred'],
					 "vehicle_modem"          => $mastervehicle[0]['vehicle_modem'],
					 "vehicle_card_no_status" => $mastervehicle[0]['vehicle_card_no_status'],
					 "auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
					 "auto_last_update"       => $jsonnya[0]->auto_last_update,
					 "auto_last_check"        => $jsonnya[0]->auto_last_check,
					 "auto_last_snap"         => $snap,
					 "auto_last_snap_time"    => $snaptime,
					 "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_position),
					 "auto_last_lat"          => substr($jsonnya[0]->auto_last_lat, 0, 10),
					 "auto_last_long"         => substr($jsonnya[0]->auto_last_long, 0, 10),
					 "auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
					 "auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
					 "auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
					 "auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
					 "auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
				));
		}

		// KALIMANTAN START
		// $devicefix = $datafix[0]['vehicle_no'];
		$url       = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=".$devicefix."&jsession=";
		$username  = "IND.LacakMobil";
		$password  = "000000";
		// $url       = "http://47.91.108.9:8080/808gps/open/player/RealPlayVideo.html?account=".$username."&password=".$password."&PlateNum=".$devicefix."&lang=en";

		$getthissession  = $this->m_securityevidence->getsession();
		$urlfix          = $url.$getthissession[0]['sess_value'];

		// GET LOGIN DENGAN SESSION LAMA
		$loginlama       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
			if ($loginlama) {
				$loginlamadecode = json_decode($loginlama);
				if (!$loginlamadecode) {
					if ($loginlamadecode->message == "Session does not exist!") {
						$loginbaru       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
						$loginbarudecode = json_decode($loginbaru);
						$urlfix          = $url.$loginbarudecode->jsession;
						$fixsession      = $loginbarudecode->jsession;
					}
				}else {
					$urlfix          = $url.$getthissession[0]['sess_value'];
					$fixsession      = $getthissession[0]['sess_value'];
				}
			}

			// GET DEVICE STATUS START
			$urlcekdevicestatus = "http://47.91.108.9:8080/StandardApiAction_getDeviceOlStatus.action?jsession=".$fixsession."&devIdno=".$devicefix;
			$cekstatus          = file_get_contents($urlcekdevicestatus);
			$loginbarudecode    = json_decode($cekstatus);
			$statusfixnya       = $loginbarudecode->onlines[0]->online;

			$callback['devicestatus']      = $statusfixnya;
			// GET DEVICE STATUS END

			$callback['vehicle']      = $datafix;
			$callback['urlfix']       = $urlfix;

			// echo "<pre>";
			// var_dump($hcekini);die();
			// echo "<pre>";

		echo json_encode($callback);
	}

	function getdriver($driver_vehicle) {

	$this->dbtransporter = $this->load->database('transporter',true);
	$this->dbtransporter->select("*");
	$this->dbtransporter->from("driver");
	$this->dbtransporter->order_by("driver_update_date","desc");
	$this->dbtransporter->where("driver_vehicle", $driver_vehicle);
	$this->dbtransporter->limit(1);
	$q = $this->dbtransporter->get();

	if ($q->num_rows > 0 ){
		$row = $q->row();
		$data = $row->driver_id;
		$data .= "-";
		$data .= $row->driver_name;
		return $data;
		$this->dbtransporter->close();
	}
	else {
	$this->dbtransporter->close();
	return false;
	}
}

function getdriverdetail($iddriver){
	$this->dbtransporter = $this->load->database('transporter',true);
	$this->dbtransporter->select("*");
	$this->dbtransporter->from("driver_image");
	$this->dbtransporter->where("driver_image_driver_id", $iddriver);
	$q   = $this->dbtransporter->get();
	return $q->result();
}

function streamall(){
	$mastervehicle   = $this->m_poipoolmaster->getmastervehicle2();
	$vehiclemv03array = array();
		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			if ($mastervehicle[$i]['vehicle_mv03'] != "0000") {
				array_push($vehiclemv03array, array(
					"vehiclemv03"    => $mastervehicle[$i]['vehicle_mv03'],
					"vehicle_no"     => $mastervehicle[$i]['vehicle_no'],
					"vehicle_name"   => $mastervehicle[$i]['vehicle_name'],
					"vehicle_device" => $mastervehicle[$i]['vehicle_device'],
					"vehicle_id" => $mastervehicle[$i]['vehicle_id'],
				));
			}
		}

	$devicefix 			 = $mastervehicle[0]['vehicle_mv03'];

	// KALIMANTAN START
	// $devicefix = $datafix[0]['vehicle_no'];
	$url       = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=".$devicefix."&jsession=";
	$username  = "IND.LacakMobil";
	$password  = "000000";
	// $url       = "http://47.91.108.9:8080/808gps/open/player/RealPlayVideo.html?account=".$username."&password=".$password."&PlateNum=".$devicefix."&lang=en";

	$getthissession  = $this->m_securityevidence->getsession();
	$urlfix          = $url.$getthissession[0]['sess_value'];

	// GET LOGIN DENGAN SESSION LAMA
	$loginlama       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession=".$getthissession[0]['sess_value']);
		if ($loginlama) {
			$loginlamadecode = json_decode($loginlama);
			if (!$loginlamadecode) {
				if ($loginlamadecode->message == "Session does not exist!") {
					$loginbaru       = file_get_contents("http://47.91.108.9:8080/StandardApiAction_login.action?account=".$username."&password=".$password);
					$loginbarudecode = json_decode($loginbaru);
					$urlfix          = $url.$loginbarudecode->jsession;
					$fixsession      = $loginbarudecode->jsession;
				}
			}else {
				$urlfix          = $url.$getthissession[0]['sess_value'];
				$fixsession      = $getthissession[0]['sess_value'];
			}
		}

		// GET DEVICE STATUS START
		$urlcekdevicestatus = "http://47.91.108.9:8080/StandardApiAction_getDeviceOlStatus.action?jsession=".$fixsession."&devIdno=".$devicefix;
		$cekstatus          = file_get_contents($urlcekdevicestatus);
		$loginbarudecode    = json_decode($cekstatus);
		// $statusfixnya       = $loginbarudecode->onlines[0]->online;

		$datafix = array();
		for ($j=0; $j < sizeof($vehiclemv03array); $j++) {
			array_push($datafix, array(
				"vehicle_no"     => $vehiclemv03array[$j]['vehicle_no'],
				"vehicle_name"   => $vehiclemv03array[$j]['vehicle_name'],
				"vehicle_device" => $vehiclemv03array[$j]['vehicle_device'],
				"vehicle_id" => $vehiclemv03array[$j]['vehicle_id'],
				"urlfix_id"    => "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno=".$vehiclemv03array[$j]['vehiclemv03']."&jsession=".$fixsession
			));
		}

		$companyid                = $this->sess->user_company;
		$user_dblive              = $this->sess->user_dblive;
		$company                  = $this->dashboardmodel->getcompany_byowner();
		$datavehicleandcompany    = array();
		$datavehicleandcompanyfix = array();

			for ($d=0; $d < sizeof($company); $d++) {
				$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
				// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
				$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
				$totalengine       = explode("|", $totaldata);
					array_push($datavehicleandcompany, array(
						"company_id"   => $company[$d]->company_id,
						"company_name" => $company[$d]->company_name,
						"totalmobil"   => $totalengine[2],
						"vehicle"      => $vehicledata[$d]
					));
			}

			// echo "<pre>";
			// var_dump($jsonnya);die();
			// echo "<pre>";

		$this->params['company']        = $company;
		$this->params['companyid']      = $companyid;

		// $this->params['devicestatus']   = $statusfixnya;
		$this->params['urlfix']         = $urlfix;
		$this->params['datafix']        = $datafix;
		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";

		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";

		// GET DEVICE STATUS END
		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps_kalimantan', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('dashboard/trackers/v_stream_all', $this->params, true);
		$this->load->view("dashboard/template_dashboard_kalimantan", $this->params);
}

function indexoverlay(){// maps ada overlay
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	if($this->sess->user_id == "1445"){
		$user_id = $this->sess->user_id; //tag
	}else{
		$user_id = $this->sess->user_id;
	}

	$user_id_fix 										= $user_id;

	$companyid                      = $this->sess->user_company;
	$user_dblive                    = $this->sess->user_dblive;
	$datafromdblive                 = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
	$mastervehicle                  = $this->m_poipoolmaster->getmastervehicle();
	//
	$datafix            = array();
	$datafixbgt         = array();
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
						 "vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
						 "vehicle_mv03" 					=> $mastervehicle[$i]['vehicle_mv03'],
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
					"vehicle_fuel_volt" 		 => $mastervehicle[$i]['vehicle_fuel_volt'],
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
					"vehicle_mv03" 					 => $mastervehicle[$i]['vehicle_mv03'],
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
			if (isset($jsonnya[$loop]['auto_last_road'])) {
				$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_road']);
			}else {
				$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
			}

			if (isset($jsonnya[$loop]['auto_last_ritase'])) {
				$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_ritase']);
			}else {
				$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
			}

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
			"vehicle_fuel_volt" 		 => $datafixbgt[$loop]['vehicle_fuel_volt'],
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
			"vehicle_mv03" 					 => $datafixbgt[$loop]['vehicle_mv03'],
			"auto_last_road"         => $autolastroad,
			"auto_last_ritase"       => $autolastritase,
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

	$company                  = $this->dashboardmodel->getcompany_byowner();
	$datavehicleandcompany    = array();
	$datavehicleandcompanyfix = array();

		for ($d=0; $d < sizeof($company); $d++) {
			$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
			// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
			$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
			$totalengine       = explode("|", $totaldata);
				array_push($datavehicleandcompany, array(
					"company_id"   => $company[$d]->company_id,
					"company_name" => $company[$d]->company_name,
					"totalmobil"   => $totalengine[2],
					"vehicle"      => $vehicledata[$d]
				));
		}

		// echo "<pre>";
		// var_dump($jsonnya);die();
		// echo "<pre>";

	$this->params['company']        = $company;
	$this->params['companyid']      = $companyid;
	$this->params['url_code_view']  = "1";
	$this->params['code_view_menu'] = "monitor";
	$this->params['maps_code']      = "morehundred";

	$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
	$datastatus                     = explode("|", $rstatus);
	$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
	$this->params['total_vehicle']  = $datastatus[3];
	$this->params['total_offline']  = $datastatus[2];

	$this->params['vehicle']      = $datavehicleandcompany;
	$this->params['vehicledata']  = $throwdatatoview;
	$this->params['vehicletotal'] = sizeof($mastervehicle);
	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);
	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
	$totalmobilnya                = sizeof($getvehicle_byowner);
	if ($totalmobilnya == 0) {
		$this->params['name']         = "0";
		$this->params['host']         = "0";
	}else {
		$arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
		$this->params['name']         = $arr[0];
		$this->params['host']         = $arr[1];
	}

	$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
	$this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
	$this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();

	// echo "<pre>";
	// var_dump($this->params['vehicledata']);die();
	// echo "<pre>";

	$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
	$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps_kalimantan', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
	$this->params["content"]        = $this->load->view('dashboard/mapsdirection/maps_view_kalimantan', $this->params, true);
	$this->load->view("dashboard/template_dashboard_kalimantan", $this->params);
}

function indexoverlaydirection(){
	// MAPS WITH OVERLAY N DIRECTION METHODE
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	if($this->sess->user_id == "1445"){
		$user_id = $this->sess->user_id; //tag
	}else{
		$user_id = $this->sess->user_id;
	}

	$user_id_fix 										= $user_id;

	$companyid                      = $this->sess->user_company;
	$user_dblive                    = $this->sess->user_dblive;
	$datafromdblive                 = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
	$mastervehicle                  = $this->m_poipoolmaster->getmastervehicle();
	//
	$datafix            = array();
	$datafixbgt         = array();
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
						 "vehicle_fuel_volt" 		 	=> $mastervehicle[$i]['vehicle_fuel_volt'],
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
						 "vehicle_mv03" 					=> $mastervehicle[$i]['vehicle_mv03'],
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
					"vehicle_fuel_volt" 		 	=> $mastervehicle[$i]['vehicle_fuel_volt'],
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
					"vehicle_mv03" 					 => $mastervehicle[$i]['vehicle_mv03'],
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
			if (isset($jsonnya[$loop]['auto_last_road'])) {
				$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_road']);
			}else {
				$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
			}

			if (isset($jsonnya[$loop]['auto_last_ritase'])) {
				$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_ritase']);
			}else {
				$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
			}

		array_push($throwdatatoview, array(
			"is_update" 						    => $datafixbgt[$loop]['is_update'],
			"vehicle_id"                => $datafixbgt[$loop]['vehicle_id'],
			"vehicle_user_id"           => $datafixbgt[$loop]['vehicle_user_id'],
			"vehicle_device"            => $datafixbgt[$loop]['vehicle_device'],
			"vehicle_no"                => $datafixbgt[$loop]['vehicle_no'],
			"vehicle_name"              => $datafixbgt[$loop]['vehicle_name'],
			"vehicle_active_date2"      => $datafixbgt[$loop]['vehicle_active_date2'],
			"vehicle_card_no"           => $datafixbgt[$loop]['vehicle_card_no'],
			"vehicle_operator"          => $datafixbgt[$loop]['vehicle_operator'],
			"vehicle_active_date"       => $datafixbgt[$loop]['vehicle_active_date'],
			"vehicle_active_date1"      => $datafixbgt[$loop]['vehicle_active_date1'],
			"vehicle_status"            => $datafixbgt[$loop]['vehicle_status'],
			"vehicle_image"             => $datafixbgt[$loop]['vehicle_image'],
			"vehicle_created_date"      => $datafixbgt[$loop]['vehicle_created_date'],
			"vehicle_type"              => $datafixbgt[$loop]['vehicle_type'],
			"vehicle_autorefill"        => $datafixbgt[$loop]['vehicle_autorefill'],
			"vehicle_maxspeed"          => $datafixbgt[$loop]['vehicle_maxspeed'],
			"vehicle_maxparking"        => $datafixbgt[$loop]['vehicle_maxparking'],
			"vehicle_company"           => $datafixbgt[$loop]['vehicle_company'],
			"vehicle_subcompany"        => $datafixbgt[$loop]['vehicle_subcompany'],
			"vehicle_group"             => $datafixbgt[$loop]['vehicle_group'],
			"vehicle_subgroup"          => $datafixbgt[$loop]['vehicle_subgroup'],
			"vehicle_odometer"          => $datafixbgt[$loop]['vehicle_odometer'],
			"vehicle_payment_type"      => $datafixbgt[$loop]['vehicle_payment_type'],
			"vehicle_payment_amount"    => $datafixbgt[$loop]['vehicle_payment_amount'],
			"vehicle_fuel_capacity"     => $datafixbgt[$loop]['vehicle_fuel_capacity'],
			"vehicle_fuel_volt" 		 		=> $datafixbgt[$loop]['vehicle_fuel_volt'],
			"vehicle_sales"             => $datafixbgt[$loop]['vehicle_sales'],
			"vehicle_teknisi_id"        => $datafixbgt[$loop]['vehicle_teknisi_id'],
			"vehicle_tanggal_pasang"    => $datafixbgt[$loop]['vehicle_tanggal_pasang'],
			"vehicle_imei"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $datafixbgt[$loop]['vehicle_imei']),
			"vehicle_dbhistory"         => $datafixbgt[$loop]['vehicle_dbhistory'],
			"vehicle_dbhistory_name"    => $datafixbgt[$loop]['vehicle_dbhistory_name'],
			"vehicle_dbname_live"       => $datafixbgt[$loop]['vehicle_dbname_live'],
			"vehicle_isred"             => $datafixbgt[$loop]['vehicle_isred'],
			"vehicle_modem"             => $datafixbgt[$loop]['vehicle_modem'],
			"vehicle_card_no_status"    => $datafixbgt[$loop]['vehicle_card_no_status'],
			"vehicle_mv03" 					    => $datafixbgt[$loop]['vehicle_mv03'],
			"auto_last_road"            => $autolastroad,
			"auto_last_ritase"          => $autolastritase,
			"auto_status"               => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_status']),
			"auto_last_update"          => date("d F Y H:i:s", strtotime($jsonnya[$loop]['auto_last_update'])),
			"auto_last_updatedirection" => date("Y-m-d H:i:s", strtotime($jsonnya[$loop]['auto_last_update'])),
			"auto_last_check"           => $jsonnya[$loop]['auto_last_check'],
			// "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_position']),
			"auto_last_lat"             => substr($jsonnya[$loop]['auto_last_lat'], 0, 10),
			"auto_last_long"            => substr($jsonnya[$loop]['auto_last_long'], 0, 10),
			"auto_last_engine"          => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_engine']),
			"auto_last_gpsstatus"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_gpsstatus']),
			"auto_last_speed"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_speed']),
			"auto_last_course"          => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_course']),
			"auto_flag"                 => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_flag'])
		));
	}

	$company                  = $this->dashboardmodel->getcompany_byowner();
	$datavehicleandcompany    = array();
	$datavehicleandcompanyfix = array();

		for ($d=0; $d < sizeof($company); $d++) {
			$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
			// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
			$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
			$totalengine       = explode("|", $totaldata);
				array_push($datavehicleandcompany, array(
					"company_id"   => $company[$d]->company_id,
					"company_name" => $company[$d]->company_name,
					"totalmobil"   => $totalengine[2],
					"vehicle"      => $vehicledata[$d]
				));
		}

		// echo "<pre>";
		// var_dump($jsonnya);die();
		// echo "<pre>";

	$this->params['company']        = $company;
	$this->params['companyid']      = $companyid;
	$this->params['url_code_view']  = "1";
	$this->params['code_view_menu'] = "monitor";
	$this->params['maps_code']      = "morehundred";

	$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
	$datastatus                     = explode("|", $rstatus);
	$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
	$this->params['total_vehicle']  = $datastatus[3];
	$this->params['total_offline']  = $datastatus[2];

	$this->params['vehicle']      = $datavehicleandcompany;
	$this->params['vehicledata']  = $throwdatatoview;
	$this->params['vehicletotal'] = sizeof($mastervehicle);
	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);
	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
	$totalmobilnya                = sizeof($getvehicle_byowner);
	if ($totalmobilnya == 0) {
		$this->params['name']         = "0";
		$this->params['host']         = "0";
	}else {
		$arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
		$this->params['name']         = $arr[0];
		$this->params['host']         = $arr[1];
	}

	$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
	$this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
	$this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();

	// echo "<pre>";
	// var_dump($this->params['vehicledata']);die();
	// echo "<pre>";

	$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);
	$this->params["sidebar"]        = $this->load->view('dashboard/sidebar_maps_kalimantan', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
	$this->params["content"]        = $this->load->view('dashboard/mapsdirection/maps_view_overlaydirection', $this->params, true);
	// $this->params["content"]        = $this->load->view('dashboard/mapsdirection/maps_view_overlaydirectioncontoh', $this->params, true);
	$this->load->view("dashboard/template_dashboard_kalimantan", $this->params);
}

function heatmap(){
	ini_set('max_execution_time', '300');
	set_time_limit(300);
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	if($this->sess->user_id == "1445"){
		$user_id = $this->sess->user_id; //tag
	}else{
		$user_id = $this->sess->user_id;
	}

	$user_id_fix 										= $user_id;

	$companyid                      = $this->sess->user_company;
	$user_dblive                    = $this->sess->user_dblive;
	$datafromdblive                 = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
	$mastervehicle                  = $this->m_poipoolmaster->getmastervehicle();
	//
	$datafix            = array();
	$datafixbgt         = array();
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
						 "vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
						 "vehicle_mv03" 					=> $mastervehicle[$i]['vehicle_mv03'],
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
					"vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
					"vehicle_mv03" 					 => $mastervehicle[$i]['vehicle_mv03'],
					// "position"  	  				 => $laspositionfromgpsmodel[$i]->georeverse->display_name,
					"vehicle_autocheck" 	 	 => $mastervehicle[$i]['vehicle_autocheck']
				));
			}
	}

	// echo "<pre>";
	// var_dump($datafix[2]['vehicle_autocheck']);die();
	// echo "<pre>";

	$datafixbgt = array_merge($datafix, $deviceidygtidakada);
	$throwdatatoview = array();
	for ($loop=0; $loop < sizeof($datafixbgt); $loop++) {
		$jsonnya[$loop] = json_decode($datafixbgt[$loop]['vehicle_autocheck'], true);
			if (isset($jsonnya[$loop]['auto_last_road'])) {
				$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_road']);
			}else {
				$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
			}

			if (isset($jsonnya[$loop]['auto_last_ritase'])) {
				$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_ritase']);
			}else {
				$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
			}

			if (isset($jsonnya[$loop]['auto_last_mvd'])) {
				$autolastfuel = $jsonnya[$loop]['auto_last_mvd'];
			}else {
				$autolastfuel = "";
			}

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
			"vehicle_fuel_volt" 		 => $datafixbgt[$loop]['vehicle_fuel_volt'],
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
			"vehicle_mv03" 					 => $datafixbgt[$loop]['vehicle_mv03'],
			"auto_last_road"         => $autolastroad,
			"auto_last_ritase"       => $autolastritase,
			"auto_last_fuel"         => $autolastfuel,
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

	$company                  = $this->dashboardmodel->getcompany_byowner();
	$datavehicleandcompany    = array();
	$datavehicleandcompanyfix = array();

		for ($d=0; $d < sizeof($company); $d++) {
			$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
			// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
			$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
			$totalengine       = explode("|", $totaldata);
				array_push($datavehicleandcompany, array(
					"company_id"   => $company[$d]->company_id,
					"company_name" => $company[$d]->company_name,
					"totalmobil"   => $totalengine[2],
					"vehicle"      => $vehicledata[$d]
				));
		}

		// echo "<pre>";
		// var_dump($jsonnya);die();
		// echo "<pre>";

	$this->params['company']        = $company;
	$this->params['companyid']      = $companyid;
	$this->params['url_code_view']  = "1";
	$this->params['code_view_menu'] = "monitor";
	$this->params['maps_code']      = "morehundred";

	$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
	$datastatus                     = explode("|", $rstatus);
	$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
	$this->params['total_vehicle']  = $datastatus[3];
	$this->params['total_offline']  = $datastatus[2];

	$this->params['vehicle']      = $datavehicleandcompany;
	$this->params['vehicledata']  = $throwdatatoview;
	$this->params['vehicletotal'] = sizeof($mastervehicle);
	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);
	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
	$totalmobilnya                = sizeof($getvehicle_byowner);
	if ($totalmobilnya == 0) {
		$this->params['name']         = "0";
		$this->params['host']         = "0";
	}else {
		$arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
		$this->params['name']         = $arr[0];
		$this->params['host']         = $arr[1];
	}

	$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
	$this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
	$this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
	$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting($this->sess->user_parent, $this->sess->user_id);

	// echo "<pre>";
	// var_dump($this->params['mapsetting']);die();
	// echo "<pre>";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
	$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmap', $this->params, true);
	$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
}

function heatmap2(){
	ini_set('max_execution_time', '300');
	set_time_limit(300);
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	if($this->sess->user_id == "1445"){
		$user_id = $this->sess->user_id; //tag
	}else{
		$user_id = $this->sess->user_id;
	}

	$user_id_fix 										= $user_id;

	$companyid                      = $this->sess->user_company;
	$user_dblive                    = $this->sess->user_dblive;
	$datafromdblive                 = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
	$mastervehicle                  = $this->m_poipoolmaster->getmastervehicle();

	// echo "<pre>";
	// var_dump($mastervehicle);die();
	// echo "<pre>";

	$datafix            = array();
	$datafixbgt         = array();
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
						 "vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
						 "vehicle_mv03" 					=> $mastervehicle[$i]['vehicle_mv03'],
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
					"vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
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
					"vehicle_mv03" 					 => $mastervehicle[$i]['vehicle_mv03'],
					// "position"  	  				 => $laspositionfromgpsmodel[$i]->georeverse->display_name,
					"vehicle_autocheck" 	 	 => $mastervehicle[$i]['vehicle_autocheck']
				));
			}
	}

	// echo "<pre>";
	// var_dump($datafix[2]['vehicle_autocheck']);die();
	// echo "<pre>";

	$datafixbgt = array_merge($datafix, $deviceidygtidakada);
	$throwdatatoview = array();
	for ($loop=0; $loop < sizeof($datafixbgt); $loop++) {
		$jsonnya[$loop] = json_decode($datafixbgt[$loop]['vehicle_autocheck'], true);
			if (isset($jsonnya[$loop]['auto_last_road'])) {
				$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_road']);
			}else {
				$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
			}

			if (isset($jsonnya[$loop]['auto_last_ritase'])) {
				$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_ritase']);
			}else {
				$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
			}

			if (isset($jsonnya[$loop]['auto_last_mvd'])) {
				$autolastfuel = $jsonnya[$loop]['auto_last_mvd'];
			}else {
				$autolastfuel = "";
			}

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
			"vehicle_fuel_volt" 		 => $datafixbgt[$loop]['vehicle_fuel_volt'],
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
			"vehicle_mv03" 					 => $datafixbgt[$loop]['vehicle_mv03'],
			"auto_last_road"         => $autolastroad,
			"auto_last_ritase"       => $autolastritase,
			"auto_last_fuel"         => $autolastfuel,
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

	$company                  = $this->dashboardmodel->getcompany_byowner();
	$datavehicleandcompany    = array();
	$datavehicleandcompanyfix = array();

		for ($d=0; $d < sizeof($company); $d++) {
			$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
			// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
			$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
			$totalengine       = explode("|", $totaldata);
				array_push($datavehicleandcompany, array(
					"company_id"   => $company[$d]->company_id,
					"company_name" => $company[$d]->company_name,
					"totalmobil"   => $totalengine[2],
					"vehicle"      => $vehicledata[$d]
				));
		}

		// echo "<pre>";
		// var_dump($jsonnya);die();
		// echo "<pre>";

	$this->params['company']        = $company;
	$this->params['companyid']      = $companyid;
	$this->params['url_code_view']  = "1";
	$this->params['code_view_menu'] = "monitor";
	$this->params['maps_code']      = "morehundred";

	$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
	$datastatus                     = explode("|", $rstatus);
	$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
	$this->params['total_vehicle']  = $datastatus[3];
	$this->params['total_offline']  = $datastatus[2];

	$this->params['vehicle']      = $datavehicleandcompany;
	$this->params['vehicledata']  = $throwdatatoview;
	$this->params['vehicletotal'] = sizeof($mastervehicle);
	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);
	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
	$totalmobilnya                = sizeof($getvehicle_byowner);
	if ($totalmobilnya == 0) {
		$this->params['name']         = "0";
		$this->params['host']         = "0";
	}else {
		$arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
		$this->params['name']         = $arr[0];
		$this->params['host']         = $arr[1];
	}

	$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
	$this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
	$this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
	$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting($this->sess->user_parent, $this->sess->user_id);

	// echo "<pre>";
	// var_dump($this->params['mapsetting']);die();
	// echo "<pre>";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
	$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmap2', $this->params, true);
	$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
}

function dataconsolidated(){
	$dataMuatan   = $this->m_poipoolmaster->getmuatanperkm("ts_minidashboard", $this->sess->user_id);
	$dataKosongan = $this->m_poipoolmaster->getkosonganperkm("ts_minidashboard", $this->sess->user_id);

		if ($dataMuatan) {
			echo json_encode(array("msg" => "success", "code" => 200, "datamuatan" =>$dataMuatan, "datakosongan" => $dataKosongan));
		}else {
			echo json_encode(array("msg" => "failed", "code" => 400));
		}
}

function vehicleonkmkosongan(){
	$dataKosongan = $this->m_poipoolmaster->getkosonganperkm("ts_minidashboard", $this->sess->user_id);
		if ($dataKosongan) {
			echo json_encode(array("msg" => "success", "code" => 200, "data" =>$dataKosongan));
		}else {
			echo json_encode(array("msg" => "failed", "code" => 400));
		}
}

function vehicleonrom(){
	$vehicleInRom = $this->m_poipoolmaster->getvehicleinrom("ts_minidashboard", $this->sess->user_id);
		if ($vehicleInRom) {
			echo json_encode(array("msg" => "success", "code" => 200, "data" =>$vehicleInRom));
		}else {
			echo json_encode(array("msg" => "failed", "code" => 400));
		}
}

function vehicleonport(){
	$vehicleInPort = $this->m_poipoolmaster->getvehicleinport("ts_minidashboard", $this->sess->user_id);
		if ($vehicleInPort) {
			echo json_encode(array("msg" => "success", "code" => 200, "data" =>$vehicleInPort));
		}else {
			echo json_encode(array("msg" => "failed", "code" => 400));
		}
}

function mapSetting(){
	$middle_limit = $this->input->post("middle_limit");
	$top_limit    = $this->input->post("top_limit");
	$parent_id    = $this->sess->user_parent;
	$user_id      = $this->sess->user_id;

	$data = array(
		"mapsetting_parent_id"    => $parent_id,
		"mapsetting_user_id"      => $user_id,
		"mapsetting_middle_limit" => $middle_limit,
		"mapsetting_top_limit"    => $top_limit,
		"mapsetting_updated_date" => date("Y-m-d H:i:s")
	);

	// echo "<pre>";
	// var_dump($data);die();
	// echo "<pre>";

	$update = $this->m_poipoolmaster->update_common("webtracking_ts", "ts_mapsetting", "mapsetting_user_id", $user_id, $data);
		if ($update) {
			$contentlog = "Map Setting Successfully Updated";
			$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "UPDATE", "FUNCTIONAL");
			echo json_encode(array("msg" => "success", "code" => 200));
		}else {
			$contentlog = "Map Setting Failed Updated";
			$insertlog  = $this->log_model->insertlog($this->sess->user_name, $contentlog, "UPDATE", "FUNCTIONAL");
			echo json_encode(array("msg" => "failed", "code" => 400));
		}
}

function getdatacontractor(){
	$this->db->where("company_created_by", $this->sess->user_id);
	$this->db->where("company_flag", 0);
	$total_company = $this->db->count_all_results("company");

	$this->db->where("company_created_by", $this->sess->user_id);
	$this->db->where("company_flag", 0);
	$this->db->order_by("company_name", "ASC");
	$q     = $this->db->get("company");
	$rows  = $q->result_array();

	// echo "<pre>";
	// var_dump($rows);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
}

function getvehiclebycontractor(){
	$companyid = $this->input->post('companyid');

	$this->db->select("*");
	$this->db->where("vehicle_company", $companyid);
	$this->db->where("vehicle_status <>", 3);
	$this->db->where("vehicle_gotohistory", 0);
	$this->db->where("vehicle_autocheck is not NULL");
	$this->db->order_by("vehicle_no", "ASC");
	$q    = $this->db->get("vehicle");
	$rows = $q->result_array();

	// echo "<pre>";
	// var_dump($rows);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
}



}
